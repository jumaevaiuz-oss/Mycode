<?php
// bot.php - Avtopilot Bot (To'liq versiya)

// PHP 7.x uchun polyfill (str_starts_with PHP 8.0 da qo'shilgan)
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

// Timeout va memory sozlamalari
set_time_limit(0);
ini_set('memory_limit', '256M');
ignore_user_abort(true);

require_once 'config.php';
require_once 'upload_helper.php';

$input = file_get_contents('php://input');
$update = json_decode($input, true);
if (!$update) exit;

// Telegramga darhol 200 OK qaytaramiz (timeout oldini olish)
http_response_code(200);
header('Content-Type: application/json');
header('Connection: close');
header('Content-Length: ' . ob_get_length());
echo json_encode(['ok' => true]);

// PHP-FPM uchun to'g'ri ulanishni yopish
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    if (ob_get_level()) ob_end_flush();
    flush();
}

// Keyin botni ishlatamiz
$message  = $update['message'] ?? null;
$callback = $update['callback_query'] ?? null;

if ($message)  handleMessage($message);
elseif ($callback) handleCallback($callback);

// ============================================================
//  MESSAGE HANDLER
// ============================================================
function handleMessage(array $msg): void {
    $chatId = $msg['chat']['id'];
    $userId = $msg['from']['id'];
    $text   = $msg['text'] ?? '';
    $photo  = $msg['photo'] ?? null;
    $doc    = $msg['document'] ?? null;

    // Foydalanuvchini bazaga saqlash
    saveUser($msg['from']);

    $state = getState($userId);

    // Rasm keldi
    if ($photo || $doc) {
        if (isAdmin($userId)) handleImageUpload($msg, $state, $chatId, $userId);
        return;
    }

    // /start
    if ($text === '/start') {
        clearState($userId);
        if (isAdmin($userId)) sendAdminMenu($chatId);
        else sendUserWelcome($chatId, $msg['from']);
        return;
    }

    // /admin — admin menyu
    if ($text === '/admin' && isAdmin($userId)) {
        clearState($userId);
        sendAdminMenu($chatId);
        return;
    }

    // Tugma turini aniqlash (emoji farqi muammosini hal qilish)
    $btnType = detectButton($text);

    // Admin state da bo'lsa va user tugmasi emas
    if (isAdmin($userId) && $state && $btnType === null) {
        handleAdminState($msg, $state, $chatId, $userId, $text);
        return;
    }

    // Foydalanuvchi tugmalari
    if ($btnType !== null) {
        handleUserButton($chatId, $userId, $btnType);
        return;
    }

    // Admin uchun admin menyu
    if (isAdmin($userId)) {
        sendAdminMenu($chatId);
        return;
    }

    // Oddiy foydalanuvchi welcome
    sendUserWelcome($chatId, $msg['from']);
}

function detectButton(string $text): ?string {
    $map = [
        'avtopilot'      => '🚀 Avtopilot',
        'kanal'          => '📢 Kanal',
        'aloqa'          => '📞 Aloqa',
        'bot haqida'     => 'ℹ️ Bot haqida',
        'bildirishnoma'  => '🔔 Bildirishnoma',
        'dasturchi'      => '🧑‍💻 Dasturchi',
    ];
    $lower = mb_strtolower($text);
    foreach ($map as $key => $val) {
        if (mb_strpos($lower, $key) !== false) return $val;
    }
    return null;
}

// ============================================================
//  FOYDALANUVCHI QABUL QILISH
// ============================================================
function sendUserWelcome(int $chatId, array $from): void {
    $name = $from['first_name'] ?? 'Do\'stim';

    $text = "👋 *Assalomu alaykum, {$name}!*\n\n";
    $text .= "🚀 *AvtoPilot* — biznes va shaxsiy rivojlanish bo'yicha eng yaxshi darsliklar platformasi!\n\n";
    $text .= "📚 Bu yerda siz topasiz:\n";
    $text .= "• Shogirdlik kurslari\n";
    $text .= "• Biznes g'oyalar\n";
    $text .= "• Avtopilot tizimlari\n";
    $text .= "• Va yana ko'p narsalar!\n\n";
    $text .= "👇 Quyidagi tugmalardan foydalaning:";

    $keyboard = [
        'keyboard' => [
            [
                ['text' => '🚀 Avtopilot'],
                ['text' => '📢 Kanal'],
            ],
            [
                ['text' => '📞 Aloqa'],
                ['text' => 'ℹ️ Bot haqida'],
            ],
            [
                ['text' => '🔔 Bildirishnoma'],
                ['text' => '🧑‍💻 Dasturchi'],
            ],
        ],
        'resize_keyboard' => true,
        'persistent' => true,
    ];

    sendMessage($chatId, $text, $keyboard, 'keyboard');
}

