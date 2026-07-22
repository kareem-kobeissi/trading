<?php
/** Keep the admin audit table compatible across localhost and Hostinger. */
function ensureAdminLogSchema($conn)
{
    $createSql = "CREATE TABLE IF NOT EXISTS admin_logs (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        order_ref VARCHAR(255) NOT NULL DEFAULT '',
        customer_name VARCHAR(255) NOT NULL DEFAULT '-',
        customer_email VARCHAR(255) NOT NULL DEFAULT '-',
        action VARCHAR(100) NOT NULL,
        product_type VARCHAR(50) NOT NULL DEFAULT '-',
        performed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_admin_logs_email (customer_email),
        INDEX idx_admin_logs_date (performed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if (!$conn->query($createSql)) {
        return 'Unable to create admin_logs: ' . $conn->error;
    }

    $column = $conn->query("SHOW COLUMNS FROM admin_logs LIKE 'product_type'");
    if ($column && $column->num_rows === 0) {
        if (!$conn->query("ALTER TABLE admin_logs ADD COLUMN product_type VARCHAR(50) NOT NULL DEFAULT '-' AFTER action")) {
            return 'Unable to update admin_logs: ' . $conn->error;
        }
    }

    return null;
}

