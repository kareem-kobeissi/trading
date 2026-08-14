<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/automation_schema.php';

if (empty($_SESSION['is_admin']) && empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Administrator authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$orderId = (int) ($input['order_id'] ?? 0);
$message = trim((string) ($input['message'] ?? ''));
if ($orderId < 1 || $message === '' || mb_strlen($message) > 10000) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Enter a message of up to 10,000 characters']);
    exit;
}

$stmt = $conn->prepare('SELECT order_ref, name, email FROM orders WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$order || !filter_var($order['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Customer order not found']);
    exit;
}

$schemaError = ensureAutomationSchema($conn);
if ($schemaError) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $schemaError]);
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
    $subject = 'Order ' . $order['order_ref'] . ' - THE TRADING ROUTINE';
    $html = brandedPlainTextEmail($subject, $message, 'A message from THE TRADING ROUTINE support');
    if (!$smtp->sendEmail($order['email'], $subject, $html)) {
        throw new RuntimeException('SMTP server did not accept the email');
    }

    $externalId = 'admin-' . bin2hex(random_bytes(12));
    $sender = defined('SENDER_EMAIL') ? SENDER_EMAIL : GMAIL_ADDRESS;
    $save = $conn->prepare(
        "INSERT INTO customer_conversations
         (order_id, channel, direction, sender_address, recipient_address, original_message, external_message_id)
         VALUES (?, 'email', 'outgoing', ?, ?, ?, ?)"
    );
    $save->bind_param('issss', $orderId, $sender, $order['email'], $message, $externalId);
    $save->execute();
    $save->close();

    echo json_encode(['success' => true, 'message' => 'Message sent and saved']);
} catch (Throwable $error) {
    error_log('Admin customer message failed: ' . $error->getMessage());
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Unable to send the message']);
}

$conn->close();
