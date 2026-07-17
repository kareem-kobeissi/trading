<?php
// save_order.php

// ===== DEBUG (REMOVE LATER) =====
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'config.php';

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

$userResult = $conn->query("SELECT id FROM users WHERE email = '$emailEscaped' LIMIT 1");

$user_id = 0;

if ($userResult && $userResult->num_rows > 0) {
    $user_id = $userResult->fetch_assoc()['id'];
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

// ===== SUCCESS =====
echo json_encode([
    'success' => true,
    'message' => 'Order saved successfully',
    'order_id' => $order_id,
    'order_ref' => $order_ref
]);

$conn->close();
?>