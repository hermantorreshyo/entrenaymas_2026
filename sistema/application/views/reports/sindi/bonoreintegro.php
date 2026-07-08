<!DOCTYPE HTML>
<html>
<head>
  <title>Bono Reintegro</title>
<style>
@media print {
    @page { margin: 0;
     size: auto; }
}
</style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif;">

<!-- ╔══════╗ -->
<!-- ║ BONO ║ -->
<!-- ╚══════╝ -->

<div style="display: inline-block;width:98%; padding-left: 5px; margin-top: 10px; margin-right: 10px" align="center">
  <div>
    <table align="center" style="border: 1px solid black; width: 100%">
      <tr align="center">
        <th rowspan="2" style="padding: 2px; text-align: center">
          <img src="/sistema/resources/images/sindilogoos.png" alt="LOGO" height="42" width="42">
        </th>
        <th style="padding: 3px; text-align: center; font-size: 17px; height: 30px">
          Obra Social de Choferes de Camiones - Seccional Chacabuco
        </th>
      </tr>
      <tr>
        <td style="text-align: center; padding-right: 10px; font-size: 13px">
          Buenos Aires 376 - Tel (02352) 432759/432735 - Chacabuco (Bs As)
        </td>
      </tr>      
    </table>
    <table style="width:100%; padding: 2px; margin-top: 3px; text-align: center; font-size: 16px; border: 1px solid black">
      <tr>
        <th >
          REINTEGRO Nº: <?php echo $reintegro->numero ?>
        </th>
      </tr>
    </table>
  </div>
  <div style="height: 10px">
  </div>
  <div style="display: inline-block; width: 98%; font-size: 24px" align="center">
    <table align="center" style="width: 100%; text-align: center">
      <tr style="height: 30px">
        <td style="text-align: left"><strong>A la orden de: </strong><?php echo $reintegro->codigoafiliado ?>-<?php echo $reintegro->identificadorafiliado ?> <strong><?php echo $reintegro->nombreafiliado ?></strong></td>
      </tr>    
    </table>
  </div>
<div style="height: 10px">
</div>  
  <table style="display: inline-block; width: 20%; border: 1px solid black">
    <tbody style="display: table; width: 100%">
      <tr>
        <th style="vertical-align: text-top; font-size: 14px">Fecha</th>
      </tr>
      <tr>
        <th style="font-size: 18px"><?php echo $reintegro->fecha ?></th>
      </tr>
    </tbody>
  </table>
  <table style="display: inline-block; width: 28%; border: 1px solid black">
    <tbody style="display: table; width: 100%">
      <tr>
        <th style="vertical-align: text-top; font-size: 14px">Seccional</th>
      </tr>
      <tr>
        <th style="font-size: 18px"><?php if ($reintegro->id_delegacion == 1) {
              echo "Central";
          } elseif ($reintegro->id_delegacion == 2) {
              echo "Chacabuco";
          } elseif ($reintegro->id_delegacion == 3) {
              echo "Rojas";
          } elseif ($reintegro->id_delegacion == 4) {
              echo "Carmen de Areco";
          } elseif ($reintegro->id_delegacion == 5) {
              echo "San Antonio de Areco";
          } elseif ($reintegro->id_delegacion == 6) {
              echo "San Andres de Giles";
          } elseif ($reintegro->id_delegacion == 7) {
              echo "Mercedes";                            
          } ?>
        </th>
      </tr>
    </tbody>
  </table>

  <table style="display: inline-block; width: 50%; border: 1px solid black">
    <tbody style="display: table; width: 100%">
      <tr>
        <th style="vertical-align: text-top; font-size: 14px">Concepto</th>
      </tr>
      <tr>
        <th style="font-size: 18px"><?php echo $reintegro->nombrereintegro ?></th>
      </tr>
    </tbody>
  </table>
<div style="height: 10px">
</div>
  <table style="display: inline-block; width: 20%">
    <tbody style="display: table; width: 100%">
      <tr>
        <th style="vertical-align: text-top; font-size: 14px; border-bottom: 1px solid black">Factura Nº</th>
      </tr>
      <tr>
        <th style="font-size: 18px"><?php echo $reintegro->factura ?></th>
      </tr>
    </tbody>
  </table>
  <table style="display: inline-block; width: 20%;">
    <tbody style="display: table; width: 100%">
      <tr>
        <th style="vertical-align: text-top; font-size: 14px; border-bottom: 1px solid black">Recibo Nº</th>
      </tr>
      <tr>
        <th style="font-size: 18px"><?php echo $reintegro->recibo ?></th>
      </tr>
    </tbody>
  </table>

  <table style="display: inline-block; width: 58%">
    <tbody style="display: table; width: 100%">
      <tr>
        <th style="vertical-align: text-top; font-size: 14px; border-bottom: 1px solid black">Detalle</th>
      </tr>
      <tr>
        <td style="font-size: 18px; text-align: center"><?php echo $reintegro->detalle ?></td>
      </tr>
    </tbody>
  </table>

  <table style="display: inline-block; width: 38%; font-size: 24px; margin-top: 60px">
    <tbody style="display: table; width: 100%">
      <tr style="height: 50px">
        <th style="">Importe: </th>
        <td>$ <?php echo number_format($reintegro->importe_documento,2,",",".") ?></td>       
      </tr>
      <tr style="height: 50px">
        <th style="">Reintegro: </th>
        <td>$ <?php echo number_format($reintegro->importe_reintegro,2,",",".") ?></td>
      </tr>
      <tr style="height: 50px">
      </tr>
      <tr style="height: 50px">
      </tr>
      <tr style="height: 50px; align-self: bottom">
        <td colspan="2" style="vertical-align: text-top; border-top: 1px dotted black; font-size: 10px; text-align: center">Autorizado por</td>
      </tr>     
    </tbody>
  </table>
  <table style="display: inline-block; width: 5%; margin-top: 60px">

  </table>
  <table style="display: inline-block; width: 38%; font-size: 24px; margin-top: 60px">
    <tbody style="display: table; width: 100%">
      <tr style="height: 50px; align-self: bottom">
        <td colspan="2" style="vertical-align: text-top; border-top: 1px dotted black; font-size: 10px; text-align: center">Firma Afiliado</td>
      </tr>
      <tr style="height: 50px; align-self: bottom">
        <td colspan="2" style="vertical-align: text-top; border-top: 1px dotted black; font-size: 10px; text-align: center">Aclaración</td>
      </tr>
      <tr style="height: 50px; align-self: bottom">
        <td colspan="2" style="vertical-align: text-top; border-top: 1px dotted black; font-size: 10px; text-align: center">D.N.I.</td>
      </tr>
      <tr style="height: 50px; align-self: bottom">
        <td colspan="2" style="vertical-align: text-top; border-top: 1px dotted black; font-size: 10px; text-align: center">Domicilio</td>
      </tr>                          
      <tr style="height: 50px; align-self: bottom">
        <td colspan="2" style="vertical-align: text-top; border-top: 1px dotted black; font-size: 10px; text-align: center">Fecha</td>
      </tr>     
    </tbody>
  </table>


</div>
<script type="text/javascript">
window.onafterprint = function(event) {
  window.close();
}
window.print();
</script>
</body>
</html>