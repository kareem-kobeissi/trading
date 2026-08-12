<?php

header('Content-Type: application/json');
header('Cache-Control: no-store');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/automation_schema.php';

// This endpoint is intentionally session-protected even though the legacy
// dashboard still needs a wider authentication cleanup.
if (empty($_SESSION['is_admin']) && empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Administrator authentication required']);
    exit;
}

$orderId = (int) ($_GET['order_id'] ?? 0);
if (!$orderId) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$schemaError = ensureAutomationSchema($conn);
if ($schemaError) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $schemaError]);
    exit;
}

$reviewStmt = $conn->prepare(
    'SELECT contact_status, recommended_status, confidence, reason, last_customer_message_at, updated_at
     FROM automation_reviews WHERE order_id = ? LIMIT 1'
);
$reviewStmt->bind_param('i', $orderId);
$reviewStmt->execute();
$review = $reviewStmt->get_result()->fetch_assoc();
$reviewStmt->close();

$messageStmt = $conn->prepare(
    'SELECT channel, direction, sender_address, recipient_address, original_message,
            translated_message, ai_summary, attachment_url, created_at
     FROM customer_conversations WHERE order_id = ? ORDER BY created_at ASC, id ASC'
);
$messageStmt->bind_param('i', $orderId);
$messageStmt->execute();
$messages = $messageStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$messageStmt->close();

echo json_encode(['success' => true, 'review' => $review, 'messages' => $messages]);
$conn->close();
