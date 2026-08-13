<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';

unset($_SESSION['is_admin'], $_SESSION['admin_logged_in'], $_SESSION['admin_email']);
session_regenerate_id(true);
echo json_encode(['success' => true]);
