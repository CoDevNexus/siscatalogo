<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<?php
$imgbbEnabled = defined('IMGBB_API_KEY') && !empty(IMGBB_API_KEY);
?>

<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                <?= $item ? 'Editar' : 'Nuevo' ?> Caso de Éxito
            </h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="<?= APP_URL ?>admin/portfolio" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <form action="<?= APP_URL ?>admin/portfolio_guardar" method="POST" enctype="multipart/form-data" id="portfolioForm">
        <input type="hidden" name="id" value="<?= $item['id'] ?? '' ?>">
        <input type="hidden" name="contenido_enriquecido" id="quill-content">
        <input type="hidden" name="imagen_principal" id="imagen_principal_input"
            value="<?= $item['imagen_principal'] ?? '' ?>">

        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Título del Proyecto</label>
                            <input type="text" name="titulo"
                                class="form-control form-control-lg fw-bold border-0 bg-light p-3 rounded-3"
                                value="<?= htmlspecialchars($item['titulo'] ?? '') ?>"
                                placeholder="Ej: Grabado Láser en Madera de Pino" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Introducción Corta (Resumen para la
                                tarjeta)</label>
                            <textarea name="intro_corta" class="form-control bg-light border-0 p-3 rounded-3" rows="3"
                                placeholder="Describe brevemente este trabajo..."><?= htmlspecialchars($item['intro_corta'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted d-block pb-2">Contenido Detallado
                                (Editor)</label>
                            <div id="editor-container"
                                style="height: 400px; background: #fdfdfd; border-radius: 0 0 12px 12px;"
                                class="rounded-3 shadow-none border-light">
                                <?= $item['contenido_enriquecido'] ?? '' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO y Metadatos -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-bold text-muted"><i class="bi bi-search me-2"></i>Optimización para Motores de
                            Búsqueda (SEO)</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Meta Descripción (Recomendado 150-160
                                caracteres)</label>
                            <textarea name="meta_description" class="form-control bg-light border-0 p-3 rounded-3"
                                rows="2"
                                placeholder="Resumen que aparecerá en Google..."><?= htmlspecialchars($item['meta_description'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">Tags / Palabras Clave</label>
                            <input type="text" name="tags" class="form-control bg-light border-0 p-3 rounded-3"
                                value="<?= htmlspecialchars($item['tags'] ?? '') ?>"
                                placeholder="Ej: Láser, Madera, Personalizado (Separados por coma)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Imagen Destacada (SELECTOR AVANZADO) -->
                <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-primary text-white border-0 py-3 px-4">
                        <h6 class="fw-bold mb-0">Imagen de Portada</h6>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        $imgSrc = $item['imagen_principal'] ?? '';
                        $sourceLabel = 'Sin imagen';
                        $sourceColor = 'secondary';

                        if ($imgSrc) {
                            if (strpos($imgSrc, 'http') === 0) {
                                if (strpos($imgSrc, 'ibb.co') !== false) {
                                    $sourceLabel = 'ImgBB (Externo)';
                                    $sourceColor = 'warning';
                                } else {
                                    $sourceLabel = 'URL Directa (Externo)';
                                    $sourceColor = 'info';
                                }
                            } else {
                                $sourceLabel = 'Servidor Local';
                                $sourceColor = 'success';
                            }
                        }
                        ?>
                        <div id="imagePreviewContainer"
                            class="mb-3 rounded-4 overflow-hidden border bg-light d-flex align-items-center justify-content-center position-relative"
                            style="height: 220px; border-style: dashed !important;">
                            <?php if (!empty($imgSrc)): ?>
                                <?php $dispUrl = (strpos($imgSrc, 'http') === 0) ? $imgSrc : APP_URL . $imgSrc; ?>
                                <img src="<?= $dispUrl ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-<?= $sourceColor ?> shadow-sm rounded-pill px-3 py-2"
                                        id="source-badge">
                                        <i class="bi bi-info-circle me-1"></i><?= $sourceLabel ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <i class="bi bi-image fs-1 text-muted opacity-25"></i>
                                    <p class="small text-muted mt-2">No hay imagen seleccionada</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary btn-sm w-100 rounded-pill"
                                    onclick="document.getElementById('inputLocal').click()">
                                    <i class="bi bi-pc-display me-1"></i>Local
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-info btn-sm w-100 rounded-pill"
                                    onclick="openImageBrowser()">
                                    <i class="bi bi-images me-1"></i>Galería
                                </button>
                            </div>
                            <?php if ($imgbbEnabled): ?>
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-warning btn-sm w-100 rounded-pill"
                                        onclick="document.getElementById('inputImgBB').click()">
                                        <i class="bi bi-cloud-arrow-up me-1"></i>Subir a ImgBB
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-0">
                            <div class="input-group input-group-sm mb-2 shadow-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-link-45deg"></i></span>
                                <input type="url" id="external_url" class="form-control"
                                    placeholder="Pegar URL de imagen..."
                                    value="<?= (strpos($imgSrc, 'http') === 0) ? htmlspecialchars($imgSrc) : '' ?>"
                                    onchange="updateFromUrl(this.value)">
                            </div>
                        </div>

                        <input type="file" name="portfolio_local" id="inputLocal" class="d-none" accept="image/*"
                            onchange="previewLocal(this)">
                        <input type="file" name="portfolio_imgbb" id="inputImgBB" class="d-none" accept="image/*"
                            onchange="previewImgBB(this)">
                    </div>
                </div>

                <!-- Configuración -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Categoría Técnica</label>
                            <select name="categoria_tecnica" class="form-select border-0 bg-light p-3 rounded-3">
                                <option value="Grabado Láser" <?= (isset($item['categoria_tecnica']) && $item['categoria_tecnica'] == 'Grabado Láser') ? 'selected' : '' ?>>Grabado Láser
                                </option>
                                <option value="Corte Láser" <?= (isset($item['categoria_tecnica']) && $item['categoria_tecnica'] == 'Corte Láser') ? 'selected' : '' ?>>Corte Láser</option>
                                <option value="Sublimación" <?= (isset($item['categoria_tecnica']) && $item['categoria_tecnica'] == 'Sublimación') ? 'selected' : '' ?>>Sublimación</option>
                                <option value="Impresión UV" <?= (isset($item['categoria_tecnica']) && $item['categoria_tecnica'] == 'Impresión UV') ? 'selected' : '' ?>>Impresión UV
                                </option>
                                <option value="Vinilo" <?= (isset($item['categoria_tecnica']) && $item['categoria_tecnica'] == 'Vinilo') ? 'selected' : '' ?>>Vinilo</option>
                                <option value="Otro" <?= (isset($item['categoria_tecnica']) && $item['categoria_tecnica'] == 'Otro') ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">Fecha de Publicación</label>
                            <input type="date" name="fecha_publicacion"
                                class="form-control border-0 bg-light p-3 rounded-3"
                                value="<?= $item['fecha_publicacion'] ?? date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>

                <!-- GALERÍA ADICIONAL -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div
                        class="card-header bg-dark text-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Galería del Proyecto</h6>
                        <span class="badge bg-secondary rounded-pill" id="gallery-count">0</span>
                    </div>
                    <div class="card-body p-4 position-relative">
                        <div id="gallery-container" class="row g-2 mb-3">
                            <!-- Items de la galería existentes -->
                            <?php if (isset($item['gallery']) && !empty($item['gallery'])): ?>
                                <?php foreach ($item['gallery'] as $idx => $gImg): ?>
                                    <div class="col-6 gallery-item" id="gallery-item-<?= $idx ?>">
                                        <div class="position-relative rounded-3 overflow-hidden border" style="height: 100px;">
                                            <?php $gUrl = ($gImg['source'] === 'local') ? APP_URL . $gImg['image_path'] : $gImg['image_path']; ?>
                                            <img src="<?= $gUrl ?>" class="w-100 h-100 object-fit-cover">
                                            <input type="hidden" name="gallery_urls[]" value="<?= $gImg['image_path'] ?>">
                                            <button type="button"
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle"
                                                onclick="removeGalleryItem('gallery-item-<?= $idx ?>')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="dropdown" style="z-index: 2000;">
                            <button class="btn btn-outline-dark w-100 py-2 rounded-pill dropdown-toggle shadow-sm"
                                type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-plus-lg me-2"></i>Agregar Imagen
                            </button>
                            <ul class="dropdown-menu w-100 border-0 shadow-lg rounded-3 dropdown-menu-end">
                                <li><a class="dropdown-item py-2" href="javascript:void(0)"
                                        onclick="document.getElementById('inputGalleryLocal').click()"><i
                                            class="bi bi-pc-display me-2 text-primary"></i>Subir Localmente</a></li>
                                <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="addGalleryUrl()"><i
                                            class="bi bi-link-45deg me-2 text-info"></i>Desde URL</a></li>
                                <?php if ($imgbbEnabled): ?>
                                    <li><a class="dropdown-item py-2" href="javascript:void(0)"
                                            onclick="document.getElementById('inputGalleryImgBB').click()"><i
                                                class="bi bi-cloud-arrow-up me-2 text-warning"></i>Subir a ImgBB</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <input type="file" id="inputGalleryLocal" class="d-none" multiple accept="image/*"
                            onchange="previewGalleryLocal(this)">
                        <input type="file" id="inputGalleryImgBB" class="d-none" multiple accept="image/*"
                            onchange="previewGalleryImgBB(this)">

                        <div id="hidden-file-inputs" class="d-none"></div>
                    </div>
                </div>

                <div class="p-3 mt-4">
                    <button type="submit"
                        class="btn btn-primary w-100 py-3 rounded-pill shadow-lg fw-bold btn-save-portfolio">
                        <i class="bi bi-save me-2"></i>Guardar Cambios y Publicar
                    </button>
                    <p class="text-center small text-muted mt-2 mb-0">Revisa el contenido antes de guardar.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Explorador de Imágenes -->
<div class="modal fade" id="imgBrowserModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 shadow-sm p-4">
                <h5 class="fw-bold mb-0">Explorar Galería Existente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-2" id="modal-img-list">
                    <!-- Dinámico -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link', 'blockquote', 'image'],
                ['clean']
            ]
        }
    });

    document.getElementById('portfolioForm').onsubmit = function () {
        var html = quill.root.innerHTML;
        document.getElementById('quill-content').value = html;
    };

    function setPreview(src, label = '', color = 'info') {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = `<img src="${src}" style="width: 100%; height: 100%; object-fit: cover;">
            <div class="position-absolute top-0 start-0 m-2">
                <span class="badge bg-${color} shadow-sm rounded-pill px-3 py-2" id="source-badge">
                    <i class="bi bi-info-circle me-1"></i>${label || 'Nueva Selección'}
                </span>
            </div>`;
    }

    function previewLocal(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                setPreview(e.target.result, 'Servidor Local (Pendiente)', 'success');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewImgBB(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                setPreview(e.target.result, 'Subiendo a ImgBB...', 'warning');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateFromUrl(url) {
        if (url) {
            setPreview(url, 'URL Directa (Externo)', 'info');
            document.getElementById('imagen_principal_input').value = url;
        }
    }

    let imgBrowserModal = null;
    function openImageBrowser() {
        if (!imgBrowserModal) imgBrowserModal = new bootstrap.Modal(document.getElementById('imgBrowserModal'));
        imgBrowserModal.show();
        loadExistingImages();
    }

    function loadExistingImages() {
        const list = document.getElementById('modal-img-list');
        list.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>';

        fetch('<?= APP_URL ?>admin/images_json')
            .then(r => r.json())
            .then(data => {
                list.innerHTML = '';
                data.forEach(img => {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-sm-3 col-md-2 mb-3';
                    col.innerHTML = `
                        <div class="border rounded-3 overflow-hidden" style="height:100px; cursor:pointer" onclick="selectFromGallery('${img.url}')">
                            <img src="${img.full}" class="w-100 h-100" style="object-fit:cover">
                        </div>`;
                    list.appendChild(col);
                });
            });
    }

    function selectFromGallery(path) {
        document.getElementById('imagen_principal_input').value = path;
        // Detectar si el path de la galería es externo o local
        const isUrl = path.indexOf('http') === 0;
        const fullPath = isUrl ? path : '<?= APP_URL ?>' + path;
        const label = isUrl ? 'Externo (Desde Galería)' : 'Local (Desde Galería)';
        const color = isUrl ? 'info' : 'success';

        setPreview(fullPath, label, color);
        imgBrowserModal.hide();
    }

    // --- LÓGICA DE GALERÍA ---
    let galleryIndex = <?= (isset($item['gallery'])) ? count($item['gallery']) : 0 ?>;
    updateGalleryCount();

    function updateGalleryCount() {
        const count = document.querySelectorAll('.gallery-item').length;
        document.getElementById('gallery-count').innerText = count;
    }

    function removeGalleryItem(id) {
        const el = document.getElementById(id);
        if (el) {
            el.remove();
            updateGalleryCount();
        }
    }

    function addGalleryToContainer(src, value, isFile = false, fileInput = null) {
        const container = document.getElementById('gallery-container');
        const id = 'gallery-item-new-' + galleryIndex++;

        const col = document.createElement('div');
        col.className = 'col-6 gallery-item';
        col.id = id;

        col.innerHTML = `
            <div class="position-relative rounded-3 overflow-hidden border" style="height: 100px;">
                <img src="${src}" class="w-100 h-100 object-fit-cover">
                <input type="hidden" name="gallery_urls[]" value="${value}">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle" 
                        onclick="removeGalleryItem('${id}')">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;

        if (isFile && fileInput) {
            fileInput.name = "gallery_local[]";
            if (fileInput.id.includes('ImgBB')) fileInput.name = "gallery_imgbb[]";
            document.getElementById('hidden-file-inputs').appendChild(fileInput);
        }

        container.appendChild(col);
        updateGalleryCount();
    }

    function addGalleryUrl() {
        const url = prompt("Ingrese la URL de la imagen:");
        if (url && url.trim() !== "") {
            addGalleryToContainer(url, url.trim());
        }
    }

    function previewGalleryLocal(input) {
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Para archivos locales, creamos un input file clonado para enviarlo
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);

                    const newFileInput = document.createElement('input');
                    newFileInput.type = 'file';
                    newFileInput.files = dataTransfer.files;

                    addGalleryToContainer(e.target.result, '', true, newFileInput);
                };
                reader.readAsDataURL(file);
            });
            input.value = ''; // Limpiar original
        }
    }

    function previewGalleryImgBB(input) {
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);

                    const newFileInput = document.createElement('input');
                    newFileInput.type = 'file';
                    newFileInput.id = 'gallery_imgbb_fake_' + galleryIndex;
                    newFileInput.files = dataTransfer.files;

                    addGalleryToContainer(e.target.result, '', true, newFileInput);
                };
                reader.readAsDataURL(file);
            });
            input.value = ''; // Limpiar original
        }
    }
</script>

<style>
    .ql-editor {
        font-size: 1rem;
        line-height: 1.6;
        color: #333;
    }

    .ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid #eee !important;
        border-radius: 12px 12px 0 0 !important;
        background: #fff !important;
    }

    .ql-container.ql-snow {
        border: none !important;
    }
</style>