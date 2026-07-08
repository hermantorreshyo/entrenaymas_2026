<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tipos_Habitaciones extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Tipo_Habitacion_Model', 'modelo');
    }

    function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/entradas/";
		$filename = $this->input->post("file");
        $res = parent::save_image($dir,$filename);

        $thumbnail_width = $this->input->post("thumbnail_width");
        if (!empty($thumbnail_width)) {
            $resp = json_decode($res);
            $filename = str_replace($dir, "", $resp->path);
            $thumbnail_width = $this->input->post("thumbnail_width");
            $thumbnail_height = $this->input->post("thumbnail_height");
            parent::thumbnails(array(
                "dir"=>$dir,
                "preffix"=>"thumb_",
                "filename"=>$filename,
                "thumbnail_width"=>$thumbnail_width,
                "thumbnail_height"=>$thumbnail_height,                
            ));
        }        
        echo $res;
    }
	
}