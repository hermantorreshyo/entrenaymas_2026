<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Medio_Pago_Configuracion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("medios_pago_configuracion","id_empresa","id_empresa ASC");
	}

  // Crea una preferencia de pago para ponerla dentro de un link
  function create_preference_mp($config) {
    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $id_factura = (isset($config["id_factura"]) ? $config["id_factura"] : 0);
    $id_punto_venta = (isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0);
    $titulo = (isset($config["titulo"]) ? $config["titulo"] : "");
    $email = (isset($config["email"]) ? $config["email"] : "");
    $monto = (isset($config["monto"]) ? $config["monto"] : 0);
    $preference = FALSE;
    if (file_exists("/home/ubuntu/data/models/mercadopago.php")) {
      require_once("/home/ubuntu/data/models/mercadopago.php");
      $q = $this->db->query("SELECT * FROM medios_pago_configuracion WHERE id_empresa = $id_empresa ");
      $medio = $q->row();
      // Si tiene configurado MercadoPago, y la factura no fue pagada
      if (!empty($medio->mp_client_id) && !empty($medio->mp_client_secret)) {
        $mp = new MP($medio->mp_client_id, $medio->mp_client_secret);  
        $items = array();
        $items[] = array(
          "id"=>$id_factura,
          "title"=>$titulo,
          "currency_id"=>"ARS",
          "quantity"=>1,
          "unit_price"=>((float)$monto) + 0,
        );
        $preference_data = array(
          "items" => $items,
          "payer" => array(
            "email" => $email,
          ),
          "notification_url" => "https://www.varcreative.com/sistema/facturas/function/ipn_mercadopago/".$id_empresa."/".$id_factura."/".$id_punto_venta,
          "local_pickup"=>false,
        );
        $preference = $mp->create_preference($preference_data);
      }
    }
    return $preference;
  }
}