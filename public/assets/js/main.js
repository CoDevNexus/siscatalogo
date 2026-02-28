/**
 * SISCATALOGO – main.js
 * Carrito en localStorage, modal de ficha de producto con galería,
 * precios dinámicos unidad/docena, checkout con proforma y html2canvas.
 */

'use strict';

// ─────────────────────────────────────────
//  CONSTANTES GLOBALES (inyectadas en HTML)
// ─────────────────────────────────────────
// APP_URL  → inyectada como var global desde el header PHP
// COMPANY  → inyectada como var global (name, logo, phone, whatsapp)

// ─────────────────────────────────────────
//  CARRITO – localStorage
// ─────────────────────────────────────────
const Cart = {
    key: 'laser_cart',

    get() {
        try { return JSON.parse(localStorage.getItem(this.key)) || []; }
        catch { return []; }
    },

    save(items) {
        localStorage.setItem(this.key, JSON.stringify(items));
        Cart.updateBadge();
    },

    add(product) {
        const items = this.get();
        const idx = items.findIndex(i => i.id == product.id);
        if (idx > -1) {
            items[idx].qty += product.qty;
            items[idx].price = product.price; // precio actual (puede ser docena)
            items[idx].note = product.note || items[idx].note;
            items[idx].logo_url = product.logo_url || items[idx].logo_url;
        } else {
            items.push({ ...product });
        }
        this.save(items);
    },

    remove(id) {
        this.save(this.get().filter(i => i.id != id));
    },

    clear() { localStorage.removeItem(this.key); Cart.updateBadge(); },

    total() {
        return this.get().reduce((s, i) => s + (i.price * i.qty), 0);
    },

    count() {
        return this.get().reduce((s, i) => s + i.qty, 0);
    },

    updateBadge() {
        const b = document.getElementById('cart-badge');
        if (b) {
            const n = Cart.count();
            b.textContent = n;
            b.style.display = n > 0 ? 'flex' : 'none';
        }
        // Actualizar panel flotante si está abierto
        if (document.getElementById('cart-panel')?.classList.contains('open')) {
            Cart.renderPanel();
        }
    },

    renderPanel() {
        const list = document.getElementById('cart-items-list');
        const foot = document.getElementById('cart-total-text');
        if (!list) return;

        const items = this.get();
        if (items.length === 0) {
            list.innerHTML = `<div class="text-center text-muted py-5">
                <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
                <p>Tu carrito está vacío</p>
            </div>`;
        } else {
            list.innerHTML = items.map(item => `
                <div class="cart-item">
                    <img class="cart-item-img" src="${item.image || 'https://placehold.co/54x54/eee/999?text=?'}"
                         onerror="this.src='https://placehold.co/54x54/eee/999?text=?'" alt="${item.name}">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">
                            ${item.qty} × $${parseFloat(item.price).toFixed(2)}
                            ${item.note ? `<br><small class="text-muted">📝 ${item.note.substring(0, 30)}${item.note.length > 30 ? '…' : ''}</small>` : ''}
                        </div>
                    </div>
                    <div class="fw-bold text-end" style="min-width:64px">
                        $${(item.price * item.qty).toFixed(2)}
                    </div>
                    <button class="cart-item-remove ms-1" onclick="Cart.remove(${item.id})" title="Quitar">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            `).join('');
        }

        if (foot) foot.textContent = '$' + Cart.total().toFixed(2);
    }
};

// ─────────────────────────────────────────
//  ABRIR / CERRAR PANEL DE CARRITO
// ─────────────────────────────────────────
function openCart() {
    document.getElementById('cart-panel')?.classList.add('open');
    document.getElementById('cart-overlay')?.classList.add('show');
    Cart.renderPanel();
}
function closeCart() {
    document.getElementById('cart-panel')?.classList.remove('open');
    document.getElementById('cart-overlay')?.classList.remove('show');
}

