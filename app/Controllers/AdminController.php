<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductoModel;
use App\Models\CategoryModel;
use App\Models\ImagenProductoModel;
use App\Services\ImageService;
use App\Services\SlugService;

class AdminController extends Controller
{

    public function __construct()
    {
        // Verificar si el usuario está logueado en todos los métodos de este controlador
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            // Si intenta acceder sin login, echarlo sutilmente al inicio
            $this->redirect('');
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Principal'
        ];
        $this->view('admin/dashboard', $data);
    }

    // Vista del Perfil de la Empresa
    public function perfil()
    {
        $companyModel = $this->model('CompanyModel');
        $profile = $companyModel->getProfile();

        $data = [
            'title' => 'Perfil de la Empresa',
            'company' => $profile,
            'success' => $_SESSION['success_msg'] ?? null,
            'error' => $_SESSION['error_msg'] ?? null
        ];

        // Limpiar mensajes flash (almacenados temporalmente en sesión)
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        $this->view('admin/perfil', $data);
    }

    // Guardar cambios del Perfil de Empresa
    public function update_perfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/perfil');
            return;
        }

        $companyModel = $this->model('CompanyModel');

        // ── Validar WhatsApp (solo dígitos) ──
        $rawWa = preg_replace('/\D/', '', $_POST['phone_whatsapp'] ?? '');

        // ── Datos del formulario (todos los campos) ──
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'eslogan' => trim($_POST['eslogan'] ?? ''),
            'ruc_nit' => trim($_POST['ruc_nit'] ?? ''),
            'ciudad' => trim($_POST['ciudad'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'phone_whatsapp' => $rawWa,
            'email' => trim($_POST['email'] ?? ''),
            'facebook_url' => trim($_POST['facebook_url'] ?? ''),
            'instagram' => trim($_POST['instagram'] ?? ''),
            'tiktok' => trim($_POST['tiktok'] ?? ''),
            'pinterest_url' => trim($_POST['pinterest_url'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'terms_conditions' => trim($_POST['terms_conditions'] ?? ''),
            'thank_you_message' => trim($_POST['thank_you_message'] ?? ''),
            'maps_embed' => trim($_POST['maps_embed'] ?? ''),
            'shipping_cost' => (float) ($_POST['shipping_cost'] ?? 0),
            'tax_rate' => (float) ($_POST['tax_rate'] ?? 0),
            'smtp_host' => trim($_POST['smtp_host'] ?? ''),
            'smtp_port' => (int) ($_POST['smtp_port'] ?? 587),
            'smtp_user' => trim($_POST['smtp_user'] ?? ''),
            'smtp_pass' => trim($_POST['smtp_pass'] ?? ''),
            'smtp_encryption' => trim($_POST['smtp_encryption'] ?? 'tls'),
            'smtp_from_email' => trim($_POST['smtp_from_email'] ?? ''),
            'smtp_from_name' => trim($_POST['smtp_from_name'] ?? ''),
        ];

        // ── Manejo del Logo ──
        $logoUploaded = false;

        // Opción 0: Usar imagen ya existente seleccionada del browser (máxima prioridad)
        $existingLogoUrl = trim($_POST['existing_logo_url'] ?? '');
        if (!empty($existingLogoUrl)) {
            // Validar: solo rutas relativas locales o URLs http/https
            $isExternalUrl = str_starts_with($existingLogoUrl, 'http');
            $isSafePath = preg_match('#^(assets/img/|storage/productos/)#', $existingLogoUrl);
            if ($isExternalUrl || $isSafePath) {
                $companyModel->updateLogo($existingLogoUrl);
                $logoUploaded = true;
            }
        }

        // Opción A: Subir a ImgBB (si hay key configurada y se eligió esa opción)
        $useImgBB = !empty($_POST['use_imgbb']) && defined('IMGBB_API_KEY') && IMGBB_API_KEY;
        if (!$logoUploaded && $useImgBB && isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $result = ImageService::uploadToImgBB($_FILES['logo']['tmp_name'], IMGBB_API_KEY);
            if ($result) {
                // Eliminar logo anterior local si existe
                $oldLogo = $companyModel->getLogoPath();
                if ($oldLogo && !str_starts_with($oldLogo, 'http') && file_exists(BASE_PATH . 'public/' . $oldLogo)) {
                    @unlink(BASE_PATH . 'public/' . $oldLogo);
                }
                $companyModel->updateLogo($result['path']); // URL externa de ImgBB
                $logoUploaded = true;
            } else {
                $_SESSION['error_msg'] = 'No se pudo subir el logo a ImgBB. Revisa la API key.';
            }
        }

        // Opción B: Subida local con compresión WebP
        if (!$logoUploaded && isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            if (!in_array($ext, $allowed)) {
                $_SESSION['error_msg'] = 'Formato de imagen no válido. Usa PNG, JPG, WebP o SVG.';
                $this->redirect('admin/perfil');
                return;
            }

            $uploadDir = BASE_PATH . 'public/assets/img/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0755, true);

            // Eliminar logo anterior si existe
            $oldLogo = $companyModel->getLogoPath();
            if ($oldLogo && !str_starts_with($oldLogo, 'http')) {
                $oldPath = BASE_PATH . 'public/' . $oldLogo;
                if (file_exists($oldPath))
                    @unlink($oldPath);
            }

            // SVG: copiar directamente (no se puede comprimir)
            if ($ext === 'svg') {
                $fileName = 'logo_empresa.svg';
                move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName);
                $companyModel->updateLogo('assets/img/' . $fileName);
            } else {
                // Comprimir a WebP inline (no usar processUpload que guarda en storage/productos)
                $src = null;
                switch ($file['type'] ?? mime_content_type($_FILES['logo']['tmp_name'])) {
                    case 'image/jpeg':
                        $src = @imagecreatefromjpeg($_FILES['logo']['tmp_name']);
                        break;
                    case 'image/png':
                        $src = @imagecreatefrompng($_FILES['logo']['tmp_name']);
                        break;
                    case 'image/webp':
                        $src = @imagecreatefromwebp($_FILES['logo']['tmp_name']);
                        break;
                    default:
                        $src = @imagecreatefromjpeg($_FILES['logo']['tmp_name']);
                        break;
                }
                if ($src && function_exists('imagewebp')) {
                    $fileName = 'logo_empresa.webp';
                    imagewebp($src, $uploadDir . $fileName, 85);
                    $companyModel->updateLogo('assets/img/' . $fileName);
                } else {
                    // Fallback: copiar sin comprimir
                    $fileName = 'logo_empresa.' . $ext;
                    move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName);
                    $companyModel->updateLogo('assets/img/' . $fileName);
                }
            }
        }

        // ── Manejo de la Imagen de Pie de Página (Publicidad PDF) ──
        // ── Manejo de la Imagen de Pie de Página (Publicidad PDF) ──
        if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['footer_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed)) {
                $uploadDir = BASE_PATH . 'public/assets/img/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0755, true);

                $oldFooter = $companyModel->getFooterImagePath();
                if ($oldFooter && !str_starts_with($oldFooter, 'http')) {
                    $oldPath = BASE_PATH . 'public/' . $oldFooter;
                    if (file_exists($oldPath))
                        @unlink($oldPath);
                }

                $fileName = 'footer_publicidad.' . $ext;
                // Copiar imagen sin comprimir 
                move_uploaded_file($_FILES['footer_image']['tmp_name'], $uploadDir . $fileName);
                $companyModel->updateFooterImage('assets/img/' . $fileName);
            }
        }

        // ── Guardar datos de texto ──
        if ($companyModel->updateProfile($data)) {
            $_SESSION['success_msg'] = '✅ Perfil de empresa actualizado correctamente.';
        } else {
            $_SESSION['error_msg'] = 'Error al guardar los datos. Intenta nuevamente.';
        }

        $this->redirect('admin/perfil');
    }

    // ==== INVENTARIO (PRODUCTOS) ====
    public function productos()
    {
        $productoModel = $this->model('ProductoModel');

        // Recepción de parámetros
        $limit = 14;
        $page = (int) ($_GET['p'] ?? 1);
        if ($page < 1)
            $page = 1;

        $search = trim($_GET['s'] ?? '');
        $sort = $_GET['sort'] ?? 'p.created_at';
        $order = $_GET['dir'] ?? 'DESC';

        $offset = ($page - 1) * $limit;

        $totalItems = $productoModel->countTotal($search);
        $totalPages = ceil($totalItems / $limit);
        $productos = $productoModel->getPaginated($limit, $offset, $search, $sort, $order);

        $data = [
            'title' => 'Inventario de Productos',
            'productos' => $productos,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'limit' => $limit,
                'search' => $search,
                'sort' => $sort,
                'dir' => $order
            ],
            'success' => $_SESSION['success_msg'] ?? null,
            'error' => $_SESSION['error_msg'] ?? null
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);
        $this->view('admin/productos/index', $data);
    }

    public function producto_crear()
    {
        $data = [
            'title' => 'Nuevo Producto',
            'categorias' => $this->model('CategoryModel')->getAll(),
            'imagenes' => [],
            'success' => null,
            'error' => ''
        ];
        $this->view('admin/productos/form', $data);
    }

    public function producto_guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/productos');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);

        if (!$name || !$categoryId) {
            $_SESSION['error_msg'] = 'Nombre y categoría son obligatorios.';
            $this->redirect('admin/producto_crear');
            return;
        }

        $productoModel = $this->model('ProductoModel');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . time();

        $data = [
            'name' => $name,
            'slug' => $slug,
            'category_id' => $categoryId,
            'price_unit' => (float) ($_POST['price_unit'] ?? 0),
            'price_dozen' => (float) ($_POST['price_dozen'] ?? 0),
            'price_combo' => !empty($_POST['price_combo']) ? (float) $_POST['price_combo'] : null,
            'is_digital' => isset($_POST['is_digital']) ? 1 : 0,
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
            'allow_client_note' => isset($_POST['allow_client_note']) ? 1 : 0,
            'allow_client_logo' => isset($_POST['allow_client_logo']) ? 1 : 0
        ];

        $productId = $productoModel->create($data);

        if ($productId) {
            // Procesar Galería (Múltiples fuentes)
            $this->processImages((int) $productId, $this->model('ImagenProductoModel'));

            // Procesar Archivo Digital
            if (!empty($_FILES['digital_file']['name'])) {
                $this->saveDigitalFile((int) $productId, $_FILES['digital_file']);
            }

            $_SESSION['success_msg'] = "Producto '$name' creado correctamente.";
        }

        $this->redirect('admin/productos');
    }

    public function producto_editar($id = null)
    {
        if (!$id) {
            $this->redirect('admin/productos');
            return;
        }

        $productoModel = $this->model('ProductoModel');
        $producto = $productoModel->getById($id);
        if (!$producto) {
            $this->redirect('admin/productos');
            return;
        }

        $data = [
            'title' => 'Editar Producto',
            'producto' => $producto,
            'categorias' => $this->model('CategoryModel')->getAll(),
            'imagenes' => $this->model('ImagenProductoModel')->getByProduct($id),
            'success' => $_SESSION['success_msg'] ?? null,
            'error' => ''
        ];
        unset($_SESSION['success_msg']);
        $this->view('admin/productos/form', $data);
    }

    public function producto_actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/productos');
            return;
        }
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            $this->redirect('admin/productos');
            return;
        }

        $productoModel = $this->model('ProductoModel');
        $existing = $productoModel->getById($id);

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'price_unit' => (float) ($_POST['price_unit'] ?? 0),
            'price_dozen' => (float) ($_POST['price_dozen'] ?? 0),
            'price_combo' => !empty($_POST['price_combo']) ? (float) $_POST['price_combo'] : null,
            'is_digital' => isset($_POST['is_digital']) ? 1 : 0,
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
            'allow_client_note' => isset($_POST['allow_client_note']) ? 1 : 0,
            'allow_client_logo' => isset($_POST['allow_client_logo']) ? 1 : 0,
            'image_url' => $existing['image_url'] ?? ''
        ];

        $productoModel->update($id, $data);

        // Procesar Galería (Nuevas imágenes de cualquier fuente)
        $this->processImages($id, $this->model('ImagenProductoModel'));

        // Procesar Archivo Digital (Si sube uno nuevo)
        if (!empty($_FILES['digital_file']['name'])) {
            $this->saveDigitalFile($id, $_FILES['digital_file']);
        }

        $_SESSION['success_msg'] = 'Producto actualizado correctamente.';
        $this->redirect('admin/productos');
    }

    public function producto_eliminar($id = null)
    {
        if ($id) {
            $productoModel = $this->model('ProductoModel');

            // Verificar si tiene pedidos asociados
            if ($productoModel->hasOrders($id)) {
                // No se puede borrar físicamente, se inactiva
                $productoModel->update($id, [
                    'status' => 'inactive',
                    // Mantener el resto de datos igual, solo cambiamos status
                    'name' => ($p = $productoModel->getById($id))['name'],
                    'category_id' => $p['category_id'],
                    'price_unit' => $p['price_unit'],
                    'price_dozen' => $p['price_dozen'],
                    'is_digital' => $p['is_digital'],
                    'description' => $p['description'],
                    'image_url' => $p['image_url'],
                    'allow_client_note' => $p['allow_client_note'],
                    'allow_client_logo' => $p['allow_client_logo']
                ]);
                $_SESSION['success_msg'] = 'El producto tiene pedidos asociados. Se ha marcado como "Inactivo" para preservar el historial.';
            } else {
                // Se puede borrar físicamente
                $productoModel->delete($id);
                $_SESSION['success_msg'] = 'Producto eliminado permanentemente.';
            }
        }
        $this->redirect('admin/productos');
    }

    // ===== GALERÍA MULTIMEDIA =====

    /** Marcar imagen como portada (llamada AJAX) */
    public function imagen_principal($imgId = null)
    {
        header('Content-Type: application/json');
        if (!$imgId) {
            echo json_encode(['ok' => false]);
            return;
        }
        $db = \App\Core\Database::getInstance();
        $imgModel = $this->model('ImagenProductoModel');
        $img = $db->fetch('SELECT product_id FROM product_images WHERE id = :id', ['id' => $imgId]);
        if ($img) {
            $imgModel->setPrimary($imgId, $img['product_id']);
            $primary = $imgModel->getPrimary($img['product_id']);
            if ($primary) {
                $this->model('ProductoModel')->updateImageUrl(
                    $img['product_id'],
                    ImageService::buildUrl($primary['image_path'], $primary['source'])
                );
            }
            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false]);
        }
    }

    /** Eliminar imagen de galería (llamada AJAX) */
    public function imagen_eliminar($imgId = null)
    {
        header('Content-Type: application/json');
        if (!$imgId) {
            echo json_encode(['ok' => false]);
            return;
        }
        $db = \App\Core\Database::getInstance();
        $imgModel = $this->model('ImagenProductoModel');
        $img = $db->fetch('SELECT product_id FROM product_images WHERE id = :id', ['id' => $imgId]);
        if ($img) {
            $imgModel->delete($imgId);
            $primary = $imgModel->getPrimary($img['product_id']);
            $this->model('ProductoModel')->updateImageUrl(
                $img['product_id'],
                $primary ? ImageService::buildUrl($primary['image_path'], $primary['source']) : ''
            );
            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false]);
        }
    }

    /** Actualizar orden de imagen (llamada AJAX) */
    public function imagen_orden($imgId = null, $order = 0)
    {
        header('Content-Type: application/json');
        if ($imgId) {
            $this->model('ImagenProductoModel')->updateOrder($imgId, (int) $order);
            echo json_encode(['ok' => true]);
        }
    }

    // ===== HELPERS PRIVADOS =====

    private function processImages(int $productId, $imgModel): array
    {
        $existingCount = count($imgModel->getByProduct($productId));
        $maxImages = 5;
        $log = [];

        // 1. Imágenes seleccionadas del Explorador (reutilizadas)
        $selectedExisting = (array) ($_POST['existing_images'] ?? []);
        foreach ($selectedExisting as $imgPath) {
            if ($existingCount >= $maxImages)
                break;

            // Detectar fuente basandose en la URL
            $source = 'local';
            if (str_starts_with($imgPath, 'http')) {
                $source = str_contains($imgPath, 'ibb.co') ? 'api' : 'url';
            }

            $imgModel->addImage($productId, $imgPath, $source, $existingCount === 0, $existingCount);
            $existingCount++;
            $log[] = ['type' => 'reused', 'name' => basename($imgPath), 'ok' => true];
        }

        // 2. Archivos locales -> WebP Comprimido
        if (!empty($_FILES['images']['name'][0])) {
            for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                if ($existingCount >= $maxImages)
                    break;

                $f = [
                    'name' => $_FILES['images']['name'][$i],
                    'type' => $_FILES['images']['type'][$i],
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'error' => $_FILES['images']['error'][$i],
                    'size' => $_FILES['images']['size'][$i],
                ];

                if ($f['error'] === UPLOAD_ERR_OK) {
                    // Calidad 75 para ahorrar espacio en productos (galerías suelen ser más pesadas)
                    $result = ImageService::processUpload($f, 75);
                    if ($result) {
                        $imgModel->addImage($productId, $result['path'], $result['source'], $existingCount === 0, $existingCount);
                        $existingCount++;
                        $log[] = ['type' => 'local', 'name' => $f['name'], 'ok' => true];
                    }
                }
            }
        }

        // 3. URLs externas directas
        $urls = array_filter(array_map('trim', (array) ($_POST['img_urls'] ?? [])));
        foreach ($urls as $url) {
            if ($existingCount >= $maxImages)
                break;
            if (ImageService::validateExternalUrl($url)) {
                $imgModel->addImage($productId, $url, 'url', $existingCount === 0, $existingCount);
                $existingCount++;
                $log[] = ['type' => 'url', 'name' => $url, 'ok' => true];
            }
        }

        // 4. Subida a ImgBB
        $imgbbKey = defined('IMGBB_API_KEY') ? IMGBB_API_KEY : '';
        if (!empty($_FILES['imgbb_uploads']['name'][0]) && $imgbbKey) {
            for ($i = 0; $i < count($_FILES['imgbb_uploads']['name']); $i++) {
                if ($existingCount >= $maxImages)
                    break;

                $f = [
                    'name' => $_FILES['imgbb_uploads']['name'][$i],
                    'type' => $_FILES['imgbb_uploads']['type'][$i],
                    'tmp_name' => $_FILES['imgbb_uploads']['tmp_name'][$i],
                    'error' => $_FILES['imgbb_uploads']['error'][$i],
                    'size' => $_FILES['imgbb_uploads']['size'][$i],
                ];

                if ($f['error'] === UPLOAD_ERR_OK) {
                    $result = ImageService::uploadToImgBB($f['tmp_name'], $imgbbKey);
                    if ($result) {
                        $imgModel->addImage($productId, $result['path'], 'api', $existingCount === 0, $existingCount);
                        $existingCount++;
                        $log[] = ['type' => 'imgbb', 'name' => $f['name'], 'ok' => true];
                    }
                }
            }
        }

        // Sincronizar image_url del producto con la principal de la galería
        $primary = $imgModel->getPrimary($productId);
        if ($primary) {
            $this->model('ProductoModel')->updateImageUrl($productId, $primary['image_path']);
        }

        return $log;
    }

    private function saveDigitalFile(int $productId, array $file)
    {
        $allowed = ['dxf', 'svg', 'ai', 'pdf', 'png', 'zip'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed))
            return;
        $dir = BASE_PATH . 'storage/digital/';
        if (!is_dir($dir))
            mkdir($dir, 0755, true);
        $fileName = 'digital_' . $productId . '_' . time() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $dir . $fileName)) {
            $this->model('ProductoModel')->updateDigitalPath($productId, 'storage/digital/' . $fileName);
        }
    }

    // Listado de pedidos (Cotizaciones administrativas)
    public function pedidos()
    {
        $this->redirect('cotizacion/admin_index');
    }
    public function digitales()
    {
        require_once BASE_PATH . 'app/Models/DigitalAccessModel.php';
        $model = new \App\Models\DigitalAccessModel();
        $accesses = $model->getAllAdminAccesses();

        $this->view('admin/digitales/index', [
            'title' => 'Entregas Digitales',
            'accesses' => $accesses
        ]);
    }
    // ==== CASOS DE ÉXITO (PORTAFOLIO) ====
    public function portfolio()
    {
        require_once BASE_PATH . 'app/Models/PortfolioModel.php';
        $model = new \App\Models\PortfolioModel();

        $data = [
            'title' => 'Gestión de Casos de Éxito',
            'items' => $model->getAll(100, 0), // Listado amplio para admin
            'success' => $_SESSION['success_msg'] ?? null,
            'error' => $_SESSION['error_msg'] ?? null
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);
        $this->view('admin/portfolio/index', $data);
    }

    public function portfolio_nuevo()
    {
        $data = [
            'title' => 'Nuevo Caso de Éxito',
            'item' => null,
            'success' => null,
            'error' => $_SESSION['error_msg'] ?? null
        ];
        unset($_SESSION['error_msg']);
        $this->view('admin/portfolio/form', $data);
    }

    public function portfolio_editar($id = null)
    {
        if (!$id) {
            $this->redirect('admin/portfolio');
            return;
        }

        require_once BASE_PATH . 'app/Models/PortfolioModel.php';
        $model = new \App\Models\PortfolioModel();
        $item = $model->getById($id);

        if (!$item) {
            $this->redirect('admin/portfolio');
            return;
        }

        $data = [
            'title' => 'Editar Caso de Éxito',
            'item' => $item,
            'success' => $_SESSION['success_msg'] ?? null,
            'error' => $_SESSION['error_msg'] ?? null
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);
        $this->view('admin/portfolio/form', $data);
    }

    public function portfolio_guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/portfolio');
            return;
        }

        $id = $_POST['id'] ?? null;
        $title = trim($_POST['titulo'] ?? '');

        if (!$title) {
            $_SESSION['error_msg'] = 'El título es obligatorio.';
            $id ? $this->redirect("admin/portfolio_editar/$id") : $this->redirect('admin/portfolio_nuevo');
            return;
        }

        require_once BASE_PATH . 'app/Models/PortfolioModel.php';
        $model = new \App\Models\PortfolioModel();

        $data = [
            'titulo' => $title,
            'intro_corta' => trim($_POST['intro_corta'] ?? ''),
            'contenido_enriquecido' => $_POST['contenido_enriquecido'] ?? '',
            'categoria_tecnica' => trim($_POST['categoria_tecnica'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
            'tags' => trim($_POST['tags'] ?? ''),
            'fecha_publicacion' => $_POST['fecha_publicacion'] ?? date('Y-m-d'),
            'imagen_principal' => $_POST['imagen_principal'] ?? null
        ];

        // 1. Imagen Local
        if (isset($_FILES['portfolio_local']) && $_FILES['portfolio_local']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->uploadPortfolioImage($_FILES['portfolio_local']);
            if ($uploaded)
                $data['imagen_principal'] = $uploaded;
        }

        // 2. Imagen ImgBB
        $imgbbKey = defined('IMGBB_API_KEY') ? IMGBB_API_KEY : '';
        if (isset($_FILES['portfolio_imgbb']) && $_FILES['portfolio_imgbb']['error'] === UPLOAD_ERR_OK && $imgbbKey) {
            $res = ImageService::uploadToImgBB($_FILES['portfolio_imgbb']['tmp_name'], $imgbbKey);
            if ($res)
                $data['imagen_principal'] = $res['path'];
        }

        if ($id) {
            $model->update($id, $data);
            $portfolioId = $id;
            $_SESSION['success_msg'] = 'Caso de éxito actualizado.';
        } else {
            $portfolioId = $model->create($data);
            $_SESSION['success_msg'] = 'Caso de éxito creado.';
        }

        // 3. Procesar Galería de Imágenes Adicionales
        if ($portfolioId) {
            $galleryImages = [];

            // 3.1 URLs Externas de la Galería
            if (isset($_POST['gallery_urls']) && is_array($_POST['gallery_urls'])) {
                foreach ($_POST['gallery_urls'] as $url) {
                    if (!empty(trim($url))) {
                        $galleryImages[] = [
                            'path' => trim($url),
                            'source' => (strpos($url, 'ibb.co') !== false) ? 'api' : 'url'
                        ];
                    }
                }
            }

            // 3.2 Imágenes Locales de la Galería
            if (isset($_FILES['gallery_local']) && !empty($_FILES['gallery_local']['name'][0])) {
                $files = $_FILES['gallery_local'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $singleFile = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];
                        $uploaded = $this->uploadPortfolioImage($singleFile);
                        if ($uploaded) {
                            $galleryImages[] = ['path' => $uploaded, 'source' => 'local'];
                        }
                    }
                }
            }

            // 3.3 Imágenes ImgBB de la Galería
            if (isset($_FILES['gallery_imgbb']) && !empty($_FILES['gallery_imgbb']['name'][0]) && $imgbbKey) {
                $files = $_FILES['gallery_imgbb'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $resImg = \App\Services\ImageService::uploadToImgBB($files['tmp_name'][$i], $imgbbKey);
                        if ($resImg) {
                            $galleryImages[] = $resImg;
                        }
                    }
                }
            }

            // Guardar en la base de datos
            if (!empty($galleryImages)) {
                $model->setGallery($portfolioId, $galleryImages);
            }
        }

        $this->redirect('admin/portfolio');
    }

    public function portfolio_eliminar($id = null)
    {
        if ($id) {
            require_once BASE_PATH . 'app/Models/PortfolioModel.php';
            $model = new \App\Models\PortfolioModel();
            $model->delete($id);
            $_SESSION['success_msg'] = 'Caso de éxito eliminado.';
        }
        $this->redirect('admin/portfolio');
    }

    private function uploadPortfolioImage($file)
    {
        // Calidad 80 para la imagen principal del portafolio (buen balance SEO/Visual)
        $result = ImageService::processUpload($file, 80);
        return $result ? $result['path'] : null;
    }


    /** AJAX: lista imágenes disponibles para el selector de logo */
    public function images_json()
    {
        header('Content-Type: application/json');
        $images = [];

        // 1. assets/img/ (logos, banners, etc.)
        $imgDir = BASE_PATH . 'public/assets/img/';
        $allowed = ['webp', 'png', 'jpg', 'jpeg', 'gif', 'svg'];
        if (is_dir($imgDir)) {
            foreach (glob($imgDir . '*') as $f) {
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (in_array($ext, $allowed)) {
                    $images[] = [
                        'url' => 'assets/img/' . basename($f),
                        'full' => APP_URL . 'assets/img/' . basename($f),
                        'label' => basename($f),
                        'source' => 'local',
                    ];
                }
            }
        }

        // 2. storage/productos/ (imágenes de productos comprimidas a WebP)
        $storDir = BASE_PATH . 'public/storage/productos/';
        if (is_dir($storDir)) {
            foreach (glob($storDir . '*.{webp,jpg,jpeg,png}', GLOB_BRACE) as $f) {
                $images[] = [
                    'url' => 'storage/productos/' . basename($f),
                    'full' => APP_URL . 'storage/productos/' . basename($f),
                    'label' => basename($f),
                    'source' => 'local',
                ];
            }
        }

        // 3. ImgBB / URLs externas de product_images
        $db = \App\Core\Database::getInstance();
        try {
            $apiRows = $db->fetchAll(
                "SELECT image_path, source, MAX(id) as max_id 
                 FROM product_images 
                 WHERE source IN ('api','url') AND image_path IS NOT NULL AND image_path != '' 
                 GROUP BY image_path, source 
                 ORDER BY max_id DESC 
                 LIMIT 80"
            );
            foreach ($apiRows as $row) {
                $images[] = [
                    'url' => $row['image_path'],
                    'full' => ImageService::buildUrl($row['image_path'], $row['source']),
                    'label' => basename(parse_url($row['image_path'], PHP_URL_PATH)) ?: 'Imagen',
                    'source' => $row['source'],
                ];
            }
        } catch (\Exception $e) {
            // Log error si es necesario, por ahora mantenemos el flujo para que no rompa el JSON local
        }

        echo json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // ==== CATEGORÍAS ====
    public function categorias()
    {
        $categoryModel = $this->model('CategoryModel');
        $data = [
            'title' => 'Gestión de Categorías',
            'items' => $categoryModel->getAll(),
            'success' => $_SESSION['success_msg'] ?? null,
            'error' => $_SESSION['error_msg'] ?? null
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);
        $this->view('admin/productos/categorias', $data);
    }

    public function categoria_guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/categorias');
            return;
        }

        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'físico';

        if (!$name) {
            $_SESSION['error_msg'] = 'El nombre es obligatorio.';
            $this->redirect('admin/categorias');
            return;
        }

        $slug = SlugService::generate($name);
        $categoryModel = $this->model('CategoryModel');

        $data = [
            'name' => $name,
            'slug' => $slug,
            'type' => $type
        ];

        if ($id) {
            $res = $categoryModel->update($id, $data);
            $msg = "Categoría '$name' actualizada.";
        } else {
            $res = $categoryModel->create($data);
            $msg = "Categoría '$name' creada.";
        }

        if ($res) {
            $_SESSION['success_msg'] = $msg;
        } else {
            $_SESSION['error_msg'] = "Error al procesar la categoría.";
        }

        $this->redirect('admin/categorias');
    }

    public function categoria_eliminar($id = null)
    {
        if ($id) {
            $categoryModel = $this->model('CategoryModel');

            if ($categoryModel->isInUse($id)) {
                $_SESSION['error_msg'] = "No se puede eliminar la categoría porque tiene productos asociados. Primero cambia los productos de categoría.";
            } else {
                $categoryModel->delete($id);
                $_SESSION['success_msg'] = "Categoría eliminada correctamente.";
            }
        }
        $this->redirect('admin/categorias');
    }
}
