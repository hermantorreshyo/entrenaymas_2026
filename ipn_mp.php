<?php
set_time_limit(0);

// Si no esta definido el ID, devolvemos ERROR
if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
  http_response_code(400);
  return;
}

http_response_code(200);

include_once("models/mercadopago.php");
include_once("models/Web_Model.php");
$web_model = new Web_Model($empresa->id,$conx);
include_once("models/Pedido_Model.php");
$pedido_model = new Pedido_Model($empresa->id,$conx);
include_once("models/Carrito_Model.php");
$carrito_model = new Carrito_Model($empresa->id,$conx);
include_once("models/Articulo_Model.php");
$articulo_model = new Articulo_Model($empresa->id,$conx);

// Emails que van como copia oculta
$bcc_emails = (($empresa->id != 571) ? "basile.matias99@gmail.com" : "").((isset($empresa->bcc_email) && !empty($empresa->bcc_email)) ? ",".$empresa->bcc_email : "");

$indice = 0;
if (strpos($dominio, "centerequipamientos") !== FALSE) {
  // Si es centerequipamientos, usamos la segunda configuracin
  $indice = 1;
}

// Si es el caso especial de VULCATIRES
if (isset($cuenta_vulcatires)) {
  $mp = $carrito_model->get_mercadopago_vulcatires(0,0,$cuenta_vulcatires);
} else {
  $mp = $carrito_model->get_mercadopago($indice);  
}


// Get the payment and the corresponding merchant_order reported by the IPN.
if(isset($_GET["topic"]) && $_GET["topic"] == 'payment'){
	$payment_info = $mp->get("/collections/notifications/" . $_GET["id"]);
	$merchant_order_info = $mp->get("/merchant_orders/" . $payment_info["response"]["collection"]["merchant_order_id"]);
// Get the merchant_order reported by the IPN.
} else if(isset($_GET["topic"]) && $_GET["topic"] == 'merchant_order'){
	$merchant_order_info = $mp->get("/merchant_orders/" . $_GET["id"]);
}

// La external reference tiene el ID del pedido
$salida = print_r($merchant_order_info["response"],true);
$salida.= print_r($_GET,true);
file_put_contents("ipn.txt",$salida,FILE_APPEND);

// Obtenemos el punto de venta utilizado para la web
$punto_venta = $articulo_model->get_punto_venta_web();

