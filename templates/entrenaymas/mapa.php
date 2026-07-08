<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("includes/init.php");
include("includes/search_params.php");
$nombre_pagina = "mapa";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <?php include("includes/head.php") ?>
</head>
<body>

<?php include("includes/header.php") ?>

<section class="psweb-search search-style-two">
  <div class="container">
    <div class="search-wrap">
      <div class="search-form">
        <?php 
        $funcion_buscar = "buscar_mapa";
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
        <a class="nav-link" href="javascript:void(0)" onclick="buscar_listado()"><i class="fa fa-list-ul" aria-hidden="true"></i> Resultados en Lista</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#Mapa"><i class="fa fa-map-marker" aria-hidden="true"></i> Resultados en Mapa</a>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="Mapa">
        <div class="blog-meta">
          <?php include("includes/searchbar.php"); ?>
        </div>
      </div>
    </div>
    <div id="map" style="height:500px"></div>
  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>

<?php include_once("templates/comun/mapa_js.php"); ?>

<script type="text/javascript">
$(document).ready(function(){

  var mymap = L.map('map').setView([<?php echo $empresa->latitud ?>,<?php echo $empresa->longitud ?>], <?php echo $empresa->zoom ?>);

  L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
      '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
      'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    id: 'mapbox.streets'
  }).addTo(mymap);

  <?php $i=0;
  foreach($profesionales as $p) {
    $link = mklink("profesional/".$p->apellido."-".$p->id."/");
    if (isset($p->latitud) && isset($p->longitud) && !empty($p->latitud) && !empty($p->longitud)) { ?>
      var contentString<?php echo $i; ?> = '<div>'+
        '<div class="feature-item" style="padding: 0px; display:table">'+
          '<div class="feature-image" style="width: 200px; display: table-cell; float:none">'+
            '<a href=\"<?php echo $link ?>\">'+
              '<img style="max-width:100%" src=\"/sistema/<?php echo ((!empty($p->path)) ? $p->path : $empresa->no_imagen) ?>\"/>'+
            '</a>'+
          '</div>'+
          '<div class="list-view-detail" style="width: 200px; float:none; vertical-align:top; display:table-cell">'+
            '<div class="featured-detail">'+
              '<h5><a href=\"<?php echo $link ?>\"><?php echo $p->nombre ?></a></h5>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>';

      var marker<?php echo $i; ?> = L.marker([<?php echo $p->latitud ?>,<?php echo $p->longitud ?>]);
      marker<?php echo $i; ?>.addTo(mymap);

      marker<?php echo $i; ?>.bindPopup(contentString<?php echo $i; ?>);

    <?php } ?>
  <?php $i++; } ?>

});

function mostrar_direccion(e) {
  var id = $(e).data("id");
  $(e).parent().find(".active").removeClass("active");
  $(e).parent().parent().find(".direccion-wrap").removeClass("active");
  $("#dir_"+id).addClass("active");
  $("#link_"+id).addClass("active");
}
function buscar() {
  buscar_mapa();
}
</script>
</body>
</html>