<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pres_Clientes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Pres_Cliente_Model', 'modelo');
  }

  function existe_documento() {
    $documento = parent::get_post("documento");
    $r = $this->modelo->get_by_documento($documento);
    if ($r === FALSE) {
      echo json_encode(array(
        "existe"=>0,
      ));
    } else {
        echo json_encode(array(
        "existe"=>1,
        "mensaje"=>"Ya existe un cliente cargado con el documento $documento en la sucursal $r->sucursal",
      ));
    }
  }

  function get_consultas() {
    $this->load->model("Consulta_Model");
    $id_contacto = $this->input->post("id_cliente");
    $res = $this->Consulta_Model->buscar(array(
      "id_contacto"=>$id_contacto,
      "offset"=>999999,
      "buscar_respuestas"=>0,
      "buscar_adjuntos"=>0,
    ));
    echo json_encode($res["results"]);
  }

  function get($id) {
    @session_start();
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {

      $garante = ($this->input->get("garante") !== FALSE) ? $this->input->get("garante") : 0;
      $id_sucursal = ($this->input->get("id_sucursal") !== FALSE) ? $this->input->get("id_sucursal") : 0;
      $order_by = ($this->input->get("order_by") !== FALSE) ? $this->input->get("order_by")." " : "";
      $order = ($this->input->get("order") !== FALSE) ? $this->input->get("order") : "";
      $filter = ($this->input->get("term") !== FALSE) ? urldecode($this->input->get("term")) : "";
      $filtro_especial = ($this->input->get("filtro_especial") !== FALSE) ? urldecode($this->input->get("filtro_especial")) : 0;
      $numero_prestamo = ($this->input->get("numero_prestamo") !== FALSE) ? urldecode($this->input->get("numero_prestamo")) : "";
      $id_plan = ($this->input->get("id_plan") !== FALSE) ? ($this->input->get("id_plan")) : 0;
      $estado = ($this->input->get("estado") !== FALSE) ? ($this->input->get("estado")) : 0;
      $fecha_vencimiento = str_replace("-","/",parent::get_get("fecha_vencimiento",""));
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");

      $r = $this->modelo->buscar(array(
        "filter"=>$filter,
        "order"=>$order_by.$order,
        "limit"=>$limit,
        "offset"=>$offset,
        "garante"=>$garante,
        "id_sucursal"=>$id_sucursal,
        "filtro_especial"=>$filtro_especial,
        "id_plan"=>$id_plan,
        "estado"=>$estado,
        "numero_prestamo"=>$numero_prestamo,
        "fecha_vencimiento"=>$fecha_vencimiento,
      ));
      echo json_encode($r);

    } else {
      $cliente = $this->modelo->get($id);
      echo json_encode($cliente);
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

    $array->nombre_completo = $array->nombre." ".$array->apellido;
    
    $id = $this->modelo->save($array);

    $this->db->query("DELETE FROM pres_clientes_estados_laborales WHERE id_empresa = $array->id_empresa AND id_cliente = $id ");
    $i=0;
    foreach($estados_laborales as $el) {
      $this->db->insert("pres_clientes_estados_laborales",array(
        "id_empresa"=>$array->id_empresa,
        "id_cliente"=>$id,
        "id_estado_laboral"=>$el->id_estado_laboral,
        "fecha_inicio"=>fecha_mysql($el->fecha_inicio),
        "fecha_fin"=>fecha_mysql($el->fecha_fin),
        "observaciones"=>$el->observaciones,
        "ingreso"=>$el->ingreso,
        "telefono_1"=>$el->telefono_1,
        "telefono_2"=>$el->telefono_2,
        "empresa"=>$el->empresa,
        "empresa_cuit"=>$el->empresa_cuit,
        "empresa_direccion"=>$el->empresa_direccion,
        "empresa_direccion"=>$el->empresa_direccion,
        "empresa_seccion"=>$el->empresa_seccion,
        "empresa_cargo"=>$el->empresa_cargo,
        "empresa_horario"=>$el->empresa_horario,
        "empresa_legajo"=>$el->empresa_legajo,
        "categoria_monotributo"=>$el->categoria_monotributo,
        "institucion"=>$el->institucion,
        "numero_beneficio"=>$el->numero_beneficio,
        "orden"=>$i,
      ));
      $i++;
    }

    $this->db->query("DELETE FROM pres_clientes_documentaciones WHERE id_empresa = $array->id_empresa AND id_cliente = $id ");
    foreach($documentaciones as $el) {
      $eliminado = (isset($el->eliminado) ? $el->eliminado : 0);
      if ($eliminado == 0) {
        $this->db->insert("pres_clientes_documentaciones",array(
          "id_empresa"=>$array->id_empresa,
          "id_cliente"=>$id,
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
    $array->fecha_ult_operacion = date("Y-m-d H:i:s");
    $array->nombre_completo = $array->nombre." ".$array->apellido;
    
    $this->modelo->save($array);

    $this->db->query("DELETE FROM pres_clientes_estados_laborales WHERE id_empresa = $array->id_empresa AND id_cliente = $array->id ");
    $i=0;
    foreach($estados_laborales as $el) {
      $this->db->insert("pres_clientes_estados_laborales",array(
        "id_empresa"=>$array->id_empresa,
        "id_cliente"=>$id,
        "id_estado_laboral"=>$el->id_estado_laboral,
        "fecha_inicio"=>fecha_mysql($el->fecha_inicio),
        "fecha_fin"=>fecha_mysql($el->fecha_fin),
        "observaciones"=>$el->observaciones,
        "ingreso"=>$el->ingreso,
        "telefono_1"=>$el->telefono_1,
        "telefono_2"=>$el->telefono_2,
        "empresa"=>$el->empresa,
        "empresa_cuit"=>$el->empresa_cuit,
        "empresa_direccion"=>$el->empresa_direccion,
        "empresa_direccion"=>$el->empresa_direccion,
        "empresa_seccion"=>$el->empresa_seccion,
        "empresa_cargo"=>$el->empresa_cargo,
        "empresa_horario"=>$el->empresa_horario,
        "empresa_legajo"=>$el->empresa_legajo,
        "categoria_monotributo"=>$el->categoria_monotributo,
        "institucion"=>$el->institucion,
        "numero_beneficio"=>$el->numero_beneficio,
        "orden"=>$i,
      ));
      $i++;
    }

    $this->db->query("DELETE FROM pres_clientes_documentaciones WHERE id_empresa = $array->id_empresa AND id_cliente = $array->id ");
    foreach($documentaciones as $el) {
      $eliminado = (isset($el->eliminado) ? $el->eliminado : 0);
      if ($eliminado == 0) {
        $this->db->insert("pres_clientes_documentaciones",array(
          "id_empresa"=>$array->id_empresa,
          "id_cliente"=>$id,
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
    $garante = ($this->input->get("garante") !== FALSE) ? $this->input->get("garante") : 0;
    $not_id = parent::get_get("not_id",0);
    $s = $this->modelo->buscar(array(
      "filter"=>$nombre,
      "garante"=>$garante,
      "not_id"=>$not_id,
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
    $sql.= "FROM pres_clientes A ";
    $sql.= "WHERE A.id_empresa = $id_empresa AND ";
    if (is_numeric($descripcion) && strlen($descripcion)<=6) {
      $sql.= "EXISTS (SELECT 1 FROM pres_prestamos PP WHERE PP.id_empresa = A.id_empresa AND PP.id_cliente = A.id AND PP.numero = '$descripcion') ";
    } else {
      $sql.= "(CONCAT(A.nombre,' ',A.apellido) LIKE '%$descripcion%'  ";
      $sql.= " OR A.documento LIKE '$descripcion%') ";
    }
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
    
    // Obtenemos el cliente
    $row = $this->modelo->get_by_codigo($codigo);
    if ($row == FALSE) { echo json_encode(array("error"=>1,"mensaje"=>"No existe un cliente con el codigo '$codigo'")); return; }
    if ($row->activo == 0) { echo json_encode(array("error"=>1,"mensaje"=>"El cliente $row->nombre esta desactivado.")); return; }
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