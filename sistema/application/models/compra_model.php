<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Compra_Model extends Abstract_Model {

  private $total = 0;
  
  function __construct() {
    parent::__construct("compras","id",'id_proveedor ASC');
  }

  function get_arbol($config = array()) {
    @session_start();
    $estado = isset($config["estado"]) ? $config["estado"] : ((!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0));
    $id_padre = isset($config["id_padre"]) ? $config["id_padre"] : 0;
    $movimiento = isset($config["movimiento"]) ? $config["movimiento"] : "";
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_razon_social = isset($config["id_razon_social"]) ? $config["id_razon_social"] : 0;
    $compra_real = isset($config["compra_real"]) ? $config["compra_real"] : 1;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    // Si estamos filtrando por movimiento, es porque es BLANCO
    if (!empty($movimiento)) $estado = 0;

    $sql = "SELECT * FROM tipos_gastos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_padre = $id_padre ";
    $sql.= "ORDER BY nombre ASC ";
    $query = $this->db->query($sql);
    $result = $query->result();
    $elementos = array();
    foreach($result as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $row->id_padre;
      $e->orden = $row->orden;
      $e->nombre = $row->nombre;
      $e->codigo = $row->codigo;
      $e->descripcion = $row->descripcion;
      $a = $this->resumen_compras_por_concepto(array(
        "id_concepto"=>$row->id,
        "movimiento"=>$movimiento,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_razon_social"=>$id_razon_social,
        "compra_real"=>$compra_real,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "estado"=>$estado,
      ));
      $e->total = $a["total"];
      $e->neto = $a["neto"];
      $e->iva = $a["iva"];
      $e->reg_especiales = $a["reg_especiales"];
      $this->total = $this->total + $e->total;
      $e->children = $this->get_arbol(array(
        "id_padre"=>$row->id,
        "movimiento"=>$movimiento,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_razon_social"=>$id_razon_social,
        "compra_real"=>$compra_real,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "estado"=>$estado,
      ));
      $elementos[] = $e;
    }
    return $elementos;  
  }

  function resumen_compras_por_concepto($config = array()) {
    @session_start();
    $estado = isset($config["estado"]) ? $config["estado"] : ((!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0));
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $movimiento = isset($config["movimiento"]) ? $config["movimiento"] : "";
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_razon_social = isset($config["id_razon_social"]) ? $config["id_razon_social"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $compra_real = isset($config["compra_real"]) ? $config["compra_real"] : 1;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    // Tomamos los hijos
    $sql = "SELECT * FROM tipos_gastos WHERE id_padre = $id_concepto AND id_empresa = $id_empresa";
    $q_hijos = $this->db->query($sql);
    $hijos = $q_hijos->result();

    // Calculamos el total de ese concepto
    $sql = "SELECT ";
    $sql.= "  SUM(CN.neto_dto) AS neto, ";
    $sql.= "  SUM(CN.iva) AS iva, ";
    $sql.= "  SUM(CN.iva) AS iva, ";
    $sql.= "  SUM(CN.neto_dto + CN.iva) AS total ";
    $sql.= "FROM compras C ";
    $sql.= "INNER JOIN compras_netos CN ON (C.id = CN.id_compra AND C.id_empresa = CN.id_empresa) ";
    $sql.= "INNER JOIN tipos_gastos CO ON (CN.id_concepto = CO.id AND CN.id_empresa = CO.id_empresa) ";
    if ($id_razon_social != 0) $sql.= "LEFT JOIN almacenes ALM ON (C.id_sucursal = ALM.id AND C.id_empresa = ALM.id_empresa) ";
    $sql.= "WHERE CN.id_concepto = $id_concepto ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    if ($compra_real != -1) $sql.= "AND C.compra_real = $compra_real ";
    if (!empty($desde)) $sql.= "AND C.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
    if (!empty($movimiento)) $sql.= "AND C.movimiento = '$movimiento' ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($id_razon_social != 0) $sql.= "AND ALM.id_razon_social = $id_razon_social ";
    if ($estado == 0) $sql.= "AND C.id_tipo_comprobante > 0 AND C.id_tipo_comprobante < 900 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      $total = (float) $row->total;
      $iva = (float) $row->iva;
      $neto = (float) $row->neto;
    } else {
      $total = 0;
      $neto = 0;
      $iva = 0;
    }

    // Total de Regimenes Especiales
    $sql = "SELECT IF(SUM(C.total_regimenes_especiales) IS NULL,0,SUM(C.total_regimenes_especiales)) AS total_regimenes_especiales ";
    $sql.= "FROM compras C ";
    if ($id_razon_social != 0) $sql.= "LEFT JOIN almacenes ALM ON (C.id_sucursal = ALM.id AND C.id_empresa = ALM.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    if ($compra_real != -1) $sql.= "AND C.compra_real = $compra_real ";
    if (!empty($desde)) $sql.= "AND C.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
    if (!empty($movimiento)) $sql.= "AND C.movimiento = '$movimiento' ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($id_razon_social != 0) $sql.= "AND ALM.id_razon_social = $id_razon_social ";
    if ($estado == 0) $sql.= "AND C.id_tipo_comprobante > 0 AND C.id_tipo_comprobante < 900 ";
    $sql.= "AND EXISTS (SELECT 1 FROM compras_netos CN WHERE C.id = CN.id_compra AND C.id_empresa = CN.id_empresa AND CN.id_concepto = $id_concepto) ";
    $qq = $this->db->query($sql);
    $rr = $qq->row();
    $reg_especiales = $rr->total_regimenes_especiales;

    // Calculamos el total de todos los hijos
    foreach($hijos as $hijo) {
      $a = $this->resumen_compras_por_concepto(array(
        "id_concepto"=>$hijo->id,
        "movimiento"=>$movimiento,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_razon_social"=>$id_razon_social,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "estado"=>$estado,
      ));
      $neto  = $neto  + (float) $a["neto"];
      $iva   = $iva   + (float) $a["iva"];
      $reg_especiales  = $reg_especiales  + (float) $a["reg_especiales"];
      $total = $total + (float) $a["total"];
    }
    return array(
      "total"=>$total,
      "neto"=>$neto,
      "iva"=>$iva,
      "reg_especiales"=>$reg_especiales,
    );
  }  

  function get_total($conf = array()) {

    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $estado = isset($conf["estado"]) ? $conf["estado"] : 0;
    $movimiento = isset($conf["movimiento"]) ? $conf["movimiento"] : "";
    $ids_conceptos = isset($conf["ids_conceptos"]) ? $conf["ids_conceptos"] : "";
    $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
    $id_razon_social = isset($config["id_razon_social"]) ? $config["id_razon_social"] : 0;
    $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
    $compra_real = isset($conf["compra_real"]) ? $conf["compra_real"] : -1;

    $sql = "SELECT SUM(C.total_general) AS total ";
    $sql.= "FROM compras C ";
    if ($id_razon_social != 0) $sql.= "LEFT JOIN almacenes ALM ON (C.id_sucursal = ALM.id AND C.id_empresa = ALM.id_empresa) ";
    if (!empty($tipo_proveedor)) $sql.= " INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "AND C.movimiento = '$movimiento' ";
    $sql.= "AND C.id_tipo_comprobante > 0 ";
    if (!empty($ids_conceptos)) $sql.= "AND EXISTS (SELECT * FROM compras_netos CN WHERE CN.id_compra = C.id AND CN.id_empresa = C.id_empresa AND CN.id_concepto IN ($ids_conceptos)) ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($id_razon_social != 0) $sql.= "AND ALM.id_razon_social = $id_razon_social ";
    if ($id_usuario != 0) $sql.= "AND C.id_usuario = $id_usuario ";
    if ($compra_real != -1) $sql.= "AND C.compra_real = $compra_real ";

    // TODO: ARREGLO PELUNCHO: SACAR EL PROVEEDOR NAZARENO
    if ($id_empresa == 134) $sql.= "AND C.id_proveedor != 51 ";

    if ($estado == 0) $sql.= "AND C.id_tipo_comprobante < 900 ";
    $q = $this->db->query($sql);
    $row = $q->row();
    if (is_null($row->total)) return 0;
    else return (float)$row->total;
  }

  // Total de compras pagadas en efectivo
  function get_total_efectivo($conf = array()) {

  $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
  $estado = isset($conf["estado"]) ? $conf["estado"] : 0;
  $movimiento = isset($conf["movimiento"]) ? $conf["movimiento"] : "";
  $ids_conceptos = isset($conf["ids_conceptos"]) ? $conf["ids_conceptos"] : "";
  $tipo_proveedor = isset($conf["tipo_proveedor"]) ? $conf["tipo_proveedor"] : "";
  $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
  $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
  $compra_real = isset($conf["compra_real"]) ? $conf["compra_real"] : -1;

  $sql = "SELECT SUM(C.total_general) AS total ";
  $sql.= "FROM compras C ";
  if (!empty($tipo_proveedor)) $sql.= " INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
  $sql.= "WHERE C.id_empresa = $id_empresa ";
  $sql.= "AND C.movimiento = '$movimiento' ";
  $sql.= "AND C.forma_pago = 'E' ";
  $sql.= "AND C.id_tipo_comprobante > 0 ";
  if (!empty($ids_conceptos)) $sql.= "AND EXISTS (SELECT * FROM compras_netos CN WHERE CN.id_compra = C.id AND CN.id_empresa = C.id_empresa AND CN.id_concepto IN ($ids_conceptos)) ";
  if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
  if ($id_usuario != 0) $sql.= "AND C.id_usuario = $id_usuario ";
  if ($compra_real != -1) $sql.= "AND C.compra_real = $compra_real ";

  // TODO: ARREGLO PELUNCHO: SACAR EL PROVEEDOR NAZARENO
  if ($id_empresa == 134) $sql.= "AND C.id_proveedor != 51 ";

  if ($estado == 0) $sql.= "AND C.id_tipo_comprobante < 900 ";
  $q = $this->db->query($sql);
  $row = $q->row();
  $total = (is_null($row->total)) ? 0 : ((float)$row->total);


  // Ordenes de pago que tienen efectivo


  return $total;
  }

  /*
  function get_total_pagado($conf = array()) {

  $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
  $estado = isset($conf["estado"]) ? $conf["estado"] : 0;
  $movimiento = isset($conf["movimiento"]) ? $conf["movimiento"] : "";
  $tipo_proveedor = isset($conf["tipo_proveedor"]) ? $conf["tipo_proveedor"] : "";

  $sql = "SELECT SUM(C.total_general) AS total ";
  $sql.= "FROM compras C ";
  if (!empty($tipo_proveedor)) $sql.= " INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
  $sql.= "WHERE C.id_empresa = $id_empresa ";
  $sql.= "AND C.movimiento = '$movimiento' ";
  $sql.= "AND C.id_tipo_comprobante > 0 ";

  // TODO: ARREGLO PELUNCHO: SACAR EL PROVEEDOR NAZARENO
  //if ($id_empresa == 134) $sql.= "AND id_proveedor != 51 ";

  if (!empty($tipo_proveedor)) $sql.= "AND P.tipo_proveedor $tipo_proveedor "; // Ya viene con el = o !=
  if ($estado == 0) $sql.= "AND C.id_tipo_comprobante < 900 ";
  $q = $this->db->query($sql);
  $row = $q->row();
  if (is_null($row->total)) return 0;
  else return (float)$row->total;
  }
  */


  function listado($conf = array()) {

  $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
  $desde = isset($conf["desde"]) ? $conf["desde"] : "";
  $hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
  $filter = isset($conf["filter"]) ? $conf["filter"] : "";
  $movimiento = isset($conf["movimiento"]) ? $conf["movimiento"] : "";
  $tipos_comprobante = isset($conf["tc"]) ? $conf["tc"] : "";
  $id_proveedor = isset($conf["id_proveedor"]) ? $conf["id_proveedor"] : 0;
  $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
  $tipo_proveedor = (isset($config["tipo_proveedor"])) ? $config["tipo_proveedor"] : -1;
  $ids_conceptos = isset($conf["ids_conceptos"]) ? $conf["ids_conceptos"] : 0;
  $numero = isset($conf["numero"]) ? $conf["numero"] : "";
  $forma_pago = isset($conf["forma_pago"]) ? $conf["forma_pago"] : "";
  $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
  $offset = isset($conf["offset"]) ? $conf["offset"] : 30;
  $estado = isset($conf["estado"]) ? $conf["estado"] : 0;
  $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
  $compra_real = isset($conf["compra_real"]) ? $conf["compra_real"] : -1;

  $sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
  $sql.= " IF(C.fecha='0000-00-00','',DATE_FORMAT(C.fecha,'%d/%m/%Y')) AS fecha, ";
  $sql.= " IF(P.nombre IS NULL,'',P.nombre) AS proveedor, ";
  $sql.= " IF(SUC.nombre IS NULL,'',SUC.nombre) AS sucursal, ";
  $sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, ";
  $sql.= " CONCAT(IF(TC.letra IS NULL,'X',TC.letra),' - ',C.numero_1,' ',C.numero_2) as comprobante ";
  $sql.= "FROM compras C ";
  $sql.= "LEFT JOIN almacenes SUC ON (C.id_sucursal = SUC.id AND C.id_empresa = SUC.id_empresa) ";
  $sql.= "LEFT JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
  $sql.= "LEFT JOIN tipos_comprobante TC ON (C.id_tipo_comprobante = TC.id) ";
  $sql.= "WHERE C.id_empresa = $id_empresa ";
  $sql.= "AND C.id_tipo_comprobante != -1 ";
  if ($estado == 0) $sql.= "AND C.id_tipo_comprobante < 900 ";
  if ($tipo_proveedor != -1) $sql.= "AND P.tipo_proveedor = $tipo_proveedor ";
  if (!empty($desde)) $sql.= "AND C.fecha >= '$desde' ";
  if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
  if (!empty($forma_pago)) $sql.= "AND C.forma_pago = '$forma_pago' ";
  if (!empty($id_proveedor)) $sql.= "AND C.id_proveedor = $id_proveedor ";
  if (!empty($numero)) $sql.= "AND C.numero_2 LIKE '%$numero%' ";
  if (!empty($filter)) $sql.= "AND (P.nombre LIKE '%$filter%' OR C.numero_2 LIKE '%$filter') ";
  if (!empty($movimiento)) $sql.= "AND C.movimiento = '$movimiento' ";
  if (!empty($tipos_comprobante)) $sql.= "AND C.id_tipo_comprobante IN ($tipos_comprobante) ";
  if (!empty($ids_conceptos)) {
    $sql.= "AND EXISTS (SELECT * FROM compras_netos CN WHERE CN.id_empresa = C.id_empresa AND CN.id_compra = C.id AND CN.id_concepto IN ($ids_conceptos) ) ";
  }
  if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
  if ($id_usuario != 0) $sql.= "AND C.id_usuario = $id_usuario ";
  if ($compra_real != -1) $sql.= "AND C.compra_real = $compra_real ";
  $sql.= "ORDER BY C.fecha DESC, C.id DESC ";
  if (!empty($offset)) $sql.= "LIMIT $limit,$offset ";
  $q = $this->db->query($sql);
  $lista = $q->result();
  
  $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
  $total = $q_total->row();
  return array(
    "total"=> $total->total,
    "results"=>$lista,
  );
  }

  function find($filter) {
  $id_empresa = parent::get_empresa();
  $this->db->where("id_empresa = $id_empresa");
  $this->db->like("nombre",$filter);
  $query = $this->db->get($this->tabla);
  $result = $query->result();
  $this->db->close();
  return $result;
  }
  
  function delete($id) {
  $id_empresa = parent::get_empresa();
  $this->db->trans_start();
  $this->db->delete("cajas_movimientos",array("id_orden_pago"=>$id,"id_empresa"=>$id_empresa));
  $this->db->delete("compras_depositos",array("id_orden_pago"=>$id,"id_empresa"=>$id_empresa));
  $this->db->delete("compras_pagos",array("id_pago"=>$id,"id_empresa"=>$id_empresa));
  $this->db->delete("compras_netos",array("id_compra"=>$id,"id_empresa"=>$id_empresa));
  $this->db->delete("compras",array("id"=>$id,"id_empresa"=>$id_empresa));
  $this->db->trans_complete();
  $this->db->close();
  }  
  
  function update($id,$data) {
  $this->db->trans_start();
  
  // Guardamos los netos en un auxiliar
  $netos = $data->netos;
  unset($data->netos);
  
  // Eliminamos toda la informacion que no se persiste
  unset($data->codigo_proveedor);
  unset($data->nombre_proveedor);
  unset($data->cuit_proveedor);
  unset($data->direccion_proveedor);
  unset($data->porc_ret_ib);
  unset($data->aplica_ret_ganancias);
  unset($data->tipo_iva_proveedor);
  unset($data->error);
  unset($data->mensaje);
  unset($data->sucursal);

  // El ID_USUARIO no lo guardamos, para mantener siempre el usuario que lo creo
  unset($data->id_usuario);
    
  // Modificamos la fecha
  $this->load->helper("fecha_helper");
  $data->fecha = fecha_mysql($data->fecha);
  
  // Dependiendo de si es una nota de credito
  // u otro tipo de comprobante
  if ($data->id_tipo_comprobante == 3 || $data->id_tipo_comprobante == 8 || $data->id_tipo_comprobante == 13 || $data->id_tipo_comprobante == 21 || $data->id_tipo_comprobante == 53 || $data->id_tipo_comprobante == 203 || $data->id_tipo_comprobante == 208) {
    $data->total_general = -$data->total_general;
    $data->total_neto = -$data->total_neto;
    $data->perc_ing_brutos = -$data->perc_ing_brutos;
    $data->perc_iva = -$data->perc_iva;
    $data->perc_agip = -$data->perc_agip;
    $data->perc_san_luis = -$data->perc_san_luis;
    $data->impuesto_interno = -$data->impuesto_interno;
    $data->no_gravado = -$data->no_gravado;
    $data->exento = -$data->exento;
    $data->total_iva = -$data->total_iva;
    $data->subtotal = -$data->subtotal;
    $data->total_regimenes_especiales = -$data->total_regimenes_especiales;
  }
    
  // Cargamos la factura de compra
  $this->db->where("id",$id);
  $this->db->where("id_empresa",$data->id_empresa);
  $this->db->update("compras",$data);
  
  // Eliminamos los netos ingresados
  $this->db->delete("compras_netos",array(
    "id_compra"=>$id,
    "id_empresa"=>$data->id_empresa,
  ));
    
  // Lo volvemos a ingresar
  foreach($netos as $neto) {
    unset($neto->id);
  $neto->id_empresa = $data->id_empresa;
    // Dependiendo si es una nota de credito u otro tipo de comprobante
    if ($data->id_tipo_comprobante == 3 || $data->id_tipo_comprobante == 8 || $data->id_tipo_comprobante == 13 || $data->id_tipo_comprobante == 21 || $data->id_tipo_comprobante == 53 || $data->id_tipo_comprobante == 203 || $data->id_tipo_comprobante == 208) {
  $neto->neto = -$neto->neto;
  $neto->porc_dto = -$neto->porc_dto;
  $neto->neto_dto = -$neto->neto_dto;
  $neto->porc_iva = -$neto->porc_iva;
  $neto->iva = -$neto->iva;
    }
    $neto->id_compra = $id;
    $this->db->insert("compras_netos",$neto);
  }
  $this->db->trans_complete();
  $this->db->close();
  }  
  
  
  function insert($data) {
  $this->db->trans_start();
  
  // Guardamos los netos en un auxiliar
  $netos = $data->netos;
  unset($data->netos);
  
  // Eliminamos toda la informacion que no se persiste
  unset($data->codigo_proveedor);
  unset($data->nombre_proveedor);
  unset($data->direccion_proveedor);
  unset($data->cuit_proveedor);
  unset($data->tipo_iva_proveedor);
  unset($data->porc_ret_ib);
  unset($data->aplica_ret_ganancias);
  unset($data->error);
  unset($data->mensaje);
  unset($data->sucursal);
  
  // Modificamos la fecha
  $this->load->helper("fecha_helper");
  $data->fecha = fecha_mysql($data->fecha);
  
  // Dependiendo de si es una nota de credito
  // u otro tipo de comprobante
  if ($data->id_tipo_comprobante == 3 || $data->id_tipo_comprobante == 8 || $data->id_tipo_comprobante == 13 || $data->id_tipo_comprobante == 21 || $data->id_tipo_comprobante == 53 || $data->id_tipo_comprobante == 203 || $data->id_tipo_comprobante == 208) {
    $data->total_general = -$data->total_general;
    $data->total_neto = -$data->total_neto;
    $data->perc_ing_brutos = -$data->perc_ing_brutos;
    $data->perc_iva = -$data->perc_iva;
    $data->perc_agip = -$data->perc_agip;
    $data->perc_san_luis = -$data->perc_san_luis;
    $data->impuesto_interno = -$data->impuesto_interno;
    $data->no_gravado = -$data->no_gravado;
    $data->exento = -$data->exento;
    $data->total_iva = -$data->total_iva;
    $data->subtotal = -$data->subtotal;
    $data->total_regimenes_especiales = -$data->total_regimenes_especiales;
  }
  
  // Cargamos la factura de compra
  $this->db->insert("compras",$data);
  $id = $this->db->insert_id();
  
  // Cargamos los netos
  foreach($netos as $neto) {
    unset($neto->id);
    $neto->id_compra = $id;
  $neto->id_empresa = $data->id_empresa;
      
    // Dependiendo si es una nota de credito u otro tipo de comprobante
    if ($data->id_tipo_comprobante == 3 || $data->id_tipo_comprobante == 8 || $data->id_tipo_comprobante == 13 || $data->id_tipo_comprobante == 21 || $data->id_tipo_comprobante == 53 || $data->id_tipo_comprobante == 203 || $data->id_tipo_comprobante == 208) {
  $neto->neto = -$neto->neto;
  $neto->porc_dto = -$neto->porc_dto;
  $neto->neto_dto = -$neto->neto_dto;
  $neto->porc_iva = -$neto->porc_iva;
  $neto->iva = -$neto->iva;
    }
      
    $this->db->insert("compras_netos",$neto);
  }
  
  // Si la forma de pago es efectivo,
  // se debe crear una orden de pago automatica
  // por el monto de esa factura
  /*if ($data->forma_pago == "E") {
    
  
  }*/
    
  $this->db->trans_complete();
  $this->db->close();
  if (!isset($id)) return -1;
  else return $id;
  }
  
  function get($id) {
  
  // Obtenemos la compra
  $id_empresa = parent::get_empresa();
  $query = $this->db->get_where("compras",array("id"=>$id,"id_empresa"=>$id_empresa));
  $row = $query->row();
  
  // Acomodamos los datos de la compra
  $this->load->helper("fecha_helper");
  $row->fecha = fecha_es($row->fecha);
  
  // Obtenemos los datos del proveedor
  $sql = "SELECT P.*, TI.nombre AS tipo_iva_proveedor ";
  $sql.= "FROM proveedores P ";
  $sql.= "LEFT JOIN tipos_iva TI ON (P.id_tipo_iva = TI.id) ";
  $sql.= "WHERE P.id = $row->id_proveedor AND P.id_empresa = $id_empresa ";
  $q_prov = $this->db->query($sql);
  $proveedor = $q_prov->row();
  $row->codigo_proveedor = $proveedor->codigo;
  $row->nombre_proveedor = $proveedor->nombre;
  $row->cuit_proveedor = $proveedor->cuit;
  $row->tipo_iva_proveedor = $proveedor->tipo_iva_proveedor;
    
  // Obtenemos el array de netos
  $sql = "SELECT * FROM compras_netos WHERE id_compra = $id AND id_empresa = $id_empresa ";
  $q_netos = $this->db->query($sql);
  $netos = array();
  $i=0;
  foreach($q_netos->result() as $neto) {
    $neto->id = $i;
    $netos[] = $neto;
    $i++;
  }
  $row->netos = $netos;
  
  $this->db->close();
  return $row;
  }

}