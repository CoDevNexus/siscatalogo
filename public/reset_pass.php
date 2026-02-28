<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';

$db = \App\Core\Database::getInstance();
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$db->query("UPDATE users SET password_hash = :hash WHERE username = 'admin'", ['hash' => $hash]);

echo "Contraseña admin123 restablecida exitosamente.";
