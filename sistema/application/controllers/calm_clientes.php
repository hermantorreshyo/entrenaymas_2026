<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Calm_Clientes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Calm_Cliente_Model', 'modelo');
  }

  function login() {

    header('Access-Control-Allow-Origin: *');
    header('Content-Type:application/json; charset=UTF-8');

    file_put_contents("log_registro_calm_clientes.txt", date("Y-m-d H:i:s")." ".print_r($_POST,true)."\n\n", FILE_APPEND);

    $email = $this->input->post("email");
    $password = $this->input->post("password");
    if (!empty($password)) $password = md5($password);

    if ($email === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se especifico el parametro email",
      ));
      exit();
    }
    $id_empresa = 997;
    
    $sql = "SELECT C.* ";
    $sql.= "FROM clientes C ";
    $sql.= "WHERE C.email = '$email' ";
    $sql.= "AND C.password = '$password' ";
    $sql.= "AND C.id_empresa = '$id_empresa' ";
    $sql.= "LIMIT 0,1 ";
    $query = $this->db->query($sql);

    // Datos invalidos
    $resultado = $query->result();
    if (empty($resultado)) {
      // Usuario incorrecto
      $men = "Nombre de usuario y/o claves incorrectos.";
      echo json_encode(array("error"=>1,"mensaje"=>$men));
      return;
    } else {
      $cliente = $query->row();
      $salida = array(
        "error"=>0,
        "mensaje"=>"",
        "id"=>$cliente->id,
        "tipo_plan"=>0,
      );
      echo json_encode($salida);
    }
  }

  function registrar() {

    header('Access-Control-Allow-Origin: *');
    header('Content-Type:application/json; charset=UTF-8');

    file_put_contents("log_registro_calm_clientes.txt", date("Y-m-d H:i:s")." ".print_r($_POST,true)."\n\n", FILE_APPEND);

    $id_empresa = 997;
    $email = $this->input->post("email");
    if (empty($email)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El email es obligatorio",
      ));
      exit();
    }
    $nombre = parent::get_post("nombre","");
    $password = parent::get_post("password","");
    if (!empty($password)) $password = md5($password);

    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get_by_email($email,$id_empresa);
    if ($cliente === FALSE) {
      $fecha = date("Y-m-d H:i:s");
      $cliente = new stdClass();
      $sql = "INSERT INTO clientes (nombre,email,password,id_empresa,activo,fecha_inicial) VALUES (";
      $sql.= "'$nombre','$email','$password','$id_empresa',1,'$fecha')";
      $this->db->query($sql);
      $id_cliente = $this->db->insert_id();
      $mensaje = "";

      // Enviamos un email de MUCHAS GRACIAS
      /*
      if (!empty($tipo_registro) && $id_empresa == 256) {
        $this->load->model("Empresa_Model");
        $empresa = $this->Empresa_Model->get($id_empresa);        
        $this->load->model("Email_Template_Model");
        $template = $this->Email_Template_Model->get_by_key("gracias-registro",$id_empresa);
        if ($template !== FALSE) {
          $bcc_array = array("basile.matias99@gmail.com");
          require APPPATH.'libraries/Mandrill/Mandrill.php';
          $body = $template->texto;
          $body = str_replace("{{nombre}}", $nombre, $body);
          mandrill_send(array(
            "to"=>$email,
            "from"=>MAIL_FROM_ADDRESS,
            "from_name"=>$empresa->nombre,
            "subject"=>$template->nombre,
            "body"=>$body,
            "reply_to"=>$empresa->email,
            "bcc"=>$bcc_array,
          ));
          $mensaje = "Thank you for your registration. We have sent an email to your mailbox to complete your profile.";
        }        
      }
      */

      $salida = array(
        "id"=>$id_cliente,
        "error"=>0,
        "tipo_plan"=>0,
        "mensaje"=>$mensaje,
      );      

    } else {
      $salida = array(
        "error"=>1,
        "mensaje"=>"El cliente ya esta registrado en el sistema. Por favor inicie sesion para entrar.",
      );
    }
    echo json_encode($salida);
  }


  function get($id) {
    if ($id == "index") {
      $limit = parent::get_get("limit",0);
      $offset = parent::get_get("offset",10);
      $filter = parent::get_get("filter","");
      $order_by = parent::get_get("order_by","A.nombre");
      $order = parent::get_get("order","ASC");
      $salida = $this->modelo->buscar(array(
        "limit"=>$limit,
        "offset"=>$offset,
        "filter"=>$filter,
        "order"=>$order,
        "order_by"=>$order_by,
      ));
      echo json_encode($salida);
    } else {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }
  }  

  function save_file() {
    $this->load->helper("file_helper");
    $this->load->helper("imagen_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    // Primero copiamos el archivo
    $filename = filename($_FILES["path"]["name"],"-");
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
	
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/entradas/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }	
	
}