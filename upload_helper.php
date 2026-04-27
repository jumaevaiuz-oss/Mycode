<?php
// upload_helper.php - Rasm yuklash yordamchi

require_once 'config.php';

/**
 * Telegram faylini to'g'ri papkaga yuklaydi
 * @param string $fileId  - Telegram file_id
 * @param string $type    - 'sections' yoki 'lessons'
 * @return string|null    - Fayl nomi yoki null
 */
function downloadTelegramFileTo(string $fileId, string $type): ?string {
    // Fayl yo'lini olish
    $ch = curl_init(BOT_URL . '/getFile');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ['file_id' => $fileId],
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (!$data['ok']) return null;

    $tgPath = $data['result']['file_path'];
    $ext = strtolower(pathinfo($tgPath, PATHINFO_EXTENSION));

    // Faqat rasm formatlari
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
        $ext = 'jpg';
    }

    $fileName = uniqid('img_', true) . '.' . $ext;
    $folder = UPLOAD_DIR . $type . '/';

    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    $localPath = $folder . $fileName;
    $fileUrl = "https://api.telegram.org/file/bot" . BOT_TOKEN . "/{$tgPath}";

    // Faylni yuklab olish
    $ch2 = curl_init($fileUrl);
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
    ]);
    $content = curl_exec($ch2);
    curl_close($ch2);

    if (!$content) return null;

    file_put_contents($localPath, $content);
    return $fileName;
}
