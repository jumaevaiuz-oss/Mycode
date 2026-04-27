<?php
// upload_helper.php - Rasm yuklash yordamchi

function downloadTelegramFileTo(string $fileId, string $type): ?string {
    // Telegram'dan fayl yo'lini olish
    $ch = curl_init(BOT_URL . '/getFile');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => ['file_id' => $fileId],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return null;

    $data = json_decode($response, true);
    if (empty($data['ok']) || empty($data['result']['file_path'])) return null;

    $tgPath = $data['result']['file_path'];
    $ext    = strtolower(pathinfo($tgPath, PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
        $ext = 'jpg';
    }

    $fileName  = uniqid('img_', true) . '.' . $ext;
    $folder    = UPLOAD_DIR . $type . '/';

    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    // Faylni yuklab olish
    $fileUrl = 'https://api.telegram.org/file/bot' . BOT_TOKEN . '/' . $tgPath;
    $ch2 = curl_init($fileUrl);
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 60,
    ]);
    $content  = curl_exec($ch2);
    $httpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);

    if (!$content || $httpCode !== 200) return null;

    if (file_put_contents($folder . $fileName, $content) === false) return null;

    return $fileName;
}
