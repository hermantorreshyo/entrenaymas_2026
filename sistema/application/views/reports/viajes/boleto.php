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
h1 { font-weight: bold; padding: 0px; text-transform: uppercase; color: <?php echo $c1; ?>; font-family: "LatoLight"; font-size: 36px; width: 100%; }
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
</style>
</head>
<body>
<?php echo $header; ?>
<div id="printable">
  <?php foreach($boletos as $r) { ?>
    <div class="a4">
      <div class="">
        <div class="row" style="margin-top: 40px; overflow: hidden; margin-bottom: 30px; ">
          <div class="col-xs-6 info">
            <h1>BOLETO</h1>
            <p class="fs16 mt30 bold "><?php echo strtoupper((!empty($r->apellido)) ? ($r->apellido.", ".$r->nombre) : $r->nombre); ?></p>
            <?php /*
            <p><b>DNI:</b> <?php echo $r->dni ?></p>
            */ ?>
          </div>
          <div class="col-xs-6 info">
            <?php if(!empty($empresa->logo)) { ?>
              <img style="width: 100%" src="/sistema/<?php echo $empresa->logo ?>"/>
            <?php } ?>
            <div class="tac">
              <?php if (!empty($empresa->direccion)) { ?>
                <p><?php echo $empresa->direccion ?></p>
              <?php } ?>
              <?php if (!empty($empresa->email)) { ?>
                <p><i class="fa fa-envelope"></i> <?php echo $empresa->email ?></p>
              <?php } ?>
              <?php if (!empty($empresa->telefono)) { ?>
                <p><i class="fa fa-phone"></i> <?php echo $empresa->telefono ?></p>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="tabla">
          <h2 class="subtitulo">INFORMACI&Oacute;N</h2>
          <div class="row">
            <div class="col-xs-6">
              <table>
                <tr>
                  <td>Fecha de reserva:</td>
                  <td><?php echo $r->fecha_reserva ?></td>
                </tr>
                <tr>
                  <td>Fechas de salida:</td>
                  <td><?php echo $r->viaje_fecha ?></td>
                </tr>
                <tr>
                  <td>N&uacute;mero de asiento:</td>
                  <td><?php echo $r->numero_asiento ?>
                    (Piso de <?php echo ($r->piso == 1)?"arriba":"abajo"; ?>)
                  </td>
                </tr>
                <?php if (!empty($r->hotel)) { ?>
                  <tr>
                    <td>Hotel:</td>
                    <td><?php echo $r->hotel ?></td>
                  </tr>
                <?php } ?>
              </table>
            </div>
            <div class="col-xs-6">
              <table>
                <tr>
                  <td>Destino:</td>
                  <td><?php echo $r->viaje_nombre; ?></td>
                </tr>
                <tr>
                  <td>Fechas de regreso:</td>
                  <td><?php echo $r->viaje_fecha_llegada ?></td>
                </tr>
                <?php if (!empty($r->hotel)) { ?>
                  <tr>
                    <td>Habitacion:</td>
                    <td><?php echo $r->numero_habitacion ?></td>
                  </tr>
                <?php } ?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
</body>
</html>