<?php

// Sends a non-blocking admin notification after a successful registration.
function notifySupportNewRegistration($username, $email, $phone = 'N/A')
{
    $to = getenv('ADMIN_NOTIFICATION_EMAIL') ?: 'support@thetradingroutine.com';
    $subject = 'New User Registration: ' . $username;
    $registeredAt = date('Y-m-d H:i:s T');

    $safeName = htmlspecialchars((string) $username, ENT_QUOTES, 'UTF-8');
    $safeEmail = htmlspecialchars((string) $email, ENT_QUOTES, 'UTF-8');
    $safePhone = htmlspecialchars((string) $phone, ENT_QUOTES, 'UTF-8');
    $safeTime = htmlspecialchars($registeredAt, ENT_QUOTES, 'UTF-8');

    $htmlMessage = <<<HTML
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>New User Registration</title></head>
<body style="margin:0;padding:24px;background:#0b1228;color:#fff;font-family:Arial,sans-serif">
  <div style="max-width:580px;margin:auto;padding:28px;background:#121c38;border:1px solid #1bbde4;border-radius:14px">
    <h2 style="margin:0 0 10px;color:#55dcf5">New User Account Registered</h2>
    <p style="color:#d0d7de">A new user registered on The Trading Routine.</p>
    <div style="padding:18px;background:#0d1730;border-radius:10px;line-height:1.8">
      <div><strong style="color:#78e8ff">Name:</strong> {$safeName}</div>
      <div><strong style="color:#78e8ff">Email:</strong> {$safeEmail}</div>
      <div><strong style="color:#78e8ff">Phone:</strong> {$safePhone}</div>
      <div><strong style="color:#78e8ff">Registered:</strong> {$safeTime}</div>
    </div>
    <p style="margin:20px 0 0;color:#8d99ae;font-size:12px">Automated notification from The Trading Routine.</p>
  </div>
</body>
</html>
HTML;

    $emailSent = false;

    try {
        require_once __DIR__ . '/api/mail-config.php';
        require_once __DIR__ . '/libs/GmailSMTP.php';

        if (USE_GMAIL_SMTP) {
            $caFile = getenv('SMTP_CA_FILE') ?: '';
            $verifyValue = strtolower(trim((string) (getenv('SMTP_VERIFY_TLS') ?: 'true')));
            $verifyTls = !in_array($verifyValue, ['0', 'false', 'no', 'off'], true);
            $smtp = new GmailSMTP(
                GMAIL_ADDRESS,
                GMAIL_PASSWORD,
                false,
                SMTP_HOST,
                SMTP_PORT,
                $caFile,
                $verifyTls
            );
            $emailSent = $smtp->sendEmail($to, $subject, $htmlMessage);
        }
    } catch (Throwable $error) {
        error_log('Registration notification failed: ' . $error->getMessage());
    }

    if ($emailSent) {
        error_log('Registration notification accepted by SMTP for ' . $to . '.');
    } else {
        error_log('Registration notification was not sent to ' . $to . '.');
    }

    return $emailSent;
}
