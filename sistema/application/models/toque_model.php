<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Toque_Model extends Abstract_Model {

  private $id_empresa = 571;
  
  function __construct() {
    parent::__construct("web_configuracion","id_empresa","id_empresa ASC",0);
  }

  // Busca los horarios disponibles para hacer un envio programado
  function disponibles($config=array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $salida = array();
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";

    // Si la fecha es hoy, la hora desde debe ser 1.5 hs mas a la actual
    $hora_desde = "";
    if ($fecha == date("Y-m-d")) {
      $dd = new DateTime();
      $dd->add(new DateInterval('PT90M'));
      $hora_desde = $dd->format("H:i:s");
    }

    // Las horas desde y hasta maximas tienen que ser las de reparto de TOQUE

    // Obtenemos todos los horarios de ese servicio ese dia en particular
    $dayofweek = date('w', strtotime($fecha));
    $sql = "SELECT * FROM com_usuarios_horarios ";
    $sql.= "WHERE id_empresa = '$id_empresa' ";
    $sql.= "AND id_usuario = '$id_usuario' ";
    $sql.= "AND dia = '$dayofweek' ";
    $q = $this->db->query($sql);
    $desde = ""; $hasta = "";
    if ($q->num_rows() <= 0) return array();
    $row = $q->row();

    $salida = array();
    $desde = new DateTime($fecha." ".$row->desde);
    $hasta = new DateTime($fecha." ".$row->hasta);
    $interval = new DateInterval("PT15M");
    $period = new DatePeriod($desde,$interval,$hasta);
    foreach ($period as $dt) {
      
      // No tomamos los horarios menores a hora desde
      if (!empty($hora_desde) && $hora_desde > $dt->format("H:i:s")) continue;

      $salida[] = array(
        "from"=>$dt->format("Y-m-d H:i:s"),
      );
    }
    return $salida;
  }  

  // Busca pedidos listos
  // esta funcion se llama cuando el repartidor finaliza un pedido y no tiene mas asignados
  function buscar_pedidos_listos($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->id_empresa;
    $id_repartidor = (isset($config["id_repartidor"])) ? $config["id_repartidor"] : 0;
    $fecha = (isset($config["fecha"])) ? $config["fecha"] : date("Y-m-d");

    $this->load->model("Repartidor_Model");
    $this->load->model("Repartidor_Caja_Movimiento_Model");

    $repartidor = $this->Repartidor_Model->get($id_repartidor,array(
      "id_empresa"=>$id_empresa
    ));

    $saldo = $this->Repartidor_Caja_Movimiento_Model->calcular_saldo(array(
      "id_empresa"=>$id_empresa,
      "id_repartidor"=>$repartidor->id,
    ));    

    $sql = "SELECT F.* ";
    $sql.= "FROM facturas F ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_vendedor = 0 "; // No asignados
    $sql.= "AND F.id_tipo_estado = 1 "; // Aceptados
    $sql.= "AND F.numero_envio != 'pickup' "; // No los pickups
    $sql.= "AND F.id_punto_venta != 2444 "; // No los anticipos
    $sql.= "AND F.fecha = '$fecha' ";
    $sql.= "AND ($saldo + F.efectivo - F.vuelto) >= $repartidor->limite_efectivo "; // Controlamos el efectivo
    $sql.= "ORDER BY F.codigo_postal ASC "; // Ordenamos por el primer pedido listo
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows() <= 0) return FALSE;
    return $q->row();
  }

  // Devuelve la cantidad de pedidos que estan en proceso
  function cantidad_pedidos_en_proceso($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->id_empresa;
    $fecha = (isset($config["fecha"])) ? $config["fecha"] : date("Y-m-d");
    $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM facturas F ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_tipo_estado >= 0 AND F.id_tipo_estado < 5 ";
    $sql.= "AND F.numero_envio != 'pickup' "; // No los pickups
    $sql.= "AND F.id_punto_venta != 2444 "; // No los anticipos
    $sql.= "AND F.fecha = '$fecha' ";
    $q_en_proceso = $this->db->query($sql);;
    $r_en_proceso = $q_en_proceso->row();
    return $r_en_proceso->cantidad;
  }

  // Devuelve la cantidad de pedidos que estan aceptados pero no asignados
  function cantidad_pedidos_no_asignados($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->id_empresa;
    $fecha = (isset($config["fecha"])) ? $config["fecha"] : date("Y-m-d");
    $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM facturas F ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_vendedor = 0 "; // No asignados
    $sql.= "AND F.id_tipo_estado = 1 "; // Aceptados
    $sql.= "AND F.numero_envio != 'pickup' "; // No los pickups
    $sql.= "AND F.id_punto_venta != 2444 "; // No los anticipos
    $sql.= "AND F.fecha = '$fecha' ";
    $q_en_proceso = $this->db->query($sql);
    $r_en_proceso = $q_en_proceso->row();
    return $r_en_proceso->cantidad;
  }  
  
  function get_config() {
    $id_empresa = 571;
    $sql = "SELECT * FROM web_configuracion WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    $row = $q->row(); 
    $r = new stdClass();
    $r->valor_envio_minimo = (float)$row->texto_quienes_somos;
    $r->distancia_minima = (float)$row->texto_newsletter;
    $r->valor_envio_variable = (float)$row->texto_contacto;
    $r->metros_por_minuto = (float)$row->texto_staff;
    $r->tiempo_servicio = (float)$row->texto_registro;
    $r->coeficiente_seguridad = (float)$row->texto_registro_gracias;
    return $r;
  }
  
}