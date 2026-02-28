<?php
/**
 * Reemplaza el bloque <script> del modal estático por uno que carga
 * imágenes dinámicamente vía AJAX desde admin/images_json.
 */
$file = dirname(__DIR__) . '/app/Views/admin/perfil.php';
$content = file_get_contents($file);

// Encontrar el bloque <script> del modal y reemplazarlo por el dinámico
$oldScript = preg_replace('/\s/', '', '
        function selectExistingLogo');

// Buscar el <script> que contiene el modal JS (empieza con function selectExistingLogo)
// Estrategia: reemplazar TODO el bloque desde <script> hasta </script> del modal

// Extraer posición del script del modal
$scriptStart = strpos($content, "\n        <script>\n        function selectExistingLogo");
if ($scriptStart === false) {
    $scriptStart = strpos($content, "<script>\n        function selectExistingLogo");
}
if ($scriptStart === false) {
    // Buscar variante
    preg_match('/(<script>\s*function selectExistingLogo.*?<\/script>)/s', $content, $m);
    if ($m) {
        $old = $m[0];
    } else {
        die("❌ No se encontró el script del modal\n");
    }
} else {
    // Extraer hasta el siguiente </script>
    $scriptEnd = strpos($content, '</script>', $scriptStart);
    $old = substr($content, $scriptStart, ($scriptEnd - $scriptStart) + strlen('</script>'));
}

$new = <<<'NEWSCRIPT'
<script>
// ── Modal de imágenes: carga AJAX dinámica ──
let _imgBrowserLoaded = false;

document.getElementById('imgBrowserModal').addEventListener('show.bs.modal', function () {
    if (_imgBrowserLoaded) return;
    loadImgBrowser();
});

function loadImgBrowser(filter) {
    const grid = document.getElementById('img-browser-grid');
    grid.innerHTML = '<div class="col-12 text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Cargando imágenes…</div>';

    fetch(APP_URL + 'admin/images_json')
        .then(r => r.json())
        .then(images => {
            _imgBrowserLoaded = true;
            renderImgBrowser(images, filter || 'all');
        })
        .catch(() => {
            grid.innerHTML = '<div class="col-12 text-center py-4 text-danger">Error al cargar imágenes.</div>';
        });
}

function renderImgBrowser(images, filter) {
    const grid = document.getElementById('img-browser-grid');
    grid.innerHTML = '';
    window._allImages = images;

    const filtered = filter === 'all' ? images : images.filter(i => i.source === filter);

    if (!filtered.length) {
        grid.innerHTML = '<div class="col-12 text-center py-4 text-muted"><i class="bi bi-images fs-2 d-block mb-2"></i>No hay imágenes en esta categoría.</div>';
        return;
    }

    filtered.forEach(img => {
        const col = document.createElement('div');
        col.className = 'col img-browser-item';
        col.dataset.source = img.source;
        col.dataset.name   = img.label;

        const badgeColor = img.source === 'local' ? '#6c757d' : '#0dcaf0';
        const badgeText  = img.source === 'local' ? 'Local' : 'CDN';

        col.innerHTML = `
            <div class="img-browser-thumb border rounded-3 overflow-hidden position-relative"
                 style="cursor:pointer;aspect-ratio:1;background:#f8f9fa;transition:outline .15s"
                 onclick="selectExistingLogo('${escJs(img.url)}','${img.source}','${escJs(img.label)}')">
                <img src="${escJs(img.full)}"
                     style="width:100%;height:100%;object-fit:contain;padding:6px;"
                     loading="lazy"
                     onerror="this.parentElement.style.background='#fee';this.style.display='none'"
                     alt="${escJs(img.label)}">
                <span class="position-absolute top-0 start-0 m-1 badge rounded-pill"
                      style="font-size:.55rem;background:${badgeColor};color:#fff">${badgeText}</span>
            </div>
            <div class="text-muted text-center mt-1" style="font-size:.65rem;word-break:break-all;overflow:hidden;max-height:2.4em">
                ${escJs(img.label.substring(0, 25))}
            </div>`;

        col.querySelector('.img-browser-thumb').addEventListener('mouseenter', e =>
            e.currentTarget.style.outline = '3px solid #0d6efd');
        col.querySelector('.img-browser-thumb').addEventListener('mouseleave', e =>
            e.currentTarget.style.outline = '');

        grid.appendChild(col);
    });
}

function escJs(str) {
    return String(str).replace(/'/g,"&#39;").replace(/"/g,'&quot;');
}

function selectExistingLogo(url, source, label) {
    document.getElementById('logoInput').value = '';
    document.getElementById('existing_logo_url').value = url;

    const fullUrl = url.startsWith('http') ? url : (APP_URL + url);

    let img = document.getElementById('logo-preview');
    if (!img) {
        const wrap = document.getElementById('logo-preview-wrap');
        wrap.innerHTML = '';
        img = document.createElement('img');
        img.id = 'logo-preview';
        img.style = 'max-height:70px;max-width:160px;object-fit:contain;border-radius:8px;border:1px solid #ddd;padding:6px;background:#fff;';
        wrap.appendChild(img);
    }
    img.src = fullUrl;

    let pvLogo = document.getElementById('pv-logo');
    if (pvLogo) pvLogo.src = fullUrl;

    document.getElementById('existing-logo-label').style.display = '';
    document.getElementById('existing-logo-name').textContent = label;

    bootstrap.Modal.getInstance(document.getElementById('imgBrowserModal')).hide();
}

function clearExistingLogo(e) {
    e.preventDefault();
    document.getElementById('existing_logo_url').value = '';
    document.getElementById('existing-logo-label').style.display = 'none';
}

function filterImgBrowser(type, btn) {
    document.querySelectorAll('[onclick^="filterImgBrowser"]').forEach(b => {
        b.classList.remove('active','btn-dark','btn-info');
        b.classList.add('btn-outline-secondary');
    });
    btn.classList.remove('btn-outline-secondary','btn-outline-info','btn-outline-secondary');
    btn.classList.add('active', type === 'all' ? 'btn-dark' : (type === 'api' ? 'btn-info' : 'btn-secondary'));

    if (window._allImages) {
        renderImgBrowser(window._allImages, type);
    }
}

function searchImgBrowser(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.img-browser-item').forEach(el => {
        el.style.display = el.dataset.name.toLowerCase().includes(q) ? '' : 'none';
    });
}

