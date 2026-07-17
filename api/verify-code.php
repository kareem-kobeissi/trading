<?php
// Start output buffering to prevent headers already sent errors
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header FIRST
header('Content-Type: application/json; charset=utf-8');

// Create logs directory if it doesn't exist
$log_dir = '../logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

// Try to include database config
try {
    require_once '../config.php';
    if (!isset($conn) || !$conn || $conn->connect_error) {
        throw new Exception('Database connection failed');
    }
} catch (Exception $e) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$email = isset($data['email']) ? $data['email'] : null;
$code = isset($data['code']) ? $data['code'] : null;

// Validate input
if (!$email || !$code) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Email and code are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

try {
    // Check if code exists and is not expired
    $stmt = $conn->prepare("
        SELECT id, code, expires_at, attempted 
        FROM verification_codes 
        WHERE email = ? 
        AND expires_at > NOW()
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No valid verification code found. Please request a new code.']);
        $stmt->close();
        exit;
    }

    $row = $result->fetch_assoc();
    $storedCode = $row['code'];
    $recordId = $row['id'];
    $attempts = $row['attempted'];

    $stmt->close();

    // Check if too many attempts
    if ($attempts >= 3) {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Too many failed attempts. Please request a new code.']);
        exit;
    }

    // Compare codes
    if ($code !== $storedCode) {
        // Increment attempts
        $updateStmt = $conn->prepare("UPDATE verification_codes SET attempted = attempted + 1 WHERE id = ?");
        if ($updateStmt) {
            $updateStmt->bind_param("i", $recordId);
            $updateStmt->execute();
            $updateStmt->close();
        }

        http_response_code(400);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please try again.']);
        exit;
    }

    // Code is valid - delete it
    $deleteStmt = $conn->prepare("DELETE FROM verification_codes WHERE id = ?");
    if ($deleteStmt) {
        $deleteStmt->bind_param("i", $recordId);
        $deleteStmt->execute();
        $deleteStmt->close();
    }

    // Log successful verification
    $log_message = "[" . date('Y-m-d H:i:s') . "] VERIFIED: $email with code $code\n";
    @file_put_contents($log_dir . '/verification_success.log', $log_message, FILE_APPEND);

    // Return success
    http_response_code(200);
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Email verified successfully',
        'verified' => true,
        'email' => $email
    ]);
} catch (Exception $e) {
    $error_message = "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    @file_put_contents($log_dir . '/error_log.log', $error_message, FILE_APPEND);

    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
