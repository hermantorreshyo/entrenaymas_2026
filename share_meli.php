<?php
require_once 'models/meli.php';
require_once 'sistema/params.php';

// Guarda los tokens para volverlos a reutilizar mas tarde
function guardar_tokens($array=array()) {
  global $conx;
  $access_token = $array["access_token"];
  $refresh_token = $array["refresh_token"];
  $expires_in = $array["expires_in"];
  $id_empresa = $array["id_empresa"];
  $sql = "UPDATE web_configuracion SET ";
  $sql.= " ml_access_token = '$access_token', ";
  $sql.= " ml_refresh_token = '$refresh_token', ";
  $sql.= " ml_expires_in = '$expires_in' ";
  $sql.= "WHERE id_empresa = $id_empresa ";
  mysqli_query($conx,$sql);
}

// Si estamos enviando los datos del articulo que queremos compartir
if (isset($_GET["id_articulo"])) {
  $id_articulo = filter_var($_GET["id_articulo"]);
  $id_empresa = filter_var($_GET["id_empresa"]);
}

$sql = "SELECT * FROM web_configuracion WHERE id_empresa = $id_empresa ";
$q = mysqli_query($conx,$sql);
if (mysqli_num_rows($q)==0) {
  echo "EMPRESA NO VALIDA"; exit();
}
$empresa = mysqli_fetch_object($q);

$meli = new Meli(ML_APP_ID, ML_APP_SECRET, $empresa->ml_access_token, $empresa->ml_refresh_token);

if (isset($_GET["code"])) {

  // Obtenemos el usuario
  $user = $meli->authorize($_GET['code'], "https://varcreative.com/share_meli.php?id_empresa=$id_empresa&id_articulo=$id_articulo");
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

  // Debemos controlar si el access tokes sigue siendo valido
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
    }
  }

  require_once("models/Articulo_Model.php");
  $articulo_model = new Articulo_Model($id_empresa,$conx);
  $articulo = $articulo_model->get($id_articulo);
  $params = array('access_token' => $empresa->ml_access_token);

  // Predecimos la categoria en la cual vamos a poner el producto
  $response = $meli->get('/sites/MLA/category_predictor/predict?title='.urlencode($articulo->nombre), $params);
  print_r($response);
  exit();

  // Enviamos la informacion a MercadoLibre
  $body = array(
    "title"=>$articulo->nombre,
    "category_id"=>"MLA3530",
    "price"=>$articulo->precio_final_dto,
    "currency_id"=>"ARS",
    "available_quantity"=>$articulo->stock,
    "buying_mode"=>"buy_it_now",
    "listing_type_id"=>"free",
    "condition"=>"new",
    "description"=> "Item de test - No Ofertar",
    "pictures"=>array(
      array("source"=>"https://www.varcreative.com/sistema/uploads/".$articulo->path)
    )
  );
  $response = $meli->post('/items', $body, $params);

  if ($response["httpCode"] == 201) {
    $res = $response["body"];

    $articulo_model->guardar_articulo_meli(array(
      "id_articulo"=>$id_articulo,
      "id_empresa"=>$id_empresa,
      "id_meli"=>$res["id"],
      "permalink"=>$res["permalink"],
      "activo"=>1,
      "fecha_publicacion"=>date("Y-m-d H:i:s"),
    ));
  }
  file_put_contents("share_meli.txt",print_r($response,TRUE));

} else {

  // Redireccionamos automaticamente para que el usuario acepte los permisos de la aplicacion
  $url = $meli->getAuthUrl(
    "https://varcreative.com/share_meli.php?id_empresa=$id_empresa&id_articulo=$id_articulo", 
    Meli::$AUTH_URL['MLA']
  );
  header("Location: $url");

}
?>