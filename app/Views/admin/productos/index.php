<?php
$pg = $pagination;
$search = $pg['search'];
$sort = $pg['sort'];
$dir = $pg['dir'];
$currentPage = $pg['current_page'];
$totalPages = $pg['total_pages'];

// Función helper para generar URLs de ordenación
$buildSortUrl = function ($col) use ($pg) {
    $newDir = ($pg['sort'] === $col && $pg['dir'] === 'ASC') ? 'DESC' : 'ASC';
    return APP_URL . "admin/productos?p=1&s=" . urlencode($pg['search']) . "&sort=$col&dir=$newDir";
};

// Función helper para iconos de ordenación
$sortIcon = function ($col) use ($sort, $dir) {
    if ($sort !== $col)
        return '<i class="bi bi-arrow-down-up ms-1 text-muted small"></i>';
    return $dir === 'ASC'
        ? '<i class="bi bi-sort-alpha-down ms-1 text-primary"></i>'
        : '<i class="bi bi-sort-alpha-up-alt ms-1 text-primary"></i>';
};
?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show"><i
            class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close"
            data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- Encabezado y buscador -->
<div class="row g-3 align-items-center mb-4">
    <div class="col-md-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2 text-primary"></i>Inventario</h5>
        <small class="text-muted"><?= $pg['total_items'] ?> producto(s) en total</small>
    </div>
    <div class="col-md-5">
        <form action="<?= APP_URL ?>admin/productos" method="GET" class="position-relative">
            <input type="hidden" name="sort" value="<?= $sort ?>">
            <input type="hidden" name="dir" value="<?= $dir ?>">
            <input type="text" name="s" class="form-control rounded-pill ps-4 shadow-sm"
                placeholder="Buscar por nombre, categoría, estado..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary position-absolute end-0 top-0 h-100 rounded-pill px-4">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    <div class="col-md-3 text-end">
        <?php if (\App\Core\Security::can('productos.crear')): ?>
            <a href="<?= APP_URL ?>admin/producto_crear" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nuevo
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Tabla de productos -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4" style="width:100px">Portada</th>
                        <th><a href="<?= $buildSortUrl('p.name') ?>" class="text-white text-decoration-none">Nombre
                                <?= $sortIcon('p.name') ?></a></th>
                        <th><a href="<?= $buildSortUrl('category_name') ?>"
                                class="text-white text-decoration-none">Categoría <?= $sortIcon('category_name') ?></a>
                        </th>
                        <th><a href="<?= $buildSortUrl('p.price_unit') ?>" class="text-white text-decoration-none">P.
                                Unit <?= $sortIcon('p.price_unit') ?></a></th>
                        <th><a href="<?= $buildSortUrl('p.is_digital') ?>" class="text-white text-decoration-none">Tipo
                                <?= $sortIcon('p.is_digital') ?></a></th>
                        <th><a href="<?= $buildSortUrl('p.status') ?>" class="text-white text-decoration-none">Estado
                                <?= $sortIcon('p.status') ?></a></th>
                        <?php if (\App\Core\Security::can('productos.editar') || \App\Core\Security::can('productos.eliminar')): ?>
                            <th class="text-end pe-4">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-search fs-1 d-block mb-2"></i>
                                No se encontraron productos coincidentes.
                                <?php if (!empty($search)): ?>
                                    <br><a href="<?= APP_URL ?>admin/productos" class="small">Limpiar filtros</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="product-preview-box">
                                        <?php if (!empty($p['image_url'])):
                                            $isExternal = str_starts_with($p['image_url'], 'http');
                                            $imgUrl = $isExternal ? $p['image_url'] : APP_URL . $p['image_url'];
                                            ?>
                                            <img src="<?= $imgUrl ?>" onerror="this.src='https://placehold.co/50x50/eee/999?text=?'"
                                                class="rounded-3 border shadow-sm" style="width:50px;height:50px;object-fit:cover;">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center bg-light border rounded-3 text-muted"
                                                style="width:50px;height:50px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></div>
                                    <small class="text-muted" style="font-size:0.75rem"><?= $p['slug'] ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?= htmlspecialchars($p['category_name'] ?? '—') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">$<?= number_format($p['price_unit'], 2) ?></div>
                                    <?php if ($p['price_dozen'] > 0): ?>
                                        <small class="text-success" style="font-size:0.7rem">Doc:
                                            $<?= number_format($p['price_dozen'], 2) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['is_digital']): ?>
                                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary px-2">
                                            <i class="bi bi-cloud-download me-1"></i>Digital
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success px-2">
                                            <i class="bi bi-box me-1"></i>Físico
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $statusColor = ($p['status'] === 'active') ? 'bg-success' : 'bg-danger'; ?>
                                    <span class="badge <?= $statusColor ?> rounded-circle p-1 me-1"></span>
                                    <small
                                        class="fw-semibold <?= ($p['status'] === 'active') ? 'text-success' : 'text-danger' ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </small>
                                </td>
                                <?php if (\App\Core\Security::can('productos.editar') || \App\Core\Security::can('productos.eliminar')): ?>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle shadow-sm" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <?php if (\App\Core\Security::can('productos.editar')): ?>
                                                    <li><a class="dropdown-item py-2"
                                                            href="<?= APP_URL ?>admin/producto_editar/<?= $p['id'] ?>">
                                                            <i class="bi bi-pencil me-2 text-primary"></i> Editar Detalle</a></li>
                                                <?php endif; ?>

                                                <?php if (\App\Core\Security::can('productos.editar') && \App\Core\Security::can('productos.eliminar')): ?>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                <?php endif; ?>

                                                <?php if (\App\Core\Security::can('productos.eliminar')): ?>
                                                    <li><button class="dropdown-item py-2 text-danger"
                                                            onclick="confirmarEliminar(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>')">
                                                            <i class="bi bi-trash3 me-2"></i> Eliminar</button></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Paginación Footer -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white py-3 border-0">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0 gap-1">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link rounded-circle border-0 shadow-sm mx-1"
                                href="<?= APP_URL ?>admin/productos?p=<?= $currentPage - 1 ?>&s=<?= urlencode($search) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
                            <a class="page-link rounded-circle border-0 shadow-sm mx-1 <?= ($i === $currentPage) ? 'bg-primary text-white' : 'bg-light text-dark' ?>"
                                href="<?= APP_URL ?>admin/productos?p=<?= $i ?>&s=<?= urlencode($search) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link rounded-circle border-0 shadow-sm mx-1"
                                href="<?= APP_URL ?>admin/productos?p=<?= $currentPage + 1 ?>&s=<?= urlencode($search) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- SweetAlert2 Confirm Eliminar -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: '¿Eliminar producto?',
            html: `<div class="p-3 bg-light rounded-3 mb-3 border"><b>${nombre}</b></div>
                   <p class="text-muted small mb-0">Esta acción no se puede deshacer y eliminará todas las imágenes de galería asociadas.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef233c',
            cancelButtonColor: '#adb5bd',
            confirmButtonText: 'Sí, eliminar permanentemente',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-4 border-0 shadow' }
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = `<?= APP_URL ?>admin/producto_eliminar/${id}`;
            }
        });
    }
</script>

<style>
    .table thead th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 15px 10px;
        border: none;
    }

    .pagination .page-link {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-item.active .page-link {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .product-preview-box img {
        transition: transform 0.2s;
    }

    .product-preview-box img:hover {
        transform: scale(1.1);
        z-index: 10;
        cursor: pointer;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.03);
    }
</style>