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
        "barHeight":30
      });
    }
    if (codigo.length == 8) {
      $(e).barcode(codigo, "ean8",{
        "barHeight":30
      });
    }
  });
});
</script>
<style type="text/css">
table { width: 100%; }
.cartelito {
  vertical-align: top;
  position: relative;
  font-size: 12px;
  border-right: solid 1px gray;
  border-bottom: solid 1px gray;
  padding-bottom: 10px;
  clear: both;
  overflow: hidden;
  height: <?php echo ($id_empresa == 342)?"3cm":(($id_empresa == 356) ? "128px" : "152px")?>;
  width: 33%;
}
.nombre {
  text-align: left;
  font-size: 16px;
  font-weight: bold;
  margin-top: 10px;
  margin-left: 10px;
  margin-right: 10px;
}
.precio {
  text-align: right;
  font-size: 28px;
  font-weight: bold;
}
.info {
  text-align: left;
  position: absolute;
  bottom: 10px;
  font-size: 11px;
  margin-left: 10px;
  margin-top: 10px;
  width: 90%;
}
.barcode {
  margin-top: 0px;
}
</style>
</head>
<body>
<?php 
$limite = (($id_empresa == 342) ? 26 : 20);
$j=0; 
$p=0; 
for($i=0;$i<sizeof($results);$i++) { ?>
  <?php if($p==0) { echo '<div class="page">'; echo '<table>'; } ?>
  <?php if($j==0) echo "<tr>" ?>
  <td class="cartelito">
    <?php $r = $results[$i]; ?>
    
    <div class="nombre">
      <?php echo ($id_empresa == 121) ? mb_strtoupper($r->nombre) : ucwords(strtolower($r->nombre)); ?>
    </div>

    <div class="oh mt10 tar pr15 precio mb10">
      $ <?php echo number_format($r->precio_final_dto,2,".",","); ?>
    </div>
    
    <div class="info">
      <?php if ($id_empresa != 121) { ?>
        <div class="fl">
          <?php
          $codigo_barra = explode("###",$r->codigo_barra);
          $cod_barra = $codigo_barra[0];
          ?>
          <div class="barcode" data-id="<?php echo $cod_barra; ?>"></div>
        </div>
      <?php } ?>
      <div class="fr">

        <?php if (!empty($r->rubro) && $r->rubro != "-") { ?>
          <b style="margin-right: 15px"><?php echo ($r->rubro) ?></b>
        <?php } ?>
        <b style="font-size: 13px">
          <?php echo ($id_empresa == 121) ? $r->codigo_barra : "" ?>
          Cod. <?php echo $r->codigo ?>
        </b>
      </div>
    </div>
    
  </td>
  <?php if($j==2) { echo "</tr>"; $j=0; } else { $j++; } ?>
  <?php if($p==$limite) { echo "</table></div>"; $p=0; } else { $p++; } ?>
<?php } ?>

<?php if($j<3) {
  for($i=$j;$i<3;$i++) {
    echo "<td class='cartelito'></td>";
  }
  echo "</tr>";
} ?>
</body>
</html>