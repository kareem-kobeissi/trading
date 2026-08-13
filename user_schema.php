<?php
function ensureUserPhoneColumn($conn)
{
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
    if (!$result) return 'Unable to inspect users table: ' . $conn->error;
    if ($result->num_rows === 0 && !$conn->query("ALTER TABLE users ADD COLUMN phone VARCHAR(32) NOT NULL DEFAULT '' AFTER email")) {
        return 'Unable to add users.phone: ' . $conn->error;
    }
    return null;
}
