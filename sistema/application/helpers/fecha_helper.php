<?php

// NOTA MUY IMPORTANTE
// Esta es una copia de resources/php/date_helper.php

function full_date_from_mysql($date,$config = array()) {
  if (empty($date)) return FALSE;
  $mostrar_dia_semana = (isset($config["mostrar_dia_semana"]) ? $config["mostrar_dia_semana"] : 1);
  $mostrar_hora = (isset($config["mostrar_hora"]) ? $config["mostrar_hora"] : 1);
  $a = explode(" ",$date);
  $dia = substr($a[0],8,2);
  $mes = substr($a[0],5,2);
  $anio = substr($a[0],0,4);
  $str = "";
  $str.= ($mostrar_dia_semana == 1) ? nombres_dias(date("l",strtotime("$anio/$mes/$dia"))).", " : "";
  $str.= "$dia de ".get_mes($mes)." de $anio";
  $str.= ((sizeof($a)>1 && $mostrar_hora == 1) ? " a las ".$a[1] : "");
  return $str;
}

// Fecha completa a partir de fecha dd/mm/yyyy
function fecha_full($date,$config = array()) {
  if (empty($date)) return FALSE;
  $mostrar_dia_semana = (isset($config["mostrar_dia_semana"]) ? $config["mostrar_dia_semana"] : 1);
  $mostrar_hora = (isset($config["mostrar_hora"]) ? $config["mostrar_hora"] : 1);
  $a = explode(" ",$date);
  $dia = substr($a[0],0,2);
  $mes = substr($a[0],3,2);
  $anio = substr($a[0],6,4);
  $s = "";
  $s.= ($mostrar_dia_semana == 1) ? nombres_dias(date("l",strtotime("$anio/$mes/$dia"))).", " : "";
  $s.= "$dia de ".get_mes($mes)." de $anio ";
  $s.= (sizeof($a)>1 && $mostrar_hora == 1) ? "a las ".$a[1] : "";
  return $s;
}

function fecha_full_en($date) {
  if (empty($date)) return FALSE;
  $a = explode(" ",$date);
  $dia = substr($a[0],0,2);
  $mes = substr($a[0],3,2);
  $anio = substr($a[0],6,4);
  $fecha = strtotime($anio."-".$mes."-".$dia);
  $s = date("F dS",$fecha).", $anio";
  return $s;
}

function fecha_full_tk($date) {
  if (empty($date)) return FALSE;
  $a = explode(" ",$date);
  $dia = substr($a[0],0,2);
  $mes = substr($a[0],3,2);
  $anio = substr($a[0],6,4);
  $fecha = strtotime($anio."-".$mes."-".$dia);
  $s = $dia." ".get_mes_tk($mes)." $anio";
  return $s;
}

function fecha_full_fr($date) {
  if (empty($date)) return FALSE;
  $a = explode(" ",$date);
  $dia = substr($a[0],0,2);
  $mes = substr($a[0],3,2);
  $anio = substr($a[0],6,4);
  $fecha = strtotime($anio."-".$mes."-".$dia);
  $s = get_dia_semana_fr(date("N",strtotime("$anio/$mes/$dia"))).", le ".$dia." ".get_mes_fr($mes)." $anio";
  return $s;
}

function fecha_mysql_full($date) {
    if (empty($date)) return FALSE;
    $a = explode(" ",$date);
  $dia = substr($a[0],8,2);
  $mes = substr($a[0],5,2);
  $anio = substr($a[0],0,4);
    return "$dia/$mes/$anio ".$a[1];
}

function numero_dia($date) {
  if (strpos($date,"/") > 0) {
    // dd/mm/yyyy
    return substr($date,0,2);
  } else {
    // yyyy-mm-dd
    return substr($date,8,2);
  }
}

function numero_mes($date) {
  if (strpos($date,"/") > 0) {
    // dd/mm/yyyy
    return substr($date,3,2);
  } else {
    // yyyy-mm-dd
    return substr($date,8,2);
  }
}

