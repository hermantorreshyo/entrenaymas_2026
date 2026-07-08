<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("/var/www/sistema/application/libraries/Paycomet_Bankstore.php");
require_once("abstract_model.php");

class Paycomet_Model extends Abstract_Model {
	
	private $merchantCode = PAYCOMET_CODE;
	private $terminal = PAYCOMET_TERMINAL;
	private $password = PAYCOMET_PASSWORD;
	private $jetid = PAYCOMET_JET_ID;
	private $paycomet = "";

	public const ID_EMPRESA = 1319;
	public const LOG_FILE = "paycomet.txt";

	function __construct() {
		parent::__construct("facturas_paycomet","id","id DESC");
		$this->paycomet = new Paycomet_Bankstore($this->merchantCode, $this->terminal, $this->password, $this->jetid);		
		$this->load->model("Log_Model");
	}

	function addUser($config = array()) {

		$user_id = $config["user_id"];
		$token = $config["token"];

		// Creamos un nuevo usuario a partir del token
    $response = $this->paycomet->AddUserToken($token);
    if ($response->RESULT != "OK") {
	    $this->Log_Model->imprimir(array(
	      "id_empresa" => Paycomet_Model::ID_EMPRESA,
	      "file" => Paycomet_Model::LOG_FILE,
	      "texto" => "ERROR: ".print_r($response,TRUE),
	    ));			
			return false;
    }

    $paycomet_user_token = $response->DS_TOKEN_USER;
    $paycomet_id_user = $response->DS_IDUSER;
    $this->Log_Model->imprimir(array(
      "id_empresa" => Paycomet_Model::ID_EMPRESA,
      "file" => Paycomet_Model::LOG_FILE,
      "texto" => "TOKEN CORRECTO: ".$paycomet_user_token."\n",
    ));

    // Lo guardamos en la base de datos
    $sql = "UPDATE com_usuarios SET ";
    $sql.= " paycomet_user = '$paycomet_id_user', paycomet_token = '$paycomet_user_token' ";
    $sql.= "WHERE id = '$user_id' ";
    $this->db->query($sql);
    return true;
	}

