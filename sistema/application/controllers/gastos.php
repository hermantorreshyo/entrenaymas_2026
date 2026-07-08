<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Gastos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Gasto_Model', 'modelo');
  }

  // Utilizado en la CAJA DIARIA
  function listado() {
    $this->load->helper("fecha_helper");
    $id_caja_diaria = $this->get_post("id_caja_diaria",0);
    $id_punto_venta = $this->get_post("id_punto_venta",0);
    $fecha = $this->get_post("fecha");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $fecha_hasta = $this->get_post("fecha_hasta");
    if (!empty($fecha_hasta)) $fecha_hasta = fecha_mysql($fecha_hasta);

    $sql = "SELECT G.*, DATE_FORMAT(G.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= "IF (G.id_tipo_gasto = 0,'Sin especificar',TG.nombre) AS tipo_gasto, ";
    $sql.= "IF (G.id_proveedor = 0,'Sin especificar',P.nombre) AS proveedor ";
    $sql.= "FROM gastos G ";
    $sql.= "LEFT JOIN tipos_gastos TG ON (TG.id = G.id_tipo_gasto) ";
    $sql.= "LEFT JOIN proveedores P ON (G.id_proveedor = P.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND G.id_caja_diaria = $id_caja_diaria ";
    if (!empty($id_punto_venta)) $sql.= "AND G.id_punto_venta = $id_punto_venta ";
    if (!empty($fecha) && empty($fecha_hasta)) {
      $sql.= "AND fecha = '$fecha' ";
    } else if (!empty($fecha) && !empty($fecha_hasta)) {
      $sql.= "AND fecha >= '$fecha' ";
      $sql.= "AND fecha <= '$fecha_hasta' ";
    }
    $q = $this->db->query($sql);
    echo json_encode(array(
      "results"=>$q->result(),
    ));
  }




  function consulta() {

    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql($this->input->post("fecha"));

    $fecha_hasta = $this->input->post("fecha_hasta");
    if (!empty($fecha_hasta)) $fecha_hasta = fecha_mysql($fecha_hasta);

    $sql = "SELECT G.*, ";
    $sql.= "IF (G.id_tipo_gasto = 0,'Sin especificar',TG.nombre) AS tipo_gasto, ";
    $sql.= "IF (G.id_proveedor = 0,'Sin especificar',P.nombre) AS proveedor ";
    $sql.= "FROM gastos G ";
    $sql.= "LEFT JOIN tipos_gastos TG ON (TG.id = G.id_tipo_gasto) ";
    $sql.= "LEFT JOIN proveedores P ON (G.id_proveedor = P.id) ";
    $sql.= "WHERE 1=1 ";

    if (!empty($fecha) && empty($fecha_hasta)) {
      $sql.= "AND fecha = '$fecha' ";
    } else if (!empty($fecha) && !empty($fecha_hasta)) {
      $sql.= "AND fecha >= '$fecha' ";
      $sql.= "AND fecha <= '$fecha_hasta' ";
    }
    $q = $this->db->query($sql);
    echo json_encode($q->result());
  }

  function consulta_agrupada() {
    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql($this->input->post("fecha"));
    $fecha_hasta = $this->input->post("fecha_hasta");
    if (!empty($fecha_hasta)) $fecha_hasta = fecha_mysql($fecha_hasta);
    $sql = "SELECT SUM(efectivo) AS efectivo, ";
    $sql.= "IF (G.id_tipo_gasto = 0,'Sin especificar',TG.nombre) AS tipo_gasto ";
    $sql.= "FROM gastos G ";
    $sql.= "LEFT JOIN tipos_gastos TG ON (TG.id = G.id_tipo_gasto) ";
    $sql.= "WHERE 1=1 ";
    if (!empty($fecha) && empty($fecha_hasta)) {
      $sql.= "AND fecha = '$fecha' ";
    } else if (!empty($fecha) && !empty($fecha_hasta)) {
      $sql.= "AND fecha >= '$fecha' ";
      $sql.= "AND fecha <= '$fecha_hasta' ";
    }
    $sql.= "GROUP BY TG.id ";
    $sql.= "ORDER BY TG.nombre ASC ";
    $q = $this->db->query($sql);
    echo json_encode($q->result());
  }    

}