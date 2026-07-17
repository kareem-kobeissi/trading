<?php
header('Content-Type: application/json');
require 'config.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = $_POST['course_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    $userId = $_SESSION['user_id'];

    if (empty($courseId)) {
        echo json_encode(['success' => false, 'message' => 'Course ID is required']);
        exit;
    }

    // Check if course exists
    $stmt = $conn->prepare("SELECT id FROM courses WHERE id = ?");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit;
    }

    // Check if course already in cart
    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $userId, $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND course_id = ?");
        $stmt->bind_param("iii", $quantity, $userId, $courseId);
    } else {
        // Add new item to cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, course_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $userId, $courseId, $quantity);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Course added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding to cart']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
