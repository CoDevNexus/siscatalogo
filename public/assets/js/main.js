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
            // Si es digital, mantenemos cantidad 1. Si es físico, sumamos.
            if (product.is_digital) {
                items[idx].qty = 1;
            } else {
                items[idx].qty += product.qty;
            }
            items[idx].price = product.price;
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
        const bf = document.getElementById('cart-float-count');
        const n = Cart.count();

        if (b) {
            b.textContent = n;
            b.style.display = n > 0 ? 'flex' : 'none';
        }
        if (bf) {
            bf.textContent = n;
            bf.style.display = n > 0 ? 'flex' : 'none';
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
    const priceDozenEl = modal.querySelector('#modal-price-dozen');
    modal.querySelector('#modal-badge').textContent = isDigital ? 'Diseño Digital' : 'Artículo Físico';
    modal.querySelector('#modal-badge').className = isDigital
        ? 'badge rounded-pill bg-primary me-2'
        : 'badge rounded-pill bg-success me-2';

    // Ocultar precio docena si es digital
    if (priceDozenEl) priceDozenEl.style.display = isDigital ? 'none' : '';

    // Personalización del cliente
    modal.querySelector('#note-group').style.display = data.allow_note ? 'block' : 'none';
    modal.querySelector('#logo-url-group').style.display = data.allow_logo ? 'block' : 'none';
    modal.querySelector('#client-note').value = '';
    modal.querySelector('#client-logo-url').value = '';

    // Cantidad y precio dinámico
    const qtyInput = modal.querySelector('#modal-qty');
    const priceDisp = modal.querySelector('#modal-price-display');
    const dozenBadge = modal.querySelector('#modal-dozen-badge');
    const qtyGroup = modal.querySelector('#modal-qty-group');

    qtyInput.value = 1;

    // Si es digital, ocultamos el grupo de cantidad completo
    if (qtyGroup) qtyGroup.style.display = isDigital ? 'none' : 'block';

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

        // Animación breve del botón flotante para dar feedback extra
        const floatBtn = document.querySelector('.cart-float-btn');
        if (floatBtn) {
            floatBtn.style.transform = 'scale(1.2)';
            setTimeout(() => floatBtn.style.transform = '', 200);
        }

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
        const productUrl = window.location.origin + window.location.pathname + '?id=' + data.id;
        const msg = encodeURIComponent(`Hola, me interesa el producto *${data.name}*. \n\nEnlace: ${productUrl}\n\n¿Me pueden dar más información?`);
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
    const total = Cart.total();
    document.getElementById('checkout-total-display').textContent = '$' + total.toFixed(2);

    // Costo de envío e IVA
    const shippingCost = (typeof COMPANY !== 'undefined' && COMPANY.shipping_cost) ? parseFloat(COMPANY.shipping_cost) : 0;
    const taxRate = (typeof COMPANY !== 'undefined' && COMPANY.tax_rate) ? parseFloat(COMPANY.tax_rate) : 0;
    const shippingCheck = document.getElementById('co-shipping');
    const invoiceCheck = document.getElementById('co-invoice');

    const divCoId = document.getElementById('div-co-id');
    const divCoAddress = document.getElementById('div-co-address');

    const divTax = document.getElementById('div-checkout-tax');
    const divShipping = document.getElementById('div-checkout-shipping');

    document.getElementById('checkout-subtotal-display').textContent = '$' + total.toFixed(2);
    if (document.getElementById('co-shipping-val')) {
        document.getElementById('co-shipping-val').textContent = shippingCost.toFixed(2);
    }

    const updateTotal = () => {
        const isShipping = shippingCheck?.checked || false;
        const isInvoice = invoiceCheck?.checked || false;

        // Visibilidad de campos
        if (divCoAddress) divCoAddress.style.display = isShipping ? 'block' : 'none';
        if (divCoId) divCoId.style.display = (isShipping || isInvoice) ? 'block' : 'none';

        // Visibilidad y valores de desglose
        if (divShipping) {
            divShipping.style.display = isShipping ? 'flex' : 'none';
            document.getElementById('checkout-shipping-display').textContent = '$' + shippingCost.toFixed(2);
        }

        let subtotalConEnvio = total + (isShipping ? shippingCost : 0);
        let taxAmount = 0;

        if (isInvoice) {
            taxAmount = subtotalConEnvio * (taxRate / 100);
            if (divTax) {
                divTax.style.display = 'flex';
                document.getElementById('checkout-tax-display').textContent = '$' + taxAmount.toFixed(2);
            }
        } else if (divTax) {
            divTax.style.display = 'none';
        }

        const finalTotal = subtotalConEnvio + taxAmount;
        document.getElementById('checkout-total-display').textContent = '$' + finalTotal.toFixed(2);
    };

    if (shippingCheck) {
        shippingCheck.checked = false;
        shippingCheck.onchange = updateTotal;
    }
    if (invoiceCheck) {
        invoiceCheck.checked = false;
        invoiceCheck.onchange = updateTotal;
    }

    updateTotal(); // Ejecución inicial
    modal.show();
}

