<?php
header('Content-Type: application/json');
include 'config.php';
date_default_timezone_set('Asia/Beirut');
$conn->query("SET time_zone = '+02:00'");

$sql = "SELECT * FROM admin_logs ORDER BY performed_at DESC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit();
}

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = [
        'id'             => $row['id'],
        'order_ref'      => $row['order_ref'],
        'customer_name'  => $row['customer_name'],
        'customer_email' => $row['customer_email'],
        'action'         => $row['action'],
        'product_type'   => $row['product_type'],
        'performed_at'   => strtotime($row['performed_at']) * 1000
    ];
}

echo json_encode(['success' => true, 'logs' => $logs]);
$conn->close();
?>