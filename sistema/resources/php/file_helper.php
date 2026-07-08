<?php
////////////////////////////////////////////////////////////////////
//
//   FUNCIONES QUE TENGAN QUE VER CON EL TRATAMIENTO DE ARCHIVOS
//
////////////////////////////////////////////////////////////////////


/**
 * Devuelve la extension de un archivo
 * @param String $file Archivo
 * @return Extension del archivo (sin el punto)
 */
function get_extension($file) {
    return strtolower(end(explode(".",$file)));
}

/**
 * Copia todos los archivos y subdirectorios de un origen a un destino
 * El destino se crea automaticamente
 * @param String $dirOrigen Directorio de Origen
 * @param String $dirDestino Directorio de Destino
 */
function copy_all($dirOrigen, $dirDestino)
{
	if ($vcarga = opendir($dirOrigen))
	{
		if (!mkdir($dirDestino, 0777, true)) {
			die("Error al crear el directorio $dirDestino");
		}
		
		while($file = readdir($vcarga)) //lo recorro enterito
		{
			if ($file != "." && $file != "..") //quito el raiz y el padre
			{
				if (!is_dir("$dirOrigen/$file")) //pregunto si no es directorio
				{
					if(!copy("$dirOrigen/$file", "$dirDestino/$file")) //como no es directorio, copio de origen a destino
					{
						return FALSE;
					}
				} else {
					// llamamos recursivamente
					copy_all("$dirOrigen/$file/", "$dirDestino/$file/");
				}
			}
		}
		closedir($vcarga);
		
		// Salio todo OK
		return TRUE;
		
	} else return FALSE;
}



/**
 * Borra todos los archivos de un directorio en particular,
 * menos los nombres de los que son pasados por el parametro $black_list
 * @param String $path Directorio que se quiere vaciar
 * @param Array $black_list Array de string con los nombres de archivos que se quieren conservar
 */
function empty_directory($path,$black_list = array()) 
{
	$black_list[] = '.';
	$black_list[] = '..';
	
	if (!is_dir($path)) return;
	
	$dir = @opendir($path);
	while($archivo = @readdir($dir))
	{
		if (!in_array($archivo,$black_list))
		{
			// Si no esta en la "LISTA PROHIBIDA"
			// Eliminamos el archivo
			@unlink("$path/$archivo");					
		}
	}
	@closedir($dir);
}




/**
 * A partir de un nombre de archivo dado (no path),
 * devuelve un string con un nombre de archivo correcto, para ser accedido
 * correctamente por URL (le saca todos los caracteres raros)
 * @param $tring $name Nombre de archivo
 * @return String
 */
function filename($name = '', $replace_char = '_', $agregar_fecha = 1)
{
	// Primero lo ponemos en minusculas
	$name = strtolower($name);
    
    if ($agregar_fecha == 0) $name = str_replace(".","",$name);
    $name = str_replace(",","",$name);    
	
	// Reemplazamos todos los caracteres invalidos
	$name = str_replace(" ",$replace_char,$name);
	$name = str_replace("Ñ","n",$name);
	$name = str_replace("Á","a",$name);
	$name = str_replace("É","e",$name);
	$name = str_replace("Í","i",$name);
	$name = str_replace("Ó","o",$name);
	$name = str_replace("Ú","u",$name);    
	$name = str_replace("ñ","n",$name);
	$name = str_replace("á","a",$name);
	$name = str_replace("é","e",$name);
	$name = str_replace("í","i",$name);
	$name = str_replace("ó","o",$name);
	$name = str_replace("ú","u",$name);
	$name = str_replace("à","a",$name);
	$name = str_replace("è","e",$name);
	$name = str_replace("ì","i",$name);
	$name = str_replace("ò","o",$name);
	$name = str_replace("ù","u",$name);
	$name = str_replace("â","a",$name);
	$name = str_replace("ê","e",$name);
	$name = str_replace("î","i",$name);
	$name = str_replace("ô","o",$name);
	$name = str_replace("û","u",$name);
	$name = str_replace("ä","a",$name);
	$name = str_replace("ë","e",$name);
	$name = str_replace("ï","i",$name);
	$name = str_replace("ö","o",$name);
	$name = str_replace("ü","u",$name);
	$name = str_replace("ç","c",$name);
	$name = str_replace("Ç","c",$name);
	$name = str_replace("(",$replace_char,$name);
	$name = str_replace(")",$replace_char,$name);
	$name = str_replace("[",$replace_char,$name);
	$name = str_replace("]",$replace_char,$name);
	$name = str_replace("{",$replace_char,$name);
	$name = str_replace("}",$replace_char,$name);
	$name = str_replace(",",$replace_char,$name);
	$name = str_replace("$",$replace_char,$name);
	$name = str_replace("'",$replace_char,$name);
	$name = str_replace("\"",$replace_char,$name);
	$name = str_replace("#",$replace_char,$name);
	$name = str_replace("%",$replace_char,$name);
	$name = str_replace("&",$replace_char,$name);
	$name = str_replace("=",$replace_char,$name);
	$name = str_replace("¡",$replace_char,$name);
	$name = str_replace("!",$replace_char,$name);
	$name = str_replace("¿",$replace_char,$name);
	$name = str_replace("?",$replace_char,$name);
	$name = str_replace("+",$replace_char,$name);
	$name = str_replace(";",$replace_char,$name);
	$name = str_replace("<",$replace_char,$name);
	$name = str_replace(">",$replace_char,$name);
	$name = str_replace("*",$replace_char,$name);
	$name = str_replace("º",$replace_char,$name);
	$name = str_replace("ª",$replace_char,$name);
	$name = str_replace("|",$replace_char,$name);
	$name = str_replace("~",$replace_char,$name);
	$name = str_replace("@",$replace_char,$name);
	
    if ($agregar_fecha == 1) return date("YmdHis_").$name;
    else return $name;
}

?>