function numero_anio($date) {
  if (strpos($date,"/") > 0) {
    // dd/mm/yyyy
    return substr($date,6,4);
  } else {
    // yyyy-mm-dd
    return substr($date,0,4);
  }
}


/**
 * Convierte una fecha en dd/mm/yyyy a formato de MySQL (yyyy-mm-dd)
 * @param String $date Fecha en formato español que se desea convertir a MySQL
 */
function fecha_mysql($date) {
  if (strpos($date, "-") > 0) {
    $p = explode("-",$date);
    if (strlen($p[0]) == 4) return $date; // Empieza con YYYY- Ya esta en formato ingles
  }
  if (!empty($date)) {
    // Si tiene tiempo
    $tiempo = "";
    if (strpos($date," ")>0) {
      $a = explode(" ",$date);
      $date = $a[0];
      $tiempo = " ".$a[1];
    }
    $dia = substr($date,0,2);
    $mes = substr($date,3,2);
    $anio = substr($date,6,4);
    return $anio.'-'.$mes.'-'.$dia.$tiempo;
  } else return FALSE;
}

/**
 * Convierte una fecha en formato de MySQL (yyyy-mm-dd) a formato dd/mm/yyyy
 * @param String $mysql Fecha en formato mysql que se desea convertir a formato español
 */
function fecha_es($mysql) {
  if (strpos($mysql, "/") > 0) return $mysql; // Ya esta en formato español
  $tiempo = "";
  if (strpos($mysql," ")>0) {
    $a = explode(" ",$mysql);
    $mysql = $a[0];
    $tiempo = " ".$a[1];    
  }
  $dia = substr($mysql,8,2);
  $mes = substr($mysql,5,2);
  $anio = substr($mysql,0,4);
  return $dia.'/'.$mes.'/'.$anio.$tiempo;
}

function fecha_completa($mysql) {
  $dia = (int)substr($mysql,8,2);
  $mes = (int)substr($mysql,5,2);
  $anio = (int)substr($mysql,0,4);  
  $mes_letras = get_mes($mes);
  $dia_letra = nombres_dias($mysql);
  return $dia_letra." ".$dia." de ".$mes_letras." de ".$anio;
}

/**
 * Calcula la cantidad de dias entre dos fechas
 * @param from: Fecha desde en formato MySQL
 * @param to: Fechas hasta en formato MySQL
 */ 
function days_between($from,$to)
{
  
  $diaFrom = substr($from,8,2);
  $mesFrom = substr($from,5,2);
  $anioFrom = substr($from,0,4);
  
  $diaTo = substr($to,8,2);
  $mesTo = substr($to,5,2);
  $anioTo = substr($to,0,4);
  
  //calculo timestam de las dos fechas 
  $timestamp1 = mktime(0,0,0,$mesFrom,$diaFrom,$anioFrom);
  $timestamp2 = mktime(0,0,0,$mesTo,$diaTo,$anioTo); 
  
  //resto a una fecha la otra 
  $segundos_diferencia = $timestamp1 - $timestamp2; 
  //convierto segundos en dias 
  $dias_diferencia = $segundos_diferencia / (60 * 60 * 24); 
  //obtengo el valor absoulto de los días (quito el posible signo negativo) 
  $dias_diferencia = abs($dias_diferencia); 
  //quito los decimales a los días de diferencia 
  $dias_diferencia = floor($dias_diferencia);

  return $dias_diferencia;
}


/**
 * Devuelve el nombre del dia a partir de una fecha en MySQL
 * @param $from Fecha de MySQL
 * @return unknown_type
 */
