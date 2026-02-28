<!-- PORTAFOLIO / CASOS DE ÉXITO -->

<div class="d-flex align-items-center gap-3 mb-2">
    <a href="<?= APP_URL ?>" class="text-muted text-decoration-none small">
        <i class="bi bi-house-door me-1"></i>Inicio
    </a>
    <i class="bi bi-chevron-right text-muted small"></i>
    <span class="small fw-semibold text-dark">Portafolio</span>
</div>

<div class="text-center mb-5">
    <h2 class="fw-bold display-6 text-dark">Nuestros Trabajos</h2>
    <p class="text-secondary mx-auto" style="max-width:540px">
        Casos reales de proyectos terminados que muestran la calidad y detalle de nuestro trabajo en corte láser y
        sublimación.
    </p>
</div>

<?php if (empty($casos)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-images fs-1 d-block mb-3 opacity-50"></i>
        <h5>Aún no hay trabajos publicados.</h5>
        <p>El administrador irá añadiendo casos de éxito próximamente.</p>
    </div>
<?php else: ?>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
        <?php foreach ($casos as $caso):
            $imgU = (!empty($caso['image_url']) && str_starts_with($caso['image_url'], 'http'))
                ? $caso['image_url']
                : APP_URL . $caso['image_url'];
            ?>
            <div class="col">
                <div class="portfolio-card"
                    onclick="openPortfolioItem('<?= addslashes($imgU) ?>', '<?= addslashes(htmlspecialchars($caso['title'] ?? '')) ?>', '<?= addslashes(htmlspecialchars($caso['description'] ?? '')) ?>')">
                    <img src="<?= $imgU ?>" alt="<?= htmlspecialchars($caso['title'] ?? '') ?>"
                        onerror="this.src='https://placehold.co/400x400/111/555?text=Sin+imagen'">
                    <div class="portfolio-overlay">
                        <div>
                            <div class="text-white fw-bold small">
                                <?= htmlspecialchars($caso['title'] ?? 'Trabajo') ?>
                            </div>
                            <div class="text-white-50 small" style="font-size:.75rem">
                                <?= htmlspecialchars(mb_substr($caso['description'] ?? '', 0, 50)) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="text-center mt-5">
    <a href="<?= APP_URL ?>" class="btn btn-dark rounded-pill px-5 py-2">
        <i class="bi bi-arrow-left me-2"></i>Ver Catálogo
    </a>
</div>