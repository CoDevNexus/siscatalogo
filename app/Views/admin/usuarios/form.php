<?php
// Modo edición o creación
$isEdit    = !empty($user);
$oldVal    = fn($key, $default = '') => htmlspecialchars(($old[$key] ?? ($user[$key] ?? $default)));
$actionUrl = $isEdit ? APP_URL . 'admin/usuario_actualizar' : APP_URL . 'admin/usuario_guardar';
?>
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= APP_URL ?>admin/usuarios">Usuarios</a></li>
                    <li class="breadcrumb-item active"><?= $isEdit ? 'Editar' : 'Nuevo Usuario' ?></li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'person-plus-fill' ?> text-primary me-2"></i>
                        <?= $isEdit ? 'Editar Usuario' : 'Nuevo Usuario' ?>
                    </h5>
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger rounded-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= $actionUrl ?>" method="POST" novalidate>
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre de usuario <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control"
                                    value="<?= $oldVal('username') ?>" required
                                    placeholder="admin2, operador, etc."
                                    autocomplete="username">
                            </div>
                            <div class="form-text">Sin espacios. Solo letras, números y guiones.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control"
                                    value="<?= $oldVal('email') ?>" required
                                    placeholder="correo@ejemplo.com"
                                    autocomplete="email">
                            </div>
                            <div class="form-text">Se usará para recuperar la contraseña.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                            <?php
                            require_once BASE_PATH . 'app/Models/RoleModel.php';
                            $_roles = (new \App\Models\RoleModel())->getAll();
                            $currentRoleId = (int)($old['role_id'] ?? $user['role_id'] ?? 0);
                            ?>
                            <select name="role_id" class="form-select" required>
                                <option value="">--- Seleccionar rol ---</option>
                                <?php foreach ($_roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"
                                        <?= ($currentRoleId === (int)$r['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['name']) ?>
                                        <?= $r['is_system'] ? '(Sistema)' : '' ?>
                                        &mdash; <?= $r['perm_count'] ?> permisos
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                <a href="<?= APP_URL ?>admin/roles" target="_blank">
                                    <i class="bi bi-box-arrow-up-right"></i> Gestionar roles
                                </a>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-lock-fill text-warning me-2"></i>
                            Contraseña<?= $isEdit ? ' <small class="text-muted fw-normal fs-7">(dejar en blanco para no cambiar)</small>' : '' ?>
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <?= $isEdit ? 'Nueva contraseña' : 'Contraseña' ?>
                                    <?= !$isEdit ? '<span class="text-danger">*</span>' : '' ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Mínimo 8 caracteres"
                                        <?= !$isEdit ? 'required' : '' ?>
                                        autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePass('password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar contraseña<?= !$isEdit ? '<span class="text-danger">*</span>' : '' ?></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" name="password_confirm" id="password_confirm" class="form-control"
                                        placeholder="Repetir contraseña"
                                        <?= !$isEdit ? 'required' : '' ?>
                                        autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePass('password_confirm', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="passwordMatchMsg" class="form-text mt-1"></div>

                        <div class="d-flex gap-2 mt-4 pt-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-check-lg me-1"></i>
                                <?= $isEdit ? 'Guardar cambios' : 'Crear usuario' ?>
                            </button>
                            <a href="<?= APP_URL ?>admin/usuarios" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-x-lg me-1"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

const p1 = document.getElementById('password');
const p2 = document.getElementById('password_confirm');
const msg = document.getElementById('passwordMatchMsg');

[p1, p2].forEach(el => el?.addEventListener('input', function() {
    if (!p1.value && !p2.value) { msg.textContent = ''; return; }
    if (p1.value === p2.value) {
        msg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Las contraseñas coinciden.</span>';
    } else {
        msg.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Las contraseñas no coinciden.</span>';
    }
}));
</script>
