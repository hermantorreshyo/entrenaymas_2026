<?php

// ARCHIVO ENCARGADO DE GUARDAR EN LA TABLA DE LOGS
// LAS OPERACIONES QUE SE REALIZAN EN EL SISTEMA


/**
 * Escribe en la tabla de logs la operacion correspondiente
 * @param String $operacion Texto leible por el admin que indica la operacion
 * @param Int $nivel Entero que representa el nivel del log
 *   0 = Normal
 *   1 = Error
 * @param Int $id_usuario Usuario que realizo la operacion
 *   0 = Sistema
 *  !0 = ID del USUARIO
 *  
 */
function write_log($operacion, $nivel=0, $id_usuario=0) {
    
    if (!function_exists("get_conex")) {
        include("../../params.php");
    }
    
    $conx = get_conex();
    $f_tar = date("Y-m-d H:i:s");
    $sql = "INSERT INTO logs(fecha,id_usuario,operacion,nivel) VALUES (";
    $sql.= "'$f_tar', $id_usuario, '$operacion', $nivel)";
    @mysql_query($sql,$conx);
    
}


?>