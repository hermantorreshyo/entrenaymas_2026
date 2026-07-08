<?php
// Obtenemos los maximos de filas por piso
$pisos = array(
  "1"=>array(
    "posicion_x"=>0,
    "posicion_y"=>0,
  ),
  "2"=>array(
    "posicion_x"=>0,
    "posicion_y"=>0,
  ),
);
foreach($asientos as $asiento) {
  if ($asiento->posicion_y >= $pisos[$asiento->piso]["posicion_y"]) {
    $pisos[$asiento->piso]["posicion_y"] = $asiento->posicion_y;
  }
  if ($asiento->posicion_x >= $pisos[$asiento->piso]["posicion_x"]) {
    $pisos[$asiento->piso]["posicion_x"] = $asiento->posicion_x;
  }
}

function get_asiento_by_pos($asientos,$x,$y,$piso) {
  foreach($asientos as $asiento) {
    if ($asiento->posicion_x == $x && $asiento->posicion_y == $y && $asiento->piso == $piso) return $asiento;
  }
  return FALSE;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<title>Pasajeros</title>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/bootstrap-cols.css" />
<style type="text/css">
body { background-color: white; font-family: Arial; }
.oficio { width: 220mm; border: solid 1px black; overflow: hidden; }
table { border-collapse: collapse; width: 100%; }
table td, table th { border: solid 1px black; font-size: 12px; padding: 2px 8px; font-weight: normal; }
h1 { text-align: center; width: 100%; font-size: 20px; }
.celda_titulo { background-color: #eee; color: black; font-weight: bold; font-size: 16px; text-align: center; }

.taquilla { width: auto; }
.taquilla td { border: none; }
.taquilla td.celda { border: solid 1px black; width: 100px; vertical-align: middle; height: 70px; text-align: center; }
.taquilla td.celda.sin_datos { border: none; }
.taquilla td.celda .numero { font-size: 12px; font-weight: bold; margin-bottom: 5px; display: block }
.taquilla td.celda .nombre { font-size: 12px; margin-bottom: 5px; display: block }
.observaciones { margin-top: 20px; border: solid 1px black; padding: 15px; }
.observaciones h3 { font-weight: bold; font-size: 16px; margin-bottom: 10px; }

</style>
</head>
<body>
<div class="oficio">
	<table>
		<tr>
			<td><h1>FLAMINGO TRAVEL S.A.</h1></td>
		</tr>
	</table>
	<table>
		<tr>
			<td><b>Viaje:</b> <?php echo $viaje->nombre; ?></td>
			<td><b>Vehiculo:</b> <?php echo $viaje->matricula; ?></td>
      <td><b>Por:</b> <?php echo $viaje->subtitulo ?></td>
		</tr>
		<tr>
			<td><b>Fecha:</b> <?php echo $viaje->fecha ?></td>
      <td></td>
			<td><b>Total de pasajeros:</b><?php echo sizeof($viaje->pasajeros) ?></td>
		</tr>
    <tr>
      <td colspan="3">
        <b>Choferes:</b>
        <?php foreach($viaje->vehiculos_tripulantes as $trip) { ?>
          <?php echo $trip->tripulante.". "; ?>
        <?php } ?>
      </td>
    </tr>
		<tr>
			<td colspan="3" class="celda_titulo">TAQUILLA DE PASAJEROS</td>	
		</tr>
	</table>
  <?php for($piso=1;$piso<=2;$piso++) { ?>
    <div style="width: 40%; <?php echo ($piso%2==1)?"float: left;":"float: right;" ?> padding: 20px;">
      <table class="taquilla">
        <?php 
        for($i=0;$i<=$pisos[$piso]["posicion_y"];$i++) { ?>
          <tr>
            <?php for($j=0;$j<=$pisos[$piso]["posicion_x"];$j++) { 
              $asiento = get_asiento_by_pos($asientos,$j,$i,$piso); 
              if ($asiento === FALSE) { ?>
                <td class="celda sin_datos"></td>
              <?php } else { ?>
                <td class="celda">
                  <span class="numero"><?php echo $asiento->numero_asiento; ?></span>
                  <span class="nombre"><?php echo (strlen($asiento->nombre)>24) ? substr($asiento->nombre,0,24)."..." : $asiento->nombre; ?></span>
                  <?php if (!empty($asiento->salida_desde)) { ?>
                    <span class="nombre fs10">(<?php echo $asiento->salida_desde; ?>)</span>
                  <?php } ?>
                </td>
              <?php } ?>
            <?php } ?>
          </tr>
        <?php } ?>
      </table>
      <?php if ($piso%2==0 && !empty($viaje->texto)) { ?>
        <div class="observaciones">
          <h3>Observaciones: </h3>
          <p>
            <?php echo nl2br($viaje->texto) ?>
          </p>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
</div>
</body>
</html>