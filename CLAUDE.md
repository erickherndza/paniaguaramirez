# CLAUDE.md — Paniagua Ramírez, Servicios y Soluciones Financieras, SRL
# Sitio web estático multi-página — Erick Hernández
# Leer COMPLETO al inicio de cada sesión antes de cualquier acción.

---

## CLIENTE

**Paniagua Ramírez, Servicios y Soluciones Financieras, SRL**
- Firma dominicana de auditoría, impuestos y asesoría financiera
- Teléfono / WhatsApp: +1 (829) 801-3142
- Correo: paniaguaramirezservicios@gmail.com
- Horario: Lun – Vie, 8:00 am – 5:00 pm
- Ubicación: Santo Domingo, República Dominicana

---

## STACK

- HTML5 + CSS3 + Bootstrap 5.3.3
- Font Awesome 6.5.1
- Google Fonts: Poppins + Inter
- Sin backend — sitio estático puro
- Deploy: GitHub Pages

---

## REPOSITORIO Y URL

- **GitHub:** https://github.com/erickherndza/paniaguaramirez
- **URL pública:** https://erickherndza.github.io/paniaguaramirez/
- **Branch:** main
- **Remote local:** origin

### Deploy (flujo normal)
```bash
cd '/Users/erickhernandez/julio paniagua'
git add .
git commit -m "descripción del cambio"
git push
```
GitHub Actions despliega automáticamente en ~2 minutos tras cada push.

---

## ESTRUCTURA DE ARCHIVOS

```
/julio paniagua/
├── index.html          — Inicio (hero slider + highlights)
├── servicios.html      — 6 servicios detallados
├── nosotros.html       — Sobre la firma + Misión/Visión/Valores
├── proceso.html        — 3 pasos + FAQ acordeón
├── contacto.html       — Formulario → WhatsApp + info de contacto
├── .nojekyll           — Evita que GitHub Pages procese con Jekyll
├── CLAUDE.md           — Este archivo
└── assets/
    ├── css/
    │   └── style.css   — CSS compartido entre todas las páginas
    ├── js/
    │   └── contacto.js — Lógica del formulario de contacto → WhatsApp
    └── img/
        ├── logo.png          — Logo principal (navbar)
        ├── icono-footer.png  — Ícono 75×75px en footer
        ├── slider01.jpg      — Foto hero slide 1
        └── slider02.jpg      — Foto hero slide 2
```

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
- Topbar con teléfono, email y WhatsApp
- Navbar con logo + 5 links + botón "Consulta gratis"
- Hero carousel 2 slides (imágenes full-width: slider01.jpg / slider02.jpg)
- Feature strip (4 pilares: Confidencialidad, DGII, 6 áreas, Atención directa)
- Preview 3 servicios + botón "Ver todos"
- About teaser con carousel de 4 fotos (Unsplash) + badge
- CTA banner → WhatsApp

### servicios.html — Servicios
- Page hero (banner azul con título)
- 6 service cards completas con descripción extendida
- Feature strip
- CTA banner

### nosotros.html — Nosotros
- Page hero
- About completo (texto + carousel 4 fotos) + badge
- MVV cards: Misión / Visión / Valores
- CTA banner

### proceso.html — Cómo trabajamos
- Page hero
- 3 pasos con listas de detalle (Diagnóstico / Organización / Acompañamiento)
- FAQ con acordeón Bootstrap (5 preguntas)
- CTA banner

### contacto.html — Contacto
- Page hero
- 3 tarjetas de info (teléfono, email, horario)
- Formulario que arma mensaje y abre WhatsApp (`contacto.js`)
- Botón directo WhatsApp + botón correo electrónico

---

## COMPONENTES CLAVE

### Hero slider (index.html)
- Imágenes de fondo a pantalla completa (`object-fit: cover`)
- Overlay oscuro degradado (80% → 45% de izquierda a derecha)
- Layout dos columnas: título izquierda / descripción+botones derecha
- Efecto Ken Burns en la foto activa (zoom sutil 8s)
- Textos siempre en blanco — `.hero-content h1 { color: #fff; }`

