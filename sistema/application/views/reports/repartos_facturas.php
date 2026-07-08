<?php
function imprimir_tabla($r,$i,$hasta) {
  $s = '';
  $s.= '<table class="tabla">';
  $s.= '  <thead>';
  $s.= '    <tr>';
  $s.= '      <th style="width:100px">Fecha</th>';
  $s.= '      <th>Cliente</th>';
  $s.= '      <th style="width:150px;">Comprobante</th>';
  $s.= '      <th style="width:150px;">Total</th>';
  $s.= '    </tr>';
  $s.= '</thead>';
  $s.= '<tbody>';
  for ($i;$i<$hasta;$i++) {
    $rep = $r[$i];
    $s.= '<tr>';
    $s.= "<td>$rep->fecha</td>";
    $s.= "<td>".($rep->cliente)."</td>";
    $s.= "<td>".($rep->comprobante)."</td>";
    $s.= "<td>$ ".(($rep->negativo==1)?"-":"").($rep->total)."</td>";
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
      <span class="bold">LISTADO DE COMPROBANTES</span>
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