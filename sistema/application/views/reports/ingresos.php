<?php
function imprimir_tabla($row,$i,$hasta) {
    echo '<table class="tabla">';
    echo '  <thead>';
    echo '      <tr>';
    echo '          <th style="width:35px">Int.</th>';
    echo '          <th style="width:35px">Prov.</th>';
    echo '          <th style="width:35px">Cant.</th>';
    echo '          <th style="width:35px">UxB</th>';
    echo '          <th>Descripcion</th>';
    echo '          <th style="width:100px">Costo Unit</th>';
    echo '          <th style="width:100px">Costo Total</th>';
    echo '      </tr>';
    echo '</thead>';
    echo '<tbody>';
    for ($i;$i<$hasta;$i++) {
        $ingreso = $row[$i];
        echo '<tr>';
        echo "<td>$ingreso->id</td>";
        echo "<td>".(isset($ingreso->codigo_proveedor)?$ingreso->codigo_proveedor:"0")."</td>";
        echo "<td>$ingreso->cantidad</td>";
        echo "<td>$ingreso->uxb</td>";
        echo "<td>".utf8_decode($ingreso->descripcion)."</td>";
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
</head>
<body>
<div class="page">
  <div class="p3mm">    
    <div class="header oh">
        <div class="subtitulo fl">
            INGRESO: <?php echo utf8_decode($proveedor->nombre); ?>
            <span class="fwn ml10">A: <?php echo $ingreso->sucursal; ?></span>
        </div>
        <div class="fr">
            <span>Fecha: <span class="bold"><?php echo $ingreso->fecha ?></span></span>
            <span style="margin-left: 20px;">Ingreso Nro: <span class="bold"><?php echo $ingreso->numero ?></span></span>
        </div>
    </div>
    
    <?php foreach($resultados as $clave => $row) { ?>
        
        <div class="subtitulo">
            <?php echo utf8_decode($clave); ?>
        </div>
        
        <?php
        imprimir_tabla($row,0,sizeof($row));
        ?>

        <div class="bbs mt5 mb5 cb"></div>
        
    <?php } ?>
  </div>
</div>
</body>
</html>