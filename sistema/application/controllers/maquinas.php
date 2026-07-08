<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Maquinas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Maquina_Model', 'modelo');
  }

  function next() {
    $codigo = $this->modelo->next();
    echo json_encode(array(
      "codigo"=>$codigo
    ));
  }
    
  function duplicar($id) {
      
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");
      
    $propiedad = $this->modelo->get($id);
    if ($propiedad === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la propiedad con ID: $id",
      ));
      return;
    }
    
    $partes = $propiedad->partes;
    
    $propiedad->id = 0;
    $propiedad->codigo = $this->modelo->next(); // Ponemos el siguiente codigo
    $propiedad->link = ""; // Como el link tiene el ID, se tiene que generar de vuelta
    
    $insert_id = $this->modelo->insert($propiedad);

    $i=1;
    foreach($partes as $p) {
      $this->db->insert("mant_partes",array(
        "id_maquina"=>$insert_id,
        "nombre"=>$p->nombre,
        "observaciones"=>$p->observaciones,
        "codigo"=>$p->codigo,
        "id_empresa"=>$p->id_empresa,
        "activo"=>$p->activo,
        "orden"=>$i,
      ));
      $i++;
    }
    echo json_encode(array(
      "id"=>$insert_id
    ));
  }
    
  function update($id) {
      
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;        
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    
    $partes = $array->partes;
    $array->codigo = trim($array->codigo);

    if ($this->modelo->existe_codigo($array->codigo,$id)) {
      $salida = array(
        "error"=>1,
        "mensaje"=>"El codigo '$array->codigo' ya existe."
      );
      echo json_encode($salida);
      return;
    }

    $this->modelo->save($array);

    // Actualizamos los partes
    $this->db->query("DELETE FROM mant_partes WHERE id_maquina = $id AND id_empresa = $id_empresa");
    $i=1;
    foreach($partes as $p) {
      $this->db->insert("mant_partes",array(
        "id_maquina"=>$id,
        "nombre"=>$p->nombre,
        "observaciones"=>$p->observaciones,
        "codigo"=>$p->codigo,
        "id_empresa"=>$p->id_empresa,
        "activo"=>$p->activo,
        "orden"=>$i,
      ));
      $i++;
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
      
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    
    $partes = $array->partes;
    $array->codigo = trim($array->codigo);
    
    if ($this->modelo->existe_codigo($array->codigo)) {
      $salida = array(
        "error"=>1,
        "mensaje"=>"El codigo '$array->codigo' ya existe."
      );
      echo json_encode($salida);
      return;
    }

    $insert_id = $this->modelo->save($array);

    // Actualizamos los partes
    $i=1;
    foreach($partes as $p) {
      $this->db->insert("mant_partes",array(
        "id_maquina"=>$insert_id,
        "nombre"=>$p->nombre,
        "observaciones"=>$p->observaciones,
        "codigo"=>$p->codigo,
        "id_empresa"=>$p->id_empresa,
        "activo"=>$p->activo,
        "orden"=>$i,
      ));
      $i++;
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
      $sql = "SELECT A.*, ";
      $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS sector ";
      $sql.= "FROM mant_maquinas A ";
      $sql.= "LEFT JOIN mant_sectores TE ON (A.id_sector = TE.id AND A.id_empresa = TE.id_empresa) ";
      $sql.= "WHERE A.activo = 1 AND A.id_empresa = '$id_empresa' ";
      $sql.= "ORDER BY A.nombre ASC ";
      $q = $this->db->query($sql);
      $result = $q->result();
      echo json_encode(array(
        "results"=>$result,
        "total"=>sizeof($result)
      ));
    } else {
      $propiedad = $this->modelo->get($id);
      echo json_encode($propiedad);
    }
  }
    
    
  /**
   *  Muestra todos los propiedades filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver() {
      
    $limit = $this->input->get("limit");
    $id_sector = $this->input->get("id_sector");
    $filter = $this->input->get("filter");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $id_empresa = ($this->input->get("id_empresa") !== FALSE) ? $this->input->get("id_empresa") : parent::get_empresa();
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";

    // Si term esta definido, es porque estamos buscando de la barra
    $term = $this->input->get("term");
    if ($term !== FALSE) $filter = $term;
      
    $conf = array(
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
      "order"=>$order,
      "id_empresa"=>$id_empresa,
      "id_sector"=>$id_sector,
    );
    $r = $this->modelo->buscar($conf);

    // Dependiendo desde donde se hace la busqueda, devolvemos uno u otro formato
    if ($term === FALSE) {
      echo json_encode($r);
    } else {
      $salida = array();
      foreach($r["results"] as $row) {
        $rr = array();
        $rr["id"] = $row->id;
        $rr["value"] = $row->id;
        $rr["label"] = $row->nombre;
        $rr["info"] = $row->codigo;
        $salida[] = $rr;
      }
      echo json_encode($salida);
    }
  }

  function ver_partes($id_maquina){
    $r = $this->modelo->buscar_partes(array(
      "id_maquina"=>$id_maquina,
    ));
    echo json_encode($r);
  }

}
