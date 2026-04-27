<?php
// api.php - Mini App uchun API

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'sections':
        getSections();
        break;
    case 'lessons':
        getLessons();
        break;
    case 'lesson':
        getLesson();
        break;
    case 'posts':
        getPosts();
        break;
    default:
        echo json_encode(['error' => 'Noma\'lum action']);
}

// Barcha bo'limlarni olish
function getSections(): void {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT s.id, s.name, s.image_path, s.sort_order, s.is_active, s.created_at, s.updated_at,
               COUNT(l.id) as lesson_count
        FROM sections s
        LEFT JOIN lessons l ON l.section_id = s.id AND l.is_active = 1
        WHERE s.is_active = 1
        GROUP BY s.id, s.name, s.image_path, s.sort_order, s.is_active, s.created_at, s.updated_at
        ORDER BY s.sort_order ASC
    ");
    $stmt->execute();
    $sections = $stmt->fetchAll();

    foreach ($sections as &$section) {
        if ($section['image_path']) {
            $section['image_url'] = UPLOAD_URL . 'sections/' . $section['image_path'];
        } else {
            $section['image_url'] = null;
        }
    }

    echo json_encode(['success' => true, 'data' => $sections]);
}

// Bo'lim darsliklarini olish
function getLessons(): void {
    $sectionId = (int)($_GET['section_id'] ?? 0);
    if (!$sectionId) {
        echo json_encode(['error' => 'section_id kerak']);
        return;
    }

    $db = getDB();

    // Bo'lim ma'lumoti
    $stmt = $db->prepare("SELECT * FROM sections WHERE id = ? AND is_active = 1");
    $stmt->execute([$sectionId]);
    $section = $stmt->fetch();

    if (!$section) {
        echo json_encode(['error' => 'Bo\'lim topilmadi']);
        return;
    }

    // Darsliklar
    $stmt = $db->prepare("
        SELECT * FROM lessons 
        WHERE section_id = ? AND is_active = 1 
        ORDER BY sort_order ASC
    ");
    $stmt->execute([$sectionId]);
    $lessons = $stmt->fetchAll();

    foreach ($lessons as &$lesson) {
        if ($lesson['cover_image']) {
            $lesson['cover_url'] = UPLOAD_URL . 'lessons/' . $lesson['cover_image'];
        } else {
            $lesson['cover_url'] = null;
        }
    }

    echo json_encode([
        'success' => true,
        'section' => $section,
        'data' => $lessons
    ]);
}

// Bitta darslik
function getLesson(): void {
    $lessonId = (int)($_GET['id'] ?? 0);
    if (!$lessonId) {
        echo json_encode(['error' => 'id kerak']);
        return;
    }

    $db = getDB();
    $stmt = $db->prepare("
        SELECT l.*, s.name as section_name 
        FROM lessons l
        JOIN sections s ON s.id = l.section_id
        WHERE l.id = ? AND l.is_active = 1
    ");
    $stmt->execute([$lessonId]);
    $lesson = $stmt->fetch();

    if (!$lesson) {
        echo json_encode(['error' => 'Darslik topilmadi']);
        return;
    }

    if ($lesson['cover_image']) {
        $lesson['cover_url'] = UPLOAD_URL . 'lessons/' . $lesson['cover_image'];
    } else {
        $lesson['cover_url'] = null;
    }

    echo json_encode(['success' => true, 'data' => $lesson]);
}

// Postlarni olish
function getPosts(): void {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT * FROM posts
        WHERE is_active = 1
        ORDER BY sort_order DESC, created_at DESC
    ");
    $stmt->execute();
    $posts = $stmt->fetchAll();
    foreach ($posts as &$post) {
        $post['image_url'] = $post['image_path']
            ? UPLOAD_URL . 'posts/' . $post['image_path']
            : null;
    }
    echo json_encode(['success' => true, 'data' => $posts]);
}
