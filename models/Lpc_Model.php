<?php
class Lpc_Model {

  // De la empresa PIVOT obtenemos ciertas configuraciones
  private $id_empresa_pivot = 791;

  private $id_empresa = 0;
  private $ids_empresas = "";
  private $conx = null;
  private $sql = "";
  private $total = 0;

  // NOTAS:
  // articulos.custom_1 tiene la categoria de LPC
  // La tabla ´rubros_lpc´ es una copia de ´rubros´ id_empresa = 791

  function __construct($id_empresa,$conx,$config = array()) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
    // Lo primero que hacemos en la plata construye es obtener todas las empresas que pertenecen al grupo
    $this->get_empresas();
  }

  private function get_empresas() {
    $ids_empresas = array();
    $sql = "SELECT id_empresa FROM web_configuracion WHERE tipo_empresa = 4 ";
    $q = mysqli_query($this->conx,$sql);
    while(($e=mysqli_fetch_object($q))!==NULL) { 
      $ids_empresas[] = $e->id_empresa;
    }
    $ids_empresas = implode(",", $ids_empresas);
    $this->ids_empresas = $ids_empresas;
  }

  function get_variables($config = array()) {
    global $params;
    global $get_params;

    $offset_default = (isset($config["offset"]) ? $config["offset"] : 8);
    $order_default = (isset($config["order"]) ? $config["order"] : 2);
    $vc_preview = ((isset($get_params["preview"]) && $get_params["preview"] == 1) ? 1 : 0);
    $vc_page = 0;

    // Recorremos las categorias
    $vc_categoria = FALSE;
    $vc_categoria_nombre = "";
    $vc_categorias = array();
    $vc_titulo = "Productos";
    $vc_id_categoria = 0;
    $vc_link_pagina = "";
    $vc_id_categoria_padre = 0;
    $vc_link = "productos/";
    for($i=1;$i<(sizeof($params));$i++) {
      $link_categoria = $params[$i];
      $sql = "SELECT * FROM rubros_lpc WHERE link = '".$link_categoria."' ";
      // Esto se hace por si hay diferentes subcategorias con el mismo nombre en diferentes categorias padre
      $sql.= "AND id_padre = $vc_id_categoria_padre ";
      $q = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q)>0) {
        $c = mysqli_fetch_object($q);
        $vc_categoria = $c;
        $vc_id_categoria = $c->id;
        $vc_categorias[] = $c;
        $vc_categoria_nombre = mb_convert_encoding($c->nombre, 'UTF-8', 'ISO-8859-1');
        $vc_id_categoria_padre = $c->id;
        $vc_link.=$link_categoria."/";
        if ($i==1) $vc_link_pagina = $c->link;
      } else {
        // Si el ultimo parametro es un numero, es porque indica el numero de pagina
        if (is_numeric($link_categoria) && ($i == sizeof($params)-1)) {
          $vc_page = (int)$link_categoria;
        } else {
          // La categoria no es valida, directamente redireccionamos
          header("Location: /404.php");
        }
      }
    }
    $vc_link = mklink($vc_link);
    $vc_link_canonico = $vc_link;

    // Ponemos como titulo el nombre de la categoria principal
    if (sizeof($vc_categorias)>0) {
      $cat_ppal = $vc_categorias[0];
      $vc_titulo = $cat_ppal->nombre;
    }

    // -----------------------------------

    // PARAMETROS DE BUSQUEDA

    $vc_precio_maximo = $this->get_precio_maximo(array(
      "from_id_categoria"=>($vc_categoria !== FALSE) ? $vc_categoria->id : 0,
    ));

    // Order
    $vc_order_by = "";
    if (isset($get_params["order"])) { $_SESSION["order1"] = filter_var($get_params["order"],FILTER_SANITIZE_STRING); }
    $vc_order = isset($_SESSION["order1"]) ? $_SESSION["order1"] : $order_default;
    if ($vc_order == "undefined") $vc_order = $order_default;
    if ($vc_order == 0) {
      $vc_order_by = "A.precio_final_dto DESC ";
    } else if ($vc_order == 1) {
      $vc_order_by = "A.precio_final_dto ASC ";
    } else if ($vc_order == 2) {
      $vc_order_by = "A.lista_precios DESC, A.id DESC ";
    }

    $vc_custom_1 = isset($get_params["custom_1"]) ? filter_var($get_params["custom_1"],FILTER_SANITIZE_STRING) : 0;
    $vc_custom_2 = isset($get_params["custom_2"]) ? filter_var($get_params["custom_2"],FILTER_SANITIZE_STRING) : 0;
    $vc_custom_3 = isset($get_params["custom_3"]) ? filter_var($get_params["custom_3"],FILTER_SANITIZE_STRING) : 0;
    $vc_custom_4 = isset($get_params["custom_4"]) ? filter_var($get_params["custom_4"],FILTER_SANITIZE_STRING) : 0;
    $vc_custom_5 = isset($get_params["custom_5"]) ? filter_var($get_params["custom_5"],FILTER_SANITIZE_STRING) : 0;
    $vc_custom_6 = isset($get_params["custom_6"]) ? filter_var($get_params["custom_6"],FILTER_SANITIZE_STRING) : 0;
    $vc_custom_7 = isset($get_params["custom_7"]) ? filter_var($get_params["custom_7"],FILTER_SANITIZE_STRING) : 0; // Ancho neumatico
    $vc_custom_8 = isset($get_params["custom_8"]) ? filter_var($get_params["custom_8"],FILTER_SANITIZE_STRING) : 0; // Alto neumatico
    $vc_custom_9 = isset($get_params["custom_9"]) ? filter_var($get_params["custom_9"],FILTER_SANITIZE_STRING) : 0; // Perfil neumatico
    $vc_filter = isset($get_params["filter"]) ? urldecode(filter_var($get_params["filter"],FILTER_SANITIZE_STRING)) : "";

    // Tipo de vista (listado => 0, grilla => 1)
    if (isset($get_params["view"])) { $_SESSION["view"] = filter_var($get_params["view"],FILTER_SANITIZE_STRING); }
    $vc_view = isset($_SESSION["view"]) ? $_SESSION["view"] : 1;

    // Offset
    if (isset($get_params["offset"])) { $_SESSION["offset"] = filter_var($get_params["offset"],FILTER_SANITIZE_STRING); }
    $vc_offset = isset($_SESSION["offset"]) ? $_SESSION["offset"] : $offset_default;
    if ($vc_offset == "undefined") $vc_offset = $offset_default;

    $vc_etiqueta_link = isset($get_params["label"]) ? filter_var($get_params["label"],FILTER_SANITIZE_STRING) : 0;

    // Minimo
    $vc_minimo = isset($get_params["minimo"]) ? filter_var($get_params["minimo"],FILTER_SANITIZE_STRING) : 0;

    // Maximo
    $vc_maximo = isset($get_params["maximo"]) ? filter_var($get_params["maximo"],FILTER_SANITIZE_STRING) : $vc_precio_maximo;

    // Marcas
    if (isset($get_params["m"])) {
      $vc_in_marcas = str_replace("-", ",", filter_var($get_params["m"],FILTER_SANITIZE_STRING));
      $vc_marcas_seleccionadas = explode(",", $in_marcas);
    } else {
      $vc_in_marcas = "";
      $vc_marcas_seleccionadas = array();
    }

    // Si tiene descuento
    $vc_desc1 = isset($get_params["desc1"]) ? filter_var($get_params["desc1"],FILTER_SANITIZE_STRING) : 0;
    $vc_desc2 = isset($get_params["desc2"]) ? filter_var($get_params["desc2"],FILTER_SANITIZE_STRING) : 0;
    $vc_desc3 = isset($get_params["desc3"]) ? filter_var($get_params["desc3"],FILTER_SANITIZE_STRING) : 0;
    $vc_desc4 = isset($get_params["desc4"]) ? filter_var($get_params["desc4"],FILTER_SANITIZE_STRING) : 0;
    $vc_desc5 = isset($get_params["desc5"]) ? filter_var($get_params["desc5"],FILTER_SANITIZE_STRING) : 0;
    $vc_desc6 = isset($get_params["desc6"]) ? filter_var($get_params["desc6"],FILTER_SANITIZE_STRING) : 0;

    $config = array(
      "from_id_categoria"=>(($vc_categoria !== FALSE)?$vc_categoria->id:0),
      "custom_7"=>$vc_custom_7,
      "custom_8"=>$vc_custom_8,
      "custom_9"=>$vc_custom_9,
      "filter"=>$vc_filter,
      "in_marcas"=>$vc_in_marcas,
      "minimo"=>$vc_minimo,
      "maximo"=>$vc_maximo,
      "tiene_descuento_1"=>$vc_desc1,
      "tiene_descuento_2"=>$vc_desc2,
      "tiene_descuento_3"=>$vc_desc3,
      "tiene_descuento_4"=>$vc_desc4,
      "tiene_descuento_5"=>$vc_desc5,
      "tiene_descuento_6"=>$vc_desc6,
      "limit"=>($vc_page * $vc_offset),
      "offset"=>$vc_offset,
      "order_by"=>$vc_order_by,
      "activo_categorias"=>1,
      "etiqueta_link"=>$vc_etiqueta_link,
    );
    $s_params = (!empty($get_params)) ? "?".http_build_query($get_params) : "";
    $listado = $this->get_list($config);

    $total = $this->get_total_results();
    $total_paginas = ceil($total / $vc_offset);

    // Registramos la busqueda
    if (($vc_preview==0) && (!empty($vc_filter) || !empty($vc_custom_7) || !empty($vc_custom_8) || !empty($vc_custom_9))) {
      $this->guardar_busqueda(array(
        "palabra"=>$vc_filter,
        "custom_7"=>$vc_custom_7,
        "custom_8"=>$vc_custom_8,
        "custom_9"=>$vc_custom_9,
      ));
    }

    return array(
      "vc_listado"=>$listado,
      "vc_offset"=>$vc_offset,
      "vc_page"=>$vc_page,
      "vc_total_resultados"=>$total,
      "vc_total_paginas"=>$total_paginas,
      "vc_id_categoria"=>$vc_id_categoria,
      "vc_categoria"=>$vc_categoria,
      "vc_categoria_nombre"=>$vc_categoria_nombre,
      "vc_categorias"=>$vc_categorias,
      "vc_titulo"=>$vc_titulo,
      "vc_preview"=>$vc_preview,
      "vc_view"=>$vc_view,
      "vc_link"=>$vc_link,
      "vc_link_canonico"=>$vc_link_canonico,
      "vc_etiqueta_link"=>$vc_etiqueta_link,
      "vc_order"=>$vc_order,
      "vc_order_by"=>$vc_order_by,
      "vc_in_marcas"=>$vc_in_marcas,
      "vc_precio_maximo"=>$vc_precio_maximo,
      "vc_maximo"=>$vc_maximo,
      "vc_minimo"=>$vc_minimo,      
      "vc_params"=>$s_params,
      "vc_filter"=>$vc_filter,
      "vc_custom_1"=>$vc_custom_1,
      "vc_custom_2"=>$vc_custom_2,
      "vc_custom_3"=>$vc_custom_3,
      "vc_custom_4"=>$vc_custom_4,
      "vc_custom_5"=>$vc_custom_5,
      "vc_custom_6"=>$vc_custom_6,
      "vc_custom_7"=>$vc_custom_7,
      "vc_custom_8"=>$vc_custom_8,
      "vc_desc_1"=>$vc_desc1,
      "vc_desc_2"=>$vc_desc2,
      "vc_desc_3"=>$vc_desc3,
      "vc_desc_4"=>$vc_desc4,
      "vc_desc_5"=>$vc_desc5,
      "vc_desc_6"=>$vc_desc6,
    );
  }

  function get_barra_producto($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa_pivot;
    $sql = "SELECT BP.*, E.logo AS empresa_path, E.nombre AS empresa, ";
    $sql.= " IF(R.id IS NULL,'',R.nombre) AS rubro, ";
    $sql.= " IF(R.id IS NULL,'',R.path) AS rubro_path, ";
    $sql.= " IF(M.id IS NULL,'',M.nombre) AS marca, ";
    $sql.= " IF(M.id IS NULL,'',M.path) AS marca_path ";
    $sql.= "FROM web_barras_productos BP ";
    $sql.= "INNER JOIN empresas E ON (BP.id_empresa = E.id) ";
    $sql.= "LEFT JOIN rubros_lpc R ON (BP.id_rubro = R.id) ";
    $sql.= "LEFT JOIN marcas M ON (BP.id_marca = M.id AND BP.id_empresa = M.id_empresa) ";
    $sql.= "WHERE BP.id_empresa = $this->id_empresa ";
    $sql.= "ORDER BY BP.orden ASC ";
    $q_barras = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($barra=mysqli_fetch_object($q_barras))!==NULL) { 
      $config_lista = array(
        "from_id_categoria"=>$barra->id_rubro,
        "id_marca"=>$barra->id_marca,
        "offset"=>$barra->total_productos,
        "order_by"=>(($barra->aleatorio == 1)?"RAND() ASC":"A.lista_precios DESC"),
      );
      if (isset($config_lista["tiene_descuento_1"])) $config_lista["tiene_descuento_1"] = 1;
      $barra->lista = $this->get_list($config_lista);
      $barra->nombre = mb_convert_encoding($barra->nombre, 'UTF-8', 'ISO-8859-1');
      $barra->subtitulo = mb_convert_encoding($barra->subtitulo, 'UTF-8', 'ISO-8859-1');
      $barra->rubro_path = (!empty($barra->rubro_path)) ? (((strpos($barra->rubro_path,"http://")===FALSE)) ? "/sistema/".$barra->rubro_path : $barra->rubro_path) : "";
      $barra->marca_path = (!empty($barra->marca_path)) ? (((strpos($barra->marca_path,"http://")===FALSE)) ? "/sistema/".$barra->marca_path : $barra->marca_path) : "";
      $salida[] = $barra;
    }
    return $salida;
  }

  function guardar_busqueda($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa_pivot;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s");
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_rubro = isset($config["id_rubro"]) ? $config["id_rubro"] : 0;
    $id_marca = isset($config["id_marca"]) ? $config["id_marca"] : 0;
    $id_etiqueta = isset($config["id_etiqueta"]) ? $config["id_etiqueta"] : 0;
    $precio_minimo = isset($config["precio_minimo"]) ? $config["precio_minimo"] : 0;
    $precio_maximo = isset($config["precio_maximo"]) ? $config["precio_maximo"] : 0;
    $custom_7 = isset($config["custom_7"]) ? $config["custom_7"] : "";
    $custom_8 = isset($config["custom_8"]) ? $config["custom_8"] : "";
    $custom_9 = isset($config["custom_9"]) ? $config["custom_9"] : "";
    $palabra = isset($config["palabra"]) ? $config["palabra"] : "";
    $link = isset($config["link"]) ? $config["link"] : current_url()."&preview=1";
    $sql = "INSERT INTO busquedas (";
    $sql.= "id_empresa,fecha,id_cliente,id_rubro,id_marca,id_etiqueta,precio_minimo,precio_maximo,palabra,custom_7,custom_8,custom_9,link";
    $sql.= ") VALUES (";
    $sql.= "'$id_empresa','$fecha','$id_cliente','$id_rubro','$id_marca','$id_etiqueta','$precio_minimo','$precio_maximo','$palabra','$custom_7','$custom_8','$custom_9','$link'";
    $sql.= ")";
    mysqli_query($this->conx,$sql);
  }

  function get_punto_venta_web($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    // Buscamos el punto de venta asociado con la web
    $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
    $sql.= "FROM puntos_venta PV INNER JOIN web_configuracion CONF ON (PV.id = CONF.id_punto_venta AND PV.id_empresa = CONF.id_empresa) ";
    $sql.= "LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
    $sql.= "WHERE PV.id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $pv = mysqli_fetch_object($q);
      return $pv;
    }
    return FALSE;
  }

  function consultar_stock($id_articulo,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    if ($id_sucursal == 0) {
      // Tomamos el PV por defecto de la web
      $pv = $this->get_punto_venta_web(array(
        "id_empresa"=>$id_empresa
      ));
      if ($pv !== FALSE) $id_sucursal = $pv->id_sucursal;
    }
    $sql = "SELECT IF(SUM(stock_actual) IS NULL,0,SUM(stock_actual)) AS stock_actual ";
    $sql.= "FROM stock ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_articulo = $id_articulo ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) { 
      $r = mysqli_fetch_object($q);
      return $r->stock_actual;
    } else {
      return 0;
    }
  }

  function get_precio_maximo($config=array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->ids_empresas;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;

    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    if ($from_id_categoria != 0) {
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
    }

    // Cotizacion del dolar
    $cotizacion = $this->get_cotizacion('U$D');

    $sql = 'SELECT IF(MAX(precio_final) IS NULL,0,MAX(precio_final)) AS maximo ';
    $sql.= "FROM articulos ";
    $sql.= "WHERE id_empresa IN ($id_empresa) ";
    $sql.= 'AND moneda = "U$S" ';
    if ($id_categoria != 0) $sql.= "AND custom_1 = ".$id_categoria;
    if ($from_id_categoria != 0) $sql.= "AND custom_1 IN ($ids_categorias) ";
    $q_maximo_usd = mysqli_query($this->conx,$sql);
    $r_maximo_usd = mysqli_fetch_object($q_maximo_usd);
    $maximo_usd = (ceil($r_maximo_usd->maximo * $cotizacion/100)*100);

    $sql = 'SELECT IF(MAX(precio_final) IS NULL,0,MAX(precio_final)) AS maximo ';
    $sql.= "FROM articulos ";
    $sql.= "WHERE id_empresa IN ($id_empresa) ";
    $sql.= 'AND moneda = "$" ';
    if ($id_categoria != 0) $sql.= "AND custom_1 = ".$id_categoria;
    if ($from_id_categoria != 0) $sql.= "AND custom_1 IN ($ids_categorias) ";
    $q_maximo_pe = mysqli_query($this->conx,$sql);
    $r_maximo_pe = mysqli_fetch_object($q_maximo_pe);
    $maximo_pe = $r_maximo_pe->maximo;
    return max($maximo_usd,$maximo_pe);
  }

  function get_propiedades($config = array()) {
    $id_propiedad = isset($config["id_propiedad"]) ? $config["id_propiedad"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $sql = "SELECT * ";
    $sql.= "FROM articulos_propiedades P ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if ($id_propiedad != 0) $sql.= "AND id = $id_propiedad ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $sql = "SELECT APO.* ";
      $sql.= "FROM articulos_propiedades_opciones APO ";
      $sql.= "WHERE APO.id_empresa = $id_empresa ";
      $sql.= "AND APO.id_propiedad = $r->id ";
      // Si hay algun producto que las usa
      $sql.= "AND EXISTS (SELECT AV.* FROM articulos_variantes AV WHERE (APO.id = AV.id_opcion_1 OR APO.id = AV.id_opcion_2 OR APO.id = AV.id_opcion_3) AND APO.id_empresa = AV.id_empresa ) ";
      $qq = mysqli_query($this->conx,$sql);
      $r->opciones = array();
      while(($rr=mysqli_fetch_object($qq))!==NULL) $r->opciones[] = $rr;

      // Solamente agregamos aquellas que tengan opciones
      if (sizeof($r->opciones)>0) $salida[] = $r;
    }
    return $salida;
  }

  function get_marcas($config = array()) {

    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    $tiene_productos = isset($config["tiene_productos"]) ? $config["tiene_productos"] : 0;
    $tiene_imagen = isset($config["tiene_imagen"]) ? $config["tiene_imagen"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;

    // Tomamos las marcas de todos los articulos que pertenecen a una determinada categoria padre
    if (!empty($from_id_categoria)) {
      $sql = "SELECT DISTINCT M.id, M.nombre, M.path ";
      $sql.= "FROM articulos A INNER JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
      $sql.= "WHERE A.lista_precios > 1 AND A.id_empresa = $id_empresa ";
      	// A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
      if ($tiene_imagen == 1) $sql.= "AND A.path != '' ";
      $sql.= "AND A.custom_1 IN ($ids_categorias) ";
      $sql.= "ORDER BY M.orden ASC, M.nombre ASC ";

    // Tomamos las marcas directamente, sin relacionarlas con los articulos
    } else {
      $sql = "SELECT A.* FROM marcas A ";
      $sql.= "WHERE A.id_empresa = $id_empresa ";
      if (!empty($tiene_productos)) $sql.= "AND EXISTS (SELECT 1 FROM articulos ART WHERE ART.id_empresa = A.id_empresa AND ART.id_marca = A.id AND ART.lista_precios > 1) ";
      if ($tiene_imagen == 1) $sql.= "AND A.path != '' ";
      // $sql.= "AND A.activo = 1 ";
      $sql.= "ORDER BY A.orden ASC, A.nombre ASC ";
    }

    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r->path = (!empty($r->path)) ? (((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path) : "";
      $r->path = str_replace(" ","",$r->path);
      $salida[] = $r;
    }
    return $salida;
  }


  function get_promociones($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;

    // Tomamos las promociones de todos los articulos que pertenecen a una determinada categoria padre
    if (!empty($from_id_categoria)) {
      $sql = "SELECT DISTINCT M.id, M.nombre, M.path, M.link ";
      $sql.= "FROM articulos A INNER JOIN promociones M ON (A.id_promocion = M.id AND A.id_empresa = M.id_empresa) ";
      $sql.= "WHERE A.lista_precios > 1 AND A.id_empresa = $id_empresa ";
      // A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria);
      $ids_categorias[] = $from_id_categoria;
      $ids_categorias = implode(",", $ids_categorias);
      $sql.= "AND A.custom_1 IN ($ids_categorias) ";
      $sql.= "AND M.activo = 1 ";
      $sql.= "ORDER BY M.nombre ASC ";

      // Tomamos las marcas directamente, sin relacionarlas con los articulos
      } else {

        $sql = "SELECT * FROM promociones A ";
        $sql.= "WHERE A.id_empresa = $id_empresa ";
        $sql.= "AND A.activo = 1 ";
      }

      $q = mysqli_query($this->conx,$sql);
      $salida = array();
      while(($r=mysqli_fetch_object($q))!==NULL) {
        $r->path = (!empty($r->path)) ? (((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path) : "";
        $r->path = str_replace(" ","",$r->path);
        $salida[] = $r;
      }
      return $salida;
  }


  // Obtenemos los datos del articulo
  function get($id,$config = array()) {

    if ($id == 0) return FALSE;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $buscar_relacionados = isset($config["buscar_relacionados"]) ? $config["buscar_relacionados"] : 0;
    $relacionados_offset = isset($config["relacionados_offset"]) ? $config["relacionados_offset"] : 6;
    $buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;
    $buscar_clientes = isset($config["buscar_clientes"]) ? $config["buscar_clientes"] : 0;
    $buscar_variantes = isset($config["buscar_variantes"]) ? $config["buscar_variantes"] : 1;
    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 0;
    $buscar_ingredientes = isset($config["buscar_ingredientes"]) ? $config["buscar_ingredientes"] : 0;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $lista_precios = isset($config["lista_precios"]) ? $config["lista_precios"] : $this->get_lista_precios();
    $tipo_envio = isset($config["tipo_envio"]) ? $config["tipo_envio"] : -1;
    $consultar_stock = isset($config["consultar_stock"]) ? $config["consultar_stock"] : 1;
    $tipo_envio_comparacion = isset($config["tipo_envio_comparacion"]) ? $config["tipo_envio_comparacion"] : "=";
    $mostrar_precio_neto = $this->usar_precio_neto();

    // Si no se envia un parametro, tomamos la configuracion guardada
    $convertir_moneda = isset($config["convertir_moneda"]) ? $config["convertir_moneda"] : $this->get_moneda_tienda();

    $sql = "SELECT A.*, ";
    $sql.= " E.nombre AS empresa, E.logo AS empresa_path, ";
    $sql.= " IF(R.nombre IS NULL,'',R.nombre) AS rubro, ";
    $sql.= " IF(PRO.nombre IS NULL,'',PRO.nombre) AS promocion, ";
    $sql.= " IF(PRO.link IS NULL,'',PRO.link) AS promocion_link, ";
    $sql.= " IF(PRO.path IS NULL,'',PRO.path) AS promocion_path, ";
    $sql.= " IF(M.nombre IS NULL,'',M.nombre) AS marca, ";
    $sql.= " IF(M.path IS NULL,'',M.path) AS marca_path ";
    $sql.= "FROM articulos A ";
    $sql.= " INNER JOIN empresas E ON (A.id_empresa = E.id) ";  
    $sql.= " LEFT JOIN rubros_lpc R ON (A.custom_1 = R.id) ";
    $sql.= " LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
    $sql.= " LEFT JOIN promociones PRO ON (A.id_promocion = PRO.id AND A.id_empresa = PRO.id_empresa AND PRO.activo = 1) ";
    $sql.= "WHERE A.id = $id ";
    //$sql.= "AND A.id_empresa = $id_empresa ";

    if ($activo == 1) $sql.= "AND A.lista_precios > 1 ";
    else if ($activo == 0) $sql.= "AND A.lista_precios <= 1 ";

    if ($tipo_envio != -1) $sql.= "AND A.no_totalizar_reparto $tipo_envio_comparacion $tipo_envio ";
    if (!empty($id_cliente)) {
      $sql.= "AND EXISTS (SELECT * FROM articulos_clientes ART_CLI WHERE ART_CLI.id_articulo = A.id AND ART_CLI.id_empresa = A.id_empresa AND ART_CLI.id_cliente = $id_cliente) ";
    }
    $this->sql = $sql;
    $q = mysqli_query($this->conx,$sql);

    if ($q === FALSE) {
      error_mail($this->sql);
      return FALSE;
    }

    if (mysqli_num_rows($q)<=0) return FALSE;
    $producto = mysqli_fetch_object($q);
    $producto = $this->encoding($producto);
    $producto->path = (!empty($producto->path)) ? (((strpos($producto->path,"http://")===FALSE)) ? "/sistema/".$producto->path : $producto->path) : "";
    $producto->path = str_replace(" ","",$producto->path);
    $producto->marca_path = (!empty($producto->marca_path)) ? (((strpos($producto->marca_path,"http://")===FALSE)) ? "/sistema/".$producto->marca_path : $producto->marca_path) : "";
    $producto->marca_path = str_replace(" ","",$producto->marca_path);
    $producto->images = array();
    $producto->propiedades = array();
    $producto->variantes = array();
    $producto->variantes_images = array();

    $producto = $this->usar_lista_precios($producto,$lista_precios);

    // Si tenemos que mostrar los precios netos, en vez de finales
    if ($mostrar_precio_neto == 1) {
      $producto->precio_final = $producto->precio_neto;
      $producto->precio_final_dto = $producto->precio_neto * ((100-$producto->porc_bonif)/100);
    }

    // Obtenemos sus fotos
    if ($buscar_imagenes == 1) {
      $sql = "SELECT * FROM articulos_images WHERE id_articulo = $producto->id AND id_empresa = $producto->id_empresa ORDER BY orden ASC ";
      $q_fotos = mysqli_query($this->conx,$sql);
      while(($foto = mysqli_fetch_object($q_fotos))!==NULL) {
        $foto->path = ((strpos($foto->path,"http://")===FALSE)) ? "/sistema/".$foto->path : $foto->path;
        $foto->path = str_replace(" ","",$foto->path);
        $producto->images[] = $foto;
      }
    }

    $producto->clientes = array();
    if ($buscar_clientes == 1) {
      $sql = "SELECT C.*, AC.id_cliente, AC.id_articulo ";
      $sql.= "FROM articulos_clientes AC ";
      $sql.= "INNER JOIN clientes C ON (AC.id_empresa = C.id_empresa AND AC.id_cliente = C.id) ";
      $sql.= "WHERE AC.id_empresa = $producto->id_empresa AND AC.id_articulo = $producto->id ";
      $q_clientes = mysqli_query($this->conx,$sql);
      while(($cli = mysqli_fetch_object($q_clientes))!==NULL) {
        $producto->clientes[] = $cli;
      }
    }

    $producto->ingredientes = array();
    if ($buscar_ingredientes == 1) {
      // Obtenemos los ingredientes de ese producto
      $sql = "SELECT E.nombre, E.valores, E.adicional ";
      $sql.= "FROM articulos_ingredientes E ";
      $sql.= "WHERE E.id_articulo = $producto->id AND E.id_empresa = $id_empresa ";
      $sql.= "ORDER BY E.orden ASC ";
      $q_ingredientes = mysqli_query($this->conx,$sql);
      while(($ing = mysqli_fetch_object($q_ingredientes))!==NULL) {
        $producto->ingredientes[] = $ing;
      } 
    }

    $producto->relacionados = array();
    if ($buscar_relacionados == 1) {
      // TODO: Buscamos otra nueva forma de hacer relacionados
    }

    $producto->etiquetas = array();
    if ($buscar_etiquetas == 1) {
      // Obtenemos las etiquetas de esa entrada
      $sql = "SELECT E.* FROM articulos_etiquetas_relacionadas EE ";
      $sql.= " INNER JOIN articulos_etiquetas E ON (E.id = EE.id_etiqueta AND E.id_empresa = EE.id_empresa) ";
      $sql.= "WHERE EE.id_articulo = $id AND E.id_empresa = $producto->id_empresa ";
      $sql.= "ORDER BY EE.orden ASC, E.nombre ASC ";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        if (isset($r->nombre)) $r->nombre = mb_convert_encoding($r->nombre, 'UTF-8', 'ISO-8859-1');
        $producto->etiquetas[] = $r;
      }
    }

    // Si tenemos que consultar el stock
    if ($consultar_stock == 1) {
      $producto->stock = $this->consultar_stock($producto->id,array(
        "id_empresa"=>$producto->id_empresa,
      ));
    }

    if ($buscar_variantes == 1) {

      // Obtenemos las variantes del articulo
      $sql = "SELECT AV.*, ";
      $sql.= " IF(APO_1.nombre IS NULL,'',APO_1.nombre) AS nombre_opcion_1, ";
      $sql.= " IF(APO_2.nombre IS NULL,'',APO_2.nombre) AS nombre_opcion_2, ";
      $sql.= " IF(APO_3.nombre IS NULL,'',APO_3.nombre) AS nombre_opcion_3, ";
      $sql.= " IF(APO_1.etiqueta IS NULL,'',APO_1.etiqueta) AS etiqueta_opcion_1, ";
      $sql.= " IF(APO_2.etiqueta IS NULL,'',APO_2.etiqueta) AS etiqueta_opcion_2, ";
      $sql.= " IF(APO_3.etiqueta IS NULL,'',APO_3.etiqueta) AS etiqueta_opcion_3, ";
      $sql.= " IF(AP_1.nombre IS NULL,'',AP_1.nombre) AS nombre_propiedad_1, ";
      $sql.= " IF(AP_2.nombre IS NULL,'',AP_2.nombre) AS nombre_propiedad_2, ";
      $sql.= " IF(AP_3.nombre IS NULL,'',AP_3.nombre) AS nombre_propiedad_3, ";
      $sql.= " IF(AP_1.id IS NULL,0,AP_1.id) AS id_propiedad_1, ";
      $sql.= " IF(AP_2.id IS NULL,0,AP_2.id) AS id_propiedad_2, ";
      $sql.= " IF(AP_3.id IS NULL,0,AP_3.id) AS id_propiedad_3 ";
      $sql.= "FROM articulos_variantes AV ";
      $sql.= " LEFT JOIN articulos_propiedades_opciones APO_1 ON (AV.id_opcion_1 = APO_1.id AND AV.id_empresa = APO_1.id_empresa) ";
      $sql.= " LEFT JOIN articulos_propiedades AP_1 ON (APO_1.id_propiedad = AP_1.id AND (AV.id_empresa = AP_1.id_empresa OR AP_1.id_empresa = 0) ) ";
      $sql.= " LEFT JOIN articulos_propiedades_opciones APO_2 ON (AV.id_opcion_2 = APO_2.id AND AV.id_empresa = APO_2.id_empresa) ";
      $sql.= " LEFT JOIN articulos_propiedades AP_2 ON (APO_2.id_propiedad = AP_2.id AND (AV.id_empresa = AP_2.id_empresa OR AP_2.id_empresa = 0) ) ";
      $sql.= " LEFT JOIN articulos_propiedades_opciones APO_3 ON (AV.id_opcion_3 = APO_3.id AND AV.id_empresa = APO_3.id_empresa) ";
      $sql.= " LEFT JOIN articulos_propiedades AP_3 ON (APO_3.id_propiedad = AP_3.id AND (AV.id_empresa = AP_3.id_empresa OR AP_3.id_empresa = 0) ) ";
      $sql.= "WHERE AV.id_articulo = $id ";
      $sql.= "AND AV.id_empresa = $producto->id_empresa ";
      $sql.= "ORDER BY AV.id ASC ";
      $q = mysqli_query($this->conx,$sql);

      $pv_web = $this->get_punto_venta_web();
      if ($pv_web !== FALSE) {
        while(($r=mysqli_fetch_object($q))!==NULL) {
          $r->nombre = mb_convert_encoding($r->nombre, 'UTF-8', 'ISO-8859-1');
          $r->etiqueta_opcion_1 = mb_convert_encoding($r->etiqueta_opcion_1, 'UTF-8', 'ISO-8859-1');
          $r->etiqueta_opcion_2 = mb_convert_encoding($r->etiqueta_opcion_2, 'UTF-8', 'ISO-8859-1');
          $r->etiqueta_opcion_3 = mb_convert_encoding($r->etiqueta_opcion_3, 'UTF-8', 'ISO-8859-1');

          // Buscamos el stock para esa variante
          $sql = "SELECT * ";
          $sql.= "FROM stock_variantes ";
          $sql.= "WHERE id_empresa = $producto->id_empresa ";
          $sql.= "AND id_articulo = $id ";
          $sql.= "AND id_sucursal = $pv_web->id_sucursal ";
          $sql.= "AND id_variante = $r->id ";
          $q_stock = mysqli_query($this->conx,$sql);
          if (mysqli_num_rows($q_stock)>0) {
            $r_stock = mysqli_fetch_object($q_stock);
            $r->stock = $r_stock->stock_actual;
          } else {
            $r->stock = 0;
          }

          $producto->variantes[] = $r;
          if (!empty($r->path)) {
            $o = new stdClass();
            $o->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
            $o->path = str_replace(" ","",$o->path);
            $o->id_propiedad_1 = $r->id_propiedad_1;
            $o->id_propiedad_2 = $r->id_propiedad_2;
            $o->id_propiedad_3 = $r->id_propiedad_3;
            $o->id_opcion_1 = $r->id_opcion_1;
            $o->id_opcion_2 = $r->id_opcion_2;
            $o->id_opcion_3 = $r->id_opcion_3;
            $producto->variantes_images[] = $o;
          }
        }
      }

      // Agrupamos las variantes segun las propiedades
      if (sizeof($producto->variantes)>0) {
        foreach($producto->variantes as $v) {
          for($i=1;$i<=3;$i++) { 
            if ($v->{"id_propiedad_$i"} != 0) {
              if (!isset($producto->propiedades[$v->{"id_propiedad_$i"}])) {
                $producto->propiedades[$v->{"id_propiedad_$i"}] = array(
                  "nombre"=>$v->{"nombre_propiedad_$i"},
                  "opciones"=>array(
                    array(
                      "id"=>$v->{"id_opcion_$i"},
                      "nombre"=>$v->{"nombre_opcion_$i"},
                      "etiqueta"=>($v->{"etiqueta_opcion_$i"}),
                    ),
                  ),
                );
              } else {

                // Primero buscamos si no existe
                $encontro = FALSE;
                foreach($producto->propiedades[$v->{"id_propiedad_$i"}]["opciones"] as $r) {
                  if ($r["id"] == $v->{"id_opcion_$i"}) {
                    $encontro = TRUE; break;
                  }
                }
                if (!$encontro) { 
                  $producto->propiedades[$v->{"id_propiedad_$i"}]["opciones"][] = array(
                    "id"=>$v->{"id_opcion_$i"},
                    "nombre"=>$v->{"nombre_opcion_$i"},
                    "etiqueta"=>($v->{"etiqueta_opcion_$i"}),
                  );
                }
                
              }
            }
          }
        }
      }

    }

    if (!empty($convertir_moneda) && $convertir_moneda != $producto->moneda) {
      $cotizacion = $this->get_cotizacion($producto->moneda);
      $producto = $this->multiplicar_cotizacion($cotizacion,$producto);
      $producto->moneda = $convertir_moneda;
    }

    return $producto;
  }

  // Mueve la lista de precios a la pasada por parametro
  private function usar_lista_precios($producto,$lista_precios) {
    if ($lista_precios == 2) {
      // Usamos lista de precios 2
      $producto->precio_neto = $producto->precio_neto_2;
      $producto->precio_final = $producto->precio_final_2;
      $producto->porc_ganancia = $producto->porc_ganancia_2;
      $producto->porc_bonif = $producto->porc_bonif_2;
      $producto->precio_final_dto = $producto->precio_final_dto_2;
    } else if ($lista_precios == 3) {
      // Usamos lista de precios 3
      $producto->precio_neto = $producto->precio_neto_3;
      $producto->precio_final = $producto->precio_final_3;
      $producto->porc_ganancia = $producto->porc_ganancia_3;
      $producto->porc_bonif = $producto->porc_bonif_3;
      $producto->precio_final_dto = $producto->precio_final_dto_3;        
    } else if ($lista_precios == 4) {
      // Usamos lista de precios 4
      $producto->precio_neto = $producto->precio_neto_4;
      $producto->precio_final = $producto->precio_final_4;
      $producto->porc_ganancia = $producto->porc_ganancia_4;
      $producto->porc_bonif = $producto->porc_bonif_4;
      $producto->precio_final_dto = $producto->precio_final_dto_4;        
    } else if ($lista_precios == 5) {
      // Usamos lista de precios 5
      $producto->precio_neto = $producto->precio_neto_5;
      $producto->precio_final = $producto->precio_final_5;
      $producto->porc_ganancia = $producto->porc_ganancia_5;
      $producto->porc_bonif = $producto->porc_bonif_5;
      $producto->precio_final_dto = $producto->precio_final_dto_5;        
    } else if ($lista_precios == 6) {
      // Usamos lista de precios 6
      $producto->precio_neto = $producto->precio_neto_6;
      $producto->precio_final = $producto->precio_final_6;
      $producto->porc_ganancia = $producto->porc_ganancia_6;
      $producto->porc_bonif = $producto->porc_bonif_6;
      $producto->precio_final_dto = $producto->precio_final_dto_6;        
    }
    return $producto;
  }

  // Busca en la configuracion si usa precio NETO o FINAL (por defecto)
  function usar_precio_neto($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $mostrar_precio_neto = 0;
    $sql = "SELECT articulo_mostrar_precio_neto FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $web_conf = mysqli_fetch_object($q);
      $mostrar_precio_neto = $web_conf->articulo_mostrar_precio_neto;
    }
    return $mostrar_precio_neto;
  }

  // Busca en la configuracion cual es la lista de precios que debemos usar
  function get_lista_precios($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $lista_precios = 0;
    $sql = "SELECT tienda_lista_precios FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $web_conf = mysqli_fetch_object($q);
      $lista_precios = $web_conf->tienda_lista_precios;
    }
    return $lista_precios;
  }

  function guardar_articulo_meli($array = array()) {
    $campos = array();
    $valores = array();
    foreach ($array as $key => $value) {
      $campos[] = $key;
      $valores = "'$value'";
    }
    $sql = "INSERT INTO articulos_meli (";
    $sql.= implode(",", $campos);
    $sql.= ") VALUES (";
    $sql.= implode(",", $valores);
    $sql.= ")";
    mysqli_query($this->conx,$sql);
  }

  function get_categoria($id,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa_pivot;
    $result = array();
    $sql = "SELECT * FROM rubros_lpc WHERE id = $id ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)==0) return FALSE;
    $row=mysqli_fetch_object($q);
    $row->nombre = mb_convert_encoding($row->nombre, 'UTF-8', 'ISO-8859-1');
    $row->subtitulo = mb_convert_encoding($row->subtitulo, 'UTF-8', 'ISO-8859-1');
    return $row;
  }

  function get_categoria_by_link($link,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa_pivot;
    $result = array();
    $sql = "SELECT * FROM rubros_lpc WHERE link = '$link' ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)==0) return FALSE;
    $row=mysqli_fetch_object($q);
    $row->nombre = mb_convert_encoding($row->nombre, 'UTF-8', 'ISO-8859-1');
    $row->subtitulo = mb_convert_encoding($row->subtitulo, 'UTF-8', 'ISO-8859-1');
    return $row;
  }

  function get_main_categories($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa_pivot;
    $id_padre = isset($config["id_padre"]) ? $config["id_padre"] : 0; // Si es -1, este parametro se anula
    $destacado = isset($config["destacado"]) ? $config["destacado"] : -1;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 0;
    $result = array();
    $sql = "SELECT * FROM rubros_lpc WHERE 1=1 ";
    if ($id_padre != -1) $sql.= "AND id_padre = $id_padre ";
    if ($destacado != -1) $sql.= "AND destacado = $destacado ";
    $sql.= "ORDER BY orden ASC ";
    if ($offset > 0) $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    while(($row=mysqli_fetch_object($q))!==NULL) { 
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = 0;
      $e->nombre = mb_convert_encoding($row->nombre, 'UTF-8', 'ISO-8859-1');
      $e->subtitulo = mb_convert_encoding($row->subtitulo, 'UTF-8', 'ISO-8859-1');
      $e->link = $row->link;
      $result[] = $e;
    }
    return $result;		
  }

  function get_subcategorias($id_categoria_padre,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa_pivot;
    $ids_empresas = isset($config["ids_empresas"]) ? $config["ids_empresas"] : $this->ids_empresas;
    $activo = isset($config["activo"]) ? $config["activo"] : -1;
    $con_imagen = isset($config["con_imagen"]) ? $config["con_imagen"] : -1;
    $fija = isset($config["fija"]) ? $config["fija"] : -1;
    $offset = isset($config["offset"]) ? $config["offset"] : 9999;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $profundidad = isset($config["profundidad"]) ? $config["profundidad"] : 0;
    $tiene_productos = isset($config["tiene_productos"]) ? $config["tiene_productos"] : 0;
    $tiene_productos_destacados = isset($config["tiene_productos_destacados"]) ? $config["tiene_productos_destacados"] : 0;
    $sql = "SELECT * FROM rubros_lpc A ";
    $sql.= "WHERE A.id_padre = $id_categoria_padre ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($con_imagen == 1) $sql.= "AND A.path != '' ";
    $sql.= "ORDER BY orden ASC ";
    $sql.= "LIMIT $limit, $offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    if ($profundidad == 4) return $salida;
    if (mysqli_num_rows($q)>0) {
      while(($r=mysqli_fetch_object($q))!==NULL) {

        $profundidad++;
        $config["profundidad"] = $profundidad;

        $r->children = $this->get_subcategorias($r->id,$config);
        //print_r($r->children);
        //echo "<br/><br/>";

        // Si es una categoria hoja, y hay que fijarse si tiene productos
        if (sizeof($r->children) == 0 && $tiene_productos == 1) {

          $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
          $sql.= "FROM articulos AA ";
          $sql.= "WHERE AA.custom_1 = $r->id ";
          $sql.= "AND AA.id_empresa IN ($ids_empresas) ";
          $sql.= "AND AA.lista_precios > 1 ";
          if ($tiene_productos_destacados == 1) $sql.= "AND AA.lista_precios > 2 ";
          $qq = mysqli_query($this->conx,$sql);
          $cant_children = mysqli_fetch_object($qq);
          if ($cant_children->cantidad > 0) {
            $salida[] = $r;
          }

        } else {
          $salida[] = $r;  
        }
      }
    }
    return $salida;
  }

	// Devuelve un array con todos los IDS de todos los descendientes de esa categoria padre
  function get_ids_subcategorias($id_categoria_padre,$config=array()) {
    $salida = array();
    $s = $this->get_subcategorias($id_categoria_padre,$config);
    foreach($s as $r) {
      $salida[] = $r->id;
      if (isset($r->children) && sizeof($r->children)>0) {
        foreach($r->children as $rr) {
          $salida[] = $rr->id;
          if (isset($rr->children) && sizeof($rr->children)>0) {
            foreach($rr->children as $rrr) {
              $salida[] = $rrr->id;
              if (isset($rrr->children) && sizeof($rrr->children)>0) {
                foreach($rrr->children as $rrrr) {
                  $salida[] = $rrrr->id;
                }
              }
            }
          }
        }
      }
    }
		$salida[] = $id_categoria_padre; // Incluimos el padre
		return $salida;
	}

	function get_categorias($id_categoria,$config = array()) {
		$link = isset($config["link"]) ? $config["link"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa_pivot;
		$separador_nombre = isset($config["separador_nombre"]) ? $config["separador_nombre"] : " | ";
		$categorias = array();
		while(TRUE) {
			$sql = "SELECT * FROM rubros_lpc WHERE id = $id_categoria ";
			$q = mysqli_query($this->conx,$sql);
			if (mysqli_num_rows($q)>0) {
				$cat = mysqli_fetch_object($q);
				$categorias[] = $cat;
				if ($cat->id_padre == 0) break; // Llegamos al final
				$id_categoria = $cat->id_padre;				
			} else break;
		}
		$categorias = array_reverse($categorias);
		$link_1 = "";
		$nombre = "";
		foreach($categorias as $cat) {
			$link_1 .= $cat->link."/";
			$cat->link = $link.$link_1;
			$cat->full_name = $nombre.(!empty($nombre) ? $separador_nombre : "").$cat->nombre;
			$nombre = $cat->full_name;
		}
		return $categorias;
	}
	
	private function encoding($entrada) {
		$entrada->plain_text = (!empty($entrada->descripcion)) ? mb_convert_encoding($entrada->descripcion, 'UTF-8', 'ISO-8859-1') : mb_convert_encoding(strip_tags($entrada->texto,"<a><i><b><br>"), 'UTF-8', 'ISO-8859-1');
		if (isset($entrada->texto)) $entrada->texto = mb_convert_encoding($entrada->texto, 'UTF-8', 'ISO-8859-1');
		if (isset($entrada->texto_destacado)) $entrada->texto_destacado = mb_convert_encoding($entrada->texto_destacado, 'UTF-8', 'ISO-8859-1');
		if (isset($entrada->nombre)) $entrada->nombre = mb_convert_encoding($entrada->nombre, 'UTF-8', 'ISO-8859-1');
		if (isset($entrada->caracteristicas)) $entrada->caracteristicas = mb_convert_encoding($entrada->caracteristicas, 'UTF-8', 'ISO-8859-1');
		if (isset($entrada->rubro)) $entrada->rubro = mb_convert_encoding($entrada->rubro, 'UTF-8', 'ISO-8859-1');
    if (isset($entrada->descripcion)) $entrada->descripcion = mb_convert_encoding($entrada->descripcion, 'UTF-8', 'ISO-8859-1');
    if (isset($entrada->marca)) $entrada->marca = mb_convert_encoding($entrada->marca, 'UTF-8', 'ISO-8859-1');
		return $entrada;
	}	

  public function get_moneda_tienda($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa_pivot;
    $sql = "SELECT tienda_moneda FROM web_configuracion WHERE id_empresa = $id_empresa";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)<=0) return "";
    $r_moneda = mysqli_fetch_object($q);

    // TODO: Esto se hace para acoplar los signos de los codigos, en un futuro todo debe ser codigo
    if ($r_moneda->tienda_moneda == "USD") $r_moneda->tienda_moneda = 'U$S';
    else if ($r_moneda->tienda_moneda == "ARS") $r_moneda->tienda_moneda = '$';

    return $r_moneda->tienda_moneda;
  }

  function get_cotizacion($moneda) {
    if ($moneda == "USD" || $moneda == 'U$S') $moneda = 'U$D';
    $sql_cot = 'SELECT * FROM cotizaciones WHERE moneda = "'.$moneda.'" ORDER BY fecha DESC LIMIT 0,1 ';
    $q_cot = mysqli_query($this->conx,$sql_cot);
    if (mysqli_num_rows($q_cot)<=0) return 1;
    $r_cot = mysqli_fetch_object($q_cot);
    return $r_cot->valor;
  }

  function multiplicar_cotizacion($cotizacion,$r) {
    if (isset($r->costo_neto)) $r->costo_neto = $r->costo_neto * $cotizacion;
    if (isset($r->costo_iva)) $r->costo_iva = $r->costo_iva * $cotizacion;
    if (isset($r->costo_final)) $r->costo_final = $r->costo_final * $cotizacion;
    if (isset($r->ganancia)) $r->ganancia = $r->ganancia * $cotizacion;
    if (isset($r->precio_neto)) $r->precio_neto = $r->precio_neto * $cotizacion;
    if (isset($r->precio_final)) $r->precio_final = $r->precio_final * $cotizacion;
    if (isset($r->precio_final_dto)) $r->precio_final_dto = $r->precio_final_dto * $cotizacion;
    if (isset($r->ganancia_2)) $r->ganancia_2 = $r->ganancia_2 * $cotizacion;
    if (isset($r->precio_neto_2)) $r->precio_neto_2 = $r->precio_neto_2 * $cotizacion;
    if (isset($r->precio_final_2)) $r->precio_final_2 = $r->precio_final_2 * $cotizacion;
    if (isset($r->precio_final_dto_2)) $r->precio_final_dto_2 = $r->precio_final_dto_2 * $cotizacion;
    if (isset($r->ganancia_3)) $r->ganancia_3 = $r->ganancia_3 * $cotizacion;
    if (isset($r->precio_neto_3)) $r->precio_neto_3 = $r->precio_neto_3 * $cotizacion;
    if (isset($r->precio_final_3)) $r->precio_final_3 = $r->precio_final_3 * $cotizacion;
    if (isset($r->precio_final_dto_3)) $r->precio_final_dto_3 = $r->precio_final_dto_3 * $cotizacion;
    if (isset($r->ganancia_4)) $r->ganancia_4 = $r->ganancia_4 * $cotizacion;
    if (isset($r->precio_neto_4)) $r->precio_neto_4 = $r->precio_neto_4 * $cotizacion;
    if (isset($r->precio_final_4)) $r->precio_final_4 = $r->precio_final_4 * $cotizacion;
    if (isset($r->precio_final_dto_4)) $r->precio_final_dto_4 = $r->precio_final_dto_4 * $cotizacion;
    if (isset($r->ganancia_5)) $r->ganancia_5 = $r->ganancia_5 * $cotizacion;
    if (isset($r->precio_neto_5)) $r->precio_neto_5 = $r->precio_neto_5 * $cotizacion;
    if (isset($r->precio_final_5)) $r->precio_final_5 = $r->precio_final_5 * $cotizacion;
    if (isset($r->precio_final_dto_5)) $r->precio_final_dto_5 = $r->precio_final_dto_5 * $cotizacion;
    if (isset($r->ganancia_6)) $r->ganancia_6 = $r->ganancia_6 * $cotizacion;
    if (isset($r->precio_neto_6)) $r->precio_neto_6 = $r->precio_neto_6 * $cotizacion;
    if (isset($r->precio_final_6)) $r->precio_final_6 = $r->precio_final_6 * $cotizacion;
    if (isset($r->precio_final_dto_6)) $r->precio_final_dto_6 = $r->precio_final_dto_6 * $cotizacion;
    return $r;
  }


  function get_list($config = array()) {

    // Lista de las empresas que estamos consultando (si no se manda, se toma por defecto todas las de LPC)
    $ids_empresas = isset($config["ids_empresas"]) ? $config["ids_empresas"] : $this->ids_empresas;
    // Si consultamos por una empresa en particular, tenemos que mandar el id_empresa
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : 0;

    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 6;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $activo_categorias = isset($config["activo_categorias"]) ? $config["activo_categorias"] : -1;
    $destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $codigo = isset($config["codigo"]) ? $config["codigo"] : "";
    $not_in_ids = isset($config["not_in_ids"]) ? $config["not_in_ids"] : "";
    $in_ids = isset($config["in_ids"]) ? $config["in_ids"] : "";
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $categoria = isset($config["categoria"]) ? $config["categoria"] : "";
    $id_promocion = isset($config["id_promocion"]) ? $config["id_promocion"] : 0;
    $ids_promociones = isset($config["ids_promociones"]) ? $config["ids_promociones"] : "";
    $tiene_descuento_1 = isset($config["tiene_descuento_1"]) ? $config["tiene_descuento_1"] : 0;
    $tiene_descuento_2 = isset($config["tiene_descuento_2"]) ? $config["tiene_descuento_2"] : 0;
    $tiene_descuento_3 = isset($config["tiene_descuento_3"]) ? $config["tiene_descuento_3"] : 0;
    $tiene_descuento_4 = isset($config["tiene_descuento_4"]) ? $config["tiene_descuento_4"] : 0;
    $tiene_descuento_5 = isset($config["tiene_descuento_5"]) ? $config["tiene_descuento_5"] : 0;
    $tiene_descuento_6 = isset($config["tiene_descuento_6"]) ? $config["tiene_descuento_6"] : 0;
    $link_promocion = isset($config["link_promocion"]) ? $config["link_promocion"] : "";
    $id_categoria_or_padre = isset($config["id_categoria_or_padre"]) ? $config["id_categoria_or_padre"] : 0;
    $id_marca = isset($config["id_marca"]) ? $config["id_marca"] : 0;
    $in_marcas = isset($config["in_marcas"]) ? $config["in_marcas"] : "";
    $in_categorias = isset($config["in_categorias"]) ? $config["in_categorias"] : "";
    $from_id_categoria = isset($config["from_id_categoria"]) ? $config["from_id_categoria"] : 0;
    $maximo = isset($config["maximo"]) ? $config["maximo"] : 0;
    $minimo = isset($config["minimo"]) ? $config["minimo"] : 0;
    $ancho = isset($config["ancho"]) ? $config["ancho"] : 0;
    $alto = isset($config["alto"]) ? $config["alto"] : 0;
    $profundidad = isset($config["profundidad"]) ? $config["profundidad"] : 0;
    $custom_1 = isset($config["custom_1"]) ? $config["custom_1"] : "";
    $custom_2 = isset($config["custom_2"]) ? $config["custom_2"] : "";
    $custom_3 = isset($config["custom_3"]) ? $config["custom_3"] : "";
    $custom_4 = isset($config["custom_4"]) ? $config["custom_4"] : "";
    $custom_5 = isset($config["custom_5"]) ? $config["custom_5"] : "";
    $custom_6 = isset($config["custom_6"]) ? $config["custom_6"] : "";
    $custom_7 = isset($config["custom_7"]) ? $config["custom_7"] : "";
    $custom_8 = isset($config["custom_8"]) ? $config["custom_8"] : "";
    $custom_9 = isset($config["custom_9"]) ? $config["custom_9"] : "";
    $custom_10 = isset($config["custom_10"]) ? $config["custom_10"] : "";
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "A.id DESC ";
    $id_opcion = isset($config["id_opcion"]) ? $config["id_opcion"] : "";
    $solo_contar = isset($config["solo_contar"]) ? $config["solo_contar"] : 0;
    $id_etiqueta = isset($config["id_etiqueta"]) ? $config["id_etiqueta"] : 0;
    $etiqueta_link = isset($config["etiqueta_link"]) ? $config["etiqueta_link"] : "";
    $tipo_envio = isset($config["tipo_envio"]) ? $config["tipo_envio"] : -1;
    $tipo_envio_comparacion = isset($config["tipo_envio_comparacion"]) ? $config["tipo_envio_comparacion"] : "=";
    $mostrar_precio_neto = $this->usar_precio_neto();
    $consultar_stock = isset($config["consultar_stock"]) ? $config["consultar_stock"] : 0; // Si es 1, llama a consultar_stock()
    $tiene_precio = isset($config["tiene_precio"]) ? $config["tiene_precio"] : -1;

    // Si no se envia un parametro, tomamos la configuracion guardada
    $convertir_moneda = isset($config["convertir_moneda"]) ? $config["convertir_moneda"] : $this->get_moneda_tienda();

    // Si se envia una lista de precios diferente a la que esta configurada por defecto en la web
    $lista_precios = isset($config["lista_precios"]) ? $config["lista_precios"] : $this->get_lista_precios();

		// CREAMOS LA CONSULTA
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " E.nombre AS empresa, E.logo AS empresa_path, ";
    $sql.= " IF(PRO.nombre IS NULL,'',PRO.nombre) AS promocion, ";
    $sql.= " IF(PRO.link IS NULL,'',PRO.link) AS promocion_link, ";
    $sql.= " IF(PRO.path IS NULL,'',PRO.path) AS promocion_path, ";
    $sql.= " IF (R.nombre IS NULL,'',R.nombre) AS rubro, ";
    $sql.= " IF (M.nombre IS NULL,'',M.nombre) AS marca, ";
    $sql.= " IF (M.path IS NULL,'',M.path) AS marca_path ";
    $sql.= "FROM articulos A ";
    $sql.= " INNER JOIN empresas E ON (A.id_empresa = E.id) ";
    $sql.= " LEFT JOIN rubros_lpc R ON (A.custom_1 = R.id) ";  
    $sql.= " LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
    $sql.= " LEFT JOIN promociones PRO ON (A.id_promocion = PRO.id AND A.id_empresa = PRO.id_empresa AND PRO.activo = 1) ";
    $sql.= "WHERE 1=1 ";
    if (!empty($id_empresa)) $sql.= "AND A.id_empresa = $id_empresa ";
    else $sql.= "AND A.id_empresa IN ($this->ids_empresas) ";

    if (!empty($codigo)) $sql.= "AND A.codigo = '$codigo' ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
    if (!empty($id_categoria)) $sql.= "AND A.custom_1 = $id_categoria ";
    if (!empty($id_categoria_or_padre)) $sql.= "AND (R.id_padre = $id_categoria_or_padre OR R.id = $id_categoria_or_padre) ";
    if (!empty($in_categorias)) $sql.= "AND A.custom_1 IN ($in_categorias) ";
    if (!empty($categoria)) {
      $cat = $this->get_categoria_by_link($categoria);
      $from_id_categoria = $cat->id;
    }
    if (!empty($from_id_categoria)) {
		// A partir de una categoria padre, tomamos todas las subcategorias y buscamos
      $ids_categorias = $this->get_ids_subcategorias($from_id_categoria,array(
        "tiene_productos"=>1,
        "id_empresa"=>$id_empresa,
      ));
      $ids_categorias = implode(",", $ids_categorias);
      $sql.= "AND A.custom_1 IN ($ids_categorias) ";	
    }
    if ($tiene_precio == 1) $sql.= "AND A.precio_final_dto > 0 ";
    if ($tipo_envio != -1) $sql.= "AND A.no_totalizar_reparto $tipo_envio_comparacion $tipo_envio ";
    if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
    if (!empty($in_marcas)) $sql.= "AND A.id_marca IN ($in_marcas) ";

    if ($destacado == 1) $sql.= "AND A.lista_precios >= 3 ";
    else if ($destacado == 0) $sql.= "AND A.lista_precios < 3 ";
    if ($activo == 1) $sql.= "AND A.lista_precios >= 2 ";
    else if ($activo == 0) $sql.= "AND A.lista_precios < 2 ";

    if ($activo_categorias != -1) $sql.= "AND R.activo = $activo_categorias ";
    if ($maximo != 0) $sql.= "AND A.precio_final_dto >= $minimo AND A.precio_final_dto <= $maximo ";
    if (!empty($filter)) {
      $sql.= "AND ( ";
      $filter3 = "";
      $filter2 = preg_split('/\s+/', $filter);
      foreach($filter2 as $fil) {
        $filter3 .= "+(*".$fil."*) ";
      }
      $sql.= "( MATCH(A.nombre) AGAINST ('$filter3' IN BOOLEAN MODE) ) ";
      $sql.= "OR ( MATCH(A.texto) AGAINST ('$filter3' IN BOOLEAN MODE) ) ";
      $sql.= "OR A.codigo LIKE '%$filter%' ";
      $sql.= "OR M.nombre LIKE '%$filter%' ";
      $sql.= "OR R.nombre LIKE '%$filter%' ";
      $sql.= ") ";
    }
    if (!empty($not_in_ids)) $sql.= "AND A.id NOT IN ($not_in_ids) ";
    if (!empty($in_ids)) $sql.= "AND A.id IN ($in_ids) ";
    if (!empty($ancho)) $sql.= "AND A.ancho = '$ancho' ";
    if (!empty($alto)) $sql.= "AND A.alto = '$alto' ";
    if (!empty($id_promocion)) $sql.= "AND A.id_promocion = '$id_promocion' ";
    if (!empty($ids_promociones)) $sql.= "AND A.id_promocion IN ($ids_promociones) ";
    if (!empty($link_promocion)) $sql.= "AND PRO.link = '$link_promocion' ";
    if (!empty($profundidad)) $sql.= "AND A.profundidad = '$profundidad' ";
    if (!empty($custom_1)) $sql.= "AND A.custom_1 = '$custom_1' ";
    if (!empty($custom_2)) $sql.= "AND A.custom_2 = '$custom_2' ";
    if (!empty($custom_3)) $sql.= "AND A.custom_3 = '$custom_3' ";
    if (!empty($custom_4)) $sql.= "AND A.custom_4 = '$custom_4' ";
    if (!empty($custom_5)) $sql.= "AND A.custom_5 = '$custom_5' ";
    if (!empty($custom_6)) $sql.= "AND A.custom_6 = '$custom_6' ";
    if (!empty($custom_7)) $sql.= "AND A.custom_7 = '$custom_7' ";
    if (!empty($custom_8)) $sql.= "AND A.custom_8 = '$custom_8' ";
    if (!empty($custom_9)) $sql.= "AND A.custom_9 = '$custom_9' ";
    if (!empty($custom_10)) $sql.= "AND A.custom_10 = '$custom_10' ";
    if (!empty($tiene_descuento_1)) $sql.= "AND A.porc_bonif > 0 ";
    if (!empty($tiene_descuento_2)) $sql.= "AND A.porc_bonif_2 > 0 ";
    if (!empty($tiene_descuento_3)) $sql.= "AND A.porc_bonif_3 > 0 ";
    if (!empty($tiene_descuento_4)) $sql.= "AND A.porc_bonif_4 > 0 ";
    if (!empty($tiene_descuento_5)) $sql.= "AND A.porc_bonif_5 > 0 ";
    if (!empty($tiene_descuento_6)) $sql.= "AND A.porc_bonif_6 > 0 ";
    if (!empty($id_opcion)) {
      $sql.= "AND EXISTS (SELECT AV.* FROM articulos_variantes AV WHERE A.id = AV.id_articulo AND A.id_empresa = AV.id_empresa AND ($id_opcion = AV.id_opcion_1 OR $id_opcion = AV.id_opcion_2 OR $id_opcion = AV.id_opcion_3) ) ";
    }

    if (!empty($id_cliente)) {
      $sql.= "AND EXISTS (SELECT * FROM articulos_clientes ART_CLI WHERE ART_CLI.id_articulo = A.id AND ART_CLI.id_empresa = A.id_empresa AND ART_CLI.id_cliente = $id_cliente) ";
    }
    if (!empty($etiqueta_link)) {
      $sql.= "AND EXISTS (SELECT 1 FROM articulos_etiquetas ETIQ INNER JOIN articulos_etiquetas_relacionadas ETIQ_REL ON (ETIQ.id_empresa = ETIQ_REL.id_empresa AND ETIQ_REL.id_etiqueta = ETIQ.id) WHERE ETIQ_REL.id_articulo = A.id AND ETIQ.link = '$etiqueta_link' ) ";
    }
    if (!empty($id_etiqueta)) {
      $sql.= "AND EXISTS (SELECT 1 FROM articulos_etiquetas_relacionadas ETIQ_REL WHERE ETIQ_REL.id_empresa = A.id_empresa AND ETIQ_REL.id_articulo = A.id AND ETIQ_REL.id_etiqueta = $id_etiqueta) ";
    }
    $sql.= "ORDER BY $order_by ";
    $sql.= "LIMIT $limit, $offset ";
    $this->sql = $sql;
    $q = mysqli_query($this->conx,$sql);

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;
    if ($solo_contar == 1) {
      return $this->get_total_results();
    }

    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r = $this->encoding($r);
      $r = $this->usar_lista_precios($r,$lista_precios);

      // Si tenemos que mostrar los precios netos, en vez de finales
      if ($mostrar_precio_neto == 1) {
        $r->precio_final = $r->precio_neto;
        $r->precio_final_dto = $r->precio_neto * ((100-$r->porc_bonif)/100);
      }

      // Si tenemos que consultar el stock
      if ($consultar_stock == 1) {
        $r->stock = $this->consultar_stock($r->id,array(
          "id_empresa"=>$r->id_empresa,
        ));
      }

      if (!empty($convertir_moneda) && $convertir_moneda != $r->moneda) {
        $cotizacion = $this->get_cotizacion($r->moneda);
        $r = $this->multiplicar_cotizacion($cotizacion,$r);
        $r->moneda = $convertir_moneda;
      }

      $r->path = (!empty($r->path)) ? (((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path) : "";
      $r->path = str_replace(" ","",$r->path);
      $r->marca_path = (!empty($r->marca_path)) ? (((strpos($r->marca_path,"http://")===FALSE)) ? "/sistema/".$r->marca_path : $r->marca_path) : "";
      $r->marca_path = str_replace(" ","",$r->marca_path);
      $salida[] = $r;
    }
    return $salida;
  }

  function get_total_results() {
    return $this->total;
  }

  function get_sql() {
    return $this->sql;
  }

  function get_etiquetas($config = array()) {
    $ids_empresas = isset($config["ids_empresas"]) ? $config["ids_empresas"] : $this->id_empresa;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 9999;
    $link = isset($config["link"]) ? $config["link"] : "";
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "A.orden ASC, A.nombre ASC ";
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
    $sql.= "FROM articulos_etiquetas A ";
    $sql.= "WHERE A.id_empresa IN ($ids_empresas) ";
    if (!empty($link)) $sql.= "AND A.link = '$link' ";
    $sql.= "ORDER BY $order_by ";
    $sql.= "LIMIT $limit, $offset ";
    $q = mysqli_query($this->conx,$sql);
    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (isset($r->nombre)) $r->nombre = mb_convert_encoding($r->nombre, 'UTF-8', 'ISO-8859-1');
      $salida[] = $r;
    }
    return $salida;
  }
    
  /**
   * Obtiene los articulos destacadas
   */
  function destacados($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["destacado"] = 1;
    return $this->get_list($config);
  }
    
  /**
   * Obtiene las ultimos articulos
   */
  function ultimos($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["order_by"] = "A.id DESC ";
    return $this->get_list($config);
  }    

  function mas_vistos($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["order_by"] = "A.id DESC ";
    return $this->get_list($config);
  }    

  function mas_vendidos($config = array()) {
    $config["limit"] = isset($config["limit"]) ? $config["limit"] : 0;
    $config["offset"] = isset($config["offset"]) ? $config["offset"] : 6;
    $config["order_by"] = "A.id DESC ";
    return $this->get_list($config);
  }    
    
}
?>