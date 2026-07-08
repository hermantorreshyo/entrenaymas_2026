<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("includes/init.php");
$profesional = $usuario_model->get($id);

$categorias_profe = array();
foreach ($profesional->categorias as $cat) {
  $categorias_profe[] = $cat->id;
}

$localidades_busqueda = array();
foreach($profesional->localidades as $ll) { 
  $localidades_busqueda[] = $ll->id_localidad;
}

//$localidades_busqueda = implode("-", $localidades_busqueda);


$profesionales_relacionados = $usuario_model->get_list(array(
  "categorias"=>$categorias_profe,
  "not_ids"=>$id,
  "offset"=>3,
  "limit"=>0,
  "order_by"=>"RAND() ASC",
  "localidades"=>$localidades_busqueda,
));

if (sizeof($profesionales_relacionados) <= 2) {
  $profesionales_relacionados = $usuario_model->get_list(array(
    "not_ids"=>$id,
    "offset"=>3,
    "limit"=>0,
    "order_by"=>"RAND() ASC",
    "localidades"=>$localidades_busqueda,
  ));
}

if (sizeof($profesionales_relacionados) <= 2) {
  $profesionales_relacionados = $usuario_model->get_list(array(
    "not_ids"=>$id,
    "offset"=>3,
    "limit"=>0,
    "order_by"=>"RAND() ASC",
  ));
}

$nombre_pagina = "profesionales";
$funcion_buscar = "buscar_listado";
if (isset($profesional->seo_title) && !empty($profesional->seo_title)) $seo_title = utf8_decode($profesional->seo_title);
if (isset($profesional->seo_description) && !empty($profesional->seo_description)) $seo_description = utf8_decode(str_replace("\n", "", strip_tags($profesional->seo_description)));

