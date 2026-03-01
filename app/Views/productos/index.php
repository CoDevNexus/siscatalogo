<!-- ========= CATÁLOGO PRINCIPAL ========= -->

<!-- Hero -->
<div class="hero-section text-center">
    <div class="container position-relative">
        <span class="badge bg-danger mb-3 px-3 py-2 rounded-pill fw-normal fs-6">⚡ Nuevos Diseños 2026</span>
        <h1 class="display-4 fw-bold text-dark mb-3">Eleva tu Marca con Corte Láser</h1>
        <p class="lead text-secondary mx-auto mb-4" style="max-width:580px;">
            Personalizamos MDF, acrílico y ofrecemos los mejores vectores digitales listos para tus propias máquinas de
            corte.
        </p>
        <!-- Búsqueda -->
        <form method="GET" action="" class="mx-auto" style="max-width:440px">
            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-0 bg-white"
                    placeholder="Buscar llavero, caja, vector..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
        </form>
    </div>
</div>

<!-- Filtros -->
<div class="d-flex justify-content-center mb-5 gap-2 flex-wrap px-2">
    <a href="<?= APP_URL ?>"
        class="filter-btn text-decoration-none <?= (!$filterCat && !$filterType) ? 'active' : '' ?>">
        Ver Todos
    </a>
    <a href="<?= APP_URL ?>?tipo=fisico"
        class="filter-btn text-decoration-none <?= $filterType === 'fisico' ? 'active' : '' ?>">
        <i class="bi bi-box-seam me-1"></i>Físicos
    </a>
    <a href="<?= APP_URL ?>?tipo=digital"
        class="filter-btn text-decoration-none <?= $filterType === 'digital' ? 'active' : '' ?>">
        <i class="bi bi-cloud-arrow-down me-1"></i>Digitales
    </a>
    <?php foreach ($categorias as $cat): ?>
        <a href="<?= APP_URL ?>?cat=<?= $cat['id'] ?>"
            class="filter-btn text-decoration-none <?= $filterCat == $cat['id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php
// Cargar galería de imágenes para cada producto
$db = \App\Core\Database::getInstance();
$allImgs = $db->fetchAll("SELECT product_id, image_path, source, is_primary FROM product_images ORDER BY product_id, is_primary DESC, sort_order ASC");
$imgsByProduct = [];
foreach ($allImgs as $img) {
    $imgsByProduct[$img['product_id']][] = $img;
}
?>

<!-- Grid de productos -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-5">
    <?php if (empty($productos)): ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
            <h5>No hay productos en esta categoría aún.</h5>
        </div>
    <?php else: ?>
        <?php foreach ($productos as $p):
            // Construir array de URLs de imágenes para este producto
            $pImgs = $imgsByProduct[$p['id']] ?? [];
            $imgUrls = [];
            foreach ($pImgs as $pi) {
                $imgUrls[] = ($pi['source'] === 'local') ? APP_URL . $pi['image_path'] : $pi['image_path'];
            }
            // Imagen principal (fallback a image_url antiguo)
            if (empty($imgUrls) && !empty($p['image_url'])) {
                $isExternal = (strpos($p['image_url'], 'http') === 0);
                $imgUrls[] = $isExternal ? $p['image_url'] : APP_URL . $p['image_url'];
            }
            $coverImg = $imgUrls[0] ?? null;

            // JSON para el modal
            $productData = json_encode([
                'id' => $p['id'],
                'name' => $p['name'],
                'description' => $p['description'] ?? '',
                'price_unit' => $p['price_unit'],
                'price_dozen' => $p['price_dozen'],
                'price_combo' => $p['price_combo'] ?? 0,
                'is_digital' => (bool) $p['is_digital'],
                'allow_note' => !empty($p['allow_client_note']),
                'allow_logo' => !empty($p['allow_client_logo']),
                'category' => $p['category_name'] ?? '',
                'images' => $imgUrls,
            ]);
            ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm product-card"
                    onclick='openProductModal(<?= htmlspecialchars($productData, ENT_QUOTES) ?>)'>
                    <div class="product-img-wrapper">
                        <?php if ($p['is_digital']): ?>
                            <span class="badge badge-premium badge-digital position-absolute top-0 start-0 m-3" style="z-index:2">
                                <i class="bi bi-download me-1"></i>Digital
                            </span>
                        <?php else: ?>
                            <span class="badge badge-premium position-absolute top-0 start-0 m-3" style="z-index:2">Físico</span>
                        <?php endif; ?>

                        <?php if ($coverImg): ?>
                            <img src="<?= htmlspecialchars($coverImg) ?>"
                                onerror="this.src='https://placehold.co/400x400/eee/999?text=?'"
                                alt="<?= htmlspecialchars($p['name']) ?>">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center"
                                style="position:absolute;inset:0;background:#f4f5f9;">
                                <i class="bi <?= $p['is_digital'] ? 'bi-file-earmark-code' : 'bi-box' ?> text-muted fs-1"></i>
                            </div>
                        <?php endif; ?>

                        <?php if (count($imgUrls) > 1): ?>
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-60 rounded-pill"
                                style="font-size:.7rem;z-index:2">
                                <i class="bi bi-images me-1"></i><?= count($imgUrls) ?> fotos
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="text-muted small mb-1"><?= htmlspecialchars($p['category_name'] ?? '') ?></div>
                        <h6 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($p['name']) ?></h6>
                        <?php if (!empty($p['description'])): ?>
                            <p class="card-text text-secondary small mb-2" style="line-height:1.4">
                                <?= htmlspecialchars(mb_substr($p['description'], 0, 65)) . (mb_strlen($p['description']) > 65 ? '…' : '') ?>
                            </p>
                        <?php endif; ?>
                        <div class="mt-auto pt-2 border-top border-light d-flex justify-content-between align-items-end">
                            <div>
                                <span class="fw-bold fs-5 <?= $p['is_digital'] ? 'text-primary' : 'text-dark' ?>">
                                    $<?= number_format($p['price_unit'], 2) ?>
                                </span>
                                <small class="text-muted">/und</small>
                                <?php if (!$p['is_digital'] && $p['price_dozen'] > 0): ?>
                                    <br><small class="text-danger fw-semibold">
                                        <i class="bi bi-tag-fill"></i> $<?= number_format($p['price_dozen'], 2) ?>/doc
                                    </small>
                                <?php endif; ?>
                            </div>
                            <button
                                class="btn <?= $p['is_digital'] ? 'btn-primary' : 'btn-dark' ?> rounded-circle p-0 shadow-sm"
                                style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;"
                                onclick='event.stopPropagation();openProductModal(<?= htmlspecialchars($productData, ENT_QUOTES) ?>)'
                                title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ========= MODALES Y SCRIPTS COMPARTIDOS ========= -->
<?php include BASE_PATH . 'app/Views/productos/modals.php'; ?>