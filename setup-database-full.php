<?php
// Setup database with automatic database creation
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Trading Routine Database & Tables Setup ===\n\n";

// Credentials 
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'platform';

echo "Step 1: Connecting to MySQL Server...\n";

// First connect without specifying database
$conn_temp = new mysqli($db_host, $db_user, $db_pass);

if ($conn_temp->connect_error) {
    die("❌ Connection failed: " . $conn_temp->connect_error . "\n");
}

echo "✅ Connected to MySQL\n\n";

echo "Step 2: Checking/Creating database...\n";

// Check if database exists
$result = $conn_temp->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");

if ($result && $result->num_rows > 0) {
    echo "✅ Database '$db_name' already exists\n";
} else {
    echo "Creating database '$db_name'...\n";
    if ($conn_temp->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8 COLLATE utf8_general_ci")) {
        echo "✅ Database '$db_name' created\n";
    } else {
        echo "❌ Failed to create database: " . $conn_temp->error . "\n";
        $conn_temp->close();
        exit(1);
    }
}

// Now connect to the database
echo "\nStep 3: Connecting to database '$db_name'...\n";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error . "\n");
}

echo "✅ Connected to database\n\n";
$conn->set_charset("utf8");

echo "Step 4: Creating tables...\n\n";

// Create verification_codes table
echo "Creating verification_codes table...\n";
$sql1 = "CREATE TABLE IF NOT EXISTS `verification_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `code` VARCHAR(6) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  `attempted` INT DEFAULT 0,
  INDEX `idx_email` (`email`),
  INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

if ($conn->query($sql1) === TRUE) {
    echo "✅ verification_codes table ready\n";
} else {
    echo "❌ Error: " . $conn->error . "\n";
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
  `expires_at` DATETIME NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` VARCHAR(255),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_token` (`session_token`),
  INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

if ($conn->query($sql2) === TRUE) {
    echo "✅ user_sessions table ready\n";
} else {
    echo "❌ Error: " . $conn->error . "\n";
}

// Verification
echo "\n" . str_repeat("=", 50) . "\n";
echo "Step 5: Verifying setup...\n\n";

$tables = ['verification_codes', 'user_sessions'];
$all_ok = true;

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "✅ $table\n";

        // Get row count
        $count_result = $conn->query("SELECT COUNT(*) as cnt FROM $table");
        $count_row = $count_result->fetch_assoc();
        echo "   Rows: " . $count_row['cnt'] . "\n";
    } else {
        echo "❌ $table NOT FOUND\n";
        $all_ok = false;
    }
}

echo "\n" . str_repeat("=", 50) . "\n";

if ($all_ok) {
    echo "\n✅ Setup completed successfully!\n";
    echo "\nYour database is ready for:\n";
    echo "  • Email verification codes\n";
    echo "  • OAuth sessions\n";
    echo "  • Remember me tokens\n";
} else {
    echo "\n⚠️ Setup completed with errors. Please review above.\n";
}

$conn->close();
$conn_temp->close();
