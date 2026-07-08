<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Alertas extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Alerta_Model', 'modelo');
    }

    // Registra un alerta de la aplicacion
    function registrar() {

	    header("Access-Control-Allow-Origin: *");
	    header('Access-Control-Allow-Credentials: true');
	    header('Access-Control-Max-Age: 86400');    // cache for 1 day    	

	    $id_usuario = $this->input->post("id_usuario");
	    $latitud = $this->input->post("latitud");
	    $longitud = $this->input->post("longitud");
	    $tipo = $this->input->post("tipo");
	    $estado = $this->input->post("estado");
	    $id_empresa = $this->input->post("id_empresa");
	    $fecha = date("Y-m-d H:i:s");

	    // Controlamos que el usuario este registrado
	    $this->load->model("Web_User_Model");
	    $usuario = $this->Web_User_Model->get($id_usuario,$id_empresa);
	    if ($usuario === FALSE) {
	    	echo json_encode(array(
	    		"error"=>1,
	    		"mensaje"=>"El usuario con ID $id_usuario no se encuentra registrado.",
	    	));
	    	exit();
	    }

	    // Si no se envia latitud y longitud, tenemos que tomar la cargada por defecto del usuario
	    if ($latitud === FALSE || $latitud == 0) {
	    	$latitud = $usuario->latitud;
	    	$longitud = $usuario->longitud;
	    }

	    // Guardamos la alerta
	    $sql = "INSERT INTO app_alertas (id_empresa,fecha,id_usuario,estado,tipo,latitud,longitud) VALUES (";
	    $sql.= "$id_empresa, '$fecha', $id_usuario, '$estado', '$tipo', $latitud, $longitud )";
	    $this->db->query($sql);

    	echo json_encode(array(
    		"mensaje"=>"Su alerta ha sido enviada.",
    		"error"=>0,
    	));
    }

    // Registra un usuario de la aplicacion al panel de control
    function registrar_usuario() {

	    header("Access-Control-Allow-Origin: *");
	    header('Access-Control-Allow-Credentials: true');
	    header('Access-Control-Max-Age: 86400');    // cache for 1 day    	

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		// Obtenemos los datos enviados por el usuario
    	$usuario = new stdClass();
    	$usuario->nombre = $this->input->post("nombre");
    	$usuario->apellido = $this->input->post("apellido");
    	$usuario->password = $this->input->post("uuid");
    	$usuario->ciudad = $this->input->post("ciudad");
    	$usuario->telefono = $this->input->post("telefono");
    	$usuario->direccion = $this->input->post("direccion");
    	$usuario->id_empresa = $this->input->post("id_empresa");
    	$usuario->fecha_inicial = date("Y-m-d");		

		// Realizamos una geolocalizacion para obtener la latitud y la longitud de la direccion indicada
		require APPPATH.'libraries/php-geocode/Location.php';
		require APPPATH.'libraries/php-geocode/Geocode.php';
		$geocode = new Geocode();
		$location = $geocode->get("$usuario->direccion, $usuario->ciudad, Buenos Aires, Argentina");
		$usuario->latitud = $location->getLatitude();
		$usuario->longitud = $location->getLongitude();
		// La direccion esta mal, me esta mandando al centro
		if ($usuario->latitud == -34.6420841 && $usuario->longitud == -60.4715188) {

			// TODO: En caso de este error, podriamos enviarnos un email con los datos
			// que se estan intentando registrar para luego contactarse con ellos

			echo json_encode(array(
				"error"=>1,
				"mensaje"=>"ERROR: La direccion no es valida. Por favor verifique si esta bien escrita e intente de nuevo.",
			));
			exit();
		}

    	$this->load->model("Web_User_Model");
    	$id_usuario = $this->Web_User_Model->insert($usuario);
    	echo json_encode(array(
    		"id"=>$id_usuario,
    		"error"=>0,
    	));
    }

    function ver() {
        
        $limit = $this->input->get("limit");
		$filter = $this->input->get("filter");
        $offset = $this->input->get("offset");
        $order_by = $this->input->get("order_by");
        $order = $this->input->get("order");
        if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
        else $order = "";
        
        $conf = array(
            "filter"=>$filter,
            "order"=>$order,
            "limit"=>$limit,
            "offset"=>$offset,
        );
        
        $r = $this->modelo->buscar($conf);
        echo json_encode($r);
    }    
}