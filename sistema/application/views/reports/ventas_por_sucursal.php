<?php
function imprimir_tabla($r,$i,$hasta) {
  $s = '';
  $s.= '<table class="tabla">';
  $s.= '  <thead>';
  $s.= '      <tr>';
  $s.= '          <th style=">Int.</th>';
  $s.= '          <th style="">Barra</th>';
  $s.= '          <th style=">Prov.</th>';
  $s.= '          <th>Descripcion</th>';
  $s.= '          <th style="padding-right:15px;">Sucursal</th>';
  $s.= '          <th style="padding-right:15px;">Venta</th>';
  $s.= '          <th style="padding-right:15px;">%</th>';
  $s.= '          <th style="padding-right:15px;">Tickets</th>';
  $s.= '          <th style="padding-right:15px;">Ticket Prom.</th>';
  $s.= '          <th style="padding-right:15px;">CMV</th>';
  $s.= '          <th style="padding-right:15px;">Ganancia</th>';
  $s.= '          <th style="padding-right:15px;">% Marc.</th>';
  $s.= '          <th style="padding-right:15px;">Oferta</th>';
  $s.= '      </tr>';
  $s.= '</thead>';
  $s.= '<tbody>';
  for ($i;$i<$hasta;$i++) {
    $row = $r[$i];
    $s.= '<tr>';
    $s.= "<td>$row->sucursal</td>";
    $s.= "<td>".number_format($row->venta,2)."</td>";
    $s.= "<td>".number_format($row->porcentaje,2)." %</td>";
    $s.= "<td>".number_format($row->cantidad,2)."</td>";
    $s.= "<td>".number_format($row->ticket_promedio,2)."</td>";
    $s.= "<td>".number_format($row->costo,2)."</td>";
    $s.= "<td>".number_format($row->ganancia,2)."</td>";
    $s.= "<td>".number_format($row->marcacion,2)." %</td>";
    $s.= "<td>".number_format($row->oferta,2)."</td>";
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
</style>
</head>
<body>
  <div class="page horizontal">
    <div class="p3mm">    
      <div class="header oh">
        <div class="subtitulo fl">
          <span class="bold">VENTAS POR SUCURSAL</span>
        </div>
        <div class="fr">
          Desde: <span class="bold"><?php echo $desde; ?></span><br/>
          Hasta: <span class="bold"><?php echo $desde; ?></span>
        </div>
      </div>    
      <?php echo imprimir_tabla($data,0,sizeof($data)); ?>
    </div>
  </div>
</body>
</html>