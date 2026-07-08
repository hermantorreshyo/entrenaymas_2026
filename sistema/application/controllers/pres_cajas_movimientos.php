<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pres_Cajas_Movimientos extends REST_Controller {

  private $total = 0;

  function __construct() {
    parent::__construct();
  }

  function insert() { echo json_encode(array()); }
  function delete() { echo json_encode(array()); }
  function update() { echo json_encode(array()); }
  function get() { echo json_encode(array()); }

  public function resumen_compras_arbol() {
    $id_empresa = parent::get_empresa();
    $id_sucursal = parent::get_post("id_sucursal",0);
    $this->load->helper("fecha_helper");
    $fecha_desde = fecha_mysql(parent::get_post("desde",date("d/m/Y")));
    $fecha_hasta = fecha_mysql(parent::get_post("hasta",date("d/m/Y")));
    
    $arr = $this->get_arbol(0,array(
      "id_sucursal"=>$id_sucursal,
      "id_empresa"=>$id_empresa,
      "desde"=>$fecha_desde,
      "hasta"=>$fecha_hasta,
    ));
    $salida = array(
      "total"=>sizeof($arr),
      "results"=>$arr,
    );        
    echo json_encode($salida);
  }

  public function get_arbol($id_padre = 0, $config = array()) {

    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : date("Y-m-d");
    $hasta = isset($config["hasta"]) ? $config["hasta"] : date("Y-m-d");

    $sql = "SELECT * FROM tipos_gastos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_padre = $id_padre ";
    $sql.= "ORDER BY orden ASC ";
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
      $a = $this->resumen_compras_por_concepto($row->id,array(
        "id_sucursal"=>$id_sucursal,
        "id_empresa"=>$id_empresa,
        "desde"=>$desde,
        "hasta"=>$hasta
      ));
      $e->total = $a["total"];
      $this->total = $this->total + $e->total;
      $e->children = $this->get_arbol($row->id,array(
        "id_sucursal"=>$id_sucursal,
        "id_empresa"=>$id_empresa,
        "desde"=>$desde,
        "hasta"=>$hasta
      ));
      $elementos[] = $e;
    }
    return $elementos;    
  }

  function resumen_compras_por_concepto($id_concepto, $config = array()) {

    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : date("Y-m-d");
    $hasta = isset($config["hasta"]) ? $config["hasta"] : date("Y-m-d");

    // Tomamos los hijos
    $sql = "SELECT * FROM tipos_gastos WHERE id_padre = $id_concepto AND id_empresa = $id_empresa";
    $q_hijos = $this->db->query($sql);
    $hijos = $q_hijos->result();

    // Calculamos el total de ese concepto
    $sql = "SELECT IF(SUM(monto) IS NULL,0,SUM(monto)) AS total ";
    $sql.= "FROM pres_cajas_movimientos ";
    $sql.= "WHERE fecha >= '$desde 00:00:00' ";
    $sql.= "AND fecha <= '$hasta 23:59:59' ";
    $sql.= "AND id_sucursal = $id_sucursal ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "AND id_concepto = $id_concepto ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      $total = $row->total;
    } else {
      $total = 0;
    }

    // Calculamos el total de todos los hijos
    foreach($hijos as $hijo) {
      $a = $this->resumen_compras_por_concepto($hijo->id,array(
        "id_sucursal"=>$id_sucursal,
        "id_empresa"=>$id_empresa,
        "desde"=>$desde,
        "hasta"=>$hasta
      ));
      $total = $total + (float) $a["total"];
    }
    return array(
      "total"=>((float)$total),
    );
  }

  function borrar_pago($id,$id_cuota=0,$id_sucursal = 0) {
    $id_empresa = parent::get_empresa();

    $this->load->model("Pres_Prestamo_Cuota_Model");
    $cuota = $this->Pres_Prestamo_Cuota_Model->get($id_cuota);

    $this->load->model("Pres_Prestamo_Model");
    $prestamo = $this->Pres_Prestamo_Model->get($cuota->id_prestamo);

    $this->load->model('Pres_Caja_Diaria_Model');
    $caja = $this->Pres_Caja_Diaria_Model->get($id);

    // Restamos los saldos
    $sql = "UPDATE pres_prestamos_cuotas SET ";
    $sql.= " saldo_interes = saldo_interes + $caja->cancelacion_interes, ";
    $sql.= " saldo_capital = saldo_capital + $caja->cancelacion_capital, ";
    $sql.= " monto_pagado = monto_pagado - $caja->cancelacion_capital, ";
    $sql.= " interes_pagado = interes_pagado - $caja->cancelacion_interes, ";
    $sql.= " fecha_pago = '0000-00-00', ";
    $sql.= " estado = 2, ";
    $sql.= " saldo = saldo_capital + saldo_interes ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_prestamo = $prestamo->id ";
    $sql.= "AND id = $cuota->id ";
    $this->db->query($sql);

    // Actualizamos la fecha de ultimo pago que tenga el credito (por las dudas de que se haya borrado el ultimo pago)
    $sql = "SELECT IF(MAX(fecha_pago) IS NULL,'0000-00-00',MAX(fecha_pago)) AS fecha_ultimo_pago ";
    $sql.= "FROM pres_prestamos_cuotas ";
    $sql.= "WHERE estado IN (1,2) AND id_prestamo = $prestamo->id ";
    $sql.= "AND id_empresa = $id_empresa ";
    $qq = $this->db->query($sql);
    $rr = $qq->row();
    $sql = "UPDATE pres_prestamos SET fecha_ultimo_pago = '$rr->fecha_ultimo_pago' ";
    $sql.= "WHERE id = '$prestamo->id' AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    // Eliminamos el movimiento de la caja
    $sql = "DELETE FROM pres_cajas_movimientos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id = $id ";
    $sql.= "AND id_prestamo = $prestamo->id ";
    $sql.= "AND id_sucursal = $id_sucursal ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0
    ));
  }

  function imprimir_pago($id,$id_cuota=0,$id_sucursal = 0) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    $this->load->model('Pres_Caja_Diaria_Model');
    $caja = $this->Pres_Caja_Diaria_Model->get($id);

    $this->load->model("Pres_Prestamo_Cuota_Model");
    $cuota = $this->Pres_Prestamo_Cuota_Model->get($id_cuota);

    $sucursal = FALSE;
    if ($id_sucursal != 0) {
      $this->load->model("Almacen_Model");
      $sucursal = $this->Almacen_Model->get($id_sucursal);
    }

    $this->load->model("Pres_Prestamo_Model");
    $prestamo = $this->Pres_Prestamo_Model->get($cuota->id_prestamo);
    $this->load->model("Pres_Cliente_Model");
    $cliente = $this->Pres_Cliente_Model->get($prestamo->id_cliente);
    $datos = array(
      "sucursal"=>$sucursal,
      "caja"=>$caja,
      "cuota"=>$cuota,
      "prestamo"=>$prestamo,
      "cliente"=>$cliente,
      "empresa"=>$empresa,
    );
    $this->load->view("reports/prestamo/pago_parcial.php",$datos);
  }


}