<?php
// config.php - Bot konfiguratsiyasi
// MUHIM: Bu faylni serverga yuklaganingizdan keyin quyidagi qiymatlarni to'ldiring!

// ============================================================
//  BOT SOZLAMALARI
// ============================================================
define('BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE');           // @BotFather dan olingan token
define('BOT_URL',   'https://api.telegram.org/bot' . BOT_TOKEN);
define('SITE_URL',  'https://your-domain.com');        // Saytingiz manzili (https bilan)

// ============================================================
//  ADMIN SOZLAMALARI
// ============================================================
define('ADMIN_IDS',       [123456789]);                // Admin Telegram ID raqamlar (massiv)
define('CHANNEL_LINK',    'https://t.me/your_channel'); // Rasmiy kanal linki
define('ADMIN_USERNAME',  'your_admin_username');       // Admin username (@ belgisisiz)
define('DEV_USERNAME',    'your_dev_username');          // Dasturchi username (@ belgisisiz)

// ============================================================
//  FAYL YUKLASH
// ============================================================
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// ============================================================
//  MA'LUMOTLAR BAZASI
// ============================================================
define('DB_HOST',    'localhost');
define('DB_NAME',    'your_database_name');
define('DB_USER',    'your_database_user');
define('DB_PASS',    'your_database_password');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
