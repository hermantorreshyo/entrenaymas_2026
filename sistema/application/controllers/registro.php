<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registro extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function entrenaymas() {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$tipo = $this->input->post("tipo");
		$profesional = $this->input->post("profesional");
		$profesional_nombre = $this->input->post("profesional_nombre");
		$nombre = $this->input->post("nombre");
		$localidad = $this->input->post("localidad");
		$id_localidad = $this->input->post("id_localidad");
		$direccion = $this->input->post("direccion");
		$provincia = $this->input->post("provincia");
		$id_provincia = $this->input->post("id_provincia");
		$id_pais = $this->input->post("id_pais");
		$pais = $this->input->post("pais");
		$telefono = $this->input->post("telefono");
		$email = $this->input->post("email");
		$password = $this->input->post("password");
		$numero = $this->input->post("numero");
		$latitud = $this->input->post("latitud");
		$longitud = $this->input->post("longitud");
    $sobre_mi = $this->input->post("sobre_mi");
		$id_empresa = 1319;

		//Tipos
		$subscription = $this->input->post("subscription");
		$cupon = $this->input->post("cupon");
		//Mensual o Anual
		$cuota = $this->input->post("cuota");

		// Controlamos que el email ya no este usado
		$this->load->model("Usuario_Model");
		if ($this->Usuario_Model->existe_usuario(array(
			"id_empresa"=>$id_empresa,
			"email"=>$email
		))) {
			echo json_encode(array(
				"error"=>1,
				"mensaje"=>"Ya existe un usuario registrado con el email $email.",
			));
			exit();
		}		

		$data = new stdClass();
		$data->id = 0;
		$data->id_empresa = $id_empresa;
		$data->nombre = $nombre;
		$data->id_perfiles = 1357;
		$data->activo = 0;
		$data->fecha_alta = date("Y-m-d H:i:s");
		$data->password = md5($password);
		$data->celular = $telefono;
		$data->email = $email;
		$data->direccion = $direccion;
		$data->id_provincia = $id_provincia;
		$data->id_localidad = $id_localidad;		
		$data->provincia = $provincia;
		$data->localidad = $localidad;
		$data->id_pais = $id_pais;
		$data->pais = $pais;
		$data->cargo = $numero;
		$data->custom_7 = $tipo; // TODO: CENTRO O PROFESIONAL
		$data->custom_8 = $id_localidad; // LOCALIDAD
		$data->titulos = array($profesional);
		$data->latitud = $latitud;
		$data->longitud = $longitud;
    $data->sobre_mi = $sobre_mi;
    $data->seo_title = $nombre." ".$profesional_nombre;
    $data->seo_description = $sobre_mi;
    $data->cupon = $cupon;
		$data->path = "";

		$dir = new stdClass();
		$dir->id = 0;
		$dir->id_empresa = $id_empresa;
		$dir->id_provincia = $id_provincia;
		$dir->id_localidad = $id_localidad;		
		$dir->provincia = $provincia;
		$dir->localidad = $localidad;
		$dir->valor = 0;
		$data->localidades = array($dir);

		$id = $this->Usuario_Model->save($data);

		$this->load->model("Empresa_Model");
		$empresa = $this->Empresa_Model->get($id_empresa);

		$this->load->model("Email_Template_Model");
		$template = $this->Email_Template_Model->get_by_key("email-nuevo-registro",$id_empresa);
    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $body = $template->texto;
    $body = str_replace("{{nombre}}", $nombre, $body);
    mandrill_send(array(
      "to"=>$email,
      "from"=>MAIL_FROM_ADDRESS,
      "from_name"=>"Entrena y Mas",
      "subject"=>$template->nombre,
      "body"=>$body,
      "bcc"=>array(
      	//$empresa->email,
      	"basile.matias99@gmail.com",
      	"info@entrenaymas.com",
      ),
    ));
		echo json_encode(array(
			"error"=>0,
			"id"=>$id,
		));
	}

}