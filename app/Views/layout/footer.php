</main>

<!-- ─── Footer Premium ─── -->
<!-- Carrito Flotante (Inferior Izquierda) -->
<div id="cart-float" data-open-cart style="cursor:pointer">
    <button class="cart-float-btn">
        <i class="bi bi-cart3"></i>
        <span class="cart-float-count" id="cart-float-count" style="display:none">0</span>
    </button>
</div>

<footer class="footer-premium">
    <div class="container">
        <div class="row g-5 pb-4">

            <div class="col-lg-4">
                <?php
                $db = \App\Core\Database::getInstance();
                $co = $db->fetch("SELECT name, logo_url, phone_whatsapp, whatsapp, facebook_url, facebook, instagram, tiktok, pinterest_url, address, ciudad, email, eslogan, description FROM company_profile WHERE id = 1");

                // Normalización de Logo (Soporte ImgBB y Local)
                $logoF = null;
                if (!empty($co['logo_url'])) {
                    $logoF = str_starts_with($co['logo_url'], 'http')
                        ? htmlspecialchars($co['logo_url'])
                        : APP_URL . htmlspecialchars($co['logo_url']);
                }

                $coNameF = !empty($co['name']) ? htmlspecialchars($co['name']) : APP_NAME;

                // Normalización de Redes y Contacto
                $co['whatsapp'] = !empty($co['whatsapp']) ? $co['whatsapp'] : ($co['phone_whatsapp'] ?? '');
                $co['facebook'] = !empty($co['facebook']) ? $co['facebook'] : ($co['facebook_url'] ?? '');
                $co['instagram'] = $co['instagram'] ?? '';
                $co['tiktok'] = $co['tiktok'] ?? '';
                $co['pinterest'] = $co['pinterest_url'] ?? '';
                $co['eslogan'] = !empty($co['eslogan']) ? htmlspecialchars($co['eslogan']) : 'Especialistas en corte láser y diseño personalizado.';
                ?>
                <?php if ($logoF): ?>
                    <img src="<?= $logoF ?>" alt="Logo"
                        style="max-height:50px;max-width:180px;object-fit:contain;filter:brightness(0) invert(1)"
                        class="mb-3">
                <?php endif; ?>
                <h5 class="mb-2"><?= $coNameF ?></h5>
                <p class="mb-3" style="font-size:.88rem;line-height:1.6">
                    <?= $co['eslogan'] ?>
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
                    <?php if (!empty($co['tiktok'])): ?>
                        <a href="<?= htmlspecialchars($co['tiktok']) ?>" target="_blank" class="social-icon" title="TikTok">
                            <i class="bi bi-tiktok"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($co['pinterest'])): ?>
                        <a href="<?= htmlspecialchars($co['pinterest']) ?>" target="_blank" class="social-icon"
                            title="Pinterest">
                            <i class="bi bi-pinterest"></i>
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
                <h5 class="mb-3 fs-6 fw-bold text-uppercase" style="letter-spacing:.8px">Ubicación y Contacto</h5>
                <?php if (!empty($co['address'])): ?>
                    <p class="mb-2" style="font-size:.88rem">
                        <i class="bi bi-geo-alt me-2 text-danger"></i>
                        <?= htmlspecialchars($co['address']) ?>
                        <?= !empty($co['ciudad']) ? ', ' . htmlspecialchars($co['ciudad']) : '' ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($co['email'])): ?>
                    <p class="mb-3" style="font-size:.88rem">
                        <i class="bi bi-envelope me-2 text-danger"></i>
                        <?= htmlspecialchars($co['email']) ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($co['whatsapp'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $co['whatsapp']) ?>?text=<?= urlencode('Hola, me interesa hacer un pedido.') ?>"
                        target="_blank" class="btn btn-success btn-sm rounded-pill px-4 fw-semibold mt-2">
                        <i class="bi bi-whatsapp me-1"></i>Escribir por WhatsApp
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <hr class="footer-divider">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size:.82rem">
            <span>&copy; <?= date('Y') ?> <?= $coNameF ?>. Todos los derechos reservados.</span>
            <span style="opacity:.5">Desarrollado por <a href="https://codevnexus.tech/" target="_blank"
                    class="text-decoration-none fw-bold text-white shadow-sm">CoDevNexus</a></span>
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