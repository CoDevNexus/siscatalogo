<?php
$db = \App\Core\Database::getInstance();

$totalProductos = $db->fetch("SELECT COUNT(*) AS c FROM products WHERE status='active'")['c'] ?? 0;
$totalProformas = $db->fetch("SELECT COUNT(*) AS c FROM orders WHERE status='pending'")['c'] ?? 0;
$totalDigitales = $db->fetch("SELECT COUNT(*) AS c FROM digital_access WHERE downloads_count = 0")['c'] ?? 0;
$totalPortfolio = $db->fetch("SELECT COUNT(*) AS c FROM success_cases")['c'] ?? 0;

$ultimasProformas = $db->fetchAll("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5") ?? [];
$ultimosProductos = $db->fetchAll("SELECT name, price_unit, is_digital, status FROM products ORDER BY created_at DESC LIMIT 5") ?? [];
?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-1">Proformas Pendientes</h6>
                        <h3 class="mb-0 fw-bold"><?= $totalProformas ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="bi bi-cart3 fs-4"></i>
                    </div>
                </div>
                <a href="<?= APP_URL ?>admin/ventas" class="text-primary text-decoration-none small">Ver todas →</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-1">Entregas Pendientes</h6>
                        <h3 class="mb-0 fw-bold text-success"><?= $totalDigitales ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="bi bi-cloud-arrow-down fs-4"></i>
                    </div>
                </div>
                <a href="<?= APP_URL ?>admin/digitales" class="text-success text-decoration-none small">Ver digitales
                    →</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <a href="<?= APP_URL ?>admin/productos" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">Total Productos</h6>
                            <h3 class="mb-0 fw-bold"><?= $totalProductos ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= APP_URL ?>admin/portfolio" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">Casos de Éxito</h6>
                            <h3 class="mb-0 fw-bold"><?= $totalPortfolio ?></h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                            <i class="bi bi-image fs-4"></i>
                        </div>
                    </div>
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
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i> Últimas Proformas</h6>
                <a href="<?= APP_URL ?>admin/ventas" class="btn btn-sm btn-outline-primary rounded-pill px-3">Ver
                    todas</a>
            </div>
            <div class="card-body p-4">
                <?php if (empty($ultimasProformas)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        <p class="small">No hay proformas aún.</p>
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