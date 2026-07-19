<?php 
include("includes/init.php");

function get_provincias() {
  global $conx;
  $sql = "SELECT D.* FROM com_departamentos D ";
  $sql.= "INNER JOIN com_provincias P ON (D.id_provincia = P.id) ";
  $sql.= "WHERE P.id_pais = 196 ";
  $sql.= "ORDER BY D.nombre ASC ";
  $q = mysqli_query($conx,$sql);
  $salida = array();
  while(($r=mysqli_fetch_object($q))!==NULL) {
    $salida[] = $r;
  }
  return $salida;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php") ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<style type="text/css">
.modal { background-color: rgba(0,0,0,0.5); }
.registro-hp { position: absolute; left: -9999px; top: -9999px; }
</style>
</head>
<body>

<script type='text/javascript'>var _pix = document.getElementById('_pix_id_50d406b7-0364-a51b-283b-1302ef9f03dd');if (!_pix) { var protocol = '//'; var a = Math.random() * 1000000000000000000; _pix = document.createElement('iframe'); _pix.style.display = 'none'; _pix.setAttribute('src', protocol + 'aax-eu.amazon-adsystem.com/s/iu3?d=generic&ex-fargs=%3Fid%3D50d406b7-0364-a51b-283b-1302ef9f03dd%26type%3D22%26m%3D44551&ex-fch=416613&ex-src=https://entrenaymas.com/&ex-hargs=v%3D1.0%3Bc%3D8269573310802%3Bp%3D50D406B7-0364-A51B-283B-1302EF9F03DD' + '&cb=' + a); _pix.setAttribute('id','_pix_id_50d406b7-0364-a51b-283b-1302ef9f03dd'); document.body.appendChild(_pix);}</script><noscript> <img height='1' width='1' border='0' alt='' src='https://aax-eu.amazon-adsystem.com/s/iui3?d=forester-did&ex-fargs=%3Fid%3D50d406b7-0364-a51b-283b-1302ef9f03dd%26type%3D22%26m%3D44551&ex-fch=416613&ex-src=https://entrenaymas.com/&ex-hargs=v%3D1.0%3Bc%3D8269573310802%3Bp%3D50D406B7-0364-A51B-283B-1302EF9F03DD' /></noscript>

<?php include("includes/header.php") ?>

<!-- Psweb Page Title -->
<section class="psweb-page-title">
  <div class="container">
    <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">Registro Profesional</h2>
    <ul class="breadcrumb wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
      <li><a href="<?php echo mklink("/") ?>">Home</a></li>
      <li><i class="fs14 ml10 mr10 fa fa-chevron-right"></i></li>
      <li>Registro</li>
    </ul>
  </div>
</section>

<!-- Psweb Register -->
<section class="psweb-register">
  <div class="container">
    <div class="plans-title">
      <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s">Crea tu perfil gratuito</h4>
      <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">Registrarme como Profesional o Centro Especializado</h2>

      <?php $t = $web_model->get_text("registro-parrafo"); ?>
      <p class="wow fadeInUp editable" data-id="<?php echo $t->id ?>" data-clave="<?php echo $t->clave ?>" data-wow-duration="1s" data-wow-delay="0.9s"><?php echo nl2br($t->plain_text) ?></p>

    </div>
    <?php /*
    <div class="row">
      <div class="col-md-4">
        <div class="register-item">
          <div class="psweb-icon"><img src="assets/images/icon45.png" alt="Icon"></div>
          <h5>Deje que los usuarios le encuentren</h5>
          <p>1,5 millones de usuarios visitan <br> Entrena y Mas cada mes.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="register-item">
          <div class="psweb-icon"><img src="assets/images/icon46.png" alt="Icon"></div>
          <h5>87% de los profesionales</h5>
          <p>Asegura haber mejorado el rendimiento de su consulta médica gracias a Entrena y Mas</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="register-item">
          <div class="psweb-icon"><img src="assets/images/icon47.png" alt="Icon"></div>
          <h5>9 de cada 10 especialistas</h5>
          <p>Recomendarían Entrena y Mas a otro <br> profesional.</p>
        </div>
      </div>
    </div>
    */ ?>
    <div class="row justify-content-center wow fadeInUp" id="select-subscription" data-wow-duration="1s" data-wow-delay="1.5s">
      <div class="col-sm-6 col-md-4">
        <div class="card text-center">
          <div class="card-body pt-5">
            <h5 class="subscription-title">Perfil básico</h5>
            <h6 class="card-subtitle mb-3 text-muted">Para profesionales</h6>
            <p class="card-text subscription-price">GRATIS</p>
            <ul class="subscription-item list-unstyled">
              <li class="li-success">Mensajes limitados</li>
              <li class="li-success">Ofertas y descuentos exclusivos de nuestros partners.</li>
              <li class="li-success">Perfil con información completa de servicios, tarifas, descripción, formación, fotos... </li>
            </ul>
            <a href="javascript:perfil_gratuito()" class="desde_precio_contactar font-weight-bolder">Crear perfil Gratuito</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
      <div class="card text-center">
        <span class="badge badge-info">RECOMENDADO</span>
          <div class="card-body pt-4">
            <h5 class="subscription-title">Plan Premium</h5>
            <h6 class="card-subtitle mb-3 text-muted">Para profesionales desde</h6>
            <p class="card-text subscription-price">
              4,90 <i class="fa fa-eur" aria-hidden="true"></i> mes
              / 
              49 <i class="fa fa-eur" aria-hidden="true"></i> año
            </p>
            <ul class="subscription-item list-unstyled">
              <li class="li-success">Mensajes ilimitados</li>
              <li class="li-success">Perfil recomendado a usuarios y clientes</li>
              <li class="li-success">Link de RRSS y URL</li>
              <li class="li-success">Opiniones y valoraciones de clientes</li>
              <li class="li-success">Perfil destacado en Entrenaymas y Google ads/ Facebook Ads</li>
              <li class="li-success">Perfil verificado</li>
              <li class="li-success">Ofertas y descuentos exclusivos de nuestros partners.</li>
              <li class="li-success">Perfil con información completa de servicios, tarifas, descripción, formación, fotos... </li>              
              
            </ul>
            <a href="javascript:perfil_premium()" class="desde_precio_contactar bold font-weight-bolder">Crear perfil Premium</a>
          </div>
        </div>
      </div>
    </div>
    
    <div class="register-form d-none wow fadeInUp" id="register-form" data-wow-duration="0.5s" data-wow-delay="0.5s">
      <div class="d-flex justify-content-end">
        <a href="javascript:cambiarPlan()" class="desde_precio_contactar font-weight-bolder m-0">Cambiar de plan</a>
      </div>
      <ul class="nav nav-tabs style-two">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#Lista"><i class="fa fa-user" aria-hidden="true"></i> Profesional</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#Mapa"><i class="fa fa-map-marker" aria-hidden="true"></i> Centro Especializado</a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="Lista">
          <form onsubmit="return enviar_registro_profesional()">
            <input type="hidden" id="type-subscription" name="subscription" value="" />
            <div class="form-group">
              <label for="registro_profesional">Tipo de Profesional</label>
              <select class="form-control" id="registro_profesional">
                <option value="">Elegir tipo de profesional</option>
                <?php $titulos = $profesional_model->get_titulos(); 
                foreach($titulos as $r) { ?>
                  <option value="<?php echo $r->id ?>"><?php echo $r->nombre ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_nombre">Nombre</label>
                  <input type="text" class="form-control" id="registro_nombre">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_apellido">Apellido</label>
                  <input type="text" class="form-control" id="registro_apellido">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_pais">País</label>
                  <select class="form-control" name="pais" id="registro_pais">
                    <option value="España">España</option>
                  </select>
                </div>
              </div>              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_provincia">Provincia</label>
                  <select onchange="get_localidades()" class="form-control" name="provincia" id="registro_provincia">
                    <option value="0">-</option>
                    <?php $provincias = get_provincias();
                    foreach($provincias as $p) { ?>
                      <option value="<?php echo $p->id ?>"><?php echo $p->nombre ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_localidad">Ciudad</label>
                  <select class="form-control" name="localidad" id="registro_localidad"></select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_direccion">Dirección</label>
                  <input type="text" class="form-control" id="registro_direccion">
                  <!--<span class="mt5 db">No es necesario una dirección exacta.</span>-->
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="registro_telefono">Whatsapp</label>
              <input type="text" class="form-control" id="registro_telefono">
            </div>
            <div class="form-group">
              <label for="registro_email">Correo Electrónico</label>
              <input type="text" class="form-control" id="registro_email">
            </div>
            <div class="form-group">
              <label for="registro_password">Contraseña</label>
              <input type="text" class="form-control" id="registro_password">
            </div>
            <div class="form-group">
              <label for="registro_sobre_mi">Defínete en 150 caracteres</label>
              <textarea class="form-control" rows="6" maxlength="150" id="registro_sobre_mi"></textarea>
            </div>
            <div class="row d-none row-premium">
              <div class="col-md-8">
                <div class="form-group">
                  <label for="cuota">Cuota</label>
                  <select class="form-control" name="cuota" id="cuota">
                    <option value="Mensual">Cuota Mensual (€4.90 por mes)</option>
                    <option value="Anual">Cuota Anual (€49 cada año)</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="cupon-descuento">Cupón de descuento</label>
                  <input type="text" class="form-control cupon-descuento" id="cupon-descuento">
                  <span class="dn mensaje_descuento"></span>                 
                </div>
              </div>
            </div>
            <div class="registro-hp" aria-hidden="true">
              <input type="text" name="direccion_2" id="registro_hp" tabindex="-1" autocomplete="off">
            </div>
            <div class="form-group">
              <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY ?>"></div>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="registro_condiciones" name="example1">
              <label class="custom-control-label" for="registro_condiciones">Acepto los <a target="_blank" href="<?php echo mklink("entrada/terminos-y-condiciones-41119") ?>">términos y condiciones</a>, la <a target="_blank" href="<?php echo mklink("entrada/politica-de-privacidad-41120") ?>">política de privacidad</a> y el tratamiento de mis datos*</label>
            </div>
            <input type="submit" name="Submit" value="Crear Perfil Gratuito" class="psweb-btn envio-form">
          </form>
        </div>
        <div class="tab-pane fade" id="Mapa">
          <form onsubmit="return enviar_registro_centro_medico()">
            <input type="hidden" id="type-subscription" name="subscription" value="" />
            <div class="form-group">
              <label for="registro_centro_actividad">Actividad Principal del Centro</label>
              <select class="form-control" id="registro_centro_actividades">
                <option value="">Elegir</option>
                <?php $titulos = $profesional_model->get_titulos(); 
                foreach($titulos as $r) { ?>
                  <option value="<?php echo $r->id ?>"><?php echo $r->nombre ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="registro_centro_nombre">Nombre del Centro</label>
              <input type="text" class="form-control" id="registro_centro_nombre">
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_centro_pais">País</label>
                  <select class="form-control" name="pais" id="registro_centro_pais">
                    <option value="España">España</option>
                  </select>
                </div>
              </div>              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_centro_provincia">Provincia</label>
                  <select onchange="get_localidades_centro()" class="form-control" name="provincia" id="registro_centro_provincia">
                    <option value="0">-</option>
                    <?php $provincias = get_provincias();
                    foreach($provincias as $p) { ?>
                      <option value="<?php echo $p->id ?>"><?php echo $p->nombre ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_centro_localidad">Ciudad</label>
                  <select class="form-control" name="localidad" id="registro_centro_localidad"></select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="registro_centro_direccion">Dirección</label>
                  <input type="text" class="form-control" id="registro_centro_direccion">
                  <!--<span class="mt5 db">No es necesario una dirección exacta.</span>-->
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="registro_centro_telefono">Whatsapp</label>
              <input type="text" class="form-control" id="registro_centro_telefono">
            </div>
            <div class="form-group">
              <label for="registro_centro_email">Correo Electrónico</label>
              <input type="text" class="form-control" id="registro_centro_email">
            </div>
            <div class="form-group">
              <label for="registro_centro_password">Contraseña</label>
              <input type="text" class="form-control" id="registro_centro_password">
            </div>
            <div class="form-group">
              <label for="registro_centro_numero">Nombre y Apellido de Responsable</label>
              <input type="text" class="form-control" id="registro_centro_numero">
            </div>

            <div class="row d-none row-premium">
              <div class="col-md-8">
                <div class="form-group">
                  <label for="cuota">Cuota</label>
                  <select class="form-control" name="cuota" id="cuota_centro">
                    <option value="Mensual">Cuota Mensual (€4.90 por mes)</option>
                    <option value="Anual">Cuota Anual (€49 cada año)</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="cupon-descuento_centro">Cupón de descuento</label>
                  <input type="text" class="form-control cupon-descuento" id="cupon-descuento_centro">
                  <span class="dn mensaje_descuento"></span>                 
                </div>
              </div>
            </div>

            <div class="registro-hp" aria-hidden="true">
              <input type="text" name="direccion_2" id="registro_centro_hp" tabindex="-1" autocomplete="off">
            </div>
            <div class="form-group">
              <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY ?>"></div>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="registro_centro_condiciones" name="example1">
              <label class="custom-control-label" for="registro_centro_condiciones">Acepto los <a target="_blank" href="<?php echo mklink("entrada/terminos-y-condiciones-41119") ?>">términos y condiciones</a>, la <a target="_blank" href="<?php echo mklink("entrada/politica-de-privacidad-41120") ?>">política de privacidad</a> y el tratamiento de mis datos*</label>
            </div>
            <input type="submit" name="Submit" value="Crear Perfil Gratuito" class="psweb-btn envio-form">
          </form>
        </div>
      </div>
    </div>

    <?php 
    $slider = $web_model->get_slider(array(
      "clave"=>"slider_2",
    ));
    if (sizeof($slider)>0) { ?>
      <div class="owl-carousel mt40" data-items="1" data-margin="0" data-loop="true" data-nav="true" data-dots="false" data-autoplay="true">
        <?php foreach($slider as $r) { ?>
          <div class="item">
            <div class="banner-wrap" style="background-image: url('<?php echo $r->path ?>')">
              <div class="psweb-table-wrap">
                <div class="psweb-align-wrap">
                  <div class="container">
                    <div class="banner-caption">
                      <?php if (!empty($r->linea_1)) { ?>
                        <h3 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s"><?php echo $r->linea_1 ?></h3>
                      <?php } ?>
                      <?php if (!empty($r->linea_2)) { ?>
                        <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s"><?php echo $r->linea_2.(!empty($r->linea_3) ? "<br>$r->linea_3":"") ?></h2>
                      <?php } ?>
                      <?php if (!empty($r->linea_4)) { ?>
                        <p class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s"><?php echo $r->linea_4.(!empty($r->linea_5) ? "<br>$r->linea_5":"") ?></p>
                      <?php } ?>
                      <?php if (!empty($r->link_1)) { ?>
                        <div class="psweb-btn-wrap wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">
                          <a href="<?php echo $r->link_1 ?>" class="psweb-btn"><?php echo $r->texto_link_1 ?></a>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>

  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>

<?php include("includes/paycomet_iframe.php") ?>

<script type="text/javascript">

$(document).ready(function(){
  $(".cupon-descuento").on('keyup', function(e){
    var codigo = $(e.currentTarget).val();
    if (codigo == "") {
      $(".mensaje_descuento").addClass("dn");
      return false;
    }
    $.ajax({
      "url": "/sistema/cupones/function/verificar_cupon",
      "type": "post",
      "dataType": "json",
      "data": {
        "id_empresa": ID_EMPRESA,
        "codigo": codigo,
      }, success:function(r) {
        $(".mensaje_descuento").removeClass("dn");
        if (r.error == 0) {
          $(".mensaje_descuento").removeClass("text-danger");
          $(".mensaje_descuento").addClass("text-success");
        } else {
          $(".mensaje_descuento").removeClass("text-success");
          $(".mensaje_descuento").addClass("text-danger");
        }
        $(".mensaje_descuento").html(r.mensaje);
      },
    });
  });
});

function cambiarPlan() {
  $("#select-subscription").removeClass("d-none");
  $("#register-form").addClass("d-none");
}

function perfil_gratuito() {
  $("#select-subscription").addClass("d-none");
  $("#register-form").removeClass("d-none");
  $("#type-subscription").val("gratuito");
  $("#type-subscription_centro").val("gratuito");
  $(".row-premium").addClass("d-none");
  $("#cuota").val('');
  $(".cupon-descuento").val('');
  $(".envio-form").val("Crear perfil Gratuito");
}

function perfil_premium() {
  $("#select-subscription").addClass("d-none");
  $("#register-form").removeClass("d-none");
  $(".envio-form").val("Crear perfil Premium");
  $(".row-premium").removeClass("d-none");
  $("#type-subscription").val("premium");
  $("#type-subscription_centro").val("premium");
}

function get_localidades() {
  var id_departamento = $("#registro_provincia").val();
  if (id_departamento == 0) return;
  $.ajax({
    "url":"/sistema/localidades/function/get_by_departamento/"+id_departamento,
    "dataType":"json",
    "success":function(r){
      $("#registro_localidad").empty();
      for(var i=0;i<r.results.length;i++) {
        var o = r.results[i];
        var s = "<option value='"+o.id+"'>"+o.nombre+"</option>";
        $("#registro_localidad").append(s);
      }
    }
  })
}

function get_localidades_centro() {
  var id_departamento = $("#registro_centro_provincia").val();
  if (id_departamento == 0) return;
  $.ajax({
    "url":"/sistema/localidades/function/get_by_departamento/"+id_departamento,
    "dataType":"json",
    "success":function(r){
      $("#registro_centro_localidad").empty();
      for(var i=0;i<r.results.length;i++) {
        var o = r.results[i];
        var s = "<option value='"+o.id+"'>"+o.nombre+"</option>";
        $("#registro_centro_localidad").append(s);
      }
    }
  })
}

window.enviado = 0;

function enviar_registro_profesional() {
  var profesional = $("#registro_profesional").val();
  if (isEmpty(profesional)) {
    alert("Por favor seleccione un tipo de profesional.");
    $("#registro_profesional").focus();
    return false;
  }
  var profesional_nombre = $("#registro_profesional option:selected").text();
  var nombre = $("#registro_nombre").val();
  if (isEmpty(nombre)) {
    alert("Por favor ingrese un nombre.");
    $("#registro_nombre").focus();
    return false;
  }
  var apellido = $("#registro_apellido").val();
  if (isEmpty(apellido)) {
    alert("Por favor ingrese un apellido.");
    $("#registro_apellido").focus();
    return false;
  }
  var id_localidad = $("#registro_localidad").val();
  var localidad = $("#registro_localidad option:selected").text();
  if (isEmpty(localidad)) {
    alert("Por favor ingrese la localidad.");
    $("#registro_localidad").focus();
    return false;
  }
  var provincia = $("#registro_provincia option:selected").text();
  var id_provincia = $("#registro_provincia").val();
  var pais = $("#registro_pais").val();

  var direccion = $("#registro_direccion").val();
  if (isEmpty(direccion)) {
    alert("Por favor ingrese la direccion.");
    $("#registro_direccion").focus();
    return false;
  }  

  var telefono = $("#registro_telefono").val();
  if (isEmpty(telefono)) {
    alert("Por favor ingrese su teléfono.");
    $("#registro_telefono").focus();
    return false;
  }
  var email = $("#registro_email").val();
  if (isEmpty(email)) {
    alert("Por favor ingrese su email.");
    $("#registro_email").focus();
    return false;
  }
  var password = $("#registro_password").val();
  if (isEmpty(password)) {
    alert("Por favor ingrese su password.");
    $("#registro_password").focus();
    return false;
  }

  var sobre_mi = $("#registro_sobre_mi").val();
  if (isEmpty(sobre_mi)) {
    alert("Por favor ingrese una breve descripción..");
    $("#registro_sobre_mi").focus();
    return false;
  }
  var subscription = $("#type-subscription").val();
  var cuota = $("#cuota").val();
  var cupon = $("#cupon-descuento").val();

  if (!$("#registro_condiciones").is(":checked")) {
    alert("Por favor acepte los terminos y condiciones.");
    return false;
  }

  var captcha = grecaptcha.getResponse(0);
  if (isEmpty(captcha)) {
    alert("Por favor complete el captcha.");
    return false;
  }

  iniciar_enviar_registro({
    "tipo":"profesional",
    "profesional":profesional,
    "profesional_nombre":profesional_nombre,
    "nombre":nombre+" "+apellido,
    "direccion":direccion,
    "localidad":localidad,
    "id_localidad":id_localidad,
    "id_provincia":id_provincia,
    "provincia":provincia,
    "pais":pais,
    "telefono":telefono,
    "email":email,
    "password":password,
    "numero":"",
    "sobre_mi":sobre_mi,
    "cuota":cuota,
    "cupon":cupon,
    "subscription":subscription,
    "captcha":captcha,
    "captcha_widget":0,
    "hp":$("#registro_hp").val(),
  });

  return false;
}

function iniciar_enviar_registro(o) {
  if (window.enviado == 1) return;
  window.enviado = 1;  
  var buscar = o.direccion+", "+o.localidad+", "+o.provincia+", "+o.pais;
  o.latitud = 0;
  o.longitud = 0;
  enviar_registro(o);
  return false;
}

function enviar_registro(o) {
  $.ajax({
    "url":"/sistema/registro/entrenaymas/",
    "dataType":"json",
    "type":"post",
    "data":{
      "tipo":o.tipo,
      "profesional":o.profesional,
      "nombre":o.nombre,
      "direccion":o.direccion,
      "localidad":o.localidad,
      "id_localidad":o.id_localidad,
      "provincia":o.provincia,
      "id_provincia":o.id_provincia,
      "pais":o.pais,
      "telefono":o.telefono,
      "email":o.email,
      "password":o.password,
      "latitud":o.latitud,
      "longitud":o.longitud,
      "sobre_mi": o.sobre_mi,
      "cuota": o.cuota,
      "cupon": o.cupon,
      "subscription": o.subscription,
      "g-recaptcha-response": o.captcha,
      "hp": o.hp,
    },
    "success":function(r) {
      window.enviado = 0;
      if (r.error != 0) {
        grecaptcha.reset(o.captcha_widget);
        alert(r.mensaje);
        return;
      }

      if (o.subscription == "premium") {

        // Es un registro Premium
        localStorage.setItem("info_user",JSON.stringify(o));

        var periodicity = 365;
        if (o.cuota == "Mensual") periodicity = 30;

        // ABRIMOS EL IFRAME DE PAYCOMET
        openIframePaycomet(periodicity, r.id);

      } else {
        // Es un registro gratuito        
        alert("Hemos enviado su registro correctamente. Pronto le llegara un email con la confirmación del mismo. Muchas gracias!");
        location.href = "<?php echo mklink("/") ?>";
      }
    },
    "error":function(){
      alert("Ocurrio un error al enviar el registro. Por favor intente nuevamente.");
      grecaptcha.reset(o.captcha_widget);
      window.enviado = 0;
    }
  });
}

function enviar_registro_centro_medico() {
  var profesional = $("#registro_centro_actividades").val();
  if (isEmpty(profesional)) {
    alert("Por favor seleccione un la actividad principal.");
    $("#registro_centro_actividades").focus();
    return false;
  }
  var nombre = $("#registro_centro_nombre").val();
  if (isEmpty(nombre)) {
    alert("Por favor ingrese un nombre.");
    $("#registro_centro_nombre").focus();
    return false;
  }
  var direccion = $("#registro_centro_direccion").val();
  if (isEmpty(direccion)) {
    alert("Por favor ingrese la direccion.");
    $("#registro_centro_direccion").focus();
    return false;
  }  
  var localidad = $("#registro_centro_localidad option:selected").text();
  var id_localidad = $("#registro_centro_localidad").val();
  if (isEmpty(localidad)) {
    alert("Por favor ingrese la localidad.");
    $("#registro_centro_localidad").focus();
    return false;
  }
  var id_provincia = $("#registro_centro_provincia").val();
  var provincia = $("#registro_centro_provincia option:selected").text();
  var pais = $("#registro_centro_pais").val();

  var telefono = $("#registro_centro_telefono").val();
  if (isEmpty(telefono)) {
    alert("Por favor ingrese su telefono.");
    $("#registro_centro_telefono").focus();
    return false;
  }
  var email = $("#registro_centro_email").val();
  if (isEmpty(email)) {
    alert("Por favor ingrese su email.");
    $("#registro_centro_email").focus();
    return false;
  }
  var password = $("#registro_centro_password").val();
  if (isEmpty(password)) {
    alert("Por favor ingrese su password.");
    $("#registro_centro_password").focus();
    return false;
  }

  if (!$("#registro_centro_condiciones").is(":checked")) {
    alert("Por favor acepte los terminos y condiciones.");
    return false;
  }

  var subscription = $("#type-subscription_centro").val();
  var cuota = $("#cuota_centro").val();
  var cupon = $("#cupon-descuento_centro").val();

  var captcha = grecaptcha.getResponse(1);
  if (isEmpty(captcha)) {
    alert("Por favor complete el captcha.");
    return false;
  }

  iniciar_enviar_registro({
    "tipo":"gimnasio",
    "profesional":profesional,
    "nombre":nombre,
    "direccion":direccion,
    "localidad":localidad,
    "id_localidad":id_localidad,
    "id_provincia":id_provincia,
    "provincia":provincia,
    "pais":pais,
    "telefono":telefono,
    "email":email,
    "password":password,
    "sobre_mi": "",
    "cuota":cuota,
    "cupon":cupon,
    "subscription":subscription,
    "captcha":captcha,
    "captcha_widget":1,
    "hp":$("#registro_centro_hp").val(),
  });
  return false;
}
</script>

</body>
</html>