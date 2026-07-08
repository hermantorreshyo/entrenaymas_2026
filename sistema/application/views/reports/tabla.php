<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<script type="text/javascript" src="/sistema/resources/js/jquery.js"></script> 
<style type="text/css">
body { background-color: white; }
</style>
</head>
<body>
  <div class="page horizontal">
    <div class="p3mm">    
      <div class="header oh">
        <div class="subtitulo fl">
          <span class="bold"><?php echo $titulo ?></span>
        </div>
        <?php if (isset($fechas) && !empty($fechas)) { ?>
          <div class="fr">
            <?php echo $fechas ?>
          </div>
        <?php } ?>
      </div>
      <?php echo html_entity_decode($tabla) ?>
    </div>
  </div>
</body>
</html>