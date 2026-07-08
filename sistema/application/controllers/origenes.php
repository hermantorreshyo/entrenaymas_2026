<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Origenes extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Origen_Model', 'modelo',"orden ASC");
    }
    
    function ordenar() {
        $ids = $this->input->post("ids");
        if (!empty($ids)) {
            $ids = json_decode($ids);
            for($i=0;$i<sizeof($ids);$i++) {
                $id = $ids[$i];
                $this->db->query("UPDATE crm_origenes SET orden = $i WHERE id = $id");
            }
        }
        echo json_encode(array());
    }    
    
}