<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Vehiculos extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Vehiculo_Model', 'modelo');
    }

    // Utilizado en SELECT de VULCA
    function get_by_field() {

    	// Indica los campos que queremos mostrar
    	$campos = $this->input->post("campos");

    	// Filtros utilizados
    	$marca = $this->input->post("marca");
    	$anio = $this->input->post("anio");
    	$version = $this->input->post("version");
    	$modelo = $this->input->post("modelo");
    	$order_by = $this->input->post("order_by");
    	$id_empresa = $this->input->post("id_empresa");

    	$sql = "SELECT $campos FROM veh_autos ";
    	$sql.= "WHERE id_empresa = $id_empresa ";
    	if ($marca !== FALSE) $sql.= "AND marca = '$marca' ";
    	if ($anio !== FALSE) $sql.= "AND anio = '$anio' ";
    	if ($version !== FALSE) $sql.= "AND version = '$version' ";
    	if ($modelo !== FALSE) $sql.= "AND modelo = '$modelo' ";
    	if ($order_by !== FALSE) $sql.= "ORDER BY $order_by ";
    	$q = $this->db->query($sql);
    	echo json_encode($q->result());
    }
	
}