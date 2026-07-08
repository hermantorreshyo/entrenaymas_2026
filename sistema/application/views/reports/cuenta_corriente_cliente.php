<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/bootstrap-cols.css" />
<style type="text/css">
body {
  background-color: white;
}
.tabla tr td {
  padding-bottom: 3px;
  padding-top: 3px;
}
@media print {
  body {-webkit-print-color-adjust: exact; margin:0px; }
}
@page {
  size: auto;
  margin: 30px 0px 30px 0px;
}

.gran-tabla { width: 100%; font-size: 14px; border-spacing: 0; }
.gran-tabla > thead > tr > th { font-weight: bold; font-size: 16px; text-align: left; padding: 5px 0px; }
.gran-tabla > tbody > tr > td { border-top: solid 1px black; padding: 5px 3px; }
.gran-tabla .fila-final td { padding-bottom: 30px; }
</style>
</head>
<body>
<?php echo $header; ?>
<div class="p30">
  <div class="header oh">
    <div class="subtitulo fl">
      CUENTA CORRIENTE DE <?php echo $cliente->nombre ?>
    </div>
    <div class="fr">
      Periodo: <span class="bold"><?php echo $fecha_desde ?> / <?php echo $fecha_hasta ?></span>
    </div>
  </div>
  <table class="gran-tabla">
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Comprobante</th>
        <th class="tar">Debe</th>
        <th class="tar">Haber</th>
        <th class="tar">Saldo</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $saldo = 0;
      $debe = 0;
      $haber = 0;
      $saldoParcial = $saldo_inicial; ?>
      <tr>
        <td>Saldo Inicial</td>
        <td></td>
        <td></td>
        <td></td>
        <td class="tar"><?php echo number_format($saldoParcial,2,",",".") ?></td>
      </tr>

      <?php foreach($datos as $row) { 
        $total = $row->total;
        $totalComprobante = $total;   
        $pago = $row->pago;
        
        if ($row->negativo == 1) { // Nota de Credito
          // Invertimos los valores
          $aux = $total;
          $total = $pago;
          $pago = -$aux;
          
        } else if ($row->negativo == 0 && $total < 0) {
          // Remito negativo
          $aux = $pago;
          $pago = $total;
          $total = $aux;
        }
        
        //$pagoFactura = pago;        
        if ($total < 0) {
          $haber = abs($total);
        } else {
          $debe = $total;
        }
        
        if ($pago > 0) {
          $debe += $pago;
        } else {
          $haber = abs($pago);
        }        
        
        // Si la factura esta anulada, no se cuenta NADA
        if ($row->anulada == 1) {
          $debe = $haber;
        } else {
          $saldoParcial = $saldoParcial + $debe - $haber;
        }
        ?>
        <tr>
          <td><?php echo $row->fecha ?></td>
          <td><?php echo $row->comprobante ?></td>
          <?php if ($detalle_items == 0) { ?>
            <td class="tar"><?php echo number_format($debe,2,",",".") ?></td>
            <td class="tar"><?php echo number_format($haber,2,",",".") ?></td>
            <td class="tar"><?php echo number_format($saldoParcial,2,",",".") ?></td>
          <?php } else { ?>
            <td></td>
            <td></td>
            <td></td>
          <?php } ?>
        </tr>
        <?php if ($detalle_items == 1) { ?>
          <tr>
            <td colspan="5">
              <table class="table">
                <thead>
                  <tr>
                    <th class="w70">Cant.</th>
                    <th>Desc.</th>
                    <th class="w70 tar">Unit.</th>
                    <th class="w70 tar">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($row->items as $item) { 
                    if ($item->anulado == 0) { ?>
                      <tr>
                        <td><?php echo $item->cantidad ?></td>
                        <td><?php echo $item->nombre ?></td>
                        <td class="tar"><?php echo $item->precio ?></td>
                        <td class="tar"><?php echo $item->total_con_iva ?></td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td class="tar"><b>TOTAL:</b></td>
                    <td class="tar"><?php echo number_format($row->total,2,",",".") ?></td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          <tr class="fila-final">
            <td></td>
            <td></td>
            <td class="tar"><?php echo number_format($debe,2,",",".") ?></td>
            <td class="tar"><?php echo number_format($haber,2,",",".") ?></td>
            <td class="tar"><?php echo number_format($saldoParcial,2,",",".") ?></td>
          </tr>
        <?php } ?>
      <?php } ?>
    </tbody>
  </table>
</div>
</body>
</html>