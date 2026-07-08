<?php
function imprimir_tabla($row) {
  $total = 0;
  echo '<table class="tabla">';
  echo '  <thead>';
  echo '    <tr>';
  echo '      <th style="width:100px">Cod.</th>';
  echo '      <th style="width:100px">Cod. Barra</th>';
  echo '      <th style="width:40px">Mov.</th>';
  echo '      <th style="width:40px">Cant.</th>';
  echo '      <th>Articulo</th>';
  echo '      <th class="tar" style="width:80px">Costo Unit.</th>';
  echo '      <th class="tar" style="width:80px">Costo Total</th>';
  echo '      <th class="tar" style="width:80px">Precio Unit.</th>';
  echo '      <th class="tar" style="width:80px">Precio Total</th>';
  echo '    </tr>';
  echo '</thead>';
  echo '<tbody>';
  foreach($row as $stock) {
    $codigo = trim($stock->codigo);
    $codigo_barra = trim($stock->codigo_barra);
    $movimiento = ($stock->movimiento == "A") ? "Alta" : ($stock->movimiento=="B"?"Baja":($stock->movimiento=="M")?"Ajuste":"Rotura");
    $stock->cantidad = ($stock->movimiento == "B"||$stock->movimiento == "R")? (-$stock->cantidad) : $stock->cantidad;
    $cantidad = str_replace(".00","",$stock->cantidad);
    echo '<tr>';
    echo "<td>$codigo</td>";
    echo "<td>";
    $codigo_barra = explode("###",$codigo_barra);
    foreach($codigo_barra as $cod) {
      echo ($cod != $stock->codigo) ? $cod."<br/>" : "";
    }
    echo "</td>";
    echo "<td>$movimiento</td>";
    echo "<td>$cantidad</td>";
    echo "<td>".$stock->nombre."</td>";
    $total += (float) ($stock->costo_final * $cantidad);
    echo "<td class='tar'>$ ".number_format($stock->costo_final,2)."</td>";
    echo "<td class='tar'>$ ".number_format($stock->costo_final * $cantidad,2)."</td>";
    echo "<td class='tar'>$ ".number_format($stock->precio_final,2)."</td>";
    echo "<td class='tar'>$ ".number_format($stock->precio_final * $cantidad,2)."</td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";
  return $total;
}
?>
<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
body {
  background-color: white;
}
.tabla tr td {
  padding-bottom: 3px;
  padding-top: 3px;
}
@media print {
  body {-webkit-print-color-adjust: exact; margin:0px; }
}
@page {
  size: auto;
  margin: 30px 0px 30px 0px;
}
</style>
</head>
<body>
<?php echo $header; ?>
<div class="p30">
  <div class="header oh">
    <div class="subtitulo fl">
      MOVIMIENTOS
      <?php if(!empty($almacen)) { ?>
        EN <?php echo utf8_decode($almacen->nombre); ?>
      <?php } ?>
      <?php if(!empty($proveedor)) { ?>
        DE <?php echo utf8_decode($proveedor->nombre); ?>
      <?php } ?>
    </div>
    <div class="fr">
      <span>Fecha: <span class="bold"><?php echo date("d/m/Y") ?></span></span>
    </div>
  </div>
  
  <?php 
  $total_costo = 0;
  foreach($resultados as $clave => $row) { ?>
    
    <div class="subtitulo">
      <?php echo ($clave); ?>
    </div>
    
    <div class="oh">  
    <?php $total_costo += imprimir_tabla($row); ?>
    </div>
    
    <div class="bbs mt5 mb5 cb"></div>
    
  <?php } ?>

  <b>Costo Total: $<?php echo round($total_costo,2)?> </b>
</div>
</body>
</html>