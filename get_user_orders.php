<?php
// get_user_orders.php
header('Content-Type: application/json');
include 'config.php';
date_default_timezone_set('Asia/Beirut');
$conn->query("SET time_zone = '+03:00'");

// Get email from session or POST
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = '';

// Try to get from POST
if (isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
} elseif (isset($_GET['email'])) {
    $email = $conn->real_escape_string($_GET['email']);
} elseif (isset($_SESSION['email'])) {
    $email = $conn->real_escape_string($_SESSION['email']);
}

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'No email provided']);
    exit();
}

// Fetch orders for this user — per item with per-item status
$sql = "SELECT 
    o.id,
    o.order_ref,
    o.name,
    o.phone,
    o.email,
    o.payment_method,
    o.created_at,
    o.cancelled_at,
    o.unlocked_at,
    oi.id          AS item_id,
    oi.price       AS item_price,
    oi.product_type,
    oi.item_status
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
WHERE o.email = '$email'
ORDER BY o.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query failed: ' . $conn->error]);
    exit();
}

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'id'            => $row['order_ref'] ?: ('ORD-' . $row['id']),
        'db_id'         => $row['id'],
        'item_id'       => $row['item_id'],
        'name'          => $row['name'],
        'phone'         => $row['phone'],
        'email'         => $row['email'],
        'paymentMethod' => $row['payment_method'],
        'total'         => round($row['item_price'] * 1.01, 2),
        'status'        => $row['item_status'],
        'createdAt'     => date('F j, Y', strtotime($row['created_at'])),
        'createdTime'   => strtotime($row['created_at']) * 1000,
        'cancelledAt'   => $row['cancelled_at'],
        'unlockedAt'    => $row['unlocked_at'],
        'product_type'  => $row['product_type'],
    ];
}

echo json_encode(['success' => true, 'orders' => $orders]);
$conn->close();
