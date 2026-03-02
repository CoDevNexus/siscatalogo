<?php
namespace App\Models;

use App\Core\Model;

class RoleModel extends Model
{
    /**
     * Obtener todos los roles con conteo de usuarios y permisos
     */
    public function getAll(): array
    {
        return $this->db->fetchAll("
            SELECT r.*,
                   COUNT(DISTINCT u.id)  AS user_count,
                   COUNT(DISTINCT rp.permission_id) AS perm_count
            FROM roles r
            LEFT JOIN users u ON u.role_id = r.id
            LEFT JOIN role_permissions rp ON rp.role_id = r.id
            GROUP BY r.id
            ORDER BY r.is_system DESC, r.name
        ") ?: [];
    }

    /**
     * Obtener un rol por ID
     */
    public function getById(int $id): ?array
    {
        $role = $this->db->fetch("SELECT * FROM roles WHERE id = :id LIMIT 1", ['id' => $id]);
        return $role ?: null;
    }

    /**
     * Obtener slugs de permisos asignados a un rol
     */
    public function getPermissionSlugs(int $roleId): array
    {
        $rows = $this->db->fetchAll("
            SELECT p.slug FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id
        ", ['role_id' => $roleId]);
        return array_column($rows ?: [], 'slug');
    }

    /**
     * Obtener slugs de permisos de un usuario por su role_id
     */
    public function getUserPermissions(int $userId): array
    {
        $rows = $this->db->fetchAll("
            SELECT p.slug FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            JOIN users u ON u.role_id = rp.role_id
            WHERE u.id = :user_id
        ", ['user_id' => $userId]);
        return array_column($rows ?: [], 'slug');
    }

    /**
     * Crear un nuevo rol
     */
    public function create(array $data): int|false
    {
        $stmt = $this->db->query(
            "INSERT INTO roles (name, description, is_system) VALUES (:name, :desc, 0)",
            ['name' => $data['name'], 'desc' => $data['description'] ?? null]
        );
        return $stmt->rowCount() > 0 ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Actualizar nombre y descripción de un rol
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->query(
            "UPDATE roles SET name = :name, description = :desc WHERE id = :id",
            ['name' => $data['name'], 'desc' => $data['description'] ?? null, 'id' => $id]
        );
        return $stmt->rowCount() >= 0;
    }

    /**
     * Sincronizar permisos del rol (reemplaza todos los permisos actuales)
     */
    public function setPermissions(int $roleId, array $slugs): void
    {
        // 1. Borrar permisos actuales
        $this->db->query("DELETE FROM role_permissions WHERE role_id = :id", ['id' => $roleId]);

        if (empty($slugs))
            return;

        // 2. Insertar los nuevos por cada slug
        foreach ($slugs as $slug) {
            $slug = trim($slug);
            if (!$slug)
                continue;
            $this->db->query("
                INSERT IGNORE INTO role_permissions (role_id, permission_id)
                SELECT :role_id, id FROM permissions WHERE slug = :slug
            ", ['role_id' => $roleId, 'slug' => $slug]);
        }
    }

    /**
     * Eliminar un rol (solo si no es sistema y no tiene usuarios)
     */
    public function delete(int $id): array
    {
        $role = $this->getById($id);
        if (!$role)
            return ['ok' => false, 'msg' => 'Rol no encontrado.'];
        if ($role['is_system'])
            return ['ok' => false, 'msg' => 'No se puede eliminar el rol de sistema.'];

        $row = $this->db->fetch("SELECT COUNT(*) AS c FROM users WHERE role_id = :id", ['id' => $id]);
        if (($row['c'] ?? 0) > 0) {
            return ['ok' => false, 'msg' => 'El rol tiene usuarios asignados. Cambia su rol primero.'];
        }

        $this->db->query("DELETE FROM roles WHERE id = :id", ['id' => $id]);
        return ['ok' => true, 'msg' => 'Rol eliminado.'];
    }

    /**
     * Verificar si name ya existe
     */
    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $row = $this->db->fetch("SELECT id FROM roles WHERE name = :n AND id != :id LIMIT 1", ['n' => $name, 'id' => $excludeId]);
        } else {
            $row = $this->db->fetch("SELECT id FROM roles WHERE name = :n LIMIT 1", ['n' => $name]);
        }
        return (bool) $row;
    }
}
