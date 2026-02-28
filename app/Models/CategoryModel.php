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
}
