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
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/bootstrap-cols.css" />
<style type="text/css">
body {
  background-color: white;
}
.subtitulo { font-size: 15px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; border-bottom: solid 1px #222; }
.tabla tr td {
  padding-bottom: 3px;
  padding-top: 3px;
}
.bg-default { background-color: #e5e5e5; padding-top: 10px; font-size: 14px; }
.tabla tr th {
  padding-bottom: 3px;
  padding-top: 3px;
  font-weight: bold;
}
.hoja { max-width: 960px; margin: 0 auto; }
@media print {
  body {-webkit-print-color-adjust: exact; margin:0px; }
  .hoja { max-width:  }
}
@page {
  size: auto;
  margin: 30px 0px 30px 0px;
}
</style>
</head>
<body>
  <div class="p30 hoja">
    <div class="header oh mb15">
      <div class="subtitulo fl">
        REPORTE DE SUCURSALES
      </div>
      <div class="fr">
        <span>Generado el <span class="bold"><?php echo date("d/m/Y"); ?></span></span>
      </div>
    </div>
    <div class="oh mb15">
      <div class="fl">
        SUCURSAL: <b><?php echo $sucursal->nombre; ?></b><br/>
        Desde: <b><?php echo $desde; ?></b> - Hasta: <b><?php echo $hasta; ?></b><br/>
      </div>
    </div>

    <div class="subtitulo">VENTAS</div>
    <table class="tabla mb30">
      <thead>
        <tr>
          <th>Fecha</th>
          <th class="tar">Ops.</th>
          <th class="tar">Efectivo</th>
          <th class="tar">Tarjetas</th>
          <th class="tar">Recargo</th>
          <th class="tar">Venta Total</th>
          <th class="tar">Costo Mercaderia</th>
          <th class="tar">Ganancia Bruta</th>
          <th class="tar">% Margen</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $t_cantidad = 0;
        $t_efectivo = 0;
        $t_tarjetas = 0;
        $t_intereses = 0;
        $t_ventas = 0;
        $t_costo = 0;
        $t_ganancia = 0;
        foreach($ventas as $r) { 
          $t_cantidad += $r->cantidad;
          $t_efectivo += $r->efectivo;
          $t_tarjetas += $r->tarjetas - $r->intereses;
          $t_intereses += $r->intereses;
          $t_ventas += $r->total;
          $t_costo += $r->costo;
          $t_ganancia += $r->total - $r->costo; ?>
          <tr>
            <td><?php echo $r->fecha ?></td>
            <td class="tar"><?php echo $r->cantidad ?></td>
            <td class="tar">$ <?php echo number_format($r->efectivo,2) ?></td>
            <td class="tar">$ <?php echo number_format($r->tarjetas,2) ?></td>
            <td class="tar">$ <?php echo number_format($r->intereses,2) ?></td>
            <td class="negro tar">$ <?php echo number_format($r->total,2) ?></td>
            <td class="tar">$ <?php echo number_format($r->costo,2) ?></td>
            <td class="tar">$ <?php echo number_format($r->total - $r->costo,2) ?></td>
            <?php $margen = ($r->costo > 0) ? ((($r->total - $r->costo) / $r->costo) * 100) : 0 ?>
            <td class="tar"><?php echo number_format($margen,2) ?> %</td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot class="bg-important">
        <tr>
          <td></td>
          <td id="estadisticas_sucursales_tickets" class="bold tar"><?php echo $t_cantidad ?></td>
          <td id="estadisticas_sucursales_efectivo" class="bold tar">$ <?php echo number_format($t_efectivo,2) ?></td>
          <td id="estadisticas_sucursales_tarjetas" class="bold tar">$ <?php echo number_format($t_tarjetas,2) ?></td>
          <td id="estadisticas_sucursales_intereses" class="bold tar">$ <?php echo number_format($t_intereses,2) ?></td>
          <td id="estadisticas_sucursales_venta" class="bold tar">$ <?php echo number_format($t_ventas,2) ?></td>
          <td id="estadisticas_sucursales_cmv" class="bold tar">$ <?php echo number_format($t_costo,2) ?></td>
          <td id="estadisticas_sucursales_ganancia" class="bold tar">$ <?php echo number_format($t_ganancia,2) ?></td>
          <td id="estadisticas_sucursales_margen" class="bold tar"><?php echo number_format($margen,2) ?> %</td>
        </tr>
      </tfoot>
    </table>

    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($t_efectivo,2) ?></span>
              <span class="bold text-md pt5 db">Efectivo <span id="estadisticas_sucursales_ventas_efectivo_porc"></span></span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_tarjetas" class="font-thin h3 block">$ <?php echo number_format($t_tarjetas,2) ?></span>
              <span class="bold text-md pt5 db">Tarjetas <span id="estadisticas_sucursales_ventas_tarjetas_porc"></span></span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_cmv" class="font-thin h3 block">$ <?php echo number_format($t_costo,2) ?></span>
              <span class="bold text-md pt5 db">Costo Mercaderia</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v item mb0 bg-default" style="height: 60px">
              <div id="estadisticas_sucursales_ventas_total" class="h3 font-thin block">$ <?php echo number_format($t_ventas,2) ?></div>
              <span class="bold text-md pt5 db">Venta Total</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_anterior" class="font-thin h3 block">$ <?php echo number_format($venta_anterior,2) ?></span>
              <span class="bold text-md pt5 db">Año Ant. <span id="estadisticas_sucursales_ventas_anterior_variacion"></span></span>
            </div>            
          </td>
          <td>
            <?php $ticket_promedio = (($t_cantidad > 0) ? ($t_ventas / $t_cantidad) : 0); ?>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_ticket_promedio" class="font-thin h3 block"><?php echo number_format($ticket_promedio,2) ?></span>
              <span class="bold text-md pt5 db">Ticket Promedio</span>
            </div>            
          </td>
        </tr>
      </table>
    </div>      

    <div class="subtitulo">INGRESOS DE MERCADERIA</div>
    <table class="tabla">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Numero</th>
          <th>Proveedor</th>
          <th>Total</th>
          <th>Observaciones</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $t_ingresos = 0;
        foreach($ingresos as $r) { 
          $t_ingresos += $r->total; ?>
          <tr>
            <td><?php echo $r->fecha ?></td>
            <td><?php echo $r->numero_remito ?></td>
            <td><span class="text-info"><?php echo $r->proveedor ?></span></td>
            <td class="negro">$ <?php echo number_format($r->total,2) ?></td>
            <td><?php echo $r->observaciones ?></td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot class="bg-important">
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td id="estadisticas_sucursales_venta" class="bold">$ <?php echo number_format($t_ingresos,2) ?></td>
          <td></td>
        </tr>
      </tfoot>
    </table>

    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($stock_inicial,2) ?></span>
              <span class="bold text-md pt5 db">Stock Inicial</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_tarjetas" class="font-thin h3 block">$ <?php echo number_format($stock_final,2) ?></span>
              <span class="bold text-md pt5 db">Stock Final</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_cmv" class="font-thin h3 block">$ <?php echo number_format($t_ingresos,2) ?></span>
              <span class="bold text-md pt5 db">Total Ingresos</span>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <div class="subtitulo">TOTAL DE GASTOS</div>
    <table class="tabla">
      <thead>
        <tr>
          <th>Concepto</th>
          <?php for ($i = 0; $i< sizeof($cajas_gastos); $i++) { ?>
            <?php $cajas_gastos[$i]->total = 0; ?>
            <?php $o = $cajas_gastos[$i] ?>
            <th><?php echo $o->nombre ?></th>
          <?php } ?>
          <th>%</th>
        </tr>
      </thead>
      <tbody>
        <?php for ($i = 0; $i< sizeof($gastos); $i++) { ?>
          <?php $o = $gastos[$i] ?>
          <tr>
            <?php $total_gasto_por_concepto = 0 ?>
            <td><span class="text-info"><?php echo $o->nombre ?></span></td>
            <?php for ($j = 0; $j< sizeof($cajas_gastos); $j++) { ?>
              <?php $caja_gasto = $cajas_gastos[$j] ?>
              <?php for ($k = 0; $k < sizeof($o->cajas); $k++) { ?>
                <?php $cc = $o->cajas[$k]; ?>
                <?php if ($caja_gasto->id == $cc["id"]) { ?>
                  <?php $caja_gasto->total += $cc["total"]; ?>
                  <td>$ <?php echo number_format($cc["total"],2) ?></td>
                  <?php $total_gasto_por_concepto += $cc["total"] ?>
                <?php } ?>
              <?php } ?>
            <?php } ?>
            <td><?php echo number_format(($t_ventas > 0) ? ($total_gasto_por_concepto / $t_ventas * 100) : 0,2) ?>%</td>
          </tr>
        <?php } ?>
      </tbody> 
    </table>

    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <?php 
          $t_gastos = 0;
          foreach($cajas_gastos as $c) { ?>
            <td>
              <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
                <span class="font-thin h3 block">$ <?php echo number_format($c->total,2) ?></span>
                <span class="bold text-md pt5 db"><?php echo $c->nombre ?></span>
              </div>
            </td>
          <?php $t_gastos += $c->total; } ?>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_cmv" class="font-thin h3 block">$ <?php echo number_format($t_gastos,2) ?></span>
              <span class="bold text-md pt5 db">Total Gastos</span>
            </div>
          </td>
        </tr>
      </table>
    </div>


    <div class="subtitulo">PAGOS</div>
    <table class="tabla">
      <thead>
        <tr>
          <th>Orden</th>
          <th>Proveedor</th>
          <th>Fecha</th>
          <th class="tar">Efectivo</th>
          <th class="tar">Cheques Emitidos</th>
          <th class="tar">Transf.</th>
          <th class="tar">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php $total_ordenes_pago = 0 ?>
        <?php $total_pagos_efectivo = 0 ?>
        <?php $total_cheques_emitidos = 0 ?>
        <?php $total_transferencias = 0 ?>
        <?php for($i=0;$i< sizeof($ordenes_pago);$i++) { ?>
          <?php $o = $ordenes_pago[$i]; ?>
          <tr>
            <td>OP <?php echo $o->numero_2 ?></td>
            <td><?php echo $o->nombre ?></td>
            <td><?php echo $o->fecha ?></td>
            <td class="tar"><?php echo number_format($o->efectivo,2) ?></td>
            <td class="tar"><?php echo number_format($o->total_cheques,2) ?></td>
            <td class="tar"><?php echo number_format($o->total_depositos,2) ?></td>
            <td class="tar"><?php echo number_format($o->total_general,2) ?></td>
            <?php $total_pagos_efectivo += (float)($o->efectivo) ?>
            <?php $total_cheques_emitidos += (float)($o->total_cheques) ?>
            <?php $total_transferencias += (float)($o->total_depositos) ?>
            <?php $total_ordenes_pago += (float)($o->total_general) ?>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($total_pagos_efectivo,2) ?></span>
              <span class="bold text-md pt5 db">Pagos en Efectivo</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($total_transferencias,2) ?></span>
              <span class="bold text-md pt5 db">Transferencias</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($total_pago_cheques,2) ?></span>
              <span class="bold text-md pt5 db">Cheques Cubiertos</span>
            </div>
          </td>
          <?php 
          // ATENCION: Tomamos el total de pagos con los cheques cubiertos, no con los cheques emitidos
          $total_pagos = $total_pagos_efectivo + $total_transferencias + $total_pago_cheques; ?>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_cmv" class="font-thin h3 block">$ <?php echo number_format($total_pagos,2) ?></span>
              <span class="bold text-md pt5 db">Total Pagos</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_cmv" class="font-thin h3 block">$ <?php echo number_format($total_cheques_emitidos,2) ?></span>
              <span class="bold text-md pt5 db">Cheques Emitidos</span>
            </div>
          </td>
        </tr>
      </table>
    </div>


    <div class="subtitulo">DEUDA PROVEEDORES</div>
    <table class="tabla">
      <thead>
        <tr>
          <th class="sorting" data-sort-by="id">Cod.</th>
          <th class="sorting" data-sort-by="nombre">Proveedor</th>
          <th class="tar">+90</th>
          <th class="tar">90</th>
          <th class="tar">60</th>
          <th class="tar">30</th>
          <th class="tar">Saldo</th>
          <th class="tar">Ult.Compra</th>
          <th class="tar">Fecha</th>
          <th class="tar">Ult.Pago</th>
          <th class="tar">Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $t_deuda_vencida = 0;
        $t_adelantos = 0;
        $t_deuda_prov = 0;
        $t_deuda_prov_30 = 0;
        $t_deuda_prov_60 = 0;
        $t_deuda_prov_90 = 0;
        $t_deuda_prov_mas_90 = 0;
        for($i=0;$i<sizeof($deuda_proveedores);$i++) {
          $elem = $deuda_proveedores[$i];
          if ($elem->saldo < 0) {
            $t_adelantos += abs($elem->saldo);
          } else {
            $t_deuda_prov += (float)($elem->saldo);  
          }
          $elem->saldo_mas_90 = (float)($elem->saldo_mas_90);
          $elem->saldo_90 = (float)($elem->saldo_90);
          $elem->saldo_60 = (float)($elem->saldo_60);
          $elem->saldo_30 = (float)($elem->saldo_30);
          $elem->saldo = (float)($elem->saldo);

          // Dependiendo si la deuda esta vencida
          if ($elem->dias_pago == 90) {
            $t_deuda_vencida = ($elem->saldo_mas_90 > 0 ? $elem->saldo_mas_90 : 0);
          } else if ($elem->dias_pago == 60) {
            $t_deuda_vencida = ($elem->saldo_mas_90 > 0 ? $elem->saldo_mas_90 : 0);
            $t_deuda_vencida = ($elem->saldo_90 > 0 ? $elem->saldo_90 : 0);
          } else if ($elem->dias_pago == 30) {
            $t_deuda_vencida = ($elem->saldo_mas_90 > 0 ? $elem->saldo_mas_90 : 0);
            $t_deuda_vencida = ($elem->saldo_90 > 0 ? $elem->saldo_90 : 0);          
            $t_deuda_vencida = ($elem->saldo_60 > 0 ? $elem->saldo_60 : 0);
          } else if ($elem->dias_pago == 0) {
            $t_deuda_vencida += ($elem->saldo > 0) ? $elem->saldo : 0;
          }
          $t_deuda_prov_30 += $elem->saldo_30;
          $t_deuda_prov_60 += $elem->saldo_60;
          $t_deuda_prov_90 += $elem->saldo_90;
          $t_deuda_prov_mas_90 += $elem->saldo_mas_90; ?>

          <tr>
            <td><?php echo $elem->codigo ?></td>
            <td><?php echo $elem->nombre ?></td>
            <td class="tar"><?php echo (($elem->saldo_mas_90) == 0) ? '' : number_format($elem->saldo_mas_90,2) ?></td>
            <td class="tar"><?php echo (($elem->saldo_90) == 0) ? '' : number_format($elem->saldo_90,2) ?></td>
            <td class="tar"><?php echo (($elem->saldo_60) == 0) ? '' : number_format($elem->saldo_60,2) ?></td>
            <td class="tar"><?php echo (($elem->saldo_30) == 0) ? '' : number_format($elem->saldo_30,2) ?></td>
            <td class="tar"><?php echo (($elem->saldo) == 0) ? '' : number_format($elem->saldo,2) ?></td>
            <td class="tar"><?php echo (($elem->monto_ultima_compra) == 0) ? '' : number_format($elem->monto_ultima_compra,2) ?></td>
            <td class="tar"><?php echo $elem->ultima_compra ?></td>
            <td class="tar"><?php echo (($elem->monto_ultimo_pago) == 0) ? '' : number_format($elem->monto_ultimo_pago,2) ?></td>
            <td class="tar"><?php echo $elem->ultimo_pago ?></td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" class="bold tar">Totales</td>
          <td id="estadisticas_sucursales_deuda_proveedores_total_saldo_mas_90" class="bold tar"><?php echo number_format($t_deuda_prov_mas_90,2) ?></td>
          <td id="estadisticas_sucursales_deuda_proveedores_total_saldo_90" class="bold tar"<?php echo number_format($t_deuda_prov_90,2) ?>></td>
          <td id="estadisticas_sucursales_deuda_proveedores_total_saldo_60" class="bold tar"><?php echo number_format($t_deuda_prov_60,2) ?></td>
          <td id="estadisticas_sucursales_deuda_proveedores_total_saldo_30" class="bold tar"><?php echo number_format($t_deuda_prov_30,2) ?></td>
          <td id="estadisticas_sucursales_deuda_proveedores_total_saldo" class="bold tar"><?php echo number_format($t_deuda_prov - $t_adelantos,2) ?></td>
          <td id="estadisticas_sucursales_deuda_proveedores_total_compras" class="bold tar"></td>
          <td></td>
          <td class="fs16 bold tar"></td>
          <td></td>
        </tr>
      </tfoot>
    </table>

    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($total_deuda_cheques,2) ?></span>
              <span class="bold text-md pt5 db">Deuda en Cheques</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($t_deuda_prov,2) ?></span>
              <span class="bold text-md pt5 db">Deuda en Efectivo</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($t_adelantos,2) ?></span>
              <span class="bold text-md pt5 db">Adelantos</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_cmv" class="font-thin h3 block">$ <?php echo number_format($t_deuda_prov + $total_deuda_cheques,2) ?></span>
              <span class="bold text-md pt5 db">Deuda Total</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span id="estadisticas_sucursales_ventas_cmv" class="font-thin h3 block">$ <?php echo number_format($t_deuda_vencida,2) ?></span>
              <span class="bold text-md pt5 db">Deuda Vencida</span>
            </div>
          </td>
        </tr>
      </table>
    </div>    

    <?php if (sizeof($deuda_cheques) > 0) { ?>
      <div class="resumen oh cb pb0 mb30">
        <h3 class="h4 mb10 bold">Deuda en cheques:</h3>
        <table class="w100p">
          <tr>
          <?php for($ii=0;$ii< sizeof($deuda_cheques); $ii++) { ?>
            <?php $mes = $deuda_cheques[$ii] ?>        
            <td>
              <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
                <span class="text-muted text-md pt5 db">$ <?php echo number_format($mes["total"],2) ?></span>
                <span class="text-muted text-md pt5 db"><?php echo $mes["mes"] ?></span>
              </div>
            </td>
          <?php } ?>
          </tr>
        </table>
      </div>
    <?php } ?>    


    <div class="subtitulo">SALDOS</div>
    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($efectivo_inicial,2) ?></span>
              <span class="bold text-md pt5 db">Efectivo Inicial</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($efectivo_final,2) ?></span>
              <span class="bold text-md pt5 db">Efectivo Final</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($banco_inicial,2) ?></span>
              <span class="bold text-md pt5 db">Banco Inicial</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($banco_final,2) ?></span>
              <span class="bold text-md pt5 db">Banco Final</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($cargas_sociales,2) ?></span>
              <span class="bold text-md pt5 db">Cargas Sociales B</span>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <div class="subtitulo mb30">RETIROS DE SOCIOS</div>

    <div class="subtitulo">SOCIO 1</div>
    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($socio_1_efectivo,2) ?></span>
              <span class="bold text-md pt5 db">Efectivo</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($socio_1_banco,2) ?></span>
              <span class="bold text-md pt5 db">Tarjeta</span>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <div class="subtitulo">SOCIO 2</div>
    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($socio_2_efectivo,2) ?></span>
              <span class="bold text-md pt5 db">Efectivo</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($socio_2_banco,2) ?></span>
              <span class="bold text-md pt5 db">Tarjeta</span>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <div class="subtitulo">SOCIO 3</div>
    <div class="resumen oh cb pb0 mb30">
      <table class="w100p">
        <tr>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($socio_3_efectivo,2) ?></span>
              <span class="bold text-md pt5 db">Efectivo</span>
            </div>
          </td>
          <td>
            <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 60px">
              <span class="font-thin h3 block">$ <?php echo number_format($socio_3_banco,2) ?></span>
              <span class="bold text-md pt5 db">Tarjeta</span>
            </div>
          </td>
        </tr>
      </table>
    </div>


  </div>
</body>
</html>