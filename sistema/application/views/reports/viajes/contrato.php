<!DOCTYPE html>
<html dir="ltr" lang="en" class="no-js">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="/templates/excursiones/css/fonts.css" />
<?php $c1 = $empresa->config["color_principal"]; ?>
<?php $c2 = $empresa->config["color_secundario"]; ?>
<title>Contrato</title>
<style type="text/css">
body { background-color: #eee; font-family: "Lato-Regular",Arial; font-size: 16px; color: <?php echo $c1; ?>; }
h1 { font-weight: bold; padding: 0px; text-transform: uppercase; color: <?php echo $c1; ?>; font-family: "LatoLight"; font-size: 36px; width: 100%; }

table { border-collapse: collapse; width: 100%; }
table td, table th { border: solid 1px #222; font-size: 12px; padding: 2px 8px; font-weight: normal; }
.tabla_ppal td { border: solid 1px #999; }
h1 { text-align: center; width: 100%; font-size: 20px; }
.celda_titulo { background-color: #eee; color: black; font-weight: bold; font-size: 16px; text-align: center; }

.subtitulo { padding-bottom: 15px; margin-bottom: 15px; font-size: 16px; text-transform: uppercase; border-bottom: solid 3px <?php echo $c1 ?>; font-weight: bold }
.info .subtitulo { border-bottom-color: #e6e6e6; }
.tabla table { padding-bottom: 15px; border-collapse: collapse; width: 100%; }
.tabla table tr td { font-size: 16px; padding: 8px 0px; border-bottom: solid 1px #e6e6e6; }
.tabla table tr td:first-child { font-weight: bold; }
.tabla table tr td:last-child { text-align: right; }
.tabla table tr:last-child td { border-bottom-color: transparent; padding-bottom: 30px; }
i { color: <?php echo $c2 ?>; }
.a4 {
  padding: 15px 30px;
  width: 210mm;
  height: 291mm;
  overflow: hidden;
  margin: 0 auto;
  background-color: white;
  margin-bottom: 30px;
}
@media print {
  body {-webkit-print-color-adjust: exact; background-color: white; }
  .a4 { page-break-after: always; margin-bottom: 0px; }
}
.firmas { border: solid 1px #ccc; padding: 10px; text-align: center; overflow: hidden; clear: both;  }
.fila { float: left; width: 100%; clear: both; }
</style>
</head>
<body>
<div id="printable">
  <div class="a4">
    <?php if(!empty($empresa->logo)) { ?>
      <div class="fila">
        <img style="max-width: 300px; margin-bottom: 15px;" src="/sistema/<?php echo $empresa->logo ?>"/>
      </div>
    <?php } ?>
    <div class="fila">
      <p>
        En Chacabuco, a los <?php echo $dia ?> días del mes de <?php echo $mes ?> de <?php echo $anio ?>
        se reúnen, por una parte <b>Ernesto Ezequiel Aluise</b>, socio gerente de la empresa Chacabuco Noroeste Tour S.R.L.
        con D.N.I.: 25.817.748 en adelante el TRANSPORTISTA, y la otra parte 
        <b><?php echo $cliente->nombre ?></b>
        <?php if (!empty($cliente->direccion)) { ?>
          domiciliado en 
          <b>
            <?php echo $cliente->direccion ?>
            <?php if (!empty($cliente->localidad)) { ?>, <?php echo $cliente->localidad ?><?php } ?>
          </b>
        <?php } ?>
        en adelante el CONTRATANTE. 
        <?php if (!empty($cliente->cuit)) { ?>(CUIT: <?php echo $cliente->cuit ?>)<?php } ?>
      </p>
      <p>
        El transportista se compromete a trasladar a los pasajeros desde la ciudad de <b><?php echo $viaje->custom_1 ?></b>
        hasta la ciudad de <b><?php echo $viaje->custom_2 ?></b>
        por el monto de <b>$ <?php echo number_format($viaje->precio,2) ?></b>,
        y se obliga a contratar un seguro de responsabilidad civil que cubre todo
        daño que pueda sufrir con sus pasajeros.
      </p>
      <?php if ($viaje->capacidad_vehiculo > 0) { ?>
        <p>
          <b>Capacidad del vehículo: </b><?php echo $viaje->capacidad_vehiculo ?>
        </p>
      <?php } ?>
      <p>
        <b>Domicilio de salida: </b><?php echo $viaje->custom_3 ?>
      </p>
      <?php if (!empty($viaje->texto)) { ?>
        <p>
          <b>Programación del viaje: </b><?php echo $viaje->texto ?>
        </p>
      <?php } ?>

      <p style="overflow: hidden;" class="mb20">
        <div class="w40p fl">
          <b>Entrega: </b>
        </div>
        <div class="w40p fl">
          <b>Restan: </b>
        </div>
      </p>
    </div>

    <div class="fila" style="overflow: hidden; clear: both; width: 100%; margin-top: 30px; margin-bottom: 40px;">
      <div class="firmas">
        <div class="row">
          <div class="col-xs-6">Transportista</div>
          <div class="col-xs-6">Contratante</div>
        </div>
      </div>
      <div class="firmas">
        <div>&nbsp;<br/><br/></div>
      </div>
    </div>

    <div class="fila">
      <p>
        <b><?php echo (sizeof($viaje->vehiculos_tripulantes) == 1) ? "Chofer":"Choferes" ?>: </b>
        <?php for($i=0;$i<sizeof($viaje->vehiculos_tripulantes);$i++) { 
          $tripu = $viaje->vehiculos_tripulantes[$i]; ?>
          <?php echo $tripu->tripulante ?> <?php echo ($i<sizeof($viaje->vehiculos_tripulantes)-1)?" - ":"" ?>
        <?php } ?>
      </p>
      <p>
        <b>M&oacute;vil: </b><?php echo $viaje->matricula ?>
      </p>
      <p>
        <b>Destino: </b><?php echo $viaje->custom_2 ?>.<br/><?php echo $viaje->custom_4 ?>
      </p>
      <p>
        <b>Caja: </b>
      </p>
      <p>
        <div class="w40p fl">
          <b>Fecha de salida: </b> <?php echo $viaje->fecha ?>
        </div>
        <div class="w40p fl">
          <b>Hora: </b> <?php echo $viaje->custom_6 ?>
        </div>
      </p>
      <p>
        <div class="w40p fl">
          <b>Fecha de llegada: </b> <?php echo $viaje->fecha_llegada ?>
        </div>
        <div class="w40p fl">
          <b>Hora: </b> <?php echo $viaje->custom_7 ?>
        </div>
      </p>
    </div>
  </div>
</div>
</body>
</html>