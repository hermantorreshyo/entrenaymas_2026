<?php
class Turno_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $total = 0;

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : utf8_encode($r));
  }

  function get_servicio($id,$config = array()) {

    $sql = "SELECT A.*, ";
    $sql.= " IF(ES.nombre IS NULL,'',ES.nombre) AS especialidad ";
    $sql.= "FROM turnos_servicios A ";
    $sql.= "LEFT JOIN med_especialidades ES ON (A.id_especialidad = ES.id AND A.id_empresa = ES.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $this->id_empresa AND A.activo = 1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)<=0) return FALSE;
    $r = mysqli_fetch_object($q);
    $r = $this->encoding_servicio($r);

    $r->path = (!empty($r->path)) ? (((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path) : "";
    $r->link = str_replace("servicio/", "profesional/", $r->link);

    // Obtenemos los dias de ese turno
    $sql = "SELECT DISTINCT dia ";
    $sql.= "FROM turnos_servicios_horarios H ";
    $sql.= "WHERE H.id_empresa = $r->id_empresa AND H.id_servicio = $r->id ";
    $qq = mysqli_query($this->conx,$sql);
    $r->dias = array();
    while(($rr=mysqli_fetch_object($qq))!==NULL) {
      $r->dias[] = $rr->dia;
    }
    return $r;
  }

  function get_servicios($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 9999;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
		$destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? $config["filter"] : 0;
		$order_by = isset($config["order_by"]) ? $config["order_by"] : "A.nombre ASC";

		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(ES.nombre IS NULL,'',ES.nombre) AS especialidad ";
    $sql.= "FROM turnos_servicios A ";
    $sql.= "LEFT JOIN med_especialidades ES ON (A.id_especialidad = ES.id AND A.id_empresa = ES.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if (!empty($filter)) $sql.= "AND (A.nombre LIKE '%$filter%' OR A.texto LIKE '%$filter%') ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
    $sql.= "ORDER BY $order_by ";
    $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r = $this->encoding_servicio($r);

      $r->path = (!empty($r->path)) ? (((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path) : "";
      $r->link = str_replace("servicio/", "profesional/", $r->link);

      // Obtenemos los dias de ese turno
      $sql = "SELECT DISTINCT dia ";
      $sql.= "FROM turnos_servicios_horarios H ";
      $sql.= "WHERE H.id_empresa = $r->id_empresa AND H.id_servicio = $r->id ";
      $qq = mysqli_query($this->conx,$sql);
      $r->dias = array();
      while(($rr=mysqli_fetch_object($qq))!==NULL) {
        $r->dias[] = $rr->dia;
      }

      $salida[] = $r;
    }
    return $salida;
  }

  function get_total_results() {
    return $this->total;
  }

  private function encoding_servicio($e) {
    $e->texto = $this->encod($e->texto);
    $e->texto_en = $this->encod($e->texto_en);
    $e->nombre = $this->encod($e->nombre);
    return $e;
  }

}
?>