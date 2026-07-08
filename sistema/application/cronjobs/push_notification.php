<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_empresa = isset($_GET["id_empresa"]) ? (int)$_GET["id_empresa"] : 0;
if ($id_empresa == 0) die("No hay id_empresa");

$id_usuario = isset($_GET["id_usuario"]) ? (int)$_GET["id_usuario"] : 0;

$subject = ($id_empresa == 571) ? 'Toque' : 'Varcreative';
$texto = isset($_GET["texto"]) ? urldecode($_GET["texto"]) : "";
$image = isset($_GET["image"]) ? urldecode($_GET["image"]) : "";
$link = isset($_GET["link"]) ? urldecode($_GET["link"]) : "";
$json = json_encode(array(
  "id_empresa"=>$id_empresa,
  "title"=>$subject,
  "texto"=>$texto,
  "image"=>$image,
  "link"=>$link,
));

include("../../params.php");
include("../../../vendor/autoload.php");
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$auth = [
  'GCM' => 'MY_GCM_API_KEY', // deprecated and optional, it's here only for compatibility reasons
  'VAPID' => [
    'subject' => $subject, // can be a mailto: or your website address
    'publicKey' => 'BG4hDy_0netdNoxxKir3Z6hGS-5HY5EZgRfXbIpsvfWM78Bc-cZzwyW5UqnNAWnSdF8tcYalaBcHRiYaqByWjnA', // (recommended) uncompressed public key P-256 encoded in Base64-URL
    'privateKey' => 'oBOwB0PnPCOciIaLJtcRM7wNYbpVQPaDrEOdJ_qXWNc', // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
  ],
];

$sql = "SELECT * FROM notif_usuarios ";
$sql.= "WHERE id_empresa = $id_empresa ";
if (!empty($id_usuario)) $sql.= "AND id_usuario = $id_usuario ";
$q = mysqli_query($conx,$sql);
$endpoints = array();
while(($r=mysqli_fetch_object($q))!=NULL) {
  $endpoints[] = $r->endpoint;
}
$subscription = Subscription::create($endpoints);
$webPush = new WebPush($auth);
$res = $webPush->sendNotification(
  $subscription,
  "TEST",
);
/*

  $webPush->sendNotification(
    $subscription,
    $json,
    $r->user_public_key,
    $r->user_auth_key
  );
*/
$webPush->flush();
echo $sql;
?>