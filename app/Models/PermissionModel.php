<?php
namespace App\Models;

use App\Core\Model;

class PermissionModel extends Model
{
    /**
     * Obtener todos los permisos agrupados por módulo
     * @return array<string, array> ['module' => [permission, ...], ...]
     */
    public function getAllGrouped(): array
    {
        $rows = $this->db->fetchAll("SELECT * FROM permissions ORDER BY module, slug") ?: [];
        $grouped = [];
        foreach ($rows as $perm) {
            $grouped[$perm['module']][] = $perm;
        }
        return $grouped;
    }

    /**
     * Obtener todos como lista plana
     */
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM permissions ORDER BY module, slug") ?: [];
    }
}
