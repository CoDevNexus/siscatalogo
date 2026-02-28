<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';

$db = \App\Core\Database::getInstance();

$cats = [
    ['Cajas y Cofres', 'cajas-cofres', 'physical'],
    ['Llaveros', 'llaveros', 'physical'],
    ['Trofeos y Premios', 'trofeos-premios', 'physical'],
    ['Sublimación', 'sublimacion', 'physical'],
    ['Grabado Personalizado', 'grabado-personalizado', 'physical'],
    ['Vectores / DXF', 'vectores-dxf', 'digital'],
    ['Diseños SVG', 'disenos-svg', 'digital'],
    ['Archivos de Corte', 'archivos-corte', 'digital'],
];

$inserted = 0;
foreach ($cats as $c) {
    $db->query(
        'INSERT IGNORE INTO categories (name, slug, type) VALUES (:name, :slug, :type)',
        ['name' => $c[0], 'slug' => $c[1], 'type' => $c[2]]
    );
    $inserted++;
}

echo "Categorias insertadas correctamente: $inserted.\n";
