<?php
$db = \App\Core\Database::getInstance();

$totalProductos = $db->fetch("SELECT COUNT(*) AS c FROM products")['c'] ?? 0;
$totalProformas = $db->fetch("SELECT COUNT(*) AS c FROM orders WHERE status='pending'")['c'] ?? 0;
$totalDigitales = $db->fetch("SELECT COUNT(*) AS c FROM digital_access WHERE downloads_count = 0")['c'] ?? 0;
// Corregido: La tabla es portfolio, no success_cases
$totalPortfolio = $db->fetch("SELECT COUNT(*) AS c FROM portfolio")['c'] ?? 0;
$totalCategorias = $db->fetch("SELECT COUNT(*) AS c FROM categories")['c'] ?? 0;

$ultimasProformas = $db->fetchAll("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5") ?? [];
$ultimosProductos = $db->fetchAll("SELECT name, price_unit, is_digital, status FROM products ORDER BY created_at DESC LIMIT 5") ?? [];
?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4 col-xl-2">
        <a href="<?= APP_URL ?>admin/pedidos" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-primary border-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                            <i class="bi bi-cart3 fs-5"></i>
                        </div>
                        <h4 class="mb-0 fw-bold"><?= $totalProformas ?></h4>
                    </div>
                    <h6 class="text-muted small fw-bold mb-0">Pedidos Pendientes</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="<?= APP_URL ?>admin/digitales" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-success border-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                            <i class="bi bi-cloud-arrow-down fs-5"></i>
                        </div>
                        <h4 class="mb-0 fw-bold text-success"><?= $totalDigitales ?></h4>
                    </div>
                    <h6 class="text-muted small fw-bold mb-0">Entregas Digitales</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="<?= APP_URL ?>admin/productos" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-warning border-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                            <i class="bi bi-box-seam fs-5"></i>
                        </div>
                        <h4 class="mb-0 fw-bold"><?= $totalProductos ?></h4>
                    </div>
                    <h6 class="text-muted small fw-bold mb-0">Total Productos</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="<?= APP_URL ?>admin/portfolio" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-info border-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                            <i class="bi bi-image fs-5"></i>
                        </div>
                        <h4 class="mb-0 fw-bold"><?= $totalPortfolio ?></h4>
                    </div>
                    <h6 class="text-muted small fw-bold mb-0">Casos de Éxito</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="<?= APP_URL ?>admin/categorias" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-secondary border-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-secondary bg-opacity-10 p-2 rounded-3 text-secondary">
                            <i class="bi bi-tags fs-5"></i>
                        </div>
                        <h4 class="mb-0 fw-bold"><?= $totalCategorias ?></h4>
                    </div>
                    <h6 class="text-muted small fw-bold mb-0">Categorías</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="<?= APP_URL ?>admin/perfil" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-dark border-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-dark bg-opacity-10 p-2 rounded-3 text-dark">
                            <i class="bi bi-shop fs-5"></i>
                        </div>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </div>
                    <h6 class="text-muted small fw-bold mb-0">Perfil Empresa</h6>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Últimas proformas -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i> Últimos Pedidos</h6>
                <a href="<?= APP_URL ?>admin/pedidos" class="btn btn-sm btn-outline-primary rounded-pill px-3">Ver
                    todos</a>
            </div>
            <div class="card-body p-4">
                <?php if (empty($ultimasProformas)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        <p class="small">No hay pedidos aún.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Ciudad</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasProformas as $o): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($o['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($o['customer_city']) ?></td>
                                        <td>$<?= number_format($o['total_amount'], 2) ?></td>
                                        <td><span class="badge bg-warning text-dark"><?= $o['status'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Últimos Productos -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-box-seam me-2 text-warning"></i> Últimos Productos Agregados
                </h6>
                <a href="<?= APP_URL ?>admin/productos"
                    class="btn btn-sm btn-outline-warning rounded-pill px-3">Inventario</a>
            </div>
            <div class="card-body p-4">
                <?php if (empty($ultimosProductos)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        <p class="small">No hay productos aún. <a href="<?= APP_URL ?>admin/producto_crear">Crear
                                primero</a></p>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($ultimosProductos as $p): ?>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span>
                                    <?php if ($p['is_digital']): ?>
                                        <i class="bi bi-cloud-download text-primary me-2"></i>
                                    <?php else: ?>
                                        <i class="bi bi-box text-success me-2"></i>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($p['name']) ?>
                                </span>
                                <span class="fw-bold text-dark">$<?= number_format($p['price_unit'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>