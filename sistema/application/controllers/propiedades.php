<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Propiedades extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Propiedad_Model', 'modelo');
  }

  function crear_video() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    $id_empresa = parent::get_empresa();
    $id = parent::get_get("id");
    $propiedad = $this->modelo->get($id,array(
      "id_empresa"=>$id_empresa,
    ));

    // Borramos todo lo que haya en la carpeta temporal de la empresa
    $this->load->helper("file_helper");
    require APPPATH.'libraries/SimpleImage.php';
    $dir = "/home/ubuntu/data/sistema/uploads/$id_empresa/temporal/";
    empty_directory($dir);

    // El primer paso, tenemos que normalizar todas las fotos a 800x600
    $images = array();
    $i=0;
    foreach($propiedad->images as $img) {
      if (strpos($img,"http://")===FALSE) {
        $s = explode("/", $img);
        $dest = end($s);
        $filename = $dir.$dest;
        copy($img, $filename);

        // Redimensionamos las fotos a 800x600
        $simple = new SimpleImage();
        $simple->load($filename);
        $simple->resize(800,600);
        $simple->save($filename);
        $images[] = $filename;
        $i++;
      }
    }

    // Segundo paso, creamos el comando que vamos a ejecutar
    $cmd = 'ffmpeg -y ';
    foreach($images as $img) $cmd.= '-loop 1 -i '.$img.' ';
    $cmd.= '-filter_complex "';
    for($i=0;$i<sizeof($images);$i++) {
      $cmd.= '['.$i.':v]trim=duration=6,fade=t=in:st=0:d=1,fade=t=out:st=5:d=1,setsar=1:1[v'.$i.']; ';
    }
    for($i=0;$i<sizeof($images);$i++) {
      $cmd.= "[v".$i."]";
    }
    $cmd.= ' concat=n='.sizeof($images).':v=1:a=0,setsar=1:1[v]" -map "[v]" -aspect 16:9 -r 24 '.$dir."slide.mp4";
    echo $cmd."\n";
    shell_exec($cmd);
    
    // Creamos un archivo de lista con 100 veces lo mismo
    $s = "";
    for($i=0;$i<100;$i++) $s.= "file '".$dir."slide.mp4' \n";
    file_put_contents($dir."list.txt", $s);

    $cmd = "ffmpeg -y ";
    $cmd.= "-f concat -i ".$dir."list.txt ";
    $cmd.= '-i /home/ubuntu/data/sistema/uploads/'.$id_empresa.'/brand.png -filter_complex "overlay=0:0" ';
    $cmd.= '-i /home/ubuntu/data/sistema/uploads/'.$id_empresa.'/test.mp3 -c:a copy ';
    $cmd.= '-shortest '.$dir.'salida.mp4 ';
    echo $cmd."\n";
    shell_exec($cmd);
    
    echo "TERMINO";
  }

  function invitar_colega() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = parent::get_post("id_empresa",parent::get_empresa());
    $email = parent::get_post("email");
    $inmobiliaria = parent::get_post("inmobiliaria");
    $this->load->model("Empresa_Model");
    $this->load->model("Email_Template_Model");
    $empresa = $this->Empresa_Model->get_min($id_empresa);
    $empresa->nombre = ucwords(strtolower($empresa->nombre));
    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $template = $this->Email_Template_Model->get_by_key("invitacion",118);
    $template->nombre = str_replace("{{nombre}}", $empresa->nombre, $template->nombre);
    $body = str_replace("{{nombre}}", $empresa->nombre, $template->texto);
    $body = str_replace("{{inmobiliaria}}", $inmobiliaria, $body);
    $bcc_array = array("basile.matias99@gmail.com","misticastudio@gmail.com");
    mandrill_send(array(
      "to"=>$email,
      "subject"=>$template->nombre,
      "from"=>MAIL_FROM_ADDRESS,
      "from_name"=>"Inmovar",
      "body"=>$body,
      "bcc"=>$bcc_array,
    ));  
    echo json_encode(array("error"=>0));
  }

  function exportar_calendario($id_empresa) {

    // Dependiendo del dominio es el ID_EMPRESA
    /*
    $this->load->model("Empresa_Model");
    $dominio = strtolower($_SERVER["HTTP_HOST"]);
    $empresa = $this->Empresa_Model->get_empresa_by_dominio($dominio);
    if ($empresa === FALSE) exit();
    $id_empresa = $empresa->id;
    */
    require_once APPPATH.'libraries/icalendar/zapcallib.php';

    require_once APPPATH.'libraries/icalendar/zapcallib.php';
    $sql = "SELECT * FROM inm_propiedades_reservas WHERE id_empresa = $id_empresa AND id_cliente != 0 AND fecha_desde != '0000-00-00' AND fecha_hasta != '0000-00-00' ";
    $q = $this->db->query($sql);
    $icalobj = new ZCiCal();
    foreach($q->result() as $r) {
      $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
      $eventobj->addNode(new ZCiCalDataNode("DTSTART:".ZCiCal::fromSqlDateTime($r->fecha_desde." 08:00:00")));
      $eventobj->addNode(new ZCiCalDataNode("DTEND:".ZCiCal::fromSqlDateTime($r->fecha_hasta." 08:00:00")));
      $eventobj->addNode(new ZCiCalDataNode("UID:".$r->id_empresa."-".$r->id."@varcreative.com"));
      $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
    }
    echo $icalobj->export();
  }

  function sincronizar_calendario() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id = parent::get_get("id",0);
    $id_empresa = parent::get_get("id_empresa",0);
    // Tenemos que sincronizar una propiedad en particular
    if ($id_empresa != 0 && $id != 0) {
      $propiedad = $this->modelo->get($id,array(
        "id_empresa"=>$id_empresa
      ));
      if (empty($propiedad->links_ical)) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"No hay links de calendarios definidos."
        ));
        exit();
      }
      $lineas = explode("\n", $propiedad->links_ical);
      foreach($lineas as $linea) {
        if (empty($linea)) continue;
        $this->modelo->sincronizar_calendario(array(
          "id_propiedad"=>$id,
          "link"=>$linea,
          "id_empresa"=>$id_empresa,
        ));
      }
      echo json_encode(array("error"=>0));

    // Tenemos que sincronizar todas las propiedades
    } else if ($id_empresa == 0 && $id == 0) {
      $sql = "SELECT id, id_empresa, links_ical FROM inm_propiedades ";
      $sql.= "WHERE links_ical != '' ";
      $q = $this->db->query($sql);
      foreach($q->result() as $propiedad) {
        $lineas = explode("\n", $propiedad->links_ical);
        foreach($lineas as $linea) {
          if (empty($linea)) continue;
          $this->modelo->sincronizar_calendario(array(
            "id_propiedad"=>$propiedad->id,
            "link"=>$linea,
            "id_empresa"=>$propiedad->id_empresa,
          ));
        }
        echo "Sincronizo [$linea] ID: [$propiedad->id] <br/>";
      }
    }
  }

  function actualizar_parametros() {
    $sql = "SELECT * FROM com_localidades ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $nombre = ucwords(strtolower($r->nombre));
      $this->db->query("UPDATE com_localidades SET nombre = \"$nombre\" WHERE id = $r->id ");
    }
    $sql = "SELECT * FROM com_departamentos ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $nombre = ucwords(strtolower($r->nombre));
      $this->db->query("UPDATE com_departamentos SET nombre = \"$nombre\" WHERE id = $r->id ");
    }
    $sql = "SELECT * FROM com_provincias ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $nombre = ucwords(strtolower($r->nombre));
      $this->db->query("UPDATE com_provincias SET nombre = \"$nombre\" WHERE id = $r->id ");
    }
    echo "TERMINO";
  }

  function obtenerCiudad($lat,$lon) {
    return 'https://nominatim.openstreetmap.org/reverse?format=json&lat='.$lat.'&lon='.$lon;
    /*
    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $archivo_web = curl_exec($c);
    var_dump($archivo_web); exit();
    curl_close($c);
    $archivo = json_decode(mb_convert_encoding($archivo_web, 'ISO-8859-1', 'UTF-8'));
    return $archivo->address->city;
    */
  }

  function revisar_localidades() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $propiedades = $this->modelo->buscar(array(
      "id_empresa"=>108, // YACOUB
      "id_tipo_estado"=>1, // Estado Activo
      "activo"=>1,
      "filtro_inmobusquedas"=>3, // Todas (activas y pendientes)
      "buscar_imagenes"=>1,
      "offset"=>999999,
    ));
    echo "<table border=1>";
    foreach($propiedades["results"] as $row) {
      $ciudad = $this->obtenerCiudad($row->latitud,$row->longitud);
      echo "<tr>";
      echo "<td>".$row->codigo."</td>";
      echo "<td>".$row->nombre."</td>";
      echo "<td>".$row->latitud."</td>";
      echo "<td>".$row->longitud."</td>";
      echo "<td>CIUDAD CARGADA: ".$row->localidad."</td>";
      echo "<td>".$ciudad."</td>";
      echo "</tr>";
    }
    echo "</table>";
  }


  function compartir_red_multiple() {
    $id_empresa = parent::get_empresa();
    $ids = parent::get_post("ids","");
    $compartir = parent::get_post("compartir",0);
    $sql = "UPDATE inm_propiedades SET compartida = '$compartir' ";
    $sql.= "WHERE id_empresa = $id_empresa AND id IN ($ids) ";
    $this->db->query($sql);
    echo json_encode(array("error"=>0));
  }  


  function buscar_etiqueta() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM inm_etiquetas ";
    $sql.= "WHERE nombre LIKE '%$nombre%' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->nombre;
      $rr->text = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }   

  function upload_images($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_images(array(
      "id_empresa"=>$id_empresa,
      "clave_width"=>"propiedad_galeria_image_width",
      "clave_height"=>"propiedad_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/propiedades/",
    ));
  }

	function compartir($id) {
			
		$propiedad = $this->modelo->get($id);
		$descripcion = $propiedad->localidad;
		$link = parent::mklink($propiedad->link);
		$titulo = $propiedad->nombre;
		$imagen = parent::mklink("uploads/$propiedad->path");
		$this->load->view("compartir_fb",array(
			"link"=>$link,
			"titulo"=>$titulo,
			"path"=>$imagen,
			"descripcion"=>$descripcion,
		));
	}

  function save_file() {
    /*
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    */
    $this->load->helper("file_helper");
    $this->load->helper("imagen_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    // Primero copiamos el archivo
    $filename = filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/propiedades/";
    $filename = rename_if_exists($path,$filename);
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path.$filename);
    // Si es una imagen, lo redimensionamos
    if (is_image($filename)) {
      resize(array(
        "dir"=>$path,
        "filename"=>$filename,
      ));
    }
    echo json_encode(array(
      "path"=>$path.$filename,
      "error"=>0,
    ));
  }   


  function qr($id = 0) {
    $config = array();

    // Empresa
    $id_empresa = $this->input->get("id_empresa");
    if ($id_empresa !== FALSE) $config["id_empresa"] = $id_empresa;

    // Formato de salida
    $output = ($this->input->get("output") !== FALSE) ? strtolower($this->input->get("output")) : "png";
    
    // Obtenemos la propiedad
    $propiedad = $this->modelo->get($id,$config);
    if ($propiedad === FALSE) {
      echo "No existe la propiedad con ID $id."; exit();
    }

    $link = "PHP QR Code :)";
    require APPPATH.'libraries/phpqrcode/qrlib.php';

    // Dependiendo el formato de salida
    if ($output == "png") {
      header('Content-Type: image/png');
      QRcode::png($link);
    //} else if ($output == "svg") {
      //header('Content-type: image/svg+xml');
      //echo QRcode::svg($link);
    }
  }
    
	/**
	 * ESTA FUNCION LA USAN LOS CLIENTES DE LA EMPRESA PARA VER LAS FICHAS
	 */
	function ficha($hash = "") {

		$this->load->helper("fecha_helper");
		$propiedad = $this->modelo->get_by_hash($hash);
		
		$this->load->model("Empresa_Model");
    $dominio = strtolower($_SERVER["HTTP_HOST"]);
    $dominio = str_replace("www.", "", $dominio);
    if ($dominio == "varcreative.com") {
      $id_empresa = $propiedad->id_empresa;
    } else {
      $id_empresa_dominio = $this->Empresa_Model->get_id_empresa_by_dominio($dominio);
      $id_empresa = ($id_empresa_dominio != 0) ? $id_empresa_dominio : $propiedad->id_empresa;      
    }
		$empresa = $this->Empresa_Model->get($id_empresa);
		
		$header = $this->load->view("reports/propiedad/header",null,true);
		
		$this->load->model("Web_Configuracion_Model");
		$web_conf = $this->Web_Configuracion_Model->get($id_empresa);
		$empresa = (object) array_merge((array) $empresa, (array) $web_conf);
		
		//$tpl = $empresa->config["template_propiedad"];
  	$tpl = "modelo1";
		
		// Indicamos que el cliente vio la factura
		//$this->db->query("UPDATE facturas SET visto = visto + 1 WHERE id = $factura->id");
		
		$datos = array(
			"propiedad"=>$propiedad,
			"empresa"=>$empresa,
			"header"=>$header,
			"folder"=>"/application/views/reports/propiedad/$tpl/blue",
		);
		$this->load->view("reports/propiedad/$tpl/ficha.php",$datos);
		
	}

  function ver_ficha($id_empresa,$id,$id_empresa_vista = 0) {

    $this->load->helper("fecha_helper");
    $propiedad = $this->modelo->get($id,array(
      "id_empresa"=>$id_empresa,
    ));
    
    $this->load->model("Empresa_Model");
    if ($id_empresa_vista == 0) $id_empresa_vista = $id_empresa;
    $empresa = $this->Empresa_Model->get($id_empresa_vista);
    
    $header = $this->load->view("reports/propiedad/header",null,true);
    
    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($id_empresa_vista);
    $empresa = (object) array_merge((array) $empresa, (array) $web_conf);
    
    $tpl = "modelo1";    
    $datos = array(
      "propiedad"=>$propiedad,
      "empresa"=>$empresa,
      "header"=>$header,
      "folder"=>"/application/views/reports/propiedad/$tpl/blue",
    );
    $this->load->view("reports/propiedad/$tpl/ficha.php",$datos);
  }
    
  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/propiedades/";
    $filename = $this->input->post("file");
    $res = parent::save_image($dir,$filename);

    if ($this->input->post("thumbnail_width") !== FALSE) {
      $resp = json_decode($res);
      $filename = str_replace($dir, "", $resp->path);
      $thumbnail_width = $this->input->post("thumbnail_width");
      $thumbnail_height = $this->input->post("thumbnail_height");
      if ($thumbnail_width != 0 && $thumbnail_height != 0) {
        parent::thumbnails(array(
          "dir"=>$dir,
          "preffix"=>"thumb_",
          "filename"=>$filename,
          "thumbnail_width"=>$thumbnail_width,
          "thumbnail_height"=>$thumbnail_height,                
          "tipo_redimension"=>2,
        ));                
      }
    }        
    echo $res;
  }
    
  function next() {
    $codigo = $this->modelo->next();
    echo json_encode(array(
      "codigo"=>$codigo
    ));
  }
    
  function duplicar($id) {
      
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");

    $id_empresa = parent::get_empresa();
    $control_plan = $this->modelo->controlar_plan($id_empresa);
    if ($control_plan !== TRUE) {
      echo json_encode($control_plan);
      exit();
    }

    $propiedad = $this->modelo->get($id);
    if ($propiedad === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la propiedad con ID: $id",
      ));
      return;
    }

    $relacionados = $propiedad->relacionados;
    $images = $propiedad->images;
    $images_meli = $propiedad->images_meli;
    $planos = $propiedad->planos;
    $departamentos = $propiedad->departamentos;
    $propiedad->valido_hasta = fecha_mysql($propiedad->valido_hasta);
    $temporada = (isset($array->temporada)) ? $array->temporada : array();
    $impuestos = (isset($array->impuestos)) ? $array->impuestos : array();

    $this->remove_properties($propiedad);
    $propiedad->fecha_publicacion = (!empty($propiedad->fecha_publicacion)) ? fecha_mysql($propiedad->fecha_publicacion) : date("Y-m-d");
    
    $propiedad->id = 0;
    $propiedad->codigo = $this->modelo->next(); // Ponemos el siguiente codigo
    $propiedad->link = ""; // Como el link tiene el ID, se tiene que generar de vuelta
    
    $insert_id = $this->modelo->insert($propiedad);
	  $hash = md5($insert_id);
      
    // Actualizamos el link
    $propiedad->link = "propiedad/".filename($propiedad->nombre,"-",0)."-".$insert_id."/";
    $this->db->query("UPDATE inm_propiedades SET link = '$propiedad->link', hash = '$hash' WHERE id = $insert_id AND id_empresa = $propiedad->id_empresa");
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($relacionados as $p) {
      $this->db->insert("inm_propiedades_relacionados",array(
        "id_propiedad"=>$insert_id,
        "id_relacion"=>$p->id,
        "id_rubro"=>0,
        "destacado"=>$p->destacado,
        "orden"=>$i,
      ));
      $i++;
    }

    $i=1;
    foreach($departamentos as $p) {
      $this->db->insert("inm_departamentos",array(
        "id_propiedad"=>$insert_id,
        "nombre"=>$p->nombre,
        "texto"=>$p->texto,
        "piso"=>$p->piso,
        "id_empresa"=>$p->id_empresa,
        "disponible"=>$p->disponible,
        "orden"=>$p->orden,
      ));
      $id_departamento = $this->db->insert_id();
      // Insertamos las fotos del departamento
      $j=0;
      foreach($p->images_dptos as $f) {
        $this->db->insert("inm_departamentos_images",array(
          "id_propiedad"=>$insert_id,
          "id_departamento"=>$id_departamento,
          "path"=>$f,
          "id_empresa"=>$p->id_empresa,
          "orden"=>$j,
        ));
        $j++;
      }
      $i++;
    }

    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO inm_propiedades_images (plano,id_empresa,id_propiedad,path,orden) VALUES(0,$propiedad->id_empresa,$insert_id,'$im',$k)");
      $k++;
    }
    $k=0;
    foreach($images_meli as $im) {
      $sql = "INSERT INTO inm_propiedades_images_meli (id_empresa,id_propiedad,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$propiedad->id_empresa,$insert_id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }
    
    // Guardamos los planos
    $k=0;
    foreach($planos as $im) {
      $this->db->query("INSERT INTO inm_propiedades_images (plano,id_empresa,id_propiedad,path,orden) VALUES(1,$propiedad->id_empresa,$insert_id,'$im',$k)");
      $k++;
    }

    // Guardamos los precios
    $this->db->query("DELETE FROM inm_propiedades_precios WHERE id_propiedad = $insert_id AND id_empresa = $propiedad->id_empresa");
    foreach($temporada as $im) {
      $desde = fecha_mysql($im->fecha_desde);
      $hasta = fecha_mysql($im->fecha_hasta);
      $this->db->query("INSERT INTO inm_propiedades_precios (id_empresa,id_propiedad,promocion,fecha_desde,fecha_hasta,precio_finde,precio_semana,precio_mes,nombre,minimo_dias_reserva,precio) VALUES($propiedad->id_empresa,$insert_id,0,'$desde','$hasta',$im->precio_finde,$im->precio_semana,$im->precio_mes,'$im->nombre',$im->minimo_dias_reserva,$im->precio)");
    }

    // Guardamos los impuestos
    $this->db->query("DELETE FROM inm_propiedades_impuestos WHERE id_propiedad = $insert_id AND id_empresa = $propiedad->id_empresa");
    $k=0;
    foreach($impuestos as $im) {
      $this->db->query("INSERT INTO inm_propiedades_impuestos (id_empresa,id_propiedad,nombre,tipo,monto,orden) VALUES($propiedad->id_empresa,$insert_id,'$im->nombre','$im->tipo','$im->monto',$k)");
      $k++;
    }
    
    // Actualizamos las relaciones
    echo json_encode(array(
      "id"=>$insert_id
    ));
  }
    
  private function remove_properties($array) {
    unset($array->tipo_operacion);
    unset($array->tipo_estado);
    unset($array->tipo_inmueble);
    unset($array->localidad);
    unset($array->provincia);
    unset($array->propietario);
    unset($array->images);        
    unset($array->images_meli);
    unset($array->planos);  
    unset($array->departamentos);  
    unset($array->relacionados);
    unset($array->usuario);
    unset($array->usuario_email);
    unset($array->etiquetas);
    //unset($array->rubros_relacionados);        
  }
    
  function update($id) {
      
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $this->load->helper("fecha_helper");
    $array = $this->parse_put();
    
    // Ponemos excepciones para el control de session
    if (isset($array->id_empresa) && $array->id_empresa == 263) {
      $id_empresa = 263;
    } else {
      $id_empresa = parent::get_empresa();  
    }
    $array->id_empresa = $id_empresa;
    
    // Eliminamos todo lo que no se persiste
    $images = (isset($array->images)) ? $array->images : array();
    $images_meli = (isset($array->images_meli)) ? $array->images_meli : array();
    $planos = (isset($array->planos)) ? $array->planos : array();
    $departamentos = (isset($array->departamentos)) ? $array->departamentos : array();
    $productos_relacionados = (isset($array->relacionados)) ? $array->relacionados : array();
    $etiquetas = (isset($array->etiquetas)) ? $array->etiquetas : array();
    $array->valido_hasta = fecha_mysql($array->valido_hasta);
    $temporada = (isset($array->temporada)) ? $array->temporada : array();
    $impuestos = (isset($array->impuestos)) ? $array->impuestos : array();

    $id_meli = isset($array->id_meli) ? $array->id_meli : "";
    $permalink = isset($array->permalink) ? $array->permalink : "";
    $fecha_publicacion = isset($array->fecha_publicacion) ? $array->fecha_publicacion : "";
    $activo_meli = isset($array->activo_meli) ? $array->activo_meli : 0;
    $titulo_meli = isset($array->titulo_meli) ? $array->titulo_meli : "";
    $texto_meli = isset($array->texto_meli) ? $array->texto_meli : "";
    $categoria_meli = isset($array->categoria_meli) ? $array->categoria_meli : "";
    $precio_meli = isset($array->precio_final) ? $array->precio_final : 0;
    $list_type_id = isset($array->list_type_id) ? $array->list_type_id : "";
    $ciudad_meli = isset($array->ciudad_meli) ? $array->ciudad_meli : "";
    $status = isset($array->status) ? $array->status : "";    

    $this->remove_properties($array);
    $array->fecha_publicacion = (!empty($array->fecha_publicacion)) ? fecha_mysql($array->fecha_publicacion) : date("Y-m-d");
    
    $array->codigo = trim($array->codigo);
    if (!empty($array->codigo)) {
      if ($this->modelo->existe_codigo($array->codigo,$id)) {
        $salida = array(
          "error"=>1,
          "mensaje"=>"El codigo '$array->codigo' ya existe."
        );
        echo json_encode($salida);
        return;
      }      
    }

    // Excepciones
    if ($id_empresa == 70 && sizeof($images)>0) {
      $array->path = $images[0];
    }

    if (is_null($array->id_propietario)) $array->id_propietario = 0;
        
    // Actualizamos los datos del propiedad
    $this->modelo->save($array);

    // Actualizamos el link
    $array->hash = md5($id);
    $array->link = "propiedad/".filename($array->nombre,"-",0)."-".$id."/";
    $this->db->query("UPDATE inm_propiedades SET link = '$array->link', hash='$array->hash' WHERE id = $id AND id_empresa = $id_empresa");

    // CONTROLAMOS SI ESTA PUBLICADO EN MERCADOLIBRE
    $array->categoria_meli = $categoria_meli;
    $array->id_meli = $id_meli;
    $array->permalink = $permalink;
    $array->fecha_publicacion = $fecha_publicacion;
    $array->activo_meli = $activo_meli;
    $array->titulo_meli = $titulo_meli;
    $array->texto_meli = $texto_meli;
    $array->categoria_meli = $categoria_meli;
    $array->precio_meli = $precio_meli;
    $array->list_type_id = $list_type_id;
    $array->ciudad_meli = $ciudad_meli;
    $array->status = $status;
    $publicado = $this->modelo->update_meli($array);
    if ($publicado) $this->modelo->update_publicacion_mercadolibre($id);
    
    // Eliminamos las relaciones entre propiedades
    $this->db->query("DELETE FROM inm_propiedades_relacionados WHERE id_propiedad = $id ");
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($productos_relacionados as $p) {
      $this->db->insert("inm_propiedades_relacionados",array(
        "id_propiedad"=>$id,
        "id_relacion"=>$p->id,
        "id_rubro"=>0,
        "destacado"=>$p->destacado,
        "orden"=>$i,
      ));
      $i++;
    }

    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $this->db->query("DELETE FROM inm_propiedades_etiquetas WHERE id_propiedad = $id AND id_empresa = $id_empresa");
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $id_empresa;
      $tag->id_propiedad = $id;
      $tag->nombre = $e;
      $this->modelo->save_tag($tag);
    }    

    // Actualizamos los departamentos
    $this->db->query("DELETE FROM inm_departamentos WHERE id_propiedad = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM inm_departamentos_images WHERE id_propiedad = $id AND id_empresa = $id_empresa");
    $i=1;
    foreach($departamentos as $p) {
      $this->db->insert("inm_departamentos",array(
        "id_propiedad"=>$id,
        "nombre"=>$p->nombre,
        "texto"=>$p->texto,
        "piso"=>$p->piso,
        "id_empresa"=>$p->id_empresa,
        "disponible"=>$p->disponible,
        "orden"=>$p->orden,
      ));
      $id_departamento = $this->db->insert_id();
      // Insertamos las fotos del departamento
      $j=0;
      foreach($p->images_dptos as $f) {
        $this->db->insert("inm_departamentos_images",array(
          "id_propiedad"=>$id,
          "id_departamento"=>$id_departamento,
          "path"=>$f,
          "id_empresa"=>$p->id_empresa,
          "orden"=>$j,
        ));
        $j++;
      }
      $i++;
    }
        
    // Guardamos las imagenes
    $this->db->query("DELETE FROM inm_propiedades_images WHERE plano = 0 AND id_propiedad = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO inm_propiedades_images (plano,id_empresa,id_propiedad,path,orden) VALUES(0,$id_empresa,$id,'$im',$k)");
      $k++;
    }
    $this->db->query("DELETE FROM inm_propiedades_images_meli WHERE id_propiedad = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images_meli as $im) {
      $sql = "INSERT INTO inm_propiedades_images_meli (id_empresa,id_propiedad,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$id_empresa,$id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }  
    
    // Guardamos los planos
    $this->db->query("DELETE FROM inm_propiedades_images WHERE plano = 1 AND id_propiedad = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($planos as $im) {
      $this->db->query("INSERT INTO inm_propiedades_images (plano,id_empresa,id_propiedad,path,orden) VALUES(1,$id_empresa,$id,'$im',$k)");
      $k++;
    }

    // Guardamos los precios
    $this->db->query("DELETE FROM inm_propiedades_precios WHERE id_propiedad = $id AND id_empresa = $array->id_empresa");
    foreach($temporada as $im) {
      $desde = fecha_mysql($im->fecha_desde);
      $hasta = fecha_mysql($im->fecha_hasta);
      $this->db->query("INSERT INTO inm_propiedades_precios (id_empresa,id_propiedad,promocion,fecha_desde,fecha_hasta,precio_finde,precio_semana,precio_mes,nombre,minimo_dias_reserva,precio) VALUES($array->id_empresa,$id,0,'$desde','$hasta',$im->precio_finde,$im->precio_semana,$im->precio_mes,'$im->nombre',$im->minimo_dias_reserva,$im->precio)");
    }

    // Guardamos los impuestos
    $this->db->query("DELETE FROM inm_propiedades_impuestos WHERE id_propiedad = $id AND id_empresa = $array->id_empresa");
    $k=0;
    foreach($impuestos as $im) {
      $this->db->query("INSERT INTO inm_propiedades_impuestos (id_empresa,id_propiedad,nombre,tipo,monto,orden) VALUES($array->id_empresa,$id,'$im->nombre','$im->tipo','$im->monto',$k)");
      $k++;
    }

    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }
    
    
  function insert() {
      
    $this->load->helper("file_helper");
    $this->load->helper("fecha_helper");
  	$array = $this->parse_put();
    
    // Ponemos excepciones para el control de session
    if (isset($array->id_empresa) && $array->id_empresa == 263) {
      $id_empresa = 263;
    } else {
      $id_empresa = parent::get_empresa();  
    }
    $array->id_empresa = $id_empresa;

    $control_plan = $this->modelo->controlar_plan($id_empresa);
    if ($control_plan !== TRUE) {
      echo json_encode($control_plan);
      exit();
    }
    
    // Eliminamos todo lo que no se persiste
    $images = (isset($array->images)) ? $array->images : array();
    $images_meli = (isset($array->images_meli)) ? $array->images_meli : array();
    $planos = (isset($array->planos)) ? $array->planos : array();
    $departamentos = (isset($array->departamentos)) ? $array->departamentos : array();
    $productos_relacionados = (isset($array->relacionados)) ? $array->relacionados : array();
    $etiquetas = (isset($array->etiquetas)) ? $array->etiquetas : array();
    $temporada = (isset($array->temporada)) ? $array->temporada : array();
    $impuestos = (isset($array->impuestos)) ? $array->impuestos : array();

    if ($id_empresa == 263) {
      // Utilizamos la fecha de ingreso como fecha de vencimiento
      $this->load->model("Web_Configuracion_Model");
      $web_conf = $this->Web_Configuracion_Model->get($id_empresa);
      $cant_dias = (empty($web_conf->texto_quienes_somos)) ? 30 : ((int)$web_conf->texto_quienes_somos);
      $array->valido_hasta = date("Y-m-d",strtotime("+".$cant_dias." days"));
    } else {
      $array->valido_hasta = fecha_mysql($array->valido_hasta);
    }
    
    $this->remove_properties($array);
      
    $array->fecha_ingreso = date("Y-m-d");
    $array->fecha_publicacion = (!empty($array->fecha_publicacion)) ? fecha_mysql($array->fecha_publicacion) : date("Y-m-d");

    $array->codigo = isset($array->codigo) ? $array->codigo : "";
    $array->codigo = trim($array->codigo);
    if (!empty($array->codigo)) {
      if ($this->modelo->existe_codigo($array->codigo)) {
        $salida = array(
          "error"=>1,
          "mensaje"=>"El codigo '$array->codigo' ya existe."
        );
        echo json_encode($salida);
        return;
      }      
    }

    // Excepciones
    if ($id_empresa == 70 && sizeof($images)>0) {
      $array->path = $images[0];
    }

    if (is_null($array->id_propietario)) $array->id_propietario = 0;
      
    // Insertamos la propiedad
    $insert_id = $this->modelo->save($array);
	  $hash = md5($insert_id);
      
    // Actualizamos el link
    $array->link = "propiedad/".filename($array->nombre,"-",0)."-".$insert_id."/";
    $this->db->query("UPDATE inm_propiedades SET link = '$array->link', hash='$hash' WHERE id = $insert_id AND id_empresa = $id_empresa");
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($productos_relacionados as $p) {
      $this->db->insert("inm_propiedades_relacionados",array(
        "id_propiedad"=>$insert_id,
        "id_relacion"=>$p->id,
        "id_rubro"=>0,
        "destacado"=>$p->destacado,
        "orden"=>$i,
      ));
      $i++;
    }

    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $i=1;
    $this->db->query("DELETE FROM inm_propiedades_etiquetas WHERE id_propiedad = $insert_id AND id_empresa = $id_empresa");
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $id_empresa;
      $tag->id_propiedad = $insert_id;
      $tag->nombre = $e;
      $this->modelo->save_tag($tag);
    }   

    /*
    // Actualizamos las categorias relacionadas
    $i=1;
    foreach($rubros_relacionados as $p) {
      $this->db->insert("inm_propiedades_relacionados",array(
        "id_propiedad"=>$insert_id,
        "id_relacion"=>0,
        "id_rubro"=>$p->id,
        "orden"=>$i,
      ));
      $i++;
    }
    */

    // Actualizamos los departamentos
    $i=1;
    foreach($departamentos as $p) {
      $this->db->insert("inm_departamentos",array(
        "id_propiedad"=>$insert_id,
        "nombre"=>$p->nombre,
        "texto"=>$p->texto,
        "piso"=>$p->piso,
        "id_empresa"=>$p->id_empresa,
        "disponible"=>$p->disponible,
        "orden"=>$p->orden,
      ));
      $id_departamento = $this->db->insert_id();
      // Insertamos las fotos del departamento
      $j=0;
      foreach($p->images_dptos as $f) {
        $this->db->insert("inm_departamentos_images",array(
          "id_propiedad"=>$insert_id,
          "id_departamento"=>$id_departamento,
          "path"=>$f,
          "id_empresa"=>$p->id_empresa,
          "orden"=>$j,
        ));
        $j++;
      }
      $i++;
    }
    
    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO inm_propiedades_images (id_empresa,id_propiedad,path,orden,plano) VALUES($id_empresa,$insert_id,'$im',$k,0)");
      $k++;
    }
    $k=0;
    foreach($images_meli as $im) {
      $sql = "INSERT INTO inm_propiedades_images_meli (id_empresa,id_propiedad,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$id_empresa,$insert_id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }

    // Guardamos los planos
    $k=0;
    foreach($planos as $im) {
      $this->db->query("INSERT INTO inm_propiedades_images (id_empresa,id_propiedad,path,orden,plano) VALUES($id_empresa,$insert_id,'$im',$k,1)");
      $k++;
    }

    // Guardamos los precios
    $this->db->query("DELETE FROM inm_propiedades_precios WHERE id_propiedad = $insert_id AND id_empresa = $array->id_empresa");
    foreach($temporada as $im) {
      $desde = fecha_mysql($im->fecha_desde);
      $hasta = fecha_mysql($im->fecha_hasta);
      $this->db->query("INSERT INTO inm_propiedades_precios (id_empresa,id_propiedad,promocion,fecha_desde,fecha_hasta,precio_finde,precio_semana,precio_mes,nombre,minimo_dias_reserva,precio) VALUES($array->id_empresa,$insert_id,0,'$desde','$hasta',$im->precio_finde,$im->precio_semana,$im->precio_mes,'$im->nombre',$im->minimo_dias_reserva,$im->precio)");
    }

    // Guardamos los impuestos
    $this->db->query("DELETE FROM inm_propiedades_impuestos WHERE id_propiedad = $insert_id AND id_empresa = $array->id_empresa");
    $k=0;
    foreach($impuestos as $im) {
      $this->db->query("INSERT INTO inm_propiedades_impuestos (id_empresa,id_propiedad,monto,tipo,nombre,orden) VALUES($array->id_empresa,$insert_id,'$im->monto','$im->tipo','$im->nombre',$k)");
      $k++;
    }
    
    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }
    
    
  function get_by_codigo($codigo='') {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.fecha_publicacion='0000-00-00','',DATE_FORMAT(A.fecha_publicacion,'%d/%m/%Y')) AS fecha_publicacion, ";
    $sql.= "IF(P.nombre IS NULL,'',P.nombre) AS propietario, ";
    $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS tipo_estado, ";
    $sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo_inmueble, ";
    $sql.= "IF(X.nombre IS NULL,'',X.nombre) AS tipo_operacion, ";
    $sql.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= "FROM inm_propiedades A ";
    $sql.= "LEFT JOIN inm_tipos_estado TE ON (A.id_tipo_estado = TE.id) ";
    $sql.= "LEFT JOIN inm_tipos_inmueble TI ON (A.id_tipo_inmueble = TI.id) ";
    $sql.= "LEFT JOIN inm_tipos_operacion X ON (A.id_tipo_operacion = X.id) ";
    $sql.= "LEFT JOIN clientes P ON (A.id_propietario = P.id AND A.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "WHERE A.activo = 1 ";
    $sql.= "AND A.codigo = '$codigo' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      echo json_encode(array(
        "error"=>0,
        "propiedad"=>$row,
      ));
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe una propiedad con el codigo: '$codigo'"
      ));
    }
  }
    
  function get_by_descripcion() {
    $id_empresa = parent::get_empresa();
    $descripcion = $this->input->get("term");
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.fecha_publicacion='0000-00-00','',DATE_FORMAT(A.fecha_publicacion,'%d/%m/%Y')) AS fecha_publicacion, ";
    $sql.= "IF(P.nombre IS NULL,'',P.nombre) AS propietario, ";
    $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS tipo_estado, ";
    $sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo_inmueble, ";
    $sql.= "IF(X.nombre IS NULL,'',X.nombre) AS tipo_operacion, ";
    $sql.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= "FROM inm_propiedades A ";
    $sql.= "LEFT JOIN inm_tipos_estado TE ON (A.id_tipo_estado = TE.id) ";
    $sql.= "LEFT JOIN inm_tipos_inmueble TI ON (A.id_tipo_inmueble = TI.id) ";
    $sql.= "LEFT JOIN inm_tipos_operacion X ON (A.id_tipo_operacion = X.id) ";
    $sql.= "LEFT JOIN clientes P ON (A.id_propietario = P.id AND A.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "WHERE A.activo = 1 ";
    $sql.= "AND A.nombre LIKE '%$descripcion%' ";
    $sql.= "AND A.id_empresa = '$id_empresa' ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->codigo;
      $rr->value = $r->codigo;
      $rr->label = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }    
    
    
  /**
   *  Obtenemos los datos de un propiedad en particular
   */
  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {
      $sql = "SELECT A.*, ";
      $sql.= "IF(A.fecha_publicacion='0000-00-00','',DATE_FORMAT(A.fecha_publicacion,'%d/%m/%Y')) AS fecha_publicacion, ";
      $sql.= "IF(P.nombre IS NULL,'',P.nombre) AS propietario, ";
      $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS tipo_estado, ";
      $sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo_inmueble, ";
      $sql.= "IF(X.nombre IS NULL,'',X.nombre) AS tipo_operacion, ";
      $sql.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
      $sql.= "FROM inm_propiedades A ";
      $sql.= "LEFT JOIN inm_tipos_estado TE ON (A.id_tipo_estado = TE.id) ";
      $sql.= "LEFT JOIN inm_tipos_inmueble TI ON (A.id_tipo_inmueble = TI.id) ";
      $sql.= "LEFT JOIN inm_tipos_operacion X ON (A.id_tipo_operacion = X.id) ";
      $sql.= "LEFT JOIN clientes P ON (A.id_propietario = P.id AND A.id_empresa = P.id_empresa) ";
      $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
      $sql.= "WHERE A.activo = 1 AND A.id_empresa = '$id_empresa' ";
      $sql.= "ORDER BY A.nombre ASC ";
      $q = $this->db->query($sql);
      $result = $q->result();
      echo json_encode(array(
        "results"=>$result,
        "total"=>sizeof($result)
      ));
    } else {
      $propiedad = $this->modelo->get($id);
      echo json_encode($propiedad);
    }
  }

  function ver_propiedad($id,$id_empresa) {
    $propiedad = $this->modelo->get($id,array(
      "id_empresa"=>$id_empresa
    ));
    echo json_encode($propiedad);    
  }
    
    
  /**
   *  Muestra todos los propiedades filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver() {
      
    $limit = $this->input->get("limit");
    $id_tipo_operacion = str_replace("-",",",parent::get_get("id_tipo_operacion",""));
    $id_tipo_estado = str_replace("-",",",parent::get_get("id_tipo_estado",""));
    $id_tipo_inmueble = str_replace("-",",",parent::get_get("id_tipo_inmueble",""));
    $buscar_red = parent::get_get("buscar_red",0);
    $buscar_red_empresa = parent::get_get("buscar_red_empresa",0);
    $id_propietario = parent::get_get("id_propietario",0);
    $filter = $this->input->get("filter");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $id_categoria = $this->input->get("id_categoria");
    $monto = ($this->input->get("monto") !== FALSE) ? $this->input->get("monto") : "";
    $monto_2 = ($this->input->get("monto_2") !== FALSE) ? $this->input->get("monto_2") : "";
    $monto_tipo = ($this->input->get("monto_tipo") !== FALSE) ? $this->input->get("monto_tipo") : "igual";
    $monto_moneda = ($this->input->get("monto_moneda") !== FALSE) ? $this->input->get("monto_moneda") : "$";
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    $id_localidad = str_replace("-",",",parent::get_get("id_localidad",""));
    $apto_banco = ($this->input->get("apto_banco") !== FALSE) ? $this->input->get("apto_banco") : 0;
    $acepta_permuta = ($this->input->get("acepta_permuta") !== FALSE) ? $this->input->get("acepta_permuta") : 0;
    $filtro_meli = ($this->input->get("filtro_meli") !== FALSE) ? $this->input->get("filtro_meli") : -1;
    $filtro_olx = ($this->input->get("filtro_olx") !== FALSE) ? $this->input->get("filtro_olx") : -1;
    $filtro_inmovar = ($this->input->get("filtro_inmovar") !== FALSE) ? $this->input->get("filtro_inmovar") : -1;
    $filtro_inmobusquedas = ($this->input->get("filtro_inmobusquedas") !== FALSE) ? $this->input->get("filtro_inmobusquedas") : -1;
    $filtro_argenprop = ($this->input->get("filtro_argenprop") !== FALSE) ? $this->input->get("filtro_argenprop") : -1;
    $activo = parent::get_get("activo",-1);
    $dormitorios = ($this->input->get("dormitorios") !== FALSE) ? $this->input->get("dormitorios") : "";
    $banios = ($this->input->get("banios") !== FALSE) ? $this->input->get("banios") : "";
    $calle = ($this->input->get("calle") !== FALSE) ? $this->input->get("calle") : "";
    $id_empresa = ($this->input->get("id_empresa") !== FALSE) ? $this->input->get("id_empresa") : parent::get_empresa();
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";

    if (!is_numeric($monto)) $monto = "";
    if (!is_numeric($monto_2)) $monto_2 = "";

    // Si term esta definido, es porque estamos buscando de la barra
    $term = $this->input->get("term");
    if ($term !== FALSE) $filter = $term;
    $filter = trim($filter);
      
    $conf = array(
      "buscar_red"=>$buscar_red,
      "buscar_red_empresa"=>$buscar_red_empresa,
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
      "order"=>$order,
      "id_empresa"=>$id_empresa,
      "id_tipo_operacion"=>$id_tipo_operacion,
      "id_tipo_estado"=>$id_tipo_estado,
      "id_tipo_inmueble"=>$id_tipo_inmueble,
      "id_localidad"=>$id_localidad,
      "dormitorios"=>$dormitorios,
      "banios"=>$banios,
      "filtro_meli"=>$filtro_meli,
      "filtro_olx"=>$filtro_olx,
      "filtro_inmovar"=>$filtro_inmovar,
      "filtro_inmobusquedas"=>$filtro_inmobusquedas,
      "filtro_argenprop"=>$filtro_argenprop,
      "activo"=>$activo,
      "monto"=>$monto,
      "monto_2"=>$monto_2,
      "monto_moneda"=>$monto_moneda,
      "apto_banco"=>$apto_banco,
      "acepta_permuta"=>$acepta_permuta,
      "calle"=>$calle,
      "id_usuario"=>$id_usuario,
      "id_propietario"=>$id_propietario,
    );
    $r = $this->modelo->buscar($conf);

    // Dependiendo desde donde se hace la busqueda, devolvemos uno u otro formato
    if ($term === FALSE) {
      echo json_encode($r);
    } else {
      $salida = array();
      foreach($r["results"] as $row) {
        $rr = array();
        $rr["id"] = $row->id;
        $rr["value"] = $row->id;
        $rr["label"] = $row->nombre;
        $rr["info"] = $row->calle." ".$row->altura." - ".$row->localidad;
        $rr["path"] = $row->path;
        $salida[] = $rr;
      }
      echo json_encode($salida);
    }
  }

  function diario_dia($id_empresa = 45) {

    $inmuebles = array();
    $atributos = array();
    $fotos = array();

    $listado = $this->modelo->buscar(array(
      "id_empresa"=>$id_empresa,
      "id_tipo_estado"=>1,
      "activo"=>1,
    ));
    foreach($listado["results"] as $r) {

      $linea = array();

      // Reemplazamos las comas, para que no haya errores de campos
      $r->nombre = str_replace(",", "", $r->nombre);
      $r->descripcion = str_replace(",", "", $r->descripcion);
      $r->localidad = str_replace(",", "", $r->localidad);
      $r->propietario = str_replace(",", "", $r->propietario);

      $linea[] = $r->id;
      $linea[] = $r->nombre;
      $linea[] = ($r->publica_precio == 1) ? "Si":"No";
      $linea[] = $r->precio_final;
      $linea[] = $r->latitud;
      $linea[] = $r->longitud;
      $linea[] = "";
      $linea[] = $r->tipo_inmueble;
      $linea[] = $r->ambientes;
      $linea[] = $r->tipo_operacion;
      $linea[] = $r->calle." ".$r->altura;
      $linea[] = $r->calle;
      $linea[] = $r->altura;
      $linea[] = "";
      $linea[] = "";
      $linea[] = $r->piso;
      $linea[] = $r->numero;
      $linea[] = $r->localidad;
      $linea[] = "";
      $linea[] = $r->superficie_total;
      $linea[] = str_replace("\n", "", $r->texto);
      if ($r->moneda == 'U$S') $linea[] = "2";
      else $linea[] = "1";
      $linea_s = implode($linea, ",");
      $inmuebles[] = $linea_s;

      // Foto de portada
      $fotos[] = $r->id.","."http://www.grupo-urbano.com.ar/sistema/".$r->path.",S";
      // Mas fotos
      $sql = "SELECT AI.* FROM inm_propiedades_images AI ";
      $sql.= "WHERE AI.id_propiedad = $r->id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
      $q_img = $this->db->query($sql);
      foreach($q_img->result() as $img) {
        if ($img->plano == 1) $fotos[] = $r->id.","."http://www.grupo-urbano.com.ar/sistema/".$img->path.",N";
      }

      // Atributos
      if ($r->banios > 0) $atributos[] = $r->id.",Baños,".$r->banios;
      if ($r->cocheras > 0) $atributos[] = $r->id.",Cocheras,".$r->cocheras;
      if ($r->dormitorios > 0) $atributos[] = $r->id.",Dormitorios,".$r->dormitorios;
      if ($r->nuevo == 1) $atributos[] = $r->id.",Nueva,Si";
    }

    $inmuebles_s = implode("\n", $inmuebles);
    $fotos_s = implode("\n", $fotos);
    $atributos_s = implode("\n", $atributos);
    $base = $_SERVER['DOCUMENT_ROOT'];
    file_put_contents("$base/sistema/uploads/$id_empresa/inmuebles.txt", $inmuebles_s);
    file_put_contents("$base/sistema/uploads/$id_empresa/inmuebles_fotos.txt", $fotos_s);
    file_put_contents("$base/sistema/uploads/$id_empresa/inmuebles_atributos.txt", $atributos_s);

    $zip = new ZipArchive();
    if ($zip->open("$base/sistema/uploads/$id_empresa/inmuebles.zip", ZIPARCHIVE::CREATE) != TRUE) {
      die ("Could not open archive");
    }
    $zip->addFile("$base/sistema/uploads/$id_empresa/inmuebles.txt","inmuebles.txt");
    $zip->addFile("$base/sistema/uploads/$id_empresa/inmuebles_fotos.txt","inmuebles_fotos.txt");
    $zip->addFile("$base/sistema/uploads/$id_empresa/inmuebles_atributos.txt","inmuebles_atributos.txt");
    $zip->close();

    header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=inmuebles.zip");
    header("Content-length: " . filesize("$base/sistema/uploads/$id_empresa/inmuebles.zip"));
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    readfile("uploads/$id_empresa/inmuebles.zip");
  }

  private function limpiar_campo($str) {
    $str = str_replace("&", "", $str);
    $str = str_replace("´", "", $str);
    $str = strip_tags($str);
    $str = html_entity_decode($str,ENT_QUOTES);
    $str = str_replace("\n", " ", $str);
    $str = str_replace("\"", "", $str);
    $str = str_replace("'", "", $str);
    $str = str_replace(";", "", $str);
    $str = trim($str);
    //$str = utf8_encode($str);
    return $str;
  }

  function procesar_olx() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    try {
      $archivo_olx = file_get_contents("https://production-feeds.s3.amazonaws.com/feedFiles/3ebc3e1edfddd975f36d206f3acd4ada.csv");  
      $array = explode("\n", $archivo_olx);
      $i=0;
      foreach($array as $linea) {
        if ($i==0) { $i++; continue; }
        $campos = explode(",", $linea);
        $id_unico = str_replace("\"", "", $campos[0]);
        $id_olx = str_replace("\"", "", $campos[1]);
        $creado = str_replace("\"", "", $campos[2]);
        $actualizado = str_replace("\"", "", $campos[3]);

        // El ID esta formado de derecha a izquierda, por el ID y el ID_EMPRESA
        if (strlen($id_unico)>8) {
          $id = (int)substr($id_unico,-8);
          $id_empresa = (int)substr($id_unico,0,-8);

          $sql = "UPDATE inm_propiedades SET ";
          $sql.= " olx_id = '$id_olx', ";
          $sql.= " olx_creado = '$creado', ";
          $sql.= " olx_creado = '$actualizado' ";
          $sql.= "WHERE id = '$id' AND id_empresa = $id_empresa ";
          $this->db->query($sql);
          echo $id_olx."<br/>";
        }
        $i++;
      }
    } catch(Exception $e) {
      print_r($e);
    }
  }

  function exportar_olx() {

    $salida = "";
    $this->load->model("Empresa_Model");
    $listado = $this->modelo->buscar(array(
      "id_empresa"=>-1, // Todas las empresas
      "activo"=>1,
      "id_tipo_estado"=>1,
      "olx_habilitado"=>1,
    ));

    foreach ($listado["results"] as $l) {

      $empresa = $this->Empresa_Model->get($l->id_empresa);

      $propiedad = $this->modelo->get($l->id,array(
        "id_empresa"=>$l->id_empresa,
      ));

      $texto = $this->limpiar_campo($l->texto);
      $titulo = $this->limpiar_campo($empresa->nombre." | ".$l->nombre);

      // GRUPO URBANO
      if ($l->id_empresa == 45) {
        $empresa->email = "info@grupo-urbano.com.ar";
        $empresa->telefono = "5492214271544";
      } else if ($l->id_empresa == 731) {
        $empresa->email = "contacto@scipionipropiedades.com.ar";
        $empresa->telefono = "5492216405940";
      }

      // Si no tiene alguno de estos campos, directamente salteamos la propiedad
      if (empty($titulo) || empty($texto) || empty($l->path)) continue;

      $id_unico = (int)(str_pad($l->id_empresa, 8, "0", STR_PAD_LEFT).str_pad($l->id, 8, "0", STR_PAD_LEFT));

      $linea = "";
      $linea .= '"'.$id_unico.'";';
      $linea .= '"'.$titulo.'";';
      $linea .= '"'.$texto.'";';
      $linea .= '"'.$this->limpiar_campo($empresa->email).'";';
      $linea .= '"'.$this->limpiar_campo($empresa->telefono).'";';
      $linea .= '"'.$this->limpiar_campo($empresa->nombre).'";';
      $linea .= '"'."www.olx.com.ar".'";';
      $linea .= '"'."buenosaires.olx.com.ar".'";';

      //if ($empresa->id_localidad == 513) {  
        $linea .= '"'."laplata.olx.com.ar".'";';
      //}
      /*BARRIO ES OPCIONAL, COMPLETAMOS VACIO */
      $linea .= '"";'; 

      $linea .= '"'.$l->latitud.'";'; 
      $linea .= '"'.$l->longitud.'";'; 

      /*SI ES ALQUILER(363) O VENTA() */
      if ($l->id_tipo_operacion == 1) { $tipo_operacion = 367;} 
      elseif ($l->id_tipo_operacion) { $tipo_operacion = 363 ;}

      $linea .= '"'.$tipo_operacion.'";';

      // Tenemos 8 campos de texto en total
      $linea .= '"'.'https://www.varcreative.com/sistema/'.($l->path).'";';
      for($j=0;$j<7;$j++) {
        if (isset($propiedad->images[$j])) {
          $path = $propiedad->images[$j];
          $linea.= '"'.'https://www.varcreative.com/sistema/'.($path).'";';
        } else {
          $linea.= '"";';
        }
      }

      /*PRECIO ENTERO */
      $linea .= '"'.round($l->precio_final,0,2).'";';

      /*TIPO MONEDA ID PESO ARG(26), DOLAR ARG(11) */
      if ($l->moneda == 'U$S') { $moneda = 11; }
      else { $moneda = 26; }
      $linea .= '"'.$moneda.'";';

      /*DIRECCION EXACTA OPCIONAL */
      $linea .= '"'.$this->limpiar_campo($l->calle." ".$l->altura).'";'; 

      /*AÑADIMOS DATOS PARA INMUEBLES*/
      $linea .= '"'.$l->dormitorios.'";';
      $linea .= '"'.$l->banios.'";';
      $linea .= '"'.(($l->superficie_total < 10) ? 10 : $l->superficie_total).'";';
      /*OLX SOLO ADMITE casas(1), deptos(2), ph(3), otros(4) */

      if ($l->id_tipo_inmueble==1) {$id_tipo_inmueble = 1 ;} /* si es casa 1 */
      elseif ($l->id_tipo_inmueble==2) {$id_tipo_inmueble =2;} /* si es depto 2 */
      elseif ($l->id_tipo_inmueble==3) {$id_tipo_inmueble = 3;} /* si es ph 3 */
      else {$id_tipo_inmueble=4;};/* si es otro 4 */
      
      $linea .= '"'.$id_tipo_inmueble.'";';

      //if ($l->nuevo ==0) {$antiguedad = 7;} /* en construccion */
      //elseif ($l->nuevo==1) {$antiguedad = 8;} /* si es a estrenar */
      //elseif ($l->nuevo==5) {$antiguedad = 9;} /* de 2 a 5 años */
      //elseif ($l->nuevo==10) {$antiguedad = 10;} /* de 5 a 10 años */
      //elseif ($l->nuevo==20) {$antiguedad = 11;} /* de 10 a 20 años */
      //elseif ($l->nuevo==30) {$antiguedad = 12;} /* de 20 a 30 años */
      //elseif ($l->nuevo==50) {$antiguedad = 13;} /* de 30 a 50 años */
      //elseif ($l->nuevo>50) {$antiguedad = 14;} /* mas de 50 años */
      //$linea .= '"'.$antiguedad.'";';
      $linea .= "\n";
      $salida .= $linea;
    }

    $enc = '"ID";"TITLE";"DESCRIPTION";"CONTACT_EMAIL";"CONTACT_PHONE";"CONTACT_NAME";';
    $enc.= '"LOCATION_COUNTRY";"LOCATION_STATE";"LOCATION_CITY";"LOCATION_NEIGHBORHOOD";';
    $enc.= '"LOCATION_LATITUD";"LOCATION_LONGITUDE";"CATEGORY";';
    $enc.= '"IMAGE_URL";"IMAGE_URL";"IMAGE_URL";"IMAGE_URL";"IMAGE_URL";"IMAGE_URL";"IMAGE_URL";"IMAGE_URL";';
    $enc.= '"PRICE";"PRICE_CURRENCY";"EXACT_ADDRESS";';
    $enc.= '"APARTMENT_BEDROOMS";"APARTMENT_BATHROOMS";"APARTMENT_SURFACE";"APARTMENT_TYPE";';
    $enc.= "\n";
    $salida = $enc.$salida;

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=propiedades.csv');
    header('Pragma: no-cache');
    echo $salida;
  }

  // Esta funcion procesa el archivo que procesa inmobusqueda (el link se guarda en la configuracion)
  // para obtener los links de donde se subieron las propiedades
  function procesar_inmobusquedas() {
    $urls = array();
    $sql = "SELECT id_empresa, url_inmobusqueda FROM web_configuracion ";
    $sql.= "WHERE url_inmobusqueda != '' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $file = file_get_contents($r->url_inmobusqueda);
      if (empty($file)) continue;
      $json = json_decode($file);
      $this->db->query("UPDATE inm_propiedades SET inmobusquedas_url = '' WHERE id_empresa = ".$r->id_empresa);
      foreach($json->ids as $item) {
        $sql = "UPDATE inm_propiedades SET inmobusquedas_url = '".$item->idinmobusqueda."' WHERE id_empresa = '".$r->id_empresa."' AND codigo = '".$item->id."' ";
        echo $sql."<br/>";
        $this->db->query($sql);
      }
    }
    echo "TERMINO";
  }

  function exportar_inmobusquedas() {

    // Dependiendo del dominio es el ID_EMPRESA
    $this->load->model("Empresa_Model");
    $dominio = strtolower($_SERVER["HTTP_HOST"]);
    $empresa = $this->Empresa_Model->get_empresa_by_dominio($dominio);
    if ($empresa === FALSE) exit();

    $this->load->model("Propiedad_Model");
    $propiedades = $this->Propiedad_Model->buscar(array(
      "id_empresa"=>$empresa->id,
      "id_tipo_estado"=>1, // Estado Activo
      "activo"=>1,
      "filtro_inmobusquedas"=>3, // Todas (activas y pendientes)
      "buscar_imagenes"=>1,
      "offset"=>999999,
    ));

    require APPPATH.'libraries/SimpleXMLExtended.php';
    $xml = new SimpleXMLExtended("<xml/>");
    if (sizeof($propiedades["results"]) > 0) {
      $avisos = $xml->addChild("AVISOS");
      foreach($propiedades["results"] as $p) {

        if ($p->precio_final == 0) continue;
        if ($p->id_localidad == 0) continue;
        
        $aviso = $avisos->addChild("AVISO");
        
        $id = $aviso->addChild("ID");
        $id->addCDATA($p->codigo);

        $id_empresa = $aviso->addChild("INMOBILIARIA");
        $id_empresa->addCDATA($p->id_empresa);

        $tipo = $aviso->addChild("TIPO");
        $id_tipo_propiedad = 1; // Por defecto CASA
        if ($p->id_tipo_inmueble == 2 || $p->id_tipo_inmueble == 14) $id_tipo_propiedad = 2; // Departamento
        if ($p->id_tipo_inmueble == 9) $id_tipo_propiedad = 5; // Local
        if ($p->id_tipo_inmueble == 7) $id_tipo_propiedad = 6; // Terreno
        if ($p->id_tipo_inmueble == 17) $id_tipo_propiedad = 7; // Piso
        if ($p->id_tipo_inmueble == 6) $id_tipo_propiedad = 9; // Campo
        if ($p->id_tipo_inmueble == 11) $id_tipo_propiedad = 10; // Oficina
        if ($p->id_tipo_inmueble == 13) $id_tipo_propiedad = 11; // Cocheras
        if ($p->id_tipo_inmueble == 8) $id_tipo_propiedad = 15; // Galpon
        if ($p->id_tipo_inmueble == 15) $id_tipo_propiedad = 16; // Duplex
        if ($p->id_tipo_inmueble == 16) $id_tipo_propiedad = 17; // Triplex
        if ($p->id_tipo_inmueble == 10) $id_tipo_propiedad = 21; // Fondo de comercio
        if ($p->id_tipo_inmueble == 3) $id_tipo_propiedad = 23; // PH
        if ($p->id_tipo_inmueble == 4) $id_tipo_propiedad = 30; // Countries
        $tipo->addCDATA($id_tipo_propiedad);

        $tipo_operacion = $aviso->addChild("OPERACION");
        $id_tipo_operacion = 1; // VENTA
        if ($p->id_tipo_operacion == 1) $id_tipo_operacion = 1; // VENTA
        if ($p->id_tipo_operacion == 2) $id_tipo_operacion = 0; // ALQUILER
        if ($p->id_tipo_operacion == 3) $id_tipo_operacion = 2; // ALQUILER TEMPORARIO
        $tipo_operacion->addCDATA($id_tipo_operacion);

        $precio = $aviso->addChild("PRECIO");
        $precio->addCDATA(round($p->precio_final),0);

        $moneda = $aviso->addChild("MONEDA");
        $moneda->addCDATA(($p->moneda == '$')?1:0);

        $publica_precio = $aviso->addChild("PUBLICARPRECIO");
        $publica_precio->addCDATA($p->publica_precio);

        $calle = $aviso->addChild("DIRECCION_SOBRECALLE");
        $calle->addCDATA($p->calle);

        $entre_calle_1 = $aviso->addChild("DIRECCION_ENTRECALLE1");
        $entre_calle_1->addCDATA("");

        $entre_calle_2 = $aviso->addChild("DIRECCION_ENTRECALLE2");
        $entre_calle_2->addCDATA("");

        $altura = $aviso->addChild("DIRECCION_NUMERO");
        $altura->addCDATA($p->altura);

        $texto = $aviso->addChild("DESCRIPTION");
        $texto->addCDATA($p->texto);

        $provincia = $aviso->addChild("PROVINCIA");
        $provincia->addCDATA($p->id_provincia_inmobusquedas);

        $partido = $aviso->addChild("PARTIDO");
        $partido->addCDATA($p->id_partido_inmobusquedas);

        $localidad = $aviso->addChild("LOCALIDAD");
        $localidad->addCDATA($p->id_localidad_inmobusquedas);

        // TODO: Tomar los barrios
        $barrio = $aviso->addChild("BARRIO");
        $id_barrio = 103;
        $barrio->addCDATA($id_barrio);

        $latitud = $aviso->addChild("LATITUD");
        $latitud->addCDATA($p->latitud);
        
        $longitud = $aviso->addChild("LONGITUD");
        $longitud->addCDATA($p->longitud);

        $banios = $aviso->addChild("BANOS");
        $banios->addCDATA($p->banios);

        $ambientes = $aviso->addChild("AMBIENTES");
        $ambientes->addCDATA($p->ambientes);

        $dormitorios = $aviso->addChild("DORMITORIOS");
        $dormitorios->addCDATA($p->dormitorios);

        $apto_banco = $aviso->addChild("APTOBANCO");
        $apto_banco->addCDATA($p->apto_banco);

        $cocheras = $aviso->addChild("GARAGE");
        $cocheras->addCDATA($p->cocheras);

        $antiguedad = $aviso->addChild("ANTIGUEDAD");
        if ($p->nuevo == 1) $id_antiguedad = 0;
        else $id_antiguedad = $p->nuevo;
        $antiguedad->addCDATA($id_antiguedad);

        // TODO: Por ahora a todos le ponemos MUY BUENO
        /*
         0:Nuevo
         1:A estrenar
         2:Excelente
         3:Muy Bueno
         4:Bueno
         5:Usado
         6:Regular
         7:A Reciclar
         8:A Demoler
         9:En Construcción
         10:Refaccionado
         11:En Pozo
        */
        $estado_construccion = $aviso->addChild("ESTADOCONSTRUCCION");
        $estado_construccion->addCDATA(3);

        $superficie_construida = $aviso->addChild("SUPERFICIECONSTRUIDA");
        $superficie_construida->addCDATA($p->superficie_cubierta);

        $superficie_total = $aviso->addChild("SUPERFICIETOTAL");
        $superficie_total->addCDATA($p->superficie_total);

        $ubicacion_en_planta = $aviso->addChild("UBICACIONENPLANTA");
        $p->ubicacion_en_planta = 0; // Frente
        if ($p->ubicacion_departamento == "F") {
          $p->ubicacion_en_planta = 0;  // Frente
        } else if ($p->ubicacion_departamento == "C") {
          $p->ubicacion_en_planta = 1;  // Contrafrente
        } else if ($p->ubicacion_departamento == "I") {
          $p->ubicacion_en_planta = 3;  // Interno
        }      
        $ubicacion_en_planta->addCDATA($p->ubicacion_en_planta);

        $orientacion = $aviso->addChild("ORIENTACION");
        $p->orientacion = 4; // Ninguno
        if ($p->ubicacion_departamento == "F") {
          $p->orientacion = 0;  // Frente
        } else if ($p->ubicacion_departamento == "C") {
          $p->orientacion = 1;  // Contrafrente
        } else if ($p->ubicacion_departamento == "I") {
          $p->orientacion = 3;  // Interno
        }      
        $orientacion->addCDATA($p->orientacion);

        $servicios_gas = $aviso->addChild("SERVICIOS_GAS");
        $servicios_gas->addCDATA($p->servicios_gas);

        $servicios_cloacas = $aviso->addChild("SERVICIOS_CLOACAS");
        $servicios_cloacas->addCDATA($p->servicios_cloacas);

        $servicios_agua_corriente = $aviso->addChild("SERVICIOS_AGUACORRIENTE");
        $servicios_agua_corriente->addCDATA($p->servicios_agua_corriente);

        $servicios_asfalto = $aviso->addChild("SERVICIOS_ASFALTO");
        $servicios_asfalto->addCDATA($p->servicios_asfalto);

        $servicios_electricidad = $aviso->addChild("SERVICIOS_ELECTRICIDAD");
        $servicios_electricidad->addCDATA($p->servicios_electricidad);

        $servicios_telefono = $aviso->addChild("SERVICIOS_TELEFONO");
        $servicios_telefono->addCDATA($p->servicios_telefono);

        $servicios_cable = $aviso->addChild("SERVICIOS_CABLE");
        $servicios_cable->addCDATA($p->servicios_cable);

        $apto_profesional = $aviso->addChild("APTOPROFESIONAL");
        $apto_profesional->addCDATA($p->apto_profesional);

        if (sizeof($p->images)>0) {
          $imagenes = $aviso->addChild("IMAGENES");
          foreach($p->images as $img) {
            $image = $imagenes->addChild("IMAGEN_URL");
            $img = "https://www.varcreative.com/sistema/".$img;
            $image->addCDATA($img);
          }
        }
      }
    }
    header('Content-type: text/xml');
    print($xml->asXML());
  }

  // IMPORTACION DE PROPIEDADES DE TOKKO BROKERS
  // Esta funcion se ejecuta en un cronjob
  function importar_tokko() {
    include_once APPPATH.'libraries/tokko/api.php';
    // Buscamos todas las empresas que tengan la importacion automatica de TOKKO
    $sql = "SELECT id_empresa, tokko_apikey FROM web_configuracion ";
    $sql.= "WHERE tokko_apikey != '' AND tokko_importacion = 1 ";
    $q = $this->db->query($sql);
    $this->load->model("Propiedad_Model");
    $this->load->helper("file_helper");
    $cant_update = 0;
    $cant_insert = 0;
    $errores = array();
    $this->load->model("Log_Model");
    foreach($q->result() as $emp) {
      $id_empresa = $emp->id_empresa;
      $auth = new TokkoAuth($emp->tokko_apikey);
      $search = new TokkoSearch($auth,array(
        "operation_types"=>0,
        "property_types"=>0,
        "price_from"=>0,
        "price_to"=>99999999,
      ));
      $search->do_search();
      $properties = $search->get_properties();
      if (sizeof($properties)>0) {
        // Limpiamos todas las propiedades que esten sincronizadas con Tokko
        // Porque las que no vienen en el array es porque se deshabilitaron del otro lado
        $sql = "UPDATE inm_propiedades ";
        $sql.= "SET activo = 0 ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND tokko_id != '' ";
        $sql.= "AND tokko_url != '' ";
        $this->db->query($sql);
      }
      foreach ($properties as $property) {

        $this->Log_Model->imprimir(array(
          "id_empresa"=>$id_empresa,
          "file"=>date("Ymd")."_importacion_tokko.txt",
          "texto"=>print_r($property,TRUE)."\n\n",
        ));

        $property->id_empresa = $id_empresa;
        $p = new stdClass();
        $p->nombre = $property->get_field("publication_title");
        $p->codigo = $property->get_field("reference_code");
        $p->calle = $property->get_field("real_address");
        $p->banios = $property->get_field("bathroom_amount");
        $p->texto = $property->get_field("description");
        $p->latitud = $property->get_field("geo_lat");
        $p->longitud = $property->get_field("geo_long");
        $p->tokko_id = $property->get_field("id");
        $p->tokko_url = $property->get_field("public_url");
        $p->dormitorios = $property->get_field("suite_amount");
        $p->ambientes = $property->get_field("room_amount");
        $p->cocheras = $property->get_field("parking_lot_amount");
        $p->superficie_cubierta = $property->get_field("roofed_surface");
        $p->superficie_semicubierta = $property->get_field("semiroofed_surface");
        $p->superficie_descubierta = $property->get_field("unroofed_surface");
        $p->superficie_total = $property->get_field("total_surface");
        $p->zoom = 17;
        $p->activo = 1;

        // TAGS
        $tags = $property->get_field("tags");
        if (sizeof($tags)>0) {
          foreach($tags as $t) {
            if ($t->name == "Agua Corriente") $p->servicios_agua_corriente = 1;
            else if ($t->name == "Cloaca") $p->servicios_cloacas = 1;
            else if ($t->name == "Gas Natural") $p->servicios_gas = 1;
            else if ($t->name == "Electricidad") $p->servicios_electricidad = 1;
            else if ($t->name == "Pavimento") $p->servicios_asfalto = 1;
            else if ($t->name == "Telefono") $p->servicios_telefono = 1;
            else if ($t->name == "Cable") $p->servicios_cable = 1;
            else if ($t->name == "Balcón") $p->balcon = 1;
            else if ($t->name == "Apto crédito") $p->apto_banco = 1;
          }
        }

        // ID_TIPO_OPERACION
        $operations = $property->get_field("operations");
        $operacion = $operations[0];
        if ($operacion->operation_type == "Venta") $p->id_tipo_operacion = 1;
        else if ($operacion->operation_type == "Alquiler") $p->id_tipo_operacion = 2;

        // PRECIO
        $p->publica_precio = 1;
        $p->precio_final = 0;
        foreach($operacion->prices as $precio) {
          if ($precio->price > 0) {
            if ($precio->currency == "USD") $p->moneda = 'U$S';
            else $p->moneda = '$';
            $p->precio_final = $precio->price;
          }
        }

        // TIPO PROPIEDAD
        $tipo = $property->get_field("type");
        $p->id_tipo_inmueble = 0;
        if ($tipo->name == "Departamento") $p->id_tipo_inmueble = 2;
        else if ($tipo->name == "Casa") $p->id_tipo_inmueble = 1;
        else if ($tipo->name == "Terreno") $p->id_tipo_inmueble = 7;
        else if ($tipo->name == "PH") $p->id_tipo_inmueble = 3;
        else if ($tipo->name == "Local") $p->id_tipo_inmueble = 9;
        else if ($tipo->name == "Galpón") $p->id_tipo_inmueble = 8;
        else {
          // Si no esta definica el tipo de propiedad, lo ponemos como error
          $errores[] = print_r($property,TRUE);
        }

        // LOCALIDAD
        $location = $property->get_field("location");
        $p->id_localidad = 0;
        $p->id_provincia = 1;
        $p->id_pais = 1;
        if ($location->name == "La Plata" || $location->name == "Villa Parque Sicardi" || $location->id == 26524) $p->id_localidad = 513;
        else if ($location->name == "City Bell") $p->id_localidad = 205;
        else if ($location->name == "Berisso" || $location->name == "Los Talas") $p->id_localidad = 5492;
        else if ($location->name == "Bme Bavio Gral Mansilla") $p->id_localidad = 111;
        else if ($location->name == "Pilar") $p->id_localidad = 723;
        else if ($location->name == "Manuel B Gonnet") $p->id_localidad = 396;
        else if ($location->name == "Tolosa") $p->id_localidad = 900;
        else if ($location->name == "Villa Elvira") $p->id_localidad = 5117;
        else if ($location->name == "Abasto") $p->id_localidad = 10;
        else if ($location->name == "Ringuelet") $p->id_localidad = 776;
        else if ($location->name == "Nueva Hermosura") $p->id_localidad = 5506;
        else if ($location->name == "San Bernardo Del Tuyu") $p->id_localidad = 812;
        else if ($location->name == "Mar Del Tuyu") $p->id_localidad = 601;
        else if ($location->name == "San Clemente Del Tuyu") $p->id_localidad = 815;
        else if ($location->name == "Ensenada") $p->id_localidad = 312;
        else if ($location->name == "Villa Gesell") $p->id_localidad = 951;
        else if ($location->name == "Necochea") $p->id_localidad = 655;
        else if ($location->name == "Mar De Ajo") $p->id_localidad = 599;
        else if ($location->name == "Los Hornos") $p->id_localidad = 5504;
        else if ($location->name == "Joaquin Gorina") $p->id_localidad = 401;
        else if ($location->name == "Lisandro Olmos Etcheverry") $p->id_localidad = 674;
        else if ($location->name == "Guillermo E Hudson" || strpos($location->full_location, "Guillermo E Hudson") !== FALSE) $p->id_localidad = 431;
        else if ($location->name == "Coronel Brandsen" || strpos($location->full_location, "Coronel Brandsen") !== FALSE) $p->id_localidad = 231;
        
        else if ($location->name == "Miami Beach") $p->id_localidad = 5500;
        else if ($location->name == "El Palmar") $p->id_localidad = 5511;

        // Si no se encuentra el nombre exacto, pero la ubicacion completa contiene el nombre de La Plata
        else if (strpos($location->full_location, "La Plata") !== FALSE) $p->id_localidad = 513;

        else {
          // Sino buscamos por nombre
          $sql = "SELECT L.*, D.id_provincia, P.id_pais ";
          $sql.= " FROM com_localidades L ";
          $sql.= " INNER JOIN com_departamentos D ON (L.id_departamento = D.id) ";
          $sql.= " INNER JOIN com_provincias P ON (D.id_provincia = P.id) ";
          $sql.= "WHERE L.nombre = '$location->name' LIMIT 0,1 ";
          $qq = $this->db->query($sql);
          if ($qq->num_rows() > 0) {
            $rr = $qq->row();
            $p->id_localidad = $rr->id;
            $p->id_provincia = $rr->id_provincia;
            $p->id_pais = $rr->id_pais;
          }
        }

        // Si no se encontro una localidad, lo ponemos como error
        if (empty($p->id_localidad)) {
          $errores[] = print_r($property,TRUE);
          print_r($property);
        }

        $images = $property->get_field("photos");
        if (sizeof($images)>0) {
          $ppal = $images[0];
          $p->path = $ppal->image;
        }

        // Consultamos si la propiedad ya esta subida
        $sql = "SELECT * FROM inm_propiedades WHERE tokko_id = '".$property->get_field("id")."' AND id_empresa = $id_empresa ";
        $q = $this->db->query($sql);
        if ($q->num_rows()>0) {
          $r = $q->row();
          $p->id = $r->id;
          $p->id_empresa = $id_empresa;
          $this->Propiedad_Model->update($p->id,$p);
          $cant_update++;
        } else {
          $p->fecha_ingreso = date("Y-m-d");
          $p->fecha_publicacion = date("Y-m-d");
          $p->id_empresa = $id_empresa;
          $p->id = $this->Propiedad_Model->insert($p);
          $cant_insert++;
        }

        // Creamos el link
        $hash = md5($p->id);
        $link = "propiedad/".filename($p->nombre,"-",0)."-".$p->id."/";
        $this->db->query("UPDATE inm_propiedades SET link = '$link', hash = '$hash' WHERE id = $p->id AND id_empresa = $id_empresa");

        // IMAGENES
        if (sizeof($images)>0) {
          $this->db->query("DELETE FROM inm_propiedades_images WHERE id_empresa = $id_empresa AND id_propiedad = $p->id ");
          $k=0;
          foreach($images as $im) {
            if (empty($im->image)) continue;
            $this->db->query("INSERT INTO inm_propiedades_images (id_empresa,id_propiedad,path,orden) VALUES ($id_empresa,$p->id,'$im->image',$k) ");
            $k++;
          }
        }
      }
    }
    // Si hay errores, nos lo mandamos por email
    if (sizeof($errores)>0) {
      $body = implode("<br/>", $errores);
      require APPPATH.'libraries/Mandrill/Mandrill.php';
      mandrill_send(array(
        "to"=>"basile.matias99@gmail.com",
        "subject"=>"ERROR IMPORTACION TOKKO",
        "body"=>$body,
      ));
    } else {
      echo "TODO OK";
    }
  }

  // IMPORTACION DE PROFIT PARA MENACHO
  function importar_profit($id_empresa = 541) {

    $fecha = date("Ymd");    
    //$archivo = "uploads/$id_empresa/propiedades/propiedades_$fecha.xml";
    $archivo = "http://c1110070.ferozo.com/profit/propiedades_$fecha.xml";
    /*if (!file_exists($archivo)) {
      echo "El archivo $archivo no existe.";
      return;
    }*/
    $xml = file_get_contents($archivo);
    if ($xml === FALSE) {
      // No se encuentra el archivo
      echo "ERROR: No se encuentra el archivo $archivo";
      exit();
    }
    $propiedades = new SimpleXMLElement($xml);

    $this->load->model("Propiedad_Model");
    $this->load->model("Localidad_Model");
    $this->load->helper("file_helper");

    // Si tiene propiedades, empezamos a procesar el archivo
    // Lo primero que hacemos es desactivar todas las propiedades actuales
    $sql = "UPDATE inm_propiedades SET activo = 0 WHERE id_empresa = $id_empresa AND id_tipo_operacion IN (1,2) ";
    $this->db->query($sql);

    $cant_insert = 0;
    $cant_update = 0;

    foreach($propiedades as $key => $propiedad) {

      $p = new stdClass();
      $p->id_empresa = $id_empresa;
      $p->codigo = $propiedad->numero->__toString();

      $p->zoom = 14;
      $p->publica_precio = 1;
      $p->activo = 1;
      $p->destacado = 0;
      $p->id_tipo_estado = 1;

      // Buscamos el ID_LOCALIDAD a partir de su codigo postal
      $codigo_postal_localidad = $propiedad->codigo_postal_localidad->__toString();
      $localidad = $this->Localidad_Model->get_by_codigo_postal($codigo_postal_localidad);
      $p->id_localidad = 0;
      if ($localidad !== FALSE) {
        $p->id_localidad = $localidad->id;
      }

      $p->id_tipo_inmueble = 1;
      // ID_TIPO_PROPIEDAD
      if ($propiedad->id_tipo_propiedad == 1) {
        // Departamento
        $p->id_tipo_inmueble = 2;
      } else if ($propiedad->id_tipo_propiedad == 2) {
        // Casa
        $p->id_tipo_inmueble = 1;
      } else if ($propiedad->id_tipo_propiedad == 3) {
        // Local
        $p->id_tipo_inmueble = 9;
      } else if ($propiedad->id_tipo_propiedad == 4) {
        // Terreno
        $p->id_tipo_inmueble = 7;
      } else if ($propiedad->id_tipo_propiedad == 5) {
        // Oficina
        $p->id_tipo_inmueble = 11;
      } else if ($propiedad->id_tipo_propiedad == 12) {
        // Cochera
        $p->id_tipo_inmueble = 13;
      } else if ($propiedad->id_tipo_propiedad == 11) {
        // Campos
        $p->id_tipo_inmueble = 6;
      } else if ($propiedad->id_tipo_propiedad == 9) {
        // Galpones
        $p->id_tipo_inmueble = 8;
      }

      if (strtoupper($propiedad->en_venta) == "SI") {
        $p->id_tipo_operacion = 1;
        $p->operacion = "Venta";
        $p->precio_final = $propiedad->importe_venta->__toString();
        $p->moneda = ($propiedad->id_moneda_venta->__toString() == 1) ? "$" : 'U$S';
      } else {
        $p->id_tipo_operacion = 2;
        $p->operacion = "Alquiler";
        $p->precio_final = $propiedad->importe_alquiler->__toString();
        $p->moneda = ($propiedad->id_moneda_alquiler->__toString() == 1) ? "$" : 'U$S';
      }

      // Ej: Local en venta
      $p->nombre = $propiedad->nombre_tipo_propiedad->__toString()." en ".$p->operacion;

      $p->calle = mb_convert_encoding($propiedad->calle->__toString(), 'UTF-8', 'ISO-8859-1');
      $p->altura = mb_convert_encoding($propiedad->numero_calle->__toString(), 'UTF-8', 'ISO-8859-1');
      $p->latitud = $propiedad->latitud_propiedad->__toString();
      $p->longitud = $propiedad->longitud_propiedad->__toString();

      $p->texto = mb_convert_encoding((isset($propiedad->datos_enlace_propiedad->texto_web_propiedad)) ? $propiedad->datos_enlace_propiedad->texto_web_propiedad->__toString() : $propiedad->descripcion_web_propiedad->__toString(), 'UTF-8', 'ISO-8859-1');
      $p->entre_calles = mb_convert_encoding((isset($propiedad->domicilio_extra_propiedad)) ? $propiedad->domicilio_extra_propiedad->__toString() : "", 'UTF-8', 'ISO-8859-1');

      $p->superficie_total = $propiedad->superficie_total_propiedad->__toString();
      $p->superficie_cubierta = $propiedad->superficie_cubierta_propiedad->__toString();
      $p->dormitorios = $propiedad->numero_ambientes_propiedad->__toString();
      $p->ambientes = $p->dormitorios;
      $p->banios = $propiedad->cantidad_baños_propiedad->__toString();
      $p->balcon = (strtoupper($propiedad->balcon_propiedad->__toString()) == "S") ? 1 : 0;
      $p->cocheras = (strtoupper($propiedad->descripcion_cochera_propiedad->__toString()) == "SI") ? 1 : 0;
      $p->fecha_publicacion = date("Y-m-d");

      $p->destacado = 0;
      if (isset($propiedad->datos_enlace_propiedad->descripcion_destacada_propiedad)
      && strtoupper($propiedad->datos_enlace_propiedad->descripcion_destacada_propiedad->__toString()) == "SI") {
        $p->destacado = 1;
      }

      $p->publica_precio = 1;
      if (isset($propiedad->datos_enlace_propiedad->descripcion_muestra_precio_web)
      && strtoupper($propiedad->datos_enlace_propiedad->descripcion_muestra_precio_web->__toString()) == "NO") {
        $p->publica_precio = 0;
      }

      $p->apto_banco = 0;
      foreach($propiedad->campos_extra_propiedad->children() as $campo_extra) {
        if (strtoupper($campo_extra->descripcion_tipo_campo_extra_propiedad->__toString()) == "APTO BANCO"
          && strtoupper($campo_extra->valor_campo_extra_propiedad->__toString()) == "SI"
        ) {
          $p->apto_banco = 1;
        }
      }

      $images = array();
      foreach($propiedad->multimedia_propiedad->children() as $archivo) {
        if (strtoupper($archivo->descripcion_tipo_archivo->__toString()) == "IMAGEN") {
          $cc = new stdClass();
          $cc->orden = (int)($archivo->orden_archivo->__toString());
          $cc->archivo = "http://c1110070.ferozo.com/profit/".$archivo->nombre_archivo->__toString();
          $images[] = $cc;
        }
      }

      usort($images,array('Propiedades','importar_profit_ordenar_images'));

      if (sizeof($images)>0) {
        $first = array_shift($images);
        $p->path = $first->archivo;
      }

      // Consultamos si la propiedad ya esta subida
      $sql = "SELECT * FROM inm_propiedades WHERE codigo = '$p->codigo' AND id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $r = $q->row();
        $p->id = $r->id;
        unset($p->destacado); // TODO: Arreglar esto de destacados despues
        //if ($r->destacado == 1 && $p->) $p->destacado;
        /*
        if ($p->moneda == 'U$S' && $p->precio_final > 100000 && $id_empresa == 541) {
          // En Menacho, si la propiedad vale mas de 100 mil dolares, no se publica el precio
          $p->publica_precio = 0;
        }
        */
        $this->Propiedad_Model->update($p->id,$p);
        $cant_update++;
      } else {
        $p->fecha_ingreso = date("Y-m-d");
        $p->id = $this->Propiedad_Model->insert($p);
        $cant_insert++;
      }

      // Creamos el link
      $hash = md5($p->id);
      $link = "propiedad/".filename($p->nombre,"-",0)."-".$p->id."/";
      $this->db->query("UPDATE inm_propiedades SET link = '$link', hash = '$hash' WHERE id = $p->id AND id_empresa = $id_empresa");

      // Guardamos las imagenes
      if (sizeof($images)>0) {
        $this->db->query("DELETE FROM inm_propiedades_images WHERE id_empresa = $id_empresa AND id_propiedad = $p->id ");
        $k=0;
        foreach($images as $im) {
          if (empty($im->archivo)) continue;
          $this->db->query("INSERT INTO inm_propiedades_images (id_empresa,id_propiedad,path,orden) VALUES ($id_empresa,$p->id,'$im->archivo',$k) ");
          $k++;
        }
      }

    }
    echo "TERMINO CORRECTAMENTE<br/>";
    echo "INSERTADOS: $cant_insert<br/>";
    echo "ACTUALIZADOS: $cant_update";
  }

  private static function importar_profit_ordenar_images($a,$b) {
    return ($a->orden >= $b->orden) ? 1 : -1;
  }

  function compartir_argenprop() {

    $this->load->model("Log_Model");
    $id_empresa = parent::get_empresa();
    $id_propiedad = parent::get_get("id_propiedad");
    $propiedad = $this->modelo->get($id_propiedad,array(
      "id_empresa"=>$id_empresa
    ));
    if (empty($propiedad)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la propiedad con ID: $id_propiedad",
      ));
      exit();
    }

    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($id_empresa);
    if (empty($web_conf->argenprop_usuario) || empty($web_conf->argenprop_password) || empty($web_conf->argenprop_id_vendedor)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Falta configurar las credenciales de Argenprop. Por favor ingreselas en Configuracion / Avanzada / Integracion con Argenprop",
      ));
      exit();
    }

    $headers = array(
      'cache-control' => 'no-cache',
      'content-type' => 'application/x-www-form-urlencoded'
    );

    $id_origen = $propiedad->id."_".$propiedad->id_empresa;
    //$usuario_argenprop = 'integrador@argenprop.com';
    //$password_argenprop = '123456';
    //$id_vendedor = '242566';
    $usuario_argenprop = $web_conf->argenprop_usuario;
    $password_argenprop = $web_conf->argenprop_password;
    $id_vendedor = $web_conf->argenprop_id_vendedor;

    $id_tipo_operacion = "1"; // Venta
    if ($propiedad->id_tipo_operacion == 2) {
      $id_tipo_operacion = "2"; // Alquiler
    } else if ($propiedad->id_tipo_operacion == 3) {
      $id_tipo_operacion = "3"; // Alquiler temporario
    } else if ($propiedad->id_tipo_operacion == 4) {
      $id_tipo_operacion = "1"; // Emprendimientos: es venta en realidad
    }

    $id_tipo_propiedad = "3"; // Casa
    if ($propiedad->id_tipo_inmueble == 2) {
      $id_tipo_propiedad = "1"; // Departamento
    } else if ($propiedad->id_tipo_inmueble == 5) {
      $id_tipo_propiedad = "4"; // Quinta
    } else if ($propiedad->id_tipo_inmueble == 13) {
      $id_tipo_propiedad = "5"; // Cochera
    } else if ($propiedad->id_tipo_inmueble == 9) {
      $id_tipo_propiedad = "6"; // Local
    } else if ($propiedad->id_tipo_inmueble == 7) {
      $id_tipo_propiedad = "8"; // Terreno
    } else if ($propiedad->id_tipo_inmueble == 11) {
      $id_tipo_propiedad = "9"; // Oficina
    } else if ($propiedad->id_tipo_inmueble == 6) {
      $id_tipo_propiedad = "10"; // Campo
    } else if ($propiedad->id_tipo_inmueble == 10) {
      $id_tipo_propiedad = "11"; // Fondo de Comercio
    } else if ($propiedad->id_tipo_inmueble == 8) {
      $id_tipo_propiedad = "12"; // Galpon
    }

    $monto = (string)round($propiedad->precio_final,0);
    $moneda = "2";
    if ($propiedad->moneda == '$') $moneda = "1";

    $fields = array(
      'usr' => $usuario_argenprop,
      'psd' => $password_argenprop,
      'aviso.SistemaOrigen.Id' => '10',
      'aviso.Vendedor.IdOrigen' => $id_origen,
      'aviso.EsWeb' => 'true',
      'aviso.Vendedor.Id' => $id_vendedor,
      'aviso.IdOrigen' => $id_origen,
      'aviso.Titulo' => substr($propiedad->nombre, 0, 100),
      'aviso.TipoOperacion' => $id_tipo_operacion,
      'visibilidades[0].MontoOperacion' => $monto,
      'visibilidades[0].Moneda.Id' => $moneda,
      'tipoPropiedad' => $id_tipo_propiedad,
    );

    $this->load->model("Localidad_Model");
    $localidad = $this->Localidad_Model->get_argenprop($propiedad->id_localidad);
    $fields['propiedad.Direccion.Pais.Id'] = $localidad->id_pais_argenprop;
    $fields['propiedad.Direccion.Provincia.Id'] = $localidad->id_provincia_argenprop;
    $fields['propiedad.Direccion.Partido.Id'] = $localidad->id_departamento_argenprop;
    $fields['propiedad.Direccion.Localidad.Id'] = $localidad->id_localidad_argenprop;
    if (!empty($localidad->id_barrio_argenprop)) $fields["propiedad.Direccion.Barrio.Id"] = $localidad->id_barrio_argenprop;
    else if (!empty($propiedad->id_barrio_argenprop)) $fields["propiedad.Direccion.Barrio.Id"] = $propiedad->id_barrio_argenprop;
    $fields['propiedad.Direccion.Coordenadas.Latitud'] = $propiedad->latitud;
    $fields['propiedad.Direccion.Coordenadas.Longitud'] = $propiedad->longitud;
    $fields['propiedad.Direccion.Nombrecalle'] = $propiedad->calle;
    $fields['propiedad.Direccion.Numero'] = $propiedad->altura;
 
    if (!empty($propiedad->superficie_cubierta)) $fields['propiedad.SuperficieCubierta'] = "$propiedad->superficie_cubierta";
    if (!empty($propiedad->superficie_total)) $fields['propiedad.SuperficieTotal'] = "$propiedad->superficie_total";
    if (!empty($propiedad->nuevo)) $fields['propiedad.Antiguedad'] = "$propiedad->nuevo";
    if (!empty($propiedad->ambientes)) $fields['propiedad.CantidadAmbientes'] = "$propiedad->ambientes";
    if (!empty($propiedad->banios)) $fields['propiedad.CantidadBanos'] = "$propiedad->banios";
    if (!empty($propiedad->dormitorios)) $fields['propiedad.CantidadDormitorios'] = "$propiedad->dormitorios";
    if (!empty($propiedad->cocheras)) $fields['propiedad.CantidadCocheras'] = "$propiedad->cocheras";
    if ($propiedad->balcon == 1) $fields["propiedad.Ambientes.Balcon"] = 'true';
    if ($propiedad->patio == 1) $fields["propiedad.Ambientes.Patio"] = 'true';

    if ($propiedad->apto_profesional == 1) $fields["propiedad.AptoProfesional"] = 'true';
    if ($propiedad->servicios_gas == 1) $fields["propiedad.Instalaciones.GasNatural"] = 'true';
    //if ($propiedad->servicios_cloacas == 1) $fields["propiedad.Ambientes.Patio"] = 'true';
    if ($propiedad->servicios_agua_corriente == 1) $fields["propiedad.Ambientes.Patio"] = 'true';
    //if ($propiedad->servicios_asfalto == 1) $fields["propiedad.Ambientes.Patio"] = 'true';
    if ($propiedad->servicios_electricidad == 1) $fields["propiedad.Instalaciones.Electricidad"] = 'true';
    if ($propiedad->servicios_telefono == 1) $fields["propiedad.Instalaciones.Telefono"] = 'true';
    if ($propiedad->servicios_cable == 1) $fields["propiedad.Servicios.Videocable"] = 'true';

    if (!empty($propiedad->id_usuario)) {
      $this->load->model("Usuario_Model");
      $usuario = $this->Usuario_Model->get($propiedad->id_usuario,array(
        "id_empresa"=>$id_empresa
      ));
      if ($id_empresa == 45) $usuario->email = "info@grupo-urbano.com.ar";
      if (!empty($usuario)) {
        if (!empty($usuario->nombre)) $fields['aviso.DatosContacto.Nombre'] = $usuario->nombre;
        if (!empty($usuario->celular)) $fields['aviso.DatosContacto.Celular'] = $usuario->celular;
        if (!empty($usuario->telefono)) $fields['aviso.DatosContacto.Telefono'] = $usuario->telefono;
        if (!empty($usuario->email)) $fields['aviso.DatosContacto.Email'] = $usuario->email;
      }
    }

    if (!empty($propiedad->path)) $fields["aviso.fotos[0].url"] = "https://www.varcreative.com/sistema/".$propiedad->path;
    $i = 1;
    foreach($propiedad->images as $image) {
      $fields["aviso.fotos[$i].url"] = "https://www.varcreative.com/sistema/".$image;
      $i++;
    }

    $this->Log_Model->imprimir(array("file"=>"argenprop.txt","id_empresa"=>$id_empresa,"texto"=>"PREPARANDO PARA COMPARTIR: \n".print_r($fields,TRUE)));

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, 'https://www.inmuebles.clarin.com/Publicaciones/Publicar?contentType=json');
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
    $result = curl_exec($ch);
    $this->Log_Model->imprimir(array("file"=>"argenprop.txt","id_empresa"=>$id_empresa,"texto"=>"COMPARTIR: ".$result));

    $array = json_decode($result);
    if (!is_array($array)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$result,
      ));
      exit();
    }
    if (!isset($array[0]) || !is_numeric($array[0])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$result,
      ));
      exit();      
    }
    $id_argenprop = $array[0];

    $url_final = "https://www.argenprop.com/prop--".$id_argenprop;

    // Actualizamos la tabla
    $sql = "UPDATE inm_propiedades SET argenprop_habilitado = 1, argenprop_url = '$url_final' ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id_propiedad ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>$url_final,
    ));
  }


  function suspender_argenprop() {

    $this->load->model("Log_Model");
    $id_empresa = parent::get_empresa();
    $id_propiedad = parent::get_get("id_propiedad");
    $propiedad = $this->modelo->get($id_propiedad,array(
      "id_empresa"=>$id_empresa
    ));
    if (empty($propiedad)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la propiedad con ID: $id_propiedad",
      ));
      exit();
    }

    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($id_empresa);
    if (empty($web_conf->argenprop_usuario) || empty($web_conf->argenprop_password) || empty($web_conf->argenprop_id_vendedor)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Falta configurar las credenciales de Argenprop. Por favor ingreselas en Configuracion / Avanzada / Integracion con Argenprop",
      ));
      exit();
    }

    $headers = array(
      'cache-control' => 'no-cache',
      'content-type' => 'application/x-www-form-urlencoded'
    );

    $id_origen = $propiedad->id."_".$propiedad->id_empresa;
    $usuario_argenprop = $web_conf->argenprop_usuario;
    $password_argenprop = $web_conf->argenprop_password;
    $id_vendedor = $web_conf->argenprop_id_vendedor;

    $fields = array(
      'usr' => $usuario_argenprop,
      'psd' => $password_argenprop,
      'aviso.IdOrigen' => $id_origen,
      'aviso.SistemaOrigen.Id' => '10',
    );

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, 'https://www.inmuebles.clarin.com/Publicaciones/Suspender?contentType=json');
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
    $result = curl_exec($ch);
    $this->Log_Model->imprimir(array("file"=>"argenprop.txt","id_empresa"=>$propiedad->id_empresa,"texto"=>"SUSPENDER: ".$result));

    // Actualizamos la tabla
    $sql = "UPDATE inm_propiedades SET argenprop_habilitado = 2 ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id_propiedad ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function activar_argenprop() {

    $this->load->model("Log_Model");
    $id_empresa = parent::get_empresa();
    $id_propiedad = parent::get_get("id_propiedad");
    $propiedad = $this->modelo->get($id_propiedad,array(
      "id_empresa"=>$id_empresa
    ));
    if (empty($propiedad)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la propiedad con ID: $id_propiedad",
      ));
      exit();
    }

    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($id_empresa);
    if (empty($web_conf->argenprop_usuario) || empty($web_conf->argenprop_password) || empty($web_conf->argenprop_id_vendedor)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Falta configurar las credenciales de Argenprop. Por favor ingreselas en Configuracion / Avanzada / Integracion con Argenprop",
      ));
      exit();
    }

    $headers = array(
      'cache-control' => 'no-cache',
      'content-type' => 'application/x-www-form-urlencoded'
    );

    $id_origen = $propiedad->id."_".$propiedad->id_empresa;
    $usuario_argenprop = $web_conf->argenprop_usuario;
    $password_argenprop = $web_conf->argenprop_password;
    $id_vendedor = $web_conf->argenprop_id_vendedor;

    $fields = array(
      'usr' => $usuario_argenprop,
      'psd' => $password_argenprop,
      'aviso.IdOrigen' => $id_origen,
      'aviso.SistemaOrigen.Id' => '10',
    );

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, 'https://www.inmuebles.clarin.com/Publicaciones/Activar?contentType=json');
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
    $result = curl_exec($ch);
    $this->Log_Model->imprimir(array("file"=>"argenprop.txt","id_empresa"=>$id_empresa,"texto"=>"ACTIVAR: ".$result));

    // Actualizamos la tabla
    $sql = "UPDATE inm_propiedades SET argenprop_habilitado = 1 ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id_propiedad ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function eliminar_argenprop() {

    $this->load->model("Log_Model");
    $id_empresa = parent::get_empresa();
    $id_propiedad = parent::get_get("id_propiedad");
    $propiedad = $this->modelo->get($id_propiedad,array(
      "id_empresa"=>$id_empresa
    ));
    if (empty($propiedad)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la propiedad con ID: $id_propiedad",
      ));
      exit();
    }

    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($id_empresa);
    if (empty($web_conf->argenprop_usuario) || empty($web_conf->argenprop_password) || empty($web_conf->argenprop_id_vendedor)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Falta configurar las credenciales de Argenprop. Por favor ingreselas en Configuracion / Avanzada / Integracion con Argenprop",
      ));
      exit();
    }

    $headers = array(
      'cache-control' => 'no-cache',
      'content-type' => 'application/x-www-form-urlencoded'
    );

    $id_origen = $propiedad->id."_".$propiedad->id_empresa;
    $usuario_argenprop = $web_conf->argenprop_usuario;
    $password_argenprop = $web_conf->argenprop_password;
    $id_vendedor = $web_conf->argenprop_id_vendedor;

    $fields = array(
      'usr' => $usuario_argenprop,
      'psd' => $password_argenprop,
      'aviso.IdOrigen' => $id_origen,
      'aviso.SistemaOrigen.Id' => '10',
    );

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, 'https://www.inmuebles.clarin.com/Publicaciones/DarDeBaja?contentType=json');
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
    $result = curl_exec($ch);
    $this->Log_Model->imprimir(array("file"=>"argenprop.txt","id_empresa"=>$id_empresa,"texto"=>"ELIMINAR: ".$result));

    // Actualizamos la tabla
    $sql = "UPDATE inm_propiedades SET argenprop_habilitado = 0, argenprop_url = '' ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id_propiedad ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }  

  function importar_inmobusqueda() {

    set_time_limit(0);
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    $id_empresa = 1392;
    $cant_insert = 0;
    $this->load->helper("file_helper");
    $this->load->helper("fecha_helper");    

    $this->db->query("DELETE FROM inm_propiedades WHERE id_empresa = $id_empresa");
    $this->db->query("DELETE FROM inm_propiedades_images WHERE id_empresa = $id_empresa");

    // Este script procesa los archivos HTML cacheados
    // y analiza la estructura para volcarlos a la base nuestra

    // Recorremos los archivos TXT dentro de la carpeta cache
    foreach (glob("../importar/inmobusqueda/cache/*.txt") as $link) {

      $html = file_get_contents($link);

      $propiedad = new stdClass();
      $propiedad->id_empresa = $id_empresa;
      $propiedad->inmobusquedas_habilitado = 0;
      $propiedad->inmobusquedas_url = $link;
      $imagenes = array();
      $propiedad->latitud = 0;
      $propiedad->longitud = 0;
      $propiedad->zoom = 16;

      // Procesamos las lineas del archivo para encontrar la latitud y longitud
      $lineas = explode("\n", $html);
      foreach($lineas as $l) {
        if (strpos($l, "center: [") !== FALSE) {
          $l = str_replace("center: [", "", $l);
          $l = str_replace("],", "", $l);
          $pos = explode(", ", $l);
          $propiedad->latitud = trim($pos[0]);
          $propiedad->longitud = trim($pos[1]);
          break;
        }
      }

      $dom = new DOMDocument();
      libxml_use_internal_errors(true);
      $dom->loadHTML($html);
      $finder = new DomXPath($dom);

      // Buscamos las fotos
      $nodes = $finder->query("//div[@id='listadoFo3tos']//a");
      foreach ($nodes as $node) {
        $imagen = $node->getAttribute("href");
        $cc = explode("/", $imagen);
        $filename = end($cc);
        grab_image($imagen,"../importar/inmobusqueda/cache/$filename");
        $imagenes[] = "uploads/$id_empresa/propiedades/".$filename;
      }

      $propiedad->path = (sizeof($imagenes)>0) ? $imagenes[0] : "";
      $propiedad->nombre = "";
      $propiedad->superficie_total = "";
      $propiedad->direccion = "";
      $propiedad->precio_final = "";
      $propiedad->ciudad = "";

      // Buscamos el nombre
      $nodes = $finder->query("//div[@class='nombresobreslide']");
      foreach ($nodes as $node) {
        $i=0;
        foreach($node->childNodes as $c) {
          if ($i==0) $propiedad->nombre = trim($c->textContent);
          else if ($i==1) $propiedad->superficie_total = trim($c->textContent);
          else if ($i==3) {
            // Direccion y precio
            $j = 0;
            if ($c->hasChildNodes()) {
              foreach($c->childNodes as $cc) {
                if ($j == 0) $propiedad->direccion = trim($cc->textContent);
                else if ($j == 2) $propiedad->precio_final = trim($cc->textContent);
                $j++;
              }
            }
          } else if ($i==5) {
            // Ciudad
            $propiedad->ciudad = $c->textContent;
          }
          $i++;
        }
      }

      $propiedad->atributos = array();

      $nodes = $finder->query("//*[contains(@class, 'detalleizquierda')]");
      foreach ($nodes as $node) {
        $propiedad->atributos[] = array(
          "propiedad"=>$node->textContent,
        );
        $node->parentNode->removeChild($node);
      }

      $nodes = $finder->query("//*[contains(@class, 'detallederecha')]");
      $i=0;
      foreach ($nodes as $node) {
        $propiedad->atributos[$i]["valor"] = $node->textContent;
        $node->parentNode->removeChild($node);
        $i++;
      }

      $nodes = $finder->query("//*[contains(@class, 'descripcion')]");
      $i=0;
      $propiedad->texto = "";
      foreach ($nodes as $node) {
        if ($i==1) { 
          foreach($node->childNodes as $c) {
            if ($c->nodeName == "#text") $propiedad->texto.= $c->textContent; 
          }
          break;
        }
        $i++;
      }
      $propiedad->texto = trim($propiedad->texto);

      // Si el titulo tiene la palabra Venta
      if (strpos($propiedad->nombre, "Venta")>0) {
        $propiedad->id_tipo_operacion = 1;
      } else $propiedad->id_tipo_operacion = 2;

      // Dependiendo de las palabras clave del titulo
      $propiedad->id_tipo_inmueble = 0;
      if (strpos($propiedad->nombre, "Casa") !== FALSE) {
        $propiedad->id_tipo_inmueble = 1;
      } else if (strpos($propiedad->nombre, "Departamento") !== FALSE) {
        $propiedad->id_tipo_inmueble = 2;
      } else if (strpos($propiedad->nombre, "PH") !== FALSE) {
        $propiedad->id_tipo_inmueble = 3;
      } else if (strpos($propiedad->nombre, "Lote") !== FALSE) {
        $propiedad->id_tipo_inmueble = 7;
      } else if (strpos($propiedad->nombre, "Campo") !== FALSE) {
        $propiedad->id_tipo_inmueble = 6;
      } else if (strpos($propiedad->nombre, "Terreno") !== FALSE) {
        $propiedad->id_tipo_inmueble = 7;
      } else if (strpos($propiedad->nombre, "Galpon") !== FALSE || strpos($propiedad->nombre, "Galpón") !== FALSE) {
        $propiedad->id_tipo_inmueble = 8;
      } else if (strpos($propiedad->nombre, "Local") !== FALSE) {
        $propiedad->id_tipo_inmueble = 9;
      } else if (strpos($propiedad->nombre, "Oficina") !== FALSE) {
        $propiedad->id_tipo_inmueble = 11;
      } else if (strpos($propiedad->nombre, "Cochera") !== FALSE) {
        $propiedad->id_tipo_inmueble = 13;
      } else if (strpos($propiedad->nombre, "Monoambiente") !== FALSE) {
        $propiedad->id_tipo_inmueble = 2;
      } else if (strpos($propiedad->nombre, "Deposito") !== FALSE || strpos($propiedad->nombre, "Depósito") !== FALSE) {
        $propiedad->id_tipo_inmueble = 18;
      } else if (strpos($propiedad->nombre, "Duplex") !== FALSE || strpos($propiedad->nombre, "Dúplex") !== FALSE) {
        $propiedad->id_tipo_inmueble = 15;
      }

      // Acomodamos el precio y la moneda
      $propiedad->moneda = '$';
      $propiedad->publica_precio = 1;
      if (strpos($propiedad->precio_final, 'u$d') !== FALSE) {
        $propiedad->moneda = 'U$S';
        $propiedad->precio_final = str_replace('u$d', '', $propiedad->precio_final);
      } else if (strpos($propiedad->precio_final, "Consulte") !== FALSE) {
        $propiedad->publica_precio = 0;
        $propiedad->precio_final = 0;
      }
      $propiedad->precio_final = str_replace("$", "", $propiedad->precio_final);
      $propiedad->precio_final = str_replace(".", "", $propiedad->precio_final);
      $precio = explode(" ", $propiedad->precio_final);
      $propiedad->precio_final = $precio[0];

      // La superficie total puede tener la cantidad de dormitorios tmb
      $subtitulo = explode("   ", $propiedad->superficie_total);
      $propiedad->dormitorios = "";
      if (sizeof($subtitulo)>1) {
        $propiedad->dormitorios = $subtitulo[0];
        $propiedad->dormitorios = str_replace(" Dorm", "", $propiedad->dormitorios);
        $propiedad->superficie_total = $subtitulo[1];
        $propiedad->superficie_total = str_replace(" mts", "", $propiedad->superficie_total);
        $propiedad->superficie_total = str_replace(",", ".", $propiedad->superficie_total);
      }

      $propiedad->activo = 1;
      $propiedad->id_tipo_estado = 1; // Por defecto todas activas
      $propiedad->fecha_ingreso = date("Y-m-d");

      // Analizamos la ciudad
      $propiedad->id_localidad = 0;
      $propiedad->id_departamento = 0;
      if (strpos($propiedad->ciudad, "casco urbano") !== FALSE) {
        $propiedad->id_localidad = 513;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Romero") !== FALSE) {
        $propiedad->id_localidad = 791;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Ringuelet") !== FALSE) {
        $propiedad->id_localidad = 776;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "San Lorenzo") !== FALSE) {
        $propiedad->id_localidad = 5503;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Villa Elvira") !== FALSE) {
        $propiedad->id_localidad = 5117;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Berisso") !== FALSE) {
        $propiedad->id_localidad = 5492;
        $propiedad->id_departamento = 66;
      } else if (strpos($propiedad->ciudad, "Gonnet") !== FALSE) {
        $propiedad->id_localidad = 396;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Hernández") !== FALSE) {
        $propiedad->id_localidad = 425;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Ensenada") !== FALSE) {
        $propiedad->id_localidad = 312;
        $propiedad->id_departamento = 117;
      } else if (strpos($propiedad->ciudad, "Gorina") !== FALSE) {
        $propiedad->id_localidad = 401;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Elisa") !== FALSE) {
        $propiedad->id_localidad = 946;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "San Carlos") !== FALSE) {
        $propiedad->id_localidad = 5505;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Tolosa") !== FALSE) {
        $propiedad->id_localidad = 900;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Etcheverry") !== FALSE) {
        $propiedad->id_localidad = 326;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Correas") !== FALSE) {
        $propiedad->id_localidad = 244;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Abasto") !== FALSE) {
        $propiedad->id_localidad = 10;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Arana") !== FALSE) {
        $propiedad->id_localidad = 56;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Olmos") !== FALSE) {
        $propiedad->id_localidad = 674;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Garibaldi") !== FALSE) {
        $propiedad->id_localidad = 948;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "El Peligro") !== FALSE) {
        $propiedad->id_localidad = 5502;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Hornos") !== FALSE) {
        $propiedad->id_localidad = 5504;
        $propiedad->id_departamento = 9;
      } else if (strpos($propiedad->ciudad, "Hermosura") !== FALSE) {
        $propiedad->id_localidad = 5506;
        $propiedad->id_departamento = 9;
      }

      // Analizamos algunos atributos mas
      $propiedad->banios = 0;
      $propiedad->cocheras = 0;
      $propiedad->servicios_cloacas = 0;
      $propiedad->servicios_agua_corriente = 0;
      $propiedad->servicios_asfalto = 0;
      $propiedad->servicios_electricidad = 0;
      $propiedad->servicios_telefono = 0;
      $propiedad->servicios_cable = 0;
      $propiedad->superficie_cubierta = 0;
      $propiedad->ambientes = 0;

      foreach($propiedad->atributos as $atributo) {
        if ($atributo["propiedad"] == "Baños") {
          $propiedad->banios = $atributo["valor"];
        } else if ($atributo["propiedad"] == "Garage") {
          if ($atributo["valor"] == "Si" || strlen($atributo["valor"])>2) $propiedad->cocheras = 1;
        } else if ($atributo["propiedad"] == "Cloacas") {
          if ($atributo["valor"] == "Si") $propiedad->servicios_cloacas = 1;
        } else if ($atributo["propiedad"] == "Agua") {
          if ($atributo["valor"] == "Si") $propiedad->servicios_agua_corriente = 1;
        } else if ($atributo["propiedad"] == "Asfalto") {
          if ($atributo["valor"] == "Si") $propiedad->servicios_asfalto = 1;
        } else if ($atributo["propiedad"] == "Energia") {
          if ($atributo["valor"] == "Si") $propiedad->servicios_electricidad = 1;
        } else if ($atributo["propiedad"] == "Teléfono") {
          if ($atributo["valor"] == "Si") $propiedad->servicios_telefono = 1;
        } else if ($atributo["propiedad"] == "Cable") {
          if ($atributo["valor"] == "Si") $propiedad->servicios_cable = 1;
        } else if ($atributo["propiedad"] == "Superficie Construida") {
          $s = explode(" ", $atributo["valor"]);
          $propiedad->superficie_cubierta = $s[0];
        } else if ($atributo["propiedad"] == "Ambientes") {
          $propiedad->ambientes = (($atributo["valor"] == "-") ? 0 : $atributo["valor"]);
        }
      }
    
      // INSERTAMOS EL OBJETO
      $insert_id = $this->modelo->save($propiedad);
      $hash = md5($insert_id);

      // Actualizamos el link
      $propiedad->link = "propiedad/".filename($propiedad->nombre,"-",0)."-".$insert_id."/";
      $this->db->query("UPDATE inm_propiedades SET link = '$propiedad->link', hash='$hash' WHERE id = $insert_id AND id_empresa = $id_empresa");

      // INSERTAMOS LAS IMAGENES
      $k=0;
      foreach($imagenes as $im) {
        $this->db->query("INSERT INTO inm_propiedades_images (id_empresa,id_propiedad,path,orden,plano) VALUES($id_empresa,$insert_id,'$im',$k,0)");
        $k++;
      }
      $cant_insert++;
    }
    echo "TERMINO $cant_insert";
  }

    
}