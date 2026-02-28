<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    /**
     * Verificar credenciales del administrador (O cliente)
     * @param string $username
     * @param string $password
     * @return array|bool Retorna el array del usuario o false
     */
    public function authenticate($username, $password)
    {
        $sql = "SELECT id, username, password_hash, role, email FROM users WHERE username = :username LIMIT 1";
        $user = $this->db->fetch($sql, ['username' => $username]);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Eliminar el hash del array antes de devolverlo por seguridad en la sesión
            unset($user['password_hash']);
            return $user;
        }
        return false;
    }
}
