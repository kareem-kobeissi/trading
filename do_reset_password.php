<?php
header('Content-Type: application/json');
include 'config.php';
date_default_timezone_set('Asia/Beirut');

$token        = isset($_POST['token']) ? trim($_POST['token']) : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

if (!$token || !$new_password) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

$escaped = $conn->real_escape_string($token);

// Check token is valid and not expired
$result = $conn->query("SELECT id FROM users WHERE reset_token = '$escaped' AND reset_expires > NOW() LIMIT 1");

if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired reset link']);
    exit();
}

// Update password and clear token
$hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
$conn->query("UPDATE users SET password = '$hashedPassword', reset_token = NULL, reset_expires = NULL WHERE reset_token = '$escaped'");

echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
$conn->close();
?>