<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Asiento_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("via_vehiculos_asientos","id","nombre ASC");
	}

	function ver($params = array()) {
		
		$piso = isset($params["piso"]) ? $params["piso"] : 0;
		$id_vehiculo = isset($params["id_vehiculo"]) ? $params["id_vehiculo"] : 0;
		$id_viaje = isset($params["id_viaje"]) ? $params["id_viaje"] : 0;
    $filter = isset($params["filter"]) ? $params["filter"] : "";
		$id_empresa = parent::get_empresa();

		$sql = "SELECT *, 0 AS id_reserva ";
		$sql.= "FROM via_vehiculos_asientos M ";
		$sql.= "WHERE 1=1 ";
		if (!empty($id_vehiculo)) $sql.= "AND M.id_vehiculo = $id_vehiculo ";
		if (!empty($piso)) $sql.= "AND M.piso = $piso ";
    $q = $this->db->query($sql);
    $res = $q->result();

    // Si tenemos que ver los asientos reservados en un viaje en particular
    if ($id_viaje != 0) {
    	foreach($res as $row) {
    		$sql = "SELECT RA.*, IF(VEN.nombre IS NULL,'',VEN.nombre) AS vendedor, R.salida_desde, ";
        $sql.= " IF(VEN.color IS NULL,'',VEN.color) AS color ";
        $sql.= "FROM via_reservas_asientos RA ";
        $sql.= "INNER JOIN via_reservas R ON (RA.id_reserva = R.id AND RA.id_empresa = R.id_empresa) ";
        $sql.= "LEFT JOIN vendedores VEN ON (R.id_vendedor = VEN.id AND R.id_empresa = VEN.id_empresa) ";
        $sql.= "WHERE RA.id_empresa = $id_empresa ";
        $sql.= "AND RA.id_asiento = $row->id ";
        if (!empty($id_vehiculo)) $sql.= "AND RA.id_vehiculo = $id_vehiculo ";
        if (!empty($id_viaje)) $sql.= "AND R.id_viaje = $id_viaje ";
    		$q_fact = $this->db->query($sql);
    		if ($q_fact->num_rows()>0) {
    			$fact = $q_fact->row();
    			$row->id_reserva = $fact->id_reserva;
          $row->nombre = strtoupper((!empty($fact->apellido)) ? $fact->apellido.", ".$fact->nombre : $fact->nombre);
          $row->vendedor = $fact->vendedor;
          $row->salida_desde = $fact->salida_desde;
          $row->color = $fact->color;
    		} else {
          $row->id_reserva = 0;
          $row->nombre = "";
          $row->vendedor = "";
          $row->salida_desde = "";
          $row->color = "";
        }
    	}
    }
		return array(
      "results"=>$res,
		);
	}

	function save($data) {
		unset($data->id_reserva);
		return parent::save($data);
	}

    
}