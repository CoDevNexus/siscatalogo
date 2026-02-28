<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();

echo "=== product_images (primeras 10) ===\n";
$rows = $db->fetchAll("SELECT id, image_path, source FROM product_images LIMIT 10");
print_r($rows);

echo "\n=== Sources disponibles ===\n";
$src = $db->fetchAll("SELECT DISTINCT source FROM product_images");
print_r($src);
