<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Milling_Halloffame_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("milling_halloffames","id");
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

	function save($data) {
    $this->load->helper("file_helper");
		$images = $data->images;
		$id = parent::save($data);

    // Guardamos las imagenes
    $this->db->query("DELETE FROM milling_halloffames_images WHERE id_hall = $id");
    $k=0;
    foreach($images as $im) {
      $sql = "INSERT INTO milling_halloffames_images (id_hall,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }

    $data->nombre = trim($data->nombre);
    $data->nombre = str_replace("/", "-", $data->nombre);
    $link = "profile/".filename($data->nombre,"-",0)."/";
    $this->db->query("UPDATE milling_halloffames SET link = '$link' WHERE id = $id");

		return $id;
	}

}