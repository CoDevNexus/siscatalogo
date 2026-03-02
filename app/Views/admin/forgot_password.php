<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($title) ?> —
        <?= defined('APP_NAME') ? APP_NAME : 'Admin' ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --auth-bg: #0f172a;
            --card-bg: #1e293b;
            --accent: #6366f1;
            --accent-h: #4f46e5;
            --text-muted: #94a3b8;
            --border: #334155;
            --input-bg: #0f172a;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            background: var(--auth-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px 36px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, .5);
            animation: slideUp .35s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent), var(--accent-h));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.6rem;
            color: #fff;
        }

        .auth-title {
            color: #f1f5f9;
            font-size: 1.3rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 6px;
        }

        .auth-subtitle {
            color: var(--text-muted);
            font-size: .85rem;
            text-align: center;
            margin-bottom: 28px;
        }

        .form-label {
            color: #cbd5e1;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .4px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .input-group-text {
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-right: none;
            color: var(--text-muted);
        }

        .form-control {
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-left: none;
            color: #f1f5f9;
            padding: .6rem .9rem;
            transition: border-color .2s;
        }

        .form-control:focus {
            background: var(--input-bg);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .2);
            color: #f1f5f9;
            outline: none;
        }

        .form-control::placeholder {
            color: #475569;
        }

        .btn-auth {
            background: linear-gradient(135deg, var(--accent), var(--accent-h));
            color: #fff;
            border: none;
            padding: .75rem;
            font-weight: 700;
            border-radius: 8px;
            font-size: .95rem;
            width: 100%;
            transition: opacity .2s, transform .1s;
            margin-top: 4px;
        }

        .btn-auth:hover {
            opacity: .9;
            color: #fff;
        }

        .btn-auth:active {
            transform: scale(.99);
        }

        .auth-alert-danger {
            background: rgba(239, 68, 68, .12);
            border: 1px solid rgba(239, 68, 68, .3);
            color: #fca5a5;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: .85rem;
            margin-bottom: 18px;
        }

        .auth-alert-success {
            background: rgba(34, 197, 94, .12);
            border: 1px solid rgba(34, 197, 94, .3);
            color: #86efac;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: .88rem;
            margin-bottom: 18px;
        }

        .auth-link {
            color: var(--text-muted);
            font-size: .82rem;
            text-decoration: none;
            transition: color .2s;
        }

        .auth-link:hover {
            color: var(--accent);
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 22px 0;
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="auth-icon">
            <i class="bi bi-envelope-at"></i>
        </div>
        <div class="auth-title">Recuperar contraseña</div>
        <div class="auth-subtitle">Ingresa tu correo o nombre de usuario registrado</div>

        <?php if (!empty($error)): ?>
            <div class="auth-alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="auth-alert-success">
                <i class="bi bi-check-circle-fill me-1"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php else: ?>
            <form action="<?= APP_URL ?>syslogin/forgot_send" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="mb-4">
                    <label for="email" class="form-label">Correo electrónico o Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                        <input type="text" class="form-control" id="email" name="email" required
                            placeholder="correo@ejemplo.com o: admin" autocomplete="username">
                    </div>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="bi bi-send me-2"></i>Enviar enlace de recuperación
                </button>
            </form>
        <?php endif; ?>

        <div class="divider"></div>
        <div class="text-center">
            <a href="<?= APP_URL ?>syslogin" class="auth-link">
                <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
            </a>
        </div>
    </div>
</body>

</html>