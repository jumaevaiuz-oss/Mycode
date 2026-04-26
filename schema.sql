-- AvtoPilot Mini App - Database Schema
-- PHP 8.2 + MySQL

CREATE DATABASE IF NOT EXISTS avtopilot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE avtopilot;

-- Bo'limlar jadvali
CREATE TABLE IF NOT EXISTS sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_path VARCHAR(500) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active_order (is_active, sort_order)
);

-- Darsliklar jadvali
CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    cover_image VARCHAR(500) DEFAULT NULL,
    telegram_link VARCHAR(500) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_section_active (section_id, is_active, sort_order),
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
);

-- Foydalanuvchilar jadvali
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telegram_id BIGINT NOT NULL UNIQUE,
    username VARCHAR(100) DEFAULT NULL,
    full_name VARCHAR(255) DEFAULT NULL,
    notifications TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bot sozlamalari (kanal linki, admin/dasturchi username)
CREATE TABLE IF NOT EXISTS bot_config (
    `key` VARCHAR(100) PRIMARY KEY,
    value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin foydalanuvchilar
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telegram_id BIGINT NOT NULL UNIQUE,
    username VARCHAR(100) DEFAULT NULL,
    full_name VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bot holati (conversation state)
CREATE TABLE IF NOT EXISTS bot_states (
    telegram_id BIGINT PRIMARY KEY,
    state VARCHAR(100) DEFAULT NULL,
    data JSON DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Boshlang'ich bo'limlar
INSERT INTO sections (name, sort_order) VALUES
('Shogirdlik kursi', 1),
('Tafakkur darslari', 2),
('Biznes g''oyalar', 3),
('Kunlik insaydlar', 4),
('Jonli efirlar', 5),
('Avtopilot sotuv', 6),
('Avtopilot marketing', 7),
('Avtopilot tizim', 8),
('Avtopilot miya', 9),
('Natija va fikr', 10);