### About carousel (index.html y nosotros.html)
- Bootstrap carousel con 4 fotos Unsplash
- IDs únicos: `aboutCarouselHome` / `aboutCarouselNosotros`
- Badge flotante con nombre de la firma
- En mobile: altura fija 260px (no aspect-ratio)

### Formulario de contacto (contacto.js)
- Lee campos: nombre, empresa, teléfono, servicio, mensaje
- Valida campos requeridos (nombre, teléfono, mensaje)
- Construye mensaje con formato WhatsApp (*negrita*)
- Abre `wa.me/18298013142?text=...` en nueva pestaña

---

## RESPONSIVE — BREAKPOINTS

| Breakpoint | Cambios principales |
|---|---|
| ≤ 991px (tablet) | Hero apilado, about-badge estático, nav-logo 44px |
| ≤ 767px (mobile) | Topbar 1 línea (tel + WA), logo 114px, hero padding 20px laterales, carousel altura fija |
| ≤ 480px (mobile xs) | Hero más compacto, logo 80px, carousel 210px |

### Reglas anti-overflow
```css
html, body { overflow-x: hidden; max-width: 100%; }
```
Aplicado globalmente para evitar scroll horizontal.

### Topbar en mobile
- Solo muestra: teléfono (izquierda) + ícono WhatsApp (derecha)
- Ocultos: email, divisor `|`, horario

---

## ERRORES CONOCIDOS Y SOLUCIONES

### E-001: Texto del hero pegado al borde izquierdo en mobile
**Causa:** Bootstrap container tiene padding mínimo en xs y el texto largo desborda.
**Fix:** `padding: 52px 20px 44px !important` en `.hero-content` a ≤767px.

### E-002: Títulos h1/h2 azul oscuro sobre fondo hero
**Causa:** Regla global `h1,h2 { color: var(--text-dark) }` tiene más especificidad que el color heredado.
**Fix:** Declarar `color: #fff` explícito en `.hero-content h1`.

### E-003: GitHub Pages muestra 404
**Causa:** Pages intentaba procesar con Jekyll o Source no configurado.
**Fix:** Archivo `.nojekyll` en raíz + workflow "Static HTML" en GitHub Actions.

### E-004: Push rechazado (fetch first)
**Causa:** GitHub creó commit del workflow desde la UI web.
**Fix:** `git pull --rebase origin main && git push`

### E-005: Scroll horizontal en mobile
**Causa:** Elementos con `width: 100vw` o sin `overflow-x: hidden`.
**Fix:** `html, body { overflow-x: hidden; max-width: 100%; }`

---

## LOG DE SESIÓN

### 2026-07-20 — Construcción completa desde cero

**Punto de partida:** `index.html` landing page de una sola página.

**Lo construido:**
1. Convertida a web multi-página: 5 archivos HTML independientes
2. CSS extraído a `assets/css/style.css` (compartido)
3. JS de contacto extraído a `assets/js/contacto.js`
4. Hero slider: imágenes full-width con overlay + layout dos columnas
5. About media: slider de 4 fotos en lugar de placeholder con play button
6. Logo en navbar (todos los archivos) y footer
7. Ícono 75×75 en footer (todos los archivos)
8. Imágenes propias del cliente: slider01.jpg, slider02.jpg, logo.png, icono-footer.png
9. Responsive completo: 4 breakpoints, topbar simplificado en mobile
10. Deploy en GitHub Pages con workflow Static HTML
11. Fixes: .nojekyll, overflow-x, hero padding, color títulos hero

**Pendientes para próximas sesiones:**
- Agregar dirección física si el cliente la proporciona
- Fotos reales del equipo en sección Nosotros
- Testimonios de clientes
- Google Analytics / Meta Pixel si se requiere tracking
- Dominio personalizado (Custom domain en GitHub Pages)
