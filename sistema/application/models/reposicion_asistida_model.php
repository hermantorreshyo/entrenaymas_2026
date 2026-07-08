<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Reposicion_Asistida_Model extends Abstract_Model {
	
	function __construct() {}

  // =================================================
  // Funciones que se utilizan en el mismo modelo

  const CLASE_A = 10;
  const CLASE_B = 5;
  const CLASE_C = 3;

  // Consulta la fecha de ultimo pedido
  private function get_fecha_ultimo_pedido($params = array()) {
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : 0;
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;    
    $id_proveedor = isset($params["id_proveedor"]) ? $params["id_proveedor"] : 0;
    $fecha_ultimo_pedido = "0000-00-00";
    $sql = "SELECT fecha FROM ped_pedidos_proveedores ";
    $sql.= "WHERE id_proveedor = $id_proveedor ";
    $sql.= "AND id_sucursal = $id_sucursal ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q_ultimo_pedido = $this->db->query($sql);
    if ($q_ultimo_pedido->num_rows() > 0) {
      $r_ultimo_pedido = $q_ultimo_pedido->row();
      $fecha_ultimo_pedido = $r_ultimo_pedido->fecha;
    }
    return $fecha_ultimo_pedido;
  }

  // Consulta si hay articulos por debajo del stock minimo
  private function consulta_stock_minimo($params = array()) {
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : 0;
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;    
    $id_proveedor = isset($params["id_proveedor"]) ? $params["id_proveedor"] : 0;
    $clase_art = isset($params["clase_art"]) ? $params["clase_art"] : 0;
    $sql = "SELECT * FROM reposicion_asistida ";
    $sql.= "WHERE id_proveedor = $id_proveedor ";
    $sql.= "AND id_sucursal = $id_sucursal ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "AND stock_actual <= stock_minimo ";
    if ($clase_art != 0) $sql.= "AND clase_art = '$clase_art' ";
    $q = $this->db->query($sql);
    return $q->result();
  }

  // Consulta si hay articulos sin stock
  private function consulta_sin_stock($params = array()) {
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : 0;
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;    
    $id_proveedor = isset($params["id_proveedor"]) ? $params["id_proveedor"] : 0;
    $clase_art = isset($params["clase_art"]) ? $params["clase_art"] : 0;
    $sql = "SELECT * FROM reposicion_asistida ";
    $sql.= "WHERE id_proveedor = $id_proveedor ";
    $sql.= "AND id_sucursal = $id_sucursal ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "AND stock_actual = 0 ";
    if ($clase_art != 0) $sql.= "AND clase_art = '$clase_art' ";
    $q = $this->db->query($sql);
    return $q->result();
  }

  // =================================================  

  // Muestra el listado de todos los proveedores
  // e indica si se debe hacer un pedido o no
  function ver_proveedores($params = array()) {

    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : 0;
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;
    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $tipo = isset($params["tipo"]) ? $params["tipo"] : 0;
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 30;
    // Recorremos los proveedores
    $sql = "SELECT SQL_CALC_FOUND_ROWS * ";
    $sql.= "FROM proveedores ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND tipo_proveedor = '1' "; // Que sean de mercaderia
    if (!empty($filter)) $sql.= "AND nombre LIKE '%$filter%' ";
    if (!empty($tipo)) $sql.= "AND tipo = '$tipo' ";
    $sql.= "ORDER BY tipo DESC "; // Se ordena por TIPO
    $salida = array();
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    foreach($q->result() as $prov) {

      // Flag que indica si debemos realizar el pedido o no
      $prov->pedir = 0;

      // Controlamos si ya deberiamos pedir al proveedor porque se paso de fecha
      $prov->fecha_ultimo_pedido = $this->get_fecha_ultimo_pedido(array(
        "id_proveedor"=>$prov->id,
        "id_sucursal"=>$id_sucursal,
        "id_empresa"=>$id_empresa,
      ));
      if ($prov->fecha_ultimo_pedido != "0000-00-00") {
        $date_desde = new DateTime($prov->fecha_ultimo_pedido);
        $date_hasta = new DateTime(date("Y-m-d"));
        $dias = $date_hasta->diff($date_desde)->format("%a");
        $frecuencia = (int)$prov->frecuencia;
        if ($dias > $frecuencia) {
          $prov->pedir = 1;
        }
      }

      // Si el proveedor es clase A
      if ($prov->tipo == self::CLASE_A) {
        // Si hay productos clase A por debajo del stock minimo, entonces tendriamos que hacer un pedido
        $arts = $this->consulta_stock_minimo(array(
          "id_empresa"=>$id_empresa,
          "id_proveedor"=>$prov->id,
          "id_sucursal"=>$id_sucursal,
          "clase_art"=>self::CLASE_A,
        ));
        if (sizeof($arts)>0) $prov->pedir = 1;

      // Si es un proveedor clase B
      } else if ($prov->tipo == self::CLASE_B) {
        // Si 
      }

      $salida[] = $prov;
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  // Muestra un pedido sugerido para un proveedor y una sucursal en particular
  function ver_pedido_sugerido($params = array()) {

    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : 0;
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;
    $id_proveedor = isset($params["id_proveedor"]) ? $params["id_proveedor"] : 0;

    $sql = "SELECT * ";
    $sql.= "FROM reposicion_asistida ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_proveedor = $id_proveedor ";
    $sql.= "AND id_sucursal = $id_sucursal ";
    $q = $this->db->query($sql);
    return $q->result();
  }






  // Esta funcion calcula todas las noches los datos utilizados para la reposicion asistida
  // La tabla utilizada es "reposicion_asistida"
	function calcular($params = array()) {

    $this->load->model("Stock_Model");
    $this->load->model("Venta_Model");
		$id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : 0;

    // Primero limpiamos la tabla para volverla a llenar
    $this->db->query("DELETE FROM reposicion_asistida WHERE id_empresa = $id_empresa ");

    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa ";
    if ($id_empresa == 868) $sql.= "AND id != 531 "; // No tomamos CENTRAL
    $q = $this->db->query($sql);
    $almacenes = array();
    foreach($q->result() as $alm) {
      $almacenes[] = $alm;
    }

    // Recorremos los proveedores
    $sql = "SELECT * FROM proveedores WHERE id_empresa = $id_empresa ";
    $sql.= "AND tipo_proveedor = 1 "; // Que sean de mercaderia
    $q = $this->db->query($sql);
    foreach($q->result() as $prov) {

      // Recorremos las sucursales
      foreach($almacenes as $alm)  {

        // Tomamos el ultimo pedido hecho a esa sucursal
        $fecha_ultimo_pedido = $this->get_fecha_ultimo_pedido(array(
          "id_sucursal"=>$alm->id,
          "id_empresa"=>$id_empresa,
          "id_proveedor"=>$prov->id,
        ));

        // Consultamos el stock de los articulos
        $sql = "SELECT AP.id_articulo, S.stock_actual, S.stock_minimo, ";
        $sql.= " A.nombre, A.codigo, AP.codigo AS codigo_prov, A.tipo AS clase_art ";
        $sql.= "FROM articulos_proveedores AP ";
        $sql.= "INNER JOIN stock S ON (AP.id_empresa = S.id_empresa AND S.id_articulo = AP.id_articulo) ";
        $sql.= "INNER JOIN articulos A ON (AP.id_articulo = A.id AND AP.id_empresa = A.id_empresa) ";
        $sql.= "WHERE AP.id_empresa = $id_empresa ";
        $sql.= "AND AP.id_proveedor = $prov->id ";
        $sql.= "AND S.id_sucursal = $alm->id";
        $q_stock = $this->db->query($sql);
        foreach($q_stock->result() as $art) {

          $cantidad_ultimo_pedido = 0;
          // Consultamos la ultima cantidad pedida

          // Consultamos la venta, desde el ultimo pedido hasta hoy
          $cantidad_vendida = 0;
          $venta_diaria = 0;
          $venta = $this->Venta_Model->articulos(array(
            "id_sucursal"=>$alm->id,
            "id_empresa"=>$id_empresa,
            "id_articulo"=>$art->id_articulo,
            "desde"=>$fecha_ultimo_pedido,
            "hasta"=>date("Y-m-d"),
          ));
          if (sizeof($venta["results"])>0) {
            $v = $venta["results"][0];
            $cantidad_vendida = $v->cantidad;
          }

          // Calculamos la cantidad SUGERIDA
          $sugerido = $cantidad_vendida + $art->stock_minimo - $art->stock_actual;
          if ($sugerido < 0) $sugerido = 0;

          // Insertamos el registro en la tabla
          $sql = "INSERT INTO reposicion_asistida (";
          $sql.= " id_empresa,id_articulo,id_sucursal,id_proveedor,";
          $sql.= " ultimo_pedido,pedido,venta,venta_diaria,";
          $sql.= " stock_actual,stock_minimo,clase_prov,clase_art,sugerido,";
          $sql.= " proveedor,sucursal,articulo,codigo_articulo,codigo_prov";
          $sql.= ") VALUES(";
          $sql.= " '$id_empresa','$art->id_articulo','$alm->id','$prov->id', ";
          $sql.= " '$fecha_ultimo_pedido','$cantidad_ultimo_pedido','$cantidad_vendida','$venta_diaria', ";
          $sql.= " '$art->stock_actual', '$art->stock_minimo', '$prov->tipo', '$art->clase_art', '$sugerido', ";
          $sql.= " '$prov->nombre','$alm->nombre','$art->nombre','$art->codigo','$art->codigo_prov' ";
          $sql.= ")";
          $this->db->query($sql);
        }
      }
    }
	}

}