// ── Selector de destino de logo ──
function toggleDestCard() {
    const selLocal  = document.getElementById('dest_local');
    const cardLocal = document.getElementById('card_local');
    const cardImgBB = document.getElementById('card_imgbb');
    if (!selLocal) return;
    if (cardLocal) {
        cardLocal.style.borderColor = selLocal.checked ? '#0d6efd' : '';
        cardLocal.style.background  = selLocal.checked ? 'rgba(13,110,253,.06)' : '';
    }
    if (cardImgBB) {
        cardImgBB.style.borderColor = !selLocal.checked ? '#0dcaf0' : '';
        cardImgBB.style.background  = !selLocal.checked ? 'rgba(13,202,240,.06)' : '';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var destLocal = document.getElementById('dest_local');
    if (destLocal) { destLocal.checked = true; toggleDestCard(); }
});

// ── Maqueta de proforma en vivo ──
document.getElementById('logoInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    let img = document.getElementById('logo-preview');
    if (!img) {
        const wrap = document.getElementById('logo-preview-wrap');
        wrap.innerHTML = '';
        img = document.createElement('img');
        img.id = 'logo-preview';
        img.style = 'max-height:70px;max-width:160px;object-fit:contain;border-radius:8px;border:1px solid #ddd;padding:6px;background:#fff;';
        wrap.appendChild(img);
    }
    img.src = url;
    let pvLogo = document.getElementById('pv-logo');
    if (pvLogo) pvLogo.src = url;
    // Limpiar selección existente
    document.getElementById('existing_logo_url').value = '';
    document.getElementById('existing-logo-label').style.display = 'none';
});

function bind(inputId, previewId) {
    const inp = document.getElementById(inputId);
    const pv  = document.getElementById(previewId);
    if (!inp || !pv) return;
    inp.addEventListener('input', () => pv.textContent = inp.value || inp.placeholder || '');
}
bind('inp-name',   'pv-name');
bind('inp-eslogan','pv-eslogan');
bind('inp-terms',  'pv-terms');
bind('inp-thanks', 'pv-thanks');

const addrEl = document.getElementById('inp-addr');
const cityEl = document.getElementById('inp-city');
const pvAddr  = document.getElementById('pv-addr');
if (addrEl && cityEl && pvAddr) {
    const update = () => pvAddr.textContent = [addrEl.value.trim(), cityEl.value.trim()].filter(Boolean).join(', ');
    addrEl.addEventListener('input', update);
    cityEl.addEventListener('input', update);
}
</script>
NEWSCRIPT;

// Reemplazar el bloque encontrado
if (isset($old)) {
    $content = str_replace($old, $new, $content);
    echo "✅ Script del modal reemplazado por versión AJAX dinámica.\n";
} else {
    // Append
    $content .= "\n" . $new;
    echo "⚠️  Script añadido al final.\n";
}

// También quitar los scripts duplicados del final (toggleDestCard, bind, addrEl que ya estaban)
// El nuevo script los incluye todos - el archivo puede tener duplicados del patch anterior
// Removemos los scripts sueltos previos al modal que puedan haber quedado
$content = preg_replace(
    '/\n\n\/\/ Previsualización del logo[\s\S]*?cityEl\.addEventListener\(\'input\', update\);\n\}\n\n\/\/ ── Selector de destino de logo ──[\s\S]*?toggleDestCard\(\);\n\}\);\n/m',
    "\n",
    $content
);

file_put_contents($file, $content);
echo "✅ perfil.php actualizado.\n";
