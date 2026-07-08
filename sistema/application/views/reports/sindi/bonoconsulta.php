<!DOCTYPE HTML>
<html>
<head>
  <title>Bono Consulta</title>
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

<div style="display: inline-block; width: 18%; padding-right: 5px; border-right: 1px dotted black; margin-top: 10px; margin-left: 10px; height: 571px; align-self: top">
  <table style="font-size: 13px; align-self: top" align="center">
    <tr>
      <th style="text-align: left">
        CONSULTA N°
      </th>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 18px">
        <strong><?php echo $consulta->numero ?></strong>
      </td>
    </tr>
    <tr>
      <th style="text-align: left">
        FECHA
      </th>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 16px">
        <?php echo $consulta->fecha ?>
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
        <?php echo $consulta->codigoafiliado ?>-<?php echo $consulta->identificadorafiliado ?>
      </td>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 16px">
         <?php echo $consulta->nombreafiliado ?>
      </td>
    </tr>
    <tr>
      <td style="text-align: right; font-size: 16px">
          <strong><?php echo $consulta->condicionespecial ?></strong>
      </td>
    </tr>
    <tr style="height: 20px">
    </tr>
    <tr>
      <td style="text-align: center; font-size: 18px">
        <strong>$ <?php echo number_format($consulta->importe,2,",",".") ?><strong>
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
      <td style="text-align: center; font-size: 14px">
        <strong><?php echo ($consulta->hospital == 0) ? "" : "Hospital" ?></strong>
      </td>
    </tr>      
    <tr>
      <td style="text-align: center">
        <strong>VENCE</strong> <?php echo $consulta->vencimiento; ?>
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
          CONSULTA Nº: <?php echo $consulta->numero ?>
        </th>
      </tr>
    </table>
  </div>
  <div style="height: 10px">
  </div>
  <div style="display: inline-block; width: 100%; font-size: 13px">
    <table align="center" style="width: 100%; text-align: center">
      <tr style="height: 70px">
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Fecha<p style="text-align: center; vertical-align: bottom; font-size: 18px"><?php echo $consulta->fecha ?></p></th>
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Fecha Vencimiento<p style="text-align: center; vertical-align: bottom; font-size: 18px"><?php echo $consulta->vencimiento ?></p></th>
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Fecha de Prestación</th>       
      </tr>
    </table>
  </div>  
  <div style="height: 10px; border-bottom: 1px solid black">
  </div>
  <div style="display: inline-block; width: 100%; font-size: 17px">
    <table align="center" style="width: 100%; text-align: center">
      <tr style="height: 30px">
        <th style="width: 15%; text-align: left">Titular:</th>
        <td colspan="2" style="width: 84%; text-align: left"><?php echo $titular->codigo ?>-0 <?php echo $titular->nombre ?></td>
      </tr>
      <tr style="height: 30px">
        <th style="width: 15%; text-align: left">Beneficiario:</th>
        <td colspan="2" style="width: 84%; text-align: left"><?php echo $consulta->codigoafiliado ?>-<?php echo $consulta->identificadorafiliado ?> <?php echo $consulta->nombreafiliado ?></td>
      </tr>
      <tr style="height: 30px">
        <th style="width: 15%; text-align: left">Domicilio:</th>
        <td colspan="2" style="width: 84%;text-align: left"><?php echo $consulta->domicilio ?></td>
      </tr>      
    </table>
  </div>
  <div style="height: 10px; border-top: 1px solid black">
  </div>
  <div style="display: inline-block; width: 100%; font-size: 15px">
    <table align="center" style="width: 100%; text-align: center">
      <tr style="height: 30px">
        <td style="width: 49%; text-align: left"><strong>Condición:</strong> <?php echo $consulta->condicionespecial ?></td>
        <td style="width: 49%; text-align: left"><strong>Concepto:</strong> <?php if ($consulta->id_concepto == 1) {
                echo "";
            } elseif ($consulta->id_concepto == 2) {
                echo "Gastos";
            } elseif ($consulta->id_concepto == 3) {
                echo "Honorarios";
            } ?> </td>
      </tr>
      <tr>
        <td style="width: 49%; text-align: left"><strong>Tipo de Bono: </strong><?php echo ($consulta->id_tipo_bono == 1) ? "Ambulatorio":"Internación" ?></td>
        <td style="width: 49%; text-align: left"><strong>Hospital: </strong><?php echo ($consulta->hospital == 0) ? "No":"Si" ?></td>
      </tr>
    </table>
  </div> 
  <div style="height: 10px">
  </div>
  <div style="display: inline-block; width: 100%; font-size: 13px">
    <table align="center" style="width: 100%; text-align: center">
      <tr style="height: 90px">
        <th colspan="2" style="width: 66%; text-align: center; border: 1px solid black;vertical-align: text-top">Establecimiento</th>
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Orden de Internación Nº</th>
      </tr>
      <tr style="height: 90px">
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Firma Beneficiario</th>
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Firma y Sello Medico</th>
        <th style="width: 33%; text-align: center; border: 1px solid black;vertical-align: text-top">Diagnostico</th>       
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