<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("includes/init.php");
include("includes/search_params.php");
$nombre_pagina = "profesionales";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php") ?>
<?php if (isset($canonical)) { ?>
  <link rel="canonical" href="<?php echo $canonical ?>" />
<?php } ?>
</head>
<body>

<script type='text/javascript'>var _pix = document.getElementById('_pix_id_56b02767-44d1-a22d-69b6-1d64acf3a3a9');if (!_pix) { var protocol = '//'; var a = Math.random() * 1000000000000000000; _pix = document.createElement('iframe'); _pix.style.display = 'none'; _pix.setAttribute('src', protocol + 'aax-eu.amazon-adsystem.com/s/iu3?d=generic&ex-fargs=%3Fid%3D56b02767-44d1-a22d-69b6-1d64acf3a3a9%26type%3D31%26m%3D44551&ex-fch=416613&ex-src=https://entrenaymas.com/&ex-hargs=v%3D1.0%3Bc%3D8269573310802%3Bp%3D56B02767-44D1-A22D-69B6-1D64ACF3A3A9' + '&cb=' + a); _pix.setAttribute('id','_pix_id_56b02767-44d1-a22d-69b6-1d64acf3a3a9'); document.body.appendChild(_pix);}</script><noscript> <img height='1' width='1' border='0' alt='' src='https://aax-eu.amazon-adsystem.com/s/iui3?d=forester-did&ex-fargs=%3Fid%3D56b02767-44d1-a22d-69b6-1d64acf3a3a9%26type%3D31%26m%3D44551&ex-fch=416613&ex-src=https://entrenaymas.com/&ex-hargs=v%3D1.0%3Bc%3D8269573310802%3Bp%3D56B02767-44D1-A22D-69B6-1D64ACF3A3A9' /></noscript>

<?php include("includes/header.php") ?>

<section class="psweb-search search-style-two">
  <div class="container">
    <div class="search-wrap">
      <div class="search-form">
        <?php 
        $funcion_buscar = "buscar_listado";
        include("includes/searchform.php") ?>
      </div>
    </div>
  </div>
</section>

