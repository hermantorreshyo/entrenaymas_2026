<!DOCTYPE HTML>
<html>
<head>
  <title>Bono Practica</title>
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

  <div style="display: inline-block; width: 18%; padding-right: 5px; border-right: 1px dotted black; margin-top: 10px; margin-left: 10px; height: 560px; align-self: top">
    <table style="font-size: 13px; align-self: top" align="center">
      <tr>
        <th style="text-align: left">
          PRACTICA N°
        </th>
      </tr>
      <tr>
        <td style="text-align: right; font-size: 18px">
          <strong><?php echo $practica->numero ?></strong>
        </td>
      </tr>
      <tr>
        <th style="text-align: left">
          FECHA
        </th>
      </tr>
      <tr>
        <td style="text-align: right; font-size: 16px">
          <?php echo $practica->fecha ?>
        </td>
      </tr>
      <tr>
        <th style="text-align: left">
          BENEFICIARIO
        </th>
      </tr>
      <tr>
        <td style="text-align: right; font-size: 14px">
          <?php echo $practica->codigoafiliado ?>-<?php echo $practica->identificadorafiliado ?>
        </td>
      </tr>
      <tr>
        <td style="text-align: right; font-size: 16px">
          <?php echo $practica->nombreafiliado ?>
        </td>
      </tr>
      <tr>
        <td style="text-align: center; font-size: 14px">
          <strong><?php echo $practica->condicionespecial ?></strong>
        </td>
      </tr>
    </table>

    <table style="font-size: 9px; align-self: top" align="center">
      <thead>
        <tr>
          <th style="text-align: left; width: 50%">Codigo</th>
          <th style="text-align: center; width: 20%">Cant.</th>
          <th style="text-align: center; width: 30%">Valor</th>
        </tr>
      </thead>
      <tbody>
        <?php for($i=0;$i<sizeof($practica->items);$i++) { ?>
          <?php $item = $practica->items[$i] ?>
          <tr>
            <td style="text-align: left; width: 50%"><?php echo $item->codigo ?></td>
            <td style="text-align: center; width: 20%"><?php echo $item->cantidad ?></td>
            <td style="text-align: center; width: 30%"><?php echo number_format($item->cantidad * $item->importe_unitario,2,",",".") ?></td>
          </tr>
        <?php } ?>
        <?php for($i=sizeof($practica->items);$i<18;$i++) { ?>
          <tr>
            <td style="text-align: left; width: 50%">&nbsp;</td>
            <td style="text-align: center; width: 20%">&nbsp;</td>
            <td style="text-align: center; width: 30%">&nbsp;</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <table style="font-size: 13px; align-self: top" align="center">
      <tr>
        <td style="width:80%; text-align: center; font-size: 16px">
          <strong>$ <?php echo number_format($practica->importe,2,",",".") ?><strong>
        </td>
      </tr>
      <tr style="height: 20px">
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
          <strong><?php echo ($practica->hospital == 0) ? "" : "Hospital" ?></strong>
        </td>
      </tr>      
      <tr>
        <td style="text-align: center">
          <strong>VENCE</strong> <?php echo $practica->vencimiento; ?>
        </td>
      </tr>             
    </table>
  </div>

    <!-- ╔══════╗ -->
    <!-- ║ BONO ║ -->
    <!-- ╚══════╝ -->
    <div style="display: inline-block; width:77%; padding-left: 5px; margin-right: 10px; height: 560px; align-self: top">
      <div style="display: inline-block; width:48%">
        <div>
          <table align="center" style="border: 1px solid black; width: 100%">
            <tr align="center">
              <th rowspan="2" style="padding: 2px; text-align: center">
                <img src="/sistema/resources/images/sindilogoos.png" alt="LOGO" height="42" width="42">
              </th>
              <th style="padding: 3px; text-align: center; font-size: 14px; height: 30px">
                O.S. de Choferes de Camiones Seccional Chacabuco
              </th>
            </tr>
            <tr>
              <td style="text-align: center; padding-right: 10px; font-size: 11px">
                Buenos Aires 376 - Chacabuco (Bs As)
              </td>
            </tr>      
          </table>
          <table style="width:100%; padding: 2px; margin-top: 3px; text-align: center; font-size: 16px; border: 1px solid black">
            <tr>
              <th >
                PRACTICA Nº: <?php echo $practica->numero ?>
              </th>
            </tr>
          </table>
        </div>

        <div style="display: inline-block; width: 100%; font-size: 10px" align="center">
          <table style="display: inline-block; width: 31%; text-align: center; border: 1px solid black;vertical-align: text-top">
            <tbody style="width: 100%; display: table">
              <tr>
                <th style="font-size: 8px">Fecha Impresión</th>
              </tr>
              <tr>
                <td style="font-size: 14px"><?php echo $practica->fecha ?></td>
              </tr>
            </tbody>
          </table>
          <table style="display: inline-block; width: 31%; text-align: center; border: 1px solid black;vertical-align: text-top">
            <tbody style="width: 100%; display: table">
              <tr>
                <th style="font-size: 8px">Fecha Vencimiento</th>
              </tr>
              <tr>
                <td style="font-size: 14px"><?php echo $practica->vencimiento ?></td>
              </tr>
            </tbody>
          </table>
          <table style="display: inline-block; height: 36px; width: 31%; text-align: center; border: 1px solid black;vertical-align: text-top">
            <tbody style="width: 100%; display: table">
              <tr>
                <th style="font-size: 8px">Fecha Prestación</th>
              </tr>
              <tr>
                <td style="font-size: 14px"></td>
              </tr>
            </tbody>
          </table>   
        </div>  
        <div style="display: inline-block; width: 100%; font-size: 14px">
          <table align="center" style="width: 100%; text-align: center">
            <tr>
              <td colspan="2" style="text-align: left"><strong>Titular:</strong> <?php echo $practica->codigoafiliado ?>-0 <strong><?php echo $titular->nombre ?></strong></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: left"><strong>Beneficiario:</strong> <?php echo $practica->codigoafiliado ?>-<?php echo $practica->identificadorafiliado ?> <strong><?php echo $practica->nombreafiliado ?></strong></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: left"><strong>Cond. Especial:</strong> <?php echo $practica->condicionespecial ?></td>
            </tr>      
            <tr>
              <td style="width: 79%; text-align: left"><strong>Tipo:</strong> <?php echo $practica->nombretipopractica ?></th>
              <td style="width: 20%; text-align: right;"><strong><?php echo ($practica->hospital == 0) ? "" : "Hospital" ?></strong></td>
            </tr>
          </table>  
        </div>

        <div style="width: 100%">
          <table style="font-size: 10px; width: 100%">
            <thead>
              <th style="width: 70%; text-align: left; border-bottom: 1px solid black">Practica Medica (HONORARIOS)</th>
              <th style="width: 10%; border-bottom: 1px solid black">Cant.</th>
              <th style="width: 20%; border-bottom: 1px solid black">Codigo</th>
            </thead>
            <tbody id="practicas">
              <?php for($i=0;$i<sizeof($practica->items);$i++) { ?>
                <?php $item = $practica->items[$i] ?>
                <tr>
                  <td><?php echo $item->nombre_nomenclador ?></td>
                  <td style="text-align: center"><?php echo $item->cantidad ?></td>
                  <td style="text-align: center"><?php echo $item->codigo ?></td>
                </tr>
              <?php } ?>
              <?php for($i=sizeof($practica->items);$i<18;$i++) { ?>
                <tr>
                  <td>&nbsp;</td>
                  <td style="text-align: center">&nbsp;</td>
                  <td style="text-align: center">&nbsp;</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

        <div style="display: inline-block; width: 100%; font-size: 13px">
          <table align="center" style="width: 100%; text-align: center">
            <tr style="height: 60px">
              <th style="width: 50%; text-align: center; border: 1px solid black;vertical-align: text-top">Firma y Sello Medico</th>
              <th style="width: 50%; text-align: center; border: 1px solid black;vertical-align: text-top">Firma Beneficiario</th>      
            </tr>
          </table>
        </div>
      </div>      

      <!-- ╔══════╗ -->
      <!-- ║ BONO ║ -->
      <!-- ╚══════╝ -->

<div style="display: inline-block; width:48%">
        <div>
          <table align="center" style="border: 1px solid black; width: 100%">
            <tr align="center">
              <th rowspan="2" style="padding: 2px; text-align: center">
                <img src="/sistema/resources/images/sindilogoos.png" alt="LOGO" height="42" width="42">
              </th>
              <th style="padding: 3px; text-align: center; font-size: 14px; height: 30px">
                O.S. de Choferes de Camiones Seccional Chacabuco
              </th>
            </tr>
            <tr>
              <td style="text-align: center; padding-right: 10px; font-size: 11px">
                Buenos Aires 376 - Chacabuco (Bs As)
              </td>
            </tr>      
          </table>
          <table style="width:100%; padding: 2px; margin-top: 3px; text-align: center; font-size: 16px; border: 1px solid black">
            <tr>
              <th >
                PRACTICA Nº: <?php echo $practica->numero ?>
              </th>
            </tr>
          </table>
        </div>

        <div style="display: inline-block; width: 100%; font-size: 10px" align="center">
          <table style="display: inline-block; width: 31%; text-align: center; border: 1px solid black;vertical-align: text-top">
            <tbody style="width: 100%; display: table">
              <tr>
                <th style="font-size: 8px">Fecha Impresión</th>
              </tr>
              <tr>
                <td style="font-size: 14px"><?php echo $practica->fecha ?></td>
              </tr>
            </tbody>
          </table>
          <table style="display: inline-block; width: 31%; text-align: center; border: 1px solid black;vertical-align: text-top">
            <tbody style="width: 100%; display: table">
              <tr>
                <th style="font-size: 8px">Fecha Vencimiento</th>
              </tr>
              <tr>
                <td style="font-size: 14px"><?php echo $practica->vencimiento ?></td>
              </tr>
            </tbody>
          </table>
          <table style="display: inline-block; height: 36px; width: 31%; text-align: center; border: 1px solid black;vertical-align: text-top">
            <tbody style="width: 100%; display: table">
              <tr>
                <th style="font-size: 8px">Fecha Prestación</th>
              </tr>
              <tr>
                <td style="font-size: 14px"></td>
              </tr>
            </tbody>
          </table>   
        </div>  
        <div style="display: inline-block; width: 100%; font-size: 14px">
          <table align="center" style="width: 100%; text-align: center">
            <tr>
              <td colspan="2" style="text-align: left"><strong>Titular:</strong> <?php echo $practica->codigoafiliado ?>-0 <strong><?php echo $titular->nombre ?></strong></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: left"><strong>Beneficiario:</strong> <?php echo $practica->codigoafiliado ?>-<?php echo $practica->identificadorafiliado ?> <strong><?php echo $practica->nombreafiliado ?></strong></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: left"><strong>Cond. Especial:</strong> <?php echo $practica->condicionespecial ?></td>
            </tr>      
            <tr>
              <td style="width: 79%; text-align: left"><strong>Tipo:</strong> <?php echo $practica->nombretipopractica ?></th>
              <td style="width: 20%; text-align: right;"><strong><?php echo ($practica->hospital == 0) ? "" : "Hospital" ?></strong></td>
            </tr>
          </table>  
        </div>

        <div style="width: 100%">
          <table style="font-size: 10px; width: 100%">
            <thead>
              <th style="width: 70%; text-align: left; border-bottom: 1px solid black">Practica Medica (GASTOS)</th>
              <th style="width: 10%; border-bottom: 1px solid black">Cant.</th>
              <th style="width: 20%; border-bottom: 1px solid black">Codigo</th>
            </thead>
            <tbody id="practicas">
              <?php for($i=0;$i<sizeof($practica->items);$i++) { ?>
                <?php $item = $practica->items[$i] ?>
                <tr>
                  <td><?php echo $item->nombre_nomenclador ?></td>
                  <td style="text-align: center"><?php echo $item->cantidad ?></td>
                  <td style="text-align: center"><?php echo $item->codigo ?></td>
                </tr>
              <?php } ?>
              <?php for($i=sizeof($practica->items);$i<18;$i++) { ?>
                <tr>
                  <td>&nbsp;</td>
                  <td style="text-align: center">&nbsp;</td>
                  <td style="text-align: center">&nbsp;</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

        <div style="display: inline-block; width: 100%; font-size: 13px">
          <table align="center" style="width: 100%; text-align: center">
            <tr style="height: 60px">
              <th style="width: 50%; text-align: center; border: 1px solid black;vertical-align: text-top">Firma y Sello Medico</th>
              <th style="width: 50%; text-align: center; border: 1px solid black;vertical-align: text-top">Firma Beneficiario</th>      
            </tr>
          </table>
        </div>
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