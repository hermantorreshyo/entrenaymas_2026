<?php
set_time_limit(0);
// Este script se ejecuta cada 1 MINUTO, y va analizando si hay nuevas actualizaciones de MercadoLibre

// A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
$filename = "notification_meli_lock.txt";
if (file_exists($filename) === FALSE) file_put_contents($filename, "");
$file = fopen($filename, "r+");
if (flock($file, LOCK_EX | LOCK_NB) === FALSE) {  // Intenta adquirir un bloqueo exclusivo
  // Si falla es porque el proceso sigue activo
  exit();
}

function cerrar_archivo($archivo,$con_error = 0) {
  // Para no volver a procesar el mismo archivo, renombramos
  $nuevo_nombre = str_replace("NEW_", (($con_error == 1)?"FAIL_":"OK_"), $archivo);
  rename($archivo,$nuevo_nombre);
}

function logfile($archivo,$str){
  file_put_contents($archivo, date("Y-m-d H:i:s")." ".$str."\n", FILE_APPEND);
}

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

// Buscamos los archivos nuevos de MercadoLibre
$archivos = glob("ipn/NEW_*.txt");
if ($archivos === FALSE || sizeof($archivos) == 0) exit();

require_once 'models/meli.php';
require_once 'sistema/params.php';

foreach($archivos as $archivo) {

  $input = json_decode(file_get_contents($archivo));
  $topic = $input->topic;
  $user_id = $input->user_id;
  $resource = $input->resource;
  $ahora = date("Y-m-d H:i:s");
  logfile($archivo,"TOPIC: $topic RESOURCE: $resource");

  // El user_id identifica al usuario que subio la publicacion a ML
  // Lo tenemos guardado en web_configuracion
  $sql = "SELECT WC.*, E.nombre FROM web_configuracion WC INNER JOIN empresas E ON (WC.id_empresa = E.id) WHERE WC.ml_user_id = '$user_id' ";
  $q = mysqli_query($conx,$sql);
  if (mysqli_num_rows($q)==0) {
    logfile($archivo,"[$user_id] no valido");
    cerrar_archivo($archivo,1);
    continue;
  }
  $empresa = mysqli_fetch_object($q);
  if (!isset($empresa->ml_access_token) || !isset($empresa->expires_in) || empty($empresa->ml_access_token) || empty($empresa->ml_expires_in)) {
    logfile($archivo,"$empresa->id no tiene token.");
    cerrar_archivo($archivo,1);
    continue;
  }
  $id_empresa = $empresa->id_empresa;
  $nombre_empresa = $empresa->nombre;
  $tabla = ($empresa->id_proyecto == 3) ? "inm_propiedades_meli" : "articulos_meli";

  // Creamos el objeto MELI
  $meli = new Meli(ML_APP_ID, ML_APP_SECRET, $empresa->ml_access_token, $empresa->ml_refresh_token);

  // Debemos controlar si el access token sigue siendo valido
  if($empresa->ml_expires_in < time()) {
    try {
      // Refrescamos el access token
      $refresh = $meli->refreshAccessToken();
      $empresa->ml_access_token = $refresh['body']->access_token;
      $empresa->ml_expires_in = time() + $refresh['body']->expires_in;
      $empresa->refresh_token = $refresh['body']->refresh_token;
      guardar_tokens(array(
        "access_token"=>$empresa->ml_access_token,
        "expires_in"=>$empresa->ml_expires_in,
        "refresh_token"=>$empresa->refresh_token,
        "id_empresa"=>$id_empresa,
      ));
    } catch (Exception $e) {
      logfile($e->getMessage());
      cerrar_archivo($archivo,1);
      continue;
    }
  }

  if ($topic == "questions") {

    /*

    logfile($archivo,"Topic es questions");

    // Obtenemos el question en particular (lo manda ML en el parametro resource)
    $params = array('access_token'=>$empresa->ml_access_token);
    $response = $meli->get($resource,$params);
    if ($response["httpCode"] == 200) {

      $consulta = $response["body"];
      logfile($archivo,"CONSULTA: ".print_r($consulta,true));

      $id_usuario = $consulta->from->id;

      // Buscamos el usuario que consulto
      $res_user = $meli->get("/users/$id_usuario",$params);
      if ($res_user["httpCode"] == 200) {
        $usuario = $res_user["body"];

        logfile($archivo,"USUARIO: ".print_r($usuario,true));

        // El ID del usuario de ML lo usamos como codigo interno del cliente
        $sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa AND codigo = '$id_usuario' ";
        $q = mysqli_query($conx,$sql);
        if (mysqli_num_rows($q)>0) {
          $cliente = mysqli_fetch_object($q);
          $id_cliente = $cliente->id;
        } else {
          // Es un cliente nuevo, lo insertamos
          $sql = "INSERT INTO clientes (";
          $sql.= " id_empresa, tipo, nombre, codigo, activo, fecha_inicial, fecha_ult_operacion, no_leido ";
          $sql.= ") VALUES (";
          $sql.= " '$id_empresa', 1, '$usuario->nickname', '$id_usuario', 1, '$ahora','$ahora', 1 ";
          $sql.= ")";
          logfile($archivo,"CREAMOS NUEVO USUARIO: ".$sql);
          mysqli_query($conx,$sql);
          $id_cliente = mysqli_insert_id($conx);

          $id_origen = 20; // CREACION DE USUARIO
          $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_referencia,id_contacto) VALUES(";
          $sql.= "'$id_empresa','$ahora','Nuevo usuario','$id_origen','0','$id_cliente')";
          logfile($archivo,"CREAMOS NUEVO USUARIO: ".$sql);
          mysqli_query($conx,$sql);
        }

        logfile($archivo,"ID_CLIENTE: ".$id_cliente);

        // Buscamos el la base de datos el articulo por el cual esta consultando
        $sql = "SELECT * FROM articulos_meli WHERE id_empresa = $id_empresa AND id_meli = '$consulta->item_id' ";
        $qq = mysqli_query($conx,$sql);
        if (mysqli_num_rows($qq)>0) {
          $articulo = mysqli_fetch_object($qq);

          // Creamos la consulta en el panel
          $id_origen = 25; // CONSULTA DESDE MERCADOLIBRE
          $sql = "INSERT INTO crm_consultas (";
          $sql.= " id_empresa, id_contacto, fecha, asunto, texto, id_origen, tipo, id_referencia ";
          $sql.= ") VALUES (";
          $sql.= " '$id_empresa','$id_cliente','$ahora','Consulta desde Mercadolibre','$consulta->text',$id_origen,0,$articulo->id_articulo ";
          $sql.= ")";
          logfile($archivo,"GUARDAMOS CONSULTA: ".$sql);
          mysqli_query($conx,$sql);        
        }
      }

    } else {
      logfile($archivo,"No se pudo consultar usuario: $id_usuario");
      cerrar_archivo($archivo,1);
    }

    */

  } // Fin questions

  else if ($topic == "items") {

    logfile($archivo,"Topic es items");

    $params = array('access_token'=>$empresa->ml_access_token);
    $response = $meli->get($resource,$params);

    if ($response["httpCode"] != 200) {
      logfile($archivo,"ERROR: httpCode != 200");
      cerrar_archivo($archivo,1);
      continue;
    }

    $item = $response["body"];
    $id_meli = $item->id;
    logfile($archivo,"ITEM: ".print_r($item,true));

    if (isset($item->sub_status) && isset($item->status) && $item->status == "closed" && $item->sub_status == "deleted") {
      // Eliminamos la publicacion de MercadoLibre, entonces eliminamos el registro
      $sql = "DELETE FROM $tabla WHERE id_empresa = $id_empresa AND id_meli = '$id_meli' ";
    } else {
      // Actualizamos los campos
      $sql = "UPDATE $tabla SET ";
      if ($item->status == "active") $sql.= " activo_meli = 1, ";
      else $sql.= " activo_meli = 0, ";
      $sql.= " status = '$item->status', ";
      $sql.= " titulo_meli = '$item->title', ";
      $sql.= " precio_meli = '$item->price' ";
      $sql.= "WHERE id_meli = '$id_meli' ";
      $sql.= "AND id_empresa = $id_empresa ";
    }
    logfile($archivo,"ACTUALIZAR: ".$sql);
    mysqli_query($conx,$sql);
    cerrar_archivo($archivo,0);
    continue;

  } // Fin Items

  else if ($topic == "orders") {

    logfile($archivo,"Topic es orders");

    $params = array('access_token'=>$empresa->ml_access_token);
    $response = $meli->get($resource,$params);

    if ($response["httpCode"] != 200) {
      logfile($archivo,"ERROR: httpCode != 200");
      cerrar_archivo($archivo,1);
      continue;
    }

    $orden = $response["body"];
    logfile($archivo,"ORDEN: ".print_r($orden,true));

    // Primero buscamos si ya existe la orden, para no duplicarla
    $sql = "SELECT 1 FROM facturas WHERE id_empresa = $id_empresa AND numero_referencia = '$orden->id' ";
    $q_fact = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q_fact)>0) {
      logfile($archivo,"LA ORDEN YA EXISTE, NO SE VUELVE A PROCESAR");
      cerrar_archivo($archivo,1);
      continue;
    }

    if ($orden->status != "paid") {
      logfile($archivo,"LA ORDEN TODAVIA NO ESTA PAGADA.");
      cerrar_archivo($archivo,1);
      continue;
    }

    if (!isset($orden->buyer->id)) {
      logfile($archivo,"No existe el ID del comprador. $resource");
      cerrar_archivo($archivo,1);
      continue;
    }
    $id_usuario = $orden->buyer->id;

    // El ID del usuario de ML lo usamos como codigo interno del cliente
    $sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa AND codigo = '$id_usuario' ";
    logfile($archivo,"BUSCAR CLIENTE: ".$sql);

    $q = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $cliente = mysqli_fetch_object($q);
      $nombre_cliente = $cliente->nombre;
      $id_cliente = $cliente->id;
    } else {
      // Es un cliente nuevo, lo insertamos
      $nickname = isset($orden->buyer->nickname) ? $orden->buyer->nickname : "Cliente nuevo";
      $nombre_cliente = $nickname;
      $sql = "INSERT INTO clientes (";
      $sql.= " id_empresa, tipo, nombre, codigo, activo, fecha_inicial, fecha_ult_operacion, no_leido ";
      $sql.= ") VALUES (";
      $sql.= " '$id_empresa', 0, '$nickname', '$id_usuario', 1, '$ahora','$ahora', 1 ";
      $sql.= ")";
      logfile($archivo,"GUARDAMOS CLIENTE: ".$sql);
      mysqli_query($conx,$sql);
      $id_cliente = mysqli_insert_id($conx);

      $id_origen = 20; // CREACION DE USUARIO
      $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_referencia,id_contacto) VALUES(";
      $sql.= "'$id_empresa','$ahora','Nuevo usuario','$id_origen','0','$id_cliente')";
      logfile($archivo,"CREAMOS USUARIO: ".$sql);
      mysqli_query($conx,$sql);
    }

    logfile($archivo,"ID_CLIENTE: ".$id_cliente);

    if (!isset($orden->order_items) || sizeof($orden->order_items) == 0) {
      lofgile($archivo,"LA ORDEN NO TIENE ITEMS");
      continue;
    }

    $item_0 = $orden->order_items[0];
    $id_articulo_meli = $item_0->item->id;
    $sql = "SELECT * FROM articulos_meli WHERE id_empresa = $id_empresa AND id_meli = '$id_articulo_meli' ";
    logfile($archivo,"BUSCAMOS ARTICULO_MELI: ".$sql);
    $qq = mysqli_query($conx,$sql);

    if (mysqli_num_rows($qq)==0) {
      logfile($archivo,"NO SE ENCUENTRA EL ARTICULO CON ID_MELI = '$id_articulo_meli' ");
      continue;
    }
    
    $articulo_meli = mysqli_fetch_object($qq);
    $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa AND id = $articulo_meli->id_articulo ";
    logfile($archivo,"BUSCAMOS ARTICULO: ".$sql);
    $qqq = mysqli_query($conx,$sql);
    $articulo = mysqli_fetch_object($qqq);

    $hoy = date("Y-m-d");
    $ahora = date("H:i:s");
    $observaciones = "Compra desde Mercadolibre";
    $precio_unitario = (float) $item_0->unit_price;
    $cantidad = (float) $item_0->quantity;
    $total = $precio_unitario * $cantidad;
    $id_tipo_estado = 6;

    // TODO: Por el momento, se hace REMITO
    $id_tipo_comprobante = (isset($id_tipo_comprobante)) ? $id_tipo_comprobante : 999;

    // Buscamos el punto de venta asociado con la web
    $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
    $sql.= "FROM puntos_venta PV INNER JOIN web_configuracion CONF ON (PV.id = CONF.id_punto_venta AND PV.id_empresa = CONF.id_empresa) ";
    $sql.= "LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
    $sql.= "WHERE PV.id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $pv = mysqli_fetch_object($q);
      $id_punto_venta = $pv->id;
      $punto_venta = $pv->numero;          
      $tipo_punto_venta = $pv->tipo_impresion;
      $id_sucursal = $pv->id_sucursal;
      $sucursal = $pv->sucursal;
    } else {
      $id_punto_venta = 0;
      $punto_venta = 0;
      $tipo_punto_venta = "";
      $id_sucursal = 0;
      $sucursal = "";
    }

    // Buscamos si ya fue cargada la venta, para no duplicarla
    $sql = "SELECT 1 FROM facturas WHERE id_punto_venta = '$id_punto_venta' AND numero_referencia = '$orden->id' AND id_empresa = '$id_empresa' ";
    $q = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q)>0) {
      logfile($archivo,"YA EXISTE LA VENTA: $sql");
      continue;
    }

    // Buscamos el proximo numero de comprobante
    $sql = "SELECT * FROM numeros_comprobantes ";
    $sql.= "WHERE id_punto_venta = $pv->id AND id_empresa = $id_empresa AND id_tipo_comprobante = $id_tipo_comprobante ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $nro_comp = mysqli_fetch_object($q);  
      $nro_comprobante = ((float)$nro_comp->ultimo) + 1;
    } else {
      $nro_comprobante = 0;
    }

    // Ponemos el texto del comprobante
    if ($id_punto_venta > 0) {
      $sql = "SELECT * FROM tipos_comprobante WHERE id = $id_tipo_comprobante";
      $q = mysqli_query($conx,$sql);
      $tipo_comp = mysqli_fetch_object($q);  
      $tipo_comprobante = $tipo_comp->nombre;
      $comprobante = $tipo_comp->letra." ".str_pad($punto_venta, 4, "0", STR_PAD_LEFT)."-".str_pad($nro_comprobante, 8, "0", STR_PAD_LEFT);          
    } else {
      $comprobante = "";
      $tipo_comprobante = "";
    }

    // Creamos la nueva venta
    $sql = "INSERT INTO facturas (";
    $sql.= " id_empresa, fecha, hora, id_cliente, id_tipo_comprobante, estado, observaciones, ";
    $sql.= " id_punto_venta, punto_venta, numero, comprobante, ";
    $sql.= " total, subtotal, neto, iva, porc_descuento, descuento, ";
    $sql.= " direccion, localidad, id_localidad, id_tipo_estado, costo_envio, ";
    $sql.= " retirar_envio, codigo_postal, id_usuario, numero_referencia, ";
    $sql.= " cliente, tipo_comprobante, tipo_punto_venta, id_origen, nueva, empresa, sucursal, id_sucursal, costo_final ";
    $sql.= ") VALUES (";
    $sql.= " '$id_empresa', '$hoy', '$ahora', '$id_cliente', '$id_tipo_comprobante', 1, '$observaciones', ";
    $sql.= " '$id_punto_venta', '$punto_venta', '$nro_comprobante', '$comprobante', ";
    $sql.= " '$total', '$total', '$total', '0', '0', '0',  ";
    $sql.= " '', '', '', '$id_tipo_estado', '0', ";
    $sql.= " '0', '', '$articulo->id_usuario', '$orden->id', ";
    $sql.= " '$nombre_cliente', '$tipo_comprobante', '$tipo_punto_venta', 2, 1, '$nombre_empresa', '$sucursal', '$id_sucursal', '$articulo->costo_final' ";
    $sql.= ")";
    logfile($archivo,"GUARDAMOS FACTURA: ".$sql);
    mysqli_query($conx,$sql);
    $id_pedido = mysqli_insert_id($conx);

    // TODO: Que se pueda elegir si se quiere reservar o descontar stock directamente
    $reservar_stock = ($id_empresa == 342) ? 1 : "";
    
    // Creamos el item de la nueva venta
    $sql = "INSERT INTO facturas_items (";
    $sql.= " id_empresa, id_factura, id_articulo, cantidad, nombre, ";
    $sql.= " id_punto_venta, id_tipo_comprobante, ";
    $sql.= " precio, total_con_iva, orden, custom_3, costo_final ";
    $sql.= ") VALUES (";
    $sql.= " '$id_empresa', '$id_pedido', '$articulo->id', '$cantidad','$articulo->nombre',";
    $sql.= " '$id_punto_venta', '$id_tipo_comprobante', ";
    $sql.= " '$precio_unitario','$total', '0', '$reservar_stock', '$articulo->costo_final' ";
    $sql.= ")";
    logfile($archivo,"GUARDAMOS FACTURA_ITEM: ".$sql);
    mysqli_query($conx,$sql);

    // Antes de descontar del stock, controlamos que tipo de stock se esta llevando
    $sql = "SELECT * FROM stock ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_articulo = $articulo->id ";
    $sql.= "LIMIT 0,1 ";
    $q_stock = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q_stock)>0) {

      $r_stock = mysqli_fetch_object($q_stock);
      // EL STOCK SE LLEVA POR ALMACENES (PYMVAR)
      // TODO: Podriamos configurar un solo deposito al cual sacarle el stock

      // Se debe comprometer el stock en vez de descontarlo
      if ($reservar_stock == 1) {

        $sql = "UPDATE stock SET reservado = reservado + $cantidad ";
        $sql.= "WHERE id_sucursal = $r_stock->id_sucursal ";
        $sql.= "AND id_articulo = $articulo->id ";
        $sql.= "AND id_empresa = $id_empresa ";
        logfile($archivo,"RESERVAMOS EL STOCK: ".$sql);
        mysqli_query($conx,$sql);

      } else {

        // Descontamos el stock
        $sql = "UPDATE stock SET stock_actual = stock_actual - $cantidad ";   
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_articulo = $articulo->id ";
        $sql.= "AND A.id_sucursal = $r_stock->id_sucursal ";
        logfile($archivo,"ACTUALIZAMOS STOCK ALMACEN: ".$sql);
        mysqli_query($conx,$sql);

        // Agregamos el movimiento
        $saldo = (float)$r_stock->stock_actual - (float)$cantidad;
        $sql = "INSERT INTO stock_movimientos (id_sucursal,id_articulo,movimiento,fecha,cantidad,saldo,detalle,id_empresa) VALUES (";
        $sql.= " '$r_stock->id_sucursal','$articulo->id','B',NOW(),'$cantidad','$saldo','Venta por MercadoLibre','$id_empresa')";
        logfile($archivo,"ACTUALIZAMOS STOCK_MOVIMIENTOS ALMACEN: ".$sql);
        mysqli_query($conx,$sql);
      }      

    } else {

      // El stock se lleva en el mismo producto

      // Descontamos el stock del producto   
      $sql = "UPDATE articulos SET stock = stock - $cantidad ";   
      $sql.= "WHERE id_empresa = $id_empresa AND id = $articulo->id ";
      logfile($archivo,"ACTUALIZAMOS STOCK SIMPLE: ".$sql);
      mysqli_query($conx,$sql);
      
      /*
      // Descontamos el stock de la variante en caso de tenerla
      if ($a->id_opcion_1 != 0 || $a->id_opcion_2 != 0 || $a->id_opcion_3 != 0) {
        $sql = "UPDATE articulos_variantes ";
        $sql.= "SET stock = stock - $a->cantidad ";
        $sql.= "WHERE id_empresa = $id_empresa AND id_articulo = $a->id ";
        $sql.= "AND id_opcion_1 = $a->id_opcion_1 ";
        $sql.= "AND id_opcion_2 = $a->id_opcion_2 ";
        $sql.= "AND id_opcion_3 = $a->id_opcion_3 ";
        mysqli_query($conx,$sql);
      }
      */
    }      

    // Guardamos el evento
    $id_origen = 19; // COMPRA DESDE MERCADOLIBRE
    // En id_relacion guardamos el pedido realizado
    $texto_consulta = "Total: $ ".number_format($total,2);
    $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_relacion,id_contacto,texto) VALUES(";
    $sql.= "'$id_empresa','$hoy $ahora','Venta','$id_origen','$id_pedido','$id_cliente','$texto_consulta')";
    logfile($sql);
    mysqli_query($conx,$sql);

    // Actualizamos el contacto con la ultima fecha de operacion
    // Tipo = 0, indica que es un cliente
    $sql = "UPDATE clientes SET fecha_ult_operacion = NOW(), no_leido = 1, tipo = 0 ";
    if (isset($orden->buyer->first_name) && isset($orden->buyer->last_name)) {
      $sql.= ", nombre = '$orden->buyer->first_name $orden->buyer->last_name' ";
    }
    $sql.= "WHERE id = $id_cliente AND id_empresa = $id_empresa ";
    logfile($sql);
    mysqli_query($conx,$sql);

    cerrar_archivo($archivo,0);

  } // Fin orders

}
?>