<?php

// SMTP credentials must come from server environment variables. Never store
// mailbox passwords in the public project or commit them to GitHub.
define('SMTP_HOST', function_exists('runtimeSecret') ? runtimeSecret('SMTP_HOST', 'smtp.hostinger.com') : (getenv('SMTP_HOST') ?: 'smtp.hostinger.com'));
define('SMTP_PORT', (int) (function_exists('runtimeSecret') ? runtimeSecret('SMTP_PORT', '587') : (getenv('SMTP_PORT') ?: 587)));
define('GMAIL_ADDRESS', function_exists('runtimeSecret') ? runtimeSecret('SMTP_USERNAME') : (getenv('SMTP_USERNAME') ?: ''));
define('GMAIL_PASSWORD', function_exists('runtimeSecret') ? runtimeSecret('SMTP_PASSWORD') : (getenv('SMTP_PASSWORD') ?: ''));
define('SENDER_EMAIL', function_exists('runtimeSecret') ? runtimeSecret('SMTP_FROM_EMAIL', GMAIL_ADDRESS) : (getenv('SMTP_FROM_EMAIL') ?: GMAIL_ADDRESS));
define('SENDER_NAME', function_exists('runtimeSecret') ? runtimeSecret('SMTP_FROM_NAME', 'The Trading Routine Support') : (getenv('SMTP_FROM_NAME') ?: 'The Trading Routine Support'));
define('USE_GMAIL_SMTP', GMAIL_ADDRESS !== '' && GMAIL_PASSWORD !== '');

$log_dir = '../logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

@file_put_contents(
    $log_dir . '/config_loaded.log',
    '[' . date('Y-m-d H:i:s') . '] Email config loaded. SMTP: '
        . (USE_GMAIL_SMTP ? 'ENABLED' : 'DISABLED') . "\n",
    FILE_APPEND
);
