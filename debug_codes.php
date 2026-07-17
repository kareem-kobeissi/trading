<?php
header('Content-Type: application/json');

require_once 'config.php';

$email = isset($_GET['email']) ? $_GET['email'] : '';
$response = [];

if (!$email) {
    echo json_encode(['error' => 'Email parameter required']);
    exit;
}

$response['email'] = $email;
$response['checking'] = 'Searching database for verification codes...';

// Check all codes for this email (including expired ones)
$stmt = $conn->prepare("SELECT code, created_at, expires_at, attempted FROM verification_codes WHERE email = ? ORDER BY created_at DESC LIMIT 5");

if (!$stmt) {
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$response['codes_found'] = $result->num_rows;

if ($result->num_rows > 0) {
    $response['codes'] = [];
    while ($row = $result->fetch_assoc()) {
        $isValid = strtotime($row['expires_at']) > time();
        $response['codes'][] = [
            'code' => $row['code'],
            'status' => $isValid ? 'VALID' : 'EXPIRED',
            'created_at' => $row['created_at'],
            'expires_at' => $row['expires_at'],
            'attempted' => $row['attempted'],
            'valid' => $isValid
        ];
    }
} else {
    $response['message'] = 'No verification codes found for this email';
}

$stmt->close();

// Also check if user exists with this email
$stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $response['user_exists'] = $userResult->num_rows > 0;
    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        $response['existing_user'] = $user['username'];
    }
    $stmt->close();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$conn->close();
