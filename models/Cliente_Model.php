<?php
class Cliente_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $total = 0;
  private $sql = "";

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : utf8_encode($r));
  }  

  function get_list($config = array()) {
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 6;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $filter = isset($config["filter"]) ? $config["filter"] : 0;
    $lista = isset($config["lista"]) ? $config["lista"] : -1;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : -1;
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $custom_1 = isset($config["custom_1"]) ? $config["custom_1"] : "";
    $custom_2 = isset($config["custom_2"]) ? $config["custom_2"] : "";
    $custom_3 = isset($config["custom_3"]) ? $config["custom_3"] : "";
    $custom_4 = isset($config["custom_4"]) ? $config["custom_4"] : "";
    $custom_5 = isset($config["custom_5"]) ? $config["custom_5"] : "";
    $custom_where = isset($config["custom_where"]) ? $config["custom_where"] : "";
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "A.nombre ASC";
    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 0;
    $encoding = isset($config["encoding"]) ? $config["encoding"] : 1;

    $sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
    $sql.= "FROM clientes A ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if ($not_id > 0) $sql.= "AND A.id != $not_id ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($lista != -1) $sql.= "AND A.lista = $lista ";
    if ($tipo != -1) $sql.= "AND A.tipo = $tipo ";
    if (!empty($custom_1)) {
      if (strpos($custom_1, ",")>0) {
        $cs = explode(",", $custom_1);
        $sql.= "AND (";
        foreach($cs as $c) {
          $sql.= "A.custom_1 = '$c' OR ";
        }
        $sql.= "1=0 ) ";
      } else {
        $sql.= "AND A.custom_1 = '$custom_1' ";
      }
    }
    if (!empty($custom_2)) $sql.= "AND A.custom_2 = '$custom_2' ";
    if (!empty($custom_3)) $sql.= "AND A.custom_3 = '$custom_3' ";
    if (!empty($custom_4)) $sql.= "AND A.custom_4 = '$custom_4' ";
    if (!empty($custom_5)) $sql.= "AND A.custom_5 = '$custom_5' ";
    if (!empty($filter)) {
      $sql.= "AND (A.nombre LIKE '%$filter%' ";
      if ($buscar_etiquetas == 1) {
        $filter = strtolower($filter);
        $filter = str_replace("á", "a", $filter);
        $filter = str_replace("é", "e", $filter);
        $filter = str_replace("í", "i", $filter);
        $filter = str_replace("ó", "o", $filter);
        $filter = str_replace("ú", "u", $filter);
        $sql.= "OR EXISTS (SELECT * FROM clientes_etiquetas_relacion EE INNER JOIN clientes_etiquetas ETIQ ON (EE.id_etiqueta = ETIQ.id AND EE.id_empresa = ETIQ.id_empresa) WHERE A.id = EE.id_cliente AND A.id_empresa = EE.id_empresa AND (ETIQ.nombre LIKE '%$filter%' OR MATCH(ETIQ.nombre) AGAINST ('$filter' IN BOOLEAN MODE)) ) "; 
      }
      $sql.= ") ";
    }
    $sql.= $custom_where;
    $sql.= "ORDER BY $order_by ";
    $sql.= "LIMIT $limit,$offset ";
    $this->sql = $sql;
    $q = mysqli_query($this->conx,$sql);
    $salida = array();

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (!empty($r->path)) $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
      if ($encoding == 1) $r = $this->encoding($r);
      $salida[] = $r;
    }
    return $salida;
  }

  function get_last_sql() {
    return $this->sql;
  }

  function get_total_results() {
    return $this->total;
  }

  function encoding($r) {
    $r->nombre = $this->encod($r->nombre);
    $r->direccion = $this->encod($r->direccion);
    $r->observaciones = $this->encod($r->observaciones);
    $r->custom_1 = $this->encod($r->custom_1);
    $r->custom_2 = $this->encod($r->custom_2);
    $r->custom_3 = $this->encod($r->custom_3);
    $r->custom_4 = $this->encod($r->custom_4);
    $r->custom_5 = $this->encod($r->custom_5);    
    return $r;
  }

  function get($id = 0,$config = array()) {
    $id = (($id != 0) ? $id : (isset($_SESSION["id_cliente"]) ? $_SESSION["id_cliente"] : 0));
    $encoding = isset($config["encoding"]) ? $config["encoding"] : 1;
    $tuvo_ventas = isset($config["tuvo_ventas"]) ? $config["tuvo_ventas"] : 0;
		$sql = "SELECT C.*, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql.= " IF(DEP.nombre IS NULL,'',DEP.nombre) AS departamento, ";
    $sql.= " IF(PROV.nombre IS NULL,'',PROV.nombre) AS provincia, ";
		$sql.= " IF(C.codigo_postal = '',IF(L.codigo_postal IS NULL,'',L.codigo_postal),C.codigo_postal)  AS codigo_postal, ";
		$sql.= " IF(L.latitud IS NULL,0,L.latitud) AS latitud_localidad, ";
		$sql.= " IF(L.longitud IS NULL,0,L.longitud) AS longitud_localidad ";
		$sql.= " FROM clientes C LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
    $sql.= " LEFT JOIN com_departamentos DEP ON (L.id_departamento = DEP.id) ";
    $sql.= " LEFT JOIN com_provincias PROV ON (DEP.id_provincia = PROV.id) ";
		$sql.= "WHERE C.id = $id AND C.id_empresa = $this->id_empresa";
		$q = mysqli_query($this->conx,$sql);
		if (mysqli_num_rows($q)>0) {
      $row = mysqli_fetch_object($q);
      if (!empty($row->path)) $row->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
      if ($encoding == 1) $row = $this->encoding($row);

      if ($tuvo_ventas == 1) {
        $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM facturas ";
        $sql.= "WHERE id_empresa = $this->id_empresa ";
        $sql.= "AND id_cliente = $row->id ";
        $sql.= "AND id_tipo_estado = 6 ";
        $qq = mysqli_query($this->conx,$sql);
        $rr = mysqli_fetch_object($qq);
        $row->cantidad_ventas = $rr->cantidad;
        //$row->tuvo_ventas = (mysqli_num_rows($qq)>0)?1:0;
      }

			return $row;
		} else {
			return FALSE;
		}
  }
}
?>