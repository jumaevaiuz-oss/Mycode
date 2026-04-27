<?php
// setup_webhook.php - Webhookni o'rnatish
// MUHIM: Ishlatib bo'lgach bu faylni o'chirib tashlang!

require_once 'config.php';

// Xavfsizlik: faqat to'g'ri kalit bilan kirish mumkin
$key = $_GET['key'] ?? '';
if ($key !== WEBHOOK_SECRET || empty(WEBHOOK_SECRET)) {
    http_response_code(403);
    die('<b>Xato:</b> Ruxsat yo\'q. ?key=WEBHOOK_SECRET parametrini qo\'shing.');
}

$webhookUrl = SITE_URL . '/bot.php';

$ch = curl_init(BOT_URL . '/setWebhook');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => [
        'url'          => $webhookUrl,
        'secret_token' => WEBHOOK_SECRET,
        'max_connections' => 40,
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

echo '<pre style="font-family:monospace;background:#111;color:#0f0;padding:20px">';
echo 'Webhook URL: ' . $webhookUrl . "\n\n";

if (!empty($result['ok'])) {
    echo "✅ Webhook muvaffaqiyatli o'rnatildi!\n";
    echo "⚠️  Xavfsizlik uchun bu faylni o'chirib tashlang: setup_webhook.php\n";
} else {
    echo '❌ Xato: ' . ($result['description'] ?? 'Noma\'lum xato') . "\n";
}

echo "\n--- Webhook info ---\n";
$ch2 = curl_init(BOT_URL . '/getWebhookInfo');
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$info = curl_exec($ch2);
curl_close($ch2);
echo $info;
echo '</pre>';
