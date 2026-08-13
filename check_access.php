<?php
// check_access.php — Returns status ('unlocked', 'pending', 'cancelled', 'none') for a product
require_once 'config.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$email  = trim($_POST['email'] ?? $_GET['email'] ?? '');
$product = trim($_POST['product'] ?? $_GET['product'] ?? 'course');

if (!$product) {
    $product = 'course';
}

if (!$userId && !$email) {
    echo json_encode(['success' => false, 'has_access' => false, 'status' => 'none', 'message' => 'Not logged in']);
    exit();
}

$whereClause = "";
if ($userId > 0) {
    $whereClause = "o.user_id = " . intval($userId);
} else {
    $cleanEmail = $conn->real_escape_string($email);
    $whereClause = "o.email = '$cleanEmail'";
}

$cleanProduct = $conn->real_escape_string($product);

// Check order items for unlocked or pending status
$sql = "SELECT oi.item_status, o.status as order_status
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE $whereClause
        AND oi.product_type = '$cleanProduct'
        ORDER BY FIELD(oi.item_status, 'unlocked', 'pending', 'cancelled')
        LIMIT 1";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    $status = 'none';
} else {
    $row = $result->fetch_assoc();
    $status = $row['item_status'] ?: $row['order_status'];
}

$hasAccess = ($status === 'unlocked');

echo json_encode([
    'success'    => true,
    'has_access' => $hasAccess,
    'status'     => $status
]);

$conn->close();
