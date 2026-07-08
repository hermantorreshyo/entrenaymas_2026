<?php
function imprimir_tabla($r,$i,$hasta) {
  $s = '';
  $s.= '<table class="tabla">';
  $s.= '  <thead>';
  $s.= '      <tr>';
  $s.= '          <th style="width:40px">Int.</th>';
  $s.= '          <th style="width:60px">Barra</th>';
  $s.= '          <th style="width:40px">Prov.</th>';
  $s.= '          <th>Descripcion</th>';
  $s.= '          <th style="width:60px; padding-right:15px;">Fecha</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">Neto s/dto</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">% Prov</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">Neto</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">IVA</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">Final</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">% L1</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">Lista 1</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">% L2</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">Lista 2</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">% L3</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">Lista 3</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">% L4</th>';
  $s.= '          <th style="width:40px; padding-right:15px;">Lista 4</th>';
  $s.= '      </tr>';
  $s.= '</thead>';
  $s.= '<tbody>';
  for ($i;$i<$hasta;$i++) {
    $row = $r[$i];
    $s.= '<tr>';
    $s.= "<td>$row->codigo</td>";
    $s.= "<td>";
    $codigo_barra = explode("###",$row->codigo_barra);
    foreach($codigo_barra as $cod) {
      $s.= ($cod != $row->codigo) ? $cod."<br/>" : "";
    }
    $s.= "</td>";
    $s.= "<td>".(isset($row->codigo_proveedor)?$row->codigo_proveedor:"0")."</td>";
    $s.= "<td>".($row->nombre)."</td>";
    $s.= "<td>".$row->fecha_mov."</td>";
    $s.= "<td>".number_format($row->costo_neto_inicial,2)."</td>";
    $s.= "<td>".number_format($row->dto_prov,2)."</td>";
    $s.= "<td>".number_format($row->costo_neto,2)."</td>";
    $s.= "<td>".number_format($row->porc_iva,2)." %</td>";
    $s.= "<td>".number_format($row->costo_final,2)."</td>";
    $s.= "<td>".number_format($row->porc_ganancia,2)." %</td>";
    $s.= "<td>".number_format($row->precio_final,2)."</td>";
    $s.= "<td>".number_format($row->porc_ganancia_2,2)." %</td>";
    $s.= "<td>".number_format($row->precio_final_2,2)."</td>";
    $s.= "<td>".number_format($row->porc_ganancia_3,2)." %</td>";
    $s.= "<td>".number_format($row->precio_final_3,2)."</td>";
    $s.= "<td>".number_format($row->porc_ganancia_4,2)." %</td>";
    $s.= "<td>".number_format($row->precio_final_4,2)."</td>";
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
          <span class="bold">LISTADO DE COSTOS</span>
        </div>
        <div class="fr">
          Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
        </div>
      </div>    
      <?php echo imprimir_tabla($results,0,sizeof($results)); ?>
    </div>
  </div>
</body>
</html>