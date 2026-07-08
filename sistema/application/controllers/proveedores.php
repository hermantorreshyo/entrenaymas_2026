<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Proveedores extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Proveedor_Model', 'modelo');
  }

  function totales_deuda() {

    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $fecha = fecha_mysql(parent::get_get("fecha",date("d/m/Y")));
    $excel = parent::get_get("excel",0);

    $totales_por_proveedor = array();
    $sucursales = array();
    $sql = "SELECT * FROM almacenes ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id != 25 ";
    $sql.= "ORDER BY nombre ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sucursales[] = $row;
      $totales_por_proveedor[] = 0;
    }
    $col_total = new stdClass();
    $col_total->id = 0;
    $col_total->nombre = "TOTAL";
    $sucursales[] = $col_total;

    $proveedores = array();
    $sql = "SELECT * FROM proveedores ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND tipo_proveedor = 1 ";
    $sql.= "ORDER BY nombre ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $prov) {

      $mostrar_proveedor = 0;
      $salida_sucursales = array();

      $total_sucursal = 0;
      for ($i=0; $i < sizeof($sucursales)-1; $i++) { 
        $suc = $sucursales[$i];
        $saldo = $this->modelo->saldo($prov->id,array(
          "fecha"=>$fecha,
          "id_sucursal"=>$suc->id,
          "incluir_dia"=>1,
          "estado"=>1,
        ));
        if ($id_empresa == 249 || $id_empresa == 868) {
          if ($mostrar_proveedor == 0) $mostrar_proveedor = (abs($saldo)>100) ? 1 : 0;
          $s = (float)((abs($saldo)>100) ? $saldo : 0);
        } else {
          $mostrar_proveedor = 1;
          $s = (float) $saldo;
        }
        $salida_sucursales[] = $s;
        $total_sucursal += $s;
        $totales_por_proveedor[$i] += $s;
      }
      $salida_sucursales[] = $total_sucursal; // Como ultimo ponemos la suma de la fila

      if ($mostrar_proveedor == 1) {
        $proveedores[] = array(
          "nombre"=>$prov->nombre,
          "sucursales"=>$salida_sucursales,
        );
      }
    }

    // Sumamos el total general
    $total_general = 0;
    for ($i=0; $i < sizeof($totales_por_proveedor); $i++) { 
      $tot = (float)$totales_por_proveedor[$i];
      $total_general += $tot;
    }
    $totales_por_proveedor[] = $total_general;

    // Como ultima fila ponemos los totales
    $proveedores[] = array(
      "nombre"=>"TOTALES",
      "sucursales"=>$totales_por_proveedor,
    );

    if ($excel == 1) {
      $encabezado = array();
      $encabezado[] = "Proveedor";
      foreach($sucursales as $s) {
        $ss = explode("-", $s->nombre);
        $ss = end($ss);
        $encabezado[] = trim($ss);
      }
      $datos = array();
      for($j=0;$j< sizeof($proveedores);$j++) {
        $p = $proveedores[$j];
        $fila = new stdClass();
        $fila->nombre = $p["nombre"];
        $i=1;
        foreach($p["sucursales"] as $s) {
          $fila->{"suc".$i} = $s;
          $i++;
        }
        $datos[] = $fila;
      }
      $this->load->library("Excel");
      $this->excel->create(array(
        "date"=>"",
        "filename"=>"Total de Deuda",
        "footer"=>array(),
        "header"=>$encabezado,
        "data"=>$datos,
        "title"=>"",
      ));  
    } else {
      echo json_encode(array(
        "proveedores"=>$proveedores,
        "sucursales"=>$sucursales,
      ));
    }
  }

  function next() {
    $id_empresa = parent::get_empresa();
    $q = $this->db->query("SELECT IF(MAX(CAST(codigo AS UNSIGNED)) IS NULL,0,MAX(CAST(codigo AS UNSIGNED))) AS codigo FROM proveedores WHERE id_empresa = $id_empresa");
    $r = $q->row();
    echo json_encode(array(
      "codigo"=>((int)$r->codigo + 1)
    ));
  }	

  function get_by_codigo() {
    $id_empresa = parent::get_empresa();
    $codigo = $this->input->get("codigo");
    $s = $this->modelo->get_by_codigo($codigo);
    echo json_encode($s);
  }

  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT P.*, ";
    $sql.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad ";		
    $sql.= "FROM proveedores P ";
    $sql.= " LEFT JOIN com_localidades L ON (P.id_localidad = L.id) ";
    $sql.= "WHERE P.nombre LIKE '%$nombre%' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->codigo;
      $rr->label = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  } 	


  function get($id) {
    $id_empresa = parent::get_empresa();
    	// Obtenemos todos los registros
    if ($id == "index") {
      $limit = ($this->input->get("limit") !== FALSE) ? $this->input->get("limit") : 0;
      $offset = ($this->input->get("offset") !== FALSE) ? $this->input->get("offset") : 0;
      $filter = ($this->input->get("term") !== FALSE) ? $this->input->get("term") : "";
      $order_by = ($this->input->get("order_by") !== FALSE) ? $this->input->get("order_by") : "";
      $order = ($this->input->get("order") !== FALSE) ? $this->input->get("order") : "";
      $order = (!empty($order_by)) ? ($order_by." ".$order) : "";
      $tipo_proveedor = $this->input->get("tipo_proveedor");			
      $salida = $this->modelo->buscar(array(
        "limit"=>$limit,
        "offset"=>$offset,
        "filter"=>$filter,
        "order"=>$order,
        "tipo_proveedor"=>$tipo_proveedor,
      ));
      echo json_encode($salida);
    } else {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }
  }

  function remove_properties($array) {
    unset($array->mensaje);
    unset($array->error);
    unset($array->localidad);		
  }


  function insert() {

    $id_empresa = parent::get_empresa();
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $this->remove_properties($array);

    // Controlamos si el codigo ya existe
    $array->codigo = trim($array->codigo);
    if (!empty($array->codigo)) {
      $q = $this->db->query("SELECT * FROM proveedores WHERE codigo = '$array->codigo' AND id_empresa = $id_empresa");
      if ($q->num_rows()>0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: Ya existe un proveedor con el codigo $array->codigo."
        ));
        return;
      }
    }

    // Controlamos si el CUIT ya existe
    $array->cuit = trim($array->cuit);
    if (!empty($array->cuit)) {
      //echo "SELECT * FROM proveedores WHERE cuit = '$array->cuit' AND id_empresa = $id_empresa"; exit();
      $q = $this->db->query("SELECT * FROM proveedores WHERE cuit = '$array->cuit' AND id_empresa = $id_empresa");
      if ($q->num_rows()>0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: El cuit es repetido con otro proveedor."
        ));
        return;
      }
    }		

    $array->fecha_alta = date("Y-m-d");
    $insert_id = $this->modelo->save($array);

    echo json_encode(array(
      "id"=>$insert_id,
      "error"=>0
    ));
  }

  function update($id) {

    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }

    $id_empresa = parent::get_empresa();
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $this->remove_properties($array);

    // Controlamos que el CUIT no exista
    $array->cuit = trim($array->cuit);
    if (!empty($array->cuit)) {
      $q = $this->db->query("SELECT * FROM proveedores WHERE cuit = '$array->cuit' AND id != $id AND id_empresa = $id_empresa");
      if ($q->num_rows()>0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: El cuit es repetido con otro proveedor."
          ));
        return;
      }
    }
    // Controlamos si el codigo ya existe
    $array->codigo = trim($array->codigo);
    if (!empty($array->codigo)) {
      $q = $this->db->query("SELECT * FROM proveedores WHERE codigo = '$array->codigo' AND id != $id AND id_empresa = $id_empresa");
      if ($q->num_rows()>0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: El codigo es repetido con otro proveedor."
        ));
        return;
      }
    }
    $this->modelo->save($array);
    echo json_encode(array(
      "id"=>$id,
      "error"=>0
    ));
  }    


  function unique_find_by_codigo($codigo = "") {
    if (empty($codigo)) {
      echo json_encode(array("results"=>array(),"total"=>0));
      return;
    }
    $id_empresa = parent::get_empresa();
    $sql = "SELECT P.id, P.nombre, P.codigo, P.cuit, P.direccion, ";
    $sql.= "P.id_localidad, P.id_provincia, P.id_tipo_iva, P.telefono, ";
    $sql.= "TI.descripcion AS tipo_iva ";
    $sql.= "FROM proveedores P ";
    $sql.= "INNER JOIN tipo_iva TI ON (P.id_tipo_iva = TI.id_iva) ";
    $sql.= "WHERE codigo = '$codigo' AND id_empresa = $id_empresa LIMIT 0,1";
    $query = $this->db->query($sql);
    if ($query->num_rows($query)>0) {
      $row = $query->row();
      echo json_encode(array("results"=>array($row),"total"=>1));
    } else {
      echo json_encode(array("results"=>array(),"total"=>0));
    }
  }

  function listado_deuda() {

    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $filtrar_en_cero = parent::get_get("filtrar_en_cero",0);
    $tipo_proveedor = ($this->input->get("tipo_proveedor") !== FALSE) ? $this->input->get("tipo_proveedor") : 0;
    $id_sucursal = ($this->input->get("id_sucursal") !== FALSE) ? $this->input->get("id_sucursal") : 0;
    $fecha_desde = ($this->input->get("fecha_desde") !== FALSE) ? fecha_mysql(str_replace("-","/",$this->input->get("fecha_desde"))) : "";
    $order_by = $this->input->get("order_by");
    $order_direction = $this->input->get("order");
    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $salida = $this->modelo->listado_deuda(array(
      "id_empresa"=>$id_empresa,
      "filtrar_en_cero"=>$filtrar_en_cero,
      "tipo_proveedor"=>$tipo_proveedor,
      "id_sucursal"=>$id_sucursal,
      "fecha_desde"=>$fecha_desde,
      "order_by"=>$order_by,
      "order"=>$order_direction,
      "estado"=>$estado,
    ));
    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
    ));
  }



  function listado_saldos($fecha = "", $id_empresa = 0) {

    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $fecha = $this->input->get("fecha");
    if ($fecha !== FALSE) $fecha = fecha_mysql(str_replace("-","/",$fecha));
    $filtrar_en_cero = $this->input->get("filtrar_en_cero");
    $tipo_proveedor = ($this->input->get("tipo_proveedor") !== FALSE) ? $this->input->get("tipo_proveedor") : 0;
    $id_sucursal = ($this->input->get("id_sucursal") !== FALSE) ? $this->input->get("id_sucursal") : 0;
    $fecha_desde = ($this->input->get("fecha_desde") !== FALSE) ? fecha_mysql(str_replace("-","/",$this->input->get("fecha_desde"))) : "";

    $order_by = $this->input->get("order_by");
    $order_direction = $this->input->get("order");

    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);

    $salida = array();
    $sql = "SELECT P.id, P.codigo, P.nombre FROM proveedores P WHERE P.id_empresa = $id_empresa ";
    $sql.= "AND NOT EXISTS (SELECT * FROM proveedores_relacionados PR WHERE P.id_empresa = PR.id_empresa AND PR.id_relacionado = P.id) ";
    if ($tipo_proveedor != 0) $sql.= "AND tipo_proveedor = $tipo_proveedor ";

    // TODO: ARREGLO PELUNCHO: SACAR EL PROVEEDOR NAZARENO
    //if ($id_empresa == 134) $sql.= "AND id != 51 ";

    if ($order_by != "saldo") $sql.= "ORDER BY ".$order_by." ".$order_direction;

    $q = $this->db->query($sql);
    foreach($q->result() as $r) {

      $r->saldo = $this->modelo->saldo($r->id,array(
        "fecha"=>$fecha,
        "fecha_desde"=>$fecha_desde,
        "estado"=>$estado,
        "id_sucursal"=>$id_sucursal,
        "incluir_dia"=>1,
      ));

      $r->total_pagos = 0;
      if (!empty($fecha_desde)) {
        // Calculamos los pagos efectuados al mes con ese proveedor
        $sql = "SELECT IF(SUM(C.total_general) IS NULL,0,SUM(C.total_general)) AS total ";
        $sql.= "FROM compras C ";
        $sql.= "WHERE C.id_proveedor = $r->id ";
        $sql.= "AND C.fecha <= '$fecha' "; // Que sea menor a la fecha que estamos buscando
        $sql.= "AND '$fecha_desde' <= C.fecha ";
        $sql.= "AND C.id_tipo_comprobante = -1 ";
        $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
        if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
        if ($estado == 0) $sql.= "AND C.estado = $estado ";
        // IMPORTANTE
        $sql.= "AND (C.compra_real = 1 OR C.ver_en_cuenta = 1) ";

        $qq = $this->db->query($sql);
        $rr = $qq->row();
        $r->total_pagos = $rr->total;
      }
      if ( (!(abs($r->saldo)<1 && ($filtrar_en_cero == 1))) || $r->total_pagos != 0) {
        $salida[] = $r;
      }
    }

    if ($order_by == "saldo") {
      if ($order_direction == "asc") usort($salida,array("Proveedores", "ordenar_saldos"));
      else usort($salida,array("Proveedores", "ordenar_saldos_desc"));
    }

    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
      ));
  }

  // Funciones utilizadas para ordenar por saldos
  static function ordenar_saldos($a,$b) {
    return ($a->saldo > $b->saldo) ? 1 : -1;
  }
  static function ordenar_saldos_desc($a,$b) {
    return ($a->saldo > $b->saldo) ? -1 : 1;
  }
  static function ordenar_ultima_compra($a,$b) {
    return ($a->ultima_compra_mysql > $b->ultima_compra_mysql) ? 1 : -1;
  }
  static function ordenar_ultima_compra_desc($a,$b) {
    return ($a->ultima_compra_mysql > $b->ultima_compra_mysql) ? -1 : 1;
  }
  static function ordenar_ultimo_pago($a,$b) {
    return ($a->ultimo_pago_mysql > $b->ultimo_pago_mysql) ? 1 : -1;
  }
  static function ordenar_ultimo_pago_desc($a,$b) {
    return ($a->ultimo_pago_mysql > $b->ultimo_pago_mysql) ? -1 : 1;
  }


  function cuentas_corrientes(
    $fecha_desde = "", $fecha_hasta = "",
    $codigo_proveedor = "", $id_empresa = 0, $id_proveedor = 0, $id_sucursal = 0) {

    $id_empresa = parent::get_empresa();
    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);

    $this->load->helper("fecha_helper");

    // Acomodamos los datos de entrada
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));

    // Calculamos el saldo inicial
    $data["saldo_inicial"] = $this->modelo->saldo($id_proveedor,array(
      "fecha"=>$fecha_desde,
      "id_sucursal"=>$id_sucursal,
    ));

    // Obtenemos los datos del proveedor
    $proveedor = $this->modelo->get($id_proveedor);
    if (sizeof($proveedor->relacionados)>0) {
      $ids = array($proveedor->id);
      foreach($proveedor->relacionados as $rel) {
        $ids[] = $rel->id;
      }
      $id_proveedor = implode(",",$ids);
    }

    // Obtenemos los registros que estan dentro del intervalo de fechas
    $sql = "SELECT CO.id, P.nombre, CO.id_empresa, CO.observaciones, ";
    $sql.= "IF(P.nombre IS NULL,'',P.nombre) AS proveedor, IF(P.id IS NULL,0,P.id) AS id_proveedor, ";
    $sql.= "IF(ISNULL(TC.nombre),'Orden de Pago',TC.nombre) AS tipo_comprobante, ";
    $sql.= "CO.total_iva, CO.total_neto, CO.cancelado, ";
    $sql.= "CO.id_proveedor, CO.id_sucursal, ";
    $sql.= "CO.compra_real, ";
    $sql.= "CO.efectivo, ";
    $sql.= "CO.id_tipo_comprobante, ";
    $sql.= "CO.numero_1, CO.numero_2, CO.total_general, CO.pago, ";
    $sql.= "CO.total_neto, CO.forma_pago, CO.pagada, ";
    $sql.= "CO.ret_ganancias, P.aplica_ret_ganancias, ";
    $sql.= "DATE_FORMAT(CO.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM compras CO ";
    $sql.= "LEFT JOIN proveedores P ON (CO.id_proveedor = P.id AND CO.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN tipos_comprobante TC ON (CO.id_tipo_comprobante = TC.id) ";
    $sql.= "WHERE CO.id_empresa = $id_empresa ";
    // IMPORTANTE
    $sql.= "AND (CO.compra_real = 1 OR CO.ver_en_cuenta = 1) ";
    
    if (!empty($id_proveedor)) $sql.= "AND CO.id_proveedor IN ($id_proveedor) ";
    if (!empty($codigo_proveedor)) $sql.= "AND P.codigo = '$codigo_proveedor' ";
    $sql.= "AND '$fecha_desde' <= CO.fecha AND CO.fecha <= '$fecha_hasta' ";
    if ($estado == 0) $sql.= "AND CO.estado = $estado ";
    if ($id_empresa != 0) $sql.= "AND CO.id_empresa = $id_empresa ";
    if ($id_sucursal != 0) $sql.= "AND CO.id_sucursal = $id_sucursal ";
    $sql.= "ORDER BY CO.fecha ASC, CO.id_tipo_comprobante DESC, CO.numero_1 ASC, CO.numero_2 ASC";
    $query = $this->db->query($sql);

    $salida = array();
    foreach($query->result() as $r) {
      $sql = "SELECT CD.* ";
      $sql.= "FROM cajas_movimientos CD ";
      $sql.= "INNER JOIN cajas CB ON (CD.id_empresa = CB.id_empresa AND CD.id_caja = CB.id) ";
      $sql.= "WHERE CD.id_empresa = $id_empresa ";
      $sql.= "AND CD.estado = 1 "; // Si hay algun movimiento pendiente
      $sql.= "AND CD.id_orden_pago = $r->id ";
      $qq = $this->db->query($sql);
      $r->pendiente = (($qq->num_rows() > 0) ? 1 : 0);
      $salida[] = $r;
    }

    $data["datos"] = $salida;

    // Imprimimos la salida
    echo json_encode($data);
  }


  function actualizar_padron() {

    $id_empresa = parent::get_empresa();
    set_time_limit(0);

    // Tomamos los cuits de los proveedores
    $query = $this->db->query("SELECT cuit FROM proveedores WHERE id_empresa = $id_empresa ");

    // Lo pasamos a un array;
    $proveedores = array();
    foreach($query->result() as $proveedor) {
      $proveedores[] = trim(str_replace("-","",$proveedor->cuit));
    }

    foreach (glob("uploads/padrones/PadronRGSRet*.txt") as $filename) {

      $handle = @fopen($filename, "r");
      if ($handle) {
        // Vamos tomando la linea del archivo
        while (($linea = fgets($handle)) !== FALSE) {
          $cuit = substr($linea,29,11);
          // Si el CUIT es de algun proveedor
          if (in_array($cuit,$proveedores)) {

            // Tomamos el porcentaje de retencion
            $porc = substr($linea,47,4);
            $porc = str_replace(",",".",$porc);

            // Ponemos los guiones al CUIT para buscarlo en la base de datos
            $nro_1 = substr($cuit,0,2);
            $nro_2 = substr($cuit,2,8);
            $nro_3 = substr($cuit,10,1);
            $cuit = $nro_1."-".$nro_2."-".$nro_3;

            // Tenemos que actualizar el porcentaje
            $sql = "UPDATE proveedores SET porc_ret_ib = $porc WHERE cuit = '$cuit' AND id_empresa = $id_empresa ";
            $this->db->query($sql);
          }
        }
        if (!feof($handle)) {
          // Enviamos el error
          echo "ERROR: No se puede abrir el archivo.";
          return;
        }
        fclose($handle);
      } else {
        // Enviamos el error
        echo "ERROR: No se puede abrir el archivo $filename.";
        return;
      }

      // Termino todo bien
      echo "OK: El padron se ha actualizado correctamente.";
      return;
    }

    echo "ERROR: No existe el archivo de padron en la carpeta.";
    return;
  }


  function imprimir() {
    $filter = $this->input->post("texto");
    $id_empresa = parent::get_empresa();
    $sql = "SELECT P.*, ";
    $sql.= "  IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql.= "  IF(L.codigo_postal IS NULL,'',L.codigo_postal) AS codigo_postal, ";
    $sql.= "  TI.descripcion AS tipo_iva ";
    $sql.= "FROM proveedores P ";
    $sql.= " LEFT JOIN com_localidades L ON (P.id_localidad = L.codigo) ";
    $sql.= " INNER JOIN tipo_iva TI ON (P.id_tipo_iva = TI.id_iva) ";
    $sql.= "WHERE activo=1 AND P.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND nombre LIKE '%$filter%' ";
    $sql.= "ORDER BY nombre ASC ";
    $q = $this->db->query($sql);
    $lista = $q->result();
    $this->load->view("reports/proveedores",array(
     "resultados" => $lista
    ));
  }    


  function deuda_por_sucursal() {

    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $id_proveedor = ($this->input->get("id_proveedor") !== FALSE) ? $this->input->get("id_proveedor") : 0;
    $fecha_desde = ($this->input->get("fecha_desde") !== FALSE) ? fecha_mysql(str_replace("-","/",$this->input->get("fecha_desde"))) : "";
    $fecha_hasta = ($this->input->get("fecha_hasta") !== FALSE) ? fecha_mysql(str_replace("-","/",$this->input->get("fecha_hasta"))) : "";

    $order_by = $this->input->get("order_by");
    $order_direction = $this->input->get("order");
    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $salida = array();
    $sql = "SELECT id, nombre FROM almacenes WHERE id_empresa = $id_empresa ";
    if ($order_by != "saldo" && $order_by != "ultima_compra" && $order_by != "ultimo_pago") $sql.= "ORDER BY ".$order_by." ".$order_direction;

    $q = $this->db->query($sql);
    foreach($q->result() as $r) {

      // Tomamos el saldo hasta el dia anterior
      $r->saldo = $this->modelo->saldo($id_proveedor,array(
        "fecha"=>$fecha_desde,
        "estado"=>$estado,
        "id_sucursal"=>$r->id,
        "incluir_dia"=>0,
      ));

      $r->total_pagos = 0;
      // Calculamos los pagos efectuados al mes con ese proveedor
      $sql = "SELECT IF(SUM(C.total_general) IS NULL,0,SUM(C.total_general)) AS total ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_proveedor = $id_proveedor ";
      $sql.= "AND C.fecha <= '$fecha_hasta' "; // Que sea menor a la fecha que estamos buscando
      $sql.= "AND '$fecha_desde' <= C.fecha ";
      $sql.= "AND C.id_tipo_comprobante = -1 ";
      $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
      $sql.= "AND C.id_sucursal = $r->id ";
      if ($estado == 0) $sql.= "AND C.estado = $estado ";
      // IMPORTANTE
      $sql.= "AND (C.compra_real = 1 OR C.ver_en_cuenta = 1) ";

      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $r->total_pagos = $rr->total;
      }

      $r->total_compras = 0;
      // Calculamos las compras realizadas a ese proveedor
      $sql = "SELECT SUM(C.total_general - C.pago) AS total ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_proveedor = $id_proveedor ";
      $sql.= "AND C.fecha <= '$fecha_hasta' "; // Que sea menor a la fecha que estamos buscando
      $sql.= "AND '$fecha_desde' <= C.fecha ";
      $sql.= "AND C.id_tipo_comprobante != -1 ";
      $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
      $sql.= "AND C.id_sucursal = $r->id ";
      // IMPORTANTE
      $sql.= "AND (C.compra_real = 1 OR C.ver_en_cuenta = 1) ";

      if ($estado == 0) $sql.= "AND C.estado = $estado ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $r->total_compras = $rr->total;        
      }

      // Ultimo pago
      $sql = "SELECT IF(MAX(C.fecha) IS NULL,'',DATE_FORMAT(MAX(C.fecha),'%d/%m/%Y')) AS ultimo_pago ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_proveedor = $id_proveedor ";
      //$sql.= "AND C.fecha <= '$fecha_hasta' "; // Que sea menor a la fecha que estamos buscando
      //$sql.= "AND '$fecha_desde' <= C.fecha ";
      $sql.= "AND C.id_tipo_comprobante = -1 ";
      $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
      $sql.= "AND C.id_sucursal = $r->id ";
      if ($estado == 0) $sql.= "AND C.estado = $estado ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $r->ultimo_pago = $rr->ultimo_pago;        
        $r->ultimo_pago_mysql = fecha_mysql($r->ultimo_pago);
      } else {
        $r->ultimo_pago = "";
        $r->ultimo_pago_mysql = "0000-00-00";
      }

      // Ultima compra
      $sql = "SELECT IF(MAX(C.fecha) IS NULL,'',DATE_FORMAT(MAX(C.fecha),'%d/%m/%Y')) AS ultima_compra ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_proveedor = $id_proveedor ";
      $sql.= "AND C.fecha <= '$fecha_hasta' "; // Que sea menor a la fecha que estamos buscando
      //$sql.= "AND '$fecha_desde' <= C.fecha ";
      $sql.= "AND C.id_tipo_comprobante != -1 ";
      $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
      $sql.= "AND C.id_sucursal = $r->id ";
      // IMPORTANTE
      $sql.= "AND (C.compra_real = 1 OR C.ver_en_cuenta = 1) ";

      if ($estado == 0) $sql.= "AND C.estado = $estado ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $r->ultima_compra = $rr->ultima_compra;        
        $r->ultima_compra_mysql = fecha_mysql($r->ultima_compra);
      } else {
        $r->ultima_compra = "";
        $r->ultima_compra_mysql = "0000-00-00";
      }

      if ( abs($r->saldo) > 1 || $r->total_pagos != 0 || $r->total_compras != 0 ) {
        $salida[] = $r;
      }
    }

    if ($order_by == "saldo") {
      if ($order_direction == "asc") usort($salida,array("Proveedores", "ordenar_saldos"));
      else usort($salida,array("Proveedores", "ordenar_saldos_desc"));
    } else if ($order_by == "ultima_compra") {
      if ($order_direction == "asc") usort($salida,array("Proveedores", "ordenar_ultima_compra"));
      else usort($salida,array("Proveedores", "ordenar_ultima_compra_desc"));      
    } else if ($order_by == "ultimo_pago") {
      if ($order_direction == "asc") usort($salida,array("Proveedores", "ordenar_ultimo_pago"));
      else usort($salida,array("Proveedores", "ordenar_ultimo_pago_desc"));      
    }

    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
    ));
  }

}