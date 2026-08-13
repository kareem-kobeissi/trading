<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/automation_config.php';
require_once __DIR__ . '/automation_schema.php';

if (!automationIsConfigured()) {
    fwrite(STDERR, "Automation webhook URL or secret is not configured.\n");
    exit(1);
}

$schemaError = ensureAutomationSchema($conn);
if ($schemaError) {
    fwrite(STDERR, $schemaError . "\n");
    exit(1);
}

$events = $conn->query(
    "SELECT id, event_uuid, payload_json, attempts
     FROM automation_events
     WHERE delivery_status IN ('queued','failed')
       AND attempts < 8
       AND (next_attempt_at IS NULL OR next_attempt_at <= NOW())
     ORDER BY id ASC
     LIMIT 20"
);

while ($event = $events->fetch_assoc()) {
    $eventId = (int) $event['id'];
    $conn->query("UPDATE automation_events SET delivery_status = 'processing', attempts = attempts + 1 WHERE id = $eventId");

    $timestamp = (string) time();
    $signature = hash_hmac(
        'sha256',
        $timestamp . '.' . $event['payload_json'],
        automationConfig('TTR_AUTOMATION_WEBHOOK_SECRET')
    );
    $headers = [
        'Content-Type: application/json',
        'X-TTR-Event-ID: ' . $event['event_uuid'],
        'X-TTR-Timestamp: ' . $timestamp,
        'X-TTR-Signature: sha256=' . $signature
    ];

    $ch = curl_init(automationConfig('TTR_AUTOMATION_WEBHOOK_URL'));
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $event['payload_json'],
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 60
    ]);
    $response = curl_exec($ch);
    $responseCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($responseCode >= 200 && $responseCode < 300) {
        $stmt = $conn->prepare(
            "UPDATE automation_events
             SET delivery_status = 'delivered', delivered_at = NOW(), response_code = ?, last_error = NULL
             WHERE id = ?"
        );
        $stmt->bind_param('ii', $responseCode, $eventId);
        $stmt->execute();
        $stmt->close();
        echo "Delivered event {$event['event_uuid']}.\n";
        continue;
    }

    $error = $curlError ?: ('HTTP ' . $responseCode . ': ' . substr((string) $response, 0, 500));
    $attempt = ((int) $event['attempts']) + 1;
    $delayMinutes = min(60, 2 ** min($attempt, 5));
    $stmt = $conn->prepare(
        "UPDATE automation_events
         SET delivery_status = 'failed', response_code = ?, last_error = ?,
             next_attempt_at = DATE_ADD(NOW(), INTERVAL ? MINUTE)
         WHERE id = ?"
    );
    $stmt->bind_param('isii', $responseCode, $error, $delayMinutes, $eventId);
    $stmt->execute();
    $stmt->close();
    fwrite(STDERR, "Failed event {$event['event_uuid']}: $error\n");
}

$conn->close();
