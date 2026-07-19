<?php
function imprimir_tabla($r,$i,$hasta) {
    $s = '';
    $s.= '<table class="tabla w50p fl">';
    $s.= '  <thead>';
    $s.= '      <tr>';
    $s.= '          <th style="width:40px">Int.</th>';
    //$s.= '          <th style="width:35px">Bul.</th>';
    //$s.= '          <th style="width:35px">UxB</th>';
    //$s.= '          <th style="width:35px">Unid.</th>';
    $s.= '          <th style="width:105px">Cant.</th>';
    $s.= '          <th>Descripcion</th>';
    $s.= '      </tr>';
    $s.= '</thead>';
    $s.= '<tbody>';
    for ($i;$i<$hasta;$i++) {
        $faltante = $r[$i];
        $cantidad = str_replace(".00","",$faltante->cantidad);
        $uxb = str_replace(".00","",$faltante->uxb);
        $unidades = floor($faltante->cantidad * $faltante->uxb);

        if (strpos($cantidad,".") === FALSE) {
            if ($uxb == 1) {
                $total = "$cantidad Un";
            } else {
                $total = $cantidad." B x $uxb u";    
            }
        } else {
            $total = floor($faltante->cantidad * $faltante->uxb)." Un";
        }
        
        $s.= '<tr>';
        $s.= "<td>$faltante->id_articulo</td>";
        //$s.= "<td>$cantidad</td>";
        //$s.= "<td>$uxb</td>";
        //$s.= "<td>$unidades</td>";
        $s.= "<td>$total</td>";
        $s.= "<td>".mb_convert_encoding($faltante->descripcion, 'ISO-8859-1', 'UTF-8')."</td>";
        $s.= "</tr>";
    }
    $s.= "</tbody>";
    $s.= "</table>";
    return $s;
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
            <span class="bold">
                <?php if ($id_sucursal == 0) { ?>
                    FALTANTE GENERAL
                <?php } else { ?>
                    FALTANTE DE <?php echo $id_sucursal; ?>
                <?php } ?>
                <?php if ($id_proveedor != 0 && !empty($proveedor->nombre)) { ?>
                    DE <?php echo $proveedor->nombre; ?>
                <?php } ?>
            </span>
        </div>
        <div class="fr">
            Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
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