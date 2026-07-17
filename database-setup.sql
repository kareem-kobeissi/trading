-- Create verification_codes table
CREATE TABLE IF NOT EXISTS `verification_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `code` VARCHAR(6) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  `attempted` INT DEFAULT 0,
  INDEX `idx_email` (`email`),
  INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Optional: Create a sessions table for OAuth and remember-me tokens
CREATE TABLE IF NOT EXISTS `user_sessions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
