<?php
include_once("includes/init.php");

$id_factura = 0;
if (isset($_GET["external_reference"])) {
  $id_factura = filter_var($_GET["external_reference"],FILTER_SANITIZE_STRING);
}

$factura = $pedido_model->get($id_factura);
if ($factura === FALSE) {
  header("Location: /");
}

// En el numero de remito, tenemos el numero de carrito
$numero_carrito = $factura->numero_remito;
$carrito_model->borrar_carrito($numero_carrito);
$carrito = $carrito_model->get_carrito();

$forma_pago = "";
if (isset($_GET["payment_type"])) {
  $forma_pago = filter_var($_GET["payment_type"],FILTER_SANITIZE_STRING);
}

$estado_pago = "";
if (isset($_GET["collection_status"])) {
  $estado_pago = filter_var($_GET["collection_status"],FILTER_SANITIZE_STRING);
}

$tipo_pago = "";
if ($factura->id_tipo_estado == 9) {
  $tipo_pago = "Pago a convenir";
} else if ($factura->id_tipo_estado == 8) {
  $tipo_pago = "Pago en sucursal";
} else if ($factura->id_tipo_estado == 1 || $factura->id_tipo_estado == 3) {
  $tipo_pago = "Pago pendiente";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php"); ?>
<meta name="robots" content="noindex" />
</head>
<body class="checkout-page">
<?php include("includes/".$empresa->template_header.".php"); ?>
<?php include("templates/comun/compra_ok.php"); ?>
<?php include("includes/newsletter.php"); ?>
<?php include("includes/footer.php"); ?>
</body>
</html>
