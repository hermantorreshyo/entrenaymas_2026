<?php
function f($n) {
  return number_format($n,2,",",".");
}

function encabezado($fecha_desde, $fecha_hasta, $empresa) {
  $s = "";
  $s.= "<div style='overflow:hidden; margin-bottom:20px'>";
  $s.="<div style='float:right; font-size: 12px; text-align: right; margin-right:10px'>";
  $s.="Desde el: $fecha_desde<br/>";
  $s.="Hasta el: $fecha_hasta";
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
      <?php echo encabezado($fecha_desde, $fecha_hasta, $empresa); ?>
    
      <div style="border-top: solid 2px black; border-bottom: solid 2px black; font-weight: bold; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
        RESUMEN DE VENTAS POR CONCEPTO
      </div>
        
      <table class="totales" style="border-top: solid 2px black; border-bottom: solid 2px black; font-size: 14px; margin: 20px 0px; font-style: italic; padding: 10px;">
        <?php 
        $conceptos = array();
        $neto_g = 0; $iva_g = 0; $total_g = 0;
        foreach($datos as $key => $value) { ?>
          <tr style="border-bottom: solid 1px black; font-weight: bold">
            <td style="width: 30%">Punto Venta <?php echo $key ?></td>
            <td style="width: 20%">Neto</td>
            <td style="width: 20%">IVA</td>
            <td style="width: 20%">Total</td>
          </tr>
          <?php 
          $neto = 0; $iva = 0; $total = 0; $percep = 0;
          foreach($value as $v) { 
            $neto += $v->neto;
            $iva += $v->iva;
            $total += $v->total; 
            $v->nombre = is_null($v->nombre)?"No especificado":$v->nombre;
            if (!isset($conceptos[$v->nombre])) $conceptos[$v->nombre] = array("neto"=>0,"iva"=>0,"total"=>0);
            $conceptos[$v->nombre]["neto"] = $conceptos[$v->nombre]["neto"] + $v->neto;
            $conceptos[$v->nombre]["iva"] = $conceptos[$v->nombre]["iva"] + $v->iva;
            $conceptos[$v->nombre]["total"] = $conceptos[$v->nombre]["total"] + $v->total;
            ?>
            <tr>
              <td style="width: 30%"><?php echo $v->nombre ?></td>
              <td style="width: 20%"><?php echo f($v->neto);?></td>
              <td style="width: 20%"><?php echo f($v->iva);?></td>
              <td style="width: 20%"><?php echo f($v->total);?></td>
            </tr>
          <?php } ?>
          <tr style="border-top: solid 1px black; font-weight: bold">
            <td style="width: 30%">Subtotal</td>
            <td style="width: 20%"><?php echo f($neto);?></td>
            <td style="width: 20%"><?php echo f($iva);?></td>
            <td style="width: 20%"><?php echo f($total);?></td>
          </tr>
          <tr style="border-bottom: solid 1px black">
            <td style="width: 30%"></td>
            <td style="width: 20%"></td>
            <td style="width: 20%"></td>
            <td style="width: 20%"></td>
          </tr>
        <?php 
          $neto_g += $neto; 
          $iva_g += $iva; 
          $total_g += $total;
        } ?>
      </table>
        
      <table class="totales" style="border-bottom: solid 2px black; border-top: solid 2px black; font-size: 18px; margin-top: 30px; font-style: italic; padding: 10px;">
        <tbody>
          <tr style="border-bottom: solid 1px black; font-weight: bold">
            <td style="width: 30%">Concepto agrupado</td>
            <td style="width: 20%">Neto</td>
            <td style="width: 20%">IVA</td>
            <td style="width: 20%">Total</td>
          </tr>
          <?php foreach($conceptos as $key => $value) { ?>
            <tr>
              <td style="width: 30%"><?php echo is_null($key)?"No especificado":$key ?></td>
              <td style="width: 20%"><?php echo f($value["neto"]);?></td>
              <td style="width: 20%"><?php echo f($value["iva"]);?></td>
              <td style="width: 20%"><?php echo f($value["total"]);?></td>
            </tr>
          <?php } ?>
          <tr style="font-weight: bold; border-top: solid 2px black;">
            <td style="width: 30%; font-weight: bold; font-size: 14px;">TOTALES GENERALES</td>
            <td style="width: 20%"><?php echo f($neto_g)?></td>
            <td style="width: 20%"><?php echo f($iva_g)?></td>
            <td style="width: 20%"><?php echo f($total_g)?></td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>
</body>
</html>