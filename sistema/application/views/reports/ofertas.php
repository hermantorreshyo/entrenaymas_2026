<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<script type="text/javascript" src="/sistema/resources/js/jquery.js"></script> 
<script type="text/javascript" src="/sistema/resources/js/jquery-barcode.js"></script>
<script type="application/javascript">
$(document).ready(function(){
  /*
  $(".barcode").each(function(i,e){
    var codigo = $(e).attr("data-id");
    if (codigo.length == 13) {
      $(e).barcode(codigo, "ean13");
    }
    if (codigo.length == 8) {
      $(e).barcode(codigo, "ean8");
    }
  });
  */
});
</script>
<style type="text/css">
body { background-color: white; }
.page {
  overflow: hidden;
}
.cartelito {
  font-size: 12px;
  border: solid 1px gray;
  padding: 40px;
  clear: both;
  overflow: hidden;
  height: 180px;
  width: 790px;
}
.titulo {
  font-size: 72px;
  font-weight: bold;
  text-align: center;
  vertical-align: middle;
  padding: 20px;
}
.nombre {
  font-size: 44px;
  font-weight: bold;
  text-align: center;
  vertical-align: middle;
  padding: 20px;
}
.precio {
  text-align: right;
  font-size: 72px;
  font-weight: bold;
  width: 30%;
  padding: 10px;
}
.info {
  text-align: right;
  font-size: 11px;
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
          <td colspan="2" class="titulo">OFERTA !!</td>
        </tr>
        <tr>
          <td class="nombre">
            <?php echo ($r->nombre); ?>
          </td>
          <td class="precio">
            <?php echo number_format($r->precio_final,2,".",","); ?>
            <div class="info">
              <?php echo ($r->rubro) ?><br/>
              <b>Cod. Int: <?php echo $r->codigo ?></b> &nbsp;&nbsp;&nbsp;&nbsp;
              UxB: <?php echo $r->uxb ?>
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