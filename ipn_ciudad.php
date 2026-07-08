<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
if (function_exists("register_shutdown_function")) {
  register_shutdown_function( "fatal_handler" );
}

function fatal_handler() {
  $errfile = "unknown file";
  $errstr  = "shutdown";
  $errno   = E_CORE_ERROR;
  $errline = 0;
  $error = error_get_last();
  if($error !== NULL) {
    $errno   = $error["type"];
    $errfile = $error["file"];
    $errline = $error["line"];
    $errstr  = $error["message"];
    log_error(format_error($errno, $errstr, $errfile, $errline));
  }
}

function format_error( $errno, $errstr, $errfile, $errline ) {
  $trace = print_r(debug_backtrace(false),true);
  $content = "
  <table>
  <thead><th>Item</th><th>Description</th></thead>
  <tbody>
  <tr>
    <th>Error</th>
    <td><pre>$errstr</pre></td>
  </tr>
  <tr>
    <th>Errno</th>
    <td><pre>$errno</pre></td>
  </tr>
  <tr>
    <th>File</th>
    <td>$errfile</td>
  </tr>
  <tr>
    <th>Line</th>
    <td>$errline</td>
  </tr>
  <tr>
    <th>Trace</th>
    <td><pre>$trace</pre></td>
  </tr>
  </tbody>
  </table>";
  return $content;
}

function log_error($body) {
  $log_file = "log_ipn_ciudad.txt";
  file_put_contents($log_file, date("Y-m-d H:i:s"), FILE_APPEND);
  file_put_contents($log_file,$body,FILE_APPEND);
}

// Si no esta definido el ID, devolvemos ERROR
if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
  http_response_code(400);
  return;
}

log_error(print_r($_GET,true));

header("HTTP/1.1 200 OK");
include_once("models/mercadopago.php");
include_once("models/Web_Model.php");
include_once("models/Auto_Model.php");
include_once("models/Propiedad_Model.php");
include_once("models/Articulo_Model.php");
$web_model = new Web_Model($empresa->id,$conx);
$auto_model = new Auto_Model($empresa->id,$conx);
$propiedad_model = new Propiedad_Model($empresa->id,$conx);
$articulo_model = new Articulo_Model($empresa->id,$conx);

$mp = $auto_model->get_mercadopago();
if ($mp === FALSE) return;

// Get the payment and the corresponding merchant_order reported by the IPN.
if($_GET["topic"] == 'payment') {

  $payment_info = $mp->get("/collections/notifications/" . $_GET["id"]);
  $merchant_order_info = $mp->get("/merchant_orders/" . $payment_info["response"]["collection"]["merchant_order_id"]);

  log_error(print_r($payment_info["response"],true));

  include_once("sistema/application/libraries/Mandrill/Mandrill.php");

  if ($merchant_order_info["status"] == 200) {
    // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items 
    $paid_amount = 0;

    foreach ($merchant_order_info["response"]["payments"] as  $payment) {
      if ($payment['status'] == 'approved'){
        $paid_amount += $payment['transaction_amount'];
      }  
    }

    if($paid_amount >= $merchant_order_info["response"]["total_amount"]) {
        
      // La external reference tiene el TIPO y ID del ANUNCIO
      $id_anuncio = $payment_info["response"]["collection"]["external_reference"];
      if (strpos($id_anuncio, "_") === FALSE) {
        log_error("Problema con external_reference $id_anuncio");
        exit();
      }
      $ids = explode("_", $id_anuncio);
      $tipo_anuncio = $ids[0];
      $id = $ids[1];
      $email_cliente = "";
      $nombre_cliente = "";

      // Si el tipo de anuncio es un auto
      if ($tipo_anuncio == "A") {
        $auto = $auto_model->get($id);
        if (empty($auto)) {
          log_error("Error al obtener el auto [$id]");
          exit();
        }
        $nombre_cliente = $auto->cliente;
        $email_cliente = $auto->cliente_email;
        $sql = "UPDATE veh_autos SET destacado = 1 ";
        $sql.= "WHERE id_empresa = $empresa->id AND id = $id ";
        mysqli_query($conx,$sql);

      // El tipo de anuncio es una propiedad
      } else if ($tipo_anuncio == "P") {
        $propiedad = $propiedad_model->get($id);
        if (empty($propiedad)) {
          log_error("Error al obtener el propiedad [$id]");
          exit();
        }
        $nombre_cliente = $propiedad->cliente;
        $email_cliente = $propiedad->cliente_email;
        $sql = "UPDATE inm_propiedades SET destacado = 1 ";
        $sql.= "WHERE id_empresa = $empresa->id AND id = $id ";
        mysqli_query($conx,$sql);

      // El tipo de anuncio es un producto
      } else if ($tipo_anuncio == "O") {
        $articulo = $articulo_model->get($id,array(
          "buscar_clientes"=>1,
        ));
        if (empty($articulo)) {
          log_error("Error al obtener el articulo [$id]");
          exit();
        }
        foreach($articulo->clientes as $c) {
          $nombre_cliente = $c->nombre;
          $email_cliente = $c->email;
        }
        $sql = "UPDATE articulos SET destacado = 1 ";
        $sql.= "WHERE id_empresa = $empresa->id AND id = $id ";
        mysqli_query($conx,$sql);

      }

      // FINALMENTE:
      // Enviamos un email al cliente que la compra fue exitosa
      if (!empty($email_cliente)) {
        $texto = $web_model->get_email("compra-ok");
        if (empty($texto->nombre)) $texto->nombre = "Compra Exitosa";
        if (empty($texto->texto)) $texto->texto = "Muchas gracias por su compra!";
        $body = $texto->texto;
        $body = str_replace("{{cliente}}",$nombre_cliente,$body);

        mandrill_send(array(
          "from_name"=>$empresa->nombre,
          "reply_to"=>$empresa->email,
          "to"=>$email_cliente,
          "to_name"=>$nombre_cliente,
          "bcc"=>"basile.matias99@gmail.com",
          "subject"=>$texto->nombre,
          "body"=>$body,
        ));
      }

    }
  }
}
?>