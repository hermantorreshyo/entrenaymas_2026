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
<title>Voucher</title>
<style type="text/css">
body { background-color: #eee; font-family: "Lato-Regular",Arial; font-size: 16px; color: <?php echo $c1; ?>; }
h1 { font-weight: normal; padding-top: 15px; padding-bottom: 15px; padding-left: 30px; text-transform: uppercase; color: white; background-color: <?php echo $c1; ?>; font-family: "LatoLight"; font-size: 42px; width: 100%; }
.subtitulo { padding-bottom: 15px; margin-bottom: 15px; font-size: 16px; text-transform: uppercase; border-bottom: solid 3px <?php echo $c1 ?>; font-weight: bold }
.info .subtitulo { border-bottom-color: #e6e6e6; }
.tabla table { padding-bottom: 15px; border-bottom: solid 3px <?php echo $c2; ?>; border-collapse: collapse; width: 100%; }
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
</style>
</head>
<body>
<?php echo $header; ?>
<div id="printable">
  <div class="a4">
    <div class="">
      <div class="row" style="overflow: hidden; margin-top: 10px; ">
        <div class="col-xs-6">
          <h1>VOUCHER</h1>
        </div>
        <div class="col-xs-6">
          <?php if(!empty($empresa->logo)) { ?>
            <img style="width: 100%" src="/sistema/<?php echo $empresa->logo ?>"/>
          <?php } ?>
        </div>
      </div>
      <div class="row" style="margin-top: 40px; overflow: hidden; margin-bottom: 30px; ">
        <div class="col-xs-6 info">
          <h2 class="subtitulo">PASAJERO</h2>
          <?php $pax1 = $reserva->asientos[0]; ?>
          <p class="fs20 bold"><?php echo $pax1->nombre ?></p>
          <?php if (!empty($pax1->dni)) { ?>
            <p><b>DNI:</b> <?php echo $pax1->dni ?></p>
          <?php } ?>
          <?php if (!empty($pax1->nacionalidad)) { ?>
            <p><b>Nacionalidad:</b> <?php echo $pax1->nacionalidad ?></p>
          <?php } ?>
          <p><b>Telefono:</b> <?php echo $reserva->cliente_telefono ?></p>
        </div>
        <div class="col-xs-6 info">
          <h2 class="subtitulo">INFORMACI&Oacute;N</h2>
          <?php if (!empty($empresa->direccion)) { ?>
            <p><?php echo $empresa->direccion ?></p>
          <?php } ?>
          <?php if (!empty($empresa->email)) { ?>
            <p><span style="width: 25px; display: inline-block;"><i class="fa fa-envelope"></i></span> <?php echo $empresa->email ?></p>
          <?php } ?>
          <?php if (!empty($empresa->telefono)) { ?>
            <p><span style="width: 25px; display: inline-block;"><i class="fa fa-phone"></i></span> <?php echo $empresa->telefono ?></p>
          <?php } ?>
          <?php if (isset($empresa->dominios) && sizeof($empresa->dominios)>0) { ?>
            <p><span style="width: 25px; display: inline-block;"><i class="fa fa-globe"></i></span> <?php echo $empresa->dominios[0] ?></p>
          <?php } ?>
        </div>
      </div>
      <div class="tabla">
        <h2 class="subtitulo">
          INFORMACI&Oacute;N
        </h2>
        <table>
          <tr>
            <td>Fecha del servicio:</td>
            <td><?php echo $reserva->fecha_reserva ?></td>
          </tr>
          <?php if ($reserva->id_viaje == 122) { 
            if (!empty($reserva->hotel_observaciones)) {
              $lineas = explode("\n", $reserva->hotel_observaciones);
              foreach($lineas as $linea) {
                $li2 = explode(": ", $linea); 
                if (!empty($li2[0])){ ?>
                  <tr>
                    <td><?php echo $li2[0];?>:</td>
                    <td><?php echo $li2[1]; ?></td>
                  </tr>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          <?php } else { ?>
            <tr>
              <td>Cantidad de pasajeros:</td>
              <td><?php echo sizeof($reserva->asientos) ?></td>
            </tr>
            <?php if (!empty($reserva->hotel)) { ?>
              <tr>
                <td>Lugar de alojamiento:</td>
                <td><?php echo $reserva->hotel ?></td>
              </tr>
            <?php } ?>
            <tr>
              <td>Nombre:</td>
              <td><?php echo $reserva->viaje ?></td>
            </tr>
            <tr>
              <td>Opcionales:</td>
              <td>
                <?php if (sizeof($reserva->opcionales) == 0) { ?>
                  -
                <?php } else { ?>
                  <?php $i=0; foreach($reserva->opcionales as $opcional) { ?>
                    <?php echo ($i!=0)?" | ":""; echo $opcional->opcional ?>
                  <?php $i++; } ?>
                <?php } ?>
              </td>
            </tr>
            <?php if (!empty($reserva->prestador_servicio)) { ?>
              <tr>
                <td>Prestador del servicio:</td>
                <td><?php echo $reserva->prestador_servicio ?></td>
              </tr>
            <?php } ?>
          <?php } ?>
        </table>
      </div>
      <?php /*
      <div class="footer">
        <div class="row" style="overflow: hidden; margin-top: 160px;">
          <div class="col-xs-4">
            <?php if(!empty($empresa->logo)) { ?>
              <img style="width: 100%" src="/sistema/<?php echo $empresa->logo ?>"/>
            <?php } ?>
          </div>
          <div class="col-xs-8" style="text-align: right;">
            <span style="margin-top: 20px; display: block;">
              
            </span>
          </div>
        </div>
      </div>
      */ ?>
    </div>
  </div>
</div>
</body>
</html>