	function getUser($config = array()) {

		$user_id = isset($config["user_id"]) ? $config["user_id"] : ""; // ID DEL SISTEMA
		$paycomet_user_id = isset($config["paycomet_user_id"]) ? $config["paycomet_user_id"] : ""; // ID DE PAYCOMET

		// Obtenemos los datos del usuario
		$sql = "SELECT * FROM com_usuarios WHERE ";
		if (!empty($user_id)) $sql.= " id = '$user_id' ";
		else if (!empty($paycomet_user_id)) $sql.= " paycomet_user = '$paycomet_user_id' ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) {
	    $this->Log_Model->imprimir(array(
	      "id_empresa" => Paycomet_Model::ID_EMPRESA,
	      "file" => Paycomet_Model::LOG_FILE,
	      "texto" => "ERROR: Usuario no encontrado $user_id | $paycomet_user_id",
	    ));			
			return false;
		}
		$usuario = $q->row();
		/*
		if (empty($usuario->paycomet_user) || empty($usuario->paycomet_token)) {
	    $this->Log_Model->imprimir(array(
	      "id_empresa" => Paycomet_Model::ID_EMPRESA,
	      "file" => Paycomet_Model::LOG_FILE,
	      "texto" => "ERROR: El usuario [$user_id] no tiene token asignado",
	    ));			
			return false;
		}*/
		return $usuario;		
	}

	function createSubscription($config = array()) {

		$user_id = $config["user_id"];
		$amount = $config["amount"];
		$periodicity = $config["periodicity"];
		$token = $config["token"];
		$order = $config["order"];
		$startDate = $config["startDate"];
		$endDate = $config["endDate"];

		$usuario = $this->getUser(array(
			"user_id" => $user_id
		));
		if ($usuario === false) {
			return false;
		}

		$ss = explode(" ", $usuario->nombre);
		if (sizeof($ss)>1) {
			$apellido = array_pop($ss);
			$nombre = implode(" ", $ss);
		} else {
			$nombre = $usuario->nombre;
			$apellido = "";
		}
		$merchant_data = array(
			"customer" => array(
				"id" => $usuario->id,
				"name" => $nombre,
				"surname" => $apellido,
				"email" => $usuario->email,
				"mobilePhone" => array(
					"cc" => "34",
					"subscriber" => $usuario->celular
				),
			),
			"recurringExpiry" => $endDate,
			"recurringFrequency" => 30,
		);
		$merchant_data = urlencode(base64_encode(json_encode($merchant_data)));

    $subscriptionResponse = $this->paycomet->CreateSubscriptionToken(
      $usuario->paycomet_user,
      $usuario->paycomet_token,
      $startDate,
      $endDate,
      $order,
      $periodicity,
      $amount,
      "EUR",
      null,
      $merchant_data,
      "LWV", 
      "I", 
      null,
      1
    );
    $this->Log_Model->imprimir(array(
      "id_empresa" => Paycomet_Model::ID_EMPRESA,
      "file" => Paycomet_Model::LOG_FILE,
      "texto" => "RESULTADO SUSCRIPCION: ".print_r($subscriptionResponse,TRUE)."\n\n",
    ));

    return $subscriptionResponse;
	}

	function createPaymentIframe($config = array()) {

		$id_usuario = $config["id_usuario"];
		$amount = $config["amount"];
		$order = $config["order"];
		$urlOk = $config["urlOk"];
		$urlKo = $config["urlKo"];

		$usuario = $this->getUser(array(
			"user_id" => $id_usuario
		));
		if ($usuario === false) {
			return false;
		}

		$ss = explode(" ", $usuario->nombre);
		if (sizeof($ss)>1) {
			$apellido = array_pop($ss);
			$nombre = implode(" ", $ss);
		} else {
			$nombre = $usuario->nombre;
			$apellido = "";
		}
		$merchant_data = array(
			"customer" => array(
				"id" => $usuario->id,
				"name" => $nombre,
				"surname" => $apellido,
				"email" => $usuario->email,
				"mobilePhone" => array(
					"cc" => "34",
					"subscriber" => $usuario->celular
				),
			),
		);
		$merchant_data = urlencode(base64_encode(json_encode($merchant_data)));

    $response = $this->paycomet->ExecutePurchaseUrl(
    	$order,
      $amount,
      "EUR",
      "ES",
      false,
      true, // secure
      null,
      $urlOk,
      $urlKo,
      $merchant_data,
    );
    $this->Log_Model->imprimir(array(
      "id_empresa" => Paycomet_Model::ID_EMPRESA,
      "file" => Paycomet_Model::LOG_FILE,
      "texto" => "PURCHASE URL: ".print_r($response,TRUE)."\n\n",
    ));

    return $response;
	}



	function createSubscriptionIframe($config = array()) {

		$id_usuario = $config["id_usuario"];
		$amount = $config["amount"];
		$periodicity = $config["periodicity"];
		$order = $config["order"];
		$startDate = $config["startDate"];
		$endDate = $config["endDate"];
		$urlOk = $config["urlOk"];
		$urlKo = $config["urlKo"];

		$usuario = $this->getUser(array(
			"user_id" => $id_usuario
		));
		if ($usuario === false) {
			return false;
		}

		$ss = explode(" ", $usuario->nombre);
		if (sizeof($ss)>1) {
			$apellido = array_pop($ss);
			$nombre = implode(" ", $ss);
		} else {
			$nombre = $usuario->nombre;
			$apellido = "";
		}
		$merchant_data = array(
			"customer" => array(
				"id" => $usuario->id,
				"name" => $nombre,
				"surname" => $apellido,
				"email" => $usuario->email,
				"mobilePhone" => array(
					"cc" => "34",
					"subscriber" => $usuario->celular
				),
			),
			"recurringExpiry" => $endDate,
			"recurringFrequency" => 30,
		);
		$merchant_data = urlencode(base64_encode(json_encode($merchant_data)));

    $subscriptionResponse = $this->paycomet->CreateSubscriptionUrl(
    	$order,
      $amount,
      "EUR",
      $startDate,
      $endDate,
      $periodicity,
      "ES",
      true, // secure
      null,
      $urlOk,
      $urlKo,
      $merchant_data,
    );
    $this->Log_Model->imprimir(array(
      "id_empresa" => Paycomet_Model::ID_EMPRESA,
      "file" => Paycomet_Model::LOG_FILE,
      "texto" => "RESULTADO SUSCRIPCION: ".print_r($subscriptionResponse,TRUE)."\n\n",
    ));

    // Guardar el TOKEN del usuario

    return $subscriptionResponse;
	}


	function createRefund($config = array()) {

		$id_usuario = $config["id_usuario"];
		$amount = $config["amount"];
		$reference = $config["reference"];
		$auth_code = $config["auth_code"];

		$usuario = $this->getUser(array(
			"user_id" => $id_usuario // Buscamos por ID del sistema
		));
		if ($usuario === false) {
			return false;
		}

		$response = $this->paycomet->ExecuteRefund(
			$usuario->paycomet_user,
			$usuario->paycomet_token,
			$reference,
			"EUR",
			$auth_code,
			$amount
		);

    $this->Log_Model->imprimir(array(
      "id_empresa" => Paycomet_Model::ID_EMPRESA,
      "file" => Paycomet_Model::LOG_FILE,
      "texto" => "RESULTADO REFUND: ".print_r($response,TRUE)."\n\n",
    ));

    return $response;
	}	


	function createPayment($config = array()) {

		$user_id = $config["user_id"];
		$amount = $config["amount"];
		$reference = $config["reference"];

		$usuario = $this->getUser(array(
			"user_id" => $user_id
		));
		if ($usuario === false) {
			return false;
		}		

		$response = $this->paycomet->ExecutePurchase(
			$usuario->paycomet_user,
			$usuario->paycomet_token,
			$amount,
			$reference,
			"EUR",
		);
    $this->Log_Model->imprimir(array(
      "id_empresa" => Paycomet_Model::ID_EMPRESA,
      "file" => Paycomet_Model::LOG_FILE,
      "texto" => "RESULTADO PAYMENT: ".print_r($response,TRUE)."\n\n",
    ));

    return $response;
	}


	function endSubscription($conf = array()) {
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;

		$this->load->model("Usuario_Model");
		$user = $this->Usuario_Model->get($id_usuario,array(
			"id_empresa"=>$id_empresa
		));

		$response = $this->paycomet->RemoveSubscription($user->id_user_paycomet, $user->token_paycomet);
		return $response;		
	}

	//Crea un URL determinado para tokenizar una tarjeta
	/*
	function GenerateSubscription($conf = array()) {

		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;

		$order = isset($conf["order"]) ? $conf["order"] : "next-order";
		$idioma = isset($conf["idioma"]) ? $conf["idioma"] : "es";
		$price = isset($conf["price"]) ? $conf["price"] : 0;
		$periodicity = isset($conf["periodicity"]) ? $conf["periodicity"] : 30;
		$startDate = isset($conf["startDate"]) ? $conf["startDate"] : date("Ymd");
		$endDate = isset($conf["endDate"]) ? $conf["endDate"] : date("Ymd", strtotime("+10 years"));
		$urlok = isset($conf["urlok"]) ? $conf["urlok"] : URL_BASE."/web/muchas_gracias/";
		$urlko = isset($conf["urlko"]) ? $conf["urlko"] : URL_BASE."/web/pago_rechazado/";

		$this->load->model("Facturas_Paycomet_Model");
		$factura = new stdClass();
		$factura->id = 0;
		$factura->id_empresa = $id_empresa;
		$factura->id_usuario = $id_usuario;
		$factura->monto = $price;
		$factura->periodicidad = $periodicity;
		$factura->fecha = substr($startDate, 0, 4)."-".substr($startDate, 4, 2)."-".substr($startDate, 6, 2)." ".date("H:i:s");
		$factura->estado = 0;
		$factura->tipo = "Suscripcion";
		$factura->orden = $order;

		$this->Facturas_Paycomet_Model->save($factura);

		$response = $this->paycomet->CreateSubscriptionUrl($order, $price, "EUR", $startDate, $endDate, $periodicity, "ES", false, null, $urlok, $urlko);
		$this->Log_Model->imprimir(array(
			"id_empresa"=>$this->id_empresa,
			"texto"=>print_r($response, TRUE),
			"file"=>"generate_subscription.txt",
		));
		return $response;
	}



	function GeneratePayment($conf = array()) {

		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;

		$order = isset($conf["order"]) ? $conf["order"] : "next-order";
		$idioma = isset($conf["idioma"]) ? $conf["idioma"] : "es";
		$price = isset($conf["price"]) ? $conf["price"] : 0;
		$urlok = isset($conf["urlok"]) ? $conf["urlok"] : URL_BASE."/web/muchas_gracias/";
		$urlko = isset($conf["urlko"]) ? $conf["urlko"] : URL_BASE."/web/pago_rechazado/";

		$this->load->model("Facturas_Paycomet_Model");
		$factura = new stdClass();
		$factura->id = 0;
		$factura->id_empresa = $id_empresa;
		$factura->id_usuario = $id_usuario;
		$factura->monto = ($price / 100);
		$factura->fecha = date("Y-m-d H:i:s");
		$factura->estado = 0;
		$factura->tipo = "Verificar Perfil";
		$factura->orden = $order;

		$this->Facturas_Paycomet_Model->save($factura);

		//$transreference, $amount, $currency, $lang = "ES", $description = false, $secure3d = false, $scoring = null, $urlOk = null, $urlKo = null, $merchant_data = null, $merchant_description = null, $sca_exception = null, $trx_type = null, $scrow_targets = null
		$response = $this->paycomet->ExecutePurchaseUrl($order, $price, "EUR", "ES", "Verificacion de Perfil", false, null, $urlok, $urlko);
		$this->Log_Model->imprimir(array(
			"id_empresa"=>$this->id_empresa,
			"texto"=>print_r($response, TRUE),
			"file"=>"generate_payment.txt",
		));
		return $response;
	}
	*/

}