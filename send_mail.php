<?php
// Rechazar peticiones que no sean POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.html');
    exit;
}

// Sanitizar entradas
$nombre   = htmlspecialchars(trim($_POST['nombre']   ?? ''), ENT_QUOTES, 'UTF-8');
$empresa  = htmlspecialchars(trim($_POST['empresa']  ?? ''), ENT_QUOTES, 'UTF-8');
$telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''), ENT_QUOTES, 'UTF-8');
$correo   = htmlspecialchars(trim($_POST['correo']   ?? ''), ENT_QUOTES, 'UTF-8');
$servicio = htmlspecialchars(trim($_POST['servicio'] ?? ''), ENT_QUOTES, 'UTF-8');
$mensaje  = htmlspecialchars(trim($_POST['mensaje']  ?? ''), ENT_QUOTES, 'UTF-8');

// Validar campos requeridos
if (!$nombre || !$telefono || !$mensaje) {
    header('Location: contacto.html?error=campos');
    exit;
}

// Validar correo si se proporcionó
if ($correo && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header('Location: contacto.html?error=correo');
    exit;
}

$destinatario = 'info@paniaguaramirezsrl.com';
$asunto       = '=?UTF-8?B?' . base64_encode('Nuevo mensaje desde el sitio web — ' . $nombre) . '?=';

// Cuerpo del mensaje
$cuerpo  = "Ha recibido un nuevo mensaje desde el sitio web paniaguaramirezsrl.com\n";
$cuerpo .= str_repeat('-', 50) . "\n\n";
$cuerpo .= "Nombre:   $nombre\n";
if ($empresa)  $cuerpo .= "Empresa:  $empresa\n";
$cuerpo .= "Teléfono: $telefono\n";
if ($correo)   $cuerpo .= "Correo:   $correo\n";
if ($servicio) $cuerpo .= "Servicio: $servicio\n";
$cuerpo .= "\nMensaje:\n$mensaje\n";
$cuerpo .= "\n" . str_repeat('-', 50) . "\n";
$cuerpo .= "Enviado el: " . date('d/m/Y H:i') . " (hora del servidor)\n";

// Cabeceras
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: =?UTF-8?B?" . base64_encode('Sitio Web Paniagua Ramírez') . "?= <info@paniaguaramirezsrl.com>\r\n";
if ($correo) {
    $headers .= "Reply-To: $correo\r\n";
}

if (mail($destinatario, $asunto, $cuerpo, $headers)) {
    header('Location: gracias.html');
} else {
    header('Location: contacto.html?error=envio');
}
exit;
