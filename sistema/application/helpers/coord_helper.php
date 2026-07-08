<?php
function distance($lat1, $lon1, $lat2, $lon2, $unit = "K") {
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);
  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
    return ($miles * 0.8684);
  } else {
    return $miles;
  }
}

// CALCULA LA DISTANCIA PERO NO LINEAL, SINO "YENDO POR LOS CATETOS"
function distancia_no_lineal($lat1, $lon1, $lat2, $lon2, $unit = "K") {
  $distancia1 = distance($lat1,$lon1,$lat2,$lon1,$unit);
  $distancia2 = distance($lat2,$lon1,$lat2,$lon2,$unit);
  return $distancia1 + $distancia2;
}
?>