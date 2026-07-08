<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tripulantes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Tripulante_Model', 'modelo');
  }

  function liquidacion() {

    $id_empresa = parent::get_empresa();
    $mes = parent::get_post("mes",date("m"));
    $anio = parent::get_post("anio",date("Y"));
    $id_tripulante = parent::get_post("id_tripulante",0);
    $salida = array();

    // Primero seleccionamos el sueldo base
    $sql = "SELECT * FROM via_tripulantes WHERE id_empresa = $id_empresa AND id = $id_tripulante";
    $q = $this->db->query($sql);
    if ($q->num_rows()==0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el chofer con ID $id",
      ));
      exit();
    }
    $chofer = $q->row();
    $salida[] = array(
      "concepto"=>"Sueldo base",
      "base"=>0,
      "porcentaje"=>0,
      "monto"=>$chofer->sueldo_base,
    );

    // Ahora tomamos los viajes realizados en el periodo
    $sql = "SELECT V.nombre, VT.comision, V.precio, DATE_FORMAT(V.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM via_viajes V INNER JOIN via_viajes_vehiculos_tripulantes VT ON (V.id_empresa = VT.id_empresa AND V.id = VT.id_viaje) ";
    $sql.= "WHERE V.id_empresa = $id_empresa ";
    $sql.= "AND DATE_FORMAT(V.fecha,'%m-%Y') = '".$mes."-".$anio."' ";
    $sql.= "AND VT.id_tripulante = $id_tripulante ";
    $sql.= "AND V.estado = 1 ";
    $sql.= "ORDER BY V.fecha ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $salida[] = array(
        "concepto"=>$row->nombre." (".$row->fecha.")",
        "base"=>$row->precio,
        "porcentaje"=>$row->comision,
        "monto"=>($row->precio * ($row->comision / 100)),
      );
    }

    echo json_encode($salida);
  }

}