function generateProforma() {
    const name = document.getElementById('co-name').value.trim();
    const email = document.getElementById('co-email').value.trim();
    const city = document.getElementById('co-city').value.trim();
    const phone = document.getElementById('co-phone').value.trim();
    const cid = document.getElementById('co-id').value.trim();
    const addr = document.getElementById('co-address').value.trim();

    const isShipping = document.getElementById('co-shipping')?.checked;
    const isInvoice = document.getElementById('co-invoice')?.checked;

    if (!name || !email || !city) {
        Swal.fire('Datos incompletos', 'Completa Nombre, Email y Ciudad.', 'warning');
        return;
    }

    // Validar condicionales
    if ((isShipping || isInvoice) && !cid) {
        Swal.fire('Atención', 'Por favor ingresa tu Cédula o RUC.', 'warning');
        return;
    }
    if (isShipping && !addr) {
        Swal.fire('Atención', 'Por favor ingresa la dirección de entrega.', 'warning');
        return;
    }

    const items = Cart.get();
    const subtotal = Cart.total();
    const shippingCost = isShipping ? ((typeof COMPANY !== 'undefined' && COMPANY.shipping_cost) ? parseFloat(COMPANY.shipping_cost) : 0) : 0;
    const taxRate = isInvoice ? ((typeof COMPANY !== 'undefined' && COMPANY.tax_rate) ? parseFloat(COMPANY.tax_rate) : 0) : 0;

    let subtotalConEnvio = subtotal + shippingCost;
    const taxAmount = isInvoice ? (subtotalConEnvio * (taxRate / 100)) : 0;
    const total = subtotalConEnvio + taxAmount;

    const now = new Date().toLocaleDateString('es-EC', { year: 'numeric', month: 'long', day: 'numeric' });
    const logo = (typeof COMPANY !== 'undefined' && COMPANY.logo) ? `<img src="${COMPANY.logo}" style="max-height:60px;max-width:200px;object-fit:contain;" alt="Logo">` : '';
    const coName = (typeof COMPANY !== 'undefined' && COMPANY.name) ? COMPANY.name : 'Catálogo Láser';
    const eslogan = (typeof COMPANY !== 'undefined' && COMPANY.eslogan) ? COMPANY.eslogan : 'Corte Láser | Sublimación | Diseños Digitales';
    const proformaNum = 'PRF-' + Date.now().toString().slice(-6);

    const companyInfoHtml = typeof COMPANY !== 'undefined' ? `
        <div style="font-size:0.8rem;color:#666;line-height:1.2;margin-top:5px;">
            ${COMPANY.ruc ? `<div>RUC/NIT: ${COMPANY.ruc}</div>` : ''}
            ${COMPANY.address ? `<div>${COMPANY.address}</div>` : ''}
            ${COMPANY.phone ? `<div>Tel: ${COMPANY.phone}</div>` : ''}
            ${COMPANY.email ? `<div>${COMPANY.email}</div>` : ''}
        </div>
    ` : '';

    const footerExtras = typeof COMPANY !== 'undefined' ? `
        ${COMPANY.thanks ? `<strong>${COMPANY.thanks}</strong><br>` : ''}
        ${COMPANY.terms ? `<em>${COMPANY.terms}</em><br>` : ''}
    ` : '';

    // Función para generar HTML ahora acepta num de pedido
    const generateReceiptHtml = (orderNumCode) => `
    <div class="proforma-card" id="proforma-print">
        <div class="proforma-header d-flex justify-content-between">
            <div>
                ${logo}
                <h4 class="mt-2 mb-1" style="color:var(--primary)">${coName}</h4>
                <small style="color:#888">${eslogan}</small>
                ${companyInfoHtml}
            </div>
            <div class="text-end">
                <h5 style="color:var(--primary);font-weight:700">PEDIDO</h5>
                <div style="font-size:.9rem;color:#555">${orderNumCode}<br>${now}</div>
            </div>
        </div>
        <div class="row mb-4" style="font-size:.9rem">
            <div class="col-6">
                <strong>Cliente:</strong> ${name}<br>
                ${cid ? '<strong>Cédula/RUC:</strong> ' + cid + '<br>' : ''}
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
        <div class="row g-0">
            <div class="col-8">
                ${isInvoice ? `
                    <div class="alert alert-light border-0 small text-muted px-0 mt-2">
                        <i class="bi bi-info-circle me-1"></i> Se ha aplicado un <strong>${taxRate}% de IVA</strong> por concepto de facturación electrónica.
                    </div>
                ` : ''}
            </div>
            <div class="col-4 border rounded p-3 bg-light">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>$${subtotal.toFixed(2)}</span>
                </div>
                ${isShipping ? `
                <div class="d-flex justify-content-between mb-2 text-primary">
                    <span>Envío (Courier):</span>
                    <span>$${shippingCost.toFixed(2)}</span>
                </div>
                ` : ''}
                ${isInvoice ? `
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>IVA (${taxRate}%):</span>
                    <span>$${taxAmount.toFixed(2)}</span>
                </div>
                ` : ''}
                <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2" style="font-size:1.2rem">
                    <span>TOTAL:</span>
                    <span class="text-danger">$${total.toFixed(2)}</span>
                </div>
            </div>
        </div>
        </div>
        </div>
        <div style="color:#888;font-size:.8rem;border-top:1px solid #eee;padding-top:12px;text-align:center;">
            ${footerExtras}
            Este es un comprobante de su pedido. Los precios pueden cambiar sin previo aviso.
        </div>
    </div>`;

    // Preparar datos para el servidor
    const orderData = {
        customer_name: name,
        customer_id: cid,
        customer_email: email,
        customer_phone: phone,
        customer_city: city,
        customer_address: addr,
        subtotal: subtotal,
        shipping_amount: isShipping ? shippingCost : 0,
        tax_amount: isInvoice ? taxAmount : 0,
        needs_shipping: isShipping ? 1 : 0,
        needs_invoice: isInvoice ? 1 : 0,
        total_amount: total,
        items: items
    };

    // Guardar en BD vía AJAX
    let currentStep = 0;
    const steps = [
        '<div class="text-start mt-3"><div class="mb-2"><i class="bi bi-database text-primary me-2"></i>Guardando su proforma... <span class="spinner-border spinner-border-sm ms-2" style="font-size: 0.5rem"></span></div><div class="mb-2 text-muted" style="opacity:0.5"><i class="bi bi-envelope me-2"></i>Preparando copias por correo...</div><div class="text-muted" style="opacity:0.5"><i class="bi bi-telegram me-2"></i>Notificando al taller...</div></div>',
        '<div class="text-start mt-3"><div class="mb-2 text-success"><i class="bi bi-check-circle me-2"></i>Proforma guardada con éxito.<br></div><div class="mb-2"><i class="bi bi-envelope text-info me-2"></i>Preparando copias por correo... <span class="spinner-border spinner-border-sm ms-2" style="font-size: 0.5rem"></span></div><div class="text-muted" style="opacity:0.5"><i class="bi bi-telegram me-2"></i>Notificando al taller...</div></div>',
        '<div class="text-start mt-3"><div class="mb-2 text-success"><i class="bi bi-check-circle me-2"></i>Proforma guardada con éxito.</div><div class="mb-2 text-success"><i class="bi bi-check-circle me-2"></i>Copias enviadas por correo.</div><div><i class="bi bi-telegram text-info me-2"></i>Notificando al taller... <span class="spinner-border spinner-border-sm ms-2" style="font-size: 0.5rem"></span></div></div>',
        '<div class="text-start mt-3"><div class="mb-2 text-success"><i class="bi bi-check-circle me-2"></i>Proforma guardada con éxito.</div><div class="mb-2 text-success"><i class="bi bi-check-circle me-2"></i>Copias enviadas por correo.</div><div class="text-success"><i class="bi bi-check-circle me-2"></i>Taller notificado. ¡Todo listo!</div></div>'
    ];

    Swal.fire({
        title: 'Procesando pedido',
        html: steps[0],
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            // Avanzar falsamente la UI mientras carga el servidor (ya que el PHP hace todo de corrido)
            setTimeout(() => { if (Swal.isVisible() && currentStep < 1) { currentStep = 1; Swal.update({ html: steps[1] }); } }, 1200);
            setTimeout(() => { if (Swal.isVisible() && currentStep < 2) { currentStep = 2; Swal.update({ html: steps[2] }); } }, 2500);
        }
    });

    fetch(APP_URL + 'cotizacion/confirmar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData)
    })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                // Completar UI y cerrar rápido
                currentStep = 3;
                Swal.update({ html: steps[3] });

                setTimeout(() => {
                    Swal.close();

                    orderData.id = res.order_id;
                    const finalOrderNum = 'PED-' + res.order_id.toString().padStart(6, '0');
                    window.currentProformaData = orderData;
                    window.currentProformaNum = finalOrderNum;

                    const proformaModal = new bootstrap.Modal(document.getElementById('proformaModal'));
                    document.getElementById('proforma-container').innerHTML = generateReceiptHtml(finalOrderNum);

                    bootstrap.Modal.getInstance(document.getElementById('checkoutModal'))?.hide();
                    proformaModal.show();

                    Cart.clear();
                    Cart.renderPanel();
                }, 800);

            } else {
                Swal.close();
                Swal.fire('Error', res.message || 'No se pudo guardar el pedido.', 'error');
            }
        })
        .catch(err => {
            Swal.close();
            console.error(err);
            Swal.fire('Error', 'Error de comunicación local.', 'error');
        });
}

