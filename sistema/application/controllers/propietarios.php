<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Propietarios extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Propietario_Model', 'modelo');
  }

  function pasar_clientes() {

    // Pasamos los clientes normales a contactos
    $sql = "SELECT * FROM empresas WHERE id_proyecto = 3";
    $q = $this->db->query($sql);
    foreach($q->result() as $empresa) {
      // Son contactos
      $this->db->query("UPDATE clientes C SET C.custom_3 = '1' WHERE C.id_empresa = $empresa->id AND NOT EXISTS (SELECT 1 FROM facturas F WHERE (F.id_empresa = C.id_empresa AND F.id_cliente = C.id)) ");
      // Son inquilinos
      $this->db->query("UPDATE clientes C SET C.custom_4 = '1' WHERE C.id_empresa = $empresa->id AND EXISTS (SELECT 1 FROM facturas F WHERE (F.id_empresa = C.id_empresa AND F.id_cliente = C.id)) ");
    }

    $sql = "SELECT * FROM inm_propietarios ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {

      $sql = "INSERT INTO clientes (";
      $sql.= " id_empresa, nombre, email, password, telefono, celular, direccion, observaciones, id_localidad, cuit, codigo, ";
      $sql.= " custom_5, id_tipo_iva, forma_pago, id_tipo_documento, activo ";
      $sql.= ") VALUES (";
      $sql.= " '$r->id_empresa', '$r->nombre', '$r->email', '$r->password', '$r->telefono', '$r->celular', '$r->direccion', '$r->nota_interna', '$r->id_localidad', '$r->cuit', '$r->codigo', ";
      $sql.= " '1',4,'C',96,1 ";
      $sql.= ")";
      $qq = $this->db->query($sql);
      $id_cliente = $this->db->insert_id();

      $this->db->query("UPDATE inm_propiedades SET id_propietario = $id_cliente WHERE id_propietario = $r->id AND id_empresa = $r->id_empresa ");
    }
    echo "TERMINO";
  }
  
  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * FROM clientes L ";
    $sql.= "WHERE L.nombre LIKE '%$nombre%' ";
    $sql.= "AND L.id_empresa = $id_empresa ";
    $sql.= "AND L.custom_5 = '1' "; // Indica que es un propietario
    $sql.= "ORDER BY L.nombre ASC ";
    $sql.= "LIMIT 0,20 ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->nombre;
      $rr->label = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }  
  
  
}