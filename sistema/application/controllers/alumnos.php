<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Alumnos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Alumno_Model', 'modelo');
  }
  
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/alumnos/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }	
  
  function next() {
    $numero_legajo = $this->modelo->next();
    echo json_encode(array(
      "numero_legajo"=>$numero_legajo
    ));
  }
    
  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT ";
    $sql.= " C.nombre, C.id, C.email, C.celular, C.cuit, C.activo, C.path, A.numero_legajo, A.id_comision ";
    $sql.= "FROM aca_alumnos A ";
    $sql.= "INNER JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE (C.nombre LIKE '%$nombre%' OR C.cuit = '$nombre' OR A.numero_legajo = '$nombre') ";
    $sql.= "AND A.id_empresa = '$id_empresa' ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->nombre;
      $rr->label = $r->nombre;
      $rr->info = ((!empty($r->numero_legajo)) ? "Legajo: ".$r->numero_legajo.". " : "").((!empty($r->cuit)) ? "DNI: ".$r->cuit.". " : "");
      $rr->numero_legajo = $r->numero_legajo;
      $rr->id_comision = $r->id_comision;
      $rr->cuit = $r->cuit;
      $rr->path = $r->path;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }    
    
  function ver() {
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $id_comision = $this->input->get("id_comision");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $conf = array(
      "filter"=>$filter,
      "id_comision"=>$id_comision,
      "limit"=>$limit,
      "offset"=>$offset,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }
	
	
  function registro() {

    $obj = new stdClass();
    $obj->id_empresa = $this->input->post("id_empresa");
    if ($obj->id_empresa === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: id_empresa no definida",
      ));
      return;
    }
    $obj->nombre = $this->input->post("nombre");
    if ($obj->nombre === FALSE) $obj->nombre = "";
    $obj->apellido = $this->input->post("apellido");
    if ($obj->apellido === FALSE) $obj->apellido = "";
    $obj->email = $this->input->post("email");
    if ($obj->email === FALSE) $obj->email = "";
    $obj->id_comision = $this->input->post("id_comision");
    if ($obj->id_comision === FALSE) $obj->id_comision = 0;
    $obj->password = $this->input->post("password");
    if ($obj->password === FALSE) $obj->password = "";
    $obj->numero_documento = $this->input->post("dni");
    if ($obj->numero_documento === FALSE) $obj->numero_documento = "";

    $obj->patologia = $this->input->post("patologia");
    if ($obj->patologia === FALSE) $obj->patologia = "";
    $obj->alergia = $this->input->post("alergia");
    if ($obj->alergia === FALSE) $obj->alergia = "";
    $obj->medicacion = $this->input->post("medicacion");
    if ($obj->medicacion === FALSE) $obj->medicacion = "";
    $obj->obra_social = $this->input->post("obra_social");
    if ($obj->obra_social === FALSE) $obj->obra_social = "";


    $email_tutor = $this->input->post("email_tutor");
    $this->load->model("Tutor_Model");
    if ($email_tutor !== FALSE) {
    $tutor = $this->Tutor_Model->find_by_email($email_tutor,$obj->id_empresa);
    if ($tutor === FALSE) {
    // Debemos crearlo
    $tutor = new stdClass();
    $tutor->id_empresa = $obj->id_empresa;
    $tutor->email = $email_tutor;
    $tutor->nombre = ($this->input->post("nombre_tutor") === FALSE) ? "" : $this->input->post("nombre_tutor");
    $tutor->apellido = ($this->input->post("apellido_tutor") === FALSE) ? "" : $this->input->post("apellido_tutor");
    $tutor->dni = ($this->input->post("dni_tutor") === FALSE) ? "" : $this->input->post("dni_tutor");
    $id = $this->Tutor_Model->insert($tutor);
    $tutor->id = $id;
    }
    $obj->id_tutor = $tutor->id;
    }

    // Guardamos los datos
    $this->modelo->insert($obj);
    echo json_encode(array(
      "error"=>0,
    ));
  }
	
    
}
