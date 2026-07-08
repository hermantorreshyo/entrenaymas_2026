<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Publicidades extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Publicidad_Model', 'modelo');
  }

  function insertar_click($id_empresa,$id) {
    $sql = "INSERT INTO not_publicidades_clicks (id_empresa,id_publicidad,stamp) VALUES($id_empresa,$id,NOW())";
    $this->db->query($sql);
    echo json_encode(array());
  }

  function estadisticas() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->post("desde");
    $f_hasta = $this->input->post("hasta");
    $id_campania = $this->input->post("id_campania");
    $ids_piezas = $this->input->post("ids_piezas");
    $intervalo = "D";

    $grafico = array();
    $total = 0;

    $series = array();
    $desde = new DateTime(fecha_mysql($f_desde));
    $hasta = new DateTime(fecha_mysql($f_hasta));
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);
    $diff = $hasta->diff($desde)->format("%a");

    $res = array(); $cos = array();

    $sql_base = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS total ";
    $sql_base.= "FROM not_publicidades_impresiones F ";
    $sql_base.= " INNER JOIN pub_piezas P ON (F.id_empresa = P.id_empresa AND F.id_publicidad = P.id) ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($ids_piezas)) $sql_base.= "AND F.id_publicidad IN ($ids_piezas) ";
    if ($id_campania != 0) $sql_base.= "AND P.id_campania = $id_campania "; 
    /*
    $sql_base = "SELECT IF(SUM(cantidad) IS NULL,0,SUM(cantidad)) AS total ";
    $sql_base.= "FROM pub_resumen_impresiones F ";
    $sql_base.= " INNER JOIN pub_piezas P ON (F.id_empresa = P.id_empresa AND F.id_publicidad = P.id) ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($ids_piezas)) $sql_base.= "AND F.id_publicidad IN ($ids_piezas) ";
    if ($id_campania != 0) $sql_base.= "AND P.id_campania = $id_campania ";  
    */

    // Recorremos cada dia del rango
    foreach($range as $fecha) {
      $sql = $sql_base;
      $sql.= "AND DATE_FORMAT(F.stamp,'%Y-%m-%d') = '" .$fecha->format("Y-m-d")."' ";
      //$sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
      $q = $this->db->query($sql);
      $rr = $q->row();
      $t = (float)$rr->total;
      $total += $t;
      $res[] = $t;
    }

    // Total de clicks
    $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS total ";
    $sql.= "FROM not_publicidades_clicks F ";
    $sql.= " INNER JOIN pub_piezas P ON (F.id_empresa = P.id_empresa AND F.id_publicidad = P.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($ids_piezas)) $sql.= "AND F.id_publicidad IN ($ids_piezas) ";
    if ($id_campania != 0) $sql.= "AND P.id_campania = $id_campania "; 
    $sql.= "AND F.stamp >= '" .$desde->format("Y-m-d 00:00:00")."' ";
    $sql.= "AND F.stamp < '" .$hasta->format("Y-m-d 00:00:00")."' ";
    $q = $this->db->query($sql);
    $row = $q->row();
    $total_clicks = $row->total;

    // Promedio de visualizaciones por dia
    $promedio_por_dia = ($diff <= 0) ? 0 : (float) $total / $diff;

    // Promedio de clicks por dia
    $promedio_clicks_por_dia = ($diff <= 0) ? 0 : (float) $total_clicks / $diff;

    // Agregamos la serie
    $series[] = array(
      "name"=>"Impresiones",
      "data"=>$res,
    );
    $grafico[] = array(
      "series"=>$series,
      "desde"=>$desde->format("d/m/Y"),
      "hasta"=>$hasta->format("d/m/Y"),
      "intervalo"=>$intervalo,
    ); 

    $salida = array(
      "grafico"=>$grafico,
      "total"=>$total,
      "total_clicks"=>$total_clicks,
      "promedio_por_dia"=>$promedio_por_dia,
      "promedio_clicks_por_dia"=>$promedio_clicks_por_dia,
      "fecha_desde"=>str_replace("-","/",$f_desde),
      "fecha_hasta"=>str_replace("-","/",$f_hasta),
    );

    if ($id_empresa == 256 || $id_empresa == 257) {

      $this->load->model("Web_Configuracion_Model");
      $conf = $this->Web_Configuracion_Model->get($id_empresa);

      if (!empty($conf->view_id)) {

        set_include_path(get_include_path().PATH_SEPARATOR.APPPATH.'libraries/Google/');
        require APPPATH.'libraries/Google/Client.php';
        require APPPATH.'libraries/Google/Service/Analytics.php';

        $client_id = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q.apps.googleusercontent.com'; //Client ID
        $service_account_name = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q@developer.gserviceaccount.com'; //Email Address 
        $key_file_location = APPPATH.'libraries/Google/key.p12';
        
        $client = new Google_Client();
        $client->setApplicationName("Client_Library_Examples");
        $service = new Google_Service_Analytics($client);
        
        if (isset($_SESSION['service_token'])) {
          $client->setAccessToken($_SESSION['service_token']);
        }
        $key = file_get_contents($key_file_location);
        $cred = new Google_Auth_AssertionCredentials(
          $service_account_name,
          array('https://www.googleapis.com/auth/analytics.readonly'),
          $key
        );
        $client->setAssertionCredentials($cred);
        if($client->getAuth()->isAccessTokenExpired()) {
          $client->getAuth()->refreshTokenWithAssertion($cred);
        }
        $_SESSION['service_token'] = $client->getAccessToken();
        
        $view_id = "ga:".$conf->view_id;
        $fecha_desde = $desde->format("Y-m-d");
        $fecha_hasta = $hasta->format("Y-m-d");
        
        try {

          // PAGINAS VISTAS
          $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:pageviews');
          if (count($results->getRows()) > 0) {
            $rows = $results->getRows();
            $salida["paginas_vistas"] = $rows[0][0];
          }
          
          // PAISES
          $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:pageviews',array(
            "sort" => "-ga:pageviews",
            "max-results" => 9,
            "dimensions" => "ga:country"
          ));
          if (count($results->getRows()) > 0) {
            foreach ($results->getRows() as $r) {
              $o = new stdClass();
              $o->nombre = ($r[0] != "(not set)") ? $r[0] : "No definida";
              $o->cantidad = $r[1];
              if (!empty($salida["paginas_vistas"])) {
                $o->porcentaje = number_format(($o->cantidad / $salida["paginas_vistas"])*100,0);
              } else {
                $o->porcentaje = 0;
              }
              $salida["ciudades"][] = $o;
            }
          }          
          
          // ORIGENES
          $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:pageviews',array(
            "sort" => "-ga:pageviews",
            "max-results" => 9,
            "dimensions" => "ga:source"
          ));
          if (count($results->getRows()) > 0) {
            foreach ($results->getRows() as $r) {
              $o = new stdClass();
              $o->nombre = ($r[0] != "(direct)") ? $r[0] : "Directo";
              $o->cantidad = $r[1];
              if (!empty($salida["paginas_vistas"])) {
                $o->porcentaje = number_format(($o->cantidad / $salida["paginas_vistas"])*100,0);
              } else {
                $o->porcentaje = 0;
              }
              $salida["fuentes"][] = $o;
            }
          }
        } catch(Exception $e) {
          $salida["error"] = $e;
        }  
      } else {
        $salida["error"] = "No tiene configurado el VIEW ID";
      }


      if ($id_campania != 0) {
        $this->load->model("Campania_Model");
        $campania = $this->Campania_Model->get($id_campania);
        $this->load->model("Entrada_Model");
        $paginas = $this->Entrada_Model->buscar(array(
          "id_cliente"=>$campania->id_cliente,
          "order"=>"A.vistos DESC",
        ));
        $salida["paginas"] = $paginas["results"];
      }

    }

    echo json_encode($salida);
  }

	
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/images/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }
	
  function save_file() {
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    $filename = filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/images/$filename";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
    echo json_encode(array(
      "path"=>$path,
      "error"=>0,
    ));
  }	
  
  function duplicar($id) {
    
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");
    
    $publicidad = $this->modelo->get($id);
    if ($publicidad === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la publicidad con ID: $id",
      ));
      return;
    }
    
    $images = $publicidad->images;
    $this->remove_properties($publicidad);
    $publicidad->id = 0;
		
    $insert_id = $this->modelo->insert($publicidad);
    
    // Actualizamos las relaciones
    echo json_encode(array(
      "id"=>$insert_id
    ));
  }
  
  private function remove_properties($array) {
    unset($array->tipo_publicidad);
    unset($array->categoria);
		unset($array->cliente);
		unset($array->vendedor);
    unset($array->images);   
    unset($array->dias_vencimiento);
    unset($array->desde);
    unset($array->hasta);
  }
  
  function update($id) {
    
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
		
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
  	$array->valida_desde = fecha_mysql($array->valida_desde);
		$array->valida_hasta = fecha_mysql($array->valida_hasta);
		
    $images = $array->images;
    $this->remove_properties($array);
    $this->modelo->save($array);
    
    // Guardamos las imagenes
    $this->db->query("DELETE FROM not_publicidades_images WHERE id_publicidad = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO not_publicidades_images (id_empresa,id_publicidad,path,orden) VALUES($id_empresa,$id,'$im',$k)");
      $k++;
    }
		
		// Copiamos el estado de la publicidad al estado de la guia asociada
		$sql = "UPDATE clasificados SET activo = $array->activo WHERE id_publicidad = $id AND id_empresa = $id_empresa";
		$this->db->query($sql);

    // Llamamos al cacheador
    if ($id_empresa == 70) {
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://www.quepensaschacabuco.com/sistema/application/cronjobs/cachear_quepensas.php");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_exec($ch);
    }    
    
    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);    
  }
  
  
  function insert() {
    
    $this->load->helper("file_helper");
  	$array = $this->parse_put();
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
		
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
  	$array->valida_desde = fecha_mysql($array->valida_desde);
		$array->valida_hasta = fecha_mysql($array->valida_hasta);
		
    $images = $array->images;
    $this->remove_properties($array);
    
    // Insertamos el publicidad
    $insert_id = $this->modelo->save($array);
    
    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO not_publicidades_images (id_empresa,id_publicidad,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
      $k++;
    }

    // Llamamos al cacheador
    if ($id_empresa == 70) {
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://www.quepensaschacabuco.com/sistema/application/cronjobs/cachear_quepensas.php");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_exec($ch);
    }    
    
    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
    );
    echo json_encode($salida);    
  }
  
  /**
   *  Obtenemos los datos de un publicidad en particular
   */
  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {
      $sql = "SELECT A.*, ";
      $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS categoria ";
      $sql.= "FROM not_publicidades A ";
      $sql.= "LEFT JOIN not_publicidades_categorias TE ON (A.id_categoria = TE.id) ";
      $sql.= "WHERE A.activo = 1 AND A.id_empresa = '$id_empresa' ";
      $sql.= "ORDER BY A.nombre ASC ";
      $q = $this->db->query($sql);
      $result = $q->result();
      echo json_encode(array(
        "results"=>$result,
        "total"=>sizeof($result)
      ));
    } else {
      $publicidad = $this->modelo->get($id);
      echo json_encode($publicidad);
    }
    
  }
  
  
  /**
   *  Muestra todos los publicidades filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver() {
		$filter = $this->input->get("filter");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $id_categoria = ($this->input->get("id_categoria") === FALSE) ? 0 : $this->input->get("id_categoria");
    $activo = ($this->input->get("activo") === FALSE) ? -1 : $this->input->get("activo");
    $id_cliente = ($this->input->get("id_cliente") === FALSE) ? 0 : $this->input->get("id_cliente");
    $id_vendedor = ($this->input->get("id_vendedor") === FALSE) ? 0 : $this->input->get("id_vendedor");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "id_categoria"=>$id_categoria,
      "id_cliente"=>$id_cliente,
      "id_vendedor"=>$id_vendedor,
      "activo"=>$activo,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }
		
	function impresiones() {
		$filter = $this->input->get("filter");
		$desde = $this->input->get("desde");
		$hasta = $this->input->get("hasta");
		$id_categoria = $this->input->get("id_categoria");
		$limit = $this->input->get("limit");
		$offset = $this->input->get("offset");
		$order_by = $this->input->get("order_by");
		$order = $this->input->get("order");
		if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
		else $order = "";
	
		$conf = array(
			"filter"=>$filter,
			"desde"=>$desde,
			"hasta"=>$hasta,
			"limit"=>$limit,
			"offset"=>$offset,
			"order"=>$order,
			"id_categoria"=>$id_categoria,
		);
		$r = $this->modelo->impresiones($conf);
		echo json_encode($r);				
	}
  
}
