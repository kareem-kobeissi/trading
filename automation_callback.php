<?php

header('Content-Type: application/json');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/automation_config.php';
require_once __DIR__ . '/automation_schema.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$secret = automationConfig('TTR_AUTOMATION_CALLBACK_SECRET');
$timestamp = $_SERVER['HTTP_X_TTR_TIMESTAMP'] ?? '';
$providedSignature = $_SERVER['HTTP_X_TTR_SIGNATURE'] ?? '';
$rawBody = file_get_contents('php://input');

if ($secret === '' || !ctype_digit($timestamp) || abs(time() - (int) $timestamp) > 300) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid callback credentials']);
    exit;
}

$expectedSignature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);
if (!hash_equals($expectedSignature, $providedSignature)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid callback signature']);
    exit;
}

$input = json_decode($rawBody, true);
$orderId = (int) ($input['order_id'] ?? 0);
$event = (string) ($input['event'] ?? '');
if (!$orderId || !in_array($event, ['message.sent', 'message.received', 'review.updated'], true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid callback payload']);
    exit;
}

$schemaError = ensureAutomationSchema($conn);
if ($schemaError) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $schemaError]);
    exit;
}

$orderCheck = $conn->prepare('SELECT id FROM orders WHERE id = ? LIMIT 1');
$orderCheck->bind_param('i', $orderId);
$orderCheck->execute();
$orderExists = $orderCheck->get_result()->num_rows === 1;
$orderCheck->close();
if (!$orderExists) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

if ($event === 'message.sent' || $event === 'message.received') {
    $message = $input['message'] ?? [];
    $channel = in_array(($message['channel'] ?? ''), ['email', 'whatsapp', 'system'], true)
        ? $message['channel']
        : 'system';
    $direction = $event === 'message.sent' ? 'outgoing' : 'incoming';
    $sender = substr(trim((string) ($message['sender'] ?? '')), 0, 255);
    $recipient = substr(trim((string) ($message['recipient'] ?? '')), 0, 255);
    $original = trim((string) ($message['original'] ?? ''));
    $translated = trim((string) ($message['translated'] ?? ''));
    $summary = trim((string) ($message['summary'] ?? ''));
    $attachmentUrl = trim((string) ($message['attachment_url'] ?? ''));
    $externalId = substr(trim((string) ($message['external_id'] ?? '')), 0, 255);

    if ($original === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Message text is required']);
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO customer_conversations
         (order_id, channel, direction, sender_address, recipient_address,
          original_message, translated_message, ai_summary, attachment_url, external_message_id)
         VALUES (?, ?, ?, ?, ?, ?, NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''))
         ON DUPLICATE KEY UPDATE id = id"
    );
    $stmt->bind_param(
        'isssssssss',
        $orderId,
        $channel,
        $direction,
        $sender,
        $recipient,
        $original,
        $translated,
        $summary,
        $attachmentUrl,
        $externalId
    );
    $stmt->execute();
    $stmt->close();
}

$review = $input['review'] ?? [];
$allowedContact = ['not_contacted', 'contacted', 'waiting_for_proof', 'proof_received', 'needs_admin_review'];
$allowedRecommendation = ['pending', 'likely_valid', 'likely_invalid', 'needs_review'];
$contactStatus = in_array(($review['contact_status'] ?? ''), $allowedContact, true)
    ? $review['contact_status']
    : ($event === 'message.received' ? 'needs_admin_review' : 'contacted');
$recommendation = in_array(($review['recommended_status'] ?? ''), $allowedRecommendation, true)
    ? $review['recommended_status']
    : 'pending';
$confidence = isset($review['confidence']) ? max(0, min(100, (float) $review['confidence'])) : null;
$reason = trim((string) ($review['reason'] ?? ''));

$stmt = $conn->prepare(
    "INSERT INTO automation_reviews
     (order_id, contact_status, recommended_status, confidence, reason, last_customer_message_at)
     VALUES (?, ?, ?, ?, NULLIF(?, ''), IF(? = 'message.received', NOW(), NULL))
     ON DUPLICATE KEY UPDATE
       contact_status = VALUES(contact_status),
       recommended_status = VALUES(recommended_status),
       confidence = VALUES(confidence),
       reason = VALUES(reason),
       last_customer_message_at = IF(? = 'message.received', NOW(), last_customer_message_at)"
);
$stmt->bind_param(
    'issdsss',
    $orderId,
    $contactStatus,
    $recommendation,
    $confidence,
    $reason,
    $event,
    $event
);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
$conn->close();
