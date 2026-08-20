<?php
header('Content-Type: application/json');
include 'config.php';
date_default_timezone_set('Asia/Beirut');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$email = isset($input['email']) ? trim($conn->real_escape_string($input['email'])) : '';

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit();
}

// Check if email exists
$result = $conn->query("SELECT id, username FROM users WHERE email = '$email' LIMIT 1");
if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No account found with this email']);
    exit();
}

$user = $result->fetch_assoc();

// Generate secure token
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Save token to DB
$conn->query("UPDATE users SET reset_token = '$token', reset_expires = '$expires' WHERE email = '$email'");

// Build reset link
$isLocalhost = isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
$baseUrl = $isLocalhost ? 'http://localhost/trading' : 'https://thetradingroutine.com';
$resetLink = $baseUrl . '/reset_password.php?token=' . $token;

require_once __DIR__ . '/email_template.php';

// Send email
$body = "
<!DOCTYPE html>
<html>
<body style='margin:0;padding:0;background:#0a0e27;font-family:Arial,sans-serif;'>
    <div style='max-width:600px;margin:0 auto;padding:40px 20px;'>
        <div style='text-align:center;margin-bottom:40px;'>
            <h1 style='color:#00d4ff;font-size:2rem;margin:0;letter-spacing:2px;'>THE TRADING ROUTINE</h1>
            <p style='color:#666;margin:5px 0 0 0;font-size:0.9rem;'>thetradingroutine.com</p>
        </div>
        <div style='background:linear-gradient(135deg,#1a1f3a,#0a0e27);border:1px solid rgba(0,212,255,0.3);border-radius:16px;padding:40px;box-shadow:0 20px 60px rgba(0,212,255,0.1);'>
            <div style='text-align:center;margin-bottom:30px;'>
                <div style='display:inline-block;background:rgba(0,212,255,0.1);border:2px solid rgba(0,212,255,0.3);border-radius:50%;width:80px;height:80px;line-height:80px;font-size:2.5rem;text-align:center;'>🔐</div>
            </div>
            <h2 style='color:#ffffff;text-align:center;font-size:1.8rem;margin:0 0 10px 0;'>Password Reset</h2>
            <p style='color:#888;text-align:center;margin:0 0 30px 0;font-size:1rem;'>Your account security is our priority</p>
            <p style='color:#cccccc;font-size:1rem;margin:0 0 20px 0;'>Hello <strong style='color:#00d4ff;'>{$user['username']}</strong>,</p>
            <p style='color:#888;font-size:0.95rem;line-height:1.6;margin:0 0 30px 0;'>We received a request to reset your password. Click the button below to set a new password. This link expires in <strong style='color:#f59d00;'>1 hour</strong>.</p>
            <div style='text-align:center;margin:0 0 30px 0;'>
                <a href='{$resetLink}' style='display:inline-block;background:white;color:#000;padding:16px 48px;border-radius:10px;font-weight:bold;font-size:1.1rem;text-decoration:none;letter-spacing:1px;'>Change Password →</a>
            </div>
            <div style='background:rgba(245,157,0,0.1);border:1px solid rgba(245,157,0,0.3);border-radius:10px;padding:15px 20px;margin:0 0 30px 0;'>
                <p style='margin:0;color:#f59d00;font-size:0.9rem;'>⚠️ If you did not request this, ignore this email — your password will not change.</p>
            </div>
            <div style='border-top:1px solid rgba(0,212,255,0.1);padding-top:20px;'>
                <p style='color:#555;font-size:0.85rem;text-align:center;margin:0;'>Or copy this link: <a href='{$resetLink}' style='color:#00d4ff;word-break:break-all;'>{$resetLink}</a></p>
            </div>
        </div>
        <div style='text-align:center;margin-top:30px;'>
            <p style='color:#444;font-size:0.8rem;margin:0;'>© 2026 The Trading Routine. All rights reserved.</p>
            <p style='color:#444;font-size:0.8rem;margin:5px 0 0 0;'><a href='https://thetradingroutine.com' style='color:#00d4ff;text-decoration:none;'>thetradingroutine.com</a></p>
        </div>
    </div>
