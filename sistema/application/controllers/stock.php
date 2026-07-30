<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Stock extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Stock_Model', 'modelo');
  }

  function edicion_stock() {
    $id_empresa = parent::get_empresa();
    $id_articulo = parent::get_post("id_articulo",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $id_variante = parent::get_post("id_variante",0);
    $stock = parent::get_post("stock",0);
    $id_usuario = (isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : 0);
    $this->modelo->ajustar_stock(array(
      "id_articulo"=>$id_articulo,
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "id_variante"=>$id_variante,
      "cantidad"=>$stock,
    ));
    echo json_encode(array("error"=>0));
  }

  function export($id_empresa = 0,$id_sucursal = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM stock A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function recalcular_de_ventas() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    set_time_limit(0);
    $this->load->helper("fecha_helper");
    $id_sucursal = 702;
    $id_articulo = 306603;
    $id_empresa = 249;
    $sql = "SELECT F.id, F.fecha, F.comprobante, FI.* ";
    $sql.= "FROM facturas F INNER JOIN facturas_items FI ON (F.id_empresa = FI.id_empresa AND F.id = FI.id_factura AND F.id_punto_venta = FI.id_punto_venta) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_sucursal = $id_sucursal ";
    $sql.= "AND F.fecha >= '2018-01-01' ";
    $sql.= "AND F.anulada = 0 AND FI.anulado = 0 ";
    $sql.= "AND FI.id_articulo != 0 ";
    $sql.= "AND FI.id_articulo = $id_articulo ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $fecha_es = fecha_es($r->fecha);
      $sql = "SELECT * FROM stock_movimientos ";
      $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $r->id_articulo ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "AND fecha = '$r->fecha' ";
      $sql.= "AND detalle = '$r->comprobante - $fecha_es' ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() == 0) {
        $sql = "INSERT INTO stock_movimientos (id_sucursal, id_articulo, movimiento, fecha, cantidad, detalle, id_empresa) VALUES (";
        $sql.= "$id_sucursal, $id_articulo, 'B', '$r->fecha', '$r->cantidad', '$r->comprobante - $fecha_es', $id_empresa)";
        $this->db->query($sql);
      }
    }
    $this->modelo->recalcular_stock(array(
      "id_articulo"=>$id_articulo,
      "id_sucursal"=>$id_sucursal,
      "id_empresa"=>$id_empresa
    ));
    echo "TERMINO";
  }

  function registrar_costo_final() {
    set_time_limit(0);
    $id_empresa = 249;
    $id_sucursal = 16;
    $sql = "SELECT * FROM temporal WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $this->db->query("UPDATE stock_movimientos SET costo_final = $r->costo_final WHERE id_sucursal = $r->id_sucursal AND id_empresa = $r->id_empresa AND id_articulo = $r->id_articulo AND fecha = '$r->fecha' ");
    }
    echo "TERMINO";
  }

  // CON ESTA FUNCION VAMOS REGISTRANDO EL STOCK DIA POR DIA DE LAS SUCURSALES
  // PARA TENER UN HISTORICO SIN LA NECESIDAD DE CALCULAR TODO DIA POR DIA
  function registrar_historia_stock() {
    set_time_limit(0);
    $fecha = date("Y-m-d");
    $sql = "SELECT id_sucursal, id_empresa ";
    $sql.= "FROM stock ";
    $sql.= "WHERE id_sucursal != 0 ";
    $sql.= "GROUP BY id_empresa, id_sucursal ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      // Consultamos si usa precios de sucursales
      $sql = "SELECT 1 FROM articulos_precios_sucursales WHERE id_empresa = $row->id_empresa AND id_sucursal = $row->id_sucursal ";
      $qq = $this->db->query($sql);

      $sql = "SELECT SUM(A.costo_final * S.stock_actual) AS costo_final, ";
      $sql.= " SUM(S.stock_actual) AS total_unidades, ";
      $sql.= " SUM(A.precio_final_dto * S.stock_actual) AS precio_final ";
      if ($qq->num_rows()>0) {
        $sql.= "FROM stock S INNER JOIN articulos_precios_sucursales A ON (S.id_empresa = A.id_empresa AND S.id_sucursal = A.id_sucursal AND S.id_articulo = A.id_articulo) ";
      } else {
        $sql.= "FROM stock S INNER JOIN articulos A ON (S.id_empresa = A.id_empresa AND S.id_articulo = A.id) ";
      }
      $sql.= "WHERE S.id_empresa = $row->id_empresa ";
      $sql.= "AND S.id_sucursal = $row->id_sucursal ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $rr->costo_final = is_null($rr->costo_final) ? 0 : $rr->costo_final;
      $rr->total_unidades = is_null($rr->total_unidades) ? 0 : $rr->total_unidades;
      $rr->precio_final = is_null($rr->precio_final) ? 0 : $rr->precio_final;

      $sql = "INSERT INTO stock_historial (id_empresa,id_sucursal,fecha,costo_final,total_unidades,precio_final) VALUES ( ";
      $sql.= "'$row->id_empresa','$row->id_sucursal','$fecha','$rr->costo_final','$rr->total_unidades','$rr->precio_final') ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }

  /*
  function test() {

    $id_empresa = 249;
    $punto_venta = 23;
    $id_articulo = 10228863;
    $desde = "2019-04-06";

    $this->load->helper("fecha_helper");
    $sql = "SELECT FI.id_articulo,FI.id_factura, TC.negativo, F.fecha, ";
    $sql.= " FI.cantidad, APV.id_almacen, APV.id_punto_venta, F.comprobante, ";
    $sql.= " FI.custom_3, "; // Si es 1, indica que el stock tiene que reservarse
    $sql.= " FI.id_variante ";
    $sql.= "FROM facturas_items FI ";
    $sql.= " INNER JOIN facturas F ON (FI.id_factura = F.id AND FI.id_punto_venta = F.id_punto_venta AND F.id_empresa = FI.id_empresa) ";
    $sql.= " INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql.= " INNER JOIN almacenes_puntos_venta APV ON (FI.id_punto_venta = APV.id_punto_venta AND FI.id_empresa = APV.id_empresa) ";
    $sql.= " INNER JOIN puntos_venta PV ON (APV.id_punto_venta = PV.id AND FI.id_empresa = PV.id_empresa) ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND PV.numero = $punto_venta ";
    $sql.= "AND FI.id_articulo = $id_articulo ";
    $sql.= "AND F.fecha >= '$desde' ";
    //$sql.= "AND FI.uploaded = 0 "; // Las que fueron subidas recien y todavia no se procesaron
    $sql.= "AND FI.anulado = 0 "; // Los items que no fueron anulados
    $q = $this->db->query($sql);
    if ($q->num_rows()==0) return FALSE;

    foreach($q->result() as $row) {
      // Si no existe un ajuste posterior a la fecha
      $sql = "SELECT * ";
      $sql.= "FROM stock_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_sucursal = $row->id_almacen ";
      $sql.= "AND movimiento = 'M' ";
      $sql.= "AND fecha > '$row->fecha' ";
      $sql.= "AND id_articulo = $row->id_articulo "; 
      $qq = $this->db->query($sql);
      if ($qq->num_rows()==0) {
        if ($row->id_articulo != 0) {
          $detalle = $row->comprobante." - ".fecha_es($row->fecha);
          if ($row->negativo == 0 && $row->cantidad > 0) {
            echo "SACAR $row->id_articulo $row->id_almacen $row->cantidad $row->fecha $detalle $row->id_variante <br/>";
            $this->modelo->sacar($row->id_articulo,$row->cantidad,$row->id_almacen,'B',$row->fecha,$detalle,0,$row->id_variante);
          } else {
            $row->cantidad = abs($row->cantidad);
            $this->modelo->agregar($row->id_articulo,$row->cantidad,$row->id_almacen,$row->fecha,$detalle,0,$row->id_variante);
          }
        }
      }
    }
    echo "TERMINO";
  }
  */

  function corregir_stock() {
    $this->load->model("Stock_Model");
    $id_empresa = 249;

    $this->Stock_Model->recalcular_stock(array(
      "id_articulo"=>206806,
      "id_sucursal"=>14,
      "id_empresa"=>$id_empresa
    ));
    exit();

    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa AND id = 11 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $suc) {
      $sql = "SELECT * FROM stock S ";
      $sql.= "WHERE S.id_empresa = $id_empresa ";
      $sql.= "AND S.id_sucursal = $suc->id ";
      $sql.= "AND S.stock_actual != (";
      $sql.= " SELECT SM.saldo FROM stock_movimientos SM ";
      $sql.= " WHERE SM.id_empresa = S.id_empresa ";
      $sql.= " AND SM.id_sucursal = S.id_sucursal ";
      $sql.= " AND SM.id_articulo = S.id_articulo ";
      $sql.= " ORDER BY SM.fecha DESC, SM.id DESC ";
      $sql.= " LIMIT 0,1 ";
      $sql.= ") ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $r) {
        echo "Suc: $suc->nombre - $r->id_articulo <br/>";
        $this->Stock_Model->recalcular_stock(array(
          "id_articulo"=>$r->id_articulo,
          "id_sucursal"=>$suc->id,
          "id_empresa"=>$id_empresa
        ));
      }
    }
    echo "TERMINO";
  }


  // Funcion utilizada para dar de alta el stock a muchos productos a la vez
  function masivo() {
    $id_empresa = parent::get_empresa();
    $id_sucursal = parent::get_post("id_sucursal",0);
    $cantidad = parent::get_post("cantidad",0);
    $operacion = parent::get_post("operacion","ajuste");
    $ids = parent::get_post("ids","");
    $array = explode("-", $ids);

    if (empty($id_sucursal)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se eligio una sucursal.",
      ));
      exit();
    }
    if (empty($array)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se seleccionaron articulos.",
      ));
      exit();
    }

    // Recorremos los IDS
    foreach($array as $id) {
      
      if ($operacion == "alta") {
        $this->modelo->agregar($id,$cantidad,$id_sucursal);        

      } else if ($operacion == "baja") {
        $this->modelo->sacar($id,$cantidad,$id_sucursal);

      } else if ($operacion == "ajuste") {
        $this->modelo->ajustar_stock(array(
          "id_empresa"=>$id_empresa,
          "id_articulo"=>$id,
          "cantidad"=>$cantidad,
          "id_sucursal"=>$id_sucursal,
        ));
      }
    }
    echo json_encode(array(
      "error"=>0,
    ));
  }

  // FUNCION QUE SE UTILIZA PARA PASAR DEL STOCK POR ARTICULO AL STOCK POR SUCURSAL
  function pasar_metodo_stock() {
    $id_empresa = 65;
    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    $r = $q->row();
    $id_sucursal = $r->id;
    $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $this->modelo->ajustar_stock(array(
        "id_articulo"=>$r->id,
        "cantidad"=>99,//$r->stock,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
      ));
    }
    echo "TERMINO";
  }

  function recalcular_desde_variantes() {
    $this->modelo->recalcular_desde_variantes(array(
      "id_empresa"=>1282
    ));
    echo "TERMINO";
  }

  function mover_stock() {
    $fecha = '2017-10-26';
    $sql = "SELECT * FROM stock_movimientos where id_empresa = 224 and fecha = '$fecha' and movimiento = 'A' and id_sucursal = 4";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $this->modelo->sacar($row->id_articulo,$row->cantidad,$row->id_sucursal,'B',$fecha,'Ajuste por error');
      $this->modelo->agregar($row->id_articulo,$row->cantidad,6,$fecha);
    }
    echo "TERMINO";
  }

  function procesar($id_empresa,$punto_venta) {
    $this->modelo->procesar($id_empresa,$punto_venta);
  }
  
  function pedido() {
    $filter = $this->input->get("filter");
    $id_proveedor = $this->input->get("id_proveedor");
    $id_rubro = $this->input->get("id_rubro");
    $id_sucursal = $this->input->get("id_sucursal");
    
    $this->load->model("Venta_Model");
    if (!empty($filter)) $filter = urldecode($filter);
    $array = $this->modelo->ver(array(
      "filter"=>$filter,
      "id_proveedor"=>$id_proveedor,
      "id_rubro"=>$id_rubro,
      "id_sucursal"=>$id_sucursal,
    ));
    $articulos = $array["results"];
    
    // Recorremos los articulos, y consultamos su venta
    // entre las dos fechas dadas
    foreach($articulos as $a) {
      $r = $this->Venta_Model->totales(array(
        "id_producto"=>$a->id
      ));
      $v = $r["results"];
      print_r($v);
    }
  }


  /*
  ====================
  CONSULTA PARA SABER SI LOS MOVIMIENTOS DE STOCK COINCIDEN CON EL STOCK ACTUAL
  ====================
  
  SELECT SM.*, S.stock_actual FROM stock_movimientos SM INNER JOIN (select MAX(id) AS id from stock_movimientos GROUP BY id_articulo) S ON (SM.id = S.id) INNER JOIN stock S ON (S.id_articulo = SM.id_articulo) WHERE SM.id_empresa = 134 AND SM.saldo != S.stock_actual
  
  */
  function valoracion($fecha = '', $id_sucursal = 0) {
    
    $id_empresa = $this->get_empresa();
    if (empty($fecha)) $fecha = date("Y-m-d");
    else {
      // Formateamos la fecha
      $this->load->helper("fecha_helper");
      $fecha = fecha_mysql($fecha);
    }
    $res = $this->modelo->valoracion(array(
      "id_empresa"=>$id_empresa,
      "fecha"=>$fecha,
      "id_sucursal"=>$id_sucursal,
    ));
    echo json_encode(array(
      "datos"=>$res,
    ));
  }
 

  function eliminar_negativos() {
    $id_empresa = 133;
    $id_sucursal = 55;
    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa AND id = $id_sucursal ";
    $q = $this->db->query($sql);
    foreach($q->result() as $alm) {
      $sql = "SELECT * FROM stock WHERE id_empresa = $id_empresa AND id_sucursal = $alm->id AND stock_actual < 0";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $r) {
        $this->modelo->ajustar_stock(array(
          "id_articulo"=>$r->id_articulo,
          "id_sucursal"=>$alm->id,
          "id_empresa"=>$id_empresa,
          "fecha"=>date("Y-m-d"),
          "cantidad"=>0,
        ));
      }
    }
    echo "TERMINO";
  }    
  
  function en_cero() {
    $id_empresa = 1355;
    $id_sucursal = 1034;
    $sql = "SELECT A.id FROM articulos A WHERE A.id_empresa = $id_empresa ";
    //$sql = "SELECT A.id FROM articulos A WHERE A.id_empresa = $id_empresa AND NOT EXISTS (SELECT 1 FROM stock S WHERE S.id_articulo = A.id AND S.id_empresa = $id_empresa AND S.id_sucursal = $id_sucursal)";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $this->modelo->ajustar_stock(array(
        "id_articulo"=>$r->id,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "cantidad"=>0,
      ));
    }
    echo "TERMINO";
    /*
    $this->modelo->inicializar(array(
      "id_empresa"=>393,
      "id_sucursal"=>84,
      "cantidad"=>4,
      //"sin_stock"=>0,
    ));
    */
  }  
  
  /**
   * Elimina los movimientos de un determinado dia y sucursal
   * y vuelve ajustar el stock del producto
   */
  function eliminar_movimientos() {
  
    $movimiento = 'B';
    // Seleccionamos los movimientos de ese tipo y con esa fecha
    $sql = "SELECT * FROM stock_movimientos ";
    $sql.= "WHERE movimiento = '$movimiento' ";
    $sql.= "AND fecha >= '2014-06-01' ";
    $sql.= "AND fecha <= '2014-07-02' ";
    $q = $this->db->query($sql);
    
    foreach($q->result() as $r) {
      $sql = "UPDATE stock ";
      
      // Si estamos borrando una baja, sumamos la cantidad
      if ($movimiento == 'B') {
      $sql.= "SET stock_actual = stock_actual + $r->cantidad ";
      
      // Si estamos borrando una alta, restamos la cantidad
      } else if ($movimiento == 'A') {
      $sql.= "SET stock_actual = stock_actual - $r->cantidad ";
      }
      $sql.= "WHERE id_articulo = $r->id_articulo ";
      $this->db->query($sql);
      
      // Borramos los movimientos seleccionados
      $sql = "DELETE FROM stock_movimientos WHERE id = $r->id ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }
  
  function recalcular() {
    set_time_limit(0);
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = 249;
    $sucursales = array(7,18,17,16,15,14,12,11,8,23,22,20,21,19,56,223,224);
    $sql = "SELECT id FROM articulos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      foreach($sucursales as $id_sucursal) {
        $this->modelo->recalcular_stock(array(
          "id_empresa"=>$id_empresa,
          "id_articulo"=>$row->id,
          "id_sucursal"=>$id_sucursal,
        ));      
      }
    }
    echo "TERMINO";
  }
  

  function ingresar_vendidos() {
    set_time_limit(0);
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = 249;
    $id_articulo = "10213586";

    $sql = "DELETE FROM stock_movimientos WHERE movimiento = 'B' AND id_articulo = '$id_articulo' AND id_empresa = $id_empresa ";
    $this->db->query($sql);
    $sucursales = array(7,8,18,17,16,15,14,12,11,23,22,20,21,19,56);
    foreach($sucursales as $id_sucursal) {
      $sql = "SELECT * FROM facturas_items FI INNER JOIN facturas F ON (F.id = FI.id_factura AND F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta) ";
      $sql.= "WHERE F.id_sucursal = $id_sucursal AND FI.id_articulo = $id_articulo AND F.id_empresa = $id_empresa AND F.anulada = 0 AND FI.anulado = 0";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        foreach($qq->result() as $rr) {
          $sql = "INSERT INTO stock_movimientos (id_sucursal,id_articulo,movimiento,fecha,cantidad,id_empresa,detalle) VALUES(";
          $sql.= "$id_sucursal,$rr->id_articulo,'B','$rr->fecha','$rr->cantidad',$id_empresa,'$rr->comprobante') ";
          $this->db->query($sql);
          //echo $rr->id_articulo." ".$rr->cantidad." $rr->fecha <br/>";
        }
        // Volvemos a recalcular
        $this->modelo->recalcular_stock(array(
          "id_articulo"=>$id_articulo,
          "id_empresa"=>$id_empresa,
          "id_sucursal"=>$id_sucursal,
        ));
      }
    }
    echo "TERMINO";
  }
  
  function insert() {
    $array = $this->parse_put();
    $this->load->model("Articulo_Model");

    $fecha = "";
    if (isset($array->fecha) && strpos($array->fecha, "/")>0) {
      $this->load->helper("fecha_helper");
      $fecha = fecha_mysql($array->fecha);
    }

    // Si la empresa tiene MercadoLibre activo
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($id_empresa);
    
    // Recorremos los items
    foreach($array->items as $item) {

      $id_articulo = $item->id;
      $id_variante = (isset($item->id_variante) ? $item->id_variante : 0);
      
      if ($item->movimiento == "A") {
        $this->modelo->agregar($item->id,$item->cantidad,$array->id_sucursal,$fecha,"",$array->id_proveedor,$id_variante);

      } else if ($item->movimiento == "B" || $item->movimiento == "R") {
        $this->modelo->sacar($item->id,$item->cantidad,$array->id_sucursal,$item->movimiento,$fecha,"",$array->id_proveedor,$id_variante);

      } else if ($item->movimiento == "M") {
        $this->modelo->ajustar($item->id,$item->cantidad,$array->id_sucursal,$fecha,$array->id_proveedor,$id_variante);
      }

      // Si el articulo esta compartido en mercadolibre
      if ($usa_meli) {
        $this->Articulo_Model->update_publicacion_mercadolibre($id_articulo);
      }

    }
    echo json_encode(array());
  }

  function update($id) {
    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }
    // El STOCK NO SE PUEDE MODIFICAR, siempre se inserta
  }  


  function get_params() {
    $this->load->helper("fecha_helper");
    $filter = $this->input->get("filter");
    $id_sucursal = $this->input->get("id_sucursal");
    $id_rubro = $this->input->get("id_rubro");
    $id_proveedor = $this->input->get("id_proveedor");
    $codigo_prov = parent::get_get("codigo_prov",0);
    $id_marca = $this->input->get("id_marca");
    $desde = ($this->input->get("desde") !== FALSE) ? fecha_mysql($this->input->get("desde")) : "";
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = parent::get_get("order_by","");
    $order = parent::get_get("order","ASC");
    if (!empty($order_by) && !empty($order)) $order_by = $order_by." ".$order;
    else $order_by = "";
    $filtro_stock = parent::get_get("filtro_stock",0);
    return array(
      "filter"=>$filter,
      "filtro_stock"=>$filtro_stock,
      "id_sucursal"=>$id_sucursal,
      "id_rubro"=>$id_rubro,
      "id_proveedor"=>$id_proveedor,
      "id_marca"=>$id_marca,
      "desde"=>$desde,
      "limit"=>$limit,
      "offset"=>$offset,
      "order_by"=>$order_by,
      "codigo_prov"=>$codigo_prov,
    );
  }

  function ver() {
    $conf = $this->get_params();
    $array = $this->modelo->ver($conf);
    echo json_encode($array);
  }

  function exportar_excel() {

    $id_empresa = parent::get_empresa();
    $conf = $this->get_params();
    $conf["limit"] = 0;
    $conf["offset"] = 99999999999;
    $salida = $this->modelo->ver($conf);

    $resultado = array();
    $header = array();
    $header[] = "Sucursal";
    $header[] = "Cod.";
    $header[] = "Cod. Barra";
    if ($id_empresa == 249 || $id_empresa == 868 || $id_empresa == 356) $header[] = "Prov.";
    $header[] = "Descripcion";
    $header[] = "Unidades";
    $header[] = "Stk. Min.";
    $header[] = "Costo Unit.";
    $header[] = "Valoracion";
    $header[] = "Ult. Alta";
    $header[] = "Ult. Baja";

    $this->load->model("Configuracion_Model");
    $cotizacion = $this->Configuracion_Model->get_cotizacion(array(
      "id_empresa"=>$id_empresa,
    ));

    foreach($salida["results"] as $r) {
      $row = new stdClass();
      $res = array();
      $res["almacen"] = $r->almacen;
      $res["codigo"] = $r->codigo;
      $res["codigo_barra"] = str_replace("###", " | ", $r->codigo_barra);
      if ($id_empresa == 249 || $id_empresa == 868 || $id_empresa == 356) $res["codigo_prov"] = $r->custom_10;
      $res["nombre"] = $r->nombre;
      $res["stock_actual"] = $r->stock_actual;
      $res["stock_minimo"] = $r->stock_minimo;
      $res["costo_final"] = ($r->moneda == 'U$S') ? ($r->costo_final * $cotizacion) : $r->costo_final;
      $res["valoracion"] = ($r->moneda == 'U$S') ? ($r->costo_final * $cotizacion * $r->stock_actual) : ($r->costo_final * $r->stock_actual);
      $res["fecha_ult_compra"] = $r->fecha_ult_compra;
      $res["fecha_ult_venta"] = $r->fecha_ult_venta;
      $resultado[] = $res;
    }
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>date("d/m/Y"),
      "filename"=>"stock",
      "header"=>$header,
      "footer"=>array(),
      "data"=>$resultado,
      "title"=>"Stock",
    ));
  }
  
  
  function detalle() {
    $id_articulo = parent::get_post("id_articulo",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $desde = parent::get_post("desde",date("d/m/Y"));
    $hasta = parent::get_post("hasta",date("d/m/Y"));
    $this->load->helper("fecha_helper");
    $desde = fecha_mysql($desde);
    $hasta = fecha_mysql($hasta);
    $array = $this->modelo->detalle(array(
      "id_articulo"=>$id_articulo,
      "id_sucursal"=>$id_sucursal,
      "desde"=>$desde,
      "hasta"=>$hasta,
    ));
    echo json_encode($array);
  }
  
  function modificar_stock_minimo() {
    $id_empresa = parent::get_empresa();
    $id_articulo = parent::get_post("id_articulo",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $stock_minimo = parent::get_post("stock_minimo",0);
    $sql = "UPDATE stock SET stock_minimo = '$stock_minimo' ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_articulo = $id_articulo ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }
  
  function delete($id = null) {
  //$this->db->query("DELETE FROM faltantes WHERE id = $id ");
    echo json_encode(array());
  }
  
  function consulta($codigo='',$id_sucursal = 1) {
    $codigo = urldecode($codigo);
    $articulo = $this->modelo->consulta($codigo,$id_sucursal);
    if ($articulo === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El articulo con el codigo: '$codigo' no esta habilitado para gestionar stock."
      ));
    } else {
      echo json_encode(array(
        "error"=>0,
        "articulo"=>$articulo,
      ));
    }
  }

  function consulta_por_id($id=0,$id_sucursal = 1) {
    $articulo = $this->modelo->consulta("",$id_sucursal,$id);
    if ($articulo === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El articulo no esta habilitado para gestionar stock."
      ));
    } else {
      echo json_encode(array(
        "error"=>0,
        "articulo"=>$articulo,
      ));
    }
  }  
  
  function imprimir() {
  
    $id_proveedor = $this->input->get("id_proveedor");
    $id_sucursal = $this->input->get("id_sucursal");
    $id_rubro = $this->input->get("id_rubro");
    $id_marca = $this->input->get("id_marca");
    $filter = $this->input->get("texto");
    $id_desde = ($this->input->get("id_desde") !== FALSE) ? $this->input->get("id_desde") : 0;
    $this->load->helper("fecha_helper");
    $desde = ($this->input->get("desde") !== FALSE) ? fecha_mysql($this->input->get("desde")) : "";
    $tipo_listado = ($this->input->get("p") !== FALSE) ? $this->input->get("p") : 1;
    $filtro_stock = parent::get_get("filtro_stock",0);

    $params = array(
      "id_proveedor"=>$id_proveedor,
      "id_sucursal"=>$id_sucursal,
      "id_rubro"=>$id_rubro,
      "id_marca"=>$id_marca,
      "id_desde"=>$id_desde,
      "desde"=>$desde,
      "filter"=>$filter,
      "filtro_stock"=>$filtro_stock,
    );
    
    if ($tipo_listado == 1) {
      $array = $this->modelo->ver($params);
      $listado = "reports/stock";
    } else if ($tipo_listado == 2) {
      $listado = "reports/stock_movimientos";
      $array = $this->modelo->ver_movimiento($params);
    } else {
      exit();
    }
    
    // Tomamos los datos del proveedor
    if (!empty($id_proveedor)) {
      $this->load->model("Proveedor_Model");
      $proveedor = $this->Proveedor_Model->get($id_proveedor);    
    } else {
      $proveedor = false;
    }
    
    // Tomamos los datos del almacen
    if (!empty($id_sucursal)) {
      $this->load->model("Almacen_Model");
      $almacen = $this->Almacen_Model->get($id_sucursal);
    } else {
      $almacen = false;
    }
    
    // Agrupamos los items de acuerdo al rubro
    $set = array();
    foreach($array["results"] as $item) {
      if (!isset($set[$item->rubro])) {
        $set[$item->rubro] = array();
      }
      $set[$item->rubro][] = $item;
    }
    ksort($set);
    
    $header = $this->load->view("reports/header",null,true);
    
    $this->load->view($listado,array(
      "resultados"=>$set,
      "header"=>$header,
      "proveedor"=>$proveedor,
      "almacen"=>$almacen,
      "id_sucursal"=>$id_sucursal,
    ));
  }
  
  
  function importar() {
  
  $this->load->model("Stock_Model");
  
  // Recorremos los archivos subidos
  for($i=0;$i<sizeof($_FILES["stocks"]["tmp_name"]);$i++) {
    
    $nombre = $_FILES["stocks"]["name"][$i];
    $file = fopen($_FILES["stocks"]["tmp_name"][$i],"r");
    
    // Obtenemos el numero de la sucursal
    $suc = str_replace("stock_","",$nombre);
    $suc = str_replace(".txt","",$suc);
    
    $stock = array();
    
    // Leemos cada linea del archivo
    while(($linea = fgets($file)) !== FALSE) {
    
    $campos = explode(";",$linea);
    $codigo = mb_convert_encoding($campos[0], 'ISO-8859-1', 'UTF-8');
    $cantidad = mb_convert_encoding($campos[1], 'ISO-8859-1', 'UTF-8');
    $movimiento = mb_convert_encoding($campos[2], 'ISO-8859-1', 'UTF-8');
    $codigo = trim(str_replace("?","",$codigo));
    $cantidad = trim(str_replace("?","",$cantidad));
    $movimiento = trim(str_replace("?","",$movimiento));
    
    $encontro = FALSE;
    if (sizeof($stock)>0) {
      foreach($stock as $s) {
      if ($s["codigo"] == $codigo) {
        //echo "$codigo repetido<br/>";    
        $encontro = TRUE;
        break;
      }
      }      
    }
    if (!$encontro) {
      $stock[] = array(
      "codigo"=>$codigo,
      "cantidad"=>$cantidad,
      "movimiento"=>$movimiento
      );
      
      // Si la cantidad es cero, el articulo no se ingresa
      if ($cantidad != 0) {
      // Dependiendo del tipo de movimiento, se modifica el stock
      if ($movimiento == "A") {
        $this->Stock_Model->agregar($codigo,$cantidad,$suc);
      } else if ($movimiento == "M") {
        $this->Stock_Model->ajustar($codigo,$cantidad,$suc);
      } else if ($movimiento == "B" || $movimiento == "R") {
        $this->Stock_Model->sacar($codigo,$cantidad,$suc);
      }
      }
      echo "$codigo - $cantidad<br/>";
    }
    } // Fin del archivo
    fclose($file);
    
  } // Fin de for de archivos
  
  // Si termino todo bien, redireccionamos al listado de pedidos
  //header("Location: app/#stock");
  }
  
}