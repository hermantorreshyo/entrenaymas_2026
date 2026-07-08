<?php 
date_default_timezone_set("America/Argentina/Buenos_Aires");
set_time_limit(0);
include("../../params.php");

$total_insert = 0;
$id_empresa = 229;
$sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa ";
$q = mysqli_query($conx,$sql);
while(($suc=mysqli_fetch_object($q))!==NULL) {

  // Seleccionamos los articulos que no tienen precio_sucursal para esa sucursal
  $sql = "SELECT A.* FROM articulos A ";
  $sql.= "WHERE A.id_empresa = $id_empresa ";
  $sql.= "AND NOT EXISTS (SELECT * FROM articulos_precios_sucursales PS WHERE A.id = PS.id_articulo AND A.id_empresa = PS.id_empresa AND PS.id_sucursal = $suc->id) ";
  $qq = mysqli_query($conx,$sql);
  while(($r=mysqli_fetch_object($qq))!==NULL) {
    $sql = "INSERT INTO articulos_precios_sucursales (";
    $sql.= " id_sucursal, id_articulo, id_empresa, fecha_mov, id_tipo_alicuota_iva, moneda, porc_iva, costo_iva, costo_neto, costo_final, porc_ganancia, ganancia, precio_neto, precio_final, porc_bonif, precio_final_dto, last_update, costo_neto_inicial, dto_prov ";
    $sql.= ") VALUES (";
    $sql.= " '$suc->id', '$r->id', $id_empresa, '$r->fecha_mov', '$r->id_tipo_alicuota_iva', '$r->moneda', '$r->porc_iva', '$r->costo_iva', '$r->costo_neto', '$r->costo_final', '$r->porc_ganancia', '$r->ganancia', '$r->precio_neto', '$r->precio_final', '$r->porc_bonif', '$r->precio_final_dto', '$r->last_update', '$r->costo_neto_inicial', '$r->dto_prov' ";
    $sql.= ") ";
    mysqli_query($conx,$sql);
    $total_insert++;
  }
}
echo "TERMINO $total_insert";
?>