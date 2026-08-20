<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/user_schema.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$schemaError = ensureUserPhoneColumn($conn);
if ($schemaError) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $schemaError]);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT phone FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$phone = trim((string) ($row['phone'] ?? ''));

echo json_encode([
    'success' => true,
    'verification_required' => $phone === '',
    'phone_saved' => $phone !== ''
]);
$conn->close();

