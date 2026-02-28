<!-- NOSOTROS / EMPRESA -->
<?php
$db = \App\Core\Database::getInstance();
$co = $db->fetch("SELECT * FROM company_profile WHERE id = 1");
$logo = !empty($co['logo_url']) ? APP_URL . $co['logo_url'] : null;
// Normalizar columnas legadas vs nuevas
$co['whatsapp'] = !empty($co['whatsapp']) ? $co['whatsapp'] : ($co['phone_whatsapp'] ?? '');
$co['facebook'] = !empty($co['facebook']) ? $co['facebook'] : ($co['facebook_url'] ?? '');
$co['phone'] = $co['phone_whatsapp'] ?? $co['whatsapp'] ?? '';
?>
<div class="row g-5 align-items-center mb-5">
    <div class="col-lg-5 text-center">
        <?php if ($logo): ?>
            <img src="<?= $logo ?>" alt="Logo" style="max-width:220px;max-height:220px;object-fit:contain" class="mb-3">
        <?php else: ?>
            <i class="bi bi-heptagon-half text-danger" style="font-size:120px"></i>
        <?php endif; ?>
        <h3 class="fw-bold text-dark">
            <?= htmlspecialchars($co['name'] ?? APP_NAME) ?>
        </h3>
        <?php if (!empty($co['ruc_nit'])): ?>
            <p class="text-muted small">RUC/NIT:
                <?= htmlspecialchars($co['ruc_nit']) ?>
            </p>
        <?php endif; ?>
    </div>
    <div class="col-lg-7">
        <h4 class="fw-bold mb-3 text-dark">Sobre Nosotros</h4>
        <?php if (!empty($co['description'])): ?>
            <p class="text-secondary">
                <?= nl2br(htmlspecialchars($co['description'])) ?>
            </p>
        <?php else: ?>
            <p class="text-secondary">
                Somos especialistas en corte láser, grabado personalizado, sublimación y vectores digitales.
                Trabajamos con MDF, acrílico y una amplia gama de materiales para crear productos únicos y de alta calidad
                para tu negocio.
            </p>
        <?php endif; ?>
        <hr>
        <div class="row g-3 mt-1">
            <?php if (!empty($co['phone'])): ?>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width:38px;height:38px">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <div>
                            <div class="small text-muted">Teléfono / WhatsApp</div>
                            <div class="fw-semibold">
                                <?= htmlspecialchars($co['phone']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($co['whatsapp']) && $co['whatsapp'] !== $co['phone']): ?>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width:38px;height:38px">
                            <i class="bi bi-whatsapp"></i>
                        </span>
                        <div>
                            <div class="small text-muted">WhatsApp</div>
                            <a href="https://wa.me/<?= preg_replace('/\D/', '', $co['whatsapp']) ?>" target="_blank"
                                class="fw-semibold text-decoration-none text-dark">
                                <?= htmlspecialchars($co['whatsapp']) ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($co['address'])): ?>
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width:38px;height:38px">
                            <i class="bi bi-geo-alt"></i>
                        </span>
                        <div>
                            <div class="small text-muted">Dirección</div>
                            <div class="fw-semibold">
                                <?= htmlspecialchars($co['address']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="mt-4">
            <?php $waNum = preg_replace('/\D/', '', $co['whatsapp'] ?: $co['phone']); ?>
            <?php if ($waNum): ?>
                <a href="https://wa.me/<?= $waNum ?>?text=<?= urlencode('Hola, quisiera hacer un pedido.') ?>"
                    target="_blank" class="btn btn-success rounded-pill px-4 me-2 fw-semibold">
                    <i class="bi bi-whatsapp me-1"></i>Escribir por WhatsApp
                </a>
            <?php endif; ?>
            <a href="<?= APP_URL ?>" class="btn btn-outline-dark rounded-pill px-4">
                <i class="bi bi-shop me-1"></i>Ver Catálogo
            </a>
        </div>
    </div>
</div>