<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Carp_Choferes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Carp_Chofer_Model', 'modelo');
  }

  function buscar_por_dni() {
    $id_empresa = parent::get_empresa();
    $dni = parent::get_post("dni","");
    $id_propietario = parent::get_post("id_propietario",0);
    $sql = "SELECT U.*, CH.observaciones, CH.estado, CH.vehiculo, CH.id_propietario, CH.apellido, ";
    $sql.= " IF(CH.fecha_alta = '0000-00-00','',DATE_FORMAT(CH.fecha_alta,'%d/%m/%Y')) AS fecha_alta, ";
    $sql.= " IF(CH.fecha_baja = '0000-00-00','',DATE_FORMAT(CH.fecha_baja,'%d/%m/%Y')) AS fecha_baja, ";        
    $sql.= " CH.bolsa_trabajo, CH.latitud, CH.longitud, CH.numero_calle, CH.ciudad ";
    $sql.= "FROM com_usuarios U INNER JOIN carp_choferes CH ON (U.id_empresa = CH.id_empresa AND CH.id_usuario = U.id) ";
    $sql.= "WHERE U.dni = '$dni' ";
    $sql.= "AND U.id_empresa = $id_empresa ";
    $sql.= "AND CH.id_propietario != $id_propietario ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();

      if (empty($r->fecha_baja)) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"El DNI ya se encuentra cargado en otra remiseria. Por favor solicite al administrador la baja.",
        ));
        exit();
      }

      $r->error = 0;
      echo json_encode($r);
    } else {
      echo json_encode(array("error"=>1,"mensaje"=>""));
    }
  }

  function get($id) {

    if ($id == "index") {
      $conf = array();
      $order_by = $this->input->get("order_by");
      $order = $this->input->get("order");
      if ($order_by !== FALSE) $conf["order"] = $order_by." ".$order;
      $conf["limit"] = parent::get_get("limit",0);
      $conf["id_agencia"] = parent::get_get("id_agencia",0);
      $conf["id_propietario"] = parent::get_get("id_propietario",0);
      $conf["offset"] = parent::get_get("offset",30);
      $conf["filter"] = parent::get_get("filter","");
      $conf["estado"] = parent::get_get("estado","");
      $conf["id_empresa"] = parent::get_get("id_empresa",parent::get_empresa());
      $lista = $this->modelo->buscar($conf);
      $total = $this->modelo->get_total_results();
      $salida = array(
        "total"=> $total,
        "results"=>$lista
      );
      echo json_encode($salida);
    }  else {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }
  }    
    
  function save_file() {
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    $filename = filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/$filename";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
    echo json_encode(array(
      "path"=>$path,
      "error"=>0,
    ));
  }
    
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }    

}