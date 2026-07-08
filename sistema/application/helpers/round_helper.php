<?php
// http://stackoverflow.com/questions/14903379/rounding-to-nearest-fraction-half-quarter-etc
function floorToFraction($number, $denominator = 4) {
    $x = $number * $denominator;
    $x = floor($x);
    $x = $x / $denominator;
    return $x;
}
function ceilToFraction($number, $denominator = 4) {
    $x = $number * $denominator;
    $x = ceil($x);
    $x = $x / $denominator;
    return $x;
}

?>