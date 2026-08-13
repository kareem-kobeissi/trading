<?php
// Prevent any output before JSON
ob_start();

// Set JSON header first
header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function () {
    $error = error_get_last();
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!$error || !in_array($error['type'], $fatalTypes, true)) return;
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    while (ob_get_level() > 0) ob_end_clean();
    error_log('Signup fatal error: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
    echo json_encode([
        'success' => false,
        'message' => 'Signup server configuration error',
        'diagnostic' => basename($error['file']) . ':' . $error['line']
    ]);
});
  
// Disable error display
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Get config
try {
    require_once __DIR__ . '/config.php';
} catch (Throwable $e) {
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
    // Get action parameter
    $action = isset($_POST['action']) ? trim($_POST['action']) : 'legacy';

    // ===== ACTION: SEND CODE =====
    if ($action === 'send_code') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';

        if (empty($email)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            exit;
        }

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store code in database with 15-minute expiry
        $stmt = $conn->prepare("INSERT INTO verification_codes (email, code, created_at, expires_at, attempted) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 15 MINUTE), 0)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $email, $code);
        if (!$stmt->execute()) {
            throw new Exception("Insert failed: " . $stmt->error);
        }
        $stmt->close();

        // Send email with verification code
        $emailSent = false;
        $deliveryDiagnostic = 'unknown';
        try {
            require_once __DIR__ . '/api/mail-config.php';

            $smtpPassword = GMAIL_PASSWORD;
            $smtpCaFile = getenv('SMTP_CA_FILE') ?: '';
            $smtpVerifyTlsValue = strtolower(trim((string) (getenv('SMTP_VERIFY_TLS') ?: 'true')));
            $smtpVerifyTls = !in_array($smtpVerifyTlsValue, ['0', 'false', 'no', 'off'], true);
            $smtpDebugValue = strtolower(trim((string) (getenv('SMTP_DEBUG') ?: 'false')));
            $smtpDebug = in_array($smtpDebugValue, ['1', 'true', 'yes', 'on'], true);

            $subject = "Your Verification Code - The Trading Routine";
            $htmlMessage = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .header { text-align: center; margin-bottom: 30px; }
                    .header h2 { color: #00d4ff; margin: 0; }
                    .content { text-align: center; }
                    .code-box { background-color: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 8px; border: 2px solid #00d4ff; }
                    .code { font-size: 32px; font-weight: bold; color: #00d4ff; letter-spacing: 5px; }
                    .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>The Trading Routine</h2>
                    </div>
                    <div class='content'>
                        <p>Hello,</p>
                        <p>Your verification code is:</p>
                        <div class='code-box'>
                            <div class='code'>" . htmlspecialchars($code) . "</div>
                        </div>
                        <p>This code will expire in <strong>15 minutes</strong>.</p>
                        <p>If you did not request this code, please ignore this email.</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; The Trading Routine. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>";

            $plainTextMessage = "Your verification code is: " . $code . "\n\nThis code will expire in 15 minutes.\n\nIf you did not request this code, please ignore this email.";

            if (defined('USE_GMAIL_SMTP') && USE_GMAIL_SMTP) {
                // Send via Gmail SMTP
                if (file_exists(__DIR__ . '/libs/GmailSMTP.php')) {
                    require_once __DIR__ . '/libs/GmailSMTP.php';
                    $smtp = new GmailSMTP(GMAIL_ADDRESS, $smtpPassword, $smtpDebug, SMTP_HOST, SMTP_PORT, $smtpCaFile, $smtpVerifyTls);
                    $emailSent = $smtp->sendEmail($email, $subject, $htmlMessage);
                    $deliveryDiagnostic = $emailSent ? 'smtp_accepted' : 'smtp_rejected';
                }
            }

            // Fallback to PHP mail() if SMTP fails or is not configured
            if (!$emailSent && GMAIL_ADDRESS !== '') {
                $headers = "From: " . GMAIL_ADDRESS . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $emailSent = @mail($email, $subject, $htmlMessage, $headers);
            }
        } catch (Throwable $e) {
            // Log the SMTP error and use the hosting provider's native mail
            // transport as a production fallback.
            error_log("Email sending error: " . $e->getMessage());
            $signupLogDir = __DIR__ . '/logs';
            if (!is_dir($signupLogDir)) @mkdir($signupLogDir, 0755, true);
            $safeLogMessage = preg_replace(
                '/(?:sk-(?:proj-)?[A-Za-z0-9_-]+|[A-Za-z0-9+\/=]{32,})/',
                '[redacted]',
                $e->getMessage()
            );
            @file_put_contents(
                $signupLogDir . '/signup_email_errors.log',
                '[' . date('Y-m-d H:i:s') . '] ' . get_class($e) . ': ' . $safeLogMessage . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );
            $errorMessage = strtolower($e->getMessage());
            if (str_contains($errorMessage, 'authentication') || str_contains($errorMessage, 'smtp 535')) {
                $deliveryDiagnostic = 'smtp_authentication_failed';
            } elseif (str_contains($errorMessage, 'certificate') || str_contains($errorMessage, 'tls')) {
                $deliveryDiagnostic = 'smtp_tls_failed';
            } elseif (str_contains($errorMessage, 'connect')) {
                $deliveryDiagnostic = 'smtp_connection_failed';
            } else {
                $deliveryDiagnostic = 'smtp_failed';
            }
            if (isset($subject, $htmlMessage) && defined('GMAIL_ADDRESS') && GMAIL_ADDRESS !== '') {
                $fallbackHeaders = "From: " . GMAIL_ADDRESS . "\r\n";
                $fallbackHeaders .= "Reply-To: " . GMAIL_ADDRESS . "\r\n";
                $fallbackHeaders .= "MIME-Version: 1.0\r\n";
                $fallbackHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
                $emailSent = @mail($email, $subject, $htmlMessage, $fallbackHeaders);
                if (!$emailSent) $deliveryDiagnostic .= '_and_native_mail_failed';
            } else {
                $emailSent = false;
            }
        }

        if (!$emailSent) {
            // Email failed - delete the code we just created
            $stmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ? AND code = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $email, $code);
                $stmt->execute();
                $stmt->close();
            }

            ob_end_clean();
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.',
                'diagnostic' => $deliveryDiagnostic
            ]);
            exit;
        }

        // Success - do NOT return the code in response
        ob_end_clean();
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Verification code sent to ' . $email,
            'email' => $email
        ]);
        exit;
    }

    // ===== ACTION: VERIFY AND CREATE =====
    if ($action === 'verify_and_create') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone    = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $code     = isset($_POST['code']) ? trim($_POST['code']) : '';

        // Validate inputs - provide specific error for each field
        if (empty($username)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Full Name is required']);
            exit;
        }

        if (empty($email)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }

        if (empty($password)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Password is required']);
            exit;
        }

        if (empty($code)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Verification code is required']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        if (strlen($password) < 6) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            exit;
        }

        if (strlen($code) !== 6 || !ctype_digit($code)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Verification code must be 6 digits']);
            exit;
        }

        // Verify code
        $stmt = $conn->prepare("SELECT id, attempted FROM verification_codes WHERE email = ? AND code = ? AND expires_at > NOW()");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $email, $code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            ob_end_clean();
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid or expired verification code']);
            $stmt->close();
            exit;
        }

        $codeRecord = $result->fetch_assoc();
        $stmt->close();

        // Check if too many attempts
        if ($codeRecord['attempted'] >= 3) {
            ob_end_clean();
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Too many failed attempts']);
            exit;
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            exit;
        }

        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            exit;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Create user account
        $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssss", $username, $email, $phone, $hashedPassword);
        $stmt->execute();
        $stmt->close();

        // Send registration alert email to support@thetradingroutine.com
        require_once __DIR__ . '/notify_admin.php';
        $adminNotificationSent = notifySupportNewRegistration($username, $email, !empty($phone) ? $phone : 'N/A');

        // Delete verification code after success
        $stmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ? AND code = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $email, $code);
        $stmt->execute();
        $stmt->close();

        ob_end_clean();
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'username' => $username,
            'email' => $email,
            'admin_notification_sent' => $adminNotificationSent
        ]);
        exit;
    }

    // ===== LEGACY ACTION (backward compatibility) =====
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone    = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($email) || empty($password)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    if (strlen($password) < 6) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }

    // Hash password and create user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssss", $username, $email, $phone, $hashedPassword);
    $stmt->execute();
    $stmt->close();

    // Send registration alert email to support@thetradingroutine.com
    require_once __DIR__ . '/notify_admin.php';
    $adminNotificationSent = notifySupportNewRegistration($username, $email, !empty($phone) ? $phone : 'N/A');

    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully',
        'username' => $username,
        'email' => $email,
        'admin_notification_sent' => $adminNotificationSent
    ]);
} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

// Close database connection
if (isset($conn)) {
    $conn->close();
}
