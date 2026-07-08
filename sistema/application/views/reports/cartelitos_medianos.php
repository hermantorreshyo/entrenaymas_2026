<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<script type="text/javascript" src="/sistema/resources/js/jquery.js"></script> 
<script type="text/javascript" src="/sistema/resources/js/jquery-barcode.js"></script>
<script type="application/javascript">
$(document).ready(function(){
  $(".barcode").each(function(i,e){
    var codigo = $(e).attr("data-id");
    if (codigo.length == 13) {
      $(e).barcode(codigo, "ean13");
    }
    if (codigo.length == 8) {
      $(e).barcode(codigo, "ean8");
    }
  });
});
</script>
<style type="text/css">
body { background-color: white; }
table { width: 100%; }
.cartelito {
  font-size: 12px;
  border-bottom: solid 1px gray;
  padding-bottom: 6px;
  clear: both;
  overflow: hidden;
  height: 140px;
  width: 540px;
}
.oferta {
  font-size: 38px;
  font-weight: bold;
  text-align: center;
  vertical-align: middle;
  margin-bottom: 10px;
}
.nombre {
  font-size: 22px;
  font-weight: bold;
  text-align: center;
  vertical-align: middle;
}
.precio {
  text-align: right;
  font-size: 48px;
  font-weight: bold;
  width: 30%;
  padding: 10px;
}
.info {
  text-align: right;
  font-size: 14px;
  margin-left: 10px;
  margin-top: 10px;
  font-weight: normal;
}
.barcode {
}
</style>
</head>
<body>
<?php $p=0; ?>
<?php for($i=0;$i<sizeof($results);$i++) { ?>
  <?php if($p==0) { echo '<div class="page">'; echo '<table>'; } ?>
  <tr>
    <td class="cartelito">
      <?php $r = $results[$i]; ?>
      <table style="width: 100%">
        <tr>
          <td>
            <div class="oferta">OFERTA!!</div>
            <div class="nombre"><?php echo ($r->nombre); ?></div>
          </td>
          <td class="precio">
            <?php if ($r->custom_5 == "1") { ?>
              <?php echo number_format($r->precio_neto,2,".",","); ?>
            <?php } else { ?>
              <?php echo number_format($r->precio_final,2,".",","); ?>
            <?php } ?>
            
            <div class="info">
              <?php echo ($r->rubro) ?><br/>
              <b>Cod. Int: <?php echo $r->codigo ?></b>
            </div>
            
          </td>
        </tr>
      </table>
      
      
    </td>
  </tr>
  <?php if($p==5) { echo "</table></div>"; $p=0; } else { $p++; } ?>
<?php } ?>
</body>
</html>