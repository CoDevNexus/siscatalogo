<?php
namespace App\Helpers;

use App\Models\CompanyModel;

class TelegramHelper
{
    /**
     * Envía un mensaje con formato HTML a Telegram sobre una nueva cotización
     *
     * @param array $order Datos de la cotización principal
     * @param array $company Datos de la empresa (para extraer credenciales si no vienen definidas)
     * @return array ['success' => bool, 'error' => string]
     */
    public static function sendOrderNotification(array $order): array
    {
        $companyModel = new CompanyModel();
        $company = $companyModel->getProfile();

        $token = $company['telegram_token'] ?? null;
        $chatId = $company['telegram_chat_id'] ?? null;
        $isActive = $company['telegram_active'] ?? 0;

        if (!$isActive || empty($token) || empty($chatId)) {
            return ['success' => false, 'error' => 'Telegram no configurado o inactivo'];
        }

        $appUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
        $companyName = !empty($company['name']) ? htmlspecialchars($company['name']) : 'Nuestra Empresa';

        // Preparamos los datos
        $customerName = htmlspecialchars($order['customer_name'] ?? 'Cliente Desconocido');
        $customerEmail = htmlspecialchars($order['customer_email'] ?? 'Sin correo');
        $customerPhone = htmlspecialchars($order['customer_phone'] ?? '');
        $customerId = htmlspecialchars($order['customer_id'] ?? '');
        $city = htmlspecialchars($order['city'] ?? 'No especificada');
        $total = number_format((float) ($order['total_amount'] ?? 0), 2);

        $notaHtml = "";
        if (!empty($order['custom_note'])) {
            $notaHtml = "\n📝 <b>Nota del cliente:</b> " . htmlspecialchars($order['custom_note']);
        }

        $orderId = $order['id'] ?? 0;
        $adminViewUrl = "{$appUrl}/admin/cotizaciones/detalle/{$orderId}"; // Ajustado al formato del controlador existente

        // Armamos el mensaje HTML
        $message = "🚀 <b>Nueva Cotización Recibida - {$companyName}</b>\n\n";
        $message .= "👤 <b>Cliente:</b> {$customerName}\n";

        if (!empty($customerId)) {
            $message .= "🪪 <b>CI/RUC:</b> {$customerId}\n";
        }
        if (!empty($customerPhone)) {
            $message .= "📱 <b>Teléfono:</b> {$customerPhone}\n";
        }

        $message .= "📧 <b>Email:</b> {$customerEmail}\n";
        $message .= "📍 <b>Ciudad:</b> {$city}\n";

        $message .= "\n📦 <b>Productos Solicitados:</b>\n";
        if (!empty($order['items']) && is_array($order['items'])) {
            foreach ($order['items'] as $item) {
                // Soportar diferentes formatos de claves dependiendo del origen (DB vs $_POST)
                $qty = $item['quantity'] ?? $item['cantidad'] ?? $item['qty'] ?? 1;
                $name = htmlspecialchars($item['product_name'] ?? $item['name'] ?? 'Producto');
                $price = number_format((float) ($item['unit_price'] ?? $item['price'] ?? 0), 2);

                $message .= "- {$qty}x {$name}\n";
            }
        } else {
            $message .= "<i>(Múltiples items - Ver en sistema)</i>\n";
        }

        $message .= "\n💰 <b>Total:</b> $ {$total}\n";
        $message .= "{$notaHtml}\n\n";
        $message .= "🔗 <a href='{$adminViewUrl}'>Ver Detalle en el Admin</a>";

        return self::sendMessage($token, $chatId, $message);
    }

    /**
     * Envía un mensaje de prueba con credenciales explícitas
     * @return array ['success' => bool, 'error' => string]
     */
    public static function sendTestMessage(string $token, string $chatId): array
    {
        $message = "✅ <b>¡Conexión Exitosa!</b>\n\nTu bot de Telegram está configurado correctamente en el sistema y listo para recibir notificaciones de nuevas cotizaciones.";
        return self::sendMessage($token, $chatId, $message);
    }

    /**
     * Ejecuta la petición cURL a la API de Telegram
     * 
     * @param string $token
     * @param string $chatId
     * @param string $message
     * @return array ['success' => bool, 'error' => string]
     */
    private static function sendMessage(string $token, string $chatId, string $message): array
    {
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Timeout corto para no retrasar la carga del cliente
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        // Es importante deshabilitar la verificación SSL en entornos de desarrollo locales problemáticos
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            return ['success' => false, 'error' => 'Error de conexión cURL: ' . $curlError];
        }

        $responseData = json_decode($response, true);

        if ($httpCode === 200 && isset($responseData['ok']) && $responseData['ok'] === true) {
            return ['success' => true, 'error' => ''];
        }

        $tgError = $responseData['description'] ?? 'Error desconocido de Telegram HTTP ' . $httpCode;
        return ['success' => false, 'error' => $tgError];
    }
}
