<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Reparaciones extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Reparacion_Model', 'modelo');
  }

  function imprimir($id) {
    
    $this->load->helper("fecha_helper");
    $reparacion = $this->modelo->get($id);
    if ($reparacion === FALSE || empty($reparacion)) {
      echo "Lo sentimos pero el elemento ha sido eliminado.";
      exit();
    }
    
    $id_empresa = $reparacion->id_empresa;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    $header = $this->load->view("reports/reparacion/header",null,true);
    
    $tpl = "modelo1";
    $folder = "/sistema/application/views/reports/reparacion/$tpl/dark_blue";
    
    $datos = array(
      "pedido"=>$reparacion,
      "empresa"=>$empresa,
      "header"=>$header,
      "folder"=>$folder,
      "con_precio"=>$con_precio,
    );
    $this->load->view("reports/reparacion/$tpl/pedido.php",$datos);
  }

  function update($id) {
        
    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }
    
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");

    $fecha_entrega = $array->fecha_entrega;
    if (isset($array->fecha_entrega)) $array->fecha_entrega = fecha_mysql($array->fecha_entrega);
    else $array->fecha_entrega = date("Y-m-d");

    $items = $array->items;
    $this->modelo->save($array);
    
    $this->db->query("DELETE FROM reparaciones_items WHERE id_reparacion = $id AND id_empresa = $id_empresa");
    $i=0;
    foreach($items as $l) {
      $this->db->insert("reparaciones_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_reparacion"=>$id,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio_final"=>$l->precio_final,
        "orden"=>$i,
        "total"=>$l->total,
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

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");
    
    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d H:i:s");

    $fecha_entrega = $array->fecha_entrega;
    if (isset($array->fecha_entrega)) $array->fecha_entrega = fecha_mysql($array->fecha_entrega);
    else $array->fecha_entrega = date("Y-m-d H:i:s");

    $id_usuario = $_SESSION["id"];
    $array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;
    
    $items = $array->items;
    $id_reparacion = $this->modelo->save($array);
    
    $i=0;
    foreach($items as $l) {
      $this->db->insert("reparaciones_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_reparacion"=>$id_reparacion,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio_final"=>$l->precio_final,
        "orden"=>$i,
        "total"=>$l->total,
      ));
      $i++;
    }
    echo json_encode(array(
      "id"=>$id_reparacion,
      "error"=>0,
    ));
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $reparacion = $this->modelo->get($id);
    if ($reparacion === FALSE) {
      echo json_encode(array(
        "error"=>1,
      ));
      exit();
    }
    $sql = "DELETE FROM reparaciones_items WHERE id_reparacion = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);
    $sql = "DELETE FROM reparaciones WHERE id = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);
    echo json_encode(array());
  }

}