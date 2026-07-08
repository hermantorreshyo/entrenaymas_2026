<?php
class Entrada_Model {

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

  function get_variables($config = array()) {

    global $params;
    global $get_params;
    $redirect = isset($config["redirect"]) ? $config["redirect"] : 1;
    $privada = isset($config["privada"]) ? $config["privada"] : 0;
    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 0;
    $filter = isset($get_params["filter"]) ? $get_params["filter"] : "";
    $offset = isset($get_params["offset"]) ? $get_params["offset"] : (isset($config["offset"]) ? $config["offset"] : 12);
    $label = isset($get_params["label"]) ? $get_params["label"] : (isset($config["label"]) ? $config["label"] : "");
    $page = isset($get_params["page"]) ? $get_params["page"] : (isset($config["page"]) ? $config["page"] : 0);
    $mes = isset($get_params["m"]) ? $get_params["m"] : (isset($config["m"]) ? $config["m"] : 0);
    $anio = isset($get_params["y"]) ? $get_params["y"] : (isset($config["y"]) ? $config["y"] : 0);
    $order = isset($get_params["order"]) ? $get_params["order"] : (isset($config["order"]) ? $config["order"] : 0);
    $id_editor = isset($get_params["editor"]) ? $get_params["editor"] : (isset($config["editor"]) ? $config["editor"] : 0);
    $id_categoria = isset($get_params["cat"]) ? $get_params["cat"] : (isset($config["id_categoria"]) ? $config["id_categoria"] : 0);
    $vc_link = "entradas/";
    $no_analizar_url = isset($config["no_analizar_url"]) ? $config["no_analizar_url"] : 0;

    // IDS de las categorias que se quire agregar (o no), separados por comas
    $ids_categorias = isset($config["ids_categorias"]) ? $config["ids_categorias"] : "";
    $not_ids_categorias = isset($config["not_ids_categorias"]) ? $config["not_ids_categorias"] : "";
    if (is_array($not_ids_categorias)) $not_ids_categorias = implode(",", $not_ids_categorias);

    // Categorias que no tienen que estar
    $not_from_id_categoria = isset($config["not_from_id_categoria"]) ? $config["not_from_id_categoria"] : "";

    $id_padre = 0;
    $vc_id_categoria_padre = 0;
    $cat = false;
    $link_categoria = "";
    $categorias = array();
    $titulo_pagina = (isset($config["titulo"]) ? $config["titulo"] : "Noticias");
    for($i=1;$i<(sizeof($params));$i++) {
      $p = $params[$i];
      $sql = "SELECT * FROM not_categorias WHERE link = '".$p."' AND id_empresa = $this->id_empresa ";
      $sql.= "AND id_padre = $vc_id_categoria_padre ";
      $q = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q)>0) {
        $cat = mysqli_fetch_object($q);
        $categorias[] = $cat;
        $id_categoria = $cat->id;
        $id_padre = $cat->id_padre;
        $vc_id_categoria_padre = $cat->id;
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

    // Ponemos como titulo el nombre de la categoria principal
    $cat_ppal = false;
    if (sizeof($categorias)>0) {
      $cat_ppal = $categorias[0];
    }

    // Distintos ordenes
    $order_by = "A.fecha DESC ";
    if ($order == 1) $order_by = "A.destacado DESC, A.fecha DESC ";
    else if ($order == 2) $order_by = "A.titulo ASC ";
    else if ($order == 3) $order_by = "A.fecha ASC ";

    $listado = $this->get_list(array(
      "filter"=>$filter,
      "mes"=>$mes,
      "anio"=>$anio,
      'from_id_categoria'=>$id_categoria,
      "offset"=>$offset,
      "limit"=>($page * $offset),
      "ids_categorias"=>$ids_categorias,
      "not_ids_categorias"=>$not_ids_categorias,
      "not_from_id_categoria"=>$not_from_id_categoria,
      "link_etiqueta"=>$label,
      "id_editor"=>$id_editor,
      "order_by"=>$order_by,
      "buscar_etiquetas"=>$buscar_etiquetas,
      "privada"=>$privada,
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
      "vc_categoria_principal"=>$cat_ppal,
      "vc_id_padre"=>$id_padre,
      "vc_categoria"=>$cat,
      "vc_page"=>$page,
      "vc_id_editor"=>$id_editor,
      "vc_offset"=>$offset,
      "vc_filter"=>$filter,
      "vc_params"=>$s_params,
      "vc_order"=>$order,
    );
  }

	// Obtenemos los datos del entrada
	function get($id = 0,$config = array()) {

		// Estos parametros se pueden deshabilitar para ganar velocidad, ya que no tiene sentido a veces cargarlos
		$buscar_relacionados = isset($config["buscar_relacionados"]) ? $config["buscar_relacionados"] : 1;
		$buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;
    $buscar_primera_imagen = isset($config["buscar_primera_imagen"]) ? $config["buscar_primera_imagen"] : 0;
		$buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 1;
		$buscar_comentarios = isset($config["buscar_comentarios"]) ? $config["buscar_comentarios"] : 1;
    $buscar_preguntas = isset($config["buscar_preguntas"]) ? $config["buscar_preguntas"] : 1;
    $buscar_horarios = isset($config["buscar_horarios"]) ? $config["buscar_horarios"] : 1;
    $relacionados_offset = isset($config["relacionados_offset"]) ? $config["relacionados_offset"] : 3;
    $encoding = isset($config["encoding"]) ? $config["encoding"] : 1;
    $privada = isset($config["privada"]) ? $config["privada"] : 0;

    // Parametro general para deshabilitar toda la busqueda anexa
    $buscar_solo_registro = isset($config["buscar_solo_registro"]) ? $config["buscar_solo_registro"] : 0;
    if ($buscar_solo_registro == 1) {
      $buscar_relacionados = 0;
      $buscar_imagenes = 0;
      $buscar_etiquetas = 0;
      $buscar_comentarios = 0;
      $buscar_preguntas = 0;
      $buscar_horarios = 0;
    }

    $lang = isset($config["lang"]) ? str_replace("es", "",$config["lang"]) : "";

		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
		$id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
		$limit = isset($config["limit"]) ? $config["limit"] : 0;
		$offset = isset($config["offset"]) ? $config["offset"] : 6;
		$tiene_video = isset($config["tiene_video"]) ? $config["tiene_video"] : 0;

    // 0 = NO IMPORTA LA FECHA
    // 1 = FECHA_PUBLICACION < NOW() (Ej: diario) DEFAULT
    // 2 = FECHA_PUBLICACION > NOW()
    $filtro_fecha = isset($config["filtro_fecha"]) ? $config["filtro_fecha"] : 1;
    $now = isset($config["now"]) ? $config["now"] : date("Y-m-d H:i:s");

		$id = (int)$id;
		$sql = "SELECT A.*, DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
    $sql.= " DATE_FORMAT(A.fecha,'%m') AS mes, DATE_FORMAT(A.fecha,'%Y') AS anio, ";
    if (!empty($lang)) $sql.= " A.titulo_$lang AS titulo, ";
    if (!empty($lang)) $sql.= " A.texto_$lang AS texto, ";
		$sql.= " YEAR(A.fecha) AS anio, MONTH(A.fecha) AS mes, ";
		$sql.= " A.fecha AS fecha_original, ";
    $sql.= " IF(EDI.nombre IS NULL,'',EDI.nombre) AS editor, ";
    $sql.= " IF(EDI.path IS NULL,'',EDI.path) AS editor_path, ";
    $sql.= " IF(EDI.tipo IS NULL,'',EDI.tipo) AS editor_tipo, ";
    $sql.= " IF(EDI.subtitulo IS NULL,'',EDI.subtitulo) AS editor_subtitulo, ";
		$sql.= " IF(P.nombre IS NULL,'',P.nombre) AS pais, ";
    $sql.= " IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= " IF(P.nombre_en IS NULL,'',P.nombre_en) AS pais_en, ";
		$sql.= " IF(C.link IS NULL,'',C.full_link) AS categoria_link, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path, ";
    $sql.= " IF(C.nombre_en IS NULL,'',C.nombre_en) AS categoria_en, ";
    $sql.= " IF(C.nombre_pt IS NULL,'',C.nombre_pt) AS categoria_pt, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
		$sql.= "FROM not_entradas A ";
		$sql.= "LEFT JOIN not_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN not_editores EDI ON (A.id_empresa = EDI.id_empresa AND A.id_editor = EDI.id) ";
		$sql.= "LEFT JOIN com_paises P ON (A.id_pais = P.id) ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
		$sql.= "WHERE 1=1 ";
    if ($id != 0) $sql.= "AND A.id = $id ";
    if ($privada == 0) $sql.= "AND A.privada != 1 ";
    if ($filtro_fecha == 1) $sql.= "AND A.fecha <= '$now' ";
    else if ($filtro_fecha == 2) $sql.= "AND A.fecha >= '$now' ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";
		if ($tiene_video == 1) $sql.= "AND A.video != '' ";
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

    // Buscamos las categorias de la entrada
    $entrada->categorias = $this->get_categorias($entrada->id_categoria);

		$entrada->relacionados = array();
		if ($buscar_relacionados == 1) {

      $sql = "SELECT AR.* ";
      $sql.= "FROM not_entradas_relacionadas AR ";
      $sql.= "WHERE AR.id_entrada = $id ";
      $sql.= "AND AR.id_empresa = $this->id_empresa ";
      $sql.= "ORDER BY AR.orden ASC ";
      $sql.= "LIMIT 0, $relacionados_offset ";
      $q = mysqli_query($this->conx,$sql);
      $not_ids = array($entrada->id);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        if ($r->id_relacion != 0) {
          // Estamos relacionando con una entrada en particular
          $rel = $this->get($r->id_relacion,array(
            "buscar_relacionados"=>0,
            "buscar_imagenes"=>0,
            "buscar_primera_imagen"=>$buscar_primera_imagen,
          ));
          if ($rel !== FALSE) {
            $entrada->relacionados[] = $rel;
            $not_ids[] = $rel->id;
          }
        } else if ($r->id_categoria != 0) {
          // Estamos relacionando con otra categoria
          $relacionados = $this->get_list(array(
            "id_categoria"=>$r->id_categoria,
            "not_ids"=>implode(",",$not_ids),
            "buscar_primera_imagen"=>$buscar_primera_imagen,
            "buscar_relacionados"=>0,
            "buscar_comentarios"=>0,
            "buscar_imagenes"=>0,
            "buscar_etiquetas"=>0,
            "buscar_horarios"=>0,
            "offset"=>$relacionados_offset,
            "encoding"=>$encoding,
            "filtro_fecha"=>$filtro_fecha,
            "now"=>$now,
          ));
          if (sizeof($relacionados)>0) {
            foreach($relacionados as $r2) {
              $not_ids[] = $r2->id;
            }
            $entrada->relacionados = array_merge($entrada->relacionados,$relacionados);
          }
        }
      }
      // Rellenamos el array con articulos de la misma categoria
      $relacionados_offset = $relacionados_offset - sizeof($entrada->relacionados);
      if ($relacionados_offset > 0) {
        $relacionados = $this->get_list(array(
          "id_categoria"=>$entrada->id_categoria,
          "not_ids"=>implode(",",$not_ids),
          "buscar_primera_imagen"=>$buscar_primera_imagen,
          "buscar_relacionados"=>0,
          "buscar_comentarios"=>0,
          "buscar_imagenes"=>0,
          "buscar_etiquetas"=>0,
          "buscar_horarios"=>0,
          "offset"=>$relacionados_offset,
          "encoding"=>$encoding,
          "filtro_fecha"=>$filtro_fecha,
          "now"=>$now,
        ));
        $entrada->relacionados = array_merge($entrada->relacionados,$relacionados);
      }
		}

		$entrada->images = array();
		if ($buscar_imagenes == 1) {
			// Obtenemos las imagenes de ese entrada
			$sql = "SELECT AI.* FROM not_entradas_images AI WHERE AI.id_entrada = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
			$q = mysqli_query($this->conx,$sql);
			while(($r=mysqli_fetch_object($q))!==NULL) {
				if (!empty($r->path)) {
					$r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
				}
				$entrada->images[] = $r->path;
			}
		}

    $entrada->preguntas = array();
    if ($buscar_preguntas == 1) {
      $sql = "SELECT AI.* FROM not_entradas_preguntas AI WHERE AI.id_entrada = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        $r->pregunta = $this->encod(nl2br($r->pregunta));
        $r->respuesta = $this->encod(nl2br($r->respuesta));
        $entrada->preguntas[] = $r;
      }
    }

