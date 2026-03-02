<?php
$isEdit    = !empty($role);
$actionUrl = $isEdit ? APP_URL . 'admin/rol_actualizar' : APP_URL . 'admin/rol_guardar';

$moduleLabels = [
    'dashboard'    => '🏠 Dashboard',
    'productos'    => '📦 Productos',
    'pedidos'      => '🛒 Pedidos',
    'categorias'   => '🏷️ Categorías',
    'portfolio'    => '🖼️ Portafolio',
    'digitales'    => '☁️ Entregas Digitales',
    'usuarios'     => '👥 Usuarios',
    'roles'        => '🔐 Roles y Permisos',
    'bitacora'     => '📋 Bitácora',
    'configuracion'=> '⚙️ Configuración',
];

$oldName = htmlspecialchars($old['name'] ?? $role['name'] ?? '');
$oldDesc = htmlspecialchars($old['desc'] ?? $role['description'] ?? '');
$isSystem = (bool)($role['is_system'] ?? false);
?>
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-xl-8">

            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= APP_URL ?>admin/roles">Roles y Permisos</a></li>
                    <li class="breadcrumb-item active"><?= $isEdit ? 'Editar: ' . htmlspecialchars($role['name']) : 'Nuevo Rol' ?></li>
                </ol>
            </nav>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger rounded-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= $actionUrl ?>" method="POST">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= $role['id'] ?>">
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-shield-lock text-danger me-2"></i>
                            <?= $isEdit ? 'Editar Rol' : 'Nuevo Rol' ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Nombre del Rol <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="<?= $oldName ?>"
                                    <?= $isSystem ? 'readonly title="El rol de sistema no puede renombrarse"' : 'required' ?>
                                    placeholder="ej: Operador, Supervisor">
                                <?php if ($isSystem): ?>
                                    <div class="form-text text-warning"><i class="bi bi-lock"></i> Nombre protegido (rol de sistema)</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Descripción</label>
                                <input type="text" name="description" class="form-control"
                                    value="<?= $oldDesc ?>"
                                    placeholder="¿Para qué sirve este rol?">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permisos por módulo -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-check2-square text-success me-2"></i>Permisos</h5>
                        <?php if (!$isSystem): ?>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill" onclick="toggleAll(true)">
                                    <i class="bi bi-check-all"></i> Seleccionar todo
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="toggleAll(false)">
                                    <i class="bi bi-x"></i> Limpiar
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($isSystem): ?>
                            <div class="alert alert-warning rounded-3 mb-3">
                                <i class="bi bi-shield-fill-check me-2"></i>
                                El rol <strong>Admin</strong> tiene <strong>todos los permisos</strong> siempre.
                                Los cambios aquí no afectan su acceso real.
                            </div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <?php foreach ($allPerms as $module => $perms): ?>
                                <div class="col-md-6 col-xl-4">
                                    <div class="border rounded-3 p-3 h-100 bg-light bg-opacity-50">
                                        <h6 class="fw-semibold mb-3 border-bottom pb-2">
                                            <?= $moduleLabels[$module] ?? ucfirst($module) ?>
                                        </h6>
                                        <?php foreach ($perms as $perm): ?>
                                            <?php $checked = in_array($perm['slug'], $assignedSlugs, true); ?>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input perm-check" type="checkbox"
                                                    name="permissions[]"
                                                    value="<?= htmlspecialchars($perm['slug']) ?>"
                                                    id="perm_<?= str_replace('.', '_', $perm['slug']) ?>"
                                                    <?= $checked ? 'checked' : '' ?>
                                                    <?= $isSystem ? 'disabled' : '' ?>>
                                                <label class="form-check-label small"
                                                    for="perm_<?= str_replace('.', '_', $perm['slug']) ?>">
                                                    <?= htmlspecialchars($perm['name']) ?>
                                                    <br><span class="text-muted font-monospace" style="font-size:.7rem"><?= htmlspecialchars($perm['slug']) ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <?php if (!$isSystem): ?>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Guardar cambios' : 'Crear Rol' ?>
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-warning rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i>Guardar (solo permisos en BD)
                        </button>
                    <?php endif; ?>
                    <a href="<?= APP_URL ?>admin/roles" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function toggleAll(state) {
    document.querySelectorAll('.perm-check:not([disabled])').forEach(cb => cb.checked = state);
}
</script>
