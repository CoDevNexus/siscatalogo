<div class="container-fluid px-0">
    <div class="row g-0">
        <div class="col-12 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 fw-bold mb-0">Textos del Home</h2>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Ajustes guardados correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="<?= APP_URL ?>admin/home_settings_guardar" method="POST"
                        enctype="multipart/form-data">
                        <div class="row g-4">
                            <!-- Sección: Hero del Catálogo -->
                            <div class="col-md-12 mb-2">
                                <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">Sección: Portada del Catálogo
                                </h5>
                                <div class="row">
                                    <?php foreach ($settings as $set): ?>
                                        <?php if ($set['group_name'] === 'hero'): ?>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small fw-bold">
                                                    <?= htmlspecialchars($set['label']) ?>
                                                </label>
                                                <?php if (strpos($set['key'], 'desc') !== false): ?>
                                                    <textarea name="settings[<?= $set['key'] ?>]" class="form-control"
                                                        rows="3"><?= htmlspecialchars($set['value']) ?></textarea>
                                                <?php elseif (strpos($set['key'], 'image') !== false): ?>
                                                    <div class="mb-2">
                                                        <?php if (!empty($set['value'])): ?>
                                                            <img src="<?= APP_URL . htmlspecialchars($set['value']) ?>"
                                                                class="img-thumbnail mb-2" style="max-height: 80px;">
                                                        <?php endif; ?>
                                                    </div>
                                                    <input type="file" name="settings_files[<?= $set['key'] ?>]"
                                                        class="form-control" accept="image/*">
                                                    <input type="hidden" name="settings[<?= $set['key'] ?>]"
                                                        value="<?= htmlspecialchars($set['value']) ?>">
                                                <?php else: ?>
                                                    <input type="text" name="settings[<?= $set['key'] ?>]" class="form-control"
                                                        value="<?= htmlspecialchars($set['value']) ?>">
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Sección: Productos Destacados -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3 border-bottom pb-2">Sección: Productos Destacados</h5>
                                <?php foreach ($settings as $set): ?>
                                    <?php if ($set['group_name'] === 'featured'): ?>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">
                                                <?= htmlspecialchars($set['label']) ?>
                                            </label>
                                            <?php if (strpos($set['key'], 'desc') !== false): ?>
                                                <textarea name="settings[<?= $set['key'] ?>]" class="form-control"
                                                    rows="3"><?= htmlspecialchars($set['value']) ?></textarea>
                                            <?php elseif (strpos($set['key'], 'image') !== false): ?>
                                                <div class="mb-2">
                                                    <?php if (!empty($set['value'])): ?>
                                                        <img src="<?= APP_URL . htmlspecialchars($set['value']) ?>"
                                                            class="img-thumbnail mb-2" style="max-height: 80px;">
                                                    <?php endif; ?>
                                                </div>
                                                <input type="file" name="settings_files[<?= $set['key'] ?>]" class="form-control"
                                                    accept="image/*">
                                                <input type="hidden" name="settings[<?= $set['key'] ?>]"
                                                    value="<?= htmlspecialchars($set['value']) ?>">
                                            <?php else: ?>
                                                <input type="text" name="settings[<?= $set['key'] ?>]" class="form-control"
                                                    value="<?= htmlspecialchars($set['value']) ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <!-- Sección: Portafolio -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3 border-bottom pb-2">Sección: Casos de Éxito (Portafolio)</h5>
                                <?php foreach ($settings as $set): ?>
                                    <?php if ($set['group_name'] === 'portfolio'): ?>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">
                                                <?= htmlspecialchars($set['label']) ?>
                                            </label>
                                            <?php if (strpos($set['key'], 'desc') !== false): ?>
                                                <textarea name="settings[<?= $set['key'] ?>]" class="form-control"
                                                    rows="3"><?= htmlspecialchars($set['value']) ?></textarea>
                                            <?php else: ?>
                                                <input type="text" name="settings[<?= $set['key'] ?>]" class="form-control"
                                                    value="<?= htmlspecialchars($set['value']) ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <?php if (\App\Core\Security::can('configuracion.editar')): ?>
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill">
                                    <i class="bi bi-save me-2"></i>Guardar Todos los Cambios
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>