<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Caja extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Caja_Diaria_Model', 'modelo');
  }

  function listado_actualizadas() {
    $id_empresa = parent::get_empresa();
    $salida = array();
    $sql = "SELECT PV.*, S.nombre AS sucursal ";
    $sql.= "FROM puntos_venta PV ";
    $sql.= "INNER JOIN almacenes S ON (PV.id_sucursal = S.id AND PV.id_empresa = S.id_empresa) ";
    $sql.= "WHERE PV.id_empresa = $id_empresa ";
    $sql.= "ORDER BY S.nombre ASC, PV.numero ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "SELECT *, DATE_FORMAT(AC.fecha,'%d/%m/%Y %H:%i') AS fecha ";
      $sql.= "FROM actualizaciones_cajas AC ";
      $sql.= "WHERE AC.id_empresa = $id_empresa ";
      $sql.= "AND AC.id_punto_venta = $row->id ";
      $sql.= "ORDER BY AC.fecha DESC ";
      $sql.= "LIMIT 0,1 ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();  
        $row->fecha = $rr->fecha;
        $row->version_git = $rr->version_git;
        $row->version_db = $rr->version_db;
      } else {
        $row->fecha = "Sin datos";
        $row->version_git = "Sin datos";
        $row->version_db = "Sin datos";
      }
      $salida[] = $row;
    }
    echo json_encode(array(
      "results"=>$salida,
    ));
  }
  
  // Controla si la caja esta abierta o no
  function esta_abierta() {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM caja_diaria WHERE 1=1 ";
    $sql.= "AND estado = 'A' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $caja = $q->row();
      if ($caja->fecha == date("Y-m-d")) {
        echo json_encode(array(
          "abierta"=>1
        ));
      } else {
        echo json_encode(array(
          "abierta"=>0,
          "mensaje"=>"La caja quedo abierta del dia de ayer. Por favor cierre la misma y abra nuevamente.",
        ));
      }
    } else {
      echo json_encode(array(
        "abierta"=>0,
        "mensaje"=>"Antes de realizar una factura debe abrir la caja.",
      ));
    }
  }
  
  function ver_diaria() {
    
    $resultado = new stdClass();
    $resultado->efectivo = 0;
    $resultado->efectivo_inicial = 0;
    $resultado->tarjetas = 0;
    $resultado->cheques = 0;
    $resultado->efectivo_real = 0;
    $resultado->efectivo = 0;
    $resultado->salida_efectivo = 0;
    $resultado->salida_cheques = 0;
    //$resultado->gastos = 0;
    $resultado->estado = "X"; // No hay caja todavia
    
    $this->load->helper("fecha_helper");
    $fecha = $this->input->post("fecha");
    $fecha = fecha_mysql(str_replace("-","/",$fecha));
        
    $sql = "SELECT *, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM caja_diaria WHERE fecha = '$fecha' LIMIT 0,1 ";
    $q = $this->db->query($sql);

    $resultado->fecha = fecha_es($fecha);
    
    // El registro existe, hay que ver el estado
    if ($q->num_rows() > 0) {
      
      $resultado = $q->row();
      if ($resultado->estado == "A") {
        
        // Sumamos las entradas
        $sql = "SELECT ";
        $sql.= " SUM(IF(cta_cte < 0,efectivo-vuelto-cta_cte,efectivo-vuelto)) AS efectivo, ";
        $sql.= " SUM(tarjeta) AS tarjetas, ";
        $sql.= " SUM(cheque) AS cheques ";
        $sql.= "FROM facturas F ";
        $sql.= "WHERE 1=1 ";
        $sql.= "AND F.fecha = '$fecha' ";
        $q = $this->db->query($sql);
        $r = $q->row();
        $resultado->efectivo = (is_null($r->efectivo)) ? 0 : $r->efectivo;
        $resultado->tarjetas = (is_null($r->tarjetas)) ? 0 : $r->tarjetas;
        $resultado->cheques = (is_null($r->cheques)) ? 0 : $r->cheques;
        
        // Sumamos los gastos
        /*
        $sql = "SELECT IF(SUM(efectivo) IS NULL,0,SUM(efectivo)) AS gastos ";
        $sql.= "FROM gastos ";
        $sql.= "WHERE fecha = '$fecha' ";
        $qq = $this->db->query($sql);
        $r = $qq->row();
        $resultado->gastos = $r->gastos;
        */
      }
    }
    echo json_encode($resultado);
  }
  
  function resumen($fecha_desde = "",$fecha_hasta = "") {

    $resultado = array();
    $this->load->helper("fecha_helper");
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    
    $sql = "SELECT ";
    $sql.= " SUM(efectivo-vuelto) AS efectivo, ";
    $sql.= " SUM(IF(cta_cte > 0,cta_cte,0)) AS cta_cte, ";
    $sql.= " SUM(IF(cta_cte < 0,-cta_cte,0)) AS pagos, ";
    $sql.= " SUM(tarjeta) AS tarjeta, ";
    $sql.= " SUM(cheque) AS cheque ";
    $sql.= "FROM facturas F ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND F.fecha >= '$fecha_desde' ";
    $sql.= "AND F.fecha <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() <= 0) {
      $resultado = new stdClass;
      $resultado->efectivo = 0;
      $resultado->cta_cte = 0;
      $resultado->pagos = 0;
      $resultado->tarjeta = 0;
      $resultado->cheque = 0;
    } else {
      $r = $q->result();
      $resultado = $r[0];
    }
    
    // Sumamos los gastos
    $sql = "SELECT IF(SUM(efectivo) IS NULL,0,SUM(efectivo)) AS gastos ";
    $sql.= "FROM gastos WHERE 1=1 ";
    $sql.= "AND fecha >= '$fecha_desde' ";
    $sql.= "AND fecha <= '$fecha_hasta' ";
    $qq = $this->db->query($sql);
    $r = $qq->row();
    $resultado->gastos = $r->gastos;

    echo json_encode($resultado);
    
  }
  
}