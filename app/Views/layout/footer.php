</main>

<!-- ─── Footer Premium ─── -->
<footer class="footer-premium">
    <div class="container">
        <div class="row g-5 pb-4">

            <div class="col-lg-4">
                <?php
                $db = \App\Core\Database::getInstance();
                $co = $db->fetch("SELECT name, logo_url, phone_whatsapp, whatsapp, facebook_url, facebook, instagram, address FROM company_profile WHERE id = 1");
                $logoF = !empty($co['logo_url']) ? APP_URL . $co['logo_url'] : null;
                $coNameF = !empty($co['name']) ? htmlspecialchars($co['name']) : APP_NAME;
                $co['whatsapp'] = !empty($co['whatsapp']) ? $co['whatsapp'] : ($co['phone_whatsapp'] ?? '');
                $co['facebook'] = !empty($co['facebook']) ? $co['facebook'] : ($co['facebook_url'] ?? '');
                $co['instagram'] = $co['instagram'] ?? '';
                ?>
                <?php if ($logoF): ?>
                    <img src="<?= $logoF ?>" alt="Logo"
                        style="max-height:50px;max-width:180px;object-fit:contain;filter:brightness(0) invert(1)"
                        class="mb-3">
                <?php endif; ?>
                <h5 class="mb-2"><?= $coNameF ?></h5>
                <p class="mb-3" style="font-size:.88rem;line-height:1.6">
                    Especialistas en corte láser, grabado personalizado, sublimación y vectores digitales para MDF y
                    acrílico.
                </p>
                <div class="d-flex gap-2">
                    <?php if (!empty($co['whatsapp'])): ?>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $co['whatsapp']) ?>" target="_blank"
                            class="social-icon" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($co['facebook'])): ?>
                        <a href="<?= htmlspecialchars($co['facebook']) ?>" target="_blank" class="social-icon"
                            title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($co['instagram'])): ?>
                        <a href="<?= htmlspecialchars($co['instagram']) ?>" target="_blank" class="social-icon"
                            title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h5 class="mb-3 fs-6 fw-bold text-uppercase" style="letter-spacing:.8px">Catálogo</h5>
                <a href="<?= APP_URL ?>" class="footer-link">Ver Todo</a>
                <a href="<?= APP_URL ?>?tipo=fisico" class="footer-link">Artículos Físicos</a>
                <a href="<?= APP_URL ?>?tipo=digital" class="footer-link">Diseños Digitales</a>
                <a href="<?= APP_URL ?>portfolio" class="footer-link">Portafolio</a>
            </div>

            <div class="col-6 col-lg-2">
                <h5 class="mb-3 fs-6 fw-bold text-uppercase" style="letter-spacing:.8px">Empresa</h5>
                <a href="<?= APP_URL ?>nosotros" class="footer-link">Sobre Nosotros</a>
                <?php if (!empty($co['whatsapp'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $co['whatsapp']) ?>" target="_blank"
                        class="footer-link">Contacto</a>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <h5 class="mb-3 fs-6 fw-bold text-uppercase" style="letter-spacing:.8px">Contacto rápido</h5>
                <?php if (!empty($co['phone'])): ?>
                    <p class="mb-2" style="font-size:.88rem"><i
                            class="bi bi-telephone me-2 text-danger"></i><?= htmlspecialchars($co['phone']) ?></p>
                <?php endif; ?>
                <?php if (!empty($co['address'])): ?>
                    <p class="mb-3" style="font-size:.88rem"><i
                            class="bi bi-geo-alt me-2 text-danger"></i><?= htmlspecialchars($co['address']) ?></p>
                <?php endif; ?>
                <?php if (!empty($co['whatsapp'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $co['whatsapp']) ?>?text=<?= urlencode('Hola, me interesa hacer un pedido.') ?>"
                        target="_blank" class="btn btn-success btn-sm rounded-pill px-4 fw-semibold">
                        <i class="bi bi-whatsapp me-1"></i>Escribir por WhatsApp
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <hr class="footer-divider">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size:.82rem">
            <span>&copy; <?= date('Y') ?> <?= $coNameF ?>. Todos los derechos reservados.</span>
            <span style="opacity:.5">Desarrollado con <i class="bi bi-heart-fill text-danger"></i> · MVC PHP</span>
        </div>
    </div>
</footer>

<!-- ─── Scripts ─── -->
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<!-- html2canvas (para proforma descargable) -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<!-- Main JS del catálogo -->
<script src="<?= APP_URL ?>assets/js/main.js"></script>

</body>

</html>