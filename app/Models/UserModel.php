<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    /**
     * Verificar credenciales del administrador
     * @param string $username
     * @param string $password
     * @return array|bool Retorna el array del usuario o false
     */
    public function authenticate($username, $password)
    {
        $sql = "SELECT u.id, u.username, u.password_hash, u.role, u.role_id,
                       COALESCE(r.is_system, 0) AS is_system
                FROM users u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE u.username = :username LIMIT 1";
        $user = $this->db->fetch($sql, ['username' => $username]);

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
        return false;
    }

    /**
     * Buscar usuario por email (para recuperación de contraseña)
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, username, email, role FROM users WHERE email = :email LIMIT 1";
        $user = $this->db->fetch($sql, ['email' => $email]);
        return $user ?: null;
    }

    /**
     * Buscar usuario por username (para recuperación de contraseña)
     * @param string $username
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT id, username, email, role FROM users WHERE username = :username LIMIT 1";
        $user = $this->db->fetch($sql, ['username' => $username]);
        return $user ?: null;
    }

    /**
     * Crear un token de recuperación de contraseña (expira en 1 hora).
     * Elimina tokens previos del mismo usuario para garantizar uno activo a la vez.
     * @param int    $userId
     * @param string $token
     * @return bool
     */
    public function createPasswordResetToken(int $userId, string $token): bool
    {
        // Limpiar tokens previos del usuario
        $this->db->query(
            "DELETE FROM password_resets WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hora

        $stmt = $this->db->query(
            "INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)",
            ['user_id' => $userId, 'token' => $token, 'expires_at' => $expiresAt]
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Buscar token válido (no expirado) y retornar datos del usuario asociado
     * @param string $token
     * @return array|null Array con user_id, email, username — o null si inválido/expirado
     */
    public function findValidResetToken(string $token): ?array
    {
        $sql = "SELECT pr.user_id, u.email, u.username
                FROM password_resets pr
                JOIN users u ON u.id = pr.user_id
                WHERE pr.token = :token AND pr.expires_at > NOW()
                LIMIT 1";
        $result = $this->db->fetch($sql, ['token' => $token]);
        return $result ?: null;
    }

    /**
     * Eliminar token usado (limpieza post-reset)
     * @param string $token
     * @return bool
     */
    public function deleteResetToken(string $token): bool
    {
        $stmt = $this->db->query(
            "DELETE FROM password_resets WHERE token = :token",
            ['token' => $token]
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Actualizar contraseña cifrada del usuario
     * @param int    $userId
     * @param string $hash  Hash generado con password_hash()
     * @return bool
     */
    public function updatePassword(int $userId, string $hash): bool
    {
        $stmt = $this->db->query(
            "UPDATE users SET password_hash = :hash WHERE id = :id",
            ['hash' => $hash, 'id' => $userId]
        );
        return $stmt->rowCount() > 0;
    }

    // =====================================================
    // CRUD DE USUARIOS (Gestión desde el Panel Admin)
    // =====================================================

    /**
     * Listar todos los usuarios
     */
    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT u.id, u.username, u.email, u.role, u.role_id, u.created_at,
                    COALESCE(r.name, u.role) AS role_name,
                    COALESCE(r.is_system, 0)  AS is_system,
                    r.description             AS role_description
             FROM users u
             LEFT JOIN roles r ON r.id = u.role_id
             ORDER BY u.created_at DESC"
        ) ?: [];
    }

    /**
     * Obtener usuario por ID
     */
    public function getById(int $id): ?array
    {
        $user = $this->db->fetch(
            "SELECT u.id, u.username, u.email, u.role, u.role_id,
                    COALESCE(r.is_system, 0) AS is_system
             FROM users u
             LEFT JOIN roles r ON r.id = u.role_id
             WHERE u.id = :id LIMIT 1",
            ['id' => $id]
        );
        return $user ?: null;
    }

    /**
     * Crear nuevo usuario
     */
    public function create(array $data): int|false
    {
        $stmt = $this->db->query(
            "INSERT INTO users (username, email, password_hash, role, role_id) VALUES (:username, :email, :hash, :role, :role_id)",
            [
                'username' => $data['username'],
                'email' => $data['email'],
                'hash' => password_hash($data['password'], PASSWORD_BCRYPT),
                'role' => 'admin', // legado; el control real es role_id
                'role_id' => $data['role_id'] ?? null,
            ]
        );
        if ($stmt->rowCount() > 0) {
            return (int) $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Actualizar usuario (contraseña opcional)
     */
    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $stmt = $this->db->query(
                "UPDATE users SET username = :username, email = :email, role_id = :role_id, password_hash = :hash WHERE id = :id",
                [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'role_id' => $data['role_id'] ?? null,
                    'hash' => password_hash($data['password'], PASSWORD_BCRYPT),
                    'id' => $id,
                ]
            );
        } else {
            $stmt = $this->db->query(
                "UPDATE users SET username = :username, email = :email, role_id = :role_id WHERE id = :id",
                [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'role_id' => $data['role_id'] ?? null,
                    'id' => $id,
                ]
            );
        }
        return $stmt->rowCount() >= 0;
    }

    /**
     * Eliminar usuario
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->query("DELETE FROM users WHERE id = :id", ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Verificar si username ya existe (excluyendo un ID opcional)
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $row = $this->db->fetch(
                "SELECT id FROM users WHERE username = :u AND id != :id LIMIT 1",
                ['u' => $username, 'id' => $excludeId]
            );
        } else {
            $row = $this->db->fetch("SELECT id FROM users WHERE username = :u LIMIT 1", ['u' => $username]);
        }
        return (bool) $row;
    }

    /**
     * Verificar si email ya existe (excluyendo un ID opcional)
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $row = $this->db->fetch(
                "SELECT id FROM users WHERE email = :e AND id != :id LIMIT 1",
                ['e' => $email, 'id' => $excludeId]
            );
        } else {
            $row = $this->db->fetch("SELECT id FROM users WHERE email = :e LIMIT 1", ['e' => $email]);
        }
        return (bool) $row;
    }
}

