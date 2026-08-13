<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/automation_queue.php';

$productNames = [
    'course' => 'Trading Mastery Course',
    'ea' => 'Expert Advisor',
    'indicator' => 'The Holly Grail Indicator',
    'robot' => 'TTR Robot',
    'robot_sr' => 'S&R Precision EA',
    'robot_ib' => 'Instant Breakout EA'
];

$result = $conn->query(
    "SELECT o.id, o.order_ref, o.name, o.email, o.phone, o.payment_method,
            o.total_price, oi.product_type, oi.price
     FROM orders o
     JOIN order_items oi ON oi.order_id = o.id
     LEFT JOIN automation_events ae
       ON ae.order_id = o.id AND ae.event_type = 'order.pending'
     WHERE o.status = 'pending' AND ae.id IS NULL
     ORDER BY o.id ASC"
);

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = (int) $row['id'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'row' => $row,
            'items' => []
        ];
    }
    $type = trim((string) $row['product_type']);
    $orders[$orderId]['items'][] = [
        'type' => $type,
        'name' => $productNames[$type] ?? 'Trading Product',
        'price' => round((float) $row['price'], 2)
    ];
}

$queued = 0;
foreach ($orders as $orderId => $data) {
    $row = $data['row'];
    $eventId = queueAutomationEvent($conn, $orderId, 'order.pending', [
        'order' => [
            'id' => $orderId,
            'reference' => $row['order_ref'],
            'status' => 'pending',
            'customer' => [
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone']
            ],
            'payment_method' => $row['payment_method'],
            'total' => round((float) $row['total_price'], 2),
            'currency' => 'USD',
            'items' => $data['items']
        ],
        'owner' => ['whatsapp' => '+96171493997']
    ]);
    if ($eventId !== false) $queued++;
}

echo "Queued $queued pending order event(s)." . PHP_EOL;
$conn->close();
