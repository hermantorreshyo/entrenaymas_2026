<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
          <span class="bold">COMPARACION DE PRECIOS MAXIMOS</span>
        </div>
        <div class="fr">
          Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span>
        </div>
      </div>
      <div class="oh cb">
        <table class="tabla">
          <thead>
            <tr>
              <th style="width: 20px">#</th>
              <th>Codigo</th>
              <th>Barra</th>
              <th>Nombre</th>
              <th>Precio</th>
              <th>Precio Maximo</th>
              <th>Diferencia</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach($resultados as $row) { ?>
              <tr>
                <td><?php echo $i ?></td>
                <td class='tar pr5'><?php echo $row->codigo ?></td>
                <td class='tar pr5'>
                  <?php $codigo_barra = explode("###",$row->codigo_barra);
                  foreach($codigo_barra as $cod) {
                    echo ($cod != $row->codigo) ? $cod."<br/>" : "";
                  } ?>
                </td>
                <td><?php echo $row->nombre ?></td>
                <td>$ <?php echo number_format($row->precio_final_dto,2) ?></td>
                <td>$ <?php echo number_format($row->precio_maximo,2) ?></td>
                <td>$ <?php echo number_format($row->precio_final_dto - $row->precio_maximo,2) ?></td>
              </tr>
            <?php $i++; } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>