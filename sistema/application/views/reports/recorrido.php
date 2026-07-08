<?php
function imprimir_tabla($r,$i,$hasta) {
  $s = '';
  $s.= '<table class="tabla">';
  $s.= '  <thead>';
  $s.= '    <tr>';
  $s.= '      <th style="width:30px">#</th>';
  $s.= '      <th>Cliente</th>';
  $s.= '      <th>Direccion</th>';
  $s.= '    </tr>';
  $s.= '</thead>';
  $s.= '<tbody>';
  for ($i;$i<$hasta;$i++) {
  $rep = $r[$i];
    $s.= '<tr>';
    $s.= "<td>".($i+1)."</td>";
    $s.= "<td>".utf8_decode($rep->nombre)."</td>";
    $s.= "<td>".utf8_decode($rep->direccion)."</td>";
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
<?php echo $header; ?>
<div id="printable">
  <div class="a4">
    <div class="inner">
      <div class="header oh">
        <div class="subtitulo fl">
          <span class="bold">RECORRIDO: <?php echo $recorrido->nombre ?></span>
        </div>
        <div class="fr">
          Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
        </div>
      </div>  
      <?php
      echo imprimir_tabla($recorrido->clientes,0,sizeof($recorrido->clientes));
      ?>
    </div>
  </div>
</div>
</body>
</html>