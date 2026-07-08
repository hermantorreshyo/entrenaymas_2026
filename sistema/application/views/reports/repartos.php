<?php
function imprimir_tabla($r,$i,$hasta) {
  $s = '';
  $s.= '<table class="tabla">';
  $s.= '  <thead>';
  $s.= '    <tr>';
  $s.= '      <th style="width:60px">Codigo</th>';
  $s.= '      <th>Articulo</th>';
  $s.= '      <th class="tar" style="width:100px;">Facturado</th>';
  $s.= '      <th class="tar" style="width:100px;">Devolucion</th>';
  $s.= '      <th class="tar" style="width:100px;">Bonificado</th>';
  $s.= '      <th class="tar" style="width:100px;">Total</th>';
  $s.= '    </tr>';
  $s.= '</thead>';
  $s.= '<tbody>';
  for ($i;$i<$hasta;$i++) {
    $rep = $r[$i];
    $s.= '<tr>';
    $s.= "<td>$rep->codigo</td>";
    $s.= "<td>".($rep->descripcion)."</td>";
    $s.= "<td class='tar'>";
    $s.= (($rep->facturado != 0)?number_format($rep->facturado,2):"");
    $s.= "</td>";
    $s.= "<td class='tar'>".(($rep->devolucion != 0)?number_format($rep->devolucion,2):"")."</td>";
    $s.= "<td class='tar'>".(($rep->bonificacion != 0)?number_format($rep->bonificacion,2):"")."</td>";
    $t = $rep->facturado + $rep->devolucion + $rep->bonificacion;
    $s.= "<td class='tar'>".(($t!=0)?number_format($t,2):"");
    if ($rep->uxb > 1) $s.= " (".number_format(floor($t / $rep->uxb),0)." Bul.".((($t % $rep->uxb)!=0) ? ("+".$t % $rep->uxb." unid.") : "").")";
    $s.= "</td>";
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
      <span class="bold">LISTADO DE REPARTO</span>
    </div>
    <div class="fr">
      Numero: <span class="bold"><?php echo $numero; ?></span> &nbsp;&nbsp;&nbsp;&nbsp;
      Fecha: <span class="bold"><?php echo $fecha; ?></span>
    </div>
  </div>  
  <?php
  echo imprimir_tabla($results,0,sizeof($results));
  ?>
</div>
</body>
</html>