<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Ingreso_Proveedor_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("ingresos_proveedores","id","fecha DESC");
	}

  function existe_comprobante($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : 0;
    $id_almacen = isset($config["id_almacen"]) ? $config["id_almacen"] : 0;
    $numero_remito = isset($config["numero_remito"]) ? $config["numero_remito"] : 0;
    $id = isset($config["id"]) ? $config["id"] : 0;
    $sql = "SELECT * FROM ingresos_proveedores ";
    $sql.= " WHERE id_empresa = $id_empresa ";
    if (!empty($id_proveedor)) $sql.= " AND id_proveedor = $id_proveedor ";
    if (!empty($id_almacen)) $sql.= " AND id_almacen = $id_almacen ";
    if (!empty($numero_remito)) $sql.= " AND numero_remito = $numero_remito ";
    $q = $this->db->query($sql);
    return (($q->num_rows()>0) ? 1 : 0);
  }
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

  function confirmar($ingreso,$linea) {

    if (!isset($ingreso->estado) || $ingreso->estado == 0) return FALSE;
    if (!isset($ingreso->id)) return FALSE;
    if (!isset($ingreso->id_empresa)) return FALSE;

    if (!isset($linea->bonificado)) $linea->bonificado = 0;
    if (!isset($linea->no_editar_precios)) $linea->no_editar_precios = 0;
    if (!isset($linea->no_editar_stock)) $linea->no_editar_stock = 0;

    $this->load->model("Stock_Model");
    if ($linea->no_editar_stock == 0) {
      $this->Stock_Model->agregar($linea->id_articulo,$linea->cantidad,$ingreso->id_almacen,$ingreso->fecha,"Ingreso $ingreso->numero_remito",$ingreso->id_proveedor);

      // Recalculamos el stock
      $this->Stock_Model->recalcular_stock(array(
        "id_articulo"=>$linea->id_articulo,
        "id_sucursal"=>$ingreso->id_almacen,
        "id_empresa"=>$ingreso->id_empresa,
      ));

    }

    $this->load->model("Articulo_Model");
    // Si se cambio el precio, cambiamos la fecha de movimiento
    if ($linea->bonificado == 0 && $linea->no_editar_precios == 0) {
      
      if ($this->Articulo_Model->existe_cambio_precio(array(
        "id"=>$ingreso->id,
        "precio_final"=>$linea->precio_final,
        "costo_neto"=>$linea->costo_neto,
        "id_sucursal"=>$ingreso->id_almacen,
      ))) {
        $fecha_mov = date("Y-m-d");
        $last_update = time();
      } else {
        $last_update = "";
      }

      if ($ingreso->id_almacen != 0) {

        $this->load->model("Centro_Costo_Model");
        $this->load->model("Almacen_Model");
        $almacen = $this->Almacen_Model->get($ingreso->id_almacen);
        $almacenes = $this->Centro_Costo_Model->get_sucursales($almacen->id_centro_costo);
        $almacenes_array = array();
        foreach ($almacenes as $alm) {
          $almacenes_array[] = $alm->id;
        }
        $almacenes_string = implode(",", $almacenes_array);

        // Actualizamos el precio de todas las sucursales que tengan el mismo centro de costos de esa sucursal
        $sql = "UPDATE articulos_precios_sucursales APC SET ";
        if (!empty($last_update)) $sql.= " APC.fecha_mov = '$fecha_mov', APC.last_update = '$last_update', ";

        if ($ingreso->id_empresa == 868) {
          // en caso de MEGASHOP CENTRAL, los costos de la sucursal son los precios de venta de CENTRAL
          $central_costo_neto_inicial = $linea->costo_neto_inicial;
          $central_costo_neto = $linea->costo_neto;
          $central_costo_final = $linea->costo_final;
          $central_precio_neto = $linea->precio_neto;
          $central_porc_ganancia = $linea->porc_ganancia;
          $linea->precio_neto_central = $linea->precio_final_central / ((100+$linea->porc_iva)/100); 
          $linea->costo_neto_inicial = $linea->precio_final_central / ((100+$linea->porc_iva)/100);
          $linea->costo_neto = $linea->precio_final_central / ((100+$linea->porc_iva)/100);
          $linea->costo_final = $linea->precio_final_central;
          $linea->precio_neto = $linea->precio_final * ((100-$linea->porc_iva)/100);
          $linea->porc_ganancia = $linea->porc_ganancia_sucursal;
        }

        if (isset($linea->costo_neto_inicial)) $sql.= " APC.costo_neto_inicial = '$linea->costo_neto_inicial', ";

        // En MEGASHOP, poner los descuentos de los proveedores como string en el custom_1
        if ($ingreso->id_empresa == 249 || $ingreso->id_empresa == 868) {
          $descuento_total = 0;
          $dto_array = array();
          if (isset($linea->dto_prov) && $linea->dto_prov > 0) { $dto_array[] = $linea->dto_prov."%"; $descuento_total+= $linea->dto_prov; }
          if (isset($linea->dto_prov_2) && $linea->dto_prov_2 > 0) { $dto_array[] = $linea->dto_prov_2."%"; $descuento_total+= $linea->dto_prov_2; }
          if (isset($linea->dto_prov_3) && $linea->dto_prov_3 > 0) { $dto_array[] = $linea->dto_prov_3."%"; $descuento_total+= $linea->dto_prov_3; }
          if (isset($linea->dto_prov_4) && $linea->dto_prov_4 > 0) { $dto_array[] = $linea->dto_prov_4."%"; $descuento_total+= $linea->dto_prov_4; }
          if (isset($linea->dto_prov_5) && $linea->dto_prov_5 > 0) { $dto_array[] = $linea->dto_prov_5."%"; $descuento_total+= $linea->dto_prov_5; }
          if (sizeof($dto_array)>0) {
            $dto_string = implode(" + ", $dto_array);
            $sql.=" APC.custom_1 = '$dto_string', "; // Se usa para el globito de los distintos descuentos del producto  
          }
        }

        // Juntamos todos los descuentos
        if ($ingreso->id_empresa == 868) {
          $linea->dto_prov = (isset($central_costo_neto_inicial) && $central_costo_neto_inicial != 0) ? ((1 - ($central_costo_neto / $central_costo_neto_inicial)) * 100) : 0;
        } else {
          $linea->dto_prov = (isset($linea->costo_neto_inicial) && $linea->costo_neto_inicial != 0) ? ((1 - ($linea->costo_neto / $linea->costo_neto_inicial)) * 100) : 0;  
          if (isset($linea->dto_prov)) $sql.= " APC.dto_prov = '$linea->dto_prov', ";
        }        
        $sql.= " APC.id_tipo_alicuota_iva = '$linea->id_tipo_alicuota_iva', ";
        $sql.= " APC.porc_iva = '$linea->porc_iva', ";
        $sql.= " APC.costo_neto = '$linea->costo_neto', ";
        $sql.= " APC.costo_final = '$linea->costo_final', ";
        $sql.= " APC.porc_ganancia = '$linea->porc_ganancia', ";
        $sql.= " APC.precio_neto = '$linea->precio_neto', ";
        $sql.= " APC.precio_final = '$linea->precio_final', ";
        $sql.= " APC.precio_final_dto = $linea->precio_final * ((100-APC.porc_bonif)/100) ";
        $sql.= "WHERE APC.id_empresa = $ingreso->id_empresa ";
        $sql.= "AND APC.id_articulo = $linea->id_articulo ";
        $sql.= "AND APC.id_sucursal IN ($almacenes_string) ";
        $this->db->query($sql);
      }

      // Actualizamos el precio del articulo
      $sql = "UPDATE articulos SET ";
      if (!empty($last_update)) $sql.= " fecha_mov = '$fecha_mov', last_update = '$last_update', ";
      if (isset($linea->costo_neto_inicial)) $sql.= " costo_neto_inicial = '$linea->costo_neto_inicial', ";
      if (isset($linea->dto_prov)) $sql.= " dto_prov = '$linea->dto_prov', ";    
      $sql.= " id_tipo_alicuota_iva = '$linea->id_tipo_alicuota_iva', ";
      $sql.= " porc_iva = '$linea->porc_iva', ";
      $sql.= " costo_neto = '$linea->costo_neto', ";
      $sql.= " costo_final = '$linea->costo_final', ";
      $sql.= " porc_ganancia = '$linea->porc_ganancia', ";
      $sql.= " precio_neto = '$linea->precio_neto', ";
      $sql.= " precio_final = '$linea->precio_final', ";
      $sql.= " precio_final_dto = $linea->precio_final * ((100-porc_bonif)/100) ";
      $sql.= "WHERE id_empresa = $ingreso->id_empresa ";
      $sql.= "AND id = $linea->id_articulo ";
      $this->db->query($sql);

      if ($ingreso->id_empresa == 868) {
        // Actualizamos los costos y precios de CENTRAL
        $sql = "UPDATE articulos_precios_sucursales SET ";
        if (!empty($last_update)) $sql.= " fecha_mov = '$fecha_mov', last_update = '$last_update', ";
        $sql.= " costo_neto_inicial = '$central_costo_neto_inicial', ";
        if (isset($linea->dto_prov)) $sql.= " dto_prov = '$linea->dto_prov', ";    
        $sql.= " id_tipo_alicuota_iva = '$linea->id_tipo_alicuota_iva', ";
        $sql.= " porc_iva = '$linea->porc_iva', ";
        $sql.= " costo_neto = '$central_costo_neto', ";
        $sql.= " costo_final = '$central_costo_final', ";
        $sql.= " porc_ganancia = '$central_porc_ganancia', ";
        $sql.= " precio_neto = '$central_precio_neto', ";
        $sql.= " precio_final = '$linea->precio_final_central', ";
        $sql.= " precio_final_dto = $linea->precio_final_central * ((100-porc_bonif)/100) ";
        $sql.= "WHERE id_empresa = $ingreso->id_empresa ";
        $sql.= "AND id_articulo = $linea->id_articulo ";
        $sql.= "AND id_sucursal = 531 ";
        $this->db->query($sql);

        // Actualizamos el costo final de central en el articulo (custom_1)
        $sql = "UPDATE articulos SET custom_1 = '$central_costo_final' ";
        $sql.= "WHERE id_empresa = $ingreso->id_empresa ";
        $sql.= "AND id = $linea->id_articulo ";
        $this->db->query($sql);
      }
    }

    if ($ingreso->id_empresa == 868) $this->guardar_cuenta_corriente($ingreso);

    $this->load->model("Empresa_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($ingreso->id_empresa);
    // Si el articulo esta compartido en mercadolibre
    if ($usa_meli) {
      $this->Articulo_Model->update_publicacion_mercadolibre($linea->id_articulo);
    }
    
    return TRUE;
  }


  function guardar_cuenta_corriente($ingreso) {
    // MEGASHOP CENTRAL: Se crean dos remitos en la cuenta de NAZARENO,
    // uno con el costo, y otro con la venta,
    // ambos asociados a este ingreso por si despues se elimina 
    // "custom_1" tiene el id_ingreso
    // "custom_2" tiene el id_proveedor original
    if ($ingreso->id_empresa == 868) {
      $id_proveedor = 2112; // ID PROVEEDOR NAZARENO

      // Primero controlamos que no se haya guardado anteriormente, para evitar duplicados
      $sql = "SELECT * FROM compras ";
      $sql.= "WHERE id_empresa = $ingreso->id_empresa ";
      $sql.= "AND id_sucursal = $ingreso->id_almacen ";
      $sql.= "AND custom_1 = '$ingreso->id' AND custom_2 = '$ingreso->id_proveedor' ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) return;

      $movimiento = substr($ingreso->fecha, 5, 2).substr($ingreso->fecha, 2, 2);
      $this->load->model("Proveedor_Model");
      $prov = $this->Proveedor_Model->get($ingreso->id_proveedor,array(
        "id_empresa"=>868
      ));
      /*
      $observaciones = "Costo Mercaderia $prov->nombre";
      $sql = "INSERT INTO compras (";
      $sql.= " id_proveedor, fecha, id_tipo_comprobante, incluido_libro_iva, numero_1, numero_2, ";
      $sql.= " movimiento, total_general, total_neto, subtotal, compra_real, forma_pago, observaciones, ";
      $sql.= " estado, id_empresa, id_sucursal, custom_1, custom_2 ";
      $sql.= ") VALUES ( ";
      $sql.= " $id_proveedor, '$ingreso->fecha', 999, 0, '1', '$ingreso->numero_remito', ";
      $sql.= " '$movimiento', $ingreso->total, $ingreso->total, $ingreso->total, 1, 'C', '$observaciones', ";
      $sql.= " 0, $ingreso->id_empresa, $ingreso->id_almacen, '$ingreso->id', '$ingreso->id_proveedor') ";
      $this->db->query($sql);
      */

      $this->load->model("Almacen_Model");
      $suc = $this->Almacen_Model->get($ingreso->id_almacen);
      $observaciones = "$suc->nombre: $prov->nombre (Remito: $ingreso->numero_remito)";
      $observaciones = str_replace("'", "", $observaciones);
      $observaciones = str_replace("\"", "", $observaciones);
      $venta = $ingreso->valor - $ingreso->total;
      $sql = "INSERT INTO compras (";
      $sql.= " id_proveedor, fecha, id_tipo_comprobante, incluido_libro_iva, numero_1, numero_2, ";
      $sql.= " movimiento, total_general, total_neto, subtotal, compra_real, forma_pago, observaciones, ";
      $sql.= " estado, id_empresa, id_sucursal, custom_1, custom_2 ";
      $sql.= ") VALUES ( ";
      $sql.= " $id_proveedor, '$ingreso->fecha', 999, 0, '2', '$ingreso->numero_remito', ";
      $sql.= " '$movimiento', $venta, $venta, $venta, 1, 'C', '$observaciones', ";
      $sql.= " 0, $ingreso->id_empresa, $ingreso->id_almacen, '$ingreso->id', '$ingreso->id_proveedor') ";
      $this->db->query($sql);
    }
  }

  function buscar($config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $estado = isset($config["estado"]) ? $config["estado"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $codigo_articulo = (isset($config["codigo_articulo"])) ? urldecode($config["codigo_articulo"]) : "";
    $in_ids_estados = isset($config["in_ids_estados"]) ? $config["in_ids_estados"] : "";
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(S.nombre IS NULL,'',S.nombre) AS almacen, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS proveedor, ";
    $sql.= " IF(A.id_proveedor = 0,'',IF(C.nombre IS NULL,'',C.nombre)) AS proveedor, ";
    $sql.= " IF(A.fecha='0000-00-00','',DATE_FORMAT(A.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM ingresos_proveedores A ";
    $sql.= "LEFT JOIN almacenes S ON (A.id_almacen = S.id AND A.id_empresa = S.id_empresa) ";
    $sql.= "LEFT JOIN proveedores C ON (A.id_proveedor = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= " AND (A.numero_remito = '$filter' OR C.nombre LIKE '%$filter%') ";
    if (!empty($id_proveedor)) $sql.= " AND A.id_proveedor = $id_proveedor ";
    if (!empty($id_sucursal)) $sql.= " AND A.id_almacen = $id_sucursal ";
    if (!empty($desde)) $sql.= " AND A.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= " AND A.fecha <= '$hasta' ";
    if (!empty($in_ids_estados)) $sql.= " AND A.estado IN ($in_ids_estados) ";
    if ($estado != -1) $sql.= "AND A.estado = $estado ";

    if (!empty($codigo_articulo)) {
      $sql.= "AND EXISTS (";
      $sql.= " SELECT 1 FROM ingresos_proveedores_items FI INNER JOIN articulos ART ON (FI.id_articulo = ART.id AND FI.id_empresa = ART.id_empresa) WHERE ";
      $sql.= " FI.id_empresa = A.id_empresa AND FI.id_ingreso = A.id ";
      $sql.= " AND ART.codigo = '$codigo_articulo' ";
      $sql.= ") ";
    }

    $sql.= "ORDER BY A.fecha DESC, A.id DESC ";
    if (!empty($offset)) $sql.= "LIMIT $limit,$offset ";
    $query = $this->db->query($sql);
    $result = $query->result();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$result,
      "total"=>$total->total,
    );
  }


  function get($id) {
    
    $id_empresa = parent::get_empresa();
    
    $sql = "SELECT F.*, ";
    $sql.= " IF(A.nombre IS NULL,'',A.nombre) AS almacen, ";
    $sql.= " IF(P.nombre IS NULL,'',P.nombre) AS proveedor, ";
    $sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM ingresos_proveedores F ";
    $sql.= " LEFT JOIN proveedores P ON (F.id_proveedor = P.id AND F.id_empresa = P.id_empresa) ";
    $sql.= " LEFT JOIN almacenes A ON (F.id_almacen = A.id AND F.id_empresa = A.id_empresa) ";
    $sql.= "WHERE F.id = $id ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    $row = $query->row();
    
    if (!empty($row)) {
      // Tomamos los items
      $sql = "SELECT FI.*, ";
      $sql.= " IF(A.nombre IS NULL,'',A.nombre) AS nombre, ";
      $sql.= " IF(A.codigo_barra IS NULL,'',A.codigo_barra) AS codigo_barra, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo ";
      $sql.= "FROM ingresos_proveedores_items FI ";
      $sql.= " LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
      $sql.= "WHERE FI.id_ingreso = $id ";
      $sql.= "AND FI.id_empresa = $id_empresa ";
      $sql.= "ORDER BY FI.orden ASC";
      $q = $this->db->query($sql);
      foreach($q->result() as $rr) {
        $sql = "SELECT * FROM articulos_proveedores ";
        $sql.= "WHERE id_empresa = $row->id_empresa ";
        $sql.= "AND id_proveedor = $row->id_proveedor ";
        $sql.= "AND id_articulo = $rr->id_articulo ";
        $qq = $this->db->query($sql);
        $rr->codigo_prov = "";
        foreach($qq->result() as $rrr) {
          $rr->codigo_prov .= $rrr->codigo."\n";
        }
      }
      $row->items = $q->result();
    }
    
    $this->db->close();
    return $row;
  }


}