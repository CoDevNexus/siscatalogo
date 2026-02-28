<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;

class SysloginController extends Controller
{

    public function index()
    {
        // Redirigir si ya está logueado
        if (isset($_SESSION['user_id'])) {
            $this->redirect('admin');
        }

        $data = [
            'title' => 'Acceso Restringido',
            'csrf_token' => Security::generateCsrfToken(),
            'error' => ''
        ];

        // Se usa view simple, sin header/footer públicos
        $this->view('admin/login', $data);
    }

    public function auth()
    {
        // Verificación de método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('syslogin');
        }

        // 1. Verificación Honeypot (Campo oculto que los bots suelen rellenar)
        if (!empty($_POST['website_url'])) {
            // Es un bot, lo mandamos lejos sutilmente
            $this->redirect('');
        }

        // 2. Verificación CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($token)) {
            die('Error de validación de seguridad (CSRF). Intente nuevamente.');
        }

        // 3. Rate Limiting (Protección Fuerza Bruta)
        if (!Security::checkLoginAttempts()) {
            $data = [
                'title' => 'Acceso Restringido',
                'csrf_token' => Security::generateCsrfToken(),
                'error' => 'Demasiados intentos fallidos. Intente en 15 minutos.'
            ];
            $this->view('admin/login', $data);
            return;
        }

        // Procesar Login
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = $this->model('UserModel');
        $user = $userModel->authenticate($username, $password);

        if ($user && $user['role'] === 'admin') {
            Security::resetLoginAttempts();

            // Regenerar ID de Sesión para prevenir Session Fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Guardar log interno si es necesario
            $this->redirect('admin');
        } else {
            Security::recordFailedLogin();
            $data = [
                'title' => 'Acceso Restringido',
                'csrf_token' => Security::generateCsrfToken(),
                'error' => 'Credenciales inválidas.'
            ];
            $this->view('admin/login', $data);
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        // Redirigir al catálogo público ocultando que existe /syslogin
        $this->redirect('');
    }
}
