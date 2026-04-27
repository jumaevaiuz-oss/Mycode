<?php
// upload_helper.php - Telegram'dan fayl yuklash yordamchi funksiyasi

function downloadTelegramFileTo(string $fileId, string $subDir): ?string {
    // Telegram'dan fayl yo'lini olish
    $ch = curl_init(BOT_URL . '/getFile');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => ['file_id' => $fileId],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 30,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);

    if (!$res) return null;

    $data = json_decode($res, true);
    if (!($data['ok'] ?? false) || !isset($data['result']['file_path'])) {
        return null;
    }

    $filePath = $data['result']['file_path'];
    $ext      = strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) ?: 'jpg';
    $fileName = uniqid('img_', true) . '.' . $ext;

    $dir = UPLOAD_DIR . $subDir . '/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Faylni yuklab olish
    $fileUrl = 'https://api.telegram.org/file/bot' . BOT_TOKEN . '/' . $filePath;
    $ch = curl_init($fileUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$content || $httpCode !== 200) {
        return null;
    }

    if (file_put_contents($dir . $fileName, $content) === false) {
        return null;
    }

    return $fileName;
}
