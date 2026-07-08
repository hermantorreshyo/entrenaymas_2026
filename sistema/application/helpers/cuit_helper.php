<?php
function validarCUIT($cuit) {
  try {
    if (strlen($cuit)<=10) return false;
    $cuit = str_replace("-", "", $cuit);
    $cadena = str_split($cuit);
    $result = $cadena[0]*5;
    $result += $cadena[1]*4;
    $result += $cadena[2]*3;
    $result += $cadena[3]*2;
    $result += $cadena[4]*7;
    $result += $cadena[5]*6;
    $result += $cadena[6]*5;
    $result += $cadena[7]*4;
    $result += $cadena[8]*3;
    $result += $cadena[9]*2;

    $div = intval($result/11);
    $resto = $result - ($div*11);

    if($resto==0){
      if($resto==$cadena[10]){
        return true;
      }else{
        return false;
      }
    } elseif($resto==1){
      if($cadena[10]==9 AND $cadena[0]==2 AND $cadena[1]==3){
        return true;
      } elseif($cadena[10]==4 AND $cadena[0]==2 AND $cadena[1]==3){
        return true;
      }
    } elseif($cadena[10]==(11-$resto)){
      return true;
    } else{
      return false;
    }
  } catch(Exception $e) {
    return false;
  }
}
?>