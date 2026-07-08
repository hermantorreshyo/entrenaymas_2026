<?php
function f($n) {
  return number_format($n,2,",",".");
}

function encabezado($nro_pagina, $fecha_desde, $fecha_hasta, $empresa) {
  $s = "";
  $s.= "<div style='overflow:hidden; margin-bottom:20px'>";
    $s.="<div style='float:right; font-size: 12px; text-align: right; margin-right:10px'>";
      $s.="Hoja Nro: $nro_pagina<br/>";
      $s.="Desde el: $fecha_desde<br/>";
      $s.="Hasta el: $fecha_hasta";
    $s.="</div>";
    $s.="<div style='float:left; font-size: 12px'>";
      $s.= $empresa->razon_social."<br/>";
      $s.= "CUIT: $empresa->cuit<br/>";
      $s.= "Domicilio: $empresa->direccion $empresa->localidad<br/>";
    $s.="</div>";
    $s.="<h1 style='font-size: 16px; font-weight:bold; text-align: center'>Libro IVA Ventas</h1>";
  $s.="</div>";
  return $s;
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
.inner { padding: 15px; }
.tar { text-align: right !important; }
.tal { text-align: left !important; }
table {
  border-collapse: collapse;
  width: 100%;
}
table tr th { text-align: center; font-size: 8px; }
table td {
  padding: 2px 6px;
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
<?php echo $header; ?>
<?php
$nro_pagina = $pagina_desde;
$total = 0; $total_neto = 0; $total_iva = 0;
$credito_fiscal = array();
$debito_fiscal = array();

for($i=0;$i<10;$i++) {
  $credito_fiscal[] = array("iva"=>0,"neto"=>0,"ri_iva"=>0,"ri_neto"=>0,"cf_iva"=>0,"cf_neto"=>0,"mon_iva"=>0,"mon_neto"=>0);
  $debito_fiscal[] = array("iva"=>0,"neto"=>0,"ri_iva"=>0,"ri_neto"=>0,"cf_iva"=>0,"cf_neto"=>0,"mon_iva"=>0,"mon_neto"=>0);
}
$percepcion_ib = 0;
$percep_viajes = 0;
$cerro = FALSE;
$i=0;
foreach ($datos as $r) {
    
  if ($i == 0) {
    echo "<div class='a4p'><div class='inner'>";
    echo encabezado($nro_pagina, $fecha_desde, $fecha_hasta, $empresa);        
    echo "<table>";
    echo '<thead style="border-top: solid 2px black; border-bottom: solid 2px black; font-size: 12px; margin: 15px 0px; padding: 10px;">';
    echo "<tr>";
    echo "<th>Fecha</th>";
    echo "<th>Comprobante</th>";
    echo "<th>Razon Social</th>";
    echo "<th>CUIT</th>";
    foreach($alicuotas as $a) {
      echo "<th style='width:50px'>Neto $a->nombre</th>";
      if ($a->porcentaje > 0) echo "<th style='width:50px'>IVA $a->nombre</th>";
    }
    if (sizeof($alicuotas)>1 && $empresa->id != 121) { 
      echo "<th style='width:50px'>Neto</th>";
      echo "<th style='width:50px'>IVA</th>";
    }
    echo "<th style='width:50px'>Perc. IIBB</th>";
    echo "<th style='width:50px'>Otras Perc.</th>";
    echo "<th style='width:50px'>Total</th>";
    echo "</tr>";
    echo "</thead>";
    $cerro = FALSE;
  }
    
  // Creamos cada fila
  echo "<tr>";
  echo "<td>".$r->fecha."</td>";
  $tipo_comprobante = str_replace("Nota Credito", "NC",$r->tipo_comprobante);
  $tipo_comprobante = str_replace("Nota Debito", "ND",$tipo_comprobante);
  $tipo_comprobante = str_replace("Factura", "F",$tipo_comprobante);
  $desde = end(explode(" ",$r->desde));
  if ($r->comp_desde != $r->comp_hasta) {
    echo "<td>FB $r->punto_venta $r->comp_desde-$r->comp_hasta</td>";
  } else {
    echo "<td>$tipo_comprobante $desde</td>";
  }
  echo "<td>";
  echo substr($r->cliente,0,30);
  if ($r->anulada == 1) {
    echo " (ANULADA)";
    $r->neto = 0;
    $r->iva = 0;
    $r->percepcion_ib = 0;
    $r->percep_viajes = 0;
    $r->total = 0;
  }    
  echo "</td>";
  echo "<td>".((strlen($r->cuit)==11)?(substr($r->cuit,0,2)."-".substr($r->cuit,2,8)."-".substr($r->cuit,10)):$r->cuit)."</td>";
  foreach($alicuotas as $a) {
    echo "<td class='tar'>";
    if ($r->anulada == 1) { $r->{"neto_$a->id"} = 0; }
    echo f($r->{"neto_$a->id"});
    echo "</td>";
    if ($a->porcentaje>0) {
      echo "<td class='tar'>";
      if ($r->anulada == 1) { $r->{"iva_$a->id"} = 0; }
      echo f($r->{"iva_$a->id"});
      echo "</td>";            
    }
  }
  if (sizeof($alicuotas)>1 && $empresa->id != 121) {
    echo "<td class='tar'>".f($r->neto)."</td>";
    echo "<td class='tar'>".f($r->iva)."</td>";
  }
  echo "<td class='tar'>".f($r->percepcion_ib)."</td>";
  echo "<td class='tar'>".f($r->percep_viajes)."</td>";
  // TODO: ARREGLAR EL TEMA DE LOS TOTALES Y LAS PERCEPCIONES
  echo "<td class='tar'>".f($r->total + $r->percep_viajes)."</td>";
  echo "</tr>"; // Cerramos la fila
    
  // Es una NOTA DE CREDITO, genera CREDITO FISCAL
  if ($r->negativo == 1) {
      
    foreach($alicuotas as $a) {
      $credito_fiscal[$a->id]["iva"] += (float)$r->{"iva_$a->id"};
      $credito_fiscal[$a->id]["neto"] += (float)$r->{"neto_$a->id"};

      // Dependiendo del tipo de iva del cliente
      if ($r->id_tipo_iva == 1) $sujeto = "ri";
      else if ($r->id_tipo_iva == 2) $sujeto = "mon";
      else $sujeto = "cf";
      $credito_fiscal[$a->id][$sujeto."_iva"] += (float)$r->{"iva_$a->id"};
      $credito_fiscal[$a->id][$sujeto."_neto"] += (float)$r->{"neto_$a->id"};        

    }
      
  // Es una FACTURA o NOTA DE DEBITO, genera DEBITO FISCAL
  } else {

    foreach($alicuotas as $a) {
      $debito_fiscal[$a->id]["iva"] += (float)$r->{"iva_$a->id"};
      $debito_fiscal[$a->id]["neto"] += (float)$r->{"neto_$a->id"};

      // Dependiendo del tipo de iva del cliente
      if ($r->id_tipo_iva == 1) $sujeto = "ri";
      else if ($r->id_tipo_iva == 2) $sujeto = "mon";
      else $sujeto = "cf";
      $debito_fiscal[$a->id][$sujeto."_iva"] += (float)$r->{"iva_$a->id"};
      $debito_fiscal[$a->id][$sujeto."_neto"] += (float)$r->{"neto_$a->id"};        

    }        
  }
  $percepcion_ib += (float) $r->percepcion_ib;
  $percep_viajes += (float) $r->percep_viajes;

  $total_neto += $r->neto;
  $total_iva += $r->iva;
  $total += $r->total;
  
  if ($i==38) {
    echo "</tbody></table></div></div>";
    $i=0;
    $cerro = TRUE;
    $nro_pagina++;
  } else $i++;
}
if (!$cerro) echo "</tbody></table></div></div>";

//echo "NETO: $total_neto <br/> IVA: $total_iva <br/> TOTAL: $total";
$nro_pagina++;
?>
<div class="a4p">
  <div class="inner">
    <?php echo encabezado($nro_pagina, $fecha_desde, $fecha_hasta, $empresa); ?>
  
    <div style="border-top: solid 2px black; border-bottom: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
      TOTALES DE OPERACIONES QUE GENERAN DEBITO FISCAL
    </div>
      
    <table class="totales" style="border-top: solid 2px black; border-bottom: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
      <tr>
        <td style="width: 30%"></td>
        <td style="width: 17.5%"></td>
        <td style="width: 17.5%">Neto</td>
        <td style="width: 17.5%">IVA</td>
        <td style="width: 17.5%">Total</td>
      </tr>
      <?php
      $df_neto = 0; $df_iva = 0;
      foreach($alicuotas as $a) {
        $df_neto += $debito_fiscal[$a->id]["neto"];
        $df_iva += $debito_fiscal[$a->id]["iva"];
        ?>
        <tr>
          <td style="width: 30%">Tasa <?php echo $a->nombre ?></td>
          <td style="width: 17.5%"></td>
          <td style="width: 17.5%"><b><?php echo f($debito_fiscal[$a->id]["neto"]);?></b></td>
          <td style="width: 17.5%"><b><?php echo f($debito_fiscal[$a->id]["iva"]);?></b></td>
          <td style="width: 17.5%"><b><?php echo f($debito_fiscal[$a->id]["neto"] + $debito_fiscal[$a->id]["iva"]);?></b></td>
        </tr>
      <?php } ?>
      <tr style="border-top: solid 1px black">
        <td style="width: 30%"></td>
        <td style="width: 17.5%"></td>
        <td style="width: 17.5%"><b><?php echo f($df_neto);?></b></td>
        <td style="width: 17.5%"><b><?php echo f($df_iva);?></b></td>
        <td style="width: 17.5%"><b><?php echo f($df_neto + $df_iva);?></b></td>
      </tr>
    </table>
      
    <table class="totales" style="border-top: solid 2px black; border-bottom: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
      <tr>
        <td style="width: 30%">Percepcion IB</td>
        <td style="width: 17.5%"><?php echo f($percepcion_ib);?></td>
        <td style="width: 17.5%"></td>
        <td style="width: 17.5%"></td>
        <td style="width: 17.5%"></td>
      </tr>
      <tr>
        <td style="width: 30%">Otras Percepciones</td>
        <td style="width: 17.5%"><?php echo f($percep_viajes);?></td>
        <td style="width: 17.5%"></td>
        <td style="width: 17.5%"></td>
        <td style="width: 17.5%"></td>
      </tr>
    </table>
      
      
    <div style="border-top: solid wpx black; border-bottom: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
      TOTALES DE OPERACIONES QUE GENERAN CREDITO FISCAL
    </div>
      
    <table class="totales" style="border-top: solid 2px black; border-bottom: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
      <tr>
        <td style="width: 30%"></td>
        <td style="width: 17.5%"></td>
        <td style="width: 17.5%">Neto</td>
        <td style="width: 17.5%">IVA</td>
        <td style="width: 17.5%">Total</td>
      </tr>
      <?php
      $cf_neto = 0; $cf_iva = 0;
      foreach($alicuotas as $a) {
        $cf_neto += $credito_fiscal[$a->id]["neto"];
        $cf_iva += $credito_fiscal[$a->id]["iva"];
        ?>
        <tr>
          <td style="width: 30%">Tasa <?php echo $a->nombre ?></td>
          <td style="width: 17.5%"></td>
          <td style="width: 17.5%"><b><?php echo f($credito_fiscal[$a->id]["neto"]);?></b></td>
          <td style="width: 17.5%"><b><?php echo f($credito_fiscal[$a->id]["iva"]);?></b></td>
          <td style="width: 17.5%"><b><?php echo f($credito_fiscal[$a->id]["neto"] + $credito_fiscal[$a->id]["iva"]);?></b></td>
        </tr>
      <?php } ?>
      <tr style="border-top: solid 1px black">
        <td style="width: 30%"></td>
        <td style="width: 17.5%"></td>
        <td style="width: 17.5%"><b><?php echo f($cf_neto);?></b></td>
        <td style="width: 17.5%"><b><?php echo f($cf_iva);?></b></td>
        <td style="width: 17.5%"><b><?php echo f($cf_neto + $cf_iva);?></b></td>
      </tr>    
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr style="border-top: solid 2px black">
          <td style="width: 30%"><b>TOTALES</b></td>
          <td style="width: 17.5%"><b><?php echo f($percepcion_ib + $percep_viajes);?></b></td>
          <td style="width: 17.5%"><b><?php echo f($df_neto - $cf_neto);?></b></td>
          <td style="width: 17.5%"><b><?php echo f($df_iva - $cf_iva);?></b></td>
          <td style="width: 17.5%"><b><?php echo f($df_neto - $cf_neto + $df_iva - $cf_iva + $percepcion_ib + $percep_viajes);?></b></td>
      </tr>
    </table>
  </div>
</div>

  <?php $nro_pagina++; ?>
  <div class="a4p">
    <div class="inner">
      <?php echo encabezado($nro_pagina, $fecha_desde, $fecha_hasta, $empresa); ?>
      
      <div style="border-top: solid 2px black; border-bottom: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
        OPERACIONES POR SUJETO DE CLIENTE
      </div>
      
      <div style="overflow: hidden;">
        <?php 
        $sujetos = array("ri","cf","mon");
        foreach($sujetos as $sujeto) { ?>
          <div style="float: left; width: 33%">
            <div style="font-size: 13px; font-style: italic; font-weight: bold; margin-top: 20px; margin-bottom: 15px">
              <?php if ($sujeto == "ri") { ?>
                Operaciones con Responsables Inscriptos
              <?php } else if ($sujeto == "cf") { ?>
                Operaciones con Consumidores Finales, Exentos y No Alcanzados
              <?php } else if ($sujeto == "mon") { ?>
                Operaciones con Monotributistas
              <?php } ?>
            </div>
            <table class="totales">
              <?php 
              foreach($alicuotas as $a) { 
                if ($debito_fiscal[$a->id][$sujeto."_neto"] - $credito_fiscal[$a->id][$sujeto."_neto"] != 0) { ?>
                  <tr>
                    <td style="width: 50%">Al&iacute;cuota:</td>
                    <td style="width: 50%"><?php echo $a->nombre ?></td>
                  </tr>
                  <tr>
                      <td style="width: 50%">Monto Neto Gravado:</td>
                      <td style="width: 50%"><?php echo f($debito_fiscal[$a->id][$sujeto."_neto"] - $credito_fiscal[$a->id][$sujeto."_neto"]);?></td>
                  </tr>
                  <?php if ($empresa->id == 121 && $a->id == 3) { ?>
                    <?php if ($sujeto == "mon") { ?>
                      <tr>
                        <td style="width: 50%"></td>
                        <td style="width: 50%">(21%: <?php echo f($neto_0_21_mon) ?>. 10.50%: <?php echo f($neto_0_105_mon) ?>)</td>
                      </tr>
                    <?php } else if ($sujeto == "ri") { ?>
                      <tr>
                        <td style="width: 50%"></td>
                        <td style="width: 50%">(21%: <?php echo f($neto_0_21_ri) ?>. 10.50%: <?php echo f($neto_0_105_ri) ?>)</td>
                      </tr>
                    <?php } else if ($sujeto == "cf") { 
                      $base = $debito_fiscal[3]["cf_neto"] - $credito_fiscal[3]["cf_neto"] ?>
                      <tr>
                        <td style="width: 50%"></td>
                        <td style="width: 50%">(21%: <?php echo f($base - $neto_0_21_ri - $neto_0_21_mon) ?>. 10.50%: <?php echo f($base - $neto_0_105_ri - $neto_0_105_mon) ?>)</td>
                      </tr>                      
                    <?php } ?>
                  <?php } ?>
                  <tr>
                      <td style="width: 50%">Debito Fiscal Facturado:</td>
                      <td style="width: 50%"><?php echo f($debito_fiscal[$a->id][$sujeto."_iva"] - $credito_fiscal[$a->id][$sujeto."_iva"])?></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td></td>
                  </tr>
                <?php } ?>
              <?php } ?>
            </table>
          </div>
        <?php } ?>
             
      </div>
    </div>
  </div>

</body>
</html>