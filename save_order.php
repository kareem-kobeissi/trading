<?php
// save_order.php

// ===== DEBUG (REMOVE LATER) =====
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'config.php';
require_once __DIR__ . '/automation_queue.php';

date_default_timezone_set('Asia/Beirut');
$conn->query("SET time_zone = '+03:00'");

// ===== ENSURE POST =====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}

// ===== READ INPUT SAFELY =====
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// fallback if JSON fails
if (!is_array($input)) {
    $input = $_POST;
}

// ===== VALIDATE INPUT =====
$order_ref = isset($input['order_ref']) ? $conn->real_escape_string($input['order_ref']) : '';
$name      = isset($input['name']) ? $conn->real_escape_string($input['name']) : '';
$phone     = isset($input['phone']) ? $conn->real_escape_string($input['phone']) : '';
$email     = isset($input['email']) ? $conn->real_escape_string($input['email']) : '';
$payment_method = isset($input['payment_method']) ? $conn->real_escape_string($input['payment_method']) : 'whatsapp';
$total_price = isset($input['total']) ? floatval($input['total']) : 0;

// handle items safely
$items = isset($input['items']) ? $input['items'] : [];

if (!$order_ref || !$name || !$email || $total_price < 0 || empty($items)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields',
        'debug' => $input
    ]);
    exit();
}

// ===== GET USER ID =====
$emailEscaped = $conn->real_escape_string($email);

$userResult = $conn->query("SELECT id, phone FROM users WHERE email = '$emailEscaped' LIMIT 1");

$user_id = 0;

if ($userResult && $userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $user_id = (int) $userRow['id'];
    $savedPhone = trim((string) ($userRow['phone'] ?? ''));
    if ($savedPhone === '') {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'code' => 'phone_verification_required',
            'message' => 'Verify your WhatsApp number before creating an order'
        ]);
        exit();
    }
    // The verified database value is authoritative; never trust a client-side
    // phone override when creating the order or automation payload.
    $phone = $conn->real_escape_string($savedPhone);
}

// ===== INSERT ORDER =====
$sql = "
INSERT INTO orders 
(order_ref, user_id, name, phone, email, payment_method, total_price, status, created_at)
VALUES 
('$order_ref', $user_id, '$name', '$phone', '$emailEscaped', '$payment_method', $total_price, 'pending', NOW())
";

if (!$conn->query($sql)) {
    echo json_encode([
        'success' => false,
        'message' => 'Order insert failed',
        'error' => $conn->error
    ]);
    exit();
}

$order_id = $conn->insert_id;

// ===== INSERT ORDER ITEMS =====
foreach ($items as $item) {

    $price = floatval($item['price'] ?? 0);
    $type  = $conn->real_escape_string($item['type'] ?? 'course');

    if ($type === 'ea') {

        $sqlItem = "
        INSERT INTO order_items (order_id, course_id, price, product_type)
        VALUES ($order_id, 2, $price, 'ea')
        ";

    } elseif ($type === 'robot' || $type === 'robot_sr') {
        
        $sqlItem = "
        INSERT INTO order_items (order_id, course_id, price, product_type)
        VALUES ($order_id, 3, $price, 'robot_sr')
        ";

    } elseif ($type === 'robot_ib') {
        
        $sqlItem = "
        INSERT INTO order_items (order_id, course_id, price, product_type)
        VALUES ($order_id, 4, $price, 'robot_ib')
        ";

    } else {

        // ✅ FIXED HERE
        $course_id = 1;

        $sqlItem = "
        INSERT INTO order_items (order_id, course_id, price, product_type)
        VALUES ($order_id, $course_id, $price, 'course')
        ";
    }

    if (!$conn->query($sqlItem)) {
        echo json_encode([
            'success' => false,
            'message' => 'Order item insert failed',
            'error' => $conn->error
        ]);
        exit();
    }
}

// Build a minimal, privacy-conscious payload for the external automation.
$productNames = [
    'course' => 'Trading Mastery Course',
    'ea' => 'TTR Risk Calculator EA',
    'robot' => 'TTR Robot',
    'robot_sr' => 'S&R Precision EA',
    'robot_ib' => 'Instant Breakout EA',
    'indicator' => 'The Holly Grail Indicator'
];
$automationItems = [];
foreach ($items as $automationItem) {
    $automationType = trim((string) ($automationItem['type'] ?? 'course'));
    $automationTitle = trim((string) ($automationItem['title'] ?? ''));
    $automationItems[] = [
        'type' => $automationType,
        'name' => $automationTitle !== '' ? $automationTitle : ($productNames[$automationType] ?? 'Trading Product'),
        'price' => round((float) ($automationItem['price'] ?? 0), 2)
    ];
}

// Delivery is handled by cron, so checkout remains fast if automation is down.
$automationEventId = queueAutomationEvent($conn, $order_id, 'order.pending', [
    'order' => [
        'id' => $order_id,
        'reference' => $order_ref,
        'status' => 'pending',
        'customer' => [
            'name' => html_entity_decode($name, ENT_QUOTES, 'UTF-8'),
            'email' => $email,
            'phone' => html_entity_decode($phone, ENT_QUOTES, 'UTF-8')
        ],
        'payment_method' => $payment_method,
        'total' => round($total_price, 2),
        'currency' => 'USD',
        'items' => $automationItems
    ],
    'owner' => ['whatsapp' => '+96171493997']
]);
// ===== SUCCESS =====
echo json_encode([
    'success' => true,
    'message' => 'Order saved successfully',
    'order_id' => $order_id,
    'order_ref' => $order_ref,
    'automation_queued' => $automationEventId !== false
]);

$conn->close();
?>
