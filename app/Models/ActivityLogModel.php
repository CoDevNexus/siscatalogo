<?php
namespace App\Models;

use App\Core\Model;

class ActivityLogModel extends Model
{
    /**
     * Registrar una acción en la bitácora
     */
    public function log(
        ?int $userId,
        string $username,
        string $action,
        string $module = '',
        string $detail = '',
        string $ip = ''
    ): void {
        $this->db->query(
            "INSERT INTO activity_log (user_id, username, action, module, detail, ip_address)
             VALUES (:user_id, :username, :action, :module, :detail, :ip)",
            [
                'user_id' => $userId,
                'username' => $username,
                'action' => $action,
                'module' => $module,
                'detail' => $detail,
                'ip' => $ip
            ]
        );
    }

    /**
     * Obtener registros de la bitácora con filtros y paginación
     */
    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        [$where, $params] = $this->buildWhere($filters);
        $sql = "SELECT * FROM activity_log $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        return $this->db->fetchAll($sql, $params) ?: [];
    }

    /**
     * Contar registros para paginación
     */
    public function countAll(array $filters = []): int
    {
        [$where, $params] = $this->buildWhere($filters);
        $row = $this->db->fetch("SELECT COUNT(*) AS c FROM activity_log $where", $params);
        return (int) ($row['c'] ?? 0);
    }

    /**
     * Eliminar registros con más de N días de antigüedad
     */
    public function clearOld(int $days = 90): int
    {
        $stmt = $this->db->query(
            "DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)",
            ['days' => $days]
        );
        return $stmt->rowCount();
    }

    /**
     * Obtener lista de módulos distintos (para filtro)
     */
    public function getModules(): array
    {
        $rows = $this->db->fetchAll("SELECT DISTINCT module FROM activity_log WHERE module != '' ORDER BY module");
        return array_column($rows ?: [], 'module');
    }

    /**
     * Construir cláusula WHERE a partir de filtros
     */
    private function buildWhere(array $filters): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['module'])) {
            $conditions[] = 'module = :module';
            $params['module'] = $filters['module'];
        }
        if (!empty($filters['username'])) {
            $conditions[] = 'username LIKE :username';
            $params['username'] = '%' . $filters['username'] . '%';
        }
        if (!empty($filters['date_from'])) {
            $conditions[] = 'DATE(created_at) >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $conditions[] = 'DATE(created_at) <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['action'])) {
            $conditions[] = 'action LIKE :action';
            $params['action'] = '%' . $filters['action'] . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }
}
