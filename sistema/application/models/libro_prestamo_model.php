<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Libro_Prestamo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("biblio_libros_prestamos","id","fecha_desde DESC");
	}
	
	/**
	 * Obtiene los libros a partir de diferentes parametros
	 */
	function buscar($conf = array()) {
		
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$order = isset($conf["order"]) ? $conf["order"] : "A.fecha_desde DESC";
		$id_libro = isset($conf["id_libro"]) ? $conf["id_libro"] : 0;
		$id_alumno = isset($conf["id_alumno"]) ? $conf["id_alumno"] : 0;
		
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT P.*, ";
		$sql.= "  DATE_FORMAT(P.fecha_desde,'%d/%m/%Y') AS fecha_desde, ";
		$sql.= "  DATE_FORMAT(P.fecha_hasta,'%d/%m/%Y') AS fecha_hasta, ";
		$sql.= "  DATE_FORMAT(P.fecha_devuelto,'%d/%m/%Y') AS fecha_devuelto, ";
		$sql.= "  IF(L.nombre IS NULL,'',L.nombre) AS libro, ";
		$sql.= "  IF(AT.nombre IS NULL,'',AT.nombre) AS autor, ";
		$sql.= "  IF(A.nombre IS NULL,'',CONCAT(A.apellido,' ',A.nombre)) AS alumno, ";
		$sql.= "  IF(C.email IS NULL,'',C.email) AS alumno_email, ";
		$sql.= "  IF(P.devuelto = 1,DATEDIFF(P.fecha_devuelto,P.fecha_hasta), DATEDIFF(NOW(),P.fecha_hasta)) AS dias_atraso ";
		$sql.= "FROM biblio_libros_prestamos P ";
		$sql.= "  LEFT JOIN aca_alumnos A ON (A.id_cliente = P.id_alumno) ";
    $sql.= "  LEFT JOIN clientes C ON (C.id = A.id_cliente) ";
		$sql.= "  LEFT JOIN biblio_libros L ON (L.id = P.id_libro) ";
		$sql.= "  LEFT JOIN biblio_autores AT ON (AT.id = L.id_autor) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND P.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (CONCAT(A.apellido,' ',A.nombre) LIKE '%$filter%' OR L.nombre LIKE '%$filter%') ";
		if (!empty($id_libro)) $sql.= "AND P.id_libro = $id_libro ";
		if (!empty($id_alumno)) $sql.= "AND P.id_alumno = $id_alumno ";
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
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del libro
		$id = (int)$id;
		$sql = "SELECT P.*, ";
		$sql.= "  DATE_FORMAT(P.fecha_desde,'%d/%m/%Y') AS fecha_desde, ";
		$sql.= "  DATE_FORMAT(P.fecha_hasta,'%d/%m/%Y') AS fecha_hasta, ";
		$sql.= "  DATE_FORMAT(P.fecha_devuelto,'%d/%m/%Y') AS fecha_devuelto, ";
		$sql.= "  IF(L.nombre IS NULL,'',L.nombre) AS libro, ";
		$sql.= "  IF(AT.nombre IS NULL,'',AT.nombre) AS autor, ";
		$sql.= "  IF(A.nombre IS NULL,'',CONCAT(A.apellido,' ',A.nombre)) AS alumno, ";
    $sql.= "  IF(C.email IS NULL,'',C.email) AS alumno_email, ";
		$sql.= "  IF(P.devuelto = 1,DATEDIFF(P.fecha_devuelto,P.fecha_hasta), DATEDIFF(NOW(),P.fecha_hasta)) AS dias_atraso ";
		$sql.= "FROM biblio_libros_prestamos P ";
		$sql.= "  LEFT JOIN aca_alumnos A ON (A.id_cliente = P.id_alumno) ";
    $sql.= "  LEFT JOIN clientes C ON (C.id = A.id_cliente) ";
		$sql.= "  LEFT JOIN biblio_libros L ON (L.id = P.id_libro) ";
		$sql.= "  LEFT JOIN biblio_autores AT ON (AT.id = L.id_autor) ";
		$sql.= "WHERE P.id = $id ";
		$sql.= "AND P.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$r = $q->row();
		return $r;
	}
	
}