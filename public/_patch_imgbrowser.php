<?php
/**
 * Parcha perfil.php:
 * 1. Añade campo hidden existing_logo_url + botón "Seleccionar existente"
 * 2. Añade modal de galería de imágenes (locales + ImgBB de productos)
 */

$file = dirname(__DIR__) . '/app/Views/admin/perfil.php';
$html = file_get_contents($file);

// ── 1. Insertar input hidden + botón "Seleccionar existente" después del file input ──
$after = '<input class="form-control mb-3" type="file" name="logo" id="logoInput"
                           accept=".png,.jpg,.jpeg,.svg,.webp">';

$insert = $after . '
                    <!-- Campo hidden para imagen ya subida -->
                    <input type="hidden" name="existing_logo_url" id="existing_logo_url" value="">
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#imgBrowserModal">
                            <i class="bi bi-folder2-open me-1"></i>Seleccionar imagen ya subida
                        </button>
                        <span id="existing-logo-label" class="text-muted small ms-2" style="display:none">
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            <span id="existing-logo-name"></span>
                            <a href="#" class="ms-1 text-danger" onclick="clearExistingLogo(event)">✕</a>
                        </span>
                    </div>';

$html = str_replace($after, $insert, $html);

// ── 2. Añadir modal de galería justo antes del cierre </form> ──
// Obtener imágenes locales de assets/img/
$imgDir = dirname(__DIR__) . '/public/assets/img/';
$allowed = ['webp', 'png', 'jpg', 'jpeg', 'gif', 'svg'];
$localImgs = [];
if (is_dir($imgDir)) {
    foreach (glob($imgDir . '*.*') as $f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $localImgs[] = [
                'url' => 'assets/img/' . basename($f),
                'label' => basename($f),
                'source' => 'local',
            ];
        }
    }
}

// Obtener imágenes de storage/productos
$storageDir = dirname(__DIR__) . '/storage/productos/';
if (is_dir($storageDir)) {
    foreach (glob($storageDir . '*.webp') as $f) {
        $localImgs[] = [
            'url' => 'storage/productos/' . basename($f),
            'label' => basename($f),
            'source' => 'local',
        ];
    }
}

// Obtener URLs ImgBB desde product_images en BD
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$apiImgs = $db->fetchAll("SELECT image_path, source FROM product_images WHERE source IN ('api','url') GROUP BY image_path, source ORDER BY MAX(id) DESC LIMIT 50");

// Construir HTML del modal
$modalHtml = '
        <!-- ══ MODAL GALERÍA DE IMÁGENES ══ -->
        <div class="modal fade" id="imgBrowserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content rounded-4 border-0">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-folder2-open me-2 text-warning"></i>Seleccionar imagen existente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pb-4">

                        <!-- Filtro rápido -->
                        <div class="d-flex gap-2 mb-3 flex-wrap">
                            <button class="btn btn-sm btn-dark active" onclick="filterImgBrowser(\'all\',this)">
                                <i class="bi bi-grid me-1"></i>Todas
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="filterImgBrowser(\'local\',this)">
                                <i class="bi bi-hdd me-1"></i>Servidor Local
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="filterImgBrowser(\'api\',this)">
                                <i class="bi bi-cloud me-1"></i>ImgBB / URL
                            </button>
                            <input type="text" class="form-control form-control-sm ms-auto" style="max-width:200px"
                                   placeholder="Buscar nombre…" oninput="searchImgBrowser(this.value)">
                        </div>';

// Imágenes locales en grid
$modalHtml .= '
                        <div id="img-browser-grid" class="row row-cols-3 row-cols-md-5 row-cols-lg-6 g-2">';

foreach ($localImgs as $img) {
    $url = 'APP_URL_PLACEHOLDER' . $img['url'];
    $modalHtml .= '
                            <div class="col img-browser-item" data-source="local" data-name="' . htmlspecialchars($img['label']) . '">
                                <div class="img-browser-thumb border rounded-3 overflow-hidden position-relative"
                                     onclick="selectExistingLogo(\'' . addslashes($img['url']) . '\',\'local\',\'' . addslashes($img['label']) . '\')"
                                     style="cursor:pointer;aspect-ratio:1;background:#f8f9fa">
                                    <img src="' . $url . '?t=<?= time() ?>"
                                         style="width:100%;height:100%;object-fit:contain;padding:6px;"
                                         onerror="this.parentElement.innerHTML=\'<div class=\\\'d-flex align-items-center justify-content-center h-100 text-muted\\\' style=\\\'font-size:.6rem\\\'><i class=\\\"bi bi-image\\\"></i></div>\'"
                                         alt="' . htmlspecialchars($img['label']) . '">
                                    <span class="position-absolute top-0 start-0 m-1 badge bg-secondary" style="font-size:.55rem">Local</span>
                                </div>
                                <div class="text-muted text-center" style="font-size:.65rem;word-break:break-all;margin-top:3px">' . htmlspecialchars(mb_substr($img['label'], 0, 20)) . '</div>
                            </div>';
}

