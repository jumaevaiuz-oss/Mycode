<?php
// config.php - Asosiy sozlamalar

// Xatolarni log qilish
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/bot_error.log');

define('DB_HOST', 'localhost');
define('DB_NAME', '');         // O'zgartiring
define('DB_USER', '');         // O'zgartiring
define('DB_PASS', '');         // O'zgartiring

define('BOT_TOKEN', '');       // BotFather dan olgan token
define('BOT_URL', 'https://api.telegram.org/bot' . BOT_TOKEN);
define('WEBHOOK_SECRET', '');  // Xavfsiz tasodifiy so'z (masalan: openssl rand -hex 32)

define('SITE_URL', 'https://6831eecaafce3.xvest3.ru/avtopilotminiapp');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Admin Telegram ID lari
define('ADMIN_IDS', [
    0, // Admin Telegram ID (raqam bilan almashtiring)
]);

// Fallback qiymatlar (bot_config jadvalidan o'qiladi dinamik)
define('CHANNEL_LINK',   'https://t.me/jumaev_ai');
define('ADMIN_USERNAME', 'aijumaev');
define('DEV_USERNAME',   'developerCC');

// DB ulanish
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            error_log('DB ulanish xatosi: ' . $e->getMessage());
            http_response_code(500);
            die(json_encode(['error' => 'DB ulanmadi']));
        }
    }
    return $pdo;
}

// Uploads papkalarini yaratish
foreach (['', 'sections/', 'lessons/', 'posts/', 'banner/'] as $sub) {
    $dir = UPLOAD_DIR . $sub;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}
