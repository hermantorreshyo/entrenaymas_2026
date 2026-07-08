<?php
class Delivery_Model {

	private $id_empresa = 0;
	private $conx = null;
	private $total = 0;
  public $sql = "";

	function __construct($conx) {
		$this->conx = $conx;
	}

	function get_empresas($conf = array()) {

		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 0;
		$id_localidad = isset($conf["id_localidad"]) ? $conf["id_localidad"] : 0;
		$id_categoria = isset($conf["id_categoria"]) ? $conf["id_categoria"] : 0;
		$sql = "SELECT SQL_CALC_FOUND_ROWS E.*, DC.*, ";
		$sql.= " L.nombre AS ciudad, L.link AS ciudad_link ";
		$sql.= "FROM empresas E ";
		$sql.= "INNER JOIN delivery_configuracion DC ON (E.id = DC.id_empresa) ";
		$sql.= "INNER JOIN com_localidades L ON (E.id_localidad = L.id) ";
		$sql.= "WHERE E.id_proyecto = 10 "; // Pertenece a RESTOVAR
		$sql.= "AND E.activo = 1 ";
		if (!empty($id_categoria)) $sql.= "AND EXISTS(SELECT * FROM delivery_categorias_empresas EC WHERE id_categoria = $categoria->id AND id_empresa = E.id) ";
		if (!empty($id_localidad)) $sql.= "AND E.id_localidad = $id_localidad ";
		if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
		$salida = array();
		$q = mysqli_query($this->conx,$sql);

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

		while(($r=mysqli_fetch_object($q))!==NULL) {

			// Obtenemos la categoria de la empresa
			$r->categorias = array();
			if (!empty($r->path)) $r->path = "/sistema/".$r->path;
      $qq = mysqli_query($this->conx,"SELECT EC.*, C.nombre, C.path FROM delivery_categorias C INNER JOIN delivery_categorias_empresas EC ON (C.id = EC.id_categoria) WHERE EC.id_empresa = $r->id ORDER BY orden ASC");
      while(($c=mysqli_fetch_object($qq))!==NULL) { $r->categorias[] = $c; }

			$salida[] = $r;
		}
		return $salida;
	}

  function get_total_results() {
    return $this->total;
  }

	function get_empresa($id,$conf = array()) {
		$sql = "SELECT E.*, DC.*, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS ciudad, ";
    $sql.= " IF(L.link IS NULL,'',L.link) AS ciudad_link ";
		$sql.= "FROM empresas E ";
		$sql.= "INNER JOIN delivery_configuracion DC ON (E.id = DC.id_empresa) ";
		$sql.= "LEFT JOIN com_localidades L ON (E.id_localidad = L.id) ";
		$sql.= "WHERE E.id_proyecto = 10 "; // Pertenece a RESTOVAR
		$sql.= "AND E.id = $id ";
		$sql.= "AND E.activo = 1 ";
    $this->sql = $sql;
		$q = mysqli_query($this->conx,$sql);
		if (mysqli_num_rows($q)==0) return FALSE;
		$row = mysqli_fetch_object($q);
		if (!empty($row->path)) $row->path = "/sistema/".$row->path;
		// OBTENEMOS LOS PRODUCTOS DE ESA EMPRESA
		return $row;

		/*
		// Primero llenamos el array con el orden de las categorias, para que despues se muestre bien en orden
		$productos = array();
		$categorias = array();
		$sql = "SELECT DISTINCT C.nombre FROM rubros C ";
		$sql.= "INNER JOIN articulos EC ON (C.id = EC.id_rubro) ";
		$sql.= "WHERE EC.id_empresa = $row->id ";
		$sql.= "AND EC.lista_precios = 1 "; // Articulo activo
		$sql.= "ORDER BY C.orden ASC ";
		$q = mysqli_query($this->conx,$sql);
		while(($c=mysqli_fetch_object($q))!==NULL) {
		  $productos[$c->nombre] = array();
		  $categorias[] = $c;
		}

		// Agrupamos los productos por categorias
		$sql = "SELECT P.*, IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
		$sql.= "FROM articulos P LEFT JOIN rubros C ON (P.id_rubro = C.id) ";
		$sql.= "WHERE P.id_empresa = $row->id ";
		$sql.= "AND P.lista_precios = 1 "; // Articulo activo
		$q_productos = mysqli_query($this->conx,$sql);
		while(($p=mysqli_fetch_object($q_productos))!==NULL) {
      //  Ingredientes de los productos
      $sql = "SELECT * FROM articulos_ingredientes ";
      $sql.= "WHERE id_empresa = $row->id AND id_articulo = $p->id ";
      $sql.= "ORDER BY orden ASC ";
      $qq = mysqli_query($this->conx,$sql);
      $p->ingredientes = array();
      while(($ing=mysqli_fetch_object($qq))!==NULL) {
        $p->ingredientes[] = $ing;
      }
      $categoria = utf8_encode($p->categoria);
		  $productos[$categoria][] = $p;
		}
		$row->productos = $productos;
		$row->categorias = $categorias;

		$row->comentarios = array();

		return $row;
		*/
	}

	function get_categorias() {
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 0;
		$sql = "SELECT * ";
		$sql.= "FROM delivery_categorias C ";
		// Solo aquellas que tengan al menos una empresa asignada
		$sql.= "WHERE EXISTS (SELECT * FROM delivery_categorias_empresas WHERE id_categoria = C.id) ";
		if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
		$salida = array();
		$q = mysqli_query($this->conx,$sql);
		while(($r=mysqli_fetch_object($q))!==NULL) {
			$salida[] = $r;
		}
		return $salida;		
	}

	function get_ciudades($conf = array()) {
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 0;
		$sql = "SELECT DISTINCT C.id, C.nombre, C.link ";
		$sql.= "FROM empresas E INNER JOIN com_localidades C ON (E.id_localidad = C.id) ";
		$sql.= "WHERE E.id_proyecto = 10 ";
		if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
		$salida = array();
		$q = mysqli_query($this->conx,$sql);
		while(($r=mysqli_fetch_object($q))!==NULL) {
			$salida[] = $r;
		}
		return $salida;
	}
}
?>