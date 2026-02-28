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
                $imgUrls[] = APP_URL . $p['image_url'];
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

<!-- ========= MODAL FICHA DE PRODUCTO ========= -->
<div class="modal fade modal-product" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span id="modal-badge" class="badge rounded-pill bg-success">Artículo Físico</span>
                    <span id="modal-category" class="text-white-50 small"></span>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row g-0">
                    <!-- Columna izquierda: galería -->
                    <div class="col-md-5 p-3 border-end">
                        <img id="modal-main-img" class="gallery-main mb-3" src="" alt="Producto"
                            onerror="this.src='https://placehold.co/400x400/eee/999?text=?'">
                        <div class="gallery-thumbs" id="modal-thumbs"></div>
                    </div>
                    <!-- Columna derecha: info + compra -->
                    <div class="col-md-7 p-4 d-flex flex-column">
                        <h4 id="modal-title" class="fw-bold text-dark mb-1"></h4>
                        <p id="modal-desc" class="text-secondary small mb-3"></p>

                        <!-- Precios -->
                        <div class="mb-3">
                            <div class="price-display mb-1" id="modal-price-display">$0.00</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <small class="text-muted" id="modal-price-unit"></small>
                                <small class="text-danger" id="modal-price-dozen"></small>
                            </div>
                            <div id="modal-price-combo" class="mt-1 badge bg-warning text-dark" style="display:none">
                                Combo</div>
                            <div id="modal-dozen-badge" class="price-badge-dozen mt-1">
                                <i class="bi bi-tag-fill me-1"></i>¡Precio especial por docena aplicado!
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-2">Cantidad</label>
                            <div class="qty-selector">
                                <button type="button" id="qty-minus">−</button>
                                <input type="number" id="modal-qty" value="1" min="1" max="999">
                                <button type="button" id="qty-plus">+</button>
                            </div>
                            <div class="form-text">Añade ≥12 para precio de docena.</div>
                        </div>

                        <!-- Personalización del cliente -->
                        <div id="note-group" class="mb-3" style="display:none">
                            <label class="form-label small fw-bold text-muted">
                                <i class="bi bi-chat-left-text me-1 text-info"></i>Nota de Personalización *
                            </label>
                            <input type="text" id="client-note" class="form-control"
                                placeholder="Ej: Juan García ♥ María, Cumpleaños 2026…">
                        </div>
                        <div id="logo-url-group" class="mb-3" style="display:none">
                            <label class="form-label small fw-bold text-muted">
                                <i class="bi bi-image me-1 text-success"></i>Enlace de tu Logo / Referencia
                            </label>
                            <input type="url" id="client-logo-url" class="form-control"
                                placeholder="https://drive.google.com/…">
                        </div>

                        <div class="mt-auto">
                            <!-- Acciones principales -->
                            <div class="d-grid gap-2 mb-3">
                                <button id="btn-add-to-cart" class="btn-add-modal">
                                    <i class="bi bi-cart-plus me-2"></i>Añadir al Pedido
                                </button>
                                <button id="btn-whatsapp"
                                    class="btn-whatsapp d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-whatsapp fs-5"></i>Consultar por WhatsApp
                                </button>
                            </div>
                            <!-- Compartir -->
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted me-1">Compartir:</small>
                                <button id="btn-share-wa" class="social-share-btn btn-wa" title="WhatsApp"><i
                                        class="bi bi-whatsapp"></i></button>
                                <button id="btn-share-fb" class="social-share-btn btn-fb" title="Facebook"><i
                                        class="bi bi-facebook"></i></button>
                                <button id="btn-share-pi" class="social-share-btn btn-pi" title="Pinterest"><i
                                        class="bi bi-pinterest"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========= PANEL LATERAL DEL CARRITO ========= -->
<div id="cart-overlay"></div>
<div id="cart-panel">
    <div class="cart-panel-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold"><i class="bi bi-cart3 me-2"></i>Mi Pedido</h5>
        <button id="btn-close-cart" class="btn btn-sm btn-outline-light rounded-pill px-3">Cerrar</button>
    </div>
    <div id="cart-items-list"></div>
    <div class="cart-panel-footer">
        <div class="d-flex justify-content-between fw-bold text-dark fs-5 mb-3">
            <span>Total:</span>
            <span id="cart-total-text">$0.00</span>
        </div>
        <div class="d-grid gap-2">
            <button id="btn-checkout" class="btn btn-primary py-2 fw-bold rounded-3">
                <i class="bi bi-wallet2 me-2"></i>Confirmar Pedido y Generar Proforma
            </button>
            <button id="btn-clear-cart" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash3 me-1"></i>Vaciar carrito
            </button>
        </div>
    </div>
</div>

<!-- ========= MODAL CHECKOUT ========= -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="fw-bold"><i class="bi bi-clipboard-check me-2 text-primary"></i>Confirmar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <!-- Resumen -->
                <div class="table-responsive mb-4">
                    <table class="table table-sm align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">P.Unit</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="checkout-summary-body"></tbody>
                    </table>
                    <div class="text-end fw-bold fs-5">
                        Total: <span id="checkout-total-display">$0.00</span>
                    </div>
                </div>
                <hr>
                <form id="checkout-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nombre completo *</label>
                            <input type="text" id="co-name" class="form-control" required
                                placeholder="Ej: Carlos Pérez">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Email *</label>
                            <input type="email" id="co-email" class="form-control" required
                                placeholder="correo@ejemplo.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Ciudad *</label>
                            <input type="text" id="co-city" class="form-control" required placeholder="Guayaquil">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Teléfono / WhatsApp</label>
                            <input type="tel" id="co-phone" class="form-control" placeholder="0987654321">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Dirección <span
                                    class="fw-normal">(opcional)</span></label>
                            <input type="text" id="co-address" class="form-control" placeholder="Calle / Sector">
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold">
                            <i class="bi bi-file-earmark-text me-2"></i>Generar Proforma
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========= MODAL PROFORMA ========= -->
<div class="modal fade" id="proformaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold"><i class="bi bi-file-earmark-check me-2 text-success"></i>Proforma Generada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4" id="proforma-container"></div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button id="btn-download-proforma" class="btn btn-success rounded-pill px-4 fw-bold">
                    <i class="bi bi-download me-2"></i>Descargar como Imagen
                </button>
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                    data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>