<!-- Psweb Mid Wrap -->
<section class="psweb-mid-wrap">
  <div class="container">
    <ul class="nav nav-tabs style-two">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#Lista"><i class="fa fa-list-ul" aria-hidden="true"></i> Resultados en Lista</a>
      </li>
      <?php /*
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0)" onclick="buscar_mapa()"><i class="fa fa-map-marker" aria-hidden="true"></i> Resultados en Mapa</a>
      </li>*/ ?>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="Lista">
        <div class="blog-meta">
          <?php include("includes/searchbar.php"); ?>
        </div>
        <div class="doctor-style-two">
          <?php foreach($profesionales as $r) {    ?>
            <?php item ($r) ?> 
          <?php  } ?>
        
          <?php if ($total_resultados > 20) {  ?>
            <div id="myScroll">
              <div class="text-center row ver-mas">
                <div class="continuar-viendo">
                  <a href="javascript:void(0)" onclick="add_item()" class="desde_precio_contactar boton-grande">
                    Ver más
                  </a>
                </div>
              </div>
              <div class="text-center d-none loading">
                <img src="assets/images/load-loading.gif">
              </div>
            </div>
          <?php } ?>

          <?php if (isset($texto_pie) && !empty($texto_pie)) { ?>
            <?php if (isset($h2) && !empty($h2)) { ?>
              <h2 class="subtitulo-listado"><?php echo $h2 ?></h2>
            <?php } ?>
            <div class="texto-listado">
              <?php echo $texto_pie; ?>
            </div>
          <?php } ?>

          <?php if (isset($mostrar_links_footer) && isset($seo_url) && isset($seo_url->url)) { 
            // Buscamos las relacionados
            $offset_links_relacionados = 12;
            $links_relacionados = array();
            $p = explode("/", $seo_url->url);
            $primer = $p[0];
            $sql = "SELECT * FROM seo_urls WHERE url LIKE '%$primer%' AND id_empresa = $seo_url->id_empresa LIMIT 0,$offset_links_relacionados ";
            $q = mysqli_query($conx,$sql);
            while(($r = mysqli_fetch_object($q))!==NULL) $links_relacionados[] = $r;

            // Si nos falta llenar para llegar a 12
            $hasta = ($offset_links_relacionados - sizeof($links_relacionados));
            if ($hasta > 0) {
              $sql = "SELECT * FROM seo_urls WHERE url LIKE '%$primer%' AND id_empresa = $seo_url->id_empresa ORDER BY RAND() ASC LIMIT 0,$hasta ";
              $q = mysqli_query($conx,$sql);
              while(($r = mysqli_fetch_object($q))!==NULL) $links_relacionados[] = $r;              
            }

            if (sizeof($links_relacionados)>0) { ?>
              <h2 class="subtitulo-listado">También te puede interesar:</h2>
              <div class="row">
                <?php foreach($links_relacionados as $l) { ?>
                  <div class="col-md-3">
                    <a class="linkk" href="https://entrenaymas.com/<?php echo $l->url ?>"><?php echo $l->h1 ?></a>
                  </div>
                <?php } ?>
              </div>
            <?php } ?>


            <?php 
            // MODULO DE INTERLINKEADO
            // =======================

            // entrenador/ciclismo/madrid
            if (isset($categoria_seo) && isset($localidad_seo)) { ?>

              <?php 
              // Objetivos que puedes conseguir
              $cc = array(
                "offset"=>8,
                "id_desde"=>(isset($seo_url_id) ? $seo_url_id : 0),
                "campos"=>array(
                  array(
                    "nombre"=>"Especialidad",
                    "comparacion"=>"=",
                    "valor"=>$categoria_seo->id,
                  ),
                  array(
                    "nombre"=>"Localidad",
                    "comparacion"=>"=",
                    "valor"=>$localidad_seo->id,
                  ),
                  array(
                    "nombre"=>"Objetivo",
                    "comparacion"=>"!=",
                    "valor"=>0,
                  ),
                ),                
              );
              if (isset($titulacion_seo)) {
                $cc["campos"][] = array(
                  "nombre"=>"Titulacion",
                  "comparacion"=>"=",
                  "valor"=>$titulacion_seo->id,
                );
              }
              $urls = next_seo_urls($cc);
              if (sizeof($urls)>0) { ?>
                <h2 class="subtitulo-listado">Objetivos que puedes conseguir:</h2>
                <div class="row">
                  <?php foreach($urls as $u) { ?>
                    <div class="col-md-3">
                      <a class="linkk" href="<?php echo mklink($u->url) ?>"><?php echo $u->h1 ?></a>
                    </div>
                  <?php } ?>
                </div>
              <?php } ?>

              <?php 
              // Entrenadores de <CATEGORIA> en otras poblaciones
              $cc = array(
                "offset"=>8,
                "id_desde"=>(isset($seo_url_id) ? $seo_url_id : 0),
                "campos"=>array(
                  array(
                    "nombre"=>"Especialidad",
                    "comparacion"=>"=",
                    "valor"=>$categoria_seo->id,
                  ),
                  array(
                    "nombre"=>"Localidad",
                    "comparacion"=>"!=",
                    "valor"=>$localidad_seo->id,
                  ),
                ),                
              );
              if (isset($titulacion_seo)) {
                $cc["campos"][] = array(
                  "nombre"=>"Titulacion",
                  "comparacion"=>"=",
                  "valor"=>$titulacion_seo->id,
                );
              }
              $urls = next_seo_urls($cc);
              if (sizeof($urls)>0) { ?>
                <h2 class="subtitulo-listado">Entrenadores de <?php echo ucwords(mb_strtolower($categoria_seo->nombre)); ?> en otras poblaciones:</h2>
                <div class="row">
                  <?php foreach($urls as $u) { ?>
                    <div class="col-md-3">
                      <a class="linkk" href="<?php echo mklink($u->url) ?>"><?php echo $u->h1 ?></a>
                    </div>
                  <?php } ?>
                </div>
              <?php } ?>

              <?php 
              // Entrenadores de otras especialidades en esa poblacion
              $cc = array(
                "offset"=>8,
                "id_desde"=>(isset($seo_url_id) ? $seo_url_id : 0),
                "campos"=>array(
                  array(
                    "nombre"=>"Especialidad",
                    "comparacion"=>"!=",
                    "valor"=>$categoria_seo->id,
                  ),
                  array(
                    "nombre"=>"Localidad",
                    "comparacion"=>"=",
                    "valor"=>$localidad_seo->id,
                  ),
                ),                
              );
              if (isset($titulacion_seo)) {
                $cc["campos"][] = array(
                  "nombre"=>"Titulacion",
                  "comparacion"=>"=",
                  "valor"=>$titulacion_seo->id,
                );
              }
              $urls = next_seo_urls($cc);
              if (sizeof($urls)>0) { ?>
                <h2 class="subtitulo-listado">Entrenadores de otras especialidades en <?php echo ucwords(mb_strtolower($localidad_seo->nombre)); ?>:</h2>
                <div class="row">
                  <?php foreach($urls as $u) { ?>
                    <div class="col-md-3">
                      <a class="linkk" href="<?php echo mklink($u->url) ?>"><?php echo $u->h1 ?></a>
                    </div>
                  <?php } ?>
                </div>
              <?php } ?>

            <?php
            // entrenador/ciclismo
            } else if (isset($categoria_seo) && !isset($localidad_seo)) { ?>

              <?php 
              // Objetivos que puedes conseguir
              $cc = array(
                "offset"=>8,
                "id_desde"=>(isset($seo_url_id) ? $seo_url_id : 0),
                "campos"=>array(
                  array(
                    "nombre"=>"Especialidad",
                    "comparacion"=>"=",
                    "valor"=>$categoria_seo->id,
                  ),
                  array(
                    "nombre"=>"Localidad",
                    "comparacion"=>"=",
                    "valor"=>0,
                  ),
                  array(
                    "nombre"=>"Objetivo",
                    "comparacion"=>"!=",
                    "valor"=>0,
                  ),
                ),                
              );
              if (isset($titulacion_seo)) {
                $cc["campos"][] = array(
                  "nombre"=>"Titulacion",
                  "comparacion"=>"=",
                  "valor"=>$titulacion_seo->id,
                );
              }
              $urls = next_seo_urls($cc);
              if (sizeof($urls)>0) { ?>
                <h2 class="subtitulo-listado">Objetivos que puedes conseguir:</h2>
                <div class="row">
                  <?php foreach($urls as $u) { ?>
                    <div class="col-md-3">
                      <a class="linkk" href="<?php echo mklink($u->url) ?>"><?php echo $u->h1 ?></a>
                    </div>
                  <?php } ?>
                </div>
              <?php } ?>

              <?php 
              // Entrenadores de otras especialidades
              $cc = array(
                "offset"=>8,
                "id_desde"=>(isset($seo_url_id) ? $seo_url_id : 0),
                "campos"=>array(
                  array(
                    "nombre"=>"Especialidad",
                    "comparacion"=>"!=",
                    "valor"=>$categoria_seo->id,
                  ),
                  array(
                    "nombre"=>"Localidad",
                    "comparacion"=>"=",
                    "valor"=>0,
                  ),
                ),                
              );
              if (isset($titulacion_seo)) {
                $cc["campos"][] = array(
                  "nombre"=>"Titulacion",
                  "comparacion"=>"=",
                  "valor"=>$titulacion_seo->id,
                );
              }
              $urls = next_seo_urls($cc);
              if (sizeof($urls)>0) { ?>
                <h2 class="subtitulo-listado">Entrenadores de otras especialidades:</h2>
                <div class="row">
                  <?php foreach($urls as $u) { ?>
                    <div class="col-md-3">
                      <a class="linkk" href="<?php echo mklink($u->url) ?>"><?php echo $u->h1 ?></a>
                    </div>
                  <?php } ?>
                </div>
              <?php } ?>

              <?php 
              // Donde están nuestros entrenadores de <CATEGORIA>
              $cc = array(
                "offset"=>8,
                "id_desde"=>(isset($seo_url_id) ? $seo_url_id : 0),
                "campos"=>array(
                  array(
                    "nombre"=>"Especialidad",
                    "comparacion"=>"=",
                    "valor"=>$categoria_seo->id,
                  ),
                  array(
                    "nombre"=>"Localidad",
                    "comparacion"=>"!=",
                    "valor"=>0,
                  ),
                ),                
              );
              if (isset($titulacion_seo)) {
                $cc["campos"][] = array(
                  "nombre"=>"Titulacion",
                  "comparacion"=>"=",
                  "valor"=>$titulacion_seo->id,
                );
              }
              $urls = next_seo_urls($cc);
              if (sizeof($urls)>0) { ?>
                <h2 class="subtitulo-listado">Donde están nuestros entrenadores de <?php echo ucwords(mb_strtolower($categoria_seo->nombre)); ?>:</h2>
                <div class="row">
                  <?php foreach($urls as $u) { ?>
                    <div class="col-md-3">
                      <a class="linkk" href="<?php echo mklink($u->url) ?>"><?php echo $u->h1 ?></a>
                    </div>
                  <?php } ?>
                </div>
              <?php } ?>

            <?php
            // entrenador
            } else if (!isset($categoria_seo) && !isset($localidad_seo)) { ?>

            <?php } ?>

          <?php } ?>


        </div>
        
      </div>

    </div>
  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>

