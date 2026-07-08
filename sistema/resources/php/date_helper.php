<?php
/**
 * Convierte una fecha en dd/mm/yyyy a formato de MySQL (yyyy-mm-dd)
 * @param String $date Fecha en formato español que se desea convertir a MySQL
 */
function fecha_mysql($date) {
	$dia = substr($date,0,2);
	$mes = substr($date,3,2);
	$anio = substr($date,6,4);
	return $anio.'-'.$mes.'-'.$dia;
}

/**
 * Convierte una fecha en formato de MySQL (yyyy-mm-dd) a formato dd/mm/yyyy
 * @param String $mysql Fecha en formato mysql que se desea convertir a formato español
 */
function fecha_es($mysql) {
	$dia = substr($mysql,8,2);
	$mes = substr($mysql,5,2);
	$anio = substr($mysql,0,4);
	return $dia.'/'.$mes.'/'.$anio;		
}

function fecha_full($mysql) {
    $e = explode(" ",$mysql);
    if (sizeof($e)>1) $hora = substr($e[1],0,5);
    else $hora = "";
	$dia = substr($e[0],8,2);
	$mes = substr($e[0],5,2);
	$anio = substr($e[0],0,4);
	return $dia.' de '.nombre_mes($mes).' de '.$anio.((!empty($hora)) ? ' a las '.$hora : "");
}


function hora($f) {
    $e = explode(" ",$f);
    return substr($e[1],0,5);
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

function nombre_mes($i,$lang="es") {
    $n="";
    if ($lang == "es") {
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
    } else if ($lang == "es") {
        switch ($i) {
            case 1: $n = "January"; break;
            case 2: $n = "February"; break;
            case 3: $n = "March"; break;
            case 4: $n = "April"; break;
            case 5: $n = "May"; break;
            case 6: $n = "June"; break;
            case 7: $n = "July"; break;
            case 8: $n = "August"; break;
            case 9: $n = "September"; break;
            case 10: $n = "October"; break;
            case 11: $n = "November"; break;
            case 12: $n = "December"; break;
        }        
    }
	return $n;
}

function nombre_mes_corto($i,$lang="es") {
    $n="";
    if ($lang == "es") {
        switch ($i) {
            case 1: $n = "Ene"; break;
            case 2: $n = "Feb"; break;
            case 3: $n = "Mar"; break;
            case 4: $n = "Abr"; break;
            case 5: $n = "May"; break;
            case 6: $n = "Jun"; break;
            case 7: $n = "Jul"; break;
            case 8: $n = "Ago"; break;
            case 9: $n = "Sep"; break;
            case 10: $n = "Oct"; break;
            case 11: $n = "Nov"; break;
            case 12: $n = "Dic"; break;
        }        
    } else if ($lang == "es") {
        switch ($i) {
            case 1: $n = "January"; break;
            case 2: $n = "February"; break;
            case 3: $n = "March"; break;
            case 4: $n = "April"; break;
            case 5: $n = "May"; break;
            case 6: $n = "June"; break;
            case 7: $n = "July"; break;
            case 8: $n = "August"; break;
            case 9: $n = "September"; break;
            case 10: $n = "October"; break;
            case 11: $n = "November"; break;
            case 12: $n = "December"; break;
        }        
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