<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Concepto_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("tipos_gastos","id");
  }

  function insert($data) {
    // Primero controlamos que no exista un concepto con el mismo nombre
    $sql = "SELECT * FROM tipos_gastos WHERE id_empresa = $data->id_empresa AND nombre = '$data->nombre' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $this->send_error("Ya existe un concepto con el nombre: '$data->nombre'.");
      exit();
    }
    return parent::insert($data);
  }

  function update($id,$data)   {
    // Controlamos que no hayamos cambiado el nombre que ya exista
    $sql = "SELECT * FROM tipos_gastos WHERE id_empresa = $data->id_empresa AND nombre = '$data->nombre' AND id != $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $this->send_error("Ya existe un concepto con el nombre: '$data->nombre'.");
      exit();
    }
    return parent::update($id,$data);    
  }
  
  function find($filter) {
    $id_empresa = parent::get_empresa();
    $this->db->where(array("id_empresa"=>$id_empresa));
    $this->db->like("nombre",$filter);
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }

  // Reordena los elementos del arbol
  function reorder($elements,$orden = 0, $id_padre = 0) {
    $id_empresa = parent::get_empresa();

    if (isset($elements["id"])) {
      $id = $elements["id"];
      if (!empty($id)) {
        $sql = "UPDATE tipos_gastos SET orden = $orden, id_padre = $id_padre ";
        $sql.= "WHERE id = $id AND id_empresa = $id_empresa ";
        $this->db->query($sql);        
      }
    }
    if (isset($elements["children"]) && is_array($elements["children"])){
      for($i=0;$i<sizeof($elements["children"]);$i++) {
        $e = $elements["children"][$i];
        $this->reorder($e,$i,$id);
      }
    }
  }
  
  public function get_arbol($id_padre = 0,$config=array()) {
    
    $id_empresa = parent::get_empresa();
    $agregar_sub_ids = isset($config["agregar_sub_ids"]) ? 1 : 0;
    $totaliza_en = isset($config["totaliza_en"]) ? $config["totaliza_en"] : "";

    $this->db->where(array("id_padre"=>$id_padre,"id_empresa"=>$id_empresa));
    if (!empty($totaliza_en)) $this->db->where(array("totaliza_en"=>$totaliza_en));
    $this->db->order_by("nombre","asc");
    $query = $this->db->get("tipos_gastos");
    $result = $query->result();
    $elementos = array();
    foreach($result as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $row->id_padre;
      $e->orden = $row->orden;
      $e->codigo = $row->codigo;
      $e->nombre = $row->nombre;
      $e->totaliza_en = $row->totaliza_en;
      $e->id_tipo_alicuota_iva = $row->id_tipo_alicuota_iva;
      $e->title = $row->nombre.((!empty($row->codigo)) ? " (".$row->codigo.")" : "" );
      $e->key = $row->id;
      $e->descripcion = $row->descripcion;
      $e->children = $this->get_arbol($row->id,array(
        "totaliza_en"=>$totaliza_en,
      ));
      if ($agregar_sub_ids == 1) $e->sub_ids = array_merge(array($e->id),$this->get_sub_ids($e->children));
      $elementos[] = $e;
    }
    return $elementos;    
  }

  // Obtiene un listado de IDS de todos los conceptos y subconceptos
  // que deben totalizar 
  function get_ids_totaliza_en($totaliza_en) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM tipos_gastos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND totaliza_en = '$totaliza_en' ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $concepto) {
      $concepto->children = $this->get_arbol($concepto->id,array(
        "agregar_sub_ids"=>1,
      ));
      $ids = array();
      foreach($concepto->children as $c) {
        $ids = array_merge($ids,$c->sub_ids);
      }
      $salida = array_merge($salida,array($concepto->id),$ids);
    }
    return implode(",",$salida);
  }

  function get_by_id($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM tipos_gastos WHERE id_empresa = $id_empresa AND id = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;
    $concepto = $q->row();
    $concepto->children = $this->get_arbol($concepto->id,array(
      "agregar_sub_ids"=>1,
    ));
    $ids = array();
    foreach($concepto->children as $c) {
      $ids = array_merge($ids,$c->sub_ids);
    }
    $concepto->sub_ids = array_merge(array($id),$ids);
    return $concepto;
  }

  function get_by_codigo($codigo) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM tipos_gastos WHERE id_empresa = $id_empresa AND codigo = '$codigo' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;
    $concepto = $q->row();
    $concepto->children = $this->get_arbol($concepto->id,array(
      "agregar_sub_ids"=>1,
    ));
    $ids = array();
    foreach($concepto->children as $c) {
      $ids = array_merge($ids,$c->sub_ids);
    }
    $concepto->sub_ids = array_merge(array($concepto->id),$ids);
    return $concepto;
  }

  // Esta funcion es utilizada para obtener todos los IDS de las categorias hijos
  function get_sub_ids($array) {
    $ids = array();
    for($i=0;$i<sizeof($array);$i++) {
      $o = $array[$i];
      $ids[] = $o->id;
      if (sizeof($o->children)>0) $ids = array_merge($ids,$this->get_sub_ids($o->children));
    }
    return $ids;
  }

}