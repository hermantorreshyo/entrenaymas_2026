<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cipal extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Cliente_Model', 'modelo');
  }

  function ver_invitaciones_por_empresa() {
    $id_empresa = 259;
    $empresas = array();
    $sql = "SELECT * FROM cipal_invitaciones ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $sql = "SELECT * FROM clientes WHERE UPPER(observaciones) LIKE '%$r->codigo%' ";
      //echo $r->empresa." ".$sql."<br/>";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        $usado = 1;
      } else {
        $usado = 0;
      }
      if (isset($empresas[$r->empresa])) {
        $empresas[$r->empresa]["usados"] = $empresas[$r->empresa]["usados"] + $usado;
        $empresas[$r->empresa]["total"] = $empresas[$r->empresa]["total"] + 1;
      } else {
        $empresas[$r->empresa] = array(
          "usados"=>$usado,
          "total"=>1,
        );
      }
    }
    foreach($empresas as $key => $value) {
      echo $key.";".$value["total"].";".$value["usados"]."\n";
    }
  }  

  function validar_codigo() {
    $id_empresa = 259;
    $codigo = parent::get_post("codigo","");
    $sql = "SELECT * FROM cipal_invitaciones WHERE codigo = '$codigo' AND id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

      // El codigo existe, validamos si ya fue usado
      $sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa AND custom_1 = '$codigo' ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        echo json_encode(array(
          "valido"=>0,
          "mensaje"=>"El codigo ya fue utilizado",
        ));
        exit();
      }
      // El codigo existe y no fue utilizado
      echo json_encode(array(
        "valido"=>1,
      ));
    } else {
      // El codigo no existe
      echo json_encode(array(
        "valido"=>0,
        "mensaje"=>"El codigo no existe",
      ));
    }    
  }

  function ver_invitaciones() {
    $id_empresa = 259;
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",10);
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM cipal_invitaciones WHERE id_empresa = $id_empresa AND empresa != '' ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    echo json_encode(array(
      "results"=>$q->result(),
      "total"=>$total->total,
    ));
  }

  function ver_pdf_por_codigo() {
    $id_empresa = 259;
    $codigo = parent::get_get("codigo","");
    $sql = "SELECT * FROM cipal_invitaciones WHERE codigo = '$codigo' AND id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $r = $q->row();
      // Generamos el PDF
      $cache_dir = '/home/ubuntu/data/cache';
      require_once('/home/ubuntu/data/vendor/autoload.php');
      $mpdf = new \Mpdf\Mpdf([
        'tempDir' => $cache_dir
      ]);
      $data_query = array(
        "id"=>$r->codigo,
        "client"=>$r->empresa,
      );
      $url = "https://www.cipal.com.ar/web/invitacion/?".http_build_query($data_query);
      $html = file_get_contents($url);
      $mpdf->CSSselectMedia='mpdf';
      $mpdf->setBasePath($url);
      $mpdf->WriteHTML($html);
      $mpdf->Output("Invitacion CIPAL 2019 - $empresa.pdf", \Mpdf\Output\Destination::DOWNLOAD);
    }
  }

  function descargar_pdf_invitacion() {
    $id_empresa = 259;
    $empresa = urldecode(parent::get_get("empresa",""));
    $sql = "SELECT * FROM cipal_invitaciones WHERE empresa = '' AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $this->db->query("UPDATE cipal_invitaciones SET empresa = '$empresa' WHERE id = $r->id AND id_empresa = $id_empresa ");

      // Generamos el PDF
      $cache_dir = '/home/ubuntu/data/cache';
      require_once('/home/ubuntu/data/vendor/autoload.php');
      $mpdf = new \Mpdf\Mpdf([
        'tempDir' => $cache_dir
      ]);
      $data_query = array(
        "id"=>$r->codigo,
        "client"=>$empresa,
      );
      $url = "https://www.cipal.com.ar/web/invitacion/?".http_build_query($data_query);
      $html = file_get_contents($url);
      $mpdf->CSSselectMedia='mpdf';
      $mpdf->setBasePath($url);
      $mpdf->WriteHTML($html);
      $mpdf->Output("Invitacion CIPAL 2019 - $empresa.pdf", \Mpdf\Output\Destination::DOWNLOAD);
    } else {
      echo "NO HAY MAS INVITACIONES DISPONIBLES.";
    }
  }

  function create_pdf($linea) {
    $cache_dir = '/home/ubuntu/data/cache';
    require_once('/home/ubuntu/data/vendor/autoload.php');    
    $mpdf = new \Mpdf\Mpdf([
      'tempDir' => $cache_dir,
      'format' => 'A4-L',
      'margin_left' => 0,
      'margin_right' => 0,
      'margin_top' => 0,
      'margin_bottom' => 0,
      'margin_header' => 0,
      'margin_footer' => 0,        
    ]);
    $data_query = array(
      "nombre"=>$linea,
    );
    $archivo = mb_convert_encoding(str_replace(" ", "-", $linea), 'ISO-8859-1', 'UTF-8');
    $url = "https://www.cipal.com.ar/web/certificado/?".http_build_query($data_query);
    $html = file_get_contents($url);
    $mpdf->CSSselectMedia='mpdf';
    $mpdf->setBasePath($url);
    $mpdf->WriteHTML($html);
    $mpdf->Output("/home/ubuntu/data/cache/".$archivo.".pdf", \Mpdf\Output\Destination::FILE);
    unset($mpdf);
  }

  function descargar_pdf_certificado() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $i = 0;
    $cipal = file_get_contents("cipal.txt");
    $lineas = explode("\n", $cipal);
    foreach($lineas as $linea) {
      // Generamos el PDF
      $linea = trim($linea);
      $linea = str_replace("  ", " ", $linea);
      echo $linea."<br/>";
      $this->create_pdf($linea);
      usleep(1000000);
      $i++;
    }
    echo "Archivos generados: $i";
  }  

  // Funcion para generar las invitaciones
  function generar_codigos() {
    $id_empresa = 259;
    for($i=0;$i<250;$i++) {
      $pass = strtoupper(substr(md5(uniqid(mt_rand(), true)),0,6));
      $sql = "INSERT INTO cipal_invitaciones (id_empresa,codigo) VALUES ($id_empresa,'$pass') ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }

  function generar_qr_link($id_cliente) {
    $id_empresa = parent::get_empresa();
    if ($id_empresa == 403) {
      $url_base = "https://aqfeed.info";
    } else {
      $url_base = "https://mymag.info";
    }
    $cliente = $this->modelo->get($id_cliente,$id_empresa,array(
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $link = $url_base."/sistema/qr/registrar/?e=".$id_empresa."&c=".$id_cliente."&l=".urlencode($cliente->codigo_postal);

    // Primero buscamos si ya hay una redireccion con ese mismo link
    $sql = "SELECT id FROM qr_redirecciones WHERE link = '$link' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $id = $r->id;
    } else {
      // Buscamos el proximo ID
      $sql = "SELECT MAX(id) AS id FROM qr_redirecciones ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $id = is_null($r->id) ? 1 : ($r->id + 1);
      // Insertamos
      $sql = "INSERT INTO qr_redirecciones (id,link) VALUES ('$id','$link') ";
      $this->db->query($sql);
    }
    echo json_encode(array(
      "link"=>$url_base."/e/".$id,
    ));
  }

  function generar_qr($id_cliente) {
    $id_empresa = parent::get_empresa();
    if ($id_empresa == 403) {
      $url_base = "https://aqfeed.info";
    } else {
      $url_base = "https://mymag.info";
    }
    $cliente = $this->modelo->get($id_cliente,$id_empresa,array(
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $output = "png";
    $link = $url_base."/sistema/qr/registrar/?e=".$id_empresa."&c=".$id_cliente."&l=".urlencode($cliente->codigo_postal);
    require APPPATH.'libraries/phpqrcode/qrlib.php';
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="QR '.$empresa->nombre.' '.$cliente->nombre.'.png"');
    QRcode::png($link);
  }

  function upload_files($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_files(array(
      "id_empresa"=>$id_empresa,
      "upload_dir"=>"uploads/$id_empresa/",
    ));
  }


  function registrar() {

    header('Access-Control-Allow-Origin: *');
    header('Content-Type:application/json; charset=UTF-8');

    $id_empresa = 259;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    @file_put_contents("log_registro.txt", date("Y-m-d H:i:s").": ".print_r($_POST,true)."\n\n", FILE_APPEND);

    $email = parent::get_post("email","");
    if (empty($email)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El email es obligatorio",
      ));
      exit();
    }
    $nombre = parent::get_post("nombre","");
    $apellido = parent::get_post("apellido","");
    $telefono = parent::get_post("telefono","");
    $pais = parent::get_post("pais","");
    $empresa_cliente = parent::get_post("empresa","");
    $puesto_cliente = parent::get_post("puesto","");
    $tipo = parent::get_post("tipo",1);
    $tipo_pago = parent::get_post("tipo_pago","");
    $invitacion = parent::get_post("invitacion","");

    // Si estamos usando reCAPTCHA
    $captcha = $this->input->post("g-recaptcha-response");
    if ($captcha !== FALSE) {
      require_once APPPATH.'libraries/recaptchalib.php';
      $site_key = "6LeHSTQUAAAAAA5FV121v-M7rnhqdkXZIGmP9N8E";
      $secret = "6LeHSTQUAAAAACG9dCyy6hv24tlRYL8TKtxe4O54";
      $reCaptcha = new ReCaptcha($secret);
      $resp = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $captcha
      );
      if ($resp == null || !isset($resp->success) || $resp->success === FALSE) {
        $salida = array(
          "mensaje"=>"El codigo de Captcha es incorrecto.",
          "error"=>1,
        );
        echo json_encode($salida);
        exit();
      }
    }

    /*
    $sql = "SELECT * FROM clientes ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND email = '$email' ";
    $sql.= "AND tipo >= '3' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()==0) {
      */

      $fecha = date("Y-m-d H:i:s");
      // Debemos guardar el cliente
      $cliente = new stdClass();
      $forma_pago = "E";
      $fecha_inicial = $fecha;
      $fecha_ult_operacion = $fecha;
      $sql = "INSERT INTO clientes (";
      $sql.= " nombre,direccion,email,telefono,celular,fax,localidad,password,id_empresa,tipo,activo,uploaded,forma_pago,fecha_inicial,fecha_ult_operacion,custom_1";
      $sql.= ") VALUES(";
      $sql.= " '$nombre','$apellido','$email','$telefono','$empresa_cliente','$puesto_cliente','$pais','','$id_empresa','$tipo','1','1','$forma_pago','$fecha_inicial','$fecha_ult_operacion','$invitacion')";
      $this->db->query($sql);
      $id_cliente = $this->db->insert_id();
      $salida = array(
        "id"=>$id_cliente,
        "error"=>0,
      );   

      if ($tipo_pago == "INVITACION") {

        // Generate PDF file
        $cache_dir = '/home/ubuntu/data/cache';
        require_once('/home/ubuntu/data/vendor/autoload.php');
        $mpdf = new \Mpdf\Mpdf([
          'tempDir' => $cache_dir
        ]);
        $data_query = array(
          "id"=>$id_cliente,
          "client"=>$nombre." ".$apellido,
        );
        $pdf_filename = $cache_dir.'/'.$id_cliente.".pdf";
        $url = "https://www.cipal.com.ar/web/pdf/?".http_build_query($data_query);
        $html = file_get_contents($url);
        $mpdf->CSSselectMedia='mpdf'; // assuming you used this in the document header
        $mpdf->setBasePath($url);
        $mpdf->WriteHTML($html);
        $mpdf->Output($pdf_filename, \Mpdf\Output\Destination::FILE);   

        // Actualizamos la observacion del cliente
        $sql = "UPDATE clientes SET observaciones = 'INVITACION $invitacion' WHERE id_empresa = $id_empresa AND id = $id_cliente ";
        $this->db->query($sql);

        // Enviamos el email de registro
        $this->load->model("Email_Template_Model");
        $template = $this->Email_Template_Model->get_by_key("registro-cipal",$id_empresa);
        if ($template !== FALSE) {
          $bcc_array = array("basile.matias99@gmail.com","porcelp@gmail.com");
          require APPPATH.'libraries/Mandrill/Mandrill.php';
          $body = $template->texto;
          $body = str_replace("{{nombre}}", $nombre, $body);
          $body = str_replace("{{apellido}}", $apellido, $body);
          mandrill_send(array(
            "to"=>$email,
            "from"=>MAIL_FROM_ADDRESS,
            "from_name"=>"CIPAL 2019",
            "subject"=>$template->nombre,
            "body"=>$body,
            "reply_to"=>$empresa->email,
            "bcc"=>$bcc_array,
            "attachments"=>array($pdf_filename),
          ));
        }
      }

    /*} else {
      // El cliente ya existe
      $salida = array(
        "mensaje"=>"Ya existe un registro con el email $email",
        "error"=>1,
      );
    }*/
    echo json_encode($salida);
  }

  function get($id) {
    
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {

      $order_by = ($this->input->get("order_by") !== FALSE) ? $this->input->get("order_by")." " : "";
      $order = ($this->input->get("order") !== FALSE) ? $this->input->get("order") : "";
      $filter = ($this->input->get("term") !== FALSE) ? urldecode($this->input->get("term")) : "";
      $codigo_propiedad = ($this->input->get("codigo_propiedad") !== FALSE) ? urldecode($this->input->get("codigo_propiedad")) : "";
      $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? urldecode($this->input->get("id_usuario")) : 0;
      $custom_3 = parent::get_get("custom_3","");
      $custom_4 = parent::get_get("custom_4","");
      $custom_5 = parent::get_get("custom_5","");
      $tipo = ($this->input->get("tipo") !== FALSE) ? $this->input->get("tipo") : -1;
      $id_vendedor = parent::get_get("id_vendedor",0);
      $id_etiqueta = parent::get_get("id_etiqueta",0);
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");
      $buscar_respuesta = ($this->input->get("buscar_respuesta") !== FALSE) ? $this->input->get("buscar_respuesta") : 0;

      $r = $this->modelo->buscar(array(
        "filter"=>$filter,
        "codigo_propiedad"=>$codigo_propiedad,
        "order"=>$order_by.$order,
        "limit"=>$limit,
        "tipo"=>$tipo,
        "id_vendedor"=>$id_vendedor,
        "id_etiqueta"=>$id_etiqueta,
        "activo"=>(($id_empresa == 70)?1:-1),
        "offset"=>$offset,
        "id_usuario"=>$id_usuario,
        "buscar_respuesta"=>$buscar_respuesta,
        "custom_3"=>$custom_3,
        "custom_4"=>$custom_4,
        "custom_5"=>$custom_5,
      ));
      echo json_encode($r);

    } else {

      $id_sucursal = parent::get_get("id_sucursal",0);
      $cliente = $this->modelo->get($id,$id_empresa,array(
        "id_sucursal"=>$id_sucursal
      ));
      echo json_encode($cliente);
    }
  }    

  function insert() {
    $array = $this->parse_put();
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    if (isset($array->fecha_inicial)) $array->fecha_inicial = fecha_mysql($array->fecha_inicial);
    else $array->fecha_inicial = date("Y-m-d");
    if (isset($array->fecha_ult_operacion)) $array->fecha_ult_operacion = fecha_mysql($array->fecha_ult_operacion);
    if (empty($array->fecha_ult_operacion)) $array->fecha_ult_operacion = date("Y-m-d H:i:s");
    if (isset($array->fecha_vencimiento)) $array->fecha_vencimiento = fecha_mysql($array->fecha_vencimiento);
    $array->id_empresa = $id_empresa;
    $array->cuit = str_replace("-","",$array->cuit);
    $array->cuit = str_replace(" ","",$array->cuit);

    // Controlamos si el codigo ya existe
    $codigo = trim($array->codigo);
    if (!empty($codigo)) {
      $q = $this->db->query("SELECT * FROM clientes WHERE codigo = '$array->codigo' AND id_empresa = $id_empresa");
      if ($q->num_rows()>0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: Ya existe un cliente con el codigo $array->codigo."
          ));
        return;
      }
    }

    // Controlamos si el CUIT ya existe
    /*
    $cuit = trim($array->cuit);
    if (!empty($cuit)) {
        $q = $this->db->query("SELECT * FROM clientes WHERE cuit = '$array->cuit' AND id_empresa = $id_empresa");
        if ($q->num_rows()>0) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"ERROR: El cuit es repetido con otro cliente."
            ));
            return;
        }
    }
    */

    $etiquetas = $array->etiquetas;
    unset($array->etiquetas);

    // Dependiendo de la configuracion del sistema, si es LOCAL o NO
    $this->load->model("Configuracion_Model");
    $array->uploaded = ($this->Configuracion_Model->es_local()==1)?0:1;
    
    $id = $this->modelo->insert($array);

    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $i=1;
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $array->id_empresa;
      $tag->id_cliente = $id;
      $tag->nombre = $e;
      $tag->orden = $i;
      $this->modelo->save_tag($tag);
      $i++;
    }

    echo json_encode(array(
      "id"=>$id,
      "error"=>0
    ));
  }

  function update($id) {

    if ($id == 0) { $this->insert($id); return; }
    $id_empresa = parent::get_empresa();
    $array = $this->parse_put();
    $etiquetas = $array->etiquetas;
    unset($array->etiquetas);
    $this->load->helper("fecha_helper");
    $array->fecha_inicial = fecha_mysql($array->fecha_inicial);
    $array->fecha_ult_operacion = fecha_mysql($array->fecha_ult_operacion);
    $array->fecha_vencimiento = fecha_mysql($array->fecha_vencimiento);
    $array->id_empresa = $id_empresa;
    $array->cuit = str_replace("-","",$array->cuit);
    $array->cuit = str_replace(" ","",$array->cuit);        

    // Controlamos que el CUIT no exista
    /*
    $cuit = trim($array->cuit);
    if (!empty($cuit)) {
        $q = $this->db->query("SELECT * FROM clientes WHERE cuit = '$array->cuit' AND id != $id AND id_empresa = $id_empresa");
        if ($q->num_rows()>0) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"ERROR: El cuit es repetido con otro cliente."
            ));
            return;
        }
    }
    */
        
    // Controlamos si el codigo ya existe
    $codigo = trim($array->codigo);
    if (!empty($codigo)) {
      $q = $this->db->query("SELECT * FROM clientes WHERE codigo = '$array->codigo' AND id != $id AND id_empresa = $id_empresa");
      if ($q->num_rows()>0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: El codigo es repetido con otro cliente."
          ));
        return;
      }
    }
    $this->modelo->save($array);

    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $i=1;
    $this->db->query("DELETE FROM clientes_etiquetas_relacion WHERE id_cliente = $id AND id_empresa = $array->id_empresa");
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $array->id_empresa;
      $tag->id_cliente = $id;
      $tag->nombre = $e;
      $tag->orden = $i;
      $this->modelo->save_tag($tag);
      $i++;
    }        

    echo json_encode(array(
      "id"=>$id,
      "error"=>0
    ));
  }

  function next() {
    $id_empresa = parent::get_empresa();
    $codigo = $this->modelo->next();
    echo json_encode(array(
      "codigo"=>$codigo,
    ));
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/";
    $filename = $this->input->post("file");
    $res = parent::save_image($dir,$filename);

    $thumbnail_width = $this->input->post("thumbnail_width");
    if (!empty($thumbnail_width)) {
      $resp = json_decode($res);
      $filename = str_replace($dir, "", $resp->path);
      $thumbnail_width = $this->input->post("thumbnail_width");
      $thumbnail_height = $this->input->post("thumbnail_height");
      parent::thumbnails(array(
        "dir"=>$dir,
        "preffix"=>"thumb_",
        "filename"=>$filename,
        "thumbnail_width"=>$thumbnail_width,
        "thumbnail_height"=>$thumbnail_height,                
      ));
    }
    echo $res;
  }

  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $s = $this->modelo->buscar(array(
      "filter"=>$nombre,
    ));
    $resultado = array();
    foreach($s["results"] as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->codigo;
      $rr->label = $r->nombre;
      $rr->info = (!empty($r->direccion)) ? $r->direccion.((!empty($r->localidad))?" - ".$r->localidad : "") : "";
      $rr->nombre = $r->nombre;
      $rr->email = $r->email;
      $rr->telefono = $r->telefono;
      $rr->id_sucursal = $r->id_sucursal;
      $resultado[] = $rr;
    }            
    echo json_encode($resultado);
  }

  function get_by_codigo() {
    $id_empresa = parent::get_empresa();
    $codigo = $this->input->get("codigo");
    $s = $this->modelo->get_by_codigo($codigo);
    echo json_encode($s);
  }    

  function get_by_descripcion() {
    $id_empresa = parent::get_empresa();
    $descripcion = $this->input->get("term");
    $sql = "SELECT A.* ";
    $sql.= "FROM clientes A ";
    $sql.= "WHERE A.nombre LIKE '%$descripcion%' ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->id_real = $r->id;
      $rr->value = $r->id;
      $rr->label = $r->nombre;
      $rr->path = $r->path;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }    



  function get_info($codigo) {

    $id_empresa = parent::get_empresa();
    
    // Consumidor final
    if ($codigo == 0) {
      $row = new stdClass();
      $row->id_tipo_iva = 4;
      $row->nombre = "Consumidor Final";
      $row->cuit = "";
      $row->saldo = 0;
      $row->email = "";
      $row->direccion = "";
      $row->percibe_ib = 0;
      $row->descuento = 0;
      $row->error = 0;
      $row->id_vendedor = 0;
      $row->lista = 0;
      $row->forma_pago = "E";
      echo json_encode($row);
      return;
    }
    
    // Obtenemos el cliente
    $row = $this->modelo->get_by_codigo($codigo);
    if ($row == FALSE) { echo json_encode(array("error"=>1,"mensaje"=>"No existe un cliente con el codigo '$codigo'")); return; }
    if ($row->activo == 0) { echo json_encode(array("error"=>1,"mensaje"=>"El cliente $row->nombre esta desactivado.")); return; }
    $row->error = 0;
    $row->mensaje = "";
    $row->saldo = $this->modelo->saldo($row->id);
    echo json_encode($row);
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
    $path = "uploads/$id_empresa/$filename";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
    echo json_encode(array(
      "path"=>$path,
      "error"=>0,
    ));
  } 
    
  function exportar_registros_cipal() {

    // 3 = SALON COMERCIAL
    // 4 = SALON CONFERENCIAS
    // 5 = STAFF
    // 6 = EXPOSITORES
    // 7 = CONFERENCISTAS
    $id_empresa = 259;
    $sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa AND tipo >= 3 ";
    $q = $this->db->query($sql);
    $datos = array();
    foreach($q->result() as $r) {
      $tipo = "";
      if ($r->tipo == 3) $tipo = "SALON COMERCIAL";
      else if ($r->tipo == 4) $tipo = "SALON CONFERENCIAS";
      else if ($r->tipo == 5) $tipo = "STAFF";
      else if ($r->tipo == 6) $tipo = "EXPOSITORES";
      else if ($r->tipo == 7) $tipo = "CONFERENCISTAS";
      $datos[] = array(
        "id"=>$r->id,
        "nombre"=>$r->nombre,
        "apellido"=>$r->direccion,
        "pais"=>$r->localidad,
        "email"=>$r->email,
        "telefono"=>$r->telefono,
        "empresa"=>$r->celular,
        "puesto"=>$r->fax,
        "tipo"=>$tipo,
      );
    }
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>"",
      "filename"=>"registros",
      "footer"=>array(),
      "header"=>array("ID","Nombre","Apellido","Pais","Email","Telefono","Empresa","Puesto","Tipo"),
      "data"=>$datos,
      "title"=>"",
    ));
  }
}