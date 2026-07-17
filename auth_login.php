<?php
// Prevent any output before JSON
ob_start();

// Set JSON header first
header('Content-Type: application/json; charset=utf-8');

// Disable error display
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Get config
try {
    require_once 'config.php';
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed']);
    exit;
}

try {
    // Get input
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    // Find user by email
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        $stmt->close();
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];

    // Send success response
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'username' => $user['username'],
        'email' => $user['email']
    ]);
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

// Close database connection
if (isset($conn)) {
    $conn->close();
}
