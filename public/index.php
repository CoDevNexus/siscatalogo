<?php
// Front Controller

// Iniciar sesión para el carrito de compras y autenticación
session_start();

// Cargar configuración global
require_once dirname(__DIR__) . '/config/config.php';

// Autocarga de clases PSR-4 básica (para el espacio de nombres App\)
spl_autoload_register(function ($className) {
    // Convertir App\Core\Router a app/Core/Router.php
    $classPath = str_replace('App\\', 'app\\', $className);
    $file = BASE_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $classPath) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Inicializar y despachar el Router
$router = new \App\Core\Router();
$router->dispatch($_GET['url'] ?? '');
