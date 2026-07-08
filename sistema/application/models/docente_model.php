<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Docente_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("aca_docentes","id","nombre ASC");
  }
  
  function get($id) {

    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM clientes WHERE id = $id AND id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $row = $q->row();

    $sql = "SELECT * FROM aca_docentes WHERE id_cliente = $id AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $docente = $q->row();

    // Juntamos los objetos
    $row = (object)array_merge((array)$row, (array)$docente);
    return $row;
  } 


  function insert($data) {

    $this->load->helper("fecha_helper");
    $data->fecha_nac = (!empty($data->fecha_nac)) ? fecha_mysql($data->fecha_nac) : "";
    $data->fecha_ingreso = (!empty($data->fecha_ingreso)) ? fecha_mysql($data->fecha_ingreso) : "";
    $data->fecha_egreso = (!empty($data->fecha_egreso)) ? fecha_mysql($data->fecha_egreso) : "";

    $cliente = array(
      "nombre"=>$data->apellido." ".$data->nombre,
      "id_empresa"=>$data->id_empresa,
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
    $this->db->insert("clientes",$cliente);
    $id_cliente = $this->db->insert_id();

    $docente = array(
      "id_cliente"=>$id_cliente,
      "id_empresa"=>$data->id_empresa,
      "id_departamento"=>$data->id_departamento,
      "nombre"=>$data->nombre,
      "apellido"=>$data->apellido,
      "titulo"=>$data->titulo,
      "banco"=>$data->banco,
      "cuenta_bancaria"=>$data->cuenta_bancaria,
      "fecha_ingreso"=>$data->fecha_ingreso,
      "fecha_egreso"=>$data->fecha_egreso,
      "obra_social"=>$data->obra_social,
      "numero_obra_social"=>$data->numero_obra_social,
      "inicio_docencia"=>$data->inicio_docencia,
    );
    $this->db->insert("aca_docentes",$docente);

    // Creamos el usuario en el sistema para ese docente
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Docente");
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
      "nombre"=>$data->apellido." ".$data->nombre,
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

    $docente = array(
      "id_departamento"=>$data->id_departamento,
      "nombre"=>$data->nombre,
      "apellido"=>$data->apellido,
      "titulo"=>$data->titulo,
      "banco"=>$data->banco,
      "cuenta_bancaria"=>$data->cuenta_bancaria,
      "fecha_ingreso"=>$data->fecha_ingreso,
      "fecha_egreso"=>$data->fecha_egreso,
      "obra_social"=>$data->obra_social,
      "numero_obra_social"=>$data->numero_obra_social,
      "inicio_docencia"=>$data->inicio_docencia,
    );
    $this->db->where(array(
      "id_cliente"=>$id,
      "id_empresa"=>$data->id_empresa
    ));
    $this->db->update("aca_docentes",$docente);

    // Buscamos el perfil del Docente, y actualizamos el usuario si es necesario
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Docente");
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
    $this->db->query("DELETE FROM aca_docentes WHERE id_empresa = $id_empresa AND id_cliente = $id ");
    $this->load->model("Perfil_Model");
    $perfil = $this->Perfil_Model->get_by_nombre("Docente");
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
    $id_departamento = isset($conf["id_departamento"]) ? $conf["id_departamento"] : 0;
    $id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT C.nombre, C.id, C.email, C.celular, C.activo, C.path ";
    $sql.= "FROM aca_docentes A ";
    $sql.= "INNER JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN aca_departamentos D ON (A.id_departamento = D.id AND A.id_empresa = D.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
    if (!empty($id_departamento)) $sql.= "AND A.id_departamento = $id_departamento ";
    if ($id_comision != 0) {
      $sql.= "AND EXISTS (SELECT * FROM aca_clases AC WHERE AC.id_empresa = A.id_empresa AND AC.id_comision = $id_comision AND AC.id_docente = A.id_cliente) ";
    }
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