// Consultamos la tarifa
$valor = 0.01;
if (isset($_SESSION['vc_localidades'])) {
  $loc = $_SESSION['vc_localidades'];
  foreach($profesional->localidades as $ll) {
    if ($ll->id_localidad == $loc) {
      $valor = $ll->valor;
      break;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php") ?>
<link rel="canonical" href="https://entrenaymas.com/<?php echo ("profesional/".$profesional->apellido."-".$profesional->id."/"); ?>" />
<style type="text/css">
.clienapp_boton {font-family: 'Quicksand', sans-serif !important; font-weight: 700 !important} 
</style>
</head>
<body class="profesional_detalle">

<?php include("includes/header.php") ?>

<!-- Psweb Search, Search Style Two, Search Spacer Three -->
<section class="psweb-search search-style-two search-spacer-three">
  <div class="container">
    <div class="search-wrap">
      <div class="search-form">
        <?php include("includes/searchform.php"); ?>
      </div>
    </div>
  </div>
</section>

<!-- Psweb Portal Details -->
<section class="psweb-portal-details">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <ul class="doctor-meta">
          <li><a href="<?php echo mklink("/") ?>">Inicio</a></li>
          <li><a href="<?php echo mklink("profesionales/") ?>">Profesionales</a></li>
          <!--<li><a href="#0">Angustias, Estrés y Bulling</a></li>-->
          <li><a href="javascript:void(0)"><?php echo ucwords(mb_strtolower($profesional->nombre)) ?></a></li>
        </ul>
      </div>
      <div class="col-lg-4 text-lg-right">
        <a href="javascript:history.back()" class="doctor-meta-link">VOLVER A RESULTADOS</a>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8">
        <div class="doctor-style-two doctor-style-three">
          <div class="doctor-item">
            <div class="doctor-quote"><i class="fa fa-bookmark" aria-hidden="true"></i></div>

            <?php if (estaEnFavoritos($profesional->id)) { ?>
              <a class="like-icon added" data-bookmark-state="" rel="nofollow" href="/sistema/favoritos/eliminar/?id=<?php echo $profesional->id; ?>">
                <i class="fa fa-heart" aria-hidden="true"></i>
              </a>
            <?php } else { ?>
              <a class="like-icon" rel="nofollow" href="/sistema/favoritos/agregar/?id=<?php echo $profesional->id; ?>">
                <i class="fa fa-heart-o" aria-hidden="true"></i>
              </a>
            <?php } ?>
            <?php foreach($profesional->tipos_atenciones as $ta) { ?>
              <?php if ($ta->nombre == "Online") { ?>
                <div class="detalle">
                  <span class="atencion_en_linea" data-toggle="tooltip" data-placement="bottom" title="Atención en linea">
                    <img src="assets/images/atencion.png" alt="atencion" />
                  </span>
                </div>
              <?php } ?>
            <?php } ?>


            <div class="psweb-image detail-img">
              <?php if (!empty($profesional->path)) { ?>
                <img src="<?php echo $profesional->path ?>" alt="<?php echo ($profesional->nombre);?>">
              <?php } else if (!empty($empresa->no_imagen)) { ?>
                <img src="/sistema/<?php echo $empresa->no_imagen ?>" alt="<?php echo ($profesional->nombre);?>">
              <?php } else { ?>
                <img src="images/no-imagen.png" alt="<?php echo ($profesional->nombre);?>">
              <?php } ?>

              <?php if ($profesional->destacado == 1) { ?>
                <span class="destacado" data-toggle="tooltip" data-placement="bottom" title="Perfil Verificado"><i class="fa fa-check"></i></span>
              <?php } ?>

            </div>
            
            <div class="doctor-info">
              <?php 
                $sql = "SELECT texto, fecha, votos_positivos, nombre FROM not_entradas_comentarios WHERE id_entrada = $id ORDER BY fecha desc";
                $res = mysqli_query($conx, $sql);
                if (mysqli_num_rows($res)>0){?>
                <?php 
                  $prom = "SELECT ROUND (AVG(votos_positivos),0) as prom from not_entradas_comentarios WHERE id_entrada = $id";
                  $result = mysqli_query($conx, $prom);
                  $promedio = mysqli_fetch_object($result);
                ?>
                  <div>
                    <?php for ($i=0; $i < $promedio->prom; $i++) { ?>
                      <span class="fa fa-star checked"></span>
                    <?php } ?>
                    <?php for ($i=$promedio->prom; $i < 5; $i++) { ?>
                      <span class="fa fa-star"></span>
                    <?php } ?>
                    <span>(De <?php echo mysqli_num_rows($res);?> comentarios)</span>
                  </div>
                <?php } ?>
              <?php if (sizeof($profesional->titulos)>0) { ?>
                <div class="oh">
                  <?php foreach($profesional->titulos as $titulo) { ?>
                    <h1 class="doctor-designation dib mr10"><?php echo $titulo->nombre ?></h1>
                  <?php } ?>
                </div>
              <?php } ?>
              <h4 style="text-transform: capitalize;"><?php echo ucwords(mb_strtolower($profesional->nombre)) ?></h4>
              <?php if (!empty($profesional->cargo)) { ?>
                <h5>Número Colegiado: <?php echo $profesional->cargo ?></h5>
              <?php } ?>
              <?php if (!empty($profesional->direccion)) { ?>
                <div class="direccion">
                  <span><i class="fa fa-telegram" aria-hidden="true"></i> <?php echo "Direccion: ".$profesional->direccion;?></span>
                </div>
              <?php } ?>
              <?php if (!empty($profesional->celular)) { ?>
                <?php if ($profesional->tiene_consulta_mensual == 0) { ?>
                  <div class="whatsapp_cont">
                    <a href="javascript:void(0)" onclick="render_modal_clienapp()" >
                      <div class="whatsapp">
                        <span><i class="fa fa-whatsapp" aria-hidden="true"></i> ENVIAR WHATSAPP</span>
                      </div>
                    </a>
                  </div>
                <?php } ?>
              <?php } ?>

            </div>
          </div>
        </div>

        <?php if (!empty($profesional->custom_3) || !empty($profesional->custom_4)) { ?>
          <div class="mid-item">
            <h3 class="active">Experiencia</h3>
            <div class="mid-item-info active">
              <div class="experience">
                <?php if (!empty($profesional->custom_3)) { ?>
                  <div class="experience-item">
                    <h5><img src="assets/images/icon38.png" alt="User"> Sobre Mí</h5>
                    <p><?php echo nl2br($profesional->custom_3) ?></p>
                    <!--<a href="#0" class="experience-link">Ver más</a>-->
                  </div>
                <?php } ?>
                <?php if (!empty($profesional->custom_4)) { ?>
                  <div class="experience-item">
                    <h5><img src="assets/images/icon39.png" alt="User"> Formación Académica</h5>
                    <p><?php echo nl2br($profesional->custom_4) ?></p>
                    <!--<a href="#0" class="experience-link">Ver más</a>-->
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php } ?>

        <?php if (sizeof($profesional->direcciones)>0) { ?>
          <div class="mid-item">
            <h3>Tarifas</h3>
            <div class="mid-item-info">
              <?php foreach($profesional->direcciones as $dir) { ?>
                <div class="loaction-item">
                  <div class="row align-items-center tarifas">
                    <div class="col-md-1 bcb">
                      <i class="fa fa-ticket" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-5 col-sm-4 col-12 ">
                      <h5 style="padding-top: 10px;"><a data-to="turnoclick_servicio_<?php echo $dir->id ?>" class="scrollTo" href="javascript:void(0)"><?php echo $dir->nombre ?></a></h5>
                    </div>
                    <div class="col-md-4 col-sm-4 col-8 pr0">
                      <div class="direccion-info">
                        <h4 style="padding-top: 5px;"><i class="fa fa-clock-o" aria-hidden="true"></i> <b>Duración:</b>
                        <?php if (!empty($dir->duracion_turno)) { ?>
                          <span class="duracion_minutos"> <?php echo $dir->duracion_turno ?> minutos</span>
                        <?php } else {  ?>
                          <span> - </span>
                        <?php } ?>
                        </h4>
                      </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-4 pl0">
                      <div class="tarifas_costo">
                        <?php if (!empty($dir->costo)) { ?>
                          <span><?php echo $dir->costo ?> <i class="fa fa-eur" aria-hidden="true"></i></span>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        <?php } ?>

        <?php if (!empty($profesional->email)) { ?>
          <div class="mid-item">
            <h3>Comunicate con <?php echo $profesional->nombre ?></h3>
            <div class="mid-item-info">
              <div class="loaction-item">
                <form onsubmit="return enviar_contacto()">
                  <div class="form-group">
                    <input type="text" class="form-control" id="contacto_nombre" placeholder="Nombre Completo">
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="email" class="form-control" id="contacto_email" placeholder="Email">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-5 pr0">
                            <select id="contacto_prefijo" class="form-control">
                              <?php include("includes/paises.php"); ?>
                            </select>
                          </div>
                          <div class="col-7">
                            <input type="tel" class="form-control" id="contacto_telefono" placeholder="WhatsApp/Tel">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <textarea class="form-control" id="contacto_mensaje" placeholder="Consulta al profesional"></textarea>
                  <div class="custom-control custom-checkbox mt20">
                    <input type="checkbox" class="custom-control-input" id="comunicate_condiciones" name="example1">
                    <label class="custom-control-label" for="comunicate_condiciones">Acepto los <a target="_blank" href="<?php echo mklink("entrada/terminos-y-condiciones-41119") ?>">términos y condiciones</a>, la <a target="_blank" href="<?php echo mklink("entrada/politica-de-privacidad-41120") ?>">política de privacidad</a> y el tratamiento de mis datos*</label>
                  </div>
                  <button id="contacto_submit" class="psweb-btn psweb-medium-btn border-btn mt15">Enviar</button>
                </form>
              </div>
              <div class="tac">
                <?php if ($profesional->destacado == 1) { ?>

                  <?php if (!empty($profesional->custom_5)) { ?>
                    <button class="click_red" data-ref="web" data-href="<?php echo $profesional->custom_5 ?>"> <i class="fa fa-globe" aria-hidden="true"></i> </button>
                  <?php } ?>

                  <?php if (!empty($profesional->facebook)) { ?>
                    <button class="click_red" data-ref="facebook" data-href="<?php echo $profesional->facebook ?>"> <i class="fa fa-facebook" aria-hidden="true"></i> </button>
                  <?php } ?>

                  <?php if (!empty($profesional->instagram)) { ?>
                    <button class="click_red" data-ref="instagram" data-href="<?php echo $profesional->instagram ?>"> <i class="fa fa-instagram" aria-hidden="true"></i> </button>
                  <?php } ?>

                  <?php if (!empty($profesional->linkedin)) { ?>
                    <button class="click_red" data-ref="twitter" data-href="<?php echo $profesional->linkedin ?>"> <i class="fa fa-twitter" aria-hidden="true"></i> </button>
                  <?php } ?>

                <?php } ?>
              </div>
            </div>
          </div>
        <?php } ?>

        <?php if (sizeof($profesional->images)>0) { ?>
          <div class="mid-item">
            <h3>Galería de Fotos</h3>
            <div class="mid-item-info">
              <div class="gallery">
                <div class="row">
                  <?php foreach($profesional->images as $img) { ?>
                    <div class="gallery-item">
                      <div class="psweb-popup">
                        <a href="<?php echo $img ?>">
                          <img src="<?php echo $img ?>" alt="<?php echo $profesional->nombre ?>">
                        </a>
                      </div>
                    </div>
                  <?php } /* ?>
                  <div class="gallery-item">
                    <img src="assets/images/gallery2.jpg" alt="Gallery">
                  </div>
                  <div class="gallery-item">
                    <img src="assets/images/gallery3.jpg" alt="Gallery">
                  </div>
                  <div class="gallery-item">
                    <img src="assets/images/gallery4.jpg" alt="Gallery">
                  </div>
                  <div class="gallery-item">
                    <a href="#0" class="more-gallery"><span>+3</span></a>
                  </div>
                  */ ?>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
        <?php if (sizeof($profesional->tipos_terapias)>0 || sizeof($profesional->tipos_atenciones)>0 || sizeof($profesional->formas_pago)>0) { ?>
          <div class="mid-item">
            <h3 class="active">Información</h3>
            <div class="mid-item-info">
              <div class="doctor-meta-wrap">
                <?php if (sizeof($profesional->categorias)>0) { ?>
                  <h5 class="informacion-linea">
                    <div class="dt w100p">
                      <div class="dtc w30">
                        <img src="assets/images/icon36.png" alt="User"> 
                      </div>
                      <div class="dtc">
                        Especialidades: 
                        <?php foreach($profesional->categorias as $t) { ?>
                          <span class="mr5"><?php echo $t->nombre ?>.</span>
                        <?php } ?>
                      </div>
                    </div>
                  </h5>
                <?php } ?>                
                <?php if (sizeof($profesional->tipos_terapias)>0) { ?>
                  <h5 class="informacion-linea">
                    <div class="dt w100p">
                      <div class="dtc w30">                    
                        <img src="assets/images/icon36.png" alt="User"> 
                      </div>
                      <div class="dtc">
                        Tipos de Entrenamiento: 
                        <?php foreach($profesional->tipos_terapias as $t) { ?>
                          <span class="mr5"><?php echo $t->nombre ?>.</span>
                        <?php } ?>
                      </div>
                    </div>
                  </h5>
                <?php } ?>
                <?php if (sizeof($profesional->tipos_atenciones)>0) { ?>
                  <h5 class="informacion-linea">
                    <div class="dt w100p">
                      <div class="dtc w30">                    
                        <img src="assets/images/icono-atencion.png" alt="User"> 
                      </div>
                      <div class="dtc">
                        Tipo de Atención: 
                        <?php foreach($profesional->tipos_atenciones as $t) { ?>
                          <span class="mr5"><?php echo $t->nombre ?>.</span>
                        <?php } ?>
                      </div>
                    </div>
                  </h5>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php } ?>

        <?php if (sizeof($profesional->formas_pago)>0) { ?>
          <div class="mid-item">
            <h3 class="active">Objetivos</h3>
            <div class="mid-item-info">
              <div class="doctor-meta-wrap">
                <ul>
                  <?php foreach($profesional->formas_pago as $o) { ?>
                    <p class="formas_pago">
                      <i class='fa fa-circle' aria-hidden='true'></i>
                      <?php 
                      $objetivo = existe_objetivo(array(
                        "id"=>$o->id,
                      ));
                      if ($objetivo === FALSE) { echo $o->nombre; } else { ?>
                        <a href="<?php echo mklink($objetivo->url) ?>"><?php echo $o->nombre ?></a>
                      <?php } ?>
                    </p>
                  <?php } ?>
                </ul>
              </div>
            </div>
          </div>
        <?php } ?>

        <?php if (sizeof($profesional->categorias)>0) { ?>
          <div class="mid-item">
            <h3 class="active">Especialidades</h3>
            <div class="mid-item-info">
              <div class="doctor-meta-wrap">
                <ul>
                  <?php foreach($profesional->categorias as $o) { ?>
                    <p class="formas_pago">
                      <i class='fa fa-circle' aria-hidden='true'></i>
                      <?php 
                      $objetivo = existe_categoria(array(
                        "id"=>$o->id,
                      ));
                      if ($objetivo === FALSE) { echo $o->nombre; } else { ?>
                        <a href="<?php echo mklink($objetivo->url) ?>"><?php echo $o->nombre ?></a>
                      <?php } ?>
                    </p>
                  <?php } ?>
                </ul>
              </div>
            </div>
          </div>
        <?php } ?>

        <?php 
        $sql = "SELECT texto, fecha, votos_positivos, nombre FROM not_entradas_comentarios WHERE id_entrada = $id ORDER BY fecha desc";
        $res = mysqli_query($conx, $sql);
        if (mysqli_num_rows($res)>0){?>
          <div class="mid-item">
            <h3 class="active">Comentarios</h3>
            <div class="mid-item-info">
              <div class="comentarios">
                <?php 
                $prom = "SELECT ROUND (AVG(votos_positivos),0) as prom from not_entradas_comentarios WHERE id_entrada = $id";
                $result = mysqli_query($conx, $prom);
                $promedio = mysqli_fetch_object($result);
                ?>
                <h2><?php echo $promedio->prom?>.0</h2>
                <div class="rate">
                    <?php for ($i=0; $i < $promedio->prom; $i++) { ?>
                      <span class="fa fa-star checked"></span>
                    <?php } ?>
                    <?php for ($i=$promedio->prom; $i < 5; $i++) { ?>
                      <span class="fa fa-star"></span>
                    <?php } ?>
                  </br><span><?php echo mysqli_num_rows($res);?> comentarios</span>
                </div>
                <hr>
                <?php while ($obj = mysqli_fetch_object($res)){?>
                  <div class="comentario_container mb20">
                    <b><?php echo $obj->nombre?></b></br>
                    <?php 
                    $time = strtotime($obj->fecha);
                    $fecha = date("d/m/y g:i A", $time);
                    ?>
                    <span class="fecha"><?php echo $fecha?></span>
                    <p class="mt10"><?php echo $obj->texto?></p>
                    <?php for ($i=0; $i < $obj->votos_positivos; $i++) { ?>
                      <span class="fa fa-star checked"></span>
                    <?php } ?>
                    <?php for ($i=$obj->votos_positivos; $i < 5; $i++) { ?>
                      <span class="fa fa-star"></span>
                    <?php } ?>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php } ?>

        <div class="row align-items-center">
          <div class="col-sm-6">
            <div class="hidden-xs"><a href="javascript:history.back()" class="psweb-btn psweb-medium-btn border-btn">Volver</a></div>
          </div>
          <div class="col-sm-6 tar">
            <div class="psweb-social rounded">
              <a href="whatsapp://send?text=<?php echo urlencode(current_url()) ?>" data-toggle="tooltip" data-placement="bottom" title="Compartir en WhatsApp"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
              <a href="mailto:?subject=<?php echo html_entity_decode($profesional->nombre,ENT_QUOTES) ?>&body=<?php echo(current_url()) ?>" data-toggle="tooltip" data-placement="bottom" title="Compartir por Email"><i class="fa fa-envelope" aria-hidden="true"></i></a>
              <a href="https://www.facebook.com/sharer.php?u=<?php echo urlencode(current_url()) ?>" onclick="window.open(this.href, 'mywin','left=50,top=50,width=600,height=350,toolbar=0'); return false;" data-toggle="tooltip" data-placement="bottom" title="Compartir en Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
            </div>
          </div>
        </div>

      </div>
      <div class="col-lg-4">
        <div class="request">
          <h3>Solicitar Turno Online</h3>
          <div class="request-info">
            <!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.</p>-->
            <div class="request-item">
              <h5><span>1</span> Nombre Completo</h5>
              <div>
                <div class="form-group">
                  <input type="text" class="form-control" id="turnoclick_nombre" placeholder="Nombre Completo">
                </div>
                <div class="form-group">
                  <input type="email" class="form-control" id="turnoclick_email" placeholder="Email">
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-5 pr0">
                      <select id="turnoclick_prefijo" class="form-control">
                        <?php include("includes/paises.php"); ?>
                      </select>
                    </div>
                    <div class="col-7">
                      <input type="tel" class="form-control" id="turnoclick_telefono" placeholder="WhatsApp/Tel">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="request-item mt15">
              <h5><span>2</span> Servicio</h5>
              <?php $j=0; foreach($profesional->direcciones as $dir) { ?>
                <div class="direccion-info mb0">
                  <h4>
                    <label for="turnoclick_servicio_<?php echo $dir->id ?>" class="i-checks">
                      <input 
                      data-deshabilitado_desde='<?php echo $dir->deshabilitado_desde ?>' 
                      data-deshabilitado_hasta='<?php echo $dir->deshabilitado_hasta ?>' 
                      data-dias='<?php echo json_encode($dir->dias) ?>' 
                      id="turnoclick_servicio_<?php echo $dir->id ?>" 
                      type="radio" class="turnoclick_servicio mr10" 
                      name="turnoclick_servicio" 
                      value="<?php echo $dir->id ?>" 
                      <?php echo ($j==0)?"checked":"" ?> />
                      <i></i>
                      <div class="oh">
                        <b><?php echo $dir->nombre ?></b>
                        <div class="row">
                          <div class="col-9 col-md-8 pr0">
                            <h6 style="padding-top: 5px;"><i class="fa fa-clock-o" aria-hidden="true"></i> <b>Duración:</b>
                              <?php if (!empty($dir->duracion_turno)) { ?>
                                <span class="duracion_minutos"> <?php echo $dir->duracion_turno ?> minutos</span>
                              <?php } else {  ?>
                                <span> - </span>
                              <?php } ?>
                            </h6>
                          </div>
                          <div class="col-3 col-md-4 pl0">
                            <div class="tarifas_costo_chica">
                              <?php if (!empty($dir->costo)) { ?>
                                <span><?php echo $dir->costo ?> <i class="fa fa-eur" aria-hidden="true"></i></span>
                              <?php } ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </label>
                  </h4>
                </div>
              <?php $j++; } ?>              
            </div>
            <div class="request-item services">
              <h5><span>3</span> Fecha y Hora</h5>
              <form onsubmit="return enviar_turno()">
                <div class="form-group calender">
                  <input style="padding-left: 45px" placeholder="Elegir Fecha" type="text" id="turnoclick_fecha" class="form-control"/>
                </div>
                <div class="form-group time">
                  <select style="padding-left: 45px" class="form-control" id="turnoclick_hora"><option>Elegir Horario</option></select>
                </div>
                <div class="custom-control custom-checkbox mt20 mb20">
                  <input type="checkbox" class="custom-control-input" id="turno_condiciones" name="example1">
                  <label class="custom-control-label fs16" for="turno_condiciones">Acepto los <a target="_blank" href="<?php echo mklink("entrada/terminos-y-condiciones-41119") ?>">términos y condiciones</a>, la <a target="_blank" href="<?php echo mklink("entrada/politica-de-privacidad-41120") ?>">política de privacidad</a> y el tratamiento de mis datos*</label>
                </div>
                <input id="turnoclick_submit" type="submit" name="Suscribite" value="Solicitar Turno Ahora" class="psweb-btn">
              </form>
              <?php /*
              <span class="services-link">Servicio de Turnos by <a href="https://www.turnoclick.com" target="_blank">TurnoClick.com</a></span>
              */ ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>

<script type="text/javascript">
function render_modal_clienapp() {

  $(".clienapp_paso_2").hide();
  $(".clienapp_paso_1").show();
  window.recargar_despues = null;

  $("#modal_clienapp").modal('show');
  $('#modal_clienapp').one('hidden.bs.modal', function (e) {
    location.reload();
  })
}

function do_clienapp_cerrar() {
  $('#modal_clienapp').modal('hide');
}

// CUANDO SE SUBMITEA EL BOTON DE WHATSAPP, LO PRIMERO QUE HACEMOS ES EMPEZAR A REGISTRAR EL USUARIO
function varcreative_registro_clienapp() {
  var nombre = $("#clienapp_checkout_nombre").val();
  nombre = nombre.trim();
  if (isEmpty(nombre)) {
    alert("Por favor ingrese su nombre");
    $("#clienapp_checkout_nombre").focus();
    return false;
  }
  var telefono = $("#clienapp_checkout_telefono").val();
  telefono = telefono.trim();
  telefono = telefono.replace(/\D/g,'');
  if (isEmpty(telefono)) {
    alert("Por favor ingrese su telefono");
    $("#clienapp_checkout_telefono").focus();
    return false;
  }
  if (telefono.length != 9) {
    alert("Por favor corrobore el numero de telefono.");
    $("#registro_telefono").select();
    return false;
  } 
  var email = $("#clienapp_checkout_email").val();

  if (isEmpty(email)) {
    alert("Por favor ingrese su email");
    $("#clienapp_checkout_email").focus();
    return false;    
  }
  /*
  if (!validateEmail(email)) {
    alert("Por favor ingrese su email");
    $("#clienapp_checkout_email").focus();
    return false;
  }*/

  var condiciones = $("#clientapp_condiciones").is(":checked") ? 1 : 0;
  if (condiciones == 0) {
    alert ("Por favor acepte los términos y condiciones");
    return false;
  }
  
  var prefijo = $("#clienapp_checkout_prefijo").val();
  var ps = hex_md5("1");
  clienapp_show_loading();
  $.ajax({
    "url":"/sistema/clientes/function/registrar/",
    "dataType":"json",
    "data": {
      "email":email,
      "ps":ps,
      "nombre":nombre,
      "telefono":telefono,
      "fax":prefijo,
      "id_empresa":ID_EMPRESA,
      "id_usuario": "<?php echo $profesional->id ?>",
    },
    "type":"post",
    "success":function(r) {
      if (r.error == 0) {
        // Si nos registramos correctamente, nos logueamos asi quedan las cookies
        varcreative_login_clienapp(email,ps);
        abrir_modal_relacionados();
      } else {
        alert(r.mensaje);
        clienapp_hide_loading();
      }
    },
    "error":function() {
      clienapp_hide_loading();
    },
  });
  return false;  
}

function clienapp_show_loading() {
  $("#clienapp_submit").attr("disabled","disabled");
  $("#clienapp_loading").show();
}

function clienapp_hide_loading() {
  $("#clienapp_submit").attr("disabled","");
  $("#clienapp_loading").hide();
}

function varcreative_login_clienapp(email,ps) {
  $.ajax({
    url: '/sistema/login/check_cliente/',
    type: 'POST',
    dataType: 'json',
    data: {
      'email': email, 
      'ps': ps,
      'id_empresa': ID_EMPRESA,
    },
    success: function(data, textStatus, xhr) {
      if (data.error == false && data.id_empresa == ID_EMPRESA) {
        varcreative_validar_clienapp(data.id);
      }
    },
    "error":function(){
      clienapp_hide_loading();
    }
  });
  return false;
}

function varcreative_validar_clienapp(id_cliente) {
  var nombre = $("#clienapp_checkout_nombre").val();
  var telefono = $("#clienapp_checkout_telefono").val();
  var email = $("#clienapp_checkout_email").val();
  var prefijo = $("#clienapp_checkout_prefijo").val();
  var observaciones = $("#clienapp_checkout_observaciones").val();
  var mensaje = "Hola, me contacto desde Entrena y Mas: "+"\n\n";
  mensaje += "Nombre y Apellido: *"+nombre+"*\n\n";
  mensaje += "Telefono: *"+prefijo+" "+telefono+"*\n\n";
  mensaje += "Email: *"+email+"*\n\n";
  mensaje += observaciones+"\n\n";

  var url = "https://wa.me/<?php echo (strlen($profesional->celular) < 10) ? "34".$profesional->celular : $profesional->celular ?>";
  url+= "?text="+encodeURIComponent(mensaje);  

  // Primero mandamos para finalizar el carrito
  $.ajax({
    "url":"/sistema/consultas/function/enviar/",
    "type":"post",
    "data":{
      "id_empresa":ID_EMPRESA,
      "id_usuario": "<?php echo $profesional->id ?>",
      "nombre":nombre,
      "email":email,
      "telefono":telefono,
      "prefijo":prefijo,
      "id_origen":30,
      "mensaje":observaciones,
    },
    "dataType":"json",
    "success":function(r){
      // Cuando el carrito fue enviado
      $(".clienapp_paso_1").hide();
      $(".clienapp_paso_2").show();
      window.recargar_despues = 1;
      enviar_ga();
      clienapp_hide_loading();

      // Intentamos abrir otra ventana
      var open = window.open(url,"_blank");
      if (open == null || typeof(open)=='undefined') {
        // Si se bloqueo el popup, se redirecciona
        location.href = url;
      }
    },
    "error":function() {
      clienapp_hide_loading();
    }
  });
  return false;
}
</script>
<link rel="stylesheet" type="text/css" href="/templates/comun/clienapp.css">
<div id="modal_clienapp" class="modal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content clienapp_container">  
      <div class="oh tar">
        <img class="cp" style="width: 36px;" src="/templates/comun/img/close.png" alt="Cerrar" onclick="do_clienapp_cerrar()">
      </div>
      <h3 class="clienapp_checkout_titulo" id="clienapp_checkout_titulo"><?php echo $profesional->nombre ?></h3>
      <div class="clienapp_paso_1">
        <h4 class="clienapp_checkout_subtitulo">Completa la siguiente información para enviar tu consulta por Whatsapp</h4>
        <div>
          <label class="clienapp_label" for="clienapp_checkout_nombre">Nombre y apellido (*)</label>
          <input type="text" name="clienapp_checkout_nombre" id="clienapp_checkout_nombre" class="clienapp_input" placeholder="Nombre completo. Ej: Juan Perez">
          <label class="clienapp_label" for="clienapp_checkout_telefono">Número de Whatsapp (*)</label>

          <div class="row">
            <div class="col-xs-4 col-md-3 pr0">
              <select id="clienapp_checkout_prefijo" class="clienapp_input chat_user_form_select chat_user_form_2_prefijo">
                <?php include("includes/paises.php"); ?>
              </select>
            </div>
            <div class="col-xs-8 col-md-9">
              <input type="text" name="clienapp_checkout_telefono" id="clienapp_checkout_telefono" class="clienapp_input" placeholder="Ej: 655554444">
            </div>
          </div>

          <label class="clienapp_label" for="clienapp_checkout_email">Email (*)</label>
          <input type="text" name="clienapp_checkout_email" id="clienapp_checkout_email" class="clienapp_input" placeholder="Ej: juanperez@gmail.com">

          <label class="clienapp_label" for="clienapp_checkout_observaciones">Mensaje</label>
          <textarea name="clienapp_checkout_observaciones" id="clienapp_checkout_observaciones" class="clienapp_input" placeholder="Escribe tu mensaje o consulta"></textarea>
          
          <div class="custom-control custom-checkbox mb20">
            <input type="checkbox" class="custom-control-input" id="clientapp_condiciones" name="example1">
            <label class="custom-control-label" for="clientapp_condiciones">Acepto los <a target="_blank" href="<?php echo mklink("entrada/terminos-y-condiciones-41119") ?>">términos y condiciones</a>, la <a target="_blank" href="<?php echo mklink("entrada/politica-de-privacidad-41120") ?>">política de privacidad</a> y el tratamiento de mis datos*</label>
          </div>

          <button onclick="varcreative_registro_clienapp()" id="clienapp_submit" class="clienapp_boton mb20">
            <i class="fa fa-whatsapp" aria-hidden="true"></i> ENVIAR WHATSAPP
          </button>
          <div id="clienapp_loading" style="display: none" class="tac mt10 mb10">
            <img src="/templates/comun/img/ajax-loader.gif" alt="Loading"/>
          </div>
        </div>
        <p class="clienapp_disclaimer">Se requerirá de Whatsapp móvil o web para enviar el mensaje.</p>
        <p class="clienapp_disclaimer">Servicio provisto por <a target="_blank" href="https://www.clienapp.com">Clienapp.com</a></p>
      </div>
      <div class="clienapp_paso_2">
        <div style="max-width: 400px; margin:0 auto">
          <p class="tac">Tu consulta ha sido enviada!</p>
          <p class="clienapp_disclaimer">Servicio provisto por <a target="_blank" href="https://www.clienapp.com">Clienapp.com</a></p>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="modal_profesionales_relacionados" class="modal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">  
      <i class="fa fa-times" data-dismiss="modal" aria-label="Close"></i>
      <h3 class="clienapp_checkout_titulo">CONTACTA CON PROFESIONALES RELACIONADOS</h3>
      <span class="descripcion">
        ¡Muchas gracias por contactar con <?php echo $profesional->nombre ?>!<br>
        Te recomendamos los siguientes profesionales de la misma zona que pueden ser de tu interés:
      </span>
      <div class="row container-profesionales">
        <?php foreach ($profesionales_relacionados as $pr) { ?>
          <?php $link = mklink("profesional/".$pr->apellido."-".$pr->id."/"); ?>
          <div class="col-md-4 col-xs-12">
            <div class="top">
              <?php if (!empty($pr->path)) { ?>
                <img src="<?php echo $pr->path ?>" alt="<?php echo ($pr->nombre);?>">
              <?php } else if (!empty($empresa->no_imagen)) { ?>
                <img src="/sistema/<?php echo $empresa->no_imagen ?>" alt="<?php echo ($pr->nombre);?>">
              <?php } else { ?>
                <img src="images/no-imagen.png" alt="<?php echo ($pr->nombre);?>">
              <?php } ?>
              <p><?php echo $pr->nombre; ?></p>
            </div>
            <div class="bottom">
              <span><?php echo $pr->sobre_mi; ?></span>
              <a target="_blank" class="psweb-btn psweb-medium-btn border-btn mt15" href="<?php echo $link; ?>">Contactar</a>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
function enviar_ga() {
  dataLayer.push({
   'ecommerce' : {
      'currencyCode' : 'EUR',
      'purchase': {
        'actionField': {
          'id': '<?php echo $profesional->id." ".ucwords(mb_strtolower($profesional->nombre)) ?>',
          'revenue': '<?php echo $valor ?>'  // IMPORTE QUE SE LE VA A FACTURAR O 0,01 EN CASO DE QUE NO HAYA PUESTO DE PAGO  
        },
        'products': [{                            
          'name': '<?php echo ucwords(mb_strtolower($profesional->nombre)) ?>',     
          'id': '<?php echo $profesional->id ?>',
          'price': '<?php echo $valor ?>',  //IMPORTE QUE VA A PAGAR O 0.01 SI NO TIENEN NADA DE PAGO
          'quantity': 1
        }]
      }
    }
  });
}

$(document).ready(function(){

  $("#turnoclick_fecha").datepicker({
    "dateFormat":"dd/mm/yy",
    "currentText":"Hoy",
    "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
    "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
    "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
    "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
    "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
    "nextText":"Proximo",
    "prevText":"Anterior",
    "minDate":0,
    "beforeShowDay":function(date) {
      var dia = date.getDay(); // Domingo es 0
      var dias = String($(".turnoclick_servicio:checked").data("dias"));
      if (dias.length == 0) return [true];
      var dias2 = dias.split(",");
      salida = false;
      for(var i=0;i< dias2.length;i++) {
        var d = dias2[i];
        if (dia == d) {
          salida = true;
          break;
        }
      }

      // Controlamos ademas que no esten dentro de las fechas deshabilitadas
      var deshabilitado_desde = $(".turnoclick_servicio:checked").data("deshabilitado_desde");
      var deshabilitado_hasta = $(".turnoclick_servicio:checked").data("deshabilitado_hasta");
      if (deshabilitado_desde != "0000-00-00" && deshabilitado_hasta != "0000-00-00") {
        var desde = new Date(deshabilitado_desde.substr(0,4),deshabilitado_desde.substr(5,2)-1,deshabilitado_desde.substr(8,2));
        var hasta = new Date(deshabilitado_hasta.substr(0,4),deshabilitado_hasta.substr(5,2)-1,deshabilitado_hasta.substr(8,2));
        hasta.setDate(hasta.getDate()+1);
        if (desde.getTime() < date.getTime() && date.getTime() < hasta.getTime()) salida = false;
      }
      return [salida];
    }
  }).change(buscar_horarios);
})


function enviar_turno() {
  var nombre = $("#turnoclick_nombre").val();
  var email = $("#turnoclick_email").val();
  var telefono = $("#turnoclick_telefono").val();
  var id_servicio = $(".turnoclick_servicio:checked").val();
  var fecha = $("#turnoclick_fecha").val();
  var hora = $("#turnoclick_hora").val();
  var prefijo = $("#turnoclick_prefijo").val();
  var condiciones = $("#turno_condiciones").is(":checked") ? 1 : 0;
  
  if (isEmpty(nombre) || nombre == "Nombre") {
    alert("Por favor ingrese un nombre");
    $("#turnoclick_nombre").select()
    return false;          
  }
  if (!validateEmail(email)) {
    alert("Por favor ingrese un email valido");
    $("#turnoclick_email").select()
    return false;          
  }
  if (isEmpty(telefono)) {
    alert("Por favor ingrese un telefono");
    $("#turnoclick_telefono").select()
    return false;          
  }

  if (isEmpty(id_servicio)) {
    alert("Por favor seleccione un servicio.");
    return false;
  }
  if (isEmpty(fecha)) {
    alert("Por favor seleccione una fecha para su turno.");
    return false;
  }
  if (isEmpty(hora) || hora == 0) {
    alert("Por favor seleccione un horario disponible.");
    $("#turnoclick_horarios").focus();
    return false;
  }

  if (condiciones == 0) {
    alert ("Por favor acepte los terminos y condiciones");
    return false;
  }

  $("#converse-enviar").attr('disabled', 'disabled');
  var turno = {
    nombre : nombre,
    prefijo: prefijo,
    telefono : telefono,
    id_empresa : ID_EMPRESA,
    email : email,
    fecha : fecha,
    hora : hora,
    canal: "<?php echo (!empty($marca_blanca)) ? $marca_blanca :"" ?>",
    id_servicio : id_servicio,
    id_usuario: "<?php echo $profesional->id ?>",
  };
  $.ajax({
    "url":"/sistema/turnos/function/enviar/",
    "type":"post",
    "dataType":"json",
    "data":turno,
    "success":function(r){
      if (r.error == 0) {
        enviar_ga();
        alert("El turno se ha reservado correctamente. Hemos enviado un email con su comprobante. Muchas gracias!");
      } else {
        alert("Ocurrio un error al enviar sus datos. Disculpe las molestias");
        $("#converse-enviar").removeAttr('disabled');
      }
    }
  });
  return false;
}

function buscar_horarios() {
  var id_servicio = $(".turnoclick_servicio:checked").val();
  var fecha = $("#turnoclick_fecha").val();
  if (isEmpty(fecha)) return;
  if (id_servicio == 0) return;
  $.ajax({
    "url":"/sistema/turnos/function/disponibles/",
    "dataType":"json",
    "type":"post",
    "data":{
      "id_empresa":ID_EMPRESA,
      "id_servicio":id_servicio,
      "fecha":fecha,
    },
    "success":function(r) {
      if (typeof r.error != "undefined") {
        alert(r.error);
      } else {
        $("#turnoclick_hora").empty();
        $("#turnoclick_hora").append('<option value="0">Elija Horario</option>');
        for(var i=0; i< r.disponibles.length; i++) {
          var d = r.disponibles[i];
          $("#turnoclick_hora").append('<option>'+d.hora+'</option>');
        }
      }
    }
  });
}
</script>

<script type="text/javascript">
function enviar_contacto() {
    
  var nombre = $("#contacto_nombre").val();
  var email = $("#contacto_email").val();
  var mensaje = $("#contacto_mensaje").val();
  var telefono = $("#contacto_telefono").val();
  var condiciones = $("#comunicate_condiciones").is(":checked") ? 1 : 0;

  if (isEmpty(nombre)) {
    alert("Por favor ingrese su nombre");
    $("#contacto_nombre").focus();
    return false;          
  }
  if (!validateEmail(email)) {
    alert("Por favor ingrese un email valido");
    $("#contacto_email").focus();
    return false;          
  }
  if (isEmpty(telefono)) {
    alert("Por favor ingrese su telefono");
    $("#contacto_telefono").focus();
    return false;          
  }

  if (isEmpty(mensaje) || mensaje == "Mensaje") {
    alert("Por favor ingrese su mensaje");
    $("#contacto_mensaje").focus();
    return false;              
  }

  if (condiciones == 0) {
    alert ("Por favor acepte los términos y condiciones");
    return false;
  }
  var prefijo = $("#contacto_prefijo").val();

  $("#contacto_submit").attr('disabled', 'disabled');
  var datos = {
    "para":"<?php echo $profesional->email ?>",
    "nombre":nombre,
    "email":email,
    "mensaje":mensaje,
    "prefijo":prefijo,
    "telefono":telefono,
    "id_empresa":ID_EMPRESA,
    "prefijo":prefijo,
    "canal": "<?php echo (!empty($marca_blanca)) ? $marca_blanca :"" ?>",
    "bcc":"<?php echo $empresa->email ?>",
    "id_usuario": "<?php echo $profesional->id ?>",
  }
  $.ajax({
    "url":"/sistema/consultas/function/enviar/",
    "type":"post",
    "dataType":"json",
    "data":datos,
    "success":function(r){
      if (r.error == 0) {
        enviar_ga();
        alert("Tu mensaje ha sido enviado. ¡Gracias por contactar con este profesional!");
        abrir_modal_relacionados();
      } else {
        alert("Ocurrio un error al enviar su email. Disculpe las molestias");
        $("#contacto_submit").removeAttr('disabled');
      }
    }
  });
  return false;
} 


function abrir_modal_relacionados() {

  $("#modal_profesionales_relacionados").modal('show');

  var maximo = 0;
  $("#modal_profesionales_relacionados .container-profesionales .bottom").each(function(i,e){
    if ($(e).height() > maximo) maximo = $(e).height();
  });
  maximo = Math.ceil(maximo);
  $("#modal_profesionales_relacionados .container-profesionales .bottom").height(maximo);
}

$(document).ready(function (){
  $(".scrollTo").click(function(e){
    var to = $(e.currentTarget).data("to");
    $('html, body').animate({
        scrollTop: $("#"+to).offset().top
    }, 2000);
  });

  $(".click_red").on("click", function(e) {
    var ref = $(e.currentTarget).attr("data-ref");
    var href = $(e.currentTarget).attr("data-href");

    $.ajax({
      "url": "<?php echo mklink('web/ajax_click_redes') ?>",
      "type": "post",
      "dataType": "json",
      "data": {
        "ref": ref,
        "id_usuario": "<?php echo $profesional->id ?>",
        "id_empresa": "<?php echo $empresa->id ?>",
        "ip_cliente": "<?php echo $_SERVER['REMOTE_ADDR'] ?>",
        "profesional_email": "<?php echo $profesional->email ?>",
      },
    });

    window.open(href, "_blank");
  });

});
</script>

</body>
</html>