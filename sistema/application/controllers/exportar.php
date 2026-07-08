<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Exportar extends CI_Controller {

  function table_to_report() {
    $tabla = $this->input->post("tabla");
    $titulo = $this->input->post("titulo");
    $fechas = $this->input->post("fechas");
    $this->load->view("reports/tabla",array(
      "tabla"=>$tabla,
      "titulo"=>$titulo,
      "fechas"=>$fechas,
    ));
  }
    
  function array_to_excel() {
      
    $id_empresa = $_POST["id_empresa"];
    $this->load->library("Excel");

    if (isset($_POST['data'])) $datos=json_decode($_POST['data']);
    else $datos = array();

    $where = (isset($_POST['where'])) ? $_POST["where"] : "";

    if (isset($_POST['header'])) $header=json_decode($_POST['header']);
    else $header = array();

		if (empty($datos) && isset($_POST['table'])) {
			$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
			$datos = array();
			// En el campo header se envian los nombres de campos que se quieren exportar
			$header_s = implode(",",$header);
			if(empty($header_s)) $header_s = "*";
			$sql = "SELECT $header_s FROM $table WHERE id_empresa = $id_empresa ";
			if (!empty($where)) $sql.= "AND $where ";
			$q = $this->db->query($sql);
			$datos = $q->result_array();
			
			// Si el header esta vacio, tomamos los nombres de las columnas
			if(empty($header)) {
				$fields = $this->db->field_data($table);	
				foreach ($fields as $field) {
					$header[] = $field->name;
				}
			}
		}
        
    if (isset($_POST['footer'])) $footer=json_decode($_POST['footer']);
    else $footer = array();        
    
    if (isset($_POST['filename'])) $filename = filter_var($_POST['filename'],FILTER_SANITIZE_STRING);
    else $filename = "archivo";
    
    if (isset($_POST['title'])) $title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
    else $title = "";
    
    if (isset($_POST['date'])) $date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
    else $date = "TODAY";
    if ($date == "TODAY") $date = date("d/m/Y");
		
		$this->excel->create(array(
			"date"=>$date,
			"filename"=>$filename,
			"footer"=>$footer,
			"header"=>$header,
			"data"=>$datos,
			"title"=>$title,
		));
        
  }

	/*
    function excel($nombre="exportar") {
		$excel=$_POST['e']; 
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-disposition: attachment; filename=$nombre.xls"); 
		print $excel; 
		exit; 	
    }
    */
    
    function csv($nombre="exportar") {
		$array = json_decode($_POST["e"]);
		$out = fopen('php://output', 'w');
		fputcsv($out,$array);
		fclose($out);
		/*
		header("Content-type: text/csv"); 
		header("Content-disposition: attachment; filename=$nombre.csv");
		foreach($array as $a) {
			
		}
		exit;
		*/
    }
    
}