<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Localidades extends REST_Controller {
  
  function __construct() {
    parent::__construct();
    $this->load->model('Localidad_Model', 'modelo');
  }

  function utilizadas() {
    $id_empresa = $this->input->get("id_empresa");
    $id_proyecto = $this->input->get("id_proyecto");
    $result = $this->modelo->utilizadas(array(
      "id_proyecto"=>$id_proyecto,
      "id_empresa"=>$id_empresa
    ));
    $salida = array(
      "total"=>sizeof($result),
      "results"=>$result,
    );
    echo json_encode($salida);
  }
  
  // Funcion interna que sirve para regenerar todos los links de las propiedades
  function relink() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM com_localidades");
    foreach($q->result() as $r) {
      $link = filename($r->nombre,"-",0);
      echo $link."<br/>";
      $this->db->query("UPDATE com_localidades SET link = '$link' WHERE id = $r->id ");
    }
    echo "TERMINO";
  }
  
  function get_by_provincia($id,$id_pais = 1) {
    header('Access-Control-Allow-Origin: *');
    $salida = array();
    $s = $this->modelo->get_by_provincia($id,$id_pais);  
    $salida["results"] = $s;
    $salida["total"] = sizeof($s);
    echo json_encode($salida);
  }

  function get_by_departamento($id) {
    header('Access-Control-Allow-Origin: *');
    $salida = array();
    $s = $this->modelo->get_by_departamento($id);  
    $salida["results"] = $s;
    $salida["total"] = sizeof($s);
    echo json_encode($salida);
  }
  
  function get_by_nombre() {
    $nombre = $this->input->get("term");
    $id_provincia = parent::get_get("id_provincia");
    $sql = "SELECT L.nombre AS localidad, L.id AS id, L.latitud, L.longitud, L.codigo_postal, ";
    $sql.= " D.nombre AS departamento, ";
    $sql.= " P.abreviacion AS provincia ";
    $sql.= "FROM com_localidades L ";
    $sql.= "INNER JOIN com_departamentos D ON (L.id_departamento = D.id) ";
    $sql.= "INNER JOIN com_provincias P ON (D.id_provincia = P.id) ";
    $sql.= "WHERE L.nombre LIKE '%$nombre%' ";
    if (!empty($id_provincia)) $sql.= "AND P.id = $id_provincia ";
    $sql.= "ORDER BY L.nombre ASC, P.nombre ASC ";
    $sql.= "LIMIT 0,20 ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $nombre = $r->localidad." (".$r->provincia.")";
      $rr->id = $r->id;
      $rr->latitud = $r->latitud;
      $rr->longitud = $r->longitud;
      $rr->value = $nombre;
      $rr->label = $nombre;
      $rr->codigo_postal = $r->codigo_postal;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }  


  function search_for_select() {
    $nombre = $this->input->get("term");
    $sql = "SELECT L.nombre AS localidad, L.id AS id, ";
    $sql.= " D.nombre AS departamento, ";
    $sql.= " P.abreviacion AS provincia ";
    $sql.= "FROM com_localidades L ";
    $sql.= "INNER JOIN com_departamentos D ON (L.id_departamento = D.id) ";
    $sql.= "INNER JOIN com_provincias P ON (D.id_provincia = P.id) ";
    $sql.= "WHERE L.nombre LIKE '%$nombre%' ";
    $sql.= "ORDER BY L.nombre ASC, P.nombre ASC ";
    $sql.= "LIMIT 0,20 ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->text = $r->localidad." (".$r->provincia.")";
      $resultado[] = $rr;
    }
    echo json_encode(array(
      "results"=>$resultado,
      "pagination"=>array(
        "more"=>false
      )
    ));
  }  

  public function get_select() {
    header('Access-Control-Allow-Origin: *');
    $id_departamento = parent::get_get("id_departamento",0);
    $arr = $this->modelo->get_select(array(
      "id_departamento"=>$id_departamento
    ));
    echo json_encode(array(
      "results"=>$arr,
      "total"=>sizeof($arr)
    ));
  }

}