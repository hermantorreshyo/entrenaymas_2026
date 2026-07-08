<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Rubros extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Rubro_Model', 'modelo');
  }

  function export($id_empresa = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM rubros A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }  

  // Recalcula todos los links de las categorias
  function recalcular_full_link() {
    $this->modelo->recalcular_full_link();
  }  

  function recalcular_full_link_rubros_lpc() {
    $this->load->model("Rubro_Lpc_Model");
    $q = $this->db->query("SELECT * FROM rubros_lpc WHERE full_link = '' ");
    foreach($q->result() as $r) {
      $f = $this->Rubro_Lpc_Model->full_link($r->id);
      $this->db->query("UPDATE rubros_lpc SET full_link = '".$f["full_link"]."', profundidad = '".$f["profundidad"]."' WHERE id = $r->id ");
    }
  }    

  function acomodar_link() {
    $this->modelo->recalcular_links();
    echo "TERMINO";
  }

  function mover_lote() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $rubros = parent::get_post("rubros",array());
    $id_rubro = parent::get_post("id_rubro",0);
    if (empty($rubros)) {
      echo json_encode(array("error"=>0));
      exit();
    }
    foreach($rubros as $art) {
      $sql = "UPDATE rubros SET id_padre = $id_rubro ";
      $sql.= "WHERE id = '$art' AND id_empresa = '$id_empresa' ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  } 

  function eliminar_por_lote() {
    $id_empresa = parent::get_empresa();
    $ids = parent::get_post("ids");
    if (!is_array($ids) || sizeof($ids) == 0) {
      echo json_encode(array("error"=>1));
      exit();
    }
    foreach($ids as $id) {
      $this->modelo->delete($id);
    }    
    echo json_encode(array("error"=>0));
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/articulos/";
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
  
  public function get_arbol() {
    $arr = $this->modelo->get_arbol();
    echo json_encode($arr);
  }
  
  public function get_select() {
    $id_usuario = parent::get_get("id_usuario",0);
    $id_empresa = parent::get_get("id_empresa",parent::get_empresa());
    $arr = $this->modelo->get_select(0,"",array(
      "id_usuario"=>$id_usuario,
      "id_empresa"=>$id_empresa,
    ));
    echo json_encode(array(
      "results"=>$arr,
      "total"=>sizeof($arr)
    ));
  }

  public function reordenar_todos() {
    $id_empresa = parent::get_empresa();
    $this->modelo->reordenar_todos();
    echo json_encode(array("error"=>0));
  }

  public function reorder() {
    $id_empresa = parent::get_empresa();
    $datos = $this->input->post("datos");
    if ($datos === FALSE) return;
    $this->modelo->reorder(array(
      "id"=>0,
      "children"=>$datos,
    ));

    // Recalculamos el link de las categorias hijas, nietas, etc
    // Esto no se hace en el modelo porque sino se llama muchas veces
    $this->modelo->recalcular_full_link(array(
      "id_empresa"=>$id_empresa,
    ));    

    echo json_encode(array("error"=>1));
  }
  
  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM rubros ";
    $sql.= "WHERE nombre LIKE '%$nombre%' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->nombre;
      $rr->label = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }     

}