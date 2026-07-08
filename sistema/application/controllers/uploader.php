<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Uploader extends REST_Controller {

	function __construct() {
		parent::__construct();
	}

  // Controla el numero de version de la base de datos, y envia los scripts
  // para actualizarla en caso de que este desactualizada
  function upgrade_database() {
    $id_empresa = parent::get_post("id_empresa",0);
    $version_db = parent::get_post("version_db",0);

    $q = $this->db->query("SELECT * FROM com_versiones_db WHERE id > $version_db AND subido = 1");
    if ($q->num_rows()>0) {
      $salida = "";
      foreach($q->result() as $row) {
        $salida.= $row->texto."\n";
      }
      echo gzdeflate($salida);
    } else {
      echo gzdeflate("0");
    }
  }

  // Comienza la actualizacion del sistema
  function start_upgrade() {
    // Si es LOCAL, tenemos que llamar al metodo para subir la informacion
    $this->load->model("Configuracion_Model");
    if ($this->Configuracion_Model->es_local() == 1) {
      $dominio = strtolower($_SERVER["HTTP_HOST"]);
      $puerto = $_SERVER["SERVER_PORT"];
      $url = "http://$dominio:$puerto/upgrade.php";
      $c = curl_init($url);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
      curl_exec($c);
      echo json_encode(array(
        "error"=>0,
        "mensaje"=>"El sistema se ha actualizado correctamente.",
      ));
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"La configuracion del sistema no es local, por lo tanto no se puede actualizar.",
      ));
    }    
  }

  function get_data_from_server() {

    set_time_limit(0);

    $id_empresa = parent::get_post("id_empresa",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);
    $completa = parent::get_post("completa",0);

    // La configuracion del sistema debe ser LOCAL
    $q = $this->db->query("SELECT * FROM com_configuracion WHERE id = 1");
    $configuracion = $q->row();
    if ($configuracion->local == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: El sistema no esta configurado para conectarse al servidor (LOCAL=1).",
      ));
      return;            
    }

    $url_server = (empty($configuracion->url_server)) ? "www.varcreative.com" : $configuracion->url_server;

    // Debe haber conexion con el servidor
    $this->load->helper("connection_helper");
    if (!is_connected($url_server)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: No hay conexion con $url_server.",
      ));
      return;
    }

    $url_server = ($url_server == "www.varcreative.com") ? "https://".$url_server : "http://".$url_server;
    $current_timestamp = time();

    // Obtenemos la ultima configuracion
    $q = $this->db->query("SELECT articulos_last_update FROM fact_configuracion WHERE id_empresa = $id_empresa");
    $conf = $q->row();
    $last_update = $conf->articulos_last_update;

    // TODO: Por las dudas traemos lo de ayer tambien
    $last_update = $last_update - (86400*3);
    if ($last_update < 0) $last_update = 0;

    $this->load->helper("import_helper");

    // Guardamos la version en la que se encuentra el punto de venta
    $head = FALSE;
    if ( ($id_empresa == 249 || $id_empresa == 868) && $id_sucursal != 0) {
      $this->load->helper("git_helper");
      $head = get_current_git_commit();
    }
    $head = ($head === FALSE) ? "Desconocido" : $head;

    // A traves de este array iremos haciendo diferentes llamados al servidor para actualizar cada cosa

    // TABLA DE ARTICULOS
    $articulos_obj = new stdClass();
    $articulos_obj->table_name = "articulos";
    $articulos_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $articulos_obj->delete_first = $completa; // Si la actualizacion es completa, borramos primero
    $articulos_obj->url = "$url_server/sistema/articulos/function/export/$id_empresa/$id_sucursal/$last_update/$id_punto_venta/$head/$configuracion->version_db/";

    // TABLA DE ARTICULOS VARIANTES
    $articulos_variantes_obj = new stdClass();
    $articulos_variantes_obj->table_name = "articulos_variantes";
    $articulos_variantes_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $articulos_variantes_obj->delete_first = 1;
    $articulos_variantes_obj->url = "$url_server/sistema/articulos/function/export_variantes/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE ARTICULOS PROPIEDADES
    $articulos_propiedades_obj = new stdClass();
    $articulos_propiedades_obj->table_name = "articulos_propiedades";
    $articulos_propiedades_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $articulos_propiedades_obj->delete_first = 1;
    $articulos_propiedades_obj->url = "$url_server/sistema/articulos/function/export_propiedades/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE ARTICULOS PROPIEDADES OPCIONES
    $articulos_propiedades_opciones_obj = new stdClass();
    $articulos_propiedades_opciones_obj->table_name = "articulos_propiedades_opciones";
    $articulos_propiedades_opciones_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $articulos_propiedades_opciones_obj->delete_first = 1;
    $articulos_propiedades_opciones_obj->url = "$url_server/sistema/articulos/function/export_propiedades_opciones/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE ARTICULOS ATRIBUTOS
    $articulos_atributos_obj = new stdClass();
    $articulos_atributos_obj->table_name = "articulos_atributos";
    $articulos_atributos_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $articulos_atributos_obj->delete_first = 1;
    $articulos_atributos_obj->url = "$url_server/sistema/articulos/function/export_atributos/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE USUARIOS
    $usuarios_obj = new stdClass();
    $usuarios_obj->table_name = "com_usuarios";
    $usuarios_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $usuarios_obj->delete_first = 1;
    $usuarios_obj->url = "$url_server/sistema/usuarios/function/export/$id_empresa/$id_sucursal/0/";

    // TABLA DE CLIENTES
    $clientes_obj = new stdClass();
    $clientes_obj->table_name = "clientes";
    $clientes_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $clientes_obj->delete_first = 1;
    $clientes_obj->url = "$url_server/sistema/clientes/function/export/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE VENDEDORES
    $vendedores_obj = new stdClass();
    $vendedores_obj->table_name = "vendedores";
    $vendedores_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $vendedores_obj->delete_first = 1;
    $vendedores_obj->url = "$url_server/sistema/vendedores/function/export/$id_empresa/";

    $clientes_etiquetas_obj = new stdClass();
    $clientes_etiquetas_obj->table_name = "clientes_etiquetas";
    $clientes_etiquetas_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $clientes_etiquetas_obj->delete_first = 1;
    $clientes_etiquetas_obj->url = "$url_server/sistema/clientes_etiquetas/function/export/$id_empresa/$id_sucursal/$last_update/";

    $clientes_etiquetas_relacion_obj = new stdClass();
    $clientes_etiquetas_relacion_obj->table_name = "clientes_etiquetas_relacion";
    $clientes_etiquetas_relacion_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $clientes_etiquetas_relacion_obj->delete_first = 1;
    $clientes_etiquetas_relacion_obj->url = "$url_server/sistema/clientes_etiquetas/function/export_relaciones/$id_empresa/$id_sucursal/$last_update/";


    // TABLA DEPARTAMENTOS
    $departamentos_comerciales_obj = new stdClass();
    $departamentos_comerciales_obj->table_name = "departamentos_comerciales";
    $departamentos_comerciales_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $departamentos_comerciales_obj->delete_first = 1;
    $departamentos_comerciales_obj->url = "$url_server/sistema/departamentos_comerciales/function/export/$id_empresa/";


    // TABLA DE FACT_CONFIGURACION
    $fact_configuracion_obj = new stdClass();
    $fact_configuracion_obj->table_name = "fact_configuracion";
    $fact_configuracion_obj->where = array(
      array(
        "field"=>"id_empresa",
        "position"=>0,
      ),
    );
    $fact_configuracion_obj->delete_first = 0;
    //$fact_configuracion_obj->create_first = 1;
    //$fact_configuracion_obj->url_create = "$url_server/sistema/configuracion_facturacion/function/create_table/";
    $fact_configuracion_obj->url = "$url_server/sistema/configuracion_facturacion/function/export/$id_empresa/";

    // TABLA DE TARJETAS
    $tarjetas_obj = new stdClass();
    $tarjetas_obj->table_name = "tarjetas";
    $tarjetas_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $tarjetas_obj->delete_first = 0;
    $tarjetas_obj->url = "$url_server/sistema/tarjetas/function/export/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE INTERESES DE TARJETAS
    $tarjetas_intereses_obj = new stdClass();
    $tarjetas_intereses_obj->table_name = "tarjetas_intereses";
    $tarjetas_intereses_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $tarjetas_intereses_obj->delete_first = 1;
    $tarjetas_intereses_obj->url = "$url_server/sistema/tarjetas/function/export_intereses/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE PERFILES
    $perfiles_obj = new stdClass();
    $perfiles_obj->table_name = "com_perfiles";
    $perfiles_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $perfiles_obj->delete_first = 1;
    $perfiles_obj->url = "$url_server/sistema/perfiles/function/export/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE PERMISOS
    $permisos_obj = new stdClass();
    $permisos_obj->table_name = "com_permisos_modulos";
    $permisos_obj->where = array(
      array(
        "field"=>"id_modulos",
        "position"=>0,
      ),
      array(
        "field"=>"id_perfiles",
        "position"=>1,
      ),
    );
    $permisos_obj->delete_first = 1;
    $permisos_obj->url = "$url_server/sistema/perfiles/function/export_permisos/$id_empresa/$id_sucursal/$last_update/";

    // TABLA DE EMPRESAS MODULOS
    $modulos_empresas_obj = new stdClass();
    $modulos_empresas_obj->table_name = "com_modulos_empresas";
    $modulos_empresas_obj->delete_first = 1;
    $modulos_empresas_obj->where = array(
      array(
        "field"=>"id_modulo",
        "position"=>0,
      ),
      array(
        "field"=>"id_empresa",
        "position"=>1,
      ),
    );
    $modulos_empresas_obj->url = "$url_server/sistema/perfiles/function/export_modulos_empresas/$id_empresa/";

    // TABLA TIPOS DE GASTOS
    $tipos_gastos_obj = new stdClass();
    $tipos_gastos_obj->table_name = "tipos_gastos";
    $tipos_gastos_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $tipos_gastos_obj->delete_first = 1;
    $tipos_gastos_obj->url = "$url_server/sistema/tipos_gastos/function/export/$id_empresa/";

    // TABLA RUBROS
    $rubros_obj = new stdClass();
    $rubros_obj->table_name = "rubros";
    $rubros_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $rubros_obj->delete_first = 1;
    $rubros_obj->url = "$url_server/sistema/rubros/function/export/$id_empresa/";

    // TABLA MESAS
    $mesas_obj = new stdClass();
    $mesas_obj->table_name = "res_mesas";
    $mesas_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $mesas_obj->delete_first = 1;
    $mesas_obj->url = "$url_server/sistema/mesas/function/export/$id_empresa/";

    // TABLA SALONES
    $salones_obj = new stdClass();
    $salones_obj->table_name = "res_salones";
    $salones_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $salones_obj->delete_first = 1;
    $salones_obj->url = "$url_server/sistema/salones/function/export/$id_empresa/";


    // TABLA STOCK
    $stock_obj = new stdClass();
    $stock_obj->table_name = "stock";
    $stock_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $stock_obj->delete_first = 1;
    $id_sucursal_stock = ($id_empresa == 342) ? 0 : $id_sucursal;
    $stock_obj->url = "$url_server/sistema/stock/function/export/$id_empresa/$id_sucursal_stock/";

    // TABLA ALMACENES
    $almacenes_obj = new stdClass();
    $almacenes_obj->table_name = "almacenes";
    $almacenes_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $almacenes_obj->delete_first = 1;
    $almacenes_obj->url = "$url_server/sistema/almacenes/function/export/$id_empresa/";

    // TABLA EMPRESAS
    $empresas_obj = new stdClass();
    $empresas_obj->table_name = "empresas";
    $empresas_obj->where = array(
      array(
        "field"=>"id",
        "position"=>0,
      ),
    );
    $empresas_obj->delete_first = 1;
    $empresas_obj->url = "$url_server/sistema/empresas/function/export/$id_empresa/";


    $elementos = array(
      $articulos_obj,
      $articulos_variantes_obj,
      $articulos_propiedades_obj,
      $articulos_propiedades_opciones_obj,
      $articulos_atributos_obj,
      $tarjetas_obj,
      $tarjetas_intereses_obj,
      $tipos_gastos_obj,
      $rubros_obj,
      $usuarios_obj,
      $perfiles_obj,
      $permisos_obj
    );

    if ($id_empresa == 574 || $id_empresa == 1326) {
      $elementos[] = $vendedores_obj;
    }

    // FLAMINGO NO ACTUALIZA CLIENTES
    if ($id_empresa != 135) {
      $elementos[] = $clientes_obj;
    }

    // JAVIER NO ACTUALIZA CONFIGURACIONES
    if ($id_empresa != 121 && $id_empresa != 574) {
      $elementos[] = $fact_configuracion_obj;
    }

    // JAVIER e IMPORT SHOW actualizan etiquetas
    if ($id_empresa == 121 || $id_empresa == 356) {
      $elementos[] = $clientes_etiquetas_obj;
      $elementos[] = $clientes_etiquetas_relacion_obj;      
    }
    if ($id_empresa == 356) {
      $elementos[] = $departamentos_comerciales_obj;
    }

    if ($id_empresa == 249 || $id_empresa == 868) {
      
      // TABLA DE OFERTAS
      $ofertas_obj = new stdClass();
      $ofertas_obj->table_name = "reglas_ofertas";
      $ofertas_obj->where = array(
        array(
          "field"=>"id",
          "position"=>0,
        ),
      );
      $ofertas_obj->delete_first = 1;
      $ofertas_obj->url = "$url_server/sistema/reglas_ofertas/function/export/$id_empresa/0/";

      $ofertas_articulos_obj = new stdClass();
      $ofertas_articulos_obj->table_name = "reglas_ofertas_articulos";
      $ofertas_articulos_obj->where = array(
        array(
          "field"=>"id",
          "position"=>0,
        ),
      );
      $ofertas_articulos_obj->delete_first = 1;
      $ofertas_articulos_obj->url = "$url_server/sistema/reglas_ofertas/function/export/$id_empresa/1/";

      $ofertas_sucursales_obj = new stdClass();
      $ofertas_sucursales_obj->table_name = "reglas_ofertas_sucursales";
      $ofertas_sucursales_obj->where = array(
        array(
          "field"=>"id",
          "position"=>0,
        ),
      );
      $ofertas_sucursales_obj->delete_first = 1;
      $ofertas_sucursales_obj->url = "$url_server/sistema/reglas_ofertas/function/export/$id_empresa/2/";

      $elementos[] = $ofertas_obj;
      $elementos[] = $ofertas_articulos_obj;
      $elementos[] = $ofertas_sucursales_obj;
    }

    if ($id_empresa == 1021) {
      // TODO: ESTO SE PODIA PONER EN TODOS
      $elementos[] = $modulos_empresas_obj;
      $elementos[] = $mesas_obj;
      $elementos[] = $salones_obj;
    }

    if ($id_empresa == 342) {
      // En GASTROBER agregamos el stock y las sucursales
      // para que la caja pueda ver el stock de las sucursales en ese momento
      $elementos[] = $stock_obj;
      $elementos[] = $almacenes_obj;
      $elementos[] = $empresas_obj;
    }

    // Recorremos el array de elementos
    foreach($elementos as $elem) {

      if (isset($elem->create_first) && $elem->create_first == 1 && isset($elem->url_create)) {
        $c = curl_init($elem->url_create);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $html = curl_exec($c);
        if (!empty($html)) {
          // Borramos la tabla
          $this->db->query("DROP TABLE $elem->table_name ");
          // Y la volvemos a crear
          $this->db->query($html);
        }
      }

      // Obtenemos las sentencias SQL
      $c = curl_init($elem->url);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
      $html = curl_exec($c);
      file_put_contents("log_uploader.txt", $html);
      if (empty($html)) continue;
      $salida = gzinflate($html); // Descomprimimos

      // Si no obtuvimos ningun dato
      if ($salida == "0") {
        $elem->error = 1;
        continue;
      }

      // Si el string no comienza con CERO
      $comienzo = substr($salida, 0, 2);
      if ($comienzo != "id") {
        $elem->error = 1;
        // "ERROR: Las sentencias son incorrectas."
        continue;
      }

      if ($elem->delete_first == 1) {
        $sql = "DELETE FROM $elem->table_name WHERE id_empresa = $id_empresa ";
        if ($elem->table_name == "articulos_propiedades") $sql.= "OR id_empresa = 0 ";
        $q = $this->db->query($sql);
      }

      $array = explode("|||", $salida);
      $campos = array();
      $i=0;
      // Ejecutamos una a una cada sentencia
      foreach($array as $sentence) {
        if (empty($sentence)) continue;
        if ($i==0) {
          // La primer linea contiene todos los campos
          $campos = explode(";;;", $sentence);
          $i++;
          continue;
        }
        
        $datos = explode(";;;", $sentence);
        $sql = "SELECT * FROM ".$elem->table_name;
        if (isset($elem->where)) {
          $sql.= " WHERE ";
          $sql_where = "id_empresa = $id_empresa ";
          foreach($elem->where as $cond) {
            $sql_where.= " AND ".$cond["field"]." = ".$datos[$cond["position"]]." ";
          }
          $sql = $sql.$sql_where;
        }
        $q = $this->db->query($sql);
        if ($q->num_rows()>0) {
          $sql = create_update_sql(array(
            "fields"=>$campos,
            "table"=>$elem->table_name,
            "data"=>$datos,
            "where"=>$sql_where,
          ));        
        } else {
          $sql = create_insert_sql(array(
            "fields"=>$campos,
            "table"=>$elem->table_name,
            "data"=>$datos,
          ));        
        }
        $this->db->query($sql);
        $i++;
      }
    } // Fin FOR

    // Actualizamos el ultimo timestamp
    //$this->db->query("UPDATE fact_configuracion SET articulos_last_update = '$current_timestamp' WHERE id_empresa = $id_empresa ");

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"La informacion ha sido actualizada correctamente.",
    ));

  }

  function procesar_put() {

    // Si se imprime 0, ERROR
    // Si se imprime 1, OK

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('mysqli.allow_local_infile', 1);
    error_reporting(E_ALL);

    $input = file_get_contents("php://input");
    file_put_contents("put.txt", $input);
    $datos_json = json_decode($input);

    $id_empresa = $datos_json->id_empresa;

    if ($id_empresa == 1021) file_put_contents("log_nanna.txt", print_r($datos_json,TRUE)."\n", FILE_APPEND);
    else if ($id_empresa == 1399) file_put_contents("log_4soles.txt", print_r($datos_json,TRUE)."\n", FILE_APPEND);

    $punto_venta = $datos_json->punto_venta;
    $datos = isset($datos_json->datos) ? $datos_json->datos : "";
    if (empty($id_empresa)) { echo "NO HAY ID_EMPRESA"; exit(); }
    if (empty($punto_venta)) { echo "NO HAY PUNTO_VENTA"; exit(); }
    if (empty($datos)) { echo "DATOS VACIOS"; exit(); }

    $this->db->trans_start();

    foreach($datos as $file) {

      // Guardamos la informacion en un archivo temporal
      if ($id_empresa == 121) {
        $filename = "uploads/procesar/".$id_empresa."_".$punto_venta."_".$file->table."_".date("YmdHis").".sql";
      } else {
        $filename = "/home/ubuntu/data/sistema/uploads/procesar/".$id_empresa."_".$punto_venta."_".$file->table."_".date("YmdHis").".sql";
      }
      file_put_contents($filename,$file->data);

      if ($file->table == "caja_diaria_facturas") {

        $lineas = explode("\n", $file->data);
        if (!empty($lineas)) {
          foreach($lineas as $linea) {
            $campos = explode(";;;", $linea);
            if (empty($campos)) continue;
            $id_caja_diaria = trim($campos[0]);
            if (empty($id_caja_diaria)) continue;
            $ids = trim($campos[1]);
            if (empty($ids)) continue;

            // Actualizamos las facturas que corresponden con esa caja diaria
            $sql = "UPDATE facturas SET id_caja_diaria = $id_caja_diaria ";
            $sql.= "WHERE id_empresa = $id_empresa ";
            $sql.= "AND punto_venta = $punto_venta ";
            $sql.= "AND id IN ($ids) ";
            $this->db->query($sql);
          }
        }

      } else {

        // El IGNORE hace que si hay algun error, sea ignorado (los errores pueden ser DUPLICATE KEY)
        $sql = "LOAD DATA LOCAL INFILE '$filename' IGNORE INTO TABLE $file->table";
        if ($id_empresa == 121) {
          include("params.php");
          $conx = get_conex();
        } else {
          include("/home/ubuntu/data/sistema/params.php");  
          $conx = get_conex_local_data();
        }        
        mysqli_query($conx,$sql);
      }

      // Eliminamos el archivo temporal
      @unlink($filename);
    }

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $mensaje = $this->db->_error_message();
      echo "MENSAJE: ".$mensaje; exit();
      //file_put_contents("log_uploader.php", $mensaje, FILE_APPEND);
      echo "0"; exit();
    }

    // Actualizamos el STOCK de los productos
    $this->load->model("Stock_Model");
    $this->Stock_Model->procesar($id_empresa,$punto_venta);

    // Si llego hasta aca, esta todo bien
    echo "1"; 

  }

  function test() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_empresa = 1021;
    $punto_venta = 2;
    $this->load->model("Stock_Model");
    $this->Stock_Model->procesar($id_empresa,$punto_venta);
  }

  // Procesa los datos enviados por POST
  function procesar_post() {

    // Si se imprime 0, ERROR
    // Si se imprime 1, OK

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    file_put_contents("post.txt", print_r($_POST,TRUE));

    $id_empresa = parent::get_post("id_empresa",0);
    $punto_venta = parent::get_post("punto_venta",0);
    $datos = isset($_POST["datos"]) ? $_POST["datos"] : "";
    if (empty($id_empresa)) { echo "NO HAY ID_EMPRESA"; exit(); }
    if (empty($punto_venta)) { echo "NO HAY PUNTO_VENTA"; exit(); }
    file_put_contents("procesar_post.txt", $datos);

    if (empty($datos)) { echo "DATOS VACIOS"; exit(); }
    $datos = json_decode($datos);
    if (empty($datos)) { echo "DATOS VACIOS"; exit(); }

    $this->db->trans_start();

    foreach($datos as $file) {

      // Guardamos la informacion en un archivo temporal
      $filename = "uploads/procesar/".$id_empresa."_".$punto_venta."_".$file->table."_".date("YmdHis").".sql";
      file_put_contents($filename,$file->data);

      if ($file->table == "caja_diaria_facturas") {

        $lineas = explode("\n", $file->data);
        if (!empty($lineas)) {
          foreach($lineas as $linea) {
            $campos = explode(";;;", $linea);
            if (empty($campos)) continue;
            $id_caja_diaria = trim($campos[0]);
            if (empty($id_caja_diaria)) continue;
            $ids = trim($campos[1]);
            if (empty($ids)) continue;

            // Actualizamos las facturas que corresponden con esa caja diaria
            $sql = "UPDATE facturas SET id_caja_diaria = $id_caja_diaria ";
            $sql.= "WHERE id_empresa = $id_empresa ";
            $sql.= "AND punto_venta = $punto_venta ";
            $sql.= "AND id IN ($ids) ";
            $this->db->query($sql);
          }
        }

      } else {

        // El IGNORE hace que si hay algun error, sea ignorado (los errores pueden ser DUPLICATE KEY)
        $sql = "LOAD DATA LOCAL INFILE '$filename' IGNORE INTO TABLE $file->table";
        $this->db->query($sql);

      }

      // Eliminamos el archivo temporal
      @unlink($filename);
    }

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $mensaje = $this->db->_error_message();
      echo "MENSAJE: ".$mensaje; exit();
      //file_put_contents("log_uploader.php", $mensaje, FILE_APPEND);
      echo "0"; exit();
    }

    // Actualizamos el STOCK de los productos
    $this->load->model("Stock_Model");
    $this->Stock_Model->procesar($id_empresa,$punto_venta);

    // Si llego hasta aca, esta todo bien
    echo "1"; 
  }

	// Procesa los scripts de un determinado ZIP
	function procesar($folder="") {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

		if (empty($folder)) exit();

		// El nombre esta formado por [id_empresa]_[punto_venta]_[timestamp]
		$parts = explode("_",$folder);
		if (sizeof($parts) != 3) exit(); // Si o si tiene que estar compuesto asi
		$id_empresa = $parts[0];
		$punto_venta = $parts[1];
    $path = "/home/ubuntu/data/sistema/uploads/procesar/";

    $files = glob($path.$folder."/*.sql");
    if (empty($files)) {
      echo "0"; exit(); // No hay archivos que procesar
    }
		foreach ($files as $file) {
			$filename = substr($file,strrpos($file,"/")+1);
			$filename = str_replace(".sql", "", $filename);
			// El IGNORE hace que si hay algun error, sea ignorado (los errores pueden ser DUPLICATE KEY)
			$sql = "LOAD DATA LOCAL INFILE '$file' IGNORE INTO TABLE $filename";
			$this->db->query($sql);

			// Eliminamos el archivo
			//unlink($file);
		}

    // Si tenemos que subir una caja diaria
    $file_caja_diaria_facturas = $path.$folder."/caja_diaria_facturas.txt";
    if (file_exists($file_caja_diaria_facturas)) {
      $caja_diaria_facturas = file_get_contents($file_caja_diaria_facturas);
      $lineas = explode("\n", $caja_diaria_facturas);
      if (!empty($lineas)) {
        foreach($lineas as $linea) {
          $campos = explode(";;;", $linea);
          if (empty($campos)) continue;
          $id_caja_diaria = trim($campos[0]);
          if (empty($id_caja_diaria)) continue;
          $ids = trim($campos[1]);
          if (empty($ids)) continue;

          // Actualizamos las facturas que corresponden con esa caja diaria
          $sql = "UPDATE facturas SET id_caja_diaria = $id_caja_diaria ";
          $sql.= "WHERE id_empresa = $id_empresa ";
          $sql.= "AND punto_venta = $punto_venta ";
          $sql.= "AND id IN ($ids) ";
          $this->db->query($sql);

        }

        // Finalmente eliminamos el archivo
        //unlink($file_caja_diaria_facturas);
      }
    }


		// Eliminamos la carpeta creada
		//rmdir($path.$folder."/");

		// Actualizamos el STOCK de los productos
		$this->load->model("Stock_Model");
		$this->Stock_Model->procesar($id_empresa,$punto_venta);

		echo "1"; // Indicamos que todo esta bien
	}

  function procesar_stock() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(0);

    $id_empresa = 134;
    $punto_venta = 1;
    $this->load->model("Stock_Model");
    $this->Stock_Model->procesar($id_empresa,$punto_venta);
  }

}