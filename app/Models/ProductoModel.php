<?php
namespace App\Models;

use App\Core\Model;

class ProductoModel extends Model
{

    public function getAll()
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             ORDER BY p.created_at DESC"
        );
    }

    /**
     * Obtiene productos paginados, con búsqueda y ordenación dinámica
     */
    public function getPaginated($limit, $offset, $search = '', $sort = 'p.created_at', $order = 'DESC')
    {
        $params = [];
        $where = "1=1";

        if (!empty($search)) {
            $where .= " AND (p.name LIKE :s1 
                        OR p.description LIKE :s2 
                        OR c.name LIKE :s3 
                        OR p.status LIKE :s4)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
            $params['s3'] = "%$search%";
            $params['s4'] = "%$search%";
        }

        // Validar columnas de ordenación para evitar inyección SQL (whitelist)
        $allowedSort = [
            'p.id',
            'p.name',
            'p.price_unit',
            'p.price_dozen',
            'p.status',
            'p.is_digital',
            'p.created_at',
            'category_name'
        ];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'p.created_at';
        }

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $where
                ORDER BY $sort $order
                LIMIT $limit OFFSET $offset";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Cuenta el total de productos filtrados
     */
    public function countTotal($search = '')
    {
        $params = [];
        $where = "1=1";

        if (!empty($search)) {
            $where .= " AND (p.name LIKE :s1 
                        OR p.description LIKE :s2 
                        OR c.name LIKE :s3)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
            $params['s3'] = "%$search%";
        }

        $sql = "SELECT COUNT(*) as total 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $where";

        $row = $this->db->fetch($sql, $params);
        return (int) ($row['total'] ?? 0);
    }

    public function getActive($category_id = null, $type = null, $search = null)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active'";
        $params = [];

        if ($category_id) {
            $sql .= " AND p.category_id = :cat";
            $params['cat'] = $category_id;
        }
        if ($type) {
            $mappedType = ($type === 'fisico') ? 'physical' : (($type === 'digital') ? 'digital' : $type);
            $sql .= " AND c.type = :type";
            $params['type'] = $mappedType;
        }
        if ($search) {
            $sql .= " AND (p.name LIKE :search1 OR p.description LIKE :search2)";
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
        }

        $sql .= " ORDER BY p.id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function getFeatured($limit = 6)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active' 
                ORDER BY p.id DESC 
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    public function getById($id)
    {
        return $this->db->fetch(
            "SELECT * FROM products WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function create($data)
    {
        $sql = "INSERT INTO products
                    (category_id, name, slug, description, price_unit, price_dozen, price_combo,
                     image_url, is_digital, status, allow_client_note, allow_client_logo)
                VALUES
                    (:category_id, :name, :slug, :description, :price_unit, :price_dozen, :price_combo,
                     :image_url, :is_digital, :status, :allow_client_note, :allow_client_logo)";

        // Defaults seguros para columnas opcionales
        $data = array_merge([
            'price_combo' => null,
            'allow_client_note' => 0,
            'allow_client_logo' => 0,
            'image_url' => ''
        ], $data);

        $this->db->query($sql, $data);
        return $this->getLastId();
    }

    public function getLastId()
    {
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        $data = array_merge([
            'price_combo' => null,
            'allow_client_note' => 0,
            'allow_client_logo' => 0,
        ], $data);
        return $this->db->query(
            "UPDATE products SET
                name               = :name,
                category_id        = :category_id,
                price_unit         = :price_unit,
                price_dozen        = :price_dozen,
                price_combo        = :price_combo,
                is_digital         = :is_digital,
                description        = :description,
                image_url          = :image_url,
                status             = :status,
                allow_client_note  = :allow_client_note,
                allow_client_logo  = :allow_client_logo
             WHERE id = :id",
            $data
        );
    }

    public function updateImageUrl($id, $imageUrl)
    {
        return $this->db->query(
            "UPDATE products SET image_url = :url WHERE id = :id",
            ['url' => $imageUrl, 'id' => $id]
        );
    }

    public function updateDigitalPath($id, $path)
    {
        return $this->db->query(
            "UPDATE products SET digital_file_path = :path WHERE id = :id",
            ['path' => $path, 'id' => $id]
        );
    }

    public function delete($id)
    {
        return $this->db->query("DELETE FROM products WHERE id = :id", ['id' => $id]);
    }

    public function hasOrders($id)
    {
        $res = $this->db->fetch(
            "SELECT COUNT(*) as total FROM order_items WHERE product_id = :id",
            ['id' => $id]
        );
        return ((int) ($res['total'] ?? 0)) > 0;
    }
}
