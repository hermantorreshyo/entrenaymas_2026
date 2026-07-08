<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/bootstrap-cols.css" />
<style type="text/css">
body { background-color: white; font-size: 12px; }
.borde { border: solid 1px black; padding: 10px; max-width: 360px; margin-top: 30px; line-height: 18px; font-size: 14px; }
table { width: 100%; }
table td { padding-bottom: 1px; font-size: 13px; vertical-align: top; letter-spacing: 0.05em; }
.titulo { margin-top: 5px; margin-bottom: 5px; text-align: center; line-height: 1.1em; font-size: 12px; }
</style>
</head>
<body>
  <div class="borde">
    <div>
      <img class="w100p" src="/sistema/<?php echo $empresa->logo ?>"/>
    </div>
    <div class="titulo">
      <?php if ($sucursal !== FALSE) { ?>
        <b><?php echo $sucursal->nombre." - ".$sucursal->direccion ?></b><br/>
      <?php } else { ?>
        <b>ENSENADA - Pte. Peron N&deg; 343 1/2</b><br/>
      <?php } ?>
      Comprobante no v&aacute;lido como factura.-<br/>
      Una vez confirmado este detalle exija su ticket.<br/>
      <span style="text-decoration: underline;">Apellido y Nombres del Titular</span>
    </div>
    <table>
      <tr>
        <td class="bold w50p">
          <?php echo $cliente->apellido ?>
        </td>
        <td class="bold">
          <?php echo $cliente->nombre ?>
        </td>
      </tr>
      <tr>
        <td class="bold w50p">
          <?php echo ($cliente->id_tipo_documento == 96) ? "DNI":"" ?>
          <?php echo ($cliente->id_tipo_documento == 80) ? "CUIT":"" ?>
        </td>
        <td class="bold">
          <?php echo $cliente->documento ?>
        </td>
      </tr>
      <tr>
        <td class="w50p">
          Cr&eacute;dito N&deg;  
        </td>
        <td>
          <?php echo $prestamo->numero ?>  
        </td>
      </tr>
      <tr>
        <td class="w50p">
          Fecha de pago
        </td>
        <td>
          <?php echo $cuota->fecha_pago ?>
        </td>
      </tr>
      <?php $restan = $prestamo->cantidad_cuotas - $cuota->numero; ?>
      <tr>
        <td class="w50p">
          Cuota N&deg; <?php echo $cuota->numero ?>
        </td>
        <td>
          Restan <?php echo $restan ?>
        </td>
      </tr>
      <?php if ($restan == 0) { ?>
        <tr>
          <td class="w50p">&nbsp;</td>
          <td><b>CANCELADO</b></td>
        </tr>
      <?php } ?>
      <?php 
      $descuento = 0;
      foreach($cuota->pagos as $pp) $descuento += $pp->descuento;
      if ($descuento > 0) { ?>
        <tr>
          <td class="w50p">
            Descuento $
          </td>
          <td>
            <?php echo number_format(round($descuento,0),2) ?>  
          </td>
        </tr>        
      <?php } ?>
      <tr>
        <td class="w50p">
           Importe $
        </td>
        <td>
          <?php echo number_format(round($cuota->monto_pagado + $cuota->interes_pagado - $descuento,0),2) ?>  
        </td>
      </tr>
      <?php if (isset($prestamo->cuotas[$cuota->numero])) { ?>
        <tr>
          <td class="w50p">
            Prox. Vencimiento
          </td>
          <td class="bold">
            <?php 
            $prox_cuota = $prestamo->cuotas[$cuota->numero]; 
            echo $prox_cuota->fecha_vencimiento; ?>
          </td>
        </tr>
      <?php } ?>
    </table>
  </div>
</body>
</html>