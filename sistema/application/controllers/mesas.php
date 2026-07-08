<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Mesas extends REST_Controller {

  function __construct() {
      parent::__construct();
      $this->load->model('Mesa_Model', 'modelo');
  }

  function export($id_empresa = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM res_mesas A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }    

  function ver() {
    $id_empresa = parent::get_empresa();
    $id_salon = $this->input->post("id_salon");
    $ver_pedidos = ($this->input->post("ver_pedidos") !== FALSE) ? $this->input->post("ver_pedidos") : 0;
    $array = $this->modelo->ver(array(
      "id_salon"=>$id_salon,
      "id_empresa"=>$id_empresa,
      "ver_pedidos"=>$ver_pedidos,
    ));
    echo json_encode($array);
  }

  function reasignar() {
    $id_empresa = parent::get_empresa();
    $id_pedido = $this->input->post("id_pedido");
    $id_mesa = $this->input->post("id_mesa");
    $mesa = $this->modelo->get($id_mesa);
    $titulo = "Mesa ".$mesa->nombre;
    $sql = "UPDATE facturas ";
    $sql.= "SET id_referencia = $id_mesa, reference_id = '$titulo' ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id = $id_pedido ";
    $sql.= "AND id_tipo_estado = 0 "; // Tiene que estar PENDIENTE
    $sql.= "AND tipo = 'M' "; // Y ser una mesa
    $this->db->query($sql);
    echo json_encode(array("error"=>0));
  }

  // Debemos agregar el pedido a la mesa
  function unir() {

    $id_empresa = parent::get_empresa();
    $id_pedido = $this->input->post("id_pedido");
    $id_mesa = $this->input->post("id_mesa");

    // Primero controlamos la nueva mesa
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id = $id_pedido ";
    $sql.= "AND tipo = 'M' ";
    $q = $this->db->query($sql);
    if ($q->num_rows<=0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe un pedido para $id_pedido ",
      ));
      exit();
    }
    $anterior = $q->row();

    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_referencia = $id_mesa ";
    $sql.= "AND id_tipo_estado = 0 "; // Tiene que estar PENDIENTE
    $sql.= "AND tipo = 'M' ";
    $sql.= "ORDER BY fecha DESC, hora DESC ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows<=0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: La mesa de destino no esta abierta.",
      ));
      exit();
    }
    $nueva = $q->row();

    // Calculamos la cantidad de items que tiene el pedido de destino
    $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM facturas_items ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_factura = $nueva->id AND id_punto_venta = $nueva->id_punto_venta ";
    $q = $this->db->query($sql);
    $rr = $q->row();
    $total_items_nueva = $rr->cantidad;

    // Movemos los items de un pedido al otro
    $sql = "UPDATE facturas_items ";
    $sql.= "SET id_factura = $nueva->id, orden = orden + $total_items_nueva ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_factura = $anterior->id AND id_punto_venta = $anterior->id_punto_venta ";
    $this->db->query($sql);

    // Recalculamos los totales del nuevo pedido
    $sql = "UPDATE facturas F ";
    $sql.= "SET F.total = (SELECT SUM(FI.total_con_iva) FROM facturas_items FI WHERE FI.id_empresa = F.id_empresa AND FI.id_factura = F.id AND FI.id_punto_venta = F.id_punto_venta), ";
    $sql.= " F.subtotal = F.total ";
    $sql.= "WHERE F.id_empresa = $id_empresa AND F.id = $nueva->id AND F.id_punto_venta = $nueva->id_punto_venta ";
    $this->db->query($sql);

    // Eliminamos el pedido anterior
    $sql = "DELETE FROM facturas WHERE id_empresa = $id_empresa AND id = $anterior->id AND id_punto_venta = $nueva->id_punto_venta ";
    $this->db->query($sql);

    $this->load->model("Log_Model");
    $this->Log_Model->notify(array(
      "texto"=>'COMANDO:$(".salones_container .tab_link.active").trigger("click");',
      "id_empresa"=>$id_empresa,
    ));

    echo json_encode(array("error"=>0));
  }

}