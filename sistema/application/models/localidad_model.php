<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Localidad_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_localidades","id","nombre ASC",0);
	}

  function get_select($config = array()) {
    $id_departamento = (isset($config["id_departamento"])) ? $config["id_departamento"] : 0;
    $sql = "SELECT P.* FROM com_localidades P WHERE P.id_departamento = $id_departamento ORDER BY P.prioridad ASC, P.nombre ASC";
    $q = $this->db->query($sql);
    $result = array();
    foreach($q->result() as $row) {
      $result[] = $row;
    }
    return $result;
  } 


	function utilizadas($config = array()) {
		$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
		$id_proyecto = isset($config["id_proyecto"]) ? $config["id_proyecto"] : 0;
    if ($id_proyecto == 3) {
	    $sql = "SELECT L.*, COUNT(P.id) AS cantidad ";
	    $sql.= "FROM com_localidades L ";
      $sql.= "INNER JOIN inm_propiedades P ON (L.id = P.id_localidad) ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "GROUP BY L.id ";
      $sql.= "ORDER BY cantidad DESC ";
	    $q = $this->db->query($sql);
	    return $q->result();

	  // Por ahora solo esta disponible para INMOVAR
    } else return array();
	}

  function get_by_codigo_postal($codigo_postal) {
    $sql = "SELECT L.* ";
    $sql.= "FROM com_localidades L ";
    $sql.= "WHERE L.codigo_postal = '$codigo_postal' ";
    $sql.= "ORDER BY L.prioridad ASC, L.nombre ASC ";
    $query = $this->db->query($sql);
    if ($query->num_rows()>0) return $query->row();
    else return FALSE;
  }
    
  function get_by_provincia($id_provincia,$id_pais = 1) {
		$sql = "SELECT L.* ";
		$sql.= "FROM com_localidades L INNER JOIN com_departamentos D ON (L.id_departamento = D.id) ";
		$sql.= "INNER JOIN com_provincias P ON (D.id_provincia = P.id) ";
		$sql.= "WHERE P.id = $id_provincia ";
    $sql.= "AND P.id_pais = $id_pais ";
		$sql.= "ORDER BY L.prioridad ASC, L.nombre ASC ";
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
  }

  function get_by_departamento($id_departamento) {
    $sql = "SELECT L.* ";
    $sql.= "FROM com_localidades L INNER JOIN com_departamentos D ON (L.id_departamento = D.id) ";
    $sql.= "WHERE D.id = $id_departamento ";
    $sql.= "ORDER BY L.prioridad ASC, L.nombre ASC ";
    $query = $this->db->query($sql);
    $result = $query->result();
    $this->db->close();
    return $result;
  }  

	function find($filter) {
		$sql = "SELECT L.*, ";
		$sql.= " IF(D.nombre IS NULL,'',D.nombre) AS departamento, ";
		$sql.= " IF(P.nombre IS NULL,'',P.nombre) AS provincia, ";
		$sql.= " IF(PA.nombre IS NULL,'',PA.nombre) AS pais ";
		$sql.= "FROM com_localidades L LEFT JOIN com_departamentos D ON (L.id_departamento = D.id) ";
		$sql.= "LEFT JOIN com_provincias P ON (D.id_provincia = P.id) ";
		$sql.= "LEFT JOIN com_paises PA ON (PA.id = P.id_pais) ";
		$sql.= "WHERE L.nombre LIKE '%$filter% AND PA.id = 196' ";
		$sql.= "ORDER BY L.prioridad ASC, L.nombre ASC ";
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function get($id) {
		$query = $this->db->get_where($this->tabla,array($this->ident=>$id));
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}

  function get_argenprop($id) {
    $sql = "SELECT L.*, L.id_argenprop AS id_localidad_argenprop, L.id_departamento_argenprop, L.id_barrio_argenprop, ";
    $sql.= " IF(P.id_argenprop IS NULL,0,P.id_argenprop) AS id_provincia_argenprop, ";
    $sql.= " IF(PA.id_argenprop IS NULL,0,PA.id_argenprop) AS id_pais_argenprop, ";
    $sql.= " IF(D.nombre IS NULL,'',D.nombre) AS departamento, ";
    $sql.= " IF(P.nombre IS NULL,'',P.nombre) AS provincia, ";
    $sql.= " IF(PA.nombre IS NULL,'',PA.nombre) AS pais ";
    $sql.= "FROM com_localidades L LEFT JOIN com_departamentos D ON (L.id_departamento = D.id) ";
    $sql.= "LEFT JOIN com_provincias P ON (D.id_provincia = P.id) ";
    $sql.= "LEFT JOIN com_paises PA ON (PA.id = P.id_pais) ";
    $sql.= "WHERE L.id = '$id' ";
    $query = $this->db->query($sql);
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }
	
	function save($data) {
		unset($data->departamento);
		unset($data->provincia);
		unset($data->pais);
		parent::save($data);
	}
	
  //$limit = null, $offset = null,$order_by = '',$order = ''
	function get_all($limit = null, $offset = null,$order_by = '',$order = '')  {
		$sql = "SELECT L.*, ";
		$sql.= " IF(D.nombre IS NULL,'',D.nombre) AS departamento, ";
		$sql.= " IF(P.nombre IS NULL,'',P.nombre) AS provincia, ";
		$sql.= " IF(PA.nombre IS NULL,'',PA.nombre) AS pais ";
		$sql.= "FROM com_localidades L LEFT JOIN com_departamentos D ON (L.id_departamento = D.id) ";
		$sql.= "LEFT JOIN com_provincias P ON (D.id_provincia = P.id) ";
		$sql.= "LEFT JOIN com_paises PA ON (PA.id = P.id_pais) ";
		//if (!empty($order_by)) $sql.= "ORDER BY $order_by $order ";
    //Filtro por solo españa
    $sql.= "WHERE PA.id = 196 ";
    $sql.= "ORDER BY L.prioridad ASC, L.nombre ASC ";
    if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) $sql.= "LIMIT $limit, $offset ";
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

}