<?php 
$ver_precios = ($empresa->tienda_ver_precios == 0 || ($empresa->tienda_ver_precios == 1 && isset($_SESSION["id_cliente"])));
$ver_carrito = ($empresa->tienda_carrito < 2);
$ver_consulta = ($empresa->tienda_consulta_productos == 0);
$incluir_buscador_neumaticos = (isset($empresa->tipo_empresa)) ? (($empresa->tipo_empresa == 1)?1:0) : 0;
$incluir_turnos = (isset($empresa->tipo_empresa)) ? (($empresa->tipo_empresa == 2 || isset($empresa->config["incluir_turnos"]))?1:0) : 0;

$boton_comprar_ahora = isset($empresa->config["boton_comprar_ahora"]) ? $empresa->config["boton_comprar_ahora"] : "comprar ahora";
$boton_comprar = isset($empresa->config["boton_comprar"]) ? $empresa->config["boton_comprar"] : "COMPRAR";
$boton_agregar_carrito = isset($empresa->config["boton_agregar_carrito"]) ? $empresa->config["boton_agregar_carrito"] : "agregar al carrito";
$ocultar_boton_agregar_carrito = isset($empresa->config["ocultar_boton_agregar_carrito"]) ? $empresa->config["ocultar_boton_agregar_carrito"] : 0;
$ocultar_boton_comprar = isset($empresa->config["ocultar_boton_comprar"]) ? $empresa->config["ocultar_boton_comprar"] : 0;
?>
<?php if (!empty($empresa->gtm_head)) { echo html_entity_decode($empresa->gtm_head,ENT_QUOTES); } ?>
<base href="/templates/<?php echo $empresa->template_path ?>/"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<?php 
$title = (isset($seo_title)) ? (html_entity_decode($seo_title,ENT_QUOTES)) : (html_entity_decode($empresa->seo_title,ENT_QUOTES));
$title = str_replace("\n", " ", $title);
?>
<title><?php echo $title ?></title>

<?php 
$description = (isset($seo_description)) ? (html_entity_decode($seo_description,ENT_QUOTES)) : (html_entity_decode($empresa->seo_description,ENT_QUOTES));
$description = str_replace("\n", " ", $description);
?>
<meta name="description" content="<?php echo $description ?>">

<?php 
$keywords = (isset($seo_keywords)) ? (html_entity_decode($seo_keywords,ENT_QUOTES)) : (html_entity_decode($empresa->seo_keywords,ENT_QUOTES));
$keywords = str_replace("\n", " ", $keywords);
?>
<meta name="keywords" content="<?php echo $keywords ?>">

<?php include_once("templates/comun/ldjson/organization.php"); ?>

<?php if (strpos(strtolower($empresa->favicon), ".png")>0) { ?>
  <link rel="shortcut icon" type="image/png" href="/sistema/<?php echo $empresa->favicon ?>"/>
<?php } else if (strpos(strtolower($empresa->favicon), ".ico")>0) { ?>
  <link rel="shortcut icon" type="image/x-icon" href="/sistema/<?php echo $empresa->favicon ?>" />
<?php } else { ?>
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon" />
<?php } ?>

<link href="/sistema/resources/css/common.css" media="all" type="text/css" rel="stylesheet"/>

<?php if (isset($incluir_buscador_neumaticos) && $incluir_buscador_neumaticos == 1) { ?>
  <?php include_once("templates/comun/neumaticos/buscador_css.php"); ?>
<?php } ?>

<?php if (isset($incluir_turnos) && $incluir_turnos == 1) { ?>
  <link rel="stylesheet" type="text/css" href="/sistema/resources/js/jquery/ui/jquery-ui.min.css">
  <link rel="stylesheet" type="text/css" href="/sistema/resources/js/jquery/ui/jquery-ui.theme.min.css">
<?php } ?>


<style type="text/css">
:root {
  <?php if (!empty($empresa->color_principal)) { ?>
    --c1: <?php echo $empresa->color_principal ?>;
  <?php } ?>
  <?php if (!empty($empresa->color_secundario)) { ?>
    --c2: <?php echo $empresa->color_secundario ?>;
  <?php } ?>
  <?php if (!empty($empresa->color_terciario)) { ?>
    --c3: <?php echo $empresa->color_terciario ?>;
  <?php } ?>
  <?php if (!empty($empresa->color_4)) { ?>
    --c4: <?php echo $empresa->color_4 ?>;
  <?php } ?>
  <?php if (!empty($empresa->color_5)) { ?>
    --c5: <?php echo $empresa->color_5 ?>;
  <?php } ?>
  <?php if (!empty($empresa->color_6)) { ?>
    --c6: <?php echo $empresa->color_6 ?>;
  <?php } ?>
}
</style>