    // Si el video es de youtube
    $entrada = $this->youtube($entrada);

    $entrada->horarios = array();
    if ($buscar_horarios == 1) {
      // Obtenemos las imagenes de ese entrada
      // Obtenemos las imagenes de ese entrada
      $sql = "SELECT AI.*, DATE_FORMAT(AI.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(AI.hora,'%H:%i') AS hora ";
      $sql.= "FROM not_entradas_horarios AI WHERE AI.id_entrada = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        $entrada->horarios[] = $r;
      }
    }

		$entrada->etiquetas = array();
		if ($buscar_etiquetas == 1) {
			// Obtenemos las etiquetas de esa entrada
			$sql = "SELECT E.* FROM not_entradas_etiquetas EE ";
			$sql.= " INNER JOIN not_etiquetas E ON (E.id = EE.id_etiqueta AND E.id_empresa = EE.id_empresa) ";
			$sql.= "WHERE EE.id_entrada = $id AND E.id_empresa = $this->id_empresa ";
			$sql.= "ORDER BY EE.orden ASC";
			$q = mysqli_query($this->conx,$sql);
			while(($r=mysqli_fetch_object($q))!==NULL) {
				$entrada->etiquetas[] = $r;
			}
		}

		$entrada->comentarios = array();
		if ($buscar_comentarios == 1) {
			$sql = "SELECT EC.*, EC.nombre AS usuario, ";
			$sql.= " DATE_FORMAT(EC.fecha,'%d/%m/%Y') AS fecha, ";
			$sql.= " DATE_FORMAT(EC.fecha,'%H:%i') AS hora, ";
			$sql.= " IF(C.path IS NULL,'',C.path) AS path ";
			$sql.= "FROM not_entradas_comentarios EC ";
			$sql.= " LEFT JOIN web_users C ON (C.id = EC.id_usuario AND C.id_empresa = EC.id_empresa) ";
			$sql.= "WHERE EC.id_entrada = $id AND EC.id_empresa = $this->id_empresa ";
			$sql.= " AND EC.estado = 1 "; // Si el comentario esta activo
			$sql.= "ORDER BY EC.orden ASC";
			$q = mysqli_query($this->conx,$sql);
			while(($r=mysqli_fetch_object($q))!==NULL) {
				if (!empty($r->path)) $r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
				$r->usuario = $this->encod($r->usuario);
				$r->texto = $this->encod($r->texto);
				$entrada->comentarios[] = $r;
			}
		}

		// Link de la imagen
		if (!empty($entrada->path)) {
			$entrada->path = ((strpos($entrada->path,"http")===FALSE)) ? "/sistema/".$entrada->path : $entrada->path;
		}
		if (!empty($entrada->categoria_path)) {
			$entrada->categoria_path = ((strpos($entrada->categoria_path,"http")===FALSE)) ? "/sistema/".$entrada->categoria_path : $entrada->categoria_path;
		}
    if (!empty($entrada->editor_path)) {
      $entrada->editor_path = ((strpos($entrada->editor_path,"http")===FALSE)) ? "/sistema/".$entrada->editor_path : $entrada->editor_path;
    }

		return $entrada;
	}

	function get_etiquetas($id_entrada=0,$config=array()) {
		// Obtenemos las etiquetas de esa entrada
		$limit = isset($config["limit"])?$config["limit"]:0;
		$offset = isset($config["offset"])?$config["offset"]:0;
		$etiquetas = array();
		if ($id_entrada != 0) {
			$order_by = isset($config["order"])?$config["order"]:"EE.orden ASC";
			$sql = "SELECT E.* FROM not_entradas_etiquetas EE ";
			$sql.= " INNER JOIN not_etiquetas E ON (E.id = EE.id_etiqueta AND E.id_empresa = EE.id_empresa) ";
			$sql.= "WHERE E.id_empresa = $this->id_empresa ";
			$sql.= "AND EE.id_entrada = $id_entrada ";
			$sql.= "ORDER BY $order_by ";
			if ($offset!=0) $sql.= "LIMIT $limit,$offset ";
		} else {
			$order_by = isset($config["order"])?$config["order"]:"E.nombre ASC";
			$sql = "SELECT E.* FROM not_etiquetas E ";
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
    $buscar_subcategorias = isset($config["buscar_subcategorias"]) ? $config["buscar_subcategorias"] : 0;
    $sql = "SELECT * FROM not_categorias ";
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

      $row->children = array();
      if ($buscar_subcategorias == 1) $row->children = $this->get_subcategorias($id);

      return $row;
    } else return FALSE;
  }


  function get_categorias_home($config=array()) {
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    $sql = "SELECT * FROM not_categorias A ";
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
    $sql = "SELECT * FROM not_categorias ";
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

  function get_categoria_by_nombre($nombre) {
    $result = array();
    $sql = "SELECT * FROM not_categorias WHERE id_empresa = $this->id_empresa AND link = '$nombre' ";
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
    $sql = "SELECT * FROM not_categorias ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    if (!empty($categoria)) $sql.= "AND link = '$categoria' ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    $entrada = mysqli_fetch_object($q);
    return $this->get_subcategorias($entrada->id,$config);
  }

  function get_subcategorias_por_link($link,$config = array()) {
    if (!isset($config["link_padre"])) $config["link_padre"] = $link;
    return $this->get_subcategorias(0,$config);
  }

	function get_subcategorias($id_categoria_padre = 0,$config=array()) {

		$activo = isset($config["activo"]) ? $config["activo"] : -1;
    $nivel = isset($config["nivel"]) ? $config["nivel"] : 0;
		$buscar_hijos = isset($config["buscar_hijos"]) ? $config["buscar_hijos"] : 1;
    $tiene_entradas = isset($config["tiene_entradas"]) ? $config["tiene_entradas"] : 1;
    $contar_entradas = isset($config["contar_entradas"]) ? $config["contar_entradas"] : 0;
    $fija = isset($config["fija"]) ? $config["fija"] : -1;
    $mostrar_home = isset($config["mostrar_home"]) ? $config["mostrar_home"] : -1;
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
    if ($mostrar_home != -1) $sql.= "AND A.mostrar_home = $mostrar_home ";
    if ($tiene_entradas == 1 && $nivel > 0) {
      $ids_subcategorias = $this->get_ids_subcategorias($id_categoria_padre);
      if (!empty($ids_subcategorias)) {
        $ids_subcategorias_s = implode(",", $ids_subcategorias);
        $sql.= "AND EXISTS (SELECT * FROM not_entradas EN WHERE EN.id_empresa = A.id_empresa AND EN.id_categoria IN ($ids_subcategorias_s)) ";
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

        // Si tenemos que contar las entradas
        $r->cantidad = 0;
        if ($contar_entradas == 1) {
          $this->get_list(array(
            "from_id_categoria"=>$r->id,
            "solo_contar"=>1,
          ));
          $r->cantidad = $this->get_total_results();
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
    $this->sql = "UPDATE not_entradas SET vistos = vistos + 1 WHERE id = $id AND id_empresa = $this->id_empresa ";
		mysqli_query($this->conx,$this->sql);
	}


	function get_list($config = array()) {

		$limit = isset($config["limit"]) ? intval($config["limit"]) : 0;
		$offset = isset($config["offset"]) ? intval($config["offset"]) : 6;
		$activo = isset($config["activo"]) ? intval($config["activo"]) : 1;
		$destacado = isset($config["destacado"]) ? intval($config["destacado"]) : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? intval($config["filter"]) : 0;
    $nivel_importancia = isset($config["nivel_importancia"]) ? intval($config["nivel_importancia"]) : -1;
    $privada = isset($config["privada"]) ? intval($config["privada"]) : 0;
    $id_comision = isset($config["id_comision"]) ? intval($config["id_comision"]) : 0;
		
		// 0 = NO IMPORTA LA FECHA
		// 1 = FECHA_PUBLICACION < NOW() (Ej: diario) DEFAULT
		// 2 = FECHA_PUBLICACION > NOW()
		$filtro_fecha = isset($config["filtro_fecha"]) ? intval($config["filtro_fecha"]) : 1;
		$now = isset($config["now"]) ? $config["now"] : date("Y-m-d H:i:s");

		$id_cliente = isset($config["id_cliente"]) ? intval($config["id_cliente"]) : 0;
    $id_categoria = isset($config["id_categoria"]) ? intval($config["id_categoria"]) : 0;
		$categoria = isset($config["categoria"]) ? $config["categoria"] : "";
		$not_categoria = isset($config["not_categoria"]) ? $config["not_categoria"] : "";
		$id_idioma = isset($config["id_idioma"]) ? intval($config["id_idioma"]) : 0;
		$fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
		$fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
		$mes = isset($config["mes"]) ? intval($config["mes"]) : 0;
		$anio = isset($config["anio"]) ? intval($config["anio"]) : 0;
		$id_usuario = isset($config["id_usuario"]) ? intval($config["id_usuario"]) : 0;
    $id_editor = isset($config["id_editor"]) ? intval($config["id_editor"]) : 0;
		$id_pais = isset($config["id_pais"]) ? intval($config["id_pais"]) : 0;
		$not_id = isset($config["not_id"]) ? intval($config["not_id"]) : 0;
		$not_ids = isset($config["not_ids"]) ? $config["not_ids"] : "";
		$order_by = isset($config["order_by"]) ? $config["order_by"] : "A.fecha DESC";
		$tiene_video = isset($config["tiene_video"]) ? intval($config["tiene_video"]) : 0;
    $custom_1 = isset($config["custom_1"]) ? $config["custom_1"] : "";
    $custom_2 = isset($config["custom_2"]) ? $config["custom_2"] : "";
    $custom_3 = isset($config["custom_3"]) ? $config["custom_3"] : "";
    $custom_4 = isset($config["custom_4"]) ? $config["custom_4"] : "";
    $subtitulo = isset($config["subtitulo"]) ? $config["subtitulo"] : "";

    // Sirve solamente para obtener la cantidad
    $solo_contar = isset($config["solo_contar"]) ? intval($config["solo_contar"]) : 0;

		// IDS de las categorias que se quire agregar (o no), separados por comas
		$ids_categorias = isset($config["ids_categorias"]) ? $config["ids_categorias"] : "";
		$not_ids_categorias = isset($config["not_ids_categorias"]) ? $config["not_ids_categorias"] : "";
    if (is_array($not_ids_categorias)) $not_ids_categorias = implode(",", $not_ids_categorias);

    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? intval($config["buscar_etiquetas"]) : 0;
		$ids_etiquetas = isset($config["ids_etiquetas"]) ? $config["ids_etiquetas"] : "";
		$link_etiqueta = isset($config["link_etiqueta"]) ? $config["link_etiqueta"] : "";

		$buscar_categorias = isset($config["buscar_categorias"]) ? intval($config["buscar_categorias"]) : 0;
    $from_id_categoria = isset($config["from_id_categoria"]) ? intval($config["from_id_categoria"]) : 0;
    $not_from_id_categoria = isset($config["not_from_id_categoria"]) ? intval($config["not_from_id_categoria"]) : 0;

    $buscar_horarios = isset($config["buscar_horarios"]) ? intval($config["buscar_horarios"]) : 0;
    $horario_fecha = isset($config["horario_fecha"]) ? $config["horario_fecha"] : "";
    $horario_fecha_mes = isset($config["horario_fecha_mes"]) ? $config["horario_fecha_mes"] : "";
    $horario_fecha_anio = isset($config["horario_fecha_anio"]) ? $config["horario_fecha_anio"] : "";
    $encoding = isset($config["encoding"]) ? intval($config["encoding"]) : 1;
    $buscar_primera_imagen = isset($config["buscar_primera_imagen"]) ? intval($config["buscar_primera_imagen"]) : 0;

    if (isset($config["from_link_categoria"]) && !empty($config["from_link_categoria"])) {
      $cat = $this->get_categoria_by_nombre($config["from_link_categoria"]);
      if ($cat !== FALSE) $from_id_categoria = $cat->id;
      else $from_id_categoria = -1; // Si no existe, asi no trae equivocado
    }

		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " (emocion_1_cant + emocion_2_cant + emocion_3_cant + emocion_4_cant) AS interaccion, ";
		$sql.= " A.fecha AS fecha_original, ";
		$sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(A.fecha,'%H:%i') AS hora, ";
    $sql.= " DAY(A.fecha) AS dia, MONTH(A.fecha) AS mes, YEAR(A.fecha) AS anio, ";
    $sql.= " IF(EDI.nombre IS NULL,'',EDI.nombre) AS editor, ";
    $sql.= " IF(EDI.path IS NULL,'',EDI.path) AS editor_path, ";
    $sql.= " IF(EDI.tipo IS NULL,'',EDI.tipo) AS editor_tipo, ";
    $sql.= " IF(EDI.subtitulo IS NULL,'',EDI.subtitulo) AS editor_subtitulo, ";
		$sql.= " IF(P.nombre IS NULL,'',P.nombre) AS pais, ";
    $sql.= " IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= " IF(P.nombre_en IS NULL,'',P.nombre_en) AS pais_en, ";
		$sql.= " IF(C.link IS NULL,'',C.full_link) AS categoria_link, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS categoria_path, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
		$sql.= "FROM not_entradas A ";
		$sql.= "LEFT JOIN not_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN not_editores EDI ON (A.id_empresa = EDI.id_empresa AND A.id_editor = EDI.id) ";
		$sql.= "LEFT JOIN com_paises P ON (A.id_pais = P.id) ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
		if ($filtro_fecha == 1) $sql.= "AND A.fecha <= '$now' ";
		else if ($filtro_fecha == 2) $sql.= "AND A.fecha >= '$now' ";
		if ($not_id > 0) $sql.= "AND A.id != $not_id ";
		if (!empty($not_ids)) {
      if (is_array($not_ids)) $not_ids = implode(",", $not_ids);
      $sql.= "AND A.id NOT IN ($not_ids) ";
    }
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
    if ($nivel_importancia != -1) $sql.= "AND A.nivel_importancia = $nivel_importancia ";
		if (!empty($filter)) {
      $sql.= "AND (A.titulo LIKE '%$filter%' OR A.subtitulo LIKE '%$filter%' OR A.texto LIKE '%$filter%' ";
      if ($buscar_etiquetas == 1) {
        $sql.= "OR EXISTS (SELECT * FROM not_entradas_etiquetas EE INNER JOIN not_etiquetas ETIQ ON (EE.id_etiqueta = ETIQ.id AND EE.id_empresa = ETIQ.id_empresa) WHERE A.id = EE.id_entrada AND A.id_empresa = EE.id_empresa AND ETIQ.nombre LIKE '%$filter%') "; 
      }
      $sql.= ") ";
    }
    if ($privada == 0) $sql.= "AND A.privada != 1 ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
    if (!empty($id_comision)) $sql.= "AND (A.id_comision = $id_comision OR A.id_comision = 0) ";
		if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
    if (!empty($id_editor)) $sql.= "AND A.id_editor = $id_editor ";
		if (!empty($id_idioma)) $sql.= "AND A.id_idioma = $id_idioma ";
		if (!empty($id_pais)) $sql.= "AND A.id_pais = $id_pais ";
		if (!empty($fecha_desde)) $sql.= "AND A.fecha >= '$fecha_desde' ";
		if (!empty($fecha_hasta)) $sql.= "AND A.fecha <= '$fecha_hasta' ";
		if (!empty($tiene_video)) $sql.= "AND A.video != '' ";
		if (!empty($mes)) $sql.= "AND MONTH(A.fecha) = $mes ";
		if (!empty($anio)) $sql.= "AND YEAR(A.fecha) = $anio ";
		if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
		if (!empty($not_categoria)) $sql.= "AND C.link != '$not_categoria' ";
		if (!empty($ids_categorias)) $sql.= "AND A.id_categoria IN ($ids_categorias) ";
		if (!empty($not_ids_categorias)) $sql.= "AND A.id_categoria NOT IN ($not_ids_categorias) ";
    if (!empty($custom_1)) $sql.= "AND A.custom_1 = '$custom_1' ";
    if (!empty($custom_2)) $sql.= "AND A.custom_2 = '$custom_2' ";
    if (!empty($custom_3)) $sql.= "AND A.custom_3 = '$custom_3' ";
    if (!empty($custom_4)) $sql.= "AND A.custom_4 = '$custom_4' ";
    if (!empty($subtitulo)) $sql.= "AND A.subtitulo = '$subtitulo' ";
		if (!empty($ids_etiquetas)) {
			if (is_array($ids_etiquetas)) $ids_etiquetas = implode(",", $ids_etiquetas);
			$sql.= "AND EXISTS (SELECT * FROM not_entradas_etiquetas EE WHERE A.id = EE.id_entrada AND A.id_empresa = EE.id_empresa AND EE.id_etiqueta IN ($ids_etiquetas)) ";
		}
		if (!empty($link_etiqueta)) {
      /*
			$sql.= "AND EXISTS (SELECT * FROM not_entradas_etiquetas EE INNER JOIN not_etiquetas E ON (EE.id_etiqueta = E.id AND EE.id_empresa = E.id_empresa) WHERE A.id = EE.id_entrada AND A.id_empresa = EE.id_empresa AND E.link = '$link_etiqueta') ";
      */
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
    if (!empty($horario_fecha)) {
      $sql.= "AND EXISTS (SELECT 1 FROM not_entradas_horarios EH WHERE EH.id_entrada = A.id AND EH.id_empresa = A.id_empresa AND EH.fecha = '$horario_fecha') ";
    }
    if (!empty($horario_fecha_mes) && !empty($horario_fecha_anio)) {
      $sql.= "AND EXISTS (SELECT 1 FROM not_entradas_horarios EH WHERE EH.id_entrada = A.id AND EH.id_empresa = A.id_empresa AND DATE_FORMAT(EH.fecha,'%m') = '$horario_fecha_mes' AND YEAR(EH.fecha) = '$horario_fecha_anio') ";
    }

    if ($order_by == "horarios") {
      $sql.= "ORDER BY (SELECT EH.fecha FROM not_entradas_horarios EH WHERE EH.id_empresa = A.id_empresa AND EH.id_entrada = A.id LIMIT 0,1) ASC ";
    } else if ($order_by != "mas_comentadas") {
      $sql.= "ORDER BY $order_by ";  
    }
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

    if ($solo_contar == 1) return;

		while(($r=mysqli_fetch_object($q))!==NULL) {
			if (!empty($r->path)) {
				$r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
			}
      if (!empty($r->categoria_path)) {
        $r->categoria_path = ((strpos($r->categoria_path,"http")===FALSE)) ? "/sistema/".$r->categoria_path : $r->categoria_path;
      }
      if (!empty($r->editor_path)) {
        $r->editor_path = ((strpos($r->editor_path,"http")===FALSE)) ? "/sistema/".$r->editor_path : $r->editor_path;
      }

			if (!empty($r->path)) {
			  $r->imagen = $r->path;
			} else if($r->latitud != 0 && $r->longitud != 0) {
			  $r->imagen = "https://maps.googleapis.com/maps/api/staticmap?zoom=".$r->zoom."&size=470x246&maptype=roadmap&markers=color:red%7C".$r->latitud.",".$r->longitud."&key=AIzaSyDZ0GqtfheX506XJJ90TQOQJ2lp7yYRQkY";
			} else if (!empty($r->categoria_path)) {
			  $r->imagen = $r->categoria_path;
			}

      // Si el video es de youtube
      $r = $this->youtube($r);

			// Cantidad de comentarios
			$sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS comentarios FROM not_entradas_comentarios WHERE id_entrada = $r->id AND estado = 1 AND id_empresa = $r->id_empresa ";
			$qq = mysqli_query($this->conx,$sql);
			$rr = mysqli_fetch_object($qq);
			$r->total_comentarios = $rr->comentarios;

			// Obtenemos un array con todas las categorias de la entrada (para hacer breadcrumbs se utiliza)
			if ($buscar_categorias == 1) {
				$r->categorias = $this->get_categorias($r->id_categoria);
			}

      if ($buscar_horarios == 1) {
        $r->horarios = array();
        $sql = "SELECT AI.*, DATE_FORMAT(AI.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(AI.hora,'%H:%i') AS hora, ";
        $sql.= " CONCAT(AI.fecha,' ',AI.hora) AS fecha_original ";
        $sql.= "FROM not_entradas_horarios AI WHERE AI.id_entrada = $r->id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
        $qq = mysqli_query($this->conx,$sql);
        while(($rr=mysqli_fetch_object($qq))!==NULL) {
          $r->horarios[] = $rr;
        }
      }

      $r->primera_imagen = "";
      if ($buscar_primera_imagen == 1) {
        $sql = "SELECT * FROM not_entradas_images ";
        $sql.= "WHERE id_empresa = $this->id_empresa ";
        $sql.= "AND id_entrada = $r->id ";
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

    if ($order_by == "mas_comentadas") usort($salida,array('Entrada_Model','ordenar_mas_comentadas'));
		return $salida;
	}

  private static function ordenar_mas_comentadas($a,$b) {
    return ($a->total_comentarios <= $b->total_comentarios) ? 1 : -1;
  }

  private function youtube($r) {
    $r->youtube_id = "";
    $r->youtube_link_iframe = "";
    $r->youtube_thumbnail = "";
    if (strpos($r->video, "youtube") !== FALSE) {
      $query = parse_url($r->video,PHP_URL_QUERY);
      parse_str($query,$res);
      if (isset($res["v"])) {
        // Si existe el parametro V
        $r->youtube_id = $res["v"];
        $r->youtube_thumbnail = "https://img.youtube.com/vi/".$r->youtube_id."/0.jpg";
        $r->youtube_link_iframe = "https://www.youtube.com/embed/".$r->youtube_id;
      }
    }
    return $r;    
  }

	function get_total_results() {
		return $this->total;
	}

  function get_last_sql() {
    return $this->sql;
  }

	private function encoding($entrada) {
		$entrada->plain_text = (!empty($entrada->descripcion)) ? $this->encod($entrada->descripcion) : ($this->encod(strip_tags($entrada->texto,"<br>")));
    $entrada->plain_text = trim($entrada->plain_text);
    $entrada->plain_text_en = (!empty($entrada->descripcion_en)) ? $this->encod($entrada->descripcion_en) : ($this->encod(strip_tags($entrada->texto_en,"<a><i><b><br>")));
    $entrada->plain_text_en = trim($entrada->plain_text_en);
    $entrada->plain_text_pt = (!empty($entrada->descripcion_pt)) ? $this->encod($entrada->descripcion_pt) : ($this->encod(strip_tags($entrada->texto_pt,"<a><i><b><br>")));
    $entrada->plain_text_pt = trim($entrada->plain_text_pt);
		$entrada->texto = $this->encod($entrada->texto);
    $entrada->path = $this->encod($entrada->path);
		$entrada->texto_destacado = $this->encod($entrada->texto_destacado);
		$entrada->titulo = $this->encod($entrada->titulo);
    $entrada->antetitulo = $this->encod($entrada->antetitulo);
    $entrada->seo_title = $this->encod($entrada->seo_title);
    $entrada->seo_description = $this->encod($entrada->seo_description);
		$entrada->subtitulo = $this->encod($entrada->subtitulo);
		$entrada->categoria = $this->encod($entrada->categoria);
    $entrada->custom_1 = $this->encod($entrada->custom_1);
    $entrada->custom_2 = $this->encod($entrada->custom_2);
    $entrada->custom_3 = $this->encod($entrada->custom_3);
    $entrada->custom_4 = $this->encod($entrada->custom_4);
    $entrada->custom_5 = $this->encod($entrada->custom_5);
    $entrada->custom_6 = $this->encod($entrada->custom_6);
    $entrada->custom_7 = $this->encod($entrada->custom_7);
    $entrada->custom_8 = $this->encod($entrada->custom_8);
    $entrada->custom_9 = $this->encod($entrada->custom_9);
    $entrada->custom_10 = $this->encod($entrada->custom_10);
    if (isset($entrada->usuario)) $entrada->usuario = $this->encod($entrada->usuario);

    if ($entrada->id_empresa == 821) {
      // Reemplazamos FontAwesome
      $entrada->texto = str_replace("fa fa-arrow-circle-o-right", "far fa-arrow-alt-circle-right", $entrada->texto);
    }
		return $entrada;
	}

	function get_months($config = array()) {

		$activo = isset($config["activo"]) ? $config["activo"] : 1;
		$destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? $config["filter"] : 0;
		$id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
		$categoria = isset($config["categoria"]) ? $config["categoria"] : "";
		$id_idioma = isset($config["id_idioma"]) ? $config["id_idioma"] : 0;
		$fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
		$fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
		$id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $lang = isset($config["lang"]) ? $config["lang"] : "es";
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    if (isset($config["from_link_categoria"]) && !empty($config["from_link_categoria"])) {
      $cat = $this->get_categoria_by_nombre($config["from_link_categoria"]);
      if ($cat !== FALSE) $from_id_categoria = $cat->id;
    }

		$sql = "SELECT DISTINCT DATE_FORMAT(A.fecha,'%Y-%m') AS aniomes, COUNT(*) AS cantidad ";
		$sql.= "FROM not_entradas A ";
		$sql.= "LEFT JOIN not_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $this->id_empresa ";
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
		if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
		if (!empty($filter)) $sql.= "AND A.titulo LIKE '%$filter%' ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
		if (!empty($categoria)) $sql.= "AND C.link = '$categoria' ";
		if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
		if (!empty($id_idioma)) $sql.= "AND A.id_idioma = $id_idioma ";
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
      if ($lang == "en") {
        switch ($r->mes) {
          case 1: $r->nombre_mes = "January"; break;
          case 2: $r->nombre_mes = "February"; break;
          case 3: $r->nombre_mes = "March"; break;
          case 4: $r->nombre_mes = "April"; break;
          case 5: $r->nombre_mes = "May"; break;
          case 6: $r->nombre_mes = "June"; break;
          case 7: $r->nombre_mes = "July"; break;
          case 8: $r->nombre_mes = "August"; break;
          case 9: $r->nombre_mes = "September"; break;
          case 10: $r->nombre_mes = "October"; break;
          case 11: $r->nombre_mes = "November"; break;
          case 12: $r->nombre_mes = "December"; break;
        }
      } else {
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
      }
			$salida[] = $r; 
		}
		return $salida;
	}


	/**
	 * Obtiene las entradas de un determinado propietario
	 */
	function mis_entradas($id_propietario, $config = array()) {

		$config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
		$config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
		$config["id_propietario"] = $id_propietario;
		return $this->get_list($config);

	}


	/**
	 * Obtiene las entradas destacadas
	 */
	function destacadas($config = array()) {

		$config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
		$config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
		$config["destacado"] = 1;
    $config["order_by"] = "A.nivel_importancia DESC, A.fecha DESC";
		return $this->get_list($config);

	}


	/**
	 * Obtiene las entradas mas vistas en un determinado lapso de tiempo
	 */
	function mas_leidas($config = array()) {
		$config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
		$config["offset"] = isset($config["offset"]) ? $config["offset"] : 4;
		$config["order_by"] = "A.vistos DESC";
		return $this->get_list($config);
	}

  function mas_comentadas($config = array()) {
    $config["order_by"] = "mas_comentadas";
    return $this->get_list($config);
  }

  function mas_interaccion($config = array()) {
    $config["order_by"] = "interaccion DESC ";
    return $this->get_list($config);
  }

	/**
	 * Obtiene las ultimas entradas
	 */
	function ultimas($config = array()) {

		$config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
		$config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
		$config["order"] = "A.fecha DESC ";
		return $this->get_list($config);

	}


	/**
	 * Obtiene las ultimas entradas que tengan videos relacionados
	 */
	function ultimos_videos($config = array()) {

		$config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
		$config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
		$config["tiene_video"] = 1;
		$config["order"] = "A.fecha DESC ";
		return $this->get_list($config);

	}

}
?>