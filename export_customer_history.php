<?php
require_once 'config.php';
require_once 'admin_log_schema.php';

date_default_timezone_set('Asia/Beirut');
$schemaError = ensureAdminLogSchema($conn);
if ($schemaError) {
    http_response_code(500);
    exit($schemaError);
}

$email = trim($_GET['email'] ?? '');
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('A valid customer email is required.');
}

function csvSafe($value)
{
    $value = (string)($value ?? '');
    if (preg_match('/^[=+\-@]/', $value)) {
        $value = "'" . $value;
    }
    return $value;
}

function productName($type)
{
    $names = [
        'course' => 'Trading Mastery Course',
        'ea' => 'TTR Risk Calculator',
        'robot' => 'TTR Robot',
        'robot_sr' => 'S&R Precision EA',
        'robot_ib' => 'Instant Breakout EA'
    ];
    return $names[$type] ?? ($type ?: '-');
}

$ordersSql = "SELECT o.order_ref, o.name, o.phone, o.email, o.payment_method,
                     o.created_at, o.cancelled_at, o.unlocked_at,
                     oi.price, oi.product_type, oi.item_status
              FROM orders o
              JOIN order_items oi ON oi.order_id = o.id
              WHERE LOWER(o.email) = LOWER(?)
              ORDER BY o.created_at DESC, oi.id DESC";
$ordersStmt = $conn->prepare($ordersSql);
if (!$ordersStmt) {
    http_response_code(500);
    exit('Unable to prepare customer export.');
}
$ordersStmt->bind_param('s', $email);
$ordersStmt->execute();
$orders = $ordersStmt->get_result();

$safeFile = preg_replace('/[^a-zA-Z0-9_-]+/', '_', strstr($email, '@', true) ?: 'customer');
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="Customer_' . $safeFile . '_History_' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-store, no-cache, must-revalidate');

$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['CUSTOMER ORDER DETAILS']);
fputcsv($output, ['Ref ID', 'Full Name', 'Phone', 'Email', 'Product', 'Created Date/Time', 'Total']);

$customerName = '-';
while ($row = $orders->fetch_assoc()) {
    $customerName = $row['name'] ?: $customerName;
    $price = (float)$row['price'];
    $fee = round($price * 0.01, 2);
    $reference = '#' . substr((string)$row['order_ref'], -6);
    $createdAt = $row['created_at'] ? date('F j, Y \a\t H:i', strtotime($row['created_at'])) : '-';
    fputcsv($output, array_map('csvSafe', [
        $reference,
        $row['name'],
        $row['phone'],
        $row['email'],
        productName($row['product_type']),
        $createdAt,
        '$' . number_format($price + $fee, 2, '.', '') . ' USD'
    ]));
}
$ordersStmt->close();

$logStmt = $conn->prepare("INSERT INTO admin_logs (order_ref, customer_name, customer_email, action, product_type, performed_at) VALUES ('ALL', ?, ?, 'export_excel', 'all', NOW())");
if ($logStmt) {
    $logStmt->bind_param('ss', $customerName, $email);
    $logStmt->execute();
    $logStmt->close();
}

fclose($output);
$conn->close();
exit;
