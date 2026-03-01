<?php
$currentPage = $pagination['current_page'];
$totalPages = $pagination['total_pages'];
$orderBy = $order['by'];
$orderDir = $order['dir'];

function sortLink($col, $currentBy, $currentDir, $filters)
{
    $dir = ($col === $currentBy && $currentDir === 'ASC') ? 'DESC' : 'ASC';
    $params = array_merge($filters, ['order_by' => $col, 'order_dir' => $dir, 'page' => 1]);
    return APP_URL . 'cotizacion/admin_index?' . http_build_query($params);
}

function pageLink($page, $filters, $orderBy, $orderDir)
{
    $params = array_merge($filters, ['order_by' => $orderBy, 'order_dir' => $orderDir, 'page' => $page]);
    return APP_URL . 'cotizacion/admin_index?' . http_build_query($params);
}
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Gestión de Pedidos</h2>
            <div class="badge bg-primary fs-6 rounded-pill px-3 py-2">
                <?= $pagination['total_items'] ?> órdenes encontradas
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center d-md-none"
            style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseFilters">
            <span class="fw-bold text-muted small"><i class="bi bi-filter"></i> Filtros de búsqueda</span>
            <i class="bi bi-chevron-down small"></i>
        </div>
        <div class="card-body p-4 collapse d-md-block" id="collapseFilters">
            <form method="GET" action="<?= APP_URL ?>cotizacion/admin_index" class="row g-3">
                <input type="hidden" name="order_by" value="<?= $orderBy ?>">
                <input type="hidden" name="order_dir" value="<?= $orderDir ?>">

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Cliente</label>
                    <input type="text" name="name" class="form-control"
                        value="<?= htmlspecialchars($filters['name']) ?>" placeholder="Nombre...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Ciudad</label>
                    <input type="text" name="city" class="form-control"
                        value="<?= htmlspecialchars($filters['city']) ?>" placeholder="Guayaquil...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <?php
                        $estados = ['Pendiente', 'En Diseño', 'En Producción', 'Listo para Entrega', 'Finalizado', 'Cancelado'];
                        foreach ($estados as $est): ?>
                            <option value="<?= $est ?>" <?= $filters['status'] === $est ? 'selected' : '' ?>><?= $est ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Desde</label>
                    <input type="date" name="date_from" class="form-control"
                        value="<?= htmlspecialchars($filters['date_from']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Hasta</label>
                    <input type="date" name="date_to" class="form-control"
                        value="<?= htmlspecialchars($filters['date_to']) ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">
                                <a href="<?= sortLink('id', $orderBy, $orderDir, $filters) ?>"
                                    class="text-decoration-none text-dark">
                                    ID <?= $orderBy === 'id' ? ($orderDir === 'ASC' ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= sortLink('customer_name', $orderBy, $orderDir, $filters) ?>"
                                    class="text-decoration-none text-dark">
                                    Cliente <?= $orderBy === 'customer_name' ? ($orderDir === 'ASC' ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell">
                                <a href="<?= sortLink('customer_city', $orderBy, $orderDir, $filters) ?>"
                                    class="text-decoration-none text-dark">
                                    Ciudad <?= $orderBy === 'customer_city' ? ($orderDir === 'ASC' ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= sortLink('total_amount', $orderBy, $orderDir, $filters) ?>"
                                    class="text-decoration-none text-dark">
                                    Total <?= $orderBy === 'total_amount' ? ($orderDir === 'ASC' ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= sortLink('status', $orderBy, $orderDir, $filters) ?>"
                                    class="text-decoration-none text-dark">
                                    Estado <?= $orderBy === 'status' ? ($orderDir === 'ASC' ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                            <th class="d-none d-lg-table-cell">
                                <a href="<?= sortLink('created_at', $orderBy, $orderDir, $filters) ?>"
                                    class="text-decoration-none text-dark">
                                    Fecha <?= $orderBy === 'created_at' ? ($orderDir === 'ASC' ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cotizaciones)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay pedidos que coincidan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cotizaciones as $c): ?>
                                <?php
                                $statusClass = [
                                    'Pendiente' => 'bg-warning text-dark',
                                    'En Diseño' => 'bg-info text-white',
                                    'En Producción' => 'bg-primary text-white',
                                    'Listo para Entrega' => 'bg-success text-white',
                                    'Finalizado' => 'bg-secondary text-white',
                                    'Cancelado' => 'bg-danger text-white'
                                ][$c['status']] ?? 'bg-light';
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?= $c['id'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($c['customer_name']) ?></div>
                                        <div class="small text-muted d-none d-sm-block">
                                            <?= htmlspecialchars($c['customer_email']) ?>
                                        </div>
                                        <div class="mt-1">
                                            <?php if (isset($c['has_digital']) && $c['has_digital'] > 0): ?>
                                                <?php if ($c['digital_approved']): ?>
                                                    <span class="badge bg-soft-info text-info border border-info border-opacity-25 py-1"
                                                        title="Accesos Digitales Entregados">
                                                        <i class="bi bi-cloud-check-fill"></i> Dig. Entregado
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-soft-warning text-warning border border-warning border-opacity-25 py-1"
                                                        title="Entrega Digital Pendiente">
                                                        <i class="bi bi-cloud-arrow-up"></i> Dig. Pendiente
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if ($c['needs_shipping']): ?>
                                                <span
                                                    class="badge bg-soft-primary text-primary border border-primary border-opacity-25 py-1"
                                                    title="Requiere Envío">
                                                    <i class="bi bi-truck"></i> Envío
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($c['needs_invoice']): ?>
                                                <span
                                                    class="badge bg-soft-success text-success border border-success border-opacity-25 py-1"
                                                    title="Requiere Factura">
                                                    <i class="bi bi-receipt"></i> Factura
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell text-muted">
                                        <?= htmlspecialchars($c['customer_city']) ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">$<?= number_format($c['total_amount'], 2) ?></div>
                                        <?php if ($c['tax_amount'] > 0): ?>
                                            <div class="text-success x-small" style="font-size: 0.7rem;">(Incluye IVA)</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill <?= $statusClass ?> px-3">
                                            <?= $c['status'] ?>
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell small text-muted">
                                        <?= date('d/m/Y', strtotime($c['created_at'])) ?><br>
                                        <?= date('H:i', strtotime($c['created_at'])) ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                            <button class="btn btn-sm btn-white border" onclick="verDetalle(<?= $c['id'] ?>)"
                                                title="Ver">
                                                <i class="bi bi-eye text-primary"></i>
                                            </button>
                                            <button class="btn btn-sm btn-white border"
                                                onclick="verDetalle(<?= $c['id'] ?>, true)" title="Editar">
                                                <i class="bi bi-pencil text-success"></i>
                                            </button>
                                            <?php if (isset($c['has_digital']) && $c['has_digital'] > 0 && !$c['digital_approved']): ?>
                                                <button class="btn btn-sm btn-white border"
                                                    onclick="aprobarDigital(<?= $c['id'] ?>)" title="Aprobar Entrega Digital">
                                                    <i class="bi bi-cloud-arrow-up-fill text-success"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-white border"
                                                onclick="quickAction(event, <?= $c['id'] ?>, 'pdf')" title="Descargar PDF">
                                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            </button>
                                            <button class="btn btn-sm btn-white border"
                                                onclick="quickAction(event, <?= $c['id'] ?>, 'email')" title="Enviar por Email">
                                                <i class="bi bi-envelope text-info"></i>
                                            </button>
                                            <button class="btn btn-sm btn-white border"
                                                onclick="quickAction(event, <?= $c['id'] ?>, 'image')" title="Descargar Imagen">
                                                <i class="bi bi-image text-secondary"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
            <div class="card-footer bg-white border-top-0 p-4">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link shadow-none"
                                href="<?= pageLink($currentPage - 1, $filters, $orderBy, $orderDir) ?>">Anterior</a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link shadow-none"
                                    href="<?= pageLink($i, $filters, $orderBy, $orderDir) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link shadow-none"
                                href="<?= pageLink($currentPage + 1, $filters, $orderBy, $orderDir) ?>">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.08);
    }

    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.08);
    }

    .x-small {
        font-size: 0.75rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    /* Estilos para exportación limpia */
    .export-mode input,
    .export-mode textarea,
    .export-mode select {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        appearance: none !important;
        -moz-appearance: none !important;
        -webkit-appearance: none !important;
        box-shadow: none !important;
        color: #333 !important;
        pointer-events: none !important;
    }

    .export-mode .btn-sm.rounded-circle,
    .export-mode .no-export {
        display: none !important;
    }
</style>

<!-- Modal Detalle Responsivo -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 bg-light rounded-top-4 p-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-primary" id="detalle-title">Detalle de Cotización</h5>
                    <small class="text-muted" id="detalle-subtitle">Información general del pedido</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="detalle-content-wrapper" style="max-height: 80vh; overflow-y: auto;">
                <div id="detalle-content" class="p-4">
                    <!-- Se llena vía JS -->
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 rounded-bottom-4 p-3 d-flex flex-wrap gap-2">
                <div class="me-auto d-flex flex-wrap gap-2" id="modal-actions-left">
                    <!-- Botones aprobación -->
                </div>
                <div id="modal-actions-right" class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-none"
                        id="btn-edit-order">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentOrder = null;
    let isEditing = false;

    function quickAction(e, id, action) {
        const btn = e.currentTarget;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled = true;

        fetch(`${APP_URL}cotizacion/get_detalle/${id}`)
            .then(r => r.json())
            .then(res => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                if (res.status === 'success') {
                    currentOrder = res.data;
                    if (action === 'pdf') exportOrderToPdf(btn);
                    else if (action === 'email') enviarPorEmail(btn);
                    else if (action === 'image') exportOrderToImage('download', btn);
                } else {
                    Swal.fire('Error', 'No se pudo cargar la cotización', 'error');
                }
            }).catch(err => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                console.error(err);
                Swal.fire('Error', 'Error de red', 'error');
            });
    }

    function verDetalle(id, autoEdit = false) {
        if (typeof bootstrap === 'undefined') {
            Swal.fire('Error', 'Los componentes visuales aún están cargando. Por favor, intenta de nuevo en un segundo o recarga la página.', 'error');
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
        const content = document.getElementById('detalle-content');
        const actionsLeft = document.getElementById('modal-actions-left');
        const btnEdit = document.getElementById('btn-edit-order');

        isEditing = false;
        if (btnEdit) {
            btnEdit.innerHTML = '<i class="bi bi-pencil me-1"></i>Editar';
            btnEdit.classList.remove('btn-success');
            btnEdit.classList.add('btn-outline-primary');
            if (autoEdit) {
                btnEdit.classList.remove('d-none');
            } else {
                btnEdit.classList.add('d-none');
            }
        }

        content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Cargando datos...</p></div>';
        actionsLeft.innerHTML = '';
        modal.show();

        fetch(`${APP_URL}cotizacion/get_detalle/${id}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    currentOrder = res.data;

                    if (autoEdit) {
                        startEditing();
                    } else {
                        renderOrderView();
                    }

                    // Acción del botón editar
                    if (btnEdit) {
                        btnEdit.onclick = () => {
                            if (!isEditing) {
                                startEditing();
                            } else {
                                saveChanges();
                            }
                        };
                    }

                    // Botones Digitales
                    updateDigitalButtons();
                }
            });
    }

    function renderOrderView() {
        const d = currentOrder;
        const content = document.getElementById('detalle-content');
        document.getElementById('detalle-title').textContent = `Pedido #${d.id}`;

        let itemsHtml = d.items.map(i => `
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div>
                    <h6 class="mb-0 fw-bold">${i.product_name}</h6>
                    <small class="text-muted d-block mt-1">Cantidad: ${i.quantity} x $${parseFloat(i.price_applied).toFixed(2)}</small>
                    ${i.custom_note ? `<div class="x-small text-info mt-1 bg-info bg-opacity-10 p-1 rounded"><i class="bi bi-card-text me-1"></i><strong>Nota:</strong> ${i.custom_note}</div>` : ''}
                    ${i.custom_logo_link ? `<div class="x-small mt-1"><a href="${APP_URL}${i.custom_logo_link}" target="_blank" class="text-decoration-none"><i class="bi bi-image me-1"></i>Ver Logo Adjunto</a></div>` : ''}
                </div>
                <div class="fw-bold text-dark">$${(i.quantity * i.price_applied).toFixed(2)}</div>
            </div>
        `).join('');

        const headerHtml = `
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    ${COMPANY.logo ? `<img src="${COMPANY.logo}" style="max-height: 55px; object-fit: contain;">` : `<h4 class="mb-0 text-primary fw-bold">${COMPANY.name}</h4>`}
                    <div>
                        ${COMPANY.logo ? `<h5 class="mb-0 fw-bold text-dark">${COMPANY.name}</h5>` : ''}
                        ${COMPANY.ruc ? `<div class="x-small text-muted">RUC: ${COMPANY.ruc}</div>` : ''}
                    </div>
                </div>
                <div class="text-end x-small text-muted" style="line-height: 1.2;">
                    ${COMPANY.address ? `<div>${COMPANY.address}</div>` : ''}
                    ${COMPANY.city ? `<div>${COMPANY.city}</div>` : ''}
                    ${COMPANY.phone ? `<div>Trato: ${COMPANY.phone}</div>` : ''}
                    ${COMPANY.email ? `<div>${COMPANY.email}</div>` : ''}
                </div>
            </div>
        `;

        const footerHtml = `
            <div class="mt-4 pt-3 border-top text-center x-small text-muted">
                ${COMPANY.thanks ? `<div class="fw-bold text-dark mb-1">${COMPANY.thanks}</div>` : ''}
                ${COMPANY.terms ? `<div>${COMPANY.terms}</div>` : ''}
            </div>
        `;

        content.innerHTML = `
            ${headerHtml}
            <div class="row g-4">
                <div class="col-6">
                    <h6 class="fw-bold text-primary border-bottom pb-2">Datos del Cliente</h6>
                    <p class="mb-1"><strong>Nombre:</strong> ${d.customer_name}</p>
                    <p class="mb-1"><strong>Cédula/RUC:</strong> ${d.customer_id || 'N/A'}</p>
                    <p class="mb-1"><strong>Email:</strong> ${d.customer_email}</p>
                    <p class="mb-1"><strong>Ciudad:</strong> ${d.customer_city}</p>
                    <p class="mb-1"><strong>Teléfono:</strong> <a href="https://wa.me/593${d.customer_phone?.substring(1)}" target="_blank">${d.customer_phone || 'N/A'}</a></p>
                    <p class="mb-0"><strong>Dirección:</strong> ${d.customer_address || 'Pendiente'}</p>
                </div>
                <div class="col-6">
                    <h6 class="fw-bold text-primary border-bottom pb-2 text-md-end">Resumen de Pago</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Subtotal:</span>
                        <span>$${(d.total_amount - d.shipping_amount - d.tax_amount).toFixed(2)}</span>
                    </div>
                    ${d.shipping_amount > 0 ? `
                        <div class="d-flex justify-content-between mb-1 text-primary">
                            <span>Envío:</span>
                            <span>$${parseFloat(d.shipping_amount).toFixed(2)}</span>
                        </div>
                    ` : ''}
                    ${d.tax_amount > 0 ? `
                        <div class="d-flex justify-content-between mb-1 text-success">
                            <span>IVA:</span>
                            <span>$${parseFloat(d.tax_amount).toFixed(2)}</span>
                        </div>
                    ` : ''}
                    <div class="d-flex justify-content-between border-top pt-2 mt-2 h5 fw-bold text-dark">
                        <span>TOTAL:</span>
                        <span>$${parseFloat(d.total_amount).toFixed(2)}</span>
                    </div>
                    
                    <div class="mt-3 no-export">
                        <label class="form-label small fw-bold text-muted">Ajustar Estado:</label>
                        <select class="form-select form-select-sm" onchange="cambiarEstado(${d.id}, this.value)">
                            <option value="Pendiente" ${d.status === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                            <option value="En Diseño" ${d.status === 'En Diseño' ? 'selected' : ''}>En Diseño</option>
                            <option value="En Producción" ${d.status === 'En Producción' ? 'selected' : ''}>En Producción</option>
                            <option value="Listo para Entrega" ${d.status === 'Listo para Entrega' ? 'selected' : ''}>Listo para Entrega</option>
                            <option value="Finalizado" ${d.status === 'Finalizado' ? 'selected' : ''}>Finalizado</option>
                            <option value="Cancelado" ${d.status === 'Cancelado' ? 'selected' : ''}>Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2">Productos del Pedido</h6>
                    ${itemsHtml}
                </div>
            </div>
            ${footerHtml}
        `;
    }

    function startEditing() {
        isEditing = true;
        const btnEdit = document.getElementById('btn-edit-order');
        if (btnEdit) {
            btnEdit.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Cambios';
            btnEdit.classList.remove('btn-outline-primary');
            btnEdit.classList.add('btn-success');
            btnEdit.classList.remove('d-none');
        }

        const d = currentOrder;
        const content = document.getElementById('detalle-content');

        let itemsHtml = d.items.map((i, idx) => `
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div>
                    <h6 class="mb-0 fw-bold">${i.product_name}</h6>
                    ${i.custom_note ? `<div class="x-small text-info mt-1"><i class="bi bi-card-text me-1"></i>${i.custom_note}</div>` : ''}
                    ${i.custom_logo_link ? `<div class="x-small mt-1"><a href="${APP_URL}${i.custom_logo_link}" target="_blank"><i class="bi bi-image me-1"></i>Logo</a></div>` : ''}
                    <div class="input-group input-group-sm mt-2" style="max-width: 250px;">
                        <span class="input-group-text">Cant.</span>
                        <input type="number" class="form-control" value="${i.quantity}" onchange="updateItemQty(${idx}, this.value)">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" value="${i.price_applied}" onchange="updateItemPrice(${idx}, this.value)">
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-dark">Subt: $${(i.quantity * i.price_applied).toFixed(2)}</div>
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(${idx})"><i class="bi bi-trash"></i> Eliminar</button>
                </div>
            </div>
        `).join('');

        content.innerHTML = `
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary border-bottom pb-2">Editar Datos Cliente</h6>
                    <div class="mb-2">
                        <label class="small text-muted mb-0">Nombre:</label>
                        <input type="text" class="form-control form-control-sm" value="${d.customer_name}" oninput="currentOrder.customer_name = this.value">
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted mb-0">Cédula/RUC:</label>
                        <input type="text" class="form-control form-control-sm" value="${d.customer_id || ''}" oninput="currentOrder.customer_id = this.value">
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted mb-0">Email:</label>
                        <input type="email" class="form-control form-control-sm" value="${d.customer_email}" oninput="currentOrder.customer_email = this.value">
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted mb-0">Teléfono:</label>
                        <input type="text" class="form-control form-control-sm" value="${d.customer_phone || ''}" oninput="currentOrder.customer_phone = this.value">
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted mb-0">Ciudad:</label>
                        <input type="text" class="form-control form-control-sm" value="${d.customer_city}" oninput="currentOrder.customer_city = this.value">
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted mb-0">Dirección:</label>
                        <input type="text" class="form-control form-control-sm" value="${d.customer_address || ''}" oninput="currentOrder.customer_address = this.value">
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary border-bottom pb-2 text-md-end">Resumen de Pago</h6>
                    <div class="mb-2">
                        <label class="small text-muted">Costo Envío ($):</label>
                        <input type="number" step="0.01" class="form-control form-control-sm text-end" value="${d.shipping_amount}" onchange="updateOrderShipping(this.value)">
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted">IVA Total ($):</label>
                        <input type="number" step="0.01" class="form-control form-control-sm text-end" value="${d.tax_amount}" onchange="updateOrderTax(this.value)">
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-2 h5 fw-bold text-dark">
                        <span>TOTAL:</span>
                        <span id="edit-total-display">$${parseFloat(d.total_amount).toFixed(2)}</span>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2">Productos del Pedido</h6>
                    ${itemsHtml || '<p class="text-center py-3 text-muted">No hay productos en este pedido.</p>'}
                </div>
            </div>
        `;
    }

    function updateItemQty(idx, val) {
        currentOrder.items[idx].quantity = parseInt(val) || 0;
        currentOrder.items[idx].subtotal = currentOrder.items[idx].quantity * currentOrder.items[idx].price_applied;
        recalculateTotal();
        startEditing(); // Re-render rápido
    }

    function updateItemPrice(idx, val) {
        currentOrder.items[idx].price_applied = parseFloat(val) || 0;
        currentOrder.items[idx].subtotal = currentOrder.items[idx].quantity * currentOrder.items[idx].price_applied;
        recalculateTotal();
        startEditing();
    }

    function updateOrderShipping(val) {
        currentOrder.shipping_amount = parseFloat(val) || 0;
        recalculateTotal();
        document.getElementById('edit-total-display').textContent = '$' + parseFloat(currentOrder.total_amount).toFixed(2);
    }

    function updateOrderTax(val) {
        currentOrder.tax_amount = parseFloat(val) || 0;
        recalculateTotal();
        document.getElementById('edit-total-display').textContent = '$' + parseFloat(currentOrder.total_amount).toFixed(2);
    }

    function removeItem(idx) {
        currentOrder.items.splice(idx, 1);
        recalculateTotal();
        startEditing();
    }

    function recalculateTotal() {
        let subtotalItems = currentOrder.items.reduce((s, i) => s + (i.quantity * i.price_applied), 0);
        currentOrder.total_amount = subtotalItems + parseFloat(currentOrder.shipping_amount) + parseFloat(currentOrder.tax_amount);
    }

    function saveChanges() {
        if (currentOrder.items.length === 0) {
            Swal.fire('Atención', 'El pedido debe tener al menos un producto.', 'warning');
            return;
        }

        fetch(`${APP_URL}cotizacion/actualizar_orden`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(currentOrder)
        })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire('¡Éxito!', 'Pedido actualizado correctamente', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
    }

    function updateDigitalButtons() {
        const d = currentOrder;
        const actionsLeft = document.getElementById('modal-actions-left');
        actionsLeft.innerHTML = '';
        const hasDigital = d.items.some(i => i.is_digital == 1);
        if (hasDigital && d.digital_approved == 0) {
            actionsLeft.innerHTML = `
                <button class="btn btn-success btn-sm rounded-pill px-3" onclick="aprobarDigital(${d.id})">
                    <i class="bi bi-cloud-check me-1"></i>Aprobar Entrega Digital
                </button>
            `;
        } else if (hasDigital && d.digital_approved == 1) {
            actionsLeft.innerHTML = `
                <span class="badge bg-success py-2 px-3 rounded-pill">
                    <i class="bi bi-check-circle me-1"></i>Digital Entregado
                </span>
            `;
        }
    }

    function cambiarEstado(id, status) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);

        fetch(`${APP_URL}cotizacion/actualizar_estado`, {
            method: 'POST',
            body: formData
        })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => location.reload());
                }
            });
    }

    function aprobarDigital(id) {
        Swal.fire({
            title: '¿Generar accesos digitales?',
            text: "Se crearán credenciales y se enviará notificación al cliente.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Sí, generar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);
                fetch(`${APP_URL}cotizacion/aprobar_pago_digital`, {
                    method: 'POST',
                    body: formData
                })
                    .then(r => {
                        if (!r.ok) throw new Error('Error en la respuesta del servidor (' + r.status + ')');
                        return r.json();
                    })
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire('¡Éxito!', `Accesos generados correctamente.\nSe envió un email al cliente.\n\nUsuario: ${res.user}\nClave: ${res.pass}`, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', res.message || 'Error desconocido al aprobar la entrega.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error("Error Catch Aprobar Digital:", err);
                        Swal.fire('Fallo de Procesamiento', 'La respuesta del servidor no fue el JSON esperado o hubo un error interno. (' + err.message + ')', 'error');
                    });
            }
        });
    }
    /* ── Exportación ── */
    function generateExportableHtml(d) {
        const logoHtml = COMPANY.logo ? `<img src="${COMPANY.logo}" style="max-height: 80px; max-width: 250px; object-fit: contain;">` : `<h2 style="margin:0;color:#0275d8;">${COMPANY.name}</h2>`;

        const itemsHtml = d.items.map(i => `
            <tr>
                <td style="padding: 10px 15px; border-bottom: 1px solid #eee; color: #333;">
                    <strong>${i.product_name}</strong>
                    ${i.custom_note ? `<div style="font-size: 11px; color: #666; margin-top: 4px;">Nota: ${i.custom_note}</div>` : ''}
                </td>
                <td style="padding: 10px 15px; border-bottom: 1px solid #eee; text-align: center; color: #333;">${i.quantity}</td>
                <td style="padding: 10px 15px; border-bottom: 1px solid #eee; text-align: right; color: #333;">$${parseFloat(i.price_applied).toFixed(2)}</td>
                <td style="padding: 10px 15px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold; color: #333;">$${(i.quantity * i.price_applied).toFixed(2)}</td>
            </tr>
        `).join('');

        const subtotal = d.total_amount - d.shipping_amount - d.tax_amount;
        const now = new Date(d.created_at).toLocaleDateString('es-EC', { year: 'numeric', month: 'long', day: 'numeric' });

        return `
        <div style="width: 800px; padding: 25px 40px; background: #ffffff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px;">
                <div style="max-width: 65%; display: flex; align-items: flex-start; gap: 20px;">
                    ${COMPANY.logo ? `<img src="${COMPANY.logo}" style="max-height: 70px; max-width: 180px; object-fit: contain;">` : ''}
                    <div style="font-size: 13px; color: #555; line-height: 1.3;">
                        <span style="font-size: 17px; font-weight: bold; color: #2c3e50;">${COMPANY.name}</span><br>
                        ${COMPANY.ruc ? `<strong>RUC:</strong> ${COMPANY.ruc}<br>` : ''}
                        ${COMPANY.address ? `${COMPANY.address}<br>` : ''}
                        ${COMPANY.city ? `${COMPANY.city}<br>` : ''}
                        ${COMPANY.phone ? `<strong>Tel:</strong> ${COMPANY.phone}<br>` : ''}
                        ${COMPANY.email ? `<strong>Email:</strong> ${COMPANY.email}` : ''}
                    </div>
                </div>
                <div style="text-align: right;">
                    <h1 style="margin: 0 0 5px 0; color: #2c3e50; font-size: 26px; font-weight: 700; letter-spacing: 1px;">COTIZACIÓN</h1>
                    <div style="font-size: 15px; color: #e74c3c; font-weight: bold;"># ${d.id}</div>
                    <div style="font-size: 12px; color: #888; margin-top: 5px;">Fecha: ${now}</div>
                </div>
            </div>

            <!-- Cliente Info -->
            <div style="margin-bottom: 15px; background: #f8f9fa; padding: 8px 12px; border-radius: 6px; border-left: 4px solid #0275d8;">
                <h3 style="margin: 0 0 4px 0; font-size: 11px; color: #7f8c8d; text-transform: uppercase;">Facturar a:</h3>
                <div style="font-size: 12px; color: #333; line-height: 1.2; display: flex; flex-wrap: wrap; gap: 4px 25px;">
                    <div style="flex: 1 1 100%;"><span style="font-size: 14px; font-weight: bold; color: #0275d8;">${d.customer_name}</span></div>
                    ${d.customer_id ? `<div><strong>RUC/CI:</strong> ${d.customer_id}</div>` : ''}
                    ${d.customer_phone ? `<div><strong>Teléfono:</strong> ${d.customer_phone}</div>` : ''}
                    ${d.customer_email ? `<div><strong>Email:</strong> ${d.customer_email}</div>` : ''}
                    ${d.customer_city ? `<div><strong>Ciudad:</strong> ${d.customer_city}</div>` : ''}
                    ${d.customer_address ? `<div style="flex: 1 1 100%; margin-top:2px;"><strong>Dirección:</strong> ${d.customer_address}</div>` : ''}
                </div>
            </div>

            <!-- Tabla -->
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 25px; font-size: 13px; border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden;">
                <thead>
                    <tr>
                        <th style="padding: 10px 15px; text-align: left; background: #2c3e50; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Descripción</th>
                        <th style="padding: 10px 15px; text-align: center; background: #2c3e50; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Cant.</th>
                        <th style="padding: 10px 15px; text-align: right; background: #2c3e50; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">P. Unit</th>
                        <th style="padding: 10px 15px; text-align: right; background: #2c3e50; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>

            <!-- Totales -->
            <div style="display: flex; justify-content: flex-end;">
                <div style="width: 300px;">
                    <div style="display: flex; justify-content: space-between; padding: 2px 0; font-size: 13px; color: #555;">
                        <span>Subtotal:</span>
                        <span>$${subtotal.toFixed(2)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 2px 0; font-size: 13px; color: #555;">
                        <span>Envío:</span>
                        <span>$${parseFloat(d.shipping_amount).toFixed(2)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 2px 0; font-size: 13px; color: #555;">
                        <span>IVA (${COMPANY.tax_rate ? COMPANY.tax_rate : '0'}%):</span>
                        <span>$${parseFloat(d.tax_amount).toFixed(2)}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; margin-top: 4px; border-top: 2px solid #2c3e50; font-size: 16px; font-weight: bold; color: #2c3e50;">
                        <span>TOTAL:</span>
                        <span style="color: #e74c3c;">$${parseFloat(d.total_amount).toFixed(2)}</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            ${COMPANY.footer_image ? `<div style="margin-top: 30px; text-align: center;"><img src="${COMPANY.footer_image}" style="max-width: 100%; max-height: 120px; border-radius: 8px; object-fit: contain;"></div>` : ''}
            <div style="margin-top: ${COMPANY.footer_image ? '15px' : '40px'}; text-align: center; font-size: 11px; color: #7f8c8d; border-top: 1px solid #eee; padding-top: 15px; line-height: 1.5;">
                <div style="font-size: 13px; color: #2c3e50; margin-bottom: 5px;"><strong>${COMPANY.thanks ? COMPANY.thanks : '¡Gracias por su preferencia!'}</strong></div>
                ${COMPANY.terms ? `<em style="color:#999;">${COMPANY.terms}</em>` : '<span style="color:#999;">Cotización válida por 48 horas laborables.</span>'}
            </div>
        </div>`;
    }

    function exportOrderToImage(mode, uiBtn = null) {
        if (!currentOrder) return;
        const btn = uiBtn || event?.currentTarget;
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) { btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; btn.disabled = true; }

        // Crear contenedor virtual
        const container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.top = '0';
        container.innerHTML = generateExportableHtml(currentOrder);
        document.body.appendChild(container);

        setTimeout(() => {
            html2canvas(container.firstElementChild, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(canvas => {
                document.body.removeChild(container);
                if (mode === 'download') {
                    const link = document.createElement('a'); link.download = `Pedido_${currentOrder.id}.png`; link.href = canvas.toDataURL('image/png'); link.click();
                } else if (mode === 'copy') {
                    canvas.toBlob(blob => {
                        const item = new ClipboardItem({ "image/png": blob });
                        navigator.clipboard.write([item]).then(() => {
                            Swal.fire({ icon: 'success', title: 'Copiado al portapapeles', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                        });
                    });
                }
                if (btn) { btn.innerHTML = originalHtml; btn.disabled = false; }
            }).catch(err => {
                if (document.body.contains(container)) document.body.removeChild(container);
                console.error(err); Swal.fire('Error', 'No se pudo generar la imagen', 'error');
                if (btn) { btn.innerHTML = originalHtml; btn.disabled = false; }
            });
        }, 300); // Dar tiempo a cargar imágenes
    }

    function exportOrderToPdf(uiBtn = null) {
        if (!currentOrder) return;
        const btn = uiBtn || event?.currentTarget;
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) { btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; btn.disabled = true; }

        // Crear contenedor virtual
        const container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.top = '0';
        container.innerHTML = generateExportableHtml(currentOrder);
        document.body.appendChild(container);

        setTimeout(() => {
            html2canvas(container.firstElementChild, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(canvas => {
                document.body.removeChild(container);
                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;

                // Calculamos dimensiones en mm (1px aprox 0.264583 mm)
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;
                const pdfWidth = imgWidth * 0.264583;
                const pdfHeight = imgHeight * 0.264583;

                // Creamos PDF con tamaño personalizado ajustado a la proforma
                const pdf = new jsPDF(pdfWidth > pdfHeight ? 'l' : 'p', 'mm', [pdfWidth, pdfHeight]);

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save(`Pedido_${currentOrder.id}.pdf`);

                if (btn) { btn.innerHTML = originalHtml; btn.disabled = false; }
            }).catch(err => {
                if (document.body.contains(container)) document.body.removeChild(container);
                console.error(err); Swal.fire('Error', 'No se pudo generar el PDF', 'error');
                if (btn) { btn.innerHTML = originalHtml; btn.disabled = false; }
            });
        }, 300);
    }

    function enviarPorEmail(uiBtn = null) {
        if (!currentOrder || !currentOrder.customer_email) { Swal.fire('Error', 'El cliente no tiene un email registrado', 'error'); return; }
        Swal.fire({
            title: 'Enviar pedido por Email', text: `Se enviará la proforma a: ${currentOrder.customer_email}`, icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, enviar ahora', showLoaderOnConfirm: true, confirmButtonColor: '#0dcaf0',
            preConfirm: () => {
                return fetch(`${APP_URL}cotizacion/enviar_email`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: currentOrder.id }) })
                    .then(r => r.json()).then(data => { if (data.status !== 'success') throw new Error(data.message || 'Error desconocido'); return data; })
                    .catch(error => { Swal.showValidationMessage(`Error en el envío: ${error.message}`); });
            }
        }).then((result) => { if (result.isConfirmed) Swal.fire({ icon: 'success', title: '¡Enviado!', text: 'La proforma ha sido enviada al cliente.', timer: 3000 }); });
    }
</script>