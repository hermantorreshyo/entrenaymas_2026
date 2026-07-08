<?php
set_time_limit(0);
include_once("models/mercadopago.php");
include_once("models/Web_Model.php");
include_once("models/Viaje_Model.php");
$web_model = new Web_Model($empresa->id,$conx);
$viaje_model = new Viaje_Model($empresa->id,$conx);

$mp = $viaje_model->get_mercadopago();
if ($mp === FALSE) return;

// Get the payment and the corresponding merchant_order reported by the IPN.
if(isset($_GET["topic"]) && $_GET["topic"] == 'payment'){
  $payment_info = $mp->get("/collections/notifications/" . $_GET["id"]);
  $merchant_order_info = $mp->get("/merchant_orders/" . $payment_info["response"]["collection"]["merchant_order_id"]);
// Get the merchant_order reported by the IPN.
} else if(isset($_GET["topic"]) && $_GET["topic"] == 'merchant_order'){
  $merchant_order_info = $mp->get("/merchant_orders/" . $_GET["id"]);
}

if (isset($payment_info)) {

  file_put_contents("ipn_viaje.txt",print_r($payment_info["response"],true));  

  include_once("sistema/application/libraries/Mandrill/Mandrill.php");

  if (isset($merchant_order_info) && $merchant_order_info["status"] == 200) {

    // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items 
    $paid_amount = 0;
    $pago_pendiente = 0;

    // Sumamos cada pago realizado
    foreach ($merchant_order_info["response"]["payments"] as  $payment) {
      if ($payment['status'] == 'approved'){
        $paid_amount += $payment['transaction_amount'];
      } else if ($payment['status'] == 'pending') {
        $pago_pendiente += $payment['transaction_amount'];
      }
    }

    // La external reference tiene el ID del pedido
    $id_pedido = $payment_info["response"]["collection"]["external_reference"];
    
    // Obtenemos los datos del pedido
    $sql_pedido = "SELECT R.*, ";
    $sql_pedido.= " IF(R.fecha_reserva = '0000-00-00 00:00:00','',DATE_FORMAT(R.fecha_reserva,'%d/%m/%Y')) AS fecha_reserva, ";
    $sql_pedido.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql_pedido.= " IF(C.email IS NULL,'',C.email) AS cliente_email, ";
    $sql_pedido.= " IF(V.nombre IS NULL,'',V.nombre) AS viaje ";
    $sql_pedido.= "FROM via_reservas R ";
    $sql_pedido.= "LEFT JOIN clientes C ON (R.id_cliente = C.id AND R.id_empresa = C.id_empresa) ";
    $sql_pedido.= "LEFT JOIN via_viajes V ON (R.id_viaje = V.id AND R.id_empresa = V.id_empresa) ";
    $sql_pedido.= "WHERE R.id = $id_pedido AND R.id_empresa = $empresa->id ";
    $q = mysqli_query($conx,$sql_pedido);

    if ($q === FALSE) {
      error_mail($sql_pedido);
      exit();
    }
    $pedido = mysqli_fetch_object($q);

    // Si hay algun pago en estado pendiente, ponemos el pedido en PENDIENTE DE PAGO
    if ($pago_pendiente > 0) {

      // Cambiamos el estado a PENDIENTE DE PAGO
      $sql = "UPDATE via_reservas SET ";
      $sql.= "id_tipo_estado = 3 ";
      $sql.= "WHERE id = $id_pedido AND id_empresa = $empresa->id ";
      mysqli_query($conx,$sql);

      // Enviamos un email al administrador para avisarle que hay un pedido pendiente de pago
      $texto = $web_model->get_email("reserva-viaje-pendiente-admin");
      if (empty($texto->nombre)) $texto->nombre = "Pago pendiente";
      if (empty($texto->texto)) $texto->texto = "Se ha registrado una nueva compra de {{cliente}}, y el pago se encuentra en estado pendiente.";
      $body = $texto->texto;
      $body = str_replace("{{cliente}}",$pedido->cliente,$body);

      mandrill_send(array(
        "to"=>$empresa->email,
        "bcc"=>"basile.matias99@gmail.com",
        "subject"=>$texto->nombre,
        "body"=>$body,
      ));      
      exit();

    // Si el pago completo el total que debia pagarse
    } else if($paid_amount >= $merchant_order_info["response"]["total_amount"]) {

      // Primero de todo, finalizamos el pedido y actualizamos los datos
      $sql = "UPDATE via_reservas SET ";
      $sql.= " id_tipo_estado = 6, "; // ESTADO FINALIZADO
      $sql.= " pagado = $paid_amount ";
      //$sql.= "codigo_autorizacion = '".$payment_info["response"]["collection"]["authorization_code"]."' ";
      $sql.= "WHERE id = $id_pedido AND id_empresa = $empresa->id ";
      mysqli_query($conx,$sql);

      $hoy = date("d/m/Y H:i");
      // Ponemos el pago que corresponde a esa reserva
      $sql = "INSERT INTO via_reservas_pagos (id_empresa,id_reserva,metodo,total,fecha) VALUES(";
      $sql.= "$empresa->id, $id_pedido, 'MercadoPago',$paid_amount,'$hoy')";
      mysqli_query($conx,$sql);
      
      // TODO: CREARIAMOS LA FACTURA ELECTRONICA Y EL PAGO ASOCIADO A LA MISMA, Y SE ENVIARIA AL CLIENTE
      
      // FINALMENTE:
      // Enviamos un email al administrador que la compra fue exitosa
      $clave_email = "reserva-viaje-ok-admin";
      $texto = $web_model->get_email($clave_email);
      if (empty($texto->nombre)) $texto->nombre = "Compra exitosa";
      if (empty($texto->texto)) $texto->texto = "La compra de {{cliente}} ha sido pagada.";
      $body = $texto->texto;
      $body = str_replace("{{cliente}}",$pedido->cliente,$body);

      mandrill_send(array(
        "to"=>$empresa->email,
        "from_name"=>$empresa->nombre,
        "bcc"=>"basile.matias99@gmail.com",
        "subject"=>$texto->nombre,
        "body"=>$body,
      ));
      
      // Y un email al usuario con el link de su pedido
      $clave_email = "reserva-viaje-ok";
      if ($pedido->idioma == "en") $clave_email = "reserva-viaje-ok-en";
      else if ($pedido->idioma == "pt") $clave_email = "reserva-viaje-ok-pt";
      $texto = $web_model->get_email($clave_email);
      if (empty($texto->nombre)) $texto->nombre = "Compra Exitosa";
      if (empty($texto->texto)) $texto->texto = "Muchas gracias por su compra!";
      $body = $texto->texto;
      $body = str_replace("{{cliente}}",$pedido->cliente,$body);
      $link_ver_pedido = mklink("sistema/reservas_asientos/function/voucher/".$pedido->id."/");
      $body = str_replace("{{link_ver_pedido}}",$link_ver_pedido,$body);
      $body = str_replace("'", "\"", $body);

      $array = array(
        "from_name"=>$empresa->nombre,
        "to"=>$pedido->cliente_email,
        "to_name"=>$pedido->cliente,
        "bcc"=>"basile.matias99@gmail.com",
        "subject"=>$texto->nombre,
        "body"=>$body,
      );
      file_put_contents("salida_MP.txt", print_r($array,TRUE));
      mandrill_send($array);

      // Guardamos el evento
      $texto_evento = "Estado: Pagado. Pago por MercadoPago";
      $id_origen = 13; // VIAJES
      // En id_referencia guardamos el viaje que contrato
      // En id_relacion guardamos el pedido realizado
      $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_referencia,id_contacto,texto,id_relacion) VALUES(";
      $sql.= "'$empresa->id',NOW(),'Reserva','$id_origen','$pedido->id_viaje','$pedido->id_cliente','$texto_evento','$pedido->id')";
      mysqli_query($conx,$sql);

      // Actualizamos el contacto con la ultima fecha de operacion
      $sql = "UPDATE clientes SET fecha_ult_operacion = NOW(), no_leido = 1 ";
      $sql.= "WHERE id = $pedido->id_cliente AND id_empresa = $empresa->id ";
      mysqli_query($conx,$sql);
          
    }
  }
}
?>