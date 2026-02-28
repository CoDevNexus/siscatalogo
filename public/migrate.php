<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';

$db = \App\Core\Database::getInstance();
$pdo = $db->getPdo();  // Necesitamos PDO directamente para DESCRIBE

// Columnas a añadir: [nombre, definición SQL]
$newColumns = [
    'price_combo' => 'DECIMAL(10,2) DEFAULT NULL AFTER price_dozen',
    'allow_client_note' => 'TINYINT(1) NOT NULL DEFAULT 0',
    'allow_client_logo' => 'TINYINT(1) NOT NULL DEFAULT 0',
    'digital_file_path' => 'VARCHAR(255) DEFAULT NULL',
];

// Columnas actuales en products
$existing = [];
$rows = $pdo->query("DESCRIBE products")->fetchAll(\PDO::FETCH_ASSOC);
foreach ($rows as $r)
    $existing[] = $r['Field'];

$done = 0;
foreach ($newColumns as $col => $def) {
    if (!in_array($col, $existing)) {
        $pdo->exec("ALTER TABLE products ADD COLUMN `$col` $def");
        echo "Columna '$col' añadida.\n";
        $done++;
    } else {
        echo "Columna '$col' ya existe, se omite.\n";
    }
}

// Crear product_images si no existe
$pdo->exec("CREATE TABLE IF NOT EXISTS `product_images` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `product_id`  INT NOT NULL,
    `source`      ENUM('local','url','api') NOT NULL DEFAULT 'local',
    `image_path`  VARCHAR(500) NOT NULL,
    `is_primary`  TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order`  INT NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
)");
echo "Tabla product_images lista.\n";
echo "Migración completada: $done columnas añadidas.\n";