function generateExportableHtml(d, profNum) {
    const logoHtml = (typeof COMPANY !== 'undefined' && COMPANY.logo) ? `<img src="${COMPANY.logo}" style="max-height: 80px; max-width: 250px; object-fit: contain;">` : `<h2 style="margin:0;color:#0275d8;">${(typeof COMPANY !== 'undefined' && COMPANY.name) ? COMPANY.name : 'Pedido'}</h2>`;
    const co = typeof COMPANY !== 'undefined' ? COMPANY : {};

    const itemsHtml = d.items.map(i => `
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #eee; color: #333;">
                <strong>${i.name}</strong>
                ${i.note ? '<div style="font-size: 11px; color: #666; margin-top: 4px;">Nota: ' + i.note + '</div>' : ''}
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: center; color: #333;">${i.qty}</td>
            <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: right; color: #333;">$${parseFloat(i.price).toFixed(2)}</td>
            <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold; color: #333;">$${(i.qty * i.price).toFixed(2)}</td>
        </tr>
    `).join('');

    const now = new Date().toLocaleDateString('es-EC', { year: 'numeric', month: 'long', day: 'numeric' });

    return `
    <html>
    <head>
        <meta charset="utf-8">
        <title>Pedido ${profNum}</title>
    </head>
    <body>
    <div style="width: 800px; padding: 40px 50px; background: #ffffff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 30px;">
            <div style="max-width: 65%; display: flex; align-items: flex-start; gap: 20px;">
                ${logoHtml}
                <div style="font-size: 13px; color: #555; line-height: 1.4;">
                    <span style="font-size: 18px; font-weight: bold; color: #2c3e50;">${co.name ? co.name : ''}</span><br>
                    ${co.ruc ? `<strong>RUC:</strong> ${co.ruc}<br>` : ''}
                    ${co.address ? `${co.address}<br>` : ''}
                    ${co.city ? `${co.city}<br>` : ''}
                    ${co.phone ? `<strong>Tel:</strong> ${co.phone}<br>` : ''}
                    ${co.email ? `<strong>Email:</strong> ${co.email}` : ''}
                </div>
            </div>
            <div style="text-align: right;">
                <h1 style="margin: 0 0 5px 0; color: #2c3e50; font-size: 28px; font-weight: 700; letter-spacing: 1px;">PEDIDO</h1>
                <div style="font-size: 16px; color: #e74c3c; font-weight: bold;"># ${profNum}</div>
                <div style="font-size: 13px; color: #888; margin-top: 5px;">Fecha: ${now}</div>
            </div>
        </div>

        <div style="margin-bottom: 20px; background: #f8f9fa; padding: 10px 15px; border-radius: 8px; border-left: 4px solid #0275d8;">
            <h3 style="margin: 0 0 6px 0; font-size: 12px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 1px;">Facturar a:</h3>
            <div style="font-size: 13px; color: #333; line-height: 1.3; display: flex; flex-wrap: wrap; gap: 6px 30px;">
                <div style="flex: 1 1 100%;"><span style="font-size: 15px; font-weight: bold; color: #0275d8;">${d.customer_name}</span></div>
                ${d.customer_id ? `<div><strong>RUC/CI:</strong> ${d.customer_id}</div>` : ''}
                ${d.customer_phone ? `<div><strong>Teléfono:</strong> ${d.customer_phone}</div>` : ''}
                ${d.customer_email ? `<div><strong>Email:</strong> ${d.customer_email}</div>` : ''}
                ${d.customer_city ? `<div><strong>Ciudad:</strong> ${d.customer_city}</div>` : ''}
                ${d.customer_address ? `<div style="flex: 1 1 100%; margin-top:2px;"><strong>Dirección:</strong> ${d.customer_address}</div>` : ''}
            </div>
        </div>

        <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 40px; font-size: 14px; border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden;">
            <thead>
                <tr>
                    <th style="padding: 14px 15px; text-align: left; background: #2c3e50; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Descripción</th>
                    <th style="padding: 14px 15px; text-align: center; background: #2c3e50; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Cant.</th>
                    <th style="padding: 14px 15px; text-align: right; background: #2c3e50; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">P. Unit</th>
                    <th style="padding: 14px 15px; text-align: right; background: #2c3e50; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Total</th>
                </tr>
            </thead>
            <tbody>
                ${itemsHtml}
            </tbody>
        </table>

        <div style="display: flex; justify-content: flex-end;">
            <div style="width: 320px;">
                <div style="display: flex; justify-content: space-between; padding: 4px 0; font-size: 14px; color: #555;">
                    <span>Subtotal:</span>
                    <span>$${parseFloat(d.subtotal).toFixed(2)}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 4px 0; font-size: 14px; color: #555;">
                    <span>Envío:</span>
                    <span>$${parseFloat(d.shipping_amount || 0).toFixed(2)}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 4px 0; font-size: 14px; color: #555;">
                    <span>IVA (${co.tax_rate ? co.tax_rate : '0'}%):</span>
                    <span>$${parseFloat(d.tax_amount || 0).toFixed(2)}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; margin-top: 6px; border-top: 2px solid #2c3e50; font-size: 17px; font-weight: bold; color: #2c3e50;">
                    <span>TOTAL:</span>
                    <span style="color: #e74c3c;">$${parseFloat(d.total_amount).toFixed(2)}</span>
                </div>
            </div>
        </div>

        ${co.footer_image ? `<div style="margin-top: 30px; text-align: center;"><img src="${co.footer_image}" style="max-width: 100%; max-height: 120px; border-radius: 8px; object-fit: contain;"></div>` : ''}
        <div style="margin-top: ${co.footer_image ? '20px' : '40px'}; text-align: center; font-size: 11px; color: #7f8c8d; border-top: 1px solid #eee; padding-top: 15px; line-height: 1.5;">
            <div style="font-size: 13px; color: #2c3e50; margin-bottom: 5px;"><strong>${co.thanks ? co.thanks : '¡Gracias por su preferencia!'}</strong></div>
            ${co.terms ? `<em style="color:#999;">${co.terms}</em>` : '<span style="color:#999;font-size: 11px;">Pedido válido por 48 horas laborables.</span>'}
        </div>
    </div>
    </body>
    </html>`;
}

