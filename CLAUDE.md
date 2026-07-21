# CLAUDE.md — Paniagua Ramírez, Servicios y Soluciones Financieras, SRL
# Sitio web estático multi-página — Erick Hernández
# Leer COMPLETO al inicio de cada sesión antes de cualquier acción.

---

## CLIENTE

**Paniagua Ramírez, Servicios y Soluciones Financieras, SRL**
- Firma dominicana de auditoría, impuestos y asesoría financiera
- Teléfono / WhatsApp: +1 (829) 801-3142
- Correo cliente: paniaguaramirezservicios@gmail.com
- Correo del sitio: info@paniaguaramirezsrl.com
- Horario: Lun – Vie, 8:00 am – 5:00 pm
- Ubicación: Santo Domingo, República Dominicana
- Facebook: https://www.facebook.com/paniaguaramirezsrl
- Instagram: https://www.instagram.com/paniaguaramirezsrl

---

## STACK

- HTML5 + CSS3 + Bootstrap 5.3.3
- Font Awesome 6.5.1
- Google Fonts: Poppins + Inter
- PHP: `send_mail.php` — mail() nativo de cPanel (formulario → info@paniaguaramirezsrl.com)
- Deploy: GitHub Actions (SamKirkland/FTP-Deploy-Action) → FTP → Banahosting (cPanel)

---

## REPOSITORIO Y HOSTING

- **GitHub:** https://github.com/erickherndza/paniaguaramirez
- **URL pública:** https://paniaguaramirezsrl.com/
- **Branch:** main
- **Remote local:** origin
- **Servidor:** Banahosting cPanel — usuario `mybcfcli`
- **Document root:** `/home/mybcfcli/paniaguaramirezsrl.com/`

### Deploy (flujo normal)
```bash
cd '/Users/erickhernandez/julio paniagua'
git add .
git commit -m "descripción del cambio"
git push
```
GitHub Actions despliega automáticamente vía FTP en ~2-3 min tras cada push.

### Secrets en GitHub (Settings → Secrets → Actions)
| Secret | Valor |
|--------|-------|
| FTP_HOST | servidor Banahosting |
| FTP_USER | usuario cPanel |
| FTP_PASS | contraseña cPanel |
| FTP_DIR | `paniaguaramirezsrl.com/` |

### Workflow
- Archivo: `.github/workflows/deploy.yml`
- Usa `SamKirkland/FTP-Deploy-Action@v4.3.5`
- `dangerous-clean-slate: true` — sube todos los archivos en cada deploy
- Excluye: `.git*`, `CLAUDE.md`, `.DS_Store`, `.nojekyll`, `.gitignore`

---

## ESTRUCTURA DE ARCHIVOS

```
/julio paniagua/
├── index.html          — Inicio (hero slider + highlights)
├── servicios.html      — 6 servicios detallados
├── nosotros.html       — Sobre la firma + Misión/Visión/Valores
├── proceso.html        — 3 pasos + FAQ acordeón
├── contacto.html       — Formulario email (POST → send_mail.php) + info de contacto
├── gracias.html        — Página de confirmación tras envío del formulario
├── 404.html            — Página de error 404 (SEO optimizada)
├── send_mail.php       — Mailer PHP: recibe POST, envía a info@paniaguaramirezsrl.com
├── .htaccess           — ErrorDocument 404 /404.html
├── CLAUDE.md           — Este archivo
└── assets/
    ├── css/
    │   └── style.css   — CSS compartido entre todas las páginas
    ├── js/
    │   └── contacto.js — (vacío — formulario usa POST nativo a send_mail.php)
    └── img/
        ├── logo.png          — Logo principal (navbar)
        ├── icono-footer.png  — Ícono 75×75px en footer
        ├── slider01.jpg      — Foto hero slide 1
        └── slider02.jpg      — Foto hero slide 2
```

---

## REDES SOCIALES

Aparecen en **todas las páginas** (header topbar + footer columna Contacto):
- Facebook: https://www.facebook.com/paniaguaramirezsrl
- Instagram: https://www.instagram.com/paniaguaramirezsrl
- WhatsApp: https://wa.me/18298013142

Íconos: Font Awesome — `fa-brands fa-facebook-f`, `fa-brands fa-instagram`, `fa-brands fa-whatsapp`

---

## FORMULARIO DE CONTACTO

- Archivo form: `contacto.html` — `<form action="send_mail.php" method="POST">`
- Campos con `name=""`: nombre, empresa, telefono, correo, servicio, mensaje
- Backend: `send_mail.php` → sanitiza, valida, envía con `mail()` a `info@paniaguaramirezsrl.com`
- Éxito → redirige a `gracias.html`
- Error → redirige a `contacto.html?error=campos` o `?error=envio`
- **NUNCA usar `_external=True` en URLs** — guardar siempre rutas relativas

---

## PALETA DE COLORES

