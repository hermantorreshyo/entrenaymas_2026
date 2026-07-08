<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
body {
  background-color: white;
}
.subtitulo { font-size: 15px; }
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
</style>
</head>
<body>
<?php echo $header; ?>
  <div class="p30">
    <div class="header oh mb15">
      <div class="subtitulo fl">
        LISTADO DE MORA
      </div>
      <div class="fr">
        <span>Fecha: <span class="bold"><?php echo date("d/m/Y H:i") ?></span></span>
      </div>
    </div>
    <?php /*
    <div class="oh mb15">
      <div class="fr">
        Sucursal: <b><?php echo $pedido->almacen ?></b>
      </div>
      <div class="fl">
        Proveedor: <b><?php echo $pedido->proveedor; ?></b><br/>
        <?php if (!empty($pedido->numero_remito)) { ?>
          Nro de Remito: <?php echo $pedido->numero_remito; ?>
        <?php } ?>
      </div>
    </div>
    */ ?>
    <table class="tabla">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Localidad</th>
          <th>Telefono</th>
          <th>Plan</th>
          <th>Numero</th>
          <th>Valor</th>
          <th>Cuotas</th>
          <th>Ult. Pago</th>
          <th>Mora</th>
          <th>Deuda</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($listado as $item) { ?>
          <tr>
            <td><?php echo $item->apellido ?> <?php echo $item->nombre ?></td>
            <td><span><?php echo $item->localidad ?></span></td>
            <td><span><?php echo $item->telefono ?></span></td>
            <td><span><?php echo $item->plan ?></span></td>
            <td><span><?php echo $item->numero ?></span></td>
            <td><span><?php echo $item->valor_cuota ?></span></td>
            <td><span><?php echo $item->cantidad_cuotas_pagas ?>/<?php echo $item->cantidad_cuotas ?></span></td>
            <td><span><?php echo $item->fecha_ultimo_pago ?> ($ <?php echo number_format($item->ultimo_pago,2) ?>)</span></td>
            <td><span><?php echo $item->dias_mora ?></span></td>
            <td><span><?php echo $item->deuda_vencida ?></span></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php /*
    <div class="oh" style="text-align: left; margin-top: 20px; ">
      <?php if ($con_precio == 1) { ?>
        <div class="fr">
          Total: <b>$ <?php echo number_format($pedido->total,2) ?></b>
        </div>
      <?php } ?>
      <div class="fl">
        Cantidad de productos: <?php echo $total_cantidad; ?>
      </div>
    </div>
    <?php if (!empty($pedido->observaciones)) { ?>
      <div class="mt20">
        <strong>Observaciones: </strong><br/>
        <?php echo $pedido->observaciones ?>
      </div>
    <?php } ?>    
    */ ?>
  </div>
</body>
</html>