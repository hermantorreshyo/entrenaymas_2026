<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Stock_Model extends Abstract_Model {

  private $id_empresa = 0;

  function __construct() {
    parent::__construct("stock","id");
  }

  function get_historial($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $sql = "SELECT * FROM stock_historial ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_sucursal = $id_sucursal ";
    if (!empty($fecha)) $sql.= "AND fecha = '$fecha' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      return $r;
    } else return FALSE;
  }

  function recalcular_desde_variantes($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_articulo = isset($config["id_articulo"]) ? $config["id_articulo"] : 0;
    $sql = "SELECT ";
    $sql.= " IF(SUM(stock_actual) IS NULL,0,SUM(stock_actual)) AS stock_actual, ";
    $sql.= " IF(SUM(reservado) IS NULL,0,SUM(reservado)) AS reservado, id_articulo ";
    $sql.= "FROM stock_variantes ";    
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
    if (!empty($id_articulo)) $sql.= "AND id_articulo = $id_articulo ";
    $sql.= "GROUP BY id_articulo ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "UPDATE stock SET stock_actual = $row->stock_actual, reservado = $row->reservado ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_articulo = $row->id_articulo ";
      if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
      $this->db->query($sql);
    }
  }

  // Recorre todos los articulos habilitados para gestionar el stock, y pone en cero los movimientos
  function inicializar($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $cantidad = isset($config["cantidad"]) ? $config["cantidad"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $sin_stock = isset($config["sin_stock"]) ? $config["sin_stock"] : 1;
    $sql = "SELECT A.id FROM articulos A WHERE A.usa_stock = 1 ";
    if ($sin_stock == 1) $sql.= "AND NOT EXISTS (SELECT 1 FROM stock S WHERE S.id_articulo = A.id AND S.id_empresa = A.id_empresa AND id_sucursal = $id_sucursal) ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {

      // Controlamos si tiene variantes
      $sql = "SELECT * FROM articulos_variantes WHERE id_empresa = $id_empresa AND id_articulo = $r->id";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        foreach($qq->result() as $rr) {
          $this->ajustar_stock(array(
            "id_articulo"=>$r->id,
            "cantidad"=>$cantidad,
            "id_empresa"=>$id_empresa,
            "id_sucursal"=>$id_sucursal,
            "id_variante"=>$rr->id,
            "fecha"=>$fecha,
          ));          
        }
      } else {
        $this->ajustar_stock(array(
          "id_articulo"=>$r->id,
          "cantidad"=>$cantidad,
          "id_empresa"=>$id_empresa,
          "id_sucursal"=>$id_sucursal,
          "id_variante"=>$rr->id,
          "fecha"=>$fecha,
        ));
      }
    }
    return true;
  }

  function recalcular_stock($config = array()) {
    $id_articulo = $config["id_articulo"];
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $agregar_stock_cero = isset($config["agregar_stock_cero"]) ? $config["agregar_stock_cero"] : 1; // Agrega el articulo con 0 de stock si no encuentra movimientos
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $saldo = 0;
    $sql = "SELECT * FROM stock_movimientos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_articulo = $id_articulo ";
    $sql.= "AND id_sucursal = $id_sucursal ";
    $sql.= "ORDER BY fecha ASC, id ASC ";
    $qq = $this->db->query($sql);
    if ($qq->num_rows() == 0) {
      if ($agregar_stock_cero == 1) {
        $this->ajustar_stock(array(
          "id_articulo"=>$id_articulo,
          "id_empresa"=>$id_empresa,
          "id_sucursal"=>$id_sucursal,
          "cantidad"=>0,
        ));
      }
    } else {
      foreach($qq->result() as $rr) {
        if ($rr->movimiento == "A") {
          $saldo = $saldo + $rr->cantidad;
        } else if ($rr->movimiento == "M") {
          $saldo = $rr->cantidad;
        } else if ($rr->movimiento == "B" || $rr->movimiento == "R") {
          $saldo = $saldo - $rr->cantidad;
        }
        $sql = "UPDATE stock_movimientos SET saldo = $saldo WHERE id = $rr->id AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND id_articulo = $id_articulo ";
        $this->db->query($sql);
      }
      // Finalmente ponemos el saldo al articulo
      $sql = "UPDATE stock SET stock_actual = $saldo ";
      $sql.= "WHERE id_articulo = $id_articulo ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "AND id_sucursal = $id_sucursal ";
      $this->db->query($sql);
    }
  }

  // Ajusta el stock que se lleva del articulo global, sumando el stock de las variantes
  function ajustar_calcular_desde_variantes($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_articulo = isset($config["id_articulo"]) ? $config["id_articulo"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    if ($id_articulo == 0) return;
    $sql = "UPDATE stock A SET ";
    $sql.= " A.stock_actual = (";
    $sql.= " SELECT IF(SUM(S.stock_actual) IS NULL,0,SUM(S.stock_actual)) AS stock FROM stock_variantes S ";
    $sql.= "  WHERE S.id_empresa = $id_empresa AND S.id_articulo = $id_articulo ";
    if (!empty($id_sucursal)) $sql.= "AND S.id_sucursal = $id_sucursal ";
    $sql.= "  LIMIT 0,1 ";
    $sql.= "), ";
    $sql.= " A.reservado = (";
    $sql.= " SELECT IF(SUM(S.reservado) IS NULL,0,SUM(S.reservado)) AS reservado FROM stock_variantes S ";
    $sql.= "  WHERE S.id_empresa = $id_empresa AND S.id_articulo = $id_articulo ";
    if (!empty($id_sucursal)) $sql.= "AND S.id_sucursal = $id_sucursal ";
    $sql.= "  LIMIT 0,1 ";
    $sql.= ") ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "AND A.id_articulo = $id_articulo ";
    if (!empty($id_sucursal)) $sql.= "AND A.id_sucursal = $id_sucursal ";
    $this->db->query($sql);
  }

  function valoracion($config = array()) {
    // Indica si usa precios por sucursales
    $usa_precios_sucursales = (($id_empresa == 224 || $id_empresa == 249 || $id_empresa == 868) ? 1 : 0);

    $fecha = $config["fecha"];
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;

    $total = 0;
    $salida = array();
    $sql = "SELECT id_articulo, MAX(id) AS id ";
    $sql.= "FROM stock_movimientos ";
    $sql.= "WHERE id_empresa = $id_empresa AND fecha <= '$fecha' ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
    $sql.= "GROUP BY id_articulo";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "SELECT ";
      $sql.= " A.id AS id_articulo, A.codigo, A.nombre, A.uxb, SM.saldo AS cantidad, SM.costo_final ";
      /*
      if ($usa_precios_sucursales == 1) {
        $sql.= " IF(APS.costo_neto IS NULL,A.costo_neto,APS.costo_neto) AS costo_neto, ";
        $sql.= " IF(APS.costo_final IS NULL,A.costo_final,APS.costo_final) AS costo_final, ";
        $sql.= " IF(APS.precio_final_dto IS NULL,A.precio_final_dto,APS.precio_final_dto) AS precio_final, ";
      } else {
        $sql.= " A.costo_neto, A.costo_final, A.precio_final_dto AS precio_final ";
      }
      */
      $sql.= "FROM stock_movimientos SM ";
      $sql.= "INNER JOIN articulos A ON (A.id = SM.id_articulo AND A.id_empresa = SM.id_empresa) ";
      /*
      if ($usa_precios_sucursales == 1) {
        $sql.= "LEFT JOIN articulos_precios_sucursales APS ON (A.id = APS.id_articulo AND A.id_empresa = APS.id_empresa AND APS.id_sucursal = $id_sucursal) ";
      }
      */
      $sql.= "WHERE SM.id = $row->id ";
      $sql.= "AND SM.id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $total = $total + ((float)($rr->cantidad * $rr->costo_final));
      /*
      $rr->costo_neto = 0; //(float)($rr->cantidad * $rr->costo_neto);
      $rr->costo_final = (float)($rr->cantidad * $rr->costo_final);
      $rr->precio_final = 0; //(float)($rr->cantidad * $rr->precio_final);
      $salida[] = $rr;
      */
    }
    return $total;
    //return $salida;

    /*
    $sql = "SELECT ";
    $sql.= "SM.id, A.codigo AS codigo, A.nombre, A.uxb, SM.saldo AS cantidad, ";
    $sql.= "DATE_FORMAT(SM.fecha,'%d/%m/%Y') AS fecha, ";
    if ($usa_precios_sucursales == 1) {
      $sql.= "(APS.costo_neto * SM.saldo) AS costo_neto, ";
      $sql.= "(APS.costo_final * SM.saldo) AS costo_final, ";
      $sql.= "(APS.precio_final_dto * SM.saldo) AS precio ";      
    } else {
      $sql.= "(A.costo_neto * SM.saldo) AS costo_neto, ";
      $sql.= "(A.costo_final * SM.saldo) AS costo_final, ";
      $sql.= "(A.precio_final * SM.saldo) AS precio ";      
    }
    $sql.= "FROM stock_movimientos SM ";
    $sql.= "INNER JOIN (";
    $sql.= "  SELECT MAX(id) AS id FROM stock_movimientos ";
    $sql.= "  WHERE fecha <= '$fecha' AND id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
    $sql.= "  GROUP BY id_articulo ";
    $sql.= ") S ON (S.id = SM.id) ";
    $sql.= "INNER JOIN articulos A ON (SM.id_articulo = A.id AND SM.id_empresa = A.id_empresa) ";
    if ($usa_precios_sucursales == 1) {
      $sql.= "LEFT JOIN articulos_precios_sucursales APS ON (A.id = APS.id_articulo AND A.id_empresa = APS.id_empresa AND SM.id_sucursal = APS.id_sucursal) ";
    }
    $sql.= "WHERE SM.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND SM.id_sucursal = $id_sucursal ";
    $sql.= "AND SM.saldo != 0 ";
    $sql.= "ORDER BY A.nombre ASC ";
    $q = $this->db->query($sql);
    return $q->result();
    */
  }

  // Devuelve TRUE si proceso algo, FALSE en caso contrario.
  function procesar($id_empresa=0,$punto_venta=0) {
    set_time_limit(0);
    ini_set('memory_limit','1024M');
    if ($id_empresa==0) return FALSE;
    if ($punto_venta==0) return FALSE;

    $this->load->model("Empresa_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($id_empresa);

    $this->id_empresa = $id_empresa;
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
    $sql.= "AND FI.uploaded = 0 "; // Las que fueron subidas recien y todavia no se procesaron
    $sql.= "AND FI.anulado = 0 "; // Los items que no fueron anulados
    $sql.= "AND FI.tipo_cantidad != 'C' "; // Los cambios no afectan el stock
    $q = $this->db->query($sql);
    if ($q->num_rows()==0) return FALSE;

    foreach($q->result() as $row) {
      // TODO: Probar los casos:
      // Nota de credito
      // Item con cantidad negativa

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

          // Hay que reservar el stock, nada mas
          if ($row->custom_3 == 1) {
            $this->reservar(array(
              "id_articulo"=>$row->id_articulo,
              "cantidad"=>$row->cantidad,
              "id_almacen"=>$row->id_almacen,
              "fecha"=>$row->fecha,
              "detalle"=>$detalle,
              "id_variante"=>$row->id_variante,
            ));
          } else {
            if ($row->negativo == 0 && $row->cantidad > 0) {
              $this->sacar($row->id_articulo,$row->cantidad,$row->id_almacen,'B',$row->fecha,$detalle,0,$row->id_variante);
            } else {
              $row->cantidad = abs($row->cantidad);
              $this->agregar($row->id_articulo,$row->cantidad,$row->id_almacen,$row->fecha,$detalle,0,$row->id_variante);
            }
          }
        }
      }
      // Actualizamos los elementos para que no se vuelvan a procesar
      $sql = "UPDATE facturas SET uploaded = 1 WHERE id = $row->id_factura AND id_empresa = $id_empresa AND id_punto_venta = $row->id_punto_venta ";
      $this->db->query($sql);
      $sql = "UPDATE facturas_items SET uploaded = 1 WHERE id_factura = $row->id_factura AND id_empresa = $id_empresa AND id_punto_venta = $row->id_punto_venta ";
      $this->db->query($sql);
      $sql = "UPDATE facturas_iva SET uploaded = 1 WHERE id_factura = $row->id_factura AND id_empresa = $id_empresa ";
      $this->db->query($sql);        

      // Si el articulo esta compartido en mercadolibre
      if ($usa_meli) {
        $this->load->model("Articulo_Model");
        $this->Articulo_Model->update_publicacion_mercadolibre($row->id_articulo,array(
          "id_empresa"=>$id_empresa,
        ));
      }

    }
    return TRUE;
  }


  // Devuelve TRUE si proceso algo, FALSE en caso contrario.
  function procesar_presupuesto($id,$config = array()) {

    set_time_limit(0);
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    if (empty($id_empresa)) return FALSE;
    if (empty($id_sucursal)) return FALSE;

    $this->id_empresa = $id_empresa;
    $this->load->helper("fecha_helper");
    $sql = "SELECT F.id, FI.id_articulo, FI.id_presupuesto, F.fecha, FI.cantidad, F.numero ";
    $sql.= "FROM presupuestos_items FI ";
    $sql.= " INNER JOIN presupuestos F ON (FI.id_presupuesto = F.id AND F.id_empresa = FI.id_empresa) ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND F.stock = 0 "; // Si todavia no fue procesado
    $sql.= "AND FI.id_presupuesto = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows()==0) return FALSE;

    foreach($q->result() as $row) {
      // Si no existe un ajuste posterior a la fecha
      $sql = "SELECT * ";
      $sql.= "FROM stock_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_sucursal = $id_sucursal ";
      $sql.= "AND movimiento = 'M' ";
      $sql.= "AND fecha > '$row->fecha' ";
      $sql.= "AND id_articulo = $row->id_articulo "; 
      $qq = $this->db->query($sql);
      if ($qq->num_rows()==0) {
        if ($row->id_articulo != 0) {
          $detalle = "Presupuesto ".fecha_es($row->fecha);
          if ($row->cantidad > 0) {
            $this->sacar($row->id_articulo,$row->cantidad,$id_sucursal,'B',$row->fecha,$detalle,0);
          } else {
            $row->cantidad = abs($row->cantidad);
            $this->agregar($row->id_articulo,$row->cantidad,$id_sucursal,$row->fecha,$detalle,0);
          }

          // Por las dudas reajustamos
          $this->recalcular_stock(array(
            "id_articulo"=>$row->id_articulo,
            "id_sucursal"=>$id_sucursal,
            "id_empresa"=>$id_empresa
          ));

        }
      }
      // Actualizamos los elementos para que no se vuelvan a procesar
      $sql = "UPDATE presupuestos SET stock = 1 WHERE id = $row->id AND id_empresa = $id_empresa";
      $this->db->query($sql);
    }
    return TRUE;
  }


  function reservar($conf = array()) {

    $id_empresa = ($this->id_empresa != 0) ? $this->id_empresa : parent::get_empresa();
    $id_articulo = isset($conf["id_articulo"]) ? $conf["id_articulo"] : 0;
    $cantidad = isset($conf["cantidad"]) ? $conf["cantidad"] : 0;
    $id_sucursal = isset($conf["id_almacen"]) ? $conf["id_almacen"] : 0;
    $id_variante = isset($conf["id_variante"]) ? $conf["id_variante"] : 0;
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : date("Y-m-d");
    $detalle = isset($conf["detalle"]) ? $conf["detalle"] : "";
    if ($id_articulo == 0) return;

    // Controlamos que el articulo exista, y que este habilitado para utilizar el stock
    $sql = "SELECT * FROM articulos WHERE id = $id_articulo AND id_empresa = $id_empresa AND usa_stock = 1 LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

      // Si el articulo es COMPUESTO
      if ($this->db->table_exists('articulos_componentes')) {
        $sql = "SELECT * FROM articulos_componentes WHERE id_articulo = $id_articulo AND id_empresa = $id_empresa ";
        $q_compuesto = $this->db->query($sql);
        if ($q_compuesto->num_rows() > 0) {
          // Hacemos la misma operacion pero con cada uno de los componentes
          foreach($q_compuesto->result() as $componente) {
            $conf["id_articulo"] = $componente->id_articulo_componente;
            $conf["cantidad"] = $cantidad * $componente->cantidad;
            $this->reservar($conf);
          }
          return;
        }
      }

      $sql = "UPDATE stock SET reservado = reservado + $cantidad ";
      $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $id_articulo AND id_empresa = $id_empresa ";
      $this->db->query($sql);
      if ($id_variante != 0) {
        $sql = "UPDATE stock_variantes SET reservado = reservado + $cantidad ";
        $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $id_articulo AND id_empresa = $id_empresa AND id_variante = $id_variante ";
        $this->db->query($sql);        
      }
    }
  }


  /**
   * Saca del stock la cantidad de un articulo determinado
   *   @param $id_articulo Articulo
   *   @param $cantidad Cantidad de UNIDADES que se desean sacar
   *   @param $id_sucursal Trabaja sobre el stock de esa sucursal
   *   @param $movimiento Tipo de movimiento. Baja por defecto.
   */
  function sacar($id_articulo,$cantidad,$id_sucursal,$movimiento = 'B',$fecha = "",$detalle = "",$id_proveedor = 0, $id_variante = 0) {

    if (empty($fecha)) $fecha = date("Y-m-d");
    $cantidad = (float) $cantidad;
    $id_empresa = ($this->id_empresa != 0) ? $this->id_empresa : parent::get_empresa();

    // Controlamos que el articulo exista, y que este habilitado para utilizar el stock
    $sql = "SELECT * FROM articulos WHERE id = $id_articulo AND id_empresa = $id_empresa AND usa_stock = 1 LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

      // Si el articulo es COMPUESTO
      if ($this->db->table_exists('articulos_componentes')) {
        $sql = "SELECT * FROM articulos_componentes WHERE id_articulo = $id_articulo AND id_empresa = $id_empresa ";
        $q_compuesto = $this->db->query($sql);
        if ($q_compuesto->num_rows() > 0) {
          // Hacemos la misma operacion pero con cada uno de los componentes
          foreach($q_compuesto->result() as $componente) {
            $this->sacar($componente->id_articulo_componente,$cantidad * $componente->cantidad,$id_sucursal,$movimiento,$fecha,$detalle,$id_proveedor,$id_variante);
          }
          return;
        }
      }

      // Obtenemos el articulo
      $articulo = $q->row();
      // Controlamos si tenemos articulos_precios_sucursales
      $sql = "SELECT * FROM articulos_precios_sucursales ";
      $sql.= "WHERE id_articulo = $id_articulo ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "AND id_sucursal = $id_sucursal ";
      $q_ps = $this->db->query($sql);
      if ($q_ps->num_rows() > 0) {
        $articulo_precio_sucursal = $q_ps->row();
        $costo_final = $articulo_precio_sucursal->costo_final;
      } else {
        $costo_final = $articulo->costo_final;
      }      

      // Obtenemos el stock del articulo en la sucursal
      $sql = "SELECT * FROM stock WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND id_articulo = $id_articulo ORDER BY id DESC LIMIT 0,1";
      $qq = $this->db->query($sql);

      // Si existe el ultimo registro
      if ($qq->num_rows()>0) {
        $ultimo = $qq->row();
        $saldo = $ultimo->stock_actual - $cantidad;
        //if ($saldo < 0) $saldo = 0;

        // Actualizamos el stock del articulo en la sucursal
        $sql = "UPDATE stock SET stock_actual = $saldo ";
        $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $id_articulo AND id_empresa = $id_empresa ";
        $this->db->query($sql);

        // Actualizamos la tabla de MOVIMIENTOS
        $sql = "INSERT INTO stock_movimientos (id_sucursal,id_articulo,movimiento,fecha,cantidad,saldo,id_empresa,detalle,id_variante,costo_final) VALUES (";
        $sql.= "$id_sucursal,$id_articulo,'$movimiento','$fecha',$cantidad,$saldo,$id_empresa,'$detalle','$id_variante','$costo_final')";
        $this->db->query($sql);

        // Ajustamos la variante
        if ($id_variante != 0) {
          $q = $this->db->query("SELECT * FROM stock_variantes WHERE id_empresa = $id_empresa AND id_articulo = $id_articulo AND id_variante = $id_variante AND id_sucursal = $id_sucursal");
          if ($q->num_rows()>0) {
            $sql = "UPDATE stock_variantes SET stock_actual = stock_actual - $cantidad ";
            $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $id_articulo AND id_empresa = $id_empresa AND id_variante = $id_variante ";
          } else {
            $sql = "INSERT INTO stock_variantes (id_empresa,id_articulo,id_variante,id_sucursal,stock_actual) VALUES ($id_empresa,$id_articulo,$id_variante,$id_sucursal,-$cantidad) ";
          }
          $this->db->query($sql);
        }

      }

    }
  }


  /**
   * Agrega el articulo al stock correspondiente
   *   @param $id_articulo Articulo
   *   @param $cantidad Cantidad de UNIDADES que se desean agregar
   *   @param $id_sucursal Trabaja sobre el stock de esa sucursal
   */
  function agregar($id_articulo,$cantidad,$id_sucursal,$fecha="",$detalle = "",$id_proveedor = 0,$id_variante = 0) {

    if (empty($fecha)) $fecha = date("Y-m-d");
    $id_empresa = ($this->id_empresa != 0) ? $this->id_empresa : parent::get_empresa();

    // Controlamos que el articulo exista, y que este habilitado para utilizar el stock
    $sql = "SELECT * FROM articulos WHERE id = $id_articulo AND id_empresa = $id_empresa AND usa_stock = 1 LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

      // Si el articulo es COMPUESTO
      if ($this->db->table_exists('articulos_componentes')) {
        $sql = "SELECT * FROM articulos_componentes WHERE id_articulo = $id_articulo AND id_empresa = $id_empresa ";
        $q_compuesto = $this->db->query($sql);
        if ($q_compuesto->num_rows() > 0) {
          // Hacemos la misma operacion pero con cada uno de los componentes
          foreach($q_compuesto->result() as $componente) {
            $this->agregar($componente->id_articulo_componente,$cantidad * $componente->cantidad,$id_sucursal,$fecha,$detalle,$id_proveedor,$id_variante);
          }
          return;
        }
      }

      // Si hay un ajuste posterior a la fecha, no debemos agregarlo
      $sql = "SELECT 1 FROM stock_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND id_articulo = $id_articulo ";
      if ($id_variante != 0) $sql.= "AND id_variante = $id_variante ";
      $sql.= "AND fecha > '$fecha' AND movimiento = 'M' ";
      file_put_contents("log_stock_model.txt", $sql."\n", FILE_APPEND);
      $q_ajuste = $this->db->query($sql);
      if ($q_ajuste->num_rows() > 0) { return; }

      // Obtenemos el articulo
      $articulo = $q->row();
      // Controlamos si tenemos articulos_precios_sucursales
      $sql = "SELECT * FROM articulos_precios_sucursales ";
      $sql.= "WHERE id_articulo = $id_articulo ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "AND id_sucursal = $id_sucursal ";
      $q_ps = $this->db->query($sql);
      if ($q_ps->num_rows() > 0) {
        $articulo_precio_sucursal = $q_ps->row();
        $costo_final = $articulo_precio_sucursal->costo_final;
      } else {
        $costo_final = $articulo->costo_final;
      }      

      // Obtenemos el stock del articulo en la sucursal
      $q = $this->db->query("SELECT * FROM stock WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND id_articulo = $id_articulo ORDER BY id DESC LIMIT 0,1");

      // Si existe el ultimo registro
      if ($q->num_rows()>0) {
        $ultimo = $q->row();
        $cantidad_anterior = $ultimo->stock_actual;
        $saldo = (float)($cantidad_anterior + $cantidad);

        // Actualizamos el stock del articulo en la sucursal
        $sql = "UPDATE stock SET stock_actual = $saldo ";
        $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $id_articulo AND id_empresa = $id_empresa ";
        $this->db->query($sql);

        // Si no existe el registro
      } else {
        $saldo = $cantidad;
        // Insertamos el stock del articulo en la sucursal
        $sql = "INSERT INTO stock (id_articulo,id_sucursal,stock_actual,id_empresa) VALUES ( ";
        $sql.= "$id_articulo,$id_sucursal,$cantidad,$id_empresa) ";
        $this->db->query($sql);
      }

      // Actualizamos la tabla de MOVIMIENTOS
      $sql = "INSERT INTO stock_movimientos (id_sucursal,id_articulo,movimiento,fecha,cantidad,saldo,id_empresa,id_proveedor,detalle,id_variante,costo_final) VALUES (";
      $sql.= "$id_sucursal,$id_articulo,'A','$fecha',$cantidad,$saldo,$id_empresa,$id_proveedor,'$detalle','$id_variante','$costo_final')";
      $this->db->query($sql);

      // Ajustamos la variante
      if ($id_variante != 0) {
        $q = $this->db->query("SELECT * FROM stock_variantes WHERE id_empresa = $id_empresa AND id_articulo = $id_articulo AND id_variante = $id_variante AND id_sucursal = $id_sucursal");
        if ($q->num_rows()>0) {
          $sql = "UPDATE stock_variantes SET stock_actual = stock_actual + $cantidad ";
          $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $id_articulo AND id_empresa = $id_empresa AND id_variante = $id_variante ";
        } else {
          $sql = "INSERT INTO stock_variantes (id_empresa,id_articulo,id_variante,id_sucursal,stock_actual) VALUES ($id_empresa,$id_articulo,$id_variante,$id_sucursal,$cantidad) ";
        }
        $this->db->query($sql);
      }

    }
  }


  /**
   * Ajusta el stock a la cantidad indicada
   *   @param $id_articulo Articulo
   *   @param $cantidad Cantidad de UNIDADES que se desean agregar
   *   @param $id_sucursal Trabaja sobre el stock de esa sucursal
   */
  function ajustar($id_articulo,$cantidad,$id_sucursal,$fecha="",$id_proveedor=0,$id_variante = 0) {

    if (empty($fecha)) $fecha = date("Y-m-d");
    $id_empresa = ($this->id_empresa != 0) ? $this->id_empresa : parent::get_empresa();

    // Controlamos que el articulo exista, y que este habilitado para utilizar el stock
    $sql = "SELECT * FROM articulos WHERE id = $id_articulo AND usa_stock = 1 LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

      // Si el articulo es COMPUESTO
      if ($this->db->table_exists('articulos_componentes')) {
        $sql = "SELECT * FROM articulos_componentes WHERE id_articulo = $id_articulo AND id_empresa = $id_empresa ";
        $q_compuesto = $this->db->query($sql);
        if ($q_compuesto->num_rows() > 0) {
          // Hacemos la misma operacion pero con cada uno de los componentes
          foreach($q_compuesto->result() as $componente) {
            $this->ajustar($componente->id_articulo_componente,$cantidad * $componente->cantidad,$id_sucursal,$fecha,$id_proveedor,$id_variante);
          }
          return;
        }
      }

      // Obtenemos el articulo
      $articulo = $q->row();
      // Controlamos si tenemos articulos_precios_sucursales
      $sql = "SELECT * FROM articulos_precios_sucursales ";
      $sql.= "WHERE id_articulo = $id_articulo ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "AND id_sucursal = $id_sucursal ";
      $q_ps = $this->db->query($sql);
      if ($q_ps->num_rows() > 0) {
        $articulo_precio_sucursal = $q_ps->row();
        $costo_final = $articulo_precio_sucursal->costo_final;
      } else {
        $costo_final = $articulo->costo_final;
      }

      // Obtenemos el stock del articulo en la sucursal
      if ($id_variante == 0) {
        $q = $this->db->query("SELECT * FROM stock WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND id_articulo = $id_articulo ORDER BY id DESC LIMIT 0,1");
        // Si existe el ultimo registro
        if ($q->num_rows()>0) {
          // Actualizamos el stock del articulo en la sucursal
          $sql = "UPDATE stock SET stock_actual = $cantidad ";
          $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $id_articulo AND id_empresa = $id_empresa ";
          $this->db->query($sql);
        } else {
          // Insertamos el stock del articulo en la sucursal
          $sql = "INSERT INTO stock (id_articulo,id_sucursal,stock_actual,id_empresa) VALUES ( ";
          $sql.= "$id_articulo,$id_sucursal,$cantidad,$id_empresa) ";
          $this->db->query($sql);
        }
      }

      $sql = "INSERT INTO stock_movimientos (id_sucursal,id_articulo,movimiento,fecha,cantidad,saldo,id_empresa,id_proveedor,id_variante,costo_final) VALUES (";
      $sql.= "$id_sucursal,$id_articulo,'M','$fecha',$cantidad,$cantidad,$id_empresa,$id_proveedor,$id_variante,$costo_final)";
      $this->db->query($sql);

      // Ajustamos la variante
      if ($id_variante != 0) {
        $q = $this->db->query("SELECT * FROM stock_variantes WHERE id_empresa = $id_empresa AND id_articulo = $id_articulo AND id_variante = $id_variante AND id_sucursal = $id_sucursal");
        if ($q->num_rows()>0) {
          $sql = "UPDATE stock_variantes SET stock_actual = $cantidad ";
          $sql.= "WHERE id_sucursal = $id_sucursal AND id_articulo = $id_articulo AND id_empresa = $id_empresa AND id_variante = $id_variante ";
        } else {
          $sql = "INSERT INTO stock_variantes (id_empresa,id_articulo,id_variante,id_sucursal,stock_actual) VALUES ($id_empresa,$id_articulo,$id_variante,$id_sucursal,$cantidad) ";
        }
        $this->db->query($sql);
        $this->ajustar_calcular_desde_variantes(array(
          "id_sucursal"=>$id_sucursal,
          "id_empresa"=>$id_empresa,
          "id_articulo"=>$id_articulo
        ));
      } else {
        $this->recalcular_stock(array(
          "id_sucursal"=>$id_sucursal,
          "id_empresa"=>$id_empresa,
          "id_articulo"=>$id_articulo
        ));
      }

    }
  }

  // WRAPPER PARA EVITAR EL PROBLEMA DE LOS PARAMETROS
  function ajustar_stock($config = array()) {
    $id_articulo = isset($config["id_articulo"]) ? $config["id_articulo"] : 0;
    $codigo = isset($config["codigo"]) ? $config["codigo"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $cantidad = isset($config["cantidad"]) ? $config["cantidad"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_variante = isset($config["id_variante"]) ? $config["id_variante"] : 0;

    if (!empty($codigo)) {
      $this->load->model("Articulo_Model");
      $art = $this->Articulo_Model->get_by_codigo_string($codigo,array(
        "id_empresa"=>$id_empresa
      ));
      if ($art === FALSE) return;
      $id_articulo = $art->id;
    }

    $this->id_empresa = $id_empresa;
    $this->ajustar($id_articulo,$cantidad,$id_sucursal,$fecha,$id_proveedor,$id_variante);
  }

  // WRAPPER PARA EVITAR EL PROBLEMA DE LOS PARAMETROS
  function agregar_stock($config = array()) {
    $id_articulo = isset($config["id_articulo"]) ? $config["id_articulo"] : 0;
    $codigo = isset($config["codigo"]) ? $config["codigo"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $cantidad = isset($config["cantidad"]) ? $config["cantidad"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_variante = isset($config["id_variante"]) ? $config["id_variante"] : 0;
    $detalle = isset($config["detalle"]) ? $config["detalle"] : "";

    if (!empty($codigo)) {
      $this->load->model("Articulo_Model");
      $art = $this->Articulo_Model->get_by_codigo_string($codigo,array(
        "id_empresa"=>$id_empresa
      ));
      if ($art === FALSE) return;
      $id_articulo = $art->id;
    }

    $this->id_empresa = $id_empresa;
    $this->agregar($id_articulo,$cantidad,$id_sucursal,$fecha,$detalle,$id_proveedor,$id_variante);
  }  


  function ver($params = array()) {

    $this->load->helper("fecha_helper");
    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;
    $id_marca = isset($params["id_marca"]) ? $params["id_marca"] : 0;
    $id_rubro = isset($params["id_rubro"]) ? $params["id_rubro"] : 0;
    $id_desde = isset($params["id_desde"]) ? $params["id_desde"] : 0;
    $desde = isset($params["desde"]) ? $params["desde"] : "";
    $id_proveedor = isset($params["id_proveedor"]) ? $params["id_proveedor"] : 0;
    $codigo_prov = isset($params["codigo_prov"]) ? $params["codigo_prov"] : "";
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 0;
    $order_by = (isset($params["order_by"]) && !empty($params["order_by"])) ? $params["order_by"].", S.id_sucursal ASC" : "A.nombre ASC, S.id_sucursal ASC";
    $filtro_stock = isset($params["filtro_stock"]) ? $params["filtro_stock"] : 0;
    $id_empresa = ($this->id_empresa != 0) ? $this->id_empresa : parent::get_empresa();
    
    // Indica si usa precios por sucursales
    $usa_precios_sucursales = (($id_empresa == 224 || $id_empresa == 249 || $id_empresa == 868) ? 1 : 0);
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS ";
    $sql.= " IF(S.id IS NULL,0,S.id) AS id, ";
    $sql.= " A.id AS id_articulo, ";
    $sql.= " IF(S.id_sucursal IS NULL,0,S.id_sucursal) AS id_sucursal, ";
    $sql.= " IF(S.stock_actual IS NULL,0,S.stock_actual) AS stock_actual, ";
    $sql.= " IF(S.stock_minimo IS NULL,0,S.stock_minimo) AS stock_minimo, ";
    $sql.= " IF(S.reservado IS NULL,0,S.reservado) AS reservado, ";
    if ($usa_precios_sucursales == 1) {
      $sql.= " IF(APS.moneda IS NULL,A.moneda,APS.moneda) AS moneda, ";
      $sql.= " IF(APS.costo_neto IS NULL,A.costo_neto,APS.costo_neto) AS costo_neto, ";
      $sql.= " IF(APS.costo_final IS NULL,A.costo_final,APS.costo_final) AS costo_final, ";
      $sql.= " IF(APS.precio_final_dto IS NULL,A.precio_final_dto,APS.precio_final_dto) AS precio_final_dto, ";
      $sql.= " IF(APS.precio_final_dto_2 IS NULL,A.precio_final_dto_2,APS.precio_final_dto_2) AS precio_final_dto_2, ";
      $sql.= " IF(APS.precio_final_dto_3 IS NULL,A.precio_final_dto_3,APS.precio_final_dto_3) AS precio_final_dto_3, ";
    } else {
      $sql.= " A.costo_neto, A.costo_final, A.precio_final_dto, A.precio_final_dto_2, A.precio_final_dto_3, A.moneda, ";
    }
    $sql.= "  A.codigo_barra, ";
    $sql.= "  IF(R.nombre IS NULL,'',R.nombre) AS rubro, ";
    $sql.= "  IF(SUC.nombre IS NULL,'',SUC.nombre) AS almacen, ";
    $sql.= "  A.codigo, A.codigo_barra, A.nombre, A.uxb, A.custom_10 ";
    $sql_base = "";
    $sql_base.= "FROM articulos A ";
    $sql_base.= "LEFT JOIN stock S ON (S.id_articulo = A.id AND S.id_empresa = A.id_empresa) ";
    $sql_base.= "LEFT JOIN rubros R ON (A.id_rubro = R.id AND R.id_empresa = A.id_empresa) ";
    $sql_base.= "LEFT JOIN almacenes SUC ON (S.id_sucursal = SUC.id AND SUC.id_empresa = A.id_empresa) ";
    if ($usa_precios_sucursales == 1) {
      $sql_base.= "LEFT JOIN articulos_precios_sucursales APS ON (A.id = APS.id_articulo AND A.id_empresa = APS.id_empresa AND SUC.id = APS.id_sucursal) ";
    }
    if (!empty($id_proveedor)) $sql_base.= "INNER JOIN (SELECT AP1.* FROM articulos_proveedores AP1 WHERE AP1.id_proveedor = $id_proveedor AND AP1.id_empresa = $id_empresa GROUP BY AP1.id_articulo) AP ON (AP.id_articulo = A.id AND AP.id_empresa = A.id_empresa) ";
    $sql_base.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($id_desde)) $sql_base.= "AND A.id >= $id_desde ";
    
    if (!empty($filter)) {

      if ($id_empresa == 249 || $id_empresa == 868) {

        // Arreglo para MEGASHOP
        // El sistema anterior cortaba los codigos de barra en 12 digitos
        $sql_base.= "AND (";
        if (is_numeric($filter)) {
          if (strlen($filter) == 13 && ($id_empresa == 249 || $id_empresa == 868)) {
            $filter_12 = substr($filter, 0, 12);
            $sql_base.= "(A.codigo_barra LIKE '%$filter%' OR A.codigo_barra LIKE '%$filter_12%') ";
          } else if (strlen($filter)>7) {
            $filter = (double) $filter;
            $sql_base.= "(A.codigo_barra LIKE '%$filter%') ";
          } else if (strlen($filter) == 7) {
            $filter_6 = substr($filter, 0, 6);
            $filter_0 = "0".$filter;
            $sql_base.= "(A.codigo_barra = '$filter_0' OR A.codigo = '$filter' OR A.codigo = '$filter_6' OR A.codigo_barra LIKE '%$filter%' OR A.codigo_barra LIKE '$filter_6%') ";
          } else {
            $sql_base.= "(A.codigo = '$filter') ";
          }
        } else {
          $filter3 = "";
          $filter2 = preg_split('/\s+/', $filter);
          foreach($filter2 as $fil) {
            $filter3 .= "+(*".$fil."*) ";
          }
          $sql_base.= "( MATCH(A.nombre) AGAINST ('$filter3' IN BOOLEAN MODE)  ) ";
        }
        if ($id_proveedor != 0) {
          $sql_base.= "OR (AP.codigo LIKE '%$filter%') ";
        }
        $sql_base.= ") ";

      } else {
        $sql_base.= "AND (A.nombre LIKE '%$filter%' OR A.codigo LIKE '%$filter%' OR A.codigo_barra LIKE '%$filter%') ";
      }
    }
      
    if (!empty($id_rubro)) $sql_base.= "AND A.id_rubro = $id_rubro ";
    if (!empty($id_marca)) $sql_base.= "AND A.id_marca = $id_marca ";
    if (!empty($id_proveedor)) {
      $sql_base.= "AND AP.id_proveedor = $id_proveedor ";
      if (!empty($codigo_prov)) {
        $sql_base.= "AND AP.codigo = '$codigo_prov' ";
      }
    } 
    if (!empty($id_sucursal)) $sql_base.= "AND S.id_sucursal = $id_sucursal ";
    if (!empty($desde)) {
      $sql_base.="AND A.id IN (SELECT SM.id_articulo FROM stock_movimientos SM WHERE SM.id_empresa = $id_empresa AND SM.fecha = '$desde' )";
    }
    if ($filtro_stock == 1) {
      // Stock por arriba del minimo
      $sql_base.= "AND S.stock_actual > S.stock_minimo ";
    } else if ($filtro_stock == 2) {
      // Stock por debajo del minimo
      $sql_base.= "AND S.stock_actual > 0 AND S.stock_actual <= S.stock_minimo ";
    } else if ($filtro_stock == 3) {
      // Sin stock
      $sql_base.= "AND S.stock_actual <= 0 ";
    } else if ($filtro_stock == 6) {
      // Con stock
      $sql_base.= "AND S.stock_actual > 0 ";
    } else if ($filtro_stock == 5) {
      // Negativos
      $sql_base.= "AND S.stock_actual < 0 ";
    } else if ($filtro_stock == 4) {
      // Que tiene reservas
      $sql_base.= "AND S.reservado > 0 ";
    }
    $sql.= $sql_base;

    // TODO: Filtrar por sucursal
    // Lo comente porque sino, cuando el articulo esta cargado pero no tiene
    // stock inicial, no aparecia y generaba confusion
    //if (!empty($id_sucursal)) $sql.= "AND S.id_sucursal = $id_sucursal ";
    $sql.= "ORDER BY $order_by ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $sql_real = $sql;
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $this->load->model("Configuracion_Model");
    $cotizacion = $this->Configuracion_Model->get_cotizacion(array(
      "id_empresa"=>$id_empresa,
    ));

    // Totales sin paginado
    if ($usa_precios_sucursales == 0) {
      $sql = "SELECT SUM(A.costo_final * S.stock_actual * IF(A.moneda = '".'U$S'."',$cotizacion,1) ) AS costo_final, ";
      $sql.= " SUM(S.stock_actual) AS total_unidades, ";
      $sql.= " SUM(A.precio_final_dto * S.stock_actual * IF(A.moneda = '".'U$S'."',$cotizacion,1)) AS precio_final ";
    } else {
      $sql = "SELECT SUM(APS.costo_final * S.stock_actual * IF(APS.moneda = '".'U$S'."',$cotizacion,1)) AS costo_final, ";
      $sql.= " SUM(S.stock_actual) AS total_unidades, ";
      $sql.= " SUM(APS.precio_final_dto * S.stock_actual * IF(APS.moneda = '".'U$S'."',$cotizacion,1)) AS precio_final ";
    }
    $sql.= $sql_base;
    $sql_suma = $sql;
    $q_suma = $this->db->query($sql);
    $totales = $q_suma->row();

    $salida = array();
    foreach($q->result() as $row) {

      // Fecha de ultima compra
      $sql = "SELECT IF(MAX(fecha) IS NULL,'',MAX(fecha)) AS fecha ";
      $sql.= "FROM stock_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_articulo = $row->id_articulo ";
      if (!empty($row->id_sucursal)) $sql.= "AND id_sucursal = $row->id_sucursal ";
      $sql.= "AND movimiento = 'A' ";
      $sql.= "ORDER BY fecha DESC, id DESC ";
      $q_compra = $this->db->query($sql);
      $r_compra = $q_compra->row();
      $row->fecha_ult_compra = (!empty($r_compra->fecha)) ? fecha_es($r_compra->fecha) : "";

      // Fecha de ultima venta
      $sql = "SELECT IF(MAX(fecha) IS NULL,'',MAX(fecha)) AS fecha ";
      $sql.= "FROM stock_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_articulo = $row->id_articulo ";
      if (!empty($row->id_sucursal)) $sql.= "AND id_sucursal = $row->id_sucursal ";
      $sql.= "AND movimiento = 'B' ";
      $sql.= "ORDER BY fecha DESC, id DESC ";
      $q_venta = $this->db->query($sql);
      $r_venta = $q_venta->row();
      $row->fecha_ult_venta = (!empty($r_venta->fecha)) ? fecha_es($r_venta->fecha) : "";

      // Obtenemos las variantes del articulo
      $row->variantes = $this->get_variantes(array(
        "id_empresa"=>$id_empresa,
        "id_articulo"=>$row->id_articulo,
        "id_sucursal"=>$id_sucursal,
      ));

      $salida[] = $row;
    }

    return array(
      "results"=>$salida,
      "total"=>$total->total,
      "meta"=>array(
        "total_costo"=>(is_null($totales->costo_final) ? 0 : $totales->costo_final),
        "total_precio"=>(is_null($totales->precio_final) ? 0 : $totales->precio_final),
        "total_unidades"=>(is_null($totales->total_unidades) ? 0 : $totales->total_unidades),
      ),
      "sql_suma"=>$sql_suma,
      "sql_real"=>$sql_real,
    );
  }

  function ver_movimiento($params = array()) {

    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;
    $id_marca = isset($params["id_marca"]) ? $params["id_marca"] : 0;
    $id_rubro = isset($params["id_rubro"]) ? $params["id_rubro"] : 0;
    $fecha = isset($params["desde"]) ? $params["desde"] : "";
    $id_proveedor = isset($params["id_proveedor"]) ? $params["id_proveedor"] : 0;
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 0;
    $id_empresa = ($this->id_empresa != 0) ? $this->id_empresa : parent::get_empresa();

    $sql = "SELECT SQL_CALC_FOUND_ROWS ";
    $sql.= "  SM.id, SM.movimiento, SM.cantidad, ";
    $sql.= "  A.id AS id_articulo, ";
    $sql.= "  IF(SM.id_sucursal IS NULL,0,SM.id_sucursal) AS id_sucursal, ";
    $sql.= "  SM.cantidad, ";
    $sql.= "  A.costo_neto, A.costo_final, A.codigo_barra, ";
    $sql.= "  A.precio_final, A.precio_final_2, A.precio_final_3, ";
    $sql.= "  IF(R.nombre IS NULL,'',R.nombre) AS rubro, ";
    $sql.= "  IF(SUC.nombre IS NULL,'',SUC.nombre) AS almacen, ";
    $sql.= "  A.codigo, A.codigo_barra, A.nombre, A.uxb ";
    $sql.= "FROM stock_movimientos SM ";
    $sql.= "INNER JOIN articulos A ON (A.id_empresa = SM.id_empresa AND A.id = SM.id_articulo) ";
    $sql.= "LEFT JOIN rubros R ON (A.id_rubro = R.id AND SM.id_empresa = R.id_empresa) ";
    $sql.= "LEFT JOIN almacenes SUC ON (SM.id_sucursal = SUC.id AND SM.id_empresa = SUC.id_empresa) ";
  //if (!empty($id_proveedor)) $sql.= "INNER JOIN articulos_proveedores AP ON (AP.id_articulo = A.id) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "AND SM.movimiento = 'A' ";
    if (!empty($filter)) $sql.= "AND (A.nombre LIKE '%$filter%' OR A.codigo LIKE '%$filter%' OR A.codigo_barra LIKE '%$filter%') ";
    if (!empty($id_rubro)) $sql.= "AND A.id_rubro = $id_rubro ";
    if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
    if (!empty($id_proveedor)) $sql.= "AND SM.id_proveedor = $id_proveedor ";
    if (!empty($fecha)) $sql.="AND SM.fecha = '$fecha' ";
  // TODO: Filtrar por sucursal
  // Lo comente porque sino, cuando el articulo esta cargado pero no tiene
  // stock inicial, no aparecia y generaba confusion
  //if (!empty($id_sucursal)) $sql.= "AND S.id_sucursal = $id_sucursal ";
    $sql.= "ORDER BY A.nombre ASC, SM.id_sucursal ASC ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$q->result(),
      "total"=>$total->total,

    );
  }


  function detalle($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $id_articulo = isset($conf["id_articulo"]) ? $conf["id_articulo"] : 0;
    $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
    $desde = isset($conf["desde"]) ? $conf["desde"] : date("Y-m-d");
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : date("Y-m-d");
    $sql = "SELECT ";
    $sql.= "  (CASE movimiento WHEN 'I' THEN 'Inicial' WHEN 'A' THEN 'Alta' WHEN 'B' THEN 'Baja' WHEN 'M' THEN 'Ajuste' WHEN 'R' THEN 'Rotura' END) AS movimiento, ";
    $sql.= "  S.id, S.id_articulo, S.cantidad, S.saldo AS saldo, S.detalle, ";
    $sql.= "  IF(S.fecha='0000-00-00','',DATE_FORMAT(S.fecha,'%d/%m/%Y')) AS fecha, ";
    $sql.= "  IF(P.nombre IS NULL,'',P.nombre) AS proveedor, ";
    $sql.= "  A.codigo, A.codigo_barra, A.nombre, A.uxb ";
    $sql.= "FROM stock_movimientos S ";
    $sql.= "INNER JOIN articulos A ON (S.id_articulo = A.id AND S.id_empresa = A.id_empresa) ";
    $sql.= "LEFT JOIN proveedores P ON (S.id_proveedor = P.id AND S.id_empresa = P.id_empresa) ";
    $sql.= "WHERE S.id_articulo = $id_articulo AND S.id_empresa = $id_empresa ";
    $sql.= "AND S.fecha >= '$desde' AND S.fecha <= '$hasta' ";
    if (!empty($id_sucursal)) $sql.= "AND S.id_sucursal = $id_sucursal ";
    $sql.= "ORDER BY S.fecha DESC, S.id DESC ";
    $q = $this->db->query($sql);
    return array(
      "results"=>$q->result(),
      "sql"=>$sql
    );
  }

  function get_saldo($params = array()) {
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;
    $id_articulo = $params["id_articulo"];
    $fecha = isset($params["fecha"]) ? $params["fecha"] : date("Y-m-d");
    $sql = "SELECT A.saldo ";
    $sql.= "FROM stock_movimientos A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "AND A.id_sucursal = $id_sucursal ";
    $sql.= "AND A.id_articulo = $id_articulo ";
    $sql.= "AND A.fecha <= '$fecha' ";
    $sql.= "ORDER BY A.fecha DESC, A.id DESC ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
     $row = $q->row();
     return $row->saldo;
   } else {
     return 0;
   }
  }

  function consulta($codigo = '',$id_sucursal = 0,$id_articulo = 0) {

    $id_empresa = ($this->id_empresa != 0) ? $this->id_empresa : parent::get_empresa();
    // Buscamos el articulo
    $sql = "SELECT A.* ";
    $sql.= "FROM articulos A ";
    $sql.= "WHERE 1=1 ";
    if (!empty($codigo)) $sql.= "AND (A.codigo = '$codigo' OR A.codigo_barra = '$codigo') "; // Que coincida el codigo
    if (!empty($id_articulo)) $sql.= "AND A.id = $id_articulo ";
    $sql.= "AND eliminado = 0 "; // Que no este eliminado
    $sql.= "AND usa_stock = 1 "; // Que se gestiona el stock
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);

    // No encontramos nada, buscamos por un CODIGO DE PROVEEDOR
    if ($q->num_rows() == 0 && !empty($codigo)) {
      $sql = "SELECT A.* ";
      $sql.= "FROM articulos A ";
      $sql.= "INNER JOIN articulos_proveedores AP ON (A.id = AP.id_articulo AND A.id_empresa = AP.id_empresa) ";
      $sql.= "WHERE (AP.codigo = '$codigo') "; // Que coincida el codigo
      $sql.= "AND A.eliminado = 0 "; // Que no este eliminado
      $sql.= "AND A.usa_stock = 1 "; // Que se gestiona el stock
      $sql.= "AND A.id_empresa = $id_empresa ";
      $sql.= "LIMIT 0,1 ";
      $q = $this->db->query($sql);
    }

    // Si el codigo del articulo existe
    if ($q->num_rows()>0) {
      $row = $q->row();

      // Ahora buscamos el stock del articulo en esa sucursal
      $sql = "SELECT * ";
      $sql.= "FROM stock ";
      $sql.= "WHERE id_sucursal = $id_sucursal ";
      $sql.= "AND id_articulo = $row->id ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "ORDER BY id DESC ";
      $sql.= "LIMIT 0,1 ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        $stock = $qq->row();
        $row->stock = $stock->stock_actual;
      } else {
        $row->stock = 0;
      }

      // Buscamos si tiene alguna variante
      $row->variantes = $this->get_variantes(array(
        "id_empresa"=>$id_empresa,
        "id_articulo"=>$row->id,
        "id_sucursal"=>$id_sucursal,
      ));
      $row->variante = "";
      $row->id_variante = 0;
      return $row;

    } else {
      return FALSE;
    }
  }

  // Obtenemos las variantes de un articulo en particular
  function get_variantes($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $id_articulo = (isset($config["id_articulo"])) ? $config["id_articulo"] : 0;
    $id_sucursal = (isset($config["id_sucursal"])) ? $config["id_sucursal"] : 0;
    $sql = "SELECT ART_VAR.* ";
    $sql.= "FROM articulos_variantes ART_VAR ";
    $sql.= "WHERE ART_VAR.id_empresa = $id_empresa ";
    $sql.= "AND ART_VAR.id_articulo = $id_articulo ";
    $qq = $this->db->query($sql);
    $salida = array();
    foreach($qq->result() as $var) {
      $variante = new stdClass();
      $variante->id = $var->id;
      $variante->nombre = $var->nombre;
      $sql = "SELECT stock_actual, reservado ";
      $sql.= "FROM stock_variantes ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_articulo = $id_articulo ";
      $sql.= "AND id_sucursal = $id_sucursal ";
      $sql.= "AND id_variante = $var->id ";
      $qqq = $this->db->query($sql);
      if ($qqq->num_rows()>0) {
        $rrr = $qqq->row();
        $variante->stock = $rrr->stock_actual;
        $variante->reservado = $rrr->reservado;
      } else {
        $variante->stock = 0;
        $variante->reservado = 0;
      }
      $salida[] = $variante;
    }
    return $salida;
  }


  function get_stock($id_articulo,$config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = (isset($config["id_sucursal"])) ? $config["id_sucursal"] : 0;

    // Buscamos el articulo
    $sql = "SELECT A.* ";
    $sql.= "FROM articulos A ";
    $sql.= "WHERE A.id = $id_articulo ";
    $sql.= "AND eliminado = 0 "; // Que no este eliminado
    $sql.= "AND usa_stock = 1 "; // Que se gestiona el stock
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);

    // Si el codigo del articulo existe
    if ($q->num_rows()>0) {
      $row = $q->row();

      // Si se usa el stock como PYMVAR
      $sql = "SELECT * ";
      $sql.= "FROM stock ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_articulo = $row->id ";
      if ($id_sucursal != 0) $sql.= "AND id_sucursal = $id_sucursal ";
      $sql.= "LIMIT 0,1 ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        // Tomamos el stock actual
        $sql = "SELECT IF(SUM(stock_actual) IS NULL,0,SUM(stock_actual)) AS stock_actual ";
        $sql.= "FROM stock ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_articulo = $row->id ";
        // En caso de que sea de una sucursal, lo filtramos
        if ($id_sucursal != 0) $sql.= "AND id_sucursal = $id_sucursal ";
        $qqq = $this->db->query($sql);
        $stock = $qqq->row();
        $row->stock = $stock->stock_actual;
        return $row->stock;
      } else {
        return 0;
      }
    } else {
      return 0;
    }
  }

}