<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Reparto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("repartos","id","fecha DESC, numero DESC");
	}
    
  function get($fecha = "",$numero = 0) {

    $id_empresa = parent::get_empresa();
    $sql = "SELECT FI.id_articulo, ";
    $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
    $sql.= " IF(A.uxb IS NULL,'',A.uxb) AS uxb, ";
    $sql.= " IF(A.nombre IS NULL,'',A.nombre) AS descripcion, F.reparto, ";
    $sql.= " IF(A.no_totalizar_reparto IS NULL,0,A.no_totalizar_reparto) AS no_totalizar_reparto, ";
    $sql.= " SUM(IF(bonificacion = 0 AND cantidad > 0,cantidad,0)) AS facturado, ";
    $sql.= " SUM(IF(bonificacion > 0,cantidad,0)) AS bonificacion, ";
    $sql.= " SUM(IF(cantidad < 0,ABS(cantidad),0)) AS devolucion ";
    $sql.= "FROM facturas F INNER JOIN facturas_items FI ON (F.id = FI.id_factura AND F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta) ";
    $sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND F.id_empresa = A.id_empresa) ";
    $sql.= "WHERE F.anulada = 0 ";
    $sql.= "AND FI.id_empresa = $id_empresa ";
    if (!empty($fecha)) $sql.= "AND F.fecha_reparto = '$fecha' ";
    if (!empty($numero)) $sql.= "AND F.reparto = $numero ";
    $sql.= "GROUP BY FI.id_articulo ";
    if ($id_empresa == 229 || $id_empresa == 230 || $id_empresa == 1355) {
      $sql.= "ORDER BY CAST(A.custom_1 AS SIGNED) ASC ";
    //} else {
      //$sql.= "ORDER BY FI.orden ASC ";
    }
    $q = $this->db->query($sql);
    $result = $q->result();
    $salida = array();
    
    // Recorremos la lista, y nos fijamos si el articulo hay que agruparlo o no para el reparto
    $i=0;
    foreach($result as $l) {
        
      $salida[] = $l;
      
      if ($l->no_totalizar_reparto == 1) {
          
        $sql = "SELECT FI.* ";
        $sql.= "FROM facturas F INNER JOIN facturas_items FI ON (F.id = FI.id_factura AND F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta) ";
        $sql.= "WHERE F.anulada = 0 ";
        $sql.= "AND F.id_empresa = $id_empresa ";
        $sql.= "AND FI.id_articulo = $l->id_articulo ";
        if (!empty($fecha)) $sql.= "AND F.fecha_reparto = '$fecha' ";
        if (!empty($numero)) $sql.= "AND F.reparto = $numero ";
        $q = $this->db->query($sql);
        $cantidades = array();
        foreach($q->result() as $r){
          $cantidades[] = number_format($r->cantidad,2);
        }
        sort($cantidades);
        
        // Creamos una nueva fila para el reparto
        $o = new stdClass();
        $o->id_articulo = "";
        $o->codigo = "";
        $o->descripcion = implode(", ",$cantidades);
        $o->facturado = 0;
        $o->bonificacion = 0;
        $o->devolucion = 0;
        $o->uxb = 1;
        $o->reparto = $numero;
        $salida[] = $o; // Lo agregamos al array
      }
      $i++;
    }
    
    return $salida;
  }
	
}