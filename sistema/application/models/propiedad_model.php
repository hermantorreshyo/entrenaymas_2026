<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Propiedad_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("inm_propiedades","id");
  }
  
  // Controla si la empresa puede seguir creando propiedades o no
  function controlar_plan($id_empresa) {
    $sql = "SELECT * FROM empresas WHERE id = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      return array(
        "error"=>1,
        "mensaje"=>"La empresa con ID $id_empresa no existe",
      );
    }
    $empresa = $q->row();
    if ($empresa->limite == 0) return TRUE;
    else {
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM inm_propiedades ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $qqq = $this->db->query($sql);
      $rrr = $qqq->row();
      $cantidad = $rrr->cantidad;
      if ($cantidad < $empresa->limite) {
        return TRUE;
      } else {

        // Lanzamos un email pidiendo que actualice el plan
        $this->load->model("Email_Template_Model");
        $temp = $this->Email_Template_Model->get_by_key("cambiar-plan",118);
        $bcc_array = array("basile.matias99@gmail.com","misticastudio@gmail.com");
        $texto = $temp->texto;
        $texto = str_replace("{{nombre}}", $empresa->nombre, $texto);
        require APPPATH.'libraries/Mandrill/Mandrill.php';
        mandrill_send(array(
          "to"=>$empresa->email,
          "subject"=>$temp->nombre,
          "body"=>$texto,
          "bcc"=>$bcc_array,
        ));

        // Devolvemos el error
        return array(
          "error"=>1,
          "mensaje"=>"Ha llegado al limite de su plan contratado. Por favor comuniquese para actualizar al plan siguiente.",
        );        
      }
    }
  }  

  function count_all() {
    if ($this->usa_id_empresa == 1) {
      $id_empresa = $this->get_empresa();
      $this->db->where("id_empresa",$id_empresa);
    }
    $this->db->where("de_prueba","0");
    $this->db->from($this->tabla);
    $r = $this->db->count_all_results();
    $this->db->close();
    return $r;
  }  

  function sincronizar_calendario($config = array()) {
    $icalfile = isset($config["link"]) ? $config["link"] : "";
    $id_propiedad = isset($config["id_propiedad"]) ? $config["id_propiedad"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : 0;
    require_once APPPATH.'libraries/icalendar/zapcallib.php';
    $this->load->model("Propiedad_Reserva_Model");

    $urlbase = $icalfile;
    $urlbase = str_replace("https://", "", $urlbase);
    $urlbase = str_replace("http://", "", $urlbase);
    $b = explode("/", $urlbase);
    $urlbase = $b[0];

    $c = curl_init($icalfile);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $icalfeed = curl_exec($c);
    if (curl_error($c)) die(curl_error($c));
    $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);

    if (empty($icalfeed)) {
      // Intentamos con file_get_contents
      $icalfeed = file_get_contents($icalfile);
    }

    $eventos = array();
    $lineas = explode("\n", $icalfeed);
    $comenzo_evento = false;
    $evento = new stdClass();
    $evento->uid = "";
    $evento->start = "";
    $evento->end = "";
    foreach($lineas as $linea) {
      $linea = trim($linea);
      if (strpos($linea, "BEGIN:VEVENT") !== FALSE) {
        $evento = new stdClass();
        $evento->uid = "";
        $evento->start = "";
        $evento->end = "";        
        $comenzo_evento = true; continue;
      }
      if ($comenzo_evento) {
        // Fin del evento
        if (strpos($linea, "END:VEVENT") !== FALSE) {
          $eventos[] = $evento;
          $comenzo_evento = false; continue;
        }
        // Fecha de Inicio
        if (strpos($linea, "DTSTART;VALUE=DATE:") !== FALSE) {
          $fecha = str_replace("DTSTART;VALUE=DATE:", "", $linea);
          $evento->start = substr($fecha, 0, 4)."-".substr($fecha, 4, 2)."-".substr($fecha, 6, 2);
        }
        // Fecha de Fin
        if (strpos($linea, "DTEND;VALUE=DATE:") !== FALSE) {
          $fecha = str_replace("DTEND;VALUE=DATE:", "", $linea);
          $evento->end = substr($fecha, 0, 4)."-".substr($fecha, 4, 2)."-".substr($fecha, 6, 2);
        }
        // Fecha de Fin
        if (strpos($linea, "SUMMARY:") !== FALSE) {
          $comentario = str_replace("SUMMARY:", "", $linea);
          $evento->comentario = $urlbase.": ".$comentario;
        }        
        // UID
        if (strpos($linea, "UID:") !== FALSE) {
          $evento->uid = str_replace("UID:", "", $linea);
        }
      }
    }

    foreach($eventos as $evento) {
      if (empty($evento->uid) || empty($evento->start) || empty($evento->end)) continue;
      $sql = "SELECT * FROM inm_propiedades_reservas ";
      $sql.= "WHERE id_propiedad = $id_propiedad AND id_empresa = $id_empresa ";
      $sql.= "AND fecha_desde = '$evento->start' AND fecha_hasta = '$evento->end' ";
      //$sql.= "AND uid = '$evento->uid' ";
      $q = $this->db->query($sql);
      if ($q->num_rows() == 0) {
        // Insertamos la nueva reserva
        $sql = "INSERT INTO inm_propiedades_reservas (id_empresa, id_propiedad, fecha_desde, fecha_hasta, id_estado, comentario, uid) VALUES (";
        $sql.= "'$id_empresa', '$id_propiedad', '$evento->start', '$evento->end', 0, '$evento->comentario', '$evento->uid' )";
        $this->db->query($sql);
        $id_reserva = $this->db->insert_id();

        // Recorremos las fechas
        $d = new DateTime($evento->start);
        $h = new DateTime($evento->end);
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($d,$interval,$h);
        foreach($range as $fecha) {
          $f = $fecha->format("Y-m-d");
          $sql = "INSERT INTO inm_propiedades_reservas_disponibilidad (id_empresa,id_propiedad,fecha,disponible,id_reserva) VALUES(";
          $sql.= "'$id_empresa','$id_propiedad','$f','0','$id_reserva')";
          $this->db->query($sql);          
        }

      } else {
        $reserva = $q->row();
        // Actualizamos la reserva
        $sql = "UPDATE inm_propiedades_reservas ";
        $sql.= "SET comentario = '$evento->comentario' ";
        $sql.= "WHERE id_propiedad = $id_propiedad AND id_empresa = $id_empresa ";
        $sql.= "AND fecha_desde = '$evento->start' AND fecha_hasta = '$evento->end' ";
        $this->db->query($sql);
        $id_reserva = $reserva->id;
        // Borramos la disponibilidad para volverla a crear
        $sql = "DELETE FROM inm_propiedades_reservas_disponibilidad WHERE id_empresa = $id_empresa AND id_propiedad = $id_propiedad AND id_reserva = $id_reserva";
        $this->db->query($sql);
      }

      // Recorremos las fechas y cancelamos la disponibilidad
      $d = new DateTime($evento->start);
      $h = new DateTime($evento->end);
      $interval = new DateInterval('P1D');
      $range = new DatePeriod($d,$interval,$h);
      foreach($range as $fecha) {
        $f = $fecha->format("Y-m-d");
        $sql = "INSERT INTO inm_propiedades_reservas_disponibilidad (id_empresa,id_propiedad,fecha,disponible,id_reserva) VALUES(";
        $sql.= "'$id_empresa','$id_propiedad','$f','0','$id_reserva')";
        $this->db->query($sql);          
      }
    }
  }

  function save_tag($tag) {
    $this->load->helper("file_helper");
    // Primero controlamos si existe la etiqueta
    $q = $this->db->query("SELECT * FROM inm_etiquetas WHERE nombre = '$tag->nombre' AND id_empresa = $tag->id_empresa LIMIT 0,1");
    if ($q->num_rows()<=0) {
      // Si no existe, la guardamos
      $link = filename($tag->nombre,"-",0);
      $this->db->query("INSERT INTO inm_etiquetas (nombre,link,id_empresa) VALUES ('$tag->nombre','$link',$tag->id_empresa)");
      $id_etiqueta = $this->db->insert_id();
    } else {
      $row = $q->row();
      $id_etiqueta = $row->id;
    }
    $this->db->query("INSERT INTO inm_propiedades_etiquetas (id_empresa,id_propiedad,id_etiqueta) VALUES ($tag->id_empresa,$tag->id_propiedad,$id_etiqueta) ");
  }

  function get_propiedad_meli($id,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT * FROM inm_propiedades_meli ";
    $sql.= "WHERE id_propiedad = $id AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $prop_meli = $q->row();
      return $prop_meli;
    } return FALSE;
  }

  function update_meli($art) {
    
    // Si no existe esta opcion, solamente salimos
    if (!isset($art->categoria_meli)) return FALSE;
    if (empty($art->categoria_meli)) return FALSE;

    $this->load->model("Empresa_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($art->id_empresa);
    if (!$usa_meli) return FALSE;

    $prop_meli = $this->get_propiedad_meli($art->id);
    if ($prop_meli === FALSE) {
      // Debemos insertar el elemento en la otra tabla
      $this->db->insert("inm_propiedades_meli",array(
        "id_propiedad"=>$art->id,
        "id_empresa"=>$art->id_empresa,
        "activo_meli"=>0,
        "titulo_meli"=>$art->titulo_meli,
        "texto_meli"=>$art->texto_meli,
        "categoria_meli"=>$art->categoria_meli,
        "precio_meli"=>$art->precio_meli,
        "ciudad_meli"=>$art->ciudad_meli,
        "list_type_id"=>$art->list_type_id,
      ));
    } else {
      // Debemos actualizar los datos de la otra tabla
      $this->db->where("id_propiedad",$art->id);
      $this->db->where("id_empresa",$art->id_empresa);
      $this->db->update("inm_propiedades_meli",array(
        "id_propiedad"=>$art->id,
        "id_empresa"=>$art->id_empresa,
        "activo_meli"=>$art->activo_meli,
        "titulo_meli"=>$art->titulo_meli,
        "texto_meli"=>$art->texto_meli,
        "categoria_meli"=>$art->categoria_meli,
        "precio_meli"=>$art->precio_meli,
        "ciudad_meli"=>$art->ciudad_meli,
        "list_type_id"=>$art->list_type_id,
      ));
    }
    return TRUE;
  }

  function update_publicacion_mercadolibre($id) {

    // Volvemos a obtener el objeto
    $prop_meli = $this->get($id);
    if ($prop_meli === FALSE) return FALSE;

    if ($prop_meli->status == "active") {

      // El propiedad esta activo en MercadoLibre
      // entonces lo que tenemos que hacer es sincronizar los datos con la publicacion

      // Obtenemos la configuracion de la empresa
      $this->load->model("Empresa_Model");
      $web_conf = $this->Empresa_Model->get_web_conf($prop_meli->id_empresa);
      if ($web_conf === FALSE) return FALSE;
      if (empty($web_conf->ml_access_token) || empty($web_conf->ml_refresh_token)) return FALSE;

      require_once '../models/meli.php';
      $meli = new Meli(ML_APP_ID, ML_APP_SECRET, $web_conf->ml_access_token, $web_conf->ml_refresh_token);
      if($web_conf->ml_expires_in < time()) {
        try {
          // Refrescamos el access token
          $refresh = $meli->refreshAccessToken();
          $web_conf->ml_access_token = $refresh['body']->access_token;
          $web_conf->ml_expires_in = time() + $refresh['body']->expires_in;
          $web_conf->ml_refresh_token = $refresh['body']->refresh_token;
          $this->db->where("id_empresa",$web_conf->id_empresa);
          $this->db->update("web_configuracion",$web_conf);
        } catch (Exception $e) {
          echo $e->getMessage();
          return FALSE;
        }
      }

      $params = array('access_token'=>$web_conf->ml_access_token);
      $body = array(
        "price"=>$prop_meli->precio_meli,
      );
      $response = $meli->put("/items/".$prop_meli->id_meli, $body, $params);
      if ($response["httpCode"] == 200) {
        return TRUE;
      } else {
        return FALSE;
      }
    }
  }

  function find($filter) {
    $id_empresa = parent::get_empresa();
    $this->db->where("id_empresa",$id_empresa);
    $this->db->like("nombre",$filter);
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }
    
  // Controlamos si existe el codigo
  function existe_codigo($codigo,$id = 0) {
    $id_empresa = parent::get_empresa();
    if (empty($codigo)) return FALSE;
    $sql = "SELECT * FROM inm_propiedades WHERE codigo = '$codigo' AND id_empresa = '$id_empresa' ";
    if ($id != 0) $sql.= "AND id != $id ";
    $q = $this->db->query($sql);
    return ($q->num_rows()>0);
  }
    
  /**
   * Obtiene los propiedades a partir de diferentes parametros
   */
  function buscar($conf = array()) {
    
    $id_empresa = (isset($conf["id_empresa"])) ? $conf["id_empresa"] : parent::get_empresa();
    $buscar_red = (isset($conf["buscar_red"])) ? $conf["buscar_red"] : 0;
    $buscar_red_empresa = (isset($conf["buscar_red_empresa"])) ? $conf["buscar_red_empresa"] : 0;
    $id_propietario = (isset($conf["id_propietario"])) ? $conf["id_propietario"] : 0;
    $id_tipo_estado = (isset($conf["id_tipo_estado"])) ? $conf["id_tipo_estado"] : 0;
    $id_tipo_operacion = (isset($conf["id_tipo_operacion"])) ? $conf["id_tipo_operacion"] : 0;
    $id_tipo_inmueble = (isset($conf["id_tipo_inmueble"])) ? $conf["id_tipo_inmueble"] : 0;
    $id_localidad = (isset($conf["id_localidad"])) ? $conf["id_localidad"] : 0;
    $calle = (isset($conf["calle"])) ? $conf["calle"] : "";
    $id_usuario = (isset($conf["id_usuario"])) ? $conf["id_usuario"] : 0;
    $apto_banco = (isset($conf["apto_banco"])) ? $conf["apto_banco"] : 0;
    $acepta_permuta = (isset($conf["acepta_permuta"])) ? $conf["acepta_permuta"] : 0;
    $filter = (isset($conf["filter"])) ? $conf["filter"] : "";
    $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"])) ? $conf["offset"] : 0;
    $order = (isset($conf["order"])) ? $conf["order"] : "";
    $activo = (isset($conf["activo"])) ? $conf["activo"] : -1;
    $filtro_meli = (isset($conf["filtro_meli"])) ? $conf["filtro_meli"] : -1;
    $filtro_olx = (isset($conf["filtro_olx"])) ? $conf["filtro_olx"] : -1;
    $filtro_inmovar = (isset($conf["filtro_inmovar"])) ? $conf["filtro_inmovar"] : -1;
    $filtro_inmobusquedas = (isset($conf["filtro_inmobusquedas"])) ? $conf["filtro_inmobusquedas"] : -1;
    $filtro_argenprop = (isset($conf["filtro_argenprop"])) ? $conf["filtro_argenprop"] : -1;
    $monto = (isset($conf["monto"])) ? $conf["monto"] : "";
    $monto_2 = (isset($conf["monto_2"])) ? $conf["monto_2"] : "";
    $monto_moneda = (isset($conf["monto_moneda"])) ? $conf["monto_moneda"] : "$";
    $dormitorios = (isset($conf["dormitorios"])) ? $conf["dormitorios"] : "";
    $banios = (isset($conf["banios"])) ? $conf["banios"] : "";
    $olx_habilitado = (isset($conf["olx_habilitado"])) ? $conf["olx_habilitado"] : -1;
    $buscar_imagenes = (isset($conf["buscar_imagenes"])) ? $conf["buscar_imagenes"] : 0;

    // Cotizacion del dolar
    $cotizacion = 1;
    $q_cot = $this->db->query('SELECT * FROM cotizaciones WHERE moneda = "U$D" ORDER BY fecha DESC LIMIT 0,1 ');
    if ($q_cot->num_rows()>0) {
      $r_cot = $q_cot->row();
      $cotizacion = $r_cot->valor;
    }
    if (!is_numeric($monto)) $monto = 0;
    if (!is_numeric($monto_2)) $monto_2 = 99999999999999;
    
    $sql_count = "COUNT(*) AS cantidad ";
    
    $sql_fields = "SQL_CALC_FOUND_ROWS A.*, ";
    $sql_fields.= "IF(A.valido_hasta='0000-00-00','',DATE_FORMAT(A.valido_hasta,'%d/%m/%Y')) AS valido_hasta, ";
    $sql_fields.= "IF(A.fecha_publicacion='0000-00-00','',DATE_FORMAT(A.fecha_publicacion,'%d/%m/%Y')) AS fecha_publicacion, ";
    $sql_fields.= "IF(P.nombre IS NULL,'',P.nombre) AS propietario, ";
    $sql_fields.= "IF(TE.nombre IS NULL,'',TE.nombre) AS tipo_estado, ";
    $sql_fields.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo_inmueble, ";
    $sql_fields.= "IF(X.nombre IS NULL,'',X.nombre) AS tipo_operacion, ";
    $sql_fields.= "IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql_fields.= "IF(U.email IS NULL,'',U.email) AS usuario_email, ";
    $sql_fields.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql_fields.= "IF(L.id_localidad_inmobusquedas IS NULL,0,L.id_localidad_inmobusquedas) AS id_localidad_inmobusquedas, ";
    $sql_fields.= "IF(L.id_partido_inmobusquedas IS NULL,0,L.id_partido_inmobusquedas) AS id_partido_inmobusquedas, ";
    $sql_fields.= "IF(PROV.id_inmobusquedas IS NULL,0,PROV.id_inmobusquedas) AS id_provincia_inmobusquedas ";

    $sql_from = "FROM inm_propiedades A ";
    $sql_from.= "LEFT JOIN inm_tipos_estado TE ON (A.id_tipo_estado = TE.id) ";
    $sql_from.= "LEFT JOIN inm_tipos_inmueble TI ON (A.id_tipo_inmueble = TI.id) ";
    $sql_from.= "LEFT JOIN inm_tipos_operacion X ON (A.id_tipo_operacion = X.id) ";
    $sql_from.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql_from.= "LEFT JOIN clientes P ON (A.id_propietario = P.id AND A.id_empresa = P.id_empresa) ";
    $sql_from.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql_from.= "LEFT JOIN com_departamentos DEP ON (L.id_departamento = DEP.id) ";
    $sql_from.= "LEFT JOIN com_provincias PROV ON (DEP.id_provincia = PROV.id) ";
    if ($filtro_meli >= 0) $sql_from.= "LEFT JOIN inm_propiedades_meli PROP_MELI ON (A.id = PROP_MELI.id_propiedad AND A.id_empresa = PROP_MELI.id_empresa) ";

    $sql_where = "WHERE 1=1 ";
    if ($activo != -1) $sql_where.= "AND A.activo = $activo ";
    if ($olx_habilitado != -1) $sql_where.= "AND A.olx_habilitado = $olx_habilitado ";
    if (!empty($filter)) $sql_where.= "AND (A.codigo LIKE '%$filter%' OR A.nombre LIKE '%$filter%' OR A.calle LIKE '%$filter%') ";
    if (!empty($id_tipo_estado)) $sql_where.= "AND A.id_tipo_estado = $id_tipo_estado ";
    if (!empty($id_tipo_operacion)) $sql_where.= "AND A.id_tipo_operacion IN ($id_tipo_operacion) ";
    if (!empty($id_tipo_inmueble)) $sql_where.= "AND A.id_tipo_inmueble IN ($id_tipo_inmueble) ";
    if (!empty($id_usuario)) $sql_where.= "AND A.id_usuario = $id_usuario ";
    if (!empty($id_propietario)) $sql_where.= "AND A.id_propietario = $id_propietario ";
    if (!empty($id_localidad)) $sql_where.= "AND A.id_localidad IN ($id_localidad) ";
    if (!empty($calle)) $sql_where.= "AND A.calle = '$calle' ";
    if ($apto_banco == 1) $sql_where.= "AND A.apto_banco = 1 ";
    if ($acepta_permuta == 1) $sql_where.= "AND A.acepta_permuta = 1 ";
    if (!empty($dormitorios)) {
      if ($dormitorios == "7") $sql_where.= "AND A.dormitorios > 6 ";
      else $sql_where.= "AND A.dormitorios = $dormitorios ";
    }
    if (!empty($banios)) {
      if ($banios == "7") $sql_where.= "AND A.banios > 6 ";
      else $sql_where.= "AND A.banios = $banios ";
    }

    if ($monto_moneda == 'U$D') {
      $sql_where.= "AND (";
      $sql_where.= "(A.precio_final >= $monto AND A.precio_final <= $monto_2 AND A.moneda = '".'U$S'."') ";
      if ($cotizacion != 0) $sql_where.= "OR (A.moneda = '$' AND ((A.precio_final / $cotizacion) >= $monto) AND ((A.precio_final / $cotizacion) <= $monto_2) ) ";
      $sql_where.= ") ";
    } else if ($monto_moneda == "$") {
      $sql_where.= "AND (";
      $sql_where.= "(A.precio_final >= $monto AND A.precio_final <= $monto_2 AND A.moneda = '$') ";
      $sql_where.= "OR (A.moneda = '".'U$S'."' AND (A.precio_final * $cotizacion) >= $monto AND (A.precio_final * $cotizacion) <= $monto_2 ) ";
      $sql_where.= ") ";      
    }
    if ($buscar_red == 1) {

      // Si estamos buscando en la RED
      $sql_where_2 = "AND A.compartida = 1 ";
      $sql_where_2.= "AND A.id_empresa IN (";
      $sql_where_2.= " SELECT PR.id_empresa_compartida FROM inm_permisos_red PR ";
      $sql_where_2.= " INNER JOIN web_configuracion WC1 ON (PR.id_empresa_compartida = WC1.id_empresa) ";
      $sql_where_2.= " WHERE PR.id_empresa = $id_empresa ";
      $sql_where_2.= " AND PR.estado >= 1 "; // El estado 1 es SOLO PANEL, el 2 es tambien WEB
      $sql_where_2.= " AND WC1.red_inmovar = 1 ";
      if (!empty($buscar_red_empresa)) $sql_where_2.= " AND PR.id_empresa_compartida = $buscar_red_empresa ";
      $sql_where_2.= ") ";

      $sql = "SELECT ".$sql_fields.$sql_from.$sql_where.$sql_where_2;
      if (!empty($order)) $sql.= "ORDER BY $order ";
      if ($offset != 0) $sql.= "LIMIT $limit, $offset ";

    } else {

      // Si estamos buscando en MIS PROPIEDADES
      $sql_where_2 = "";

      // Estos filtros solo se aplican para propiedades propias, no para las de la red
      if ($filtro_meli == 1) $sql_where_2.= "AND PROP_MELI.status = 'active' ";
      else if ($filtro_meli == 2) $sql_where_2.= "AND PROP_MELI.status = 'paused' ";
      else if ($filtro_meli == 3) $sql_where_2.= "AND PROP_MELI.status = 'closed' ";
      else if ($filtro_meli == 0) $sql_where_2.= "AND (PROP_MELI.id_propiedad IS NULL OR PROP_MELI.status = '') ";
      
      if ($filtro_olx == 1) $sql_where_2.= "AND A.olx_habilitado = 1 AND A.olx_id != '' ";
      else if ($filtro_olx == 2) $sql_where_2.= "AND A.olx_habilitado = 1 AND A.olx_id = '' ";
      else if ($filtro_olx == 0) $sql_where_2.= "AND A.olx_habilitado = 0 ";

      if ($filtro_inmovar == 1) $sql_where_2.= "AND A.compartida = 1 ";
      else if ($filtro_inmovar == 0) $sql_where_2.= "AND A.compartida = 0 ";

      if ($filtro_inmobusquedas == 1) $sql_where_2.= "AND A.inmobusquedas_habilitado = 1 AND A.inmobusquedas_url != '' ";
      else if ($filtro_inmobusquedas == 2) $sql_where_2.= "AND A.inmobusquedas_habilitado = 1 AND A.inmobusquedas_url = '' ";
      else if ($filtro_inmobusquedas == 3) $sql_where_2.= "AND A.inmobusquedas_habilitado = 1 ";
      else if ($filtro_inmobusquedas == 0) $sql_where_2.= "AND A.inmobusquedas_habilitado = 0 ";

      if ($filtro_argenprop == 1) $sql_where_2.= "AND A.argenprop_habilitado >= 1 ";
      else if ($filtro_argenprop == 0) $sql_where_2.= "AND A.argenprop_habilitado = 0 ";

      if ($id_empresa != -1) $sql_where_2.= "AND A.id_empresa = $id_empresa ";

      $sql = "SELECT ".$sql_fields.$sql_from.$sql_where.$sql_where_2;
      if (!empty($order)) $sql.= "ORDER BY $order ";
      if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
    }

    $sql_final = $sql;
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($q->result() as $r) {

      if ($buscar_imagenes == 1) {
        $sql = "SELECT AI.* FROM inm_propiedades_images AI ";
        $sql.= "WHERE AI.id_propiedad = $r->id AND AI.id_empresa = $r->id_empresa ORDER BY AI.orden ASC";
        $qq = $this->db->query($sql);
        $r->images = array();
        $r->planos = array();
        foreach($qq->result() as $rr) {
          if ($rr->plano == 1) $r->planos[] = $rr->path;
          else $r->images[] = $rr->path;
        }
      }

      $ingresar_row = 1;

      // Si esta compartido en MercadoLibre
      $r->activo_meli = 0;
      $r->categoria_meli = "";
      $r->permalink = "";
      $r->status = "";
      $r->id_meli = "";
      $qqq = $this->db->query("SELECT * FROM inm_propiedades_meli WHERE id_empresa = $id_empresa AND id_propiedad = $r->id ");
      if ($qqq->num_rows()>0) {
        $rrr = $qqq->row();
        $r->id_meli = $rrr->id_meli;
        $r->activo_meli = $rrr->activo_meli;
        $r->categoria_meli = $rrr->categoria_meli;
        $r->permalink = $rrr->permalink;
        $r->status = $rrr->status;
      }
      /*
      if ($filtro_meli == 1 && $r->status != "active") $ingresar_row = 0;
      else if ($filtro_meli == 2 && $r->status != "paused") $ingresar_row = 0;
      else if ($filtro_meli == 3 && $r->status != "closed") $ingresar_row = 0;
      else if ($filtro_meli == 0 && !empty($r->id_meli)) $ingresar_row = 0; 
      */
      $salida[] = $r;
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
      "sql"=>$sql_final,
    );
  }

  function insert($data) {
    // Si no tiene codigo, le creamos uno
    if (!isset($data->codigo) || empty($data->codigo)) {
      $data->codigo = $this->next(array(
        "id_empresa"=>$data->id_empresa,
      ));
    }
    return parent::insert($data);
  }
  
  function next($config = array()) {
    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $q = $this->db->query("SELECT IF(MAX(codigo) IS NULL,0,MAX(codigo)) AS codigo FROM inm_propiedades WHERE id_empresa = $id_empresa");
    $r = $q->row();
    return ((int)$r->codigo + 1);
  }
  
  
  function get($id,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    // Obtenemos los datos del propiedad
    $id = (int)$id;
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.valido_hasta='0000-00-00','',DATE_FORMAT(A.valido_hasta,'%d/%m/%Y')) AS valido_hasta, ";
    $sql.= "IF(A.fecha_publicacion='0000-00-00','',DATE_FORMAT(A.fecha_publicacion,'%d/%m/%Y')) AS fecha_publicacion, ";
    $sql.= "E.nombre AS empresa, E.path AS empresa_path, E.telefono_empresa AS empresa_telefono, E.direccion_empresa AS empresa_direccion, E.email AS empresa_email, ";
    $sql.= "IF(P.nombre IS NULL,'',P.nombre) AS propietario, ";
    $sql.= "IF(P.telefono IS NULL,'',P.telefono) AS propietario_telefono, ";
    $sql.= "IF(P.email IS NULL,'',P.email) AS propietario_email, ";
    $sql.= "IF(P.direccion IS NULL,'',P.direccion) AS propietario_direccion, ";
    $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS tipo_estado, ";
    $sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo_inmueble, ";
    $sql.= "IF(X.nombre IS NULL,'',X.nombre) AS tipo_operacion, ";
    $sql.= "IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= "IF(U.email IS NULL,'',U.email) AS usuario_email, ";
    $sql.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= "FROM inm_propiedades A ";
    $sql.= "INNER JOIN empresas E ON (A.id_empresa = E.id) ";
    $sql.= "LEFT JOIN inm_tipos_estado TE ON (A.id_tipo_estado = TE.id) ";
    $sql.= "LEFT JOIN inm_tipos_inmueble TI ON (A.id_tipo_inmueble = TI.id) ";
    $sql.= "LEFT JOIN inm_tipos_operacion X ON (A.id_tipo_operacion = X.id) ";
    $sql.= "LEFT JOIN clientes P ON (A.id_propietario = P.id AND A.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $propiedad = $q->row();
    
    // Obtenemos los propiedades relacionados con ese producto
    $sql = "SELECT A.id, A.nombre, A.path, AR.destacado ";
    $sql.= "FROM inm_propiedades A INNER JOIN inm_propiedades_relacionados AR ON (A.id = AR.id_relacion) ";
    $sql.= "WHERE AR.id_propiedad = $id ";
    $sql.= "ORDER BY AR.orden ASC ";
    $q = $this->db->query($sql);
    $propiedad->relacionados = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id = $r->id;
      $obj->nombre = $r->nombre;
      $obj->path = $r->path;
      $obj->destacado = $r->destacado;
      $propiedad->relacionados[] = $obj;
    }

    // Obtenemos las etiquetas
    $sql = "SELECT E.nombre ";
    $sql.= " FROM inm_propiedades_etiquetas EE INNER JOIN inm_etiquetas E ON (EE.id_etiqueta = E.id AND EE.id_empresa = E.id_empresa) ";
    $sql.= "WHERE EE.id_propiedad = $id AND EE.id_empresa = $id_empresa ORDER BY EE.orden ASC";
    $q = $this->db->query($sql);
    $propiedad->etiquetas = array();
    foreach($q->result() as $r) {
      $propiedad->etiquetas[] = (html_entity_decode($r->nombre));
    }

    // Obtenemos los departamentos
    $sql = "SELECT * ";
    $sql.= "FROM inm_departamentos ";
    $sql.= "WHERE id_propiedad = $id AND id_empresa = $propiedad->id_empresa ";
    $sql.= "ORDER BY orden ASC, piso ASC ";
    $q = $this->db->query($sql);
    $propiedad->departamentos = array();
    foreach($q->result() as $r) {
      // Obtenemos las imagenes
      $sql = "SELECT path FROM inm_departamentos_images ";
      $sql.= "WHERE id_empresa = $propiedad->id_empresa ";
      $sql.= "AND id_propiedad = $propiedad->id ";
      $sql.= "AND id_departamento = $r->id ";
      $qq = $this->db->query($sql);
      $r->images_dptos = array();
      foreach($qq->result() as $rr) $r->images_dptos[] = $rr->path;
      $propiedad->departamentos[] = $r;
    }

    // Obtenemos las imagenes de ese propiedad
    $sql = "SELECT AI.* FROM inm_propiedades_images_meli AI ";
    $sql.= "WHERE AI.id_propiedad = $id AND AI.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $propiedad->images_meli = array();
    foreach($q->result() as $r) {
      $propiedad->images_meli[] = $r->path;
    }

    // TODO: CAMBIAR EL ID POR ID_INMOBUSQUEDA, Y HACER UN ID AUTOINCREMENTABLE
    $propiedad->id_barrio_argenprop = 0;
    $propiedad->id_barrio_inmobusqueda = 0;
    if (!empty($propiedad->id_barrio)) {
      $sql = "SELECT * FROM com_barrios WHERE id = $propiedad->id_barrio ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $barrio = $q->row();
        $propiedad->id_barrio_argenprop = $barrio->id_argenprop;
        $propiedad->id_barrio_inmobusqueda = $barrio->id;
      }
    }

    // Obtenemos los precios
    $this->load->helper("fecha_helper");
    $propiedad->temporada = array();
    $propiedad->promociones = array();
    $sql = "SELECT AI.* FROM inm_propiedades_precios AI WHERE AI.id_propiedad = $id AND AI.id_empresa = $id_empresa ORDER BY AI.id ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $r->fecha_desde = fecha_es($r->fecha_desde);
      $r->fecha_hasta = fecha_es($r->fecha_hasta);
      if ($r->promocion == 0) {
        $propiedad->temporada[] = $r;
      } else if ($r->promocion == 1) {
        $propiedad->promociones[] = $r;
      }
    }

    $sql = "SELECT AI.* FROM inm_propiedades_impuestos AI WHERE AI.id_propiedad = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $propiedad->impuestos = ($q->num_rows()>0) ? $q->result() : array();

    /*
    // Obtenemos las categorias relacionados con ese producto
    $sql = "SELECT R.id, R.nombre ";
    $sql.= "FROM rubros R INNER JOIN inm_propiedades_relacionados AR ON (R.id = AR.id_rubro) ";
    $sql.= "WHERE AR.id_propiedad = $id ";
    $sql.= "ORDER BY AR.orden ASC ";
    $q = $this->db->query($sql);
    $propiedad->rubros_relacionados = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id = $r->id;
      $obj->nombre = $r->nombre;
      $propiedad->rubros_relacionados[] = $obj;
    }
    */
    
    // Obtenemos las imagenes de ese propiedad
    $sql = "SELECT AI.* FROM inm_propiedades_images AI ";
    $sql.= "WHERE AI.id_propiedad = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $propiedad->images = array();
    $propiedad->planos = array();
    foreach($q->result() as $r) {
      if ($r->plano == 1) $propiedad->planos[] = $r->path;
      else $propiedad->images[] = $r->path;
    }

    // Buscamos si el propiedad esta compartido en MERCADOLIBRE
    $propiedad_meli = $this->get_propiedad_meli($id,array(
      "id_empresa"=>$id_empresa,
    ));
    if ($propiedad_meli !== FALSE) {
      $propiedad->id_meli = $propiedad_meli->id_meli;
      $propiedad->permalink = $propiedad_meli->permalink;
      $propiedad->fecha_publicacion = $propiedad_meli->fecha_publicacion;
      $propiedad->titulo_meli = $propiedad_meli->titulo_meli;
      $propiedad->texto_meli = $propiedad_meli->texto_meli;
      $propiedad->precio_meli = $propiedad_meli->precio_meli;
      $propiedad->categoria_meli = $propiedad_meli->categoria_meli;
      $propiedad->activo_meli = $propiedad_meli->activo_meli;
      $propiedad->list_type_id = $propiedad_meli->list_type_id;
      $propiedad->ciudad_meli = $propiedad_meli->ciudad_meli;
      $propiedad->status = $propiedad_meli->status;

    } else {

      $sql = "SELECT ml_texto_empresa FROM web_configuracion WHERE id_empresa = $id_empresa LIMIT 0,1";
      $qw = $this->db->query($sql);
      $web_conf = $qw->row();

      $propiedad->id_meli = "";
      $propiedad->permalink = "";
      //$propiedad->fecha_publicacion = "";
      $propiedad->titulo_meli = $propiedad->nombre;
      $propiedad->texto_meli = strip_tags($propiedad->texto)."\n\n".$web_conf->ml_texto_empresa;
      $propiedad->precio_meli = $propiedad->precio_final;
      $propiedad->categoria_meli = "";
      $propiedad->activo_meli = -1; // Todavia no fue compartido
      $propiedad->list_type_id = 0;
      $propiedad->ciudad_meli = "";
      $propiedad->status = "";
    }

    return $propiedad;
  }
  
  
  
  function get_by_hash($hash) {
    
    // Obtenemos los datos del propiedad
    $sql = "SELECT A.*, ";
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
    $sql.= "WHERE A.hash = '$hash' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $propiedad = $q->row();
    
    // Obtenemos los propiedades relacionados con ese producto
    $sql = "SELECT A.id, A.nombre, A.path, AR.destacado ";
    $sql.= "FROM inm_propiedades A INNER JOIN inm_propiedades_relacionados AR ON (A.id = AR.id_relacion) ";
    $sql.= "WHERE AR.id_propiedad = $propiedad->id ";
    $sql.= "ORDER BY AR.orden ASC ";
    $q = $this->db->query($sql);
    $propiedad->relacionados = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id = $r->id;
      $obj->nombre = $r->nombre;
      $obj->path = $r->path;
      $obj->destacado = $r->destacado;
      $propiedad->relacionados[] = $obj;
    }

    // Obtenemos los departamentos
    $sql = "SELECT * ";
    $sql.= "FROM inm_departamentos ";
    $sql.= "WHERE id_propiedad = $propiedad->id AND id_empresa = $propiedad->id_empresa ";
    $sql.= "ORDER BY orden ASC ";
    $q = $this->db->query($sql);
    $propiedad->departamentos = array();
    foreach($q->result() as $r) {
      $propiedad->departamentos[] = $r;
    }
    
    // Obtenemos las imagenes de ese propiedad
    $sql = "SELECT AI.* FROM inm_propiedades_images AI WHERE AI.id_propiedad = $propiedad->id AND AI.id_empresa = $propiedad->id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $propiedad->images = array();
    $propiedad->planos = array();
    foreach($q->result() as $r) {
      if ($r->plano == 1) $propiedad->planos[] = $r->path;
      else $propiedad->images[] = $r->path;
    }
    return $propiedad;
  }
  
  function get_by_id($id,$config = array()) {
    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    // Obtenemos los datos del propiedad
    $id = (int)$id;
    $sql = "SELECT A.*, ";
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
    $sql.= "WHERE A.id = '$id' AND A.id_empresa = '$id_empresa' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $propiedad = $q->row();      
    } else {
      $propiedad = FALSE;
    }
    return $propiedad;
  }
  
  function get_by_codigo($codigo) {
    $id_empresa = parent::get_empresa();
    // Obtenemos los datos del propiedad
    $codigo = (int)$codigo;
    $sql = "SELECT A.*, ";
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
    $sql.= "WHERE A.codigo = '$codigo' AND A.id_empresa = '$id_empresa' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $propiedad = $q->row();      
    } else {
      $propiedad = FALSE;
    }
    return $propiedad;
  }
  
  function delete($id) {
    // Controlamos que se este borrando un propiedad que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $q = $this->db->query("SELECT * FROM inm_propiedades WHERE id = $id AND id_empresa = $id_empresa ");
    if ($q->num_rows()>0) {
      $this->db->query("DELETE FROM inm_propiedades_impuestos WHERE id_propiedad = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM inm_propiedades_precios WHERE id_propiedad = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM inm_departamentos WHERE id_propiedad = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM inm_departamentos_images WHERE id_propiedad = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM inm_propiedades_relacionados WHERE id_propiedad = $id ");
      $this->db->query("DELETE FROM inm_propiedades_images WHERE id_propiedad = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM inm_propiedades_images_meli WHERE id_propiedad = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM inm_propiedades_meli WHERE id_propiedad = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM inm_propiedades WHERE id = $id AND id_empresa = $id_empresa");
    }
  }

}