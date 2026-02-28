<?php
$imgbbEnabled = defined('IMGBB_API_KEY') && !empty(IMGBB_API_KEY);
$wa = $company['phone_whatsapp'] ?? $company['whatsapp'] ?? '';
$logo = !empty($company['logo_url'])
    ? (str_starts_with($company['logo_url'], 'http') ? $company['logo_url'] : APP_URL . $company['logo_url'])
    : null;
$isImgBBLogo = !empty($company['logo_url']) && str_starts_with($company['logo_url'], 'http');
?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- ══ COLUMNA IZQUIERDA: Formulario ══ -->
    <div class="col-lg-7">
        <form action="<?= APP_URL ?>admin/update_perfil" method="POST" enctype="multipart/form-data" id="profileForm">

            <!-- Campo hidden para imagen seleccionada del browser -->
            <input type="hidden" name="existing_logo_url" id="existing_logo_url" value="">

            <!-- ── LOGO ── -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="bi bi-image me-2 text-warning"></i>Logotipo de la Empresa</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <!-- Logo actual -->
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                        <div id="logo-preview-wrap" class="flex-shrink-0">
                            <?php if ($logo): ?>
                                <img id="logo-preview" src="<?= htmlspecialchars($logo) ?>?t=<?= time() ?>" alt="Logo"
                                    style="max-height:70px;max-width:160px;object-fit:contain;border-radius:8px;border:1px solid #ddd;padding:6px;background:#fff;">
                            <?php else: ?>
                                <div class="bg-white border rounded-3 d-flex align-items-center justify-content-center text-muted"
                                    style="width:70px;height:70px">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="small fw-bold text-dark mb-1">Logo actual</div>
                            <?php if (!empty($company['logo_url'])): ?>
                                <span class="badge <?= $isImgBBLogo ? 'bg-info' : 'bg-secondary' ?> rounded-pill">
                                    <i class="bi <?= $isImgBBLogo ? 'bi-cloud-check' : 'bi-hdd' ?> me-1"></i>
                                    <?= $isImgBBLogo ? 'ImgBB CDN' : 'Servidor Local' ?>
                                </span>
                                <div class="text-muted mt-1" style="font-size:.72rem;word-break:break-all">
                                    <?= htmlspecialchars($company['logo_url']) ?></div>
                            <?php else: ?>
                                <span class="text-muted small">Sin logo configurado</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Seleccionar imagen existente -->
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
                    </div>

                    <!-- Subir nuevo logo -->
                    <label class="form-label small fw-bold text-muted">Subir nuevo logo <span
                            class="fw-normal">(opcional)</span></label>
                    <input class="form-control mb-3" type="file" name="logo" id="logoInput"
                        accept=".png,.jpg,.jpeg,.svg,.webp">

                    <!-- Destino -->
                    <label class="form-label small fw-bold text-muted d-block mb-2">¿Dónde guardar?</label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="d-block border rounded-3 p-3" for="dest_local" id="card_local"
                                style="cursor:pointer">
                                <input type="radio" class="d-none" name="use_imgbb" id="dest_local" value="0" checked
                                    onchange="toggleDestCard()">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-hdd-fill fs-4 text-secondary"></i>
                                    <div>
                                        <div class="fw-bold small">Servidor Local</div>
                                        <div class="text-muted" style="font-size:.72rem">Comprimido a WebP en
                                            <code>assets/img/</code></div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <?php if ($imgbbEnabled): ?>
                                <label class="d-block border rounded-3 p-3" for="dest_imgbb" id="card_imgbb"
                                    style="cursor:pointer">
                                    <input type="radio" class="d-none" name="use_imgbb" id="dest_imgbb" value="1"
                                        onchange="toggleDestCard()">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-cloud-arrow-up-fill fs-4 text-info"></i>
                                        <div>
                                            <div class="fw-bold small">ImgBB CDN <span class="badge bg-success ms-1"
                                                    style="font-size:.65rem">Recomendado</span></div>
                                            <div class="text-muted" style="font-size:.72rem">URL permanente — ideal para
                                                proformas portables</div>
                                        </div>
                                    </div>
                                </label>
                            <?php else: ?>
                                <div class="border rounded-3 p-3 bg-light opacity-50">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-cloud-slash fs-4 text-muted"></i>
                                        <div>
                                            <div class="fw-bold small text-muted">ImgBB CDN</div>
                                            <div class="text-muted" style="font-size:.72rem">Configura
                                                <code>IMGBB_API_KEY</code> en <code>config.php</code></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-text mt-2">PNG con fondo transparente recomendado. El logo anterior se elimina al
                        subir uno nuevo.</div>
                </div>
            </div>

            <!-- ── IDENTIDAD ── -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="bi bi-building me-2 text-primary"></i>Identidad Corporativa</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-muted">Nombre Comercial *</label>
                            <input type="text" name="name" id="inp-name" class="form-control form-control-lg" required
                                value="<?= htmlspecialchars($company['name'] ?? '') ?>" placeholder="Ej: Laser Studio">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">RUC / NIT</label>
                            <input type="text" name="ruc_nit" class="form-control"
                                value="<?= htmlspecialchars($company['ruc_nit'] ?? '') ?>"
                                placeholder="001234567890001">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Eslogan</label>
                            <input type="text" name="eslogan" id="inp-eslogan" class="form-control"
                                value="<?= htmlspecialchars($company['eslogan'] ?? '') ?>"
                                placeholder="Precisión y creatividad en cada corte">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Descripción / Sobre Nosotros</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Descripción para la página Sobre Nosotros..."><?= htmlspecialchars($company['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── CONTACTO ── -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="bi bi-telephone me-2 text-success"></i>Contacto y Ubicación</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">WhatsApp *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white border-0"><i
                                        class="bi bi-whatsapp"></i></span>
                                <input type="text" name="phone_whatsapp" id="inp-wa" class="form-control"
                                    value="<?= htmlspecialchars($wa) ?>" placeholder="593987654321">
                            </div>
                            <div class="form-text">Solo números con código de país. Ej: <code>593987654321</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($company['email'] ?? '') ?>"
                                    placeholder="contacto@empresa.com">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-muted">Dirección</label>
                            <input type="text" name="address" id="inp-addr" class="form-control"
                                value="<?= htmlspecialchars($company['address'] ?? '') ?>"
                                placeholder="Av. Principal 123">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">Ciudad</label>
                            <input type="text" name="ciudad" id="inp-city" class="form-control"
                                value="<?= htmlspecialchars($company['ciudad'] ?? '') ?>" placeholder="Guayaquil">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── REDES SOCIALES ── -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="bi bi-share me-2 text-info"></i>Redes Sociales</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#1877f2;color:#fff;border:none"><i
                                        class="bi bi-facebook"></i></span>
                                <input type="url" name="facebook_url" class="form-control"
                                    value="<?= htmlspecialchars($company['facebook_url'] ?? $company['facebook'] ?? '') ?>"
                                    placeholder="https://facebook.com/tupagina">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text"
                                    style="background:radial-gradient(circle at 30% 110%,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);color:#fff;border:none"><i
                                        class="bi bi-instagram"></i></span>
                                <input type="url" name="instagram" class="form-control"
                                    value="<?= htmlspecialchars($company['instagram'] ?? '') ?>"
                                    placeholder="https://instagram.com/tuusuario">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">TikTok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white border-0"><i
                                        class="bi bi-tiktok"></i></span>
                                <input type="url" name="tiktok" class="form-control"
                                    value="<?= htmlspecialchars($company['tiktok'] ?? '') ?>"
                                    placeholder="https://tiktok.com/@tuusuario">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Pinterest</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#e60023;color:#fff;border:none"><i
                                        class="bi bi-pinterest"></i></span>
                                <input type="url" name="pinterest_url" class="form-control"
                                    value="<?= htmlspecialchars($company['pinterest_url'] ?? '') ?>"
                                    placeholder="https://pinterest.com/tuperfil">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── PROFORMAS ── -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="bi bi-file-earmark-text me-2 text-secondary"></i>Configuración de
                        Proformas</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Términos y Condiciones</label>
                            <textarea name="terms_conditions" id="inp-terms" class="form-control" rows="2"
                                placeholder="Ej: Cotización válida por 48 horas."><?= htmlspecialchars($company['terms_conditions'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Mensaje de Agradecimiento</label>
                            <input type="text" name="thank_you_message" id="inp-thanks" class="form-control"
                                value="<?= htmlspecialchars($company['thank_you_message'] ?? '¡Gracias por su preferencia!') ?>"
                                placeholder="¡Gracias por confiar en nosotros!">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── GOOGLE MAPS ── -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="bi bi-geo-alt me-2 text-danger"></i>Mapa de Ubicación</h6>
                    <small class="text-muted">Pega el <code>&lt;iframe&gt;</code> de Google Maps (Compartir → Insertar
                        mapa).</small>
                </div>
                <div class="card-body px-4 pb-4">
                    <textarea name="maps_embed" class="form-control font-monospace" rows="3"
                        placeholder='&lt;iframe src="https://www.google.com/maps/embed?..."&gt;&lt;/iframe&gt;'><?= htmlspecialchars($company['maps_embed'] ?? '') ?></textarea>
                    <?php if (!empty($company['maps_embed'])): ?>
                        <div class="mt-3 rounded-3 overflow-hidden border" style="height:180px">
                            <?= $company['maps_embed'] ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── GUARDAR ── -->
            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary py-3 fw-bold rounded-3 fs-5">
                    <i class="bi bi-check-circle me-2"></i>Guardar Todo el Perfil
                </button>
            </div>
        </form>
    </div><!-- /col-lg-7 -->

    <!-- ══ COLUMNA DERECHA: Maqueta de Proforma en Vivo ══ -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:80px">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-muted"><i class="bi bi-eye me-2"></i>Vista previa de Proforma</h6>
                <small class="text-muted">Se actualiza mientras escribes.</small>
            </div>
            <div class="card-body p-3">
                <div class="bg-white rounded-3 border p-3" style="font-size:.78rem;line-height:1.5">
                    <div class="d-flex align-items-start justify-content-between border-bottom pb-3 mb-3">
                        <div>
                            <div id="pv-logo-wrap">
                                <?php if ($logo): ?>
                                    <img id="pv-logo" src="<?= htmlspecialchars($logo) ?>"
                                        style="max-height:40px;max-width:120px;object-fit:contain;" class="mb-1">
                                <?php endif; ?>
                            </div>
                            <div id="pv-name" class="fw-bold text-dark" style="font-size:1rem">
                                <?= htmlspecialchars($company['name'] ?? 'Mi Empresa') ?></div>
                            <div id="pv-eslogan" class="text-muted" style="font-size:.72rem">
                                <?= htmlspecialchars($company['eslogan'] ?? '') ?></div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark">PROFORMA</div>
                            <div class="text-muted" style="font-size:.7rem">PRF-<?= date('ymd') ?> ·
                                <?= date('d M Y') ?></div>
                        </div>
                    </div>
                    <div id="pv-addr" class="text-muted mb-2">
                        <?= htmlspecialchars(($company['address'] ?? '') . (isset($company['ciudad']) && $company['ciudad'] ? ', ' . $company['ciudad'] : '')) ?>
                    </div>
                    <table class="table table-sm table-bordered" style="font-size:.75rem">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Cant.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Llavero Grabado Láser</td>
                                <td class="text-center">5</td>
                                <td class="text-end fw-bold">$12.50</td>
                            </tr>
                            <tr>
                                <td>Caja MDF Personalizada</td>
                                <td class="text-center">2</td>
                                <td class="text-end fw-bold">$30.00</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-end fw-bold" style="font-size:.9rem">TOTAL: <span class="text-danger">$42.50</span>
                    </div>
                    <hr style="margin:8px 0">
                    <div id="pv-terms" class="text-muted" style="font-size:.68rem">
                        <?= htmlspecialchars($company['terms_conditions'] ?? 'Cotización válida por 48 horas.') ?></div>
                    <div id="pv-thanks" class="fw-semibold mt-1" style="font-size:.75rem">
                        <?= htmlspecialchars($company['thank_you_message'] ?? '¡Gracias por su preferencia!') ?></div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /row -->

<!-- ══ MODAL GALERÍA (fuera del form) ══ -->
<div class="modal fade" id="imgBrowserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-folder2-open me-2 text-warning"></i>Seleccionar imagen existente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-2">
                <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
                    <button type="button" class="btn btn-sm btn-dark active" onclick="filterImg('all',this)"><i
                            class="bi bi-grid me-1"></i>Todas</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="filterImg('local',this)"><i
                            class="bi bi-hdd me-1"></i>Servidor Local</button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="filterImg('api',this)"><i
                            class="bi bi-cloud me-1"></i>ImgBB / URL</button>
                    <input type="text" class="form-control form-control-sm ms-auto" style="max-width:200px"
                        placeholder="Buscar nombre…" oninput="searchImg(this.value)">
                </div>
                <div id="img-browser-grid" class="row row-cols-3 row-cols-md-5 row-cols-lg-6 g-2">
                    <div class="col-12 text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm me-2"></div>Cargando…
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-3">
                <small class="text-muted me-auto">Clic en la imagen para seleccionarla como logo.</small>
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                    data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const APP_URL = '<?= APP_URL ?>';
    
    /* ── Estado global ── */
    var _imgs = [], _loaded = false;

    /* ── Cargar al abrir modal ── */
    document.getElementById('imgBrowserModal').addEventListener('show.bs.modal', function () {
        if (_loaded) return;
        fetch(APP_URL + 'admin/images_json')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                _imgs = data;
                _loaded = true;
                renderImg('all');
            })
            .catch(function () {
                document.getElementById('img-browser-grid').innerHTML =
                    '<div class="col-12 text-center py-4 text-danger">Error al cargar imágenes.</div>';
            });
    });

    /* ── Renderizar grid ── */
    function renderImg(filter) {
        var grid = document.getElementById('img-browser-grid');
        var list = filter === 'all' ? _imgs : _imgs.filter(function (i) { return i.source === filter; });
        if (!list.length) {
            grid.innerHTML = '<div class="col-12 text-center py-4 text-muted"><i class="bi bi-images fs-2 d-block mb-2"></i>No hay imágenes en esta categoría.</div>';
            return;
        }
        grid.innerHTML = '';
        list.forEach(function (img) {
            var div = document.createElement('div');
            div.className = 'col img-item';
            div.dataset.source = img.source;
            div.dataset.name = img.label;

            var thumb = document.createElement('div');
            thumb.className = 'border rounded-3 overflow-hidden position-relative';
            thumb.style.cssText = 'cursor:pointer;aspect-ratio:1;background:#f8f9fa;transition:outline .15s';
            thumb.onclick = function () { pickImg(img.url, img.source, img.label); };
            thumb.onmouseenter = function () { this.style.outline = '3px solid #0d6efd'; };
            thumb.onmouseleave = function () { this.style.outline = ''; };

            var im = document.createElement('img');
            im.src = img.full;
            im.alt = img.label;
            im.style = 'width:100%;height:100%;object-fit:contain;padding:6px;';
            im.loading = 'lazy';
            im.onerror = function () { this.parentElement.style.background = '#fee'; this.style.display = 'none'; };
            thumb.appendChild(im);

            var badge = document.createElement('span');
            badge.className = 'position-absolute top-0 start-0 m-1 badge rounded-pill';
            badge.style.cssText = 'font-size:.55rem;background:' + (img.source === 'local' ? '#6c757d' : '#0dcaf0') + ';color:#fff';
            badge.textContent = img.source === 'local' ? 'Local' : 'CDN';
            thumb.appendChild(badge);

            var lbl = document.createElement('div');
            lbl.className = 'text-muted text-center mt-1';
            lbl.style = 'font-size:.65rem;word-break:break-all;overflow:hidden;max-height:2.4em';
            lbl.textContent = img.label.substring(0, 28);

            div.appendChild(thumb);
            div.appendChild(lbl);
            grid.appendChild(div);
        });
    }

    /* ── Seleccionar imagen ── */
    function pickImg(url, source, label) {
        document.getElementById('existing_logo_url').value = url;
        var fullUrl = url.indexOf('http') === 0 ? url : (APP_URL + url);

        /* preview en card logo */
        var prev = document.getElementById('logo-preview');
        if (!prev) {
            var wrap = document.getElementById('logo-preview-wrap');
            wrap.innerHTML = '';
            prev = document.createElement('img');
            prev.id = 'logo-preview';
            prev.style = 'max-height:70px;max-width:160px;object-fit:contain;border-radius:8px;border:1px solid #ddd;padding:6px;background:#fff;';
            wrap.appendChild(prev);
        }
        prev.src = fullUrl;

        /* preview en maqueta proforma */
        var pvLogo = document.getElementById('pv-logo');
        if (!pvLogo) {
            var pvWrap = document.getElementById('pv-logo-wrap');
            pvLogo = document.createElement('img');
            pvLogo.id = 'pv-logo';
            pvLogo.style = 'max-height:40px;max-width:120px;object-fit:contain;';
            pvWrap.appendChild(pvLogo);
        }
        pvLogo.src = fullUrl;

        /* etiqueta de imagen seleccionada */
        document.getElementById('existing-logo-label').style.display = '';
        document.getElementById('existing-logo-name').textContent = label;

        /* limpiar file input */
        document.getElementById('logoInput').value = '';

        /* cerrar modal */
        if (typeof bootstrap !== 'undefined') {
            var m = bootstrap.Modal.getInstance(document.getElementById('imgBrowserModal'));
            if (m) m.hide();
        } else {
            // Fallback si por alguna razón no hay bootstrap
            $('#imgBrowserModal').modal('hide'); 
        }
    }

    function clearExistingLogo(e) {
        e.preventDefault();
        document.getElementById('existing_logo_url').value = '';
        document.getElementById('existing-logo-label').style.display = 'none';
    }

    function filterImg(type, btn) {
        document.querySelectorAll('[onclick^="filterImg"]').forEach(function (b) {
            b.className = 'btn btn-sm btn-outline-secondary';
        });
        btn.className = 'btn btn-sm ' + (type === 'all' ? 'btn-dark active' : type === 'api' ? 'btn-info active' : 'btn-secondary active');
        renderImg(type);
    }

    function searchImg(q) {
        q = q.toLowerCase();
        document.querySelectorAll('.img-item').forEach(function (el) {
            el.style.display = el.dataset.name.toLowerCase().indexOf(q) >= 0 ? '' : 'none';
        });
    }

    /* ── Preview logo al subir archivo ── */
    document.getElementById('logoInput').addEventListener('change', function () {
        if (!this.files[0]) return;
        var url = URL.createObjectURL(this.files[0]);
        var prev = document.getElementById('logo-preview');
        if (!prev) {
            document.getElementById('logo-preview-wrap').innerHTML = '';
            prev = document.createElement('img');
            prev.id = 'logo-preview';
            prev.style = 'max-height:70px;max-width:160px;object-fit:contain;border-radius:8px;border:1px solid #ddd;padding:6px;background:#fff;';
            document.getElementById('logo-preview-wrap').appendChild(prev);
        }
        prev.src = url;
        var pvLogo = document.getElementById('pv-logo');
        if (pvLogo) pvLogo.src = url;
        document.getElementById('existing_logo_url').value = '';
        document.getElementById('existing-logo-label').style.display = 'none';
    });

    /* ── Tarjetas de destino ── */
    function toggleDestCard() {
        var selLocal = document.getElementById('dest_local');
        var cardLocal = document.getElementById('card_local');
        var cardImgBB = document.getElementById('card_imgbb');
        if (!selLocal) return;
        if (cardLocal) {
            cardLocal.style.borderColor = selLocal.checked ? '#0d6efd' : '';
            cardLocal.style.background = selLocal.checked ? 'rgba(13,110,253,.06)' : '';
        }
        if (cardImgBB) {
            cardImgBB.style.borderColor = !selLocal.checked ? '#0dcaf0' : '';
            cardImgBB.style.background = !selLocal.checked ? 'rgba(13,202,240,.06)' : '';
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        var d = document.getElementById('dest_local');
        if (d) { d.checked = true; toggleDestCard(); }
    });

    /* ── Maqueta de proforma en vivo ── */
    function bindLive(inpId, pvId) {
        var inp = document.getElementById(inpId), pv = document.getElementById(pvId);
        if (inp && pv) inp.addEventListener('input', function () { pv.textContent = inp.value || inp.placeholder || ''; });
    }
    bindLive('inp-name', 'pv-name');
    bindLive('inp-eslogan', 'pv-eslogan');
    bindLive('inp-terms', 'pv-terms');
    bindLive('inp-thanks', 'pv-thanks');
    (function () {
        var a = document.getElementById('inp-addr'),
            c = document.getElementById('inp-city'),
            p = document.getElementById('pv-addr');
        if (a && c && p) {
            var upd = function () { p.textContent = [a.value.trim(), c.value.trim()].filter(Boolean).join(', '); };
            a.addEventListener('input', upd);
            c.addEventListener('input', upd);
        }
    })();
</script>