<?php
$message = '';
$error = '';
$configFile = __DIR__ . '/../config/config.php';

// Auto-detect base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script = dirname($_SERVER['SCRIPT_NAME']);
$script = $script === '\\' || $script === '/' ? '' : $script;
$autoUrl = $protocol . "://" . $host . $script . "/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    $app_url = $_POST['app_url'] ?? $autoUrl;
    $type = $_POST['type'] ?? 'clean';

    try {
        if (strpos($db_host, 'http://') !== false || strpos($db_host, 'https://') !== false) {
            throw new Exception("El 'Host de Base de Datos' no debe ser una URL. Normalmente es 'localhost' o una dirección IP.");
        }

        // 1. Probar conexión y crear BD si no existe
        $dsnTemp = "mysql:host=" . $db_host . ";charset=utf8mb4";
        $pdoTemp = new PDO($dsnTemp, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdoTemp->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // 2. Conectar a la BD específica
        $dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // 3. Escribir config.php completo
        $configContent = "<?php\n";
        $configContent .= "/**\n * Archivo de Configuración Principal\n * Generado por el Instalador\n */\n\n";
        $configContent .= "define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);\n\n";
        $configContent .= "define('APP_NAME', 'Catálogo Online');\n";
        $configContent .= "define('APP_URL',  '" . rtrim($app_url, '/') . "/');\n\n";
        $configContent .= "// Base de Datos\n";
        $configContent .= "define('DB_HOST', '" . addslashes($db_host) . "');\n";
        $configContent .= "define('DB_USER', '" . addslashes($db_user) . "');\n";
        $configContent .= "define('DB_PASS', '" . addslashes($db_pass) . "');\n";
        $configContent .= "define('DB_NAME', '" . addslashes($db_name) . "');\n\n";
        
        $configContent .= "// Configuraciones adicionales corporativas\n";
        $configContent .= "define('DEFAULT_TIMEZONE', 'America/Guayaquil');\n";
        $configContent .= "date_default_timezone_set(DEFAULT_TIMEZONE);\n\n";
        
        $configContent .= "// APIs Externas\n";
        $configContent .= "define('IMGBB_API_KEY', '1d3cf0fc76865635d7dc8d26c5863be6');\n\n";
        
        $configContent .= "// Entorno de ejecución\n";
        $configContent .= "define('APP_DEBUG', true);\n\n";
        $configContent .= "if (APP_DEBUG) {\n    error_reporting(E_ALL);\n    ini_set('display_errors', 1);\n} else {\n    error_reporting(0);\n    ini_set('display_errors', 0);\n}\n";

        if (file_put_contents($configFile, $configContent) === false) {
            throw new Exception("No se pudo escribir en el archivo config/config.php. Verifica los permisos de escritura.");
        }

        // 4. Ejecutar SQL Dumps
        $schemaSql = file_get_contents(__DIR__ . '/../database/schema.sql');
        $essentialSql = file_get_contents(__DIR__ . '/../database/essential_data.sql');

        $pdo->exec($schemaSql);
        $pdo->exec($essentialSql);

        if ($type === 'demo') {
            $demoSql = file_get_contents(__DIR__ . '/../database/demo_data.sql');
            $pdo->exec($demoSql);
            $message = "Instalación completada con éxito. Se configuró la base de datos y se cargaron los datos de Demostración.";
        } else {
            $message = "Instalación limpia completada. Se configuró la base de datos y se generó el administrador por defecto.";
        }

        // 5. Actualizar los datos del perfil de la empresa si fueron llenados
        $co_name = $_POST['co_name'] ?? '';
        $co_whatsapp = $_POST['co_whatsapp'] ?? '';
        $co_email = $_POST['co_email'] ?? '';
        $co_address = $_POST['co_address'] ?? '';
        $co_slogan = $_POST['co_slogan'] ?? '';

        if (!empty($co_name)) {
            $stmt = $pdo->prepare("UPDATE company_profile SET
                name = :name,
                whatsapp = :whatsapp,
                phone_whatsapp = :phone_whatsapp,
                email = :email,
                address = :address,
                eslogan = :eslogan
                WHERE id = 1");
            $stmt->execute([
                ':name' => $co_name,
                ':whatsapp' => $co_whatsapp,
                ':phone_whatsapp' => $co_whatsapp,
                ':email' => $co_email,
                ':address' => $co_address,
                ':eslogan' => $co_slogan
            ]);
        }

    } catch (PDOException $e) {
        $error = "Error de Base de Datos: Verifique sus credenciales. Detalle: " . $e->getMessage();
    } catch (Exception $e) {
        $error = "Error del Sistema: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente de Instalación - SisCatalogo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .cursor-pointer {
            cursor: pointer;
            transition: all 0.2s;
        }

        .cursor-pointer:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .border-2 {
            border-width: 2px !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0 fw-bold">🚀 Asistente de Instalación</h3>
                        <p class="mb-0 mt-2 text-white-50">Configura tu conexión y base de datos inicial</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <?php if ($message): ?>
                            <div class="alert alert-success text-center pb-4 pt-4 border-success">
                                <h4 class="alert-heading fw-bold mb-3">¡Instalación Exitosa!</h4>
                                <p class="mb-0"><?= $message ?></p>
                                <hr>
                                <p class="mb-0 text-dark"><strong>Usuario:</strong> admin <br><strong>Contraseña:</strong>
                                    admin</p>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                                <a href="<?= rtrim($_POST['app_url'] ?? $autoUrl, '/') ?>/"
                                    class="btn btn-outline-primary px-4 py-2">Ir a la Tienda</a>
                                <a href="<?= rtrim($_POST['app_url'] ?? $autoUrl, '/') ?>/syslogin"
                                    class="btn btn-primary px-4 py-2">Ir al Panel Admin</a>
                            </div>
                            <div class="alert alert-danger mt-4 text-center fw-bold">
                                ⚠️ Por tu seguridad, ELIMINA el archivo <code>public/install.php</code> del servidor ahora
                                mismo.
                            </div>
                        <?php else: ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger fw-bold border-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form method="POST" id="installForm"
                                onsubmit="return confirm('ATENCIÓN: Si la base de datos ya existe, las tablas serán eliminadas y re-creadas. ¿Continuar?');">

                                <h5 class="fw-bold mb-4 text-primary border-bottom pb-2">1. Configuración del Servidor</h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">URL de la Aplicación (Ruta del catálogo)</label>
                                        <input type="url" name="app_url" class="form-control bg-light"
                                            value="<?= htmlspecialchars($_POST['app_url'] ?? $autoUrl) ?>" required>
                                        <div class="form-text">Ruta web detectada automáticamente. Asegúrate de que termine
                                            en <code>/</code>.</div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-4 text-primary border-bottom pb-2 mt-5">2. Credenciales de la Base de
                                    Datos</h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Host de Base de Datos</label>
                                        <input type="text" name="db_host" class="form-control"
                                            value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nombre de la Base de Datos</label>
                                        <input type="text" name="db_name" class="form-control"
                                            value="<?= htmlspecialchars($_POST['db_name'] ?? 'siscatalogo') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Usuario BD</label>
                                        <input type="text" name="db_user" class="form-control"
                                            value="<?= htmlspecialchars($_POST['db_user'] ?? 'root') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Contraseña BD</label>
                                        <input type="password" name="db_pass" class="form-control">
                                        <div class="form-text">Déjalo en blanco si tu servidor local no usa contraseña.
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-4 text-primary border-bottom pb-2 mt-5">3. Datos del Negocio</h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nombre del Negocio</label>
                                        <input type="text" name="co_name" class="form-control"
                                            placeholder="Ej. Mi Tienda Increíble"
                                            value="<?= htmlspecialchars($_POST['co_name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Eslogan (Breve frase)</label>
                                        <input type="text" name="co_slogan" class="form-control"
                                            placeholder="Ej. Calidad al mejor precio"
                                            value="<?= htmlspecialchars($_POST['co_slogan'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">WhatsApp Principal</label>
                                        <input type="text" name="co_whatsapp" class="form-control"
                                            placeholder="Ej. 593999888777"
                                            value="<?= htmlspecialchars($_POST['co_whatsapp'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Correo Electrónico</label>
                                        <input type="email" name="co_email" class="form-control"
                                            placeholder="Ej. ventas@mitienda.com"
                                            value="<?= htmlspecialchars($_POST['co_email'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Dirección Física</label>
                                        <input type="text" name="co_address" class="form-control"
                                            placeholder="Ej. Av. Central y Calle Secundaria"
                                            value="<?= htmlspecialchars($_POST['co_address'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-4 text-primary border-bottom pb-2 mt-5">4. Tipo de Instalación</h5>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="card h-100 border-primary cursor-pointer border-2 shadow-sm"
                                            onclick="selectCard(this);">
                                            <div class="card-body text-center p-4">
                                                <div class="form-check d-flex justify-content-center mb-3">
                                                    <input class="form-check-input fs-4" type="radio" name="type"
                                                        id="type-clean" value="clean" checked>
                                                </div>
                                                <h5 class="card-title text-primary fw-bold">Instalación Limpia</h5>
                                                <p class="card-text text-muted small mt-2">
                                                    Tablas vacías listas para producción. Solo se inserta el usuario
                                                    <b>admin</b> y las configuraciones base.
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="card h-100 border-success cursor-pointer shadow-sm"
                                            onclick="selectCard(this);">
                                            <div class="card-body text-center p-4">
                                                <div class="form-check d-flex justify-content-center mb-3">
                                                    <input class="form-check-input fs-4" type="radio" name="type"
                                                        id="type-demo" value="demo">
                                                </div>
                                                <h5 class="card-title text-success fw-bold">Instalar con Demos</h5>
                                                <p class="card-text text-muted small mt-2">
                                                    Instalación Limpia + <b>Productos de Ejemplo</b>, Categorías, Portafolio
                                                    y Sliders. Ideal para ver cómo funciona el sistema recién instalado.
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="d-grid mt-5">
                                    <button type="submit" class="btn btn-primary py-3 fw-bold fs-5 shadow text-uppercase">
                                        Conectar e Instalar Sistema
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-4 text-secondary small">
                    Este sistema utiliza PDO. Asegúrese de tener el driver <code>pdo_mysql</code> activo en su servidor
                    PHP.
                </div>
            </div>
        </div>
    </div>
    <script>
        function selectCard(element) {
            document.querySelectorAll('.card.cursor-pointer').forEach(c => c.classList.remove('border-2'));
            element.classList.add('border-2');
            element.querySelector('input[type="radio"]').checked = true;
    }
    </script>
</body>

</html>