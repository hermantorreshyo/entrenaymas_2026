<?php
function f($n) {
    return number_format($n,2,",",".");
}

function encabezado($nro_pagina, $fecha_desde, $fecha_hasta, $empresa) {
    $s = "";
    $s.= "<div style='overflow:hidden; margin-bottom:20px'>";
        $s.="<div style='float:right; font-size: 12px; text-align: right'>";
            $s.="Hoja Nro: $nro_pagina<br/>";
            $s.="Desde el: $fecha_desde<br/>";
            $s.="Hasta el: $fecha_hasta";
        $s.="</div>";
        $s.="<div style='float:left; font-size: 12px'>";
            $s.= $empresa->razon_social."<br/>";
            $s.= "CUIT: $empresa->cuit<br/>";
            $s.= "Domicilio: $empresa->direccion $empresa->localidad<br/>";
        $s.="</div>";
        $s.="<h1 style='font-size: 16px; font-weight:bold; text-align: center'>Libro IVA Compras</h1>";
    $s.="</div>";
    return $s;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
table {
    border-collapse: collapse;
    width: 100%;
}
table td {
    padding: 3px 8px;
    font-size: 10px;
}
.totales td {
    padding: 3px 8px;
    font-size: 12px;    
}

.total_gral {
    margin-top: 30px;
    border-top: solid 2px black;
    border-bottom: solid 2px black;
    padding: 10px;
}
.total_gral td {
    padding: 10px 8px;
    font-size: 14px;
    font-weight:bold;
    font-style: italic;
    text-align: right;
}
tfoot td {
    background-color: #e5e5e5;
    font-weight:bold;
    padding: 7px 4px;
}
</style>

</head>
<body>

<!--<div style="position: absolute; top: 0px; left: 0px; width: 297mm; height: 210mm; border: solid 1px black;"></div>-->

<div class="a4">
<?php
$cerro = FALSE;
$i=0;
$nro_pagina = $inicio + 1;

$total_exento = 0;
$total_perc_ib = 0;
$total_perc_iva = 0;
$total_imp_interno = 0;
$total_neto = 0;
$total_iva = 0;
$total_general = 0;

$numero_anterior = "";

$ivas = array();
$ivas[0] = array(0,0); // IVA 0
$ivas[1] = array(0,0); // IVA 10.50
$ivas[2] = array(0,0); // IVA 21.00
$ivas[3] = array(0,0); // IVA 27.00
$ivas[4] = array(0,0); // IVA -10.50  Para discriminar NC
$ivas[5] = array(0,0); // IVA -21.00
$ivas[6] = array(0,0); // IVA -27.00

foreach ($datos as $r) {
    
    if ($i == 0) {
        echo "<div class='a4'>";
        echo encabezado($nro_pagina, $fecha_desde, $fecha_hasta, $empresa);
        echo "<table>";
        echo '<thead style="border-top: solid 2px black; border-bottom: solid 2px black; font-size: 12px; margin: 15px 0px; padding: 10px;">';
        echo "<tr>";
        echo "<th>Fecha</th>";
        echo "<th>Comprobante</th>";
        echo "<th>Razon Social</th>";
        echo "<th>C.U.I.T.</th>";
        echo "<th>Nro. IB</th>";
        echo "<th class='tar'>NIns,NRes,Ex,<br/>Mon,NCat</th>";
        echo "<th class='tar'>Perc IB</th>";
        echo "<th class='tar'>Perc IVA</th>";
        echo "<th class='tar'>Imp Int</th>";
        echo "<th class='tar'>Neto</th>";
        echo "<th class='tar'>% IVA</th>";
        echo "<th class='tar'>IVA</th>";
        echo "<th class='tar'>Total</th>";
        echo "</tr>";
        echo "</thead>";
        $cerro = FALSE;
    }
    
    // Creamos cada fila
    echo "<tr>";

    echo "<td>".$r->fecha."</td>";
    
    echo "<td>";
    switch($r->id_tipo_comprobante) {
        case 1: echo "FC A "; break;
        case 3: echo "NC "; break;
        case 4: echo "ND "; break;
        case 5: echo "FC B "; break;
        case 6: echo "FC C "; break;
        case 9: echo "R "; break;
    }
    echo $r->numero_1."-".$r->numero_2;
    echo "</td>";
    
    // Si no cambio de comprobante
    if ($numero_anterior != ($r->id_proveedor."-".$r->numero_1."-".$r->numero_2)) {
    
        echo "<td>".substr($r->nombre,0,30)."</td>";
        echo "<td>".$r->cuit."</td>";
        
        // Mostramos el Nro Ing Brutos si es distinto al CUIT
        echo "<td>".(($r->cuit != $r->convenio_multilateral) ? $r->convenio_multilateral : "")."</td>";
    
        // NIns,NRes,Ex,Mon,NCat
        echo "<td class='tar'>";
        if ($r->id_tipo_comprobante == 6) echo $r->total_general;
        else echo $r->exento;    
        echo "</td>";
        
        echo "<td class='tar'>".$r->perc_ing_brutos."</td>";
        echo "<td class='tar'>".$r->perc_iva."</td>";
        echo "<td class='tar'>".$r->impuesto_interno."</td>";
    
        echo "<td class='tar'>";
        if ($r->id_tipo_comprobante == 6) echo "0.00";
        else echo $r->neto;
        echo "</td>";
        
        echo "<td class='tar'>".number_format(abs($r->porc_iva),2)."</td>";
        echo "<td class='tar'>".$r->iva."</td>";
        echo "<td class='tar'>".$r->total_general."</td>";
        
        $total_exento += $r->exento;
        if ($r->id_tipo_comprobante == 6) $total_exento += $r->neto;
        $total_perc_ib += $r->perc_ing_brutos;
        $total_perc_iva += $r->perc_iva;
        $total_imp_interno += $r->impuesto_interno;
        $total_general += $r->total_general;
        if ($r->id_tipo_comprobante != 6) $total_neto += $r->neto;
        if ($r->id_tipo_comprobante != 6) $total_iva += $r->iva;
        
        
    }
    // Si cambio de comprobante
    else {
        
        echo "<td></td>"; // Razon Social
        echo "<td></td>"; // CUIT
        echo "<td></td>"; // Nro Ing Brutos
        echo "<td class='tar'>0.00</td>"; // NIns,NRes,Ex,Mon,NCat
        echo "<td class='tar'>0.00</td>"; // Perc Ing Brutos
        echo "<td class='tar'>0.00</td>"; // Perc Iva
        echo "<td class='tar'>0.00</td>"; // Imp Interno
        
        echo "<td class='tar'>";
        if ($r->id_tipo_comprobante == 6) echo "0.00";
        else echo $r->neto;
        echo "</td>";
        
        echo "<td class='tar'>".number_format(abs($r->porc_iva),2)."</td>";
        echo "<td class='tar'>".$r->iva."</td>";
        echo "<td class='tar'></td>";    
        
        if ($r->id_tipo_comprobante != 6) $total_neto += $r->neto;
        if ($r->id_tipo_comprobante != 6) $total_iva += $r->iva;
        
    }
        
    $numero_anterior = $r->id_proveedor."-".$r->numero_1."-".$r->numero_2;
        
    echo "</tr>"; // Cerramos la fila
    
    // Si se discrimina IVA
    if ($r->id_tipo_comprobante != 6) {
        
        if ($r->porc_iva === "0.00") $indice = 0;
        else if ($r->porc_iva === "10.50") $indice = 1;
        else if ($r->porc_iva === "21.00") $indice = 2;
        else if ($r->porc_iva === "27.00") $indice = 3;
        else if ($r->porc_iva === "-10.50") $indice = 4;
        else if ($r->porc_iva === "-21.00") $indice = 5;
        else if ($r->porc_iva === "-27.00") $indice = 6;
        
        $ivas[$indice] = array(
            $r->neto + $ivas[$indice][0],
            $r->iva  + $ivas[$indice][1]
        );
    }    
    
    if ($i==32) {
        echo "</tbody></table></div>";
        $i=0;
        $cerro = TRUE;
        $nro_pagina++;
    } else $i++;
}

if (!$cerro) {
    echo "</tbody>";
    echo "<tfoot><tr>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td class='tar'>".f($total_exento)."</td>";
    echo "<td class='tar'>".f($total_perc_ib)."</td>";
    echo "<td class='tar'>".f($total_perc_iva)."</td>";
    echo "<td class='tar'>".f($total_imp_interno)."</td>";
    echo "<td class='tar'>".f($total_neto)."</td>";
    echo "<td></td>";
    echo "<td class='tar'>".f($total_iva)."</td>";
    echo "<td class='tar'>".f($total_exento + $total_perc_ib + $total_perc_iva + $total_imp_interno + $total_neto + $total_iva)."</td>";
    echo "</tr></tfoot>";
    echo "</table></div>";
}

$nro_pagina++;
?>

<div class="a4">
    <?php echo encabezado($nro_pagina, $fecha_desde, $fecha_hasta, $empresa); ?>

    <div style="border-top: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px 0px 0px; font-style: italic; padding: 10px;">
        TOTALES DE OPERACIONES QUE GENERAN CREDITO FISCAL
    </div>
    
    <table class="totales" style="border-top: solid 2px black; font-weight: bold; font-size: 14px; font-style: italic; padding: 10px;">
    <tr>
        <td style="width: 25%"></td>
        <td style="width: 12.5%" class="tar">No Grav.</td>
        <td style="width: 12.5%" class="tar">Reg. Especiales</td>
        <td style="width: 12.5%" class="tar">NIns,NRes,Mon,Ncat</td>
        <td style="width: 12.5%" class="tar">Neto Gravado</td>
        <td style="width: 12.5%" class="tar">IVA</td>
        <td style="width: 12.5%" class="tar">Total</td>
    </tr>
    <tr>
        <td style="width: 25%">Tasa IVA 10,50 %</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[1][0]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[1][1]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[1][0] + $ivas[1][1]);?></b></td>
    </tr>
    <tr>
        <td style="width: 25%">Tasa IVA 21,00 %</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[2][0]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[2][1]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[2][0] + $ivas[2][1]);?></b></td>
    </tr>
    <tr>
        <td style="width: 25%">Tasa IVA 27,00 %</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[3][0]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[3][1]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[3][0] + $ivas[3][1]);?></b></td>
    </tr>
    <tr>
        <td style="width: 25%">Compras NI, NR, Exento, Monot. o NCat</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($total_exento);?></b></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($total_exento);?></b></td>
    </tr>    
    <tr>
        <td style="width: 25%">Impuesto Interno</td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($total_imp_interno);?></b></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($total_imp_interno);?></b></td>
    </tr>
    <tr>
        <td style="width: 25%">Impuesto al Debito y Credito</td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[0][0]);?></b></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[0][0]);?></b></td>
    </tr>      
    
    
    <tr style="border-top: solid 1px black">
        <td style="width: 25%">Subtotales</td>
        <td style="width: 12.5%" class="tar"><?php echo f($total_imp_interno + $ivas[0][0]);?></td>
        <td style="width: 12.5%" class="tar"></td>
        <td style="width: 12.5%" class="tar"><?php echo f($total_exento); ?></td>
        <td style="width: 12.5%" class="tar"><b><?php $neto_credito = $ivas[1][0]+$ivas[2][0]+$ivas[3][0]; echo f($neto_credito);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php $iva_credito = $ivas[1][1]+$ivas[2][1]+$ivas[3][1]; echo f($iva_credito);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php $total_credito_fiscal = $ivas[1][0]+$ivas[2][0]+$ivas[3][0]+$ivas[1][1]+$ivas[2][1]+$ivas[3][1]+$total_exento+$ivas[0][0]+$total_imp_interno; echo f($total_credito_fiscal);?></b></td>    
    </tr>
    </table>
    
    <div style="border-top: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px 0px 0px; font-style: italic; padding: 10px;">
        REGIMENES ESPECIALES
    </div>
    
    <table class="totales" style="border-top: solid 2px black; font-weight: bold; font-size: 14px; font-style: italic; padding: 10px;">
    <tr>
        <td style="width: 25%">Percepcion Ingresos Brutos</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><?php echo f($total_perc_ib);?></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><?php echo f($total_perc_ib);?></td>
    </tr>
    <tr>
        <td style="width: 25%">Percepcion IVA</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><?php echo f($total_perc_iva);?></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><?php echo f($total_perc_iva);?></td>
    </tr>
    <tr style="border-top: solid 1px black">
        <td style="width: 25%">Subtotales</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php $total_regimenes_especiales = $total_perc_ib + $total_perc_iva; echo f($total_regimenes_especiales);?></b></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"></td>
        <td style="width: 12.5%" class="tar"></td>
        <td style="width: 12.5%" class="tar"><?php echo f($total_regimenes_especiales);?></td>
    </tr>
    </table>
    
    
    <div style="border-top: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px 0px 0px; font-style: italic; padding: 10px;">
        TOTALES DE OPERACIONES QUE GENERAN DEBITO FISCAL
    </div>
    
    <table class="totales" style="border-top: solid 2px black; font-weight: bold; font-size: 14px; font-style: italic; padding: 10px;">
    <tr>
        <td style="width: 25%">Tasa IVA 10,50 %</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[4][0]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[4][1]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[4][0] + $ivas[4][1]);?></b></td>
    </tr>
    <tr>
        <td style="width: 25%">Tasa IVA 21,00 %</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[5][0]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[5][1]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[5][0] + $ivas[5][1]);?></b></td>
    </tr>
    <tr>
        <td style="width: 25%">Tasa IVA 27,00 %</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[6][0]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[6][1]);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php echo f($ivas[6][0] + $ivas[6][1]);?></b></td>
    </tr>    
    <tr style="border-top: solid 1px black">
        <td style="width: 25%">Subtotales</td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%"></td>
        <td style="width: 12.5%" class="tar"><b><?php $neto_debito = $ivas[4][0]+$ivas[5][0]+$ivas[6][0]; echo f($neto_debito);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php $iva_debito = $ivas[4][1]+$ivas[5][1]+$ivas[6][1]; echo f($iva_debito);?></b></td>
        <td style="width: 12.5%" class="tar"><b><?php $total_debito_fiscal = $ivas[4][0]+$ivas[5][0]+$ivas[6][0]+$ivas[4][1]+$ivas[5][1]+$ivas[6][1]; echo f($total_debito_fiscal);?></b></td>    
    </tr>
    </table>
    
    <table class="total_gral">
    <tr>
        <td style="width: 25%" class="tal">TOTALES AL <?php echo $fecha_hasta ?></td>
        <td style="width: 12.5%"><?php echo f($total_imp_interno + $ivas[0][0]);?></td>
        <td style="width: 12.5%"><?php echo f($total_regimenes_especiales); ?></td>
        <td style="width: 12.5%"><?php echo f($total_exento); ?></td>
        <td style="width: 12.5%"><?php echo f($neto_credito + $neto_debito); ?></td>
        <td style="width: 12.5%"><?php echo f($iva_credito + $iva_debito); ?></td>
        <td style="width: 12.5%"><?php echo f($total_credito_fiscal + $total_debito_fiscal + $total_regimenes_especiales); ?></td>
    </tr>
    </table>
</div>
</div>
</body>
</html>