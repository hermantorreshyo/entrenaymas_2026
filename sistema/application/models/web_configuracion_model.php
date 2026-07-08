<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Web_Configuracion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("web_configuracion","id_empresa","id_empresa ASC",0);
	}
	
	function get($id) {
		$query = $this->db->get_where($this->tabla,array($this->ident=>$id));
		$row = $query->row(); 

    // Obtenemos las imagenes para ML
    $sql = "SELECT AI.* FROM web_configuracion_images_meli AI ";
    $sql.= "WHERE AI.id_empresa = $row->id_empresa ";
    $sql.= "ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $row->images_meli = array();
    foreach($q->result() as $r) {
      $row->images_meli[] = $r->path;
    }

    // Controlamos si tiene cotizaciones especiales
    $sql = "SELECT * FROM cotizaciones WHERE id_empresa = $row->id_empresa ";
    $qq = $this->db->query($sql);
    if ($qq->num_rows()>0) {
      $row->conversion_automatica = 0;
      foreach($qq->result() as $rr) {
        if ($rr->moneda == 'U$D') $row->dolar = $rr->valor;
      }
    } else {
      $row->dolar = "";
      $row->conversion_automatica = 1;
    }

		$this->db->close();
		return $row;
	}
	
	function save($data) {
		unset($data->id);
    $images_meli = (isset($data->images_meli)) ? $data->images_meli : array();

    
    if (isset($data->conversion_automatica)) {
      // Si la conversion automatica esta activada
      if ($data->conversion_automatica == 1) {
        $this->db->query("DELETE FROM cotizaciones WHERE id_empresa = $data->id_empresa ");
      } else {
        // Si la conversion automatica esta desactivada, tomamos el valor ingresado por el usuario
        if (isset($data->dolar) && !empty($data->dolar)) {
          $q = $this->db->query("SELECT * FROM cotizaciones WHERE id_empresa = $data->id_empresa AND moneda = '".'U$D'."' ");
          if ($q->num_rows()>0) {
            // Actualizamos el valor especifico para esa empresa
            $r = $q->row();
            $this->db->query("UPDATE cotizaciones SET valor = '$data->dolar', fecha = NOW() WHERE id = $r->id AND id_empresa = $data->id_empresa");
          } else {
            // Insertamos el valor especifico para esa empresa
            $this->db->query("INSERT INTO cotizaciones (moneda,fecha,valor,id_empresa) VALUES ('".'U$D'."',NOW(),'$data->dolar','$data->id_empresa') ");
          }
        }
      }
    }

		$data->id_empresa = $this->get_empresa();
		parent::save($data);

    $k=0;
    $this->db->query("DELETE FROM web_configuracion_images_meli WHERE id_empresa = $data->id_empresa");
    foreach($images_meli as $im) {
      $sql = "INSERT INTO web_configuracion_images_meli (id_empresa,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$data->id_empresa,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }
	}	
    
}