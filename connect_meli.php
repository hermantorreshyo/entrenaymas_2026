<?php
require_once 'models/meli.php';
require_once 'sistema/params.php';

// Guarda los tokens para volverlos a reutilizar mas tarde
function guardar_tokens($array=array()) {
  global $conx;
  $id_empresa = $array["id_empresa"];
  $sql = "UPDATE web_configuracion SET ";
  if (isset($array["access_token"])) $sql.= " ml_access_token = '".$array["access_token"]."', ";
  if (isset($array["refresh_token"])) $sql.= " ml_refresh_token = '".$array["refresh_token"]."', ";
  if (isset($array["expires_in"])) $sql.= " ml_expires_in = '".$array["expires_in"]."', ";
  if (isset($array["ml_user_id"])) $sql.= " ml_user_id = '".$array["ml_user_id"]."', ";
  $sql.= " id_empresa = $id_empresa ";
  $sql.= "WHERE id_empresa = $id_empresa ";
  mysqli_query($conx,$sql);
}

// Si estamos enviando los datos del articulo que queremos compartir
if (isset($_GET["id_empresa"])) $id_empresa = filter_var($_GET["id_empresa"]);

$sql = "SELECT * FROM web_configuracion WHERE id_empresa = $id_empresa ";
$q = mysqli_query($conx,$sql);
if (mysqli_num_rows($q)==0) {
  echo "EMPRESA NO VALIDA"; exit();
}
$empresa = mysqli_fetch_object($q);

$meli = new Meli(ML_APP_ID, ML_APP_SECRET, $empresa->ml_access_token, $empresa->ml_refresh_token);

if (isset($_GET["code"])) {

  // Obtenemos el usuario
  $user = $meli->authorize($_GET['code'], "https://www.varcreative.com/connect_meli.php?id_empresa=$id_empresa");
  $empresa->ml_access_token = $user['body']->access_token;
  $empresa->expires_in = time() + $user['body']->expires_in;
  $empresa->refresh_token = $user['body']->refresh_token;
  guardar_tokens(array(
    "access_token"=>$empresa->ml_access_token,
    "expires_in"=>$empresa->expires_in,
    "refresh_token"=>$empresa->refresh_token,
    "id_empresa"=>$id_empresa,
  ));

}

if (!empty($empresa->ml_access_token) && !empty($empresa->ml_expires_in)) {

  // Debemos controlar si el access token sigue siendo valido
  if($empresa->ml_expires_in < time()) {
    try {
      // Refrescamos el access token
      $refresh = $meli->refreshAccessToken();
      $empresa->ml_access_token = $refresh['body']->access_token;
      $empresa->expires_in = time() + $refresh['body']->expires_in;
      $empresa->refresh_token = $refresh['body']->refresh_token;
      guardar_tokens(array(
        "access_token"=>$empresa->ml_access_token,
        "expires_in"=>$empresa->expires_in,
        "refresh_token"=>$empresa->refresh_token,
        "id_empresa"=>$id_empresa,
      ));

    } catch (Exception $e) {
      echo $e->getMessage();
      exit();
    }
  }

  // ID de usuario MERCADOLIBRE
  $params = array('access_token'=>$empresa->ml_access_token);
  $response = $meli->get("/users/me", $params);
  // Guardamos el usuario de ML
  if ($response["httpCode"] == 200) {
    $body = $response["body"];
    guardar_tokens(array(
      "ml_user_id"=>$body->id,
      "id_empresa"=>$id_empresa,
    ));
    echo "Su cuenta de MercadoLibre esta sincronizada. Puede cerrar esta ventana.";
  }
  exit();

} else {

  // Redireccionamos automaticamente para que el usuario acepte los permisos de la aplicacion
  $url = $meli->getAuthUrl(
    "https://www.varcreative.com/connect_meli.php?id_empresa=$id_empresa", 
    Meli::$AUTH_URL['MLA']
  );
  header("Location: $url");

}
?>