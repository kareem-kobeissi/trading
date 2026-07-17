<?php
// Database Setup Script - Uses config.php credentials
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Trading Routine Database Setup ===\n\n";

// Load config
require_once 'config.php';

echo "Attempting to connect to database...\n";
echo "Host: " . DB_HOST . "\n";
echo "User: " . DB_USER . "\n";
echo "Database: " . DB_NAME . "\n\n";

try {
    // Try to connect
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }

    echo "✅ Connected successfully!\n\n";

    // Set character set
    $conn->set_charset("utf8");

    // Create verification_codes table
    echo "Creating verification_codes table...\n";
    $sql1 = "CREATE TABLE IF NOT EXISTS `verification_codes` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `email` VARCHAR(255) NOT NULL,
      `code` VARCHAR(6) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `expires_at` TIMESTAMP NOT NULL,
      `attempted` INT DEFAULT 0,
      INDEX `idx_email` (`email`),
      INDEX `idx_expires` (`expires_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

    if ($conn->query($sql1) === TRUE) {
        echo "✅ verification_codes table created successfully\n";
    } else {
        echo "❌ Error creating verification_codes table: " . $conn->error . "\n";
    }

    // Create user_sessions table
    echo "\nCreating user_sessions table...\n";
    $sql2 = "CREATE TABLE IF NOT EXISTS `user_sessions` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` INT,
      `session_token` VARCHAR(255) UNIQUE NOT NULL,
      `oauth_provider` VARCHAR(50),
      `oauth_id` VARCHAR(255),
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `expires_at` TIMESTAMP NOT NULL,
      `ip_address` VARCHAR(45),
      `user_agent` VARCHAR(255),
      INDEX `idx_user_id` (`user_id`),
      INDEX `idx_token` (`session_token`),
      INDEX `idx_expires` (`expires_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

    if ($conn->query($sql2) === TRUE) {
        echo "✅ user_sessions table created successfully\n";
    } else {
        echo "❌ Error creating user_sessions table: " . $conn->error . "\n";
    }

    // Verify tables exist
    echo "\n=== Verification ===\n";

    $tables = ['verification_codes', 'user_sessions'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✅ $table exists\n";

            // Show table structure
            $columns = $conn->query("SHOW COLUMNS FROM $table");
            echo "   Columns: ";
            $col_names = [];
            while ($col = $columns->fetch_assoc()) {
                $col_names[] = $col['Field'];
            }
            echo implode(", ", $col_names) . "\n";
        } else {
            echo "❌ $table not found\n";
        }
    }

    echo "\n✅ Database setup completed successfully!\n";
    $conn->close();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
