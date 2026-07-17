<?php
require_once 'config.php';

$tables = [
    'users' => "SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'trading_platform' AND TABLE_NAME = 'users'",
    'verification_codes' => "SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'trading_platform' AND TABLE_NAME = 'verification_codes'"
];

$response = [];

foreach ($tables as $name => $query) {
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $response[$name] = $row['count'] > 0 ? 'EXISTS' : 'MISSING';
    } else {
        $response[$name] = 'ERROR: ' . $conn->error;
    }
}

// Also test a simple INSERT to see if there's a hang
$response['db_test'] = 'Starting test...';
$test_email = 'test_' . time() . '@test.com';
$stmt = $conn->prepare("INSERT INTO verification_codes (email, code, created_at, expires_at, attempted) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 15 MINUTE), 0)");
if ($stmt) {
    $code = '123456';
    $stmt->bind_param("ss", $test_email, $code);
    if ($stmt->execute()) {
        $response['db_test'] = 'INSERT successful, id=' . $conn->insert_id;
        // Clean up
        $conn->query("DELETE FROM verification_codes WHERE email = '$test_email'");
    } else {
        $response['db_test'] = 'INSERT failed: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $response['db_test'] = 'Prepare failed: ' . $conn->error;
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
