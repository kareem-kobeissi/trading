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
$event = (string) ($input['event'] ?? '');
$messageWasNew = null;

if ($event === 'customer.lookup_by_phone') {
    $requestedPhone = preg_replace('/\D+/', '', (string) ($input['phone'] ?? ''));
    if (strlen($requestedPhone) < 7) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
        exit;
    }

    $lookup = $conn->query(
        "SELECT id, order_ref, name, email, phone, status
         FROM orders
         ORDER BY (status = 'pending') DESC, id DESC
         LIMIT 250"
    );
    $matched = null;
    while ($row = $lookup->fetch_assoc()) {
        $storedPhone = preg_replace('/\D+/', '', (string) $row['phone']);
        if ($storedPhone !== '' && (
            hash_equals($storedPhone, $requestedPhone)
            || (strlen($storedPhone) >= 7 && substr($storedPhone, -7) === substr($requestedPhone, -7))
        )) {
            $matched = $row;
            break;
        }
    }

    if (!$matched) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No customer order matches this phone']);
        exit;
    }

    $matched['items'] = [];
    $eventStmt = $conn->prepare(
        "SELECT payload_json
         FROM automation_events
         WHERE order_id = ? AND event_type = 'order.pending'
         ORDER BY id DESC
         LIMIT 1"
    );
    if ($eventStmt) {
        $matchedOrderId = (int) $matched['id'];
        $eventStmt->bind_param('i', $matchedOrderId);
        $eventStmt->execute();
        $eventRow = $eventStmt->get_result()->fetch_assoc();
        $eventStmt->close();
        $eventPayload = json_decode((string) ($eventRow['payload_json'] ?? ''), true);
        $payloadItems = $eventPayload['order']['items'] ?? [];
        if (is_array($payloadItems)) {
            foreach ($payloadItems as $item) {
                if (is_array($item) && trim((string) ($item['name'] ?? '')) !== '') {
                    $matched['items'][] = [
                        'type' => trim((string) ($item['type'] ?? '')),
                        'name' => trim((string) $item['name'])
                    ];
                }
            }
        }
    }

    if (!$matched['items']) {
        $productNames = [
            'course' => 'Trading Mastery Course',
            'ea' => 'TTR Risk Calculator EA',
            'indicator' => 'The Holly Grail Indicator',
            'robot' => 'TTR Robot',
            'robot_sr' => 'S&R Precision EA',
            'robot_ib' => 'Instant Breakout EA'
        ];
        $itemStmt = $conn->prepare('SELECT product_type FROM order_items WHERE order_id = ? ORDER BY id ASC');
        if ($itemStmt) {
            $matchedOrderId = (int) $matched['id'];
            $itemStmt->bind_param('i', $matchedOrderId);
            $itemStmt->execute();
            $itemResult = $itemStmt->get_result();
            while ($itemRow = $itemResult->fetch_assoc()) {
                $type = trim((string) $itemRow['product_type']);
                $matched['items'][] = [
                    'type' => $type,
                    'name' => $productNames[$type] ?? 'Trading Product'
                ];
            }
            $itemStmt->close();
        }
    }

    echo json_encode(['success' => true, 'order' => $matched]);
    $conn->close();
    exit;
}

$orderId = (int) ($input['order_id'] ?? 0);
$orderReference = trim((string) ($input['order_reference'] ?? ''));
if ((!$orderId && $orderReference === '') || !in_array($event, ['email.send_requested', 'message.sent', 'message.received', 'review.updated'], true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid callback payload']);
    exit;
}

if (!$orderId && $orderReference !== '') {
    $referenceStmt = $conn->prepare('SELECT id FROM orders WHERE order_ref = ? LIMIT 1');
    $referenceStmt->bind_param('s', $orderReference);
    $referenceStmt->execute();
    $referenceRow = $referenceStmt->get_result()->fetch_assoc();
    $referenceStmt->close();
    $orderId = (int) ($referenceRow['id'] ?? 0);
}

$schemaError = ensureAutomationSchema($conn);
if ($schemaError) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $schemaError]);
    exit;
}

$orderCheck = $conn->prepare('SELECT id, email FROM orders WHERE id = ? LIMIT 1');
$orderCheck->bind_param('i', $orderId);
$orderCheck->execute();
$orderRow = $orderCheck->get_result()->fetch_assoc();
$orderExists = (bool) $orderRow;
$orderCheck->close();
if (!$orderExists) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

