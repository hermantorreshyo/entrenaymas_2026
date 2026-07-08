<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Paciente_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("med_pacientes","id");
  }
  
  function get($id,$conf = array()) {

    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();

    $sql = "SELECT * FROM med_pacientes WHERE id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND id_cliente = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $paciente = $q->row();

    $sql = "SELECT *, ";
    $sql.= " DATE_FORMAT(fecha_nac,'%d/%m/%Y') AS fecha_nac ";
    $sql.= "FROM clientes WHERE id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND id = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $row = $q->row();

    // Juntamos los objetos
    $row = (object)array_merge((array)$row, (array)$paciente);
    return $row;
  } 

  function buscar($conf = array()) {

    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "A.apellido ASC";
    $id_obra_social = isset($conf["id_obra_social"]) ? $conf["id_obra_social"] : 0;
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, C.*, ";
    $sql.= " DATE_FORMAT(C.fecha_nac,'%d/%m/%Y') AS fecha_nac, ";
    $sql.= " IF(OS.nombre IS NULL,'',OS.nombre) AS obra_social ";
    $sql.= "FROM med_pacientes A ";
    $sql.= "INNER JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN med_obras_sociales OS ON (A.id_obra_social = OS.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
    if (!empty($id_obra_social)) $sql.= "AND A.id_obra_social = $id_obra_social ";
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

  function insert($data) {

    $this->load->helper("fecha_helper");
    $data->id_empresa = parent::get_empresa();
    $data->fecha_nac = (!empty($data->fecha_nac)) ? fecha_mysql($data->fecha_nac) : "";
    $data->fecha_ingreso = (!empty($data->fecha_ingreso)) ? fecha_mysql($data->fecha_ingreso) : "";
    $data->fecha_egreso = (!empty($data->fecha_egreso)) ? fecha_mysql($data->fecha_egreso) : "";

    $cliente = array(
      "nombre"=>$data->apellido." ".$data->nombre_solo,
      "id_empresa"=>$data->id_empresa,
      "email"=>$data->email,
      "telefono"=>$data->telefono,
      "celular"=>$data->celular,
      "cuit"=>$data->cuit,
      "id_localidad"=>$data->id_localidad,
      "localidad"=>$data->localidad,
      "direccion"=>$data->direccion,
      "fecha_nac"=>$data->fecha_nac,
      "activo"=>$data->activo,
    );
    if (isset($data->path)) $cliente["path"] = $data->path;
    if (isset($data->password)) $cliente["password"] = $data->password;
    $this->db->insert("clientes",$cliente);
    $id_cliente = $this->db->insert_id();

    $paciente = array(
      "id_cliente"=>$id_cliente,
      "id_empresa"=>$data->id_empresa,
      "id_obra_social"=>$data->id_obra_social,
      "nombre_solo"=>$data->nombre_solo,
      "apellido"=>$data->apellido,
      "numero_obra_social"=>$data->numero_obra_social,
      "observaciones"=>$data->observaciones,
      "fecha_ingreso"=>$data->fecha_ingreso,
      "fecha_egreso"=>$data->fecha_egreso,
      "sexo"=>$data->sexo,
    );
    $this->db->insert("med_pacientes",$paciente);

    // Creamos el usuario en el sistema para ese alumno
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Paciente");
    if ($perfil !== FALSE) {
      $usuario = array(
        "id_referencia"=>$id_cliente,
        "id_perfiles"=>$perfil->id,
        "email"=>$data->email,
        "nombre"=>$cliente["nombre"],
        "fecha_alta"=>date("Y-m-d H:i:s"),
        "password"=>$data->password,
        "id_empresa"=>$data->id_empresa,
        "activo"=>1,
        "path"=>$data->path,
      );
      $this->db->insert("com_usuarios",$usuario);
    }

    if (!isset($id_cliente)) return -1;
    else return $id_cliente;
  }

  function update($id,$data) {

    $this->load->helper("fecha_helper");
    $data->fecha_nac = (!empty($data->fecha_nac)) ? fecha_mysql($data->fecha_nac) : "";
    $data->fecha_ingreso = (!empty($data->fecha_ingreso)) ? fecha_mysql($data->fecha_ingreso) : "";
    $data->fecha_egreso = (!empty($data->fecha_egreso)) ? fecha_mysql($data->fecha_egreso) : "";

    $cliente = array(
      "nombre"=>$data->apellido." ".$data->nombre_solo,
      "email"=>$data->email,
      "password"=>$data->password,
      "telefono"=>$data->telefono,
      "celular"=>$data->celular,
      "cuit"=>$data->cuit,
      "path"=>$data->path,
      "id_localidad"=>$data->id_localidad,
      "localidad"=>$data->localidad,
      "direccion"=>$data->direccion,
      "fecha_nac"=>$data->fecha_nac,
      "activo"=>$data->activo,
    );
    $this->db->where(array(
      "id"=>$id,
      "id_empresa"=>$data->id_empresa
    ));
    $this->db->update("clientes",$cliente);

    $paciente = array(
      "apellido"=>$data->apellido,
      "nombre_solo"=>$data->nombre_solo,
      "id_obra_social"=>$data->id_obra_social,
      "numero_obra_social"=>$data->numero_obra_social,
      "observaciones"=>$data->observaciones,
      "fecha_ingreso"=>$data->fecha_ingreso,
      "fecha_egreso"=>$data->fecha_egreso,
      "sexo"=>$data->sexo,
    );
    $this->db->where(array(
      "id_cliente"=>$id,
      "id_empresa"=>$data->id_empresa
    ));
    $this->db->update("med_pacientes",$paciente);

    // Buscamos el perfil del Paciente, y actualizamos el usuario si es necesario
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Paciente");
    if ($perfil !== FALSE) {
      $usuario = array(
        "email"=>$data->email,
        "nombre"=>$cliente["nombre"],
        "path"=>$data->path,
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
    $this->db->query("DELETE FROM med_pacientes WHERE id_empresa = $id_empresa AND id_cliente = $id ");
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Paciente");
    if ($perfil !== FALSE) {
      $this->db->query("DELETE FROM com_usuarios WHERE id_empresa = $id_empresa AND id_perfiles = $perfil->id AND id_referencia = $id ");
    }
  }

}