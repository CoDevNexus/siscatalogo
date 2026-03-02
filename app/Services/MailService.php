<?php
namespace App\Services;

use App\Models\CompanyModel;

class MailService
{
    /**
     * Envía un email utilizando la configuración SMTP guardada en la base de datos.
     * 
     * @param string|array $to Destinatario o array de destinatarios
     * @param string $subject Asunto
     * @param string $message Cuerpo del mensaje (puedes enviar HTML)
     * @param array $bcc Direcciones en copia oculta
     * @return array ['status' => 'success'|'error', 'message' => '...']
     */
    public static function send($to, string $subject, string $message, array $bcc = []): array
    {
        require_once BASE_PATH . 'app/Models/CompanyModel.php';
        $companyModel = new CompanyModel();
        $conf = $companyModel->getProfile();

        if (empty($conf['smtp_host']) || empty($conf['smtp_user']) || empty($conf['smtp_pass'])) {
            return ['status' => 'error', 'message' => 'Configuración SMTP incompleta en el perfil de empresa.'];
        }

        $host = $conf['smtp_host'];
        $port = (int) ($conf['smtp_port'] ?: 587);
        $user = $conf['smtp_user'];
        $pass = $conf['smtp_pass'];
        $encryption = $conf['smtp_encryption'] ?: 'tls';
        $fromEmail = $conf['smtp_from_email'] ?: $user;
        $fromName = $conf['smtp_from_name'] ?: ($conf['name'] ?? 'Sistema de Pedidos');

        // Utilizando PHPMailer sin Composer para asegurar las credenciales SMTP de la base de datos
        require_once BASE_PATH . 'app/Libraries/PHPMailer/Exception.php';
        require_once BASE_PATH . 'app/Libraries/PHPMailer/PHPMailer.php';
        require_once BASE_PATH . 'app/Libraries/PHPMailer/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = $pass;

            // Asignación de Encryption
            if (strtolower($encryption) === 'ssl') {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port = $port;
            $mail->CharSet = 'UTF-8';

            // Opciones mitigantes para TLS en servidores compartidos
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Remitente y Destinatario
            $mail->setFrom($fromEmail, $fromName);
            if (is_array($to)) {
                foreach ($to as $address) {
                    $mail->addAddress($address);
                }
            } else {
                $mail->addAddress($to);
            }
            if (!empty($bcc)) {
                foreach ($bcc as $b) {
                    $mail->addBCC($b);
                }
            }
            $mail->addReplyTo($fromEmail, $fromName);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            return ['status' => 'success', 'message' => 'Correo enviado correctamente usando configuración SMTP.'];
        } catch (\Exception $e) {
            // El error detallado se capturará si las credenciales son incorrectas
            return ['status' => 'error', 'message' => 'Error SMTP al enviar: ' . $mail->ErrorInfo];
        }
    }

