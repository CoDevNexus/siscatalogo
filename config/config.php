<?php
/**
 * Archivo de Configuración Principal
 * Sistema de Catálogo y Gestión
 */

// Definir constante base del sistema
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Autodescubrimiento de URL base para portabilidad (raíz o subcarpeta)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];

// Calculando el directorio en el que nos encontramos relativo al DocumentRoot
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Limpiar '/public' de la ruta base detectada para que los assets apunten a la raíz virtual
if (strpos($scriptDir, '/public') !== false) {
    $scriptDir = str_replace('/public', '', $scriptDir);
}

// Limpiar $scriptDir para proyectos en raíz directa
$baseFolder = ($scriptDir === '/') ? '' : $scriptDir;

// Concatenar el path descubierto
$appUrl = $protocol . $domainName . $baseFolder;

// Si deseamos forzar "/" al final del APP_URL
if (substr($appUrl, -1) !== '/') {
    $appUrl .= '/';
}

define('APP_URL', $appUrl);

// Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'siscatalogo');

// Configuraciones adicionales corporativas
define('APP_NAME', 'Catálogo Láser');
define('DEFAULT_TIMEZONE', 'America/Guayaquil');

date_default_timezone_set(DEFAULT_TIMEZONE);

// =====================================================
// APIs Externas
// =====================================================

// ImgBB — CDN gratuito de imágenes.
// Obtén tu API KEY en: https://api.imgbb.com/
// Pega tu key entre las comillas. Dejar vacío para deshabilitar ImgBB.
define('IMGBB_API_KEY', '1d3cf0fc76865635d7dc8d26c5863be6');

// =====================================================
// Entorno de ejecución
// =====================================================
// Cambiar a 0 en producción para ocultar errores al usuario final.
define('APP_DEBUG', true);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
