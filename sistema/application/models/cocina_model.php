<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Cocina_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("ticket","id");
  }
  
  function buscar($conf = array()) {

    $lista = array();
    $resultado = array(
      "results"=>array(),
      "total"=>0,
    );
    
    $id_empresa = (isset($conf["id_empresa"])) ? $conf["id_empresa"] : parent::get_empresa();
    /*
  $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
  $offset = (isset($conf["offset"])) ? $conf["offset"] : 10;
  $filter = (isset($conf["filter"])) ? $conf["filter"] : "";
  $order = (isset($conf["order"])) ? $conf["order"] : "F.fecha DESC, F.hora DESC, F.numero DESC ";
    */

    $sql = "SELECT F.*, ";
    $sql.= "IF(F.fecha='0000-00-00','',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha, ";
    $sql.= "IF(F.hora='00:00:00','',DATE_FORMAT(F.hora,'%H:%i')) AS hora, ";
    $sql.= "IF(C.nombre IS NULL,'',C.nombre) AS cliente ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "WHERE F.tipo != 'P' AND F.id_empresa = $id_empresa ";
    $sql.= "AND id_caja_diaria = 0 "; // Los comprobantes de la caja que esta abierta
    $sql.= "ORDER BY F.fecha DESC, F.hora DESC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {            

      $sql = "SELECT FI.id, FI.orden, FI.id_articulo, FI.cantidad, FI.bonificacion, FI.nombre, FI.descripcion, FI.precio, FI.total_con_iva, ";
      $sql.= " FI.tipo, FI.tipo_cantidad "; // EL TIPO ES USADO COMO ESTADO (0=PENDIENTE, 1=LISTO)
      $sql.= "FROM facturas_items FI ";
      $sql.= " INNER JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
      $sql.= "WHERE FI.id_empresa = $r->id_empresa AND FI.id_factura = $r->id ";
      $sql.= " AND A.no_totalizar_reparto = 1 "; // Solo aquellos productos que deben ser COCINADOS
      $sql.= " AND FI.tipo = 0 "; // Si no esta realizado
      $qq = $this->db->query($sql);
      // Si el pedido no tiene ningun articulo para que vaya a la cocina
      if ($qq->num_rows() == 0) continue;

      $r->items = array();
      $pendiente = 0;
      foreach($qq->result() as $rr) {
        if ($rr->tipo == 0) $pendiente = 1;
        $r->items[] = $rr;
      }
      $r->id_tipo_estado = ($pendiente==1)?0:1; // Si hay algun pendiente, al pedido le falta

      // Si es una MESA
      if ($r->tipo == "M") {
        $sql = "SELECT * FROM res_mesas ";
        $sql.= "WHERE id_empresa = $r->id_empresa AND id = $r->id_referencia ";
        $qq = $this->db->query($sql);
        if ($qq->num_rows()>0) {
          $mesa = $qq->row();
            $r->nombre = "Mesa $mesa->nombre";
        } else {
          $r->nombre = "Mesa";
        }
      } else if ($r->tipo == "D") {
        $r->nombre = "Delivery";
      } else if ($r->tipo == "T") {
        $r->nombre = "Mostrador";
      }

      $lista[] = $r;
    }
    $resultado["results"] = $lista;
    return $resultado;
  }
  
}