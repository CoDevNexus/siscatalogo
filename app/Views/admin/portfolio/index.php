<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0"><i class="bi bi-briefcase me-2 text-primary"></i>Gestión de Portafolio</h2>
            <p class="text-muted small">Administra tus Casos de Éxito y trabajos destacados.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="<?= APP_URL ?>admin/portfolio_nuevo" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Caso de Éxito
            </a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3" style="width: 80px;">ID</th>
                            <th>Proyecto</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay casos de éxito publicados aún.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="ps-4 text-muted fw-bold">#
                                        <?= $item['id'] ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['imagen_principal']): ?>
                                                <?php $imgSrc = (strpos($item['imagen_principal'], 'http') === 0) ? $item['imagen_principal'] : APP_URL . $item['imagen_principal']; ?>
                                                <img src="<?= $imgSrc ?>" class="rounded-3 me-3"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded-3 me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold">
                                                    <?= htmlspecialchars($item['titulo']) ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?= htmlspecialchars($item['slug']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-soft-info text-info border border-info border-opacity-25 py-1 px-3 rounded-pill">
                                            <?= htmlspecialchars($item['categoria_tecnica'] ?: 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('d/m/Y', strtotime($item['fecha_publicacion'])) ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                            <a href="<?= APP_URL ?>caso-de-exito/<?= $item['slug'] ?>" target="_blank"
                                                class="btn btn-sm btn-white border" title="Ver en Portal">
                                                <i class="bi bi-eye text-primary"></i>
                                            </a>
                                            <a href="<?= APP_URL ?>admin/portfolio_editar/<?= $item['id'] ?>"
                                                class="btn btn-sm btn-white border" title="Editar">
                                                <i class="bi bi-pencil text-success"></i>
                                            </a>
                                            <button class="btn btn-sm btn-white border"
                                                onclick="eliminarItem(<?= $item['id'] ?>, '<?= addslashes($item['titulo']) ?>')"
                                                title="Eliminar">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function eliminarItem(id, titulo) {
        Swal.fire({
            title: '¿Eliminar Caso de Éxito?',
            text: `Se borrará "${titulo}". Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= APP_URL ?>admin/portfolio_eliminar/${id}`;
            }
        });
    }
</script>

<style>
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .btn-white:hover {
        background-color: #f8f9fa;
    }
</style>