<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Caja_Diaria_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("caja_diaria","id");
  }

  function enviar_control_ventas_subidas($config = array()) {
    /*
    $this->load->model("Configuracion_Model");
    if ($this->Configuracion_Model->es_local()==0) return;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    $this->load->helper("connection_helper");
    if (!is_connected("www.varcreative.com")) return; // Si no tenemos conexion al servidor, no hacemos nada

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, "https://www.varcreative.com/sistema/graph.facebook.com/v2.6/me/messages?access_token=".$this->access_token);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($response_body));
    $result = curl_exec($ch);
    curl_close($ch);    
    */
  }

  // Obtenemos la caja correspondiente a esa sucursal
  function get_caja($config = array()) {
    
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    // 0 = Efectivo / 1 = Banco
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 0;

    // Obtenemos la caja que corresponde con la caja diaria
    $sql = "SELECT C.* FROM cajas C ";
    $sql.= "INNER JOIN almacenes ALM ON (C.id_empresa = ALM.id_empresa AND C.id_sucursal = ALM.id) ";    
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "AND C.tipo = $tipo ";
    $sql.= "AND ALM.id = $id_sucursal ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;
    else return $q->row();
  }

  // Esta funcion traslada el efectivo real de la caja diaria a un movimiento de la caja grande de la sucursal
  function confirmar($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_caja_diaria = isset($config["id_caja_diaria"]) ? $config["id_caja_diaria"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $this->load->helper("fecha_helper");

    $caja_diaria = $this->obtener($id_caja_diaria,array(
      "id_empresa"=>$id_empresa,
      "id_punto_venta"=>$id_punto_venta,
    ));

    // Controlamos que no estemos confirmando una local
    $this->load->model("Configuracion_Model");
    $es_local = $this->Configuracion_Model->es_local();
    if ($es_local == 1) {
      return array("error"=>1,"mensaje"=>"ERROR: La caja es local.");
    }

    $this->load->model("Punto_Venta_Model");
    $pv = $this->Punto_Venta_Model->get($id_punto_venta,array(
      "id_empresa"=>$id_empresa
    ));
    if (empty($pv->id_sucursal)) {
      return array("error"=>1,"mensaje"=>"ERROR: El punto de venta no tiene una sucursal asignada.");
    }

    // Obtenemos la caja en efectivo de esa sucursal
    $caja = $this->get_caja(array(
      "id_empresa"=>$id_empresa,
      "tipo"=>0, // EFECTIVO
      "id_sucursal"=>$pv->id_sucursal,
    ));
    if (empty($pv->id_sucursal)) {
      return array("error"=>1,"mensaje"=>"ERROR: No se encuentra la caja para el punto de venta.");
    }

    $id_concepto = 1489; // VENTA POR CAJA
    $this->load->model("Caja_Movimiento_Model");
    $existe = $this->Caja_Movimiento_Model->existe_movimiento(array(
      "id_caja"=>$caja->id,
      "id_concepto"=>$id_concepto,
      "id_empresa"=>$id_empresa,
      "id_caja_diaria"=>$id_caja_diaria,
      "id_punto_venta"=>$id_punto_venta,
      "id_sucursal"=>$pv->id_sucursal,
    ));

    // TODO: Megashop emite el movimiento con el efectivo real (porque la apertura y cierre de la caja es constante)
    if ($id_empresa == 249 || $id_empresa == 224 || $id_empresa == 868) {
      $monto = ($caja_diaria->efectivo_real - $caja_diaria->salida_efectivo - $caja_diaria->efectivo_inicial);  
    } else {
      // En cualquier otro caso, se pasa el monto de retiro
      $monto = $caja_diaria->retiro;
    }

    if ($existe) {
      // Actualizamos el movimiento de la caja
      $sql = "UPDATE cajas_movimientos SET monto = $monto ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_caja = $caja->id ";
      $sql.= "AND id_caja_diaria = $id_caja_diaria ";
      $sql.= "AND id_punto_venta = $id_punto_venta ";
      $sql.= "AND id_concepto = $id_concepto ";
      $sql.= "AND id_sucursal = $pv->id_sucursal ";
      $this->db->query($sql);
    } else {
      // Ingresamos un movimiento
      $this->Caja_Movimiento_Model->ingreso(array(
        "id_caja"=>$caja->id,
        "id_concepto"=>$id_concepto,
        "observaciones"=>"Efectivo Caja Diaria ".$caja_diaria->fecha,
        "fecha"=>fecha_mysql($caja_diaria->fecha)." ".$caja_diaria->hora,
        "monto"=>$monto,
        "id_empresa"=>$id_empresa,
        "id_caja_diaria"=>$id_caja_diaria,
        "id_punto_venta"=>$id_punto_venta,
        "id_sucursal"=>$pv->id_sucursal,
        "id_usuario"=>(isset($_SESSION["id"]) ? $_SESSION["id"] : 0),
      ));
    }

    return array("error"=>0,"mensaje"=>"La caja se actualizo correctamente.");
  }  

  // Funcion utilizada para actualizar los valores de la caja diaria a partir de las ventas
  // Se usa principalmente para cuando se modifica una forma de pago de alguna venta
  function recalcular_caja($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_caja_diaria = isset($config["id_caja_diaria"]) ? $config["id_caja_diaria"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : '';
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $sql = "SELECT ";
    $sql.= " IF(SUM(efectivo-vuelto) IS NULL,0,SUM(efectivo-vuelto)) AS efectivo, ";
    $sql.= " IF(SUM(tarjeta) IS NULL,0,SUM(tarjeta)) AS tarjeta, ";
    $sql.= " IF(SUM(interes) IS NULL,0,SUM(interes)) AS interes ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $sql.= "AND anulada = 0 ";
    if (!empty($fecha)) $sql.= "AND fecha = '$fecha' ";
    if (!empty($id_caja_diaria)) $sql.= "AND id_caja_diaria = $id_caja_diaria ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $sql = "UPDATE caja_diaria ";
    $sql.= "SET efectivo = '$r->efectivo', tarjetas = '$r->tarjeta', intereses = '$r->interes' ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    if (!empty($id_caja_diaria)) $sql.= "AND id = $id_caja_diaria ";
    if (!empty($fecha)) $sql.= "AND fecha = '$fecha' ";
    $this->db->query($sql);
  }

  function buscar($params = array()) {
    
    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $id_usuario = isset($params["id_usuario"]) ? $params["id_usuario"] : 0;
    $id_punto_venta = isset($params["id_punto_venta"]) ? $params["id_punto_venta"] : 0;
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;
    $fecha = isset($params["fecha"]) ? $params["fecha"] : "";
    $desde = isset($params["desde"]) ? $params["desde"] : "";
    $hasta = isset($params["hasta"]) ? $params["hasta"] : "";
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 0;
    $order = isset($params["order"]) ? $params["order"] : "";
    $id_empresa = parent::get_empresa();

    $sql = "SELECT SQL_CALC_FOUND_ROWS CD.*, DATE_FORMAT(CD.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " DATE_FORMAT(CD.hora,'%H:%i') AS hora, PV.nombre, PV.numero ";
    $sql.= "FROM caja_diaria CD ";
    $sql.= " INNER JOIN puntos_venta PV ON (CD.id_punto_venta = PV.id) ";
    $sql.= "WHERE CD.id_empresa = $id_empresa ";
    $sql.= "AND CD.estado = 'C' ";
    if (!empty($id_usuario)) $sql.= "AND CD.id_usuario = $id_usuario ";
    if (!empty($id_punto_venta)) $sql.= "AND CD.id_punto_venta = $id_punto_venta ";
    if (!empty($id_sucursal)) $sql.= "AND PV.id_sucursal = $id_sucursal ";
    if (!empty($fecha)) $sql.= "AND CD.fecha = '$fecha' ";
    if (!empty($desde)) $sql.= "AND CD.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND CD.fecha <= '$hasta' ";
    $sql.= "ORDER BY CD.fecha DESC, CD.hora DESC ";
    if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
    $sql_base = $sql;
    $q = $this->db->query($sql);

    $salida = $q->result();
    $this->load->model("Configuracion_Model");
    foreach($salida as $r) {
      $r->confirmada = 0;
      if ($this->Configuracion_Model->es_local()==0) {
        $sql = "SELECT 1 FROM cajas_movimientos WHERE id_caja_diaria = $r->id AND id_punto_venta = $r->id_punto_venta AND id_empresa = $r->id_empresa ";
        $qq = $this->db->query($sql);
        if ($qq->num_rows() > 0) $r->confirmada = 1;
      }
    }
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$salida,
      "total"=>$total->total,
      "sql"=>$sql_base,
    );
  }

  // TODO: Hacer que el GET sea con el parametro CONFIG
  function obtener($id,$config=array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;

    // Si hay una caja abierta
    $sql = "SELECT C.*, DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " IF(U.nombre IS NULL,'',U.nombre) AS usuario ";
    $sql.= "FROM caja_diaria C ";
    $sql.= "LEFT JOIN com_usuarios U ON (C.id_usuario = U.id AND C.id_empresa = U.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa AND C.id = $id ";
    $sql.= "AND C.id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;

    $row = $q->row();

    $this->load->model("Almacen_Model");
    $id_almacen = $this->Almacen_Model->get_sucursal_punto_venta($row->id_punto_venta,array(
      "id_empresa"=>$id_empresa,
    ));
    $row->sucursal = "";
    if ($id_almacen != 0) {
      $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa AND id = $id_almacen";
      $q = $this->db->query($sql);
      $almacen = $q->row();
      $row->sucursal = $almacen->nombre;
    }

    // Sumamos los gastos
    $sql = "SELECT * FROM gastos ";
    $sql.= "WHERE id_caja_diaria = '$row->id' ";
    $sql.= "AND id_punto_venta = '$row->id_punto_venta' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $qq = $this->db->query($sql);
    $row->gastos = $qq->result();
    
    // Sumamos las entradas
    $sql = "SELECT F.* ";
    $sql.= "FROM facturas F ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.id_caja_diaria = $row->id ";
    $sql.= "AND F.id_punto_venta = $row->id_punto_venta ";
    $sql.= "AND F.tipo != 'P' ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $row->entradas = $q->result();

    // Sumamos los pagos
    $sql = "SELECT F.* ";
    $sql.= "FROM facturas F ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.id_caja_diaria = $row->id ";
    $sql.= "AND F.id_punto_venta = $row->id_punto_venta ";
    $sql.= "AND F.tipo = 'P' ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $row->pagos = $q->result();

    // Sumamos la venta por departamento
    $sql = "SELECT A.id_departamento, ";
    $sql.= " IF (DC.nombre IS NULL,'No Definido',DC.nombre) AS departamento, ";
    $sql.= " SUM(FI.total_con_iva) AS total, ";
    $sql.= " SUM(FI.cantidad) AS cantidad ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN facturas_items FI ON (F.id = FI.id_factura AND F.id_punto_venta = FI.id_punto_venta AND F.id_empresa = FI.id_empresa) ";
    $sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
    $sql.= "LEFT JOIN departamentos_comerciales DC ON (A.id_departamento = DC.id AND A.id_empresa = DC.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND F.id_caja_diaria = $row->id ";
    $sql.= "AND F.id_punto_venta = $row->id_punto_venta ";
    $sql.= "AND F.tipo != 'P' ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND FI.anulado = 0 ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $sql.= "GROUP BY A.id_departamento ";
    $sql.= "ORDER BY departamento ";
    $q = $this->db->query($sql);
    $row->departamentos = $q->result();

    // Sumamos los cupones de tarjetas
    $sql = "SELECT CT.*, T.nombre AS tarjeta ";
    $sql.= "FROM cupones_tarjetas CT INNER JOIN facturas F ON (CT.id_factura = F.id AND CT.id_punto_venta = F.id_punto_venta AND CT.id_empresa = F.id_empresa) ";
    $sql.= "INNER JOIN tarjetas T ON (T.id = CT.id_tarjeta AND T.id_empresa = CT.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND CT.status = 0 ";
    $sql.= "AND F.id_caja_diaria = '$row->id' ";
    $sql.= "AND F.tipo != 'P' ";
    $sql.= "AND F.id_punto_venta = $row->id_punto_venta ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $sql.= "AND DATE_FORMAT(CT.fecha,'%d/%m/%Y') = '$row->fecha' ";
    $q = $this->db->query($sql);
    $row->tarjetas = $q->result();

    $this->load->model("Configuracion_Model");
    $row->confirmada = 0;
    if ($this->Configuracion_Model->es_local()==0) {
      $sql = "SELECT 1 FROM cajas_movimientos WHERE id_caja_diaria = $row->id AND id_punto_venta = $row->id_punto_venta AND id_empresa = $row->id_empresa ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) $row->confirmada = 1;
    }

    // Agrupamos las tarjetas
    $row->agrupado_tarjetas = array();
    foreach($row->tarjetas as $t) {
      $existe = FALSE;
      foreach($row->agrupado_tarjetas as $tar) {
        if ($tar->tarjeta == $t->tarjeta) {
          $tar->importe += $t->importe;
          $tar->interes += $t->interes;
          $tar->total += $t->total;
          $tar->cantidad++;
          $existe = TRUE;
          break;
        }
      }
      if (!$existe) {
        $tar = new stdClass();
        $tar->tarjeta = $t->tarjeta;
        $tar->importe = $t->importe;
        $tar->interes = $t->interes;
        $tar->total = $t->total;
        $tar->cantidad = 1;
        $row->agrupado_tarjetas[] = $tar;
      }
    }

    return $row;
  }
  
  function save($data) {
    $id_empresa = parent::get_empresa();
    unset($data->punto_venta);
    unset($data->confirmada);

    // Dependiendo de la configuracion del sistema, si es LOCAL o NO
    $this->load->model("Configuracion_Model");
    $data->uploaded = ($this->Configuracion_Model->es_local()==1)?0:1;

    if (!isset($data->id_punto_venta) || $data->id_punto_venta == 0) {
      $sql = "SELECT * FROM puntos_venta WHERE id_empresa = $id_empresa AND por_default = 1 LIMIT 0,1";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $row = $q->row();  
        $data->id_punto_venta = $row->id_punto_venta;
      }
    }

    if (isset($data->id) && $data->id != 0) {

      // Obtenemos la caja
      $anterior = $this->obtener($data->id,array(
        "id_empresa"=>$data->id_empresa,
        "id_punto_venta"=>$data->id_punto_venta,
      ));

      // Actualizamos
      unset($data->punto_venta);
      unset($data->fecha);
      unset($data->hora);
      $this->db->where("id",$data->id);
      $this->db->where("id_punto_venta",$data->id_punto_venta);
      $this->db->update("caja_diaria",$data);
      $id_caja_diaria = $data->id;
    } else {
      // Insertamos
      $data->fecha = date("Y-m-d");
      $data->hora = date("H:i:s");
      unset($data->punto_venta);
      $id_caja_diaria = $this->insert($data);
      $anterior = new stdClass();
      $anterior->estado = "";
    }

    // Si se esta cerrando la caja
    if ($anterior->estado == "A" && $data->estado == "C") {

      // Actualizamos todos los comprobantes abiertos con el ID de la CAJA
      $sql = "UPDATE facturas SET id_caja_diaria = $id_caja_diaria ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_caja_diaria = 0 ";
      $this->db->query($sql);

      // Si es LOCAL, tenemos que llamar al metodo para subir la informacion
      // TODO: Solucionar el caso de ALMAFUERTE DE JAVIER, es LOCAL pero no tiene que subirse la info al servidor
      if ($this->Configuracion_Model->es_local() == 1 && $id_empresa != 121 && $data->id_punto_venta != 1008) {
        $dominio = strtolower($_SERVER["HTTP_HOST"]);
        $puerto = $_SERVER["SERVER_PORT"];
        $url = "http://$dominio:$puerto/upload2.php";
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_exec($c);
      }
    }

    return $id_caja_diaria;
  }
  
}