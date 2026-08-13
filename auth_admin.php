<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed']);
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$adminEmail = trim((string) (getenv('ADMIN_EMAIL') ?: ''));
$adminPasswordHash = trim((string) (getenv('ADMIN_PASSWORD_HASH') ?: ''));
$adminPassword = (string) (getenv('ADMIN_PASSWORD') ?: '');

$passwordValid = $adminPasswordHash !== ''
    ? password_verify($password, $adminPasswordHash)
    : ($adminPassword !== '' && hash_equals($adminPassword, $password));

if ($adminEmail === '' || !hash_equals(strtolower($adminEmail), strtolower($email)) || !$passwordValid) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid administrator credentials']);
    exit;
}

session_regenerate_id(true);
$_SESSION['is_admin'] = true;
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_email'] = $adminEmail;

ob_end_clean();
echo json_encode(['success' => true, 'email' => $adminEmail]);
