<?php
function imprimir_tabla($row,$i,$hasta) {
    echo '<table class="tabla w50p fl">';
    echo '  <thead>';
    echo '      <tr>';
    echo '          <th style="width:35px">Int.</th>';
    echo '          <th style="width:35px">Prov.</th>';
    //echo '          <th style="width:32px">Bul</th>';
    //echo '          <th style="width:32px">UxB</th>';
    //echo '          <th style="width:32px">Unid</th>';
    echo '          <th style="width:96px">Cant.</th>';
    echo '          <th>Descripcion</th>';
    echo '      </tr>';
    echo '</thead>';
    echo '<tbody>';
    for ($i;$i<$hasta;$i++) {
        $pedido = $row[$i];
        $cantidad = str_replace(".00","",$pedido->cantidad);
        $uxb = str_replace(".00","",$pedido->uxb);
        
        if (strpos($cantidad,".") === FALSE) {
            if ($uxb == 1) {
                $total = "$cantidad Un";
            } else {
                $total = $cantidad." B x $uxb u";    
            }
        } else {
            $total = floor($pedido->cantidad * $pedido->uxb)." Un";
        }        
        //$unidades = floor($pedido->cantidad * $pedido->uxb);        
        echo '<tr>';
        echo "<td>$pedido->id</td>";
        echo "<td>".(isset($pedido->codigo_proveedor)?$pedido->codigo_proveedor:"0")."</td>";
        //echo "<td style='text-align:left'>$cantidad</td>";
        //echo "<td style='text-align:left'>$uxb</td>";
        //echo "<td style='text-align:left'>$unidades</td>";
        echo "<td style='text-align:left'>$total</td>";
        echo "<td>".utf8_decode($pedido->descripcion)."</td>";
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
.tabla tr td {
    padding-top: 3px;
    padding-bottom: 3px;
}
</style>
</head>
<body>
<div class="page">
  <div class="p3mm">    
    <div class="header oh">
        <div class="subtitulo fl">
            PEDIDO AGRUPADO
        </div>
        <div class="fr">
            <span>Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span></span>
        </div>
    </div>
    
    <?php /*foreach($resultados as $clave => $row) { ?>
        
        <div class="subtitulo">
            <?php echo utf8_decode($clave); ?>
        </div>
        
        <?php
        $total_filas = sizeof($row);
        $mitad = ceil($total_filas/2);
        imprimir_tabla($row,0,$mitad);
        if ($mitad != $total_filas) imprimir_tabla($row,$mitad,$total_filas);
        ?>

        <div class="bbs mt5 mb5 cb"></div>
        
    <?php }*/ ?>
    
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
    
  </div>
</div>
</body>
</html>