<?php
include("includes/init.php");
$nombre_pagina = "servicios";
if (isset($_GET["f"])){ 
  $profesional_model->aceptar_caja_profesional($_GET["f"], $empresa->id);
} 
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
      <li>Caja Regalo</li>
    </ul>
  </div>
</section>

<!-- Psweb Blog Wrap -->
<section class="psweb-blog-wrap">
  <div class="container mt30">

    <div class="activacion-wrapper">
      <h3>¡Hemos registrado tu servicio para la Caja Regalo de Entrenaymas!</h3>
    </div>

  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>
</body>
</html>