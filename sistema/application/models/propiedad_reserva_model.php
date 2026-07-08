<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Propiedad_Reserva_Model extends Abstract_Model {
  
  function __construct() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    parent::__construct("inm_propiedades_reservas","id","fecha_desde DESC",1);
  }

  function enviar_email_reserva($config = array()) {

    $clave = (isset($config["clave"]) ? $config["clave"] : "");
    if (empty($clave)) return;
    $to = (isset($config["to"]) ? $config["to"] : "");
    if (empty($to)) return;
    $from = (isset($config["from"]) ? $config["from"] : "no-reply@varcreative.com");
    $from_name = (isset($config["from_name"]) ? $config["from_name"] : "");
    $bcc_array = (isset($config["bcc_array"]) ? $config["bcc_array"] : array("basile.matias99@gmail.com"));
    $reply_to = (isset($config["reply_to"]) ? $config["reply_to"] : "");
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $id_reserva = (isset($config["id_reserva"])) ? $config["id_reserva"] : 0;

    include_once APPPATH.'libraries/Mandrill/Mandrill.php';
    $this->load->helper("fecha_helper");

    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key($clave,$id_empresa);
    if (empty($template)) return;

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_empresa_min($id_empresa);    

    $reserva = $this->get($id_reserva,array(
      "id_empresa"=>$id_empresa,
    ));

    $body = $template->texto;
    // Si no tiene descuento, ocultamos el id
    /*
    if ($reserva->descuento == 0) {
      $doc = new DOMDocument();
      $doc->loadHTML($body);
      $x = new DOMXPath($doc);
      $element = $x->query("//*[@id='bloque_descuento']")->item(0);
      if (!is_null($element)) {
        $element->parentNode->removeChild($element);
        $body = $doc->saveHTML();
      }
    }
    */
    $desde_full = ($reserva->fecha_desde);
    $hasta_full = ($reserva->fecha_hasta);
    $body = str_replace("{{cliente_nombre}}",htmlentities($reserva->cliente->nombre,ENT_QUOTES),$body);
    $body = str_replace("{{cliente_email}}",htmlentities($reserva->cliente->email,ENT_QUOTES),$body);
    $body = str_replace("{{cliente_telefono}}",htmlentities($reserva->cliente->telefono,ENT_QUOTES),$body);
    $body = str_replace("{{cliente_celular}}",htmlentities($reserva->cliente->celular,ENT_QUOTES),$body);
    $body = str_replace("{{propiedad_imagen}}","https://www.varcreative.com/sistema/".$reserva->propiedad->path,$body);
    $body = str_replace("{{propiedad_nombre}}",htmlentities($reserva->propiedad->nombre,ENT_QUOTES),$body);
    $body = str_replace("{{propiedad_capacidad}}",$reserva->propiedad->capacidad_maxima,$body);
    $body = str_replace("{{reserva_mensaje}}",htmlentities($reserva->comentario,ENT_QUOTES),$body);
    $body = str_replace("{{reserva_desde}}",$desde_full,$body);
    $body = str_replace("{{reserva_hasta}}",$hasta_full,$body);
    $body = str_replace("{{reserva_personas}}",(($reserva->personas==1)?"1 persona":($reserva->personas." personas")),$body);
    $body = str_replace("{{reserva_precio_sin_descuento}}",number_format($reserva->precio_sin_descuento,2),$body);
    $body = str_replace("{{reserva_precio_por_noche}}",number_format($reserva->precio_por_noche,2),$body);
    $body = str_replace("{{reserva_cantidad_noches}}",number_format($reserva->cantidad_noches,2),$body);
    $body = str_replace("{{reserva_descuento}}",number_format($reserva->descuento,2),$body);
    $body = str_replace("{{reserva_total}}",number_format($reserva->precio,2),$body);
    mandrill_send(array(
      "to"=>$to,
      "from"=>$from,
      "from_name"=>$from_name,
      "subject"=>$template->nombre,
      "body"=>$body,
      "reply_to"=>$reply_to,
      "bcc"=>$bcc_array,
    ));
  }

  function mover_disponibilidad($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $operacion = (isset($config["operacion"])) ? $config["operacion"] : "-";
    $cantidad = (isset($config["cantidad"])) ? $config["cantidad"] : 0;
    $fecha = (isset($config["fecha"])) ? $config["fecha"] : "";
    $id_propiedad = (isset($config["id_propiedad"])) ? $config["id_propiedad"] : 0;
    $id_reserva = (isset($config["id_reserva"])) ? $config["id_reserva"] : 0;

    $sql = "SELECT * FROM inm_propiedades_reservas_disponibilidad WHERE id_empresa = $id_empresa ";
    if (!empty($id_propiedad)) $sql.= "AND id_propiedad = $id_propiedad ";
    if (!empty($id_reserva)) $sql.= "AND id_reserva = $id_reserva ";
    if (!empty($fecha)) $sql.= "AND fecha = '$fecha' ";
    $q_hab = $this->db->query($sql);
    if ($q_hab->num_rows()<=0) {
      $this->load->model("Propiedad_Model");
      $propiedad = $this->Propiedad_Model->get($id_propiedad,array(
        "id_empresa"=>$id_empresa
      ));
      // Insertamos el registro
      $sql = "INSERT INTO inm_propiedades_reservas_disponibilidad (id_empresa,id_propiedad,fecha,disponible,id_reserva) VALUES(";
      $sql.= "$id_empresa,$id_propiedad,'$fecha','$propiedad->capacidad_maxima','$id_reserva')";
      $this->db->query($sql);
    }
    // Ahora actualizamos todos los registros que corresponden
    $sql = "UPDATE inm_propiedades_reservas_disponibilidad ";
    if ($operacion == "+") $sql.= " SET disponible = disponible + $cantidad ";
    else $sql.= " SET disponible = disponible - $cantidad ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id_propiedad)) $sql.= "AND id_propiedad = $id_propiedad ";
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
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "R.id DESC ";

    $sql = "SELECT SQL_CALC_FOUND_ROWS R.*, ";
    $sql.= " H.nombre AS propiedad, ";
    $sql.= " IF(C.nombre IS NULL,'Importado Calendario',C.nombre) AS cliente_nombre, ";
    $sql.= " IF(C.email IS NULL,'',C.email) AS cliente_email, ";
    $sql.= " IF(C.telefono IS NULL,'',C.telefono) AS cliente_telefono, ";
    $sql.= " IF(R.fecha_desde = '0000-00-00','',DATE_FORMAT(R.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= " IF(R.fecha_hasta = '0000-00-00','',DATE_FORMAT(R.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta, ";
    $sql.= " IF(R.fecha_reserva = '0000-00-00','',DATE_FORMAT(R.fecha_reserva,'%d/%m/%Y')) AS fecha_reserva ";
    $sql.= "FROM inm_propiedades_reservas R ";
    $sql.= "LEFT JOIN clientes C ON (R.id_empresa = C.id_empresa AND R.id_cliente = C.id) ";
    $sql.= "INNER JOIN inm_propiedades H ON (R.id_empresa = H.id_empresa AND R.id_propiedad = H.id) ";
    $sql.= "WHERE R.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
    if (!empty($desde)) $sql.= "AND R.fecha_desde >= $desde ";
    if (!empty($hasta)) $sql.= "AND R.fecha_hasta <= $hasta ";
    if ($tipo_estado != -1) $sql.= "AND R.id_estado = $tipo_estado ";
    if (!empty($id_cliente)) $sql.= "AND R.id_cliente = $id_cliente ";
    $sql.= "ORDER BY R.fecha_desde DESC ";
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

  function get($id,$config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT R.*, ";
    $sql.= " IF(R.fecha_desde = '0000-00-00','',DATE_FORMAT(R.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= " IF(R.fecha_hasta = '0000-00-00','',DATE_FORMAT(R.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta, ";
    $sql.= " IF(R.fecha_reserva = '0000-00-00','',DATE_FORMAT(R.fecha_reserva,'%d/%m/%Y')) AS fecha_reserva ";
    $sql.= "FROM inm_propiedades_reservas R ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;
    $row = $q->row();

    $sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa AND id = $row->id_cliente ";
    $q = $this->db->query($sql);
    $row->cliente = $q->row();

    $row->pagos = array();
    $sql = "SELECT P.*, IF(P.fecha_pago != '0000-00-00',DATE_FORMAT(P.fecha_pago,'%d/%m/%Y'),'') AS fecha_pago ";
    $sql.= "FROM inm_propiedades_reservas_pagos P WHERE P.id_empresa = $id_empresa AND P.id_reserva = $id ";
    $q = $this->db->query($sql);
    $row->pagos = $q->result();

    $this->load->model("Propiedad_Model");
    $row->propiedad = $this->Propiedad_Model->get($row->id_propiedad,array(
      "id_empresa"=>$id_empresa,
    ));
    return $row;
  }

  function buscar_calendario($conf = array()) {
    $id_empresa = isset($conf["id_empresa"])?$conf["id_empresa"]:parent::get_empresa();
    $ver_desde = isset($conf["desde"])?$conf["desde"]:"";
    $ver_hasta = isset($conf["hasta"])?$conf["hasta"]:"";
    $sql = "SELECT R.*, ";
    $sql.= " IF(C.nombre IS NULL,'Importado Calendario',C.nombre) AS title, H.id AS resourceId, ";
    $sql.= " DATE_FORMAT(R.fecha_desde, '%Y-%m-%d %H:%i:%s') AS start, ";
    $sql.= " DATE_FORMAT(R.fecha_hasta, '%Y-%m-%d %H:%i:%s') AS end ";
    $sql.= "FROM inm_propiedades_reservas R ";
    $sql.= "INNER JOIN inm_propiedades H ON (R.id_propiedad = H.id AND R.id_empresa = H.id_empresa) ";
    $sql.= "LEFT JOIN clientes C ON (R.id_cliente = C.id AND R.id_empresa = C.id_empresa) ";
    $sql.= "WHERE R.eliminada = 0 ";
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

    $this->load->helper("fecha_helper");
    $cliente = $data->cliente;
    $id_empresa = (isset($data->id_empresa) ? $data->id_empresa : parent::get_empresa());
    $pagos = (isset($data->pagos) ? $data->pagos : array());
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
        $contacto->celular = $cliente->celular;
        $contacto->fecha_inicial = date("Y-m-d");
        $contacto->custom_3 = "1";
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
        $sql.= " celular = '$cliente->celular', ";
        $sql.= " telefono = '$cliente->telefono' ";
        $sql.= "WHERE id = $data->id_cliente AND id_empresa = $data->id_empresa ";
        $this->db->query($sql);
      }
    }
    $id_reserva = parent::insert($data);

    // Guardamos los pagos
    foreach($pagos as $pago) {
      $pago->fecha_pago = fecha_mysql($pago->fecha_pago);
      $sql = "INSERT INTO inm_propiedades_reservas_pagos (id_empresa,id_reserva,metodo_pago,pago,fecha_pago,observaciones) VALUES (";
      $sql.= "'$data->id_empresa', '$id_reserva', '$pago->metodo_pago', '$pago->pago', '$pago->fecha_pago', '$pago->observaciones')";
      $this->db->query($sql);
    }

    // Obtenemos el proximo numero de reserva
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
    $sql.= "FROM inm_propiedades_reservas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $q_num = $this->db->query($sql);
    $r_num = $q_num->row();
    $numero = $r_num->numero + 1;
    $this->db->query("UPDATE inm_propiedades_reservas SET numero = $numero WHERE id = $id_reserva AND id_empresa = $id_empresa ");

    $this->load->model("Propiedad_Model");
    $propiedad = $this->Propiedad_Model->get($data->id_propiedad,array(
      "id_empresa"=>$id_empresa
    ));

    //if ($propiedad->compartida == 0) {
      // La propiedad no es compartida, sacamos de la disponibilidad el total maximo de la propiedad, para que quede inhabilitada
      $cant_personas = $propiedad->capacidad_maxima;
    //} else {
      // La propiedad es compartida, sacamos de la disponibilidad solo la cantidad que estan reservando
      //$cant_personas = $data->personas;
    //}

    // Bajamos la disponibilidad para esas fechas
    $d = new DateTime($data->fecha_desde);
    $h = new DateTime($data->fecha_hasta);
    $interval = new DateInterval('P1D');
    $range = new DatePeriod($d,$interval,$h);
    foreach($range as $fecha) {
      $f = $fecha->format("Y-m-d");
      // Disminuimos la disponibilidad de la propiedad
      $this->mover_disponibilidad(array(
        "id_propiedad"=>$data->id_propiedad,
        "id_reserva"=>$id_reserva,
        "fecha"=>$f,
        "id_empresa"=>$id_empresa,
        "cantidad"=>$cant_personas,
        "operacion"=>"-",
      ));
    }
    return $id_reserva;
  }


  function update($id,$data) {

    // Actualizamos los datos del cliente
    $this->load->helper("fecha_helper");
    $pagos = isset($data->pagos) ? $data->pagos : array();
    $cliente = $data->cliente;
    if (isset($cliente->nombre)) {
      $sql = "UPDATE clientes SET ";
      $sql.= " nombre = '$cliente->nombre', ";
      $sql.= " email = '$cliente->email', ";
      $sql.= " celular = '$cliente->celular', ";
      $sql.= " telefono = '$cliente->telefono' ";
      $sql.= "WHERE id = $data->id_cliente AND id_empresa = $data->id_empresa ";
      $this->db->query($sql);
    }

    // Borramos la disponibilidad
    $sql = "DELETE FROM inm_propiedades_reservas_disponibilidad WHERE id_empresa = $data->id_empresa AND id_reserva = $id ";
    $this->db->query($sql);
    // Y la volvemos a crear entre las nuevas fechas y/o la nueva propiedad

    $cant_personas = $data->propiedad->capacidad_maxima;
    $d = new DateTime($data->fecha_desde);
    $h = new DateTime($data->fecha_hasta);
    $interval = new DateInterval('P1D');
    $range = new DatePeriod($d,$interval,$h);
    foreach($range as $fecha) {
      $f = $fecha->format("Y-m-d");
      $this->mover_disponibilidad(array(
        "id_empresa"=>$data->id_empresa,
        "id_propiedad"=>$data->id_propiedad,
        "id_reserva"=>$id,
        "cantidad"=>$cant_personas,
        "fecha"=>$f,
        "operacion"=>"-",
      ));
    }
    // Ahora si actualizamos el registro de reservas
    $aff = parent::update($id,$data);

    // Guardamos los pagos
    $this->db->query("DELETE FROM inm_propiedades_reservas_pagos WHERE id_reserva = $id AND id_empresa = $data->id_empresa");
    foreach($pagos as $pago) {
      $pago->fecha_pago = fecha_mysql($pago->fecha_pago);
      $sql = "INSERT INTO inm_propiedades_reservas_pagos (id_empresa,id_reserva,metodo_pago,pago,fecha_pago,observaciones) VALUES (";
      $sql.= "'$data->id_empresa', '$id', '$pago->metodo_pago', '$pago->pago', '$pago->fecha_pago', '$pago->observaciones')";
      $this->db->query($sql);
    }

    return $aff;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM inm_propiedades_reservas_disponibilidad WHERE id_empresa = $id_empresa AND id_reserva = $id ");
    return parent::delete($id);
  }  

}