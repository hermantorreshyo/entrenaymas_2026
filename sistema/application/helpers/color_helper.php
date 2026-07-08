<?php
function addOpacity($c,$opacity) {
  if (strpos($c,"rgba") !== FALSE) return $c; // Ya lo tiene
  $c = str_replace("rgb","rgba",$c);
  return substr($c,0,strrpos($c,")")).",".$opacity.")";
}
function changeBrightness($c,$value) {
  $list = extract_rgb($c);
  if (sizeof($list)!=3) return $c;
  return "rgb(".maxmin($list[0]+$value).",".maxmin($list[1]+$value).",".maxmin($list[2]+$value).")";
}
function addColor($c,$color) {
  $list = extract_rgb($c);
  if (sizeof($list)!=3) return $c;
  if (sizeof($color)!=3) return $c;
  return "rgb(".maxmin($list[0]+$color[0]).",".maxmin($list[1]+$color[1]).",".maxmin($list[2]+$color[2]).")";
}
function maxmin($v) {
  return ($v > 255) ? 255 : (($v < 0) ? 0 : $v);
}
function extract_rgb($c) {
  $c = str_replace("rgba","",$c);
  $c = str_replace("rgbaa","",$c);
  $c = str_replace("rgb","",$c);
  $c = str_replace("(","",$c);
  $c = str_replace(")","",$c);
  return explode(",",$c);
}
?>