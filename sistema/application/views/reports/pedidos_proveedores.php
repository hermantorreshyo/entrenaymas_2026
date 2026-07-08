<?php
function imprimir_tabla($row,$i,$hasta) {
  echo '<table class="tabla w50p fl">';
  echo '  <thead>';
  echo '      <tr>';
  echo '          <th style="width:40px">Cod.</th>';
  echo '          <th style="width:40px">Prov.</th>';    
  echo '          <th style="width:40px">Unid.</th>';
  echo '          <th style="width:40px">Bultos</th>';
  echo '          <th>Descripcion</th>';
  echo '      </tr>';
  echo '</thead>';
  echo '<tbody>';
  for ($i;$i<$hasta;$i++) {
    $item = $row[$i];
    echo '<tr>';
    echo "<td>$item->codigo</td>";
    echo "<td>".(isset($item->codigo_proveedor)?$item->codigo_proveedor:"0")."</td>";
    echo "<td style='text-align:left'>".(($item->tipo_cantidad == "B") ? round($item->cantidad * $item->uxb,2) : $item->cantidad)."</td>";
    echo "<td style='text-align:left'>".(($item->tipo_cantidad == "B") ? $item->cantidad : "")."</td>";
    echo "<td>".utf8_decode($item->nombre)."</td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";
}
?>
<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
body { background-color: white; }
  .tabla tr td {
    padding-bottom: 3px;
    padding-top: 3px;
  }
</style>
</head>
<body>
  <?php echo $header; ?>
  <div class="header oh">
    <div class="subtitulo">
      <?php echo $empresa->razon_social ?>
      <span class="fwn">CUIT: <?php echo $empresa->cuit ?></span><br/>
      <span class="fwn">Proveedor: </span><?php echo utf8_decode($proveedor->nombre); ?> <span class="fwn">(<?php echo utf8_decode($proveedor->codigo); ?>)</span>
      <?php if (!empty($pedido->sucursal_direccion)) { ?>
        <span class="fwn ml15">Entrega en: </span><?php echo $pedido->sucursal_direccion; ?>
      <?php } ?>
    </div>
    <div class="mt5">
      <span>Fecha: <span class="bold"><?php echo $pedido->fecha ?></span></span>
      <span class="ml20">Pedido Nro: <span class="bold"><?php echo $pedido->numero ?></span></span>
    </div>
  </div>

  <?php foreach($resultados as $clave => $row) { ?>
    <div class="subtitulo">
      <?php echo utf8_decode($clave); ?>
    </div>
    <?php foreach($row as $c => $r) { ?>
      <div class="subtitulo2">
        <?php echo utf8_decode($c); ?>
      </div>
      <div class="oh">    
        <?php
        $total_filas = sizeof($r);
        $mitad = ceil($total_filas/2);
        echo imprimir_tabla($r,0,$mitad);
        if ($mitad != $total_filas) echo imprimir_tabla($r,$mitad,$total_filas);
        ?>
      </div>
    <?php } ?>
    <div class="bbs mt5 mb5 cb"></div>
  <?php } ?>

  <?php // DEVOLUCIONES ?>
  <?php if (sizeof($devoluciones) > 0) { ?>
    <div class="subtitulo bts pt10 mt20">DEVOLUCIONES</div>
    <table class="tabla">
      <thead>
        <tr>
          <th style="width:40px">Int.</th>
          <th style="width:40px">Prov.</th>
          <th style="width:96px">Cant.</th>
          <th>Descripcion</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($devoluciones as $d) { 
          $cantidad = abs(str_replace(".00","",$d->cantidad));
          $uxb = str_replace(".00","",$d->uxb);
          $unidades = floor($d->cantidad * $d->uxb);
          if (strpos($cantidad,".") === FALSE) {
            if ($uxb == 1) {
              $total = "$cantidad Un";
            } else {
              $total = $cantidad." B x $uxb u";    
            }
          } else {
            $total = floor($d->cantidad * $d->uxb)." Un";
          }

          echo '<tr>';
          echo "<td>$d->id</td>";
          echo "<td>".(isset($d->codigo_proveedor)?$d->codigo_proveedor:"0")."</td>";
          echo "<td style='text-align:left'>$total</td>";
          echo "<td>".utf8_decode($d->descripcion)."</td>";
          echo "</tr>";                
        } ?>
      </tbody>
    </table>
  <?php } ?>

  <?php if (!empty($pedido->observaciones)) { ?>
    <h4 style="margin-top: 10px; margin-bottom: 5px;">Observaciones</h4>
    <p><?php echo $pedido->observaciones ?></p>
  <?php } ?>
  
</body>
</html>