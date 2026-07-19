<?php
class Curso_Model {

	private $id_empresa = 0;
	private $conx = null;
	private $total = 0;
  private $sql = "";

	function __construct($id_empresa,$conx) {
		$this->id_empresa = $id_empresa;
		$this->conx = $conx;
	}

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : mb_convert_encoding($r, 'UTF-8', 'ISO-8859-1'));
  }

  function esta_habilitado($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_curso = isset($config["id_curso"]) ? $config["id_curso"] : 0;
    $sql = "SELECT * FROM cursos_usuarios ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_usuario = $id_usuario ";
    $sql.= "AND id_curso = $id_curso ";
    $q = mysqli_query($this->conx,$sql);
    return (mysqli_num_rows($q)>0);
  }

  function get_variables($config = array()) {

    global $params;
    global $get_params;
    $redirect = isset($config["redirect"]) ? $config["redirect"] : 1;
    $filter = isset($get_params["filter"]) ? $get_params["filter"] : "";
    $offset = isset($get_params["offset"]) ? $get_params["offset"] : (isset($config["offset"]) ? $config["offset"] : 12);
    $label = isset($get_params["label"]) ? $get_params["label"] : (isset($config["label"]) ? $config["label"] : "");
    $page = isset($get_params["page"]) ? $get_params["page"] : (isset($config["page"]) ? $config["page"] : 0);
    $order = isset($get_params["order"]) ? $get_params["order"] : (isset($config["order"]) ? $config["order"] : 0);
    $mes = isset($get_params["m"]) ? $get_params["m"] : (isset($config["m"]) ? $config["m"] : 0);
    $anio = isset($get_params["y"]) ? $get_params["y"] : (isset($config["y"]) ? $config["y"] : 0);
    $id = isset($get_params["id"]) ? $get_params["id"] : (isset($config["id"]) ? $config["id"] : 0);
    $id_categoria = isset($get_params["cat"]) ? $get_params["cat"] : (isset($config["id_categoria"]) ? $config["id_categoria"] : 0);
    $vc_link = "cursos/";
    $no_analizar_url = isset($config["no_analizar_url"]) ? $config["no_analizar_url"] : 0;

    // IDS de las categorias que se quire agregar (o no), separados por comas
    $ids_categorias = isset($config["ids_categorias"]) ? $config["ids_categorias"] : "";
    $not_ids_categorias = isset($config["not_ids_categorias"]) ? $config["not_ids_categorias"] : "";
    if (is_array($not_ids_categorias)) $not_ids_categorias = implode(",", $not_ids_categorias);

    // Categorias que no tienen que estar
    $not_from_id_categoria = isset($config["not_from_id_categoria"]) ? $config["not_from_id_categoria"] : "";

    $id_padre = 0;
    $cat = false;
    $link_categoria = "";
    $categorias = array();
    $titulo_pagina = (isset($config["titulo"]) ? $config["titulo"] : "Noticias");
    for($i=1;$i<(sizeof($params));$i++) {
      $p = $params[$i];
      $sql = "SELECT * FROM cursos_categorias WHERE link = '".$p."' AND id_empresa = $this->id_empresa ";
      $q = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q)>0) {
        $cat = mysqli_fetch_object($q);
        $categorias[] = $cat;
        $id_categoria = $cat->id;
        $id_padre = $cat->id_padre;
        $titulo_pagina = $this->encod($cat->nombre);
        $link_categoria = $cat->link;
        $vc_link.= $cat->link.'/' ;
      } else {
        // Si el ultimo parametro es un numero, es porque indica el numero de pagina
        if (is_numeric($p) && ($i == sizeof($params)-1)) {
          $page = (int)$p;
        } else {
          // La categoria no es valida, directamente redireccionamos
          //header("Location: /404.php");          
        }
      }
    }

    // Ordenamos
    $order_by = "A.fecha DESC ";
    if ($order == 1) $order_by = "A.fecha ASC ";

    $listado = $this->get_list(array(
      "filter"=>$filter,
      "id"=>$id,
      "mes"=>$mes,
      "anio"=>$anio,
      'from_id_categoria'=>$id_categoria,
      "offset"=>$offset,
      "limit"=>($page * $offset),
      "ids_categorias"=>$ids_categorias,
      "not_ids_categorias"=>$not_ids_categorias,
      "not_from_id_categoria"=>$not_from_id_categoria,
      "link_etiqueta"=>$label,
      "order_by"=>$order_by,
    ));

    if ($redirect == 1 && sizeof($listado)==1) {
      $e=$listado[0];
      header("location:". mklink($e->link));
    }

    $total = $this->get_total_results();
    $total_paginas = ceil ($total / $offset);

    $s_params = (!empty($get_params)) ? "?".http_build_query($get_params) : "";

    return array(
      "vc_link"=>$vc_link,
      "vc_total_resultados"=>$total,
      "vc_total_paginas"=>$total_paginas,
      "vc_listado"=>$listado,
      "vc_categorias"=>$categorias,
      "vc_titulo"=>$titulo_pagina,
      "vc_id_categoria"=>$id_categoria,
      "vc_link_categoria"=>$link_categoria,
      "vc_id_padre"=>$id_padre,
      "vc_categoria"=>$cat,
      "vc_page"=>$page,
      "vc_offset"=>$offset,
      "vc_filter"=>$filter,
      "vc_params"=>$s_params,
      "vc_anio"=>$anio,
      "vc_mes"=>$mes,
      "vc_id"=>$id,
    );
  }

  function get_ultimo($config = array()) {
    $config["offset"] = 1;
    $config["order_by"] = "A.fecha DESC ";
    $lista = $this->get_list($config);
    if (sizeof($lista)>0) {
      $r = $lista[0];
      return $this->get($r->id);
    } else {
      return FALSE;
    }
  }

	// Obtenemos los datos del entrada
	function get($id = 0,$config = array()) {

		// Estos parametros se pueden deshabilitar para ganar velocidad, ya que no tiene sentido a veces cargarlos
		$buscar_relacionados = isset($config["buscar_relacionados"]) ? $config["buscar_relacionados"] : 1;
		$buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;
    $buscar_primera_imagen = isset($config["buscar_primera_imagen"]) ? $config["buscar_primera_imagen"] : 0;
		$buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 1;
    $relacionados_offset = isset($config["relacionados_offset"]) ? $config["relacionados_offset"] : 3;
    $encoding = isset($config["encoding"]) ? $config["encoding"] : 1;

    // Parametro general para deshabilitar toda la busqueda anexa
    $buscar_solo_registro = isset($config["buscar_solo_registro"]) ? $config["buscar_solo_registro"] : 0;
    if ($buscar_solo_registro == 1) {
      $buscar_relacionados = 0;
      $buscar_imagenes = 0;
      $buscar_etiquetas = 0;
    }

		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
		$id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
		$limit = isset($config["limit"]) ? $config["limit"] : 0;
		$offset = isset($config["offset"]) ? $config["offset"] : 6;

    // Si el cliente esta logueado, y necesitamos ver el estado de su curso, mandamos este parametro
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;

    // 0 = NO IMPORTA LA FECHA
    // 1 = FECHA_PUBLICACION < NOW() (Ej: diario) DEFAULT
    // 2 = FECHA_PUBLICACION > NOW()
    $filtro_fecha = isset($config["filtro_fecha"]) ? $config["filtro_fecha"] : 0;
    $now = isset($config["now"]) ? $config["now"] : date("Y-m-d H:i:s");

		$id = (int)$id;
		$sql = "SELECT A.*, DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
    $sql.= " DATE_FORMAT(A.fecha,'%m') AS mes, DATE_FORMAT(A.fecha,'%Y') AS anio, ";
		$sql.= " YEAR(A.fecha) AS anio, MONTH(A.fecha) AS mes, ";
		$sql.= " A.fecha AS fecha_original, ";
    $sql.= " IF(EDI.nombre IS NULL,'',EDI.nombre) AS autor, ";
    $sql.= " IF(EDI.path IS NULL,'',EDI.path) AS autor_path, ";
		$sql.= " IF(C.link IS NULL,'',C.full_link) AS categoria_link, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
		$sql.= "FROM cursos A ";
		$sql.= "LEFT JOIN cursos_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN cursos_autores EDI ON (A.id_empresa = EDI.id_empresa AND A.id_autor = EDI.id) ";
		$sql.= "WHERE 1=1 ";
    if ($id != 0) $sql.= "AND A.id = $id ";
    if ($filtro_fecha == 1) $sql.= "AND A.fecha <= '$now' ";
    else if ($filtro_fecha == 2) $sql.= "AND A.fecha >= '$now' ";
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
		$curso = mysqli_fetch_object($q);
		if ($encoding == 1) $curso = $this->encoding($curso);

		$curso->relacionados = array();
		if ($buscar_relacionados == 1) {
      $not_ids = array($curso->id);
      $relacionados = $this->get_list(array(
        "id_categoria"=>$curso->id_categoria,
        "not_ids"=>implode(",",$not_ids),
        "buscar_primera_imagen"=>$buscar_primera_imagen,
        "buscar_relacionados"=>0,
        "buscar_imagenes"=>0,
        "buscar_etiquetas"=>0,
        "offset"=>$relacionados_offset,
        "encoding"=>$encoding,
        "filtro_fecha"=>$filtro_fecha,
        "now"=>$now,
      ));
      $curso->relacionados = array_merge($curso->relacionados,$relacionados);
		}

    $curso->clases = array();
    $sql = "SELECT AI.* FROM cursos_clases AI WHERE AI.id_curso = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
    $q = mysqli_query($this->conx,$sql);
    while(($clase=mysqli_fetch_object($q))!==NULL) {
      /*if (!empty($clase->path)) {
        $clase->path = ((strpos($clase->path,"http")===FALSE)) ? "/sistema/".$clase->path : $clase->path;
      }*/
      $clase->nombre = $this->encod($clase->nombre);
      $clase->texto = $this->encod($clase->texto);

      // Obtenemos las preguntas
      $sql = "SELECT * FROM cursos_clases_preguntas ";
      $sql.= "WHERE id_clase = $clase->id AND id_empresa = $curso->id_empresa ";
      $sql.= "ORDER BY orden ASC ";
      $qq = mysqli_query($this->conx,$sql);
      $clase->preguntas = array();
      while(($pregunta=mysqli_fetch_object($qq))!==NULL) {

        $sql = "SELECT * FROM cursos_clases_respuestas ";
        $sql.= "WHERE id_pregunta = $pregunta->id AND id_empresa = $curso->id_empresa ";
        $sql.= "ORDER BY orden ASC ";
        $qqq = mysqli_query($this->conx,$sql);
        $pregunta->respuestas = array();
        while(($respuesta=mysqli_fetch_object($qqq))!==NULL) {
          $pregunta->respuestas[] = $respuesta;
        }

        $clase->preguntas[] = $pregunta;
      }

      // Obtenemos las evaluaciones de ese usuario
      if ($id_cliente != 0) {
        $sql = "SELECT * FROM cursos_evaluaciones ";
        $sql.= "WHERE id_empresa = $curso->id_empresa ";
        $sql.= "AND id_usuario = $id_cliente ";
        $sql.= "AND id_curso = $id ";
        $sql.= "AND id_clase = $clase->id ";
        $clase->evaluaciones = array();
        $clase->aprobada = 0;
        $q_eva = mysqli_query($this->conx,$sql);
        while(($evaluacion=mysqli_fetch_object($q_eva))!==NULL) {
          if ($evaluacion->estado == 1) {
            $clase->aprobada = 1;
          }
          $clase->evaluaciones[] = $evaluacion;
        }
      }

      $curso->clases[] = $clase;
    }

		$curso->images = array();
		if ($buscar_imagenes == 1) {
			// Obtenemos las imagenes de ese entrada
			$sql = "SELECT AI.* FROM cursos_images AI WHERE AI.id_curso = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
			$q = mysqli_query($this->conx,$sql);
			while(($r=mysqli_fetch_object($q))!==NULL) {
				if (!empty($r->path)) {
					$r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
				}
				$curso->images[] = $r->path;
			}
		}

		$curso->etiquetas = array();
		if ($buscar_etiquetas == 1) {
			// Obtenemos las etiquetas de esa entrada
			$sql = "SELECT E.* FROM cursos_etiquetas_relacion EE ";
			$sql.= " INNER JOIN cursos_etiquetas E ON (E.id = EE.id_etiqueta AND E.id_empresa = EE.id_empresa) ";
			$sql.= "WHERE EE.id_curso = $id AND E.id_empresa = $this->id_empresa ";
			$sql.= "ORDER BY EE.orden ASC";
			$q = mysqli_query($this->conx,$sql);
			while(($r=mysqli_fetch_object($q))!==NULL) {
				$curso->etiquetas[] = $r;
			}
		}

		// Link de la imagen
		if (!empty($curso->path)) {
			$curso->path = ((strpos($curso->path,"http")===FALSE)) ? "/sistema/".$curso->path : $curso->path;
		}
		if (!empty($curso->categoria_path)) {
			$curso->categoria_path = ((strpos($curso->categoria_path,"http")===FALSE)) ? "/sistema/".$curso->categoria_path : $curso->categoria_path;
		}
    if (!empty($curso->autor_path)) {
      $curso->autor_path = ((strpos($curso->autor_path,"http")===FALSE)) ? "/sistema/".$curso->autor_path : $curso->autor_path;
    }

    // Controlamos si todas las clases del curso estan aprobadas
    $curso->aprobado = 0;
    if ($id_cliente != 0) {
      $encontro = FALSE;
      foreach($curso->clases as $clase) {
        if ($clase->aprobada == 0) {
          $encontro = TRUE;
          break;
        }
      }
      if (!$encontro) $curso->aprobado = 1; // TIENE TODO APROBADO
    }

		return $curso;
	}

	function get_etiquetas($id_curso=0,$config=array()) {
		// Obtenemos las etiquetas de esa entrada
		$limit = isset($config["limit"])?$config["limit"]:0;
		$offset = isset($config["offset"])?$config["offset"]:0;
		$etiquetas = array();
		if ($id_curso != 0) {
			$order_by = isset($config["order"])?$config["order"]:"EE.orden ASC";
			$sql = "SELECT E.* FROM cursos_etiquetas_relacion EE ";
			$sql.= " INNER JOIN cursos_etiquetas E ON (E.id = EE.id_etiqueta AND E.id_empresa = EE.id_empresa) ";
			$sql.= "WHERE E.id_empresa = $this->id_empresa ";
			$sql.= "AND EE.id_curso = $id_curso ";
			$sql.= "ORDER BY $order_by ";
			if ($offset!=0) $sql.= "LIMIT $limit,$offset ";
		} else {
			$order_by = isset($config["order"])?$config["order"]:"E.nombre ASC";
			$sql = "SELECT E.* FROM cursos_etiquetas E ";
			$sql.= "WHERE E.id_empresa = $this->id_empresa ";
			$sql.= "ORDER BY $order_by ";
			if ($offset!=0) $sql.= "LIMIT $limit,$offset ";
		}
		$q = mysqli_query($this->conx,$sql);
		while(($r=mysqli_fetch_object($q))!==NULL) {
			$r->nombre = $this->encod($r->nombre);
			$etiquetas[] = $r;
		}
		return $etiquetas;
	}

  function get_categoria_by_id($id,$config=array()) {
    $link = isset($config["link"]) ? $config["link"] : "";
    $sql = "SELECT * FROM cursos_categorias ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id = $id ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $row = mysqli_fetch_object($q);
      $row->nombre = $this->encod($row->nombre);
      if (!empty($row->path)) {
        $row->path = ((strpos($row->path,"http")===FALSE)) ? "/sistema/".$row->path : $row->path;
      }
      $categorias = $this->get_categorias($id,array(
        "link"=>$link
      ));
      $ultimo = end($categorias);
      $row->full_linl = $ultimo->link;
      return $row;
    } else return FALSE;
  }


  function get_categorias_home($config=array()) {
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    $sql = "SELECT * FROM cursos_categorias A ";
    $sql.= "WHERE A.id_empresa = $this->id_empresa ";
    $sql.= "AND A.mostrar_home = 1 ";
    if (!empty($from_id_categoria)) {
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
      $sql.= "AND A.id IN ($ids_categorias) ";  
    }
    $sql.= "ORDER BY A.orden ASC ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($row=mysqli_fetch_object($q))!==NULL) {
      $row->nombre = $this->encod($row->nombre);
      $row->texto = $this->encod($row->texto);
      $salida[] = $row;
    }
    return $salida;
  }

  function get_categoria_home($config=array()) {
    $sql = "SELECT * FROM cursos_categorias ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND mostrar_home = 1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $row = mysqli_fetch_object($q);
      return $row;
    } else return FALSE;
  }

	function get_categorias($id_categoria,$config = array()) {
		$link = isset($config["link"]) ? $config["link"] : "";
		$separador_nombre = isset($config["separador_nombre"]) ? $config["separador_nombre"] : " | ";
		$categorias = array();
		while(TRUE) {
			$sql = "SELECT * FROM cursos_categorias WHERE id = $id_categoria AND id_empresa = $this->id_empresa ";
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

  function get_categoria_by_nombre($nombre) {
    $result = array();
    $sql = "SELECT * FROM cursos_categorias WHERE id_empresa = $this->id_empresa AND link = '$nombre' ";
    $q = mysqli_query($this->conx,$sql);
    $row = mysqli_fetch_object($q);
    if ($row === NULL) return FALSE;
    $row->nombre = $this->encod($row->nombre);
    if (!empty($row->path)) {
      $row->path = ((strpos($row->path,"http")===FALSE)) ? "/sistema/".$row->path : $row->path;
    }   
    return $row;
  }

  function get_subcategories($config=array()) {
    $categoria = isset($config["categoria"]) ? $config["categoria"] : "";
    $sql = "SELECT * FROM cursos_categorias ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    if (!empty($categoria)) $sql.= "AND link = '$categoria' ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    $curso = mysqli_fetch_object($q);
    return $this->get_subcategorias($curso->id,$config);
  }

  function get_subcategorias_por_link($link,$config = array()) {
    if (!isset($config["link_padre"])) $config["link_padre"] = $link;
    return $this->get_subcategorias(0,$config);
  }

	function get_subcategorias($id_categoria_padre = 0,$config=array()) {

		$activo = isset($config["activo"]) ? $config["activo"] : -1;
    $nivel = isset($config["nivel"]) ? $config["nivel"] : 0;
		$buscar_hijos = isset($config["buscar_hijos"]) ? $config["buscar_hijos"] : 1;
    $tiene_cursos = isset($config["tiene_cursos"]) ? $config["tiene_cursos"] : 1;
    $fija = isset($config["fija"]) ? $config["fija"] : -1;
    $link_padre = isset($config["link_padre"]) ? $config["link_padre"] : "";

		$sql = "SELECT * ";
		$sql.= "FROM cursos_categorias A ";
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
    if ($tiene_cursos == 1 && $nivel > 0) {
      $ids_subcategorias = $this->get_ids_subcategorias($id_categoria_padre);
      if (!empty($ids_subcategorias)) {
        $ids_subcategorias_s = implode(",", $ids_subcategorias);
        $sql.= "AND EXISTS (SELECT * FROM cursos EN WHERE EN.id_empresa = A.id_empresa AND EN.id_categoria IN ($ids_subcategorias_s)) ";
      }
    }
		$sql.= "ORDER BY orden ASC ";

		$q = mysqli_query($this->conx,$sql);
		$salida = array();
		if (mysqli_num_rows($q)>0) {
			while(($r=mysqli_fetch_object($q))!==NULL) {
        if ($buscar_hijos <= 2) {
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


	function add_view($id) {
    $this->sql = "UPDATE cursos SET vistos = vistos + 1 WHERE id = $id AND id_empresa = $this->id_empresa ";
		mysqli_query($this->conx,$this->sql);
	}


	function get_list($config = array()) {

		$limit = isset($config["limit"]) ? $config["limit"] : 0;
		$offset = isset($config["offset"]) ? $config["offset"] : 6;
		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? $config["filter"] : 0;
		
		// 0 = NO IMPORTA LA FECHA
		// 1 = FECHA_PUBLICACION < NOW() (Ej: diario) DEFAULT
		// 2 = FECHA_PUBLICACION > NOW()
		$filtro_fecha = isset($config["filtro_fecha"]) ? $config["filtro_fecha"] : 0;
		$now = isset($config["now"]) ? $config["now"] : date("Y-m-d H:i:s");

		$id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
		$categoria = isset($config["categoria"]) ? $config["categoria"] : "";
		$not_categoria = isset($config["not_categoria"]) ? $config["not_categoria"] : "";
		$fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
		$fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
		$mes = isset($config["mes"]) ? $config["mes"] : 0;
		$anio = isset($config["anio"]) ? $config["anio"] : 0;
    $id_autor = isset($config["id_autor"]) ? $config["id_autor"] : 0;
    $id = isset($config["id"]) ? $config["id"] : 0;
		$not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
		$not_ids = isset($config["not_ids"]) ? $config["not_ids"] : "";
		$order_by = isset($config["order_by"]) ? $config["order_by"] : "A.fecha DESC";
    $subtitulo = isset($config["subtitulo"]) ? $config["subtitulo"] : "";

		// IDS de las categorias que se quire agregar (o no), separados por comas
		$ids_categorias = isset($config["ids_categorias"]) ? $config["ids_categorias"] : "";
		$not_ids_categorias = isset($config["not_ids_categorias"]) ? $config["not_ids_categorias"] : "";
    if (is_array($not_ids_categorias)) $not_ids_categorias = implode(",", $not_ids_categorias);

    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 0;
		$ids_etiquetas = isset($config["ids_etiquetas"]) ? $config["ids_etiquetas"] : "";
		$link_etiqueta = isset($config["link_etiqueta"]) ? $config["link_etiqueta"] : "";

		$buscar_categorias = isset($config["buscar_categorias"]) ? $config["buscar_categorias"] : 0;
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    $not_from_id_categoria = isset($config["not_from_id_categoria"]) ? $config["not_from_id_categoria"] : 0;

    $encoding = isset($config["encoding"]) ? $config["encoding"] : 1;
    $buscar_primera_imagen = isset($config["buscar_primera_imagen"]) ? $config["buscar_primera_imagen"] : 0;

    if (isset($config["from_link_categoria"]) && !empty($config["from_link_categoria"])) {
      $cat = $this->get_categoria_by_nombre($config["from_link_categoria"]);
      if ($cat !== FALSE) $from_id_categoria = $cat->id;
      else $from_id_categoria = -1; // Si no existe, asi no trae equivocado
    }

		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
		$sql.= " A.fecha AS fecha_original, ";
		$sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
    $sql.= " YEAR(A.fecha) AS anio, MONTH(A.fecha) AS mes, ";
    $sql.= " IF(EDI.nombre IS NULL,'',EDI.nombre) AS autor, ";
    $sql.= " IF(EDI.path IS NULL,'',EDI.path) AS autor_path, ";
		$sql.= " IF(C.link IS NULL,'',C.full_link) AS categoria_link, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
		$sql.= "FROM cursos A ";
		$sql.= "LEFT JOIN cursos_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN cursos_autores EDI ON (A.id_empresa = EDI.id_empresa AND A.id_autor = EDI.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
    if (!empty($id)) $sql.= "AND A.id = $id ";
		if ($filtro_fecha == 1) $sql.= "AND A.fecha <= '$now' ";
		else if ($filtro_fecha == 2) $sql.= "AND A.fecha >= '$now' ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";
		if (!empty($not_ids)) {
      if (is_array($not_ids)) $not_ids = implode(",", $not_ids);
      $sql.= "AND A.id NOT IN ($not_ids) ";
    }
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
		if (!empty($filter)) {
      $sql.= "AND (A.nombre LIKE '%$filter%' OR A.subtitulo LIKE '%$filter%' OR A.texto LIKE '%$filter%' ";
      if ($buscar_etiquetas == 1) {
        $sql.= "OR EXISTS (SELECT * FROM cursos_etiquetas_relacion EE INNER JOIN cursos_etiquetas ETIQ ON (EE.id_etiqueta = ETIQ.id AND EE.id_empresa = ETIQ.id_empresa) WHERE A.id = EE.id_curso AND A.id_empresa = EE.id_empresa AND ETIQ.nombre LIKE '%$filter%') "; 
      }
      $sql.= ") ";
    }
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
    if (!empty($id_autor)) $sql.= "AND A.id_autor = $id_autor ";
		if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
		if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
		if (!empty($mes)) $sql.= "AND MONTH(A.fecha) = $mes ";
		if (!empty($anio)) $sql.= "AND YEAR(A.fecha) = $anio ";
		if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
		if (!empty($not_categoria)) $sql.= "AND C.link != '$not_categoria' ";
		if (!empty($ids_categorias)) $sql.= "AND A.id_categoria IN ($ids_categorias) ";
		if (!empty($not_ids_categorias)) $sql.= "AND A.id_categoria NOT IN ($not_ids_categorias) ";
    if (!empty($subtitulo)) $sql.= "AND A.subtitulo = '$subtitulo' ";
		if (!empty($ids_etiquetas)) {
			if (is_array($ids_etiquetas)) $ids_etiquetas = implode(",", $ids_etiquetas);
			$sql.= "AND EXISTS (SELECT * FROM cursos_etiquetas_relacion EE WHERE A.id = EE.id_curso AND A.id_empresa = EE.id_empresa AND EE.id_etiqueta IN ($ids_etiquetas)) ";
		}
		if (!empty($link_etiqueta)) {
			$sql.= "AND EXISTS (SELECT * FROM cursos_etiquetas_relacion EE INNER JOIN cursos_etiquetas E ON (EE.id_etiqueta = E.id AND EE.id_empresa = E.id_empresa) WHERE A.id = EE.id_curso AND A.id_empresa = EE.id_empresa AND E.link = '$link_etiqueta') ";
		}
    if (!empty($from_id_categoria)) {
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
      $sql.= "AND A.id_categoria IN ($ids_categorias) ";  
    }
    if (!empty($not_from_id_categoria)) {
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $not_ids_categorias = $this->get_ids_subcategorias($not_from_id_categoria);
      $not_ids_categorias[] = $not_from_id_categoria;
      $not_ids_categorias = implode(",", $not_ids_categorias);
      $sql.= "AND A.id_categoria NOT IN ($not_ids_categorias) ";  
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
      if (!empty($r->autor_path)) {
        $r->autor_path = ((strpos($r->autor_path,"http")===FALSE)) ? "/sistema/".$r->autor_path : $r->autor_path;
      }

			if (!empty($r->path)) {
			  $r->imagen = $r->path;
			} else if (!empty($r->categoria_path)) {
			  $r->imagen = $r->categoria_path;
			}

			// Obtenemos un array con todas las categorias de la entrada (para hacer breadcrumbs se utiliza)
			if ($buscar_categorias == 1) {
				$r->categorias = $this->get_categorias($r->id_categoria);
			}

      $r->primera_imagen = "";
      if ($buscar_primera_imagen == 1) {
        $sql = "SELECT * FROM cursos_images ";
        $sql.= "WHERE id_empresa = $this->id_empresa ";
        $sql.= "AND id_curso = $r->id ";
        $sql.= "ORDER BY orden ASC ";
        $sql.= "LIMIT 0,1 ";
        $qq = mysqli_query($this->conx,$sql);
        if (mysqli_num_rows($qq)>0) {
          $rr = mysqli_fetch_object($qq);
          $rr->path = ((strpos($rr->path,"http")===FALSE)) ? "/sistema/".$rr->path : $rr->path;
          $r->primera_imagen = $rr->path;
        }
      }      

      if ($encoding == 1) $r = $this->encoding($r);

			$salida[] = $r;
		}
		return $salida;
	}

	function get_total_results() {
		return $this->total;
	}

  function get_last_sql() {
    return $this->sql;
  }

	private function encoding($curso) {
		$curso->plain_text = (!empty($curso->descripcion)) ? $this->encod($curso->descripcion) : ($this->encod(strip_tags($curso->texto,"<a><i><b><br>")));
    $curso->plain_text = trim($curso->plain_text);
    $curso->breve = (strlen($curso->plain_text)>100) ? substr($curso->plain_text, 0, 100)."..." : $curso->plain_text;
    $curso->plain_text_en = (!empty($curso->descripcion_en)) ? $this->encod($curso->descripcion_en) : ($this->encod(strip_tags($curso->texto_en,"<a><i><b><br>")));
    $curso->plain_text_en = trim($curso->plain_text_en);
    $curso->plain_text_pt = (!empty($curso->descripcion_pt)) ? $this->encod($curso->descripcion_pt) : ($this->encod(strip_tags($curso->texto_pt,"<a><i><b><br>")));
    $curso->plain_text_pt = trim($curso->plain_text_pt);
		$curso->texto = $this->encod($curso->texto);
    $curso->path = $this->encod($curso->path);
		$curso->nombre = $this->encod($curso->nombre);
    $curso->seo_title = $this->encod($curso->seo_title);
    $curso->seo_description = $this->encod($curso->seo_description);
		$curso->subtitulo = $this->encod($curso->subtitulo);
		$curso->categoria = $this->encod($curso->categoria);
		return $curso;
	}

	function get_months($config = array()) {

		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? $config["filter"] : 0;
		$id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
		$categoria = isset($config["categoria"]) ? $config["categoria"] : "";
		$fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
		$fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    if (isset($config["from_link_categoria"]) && !empty($config["from_link_categoria"])) {
      $cat = $this->get_categoria_by_nombre($config["from_link_categoria"]);
      if ($cat !== FALSE) $from_id_categoria = $cat->id;
    }

		$sql = "SELECT DISTINCT DATE_FORMAT(A.fecha,'%Y-%m') AS aniomes, COUNT(*) AS cantidad ";
		$sql.= "FROM cursos A ";
		$sql.= "LEFT JOIN cursos_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
		if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
		if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
		if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
		if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
    if (!empty($from_id_categoria)) {
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
      $sql.= "AND A.id_categoria IN ($ids_categorias) ";  
    }
		$sql.= "GROUP BY DATE_FORMAT(A.fecha,'%Y-%m') ";
		$sql.= "ORDER BY DATE_FORMAT(A.fecha,'%Y-%m') DESC ";
		$q = mysqli_query($this->conx,$sql);
		$salida = array();
		while(($r=mysqli_fetch_object($q))!==NULL) { 
			$r->anio = substr($r->aniomes, 0, strpos($r->aniomes, "-"));
			$r->mes = substr($r->aniomes, strpos($r->aniomes,"-")+1);
      switch ($r->mes) {
        case 1: $r->nombre_mes = "Enero"; break;
        case 2: $r->nombre_mes = "Febrero"; break;
        case 3: $r->nombre_mes = "Marzo"; break;
        case 4: $r->nombre_mes = "Abril"; break;
        case 5: $r->nombre_mes = "Mayo"; break;
        case 6: $r->nombre_mes = "Junio"; break;
        case 7: $r->nombre_mes = "Julio"; break;
        case 8: $r->nombre_mes = "Agosto"; break;
        case 9: $r->nombre_mes = "Septiembre"; break;
        case 10: $r->nombre_mes = "Octubre"; break;
        case 11: $r->nombre_mes = "Noviembre"; break;
        case 12: $r->nombre_mes = "Diciembre"; break;
      }
			$salida[] = $r; 
		}
		return $salida;
	}

  function get_years($config = array()) {

    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
    $filter = isset($config["filter"]) ? $config["filter"] : 0;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $categoria = isset($config["categoria"]) ? $config["categoria"] : "";
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
    $fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    if (isset($config["from_link_categoria"]) && !empty($config["from_link_categoria"])) {
      $cat = $this->get_categoria_by_nombre($config["from_link_categoria"]);
      if ($cat !== FALSE) $from_id_categoria = $cat->id;
    }

    $sql = "SELECT DISTINCT YEAR(A.fecha) AS anio, COUNT(*) AS cantidad ";
    $sql.= "FROM cursos A ";
    $sql.= "LEFT JOIN cursos_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
    if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
    if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
    if (!empty($from_id_categoria)) {
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
      $sql.= "AND A.id_categoria IN ($ids_categorias) ";  
    }
    $sql.= "GROUP BY YEAR(A.fecha) DESC ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) { 
      $salida[] = $r; 
    }
    return $salida;
  }

	/**
	 * Obtiene las cursos destacadas
	 */
	function destacadas($config = array()) {
		$config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
		$config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
		$config["destacado"] = 1;
    $config["order_by"] = "A.destacado DESC, A.fecha DESC";
		return $this->get_list($config);
	}


	/**
	 * Obtiene las cursos mas vistas en un determinado lapso de tiempo
	 */
	function mas_leidas($config = array()) {
		$config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
		$config["offset"] = isset($config["offset"]) ? $config["offset"] : 4;
		$config["order_by"] = "A.vistos DESC";
		return $this->get_list($config);
	}

	/**
	 * Obtiene las ultimas cursos
	 */
	function ultimas($config = array()) {
		$config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
		$config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
		$config["order"] = "A.fecha DESC ";
		return $this->get_list($config);
	}

}
?>