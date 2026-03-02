<!-- NOSOTROS / EMPRESA -->
<?php
$db = \App\Core\Database::getInstance();
$co = $db->fetch("SELECT * FROM company_profile WHERE id = 1");
$logo = !empty($co['logo_url']) ? APP_URL . $co['logo_url'] : null;
// Normalizar columnas legadas vs nuevas
$co['whatsapp'] = !empty($co['whatsapp']) ? $co['whatsapp'] : ($co['phone_whatsapp'] ?? '');
$co['facebook'] = !empty($co['facebook']) ? $co['facebook'] : ($co['facebook_url'] ?? '');
$co['phone'] = $co['phone_whatsapp'] ?? $co['whatsapp'] ?? '';
$co['instagram'] = $co['instagram'] ?? '';
$co['tiktok'] = $co['tiktok'] ?? '';
$co['email'] = $co['email'] ?? '';
$co['ciudad'] = $co['ciudad'] ?? '';
$co['maps_embed'] = $co['maps_embed'] ?? '';
?>
<div class="container py-5">
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
                    Trabajamos con MDF, acrílico y una amplia gama de materiales para crear productos únicos y de alta
                    calidad
                    para tu negocio.
                </p>
            <?php endif; ?>
            <hr>
            <div class="row g-3 mt-1">
                <?php if (!empty($co['phone'])): ?>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <span
                                class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
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
                            <span
                                class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
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
                    <div class="col-sm-12">
                        <div class="d-flex align-items-center gap-2">
                            <span
                                class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width:38px;height:38px;flex-shrink:0;">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <div>
                                <div class="small text-muted">Dirección <?php if (!empty($co['ciudad'])): ?>/
                                        <?= htmlspecialchars($co['ciudad']) ?>     <?php endif; ?>
                                </div>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($co['address']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($co['email'])): ?>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <span
                                class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center"
                                style="width:38px;height:38px;flex-shrink:0;">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <div>
                                <div class="small text-muted">Correo Electrónico</div>
                                <a href="mailto:<?= htmlspecialchars($co['email']) ?>"
                                    class="fw-semibold text-decoration-none text-dark text-break">
                                    <?= htmlspecialchars($co['email']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Redes Sociales -->
            <?php if (!empty($co['facebook']) || !empty($co['instagram']) || !empty($co['tiktok'])): ?>
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold mb-3 text-secondary">Síguenos en Redes Sociales</h6>
                    <div class="d-flex gap-2">
                        <?php if (!empty($co['facebook'])): ?>
                            <a href="<?= htmlspecialchars($co['facebook']) ?>" target="_blank"
                                class="btn btn-outline-primary rounded-circle"
                                style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-facebook fs-5"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($co['instagram'])): ?>
                            <a href="<?= htmlspecialchars($co['instagram']) ?>" target="_blank"
                                class="btn btn-outline-danger rounded-circle"
                                style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-instagram fs-5"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($co['tiktok'])): ?>
                            <a href="<?= htmlspecialchars($co['tiktok']) ?>" target="_blank"
                                class="btn btn-outline-dark rounded-circle"
                                style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-tiktok fs-5"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-4 pt-3 border-top">
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

    <?php if (!empty($co['maps_embed'])): ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden rounded-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-map text-primary me-2"></i>Nuestra Ubicación</h5>
                    </div>
                    <div class="card-body p-0" style="height:400px; width:100%;">
                        <?php
                        // El usuario pudo pegar el código <iframe> entero.
                        $mapHtml = trim($co['maps_embed']);
                        if (strpos($mapHtml, '<iframe') === false) {
                            // Si solo pegó la URL de embed, la rodeamos
                            $mapHtml = '<iframe src="' . htmlspecialchars($mapHtml) . '" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                        } else {
                            // Agregar w-100 h-100 para asegurar layout
                            $mapHtml = str_replace('<iframe ', '<iframe style="width:100%;height:100%;border:0;" ', $mapHtml);
                        }
                        echo $mapHtml;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div> <!-- Cierre del container principal -->