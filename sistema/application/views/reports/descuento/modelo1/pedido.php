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
  padding: 3px 5px;
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
        PRODUCTOS EN OFERTA
      </div>
      <div class="fr">
        <span>Fecha: <span class="bold"><?php echo date("d/m/Y"); ?></span></span>
      </div>
    </div>
    <table class="tabla">
      <thead>
        <tr>
          <th>Sucursal</th>
          <th>Codigo</th>
          <th>EAN</th>
          <th>Prov.</th>
          <th>Descripcion</th>
          <th>Costo</th>
          <th>Precio Ant.</th>
          <th>%</th>
          <th>Desde</th>
          <th>Hasta</th>
          <th>Nuevo</th>
          <th>%</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $i) { 
          $i["costo_final"] = (float)$i["costo_final"];
          $i["precio_anterior"] = (float)$i["precio_anterior"];
          $i["precio_final"] = (float)$i["precio_final"];
          $total_cantidad++; 
          $margen_ant = ($i["costo_final"] > 0) ? ((($i["precio_anterior"] - $i["costo_final"]) / $i["costo_final"]) * 100) : 0;
          $margen_act = ($i["costo_final"] > 0) ? ((($i["precio_final"] - $i["costo_final"]) / $i["costo_final"]) * 100) : 0;
          ?>
          <tr>
            <td><?php echo $i["almacen"] ?></td>
            <td><?php echo $i["codigo"] ?></td>
            <td><?php echo str_replace("###", "<br/>", $i["codigo_barra"]) ?></td>
            <td><?php echo $i["custom_10"] ?></td>
            <td><?php echo $i["nombre"] ?></td>
            <td><?php echo number_format($i["costo_final"],2) ?></td>
            <td><?php echo number_format($i["precio_anterior"],2) ?></td>
            <td><?php echo number_format($margen_ant,2) ?></td>
            <td><?php echo $i["desde"] ?></td>
            <td><?php echo $i["hasta"] ?></td>
            <td class="tar"><?php echo number_format($i["precio_final"],2) ?></td>
            <td><?php echo number_format($margen_act,2) ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <div class="oh" style="text-align: left; margin-top: 20px; ">
      <div class="fl">
        Cantidad de productos: <?php echo $total_cantidad; ?>
      </div>
    </div>
  </div>
</body>
</html>