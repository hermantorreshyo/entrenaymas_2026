<?php
class Objeto_Model {

    private $id_empresa = 0;
    private $conx = null;
    private $total = 0;

    function __construct($id_empresa,$conx) {
        $this->id_empresa = $id_empresa;
        $this->conx = $conx;
    }

    // Obtenemos los datos del entrada
	function get($id,$config = array()) {

		// Estos parametros se pueden deshabilitar para ganar velocidad, ya que no tiene sentido a veces cargarlos
        $buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;

		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
        $limit = isset($config["limit"]) ? $config["limit"] : 0;
        $offset = isset($config["offset"]) ? $config["offset"] : 6;

		$id = (int)$id;
		$sql = "SELECT A.*, DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora ";
		$sql.= "FROM clasif_objetos A ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";
        $sql.= "ORDER BY A.fecha DESC ";
        $sql.= "LIMIT $limit,$offset ";

		$q = mysqli_query($this->conx,$sql);
		if (mysqli_num_rows($q) == 0) return array();
		$entrada = mysqli_fetch_object($q);
		$entrada = $this->encoding($entrada);

        $entrada->images = array();
        if ($buscar_imagenes == 1) {
            // Obtenemos las imagenes de ese entrada
            $sql = "SELECT AI.* FROM clasif_objetos_images AI WHERE AI.id_objeto = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
            $q = mysqli_query($this->conx,$sql);
            while(($r=mysqli_fetch_object($q))!==NULL) {
                $entrada->images[] = $r->path;
            }
        }

		// Obtenemos los objetos relacionados
		$entrada->relacionados = $this->get_list(array(
			"not_id"=>$entrada->id,
			"offset"=>3,
		));

		// Link de la imagen
		$entrada->path = ((strpos($entrada->path,"http://")===FALSE)) ? "/sistema/".$entrada->path : $entrada->path;

		return $entrada;
	}


    function get_list($config = array()) {

        $limit = isset($config["limit"]) ? $config["limit"] : 0;
        $offset = isset($config["offset"]) ? $config["offset"] : 6;
        $activo = isset($config["activo"]) ? $config["activo"] : 1;
		$destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? $config["filter"] : 0;
		$fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
		$fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
		$activo_desde = isset($config["activo_desde"]) ? $config["activo_desde"] : "";
		$activo_hasta = isset($config["activo_hasta"]) ? $config["activo_hasta"] : "";
		$mes = isset($config["mes"]) ? $config["mes"] : 0;
		$anio = isset($config["anio"]) ? $config["anio"] : 0;
		$id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
		$not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
		$order_by = isset($config["order_by"]) ? $config["order_by"] : "A.fecha DESC";

		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
		$sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora ";
        $sql.= "FROM clasif_objetos A ";
        $sql.= "WHERE 1=1 ";
        $sql.= "AND A.id_empresa = $this->id_empresa ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";
        if ($activo != -1) $sql.= "AND A.activo = $activo ";
        if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
		if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
		if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
		if (!empty($activo_desde)) $sql.= "AND A.fecha >= '$activo_desde' ";
		if (!empty($activo_hasta)) $sql.= "AND A.fecha <= '$activo_hasta' ";
		if (!empty($mes)) $sql.= "AND MONTH(A.fecha) = $mes ";
		if (!empty($anio)) $sql.= "AND YEAR(A.fecha) = $anio ";

        $sql.= "ORDER BY $order_by ";
        $sql.= "LIMIT $limit,$offset ";
        $q = mysqli_query($this->conx,$sql);
        $salida = array();

        $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
        $t = mysqli_fetch_object($q_total);
        $this->total = $t->total;

        while(($r=mysqli_fetch_object($q))!==NULL) {

            // Obtenemos las imagenes de ese entrada
            $sql = "SELECT AI.* FROM clasif_objetos_images AI WHERE AI.id_objeto = $r->id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC LIMIT 0,1";
            $qq = mysqli_query($this->conx,$sql);
			$rr = mysqli_fetch_object($qq);

			$r = $this->encoding($r);
			$r->path = ((strpos($rr->path,"http://")===FALSE)) ? "/sistema/".$rr->path : $rr->path;
			$salida[] = $r;
		}
        return $salida;

    }

    function get_total_results() {
        return $this->total;
    }

	private function encoding($e) {
		$e->texto = mb_convert_encoding($e->texto, 'UTF-8', 'ISO-8859-1');
		$e->titulo = mb_convert_encoding($e->nombre, 'UTF-8', 'ISO-8859-1');
		$e->subtitulo = "";
		$e->tipo = "";
		return $e;
	}

}
?>