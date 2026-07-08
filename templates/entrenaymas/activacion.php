<?php
include("includes/init.php");
$nombre_pagina = "activacion";
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

    <div class="activacion-wrapper">
      <h3>Tu experiencia empieza aquí...</h3>
      <span class="registra_caja db">REGISTRA TU CAJA</span> 
      <hr class="mt20 mb10">
      <p>Código de activación</p>
      <form action="<?php echo mklink('web/seleccion/')?>" method="post">
        <input id="codigo_activacion" name="codigo_activacion" type="text" required class="form-control activacion_input">
        <a href="javascript:void(0)" class="activacion_datos db">¿Dónde encuentro estos datos?</a>
        <?php if (isset($_GET["e"])){ ?>
          <div class="alerta">
            <span>Su codigo es invalido o ya fue utilizado</span>
          </div>
        <?php } ?>
        <button type="submit" class="activacion_continuar db">Continuar</button>
      </form>
    </div>

  </div>
</section>
<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>
</body>
</html>