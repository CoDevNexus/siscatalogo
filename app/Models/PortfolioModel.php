<?php
namespace App\Models;

use App\Core\Model;

class PortfolioModel extends Model
{
    public function getAll($limit = 6, $offset = 0)
    {
        $sql = "SELECT * FROM portfolio ORDER BY fecha_publicacion DESC, id DESC LIMIT :limit OFFSET :offset";
        return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
    }

    public function getCount()
    {
        $res = $this->db->fetch("SELECT COUNT(*) as total FROM portfolio");
        return (int) ($res['total'] ?? 0);
    }

    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM portfolio WHERE id = :id", ['id' => $id]);
    }

    public function getBySlug($slug)
    {
        $item = $this->db->fetch("SELECT * FROM portfolio WHERE slug = :slug", ['slug' => $slug]);
        if ($item) {
            $item['gallery'] = $this->getGallery($item['id']);
        }
        return $item;
    }

    public function getGallery($portfolio_id)
    {
        return $this->db->fetchAll("SELECT * FROM portfolio_gallery WHERE portfolio_id = :pid ORDER BY sort_order ASC, id ASC", ['pid' => $portfolio_id]);
    }

    public function create($data)
    {
        $data['slug'] = $this->generateUniqueSlug($data['titulo']);

        $sql = "INSERT INTO portfolio (titulo, slug, intro_corta, contenido_enriquecido, imagen_principal, categoria_tecnica, meta_description, tags, fecha_publicacion) 
                VALUES (:titulo, :slug, :intro_corta, :contenido_enriquecido, :imagen_principal, :categoria_tecnica, :meta_description, :tags, :fecha_pub)";

        return $this->db->query($sql, [
            'titulo' => $data['titulo'],
            'slug' => $data['slug'],
            'intro_corta' => $data['intro_corta'] ?? '',
            'contenido_enriquecido' => $data['contenido_enriquecido'] ?? '',
            'imagen_principal' => $data['imagen_principal'] ?? null,
            'categoria_tecnica' => $data['categoria_tecnica'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'tags' => $data['tags'] ?? null,
            'fecha_pub' => $data['fecha_publicacion'] ?? date('Y-m-d')
        ]);
    }

    public function update($id, $data)
    {
        // Si el título cambió, recalcular slug? Generalmente mejor no cambiar slugs por SEO, 
        // pero si el usuario lo pide es otra cosa. Para consistencia con otros modelos, lo mantendremos.
        // Aquí opcionalmente podrías actualizar el slug si el título cambia.

        $sql = "UPDATE portfolio SET 
                titulo = :titulo,
                intro_corta = :intro_corta,
                contenido_enriquecido = :contenido_enriquecido,
                imagen_principal = :imagen_principal,
                categoria_tecnica = :categoria_tecnica,
                meta_description = :meta_description,
                tags = :tags,
                fecha_publicacion = :fecha_pub
                WHERE id = :id";

        return $this->db->query($sql, [
            'id' => $id,
            'titulo' => $data['titulo'],
            'intro_corta' => $data['intro_corta'],
            'contenido_enriquecido' => $data['contenido_enriquecido'],
            'imagen_principal' => $data['imagen_principal'],
            'categoria_tecnica' => $data['categoria_tecnica'],
            'meta_description' => $data['meta_description'],
            'tags' => $data['tags'],
            'fecha_pub' => $data['fecha_publicacion']
        ]);
    }

    public function delete($id)
    {
        // La tabla tiene ON DELETE CASCADE, así que borrará la galería automáticamente
        return $this->db->query("DELETE FROM portfolio WHERE id = :id", ['id' => $id]);
    }

    public function setGallery($portfolio_id, $images)
    {
        // Limpiar galería actual (opcional, o podrías hacer un sync inteligente)
        $this->db->query("DELETE FROM portfolio_gallery WHERE portfolio_id = :pid", ['pid' => $portfolio_id]);

        foreach ($images as $index => $img) {
            if (empty($img['path'])) continue;
            $this->db->query(
                "INSERT INTO portfolio_gallery (portfolio_id, image_path, source, sort_order) 
                 VALUES (:pid, :path, :source, :order)",
                [
                    'pid' => $portfolio_id,
                    'path' => $img['path'],
                    'source' => $img['source'] ?? 'local',
                    'order' => $index
                ]
            );
        }
    }

    private function generateUniqueSlug($title)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $originalSlug = $slug;
        $count = 1;

        while ($this->getBySlug($slug)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
