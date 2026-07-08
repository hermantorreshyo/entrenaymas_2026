<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Mesa_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("res_mesas","id","nombre ASC");
  }

  function ver($params = array()) {
    
    $id_salon = isset($params["id_salon"]) ? $params["id_salon"] : 0;
    $ver_pedidos = isset($params["ver_pedidos"]) ? $params["ver_pedidos"] : 0;
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : parent::get_empresa();

    $sql = "SELECT *, 0 AS id_pedido, 0 AS id_estado_pedido ";
    $sql.= "FROM res_mesas M ";
    $sql.= "WHERE M.id_empresa = $id_empresa ";
    if (!empty($id_salon)) $sql.= "AND M.id_salon = $id_salon ";
      $q = $this->db->query($sql);
      $res = $q->result();

      // Si tenemos que ver los pedidos activos que hay en las mesas
      if ($ver_pedidos == 1) {
        foreach($res as $row) {
          // ID_REFERENCIA = ID_MESA
          $sql = "SELECT * FROM facturas WHERE id_referencia = $row->id AND id_empresa = $id_empresa ";
          $sql.= "AND id_tipo_estado <= 3 "; // TIENE QUE ESTAR ACTIVO o RESERVADO o PENDIENTE DE PAGO
          $q_fact = $this->db->query($sql);
          if ($q_fact->num_rows()>0) {
            $fact = $q_fact->row();
            $row->id_pedido = $fact->id;
            $row->id_estado_pedido = $fact->id_tipo_estado;
          }
        }
      }

    return array(
      "results"=>$res,
    );
  }

  function save($data) {
    unset($data->id_pedido);
    unset($data->id_estado_pedido);
    return parent::save($data);
  }

}