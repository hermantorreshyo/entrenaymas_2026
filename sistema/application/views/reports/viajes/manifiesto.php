<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<title>Manifiesto</title>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
body { background-color: white; font-family: Arial; }
.oficio { width: 220mm; border: solid 1px black; overflow: hidden; }
table { border-collapse: collapse; width: 100%; }
table td, table th { border: solid 1px black; font-size: 10px; padding: 2px 5px; font-weight: normal; }
h1 { text-align: center; width: 100%; font-size: 18px; }
.celda_titulo { background-color: #eee; color: black; font-weight: bold; font-size: 11px; text-align: center; }
</style>
</head>
<body>
<div class="oficio">
	<table>
		<tr>
			<td><h1>MINISTERIO DEL INTERIOR - DIRECCION NACIONAL DE MIGRACIONES</h1></td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="celda_titulo" colspan="6">MANIFIESTO DE TRIPULANTES Y PASAJEROS - EXCLUSIVO CORREDOR TURISTICO</td>
			<td class="celda_titulo">Nº DE MANIFIESTO</td>
		</tr>
		<tr>
			<td colspan="3">DEL: FLAMINGO TRAVEL S.A.</td>
			<td colspan="3">MATRICULA: <?php echo $viaje->matricula; ?></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="3">MEDIO DE TRANSPORTE: </td>
			<td colspan="3">NACION: Argentina</td>
			<td rowspan="6" class="tac" style="vertical-align: bottom;">
				CALIFICACIÓN MIGRATORIA
			</td>
		</tr>
		<tr>
			<td colspan="2" rowspan="2">FECHA: <?php echo $viaje->fecha ?></td>
			<td colspan="2" rowspan="2">POR: <?php echo $viaje->subtitulo ?></td>
			<td>CON:</td>
			<td><?php echo sizeof($viaje->pasajeros) ?> PASAJEROS</td>			
		</tr>
		<tr>
			<td></td>
			<td><?php echo sizeof($viaje->tripulantes) ?> TRIPULANTES</td>			
		</tr>
		<tr>
			<td colspan="6">CONSIGNADO A: Flamingo Travel E. V. T.</td>
		</tr>
		<tr>
			<td class="celda_titulo" colspan="6">TRIPULANTES</td>	
		</tr>
		<tr>
			<td colspan="3"><?php 
      for($i=0;$i<sizeof($viaje->tripulantes);$i++) {
        echo (isset($viaje->tripulantes[$i])) ? $viaje->tripulantes[$i]."<br/>" : "";  
        $i++;
      }
			?></td>
      <td colspan="3"><?php 
      for($i=1;$i<sizeof($viaje->tripulantes);$i++) {
        echo (isset($viaje->tripulantes[$i])) ? $viaje->tripulantes[$i]."<br/>" : "";  
        $i++;
      }
      ?></td>
		</tr>
		<tr>
			<td class="celda_titulo" colspan="6">PASAJEROS</td>	
		</tr>
	</table>
	<table>
		<thead>
			<tr>
				<th>Nº</th>
				<th>APELLIDO Y NOMBRE</th>
				<th>FECHA DE NACIMIENTO</th>
				<th>NACIONALIDAD</th>
				<th>TIPO Y Nº DE DOCUMENTO</th>
				<th>* SIN VISA</th>
				<th>* CON VISA</th>
			</tr>
		</thead>
		<tbody>
			<?php $i=1; foreach($viaje->pasajeros as $pax) { ?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo strtoupper(((!empty($pax->apellido)) ? ($pax->apellido.", ".$pax->nombre) : $pax->nombre)); ?></td>
					<td><?php echo $pax->fecha_nac; ?></td>
					<td><?php echo $pax->nacionalidad; ?></td>
					<td><?php echo $pax->dni; ?></td>
					<td></td>
					<td></td>
				</tr>
			<?php $i++; } ?>
			<?php for($i;$i<=62;$i++) { ?>
				<tr>
					<td><?php echo $i; ?></td>
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
	<table>
		<tr style="height: 16px;">
			<td class="tac">RESPONSABLE DEL TRANSPORTE</td>
			<td class="tac">FIRMA SUPERVISOR</td>
			<td class="tac">OPERADOR</td>
			<td class="tac">FIRMA Y SELLO INSPECTOR</td>
			<td class="tac">SELLO DE CONTROL</td>
			<td class="tac" rowspan="2">E<br/>N<br/>T<br/>R<br/>A<br/>D<br/>A</td>
		</tr>
		<tr style="height: 50px">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr style="height: 16px;">
			<td class="tac">RESPONSABLE DEL TRANSPORTE</td>
			<td class="tac">FIRMA SUPERVISOR</td>
			<td class="tac">OPERADOR</td>
			<td class="tac">FIRMA Y SELLO INSPECTOR</td>
			<td class="tac">SELLO DE CONTROL</td>
			<td class="tac" rowspan="2">S<br/>A<br/>L<br/>I<br/>D<br/>A</td>
		</tr>
		<tr style="height: 50px">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
</div>
</body>
</html>