    /**
     * Genera el cuerpo HTML para una proforma de pedido.
     */
    public static function getOrderHtml(array $order, array $company, string $customHeader = ''): string
    {
        $itemsHtml = '';
        foreach ($order['items'] as $item) {
            $itemsHtml .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$item['product_name']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$item['quantity']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>$" . number_format($item['price_applied'], 2) . "</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;'>$" . number_format($item['quantity'] * $item['price_applied'], 2) . "</td>
                </tr>";
        }

        $total = (float) $order['total_amount'];
        $subtotal = $total - (float) $order['shipping_amount'] - (float) $order['tax_amount'];

        $html = "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
            <div style='background: #f8f9fa; padding: 20px; border-bottom: 2px solid #eee;'>
                <h2 style='margin: 0; color: #0d6efd;'>{$company['name']}</h2>
                <p style='margin: 5px 0 0; color: #666; font-size: 0.9em;'>{$company['eslogan']}</p>
            </div>
            <div style='padding: 20px;'>
                <h3 style='border-bottom: 1px solid #eee; padding-bottom: 10px;'>Detalle de Proforma #{$order['id']}</h3>
                " . ($customHeader ?: "<p>Hola <strong>{$order['customer_name']}</strong>,</p><p>Adjuntamos el detalle de su pedido solicitado el " . date('d/m/Y H:i', strtotime($order['created_at'])) . ".</p>") . "
                
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <thead style='background: #f1f1f1;'>
                        <tr>
                            <th style='padding: 10px; text-align: left;'>Producto</th>
                            <th style='padding: 10px; text-align: center;'>Cant</th>
                            <th style='padding: 10px; text-align: right;'>Precio</th>
                            <th style='padding: 10px; text-align: right;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                </table>

                <div style='text-align: right;'>
                    <p style='margin: 5px 0;'>Subtotal: <strong>$" . number_format($subtotal, 2) . "</strong></p>
                    <p style='margin: 5px 0; color: #0d6efd;'>Envío: <strong>$" . number_format($order['shipping_amount'], 2) . "</strong></p>
                    <p style='margin: 5px 0; color: #198754;'>IVA: <strong>$" . number_format($order['tax_amount'], 2) . "</strong></p>
                    <h3 style='margin: 10px 0; color: #333;'>TOTAL: <span style='color: #dc3545;'>$" . number_format($total, 2) . "</span></h3>
                </div>

                <div style='margin-top: 30px; padding: 15px; background: #fff8e1; border-radius: 5px; font-size: 0.85em; color: #856404;'>
                    <strong>Términos:</strong><br>{$company['terms_conditions']}
                </div>
            </div>
            <div style='background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 0.85em; border-top: 1px solid #eee;'>
                <p style='margin: 0 0 10px 0;'><strong>{$company['name']}</strong></p>
                <p style='margin: 0 0 5px 0;'>RUC: {$company['ruc_nit']} | {$company['address']}, {$company['ciudad']}</p>
                <p style='margin: 0 0 10px 0;'>
                    " . (!empty($company['phone_whatsapp']) ? "WhatsApp: {$company['phone_whatsapp']} | " : "") . "
                    " . (!empty($company['email']) ? "Email: {$company['email']}" : "") . "
                </p>
                <div style='margin-top: 10px;'>
                    " . (!empty($company['facebook_url']) ? "<a href='{$company['facebook_url']}' style='text-decoration:none; margin:0 5px;'><img src='https://cdn-icons-png.flaticon.com/512/124/124010.png' width='20' height='20'></a>" : "") . "
                    " . (!empty($company['instagram']) ? "<a href='https://instagram.com/{$company['instagram']}' style='text-decoration:none; margin:0 5px;'><img src='https://cdn-icons-png.flaticon.com/512/2111/2111463.png' width='20' height='20'></a>" : "") . "
                    " . (!empty($company['tiktok']) ? "<a href='https://tiktok.com/@{$company['tiktok']}' style='text-decoration:none; margin:0 5px;'><img src='https://cdn-icons-png.flaticon.com/512/3046/3046121.png' width='20' height='20'></a>" : "") . "
                </div>
                <p style='margin-top: 15px; font-size: 0.9em; color: #888;'>{$company['thank_you_message']}</p>
            </div>
        </div>";

        return $html;
    }

    /**
     * Genera el cuerpo HTML para el envío de credenciales de acceso digital.
     */
    public static function getDigitalDeliveryHtml(array $order, array $company, string $user, string $pass): string
    {
        $loginUrl = APP_URL . 'digital/login';

        $logoHtml = !empty($company['logo_url'])
            ? "<img src='{$company['logo_url']}' alt='{$company['name']}' style='max-height: 80px; max-width: 250px; object-fit: contain;'>"
            : "<h2 style='margin: 0; color: #0d6efd;'>{$company['name']}</h2>";

        $esloganHtml = !empty($company['eslogan']) ? "<p style='margin: 5px 0 0; color: #666; font-size: 0.9em;'>{$company['eslogan']}</p>" : "";

        $html = "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
            <div style='background: #f8f9fa; padding: 20px; text-align: center; border-bottom: 2px solid #eee;'>
                {$logoHtml}
                {$esloganHtml}
            </div>
            <div style='background: #0d6efd; padding: 15px; text-align: center;'>
                <h2 style='margin: 0; color: #fff; font-size: 1.25rem;'>¡Tus archivos digitales están listos!</h2>
            </div>
            <div style='padding: 30px 20px;'>
                <p style='font-size: 16px;'>Hola <strong>{$order['customer_name']}</strong>,</p>
                <p style='font-size: 15px; line-height: 1.5;'>Tu pedido <strong>#{$order['id']}</strong> de productos digitales ha sido aprobado. Hemos creado un acceso seguro para que descargues tus archivos.</p>
                
                <div style='background-color: #f8f9fa; border-left: 4px solid #0d6efd; padding: 15px; margin: 25px 0;'>
                    <p style='margin: 0 0 10px 0; font-weight: bold; color: #555;'>Tus credenciales de acceso:</p>
                    <p style='margin: 5px 0; font-family: monospace; font-size: 16px;'><strong>Usuario:</strong> {$user}</p>
                    <p style='margin: 5px 0; font-family: monospace; font-size: 16px;'><strong>Contraseña:</strong> {$pass}</p>
                </div>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$loginUrl}' style='background-color: #0d6efd; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Ir al Portal de Descargas</a>
                </div>

