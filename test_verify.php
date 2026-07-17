<?php
ob_start();

header('Content-Type: application/json');

$logs = [];

try {
    $logs[] = "Starting verify_and_create debug...";
    require_once 'config.php';
    $logs[] = "Config loaded";

    $email = 'newtest_380714685@test.com';
    $code = '694076';
    $password = 'TestPass123';
    $username = 'UniqueTestUser_' . time();

    $logs[] = "Input: email=$email, code=$code, username=$username";

    // Check if code exists
    $logs[] = "Querying verification_codes table...";
    $stmt = $conn->prepare("SELECT id, attempted FROM verification_codes WHERE email = ? AND code = ? AND expires_at > NOW()");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $logs[] = "Statement prepared";

    $stmt->bind_param("ss", $email, $code);
    $logs[] = "Parameters bound";

    $stmt->execute();
    $logs[] = "Query executed";

    $result = $stmt->get_result();
    $logs[] = "Result obtained";

    $logs[] = "Rows found: " . $result->num_rows;

    if ($result->num_rows === 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid or expired code', 'logs' => $logs]);
        exit;
    }

    $codeRecord = $result->fetch_assoc();
    $stmt->close();
    $logs[] = "Code verified, attempted: " . $codeRecord['attempted'];

    // Check if email already exists
    $logs[] = "Checking if email already exists...";
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $logs[] = "Email check complete, rows: " . $result->num_rows;

    if ($result->num_rows > 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Email already registered', 'logs' => $logs]);
        exit;
    }

    // Hash password
    $logs[] = "Hashing password...";
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $logs[] = "Password hashed";

    // Create user account
    $logs[] = "Creating user...";
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    $stmt->execute();
    $logs[] = "User created with id: " . $conn->insert_id;
    $stmt->close();

    // Delete verification code
    $logs[] = "Deleting verification code...";
    $stmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ? AND code = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $logs[] = "Code deleted";
    $stmt->close();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully',
        'username' => $username,
        'email' => $email,
        'logs' => $logs
    ]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'logs' => $logs]);
}
