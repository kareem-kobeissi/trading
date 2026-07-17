<?php
// Gmail Configuration - UPDATE THESE WITH YOUR DETAILS
define('GMAIL_ADDRESS', 'thetradingroutine@gmail.com');  // Your Gmail address (for authentication)
define('GMAIL_PASSWORD', 'sbkm xzof swvo lrlc');   // Your Gmail App Password
define('SENDER_EMAIL', 'thetradingroutine@gmail.com');  // Display email to customers
define('SENDER_NAME', 'The Trading Routine Support');     // Display name
define('USE_GMAIL_SMTP', true);                    // ← Change FALSE to TRUE
// File to log what's happening
$log_dir = '../logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

// Log this configuration being loaded
@file_put_contents($log_dir . '/config_loaded.log', "[" . date('Y-m-d H:i:s') . "] Email config loaded. Gmail SMTP: " . (USE_GMAIL_SMTP ? 'ENABLED' : 'DISABLED') . "\n", FILE_APPEND);
