<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Autos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Auto_Model', 'modelo');
  }

  function get_modelo() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_get("id_empresa");
    $id_marca = parent::get_get("id_marca");
    $sql = "SELECT DISTINCT modelo FROM veh_autos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_marca = $id_marca ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $r) {
      $salida[] = $r->modelo;
    }
    echo json_encode(array("results"=>$salida));
  }

  function get_anio() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_get("id_empresa");
    $id_marca = parent::get_get("id_marca",0);
    $modelo = parent::get_get("modelo","");
    $sql = "SELECT DISTINCT anio FROM veh_autos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id_marca)) $sql.= "AND id_marca = $id_marca ";
    if (!empty($modelo)) $sql.= "AND modelo = $modelo ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $r) {
      $salida[] = $r->anio;
    }
    echo json_encode(array("results"=>$salida));
  }

  function upload_images($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_images(array(
      "id_empresa"=>$id_empresa,
      "clave_width"=>"clasificado_auto_galeria_image_width",
      "clave_height"=>"clasificado_auto_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/clasificados/",
    ));
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/clasificados/";
    $filename = $this->input->post("file");
    echo parent::save_image($dir,$filename);
  }

  function duplicar($id) {

    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");

    $auto = $this->modelo->get($id);
    if ($auto === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la propiedad con ID: $id",
        ));
      return;
    }

    $images = $auto->images;
    $this->remove_properties($auto);

    $auto->id = 0;
    $insert_id = $this->modelo->insert($auto);

    $auto->link = "clasificado/autos/".filename($auto->marca." ".$auto->modelo,"-",0)."-".$insert_id."/";
    $this->db->query("UPDATE veh_autos SET link = '$auto->link' WHERE id = $insert_id");

    echo json_encode(array(
      "id"=>$insert_id
    ));
  }

  private function remove_properties($array) {
    unset($array->tipo);
    unset($array->localidad);
    unset($array->provincia);
    unset($array->cliente);
    unset($array->images);        
  }

  function update($id) {

    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();

    $this->load->helper("fecha_helper");
    $images = $array->images;
    $array->valido_hasta = fecha_mysql($array->valido_hasta);
    $this->remove_properties($array);

    $array->link = "clasificado/autos/".filename($array->marca." ".$array->modelo,"-",0)."-".$id."/";

    // Actualizamos los datos del propiedad
    $this->modelo->save($array);

    $this->db->query("DELETE FROM veh_autos_images WHERE id_auto = $id AND id_empresa = $array->id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO veh_autos_images (id_empresa,id_auto,path,orden) VALUES($array->id_empresa,$id,'$im',$k)");
      $k++;
    }

    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }


  function insert() {

    $this->load->helper("file_helper");
    $array = $this->parse_put();

    $this->load->helper("fecha_helper");
    $images = $array->images;
    $this->remove_properties($array);

    if ($array->id_empresa == 263) {
      // Utilizamos la fecha de ingreso como fecha de vencimiento
      $this->load->model("Web_Configuracion_Model");
      $web_conf = $this->Web_Configuracion_Model->get($id_empresa);
      $cant_dias = (empty($web_conf->texto_quienes_somos)) ? 30 : ((int)$web_conf->texto_quienes_somos);
      $array->valido_hasta = date("Y-m-d",strtotime("+".$cant_dias." days"));
    } else {
      $array->valido_hasta = fecha_mysql($array->valido_hasta);
    }
    $array->fecha = date("Y-m-d H:i:s");

    // Insertamos el propiedad
    $insert_id = $this->modelo->save($array);

    $array->link = "clasificado/autos/".filename($array->marca." ".$array->modelo,"-",0)."-".$insert_id."/";
    $this->db->query("UPDATE veh_autos SET link = '$array->link' WHERE id = $insert_id AND id_empresa = $array->id_empresa ");

    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO veh_autos_images (id_empresa,id_auto,path,orden) VALUES($array->id_empresa,$insert_id,'$im',$k)");
      $k++;
    }

    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }


  function get($id) {
    $id_empresa = parent::get_empresa();
        // Obtenemos el listado
    if ($id == "index") {
     $resultado = $this->modelo->buscar();
     echo json_encode($resultado);
   } else {
    $auto = $this->modelo->get($id);
    echo json_encode($auto);
  }

}


    /**
     *  Muestra todos los propiedades filtrando segun distintos parametros
     *  El resultado esta paginado
     */
    function ver() {
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");
      $filter = $this->input->get("filter");
      $order_by = $this->input->get("order_by");
      $order = $this->input->get("order");
      if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
      else $order = "";
      $conf = array(
        "filter"=>$filter,
        "limit"=>$limit,
        "offset"=>$offset,
        "order"=>$order,
      );
      $r = $this->modelo->buscar($conf);
      echo json_encode($r);
    }
    
  }
