<div class="container-fluid px-0">
    <div class="row g-0">
        <div class="col-12 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 fw-bold mb-0">Gestión de Slider (Home)</h2>
                <?php if (\App\Core\Security::can('slider.gestionar')): ?>
                    <button class="btn btn-primary" onclick="openSliderModal()">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Slide
                    </button>
                <?php endif; ?>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Slide guardado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Slide eliminado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Imagen</th>
                                    <th>Título</th>
                                    <th>Badge</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
                                    <?php if (\App\Core\Security::can('slider.gestionar')): ?>
                                        <th class="text-end pe-4">Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($slides)): ?>
                                    <?php foreach ($slides as $slide): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <img src="<?= $slide['image_url'] ?>" alt="Slide"
                                                    class="rounded object-fit-cover" style="width: 80px; height: 50px;">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">
                                                    <?= htmlspecialchars(strip_tags($slide['title'])) ?>
                                                </div>
                                                <small class="text-muted text-truncate d-block" style="max-width: 250px;">
                                                    <?= htmlspecialchars($slide['subtitle']) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($slide['badge']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $slide['sort_order'] ?>
                                            </td>
                                            <td>
                                                <?php if ($slide['status'] == 'active'): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (\App\Core\Security::can('slider.gestionar')): ?>
                                                <td class="text-end pe-4">
                                                    <button class="btn btn-sm btn-outline-primary rounded-pill me-1"
                                                        onclick='editSlider(<?= json_encode($slide) ?>)' title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="<?= APP_URL ?>admin/home_slider_eliminar/<?= $slide['id'] ?>"
                                                        class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este slide?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No hay slides configurados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Slider Form -->
<div class="modal fade" id="sliderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= APP_URL ?>admin/home_slider_guardar" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sliderModalTitle">Nuevo Slide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="slide_id">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">URL de Imagen destacada *</label>
                            <input type="url" name="image_url" id="slide_image_url" class="form-control" required
                                placeholder="https://ejemplo.com/imagen.jpg">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Título principal *</label>
                            <div id="editor_title" style="height: 100px; background: #fff; border-radius: 0 0 8px 8px;">
                            </div>
                            <input type="hidden" name="title" id="slide_title" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Badge (Etiqueta superior)</label>
                            <input type="text" name="badge" id="slide_badge" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Subtítulo (Opcional)</label>
                            <div id="editor_subtitle"
                                style="height: 120px; background: #fff; border-radius: 0 0 8px 8px;"></div>
                            <input type="hidden" name="subtitle" id="slide_subtitle">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Texto del Botón</label>
                            <input type="text" name="button_text" id="slide_button_text" class="form-control"
                                placeholder="Ej: Ver más">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Enlace del Botón</label>
                            <input type="text" name="button_link" id="slide_button_link" class="form-control"
                                placeholder="Ej: productos?cat=1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Orden de aparición</label>
                            <input type="number" name="sort_order" id="slide_sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select name="status" id="slide_status" class="form-select">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Slide</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    let sliderModal;
    let quillTitle;
    let quillSubtitle;

    // Configuración de la barra de herramientas (limpia y concisa)
    const quillOptions = {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['clean']
            ]
        }
    };

    document.addEventListener("DOMContentLoaded", function () {
        sliderModal = new bootstrap.Modal(document.getElementById('sliderModal'));

        // Inicializar editores
        quillTitle = new Quill('#editor_title', quillOptions);
        quillSubtitle = new Quill('#editor_subtitle', quillOptions);

        // Sincronizar contenido antes de enviar el formulario
        document.querySelector('#sliderModal form').addEventListener('submit', function () {
            document.getElementById('slide_title').value = quillTitle.root.innerHTML;
            document.getElementById('slide_subtitle').value = quillSubtitle.root.innerHTML;
        });
    });

    function openSliderModal() {
        document.getElementById('sliderModalTitle').innerText = 'Nuevo Slide';
        document.getElementById('slide_id').value = '';
        document.getElementById('slide_image_url').value = '';
        document.getElementById('slide_title').value = '';
        document.getElementById('slide_badge').value = '';
        document.getElementById('slide_subtitle').value = '';
        document.getElementById('slide_button_text').value = '';
        document.getElementById('slide_button_link').value = '';
        document.getElementById('slide_sort_order').value = '0';
        document.getElementById('slide_status').value = 'active';

        // Limpiar editores
        quillTitle.root.innerHTML = '';
        quillSubtitle.root.innerHTML = '';

        sliderModal.show();
    }

    function editSlider(slide) {
        document.getElementById('sliderModalTitle').innerText = 'Editar Slide';
        document.getElementById('slide_id').value = slide.id;
        document.getElementById('slide_image_url').value = slide.image_url;
        document.getElementById('slide_title').value = slide.title;
        document.getElementById('slide_badge').value = slide.badge;
        document.getElementById('slide_subtitle').value = slide.subtitle;
        document.getElementById('slide_button_text').value = slide.button_text;
        document.getElementById('slide_button_link').value = slide.button_link;
        document.getElementById('slide_sort_order').value = slide.sort_order;
        document.getElementById('slide_status').value = slide.status;

        // Cargar contenido en los editores
        quillTitle.root.innerHTML = slide.title || '';
        quillSubtitle.root.innerHTML = slide.subtitle || '';

        sliderModal.show();
    }
</script>