<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0"><i class="bi bi-tags me-2 text-primary"></i>Gestión de Categorías</h2>
            <p class="text-muted small">Organiza tus productos por categorías y tipos.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="openCategoryModal()">
                <i class="bi bi-plus-lg me-1"></i>Nueva Categoría
            </button>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Nombre</th>
                            <th>Slug</th>
                            <th>Tipo</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $category): ?>
                            <tr>
                                <td class="ps-4 fw-bold">
                                    <?= htmlspecialchars($category['name']) ?>
                                </td>
                                <td><code><?= htmlspecialchars($category['slug']) ?></code></td>
                                <td>
                                    <?php $badgeColor = ($category['type'] === 'digital') ? 'bg-soft-primary text-primary' : 'bg-soft-success text-success'; ?>
                                    <span class="badge <?= $badgeColor ?> rounded-pill px-3">
                                        <?= ucfirst(htmlspecialchars($category['type'])) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border rounded-circle me-1" title="Editar"
                                        onclick="openCategoryModal(<?= $category['id'] ?>, '<?= addslashes($category['name']) ?>', '<?= $category['type'] ?>')">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border rounded-circle text-danger" title="Eliminar"
                                        onclick="confirmDelete(<?= $category['id'] ?>, '<?= addslashes($category['name']) ?>')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar categoría -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= APP_URL ?>admin/categoria_guardar" method="POST">
                <input type="hidden" name="id" id="cat_id">
                <div class="modal-header border-0 p-4">
                    <h5 class="fw-bold mb-0" id="modalTitle">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" name="name" id="cat_name"
                            class="form-control bg-light border-0 p-3 rounded-3" placeholder="Ej. Tazas Personalizadas"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tipo</label>
                        <select name="type" id="cat_type" class="form-select bg-light border-0 p-3 rounded-3">
                            <option value="físico">Producto Físico</option>
                            <option value="digital">Producto Digital</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSubmit">Crear
                        Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    let categoryModalInstance = null;

    function openCategoryModal(id = null, name = '', type = 'físico') {
        // Inicializar el modal si aún no lo está
        if (!categoryModalInstance) {
            categoryModalInstance = new bootstrap.Modal(document.getElementById('categoryModal'));
        }

        document.getElementById('cat_id').value = id || '';
        document.getElementById('cat_name').value = name;
        document.getElementById('cat_type').value = type;
        
        document.getElementById('modalTitle').innerText = id ? 'Editar Categoría' : 'Nueva Categoría';
        document.getElementById('btnSubmit').innerText = id ? 'Guardar Cambios' : 'Crear Categoría';
        
        categoryModalInstance.show();
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            html: `<div class="p-3 bg-light rounded-3 mb-3 border"><b>${name}</b></div>
                   <p class="text-muted small mb-0">Solo se podrá eliminar si no tiene productos asociados.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef233c',
            cancelButtonColor: '#adb5bd',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-4 border-0 shadow' }
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = `<?= APP_URL ?>admin/categoria_eliminar/${id}`;
            }
        });
    }
</script>

<style>
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .bg-soft-primary {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }

    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .table thead th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>