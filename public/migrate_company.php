<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';

$db = \App\Core\Database::getInstance();
$pdo = $db->getPdo();

// Obtener columnas actuales
$existing = [];
foreach ($pdo->query('DESCRIBE company_profile')->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $existing[] = $r['Field'];
}

$toAdd = [
    'eslogan' => "VARCHAR(255) DEFAULT NULL COMMENT 'Eslogan visible en catálogo'",
    'ciudad' => "VARCHAR(100) DEFAULT NULL COMMENT 'Ciudad principal'",
    'tiktok' => "VARCHAR(255) DEFAULT NULL COMMENT 'URL de TikTok'",
    'terms_conditions' => "TEXT         DEFAULT NULL COMMENT 'T&C para proformas'",
    'thank_you_message' => "VARCHAR(255) DEFAULT NULL COMMENT 'Mensaje de agradecimiento en proforma'",
    'maps_embed' => "TEXT         DEFAULT NULL COMMENT 'iframe de Google Maps'",
];

foreach ($toAdd as $col => $def) {
    if (!in_array($col, $existing)) {
        $pdo->exec("ALTER TABLE company_profile ADD COLUMN `$col` $def");
        echo "✅ Columna añadida: $col\n";
    } else {
        echo "ℹ️  Ya existe: $col\n";
    }
}

echo "\nMigración de company_profile completada.\n";
