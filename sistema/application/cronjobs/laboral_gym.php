<?php
include("../../params.php");

$ultimo_id_file = "log_laboral_gym_ultimo_id.txt";
if (!file_exists($ultimo_id_file)) file_put_contents($ultimo_id_file, "0");
$ultimo_id = file_get_contents($ultimo_id_file);

// Seleccionamos la proxima noticia para enviar
$sql = "SELECT * FROM not_entradas ";
$sql.= "WHERE id_empresa = 341 ";
$sql.= "AND id_categoria = 410 "; // Solo se envia automatico GIMNASIA LABORAL
$sql.= "AND id > $ultimo_id ";
$sql.= "ORDER BY id ASC ";
$sql.= "LIMIT 0,1 ";
$query = mysqli_query($conx,$sql);
if (mysqli_num_rows($query) == 0) {
  $ultimo_id = "0";
  file_put_contents($ultimo_id_file, $ultimo_id);

  // Volvemos a hacer la consulta
  $sql = "SELECT * FROM not_entradas ";
  $sql.= "WHERE id_empresa = 341 ";
  $sql.= "AND id_categoria = 410 "; // Solo se envia automatico GIMNASIA LABORAL
  $sql.= "AND id > $ultimo_id ";
  $sql.= "ORDER BY id ASC ";
  $sql.= "LIMIT 0,1 ";
  $query = mysqli_query($conx,$sql);
  if (mysqli_num_rows($query) == 0) {
    file_put_contents("log_laboral_gym.txt", "Nada para enviar: ".$sql."\n", FILE_APPEND);
    exit();
  } else {
    $entrada = mysqli_fetch_object($query);
  }
} else {
  $entrada = mysqli_fetch_object($query);
}
print_r($entrada);

$fields = array(
  "id_entrada"=>$entrada->id,
  "controlar_horarios"=>1,
);

$fields_string = "";
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, 'https://www.varcreative.com/sistema/laboral_gym/function/notificar/');
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
$result = curl_exec($ch);
curl_close($ch);

// Ponemos como ultimo ID al siguiente
file_put_contents($ultimo_id_file, $entrada->id);
?>