<?php
// send_email.php - Send receipt emails via Outlook SMTP
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Create detailed debug log
$debugLog = "DEBUG LOG: " . date('Y-m-d H:i:s') . "\n";
$debugLog .= "POST data: " . json_encode($_POST) . "\n";

$response = ['success' => false, 'message' => ''];

// Get POST data
$toEmail = isset($_POST['to_email']) ? trim($_POST['to_email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';

// Validate inputs
if (empty($toEmail) || empty($subject) || empty($body)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// ============================================
// GMAIL SMTP CONFIGURATION
// ============================================
// Using Gmail SMTP (simpler than Outlook)
$smtpHost = getenv('SMTP_HOST') ?: 'smtp.hostinger.com';
$smtpPort = (int) (getenv('SMTP_PORT') ?: 587);
$smtpUsername = getenv('SMTP_USERNAME') ?: '';
$smtpPassword = getenv('SMTP_PASSWORD') ?: '';
$senderEmail = getenv('SMTP_FROM_EMAIL') ?: $smtpUsername;
$senderName = getenv('SMTP_FROM_NAME') ?: 'The Trading Routine';

// ============================================

try {
    if ($smtpUsername === '' || $smtpPassword === '') {
        throw new Exception('SMTP is not configured');
    }
    $debugLog .= "Attempting to connect to $smtpHost:$smtpPort\n";

    // Connect to SMTP server
    $socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 20);

    if (!$socket) {
        throw new Exception("Could not connect to mail server: $errstr ($errno)");
    }

    $debugLog .= "Connected to SMTP server\n";

    // Set stream timeout
    stream_set_timeout($socket, 10);

    // Helper function for SMTP commands
    function smtp_command($socket, $cmd, &$log)
    {
        $log .= "SEND: $cmd\n";
        fwrite($socket, $cmd . "\r\n");
        $response = '';
        do {
            $line = fgets($socket, 1024);
            if (!$line) break;
            $response .= $line;
        } while (strlen($line) > 3 && $line[3] === '-');
        $log .= "RECV: " . trim($response) . "\n";
        return $response;
    }

    // Read server greeting
    $greeting = fgets($socket, 1024);
    $debugLog .= "SERVER: " . trim($greeting) . "\n";

    // EHLO
    smtp_command($socket, "EHLO " . gethostname(), $debugLog);
    usleep(100000);

    // STARTTLS
    smtp_command($socket, "STARTTLS", $debugLog);
    usleep(100000);

    // Enable TLS
    $tlsMethods = [
        defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT : null,
        STREAM_CRYPTO_METHOD_TLS_CLIENT,
        defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT : null,
    ];

    $tlsOk = false;
    foreach ($tlsMethods as $method) {
        if ($method && @stream_socket_enable_crypto($socket, true, $method)) {
            $tlsOk = true;
            $debugLog .= "TLS enabled successfully\n";
            break;
        }
    }

    if (!$tlsOk) {
        throw new Exception("TLS negotiation failed");
    }
    usleep(100000);

    // EHLO again after TLS
    smtp_command($socket, "EHLO " . gethostname(), $debugLog);
    usleep(100000);

    // AUTH LOGIN
    smtp_command($socket, "AUTH LOGIN", $debugLog);
    usleep(100000);

    // Send encoded username
    fwrite($socket, base64_encode($smtpUsername) . "\r\n");
    $usernameResp = fgets($socket, 1024);
    $debugLog .= "SMTP username response: " . trim((string) $usernameResp) . "\n";
    usleep(100000);

    // Send encoded password
    fwrite($socket, base64_encode($smtpPassword) . "\r\n");
    $authResp = fgets($socket, 1024);
    $debugLog .= "SMTP authentication response: " . trim((string) $authResp) . "\n";
    usleep(100000);

    if (strpos($authResp, '235') === false && strpos($authResp, '550') !== false) {
        throw new Exception("Authentication failed: " . trim($authResp));
    }

    // MAIL FROM
    smtp_command($socket, "MAIL FROM:<$senderEmail>", $debugLog);
    usleep(100000);

    // RCPT TO
    smtp_command($socket, "RCPT TO:<$toEmail>", $debugLog);
    usleep(100000);

    // DATA
    smtp_command($socket, "DATA", $debugLog);
    usleep(100000);

    // Email headers
    $headers = "From: $senderName <$senderEmail>\r\n";
    $headers .= "To: $toEmail\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "X-Mailer: Trading Routine SMTP\r\n\r\n";

    // Send message
    $message = $headers . $body . "\r\n.";
    fwrite($socket, $message . "\r\n");
    usleep(100000);

    // Read response
    $sendResp = fgets($socket, 1024);
    $debugLog .= "SEND RESPONSE: " . trim($sendResp) . "\n";

    // QUIT
    smtp_command($socket, "QUIT", $debugLog);

    @fclose($socket);

    // Log debug info
    file_put_contents(dirname(__FILE__) . '/smtp_debug.log', $debugLog . "\n\n", FILE_APPEND);

    // Success
    ob_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => "Email sent to $toEmail"
    ]);
    exit;
} catch (Exception $e) {
    if (isset($socket) && is_resource($socket)) {
        @fclose($socket);
    }

    $debugLog .= "ERROR: " . $e->getMessage() . "\n";
    file_put_contents(dirname(__FILE__) . '/smtp_debug.log', $debugLog . "\n\n", FILE_APPEND);

    ob_clean();
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit;
}
