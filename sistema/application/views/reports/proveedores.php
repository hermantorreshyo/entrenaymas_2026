<?php
function imprimir_tabla($r,$i,$hasta) {
    $s = '';
    $s.= '<table class="tabla">';
    $s.= '  <thead>';
    $s.= '      <tr>';
    $s.= '          <th style="width:25px">Cod.</th>';
    $s.= '          <th style="width:220px">Razon Social</th>';
    $s.= '          <th style="width:80px;">CUIT</th>';
    $s.= '          <th style="width:170px">Telefono / Email</th>';
    $s.= '          <th style="width:160px">Direccion</th>';
    $s.= '          <th style="width:115px">Localidad</th>';
    $s.= '      </tr>';
    $s.= '</thead>';
    $s.= '<tbody>';
    for ($i;$i<$hasta;$i++) {
        $prov = $r[$i];
        $s.='<tr>';
        $s.=  "<td>$prov->codigo</td>";
        $s.=  "<td class='bold'>".utf8_decode($prov->nombre)."</td>";
        $s.=  "<td>$prov->cuit</td>";
        $s.=  "<td>$prov->telefono<br/>$prov->email</td>";
        $s.=  "<td>".utf8_decode($prov->direccion)."</td>";
        $s.=  "<td>".utf8_decode($prov->localidad)."</td>";
        $s.="</tr>";
        $s.='<tr style="border-top: none">';
        $s.=  "<td></td>";
        $s.=  "<td colspan='6'>";
        $s.=  utf8_decode($prov->tipo_iva);
        if (!empty($prov->contacto)) $s.=  "&nbsp;&nbsp;&nbsp; Vendedor: ".utf8_decode($prov->contacto);
        if (!empty($prov->contacto_telefono)) $s.=  "&nbsp;&nbsp;&nbsp; Tel. Vendedor: ".utf8_decode($prov->contacto_telefono);
        $s.=  "</td>";
        $s.="</tr>";
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
.tabla {
    table-layout: fixed;
}
.tabla tbody tr {
    border-top: solid 1px #CCC;
    border-bottom: none;
}
.tabla tr td {
    padding-bottom: 4px;
    padding-top: 4px;
    padding-right: 4px;
    vertical-align: top;
}
</style>
</head>
<body>
    
<div class="page">
    <div class="p3mm">    
        <div class="header oh">
            <div class="subtitulo fl">
                <span class="bold">
                    LISTADO DE PROVEEDORES
                </span>
            </div>
            <div class="fr">
                Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
            </div>
        </div>
        
        <?php $total_filas = sizeof($resultados);
        echo imprimir_tabla($resultados,0,$total_filas);?>
        
    </div>
</div>

</body>
</html>