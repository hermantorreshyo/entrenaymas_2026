<?php if (isset($producto)) { ?>
<script type="application/ld+json">
<?php 
$json = array(
  "@context"=>"http://schema.org",
  "@type"=>"Product",
  "name"=>$producto->nombre,
  "url"=>mklink($producto->link),
);
if (isset($producto->images)) {
  $json["image"] = array();
  foreach($producto->images as $f) { 
    $json["image"][] = current_url(TRUE,TRUE)."/".$f->path;
  }
  if (isset($producto->variantes_images)) {
    foreach($producto->variantes_images as $f) { 
      $json["image"][] = current_url(TRUE,TRUE)."/".$f->path; 
    }
  }
}
$json["description"] = $producto->plain_text;
$json["productID"] = "sku:".$producto->codigo;
$json["sku"] = $producto->codigo;
$json["brand"] = array(
  "@type"=>"Thing",
  "name"=>$producto->marca,
);

$json["aggregateRating"] = array(
  "@type"=>"AggregateRating",
  "ratingValue"=>"5",
  "ratingCount"=>"5",
  "reviewCount"=>"5",
);

if (isset($producto->precio_final_dto)) {
  $json["offers"] = array(
    "@type"=>"Offer",
    "price"=>$producto->precio_final_dto,
    "priceCurrency"=>"ARS", // TODO: Acomodar esto dependiendo de la moneda
    "itemCondition"=>"http://schema.org/NewCondition", // TODO: hacer esto dinamico
    "url"=>mklink($producto->link),
    "seller"=>array(
      "@type"=>"Organization",
      "name"=>$empresa->nombre
    )
  );
  if (isset($producto->usa_stock) && $producto->usa_stock == 1 && 
      isset($producto->stock) && $producto->precio_final_dto > 0 && $producto->stock > 0) {
    $json["offers"]["availability"] = "http://schema.org/InStock";
  }
}
echo json_encode($json,JSON_UNESCAPED_SLASHES); ?>
</script>
<?php } ?>