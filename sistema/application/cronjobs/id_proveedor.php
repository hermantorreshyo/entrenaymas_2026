<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
include("../../params.php");

$sql = "UPDATE facturas_items FI INNER JOIN facturas F ON (F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta AND F.id = FI.id_factura) ";
$sql.= "SET FI.id_proveedor = (SELECT AP.id_proveedor FROM articulos_proveedores AP WHERE AP.id_empresa = FI.id_empresa AND AP.id_articulo = FI.id_articulo LIMIT 0,1) ";
$sql.= "WHERE FI.id_empresa = 249 AND FI.id_proveedor = 0 ";
mysqli_query($conx,$sql);

$sql = "UPDATE facturas_items FI INNER JOIN facturas F ON (F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta AND F.id = FI.id_factura) ";
$sql.= "SET FI.id_proveedor = (SELECT AP.id_proveedor FROM articulos_proveedores AP WHERE AP.id_empresa = FI.id_empresa AND AP.id_articulo = FI.id_articulo LIMIT 0,1) ";
$sql.= "WHERE FI.id_empresa = 868 AND FI.id_proveedor = 0 ";
mysqli_query($conx,$sql);
?>