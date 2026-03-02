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
                    maps_embed        = :maps_embed,
                    shipping_cost     = :shipping_cost,
                    tax_rate          = :tax_rate,
                    smtp_host         = :smtp_host,
                    smtp_port         = :smtp_port,
                    smtp_user         = :smtp_user,
                    smtp_pass         = :smtp_pass,
                    smtp_encryption   = :smtp_encryption,
                    smtp_from_email   = :smtp_from_email,
                    smtp_from_name    = :smtp_from_name,
                    telegram_token    = :telegram_token,
                    telegram_chat_id  = :telegram_chat_id,
                    telegram_active   = :telegram_active,
                    theme_primary     = :theme_primary,
                    theme_accent      = :theme_accent,
                    theme_navbar      = :theme_navbar,
                    theme_footer      = :theme_footer
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
            'shipping_cost' => (float) ($data['shipping_cost'] ?? 0),
            'tax_rate' => (float) ($data['tax_rate'] ?? 0),
            'smtp_host' => $data['smtp_host'] ?? '',
            'smtp_port' => (int) ($data['smtp_port'] ?? 587),
            'smtp_user' => $data['smtp_user'] ?? '',
            'smtp_pass' => $data['smtp_pass'] ?? '',
            'smtp_encryption' => $data['smtp_encryption'] ?? 'tls',
            'smtp_from_email' => $data['smtp_from_email'] ?? '',
            'smtp_from_name' => $data['smtp_from_name'] ?? '',
            'telegram_token' => $data['telegram_token'] ?? '',
            'telegram_chat_id' => $data['telegram_chat_id'] ?? '',
            'telegram_active' => isset($data['telegram_active']) ? 1 : 0,
            'theme_primary' => $data['theme_primary'] ?? '#2b2d42',
            'theme_accent' => $data['theme_accent'] ?? '#ef233c',
            'theme_navbar' => $data['theme_navbar'] ?? '',
            'theme_footer' => $data['theme_footer'] ?? '',
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

    public function updateFooterImage(string $imageUrl): bool
    {
        $sql = "UPDATE company_profile SET footer_image_url = :footer_image_url WHERE id = 1";
        return (bool) $this->db->query($sql, ['footer_image_url' => $imageUrl]);
    }

    public function getFooterImagePath(): ?string
    {
        $row = $this->db->fetch("SELECT footer_image_url FROM company_profile WHERE id = 1");
        return $row['footer_image_url'] ?? null;
    }
}
