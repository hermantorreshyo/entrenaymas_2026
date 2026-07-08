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
          <h1>RESERVA</h1>
        </div>
        <div class="col-xs-6">
          <?php if(!empty($empresa->logo)) { ?>
            <img style="width: 100%" src="/sistema/<?php echo $empresa->logo ?>"/>
          <?php } ?>
        </div>
      </div>
      <div class="row" style="margin-top: 40px; overflow: hidden; margin-bottom: 30px; ">
        <div class="col-xs-6 info">
          <h2 class="subtitulo">CLIENTE</h2>
          <p class="fs20 bold "><?php echo strtoupper($reserva->cliente->nombre); ?></p>
          <p><b>Email:</b> <?php echo $reserva->cliente->email ?></p>
          <p><b>Telefono:</b> <?php echo $reserva->cliente->telefono ?></p>
        </div>
        <div class="col-xs-6 info">
          <h2 class="subtitulo">INFORMACI&Oacute;N</h2>
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
      <div class="tabla">
        <h2 class="subtitulo">INFORMACI&Oacute;N</h2>
        <table>
          <tr>
            <td>Desde:</td>
            <td><?php echo $reserva->fecha_desde ?></td>
          </tr>
          <tr>
            <td>Hasta:</td>
            <td><?php echo $reserva->fecha_hasta ?></td>
          </tr>
          <tr>
            <td>Habitacion:</td>
            <td><?php echo $reserva->tipo_habitacion->nombre ?></td>
          </tr>
          <tr>
            <td>Comentarios:</td>
            <td><?php echo $reserva->comentario ?></td>
          </tr>
        </table>
      </div>
      <div class="footer">
        <div class="row" style="overflow: hidden; margin-top: 160px;">
          <div class="col-xs-4">
            <?php if(!empty($empresa->logo)) { ?>
              <img style="width: 100%" src="/sistema/<?php echo $empresa->logo ?>"/>
            <?php } ?>
          </div>
          <div class="col-xs-8" style="text-align: right;">
            <?php if (isset($empresa->dominios) && sizeof($empresa->dominios)>0) { ?>
              <span style="margin-top: 20px; display: block;">
                <?php echo $empresa->dominios[0] ?>
              </span>
            <?php } ?>
            <?php if (isset($empresa->facebook) && !empty($empresa->facebook)) { ?>
              <span style="margin-top: 20px; display: block;">
                <?php echo $empresa->facebook ?>
              </span>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>