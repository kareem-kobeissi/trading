<?php

/** Create the Phase 1 automation tables without changing existing orders. */
function ensureAutomationSchema($conn)
{
    $queries = [
        "CREATE TABLE IF NOT EXISTS automation_events (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            event_uuid CHAR(36) NOT NULL,
            order_id INT UNSIGNED NOT NULL,
            event_type VARCHAR(80) NOT NULL,
            payload_json LONGTEXT NOT NULL,
            delivery_status ENUM('queued','processing','delivered','failed') NOT NULL DEFAULT 'queued',
            attempts INT UNSIGNED NOT NULL DEFAULT 0,
            next_attempt_at DATETIME NULL,
            delivered_at DATETIME NULL,
            response_code SMALLINT UNSIGNED NULL,
            last_error TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_automation_event_uuid (event_uuid),
            KEY idx_automation_delivery (delivery_status, next_attempt_at),
            KEY idx_automation_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS customer_conversations (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            order_id INT UNSIGNED NOT NULL,
            channel ENUM('email','whatsapp','system') NOT NULL,
            direction ENUM('incoming','outgoing','internal') NOT NULL,
            sender_address VARCHAR(255) NOT NULL DEFAULT '',
            recipient_address VARCHAR(255) NOT NULL DEFAULT '',
            original_message TEXT NOT NULL,
            translated_message TEXT NULL,
            ai_summary TEXT NULL,
            attachment_url TEXT NULL,
            external_message_id VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_conversation_external (channel, external_message_id),
            KEY idx_conversation_order (order_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS automation_reviews (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            order_id INT UNSIGNED NOT NULL,
            contact_status ENUM('not_contacted','contacted','waiting_for_proof','proof_received','needs_admin_review') NOT NULL DEFAULT 'not_contacted',
            recommended_status ENUM('pending','likely_valid','likely_invalid','needs_review') NOT NULL DEFAULT 'pending',
            confidence DECIMAL(5,2) NULL,
            reason TEXT NULL,
            last_customer_message_at DATETIME NULL,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_automation_review_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];

    foreach ($queries as $query) {
        if (!$conn->query($query)) {
            return 'Unable to prepare automation storage: ' . $conn->error;
        }
    }

    return null;
}
