<?php
class Fotografia_Model {

	private $id_empresa = 0;
	private $conx = null;
	private $total = 0;
  private $sql = "";

	function __construct($id_empresa,$conx) {
		$this->id_empresa = $id_empresa;
		$this->conx = $conx;
	}

	// Obtenemos los datos del entrada
	function get($id = 0,$config = array()) {

		// Estos parametros se pueden deshabilitar para ganar velocidad, ya que no tiene sentido a veces cargarlos
		$buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;
    $encoding = isset($config["encoding"]) ? $config["encoding"] : 1;
    $privada = isset($config["privada"]) ? $config["privada"] : 0;

    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";

		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
		$id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
		$limit = isset($config["limit"]) ? $config["limit"] : 0;
		$offset = isset($config["offset"]) ? $config["offset"] : 6;

		$id = (int)$id;
		$sql = "SELECT A.*, DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
    if (!empty($lang)) $sql.= " A.titulo_$lang AS titulo, ";
    if (!empty($lang)) $sql.= " A.texto_$lang AS texto, ";
		$sql.= " YEAR(A.fecha) AS anio, MONTH(A.fecha) AS mes, ";
		$sql.= " A.fecha AS fecha_original, ";
		$sql.= " IF(C.link IS NULL,'',C.full_link) AS categoria_link, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
		$sql.= "FROM fot_trabajos A ";
		$sql.= "LEFT JOIN not_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
		$sql.= "WHERE 1=1 ";
    if ($id != 0) $sql.= "AND A.id = $id ";
    if ($privada == 0) $sql.= "AND A.privada != 1 ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";
		$sql.= "ORDER BY A.fecha DESC ";
		$sql.= "LIMIT $limit,$offset ";
    $this->sql = $sql;

		$q = mysqli_query($this->conx,$sql);
    if ($q === FALSE) {
      error_mail($this->sql);
      return FALSE;
    }
		if (mysqli_num_rows($q) == 0) return FALSE;
		$entrada = mysqli_fetch_object($q);
		if ($encoding == 1) $entrada = $this->encoding($entrada);

		$entrada->images = array();
		if ($buscar_imagenes == 1) {
			// Obtenemos las imagenes de ese entrada
			$sql = "SELECT AI.* FROM fot_trabajos_images AI WHERE AI.id_trabajo = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
			$q = mysqli_query($this->conx,$sql);
			while(($r=mysqli_fetch_object($q))!==NULL) {
				if (!empty($r->path)) {
					$r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
				}
				$entrada->images[] = $r->path;
			}
		}

		// Link de la imagen
		if (!empty($entrada->path)) {
			$entrada->path = ((strpos($entrada->path,"http")===FALSE)) ? "/sistema/".$entrada->path : $entrada->path;
		}
		if (!empty($entrada->categoria_path)) {
			$entrada->categoria_path = ((strpos($entrada->categoria_path,"http")===FALSE)) ? "/sistema/".$entrada->categoria_path : $entrada->categoria_path;
		}

		return $entrada;
	}