                <div style='background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; font-size: 14px;'>
                    <strong>Importante:</strong>
                    <ul style='margin-top: 5px; margin-bottom: 0; padding-left: 20px;'>
                        <li>Tienes <strong>72 horas</strong> para descargar tus archivos antes de que el token expire.</li>
                        <li>Cuentas con un máximo de <strong>3 intentos de descarga</strong> por archivo por seguridad.</li>
                        <li>No compartas estas credenciales, son de uso personal.</li>
                    </ul>
                </div>
            </div>
            <div style='background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 0.85em; border-top: 1px solid #eee;'>
                <p style='margin: 0 0 10px 0;'><strong>{$company['name']}</strong></p>
                <p style='margin: 0 0 5px 0;'>RUC: {$company['ruc_nit']} | {$company['address']}, {$company['ciudad']}</p>
                <p style='margin: 0 0 10px 0;'>
                    " . (!empty($company['phone_whatsapp']) ? "WhatsApp: {$company['phone_whatsapp']} | " : "") . "
                    " . (!empty($company['email']) ? "Email: {$company['email']}" : "") . "
                </p>
                <div style='margin-top: 10px;'>
                    " . (!empty($company['facebook_url']) ? "<a href='{$company['facebook_url']}' style='text-decoration:none; margin:0 5px;'><img src='https://cdn-icons-png.flaticon.com/512/124/124010.png' width='20' height='20'></a>" : "") . "
                    " . (!empty($company['instagram']) ? "<a href='https://instagram.com/{$company['instagram']}' style='text-decoration:none; margin:0 5px;'><img src='https://cdn-icons-png.flaticon.com/512/2111/2111463.png' width='20' height='20'></a>" : "") . "
                    " . (!empty($company['tiktok']) ? "<a href='https://tiktok.com/@{$company['tiktok']}' style='text-decoration:none; margin:0 5px;'><img src='https://cdn-icons-png.flaticon.com/512/3046/3046121.png' width='20' height='20'></a>" : "") . "
                </div>
                <p style='margin-top: 15px; font-size: 0.9em; color: #888;'>{$company['thank_you_message']}</p>
            </div>
        </div>";

        return $html;
    }

    /**
     * Envía el email de recuperación de contraseña al administrador.
     *
     * @param string $to       Email del administrador
     * @param string $username Nombre de usuario
     * @param string $resetUrl URL completa del enlace de reseteo (con token)
     * @return array ['status' => 'success'|'error', 'message' => '...']
     */
    public static function sendPasswordReset(string $to, string $username, string $resetUrl): array
    {
        $subject = 'Recuperación de contraseña — ' . (defined('APP_NAME') ? APP_NAME : 'Panel Admin');
        $appName = defined('APP_NAME') ? APP_NAME : 'Panel Admin';

        $html = "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 580px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>
            <!-- Cabecera -->
            <div style='background: #111827; padding: 24px; text-align: center;'>
                <h2 style='margin: 0; color: #ffffff; font-size: 1.3rem; letter-spacing: 0.5px;'>🔐 {$appName}</h2>
            </div>
            <!-- Cuerpo -->
            <div style='padding: 32px 28px;'>
                <h3 style='margin: 0 0 12px; color: #111827; font-size: 1.1rem;'>Recuperación de contraseña</h3>
                <p style='margin: 0 0 20px; font-size: 15px; line-height: 1.6; color: #444;'>
                    Hola <strong>{$username}</strong>,<br>
                    Recibimos una solicitud para restablecer la contraseña de tu cuenta de administrador.
                    Si no realizaste esta solicitud, puedes ignorar este correo de forma segura.
                </p>
                <!-- Botón CTA -->
                <div style='text-align: center; margin: 28px 0;'>
                    <a href='{$resetUrl}'
                       style='display: inline-block; background: #111827; color: #ffffff; padding: 14px 32px;
                              text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px;
                              letter-spacing: 0.3px;'>
                        Restablecer contraseña
                    </a>
                </div>
                <!-- Advertencia expiry -->
                <div style='background: #fff8e1; border-left: 4px solid #f59e0b; padding: 14px 16px; border-radius: 4px; font-size: 13.5px; color: #92400e;'>
                    <strong>⏱ Este enlace expira en 1 hora.</strong><br>
                    Si el botón no funciona, copia y pega esta URL en tu navegador:<br>
                    <span style='word-break: break-all; color: #555;'>{$resetUrl}</span>
                </div>
            </div>
            <!-- Pie -->
            <div style='background: #f9fafb; padding: 16px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb;'>
                Este correo fue enviado automáticamente. Por favor no respondas a este mensaje.
            </div>
        </div>";

        return self::send($to, $subject, $html);
    }
}
