<?php
declare(strict_types=1);

require_once __DIR__ . '/mail_config.php';
require_once __DIR__ . '/vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ── Solo POST ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.html');
    exit;
}

// ── Verificar referrer ───────────────────────────────────────────
$host    = $_SERVER['HTTP_HOST'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if ($referer && parse_url($referer, PHP_URL_HOST) !== $host) {
    http_response_code(403);
    exit;
}

// ── Honeypot ─────────────────────────────────────────────────────
if (!empty($_POST['website'])) {
    header('Location: gracias.html');
    exit;
}

// ── Timestamp anti-bot ───────────────────────────────────────────
$form_ts = (int)($_POST['form_ts'] ?? 0);
if ($form_ts > 0) {
    $elapsed = (int)(microtime(true) * 1000) - $form_ts;
    if ($elapsed < 3000) {
        header('Location: contacto.html?error=rapido');
        exit;
    }
}

// ── Rate limiting por IP ─────────────────────────────────────────
$ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$cache_f = sys_get_temp_dir() . '/pr_rl_' . md5($ip) . '.json';
$now     = time();
$rl      = ['first' => $now, 'count' => 0];

if (file_exists($cache_f)) {
    $rl = json_decode(file_get_contents($cache_f), true) ?? $rl;
    if (($now - $rl['first']) > 3600) {
        $rl = ['first' => $now, 'count' => 0];
    }
}
$rl['count']++;
file_put_contents($cache_f, json_encode($rl), LOCK_EX);

if ($rl['count'] > 3) {
    header('Location: contacto.html?error=limite');
    exit;
}

// ── Sanitizar ────────────────────────────────────────────────────
function limpiar(string $val): string {
    return trim(str_replace(["\r", "\n"], ' ',
        htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
    ));
}

$nombre   = limpiar($_POST['nombre']   ?? '');
$empresa  = limpiar($_POST['empresa']  ?? '');
$telefono = limpiar($_POST['telefono'] ?? '');
$correo   = limpiar($_POST['correo']   ?? '');
$servicio = limpiar($_POST['servicio'] ?? '');
$mensaje  = limpiar($_POST['mensaje']  ?? '');

// ── Validaciones ─────────────────────────────────────────────────
if (!$nombre || !$telefono || !$mensaje) {
    header('Location: contacto.html?error=campos');
    exit;
}
if (mb_strlen($nombre) > 120 || mb_strlen($mensaje) > 2000) {
    header('Location: contacto.html?error=longitud');
    exit;
}
if ($correo && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header('Location: contacto.html?error=correo');
    exit;
}
if (!preg_match('/^[\d\s\+\-\(\)]{7,20}$/', $telefono)) {
    header('Location: contacto.html?error=telefono');
    exit;
}

// ── Envío SMTP con PHPMailer ─────────────────────────────────────
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;   // SSL port 465
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    // Remitente y destinatario
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->addAddress(MAIL_DESTINO);
    if ($correo) {
        $mail->addReplyTo($correo, $nombre);
    }

    // Asunto
    $mail->Subject = 'Consulta desde el sitio web — ' . $nombre;

    // Cuerpo HTML
    $servicioHtml = $servicio ? "<tr><td><b>Servicio:</b></td><td>{$servicio}</td></tr>" : '';
    $empresaHtml  = $empresa  ? "<tr><td><b>Empresa:</b></td><td>{$empresa}</td></tr>"  : '';
    $correoHtml   = $correo   ? "<tr><td><b>Correo:</b></td><td>{$correo}</td></tr>"    : '';

    $mail->isHTML(true);
    $mail->Body = "
    <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
      <div style='background:#1a3a5c;padding:24px 32px;border-radius:8px 8px 0 0;'>
        <h2 style='color:#fff;margin:0;font-size:20px;'>Nueva consulta desde paniaguaramirezsrl.com</h2>
      </div>
      <div style='background:#f9f9f9;padding:32px;border:1px solid #e0e0e0;border-top:none;border-radius:0 0 8px 8px;'>
        <table style='width:100%;border-collapse:collapse;font-size:15px;'>
          <tr><td style='padding:8px 0;width:120px;'><b>Nombre:</b></td><td style='padding:8px 0;'>{$nombre}</td></tr>
          {$empresaHtml}
          <tr><td style='padding:8px 0;'><b>Teléfono:</b></td><td style='padding:8px 0;'>{$telefono}</td></tr>
          {$correoHtml}
          {$servicioHtml}
        </table>
        <hr style='border:none;border-top:1px solid #ddd;margin:20px 0;'>
        <p style='font-size:15px;margin:0 0 8px;'><b>Mensaje:</b></p>
        <p style='font-size:15px;color:#333;white-space:pre-wrap;'>{$mensaje}</p>
        <hr style='border:none;border-top:1px solid #ddd;margin:20px 0;'>
        <p style='font-size:12px;color:#999;margin:0;'>IP: {$ip} · " . date('d/m/Y H:i') . " UTC</p>
      </div>
    </div>";

    $mail->AltBody = "Nombre: {$nombre}\nTeléfono: {$telefono}\n"
        . ($empresa  ? "Empresa: {$empresa}\n"   : '')
        . ($correo   ? "Correo: {$correo}\n"     : '')
        . ($servicio ? "Servicio: {$servicio}\n" : '')
        . "\nMensaje:\n{$mensaje}";

    $mail->send();
    header('Location: gracias.html');

} catch (Exception $e) {
    // Log el error (solo en servidor, no exponer al usuario)
    error_log('PHPMailer error: ' . $mail->ErrorInfo);
    header('Location: contacto.html?error=envio');
}
exit;
