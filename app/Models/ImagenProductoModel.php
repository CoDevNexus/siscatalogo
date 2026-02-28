<?php
namespace App\Models;

use App\Core\Model;

class ImagenProductoModel extends Model
{

    public function getByProduct($productId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = :pid ORDER BY sort_order ASC, is_primary DESC",
            ['pid' => $productId]
        );
    }

    public function getPrimary($productId)
    {
        $img = $this->db->fetch(
            "SELECT * FROM product_images WHERE product_id = :pid AND is_primary = 1 LIMIT 1",
            ['pid' => $productId]
        );
        if (!$img) {
            $img = $this->db->fetch(
                "SELECT * FROM product_images WHERE product_id = :pid ORDER BY sort_order ASC LIMIT 1",
                ['pid' => $productId]
            );
        }
        return $img;
    }

    public function addImage($productId, $imagePath, $source = 'local', $isPrimary = false, $sortOrder = 0)
    {
        // Si es primaria, desmarcar las previas
        if ($isPrimary) {
            $this->db->query(
                "UPDATE product_images SET is_primary = 0 WHERE product_id = :pid",
                ['pid' => $productId]
            );
        }
        return $this->db->query(
            "INSERT INTO product_images (product_id, source, image_path, is_primary, sort_order)
             VALUES (:pid, :source, :path, :primary, :order)",
            [
                'pid' => $productId,
                'source' => $source,
                'path' => $imagePath,
                'primary' => $isPrimary ? 1 : 0,
                'order' => $sortOrder
            ]
        );
    }

    public function setPrimary($imageId, $productId)
    {
        $this->db->query(
            "UPDATE product_images SET is_primary = 0 WHERE product_id = :pid",
            ['pid' => $productId]
        );
        return $this->db->query(
            "UPDATE product_images SET is_primary = 1 WHERE id = :id",
            ['id' => $imageId]
        );
    }

    public function updateOrder($imageId, $order)
    {
        return $this->db->query(
            "UPDATE product_images SET sort_order = :ord WHERE id = :id",
            ['ord' => $order, 'id' => $imageId]
        );
    }

    public function delete($imageId)
    {
        // Obtener path antes de borrar para eliminar el archivo físico si es local
        $img = $this->db->fetch("SELECT * FROM product_images WHERE id = :id", ['id' => $imageId]);
        if ($img && $img['source'] === 'local') {
            $localPath = BASE_PATH . 'storage/productos/' . basename($img['image_path']);
            if (file_exists($localPath)) {
                @unlink($localPath);
            }
        }
        return $this->db->query("DELETE FROM product_images WHERE id = :id", ['id' => $imageId]);
    }

    public function deleteByProduct($productId)
    {
        $imgs = $this->getByProduct($productId);
        foreach ($imgs as $img) {
            $this->delete($img['id']);
        }
    }
}
