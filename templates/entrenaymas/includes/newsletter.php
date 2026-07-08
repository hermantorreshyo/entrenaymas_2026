<section class="psweb-newsletter">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-xl-6">
        <div class="psweb-icon wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">
          <img src="assets/images/icon15.png" alt="Newsletter">
        </div>
        <div class="newsletter-title">
          <p class="wow fadeInUp h3-newsletter" data-wow-duration="1s" data-wow-delay="0.7s">Recibe todas las novedades en tu email</p>
          <p class="wow fadeInUp h2-newsletter" data-wow-duration="1s" data-wow-delay="0.9s">¡Suscríbete al Newsletter!</p>
        </div>
      </div>
      <div class="col-xl-6 wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">
        <form onsubmit="return enviar_newsletter()">
          <div class="form-group">
            <input type="email" class="form-control" id="newsletter_email" placeholder="Ingresa tu email">
            <input type="submit" name="Suscribite" id="newsletter_submit" value="Suscribirse Ahora" class="psweb-btn">
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
function enviar_newsletter() {
  var email = $("#newsletter_email").val();
  if (!validateEmail(email)) {
    alert("Por favor ingrese un email valido.");
    $("#newsletter_email").focus();
    return false;
  }
  $("#newsletter_submit").attr('disabled', 'disabled');
  var datos = {
    "email":email,
    "mensaje":"Registro a Newsletter",
    "asunto":"Registro a Newsletter",
    "para":"<?php echo $empresa->email ?>",
    "id_empresa":ID_EMPRESA,
    "id_origen":2,
  }
  $.ajax({
    "url":"/sistema/consultas/function/enviar/",
    "type":"post",
    "dataType":"json",
    "data":datos,
    "success":function(r){
      if (r.error == 0) {
        alert("¡Muchas gracias por registrarse a nuestro newsletter!");
        location.reload();
      } else {
        alert("Ocurrio un error al enviar su email. Disculpe las molestias");
        $("#newsletter_submit").removeAttr('disabled');
      }
    }
  });  
  return false;
}  
</script>