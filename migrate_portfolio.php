<?php
define('BASE_PATH', __DIR__ . '/');
require_once 'config/config.php';
require_once 'app/Core/Database.php';

$db = App\Core\Database::getInstance();

try {
    echo "Creando tabla portfolio...\n";
    $sql = "CREATE TABLE IF NOT EXISTS portfolio (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        intro_corta TEXT,
        contenido_enriquecido LONGTEXT,
        imagen_principal VARCHAR(255),
        categoria_tecnica VARCHAR(100),
        meta_description TEXT,
        tags VARCHAR(255),
        fecha_publicacion DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->query($sql);
    echo "Tabla portfolio creada con éxito.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
