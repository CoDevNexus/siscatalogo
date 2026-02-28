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
}
