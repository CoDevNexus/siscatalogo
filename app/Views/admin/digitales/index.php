<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="h4 mb-0 fw-bold text-gray-800"><i class="bi bi-cloud-arrow-down text-primary me-2"></i>Gestión de
            Entregas Digitales</h2>
        <p class="text-muted small mb-0 mt-1">Historial completo de accesos únicos generados y entregados a los
            clientes.</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small">#ID</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small">CLIENTE / ORDEN</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small">PRODUCTO DIGITAL</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small">CREDENCIALES CREADAS</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small">VENCIMIENTO</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small text-center">DESCARGAS</th>
                        <th class="py-3 px-4 fw-semibold text-muted font-monospace small text-end">Ruta Origen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($accesses)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                <h5 class="text-dark fw-bold">Sin Entregas Digitales</h5>
                                <p class="text-muted small">No se ha aprobado ninguna orden con ítems descargables aún.</p>
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
                            <tr class="<?= !$isActive ? 'bg-light opacity-75' : '' ?>">
                                <td class="px-4 py-3 fw-bold text-secondary">#<?= $acc['id'] ?></td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($acc['customer_name']) ?></div>
                                    <div class="small text-muted"><i
                                            class="bi bi-envelope me-1"></i><?= htmlspecialchars($acc['customer_email']) ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold text-primary"><?= htmlspecialchars($acc['product_name']) ?></div>
                                    <div class="small text-muted">Item Ref: <?= $acc['order_item_id'] ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="form-check form-switch card-body p-0">
                                        <input class="form-check-input update-status" type="checkbox"
                                            data-id="<?= $acc['id'] ?>" <?= $isActive ? 'checked' : '' ?>>
                                        <label class="form-check-label small"><?= $isActive ? 'Activo' : 'Inactivo' ?></label>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($isExpired): ?>
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i
                                                class="bi bi-clock-history me-1"></i>Expirado</span>
                                    <?php else: ?>
                                        <span class="text-dark"><i
                                                class="bi bi-calendar-check text-success me-2"></i><?= date('d M, h:i A', strtotime($acc['expires_at'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="input-group input-group-sm mx-auto" style="max-width: 100px;">
                                        <span class="input-group-text bg-white fw-bold"><?= $acc['downloads_count'] ?> /</span>
                                        <input type="number" class="form-control text-center update-limit"
                                            data-id="<?= $acc['id'] ?>" value="<?= $maxDownloads ?>" min="0"
                                            style="width: 50px;">
                                    </div>
                                    <?php if ($isExceeded && $isActive): ?>
                                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-triangle"></i> Límite
                                            alcanzado</small>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <span class="d-inline-block text-truncate small font-monospace text-muted"
                                        style="max-width: 150px;" title="<?= htmlspecialchars($acc['file_path']) ?>">
                                        <?= htmlspecialchars($acc['file_path']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Actualizar Estado (Activo/Inactivo)
        document.querySelectorAll('.update-status').forEach(el => {
            el.addEventListener('change', function () {
                const id = this.dataset.id;
                const val = this.checked ? 1 : 0;
                const label = this.nextElementSibling;

                label.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                const fd = new FormData();
                fd.append('id', id);
                fd.append('field', 'is_active');
                fd.append('value', val);

                fetch('<?= APP_URL ?>admin/digital_access_update', {
                    method: 'POST',
                    body: fd
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.ok) {
                            label.textContent = val ? 'Activo' : 'Inactivo';
                            this.closest('tr').classList.toggle('bg-light', !val);
                            this.closest('tr').classList.toggle('opacity-75', !val);
                        } else {
                            alert('Error al actualizar');
                            this.checked = !this.checked;
                            label.textContent = this.checked ? 'Activo' : 'Inactivo';
                        }
                    });
            });
        });

        // Actualizar Límite de Descargas
        document.querySelectorAll('.update-limit').forEach(el => {
            el.addEventListener('change', function () {
                const id = this.dataset.id;
                const val = this.value;

                this.classList.add('is-loading');

                const fd = new FormData();
                fd.append('id', id);
                fd.append('field', 'download_limit');
                fd.append('value', val);

                fetch('<?= APP_URL ?>admin/digital_access_update', {
                    method: 'POST',
                    body: fd
                })
                    .then(r => r.json())
                    .then(data => {
                        this.classList.remove('is-loading');
                        if (data.ok) {
                            this.classList.add('border-success');
                            setTimeout(() => this.classList.remove('border-success'), 1000);
                        } else {
                            alert('Error al actualizar límite');
                            location.reload();
                        }
                    });
            });
        });
    });
</script>

<style>
    .is-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .update-limit {
        border-width: 2px transition: border-color 0.3s;
    }
</style>