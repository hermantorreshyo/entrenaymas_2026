<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
include("../../params.php");
include("../libraries/Mandrill/Mandrill.php");
$now = date("Y-m-d");
$id_empresa = 256;

// Entradas de hoy
$sql = "SELECT * FROM not_entradas WHERE id_empresa = $id_empresa ";
$sql.= "AND DATE_FORMAT(fecha,'%Y-%m-%d') = '$now' ";
$sql.= "AND activo = 1 ";
$sql.= "AND eliminada = 0 ";
$q = mysqli_query($conx,$sql);
if ($q === FALSE) exit();

// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q)<=0) exit();

// Template que vamos a enviar
$template = "";
$asunto = "";
$sql = "SELECT * FROM crm_emails_templates WHERE id_empresa = $id_empresa AND clave = 'email-categoria' ";
$q_template = mysqli_query($conx,$sql);
if (mysqli_num_rows($q_template)<=0) exit();
$template_row = mysqli_fetch_object($q_template);
$template = $template_row->texto;
$asunto = $template_row->nombre;

$bcc = array();
$bcc[] = "basile.matias99@gmail.com";
$bcc[] = "porcelp@gmail.com";
$total = 0;

$noticias = array();

while(($row = mysqli_fetch_object($q))!==NULL) {

  // Seleccionamos los clientes que vamos a enviarle
  $sql = "SELECT C.*, CAT.nombre AS categoria ";
  $sql.= "FROM not_categorias_clientes CC ";
  $sql.= "INNER JOIN clientes C ON (CC.id_empresa = C.id_empresa AND CC.id_cliente = C.id) ";
  $sql.= "INNER JOIN not_categorias CAT ON (CC.id_empresa = CAT.id_empresa AND CC.id_categoria = CAT.id) ";
  $sql.= "WHERE CC.id_empresa = $id_empresa ";
  $sql.= "AND CC.id_categoria = $row->id_categoria ";
  $qq = mysqli_query($conx,$sql);
  $seguidores = 0;
  while(($seg = mysqli_fetch_object($qq))!==NULL) {
    if (empty($seg->email)) continue;

    $template_real = $template;
    $template_real = str_replace("{{nombre}}", utf8_decode($seg->nombre), $template_real);
    $template_real = str_replace("{{titulo}}", utf8_decode($row->titulo), $template_real);
    $template_real = str_replace("{{categoria}}", utf8_decode($seg->categoria), $template_real);
    $template_real = str_replace("{{link}}", "https://www.millingandgrain.com/".$row->link, $template_real);
    // Mandamos el email
    mandrill_send(array(
      "to"=>$seg->email,
      "from_name"=>"Milling and Grain",
      "subject"=>$asunto,
      "body"=>$template_real,
    ));
    $total++;
    $seguidores++;
  }
  $noticias[] = array(
    "nombre"=>$row->titulo,
    "cantidad"=>$seguidores,
  );
}

$body = "<table>";
foreach($noticias as $edit) {
  $body.= "<tr>";
  $body.= "<td>".utf8_decode($edit["nombre"])."</td>";
  $body.= "<td>: Cantidad de Emails: ".$edit["cantidad"]."</td>";
  $body.= "</tr>";
}
$body.= "</table>";
if ($total > 0) {
  // Mandamos el email
  mandrill_send(array(
    "to"=>$bcc,
    "from_name"=>"Milling and Grain",
    "subject"=>"Resumen de envio de seguidores",
    "body"=>$body,
  ));
}
echo $body;
?>