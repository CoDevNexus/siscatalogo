<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold display-4">Casos de Éxito</h1>
        <p class="lead text-muted">Explora nuestros trabajos más recientes y destacados.</p>
        <div class="mx-auto" style="width: 80px; height: 4px; background: var(--bs-primary);"></div>
    </div>

    <div id="portfolio-grid" class="row g-4">
        <?php
        $count = 0;
        foreach ($items as $index => $item):
            // Lógica de rejilla mixta: 3 columnas (col-md-4) then 2 columnas (col-md-6)
            // Ciclo de 5 elementos: 0,1,2 -> col-4 | 3,4 -> col-6
            $cyclePos = $count % 5;
            $colClass = ($cyclePos < 3) ? 'col-lg-4 col-md-6' : 'col-lg-6 col-md-12';
            $imageHeight = ($cyclePos < 3) ? '280px' : '400px';
            ?>
            <div class="<?= $colClass ?> portfolio-item animate__animated animate__fadeIn">
                <a href="<?= APP_URL ?>caso-de-exito/<?= $item['slug'] ?>" class="text-decoration-none portfolio-link"
                    data-slug="<?= $item['slug'] ?>">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden portfolio-card bg-white">
                        <div class="position-relative overflow-hidden" style="height: <?= $imageHeight ?>;">
                            <?php if ($item['imagen_principal']): ?>
                                <img src="<?= (strpos($item['imagen_principal'], 'http') === 0) ? $item['imagen_principal'] : APP_URL . $item['imagen_principal'] ?>"
                                    class="w-100 h-100 object-fit-cover transition-transform"
                                    alt="<?= htmlspecialchars($item['titulo']) ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image text-muted fs-1 opacity-25"></i>
                                </div>
                            <?php endif; ?>
                            <div class="portfolio-overlay px-3 py-2">
                                <span class="badge bg-primary text-white rounded-pill px-3 py-2 shadow-sm fw-bold">
                                    <?= htmlspecialchars($item['categoria_tecnica'] ?: 'Proyecto') ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h3 class="h5 fw-bold text-dark mb-2"><?= htmlspecialchars($item['titulo']) ?></h3>
                            <p class="text-secondary small mb-0 line-clamp-2"><?= htmlspecialchars($item['intro_corta']) ?>
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <?php
            $count++;
        endforeach;
        ?>
    </div>

    <?php if ($totalPages > $currentPage): ?>
        <div class="text-center mt-5">
            <a href="<?= APP_URL ?>portafolio/page/<?= $currentPage + 1 ?>" id="load-more-btn"
                class="btn btn-outline-primary btn-lg rounded-pill px-5 py-3 shadow-sm border-2 fw-bold">
                <span class="btn-text">Ver Más Trabajos</span>
                <div class="spinner-border spinner-border-sm d-none" role="status"></div>
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para Detalle de Portafolio -->
<div class="modal fade portfolio-modal" id="portfolioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 p-4 pb-0 position-absolute end-0 z-3">
                <button type="button" class="btn-close bg-white p-2 rounded-circle shadow-sm" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="modal-content-body">
                <!-- Se llenará dinámicamente -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .portfolio-card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .portfolio-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
    }

    .object-fit-cover {
        object-fit: cover;
    }

    .transition-transform {
        transition: transform 0.6s ease;
    }

    .portfolio-card:hover .transition-transform {
        transform: scale(1.1);
    }

    .portfolio-overlay {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 2;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    #load-more-btn {
        min-width: 250px;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate__fadeIn {
        animation: fadeIn 0.8s ease backwards;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loadMoreBtn = document.getElementById('load-more-btn');
        const grid = document.getElementById('portfolio-grid');
        const portfolioModal = new bootstrap.Modal(document.getElementById('portfolioModal'));
        const modalBody = document.getElementById('modal-content-body');
        const originalUrl = window.location.href;
        const originalTitle = document.title;
        let currentPage = <?= $currentPage ?>;
        let itemCount = <?= $count ?>;

        // Función para abrir el modal con contenido
        function openPortfolio(slug, pushState = true) {
            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
            portfolioModal.show();

            // Evitar doble barra si APP_URL termina en /
            const baseUrl = '<?= rtrim(APP_URL, "/") ?>';
            const fetchUrl = `${baseUrl}/caso-de-exito/${slug}`;
            console.log("Fetching Portfolio Data from:", fetchUrl);

            fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    console.log("Fetch Server Response Status:", res.status);
                    return res.json();
                })
                .then(data => {
                    console.log("JSON DATA:", data);
                    if (data.status === 'success' && data.item) {
                        const item = data.item;
                        const imgSrc = item.imagen_principal && (item.imagen_principal.indexOf('http') === 0)
                            ? item.imagen_principal
                            : `<?= APP_URL ?>${item.imagen_principal}`;

                        const shareUrl = encodeURIComponent(`<?= APP_URL ?>caso-de-exito/${item.slug}`);
                        const shareTitle = encodeURIComponent(item.titulo);

                        modalBody.innerHTML = `
                        <div class="row g-0">
                            <div class="col-lg-7">
                                <div class="h-100 bg-light d-flex align-items-center justify-content-center overflow-hidden" style="min-height: 400px; max-height: 700px;">
                                    ${item.imagen_principal ? `<img src="${imgSrc}" class="w-100 h-100 object-fit-cover shadow" alt="${item.titulo}">` :
                                `<i class="bi bi-image text-muted display-1 opacity-25"></i>`}
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="p-4 p-md-5 h-100 d-flex flex-column">
                                    <div class="mb-3">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 mb-2">
                                            ${item.categoria_tecnica || 'Proyecto'}
                                        </span>
                                        <h2 class="fw-bold h1">${item.titulo}</h2>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar-event me-1"></i> ${new Date(item.fecha_publicacion).toLocaleDateString()}
                                        </div>
                                    </div>
                                    <div class="portfolio-content overflow-auto flex-grow-1 my-4 pe-2" style="max-height: 350px;">
                                        ${item.contenido_enriquecido}
                                        
                                        ${item.gallery && item.gallery.length > 0 ? `
                                            <div class="row g-2 mt-4 portfolio-gallery-grid">
                                                ${item.gallery.map(img => `
                                                    <div class="col-6">
                                                        <div class="rounded-3 overflow-hidden shadow-sm" style="height: 150px;">
                                                            <img src="${img.source === 'local' ? `<?= APP_URL ?>${img.image_path}` : img.image_path}" 
                                                                 class="w-100 h-100 object-fit-cover transition-transform cursor-zoom" 
                                                                 onclick="window.open(this.src, '_blank')"
                                                                 alt="Imagen de galería">
                                                        </div>
                                                    </div>
                                                `).join('')}
                                            </div>
                                        ` : ''}
                                    </div>
                                    <div class="border-top pt-4 mt-auto">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <span class="small fw-bold text-muted">Compartir:</span>
                                            <div class="d-flex gap-2">
                                                <a href="https://api.whatsapp.com/send?text=Mira este proyecto: ${shareTitle} ${shareUrl}" target="_blank" class="btn btn-sm btn-success rounded-circle"><i class="bi bi-whatsapp"></i></a>
                                                <a href="https://www.facebook.com/sharer/sharer.php?u=${shareUrl}" target="_blank" class="btn btn-sm btn-primary rounded-circle"><i class="bi bi-facebook"></i></a>
                                            </div>
                                            <a href="<?= APP_URL ?>caso-de-exito/${item.slug}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Ver página completa</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                        if (pushState) {
                            window.history.pushState({ slug: slug }, item.titulo, `<?= APP_URL ?>caso-de-exito/${slug}`);
                            document.title = item.titulo + ' - Portafolio';
                        }
                    } else {
                        modalBody.innerHTML = `<div class="alert alert-warning m-4">No se pudo encontrar la información del proyecto. (Status: ${data.status})</div>`;
                    }
                })
                .catch(err => {
                    console.error("Error en OpenPortfolio:", err);
                    modalBody.innerHTML = `<div class="alert alert-danger m-4">Error técnico al cargar el proyecto: ${err.message}. Revise la consola.</div>`;
                });
        }

        // Interceptar clics en los enlaces de portafolio
        document.addEventListener('click', function (e) {
            const link = e.target.closest('.portfolio-link');
            if (link) {
                e.preventDefault();
                openPortfolio(link.dataset.slug);
            }
        });

        // Restaurar URL al cerrar el modal
        document.getElementById('portfolioModal').addEventListener('hidden.bs.modal', function () {
            window.history.pushState(null, originalTitle, originalUrl);
            document.title = originalTitle;
        });

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function (e) {
                e.preventDefault();

                const btnText = this.querySelector('.btn-text');
                const spinner = this.querySelector('.spinner-border');

                btnText.classList.add('d-none');
                spinner.classList.remove('d-none');
                this.classList.add('disabled');

                fetch(this.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            data.items.forEach(item => {
                                const cyclePos = itemCount % 5;
                                const colClass = (cyclePos < 3) ? 'col-lg-4 col-md-6' : 'col-lg-6 col-md-12';
                                const imageHeight = (cyclePos < 3) ? '280px' : '400px';

                                const isUrl = item.imagen_principal && (item.imagen_principal.indexOf('http') === 0);
                                const imgSrc = isUrl ? item.imagen_principal : `<?= APP_URL ?>${item.imagen_principal}`;

                                const itemHtml = `
                            <div class="${colClass} portfolio-item animate__animated animate__fadeIn">
                                <a href="<?= APP_URL ?>caso-de-exito/${item.slug}" 
                                   class="text-decoration-none portfolio-link" 
                                   data-slug="${item.slug}">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden portfolio-card bg-white">
                                        <div class="position-relative overflow-hidden" style="height: ${imageHeight};">
                                            ${item.imagen_principal ? `<img src="${imgSrc}" class="w-100 h-100 object-fit-cover transition-transform" alt="${item.titulo}">` :
                                        `<div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center"><i class="bi bi-image text-muted fs-1 opacity-25"></i></div>`}
                                            <div class="portfolio-overlay px-3 py-2">
                                                <span class="badge bg-primary text-white rounded-pill px-3 py-2 shadow-sm fw-bold">
                                                    ${item.categoria_tecnica || 'Proyecto'}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <h3 class="h5 fw-bold text-dark mb-2">${item.titulo}</h3>
                                            <p class="text-secondary small mb-0 line-clamp-2">${item.intro_corta}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        `;
                                grid.insertAdjacentHTML('beforeend', itemHtml);
                                itemCount++;
                            });

                            if (data.hasMore) {
                                this.href = `<?= APP_URL ?>portafolio/page/${data.nextPage}`;
                                btnText.classList.remove('d-none');
                                spinner.classList.add('d-none');
                                this.classList.remove('disabled');
                            } else {
                                this.remove();
                            }
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        btnText.classList.remove('d-none');
                        spinner.classList.add('d-none');
                        this.classList.remove('disabled');
                    });
            });
        }
    });
</script>