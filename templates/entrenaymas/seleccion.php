<?php
include("includes/init.php");
$nombre_pagina = "seleccion";
if (isset($_POST["codigo_activacion"])){
  $codigo = $_POST["codigo_activacion"];
  $profesionales = $profesional_model->get_profesionales_from_articulo($codigo);
  if ($profesionales == false){
    header("Location: ".mklink('web/activacion/?e=1'));
    exit();
  }
} else {
  header('Location: www.entrenaymas.com');
  exit();
}
$articulo = $profesional_model->get_articulo_from_codigo($codigo);
$categorias = $articulo_model->get_categorias_entrena($articulo->id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php") ?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/css/paquete.css">
<style type="text/css">
.descripcion p { text-align: left !important; }
.activacion-wrapper { width: 800px; }
input[name=radioprofesional]:checked + .doctor-item { background-color: var(--c2) }
input[name=radioprofesional] + .doctor-item { padding: 10px; }
input[name=radioprofesional] + .doctor-item p { padding-bottom: 0px; }
input[name=radioprofesional] + .doctor-item .psweb-image { padding-bottom: 0px; }
</style>
</head>
<body>

<?php include("includes/header.php") ?>

<!-- Psweb Page Title -->
<section class="psweb-page-title">
  <div class="container">
    <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">Paquetes</h2>
    <ul class="breadcrumb wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
      <li><a href="<?php echo mklink("/") ?>">Home</a></li>
      <li><i class="fs14 ml10 mr10 fa fa-chevron-right"></i></li>
      <li>Paquetes</li>
    </ul>
  </div>
</section>
<!-- Psweb Blog Wrap -->
<section class="psweb-blog-wrap">
  <div class="container mt30">

    <div class="activacion-wrapper seleccion">
      <h3>¡Tu caja fue activada con éxito!</h3>
      <p class="seleccion_datos" style="font-size: 18px;"><?php echo $articulo->nombre ?></p>
      <hr class="mt10 mb10">
      <p class="mb5">Puedes elegir entre: </p>
      <div class="descripcion">
        <?php echo $articulo->texto ?>
      </div>
      <!--
      <p class="seleccion_datos">4 entrenamientos personales u 8 entrenamientos personales online</p>
      <p class="seleccion_datos">3 Tratamientos de fisioterapia</p>
      <p class="seleccion_datos">1 Plan nutricional + 2 revisiones o Plan nutricional + 4 revisiones online</p>
      <p class="seleccion_datos">De 1 a 3 meses en centros deportivos</p>
    -->
      <hr class="mt15 mb15">
      <span class="registra_caja db">ARMA TU CAJA</span> 
      <p class="mt15 text-left">Elije al profesional</p>
      <div id="accordion" class="tal">
        <?php foreach ($categorias as $c) { ?>
        <div class="card">
          <div class="card-header" id="heading<?php echo $c->id; ?>">
            <h5 class="mb-0">
              <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?php echo $c->id; ?>" aria-expanded="true" aria-controls="collapse<?php echo $c->id; ?>">
                  <i style="width: 5%;" class="fa fa-plus"></i>
                  <span class="ml20" style="width: 75%;"><?php echo $c->nombre;?><span>
              </button>
            </h5>
          </div>

          <div id="collapse<?php echo $c->id; ?>" class="collapse" aria-labelledby="heading<?php echo $c->id; ?>" data-parent="#accordion">
            <div class="card-body">
              <?php foreach ($profesionales as $p) { 
                if ($c->id_categoria == $p->id_categoria) { 
                  $a = $profesional = $usuario_model->get($p->id);
                  $link = mklink("profesional/".$p->apellido."-".$p->id."/"); ?>
                  <div class="">
                    <input style="display: none" name="radioprofesional" data-id="<?php echo $p->id;?>" type="radio" id="check<?php echo $p->id ?>" class="checkboxprofesionales"/>
                    <label class="doctor-item" style="max-width: 100%;" for="check<?php echo $p->id ?>">
                      <?php foreach($a->tipos_atenciones as $ta) { ?>
                        <?php if ($ta->nombre == "Online") { ?>
                          <div class="selec-detalle">
                            <span class="atencion_en_linea" data-toggle="tooltip" data-placement="bottom" title="Atención en linea">
                              <img src="assets/images/atencion.png" alt="atencion" />
                            </span>
                          </div>
                        <?php } ?>
                      <?php } ?>
                      <div class="dt">
                        <div class="dtc vam">
                          <div class="psweb-image">
                            <a href="<?php echo $link ?>" target="_blank">
                              <?php if (!empty($p->path)) { ?>
                                <img width="70" height="70" src="https://entrenaymas.com/sistema/<?php echo $p->path ?>" alt="<?php echo ($p->nombre);?>">
                              <?php } else if (!empty($empresa->no_imagen)) { ?>
                                <img width="70" height="70" src="/sistema/<?php echo $empresa->no_imagen ?>" alt="<?php echo ($p->nombre);?>">
                              <?php } else { ?>
                                <img width="70" height="70" src="images/no-imagen.png" alt="<?php echo ($p->nombre);?>">
                              <?php } ?>
                            </a>
                          </div>
                        </div>
                        <div class="selec-info">
                          <?php if (sizeof($a->titulos)>0) { ?>
                            <div class="oh pl15">
                              <?php foreach($a->titulos as $titulo) { ?>
                                <span class="doctor-designation fl flntel mr10"><?php echo $titulo->nombre ?></span>
                              <?php } ?>
                            </div>
                          <?php } ?>
                          <p class="pl15"><?php echo $p->nombre; ?></p>
                          <?php if (!empty($a->direccion)) { ?>
                            <div class="pl15 direccion">
                              <span><i class="fa fa-telegram" aria-hidden="true"></i> <?php echo "Direccion: ".$a->direccion;?></span>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                      <h5 class="informacion-linea">
                      <?php
                      if (sizeof($a->tipos_terapias)>0 || sizeof($a->tipos_atenciones)>0 || sizeof($a->formas_pago)>0 || sizeof($a->localidades)>0) { ?>
                        <div class="mt20 profesionales_seleccion_detalle">
                          <?php if (sizeof($a->categorias)>0) { ?>
                              <img src="assets/images/icon36.png" alt="User"> 
                              Especialidades: 
                              <?php foreach($a->categorias as $t) { ?>
                                <span class="mr5"><?php echo $t->nombre ?>.</span>
                              <?php } ?></br>
                          <?php } ?>                
                          <?php if (sizeof($a->tipos_terapias)>0) { ?>
                              <img src="assets/images/icon36.png" alt="User"> 
                              Tipos de Entrenamiento: 
                              <?php foreach($a->tipos_terapias as $t) { ?>
                                <span class="mr5"><?php echo $t->nombre ?>.</span>
                              <?php } ?></br>
                          <?php } ?>
                          <?php if (sizeof($a->tipos_atenciones)>0) { ?>               
                              <img src="assets/images/icono-atencion.png" alt="User"> 
                              Tipo de Atención: 
                              <?php foreach($a->tipos_atenciones as $t) { ?>
                                <span class="mr5"><?php echo $t->nombre ?>.</span>
                              <?php } ?></br>
                          <?php } ?>
                          <?php if (sizeof($a->localidades)>0) { ?>
                              <img src="assets/images/icon36.png" alt="User"> 
                              Ciudades: 
                              <?php foreach($a->localidades as $t) { ?>
                                <span class="mr5"><?php echo $t->localidad ?>.</span>
                              <?php } ?></br>
                          <?php } ?>
                        </div>
                      <?php } ?>
                    </label>
                  </div>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
      <hr class="mt30 mb10">
      <label for="cliente_nombre" class="tal">Nombre</label>
      <input type="text" class="form-control" style="max-width: 100%;" id="cliente_nombre">
      <label for="cliente_email" class="tal mt10">Email</label>
      <input type="email" class="form-control" style="max-width: 100%;" id="cliente_email">
      <a onclick="verificar_profesional()" class="activacion_continuar db mt30">Continuar</a>
    </div>

  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>
<script type="text/javascript">
function verificar_profesional(){
  var p = $(".checkboxprofesionales:checked").length;
  var email = $("#cliente_email").val();
  var nombre = $("#cliente_nombre").val();
  if (p == 0){
    alert ("Por favor seleccione a un profesional");
    return false;
  }
  if (nombre == ""){
    alert ("Por favor ingrese su nombre");
    $("#cliente_nombre").focus();
    return false;
  }
  if (email == ""){
    alert ("Por favor ingrese su email");
    $("#cliente_email").focus();
    return false;
  }
  var id_profesional = $(".checkboxprofesionales:checked").data("id");
  $.ajax({
    "url":"/sistema/usuarios/function/activar_caja/",
    "type":"post",
    "dataType":"json",
    "data":{
      "id_profesional":id_profesional,
      "codigo": "<?php echo $codigo ?>",
      "email": email,
      "nombre": nombre,
    },
    "success":function(r){
      if (r.error == 0) {
        alert("Hemos enviado a tu mail un código QR que debes presentar al profesional para validar tu caja regalo.");
        window.location.reload();
      }
    }
  });
}

function controlar_cantidad_seleccionada(e){
  var cantidad_seleccionable_maxima = 1;
  if (cantidad_seleccionable_maxima > 0){
    // Tomamos la cantidad de checkboxes seleccionables en total
    var cantidad_sel = $(".checkboxprofesionales:checked").length;
    if (cantidad_sel > cantidad_seleccionable_maxima) {
      $(e).removeAttr("checked");
    }
  }
}
</script>
</body>
</html>