<?php
$total_costo = 0;
function imprimir_tabla($row,$i,$hasta) {
    $total_costo = 0;
    
    echo '<table class="tabla w50p fl">';
    echo '  <thead>';
    echo '      <tr>';
    echo '          <th style="width:40px">Int.</th>';
    echo '          <th style="width:40px">Prov.</th>';    
    //echo '          <th style="width:32px">Bul</th>';
    //echo '          <th style="width:32px">UxB</th>';
    //echo '          <th style="width:32px">Unid</th>';
    echo '          <th style="width:55px">Cant.</th>';
    echo '          <th style="width:40px">Costo F</th>';
    echo '          <th>Descripcion</th>';
    echo '      </tr>';
    echo '</thead>';
    echo '<tbody>';
    for ($i;$i<$hasta;$i++) {
        $pedido = $row[$i];
        
        $cantidad = str_replace(".00","",$pedido->cantidad);
        $uxb = str_replace(".00","",$pedido->uxb);
        $unidades = floor($pedido->cantidad * $pedido->uxb);
        
        if (strpos($cantidad,".") === FALSE) {
            if ($uxb == 1) {
                $total = "$cantidad Un";
            } else {
                $total = $cantidad." B x $uxb u";    
            }
        } else {
            $total = floor($pedido->cantidad * $pedido->uxb)." Un";
        }
        
        $total = $cantidad." B x $uxb u";
        
        echo '<tr>';
        echo "<td>$pedido->id</td>";
        echo "<td>".(isset($pedido->codigo_proveedor)?$pedido->codigo_proveedor:"0")."</td>";
        
        //echo "<td style='text-align:left'>$cantidad</td>";
        //echo "<td style='text-align:left'>$uxb</td>";
        //echo "<td style='text-align:left'>$unidades</td>";        
        
        echo "<td style='text-align:left'>$total</td>";
        echo "<td style='text-align:left'>".number_format($pedido->costo_final,2)."</td>";
        
        $total_costo = $total_costo + $pedido->costo_final;
        
        echo "<td>".mb_convert_encoding($pedido->descripcion, 'ISO-8859-1', 'UTF-8')."</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    return $total_costo;
}
?>
<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
.tabla tr td {
    padding-bottom: 3px;
    padding-top: 3px;
}
</style>
</head>
<body>
<div class="page">
  <div class="p2mm">    
    <div class="header oh">
        <div class="subtitulo fl">
            NOTA DE PEDIDO
            <span class="fwn">Proveedor: </span><?php echo mb_convert_encoding($proveedor->nombre, 'ISO-8859-1', 'UTF-8'); ?> <span class="fwn">(<?php echo mb_convert_encoding($proveedor->codigo, 'ISO-8859-1', 'UTF-8'); ?>)</span>
            <span class="fwn ml15">Entrega: </span><?php echo $pedido->sucursal ?>
            <span class="fwn ml15">Domicilio: </span><?php echo $pedido->direccion_sucursal; ?>
        </div>
        <div class="fr">
            <span>Fecha: <span class="bold"><?php echo $pedido->fecha ?></span></span><br/>
            <span>Pedido Nro: <span class="bold"><?php echo $pedido->numero ?></span></span>
        </div>
    </div>
    
    <?php foreach($resultados as $clave => $row) { ?>
        
        <div class="subtitulo">
            <?php echo mb_convert_encoding($clave, 'ISO-8859-1', 'UTF-8'); ?>
        </div>
        
        <?php foreach($row as $c => $r) { ?>
            <div class="subtitulo2">
                <?php echo mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'); ?>
            </div>
            <div class="oh">    
            <?php
            $total_filas = sizeof($r);
            $mitad = ceil($total_filas/2);
            $total_costo += imprimir_tabla($r,0,$mitad);
            if ($mitad != $total_filas) $total_costo += imprimir_tabla($r,$mitad,$total_filas);
            ?>
            </div>
        <?php } ?>
        
        <div class="bbs mt5 mb5 cb"></div>
        
    <?php } ?>
    
    <div class="fs12">
        <b>TOTAL: </b><?php echo "$ ".number_format($total_costo,2); ?>
    </div>    
    
    
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
                    echo "<td>".mb_convert_encoding($d->descripcion, 'ISO-8859-1', 'UTF-8')."</td>";
                    echo "</tr>";                
                } ?>
                </tbody>
            </table>
        </div>      
    <?php } ?>
        
  </div>
</div>
</body>
</html>