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
  <div class="a4">

    <?php for($k=0;$k<2;$k++) { ?>
      <div class="">
        <div class="row" style="margin-top: 40px; overflow: hidden;">
          <div class="col-xs-6 info">
            <h1>RECIBO</h1>
            <div class="tabla">
              <table>
                <tr>
                  <td>Cliente:</td>
                  <td>
                    <?php if (!empty($reserva->cliente)) { 
                      echo $reserva->cliente; 
                    } else if (!empty($reserva->asientos)) { 
                      $primero = $reserva->asientos[0];
                      echo $primero->nombre." ".$primero->apellido;
                    } ?>
                  </td>
                </tr>
                <tr>
                  <td>Fecha de reserva:</td>
                  <td><?php echo $reserva->fecha_reserva ?></td>
                </tr>
                <tr>
                  <td>Destino:</td>
                  <td><?php echo $reserva->viaje; ?></td>
                </tr>
                <?php if (!empty($reserva->vendedor)) { ?>
                  <tr>
                    <td>Vendedor:</td>
                    <td><?php echo $reserva->vendedor ?></td>
                  </tr>
                <?php } ?>
                <tr>
                  <td>Cantidad de pasajeros:</td>
                  <td><?php echo sizeof($reserva->asientos) ?></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-xs-6 info tac">
            <?php if(!empty($empresa->logo)) { ?>
              <img style="width: 100%" src="/sistema/<?php echo $empresa->logo ?>"/>
            <?php } ?>
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
        <div class="row">
          <?php if (sizeof($reserva->pagos)>0) { ?>
            <div class="col-xs-6">
              <div class="tabla">
                <h2 class="subtitulo">PAGOS REALIZADOS</h2>
                <table>
                  <?php foreach($reserva->pagos as $pago) { ?>
                    <tr>
                      <td><?php echo $pago->fecha ?></td>
                      <td><?php echo $pago->metodo ?></td>
                      <td>$ <?php echo round($pago->total,2) ?></td>
                    </tr>
                  <?php } ?>
                </table>
              </div>
            </div>
          <?php } ?>
          <div class="col-xs-6">
            <div class="tabla">
              <h2 class="subtitulo">RESUMEN</h2>
              <table>
                <tr>
                  <td>Total del servicio:</td>
                  <td><b>$ <?php echo round($reserva->total,2); ?></b></td>
                </tr>
                <tr>
                  <td>Total cancelado:</td>
                  <td><b>$ <?php echo round($reserva->pagado,2); ?></b></td>
                </tr>
                <tr>
                  <td>Resto:</td>
                  <td><b>$ <?php echo round($reserva->total - $reserva->pagado,2) ?></b></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>

  </div>
</div>
</body>
</html>