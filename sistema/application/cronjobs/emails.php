<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set("America/Argentina/Buenos_Aires");

// A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
$filename = "sem_email.txt";
if (file_exists($filename) === FALSE) file_put_contents($filename, "");
$file = fopen($filename, "r+");
// Intenta adquirir un bloqueo exclusivo
if((flock($file, LOCK_EX | LOCK_NB) === FALSE)) exit();

include("../../params.php");
$fecha = date("Y-m-d");
$hora = date("H:i"); 
$dia_semana = date("N");
$id_campania = isset($_GET["id"]) ? filter_var($_GET["id"],FILTER_SANITIZE_STRING) : 0;

$sql = "SELECT * FROM crm_campanias ";
$sql.= "WHERE metodo = 'E' "; // Email
$sql.= "AND resultado_ejecucion = '' ";
$sql.= "AND comienzo_ejecucion = '0000-00-00 00:00:00' ";		// Si no se ejecuto
if (!empty($id_campania)) {
  // Estamos ejecutando una campania particular
  $sql.= "AND id = $id_campania ";
} else {
  // Es un envio programado
  $sql.= "AND hora = '$hora' AND ";
  if ($dia_semana == 1) $sql.= " lunes = 1 ";
  if ($dia_semana == 2) $sql.= " martes = 1 ";
  if ($dia_semana == 3) $sql.= " miercoles = 1 ";
  if ($dia_semana == 4) $sql.= " jueves = 1 ";
  if ($dia_semana == 5) $sql.= " viernes = 1 ";
  if ($dia_semana == 6) $sql.= " sabado = 1 ";
  if ($dia_semana == 7) $sql.= " domingo = 1 ";
  $sql.= "AND ((fecha_inicio <= '$fecha' AND '$fecha' <= fecha_fin) OR (fecha = '$fecha')) "; // Si esta en fecha
}
$q = mysqli_query($conx,$sql);
file_put_contents("log_emails.txt", $fecha." ".$hora." :".$sql."\n", FILE_APPEND);
if (mysqli_num_rows($q)<=0) exit(); // Si no hay nada para seleccionar, salimos

include("../libraries/Mandrill/Mandrill.php");

// Recorremos las campañas activas
while(($campania = mysqli_fetch_object($q))!==NULL) {

  $sql = "SELECT * FROM empresas WHERE id = $campania->id_empresa";
  $q_emp = mysqli_query($conx,$sql);
  $empresa = mysqli_fetch_object($q_emp);

	// Obtenemos los destinatarios segun los filtros configurados
  $campania->filtros = str_replace("'", '"', $campania->filtros);
  $filtros = json_decode($campania->filtros);
  $sqls = array();
  foreach($filtros as $f) {
    $sql = "(SELECT C.email, C.nombre, C.id ";
    $sql.= "FROM clientes C ";
    if ($f->table != "clientes") $sql.= "INNER JOIN $f->table T ON (C.id = T.id_cliente AND C.id_empresa = T.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $campania->id_empresa ";
    $sql.= "AND C.email != '' ";
    if (!empty($f->filtro)) {
      $f->filtro = str_replace("-", ",", $f->filtro);
      // Si son alumnos, estamos filtrando comisiones
      if ($f->table == "aca_alumnos") $sql.= "AND id_comision IN($f->filtro) ";
      // Si son tutores, tambien por comisiones
      else if ($f->table == "aca_tutores") $sql.= "AND EXISTS (SELECT * FROM aca_alumnos AA WHERE AA.id_empresa = C.id_empresa AND AA.id_comision IN ($f->filtro) AND AA.id_tutor = C.id) ";
      // Si son docentes, tambien por comisiones
      else if ($f->table == "aca_docentes") $sql.= "AND EXISTS (SELECT * FROM aca_clases AC WHERE AC.id_comision IN ($f->filtro) AND AC.id_empresa = C.id_empresa AND AC.id_docente = C.id) ";
    }
    $sql.= ") ";
    $sqls[] = $sql;
  }
  if (empty($sqls)) continue;
  $sql = implode(" UNION ",$sqls);

	$q_destinatarios = mysqli_query($conx,$sql);
	$total_destinatarios = mysqli_num_rows($q_destinatarios);

	// Si no hay ningun destinatario, continuamos
	if ($total_destinatarios <= 0) continue;

  // Actualizamos la fecha de inicio de ejecucion
  $ahora = date("Y-m-d H:i:s");
  mysqli_query($conx,"UPDATE crm_campanias SET comienzo_ejecucion = '$ahora' WHERE id = $campania->id AND id_empresa = $campania->id_empresa ");

	// Tomamos el texto
	$texto = $campania->texto;
  $texto = str_replace("'", "\"", $texto);

  // Eliminamos los emails repetidos
  $emails = array();
	while(($destinatario = mysqli_fetch_object($q_destinatarios))!==NULL) {
    $encontro = false;
    foreach($emails as $em) {
      if ($em->email == $destinatario->email) {
        $encontro = true;
        break;
      }
    }
    if (!$encontro) $emails[] = $destinatario;
	}
  foreach($emails as $destinatario) {

    // Mandamos el email
    mandrill_send(array(
      "to"=>$destinatario->email,
      "to_name"=>$destinatario->nombre,
      "from_name"=>$empresa->nombre,
      "reply_to"=>$empresa->email,
      "subject"=>$campania->nombre,
      "body"=>$texto,
    ));

    $f_tar = date("Y-m-d H:i:s");

    // Creamos la consulta
    $sql = "INSERT INTO crm_consultas (id_contacto,id_empresa,fecha,asunto,texto,id_origen,id_usuario,tipo) VALUES(";
    $sql.= " '$destinatario->id','$campania->id_empresa','$f_tar','$campania->nombre','$texto',5,'$campania->id_usuario',1) ";
    mysqli_query($conx,$sql);
  }

	// Si se ejecuto todo bien

	// Marcamos que la campaña ya se ejecuto
	$sql = "UPDATE crm_campanias SET ";
	$sql.= " total_enviados = $total_destinatarios, ";
	$sql.= " comienzo_ejecucion = NOW(), fin_ejecucion = NOW(), ";
	$sql.= " resultado_ejecucion = 'S' "; // Success
	$sql.= "WHERE id = $campania->id ";
  $sql.= "AND id_empresa = $campania->id_empresa ";
	mysqli_query($conx,$sql);
}
?>