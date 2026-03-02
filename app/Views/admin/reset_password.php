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

        .auth-alert-invalid {
            background: rgba(239, 68, 68, .08);
            border: 1px solid rgba(239, 68, 68, .2);
            color: #fca5a5;
            border-radius: 10px;
            padding: 20px 18px;
            text-align: center;
            font-size: .9rem;
        }

        .strength-bar {
            height: 4px;
            border-radius: 4px;
            margin-top: 6px;
            background: var(--border);
            overflow: hidden;
        }

        .strength-bar-fill {
            height: 100%;
            width: 0;
            border-radius: 4px;
            transition: width .3s, background .3s;
        }

        .strength-hint {
            font-size: .75rem;
            color: var(--text-muted);
            margin-top: 4px;
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
            <i class="bi bi-key-fill"></i>
        </div>
        <div class="auth-title">Nueva contraseña</div>

        <?php if (!($valid ?? false)): ?>
            <!-- Token inválido o expirado -->
            <div class="auth-alert-invalid">
                <i class="bi bi-x-octagon-fill d-block fs-2 mb-2"></i>
                <strong>Enlace inválido o expirado</strong><br>
                <span style="color:var(--text-muted); font-size:.83rem;">
                    <?= htmlspecialchars($error ?? 'Este enlace ya no es válido.') ?>
                </span>
            </div>
            <div class="divider"></div>
            <div class="text-center">
                <a href="<?= APP_URL ?>syslogin/forgot" class="auth-link">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Solicitar nuevo enlace
                </a>
            </div>
        <?php else: ?>
            <div class="auth-subtitle">Elige una contraseña segura de al menos 8 caracteres</div>

            <?php if (!empty($error)): ?>
                <div class="auth-alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>syslogin/reset_save" method="POST" id="resetForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="reset_token" value="<?= htmlspecialchars($token ?? '') ?>">

                <div class="mb-3">
                    <label for="password" class="form-label">Nueva contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8"
                            placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                    </div>
                    <div class="strength-bar mt-2">
                        <div class="strength-bar-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-hint" id="strengthHint">Escribe tu nueva contraseña</div>
                </div>

                <div class="mb-4">
                    <label for="password_confirm" class="form-label">Confirmar contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required
                            minlength="8" placeholder="Repite la contraseña" autocomplete="new-password">
                    </div>
                    <div class="strength-hint" id="matchHint"></div>
                </div>

                <button type="submit" class="btn-auth" id="submitBtn">
                    <i class="bi bi-check-lg me-2"></i>Guardar nueva contraseña
                </button>
            </form>

            <div class="divider"></div>
            <div class="text-center">
                <a href="<?= APP_URL ?>syslogin" class="auth-link">
                    <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        (function () {
            const pwd = document.getElementById('password');
            const cfm = document.getElementById('password_confirm');
            const fill = document.getElementById('strengthFill');
            const hint = document.getElementById('strengthHint');
            const mHint = document.getElementById('matchHint');
            const btn = document.getElementById('submitBtn');

            if (!pwd) return;

            function calcStrength(p) {
                let s = 0;
                if (p.length >= 8) s++;
                if (p.length >= 12) s++;
                if (/[A-Z]/.test(p)) s++;
                if (/[0-9]/.test(p)) s++;
                if (/[^A-Za-z0-9]/.test(p)) s++;
                return s;
            }

            const levels = [
                { w: '0%', bg: 'transparent', label: 'Escribe tu nueva contraseña' },
                { w: '25%', bg: '#ef4444', label: 'Muy débil' },
                { w: '50%', bg: '#f97316', label: 'Débil' },
                { w: '75%', bg: '#eab308', label: 'Aceptable' },
                { w: '90%', bg: '#22c55e', label: 'Buena' },
                { w: '100%', bg: '#6366f1', label: 'Excelente 💪' },
            ];

            pwd.addEventListener('input', function () {
                const s = Math.min(calcStrength(this.value), 5);
                fill.style.width = levels[s].w;
                fill.style.background = levels[s].bg;
                hint.textContent = levels[s].label;
                hint.style.color = levels[s].bg || '#94a3b8';
                checkMatch();
            });

            function checkMatch() {
                if (!cfm.value) { mHint.textContent = ''; return; }
                if (pwd.value === cfm.value) {
                    mHint.textContent = '✓ Las contraseñas coinciden';
                    mHint.style.color = '#22c55e';
                } else {
                    mHint.textContent = '✗ No coinciden';
                    mHint.style.color = '#ef4444';
                }
            }

            cfm.addEventListener('input', checkMatch);
        })();
    </script>
</body>

</html>