<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?>
    </title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .honeypot {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <!-- Icono genérico en lugar de logo evidente -->
            <div class="d-inline-block bg-dark text-white rounded-circle p-3 mb-3">
                <i class="bi bi-shield-lock-fill fs-2"></i>
            </div>
            <h4 class="fw-bold">Acceso al Sistema</h4>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center small py-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="<?= APP_URL ?>syslogin/auth" method="POST">
            <!-- Token CSRF Obligatorio -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <!-- Campo Honeypot Oculto (Trampa para bots) -->
            <input type="text" name="website_url" class="honeypot" tabindex="-1" autocomplete="off">

            <div class="mb-3">
                <label for="username" class="form-label text-muted small fw-bold">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control border-start-0 bg-light" id="username" name="username"
                        required autocomplete="off">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label text-muted small fw-bold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                    <input type="password" class="form-control border-start-0 bg-light" id="password" name="password"
                        required>
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100 py-2 fw-bold">Ingresar</button>
            <div class="text-center mt-3">
                <a href="<?= APP_URL ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i>
                    Volver al Catálogo</a>
            </div>
        </form>
    </div>
</body>

</html>