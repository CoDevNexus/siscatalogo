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
                        <div class="mb-3" id="modal-qty-group">
                            <label class="form-label small fw-bold text-muted mb-2">Cantidad</label>
                            <div class="qty-selector">
                                <button type="button" id="qty-minus">−</button>
                                <input type="number" id="modal-qty" value="1" min="1" max="999">
                                <button type="button" id="qty-plus">+</button>
                            </div>
                            <div id="modal-qty-help" class="form-text">Añade ≥12 para precio de docena.</div>
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
                        <div class="col-md-12" id="div-co-id" style="display:none;">
                            <label class="form-label small fw-bold text-muted">Cédula o RUC *</label>
                            <input type="text" id="co-id" class="form-control" placeholder="1234567890">
                        </div>
                        <div class="col-md-12" id="div-co-address" style="display:none;">
                            <label class="form-label small fw-bold text-muted">Dirección de Entrega *</label>
                            <input type="text" id="co-address" class="form-control"
                                placeholder="Calle, Número, Referencia">
                        </div>
                        <div class="col-md-12">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check bg-light p-3 rounded-3 border h-100">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" id="co-shipping">
                                        <label class="form-check-label fw-bold text-dark" for="co-shipping">
                                            <i class="bi bi-truck me-1 text-primary"></i> Solicitar Envío (+$<span
                                                id="co-shipping-val">0.00</span>)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check bg-light p-3 rounded-3 border h-100">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" id="co-invoice">
                                        <label class="form-check-label fw-bold text-dark" for="co-invoice">
                                            <i class="bi bi-file-earmark-spreadsheet me-1 text-success"></i> Necesito
                                            Factura
                                        </label>
                                        <div class="small text-muted mt-1" style="font-size: 0.73rem;">Se aplicará IVA
                                            sobre el total de su proforma.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-light rounded-4 border border-primary border-opacity-10">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Subtotal:</span>
                            <span id="checkout-subtotal-display" class="fw-bold">$0.00</span>
                        </div>
                        <div id="div-checkout-shipping" class="justify-content-between mb-1 text-primary"
                            style="display:none;">
                            <span class="small">Envío (Courier):</span>
                            <span id="checkout-shipping-display" class="fw-bold">$0.00</span>
                        </div>
                        <div id="div-checkout-tax" class="justify-content-between mb-1 text-success"
                            style="display:none;">
                            <span class="small">IVA:</span>
                            <span id="checkout-tax-display" class="fw-bold">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                            <span class="h5 mb-0 fw-bold">TOTAL:</span>
                            <span id="checkout-total-display" class="h5 mb-0 fw-bold text-primary">$0.00</span>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold">
                            <i class="bi bi-cart-check me-2"></i>Generar Pedido
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
                    <i class="bi bi-download me-2"></i>Descargar Imagen
                </button>
                <button id="btn-copy-proforma" class="btn btn-info rounded-pill px-4 fw-bold text-white">
                    <i class="bi bi-clipboard me-2"></i>Copiar Imagen
                </button>
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                    data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>