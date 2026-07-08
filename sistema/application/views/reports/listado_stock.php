<?php
function imprimir_tabla($r,$i,$hasta) {
  $s = '';
  $s.= '<table class="tabla">';
  $s.= '  <thead>';
  $s.= '    <tr>';
  $s.= '      <th style="width:40px">Int.</th>';
  $s.= '      <th style="width:40px">Prov.</th>';
  $s.= '      <th style="width:90px">Cod. Barra</th>';
  $s.= '      <th>Descripcion</th>';
  $s.= '      <th style="width:40px; padding-right:15px;">UxB</th>';
  $s.= '      <th style="width:4px"></th>';
  $s.= '      <th style="width:35px;text-align:center">STK.</th>';
  $s.= '      <th style="width:35px;text-align:center">PED.</th>';
  $s.= '      <th style="width:4px"></th>';
  $s.= '      <th style="width:35px;text-align:center">STK.</th>';
  $s.= '      <th style="width:35px;text-align:center">PED.</th>';
  $s.= '      <th style="width:4px"></th>';
  $s.= '      <th style="width:35px;text-align:center">STK.</th>';
  $s.= '      <th style="width:35px;text-align:center">PED.</th>';
  $s.= '      <th style="width:4px"></th>';
  $s.= '      <th style="width:35px;text-align:center">STK.</th>';
  $s.= '      <th style="width:35px;text-align:center">PED.</th>';
  $s.= '    </tr>';
  $s.= '</thead>';
  $s.= '<tbody>';
  for ($i;$i<$hasta;$i++) {
    $faltante = $r[$i];
    $s.= '<tr>';
    $s.= "<td>$faltante->codigo</td>";
    $s.= "<td>".(isset($faltante->codigo_proveedor)?$faltante->codigo_proveedor:"0")."</td>";
    $s.= "<td>";
    $codigo_barra = explode("###",$faltante->codigo_barra);
    foreach($codigo_barra as $cod) {
      $s.= ($cod != $faltante->codigo) ? $cod."<br/>" : "";
    }
    $s.= "</td>";
    $s.= "<td>".utf8_decode($faltante->nombre)."</td>";
    $s.= "<td>x $faltante->uxb</td>";
    $s.= "<td>|</td>";
    $s.= "<td></td>";
    $s.= "<td></td>";
    $s.= "<td>|</td>";
    $s.= "<td></td>";
    $s.= "<td></td>";
    $s.= "<td>|</td>";
    $s.= "<td></td>";
    $s.= "<td></td>";
    $s.= "<td>|</td>";
    $s.= "<td></td>";
    $s.= "<td></td>";
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
.tabla tr td {
  padding-bottom: 4px;
  padding-top: 4px;
}
</style>
</head>
<body>
<div class="page">
  <div class="p3mm">  
    <div class="header oh">
      <div class="subtitulo fl">
        <span class="bold">
          LISTADO DE ARTICULOS
          <?php if ($proveedor != null && !empty($proveedor->nombre)) { ?>
            DE <?php echo $proveedor->nombre; ?>
            (<?php echo utf8_decode($proveedor->codigo); ?>)
          <?php } ?>
        </span>
      </div>
      <?php if ($proveedor != null && !empty($proveedor->contacto)) { ?>
        <div class="fl" style="clear:left">
          <?php echo utf8_decode($proveedor->contacto); ?>
          <?php echo utf8_decode($proveedor->contacto_telefono); ?>
        </div>
      <?php } ?>
      <div class="fr">
        Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
      </div>
    </div>
    
    <?php foreach($resultados as $clave => $row) { ?>
      
      <div class="subtitulo">
        <?php echo utf8_decode($clave); ?>
      </div>
      
      <?php foreach($row as $c => $r) { ?>
        <div class="subtitulo2">
          <?php echo utf8_decode($c); ?>
        </div>
        <div class="oh">  
        <?php
        $total_filas = sizeof($r);
        echo imprimir_tabla($r,0,$total_filas);
        ?>
        </div>
      <?php } ?>
      
      <div class="bbs mt5 mb5 cb"></div>
      
    <?php } ?>
    
    
  </div>
</div>

</body>
</html>