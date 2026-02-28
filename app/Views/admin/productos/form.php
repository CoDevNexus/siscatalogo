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
                        <p class="small fw-bold text-muted mb-2">Imágenes actuales — arrastra para reordenar, ☆ para
                            portada:</p>
                        <div class="row g-2 mb-4" id="gallery-sort">
                            <?php foreach ($imagenes as $img): ?>
                                <?php
                                $imgUrl = ($img['source'] === 'local') ? APP_URL . $img['image_path'] : $img['image_path'];
                                ?>
                                <div class="col-4 col-md-2" data-img-id="<?= $img['id'] ?>">
                                    <div class="gallery-thumb position-relative border rounded-3 overflow-hidden <?= $img['is_primary'] ? 'border-warning border-2' : '' ?>"
                                        style="height: 80px; cursor: grab;">
                                        <img src="<?= $imgUrl ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                        <div class="gallery-actions position-absolute bottom-0 start-0 end-0 d-flex justify-content-between p-1"
                                            style="background:rgba(0,0,0,.5)">
                                            <button type="button" class="btn btn-xs text-warning p-0 lh-1 btn-set-primary"
                                                data-img="<?= $img['id'] ?>" title="Portada" style="font-size:13px;">
                                                <i class="bi <?= $img['is_primary'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs text-danger p-0 lh-1 btn-del-img"
                                                data-img="<?= $img['id'] ?>" title="Eliminar" style="font-size:13px;">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        <?php if ($img['source'] === 'api'): ?>
                                            <span class="position-absolute top-0 end-0 badge bg-info m-1"
                                                style="font-size:9px;">API</span>
                                        <?php elseif ($img['source'] === 'url'): ?>
                                            <span class="position-absolute top-0 end-0 badge bg-secondary m-1"
                                                style="font-size:9px;">URL</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- ZONA DRAG & DROP local -->
                    <div id="drop-zone" class="border border-2 border-dashed rounded-4 text-center py-4 mb-3"
                        style="cursor:pointer; border-color: #adb5bd !important; transition: all .2s;">
                        <i class="bi bi-cloud-upload fs-2 text-muted d-block mb-2"></i>
                        <p class="mb-1 fw-semibold text-secondary">Arrastra imágenes aquí o <span
                                class="text-primary text-decoration-underline"
                                onclick="document.getElementById('inputImages').click()">haz clic</span></p>
                        <p class="text-muted small mb-0">JPG, PNG, WebP — se comprimirán automáticamente a WebP</p>
                        <input type="file" name="images[]" id="inputImages" class="d-none" multiple accept="image/*">
                    </div>

                    <!-- Previsualización de archivos seleccionados -->
                    <div id="local-preview" class="row g-2 mb-3"></div>

                    <!-- Separador fuentes -->
                    <div class="d-flex align-items-center gap-2 my-3">
                        <hr class="flex-grow-1"><span class="text-muted small">o añadir URL externa</span>
                        <hr class="flex-grow-1">
                    </div>

                    <!-- URLs Externas (hasta 5) -->
                    <div id="url-container">
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" name="img_urls[]" class="form-control"
                                placeholder="https://ejemplo.com/imagen.jpg">
                            <button type="button" class="btn btn-outline-secondary btn-add-url" title="Añadir URL"><i
                                    class="bi bi-plus"></i></button>
                        </div>
                    </div>

                    <!-- ImgBB -->
                    <?php if ($imgbbEnabled): ?>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <hr class="flex-grow-1"><span class="text-muted small">o subir a ImgBB (CDN gratuito)</span>
                            <hr class="flex-grow-1">
                        </div>
                        <div class="input-group">
                            <label class="input-group-text"><i class="bi bi-cloud-arrow-up text-info"></i></label>
                            <input type="file" name="imgbb_uploads[]" class="form-control" multiple accept="image/*">
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border small mt-2 mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Para habilitar ImgBB (CDN gratuito), define <code>IMGBB_API_KEY</code> en
                            <code>config.php</code>.
                        </div>
                    <?php endif; ?>
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

                    <!-- Switch: Tipo de Producto -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="isDigital" name="is_digital"
                            <?= (!empty($producto['is_digital'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="isDigital">
                            <i class="bi bi-cloud-download text-primary me-1"></i> Producto Digital
                        </label>
                        <div class="form-text">Activa el campo de archivo descargable y oculta precio por docena.</div>
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
    // ===== DRAG & DROP Y PREVISUALIZACIÓN =====
    const dropZone = document.getElementById('drop-zone');
    const inputImages = document.getElementById('inputImages');
    const previewGrid = document.getElementById('local-preview');
    const MAX_IMAGES = 5;
    let selectedFiles = [];

    function addFilesToPreview(files) {
        Array.from(files).forEach(file => {
            if (selectedFiles.length >= MAX_IMAGES) return;
            if (!file.type.startsWith('image/')) return;
            selectedFiles.push(file);
            const reader = new FileReader();
            reader.onload = e => {
                const col = document.createElement('div');
                col.className = 'col-4 col-md-2 position-relative';
                col.innerHTML = `
                <div class="border rounded-3 overflow-hidden" style="height:70px">
                    <img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">
                </div>
                <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 rounded-circle p-0"
                        style="width:18px;height:18px;font-size:10px;line-height:1" onclick="this.closest('.col-4').remove()">×</button>`;
                previewGrid.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
        updateFilesInput();
    }

    function updateFilesInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        inputImages.files = dt.files;
    }

    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('bg-light'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('bg-light'));
    dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('bg-light'); addFilesToPreview(e.dataTransfer.files); });
    dropZone.addEventListener('click', () => inputImages.click());
    inputImages.addEventListener('change', e => addFilesToPreview(e.target.files));

    // ===== AÑADIR CAMPO URL =====
    let urlCount = 1;
    document.querySelector('.btn-add-url').addEventListener('click', () => {
        if (urlCount >= 5) return;
        urlCount++;
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `<span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
        <input type="url" name="img_urls[]" class="form-control" placeholder="https://ejemplo.com/imagen.jpg">
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>`;
        document.getElementById('url-container').appendChild(div);
    });

    // ===== TOGGLE DIGITAL =====
    const isDigitalSwitch = document.getElementById('isDigital');
    const digitalCard = document.getElementById('digital-file-card');
    const priceDozenWrapper = document.getElementById('price-dozen-wrapper');

    function toggleDigital() {
        const digital = isDigitalSwitch.checked;
        digitalCard.style.display = digital ? 'block' : 'none';
        priceDozenWrapper.style.opacity = digital ? '.4' : '1';
    }
    isDigitalSwitch.addEventListener('change', toggleDigital);
    toggleDigital(); // Aplicar estado inicial

    // ===== GALERÍA SORTABLE =====
    const gallerySort = document.getElementById('gallery-sort');
    if (gallerySort) {
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

    // ===== MARCAR COMO PORTADA =====
    document.querySelectorAll('.btn-set-primary').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.img;
            Swal.fire({
                title: '¿Establecer como portada?', icon: 'question', showCancelButton: true,
                confirmButtonText: 'Sí', cancelButtonText: 'No'
            }).then(r => {
                if (r.isConfirmed) {
                    fetch(`<?= APP_URL ?>admin/imagen_principal/${id}`)
                        .then(() => location.reload());
                }
            });
        });
    });

    // ===== ELIMINAR IMAGEN DE GALERÍA =====
    document.querySelectorAll('.btn-del-img').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.img;
            Swal.fire({
                title: 'Eliminar imagen', text: 'Esta acción no se puede deshacer.', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef233c', confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then(r => {
                if (r.isConfirmed) {
                    fetch(`<?= APP_URL ?>admin/imagen_eliminar/${id}`)
                        .then(() => location.reload());
                }
            });
        });
    });
</script>