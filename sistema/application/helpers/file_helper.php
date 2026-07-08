<?php
////////////////////////////////////////////////////////////////////
//
//   FUNCIONES QUE TENGAN QUE VER CON EL TRATAMIENTO DE ARCHIVOS
//
////////////////////////////////////////////////////////////////////

// Guarda una imagen
function grab_image($url,$saveto){
  $ch = curl_init ($url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
  $raw=curl_exec($ch);
  curl_close ($ch);
  if(file_exists($saveto)){
    unlink($saveto);
  }
  $fp = fopen($saveto,'x');
  fwrite($fp, $raw);
  fclose($fp);
}

/**
 * Devuelve la extension de un archivo
 * @param String $file Archivo
 * @return Extension del archivo (sin el punto)
 */
function get_extension($file) {
	$arr = explode(".",$file);
	if (sizeof($arr) == 0) return "";
	else return strtolower(end($arr));
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
function filename($name = '', $replace_char = '-', $agregar_fecha = 0) {
    	
	// Reemplazamos todos los caracteres invalidos
	$name = str_replace(" ",$replace_char,$name);
  $name = str_replace("&quot;","",$name);
  $name = str_replace("&#039;","",$name);
	$name = str_replace(",","",$name);
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
	$name = str_replace("(","",$name);
	$name = str_replace(")","",$name);
	$name = str_replace("[","",$name);
	$name = str_replace("]","",$name);
	$name = str_replace("{","",$name);
	$name = str_replace("}","",$name);
	$name = str_replace(",","",$name);
	$name = str_replace(":","",$name);
	$name = str_replace("$","",$name);
	$name = str_replace("'","",$name);
	$name = str_replace("\"","",$name);
	$name = str_replace("#","",$name);
	$name = str_replace("%","",$name);
	$name = str_replace("&","",$name);
	$name = str_replace("=","",$name);
	$name = str_replace("¨","",$name);
	$name = str_replace("´","",$name);
	$name = str_replace("¬","",$name);
	$name = str_replace("Ç","",$name);
	$name = str_replace("¡","",$name);
	$name = str_replace("!","",$name);
	$name = str_replace("¿","",$name);
	$name = str_replace("?","",$name);
	$name = str_replace("+","",$name);
	$name = str_replace(";","",$name);
	$name = str_replace("<","",$name);
	$name = str_replace(">","",$name);
	$name = str_replace("*","",$name);
	$name = str_replace("º","",$name);
	$name = str_replace("°","",$name);
	$name = str_replace("ª","",$name);
	$name = str_replace("|","",$name);
	$name = str_replace("/","",$name);
	$name = str_replace("\\","",$name);
	$name = str_replace("~","",$name);
	$name = str_replace("@","",$name);
	$name = str_replace("—","-",$name);
	$name = str_replace("–","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	$name = str_replace("--","-",$name);
	
  $encoding = mb_detect_encoding($name, mb_detect_order(), false);
	if ($encoding == "UTF-8") $name = utf8_decode($name);
  //if ($agregar_fecha == 0) $name = str_replace(".","",$name);
	
	$name = mb_strtolower($name);
	if ($agregar_fecha == 1) return date("YmdHis_").$name;
  else return $name;
}


// Esta funcion controla si existe el nombre del archivo, y devuelve otro con el mismo pero con un numero al final
function rename_if_exists($directory,$path) {
	if (file_exists($directory.$path)) {
		$iterator = 1;
		while (file_exists($directory.$path)) {
			// get file extension
			$extension = pathinfo($directory.$path, PATHINFO_EXTENSION);
			// get file's name
			$filename = pathinfo($directory.$path, PATHINFO_FILENAME);
			// Tomamos hasta el ultimo "-"
			if (strpos($filename, "-")>0) $filename = substr($filename, 0, strrpos($filename, "-"));
			// add and combine the filename, iterator, extension
			$new_filename = $filename . '-' . $iterator . '.' . $extension;
			// add file name to the end of the path to place it in the new directory; the while loop will check it again
			$path = $new_filename;
			$iterator++;
		}
	}
	return $path;
}
?>