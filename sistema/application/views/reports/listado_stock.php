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
    $s.= "<td>".mb_convert_encoding($faltante->nombre, 'ISO-8859-1', 'UTF-8')."</td>";
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
            (<?php echo mb_convert_encoding($proveedor->codigo, 'ISO-8859-1', 'UTF-8'); ?>)
          <?php } ?>
        </span>
      </div>
      <?php if ($proveedor != null && !empty($proveedor->contacto)) { ?>
        <div class="fl" style="clear:left">
          <?php echo mb_convert_encoding($proveedor->contacto, 'ISO-8859-1', 'UTF-8'); ?>
          <?php echo mb_convert_encoding($proveedor->contacto_telefono, 'ISO-8859-1', 'UTF-8'); ?>
        </div>
      <?php } ?>
      <div class="fr">
        Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
      </div>
    </div>
    
    <?php foreach($resultados as $clave => $row) { ?>
      
      <div class="subtitulo">
        <?php echo mb_convert_encoding($clave, 'ISO-8859-1', 'UTF-8'); ?>
      </div>
      
      <?php foreach($row as $c => $r) { ?>
        <div class="subtitulo2">
          <?php echo mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'); ?>
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