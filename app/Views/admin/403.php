<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Acceso Denegado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: #f4f7f6;
        }

        .error-card {
            max-width: 480px;
            margin: 10vh auto;
            background: #fff;
            border-radius: 1rem;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
        }

        .error-icon {
            font-size: 3.5rem;
            color: #dc3545;
        }

        .error-code {
            font-size: 5rem;
            font-weight: 900;
            color: #dee2e6;
            line-height: 1;
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-code">403</div>
        <i class="bi bi-shield-lock-fill error-icon mb-3 d-block"></i>
        <h4 class="fw-bold mb-2">Acceso Denegado</h4>
        <p class="text-muted mb-4">
            <?= isset($msg) ? $msg : 'No tienes permisos suficientes para acceder a esta sección.' ?>
        </p>
        <a href="<?= defined('APP_URL') ? APP_URL . 'admin' : '/' ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
        </a>
    </div>
</body>

</html>