// Foydalanuvchi tugmalariga javob
function handleUserButton(int $chatId, int $userId, string $text): void {
    switch ($text) {

        case '🚀 Avtopilot':
            $miniAppUrl = SITE_URL . '/index.html';
            $keyboard = [
                'inline_keyboard' => [[
                    ['text' => '🚀 Avtopilotni ochish', 'web_app' => ['url' => $miniAppUrl]]
                ]]
            ];
            sendMessage($chatId, "👇 Quyidagi tugmani bosib Mini Appni oching:", $keyboard);
            break;

        case '📢 Kanal':
            $keyboard = [
                'inline_keyboard' => [[
                    ['text' => '📢 Kanalga o\'tish', 'url' => CHANNEL_LINK]
                ]]
            ];
            sendMessage($chatId, "📢 Bizning rasmiy kanalimiz:", $keyboard);
            break;

        case '📞 Aloqa':
            $keyboard = [
                'inline_keyboard' => [[
                    ['text' => '✍️ Admin bilan bog\'lanish', 'url' => 'https://t.me/' . ADMIN_USERNAME]
                ]]
            ];
            sendMessage($chatId, "📞 Admin bilan bog'lanish uchun:", $keyboard);
            break;

        case 'ℹ️ Bot haqida':
            $text2  = "ℹ️ *AvtoPilot Bot haqida*\n\n";
            $text2 .= "🤖 Bu bot AvtoPilot platformasining rasmiy boti.\n\n";
            $text2 .= "📚 Platforma orqali siz:\n";
            $text2 .= "• 10+ bo'lim bo'yicha darsliklar ko'rishingiz\n";
            $text2 .= "• Yangi darsliklar haqida bildirishnoma olishingiz\n";
            $text2 .= "• Biznes va shaxsiy o'sish bo'yicha bilim olishingiz mumkin\n\n";
            $text2 .= "🔄 Versiya: 1.0\n";
            $text2 .= "👨‍💻 Ishlab chiquvchi: @" . ADMIN_USERNAME;
            sendMessage($chatId, $text2);
            break;

        case '🔔 Bildirishnoma':
            $db   = getDB();
            $stmt = $db->prepare("SELECT notifications FROM users WHERE telegram_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $current = $user['notifications'] ?? 1;

            $status = $current ? "✅ *Yoqilgan*" : "❌ *O'chirilgan*";
            $btnText = $current ? "🔕 O'chirish" : "🔔 Yoqish";
            $action  = $current ? "notif_off" : "notif_on";

            $keyboard = [
                'inline_keyboard' => [[
                    ['text' => $btnText, 'callback_data' => $action]
                ]]
            ];
            sendMessage($chatId, "🔔 *Bildirishnoma holati:* {$status}\n\nYangi darsliklar va bo'limlar qo'shilganda xabar olasiz.", $keyboard);
            break;

        case '🧑‍💻 Dasturchi':
            $keyboard = [
                'inline_keyboard' => [[
                    ['text' => '🧑‍💻 Dasturchi bilan bog\'lanish', 'url' => 'https://t.me/' . DEV_USERNAME]
                ]]
            ];
            sendMessage($chatId, "🧑‍💻 Bot dasturchisi bilan bog'lanish:", $keyboard);
            break;
    }
}

// ============================================================
//  CALLBACK HANDLER
// ============================================================
function handleCallback(array $cb): void {
    $chatId  = $cb['message']['chat']['id'];
    $userId  = $cb['from']['id'];
    $msgId   = $cb['message']['message_id'];
    $data    = $cb['data'];

    answerCallback($cb['id']);

    // Bildirishnoma yoqish/o'chirish (foydalanuvchi)
    if ($data === 'notif_on' || $data === 'notif_off') {
        $val  = $data === 'notif_on' ? 1 : 0;
        $db   = getDB();
        $stmt = $db->prepare("UPDATE users SET notifications = ? WHERE telegram_id = ?");
        $stmt->execute([$val, $userId]);

        $status  = $val ? "✅ *Yoqilgan*" : "❌ *O'chirilgan*";
        $btnText = $val ? "🔕 O'chirish" : "🔔 Yoqish";
        $action  = $val ? "notif_off" : "notif_on";

        $keyboard = ['inline_keyboard' => [[['text' => $btnText, 'callback_data' => $action]]]];
        editMessage($chatId, $msgId, "🔔 *Bildirishnoma holati:* {$status}\n\nYangi darsliklar va bo'limlar qo'shilganda xabar olasiz.", $keyboard);
        return;
    }

    // Admin emas — qaytarish
    if (!isAdmin($userId)) return;

    $state = getState($userId);

    // ---- ASOSIY MENYU ----
    if ($data === 'back_main') { clearState($userId); editToAdminMenu($chatId, $msgId); return; }
    if ($data === 'cancel')    { clearState($userId); editToAdminMenu($chatId, $msgId); return; }

    // ---- BO'LIMLAR ----
    if ($data === 'sections_menu') { editSectionsList($chatId, $msgId); return; }
    if ($data === 'add_section')   {
        setState($userId, 'waiting_section_name', []);
        editMessage($chatId, $msgId, "📝 Yangi bo'lim nomini kiriting:\n\n_Bekor qilish uchun /cancel_");
        return;
    }

    if (str_starts_with($data, 'edit_section_')) {
        $id = (int)str_replace('edit_section_', '', $data);
        editSectionMenu($chatId, $msgId, $id);
        return;
    }
    if (str_starts_with($data, 'rename_section_')) {
        $id = (int)str_replace('rename_section_', '', $data);
        setState($userId, 'waiting_rename_section', ['section_id' => $id]);
        editMessage($chatId, $msgId, "✏️ Yangi nom kiriting:\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if (str_starts_with($data, 'photo_section_')) {
        $id = (int)str_replace('photo_section_', '', $data);
        setState($userId, 'waiting_section_photo', ['section_id' => $id]);
        editMessage($chatId, $msgId, "🖼 Bo'lim rasmini yuboring:\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if (str_starts_with($data, 'delete_section_')) {
        $id = (int)str_replace('delete_section_', '', $data);
        $kb = ['inline_keyboard' => [[
            ['text' => '✅ Ha, o\'chir', 'callback_data' => "confirm_del_sec_{$id}"],
            ['text' => '❌ Yo\'q', 'callback_data' => 'sections_menu'],
        ]]];
        editMessage($chatId, $msgId, "⚠️ Bo'limni o'chirmoqchimisiz?\nBarcha darsliklari ham o'chadi!", $kb);
        return;
    }
    if (str_starts_with($data, 'confirm_del_sec_')) {
        $id = (int)str_replace('confirm_del_sec_', '', $data);
        $db = getDB();
        $db->prepare("UPDATE sections SET is_active=0 WHERE id=?")->execute([$id]);
        editMessage($chatId, $msgId, "✅ Bo'lim o'chirildi.");
        sleep(1);
        editSectionsList($chatId, $msgId);
        return;
    }

    // ---- DARSLIKLAR ----
    if ($data === 'lessons_menu') { editSectionsForLessons($chatId, $msgId); return; }

    if (str_starts_with($data, 'section_lessons_')) {
        $id = (int)str_replace('section_lessons_', '', $data);
        editLessonsList($chatId, $msgId, $id);
        return;
    }
    if (str_starts_with($data, 'add_lesson_')) {
        $id = (int)str_replace('add_lesson_', '', $data);
        setState($userId, 'waiting_lesson_title', ['section_id' => $id]);
        editMessage($chatId, $msgId, "📝 Darslik nomini kiriting:\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if (str_starts_with($data, 'edit_lesson_')) {
        $id = (int)str_replace('edit_lesson_', '', $data);
        editLessonMenu($chatId, $msgId, $id);
        return;
    }
    if (str_starts_with($data, 'rename_lesson_')) {
        $id = (int)str_replace('rename_lesson_', '', $data);
        setState($userId, 'waiting_rename_lesson', ['lesson_id' => $id]);
        editMessage($chatId, $msgId, "✏️ Yangi nom kiriting:\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if (str_starts_with($data, 'desc_lesson_')) {
        $id = (int)str_replace('desc_lesson_', '', $data);
        setState($userId, 'waiting_lesson_desc', ['lesson_id' => $id]);
        editMessage($chatId, $msgId, "📄 Yangi tavsif kiriting:\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if (str_starts_with($data, 'photo_lesson_')) {
        $id = (int)str_replace('photo_lesson_', '', $data);
        setState($userId, 'waiting_lesson_photo', ['lesson_id' => $id]);
        editMessage($chatId, $msgId, "🖼 Darslik obložka rasmini yuboring:\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if (str_starts_with($data, 'link_lesson_')) {
        $id = (int)str_replace('link_lesson_', '', $data);
        setState($userId, 'waiting_lesson_link', ['lesson_id' => $id]);
        editMessage($chatId, $msgId, "🔗 Telegram kanal linkini kiriting:\nMasalan: https://t.me/kanal/123\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if (str_starts_with($data, 'delete_lesson_')) {
        $id = (int)str_replace('delete_lesson_', '', $data);
        $kb = ['inline_keyboard' => [[
            ['text' => '✅ Ha, o\'chir', 'callback_data' => "confirm_del_les_{$id}"],
            ['text' => '❌ Yo\'q', 'callback_data' => 'lessons_menu'],
        ]]];
        editMessage($chatId, $msgId, "⚠️ Darslikni o'chirmoqchimisiz?", $kb);
        return;
    }
    if (str_starts_with($data, 'confirm_del_les_')) {
        $id = (int)str_replace('confirm_del_les_', '', $data);
        $db = getDB();
        $db->prepare("UPDATE lessons SET is_active=0 WHERE id=?")->execute([$id]);
        editMessage($chatId, $msgId, "✅ Darslik o'chirildi.");
        sleep(1);
        editSectionsForLessons($chatId, $msgId);
        return;
    }

    // ---- STATISTIKA ----
    if ($data === 'stats') { editStats($chatId, $msgId); return; }

    // ---- XABAR YUBORISH ----
    if ($data === 'broadcast') {
        setState($userId, 'waiting_broadcast', []);
        editMessage($chatId, $msgId, "📣 Barcha foydalanuvchilarga yuboriladigan xabarni yozing:\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if ($data === 'confirm_broadcast') {
        $bState = getState($userId);
        if ($bState && $bState['state'] === 'broadcast_confirm' && isset($bState['data']['text'])) {
            clearState($userId);
            doBroadcast($chatId, $msgId, $bState['data']['text']);
        }
        return;
    }
    if ($data === 'cancel_broadcast') {
        clearState($userId);
        editToAdminMenu($chatId, $msgId);
        return;
    }

    // ---- POSTLAR ----
    if ($data === 'posts_menu') { editPostsList($chatId, $msgId); return; }
    if ($data === 'add_post') {
        setState($userId, 'waiting_post_title', []);
        editMessage($chatId, $msgId, "📝 Post sarlavhasini kiriting:

_Bekor: /cancel_");
        return;
    }
    if (str_starts_with($data, 'edit_post_')) {
        $id = (int)str_replace('edit_post_', '', $data);
        editPostMenu($chatId, $msgId, $id);
        return;
    }
    if (str_starts_with($data, 'rename_post_')) {
        $id = (int)str_replace('rename_post_', '', $data);
        setState($userId, 'waiting_rename_post', ['post_id' => $id]);
        editMessage($chatId, $msgId, "✏️ Yangi sarlavha:

_/cancel_");
        return;
    }
    if (str_starts_with($data, 'content_post_')) {
        $id = (int)str_replace('content_post_', '', $data);
        setState($userId, 'waiting_post_content', ['post_id' => $id]);
        editMessage($chatId, $msgId, "📄 Yangi matn:

_/cancel_");
        return;
    }
    if (str_starts_with($data, 'photo_post_')) {
        $id = (int)str_replace('photo_post_', '', $data);
        setState($userId, 'waiting_post_photo', ['post_id' => $id]);
        editMessage($chatId, $msgId, "🖼 Post rasmini yuboring:

_/cancel_");
        return;
    }
    if (str_starts_with($data, 'delete_post_')) {
        $id = (int)str_replace('delete_post_', '', $data);
        $kb = ['inline_keyboard' => [[
            ['text' => '✅ Ha', 'callback_data' => "confirm_del_post_{$id}"],
            ['text' => '❌ Bekor', 'callback_data' => 'posts_menu'],
        ]]];
        editMessage($chatId, $msgId, "⚠️ Postni o'chirmoqchimisiz?", $kb);
        return;
    }
    if (str_starts_with($data, 'confirm_del_post_')) {
        $id = (int)str_replace('confirm_del_post_', '', $data);
        getDB()->prepare("UPDATE posts SET is_active=0 WHERE id=?")->execute([$id]);
        editMessage($chatId, $msgId, "✅ Post o'chirildi.");
        sleep(1);
        editPostsList($chatId, $msgId);
        return;
    }

    // ---- KANAL ULASH ----
    if ($data === 'channel_settings') {
        editChannelSettings($chatId, $msgId);
        return;
    }
    if ($data === 'set_channel') {
        setState($userId, 'waiting_channel', []);
        editMessage($chatId, $msgId, "📢 Kanal linkini kiriting:\nMasalan: https://t.me/kanalingiz\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if ($data === 'set_admin_username') {
        setState($userId, 'waiting_admin_username', []);
        editMessage($chatId, $msgId, "👤 Admin username ni kiriting (@ belgisisiz):\nMasalan: username\n\n_Bekor qilish uchun /cancel_");
        return;
    }
    if ($data === 'set_dev_username') {
        setState($userId, 'waiting_dev_username', []);
        editMessage($chatId, $msgId, "🧑‍💻 Dasturchi username ni kiriting (@ belgisisiz):\n\n_Bekor qilish uchun /cancel_");
        return;
    }
}

// ============================================================
//  ADMIN STATE HANDLER
// ============================================================
function handleAdminState(array $msg, array $state, int $chatId, int $userId, string $text): void {
    $stateName = $state['state'];
    $data      = $state['data'] ?? [];

    // Bekor qilish
    if ($text === '/cancel') {
        clearState($userId);
        sendAdminMenu($chatId);
        return;
    }

    $db = getDB();

    switch ($stateName) {

        // Bo'lim nomi (yangi)
        case 'waiting_section_name':
            $count = $db->query("SELECT COUNT(*) FROM sections")->fetchColumn();
            $stmt  = $db->prepare("INSERT INTO sections (name, sort_order) VALUES (?, ?)");
            $stmt->execute([$text, $count + 1]);
            $newId = $db->lastInsertId();
            clearState($userId);
            sendMessage($chatId, "✅ Bo'lim qo'shildi: *{$text}*\n\nEndi rasmini yuboring yoki /cancel");
            setState($userId, 'waiting_section_photo', ['section_id' => $newId, 'is_new' => true]);
            // Bildirishnoma
            notifyUsers("📂 *Yangi bo'lim qo'shildi!*\n\n📁 *{$text}*\n\n🚀 Avtopilot orqali ko'ring!", $userId);
            break;

        // Bo'lim nomini o'zgartirish
        case 'waiting_rename_section':
            $db->prepare("UPDATE sections SET name=? WHERE id=?")->execute([$text, $data['section_id']]);
            clearState($userId);
            sendMessage($chatId, "✅ Bo'lim nomi o'zgartirildi: *{$text}*");
            sendAdminMenu($chatId);
            break;

        // Darslik nomi (yangi)
        case 'waiting_lesson_title':
            setState($userId, 'waiting_lesson_desc', [
                'section_id' => $data['section_id'],
                'title'      => $text,
            ]);
            sendMessage($chatId, "📄 Darslik tavsifini kiriting:\n\n_Bekor qilish uchun /cancel_");
            break;

        // Darslik tavsifi
        case 'waiting_lesson_desc':
            if (isset($data['lesson_id'])) {
                // Mavjud darslikni tahrirlash
                $db->prepare("UPDATE lessons SET description=? WHERE id=?")->execute([$text, $data['lesson_id']]);
                clearState($userId);
                sendMessage($chatId, "✅ Tavsif yangilandi.");
                sendAdminMenu($chatId);
            } else {
                // Yangi darslik — link kutish
                setState($userId, 'waiting_lesson_link_new', [
                    'section_id'  => $data['section_id'],
                    'title'       => $data['title'],
                    'description' => $text,
                ]);
                sendMessage($chatId, "🔗 Telegram kanal linkini kiriting:\nMasalan: https://t.me/kanal/123\n\n_Bekor qilish uchun /cancel_");
            }
            break;

        // Yangi darslik linki
        case 'waiting_lesson_link_new':
            $count = $db->prepare("SELECT COUNT(*) FROM lessons WHERE section_id=?");
            $count->execute([$data['section_id']]);
            $order = $count->fetchColumn() + 1;

            $stmt = $db->prepare("INSERT INTO lessons (section_id, title, description, telegram_link, sort_order) VALUES (?,?,?,?,?)");
            $stmt->execute([$data['section_id'], $data['title'], $data['description'], $text, $order]);
            $newId = $db->lastInsertId();

            // Bo'lim nomini olish
            $sec = $db->prepare("SELECT name FROM sections WHERE id=?");
            $sec->execute([$data['section_id']]);
            $secName = $sec->fetchColumn();

            clearState($userId);
            sendMessage($chatId, "✅ Darslik qo'shildi: *{$data['title']}*\n\nEndi obložka rasmini yuboring yoki /cancel");
            setState($userId, 'waiting_lesson_photo', ['lesson_id' => $newId, 'is_new' => true]);

            // Bildirishnoma
            notifyUsers("📚 *Yangi darslik qo'shildi!*\n\n📁 Bo'lim: *{$secName}*\n📖 Darslik: *{$data['title']}*\n\n🚀 Avtopilot orqali ko'ring!", $userId);
            break;

        // Darslik linkini o'zgartirish
        case 'waiting_lesson_link':
            $db->prepare("UPDATE lessons SET telegram_link=? WHERE id=?")->execute([$text, $data['lesson_id']]);
            clearState($userId);
            sendMessage($chatId, "✅ Link yangilandi.");
            sendAdminMenu($chatId);
            break;

        // Darslik nomini o'zgartirish
        case 'waiting_rename_lesson':
            $db->prepare("UPDATE lessons SET title=? WHERE id=?")->execute([$text, $data['lesson_id']]);
            clearState($userId);
            sendMessage($chatId, "✅ Darslik nomi o'zgartirildi: *{$text}*");
            sendAdminMenu($chatId);
            break;

        // Broadcast xabari
        case 'waiting_broadcast':
            setState($userId, 'broadcast_confirm', ['text' => $text]);
            $kb = ['inline_keyboard' => [[
                ['text' => '✅ Yuborish', 'callback_data' => 'confirm_broadcast'],
                ['text' => '❌ Bekor', 'callback_data' => 'cancel_broadcast'],
            ]]];
            sendMessage($chatId, "📣 *Preview:*\n\n{$text}\n\n---\nBarcha foydalanuvchilarga yuborilsin?", $kb);
            break;

        case 'waiting_post_title':
            setState($userId, 'waiting_post_content_new', ['title' => $text]);
            sendMessage($chatId, "📄 Post matnini kiriting:

_/cancel_");
            break;

        case 'waiting_post_content_new':
            $db = getDB();
            $cnt = $db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
            $stmt = $db->prepare("INSERT INTO posts (title, content, sort_order) VALUES (?,?,?)");
            $stmt->execute([$data['title'], $text, $cnt + 1]);
            $newId = $db->lastInsertId();
            clearState($userId);
            sendMessage($chatId, "Post qo'shildi! Endi rasmini yuboring yoki /cancel");
            setState($userId, 'waiting_post_photo', ['post_id' => $newId]);
            break;

        case 'waiting_rename_post':
            getDB()->prepare("UPDATE posts SET title=? WHERE id=?")->execute([$text, $data['post_id']]);
            clearState($userId);
            sendMessage($chatId, "✅ Sarlavha yangilandi.");
            sendAdminMenu($chatId);
            break;

        case 'waiting_post_content':
            getDB()->prepare("UPDATE posts SET content=? WHERE id=?")->execute([$text, $data['post_id']]);
            clearState($userId);
            sendMessage($chatId, "✅ Matn yangilandi.");
            sendAdminMenu($chatId);
            break;

        // Kanal linki
        case 'waiting_channel':
            saveConfig('channel_link', $text);
            clearState($userId);
            sendMessage($chatId, "✅ Kanal linki saqlandi: {$text}");
            sendAdminMenu($chatId);
            break;

        // Admin username
        case 'waiting_admin_username':
            saveConfig('admin_username', $text);
            clearState($userId);
            sendMessage($chatId, "✅ Admin username saqlandi: @{$text}");
            sendAdminMenu($chatId);
            break;

        // Dev username
        case 'waiting_dev_username':
            saveConfig('dev_username', $text);
            clearState($userId);
            sendMessage($chatId, "✅ Dasturchi username saqlandi: @{$text}");
            sendAdminMenu($chatId);
            break;

        default:
            clearState($userId);
            sendAdminMenu($chatId);
    }
}

// ============================================================
//  RASM YUKLASH
// ============================================================
function handleImageUpload(array $msg, ?array $state, int $chatId, int $userId): void {
    if (!$state) {
        sendMessage($chatId, "⚠️ Avval bo'lim yoki darslik tanlang.");
        return;
    }

    $stateName = $state['state'];
    $data      = $state['data'] ?? [];

    $fileId = isset($msg['photo'])
        ? end($msg['photo'])['file_id']
        : ($msg['document']['file_id'] ?? null);

    if (!$fileId) return;

    sendMessage($chatId, "⏳ Rasm yuklanmoqda...");

    $db = getDB();

    if ($stateName === 'waiting_section_photo' && isset($data['section_id'])) {
        $old = $db->prepare("SELECT image_path FROM sections WHERE id=?");
        $old->execute([$data['section_id']]);
        $oldRow = $old->fetch();
        if ($oldRow['image_path']) @unlink(UPLOAD_DIR . 'sections/' . $oldRow['image_path']);

        $fileName = downloadTelegramFileTo($fileId, 'sections');
        if (!$fileName) { sendMessage($chatId, "❌ Rasm yuklanmadi."); return; }

        $db->prepare("UPDATE sections SET image_path=? WHERE id=?")->execute([$fileName, $data['section_id']]);
        clearState($userId);
        sendMessage($chatId, "✅ Bo'lim rasmi saqlandi!");
        sendAdminMenu($chatId);

    } elseif ($stateName === 'waiting_post_photo' && isset($data['post_id'])) {
        $fileName = downloadTelegramFileTo($fileId, 'posts');
        if (!$fileName) { sendMessage($chatId, "❌ Rasm yuklanmadi."); return; }
        $db = getDB();
        $qOld = $db->prepare("SELECT image_path FROM posts WHERE id=?");
        $qOld->execute([$data['post_id']]);
        $oldRow = $qOld->fetch();
        if ($oldRow && $oldRow['image_path']) @unlink(UPLOAD_DIR . 'posts/' . $oldRow['image_path']);
        $db->prepare("UPDATE posts SET image_path=? WHERE id=?")->execute([$fileName, $data['post_id']]);
        clearState($userId);
        sendMessage($chatId, "✅ Post rasmi saqlandi!");
        sendAdminMenu($chatId);

    } elseif ($stateName === 'waiting_lesson_photo' && isset($data['lesson_id'])) {
        $old = $db->prepare("SELECT cover_image FROM lessons WHERE id=?");
        $old->execute([$data['lesson_id']]);
        $oldRow = $old->fetch();
        if ($oldRow['cover_image']) @unlink(UPLOAD_DIR . 'lessons/' . $oldRow['cover_image']);

        $fileName = downloadTelegramFileTo($fileId, 'lessons');
        if (!$fileName) { sendMessage($chatId, "❌ Rasm yuklanmadi."); return; }

        $db->prepare("UPDATE lessons SET cover_image=? WHERE id=?")->execute([$fileName, $data['lesson_id']]);
        clearState($userId);
        sendMessage($chatId, "✅ Darslik rasmi saqlandi!");
        sendAdminMenu($chatId);
    }
}

// ============================================================
//  ADMIN MENYULAR
// ============================================================
function sendAdminMenu(int $chatId): void {
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '📂 Bo\'limlar', 'callback_data' => 'sections_menu'],
                ['text' => '📚 Darsliklar', 'callback_data' => 'lessons_menu'],
            ],
            [
                ['text' => '➕ Bo\'lim qo\'shish', 'callback_data' => 'add_section'],
            ],
            [
                ['text' => '📝 Postlar', 'callback_data' => 'posts_menu'],
            ],
            [
                ['text' => '📊 Statistika', 'callback_data' => 'stats'],
                ['text' => '📣 Xabar yuborish', 'callback_data' => 'broadcast'],
            ],
            [
                ['text' => '⚙️ Sozlamalar', 'callback_data' => 'channel_settings'],
            ],
        ]
    ];
    sendMessage($chatId, "🎛 *AvtoPilot Admin Panel*\n\nNimani qilmoqchisiz?", $keyboard);
}

function editToAdminMenu(int $chatId, int $msgId): void {
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '📂 Bo\'limlar', 'callback_data' => 'sections_menu'],
                ['text' => '📚 Darsliklar', 'callback_data' => 'lessons_menu'],
            ],
            [
                ['text' => '➕ Bo\'lim qo\'shish', 'callback_data' => 'add_section'],
            ],
            [
                ['text' => '📝 Postlar', 'callback_data' => 'posts_menu'],
            ],
            [
                ['text' => '📊 Statistika', 'callback_data' => 'stats'],
                ['text' => '📣 Xabar yuborish', 'callback_data' => 'broadcast'],
            ],
            [
                ['text' => '⚙️ Sozlamalar', 'callback_data' => 'channel_settings'],
            ],
        ]
    ];
    editMessage($chatId, $msgId, "🎛 *AvtoPilot Admin Panel*\n\nNimani qilmoqchisiz?", $keyboard);
}

function editSectionsList(int $chatId, int $msgId): void {
    $db       = getDB();
    $sections = $db->query("SELECT * FROM sections WHERE is_active=1 ORDER BY sort_order")->fetchAll();

    $buttons = [];
    foreach ($sections as $s) {
        $buttons[] = [['text' => "📁 {$s['name']}", 'callback_data' => "edit_section_{$s['id']}"]];
    }
    $buttons[] = [['text' => '🔙 Orqaga', 'callback_data' => 'back_main']];

    editMessage($chatId, $msgId, "📂 *Bo'limlar ro'yxati:*", ['inline_keyboard' => $buttons]);
}

function editSectionMenu(int $chatId, int $msgId, int $id): void {
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM sections WHERE id=?");
    $stmt->execute([$id]);
    $s = $stmt->fetch();
    if (!$s) return;

    $kb = ['inline_keyboard' => [
        [['text' => '✏️ Nomini o\'zgartirish', 'callback_data' => "rename_section_{$id}"]],
        [['text' => '🖼 Rasmini o\'zgartirish', 'callback_data' => "photo_section_{$id}"]],
        [['text' => '🗑 O\'chirish', 'callback_data' => "delete_section_{$id}"]],
        [['text' => '🔙 Orqaga', 'callback_data' => 'sections_menu']],
    ]];
    editMessage($chatId, $msgId, "📁 *{$s['name']}*\n\nNimani o'zgartirmoqchisiz?", $kb);
}

function editSectionsForLessons(int $chatId, int $msgId): void {
    $db       = getDB();
    $sections = $db->query("SELECT * FROM sections WHERE is_active=1 ORDER BY sort_order")->fetchAll();

    $buttons = [];
    foreach ($sections as $s) {
        $buttons[] = [['text' => "📁 {$s['name']}", 'callback_data' => "section_lessons_{$s['id']}"]];
    }
    $buttons[] = [['text' => '🔙 Orqaga', 'callback_data' => 'back_main']];

    editMessage($chatId, $msgId, "📚 *Qaysi bo'lim darsliklarini boshqarmoqchisiz?*", ['inline_keyboard' => $buttons]);
}

function editLessonsList(int $chatId, int $msgId, int $sectionId): void {
    $db   = getDB();
    $sec  = $db->prepare("SELECT * FROM sections WHERE id=?");
    $sec->execute([$sectionId]);
    $s = $sec->fetch();

    $lst  = $db->prepare("SELECT * FROM lessons WHERE section_id=? AND is_active=1 ORDER BY sort_order");
    $lst->execute([$sectionId]);
    $lessons = $lst->fetchAll();

    $buttons = [];
    foreach ($lessons as $l) {
        $buttons[] = [['text' => "📖 {$l['title']}", 'callback_data' => "edit_lesson_{$l['id']}"]];
    }
    $buttons[] = [['text' => "➕ Darslik qo'shish", 'callback_data' => "add_lesson_{$sectionId}"]];
    $buttons[] = [['text' => '🔙 Orqaga', 'callback_data' => 'lessons_menu']];

    $count = count($lessons);
    editMessage($chatId, $msgId, "📚 *{$s['name']}*\n{$count} ta darslik", ['inline_keyboard' => $buttons]);
}

function editLessonMenu(int $chatId, int $msgId, int $id): void {
    $db   = getDB();
    $stmt = $db->prepare("SELECT l.*, s.name as sec_name FROM lessons l JOIN sections s ON s.id=l.section_id WHERE l.id=?");
    $stmt->execute([$id]);
    $l = $stmt->fetch();
    if (!$l) return;

    $text  = "📖 *{$l['title']}*\n";
    $text .= "📂 {$l['sec_name']}\n";
    $text .= "📄 " . ($l['description'] ? mb_substr($l['description'], 0, 80) . '...' : '_tavsif yo\'q_') . "\n";
    $text .= "🔗 " . ($l['telegram_link'] ?? '_link yo\'q_');

    $kb = ['inline_keyboard' => [
        [['text' => '✏️ Nomi',    'callback_data' => "rename_lesson_{$id}"],
         ['text' => '📄 Tavsif', 'callback_data' => "desc_lesson_{$id}"]],
        [['text' => '🖼 Rasm',   'callback_data' => "photo_lesson_{$id}"],
         ['text' => '🔗 Link',   'callback_data' => "link_lesson_{$id}"]],
        [['text' => '🗑 O\'chirish', 'callback_data' => "delete_lesson_{$id}"]],
        [['text' => '🔙 Orqaga', 'callback_data' => "section_lessons_{$l['section_id']}"]],
    ]];
    editMessage($chatId, $msgId, $text, $kb);
}

function editStats(int $chatId, int $msgId): void {
    $db = getDB();

    $totalUsers    = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $notifUsers    = $db->query("SELECT COUNT(*) FROM users WHERE notifications=1")->fetchColumn();
    $totalSections = $db->query("SELECT COUNT(*) FROM sections WHERE is_active=1")->fetchColumn();
    $totalLessons  = $db->query("SELECT COUNT(*) FROM lessons WHERE is_active=1")->fetchColumn();
    $newToday      = $db->query("SELECT COUNT(*) FROM users WHERE DATE(created_at)=CURDATE()")->fetchColumn();
    $newWeek       = $db->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

    $text  = "📊 *Statistika*\n\n";
    $text .= "👥 *Foydalanuvchilar:*\n";
    $text .= "• Jami: *{$totalUsers}* ta\n";
    $text .= "• Bugun qo'shildi: *{$newToday}* ta\n";
    $text .= "• Hafta ichida: *{$newWeek}* ta\n";
    $text .= "• Bildirishnoma yoqilgan: *{$notifUsers}* ta\n\n";
    $text .= "📚 *Kontent:*\n";
    $text .= "• Bo'limlar: *{$totalSections}* ta\n";
    $text .= "• Darsliklar: *{$totalLessons}* ta\n";

    $kb = ['inline_keyboard' => [[['text' => '🔙 Orqaga', 'callback_data' => 'back_main']]]];
    editMessage($chatId, $msgId, $text, $kb);
}

function editChannelSettings(int $chatId, int $msgId): void {
    $channel  = getConfig('channel_link') ?: '_belgilanmagan_';
    $adminU   = getConfig('admin_username') ?: '_belgilanmagan_';
    $devU     = getConfig('dev_username') ?: '_belgilanmagan_';

    $text  = "⚙️ *Sozlamalar*\n\n";
    $text .= "📢 Kanal: {$channel}\n";
    $text .= "👤 Admin: @{$adminU}\n";
    $text .= "🧑‍💻 Dasturchi: @{$devU}";

    $kb = ['inline_keyboard' => [
        [['text' => '📢 Kanal linkini o\'zgartirish', 'callback_data' => 'set_channel']],
        [['text' => '👤 Admin username', 'callback_data' => 'set_admin_username']],
        [['text' => '🧑‍💻 Dasturchi username', 'callback_data' => 'set_dev_username']],
        [['text' => '🔙 Orqaga', 'callback_data' => 'back_main']],
    ]];
    editMessage($chatId, $msgId, $text, $kb);
}

// ============================================================
//  BROADCAST
// ============================================================
function doBroadcast(int $chatId, int $msgId, string $text): void {
    $db    = getDB();
    $users = $db->query("SELECT telegram_id FROM users")->fetchAll();

    editMessage($chatId, $msgId, "📣 Yuborilmoqda... (" . count($users) . " ta foydalanuvchi)");

    $sent = 0; $failed = 0;
    foreach ($users as $u) {
        $result = sendMessage((int)$u['telegram_id'], $text);
        if ($result) $sent++; else $failed++;
        usleep(50000); // 50ms — flood limitga tushmaslik
    }

    editMessage($chatId, $msgId,
        "✅ *Xabar yuborildi!*\n\n✔️ Muvaffaqiyatli: *{$sent}*\n❌ Yuborilmadi: *{$failed}*",
        ['inline_keyboard' => [[['text' => '🔙 Orqaga', 'callback_data' => 'back_main']]]]
    );
}

// ============================================================
//  BILDIRISHNOMA
// ============================================================
function notifyUsers(string $text, int $excludeId): void {
    $db    = getDB();
    $users = $db->query("SELECT telegram_id FROM users WHERE notifications=1")->fetchAll();
    foreach ($users as $u) {
        if ((int)$u['telegram_id'] === $excludeId) continue;
        sendMessage((int)$u['telegram_id'], $text);
        usleep(50000);
    }
}

// ============================================================
//  CONFIG (kanal, admin username DB da saqlanadi)
// ============================================================
function getConfig(string $key): ?string {
    try {
        $db   = getDB();
        $stmt = $db->prepare("SELECT value FROM bot_config WHERE `key`=?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : null;
    } catch (\Throwable) {
        return null;
    }
}

function saveConfig(string $key, string $value): void {
    $db = getDB();
    $db->prepare("INSERT INTO bot_config (`key`, value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)")
       ->execute([$key, $value]);
}

// Config da dinamik qiymatlarni olish
function getChannelLink(): string {
    return getConfig('channel_link') ?? '#';
}
function getAdminUsername(): string {
    return getConfig('admin_username') ?? 'admin';
}
function getDevUsername(): string {
    return getConfig('dev_username') ?? 'admin';
}

// ============================================================
//  FOYDALANUVCHINI SAQLASH
// ============================================================
function saveUser(array $from): void {
    try {
        $db   = getDB();
        $stmt = $db->prepare("
            INSERT INTO users (telegram_id, username, full_name)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
                username  = VALUES(username),
                full_name = VALUES(full_name),
                updated_at = CURRENT_TIMESTAMP
        ");
        $fullName = trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? ''));
        $stmt->execute([$from['id'], $from['username'] ?? null, $fullName ?: null]);
    } catch (\Throwable) {}
}

// ============================================================
//  HELPERS
// ============================================================
function isAdmin(int $userId): bool {
    return in_array($userId, ADMIN_IDS);
}

function getState(int $userId): ?array {
    try {
        $db   = getDB();
        $stmt = $db->prepare("SELECT state, data FROM bot_states WHERE telegram_id=?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if (!$row || !$row['state']) return null;
        return ['state' => $row['state'], 'data' => json_decode($row['data'] ?? '{}', true)];
    } catch (\Throwable) { return null; }
}

function setState(int $userId, string $state, array $data = []): void {
    $db = getDB();
    $db->prepare("INSERT INTO bot_states (telegram_id, state, data) VALUES (?,?,?)
                  ON DUPLICATE KEY UPDATE state=VALUES(state), data=VALUES(data)")
       ->execute([$userId, $state, json_encode($data)]);
}

function clearState(int $userId): void {
    getDB()->prepare("DELETE FROM bot_states WHERE telegram_id=?")->execute([$userId]);
}

function sendMessage(int $chatId, string $text, ?array $keyboard = null, string $kbType = 'inline'): bool {
    $params = [
        'chat_id'    => $chatId,
        'text'       => $text,
        'parse_mode' => 'Markdown',
    ];
    if ($keyboard) {
        $params['reply_markup'] = json_encode($keyboard);
    }
    $ch = curl_init(BOT_URL . '/sendMessage');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($res, true);
    return $decoded['ok'] ?? false;
}

function editMessage(int $chatId, int $msgId, string $text, ?array $keyboard = null): void {
    $params = [
        'chat_id'    => $chatId,
        'message_id' => $msgId,
        'text'       => $text,
        'parse_mode' => 'Markdown',
    ];
    if ($keyboard) $params['reply_markup'] = json_encode($keyboard);

    $ch = curl_init(BOT_URL . '/editMessageText');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function answerCallback(string $id): void {
    $ch = curl_init(BOT_URL . '/answerCallbackQuery');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => ['callback_query_id' => $id],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

// ============================================================
//  POSTS FUNKSIYALARI
// ============================================================
function editPostsList(int $chatId, int $msgId): void {
    $db = getDB();
    $posts = $db->query("SELECT * FROM posts WHERE is_active=1 ORDER BY created_at DESC")->fetchAll();
    $buttons = [];
    foreach ($posts as $p) {
        $title = mb_substr($p['title'], 0, 28);
        $buttons[] = [['text' => "📝 {$title}", 'callback_data' => "edit_post_{$p['id']}"]];
    }
    $buttons[] = [['text' => "➕ Post qo'shish", 'callback_data' => 'add_post']];
    $buttons[] = [['text' => '🔙 Orqaga', 'callback_data' => 'back_main']];
    $count = count($posts);
    editMessage($chatId, $msgId, "📝 *Postlar:* {$count} ta", ['inline_keyboard' => $buttons]);
}

function editPostMenu(int $chatId, int $msgId, int $id): void {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM posts WHERE id=?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if (!$p) return;
    $preview = mb_substr($p['content'], 0, 80);
    $text = "📝 *{$p['title']}*

{$preview}...";
    $kb = ['inline_keyboard' => [
        [
            ['text' => '✏️ Sarlavha', 'callback_data' => "rename_post_{$id}"],
            ['text' => '📄 Matn', 'callback_data' => "content_post_{$id}"],
        ],
        [['text' => '🖼 Rasm', 'callback_data' => "photo_post_{$id}"]],
        [['text' => '🗑 O\'chirish', 'callback_data' => "delete_post_{$id}"]],
        [['text' => '🔙 Orqaga', 'callback_data' => 'posts_menu']],
    ]];
    editMessage($chatId, $msgId, $text, $kb);
}
