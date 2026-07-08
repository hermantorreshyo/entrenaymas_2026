<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Reserva_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("hot_reservas","id","fecha_desde DESC",1);
	}

	function mover_disponibilidad($config = array()) {

		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
		$operacion = (isset($config["operacion"])) ? $config["operacion"] : "-";
		$cantidad = (isset($config["cantidad"])) ? $config["cantidad"] : 0;
		$fecha = (isset($config["fecha"])) ? $config["fecha"] : "";
		$id_habitacion = (isset($config["id_habitacion"])) ? $config["id_habitacion"] : 0;
		$id_reserva = (isset($config["id_reserva"])) ? $config["id_reserva"] : 0;

    $sql = "SELECT * FROM hot_disponibilidad WHERE id_empresa = $id_empresa ";
    if (!empty($id_habitacion)) $sql.= "AND id_habitacion = $id_habitacion ";
    if (!empty($id_reserva)) $sql.= "AND id_reserva = $id_reserva ";
    if (!empty($fecha)) $sql.= "AND fecha = '$fecha' ";
    $q_hab = $this->db->query($sql);
		if ($q_hab->num_rows()<=0) {
    	$this->load->model("Habitacion_Model");
    	$habitacion = $this->Habitacion_Model->get($id_habitacion);
			// Insertamos el registro
      $sql = "INSERT INTO hot_disponibilidad (id_empresa,id_habitacion,fecha,disponible,id_reserva) VALUES(";
      $sql.= "$id_empresa,$id_habitacion,'$fecha',$habitacion->capacidad,'$id_reserva')";
      $this->db->query($sql);
		}
		// Ahora actualizamos todos los registros que corresponden
    $sql = "UPDATE hot_disponibilidad ";
    if ($operacion == "+") $sql.= " SET disponible = disponible + $cantidad ";
    else $sql.= " SET disponible = disponible - $cantidad ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id_habitacion)) $sql.= "AND id_habitacion = $id_habitacion ";
    if (!empty($id_reserva)) $sql.= "AND id_reserva = $id_reserva ";
    if (!empty($fecha)) $sql.= "AND fecha = '$fecha' ";
    $this->db->query($sql);
	}

	function buscar($config = array()) {

		$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
		$desde = isset($config["desde"]) ? $config["desde"] : "";
		$hasta = isset($config["hasta"]) ? $config["hasta"] : "";
		$limit = isset($config["limit"]) ? $config["limit"] : 0;
		$tipo_estado = isset($config["tipo_estado"]) ? $config["tipo_estado"] : -1;
		$id_tipo_habitacion = isset($config["id_tipo_habitacion"]) ? $config["id_tipo_habitacion"] : 0;
		$id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
		$filter = isset($config["filter"]) ? $config["filter"] : "";
		$offset = isset($config["offset"]) ? $config["offset"] : 10;
		$order_by = isset($config["order_by"]) ? $config["order_by"] : "R.id DESC ";

		$sql = "SELECT SQL_CALC_FOUND_ROWS R.*, ";
		$sql.= " H.nombre AS habitacion, ";
		$sql.= " C.nombre AS cliente_nombre, C.email AS cliente_telefono, C.email AS cliente_email, ";
		$sql.= " IF(R.fecha_desde = '0000-00-00','',DATE_FORMAT(R.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
		$sql.= " IF(R.fecha_hasta = '0000-00-00','',DATE_FORMAT(R.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta, ";
		$sql.= " IF(R.fecha_reserva = '0000-00-00','',DATE_FORMAT(R.fecha_reserva,'%d/%m/%Y')) AS fecha_reserva ";
		$sql.= "FROM hot_reservas R ";
		$sql.= "INNER JOIN clientes C ON (R.id_empresa = C.id_empresa AND R.id_cliente = C.id) ";
		$sql.= "INNER JOIN hot_habitaciones H ON (R.id_empresa = H.id_empresa AND R.id_habitacion = H.id) ";
		$sql.= "WHERE R.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
		if (!empty($desde)) $sql.= "AND R.fecha_desde >= $desde ";
		if (!empty($hasta)) $sql.= "AND R.fecha_hasta <= $hasta ";
		if ($tipo_estado != -1) $sql.= "AND R.id_estado = $tipo_estado ";
		if (!empty($id_tipo_habitacion)) $sql.= "AND R.id_tipo_habitacion = $id_tipo_habitacion ";
		if (!empty($id_cliente)) $sql.= "AND R.id_cliente = $id_cliente ";
    $sql.= "ORDER BY $order_by ";
    if (!empty($offset)) $sql.= "LIMIT $limit,$offset ";
		$q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($q->result() as $row) {
    	$row->cliente = new stdClass();
    	$row->cliente->nombre = $row->cliente_nombre;
    	$row->cliente->email = $row->cliente_email;
    	$row->cliente->telefono = $row->cliente_telefono;
    	$salida[] = $row;
    }
		return array(
			"results"=>$salida,
			"total"=>$total->total
		);
	}

	function get($id) {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT R.*, ";
		$sql.= " IF(R.fecha_desde = '0000-00-00','',DATE_FORMAT(R.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
		$sql.= " IF(R.fecha_hasta = '0000-00-00','',DATE_FORMAT(R.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta, ";
		$sql.= " IF(R.fecha_reserva = '0000-00-00','',DATE_FORMAT(R.fecha_reserva,'%d/%m/%Y')) AS fecha_reserva ";
		$sql.= "FROM hot_reservas R ";
		$sql.= "WHERE id_empresa = $id_empresa AND id = $id ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return FALSE;
		$row = $q->row();

		$sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa AND id = $row->id_cliente ";
		$q = $this->db->query($sql);
		$row->cliente = $q->row();

		$this->load->model("Habitacion_Model");
		$row->habitacion = $this->Habitacion_Model->get($row->id_habitacion);

		$this->load->model("Tipo_Habitacion_Model");
		$row->tipo_habitacion = $this->Tipo_Habitacion_Model->get($row->habitacion->id_tipo_habitacion);

		return $row;
	}

	function buscar_calendario($conf = array()) {
		$id_empresa = isset($conf["id_empresa"])?$conf["id_empresa"]:parent::get_empresa();
		$ver_desde = isset($conf["desde"])?$conf["desde"]:"";
		$ver_hasta = isset($conf["hasta"])?$conf["hasta"]:"";
		$sql = "SELECT R.*, ";
		$sql.= " C.nombre AS title, H.id AS resourceId, ";
		$sql.= " DATE_FORMAT(R.fecha_desde, '%Y-%m-%d %H:%i:%s') AS start, ";
		$sql.= " DATE_FORMAT(R.fecha_hasta, '%Y-%m-%d %H:%i:%s') AS end ";
		$sql.= "FROM hot_reservas R ";
		$sql.= "INNER JOIN hot_habitaciones H ON (R.id_habitacion = H.id AND R.id_empresa = H.id_empresa) ";
		$sql.= "INNER JOIN clientes C ON (R.id_cliente = C.id AND R.id_empresa = C.id_empresa) ";
		$sql.= "WHERE 1=1 ";
		if (!empty($id_empresa)) $sql.= "AND R.id_empresa = $id_empresa ";
		if (!empty($ver_desde)) $sql.= "AND R.fecha_hasta >= '$ver_desde' ";
		if (!empty($ver_hasta)) $sql.= "AND R.fecha_desde <= '$ver_hasta' ";
		$q = $this->db->query($sql);
		return $q->result();
	}
	
	function save($data) {
		$this->load->helper("fecha_helper");
		$data->fecha_desde = fecha_mysql($data->fecha_desde);
		$data->fecha_hasta = fecha_mysql($data->fecha_hasta);
		return parent::save($data);
	}

	function insert($data) {

		$cliente = $data->cliente;
		$id_empresa = (isset($data->id_empresa) ? $data->id_empresa : parent::get_empresa());
		$data->fecha_reserva = date("Y-m-d");
		$data->hora_reserva = date("H:i:s");

		if (isset($data->id_cliente)) {
			if ($data->id_cliente == 0) {
				// Es un nuevo cliente	
				$this->load->model("Cliente_Model");
        $contacto = new stdClass();
        $contacto->id_empresa = $id_empresa;
        $contacto->email = $cliente->email;
        $contacto->nombre = $cliente->nombre;
        $contacto->telefono = $cliente->telefono;
        $contacto->fecha_inicial = date("Y-m-d");
        $contacto->fecha_ult_operacion = date("Y-m-d H:i:s");
        $contacto->tipo = 0; // 0 = Cliente
        $contacto->activo = 1; // El cliente esta activo por defecto
        $contacto->id_empresa = $id_empresa;
        $contacto->id_sucursal = 0; // Para que en algunas BD no tire error de default value
        $data->id_cliente = $this->Cliente_Model->insert($contacto);
			} else {
				// Tenemos que actualizar los datos del cliente
				$sql = "UPDATE clientes SET ";
				$sql.= " nombre = '$cliente->nombre', ";
				$sql.= " email = '$cliente->email', ";
				$sql.= " telefono = '$cliente->telefono' ";
				$sql.= "WHERE id = $data->id_cliente AND id_empresa = $data->id_empresa ";
				$this->db->query($sql);
			}
		}
		$id_reserva = parent::insert($data);

    // Obtenemos el proximo numero de reserva
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
    $sql.= "FROM hot_reservas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $q_num = $this->db->query($sql);
    $r_num = $q_num->row();
    $numero = $r_num->numero + 1;
    $this->db->query("UPDATE hot_reservas SET numero = $numero WHERE id = $id_reserva AND id_empresa = $id_empresa ");

    $this->load->model("Habitacion_Model");
    $habitacion = $this->Habitacion_Model->get($data->id_habitacion,$id_empresa);
    $this->load->model("Tipo_Habitacion_Model");
    $tipo_habitacion = $this->Tipo_Habitacion_Model->get($habitacion->id_tipo_habitacion,$id_empresa);

    if ($tipo_habitacion->compartida == 0) {
      // La habitacion no es compartida, sacamos de la disponibilidad el total maximo de la habitacion, para que quede inhabilitada
      $cant_personas = $tipo_habitacion->capacidad_maxima;
    } else {
      // La habitacion es compartida, sacamos de la disponibilidad solo la cantidad que estan reservando
      $cant_personas = $data->personas;
    }

    // Bajamos la disponibilidad para esas fechas
    $d = new DateTime($data->fecha_desde);
    $h = new DateTime($data->fecha_hasta);
    $interval = new DateInterval('P1D');
    $range = new DatePeriod($d,$interval,$h);
    foreach($range as $fecha) {
      $f = $fecha->format("Y-m-d");
      // Disminuimos la disponibilidad de la habitacion
      $this->mover_disponibilidad(array(
        "id_habitacion"=>$data->id_habitacion,
        "id_reserva"=>$id_reserva,
        "fecha"=>$f,
        "id_empresa"=>$id_empresa,
        "cantidad"=>$cant_personas,
        "operacion"=>"-",
      ));
    }

		return $id;
	}


	function update($id,$data) {

		// Actualizamos los datos del cliente
		$cliente = $data->cliente;
		if (isset($cliente->nombre)) {
			$sql = "UPDATE clientes SET ";
			$sql.= " nombre = '$cliente->nombre', ";
			$sql.= " email = '$cliente->email', ";
			$sql.= " telefono = '$cliente->telefono' ";
			$sql.= "WHERE id = $data->id_cliente AND id_empresa = $data->id_empresa ";
			$this->db->query($sql);
		}

		// Si la habitacion no es compartida
		if ($data->tipo_habitacion->compartida == 0) {
			// Borramos la disponibilidad
			$sql = "DELETE FROM hot_disponibilidad WHERE id_empresa = $data->id_empresa AND id_reserva = $id ";
			$this->db->query($sql);
			// Y la volvemos a crear entre las nuevas fechas y/o la nueva habitacion

			$cant_personas = $data->tipo_habitacion->capacidad_maxima;
	    $d = new DateTime($data->fecha_desde);
	    $h = new DateTime($data->fecha_hasta);
	    $interval = new DateInterval('P1D');
	    $range = new DatePeriod($d,$interval,$h);
	    foreach($range as $fecha) {
	      $f = $fecha->format("Y-m-d");
				$this->mover_disponibilidad(array(
					"id_empresa"=>$data->id_empresa,
					"id_habitacion"=>$data->id_habitacion,
					"id_reserva"=>$id,
					"cantidad"=>$cant_personas,
					"fecha"=>$f,
					"operacion"=>"-",
				));
	    }

		} else {
			// TODO: falta terminar si cambiamos de habitacion o fechas de una reserva sobre una habitacion compartida
		}
		// Ahora si actualizamos el registro de reservas
		return parent::update($id,$data);
	}

}