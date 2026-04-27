<?php
require_once 'config.php';

$webhookUrl = SITE_URL . '/bot.php';

// cURL bilan yuborish
$ch = curl_init(BOT_URL . '/setWebhook');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'url' => $webhookUrl,
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

echo "<pre>";
echo "Webhook URL: " . $webhookUrl . "\n\n";

if ($result['ok']) {
    echo "✅ Webhook muvaffaqiyatli o'rnatildi!\n";
} else {
    echo "❌ Xato: " . ($result['description'] ?? 'Noma\'lum xato') . "\n";
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
echo "</pre>";
