<!-- ========= HERO SLIDER ========= -->
<div id="heroCarousel" class="carousel slide carousel-fade mb-5 shadow-sm rounded-4 overflow-hidden"
    data-bs-ride="carousel">
    <?php if (!empty($slides)): ?>
        <div class="carousel-indicators">
            <?php foreach ($slides as $index => $slide): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>"
                    class="<?= $index === 0 ? 'active' : '' ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($slides as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" style="height: 450px;">
                    <img src="<?= htmlspecialchars($slide['image_url']) ?>" class="d-block w-100 h-100 object-fit-cover"
                        alt="Slide">
                    <!-- Responsive Carousel Caption: Visible on all screens -->
                    <div class="carousel-caption d-block text-start pb-4 pb-md-5 mb-2 mb-md-4 px-3 px-md-0">
                        <!-- Custom CSS inline background para asegurar legibilidad en móviles -->
                        <div class="p-3 p-md-0 rounded">
                            <?php if (!empty($slide['badge'])): ?>
                                <span
                                    class="badge bg-danger mb-2 mb-md-3 px-2 py-1 px-md-3 py-md-2 rounded-pill fw-normal fs-7 fs-md-6">
                                    <?= htmlspecialchars($slide['badge']) ?>
                                </span>
                            <?php endif; ?>

                            <!-- Responsive Title: Smaller on mobile, display-3 on desktop -->
                            <div class="fw-bold mb-2 mb-md-3 text-white slider-title-responsive">
                                <?= $slide['title'] ?>
                            </div>

                            <!-- Responsive Subtitle -->
                            <?php if (!empty($slide['subtitle'])): ?>
                                <div class="mb-3 mb-md-4 opacity-90 text-white slider-subtitle-responsive">
                                    <?= $slide['subtitle'] ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                                <a href="<?= APP_URL . htmlspecialchars($slide['button_link']) ?>"
                                    class="btn btn-primary btn-sm btn-md-lg rounded-pill px-3 py-1 px-md-4 py-md-2 fw-bold shadow mt-2">
                                    <?= htmlspecialchars($slide['button_text']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($slides) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        <?php endif; ?>
    <?php else: ?>
        <div class="p-5 text-center bg-light">
            <h4 class="text-muted">No hay slides activos configurados</h4>
        </div>
    <?php endif; ?>
</div>

<!-- ========= BUSCADOR COMPACTO ========= -->
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <form method="GET" action="<?= APP_URL ?>productos"
                class="search-compact shadow-sm rounded-pill p-1 bg-white d-flex border">
                <input type="text" name="q" class="form-control border-0 bg-transparent ps-4 py-2"
                    placeholder="¿Qué estás buscando hoy? (ej. llaveros, tazas...)" required>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ========= PRODUCTOS DESTACADOS (2 FILAS DE 3) ========= -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold mb-0"><?= htmlspecialchars($settings['featured_title'] ?? 'Productos Destacados') ?></h2>
            <p class="text-muted small mb-0">
                <?= htmlspecialchars($settings['featured_desc'] ?? 'Nuestros artículos más populares y recientes.') ?>
            </p>
        </div>
        <a href="<?= APP_URL ?>productos" class="btn btn-outline-primary rounded-pill px-4">
            Ver Todo el Catálogo <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <?php
    // Inyectar galería de imágenes para los destacados
    $db = \App\Core\Database::getInstance();
    $featuredIds = array_column($productos, 'id');
    $imgsByProduct = [];
    if (!empty($featuredIds)) {
        $idsStr = implode(',', $featuredIds);
        $allImgs = $db->fetchAll("SELECT product_id, image_path, source, is_primary FROM product_images WHERE product_id IN ($idsStr) ORDER BY is_primary DESC, sort_order ASC");
        foreach ($allImgs as $img) {
            $imgsByProduct[$img['product_id']][] = $img;
        }
    }
    ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($productos as $p):
            $pImgs = $imgsByProduct[$p['id']] ?? [];
            $imgUrls = [];
            foreach ($pImgs as $pi) {
                $imgUrls[] = ($pi['source'] === 'local') ? APP_URL . $pi['image_path'] : $pi['image_path'];
            }
            if (empty($imgUrls) && !empty($p['image_url'])) {
                $isExternal = (strpos($p['image_url'], 'http') === 0);
                $imgUrls[] = $isExternal ? $p['image_url'] : APP_URL . $p['image_url'];
            }
            $coverImg = $imgUrls[0] ?? 'https://placehold.co/400x400/eee/999?text=?';

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
                <div class="card h-100 border-0 shadow-sm product-card transition-hover"
                    onclick='openProductModal(<?= htmlspecialchars($productData, ENT_QUOTES) ?>)'>
                    <div class="position-relative overflow-hidden rounded-4" style="aspect-ratio: 1/1;">
                        <img src="<?= htmlspecialchars($coverImg) ?>" class="w-100 h-100 object-fit-cover p-img"
                            alt="<?= htmlspecialchars($p['name']) ?>">
                        <span
                            class="badge position-absolute top-0 start-0 m-3 <?= $p['is_digital'] ? 'bg-primary' : 'bg-dark' ?>">
                            <?= $p['is_digital'] ? 'Digital' : 'Físico' ?>
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <small class="text-uppercase tracking-wider text-muted fw-bold"
                            style="font-size: 0.65rem;"><?= htmlspecialchars($p['category_name'] ?? 'General') ?></small>
                        <h6 class="fw-bold text-dark mt-1 mb-2"><?= htmlspecialchars($p['name']) ?></h6>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="h5 fw-bold text-primary mb-0">$<?= number_format($p['price_unit'], 2) ?></span>
                            <button class="btn btn-light btn-sm rounded-circle"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ========= CASOS DE ÉXITO (SÓLO 3) ========= -->
<section class="bg-light py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold mb-0"><?= htmlspecialchars($settings['portfolio_title'] ?? 'Casos de Éxito') ?></h2>
                <p class="text-muted small mb-0">
                    <?= htmlspecialchars($settings['portfolio_desc'] ?? 'Vea cómo hemos ayudado a elevar marcas reales.') ?>
                </p>
            </div>
            <a href="<?= APP_URL ?>portafolio" class="btn btn-outline-dark rounded-pill px-4">
                Ver Todo el Portafolio <i class="bi bi-grid ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($casos as $caso): ?>
                <div class="col">
                    <div
                        class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden portfolio-card transition-hover bg-white">
                        <div class="position-relative" style="height: 240px;">
                            <img src="<?= \App\Services\ImageService::buildUrl($caso['imagen_principal'], 'url') ?>"
                                class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($caso['titulo']) ?>">
                            <div class="portfolio-overlay">
                                <a href="<?= APP_URL ?>caso-de-exito/<?= $caso['slug'] ?>"
                                    class="btn btn-white rounded-pill px-4 shadow">Ver Detalles</a>
                            </div>
                        </div>
                        <div class="card-body p-4 text-center bg-white">
                            <span
                                class="badge bg-soft-primary text-primary rounded-pill px-3 mb-2"><?= htmlspecialchars($caso['categoria_tecnica'] ?? 'Trabajo Especial') ?></span>
                            <h5 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($caso['titulo']) ?></h5>
                            <p class="text-secondary small mb-0"><?= htmlspecialchars($caso['intro_corta']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Reuse Modals and Scripts from Layout (they are usually in header/footer) -->
<?php include BASE_PATH . 'app/Views/productos/modals.php'; ?>

<style>
    .transition-hover {
        transition: transform 0.3s ease, shadow 0.3s ease;
        cursor: pointer;
    }

    .transition-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .1) !important;
    }

    .p-img {
        transition: transform 0.5s ease;
    }

    .product-card:hover .p-img {
        transform: scale(1.08);
    }

    .object-fit-cover {
        object-fit: cover;
    }

    .bg-soft-primary {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }

    .search-compact input:focus {
        box-shadow: none;
        outline: none;
    }

    .portfolio-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.3s;
    }

    .portfolio-card:hover .portfolio-overlay {
        opacity: 1;
    }

    .btn-white {
        background: white;
        color: black;
        border: 0;
    }

    .btn-white:hover {
        background: #f8f9fa;
    }
</style>