	function get_list($config = array()) {

		$limit = isset($config["limit"]) ? $config["limit"] : 0;
		$offset = isset($config["offset"]) ? $config["offset"] : 6;
		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? $config["filter"] : 0;
    $privada = isset($config["privada"]) ? $config["privada"] : 0;
    $id_comision = isset($config["id_comision"]) ? $config["id_comision"] : 0;
		
		$id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
		$categoria = isset($config["categoria"]) ? $config["categoria"] : "";
		$not_categoria = isset($config["not_categoria"]) ? $config["not_categoria"] : "";
		$id_idioma = isset($config["id_idioma"]) ? $config["id_idioma"] : 0;
		$fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
		$fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
		$mes = isset($config["mes"]) ? $config["mes"] : 0;
		$anio = isset($config["anio"]) ? $config["anio"] : 0;
		$id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
		$not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
		$not_ids = isset($config["not_ids"]) ? $config["not_ids"] : "";
		$order_by = isset($config["order_by"]) ? $config["order_by"] : "A.fecha DESC";

		// IDS de las categorias que se quire agregar (o no), separados por comas
		$ids_categorias = isset($config["ids_categorias"]) ? $config["ids_categorias"] : "";
		$not_ids_categorias = isset($config["not_ids_categorias"]) ? $config["not_ids_categorias"] : "";
    if (is_array($not_ids_categorias)) $not_ids_categorias = implode(",", $not_ids_categorias);

		$buscar_categorias = isset($config["buscar_categorias"]) ? $config["buscar_categorias"] : 0;
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;

    $encoding = isset($config["encoding"]) ? $config["encoding"] : 1;

    if (isset($config["from_link_categoria"]) && !empty($config["from_link_categoria"])) {
      $cat = $this->get_categoria_by_nombre($config["from_link_categoria"]);
      if ($cat !== FALSE) $from_id_categoria = $cat->id;
    }

		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
		$sql.= " A.fecha AS fecha_original, ";
		$sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
		$sql.= " IF(C.link IS NULL,'',C.full_link) AS categoria_link, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
		$sql.= "FROM fot_trabajos A ";
		$sql.= "LEFT JOIN not_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";
		if (!empty($not_ids)) {
      if (is_array($not_ids)) $not_ids = implode(",", $not_ids);
      $sql.= "AND A.id NOT IN ($not_ids) ";
    }
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
		if (!empty($filter)) {
      $sql.= "AND (A.titulo LIKE '%$filter%' ";
      $sql.= ") ";
    }
    if ($privada == 0) $sql.= "AND A.privada != 1 ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
		if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
		if (!empty($id_idioma)) $sql.= "AND A.id_idioma = $id_idioma ";
		if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
		if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
		if (!empty($mes)) $sql.= "AND MONTH(A.fecha) = $mes ";
		if (!empty($anio)) $sql.= "AND YEAR(A.fecha) = $anio ";
		if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
		if (!empty($not_categoria)) $sql.= "AND C.link != '$not_categoria' ";
		if (!empty($ids_categorias)) $sql.= "AND A.id_categoria IN ($ids_categorias) ";
		if (!empty($not_ids_categorias)) $sql.= "AND A.id_categoria NOT IN ($not_ids_categorias) ";
    if (!empty($from_id_categoria)) {
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
      $sql.= "AND A.id_categoria IN ($ids_categorias) ";  
    }
    $sql.= "ORDER BY $order_by ";  
		$sql.= "LIMIT $limit,$offset ";
    $this->sql = $sql;
		$q = mysqli_query($this->conx,$sql);
		$salida = array();

    if ($q === FALSE) {
      error_mail($this->sql);
      return $salida;
    }

		$q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
		$t = mysqli_fetch_object($q_total);
		$this->total = $t->total;

		while(($r=mysqli_fetch_object($q))!==NULL) {
			if (!empty($r->path)) {
				$r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
			}
      if (!empty($r->categoria_path)) {
        $r->categoria_path = ((strpos($r->categoria_path,"http")===FALSE)) ? "/sistema/".$r->categoria_path : $r->categoria_path;
      }

			if (!empty($r->path)) {
			  $r->imagen = $r->path;
			} else if (!empty($r->categoria_path)) {
			  $r->imagen = $r->categoria_path;
			}
      if ($encoding == 1) $r = $this->encoding($r);

			$salida[] = $r;
		}
		return $salida;
	}

  // Devuelve todas las subcategorias, pero como un array de IDS
  function get_ids_subcategorias($id_categoria_padre,$config=array()) {
    $subcategorias = $this->get_subcategorias($id_categoria_padre,$config);
    $salida = array();
    $this->ids_to_array($subcategorias,$salida);
    return $salida;
  }
  private function ids_to_array($element,&$result = array()) {
    if (is_array($element)) {
      foreach($element as $e) {
        if (isset($e->id)) $result[] = $e->id;
        if (isset($e->children)) $this->ids_to_array($e->children,$result);
      }
    }
  }

