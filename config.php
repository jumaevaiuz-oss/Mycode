<?php
// config.php - Asosiy sozlamalar

define('DB_HOST', 'localhost');
define('DB_NAME', 'hidden');         // O'zgartiring
define('DB_USER', 'hidden');              // O'zgartiring
define('DB_PASS', 'hidden');     // O'zgartiring

define('BOT_TOKEN', 'hidddn'); // BotFather dan olgan token
define('BOT_URL', 'https://api.telegram.org/bot' . BOT_TOKEN);
define('WEBHOOK_SECRET', 'hidden');

define('SITE_URL', 'https://6831eecaafce3.xvest3.ru/avtopilotminiapp'); // Hosting URL
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Admin Telegram ID lari
define('ADMIN_IDS', [
    1234567890, // Admin Telegram ID
]);

// Kanal va aloqa linklari (bot_config jadvalidan o'qiladi)
// Quyidagilar faqat zaxira (fallback) qiymatlar
define('CHANNEL_LINK',    'https://t.me/jumaev_ai');
define('ADMIN_USERNAME',  'aijumaev');
define('DEV_USERNAME',    'developerCC');

// DB ulanish
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die(json_encode(['error' => 'DB ulanmadi']));
        }
    }
    return $pdo;
}

// Uploads papkalarini yaratish
if (!is_dir(UPLOAD_DIR))              mkdir(UPLOAD_DIR, 0755, true);
if (!is_dir(UPLOAD_DIR . 'sections/')) mkdir(UPLOAD_DIR . 'sections/', 0755, true);
if (!is_dir(UPLOAD_DIR . 'lessons/'))  mkdir(UPLOAD_DIR . 'lessons/', 0755, true);
