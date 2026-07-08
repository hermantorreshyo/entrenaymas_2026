<?php
function ean13_checksum ($message) {
  $checksum = 0;
  foreach (str_split(strrev($message)) as $pos => $val) {
    $checksum += $val * (3 - 2 * ($pos % 2));
  }
  return ((10 - ($checksum % 10)) % 10);
}
?>