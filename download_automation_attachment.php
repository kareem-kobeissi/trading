<?php
require_once __DIR__ . '/config.php';

if (empty($_SESSION['is_admin']) && empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit('Administrator authentication required');
}

$relativePath = str_replace('\\', '/', trim((string) ($_GET['path'] ?? '')));
if (!preg_match('#^uploads/automation/(\d+)/([a-f0-9]{32})\.(jpg|png|webp|pdf)$#i', $relativePath, $matches)) {
    http_response_code(400);
    exit('Invalid attachment path');
}

$orderId = (int) $matches[1];
$stmt = $conn->prepare(
    'SELECT id FROM customer_conversations
     WHERE order_id = ? AND attachment_url LIKE ? LIMIT 1'
);
$likePath = '%' . $relativePath . '%';
$stmt->bind_param('is', $orderId, $likePath);
$stmt->execute();
$authorized = $stmt->get_result()->num_rows === 1;
$stmt->close();
if (!$authorized) {
    http_response_code(404);
    exit('Attachment not found');
}

$base = realpath(__DIR__ . '/uploads/automation');
$file = realpath(__DIR__ . '/' . $relativePath);
if (!$base || !$file || !str_starts_with($file, $base . DIRECTORY_SEPARATOR) || !is_file($file)) {
    http_response_code(404);
    exit('Attachment not found');
}

$mimeTypes = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'pdf' => 'application/pdf'];
$extension = strtolower($matches[3]);
header('Content-Type: ' . $mimeTypes[$extension]);
header('Content-Length: ' . filesize($file));
header('Content-Disposition: inline; filename="proof.' . $extension . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
readfile($file);
