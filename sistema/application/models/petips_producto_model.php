<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Petips_Producto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("petips_productos","id","nombre ASC");
	}

	function save($data) {

		$claims = $data->claims;
    $images = $data->images;
		$ingredientes = $data->ingredientes;
		unset($data->claims);
		unset($data->ingredientes);
    unset($data->images);
		$id = parent::save($data);

    $this->db->query("DELETE FROM petips_productos_claims WHERE id_producto = $id AND id_empresa = $data->id_empresa ");
    $k = 0;
    foreach($claims as $item) {
      $sql = "INSERT INTO petips_productos_claims (id_empresa,id_producto,id_claim,orden";
      $sql.= ") VALUES ($data->id_empresa,$id,'$item->id_claim','$k') ";
      $this->db->query($sql);
      $k++;
    }

    $this->db->query("DELETE FROM petips_productos_ingredientes WHERE id_producto = $id AND id_empresa = $data->id_empresa ");
    $k = 0;
    foreach($ingredientes as $item) {
      $sql = "INSERT INTO petips_productos_ingredientes (id_empresa,id_producto,id_ingrediente,orden";
      $sql.= ") VALUES ($data->id_empresa,$id,'$item->id_ingrediente','$k') ";
      $this->db->query($sql);
      $k++;
    }

    $this->db->query("DELETE FROM petips_productos_imagenes WHERE id_producto = $id AND id_empresa = $data->id_empresa ");
    $k = 0;
    foreach($images as $item) {
      $sql = "INSERT INTO petips_productos_imagenes (id_empresa,id_producto,path,orden";
      $sql.= ") VALUES ($data->id_empresa,$id,'$item','$k') ";
      $this->db->query($sql);
      $k++;
    }

		return $id;
	}

	function get($id,$config = array()) {
		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->get_empresa();
		$sql = "SELECT U.* ";
		$sql.= "FROM petips_productos U ";
		$sql.= "WHERE U.id = $id AND U.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		$row = $query->row();

    $sql = "SELECT AI.*, A.nombre, A.puntaje ";
    $sql.= "FROM petips_productos_ingredientes AI ";
    $sql.= "INNER JOIN petips_ingredientes A ON (AI.id_empresa = A.id_empresa AND AI.id_ingrediente = A.id) ";
    $sql.= "WHERE AI.id_producto = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $row->ingredientes = array();
    foreach($q->result() as $r) {
      $row->ingredientes[] = $r;
    }		

    $sql = "SELECT AI.*, A.nombre, A.puntaje ";
    $sql.= "FROM petips_productos_claims AI ";
    $sql.= "INNER JOIN petips_claims A ON (AI.id_empresa = A.id_empresa AND AI.id_claim = A.id) ";
    $sql.= "WHERE AI.id_producto = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $row->claims = array();
    foreach($q->result() as $r) {
      $row->claims[] = $r;
    }		

    // Obtenemos las imagenes de ese articulo
    $sql = "SELECT AI.* FROM petips_productos_imagenes AI ";
    $sql.= "WHERE AI.id_producto = $id AND AI.id_empresa = $row->id_empresa ";
    $sql.= "ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $row->images = array();
    foreach($q->result() as $r) {
      $row->images[] = $r->path;
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
}