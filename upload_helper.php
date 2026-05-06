<?php
// upload_helper.php - Rasm yuklash yordamchi

define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Rasm magic bytes (fayl imzolari)
const IMAGE_SIGNATURES = [
    'jpg'  => ["\xFF\xD8\xFF"],
    'jpeg' => ["\xFF\xD8\xFF"],
    'png'  => ["\x89PNG\r\n\x1a\n"],
    'gif'  => ["GIF87a", "GIF89a"],
    'webp' => ["RIFF"],
];

function downloadTelegramFileTo(string $fileId, string $type): ?string {
    $ch = curl_init(BOT_URL . '/getFile');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => ['file_id' => $fileId],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return null;

    $data = json_decode($response, true);
    if (empty($data['ok']) || empty($data['result']['file_path'])) return null;

    $tgPath = $data['result']['file_path'];
    $ext    = strtolower(pathinfo($tgPath, PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
        return null; // Noto'g'ri format — rad etiladi
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
    $folder   = UPLOAD_DIR . $type . '/';
    @mkdir($folder, 0755, true);

    $fileUrl = 'https://api.telegram.org/file/bot' . BOT_TOKEN . '/' . $tgPath;
    $ch2 = curl_init($fileUrl);
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_MAXFILESIZE    => MAX_FILE_SIZE,
    ]);
    $content  = curl_exec($ch2);
    $httpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);

    if (!$content || $httpCode !== 200) return null;

    // Fayl hajmini tekshirish
    if (strlen($content) > MAX_FILE_SIZE) {
        error_log("upload_helper: fayl hajmi limitdan oshdi ($fileId)");
        return null;
    }

    // Magic bytes tekshiruvi — fayl ichidagi haqiqiy tur
    if (!validateImageSignature($content, $ext)) {
        error_log("upload_helper: fayl imzosi mos kelmadi ($fileId, $ext)");
        return null;
    }

    if (file_put_contents($folder . $fileName, $content) === false) return null;

    return $fileName;
}

function validateImageSignature(string $content, string $ext): bool {
    $signatures = IMAGE_SIGNATURES[$ext] ?? [];
    foreach ($signatures as $sig) {
        if (str_starts_with($content, $sig)) return true;
    }
    return false;
}
