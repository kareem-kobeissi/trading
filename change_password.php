<?php
header('Content-Type: application/json');
include 'config.php';

$email            = isset($_POST['email']) ? trim($conn->real_escape_string($_POST['email'])) : '';
$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password     = isset($_POST['new_password']) ? $_POST['new_password'] : '';

if (!$email || !$current_password || !$new_password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
    exit();
}

// Get user
$result = $conn->query("SELECT id, password FROM users WHERE email = '$email' LIMIT 1");
if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

$user = $result->fetch_assoc();

// Verify current password
if (!password_verify($current_password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
    exit();
}

// Hash and update new password
$hashedNew = password_hash($new_password, PASSWORD_DEFAULT);
$conn->query("UPDATE users SET password = '$hashedNew' WHERE email = '$email'");

echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
$conn->close();
?>