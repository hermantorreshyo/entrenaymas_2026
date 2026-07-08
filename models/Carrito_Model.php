<?php
class Carrito_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $forzar_guardar = 0; // Bandera que indica que hubo un cambio importante en el carrito, y que es necesario guardarlo

  function __construct($id_empresa,$conx) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  // ELIMINAR DESPUES
  function get_costo_envio_fijo() {
    return 0;
  }

  function es_toque() {
    return ($this->id_empresa == 571 || $this->id_empresa == 1216 || $this->id_empresa == 1234);
  }

  function log($linea,$archivo = "") {
    $base = "/home/ubuntu/data/sistema/logs/";
    if (!file_exists($base.$this->id_empresa)) mkdir($base.$this->id_empresa);
    if (empty($archivo)) $archivo = $this->id_empresa.".txt";
    @file_put_contents($base.$this->id_empresa."/".$archivo, date("Y-m-d H:i:s").": ".$linea."\n", FILE_APPEND);
  }

  // Obtenemos el metodo de envio configurado en la empresa
  function get_metodo_envio($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $sql = "SELECT * FROM env_configuracion WHERE id_empresa = $id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $forma_envio = mysqli_fetch_object($q);

    // EN CASO DE QUE SEA MERCADOENVIOS, SE TOMAN LAS EXCEPCIONES
    $forma_envio->excepciones_unicas = array(); // Si el codigo postal se repite, en este array se pone una sola vez
    $forma_envio->excepciones = array();
    $q = mysqli_query($this->conx,"SELECT * FROM env_excepciones WHERE id_empresa = $id_empresa AND tipo = 0 ORDER BY monto_desde DESC");
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $forma_envio->excepciones[] = $r;
      $forma_envio->excepciones_unicas[] = trim($r->codigo_postal);
    }
    $forma_envio->excepciones_unicas = array_unique($forma_envio->excepciones_unicas);

    // EN CASO DE QUE SEA REPARTO, SE TOMA ESTE ARRAY
    $forma_envio->valores_unicos = array(); // Si el codigo postal se repite, en este array se pone una sola vez
    $forma_envio->valores = array();
    $q = mysqli_query($this->conx,"SELECT * FROM env_excepciones WHERE id_empresa = $id_empresa AND tipo = 1 ORDER BY monto_desde DESC");
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $forma_envio->valores[] = $r;
      $forma_envio->valores_unicos[] = trim($r->codigo_postal);
    }
    $forma_envio->valores_unicos = array_unique($forma_envio->valores_unicos);

    return $forma_envio;
  }

  function get_paypal() {
    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_paypal == 0 || empty($medio->paypal_email)) return FALSE;
    return $medio->paypal_email;
  } 

  function get_stripe() {
    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_stripe == 0 || empty($medio->stripe_public) || empty($medio->stripe_secret)) return FALSE;
    return $medio;
  } 

  function get_excepcion_envio($config = array()) {
    $id = isset($config["id"]) ? $config["id"] : 0;
    $codigo_postal = isset($config["codigo_postal"]) ? $config["codigo_postal"] : "";
    $monto_desde = isset($config["monto_desde"]) ? $config["monto_desde"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 0; // 0 = MercadoLibre | 1 = Reparto Propio
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $sql = "SELECT * FROM env_excepciones WHERE id_empresa = $id_empresa ";
    $sql.= "AND codigo_postal = '$codigo_postal' ";
    $sql.= "AND tipo = '$tipo' ";
    $sql.= "AND IF(monto_desde > 0,IF($monto_desde > monto_desde,1,0),1) = 1 ";
    if (!empty($id)) $sql.= "AND id = $id ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $row = mysqli_fetch_object($q);
      return $row;
    } else {
      return FALSE;
    }
  }

  function get_zonas_envio($config = array()) {
    $codigo_postal = isset($config["codigo_postal"]) ? $config["codigo_postal"] : "";
    $sql = "SELECT * FROM env_zonas WHERE id_empresa = $this->id_empresa AND activo = 1 ";
    if (!empty($codigo_postal)) $sql.= "AND codigos_postales LIKE '%$codigo_postal%' ";
    $sql.= "ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    $zonas_envio = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r->nombre = ($r->nombre);
      $zonas_envio[] = $r;
    }
    return $zonas_envio;
  }

  function get_mercadopago($numero_carrito = 0, $config = array()) {

    $client_id = isset($config["client_id"]) ? $config["client_id"] : "";
    $client_secret = isset($config["client_secret"]) ? $config["client_secret"] : "";

    require_once("mercadopago.php");

    if (empty($client_id) && empty($client_secret)) {

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

    } else {
      $mp_client_id = $client_id;
      $mp_client_secret = $client_secret;
    }

    return new MP($mp_client_id, $mp_client_secret);
  }

  function get_cuenta_vulcatires($id_localidad,$id_provincia) {
    // Si es la Ciudad de Buenos Aires
    if ($id_provincia == 24) {
      // SAN MARINO
      return "SAN_MARINO";

    // Si es la provincia de Buenos Aires
    } else if ($id_provincia == 1) {

      // Buscamos los departamentos
      $sql = "SELECT D.* FROM com_localidades L INNER JOIN com_departamentos D ON (L.id_departamento = D.id) WHERE L.id = $id_localidad AND D.id_provincia = $id_provincia ";
      $q = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q)>0) {
        $departamento = mysqli_fetch_object($q);
        if ($departamento->id == 23   || // Leandro N Alem
            $departamento->id == 38   || // General Pinto
            $departamento->id == 64   || // Villegas
            $departamento->id == 708  || // Ameghino
            $departamento->id == 46   || // Lincoln
            $departamento->id == 101  || // Carlos Tejedor
            $departamento->id == 58   || // Rivadavia
            $departamento->id == 11   || // Pehuajo
            $departamento->id == 6    || // Trenque Lauquen
            $departamento->id == 78   || // Pellegrini
            $departamento->id == 709  || // Tres Lomas
            $departamento->id == 81   || // Salliquelo
            $departamento->id == 105  || // Hipolito Yrigoyen
            $departamento->id == 40   || // Daireaux
            $departamento->id == 21   || // Guamini
            $departamento->id == 43   || // Adolfo Alsina
            $departamento->id == 69   || // Coronel Suarez
            $departamento->id == 122  || // La Madrid
            $departamento->id == 31   || // Saavedra
            $departamento->id == 2    || // Puan
            $departamento->id == 98   || // Tornquist
            $departamento->id == 107  || // Pringles
            $departamento->id == 111  || // Gonzalez Chavez
            $departamento->id == 63   || // Tres Arroyos
            $departamento->id == 42   || // Coronel Dorrego
            $departamento->id == 710  || // Monte Hermoso
            $departamento->id == 29   || // Rosales
            $departamento->id == 18   || // Bahia Blanca
            $departamento->id == 47   || // Villarino
            $departamento->id == 59   || // Patagones
            $departamento->id == 133     // Laprida
        ) {
          // VULCA PATAGONIA
          return "PATAGONIA";

        } else if (
          $departamento->id == 7    || // 9 de Julio
          $departamento->id == 71   || // Carlos Casares
          $departamento->id == 128  || // Bolivar
          $departamento->id == 5    || // 25 de Mayo
          $departamento->id == 35   || // Saladillo
          $departamento->id == 131  || // Roque Perez
          $departamento->id == 116  || // General Alvear
          $departamento->id == 34   || // Tapalque
          $departamento->id == 106  || // Las Flores
          $departamento->id == 97   || // General Belgrano
          $departamento->id == 15   || // Chascomus
          $departamento->id == 36   || // Magdalena
          $departamento->id == 77   || // Olavarria
          $departamento->id == 1    || // Azul
          $departamento->id == 96   || // Rauch
          $departamento->id == 90   || // Pila
          $departamento->id == 93   || // Castelli
          $departamento->id == 112  || // Dolores
          $departamento->id == 118  || // Tordillo
          $departamento->id == 119  || // General Guido
          $departamento->id == 123  || // General Lavalle
          $departamento->id == 711  || // La Costa
          $departamento->id == 712  || // Pinamar
          $departamento->id == 713  || // Villa Gesell
          $departamento->id == 121  || // Juan Madariaga
          $departamento->id == 83   || // Mar Chiquita
          $departamento->id == 55   || // Ayacucho
          $departamento->id == 57   || // Tandil
          $departamento->id == 67   || // Benito Juarez
          $departamento->id == 110  || // San Cayetano
          $departamento->id == 99   || // Necochea
          $departamento->id == 114  || // Loberia
          $departamento->id == 62   || // Balcarce
          $departamento->id == 103  || // General Alvarado
          $departamento->id == 84   || // General Pueyrredon
          $departamento->id == 134     // Maipu
        ) {
          // VULCA MAR DEL PLATA
          return "MAR_DEL_PLATA";

        } else {
          // VULCA SAN MARINO
          return "SAN_MARINO";
        }
      }

    // Jujuy, Salta, Formosa, Chaco, Santiago del Estero, Tucuman, Catamarca, La Rioja, San Juan,
    // Cordoba, Santa Fe, Entre Rios, Corrientes y Misiones
    } else if (
      $id_provincia == 9 || // Jujuy
      $id_provincia == 16 || // Salta
      $id_provincia == 8 || // Formosa
      $id_provincia == 3 || // Chaco
      $id_provincia == 21 || // Santiago del Estero
      $id_provincia == 23 || // Tucuman
      $id_provincia == 2 || // Catamarca
      $id_provincia == 11 || // La Rioja
      $id_provincia == 17 || // San Juan
      $id_provincia == 5 || // Cordoba
      $id_provincia == 20 || // Santa Fe
      $id_provincia == 7 || // Entre Rios
      $id_provincia == 6 || // Corrientes
      $id_provincia == 13 // Misiones
    ) {
      // SAN MARINO
      return "SAN_MARINO";

    // Las provincias que quedan
    // VULCA PATAGONIA
    } else {
      return "PATAGONIA";
    }    
  }

  function get_mercadopago_vulcatires($id_localidad = 0,$id_provincia = 0,$cuenta = null) {

    $mp_client_id = "";
    $mp_client_secret = "";
    require_once("mercadopago.php");

    if ($cuenta == null) {
      $cuenta = $this->get_cuenta_vulcatires($id_localidad,$id_provincia);  
    }
    if ($cuenta == "SAN_MARINO") {
      // VULCATIRES SAN MARINO
      $mp_client_id = "3867137433303042";
      $mp_client_secret = "R6Q0HUSKPxhZdnjHLOgarzPZFqEwIfbx";      

    } else if ($cuenta == "PATAGONIA") {
      // VULCATIRES PATAGONIA
      $mp_client_id = "5189978616917685";
      $mp_client_secret = "4MyrtSLJz3mfdlAgIPQpbluObP19aF1X";

    } else if ($cuenta == "MAR_DEL_PLATA") {
      // VULCATIRES MAR DEL PLATA
      $mp_client_id = "5611459995872686";
      $mp_client_secret = "GxHd0704CuCuKNEvMm9zC8rJZwabjc0D";
    }

    if (empty($mp_client_id) || empty($mp_client_secret)) return FALSE; // No fue configurado aun
    return new MP($mp_client_id, $mp_client_secret);
  }  

  function get_moneda_mercadopago() {
    $sql = "SELECT mp_moneda FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    return (empty($medio->mp_moneda) ? "ARS" : $medio->mp_moneda);
  }

  function get_transferencia_bancaria() {
    $sql = "SELECT habilitar_banco FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_banco == 0) return FALSE;
    return TRUE;
  }

  function get_convenir_pago() {
    $sql = "SELECT habilitar_a_convenir FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_a_convenir == 0) return FALSE;
    return TRUE;
  }

  function get_pago_sucursal() {
    $sql = "SELECT habilitar_pago_sucursal FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_pago_sucursal == 0) return FALSE;
    return TRUE;
  }

  function get_contrarrembolso($pedido = null) {
    $sql = "SELECT habilitar_contrarrembolso FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_contrarrembolso == 0) return FALSE;
    else if ($medio->habilitar_contrarrembolso == 1) return TRUE;
    else if ($medio->habilitar_contrarrembolso == 2) {
      // Verificamos si estamos pidiendo dentro de las excepciones de envio
      if (is_null($pedido)) return FALSE;
      if (!isset($pedido->excepcion_envio)) return FALSE;
      return ($pedido->excepcion_envio == 1);
    }
    return FALSE;
  }


  function get_post($clave,$default="") {
    return (isset($_POST[$clave]) ? filter_var($_POST[$clave],FILTER_SANITIZE_STRING) : $default);
  }
  function get_get($clave,$default="") {
    return (isset($_GET[$clave]) ? filter_var($_GET[$clave],FILTER_SANITIZE_STRING) : $default);
  }


  // DEVUELVE EL CODIGO DE PEDIDO PENDIENTE
  function get_pendiente() {
    return ($this->es_toque()) ? -1 : 0; // Pendiente
  }

  // Devuelve los pedidos pendientes
  function get_pedidos_pendientes($config = array()) {
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $test = isset($config["test"]) ? $config["test"] : 0;
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id_cliente = $id_cliente ";
    $sql.= "AND id_tipo_estado = ".$this->get_pendiente()." ";
    $sql.= "AND anulada = 0 ";
    if (!empty($not_id)) $sql.= "AND id != $not_id ";
    if ($test == 1) echo $sql;
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($item = mysqli_fetch_object($q))!==NULL) {
      $salida[] = $item;
    }
    return $salida;
  }

  // Devuelve los pedidos que todavia no fueron finalizados ni abandonados (TOQUE)
  function get_pedidos_en_proceso($config = array()) {
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $test = isset($config["test"]) ? $config["test"] : 0;
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id_cliente = $id_cliente ";
    $sql.= "AND id_tipo_estado < 6 ";
    $sql.= "AND id_tipo_estado >= 0 ";
    if ($this->id_empresa == 571) {
      $sql.= "AND id_usuario != 0 ";
      $sql.= "AND total > 0 ";
    }
    $sql.= "AND anulada = 0 ";
    if (!empty($not_id)) $sql.= "AND id != $not_id ";
    if ($test == 1) echo $sql;
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($item = mysqli_fetch_object($q))!==NULL) {
      $salida[] = $item;
    }
    return $salida;
  }  


  function crear_pedido($numero = 0) {
    $pedido = new stdClass();
    $pedido->id = 0;

    // Si existen las cookies del usuario
    if (isset($_COOKIE["id_cliente"]) && isset($_COOKIE["email"])) {
      $pedido->numero_paso = 1; // Arranca del 1, porque ya tenemos logueado el usuario
      $pedido->id_cliente = $_COOKIE["id_cliente"];
      $pedido->email = $_COOKIE["email"];

    } else {

      $pedido->numero_paso = 0; // Va llevando por cual paso va del carrito
      // En el Paso 0: Se registra el usuario
      $pedido->id_cliente = (isset($_SESSION["id_cliente"]) ? $_SESSION["id_cliente"] : 0);
      $pedido->email = (isset($_SESSION["email"]) ? $_SESSION["email"] : "");        
    }
    $pedido->codigo_postal = (isset($_COOKIE["codigo_postal"]) ? $_COOKIE["codigo_postal"] : "");
    $pedido->direccion = (isset($_COOKIE["direccion"]) ? $_COOKIE["direccion"] : "");
    $pedido->telefono = (isset($_COOKIE["telefono"]) ? $_COOKIE["telefono"] : "");
    $pedido->cliente = (isset($_COOKIE["nombre"]) ? $_COOKIE["nombre"] : "");

    // En el Paso 1: Se define la forma de envio
    $pedido->forma_envio = ""; // Retiro a sucursal, a domicilio, o a convenir
    $pedido->retirar_envio = 0;
    $pedido->coordinar_envio = -1;

    // En el Paso 2:
    $pedido->id_sucursal = 0;
    $pedido->id_localidad = 0;
    $pedido->localidad = "";
    $pedido->id_provincia = 0;
    $pedido->empresa_envio = ""; // MercadoEnvios, OCA, ANDREANI, propio
    $pedido->excepcion_envio = 0; // Indica si usa o no alguna excepcion de envio (por ej hay una configuracion en el pago contrarrembolso que solo se aplica si esto esta activo)

    // En el Paso 3: Tipo de envio
    $pedido->costo_envio = -1; // NO DEFINIDO
    $pedido->tipo_servicio = 0; // CORREO ARGENTINO: 1 = Puerta a Puerta / 2 = Sucursal Correo

    // En el Paso 4: Forma de pago
    $pedido->forma_pago = "";

    $pedido->id_punto_venta = 0;
    $pedido->punto_venta = 0;
    $pedido->nro_comprobante = 0;
    $pedido->comprobante = "";
    $pedido->id_tipo_comprobante = 999;
    $pedido->id_usuario = 0;
    $pedido->subtotal = 0;
    $pedido->neto = 0;
    $pedido->iva = 0;
    $pedido->total = 0;
    $pedido->peso_total = 0; // Peso total del carrito
    $pedido->peso_total_calculado = 0; // Peso total de los productos que no tienen envio gratis
    $pedido->ancho_total = 0;
    $pedido->alto_total = 0;
    $pedido->profundidad_total = 0;
    $pedido->cantidad = 0; // Cantidad total de articulos
    $pedido->id_tipo_estado = $this->get_pendiente();
    $pedido->fecha = date("Y-m-d");
    $pedido->hora = date("H:i:s");
    $pedido->items = array();
    $pedido->distancia = 0;
    $pedido->selecciono_envio = 0;
    $pedido->porc_descuento = 0;
    $pedido->codigo_promocional = "";
    $pedido->porc_descuento_promo = 0;
    $pedido->utiliza_codigo_promocional = 0; // ID REGLA OFERTA utilizada en el carrito
    $pedido->descuento = 0;
    $pedido->sucursal = "";
    $pedido->tipo_envio = ""; // Descripcion del tipo de envio
    $pedido->zona_envio = "";
    $pedido->observaciones = "";
    $pedido->custom_4 = "";
    $pedido->numero_envio = "";
    $pedido->numero = $numero;
    $compress = gzcompress(json_encode($pedido,JSON_UNESCAPED_UNICODE));
    setcookie("cart_".$pedido->numero,$compress,time()+(60*60*24),"/");
    return $pedido;
  }

  // Obtiene el carrito, pero de la base de datos
  function get($id,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $sql = "SELECT F.*, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= " IF(C.email IS NULL,'',C.email) AS email, ";
    $sql.= " IF(C.codigo_postal IS NULL,'',C.codigo_postal) AS codigo_postal, ";
    $sql.= " IF(C.direccion IS NULL,'',C.direccion) AS direccion, ";
    $sql.= " IF(C.telefono IS NULL,'',C.telefono) AS telefono, ";
    $sql.= " F.numero AS nro_comprobante, ";
    $sql.= " F.numero_remito AS numero ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_empresa = C.id_empresa AND F.id_cliente = C.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa AND F.id = $id ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q) <= 0) {
      return $this->crear_pedido();
    }
    $pedido = mysqli_fetch_object($q);
    $pedido->codigo_postal = (isset($_COOKIE["codigo_postal"]) ? $_COOKIE["codigo_postal"] : $pedido->codigo_postal);
    $pedido->direccion = (isset($_COOKIE["direccion"]) ? $_COOKIE["direccion"] : ($pedido->direccion));
    $pedido->telefono = (isset($_COOKIE["telefono"]) ? $_COOKIE["telefono"] : $pedido->telefono);
    $pedido->cliente = (isset($_COOKIE["nombre"]) ? $_COOKIE["nombre"] : $pedido->cliente);
    if (isset($_COOKIE["id_cliente"]) && isset($_COOKIE["email"])) {
      $pedido->numero_paso = 1; // Arranca del 1, porque ya tenemos logueado el usuario
      $pedido->id_cliente = $_COOKIE["id_cliente"];
      $pedido->email = $_COOKIE["email"];
    } else {
      $pedido->numero_paso = 0; // Va llevando por cual paso va del carrito
      // En el Paso 0: Se registra el usuario
      $pedido->id_cliente = (isset($_SESSION["id_cliente"]) ? $_SESSION["id_cliente"] : $pedido->id_cliente);
      $pedido->email = (isset($_SESSION["email"]) ? $_SESSION["email"] : $pedido->email);
    }
    $pedido->empresa_envio = "";
    $pedido->excepcion_envio = 0; // Indica si usa o no alguna excepcion de envio (por ej hay una configuracion en el pago contrarrembolso que solo se aplica si esto esta activo)
    $pedido->tipo_servicio = 0; // CORREO ARGENTINO: 1 = Puerta a Puerta / 2 = Sucursal Correo
    $pedido->forma_pago = "";
    $pedido->id_tipo_estado = $this->get_pendiente(); // Pendiente
    $pedido->peso_total = 0; // Peso total del carrito
    $pedido->peso_total_calculado = 0; // Peso total de los productos que no tienen envio gratis
    $pedido->ancho_total = 0;
    $pedido->alto_total = 0;
    $pedido->profundidad_total = 0;
    $pedido->cantidad = 0; // Cantidad total de articulos
    $pedido->distancia = 0;
    $pedido->id_provincia = 0;
    $pedido->selecciono_envio = 0;
    $pedido->sucursal = "";
    $pedido->tipo_envio = ""; // Descripcion del tipo de envio
    $pedido->zona_envio = "";
    $pedido->observaciones = "";
    $pedido->custom_4 = ""; // En toque observaciones para el repartidor
    $pedido->numero_envio = ""; // En toque se usa para marcar si se lleva o es pickup

    // Buscamos los items
    $pedido->items = array();    
    $sql = "SELECT FI.*, FI.total_con_iva AS total, ";
    $sql.= " IF(A.peso IS NULL,0,A.peso) AS peso, ";
    $sql.= " IF(A.alto IS NULL,0,A.alto) AS alto, ";
    $sql.= " IF(A.ancho IS NULL,0,A.ancho) AS ancho, ";
    $sql.= " IF(A.profundidad IS NULL,0,A.profundidad) AS profundidad, ";
    $sql.= " IF(A.no_totalizar_reparto IS NULL,0,A.no_totalizar_reparto) AS envio_gratis ";
    $sql.= "FROM facturas_items FI ";
    $sql.= "LEFT JOIN articulos A ON (FI.id_empresa = A.id_empresa AND FI.id_articulo = A.id) ";
    $sql.= "WHERE FI.id_empresa = $id_empresa AND FI.id_factura = $id AND FI.id_punto_venta = $pedido->id_punto_venta ";
    $qq = mysqli_query($this->conx,$sql);
    while(($item = mysqli_fetch_object($qq))!==NULL) {
      // TODO: tener en cuenta estos dos campos
      $item->coordinar_envio = 0;
      $item->adicional = 0; 
      $pedido->items[] = $item;
    }

    $this->recalcular($pedido);

    $compress = gzcompress(json_encode($pedido,JSON_UNESCAPED_UNICODE));
    setcookie("cart_".$pedido->numero,$compress,time()+(60*60*24),"/");
    return $pedido;
  }

  function controlar_stock() {
    $carrito = $this->get_carrito();
    include_once("Articulo_Model.php");
    $articulo_model = new Articulo_Model($this->id_empresa,$this->conx);

    // Primero obtenemos el punto de venta que esta funcionando con la web
    $punto_venta_web = $articulo_model->get_punto_venta_web();

    $error = 0;
    $mensajes = array();

    foreach($carrito->items as $item) {
      $articulo = $articulo_model->get($item->id_articulo,array(
        "buscar_relacionados"=>0,
        "buscar_imagenes"=>0,
        "buscar_clientes"=>0,
        "buscar_etiquetas"=>0,
        "buscar_variantes"=>0,
        "buscar_rubros"=>0,
        "consultar_stock"=>1,
        "id_sucursal"=>$punto_venta_web->id_sucursal, // Tomamos la sucursal web que tiene el stock
      ));
      if ($articulo === FALSE) {
        // TODO: Lanzar un error
        continue;
      }

      // El articulo fue desactivado por alguna razon
      if ($articulo->lista_precios == 0) {
        $error = 1;
        $mensajes[] = $articulo->nombre;
        continue;
      }

      // Si ahora tenemos menos stock
      if ( ($articulo->stock - $articulo->reservado) < $item->cantidad) {
        $error = 1;
        $mensajes[] = $articulo->nombre." Stock actual: ".($articulo->stock > 0 ? $articulo->stock : 0)."\n";
      }
    }

    $salida = array();
    $salida["error"] = $error;
    if ($error == 1) {
      $salida["mensaje"] = "Los siguientes productos se acaban de quedar sin stock: \n";
      foreach($mensajes as $m) {
        $salida["mensaje"].= $m;
      }
    }
    echo json_encode($salida);
  }

  // Obtiene un crea un determinado carrito
  function get_carrito($numero = 0) {

    if (isset($_COOKIE["cart_".$numero])) {

      // Antes de devolverlo, tenemos que recalcularlo por si cambio algun precio
      $decompress = gzuncompress($_COOKIE["cart_".$numero]);
      $carrito = json_decode(htmlspecialchars_decode($decompress));
      if (is_null($carrito)) {
        return $this->crear_pedido($numero);
      }

      // En TOQUE desestimamos los carritos de mas de un dia
      if ($this->es_toque() && isset($carrito->fecha) && $carrito->fecha != date("Y-m-d")) {
        return $this->crear_pedido($numero);
      }

      // Si el carrito que esta tomando esta ABANDONADO, entonces creamos uno nuevo
      if ($this->es_toque() && isset($carrito->id_tipo_estado) && $carrito->id_tipo_estado == 7) {
        return $this->crear_pedido($numero);
      }      

      $this->recalcular($carrito);
      if ($this->forzar_guardar == 1) {
        $this->guardar($carrito);
        $this->forzar_guardar = 0;
      }
      return $carrito;

    } else {

      // CREAMOS UN NUEVO PEDIDO
      return $this->crear_pedido($numero);
    }
  }

  // Actualizamos los totales del carrito
  private function recalcular($pedido) {

    $subtotal = 0;
    $cantidad = 0;
    $ancho_total = 0;
    $alto_total = 0;
    $profundidad_total = 0;
    $peso_total = 0;
    $peso_total_calculado = 0;
    $coordinar_envio = -1;
    $iva = 0;
    $hora = date("H:i:s");
    $hoy = date("Y-m-d");
    $dia_semana = date("N");
    $descuento_item = 0;
    if ($dia_semana == 1) $dia_semana = "L";
    else if ($dia_semana == 2) $dia_semana = "M";
    else if ($dia_semana == 3) $dia_semana = "X";
    else if ($dia_semana == 4) $dia_semana = "J";
    else if ($dia_semana == 5) $dia_semana = "V";
    else if ($dia_semana == 6) $dia_semana = "S";
    else if ($dia_semana == 7) $dia_semana = "D";     

    $sql = "SELECT ";
    $sql.= " articulo_mostrar_precio_neto, tienda_lista_precios, ";
    $sql.= " tienda_descuento_cantidad_1,tienda_descuento_monto_1,tienda_descuento_porcentaje_1, ";
    $sql.= " tienda_descuento_cantidad_2,tienda_descuento_monto_2,tienda_descuento_porcentaje_2, ";
    $sql.= " tienda_descuento_cantidad_3,tienda_descuento_monto_3,tienda_descuento_porcentaje_3, ";
    $sql.= " tienda_descuento_cantidad_4,tienda_descuento_monto_4,tienda_descuento_porcentaje_4 ";
    $sql.= "FROM web_configuracion ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)<=0) return; // No puede no haber web_configuracion para la empresa
    $dd = mysqli_fetch_object($q);

    $articulos_unicos = array();

    $iterator = 0;
    if (!isset($pedido->items)) return;
    foreach($pedido->items as $item) {

      $sql = "SELECT ";
      if ($dd->articulo_mostrar_precio_neto == 1) {
        // Si tenemos que tomar los precios netos
        if ($dd->tienda_lista_precios == 0) $sql.= "precio_neto AS total "; // De la lista 1
        else $sql.= "precio_neto_".$dd->tienda_lista_precios." AS total "; // De cualquier da las otras listas
      } else {
        // Tenemos que tomar los precios finales
        if ($dd->tienda_lista_precios == 0) $sql.= "precio_final_dto AS total "; // De la lista 1
        else $sql.= "precio_final_dto_".$dd->tienda_lista_precios." AS total "; // De cualquier de las otras listas
      }
      $sql.= ", custom_1 ";
      $sql.= "FROM articulos ";
      $sql.= "WHERE id_empresa = $this->id_empresa ";
      $sql.= "AND id = '$item->id_articulo' ";
      // TODO: Esto es para que TOQUE no actualice los precios
      if (!$this->es_toque()) $sql.= "AND lista_precios >= 2 ";
      $sql.= "LIMIT 0,1 ";
      $q_art = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q_art)>0) {
        $art = mysqli_fetch_object($q_art);
        $item->precio = $art->total;
        $item->custom_1 = $this->filtrar_string(($art->custom_1));
        $iterator++;
      } else {
        // El articulo no existe mas o fue desactivado
        // Debemos sacarlo directamente del carrito
        array_splice($pedido->items,$iterator,1);
        $this->forzar_guardar = 1;
        $iterator--;
        continue;
      }

      if (isset($item->adicional)) {
        // Para las variantes (de TOQUE) que tienen costos adicionales
        $item->precio += $item->adicional;
      }

      $item->total = $item->precio * $item->cantidad;
      $subtotal = $subtotal + $item->total;
      $cantidad = $cantidad + $item->cantidad;
      $peso_total = $peso_total + ($item->peso * $item->cantidad);
      if ($item->envio_gratis == 0) {
        $peso_total_calculado = $peso_total_calculado + ($item->peso * $item->cantidad);
      }
      // Al ancho total se va sumando
      $ancho_total = $ancho_total + ($item->ancho * $item->cantidad);
      // Mientras que el alto y la profundidad total se toman los maximos
      if ($item->alto > $alto_total) $alto_total = $item->alto;
      if ($item->profundidad > $profundidad_total) $profundidad_total = $item->profundidad;

      // Si algun item es necesario coordinar el envio, entonces al pedido le ponemos coordinar envio
      if (isset($item->coordinar_envio) && $item->coordinar_envio > $coordinar_envio) {
        $pedido->coordinar_envio = $item->coordinar_envio;
      }

      // Agregamos al array de articulos unicos
      // Esto se usa despues para comparar las ofertas
      $encontro_art = false;
      for($ii=0;$ii<sizeof($articulos_unicos);$ii++) {
        $unico = $articulos_unicos[$ii];
        if ($unico["id_articulo"] == $item->id_articulo) {
          $articulos_unicos[$ii]["cantidad"] = $articulos_unicos[$ii]["cantidad"] + $item->cantidad;
          $encontro_art = true;
          break;
        }
      }
      if (!$encontro_art) {
        $articulos_unicos[] = array("id_articulo"=>$item->id_articulo,"cantidad"=>$item->cantidad);
      }

    } // Fin for items

    $pedido->cantidad = $cantidad;
    $pedido->subtotal = $subtotal;

    if (isset($pedido->codigo_promocional) && !empty($pedido->codigo_promocional)) {
    
      // Si seteamos el codigo promocional, tomamos ese descuento
      $pedido->porc_descuento = $pedido->porc_descuento_promo;
      $pedido->descuento = $pedido->subtotal * (($pedido->porc_descuento)/100);          
      $descuento_item = $pedido->descuento;
    
    } else {

      // Controlamos si hay que aplicar algun descuento por cantidad o por monto
      if ($dd->tienda_descuento_cantidad_1 > 0 
        || $dd->tienda_descuento_cantidad_2 > 0 
        || $dd->tienda_descuento_cantidad_3 > 0 
        || $dd->tienda_descuento_cantidad_4 > 0) {
        if ($cantidad >= $dd->tienda_descuento_cantidad_1) {
          if ($cantidad >= $dd->tienda_descuento_cantidad_2) {
            if ($cantidad >= $dd->tienda_descuento_cantidad_3) {
              if ($cantidad >= $dd->tienda_descuento_cantidad_4) {
                $pedido->porc_descuento = $dd->tienda_descuento_porcentaje_4;
              } else {
                $pedido->porc_descuento = $dd->tienda_descuento_porcentaje_3;
              }
            } else {
              $pedido->porc_descuento = $dd->tienda_descuento_porcentaje_2;
            }
          } else {
            $pedido->porc_descuento = $dd->tienda_descuento_porcentaje_1;
          }
        } else {
          $pedido->porc_descuento = 0;
        }
        $pedido->descuento = $pedido->subtotal * (($pedido->porc_descuento)/100);
        $descuento_item = $pedido->descuento;
      }

      if ($dd->tienda_descuento_monto_1 > 0 
        || $dd->tienda_descuento_monto_2 > 0 
        || $dd->tienda_descuento_monto_3 > 0 
        || $dd->tienda_descuento_monto_4 > 0) {
        if ($subtotal >= $dd->tienda_descuento_monto_1) {
          if ($subtotal >= $dd->tienda_descuento_monto_2) {
            if ($subtotal >= $dd->tienda_descuento_monto_3) {
              if ($subtotal >= $dd->tienda_descuento_monto_4) {
                $pedido->porc_descuento = $dd->tienda_descuento_porcentaje_4;
              } else {
                $pedido->porc_descuento = $dd->tienda_descuento_porcentaje_3;
              }
            } else {
              $pedido->porc_descuento = $dd->tienda_descuento_porcentaje_2;
            }
          } else {
            $pedido->porc_descuento = $dd->tienda_descuento_porcentaje_1;
          }
        } else {
          $pedido->porc_descuento = 0;
        }
        $pedido->descuento = $pedido->subtotal * (($pedido->porc_descuento)/100);
        $descuento_item = $pedido->descuento;
      }

      // Si editamos el descuento desde el panel de control
      if (isset($pedido->id) && !empty($pedido->id) && isset($pedido->id_punto_venta) && !empty($pedido->id_punto_venta)) {
        $sql = "SELECT * FROM facturas WHERE id_empresa = $this->id_empresa AND id = $pedido->id AND id_punto_venta = $pedido->id_punto_venta";
        $q_pedido = mysqli_query($this->conx,$sql);
        if (mysqli_num_rows($q_pedido)>0) {
          $r_pedido = mysqli_fetch_object($q_pedido);
          $pedido->porc_descuento = $r_pedido->porc_descuento;
          $pedido->descuento = $pedido->subtotal * (($pedido->porc_descuento)/100);          
          $descuento_item = $pedido->descuento;
        }
      }
    }

    
    foreach($articulos_unicos as $unico) {
      // TODO: Esto es provisorio porque no estan funcionando los grupos
      // Controlamos si aplica alguna regla especial
      $sql = "SELECT RA.*, R.nombre ";
      $sql.= "FROM reglas_ofertas R ";
      $sql.= "INNER JOIN reglas_ofertas_articulos RA ON (R.id_empresa = RA.id_empresa AND RA.id_regla = R.id) ";
      $sql.= "WHERE RA.id_articulo = ".$unico["id_articulo"]." ";
      $sql.= "AND R.activo = 1 "; // La promo esta activa
      $sql.= "AND R.id_empresa = $this->id_empresa ";
      $sql.= "AND R.desde <= '$hoy $hora' AND '$hoy $hora' <= R.hasta "; // Entre tal fecha y tal fecha
      $sql.= "AND R.semana LIKE '%".$dia_semana."%' "; // Dia de la semana
      $sql.= "AND IF(R.hora_desde_1 = '',1,IF(R.hora_desde_1 <= '$hora',1,0)) = 1 "; // Entre las horas seleccionadas
      $sql.= "AND IF(R.hora_hasta_1 = '',1,IF(R.hora_hasta_1 >= '$hora',1,0)) = 1 ";
      $sql.= "AND IF(R.hora_desde_2 = '',1,IF(R.hora_desde_2 <= '$hora',1,0)) = 1 ";
      $sql.= "AND IF(R.hora_hasta_2 = '',1,IF(R.hora_hasta_2 >= '$hora',1,0)) = 1 ";
      $sql.= "AND ".$unico["cantidad"]." >= RA.minimo ";
      $q_oferta = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q_oferta)>0) {
        $oferta = mysqli_fetch_object($q_oferta);
        $pedido->custom_5 = $oferta->nombre;
        // Calculamos la cantidad de veces que se aplica la oferta
        $veces = (($oferta->minimo > 0) ? intval($unico["cantidad"] / $oferta->minimo) : 1);
        $descuento_item += ($oferta->descuento * $veces);
      }
    }

    // TODO: Esto es provisorio porque no estan funcionando los grupos
    // Controlamos si aplica alguna regla especial
    $sql = "SELECT R.* ";
    $sql.= "FROM reglas_ofertas R ";
    $sql.= "WHERE R.id_empresa = $this->id_empresa ";
    $sql.= "AND R.desde <= '$hoy $hora' AND '$hoy $hora' <= R.hasta "; // Entre tal fecha y tal fecha
    $sql.= "AND R.semana LIKE '%".$dia_semana."%' "; // Dia de la semana
    $sql.= "AND IF(R.hora_desde_1 = '',1,IF(R.hora_desde_1 <= '$hora',1,0)) = 1 "; // Entre las horas seleccionadas
    $sql.= "AND IF(R.hora_hasta_1 = '',1,IF(R.hora_hasta_1 >= '$hora',1,0)) = 1 ";
    $sql.= "AND IF(R.hora_desde_2 = '',1,IF(R.hora_desde_2 <= '$hora',1,0)) = 1 ";
    $sql.= "AND IF(R.hora_hasta_2 = '',1,IF(R.hora_hasta_2 >= '$hora',1,0)) = 1 ";
    $sql.= "AND $pedido->cantidad >= R.cantidad_minima "; // Supera la cantidad minima en productos
    $sql.= "AND $pedido->subtotal >= R.cantidad_minima_pesos "; // Supera la cantidad minima en plata
    $sql.= "AND R.activo = 1 "; // La promo esta activa
    if ($this->es_toque()) $sql.= "AND (R.id_usuario = $pedido->id_usuario OR R.id_usuario = 0) ";
    $q_oferta = mysqli_query($this->conx,$sql);
    while(($oferta = mysqli_fetch_object($q_oferta)) !== NULL) {

      $aplica_oferta = false;
      // Si el carrito estamos aplicando algun codigo especial
      if (!empty($oferta->codigo_especial)) {
        // Si los codigos son distintos, no aplicamos la oferta
        if (isset($pedido->codigo_promocional) && strtoupper($pedido->codigo_promocional) != strtoupper($oferta->codigo_especial)) {
          $aplica_oferta = false;
        } else {
          // Si tiene un limite, y la cantidad de veces que se uso es mayor, no aplicamos la oferta
          if ($oferta->codigo_limite_maximo > 0 && $oferta->codigo_cantidad_veces >= $oferta->codigo_limite_maximo) {
            $aplica_oferta = false;
          } else {
            // Si llegamos hasta aca, tenemos que aplicar la oferta
            // Ponemos un atributo del carrito para que despues lo podamos sumar que se uso ese codigo promocionar
            $pedido->utiliza_codigo_promocional = $oferta->id;
            $aplica_oferta = true;
          }
        }
      } else {
        // La oferta no tiene codigo PROMOCIONAL
        $aplica_oferta = true;
      }

      if ($aplica_oferta) {
        // Logueamos que estamos aplicando la oferta
        $this->log("Aplica regla $oferta->nombre",$pedido->id.".txt");
        $pedido->custom_5 = $oferta->nombre;

        // Si es sobre el total
        if ($oferta->accion == "total") {
          if ($oferta->descuento_porcentaje != 0) {
            $descuento_item += ($pedido->subtotal * $oferta->descuento_porcentaje / 100);
          } else if ($oferta->descuento_fijo != 0) {
            $descuento_item += $oferta->descuento_fijo;
          }
          $this->log("Descuento $descuento_item",$pedido->id.".txt");

        // Si es sobre el costo de envio
        /* TODO: ESTO AL FINAL LO HACEMOS EN TOQUE, EN EL MISMO CHECKOUT
        } else if ($oferta->accion == "costo_envio") {
          if ($this->es_toque() && isset($pedido->valor_envio_toque)) {
            if ($oferta->descuento_porcentaje != 0) {
              $descuento_item += ($pedido->valor_envio_toque * $oferta->descuento_porcentaje / 100);
            } else if ($oferta->descuento_fijo != 0) {
              $descuento_item += $oferta->descuento_fijo;
            }
            $this->log("Descuento $descuento_item",$pedido->id.".txt");
          }*/
        }
      }
    }

    // En la variable descuento_item fuimos acumulando el descuento que se tiene que aplicar
    $pedido->descuento = $descuento_item;    

    $pedido->peso_total = $peso_total;
    $pedido->peso_total_calculado = $peso_total_calculado;
    $pedido->ancho_total = $ancho_total;
    $pedido->alto_total = $alto_total;
    $pedido->profundidad_total = $profundidad_total;
    $pedido->iva = $iva;

    // Si el carrito ya no tiene elementos
    if (sizeof($pedido->items) == 0) {
      // Ponemos el costo de envio como NO DEFINIDO
      // TODO: Lo comente para probar
      //$pedido->costo_envio = -1;
      //$pedido->coordinar_envio = -1;
      if ($pedido->id != 0) $pedido->numero_paso = 1; // Volvemos a elegir la forma de envio
    }
    if ($pedido->retirar_envio == 1) {
      // Si el pedido se retira por sucursal, el costo de envio es CERO
      $pedido->costo_envio = 0;
    } else {
      // Recalculamos el costo de envio
      $this->calcular_costo_envio_carrito($pedido);
    }

    // Si tenemos que mostrar el precio neto
    if ($dd->articulo_mostrar_precio_neto == 1) {
      $descuento = $descuento_item;
      // Recorremos los articulos y tomamos el IVA
      foreach($pedido->items as $item) {
        $descuento_item = $item->total * ($pedido->porc_descuento / 100);
        $descuento += $descuento_item;
        $item->iva = ($item->total - $descuento_item) * ($item->porc_iva / 100);
        $pedido->iva += $item->iva;
      }
      $pedido->descuento = $descuento;
      $pedido->total = $pedido->subtotal - $pedido->descuento + $pedido->iva;

    } else {
      // Calculamos el total
      $pedido->total = $pedido->subtotal - $pedido->descuento;
    }

  }

  // Calculamos el costo de envio del carrito
  function calcular_costo_envio_carrito($carrito) {

    if ($carrito->forma_envio == "retiro_sucursal") return;
    $medio_envio = $this->get_metodo_envio();

    if ($medio_envio->forma_envio == "MERCADOENVIOS") {

      // Calculamos con MercadoEnvios
      $costo = $this->do_calcular_costo_envio_mercadoenvio(array(
        "peso"=>(isset($carrito->peso_total_calculado) ? $carrito->peso_total_calculado : 0),
        "ancho"=>(isset($carrito->ancho_total) ? $carrito->ancho_total : 0),
        "alto"=>(isset($carrito->alto_total) ? $carrito->alto_total : 0),
        "profundidad"=>(isset($carrito->profundidad_total) ? $carrito->profundidad_total : 0),
        "codigo_postal"=>(isset($carrito->codigo_postal) ? $carrito->codigo_postal : ""),
        "precio"=>$carrito->total,
        "coordinar_envio"=>$carrito->coordinar_envio,
        "pedido"=>$carrito,
      ));
      $carrito->costo_envio = $costo;

    } else if ($medio_envio->forma_envio == "REPARTO") {

      // Calculamos por Reparto Propio
      $carrito->costo_envio = $this->do_calcular_reparto(array(
        "codigo_postal"=>$carrito->codigo_postal,
        "pedido"=>$carrito,
      ));

    }
  }

  // Guarda el carrito en la base de datos
  function guardar($pedido) {

    include_once("Articulo_Model.php");
    $articulo_model = new Articulo_Model($this->id_empresa,$this->conx);

    // Primero recalculamos el carrito por si hubo alguna modificacion
    $this->recalcular($pedido);

    // Si el cliente esta logueado, el carrito se persiste en la base de datos
    if (isset($_COOKIE["id_cliente"]) || (isset($pedido->finalizar_clienapp))) {

      if (isset($_COOKIE["id_cliente"])) {
        $pedido->id_cliente = $_COOKIE["id_cliente"];
        $pedido->email = (isset($_COOKIE["email"]) ? $_COOKIE["email"] : "");
      }

      // Obtenemos los datos del cliente
      include_once("Cliente_Model.php");
      $cliente_model = new Cliente_Model($this->id_empresa,$this->conx);
      $cliente = $cliente_model->get($pedido->id_cliente);
      if ($cliente !== FALSE) {
        $pedido->cliente = (!empty($cliente->nombre) ? $cliente->nombre : $cliente->email);
        //$pedido->cliente = ($pedido->cliente);
      }

      if (isset($pedido->id_usuario) && $pedido->id_usuario != 0) {
        $sql = "SELECT * FROM com_usuarios WHERE id_empresa = $this->id_empresa AND id = $pedido->id_usuario ";
        $q_usuario = mysqli_query($this->conx,$sql);
        if (mysqli_num_rows($q_usuario)>0) {
          $r_usuario = mysqli_fetch_object($q_usuario);
          $pedido->usuario = $r_usuario->nombre;
        }
      }

      $items = $pedido->items;
      unset($pedido->items);
      
      $hoy = date("Y-m-d");
      $ahora = date("H:i:s");
      $id_vendedor = ($this->id_empresa == 133) ? 63 : 0;

      if (!$this->es_toque() && $this->id_empresa != 1284) {
        $pedido->observaciones = "";
        $pedido->custom_4 = "";
        $pedido->numero_envio = "";
      }
      if (isset($pedido->forma_pago)) {
        if ($pedido->forma_pago == "pago_sucursal") $pedido->observaciones.= "Pago en sucursal<br/>";
        else if ($pedido->forma_pago == "mercadopago") $pedido->observaciones.= "Pago con MercadoPago<br/>";
        else if ($pedido->forma_pago == "transferencia") $pedido->observaciones.= "Pago con transferencia bancaria<br/>";
        else if ($pedido->forma_pago == "contrarrembolso") $pedido->observaciones.= "Pago contrarrembolso<br/>";
        else if ($pedido->forma_pago == "a_convenir") $pedido->observaciones.= "Pago a convenir<br/>";
      }
      if (isset($pedido->tipo_envio) && !empty($pedido->tipo_envio)) $pedido->observaciones.= $pedido->tipo_envio."<br/>";
      if (isset($pedido->zona_envio) && !empty($pedido->zona_envio)) $pedido->observaciones.= $pedido->zona_envio."<br/>";
      if (isset($pedido->codigo_postal) && !empty($pedido->codigo_postal)) $pedido->observaciones.= $pedido->codigo_postal."<br/>";

      if (!isset($pedido->coordinar_envio)) $pedido->coordinar_envio = 0;
      if (!isset($pedido->forma_envio)) $pedido->forma_envio = "";
      if (!isset($pedido->nueva)) $pedido->nueva = 0;

      if (!isset($pedido->id) || $pedido->id == 0) {

        // TODO: Por el momento, se hace REMITO
        $pedido->id_tipo_comprobante = (isset($pedido->id_tipo_comprobante)) ? $pedido->id_tipo_comprobante : 999;

        // Buscamos el punto de venta asociado con la web
        $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
        $sql.= "FROM puntos_venta PV INNER JOIN web_configuracion CONF ON (PV.id = CONF.id_punto_venta AND PV.id_empresa = CONF.id_empresa) ";
        $sql.= "LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
        $sql.= "WHERE PV.id_empresa = $this->id_empresa ";
        $sql.= "LIMIT 0,1 ";
        $q = mysqli_query($this->conx,$sql);
        if (mysqli_num_rows($q)>0) {
          $pv = mysqli_fetch_object($q);
          $pedido->id_punto_venta = $pv->id;
          $pedido->punto_venta = $pv->numero;          
          $tipo_punto_venta = $pv->tipo_impresion;
          $sucursal = $pv->sucursal;
          $pedido->id_sucursal = $pv->id_sucursal;

          // Buscamos el proximo numero de comprobante
          $sql = "SELECT * FROM numeros_comprobantes ";
          $sql.= "WHERE id_punto_venta = $pv->id AND id_empresa = $this->id_empresa AND id_tipo_comprobante = $pedido->id_tipo_comprobante ";
          $sql.= "LIMIT 0,1 ";
          $q_numero = mysqli_query($this->conx,$sql);
          if (mysqli_num_rows($q_numero)>0) {
            $nro_comp = mysqli_fetch_object($q_numero);  
            $pedido->nro_comprobante = ((float)($nro_comp->ultimo)) + 1;
          } else {
            $pedido->nro_comprobante = 0;
          }

        } else {
          $pedido->id_punto_venta = 0;
          $pedido->punto_venta = 0;
          $pedido->nro_comprobante = 0;
          $tipo_punto_venta = "";
          $sucursal = "";
          $pedido->id_sucursal = 0;
        }

        // Ponemos el texto del comprobante
        if ($pedido->id_punto_venta > 0) {
          $sql = "SELECT * FROM tipos_comprobante WHERE id = $pedido->id_tipo_comprobante";
          $q = mysqli_query($this->conx,$sql);
          $tipo_comp = mysqli_fetch_object($q);  
          $pedido->comprobante = $tipo_comp->letra." ".str_pad($pedido->punto_venta, 4, "0", STR_PAD_LEFT)."-".str_pad($pedido->nro_comprobante, 8, "0", STR_PAD_LEFT);          
          $tipo_comprobante = $tipo_comp->nombre;
        } else {
          $pedido->comprobante = "";
          $tipo_comprobante = "";
        }

        $id_origen = 1; // ORIGEN: WEB

        // Debemos insertarlo
        $sql = "INSERT INTO facturas (";
        $sql.= " id_empresa, fecha, hora, id_cliente, id_tipo_comprobante, estado, observaciones, ";
        $sql.= " id_punto_venta, punto_venta, comprobante, numero, ";
        $sql.= " total, subtotal, neto, iva, porc_descuento, descuento, ";
        $sql.= " direccion, localidad, id_localidad, id_tipo_estado, costo_envio, ";
        $sql.= " retirar_envio, codigo_postal, numero_remito, id_usuario, id_vendedor, ";
        if (isset($pedido->usuario)) $sql.= "usuario, ";
        if (isset($pedido->custom_4)) $sql.= "custom_4, ";
        if (isset($pedido->numero_envio)) $sql.= "numero_envio, ";
        if (isset($pedido->custom_5)) $sql.= "custom_5, ";
        if (isset($pedido->custom_7)) $sql.= "custom_7, ";
        if (isset($pedido->custom_8)) $sql.= "custom_8, ";
        $sql.= " tipo_punto_venta, tipo_comprobante, id_origen, nueva, cliente, coordinar_envio, sucursal, forma_envio, id_sucursal ";
        $sql.= ") VALUES (";
        $sql.= " '$this->id_empresa', '$hoy', '$ahora', '$pedido->id_cliente', '$pedido->id_tipo_comprobante', 1, '$pedido->observaciones', ";
        $sql.= " '$pedido->id_punto_venta', '$pedido->punto_venta', '$pedido->comprobante', '$pedido->nro_comprobante', ";
        $sql.= " '$pedido->total', '$pedido->subtotal', '$pedido->subtotal', '$pedido->iva', '$pedido->porc_descuento', '$pedido->descuento',  ";
        $sql.= " '$pedido->direccion', '$pedido->localidad', '$pedido->id_localidad', '$pedido->id_tipo_estado', '$pedido->costo_envio', ";
        $sql.= " '$pedido->retirar_envio', '$pedido->codigo_postal', '$pedido->numero', '$pedido->id_usuario', $id_vendedor, ";
        if (isset($pedido->usuario)) $sql.= "'$pedido->usuario', ";
        if (isset($pedido->custom_4)) $sql.= "'$pedido->custom_4', ";
        if (isset($pedido->numero_envio)) $sql.= "'$pedido->numero_envio', ";
        if (isset($pedido->custom_5)) $sql.= "'$pedido->custom_5', ";
        if (isset($pedido->custom_7)) $sql.= "'$pedido->custom_7', ";
        if (isset($pedido->custom_8)) $sql.= "'$pedido->custom_8', ";
        $sql.= " '$tipo_punto_venta', '$tipo_comprobante', $id_origen, 1, '$pedido->cliente', '$pedido->coordinar_envio', '$sucursal', '$pedido->forma_envio', '$pedido->id_sucursal' ";
        $sql.= ")";
        $sql = ($sql);
        mysqli_query($this->conx,$sql);
        $id_pedido = mysqli_insert_id($this->conx);

        // Actualizamos el numero de comprobante
        $sql = "UPDATE numeros_comprobantes SET ultimo = $pedido->nro_comprobante ";
        $sql.= "WHERE id_punto_venta = $pedido->id_punto_venta AND id_empresa = $this->id_empresa AND id_tipo_comprobante = $pedido->id_tipo_comprobante ";
        mysqli_query($this->conx,$sql);

      } else {

        $id_pedido = $pedido->id;

        // Controlamos que el pedido existe y no fue eliminado por el admin
        $sql = "SELECT 1 FROM facturas WHERE id_empresa = $this->id_empresa AND id = $id_pedido AND id_punto_venta = $pedido->id_punto_venta ";
        $q_fact = mysqli_query($this->conx,$sql);
        if (mysqli_num_rows($q_fact) == 0) {
          // El pedido ya no existe mas en la base de datos, fue eliminado por el administrador
          // Debemos blanquear el carrito
          $this->crear_pedido($pedido->numero);
          return FALSE;
        }

        // Debemos actualizarlo
        
        $sql = "UPDATE facturas SET ";
        $sql.= " fecha = '$hoy', ";
        $sql.= " hora = '$ahora', ";
        $sql.= " id_cliente = '$pedido->id_cliente', ";
        //$sql.= " cliente = '$pedido->cliente', ";
        $sql.= " total = '$pedido->total', ";
        $sql.= " subtotal = '$pedido->subtotal', ";
        $sql.= " neto = '$pedido->subtotal', ";
        $sql.= " porc_descuento = '$pedido->porc_descuento', ";
        $sql.= " descuento = '$pedido->descuento', ";
        $sql.= " numero_remito = '$pedido->numero', ";
        $sql.= " id_usuario = '$pedido->id_usuario', ";
        $sql.= " iva = '$pedido->iva', ";
        $sql.= " observaciones = '$pedido->observaciones', ";
        $sql.= " direccion = '$pedido->direccion', ";
        $sql.= " localidad = '$pedido->localidad', ";
        $sql.= " id_localidad = '$pedido->id_localidad', ";
        $sql.= " id_tipo_estado = '$pedido->id_tipo_estado', ";
        $sql.= " costo_envio = '$pedido->costo_envio', ";
        $sql.= " retirar_envio = '$pedido->retirar_envio', ";
        $sql.= " coordinar_envio = '$pedido->coordinar_envio', ";
        $sql.= " forma_envio = '$pedido->forma_envio', ";
        $sql.= " nueva = '$pedido->nueva', ";
        if (isset($pedido->numero_envio)) $sql.= " numero_envio = '$pedido->numero_envio', ";
        if (isset($pedido->custom_4)) $sql.= " custom_4 = '$pedido->custom_4', ";
        if (isset($pedido->custom_5)) $sql.= " custom_5 = '$pedido->custom_5', ";
        if (isset($pedido->custom_7)) $sql.= " custom_7 = '$pedido->custom_7', ";
        if (isset($pedido->custom_8)) $sql.= " custom_8 = '$pedido->custom_8', ";
        if (isset($pedido->usuario)) $sql.= " usuario = '$pedido->usuario', ";
        if (isset($pedido->cta_cte)) $sql.= " cta_cte = '$pedido->cta_cte', ";
        if (isset($pedido->efectivo)) $sql.= " efectivo = '$pedido->efectivo', ";
        if (isset($pedido->vuelto)) $sql.= " vuelto = '$pedido->vuelto', ";
        if (isset($pedido->tarjeta)) $sql.= " tarjeta = '$pedido->tarjeta', ";
        if (isset($pedido->tarjeta)) $sql.= " id_sucursal = '$pedido->id_sucursal', ";
        $sql.= " codigo_postal = '$pedido->codigo_postal' ";
        $sql.= "WHERE id = $id_pedido AND id_empresa = $this->id_empresa AND id_punto_venta = $pedido->id_punto_venta ";
        $sql = ($sql);
        mysqli_query($this->conx,$sql);

        $sql = "DELETE FROM facturas_items WHERE id_factura = $id_pedido AND id_punto_venta = $pedido->id_punto_venta AND id_empresa = $this->id_empresa";
        mysqli_query($this->conx,$sql);
      }
      $i=0; $costo_final_pedido = 0;
      foreach($items as $l) {

        $l->nombre = $this->filtrar_string($l->nombre);
        if (isset($l->custom_1)) $l->custom_1 = $this->filtrar_string($l->custom_1);

        $sql = "SELECT * FROM articulos ";
        $sql.= "WHERE id_empresa = $this->id_empresa ";
        $sql.= "AND id = $l->id_articulo ";
        $q_art = mysqli_query($this->conx,$sql);
        if (mysqli_num_rows($q_art)<=0) continue;
        $art = mysqli_fetch_object($q_art);
        $nombre = $art->nombre;
        $l->cantidad = (float) $l->cantidad;
        $art->costo_final = (float) $art->costo_final;
        $costo_final = (float) ($l->cantidad * $art->costo_final);

        $custom_3 = isset($l->custom_3) ? $l->custom_3 : ""; // Utilizado para cuando el stock esta reservado
        $descripcion = isset($l->descripcion) ? $l->descripcion : "";
        $id_opcion_1 = isset($l->id_opcion_1) ? $l->id_opcion_1 : 0;
        $id_opcion_2 = isset($l->id_opcion_2) ? $l->id_opcion_2 : 0;
        $id_opcion_3 = isset($l->id_opcion_3) ? $l->id_opcion_3 : 0;
        $id_variante = isset($l->id_variante) ? $l->id_variante : 0;

        $sql = "INSERT INTO facturas_items (";
        $sql.= " id_empresa, id_factura, id_articulo, cantidad, nombre, id_punto_venta, ";
        $sql.= " precio, total_con_iva, orden, descripcion,";
        $sql.= " id_opcion_1, id_opcion_2, id_opcion_3, costo_final, id_variante, custom_3, anulado, negativo ";
        $sql.= ") VALUES (";
        $sql.= " '$this->id_empresa', '$id_pedido', '$l->id_articulo', '$l->cantidad','$nombre','$pedido->id_punto_venta',";
        $sql.= " '$l->precio','$l->total', '$i', '$descripcion',";
        $sql.= " '$l->id_opcion_1', '$l->id_opcion_2', '$l->id_opcion_3', '$costo_final', '$id_variante', '$custom_3', 0, 0 ";
        $sql.= ")";
        mysqli_query($this->conx,$sql);
        $costo_final_pedido += $costo_final;
        $i++;
      }

      $pedido->id = $id_pedido;
      $pedido->items = $items;

      // Si tiene seteado un nombre
      if (!empty($pedido->id_cliente)) {
        $sql = "UPDATE clientes SET ";
        //if(!empty($pedido->cliente)) $sql.= " nombre = '$pedido->cliente', ";
        if(!empty($pedido->direccion)) $sql.= " direccion = '$pedido->direccion', ";
        if(!empty($pedido->codigo_postal)) $sql.= " codigo_postal = '$pedido->codigo_postal', ";
        if(!empty($pedido->telefono)) $sql.= " telefono = '$pedido->telefono', ";
        if(!empty($pedido->id_localidad)) $sql.= " id_localidad = '$pedido->id_localidad', ";
        if(!empty($pedido->localidad)) $sql.= " localidad = '$pedido->localidad', ";
        $sql.= " id = '$pedido->id_cliente' ";
        $sql.= "WHERE id_empresa = $this->id_empresa AND id = '$pedido->id_cliente' ";
        $sql = ($sql);
        mysqli_query($this->conx,$sql);
      }

      // Actualizamos el costo final de la factura
      $sql = "UPDATE facturas SET costo_final = '$costo_final_pedido' ";
      $sql.= "WHERE id = $id_pedido AND id_empresa = $this->id_empresa AND id_punto_venta = $pedido->id_punto_venta ";
      mysqli_query($this->conx,$sql);

    }

    // Guardamos todo el carrito como una COOKIE
    $compress = gzcompress(json_encode($pedido));
    setcookie("cart_".$pedido->numero,$compress,time()+(60*60*24),"/");
  }

  function filtrar_string($s) {
    $s = str_replace("°", "", $s);
    return $s;
  }

  function actualizar() {
    $numero = $this->get_post("numero",0); // Identifica al carrito (puede haber mas de uno por cada web)
    $numero_paso = $this->get_post("numero_paso",0); // Identifica el numero de paso por el cual va el carrito
    
    $id_cliente = $this->get_post("id_cliente",0);
    $cliente = $this->get_post("cliente",0);
    $email = $this->get_post("email",0);
    $direccion = $this->get_post("direccion",0);
    $telefono = $this->get_post("telefono",0);
    $codigo_postal = $this->get_post("codigo_postal",0);
    $ajax = $this->get_post("ajax",0);
    $redirect = $this->get_post("redirect","/");
    $carrito = $this->get_carrito($numero);
    $carrito->numero_paso = $numero_paso;
    $carrito->mensaje_error = ""; // Limpiamos el error cuando cambia de paso

    if (!empty($id_cliente)) $carrito->id_cliente = $id_cliente;
    if (!empty($cliente)) $carrito->cliente = $cliente;
    if (!empty($email)) $carrito->email = $email;
    if (!empty($direccion)) $carrito->direccion = $direccion;
    if (!empty($telefono)) $carrito->telefono = $telefono;
    if (!empty($codigo_postal)) $carrito->codigo_postal = $codigo_postal;

    if ($numero_paso == 1) { 
      // ENTRENA Y MAS
      // La unica opcion disponible es envio a DOMICILIO, por lo tanto ya lo seteamos y seguimos al siguiente paso
      $carrito->forma_envio = "envio_domicilio";
      $carrito->forma_pago = "stripe";
      $carrito->numero_paso = 99;
    }

    // El pedido no puede ser modificado porque ya esta autorizado
    if ($carrito->id_tipo_estado > ($this->get_pendiente()+1)) {
      return;
    }
    $this->guardar($carrito);
    if ($ajax == 1) echo json_encode(array("error"=>0));
    else header("Location: $redirect");
  }

  function set_promo_code() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $numero = $this->get_post("numero",0); // Identifica al carrito (puede haber mas de uno por cada web)    
    $codigo_cupon = $this->get_post("codigo_cupon","");
    $codigo_cupon = trim($codigo_cupon);
    $carrito = $this->get_carrito($numero);
    $carrito->valor_envio_toque = $this->get_post("valor_envio_toque","");
    if (!empty($codigo_cupon)) {
      // Para guardar el codigo de cupon utilizado
      $carrito->custom_7 = $codigo_cupon;
    }

    $hoy = date("Y-m-d");
    $sql = "SELECT * FROM cupones_descuentos ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND (fecha_desde = '0000-00-00' OR fecha_desde <= '$hoy') ";
    $sql.= "AND (fecha_hasta = '0000-00-00' OR fecha_hasta >= '$hoy') ";
    $sql.= "AND codigo = '$codigo_cupon' ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $r = mysqli_fetch_object($q);
      $carrito->codigo_promocional = $r->codigo;
      $carrito->porc_descuento_promo = $r->descuento;
    } else {
      $carrito->codigo_promocional = $codigo_cupon;
      $carrito->porc_descuento_promo = 0;
    }
    $this->guardar($carrito);
    echo json_encode(array(
      "error"=>0,
    ));      
  }


  function modificar_item() {

    $numero = $this->get_post("numero",0); // Identifica al carrito (puede haber mas de uno por cada web)
    $id_articulo = $this->get_post("id_articulo",0);
    $nombre = $this->filtrar_string($this->get_post("nombre"));
    $categoria = $this->get_post("categoria");
    $redirect = $this->get_post("redirect","/");
    $cantidad = $this->get_post("cantidad",1);
    $precio = $this->get_post("precio",0);
    $peso = $this->get_post("peso",0);                // Peso unitario del item
    $ancho = $this->get_post("ancho",0);              // Ancho unitario del item
    $alto = $this->get_post("alto",0);                // Alto unitario del item
    $profundidad = $this->get_post("profundidad",0);  // Profundidad unitario del item
    $envio_gratis = $this->get_post("envio_gratis",0);// Indica si el envio es gratis o no para ese producto
    $id_usuario = $this->get_post("id_usuario",0);
    $porc_descuento = $this->get_post("porc_descuento",0);
    $porc_iva = $this->get_post("porc_iva",21);
    $descripcion = $this->get_post("descripcion","");
    $id_opcion_1 = $this->get_post("id_opcion_1",0);
    $id_opcion_2 = $this->get_post("id_opcion_2",0);
    $id_opcion_3 = $this->get_post("id_opcion_3",0);
    $id_variante = $this->get_post("id_variante",0);
    $coordinar_envio = $this->get_post("coordinar_envio",-1);
    $add_params = $this->get_post("add_params",0);
    
    $carrito = $this->get_carrito($numero);
    $carrito->id_usuario = $id_usuario;
    $carrito->porc_descuento = $porc_descuento;
    $carrito->mensaje_error = ""; // Limpiamos el mensaje de error

    // El pedido no puede ser modificado porque ya esta autorizado
    if ($carrito->id_tipo_estado > ($this->get_pendiente()+1)) {
      if ($redirect == "json") {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"El pedido no puede ser modificado.",
        ));
        exit();
      } else {
        return;
      }
    }

    // Controlamos que el articulo no fue pedido ya
    $encontro = FALSE;
    for($i=0;$i<sizeof($carrito->items);$i++) {
      $p = $carrito->items[$i];

      if ($this->es_toque()) {
        // Toque compara por id_articulo y nombre
        $condicion = ($p->id_articulo == $id_articulo && $p->nombre === $nombre);
      } else {
        $condicion = ($p->id_articulo == $id_articulo && $p->id_opcion_1 == $id_opcion_1 && $p->id_opcion_2 == $id_opcion_2 && $p->id_opcion_3 == $id_opcion_3 && $p->id_variante == $id_variante);
      }
      if ($condicion) {
        $encontro = TRUE;

        // Debemos modificar la cantidad
        if ($cantidad > 0) {
          $p->cantidad = $cantidad;
          $p->total = $p->cantidad * $p->precio;

        // Debemos eliminar el producto del array
        } else if ($cantidad == 0) {
          array_splice($carrito->items,$i,1);
        }
      }
    }
    // Si no encontramos el articulo y no se elimino el producto
    if (!$encontro && $cantidad > 0) {
      $item = new stdClass();
      $item->id_articulo = $id_articulo;
      $item->cantidad = $cantidad;
      $item->precio = $precio;
      $item->peso = $peso;
      $item->ancho = $ancho;
      $item->alto = $alto;
      $item->profundidad = $profundidad;
      $item->total = $item->cantidad * $item->precio;
      $item->porc_iva = $porc_iva;
      $item->id_opcion_1 = $id_opcion_1;
      $item->id_opcion_2 = $id_opcion_2;
      $item->id_opcion_3 = $id_opcion_3;
      $item->id_variante = $id_variante;
      $item->nombre = $nombre;
      $item->descripcion = $descripcion;
      $item->envio_gratis = $envio_gratis;
      $item->coordinar_envio = $coordinar_envio;
      $carrito->items[] = $item;

      // Agregamos los parametros de redireccion
      if ($add_params == 1) { 
        $redirect = $this->replace_params($redirect,array(
          "o"=>"add",
          "id"=>$id_articulo,
          "name"=>$nombre,
          "cat"=>$categoria,
          "cant"=>$item->cantidad,
        ));
      }
    }

    // Recalculamos
    $this->recalcular($carrito);

    $this->guardar($carrito);
    // Si en realidad en el parametro redirect le pasamos JSON
    if ($redirect == "json") {
      echo json_encode(array(
        "error"=>0,
        "total"=>(isset($carrito->total) ? $carrito->total : 0),
        "cantidad"=>(isset($carrito->cantidad) ? $carrito->cantidad : 0),
      ));
    } else {
      header("Location: $redirect");
    }
  }

  // Es igual a la funcion anterior, pero le llega un array de items en vez de un item en particular
  // La usa TOQUE para el tema de las variantes
  function modificar_items() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $numero = $this->get_post("numero",0); // Identifica al carrito (puede haber mas de uno por cada web)
    $porc_descuento = $this->get_post("porc_descuento",0);
    $id_usuario = $this->get_post("id_usuario",0);

    $carrito = $this->get_carrito($numero);
    $carrito->id_usuario = $id_usuario;
    $carrito->porc_descuento = $porc_descuento;

    // El pedido no puede ser modificado porque ya esta autorizado
    if ($carrito->id_tipo_estado > ($this->get_pendiente()+1)) {
      if ($redirect == "json") {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"El pedido no puede ser modificado.",
        ));
        exit();
      } else {
        return;
      }
    }    

    $items = json_decode($_POST["items"]);
    foreach($items as $item_nuevo) {
      // Controlamos que el articulo no fue pedido ya
      $encontro = FALSE;
      for($i=0;$i<sizeof($carrito->items);$i++) {
        $p = $carrito->items[$i];

        if ($this->es_toque()) {
          // Toque compara por id_articulo y nombre
          $condicion = ($p->id_articulo == $item_nuevo->id_articulo && $p->nombre == $item_nuevo->nombre);
        } else {
          $condicion = ($p->id_articulo == $item_nuevo->id_articulo && $p->id_opcion_1 == $item_nuevo->id_opcion_1 && $p->id_opcion_2 == $item_nuevo->id_opcion_2 && $p->id_opcion_3 == $item_nuevo->id_opcion_3 && $p->id_variante == $item_nuevo->id_variante);
        }
        if ($condicion) {
          $encontro = TRUE;

          // Debemos modificar la cantidad
          if ($item_nuevo->cantidad > 0) {
            $p->cantidad = $item_nuevo->cantidad;
            $p->total = $p->cantidad * $p->precio;

          // Debemos eliminar el producto del array
          } else if ($item_nuevo->cantidad == 0) {
            array_splice($carrito->items,$i,1);
          }
        }
      }
      // Si no encontramos el articulo y no se elimino el producto
      if (!$encontro && $item_nuevo->cantidad > 0) {
        $item = new stdClass();
        $item->id_articulo = $item_nuevo->id_articulo;
        $item->cantidad = $item_nuevo->cantidad;
        $item->precio = $item_nuevo->precio;
        $item->peso = isset($item_nuevo->peso) ? $item_nuevo->peso : 0;
        $item->adicional = isset($item_nuevo->adicional) ? $item_nuevo->adicional : 0;
        $item->ancho = isset($item_nuevo->ancho) ? $item_nuevo->ancho : 0;
        $item->alto = isset($item_nuevo->alto) ? $item_nuevo->alto : 0;
        $item->profundidad = isset($item_nuevo->profundidad) ? $item_nuevo->profundidad : 0;
        $item->total = $item->cantidad * $item->precio;
        $item->porc_iva = isset($item_nuevo->porc_iva) ? $item_nuevo->porc_iva : 21;
        $item->id_opcion_1 = isset($item_nuevo->id_opcion_1) ? $item_nuevo->id_opcion_1 : 0;
        $item->id_opcion_2 = isset($item_nuevo->id_opcion_2) ? $item_nuevo->id_opcion_2 : 0;
        $item->id_opcion_3 = isset($item_nuevo->id_opcion_3) ? $item_nuevo->id_opcion_3 : 0;
        $item->id_variante = isset($item_nuevo->id_variante) ? $item_nuevo->id_variante : 0;
        $item->nombre = isset($item_nuevo->nombre) ? $item_nuevo->nombre : "";
        $item->descripcion = isset($item_nuevo->descripcion) ? $item_nuevo->descripcion : "";
        $item->envio_gratis = isset($item_nuevo->envio_gratis) ? $item_nuevo->envio_gratis : 0;
        $item->coordinar_envio = isset($item_nuevo->coordinar_envio) ? $item_nuevo->coordinar_envio : 0;
        $carrito->items[] = $item;
      }
    }

    $this->guardar($carrito);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function set_forma_envio() {
    $numero = $this->get_post("numero",0);
    $forma_envio = $this->get_post("forma_envio","");
    $sucursal = $this->get_post("sucursal","");
    $carrito = $this->get_carrito($numero);    
    if (!empty($forma_envio)) $carrito->forma_envio = $forma_envio;
    if (!empty($sucursal)) $carrito->sucursal = $sucursal;

    // Dependiendo de la forma de envio, vamos a un paso u otro
    if ($forma_envio == "envio_domicilio") {
      $carrito->numero_paso = 2;
      $carrito->tipo_envio = "Envio a domicilio";

    } else if ($forma_envio == "retiro_sucursal") {
      $carrito->retiro_sucursal = 1;
      $carrito->numero_paso = 4;
      $carrito->costo_envio = 0;
      $carrito->tipo_envio = "Retiro en sucursal $carrito->sucursal";

      // Si es VULCA, al elegir la sucursal de envio como
      if ($this->id_empresa == 120) {
        if ($sucursal == "Avellaneda") {
          $carrito->id_provincia = 1;
          $carrito->id_localidad = 82;
        } else if ($sucursal == "Bahia Blanca") {
          $carrito->id_provincia = 1;
          $carrito->id_localidad = 92;
        } else if ($sucursal == "Mar del Plata") {
          $carrito->id_provincia = 1;
          $carrito->id_localidad = 600;
        }
      }

    } else if ($forma_envio == "a_convenir") {
      $carrito->retiro_sucursal = -1;
      $carrito->costo_envio = 0;
      $carrito->tipo_envio = "Envio a convenir";
      $carrito->numero_paso = 4;
    }
    
    $this->guardar($carrito);
    echo json_encode(array("error"=>0));
  }

  function set_forma_pago() {
    // Obtenemos los parametros
    $numero = $this->get_post("numero",0);
    $forma_pago = $this->get_post("forma_pago","");
    $carrito = $this->get_carrito($numero);    
    if (!empty($forma_pago)) $carrito->forma_pago = $forma_pago;
    $carrito->numero_paso = 99;
    $this->guardar($carrito);
    echo json_encode(array("error"=>0));
  }

  function set_tipo_servicio_envio() {
    // Obtenemos los parametros
    $numero = $this->get_post("numero",0);
    $tipo_servicio = $this->get_post("tipo_servicio","");
    $carrito = $this->get_carrito($numero);    
    if (!empty($tipo_servicio)) $carrito->tipo_servicio = $tipo_servicio;
    $carrito->numero_paso = 4;
    $this->guardar($carrito);
    echo json_encode(array("error"=>0));    
  }

  function go_cart() {
    $carrito = $this->get_carrito();
    if ($carrito->id_cliente != 0) {
      // Si ya tiene cliente, tenemos que saltar el paso del registro
      $carrito->numero_paso = 1;
    } else {
      // Si todavia no tiene cliente, volvemos todo para atras para que lo elija
      $carrito->numero_paso = 0;
    }
    $carrito->mensaje_error = "";
    $this->guardar($carrito);
    echo json_encode(array("error"=>0));    
  }

  function set_envio() {

    // Obtenemos los parametros
    $numero = $this->get_post("numero",0);
    $ajax = $this->get_post("ajax",0);
    $json = $this->get_post("json",0);
    $carrito = $this->get_carrito($numero);

    $retirar_envio = $this->get_post("retirar_envio",0);
    $distancia = $this->get_post("distancia",0);
    $direccion = isset($_POST["direccion"]) ? $_POST["direccion"] : "";
    $latitud = $this->get_post("latitud","");
    $longitud = $this->get_post("longitud","");
    $telefono = $this->get_post("telefono","");
    $celular = $this->get_post("celular","");
    $cliente = $this->get_post("cliente","");
    $codigo_postal = $this->get_post("codigo_postal","");
    $localidad = $this->get_post("localidad","");
    $id_localidad = $this->get_post("id_localidad",0);
    $id_provincia = $this->get_post("id_provincia",0);
    $id_sucursal = $this->get_post("id_sucursal",0);
    $tipo_servicio = $this->get_post("tipo_servicio",0);
    $sucursal = $this->get_post("sucursal","");
    $observaciones = $this->get_post("observaciones","");
    $custom_4 = $this->get_post("custom_4","");
    $numero_envio = $this->get_post("numero_envio","");
    $zona_envio = $this->get_post("zona_envio","");
    $empresa_envio = $this->get_post("empresa_envio","");
    //$selecciono_envio = 1; // Marcamos que ya se eligio para seleccionar el envio

    // Si los valores fueron seteados, tenemos que guardarlo en el carrito
    if (!empty($id_localidad)) $carrito->id_localidad = $id_localidad;
    if (!empty($localidad)) $carrito->localidad = $localidad;
    if (!empty($id_provincia)) $carrito->id_provincia = $id_provincia;
    if (!empty($id_sucursal)) $carrito->id_sucursal = $id_sucursal;
    if (!empty($tipo_servicio)) $carrito->tipo_servicio = $tipo_servicio;
    if (!empty($zona_envio)) $carrito->zona_envio = $zona_envio;
    if (!empty($empresa_envio)) $carrito->empresa_envio = $empresa_envio;
    $carrito->distancia = $distancia;
    if (!empty($observaciones)) $carrito->observaciones = $observaciones;
    if (!empty($custom_4)) $carrito->custom_4 = $custom_4;
    $carrito->numero_envio = $numero_envio;

    // TOQUE TENEMOS QUE ACTUALIZAR EL PISO Y EL DEPTO DEL CLIENTE
    if ((isset($_POST["piso"]) || isset($_POST["depto"])) && $carrito->id_cliente != 0) {
      $piso = isset($_POST["piso"]) ? $_POST["piso"] : "";
      $depto = isset($_POST["depto"]) ? $_POST["depto"] : "";
      $sql = "UPDATE clientes SET custom_3 = '$piso', custom_4 = '$depto' WHERE id = $carrito->id_cliente AND id_empresa = $this->id_empresa ";
      mysqli_query($this->conx,$sql);
    }    

    $tiempo = time()+(60*60*24*90);
    if (!empty($direccion)) {
      $carrito->direccion = $direccion;
      setcookie("direccion",$direccion,$tiempo,"/");
    }
    if (!empty($telefono)) {
      $carrito->telefono = $telefono;
      setcookie("telefono",$telefono,$tiempo,"/");
    }
    if (!empty($celular)) {
      $carrito->celular = $celular;
      setcookie("celular",$celular,$tiempo,"/");
    }
    /*if (!empty($cliente)) {
      $carrito->cliente = $cliente;
      setcookie("nombre",$cliente,$tiempo,"/");
    }*/
    if (!empty($codigo_postal)) {
      $carrito->codigo_postal = $codigo_postal;
      setcookie("codigo_postal",$codigo_postal,$tiempo,"/");
    }

    if (!empty($latitud)) setcookie("latitud",$latitud,$tiempo,"/");
    if (!empty($longitud)) setcookie("longitud",$longitud,$tiempo,"/");

    $carrito->mensaje_error = "";

    // Si hay productos fragiles que solo se envian a determinadas zonas
    if (!$this->controlar_envio_productos_fragiles($carrito) && ($carrito->forma_envio == "envio_domicilio" || $carrito->forma_envio == "MERCADOENVIOS")) {
      $carrito->mensaje_error = "ATENCIÓN: El tipo de producto seleccionado no puede enviarse a esa zona. Puede elegir otro método de envío en el paso anterior.";

    } else {

      // Si tiene una forma de pago, vamos al final directamente
      if (!empty($carrito->forma_pago)) $carrito->numero_paso = 99;
      // Sino vamos a definir la forma de pago
      else $carrito->numero_paso = 4; 
    }

    $redirect = $this->get_post("redirect","/");

    // Al guardar se llama a recalcular.. y dentro de recalcular esta el tema de calcular el costo de envio
    $this->guardar($carrito);

    if (($this->es_toque() || $this->id_empresa == 957) && isset($carrito->id) && isset($carrito->id_cliente)) {
      // Si es TOQUE, guardamos la latitud y longitud
      $direccion = ($direccion);
      $sql = "UPDATE clientes SET ";
      if (!empty($latitud)) $sql.= "latitud = '$latitud', ";
      if (!empty($longitud)) $sql.= "longitud = '$longitud', ";
      $sql.= "direccion = '$direccion', ";
      $sql.= "telefono = '$telefono', celular = '$celular' ";
      $sql.= "WHERE id_empresa = $this->id_empresa AND id = $carrito->id_cliente";
      mysqli_query($this->conx,$sql);
    }

    if ($ajax == 1 || $json == 1) echo json_encode(array("error"=>0));
    else header("Location: $redirect");
  }

  function mostrar() {
    $json = $this->get_post("json",0);
    $buscar_usuario = $this->get_post("buscar_usuario",0);
    $id_empresa = $this->get_post("id_empresa",$this->id_empresa);
    $decompress = gzuncompress($_COOKIE["cart_0"]);
    $carrito = json_decode(htmlspecialchars_decode($decompress));
    if (is_null($carrito)) {
      $carrito = $this->crear_pedido(0);
    }

    // Utilizado en Esteban Echeverria
    if ($buscar_usuario == 1 && $carrito->id_usuario != 0) {
      $carrito->usuario_celular = "";
      $carrito->usuario_path = "";
      $sql = "SELECT * FROM com_usuarios WHERE id_empresa = $id_empresa AND id = $carrito->id_usuario ";
      $q = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q)>0) {
        $usuario = mysqli_fetch_object($q);  
        $carrito->usuario_celular = $usuario->celular;
        $carrito->usuario = $usuario->nombre;
        $carrito->usuario_path = ((strpos($usuario->path,"http")===0)) ? $usuario->path : "/sistema/".$usuario->path;
      }
    }

    // PARA MOSTRAR EL CODIGO EN ESTEBAN ECHEVERRIA
    if ($this->id_empresa == 1284) {
      include_once("Articulo_Model.php");
      $articulo_model = new Articulo_Model($this->id_empresa,$this->conx);
      foreach($carrito->items as $item) {
        $articulo = $articulo_model->get($item->id_articulo,array(
          "buscar_relacionados"=>0,
          "buscar_imagenes"=>0,
          "buscar_clientes"=>0,
          "buscar_etiquetas"=>0,
          "buscar_variantes"=>0,
          "buscar_rubros"=>0,
          "consultar_stock"=>0,
        ));
        if ($articulo === FALSE) {
          // TODO: Lanzar un error
          continue;
        }
        $item->codigo = $articulo->codigo;
      }
    }

    if ($json == 1) {
      echo json_encode($carrito);
    } else {
      print_r($carrito);  
    }
  }

  function delete_envio() {
    $numero = $this->get_post("numero",0);
    $carrito = $this->get_carrito($numero);
    $carrito->selecciono_envio = 0;
    $carrito->tipo_envio = "";
    $redirect = $this->get_post("redirect","/");
    $this->guardar($carrito);
    header("Location: $redirect");
  }

  // Reemplaza los parametros de una URL
  private function replace_params($dir,$params = array()) {
    $pos = (strpos($dir, "?") === FALSE)?strlen($dir):strpos($dir, "?");
    return substr($dir,0,$pos)."?".http_build_query($params);
  }

  // Reemplaza todos los placeholder del email
  // @param $template: Objeto que viene del web_model->get_email
  // @param config: array con todos los placeholder posibles
  function replace_placeholder($template,$config = array()) {

    if (empty($template)) return FALSE;
    $id = isset($config["id"]) ? $config["id"] : "";
    $empresa = isset($config["empresa"]) ? $config["empresa"] : "";
    $link_web = isset($config["link_web"]) ? $config["link_web"] : "";
    $link_pedido_toque = isset($config["link_pedido_toque"]) ? $config["link_pedido_toque"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : "";
    $numero = isset($config["numero"]) ? $config["numero"] : "";
    $celular = isset($config["celular"]) ? $config["celular"] : "";
    $items = isset($config["items"]) ? $config["items"] : "";
    $email = isset($config["email"]) ? $config["email"] : "";
    $cliente = isset($config["cliente"]) ? $config["cliente"] : "";
    $usuario = isset($config["usuario"]) ? $config["usuario"] : "";
    $comercio_direccion = isset($config["comercio_direccion"]) ? $config["comercio_direccion"] : "";
    $direccion = isset($config["direccion"]) ? $config["direccion"] : "";
    $total = isset($config["total"]) ? $config["total"] : "";
    $total_con_envio = isset($config["total_con_envio"]) ? $config["total_con_envio"] : "";
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : "";
    $forma_pago = isset($config["forma_pago"]) ? $config["forma_pago"] : "";
    $costo_envio = isset($config["costo_envio"]) ? $config["costo_envio"] : "";
    $codigo_activacion = isset($config["codigo_activacion"]) ? $config["codigo_activacion"] : "";
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("d/m/Y");
    $hora = isset($config["hora"]) ? $config["hora"] : date("H:i");

    // Primero reemplazamos el asunto
    if (empty($template->nombre)) $template->nombre = "Compra exitosa";
    $template->nombre = str_replace("{{numero}}", $numero, $template->nombre);
    $template->nombre = str_replace("{{cliente}}", $cliente, $template->nombre);
    $template->nombre = str_replace("{{client}}", $cliente, $template->nombre);

    // Despues hacemos el reemplazo en el cuerpo del email
    if (empty($template->texto)) $template->texto = "La compra de {{cliente}} ha sido pagada.";
    if (!empty($total)) $template->texto = str_replace("{{total}}", $total, $template->texto);
    if (!empty($total_con_envio)) $template->texto = str_replace("{{total_con_envio}}", $total_con_envio, $template->texto);
    
    if (!empty($costo_envio)) $template->texto = str_replace("{{costo_envio}}", $costo_envio, $template->texto);
    else $template->texto = str_replace("{{costo_envio}}", "", $template->texto);

    if (!empty($cliente)) {
      $template->texto = str_replace("{{cliente}}", ($cliente), $template->texto);
      $template->texto = str_replace("{{client}}", ($cliente), $template->texto);
    }
    
    if (!empty($observaciones)) $template->texto = str_replace("{{observaciones}}", ($observaciones), $template->texto);
    else $template->texto = str_replace("{{observaciones}}", "", $template->texto);
    
    if (!empty($usuario)) {
      $template->texto = str_replace("{{usuario}}", $usuario, $template->texto);
      $template->texto = str_replace("{{comercio}}", $usuario, $template->texto);
    }

    if (!empty($codigo_activacion)) $template->texto = str_replace("{{codigo_activacion}}", $codigo_activacion, $template->texto);
    if (!empty($celular)) $template->texto = str_replace("{{celular}}", $celular, $template->texto);
    if (!empty($email)) $template->texto = str_replace("{{email}}", $email, $template->texto);
    if (!empty($direccion)) $template->texto = str_replace("{{direccion}}", ($direccion), $template->texto);
    if (!empty($comercio_direccion)) $template->texto = str_replace("{{comercio_direccion}}", ($comercio_direccion), $template->texto);
    if (!empty($forma_pago)) $template->texto = str_replace("{{forma_pago}}", $forma_pago, $template->texto);
    if (!empty($numero)) $template->texto = str_replace("{{numero}}", $numero, $template->texto);
    if (!empty($fecha)) $template->texto = str_replace("{{fecha}}", $fecha, $template->texto);
    if (!empty($hora)) $template->texto = str_replace("{{hora}}", $hora, $template->texto);
    if (!empty($id)) {
      $link_ver_pedido = mklink("sistema/facturas/function/ver_pdf/".$id."/0/".$this->id_empresa."/");
      $template->texto = str_replace("{{link_ver_pedido}}",$link_ver_pedido,$template->texto);
      $template->texto = str_replace("{{link_pedido}}",$link_ver_pedido,$template->texto);
    }
    if (!empty($link_web)) $template->texto = str_replace("{{link_web}}",$link_web,$template->texto);  
    if (!empty($empresa)) {
      $template->texto = str_replace("{{empresa}}",$empresa,$template->texto);
      $template->texto = str_replace("{{company}}",$empresa,$template->texto);
    }
    if (!empty($id_empresa)) $template->texto = str_replace("{{id_empresa}}",$id_empresa,$template->texto);

    if (is_array($items)) {
      $s_items = "<table border='1' cellpadding='5' cellspacing='0'><thead><tr><th>Item</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>";
      foreach($items as $item) {
        $s_items.="<tr>";
        $s_items.="<td>$item->nombre<br/>$item->descripcion</td>";
        $s_items.="<td>$item->cantidad</td>";
        $s_items.="<td>$ $item->precio</td>";
        $s_items.="<td>$ $item->total</td>";
        $s_items.="</tr>";
      }
      $s_items.= "</tbody></table>";
      $template->texto = str_replace("{{items}}", $s_items, $template->texto);
    } else if (is_string($items)) {
      $template->texto = str_replace("{{items}}", $items, $template->texto);
    }
    $template->texto = str_replace("'", "\"", $template->texto);
    return $template;
  }

  function finalizar() {

    $numero = $this->get_post("numero",0);
    $redirect = $this->get_post("redirect","/");
    $carrito = $this->get_carrito($numero);
    $carrito->id_tipo_estado = $this->get_post("id_tipo_estado",0);
    $carrito->nueva = 1; // Marcamos el comprobante como NUEVO

    $forma_pago = $this->get_post("forma_pago","");
    $efectivo = $this->get_post("efectivo",0);
    $carrito->efectivo = $efectivo;
    $vuelto = $this->get_post("vuelto",0);
    $carrito->vuelto = $vuelto;
    $tarjeta = $this->get_post("tarjeta",0);
    $carrito->tarjeta = $tarjeta;
    $costo_envio = $this->get_post("costo_envio",0);
    $carrito->costo_envio = $costo_envio;
    $cta_cte = $this->get_post("cta_cte",0);
    $carrito->cta_cte = $cta_cte;

    $observaciones = $this->get_post("observaciones","");
    if (!empty($observaciones)) $carrito->observaciones = $observaciones;    
    $custom_4 = $this->get_post("custom_4","");
    if (!empty($custom_4)) $carrito->custom_4 = $custom_4;    

    $numero_envio = $this->get_post("numero_envio","");
    if (!empty($numero_envio)) {
      $carrito->numero_envio = $numero_envio;
      // Si estamos en TOQUE, al finalizar con numero_envio = pickup tenemos que generar un numero aleatorio
      // para que despues el cliente lo presente en el comercio
      if ($this->id_empresa == 571 || $this->id_empresa == 1275) {
        $generado = str_pad(rand(0,9999), 4, '0', STR_PAD_LEFT);
        $sql = "UPDATE facturas SET link_envio = '$generado' WHERE id_empresa = $this->id_empresa AND id = $carrito->id AND id_punto_venta = $carrito->id_punto_venta ";
        mysqli_query($this->conx,$sql);
      }
    }

    include_once("Articulo_Model.php");
    $articulo_model = new Articulo_Model($this->id_empresa,$this->conx);

    $punto_venta = $articulo_model->get_punto_venta_web();

    // Si esta configurado el PV    
    if ($punto_venta !== FALSE && $carrito->id_tipo_estado >= 0) {

      $id_sucursal = $punto_venta->id_sucursal;
      $carrito->id_sucursal = $id_sucursal;

      // Si el pago no fue con MP, tenemos que reservar el stock de los articulos
      if ($carrito->forma_pago != "mercadopago") {
        
        foreach($carrito->items as $item) {

          // MERCADO ECHEVERRIA DESCUENTA STOCK NO RESERVA
          // TODO: Hacer esto configurable despues
          if ($this->id_empresa == 1284) {
            // Controlamos que el articulo exista, y que este habilitado para utilizar el stock
            $sql = "SELECT * FROM articulos WHERE id = $item->id_articulo AND id_empresa = $this->id_empresa AND usa_stock = 1 LIMIT 0,1 ";
            $q = mysqli_query($this->conx,$sql);
            if (mysqli_num_rows($q)>0) {
              $sql = "UPDATE stock SET stock_actual = stock_actual - $item->cantidad ";
              $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $item->id_articulo AND id_empresa = $this->id_empresa ";
              mysqli_query($this->conx,$sql);
              if ($item->id_variante != 0) {
                $sql = "UPDATE stock_variantes SET stock_actual = stock_actual - $item->cantidad ";
                $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $item->id_articulo AND id_empresa = $this->id_empresa AND id_variante = $item->id_variante ";
                mysqli_query($this->conx,$sql);
                $item->descripcion = ""; // Limpiamos la descripcion para que no se duplique
              }
            }

          } else {
            // Controlamos que el articulo exista, y que este habilitado para utilizar el stock
            $sql = "SELECT * FROM articulos WHERE id = $item->id_articulo AND id_empresa = $this->id_empresa AND usa_stock = 1 LIMIT 0,1 ";
            $q = mysqli_query($this->conx,$sql);
            if (mysqli_num_rows($q)>0) {
              $sql = "UPDATE stock SET reservado = reservado + $item->cantidad ";
              $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $item->id_articulo AND id_empresa = $this->id_empresa ";
              mysqli_query($this->conx,$sql);
              if ($item->id_variante != 0) {
                $sql = "UPDATE stock_variantes SET reservado = reservado + $item->cantidad ";
                $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $item->id_articulo AND id_empresa = $this->id_empresa AND id_variante = $item->id_variante ";
                mysqli_query($this->conx,$sql);
                $item->descripcion = ""; // Limpiamos la descripcion para que no se duplique
              }
            }
            // Con el atributo custom_3 marcamos que fue reservado el stock
            $item->custom_3 = "1";            
          }
        }
      }
    }

    $this->log(print_r($carrito,TRUE),date("Ymd")."_finalizar_carrito.txt");
    $this->guardar($carrito);

    // Si estamos en TOQUE y usaron la billetera
    // solamente si el resto lo pagaron en EFECTIVO, porque si el resto lo pagan en tarjeta se hace en el IPN
    if ($this->es_toque() && $cta_cte > 0 && $efectivo >= 0 && $tarjeta == 0) {

      // Por las dudas controlamos que no exista el movimiento antes
      $sql = "SELECT * FROM toque_billetera_movimientos ";
      $sql.= "WHERE id_empresa = $this->id_empresa ";
      $sql.= "AND id_cliente = '$carrito->id_cliente' ";
      $sql.= "AND id_factura = '$carrito->id' ";
      $sql.= "AND id_punto_venta = '$carrito->id_punto_venta' ";
      $sql.= "AND tipo = 1 ";
      $qb = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($qb) == 0) {
        // Actualizamos el saldo del cliente
        $sql = "UPDATE clientes SET saldo_inicial = saldo_inicial - $cta_cte ";
        $sql.= "WHERE id_empresa = $this->id_empresa ";
        $sql.= "AND id = $carrito->id_cliente ";
        mysqli_query($this->conx,$sql);
        // Lo agregamos como movimiento dentro de su cuenta
        $ahora = date("Y-m-d H:i:s");
        $sql = "INSERT INTO toque_billetera_movimientos (id_concepto,id_empresa,id_cliente,fecha,tipo,monto,observaciones,id_factura,id_punto_venta) VALUES (";
        $sql.= "1425,'$this->id_empresa','$carrito->id_cliente','$ahora',1,'$cta_cte','$carrito->comprobante','$carrito->id','$carrito->id_punto_venta') ";
        mysqli_query($this->conx,$sql);
      }
    }


    // Si utilizamos un codigo promocional
    if (isset($carrito->utiliza_codigo_promocional) && !empty($carrito->utiliza_codigo_promocional)) {
      // Sumamos un uso a ese codigo promocional, por si tiene algun limite de uso
      $sql = "UPDATE reglas_ofertas SET codigo_cantidad_veces = codigo_cantidad_veces + 1 WHERE id_empresa = $this->id_empresa AND id = '$carrito->utiliza_codigo_promocional' ";
      mysqli_query($this->conx,$sql);
    }


    // FINALMENTE:
    // Enviamos un email al cliente que la compra fue exitosa
    if (!empty($carrito->email) && $carrito->id_tipo_estado >= 0) {

      include_once("sistema/application/libraries/Mandrill/Mandrill.php");

      $sql = "SELECT * FROM empresas WHERE id = $this->id_empresa";
      $q_emp = mysqli_query($this->conx,$sql);
      $empresa = mysqli_fetch_object($q_emp);

      $sql = "SELECT bcc_email FROM web_configuracion WHERE id_empresa = $this->id_empresa ";
      $q_conf_web = mysqli_query($this->conx,$sql);
      $web_conf = mysqli_fetch_object($q_conf_web);
      $empresa->bcc_email = $web_conf->bcc_email;

      $bcc_emails = array();
      if (!empty($empty->bcc_emails)) {
        $bcc_emails = explode(",", $empresa->bcc_email);
      }
      //if ($empresa->id != 571) $bcc_emails[] = "basile.matias99@gmail.com";

      $total_con_envio = $carrito->total;
      if (isset($carrito->total) && isset($costo_envio)) {
        $total_con_envio = ((float)$carrito->total + (float)$costo_envio);
      }

      // Si es toque, tomamos los datos del comercio
      $comercio_direccion = "";
      if ($this->es_toque()) {
        include_once("Usuario_Model.php");
        $usuario_model = new Usuario_Model($this->id_empresa,$this->conx);
        $comercio = $usuario_model->get($carrito->id_usuario);
        $comercio_direccion = $comercio->direccion;
      }


      // Cuando se finaliza una compra en ENTRENA y MAS, se envia un codigo de activacion autogenerado
      $codigo_activacion = $this->get_codigo_activacion();
      $sql = "UPDATE facturas SET codigo_activacion = '$codigo_activacion' WHERE id_empresa = $this->id_empresa AND id = $carrito->id AND id_punto_venta = $carrito->id_punto_venta ";
      mysqli_query($this->conx,$sql);        

      include_once("Web_Model.php");
      $web_model = new Web_Model($this->id_empresa,$this->conx);
      $bcc_emails[] = $empresa->email;
      $template = $web_model->get_email("email-compra-ok");
      $template = $this->replace_placeholder($template,array(
        "cliente"=>(isset($carrito->cliente) ? $carrito->cliente : ""),
        "usuario"=>(isset($carrito->usuario) ? $carrito->usuario : ""),
        "comercio_direccion"=>(isset($comercio_direccion) ? $comercio_direccion : ""),
        "numero"=>(isset($carrito->nro_comprobante) ? $carrito->nro_comprobante : ""),
        "total"=>(isset($carrito->total) ? $carrito->total : ""),
        "total_con_envio"=>$total_con_envio,
        "codigo_activacion"=>$codigo_activacion,
        "items"=>(isset($carrito->items) ? $carrito->items : ""),
        "direccion"=>(isset($carrito->direccion) ? $carrito->direccion : ""),
        "email"=>$carrito->email,
        "forma_pago"=>(isset($forma_pago) ? $forma_pago : ""),
        "costo_envio"=>(isset($costo_envio) ? $costo_envio : ""),
        "empresa"=>$empresa->nombre,
        "id_empresa"=>$empresa->id,
        "link_web"=>mklink("/"),
        "link_pedido_toque"=>(($empresa->id == 571) ? "https://www.toque.com.ar/web/finalizar/?id=".$carrito->id : ""),
        "link_pedido_pedienchacabuco"=>(($empresa->id == 1234) ? "https://www.pedienchacabuco.com.ar/web/finalizar/?id=".$carrito->id : ""),
        "id"=>$carrito->id,
        "observaciones"=>(isset($observaciones) ? $observaciones : ""),
      ));

      mandrill_send(array(
        "from_name"=>$empresa->nombre,
        "to"=>$carrito->email,
        "to_name"=>$carrito->cliente,
        "reply_to"=>$empresa->email, // TODO: Podria ser al email del vendedor
        "subject"=>$template->nombre,
        "body"=>$template->texto,
        "bcc"=>$bcc_emails,
      ));

      // Si ademas tenemos que mandar algun email de oferta / descuento
      $email_post_compra = $web_model->get_email_post_compra();
      if ($email_post_compra != FALSE) {

        $cantidad_pedida = 0;
        foreach($carrito->items as $item) $cantidad_pedida += $item->cantidad;

        if (($email_post_compra->email_post_compra_condicion == 0) ||
          ($email_post_compra->email_post_compra_condicion == 1 && $carrito->total >= $email_post_compra->email_post_compra_condicion_valor) ||
          ($email_post_compra->email_post_compra_condicion == 2 && $cantidad_pedida >= $email_post_compra->email_post_compra_condicion_valor)
        ) {

          $template = $this->replace_placeholder($email_post_compra,array(
            "cliente"=>(isset($carrito->cliente) ? $carrito->cliente : ""),
            "usuario"=>(isset($carrito->usuario) ? $carrito->usuario : ""),
            "comercio_direccion"=>(isset($comercio_direccion) ? $comercio_direccion : ""),
            "numero"=>(isset($carrito->nro_comprobante) ? $carrito->nro_comprobante : ""),
            "total"=>(isset($carrito->total) ? $carrito->total : ""),
            "total_con_envio"=>$total_con_envio,
            "items"=>(isset($carrito->items) ? $carrito->items : ""),
            "direccion"=>(isset($carrito->direccion) ? $carrito->direccion : ""),
            "email"=>$carrito->email,
            "forma_pago"=>(isset($forma_pago) ? $forma_pago : ""),
            "costo_envio"=>(isset($costo_envio) ? $costo_envio : ""),
            "empresa"=>$empresa->nombre,
            "id_empresa"=>$empresa->id,
            "link_web"=>mklink("/"),
            "id"=>$carrito->id,
            "observaciones"=>(isset($observaciones) ? $observaciones : ""),
          ));
          mandrill_send(array(
            "from_name"=>$empresa->nombre,
            "to"=>$carrito->email,
            "to_name"=>$carrito->cliente,
            "reply_to"=>$empresa->email, // TODO: Podria ser al email del vendedor
            "subject"=>$email_post_compra->nombre,
            "body"=>$email_post_compra->texto,
            "bcc"=>$bcc_emails,
          ));
        }
      }

      
      // Guardamos el evento
      $id_origen = 18; // COMPRA WEB
      // En id_relacion guardamos el pedido realizado
      $texto_consulta = "Total: $ ".number_format($carrito->total,2);
      $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_relacion,id_contacto,texto) VALUES(";
      $sql.= "'$this->id_empresa',NOW(),'Venta','$id_origen','$carrito->id','$carrito->id_cliente','$texto_consulta')";
      mysqli_query($this->conx,$sql);

      // Actualizamos el contacto con la ultima fecha de operacion
      // Tipo = 0, indica que es un cliente
      $sql = "UPDATE clientes SET fecha_ult_operacion = NOW(), no_leido = 1, tipo = 0 ";
      $sql.= "WHERE id = $carrito->id_cliente AND id_empresa = $this->id_empresa ";
      mysqli_query($this->conx,$sql);

    }

    // Vaciamos el carrito al finalizar
    // TODO: Hacer esto administrable despues, posiblemente tenga que funcionar asi si se manda por AJAX
    if ($this->id_empresa == 1284) {
      //$this->borrar_carrito();
      //$carrito = $this->crear_pedido();
      //$this->guardar($carrito);
    }

    if ($redirect == "JSON") {
      echo json_encode(array("error"=>0));
    } else header("Location: $redirect");
  }




  function finalizar_clienapp() {

    $carrito = $this->get_carrito();
    $carrito->id_cliente = $this->get_post("id_cliente",0);
    $carrito->id_tipo_estado = $this->get_post("id_tipo_estado",0);
    $carrito->direccion = $this->get_post("direccion","");
    $carrito->email = $this->get_post("email","");
    $carrito->telefono = $this->get_post("telefono","");
    $carrito->cliente = $this->get_post("cliente","");
    $carrito->nueva = 1; // Marcamos el comprobante como NUEVO
    $carrito->finalizar_clienapp = 1; // Fuerza el guardado
    $observaciones = htmlentities($this->get_post("observaciones",""));
    $carrito->observaciones = $observaciones;

    include_once("Articulo_Model.php");
    $articulo_model = new Articulo_Model($this->id_empresa,$this->conx);

    include_once("Usuario_Model.php");
    $usuario_model = new Usuario_Model($this->id_empresa,$this->conx);

    $punto_venta = $articulo_model->get_punto_venta_web();
    $items_string = "";

    // Si esta configurado el PV    
    if ($punto_venta !== FALSE && $carrito->id_tipo_estado >= 0) {

      $id_sucursal = $punto_venta->id_sucursal;
      $carrito->id_sucursal = $id_sucursal;

      foreach($carrito->items as $item) {

        $items_string .= "- ".$item->cantidad." x ";
        $items_string .= htmlentities($item->nombre);
        if (isset($item->codigo) && !empty($item->codigo)) $items_string.= " (Cod: ".$item->codigo.") ";
        $items_string .= " | Precio: $".round($item->precio,0)." | Total: $".round($item->total,0)."<br/>";

        // Controlamos que el articulo exista, y que este habilitado para utilizar el stock
        $sql = "SELECT * FROM articulos WHERE id = $item->id_articulo AND id_empresa = $this->id_empresa AND usa_stock = 1 LIMIT 0,1 ";
        $q = mysqli_query($this->conx,$sql);
        if (mysqli_num_rows($q)>0) {
          $sql = "UPDATE stock SET stock_actual = stock_actual - $item->cantidad ";
          $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $item->id_articulo AND id_empresa = $this->id_empresa ";
          mysqli_query($this->conx,$sql);
          if ($item->id_variante != 0) {
            $sql = "UPDATE stock_variantes SET stock_actual = stock_actual - $item->cantidad ";
            $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $item->id_articulo AND id_empresa = $this->id_empresa AND id_variante = $item->id_variante ";
            mysqli_query($this->conx,$sql);
            $item->descripcion = ""; // Limpiamos la descripcion para que no se duplique
          }
        }
      }
    }

    $this->guardar($carrito);

    // FINALMENTE:
    // Enviamos un email al cliente que la compra fue exitosa
    if (!empty($carrito->email) && $carrito->id_tipo_estado >= 0) {

      include_once("sistema/application/libraries/Mandrill/Mandrill.php");

      $sql = "SELECT * FROM empresas WHERE id = $this->id_empresa";
      $q_emp = mysqli_query($this->conx,$sql);
      $empresa = mysqli_fetch_object($q_emp);

      $sql = "SELECT bcc_email FROM web_configuracion WHERE id_empresa = $this->id_empresa ";
      $q_conf_web = mysqli_query($this->conx,$sql);
      $web_conf = mysqli_fetch_object($q_conf_web);
      $empresa->bcc_email = $web_conf->bcc_email;

      $bcc_emails = array();
      if (!empty($empresa->bcc_emails)) {
        $bcc_emails = explode(",", $empresa->bcc_email);
      }
      if ($empresa->id != 571) $bcc_emails[] = "basile.matias99@gmail.com";

      $total_con_envio = $carrito->total;
      if (isset($carrito->total) && isset($costo_envio)) {
        $total_con_envio = ((float)$carrito->total + (float)$costo_envio);
      }

      include_once("Web_Model.php");
      $web_model = new Web_Model($this->id_empresa,$this->conx);
      $bcc_emails[] = $empresa->email;

      // EMAIL PARA EL COMERCIO
      $template = $web_model->get_email("email-compra-ok");
      $template = $this->replace_placeholder($template,array(
        "cliente"=>htmlentities((isset($carrito->cliente) ? $carrito->cliente : "")),
        "usuario"=>(isset($carrito->usuario) ? $carrito->usuario : ""),
        "numero"=>(isset($carrito->nro_comprobante) ? $carrito->nro_comprobante : ""),
        "total"=>(isset($carrito->total) ? $carrito->total : ""),
        "total_con_envio"=>round($total_con_envio,2),
        "direccion"=>htmlentities((isset($carrito->direccion) ? $carrito->direccion : "")),
        "email"=>$carrito->email,
        "celular"=>$carrito->telefono,
        "forma_pago"=>(isset($forma_pago) ? $forma_pago : ""),
        "costo_envio"=>(isset($costo_envio) ? $costo_envio : ""),
        "empresa"=>htmlentities($empresa->nombre),
        "id_empresa"=>$empresa->id,
        "link_ver_pedido"=>mklink("sistema/facturas/function/ver_pdf/$carrito->id/$carrito->id_punto_venta/$empresa->id/"),
        "id"=>$carrito->id,
        "items"=>$items_string,
        "observaciones"=>$observaciones,
      ));

      $comercio = $usuario_model->get($carrito->id_usuario,array(
        "id_empresa"=>$empresa->id,
      ));
      mandrill_send(array(
        "from_name"=>$carrito->cliente,
        "to"=>$comercio->email,
        "to_name"=>$empresa->nombre,
        "reply_to"=>$carrito->email,
        "subject"=>$template->nombre,
        "body"=>$template->texto,
        "bcc"=>$bcc_emails,
      ));

      // EMAIL PARA EL CLIENTE
      $template = $web_model->get_email("recibo-cliente");
      $template = $this->replace_placeholder($template,array(
        "cliente"=>htmlentities((isset($carrito->cliente) ? $carrito->cliente : "")),
        "comercio"=>htmlentities((isset($comercio->nombre) ? $comercio->nombre : "")),
        "usuario"=>htmlentities((isset($carrito->usuario) ? $carrito->usuario : "")),
        "numero"=>(isset($carrito->nro_comprobante) ? $carrito->nro_comprobante : ""),
        "total"=>(isset($carrito->total) ? $carrito->total : ""),
        "total_con_envio"=>round($total_con_envio,2),
        "direccion"=>htmlentities((isset($carrito->direccion) ? $carrito->direccion : "")),
        "email"=>$carrito->email,
        "celular"=>$carrito->telefono,
        "forma_pago"=>(isset($forma_pago) ? $forma_pago : ""),
        "costo_envio"=>(isset($costo_envio) ? $costo_envio : ""),
        "empresa"=>htmlentities($empresa->nombre),
        "id_empresa"=>$empresa->id,
        "link_ver_pedido"=>mklink("sistema/facturas/function/ver_pdf/$carrito->id/$carrito->id_punto_venta/$empresa->id/"),
        "id"=>$carrito->id,
        "items"=>$items_string,
        "observaciones"=>$observaciones,
      ));

      mandrill_send(array(
        "from_name"=>$empresa->nombre,
        "to"=>$carrito->email,
        "to_name"=>$carrito->cliente,
        "reply_to"=>$empresa->email, // TODO: Podria ser al email del vendedor
        "subject"=>$template->nombre,
        "body"=>$template->texto,
      ));

      // Guardamos el evento
      $id_origen = 18; // COMPRA WEB
      // En id_relacion guardamos el pedido realizado
      $texto_consulta = "Total: $ ".number_format($carrito->total,2);
      $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_relacion,id_contacto,texto) VALUES(";
      $sql.= "'$this->id_empresa',NOW(),'Venta','$id_origen','$carrito->id','$carrito->id_cliente','$texto_consulta')";
      mysqli_query($this->conx,$sql);

      // Actualizamos el contacto con la ultima fecha de operacion
      // Tipo = 0, indica que es un cliente
      $sql = "UPDATE clientes SET fecha_ult_operacion = NOW(), no_leido = 1, tipo = 0 ";
      $sql.= "WHERE id = $carrito->id_cliente AND id_empresa = $this->id_empresa ";
      mysqli_query($this->conx,$sql);

    }
    $this->borrar_carrito();
    echo json_encode(array(
      "error"=>0,
      "id"=>$carrito->id,
      "id_punto_venta"=>$carrito->id_punto_venta,
    ));
  }



  // REVISAR:
  function calcular_costo_envio_mercadoenvio() {

    $redirect = $this->get_post("redirect","");
    $codigo_postal = $this->get_post("codigo_postal","");
    $numero = $this->get_post("numero",0);
    $ancho = $this->get_post("ancho",0);
    $alto = $this->get_post("alto",0);
    $profundidad = $this->get_post("profundidad",0);
    $precio = $this->get_post("precio",0);
    $carrito = $this->get_carrito($numero);
    $coordinar_envio = $this->get_post("coordinar_envio",-1);
    $id_empresa = $this->get_post("id_empresa",0);

    $carrito->codigo_postal = $codigo_postal;
    if ($carrito->forma_envio == "retiro_sucursal") {
      $carrito->costo_envio = 0;
      $costo = 0;
    } else {

      // Si enviamos la variable PESO, es porque estamos calculando
      // el costo de envio de un solo producto (en el detalle)
      // Sino, tomamos el peso total del carrito
      if (isset($_POST["peso"])) {

        // Peso del producto
        $peso = filter_var($_POST["peso"],FILTER_SANITIZE_STRING);
        $costo = $this->do_calcular_costo_envio_mercadoenvio(array(
          "numero"=>$numero,
          "peso"=>$peso,
          "ancho"=>$ancho,
          "alto"=>$alto,
          "profundidad"=>$profundidad,
          "precio"=>$precio,
          "codigo_postal"=>$codigo_postal,
          "id_empresa"=>$id_empresa,
          "coordinar_envio"=>$coordinar_envio,
          "pedido"=>$carrito,
        ));

        // Redireccionamos con el costo de envio para ese caso
        if ($redirect != "JSON") {
          $redirect = $this->replace_params($redirect,array(
            "costo_envio"=>$costo,
          ));
        }

      } else {

        $costo = $this->do_calcular_costo_envio_mercadoenvio(array(
          "numero"=>$numero,
          "peso"=>$carrito->peso_total_calculado, // Peso del carrito completo
          "ancho"=>(isset($carrito->ancho_total) ? $carrito->ancho_total : 0),
          "alto"=>(isset($carrito->alto_total) ? $carrito->alto_total : 0),
          "profundidad"=>(isset($carrito->profundidad_total) ? $carrito->profundidad_total : 0),
          "precio"=>($carrito->total - $carrito->costo_envio), // SUBTOTAL = TOTAL - COSTO ENVIO
          "codigo_postal"=>$codigo_postal,
          "coordinar_envio"=>(isset($carrito->coordinar_envio) ? $carrito->coordinar_envio : -1),
          "pedido"=>$carrito,
        ));
        $carrito->costo_envio = $costo;
      }
    }

    $this->guardar($carrito);

    if ($redirect == "JSON") {
      echo json_encode(array(
        "costo_envio"=>$costo,
      ));
    } else header("Location: $redirect");
  }


  // CALCULAMOS EL COSTO DE ENVIO POR REPARTO PROPIO
  function do_calcular_reparto($conf = array()) {
    $codigo_postal = isset($conf["codigo_postal"]) ? $conf["codigo_postal"] : "";
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : $this->id_empresa;
    $pedido = isset($conf["pedido"]) ? $conf["pedido"] : null;
    $total = isset($conf["total"]) ? $conf["total"] : 0; // Se usa para calcular un item en particular

    // Si estamos mandando un pedido, tomamos el total del pedido
    if (!is_null($pedido)) $total = $pedido->total;

    // Controlamos que todos los items del pedido esten marcados como ENVIO GRATIS
    if (!is_null($pedido) && isset($pedido->items)) {
      $envio_gratis = 0;
      foreach($pedido->items as $item) {
        if ($item->envio_gratis == 1) $envio_gratis++;
      }
      // Si todos estan marcados como envio gratis
      if (sizeof($pedido->items) > 0 && $envio_gratis == sizeof($pedido->items)) {
        return 0;
      }
    }    

    $conf_envio = $this->get_metodo_envio(array(
      "id_empresa"=>$id_empresa,
    ));
    // Si tiene valores, primero hay que controlarlas
    if (sizeof($conf_envio->valores)>0) {
      foreach($conf_envio->valores as $excep) {
        // Si coincide el codigo postal
        if ($excep->codigo_postal == $codigo_postal) {
          // Si el total del carrito es 
          if ($total > $excep->monto_desde) {
            return $excep->costo_envio;
          }
        }
      }
    }

    // Sino por defecto tomamos el costo de envio fijo
    return $conf_envio->costo_envio_fijo;
  }


  // CALCULAMOS EL COSTO DE ENVIO POR MERCADOPAGO
  function do_calcular_costo_envio_mercadoenvio($conf = array()) {

    $peso = isset($conf["peso"]) ? $conf["peso"] : 1;
    $numero = isset($conf["numero"]) ? $conf["numero"] : 0;
    $codigo_postal = isset($conf["codigo_postal"]) ? $conf["codigo_postal"] : "";
    $codigo_postal = trim($codigo_postal);
    $alto = isset($conf["alto"]) ? $conf["alto"] : 0;
    $ancho = isset($conf["ancho"]) ? $conf["ancho"] : 0;
    $profundidad = isset($conf["profundidad"]) ? $conf["profundidad"] : 0;
    $precio = isset($conf["precio"]) ? $conf["precio"] : 0;
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : $this->id_empresa;
    $coordinar_envio = isset($conf["coordinar_envio"]) ? $conf["coordinar_envio"] : -1;
    $pedido = isset($conf["pedido"]) ? $conf["pedido"] : null;

    $conf_envio = $this->get_metodo_envio(array(
      "id_empresa"=>$id_empresa,
    ));

    // Controlamos que todos los items del pedido esten marcados como ENVIO GRATIS
    if (!is_null($pedido)) unset($pedido->excepcion_envio); // Primero la blanqueamos
    if (!is_null($pedido) && isset($pedido->items)) {
      $envio_gratis = 0;
      foreach($pedido->items as $item) {
        if ($item->envio_gratis == 1) $envio_gratis++;
      }
      // Si todos estan marcados como envio gratis
      if (sizeof($pedido->items) > 0 && $envio_gratis == sizeof($pedido->items)) {
        if (!is_null($pedido)) $pedido->excepcion_envio = 1;
        return 0;
      }
    }

    // Si tiene excepciones, primero hay que controlarlas
    if (!is_null($pedido)) unset($pedido->excepcion_envio); // Primero la blanqueamos
    if (sizeof($conf_envio->excepciones)>0) {
      foreach($conf_envio->excepciones as $excep) {
        // Si coincide el codigo postal
        if ($excep->codigo_postal == $codigo_postal) {
          // Si el total del carrito es 
          if ($pedido->total > $excep->monto_desde) {
            if (!is_null($pedido)) $pedido->excepcion_envio = 1;
            return $excep->costo_envio;
          }
        }
      }
    }

    // Si el uso de excepciones es 1, entonces utilizamos las excepciones solamente si hay productos marcados con coordinar envio
    // TODO: REVISAR TODO ESTO
    /*
    if ($conf_envio->uso_excepciones == 1 && $coordinar_envio == 1) {
      if ($excepcion_envio !== FALSE) {
        if (!is_null($pedido)) $pedido->excepcion_envio = 1;
        return $excepcion_envio->costo_envio;
      } else {
        // Hay que coordinar el envio SI o SI
        return -1;
      }
    // Si hay una excepcion que aplica, devolvemos ese costo de envio (para cualquier producto)
    } else if ($conf_envio->uso_excepciones == 0) {
      if ($excepcion_envio !== FALSE) {
        if (!is_null($pedido)) $pedido->excepcion_envio = 1;
        return $excepcion_envio->costo_envio;
      }
    }
    */

    $costo_envio = -1; // SE DEBE COORDINAR

    if ($alto == 0 || $ancho == 0 || $profundidad == 0 || $peso == 0) return $costo_envio;
    
    try {
      $alto = intval($alto * 100);
      $ancho = intval($ancho * 100);
      $profundidad = intval($profundidad * 100);
      $peso = intval($peso * 1000);

      $volumen = $alto * $ancho * $profundidad;
      $dimensiones = $this->calcular_dimensiones(array("volumen"=>$volumen));

      $mp = $this->get_mercadopago($numero);
      $params = array(
        "dimensions" => $dimensiones.",".$peso,
        "zip_code" => $codigo_postal,
        "item_price"=> $precio,
      );
      $response = $mp->get("/shipping_options", $params);
      file_put_contents("/home/ubuntu/data/log_mercadoenvio_error.txt", "Empresa: $this->id_empresa. Fecha: ".date("Y-m-d H:i:s")."\n".print_r($response,TRUE), FILE_APPEND);
      if ($response["status"] == 200) {
        foreach($response["response"]["options"] as $option) {
          $costo_envio = $option["cost"];
          break;
        }
      }      
      return $costo_envio;
    } catch(Exception $e) {
      file_put_contents("/home/ubuntu/data/log_mercadoenvio_error.txt", print_r($e,TRUE), FILE_APPEND);
      return $costo_envio;
    }
  }

  function get_codigo_activacion() {
    $usados = array();
    $sql = "SELECT DISTINCT codigo_activacion FROM facturas WHERE codigo_activacion != '' ";
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $usados[] = $r->codigo_activacion;
    }
    $aleatorio = rand(0,9999999);
    while(in_array($aleatorio, $usados)) $aleatorio = rand(0,9999999);
    return $aleatorio;
  }

  function stripe_payment_intent() {
    $carrito = $this->get_carrito();
    require 'vendor/autoload.php';
    $stripe_config = $this->get_stripe();
    if ($stripe_config === FALSE) {
      echo json_encode(array("error"=>1));
      exit();
    }
    \Stripe\Stripe::setApiKey($stripe_config->stripe_secret);
    header('Content-Type: application/json');
    try {
      // retrieve JSON from POST body
      $json_str = file_get_contents('php://input');
      $json_obj = json_decode($json_str);
      $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => intval($carrito->total * 100),
        'currency' => 'eur',
      ]);
      $output = [
        'clientSecret' => $paymentIntent->client_secret,
      ];
      echo json_encode($output);
    } catch (Error $e) {
      http_response_code(500);
      echo json_encode(['error' => $e->getMessage()]);
    }    
  }
  
  function calcular_dimensiones($conf = array()) {
    $volumen = isset($conf["volumen"]) ? ((float)$conf["volumen"]) : 0;
    $ancho = isset($conf["ancho"]) ? ((float)$conf["ancho"]) : 0;
    $alto = isset($conf["alto"]) ? ((float)$conf["alto"]) : 0;
    $profundidad = isset($conf["profundidad"]) ? ((float)$conf["profundidad"]) : 0;
    if ($volumen == 0 && ($ancho > 0 || $alto > 0 || $profundidad > 0)) {
      $volumen = $ancho * $alto * $profundidad;
    }
    if ($volumen <= 1000) {
      $dimensiones = "10x10x10";
    } else if ($volumen <= 8000) {
      $dimensiones = "20x20x20";
    } else if ($volumen <= 27000) {
      $dimensiones = "30x30x30";
    } else if ($volumen <= 64000) {
      $dimensiones = "40x40x40";
    } else if ($volumen <= 125000) {
      $dimensiones = "50x50x50";
    } else if ($volumen <= 216000) {
      $dimensiones = "60x60x60";
    } else if ($volumen <= 343000) {
      $dimensiones = "70x70x70";
    } else {
      $dimensiones = "70x70x70";
    }
    return $dimensiones;
  }

  function vaciar_carrito() {
    $numero = $this->get_post("numero",0); // Identifica al carrito (puede haber mas de uno por cada web)
    $ajax = $this->get_post("ajax",1);
    $redirect = $this->get_post("redirect","/");
    $carrito = $this->get_carrito($numero);
    $carrito->items = array();
    $carrito->id_usuario = 0;
    $this->guardar($carrito);
    if ($ajax == 1) echo json_encode(array("error"=>0));
    else header("Location: $redirect");
  }  

  function borrar_carrito($numero = 0) {
    // Borramos la Cookie
    setcookie("cart_".$numero, '', time() - 3600, "/");
  }

  function eliminar_carrito() {
    $numero = $this->get_post("numero",0); // Identifica al carrito (puede haber mas de uno por cada web)
    $ajax = $this->get_post("ajax",1);
    $carrito = $this->get_carrito($numero);

    // Solamente borramos si la factura esta en estado "pendiente"
    $valor_pendiente = ($this->es_toque()) ? -1 : 0; // Pendiente
    $sql = "SELECT id_tipo_estado FROM facturas WHERE id = $carrito->id AND id_empresa = $this->id_empresa AND id_punto_venta = $carrito->id_punto_venta";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $r = mysqli_fetch_object($q);
      file_put_contents("log_eliminar_carrito.txt", print_r($r,TRUE), FILE_APPEND);
      // Borramos en la base de datos
      if ($r->id_tipo_estado == $valor_pendiente) {
        mysqli_query($this->conx,"UPDATE facturas SET anulada = 1 WHERE id = $carrito->id AND id_empresa = $this->id_empresa AND id_punto_venta = $carrito->id_punto_venta");
        /*
        mysqli_query($this->conx,"DELETE FROM facturas WHERE id = $carrito->id AND id_empresa = $this->id_empresa AND id_punto_venta = $carrito->id_punto_venta");
        mysqli_query($this->conx,"DELETE FROM facturas_items WHERE id_factura = $carrito->id AND id_empresa = $this->id_empresa AND id_punto_venta = $carrito->id_punto_venta");
        mysqli_query($this->conx,"DELETE FROM facturas_iva WHERE id_factura = $carrito->id AND id_empresa = $this->id_empresa AND id_punto_venta = $carrito->id_punto_venta");
        mysqli_query($this->conx,"DELETE FROM repartidores_pedidos WHERE id_factura = $carrito->id AND id_empresa = $this->id_empresa AND id_punto_venta = $carrito->id_punto_venta");      
        */
      }
    }

    // Borramos la Cookie
    setcookie("cart_".$numero, '', time() - 3600, "/");

    //$this->crear_pedido($numero);
    //$this->guardar($carrito);
    if ($ajax == 1) echo json_encode(array("error"=>0));
    else header("Location: $redirect");
  }  

  function limpiar() {
    $numero = $this->get_post("numero",0);
    $redirect = $this->get_post("redirect","");
    $carrito = $this->get_carrito($numero);
    if ($carrito->id > 0) {
      // El pedido ha sido cancelado: No lo borramos, cambiamos de estado para que figure
      // en el listado de Carritos Abandonados
      $sql = "UPDATE id_tipo_estado = 7 FROM facturas WHERE id = $carrito->id AND id_empresa = $this->id_empresa ";
      mysqli_query($this->conx,$sql);
    }
    unset($_COOKIE["cart_".$numero]);
    header("Location: $redirect");
  }


  // Esta funcion se encarga de controlar que 
  function controlar_envio_productos_fragiles($c) {

    // Buscamos si en el pedido hay un producto fragil
    $encontro_fragil = FALSE;
    foreach($c->items as $item) {
      $sql = "SELECT * FROM articulos ";
      $sql.= "WHERE id_empresa = $this->id_empresa ";
      $sql.= "AND id = $item->id_articulo ";
      $q_art = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q_art)<=0) continue;
      $art = mysqli_fetch_object($q_art);
      if ($art->fragil == 1) {
        $encontro_fragil = TRUE;
        break;
      }
    }

    // Si no encontramos productos fragiles, todo OK
    if (!$encontro_fragil) return TRUE;

    // Obtenemos la lista de codigos postales habilitados para los productos fragiles
    $salida = FALSE;
    $metodo_envio = $this->get_metodo_envio();
    if (!empty($metodo_envio->excepciones_fragiles)) {
      $ar = explode(",", $metodo_envio->excepciones_fragiles);
      foreach($ar as $ex) {
        $ex = trim($ex);
        if ($c->codigo_postal == $ex) {
          $salida = TRUE; // Si encontramos el CP, esta bien
          break;
        }
      }
    }
    return $salida;
  }

}
?>