// ─────────────────────────────────────────
//  MODAL DE PRODUCTO
// ─────────────────────────────────────────
function openProductModal(data) {
    // data = { id, name, description, price_unit, price_dozen, price_combo,
    //          is_digital, allow_note, allow_logo, images[], category }
    const modal = document.getElementById('productModal');
    const mEl = bootstrap.Modal.getOrCreateInstance(modal);

    // Galería
    const mainImg = modal.querySelector('#modal-main-img');
    const thumbs = modal.querySelector('#modal-thumbs');
    const imgs = data.images && data.images.length ? data.images : ['https://placehold.co/400x400/eee/999?text=Sin+imagen'];

    mainImg.src = imgs[0];
    thumbs.innerHTML = imgs.map((src, i) => `
        <img src="${src}" onerror="this.src='https://placehold.co/60x60/eee/999?text=?'"
             class="gallery-thumb-item ${i === 0 ? 'active' : ''}"
             onclick="switchThumb(this,'${src.replace(/'/g, "\\'")}')" alt="foto ${i + 1}">
    `).join('');

    // Info
    modal.querySelector('#modal-title').textContent = data.name;
    modal.querySelector('#modal-category').textContent = data.category || '';
    modal.querySelector('#modal-desc').textContent = data.description || 'Sin descripción.';

    // Precios
    const priceUnit = parseFloat(data.price_unit) || 0;
    const priceDozen = parseFloat(data.price_dozen) || 0;
    const priceCombo = parseFloat(data.price_combo) || 0;

    modal.querySelector('#modal-price-unit').textContent = '$' + priceUnit.toFixed(2);
    modal.querySelector('#modal-price-dozen').textContent = priceDozen > 0 ? ' /und (x12: $' + priceDozen.toFixed(2) + ')' : '';
    if (priceCombo > 0) {
        modal.querySelector('#modal-price-combo').textContent = 'Combo: $' + priceCombo.toFixed(2);
        modal.querySelector('#modal-price-combo').style.display = '';
    } else {
        modal.querySelector('#modal-price-combo').style.display = 'none';
    }

    // Tipo
    const isDigital = !!data.is_digital;
    modal.querySelector('#modal-badge').textContent = isDigital ? 'Diseño Digital' : 'Artículo Físico';
    modal.querySelector('#modal-badge').className = isDigital
        ? 'badge rounded-pill bg-primary me-2'
        : 'badge rounded-pill bg-success me-2';

    // Personalización del cliente
    modal.querySelector('#note-group').style.display = data.allow_note ? 'block' : 'none';
    modal.querySelector('#logo-url-group').style.display = data.allow_logo ? 'block' : 'none';
    modal.querySelector('#client-note').value = '';
    modal.querySelector('#client-logo-url').value = '';

    // Cantidad y precio dinámico
    const qtyInput = modal.querySelector('#modal-qty');
    const priceDisp = modal.querySelector('#modal-price-display');
    const dozenBadge = modal.querySelector('#modal-dozen-badge');
    qtyInput.value = 1;

    function updatePrice() {
        const q = parseInt(qtyInput.value) || 1;
        let unitPrice = priceUnit;
        if (priceDozen > 0 && q >= 12) {
            unitPrice = priceDozen;
            dozenBadge.classList.add('visible');
            priceDisp.classList.add('price-dozen');
        } else {
            dozenBadge.classList.remove('visible');
            priceDisp.classList.remove('price-dozen');
        }
        priceDisp.textContent = '$' + (unitPrice * q).toFixed(2);
        // Guardar en el botón de añadir
        modal.querySelector('#btn-add-to-cart').dataset.currentPrice = unitPrice;
        modal.querySelector('#btn-add-to-cart').dataset.currentQty = q;
    }
    qtyInput.removeEventListener('input', qtyInput._priceUpdater || (() => { }));
    qtyInput._priceUpdater = updatePrice;
    qtyInput.addEventListener('input', updatePrice);
    updatePrice();

    // Botones + / –
    modal.querySelector('#qty-minus').onclick = () => { if (parseInt(qtyInput.value) > 1) { qtyInput.value--; updatePrice(); } };
    modal.querySelector('#qty-plus').onclick = () => { qtyInput.value = parseInt(qtyInput.value) + 1; updatePrice(); };

    // Botón Añadir al carrito
    const addBtn = modal.querySelector('#btn-add-to-cart');
    addBtn.dataset.productId = data.id;
    addBtn.dataset.productName = data.name;
    addBtn.dataset.productImg = imgs[0];
    addBtn.dataset.isDigital = isDigital ? '1' : '0';
    addBtn.onclick = () => {
        const note = modal.querySelector('#client-note').value.trim();
        const logoUrl = modal.querySelector('#client-logo-url').value.trim();
        // Validar campos obligatorios
        if (data.allow_note && !note) {
            Swal.fire('Falta información', 'Por favor completa la nota de personalización.', 'warning');
            return;
        }
        const product = {
            id: parseInt(addBtn.dataset.productId),
            name: addBtn.dataset.productName,
            price: parseFloat(addBtn.dataset.currentPrice),
            qty: parseInt(addBtn.dataset.currentQty),
            image: addBtn.dataset.productImg,
            is_digital: addBtn.dataset.isDigital === '1',
            note: note,
            logo_url: logoUrl
        };
        Cart.add(product);
        mEl.hide();
        openCart();
        Swal.fire({
            icon: 'success', title: '¡Añadido!',
            text: `${product.name} × ${product.qty} añadido al pedido.`,
            timer: 2000, showConfirmButton: false,
            toast: true, position: 'top-end', timerProgressBar: true
        });
    };

    // WhatsApp
    const phone = (typeof COMPANY !== 'undefined' && COMPANY.whatsapp) ? COMPANY.whatsapp : '';
    modal.querySelector('#btn-whatsapp').onclick = () => {
        const msg = encodeURIComponent(`Hola, me interesa el producto *${data.name}*. ¿Me pueden dar más información?`);
        const url = phone ? `https://wa.me/${phone}?text=${msg}` : `https://wa.me/?text=${msg}`;
        window.open(url, '_blank');
    };

    // Social Share
    const shareUrl = encodeURIComponent(window.location.href);
    const shareText = encodeURIComponent(data.name);
    modal.querySelector('#btn-share-fb').onclick = () =>
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`, '_blank');
    modal.querySelector('#btn-share-wa').onclick = () =>
        window.open(`https://wa.me/?text=${shareText}%20${shareUrl}`, '_blank');
    modal.querySelector('#btn-share-pi').onclick = () =>
        window.open(`https://pinterest.com/pin/create/button/?url=${shareUrl}&description=${shareText}`, '_blank');

    mEl.show();
}

