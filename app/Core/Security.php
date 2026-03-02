<?php
namespace App\Core;

class Security
{
    // Generar un token CSRF seguro y almacenarlo en la sesión
    public static function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (\Exception $e) {
                // Fallback en caso de que random_bytes falle (poco probable en PHP 7/8)
                $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
            }
        }
        return $_SESSION['csrf_token'];
    }

    // Validar el token CSRF enviado desde un formulario
    public static function validateCsrfToken($token)
    {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        }
        return false;
    }

    // Rate Limiting Básico usando la Sesión (Prevenir fuerza bruta simple)
    public static function checkLoginAttempts()
    {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = time();
        }

        // Si falló más de 5 veces, bloquear por 15 minutos
        if ($_SESSION['login_attempts'] >= 5) {
            $blocked_time = 15 * 60; // 15 minutos
            if (time() - $_SESSION['last_attempt_time'] < $blocked_time) {
                return false; // Autenticación bloqueada
            } else {
                // Resetear luego de expirar el tiempo
                self::resetLoginAttempts();
            }
        }
        return true;
    }

    public static function recordFailedLogin()
    {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_attempt_time'] = time();
    }

    public static function resetLoginAttempts()
    {
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['last_attempt_time']);
    }

    /**
     * Guard de sesión general para el sistema administrativo.
     * Permite el ingreso a cualquier usuario con sesión activa.
     * El control de acceso a módulos específicos se realiza mediante roles y permisos.
     */
    public static function requireAdmin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . 'syslogin');
            exit;
        }
    }

    /**
     * Cargar y cachear en sesión los permisos del usuario actual.
     * Solo hace la consulta DB una vez por sesión.
     */
    public static function getUserPermissions(): array
    {
        if (isset($_SESSION['_perms'])) {
            return $_SESSION['_perms'];
        }
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return [];
        }
        try {
            require_once BASE_PATH . 'app/Models/RoleModel.php';
            $roleModel = new \App\Models\RoleModel();
            $perms = $roleModel->getUserPermissions((int) $userId);
            $_SESSION['_perms'] = $perms;
            return $perms;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Invalidar la caché de permisos (llamar al cambiar el rol de un usuario)
     */
    public static function clearPermissionsCache(): void
    {
        unset($_SESSION['_perms']);
    }

    /**
     * Verificar si el usuario actual tiene un permiso específico.
     * El rol Admin (is_system=1) siempre retorna true.
     *
     * @param string $slug  ej: "productos.editar"
     */
    public static function can(string $slug): bool
    {
        // Admins del sistema tienen todo
        if ($_SESSION['is_system'] ?? false)
            return true;
        if (($_SESSION['role'] ?? '') === 'admin')
            return true;

        return in_array($slug, self::getUserPermissions(), true);
    }

    /**
     * Verificar permiso y mostrar error 403 si no lo tiene.
     */
    public static function canOrFail(string $slug): void
    {
        if (!self::can($slug)) {
            http_response_code(403);
            $title = 'Acceso denegado';
            $msg = "No tienes permiso para realizar esta acción (<code>$slug</code>).";
            include BASE_PATH . 'app/Views/admin/403.php';
            exit;
        }
    }

    /**
     * Registrar una acción en la bitácora de actividad.
     */
    public static function logActivity(string $action, string $module = '', string $detail = ''): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? 'sistema';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        try {
            require_once BASE_PATH . 'app/Models/ActivityLogModel.php';
            $logModel = new \App\Models\ActivityLogModel();
            $logModel->log($userId, $username, $action, $module, $detail, $ip);
        } catch (\Throwable $e) {
            // Log silencioso — no interrumpir el flujo de la aplicación
        }
    }
}

