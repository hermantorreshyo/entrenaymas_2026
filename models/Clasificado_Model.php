<?php
class Clasificado_Model {
    
    private $id_empresa = 0;
    private $conx = null;
    
    function __construct($id_empresa,$conx) {
        $this->id_empresa = $id_empresa;
        $this->conx = $conx;
    }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : utf8_encode($r));
  }    
	
    function get_categorias($id_padre,$config=array()) {
		$tiene_clasificados = isset($config["tiene_clasificados"]) ? $config["tiene_clasificados"] : 0;
        $result = array();
		$sql = "SELECT * FROM clasificados_categorias CC WHERE CC.id_empresa = $this->id_empresa ";
		$sql.= "AND CC.id_padre = $id_padre AND CC.activo = 1 ";
		if (!empty($tiene_clasificados)) $sql.= "AND EXISTS (SELECT * FROM clasificados C WHERE C.id_empresa = $this->id_empresa AND C.id_categoria = CC.id AND C.activo = 1) ";
		$sql.= "ORDER BY CC.nombre ASC ";
		$q = mysqli_query($this->conx,$sql);
		while(($row=mysqli_fetch_object($q))!==NULL) {
			$e = new stdClass();
			$e->id = $row->id;
			$e->id_padre = $id_padre;
			$e->nombre = $this->encod($row->nombre);
			$e->link = $row->link;
			$e->children = $this->get_categorias($row->id);
			$result[] = $e;
        }
        return $result;
    }
    
    // Obtenemos los datos del entrada
	function get($id,$config = array()) {
        
		// Estos parametros se pueden deshabilitar para ganar velocidad, ya que no tiene sentido a veces cargarlos
        $buscar_relacionados = isset($config["buscar_relacionados"]) ? $config["buscar_relacionados"] : 1;
        $buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;
		$buscar_atributos = isset($config["buscar_atributos"]) ? $config["buscar_atributos"] : 1;
		
		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
		$id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
        $limit = isset($config["limit"]) ? $config["limit"] : 0;
        $offset = isset($config["offset"]) ? $config["offset"] : 6;
		
		$id = (int)$id;
		$sql = "SELECT A.*, DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
		$sql.= " IF(C.link IS NULL,'',C.link) AS categoria_link, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path, ";
		$sql.= " IF(P.path IS NULL,'',P.path) AS publicidad_path, ";
		$sql.= " IF(P.link IS NULL,'',P.link) AS publicidad_link, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
		$sql.= "FROM clasificados A ";
		$sql.= "LEFT JOIN clasificados_categorias C ON (A.id_categoria = C.id) ";
		$sql.= "LEFT JOIN not_publicidades P ON (A.id_publicidad = P.id) ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";
        $sql.= "ORDER BY A.fecha DESC ";
        $sql.= "LIMIT $limit,$offset ";
		
		$q = mysqli_query($this->conx,$sql);
		if (mysqli_num_rows($q) == 0) return array();
		$entrada = mysqli_fetch_object($q);
		$entrada = $this->encoding($entrada);
		
        $entrada->relacionados = array();
        if ($buscar_relacionados == 1) {
			
			$sql = "SELECT * FROM clasificados_categorias_relacionadas WHERE id_categoria = $entrada->id_categoria ";
			$q = mysqli_query($this->conx,$sql);
            while(($r=mysqli_fetch_object($q))!==NULL) {
                // Obtenemos los datos de esa entrada relacionada, y la ponemos en el array
                $relacionados = $this->get_list(array(
					"id_categoria"=>$r->id_relacion,
					"not_id"=>$entrada->id,
                    "buscar_relacionados"=>0,
                    "buscar_imagenes"=>0,
					"buscar_atributos"=>0,
					"limit"=>0,
                ));
				$entrada->relacionados = array_merge($entrada->relacionados,$relacionados);
            }
        }
		
        $entrada->images = array();
        if ($buscar_imagenes == 1) {
            // Obtenemos las imagenes de ese entrada
            $sql = "SELECT AI.* FROM clasificados_images AI WHERE AI.id_clasificado = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
            $q = mysqli_query($this->conx,$sql);
            while(($r=mysqli_fetch_object($q))!==NULL) {
                $entrada->images[] = $r->path;
            }
        }
		
        $entrada->atributos = array();
        if ($buscar_atributos == 1) {
			// Obtenemos los atributos
			$sql = "SELECT AV.valor, A.nombre FROM clasificados_atributos_valores AV INNER JOIN clasificados_atributos A ON (AV.id_atributo = A.id) ";
			$sql.= "WHERE AV.id_clasificado = $id ";
			$sql.= "ORDER BY A.orden ASC ";
            $q = mysqli_query($this->conx,$sql);
            while(($r=mysqli_fetch_object($q))!==NULL) {
            	$valor = trim($r->valor);
				if (empty($valor)) continue;
				$r->nombre = $this->encod($r->nombre);
				$r->valor = $this->encod($r->valor);
                $entrada->atributos[] = $r;
            }
		}
		
		// Link de la imagen
		if (!empty($entrada->path)) {
			$entrada->path = ((strpos($entrada->path,"http://")===FALSE)) ? "/sistema/".$entrada->path : $entrada->path;	
		}
		
		return $entrada;
	}
    
    
    function get_list($config = array()) {
		
        $limit = isset($config["limit"]) ? $config["limit"] : 0;
        $offset = isset($config["offset"]) ? $config["offset"] : 6;
        $activo = isset($config["activo"]) ? $config["activo"] : 1;
		$destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? $config["filter"] : 0;
		$id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
		$categoria = isset($config["categoria"]) ? $config["categoria"] : "";
		$not_categoria = isset($config["not_categoria"]) ? $config["not_categoria"] : "";
		$id_idioma = isset($config["id_idioma"]) ? $config["id_idioma"] : 0;
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
		$sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
		$sql.= " IF(C.link IS NULL,'',C.link) AS categoria_link, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path, ";
		$sql.= " IF(P.path IS NULL,'',P.path) AS publicidad_path, ";
		$sql.= " IF(P.link IS NULL,'',P.link) AS publicidad_link, ";		
        $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
        $sql.= "FROM clasificados A ";
        $sql.= "LEFT JOIN clasificados_categorias C ON (A.id_categoria = C.id) ";
		$sql.= "LEFT JOIN not_publicidades P ON (A.id_publicidad = P.id) ";
        $sql.= "WHERE 1=1 ";
		//$sql.= "AND A.latitud = 0 AND A.longitud = 0 ";
        $sql.= "AND A.id_empresa = $this->id_empresa ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";		
        if ($activo != -1) $sql.= "AND A.activo = $activo ";
        if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
        if (!empty($filter)) $sql.= "AND A.titulo LIKE '%$filter%' ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
		if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
		if (!empty($id_idioma)) $sql.= "AND A.id_idioma = $id_idioma ";
		if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
		if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
		if (!empty($activo_desde)) $sql.= "AND A.activo_desde >= '$activo_desde' ";
		if (!empty($activo_hasta)) $sql.= "AND A.activo_hasta <= '$activo_hasta' ";
		if (!empty($mes)) $sql.= "AND MONTH(A.fecha) = $mes ";
		if (!empty($anio)) $sql.= "AND YEAR(A.fecha) = $anio ";
		if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
		if (!empty($not_categoria)) $sql.= "AND C.link != '$not_categoria' ";
        
        $sql.= "ORDER BY $order_by ";
        $sql.= "LIMIT $limit,$offset ";
        $q = mysqli_query($this->conx,$sql);
        $salida = array();
        while(($r=mysqli_fetch_object($q))!==NULL) {
			$r = $this->encoding($r);
			$r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
			$salida[] = $r;
		}
        return $salida;
    
    }
	
	private function encoding($e) {
		$e->texto = $this->encod($e->texto);
		$e->titulo = $this->encod($e->titulo);
		$e->subtitulo = $this->encod($e->subtitulo);
		$e->categoria = $this->encod($e->categoria);
		$e->direccion = $this->encod($e->direccion);
		$e->telefono = $this->encod($e->telefono);
		$e->email = $this->encod($e->email);
		return $e;
	}
	
}
?>