<?php
// Cargar las imágenes de galería para todos los productos en un solo query
$db = \App\Core\Database::getInstance();
$allImages = $db->fetchAll("SELECT * FROM product_images ORDER BY product_id, sort_order ASC, is_primary DESC");

// Indexar por product_id para acceso rápido en el loop
$imagesByProduct = [];
foreach ($allImages as $img) {
    $imagesByProduct[$img['product_id']][] = $img;
}
?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show"><i
            class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close"
            data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- Encabezado y botón nuevo -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2 text-primary"></i>Inventario</h5>
        <small class="text-muted"><?= count($productos) ?> producto(s) registrado(s)</small>
    </div>
    <a href="<?= APP_URL ?>admin/producto_crear" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
    </a>
</div>

<!-- Tabla de productos -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:180px">Imágenes</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Unit.</th>
                        <th>Precio Doc.</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No hay productos aún.
                                <a href="<?= APP_URL ?>admin/producto_crear" class="d-block mt-2">Crear el primero</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $p):
                            $imgs = $imagesByProduct[$p['id']] ?? [];
                            ?>
                            <tr>
                                <!-- Columna de imágenes en miniatura -->
                                <td class="py-2">
                                    <?php if (!empty($imgs)): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($imgs as $img):
                                                $imgUrl = ($img['source'] === 'local')
                                                    ? APP_URL . $img['image_path']
                                                    : $img['image_path'];
                                                $badgeClass = match ($img['source']) {
                                                    'api' => 'bg-info',
                                                    'url' => 'bg-secondary',
                                                    default => 'bg-success',
                                                };
                                                $badgeLabel = match ($img['source']) {
                                                    'api' => 'BB',
                                                    'url' => 'URL',
                                                    default => 'L',
                                                };
                                                ?>
                                                <div class="position-relative" style="width:44px;height:44px;"
                                                    title="<?= $img['is_primary'] ? '⭐ Portada · ' : '' ?><?= ucfirst($img['source']) ?>: <?= htmlspecialchars(basename($img['image_path'])) ?>">
                                                    <img src="<?= $imgUrl ?>"
                                                        onerror="this.src='https://placehold.co/44x44/eee/999?text=?'"
                                                        class="rounded border <?= $img['is_primary'] ? 'border-warning border-2' : '' ?>"
                                                        style="width:44px;height:44px;object-fit:cover;">
                                                    <span class="position-absolute bottom-0 end-0 badge <?= $badgeClass ?> p-0"
                                                        style="font-size:8px;width:14px;height:14px;display:flex;align-items:center;justify-content:center;border-radius:3px;">
                                                        <?= $badgeLabel ?>
                                                    </span>
                                                    <?php if ($img['is_primary']): ?>
                                                        <span class="position-absolute top-0 start-0"
                                                            style="font-size:10px;line-height:1;color:#f6c90e;text-shadow:0 0 2px rgba(0,0,0,.5);">★</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted"><?= count($imgs) ?>/5
                                                imagen<?= count($imgs) > 1 ? 'es' : '' ?></small>
                                        </div>
                                    <?php elseif (!empty($p['image_url'])): ?>
                                        <!-- Imagen legada (cargada antes del sistema de galería) -->
                                        <div class="position-relative" style="width:44px;height:44px;" title="Imagen directa">
                                            <img src="<?= APP_URL . htmlspecialchars($p['image_url']) ?>"
                                                onerror="this.src='https://placehold.co/44x44/eee/999?text=?'"
                                                class="rounded border" style="width:44px;height:44px;object-fit:cover;">
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center bg-light border rounded text-muted"
                                            style="width:44px;height:44px;" title="Sin imagen">
                                            <i class="bi bi-image small"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($p['category_name'] ?? '—') ?>
                                    </span>
                                </td>
                                <td class="fw-bold">$<?= number_format($p['price_unit'], 2) ?></td>
                                <td class="text-muted">
                                    <?= $p['price_dozen'] > 0 ? '$' . number_format($p['price_dozen'], 2) : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td>
                                    <?php if ($p['is_digital']): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary">
                                            <i class="bi bi-cloud-download me-1"></i>Digital
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success">
                                            <i class="bi bi-box me-1"></i>Físico
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['status'] === 'active'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <?php $imgCount = count($imgs); ?>
                                    <a href="<?= APP_URL ?>admin/producto_editar/<?= $p['id'] ?>"
                                        class="btn btn-sm btn-outline-secondary me-1" title="Editar + Ver galería">
                                        <i class="bi bi-pencil"></i>
                                        <?php if ($imgCount < 5): ?>
                                            <span class="badge bg-warning text-dark ms-1"
                                                title="Puede agregar más imágenes">+</span>
                                        <?php endif; ?>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar producto"
                                        onclick="confirmarEliminar(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Leyenda de badges de fuente -->
<div class="mt-3 d-flex gap-3 flex-wrap">
    <small class="text-muted d-flex align-items-center gap-1">
        <span class="badge bg-success p-1" style="font-size:8px;">L</span> Local (WebP comprimido)
    </small>
    <small class="text-muted d-flex align-items-center gap-1">
        <span class="badge bg-secondary p-1" style="font-size:8px;">URL</span> URL externa
    </small>
    <small class="text-muted d-flex align-items-center gap-1">
        <span class="badge bg-info p-1" style="font-size:8px;">BB</span> ImgBB CDN
    </small>
    <small class="text-muted d-flex align-items-center gap-1">
        <span style="color:#f6c90e;">★</span> Imagen de portada del catálogo
    </small>
</div>

<!-- SweetAlert2 Confirm Eliminar -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: '¿Eliminar producto?',
            html: `<b>${nombre}</b><br><small class="text-muted">Se eliminarán también todas las imágenes asociadas.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef233c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash3"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = `<?= APP_URL ?>admin/producto_eliminar/${id}`;
            }
        });
    }
</script>