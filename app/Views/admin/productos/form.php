<?php
$isEdit = isset($producto);
$action = $isEdit ? APP_URL . 'admin/producto_actualizar' : APP_URL . 'admin/producto_guardar';
$imgbbEnabled = defined('IMGBB_API_KEY') && !empty(IMGBB_API_KEY);
?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= $error ?></div>
<?php endif; ?>


<div class="d-flex align-items-center mb-4 gap-3">
    <a href="<?= APP_URL ?>admin/productos" class="btn btn-sm btn-outline-secondary rounded-circle"
        style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="fw-bold mb-0"><?= $isEdit ? 'Editar Producto' : 'Nuevo Producto' ?></h5>
</div>

<form action="<?= $action ?>" method="POST" enctype="multipart/form-data" id="productoForm">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $producto['id'] ?>">
    <?php endif; ?>

    <div class="row g-4">
        <!-- Columna principal (izquierda) -->
        <div class="col-lg-8">

            <!-- CARD: Info básica -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold"><i class="bi bi-tag me-2 text-primary"></i>Información del Producto</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nombre del producto *</label>
                        <input type="text" name="name" class="form-control form-control-lg" required
                            value="<?= htmlspecialchars($producto['name'] ?? '') ?>"
                            placeholder="Ej: Caja de Vino Grabada con Láser">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Descripción</label>
                        <textarea name="description" class="form-control" rows="4"
                            placeholder="Describe el producto para el catálogo..."><?= htmlspecialchars($producto['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- CARD: Precios -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold"><i class="bi bi-currency-dollar me-2 text-success"></i>Precios</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Precio Unidad *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price_unit" class="form-control" required
                                    value="<?= $producto['price_unit'] ?? '0.00' ?>">
                            </div>
                        </div>
                        <div class="col-md-4" id="price-dozen-wrapper">
                            <label class="form-label small fw-bold text-muted">Precio Docena</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price_dozen" class="form-control"
                                    value="<?= $producto['price_dozen'] ?? '0.00' ?>">
                            </div>
                            <div class="form-text">0 = sin precio especial por 12 unidades.</div>
                        </div>
                        <div class="col-md-4" id="price-combo-wrapper">
                            <label class="form-label small fw-bold text-muted">Precio Combo <span
                                    class="text-muted">(opc.)</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price_combo" class="form-control"
                                    value="<?= $producto['price_combo'] ?? '' ?>" placeholder="—">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD: Galería de Imágenes -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold"><i class="bi bi-images me-2 text-warning"></i>Galería de Imágenes <small
                            class="text-muted fw-normal">(máx 5)</small></h6>
                </div>
                <div class="card-body px-4 pb-4">

                    <!-- Imágenes existentes (modo edición) -->
                    <?php if (!empty($imagenes)): ?>
                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-2 d-block">Gestión de Galería (Arrastra para
                                reordenar)</label>
                            <div class="row g-2" id="gallery-sort">
                                <?php foreach ($imagenes as $img): ?>
                                    <?php
                                    $imgUrl = (\App\Services\ImageService::buildUrl($img['image_path'], $img['source']));
                                    ?>
                                    <div class="col-4 col-md-2" data-img-id="<?= $img['id'] ?>">
                                        <div class="gallery-thumb position-relative border rounded-3 overflow-hidden <?= $img['is_primary'] ? 'border-primary border-2 shadow-sm' : '' ?>"
                                            style="height: 80px; cursor: grab;">
                                            <img src="<?= $imgUrl ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                            <div class="gallery-actions position-absolute bottom-0 start-0 end-0 d-flex justify-content-between p-1"
                                                style="background:rgba(0,0,0,.6)">
                                                <button type="button" class="btn btn-xs text-warning p-0 lh-1 btn-set-primary"
                                                    data-img="<?= $img['id'] ?>" title="Marcar como Principal"
                                                    style="font-size:13px;">
                                                    <i class="bi <?= $img['is_primary'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs text-danger p-0 lh-1 btn-del-img"
                                                    data-img="<?= $img['id'] ?>" title="Eliminar" style="font-size:13px;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <?php if ($img['is_primary']): ?>
                                                <span class="position-absolute top-0 start-0 badge bg-primary m-1"
                                                    style="font-size:8px;">PRINCIPAL</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- OPCIONES DE CARGA -->
                    <div class="card border-0 bg-light rounded-4 mb-4">
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <button type="button"
                                        class="btn btn-white w-100 border py-3 rounded-4 shadow-sm h-100"
                                        onclick="document.getElementById('inputImages').click()">
                                        <i class="bi bi-pc-display fs-4 text-primary d-block mb-1"></i>
                                        <span class="small fw-bold">Subir Local</span>
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button"
                                        class="btn btn-white w-100 border py-3 rounded-4 shadow-sm h-100"
                                        onclick="openImageBrowser()">
                                        <i class="bi bi-folder2-open fs-4 text-info d-block mb-1"></i>
                                        <span class="small fw-bold">Explorar Galería</span>
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button"
                                        class="btn btn-white w-100 border py-3 rounded-4 shadow-sm h-100"
                                        onclick="document.getElementById('inputImgBB').click()">
                                        <i class="bi bi-cloud-arrow-up fs-4 text-warning d-block mb-1"></i>
                                        <span class="small fw-bold">Subir a ImgBB</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Inputs ocultos -->
                            <input type="file" name="images[]" id="inputImages" class="d-none" multiple
                                accept="image/*">
                            <input type="file" name="imgbb_uploads[]" id="inputImgBB" class="d-none" multiple
                                accept="image/*">
                        </div>
                    </div>

                    <!-- Previsualización Unificada -->
                    <div id="selection-preview" class="row g-2 mb-3"></div>

                    <!-- Contenedor de URLs y Selección de Galería -->
                    <div id="selected-gallery-container"></div>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <hr class="flex-grow-1"><span class="text-muted small">o añadir URL directa</span>
                        <hr class="flex-grow-1">
                    </div>
                    <div id="url-container">
                        <div class="input-group mb-2 shadow-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" name="img_urls[]" class="form-control border-start-0"
                                placeholder="https://ejemplo.com/imagen.jpg">
                            <button type="button" class="btn btn-light border btn-add-url" title="Añadir otra URL"><i
                                    class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD: Archivo Digital (solo si is_digital) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" id="digital-file-card" style="display:none;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold"><i class="bi bi-file-earmark-code me-2 text-info"></i>Archivo Digital Protegido
                    </h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <?php if ($isEdit && !empty($producto['digital_file_path'])): ?>
                        <div class="alert alert-info small py-2 mb-3">
                            <i class="bi bi-check-circle me-1"></i>
                            Archivo actual: <code><?= basename($producto['digital_file_path']) ?></code>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="digital_file" class="form-control" accept=".dxf,.svg,.ai,.pdf,.png,.zip">
                    <div class="form-text">Formatos: DXF, SVG, AI, PDF, PNG, ZIP — máx 50 MB. Se guarda en zona
                        protegida.</div>
                </div>
            </div>

        </div>

        <!-- Columna lateral (derecha) -->
        <div class="col-lg-4">

            <!-- CARD: Publicar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-send me-2 text-primary"></i>Publicación</h6>
                    <label class="form-label small fw-bold text-muted">Estado</label>
                    <select name="status" class="form-select mb-3">
                        <option value="active" <?= (($producto['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>
                            Activo (Visible en catálogo)</option>
                        <option value="inactive" <?= (($producto['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>
                            Inactivo (Oculto)</option>
                    </select>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3">
                        <i class="bi bi-check-circle me-2"></i><?= $isEdit ? 'Actualizar' : 'Guardar Producto' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="<?= APP_URL ?>admin/productos" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- CARD: Clasificación -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-3 me-2 text-secondary"></i>Clasificación
                    </h6>
                    <label class="form-label small fw-bold text-muted">Categoría *</label>
                    <select name="category_id" class="form-select mb-3" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= isset($producto['category_id']) && $producto['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                                (<?= $cat['type'] === 'digital' ? 'Digital' : 'Físico' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="isDigital" name="is_digital"
                            <?= (!empty($producto['is_digital'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="isDigital">
                            <i class="bi bi-cloud-download text-primary me-1"></i> Producto Digital
                        </label>
                        <div class="form-text">Si es digital, ocultará precio por docena.</div>
                    </div>
                </div>
            </div>

            <!-- CARD: Personalización del cliente -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i
                            class="bi bi-person-badge me-2 text-warning"></i>Personalización del Cliente</h6>
                    <p class="small text-muted">Activa los campos que aparecen al cliente en el catálogo:</p>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="allowNote"
                            name="allow_client_note" <?= !empty($producto['allow_client_note']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="allowNote">
                            <i class="bi bi-chat-left-text me-1 text-info"></i> Nota del cliente
                        </label>
                        <div class="form-text">Ej: "Nombre que deseas grabar"</div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="allowLogo"
                            name="allow_client_logo" <?= !empty($producto['allow_client_logo']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="allowLogo">
                            <i class="bi bi-image me-1 text-success"></i> Enlace de logo del cliente
                        </label>
                        <div class="form-text">El cliente pega un link a su logo (Google Drive, etc.)</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<!-- SortableJS para reordenar imágenes -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script>
    // ===== ESTADO GLOBAL =====
    const MAX_IMAGES = 5;
    let selectedFiles = []; // Para subida local
    let imgbbFiles = [];    // Para subida ImgBB
    let reusedImages = [];  // Para imágenes de la galería existente
    let imgBrowserModal = null; // Se inicializará luego

    const previewGrid = document.getElementById('selection-preview');
    const galleryContainer = document.getElementById('selected-gallery-container');

    // ===== CARGA INICIAL =====
    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar modal solo si bootstrap existe
        const modalEl = document.getElementById('imgBrowserModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            imgBrowserModal = new bootstrap.Modal(modalEl);
        }

        // ===== GALERÍA SORTABLE =====
        const gallerySort = document.getElementById('gallery-sort');
        if (gallerySort && typeof Sortable !== 'undefined') {
            Sortable.create(gallerySort, {
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: function () {
                    const items = gallerySort.querySelectorAll('[data-img-id]');
                    items.forEach((el, i) => {
                        const id = el.dataset.imgId;
                        fetch(`<?= APP_URL ?>admin/imagen_orden/${id}/${i}`).catch(() => { });
                    });
                }
            });
        }
    });

    // ===== PREVISUALIZACIÓN UNIFICADA =====
    function renderPreview() {
        if (!previewGrid) return;
        previewGrid.innerHTML = '';
        galleryContainer.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => addThumb(e.target.result, 'Local', () => removeFile(index, 'local'));
            reader.readAsDataURL(file);
        });

        imgbbFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => addThumb(e.target.result, 'ImgBB', () => removeFile(index, 'imgbb'));
            reader.readAsDataURL(file);
        });

        reusedImages.forEach((url, index) => {
            addThumb(url, 'Galería', () => removeReused(index));
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'existing_images[]';
            input.value = url;
            galleryContainer.appendChild(input);
        });
    }

    function addThumb(src, label, onRemove) {
        const col = document.createElement('div');
        col.className = 'col-4 col-md-2 position-relative mb-2';
        col.innerHTML = `
            <div class="border rounded-4 overflow-hidden shadow-sm bg-white" style="height:85px">
                <img src="${src}" style="width:100%;height:100%;object-fit:cover">
                <div class="position-absolute top-0 start-0 m-1">
                    <span class="badge bg-dark-subtle text-dark" style="font-size:8px">${label}</span>
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 rounded-circle shadow"
                    style="width:20px;height:20px;margin:-5px;z-index:10">×</button>`;

        col.querySelector('button').onclick = (e) => {
            e.stopPropagation();
            onRemove();
        };
        previewGrid.appendChild(col);
    }

    function removeFile(index, type) {
        if (type === 'local') selectedFiles.splice(index, 1);
        else imgbbFiles.splice(index, 1);
        updateInputs();
        renderPreview();
    }

    function removeReused(index) {
        reusedImages.splice(index, 1);
        renderPreview();
    }

    function updateInputs() {
        const dtLocal = new DataTransfer();
        selectedFiles.forEach(f => dtLocal.items.add(f));
        const inLoc = document.getElementById('inputImages');
        if (inLoc) inLoc.files = dtLocal.files;

        const dtImgBB = new DataTransfer();
        imgbbFiles.forEach(f => dtImgBB.items.add(f));
        const inBB = document.getElementById('inputImgBB');
        if (inBB) inBB.files = dtImgBB.files;
    }

    // Eventos de Inputs
    const inLoc = document.getElementById('inputImages');
    if (inLoc) inLoc.addEventListener('change', function (e) {
        selectedFiles = [...selectedFiles, ...Array.from(e.target.files)].slice(0, MAX_IMAGES);
        updateInputs();
        renderPreview();
    });

    const inBB = document.getElementById('inputImgBB');
    if (inBB) inBB.addEventListener('change', function (e) {
        imgbbFiles = [...imgbbFiles, ...Array.from(e.target.files)].slice(0, MAX_IMAGES);
        updateInputs();
        renderPreview();
    });

    // ===== EXPLORADOR DE GALERÍA (MODAL) =====
    let _imgs = [], _loaded = false;

    function openImageBrowser() {
        if (!imgBrowserModal) {
            const modalEl = document.getElementById('imgBrowserModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                imgBrowserModal = new bootstrap.Modal(modalEl);
            } else {
                alert('Bootstrap no está listo. Por favor espera o recarga.');
                return;
            }
        }
        imgBrowserModal.show();
        if (!_loaded) loadImages();
    }

    function loadImages() {
        const list = document.getElementById('modal-img-list');
        if (!list) return;
        list.innerHTML = `<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>`;

        fetch('<?= APP_URL ?>admin/images_json')
            .then(r => r.json())
            .then(data => {
                _imgs = data;
                _loaded = true;
                renderModalImages('all');
            })
            .catch(() => list.innerHTML = 'Error al cargar imágenes.');
    }

    function renderModalImages(filter) {
        const list = document.getElementById('modal-img-list');
        if (!list) return;
        list.innerHTML = '';

        const filtered = _imgs.filter(img => {
            if (filter === 'all') return true;
            if (filter === 'local') return img.source === 'local';
            if (filter === 'api') return (img.source === 'api' || img.source === 'url');
            return true;
        });

        filtered.forEach(img => {
            const col = document.createElement('div');
            col.className = 'col-4 col-sm-3 col-md-2 mb-3';
            col.innerHTML = `
                <div class="img-browser-item border rounded-3 overflow-hidden position-relative" style="height:100px; cursor:pointer">
                    <img src="${img.full}" class="w-100 h-100" style="object-fit:cover">
                    <div class="overlay position-absolute top-0 start-0 w-100 h-100 bg-primary bg-opacity-25 d-none align-items-center justify-content-center">
                        <i class="bi bi-check-circle-fill text-white fs-4"></i>
                    </div>
                </div>`;

            col.querySelector('.img-browser-item').onclick = () => {
                if (reusedImages.length >= MAX_IMAGES) {
                    Swal.fire('Límite', 'Máximo 5 imágenes por producto', 'info');
                    return;
                }
                reusedImages.push(img.full);
                imgBrowserModal.hide();
                renderPreview();
            };
            list.appendChild(col);
        });
    }

    function filterImgBrowser(type) {
        renderModalImages(type);
    }

    // ===== AÑADIR CAMPO URL =====
    const btnAddUrl = document.querySelector('.btn-add-url');
    if (btnAddUrl) {
        btnAddUrl.addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = 'input-group mb-2 shadow-sm animate__animated animate__fadeInUp';
            div.innerHTML = `
                <span class="input-group-text bg-white"><i class="bi bi-link-45deg"></i></span>
                <input type="url" name="img_urls[]" class="form-control border-start-0" placeholder="https://ejemplo.com/imagen.jpg">
                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>`;
            document.getElementById('url-container').appendChild(div);
        });
    }

    // ===== TOGGLE DIGITAL =====
    const isDigitalSwitch = document.getElementById('isDigital');
    const digitalCard = document.getElementById('digital-file-card');
    const priceDozenWrapper = document.getElementById('price-dozen-wrapper');

    function toggleDigital() {
        if (!isDigitalSwitch) return;
        const digital = isDigitalSwitch.checked;
        if (digitalCard) digitalCard.style.display = digital ? 'block' : 'none';
        if (priceDozenWrapper) priceDozenWrapper.style.opacity = digital ? '.4' : '1';
    }
    if (isDigitalSwitch) isDigitalSwitch.addEventListener('change', toggleDigital);
    toggleDigital();

    // ===== MARCAR COMO PORTADA (AJAX) =====
    document.querySelectorAll('.btn-set-primary').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.img;
            fetch(`<?= APP_URL ?>admin/imagen_principal/${id}`)
                .then(() => location.reload());
        });
    });

    // ===== ELIMINAR IMAGEN (AJAX) =====
    document.querySelectorAll('.btn-del-img').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.img;
            const action = () => fetch(`<?= APP_URL ?>admin/imagen_eliminar/${id}`).then(() => location.reload());
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Eliminar imagen?', text: 'Se borrará permanentemente.', icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#ef233c', confirmButtonText: 'Eliminar'
                }).then(r => { if (r.isConfirmed) action(); });
            } else if (confirm('¿Eliminar imagen?')) {
                action();
            }
        });
    });
</script>

<!-- Modal Explorador de Imágenes -->
<div class="modal fade" id="imgBrowserModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Explorador de Galería</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-4 overflow-auto pb-1">
                    <button class="btn btn-sm btn-dark rounded-pill px-3"
                        onclick="filterImgBrowser('all')">Todas</button>
                    <button class="btn btn-sm btn-outline-dark rounded-pill px-3"
                        onclick="filterImgBrowser('local')">Servidor Local</button>
                    <button class="btn btn-sm btn-outline-dark rounded-pill px-3"
                        onclick="filterImgBrowser('api')">ImgBB / URL</button>
                </div>
                <div class="row g-2" id="modal-img-list">
                    <!-- Dinámico con JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .img-browser-item:hover .overlay {
        display: flex !important;
    }

    .gallery-thumb:hover .gallery-actions {
        opacity: 1;
    }

    .btn-xs {
        padding: 1px 5px;
        font-size: 10px;
    }
</style>