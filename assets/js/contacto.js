// Timestamp anti-bot: registra el momento en que la página cargó
// send_mail.php verifica que hayan pasado al menos 3 segundos antes del envío
(function () {
  var ts = document.getElementById('form_ts');
  if (ts) ts.value = Date.now();
})();
