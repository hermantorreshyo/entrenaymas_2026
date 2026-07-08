<?php
function imprimir_tabla($results) {
  $s = "";
  foreach($results as $rep) {
    $s.= '<tr>';
    $s.= "<td>".$rep->nombre."</td>";
    $s.= "<td class='tar'>".(($rep->neto != 0)?number_format($rep->neto,2):"")."</td>";
    $s.= "<td class='tar'>".(($rep->iva != 0)?number_format($rep->iva,2):"")."</td>";
    $s.= "<td class='tar'>".(($rep->percepcion_ib != 0)?number_format($rep->percepcion_ib,2):"")."</td>";
    $s.= "<td class='tar'>".(($rep->percep_viajes != 0)?number_format($rep->percep_viajes,2):"")."</td>";
    $s.= "<td class='tar'>".(($rep->total != 0)?number_format($rep->total,2):"")."</td>";
    $t = $rep->total;
    $s.= "<td class='tar'>".(($t!=0)?number_format($t,2):"");
    $s.= "</td>";
    $s.= "</tr>";
  }
  return $s;
}
?>
<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
#printable { padding: 5px 20px; background-color: white; }
.tabla tr td, .table tr td { font-size: 12px; padding-top: 5px; padding-bottom: 5px; }
</style>
</head>
<body>
<?php echo $header; ?>
<div id="printable">
  <div class="header oh">
    <div class="subtitulo fl">
      <span class="bold">RESUMEN DE VENTAS</span>
    </div>
    <div class="fr">
      Desde: <span class="bold"><?php echo $desde; ?></span><br/>
      Hasta: <span class="bold"><?php echo $hasta; ?></span>
    </div>
  </div>  
  <table class="tabla">
    <thead>
      <tr>
        <th>Concepto</th>
        <th class="tar" style="width:100px;">Neto</th>
        <th class="tar" style="width:100px;">IVA</th>
        <th class="tar" style="width:100px;">Perc. IIBB</th>
        <th class="tar" style="width:100px;">Perc. IVA</th>
        <th class="tar" style="width:100px;">Total</th>
      </tr>
  </thead>
  <tbody>
    <?php
    echo imprimir_tabla($results);
    ?>    
  </tbody>
  </table>

</div>
</body>
</html>