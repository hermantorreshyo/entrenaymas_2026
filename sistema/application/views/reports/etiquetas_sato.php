<!DOCTYPE>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
body { background-color: white; }
</style>
<script type="text/javascript" src="/sistema/resources/js/jquery.js"></script> 
<script type="text/javascript" src="/sistema/resources/js/jquery-barcode.js"></script>
<script type="application/javascript">
$(document).ready(function(){
  $(".barcode").each(function(i,e){
    var codigo = $(e).attr("data-id");
    if (codigo.length == 13) {
      $(e).barcode(codigo, "ean13",{
        "barHeight":24,
        "barWidth":1,
      });
    }
    if (codigo.length == 8) {
      $(e).barcode(codigo, "ean8",{
        "barHeight":24,
        "barWidth":1,
      });
    }
  });
});
</script>
<style type="text/css">
body { margin: 0px !important; padding: 0px !important; }
table { width: 100%; }
div.page {
  height: auto !important;
  width: 100%;
}
.cartelito {
  padding-top: 2mm;
  padding-bottom: 2mm;
  vertical-align: top;
  position: relative;
  clear: both;
  overflow: hidden;
  height: 17mm;
  width: 33.3333333%;
}
@media print {
  @page { margin: 0cm }
}
.nombre {
  text-transform: uppercase;
  text-align: center;
  font-size: 8px;
  font-weight: normal;
  margin-top: 1mm;
  margin-left: 10px;
  margin-right: 10px;
}
.barcode {
  margin-top: 0px;
  margin-left: auto; margin-right: auto;
}
.col1 { padding-left: 30px; }
.col2 { padding-left: 20px; }
</style>
</head>
<body>
<div class="page">
  <table>
<?php
$j=0; 
$p=0; 
for($i=0;$i<sizeof($articulos);$i++) { ?>
  <?php if($j==0) echo "<tr>" ?>
  <td class="cartelito col<?php echo $j ?>">
    <?php $r = $articulos[$i]; ?>
    <div style="text-align: center;">
      <div class="barcode" data-id="<?php echo $r->codigo; ?>"></div>
    </div>
    <div class="nombre">
      <?php $nombre = ($id_empresa == 121) ? mb_strtoupper($r->nombre) : ucwords(strtolower($r->nombre)); 
      echo substr($nombre, 0, 30); ?>
    </div>
  </td>
  <?php if($j==2) { echo "</tr>"; $j=0; } else { $j++; } ?>
<?php } ?>
<?php if($j<3) {
  for($i=$j;$i<3;$i++) {
    echo "<td class='cartelito'></td>";
  }
  echo "</tr>";
} ?>
</table>
</div>
</body>
</html>