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

    public function getActive($categoryId = null, $type = null)
    {
        $where = ["p.status = 'active'"];
        $params = [];
        if ($categoryId) {
            $where[] = "p.category_id = :cat_id";
            $params['cat_id'] = $categoryId;
        }
        if ($type === 'digital') {
            $where[] = "p.is_digital = 1";
        } elseif ($type === 'fisico') {
            $where[] = "p.is_digital = 0";
        }
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.created_at DESC";
        return $this->db->fetchAll($sql, $params);
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

        return $this->db->query($sql, $data);
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
}
