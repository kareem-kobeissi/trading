<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/user_schema.php';
require_once __DIR__ . '/phone_verification_schema.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true);
$code = preg_replace('/\D+/', '', (string) ($input['code'] ?? ''));
if (!preg_match('/^\d{6}$/', $code)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Enter the six-digit code']);
    exit;
}
ensureUserPhoneColumn($conn);
ensurePhoneVerificationSchema($conn);
$userId = (int) $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT phone, code_hash, expires_at, attempts FROM user_phone_verifications WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$verification = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$verification || strtotime($verification['expires_at']) < time()) {
    http_response_code(410);
    echo json_encode(['success' => false, 'message' => 'This code has expired. Request a new one']);
    exit;
}
if ((int) $verification['attempts'] >= 5) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many incorrect attempts. Request a new code']);
    exit;
}
$secret = runtimeSecret('TTR_PHONE_OTP_SECRET');
$providedHash = hash_hmac('sha256', $code, $secret);
if (!hash_equals((string) $verification['code_hash'], $providedHash)) {
    $increment = $conn->prepare('UPDATE user_phone_verifications SET attempts = attempts + 1 WHERE user_id = ?');
    $increment->bind_param('i', $userId);
    $increment->execute();
    $increment->close();
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Incorrect verification code']);
    exit;
}

$conn->begin_transaction();
try {
    $phone = $verification['phone'];
    $update = $conn->prepare('UPDATE users SET phone = ? WHERE id = ?');
    $update->bind_param('si', $phone, $userId);
    $update->execute();
    $update->close();
    $verified = $conn->prepare('UPDATE user_phone_verifications SET verified_at = NOW(), code_hash = REPEAT(\'0\', 64) WHERE user_id = ?');
    $verified->bind_param('i', $userId);
    $verified->execute();
    $verified->close();
    $conn->commit();
} catch (Throwable $error) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to save the verified phone number']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Phone number verified']);
$conn->close();

