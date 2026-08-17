<?php

function ensurePhoneVerificationSchema($conn)
{
    $query = "CREATE TABLE IF NOT EXISTS user_phone_verifications (
        user_id INT UNSIGNED NOT NULL PRIMARY KEY,
        phone VARCHAR(32) NOT NULL,
        code_hash CHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
        send_count TINYINT UNSIGNED NOT NULL DEFAULT 1,
        window_started_at DATETIME NOT NULL,
        last_sent_at DATETIME NOT NULL,
        verified_at DATETIME NULL,
        KEY idx_phone_verification_phone (phone),
        KEY idx_phone_verification_expiry (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    return $conn->query($query) ? null : 'Unable to prepare phone verification storage';
}

