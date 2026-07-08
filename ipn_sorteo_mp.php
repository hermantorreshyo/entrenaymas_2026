<?php
set_time_limit(0);

// Si no esta definido el ID, devolvemos ERROR
if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
  http_response_code(400);
  return;
}

file_put_contents("ipn_sorteo.txt",print_r($_GET,true),FILE_APPEND);

header("HTTP/1.1 200 OK");
include_once("models/mercadopago.php");
include_once("models/Web_Model.php");
include_once("models/Auto_Model.php");
include_once("models/Sorteo_Model.php");
include_once("models/Cliente_Model.php");
$web_model = new Web_Model($empresa->id,$conx);
$auto_model = new Auto_Model($empresa->id,$conx);
$sorteo_model = new Sorteo_Model($empresa->id,$conx);
$cliente_model = new Cliente_Model($empresa->id,$conx);

$mp = $auto_model->get_mercadopago();
if ($mp === FALSE) return;

// Get the payment and the corresponding merchant_order reported by the IPN.
if($_GET["topic"] == 'payment') {

  $payment_info = $mp->get("/collections/notifications/" . $_GET["id"]);
  $merchant_order_info = $mp->get("/merchant_orders/" . $payment_info["response"]["collection"]["merchant_order_id"]);

  file_put_contents("ipn_sorteo.txt",print_r($payment_info["response"],true),FILE_APPEND);

  include_once("sistema/application/libraries/Mandrill/Mandrill.php");

  if ($merchant_order_info["status"] == 200) {
    // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items 
    $paid_amount = 0;

    foreach ($merchant_order_info["response"]["payments"] as  $payment) {
      if ($payment['status'] == 'approved'){
        $paid_amount += $payment['transaction_amount'];
      }  
    }

    if($paid_amount >= $merchant_order_info["response"]["total_amount"]){
        
      // Actualizamos el estado del pago
      
      // La external reference tiene el ID del cliente
      $id_cliente = $payment_info["response"]["collection"]["external_reference"];
      $cliente = $cliente_model->get($id_cliente);

      // Obtenemos el sorteo activo
      $sorteo = $sorteo_model->get_activo();

      // No hay ningun sorteo activo, acreditamos un punto al cliente
      // para que cuando se cree un nuevo sorteo, pueda participar
      if ($sorteo === FALSE) {

        $sql = "UPDATE clientes SET saldo_inicial = saldo_inicial + 1 ";
        $sql.= "WHERE id_empresa = $empresa->id ";
        $sql.= "AND id = $cliente->id ";
        mysqli_query($conx,$sql);

        // FINALMENTE:
        // Enviamos un email al administrador que la compra fue exitosa
        $texto = $web_model->get_email("sorteo-ok-admin");
        if (empty($texto->nombre)) $texto->nombre = "Pago de cliente";
        if (empty($texto->texto)) $texto->texto = "La compra de {{cliente}} ha sido pagada.";
        $body = $texto->texto;
        $body = str_replace("{{cliente}}",$cliente->nombre,$body);

        mandrill_send(array(
          "to"=>$empresa->email,
          //"bcc"=>"basile.matias99@gmail.com",
          "subject"=>$texto->nombre,
          "body"=>$body,
        ));

      // Hay un sorteo activo, agregamos la participacion al cliente
      } else {

        // Calculamos un numero aleatorio para ese sorteo
        $numero = $sorteo_model->get_numero($sorteo->id,$sorteo->maximo);

        // Insertamos la participacion del cliente al sorteo
        $sql = "INSERT INTO custom_tdf_sorteos_clientes (id_empresa, id_sorteo, id_cliente, numero, fecha) VALUES(";
        $sql.= "$empresa->id, $sorteo->id, $id_cliente, $numero, NOW())";
        mysqli_query($conx,$sql);

        // FINALMENTE:
        // Enviamos un email al administrador que la compra fue exitosa
        $texto = $web_model->get_email("sorteo-ok-admin");
        if (empty($texto->nombre)) $texto->nombre = "Pago de cliente";
        if (empty($texto->texto)) $texto->texto = "La compra de {{cliente}} ha sido pagada.";
        $body = $texto->texto;
        $body = str_replace("{{cliente}}",$cliente->nombre,$body);

        mandrill_send(array(
          "to"=>$empresa->email,
          //"bcc"=>"basile.matias99@gmail.com",
          "subject"=>$texto->nombre,
          "body"=>$body,
        ));
        
        // Y un email al usuario con el link de su pedido
        $texto = $web_model->get_email("sorteo-ok");
        if (empty($texto->nombre)) $texto->nombre = "Compra Exitosa";
        if (empty($texto->texto)) $texto->texto = "Muchas gracias por su compra!";
        $body = $texto->texto;
        $body = str_replace("{{cliente}}",$cliente->nombre,$body);
        $body = str_replace("{{numero}}",$numero,$body);

        mandrill_send(array(
          "from_name"=>$empresa->nombre,
          "to"=>$cliente->email,
          "to_name"=>$cliente->nombre,
          //"bcc"=>"basile.matias99@gmail.com",
          "subject"=>$texto->nombre,
          "body"=>$body,
        ));
      }

      // -----------------------
      // GUARDAMOS LA VENTA

      // Obtenemos el proximo numero
      $sql = "SELECT IF(MAX(numero) IS NULL,1,MAX(numero)+1) AS proximo ";
      $sql.= "FROM facturas ";
      $sql.= "WHERE id_empresa = $empresa->id ";
      $q_remito = mysqli_query($conx,$sql);
      $remito = mysqli_fetch_object($q_remito);
      $numero_remito = $remito->proximo;

      $fecha = date("Y-m-d");
      $hora = date("H:i:s");
      $observaciones = "Participando del sorteo: $sorteo->titulo. Pago realizado con MercadoPago. ";
      $comprobante = "R 0000-".str_pad($numero_remito, 8, "0", STR_PAD_LEFT);
      $hash = md5($empresa->id.$comprobante);

      $sql = "INSERT INTO facturas (id_punto_venta,id_empresa,id_vendedor,";
      $sql.= " fecha,hora,id_tipo_comprobante,id_cliente,numero,comprobante,";
      $sql.= " total,subtotal,neto,iva,pagada,pago,id_referencia,";
      $sql.= " observaciones,id_localidad,localidad,direccion,hash,enviada,id_tipo_estado";
      $sql.= ") VALUES(";
      $sql.= " 0,'$empresa->id','$cliente->id_vendedor',";
      $sql.= " '$fecha','$hora',999,'$cliente->id','$numero_remito','$comprobante',";
      $sql.= " '$paid_amount','$paid_amount','$paid_amount',0,1,'-$paid_amount','$sorteo->id',";
      $sql.= " '$observaciones','$cliente->id_localidad','$cliente->localidad','$cliente->direccion','$hash',0,6 ";
      $sql.= ")";
      file_put_contents("factura_sorteo.txt", $sql);
      mysqli_query($conx,$sql);
      $id_factura = mysqli_insert_id($conx);

      // Guardamos el item
      $sql = "INSERT INTO facturas_items (";
      $sql.= " id_empresa, id_punto_venta, id_factura, cantidad, neto, precio, nombre, total_con_iva, total_sin_iva ";
      $sql.= ") VALUES (";
      $sql.= " $empresa->id, 0, $id_factura, 1, '$paid_amount', '$paid_amount', '$sorteo->titulo', '$paid_amount', '$paid_amount' ";
      $sql.= ")";
      file_put_contents("factura_item_sorteo.txt", $sql);
      mysqli_query($conx,$sql);


      // Le damos al cliente un mes para publicar sus clasificados
      $fecha_vencimiento = date('Y-m-d H:i:s', strtotime("+1 months"));
      $sql = "UPDATE clientes SET fecha_vencimiento = '$fecha_vencimiento' ";
      $sql.= "WHERE id_empresa = $empresa->id ";
      $sql.= "AND id = $cliente->id ";
      mysqli_query($conx,$sql);

    }
  }
}
?>