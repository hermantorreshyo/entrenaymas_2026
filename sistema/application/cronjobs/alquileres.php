<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
set_time_limit(0);
include("../../params.php");
$hoy = date("Y-m-d");
$hora = date("H:i:s");

$sql = "SELECT A.*, ";
$sql.= " AC.numero, AC.monto, AC.expensa, AC.pagada, AC.vencimiento, AC.corresponde_a, ";
$sql.= " P.nombre AS propiedad, CONCAT(P.calle,' ',P.altura,' ') AS direccion, ";
$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
$sql.= " IF(PO.nombre IS NULL,'',PO.nombre) AS propietario, ";
$sql.= " IF(PO.email IS NULL,'',PO.email) AS propietario_email, ";
$sql.= " C.nombre AS cliente, C.email AS cliente_email ";
$sql.= "FROM inm_alquileres_cuotas AC ";
$sql.= " INNER JOIN inm_alquileres A ON (AC.id_alquiler = A.id) ";
$sql.= " INNER JOIN clientes C ON (A.id_cliente = C.id) ";
$sql.= " INNER JOIN inm_propiedades P ON (A.id_propiedad = P.id) ";
$sql.= " LEFT JOIN inm_propietarios PO ON (P.id_propietario = PO.id) ";
$sql.= " LEFT JOIN com_localidades L ON (P.id_localidad = L.id) ";
$sql.= " INNER JOIN empresas E ON (A.id_empresa = E.id) ";
$sql.= "WHERE pagada = 0 ";
$sql.= " AND vencimiento = '$hoy' ";
$q = mysqli_query($conx,$sql);
// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q)<=0) exit();

while(($cuota = mysqli_fetch_object($q))!==NULL) {

	// Primero controlamos que ya no exista un remito para la misma cuota
	$sql = "SELECT * ";
	$sql.= "FROM facturas ";
	$sql.= "WHERE id_referencia = $cuota->id ";
	$sql.= "AND numero_referencia = $cuota->numero ";
	$q_fact = mysqli_query($conx,$sql);
	if (mysqli_num_rows($q_fact)>0) {
		// Ya hay una factura para esa cuota, continuamos
		continue;
	}

	// Creamos los remitos automáticos
	$remito = new stdClass();
	$sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
	$sql.= "FROM facturas ";
	$sql.= "WHERE id_tipo_comprobante = 999 ";
	$sql.= "AND id_empresa = $cuota->id_empresa ";
	$q_remito = mysqli_query($conx,$sql);
	$row = mysqli_fetch_object($q_remito);
	$numero_remito = $row->numero + 1;

	$comprobante = "R 0001-".str_pad($numero_remito, 8, "0", STR_PAD_LEFT);
	$hash = md5($cuota->id_empresa.$comprobante);		

	// Estas variables:
	//   id_referencia = id_alquiler
	//   numero_referencia = numero de cuota
	// Sirven al momento del pago, poder identificar que cuota se esta pagando
	$sql = "INSERT INTO facturas (";
	$sql.= " id_empresa, fecha, hora, punto_venta, numero, comprobante, ";
	$sql.= " id_cliente, id_tipo_comprobante, total, subtotal, ";
	$sql.= " tipo_pago, estado, hash, id_referencia, numero_referencia ";
	$sql.= ") VALUES (";
	$sql.= " '$cuota->id_empresa', '$hoy', '$hora', 1, $numero_remito, '$comprobante', ";
	$sql.= " '$cuota->id_cliente', '999', $cuota->monto, $cuota->monto,  ";
	$sql.= " 'C',1,'$hash', $cuota->id, $cuota->numero ";
	$sql.= ")";
	$q_factura = mysqli_query($conx,$sql);
	$id_remito = mysqli_insert_id($conx);

	// Insertamos una fila en el remito
	$detalle_item = "Alquiler de propiedad ubicada en ";
	$detalle_item.= $cuota->direccion.", ".$cuota->localidad.", correspondiente a ";
	$detalle_item.= $cuota->corresponde_a.".";
	$sql = "INSERT INTO facturas_items (";
	$sql.= " id_empresa, id_factura, cantidad, porc_iva, id_tipo_alicuota_iva, ";
	$sql.= " neto, precio, nombre, iva, total_sin_iva, total_con_iva ";
	$sql.= ") VALUES (";
	$sql.= " $cuota->id_empresa, $id_remito, 1, 0, 0, ";
	$sql.= " $cuota->monto, $cuota->monto, '$detalle_item', 0, $cuota->monto, $cuota->monto ";
	$sql.= ")";
	mysqli_query($conx,$sql);

	// Enviamos un email al cliente
}
echo "TERMINO";
?>