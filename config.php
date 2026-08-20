<?php
require_once __DIR__ . '/runtime_secrets.php';

// Database Configuration
define('DB_HOST', runtimeSecret('DB_HOST', 'localhost'));
define('DB_USER', runtimeSecret('DB_USER', 'root'));
define('DB_PASS', runtimeSecret('DB_PASS', ''));
define('DB_NAME', runtimeSecret('DB_NAME', 'trading_platform'));

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    throw new RuntimeException('Database connection failed');
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Session start - only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

// Redirect if not logged in (for protected pages)
function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.html");
        exit();
    }
}
//opdo iqpe zzbl deuk
