<?php $total_cantidad = 0; ?>
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
        INGRESO DE MERCADERIA
      </div>
      <div class="fr">
        <span>Fecha: <span class="bold"><?php echo $pedido->fecha; ?></span></span>
      </div>
    </div>
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
    <table class="tabla">
      <thead>
        <tr>
          <th>Cod. Interno</th>
          <th>Cod. Barra</th>
          <th>Cod. Prov.</th>
          <th>Descripcion</th>
          <th>Cantidad</th>
          <th>Precio Unit.</th>
          <?php if ($con_precio == 1) { ?>
            <th>Costo Unit.</th>
            <th>Total</th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($pedido->items as $i) { 
          $total_cantidad += $i->cantidad; ?>
          <tr>
            <td><?php echo (!empty($i->codigo))? ($i->codigo):""; ?></td>
            <td>
              <?php 
              $codigo_barra = explode("###",$i->codigo_barra); 
              foreach($codigo_barra as $cod) {
                echo ($cod != $i->codigo) ? $cod."<br/>" : "";
              } ?>
            </td>
            <td><?php echo (!empty($i->codigo_prov))? nl2br($i->codigo_prov):""; ?></td>
            <td><?php echo (!empty($i->nombre))? utf8_encode($i->nombre):""; ?></td>
            <td><?php echo (!empty($i->cantidad))?number_format($i->cantidad,2):"0"; ?></td>
            <td><?php echo (!empty($i->precio_final))?"$ ".number_format($i->precio_final,2):"$ 0.00"; ?></td>
            <?php if ($con_precio == 1) { ?>
              <td><?php echo (!empty($i->costo_final))?"$ ".number_format($i->costo_final,2):"$ 0.00"; ?></td>
              <td><?php echo "$ ".number_format($i->cantidad * $i->costo_final,2); ?></td>
            <?php } ?>
          </tr>
        <?php } ?>
      </tbody>
    </table>
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
  </div>
</body>
</html>