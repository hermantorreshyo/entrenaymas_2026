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
          <?php echo date("d/m/Y") ?>
        </td>
      </tr>
      <tr>
        <td class="w50p">
           Importe $
        </td>
        <td>
          <?php echo number_format($total,2) ?>  
        </td>
      </tr>      
      <tr>
        <td class="w50p">Cuotas</td>
        <td><?php echo implode(",", $numeros_cuotas)?></td>
      </tr>
      <?php if ($cancelo == 1) { ?>
        <tr>
          <td class="w50p">&nbsp;</td>
          <td><b>CANCELADO</b></td>
        </tr>
      <?php } ?>
    </table>
  </div>
</body>
</html>