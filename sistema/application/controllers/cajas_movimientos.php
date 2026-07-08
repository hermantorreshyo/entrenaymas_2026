<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cajas_Movimientos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Caja_Movimiento_Model', 'modelo');
  }

  function save_file() {
    $this->load->helper("imagen_helper");
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    $filename = date("YmdHis").filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/entradas/";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path.$filename);
    // Si es una imagen, lo redimensionamos
    if (is_image($filename)) {
      resize(array(
        "dir"=>$path,
        "filename"=>$filename,
      ));
    }    
    echo json_encode(array(
      "path"=>$path.$filename,
      "error"=>0,
    ));
  }    
  

  function relacionar_rec() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_caja = 9;
    $id_empresa = 399;
    $sql = "SELECT D.*, C.nombre AS cliente, F.comprobante, F.id_sucursal FROM depositos D ";
    $sql.= " INNER JOIN facturas F ON (D.id_empresa = F.id_empresa AND D.id_recibo = F.id AND D.id_punto_venta = F.id_punto_venta) ";
    $sql.= " INNER JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa)";
    $sql.= "WHERE D.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $this->load->model("Caja_Movimiento_Model");
    foreach($q->result() as $r) {
      $this->Caja_Movimiento_Model->ingreso(array(
        "id_empresa"=>$id_empresa,
        "id_caja"=>$id_caja,
        "fecha"=>$r->fecha,
        "monto"=>$r->monto,
        "id_factura"=>$r->id_recibo,
        "id_punto_venta"=>$r->id_punto_venta,
        "id_sucursal"=>$r->id_sucursal,
        "observaciones"=>$r->cliente." ".$r->comprobante
      ));
    }
    echo "TERMINO";
  }

  function transferencia() {
    $id_caja_desde = parent::get_post("id_caja_desde",0);
    $id_caja_hasta = parent::get_post("id_caja_hasta",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $id_usuario = parent::get_post("id_usuario",0);
    $observaciones = parent::get_post("observaciones",0);
    $monto = parent::get_post("monto",0);
    $fecha = parent::get_post("fecha",date("d/m/Y H:i:s"));
    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql($fecha);
    echo json_encode($this->modelo->transferencia(array(
      "id_sucursal"=>$id_sucursal,
      "id_caja_desde"=>$id_caja_desde,
      "id_caja_hasta"=>$id_caja_hasta,
      "fecha"=>$fecha,
      "id_usuario"=>$id_usuario,
      "monto"=>$monto,
      "observaciones"=>$observaciones,
    )));
  }

  function cambiar_estado($id_estado_nuevo = 0) {
    $id = parent::get_post("id",0);
    $id_caja = parent::get_post("id_caja",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $sql = "UPDATE cajas_movimientos SET estado = '$id_estado_nuevo' ";
    $sql.= "WHERE id = '$id' AND id_caja = '$id_caja' AND id_sucursal = '$id_sucursal' ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0
    ));
  }

  function delete($id = null) {
    $this->modelo->borrar(array(
      "id"=>$id,
    ));
    echo json_encode(array());
  }  

  function listado() {

    $this->load->helper("fecha_helper");
    $id_caja = $this->get_post("id_caja",0);
    $tipo = $this->get_post("tipo",0);
    $ver_saldos = $this->get_post("ver_saldos",0);
    $estado = $this->get_post("estado",-1);
    $orden_pago = $this->get_post("orden_pago",-1);
    $id_sucursal = $this->get_post("id_sucursal",0);
    $id_concepto = $this->get_post("id_concepto",0);
    $id_usuario = $this->get_post("id_usuario",0);
    $id_factura = $this->get_post("id_factura",0);
    $filter = $this->get_post("filter","");
    
    $desde = $this->get_post("desde");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    $hasta = $this->get_post("hasta");
    if (!empty($hasta)) $hasta = date("Y-m-d",strtotime(fecha_mysql($hasta)." +1 day"));

    $config = array(
      "filter"=>$filter,
      "id_caja"=>$id_caja,
      "id_sucursal"=>$id_sucursal,
      "estado"=>$estado,
      "orden_pago"=>$orden_pago,
      "id_concepto"=>$id_concepto,
      "id_factura"=>$id_factura,
      "id_usuario"=>$id_usuario,
      "desde"=>$desde,
      "hasta"=>$hasta,
    );
    if ($ver_saldos == 0) $config["tipo"] = $tipo;
    else $config["ver_saldos"] = 1;
    $resultado = $this->modelo->buscar($config);
    echo json_encode($resultado);
  }




  function consulta() {

    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql($this->input->post("fecha"));

    $fecha_hasta = $this->input->post("fecha_hasta");
    if (!empty($fecha_hasta)) $fecha_hasta = fecha_mysql($fecha_hasta);

    $sql = "SELECT G.*, ";
    $sql.= "IF (G.id_tipo_gasto = 0,'Sin especificar',TG.nombre) AS tipo_gasto, ";
    $sql.= "IF (G.id_proveedor = 0,'Sin especificar',P.nombre) AS proveedor ";
    $sql.= "FROM cajas_movimientos G ";
    $sql.= "LEFT JOIN tipos_gastos TG ON (TG.id = G.id_tipo_gasto) ";
    $sql.= "LEFT JOIN proveedores P ON (G.id_proveedor = P.id) ";
    $sql.= "WHERE 1=1 ";

    if (!empty($fecha) && empty($fecha_hasta)) {
      $sql.= "AND fecha = '$fecha' ";
    } else if (!empty($fecha) && !empty($fecha_hasta)) {
      $sql.= "AND fecha >= '$fecha' ";
      $sql.= "AND fecha <= '$fecha_hasta' ";
    }
    $q = $this->db->query($sql);
    echo json_encode($q->result());
  }

  function consulta_agrupada() {
    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql($this->input->post("fecha"));
    $fecha_hasta = $this->input->post("fecha_hasta");
    if (!empty($fecha_hasta)) $fecha_hasta = fecha_mysql($fecha_hasta);
    $sql = "SELECT SUM(efectivo) AS efectivo, ";
    $sql.= "IF (G.id_tipo_gasto = 0,'Sin especificar',TG.nombre) AS tipo_gasto ";
    $sql.= "FROM cajas_movimientos G ";
    $sql.= "LEFT JOIN tipos_gastos TG ON (TG.id = G.id_tipo_gasto) ";
    $sql.= "WHERE 1=1 ";
    if (!empty($fecha) && empty($fecha_hasta)) {
      $sql.= "AND fecha = '$fecha' ";
    } else if (!empty($fecha) && !empty($fecha_hasta)) {
      $sql.= "AND fecha >= '$fecha' ";
      $sql.= "AND fecha <= '$fecha_hasta' ";
    }
    $sql.= "GROUP BY TG.id ";
    $sql.= "ORDER BY TG.nombre ASC ";
    $q = $this->db->query($sql);
    echo json_encode($q->result());
  }    

  function resumen_arbol() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $id_padre = parent::get_post("id_padre",0);
    $desde = parent::get_post("desde","");
    $hasta = parent::get_post("hasta","");
    $incluir = parent::get_post("incluir",1);
    $this->load->helper("fecha_helper");
    $desde = (empty($desde) && empty($movimiento)) ? date("Y-m-d") : fecha_mysql($desde);
    $hasta = (empty($hasta) && empty($movimiento)) ? date("Y-m-d") : fecha_mysql($hasta);

    // Obtenemos todo el arbol
    $arr = $this->modelo->get_arbol(array(
      "id_padre"=>$id_padre,
      "id_sucursal"=>$id_sucursal,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "compra_real"=>$incluir,
    ));
    $salida = array(
      "results"=>$arr,
    );        
    echo json_encode($salida);
  }  

}