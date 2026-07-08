<?php
set_time_limit(0);
include("../../params.php");
$now = date("Y-m-d");

$id_campania = isset($_GET["id"]) ? filter_var($_GET["id"],FILTER_SANITIZE_STRING) : 0;

$sql = "SELECT * FROM crm_campanias ";
$sql.= "WHERE metodo = 'S' "; // SMS
//$sql.= "AND DATE_FORMAT(fecha_inicio,'%Y-%m-%d') <= '$now' AND '$now' <= DATE_FORMAT(fecha_fin,'%Y-%m-%d') "; 	// Si esta en fecha
//$sql.= "AND comienzo_ejecucion = '0000-00-00 00:00:00' ";		// Si no se ejecuto
if (!empty($id_campania)) $sql.= "AND id = $id_campania ";
$q = mysqli_query($conx,$sql);

// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q)<=0) exit();

// Recorremos las campañas activas
while(($campania = mysqli_fetch_object($q))!==NULL) {

	// Obtenemos los destinatarios segun los filtros configurados
	$sql = "SELECT * ";
	if ($campania->destinatarios == "clientes") {
		$sql.= ", DATE_FORMAT(fecha_ult_operacion,'%d/%m/%Y') AS fecha_ult_operacion ";
	}
	$sql.= "FROM $campania->destinatarios ";
	$sql.= "WHERE id_empresa = $campania->id_empresa ";
  if ($campania->nombre == "Recordatorio") {
    $sql.= "AND observaciones = '25' ";
  }

	// EL TELEFONO DEL DESTINATARIO TIENE QUE ESTAR COMPLETO
	// 10 caracteres, sin el 0 y sin el 15
	$sql.= "AND CHAR_LENGTH(celular) = 10 ";

	// ENVIO DE MORA ARECOCARD
	if ($campania->id == 3) {
		$sql.= "AND saldo_inicial_2 > 50 ";
	}

	$sql.= "ORDER BY id DESC";

	$q_destinatarios = mysqli_query($conx,$sql);
	$total_destinatarios = mysqli_num_rows($q_destinatarios);

	// Si no hay ningun destinatario, continuamos
	if ($total_destinatarios <= 0) continue;

	// Tomamos el texto
	$texto = $campania->texto;
	$texto = str_replace("\r\n", "", $texto); // No puede haber enters
	$texto = str_replace("\n", "", $texto);	

	$bloque = "";
	while(($destinatario = mysqli_fetch_object($q_destinatarios))!==NULL) {
		// Reemplazamos los placeholders de los campos por sus valores
		$t = $texto;
		foreach ($destinatario as $key => $value) {
			$value = str_replace("#", "Ñ", $value);
			$value = trim($value);
			$t = str_replace("{{$key}}", $value, $t);
		}
		$t = str_replace(",", " ", $t); // Sacamos las comas
		$t = str_replace("{", "", $t);
		$t = str_replace("}", "", $t);
    $t = html_entity_decode($t,ENT_QUOTES);
		$bloque.= $destinatario->celular.",".$destinatario->celular.",".$t."\n";	
	}

	//echo $bloque; exit();

	// GATEWAY UTILIZADO: SMS MASIVOS
	$url = 'http://servicio.smsmasivos.com.ar/enviar_sms_bloque.asp';
	$fields = array(
		'usuario' => "ARECOCARD",
		'clave' => "CARD152",
		'separadorcampos' => "coma",
		'bloque' => urlencode($bloque),
	);

	$fields_string = "";
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	$result = curl_exec($ch);
	curl_close($ch);
	echo $result;

	// Si se ejecuto todo bien

	// Marcamos que la campaña ya se ejecuto
  $f_tar = date("Y-m-d H:i:s");
	$sql = "UPDATE crm_campanias SET ";
	$sql.= " total_enviados = $total_destinatarios, ";
	$sql.= " comienzo_ejecucion = '$f_tar', fin_ejecucion = '$f_tar', ";
	$sql.= " resultado_ejecucion = 'S' "; // Success
	$sql.= "WHERE id = $campania->id ";
	// mysqli_query($conx,$sql);

	// Guardamos la proxima programada

}


?>
