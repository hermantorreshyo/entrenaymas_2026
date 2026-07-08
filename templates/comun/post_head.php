<script type="text/javascript" src="/sistema/resources/js/common.js"></script>
<?php if (!empty($empresa->analytics)) { echo html_entity_decode($empresa->analytics,ENT_QUOTES); } ?>
<?php if (!empty($empresa->zopim)) { echo html_entity_decode($empresa->zopim,ENT_QUOTES); } ?>
<?php if (!empty($empresa->google_site_verification)) { echo html_entity_decode($empresa->google_site_verification,ENT_QUOTES); } ?>
<?php if (!empty($empresa->adsense)) { echo html_entity_decode($empresa->adsense,ENT_QUOTES); } ?>
<?php if (!empty($empresa->pixel_fb)) { echo html_entity_decode($empresa->pixel_fb,ENT_QUOTES); } ?>
<script type="text/javascript">
// Constantes que se utilizan en el sistema
const ID_EMPRESA = "<?php echo $empresa->id ?>";
const CURRENT_URL = "<?php echo current_url(); ?>";
</script>
<?php if (!empty($empresa->texto_css)) { ?>
<style type="text/css"><?php echo $empresa->texto_css ?></style>
<?php } ?>