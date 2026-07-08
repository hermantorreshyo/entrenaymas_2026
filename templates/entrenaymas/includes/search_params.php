<?php
// Localidades
$vc_localidades = isset($get_params["locs"]) ? $get_params["locs"] : "";
if (!empty($vc_localidades)) {
  $vc_localidades = explode("-",$vc_localidades);
  foreach($vc_localidades as &$vc) $vc = intval($vc);
} else $vc_localidades = array();

// Guardamos en la sesion
if (!empty($vc_localidades)) {
  $_SESSION['vc_localidades'] = $get_params["locs"];
}

// Especialidades
$vc_especialidades = isset($get_params["esp"]) ? $get_params["esp"] : "";
if (!empty($vc_especialidades)) {
  $vc_especialidades = explode("-",$vc_especialidades);
  foreach($vc_especialidades as &$vc) $vc = intval($vc);
} else $vc_especialidades = array();

// Categorias
$vc_categorias = isset($get_params["cat"]) ? $get_params["cat"] : "";
if (!empty($vc_categorias)) {
  $vc_categorias = explode("-",$vc_categorias);
  foreach($vc_categorias as &$vc) $vc = intval($vc);
} else $vc_categorias = array();

// Titulos
$vc_titulos = isset($get_params["tit"]) ? $get_params["tit"] : "";
if (!empty($vc_titulos)) {
  $vc_titulos = explode("-",$vc_titulos);
  foreach($vc_titulos as &$vc) $vc = intval($vc);
} else $vc_titulos = array();

// Tipos de Terapias
$vc_tipos_terapias = isset($get_params["tt"]) ? $get_params["tt"] : "";
if (!empty($vc_tipos_terapias)) {
  $vc_tipos_terapias = explode("-",$vc_tipos_terapias);
  foreach($vc_tipos_terapias as &$vc) $vc = intval($vc);
} else $vc_tipos_terapias = array();

// Tipos de Atenciones
$vc_tipos_atenciones = isset($get_params["ta"]) ? $get_params["ta"] : "";
if (!empty($vc_tipos_atenciones)) {
  $vc_tipos_atenciones = explode("-",$vc_tipos_atenciones);
  foreach($vc_tipos_atenciones as &$vc) $vc = intval($vc);
} else $vc_tipos_atenciones = array();

// Formas de Pago
$vc_formas_pago = isset($get_params["fp"]) ? $get_params["fp"] : "";
if (!empty($vc_formas_pago)) {
  $vc_formas_pago = explode("-",$vc_formas_pago);
  foreach($vc_formas_pago as &$vc) $vc = intval($vc);
} else $vc_formas_pago = array();


// Formas de Pago
$vc_tipos_perfil = isset($get_params["tp"]) ? $get_params["tp"] : "";
if (!empty($vc_tipos_perfil)) {
  $vc_tipos_perfil = explode("-",$vc_tipos_perfil);
  foreach($vc_tipos_perfil as &$vc) $vc = intval($vc);
} else $vc_tipos_perfil = array();


$in_ids = array();
$favs = isset($get_params["favs"]) ? $get_params["favs"] : 0;
if ($favs == 1 && isset($_SESSION["favoritos"])) $in_ids = explode(",",$_SESSION["favoritos"]);

$offset=20;
$page = isset($params[1])?$params[1]:0;
$not_ids = isset($get_params["not_ids"]) ? $get_params["not_ids"] : "";

$offset = isset($get_params["offset"]) ? $get_params["offset"] : 20 ;
$profesionales = $usuario_model->get_list(array(
  "id_perfil"=>(isset($portal_id_perfil_profesional) ? $portal_id_perfil_profesional : 0),
  "offset"=>$offset,
  "usar_get"=>1, // Automaticamente se llama a usuario_model->get por cada elemento del array
  "localidades"=>$vc_localidades,
  "especialidades"=>$vc_especialidades,
  "categorias"=>$vc_categorias,
  "titulos"=>$vc_titulos,
  "not_ids"=>$not_ids,
  "tipos_terapias"=>$vc_tipos_terapias,
  "tipos_atenciones"=>$vc_tipos_atenciones,
  "formas_pago"=>$vc_formas_pago,
  "tipos_perfil"=>$vc_tipos_perfil,
  "in_ids"=>$in_ids,
  "activo"=>1,
));
$total_resultados = $usuario_model->get_total_results();


function next_seo_urls($config = array()) {
  global $conx, $empresa;
  $offset = isset($config["offset"]) ? $config["offset"] : 8;
  $campos = isset($config["campos"]) ? $config["campos"] : array();
  $id_desde = isset($config["id_desde"]) ? $config["id_desde"] : 0;
  $sql = "SELECT U.* ";
  $sql.= "FROM seo_urls U ";
  $sql.= "WHERE 1=1 ";
  foreach($campos as $c) {
    if ($c["valor"] == 0 && $c["comparacion"] == "=") {
      $sql.= "AND NOT EXISTS (SELECT 1 FROM seo_urls_parametros P WHERE U.id_empresa = P.id_empresa AND P.id_seo = U.id ";
      $sql.= "AND P.campo = '".$c["nombre"]."') ";
    } else if ($c["valor"] == 0 && $c["comparacion"] == "!=") {
      $sql.= "AND EXISTS (SELECT 1 FROM seo_urls_parametros P WHERE U.id_empresa = P.id_empresa AND P.id_seo = U.id ";
      $sql.= "AND P.campo = '".$c["nombre"]."') ";      
    } else {
      $sql.= "AND EXISTS (SELECT 1 FROM seo_urls_parametros P WHERE U.id_empresa = P.id_empresa AND P.id_seo = U.id ";
      $sql.= "AND P.campo = '".$c["nombre"]."' AND P.valor ".$c["comparacion"]." '".$c["valor"]."') ";
    }
  }
  if (!empty($id_desde)) $sql.= "AND U.id >= $id_desde ";
  $sql.= "ORDER BY U.id ASC ";
  $sql.= "LIMIT 0, $offset ";
  $q = mysqli_query($conx,$sql);
  $salida = array();
  while(($r = mysqli_fetch_object($q))!==NULL) {
    $salida[] = $r;
  }
  return $salida;
}
?>