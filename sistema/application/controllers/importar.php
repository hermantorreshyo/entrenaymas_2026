<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Importar extends REST_Controller {
	
  function __construct() {
    parent::__construct();
  }

  function get() {
  	$id_empresa = parent::get_empresa();
  	$id = parent::get_get("id",0);
  	$tabla = parent::get_get("tabla","");
  	$sql = "SELECT * FROM importacion_configuracion WHERE id_empresa = $id_empresa AND id = $id AND tabla = '$tabla' ";
  	$q = $this->db->query($sql);
  	if ($q->num_rows() > 0) {
  		$row = $q->row();
  		$row->error = 0;
  		echo json_encode($row);
  	} else {
  		echo json_encode(array("error"=>1));
  	}
  }

  function procesar_archivo() {

  	$id_empresa = parent::get_empresa();
  	$id = parent::get_post("id",0);
  	$ignorar_primera_fila = parent::get_post("ignorar_primera_fila",0);
    $solo_actualizar = parent::get_post("solo_actualizar",0);
    $prefijo_codigo = parent::get_post("prefijo_codigo","");
  	$campos = parent::get_post("campos","");
    $tabla = parent::get_post("tabla","");
    $moneda = parent::get_post("moneda","$");
    $id_proveedor = parent::get_post("id_proveedor",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $id_usuario = parent::get_post("id_usuario",0);
    $fecha_stock = parent::get_post("fecha_stock","");
    if (!empty($fecha_stock)) {
      $this->load->helper("fecha_helper");
      $fecha_stock = fecha_mysql($fecha_stock);
    }

    // Si es USHUAIA, tenemos que pasar las observaciones del proveedor a la nueva importacion
    $observaciones = "";
    if ($id_empresa == 444) {
      $sql = "SELECT * FROM importaciones_articulos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_proveedor = $id_proveedor ";
      $sql.= "ORDER BY fecha_alta DESC ";
      $sql.= "LIMIT 0,1 ";
      $q_us = $this->db->query($sql);
      if ($q_us->num_rows()>0) {
        $r_us = $q_us->row();
        $observaciones = $r_us->observaciones;
      }
    }

    $insert_defaults = array();
    $update_defaults = array();

    if ($tabla == "articulos") {
      $insert_defaults = array(
        "fecha_mov"=>date("Y-m-d"),
        "fecha_ingreso"=>date("Y-m-d"),
        "moneda"=>$moneda,
        "last_update"=>time(),
        // Por defecto se importa en la web, salvo a CRUMB
        "lista_precios"=>(($id_empresa == 435) ? 1 : 2),
        "usa_stock"=>1,
      );
      $update_defaults = array(
        "fecha_mov"=>date("Y-m-d"),
        "last_update"=>time(),
      );
    } else if ($tabla == "importaciones_articulos_items") {
      $insert_defaults = array(
        "fecha_modif"=>date("Y-m-d"),
        "moneda"=>$moneda,
      );
      $update_defaults = array(
        "fecha_modif"=>date("Y-m-d"),
      );
    }

    $cotizacion = 1;
    if ($moneda != '$') {
      $this->load->model("Configuracion_Model");
      $cotizacion = $this->Configuracion_Model->get_cotizacion(array(
        "id_empresa"=>$id_empresa
      ));
    }

    try {
      $this->load->library("ExcelImporter");
      $importer = new ExcelImporter();
      $importer->process_file(array(
        "id"=>$id,
        "id_empresa"=>$id_empresa,
        "table"=>$tabla,
        "fields"=>$campos,
        "ignore_first_line"=>$ignorar_primera_fila,
        "only_update"=>$solo_actualizar,
        "prefijo_codigo"=>$prefijo_codigo,
        "key_field"=>(($id_empresa == 444) ? "codigo_prov" : "codigo"),
        "db"=>$this->db,
        "id_proveedor"=>$id_proveedor,
        "id_usuario"=>$id_usuario,
        "id_sucursal"=>$id_sucursal,
        "fecha_stock_default"=>$fecha_stock,
        "insert_defaults"=>$insert_defaults,
        "update_defaults"=>$update_defaults,
        "moneda_lista"=>$moneda,
        "cotizacion"=>$cotizacion,
      ));

      // Si es USHUAIA, guardamos las observaciones a la nueva importacion
      if ($id_empresa == 444) {
        $sql = "UPDATE importaciones_articulos SET observaciones = '$observaciones', moneda = '$moneda' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = $id ";
        $this->db->query($sql);

        // Registramos la importacion al LOG
        $this->load->model("Importacion_Articulo_Model");
        $importacion = $this->Importacion_Articulo_Model->get($id);
        $this->load->model("Log_Model");
        $this->Log_Model->log("Importo ".$importacion->proveedor->nombre." (ID: $importacion->id)",$importacion->id);
      }

      echo json_encode(array("error"=>0,"id"=>$id));
    } catch(Exception $e) {
      echo json_encode(array("error"=>1,"mensaje"=>$e->getMessage()));
    }
  }
	
	function get_by_tabla($tabla) {
		$id_empresa = parent::get_empresa();

		// Obtenemos la configuracion
		$sql = "SELECT * FROM importacion_configuracion WHERE id_empresa = $id_empresa AND tabla = '$tabla' ";
		$q = $this->db->query($sql);
		if ($q->num_rows()>0) {
			$row = $q->row();
		} else {
			$row = new stdClass();
			$row->ignorar_primera_fila = 0;
			$row->reemplazar_duplicados = 0;
			$row->vaciar_antes_importar = 0;
		}

		// Obtenemos los campos
		$sql = "SELECT * FROM importacion_campos WHERE id_empresa = $id_empresa AND tabla = '$tabla' ";
		$sql.= "ORDER BY columna ASC ";
		$q = $this->db->query($sql);
		echo json_encode(array(
			"campos"=>$q->result(),
			"ignorar_primera_fila"=>$row->ignorar_primera_fila,
			"reemplazar_duplicados"=>$row->reemplazar_duplicados,
			"vaciar_antes_importar"=>$row->vaciar_antes_importar,
		));
	}
	
	// @param $procesar: Indica si el archivo es procesado o solamente se guarda la configuracion
	function guardar($procesar = 0) {

		set_time_limit(0);

		$id_empresa = parent::get_empresa();
		$datos = json_decode($this->input->post("data"));
		$tabla = $datos->tabla;
		$archivo = $datos->archivo;

		// Eliminamos la configuracion
		$this->db->query("DELETE FROM importacion_configuracion WHERE tabla = '$tabla' AND id_empresa = $id_empresa ");
		$this->db->query("DELETE FROM importacion_campos WHERE tabla = '$tabla' AND id_empresa = $id_empresa ");
		$this->db->query("DELETE FROM importacion_campos_relaciones WHERE tabla = '$tabla' AND id_empresa = $id_empresa ");

		$campos_id = array();

		// Guardamos el orden de los campos
		foreach($datos->campos as $campo) {

			// En un array ponemos los campos que forman el ID
			if ($campo->es_id == 1) $campos_id[] = $campo;

			// Insertamos el campo
			$this->db->insert("importacion_campos",array(
				"id_empresa"=>$id_empresa,
				"tabla"=>$tabla,
				"campo"=>$campo->campo,
				"columna"=>$campo->columna,
				"label"=>$campo->label,
				"es_id"=>$campo->es_id,
				"valor"=>$campo->valor,
			));
		}
		// Guardamos la configuracion
		$sql = "INSERT INTO importacion_configuracion (id_empresa,tabla,ignorar_primera_fila,reemplazar_duplicados,vaciar_antes_importar) VALUES (";
		$sql.= "$id_empresa, '$tabla', '$datos->ignorar_primera_fila','$datos->reemplazar_duplicados','$datos->vaciar_antes_importar')";
		$this->db->query($sql);

		if ($procesar == 1) {

			// No hay archivo
			if (empty($archivo)) {
				echo json_encode(array("error"=>1,"mensaje"=>"ERROR: No se ha especificado un archivo de entrada."));
				exit();				
			}

			// Ningun campo esta seteado como ID
			if (empty($campos_id)) {
				echo json_encode(array("error"=>1,"mensaje"=>"ERROR: No existe ningun campo que se haya configurado como ID."));
				exit();
			}

			include("resources/php/Excel/PHPExcel/IoFactory.php");
			try {
			    $inputFileType = PHPExcel_IOFactory::identify($archivo);
			    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
			    $objPHPExcel = $objReader->load($archivo);
			} catch(Exception $e) {
				echo json_encode(array("error"=>1,"mensaje"=>'Error loading file "'.pathinfo($archivo,PATHINFO_BASENAME).'": '.$e->getMessage()));
				exit();
			}

			// Get worksheet dimensions
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();

			// TODO: Controlamos si es necesario borrar los datos y volverlos a cargar
			// con la variable $vaciar_antes_importar			

			// Indica si debemos ignorar o no la primera fila
			$row = ($datos->ignorar_primera_fila == 1) ? 2 : 1;

			// Recorremos el archivo
			for ($row; $row <= $highestRow; $row++){ 

			    // Obtenemos toda una fila
			    $rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row,NULL,TRUE,FALSE);
			    $data = $rowData[0];

		    	// Controlamos si el elemento ya existe, utilizando el array de campos_id
		    	$sql = "SELECT * FROM $tabla WHERE ";
		    	$sql_where = "id_empresa = $id_empresa ";
		    	foreach($campos_id as $campo_id) {
		    		$sql_where.= "AND ".$campo_id->campo." = '".$data[$campo_id->columna-1]."' ";
		    	}
		    	$q = $this->db->query($sql.$sql_where);
		    	if ($q->num_rows()>0) {

			    	// Debemos reemplazar los datos duplicados
			    	if ($datos->reemplazar_duplicados == 1) {

			    		// Actualizamos los datos del elemento
			    		$sql = "UPDATE $tabla SET ";
			    		for($kk=0;$kk<sizeof($datos->campos);$kk++) {
			    			$campo = $datos->campos[$kk];
							if (!empty($campo->valor)) {
								// Tomamos el valor fijo
								$sql.= $campo->campo." = '$campo->valor' ";
							} else {
								// Tomamos los datos del campo
								$sql.= $campo->campo." = '".$data[$campo->columna-1]."' ";
							}
			    			if ($kk < sizeof($datos->campos)-1) $sql.= ", ";
			    		}
			    		$sql.= "WHERE ".$sql_where;
			    		$this->db->query($sql);

			    	} else {
			    		// Debemos ignorarlos
			    		continue;
			    	}

			    // Insertamos un elemento nuevo
		    	} else {
		    		$insert_fields = array();
		    		$insert_values = array();
				    foreach($datos->campos as $campo) {
				    	$insert_fields[] = $campo->campo;
				    	$insert_values[] = "'".((!empty($campo->valor)) ? $campo->valor : $data[$campo->columna-1])."'";
				    }
				    if (!empty($insert_fields) && !empty($insert_values) && sizeof($insert_fields) == sizeof($insert_values)) {
				    	$insert_fields[] = "id_empresa";
				    	$insert_values[] = $id_empresa;
				    	$insert_fields_string = implode(",", $insert_fields);
				    	$insert_values_string = implode(",", $insert_values);
				    	$sql = "INSERT INTO $tabla ($insert_fields_string) VALUES ($insert_values_string)";
				    	$this->db->query($sql);
				    }
		    	}
			}			
		}

		// Salio todo bien
		echo json_encode(array("error"=>0,"mensaje"=>"Los datos han sido guardados correctamente."));
	}
		
}