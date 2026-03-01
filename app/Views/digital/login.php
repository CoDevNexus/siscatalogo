<?php include BASE_PATH . 'app/Views/layout/header_blank.php'; ?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white text-center py-4 border-0">
                    <h3 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Portal de Diseños</h3>
                    <p class="mb-0 small text-white-50 mt-1">Acceso seguro a tus archivos digitales</p>
                </div>
                <div class="card-body p-4 p-md-5">

                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger shadow-sm rounded-3 py-2 px-3 fw-bold small mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= APP_URL ?>digital/authenticate" method="POST">
                        <div class="mb-4">
                            <label for="digital_user" class="form-label text-muted small fw-bold">Usuario
                                (Email)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="bi bi-person text-secondary"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0 focus-ring" id="digital_user"
                                    name="digital_user" required placeholder="tu@email.com" autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="digital_pass" class="form-label text-muted small fw-bold">Contraseña
                                Segura</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="bi bi-lock text-secondary"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0 focus-ring"
                                    id="digital_pass" name="digital_pass" required placeholder="••••••••">
                                <button class="btn btn-light border border-start-0 text-secondary" type="button"
                                    onclick="const p = document.getElementById('digital_pass'); p.type = p.type === 'password' ? 'text' : 'password';"><i
                                        class="bi bi-eye"></i></button>
                            </div>
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>Revisa tu recibo o correo
                                electrónico para obtener tus credenciales.</div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold shadow-sm">
                                Ingresar al Portal <i class="bi bi-arrow-right-short ms-1 fs-5 align-middle"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light border-0 text-center py-3">
                    <small class="text-muted">&copy;
                        <?= date('Y') ?>
                        <?php echo isset($company['name']) ? htmlspecialchars($company['name']) : 'Portal Digital'; ?>.
                        Todos los derechos reservados.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }

    .focus-ring:focus {
        box-shadow: none;
        border-color: var(--bs-primary);
    }

    .input-group-text {
        border-color: #dee2e6;
    }

    .form-control:focus+.input-group-text {
        border-color: var(--bs-primary);
    }
</style>

<?php include BASE_PATH . 'app/Views/layout/footer_blank.php'; ?>