<?php include("includes/init.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<?php include("includes/head.php") ?>
</head>
<body>

	<?php include("includes/header.php") ?>

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

	<section class="psweb-register">
	  <div class="container">
	    <div class="plans-title">
	      <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s">Su registro ha sido satisfactorio</h4>
	      <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">Registro Completado!</h2>

				<p>Puede iniciar sesion en el siguiente enlace:</p>
				<a href="<?php echo mklink("sistema/") ?>" class="psweb-btn">Iniciar Sesión</a>

	    </div>
	  </div>
	</section>

	<?php include("includes/newsletter.php") ?>

	<?php include("includes/footer.php") ?>

</body>
</html>