<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
body {
  background-color: white;
}
.subtitulo { font-size: 15px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; border-bottom: solid 1px #222; }
.tabla tr td {
  padding-bottom: 3px;
  padding-top: 3px;
}
.tabla tr th {
  padding-bottom: 3px;
  padding-top: 3px;
  font-weight: bold;
}
@media print {
  body {-webkit-print-color-adjust: exact; margin:0px; }
}
@page {
  size: auto;
  margin: 30px 0px 30px 0px;
}
</style>
</head>
<body>
<?php echo $header; ?>
  <div class="p30">
    <div class="header oh mb15">
      <div class="subtitulo fl">
        REPORTE DE CAJA DIARIA
      </div>
      <div class="fr">
        <span>Fecha: <span class="bold"><?php echo $caja->fecha; ?></span></span>
      </div>
    </div>
    <div class="oh mb15">
      <div class="fr">
        <span>Punto Venta: <b><?php echo $punto_venta->numero ?></b></span>
        <span style="margin-left: 10px">Suc: <b><?php echo $punto_venta->sucursal ?></b></span>
      </div>
      <div class="fl">
        Usuario: <b><?php echo $caja->usuario; ?></b><br/>
      </div>
    </div>

    <div class="subtitulo">Movimientos en efectivo</div>
    <table class="tabla">
      <thead>
        <tr>
          <th>Concepto</th>
          <th style="width: 120px">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Efectivo Inicial</td>
          <td>$ <?php echo number_format($caja->efectivo_inicial,2) ?></td>
        </tr>
        <tr>
          <td>Ventas en efectivo</td>
          <td>$ <?php echo number_format($caja->efectivo,2) ?></td>
        </tr>
        <?php if ($caja->pago_efectivo > 0) { ?>
          <tr>
            <td>Pago de Cuenta Corriente en efectivo</td>
            <td>$ <?php echo number_format($caja->pago_efectivo,2) ?></td>
          </tr>
        <?php } ?>
        <tr>
          <td>Gastos</td>
          <td>$ <?php echo number_format($caja->salida_efectivo,2) ?></td>
        </tr>
        <tr>
          <td>Efectivo esperado</td>
          <td>$ <?php echo number_format($caja->efectivo_inicial + $caja->efectivo - $caja->salida_efectivo + $caja->pago_efectivo,2) ?></td>
        </tr>
        <tr>
          <td>Efectivo real</td>
          <td>$ <?php echo number_format($caja->efectivo_real,2) ?></td>
        </tr>
        <tr>
          <td>Diferencia</td>
          <td><?php echo number_format($caja->efectivo_real - ($caja->efectivo_inicial + $caja->efectivo - $caja->salida_efectivo + $caja->pago_efectivo),2) ?></td>
        </tr>
        <?php if ($caja->retiro > 0) { ?>
          <tr>
            <td>Retiro de efectivo</td>
            <td>$ <?php echo number_format($caja->retiro,2) ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <?php
    $monto_tarjetas = 0; $recargo = 0; $total = 0; $cantidad_tarjetas = 0;
    if (sizeof($caja->agrupado_tarjetas)>0) { ?>
      <div class="subtitulo">Movimientos en tarjetas</div>
      <table class="tabla">
        <thead>
          <tr>
            <th>Tarjeta</th>
            <th style="width: 120px">Cupones</th>
            <th style="width: 120px">Monto</th>
            <th style="width: 120px">Recargo</th>
            <th style="width: 120px">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          foreach($caja->agrupado_tarjetas as $tar) { 
            $cantidad_tarjetas += $tar->cantidad;
            $monto_tarjetas += (float)$tar->importe;
            $recargo += (float)$tar->interes;
            $total += (float)$tar->total; ?>
            <tr>
              <td><?php echo $tar->tarjeta ?></td>
              <td><?php echo number_format($tar->cantidad,0) ?></td>
              <td>$ <?php echo number_format($tar->importe,2) ?></td>
              <td>$ <?php echo number_format($tar->interes,2) ?></td>
              <td>$ <?php echo number_format($tar->total,2) ?></td>
            </tr>
          <?php } ?>
          <tr>
            <td class="bold">Subtotal de Tarjetas</td>
            <td class="bold"><?php echo number_format($cantidad_tarjetas,0) ?></td>
            <td class="bold">$ <?php echo number_format($monto_tarjetas,2) ?></td>
            <td class="bold">$ <?php echo number_format($recargo,2) ?></td>
            <td class="bold">$ <?php echo number_format($total,2) ?></td>
          </tr>
        </tbody>
      </table>
    <?php } ?>

    <?php
    $total_cheques = $caja->cheques + $caja->pago_cheques;
    if ($total_cheques > 0) { ?>
      <div class="subtitulo">Movimientos en Cheques</div>
      <table class="tabla">
        <tbody>
          <tr>
            <td>Venta en Cheques</td>
            <td class="w120">$ <?php echo number_format($caja->cheques,2) ?></td>
          </tr>
          <tr>
            <td>Pagos de Cuenta Corriente en Cheques</td>
            <td class="w120">$ <?php echo number_format($caja->pago_cheques,2) ?></td>
          </tr>
          <tr>
            <td class="bold">Subtotal de Cheques</td>
            <td class="bold w120">$ <?php echo number_format($total_cheques,2) ?></td>
          </tr>
        </tbody>
      </table>
    <?php } ?>

    <?php
    // CALCULAMOS EL TOTAL GENERAL DE LA CAJA
    $total_general = $caja->efectivo + $caja->pago_efectivo + $monto_tarjetas + $total_cheques;
    ?>

    <?php if (sizeof($caja->departamentos)>0) { 
      $depto1 = $caja->departamentos[0];
      if (!($depto1->departamento == "No Definido" && sizeof($caja->departamentos) == 1)) { ?>
        <div class="subtitulo">Ventas por departamento</div>
        <table class="tabla">
          <thead>
            <tr>
              <th>Nombre</th>
              <th style="width: 120px">Cantidad</th>
              <th style="width: 120px">Monto</th>
              <th style="width: 120px">%</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $cantidad_departamento = 0;
            $monto_departamento = 0;
            foreach($caja->departamentos as $tar) { 
              $cantidad_departamento += $tar->cantidad;
              $monto_departamento += (float)$tar->total; ?>
              <tr>
                <td><?php echo $tar->departamento ?></td>
                <td><?php echo number_format($tar->cantidad,0) ?></td>
                <td>$ <?php echo number_format($tar->total,2) ?></td>
                <td><?php 
                  $porc = ($total_general > 0) ? (($tar->total / $total_general) * 100) : 0;
                  echo number_format($porc,2) ?>%
                </td>
              </tr>
            <?php } ?>
            <tr>
              <td class="bold">Totales</td>
              <td class="bold"><?php echo number_format($cantidad_departamento,0) ?></td>
              <td class="bold">$ <?php echo number_format($monto_departamento,2) ?></td>
              <td class="bold">100.00%</td>
            </tr>
          </tbody>
        </table>
      <?php } ?>
    <?php } ?>

    <div class="subtitulo">Resumen Final</div>
    <table class="tabla">
      <thead>
        <tr>
          <th>Concepto</th>
          <th style="width: 120px">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bold">Efectivo</td>
          <td>$ <?php echo number_format($caja->efectivo + $caja->pago_efectivo,2) ?></td>
        </tr>
        <tr>
          <td class="bold">Tarjetas</td>
          <td>$ <?php echo number_format($monto_tarjetas,2) ?></td>
        </tr>
        <?php if ($total_cheques > 0) { ?>
          <tr>
            <td class="bold">Cheques</td>
            <td>$ <?php echo number_format($total_cheques,2) ?></td>
          </tr>
        <?php } ?>
        <tr>
          <td class="bold fs14">TOTAL DE VENTAS</td>
          <td class="bold fs14">$ <?php echo number_format($total_general,2) ?></td>
        </tr>
      </tbody>
    </table>

    <?php if (!empty($caja->observaciones)) { ?>
      <div class="mt20">
        <strong>Observaciones: </strong><br/>
        <?php echo $caja->observaciones ?>
      </div>
    <?php } ?>    
  </div>
</body>
</html>