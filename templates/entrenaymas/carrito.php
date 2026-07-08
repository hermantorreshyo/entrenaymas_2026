<?php
include_once("includes/init.php");

include_once("models/mercadopago.php");

$numero_carrito = 0;
$mp = $carrito_model->get_mercadopago($numero_carrito);
if ($mp === FALSE) $ocultar_costo_envio = 1;

$costo_envio = 0;
$cliente = FALSE;
if (isset($_COOKIE["id_cliente"]) && !empty($_COOKIE["id_cliente"])) {
  	// Tomamos los datos del cliente
	$cliente = $cliente_model->get($_COOKIE["id_cliente"]);
}

// Si la tienda esta configurada para que solo compren los usuarios registrados
// y todavia no se registro un usuario, lo pateamos al login
//echo "[$empresa->tienda_compra_solo_usuarios]";
if ($empresa->tienda_compra_solo_usuarios == 1 && $cliente === FALSE) {
  header("Location: ".mklink("login/"));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php"); ?>
<meta name="robots" content="noindex" />
<script type="text/javascript" src="js/jquery.min.js"></script> 
</head>
<body class="checkout-page">
<?php include("includes/".$empresa->template_header.".php"); ?>
<?php include("templates/comun/carrito.php"); ?>
<?php include("includes/newsletter.php"); ?>
<?php include("includes/footer.php"); ?>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLtJGodU9yTjGDl0a0oB6h1zjR8qfXmSM&libraries=places" async defer></script>
</body>
</html>