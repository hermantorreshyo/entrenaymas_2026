<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Repartidores extends REST_Controller {

  private $fcm_key = "AAAAsO5Aewo:APA91bHbYGjTjGySWVWeEPjXLqBwuh2wOeqZTr7DQtf63AygHRByMqnglfUxxJcrm63uGgemTcvaJqdOYHbr4-M4-pQvhwldwKiWTtnHG9Inux1slsLVlZ36PNY_FXC5jDHhzU7VZ08V";

  function __construct() {
    parent::__construct();
    $this->load->model('Repartidor_Model', 'modelo');
  }

  function depositar_en_comercio() {
    header('Access-Control-Allow-Origin: *');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_empresa = parent::get_post("id_empresa");
    $id_repartidor = parent::get_post("id_repartidor");
    $id_usuario = parent::get_post("id_usuario");
    $monto = parent::get_post("monto");
    $observaciones = parent::get_post("observaciones","");
    $this->load->model("Repartidor_Caja_Movimiento_Model");
    $s = $this->Repartidor_Caja_Movimiento_Model->depositar_en_comercio(array(
      "id_empresa"=>$id_empresa,
      "id_repartidor"=>$id_repartidor,
      "id_usuario"=>$id_usuario,
      "monto"=>$monto,
      "observaciones"=>$observaciones,
    ));
    echo json_encode($s);
  }

  function aceptar_dinero_comercio() {
    // Cuando el comercio acepta el dinero, en la cuenta del repartidor se actualiza 
    $id_empresa = parent::get_get("id_empresa");
    $id_factura = parent::get_get("id");
    $id_repartidor = parent::get_get("id_repartidor");
    $id_punto_venta = parent::get_get("id_punto_venta");
    
    // Ponemos como activo el movimiento de caja que haya hecho ese repartidor
    $sql = "UPDATE repartidores_cajas_movimientos SET estado = 0 ";
    $sql.= "WHERE id_punto_venta = $id_punto_venta AND id_empresa = $id_empresa AND id_factura = $id_factura ";
    $this->db->query($sql);

    // Ponemos como finalizada la venta
    $sql = "UPDATE facturas SET id_tipo_estado = 6 ";
    $sql.= "WHERE id_punto_venta = $id_punto_venta AND id_empresa = $id_empresa AND id = $id_factura ";
    $this->db->query($sql);

    // Notificamos a la app del repartidor
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_punto_venta = $id_punto_venta AND id_empresa = $id_empresa AND id = $id_factura ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $this->load->model("Usuario_Model");
    $usuario = $this->Usuario_Model->get($r->id_usuario,array(
      "id_empresa"=>$id_empresa
    ));
    $this->load->model("Repartidor_Model");
    $repartidor = $this->Repartidor_Model->get($id_repartidor,array(
      "id_empresa"=>$id_empresa
    ));
    if (!empty($repartidor->token)) {
      $fields = array(
        'registration_ids' => array($repartidor->token),
        'notification' => array(
          'body'  => $usuario->nombre." ha aceptado el deposito de $".$r->total.".",
          'title' => "Deposito Aceptado",
          'sound' => 'mySound',
          "click_action" => "FCM_PLUGIN_ACTIVITY",
        ),
      );
      $headers = array(
        'Authorization: key='.$this->fcm_key,
        'Content-Type: application/json'
      );
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
      curl_setopt($ch,CURLOPT_POST, true);
      curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
      $result = curl_exec($ch);
      curl_close($ch);
    }    

    echo json_encode(array("error"=>0));
  }

  function rechazar_dinero_comercio() {
    $id_empresa = parent::get_get("id_empresa");
    $id_factura = parent::get_get("id");
    $id_repartidor = parent::get_get("id_repartidor");
    $id_punto_venta = parent::get_get("id_punto_venta");

    // Eliminamos el registro de la caja en el repartidor
    $this->load->model("Repartidor_Caja_Movimiento_Model");
    $this->Repartidor_Caja_Movimiento_Model->borrar(array(
      "id_repartidor"=>$id_repartidor,
      "id_factura"=>$id_factura,
      "id_punto_venta"=>$id_punto_venta,
      "id_empresa"=>$id_empresa,
    ));

    // Anulamos la venta
    $sql = "UPDATE facturas SET id_tipo_estado = 7 ";
    $sql.= "WHERE id_punto_venta = $id_punto_venta AND id_empresa = $id_empresa AND id = $id_factura ";
    $this->db->query($sql);

    // Notificamos a la app del repartidor
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_punto_venta = $id_punto_venta AND id_empresa = $id_empresa AND id = $id_factura ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $this->load->model("Usuario_Model");
    $usuario = $this->Usuario_Model->get($r->id_usuario,array(
      "id_empresa"=>$id_empresa
    ));
    $this->load->model("Repartidor_Model");
    $repartidor = $this->Repartidor_Model->get($id_repartidor,array(
      "id_empresa"=>$id_empresa
    ));
    if (!empty($repartidor->token)) {
      $fields = array(
        'registration_ids' => array($repartidor->token),
        'notification' => array(
          'body'  => $usuario->nombre." ha aceptado el deposito de $".$r->total.".",
          'title' => "Deposito Aceptado",
          'sound' => 'mySound',
          "click_action" => "FCM_PLUGIN_ACTIVITY",
        ),
      );
      $headers = array(
        'Authorization: key='.$this->fcm_key,
        'Content-Type: application/json'
      );
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
      curl_setopt($ch,CURLOPT_POST, true);
      curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
      $result = curl_exec($ch);
      curl_close($ch);
    }

    echo json_encode(array("error"=>0));
  }

  function get($id) {
    if ($id == "index") {
      $salida = $this->modelo->buscar(array(
        "limit"=>parent::get_get("limit",0),
        "offset"=>parent::get_get("offset",10),
        "filter"=>parent::get_get("filter",""),
        "order_by"=>parent::get_get("order_by",""),
        "order"=>parent::get_get("order",""),
        "id_usuario"=>parent::get_get("id_usuario",0),
      ));
      echo json_encode($salida);
    } else {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }
  }

  function buscar() {
    $activo = parent::get_get("activo",-1);
    $buscar_saldo = parent::get_get("buscar_saldo",0);
    $id_usuario = parent::get_get("id_usuario",0);
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",999999);
    $filter = parent::get_get("filter","");
    $s = $this->modelo->buscar(array(
      "activo"=>$activo,
      "buscar_saldo"=>$buscar_saldo,
      "id_usuario"=>$id_usuario,
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
    ));
    echo json_encode($s);
  }

  function get_saldo() {
    header('Access-Control-Allow-Origin: *');
    $id_repartidor = parent::get_get("id_repartidor",0);
    $id_empresa = parent::get_get("id_empresa",571);
    $this->load->model("Repartidor_Caja_Movimiento_Model");
    $saldo = $this->Repartidor_Caja_Movimiento_Model->calcular_saldo(array(
      "id_empresa"=>$id_empresa,
      "id_repartidor"=>$id_repartidor,
    ));
    $repartidor = $this->modelo->get($id_repartidor,array(
      "id_empresa"=>$id_empresa,
    ));
    echo json_encode(array(
      "error"=>0,
      "saldo"=>$saldo,
      "limite"=>$repartidor->limite_efectivo,
    ));
  }

  function guardar_posicion() {
    header('Access-Control-Allow-Origin: *');
    $id_repartidor = parent::get_post("id_repartidor",0);
    $id_pedido = parent::get_post("id_pedido",0);
    $id_empresa = parent::get_post("id_empresa",571);
    $lat = parent::get_post("lat",0);
    $lon = parent::get_post("lon",0);
    if ($lat != 0 && $lon != 0) {
      $sql = "INSERT INTO repartidores_posiciones (id_empresa,id_repartidor,id_pedido,latitud,longitud,tiempo ";
      $sql.= ") VALUES (";
      $sql.= " '$id_empresa','$id_repartidor','$id_pedido','$lat','$lon',NOW() ";
      $sql.= ")";
      $this->db->query($sql);
      $linea = "ID_REPARTIDOR: $id_repartidor - LAT: $lat - LON: $lon";
      file_put_contents("logs/571/".$id_pedido.".txt", date("Y-m-d H:i:s")." ".$linea."\n", FILE_APPEND);
    }
    echo json_encode(array("error"=>0));
  }

  function cambiar_estado() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_post("id_empresa",0);
    $id_repartidor = parent::get_post("id_repartidor",0);
    $estado = parent::get_post("estado",0);
    if (!is_numeric($id_empresa) || !is_numeric($id_repartidor) || !is_numeric($estado)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Los parametros son incorrectos.",
      ));
      exit();
    }
    $this->db->query("UPDATE repartidores SET activo = '$estado' WHERE id_empresa = '$id_empresa' AND id = '$id_repartidor' ");
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Se cambio el estado correctamente.",
    ));
  }

  function get_pedidos() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_post("id_empresa",0);
    $id_repartidor = parent::get_post("id_repartidor",0);
    $limit = parent::get_post("limit",0);
    $offset = parent::get_post("offset",25);
    $orden = parent::get_post("orden",0);
    $this->load->helper("fecha_helper");

    $sql = "SELECT SQL_CALC_FOUND_ROWS F.*, U.nombre AS comercio, U.direccion AS comercio_direccion, U.titulo, ";
    $sql.= " C.direccion AS cliente_direccion, C.latitud AS cliente_latitud, C.longitud AS cliente_longitud, ";
    $sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN com_usuarios U ON (F.id_empresa = U.id_empresa AND F.id_usuario = U.id) ";
    $sql.= "INNER JOIN clientes C ON (F.id_empresa = U.id_empresa AND F.id_cliente = C.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_vendedor = $id_repartidor ";
    if ($orden == 1) {
      $sql.= "AND F.id_tipo_estado < 6 ";
      $sql.= "ORDER BY F.vencimiento ASC ";
    } else $sql.= "ORDER BY F.fecha DESC, F.hora DESC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $resultado = array();
    foreach($q->result() as $r) {

      $coor_comercio = explode(";", $r->titulo);
      $r->latitud_comercio = (float)$coor_comercio[0];
      $r->longitud_comercio = (float)$coor_comercio[1];

      // Si el pedido fue rechazado por ese repartidor
      $sql = "SELECT * FROM repartidores_pedidos WHERE ";
      $sql.= "id_empresa = $id_empresa AND ";
      $sql.= "id_repartidor = $id_repartidor AND ";
      $sql.= "id_factura = $r->id AND ";
      $sql.= "id_punto_venta = $r->id_punto_venta AND ";
      $sql.= "estado = 'R' ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        // El pedido ya fue rechazado por ese mismo repartidor
        $r->id_tipo_estado = 8;
      }

      $r->custom_2 = (!empty($r->custom_2)) ? ($r->custom_2) : "";
      $r->vencimiento = (!empty($r->vencimiento)) ? ($r->vencimiento) : "";

      $resultado[] = $r;
    }
    echo json_encode(array(
      "results"=>$resultado,
      "total"=>$total->total,
    ));
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/entradas/";
    $filename = $this->input->post("file");
    echo parent::save_image($dir,$filename);
  } 

  // UTILIZADO EN LA APP
  function login() {

    header('Access-Control-Allow-Origin: *');
    $email = $this->input->post("email");
    $password = $this->input->post("password");

    $sql = "SELECT V.* ";
    $sql.= "FROM repartidores V ";
    $sql.= "WHERE V.email = '$email' ";
    $sql.= "AND V.password = '$password' ";
    $sql.= "LIMIT 0,1 ";
    $query = $this->db->query($sql);

    // Datos invalidos
    $resultado = $query->result();
    if (empty($resultado)) {
      // Usuario incorrecto
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Usuario o clave incorrectos.",
        "id"=>0,
      ));
      return;
      
    } else {

      $repartidor = $query->row();

      if ($repartidor->activo == 0) {
        // El usuario aun no esta habilitado
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"El repartidor no se encuentra habilitado. Por favor comuniquese con el administrador.")
        );
        return;        
      }
      echo json_encode(array(
        "error"=>0,
        "mensaje"=>"",
        "id"=>$repartidor->id,
        "id_empresa"=>$repartidor->id_empresa,
        "estado"=>$repartidor->activo,
      ));
    }
  }
	
}