<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Servicio_Envio_Model extends Abstract_Model {
	
	private $costos = array();
	private $excedentes = array();
	
	function __construct() {
		parent::__construct("env_servicios_envio","id","orden ASC",1);
	}
	
	
	/**
	 * CALCULA EL COSTO DE ENVIO DEPENDIENDO DE LA TABLA
	 * Valores devueltos:
	 *  >0 -> Precio que se debe cobrar
	 *  =0 -> Gratis
	 *  -1 -> Algun parametro se excede, hay que contactarse con el vendedor
	 */ 
	function calcular_costo_envio($id_servicio_envio,$peso,$distancia,$valor = 0) {
		
		$costo = 0;
		$servicio_envio = $this->get($id_servicio_envio);		
		if ($servicio_envio === FALSE || empty($servicio_envio->costos)) {
			// No existe la tabla para el servicio
			return -1;
		}
		
		// La distancia o el peso son mayores al limite, hay que contactarse con el vendedor
		if ($distancia > $servicio_envio->limite_distancia || $peso > $servicio_envio->limite_peso) {
			return -1;
		} else {
			
			// Debemos tomar el costo mas alto y calcular el excedente
			$peso_maximo = end($servicio_envio->pesos);
			if ($peso > $peso_maximo) {
				
				// Debemos seleccionar la distancia que corresponde
				$s_distancia = 0;
				foreach($servicio_envio->distancias as $d) {
					if ($distancia <= $d) {
						$s_distancia = $d;
						break;
					}
				}
				if ($s_distancia == 0) $s_distancia = end($servicio_envio->distancias);
				
				
				// Tomamos el costo base
				foreach($servicio_envio->costos as $c) {
					if ($c->peso == $peso_maximo && $c->distancia == $s_distancia) {
						$costo = $c->costo;
					}
				}
				$excedente_por_kg = 0;
				foreach($servicio_envio->excedentes as $c) {
					if ($c->distancia == $s_distancia) {
						$excedente_por_kg = $c->costo;
					}
				}
				// A la base le sumamos el excedente por cada kilo
				$costo = $costo + (($peso - $peso_maximo) * $excedente_por_kg);
				
			} else {
				
				// Debemos seleccionar la distancia que corresponde
				$s_distancia = 0;
				foreach($servicio_envio->distancias as $d) {
					if ($distancia <= $d) {
						$s_distancia = $d;
						break;
					}
				}
				if ($s_distancia == 0) $s_distancia = end($servicio_envio->distancias);
				
				// Debemos seleccionar el peso que corresponde
				$s_peso = 0;
				foreach($servicio_envio->pesos as $d) {
					if ($peso <= $d) {
						$s_peso = $d;
						break;
					}
				}
				
				// Tomamos el costo
				foreach($servicio_envio->costos as $c) {
					if ($c->peso == $s_peso && $c->distancia == $s_distancia) {
						$costo = $c->costo;
					}
				}				
				
			}
		}
		
		// Le sumamos el seguro
		if ($valor != 0) {
			// Calculamos el porcentaje
			$seguro = $valor * ($servicio_envio->seguro_porcentaje / 100);
			// Si el monto es menor al minimo, utilizamos el minimo
			if ($seguro < $servicio_envio->seguro_minimo) $seguro = $servicio_envio->seguro_minimo;
			$costo += $seguro;
		}
		
		// Le agregamos el IVA
		$costo = $costo * 1.21;
		
		return $costo;
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function save($data) {
		unset($data->undefined);
		unset($data->pesos);
		unset($data->distancias);
		$this->costos = $data->costos;
		$this->excedentes = $data->excedentes;
		unset($data->costos);
		unset($data->excedentes);
		$data->id_empresa = $this->get_empresa();
		parent::save($data);
	}
	
	function post_save($id) {
		$this->db->query("DELETE FROM env_costos_envio WHERE id_servicio_envio = $id");
		foreach($this->costos as $c) {
			$this->db->insert("env_costos_envio",array(
				"distancia"=>$c->distancia,
				"peso"=>$c->peso,
				"costo"=>$c->costo,
				"id_servicio_envio"=>$id
			));
		}
		foreach($this->excedentes as $c) {
			$this->db->insert("env_costos_envio",array(
				"distancia"=>$c->distancia,
				"peso"=>-1,
				"costo"=>$c->costo,
				"id_servicio_envio"=>$id
			));
		}
	}

	function get($id) {
		
		$id_empresa = parent::get_empresa();
		
		// Obtenemos los datos del articulo
		$id = (int)$id;
		$sql = "SELECT S.* ";
		$sql.= "FROM env_servicios_envio S ";
		$sql.= "WHERE S.id = $id ";
		$sql.= "AND S.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$row = $q->row();
		if ($row === FALSE) return $row;
		
		$row->pesos = array();
		$row->distancias = array();
		
		// Tomamos los pesos
		$sql = "SELECT * FROM env_costos_envio ";
		$sql.= "WHERE id_servicio_envio = $id AND peso != -1 ";
		$q = $this->db->query($sql);
		$row->costos = array();
		foreach($q->result() as $r) {
			$row->costos[] = $r;
			if (!in_array($r->peso,$row->pesos)) $row->pesos[] = $r->peso;
			if (!in_array($r->distancia,$row->distancias)) $row->distancias[] = $r->distancia;
		}
		sort($row->pesos);
		sort($row->distancias);
		
		// Tomamos los excedentes
		$sql = "SELECT * FROM env_costos_envio ";
		$sql.= "WHERE id_servicio_envio = $id AND peso = -1 ";
		$q = $this->db->query($sql);
		$row->excedentes = array();
		foreach($q->result() as $r) {
			$row->excedentes[] = $r;
		}
		
		return $row;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un articulo que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM env_servicios_envio WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM env_costos_envio WHERE id_servicio_envio = $id ");
			$this->db->query("DELETE FROM env_servicios_envio WHERE id = $id");
		}
	}	
	
}