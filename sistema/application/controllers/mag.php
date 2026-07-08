<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Mag extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function stripe_pago_exitoso() {
    header('Access-Control-Allow-Origin: *');
    $id_cliente = parent::get_post("id_cliente",0);    
    $id_empresa = parent::get_post("id_empresa",0);
    $milling = parent::get_post("milling",0);
    $aquafeed = parent::get_post("aquafeed",0);

    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($id_cliente,$id_empresa,array(
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_min($id_empresa);

    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("gracias-suscripcion",$id_empresa);

    $etiquetas = array();
    $revistas = "";
    if ($milling == 100) {
      $etiquetas[] = 1;
      $revistas.= "Milling And Grain - 1 Year: &pound; 100<br/>";
    } else if ($milling == 160) {
      $etiquetas[] = 2;
      $revistas.= "Milling And Grain - 2 Years: &pound; 160<br/>";
    }
    if ($aquafeed == 100) {
      $etiquetas[] = 3;
      $revistas.= "International Aquafeed - 1 Year: &pound; 100<br/>";
    } else if ($aquafeed == 160) {
      $etiquetas[] = 4;
      $revistas.= "International Aquafeed - 2 Years:  &pound; 160<br/>";
    }

    // Actualizamos las etiquetas del cliente
    foreach($etiquetas as $et) {
      $sql = "INSERT INTO clientes_etiquetas_relacion (id_cliente,id_etiqueta,id_empresa,orden) VALUES (";
      $sql.= "'$id_cliente','$et','$id_empresa','".date("Ymd")."')";
      $this->db->query($sql);
    }

    $body = $template->texto;
    $body = str_replace("{{nombre}}", $cliente->nombre, $body);
    $body = str_replace("{{email}}", $cliente->email, $body);
    $body = str_replace("{{pais}}", $cliente->contacto_telefono, $body);
    $body = str_replace("{{ciudad}}", $cliente->cliente_localidad, $body);
    $body = str_replace("{{codigo_postal}}", $cliente->codigo_postal, $body);
    $body = str_replace("{{direccion}}", $cliente->direccion, $body);
    $body = str_replace("{{departamento}}", $cliente->contacto_celular, $body);
    $body = str_replace("{{telefono}}", $cliente->telefono, $body);
    $body = str_replace("{{observaciones}}", $cliente->observaciones, $body);
    $body = str_replace("{{revistas}}", $revistas, $body);

    require APPPATH.'libraries/Mandrill/Mandrill.php';
    mandrill_send(array(
      "to"=>$cliente->email,
      "subject"=>$template->nombre,
      "body"=>$body,
      "from_name"=>$empresa->nombre,
      "bcc"=>"basile.matias99@gmail.com,porcelp@gmail.com",
    ));
    echo json_encode(array("error"=>0,"mensaje"=>"Your payment has been successful. We have sent an email with your subscription details."));
  }

  function comenzar_stripe() {
    header('Access-Control-Allow-Origin: *');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $total = parent::get_post("total",0);
    $id_cliente = parent::get_post("id_cliente",0);
    $id_empresa = parent::get_post("id_empresa",0);

    $localidad = parent::get_post("localidad","");
    $codigo_postal = parent::get_post("codigo_postal","");
    $telefono = parent::get_post("telefono","");
    $direccion = parent::get_post("direccion","");
    $contacto_celular = parent::get_post("contacto_celular","");
    $observaciones = parent::get_post("observaciones","");
    $sql = "UPDATE clientes SET ";
    $sql.= " localidad = '$localidad', ";
    $sql.= " codigo_postal = '$codigo_postal', ";
    $sql.= " telefono = '$telefono', ";
    $sql.= " direccion = '$direccion', ";
    $sql.= " contacto_celular = '$contacto_celular', ";
    $sql.= " observaciones = '$observaciones' ";
    $sql.= "WHERE id = '$id_cliente' AND id_empresa = '$id_empresa' ";
    $this->db->query($sql);

    $total = $total * 100;
    require_once('/home/ubuntu/data/vendor/autoload.php');
    $secret_key = "sk_test_heKUWmvt4QV4lcC8wN64Fh9b";
    \Stripe\Stripe::setApiKey($secret_key);
    $intent = \Stripe\PaymentIntent::create([
      'amount' => $total,
      'currency' => 'gbp',
    ]);
    echo json_encode($intent);
  }

  function ver_clientes() {
    $tiene_videos = ($this->input->get("tiene_videos") !== FALSE) ? $this->input->get("tiene_videos") : 0;
    $id_empresa = ($this->input->get("id_empresa") !== FALSE) ? $this->input->get("id_empresa") : 256;
    $this->load->model("Cliente_Model");
    $salida = $this->Cliente_Model->buscar(array(
      "id_empresa"=>$id_empresa,
      "tipo"=>0,
      "offset"=>9999999,
    ));

    $salida2 = array(
      "results"=>array(),
      "total"=>0,
    );
    if ($tiene_videos == 1) {
      foreach($salida["results"] as $cliente) {
        $sql = "SELECT * FROM not_videos WHERE id_empresa = $cliente->id_empresa AND id_cliente = $cliente->id ";  
        $q = $this->db->query($sql);
        if ($q->num_rows()>0) {
          $salida2["results"][] = $cliente;
        }
      }
    }
    $salida2["total"] = sizeof($salida2["results"]);
    echo json_encode($salida2);
  }

  function ver_eventos() {
    $tiene_videos = ($this->input->get("tiene_videos") !== FALSE) ? $this->input->get("tiene_videos") : 0;
    $id_empresa = ($this->input->get("id_empresa") !== FALSE) ? $this->input->get("id_empresa") : 256;
    $categoria = ($this->input->get("categoria") !== FALSE) ? $this->input->get("categoria") : 0;
    $this->load->model("Not_Evento_Model");
    $salida = $this->Not_Evento_Model->buscar(array(
      "id_empresa"=>$id_empresa,
      "offset"=>9999999,
      "categoria"=>$categoria,
    ));

    $salida2 = array(
      "results"=>array(),
      "total"=>0,
    );
    if ($tiene_videos == 1) {
      foreach($salida["results"] as $cliente) {
        $sql = "SELECT * FROM not_videos WHERE id_empresa = $cliente->id_empresa AND id_evento = $cliente->id ";  
        $q = $this->db->query($sql);
        if ($q->num_rows()>0) {
          $salida2["results"][] = $cliente;
        }
      }
    }
    $salida2["total"] = sizeof($salida2["results"]);
    echo json_encode($salida2);
  }

}