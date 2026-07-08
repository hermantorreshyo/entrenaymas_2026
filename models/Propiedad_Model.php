<?php
class Propiedad_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $total = 0;
  private $sql = "";

  private $s_params = ""; // String utilizado para obtener todos los parametros enviados

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : utf8_encode($r));
  }

  function calcular_precios($conf = array()) {
    $propiedad = isset($conf["propiedad"]) ? $conf["propiedad"] : FALSE;
    $desde = isset($conf["desde"]) ? $conf["desde"] : "";
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
    $personas = isset($conf["personas"]) ? $conf["personas"] : "";
    $desde = empty($desde) ? date("Y-m-d") : $desde;
    $hasta = empty($hasta) ? date("Y-m-d",strtotime("+1 day")) : $hasta;
    $personas = empty($personas) ? 1 : $personas;
    $moneda = isset($conf["moneda"]) ? $conf["moneda"] : "ARS"; // La moneda con la que se deben mostrar los precios
    $moneda = strtoupper($moneda);

    $propiedad->moneda = trim($propiedad->moneda);
    $propiedad->moneda = strtoupper($propiedad->moneda);
    $cotizacion = 1;
    if ($propiedad->moneda == 'U$S' && $moneda == "ARS") {
      $cotizacion = $this->get_dolar();
      $propiedad->moneda = $moneda;
    }
    $precio_total = 0;

    // Recorremos las fechas
    try {
      // Si hay algun error con las fechas, entonces devolvemos la propiedad y listo
      $d = new DateTime($desde);
      $h = new DateTime($hasta);
    } catch(Exception $e) {
      return $propiedad;
    }

    $diff = date_diff($d,$h);
    $cantidad_noches = $diff->format("%a"); // Total de dias de la estadia

    $interval = new DateInterval('P1D');
    $range = new DatePeriod($d,$interval,$h);
    foreach($range as $fecha) {

      $f = $fecha->format("Y-m-d");
      $dia_semana = $fecha->format("N");

      // Primero controlamos si hay un precio especial para esa fecha
      $sql = "SELECT * FROM inm_propiedades_precios ";
      $sql.= "WHERE id_empresa = $this->id_empresa ";
      $sql.= "AND fecha_desde <= '$f' ";
      $sql.= "AND '$f' <= fecha_hasta ";
      //$sql.= "AND (personas = '$personas' OR personas = 1) ";
      $sql.= "AND id_propiedad = $propiedad->id ";
      $sql.= "AND $cantidad_noches >= minimo_dias_reserva "; // Tiene que completar el minimo
      $sql.= "ORDER BY promocion DESC ";
      $sql.= "LIMIT 0,1 ";
      $q_precio = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q_precio)>0) {
        $r_precio = mysqli_fetch_object($q_precio);

        // Si se esta quedando todo el mes, tomamos el precio por mes
        if ($cantidad_noches >= 30) {
          // Precio por mes
          if ($r_precio->precio_mes > 0) $precio_total += ($r_precio->precio_mes / 30);
          else if ($r_precio->precio_semana > 0) $precio_total += ($r_precio->precio_semana / 7);
          else if (($dia_semana == 6 || $dia_semana == 7) && $r_precio->precio_finde > 0) $precio_total += $r_precio->precio_finde;
          else $precio_total += $r_precio->precio;

        } else if ($cantidad_noches >= 7) {
          // Precio por semana
          if ($r_precio->precio_semana > 0) $precio_total += ($r_precio->precio_semana / 7);
          else if (($dia_semana == 6 || $dia_semana == 7) && $r_precio->precio_finde > 0) $precio_total += $r_precio->precio_finde;
          else $precio_total += $r_precio->precio;

        } else if (($dia_semana == 6 || $dia_semana == 7)) {
          // Si es fin de semana, tomamos ese precio
          if ($r_precio->precio_finde > 0) $precio_total += $r_precio->precio_finde;
          else $precio_total += $r_precio->precio;

        } else {
          // Precio regular
          $precio_total += $r_precio->precio;
        }
      } else {
        if ($cantidad_noches >= 30) {
          
          // Precio por mes
          if ($propiedad->alq_tarifa_base_mes > 0) $precio_total += ($propiedad->alq_tarifa_base_mes / 30);
          else if ($propiedad->alq_tarifa_base_semana > 0) $precio_total += ($propiedad->alq_tarifa_base_semana / 7);
          else if (($dia_semana == 6 || $dia_semana == 7) && $propiedad->alq_tarifa_base_finde > 0) $precio_total += $propiedad->alq_tarifa_base_finde;
          else $precio_total += $propiedad->precio_final;

        } else if ($cantidad_noches >= 7) {
          
          // Precio por semana
          if ($propiedad->alq_tarifa_base_semana > 0) $precio_total += ($propiedad->alq_tarifa_base_semana / 7);
          else if (($dia_semana == 6 || $dia_semana == 7) && $propiedad->alq_tarifa_base_finde > 0) $precio_total += $propiedad->alq_tarifa_base_finde;
          else $precio_total += $propiedad->precio_final;

        } else if (($dia_semana == 6 || $dia_semana == 7)) {
          // Si es fin de semana, tomamos ese precio
          if ($propiedad->alq_tarifa_base_finde > 0) $precio_total += $propiedad->alq_tarifa_base_finde;
          else $precio_total += $propiedad->precio_final;

        } else {
          $precio_total += $propiedad->precio_final;
        }
      }
    }

    // Multiplicamos la cotizacion actual
    $propiedad->cotizacion = $cotizacion;
    $propiedad->precio_final = $propiedad->precio_final * $cotizacion;
    $precio_total = $precio_total * $cotizacion;
    $propiedad->alq_tarifa_base_finde = $propiedad->alq_tarifa_base_finde * $cotizacion;
    $propiedad->alq_tarifa_base_semana = $propiedad->alq_tarifa_base_semana * $cotizacion;
    $propiedad->alq_tarifa_base_mes = $propiedad->alq_tarifa_base_mes * $cotizacion;

    // Calculamos si aplica algun tipo de descuento
    $propiedad->porc_bonif = 0;
    $datetime1 = date_create($desde);
    $datetime2 = date_create();
    $interval = date_diff($datetime1, $datetime2);
    $dias = $interval->format("%a");    
    $dias_antes = ceil($propiedad->alq_ultima_hora_cantidad / 24);
    if ($dias <= $dias_antes) {
      // Aplica la oferta de ultima hora
      $propiedad->porc_bonif = (float)$propiedad->alq_descuento_ultima_hora;
    } else if ($dias >= $propiedad->alq_descuento_por_anticipado_cantidad) {
      // Aplica oferta por venta anticipada
      $propiedad->porc_bonif = (float)$propiedad->alq_descuento_por_anticipado;
    }

    $propiedad->cantidad_noches = $cantidad_noches;
    
    $propiedad->precio_sin_descuento = $precio_total;
    $propiedad->precio_sin_descuento_por_noche = ($cantidad_noches > 0) ? ($precio_total / $cantidad_noches) : 0;

    $propiedad->precio_final = round($precio_total * ((100 - $propiedad->porc_bonif) / 100),2);
    $propiedad->precio_por_noche = (($cantidad_noches > 0) ? round($propiedad->precio_final / $cantidad_noches,2) : 0);

    $propiedad->precio = $propiedad->moneda." ".$propiedad->precio_final;

    // Obtenemos los impuestos
    $sql = "SELECT * FROM inm_propiedades_impuestos ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id_propiedad = $propiedad->id ";
    $propiedad->impuestos = array();
    $propiedad->total_impuestos = 0;
    $q = mysqli_query($this->conx,$sql);
    while(($imp=mysqli_fetch_object($q))!==NULL) {
      $imp->total = 0;
      if ($imp->tipo == 1) {
        // Porcentaje de reserva
        $imp->total = $propiedad->precio_final * ($imp->monto / 100);
      } else if ($imp->tipo == 2) {
        // Tarifa por viajero
        $imp->total = $personas * $imp->monto * $cotizacion;
      } else if ($imp->tipo == 3) {
        // Tarifa por persona y noche
        $imp->total = $personas * $imp->monto * $cantidad_noches * $cotizacion;
      } else if ($imp->tipo == 4) {
        // Tarifa por noche
        $imp->total = $imp->monto * $cantidad_noches * $cotizacion;
      } else if ($imp->tipo == 5) {
        // Precio fijo por estadia
        $imp->total = $imp->monto * $cotizacion;
      }
      $propiedad->total_impuestos += ((float)$imp->total);
      $propiedad->impuestos[] = $imp;
    }
    $propiedad->total_con_impuestos = ((float)$propiedad->precio_final) + ((float)$propiedad->total_impuestos);
    return $propiedad;
  }

  function calcular_disponibilidad($conf = array()) {
    $propiedad = isset($conf["propiedad"]) ? $conf["propiedad"] : FALSE;
    $desde = isset($conf["desde"]) ? $conf["desde"] : "";
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
    if (empty($desde) || empty($hasta)) return 1;
    //$desde = empty($desde) ? date("Y-m-d") : $desde;
    //$hasta = empty($hasta) ? date("Y-m-d",strtotime("+1 day")) : $hasta;
    $mayores = isset($conf["mayores"]) ? $conf["mayores"] : 1;
    $mayores = empty($mayores) ? 1 : $mayores;
    $menores = isset($conf["menores"]) ? $conf["menores"] : 0;
    if (empty($propiedad)) return 0;
    if (empty($desde)) return 0;
    if (empty($hasta)) return 0;
    $disponibilidad = 0;
    try {
      // Si hay algun problema con las fechas, calculamos la disponibilidad
      $d = new DateTime($desde);
      $h = new DateTime($hasta);
      $diff = date_diff($d,$h);
    } catch(Exception $e) {
      return 0;
    }
    $cantidad_noches = $diff->format("%a"); // Total de dias de la estadia


    // Consultamos si aplica algun precio promocional
    $sql = "SELECT * FROM inm_propiedades_precios ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND fecha_desde <= '$desde' ";
    $sql.= "AND '$hasta' <= fecha_hasta ";
    $sql.= "AND id_propiedad = $propiedad->id ";
    $sql.= "ORDER BY promocion DESC ";
    $sql.= "LIMIT 0,1 ";
    $q_precio = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q_precio)>0) {
      $r_precio = mysqli_fetch_object($q_precio);
      if ($cantidad_noches < $r_precio->minimo_dias_reserva) {
        return 0;
      }      
    } else {
      // Si no se aplica ningun precio promocional, comprobamos con el minimo de dias estandar
      // Al menos se tiene que reservar tanta cantidad de dias
      if ($cantidad_noches < $propiedad->alq_minimo_dias_reserva) {
        return 0;
      }
    }

    // Controlar que se reserve X dias antes
    $hoy = new DateTime();
    $comienza = date_diff($d,$hoy);
    $dias_previos = $diff->format("%a");
    if ($dias_previos < $propiedad->alq_reservar_dias_antes) {
      return 0;
    }

    // Controlar que tenga capacidad suficiente
    if ($propiedad->capacidad_maxima < $mayores || $propiedad->capacidad_maxima_menores < $menores) {
      return 0;
    }

    // Controlamos que haya disponibilidad entre ambas fechas
    $disponibilidad = 1;
    $personas = ((int)$mayores) + ((int)$menores);
    $sql = "SELECT RD.* ";
    $sql.= "FROM inm_propiedades_reservas_disponibilidad RD ";
    $sql.= "LEFT JOIN inm_propiedades_reservas R ON (RD.id_empresa = R.id_empresa AND RD.id_reserva = R.id) ";
    $sql.= "WHERE RD.disponible < '$personas' ";
    $sql.= "AND RD.id_propiedad = $propiedad->id ";
    $sql.= "AND '$desde' <= RD.fecha  ";
    $sql.= "AND RD.fecha < '$hasta' ";
    $sql.= "AND RD.id_empresa = $this->id_empresa ";
    $sql.= "AND R.eliminada = 0 ";
    $q_disp = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q_disp)>0) {
      $disponibilidad = 0;
    }
    return $disponibilidad;
  }

  // Extrae los parametros de la URL, llama al listado y devuelve las variables
  function get_variables($config = array()) {

    global $params;
    global $get_params;
    $page = 0;
    $vc_link = "propiedades/";
    $no_analizar_url = isset($config["no_analizar_url"]) ? $config["no_analizar_url"] : 0;

    // propiedades/localidad/tipo_operacion/?parametros..
   
    $link_tipo_operacion = "";
    if (isset($params[1]) && $no_analizar_url == 0) {
      // Si el parametro es numero, es un numero de pagina
      if (is_numeric($params[1])) {
        $page = (int)$params[1];
      } else {
        $link_tipo_operacion = ($params[1] == "todos" || $params[1] == "todas") ? "" : $params[1];
        $vc_link.= (empty($link_tipo_operacion) ? "todos" : $link_tipo_operacion)."/";
      }
    }
    $tipo_operacion = ucwords(strtolower(str_replace("-", " ", $link_tipo_operacion)));

    $link_localidad = "";
    if (isset($params[2]) && $no_analizar_url == 0) {
      // Si el parametro es numero, es un numero de pagina
      if (is_numeric($params[2])) {
        $page = (int)$params[2];
      } else {
        $link_localidad = ($params[2] == "todas" || $params[2] == "todos") ? "" : $params[2];
        $vc_link.= (empty($link_localidad) ? "todas" : $link_localidad)."/";
      }
    }

    // Si existe un link de localidad
    $vc_id_departamento = 0;
    if (!empty($link_localidad)) {
      $sql = "SELECT * FROM com_localidades WHERE link = '$link_localidad' ";
      $q_localidad = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q_localidad)>0) {
        $departamento = mysqli_fetch_object($q_localidad);
        $vc_id_departamento = $departamento->id_departamento;
      }
    }
    if (isset($get_params["dep"])) $vc_id_departamento = $get_params["dep"];

    // Si el ultimo parametro es un numero de pagina
    if (isset($params[3]) && is_numeric($params[3]) && $no_analizar_url == 0) {
      $page = (int)$params[3];
    }

    $orden = (isset($config["orden"]) ? $config["orden"] : 4);
    if (isset($get_params['orden'])) {
      if ($get_params['orden']=='nuevo') $orden = -1;
      elseif ($get_params['orden']=='barato') $orden = 2;
      elseif ($get_params['orden']=='caro') $orden = 1;
      elseif ($get_params['orden']=='destacados') $orden = 4;
      else $orden = $get_params["orden"];
    } 
    
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $personas = isset($config["personas"]) ? $config["personas"] : 0;
    $ids_tipo_operacion = isset($config["ids_tipo_operacion"]) ? $config["ids_tipo_operacion"] : array();

    $view = isset($get_params["view"]) ? $get_params["view"] : 0;
    $tiene_balcon = isset($get_params["balcon"]) ? $get_params["balcon"] : 0;
    $tiene_cochera = isset($get_params["cochera"]) ? $get_params["cochera"] : 0;
    $tiene_patio = isset($get_params["patio"]) ? $get_params["patio"] : 0;
    $tiene_frente = isset($get_params["tiene_frente"]) ? $get_params["tiene_frente"] : 0;
    $tiene_contrafrente = isset($get_params["tiene_contrafrente"]) ? $get_params["tiene_contrafrente"] : 0;
    $tiene_interno = isset($get_params["tiene_interno"]) ? $get_params["tiene_interno"] : 0;

    $id_tipo_inmueble = isset($get_params["tp"]) ? $get_params["tp"] : "";
    $apto_banco = isset($get_params["banco"]) ? $get_params["banco"] : 0;
    $pint = isset($get_params["pint"]) ? $get_params["pint"] : 0;
    $video = isset($get_params["video"]) ? $get_params["video"] : 0;
    $acepta_permuta = isset($get_params["per"]) ? $get_params["per"] : 0;
    $moneda = isset($get_params["m"]) ? $get_params["m"] : "";
    $dormitorios = isset($get_params["dm"]) ? $get_params["dm"] : "";
    $antiguedad = isset($get_params["antiguedad"]) ? $get_params["antiguedad"] : "";
    $banios = isset($get_params["bn"]) ? $get_params["bn"] : "";
    $calle = isset($get_params["calle"]) ? $get_params["calle"] : "";
    $cocheras = isset($get_params["gar"]) ? $get_params["gar"] : "";
    $codigo = isset($get_params["cod"]) ? $get_params["cod"] : "";
    $filter = isset($get_params["filter"]) ? $get_params["filter"] : "";
    $offset = isset($get_params["offset"]) ? $get_params["offset"] : (isset($config["offset"]) ? $config["offset"] : 12);

    $vc_minimo = (isset($get_params["vc_minimo"])) ? filter_var($get_params["vc_minimo"],FILTER_SANITIZE_STRING) : 0;
    if ($vc_minimo == "undefined" || empty($vc_minimo)) $vc_minimo = 0;

    $vc_maximo = (isset($get_params["vc_maximo"])) ? filter_var($get_params["vc_maximo"],FILTER_SANITIZE_STRING) : 0;
    if ($vc_maximo == "undefined" || empty($vc_maximo)) $vc_maximo = 0;

    if (!empty($codigo)) {
      $config_list = array(
        "codigo"=>$codigo
      );
    } else {
      $config_list = array(
        "filter"=>$filter,
        "banios"=>$banios,
        "link_localidad"=>$link_localidad,
        "id_tipo_inmueble"=>$id_tipo_inmueble,
        "id_departamento"=>$vc_id_departamento,
        "link_tipo_operacion"=>$link_tipo_operacion,
        "tiene_cochera"=>$tiene_cochera,
        "cocheras"=>$cocheras,
        "calle"=>$calle,
        "tiene_balcon"=>$tiene_balcon,
        "tiene_patio"=>$tiene_patio,
        "tiene_frente"=>$tiene_frente,
        "tiene_contrafrente"=>$tiene_contrafrente,
        "tiene_patio"=>$tiene_patio,
        "dormitorios"=>$dormitorios,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "personas"=>$personas,
        "offset"=>$offset,
        "apto_banco"=>$apto_banco,
        "pint"=>$pint,
        "video"=>$video,
        "acepta_permuta"=>$acepta_permuta,
        "order"=>$orden,
        "limit"=>($page*$offset),
        "antiguedad"=>$antiguedad,
        "ids_tipo_operacion"=>$ids_tipo_operacion,
      );

      if ($moneda == "USD") {
        // Si esta seteado el parametro en dolares
        $config_list["minimo_usd"] = $vc_minimo;
        $config_list["maximo_usd"] = $vc_maximo;
        $config_list["moneda"] = "USD";

      } else if ($moneda == "ARS") {
        // Si esta seteado el parametro en pesos
        $config_list["minimo"] = $vc_minimo;
        $config_list["maximo"] = $vc_maximo;
        $config_list["moneda"] = "ARS";

      } else {
        // Si es en VENTAS, directamente buscamos en dolares
        if (strtolower($link_tipo_operacion) == "ventas") {
          $config_list["minimo_usd"] = $vc_minimo;
          $config_list["maximo_usd"] = $vc_maximo;
          $moneda = "USD";
        } else {
          $config_list["minimo"] = $vc_minimo;
          $config_list["maximo"] = $vc_maximo;
          $moneda = "$";
        }
      }
    }
    if (isset($config["moneda"])) $config_list["moneda"] = $config["moneda"];
    $s_params = (!empty($get_params)) ? "?".http_build_query($get_params) : "";
    $listado = $this->get_list($config_list);

    $total = $this->get_total_results();
    $total_paginas = ceil($total / $offset);

    $precio_maximo = $this->get_precio_maximo(array(
      "link_tipo_operacion"=>$link_tipo_operacion,
      "publica_precio"=>0,
    ));
    //if (empty($vc_maximo)) $vc_maximo = $precio_maximo;

    return array(
      "vc_total_resultados"=>$total,
      "vc_total_paginas"=>$total_paginas,
      "vc_maximo"=>$vc_maximo,
      "vc_minimo"=>$vc_minimo,
      "vc_moneda"=>$moneda,
      "vc_precio_maximo"=>$precio_maximo,
      "vc_params"=>$s_params,
      "vc_listado"=>$listado,
      "vc_filter"=>$filter,
      "vc_page"=>$page,
      "vc_calle"=>$calle,
      "vc_banios"=>$banios,
      "vc_cocheras"=>$cocheras,
      "vc_offset"=>$offset,
      "vc_codigo"=>$codigo,
      "vc_link_localidad"=>$link_localidad,
      "vc_id_tipo_inmueble"=>$id_tipo_inmueble,
      "vc_id_departamento"=>$vc_id_departamento,
      "vc_link_tipo_operacion"=>$link_tipo_operacion,
      "vc_tipo_operacion"=>$tipo_operacion,
      "vc_tiene_cochera"=>$tiene_cochera,
      "vc_tiene_balcon"=>$tiene_balcon,
      "vc_tiene_patio"=>$tiene_patio,
      "vc_tiene_frente"=>$tiene_frente,
      "vc_tiene_contrafrente"=>$tiene_contrafrente,
      "vc_tiene_patio"=>$tiene_patio,
      "vc_dormitorios"=>$dormitorios,
      "vc_antiguedad"=>$antiguedad,
      "vc_apto_banco"=>$apto_banco,
      "vc_pint"=>$pint,
      "vc_video"=>$video,
      "vc_acepta_permuta"=>$acepta_permuta,
      "vc_orden"=>$orden,
      "vc_link"=>$vc_link,
      "vc_view"=>$view,
    );
  }

  function get_sql() {
    return $this->sql;
  }

  // Obtiene la cotizacion del dolar
  function get_dolar() {
    // Primero consultamos si tiene una cotizacion especial la empresa
    $sql_cot = 'SELECT * FROM cotizaciones WHERE moneda = "U$D" AND id_empresa = '.$this->id_empresa.' ORDER BY fecha DESC LIMIT 0,1 ';
    $q_cot = mysqli_query($this->conx,$sql_cot);
    if (mysqli_num_rows($q_cot)>0) {
      $r_cot = mysqli_fetch_object($q_cot);  
      return $r_cot->valor;
    }
    $sql_cot = 'SELECT * FROM cotizaciones WHERE moneda = "U$D" ORDER BY fecha DESC LIMIT 0,1 ';
    $q_cot = mysqli_query($this->conx,$sql_cot);
    $r_cot = mysqli_fetch_object($q_cot);
    return $r_cot->valor;
  }

  function get_precio_maximo($config=array()) {

    $id_tipo_operacion = isset($config["id_tipo_operacion"]) ? $config["id_tipo_operacion"] : 0;
    $link_tipo_operacion = isset($config["link_tipo_operacion"]) ? $config["link_tipo_operacion"] : "";
    $publica_precio = isset($config["publica_precio"]) ? $config["publica_precio"] : 1;

    $empresas_compartida = $this->get_empresas_red();
    $empresas_compartida[] = $this->id_empresa;
    $emp_comp = implode(",", $empresas_compartida);    

    $cotizacion = $this->get_dolar();

    $sql = 'SELECT IF(MAX(P.precio_final) IS NULL,0,MAX(P.precio_final)) AS maximo ';
    $sql.= "FROM inm_propiedades P ";
    $sql.= "LEFT JOIN inm_tipos_operacion TIPO_OP ON (P.id_tipo_operacion = TIPO_OP.id) ";
    $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
    $sql.= 'AND P.moneda = "U$S" ';
    if ($publica_precio == 1) $sql.= "AND P.publica_precio = 1 ";
    if ($id_tipo_operacion != 0) $sql.= "AND P.id_tipo_operacion = ".$id_tipo_operacion;
    if (!empty($link_tipo_operacion)) $sql.= "AND TIPO_OP.link = '$link_tipo_operacion' ";
    $q_maximo_usd = mysqli_query($this->conx,$sql);
    $r_maximo_usd = mysqli_fetch_object($q_maximo_usd);
    $maximo_usd = (ceil($r_maximo_usd->maximo * ($cotizacion/100))*100);

    $sql = 'SELECT IF(MAX(P.precio_final) IS NULL,0,MAX(P.precio_final)) AS maximo ';
    $sql.= "FROM inm_propiedades P ";
    $sql.= "LEFT JOIN inm_tipos_operacion TIPO_OP ON (P.id_tipo_operacion = TIPO_OP.id) ";
    $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
    $sql.= 'AND P.moneda = "$" ';
    if ($publica_precio == 1) $sql.= "AND P.publica_precio = 1 ";
    if ($id_tipo_operacion != 0) $sql.= "AND P.id_tipo_operacion = ".$id_tipo_operacion;
    if (!empty($link_tipo_operacion)) $sql.= "AND TIPO_OP.link = '$link_tipo_operacion' ";
    $q_maximo_pe = mysqli_query($this->conx,$sql);
    $r_maximo_pe = mysqli_fetch_object($q_maximo_pe);
    $maximo_pe = $r_maximo_pe->maximo;
    return max($maximo_usd,$maximo_pe);
  }

  // Obtenemos los datos del propiedad
  function get($id,$config = array()) {

    $buscar_relacionados = isset($config["buscar_relacionados"]) ? $config["buscar_relacionados"] : 1;
    $buscar_relacionados_offset = isset($config["buscar_relacionados_offset"]) ? $config["buscar_relacionados_offset"] : 3;
    $buscar_total_visitas = isset($config["buscar_total_visitas"]) ? $config["buscar_total_visitas"] : 0;
    $buscar_fechas_reservas = isset($config["buscar_fechas_reservas"]) ? $config["buscar_fechas_reservas"] : 0;
    $buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;
    $buscar_propietario = isset($config["buscar_propietario"]) ? $config["buscar_propietario"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $personas = isset($config["personas"]) ? $config["personas"] : 1;
    $moneda = isset($config["moneda"]) ? $config["moneda"] : "$";
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $id_empresa_original = isset($config["id_empresa_original"]) ? $config["id_empresa_original"] : $this->id_empresa;
    $orden_departamentos = isset($config["orden_departamentos"]) ? $config["orden_departamentos"] : "orden ASC";

    $id = (int)$id;
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.valido_hasta = '0000-00-00','',DATE_FORMAT(A.valido_hasta,'%d/%m/%Y')) AS valido_hasta, ";
    $sql.= "IF(P.nombre IS NULL,'',P.nombre) AS propietario, ";
    $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS tipo_estado, ";
    $sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo_inmueble, ";
    $sql.= "IF(X.nombre IS NULL,'',X.nombre) AS tipo_operacion, ";
    $sql.= "IF(X.link IS NULL,'',X.link) AS tipo_operacion_link, ";
    $sql.= "IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= "IF(C.email IS NULL,'',C.email) AS cliente_email, ";
    $sql.= "IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= "IF(U.email IS NULL,'',U.email) AS usuario_email, ";
    $sql.= "IF(U.path IS NULL,'',U.path) AS usuario_path, ";
    $sql.= "IF(PART.nombre IS NULL,'',PART.nombre) AS partido, ";
    $sql.= "IF(PROV.nombre IS NULL,'',PROV.nombre) AS provincia, ";
    $sql.= "IF(PAIS.nombre IS NULL,'',PAIS.nombre) AS pais, ";
    $sql.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql.= "IF(L.link IS NULL,'',L.link) AS localidad_link ";
    $sql.= "FROM inm_propiedades A ";
    $sql.= "LEFT JOIN inm_tipos_estado TE ON (A.id_tipo_estado = TE.id) ";
    $sql.= "LEFT JOIN inm_tipos_inmueble TI ON (A.id_tipo_inmueble = TI.id) ";
    $sql.= "LEFT JOIN inm_tipos_operacion X ON (A.id_tipo_operacion = X.id) ";
    $sql.= "LEFT JOIN inm_propietarios P ON (A.id_propietario = P.id AND A.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "LEFT JOIN com_departamentos PART ON (L.id_departamento = PART.id) ";
    $sql.= "LEFT JOIN com_provincias PROV ON (PART.id_provincia = PROV.id) ";
    $sql.= "LEFT JOIN com_paises PAIS ON (PROV.id_pais = PAIS.id) ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "LEFT JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if ($id_cliente != 0) $sql.= "AND A.id_cliente = $id_cliente ";
    $this->sql = $sql;
    $q = mysqli_query($this->conx,$sql);

    if ($q === FALSE) {
      error_mail($this->sql);
      return FALSE;
    }

    if (mysqli_num_rows($q) == 0) return FALSE;
    $propiedad = mysqli_fetch_object($q);

		//$propiedad->path = ((strpos($propiedad->path,"http://")===FALSE)) ? "/sistema/".$propiedad->path : $propiedad->path;

    $propiedad->relacionados = array();
    if ($buscar_relacionados == 1) {
      /*
      // Primero obtenemos los propiedades relacionados con ese producto
      $sql = "SELECT AR.* ";
      $sql.= "FROM inm_propiedades_relacionados AR ";
      $sql.= "WHERE AR.id_propiedad = $id ";
      $sql.= "ORDER BY AR.orden ASC ";
      $sql.= "LIMIT 0, $buscar_relacionados_offset ";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        // Obtenemos los datos de esa propiedad relacionada, y la ponemos en el array
        $propiedad->relacionados[] = $this->get($r->id_relacion,array(
          "buscar_relacionados"=>0,
          "buscar_imagenes"=>0,
        ));
      }

			// Armamos el array de ids que no se deben buscar similares
      foreach($propiedad->relacionados as $rr) {
        $not_in[] = $rr->id;
      }
      */
      $not_in = array($propiedad->id);

			// Ahora buscamos las propiedades similares
      $similares = $this->get_list(array(
        "id_localidad"=>$propiedad->id_localidad,
        "id_tipo_operacion"=>$propiedad->id_tipo_operacion,
        "id_tipo_inmueble"=>$propiedad->id_tipo_inmueble,
        "id_tipo_estado"=>$propiedad->id_tipo_estado,
        "offset"=>$buscar_relacionados_offset,
        "not_in"=>$not_in,
        "order"=>3,
        "id_empresa"=>$id_empresa_original,
      ));

      $propiedad->relacionados = array_merge($propiedad->relacionados,$similares);
      $propiedad->relacionados = array_slice($propiedad->relacionados,0,$buscar_relacionados_offset);
    }

    $propiedad->images = array();
    $propiedad->planos = array();
    if ($buscar_imagenes == 1) {
      // Obtenemos las imagenes de ese propiedad
      $sql = "SELECT AI.* FROM inm_propiedades_images AI WHERE AI.id_propiedad = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        if (!empty($r->path)) {
          $r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
        }                
        if ($r->plano == 1) $propiedad->planos[] = $r->path;
        else $propiedad->images[] = $r->path;
      }
    }
    $propiedad->imagen = (empty($propiedad->path)) ? "" : (((strpos($propiedad->path,"http")===FALSE)) ? "/sistema/".$propiedad->path : $propiedad->path);

    // Con esto evitamos que se este mostrando el precio en algun lado
    if ($propiedad->publica_precio == 0) {
      $propiedad->moneda = "";
      $propiedad->precio_final = "Consultar";
    }

    $propiedad->total_visitas = 0;
    if ($buscar_total_visitas == 1) {
      // Obtenemos la cantidad de visitas en las ultimas 24 hs
      $desde = strtotime('-1 day');
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS total_visitas ";
      $sql.= "FROM inm_propiedades_visitas ";
      $sql.= "WHERE id_propiedad = $propiedad->id ";
      $sql.= "AND stamp >= '$desde' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $q = mysqli_query($this->conx,$sql);
      $r = mysqli_fetch_object($q);
      $propiedad->total_visitas = $r->total_visitas;
    }

    $propiedad->propietario = FALSE;
    if ($buscar_propietario == 1) {
      $sql = "SELECT * FROM clientes WHERE id_empresa = $propiedad->id_empresa AND id = $propiedad->id_propietario ";
      $qq = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q)>0) {
        $propiedad->propietario = mysqli_fetch_object($qq);
      }
    }

    // Obtenemos los departamentos
    $sql = "SELECT * ";
    $sql.= "FROM inm_departamentos ";
    $sql.= "WHERE id_propiedad = $propiedad->id AND id_empresa = $propiedad->id_empresa ";
    $sql.= "ORDER BY $orden_departamentos ";
    $q = mysqli_query($this->conx,$sql);
    $propiedad->departamentos = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {

      // Obtenemos las imagenes
      $sql = "SELECT path FROM inm_departamentos_images ";
      $sql.= "WHERE id_empresa = $propiedad->id_empresa ";
      $sql.= "AND id_propiedad = $propiedad->id ";
      $sql.= "AND id_departamento = $r->id ";
      $qq = mysqli_query($this->conx,$sql);
      $r->images_dptos = array();
      while(($rr=mysqli_fetch_object($qq))!==NULL) $r->images_dptos[] = $rr->path;

      $propiedad->departamentos[] = $r;
    }

    // Area total
    $propiedad->superficie_total = $propiedad->superficie_cubierta + $propiedad->superficie_descubierta + $propiedad->superficie_semicubierta;

    $propiedad = $this->encoding($propiedad);

    $propiedad->pertenece_red = ($this->id_empresa == $propiedad->id_empresa) ? 0 : 1;

    $propiedad->link_propiedad = (isset($propiedad->pertenece_red) && $propiedad->pertenece_red == 1) ? mklink($propiedad->link)."?em=".$propiedad->id_empresa : mklink($propiedad->link);
    $propiedad->link_ficha = mklink("/")."sistema/propiedades/function/ver_ficha/".$propiedad->id_empresa."/".$propiedad->id."/".$this->id_empresa."/";

    $propiedad->disponible = 1;
    if ($propiedad->id_tipo_operacion == 3) {
      // Si es un alquiler temporario
      $propiedad = $this->calcular_precios(array(
        "propiedad"=>$propiedad,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "moneda"=>$moneda,
        "personas"=>$personas,
      ));
      $propiedad->disponible = $this->calcular_disponibilidad(array(
        "propiedad"=>$propiedad,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "mayores"=>$personas,
      )); 

      // En caso de que la fecha tenga alguna promocion
      // actualizamos el valor de la propiedad alq_minimo_dias_reserva
      $sql = "SELECT * FROM inm_propiedades_precios ";
      $sql.= "WHERE id_empresa = $this->id_empresa ";
      $sql.= "AND fecha_desde <= '$desde' ";
      $sql.= "AND '$hasta' <= fecha_hasta ";
      $sql.= "AND id_propiedad = $propiedad->id ";
      $sql.= "ORDER BY promocion DESC ";
      $sql.= "LIMIT 0,1 ";
      $q_precio = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q_precio)>0) {
        $r_precio = mysqli_fetch_object($q_precio);
        $propiedad->alq_minimo_dias_reserva = $r_precio->minimo_dias_reserva;
      }

      if ($buscar_fechas_reservas == 1) {
        $propiedad->fechas_reservas = array();
        $sql = "SELECT fecha_desde, fecha_hasta ";
        $sql.= "FROM inm_propiedades_reservas ";
        $sql.= "WHERE id_empresa = $this->id_empresa ";
        $sql.= "AND id_propiedad = $propiedad->id ";
        $sql.= "AND eliminada = 0 ";
        $qf = mysqli_query($this->conx,$sql);
        while(($f=mysqli_fetch_object($qf))!==NULL) {
          $d = new DateTime($f->fecha_desde);
          $h = new DateTime($f->fecha_hasta);
          $interval = new DateInterval('P1D');
          $range = new DatePeriod($d,$interval,$h);
          foreach($range as $ff) {
            $propiedad->fechas_reservas[] = $ff->format("Y-m-d");
          }
        }
      }

    }

    // Si no tiene nada predefinido, formamos el SEO
    if (empty($propiedad->seo_description)) {
      $propiedad->seo_description = ucfirst(substr(str_replace("\n", " ", html_entity_decode($propiedad->plain_text)),0,155))."...";
      $propiedad->seo_description = str_replace("<br />", " ", $propiedad->seo_description);
      $propiedad->seo_description = str_replace("<br/>", " ", $propiedad->seo_description);
    }
    if (empty($propiedad->seo_title)) {
      $propiedad->seo_title = $propiedad->nombre;
    }

    return $propiedad;
  }

  private function encoding($p) {
    
    // Formamos el precio (si se debe mostrar o no)
    if ($p->publica_precio == 1) {
      $p->precio = $p->moneda." ".number_format($p->precio_final,0,"",".");
    } else {
      $p->precio = "Consultar";
    }

    $p->direccion = $this->encod($p->calle. (($p->publica_altura == 1)?" ".$p->altura:"") . (!empty($p->piso) ? " Piso ".$p->piso : "") . (!empty($p->numero) ? " Depto ".$p->numero : ""));
    $p->direccion_completa = $p->direccion.(!empty($p->entre_calles) ? " e/ ".$p->entre_calles : "");

    $p->plain_text = (!empty($p->descripcion)) ? $this->encod($p->descripcion) : $this->encod(strip_tags($p->texto,"<a><i><b><br>"));
    $p->nombre = $this->encod($p->nombre);
    $p->subtitulo = $this->encod($p->subtitulo);
    if (isset($p->tipo_inmueble)) $p->tipo_inmueble = $this->encod($p->tipo_inmueble);
    if (isset($p->tipo_operacion)) $p->tipo_operacion = $this->encod($p->tipo_operacion);
    $p->custom_1 = $this->encod($p->custom_1);
    $p->custom_2 = $this->encod($p->custom_2);
    $p->custom_3 = $this->encod($p->custom_3);
    $p->custom_4 = $this->encod($p->custom_4);
    $p->custom_5 = $this->encod($p->custom_5);
    $p->custom_6 = $this->encod($p->custom_6);
    $p->custom_7 = $this->encod($p->custom_7);
    $p->custom_8 = $this->encod($p->custom_8);
    $p->custom_9 = $this->encod($p->custom_9);
    $p->custom_10 = $this->encod($p->custom_10);
    return $p;
  }    

  function set_tracking_cookie($config = array()) {
    $id_propiedad = isset($config["id_propiedad"]) ? $config["id_propiedad"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    setcookie($id_propiedad."_".$id_empresa,"1",time()+60*60*24,"/");
  }

  function tracking_code($config = array()) {
    $id_propiedad = isset($config["id_propiedad"]) ? $config["id_propiedad"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $id_empresa_compartida = isset($config["id_empresa_compartida"]) ? $config["id_empresa_compartida"] : 0;
    // Si existe la cookie para esa propiedad, no volvemos a registrar la visita porque el usuario ya la vio
    if (isset($_COOKIE[$id_propiedad."_".$id_empresa])) return;
    $id_cliente = isset($_COOKIE["idc"]) ? $_COOKIE["idc"] : 0;
    $s = '';
    $s.='<script>';
    $s.='setTimeout(function(){';
    $s.='  $.ajax({';
    $s.='    "url":"/sistema/visitas/function/inmovar/",';
    $s.='    "dataType":"json",';
    $s.='    "type":"get",';
    $s.='    "data":{';
    $s.='      "e":"'.$id_empresa.'",';
    $s.='      "p":"'.$id_propiedad.'",';
    $s.='      "c":"'.$id_cliente.'",';
    $s.='      "ec":"'.$id_empresa_compartida.'",';
    $s.='    }';
    $s.='  });';
    $s.='},10000);'; // Registramos la visita despues de 10 segundos
    $s.='</script>';
    return $s;
  }

  function add_visit($id_propiedad,$id_cliente = 0) {
    $sql = "INSERT INTO inm_propiedades_visitas (id_empresa,id_propiedad,id_cliente,stamp) VALUES(";
    $sql.= $this->id_empresa.",$id_propiedad,$id_cliente,NOW())";
    $q = mysqli_query($this->conx,$sql);
    return TRUE;
  }


  // Devuelve un array con las empresas compartidas con la empresa actual
  // TODO: Esto en un futuro se podria hacer que una empresa pueda compartir en varias redes
  // seria agregar un campo mas a la tabla que identifique de que red es, y luego
  // filtrar la consulta por eso
  function get_empresas_red() {
    $salida = array();

    // Primero controlamos que la empresa tenga configurada la Red Inmovar
    $sql = "SELECT red_inmovar FROM web_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $r = $r=mysqli_fetch_object($q);
    if ($r->red_inmovar == 0) return $salida;

    // Despues buscamos todas las empresas que tengan compartida con la empresa
    $sql = "SELECT PR.id_empresa_compartida ";
    $sql.= "FROM inm_permisos_red PR ";
    $sql.= "INNER JOIN web_configuracion WC ON (PR.id_empresa_compartida = WC.id_empresa) ";
    $sql.= "WHERE PR.id_empresa = $this->id_empresa ";
    $sql.= "AND WC.red_inmovar = 1 "; // Esta habilitada para la RED
    $sql.= "AND PR.estado = 2 "; // El estado 2 significa que comparte en la web
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r->id_empresa_compartida;
    }
    return $salida;
  }


  function get_list($config = array()) {

    $limit = isset($config["limit"]) ? intval($config["limit"]) : 0;
    $order = isset($config["order"]) ? intval($config["order"]) : 0;
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "";
    $offset = isset($config["offset"]) ? intval($config["offset"]) : 6;
    $activo = isset($config["activo"]) ? intval($config["activo"]) : 1;
    $mostrar_home = isset($config["mostrar_home"]) ? intval($config["mostrar_home"]) : -1;
    $destacado = isset($config["destacado"]) ? intval($config["destacado"]) : -1; // -1 = No se tiene en cuenta el parametro
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $codigo = isset($config["codigo"]) ? intval($config["codigo"]) : 0;
    $id_usuario = isset($config["id_usuario"]) ? intval($config["id_usuario"]) : 0;
    $id_localidad = isset($config["id_localidad"]) ? intval($config["id_localidad"]) : 0;
    $id_departamento = isset($config["id_departamento"]) ? intval($config["id_departamento"]) : 0;
    $not_in_ids_localidades = isset($config["not_in_ids_localidades"]) ? $config["not_in_ids_localidades"] : "";
    $link_localidad = isset($config["link_localidad"]) ? $config["link_localidad"] : "";
    $id_tipo_operacion = isset($config["id_tipo_operacion"]) ? intval($config["id_tipo_operacion"]) : 0;
    $link_tipo_operacion = isset($config["link_tipo_operacion"]) ? $config["link_tipo_operacion"] : "";
    $tipo_operacion = isset($config["tipo_operacion"]) ? $config["tipo_operacion"] : "";
    $id_tipo_inmueble = isset($config["id_tipo_inmueble"]) ? intval($config["id_tipo_inmueble"]) : 0;
    $in_ids_tipo_inmueble = isset($config["in_ids_tipo_inmueble"]) ? intval($config["in_ids_tipo_inmueble"]) : 0;
    $link_tipo_inmueble = isset($config["link_tipo_inmueble"]) ? $config["link_tipo_inmueble"] : "";
    $id_tipo_estado = isset($config["id_tipo_estado"]) ? intval($config["id_tipo_estado"]) : 0;
    $id_propietario = isset($config["id_propietario"]) ? intval($config["id_propietario"]) : 0;
    $dormitorios = isset($config["dormitorios"]) ? intval($config["dormitorios"]) : 0;
    $apto_banco = isset($config["apto_banco"]) ? intval($config["apto_banco"]) : 0;
    $pint = isset($config["pint"]) ? intval($config["pint"]) : 0;
    $calle = isset($config["calle"]) ? $config["calle"] : "";
    $video = isset($config["video"]) ? intval($config["video"]) : 0;
    $acepta_permuta = isset($config["acepta_permuta"]) ? intval($config["acepta_permuta"]) : 0;
    $not_in = isset($config["not_in"]) ? $config["not_in"] : array();
    $in = isset($config["in"]) ? $config["in"] : array();
    $banios = isset($config["banios"]) ? intval($config["banios"]) : 0;
    $cocheras = isset($config["cocheras"]) ? intval($config["cocheras"]) : 0;
    $maximo = isset($config["maximo"]) ? floatval($config["maximo"]) : 0;
    $minimo = isset($config["minimo"]) ? floatval($config["minimo"]) : 0;
    $maximo_usd = isset($config["maximo_usd"]) ? floatval($config["maximo_usd"]) : 0;
    $minimo_usd = isset($config["minimo_usd"]) ? floatval($config["minimo_usd"]) : 0;
    $antiguedad = isset($config["antiguedad"]) ? intval($config["antiguedad"]) : 0;
    $id_cliente = isset($config["id_cliente"]) ? intval($config["id_cliente"]) : 0;
    $activo_desde = isset($config["activo_desde"]) ? $config["activo_desde"] : "";
    $solo_contar = isset($config["solo_contar"]) ? intval($config["solo_contar"]) : 0;
    $ids_tipo_operacion = isset($config["ids_tipo_operacion"]) ? $config["ids_tipo_operacion"] : array();
    $valido_hasta = isset($config["valido_hasta"]) ? $config["valido_hasta"] : ($this->id_empresa == 263 ? date("Y-m-d") : "");
    $tiene_etiqueta_link = isset($config["tiene_etiqueta_link"]) ? $config["tiene_etiqueta_link"] : "";

    $tiene_cocheras = isset($config["tiene_cocheras"]) ? intval($config["tiene_cocheras"]) : 0;
    $tiene_balcon = isset($config["tiene_balcon"]) ? intval($config["tiene_balcon"]) : 0;
    $tiene_patio = isset($config["tiene_patio"]) ? intval($config["tiene_patio"]) : 0;
    $tiene_frente = isset($config["tiene_frente"]) ? intval($config["tiene_frente"]) : 0;
    $tiene_contrafrente = isset($config["tiene_contrafrente"]) ? intval($config["tiene_contrafrente"]) : 0;
    $tiene_interno = isset($config["tiene_interno"]) ? intval($config["tiene_interno"]) : 0;

    // Filtros para alquileres temporarios
    $moneda = isset($config["moneda"]) ? $config["moneda"] : "ARS";
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $personas = isset($config["personas"]) ? $config["personas"] : "";

    // Indica si muestra propiedades de la red o solo propias
    $solo_propias = isset($config["solo_propias"]) ? intval($config["solo_propias"]) : 0;

    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? intval($config["buscar_etiquetas"]) : 0;

    // Cotizacion del dolar
    $sql_cot = 'SELECT * FROM cotizaciones WHERE moneda = "U$D" ORDER BY fecha DESC LIMIT 0,1 ';
    $q_cot = mysqli_query($this->conx,$sql_cot);
    $r_cot = mysqli_fetch_object($q_cot);
    $cotizacion = $r_cot->valor;

    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= "IF(A.fecha_publicacion='0000-00-00','',DATE_FORMAT(A.fecha_publicacion,'%d/%m/%Y')) AS fecha_publicacion, ";
    $sql.= "IF(P.nombre IS NULL,'',P.nombre) AS propietario, ";
    $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS tipo_estado, ";
    $sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo_inmueble, ";
    $sql.= "IF(X.nombre IS NULL,'',X.nombre) AS tipo_operacion, ";
    $sql.= "IF(X.link IS NULL,'',X.link) AS tipo_operacion_link, ";
    $sql.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql.= "IF(L.link IS NULL,'',L.link) AS localidad_link ";
    $sql.= "FROM inm_propiedades A ";
    $sql.= "LEFT JOIN inm_tipos_estado TE ON (A.id_tipo_estado = TE.id) ";
    $sql.= "LEFT JOIN inm_tipos_inmueble TI ON (A.id_tipo_inmueble = TI.id) ";
    $sql.= "LEFT JOIN inm_tipos_operacion X ON (A.id_tipo_operacion = X.id) ";
    $sql.= "LEFT JOIN inm_propietarios P ON (A.id_propietario = P.id AND A.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "WHERE 1=1 ";
    if ($apto_banco == 1) $sql.= "AND A.apto_banco = 1 ";
    if ($pint == 1) $sql.= "AND A.pint != '' ";
    if ($video == 1) $sql.= "AND A.video != '' ";
    if ($acepta_permuta == 1) $sql.= "AND A.acepta_permuta = 1 ";
    if ($id_cliente != 0) $sql.= "AND A.id_cliente = $id_cliente ";
    
    // Si la propiedad es propia
    //$sql.= "AND A.id_empresa = $this->id_empresa ";

    $sql.= "AND (A.id_empresa = $this->id_empresa ";
    if ($solo_propias == 0) {
      $empresas_compartida = $this->get_empresas_red();
      if (sizeof($empresas_compartida)>0) {
        $emp_comp = implode(",", $empresas_compartida);
        // O esta compartida con la red
        $sql.= "OR (A.id_empresa IN ($emp_comp) ";
        $sql.= " AND A.compartida = 1 ) ";
      }
    }
    $sql.= ") ";

    if (!empty($not_in)) {
      if (is_array($not_in)) {
        $not_in_array = implode(",",$not_in);
        $sql.= "AND A.id NOT IN (".$not_in_array.") ";
      } else if (is_string($not_in)) {
        $sql.= "AND A.id IN (".$not_in."0) ";
      }
    }
    if (!empty($in)) {
      if (is_array($in)) {
        $in_array = implode(",",$in);
        $sql.= "AND A.id IN (".$in_array.") ";
      } else if (is_string($in)) {
        $sql.= "AND A.id IN (".$in."0) ";
      }
    }
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($mostrar_home != -1) $sql.= "AND A.mostrar_home = $mostrar_home ";
    if ($destacado == 0) $sql.= "AND A.destacado = 0 ";
    else if ($destacado == 1) $sql.= "AND A.destacado > 0 ";
    if ($antiguedad != 0) $sql.= "AND A.nuevo = $antiguedad ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
    if (!empty($codigo)) {
      $sql.= "AND A.codigo = '$codigo' ";
    } else if (!empty($filter)) {
      $sql.= "AND (A.nombre LIKE '%$filter%' OR A.codigo LIKE '$filter' OR L.nombre LIKE '%$filter%' OR TI.nombre LIKE '%$filter%') ";
    }
    if (!empty($link_localidad)) $sql.= "AND L.link = '$link_localidad' ";
    if (!empty($id_localidad)) $sql.= "AND A.id_localidad = $id_localidad ";
    if (!empty($id_departamento)) $sql.= "AND L.id_departamento = $id_departamento ";
    if (!empty($id_tipo_operacion)) $sql.= "AND A.id_tipo_operacion = $id_tipo_operacion ";
    if (!empty($link_tipo_operacion)) $sql.= "AND X.link = '$link_tipo_operacion' ";
    if (!empty($link_tipo_inmueble)) $sql.= "AND TI.link = '$link_tipo_inmueble' ";
    if (!empty($tipo_operacion)) $sql.= "AND X.link = '$tipo_operacion' ";
    if (!empty($ids_tipo_operacion)) {
      if (is_array($ids_tipo_operacion)) $ids_tipo_operacion = implode(",",$ids_tipo_operacion);
      $sql.= "AND A.id_tipo_operacion IN ($ids_tipo_operacion) ";
    }
    if (!empty($id_tipo_inmueble)) $sql.= "AND A.id_tipo_inmueble = $id_tipo_inmueble ";
    if (!empty($in_ids_tipo_inmueble)) $sql.= "AND A.id_tipo_inmueble IN ($in_ids_tipo_inmueble) ";
    if (!empty($id_tipo_estado)) $sql.= "AND A.id_tipo_estado = $id_tipo_estado ";
    if (!empty($id_propietario)) $sql.= "AND A.id_propietario = $id_propietario ";
    if ($dormitorios != "") {
      if ($dormitorios == -99) $sql.= "AND A.dormitorios = 0 AND A.ambientes = 1 ";
      else $sql.= "AND A.dormitorios = $dormitorios ";
    }
    if ($banios != "") $sql.= "AND A.banios = $banios ";
    if ($cocheras != "") $sql.= "AND A.cocheras = $cocheras ";
    if ($maximo > 0) {
      $minimo = preg_replace("/[^0-9]/", "", $minimo);
      $maximo = preg_replace("/[^0-9]/", "", $maximo);
      $sql.= 'AND IF (A.moneda = "U$S",A.precio_final * '.$cotizacion.' >= '.$minimo.', A.precio_final >= '.$minimo.') ';
      $sql.= 'AND IF (A.moneda = "U$S",A.precio_final * '.$cotizacion.' <= '.$maximo.', A.precio_final <= '.$maximo.') ';
      //$sql.= "AND A.precio_final >= $minimo AND A.precio_final <= $maximo ";
    }
    if ($maximo_usd > 0) {
      $minimo_usd = preg_replace("/[^0-9]/", "", $minimo_usd);
      $maximo_usd = preg_replace("/[^0-9]/", "", $maximo_usd);
      $sql.= 'AND IF (A.moneda = "U$S",A.precio_final >= '.$minimo_usd.', A.precio_final / '.$cotizacion.'  >= '.$minimo_usd.') ';
      $sql.= 'AND IF (A.moneda = "U$S",A.precio_final <= '.$maximo_usd.', A.precio_final / '.$cotizacion.' <= '.$maximo_usd.') ';        
    }
    if (!empty($activo_desde)) $sql.= "AND A.fecha_ingreso >= '$activo_desde' ";
    if (!empty($valido_hasta)) $sql.= "AND A.valido_hasta >= '$valido_hasta' ";
    if (!empty($not_in_ids_localidades)) $sql.= "AND A.id_localidad NOT IN ($not_in_ids_localidades) ";

    // Filtros para alquileres temporarios
    if (!empty($personas)) $sql.= "AND capacidad_maxima >= $personas ";

    // Si tiene determinada etiqueta
    if (!empty($tiene_etiqueta_link)) {
      $sql.= "AND EXISTS (";
      $sql.= "  SELECT 1 FROM inm_propiedades_etiquetas PET ";
      $sql.= "  INNER JOIN inm_etiquetas INM_ET ON (PET.id_empresa = INM_ET.id_empresa AND PET.id_etiqueta = INM_ET.id) ";
      $sql.= "  WHERE PET.id_propiedad = A.id AND PET.id_empresa = A.id_empresa AND INM_ET.link = '$tiene_etiqueta_link' ";
      $sql.= ") ";
    }

    // Consultamos por propiedades especiales
    if ($tiene_cocheras == 1) $sql.= "AND A.cocheras > 0 ";
    if ($tiene_balcon == 1) $sql.= "AND A.balcon = 1 ";
    if ($tiene_patio == 1) $sql.= "AND A.patio = 1 ";
    if ($tiene_frente == 1) $sql.= "AND A.ubicacion_departamento = 'F' ";
    if ($tiene_contrafrente == 1) $sql.= "AND A.ubicacion_departamento = 'C' ";
    if ($tiene_interno == 1) $sql.= "AND A.ubicacion_departamento = 'I' ";
    if (!empty($calle)) $sql.= "AND A.calle = '$calle' ";

    $order_by_empresa = "IF(A.id_empresa = $this->id_empresa,0,1) ASC, ";

    if (!empty($order_by)) {
      $sql.= "ORDER BY $order_by_empresa $order_by ";
    } else {
      if ($order == 1) {
        $sql.= 'ORDER BY '.$order_by_empresa.' IF(A.moneda = "U$S",A.precio_final * '.$cotizacion.', A.precio_final) DESC ';
      } else if ($order == 2) {
        $sql.= 'ORDER BY '.$order_by_empresa.' IF(A.moneda = "U$S",A.precio_final * '.$cotizacion.', A.precio_final) ASC ';
      } else if ($order == 3) {
        $sql.= "ORDER BY $order_by_empresa RAND() ASC ";
      } else if ($order == 4) {
        $sql.= "ORDER BY $order_by_empresa A.destacado DESC, A.id DESC ";
      } else if ($order == 5) {
        $sql.= "ORDER BY $order_by_empresa A.id DESC ";
      } else if ($order == 6) {
        $sql.= "ORDER BY $order_by_empresa A.id ASC ";
      } else if ($order == 7) {
        $sql.= "ORDER BY $order_by_empresa A.fecha_publicacion DESC ";
      } else {
        $sql.= "ORDER BY $order_by_empresa A.id DESC ";
      }
    }

    if ($offset != 0) $sql.= "LIMIT $limit,$offset ";
    $this->sql = $sql;

    $q = mysqli_query($this->conx,$sql);
    if ($q === FALSE) {
      error_mail($this->sql);
      return array();
    }

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    if ($solo_contar == 1) {
      return $this->total;
    }

    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      //$r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
      $r->thumbnail = ((strpos($r->path,"http://")===FALSE)) ? ("/sistema/".substr($r->path,0,strrpos($r->path,"/"))."/thumb_".substr($r->path,strrpos($r->path,"/")+1)) : $r->path;
      $r->imagen = (empty($r->path)) ? "" : (((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path);
      $r->superficie_total = $r->superficie_cubierta + $r->superficie_descubierta + $r->superficie_semicubierta;
      $r->pertenece_red = ($this->id_empresa == $r->id_empresa) ? 0 : 1;

      // Utilizado cuando la empresa no es propia sino de la red
      $r->link_propiedad = (isset($r->pertenece_red) && $r->pertenece_red == 1) ? mklink($r->link)."?em=".$r->id_empresa : mklink($r->link);

      $r->disponibilidad = 1;
      if ($r->id_tipo_operacion == 3) {
        // Si es un alquiler temporal, calculamos los precios
        $r = $this->calcular_precios(array(
          "propiedad"=>$r,
          "desde"=>$desde,
          "hasta"=>$hasta,
          "personas"=>$personas,
          "moneda"=>$moneda,
        ));
        $r->disponibilidad = $this->calcular_disponibilidad(array(
          "propiedad"=>$r,
          "desde"=>$desde,
          "hasta"=>$hasta,
          "moneda"=>$moneda,
        ));
      }

      $r->etiquetas = array();
      if ($buscar_etiquetas == 1) {
        $sql = "SELECT PE.*, E.nombre, E.link FROM inm_propiedades_etiquetas PE ";
        $sql.= "INNER JOIN inm_etiquetas E ON (PE.id_empresa = E.id_empresa AND PE.id_etiqueta = E.id) ";
        $sql.= "WHERE PE.id_empresa = $this->id_empresa ";
        $sql.= "AND PE.id_propiedad = $r->id ";
        $q_etiq = mysqli_query($this->conx,$sql);
        while(($etiq=mysqli_fetch_object($q_etiq))!==NULL) {
          $r->etiquetas[] = $etiq;
        }
      }

      // Con esto evitamos que se este mostrando el precio en algun lado
      if ($r->publica_precio == 0) {
        $r->moneda = "";
        $r->precio_final = "Consultar";
      }

      $r = $this->encoding($r);
      $salida[] = $r;
    }
    return $salida;
  }

  function get_total_results() {
    return $this->total;
  }

  function get_tipos_operaciones($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    
    $empresas_compartida = $this->get_empresas_red();
    $empresas_compartida[] = $id_empresa;
    $emp_comp = implode(",", $empresas_compartida);

    $mostrar_todos = isset($config["mostrar_todos"]) ? $config["mostrar_todos"] : 0;
    if ($mostrar_todos == 0) {
      $sql = "SELECT DISTINCT L.nombre, L.link, L.id ";
      $sql.= "FROM inm_propiedades P ";
      $sql.= "INNER JOIN inm_tipos_operacion L ON (P.id_tipo_operacion = L.id) ";
      $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
      $sql.= "AND P.activo = 1 ";
      $sql.= "ORDER BY L.nombre ASC";      
    } else {
      $sql = "SELECT nombre, link, id FROM inm_tipos_operacion ";
      $sql.= "WHERE id_empresa IN ($emp_comp) ";
      $sql.= "ORDER BY orden ASC ";
    }
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }

  function get_localidades($config = array()) {
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 999999;
    $id_departamento = isset($config["id_departamento"]) ? $config["id_departamento"] : 0;

    $empresas_compartida = $this->get_empresas_red();
    $empresas_compartida[] = $this->id_empresa;
    $emp_comp = implode(",", $empresas_compartida);

    $sql = "SELECT DISTINCT L.id, L.nombre, L.link ";
    $sql.= "FROM inm_propiedades P ";
    $sql.= "INNER JOIN com_localidades L ON (P.id_localidad = L.id) ";
    $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
    $sql.= "AND P.activo = 1 ";
    if ($id_departamento) $sql.= "AND L.id_departamento = $id_departamento ";
    $sql.= "ORDER BY L.nombre ASC ";
    if ($offset != 0) $sql.= "LIMIT $limit,$offset ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }

  function get_departamentos($config = array()) {
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 999999;

    $empresas_compartida = $this->get_empresas_red();
    $empresas_compartida[] = $this->id_empresa;
    $emp_comp = implode(",", $empresas_compartida);

    $sql = "SELECT DISTINCT D.id, D.nombre ";
    $sql.= "FROM inm_propiedades P ";
    $sql.= "INNER JOIN com_localidades L ON (P.id_localidad = L.id) ";
    $sql.= "INNER JOIN com_departamentos D ON (L.id_departamento = D.id) ";
    $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
    $sql.= "AND P.activo = 1 ";
    $sql.= "ORDER BY D.nombre ASC ";
    if ($offset != 0) $sql.= "LIMIT $limit,$offset ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }

  function get_tipos_propiedades($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $empresas_compartida = $this->get_empresas_red();
    $empresas_compartida[] = $id_empresa;
    $emp_comp = implode(",", $empresas_compartida);

    $mostrar_todos = isset($config["mostrar_todos"]) ? $config["mostrar_todos"] : 0;
    if ($mostrar_todos == 0) {
      $sql = "SELECT DISTINCT T.nombre, T.id, T.link ";
      $sql.= "FROM inm_propiedades P ";
      $sql.= "INNER JOIN inm_tipos_inmueble T ON (P.id_tipo_inmueble = T.id) ";
      $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
      $sql.= "AND P.activo = 1 ";
      $sql.= "ORDER BY T.nombre ASC";
    } else {
      $sql = "SELECT nombre, link, id FROM inm_tipos_inmueble ";
      $sql.= "ORDER BY orden ASC ";
    }
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r->nombre = $this->encod($r->nombre);
      $salida[] = $r;
    }
    return $salida;
  }    

  function get_dormitorios($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $empresas_compartida = $this->get_empresas_red();
    $empresas_compartida[] = $id_empresa;
    $emp_comp = implode(",", $empresas_compartida);

    $sql = "SELECT DISTINCT P.dormitorios ";
    $sql.= "FROM inm_propiedades P ";
    $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
    $sql.= "AND P.activo = 1 ";
    $sql.= "ORDER BY P.dormitorios ASC";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }    

  function get_banios($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $empresas_compartida = $this->get_empresas_red();
    $empresas_compartida[] = $id_empresa;
    $emp_comp = implode(",", $empresas_compartida);

    $sql = "SELECT DISTINCT P.banios ";
    $sql.= "FROM inm_propiedades P ";
    $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
    $sql.= "AND P.activo = 1 ";
    $sql.= "ORDER BY P.banios ASC";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }    

  function get_cocheras($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $empresas_compartida = $this->get_empresas_red();
    $empresas_compartida[] = $id_empresa;
    $emp_comp = implode(",", $empresas_compartida);

    $sql = "SELECT DISTINCT P.cocheras ";
    $sql.= "FROM inm_propiedades P ";
    $sql.= "WHERE P.id_empresa IN ($emp_comp) ";
    $sql.= "AND P.activo = 1 ";
    $sql.= "ORDER BY P.cocheras ASC";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  } 

  /**
   * Obtiene las propiedades de un determinado propietario
   */
  function mis_propiedades($id_propietario, $config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["id_propietario"] = $id_propietario;
    return $this->get_list($config);
  }


  /**
   * Obtiene las propiedades destacadas
   */
  function destacadas($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["destacado"] = 1;
    return $this->get_list($config);
  }


  /**
   * Obtiene las ultimas propiedades
   */
  function ultimas($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    //$config["order"] = "A.fecha_ingreso DESC ";
    return $this->get_list($config);
  }


  function favoritos($config = array()) {
    if (!isset($_SESSION["favoritos"])) return array();
    $config["in"] = $_SESSION["favoritos"];
    if (empty($config["in"])) {
      return array();
    }
    $config["offset"] = 9999;
    return $this->get_list($config);
  }
}
?>