<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Regla_Oferta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("reglas_ofertas","id","desde DESC");
	}
    
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

  function buscar($conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "A.desde DESC";
    $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : "";
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.id, A.id_empresa, A.nombre, A.desde, A.hasta, A.activo, A.cantidad_minima, ";
    if (!empty($id_sucursal)) $sql.= " IF(ROS.descuento_fijo IS NULL,A.descuento_fijo,ROS.descuento_fijo) AS descuento_fijo ";
    else $sql.= " A.descuento_fijo ";
    $sql.= "FROM reglas_ofertas A ";
    if (!empty($id_sucursal)) $sql.= "INNER JOIN reglas_ofertas_sucursales ROS ON (ROS.id_empresa = A.id_empresa AND ROS.id_regla = A.id AND ROS.id_sucursal = $id_sucursal) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (A.nombre LIKE '%$filter%') ";
    if (!empty($fecha)) $sql.= "AND A.desde <= '$fecha' AND '$fecha' <= A.hasta ";
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($q->result() as $r) {
      if ($id_sucursal != 0) {
        $descuento_fijo = $r->descuento_fijo;
        $rr = $this->get($r->id);
        $rr->descuento_fijo = $descuento_fijo;        
      } else {
        $rr = $this->get($r->id);
      }
      $salida[] = $rr;
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT A.*, ";
    $sql.= " DATE_FORMAT(A.desde,'%d/%m/%Y %H:%i') AS desde, ";
    $sql.= " DATE_FORMAT(A.hasta,'%d/%m/%Y %H:%i') AS hasta ";
    $sql.= "FROM reglas_ofertas A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $row = $q->row();

    // Los articulos estan agrupados por orden
    $row->articulos = array();
    $sql = "SELECT DISTINCT orden ";
    $sql.= "FROM reglas_ofertas_articulos ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_regla = $id ";
    $sql.= "ORDER BY orden ASC ";
    $qq = $this->db->query($sql);
    foreach($qq->result() as $rr) {

      $sql = "SELECT RO.*, A.codigo, A.nombre, 0 AS actual ";
      $sql.= "FROM reglas_ofertas_articulos RO ";
      $sql.= "INNER JOIN articulos A ON (RO.id_empresa = A.id_empresa AND RO.id_articulo = A.id) ";
      $sql.= "WHERE RO.id_empresa = $id_empresa ";
      $sql.= "AND RO.id_regla = $id ";
      $sql.= "AND RO.orden = $rr->orden ";
      $qqq = $this->db->query($sql);
      $row->articulos[] = $qqq->result();
    }

    $sql = "SELECT * FROM reglas_ofertas_sucursales WHERE id_empresa = $id_empresa AND id_regla = $id ORDER BY id ASC ";
    $qq = $this->db->query($sql);

    // Obtenemos los totales de la oferta por cada sucursal
    $sucursales = array();
    foreach($qq->result() as $sucursal) {

      $sucursal->precio_total = 0;
      foreach($row->articulos as $articulo) {
        foreach($articulo as $art) {
          $sql = "SELECT APS.* ";
          $sql.= "FROM articulos_precios_sucursales APS ";
          $sql.= "WHERE APS.id_empresa = $id_empresa ";
          $sql.= "AND APS.id_articulo = $art->id_articulo ";
          $sql.= "AND APS.id_sucursal = $sucursal->id_sucursal ";
          $q_precio_suc = $this->db->query($sql);
          if ($q_precio_suc->num_rows()>0) {
            $precio_suc = $q_precio_suc->row();
            $sucursal->precio_total += ($precio_suc->precio_final_dto * $art->minimo);
          }
        }
      }

      $sucursales[] = $sucursal;
    }
    $row->sucursales = $sucursales;

    return $row;
  }

  function insert($data) {
    $this->load->helper("fecha_helper");
    $data->desde = fecha_mysql($data->desde);
    $data->hasta = fecha_mysql($data->hasta);
    $articulos = $data->articulos;
    $sucursales = $data->sucursales;
    unset($data->articulos);
    unset($data->sucursales);
    $id = parent::insert($data);

    foreach($articulos as $art) {
      $this->db->insert("reglas_ofertas_articulos",array(
        "id_empresa"=>$data->id_empresa,
        "id_regla"=>$id,
        "id_articulo"=>$art->id_articulo,
        "minimo"=>$art->minimo,
        "descuento"=>(isset($art->descuento) ? $art->descuento : 0),
        "orden"=>$art->orden,
      ));
    }

    foreach($sucursales as $art) {
      $this->db->insert("reglas_ofertas_sucursales",array(
        "id_empresa"=>$data->id_empresa,
        "id_regla"=>$id,
        "id_sucursal"=>$art->id_sucursal,
        "descuento_fijo"=>$art->descuento_fijo,
      ));
    }

    return $id;
  }

  function update($id,$data) {
    $this->load->helper("fecha_helper");
    $data->desde = fecha_mysql($data->desde);
    $data->hasta = fecha_mysql($data->hasta);
    $articulos = $data->articulos;
    $sucursales = $data->sucursales;
    unset($data->articulos);
    unset($data->sucursales);
    $res = parent::update($id,$data);

    $this->db->query("DELETE FROM reglas_ofertas_articulos WHERE id_empresa = $data->id_empresa AND id_regla = $id");
    foreach($articulos as $art) {
      $this->db->insert("reglas_ofertas_articulos",array(
        "id_empresa"=>$data->id_empresa,
        "id_regla"=>$id,
        "id_articulo"=>$art->id_articulo,
        "minimo"=>$art->minimo,
        "descuento"=>(isset($art->descuento) ? $art->descuento : 0),
        "orden"=>$art->orden,
      ));
    }
    $this->db->query("DELETE FROM reglas_ofertas_sucursales WHERE id_empresa = $data->id_empresa AND id_regla = $id");
    foreach($sucursales as $art) {
      $this->db->insert("reglas_ofertas_sucursales",array(
        "id_empresa"=>$data->id_empresa,
        "id_regla"=>$id,
        "id_sucursal"=>$art->id_sucursal,
        "descuento_fijo"=>$art->descuento_fijo,
      ));
    }

    return $res;
  }

  function delete($id) {
    // Controlamos que se este borrando un articulo que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $q = $this->db->query("SELECT * FROM reglas_ofertas WHERE id = $id AND id_empresa = $id_empresa ");
    if ($q->num_rows()>0) {

      // TODO: En caso de que el producto este compartido con MercadoLibre, debemos poner
      // la publicacion status = closed
      $this->db->query("DELETE FROM reglas_ofertas_articulos WHERE id_regla = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM reglas_ofertas_sucursales WHERE id_regla = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM reglas_ofertas WHERE id = $id AND id_empresa = $id_empresa");
    }
  }

}