<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Fotos extends REST_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->model('Foto_Model', 'modelo');
    }
    
    function get_by_campo($id_campo) {
        $this->load->model("Foto_Model");
        $salida = array();
        $fotos = $this->Foto_Model->get_all(null,null,$id_campo);
        $salida["results"] = $fotos;
        $salida["total"] = sizeof($fotos);
        echo json_encode($salida);
    }
    
	function delete($id = null) {
        $foto = $this->modelo->get($id);
        unlink($foto->path);
		$this->modelo->delete($id);
		echo json_encode(array());
	}    
    
    function upload($id_campo = 0) {
        
        $upload_dir = 'uploads/';
        $allowed_ext = array('jpg','jpeg','png','gif');
        
        if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
            $this->exit_status('-1');
        }
        
        if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){
            
            $pic = $_FILES['pic'];
        
            if(!in_array($this->get_extension($pic['name']),$allowed_ext)){
                $this->exit_status('-1');
            }	
        
            // Cambiamos el nombre de la imagen
            $filename = date("YmdHmi").'_'.$pic['name'];
            if(move_uploaded_file($pic['tmp_name'], $upload_dir.$filename)){
                
                $this->load->helper('url');
                
                // Guardamos la foto en la base de datos
                $info = getimagesize($upload_dir.$filename);
                $data = array();
                $data["id_campo"] = $id_campo;
                $data["path"] = $upload_dir.$filename;
                $this->db->insert("campos_fotos",$data);
                
                $this->exit_status($filename);
            }
            
        }
        
        $this->exit_status('-1');
    }
    
    
    function exit_status($str){
        echo json_encode(array('status'=>$str));
        exit;
    }
    
    function get_extension($file_name){
        $ext = explode('.', $file_name);
        $ext = array_pop($ext);
        return strtolower($ext);
    }    
        
}