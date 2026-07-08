<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Planes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Plan_Model', 'modelo');
  }
    
  function get_by_proyecto($id_proyecto) {
    $lista = $this->modelo->get_by_proyecto($id_proyecto);
    echo json_encode(array(
      "total"=> sizeof($lista),
      "results"=>$lista
    ));
  }

  function get_info_planes() {
    $id_empresa = parent::get_post("id_empresa", parent::get_empresa());

    $planes = array();
    $planes[0] = array(
      "id"=>1357,
      "nombre"=>"Plan Gratuito",
      "valor"=>"0",
      "items"=>array(
        "1 fa fa-times text-danger"=>"Mensajes ilimitados (*solo un sms al mes)",
        "2 fa fa-times text-danger"=>"Perfil recomendado a usuarios y clientes",
        "3 fa fa-times text-danger"=>"Link de RRSS y URL",
        "4 fa fa-times text-danger"=>"Opiniones y valoraciones de clientes",
        "5 fa fa-times text-danger"=>"Perfil destacado en Entrenaymas y Ads",
        "6 fa fa-times text-danger"=>"Perfil Verificado",
        "7 fa fa-check text-success"=>"Ofertas y descuentos exclusivos de nuestros partners",
        "8 fa fa-check text-success"=>"Perfil con información completa de servicios, tarifas, descripción",
      ),
    );
    $planes[1] = array(
      "id"=>1358,
      "nombre"=>"Plan Premium",
      "valor"=>"4.90",
      "valor_anual"=>"49.00",
      "items"=>array(
        "1 fa fa-check text-success"=>"Mensajes ilimitados",
        "2 fa fa-check text-success"=>"Perfil recomendado a usuarios y clientes",
        "3 fa fa-check text-success"=>"Link de RRSS y URL",
        "4 fa fa-check text-success"=>"Opiniones y valoraciones de clientes",
        "5 fa fa-check text-success"=>"Perfil destacado en Entrenaymas y Ads",
        "6 fa fa-check text-success"=>"Perfil Verificado",
        "7 fa fa-check text-success"=>"Ofertas y descuentos exclusivos de nuestros partners",
        "8 fa fa-check text-success"=>"Perfil con información completa de servicios, tarifas, descripción",
      ),
    );

    echo json_encode(array(
      "planes"=>$planes,
    ));
  }
    
}