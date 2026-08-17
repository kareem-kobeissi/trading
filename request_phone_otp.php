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
$phone = preg_replace('/\D+/', '', (string) ($input['phone'] ?? ''));
if (strlen($phone) < 8 || strlen($phone) > 15) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Enter a valid international WhatsApp number']);
    exit;
}
$phone = '+' . $phone;
$userId = (int) $_SESSION['user_id'];

if (($error = ensureUserPhoneColumn($conn)) || ($error = ensurePhoneVerificationSchema($conn))) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

$existingUser = $conn->prepare("SELECT id FROM users WHERE REPLACE(REPLACE(REPLACE(phone, '+', ''), ' ', ''), '-', '') = ? AND id <> ? LIMIT 1");
$phoneDigits = substr($phone, 1);
$existingUser->bind_param('si', $phoneDigits, $userId);
$existingUser->execute();
$phoneTaken = (bool) $existingUser->get_result()->fetch_assoc();
$existingUser->close();
if ($phoneTaken) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'This phone number is already connected to another account']);
    exit;
}

$rateStmt = $conn->prepare('SELECT send_count, window_started_at, last_sent_at FROM user_phone_verifications WHERE user_id = ? LIMIT 1');
$rateStmt->bind_param('i', $userId);
$rateStmt->execute();
$rate = $rateStmt->get_result()->fetch_assoc();
$rateStmt->close();
$now = time();
if ($rate && strtotime($rate['last_sent_at']) > $now - 60) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Please wait one minute before requesting another code']);
    exit;
}
$windowActive = $rate && strtotime($rate['window_started_at']) > $now - 3600;
$sendCount = $windowActive ? (int) $rate['send_count'] + 1 : 1;
if ($sendCount > 5) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many codes requested. Please try again later']);
    exit;
}

$secret = runtimeSecret('TTR_PHONE_OTP_SECRET');
if ($secret === '') {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Phone verification is not configured']);
    exit;
}
$code = (string) random_int(100000, 999999);
$codeHash = hash_hmac('sha256', $code, $secret);
$windowStarted = $windowActive ? $rate['window_started_at'] : date('Y-m-d H:i:s');
$expiresAt = date('Y-m-d H:i:s', $now + 600);
$lastSent = date('Y-m-d H:i:s');
$stmt = $conn->prepare(
    "INSERT INTO user_phone_verifications
     (user_id, phone, code_hash, expires_at, attempts, send_count, window_started_at, last_sent_at, verified_at)
     VALUES (?, ?, ?, ?, 0, ?, ?, ?, NULL)
     ON DUPLICATE KEY UPDATE phone = VALUES(phone), code_hash = VALUES(code_hash),
       expires_at = VALUES(expires_at), attempts = 0, send_count = VALUES(send_count),
       window_started_at = VALUES(window_started_at), last_sent_at = VALUES(last_sent_at), verified_at = NULL"
);
$stmt->bind_param('isssiss', $userId, $phone, $codeHash, $expiresAt, $sendCount, $windowStarted, $lastSent);
$stmt->execute();
$stmt->close();

$serviceUrl = rtrim(runtimeSecret('TTR_AUTOMATION_BASE_URL', 'https://ttr-customer-automation.onrender.com'), '/');
$payload = json_encode(['phone' => $phone, 'code' => $code], JSON_UNESCAPED_SLASHES);
$timestamp = (string) time();
$signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
$ch = curl_init($serviceUrl . '/phone-verification/send');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-TTR-Phone-Timestamp: ' . $timestamp,
        'X-TTR-Phone-Signature: ' . $signature
    ]
]);
$response = curl_exec($ch);
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);
if ($response === false || $status < 200 || $status >= 300) {
    error_log('WhatsApp OTP delivery failed: HTTP ' . $status . ' ' . $curlError);
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'The verification code could not be sent. Please try again']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Verification code sent on WhatsApp', 'expires_in' => 600]);
$conn->close();

