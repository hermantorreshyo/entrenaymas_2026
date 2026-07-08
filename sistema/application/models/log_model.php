<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Log_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_log","id","fecha DESC");
	}

	function imprimir($config = array()) {
		$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
		$id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : (isset($_SESSION["id"]) ? $_SESSION["id"] : 0);
		$file = isset($config["file"]) ? $config["file"] : "";
		$texto = isset($config["texto"]) ? $config["texto"] : "";
		if (!file_exists("logs/$id_empresa")) @mkdir("logs/$id_empresa");
		@file_put_contents("logs/$id_empresa/".$file, date("Y-m-d H:i:s").": ".$texto."\n", FILE_APPEND);
	}

	function log_file($file,$texto) {
		file_put_contents($file, date("Y-m-d").": ".$texto."\n", FILE_APPEND);
	}

	function registrar($config = array()) {
		$texto = isset($config["texto"]) ? $config["texto"] : "";
		$link = isset($config["link"]) ? $config["link"] : "";
		$importancia = isset($config["importancia"]) ? $config["importancia"] : "";
		$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
		$id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : $_SESSION["id"];
		$estado = isset($config["estado"]) ? $config["estado"] : ($_SESSION["estado"] == 1 ? 1 : 0);
    $f_tar = date("Y-m-d H:i:s");
    if ($this->db->table_exists('com_log')) {
			$this->db->query("INSERT INTO com_log (id_empresa,id_usuario,fecha,link,texto,importancia,estado) VALUES ('$id_empresa','$id_usuario','$f_tar','$link','$texto','$importancia','$estado')");
		}
	}
    
	/**
	 * @params $importancia Indica la relevancia de la operacion. Valores: D,W,P,S,I
	 */
	function log($texto = "",$link = "",$importancia = "") {
		$id_empresa = parent::get_empresa();
		$id_usuario = $_SESSION["id"];
		$estado = ($_SESSION["estado"] == 1 ? 1 : 0);
    $f_tar = date("Y-m-d H:i:s");
		$this->db->query("INSERT INTO com_log (id_empresa,id_usuario,fecha,link,texto,importancia,estado) VALUES ('$id_empresa','$id_usuario','$f_tar','$link','$texto','$importancia','$estado')");
	}
	
	function notify($data) {
		if (is_array($data)) {
			$data["fecha"] = date("Y-m-d H:i:s");
			$data["importancia"] = "N";
		} else if (is_object($data)) {
			$data->fecha = date("Y-m-d H:i:s");
			$data->importancia = "N";			
		}
		$this->db->insert("com_log",$data);
	}
	
	function get_recent_activity($limit=20,$offset=0) {
		$id_empresa = parent::get_empresa();
		$estado = ($_SESSION["estado"] == 1 ? 1 : 0);
    $f_tar = date("d/m/Y");
		$sql = "SELECT L.*, U.nombre_usuario, ";
		$sql.= " IF('$f_tar' = DATE_FORMAT(fecha,'%d/%m/%Y'),DATE_FORMAT(fecha,'%H:%i'),DATE_FORMAT(fecha,'%d/%m/%Y')) AS fecha_f ";
		$sql.= "FROM com_log L INNER JOIN com_usuarios U ON (L.id_usuario = U.id) WHERE L.id_empresa = $id_empresa ";
		if ($estado == 0) $sql.= "AND estado = 0 ";
		$sql.= "ORDER BY fecha DESC ";
		$sql.= "LIMIT $offset,$limit ";
		$q = $this->db->query($sql);
		return $q->result();
	}
}
