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
        LISTADO DE BUENOS CLIENTES
      </div>
      <div class="fr">
        <span>Fecha: <span class="bold"><?php echo date("d/m/Y H:i") ?></span></span>
      </div>
    </div>
    <table class="tabla">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Localidad</th>
          <th>Telefono</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($listado as $item) { ?>
          <tr>
            <td><?php echo $item->apellido ?> <?php echo $item->nombre ?></td>
            <td><span><?php echo $item->localidad ?></span></td>
            <td><span><?php echo $item->telefono ?></span></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>