<?php
namespace App\Models;

use App\Core\Model;

class CompanyModel extends Model
{
    public function getProfile(): array
    {
        $sql = "SELECT * FROM company_profile WHERE id = 1";
        $result = $this->db->fetch($sql);

        if (!$result) {
            $this->db->query(
                "INSERT INTO company_profile (id, name, thank_you_message, terms_conditions)
                 VALUES (1, 'Mi Empresa Láser',
                         '¡Gracias por su preferencia!',
                         'Cotización válida por 48 horas. Los precios pueden variar sin previo aviso.')"
            );
            return $this->db->fetch($sql) ?: [];
        }
        return $result;
    }

    public function updateProfile(array $data): bool
    {
        // NOTA: PDO no permite el mismo nombre de parámetro dos veces en una query.
        // Las columnas 'whatsapp' y 'facebook' son alias heredados; se sincronizan
        // usando parámetros únicos :wa_sync y :fb_sync.
        $sql = "UPDATE company_profile SET
                    name              = :name,
                    eslogan           = :eslogan,
                    ruc_nit           = :ruc_nit,
                    ciudad            = :ciudad,
                    address           = :address,
                    phone_whatsapp    = :phone_whatsapp,
                    whatsapp          = :wa_sync,
                    email             = :email,
                    facebook_url      = :facebook_url,
                    facebook          = :fb_sync,
                    instagram         = :instagram,
                    tiktok            = :tiktok,
                    pinterest_url     = :pinterest_url,
                    description       = :description,
                    terms_conditions  = :terms_conditions,
                    thank_you_message = :thank_you_message,
                    maps_embed        = :maps_embed
                WHERE id = 1";

        $wa = $data['phone_whatsapp'] ?? '';
        $fb = $data['facebook_url'] ?? '';

        return (bool) $this->db->query($sql, [
            'name' => $data['name'] ?? '',
            'eslogan' => $data['eslogan'] ?? '',
            'ruc_nit' => $data['ruc_nit'] ?? '',
            'ciudad' => $data['ciudad'] ?? '',
            'address' => $data['address'] ?? '',
            'phone_whatsapp' => $wa,
            'wa_sync' => $wa,          // alias → columna whatsapp
            'email' => $data['email'] ?? '',
            'facebook_url' => $fb,
            'fb_sync' => $fb,          // alias → columna facebook
            'instagram' => $data['instagram'] ?? '',
            'tiktok' => $data['tiktok'] ?? '',
            'pinterest_url' => $data['pinterest_url'] ?? '',
            'description' => $data['description'] ?? '',
            'terms_conditions' => $data['terms_conditions'] ?? '',
            'thank_you_message' => $data['thank_you_message'] ?? '',
            'maps_embed' => $data['maps_embed'] ?? '',
        ]);
    }

    public function updateLogo(string $logoUrl): bool
    {
        $sql = "UPDATE company_profile SET logo_url = :logo_url WHERE id = 1";
        return (bool) $this->db->query($sql, ['logo_url' => $logoUrl]);
    }

    public function getLogoPath(): ?string
    {
        $row = $this->db->fetch("SELECT logo_url FROM company_profile WHERE id = 1");
        return $row['logo_url'] ?? null;
    }
}
