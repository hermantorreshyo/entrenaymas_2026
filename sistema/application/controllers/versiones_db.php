<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Versiones_Db extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Version_Db_Model', 'modelo');
    }
    
    function exportar($desde = 0) {
        $q = $this->db->query("SELECT * FROM com_versiones_db WHERE id >= $desde ORDER BY id ASC");
        $salida = "";
        foreach($q->result() as $r) {
            $salida.= $r->texto."\r\n\r\n";
        }
        header("Content-disposition: attachment; filename=modificaciones_$desde.txt");
        header("Content-type: application/octet-stream");	
        echo $salida;        
    }
    
}