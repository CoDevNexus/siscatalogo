<?php
$moduleColors = [
    'usuarios' => 'primary',
    'productos' => 'warning',
    'bitacora' => 'info',
    'login' => 'success',
    'categorias' => 'secondary',
    'portfolio' => 'info',
    '' => 'dark',
];
$badgeColor = fn($mod) => $moduleColors[$mod] ?? 'dark';
?>
<div class="container-fluid p-4">

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-journal-text text-info me-2"></i>Bitácora de Actividad</h4>
            <small class="text-muted">
                <?= number_format($pagination['total_items']) ?> registro(s) total
            </small>
        </div>
        <?php if (\App\Core\Security::can('configuracion.editar')): ?>
            <button
                onclick="if(confirm('¿Eliminar todos los registros de más de 90 días?'))location.href='<?= APP_URL ?>admin/bitacora_limpiar'"
                class="btn btn-outline-danger rounded-pill px-3">
                <i class="bi bi-trash3 me-1"></i>Limpiar >90 días
            </button>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="<?= APP_URL ?>admin/bitacora" class="row g-2 align-items-end">
                <div class="col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Módulo</label>
                    <select name="module" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach ($modules as $mod): ?>
                            <option value="<?= htmlspecialchars($mod) ?>" <?= ($filters['module'] === $mod) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($mod)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Usuario</label>
                    <input type="text" name="username" class="form-control form-control-sm"
                        placeholder="Nombre de usuario" value="<?= htmlspecialchars($filters['username']) ?>">
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Acción</label>
                    <input type="text" name="action" class="form-control form-control-sm"
                        placeholder="ej: creado, login..." value="<?= htmlspecialchars($filters['action']) ?>">
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Desde</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($filters['date_from']) ?>">
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Hasta</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($filters['date_to']) ?>">
                </div>
                <div class="col-sm-6 col-md-3 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 flex-fill">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="<?= APP_URL ?>admin/bitacora" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="width:155px">Fecha</th>
                            <th style="width:110px">Usuario</th>
                            <th style="width:110px">Módulo</th>
                            <th>Acción</th>
                            <th>Detalle</th>
                            <th class="pe-4" style="width:120px">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-2 d-block mb-2"></i>No hay registros que coincidan con los
                                    filtros.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="ps-4 text-muted">
                                        <div>
                                            <?= date('d/m/Y', strtotime($log['created_at'])) ?>
                                        </div>
                                        <small class="text-secondary">
                                            <?= date('H:i:s', strtotime($log['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">
                                            <?= htmlspecialchars($log['username']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php $mod = $log['module'] ?: '—'; ?>
                                        <span
                                            class="badge bg-<?= $badgeColor($log['module']) ?> bg-opacity-75 rounded-pill px-2">
                                            <?= htmlspecialchars(ucfirst($mod)) ?>
                                        </span>
                                    </td>
                                    <td class="fw-medium">
                                        <?= htmlspecialchars($log['action']) ?>
                                    </td>
                                    <td class="text-muted"
                                        style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                                        title="<?= htmlspecialchars($log['detail'] ?? '') ?>">
                                        <?= htmlspecialchars($log['detail'] ?? '—') ?>
                                    </td>
                                    <td class="pe-4 text-muted font-monospace small">
                                        <?= htmlspecialchars($log['ip_address'] ?: '—') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <nav class="mt-3 d-flex justify-content-center">
            <ul class="pagination pagination-sm mb-0">
                <?php
                $q = $filters;
                for ($p = 1; $p <= $pagination['total_pages']; $p++):
                    $q['page'] = $p;
                    $isActive = $p === $pagination['current_page'];
                    ?>
                    <li class="page-item <?= $isActive ? 'active' : '' ?>">
                        <a class="page-link rounded" href="<?= APP_URL ?>admin/bitacora?<?= http_build_query($q) ?>">
                            <?= $p ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <div class="text-center text-muted small mt-2">
            Mostrando página
            <?= $pagination['current_page'] ?> de
            <?= $pagination['total_pages'] ?>
            (
            <?= number_format($pagination['total_items']) ?> registros)
        </div>
    <?php endif; ?>

</div>