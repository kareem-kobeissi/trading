<?php
header('Content-Type: application/json');
require 'config.php';

$sql = "SELECT id, title, description, price, image, level, duration, instructor FROM courses";
$result = $conn->query($sql);

$courses = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

echo json_encode($courses);

$conn->close();
