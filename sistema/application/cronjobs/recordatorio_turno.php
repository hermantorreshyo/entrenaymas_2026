<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
include("../../params.php");
include("../libraries/Mandrill/Mandrill.php");
$now = date("Y-m-d H:i:s");

// Envia un email recordando el turno
// PERIODICIDAD: 1 HORA

$sql = "SELECT F.*, ";
$sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, ";
$sql.= " DATE_FORMAT(F.hora,'%H:%i') AS hora, ";
$sql.= " U.nombre AS usuario, ";
$sql.= " E.nombre AS empresa, E.email AS empresa_email, ";
$sql.= " C.nombre AS cliente, C.email, S.nombre AS servicio ";
$sql.= "FROM turnos F ";
$sql.= " INNER JOIN empresas E ON (F.id_empresa = E.id) ";
$sql.= " INNER JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
$sql.= " INNER JOIN turnos_servicios S ON (F.id_servicio = S.id AND F.id_empresa = S.id_empresa) ";
$sql.= " INNER JOIN usuarios U ON (F.id_empresa = U.id_empresa AND F.id_usuario = U.id) ";
$sql.= "WHERE F.notificado = 0 ";
$sql.= "AND (TIME_TO_SEC(TIMEDIFF(CONCAT(F.fecha,' ',F.hora),'$now')) / 3600) <= 1.2 AND (TIME_TO_SEC(TIMEDIFF(CONCAT(F.fecha,' ',F.hora),'$now')) / 3600) > 0 ";
$q = mysqli_query($conx,$sql);

// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q)<=0) exit();

while(($row = mysqli_fetch_object($q))!==NULL) {

  // Controlamos que tenga email cargado
  if (empty($row->email)) continue;

  // Seleccionamos el template del email que tenemos que mandar
  $sql = "SELECT * FROM crm_emails_templates WHERE id_empresa = $row->id_empresa AND clave = 'turno-aviso' ";
  $qq = mysqli_query($conx,$sql);
  if (mysqli_num_rows($qq) == 0) continue;
  $template = mysqli_fetch_object($qq);

	// Tomamos el texto
	$texto = $template->texto;
  $texto = str_replace("{{cliente}}",$row->cliente,$texto);
  $texto = str_replace("{{servicio}}",$row->servicio,$texto);
  $texto = str_replace("{{profesional}}",$row->usuario,$texto);
  $texto = str_replace("{{fecha}}",$row->fecha,$texto);
  $texto = str_replace("{{hora}}",$row->hora,$texto);
  $link = "https://www.entrenaymas.com/sistema/turnos/function/cancelar_turno/?id_empresa=$row->id_empresa&id=$row->id";
  $texto = str_replace("{{link_cancelar_turno}}",$link,$texto);

  $bcc = array();
  $bcc[] = "basile.matias99@gmail.com";
  /*
  $row->bcc_email = trim($row->bcc_email);
  if (!empty($row->bcc_email)) {
    $bcc_2 = explode(",", $row->bcc_email);  
    $bcc = array_merge($bcc,$bcc_2);
  }
  */

  // Mandamos el email
  mandrill_send(array(
    "to"=>$row->email,
    "to_name"=>$row->cliente,
    "from_name"=>$row->empresa,
    "reply_to"=>$row->empresa_email,
    "subject"=>$template->nombre,
    "bcc"=>$bcc,
    "body"=>$texto,
  ));

  $sql = "UPDATE turnos SET notificado = 1 WHERE id_empresa = $row->id_empresa AND id = $row->id ";
  mysqli_query($conx,$sql);

  /*
  // ENVIO AUTOMATICO DE CARRITO ABANDONADO
  $id_origen = 21;

  // Creamos la consulta
  $sql = "INSERT INTO crm_consultas (id_contacto,id_empresa,fecha,asunto,texto,id_origen,id_usuario,tipo,id_referencia) VALUES(";
  $sql.= "'$row->id_cliente','$row->id_empresa','$now','$row->asunto','$texto','$id_origen','$row->id_usuario','1','$row->id_factura') ";
  mysqli_query($conx,$sql);
  */
}
?>