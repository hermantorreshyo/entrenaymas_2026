<?php
function imprimir_tabla($r,$i,$hasta) {
    $s = '';
    $s.= '<table class="tabla w48p fl mr10">';
    $s.= '  <thead>';
    $s.= '      <tr>';
    $s.= '          <th style="width:40px">Int.</th>';
    $s.= '          <th style="width:40px">PLU</th>';
    $s.= '          <th>Descripcion</th>';
    $s.= '          <th style="width:40px">Precio</th>';
    $s.= '      </tr>';
    $s.= '</thead>';
    $s.= '<tbody>';
    for ($i;$i<$hasta;$i++) {
        $f = $r[$i];
        $s.= '<tr>';
        $s.= "<td>$f->codigo</td>";
        $s.= "<td>".$f->nplu."</td>";
        $s.= "<td>".utf8_decode($f->nombre)."</td>";
        $s.= "<td class='tar pr10'>".$f->precio_final."</td>";
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
<script type="text/javascript" src="/sistema/resources/js/jquery.js"></script> 
<style type="text/css">
body { background-color: white; }
.tabla tr td {
    padding-top: 3px;
    padding-bottom: 3px;
    font-size: 10px;
}
</style>
</head>
<body>
    
<div class="page">
  <div class="p3mm">    
        <div class="header oh">
            <div class="subtitulo fl">
                <span class="bold">LISTA DE PLUS GRABADOS EN BALANZA</span>
            </div>
            <div class="fr">
                Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
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
        
  </div>
</div>

</body>
</html>