</body>
</html>
";
$safeUsername = htmlspecialchars((string) $user['username'], ENT_QUOTES, 'UTF-8');
$safeResetLink = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');
$body = brandedEmailTemplate(
    'Reset Your Password',
    "<p style='margin:0 0 16px'>Hello <strong style='color:#64e7ff'>{$safeUsername}</strong>,</p>
     <p style='margin:0 0 22px'>We received a request to reset your password. This secure link expires in <strong style='color:#ffffff'>1 hour</strong>.</p>
     <div style='text-align:center;margin:26px 0'>
       <a href='{$safeResetLink}' style='display:inline-block;padding:14px 26px;border-radius:10px;background:#27d7f5;color:#071224;font-weight:800;text-decoration:none'>Change Password</a>
     </div>
     <p style='margin:0 0 8px;color:#8fa3bf;font-size:13px'>If the button does not work, copy this link:</p>
     <p style='margin:0 0 18px;word-break:break-all'><a href='{$safeResetLink}' style='color:#64e7ff'>{$safeResetLink}</a></p>
     <p style='margin:0;color:#8fa3bf;font-size:13px'>If you did not request a password reset, ignore this email. Your password will remain unchanged.</p>",
    'Secure password reset instructions from THE TRADING ROUTINE'
);

// SMTP send
$smtpHost = getenv('SMTP_HOST') ?: 'smtp.hostinger.com';
$smtpPort = (int) (getenv('SMTP_PORT') ?: 587);
$smtpUsername = getenv('SMTP_USERNAME') ?: '';
$smtpPassword = getenv('SMTP_PASSWORD') ?: '';
$senderEmail = getenv('SMTP_FROM_EMAIL') ?: $smtpUsername;
$senderName = getenv('SMTP_FROM_NAME') ?: 'The Trading Routine';
$subject = 'Reset Your Password - The Trading Routine';

try {
    if ($smtpUsername === '' || $smtpPassword === '') {
        throw new Exception('SMTP is not configured');
    }
    $socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 20);
    if (!$socket) throw new Exception("Could not connect: $errstr");
    stream_set_timeout($socket, 10);

    function smtp_cmd_fp2($socket, $cmd) {
        fwrite($socket, $cmd . "\r\n");
        $response = '';
        do {
            $line = fgets($socket, 1024);
            if (!$line) break;
            $response .= $line;
        } while (strlen($line) > 3 && $line[3] === '-');
        return $response;
    }

    fgets($socket, 1024);
    smtp_cmd_fp2($socket, "EHLO " . gethostname());
    usleep(100000);
    smtp_cmd_fp2($socket, "STARTTLS");
    usleep(100000);

    $tlsOk = false;
    foreach ([
        defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT : null,
        STREAM_CRYPTO_METHOD_TLS_CLIENT
    ] as $method) {
        if ($method && @stream_socket_enable_crypto($socket, true, $method)) {
            $tlsOk = true; break;
        }
    }
    if (!$tlsOk) throw new Exception("TLS failed");
    usleep(100000);

    smtp_cmd_fp2($socket, "EHLO " . gethostname());
    usleep(100000);
    smtp_cmd_fp2($socket, "AUTH LOGIN");
    usleep(100000);
    smtp_cmd_fp2($socket, base64_encode($smtpUsername));
    usleep(100000);
    smtp_cmd_fp2($socket, base64_encode($smtpPassword));
    usleep(100000);
    smtp_cmd_fp2($socket, "MAIL FROM:<$senderEmail>");
    usleep(100000);
    smtp_cmd_fp2($socket, "RCPT TO:<$email>");
    usleep(100000);
    smtp_cmd_fp2($socket, "DATA");
    usleep(100000);

    $headers  = "From: $senderName <$senderEmail>\r\n";
    $headers .= "To: $email\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "MIME-Version: 1.0\r\n\r\n";

    fwrite($socket, $headers . $body . "\r\n.\r\n");
    usleep(100000);
    fgets($socket, 1024);
    smtp_cmd_fp2($socket, "QUIT");
    @fclose($socket);

    echo json_encode(['success' => true, 'message' => 'Password reset email sent']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
}

$conn->close();
?>