```css
--blue:      #1C5CA8   /* azul principal */
--blue-dark: #123863   /* azul oscuro hover */
--navy:      #0F2A4A   /* azul navy (fondos hero, footer, strips) */
--ice:       #F3F7FB   /* gris muy claro (fondos secciones alternas) */
--text-body: #66707B   /* texto párrafos */
--text-dark: #132B47   /* texto títulos */
```

**Regla:** NUNCA hardcodear colores en los HTML — siempre usar las variables CSS.

---

## TIPOGRAFÍA

- **Títulos (h1–h4):** Poppins 700/800
- **Cuerpo:** Inter 400/500/600
- Cargadas desde Google Fonts en cada HTML

---

## PÁGINAS — CONTENIDO POR PÁGINA

### index.html — Inicio
- Topbar: teléfono · email · horario · Facebook · Instagram · WhatsApp
- Navbar: logo + 5 links + botón "Consulta gratis"
- Hero carousel 2 slides (slider01.jpg / slider02.jpg)
- Feature strip (4 pilares)
- Preview 3 servicios + botón "Ver todos"
- About teaser con carousel 4 fotos + badge
- CTA banner → WhatsApp

### servicios.html — Servicios
- Page hero + 6 service cards + feature strip + CTA banner

### nosotros.html — Nosotros
- Page hero + About completo + MVV cards + CTA banner

### proceso.html — Cómo trabajamos
- Page hero + 3 pasos + FAQ acordeón + CTA banner

### contacto.html — Contacto
- Page hero
- 3 tarjetas info (teléfono, email, horario)
- Formulario POST → send_mail.php (nombre, empresa, teléfono, correo, servicio, mensaje)
- Card WhatsApp directo + card correo

### gracias.html — Confirmación
- Logo + ícono check verde
- Mensaje: "Muchas gracias por contactarnos. En breve le contactaremos para asistirle."
- Botones: WhatsApp + Volver al inicio

### 404.html — Error 404
- SEO: `noindex, follow` + canonical a inicio
- Número 404 grande + links a todas las páginas
- Activado via `.htaccess`: `ErrorDocument 404 /404.html`

---

## RESPONSIVE — BREAKPOINTS

| Breakpoint | Cambios principales |
|---|---|
| ≤ 991px (tablet) | Hero apilado, about-badge estático, nav-logo 44px |
| ≤ 767px (mobile) | Topbar 1 línea, logo 114px, hero padding 20px, carousel altura fija |
| ≤ 480px (mobile xs) | Hero compacto, logo 80px, carousel 210px |

---

## ERRORES CONOCIDOS Y SOLUCIONES

### E-001: Texto del hero pegado al borde en mobile
**Fix:** `padding: 52px 20px 44px !important` en `.hero-content` a ≤767px.

### E-002: Títulos h1/h2 azul oscuro sobre hero
**Fix:** `color: #fff` explícito en `.hero-content h1`.

### E-003: FTP_DIR incorrecto → archivos van a carpeta equivocada
**Causa:** Poner ruta absoluta `/home/mybcfcli/paniaguaramirezsrl.com` en FTP_DIR.
**Fix:** FTP ya conecta en `/home/mybcfcli/` — usar solo `paniaguaramirezsrl.com/` (relativo).

### E-004: SamKirkland solo sube archivos "nuevos" ignorando los subidos manualmente
**Fix:** `dangerous-clean-slate: true` en el workflow — borra y re-sube todo.

### E-005: Scroll horizontal en mobile
**Fix:** `html, body { overflow-x: hidden; max-width: 100%; }`

---

## LOG DE SESIONES

### 2026-07-20 — Sesión 1: Construcción completa desde cero
1. Convertida a web multi-página: 5 HTML independientes
2. CSS extraído a `assets/css/style.css`
3. Hero slider full-width con overlay + layout dos columnas
4. About media: carousel 4 fotos + badge
5. Logo navbar + ícono footer en todas las páginas
6. Imágenes propias: slider01.jpg, slider02.jpg, logo.png, icono-footer.png
7. Responsive: 4 breakpoints

### 2026-07-20 — Sesión 2: Mejoras y deploy a Banahosting
1. Redes sociales (Facebook + Instagram) en topbar y footer de las 5 páginas
2. Formulario de contacto: cambiado de WhatsApp-only a POST → send_mail.php → info@paniaguaramirezsrl.com
3. `gracias.html`: página de confirmación con logo + mensaje de agradecimiento
4. `404.html`: página de error SEO optimizada (noindex, follow + links internos)
5. `.htaccess`: `ErrorDocument 404 /404.html`
6. Deploy: migrado de GitHub Pages a Banahosting FTP via SamKirkland/FTP-Deploy-Action
7. Fix FTP_DIR: `paniaguaramirezsrl.com/` (relativo, no absoluto)
8. Fix re-upload: `dangerous-clean-slate: true` para forzar subida completa

**Pendientes para próximas sesiones:**
- Verificar que send_mail.php funciona en producción (PHP mail() de cPanel)
- Agregar dirección física si el cliente la proporciona
- Fotos reales del equipo en sección Nosotros
- Testimonios de clientes
- Google Analytics / Meta Pixel
