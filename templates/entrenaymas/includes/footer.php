<footer class="psweb-footer">
  <div class="container">
    <div class="row">
      <div class="col-lg-2">
        <a href="<?php echo mklink("/") ?>" class="footer-logo">
          <?php if ($marca_blanca == "marca") { ?>
            <img src="assets/images/marca2.png" alt="<?php echo $empresa->nombre ?>">
          <?php } else if ($marca_blanca == "tmr-world-shop") { ?>
            <img src="assets/images/tmr-world-shop.png" alt="<?php echo $empresa->nombre ?>">
          <?php } else { ?>
            <img src="assets/images/Y.png" alt="<?php echo $empresa->nombre ?>">
          <?php } ?>
        </a>
        <p><?php echo $empresa->direccion ?></p>
      </div>
      <div class="col-lg-8">
        <div class="row">
          <div class="col-md-6">
            <h4>Información</h4>
            <div class="row">
              <?php $ayuda = $entrada_model->get_list(array(
                "id_categoria"=>1486
              )); 
              $m = ceil(sizeof($ayuda)/2);
              $mitad1 = array(); $mitad2 = array();
              for($i=0;$i<$m;$i++) {
                $a = $ayuda[$i];
                $mitad1[] = $a;
              } 
              for($i=$m;$i<sizeof($ayuda);$i++) {
                $a = $ayuda[$i];
                $mitad2[] = $a;
              } 
              if (sizeof($mitad1)>0) { ?>
                <div class="col-md-6">
                  <ul>
                    <?php foreach($mitad1 as $a) { ?>
                      <li><a href="<?php echo mklink($a->link) ?>"><?php echo $a->titulo ?></a></li>
                    <?php } ?>
                  </ul>
                </div>
              <?php }
              if (sizeof($mitad2)>0) { ?>
                <div class="col-md-6">
                  <ul>
                    <?php foreach($mitad2 as $a) { ?>
                      <li><a href="<?php echo mklink($a->link) ?>"><?php echo $a->titulo ?></a></li>
                    <?php } ?>
                  </ul>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-6">
            <h4>Profesionales</h4>
            <div class="row">
              <div class="col-md-6">
                <ul>
                  <li><a href="<?php echo mklink("profesionales/") ?>">Ver Todos</a></li>
                  <?php //<li><a href="<?php echo mklink("web/planes/")">Planes de Precio</a></li> ?>
                  <li><a href="<?php echo mklink("entradas/blog/") ?>">Blog</a></li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul>
                  <li><a href="<?php echo mklink("web/activacion/") ?>">Activar Caja</a></li>
                  <li><a href="<?php echo mklink("web/registro/") ?>">Registrate</a></li>
                  <li><a href="/sistema/" target="_blank">Login</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div>
          <h4>Entrenaymas</h4>
          <ul>
            <li>B01706290</li>
            <li>C/ Duque de Sesto 11, 28009, Madrid. España</li>
            <li>(+34) 641522483</li>
            <li><a href="mailto:info@entrenaymas.com">info@entrenaymas.com</a></li>
          </ul>
        </div>
        <div class="psweb-social rounded">
          <?php if (!empty($empresa->facebook)) { ?>
            <a href="<?php echo $empresa->facebook ?>" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>
          <?php } ?>
          <?php if (!empty($empresa->instagram)) { ?>
            <a href="<?php echo $empresa->instagram ?>" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a>
          <?php } ?>
          <?php if (!empty($empresa->linkedin)) { ?>
            <a href="<?php echo $empresa->linkedin ?>" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
          <?php } ?>
        </div>        
      </div>
    </div>
  </div>
</footer>

<section class="psweb-copyright">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <p>
          <?php if (empty($marca_blanca)) { ?>
            <span>
              <a href="<?php echo mklink("/") ?>"><?php echo $empresa->nombre ?></a> 
              <script type="text/javascript">document.write(new Date().getFullYear())</script>.
            </span>
          <?php } ?> 
          Todos Los Derechos Reservados
        </p>
      </div>
      <div class="col-md-6 textright">
        <p>
          <span class="design_by">Diseño y Desarrollo Web </span>
          <a target="_blank" href="https://www.misticastudio.com"><img src="assets/images/logo3.png" alt="Mistica Studio"></a>
        </p>
      </div>
    </div>
  </div>
</section>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/html5.min.js"></script>
<script src="assets/js/respond.min.js"></script>
<script src="assets/js/placeholders.min.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/magnific-popup-min.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/star_rating.js"></script>
<script src="/sistema/resources/js/md5.js"></script>
<script src="/sistema/resources/js/js.cookie.js"></script>
<script src="/sistema/resources/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/sistema/resources/js/jquery/ui/jquery-ui.min.css">
<script src="/sistema/resources/js/jquery/ui/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script type="text/javascript">
  $(document).ready(function(){
    $("#searchform_localidades").select2();
  });
</script>


<script type="text/javascript">
/*
function mostrar_recuperar_password() {
  $("#form_login").hide();
  $("#form_recuperar").show();
}
function mostrar_login() {
  $("#form_recuperar").hide();
  $("#form_login").show();
}

function recuperar_password() {
  var email = $("#varcreative_recuperar_email").val();
  if (isEmpty(email)) {
    alert("Por favor ingrese su email");
    $("#varcreative_recuperar_email").focus();
    return false;
  }
  $.ajax({
    "url":"/sistema/clientes/function/recuperar_pass/",
    "dataType":"json",
    "type":"post",
    "data":{
      "id_empresa":ID_EMPRESA,
      "email":email,
    },
    "success":function(r) {
      alert(r.mensaje);
    },
  });
  return false;
}

function validar_login() {
  var email = $("#varcreative_login_email").val();
  var password = $("#varcreative_login_password").val();
  if (isEmpty(email)) {
    alert("Por favor ingrese su email");
    $("#varcreative_login_email").focus();
    return false;
  }
  if (isEmpty(password)) {
    alert("Por favor ingrese su password");
    $("#varcreative_login_password").focus();
    return false;
  }
  return varcreative_login({
    "email":email,
    "password":password,
  });
}
*/
</script>

<script type="text/javascript">
// Redirige a la busqueda de mapa
function buscar_mapa() {
  var url = "<?php echo mklink("mapa/") ?>";
  url += get_parameters();
  location.href = url;
}

// Redirige a la busqueda por listado
function buscar_listado() {
  var url = "<?php echo mklink("profesionales/") ?>";
  url += get_parameters();
  location.href = url;
}

function get_parameters() {
  var titulo = $("#searchform_titulos").val();
  var localidad = $("#searchform_localidades").val();

  // TIPOS DE PERFIL
  var tipos_perfil = new Array();
  $(".tipo_perfil:checked").each(function(i,e){
    tipos_perfil.push($(e).val());
  });

  // TIPO DE TERAPIA
  var tipos_terapias = new Array();
  $(".tipo_terapia:checked").each(function(i,e){
    tipos_terapias.push($(e).val());
  });
  // TIPO DE ATENCION
  var tipos_atenciones = new Array();
  $(".tipo_atencion:checked").each(function(i,e){
    tipos_atenciones.push($(e).val());
  });
  // TEMATICA
  var categorias = new Array();
  $(".categoria:checked").each(function(i,e){
    categorias.push($(e).val());
  });
  // FORMA DE PAGO
  var formas_pago = new Array();
  $(".forma_pago:checked").each(function(i,e){
    formas_pago.push($(e).val());
  });
  var url = "";
  url += "?tt="+tipos_terapias.join("-");
  url += "&ta="+tipos_atenciones.join("-");
  url += "&cat="+categorias.join("-");
  url += "&fp="+formas_pago.join("-");
  url += "&tp="+tipos_perfil.join("-");
  url += "&locs="+localidad;
  url += "&tit="+titulo;
  return url;
}




</script>

<?php include("templates/comun/vc_login.php") ?>
<?php include("templates/comun/carrito_js.php"); ?>
<?php 
include("templates/comun/cookies.php"); 
cookies(array(
  "link_politicas"=>mklink("entrada/politica-de-privacidad-41120"),
));
?>