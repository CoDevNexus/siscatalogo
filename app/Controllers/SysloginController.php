<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Services\MailService;

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

        // Vista standalone (sin header/footer públicos ni admin)
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

        // Permitir login a cualquier usuario validado. El control de acceso a módulos 
        // se maneja a través del sistema de Roles y Permisos internamente.
        if ($user) {
            Security::resetLoginAttempts();
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            $_SESSION['role_id'] = $user['role_id'] ?? null;
            $_SESSION['is_system'] = (bool) ($user['is_system'] ?? false);

            Security::logActivity('Inicio de sesión exitoso', 'login', "Usuario: {$user['username']}");
            $this->redirect('admin');
        } else {
            Security::recordFailedLogin();
            // Registrar intento fallido (sin datos de usuario para no revelar existencia)
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            require_once BASE_PATH . 'app/Models/ActivityLogModel.php';
            $lg = new \App\Models\ActivityLogModel();
            $lg->log(null, $username, 'Intento de login fallido', 'login', "IP: $ip", $ip);

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
        Security::logActivity('Cierre de sesión', 'login', "Usuario: " . ($_SESSION['username'] ?? 'desconocido'));
        session_unset();
        session_destroy();
        // Redirigir al catálogo público ocultando que existe /syslogin
        $this->redirect('');
    }

    // =====================================================
    // RECUPERACIÓN DE CONTRASEÑA
    // =====================================================

    /**
     * GET: Muestra el formulario para pedir el email de recuperación
     */
    public function forgot()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('admin');
        }

        $data = [
            'title' => 'Recuperar Contraseña',
            'csrf_token' => Security::generateCsrfToken(),
            'success' => '',
            'error' => ''
        ];
        $this->view('admin/forgot_password', $data);
    }

    /**
     * POST: Valida el email/username y envía el enlace de recuperación
     */
    public function forgot_send()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('syslogin/forgot');
        }

        // CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($token)) {
            die('Error de validación CSRF. Intente nuevamente.');
        }

        $input = trim($_POST['email'] ?? '');
        $userModel = $this->model('UserModel');

        // Buscar por email o por username (lo que el admin ingrese)
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $user = $userModel->findByEmail($input);
        } else {
            $user = $userModel->findByUsername($input);
        }

        // Mensaje genérico siempre — no revelar si el usuario existe o no
        $successMsg = 'Si ese usuario o correo está registrado, recibirás un enlace de recuperación en breve.';

        if ($user && $user['role'] === 'admin' && !empty($user['email'])) {
            // Generar token criptográficamente seguro
            try {
                $resetToken = bin2hex(random_bytes(32));
            } catch (\Exception $e) {
                $resetToken = bin2hex(openssl_random_pseudo_bytes(32));
            }

            $userModel->createPasswordResetToken((int) $user['id'], $resetToken);

            $resetUrl = APP_URL . 'syslogin/reset/' . $resetToken;

            // Enviar email
            MailService::sendPasswordReset($user['email'], $user['username'], $resetUrl);
        }

        $data = [
            'title' => 'Recuperar Contraseña',
            'csrf_token' => Security::generateCsrfToken(),
            'success' => $successMsg,
            'error' => ''
        ];
        $this->view('admin/forgot_password', $data);
    }

    /**
     * GET: Muestra el formulario de nueva contraseña (valida el token en la URL)
     */
    public function reset($token = null)
    {
        if (!$token) {
            $this->redirect('syslogin');
        }

        $userModel = $this->model('UserModel');
        $record = $userModel->findValidResetToken($token);

        if (!$record) {
            $data = [
                'title' => 'Enlace inválido',
                'csrf_token' => Security::generateCsrfToken(),
                'error' => 'Este enlace ya expiró o no es válido. Solicita uno nuevo.',
                'token' => '',
                'valid' => false
            ];
        } else {
            $data = [
                'title' => 'Nueva Contraseña',
                'csrf_token' => Security::generateCsrfToken(),
                'error' => '',
                'token' => $token,
                'valid' => true
            ];
        }

        $this->view('admin/reset_password', $data);
    }

    /**
     * POST: Valida token + contraseñas, actualiza la clave y redirige al login
     */
    public function reset_save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('syslogin');
        }

        // CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            die('Error de validación CSRF. Intente nuevamente.');
        }

        $token = $_POST['reset_token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        $userModel = $this->model('UserModel');
        $record = $userModel->findValidResetToken($token);

        if (!$record) {
            $data = [
                'title' => 'Enlace inválido',
                'csrf_token' => Security::generateCsrfToken(),
                'error' => 'Este enlace ya expiró o no es válido. Solicita uno nuevo.',
                'token' => '',
                'valid' => false
            ];
            $this->view('admin/reset_password', $data);
            return;
        }

        // Validar contraseñas
        if (strlen($password) < 8) {
            $data = [
                'title' => 'Nueva Contraseña',
                'csrf_token' => Security::generateCsrfToken(),
                'error' => 'La contraseña debe tener al menos 8 caracteres.',
                'token' => $token,
                'valid' => true
            ];
            $this->view('admin/reset_password', $data);
            return;
        }

        if ($password !== $confirm) {
            $data = [
                'title' => 'Nueva Contraseña',
                'csrf_token' => Security::generateCsrfToken(),
                'error' => 'Las contraseñas no coinciden.',
                'token' => $token,
                'valid' => true
            ];
            $this->view('admin/reset_password', $data);
            return;
        }

        // Actualizar contraseña y limpiar token
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $userModel->updatePassword((int) $record['user_id'], $newHash);
        $userModel->deleteResetToken($token);

        // Destruir sesión existente por seguridad
        session_unset();
        session_destroy();
        session_start();

        $_SESSION['login_success'] = '✅ Contraseña actualizada. Inicia sesión con tu nueva clave.';
        $this->redirect('syslogin');
    }
}
