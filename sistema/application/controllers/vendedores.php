<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Vendedores extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Vendedor_Model', 'modelo');
  }

  function export($id_empresa = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM vendedores A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }  

  function comisiones($fecha_desde = "", $fecha_hasta = "") {
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    $salida = array();
    $vendedores = $this->modelo->get_all();

    // SIN VENDEDOR ASIGNADO
    $ven = new stdClass();
    $ven->id = 0;
    $ven->nombre = "Sin Vendedor";
    $ven->comision = 0;
    $vendedores[] = $ven;

    foreach($vendedores as $v) {
      $sql = "SELECT ";
      $sql.= " SUM(F.total * IF(TC.negativo = 1,-1,1)) AS total, ";
      $sql.= " COUNT(*) AS total_facturas, ";
      $sql.= " COUNT(DISTINCT id_cliente) AS total_clientes ";
      $sql.= "FROM facturas F ";
      $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql.= "WHERE F.id_vendedor = $v->id ";
      $sql.= "AND F.fecha >= '$fecha_desde' ";
      $sql.= "AND F.fecha <= '$fecha_hasta' ";
      $sql.= "AND F.anulada = 0 ";
      $sql.= "AND F.pendiente = 0 ";
      $sql.= "AND F.id_punto_venta != 0 ";
      $sql.= "AND F.id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $o = new stdClass(); // Se hace un nuevo objeto para poner los atributos en orden, para luego poder exportarlo directamente a excel y que las columnas queden ordenadas
      $o->vendedor = $v->nombre;
      $o->total_clientes = (!is_null($r->total_clientes)) ? $r->total_clientes : 0;
      $o->total_facturas = (!is_null($r->total_facturas)) ? $r->total_facturas : 0;
      $o->total = (!is_null($r->total)) ? $r->total : 0;
      $o->comision = (!is_null($v->comision)) ? $v->comision : 0;
      $o->total_comision = round($r->total * $v->comision / 100,2);
      $salida[] = $o;
    }
    echo json_encode(array(
      "datos"=>$salida
    ));
  }



  function comisiones_viajes($fecha_desde = "", $fecha_hasta = "") {
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    $salida = array();
    $vendedores = $this->modelo->get_all();

    // SIN VENDEDOR ASIGNADO
    $ven = new stdClass();
    $ven->id = 0;
    $ven->nombre = "Sin Vendedor";
    $ven->comision = 0;
    $vendedores[] = $ven;

    foreach($vendedores as $v) {
      $sql = "SELECT ";
      $sql.= " IF(SUM(total) IS NULL,0,SUM(total)) as total, ";
      $sql.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS total_facturas, ";
      $sql.= " IF(COUNT(DISTINCT id_cliente) IS NULL,0,COUNT(DISTINCT id_cliente)) AS total_clientes ";
      $sql.= "FROM via_reservas F ";
      $sql.= "WHERE F.id_vendedor = $v->id ";
      $sql.= "AND DATE_FORMAT(F.fecha_reserva,'%Y-%m-%d') >= '$fecha_desde' ";
      $sql.= "AND DATE_FORMAT(F.fecha_reserva,'%Y-%m-%d') <= '$fecha_hasta' ";
      $sql.= "AND F.id_tipo_estado = 0 ";
      $sql.= "AND F.id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $o = new stdClass(); // Se hace un nuevo objeto para poner los atributos en orden, para luego poder exportarlo directamente a excel y que las columnas queden ordenadas
      $o->vendedor = $v->nombre;
      $o->total_clientes = $r->total_clientes;
      $o->total_facturas = $r->total_facturas;
      $o->total = $r->total;
      $o->comision = $v->comision;
      $o->total_comision = round($r->total * $v->comision / 100,2);
      $salida[] = $o;
    }
    echo json_encode(array(
      "datos"=>$salida
    ));
  }

}