<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Punto_Venta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("puntos_venta","id","nombre ASC");
	}

  function get_por_defecto($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    // Buscamos el punto de venta asociado con la web
    $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
    $sql.= "FROM puntos_venta PV ";
    $sql.= "LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
    $sql.= "WHERE PV.id_empresa = $id_empresa ";
    $sql.= "AND PV.por_default = 1 ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $pv = $q->row();
      return $pv;
    }
    return FALSE;
  }

  function get_punto_venta_web($config = array()) {
  	$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    // Buscamos el punto de venta asociado con la web
    $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
    $sql.= "FROM puntos_venta PV INNER JOIN web_configuracion CONF ON (PV.id = CONF.id_punto_venta AND PV.id_empresa = CONF.id_empresa) ";
    $sql.= "LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
    $sql.= "WHERE PV.id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
    	$pv = $q->row();
      return $pv;
    }
    return FALSE;
  }

  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    if (!empty($order_by)) $this->db->order_by($order_by." ".$order);
    else $this->db->order_by($this->order_by);
    $id_empresa = $this->get_empresa();
    $this->db->where("id_empresa",$id_empresa);      
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }
	
	function get($id, $config = array()) {
		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->get_empresa();
    $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
    $sql.= "FROM puntos_venta PV ";
    $sql.= "LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
    $sql.= "WHERE PV.id = $id AND PV.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
    if ($query->num_rows()==0) return FALSE;
		$row = $query->row();

    $this->load->model("Tipo_Comprobante_Model");
    $comprobantes = $this->Tipo_Comprobante_Model->get_all();
    foreach($comprobantes as $c) {
  		$sql = "SELECT * FROM numeros_comprobantes WHERE id_empresa = $id_empresa AND id_punto_venta = $id AND id_tipo_comprobante = $c->id ";
  		$q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $r = $q->row();
        $row->{"numero_comp_$r->id_tipo_comprobante"} = $r->ultimo;
        $row->{"copias_comp_$r->id_tipo_comprobante"} = $r->copias;
      } else {
        $row->{"numero_comp_$c->id"} = 0;
        $row->{"copias_comp_$c->id"} = 1;
      }
    }
		
		$this->db->close();
		return $row;
	}	
    
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);		
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function insert($data) {
		
		foreach(get_object_vars($data) as $key => $value) {
			if (strpos($key,"numero_comp_") === 0) unset($data->{$key});
			if (strpos($key,"copias_comp_") === 0) unset($data->{$key});
		}		
    unset($data->sucursal);
		
		$this->db->insert($this->tabla,$data);
		$id = $this->db->insert_id();
		
		// Creamos los numeros de comprobantes
		$this->load->model("Tipo_Comprobante_Model");
		$comprobantes = $this->Tipo_Comprobante_Model->get_all();
		foreach($comprobantes as $c) {
			$sql = "INSERT INTO numeros_comprobantes (id_empresa,id_punto_venta,id_tipo_comprobante,ultimo,copias) VALUES (";
			$sql.= "$data->id_empresa,$id,$c->id,0,1)";
			$this->db->query($sql);
		}
		$this->db->close();
		if (!isset($id)) return -1;
		else return $id;
	}
	
	function update($id,$data) {
		
		// Controlamos que estemos editando un elemento que nos pertenece como empresa
		if (isset($data->id_empresa)) {
			$q = $this->db->get_where($this->tabla,array("id_empresa"=>$data->id_empresa,$this->ident=>$id));
			if ($q->num_rows()<=0) {
				return 0;
			}
		}
		
		// Guardamos la numeracion
		$comprobantes = array();
		// Primero recorremos los numeros
		foreach(get_object_vars($data) as $key => $value) {
			if (strpos($key,"numero_comp_") === 0) {
				$id_tipo_comprobante = str_replace("numero_comp_","",$key);
				$comprobantes[$id_tipo_comprobante] = array(
					"ultimo"=>$value,
					"id_tipo_comprobante"=>$id_tipo_comprobante,
					"id_empresa"=>$data->id_empresa
				);
				unset($data->{$key});
			}
		}
    unset($data->sucursal);
    
		// Despues recorremos las copias
		foreach(get_object_vars($data) as $key => $value) {
			if (strpos($key,"copias_comp_") === 0) {
				$id_tipo_comprobante = str_replace("copias_comp_","",$key);
				$comprobantes[$id_tipo_comprobante] = array_merge($comprobantes[$id_tipo_comprobante],array("copias"=>$value));
				unset($data->{$key});
			}
		}
		// Finalmente guardamos
		foreach($comprobantes as $c) {
      $sql = "SELECT * FROM numeros_comprobantes ";
      $sql.= "WHERE id_tipo_comprobante = ".$c["id_tipo_comprobante"]." AND id_empresa = ".$c["id_empresa"]." AND id_punto_venta = $id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
  			$sql = "UPDATE numeros_comprobantes SET ";
  			$sql.= " ultimo = ".$c["ultimo"].", ";
  			$sql.= " copias = ".$c["copias"]." ";
  			$sql.= "WHERE id_tipo_comprobante = ".$c["id_tipo_comprobante"]." AND id_empresa = ".$c["id_empresa"]." AND id_punto_venta = $id ";
      } else {
        $sql = "INSERT INTO numeros_comprobantes ";
        $sql.= "(id_empresa,id_punto_venta,id_tipo_comprobante,ultimo,copias) VALUES (";
        $sql.= $c["id_empresa"].",$id,".$c["id_tipo_comprobante"].",0,1)";
      }
			$this->db->query($sql);
		}
		
		// Controlamos si el punto de venta es por default
		//if ($data->por_default == 1)
		
		$this->db->where("id_empresa",$data->id_empresa);
    $this->db->where($this->ident,$id);
		$this->db->update($this->tabla,$data);
		$aff = $this->db->affected_rows();
		$this->db->close();
		return $aff;
	}	
	
	
}