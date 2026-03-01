<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\DigitalAccessModel;

class DigitalController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new DigitalAccessModel();
    }

    /**
     * Pantalla de login para el cliente
     */
    public function login()
    {
        // Si ya está logueado como cliente digital, redirigir al portal
        if (isset($_SESSION['digital_logged_in']) && $_SESSION['digital_logged_in'] === true) {
            $this->redirect('digital/portal');
        }

        $data = [
            'title' => 'Acceso a Descargas Digitales',
            'error' => $_SESSION['error_msg'] ?? null
        ];
        unset($_SESSION['error_msg']);

        $this->view('digital/login', $data);
    }

    /**
     * Procesa la autenticación
     */
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('digital/login');
        }

        $user = trim($_POST['digital_user'] ?? '');
        $pass = trim($_POST['digital_pass'] ?? '');

        if (!$user || !$pass) {
            $_SESSION['error_msg'] = 'Por favor, ingrese sus credenciales.';
            $this->redirect('digital/login');
        }

        $db = \App\Core\Database::getInstance();
        $sql = "SELECT id, customer_name, customer_email, digital_user FROM orders 
                WHERE digital_user = :user AND digital_pass = :pass LIMIT 1";
        $order = $db->fetch($sql, ['user' => $user, 'pass' => $pass]);

        if ($order) {
            // Iniciar sesión digital aislada del administrador
            $_SESSION['digital_logged_in'] = true;
            $_SESSION['digital_order_id'] = $order['id']; // ID de la orden actual logueada
            $_SESSION['digital_customer_email'] = $order['customer_email']; // Email para agrupar productos
            $_SESSION['digital_customer_name'] = $order['customer_name'];

            $this->redirect('digital/portal');
        } else {
            $_SESSION['error_msg'] = 'Usuario o contraseña incorrectos.';
            $this->redirect('digital/login');
        }
    }

    /**
     * Portal del cliente ("Mis Diseños")
     */
    public function portal()
    {
        if (!isset($_SESSION['digital_logged_in']) || $_SESSION['digital_logged_in'] !== true) {
            $this->redirect('digital/login');
        }

        $email = $_SESSION['digital_customer_email'];
        $accesses = $this->model->getAccessesByEmail($email);

        $data = [
            'title' => 'Mis Diseños Digitales',
            'customer_name' => $_SESSION['digital_customer_name'],
            'accesses' => $accesses
        ];

        $this->view('digital/portal', $data);
    }

    /**
     * Cierra la sesión
     */
    public function logout()
    {
        unset($_SESSION['digital_logged_in']);
        unset($_SESSION['digital_order_id']);
        unset($_SESSION['digital_customer_email']);
        unset($_SESSION['digital_customer_name']);

        $this->redirect('digital/login');
    }

    /**
     * Lógica de Descarga Segura
     */
    public function download($token = null)
    {
        if (!$token) {
            die("Token no proporcionado.");
        }

        if (!isset($_SESSION['digital_logged_in']) || $_SESSION['digital_logged_in'] !== true) {
            die("Acceso denegado. Inicie sesión.");
        }

        $orderId = $_SESSION['digital_order_id'];
        $access = $this->model->getByToken($token, $orderId);

        if (!$this->model->isTokenValid($access)) {
            die("El enlace de descarga es inválido, ha expirado o ha superado el límite de descargas.");
        }

        $filePath = BASE_PATH . $access['file_path'];

        if (!file_exists($filePath)) {
            die("El archivo ya no existe en el servidor.");
        }

        // Incrementar descargas
        $this->model->incrementDownload($token);

        // Forzar descarga del archivo
        $fileName = basename($filePath);
        $mimeType = mime_content_type($filePath);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . ($mimeType ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Limpiar output buffering para descargar archivos grandes
        if (ob_get_level()) {
            ob_end_clean();
        }

        readfile($filePath);
        exit;
    }
}
