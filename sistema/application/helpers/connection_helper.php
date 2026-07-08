<?php
// Indica si tenemos conexion al servidor
function is_connected($url,$port=80) {
  $connected = @fsockopen($url, $port); 
  if ($connected) {
    $is_conn = true;
    fclose($connected);
  } else $is_conn = false;
  return $is_conn;
}
?>