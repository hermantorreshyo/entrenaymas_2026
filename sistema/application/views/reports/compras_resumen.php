<?php
function imprimir_tabla($results) {
  $s = "";
  foreach($results as $rep) {
    $s.= '<tr>';
    $s.= "<td>".$rep->nombre."</td>";
    $s.= "<td class='tar'>".(($rep->neto != 0)?number_format($rep->neto,2):"")."</td>";
    $s.= "<td class='tar'>".(($rep->iva != 0)?number_format($rep->iva,2):"")."</td>";
    $s.= "<td class='tar'>".(($rep->reg_especiales != 0)?number_format($rep->reg_especiales,2):"")."</td>";
    $s.= "<td class='tar'>".(($rep->total != 0)?number_format($rep->total,2):"")."</td>";
    $t = $rep->total + $rep->reg_especiales;
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
      <span class="bold">RESUMEN DE COMPRAS</span>
    </div>
    <div class="fr">
      <?php if (!empty($movimiento)) { ?>
        Fecha: <span class="bold"><?php echo get_mes(intval(substr($movimiento,0,2)))." 20".substr($movimiento, 2); ?></span>
      <?php } else { ?>
        Desde: <span class="bold"><?php echo $desde; ?></span><br/>
        Hasta: <span class="bold"><?php echo $hasta; ?></span>
      <?php } ?>
    </div>
  </div>  
  <table class="tabla">
    <thead>
      <tr>
        <th>Concepto</th>
        <th class="tar" style="width:100px;">Neto</th>
        <th class="tar" style="width:100px;">IVA</th>
        <th class="tar" style="width:100px;">Reg. Especiales</th>
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