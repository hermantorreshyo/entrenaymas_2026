<?php
$total = 0;
function imprimir_tabla($row) {
    global $total;
    echo '<table class="tabla">';
    echo '  <thead>';
    echo '      <tr>';
    echo '          <th style="width:70px">Cod.</th>';
    echo '          <th style="width:70px">Prov.</th>';
    echo '          <th style="width:100px">Cod. Barra</th>';
    echo '          <th style="width:40px">UxB</th>';
    echo '          <th style="width:40px">Stk</th>';
    echo '          <th style="width:40px">Bultos</th>';
    echo '          <th>Articulo</th>';
    echo '          <th style="width:70px">Precio</th>';
    echo '          <th style="width:70px">Ult. Alta</th>';
    echo '          <th style="width:70px">Ult. Baja</th>';
    echo '      </tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($row as $stock) {
        $codigo = trim($stock->codigo);
        $codigo_prov = trim($stock->custom_10);
        $codigo_barra = trim($stock->codigo_barra);
        $stock_minimo = str_replace(".00","",$stock->stock_minimo);
        $stock_actual = str_replace(".00","",$stock->stock_actual);
        $uxb = str_replace(".00","",$stock->uxb);
        if ($uxb == 0) $uxb = "";
        echo '<tr>';
        echo "<td>$codigo</td>";
        echo "<td>$codigo_prov</td>";
        echo "<td>";
        $codigo_barra = explode("###",$codigo_barra);
        foreach($codigo_barra as $cod) {
          echo ($cod != $stock->codigo) ? $cod."<br/>" : "";
        }
        echo "</td>";
        echo "<td>$uxb</td>";
        echo "<td>$stock_actual</td>";
        echo "<td>";
        if ($uxb != 0) {
            if (($stock_actual % $uxb) == 0) {
                echo round($stock_actual/$uxb,2);
            } else {
                if (floor($stock_actual/$uxb) != 0) {
                    echo floor($stock_actual/$uxb)." B ".($stock_actual % $uxb)." u";    
                } else {
                    echo ($stock_actual % $uxb)." u";    
                }
            }            
        } else echo "";
        echo "</td>";
        echo "<td>".($stock->nombre)."</td>";
        echo "<td>$ ".$stock->precio_final."</td>";
        echo "<td>".$stock->fecha_ult_compra."</td>";
        echo "<td>".$stock->fecha_ult_venta."</td>";
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
            STOCK
            <?php if(!empty($almacen)) { ?>
                DE <?php echo mb_convert_encoding($almacen->nombre, 'ISO-8859-1', 'UTF-8'); ?>
            <?php } ?>
            <?php if(!empty($proveedor)) { ?>
                DE <?php echo ($proveedor->nombre); ?>
            <?php } ?>
        </div>
        <div class="fr">
            <span>Fecha: <span class="bold"><?php echo date("d/m/Y") ?></span></span>
        </div>
    </div>
    
    <?php foreach($resultados as $clave => $row) { ?>
        
        <div class="subtitulo">
            <?php echo ($clave); ?>
        </div>
        
        <div class="oh">    
        <?php echo imprimir_tabla($row); ?>
        </div>
        
        <div class="bbs mt5 mb5 cb"></div>
        
    <?php } ?>

</div>
</body>
</html>