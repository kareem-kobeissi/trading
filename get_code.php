<?php
$conn = new mysqli('localhost', 'root', '', 'trading_platform');
if ($conn->connect_error) {
    echo json_encode(['error' => $conn->connect_error]);
    exit;
}

$email = isset($_GET['email']) ? $_GET['email'] : 'newtest_380714685@test.com';

$result = $conn->query("SELECT code FROM verification_codes WHERE email = '" . $conn->real_escape_string($email) . "' ORDER BY created_at DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['code' => $row['code'], 'email' => $email]);
} else {
    echo json_encode(['error' => 'No code found', 'email' => $email]);
}
$conn->close();