<script type="text/javascript">
function mostrar_direccion(e) {
  var id = $(e).data("id");
  $(e).parent().find(".active").removeClass("active");
  $(e).parent().parent().find(".direccion-wrap").removeClass("active");
  $("#dir_"+id).addClass("active");
  $("#link_"+id).addClass("active");
}
function buscar() {
  buscar_listado();
}
</script>
<script type="text/javascript">
  window.page = 1;
  window.stop = 0;
  function add_item() { 

    var not_ids = new Array();
    $(".doctor-item").each(function(i,e){
      not_ids.push($(e).data("id"));
    });
    not_ids = not_ids.join("-");

    var offset = 99999;
      $(".loading").removeClass('d-none');
      $.ajax({
        type: "GET",
        url: "/ajax_profesionales/",
        contentType: "application/json; charset=utf-8",
        data: {
          "id_empresa":ID_EMPRESA,
          //"page":window.page,
          "offset":offset,
          "not_ids":not_ids,
          "locs":'<?php echo implode("-", $vc_localidades) ?>',
          "esp":'<?php echo implode("-", $vc_especialidades) ?>',
          "cat":'<?php echo implode("-", $vc_categorias) ?>',
          "tit":'<?php echo implode("-", $vc_titulos) ?>',
          "tt":'<?php echo implode("-", $vc_tipos_terapias) ?>',
          "ta":'<?php echo implode("-", $vc_tipos_atenciones) ?>',
          "fp":'<?php echo implode("-", $vc_formas_pago) ?>',
          "favs":'<?php echo implode("-", $in_ids) ?>',
          "id_perfil":'<?php echo (isset($portal_id_perfil_profesional) ? $portal_id_perfil_profesional : 0) ?>',
          "usar_get":1, // Automaticamente se llama a usuario_model->get por cada elemento del array
        },
        dataType: "html",
        success: function (r) {
          console.log(r);
          $("#myScroll").append(r);
          window.page++;
          $(".loading").addClass('d-none');
          $(".ver-mas").addClass('d-none');
        },
        error: function (req, status, error) {
          alert("Error try agin");
        }
      });}
  /*
  $(window).scroll(function () {
    // End of the document reached?
    var position = $(window).scrollTop();
    var bottom = $("#myScroll").height() + 200;
    if (position > bottom && window.stop == 0) {
      add_item();
    }
  }); 
  */
</script>
</body>
</html>