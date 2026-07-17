<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ Not authorized");
}

$userId = $_SESSION['user_id'];

// Check access for Instant Breakout EA (course_id = 4)
$stmt = $conn->prepare("
    SELECT oi.id 
    FROM order_items oi
    JOIN orders o ON o.id = oi.order_id
    WHERE o.user_id = ?
    AND oi.item_status = 'unlocked'
    AND oi.course_id = 4
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ No access");
}

$file = __DIR__ . '/Instant Breakout Robot_Setup.exe';

if (!file_exists($file)) {
    die("❌ File not found");
}

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="Instant Breakout Robot_Setup.exe"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