function nombres_dias($from)
{
  $nombreDia = getdate(strtotime($from));
  $nomb = strtolower($nombreDia["weekday"]);
  $n = "";
  switch ($nomb) {
    case "sunday": $n = "Domingo"; break;
    case "monday": $n = "Lunes"; break;
    case "tuesday": $n = "Martes"; break;
    case "wednesday": $n = "Miercoles"; break;
    case "thursday": $n = "Jueves"; break;
    case "friday": $n = "Viernes"; break;
    case "saturday": $n = "Sabado"; break;
  }
  return $n;
}




/**
 * Obtiene el nombre del dia de la semana a partir de un entero
 */
function get_dia_semana($i)
{
  $n="";
  switch ($i) {
    case 1: $n = "Lunes"; break;
    case 2: $n = "Martes"; break;
    case 3: $n = "Miercoles"; break;
    case 4: $n = "Jueves"; break;
    case 5: $n = "Viernes"; break;
    case 6: $n = "Sabado"; break;
    case 7: $n = "Domingo"; break;
  }
  return $n;
}

function get_dia_semana_fr($i)
{
  $n="";
  switch ($i) {
    case 1: $n = "Lundi"; break;
    case 2: $n = "Mardi"; break;
    case 3: $n = "Mercredi"; break;
    case 4: $n = "Jeudi"; break;
    case 5: $n = "Vendredi"; break;
    case 6: $n = "Samedi"; break;
    case 7: $n = "Dimanche"; break;
  }
  return $n;
}


function get_mes($i) {
  $n="";
  switch ($i) {
    case 1: $n = "Enero"; break;
    case 2: $n = "Febrero"; break;
    case 3: $n = "Marzo"; break;
    case 4: $n = "Abril"; break;
    case 5: $n = "Mayo"; break;
    case 6: $n = "Junio"; break;
    case 7: $n = "Julio"; break;
    case 8: $n = "Agosto"; break;
    case 9: $n = "Septiembre"; break;
    case 10: $n = "Octubre"; break;
    case 11: $n = "Noviembre"; break;
    case 12: $n = "Diciembre"; break;
  }
  return $n;
}

function get_mes_tk($i) {
  $n="";
  switch ($i) {
    case 1: $n = "Ocak"; break;
    case 2: $n = "Şubat"; break;
    case 3: $n = "Mart"; break;
    case 4: $n = "Nisan"; break;
    case 5: $n = "Mayıs"; break;
    case 6: $n = "Haziran"; break;
    case 7: $n = "Temmuz"; break;
    case 8: $n = "Ağustos"; break;
    case 9: $n = "Eylül"; break;
    case 10: $n = "Ekim"; break;
    case 11: $n = "Kasım"; break;
    case 12: $n = "Aralık"; break;
  }
  return $n;
}

function get_mes_fr($i) {
  $n="";
  switch ($i) {
    case 1: $n = "Janvier"; break;
    case 2: $n = "Février"; break;
    case 3: $n = "Mars"; break;
    case 4: $n = "Avril"; break;
    case 5: $n = "Mai"; break;
    case 6: $n = "Juin"; break;
    case 7: $n = "Juillet"; break;
    case 8: $n = "Août"; break;
    case 9: $n = "Septembre"; break;
    case 10: $n = "Octobre"; break;
    case 11: $n = "Novembre"; break;
    case 12: $n = "Décembre"; break;
  }
  return $n;
}


function hoy() {
  return date('d/m/Y');
}

function hoyFull() {
  return date('Y/m/d H:i:s');
}

function maniana() {
  $prox = mktime(0, 0, 0, date("m"),date("d")+1,date("Y"));
  return date('d/m/Y',$prox);
}

function ayer() {
  $prox = mktime(0, 0, 0, date("m"),date("d")-1,date("Y"));
  return date('d/m/Y',$prox);
}

function mes_proximo() {
  $prox = mktime(0, 0, 0, date("m")+1,date("d"),date("Y"));
  return date('d/m/Y',$prox);
}

function mes_anterior() {
  $prox = mktime(0, 0, 0, date("m")-1,date("d"),date("Y"));
  return date('d/m/Y',$prox);
}

?>