<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CotizacionModel;
use App\Models\CompanyModel;

class CotizacionController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new CotizacionModel();
    }

    // --- ACCIONES PÚBLICAS ---

    public function confirmar()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['items'])) {
            echo json_encode(['status' => 'error', 'message' => 'Datos de pedido inválidos']);
            return;
        }

        try {
            // Asegurar que las banderas lleguen
            $data['needs_shipping'] = isset($data['shipping_amount']) && $data['shipping_amount'] > 0 ? 1 : 0;
            $data['needs_invoice'] = isset($data['tax_amount']) && $data['tax_amount'] > 0 ? 1 : 0;

            $orderId = $this->model->create($data, $data['items']);
            echo json_encode(['status' => 'success', 'order_id' => $orderId]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }

    // --- ACCIONES ADMINISTRATIVAS ---

    public function admin_index()
    {
        $this->checkAdmin();
        $filters = [
            'name' => $_GET['name'] ?? '',
            'city' => $_GET['city'] ?? '',
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        // Paginación
        $limit = 15;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $limit;

        // Ordenamiento
        $order_by = $_GET['order_by'] ?? 'created_at';
        $order_dir = $_GET['order_dir'] ?? 'DESC';

        $cotizaciones = $this->model->getAll($filters, $limit, $offset, $order_by, $order_dir);
        $totalItems = $this->model->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $this->view('admin/cotizaciones/index', [
            'cotizaciones' => $cotizaciones,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems
            ],
            'order' => [
                'by' => $order_by,
                'dir' => $order_dir
            ],
            'title' => 'Gestión de Cotizaciones'
        ]);
    }

    public function get_detalle($id)
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        $order = $this->model->getById($id);
        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'No encontrada']);
            return;
        }

        $items = $this->model->getItems($id);
        $order['items'] = $items;

        echo json_encode(['status' => 'success', 'data' => $order]);
    }

    public function actualizar_estado()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
            return;
        }

        if ($this->model->updateStatus($id, $status)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar']);
        }
    }

    public function aprobar_pago_digital()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID requerido']);
            return;
        }

        $order = $this->model->getById($id);
        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Orden no encontrada']);
            return;
        }

        // Generar credenciales
        $user = $order['digital_user'] ?: strtolower($order['customer_email']);
        $pass = $order['digital_pass'] ?: substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        if ($this->model->approveDigital($id, $user, $pass)) {
            // Verificar si hay ítems digitales y generar los accesos en digital_access
            $items = $this->model->getItems($id);
            $hasDigitalItems = false;

            require_once BASE_PATH . 'app/Models/DigitalAccessModel.php';
            $accessModel = new \App\Models\DigitalAccessModel();

            foreach ($items as $item) {
                if ($item['is_digital'] == 1) {
                    $filePath = !empty($item['digital_file_path']) ? $item['digital_file_path'] : '';
                    $accessModel->createAccess($item['id'], $id, $filePath, 72);
                    $hasDigitalItems = true;
                }
            }

            // Enviar correo con credenciales
            require_once BASE_PATH . 'app/Services/MailService.php';
            $db = \App\Core\Database::getInstance();
            $company = $db->fetch("SELECT * FROM company_profile WHERE id = 1");
            $html = \App\Services\MailService::getDigitalDeliveryHtml($order, $company, $user, $pass);
            \App\Services\MailService::send($order['customer_email'], 'Tus Accesos Digitales - ' . $company['name'], $html);

            // Limpiar cualquier salida previa (warnings de mail(), etc) para no romper el JSON
            if (ob_get_level()) {
                ob_end_clean();
            }

            echo json_encode([
                'status' => 'success',
                'user' => $user,
                'pass' => $pass,
                'message' => $hasDigitalItems ? 'Accesos digitales listos y enviados por correo.' : 'Sin ítems digitales.'
            ]);
        } else {
            if (ob_get_level()) {
                ob_end_clean();
            }
            echo json_encode(['status' => 'error', 'message' => 'Error al aprobar']);
        }
    }

    public function actualizar_orden()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if (!$id || empty($data['items'])) {
            echo json_encode(['status' => 'error', 'message' => 'Datos insuficientes']);
            return;
        }

        if ($this->model->updateOrder($id, $data, $data['items'])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar']);
        }
    }

    public function exportar_pdf($id)
    {
        $this->checkAdmin();
        // Pendiente: Integrar DomPDF o similar.
        // Por ahora simulamos servir el HTML o error si no está la lib.
        die("Funcionalidad PDF en proceso de integración. Requiere DomPDF.");
    }

    private function checkAdmin()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . APP_URL . 'admin/login');
            exit();
        }
    }

    public function enviar_email()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID de pedido no proporcionado']);
            return;
        }

        $order = $this->model->getById($id);
        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Pedido no encontrado']);
            return;
        }
        $order['items'] = $this->model->getItems($id);

        $companyModel = new \App\Models\CompanyModel();
        $company = $companyModel->getProfile();

        $html = \App\Services\MailService::getOrderHtml($order, $company);
        $result = \App\Services\MailService::send($order['customer_email'], "Detalle de su Pedido #{$id} - {$company['name']}", $html);

        echo json_encode($result);
    }
}
