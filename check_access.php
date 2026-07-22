<?php
// check_access.php
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
include 'config.php';

$email = '';

if (isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
} elseif (isset($_GET['email'])) {
    $email = $conn->real_escape_string($_GET['email']);
}

if (!$email) {
    echo json_encode(['success' => false, 'has_access' => false, 'message' => 'No email provided']);
    exit();
}

// Check if user has any unlocked order item (course)
$sql = "SELECT oi.id FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE o.email = '$email'
        AND oi.item_status = 'unlocked'
        AND oi.product_type = 'course'
        LIMIT 1";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'has_access' => false, 'message' => 'Query failed']);
    exit();
}

$has_access = ($result->num_rows > 0);

echo json_encode([
    'success'    => true,
    'has_access' => $has_access
]);

$conn->close();
