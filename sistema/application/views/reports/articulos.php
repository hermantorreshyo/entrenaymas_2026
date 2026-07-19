<?php
// ver_listas es un parametro de la forma 1-0-1-0-1-0
// cada posicion significa la 
if (!isset($ver_listas) || empty($ver_listas)) {
  // Si esta vacio, vemos todas las listas
  $ver_listas = array(1,1,1,1,1,1);
} else {
  $ver_listas = explode("-", $ver_listas);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function imprimir_tabla($r,$i,$ver_listas) {
  $s = '';
  $s.= '<table class="tabla">';
  $s.= '  <thead>';
  $s.= '      <tr>';
  $s.= '          <th style="width:60px">Interno</th>';
  $s.= '          <th style="width:60px">Barra</th>';
  $s.= '          <th style="width:60px">Proveedor</th>';
  $s.= '          <th>Descripcion</th>';
  if (isset($ver_listas[0]) && $ver_listas[0] == 1) $s.= '<th style="width:60px; padding-right:15px;">Lista 1</th>';
  if (isset($ver_listas[1]) && $ver_listas[1] == 1) $s.= '<th style="width:60px; padding-right:15px;">Lista 2</th>';
  if (isset($ver_listas[2]) && $ver_listas[2] == 1) $s.= '<th style="width:60px; padding-right:15px;">Lista 3</th>';
  if (isset($ver_listas[3]) && $ver_listas[3] == 1) $s.= '<th style="width:60px; padding-right:15px;">Lista 4</th>';
  if (isset($ver_listas[4]) && $ver_listas[4] == 1) $s.= '<th style="width:60px; padding-right:15px;">Lista 5</th>';
  if (isset($ver_listas[5]) && $ver_listas[5] == 1) $s.= '<th style="width:60px; padding-right:15px;">Lista 6</th>';
  $s.= '      </tr>';
  $s.= '</thead>';
  $s.= '<tbody>';
  for ($i;$i<sizeof($r);$i++) {
    $row = $r[$i];
    $s.= '<tr>';
    $s.= "<td class='tar pr5'>$row->codigo</td>";
    $s.= "<td class='tar pr5'>";
    $codigo_barra = explode("###",$row->codigo_barra);
    foreach($codigo_barra as $cod) {
      $s.= ($cod != $row->codigo) ? $cod."<br/>" : "";
    }
    $s.= "</td>";
    $s.= "<td class='tar pr5'>".nl2br($row->codigo_proveedor)."</td>";
    $s.= "<td>".($row->nombre)."</td>";
    if (isset($ver_listas[0]) && $ver_listas[0] == 1) $s.= "<td>$ ".number_format($row->precio_final_dto,2)."</td>";
    if (isset($ver_listas[1]) && $ver_listas[1] == 1) $s.= "<td>$ ".number_format($row->precio_final_dto_2,2)."</td>";
    if (isset($ver_listas[2]) && $ver_listas[2] == 1) $s.= "<td>$ ".number_format($row->precio_final_dto_3,2)."</td>";
    if (isset($ver_listas[3]) && $ver_listas[3] == 1) $s.= "<td>$ ".number_format($row->precio_final_dto_4,2)."</td>";
    if (isset($ver_listas[4]) && $ver_listas[4] == 1) $s.= "<td>$ ".number_format($row->precio_final_dto_5,2)."</td>";
    if (isset($ver_listas[5]) && $ver_listas[5] == 1) $s.= "<td>$ ".number_format($row->precio_final_dto_6,2)."</td>";
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
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<script type="text/javascript" src="/sistema/resources/js/jquery.js"></script> 
<style type="text/css">
body { background-color: white; }
</style>
</head>
<body>
  <div class="page">
    <div class="p3mm">    
        <div class="header oh">
          <div class="subtitulo fl">
            <span class="bold">LISTADO DE PRECIOS</span>
          </div>
          <div class="fr">
            Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
          </div>
        </div>
          
        <?php foreach($resultados as $clave => $row) { ?>
          <div class="subtitulo">
            <?php echo mb_convert_encoding($clave, 'ISO-8859-1', 'UTF-8'); ?>
          </div>
          <?php foreach($row as $c => $r) { ?>
            <?php if (strtolower($c) != "sin definir") { ?>
              <div class="subtitulo2">
                <?php echo mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'); ?>
              </div>
            <?php } ?>
            <div class="oh">    
              <?php echo imprimir_tabla($r,0,$ver_listas); ?>
            </div>
          <?php } ?>
          <div class="bbs mt5 mb5 cb"></div>
        <?php } ?>        
    </div>
  </div>
</body>
</html>