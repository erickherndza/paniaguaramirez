<?php
declare(strict_types=1);

// ── Configuración ────────────────────────────────────────────────
const DESTINATARIO   = 'paniaguaramirezservicios@gmail.com';
const RATE_MAX       = 3;       // envíos máximos por IP por ventana
const RATE_VENTANA   = 3600;    // segundos (1 hora)
const TS_MIN         = 3;       // segundos mínimos desde que cargó el form
const MSG_MAX        = 2000;    // caracteres máximos en mensaje

// ── Solo POST ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.html');
    exit;
}

// ── Verificar referrer (debe venir del propio sitio) ─────────────
$host    = $_SERVER['HTTP_HOST'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if ($referer && parse_url($referer, PHP_URL_HOST) !== $host) {
    http_response_code(403);
    exit;
}

// ── Honeypot (bots lo llenan, humanos no) ────────────────────────
if (!empty($_POST['website'])) {
    // Silencioso — no revelar al bot que fue detectado
    header('Location: gracias.html');
    exit;
}

// ── Verificar timestamp anti-bot ─────────────────────────────────
$form_ts = (int)($_POST['form_ts'] ?? 0);
if ($form_ts > 0) {
    $elapsed = (int)(microtime(true) * 1000) - $form_ts;
    if ($elapsed < (TS_MIN * 1000)) {
        header('Location: contacto.html?error=rapido');
        exit;
    }
}

// ── Rate limiting por IP ─────────────────────────────────────────
$ip        = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$cache_dir = sys_get_temp_dir();
$cache_f   = $cache_dir . '/pr_rl_' . md5($ip) . '.json';
$now       = time();

$rl = ['first' => $now, 'count' => 0];
if (file_exists($cache_f)) {
    $rl = json_decode(file_get_contents($cache_f), true) ?? $rl;
    if (($now - $rl['first']) > RATE_VENTANA) {
        $rl = ['first' => $now, 'count' => 0];
    }
}
$rl['count']++;
file_put_contents($cache_f, json_encode($rl), LOCK_EX);

if ($rl['count'] > RATE_MAX) {
    header('Location: contacto.html?error=limite');
    exit;
}

// ── Sanitizar entradas ───────────────────────────────────────────
function limpiar(string $val): string {
    // Eliminar saltos de línea (previene email injection)
    return trim(str_replace(["\r", "\n", "\r\n"], ' ',
        htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
    ));
}

$nombre   = limpiar($_POST['nombre']   ?? '');
$empresa  = limpiar($_POST['empresa']  ?? '');
$telefono = limpiar($_POST['telefono'] ?? '');
$correo   = limpiar($_POST['correo']   ?? '');
$servicio = limpiar($_POST['servicio'] ?? '');
$mensaje  = limpiar($_POST['mensaje']  ?? '');

// ── Validar campos requeridos ────────────────────────────────────
if (!$nombre || !$telefono || !$mensaje) {
    header('Location: contacto.html?error=campos');
    exit;
}

// ── Validar longitudes ───────────────────────────────────────────
if (mb_strlen($nombre) > 120 || mb_strlen($mensaje) > MSG_MAX || mb_strlen($empresa) > 120) {
    header('Location: contacto.html?error=longitud');
    exit;
}

// ── Validar correo ───────────────────────────────────────────────
if ($correo && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header('Location: contacto.html?error=correo');
    exit;
}

// Limpiar correo extra (prevenir header injection)
$correo = filter_var($correo, FILTER_SANITIZE_EMAIL);

// ── Validar teléfono (solo dígitos, +, espacios, guiones) ────────
if (!preg_match('/^[\d\s\+\-\(\)]{7,20}$/', $telefono)) {
    header('Location: contacto.html?error=telefono');
    exit;
}

// ── Bloquear spam común ──────────────────────────────────────────
$spam_keywords = ['http://', 'https://', 'www.', 'casino', 'viagra', 'loan', 'crypto', 'bitcoin', 'click here', 'unsubscribe'];
$texto_check   = strtolower($nombre . ' ' . $mensaje);
foreach ($spam_keywords as $kw) {
    if (str_contains($texto_check, $kw)) {
        header('Location: gracias.html'); // Silencioso
        exit;
    }
}

// ── Construir correo ─────────────────────────────────────────────
$asunto  = '=?UTF-8?B?' . base64_encode('Consulta desde el sitio web — ' . $nombre) . '?=';

$cuerpo  = "Nuevo mensaje desde paniaguaramirezsrl.com\n";
$cuerpo .= str_repeat('─', 50) . "\n\n";
$cuerpo .= "Nombre:   {$nombre}\n";
if ($empresa)  $cuerpo .= "Empresa:  {$empresa}\n";
$cuerpo .= "Teléfono: {$telefono}\n";
if ($correo)   $cuerpo .= "Correo:   {$correo}\n";
if ($servicio) $cuerpo .= "Servicio: {$servicio}\n";
$cuerpo .= "\nMensaje:\n{$mensaje}\n";
$cuerpo .= "\n" . str_repeat('─', 50) . "\n";
$cuerpo .= "IP: {$ip}\n";
$cuerpo .= "Fecha: " . date('d/m/Y H:i') . " UTC\n";

// ── Cabeceras seguras (sin injection) ────────────────────────────
$from_name = '=?UTF-8?B?' . base64_encode('Sitio Web Paniagua Ramírez') . '?=';
$headers   = implode("\r\n", [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    "From: {$from_name} <no-reply@paniaguaramirezsrl.com>",
    'X-Mailer: PHP/' . PHP_VERSION,
]);

// Reply-To solo si el correo es válido (ya sanitizado arriba)
if ($correo) {
    $headers .= "\r\nReply-To: {$correo}";
}

// ── Enviar ───────────────────────────────────────────────────────
if (mail(DESTINATARIO, $asunto, $cuerpo, $headers)) {
    header('Location: gracias.html');
} else {
    header('Location: contacto.html?error=envio');
}
exit;
