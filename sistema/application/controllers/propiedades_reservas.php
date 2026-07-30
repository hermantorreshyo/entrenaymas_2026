<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Propiedades_Reservas extends REST_Controller {

  function __construct() {
    
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    parent::__construct();
    $this->load->model('Propiedad_Reserva_Model', 'modelo',"fecha ASC",1);
  }

  function ipn_reserva_mp($id_empresa) {

    set_time_limit(0);
    $filename = "logs/$id_empresa/propiedades_reservas_ipn_mp.txt";
    if (!file_exists("logs/$id_empresa")) mkdir("logs/$id_empresa");
    file_put_contents($filename,date("Y-m-d H:i:s").": Comienza ejecucion /sistema/propiedades_reservas/function/ipn_reserva_mp/\n\n",FILE_APPEND);
    
    // Si no esta definido el ID, devolvemos ERROR
    if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
      http_response_code(400);
      exit();
    }
    include_once("/home/ubuntu/data/models/mercadopago.php");

    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) {
      http_response_code(400);
      exit();
    }
    $medio = $q->row();
    if ($medio->habilitar_mp == 0) {
      http_response_code(400);
      exit();      
    }
    $mp = new MP($medio->mp_client_id, $medio->mp_client_secret);

    // Get the payment and the corresponding merchant_order reported by the IPN.
    if(isset($_GET["topic"]) && $_GET["topic"] == 'payment') {
      $payment_info = $mp->get("/collections/notifications/".$_GET["id"]);
      $merchant_order_info = $mp->get("/merchant_orders/".$payment_info["response"]["collection"]["merchant_order_id"]);
    } else {
      http_response_code(400);
      exit();
    }

    http_response_code(200);
    // La external reference tiene el ID del pedido
    $salida = print_r($merchant_order_info["response"],true);
    file_put_contents($filename,$salida."\n\n",FILE_APPEND);
    if (isset($merchant_order_info) & $merchant_order_info["status"] == 200) {
      $paid_amount = 0;
      $pago_pendiente = 0;
      foreach ($merchant_order_info["response"]["payments"] as  $payment) {
        if ($payment['status'] == 'approved'){
          $paid_amount += $payment['transaction_amount'];
        } else if ($payment['status'] == 'pending') {
          $pago_pendiente += $payment['transaction_amount'];
        }
      }
      $id_reserva = $merchant_order_info["response"]["external_reference"];
      file_put_contents($filename,"ID RESERVA: $id_reserva\n",FILE_APPEND);
      file_put_contents($filename,"TOTAL PAGADO: $paid_amount\n",FILE_APPEND);
      $this->load->model("Propiedad_Reserva_Model");
      $reserva = $this->Propiedad_Reserva_Model->get($id_reserva,array(
        "id_empresa"=>$id_empresa
      ));
      file_put_contents($filename,print_r($reserva,TRUE)."\n",FILE_APPEND);

      // Si el pago completa la totalidad de la reserva, y aun no ha sido pagada
      if ($paid_amount >= $reserva->precio && $reserva->id_estado != 2) {

        file_put_contents($filename,"EL PAGO ES COMPLETO\n\n",FILE_APPEND);

        // Actualizamos la reserva
        $sql = "UPDATE inm_propiedades_reservas SET ";
        $sql.= " id_estado = 2, total_pagado = '$paid_amount' ";
        $sql.= "WHERE id = $reserva->id AND id_empresa = $reserva->id_empresa";
        file_put_contents($filename,"$sql\n\n",FILE_APPEND);
        $this->db->query($sql);

        // Ponemos el pago
        $sql = "INSERT INTO inm_propiedades_reservas_pagos (id_empresa,id_reserva,metodo_pago,pago,fecha_pago,observaciones) VALUES (";
        $sql.= "$reserva->id_empresa, $reserva->id, 'Mercadopago', '$paid_amount', NOW(), '')";
        file_put_contents($filename,"$sql\n\n",FILE_APPEND);
        $this->db->query($sql);

        // Ponemos que el cliente ya concreto una venta
        $sql = "UPDATE clientes C INNER JOIN inm_propiedades_reservas H ON (C.id_empresa = H.id_empresa AND C.id = H.id_cliente) ";
        $sql.= "SET C.tipo = 0 ";
        $sql.= "WHERE id_empresa = $reserva->id_empresa AND H.id = $reserva->id ";
        file_put_contents($filename,"$sql\n\n",FILE_APPEND);
        $this->db->query($sql);

        $this->load->model("Empresa_Model");
        $empresa = $this->Empresa_Model->get_empresa_min($id_empresa);
        file_put_contents($filename,"Obtenemos la empresa\n\n",FILE_APPEND);

        // MANDAMOS EL EMAIL AL ADMINISTRADOR, CON LA NUEVA RESERVA
        $this->modelo->enviar_email_reserva(array(
          "clave"=>"reserva-pago-mp",
          "id_empresa"=>$id_empresa,
          "to"=>$empresa->email,
          "id_reserva"=>$id_reserva,
          "from_name"=>$empresa->nombre,
        ));
        file_put_contents($filename,"Enviamos el email\n\n",FILE_APPEND);
      }
    }
  }

  function ipn_reserva_paypal() {

  }

  // Utilizado en Calendar de Reservas
  function get_alquileres_temporarios() {
    $id_empresa = $this->get_empresa();
    $sql = "SELECT T.id, T.nombre, T.codigo ";
    $sql.= "FROM inm_propiedades T ";
    $sql.= "WHERE T.id_empresa = $id_empresa ";
    $sql.= "AND T.id_tipo_operacion = 3 ";
    $sql.= "AND T.activo > 0 ";
    $sql.= "ORDER BY T.nombre ASC ";
    $q = $this->db->query($sql);
    echo json_encode($q->result());
  }  

  // Registra una reserva para esa propiedad
  function reservar() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    $this->load->model("Cliente_Model");
    $this->load->helper("fecha_helper");

    $nombre = parent::get_post("nombre","");
    $apellido = parent::get_post("apellido","");
    if (!empty($apellido)) $nombre = $nombre." ".$apellido;
    $email = parent::get_post("email","");
    $telefono = parent::get_post("telefono","");
    $celular = parent::get_post("celular","");
    $mensaje = parent::get_post("mensaje","");
    $forma_pago = parent::get_post("forma_pago","");
    $fecha_nac = parent::get_post("fecha_nac","");
    $id_empresa = parent::get_post("id_empresa",0);
    $id_propiedad = parent::get_post("id_propiedad",0);
    $personas = parent::get_post("personas",1);
    $precio = parent::get_post("precio",0);
    $desde = ($this->input->post("desde") !== FALSE) ? $this->input->post("desde") : date("Y-m-d");
    $hasta = ($this->input->post("hasta") !== FALSE) ? $this->input->post("hasta") : date("Y-m-d");
    $precio_por_noche = parent::get_post("precio_por_noche",0);
    $precio_sin_descuento = parent::get_post("precio_sin_descuento",0);
    $cantidad_noches = parent::get_post("cantidad_noches",0);
    $descuento = parent::get_post("descuento",0);

    if (empty($id_empresa)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Falta parametro id_empresa."
      ));
      return;      
    }
    $filename = "logs/$id_empresa/propiedades_reservas.txt";
    if (!file_exists("logs/$id_empresa")) mkdir("logs/$id_empresa");
    file_put_contents($filename,date("Y-m-d H:i:s")."\n".print_r($_POST,TRUE),FILE_APPEND);    

    if (empty($id_propiedad)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Falta parametro id_propiedad."
      ));
      return;      
    }
    if (empty($email)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Por favor ingrese un email."
      ));
      return;
    }

    $contacto = $this->Cliente_Model->get_by_email($email,$id_empresa);
    if ($contacto === FALSE) {
      // Debemos crearlo
      $contacto = new stdClass();
      $contacto->id_empresa = $id_empresa;
      $contacto->email = $email;
      $contacto->nombre = $nombre;
      $contacto->telefono = $telefono;
      $contacto->celular = $celular;
      $contacto->fecha_nac = $fecha_nac;
      $contacto->fecha_inicial = date("Y-m-d");
      $contacto->fecha_ult_operacion = date("Y-m-d H:i:s");
      $contacto->id_tipo_iva = 4; // CF por defecto
      $contacto->forma_pago = "E"; // Efectivo
      $contacto->custom_3 = "1";
      $contacto->enviar_email = 1;
      $contacto->no_leido = 1;
      $contacto->activo = 1;
      $contacto->tipo = 1;
      $id = $this->Cliente_Model->insert($contacto);
      $contacto->id = $id;
    }

    // Obtenemos el proximo numero de reserva
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
    $sql.= "FROM inm_propiedades_reservas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $q_num = $this->db->query($sql);
    $r_num = $q_num->row();
    $numero = $r_num->numero + 1;

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql($desde);
    $hasta = fecha_mysql($hasta);

    $reserva = array(
      "id_empresa"=>$id_empresa,
      "id_cliente"=>$contacto->id,
      "fecha_desde"=>$desde,
      "fecha_hasta"=>$hasta,
      "personas"=>$personas,
      "precio"=>$precio,
      "descuento"=>$descuento,
      "precio_por_noche"=>$precio_por_noche,
      "precio_sin_descuento"=>$precio_sin_descuento,
      "cantidad_noches"=>$cantidad_noches,
      "id_propiedad"=>$id_propiedad,
      "id_estado"=>(($forma_pago == "COORDINAR") ? 3 : 0),
      "fecha_reserva"=>date("Y-m-d"),
      "hora_reserva"=>date("H:i:s"),
      "numero"=>$numero,
      "comentario"=>$mensaje,
    );
    $this->db->insert("inm_propiedades_reservas",$reserva);
    $id_reserva = $this->db->insert_id();

    // Bajamos la disponibilidad para esas fechas
    $d = new DateTime($desde);
    $h = new DateTime($hasta);
    $interval = new DateInterval('P1D');
    $range = new DatePeriod($d,$interval,$h);
    foreach($range as $fecha) {
      $f = $fecha->format("Y-m-d");
      // Disminuimos la disponibilidad de la habitacion
      $this->modelo->mover_disponibilidad(array(
        "id_propiedad"=>$id_propiedad,
        "id_reserva"=>$id_reserva,
        "fecha"=>$f,
        "id_empresa"=>$id_empresa,
        "cantidad"=>$personas,
        "operacion"=>"-",
      ));
    }

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_empresa_min($id_empresa);    

    if ($id_empresa == 685) $empresa->email = "reservas@tuapartenbariloche.com";
    $bcc_array = array(
      "basile.matias99@gmail.com",
      "misticastudio@gmail.com",
      $empresa->email, // Lo mandamos con copia oculta al admin
    );
    if ($id_empresa == 685) $bcc_array[] = "tuapartenbariloche@gmail.com";

    if ($forma_pago == "TRANSFERENCIA_BANCARIA") {
      $this->modelo->enviar_email_reserva(array(
        "clave"=>"datos-bancarios",
        "id_empresa"=>$id_empresa,
        "to"=>$email,
        "reply_to"=>$empresa->email,
        "id_reserva"=>$id_reserva,
        "from_name"=>$empresa->nombre,
        "bcc_array"=>$bcc_array,
      ));      
    } else if ($forma_pago == "WESTERN_UNION") {
      $this->modelo->enviar_email_reserva(array(
        "clave"=>"datos-western-union",
        "id_empresa"=>$id_empresa,
        "to"=>$email,
        "reply_to"=>$empresa->email,
        "id_reserva"=>$id_reserva,
        "from_name"=>$empresa->nombre,
        "bcc_array"=>$bcc_array,
      ));
    } else if ($forma_pago == "COORDINAR") {
      $this->modelo->enviar_email_reserva(array(
        "clave"=>"coordinar",
        "id_empresa"=>$id_empresa,
        "to"=>$email,
        "reply_to"=>$empresa->email,
        "id_reserva"=>$id_reserva,
        "from_name"=>$empresa->nombre,
        "bcc_array"=>$bcc_array,
      ));
    } else {
      // MANDAMOS EL EMAIL AL USUARIO CON LA NUEVA RESERVA
      $this->modelo->enviar_email_reserva(array(
        "clave"=>"reserva",
        "id_empresa"=>$id_empresa,
        "to"=>$email,
        "reply_to"=>$empresa->email,
        "id_reserva"=>$id_reserva,
        "from_name"=>$empresa->nombre,
        "bcc_array"=>$bcc_array,
      ));
    }
    
    echo json_encode(array(
      "error"=>0,
      "id_reserva"=>$id_reserva
    ));
  }   

  function buscar() {
    $this->load->helper("fecha_helper");
    $conf = array(
      "id_empresa"=>parent::get_empresa(),
      "desde"=>fecha_mysql(parent::get_get("desde",date("d-m-Y"))),
      "hasta"=>fecha_mysql(parent::get_get("hasta",date("d-m-Y"))),
      "filter"=>parent::get_get("filter",""),
      "limit"=>parent::get_get("limit",0),
      "offset"=>parent::get_get("offset",30),
      "tipo_estado"=>parent::get_get("tipo_estado",-1),
    );
    $salida = $this->modelo->buscar($conf);
    echo json_encode($salida);
  }

  function calendario() {
    $conf = array();
    $conf["id_empresa"] = parent::get_empresa();
    $conf["desde"] = $this->input->get("start");
    $conf["hasta"] = $this->input->get("end");
    $salida = $this->modelo->buscar_calendario($conf);
    echo json_encode($salida);
  }

  function imprimir($id_reserva) {
    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $reserva = $this->modelo->get($id_reserva);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($reserva->id_empresa);
    $header = $this->load->view("reports/propiedad_reserva/header",null,true);
    $this->load->view("reports/propiedad_reserva/reserva",array(
      "reserva"=>$reserva,
      "empresa"=>$empresa,
      "header"=>$header,
    ));
  }

}