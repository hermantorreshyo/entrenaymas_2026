<?php
include("includes/init.php");
$nombre_pagina = "listado";
$default = array(
  "offset"=>24,
  "order"=>2,
);
extract($articulo_model->get_variables($default));
$seo_title = $empresa->seo_title." | ".$vc_titulo;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php") ?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/css/paquete.css">
</head>
<body>

<?php include("includes/header.php") ?>

<!-- Psweb Page Title -->
<section class="psweb-page-title">
  <div class="container">
    <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">Caja Regalo</h2>
    <!-- <ul class="breadcrumb wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
      <li><a href="<?php echo mklink("/") ?>">Home</a></li>
      <li><i class="fs14 ml10 mr10 fa fa-chevron-right"></i></li>
      <li>Paquetes</li>
    </ul> -->
  </div>
</section>

<div class="paquete">
  <div class="fondo">
    <div class="tamaño mx-auto">
      <div id="carouselExampleControls" class="carousel slide">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="assets/img/foto-slider.png" class="d-block w-100" alt="...">
          </div>
          <div class="carousel-item ">
            <img src="assets/img/foto-slider.png" class="d-block w-100" alt="...">
          </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
    </div>
  </div>
</div>
<!-- Psweb Blog Wrap -->
<section class="psweb-blog-wrap">
  <div class="container">

    <div class="paquete">
      <div class="container texto">
        <div class="row">
          <div class="col-sm-12">
            <div class="row ">
              <h5 class="H5">Cajas Destacadas</h5>
            </div>
            <div class="row">
              <h1 class="H2">¡Elige la que más te guste!</h1>
            </div>
            <div class="row ">
              <img class="img" src="assets/img/linea.png" alt="">
            </div>
          </div>
        </div>
      </div>
      <div class="container iconos">
        <div class="roww">
          <div class="col-sm-4 cuaderno">
            <div class="fondo-redondeado">
              <img src="assets/img/cuaderno.png" alt="">  
            </div>
            <h6 class="H6">Elige tu Caja Regalo</h6>
            <p>Elige la caja que más te guste para ti o para regalar</p>
          </div>
          <div class="col-sm-4 ticket">
            <div class="fondo-redondeado">
              <img src="assets/img/tickets.png" alt="">
            </div>
            <h6>Introduce tu código de activación</h6>
            <p>Introduce el código que te permitirá canjear tu regalo.</p>
          </div>
          <div class="col-sm-4 like">
            <div class="fondo-redondeado">
              <img src="assets/img/like.png" alt="">
            </div>
            <h6>Escoge a tu profesional</h6>
            <p>Selecciona al profesional que más se adapte a ti ¡y logra tus objetivos!</p>
          </div>
        </div>
      </div>
      <div class="container precios">
        <div class="row">
          <?php $listado = $articulo_model->get_list(array(
            "offset"=>2,
          ));
          if (!empty($listado)) array_push($listado,array_shift($listado));
          foreach($listado as $r) { ?>
            <div class="col-sm-6">
              <div class="venta1">
                <div class="fon a">
                  <img class="img" src="<?php echo $r->path ?>">
                  <a href="<?php echo mklink($r->link) ?>">
                    <div class="mas">
                      <img src="assets/img/mas.png" alt="">
                    </div>
                  </a>
                </div>
                <div class="padding">
                  <h3><?php echo $r->nombre ?></h3>
                  <p>Elige la especialidad y al profesional que mejor se adapte a ti.</p>
                  <h4><?php echo $r->precio_final ?> €</h4>          
                  <a href="<?php echo mklink($r->link) ?>" class="btn">Comprar ahora</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="tac mt40">
      <a href="<?php echo mklink('web/activacion/')?>">
        <div class="row">
          <div class="col-sm-12 p">
            <button href="<?php echo mklink('web/activacion/')?>" class="cuadrado prdlis">
              <h6>¿Ya tienes algunos de estos productos?</h6>
              <span>¡Actívalo haciendo click aquí!</span>
            </button>
          </div>
        </div>
      </a>
    </div>

  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>

</body>
</html>