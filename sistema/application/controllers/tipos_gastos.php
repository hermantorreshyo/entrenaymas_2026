<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tipos_Gastos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Tipo_Gasto_Model', 'modelo');
  }

  function export($id_empresa = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT id,id_empresa,nombre,id_padre,orden,codigo,descripcion,id_tipo_alicuota_iva,totaliza_en ";
    $sql.= "FROM tipos_gastos A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  public function get_arbol() {
    $arr = $this->modelo->get_arbol(0);
    echo json_encode($arr);
  }
  
  function unique_find_by_codigo($codigo) {
    $id_empresa = parent::get_empresa();
    $this->db->where(array("codigo"=>$codigo,"id_empresa"=>$id_empresa));
    $query = $this->db->get("tipos_gastos");
    if ($query->num_rows($query)>0) {
      $row = $query->row();
      echo json_encode(array("results"=>array($row),"total"=>1));
    } else {
      echo json_encode(array("results"=>array(),"total"=>0));
    }
  }  

  public function reorder() {
    $id_empresa = parent::get_empresa();
    $datos = $this->input->post("datos");
    if ($datos === FALSE) return;
    $this->modelo->reorder(array(
      "id"=>0,
      "children"=>$datos,
    ));
    echo json_encode(array("error"=>1));
  }

  
}