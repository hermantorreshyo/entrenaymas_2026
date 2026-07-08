<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Configuracion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_configuracion","id");
	}

  function get_cotizacion($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $moneda = isset($config["moneda"]) ? $config["moneda"] : 'U$D';
    $q_cotiz = $this->db->query("SELECT * FROM cotizaciones WHERE moneda = '".$moneda."' AND id_empresa = $id_empresa ");
    if ($q_cotiz->num_rows()>0) {
      $r_cotiz = $q_cotiz->row();
      return ((float)$r_cotiz->valor);
    }

    if ($id_empresa == 224) {
      $sql = "SELECT * FROM fact_configuracion WHERE id_empresa = $id_empresa";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $rr = $q->row();
        return ((float)$rr->cotizacion_dolar);
      }
    }

    $q_cotiz = $this->db->query("SELECT * FROM cotizaciones WHERE moneda = '".$moneda."' AND id_empresa = 0 ");
    if ($q_cotiz->num_rows()>0) {
      $r_cotiz = $q_cotiz->row();
      return ((float)$r_cotiz->valor);
    }
    return 1;
  }

	function es_local() {
		$sql = "SELECT * FROM com_configuracion WHERE id = 1";
		$q = $this->db->query($sql);
		$row = $q->row();
		return $row->local;
	}

  function tiene_activa_columna($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $tabla = isset($config["tabla"]) ? $config["tabla"] : "";
    $campo = isset($config["campo"]) ? $config["campo"] : "";
    $sql = "SELECT * FROM tablas_configuracion ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND tabla = '$tabla' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $c = json_decode($r->configuracion);
      foreach($c as $cc) {
        if ($cc->campo == "items" && $cc->visible == 1) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

	// Devuelve la configuracion de la tabla articulos
	function get_tabla_articulos($config = array()) {
		$id_empresa = $config["id_empresa"];
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    $sql = "SELECT * FROM tablas_configuracion WHERE id_empresa = $id_empresa AND tabla = 'articulos' ";
    $q = $this->db->query($sql);
    $this->db->db_debug = $db_debug;
    if ($q === FALSE || $q->num_rows() == 0) {
    	$salida = array(
    		"cant_items"=>10,
    		"tabla"=>"articulos",
    		"campos"=>array(
	    		array(
	    			"visible"=>1,						// Si se muestra o no por defecto
	    			"campo"=>"path",				// Atributo del objeto
	    			"titulo"=>"Imagen",			// Titulo del encabezado
	    			"ordenable"=>0,					// Agrega sortable como clase
	    			"ocultable"=>1,					// Agrega hidden-xs como clase
	    			"clases"=>"w50 tac",		// Clases CSS especiales
	    		),
	    		array(
	    			"visible"=>1,
	    			"campo"=>"codigo",
	    			"titulo"=>"Código",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"w100",
	    		),
	    		array(
	    			"visible"=>1,
	    			"campo"=>"nombre",
	    			"titulo"=>"Nombre",
	    			"ordenable"=>1,
	    			"ocultable"=>0,
	    			"clases"=>"",
	    		),
          array(
            "visible"=>0,
            "campo"=>"tipo",
            "titulo"=>"Clase",
            "ordenable"=>1,
            "ocultable"=>0,
            "clases"=>"",
          ),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"codigo_barra",
	    			"titulo"=>"Cod. Barra",
	    			"ordenable"=>0,
	    			"ocultable"=>1,
	    			"clases"=>"w100",
	    		),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"codigo_prov",
	    			"titulo"=>"Cod. Prov.",
	    			"ordenable"=>0,
	    			"ocultable"=>1,
	    			"clases"=>"w100",
	    		),
          array(
            "visible"=>0,
            "campo"=>"proveedor",
            "titulo"=>"Proveedor",
            "ordenable"=>1,
            "ocultable"=>1,
            "clases"=>"",
          ),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"marca",
	    			"titulo"=>"Marca",
	    			"ordenable"=>0,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>1,
	    			"campo"=>"rubro",
	    			"titulo"=>"Categoria",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
          array(
            "visible"=>0,
            "campo"=>"usuario",
            "titulo"=>"Usuario",
            "ordenable"=>0,
            "ocultable"=>1,
            "clases"=>"",
          ),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"fecha_ingreso",
	    			"titulo"=>"Alta",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"fecha_mov",
	    			"titulo"=>"Modif.",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"porc_ganancia",
	    			"titulo"=>"Margen",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"costo_neto",
	    			"titulo"=>"Costo Neto",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"costo_final",
	    			"titulo"=>"Costo Final",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>1,
	    			"campo"=>"precio_final_dto",
	    			"titulo"=>"Lista 1",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"precio_final_dto_2",
	    			"titulo"=>"Lista 2",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"precio_final_dto_3",
	    			"titulo"=>"Lista 3",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),    		
	    		array(
	    			"visible"=>0,
	    			"campo"=>"precio_final_dto_4",
	    			"titulo"=>"Lista 4",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),    		
	    		array(
	    			"visible"=>0,
	    			"campo"=>"precio_final_dto_5",
	    			"titulo"=>"Lista 5",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),    		
	    		array(
	    			"visible"=>0,
	    			"campo"=>"precio_final_dto_6",
	    			"titulo"=>"Lista 6",
	    			"ordenable"=>1,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    		array(
	    			"visible"=>0,
	    			"campo"=>"stock_almacenes",
	    			"titulo"=>"Stock", // Este es un campo que se trata de una manera especial
	    			"ordenable"=>0,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		),
	    	),
    	);
    	return $salida;
    } else {
    	$row = $q->row();
    	return array(
    		"tabla"=>"articulos",
    		"cant_items"=>$row->cant_items,
    		"campos"=>json_decode($row->configuracion)
    	);
    }
	}


	// Devuelve la configuracion de la tabla ventas
	function get_tabla_ventas($config = array()) {
		$id_empresa = $config["id_empresa"];
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    $sql = "SELECT * FROM tablas_configuracion WHERE id_empresa = $id_empresa AND tabla = 'ventas' ";
    $q = $this->db->query($sql);
    $this->db->db_debug = $db_debug;
    if ($q === FALSE || $q->num_rows() == 0) {
    	$salida = array(
    		"cant_items"=>10,
    		"tabla"=>"ventas",
    		"campos"=>array(
	    		array(
	    			"visible"=>1,
	    			"campo"=>"punto_venta",
	    			"titulo"=>"PV",
	    			"ordenable"=>0,
	    			"ocultable"=>0,
	    			"clases"=>"",
	    		), 
	    		array(
	    			"visible"=>1,
	    			"campo"=>"fecha",
	    			"titulo"=>"Fecha",
	    			"ordenable"=>0,
	    			"ocultable"=>0,
	    			"clases"=>"",
	    		), 
	    		array(
	    			"visible"=>0,
	    			"campo"=>"tipo",
	    			"titulo"=>"Tipo",
	    			"ordenable"=>0,
	    			"ocultable"=>0,
	    			"clases"=>"",
	    		), 
          array(
            "visible"=>0,
            "campo"=>"reparto",
            "titulo"=>"Reparto",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
          array(
            "visible"=>0,
            "campo"=>"impresa",
            "titulo"=>"Imp.",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
          array(
            "visible"=>0,
            "campo"=>"codigo_cliente",
            "titulo"=>"Cod.",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
          array(
            "visible"=>0,
            "campo"=>"pagada",
            "titulo"=>"Pago",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
	    		array(
	    			"visible"=>1,
	    			"campo"=>"cliente",
	    			"titulo"=>"Cliente",
	    			"ordenable"=>0,
	    			"ocultable"=>0,
	    			"clases"=>"",
	    		), 
          array(
            "visible"=>0,
            "campo"=>"direccion",
            "titulo"=>"Direccion",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
          array(
            "visible"=>0,
            "campo"=>"telefono",
            "titulo"=>"Telefono",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
	    		array(
	    			"visible"=>1,
	    			"campo"=>"comprobante",
	    			"titulo"=>"Comprobante",
	    			"ordenable"=>0,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		), 
	    		array(
	    			"visible"=>0,
	    			"campo"=>"numero",
	    			"titulo"=>"Numero",
	    			"ordenable"=>0,
	    			"ocultable"=>0,
	    			"clases"=>"",
	    		), 
	    		array(
	    			"visible"=>0,
	    			"campo"=>"vendedor",
	    			"titulo"=>"Vendedor",
	    			"ordenable"=>0,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		), 
          array(
            "visible"=>0,
            "campo"=>"concepto",
            "titulo"=>"Concepto",
            "ordenable"=>0,
            "ocultable"=>1,
            "clases"=>"",
          ), 
          array(
            "visible"=>0,
            "campo"=>"sucursal",
            "titulo"=>"Sucursal",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
	    		array(
	    			"visible"=>1,
	    			"campo"=>"estado",
	    			"titulo"=>"Pago",
	    			"ordenable"=>0,
	    			"ocultable"=>1,
	    			"clases"=>"",
	    		), 
          array(
            "visible"=>0,
            "campo"=>"custom_6",
            "titulo"=>"Envío",
            "ordenable"=>0,
            "ocultable"=>1,
            "clases"=>"",
          ), 
          array(
            "visible"=>0,
            "campo"=>"items",
            "titulo"=>"Items",
            "ordenable"=>0,
            "ocultable"=>1,
            "clases"=>"w250",
          ), 
	    		array(
	    			"visible"=>1,
	    			"campo"=>"total",
	    			"titulo"=>"Total",
	    			"ordenable"=>0,
	    			"ocultable"=>0,
	    			"clases"=>"tar w150",
	    		), 
	    		array(
	    			"visible"=>0,
	    			"campo"=>"usuario",
	    			"titulo"=>"Usuario",
	    			"ordenable"=>0,
	    			"ocultable"=>0,
	    			"clases"=>"",
	    		),    		
	    	),
    	);
    	return $salida;
    } else {
    	$row = $q->row();
    	return array(
    		"tabla"=>"ventas",
    		"cant_items"=>$row->cant_items,
    		"campos"=>json_decode($row->configuracion)
    	);
    }
	}


  // Devuelve la configuracion de la tabla compras
  function get_tabla_compras($config = array()) {
    $id_empresa = $config["id_empresa"];
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    $sql = "SELECT * FROM tablas_configuracion WHERE id_empresa = $id_empresa AND tabla = 'compras' ";
    $q = $this->db->query($sql);
    $this->db->db_debug = $db_debug;
    if ($q === FALSE || $q->num_rows() == 0) {
      $salida = array(
        "cant_items"=>30,
        "tabla"=>"compras",
        "campos"=>array(
          array(
            "visible"=>0,
            "campo"=>"sucursal",
            "titulo"=>"Sucursal",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
          array(
            "visible"=>1,
            "campo"=>"fecha",
            "titulo"=>"Fecha",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
          array(
            "visible"=>1,
            "campo"=>"proveedor",
            "titulo"=>"Proveedor",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
          array(
            "visible"=>1,
            "campo"=>"tipo_comprobante",
            "titulo"=>"Tipo",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"",
          ), 
          array(
            "visible"=>1,
            "campo"=>"comprobante",
            "titulo"=>"Comprobante",
            "ordenable"=>0,
            "ocultable"=>1,
            "clases"=>"",
          ), 
          array(
            "visible"=>0,
            "campo"=>"total_neto",
            "titulo"=>"Neto",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"tar w100",
          ), 
          array(
            "visible"=>0,
            "campo"=>"total_iva",
            "titulo"=>"IVA",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"tar w100",
          ), 
          array(
            "visible"=>0,
            "campo"=>"total_regimenes_especiales",
            "titulo"=>"Reg.",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"tar w100",
          ), 
          array(
            "visible"=>1,
            "campo"=>"total_general",
            "titulo"=>"Total",
            "ordenable"=>0,
            "ocultable"=>0,
            "clases"=>"tar w150",
          ), 
          array(
            "visible"=>0,
            "campo"=>"observaciones",
            "titulo"=>"Obs.",
            "ordenable"=>0,
            "ocultable"=>1,
            "clases"=>"",
          ),           
        ),
      );
      return $salida;
    } else {
      $row = $q->row();
      return array(
        "tabla"=>"compras",
        "cant_items"=>$row->cant_items,
        "campos"=>json_decode($row->configuracion)
      );
    }
  }


  function set_tablas_configuracion($data) {

    if (!isset($data->id_empresa) || empty($data->id_empresa)) return;
    if (!isset($data->tabla) || empty($data->tabla)) return;
    if (!isset($data->cant_items) || empty($data->cant_items)) return;
    if (!isset($data->configuracion) || empty($data->configuracion)) return;

    $data->configuracion = addslashes($data->configuracion);

		// Deshabilitamos los errores de la base de datos de codeigniter para evitar que si no existe la tabla tire un error
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    $sql = "SELECT * FROM tablas_configuracion WHERE id_empresa = $data->id_empresa AND tabla = '$data->tabla' ";
    $q = $this->db->query($sql);
    $this->db->db_debug = $db_debug;
    if ($q === FALSE) return;
    if ($q->num_rows()>0) {
      $sql = "UPDATE tablas_configuracion SET ";
      $sql.= " configuracion = '$data->configuracion', ";
      $sql.= " cant_items = '$data->cant_items' ";
      $sql.= "WHERE id_empresa = $data->id_empresa AND tabla = '$data->tabla' ";
    } else {
      $sql = "INSERT INTO tablas_configuracion (";
      $sql.= " id_empresa, tabla, configuracion, cant_items ";
      $sql.= ") VALUES (";
      $sql.= " '$data->id_empresa', '$data->tabla', '$data->configuracion', '$data->cant_items' ";
      $sql.= ")";
    }
    $this->db->query($sql);

  }
	
}