if (isset($merchant_order_info) && $merchant_order_info["status"] == 200) {

  include_once("sistema/application/libraries/Mandrill/Mandrill.php");

	// If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items 
	$paid_amount = 0;
  $pago_pendiente = 0;

  // Sumamos cada pago realizado
	foreach ($merchant_order_info["response"]["payments"] as  $payment) {
		if ($payment['status'] == 'approved'){
			$paid_amount += $payment['transaction_amount'];
		}	else if ($payment['status'] == 'pending') {
      $pago_pendiente += $payment['transaction_amount'];
    }
	}

  $id_pedido = $merchant_order_info["response"]["external_reference"];

  // ===================================
  // Controlamos si es una reserva de un hotel
  if (strpos($id_pedido, "SERVA_")>0) {

    include_once("models/Reserva_Model.php");
    $reserva_model = new Reserva_Model($empresa->id,$conx);
    $id_reserva = str_replace("RESERVA_", "", $id_pedido);
    $reserva = $reserva_model->get_reserva($id_reserva,array("id_empresa"=>$empresa->id));

    // Si el pago completa la totalidad de la reserva, y aun no ha sido pagada
    if ($paid_amount == $reserva->precio && $reserva->id_estado != 2) {

      // Actualizamos el estado de la reserva
      $sql = "UPDATE hot_reservas SET id_estado = 2 WHERE id = $reserva->id AND id_empresa = $empresa->id";
      mysqli_query($conx,$sql);

      // Ponemos que el cliente ya concreto una venta
      $sql = "UPDATE clientes C INNER JOIN hot_reservas H ON (C.id_empresa = H.id_empresa AND C.id = H.id_cliente) ";
      $sql.= "SET C.tipo = 0 ";
      $sql.= "WHERE id_empresa = $empresa->id AND H.id_reserva = $reserva->id ";
      mysqli_query($conx,$sql);

      // Email al administrador avisandole que se pago la reserva
      $texto = $web_model->get_email("pago-reserva");
      if (empty($texto->nombre)) $texto->nombre = "Pago de Reserva";
      if (empty($texto->texto)) $texto->texto = "La reserva nro: {{numero}}, de {{cliente}} ha sido pagada satisfactoriamente con MercadoPago.";
      $body = $texto->texto;
      $body = str_replace("{{cliente}}",$reserva->cliente->nombre,$body);
      $link_ver_reserva = mklink("sistema/reservas/function/imprimir/".$id_reserva."/");
      $body = str_replace("{{link_ver_reserva}}",$link_ver_reserva,$body);
      $body = str_replace("{{link_web}}",mklink("/"),$body);
      $body = str_replace("{{numero}}",$reserva->numero,$body);
      $body = str_replace("{{empresa}}",$empresa->nombre,$body);
      $body = str_replace("{{id_empresa}}",$empresa->id,$body);
      $body = str_replace("'", "\"", $body);

      mandrill_send(array(
        "to"=>$empresa->email,
        "subject"=>$texto->nombre,
        "body"=>$body,
        "bcc"=>$bcc_emails,
      ));
      exit();
    }

  }
  // ===================================


  $pedido = $pedido_model->get($id_pedido);

  // Si hay algun pago en estado pendiente, ponemos el pedido en PENDIENTE DE PAGO
  if ($pago_pendiente > 0) {

    // Cambiamos el estado a PENDIENTE DE PAGO
    $sql = "UPDATE facturas SET ";
    $sql.= "id_tipo_estado = 3, ";
    $sql.= "custom_10 = '".$_GET["id"]."' "; // Guardamos el ID de MercadoPago para consultar por las dudas la transaccion
    $sql.= "WHERE id = $id_pedido ";
    $sql.= "AND id_empresa = $empresa->id ";
    if ($punto_venta !== FALSE) $sql.= "AND id_punto_venta = $punto_venta->id ";
    mysqli_query($conx,$sql);

    // Enviamos un email al administrador para avisarle que hay un pedido pendiente de pago
    $texto = $web_model->get_email("email-compra-pendiente-admin");
    if (empty($texto->nombre)) $texto->nombre = "Compra pendiente";
    if (empty($texto->texto)) $texto->texto = "Se ha registrado una nueva compra de {{cliente}}, y esta pendiente de pago.";
    $body = $texto->texto;
    $body = str_replace("{{cliente}}",$pedido->cliente,$body);

    mandrill_send(array(
      "to"=>$empresa->email,
      "bcc"=>$bcc_emails,
      "subject"=>$texto->nombre,
      "body"=>$body,
    ));
    exit();

  // Si el pago completo el total que debia pagarse
  } else if($paid_amount >= $merchant_order_info["response"]["total_amount"]){

    /*
		if(count($merchant_order_info["response"]["shipments"]) > 0) { // The merchant_order has shipments
			if($merchant_order_info["response"]["shipments"][0]["status"] == "ready_to_ship"){
				print_r("Totally paid. Print the label and release your item.");
			}
		} else { // The merchant_order don't has any shipments
    */

    if (($empresa->id == 571 && $pedido->id_tipo_estado > 0) || ($empresa->id != 571 && $pedido->id_tipo_estado > 3)) {
      // El pago ya fue procesado, no volvemos a hacerlo
      /*
      mandrill_send(array(
        "to"=>'basile.matias99@gmail.com',
        "subject"=>'Pago duplicado',
        "body"=>"$empresa->id $id_pedido",
      ));
      */
      exit();
    }

		// Primero de todo, finalizamos el pedido y actualizamos los datos
		$sql = "UPDATE facturas SET ";
    if ($empresa->id == 571) {
      // TOQUE: No cambiamos el estado, solamente ponemos el total como que pago con tarjeta
      $sql.= "tarjeta = total, id_tipo_estado = 0, ";
    } else {
      $sql.= "id_tipo_estado = 4, "; // ESTADO: PAGO CON MERCADOPAGO
    }
    $sql.= "anulada = 0, ";
    $sql.= "custom_10 = '".$_GET["id"]."' "; // Guardamos el ID de MercadoPago para consultar por las dudas la transaccion
		//$sql.= "codigo_autorizacion = '".$payment_info["response"]["collection"]["authorization_code"]."' ";
		$sql.= "WHERE id = $id_pedido ";
    $sql.= "AND id_empresa = $empresa->id ";
    if ($punto_venta !== FALSE) $sql.= "AND id_punto_venta = $punto_venta->id ";

		mysqli_query($conx,$sql);


    // Para solucionar problema de ANACLETO con ventas repetidas pendientes
    // Anulamos todas las facturas del mismo cliente, que no sean las del pedido pagado
    // que coincidan en importe o sean 0
    if ($empresa->id != 571) {
      $sql = "UPDATE facturas SET anulada = 1 ";
      $sql.= "WHERE id_empresa = $empresa->id ";
      $sql.= "AND id_cliente = $pedido->id_cliente ";
      $sql.= "AND id != $id_pedido ";
      $sql.= "AND (total = $pedido->total OR total = 0) ";
      $sql.= "AND id_tipo_estado < 4 ";
      if ($punto_venta !== FALSE) $sql.= "AND id_punto_venta = $punto_venta->id ";
      mysqli_query($conx,$sql);
    }
		
		// TODO: CREARIAMOS LA FACTURA ELECTRONICA Y EL PAGO ASOCIADO A LA MISMA, Y SE ENVIARIA AL CLIENTE

		$sql = "SELECT * FROM env_configuracion WHERE id_empresa = $empresa->id";
		$q_env = mysqli_query($conx,$sql);
		$env_conf = mysqli_fetch_object($q_env);

    $peso_total = 0;
    // Obtenemos los productos del pedido para sumar su peso
    $sql = "SELECT A.peso, A.ancho, A.alto, A.profundidad, PI.cantidad, A.id, ";
    $sql.= " PI.id_opcion_1, PI.id_opcion_2, PI.id_opcion_3, PI.id_variante, A.usa_stock ";
    $sql.= "FROM facturas_items PI INNER JOIN articulos A ON (PI.id_articulo = A.id AND PI.id_empresa = A.id_empresa) ";
    $sql.= "WHERE PI.id_factura = $id_pedido ";
    $sql.= "AND PI.id_empresa = $empresa->id ";
    if ($punto_venta !== FALSE) $sql.= "AND PI.id_punto_venta = $punto_venta->id ";
    $qq = mysqli_query($conx,$sql);
    while(($a=mysqli_fetch_object($qq))!==NULL) {

      // Calculamos el paso total del pedido
      $peso_total = $peso_total + ($a->cantidad * $a->peso);

      // Si esta definido un punto de venta para la web, y hay que llevar el stock del articulo
      if ($punto_venta !== FALSE && $a->usa_stock == 1) {

        $sql = "SELECT S.* FROM stock S ";
        $sql.= "WHERE S.id_empresa = $empresa->id ";
        $sql.= "AND S.id_articulo = $a->id ";
        $sql.= "AND S.id_sucursal = $punto_venta->id_sucursal ";
        $q_stock = mysqli_query($conx,$sql);
        if (mysqli_num_rows($q_stock)>0) {

          // Obtenemos el stock actual
          $r_stock = mysqli_fetch_object($q_stock);  
          $saldo = (float)$r_stock->stock_actual - (float)$a->cantidad;

          // Descontamos el stock
          $sql = "UPDATE stock SET stock_actual = $saldo ";
          $sql.= "WHERE id_empresa = $empresa->id ";
          $sql.= "AND id_articulo = $a->id ";
          $sql.= "AND id_sucursal = $punto_venta->id_sucursal ";
          mysqli_query($conx,$sql);

          // Si es un articulo con variantes, ademas descontamos el stock de la variante
          if ($a->id_variante != 0) {

            // Descontamos el stock de la variante
            $sql = "UPDATE stock_variantes SET stock_actual = stock_actual - $a->cantidad ";   
            $sql.= "WHERE id_empresa = $empresa->id ";
            $sql.= "AND id_articulo = $a->id ";
            $sql.= "AND id_sucursal = $punto_venta->id_sucursal ";
            $sql.= "AND id_variante = $a->id_variante ";
            mysqli_query($conx,$sql);

            // Obtenemos el stock actual para asignarselo a la variable $saldo, y de esa manera
            // ingresarla en stock_movimientos
            $sql = "SELECT * FROM stock_variantes ";
            $sql.= "WHERE id_empresa = $empresa->id ";
            $sql.= "AND id_articulo = $a->id ";
            $sql.= "AND id_sucursal = $punto_venta->id_sucursal ";
            $sql.= "AND id_variante = $a->id_variante ";
            $q_variante = mysqli_query($conx,$sql);
            if (mysqli_num_rows($q_variante)>0) {
              $r_variante = mysqli_fetch_object($q_variante);
              $saldo = $r_variante->stock_actual;
            }

          }

        } else {

          $saldo = ((float)$a->cantidad) * -1;

          // No tiene definido todavia ningun stock, pero hay que llevarlo igual, asi que insertamos y queda en negativo
          $sql = "INSERT INTO stock (id_empresa,id_articulo,id_sucursal,stock_actual,stock_minimo,reservado) VALUES (";
          $sql.= " '$empresa->id','$a->id','$punto_venta->id_sucursal','$saldo',0,0 )";
          mysqli_query($conx,$sql);
          if ($a->id_variante != 0) {
            $sql = "INSERT INTO stock_variantes (id_empresa,id_articulo,id_variante,id_sucursal,stock_actual) VALUES (";
            $sql.= " '$empresa->id','$a->id','$a->id_variante','$punto_venta->id_sucursal','$saldo')";
            mysqli_query($conx,$sql);
          }
        }

        // Agregamos el movimiento
        $detalle = ((isset($pedido->comprobante)) ? "Venta Web: ".$pedido->comprobante : "Venta Web");
        $sql = "INSERT INTO stock_movimientos (id_sucursal,id_articulo,movimiento,fecha,cantidad,saldo,detalle,id_empresa) VALUES (";
        $sql.= " '$punto_venta->id_sucursal','$a->id','B',NOW(),'$a->cantidad','$saldo','$detalle','$empresa->id')";
        mysqli_query($conx,$sql);

        // En gastrober ademas llevamos el stock sumado de las sucursales en el propio articulo
        // TODO: Hacer esto configurable
        if ($empresa->id == 342) {
          $sql = "UPDATE articulos A SET ";
          $sql.= " A.stock = (SELECT IF(SUM(S.stock_actual) IS NULL,0,SUM(S.stock_actual)) AS stock FROM stock S WHERE S.id_empresa = $empresa->id AND S.id_articulo = $a->id LIMIT 0,1) ";
          $sql.= "WHERE A.id_empresa = $empresa->id AND A.id = $a->id ";
          mysqli_query($conx,$sql);
        }

      }
    }

    $numero_envio = "";
    $link_envio = "";

		if ($env_conf->forma_envio == "ANDREANI") {

			// El peso debe ser expresado en gramos
			$peso_total = $peso_total * 1000;
			
			// Si tenemos que conectarnos con ANDREANI
			if ($pedido->retirar_envio == 0) {
				$sql = "SELECT * FROM env_servicios_envio WHERE empresa = 'Andreani' AND activo = 1 AND id_empresa = $empresa->id ";
				$q = mysqli_query($conx,$sql);
				if (mysqli_num_rows($q)>0) {
					$servicio = mysqli_fetch_object($q);
					require_once 'sistema/application/libraries/Andreani/ConfirmarCompra.php';
					require_once 'sistema/application/libraries/Andreani/ImpresionDeConstancia.php';
					require_once 'sistema/application/libraries/Andreani/Andreani.php';
					
					if ($servicio->test == 0) {
						$usuario = $servicio->prod_usuario;
						$password = $servicio->prod_password;
						$cliente = $servicio->prod_cliente;
						$contrato = $servicio->prod_contrato;
					} else {
						$usuario = $servicio->test_usuario;
						$password = $servicio->test_password;
						$cliente = $servicio->test_cliente;
						$contrato = $servicio->test_contrato;
					}
					
					// Debemos separar la direccion de la altura
					$calle = $pedido->cliente_direccion;
					$altura = "0";
					$pos = strrpos(trim($pedido->cliente_direccion)," ");
					if ($pos>=0) {
						$calle = trim(substr($pedido->cliente_direccion,0,$pos));
						$altura = trim(substr($pedido->cliente_direccion,$pos));
				        if (!is_numeric($altura)) {
				            $calle = $pedido->cliente_direccion;
				            $altura = "0";                          
				        }
					}
					
					$request = new ConfirmarCompra();
					$request->setDatosTransaccion($contrato);
					$request->setDatosDestino($pedido->provincia,$pedido->localidad,$pedido->codigo_postal,$calle,$altura);
					// El "0" es porque el numero de documento es OBLIGATORIO y nosotros no lo registramos en la web
					$request->setDatosDestinatario($pedido->cliente,null,"DNI","0");
					$request->setDatosEnvio($peso_total);
					$andreani = new Andreani($usuario,$password,'prod');
					$response = $andreani->call($request);
					if($response->isValid()){
						$numero_envio = $response->getMessage()->ConfirmarCompraResult->NumeroAndreani;
						//$link_envio = "https://bpmwmbsrv.andreani.com:41443/ImprimirEtiquetas/".$numero_envio."/63770/0/95226143";
						if (!empty($numero_envio)) {
							
							$request = new ImpresionDeConstancia();
							$request->setNumeroDeEnvio($numero_envio);
							$response = $andreani->call($request);
							if($response->isValid()){
								$link_envio = $response->getMessage()->ImprimirConstanciaResult->ResultadoImprimirConstancia->PdfLinkFile;
							} else {
								
								// Me envio un email con el error
								$headers = "From:info@grupoanacleto.com.ar\r\n";
								$headers.= "MIME-Version: 1.0\r\n";
								$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
								$string = print_r($response,true)."<br/><br/><br/>";
								@mail("basile.matias99@gmail.com","ERROR ANDREANI",$string,$headers);
							}
						}

					} else {
						
						// Me envio un email con el error
						$headers = "From:info@grupoanacleto.com.ar\r\n";
						$headers.= "MIME-Version: 1.0\r\n";
						$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
						$string = print_r($response,true)."<br/><br/><br/>";
						$string.= "PESO TOTAL: $peso_total <br/><br/>";
						$string.= "SQL PEDIDO: $sql_pedido <br/><br/>";
						@mail("basile.matias99@gmail.com","ERROR ANDREANI",$string,$headers);
					}
					// Actualizamos los datos
					$sql = "UPDATE facturas SET ";
					$sql.= "numero_envio = '$numero_envio', ";
					$sql.= "link_envio = '$link_envio' ";
					$sql.= "WHERE id = $pedido->id ";
          $sql.= "AND id_empresa = $empresa->id ";
					mysqli_query($conx,$sql);
					
				}
			}

		} // Fin ANDREANI

		
		// FINALMENTE:
		// Configuramos para reemplazar los placeholders
    $config_placeholders = array(
      "cliente"=>(isset($pedido->cliente) ? $pedido->cliente : ""),
      "usuario"=>(isset($pedido->usuario) ? $pedido->usuario : ""),
      "comercio_direccion"=>(isset($pedido->usuario_direccion) ? $pedido->usuario_direccion : ""),
      "numero"=>(isset($pedido->comprobante) ? $pedido->comprobante : ""),
      "total"=>(isset($pedido->total) ? $pedido->total : ""),
      "total_con_envio"=>(isset($pedido->total) ? $pedido->total + $pedido->costo_envio : ""),
      "items"=>(isset($pedido->items) ? $pedido->items : ""),
      "direccion"=>(isset($pedido->direccion) ? $pedido->direccion : ""),
      "email"=>$pedido->email,
      "forma_pago"=>"MercadoPago",
      "costo_envio"=>(isset($pedido->costo_envio) ? $pedido->costo_envio : ""),
      "observaciones"=>(isset($pedido->observaciones) ? $pedido->observaciones : ""),
      "id_empresa"=>$empresa->id,
      "empresa"=>$empresa->nombre,
      "link_web"=>mklink("/"),
      "link_pedido"=>mklink("sistema/pedidos/function/ver_pdf/".$pedido->id."/"),
      "link_pedido_toque"=>(($empresa->id == 571) ? "https://www.toque.com.ar/web/finalizar/?id=".$pedido->id : ""),
      "id"=>$pedido->id,
    );

    if ($empresa->id == 1099) {

      // EPEP, DEPENDIENDO DEL PEDIDO QUE HIZO MANDAMOS EL EMAIL CORRESPONDIENTE
      foreach($pedido->items as $item) {
        $texto = $web_model->get_email("compra-ok-".$item->id_articulo);
        if ($texto->id != 0) {
          $texto = $carrito_model->replace_placeholder($texto,$config_placeholders);
          mandrill_send(array(
            "from_name"=>$empresa->nombre,
            "to"=>$pedido->cliente_email,
            "to_name"=>$pedido->cliente,
            "reply_to"=>$empresa->email, // TODO: Podria ser al email del vendedor
            "subject"=>$texto->nombre,
            "body"=>$texto->texto,
            "bcc"=>$bcc_emails,
          ));
        }
      }
      
    } else {

      // Enviamos un email al administrador que la compra fue exitosa
  		$texto = $web_model->get_email("email-compra-ok-admin");
      $texto = $carrito_model->replace_placeholder($texto,$config_placeholders);
  		if (empty($texto->nombre)) $texto->nombre = "Compra exitosa";
  		if (empty($texto->texto)) $texto->texto = "La compra de {{cliente}} ha sido pagada.";
  		$body = $texto->texto;
  		$body = str_replace("{{link_envio}}",$link_envio,$body);
  		$body = str_replace("{{numero_envio}}",$numero_envio,$body);
      mandrill_send(array(
        "to"=>$empresa->email,
        "from_name"=>$empresa->nombre,
        "bcc"=>$bcc_emails,
        "subject"=>$texto->nombre,
        "body"=>$body,
      ));		

  		// Y un email al usuario con el link de su pedido
  		$texto = $web_model->get_email("email-compra-ok");
      $texto = $carrito_model->replace_placeholder($texto,$config_placeholders);
      mandrill_send(array(
        "from_name"=>$empresa->nombre,
        "to"=>$pedido->cliente_email,
        "to_name"=>$pedido->cliente,
        "reply_to"=>$empresa->email, // TODO: Podria ser al email del vendedor
        "subject"=>$texto->nombre,
        "body"=>$texto->texto,
        "bcc"=>$bcc_emails,
      ));
    }

    // Si ademas tenemos que mandar algun email de oferta / descuento
    $email_post_compra = $web_model->get_email_post_compra();
    if ($email_post_compra != FALSE) {

      $cantidad_pedida = 0;
      foreach($pedido->items as $item) $cantidad_pedida += $item->cantidad;

      if (($email_post_compra->email_post_compra_condicion == 0) ||
        ($email_post_compra->email_post_compra_condicion == 1 && $pedido->total >= $email_post_compra->email_post_compra_condicion_valor) ||
        ($email_post_compra->email_post_compra_condicion == 2 && $cantidad_pedida >= $email_post_compra->email_post_compra_condicion_valor)
      ) {

        $email_post_compra = $carrito_model->replace_placeholder($email_post_compra,$config_placeholders);
        $body = $email_post_compra->texto;
        mandrill_send(array(
          "from_name"=>$empresa->nombre,
          "to"=>$pedido->cliente_email,
          "to_name"=>$pedido->cliente,
          "reply_to"=>$empresa->email, // TODO: Podria ser al email del vendedor
          "subject"=>$email_post_compra->nombre,
          "body"=>$body,
          "bcc"=>$bcc_emails,
        ));
      }
    }

    // Guardamos el evento
    $id_origen = 18; // COMPRA WEB
    // En id_relacion guardamos el pedido realizado
    $texto_consulta = "Total: $ ".number_format($pedido->total,2);
    $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_relacion,id_contacto,texto) VALUES(";
    $sql.= "'$empresa->id',NOW(),'Venta','$id_origen','$pedido->id','$pedido->id_cliente','$texto_consulta')";
    mysqli_query($conx,$sql);

    // Actualizamos el contacto con la ultima fecha de operacion
    // Tipo = 0, indica que es un cliente
    $sql = "UPDATE clientes SET fecha_ult_operacion = NOW(), no_leido = 1, tipo = 0 ";
    $sql.= "WHERE id = $pedido->id_cliente AND id_empresa = $empresa->id ";
    mysqli_query($conx,$sql);

	} else {
		print_r("Not paid yet. Do not release your item.");
	}	
}
?>