  function get_subcategorias($id_categoria_padre = 0,$config=array()) {

    $activo = isset($config["activo"]) ? $config["activo"] : -1;
    $nivel = isset($config["nivel"]) ? $config["nivel"] : 0;
    $buscar_hijos = isset($config["buscar_hijos"]) ? $config["buscar_hijos"] : 1;
    $tiene_entradas = isset($config["tiene_entradas"]) ? $config["tiene_entradas"] : 1;
    $fija = isset($config["fija"]) ? $config["fija"] : -1;
    $link_padre = isset($config["link_padre"]) ? $config["link_padre"] : "";
    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";

    $sql = "SELECT * ";
    if (!empty($lang)) $sql.= ", nombre_$lang AS nombre ";
    $sql.= "FROM not_categorias A ";
    $sql.= "WHERE A.id_empresa = $this->id_empresa ";
    if (!empty($link_padre)) {
      $cat_padre = $this->get_categoria_by_nombre($link_padre);
      if ($cat_padre !== FALSE) $sql.= "AND A.id_padre = $cat_padre->id ";  
      unset($config["link_padre"]); // No se vuelve a mandar a los hijos
    } else {
      $sql.= "AND A.id_padre = $id_categoria_padre ";  
    }
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($fija != -1) $sql.= "AND A.fija = $fija ";
    if ($tiene_entradas == 1 && $nivel > 0) {
      $sql.= "AND EXISTS (SELECT * FROM fot_trabajos EN WHERE EN.id_empresa = A.id_empresa AND EN.id_categoria = A.id) ";
    }
    $sql.= "ORDER BY orden ASC ";

    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    if (mysqli_num_rows($q)>0) {
      while(($r=mysqli_fetch_object($q))!==NULL) {
        if ($buscar_hijos == 1) {
          $config["nivel"] = $nivel + 1;
          $r->children = $this->get_subcategorias($r->id,$config);  
        } else {
          $r->children = array();
        }
        $salida[] = $r;
      }
    }
    return $salida;
  }

  function get_categorias($id_categoria,$config = array()) {
    $link = isset($config["link"]) ? $config["link"] : "";
    $separador_nombre = isset($config["separador_nombre"]) ? $config["separador_nombre"] : " | ";
    $categorias = array();
    while(TRUE) {
      $sql = "SELECT * FROM not_categorias WHERE id = $id_categoria AND id_empresa = $this->id_empresa ";
      $q = mysqli_query($this->conx,$sql);
      $cat = mysqli_fetch_object($q);
      if ($cat === FALSE) break;
      $categorias[] = $cat;
      if ($cat->id_padre == 0) break; // Llegamos al final
      $id_categoria = $cat->id_padre;
    }
    $categorias = array_reverse($categorias);
    $link_1 = "";
    $nombre = "";
    foreach($categorias as $cat) {
      $link_1 .= $cat->link."/";
      $cat->link = $link.$link_1;
      $cat->full_name = $nombre.(!empty($nombre) ? $separador_nombre : "").(isset($cat->nombre) ? $cat->nombre : "");
      $nombre = $cat->full_name;
    }
    return $categorias;
  }

	function get_total_results() {
		return $this->total;
	}

  function get_last_sql() {
    return $this->sql;
  }

	private function encoding($entrada) {
		$entrada->plain_text = mb_convert_encoding(strip_tags($entrada->texto,"<a><i><b><br>"), 'UTF-8', 'ISO-8859-1');
		$entrada->texto = mb_convert_encoding($entrada->texto, 'UTF-8', 'ISO-8859-1');
    $entrada->path = mb_convert_encoding($entrada->path, 'UTF-8', 'ISO-8859-1');
		$entrada->titulo = mb_convert_encoding($entrada->titulo, 'UTF-8', 'ISO-8859-1');
		$entrada->categoria = mb_convert_encoding($entrada->categoria, 'UTF-8', 'ISO-8859-1');
		return $entrada;
	}

}
?>