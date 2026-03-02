<?php
// Colores de rol dinámico: sistema=rojo, viewer=gris, resto=azul
$roleBadge = fn($u) => match (true) {
    (bool) ($u['is_system'] ?? false) => 'danger',
    strtolower($u['role_name'] ?? '') === 'viewer' => 'secondary',
    default => 'primary'
};

function userInitials(string $username): string
{
    $parts = preg_split('/[\s_\-\.]+/', strtoupper($username));
    return substr($parts[0] ?? '?', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : substr($parts[0] ?? '?', 1, 1));
}
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
            <h4 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Gestión de Usuarios</h4>
            <small class="text-muted">
                <?= count($users) ?> usuario(s) registrado(s)
            </small>
        </div>
        <?php if (\App\Core\Security::can('usuarios.crear')): ?>
            <a href="<?= APP_URL ?>admin/usuario_crear" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario
            </a>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3">Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registrado</th>
                            <?php if (\App\Core\Security::can('usuarios.editar') || \App\Core\Security::can('usuarios.eliminar')): ?>
                                <th class="pe-4 text-end">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-2 d-block mb-2"></i>No hay usuarios registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <?php $isSelf = ((int) ($_SESSION['user_id'] ?? 0) === (int) $u['id']); ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold me-3"
                                                style="width:38px;height:38px;font-size:.85rem;flex-shrink:0">
                                                <?= userInitials($u['username']) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars($u['username']) ?>
                                                </div>
                                                <?php if ($isSelf): ?>
                                                    <small class="text-success"><i class="bi bi-person-check-fill"></i> Sesión
                                                        actual</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        <?= htmlspecialchars($u['email'] ?: '—') ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $roleBadge($u) ?> rounded-pill px-3"
                                            title="<?= htmlspecialchars($u['role_description'] ?? '') ?>">
                                            <?= htmlspecialchars($u['role_name'] ?? ucfirst($u['role'])) ?>
                                        </span>
                                        <?php if ($u['is_system'] ?? false): ?>
                                            <small class="text-muted ms-1"><i class="bi bi-shield-fill"
                                                    title="Rol de sistema"></i></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                                    </td>
                                    <?php if (\App\Core\Security::can('usuarios.editar') || \App\Core\Security::can('usuarios.eliminar')): ?>
                                        <td class="pe-4 text-end">
                                            <?php if (\App\Core\Security::can('usuarios.editar')): ?>
                                                <a href="<?= APP_URL ?>admin/usuario_editar/<?= $u['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary rounded-pill me-1">
                                                    <i class="bi bi-pencil-fill"></i> Editar
                                                </a>
                                            <?php endif; ?>

                                            <?php if (\App\Core\Security::can('usuarios.eliminar')): ?>
                                                <?php if (!$isSelf): ?>
                                                    <button class="btn btn-sm btn-outline-danger rounded-pill"
                                                        onclick="confirmDelete(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['username'])) ?>')">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" disabled
                                                        title="No puedes eliminarte a ti mismo">
                                                        <i class="bi bi-lock-fill"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 text-muted small">
        <i class="bi bi-info-circle me-1"></i>
        Los roles se configuran en <a href="<?= APP_URL ?>admin/roles">Roles y Permisos</a>.
        El rol <strong>Admin</strong> tiene acceso completo. Los demás roles respetan los permisos asignados.
    </div>
</div>

<!-- Modal confirmación eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar
                    Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar al usuario <strong id="deleteUsername"></strong>? Esta acción no se
                puede deshacer.
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger rounded-pill"><i
                        class="bi bi-trash me-1"></i>Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, username) {
        document.getElementById('deleteUsername').textContent = username;
        document.getElementById('deleteConfirmBtn').href = APP_URL + 'admin/usuario_eliminar/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>