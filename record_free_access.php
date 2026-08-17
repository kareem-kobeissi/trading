<?php
require_once 'config.php';
require_once 'user_schema.php';
require_once 'automation_queue.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!isset($_SESSION['user_id'])) {
    if (isset($_GET['product'])) {
        header('Location: login.php');
    } else {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Login required']);
    }
    exit;
}

$products = [
    'course' => ['course_id' => 1, 'type' => 'course', 'redirect' => 'courses.php'],
    'ea' => ['course_id' => 2, 'type' => 'ea', 'redirect' => 'ea.php'],
    'indicator' => ['course_id' => 5, 'type' => 'indicator', 'redirect' => 'https://www.tradingview.com/script/HH96qNwI-The-Holly-Grail/']
];

$productKey = trim($_POST['product'] ?? $_GET['product'] ?? '');
if (!isset($products[$productKey])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

$schemaError = ensureUserPhoneColumn($conn);
if ($schemaError) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $schemaError]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$product = $products[$productKey];
$type = $product['type'];

$existing = $conn->prepare("SELECT oi.id FROM order_items oi JOIN orders o ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_type = ? LIMIT 1");
$existing->bind_param('is', $userId, $type);
$existing->execute();
$alreadyRecorded = $existing->get_result()->num_rows > 0;
$existing->close();

if (!$alreadyRecorded) {
    $userStmt = $conn->prepare("SELECT username, email, phone FROM users WHERE id = ? LIMIT 1");
    $userStmt->bind_param('i', $userId);
    $userStmt->execute();
    $user = $userStmt->get_result()->fetch_assoc();
    $userStmt->close();

    if (!$user) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $orderRef = 'REQ-' . strtoupper($productKey) . '-' . $userId . '-' . date('YmdHis');
    $paymentMethod = 'request';
    $status = 'pending';
    // Standard course price. Access may be granted free after the administrator
    // confirms registration through the official broker partner.
    $total = 200.00;

    $conn->begin_transaction();
    try {
        $orderStmt = $conn->prepare("INSERT INTO orders (order_ref, user_id, name, phone, email, payment_method, total_price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $orderStmt->bind_param('sissssds', $orderRef, $userId, $user['username'], $user['phone'], $user['email'], $paymentMethod, $total, $status);
        $orderStmt->execute();
        $orderId = $conn->insert_id;
        $orderStmt->close();

        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, course_id, price, product_type, item_status) VALUES (?, ?, ?, ?, 'pending')");
        $itemStmt->bind_param('iids', $orderId, $product['course_id'], $total, $type);
        $itemStmt->execute();
        $itemStmt->close();
        $conn->commit();

        $productNames = [
            'course' => 'Trading Mastery Course',
            'ea' => 'Expert Advisor',
            'indicator' => 'The Holly Grail Indicator'
        ];
        queueAutomationEvent($conn, $orderId, 'order.pending', [
            'order' => [
                'id' => $orderId,
                'reference' => $orderRef,
                'status' => 'pending',
                'customer' => [
                    'name' => $user['username'],
                    'email' => $user['email'],
                    'phone' => $user['phone']
                ],
                'payment_method' => $paymentMethod,
                'total' => $total,
                'currency' => 'USD',
                'items' => [[
                    'type' => $type,
                    'name' => $productNames[$type],
                    'price' => $total
                ]]
            ],
            'owner' => ['whatsapp' => '+96171493997']
        ]);
    } catch (Throwable $error) {
        $conn->rollback();
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Could not record free access']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Location: ' . $product['redirect']);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'recorded' => !$alreadyRecorded]);
$conn->close();
