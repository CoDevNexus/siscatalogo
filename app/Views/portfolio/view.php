<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Navegación y Botón Volver -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= APP_URL ?>"
                            class="text-decoration-none text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?= APP_URL ?>portafolio"
                            class="text-decoration-none text-muted">Portafolio</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">
                        <?= htmlspecialchars($item['titulo']) ?>
                    </li>
                </ol>
            </nav>

            <div class="mb-4 text-center">
                <span
                    class="badge bg-soft-primary text-primary rounded-pill px-4 py-2 border border-primary border-opacity-10 mb-3 fw-bold">
                    <?= htmlspecialchars($item['categoria_tecnica'] ?: 'Proyecto Destacado') ?>
                </span>
                <h1 class="display-4 fw-bold mb-3">
                    <?= htmlspecialchars($item['titulo']) ?>
                </h1>
                <div class="text-muted d-flex justify-content-center align-items-center gap-3">
                    <span><i class="bi bi-calendar-event me-1"></i>
                        <?= date('d/m/Y', strtotime($item['fecha_publicacion'])) ?>
                    </span>
                    <span class="vr"></span>
                    <span><i class="bi bi-tag me-1"></i>
                        <?= htmlspecialchars($item['tags'] ?: 'General') ?>
                    </span>
                </div>
            </div>

            <!-- Imagen Principal -->
            <?php if ($item['imagen_principal']): ?>
                <div class="rounded-4 overflow-hidden shadow-lg mb-5" style="max-height: 550px;">
                    <?php
                    $imgSrc = (strpos($item['imagen_principal'], 'http') === 0) ? $item['imagen_principal'] : APP_URL . $item['imagen_principal'];
                    ?>
                    <img src="<?= $imgSrc ?>" class="w-100 h-100 object-fit-cover"
                        alt="<?= htmlspecialchars($item['titulo']) ?>">
                </div>
            <?php endif; ?>

            <!-- Contenido Enriquecido -->
            <div class="card border-0 shadow-sm rounded-4 mb-5">
                <div class="card-body p-4 p-md-5">
                    <div class="portfolio-content ql-editor-view">
                        <?= $item['contenido_enriquecido'] ?>
                    </div>

                    <?php if (isset($item['gallery']) && !empty($item['gallery'])): ?>
                        <div class="mt-5 pt-4 border-top">
                            <h4 class="fw-bold mb-4">Galería del Proyecto</h4>
                            <div class="row g-3">
                                <?php foreach ($item['gallery'] as $gImg): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="rounded-4 overflow-hidden shadow-sm hover-zoom"
                                            style="height: 250px; cursor: pointer;"
                                            onclick="window.open('<?= ($gImg['source'] === 'local') ? APP_URL . $gImg['image_path'] : $gImg['image_path'] ?>', '_blank')">
                                            <?php $gUrl = ($gImg['source'] === 'local') ? APP_URL . $gImg['image_path'] : $gImg['image_path']; ?>
                                            <img src="<?= $gUrl ?>" class="w-100 h-100 object-fit-cover transition-transform"
                                                alt="Imagen adicional">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sección de Compartir -->
            <div
                class="bg-light rounded-4 p-4 text-center shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="fw-bold text-dark">
                    <i class="bi bi-share me-2"></i>¿Te gusta este proyecto? ¡Comparte con tus amigos!
                </div>
                <div class="d-flex gap-2">
                    <?php
                    $pageUrl = urlencode(APP_URL . 'caso-de-exito/' . $item['slug']);
                    $pageTitle = urlencode($item['titulo']);
                    ?>
                    <a href="https://api.whatsapp.com/send?text=Mira este proyecto: <?= $pageTitle ?> <?= $pageUrl ?>"
                        target="_blank"
                        class="btn btn-success rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $pageUrl ?>" target="_blank"
                        class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url=<?= $pageUrl ?>&description=<?= $pageTitle ?>"
                        target="_blank"
                        class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="bi bi-pinterest"></i>
                    </a>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="<?= APP_URL ?>portafolio"
                    class="btn btn-outline-primary rounded-pill px-5 py-3 fw-bold shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>Ver todo el portafolio
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.08);
    }

    .portfolio-content {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #333;
    }

    .portfolio-content img {
        max-width: 100%;
        height: auto;
        border-radius: 1rem;
        margin: 2rem 0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .portfolio-content h2,
    .portfolio-content h3 {
        margin-top: 2.5rem;
        font-weight: 700;
        color: #111;
    }

    .ql-editor-view blockquote {
        border-left: 4px solid var(--bs-primary);
        padding-left: 1.5rem;
        font-style: italic;
        color: #555;
        margin: 2rem 0;
    }
</style>