foreach ($apiImgs as $img) {
    $modalHtml .= '
                            <div class="col img-browser-item" data-source="api" data-name="' . htmlspecialchars($img['image_path']) . '">
                                <div class="img-browser-thumb border rounded-3 overflow-hidden position-relative"
                                     onclick="selectExistingLogo(\'' . addslashes($img['image_path']) . '\',\'api\',\'ImgBB image\')"
                                     style="cursor:pointer;aspect-ratio:1;background:#f0f8ff">
                                    <img src="' . htmlspecialchars($img['image_path']) . '"
                                         style="width:100%;height:100%;object-fit:contain;padding:6px;"
                                         onerror="this.style.display=\'none\'"
                                         alt="ImgBB">
                                    <span class="position-absolute top-0 start-0 m-1 badge bg-info" style="font-size:.55rem">CDN</span>
                                </div>
                                <div class="text-muted text-center" style="font-size:.65rem;word-break:break-all;margin-top:3px">ImgBB</div>
                            </div>';
}

if (empty($localImgs) && empty($apiImgs)) {
    $modalHtml .= '<div class="col-12 text-center text-muted py-4">No hay imágenes subidas aún.</div>';
}

$modalHtml .= '
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-3">
                        <small class="text-muted me-auto">Haz clic en la imagen para seleccionarla como logo.</small>
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function selectExistingLogo(url, source, label) {
            // Limpiar el file input (no subir nada nuevo)
            document.getElementById(\'logoInput\').value = \'\';

            // Poner la URL en el campo hidden
            document.getElementById(\'existing_logo_url\').value = url;

            // Actualizar preview
            const fullUrl = url.startsWith(\'http\') ? url : (APP_URL + url);
            let img = document.getElementById(\'logo-preview\');
            if (!img) {
                const wrap = document.getElementById(\'logo-preview-wrap\');
                wrap.innerHTML = \'\';
                img = document.createElement(\'img\');
                img.id = \'logo-preview\';
                img.style = \'max-height:70px;max-width:160px;object-fit:contain;border-radius:8px;border:1px solid #ddd;padding:6px;background:#fff;\';
                wrap.appendChild(img);
            }
            img.src = fullUrl;

            // También actualizar maqueta de proforma
            let pvLogo = document.getElementById(\'pv-logo\');
            if (pvLogo) pvLogo.src = fullUrl;

            // Mostrar etiqueta
            document.getElementById(\'existing-logo-label\').style.display = \'\';
            document.getElementById(\'existing-logo-name\').textContent = label;

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById(\'imgBrowserModal\')).hide();
        }

        function clearExistingLogo(e) {
            e.preventDefault();
            document.getElementById(\'existing_logo_url\').value = \'\';
            document.getElementById(\'existing-logo-label\').style.display = \'none\';
        }

        function filterImgBrowser(type, btn) {
            document.querySelectorAll(\'[onclick^="filterImgBrowser"]\').forEach(b => {
                b.className = b.className.replace(/ active/,\'\').replace(\'btn-dark\',\'btn-outline-secondary\').replace(\'btn-outline-info\',\'btn-outline-info\');
            });
            btn.classList.add(\'active\');
            document.querySelectorAll(\'.img-browser-item\').forEach(el => {
                el.style.display = (type === \'all\' || el.dataset.source === type) ? \'\' : \'none\';
            });
        }

        function searchImgBrowser(q) {
            q = q.toLowerCase();
            document.querySelectorAll(\'.img-browser-item\').forEach(el => {
                el.style.display = el.dataset.name.toLowerCase().includes(q) ? \'\' : \'none\';
            });
        }

        // Hover effect en miniaturas
        document.querySelectorAll(\'.img-browser-thumb\').forEach(el => {
            el.addEventListener(\'mouseenter\', () => el.style.outline = \'3px solid #0d6efd\');
            el.addEventListener(\'mouseleave\', () => el.style.outline = \'\');
        });
        </script>
';

// Insertar modal + script justo antes del cierre de </form>
$html = str_replace('            <!-- IDENTIDAD -->', $modalHtml . "\n            <!-- IDENTIDAD -->", $html);

// Reemplazar el placeholder de APP_URL
$html = str_replace('APP_URL_PLACEHOLDER', '<?= APP_URL ?>', $html);

file_put_contents($file, $html);
echo "✅ Modal de selección de imagen insertado en perfil.php\n";
