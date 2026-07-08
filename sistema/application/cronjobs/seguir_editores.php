<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
include("../../params.php");
include("../libraries/Mandrill/Mandrill.php");
$now = date("Y-m-d");
$id_empresa = 256;

$sql = "SELECT * FROM not_entradas WHERE id_empresa = $id_empresa ";
$sql.= "AND DATE_FORMAT(fecha,'%Y-%m-%d') = '$now' ";
$sql.= "AND id_editor != 0 ";
$sql.= "AND activo = 1 ";
$sql.= "AND eliminada = 0 ";
$q = mysqli_query($conx,$sql);
if ($q === FALSE) exit();

// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q)<=0) exit();

$template = "";
$asunto = "";
$sql = "SELECT * FROM crm_emails_templates WHERE id_empresa = $id_empresa AND clave = 'email-editor' ";
$q_template = mysqli_query($conx,$sql);
if (mysqli_num_rows($q_template)<=0) exit();
$template_row = mysqli_fetch_object($q_template);
$template = $template_row->texto;
$asunto = $template_row->nombre;

$bcc = array();
$bcc[] = "basile.matias99@gmail.com";
$bcc[] = "porcelp@gmail.com";
$total = 0;

$editores = array();

while(($row = mysqli_fetch_object($q))!==NULL) {

  // Seleccionamos la relacion
  $sql = "SELECT C.*, EDI.nombre AS editor, EDI.id AS id_editor FROM not_editores_seguidores NES ";
  $sql.= "INNER JOIN not_editores EDI ON (EDI.id_empresa = NES.id_empresa AND EDI.id = NES.id_editor) ";
  $sql.= "INNER JOIN clientes C ON (NES.id_usuario = C.id AND NES.id_empresa = C.id_empresa) ";
  $sql.= "WHERE NES.id_empresa = $id_empresa ";
  $sql.= "AND NES.id_editor = $row->id_editor ";
  $qq = mysqli_query($conx,$sql);
  $seguidores = array();
  while(($seg = mysqli_fetch_object($qq))!==NULL) {
    if (empty($seg->email)) continue;

    $template_real = $template;
    $template_real = str_replace("{{nombre}}", $seg->nombre, $template_real);
    $template_real = str_replace("{{editor}}", $seg->editor, $template_real);
    $template_real = str_replace("{{titulo}}", $row->titulo, $template_real);
    $template_real = str_replace("{{link}}", "https://www.millingandgrain.com/".$row->link, $template_real);

    if (!isset($editores[$seg->id_editor])) {
      $editores[$seg->id_editor] = array(
        "cantidad"=>1,
        "nombre"=>$seg->editor,
      );
    } else {
      $editores[$seg->id_editor]["cantidad"] = $editores[$seg->id_editor]["cantidad"] + 1;
    }

    // Mandamos el email
    mandrill_send(array(
      "to"=>$seg->email,
      "from_name"=>"Milling and Grain",
      "subject"=>$asunto,
      "body"=>$template_real,
    ));
    $total++;
  }

}

$body = "<table>";
foreach($editores as $edit) {
  $body.= "<tr>";
  $body.= "<td>".$edit["nombre"]."</td>";
  $body.= "<td>Emails: ".$edit["cantidad"]."</td>";
  $body.= "</tr>";
}
$body.= "</table>";
// Mandamos el email
mandrill_send(array(
  "to"=>$bcc,
  "from_name"=>"Milling and Grain",
  "subject"=>"Resumen de envio de seguidores",
  "body"=>$body,
));
echo $body;
?>