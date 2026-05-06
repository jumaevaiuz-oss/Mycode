<?php
// api.php - Mini App uchun API

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');
// CORS: faqat ruxsat etilgan domenlar
$allowedOrigins = [
    'https://6831eecaafce3.xvest3.ru',
    'https://t.me',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: ' . SITE_URL);
}
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: public, max-age=60');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'sections': getSections(); break;
    case 'lessons':  getLessons();  break;
    case 'lesson':   getLesson();   break;
    case 'posts':    getPosts();    break;
    case 'search':   doSearch();    break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Noma\'lum action']);
}

function getSections(): void {
    $db   = getDB();
    $stmt = $db->query("
        SELECT s.id, s.name, s.image_path, s.sort_order,
               COUNT(l.id) AS lesson_count
        FROM sections s
        LEFT JOIN lessons l ON l.section_id = s.id AND l.is_active = 1
        WHERE s.is_active = 1
        GROUP BY s.id, s.name, s.image_path, s.sort_order
        ORDER BY s.sort_order ASC
    ");
    $sections = $stmt->fetchAll();

    foreach ($sections as &$s) {
        $s['image_url'] = $s['image_path']
            ? UPLOAD_URL . 'sections/' . $s['image_path']
            : null;
        unset($s['image_path']);
    }

    echo json_encode(['success' => true, 'data' => $sections]);
}

function getLessons(): void {
    $sectionId = (int)($_GET['section_id'] ?? 0);
    if (!$sectionId) {
        http_response_code(400);
        echo json_encode(['error' => 'section_id kerak']);
        return;
    }

    $db = getDB();

    $stmt = $db->prepare("SELECT id, name FROM sections WHERE id = ? AND is_active = 1");
    $stmt->execute([$sectionId]);
    $section = $stmt->fetch();

    if (!$section) {
        http_response_code(404);
        echo json_encode(['error' => 'Bo\'lim topilmadi']);
        return;
    }

    $stmt = $db->prepare("
        SELECT id, title, description, cover_image, telegram_link, sort_order
        FROM lessons
        WHERE section_id = ? AND is_active = 1
        ORDER BY sort_order ASC
    ");
    $stmt->execute([$sectionId]);
    $lessons = $stmt->fetchAll();

    foreach ($lessons as &$l) {
        $l['cover_url'] = $l['cover_image']
            ? UPLOAD_URL . 'lessons/' . $l['cover_image']
            : null;
        unset($l['cover_image']);
    }

    echo json_encode(['success' => true, 'section' => $section, 'data' => $lessons]);
}

function getLesson(): void {
    $lessonId = (int)($_GET['id'] ?? 0);
    if (!$lessonId) {
        http_response_code(400);
        echo json_encode(['error' => 'id kerak']);
        return;
    }

    $db   = getDB();
    $stmt = $db->prepare("
        SELECT l.id, l.title, l.description, l.cover_image, l.telegram_link,
               s.name AS section_name
        FROM lessons l
        JOIN sections s ON s.id = l.section_id
        WHERE l.id = ? AND l.is_active = 1
    ");
    $stmt->execute([$lessonId]);
    $lesson = $stmt->fetch();

    if (!$lesson) {
        http_response_code(404);
        echo json_encode(['error' => 'Darslik topilmadi']);
        return;
    }

    $lesson['cover_url'] = $lesson['cover_image']
        ? UPLOAD_URL . 'lessons/' . $lesson['cover_image']
        : null;
    unset($lesson['cover_image']);

    echo json_encode(['success' => true, 'data' => $lesson]);
}

function doSearch(): void {
    $q = trim($_GET['q'] ?? '');
    if (mb_strlen($q) < 2) {
        echo json_encode(['success' => true, 'sections' => [], 'lessons' => []]);
        return;
    }
    $db   = getDB();
    $like = '%' . $q . '%';

    $stmt = $db->prepare("
        SELECT s.id, s.name, s.image_path,
               COUNT(l.id) AS lesson_count
        FROM sections s
        LEFT JOIN lessons l ON l.section_id = s.id AND l.is_active = 1
        WHERE s.name LIKE ? AND s.is_active = 1
        GROUP BY s.id ORDER BY s.sort_order LIMIT 10
    ");
    $stmt->execute([$like]);
    $sections = $stmt->fetchAll();
    foreach ($sections as &$s) {
        $s['image_url'] = $s['image_path'] ? UPLOAD_URL . 'sections/' . $s['image_path'] : null;
        unset($s['image_path']);
    }

    $stmt = $db->prepare("
        SELECT l.id, l.title, l.cover_image, s.name AS section_name, l.section_id
        FROM lessons l
        JOIN sections s ON s.id = l.section_id
        WHERE l.title LIKE ? AND l.is_active = 1
        ORDER BY l.sort_order LIMIT 15
    ");
    $stmt->execute([$like]);
    $lessons = $stmt->fetchAll();
    foreach ($lessons as &$l) {
        $l['cover_url'] = $l['cover_image'] ? UPLOAD_URL . 'lessons/' . $l['cover_image'] : null;
        unset($l['cover_image']);
    }

    echo json_encode(['success' => true, 'sections' => $sections, 'lessons' => $lessons]);
}

function getPosts(): void {
    $db   = getDB();
    $stmt = $db->query("
        SELECT id, title, content, image_path, button_text, button_url, created_at
        FROM posts
        WHERE is_active = 1
        ORDER BY sort_order DESC, created_at DESC
    ");
    $posts = $stmt->fetchAll();

    foreach ($posts as &$p) {
        $p['image_url'] = $p['image_path']
            ? UPLOAD_URL . 'posts/' . $p['image_path']
            : null;
        unset($p['image_path']);
    }

    echo json_encode(['success' => true, 'data' => $posts]);
}
