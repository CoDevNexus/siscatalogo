<?php
namespace App\Models;

use App\Core\Model;

class CategoryModel extends Model
{
    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
    }
    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM categories WHERE id = :id LIMIT 1", ['id' => $id]);
    }
    public function create($data)
    {
        return $this->db->query(
            "INSERT INTO categories (name, slug, type) VALUES (:name, :slug, :type)",
            $data
        );
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        return $this->db->query(
            "UPDATE categories SET name = :name, slug = :slug, type = :type WHERE id = :id",
            $data
        );
    }

    public function delete($id)
    {
        return $this->db->query("DELETE FROM categories WHERE id = :id", ['id' => $id]);
    }

    public function isInUse($id)
    {
        $res = $this->db->fetch(
            "SELECT COUNT(*) as total FROM products WHERE category_id = :id",
            ['id' => $id]
        );
        return ((int) ($res['total'] ?? 0)) > 0;
    }
}
