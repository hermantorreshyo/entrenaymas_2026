<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Viajes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Viaje_Model', 'modelo');
  }

  function upload_images($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_images(array(
      "id_empresa"=>$id_empresa,
      "clave_width"=>"viaje_galeria_image_width",
      "clave_height"=>"viaje_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/entradas/",
    ));
  }

  function mover_asientos() {
    $id_empresa = parent::get_empresa();
    

    
    $sql = "UPDATE via_reservas_asientos ";
    $sql.= "SET id_asiento = ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_reserva = $id_reserva ";
    $sql.= "AND id_vehiculo = $id_vehiculo ";
  }

  function buscar($min=0) {
    $id_empresa = parent::get_empresa();
    $filter = ($this->input->get("filter") === FALSE) ? "" : $this->input->get("filter");
    $id_usuario = ($this->input->get("id_usuario") === FALSE) ? 0 : $this->input->get("id_usuario");
    $activo = ($this->input->get("activo") === FALSE) ? -1 : $this->input->get("activo");
    $id_tripulante = ($this->input->get("id_tripulante") === FALSE) ? 0 : $this->input->get("id_tripulante");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->buscar(array(
      "activo"=>$activo,
      "filter"=>$filter,
      "id_usuario"=>$id_usuario,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
      "id_tripulante"=>$id_tripulante,
    ));
    echo json_encode($r);
  }

  function duplicar($id) {
    $viaje = $this->modelo->get($id);
    if ($viaje === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el viaje con ID: $id",
      ));
      return;
    }
    $viaje->id = 0;
    $insert_id = $this->modelo->save($viaje);
    echo json_encode(array(
      "id"=>$insert_id
    ));
  }


  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/entradas/";
    $filename = $this->input->post("file");
    $res = parent::save_image($dir,$filename);
    $thumbnail_width = $this->input->post("thumbnail_width");
    if (!empty($thumbnail_width)) {
      $resp = json_decode($res);
      $filename = str_replace($dir, "", $resp->path);
      $thumbnail_width = $this->input->post("thumbnail_width");
      $thumbnail_height = $this->input->post("thumbnail_height");
      parent::thumbnails(array(
        "dir"=>$dir,
        "preffix"=>"thumb_",
        "filename"=>$filename,
        "thumbnail_width"=>$thumbnail_width,
        "thumbnail_height"=>$thumbnail_height,                
      ));
    }        
    echo $res;
  }

  function save_file() {
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    $filename = filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/entradas/$filename";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
    echo json_encode(array(
      "path"=>$path,
      "error"=>0,
    ));
  }


  function imprimir_contrato($id_viaje) {
    
    $id_empresa = parent::get_empresa();

    // Obtenemos los datos del viaje
    $viaje = $this->modelo->get($id_viaje);
    $viaje->tripulantes = array();

    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($viaje->id_cliente);

    // Solamente tomamos el vehiculo que se esta imprimiendo
    $chofer = "";
    foreach($viaje->vehiculos_tripulantes as $v) {
      $viaje->tripulantes[] = $v->tripulante." DNI: ".$v->dni;
      $viaje->matricula = $v->patente;
      $viaje->capacidad_vehiculo = $v->cant_asientos_piso_1;
      $chofer = $v->tripulante;
    }
    //print_r($viaje); exit();

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($viaje->id_empresa);
    $this->load->view("reports/viajes/contrato",array(
      "viaje"=>$viaje,
      "dia"=>date("d"),
      "mes"=>date("m"),
      "anio"=>date("Y"),
      "cliente"=>$cliente,
      "empresa"=>$empresa,
      "chofer"=>$chofer,
    ));
  }

  function imprimir_manifiesto() {
    
    $id_empresa = parent::get_empresa();
    $id_viaje = $this->input->get("id_viaje");
    $id_vehiculo = $this->input->get("id_vehiculo");

    // Obtenemos los datos del viaje
    $viaje = $this->modelo->get($id_viaje);
    $viaje->tripulantes = array();

    // Solamente tomamos el vehiculo que se esta imprimiendo
    foreach($viaje->vehiculos_tripulantes as $v) {
      if ($v->id_vehiculo == $id_vehiculo) {
        $viaje->tripulantes[] = $v->tripulante." DNI: ".$v->dni;
        $viaje->matricula = $v->patente;
      }
    }
    // Solamente tomamos el vehiculo que se esta imprimiendo
    $pasajeros = array();
    foreach($viaje->pasajeros as $v) {
      if ($v->id_vehiculo == $id_vehiculo) {
        $pasajeros[] = $v;
      }
    }
    $viaje->pasajeros = $pasajeros;

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($viaje->id_empresa);
    $this->load->view("reports/viajes/manifiesto",array(
      "viaje"=>$viaje,
      "empresa"=>$empresa,
    ));
  }

  function imprimir_pasajeros() {
    
    $id_empresa = parent::get_empresa();
    $id_viaje = $this->input->get("id_viaje");
    $id_vehiculo = $this->input->get("id_vehiculo");
    $this->load->model("Reserva_Asiento_Model");

    // Obtenemos los datos del viaje
    $viaje = $this->modelo->get($id_viaje);
    // Solamente tomamos el vehiculo que se esta imprimiendo
    foreach($viaje->vehiculos_tripulantes as $v) {
      if ($v->id_vehiculo == $id_vehiculo) {
        $viaje->matricula = $v->patente;
      }
    }
    // Solamente tomamos el vehiculo que se esta imprimiendo
    $pasajeros = array();
    $reservas = array();
    foreach($viaje->pasajeros as $v) {
      if ($v->id_vehiculo == $id_vehiculo) {
        // Solamente el primer pasajero de una reserva le calculamos la diferencia que le falta pagar
        if (!in_array($v->id_reserva, $reservas)) {
          $res = $this->Reserva_Asiento_Model->get($v->id_reserva);
          $v->resto = $res->total - $res->pagado;
          $v->vendedor = $res->vendedor;
        } else {
          $v->resto = 0;
          $v->vendedor = "";
        }
        $pasajeros[] = $v;
        $reservas[] = $v->id_reserva;
      }
    }
    $viaje->pasajeros = $pasajeros;

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($viaje->id_empresa);
    $this->load->view("reports/viajes/pasajeros",array(
      "viaje"=>$viaje,
      "empresa"=>$empresa,
    ));
  }


  function imprimir_taquilla() {
    
    $id_empresa = parent::get_empresa();
    $id_viaje = $this->input->get("id_viaje");
    $id_vehiculo = $this->input->get("id_vehiculo");
    $this->load->model("Reserva_Asiento_Model");
    $this->load->model("Asiento_Model");
    $asientos = $this->Asiento_Model->ver(array(
      "id_viaje"=>$id_viaje,
      "id_vehiculo"=>$id_vehiculo,
    ));

    // Obtenemos los datos del viaje
    $viaje = $this->modelo->get($id_viaje);
    // Solamente tomamos el vehiculo que se esta imprimiendo
    foreach($viaje->vehiculos_tripulantes as $v) {
      if ($v->id_vehiculo == $id_vehiculo) {
        $viaje->matricula = $v->patente;
      }
    }
    // Solamente tomamos el vehiculo que se esta imprimiendo
    $pasajeros = array();
    $reservas = array();
    foreach($viaje->pasajeros as $v) {
      if ($v->id_vehiculo == $id_vehiculo) {
        // Solamente el primer pasajero de una reserva le calculamos la diferencia que le falta pagar
        if (!in_array($v->id_reserva, $reservas)) {
          $res = $this->Reserva_Asiento_Model->get($v->id_reserva);
          $v->resto = $res->total - $res->pagado;
        } else {
          $v->resto = 0;
        }
        $pasajeros[] = $v;
        $reservas[] = $v->id_reserva;
      }
    }
    $viaje->pasajeros = $pasajeros;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($viaje->id_empresa);
    $this->load->view("reports/viajes/taquilla",array(
      "viaje"=>$viaje,
      "asientos"=>$asientos["results"],
      "empresa"=>$empresa,
    ));
  }

  function imprimir_boleto() {
    
    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $id_reserva = $this->input->get("id_reserva");
    $this->load->model("Reserva_Asiento_Model");
    $reserva = $this->Reserva_Asiento_Model->get($id_reserva);
    $viaje = $this->modelo->get($reserva->id_viaje);

    $boletos = array();
    foreach($reserva->asientos as $r) {
      $o = new stdClass();
      $o->viaje_nombre = $viaje->nombre;
      $o->viaje_fecha = $viaje->fecha;
      $o->viaje_fecha_llegada = $viaje->fecha_llegada;
      $o->nombre = $r->nombre;
      $o->apellido = $r->apellido;
      $o->dni = $r->dni;
      $o->precio = $r->precio;
      $o->numero_asiento = $r->numero_asiento;
      $o->piso = $r->piso;
      $o->fecha_reserva = $reserva->fecha_reserva;
      $o->hotel = $r->hotel;
      $o->tipo_habitacion = $r->tipo_habitacion;
      $o->numero_habitacion = $r->numero_habitacion;
      $boletos[] = $o;
    }

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($viaje->id_empresa);
    $header = $this->load->view("reports/viajes/header",null,true);
    $this->load->view("reports/viajes/boleto",array(
      "boletos"=>$boletos,
      "empresa"=>$empresa,
      "header"=>$header,
    ));
  }

  function imprimir_recibo() {
    
    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $id_reserva = $this->input->get("id_reserva");
    $this->load->model("Reserva_Asiento_Model");
    $reserva = $this->Reserva_Asiento_Model->get($id_reserva);
    $viaje = $this->modelo->get($reserva->id_viaje);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($viaje->id_empresa);
    $header = $this->load->view("reports/viajes/header",null,true);
    $this->load->view("reports/viajes/recibo",array(
      "viaje"=>$viaje,
      "reserva"=>$reserva,
      "empresa"=>$empresa,
      "header"=>$header,
    ));
  }

  
  function ver_comisiones() {

    $data = array();
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $fecha_desde = parent::get_post("fecha_desde","");
    $fecha_hasta = parent::get_post("fecha_hasta","");
    $id_vendedor = parent::get_post("id_vendedor",0);

    // Acomodamos los datos de entrada
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));

    // Calculamos el saldo inicial
    $sql = "SELECT IF(SUM(RA.comision_vendedor) IS NULL,0,SUM(RA.comision_vendedor)) AS saldo ";
    $sql.= "FROM via_reservas_asientos RA ";
    $sql.= "WHERE RA.id_empresa = $id_empresa ";
    $sql.= "AND RA.id_vendedor = '$id_vendedor' ";
    $sql.= "AND RA.fecha_mov < '$fecha_desde' ";
    $query = $this->db->query($sql);
    $row = $query->row();
    $data["saldo_inicial"] = $row->saldo;

    // Obtenemos los registros que estan dentro del intervalo de fechas
    $sql = "SELECT RA.id, IF(V.nombre IS NULL,'',V.nombre) AS viaje, ";
    $sql.= "RA.nombre, RA.tipo_habitacion, RA.id_reserva, ";
    $sql.= "RA.precio AS total, RA.comision_vendedor, ";
    $sql.= "(RA.recargo + RA.recargo_2 + RA.recargo_3 + RA.recargo_4) AS adicionales, ";
    $sql.= "DATE_FORMAT(RA.fecha_mov,'%d/%m/%Y') AS fecha_mov ";
    $sql.= "FROM via_reservas_asientos RA ";
    $sql.= "LEFT JOIN via_reservas R ON (RA.id_reserva = R.id AND RA.id_empresa = R.id_empresa) ";
    $sql.= "LEFT JOIN via_viajes V ON (R.id_viaje = V.id AND R.id_empresa = V.id_empresa) ";
    $sql.= "WHERE RA.id_empresa = $id_empresa ";
    $sql.= "AND RA.id_vendedor = '$id_vendedor' ";
    $sql.= "AND '$fecha_desde' <= RA.fecha_mov AND RA.fecha_mov <= '$fecha_hasta' ";
    $sql.= "ORDER BY RA.fecha_mov ASC";
    $query = $this->db->query($sql);
    $data["datos"] = $query->result();

    echo json_encode($data);
  }

  function editar_comision() {
    $id_empresa = parent::get_empresa();
    $id = parent::get_post("id");
    $valor = parent::get_post("valor");
    $this->db->query("UPDATE via_reservas_asientos SET comision_vendedor = $valor WHERE id_empresa = $id_empresa AND id = $id ");
    echo json_encode(array());
  }

}