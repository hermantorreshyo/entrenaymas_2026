<div class="footer">
  <div class="col-md-4">
    <div class="pull-right">
      <div class="logo"><a href="<?php echo mklink("/");?>"><img src="images/footer-logo.png" alt="Logo" /></a></div>
      <?php $t = $web_model->get_text("TextoFooter","Lorem Ipsum is simply dummy text<br>of the printing and typesetting...")  ?>
      <p class="editable" data-id="<?php echo $t->id ?>" data-clave="<?php echo $t->clave ?>"><?php echo $t->plain_text ?></p>
      <div class="social-links">
        <ul>
          <?php if (!empty($empresa->facebook)) { ?>
            <li><a target="_blank" href="<?php echo $empresa->facebook ?>"><i class="fa fa-facebook-f" aria-hidden="true"></i></a></li>
          <?php } ?>
          <?php if (!empty($empresa->instagram)) { ?>
            <li><a target="_blank" href="<?php echo $empresa->instagram ?>"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="footer-right">
      <div class="col-md-4">
        <h6>Contacto</h6>
        <div class="contact-info"> <a ><?php echo mb_convert_encoding($empresa->direccion, 'UTF-8', 'ISO-8859-1') ?><br>
          <?php echo mb_convert_encoding($empresa->codigo_postal." ".$empresa->ciudad, 'UTF-8', 'ISO-8859-1') ?></a>
          <div class="block"> <i class="fa fa-phone" aria-hidden="true"></i> <a href="tel:<?php echo $empresa->telefono ?>"><?php echo $empresa->telefono ?></a> </div>
          <div class="block"> <i class="fa fa-envelope" aria-hidden="true"></i> <a href="mailto:<?php echo $empresa->email ?>"><?php echo $empresa->email ?></a> </div>
        </div>
      </div>
      <div class="col-md-3">
        <h6>Propiedades</h6>
        <div class="quick-menu">
          <ul>
            <?php 
            $localidades = $propiedad_model->get_localidades(array(
              "offset"=>4,
            ));
            foreach($localidades as $l) { ?>
              <li>
                <a href="<?php echo mklink ("propiedades/$l->link/") ?>">
                  <?php echo $l->nombre ?> 
                </a>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <div class="col-md-3">
        <h6>Vehículos</h6>
        <div class="quick-menu">
          <ul>
            <?php 
            $marcas = $auto_model->get_marcas_vehiculos(array(
              "offset"=>4,
              "tiene_elementos"=>1,
            ));
            foreach ($marcas as $l) { ?>
              <li>
                <a href="<?php echo mklink ("clasificados/$l->link/") ?>">
                  <?php echo $l->nombre ?>
                </a>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <div class="col-md-2">
        <h6>Productos</h6>
        <div class="quick-menu">
          <ul>
            <?php 
            $rub = $articulo_model->get_subcategorias(0,array(
              "tiene_productos"=>1,
              "offset"=>4,
            ));
            foreach ($rub as $l) { ?>
              <li>
                <a href="<?php echo mklink ("productos/$l->link/") ?>">
                  <?php echo mb_convert_encoding($l->nombre, 'UTF-8', 'ISO-8859-1') ?>
                </a>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- COPYRIGHT -->
<div class="copyright">
  <div class="container">
    <div class="row">
      <div class="col-md-5">
        <?php 
        $entradas = $entrada_model->get_list(array(
          "categoria"=>"informacion",
          "offset"=>4
        )); ?>
        <ul>
          <?php 
          $i=0;
          foreach($entradas as $r) { ?>
            <li>
              <a href="<?php echo mklink($r->link); ?>"><?php echo $r->titulo ?></a>
              <?php if ($i<sizeof($entradas)-1) { ?><i class="fa fa-circle" aria-hidden="true"></i><?php } ?>
            </li>
          <?php $i++; } ?>
        </ul>
      </div>
      <div class="col-md-7"> <span>ciudadclasificados.com (c) 2018. Todos los derechos reservados</span> <a target="_blank" href="http://www.misticastudio.com"><img src="images/mistica-brand.png" alt="Mistica" /></a> </div>
    </div>
  </div>
</div>
<!-- BACK TO TOP -->
<div class="back-top"><a href="javascript:void(0);"><i class="fa fa-angle-up" aria-hidden="true"></i></a></div>
<!-- SCRIPTS --> 
<script type="text/javascript" src="js/jquery.min.js"></script> 
<script type="text/javascript" src="js/bootstrap.min.js"></script> 
<script type="text/javascript" src="js/flexslider.js"></script> 
<script type="text/javascript" src="js/fancybox.js"></script> 
<script type="text/javascript" src="js/jquery.infinitescroll.min.js"></script> 
<script type="text/javascript" src="js/owl.carousel.js"></script> 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLtJGodU9yTjGDl0a0oB6h1zjR8qfXmSM"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
<script type="text/javascript" src="js/jquery-ui.min.js"></script> 
<script type="text/javascript">
//FANCYBOX SCRIPT
$(function() {
 $(".fancybox").fancybox({
  transitionIn : 'fade',
  transitionOut: 'fade',
  openEffect   : 'fade',
  closeEffect  : 'fade',
  nextEffect   : 'fade',
  prevEffect   : 'fade',
  helpers      : {
  overlay      :  {
  locked       : false,
  closeClick   : false
      },
     }
  });
});

//carousel script
  $('.product-listing').each( function() {
    var $carousel = $(this);
    var $items = ($carousel.data("items") !== undefined) ? $carousel.data("items") : 1;
    var $items_tablet = ($carousel.data("items-tablet") !== undefined) ? $carousel.data("items-tablet") : 1;
    var $items_mobile_landscape = ($carousel.data("items-mobile-landscape") !== undefined) ? $carousel.data("items-mobile-landscape") : 1;
    var $items_mobile_portrait = ($carousel.data("items-mobile-portrait") !== undefined) ? $carousel.data("items-mobile-portrait") : 1;
    $carousel.owlCarousel ({
      loop : ($carousel.data("loop") !== undefined) ? $carousel.data("loop") : true,
      items : $carousel.data("items"),
      margin : ($carousel.data("margin") !== undefined) ? $carousel.data("margin") : 0,
      dots : ($carousel.data("dots") !== undefined) ? $carousel.data("dots") : true,
      nav : ($carousel.data("nav") !== undefined) ? $carousel.data("nav") : false,
      navText : ["<div class='slider-no-current'><span class='current-no'></span><span class='total-no'></span></div><span class='current-monials'></span>", "<div class='slider-no-next'></div><span class='next-monials'></span>"],
      autoplay : ($carousel.data("autoplay") !== undefined) ? $carousel.data("autoplay") : false,
      autoplayTimeout : ($carousel.data("autoplay-timeout") !== undefined) ? $carousel.data("autoplay-timeout") : 5000,
      animateOut : ($carousel.data("animateout") !== undefined) ? $carousel.data("animateout") : false,
      animateIn : ($carousel.data("animatein") !== undefined) ? $carousel.data("animatein") : false,
      mouseDrag : ($carousel.data("mouse-drag") !== undefined) ? $carousel.data("mouse-drag") : true,
      autoWidth : ($carousel.data("auto-width") !== undefined) ? $carousel.data("auto-width") : false,
      autoHeight : ($carousel.data("auto-height") !== undefined) ? $carousel.data("auto-height") : false,
      responsiveClass: true,
      responsive : {
        0 : {
          items : $items_mobile_portrait,
        },
        480 : {
          items : $items_mobile_landscape,
        },
        768 : {
          items : $items_tablet,
        },
        960 : {
          items : $items,
        }
      }
    });
    var totLength = $(".owl-dot", $carousel).length;
    $(".total-no", $carousel).html(totLength);
    $(".current-no", $carousel).html(totLength);
    $carousel.owlCarousel();
    $(".current-no", $carousel).html(1);
    $carousel.on('changed.owl.carousel', function(event) {
      var total_items = event.page.count;
      var currentNum = event.page.index + 1;
      $(".total-no", $carousel ).html(total_items);
      $(".current-no", $carousel).html(currentNum);
    });
  });

    //range slider script
  $(function() {
    $('.range-slider').each(function(i,e){
      var val_min = $(e).data("val-min");
      var val_max = $(e).data("val-max");
      var max = $(e).data("max");
      $(e).slider({
        range: true,
        step: 2,
        min: 0,
        max: max,
        values: [val_min, val_max],
        slide: function(event, ui) {
          $(e).parent().find('.range-amount').val('$' + ui.values[ 0 ] + ' - $' + ui.values[1]);
        }
      });
      $(e).parent().find(".range-amount").val('$' + $(e).slider('values', 0) + ' - $' + $(e).slider('values', 1));
    });
  });

  /*ADD SCRIPT*/
$(".propiedades").click(function(e) {
                    $(".text-block").addClass("propiedades-content");
                    $(".text-block").removeClass("vehiculos-content");
                    $(".text-block").removeClass("otros-content");
       });
     
/*ADD SCRIPT*/
$(".vehiculos").click(function(e) {
                    $(".text-block").addClass("vehiculos-content");
                              $(".text-block").removeClass("propiedades-content");
                              $(".text-block").removeClass("otros-content");

       });
     
     /*ADD SCRIPT*/
$(".otros").click(function(e) {
                    $(".text-block").addClass("otros-content");
                                        $(".text-block").removeClass("vehiculos-content");
                                        $(".text-block").removeClass("propiedades-content");

       });

  
   
      //BACK TO TOP SCRIPT
jQuery(".back-top").hide();
jQuery(function () {
  jQuery(window).scroll(function () {
      if (jQuery(this).scrollTop() > 150) {
          jQuery('.back-top').fadeIn();
      } else {
          jQuery('.back-top').fadeOut();
      }
  });
  jQuery('.back-top a').click(function () {
      jQuery('body,html').animate({
          scrollTop: 0
      }, 350);
      return false;
  });
  
});
</script>
<script type="text/javascript">

var id_localidad_seleccionada = 0;
function cambiar_provincia(id_provincia) {
  $.ajax({
    url: '/sistema/localidades/function/get_by_provincia/'+id_provincia+"/",
    dataType: 'json',
    success: function(datos) {
      $(".localidad_select").empty();
      $(".localidad_select").append('<option value="0">Localidad</option>');
      for(var i=0;i<datos.results.length;i++) {
        var o = datos.results[i];
        $(".localidad_select").append('<option '+((o.id == id_localidad_seleccionada)?"selected":"")+' data-latitud="'+o.latitud+'" data-longitud="'+o.longitud+'" value="'+o.id+'">'+o.nombre+'</option>');
      }
    },
  });
}  

function enviar_comentario() {
  var id_consulta = $("#comentario_id_consulta").val();
  var tipo = $("#comentario_tipo_consulta").val();
  var para = $("#comentario_para").val();
  var nombre = $("#comentario_nombre").val();
  if (isEmpty(nombre)) {
   alert("Por favor escriba su nombre.");
   $("#comentario_nombre").focus();
   return false;
  }
  var email = $("#comentario_email").val();
  if (!validateEmail(email)) {
   alert("Por favor escriba su email.");
   $("#comentario_email").focus();
   return false;
  }

  var telefono = $("#comentario_telefono").val();
  if (isEmpty(telefono)) {
   alert("Por favor escriba su telefono.");
   $("#comentario_telefono").focus();
   return false;
  }

  var comentario = $("#comentario_texto").val();
  if (isEmpty(comentario)) {
   alert("Por favor escriba su comentario.");
   $("#comentario_texto").focus();
   return false;
  }

  var datos = {
    "nombre":nombre,
    "email":email,
    "telefono":telefono,
    "mensaje":comentario,
    "g-recaptcha-response": grecaptcha.getResponse(),
    "id_empresa":"<?php echo $empresa->id; ?>",
    "es_usuario":0,
    "para":para,
    "bcc":"<?php echo $empresa->bcc_email ?>",
    "estado_comentario":0, // USUARIO INACTIVO
  };
  datos[tipo] = id_consulta;

  $.ajax({
   "url":"/sistema/consultas/function/enviar/",
   "data": datos,
   "dataType":"json",
   "type":"post",
   "success":function(r) {
     alert("Hemos enviado su contacto. Muchas gracias!");
     if (r.error == 0) location.reload();
   }
  });
  return false;
  }
</script>
<script type="text/javascript">
   function cambiar_imagen(imagen) {
 $('#imagen_ppal').attr('src',imagen);
}

// =====================================================
// FUNCIONES DE LOGIN Y REGISTRO

function enviar_login() {
  var self = this;
  $("#login_submit").attr("disabled","disabled");
  try {
    var email = validate_input("login_email",IS_EMAIL,"Por favor ingrese un email correcto");
    var password = validate_input("login_password",IS_EMPTY,"Por favor ingrese su clave de acceso");    
  } catch(e) {
    $("#login_submit").removeAttr("disabled");
    return false;
  }
  password = hex_md5(password);
  return login(email,password);
}

function reset_password() {
  var email = validate_input("login_email",IS_EMAIL,"Por favor ingrese su email. Le enviaremos una nueva clave a su casilla de correo.");
  $.ajax({
    url: '/sistema/clientes/function/reset_password/',
    type: 'POST',
    dataType: 'json',
    data: {
      'email': email, 
      'id_empresa': ID_EMPRESA,
    },
    success: function(data) {
      alert(data.mensaje);
    },
  });
  return false;
}

function login(email,password,callback) {
  callback = (typeof callback != "undefined") ? callback : null;
  $.ajax({
    url: '/sistema/login/check_cliente/',
    type: 'POST',
    dataType: 'json',
    data: {
      'email': email, 
      'password': password,
      'id_empresa': ID_EMPRESA,
    },
    success: function(data, textStatus, xhr) {
      $("#login_submit").removeAttr("disabled");
      if (data.error == false && data.id_empresa == ID_EMPRESA) {
        if (callback != null) callback();
        else {
          // ENTRAMOS AL AREA PRIVADA
          location.href="<?php echo mklink("web/privada/"); ?>";
        }
      } else {
        if (data.mensaje !== undefined) {
          alert(data.mensaje);
        } else {
          alert("Nombre de usuario y/o password incorrectos.");
        }
        $("#login_email").focus();                
      }
    },
    error: function() {
      alert("Ocurrio un error al enviar los datos de ingreso.");
      $("#login_submit").removeAttr("disabled");
    }
  });
  return false;
}

function enviar_registro() {
  $(".warning").removeClass("warning");
  var nombre = $("#registro_nombre").val();
  var apellido = $("#registro_apellido").val();
  var telefono = $("#registro_telefono").val();
  var provincia = $("#registro_provincia").val();
  var localidad = $("#registro_localidad").val();
  var email = $("#registro_email").val();
  var password = $("#registro_password").val();
  var password_2 = $("#registro_password_2").val();
  var calle = $("#registro_calle").val();
  var altura = $("#registro_altura").val();
  var piso = $("#registro_piso").val();
  var depto = $("#registro_numero").val();

  if (isEmpty(nombre)) {
    alert("Por favor ingrese su nombre");
    $("#registro_nombre").addClass("warning");
    $("#registro_nombre").focus();
    return false;
  }
  if (isEmpty(apellido)) {
    alert("Por favor ingrese su apellido");
    $("#registro_apellido").addClass("warning");
    $("#registro_apellido").focus();
    return false;
  }
  if (!validateEmail(email)) {
    alert("Por favor ingrese su email");
    $("#registro_email").prev().addClass("warning");
    $("#registro_email").addClass("warning");
    $("#registro_email").focus();
    return false;
  }
  if (isEmpty(telefono)) {
    alert("Por favor ingrese su telefono");
    $("#registro_telefono").addClass("warning");
    $("#registro_telefono").focus();
    return false;
  }
  if (provincia == 0) {
    alert("Por favor seleccione una provincia");
    $("#registro_provincia").addClass("warning");
    $("#registro_provincia").focus();
    return false;
  }
  if (localidad == 0) {
    alert("Por favor seleccione una localidad");
    $("#registro_localidad").addClass("warning");
    $("#registro_localidad").focus();
    return false;
  }
  if (isEmpty(password)) {
    alert("Por favor ingrese su clave");
    $("#registro_password").addClass("warning");
    $("#registro_password").focus();
    return false;
  }
  if (isEmpty(password_2)) {
    alert("Por favor reingrese la clave");
    $("#registro_password_2").addClass("warning");
    $("#registro_password_2").focus();
    return false;
  }
  if (password != password_2) {
    alert("ERROR: Las claves no coinciden. Intente nuevamente.");
    $("#registro_password_2").addClass("warning");
    $("#registro_password_2").focus();
    return false;
  }

  password = hex_md5(password);
  var direccion = calle+" "+altura+" "+piso+" "+depto;

  $("#registro_submit").attr("disabled","disabled");
  $("#registro_submit").val("Enviando datos...");
  $.ajax({
    "url":"/sistema/clientes/function/registrar/",
    "dataType":"json",
    "data": {
      "nombre":nombre+" "+apellido,
      "email":email,
      "telefono":telefono,
      "password":password,
      "direccion":direccion,
      "id_localidad":localidad,
      "id_provincia":provincia,
      "id_empresa":<?php echo $empresa->id ?>,
      'g-recaptcha-response': grecaptcha.getResponse(),
    },
    "type":"post",
    "success":function(r) {
      if (r.error == 0) {
        // Si nos registramos correctamente, nos logueamos directo
        window.login(email,password,registro_paso_2);
      } else {
        alert(r.mensaje);
        $("#registro_submit").val("Enviar");
      }
      $("#registro_submit").removeAttr("disabled");
    },
    "error":function() {
      $("#registro_submit").removeAttr("disabled");
    },
  });
  return false;
}

function registro_paso_2(params) {
  location.href="<?php echo mklink("web/privada/"); ?>";
}
</script>
