<div class="container py-5">
    <div class="row justify-content-center py-5">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-primary p-4 text-center text-white">
                    <i class="bi bi-cloud-arrow-down fs-1 mb-2"></i>
                    <h3 class="fw-bold mb-1">Descargas Seguras</h3>
                    <p class="mb-0 opacity-75">Ingresa tus credenciales para acceder</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form id="login-digital-form">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Usuario Digital</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="bi bi-person text-primary"></i></span>
                                <input type="text" id="dig-user" class="form-control border-start-0 ps-0"
                                    placeholder="Ej: USR12345" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Contraseña de Acceso</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="bi bi-key text-primary"></i></span>
                                <input type="password" id="dig-pass" class="form-control border-start-0 ps-0"
                                    placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                <i class="bi bi-unlock me-2"></i>Acceder a mis archivos
                            </button>
                        </div>
                    </form>

                    <div id="digital-results" class="mt-4" style="display:none;">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Tus Productos Listos:</h6>
                        <div id="files-container"></div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small">
                    Si no tienes tus credenciales, por favor contacta con soporte o revisa el correo enviado tras la
                    aprobación de tu pedido.
                </p>
                <a href="<?= APP_URL ?>" class="btn btn-link text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Catálogo
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('login-digital-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const user = document.getElementById('dig-user').value.trim();
        const pass = document.getElementById('dig-pass').value.trim();

        Swal.fire({ title: 'Validando...', didOpen: () => Swal.showLoading() });

        try {
            const formData = new FormData();
            formData.append('user', user);
            formData.append('pass', pass);

            const r = await fetch('<?= APP_URL ?>descarga/login', {
                method: 'POST',
                body: formData
            });
            const res = await r.json();
            Swal.close();

            if (res.status === 'success') {
                document.getElementById('login-digital-form').style.display = 'none';
                document.getElementById('digital-results').style.display = 'block';

                const container = document.getElementById('files-container');
                container.innerHTML = res.items.map(item => `
                <div class="p-3 border rounded-3 mb-2 d-flex align-items-center justify-content-between bg-light">
                    <div>
                        <div class="fw-bold text-dark">${item.product_name}</div>
                        <small class="text-muted">Formato digital aprobado</small>
                    </div>
                    <a href="<?= APP_URL ?>descarga/archivo/${res.token}/${item.product_id}" 
                       class="btn btn-sm btn-success rounded-pill px-3">
                        <i class="bi bi-download me-1"></i> Descargar
                    </a>
                </div>
            `).join('');

                Swal.fire({ icon: 'success', title: 'Acceso concedido', text: 'Tus archivos están disponibles.', timer: 1500, showConfirmButton: false });
            } else {
                Swal.fire('Error de acceso', res.message || 'Credenciales incorrectas o pago no aprobado aún.', 'error');
            }
        } catch (e) {
            Swal.close();
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
</script>