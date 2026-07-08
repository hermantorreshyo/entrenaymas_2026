<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Web_Banners extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Web_Banner_Model', 'modelo',"orden ASC",1);
  }
    
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/slider/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }
        
    function ordenar() {
        $ids = $this->input->post("ids");
        $id_empresa = parent::get_empresa();
        if (!empty($ids)) {
            $ids = json_decode($ids);
            for($i=0;$i<sizeof($ids);$i++) {
                $id = $ids[$i];
                $this->db->query("UPDATE web_banners SET orden = $i WHERE id = $id AND id_empresa = $id_empresa");
            }
        }
        echo json_encode(array());
    }
    
}