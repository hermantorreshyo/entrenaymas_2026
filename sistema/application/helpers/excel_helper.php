<?php
function excel_letter($i) {
  $alphabet = ['A','B','C','D','E','F','G','H','I','J','K',
               'L','M','N','O','P','Q','R','S','T','U',
               'V','W','X','Y','Z'];
  $entera = floor($i / 25);
  $modulo = $i % 25;
  if ($entera == 0) {
    return $alphabet[$modulo];
  } else if ($entera > 0) {
    return $alphabet[$entera].$alphabet[$modulo];
  }
}
?>