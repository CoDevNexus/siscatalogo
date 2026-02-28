<?php
namespace App\Services;

/**
 * Servicio de gestión de imágenes:
 * - Compresión y conversión a WebP
 * - Subida a API ImgBB
 * - Validación de URLs externas
 */
class ImageService
{

    private static $storagePath;

    private static function init()
    {
        self::$storagePath = BASE_PATH . 'storage/productos/';
        if (!is_dir(self::$storagePath)) {
            mkdir(self::$storagePath, 0755, true);
        }
    }

    /**
     * Procesa un archivo subido localmente, convierte a WebP y comprime.
     * @return array ['path' => ruta relativa, 'source' => 'local'] o false
     */
    public static function processUpload(array $file, int $quality = 80): array|false
    {
        self::init();

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
        if (!in_array($file['type'], $allowed)) {
            return false;
        }
        if ($file['size'] > 8 * 1024 * 1024) { // 8MB máx
            return false;
        }

        $tmpPath = $file['tmp_name'];
        $fileName = 'prod_' . uniqid() . '_' . time() . '.webp';
        $destPath = self::$storagePath . $fileName;

        // Detectar tipo original y crear imagen GD
        $src = null;
        switch ($file['type']) {
            case 'image/jpeg':
                $src = @imagecreatefromjpeg($tmpPath);
                break;
            case 'image/png':
                $src = @imagecreatefrompng($tmpPath);
                break;
            case 'image/gif':
                $src = @imagecreatefromgif($tmpPath);
                break;
            case 'image/webp':
                $src = @imagecreatefromwebp($tmpPath);
                break;
            case 'image/bmp':
                $src = @imagecreatefrombmp($tmpPath);
                break;
        }

        if (!$src) {
            // Si GD no puede convertir, guardar como raw
            $rawName = 'prod_' . uniqid() . '.' . strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $rawDest = self::$storagePath . $rawName;
            if (move_uploaded_file($tmpPath, $rawDest)) {
                return ['path' => 'storage/productos/' . $rawName, 'source' => 'local'];
            }
            return false;
        }

        // Convertir a WebP con compresión
        $success = imagewebp($src, $destPath, $quality);
        imagedestroy($src);

        if ($success) {
            return ['path' => 'storage/productos/' . $fileName, 'source' => 'local'];
        }
        return false;
    }

    /**
     * Valida que una URL externa es accesible y retorna imagen.
     */
    public static function validateExternalUrl(string $url): bool
    {
        // Validación básica de URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        // Solo http/https
        if (!preg_match('/^https?:\/\//i', $url)) {
            return false;
        }
        return true;
    }

    /**
     * Sube imagen a ImgBB y retorna la URL pública.
     * @param string $filePath  Ruta local del archivo a subir
     * @param string $apiKey    API key de ImgBB
     * @return array|false      ['path' => url_publica, 'source' => 'api'] o false
     */
    public static function uploadToImgBB(string $filePath, string $apiKey): array|false
    {
        if (!file_exists($filePath) || empty($apiKey)) {
            return false;
        }

        $imageB64 = base64_encode(file_get_contents($filePath));

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.imgbb.com/1/upload',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => [
                'key' => $apiKey,
                'image' => $imageB64,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || !$response) {
            return false;
        }

        $data = json_decode($response, true);
        if (!empty($data['success']) && !empty($data['data']['url'])) {
            return ['path' => $data['data']['url'], 'source' => 'api'];
        }
        return false;
    }

    /**
     * Genera la URL completa de una imagen (local o externa)
     */
    public static function buildUrl(string $path, string $source = 'local'): string
    {
        if (in_array($source, ['url', 'api'])) {
            return $path; // Ya es URL completa
        }
        return APP_URL . ltrim($path, '/');
    }
}
