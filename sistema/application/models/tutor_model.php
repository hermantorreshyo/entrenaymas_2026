<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tutor_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("aca_tutores","id_cliente","apellido ASC");
  }

  function get($id,$conf = array()) {

    $id_empresa = parent::get_empresa();

    $sql = "SELECT * FROM aca_tutores WHERE id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND id_cliente = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $tutor = $q->row();

    $sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND id = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $row = $q->row();

    // Juntamos los objetos
    $row = (object)array_merge((array)$row, (array)$tutor);
    return $row;
  } 

  function insert($data) {

    $this->load->helper("fecha_helper");
    $cliente = array(
      "nombre"=>$data->apellido." ".$data->nombre,
      "id_empresa"=>$data->id_empresa,
      "email"=>$data->email,
      "password"=>((isset($data->password)) ? $data->password : ""),
      "telefono"=>$data->telefono,
      "celular"=>$data->celular,
      "cuit"=>((isset($data->cuit)) ? $data->cuit : ""),
      "id_localidad"=>((isset($data->id_localidad)) ? $data->id_localidad : 0),
      "localidad"=>((isset($data->localidad)) ? $data->localidad : ""),
      "direccion"=>((isset($data->direccion)) ? $data->direccion : ""),
      "activo"=>((isset($data->activo)) ? $data->activo : 1),
    );
    $this->db->insert("clientes",$cliente);
    $id_cliente = $this->db->insert_id();

    $tutor = array(
      "id_cliente"=>$id_cliente,
      "id_empresa"=>$data->id_empresa,
      "nombre"=>$data->nombre,
      "apellido"=>$data->apellido,
      "telefono_2"=>((isset($data->telefono_2)) ? $data->telefono_2 : ""),
      "celular_2"=>((isset($data->celular_2)) ? $data->celular_2 : ""),
      "observaciones"=>((isset($data->observaciones)) ? $data->observaciones : ""),
    );
    $this->db->insert("aca_tutores",$tutor);

    // Creamos el usuario en el sistema para ese tutor
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Tutor");
    if ($perfil !== FALSE) {
      $usuario = array(
        "id_referencia"=>$id_cliente,
        "id_perfiles"=>$perfil->id,
        "email"=>$data->email,
        "nombre"=>$cliente["nombre"],
        "fecha_alta"=>date("Y-m-d H:i:s"),
        "password"=>((isset($data->password)) ? $data->password : ""),
        "id_empresa"=>$data->id_empresa,
        "activo"=>1,
      );
      $this->db->insert("com_usuarios",$usuario);
    }

    if (!isset($id_cliente)) return -1;
    else return $id_cliente;
  }

  function update($id,$data) {

    $this->load->helper("fecha_helper");
    $cliente = array(
      "nombre"=>$data->apellido." ".$data->nombre,
      "email"=>$data->email,
      "password"=>$data->password,
      "telefono"=>$data->telefono,
      "celular"=>$data->celular,
      "cuit"=>$data->cuit,
      "id_localidad"=>$data->id_localidad,
      "localidad"=>$data->localidad,
      "direccion"=>$data->direccion,
      "activo"=>$data->activo,
    );
    $this->db->where(array(
      "id"=>$id,
      "id_empresa"=>$data->id_empresa
    ));
    $this->db->update("clientes",$cliente);

    $tutor = array(
      "nombre"=>$data->nombre,
      "apellido"=>$data->apellido,
      "telefono_2"=>$data->telefono_2,
      "celular_2"=>$data->celular_2,
      "observaciones"=>$data->observaciones,
    );
    $this->db->where(array(
      "id_cliente"=>$id,
      "id_empresa"=>$data->id_empresa
    ));
    $this->db->update("aca_tutores",$tutor);

    // Buscamos el perfil del Tutor, y actualizamos el usuario si es necesario
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Tutor");
    if ($perfil !== FALSE) {
      $usuario = array(
        "email"=>$data->email,
        "nombre"=>$cliente["nombre"],
        "activo"=>$data->activo,
      );
      if (!empty($data->password)) $usuario["password"] = $data->password;
      $this->db->where(array(
        "id_perfiles"=>$perfil->id,
        "id_empresa"=>$data->id_empresa,
        "id_referencia"=>$id,
      ));
      $this->db->update("com_usuarios",$usuario);
    }
    return 1;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM clientes WHERE id_empresa = $id_empresa AND id = $id ");
    $this->db->query("DELETE FROM aca_tutores WHERE id_empresa = $id_empresa AND id_cliente = $id ");
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Tutor");
    if ($perfil !== FALSE) {
      $this->db->query("DELETE FROM com_usuarios WHERE id_empresa = $id_empresa AND id_perfiles = $perfil->id AND id_referencia = $id ");
    }
  }

  function buscar($conf = array()) {
    
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "A.apellido ASC";
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT C.nombre, C.id, C.email, C.celular, C.activo ";
    $sql.= "FROM aca_tutores A ";
    $sql.= "INNER JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }
  
}