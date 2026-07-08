<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/bootstrap-cols.css" />
<style type="text/css">
body { background-color: white; font-size: 12px; }
.borde { border: solid 1px black; padding: 5px; max-width: 400px; margin-top: 30px; line-height: 18px; font-size: 14px; }
table { width: 100%; }
table td { padding-bottom: 1px; font-size: 12px; vertical-align: top; }
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
          Otorgado d&iacute;a
        </td>
        <td>
          <?php echo $prestamo->fecha ?>
        </td>
      </tr>
      <tr>
        <td class="w50p">
          Monto $
        </td>
        <td>
          <?php echo $prestamo->monto_prestado ?>
        </td>
      </tr>
      <tr>
        <td class="w50p">
          N&deg; de Cuotas  
        </td>
        <td>
          <?php echo $prestamo->cantidad_cuotas ?>  
        </td>
      </tr>
      <tr>
        <td class="w50p">
          Importe Cta. $  
        </td>
        <td>
          <?php echo $prestamo->valor_cuota ?>  
        </td>
      </tr>
      <tr>
        <td class="w50p">
          1&deg; Vencimiento  
        </td>
        <td>
          <?php $primera_cuota = $prestamo->cuotas[0]; 
          echo $primera_cuota->fecha_vencimiento; ?>
        </td>
      </tr>
      <tr>
        <td class="w50p">
          Plan
        </td>
        <td>
          <?php echo $prestamo->plan ?>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>