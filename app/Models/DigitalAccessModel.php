<?php
namespace App\Models;

use App\Core\Model;

class DigitalAccessModel extends Model
{
    /**
     * Crea un nuevo registro de acceso para un archivo digital.
     * Genera un token único y lo asocia al ítem de la orden.
     */
    public function createAccess($orderItemId, $userId, $filePath, $hoursToExpire = 72)
    {
        // Generar un token único
        $token = bin2hex(random_bytes(16));

        // Calcular fecha de expiración
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$hoursToExpire hours"));

        $sql = "INSERT INTO digital_access (order_item_id, user_id, file_path, download_token, expires_at, downloads_count) 
                VALUES (:order_item_id, :user_id, :file_path, :download_token, :expires_at, 0)";

        $params = [
            'order_item_id' => $orderItemId,
            'user_id' => $userId,
            'file_path' => $filePath,
            'download_token' => $token,
            'expires_at' => $expiresAt
        ];

        $this->db->query($sql, $params);
        return $token;
    }

    /**
     * Obtiene la información de acceso digital validando el token.
     * Opcionalmente valida contra un user_id si se requiere seguridad adicional.
     */
    public function getByToken($token, $userId = null)
    {
        if ($userId) {
            $sql = "SELECT da.*, oi.product_id, p.name as product_name
                    FROM digital_access da
                    INNER JOIN order_items oi ON da.order_item_id = oi.id
                    INNER JOIN products p ON oi.product_id = p.id
                    WHERE da.download_token = :token AND da.user_id = :user_id";
            return $this->db->fetch($sql, ['token' => $token, 'user_id' => $userId]);
        } else {
            $sql = "SELECT da.*, oi.product_id, p.name as product_name
                    FROM digital_access da
                    INNER JOIN order_items oi ON da.order_item_id = oi.id
                    INNER JOIN products p ON oi.product_id = p.id
                    WHERE da.download_token = :token";
            return $this->db->fetch($sql, ['token' => $token]);
        }
    }

    /**
     * Obtiene todo el catálogo de diseños digitales comprados por un usuario (asociado a sus órdenes).
     * Nota: En esta arquitectura, el user_id de digital_access corresponde a un identificador propio del cliente (el order_id o user_id generado).
     */
    public function getUserAccesses($userId)
    {
        $sql = "SELECT da.*, oi.product_id, p.name as product_name, p.image_url 
                FROM digital_access da
                INNER JOIN order_items oi ON da.order_item_id = oi.id
                INNER JOIN products p ON oi.product_id = p.id
                WHERE da.user_id = :user_id
                ORDER BY da.created_at DESC";
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Obtiene todos los accesos digitales asociados a un email de cliente (multi-orden).
     */
    public function getAccessesByEmail($email)
    {
        $sql = "SELECT da.*, oi.product_id, p.name as product_name, p.image_url, o.id as order_id, o.created_at as order_created_at
                FROM digital_access da
                INNER JOIN order_items oi ON da.order_item_id = oi.id
                INNER JOIN products p ON oi.product_id = p.id
                INNER JOIN orders o ON oi.order_id = o.id
                WHERE o.customer_email = :email
                ORDER BY o.created_at DESC, da.created_at DESC";
        return $this->db->fetchAll($sql, ['email' => $email]);
    }

    /**
     * Incrementa el contador de descargas de un token específico tras una descarga exitosa.
     */
    public function incrementDownload($token)
    {
        $sql = "UPDATE digital_access 
                SET downloads_count = downloads_count + 1 
                WHERE download_token = :token";
        return $this->db->query($sql, ['token' => $token]);
    }

    /**
     * Verifica si el token aún es válido (No expirado, menor a límite de descargas).
     */
    public function isTokenValid($access)
    {
        if (!$access)
            return false;

        // Validar Estado Manual
        if (isset($access['is_active']) && $access['is_active'] == 0) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        if ($access['expires_at'] < $now) {
            return false; // Expirado
        }

        $limit = $access['download_limit'] ?? 3;
        if ($access['downloads_count'] >= $limit) {
            return false; // Superó límite de descargas
        }

        return true;
    }

    /**
     * Actualiza un registro de acceso digital.
     */
    public function updateAccess($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        $sql = "UPDATE digital_access SET " . implode(', ', $fields) . " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    /**
     * Obtiene el listado completo de todas las entregas digitales para el panel administrativo.
     */
    public function getAllAdminAccesses()
    {
        $sql = "SELECT da.*, 
                       p.name as product_name, 
                       o.customer_name, 
                       o.customer_email,
                       o.digital_user,
                       o.digital_pass
                FROM digital_access da
                INNER JOIN order_items oi ON da.order_item_id = oi.id
                INNER JOIN products p ON oi.product_id = p.id
                INNER JOIN orders o ON oi.order_id = o.id
                ORDER BY da.created_at DESC";
        return $this->db->fetchAll($sql);
    }
}
