<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/automation_config.php';

$jobSecret = automationConfig('TTR_JOB_SECRET');
$webhookUrl = automationConfig('TTR_AUTOMATION_WEBHOOK_URL');
$pollUrl = automationConfig(
    'TTR_AUTOMATION_POLL_URL',
    preg_replace('#/webhooks/orders/?$#', '/jobs/poll-email', $webhookUrl)
);

if ($jobSecret === '' || !$pollUrl || !filter_var($pollUrl, FILTER_VALIDATE_URL)) {
    fwrite(STDERR, "Email polling URL or job secret is not configured.\n");
    exit(1);
}

$ch = curl_init($pollUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => '',
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'X-TTR-Job-Secret: ' . $jobSecret
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 55
]);
$response = curl_exec($ch);
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($status < 200 || $status >= 300) {
    $detail = $error ?: ('HTTP ' . $status . ': ' . substr((string) $response, 0, 500));
    fwrite(STDERR, '[' . date('Y-m-d H:i:s') . "] Email poll failed: $detail\n");
    exit(1);
}

$result = json_decode((string) $response, true);
$processed = (int) ($result['processed'] ?? 0);
$skipped = (int) ($result['skipped'] ?? 0);
echo '[' . date('Y-m-d H:i:s') . "] Email poll complete: $processed processed, $skipped skipped.\n";

if (isset($conn) && $conn instanceof mysqli) $conn->close();
