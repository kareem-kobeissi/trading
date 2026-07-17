<?php
// Database initialization script
// This creates the necessary tables for email verification and OAuth

require_once '../config.php';

// SQL to create tables
$createTablesSQL = array(
    // Verification codes table
    "CREATE TABLE IF NOT EXISTS `verification_codes` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `email` VARCHAR(255) NOT NULL,
      `code` VARCHAR(6) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `expires_at` TIMESTAMP NOT NULL,
      `attempted` INT DEFAULT 0,
      INDEX `idx_email` (`email`),
      INDEX `idx_expires` (`expires_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

    // User sessions table
    "CREATE TABLE IF NOT EXISTS `user_sessions` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
);

// Execute table creation
$errors = array();
$success = array();

foreach ($createTablesSQL as $sql) {
    if (!$conn->query($sql)) {
        $errors[] = "Error creating table: " . $conn->error;
    } else {
        $success[] = "Table created successfully";
    }
}

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');

if (empty($errors)) {
    echo json_encode([
        'success' => true,
        'message' => 'Database initialization completed successfully',
        'details' => $success
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Some errors occurred during initialization',
        'errors' => $errors,
        'successes' => $success
    ]);
}
