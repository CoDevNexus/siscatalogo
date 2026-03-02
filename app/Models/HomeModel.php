<?php
namespace App\Models;

use App\Core\Model;

class HomeModel extends Model
{
    /**
     * Obtener todos los slides activos para el Home
     */
    public function getActiveSlides()
    {
        return $this->db->fetchAll(
            "SELECT * FROM home_slider WHERE status = 'active' ORDER BY sort_order ASC"
        );
    }

    /**
     * Obtener todos los slides (para el admin)
     */
    public function getAllSlides()
    {
        return $this->db->fetchAll(
            "SELECT * FROM home_slider ORDER BY sort_order ASC"
        );
    }

    /**
     * Guardar o actualizar un slide
     */
    public function saveSlide($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            $sql = "UPDATE home_slider SET 
                    title = :title, 
                    subtitle = :subtitle, 
                    badge = :badge, 
                    image_url = :image_url, 
                    button_text = :button_text, 
                    button_link = :button_link, 
                    sort_order = :sort_order, 
                    status = :status 
                    WHERE id = :id";
            return $this->db->query($sql, $data);
        } else {
            $sql = "INSERT INTO home_slider (title, subtitle, badge, image_url, button_text, button_link, sort_order, status) 
                    VALUES (:title, :subtitle, :badge, :image_url, :button_text, :button_link, :sort_order, :status)";
            return $this->db->query($sql, $data);
        }
    }

    /**
     * Eliminar un slide
     */
    public function deleteSlide($id)
    {
        return $this->db->query("DELETE FROM home_slider WHERE id = :id", ['id' => $id]);
    }

    /**
     * Obtener todos los ajustes del Home
     */
    public function getSettings()
    {
        $rows = $this->db->fetchAll("SELECT `key`, `value` FROM home_settings");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }

    /**
     * Obtener ajustes detallados (para el admin)
     */
    public function getDetailedSettings()
    {
        return $this->db->fetchAll("SELECT * FROM home_settings ORDER BY group_name ASC");
    }

    /**
     * Actualizar un ajuste
     */
    public function updateSetting($key, $value)
    {
        return $this->db->query(
            "UPDATE home_settings SET `value` = :value WHERE `key` = :key",
            ['value' => $value, 'key' => $key]
        );
    }
}
