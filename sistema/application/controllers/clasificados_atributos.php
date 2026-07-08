<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Clasificados_Atributos extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Clasificado_Atributo_Model', 'modelo');
    }
	
    function get_by_nombre() {
		$id_empresa = parent::get_empresa();
        $nombre = $this->input->get("term");
        $sql = "SELECT * ";
        $sql.= "FROM clasificados_atributos ";
        $sql.= "WHERE nombre LIKE '%$nombre%' ";
		$sql.= "AND id_empresa = $id_empresa ";
        $q = $this->db->query($sql);
        $resultado = array();
        foreach($q->result() as $r) {
            $rr = new stdClass();
            $rr->id = $r->id;
            $rr->value = $r->nombre;
            $rr->label = $r->nombre;
            $resultado[] = $rr;
        }
        echo json_encode($resultado);
    }
	
	/**
	 * OBTIENE LOS VALORES QUE AGREGO EL USUARIO A UN ATRIBUTO DADO
	 * Tambien devuelve en blanco todos los atributos que pertenecen a esa categoria
	 */
	function get_values($id_categoria = 0,$id = 0) {
		
		// Obtiene los atributos para la categoria que se esta consultando
		$sql = "SELECT CA.*, A.nombre FROM clasificados_categorias_atributos CA INNER JOIN clasificados_atributos A ON (CA.id_atributo = A.id) ";
		$sql.= "WHERE CA.id_clasificado = $id_categoria ";
		$q = $this->db->query($sql);
		$salida = array();
		foreach($q->result() as $r) {
			
			// Obtiene los atributos para ese clasificado
			$sql = "SELECT * FROM clasificados_atributos_valores WHERE id_clasificado = $id AND id_atributo = $r->id_atributo AND id_empresa = $r->id_empresa ";
			$qq = $this->db->query($sql);
			if ($qq->num_rows()>0) {
				$row = $qq->row();
				$r->valor = $row->valor;
			} else {
				$r->valor = "";
			}
			$salida[] = $r;
		}
		echo json_encode($salida);
	}
    
}