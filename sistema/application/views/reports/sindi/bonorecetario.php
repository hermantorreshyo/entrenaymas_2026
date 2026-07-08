<!DOCTYPE HTML>
<html>
<head>
  <title>Bono Recetario</title>
<style>
@media print {
    @page { margin: 0;
     size: auto; }
}
</style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif;">


<!-- ╔═══════╗ -->
<!-- ║ TALON ║ -->
<!-- ╚═══════╝ -->

<div style="display: inline-block; width: 18%; padding-right: 5px; border-right: 1px dotted black; margin-top: 10px; margin-left: 10px; height: 596px">
  <table style="font-size: 13px; align-self: top" align="center">
    <tr>
      <th style="text-align: left">
        RECETARIO N°
      </th>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 18px">
        <strong><?php echo $recetario->numero ?></strong>
      </td>
    </tr>
    <tr>
      <th style="text-align: left">
        FECHA
      </th>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 16px">
        <?php echo $recetario->fecha ?>
      </td>
    </tr>
    <tr>
      <th style="text-align: left">
        TITULAR
      </th>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 14px">
        <?php echo $titular->codigo ?>-<?php echo $titular->identificador ?>
      </td>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 16px">
          <?php echo $titular->nombre ?>
      </td>
    </tr>
    <tr>
      <th style="text-align: left">
        BENEFICIARIO
      </th>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 14px">
        <?php echo $recetario->codigoafiliado ?>-<?php echo $recetario->identificadorafiliado ?>
      </td>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 16px">
         <?php echo $recetario->nombreafiliado ?>
      </td>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 16px">
          <strong><?php echo $recetario->condicionespecial ?></strong>
      </td>
    </tr>
    <tr style="height: 20px">
    </tr>
    <tr>
      <td style="text-align: center; font-size: 40px">
        <strong><?php echo $recetario->porcentaje ?>%<strong>
      </td>
    </tr>
    <tr style="height: 140px">
    </tr>
    <tr>
      <th style="text-align: center">
        ...........................
      </th>
    </tr>
    <tr>
      <th style="text-align: center; font-size: 11px">
        FIRMA
      </th>
    </tr>
    <tr>
      <td style="text-align: center">
        <strong>VENCE</strong> <?php echo $recetario->vencimiento; ?>
      </td>
    </tr>             
  </table>
</div>

<!-- ╔══════╗ -->
<!-- ║ BONO ║ -->
<!-- ╚══════╝ -->

<div style="display: inline-block;width:77%; padding-left: 5px; margin-top: 10px; margin-right: 10px">
  <div>
    <table align="center" style="border: 1px solid black; width: 100%">
      <tr align="center">
        <th rowspan="3" style="padding: 2px; text-align: center">
          <img src="/sistema/resources/images/sindilogoos.png" alt="LOGO" height="42" width="42">
        </th>
        <th style="padding: 3px; text-align: center; font-size: 17px">
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
          RECETARIO Nº: <?php echo $recetario->numero ?>
        </th>
      </tr>
    </table>
  </div>

  <div style="height: 10px">
  </div>

  <div id="troqueles" style="display: inline-block; width: 20%"> 
    <table align="center" style="width: 100%; text-align: center">
      <tr style="height: 80px; font-size: 11px">
        <th style="text-align: center; border: 1px dotted black">TROQUEL 1</th>
      </tr>
      <tr style="height: 80px; font-size: 11px">
        <th style="text-align: center; border: 1px dotted black">TROQUEL 2</th>
      </tr>    
      <tr style="height: 80px; font-size: 11px">
        <th style="text-align: center; border: 1px dotted black">TROQUEL 3</th>
      </tr>
    </table>
  </div>
  <div style="display: inline-block; width: 79%">
    <table style="width: 100%">
      <tr style="height: 30px">
        <th style="width: 11%; text-align: left">Afiliado:</th>
        <td colspan="2" style="width: 74%"><?php echo $recetario->codigoafiliado ?>-<?php echo $recetario->identificadorafiliado ?> <?php echo $recetario->nombreafiliado ?></td>
        <th style="height: 35px; width: 7%; text-align: center; border: 1px solid black;vertical-align: text-top; font-size: 8px">Edad</th>
        <th style="height: 35px; width: 7%; text-align: center; border: 1px solid black;vertical-align: text-top; font-size: 8px">Sexo</th>
      </tr>
    </table>
    <table align="center" style="width: 100%; text-align: center">
      <tr style="height: 50px; font-size: 13px">
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Fecha Prescripción</th>
        <td style="width: 33%; text-align: center;vertical-align: center; font-size: 10px"><i>SE RECONOCERAN UNICAMENTE MEDICAMENTOS INCLUIDOS EN EL VADEMECUM DE O.S.CHO.CA.</i></td>
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Fecha Expendio</th>
      </tr>
    </table>
    <table style="width: 100%; font-size: 13px">
      <tr>
        <th style="width: 80%; text-align: left; padding-left: 2px; vertical-align: text-top; border: 1px solid black; height: 45px">R.p./ 1º</th>
        <th style="width: 19%; border: 1px solid black"></th>
      </tr>
      <tr>
        <th style="width: 80%; text-align: left; padding-left: 2px; vertical-align: text-top; border: 1px solid black; height: 45px">2º</th>
        <th style="width: 19%; border: 1px solid black"></th>
      </tr>
      <tr>
        <th style="width: 80%; text-align: left; padding-left: 2px; vertical-align: text-top; border: 1px solid black; height: 45px">Diagnostico:</th>
        <th style="width: 19%; border: 1px solid black"></th>  
      </tr>
    </table>
  </div>

  <div style="height: 10px">
  </div>

  <div>
    <table style="width: 100%; font-size: 13px">
      <tr>
        <th style="font-size: 40px; width: 24%; text-align: center; border: 1px solid black"><?php echo $recetario->porcentaje ?>%</th>
        <th style="width: 25%; text-align: center; border: 1px solid black;vertical-align: text-top">Obra Social %</th>
        <th style="width: 25%; text-align: center; border: 1px solid black;vertical-align: text-top">Afiliado %</th>
        <th style="width: 25%; text-align: center; border: 1px solid black;vertical-align: text-top">Total Factura</th>        
      </tr>
    </table>
  </div>

  <div style="height: 10px">
  </div>

  <div style="display: inline-block; width: 100%; font-size: 13px">
    <table align="center" style="width: 100%; text-align: center">
      <tr style="height: 70px">
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Firma Beneficiario</th>
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Firma y Sello Medico</th>
        <td style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top"><strong>Firma y Sello Farmaceutico</strong><p style="text-align: center; vertical-align: bottom; font-size: 11px"><i>Certifica entrega de medicamentos facturados</i></p></td>       
      </tr>  
      <tr style="height: 90px">
        <td colspan="3" style="width: 100%; text-align: center; border: 1px solid black;vertical-align: bottom">Para uso exclusivo de O.S.CHO.CA.</td> 
      </tr>
    </table>
  </div>      
</div>
<script type="text/javascript">
window.onafterprint = function(event) {
  window.close();
}
window.print();
</script>
</body>
</html>