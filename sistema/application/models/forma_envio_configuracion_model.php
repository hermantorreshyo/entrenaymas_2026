<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Forma_Envio_Configuracion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("env_configuracion","id_empresa","id_empresa ASC");
	}

	function get($id) {
		// Obtenemos la configuracion
		$row = parent::get($id);
		if ($row === FALSE || empty($row)) return $row;

		$q = $this->db->query("SELECT * FROM env_excepciones WHERE id_empresa = $row->id_empresa AND tipo = 0 ORDER BY id ASC");
		$row->excepciones = $q->result();

		$q = $this->db->query("SELECT * FROM env_excepciones WHERE id_empresa = $row->id_empresa AND tipo = 1 ORDER BY id ASC");
		$row->valores = $q->result();

		return $row;
	}

	function save($data) {
		$excepciones = $data->excepciones;
		$valores = $data->valores;
		unset($data->excepciones);
		unset($data->valores);

		// Guardamos
		$id = parent::save($data);

		$this->db->query("DELETE FROM env_excepciones WHERE id_empresa = $data->id_empresa");

		// Guardamos las excepciones
		foreach($excepciones as $r) {
			$r->codigo_postal = trim($r->codigo_postal);
			$this->db->query("INSERT INTO env_excepciones (id_empresa,codigo_postal,costo_envio,monto_desde,tipo) VALUES ($data->id_empresa,'$r->codigo_postal','$r->costo_envio','$r->monto_desde',0)");
		}

		// Guardamos las valores
		foreach($valores as $r) {
			$r->codigo_postal = trim($r->codigo_postal);
			$this->db->query("INSERT INTO env_excepciones (id_empresa,codigo_postal,costo_envio,monto_desde,tipo) VALUES ($data->id_empresa,'$r->codigo_postal','$r->costo_envio','$r->monto_desde',1)");
		}
	}

}