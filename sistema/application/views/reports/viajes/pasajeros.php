<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<title>Pasajeros</title>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
body { background-color: white; font-family: Arial; }
.oficio { width: 220mm; border: solid 1px black; overflow: hidden; }
table { border-collapse: collapse; width: 100%; }
table td, table th { border: solid 1px #222; font-size: 12px; padding: 2px 8px; font-weight: normal; }
.tabla_ppal td { border: solid 1px #999; }
h1 { text-align: center; width: 100%; font-size: 20px; }
.celda_titulo { background-color: #eee; color: black; font-weight: bold; font-size: 16px; text-align: center; }
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
			<td><?php echo $viaje->nombre; ?></td>
			<td>Vehiculo: <?php echo $viaje->matricula; ?></td>
      <td>Por: <?php echo $viaje->subtitulo ?></td>
		</tr>
		<tr>
			<td>Fecha: <?php echo $viaje->fecha ?></td>
      <td></td>
			<td>Total de pasajeros: <b><?php echo sizeof($viaje->pasajeros) ?></b></td>
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
			<td colspan="3" class="celda_titulo">PASAJEROS</td>	
		</tr>
	</table>
	<table class="tabla_ppal">
		<thead>
			<tr>
				<th>Nº ASIENTO</th>
				<th>APELLIDO Y NOMBRE</th>
        <th>DOCUMENTO</th>
				<th>VENDIDO POR</th>
        <th>HOTEL</th>
        <th>TIPO HAB.</th>
        <th>NRO. HAB.</th>
        <th>RESTO</th>
			</tr>
		</thead>
		<tbody>
			<?php $i=1; foreach($viaje->pasajeros as $pax) { ?>
				<tr>
					<td><?php echo $pax->numero_asiento; ?></td>
					<td><?php echo strtoupper(((!empty($pax->apellido)) ? ($pax->apellido.", ".$pax->nombre) : $pax->nombre)); ?></td>
          <td><?php echo $pax->dni; ?></td>
					<td><?php echo $pax->vendedor; ?></td>
          <td><?php echo $pax->hotel; ?></td>
          <td>
            <?php echo ($pax->tipo_habitacion==0)?"-":"" ?>
            <?php echo ($pax->tipo_habitacion==1)?"SINGLE":"" ?>
            <?php echo ($pax->tipo_habitacion==2)?"MAT":"" ?>
            <?php echo ($pax->tipo_habitacion==3)?"DOBLE":"" ?>
            <?php echo ($pax->tipo_habitacion==4)?"MAT+1":"" ?>
            <?php echo ($pax->tipo_habitacion==5)?"TRIPLE":"" ?>
            <?php echo ($pax->tipo_habitacion==6)?"X4":"" ?>
            <?php echo ($pax->tipo_habitacion==9)?"MAT+2":"" ?>
            <?php echo ($pax->tipo_habitacion==10)?"X5":"" ?>
            <?php echo ($pax->tipo_habitacion==11)?"MAT+3":"" ?>
            <?php echo ($pax->tipo_habitacion==12)?"X6":"" ?>
            <?php echo ($pax->tipo_habitacion==7)?"SOLO A COMPARTIR":"" ?>
            <?php echo ($pax->tipo_habitacion==8)?"SOLA A COMPARTIR":"" ?>
          </td>
          <td><?php echo $pax->numero_habitacion; ?></td>
          <td><?php echo ($pax->resto != 0) ? "$ ".$pax->resto : ""; ?></td>
				</tr>
			<?php $i++; } ?>
			<?php for($i;$i<=62;$i++) { ?>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
</body>
</html>