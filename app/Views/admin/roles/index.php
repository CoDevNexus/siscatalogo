<?php
$moduleLabels = [
    'dashboard' => '🏠 Dashboard',
    'productos' => '📦 Productos',
    'pedidos' => '🛒 Pedidos',
    'categorias' => '🏷 Categorías',
    'portfolio' => '🖼 Portafolio',
    'digitales' => '☁️ Entregas Digitales',
    'usuarios' => '👥 Usuarios',
    'roles' => '🔐 Roles',
    'bitacora' => '📋 Bitácora',
    'configuracion' => '⚙️ Configuración',
];
?>
<div class="container-fluid p-4">

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Roles y Permisos</h4>
            <small class="text-muted">
                <?= count($roles) ?> rol(es) configurado(s)
            </small>
        </div>
        <?php if (\App\Core\Security::can('roles.crear')): ?>
            <a href="<?= APP_URL ?>admin/rol_crear" class="btn btn-danger rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Rol
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php foreach ($roles as $r): ?>
            <div class="col-md-6 col-xl-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 <?= $r['is_system'] ? 'border-start border-4 border-danger' : 'border-start border-4 border-secondary' ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-0">
                                    <?= htmlspecialchars($r['name']) ?>
                                </h5>
                                <?php if ($r['is_system']): ?>
                                    <span class="badge bg-danger rounded-pill mt-1">Sistema</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <?php if (\App\Core\Security::can('roles.gestionar')): ?>
                                    <a href="<?= APP_URL ?>admin/rol_editar/<?= $r['id'] ?>"
                                        class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-pencil-fill"></i> Permisos
                                    </a>
                                <?php endif; ?>
                                <?php if (\App\Core\Security::can('roles.eliminar') && !$r['is_system']): ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill"
                                        onclick="confirmDelete(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['name'])) ?>')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p class="text-muted small mb-3">
                            <?= htmlspecialchars($r['description'] ?: 'Sin descripción') ?>
                        </p>

                        <div class="d-flex gap-3 mt-auto">
                            <div class="text-center">
                                <div class="fw-bold fs-5 text-primary">
                                    <?= $r['user_count'] ?>
                                </div>
                                <div class="text-muted" style="font-size:.75rem">Usuarios</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold fs-5 text-success">
                                    <?= $r['perm_count'] ?>
                                </div>
                                <div class="text-muted" style="font-size:.75rem">Permisos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4 p-3 bg-light rounded-3">
        <h6 class="fw-semibold"><i class="bi bi-info-circle me-2 text-info"></i>Catálogo de permisos disponibles</h6>
        <p class="text-muted small mb-0">
            Los permisos están organizados por módulo (productos, pedidos, usuarios, etc.) y se asignan
            marcando checkboxes en el formulario de cada rol. El rol <strong>Admin (Sistema)</strong>
            siempre tiene acceso a todo — sus permisos no pueden ser reducidos.
        </p>
    </div>
</div>

<!-- Modal eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar Rol
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Seguro que deseas eliminar el rol <strong id="delRoleName"></strong>?
                Asegúrate de que ningún usuario lo tenga asignado antes.
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <a id="delConfirmBtn" href="#" class="btn btn-danger rounded-pill">Eliminar</a>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmDelete(id, name) {
        document.getElementById('delRoleName').textContent = name;
        document.getElementById('delConfirmBtn').href = APP_URL + 'admin/rol_eliminar/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>