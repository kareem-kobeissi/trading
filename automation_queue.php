<?php

require_once __DIR__ . '/automation_schema.php';

function automationUuidV4()
{
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function queueAutomationEvent($conn, $orderId, $eventType, array $payload)
{
    $schemaError = ensureAutomationSchema($conn);
    if ($schemaError) {
        error_log($schemaError);
        return false;
    }

    $eventUuid = automationUuidV4();
    $payload['event_id'] = $eventUuid;
    $payload['event_type'] = $eventType;
    $payload['created_at'] = gmdate('c');
    $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($payloadJson === false) {
        error_log('Unable to encode automation event payload.');
        return false;
    }

    $stmt = $conn->prepare(
        "INSERT INTO automation_events
         (event_uuid, order_id, event_type, payload_json, delivery_status, next_attempt_at)
         VALUES (?, ?, ?, ?, 'queued', NOW())"
    );
    if (!$stmt) {
        error_log('Unable to prepare automation event: ' . $conn->error);
        return false;
    }
    $stmt->bind_param('siss', $eventUuid, $orderId, $eventType, $payloadJson);
    $saved = $stmt->execute();
    if (!$saved) {
        error_log('Unable to queue automation event: ' . $stmt->error);
    }
    $stmt->close();
    return $saved ? $eventUuid : false;
}
