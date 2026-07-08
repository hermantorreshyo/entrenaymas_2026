<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Cursos_Evaluaciones_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("cursos_evaluaciones","id","id ASC");
  }
  
  function buscar($config = array()){
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_curso = isset($config["id_curso"]) ? $config["id_curso"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : -1;
    $id_etiqueta = isset($config["id_etiqueta"]) ? $config["id_etiqueta"] : 0;
    $id_empresa = parent::get_empresa();
    $sql= "SELECT SQL_CALC_FOUND_ROWS CE.id, U.id AS id_usuario, U.nombre, C.nombre as curso, ";
    $sql.= " DATE_FORMAT(CE.fecha,'%d/%m/%Y %H:%i') AS fecha, CE.estado, CLA.nombre AS clase ";
    $sql.= "FROM cursos_evaluaciones CE ";
    $sql.= "INNER JOIN clientes U ON (CE.id_usuario = U.id AND CE.id_empresa = U.id_empresa) ";
    $sql.= "INNER JOIN cursos C ON (CE.id_curso = C.id AND C.id_empresa = CE.id_empresa) ";
    $sql.= "INNER JOIN cursos_clases CLA ON (CLA.id_empresa = CE.id_empresa AND CLA.id_curso = C.id AND CE.id_clase = CLA.id) ";
    $sql.= "WHERE CE.id_empresa = $id_empresa ";
    if (!empty($id_curso)) $sql.= "AND C.id = $id_curso ";
    if (!empty($id_usuario)) $sql.= "AND U.id = $id_usuario ";
    if ($estado != -1) $sql.= "AND CE.estado = $estado ";
    if (!empty($id_etiqueta)) $sql.= "AND EXISTS (SELECT 1 FROM clientes_etiquetas_relacion CER WHERE CER.id_empresa = $id_empresa AND CER.id_etiqueta = $id_etiqueta AND CER.id_cliente = U.id) ";
    $sql.= "ORDER BY U.id ASC, CE.fecha ASC ";
    $sql.="LIMIT $limit,$offset ";
    $query = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($query->result() as $r) {

      $sql = "SELECT E.* FROM clientes_etiquetas E INNER JOIN clientes_etiquetas_relacion CER ON (E.id_empresa = CER.id_empresa AND E.id = CER.id_etiqueta) ";
      $sql.= "WHERE E.id_empresa = $id_empresa ";
      $sql.= "AND CER.id_cliente = $r->id_usuario ";
      $qq = $this->db->query($sql);
      $r->etiqueta = "";
      foreach($qq->result() as $rr) {
        $r->etiqueta = $rr->nombre;
      }

      $salida[] = $r;
    }

    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

}