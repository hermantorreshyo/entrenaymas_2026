<?php
class Viaje_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $total = 0;
  private $sql = "";

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : mb_convert_encoding($r, 'UTF-8', 'ISO-8859-1'));
  }

  function nueva_reserva($config = array()) {

    $id_viaje = isset($config["id_viaje"]) ? $config["id_viaje"] : 0;
    $fecha_reserva = isset($config["fecha_reserva"]) ? $config["fecha_reserva"] : "";
    $lang = isset($config["lang"]) ? $config["lang"] : "";
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : "";

    // Creamos un adulto en blanco (porque al menos debe haber un mayor)
    $adulto1 = new stdClass();
    $adulto1->nombre = "";
    $adulto1->dni = "";
    $adulto1->fecha_nac = "";
    $adulto1->nacionalidad = "";
    $adulto1->email = "";
    $adulto1->email_2 = "";
    $adulto1->telefono = "";
    $adulto1->celular = "";
    $adulto1->precio = 0;
    $adulto1->moneda = "ARS";

    $pedido = new stdClass();
    $pedido->id = 0;
    $pedido->id_viaje = $id_viaje;
    $pedido->fecha_reserva = $fecha_reserva;
    $pedido->observaciones = $observaciones;
    $pedido->hotel = "";
    $pedido->fecha_llegada_hotel = "";
    $pedido->hotel_observaciones = "";
    $pedido->adultos = [$adulto1];
    $pedido->menores = [];
    $pedido->idioma = $lang;
    $pedido->prestador_servicio = "";
    $pedido->total_general = 0;
    $pedido->subtotales = [];
    $pedido->opcionales = [];
    $pedido->id_cliente = 0;
    return $pedido;
  }

  function guardar_reserva($pedido) {

    // Buscamos o insertamos el nuevo cliente
    $id_cliente = 0;
    if (sizeof($pedido->adultos)==0) return;
    $cliente = $pedido->adultos[0];
    if (!isset($cliente->email) || empty($cliente->email)) return;

    if (!isset($cliente->dni)) $cliente->dni = "";
    if (!isset($cliente->celular)) $cliente->celular = "";

    // A la fecha de nac del cliente la ponemos en formato MySQL
    if (isset($cliente->fecha_nac)) { 
      $dia = substr($cliente->fecha_nac,0,2);
      $mes = substr($cliente->fecha_nac,3,2);
      $anio = substr($cliente->fecha_nac,6,4);
      $cliente_fecha_nac = $anio.'-'.$mes.'-'.$dia;
    } else {
      $cliente_fecha_nac = "0000-00-00";
    }

    // A la fecha de llegada del hotel la ponemos en formato MySQL
    if (isset($pedido->fecha_llegada_hotel) && !empty($pedido->fecha_llegada_hotel)) {
      $dia = substr($pedido->fecha_llegada_hotel,0,2);
      $mes = substr($pedido->fecha_llegada_hotel,3,2);
      $anio = substr($pedido->fecha_llegada_hotel,6,4);
      $pedido->fecha_llegada_hotel = $anio.'-'.$mes.'-'.$dia;
    } else {
      $pedido->fecha_llegada_hotel = "";
    }
    $fecha_inic = date("Y-m-d H:i:s");

    $sql = "SELECT * FROM clientes WHERE id_empresa = $this->id_empresa AND email = '$cliente->email' ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $cli = mysqli_fetch_object($q);
      $id_cliente = $cli->id;
      // Actualizamos los valores
      $sql = "UPDATE clientes SET ";
      $sql.= " nombre = '$cliente->nombre', ";
      $sql.= " fecha_nac = '$cliente_fecha_nac', ";
      $sql.= " cuit = '$cliente->dni', ";
      $sql.= " telefono = '$cliente->telefono', ";
      $sql.= " fecha_ult_operacion = '$fecha_inic', ";
      $sql.= " no_leido = 1, ";
      $sql.= " tipo = '0', "; // 0 = CLIENTE
      $sql.= " celular = '$cliente->celular' ";
      $sql.= "WHERE id = $id_cliente AND id_empresa = $this->id_empresa ";
      mysqli_query($this->conx,$sql);
    } else {
      // Insertamos un nuevo cliente
      $sql = "INSERT INTO clientes (id_empresa,nombre,email,id_tipo_documento,";
      $sql.= " cuit,id_tipo_iva,activo,lista,telefono,celular,forma_pago,";
      $sql.= " fecha_inicial,fecha_nac,tipo,fecha_ult_operacion,no_leido ";
      $sql.= ") VALUES (";
      $sql.= " $this->id_empresa, '$cliente->nombre', '$cliente->email', 99, ";
      $sql.= " '$cliente->dni', 4, 1, 0, '$cliente->telefono', '$cliente->celular', 'E', ";
      $sql.= " '$fecha_inic', '$cliente_fecha_nac',0,'$fecha_inic',1 ";
      $sql.= ")";
      $q = mysqli_query($this->conx,$sql);
      $id_cliente = mysqli_insert_id($this->conx);

      $id_origen = 20; // CREACION DE USUARIO
      $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_referencia,id_contacto) VALUES(";
      $sql.= "'$this->id_empresa','$fecha_inic','Nuevo usuario','$id_origen','0','$id_cliente')";
      mysqli_query($this->conx,$sql);
    }

    $pedido->id_cliente = $id_cliente;
    $pedido->horario = isset($pedido->horario) ? $pedido->horario : "";
    $pedido->idioma = isset($pedido->idioma) ? $pedido->idioma : "";
    $ahora = date("Y-m-d H:i:s");

    if ($pedido->id == 0) {
      // Insertamos la reserva
      $sql = "INSERT INTO via_reservas (id_empresa,id_cliente,id_viaje,fecha_reserva,total,observaciones,hotel,hotel_observaciones,fecha_llegada_hotel,prestador_servicio,salida_desde,idioma,fecha_realizacion) VALUES(";
      $sql.= "$this->id_empresa,$id_cliente,$pedido->id_viaje,'$pedido->fecha_reserva',$pedido->total_general,'$pedido->observaciones','$pedido->hotel','$pedido->hotel_observaciones','$pedido->fecha_llegada_hotel','$pedido->prestador_servicio','$pedido->horario','$pedido->idioma','$ahora')";
      $q = mysqli_query($this->conx,$sql);
      $id_reserva = mysqli_insert_id($this->conx);
    } else {
      // Actualizamos la reserva
      $id_reserva = $pedido->id;
      $sql = "UPDATE via_reservas SET ";
      $sql.= " id_cliente = $id_cliente, ";
      $sql.= " id_viaje = $pedido->id_viaje, ";
      $sql.= " fecha_reserva = '$pedido->fecha_reserva', ";
      if (!empty($pedido->fecha_llegada_hotel)) $sql.= " fecha_llegada_hotel = '$pedido->fecha_llegada_hotel', ";
      $sql.= " hotel = '$pedido->hotel', ";
      $sql.= " hotel_observaciones = '$pedido->hotel_observaciones', ";
      $sql.= " observaciones = '$pedido->observaciones', ";
      if (isset($pedido->prestador_servicio) && !empty($pedido->prestador_servicio)) $sql.= " prestador_servicio = '$pedido->prestador_servicio', ";
      if (isset($pedido->horario) && !empty($pedido->horario)) $sql.= " salida_desde = '$pedido->horario', ";
      $sql.= " idioma = '$pedido->idioma', ";
      $sql.= " fecha_realizacion = '$ahora', ";
      $sql.= " total = $pedido->total_general ";
      $sql.= "WHERE id_empresa = $this->id_empresa ";
      $sql.= "AND id = $id_reserva ";
      mysqli_query($this->conx,$sql);
    }

    $sql = "DELETE FROM via_reservas_asientos WHERE id_empresa = $this->id_empresa AND id_reserva = $id_reserva ";
    mysqli_query($this->conx,$sql);
    $pasajeros = array_merge($pedido->adultos,$pedido->menores);
    foreach($pasajeros as $pax) {
      if (!isset($pax->nacionalidad)) $pax->nacionalidad = "";
      if (!isset($pax->nombre)) $pax->nombre = "";
      if (!isset($pax->dni)) $pax->dni = "";
      if (!isset($pax->precio)) $pax->precio = "0";
      if (!isset($pax->moneda)) $pax->moneda = "ARS";
      if (!isset($pax->fecha_nac)) $pax->fecha_nac = "0000-00-00";
      // Insertamos los pasajeros
      $sql = "INSERT INTO via_reservas_asientos (";  
      $sql.= " id_empresa, id_reserva, id_asiento, id_vehiculo, nombre, nacionalidad, dni, fecha_nac, precio, moneda ";
      $sql.= ") VALUES (";
      $sql.= " $this->id_empresa, $id_reserva, 0, 0, '$pax->nombre', '$pax->nacionalidad', '$pax->dni', '$pax->fecha_nac', '$pax->precio', '$pax->moneda' ";
      $sql.= ")";
      mysqli_query($this->conx,$sql);
    }

    $sql = "DELETE FROM via_reservas_opcionales WHERE id_empresa = $this->id_empresa AND id_reserva = $id_reserva ";
    mysqli_query($this->conx,$sql);
    foreach($pedido->opcionales as $opc) {
      foreach($opc as $id_tipo_tarifa => $value) {
        // Insertamos los opcionales del viaje  
        $sql = "INSERT INTO via_reservas_opcionales (";
        $sql.= " id_empresa, id_reserva, id_opcional, total, moneda ";
        $sql.= ") VALUES (";
        $sql.= " $this->id_empresa, $id_reserva, '$value->id_opcional', '$value->precio', '$value->moneda' ";
        $sql.= ")";
        mysqli_query($this->conx,$sql);
      }
    }

    return $id_reserva;
  }

  function get_mercadopago($numero_carrito = 0) {

    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_mp == 0) return FALSE;
    // La configuracion de las dos cuentas esta separada por ;;;
    // Dependiendo del carrito, tomamos una u otra configuracion
    $clients_id = explode(";;;", $medio->mp_client_id);
    $clients_secret = explode(";;;", $medio->mp_client_secret);

    // Dependiendo de cual carrito estamos haciendo el checkout
    $mp_client_id = trim($clients_id[$numero_carrito]);
    $mp_client_secret = trim($clients_secret[$numero_carrito]);
    if (empty($mp_client_id) || empty($mp_client_secret)) return FALSE; // No fue configurado aun
    return new MP($mp_client_id, $mp_client_secret);
  }

  function get_paypal_email($numero_carrito = 0) {
    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_paypal == 0) return FALSE;
    $clients_id = explode(";;;", $medio->paypal_email);
    $paypal_email = trim($clients_id[$numero_carrito]);
    if (empty($paypal_email)) return FALSE; // No fue configurado aun
    return $paypal_email;
  }

  function get_transferencia_bancaria() {
    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_banco == 0) return FALSE;
    return TRUE;
  }

  function get_categorias($id_categoria,$config = array()) {
    $link = isset($config["link"]) ? $config["link"] : "";
    $separador_nombre = isset($config["separador_nombre"]) ? $config["separador_nombre"] : " | ";
    $categorias = array();
    while(TRUE) {
      $sql = "SELECT * FROM via_viajes_categorias WHERE id = $id_categoria AND id_empresa = $this->id_empresa AND activo = 1 ";
      $q = mysqli_query($this->conx,$sql);
      $cat = mysqli_fetch_object($q);
      if ($cat == NULL) break;
      $categorias[] = $cat;
      if ($cat->id_padre == 0) break; // Llegamos al final
      $id_categoria = $cat->id_padre;
    }
    $categorias = array_reverse($categorias);
    $link_1 = "";
    $nombre = "";
    foreach($categorias as $cat) {
      $link_1 .= $cat->link."/";
      $cat->link = $link.$link_1;
      $cat->full_name = $nombre.(!empty($nombre) ? $separador_nombre : "").$cat->nombre;
      $nombre = $cat->full_name;
    }
    return $categorias;
  }

  function get_subcategorias($id_categoria_padre,$config=array()) {
    $activo = isset($config["activo"]) ? $config["activo"] : -1;
    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";
    $sql = "SELECT * ";
    if (!empty($lang)) $sql.= ", nombre_$lang AS nombre ";
    $sql.= "FROM via_viajes_categorias A ";
    $sql.= "WHERE A.id_empresa = $this->id_empresa ";
    $sql.= "AND A.activo = 1 ";
    $sql.= "AND A.id_padre = $id_categoria_padre ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    $sql.= "ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    if (mysqli_num_rows($q)>0) {
      while(($r=mysqli_fetch_object($q))!==NULL) {
        $r->children = $this->get_subcategorias($r->id,$config);
        $salida[] = $r;
      }
    }
    return $salida;
  }

  function get_cotizacion_dolar() {
    // Buscamos la cotizacion actual del dolar
    $cotizacion_dolar = 1;
    // Si hay una cotizacion especifica para la empresa
    $sql = "SELECT * FROM cotizaciones WHERE moneda = '".'U$D'."' ";
    $q = mysqli_query($this->conx,$sql." AND id_empresa = ".$this->id_empresa);
    if (mysqli_num_rows($q)>0) {
      $r = mysqli_fetch_object($q);
      $cotizacion_dolar = $r->valor;
    } else {
      // Tomamos la configuracion automatica del dolar
      $q = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q)>0) {
        $r = mysqli_fetch_object($q);
        $cotizacion_dolar = $r->valor;
      }
    }
    return $cotizacion_dolar;    
  }


    // Obtenemos los datos del entrada
  function get($id,$config = array()) {

    // Estos parametros se pueden deshabilitar para ganar velocidad, ya que no tiene sentido a veces cargarlos
    $buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;
    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 1;
    $buscar_comentarios = isset($config["buscar_comentarios"]) ? $config["buscar_comentarios"] : 0;

    // FECHA QUE SE TOMA COMO BASE A LOS PRECIOS DE TEMPORADA
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";

    // Indica cual precio debemos tomar de la LISTA DE PRECIOS
    // 1 = Mas bajo
    // 2 = Primer cargado
    $orden_precios = isset($config["orden_precios"]) ? $config["orden_precios"] : 1;

    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $oferta = isset($config["oferta"]) ? $config["oferta"] : -1;
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $tiene_video = isset($config["tiene_video"]) ? $config["tiene_video"] : 0;
    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";
    $currency = isset($config["currency"]) ? $config["currency"] : "ARS";
    $no_currency = isset($config["no_currency"]) ? $config["no_currency"] : 0;
    $mostrar_en_moneda = isset($config["mostrar_en_moneda"]) ? $config["mostrar_en_moneda"] : "ARS";

    $cotizacion_dolar = $this->get_cotizacion_dolar();

    $id = (int)$id;
    $sql = "SELECT A.*, DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, ";
    if (!empty($lang)) $sql.= " A.nombre_$lang AS nombre, ";
    if (!empty($lang)) $sql.= " A.observaciones_$lang AS observaciones, ";
    if (!empty($lang)) $sql.= " A.texto_$lang AS texto, ";
    if (!empty($lang)) $sql.= " A.custom_1_$lang AS custom_1, ";
    if (!empty($lang)) $sql.= " A.custom_2_$lang AS custom_2, ";
    if (!empty($lang)) $sql.= " A.custom_3_$lang AS custom_3, ";
    if (!empty($lang)) $sql.= " A.custom_4_$lang AS custom_4, ";
    $sql.= " DATE_FORMAT(A.fecha_llegada,'%d/%m/%Y') AS fecha_llegada, ";
    $sql.= " YEAR(A.fecha) AS anio, MONTH(A.fecha) AS mes, ";
    $sql.= " A.fecha AS fecha_original, ";
    if (!empty($lang)) $sql.= " IF(C.nombre_$lang IS NULL,'',C.nombre_$lang) AS categoria, ";
    else $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria, ";
    $sql.= " IF(C.link IS NULL,'',C.link) AS categoria_link, ";
    $sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path ";
    $sql.= "FROM via_viajes A ";
    $sql.= "LEFT JOIN via_viajes_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
        //$sql.= "AND A.fecha >= NOW() ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($oferta != -1) $sql.= "AND A.id_promocion = $oferta ";
    if ($not_id > 0) $sql.= "AND A.id != $not_id ";
    if ($tiene_video == 1) $sql.= "AND A.video != '' ";
    $sql.= "ORDER BY A.fecha DESC ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q) == 0) {
      error_mail($sql);
      return FALSE;
    }
    $entrada = mysqli_fetch_object($q);
    $entrada = $this->encoding($entrada);

    $entrada->images = array();
    if ($buscar_imagenes == 1) {
      // Obtenemos las imagenes de ese entrada
      $sql = "SELECT AI.* FROM via_viajes_images AI WHERE AI.id_viaje = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        if (!empty($r->path)) {
          $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
        }
        $entrada->images[] = $r->path;
      }
    }

    $entrada->precios = array();
    $entrada->precio = 0;
    $entrada->moneda = "ARS";
    $entrada->tarifa = "";

    // Ponemos el precio del paquete a un precio seleccionado de la lista
    $sql = "SELECT VP.* ";
    if (empty($lang) || $lang == "es") $sql.= ", IF(VT.nombre IS NULL,'',VT.nombre) AS nombre ";
    else if ($lang == "en") $sql.= ", IF(VT.nombre_en IS NULL,'',VT.nombre_en) AS nombre ";
    else if ($lang == "pt") $sql.= ", IF(VT.nombre_pt IS NULL,'',VT.nombre_pt) AS nombre ";    
    $sql.= "FROM via_viajes_vehiculos_precios VP ";
    $sql.= "LEFT JOIN via_tipos_tarifas VT ON (VP.id_tipo_tarifa = VT.id AND VP.id_empresa = VT.id_empresa) ";
    $sql.= "WHERE VP.id_viaje = $entrada->id AND VP.id_empresa = $entrada->id_empresa ";
    //if ($no_currency == 0) $sql.= ($currency == "USD") ? 'AND moneda = "USD" ' : "AND moneda = 'ARS' ";
    if ($orden_precios == 1) $sql.= "ORDER BY VP.precio ASC ";
    else if ($orden_precios == 2) $sql.= "ORDER BY VP.id ASC ";
    $k=0;
    $q_precio = mysqli_query($this->conx,$sql);
    while(($precio=mysqli_fetch_object($q_precio))!==NULL) {
      $precio->nombre = mb_convert_encoding($precio->nombre, 'UTF-8', 'ISO-8859-1');

      // Debemos hacer la conversion de monedas
      if ($mostrar_en_moneda == "ARS" && $precio->moneda == 'USD') {
        $precio->precio = $precio->precio * $cotizacion_dolar;
        $precio->moneda = "ARS";
      }

      $entrada->precios[] = $precio;

      // Estamos seteando una fecha concreta, tenemos que tomar el precio para esa fecha
      // Pero el array de precios tiene que tener todas las fechas, ya que el calendario puede cambiar
      if (!empty($fecha)) {
        if ($precio->fecha_desde <= $fecha && $fecha <= $precio->fecha_hasta) {
          $entrada->precio = $precio->precio;
          $entrada->moneda = $precio->moneda;
          $entrada->tarifa = $precio->nombre;
        }
      } else if ($k==0) {
        // Ponemos el precio por defecto del paquete
        $entrada->precio = $precio->precio;
        $entrada->moneda = $precio->moneda;
        $entrada->tarifa = $precio->nombre;
      }

      $k++;
    }

    // Obtenemos los opcionales
    $entrada->opcionales = $this->get_opcionales(array(
      "id_viaje"=>$entrada->id,
      "orden_precios"=>$orden_precios,
      "fecha"=>$fecha,
      "lang"=>$lang,
      "currency"=>$currency,
    ));

    // Link de la imagen
    if (!empty($entrada->path)) {
      $entrada->path = ((strpos($entrada->path,"http://")===FALSE)) ? "/sistema/".$entrada->path : $entrada->path;
    }

    // Obtenemos los viajes relacionados
    $sql = "SELECT A.*, DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, ";
    if (!empty($lang)) $sql.= " A.nombre_$lang AS nombre, ";
    if (!empty($lang)) $sql.= " A.observaciones_$lang AS observaciones, ";
    if (!empty($lang)) $sql.= " A.texto_$lang AS texto, ";
    if (!empty($lang)) $sql.= " A.custom_1_$lang AS custom_1, ";
    if (!empty($lang)) $sql.= " A.custom_2_$lang AS custom_2, ";
    if (!empty($lang)) $sql.= " A.custom_3_$lang AS custom_3, ";
    if (!empty($lang)) $sql.= " A.custom_4_$lang AS custom_4, ";
    $sql.= " DATE_FORMAT(A.fecha_llegada,'%d/%m/%Y') AS fecha_llegada, ";
    $sql.= " YEAR(A.fecha) AS anio, MONTH(A.fecha) AS mes, ";
    $sql.= " A.fecha AS fecha_original, ";
    if (!empty($lang)) $sql.= " IF(C.nombre_$lang IS NULL,'',C.nombre_$lang) AS categoria, ";
    else $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria, ";
    $sql.= " IF(C.link IS NULL,'',C.link) AS categoria_link, ";
    $sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path ";
    $sql.= "FROM via_viajes A ";
    $sql.= "INNER JOIN via_viajes_relacionados VR ON (A.id = VR.id_relacion AND A.id_empresa = VR.id_empresa) ";
    $sql.= "LEFT JOIN via_viajes_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE VR.id_viaje = $id ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    $sql.= "ORDER BY A.fecha DESC ";
    $q_relacionados = mysqli_query($this->conx,$sql);
    $entrada->relacionados = array();
    while(($rel=mysqli_fetch_object($q_relacionados))!==NULL) {
      if (!empty($rel->path)) {
        $rel->path = ((strpos($rel->path,"http://")===FALSE)) ? "/sistema/".$rel->path : $rel->path;
      }
      $entrada->relacionados[] = $this->encoding($rel);
    }

    if (!empty($entrada->categoria_path)) {
      $entrada->categoria_path = ((strpos($entrada->categoria_path,"http://")===FALSE)) ? "/sistema/".$entrada->categoria_path : $entrada->categoria_path;
    }

    return $entrada;
  }

  function add_view($id) {
    $sql = "UPDATE via_viajes SET vistos = vistos + 1 WHERE id = $id AND id_empresa = $this->id_empresa ";
    mysqli_query($this->conx,$sql);
  }

  function get_list($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 6;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
    $oferta = isset($config["oferta"]) ? $config["oferta"] : -1; // -1 = No se tiene en cuenta el parametro
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";
    $currency = isset($config["currency"]) ? $config["currency"] : "ARS";
    $no_currency = isset($config["no_currency"]) ? $config["no_currency"] : 0;

    $mostrar_en_moneda = isset($config["mostrar_en_moneda"]) ? $config["mostrar_en_moneda"] : "ARS";
    $cotizacion_dolar = $this->get_cotizacion_dolar();

    // FECHA QUE SE TOMA COMO BASE A LOS PRECIOS DE TEMPORADA
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";

    // Indica cual precio debemos tomar de la LISTA DE PRECIOS
    // 1 = Mas bajo
    // 2 = Primer cargado
    $orden_precios = isset($config["orden_precios"]) ? $config["orden_precios"] : 1;
    
    // 0 = NO IMPORTA LA FECHA
    // 1 = FECHA_PUBLICACION < NOW() (Ej: diario) DEFAULT
    // 2 = FECHA_PUBLICACION > NOW()
    $filtro_fecha = isset($config["filtro_fecha"]) ? $config["filtro_fecha"] : 1;
    $filtro_fecha_llegada = isset($config["filtro_fecha_llegada"]) ? $config["filtro_fecha_llegada"] : 1;
    $now = isset($config["now"]) ? "'".$config["now"]."'" : "NOW()";

    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $categoria = isset($config["categoria"]) ? $config["categoria"] : "";
    $not_categoria = isset($config["not_categoria"]) ? $config["not_categoria"] : "";
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
    $fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
    $mes = isset($config["mes"]) ? $config["mes"] : 0;
    $anio = isset($config["anio"]) ? $config["anio"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $not_ids = isset($config["not_ids"]) ? $config["not_ids"] : "";
    $order = isset($config["order"]) ? $config["order"] : "0";
    $tiene_video = isset($config["tiene_video"]) ? $config["tiene_video"] : 0;

    // IDS de las categorias que se quire agregar (o no), separados por comas
    $ids_categorias = isset($config["ids_categorias"]) ? $config["ids_categorias"] : "";
    $not_ids_categorias = isset($config["not_ids_categorias"]) ? $config["not_ids_categorias"] : "";

    $ids_etiquetas = isset($config["ids_etiquetas"]) ? $config["ids_etiquetas"] : "";
    $link_etiqueta = isset($config["link_etiqueta"]) ? $config["link_etiqueta"] : "";

    $buscar_categorias = isset($config["buscar_categorias"]) ? $config["buscar_categorias"] : 0;

    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    if (!empty($lang)) $sql.= " A.nombre_$lang AS nombre, ";
    if (!empty($lang)) $sql.= " A.observaciones_$lang AS observaciones, ";
    if (!empty($lang)) $sql.= " A.texto_$lang AS texto, ";
    if (!empty($lang)) $sql.= " A.custom_1_$lang AS custom_1, ";
    if (!empty($lang)) $sql.= " A.custom_2_$lang AS custom_2, ";
    if (!empty($lang)) $sql.= " A.custom_3_$lang AS custom_3, ";
    if (!empty($lang)) $sql.= " A.custom_4_$lang AS custom_4, ";
    $sql.= " A.fecha AS fecha_original, ";
    $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
    if (!empty($lang)) $sql.= " IF(C.nombre_$lang IS NULL,'',C.nombre_$lang) AS categoria, ";
    else $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria, ";
    $sql.= " IF(C.link IS NULL,'',C.link) AS categoria_link, ";
    $sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path ";
    $sql.= "FROM via_viajes A ";
    $sql.= "LEFT JOIN via_viajes_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    //if ($filtro_fecha == 1) $sql.= "AND A.fecha <= $now ";
    //else if ($filtro_fecha == 2) $sql.= "AND A.fecha >= $now ";
    if ($filtro_fecha_llegada == 2) $sql.= "AND A.fecha_llegada >= $now ";
    if ($not_id > 0) $sql.= "AND A.id != $not_id ";
    if (!empty($not_ids)) $sql.= "AND A.id NOT IN ($not_ids) ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($oferta != -1) $sql.= "AND A.id_promocion = $oferta ";
    if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
    if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND A.fecha_llegada <= '$fecha_hasta' ";
    if (!empty($tiene_video)) $sql.= "AND A.video != '' ";
    if (!empty($mes)) $sql.= "AND MONTH(A.fecha) = $mes ";
    if (!empty($anio)) $sql.= "AND YEAR(A.fecha) = $anio ";
    if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
    if (!empty($not_categoria)) $sql.= "AND C.link != '$not_categoria' ";
    if (!empty($ids_categorias)) $sql.= "AND A.id_categoria IN ($ids_categorias) ";
    if (!empty($not_ids_categorias)) $sql.= "AND A.id_categoria NOT IN ($not_ids_categorias) ";
    if (!empty($from_id_categoria)) {
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
      $sql.= "AND A.id_categoria IN ($ids_categorias) ";  
    }

    /*
    if (!empty($ids_etiquetas)) {
      if (is_array($ids_etiquetas)) $ids_etiquetas = implode(",", $ids_etiquetas);
      $sql.= "AND EXISTS (SELECT * FROM via_viajes_etiquetas EE WHERE A.id = EE.id_viaje AND EE.id_etiqueta IN ($ids_etiquetas)) ";
    }
    if (!empty($link_etiqueta)) {
      $sql.= "AND EXISTS (SELECT * FROM via_viajes_etiquetas EE INNER JOIN not_etiquetas E ON (EE.id_etiqueta = E.id) WHERE A.id = EE.id_viaje AND E.link = '$link_etiqueta') ";
    }
    */
    if ($order == 2) $sql.= "ORDER BY A.id DESC "; // ULTIMOS CARGADOS
    else if ($order == 3) $sql.= "ORDER BY A.id ASC "; // PRIMEROS CARGADOS
    else if ($order == 4) $sql.= "ORDER BY A.fecha_llegada ASC ";
    else if ($order == 5) $sql.= "ORDER BY A.fecha_llegada DESC ";
    else if ($order == 6) $sql.= "ORDER BY A.vistos DESC ";
    else if ($order == 7) $sql.= "ORDER BY A.precio ASC ";
    else if ($order == 8) $sql.= "ORDER BY A.precio DESC ";
    else if ($order == 9) $sql.= "ORDER BY A.orden ASC ";
    else if ($order == 10) $sql.= "ORDER BY A.orden DESC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (!empty($r->path)) {
        $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
      }

      if (!empty($r->path)) {
        $r->imagen = $r->path;
      } else if($r->latitud != 0 && $r->longitud != 0) {
        $r->imagen = "https://maps.googleapis.com/maps/api/staticmap?zoom=".$r->zoom."&size=470x246&maptype=roadmap&markers=color:red%7C".$r->latitud.",".$r->longitud."&key=AIzaSyDZ0GqtfheX506XJJ90TQOQJ2lp7yYRQkY";
      } else if (!empty($r->categoria_path)) {
        $r->imagen = "/sistema/".$r->categoria_path;
      }

      // Ponemos como precio el mas bajo (que no sea 0)
      $sql = "SELECT VP.* ";
      if (empty($lang) || $lang == "es") $sql.= ", IF(VT.nombre IS NULL,'',VT.nombre) AS nombre ";
      else if ($lang == "en") $sql.= ", IF(VT.nombre_en IS NULL,'',VT.nombre_en) AS nombre ";
      else if ($lang == "pt") $sql.= ", IF(VT.nombre_pt IS NULL,'',VT.nombre_pt) AS nombre ";    
      $sql.= "FROM via_viajes_vehiculos_precios VP ";
      $sql.= "LEFT JOIN via_tipos_tarifas VT ON (VP.id_tipo_tarifa = VT.id AND VP.id_empresa = VT.id_empresa) ";
      $sql.= "WHERE VP.id_viaje = $r->id AND VP.id_empresa = $r->id_empresa ";
      $sql.= "AND VP.precio > 0 ";
      if (!empty($fecha)) $sql.= "AND VP.fecha_desde <= '$fecha' AND '$fecha' <= VP.fecha_hasta ";
      if ($no_currency == 0) $sql.= ($currency == "USD") ? 'AND VP.moneda = "USD" ' : "AND VP.moneda = 'ARS' ";
      if ($orden_precios == 1) $sql.= "ORDER BY VP.precio ASC ";
      else if ($orden_precios == 2) $sql.= "ORDER BY VP.id ASC ";
      $sql.= "LIMIT 0,1 ";
      $q_precio = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q_precio)>0) {
        $precio = mysqli_fetch_object($q_precio);
        $r->precio = $precio->precio;
        $r->moneda = $precio->moneda;
        $r->tarifa = $precio->nombre;

        // Debemos hacer la conversion de monedas
        if ($mostrar_en_moneda == "ARS" && $precio->moneda == 'USD') {
          $r->precio = $r->precio * $cotizacion_dolar;
          $r->moneda = "ARS";
        }

      } else {
        $r->precio = "";
        $r->moneda = "";
        $r->tarifa = "";
      }

      // Obtenemos un array con todas las categorias de la entrada (para hacer breadcrumbs se utiliza)
      if ($buscar_categorias == 1) {
        $r->categorias = $this->get_categorias($r->id_categoria);
      }

      $r = $this->encoding($r);
      $salida[] = $r;
    }

    // Ordenamos por precio
    if ($order == "0") usort($salida,array('Viaje_Model','ordenar_asc'));
    else if ($order == "1") usort($salida,array('Viaje_Model',"ordenar_desc"));
    return $salida;
  }

  private static function ordenar_asc($a,$b) {
    return ($a->precio <= $b->precio) ? -1 : 1;
  }
  private static function ordenar_desc($a,$b) {
    return ($a->precio <= $b->precio) ? 1 : -1;
  }

  // Devuelve un array con todos los IDS de todos los descendientes de esa categoria padre
 function get_ids_subcategorias($id_categoria_padre,$config=array()) {
  $salida = array();
  $s = $this->get_subcategorias($id_categoria_padre,$config);
  foreach($s as $r) {
    $salida[] = $r->id;
  }
    $salida[] = $id_categoria_padre; // Incluimos el padre
    return $salida;
  }

  function get_total_results() {
    return $this->total;
  }

  private function encoding($entrada) {
    $entrada->plain_text = (!empty($entrada->observaciones)) ? mb_convert_encoding($entrada->observaciones, 'UTF-8', 'ISO-8859-1') : (mb_convert_encoding(strip_tags($entrada->texto,"<a><i><b><br>"), 'UTF-8', 'ISO-8859-1'));
    $entrada->texto = mb_convert_encoding($entrada->texto, 'UTF-8', 'ISO-8859-1');
    $entrada->nombre = mb_convert_encoding($entrada->nombre, 'UTF-8', 'ISO-8859-1');
    $entrada->subtitulo = mb_convert_encoding($entrada->subtitulo, 'UTF-8', 'ISO-8859-1');
    $entrada->categoria = mb_convert_encoding($entrada->categoria, 'UTF-8', 'ISO-8859-1');
    $entrada->custom_1 = mb_convert_encoding($entrada->custom_1, 'UTF-8', 'ISO-8859-1');
    $entrada->custom_2 = mb_convert_encoding($entrada->custom_2, 'UTF-8', 'ISO-8859-1');
    $entrada->custom_3 = mb_convert_encoding($entrada->custom_3, 'UTF-8', 'ISO-8859-1');
    $entrada->custom_4 = mb_convert_encoding($entrada->custom_4, 'UTF-8', 'ISO-8859-1');
    return $entrada;
  }

  function get_months($config = array()) {

    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
    $oferta = isset($config["oferta"]) ? $config["oferta"] : -1; // -1 = No se tiene en cuenta el parametro
    $filter = isset($config["filter"]) ? $config["filter"] : 0;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $categoria = isset($config["categoria"]) ? $config["categoria"] : "";
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
    $fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;

    $sql = "SELECT DISTINCT DATE_FORMAT(A.fecha,'%Y-%m') AS aniomes, COUNT(*) AS cantidad ";
    $sql.= "FROM via_viajes A ";
    $sql.= "LEFT JOIN via_viajes_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
    if ($oferta != -1) $sql.= "AND A.id_promocion = $oferta ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
    if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
    if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND A.fecha_llegada <= '$fecha_hasta' ";
    $sql.= "GROUP BY DATE_FORMAT(A.fecha,'%Y-%m') ";
    $sql.= "ORDER BY DATE_FORMAT(A.fecha,'%Y-%m') DESC ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) { 
      $r->anio = substr($r->aniomes, 0, strpos($r->aniomes, "-"));
      $r->mes = substr($r->aniomes, strpos($r->aniomes,"-")+1);
      switch ($r->mes) {
        case 1: $r->nombre_mes = "Enero"; break;
        case 2: $r->nombre_mes = "Febrero"; break;
        case 3: $r->nombre_mes = "Marzo"; break;
        case 4: $r->nombre_mes = "Abril"; break;
        case 5: $r->nombre_mes = "Mayo"; break;
        case 6: $r->nombre_mes = "Junio"; break;
        case 7: $r->nombre_mes = "Julio"; break;
        case 8: $r->nombre_mes = "Agosto"; break;
        case 9: $r->nombre_mes = "Septiembre"; break;
        case 10: $r->nombre_mes = "Octubre"; break;
        case 11: $r->nombre_mes = "Noviembre"; break;
        case 12: $r->nombre_mes = "Diciembre"; break;
      }
      $salida[] = $r; 
    }
    return $salida;
  }

  function get_opcionales_categorias($id_categoria_padre=0,$config=array()) {
    $activo = isset($config["activo"]) ? $config["activo"] : -1;
    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";
    $sql = "SELECT * ";
    if (!empty($lang)) $sql.= ", nombre_$lang AS nombre ";
    $sql.= "FROM via_opcionales_categorias A ";
    $sql.= "WHERE A.id_empresa = $this->id_empresa ";
    $sql.= "AND A.id_padre = $id_categoria_padre ";
    //if ($activo != -1) $sql.= "AND A.activo = $activo ";
    $sql.= "ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    if (mysqli_num_rows($q)>0) {
      while(($r=mysqli_fetch_object($q))!==NULL) {
        $r->children = $this->get_opcionales_categorias($r->id,$config);
        $r->nombre = mb_convert_encoding($r->nombre, 'UTF-8', 'ISO-8859-1');
        $salida[] = $r;
      }
    }
    return $salida;
  }

  function get_opcional_categoria($id,$config=array()) {
    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";
    $sql = "SELECT * ";
    if (!empty($lang)) $sql.= ", nombre_$lang AS nombre ";
    $sql.= "FROM via_opcionales_categorias ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id = $id ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)==0) return FALSE;
    $r = mysqli_fetch_object($q);
    $r->nombre = mb_convert_encoding($r->nombre, 'UTF-8', 'ISO-8859-1');
    return $r;
  }

  function get_opcionales($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 6;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";
    $currency = isset($config["currency"]) ? $config["currency"] : "ARS";
    $id_viaje = isset($config["id_viaje"]) ? $config["id_viaje"] : 0;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $categoria = isset($config["categoria"]) ? $config["categoria"] : "";
    $not_categoria = isset($config["not_categoria"]) ? $config["not_categoria"] : "";
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $not_ids = isset($config["not_ids"]) ? $config["not_ids"] : "";
    $order = isset($config["order"]) ? $config["order"] : "0";
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";

    // Indica cual precio debemos tomar de la LISTA DE PRECIOS
    // 1 = Mas bajo
    // 2 = Primer cargado
    $orden_precios = isset($config["orden_precios"]) ? $config["orden_precios"] : 1;

    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    if (!empty($lang)) $sql.= " A.nombre_$lang AS nombre, ";
    if (!empty($lang)) $sql.= " A.texto_$lang AS texto, ";
    if (!empty($lang)) $sql.= " IF(C.nombre_$lang IS NULL,'',C.nombre_$lang) AS categoria, ";
    else $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria, ";
    $sql.= " IF(C.link IS NULL,'',C.link) AS categoria_link, ";
    $sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path ";
    $sql.= "FROM via_opcionales A ";
    $sql.= "LEFT JOIN via_opcionales_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if ($not_id > 0) $sql.= "AND A.id != $not_id ";
    if (!empty($not_ids)) $sql.= "AND A.id NOT IN ($not_ids) ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
    if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
    if (!empty($not_categoria)) $sql.= "AND C.link != '$not_categoria' ";
    if (!empty($ids_categorias)) $sql.= "AND A.id_categoria IN ($ids_categorias) ";
    if (!empty($not_ids_categorias)) $sql.= "AND A.id_categoria NOT IN ($not_ids_categorias) ";
    if (!empty($id_viaje)) {
      $sql.= "AND EXISTS (SELECT * FROM via_viajes_opcionales VVO WHERE VVO.id_empresa = $this->id_empresa AND VVO.id_viaje = $id_viaje AND VVO.id_opcional = A.id) ";
    }
    $sql.= "ORDER BY A.orden ASC ";
    $sql.= "LIMIT $limit,$offset ";
    $this->sql = $sql;
    $q = mysqli_query($this->conx,$sql);
    if ($q === FALSE) {
      error_mail($this->sql);
      return array();
    }

    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (!empty($r->path)) {
        $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
      }
      $r->nombre = mb_convert_encoding($r->nombre, 'UTF-8', 'ISO-8859-1');
      $r->texto = mb_convert_encoding($r->texto, 'UTF-8', 'ISO-8859-1');
      $r->categoria = mb_convert_encoding($r->categoria, 'UTF-8', 'ISO-8859-1');
      $r->precio = 0;
      $r->moneda = "ARS";
      $r->precios = array();

      // Obtenemos los precios de los opcionales
      $sql = "SELECT VOP.* ";
      if (empty($lang) || $lang == "es") $sql.= ", IF(VT.nombre IS NULL,'',VT.nombre) AS nombre ";
      else if ($lang == "en") $sql.= ", IF(VT.nombre_en IS NULL,'',VT.nombre_en) AS nombre ";
      else if ($lang == "pt") $sql.= ", IF(VT.nombre_pt IS NULL,'',VT.nombre_pt) AS nombre ";    
      $sql.= "FROM via_opcionales_precios VOP ";
      $sql.= "LEFT JOIN via_tipos_tarifas VT ON (VOP.id_tipo_tarifa = VT.id AND VOP.id_empresa = VT.id_empresa) ";
      $sql.= "WHERE VOP.id_opcional = $r->id AND VOP.id_empresa = $this->id_empresa ";
      $sql.= ($currency == "USD") ? 'AND VOP.moneda = "USD" ' : 'AND VOP.moneda = "ARS" ';
      if (!empty($fecha)) $sql.= "AND VOP.fecha_desde <= '$fecha' AND '$fecha' <= VOP.fecha_hasta ";
      if ($orden_precios == 1) $sql.= "ORDER BY VOP.precio ASC ";
      else if ($orden_precios == 2) $sql.= "ORDER BY VOP.id ASC ";
      $q_precios = mysqli_query($this->conx,$sql);
      $jj=0;
      while(($precio=mysqli_fetch_object($q_precios))!==NULL) {
        $precio->nombre = mb_convert_encoding($precio->nombre, 'UTF-8', 'ISO-8859-1');
        $r->precios[] = $precio;
        if ($jj==0) {
          $r->precio = $precio->precio;
          $r->moneda = $precio->moneda;
        }
        $jj++;
      }

      $salida[] = $r;
    }
    return $salida;
  }



  /**
   * Obtiene las entradas destacadas
   */
  function destacados($config = array()) {

    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["order"] = 2;
    $config["destacado"] = 1;
    return $this->get_list($config);

  }


  /**
   * Obtiene las entradas mas vistas en un determinado lapso de tiempo
   */
  function mas_vistos($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 4;
    $config["order"] = "6";
    return $this->get_list($config);
  }


  function ofertas($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 4;
    return $this->get_list($config);
  }


  function ultimos($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["order"] = "2";
    return $this->get_list($config);
  }

  function ultimos_videos($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["tiene_video"] = 1;
    $config["order"] = "2";
    return $this->get_list($config);
  }

}
?>