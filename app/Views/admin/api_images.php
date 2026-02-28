<?php
/**
 * AJAX endpoint: lista imágenes disponibles del servidor + ImgBB de BD
 * Llamado desde el modal de selección de imagen del perfil.
 */
if (!isset($_SESSION))
    session_start();
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';

// Seguridad mínima: solo admins logueados
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$images = [];

// ── 1. Imágenes locales en assets/img/ ──
$imgDir = BASE_PATH . 'public/assets/img/';
$allowed = ['webp', 'png', 'jpg', 'jpeg', 'gif', 'svg'];

if (is_dir($imgDir)) {
    foreach (glob($imgDir . '*') as $f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $images[] = [
                'url' => 'assets/img/' . basename($f),
                'full' => APP_URL . 'assets/img/' . basename($f),
                'label' => basename($f),
                'source' => 'local',
            ];
        }
    }
}

// ── 2. Imágenes de productos en storage/productos/ ──
$storDir = BASE_PATH . 'storage/productos/';
if (is_dir($storDir)) {
    foreach (glob($storDir . '*.{webp,jpg,jpeg,png}', GLOB_BRACE) as $f) {
        $images[] = [
            'url' => 'storage/productos/' . basename($f),
            'full' => APP_URL . 'storage/productos/' . basename($f),
            'label' => basename($f),
            'source' => 'local',
        ];
    }
}

// ── 3. ImgBB / URLs externas de product_images ──
$db = \App\Core\Database::getInstance();
try {
    $apiRows = $db->fetchAll(
        "SELECT image_path, source FROM product_images
         WHERE source IN ('api','url') AND image_path != ''
         GROUP BY image_path, source ORDER BY MAX(id) DESC LIMIT 80"
    );
    foreach ($apiRows as $row) {
        $images[] = [
            'url' => $row['image_path'],
            'full' => $row['image_path'],
            'label' => basename(parse_url($row['image_path'], PHP_URL_PATH)) ?: 'ImgBB',
            'source' => 'api',
        ];
    }
} catch (\Exception $e) {
    // Tabla puede no existir
}

echo json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
