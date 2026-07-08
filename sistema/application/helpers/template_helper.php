<?php
/**
 * Procesa el template dado por $tpl llenando los campos con elementos del $array
 */
function process_template($tpl,$array) {
    
    $str = file_get_contents($tpl);
    $original = $str;
    $pos_inicial = -1;
    
    // Array de campos estaticos
    $campos = array();
    while(($pos_inicial = strpos($str,"{"))!==FALSE) {
        $pos_final = strpos($str,"}")-$pos_inicial+1;
        $campo = substr($str,$pos_inicial,$pos_final);
        $c = explode(",",$campo);
        $campos[] = array(
            "nombre"=>str_replace("}","",str_replace("{","",$c[0])),
            "campo"=>$campo,
            "longitud"=>(isset($c[1])) ? str_replace("}","",$c[1]) : 0,
            "alineado"=>(isset($c[2])) ? str_replace("}","",$c[2]) : "L",
            "style"=>(isset($c[3])) ? str_replace("}","",$c[3]) : "",
        );
        $str = substr($str,strpos($str,"}")+1);
    }
    file_put_contents("salida.txt",print_r($campos,true));

    foreach($campos as $c) {
        if ($c["alineado"] == "L") $alineado = STR_PAD_RIGHT;
        else $alineado = STR_PAD_LEFT;
        // Agregamos estilo a la linea
        $head = ""; $foot = "";
        if (!empty($c["style"])) {
            $style = $c["style"];
            if ($style == "C") {
                $head = chr(27)."!".chr(4);
                $foot = chr(27)."!".chr(0);
            }
        }
        $original = str_replace($c["campo"],$head.str_pad($array[$c["nombre"]],$c["longitud"]," ",$alineado).$foot,$original);
    }
    
    // Si tenemos que recorrer un array
    $pos_items = strpos($original,"[[items]]");
    if ($pos_items !== FALSE) {
        $items = substr($original,$pos_items+9,strpos($original,"[[items]]",$pos_items+1)-$pos_items-9);
        $items_original = substr($original,$pos_items,strpos($original,"[[items]]",$pos_items+1)-$pos_items+11);
        $items = str_replace("\n","",$items);
        $linea_items = $items;
        
        // Reemplazamos los campos del array
        $campos = array();
        while(($pos_inicial = strpos($items,"["))!==FALSE) {
            $pos_final = strpos($items,"]")-$pos_inicial+1;
            $campo = substr($items,$pos_inicial,$pos_final);
            $c = explode(",",$campo);
            $campos[] = array(
                "nombre"=>str_replace("]","",str_replace("[","",$c[0])),
                "campo"=>$campo,
                "longitud"=>(isset($c[1])) ? str_replace("]","",$c[1]) : 0,
                "style"=>(isset($c[2])) ? str_replace("]","",$c[2]) : "",
            );
            $items = substr($items,strpos($items,"]")+1);
        }
        $items_final = "";
        $ultima_linea = 0;
        for($i=0;$i<sizeof($array["items"]);$i++){
            $item = $array["items"][$i];
            $linea = $linea_items;
            foreach($campos as $c) {
                
                // Agregamos estilo a la linea
                $head = ""; $foot = "";
                if (!empty($c["style"])) {
                    $style = $c["style"];
                    if ($style == "C") {
                        $head = chr(27)."!".chr(4);
                        $foot = chr(27)."!".chr(0);
                    }
                }
                
                // Reemplazamos la linea por el valor que corresponde, y rellenamos a esa longitud
                $linea = str_replace($c["campo"],$head.str_pad($item[$c["nombre"]],$c["longitud"]," ",STR_PAD_RIGHT).$foot,$linea);
            }
            $items_final.=$linea."\n";
            $ultima_linea++;
        }
        
        // Rellenamos los espacios que le faltan a la factura
        for($i=$ultima_linea;$i<17;$i++) {
            $items_final.="\n";
        }
        
        $original = str_replace($items_original,$items_final,$original);
    }
    return $original;
}
?>