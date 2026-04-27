-- setup.sql - Ma'lumotlar bazasi jadvallarini yaratish
-- MySQL/MariaDB

CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `telegram_id`   BIGINT UNIQUE NOT NULL,
    `username`      VARCHAR(64)  DEFAULT NULL,
    `full_name`     VARCHAR(256) DEFAULT NULL,
    `notifications` TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_telegram_id` (`telegram_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sections` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(255) NOT NULL,
    `image_path` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lessons` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `section_id`    INT UNSIGNED NOT NULL,
    `title`         VARCHAR(255) NOT NULL,
    `description`   TEXT         DEFAULT NULL,
    `cover_image`   VARCHAR(255) DEFAULT NULL,
    `telegram_link` VARCHAR(512) DEFAULT NULL,
    `sort_order`    INT UNSIGNED NOT NULL DEFAULT 0,
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_section_active` (`section_id`, `is_active`, `sort_order`),
    FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`       VARCHAR(255) NOT NULL,
    `content`     TEXT         NOT NULL,
    `image_path`  VARCHAR(255) DEFAULT NULL,
    `button_text` VARCHAR(100) DEFAULT NULL,
    `button_url`  VARCHAR(500) DEFAULT NULL,
    `sort_order`  INT UNSIGNED NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_active_created` (`is_active`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mavjud posts jadvaliga ustun qo'shish (agar allaqachon yaratilgan bo'lsa)
ALTER TABLE `posts`
    ADD COLUMN IF NOT EXISTS `button_text` VARCHAR(100) DEFAULT NULL AFTER `image_path`,
    ADD COLUMN IF NOT EXISTS `button_url`  VARCHAR(500) DEFAULT NULL AFTER `button_text`;

CREATE TABLE IF NOT EXISTS `bot_states` (
    `telegram_id` BIGINT PRIMARY KEY,
    `state`       VARCHAR(64)  NOT NULL,
    `data`        TEXT         DEFAULT NULL,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bot_config` (
    `key`        VARCHAR(64)  PRIMARY KEY,
    `value`      TEXT         NOT NULL,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
