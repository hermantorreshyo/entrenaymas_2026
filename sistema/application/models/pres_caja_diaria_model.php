<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pres_Caja_Diaria_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("pres_cajas_movimientos","id","fecha DESC");
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    //$this->db->query("DELETE FROM pres_cajas_movimientos WHERE id = $id AND id_empresa = $id_empresa");
  }
  
  function get($id,$id_empresa = 0) {
    if (empty($id)) return FALSE;
    if ($id_empresa == 0) $id_empresa = parent::get_empresa();
    $sql = "SELECT P.*, PC.nombre AS concepto, ";
    $sql.= " IF(P.fecha = '0000-00-00 00:00:00','',DATE_FORMAT(P.fecha,'%d/%m/%Y %H:%i')) AS fecha ";
    $sql.= "FROM pres_cajas_movimientos P ";
    $sql.= "INNER JOIN tipos_gastos PC ON (P.id_concepto = PC.id AND P.id_empresa = PC.id_empresa) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    $sql.= "AND P.id = $id ";
    $query = $this->db->query($sql);
    $row = $query->row(); 
    return $row;
  }

  function insert($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    $id_prestamo = parent::insert($data);
    return $id_prestamo;
  }

  function update($id,$data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    parent::update($id,$data);
    return $id;
  }

  function calcular_saldo($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";

    // Sumamos las entradas
    $sql = "SELECT IF(SUM(monto-descuento) IS NULL,0,SUM(monto-descuento)) AS total ";
    $sql.= "FROM pres_cajas_movimientos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND tipo = 'E' ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
    if (!empty($desde)) $sql.= "AND fecha > '$desde' ";
    if (!empty($hasta)) $sql.= "AND fecha < '$hasta' ";
    $q = $this->db->query($sql);
    $entrada = $q->row();

    // Sumamos las salidas
    $sql = "SELECT IF(SUM(monto-descuento) IS NULL,0,SUM(monto-descuento)) AS total ";
    $sql.= "FROM pres_cajas_movimientos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND tipo = 'S' ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
    if (!empty($desde)) $sql.= "AND fecha > '$desde' ";
    if (!empty($hasta)) $sql.= "AND fecha < '$hasta' ";
    $q = $this->db->query($sql);
    $salida = $q->row();

    return ($entrada->total - $salida->total);
  }

  function buscar($config = array()) {

    $this->load->helper("fecha_helper");
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;

    // Calculamos el saldo incial

    $sql = "SELECT SQL_CALC_FOUND_ROWS P.*, PC.nombre AS concepto, ";
    $sql.= " IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= " IF(P.fecha = '0000-00-00 00:00:00','',DATE_FORMAT(P.fecha,'%d/%m/%Y %H:%i')) AS fecha ";
    $sql.= "FROM pres_cajas_movimientos P ";
    $sql.= "INNER JOIN tipos_gastos PC ON (P.id_concepto = PC.id AND P.id_empresa = PC.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios U ON (P.id_empresa = U.id_empresa AND P.id_usuario = U.id) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    if (!empty($id_cliente)) $sql.= "AND P.id_cliente = $id_cliente ";
    if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
    if (!empty($id_concepto)) $sql.= "AND P.id_concepto = $id_concepto ";
    if (!empty($desde)) $sql.= "AND P.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND P.fecha <= '$hasta' ";
    $sql.= "ORDER BY fecha ASC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);

    $salida = array();
    foreach($q->result() as $row) {

      $row->estado_facturacion = 0; // No es necesario facturar
      
      if ($row->id_cuota != 0 && $row->id_prestamo != 0) {
        $sql = "SELECT * FROM pres_prestamos_cuotas ";
        $sql.= "WHERE id = $row->id_cuota AND id_prestamo = $row->id_prestamo ";
        $sql.= "AND id_empresa = $row->id_empresa ";
        $q_cuota = $this->db->query($sql);
        $cuota = $q_cuota->row();
        if ($cuota->id_factura != 0) {
          $row->estado_facturacion = 2; // Ya fue facturada esa cuota
        } else {
          // Consultamos las cuotas anteriores se hizo alguna factura
          $sql = "SELECT * FROM pres_prestamos_cuotas ";
          $sql.= "WHERE id_prestamo = $row->id_prestamo ";
          $sql.= "AND numero < $cuota->numero ";
          $sql.= "AND id_empresa = $row->id_empresa ";
          $sql.= "ORDER BY numero ASC ";
          $row->sql = $sql;
          $q_cuotas = $this->db->query($sql);
          foreach($q_cuotas->result() as $cc) {
            if ($cc->id_factura != 0) {
              $row->estado_facturacion = 1; // Tiene que ser facturada, porque una anterior fue facturada
              break;
            }
          }
        }
      }

      $salida[] = $row;
    }
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    if ($id_concepto == 0) {
      $saldo_inicial = $this->calcular_saldo(array(
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "hasta"=>$desde,
      ));
    } else {
      // Si se filtra por concepto los saldos no se toman en cuenta
      $saldo_inicial = 0;
    }

    return array(
      "results"=>$salida,
      "total"=>$total->total,
      "meta"=>array(
        "saldo_inicial"=>$saldo_inicial,
      ),
    );
  }

}