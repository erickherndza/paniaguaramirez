document.getElementById('contactForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const nombre   = document.getElementById('nombre').value.trim();
  const empresa  = document.getElementById('empresa').value.trim();
  const telefono = document.getElementById('telefono').value.trim();
  const servicio = document.getElementById('servicio').value;
  const mensaje  = document.getElementById('mensaje').value.trim();

  if (!nombre || !telefono || !mensaje) {
    alert('Por favor complete los campos requeridos: nombre, teléfono y mensaje.');
    return;
  }

  let texto = `Hola, le escribo desde el sitio web.\n\n`;
  texto += `*Nombre:* ${nombre}\n`;
  if (empresa) texto += `*Empresa:* ${empresa}\n`;
  texto += `*Teléfono:* ${telefono}\n`;
  if (servicio) texto += `*Servicio de interés:* ${servicio}\n`;
  texto += `\n*Mensaje:*\n${mensaje}`;

  const url = `https://wa.me/18298013142?text=${encodeURIComponent(texto)}`;
  window.open(url, '_blank');
});