if ($event === 'email.send_requested') {
    $message = is_array($input['message'] ?? null) ? $input['message'] : [];
    $recipient = trim((string) ($message['recipient'] ?? ''));
    $subject = trim((string) ($message['subject'] ?? ''));
    $original = trim((string) ($message['original'] ?? ''));
    $supportEmail = function_exists('runtimeSecret')
        ? runtimeSecret('OWNER_EMAIL', runtimeSecret('SMTP_FROM_EMAIL', runtimeSecret('SMTP_USERNAME')))
        : '';
    $allowedRecipients = array_filter([(string) $orderRow['email'], $supportEmail]);

    if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)
        || !in_array(strtolower($recipient), array_map('strtolower', $allowedRecipients), true)
        || $subject === '' || $original === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid email request']);
        exit;
    }

    try {
        require_once __DIR__ . '/api/mail-config.php';
        require_once __DIR__ . '/libs/GmailSMTP.php';
        require_once __DIR__ . '/email_template.php';
        if (!USE_GMAIL_SMTP) throw new RuntimeException('Hostinger SMTP is not configured');

        $verifyValue = strtolower(trim((string) (getenv('SMTP_VERIFY_TLS') ?: 'true')));
        $smtp = new GmailSMTP(
            GMAIL_ADDRESS,
            GMAIL_PASSWORD,
            false,
            SMTP_HOST,
            SMTP_PORT,
            getenv('SMTP_CA_FILE') ?: '',
            !in_array($verifyValue, ['0', 'false', 'no', 'off'], true)
        );
        $html = brandedPlainTextEmail($subject, $original, $subject);
        if (!$smtp->sendEmail($recipient, substr($subject, 0, 240), $html)) {
            throw new RuntimeException('SMTP server did not accept the email');
        }
    } catch (Throwable $error) {
        error_log('Automation email bridge failed: ' . $error->getMessage());
        http_response_code(502);
        echo json_encode(['success' => false, 'message' => 'Email delivery failed']);
        exit;
    }

    $externalId = 'hostinger-' . bin2hex(random_bytes(12));
    $sender = defined('SENDER_EMAIL') ? SENDER_EMAIL : GMAIL_ADDRESS;
    $stmt = $conn->prepare(
        "INSERT INTO customer_conversations
         (order_id, channel, direction, sender_address, recipient_address,
          original_message, external_message_id)
         VALUES (?, 'email', 'outgoing', ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE id = id"
    );
    $stmt->bind_param('issss', $orderId, $sender, $recipient, $original, $externalId);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'external_id' => $externalId]);
    $conn->close();
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

    if ($event === 'message.received') {
        $senderMatches = $channel === 'email'
            ? strcasecmp($sender, (string) $orderRow['email']) === 0
            : true;
        if ($channel === 'whatsapp') {
            $phoneStmt = $conn->prepare('SELECT phone FROM orders WHERE id = ? LIMIT 1');
            $phoneStmt->bind_param('i', $orderId);
            $phoneStmt->execute();
            $phoneRow = $phoneStmt->get_result()->fetch_assoc();
            $phoneStmt->close();
            $storedDigits = preg_replace('/\D+/', '', (string) ($phoneRow['phone'] ?? ''));
            $senderDigits = preg_replace('/\D+/', '', $sender);
            $senderMatches = $storedDigits !== '' && $senderDigits !== ''
                && ($storedDigits === $senderDigits
                    || substr($storedDigits, -7) === substr($senderDigits, -7));
        }
        if (!$senderMatches) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Reply sender does not match the order customer']);
            exit;
        }
    }

    $savedAttachments = [];
    $attachments = is_array($message['attachments'] ?? null) ? $message['attachments'] : [];
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf'
    ];
    foreach (array_slice($attachments, 0, 5) as $attachment) {
        $contentType = strtolower(trim((string) ($attachment['content_type'] ?? '')));
        $encoded = (string) ($attachment['data_base64'] ?? '');
        if (!isset($allowedTypes[$contentType]) || $encoded === '') continue;
        $binary = base64_decode($encoded, true);
        if ($binary === false || strlen($binary) > 4 * 1024 * 1024) continue;
        $relativeDir = 'uploads/automation/' . $orderId;
        $absoluteDir = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeDir);
        if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0755, true) && !is_dir($absoluteDir)) continue;
        $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$contentType];
        if (file_put_contents($absoluteDir . DIRECTORY_SEPARATOR . $filename, $binary, LOCK_EX) !== false) {
            $savedAttachments[] = $relativeDir . '/' . $filename;
        }
    }
    if ($savedAttachments) {
        $attachmentUrl = json_encode($savedAttachments, JSON_UNESCAPED_SLASHES);
    }

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
    $messageWasNew = $stmt->affected_rows > 0;
    $stmt->close();
}

$review = $input['review'] ?? [];
if (empty($input['preserve_review'])) {
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
}

echo json_encode(['success' => true, 'message_saved' => $messageWasNew]);
$conn->close();
