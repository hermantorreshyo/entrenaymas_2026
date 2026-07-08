<base rel="nofollow" href="/templates/<?php echo $empresa->template_path ?>/"/>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<title><?php echo (isset($seo_title)) ? utf8_encode(html_entity_decode($seo_title,ENT_QUOTES)) : utf8_encode(html_entity_decode($empresa->seo_title,ENT_QUOTES)); ?></title>
<meta name="description" content="<?php echo (isset($seo_description)) ? utf8_encode(html_entity_decode($seo_description,ENT_QUOTES)) : utf8_encode(html_entity_decode($empresa->seo_description,ENT_QUOTES)); ?>">

<?php 
$keywords = (isset($seo_keywords)) ? (html_entity_decode($seo_keywords,ENT_QUOTES)) : (html_entity_decode($empresa->seo_keywords,ENT_QUOTES));
if (!empty($keywords)) { ?>
  <meta name="keywords" content="<?php echo $keywords; ?>">
<?php } ?>

<!-- Psweb External CSS -->
<link href="assets/css/magnific-popup.min.css" rel="stylesheet" type="text/css" media="all">
<link href="assets/css/owl.carousel.min.css" rel="stylesheet" type="text/css" media="all">
<link href="assets/css/animate.css" rel="stylesheet" type="text/css" media="all">
<link href="assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all">
<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
<link href="assets/css/styles.css?v=2022-06-18" rel="stylesheet" type="text/css" media="all">
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css" media="all">
<link href="https://fonts.googleapis.com/css?family=Arimo:400,700&display=swap" rel="stylesheet" type="text/css" media="all">
<link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet" type="text/css" media="all">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<?php /*$canonical = (isset($canonical) ? $canonical : "https://www.cdsurargentina.com.ar/") ?>
<link rel="canonical" href="<?php echo $canonical ?>"/> 
*/ ?>

<?php include_once("templates/comun/ldjson/organization.php"); ?>

<?php if (strpos(strtolower($empresa->favicon), ".png")>0) { ?>
  <link rel="shortcut icon" type="image/png" href="/sistema/<?php echo $empresa->favicon ?>"/>
<?php } else if (strpos(strtolower($empresa->favicon), ".ico")>0) { ?>
  <link rel="shortcut icon" type="image/x-icon" href="/sistema/<?php echo $empresa->favicon ?>" />
<?php } else { ?>
  <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon" />
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

<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css">

<script type="text/javascript">
// Constantes que se utilizan en el sistema
const ID_EMPRESA = "<?php echo $empresa->id ?>";
const CURRENT_URL = "<?php echo current_url(); ?>";
</script>
<?php if (!empty($empresa->texto_css)) { ?>
<style type="text/css"><?php echo $empresa->texto_css ?></style>
<?php } ?>
<?php if (!empty($empresa->analytics)) { echo html_entity_decode($empresa->analytics,ENT_QUOTES); } ?>
<?php if (!empty($empresa->zopim)) { echo html_entity_decode($empresa->zopim,ENT_QUOTES); } ?>
<?php if (!empty($empresa->google_site_verification)) { echo html_entity_decode($empresa->google_site_verification,ENT_QUOTES); } ?>
<?php if (!empty($empresa->adsense)) { echo html_entity_decode($empresa->adsense,ENT_QUOTES); } ?>
<?php if (!empty($empresa->pixel_fb)) { echo html_entity_decode($empresa->pixel_fb,ENT_QUOTES); } ?>