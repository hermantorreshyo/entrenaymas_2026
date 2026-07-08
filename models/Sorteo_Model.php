<?php
class Sorteo_Model {

  private $id_empresa = 0;
  private $conx = null;

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  function get_numero($id_sorteo,$maximo=0) {
    $rand = 0;
    if ($maximo > 0) {
      while(TRUE) {
        $rand = rand(0,$maximo);
        $sql = "SELECT * FROM custom_tdf_sorteos_clientes ";
        $sql.= "WHERE id_empresa = $this->id_empresa ";
        $sql.= "AND id_sorteo = $id_sorteo ";
        $sql.= "AND numero = $rand ";
        $q = mysqli_query($this->conx,$sql);
        if (mysqli_num_rows($q)<=0) break;
      }
    }
    return $rand;
  }

  function get_activo($conf = array()) {
    $sql = "SELECT * FROM custom_tdf_sorteos ";
    $sql.= "WHERE activo = 1 AND id_empresa = $this->id_empresa ";
    $sql.= "AND fecha_desde < NOW() AND NOW() < fecha_hasta ";
    $sql.= "ORDER BY fecha_creacion ASC ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)<=0) return FALSE;
    $sorteo = mysqli_fetch_object($q); 
    return $sorteo;
  }

  function get_ultimo($conf = array()) {
    $sql = "SELECT * FROM custom_tdf_sorteos S ";
    $sql.= " INNER JOIN custom_tdf_sorteos_clientes SC ON (S.id_ganador = SC.id_cliente AND SC.id_empresa = S.id_empresa AND SC.id_sorteo = S.id) ";
    $sql.= "WHERE S.activo = 0 AND S.id_empresa = $this->id_empresa ";
    $sql.= "AND S.id_ganador != 0 ";
    $sql.= "ORDER BY S.fecha_creacion DESC ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)<=0) return FALSE;
    $sorteo = mysqli_fetch_object($q); 
    return $sorteo;
  }
}