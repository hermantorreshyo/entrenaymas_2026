<?php

/**
 * Obtiene el precio final de un producto, a partir de su precio base
 * @param float $base Precio base del producto, sin aplicarle nada
 * @param boolean $usa_descuento Booleano que indica si utiliza descuento o no
 * @param float $porc_descuento Porcentaje de descuento que se aplicara
 * @return float Precio final del producto
 */
function get_final_price($base=0,$usa_descuento=0,$porc_descuento=0)
{
    // TODO: faltaria el manejo de IVA
    
    $precio = $base;
    
    // Si usa descuento
    if ($usa_descuento) {
        $precio = $base * ((100-$porc_descuento)/100);
    }
    
    return $precio;
}


/**
 * @param float $price Precio que se quiere formatear
 * @param int $decimales Cantidad de decimales que se quiere agregar
 * @param string $sep_decimal Caracter que indica el separador de decimales
 */
function format_price($price,$decimales=2,$sep_decimal=",")
{
    // Primero redondeamos a la cantidad de decimales
    $factor = pow(10, $decimales); 
    $price = (round($price*$factor)/$factor);
    
    // Formamos el String
    $partes = explode(".",$price); // Separamos por el punto
    
    if (sizeof($partes)>1) {
        // Rellenamos con 0's hasta la cantidad de decimales necesaria
        $parte_decimal = str_pad($partes[1],$decimales,"0"); 
    } else {
        // Ponemos todos 0's
        $parte_decimal = "";
        for($i=0;$i<$decimales;$i++) $parte_decimal.="0";
    }
    
    $price = $partes[0].$sep_decimal.$parte_decimal;
    return $price;    
}

?>