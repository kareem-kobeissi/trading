<?php
// Start output buffering
ob_start();

// Set headers
header('Content-Type: application/json; charset=utf-8');

// Disable errors from showing
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Create logs directory
$log_dir = '../logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

// Get database connection
require_once '../config.php';

// Get mail config
require_once 'mail-config.php';

// Get request data
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$email = isset($data['email']) ? trim($data['email']) : null;

// Validate email
if (!$email) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

try {
    // Generate 6-digit code
    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Log code generation
    @file_put_contents(
        $log_dir . '/codes_generated.log',
        "[" . date('Y-m-d H:i:s') . "] Email: $email | Code: $code\n",
        FILE_APPEND
    );

    // Delete any existing codes for this email
    $deleteStmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ?");
    if ($deleteStmt) {
        $deleteStmt->bind_param("s", $email);
        @$deleteStmt->execute();
        $deleteStmt->close();
    }

    // Insert new code
    $insertStmt = $conn->prepare("INSERT INTO verification_codes (email, code, created_at, expires_at, attempted) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 15 MINUTE), 0)");
    if (!$insertStmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $insertStmt->bind_param("ss", $email, $code);
    if (!$insertStmt->execute()) {
        throw new Exception("Failed to save code: " . $insertStmt->error);
    }
    $insertStmt->close();

    // Prepare email content
    $subject = "Your Verification Code - Trading Routine";
    $html_message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
            .header h1 { margin: 0; }
            .content { color: #333; line-height: 1.6; }
            .code-box { background: #f0f8ff; border: 2px solid #00d4ff; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0; }
            .code { font-size: 36px; font-weight: bold; color: #00d4ff; letter-spacing: 5px; font-family: 'Courier New', monospace; margin: 0; }
            .expiry { color: #ff6600; font-weight: bold; }
            .warning { background: #fffbcc; border-left: 4px solid #ffcc00; padding: 10px; margin: 15px 0; font-size: 13px; color: #666; }
            .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✉️ Email Verification</h1>
            </div>
            <div class='content'>
                <p>Hello,</p>
                <p>You requested to verify your email address for your Trading Routine account.</p>
                <p><strong>Your verification code is:</strong></p>
                <div class='code-box'>
                    <div class='code'>$code</div>
                </div>
                <p>This code will expire in <span class='expiry'>15 minutes</span>.</p>
                <div class='warning'>
                    <strong>⚠️ Security Notice:</strong> Never share this code with anyone. Trading Routine will never ask for your code via email or phone.
                </div>
                <p>If you did not request this verification code, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>&copy; 2026 Trading Routine. All rights reserved.</p>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Attempt to send email
    $mail_sent = false;
    $send_method = '';

    // Try Gmail SMTP if configured
    if (USE_GMAIL_SMTP && GMAIL_ADDRESS && GMAIL_PASSWORD) {
        $send_method = 'gmail_smtp';

        try {
            $mail_sent = sendEmailViaGmailSMTP(
                GMAIL_ADDRESS,
                GMAIL_PASSWORD,
                SENDER_EMAIL,
                SENDER_NAME,
                $email,
                $subject,
                $html_message
            );

            if ($mail_sent) {
                @file_put_contents(
                    $log_dir . '/emails_sent.log',
                    "[" . date('Y-m-d H:i:s') . "] ✓ SUCCESS Gmail SMTP | To: $email | Code: $code\n",
                    FILE_APPEND
                );
            } else {
                @file_put_contents(
                    $log_dir . '/emails_failed.log',
                    "[" . date('Y-m-d H:i:s') . "] ✗ FAILED Gmail SMTP | To: $email\n",
                    FILE_APPEND
                );
            }
        } catch (Exception $e) {
            @file_put_contents(
                $log_dir . '/smtp_errors.log',
                "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n",
                FILE_APPEND
            );
            $mail_sent = false;
        }
    }

    // Fallback to PHP mail()
    if (!$mail_sent) {
        $send_method = 'php_mail';

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: noreply@thetradingroutine.com\r\n";

        $mail_sent = @mail($email, $subject, $html_message, $headers);

        if ($mail_sent) {
            @file_put_contents(
                $log_dir . '/emails_sent.log',
                "[" . date('Y-m-d H:i:s') . "] ✓ SUCCESS PHP mail() | To: $email | Code: $code\n",
                FILE_APPEND
            );
        } else {
            @file_put_contents(
                $log_dir . '/emails_failed.log',
                "[" . date('Y-m-d H:i:s') . "] ✗ FAILED PHP mail() | To: $email\n",
                FILE_APPEND
            );
        }
    }

    // Return success response
    http_response_code(200);
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => $mail_sent ? "Verification code sent to $email" : "Code generated (email delivery failed)",
        'email' => $email,
        'code' => $code,  // For development testing
        'sent' => $mail_sent,
        'method' => $send_method
    ]);
} catch (Exception $e) {
    @file_put_contents(
        $log_dir . '/api_errors.log',
        "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n",
        FILE_APPEND
    );

    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close database
if (isset($conn)) {
    $conn->close();
}

/**
 * Send email via Gmail SMTP
 */
function sendEmailViaGmailSMTP($auth_email, $auth_password, $display_email, $display_name, $to_email, $subject, $message)
{
    $host = 'smtp.gmail.com';
    $port = 587;

    // Remove spaces from password
    $auth_password = str_replace(' ', '', $auth_password);

    // Connect to server
    $socket = @fsockopen($host, $port, $errno, $errstr, 15);
    if (!$socket) {
        throw new Exception("Failed to connect to $host:$port");
    }

    // Get server response
    $response = fgets($socket, 1024);
    if (strpos($response, '220') === false) {
        fclose($socket);
        throw new Exception("Server returned: $response");
    }

    // Send EHLO
    fputs($socket, "EHLO " . gethostname() . "\r\n");
    while (true) {
        $response = fgets($socket, 1024);
        if (substr($response, 3, 1) == ' ') {
            break;
        }
    }

    // Start TLS
    fputs($socket, "STARTTLS\r\n");
    $response = fgets($socket, 1024);
    if (strpos($response, '220') === false) {
        fclose($socket);
        throw new Exception("TLS not available");
    }

    // Enable encryption
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
        fclose($socket);
        throw new Exception("Failed to enable TLS");
    }

    // Send EHLO again after TLS
    fputs($socket, "EHLO " . gethostname() . "\r\n");
    while (true) {
        $response = fgets($socket, 1024);
        if (substr($response, 3, 1) == ' ') {
            break;
        }
    }

    // Authenticate
    fputs($socket, "AUTH LOGIN\r\n");
    fgets($socket, 1024);

    fputs($socket, base64_encode($auth_email) . "\r\n");
    fgets($socket, 1024);

    fputs($socket, base64_encode($auth_password) . "\r\n");
    $response = fgets($socket, 1024);
    if (strpos($response, '235') === false) {
        fclose($socket);
        throw new Exception("Authentication failed: $response");
    }

    // Send FROM (use auth email for SMTP)
    fputs($socket, "MAIL FROM:<" . $auth_email . ">\r\n");
    $response = fgets($socket, 1024);

    // Send TO
    fputs($socket, "RCPT TO:<" . $to_email . ">\r\n");
    $response = fgets($socket, 1024);

    // Send DATA
    fputs($socket, "DATA\r\n");
    fgets($socket, 1024);

    // Compose full message with display email
    $full_message = "From: " . $display_name . " <" . $display_email . ">\r\n";
    $full_message .= "To: " . $to_email . "\r\n";
    $full_message .= "Subject: " . $subject . "\r\n";
    $full_message .= "MIME-Version: 1.0\r\n";
    $full_message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $full_message .= "Content-Transfer-Encoding: 7bit\r\n";
    $full_message .= "\r\n";
    $full_message .= $message . "\r\n";

    fputs($socket, $full_message . "\r\n.\r\n");
    $response = fgets($socket, 1024);

    // Quit
    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return strpos($response, '250') !== false;
}
