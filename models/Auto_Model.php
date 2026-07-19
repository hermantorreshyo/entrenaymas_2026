<?php
class Auto_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $total = 0;

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : mb_convert_encoding($r, 'UTF-8', 'ISO-8859-1'));
  }

  function get_variables($config = array()) {

    global $params;
    global $get_params;
    $redirect = isset($config["redirect"]) ? $config["redirect"] : 1;
    $filter = isset($get_params["filter"]) ? $get_params["filter"] : "";
    $orden = isset($get_params["orden"]) ? $get_params["orden"] : "";
    $id_localidad = isset($get_params["id_localidad"]) ? $get_params["id_localidad"] : 0;
    $id_marca = isset($get_params["id_marca"]) ? $get_params["id_marca"] : 0;
    $tipo = isset($get_params["tipo"]) ? $get_params["tipo"] : 0;
    $modelo = isset($get_params["modelo"]) ? $get_params["modelo"] : "";
    $anio = isset($get_params["anio"]) ? $get_params["anio"] : "";
    $minimo = isset($get_params["minimo"]) ? $get_params["minimo"] : 0;
    $maximo = isset($get_params["maximo"]) ? $get_params["maximo"] : 0;
    $offset = isset($get_params["offset"]) ? $get_params["offset"] : (isset($config["offset"]) ? $config["offset"] : 12);
    $page = isset($get_params["page"]) ? $get_params["page"] : (isset($config["page"]) ? $config["page"] : 0);
    if (isset($params[1]) && is_numeric($params[1])) $page = $params[1];
    $vc_link = "clasificados/";
    $no_analizar_url = isset($config["no_analizar_url"]) ? $config["no_analizar_url"] : 0;

    $cc = array(
      "filter"=>$filter,
      "offset"=>$offset,
      "id_localidad"=>$id_localidad,
      "id_marca"=>$id_marca,
      "modelo"=>$modelo,
      "anio"=>$anio,
      "minimo"=>$minimo,
      "maximo"=>$maximo,
      "id_tipo"=>$tipo,
      "limit"=>($page * $offset),
    );
    if ($orden == 'nuevos') { 
      $cc["order_by"] = "A.id DESC";
    } else if ($orden == 'viejos') {
      $cc["order_by"] = "A.id ASC";
    } else {
      $cc["order_by"] = "A.id DESC"; 
    }
    $listado = $this->get_list($cc);

    if ($redirect == 1 && sizeof($listado)==1) {
      $e=$listado[0];
      header("location:". mklink($e->link));
    }

    $total = $this->get_total_results();
    $total_paginas = ceil ($total / $offset);

    $s_params = (!empty($get_params)) ? "?".http_build_query($get_params) : "";

    $vc_precio_maximo = $this->get_precio_maximo();
    if (empty($maximo)) $maximo = $vc_precio_maximo;

    return array(
      "vc_total_resultados"=>$total,
      "vc_total_paginas"=>$total_paginas,
      "vc_listado"=>$listado,
      "vc_page"=>$page,
      "vc_orden"=>$orden,
      "vc_offset"=>$offset,
      "vc_filter"=>$filter,
      "vc_params"=>$s_params,
      "vc_anio"=>$anio,
      "vc_modelo"=>$modelo,
      "vc_id_marca"=>$id_marca,
      "vc_id_localidad"=>$id_localidad,
      "vc_tipo"=>$tipo,
      "vc_link"=>$vc_link,
      "vc_minimo"=>$minimo,
      "vc_maximo"=>$maximo,
      "vc_precio_maximo"=>$vc_precio_maximo,
    );
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

  // Obtenemos los datos del entrada
  function get($id,$config = array()) {

    // Estos parametros se pueden deshabilitar para ganar velocidad, ya que no tiene sentido a veces cargarlos
    $buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;

    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $id_tipo = isset($config["id_tipo"]) ? $config["id_tipo"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 6;
    $id_marca = isset($config["id_marca"]) ? $config["id_marca"] : 0;
    $modelo = isset($config["modelo"]) ? $config["modelo"] : "";
    $anio = isset($config["anio"]) ? $config["anio"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;

    // Utilizado en proyecto TDFCars:
    // Controla si la fecha_vencimiento del cliente es menor a la fecha de hoy,
    // para saber si los clasificados siguen activos
    $fecha_cliente = isset($config["fecha_cliente"]) ? $config["fecha_cliente"] : 0;

    $id = (int)$id;
    $sql = "SELECT A.*, DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
    $sql.= " IF(CLI.nombre IS NULL,'',CLI.nombre) AS cliente, ";
    $sql.= " IF(MV.nombre IS NULL,'',MV.nombre) AS marca, ";
    $sql.= " IF(MV.link IS NULL,'',MV.link) AS marca_link, ";
    $sql.= " IF(CLI.email IS NULL,'',CLI.email) AS cliente_email, ";
    $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS tipo ";
    $sql.= "FROM veh_autos A ";
    $sql.= "LEFT JOIN marcas_vehiculos MV ON (A.id_marca = MV.id AND A.id_empresa = MV.id_empresa) ";
    $sql.= "LEFT JOIN veh_tipos C ON (A.id_tipo = C.id) ";
    $sql.= "LEFT JOIN clientes CLI ON (A.id_cliente = CLI.id AND A.id_empresa = CLI.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($id_tipo)) $sql.= "AND A.id_tipo = $id_tipo ";
    if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
    if (!empty($modelo)) $sql.= "AND A.modelo = '$modelo' ";
    if (!empty($anio)) $sql.= "AND A.anio = '$anio' ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($not_id > 0) $sql.= "AND A.id != $not_id ";
    if ($fecha_cliente == 1) $sql.= "AND CLI.fecha_vencimiento > NOW() ";
    $sql.= "ORDER BY A.fecha DESC ";
    $sql.= "LIMIT $limit,$offset ";

    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q) == 0) return array();
    $entrada = mysqli_fetch_object($q);
    $entrada = $this->encoding($entrada);

    $entrada->images = array();
    if ($buscar_imagenes == 1) {
      // Obtenemos las imagenes de ese entrada
      $sql = "SELECT AI.* FROM veh_autos_images AI WHERE AI.id_auto = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
        $entrada->images[] = $r->path;
      }
    }

    // Obtenemos los autos relacionados
    $entrada->relacionados = $this->get_list(array(
     "not_id"=>$entrada->id,
     "offset"=>3,
     ));

    // Link de la imagen
    $entrada->path = ((strpos($entrada->path,"http://")===FALSE)) ? "/sistema/".$entrada->path : $entrada->path;

    return $entrada;
  }


  function get_list($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 6;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
    $filter = isset($config["filter"]) ? $config["filter"] : 0;
    $id_tipo = isset($config["id_tipo"]) ? $config["id_tipo"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : "";
    $not_tipo = isset($config["not_tipo"]) ? $config["not_tipo"] : "";
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
    $fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
    $activo_desde = isset($config["activo_desde"]) ? $config["activo_desde"] : "";
    $activo_hasta = isset($config["activo_hasta"]) ? $config["activo_hasta"] : "";
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $localidad = isset($config["localidad"]) ? $config["localidad"] : "";
    $id_localidad = isset($config["id_localidad"]) ? $config["id_localidad"] : 0;
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "A.fecha DESC";
    $marca = isset($config["marca"]) ? $config["marca"] : "";
    $id_marca = isset($config["id_marca"]) ? $config["id_marca"] : 0;
    $modelo = isset($config["modelo"]) ? $config["modelo"] : "";
    $anio = isset($config["anio"]) ? $config["anio"] : "";
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $solo_contar = isset($config["solo_contar"]) ? $config["solo_contar"] : 0;
    $maximo = isset($config["maximo"]) ? $config["maximo"] : 0;
    $minimo = isset($config["minimo"]) ? $config["minimo"] : 0;

    // Utilizado en proyecto TDFCars:
    // Controla si la fecha_vencimiento del cliente es menor a la fecha de hoy,
    // para saber si los clasificados siguen activos
    $fecha_cliente = isset($config["fecha_cliente"]) ? $config["fecha_cliente"] : 0;

    $valido_hasta = isset($config["valido_hasta"]) ? $config["valido_hasta"] : (($this->id_empresa == 263) ? date("Y-m-d") : "");

    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
    $sql.= " IF(CLI.nombre IS NULL,'',CLI.nombre) AS cliente, ";
    $sql.= " IF(MV.nombre IS NULL,'',MV.nombre) AS marca, ";
    $sql.= " IF(MV.link IS NULL,'',MV.link) AS marca_link, ";
    $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS tipo ";
    $sql.= "FROM veh_autos A ";
    $sql.= "LEFT JOIN marcas_vehiculos MV ON (A.id_marca = MV.id AND A.id_empresa = MV.id_empresa) ";
    $sql.= "LEFT JOIN veh_tipos C ON (A.id_tipo = C.id) ";
    $sql.= "LEFT JOIN clientes CLI ON (A.id_cliente = CLI.id AND A.id_empresa = CLI.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if ($not_id > 0) $sql.= "AND A.id != $not_id ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
    if (!empty($id_tipo)) {
      if ($id_tipo == 99) $sql.= "AND A.id_tipo NOT IN (1,2,14) ";
      else $sql.= "AND A.id_tipo = $id_tipo ";
    }
    if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
    if (!empty($activo_desde)) $sql.= "AND A.fecha >= '$activo_desde' ";
    if (!empty($activo_hasta)) $sql.= "AND A.fecha <= '$activo_hasta' ";
    if (!empty($tipo)) $sql.= "AND C.link = '$tipo' ";
    if (!empty($not_tipo)) $sql.= "AND C.link != '$not_tipo' ";
    if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
    if (!empty($marca)) $sql.= "AND MV.link = '$marca' ";
    if (!empty($filter)) $sql.= "AND (A.titulo LIKE '%$filter%' OR A.text LIKE '%$filter%') ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
    if (!empty($localidad)) $sql.= "AND L.link = '$localidad' ";
    if (!empty($id_localidad)) $sql.= "AND L.id = '$id_localidad' ";
    if (!empty($modelo)) $sql.= "AND A.modelo = '$modelo' ";
    if (!empty($anio)) $sql.= "AND A.anio = '$anio' ";
    if ($fecha_cliente == 1) $sql.= "AND CLI.fecha_vencimiento > NOW() ";
    if ($maximo > 0) {

      // Cotizacion del dolar
      $sql_cot = 'SELECT * FROM cotizaciones WHERE moneda = "U$D" ORDER BY fecha DESC LIMIT 0,1 ';
      $q_cot = mysqli_query($this->conx,$sql_cot);
      $r_cot = mysqli_fetch_object($q_cot);
      $cotizacion = $r_cot->valor;

      $sql.= 'AND IF (A.moneda = "U$S",A.precio_final * '.$cotizacion.' >= '.$minimo.', A.precio_final >= '.$minimo.') ';
      $sql.= 'AND IF (A.moneda = "U$S",A.precio_final * '.$cotizacion.' <= '.$maximo.', A.precio_final <= '.$maximo.') ';
      $sql.= "AND A.precio_final >= $minimo AND A.precio_final <= $maximo ";
    }
    if (!empty($valido_hasta)) $sql.= "AND A.valido_hasta >= '$valido_hasta' ";

    $sql.= "ORDER BY $order_by ";
    $sql.= "LIMIT $limit,$offset ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    if ($q === FALSE) {
      error_mail($sql);
      return $salida;
    }

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    if ($solo_contar == 1) {
      return $this->total;
    }

    while(($r=mysqli_fetch_object($q))!==NULL) {

      // Obtenemos las imagenes de ese entrada
      $sql = "SELECT AI.* FROM veh_autos_images AI WHERE AI.id_auto = $r->id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC LIMIT 0,1";
      $qq = mysqli_query($this->conx,$sql);
      $rr = mysqli_fetch_object($qq);

      $r = $this->encoding($r);
      $r->path = ($rr != NULL) ? (((strpos($rr->path,"http://")===FALSE)) ? "/sistema/".$rr->path : $rr->path) : "";
      $salida[] = $r;
    }
    return $salida;

  }



  function get_precio_maximo($config = array()) {

    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
    $filter = isset($config["filter"]) ? $config["filter"] : 0;
    $id_tipo = isset($config["id_tipo"]) ? $config["id_tipo"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : "";
    $not_tipo = isset($config["not_tipo"]) ? $config["not_tipo"] : "";
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
    $fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
    $activo_desde = isset($config["activo_desde"]) ? $config["activo_desde"] : "";
    $activo_hasta = isset($config["activo_hasta"]) ? $config["activo_hasta"] : "";
    $mes = isset($config["mes"]) ? $config["mes"] : 0;
    $anio = isset($config["anio"]) ? $config["anio"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $localidad = isset($config["localidad"]) ? $config["localidad"] : "";
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "A.fecha DESC";
    $id_marca = isset($config["id_marca"]) ? $config["id_marca"] : 0;
    $modelo = isset($config["modelo"]) ? $config["modelo"] : "";
    $anio = isset($config["anio"]) ? $config["anio"] : "";
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;

    // Utilizado en proyecto TDFCars:
    // Controla si la fecha_vencimiento del cliente es menor a la fecha de hoy,
    // para saber si los clasificados siguen activos
    $fecha_cliente = isset($config["fecha_cliente"]) ? $config["fecha_cliente"] : 0;

    $sql = "SELECT IF(MAX(precio_final) IS NULL,0,MAX(precio_final)) AS maximo ";
    $sql.= "FROM veh_autos A ";
    $sql.= "LEFT JOIN veh_tipos C ON (A.id_tipo = C.id) ";
    $sql.= "LEFT JOIN clientes CLI ON (A.id_cliente = CLI.id AND A.id_empresa = CLI.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (CLI.id_localidad = L.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if ($not_id > 0) $sql.= "AND A.id != $not_id ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
    if (!empty($id_tipo)) $sql.= "AND A.id_tipo = $id_tipo ";
    if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
    if (!empty($activo_desde)) $sql.= "AND A.fecha >= '$activo_desde' ";
    if (!empty($activo_hasta)) $sql.= "AND A.fecha <= '$activo_hasta' ";
    if (!empty($mes)) $sql.= "AND MONTH(A.fecha) = $mes ";
    if (!empty($anio)) $sql.= "AND YEAR(A.fecha) = $anio ";
    if (!empty($tipo)) $sql.= "AND C.link = '$tipo' ";
    if (!empty($not_tipo)) $sql.= "AND C.link != '$not_tipo' ";
    if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
    if (!empty($localidad)) $sql.= "AND L.link = '$localidad' ";
    if (!empty($modelo)) $sql.= "AND A.modelo = '$modelo' ";
    if (!empty($anio)) $sql.= "AND A.anio = '$anio' ";
    if ($fecha_cliente == 1) $sql.= "AND CLI.fecha_vencimiento > NOW() ";
    $q = mysqli_query($this->conx,$sql);
    if ($q === FALSE) {
      error_mail($sql);
      return FALSE;
    }
    $r = mysqli_fetch_object($q);
    return $r->maximo;
  }



  function get_localidades_clasificados($config = array()) {

    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $filter = isset($config["filter"]) ? $config["filter"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $sql = "SELECT L.id, L.nombre, L.link, COUNT(A.id) AS cantidad ";
    $sql.= "FROM veh_autos A ";
    $sql.= "INNER JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    $sql.= "GROUP BY A.id_localidad ";
    $sql.= "ORDER BY cantidad ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    if ($q === FALSE) {
      error_mail($sql);
      return $salida;
    }
    while(($r=mysqli_fetch_object($q))!==NULL) $salida[] = $r;
    return $salida;
  }

  function get_tipos_vehiculos() {
    $sql = "SELECT * FROM veh_tipos ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }

  function get_marcas_vehiculos($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 999999;
    $tiene_elementos = isset($config["tiene_elementos"]) ? $config["tiene_elementos"] : 0;
    $id_tipo = isset($config["id_tipo"]) ? $config["id_tipo"] : 0;
    $sql = "SELECT MV.* FROM marcas_vehiculos MV ";
    $sql.= "WHERE MV.id_empresa = $id_empresa ";
    if ($tiene_elementos == 1 || $id_tipo != 0) {
      $sql.= "AND EXISTS(SELECT * FROM veh_autos A WHERE A.id_empresa = MV.id_empresa AND A.id_marca = MV.id ";
      if ($id_tipo != 0 && $id_tipo != 99) $sql.= "AND A.id_tipo = $id_tipo ";
      $sql.= ") ";
    }
    $sql.= "ORDER BY MV.orden ASC, MV.nombre ASC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }

  function get_total_results() {
    return $this->total;
  }

  private function encoding($e) {
    $e->texto = $this->encod($e->texto);
    $e->plain_text = str_replace("\n", "", strip_tags(html_entity_decode($e->texto,ENT_QUOTES)));
    $e->titulo = ($this->id_empresa == 70) ? $this->encod($e->marca." ".$e->modelo) : $this->encod($e->titulo);
    $e->subtitulo = $this->encod(((!empty($e->anio)) ? "A�o: ".$e->anio : "").((!empty($e->kms)) ? " - ".$e->kms." kms." : ""));
    $e->tipo = $this->encod($e->tipo);
    return $e;
  }

  function destacados($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 12;
    $config["order"] = "A.fecha DESC ";
    $config["destacado"] = 1;
    return $this->get_list($config);
  }

  function ultimos($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 12;
    $config["order"] = "A.fecha DESC ";
    return $this->get_list($config);
  }
}
?>