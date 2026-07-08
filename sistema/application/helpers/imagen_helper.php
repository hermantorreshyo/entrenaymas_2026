<?php
function is_image($filename) {
  $a = explode(".", $filename);
  $u = end($a);
  $u = strtoupper(strtolower($u));
  return ($u == "GIF" || $u == "PNG" || $u == "JPG" || $u == "JPEG");
}

function resize($config = array()) {
  $updir = isset($config["dir"]) ? $config["dir"] : "uploads";
  $img = isset($config["filename"]) ? $config["filename"] : "";
  $max_width = isset($config["max_width"]) ? $config["max_width"] : 1920;
  $max_height = isset($config["max_height"]) ? $config["max_height"] : 1080;
  $preffix = isset($config["preffix"]) ? $config["preffix"] : "";

  // Es en 0 porque ahora se redirecciona, nunca se va a cortar la imagen
  $dest_x = 0;
  $dest_y = 0;
  $arr_image_details = getimagesize("$updir"."$img");
  $img_width = $arr_image_details[0];
  $img_height = $arr_image_details[1];

  if ($img_width < intval($max_width) && $img_height < intval($max_height)) {
    $new_width = $img_width;
    $new_height = $img_height;

  } else {
    // La imagen es mas ancha que los limites
    if ($img_width > intval($max_width)) {
      // Ponemos como ancho el maximo
      $new_width = $max_width;
      // Y calculamos el alto
      $new_height = intval(($img_height * $max_width) / $img_width);
      
      // Si superamos el alto el limite
      if ($new_height > $max_height) {
        // Tomamos el alto el maximo
        $new_height = $max_height;
        // Y calculamos el nuevo ancho
        $new_width = intval(($img_width * $max_height) / $img_height);
      }
    } else {
      // La imagen es vertical, controlamos que el alto sea mayor
      if ($img_height > $max_height) {
        // Tomamos como alto el maximo
        $new_height = $max_height;
        // Y calculamos el nuevo ancho
        $new_width = intval(($img_width * $max_height) / $img_height);
      }
    }
  }
  if ($arr_image_details[2] == 1) {
    $imgt = "imagegif";
    $imgcreatefrom = "imagecreatefromgif";
  }
  if ($arr_image_details[2] == 2) {
    $imgt = "imagejpeg";
    $imgcreatefrom = "imagecreatefromjpeg";
  }
  if ($arr_image_details[2] == 3) {
    $imgt = "imagepng";
    $imgcreatefrom = "imagecreatefrompng";
  }
  if ($imgt) {
    $old_image = $imgcreatefrom("$updir"."$img");
    $new_image = imagecreatetruecolor($new_width, $new_height);
    $blanco = imagecolorallocate($new_image, 255, 255, 255);
    imagefill($new_image, 0, 0, $blanco);
    imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $img_width, $img_height);
    $imgt($new_image, "$updir"."$preffix"."$img");
  }  
}
?>