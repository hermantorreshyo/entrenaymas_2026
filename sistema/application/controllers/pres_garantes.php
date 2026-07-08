<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pres_Garantes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Pres_Garante_Model', 'modelo');
  }

  function get($id) {
    
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {

      $order_by = ($this->input->get("order_by") !== FALSE) ? $this->input->get("order_by")." " : "";
      $order = ($this->input->get("order") !== FALSE) ? $this->input->get("order") : "";
      $filter = ($this->input->get("term") !== FALSE) ? urldecode($this->input->get("term")) : "";
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");

      $r = $this->modelo->buscar(array(
        "filter"=>$filter,
        "order"=>$order_by.$order,
        "limit"=>$limit,
        "offset"=>$offset,
      ));
      echo json_encode($r);

    } else {
      $garante = $this->modelo->get($id);
      echo json_encode($garante);
    }
  }    

  function insert() {
    $array = $this->parse_put();
    unset($array->localidad);
    $estados_laborales = $array->estados_laborales;
    unset($array->estados_laborales);
    $documentaciones = $array->documentaciones;
    unset($array->documentaciones);

    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    if (isset($array->fecha_inicial)) $array->fecha_inicial = fecha_mysql($array->fecha_inicial);
    else $array->fecha_inicial = date("Y-m-d");
    if (isset($array->fecha_nac)) $array->fecha_nac = fecha_mysql($array->fecha_nac);
    $array->id_empresa = $id_empresa;
    $array->cuit = str_replace("-","",$array->cuit);
    $array->cuit = str_replace(" ","",$array->cuit);

    // Dependiendo de la configuracion del sistema, si es LOCAL o NO
    $this->load->model("Configuracion_Model");
    $array->uploaded = ($this->Configuracion_Model->es_local()==1)?0:1;
    
    $id = $this->modelo->save($array);

    $this->db->query("DELETE FROM pres_garantes_estados_laborales WHERE id_empresa = $array->id_empresa AND id_garante = $id ");
    $i=0;
    foreach($estados_laborales as $el) {
      $this->db->insert("pres_garantes_estados_laborales",array(
        "id_empresa"=>$array->id_empresa,
        "id_garante"=>$id,
        "id_estado_laboral"=>$el->id_estado_laboral,
        "fecha_inicio"=>fecha_mysql($el->fecha_inicio),
        "fecha_fin"=>fecha_mysql($el->fecha_fin),
        "observaciones"=>$el->observaciones,
        "ingreso"=>$el->ingreso,
        "telefono_1"=>$el->telefono_1,
        "telefono_2"=>$el->telefono_2,
        "orden"=>$i,
      ));
      $i++;
    }

    $this->db->query("DELETE FROM pres_garantes_documentaciones WHERE id_empresa = $array->id_empresa AND id_garante = $id ");
    foreach($documentaciones as $el) {
      $eliminado = (isset($el->eliminado) ? $el->eliminado : 0);
      if ($eliminado == 0) {
        $this->db->insert("pres_garantes_documentaciones",array(
          "id_empresa"=>$array->id_empresa,
          "id_garante"=>$id,
          "id_documentacion"=>$el->id_documentacion,
          "fecha"=>fecha_mysql($el->fecha),
          "observaciones"=>$el->observaciones,
          "path_documentacion"=>$el->path_documentacion,
        ));
      }
    }

    echo json_encode(array(
      "id"=>$id,
      "error"=>0
    ));
  }

  function update($id) {

    if ($id == 0) { $this->insert($id); return; }
    $id_empresa = parent::get_empresa();
    $array = $this->parse_put();
    $estados_laborales = $array->estados_laborales;
    unset($array->estados_laborales);
    $documentaciones = $array->documentaciones;
    unset($array->documentaciones);

    $this->load->helper("fecha_helper");
    $array->fecha_inicial = fecha_mysql($array->fecha_inicial);
    $array->fecha_nac = fecha_mysql($array->fecha_nac);
    $array->id_empresa = $id_empresa;
    $array->cuit = str_replace("-","",$array->cuit);
    $array->cuit = str_replace(" ","",$array->cuit);        
    
    $this->modelo->save($array);

    $this->db->query("DELETE FROM pres_garantes_estados_laborales WHERE id_empresa = $array->id_empresa AND id_garante = $array->id ");
    $i=0;
    foreach($estados_laborales as $el) {
      $this->db->insert("pres_garantes_estados_laborales",array(
        "id_empresa"=>$array->id_empresa,
        "id_garante"=>$id,
        "id_estado_laboral"=>$el->id_estado_laboral,
        "fecha_inicio"=>fecha_mysql($el->fecha_inicio),
        "fecha_fin"=>fecha_mysql($el->fecha_fin),
        "observaciones"=>$el->observaciones,
        "ingreso"=>$el->ingreso,
        "telefono_1"=>$el->telefono_1,
        "telefono_2"=>$el->telefono_2,
        "orden"=>$i,
      ));
      $i++;
    }

    $this->db->query("DELETE FROM pres_garantes_documentaciones WHERE id_empresa = $array->id_empresa AND id_garante = $array->id ");
    foreach($documentaciones as $el) {
      $eliminado = (isset($el->eliminado) ? $el->eliminado : 0);
      if ($eliminado == 0) {
        $this->db->insert("pres_garantes_documentaciones",array(
          "id_empresa"=>$array->id_empresa,
          "id_garante"=>$id,
          "id_documentacion"=>$el->id_documentacion,
          "fecha"=>fecha_mysql($el->fecha),
          "observaciones"=>$el->observaciones,
          "path_documentacion"=>$el->path_documentacion,
        ));
      }
    }
    echo json_encode(array(
      "id"=>$id,
      "error"=>0
    ));
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/";
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

  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $s = $this->modelo->buscar(array(
      "filter"=>$nombre,
    ));
    $resultado = array();
    foreach($s["results"] as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->codigo;
      $rr->label = $r->nombre." ".$r->apellido;
      $rr->info = (!empty($r->direccion)) ? $r->direccion.((!empty($r->localidad))?" - ".$r->localidad : "") : "";
      $rr->nombre = $rr->label;
      $resultado[] = $rr;
    }            
    echo json_encode($resultado);
  }

  function get_by_codigo() {
    $id_empresa = parent::get_empresa();
    $codigo = $this->input->get("codigo");
    $s = $this->modelo->get_by_codigo($codigo);
    echo json_encode($s);
  }    

  function get_by_descripcion() {
    $id_empresa = parent::get_empresa();
    $descripcion = $this->input->get("term");
    $sql = "SELECT A.* ";
    $sql.= "FROM pres_garantes A ";
    $sql.= "WHERE A.id_empresa = $id_empresa AND ";
    $sql.= "(CONCAT(A.nombre,' ',A.apellido) LIKE '%$descripcion%'  ";
    $sql.= " OR A.documento LIKE '$descripcion%') ";
    $sql.= "LIMIT 0,20 ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->id_real = $r->id;
      $rr->value = $r->id;
      $rr->label = $r->nombre." ".$r->apellido;
      $rr->info = ((!empty($r->documento)) ? "Doc: ".$r->documento." " : "").((!empty($r->direccion)) ? "Dir: ".$r->direccion." " : "");
      $rr->path = $r->path;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }

  function get_info($codigo) {

    $id_empresa = parent::get_empresa();
    
    // Consumidor final
    if ($codigo == 0) {
      $row = new stdClass();
      $row->id_tipo_iva = 4;
      $row->nombre = "Consumidor Final";
      $row->cuit = "";
      $row->saldo = 0;
      $row->email = "";
      $row->direccion = "";
      $row->descuento = 0;
      $row->error = 0;
      echo json_encode($row);
      return;
    }
    
    // Obtenemos el garante
    $row = $this->modelo->get_by_codigo($codigo);
    if ($row == FALSE) { echo json_encode(array("error"=>1,"mensaje"=>"No existe un garante con el codigo '$codigo'")); return; }
    if ($row->activo == 0) { echo json_encode(array("error"=>1,"mensaje"=>"El garante $row->nombre esta desactivado.")); return; }
    $row->error = 0;
    $row->mensaje = "";
    $row->saldo = $this->modelo->saldo($row->id);
    echo json_encode($row);
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
    

  function save_documentacion() {
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path_documentacion']) || empty($_FILES['path_documentacion'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    $filename = filename($_FILES["path_documentacion"]["name"],"-");
    $path_documentacion = "uploads/$id_empresa/$filename";
    @move_uploaded_file($_FILES["path_documentacion"]["tmp_name"],$path_documentacion);
    echo json_encode(array(
      "path_documentacion"=>$path_documentacion,
      "error"=>0,
    ));
  }     
}