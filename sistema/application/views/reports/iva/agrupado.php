<?php
function f($n) {
  return number_format($n,2,",",".");
}

function encabezado($empresa) {
  $s = "";
  $s.= "<div style='overflow:hidden; margin-bottom:20px'>";
  $s.="<div style='float:right; font-size: 12px; text-align: right; margin-right:10px'>";
  $s.="</div>";
  $s.="<div style='float:left; font-size: 12px'>";
  $s.= $empresa->razon_social."<br/>";
  $s.= "CUIT: $empresa->cuit<br/>";
  $s.= "Domicilio: $empresa->direccion $empresa->localidad<br/>";
  $s.="</div>";
  $s.="</div>";
  return $s;
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
.inner { padding: 15px; }
.tar { text-align: right !important; }
.tal { text-align: left !important; }
table {
    border-collapse: collapse;
    width: 100%;
}
table tr th { text-align: center; font-size: 8px; }
table td {
    padding: 2px 6px;
    font-size: 10px;
}
.totales td {
    padding: 3px 8px;
    font-size: 12px;    
}

.total_gral {
    margin-top: 30px;
    border-top: solid 2px black;
    border-bottom: solid 2px black;
    padding: 10px;
}
.total_gral td {
    padding: 10px 8px;
    font-size: 14px;
    font-weight:bold;
    font-style: italic;
    text-align: right;
}


tfoot td {
    background-color: #e5e5e5;
    font-weight:bold;
    padding: 7px 4px;
}
</style>
</head>
<body>
<?php echo $header; ?>
  <div class="a4p">
    <div class="inner">
      <?php echo encabezado($empresa); ?>
    
      <div style="border-top: solid 2px black; border-bottom: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
        RESUMEN POR COMPROBANTES
      </div>
        
      <table class="totales" style="border-top: solid 2px black; border-bottom: solid 2px black; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
        <thead>
          <tr style="border-bottom: solid 1px black; font-weight: bold">
            <td></td>
            <?php foreach($datos as $row) { ?>
              <td class="tar"><?php echo $row->nombre ?></td>
            <?php } ?>
            <td class="tar">Total</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Neto</td>
            <?php 
            $neto = 0;
            foreach($datos as $row) { ?>
              <td class="tar"><?php echo number_format($row->neto,2) ?></td>
              <?php $neto = $neto + $row->neto;
            } ?>
            <td class="tar"><?php echo number_format($neto,2) ?></td>
          </tr>
          <tr>
            <td>IVA</td>
            <?php 
            $iva = 0;
            foreach($datos as $row) { ?>
              <td class="tar"><?php echo number_format($row->iva,2) ?></td>
              <?php $iva = $iva + $row->iva;
            } ?>
            <td class="tar"><?php echo number_format($iva,2) ?></td>
          </tr>
          <tr>
            <td>Total</td>
            <?php 
            $total = 0;
            foreach($datos as $row) { ?>
              <td class="tar"><?php echo number_format($row->total,2) ?></td>
              <?php $total = $total + $row->total;
            } ?>
            <td class="tar"><?php echo number_format($total,2) ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>