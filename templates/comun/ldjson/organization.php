<?php if (isset($empresa)) { ?>
<script type="application/ld+json">
<?php 
$json = array(
  "@context"=>"http://schema.org",
  "@type"=>"Organization",
  "name"=>$empresa->nombre,
  "url"=>current_url(TRUE,TRUE),
);
if (!empty($empresa->logo_1)) {
  $json["logo"] = current_url(TRUE,TRUE)."/sistema/".$empresa->logo_1;
}
if (!empty($empresa->telefono)) {
  $json["contactPoint"] = array(
    array(
      "@type"=>"ContactPoint",
      "telephone"=>str_replace(")","",str_replace("(","",str_replace(" ","",str_replace("-", "",$empresa->telefono)))),
      "contactType"=>"customer service",
    )
  );
  if (!empty($empresa->telefono_2) && $empresa->telefono_2 != $empresa->telefono) {
    $json["contactPoint"][] = array(
      "@type"=>"ContactPoint",
      "telephone"=>str_replace(")","",str_replace("(","",str_replace(" ","",str_replace("-", "",$empresa->telefono_2)))),
      "contactType"=>"customer service",
    );
  }
}
$sameAs = array();
if (!empty($empresa->facebook)) $sameAs[] = $empresa->facebook;
if (!empty($empresa->twitter)) $sameAs[] = $empresa->twitter;
if (!empty($empresa->linkedin)) $sameAs[] = $empresa->linkedin;
if (!empty($empresa->instagram)) $sameAs[] = $empresa->instagram;
if (!empty($empresa->youtube)) $sameAs[] = $empresa->youtube;
if (!empty($empresa->google_plus)) $sameAs[] = $empresa->google_plus;
if (sizeof($sameAs)>0) {
  $json["sameAs"] = array();
  for($i=0;$i<sizeof($sameAs);$i++) { 
    $same = $sameAs[$i];
    $json["sameAs"][] = $same;
  }
}
echo json_encode($json,JSON_UNESCAPED_SLASHES); ?>
</script>
<?php } ?>