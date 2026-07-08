<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Laboral_Gym extends REST_Controller {

  private $id_empresa = 341;
  private $fcm_key = "AAAAqi3PCmw:APA91bEHEFijYczQaRe-irCCwdM9_iMnA_ODrwZ8B6srfjLC5xwN-JJnQzncpgeAToAYDVTraN6QDFGvVjCIYAYbj4W5a9Qob2KOXWN9C2YZXm8ZdtiJlcO58tCixBkBvmEl4GC3iA-20nFQGtbzhXuhKDZyWaEgMw";

  function __construct() {
    parent::__construct();
  }

  function guardar_configuracion() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header('Access-Control-Allow-Origin: *');
    if ($this->input->post("id") === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El parametro ID es obligatorio",
      ));
      exit();
    }
    $id = parent::get_post("id",0);
    $hora = parent::get_post("hora","");
    $dias = parent::get_post("dias","");
    $token = parent::get_post("token","");

    if (!empty($hora)) file_put_contents("log_laboral_gym.txt", date("Y-m-d H:i:s")." ID_CLIENTE: $id / HORA: $hora\n", FILE_APPEND);
    file_put_contents("log_laboral_gym.txt", date("Y-m-d H:i:s")." ID_CLIENTE: $id / TOKEN: $token\n", FILE_APPEND);

    $sql = "UPDATE clientes SET ";
    if (!empty($hora)) $sql.= "custom_1 = '$hora', ";
    if (!empty($dias)) $sql.= "custom_2 = '$dias', ";
    if (!empty($token)) $sql.= "custom_3 = '$token', ";
    $sql.= " id_empresa = '$this->id_empresa' ";
    $sql.= "WHERE id = '$id' AND id_empresa = $this->id_empresa ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function notificar_activo() {

    set_time_limit(0);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_cliente = parent::get_post("id_cliente",0);
    $activo = parent::get_post("activo",0);

    // Obtenemos los clientes
    // En custom_3 tenemos el Registration ID
    $sql = "SELECT * FROM clientes WHERE id_empresa = $this->id_empresa AND id = $id_cliente ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe el cliente",
      ));
      exit();
    }
    $cliente = $q->row();

    if (empty($cliente->custom_3)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El cliente no tiene registro de ID",
      ));
      exit();
    }

    if ($activo == 1) {
      $titulo = "Laboral Gym ha aceptado tu solicitud de registro.";
      $q = $this->db->query("UPDATE clientes SET activo = 1 WHERE id_empresa = $this->id_empresa AND id = $id_cliente");
    } else if ($activo == 0) {
      $titulo = "Laboral Gym ha denegado tu solicitud de registro.";
      $q = $this->db->query("UPDATE clientes SET activo = 0 WHERE id_empresa = $this->id_empresa AND id = $id_cliente");
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El parametro activo es incorrecto",
      ));
      exit();
    }

    $fields = array(
      'to' => $cliente->custom_3,
      'notification' => array(
        //'body'  => (!empty($entrada->descripcion)) ? $entrada->descripcion : substr(strip_tags($entrada->texto), 0, 100),
        'title' => $titulo,
        'sound' => 'mySound',
        "click_action" => "FCM_PLUGIN_ACTIVITY",
      ),
      'data'=> array(
        "reset"=>1, // Indica que se fuerza el cierre de sesion
      ),
    );
    $headers = array(
      'Authorization: key='.$this->fcm_key,
      'Content-Type: application/json'
    );
    // Send Reponse To FireBase Server  
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    // #Echo Result Of FireBase Server
    echo json_encode(array(
      "mensaje"=>$result
    ));
  }


  function notificar() {

    set_time_limit(0);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = $this->id_empresa;
    $id_entrada = parent::get_post("id_entrada",0);
    file_put_contents("log_laboral_gym.txt", "ID_ENTRADA: ".$id_entrada."\n", FILE_APPEND);
    $controlar_horarios = parent::get_post("controlar_horarios",0);

    // Obtenemos la noticia que corresponderia enviarse
    $sql = "SELECT * FROM not_entradas WHERE id_empresa = $this->id_empresa AND id = $id_entrada LIMIT 0,1";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Nada para enviar.",
      ));
      exit();
    }
    $entrada = $q->row();

    // Esto es para redirigir a la seccion correspondiente
    $seccion = "";
    $this->load->model("Categoria_Entrada_Model");
    $id_root = $this->Categoria_Entrada_Model->get_id_root($entrada->id_categoria,array(
      "id_empresa"=>$id_empresa,
    ));
    if ($id_root == 378) {
      $seccion = "generalidades";
    } else if ($id_root == 384) {
      $seccion = "especiales";
    } else if ($id_root == 387) {
      $seccion = "lesiones";
    } else if ($id_root == 410) {
      $seccion = "laboral";
    }

    // Obtenemos los clientes
    // En custom_3 tenemos el Registration ID
    $sql = "SELECT * FROM clientes WHERE id_empresa = $this->id_empresa AND custom_3 != '' ";
    if ($controlar_horarios == 1) {
      // Debemos controlar los dias y horarios de los clientes, para saber si lo tiene que mandar o no
      $numero_dia = date("N");
      if ($numero_dia == 1) $sql.= "AND custom_2 LIKE '%L%' ";
      else if ($numero_dia == 2) $sql.= "AND custom_2 LIKE '%M%' ";
      else if ($numero_dia == 3) $sql.= "AND custom_2 LIKE '%I%' ";
      else if ($numero_dia == 4) $sql.= "AND custom_2 LIKE '%J%' ";
      else if ($numero_dia == 5) $sql.= "AND custom_2 LIKE '%V%' ";
      else if ($numero_dia == 6) $sql.= "AND custom_2 LIKE '%S%' ";
      else if ($numero_dia == 7) $sql.= "AND custom_2 LIKE '%D%' ";
      $hora = date("G");
      $sql.= "AND custom_1 = '$hora' ";
    }
    $q = $this->db->query($sql);

    $registrationIds = array();
    foreach($q->result() as $row) {
      $registrationIds[] = $row->custom_3;
    }

    $fields = array(
      'registration_ids' => $registrationIds,
      'notification' => array(
        'body'  => (!empty($entrada->descripcion)) ? $entrada->descripcion : substr(strip_tags($entrada->texto), 0, 100),
        'title' => $entrada->titulo,
        //'icon'  => 'myicon',
        'sound' => 'mySound',
        "click_action" => "FCM_PLUGIN_ACTIVITY",
      ),
      'data'=> array(
        'link'=>"detalle.html?id=".$entrada->id."&seccion=".$seccion,
        'id'=>$entrada->id,
        'seccion'=>$seccion,
      ),
    );
    $headers = array(
      'Authorization: key='.$this->fcm_key,
      'Content-Type: application/json'
    );
    // Send Reponse To FireBase Server  
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    // #Echo Result Of FireBase Server
    echo json_encode(array(
      "mensaje"=>$result
    ));
  }

  function guardar_preferencias() {
    $this->input->post("id_usuario");
    $this->input->post("nombre");
    $this->input->post("es_fumador");
    $this->input->post("tipo_trabajo");
    echo json_encode(array(
      "resultado"=>0,
    ));
  }

  function get_video_dia() {
    $id_cliente = $this->input->post("id_cliente");
    $this->load->model("Entrada_Model");
    $listado = $this->Entrada_Model->buscar(array(
      "id_empresa"=>$this->id_empresa,
      "id_categoria"=>410, // Categoria: GIMNASIA LABORAL
    ));
    if (sizeof($listado["results"])>0) {
      $entrada = $listado["results"][0];
      echo json_encode($entrada);
    } else {
      echo json_encode(array());
    }
  }

  function marcar_visto() {
    $this->input->post("id_usuario");
    $this->input->post("id_entrada");
  }

  function get_publicaciones() {
    /*
    $this->input->post("id_usuario");
    $this->input->post("id_categoria");
    $this->input->post("tipo"); // V o T
    */
    $this->load->model("Entrada_Model");
    $entradas = $this->Entrada_Model->buscar(array(
      "id_empresa"=>$this->id_empresa,
    ));
    $salida = array();
    foreach($entradas["results"] as $r) {
      $row = new stdClass();
      $row->id = $r->id;
      $row->titulo = $r->titulo;
      $row->video = $r->video;
      $row->breve = substr(strip_tags($r->texto), 0, 100);
      $row->imagen = "https://www.varcreative.com/uploads/".$r->path;
      $salida[] = $row;
    }
    echo json_encode($salida);
  }

  function get_publicacion() {
    $this->input->post("id_usuario");
    $this->input->post("id_entrada");
    echo json_encode(array(
      "id_entrada"=>0,
      "texto"=>0,
    ));
  }

}