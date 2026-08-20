<?php
// update_order.php
header('Content-Type: application/json');
include 'config.php';
require_once 'admin_log_schema.php';
require_once __DIR__ . '/automation_queue.php';

if (empty($_SESSION['is_admin']) && empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Administrator authentication required']);
    exit();
}

function queueOrderStatusNotification($conn, $orderId, $status)
{
    $stmt = $conn->prepare('SELECT id, order_ref, name, email FROM orders WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$order) return false;

    $messages = [
        'unlocked' => 'Your access has been activated.',
        'cancelled' => 'The administrator could not approve this request. Contact support if you need assistance.',
        'pending' => 'Your request is pending administrator review.'
    ];
    return queueAutomationEvent($conn, $orderId, 'order.status_changed', [
        'order' => [
            'id' => (int) $order['id'],
            'reference' => $order['order_ref'],
            'status' => $status,
            'status_message' => $messages[$status] ?? '',
            'customer' => ['name' => $order['name'], 'email' => $order['email']]
        ]
    ]);
}

$schemaError = ensureAdminLogSchema($conn);
if ($schemaError) {
    echo json_encode(['success' => false, 'message' => $schemaError]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action   = isset($input['action']) ? $conn->real_escape_string($input['action']) : '';
$order_ref = isset($input['order_ref']) ? $conn->real_escape_string($input['order_ref']) : '';
$db_id    = isset($input['db_id']) ? intval($input['db_id']) : 0;
$item_id  = isset($input['item_id']) ? intval($input['item_id']) : 0;

if (!$action || (!$order_ref && !$db_id && !$item_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing action or order id']);
    exit();
}

// Build WHERE clause for order-level operations
$where = $db_id ? "id = $db_id" : "order_ref = '$order_ref'";

// ===== PER-ITEM ACTIONS =====
if (in_array($action, ['approve_item', 'cancel_item', 'restore_item', 'revert_item']) && $item_id) {
    $statusMap = [
        'approve_item' => 'unlocked',
        'cancel_item' => 'cancelled',
        'restore_item' => 'pending',
        'revert_item' => 'pending'
    ];
    $newStatus = $statusMap[$action];
    $parentResult = $conn->query("SELECT order_id FROM order_items WHERE id = $item_id LIMIT 1");
    $parentOrderId = $parentResult && $parentResult->num_rows ? (int) $parentResult->fetch_assoc()['order_id'] : 0;
    $sql = "UPDATE order_items SET item_status = '$newStatus' WHERE id = $item_id";
    if ($conn->query($sql)) {
        // Update parent order status as well
        if ($newStatus === 'unlocked') {
            $conn->query("UPDATE orders o JOIN order_items oi ON o.id = oi.order_id SET o.status = 'unlocked', o.unlocked_at = NOW() WHERE oi.id = $item_id");
        } else if ($newStatus === 'cancelled') {
            $conn->query("UPDATE orders o JOIN order_items oi ON o.id = oi.order_id SET o.status = 'cancelled', o.cancelled_at = NOW() WHERE oi.id = $item_id");
        } else if ($newStatus === 'pending') {
            $conn->query("UPDATE orders o JOIN order_items oi ON o.id = oi.order_id SET o.status = 'pending' WHERE oi.id = $item_id");
        }
        // Log the action
        $logRef = $order_ref ?: "ITEM-$item_id";
        $custRes = $conn->query("SELECT o.name, o.email, oi.product_type FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE oi.id = $item_id LIMIT 1");
        $cName = '-';
        $cEmail = '-';
        $pType = '-';
        if ($custRes && $custRes->num_rows > 0) {
            $cr = $custRes->fetch_assoc();
            $cName = $conn->real_escape_string($cr['name']);
            $cEmail = $conn->real_escape_string($cr['email']);
            $pType = $conn->real_escape_string($cr['product_type']);
        }
        $logAction = str_replace('_item', '', $action);
        $conn->query("INSERT INTO admin_logs (order_ref, customer_name, customer_email, action, product_type, performed_at) VALUES ('$logRef', '$cName', '$cEmail', '$logAction', '$pType', NOW())");
        if ($parentOrderId) queueOrderStatusNotification($conn, $parentOrderId, $newStatus);
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
    }
    $conn->close();
    exit();
}

// ===== DELETE SINGLE ITEM =====
if ($action === 'delete_item' && $item_id) {
    $logRef = $order_ref ?: "ITEM-$item_id";
    $custRes = $conn->query("SELECT o.name, o.email, oi.product_type FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE oi.id = $item_id LIMIT 1");
    $cName = '-';
    $cEmail = '-';
    $pType = '-';
    if ($custRes && $custRes->num_rows > 0) {
        $cr = $custRes->fetch_assoc();
        $cName = $conn->real_escape_string($cr['name']);
        $cEmail = $conn->real_escape_string($cr['email']);
        $pType = $conn->real_escape_string($cr['product_type']);
    }
    $conn->query("DELETE FROM order_items WHERE id = $item_id");
    $conn->query("INSERT INTO admin_logs (order_ref, customer_name, customer_email, action, product_type, performed_at) VALUES ('$logRef', '$cName', '$cEmail', 'delete', '$pType', NOW())");
    echo json_encode(['success' => true, 'message' => 'Item permanently deleted']);
    $conn->close();
    exit();
}

switch ($action) {

    case 'approve':
        $sql = "UPDATE orders SET status = 'unlocked', unlocked_at = NOW() WHERE $where";
        break;

    case 'cancel':
        $sql = "UPDATE orders SET status = 'cancelled', cancelled_at = NOW() WHERE $where";
        break;

    case 'restore':
        $sql = "UPDATE orders SET status = 'pending', cancelled_at = NULL WHERE $where";
        break;

    case 'revert':
        $sql = "UPDATE orders SET status = 'pending', unlocked_at = NULL WHERE $where";
        break;

    case 'delete':
        // Fetch customer details BEFORE deleting
        if (!$db_id) {
            $res = $conn->query("SELECT id FROM orders WHERE order_ref = '$order_ref' LIMIT 1");
            if ($res && $res->num_rows > 0) {
                $db_id = $res->fetch_assoc()['id'];
            }
        }
        // Get customer info before deletion
        $delCustomerName = '-';
        $delCustomerEmail = '-';
        $delRes = $conn->query("SELECT name, email FROM orders WHERE id = $db_id LIMIT 1");
        if ($delRes && $delRes->num_rows > 0) {
            $delRow = $delRes->fetch_assoc();
            $delCustomerName = $conn->real_escape_string($delRow['name']);
            $delCustomerEmail = $conn->real_escape_string($delRow['email']);
        }
        if ($db_id) {
            $conn->query("DELETE FROM order_items WHERE order_id = $db_id");
            $conn->query("DELETE FROM orders WHERE id = $db_id");
        }
        // Log the delete action AFTER deletion using pre-fetched data
        $conn->query("INSERT INTO admin_logs (order_ref, customer_name, customer_email, action, performed_at) 
                      VALUES ('$order_ref', '$delCustomerName', '$delCustomerEmail', 'delete', NOW())");
        echo json_encode(['success' => true, 'message' => 'Order permanently deleted']);
        $conn->close();
        exit();

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
        $conn->close();
        exit();
}

if ($conn->query($sql)) {
    // Log the action
    $customerRes = $conn->query("SELECT name, email FROM orders WHERE $where LIMIT 1");
    $customerName = '-';
    $customerEmail = '-';
    if ($customerRes && $customerRes->num_rows > 0) {
        $row = $customerRes->fetch_assoc();
        $customerName = $conn->real_escape_string($row['name']);
        $customerEmail = $conn->real_escape_string($row['email']);
    }
    $conn->query("INSERT INTO admin_logs (order_ref, customer_name, customer_email, action, performed_at) 
                      VALUES ('$order_ref', '$customerName', '$customerEmail', '$action', NOW())");

    if (!$db_id) {
        $idResult = $conn->query("SELECT id FROM orders WHERE $where LIMIT 1");
        if ($idResult && $idResult->num_rows) $db_id = (int) $idResult->fetch_assoc()['id'];
    }
    $statusForAction = ['approve' => 'unlocked', 'cancel' => 'cancelled', 'restore' => 'pending', 'revert' => 'pending'];
    if ($db_id && isset($statusForAction[$action])) {
        queueOrderStatusNotification($conn, $db_id, $statusForAction[$action]);
    }

    echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
}
$conn->close();