function downloadProforma() {
    const data = window.currentProformaData;
    const num = window.currentProformaNum;
    if (!data) return;

    Swal.fire({
        title: 'Generando imagen...',
        text: 'Por favor espere.',
        didOpen: () => { Swal.showLoading(); }
    });

    const container = document.createElement('div');
    container.style.position = 'absolute';
    container.style.left = '-9999px';
    container.style.top = '0';
    container.innerHTML = generateExportableHtml(data, num);
    document.body.appendChild(container);

    setTimeout(() => {
        if (typeof html2canvas === 'undefined') {
            document.body.removeChild(container);
            Swal.fire('Error', 'La librería de generación no está cargada.', 'error');
            return;
        }

        html2canvas(container.firstElementChild, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(canvas => {
            document.body.removeChild(container);
            Swal.close();

            const link = document.createElement('a');
            link.download = `Pedido_${num}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        }).catch(err => {
            if (document.body.contains(container)) document.body.removeChild(container);
            console.error(err);
            Swal.fire('Error', 'No se pudo generar la imagen.', 'error');
        });
    }, 500);
}

function copyProforma() {
    const data = window.currentProformaData;
    const num = window.currentProformaNum;
    if (!data) return;

    const btn = document.getElementById('btn-copy-proforma');
    if (!btn) return;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Copiando...';
    btn.disabled = true;

    const container = document.createElement('div');
    container.style.position = 'absolute';
    container.style.left = '-9999px';
    container.style.top = '0';
    container.innerHTML = generateExportableHtml(data, num);
    document.body.appendChild(container);

    setTimeout(() => {
        if (typeof html2canvas === 'undefined') {
            document.body.removeChild(container);
            btn.innerHTML = originalText;
            btn.disabled = false;
            Swal.fire('Error', 'La librería de generación no está cargada.', 'error');
            return;
        }

        html2canvas(container.firstElementChild, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(canvas => {
            document.body.removeChild(container);
            btn.innerHTML = originalText;
            btn.disabled = false;

            canvas.toBlob(async blob => {
                try {
                    await navigator.clipboard.write([
                        new ClipboardItem({ 'image/png': blob })
                    ]);
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: '¡Imagen copiada al portapapeles!', showConfirmButton: false, timer: 3000
                    });
                } catch (err) {
                    console.error('Error al copiar:', err);
                    Swal.fire('Atención', 'Tu navegador bloqueó el copiado automático. Usa el botón "Descargar Imagen" en su lugar.', 'warning');
                }
            });
        }).catch(err => {
            if (document.body.contains(container)) document.body.removeChild(container);
            console.error(err);
            btn.innerHTML = originalText;
            btn.disabled = false;
            Swal.fire('Error', 'No se pudo generar la imagen para copiar.', 'error');
        });
    }, 500);
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

    // ─────────────────────────────────────────
    // CARRITO ARRASTRABLE (DRAG & DROP)
    // ─────────────────────────────────────────
    const cartFloat = document.getElementById('cart-float');
    if (cartFloat) {
        let isDragging = false;
        let diffX = 0, diffY = 0;
        let wasDragged = false;

        const onDragStart = (e) => {
            // No prevenimos default si es touchstart en móviles para botones internos, solo procesamos coords
            if (e.type === 'mousedown') e.preventDefault();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            const rect = cartFloat.getBoundingClientRect();
            diffX = clientX - rect.left;
            diffY = clientY - rect.top;

            isDragging = true;
            wasDragged = false;
            cartFloat.style.transition = 'none';

            document.addEventListener('mousemove', onDragMove);
            document.addEventListener('mouseup', onDragEnd);
            document.addEventListener('touchmove', onDragMove, { passive: false });
            document.addEventListener('touchend', onDragEnd);
        };

        const onDragMove = (e) => {
            if (!isDragging) return;
            // Prevenir scroll de la página si estamos arrastrando el carrito
            if (e.type === 'touchmove') e.preventDefault();

            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            let newX = clientX - diffX;
            let newY = clientY - diffY;

            // Mantener dentro de los límites de pantalla
            newX = Math.max(0, Math.min(newX, window.innerWidth - cartFloat.offsetWidth));
            newY = Math.max(0, Math.min(newY, window.innerHeight - cartFloat.offsetHeight));

            cartFloat.style.left = newX + 'px';
            cartFloat.style.top = newY + 'px';
            cartFloat.style.right = 'auto'; // Anular ancla derecha del CSS
            cartFloat.style.bottom = 'auto'; // Anular ancla bottom del CSS

            wasDragged = true;
        };

        const onDragEnd = () => {
            if (!isDragging) return;
            isDragging = false;
            cartFloat.style.transition = ''; // Restaurar hover transitions

            document.removeEventListener('mousemove', onDragMove);
            document.removeEventListener('mouseup', onDragEnd);
            document.removeEventListener('touchmove', onDragMove);
            document.removeEventListener('touchend', onDragEnd);
        };

        cartFloat.addEventListener('mousedown', onDragStart);
        cartFloat.addEventListener('touchstart', onDragStart, { passive: false });

        // Prevenir la apertura del panel si el usuario solo estaba arrastrando
        cartFloat.addEventListener('click', (e) => {
            if (wasDragged) {
                e.preventDefault();
                e.stopImmediatePropagation(); // Evita que [data-open-cart] capture el click
            }
        }, true); // Captura temprana
    }

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

    // Botón copiar proforma
    document.getElementById('btn-copy-proforma')?.addEventListener('click', copyProforma);

    // Botón vaciar carrito desde panel
    document.getElementById('btn-clear-cart')?.addEventListener('click', () => {
        Swal.fire({
            title: '¿Vaciar carrito?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Sí, vaciar', cancelButtonText: 'No'
        }).then(r => { if (r.isConfirmed) { Cart.clear(); Cart.renderPanel(); } });
    });
});
