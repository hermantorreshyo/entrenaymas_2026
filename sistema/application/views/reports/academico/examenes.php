<!DOCTYPE html>
<html>
<head>
<title></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="/templates/excursiones/css/fonts.css" />
<?php $c1 = $empresa->config["color_principal"]; ?>
<?php $c2 = $empresa->config["color_secundario"]; ?>
<style type="text/css">
<?php include("style.php"); ?>
</style>
</head>
<body>
<?php echo $header; ?>
<table class="a4">
  <thead>
    <tr>
      <th>
        <table class="encabezado">
          <?php include("encabezado.php"); ?>
          <tr>
            <td>
              <div class="informacion">
                <p>
                  <b>COMISIÓN: </b><?php echo $comision->nombre ?>
                </p>
                <p>
                  <b>MATERIA: </b><?php echo $materia->nombre ?>
                </p>
                <p>
                  <b>FECHA: </b><?php echo date("d/m/Y") ?>
                </p>
              </div>
            </td>
            <td>
              <h1>EXAMENES</h1>
            </td>
          </tr>
        </table>
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>        
        <div class="mt20">
          <div class="tabla">
            <table class="table-striped">
              <thead>
                <tr>
                  <th class="tac" style="width: 40px;">#</th>
                  <th>ALUMNO</th>
                  <?php foreach($examenes as $c) { ?>
                    <th class="tac"><?php echo strtoupper($c->nombre); ?></th>
                  <?php } ?>
                  <th class="tac">PROMEDIO</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; foreach($results as $r) { ?>
                  <tr>
                    <td class="tac"><?php echo str_pad($i,2,"0",STR_PAD_LEFT); ?></td>
                    <td class="negro">
                      <?php echo ucwords($r["nombre"]); ?>
                    </td>
                    <?php 
                    $promedio = 0;
                    foreach($r["examenes"] as $examen) { 
                      $promedio += (($examen->valor != "" && $examen->valor != "-") ? $examen->valor : 0); ?>
                      <td class="tac"><?php echo $examen->valor ?></td>
                    <?php } ?>
                    <td class="tac"><?php echo ((sizeof($r["examenes"])==0) ? 0: round($promedio/sizeof($r["examenes"]),2)) ?></td>
                  </tr>
                <?php $i++; } ?>
              </tbody>
            </table>
          </div>
        </div>
      </td>
    </tr>
  </tbody>
</table>
</body>
</html>