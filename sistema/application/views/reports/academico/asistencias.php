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
                <?php if (isset($comision)) { ?>
                  <p>
                    <b>COMISIÓN: </b><?php echo $comision->nombre ?>
                  </p>
                <?php } ?>
                <?php if (isset($materia) && $id_materia != 0) { ?>
                  <p>
                    <b>MATERIA: </b><?php echo $materia->nombre ?>
                  </p>
                <?php } ?>
                <p>
                  <b>FECHA: </b><?php echo date("d/m/Y") ?>
                </p>
              </div>
            </td>
            <td>
              <h1>ASISTENCIA</h1>
            </td>
          </tr>
        </table>
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>  
        <div class="tabla">
          <table class="table-striped">
            <thead>
              <tr>
                <th class="tac" style="width: 40px;">#</th>
                <th><?php echo (isset($es_docente) ? "DOCENTE" : "ALUMNO") ?></th>
                <?php foreach($clases as $c) { ?>
                  <th class="tac"><?php echo substr(fecha_es($c["fecha"]),0,5); ?></th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php 
              $i = 1;
              foreach($results as $r) { ?>
                <tr>
                  <td class="tac"><?php echo str_pad($i,2,"0",STR_PAD_LEFT); ?></td>
                  <td class="negro">
                    <?php echo ucwords($r["nombre"]) ?>
                  </td>
                  <?php foreach($r["clases"] as $clase) { ?>
                    <td class="tac"><?php echo $clase->condicion ?></td>
                  <?php } ?>
                </tr>
              <?php $i++; } ?>
            </tbody>
          </table>
        </div>
      </td>
    </tr>
  </tbody>
</table>
</body>
</html>