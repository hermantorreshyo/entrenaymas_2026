<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cocinas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Cocina_Model', 'modelo');
  }	

  function consulta() {
    $salida = $this->modelo->buscar();
    echo json_encode($salida);
  }

  function cambiar_estado_item() {
  	$id = $this->input->post("id");
  	$id_empresa = parent::get_empresa();
  	$sql = "UPDATE facturas_items SET ";
  	$sql.= " tipo = IF(tipo=1,0,1) ";
  	$sql.= "WHERE id_empresa = $id_empresa AND id = $id ";
  	$this->db->query($sql);

    // Controlamos si esa parte del pedido ya fue finalizado
    // para poder mandar un alerta
    $sql = "SELECT * FROM facturas_items ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      if ($row->tipo == 1) {
        // Controlamos si quedo algun item pendiente
        // que pertenezca a la misma parte del pedido (esta definido por el orden)
        $sql = "SELECT FI.* FROM facturas_items FI ";
        $sql.= " INNER JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
        $sql.= "WHERE FI.id_empresa = $id_empresa AND FI.id_factura = $row->id_factura ";
        $sql.= "AND FI.id_punto_venta = $row->id_punto_venta ";
        $sql.= "AND FI.orden = $row->orden ";
        $sql.= "AND FI.tipo = 0 ";        
        $sql.= "AND A.no_totalizar_reparto = 1 "; // Si tienen que estar en la COCINA
        $qq = $this->db->query($sql);
        if ($qq->num_rows() == 0) {

          // Obtenemos los datos del pedido
          $this->load->model("Pedido_Mesa_Model");
          $pedido = $this->Pedido_Mesa_Model->get($row->id_factura);

          // El pedido esta todo terminado
          $this->load->model("Log_Model");
          $this->Log_Model->notify(array(
            "texto"=>"El pedido de $pedido->titulo se encuentra listo.",
            "id_empresa"=>$id_empresa,
          ));
        }
      }
    }
  	echo json_encode(array("error"=>0));
  }

  function cambiar_usuario() {
    $id = $this->input->post("id");
    $id_usuario = $this->input->post("id_usuario");
    $id_empresa = parent::get_empresa();
    $sql = "UPDATE facturas SET ";
    $sql.= " id_usuario = $id_usuario ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id ";
    $this->db->query($sql);
    echo json_encode(array("error"=>0));
  }
        
}