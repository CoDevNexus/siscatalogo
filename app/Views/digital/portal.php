<?php include BASE_PATH . 'app/Views/layout/header_blank.php'; ?>

<div class="container py-5 mt-4">
    <!-- Header del Portal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="bi bi-cloud-arrow-down text-primary me-2"></i>Mis Diseños
                Digitales</h2>
            <p class="text-muted mb-0">Bienvenido/a, <strong>
                    <?= htmlspecialchars($customer_name) ?>
                </strong></p>
        </div>
        <a href="<?= APP_URL ?>digital/logout" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
        </a>
    </div>

    <!-- Lista de Diseños -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small">PRODUCTO</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small">VENCIMIENTO</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small text-center">DESCARGAS</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small text-end">ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($accesses)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 opacity-50 mb-3 d-block"></i>
                                <h5 class="fw-bold">Sin archivos digitales</h5>
                                <p class="mb-0 small">No tienes diseños aprobados o han caducado en su totalidad.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($accesses as $acc):
                            $maxDownloads = $acc['download_limit'] ?? 3;
                            $now = date('Y-m-d H:i:s');
                            $isExpired = ($acc['expires_at'] < $now);
                            $isExceeded = ($acc['downloads_count'] >= $maxDownloads);
                            $isActive = (isset($acc['is_active']) ? (bool) $acc['is_active'] : true);
                            $isValid = ($isActive && !$isExpired && !$isExceeded);
                            ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-3"
                                            style="width: 45px; height: 45px;">
                                            <i class="bi bi-file-earmark-zip fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">
                                                <?= htmlspecialchars($acc['product_name']) ?>
                                            </h6>
                                            <small class="text-muted">Orden #<?= htmlspecialchars($acc['order_id']) ?> -
                                                <?= date('d/m/Y', strtotime($acc['order_created_at'])) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($isExpired): ?>
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i
                                                class="bi bi-clock-history me-1"></i>Expirado</span>
                                    <?php else: ?>
                                        <span class="text-dark"><i class="bi bi-calendar-check text-success me-2"></i>
                                            <?= date('d M Y, h:i A', strtotime($acc['expires_at'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($isExceeded): ?>
                                        <span class="badge bg-warning text-dark"><i
                                                class="bi bi-exclamation-triangle me-1"></i>Límite alcanzado</span>
                                    <?php else: ?>
                                        <span class="fw-bold <?= $acc['downloads_count'] > 0 ? 'text-primary' : 'text-muted' ?>">
                                            <?= $acc['downloads_count'] ?>
                                        </span> <span class="text-muted small">/
                                            <?= $maxDownloads ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <?php if ($isValid): ?>
                                        <a href="<?= APP_URL ?>digital/download/<?= htmlspecialchars($acc['download_token']) ?>"
                                            class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">
                                            <i class="bi bi-download me-1"></i>Descargar
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm rounded-pill px-3 opacity-50 pe-none" disabled>
                                            <i class="bi bi-lock me-1"></i>
                                            <?php 
                                                if (!$isActive) echo "Bloqueado por Admin";
                                                elseif ($isExpired) echo "Expirado";
                                                else echo "Límite alcanzado";
                                            ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
        transition: all 0.2s ease;
    }
</style>

<?php include BASE_PATH . 'app/Views/layout/footer_blank.php'; ?>