function switchThumb(el, src) {
    document.querySelectorAll('.gallery-thumb-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const main = document.getElementById('modal-main-img');
    main.style.opacity = '0';
    setTimeout(() => { main.src = src; main.style.opacity = '1'; }, 150);
}

// ─────────────────────────────────────────
//  CHECKOUT
// ─────────────────────────────────────────
function openCheckout() {
    const items = Cart.get();
    if (items.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos antes de continuar.', 'warning');
        return;
    }
    const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    // Llenar tabla de resumen
    const tbody = document.querySelector('#checkout-summary-body');
    tbody.innerHTML = items.map(i => `
        <tr>
            <td>${i.name}${i.note ? '<br><small class="text-muted">📝 ' + i.note + '</small>' : ''}${i.logo_url ? '<br><small class="text-muted">🔗 <a href="' + i.logo_url + '" target="_blank">Ver logo</a></small>' : ''}</td>
            <td class="text-center">${i.qty}</td>
            <td class="text-end">$${parseFloat(i.price).toFixed(2)}</td>
            <td class="text-end fw-bold">$${(i.price * i.qty).toFixed(2)}</td>
        </tr>
    `).join('');
    document.getElementById('checkout-total-display').textContent = '$' + Cart.total().toFixed(2);
    modal.show();
}

function generateProforma() {
    const name = document.getElementById('co-name').value.trim();
    const email = document.getElementById('co-email').value.trim();
    const city = document.getElementById('co-city').value.trim();
    const phone = document.getElementById('co-phone').value.trim();
    const addr = document.getElementById('co-address').value.trim();

    if (!name || !email || !city) {
        Swal.fire('Datos incompletos', 'Completa Nombre, Email y Ciudad.', 'warning');
        return;
    }

    const items = Cart.get();
    const total = Cart.total();
    const now = new Date().toLocaleDateString('es-EC', { year: 'numeric', month: 'long', day: 'numeric' });
    const logo = (typeof COMPANY !== 'undefined' && COMPANY.logo) ? `<img src="${COMPANY.logo}" style="max-height:60px;max-width:200px;object-fit:contain;" alt="Logo">` : '';
    const coName = (typeof COMPANY !== 'undefined' && COMPANY.name) ? COMPANY.name : 'Catálogo Láser';
    const proformaNum = 'PRF-' + Date.now().toString().slice(-6);

    const html = `
    <div class="proforma-card" id="proforma-print">
        <div class="proforma-header">
            <div>
                ${logo}
                <h4 class="mt-2 mb-1" style="color:var(--primary)">${coName}</h4>
                <small style="color:#888">Corte Láser | Sublimación | Diseños Digitales</small>
            </div>
            <div class="text-end">
                <h5 style="color:var(--primary);font-weight:700">PROFORMA</h5>
                <div style="font-size:.9rem;color:#555">${proformaNum}<br>${now}</div>
            </div>
        </div>
        <div class="row mb-4" style="font-size:.9rem">
            <div class="col-6">
                <strong>Cliente:</strong> ${name}<br>
                <strong>Email:</strong> ${email}<br>
                <strong>Ciudad:</strong> ${city}
                ${phone ? '<br><strong>Teléfono:</strong> ' + phone : ''}
                ${addr ? '<br><strong>Dirección:</strong> ' + addr : ''}
            </div>
        </div>
        <table class="table proforma-table table-bordered">
            <thead>
                <tr>
                    <th style="width:40%">Producto</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-end">Precio Unit.</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                ${items.map(i => `<tr>
                    <td>${i.name}${i.note ? '<br><small style="color:#777">Nota: ' + i.note + '</small>' : ''}${i.logo_url ? '<br><small style="color:#777">Logo: ' + i.logo_url + '</small>' : ''}</td>
                    <td class="text-center">${i.qty}</td>
                    <td class="text-end">$${parseFloat(i.price).toFixed(2)}</td>
                    <td class="text-end fw-bold">$${(i.price * i.qty).toFixed(2)}</td>
                </tr>`).join('')}
            </tbody>
        </table>
        <div class="text-end mb-4">
            <span class="proforma-total">TOTAL: $${total.toFixed(2)}</span>
        </div>
        <div style="color:#888;font-size:.8rem;border-top:1px solid #eee;padding-top:12px">
            Esta proforma es válida por 48 horas. Los precios pueden cambiar sin previo aviso.
        </div>
    </div>`;

    const proformaModal = new bootstrap.Modal(document.getElementById('proformaModal'));
    document.getElementById('proforma-container').innerHTML = html;
    bootstrap.Modal.getInstance(document.getElementById('checkoutModal'))?.hide();
    proformaModal.show();
}

function downloadProforma() {
    const el = document.getElementById('proforma-print');
    if (!el) return;

    Swal.fire({ title: 'Generando imagen…', didOpen: () => Swal.showLoading() });

    // html2canvas debe estar cargado vía CDN en el footer
    if (typeof html2canvas === 'undefined') {
        Swal.fire('Error', 'La librería html2canvas no está disponible.', 'error');
        return;
    }

    html2canvas(el, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(canvas => {
        Swal.close();
        const a = document.createElement('a');
        a.download = 'proforma-' + Date.now() + '.png';
        a.href = canvas.toDataURL('image/png');
        a.click();
    }).catch(() => Swal.fire('Error', 'No se pudo generar la imagen.', 'error'));
}

// ─────────────────────────────────────────
//  PORTAFOLIO – Lightbox
// ─────────────────────────────────────────
function openPortfolioItem(imgSrc, title, description) {
    Swal.fire({
        imageUrl: imgSrc,
        imageAlt: title,
        title: title || '',
        text: description || '',
        showCloseButton: true,
        showConfirmButton: false,
        width: '90vw',
        customClass: { popup: 'rounded-4', image: 'rounded-3' }
    });
}

// ─────────────────────────────────────────
//  INIT
// ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

    // Inicializar badge
    Cart.updateBadge();

    // Carrito Panel – botón flotante y botón del navbar
    document.querySelectorAll('[data-open-cart]').forEach(b =>
        b.addEventListener('click', e => { e.preventDefault(); openCart(); })
    );
    document.getElementById('cart-overlay')?.addEventListener('click', closeCart);
    document.getElementById('btn-close-cart')?.addEventListener('click', closeCart);

    // Botón de checkout desde panel
    document.getElementById('btn-checkout')?.addEventListener('click', () => {
        closeCart();
        setTimeout(openCheckout, 350);
    });

    // Form checkout → generar proforma
    document.getElementById('checkout-form')?.addEventListener('submit', e => {
        e.preventDefault();
        generateProforma();
    });

    // Botón descargar proforma
    document.getElementById('btn-download-proforma')?.addEventListener('click', downloadProforma);

    // Botón vaciar carrito desde panel
    document.getElementById('btn-clear-cart')?.addEventListener('click', () => {
        Swal.fire({
            title: '¿Vaciar carrito?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Sí, vaciar', cancelButtonText: 'No'
        }).then(r => { if (r.isConfirmed) { Cart.clear(); Cart.renderPanel(); } });
    });
});
