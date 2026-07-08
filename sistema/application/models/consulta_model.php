<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Consulta_Model extends Abstract_Model {

  public $sql = "";

  /*
  ID_ORIGEN:
    1 = CONSULTA DE PROPIEDADES
    2 = REGISTRO DE NEWSLETTER
    3 = CONSULTA PERSONAL
    4 = CONSULTA TELEFONICA
    5 = EMAIL
    6 = WEB
    7 = TASACIONES WEB
    8 = SOLICITUD DE VISITAS WEB
    9 = WEB
    10 = FORMULARIO DE INTERESES
    11 = ARTICULOS WEB
    12 = COMENTARIOS
    13 = RESERVA DE VIAJE
    14 = NOTA
    15 = SMS
    16 = TURNO MEDICO
    17 = TAREA
    18 = COMPRA WEB
    19 = COMPRA DESDE MERCADOLIBRE
    20 = CREACION DE USUARIO
    21 = ENVIO DE EMAIL AUTOMATICO
    22 = APERTURA DE LINK DE EMAIL AUTOMATICO
    23 = TURNO EN GENERAL
    25 = CONSULTA DESDE MERCADOLIBRE
    26 = FACEBOOK
    27 = WHATSAPP
    28 = EMAIL INTERESADO EN PROPIEDAD
    30 = CLIENAPP WHATSAPP
    31 = CLIENAPP CONSULTA FUERA DE LINEA
    32 = NOTIFICACION DEL SISTEMA: CAMBIO DE ESTADO O DE USUARIO
  */

  // =====================================
  // Estas funciones sirven para filtrar por grupos de origenes

  // Incluye clienapp y los registros manuales de whatsapp
  function get_origenes_consultas_whatsapp() {
    return "30,31,27";
  }

  // Incluye cualquier formulario web
  function get_origenes_consultas_web() {
    return "1,2,6,7,8,9,10,11,12";
  }

  // Turno
  function get_origenes_turnos() {
    return "23";
  }

  // Incluye todo lo que se registra manualmente
  function get_origenes_consultas_manuales() {
    return "3,4,5,14,15,26";
  }

  // =====================================

  
  function __construct() {
    parent::__construct("crm_consultas","id","fecha DESC");
  }

  function get_primera_consulta_mensual($conf = array()) {
    // id_origen 30 = wpp
    // id_origen 9  = email
    // asunto       = Contacto
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $mes = isset($conf["mes"]) ? $conf["mes"] : date("m");
    $anio = isset($conf["anio"]) ? $conf["anio"] : date("Y");
    $asunto = isset($conf["asunto"]) ? $conf["asunto"] : "";
    $id_origen = isset($conf["id_origen"]) ? $conf["id_origen"] : 0;
    $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
    $id_contacto = isset($conf["id_contacto"]) ? $conf["id_contacto"] : 0;

    $fecha_desde = $anio."-".$mes."-01 00:00:00";
    $fecha_hasta = date("Y-m-d H:i:s", strtotime($fecha_desde." +1 months"));

    //Buscamos la ultima consulta que hizo el cliente

    $sql = "SELECT CC.* ";
    $sql.= "FROM crm_consultas CC ";
    $sql.= "WHERE CC.id_empresa = '$id_empresa' ";
    if (!empty($asunto)) $sql.= "AND CC.asunto = '$asunto' ";
    if (!empty($id_origen)) $sql.= "AND CC.id_origen = '$id_origen' ";
    if (!empty($id_usuario)) $sql.= "AND CC.id_usuario = '$id_usuario' ";
    if (!empty($id_contacto)) $sql.= "AND CC.id_contacto = '$id_contacto' ";
    $sql.= "AND '$fecha_desde' <= CC.fecha AND CC.fecha < '$fecha_hasta' ";
    $sql.= "ORDER BY CC.fecha DESC ";
    $sql.= "LIMIT 0,1 ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;

    $row = $q->row();

    //Ahora, si la ultima consulta tiene un custom_1 de mostrar_siempre, significa que por mas 
    //que las fechas y los perfiles esten blureados, se muestra igual

    if ($row->custom_1 == "mostrar_siempre") return FALSE;

    $fecha_ultima_consulta = strtotime($row->fecha);
    $fecha_hoy = strtotime(date("Y-m-d H:i:s"));

    $row->dias_ultima_consulta = round(($fecha_hoy - $fecha_ultima_consulta) / (60 * 60 * 24));

    return $row;
  }

  function total_consultas($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $sql = "SELECT COUNT(*) AS total ";
    $sql.= "FROM crm_consultas WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_origen NOT IN (20,21,22,28) ";
    $sql.= "AND tipo = 0 ";
    $q = $this->db->query($sql);
    $r = $q->row();
    return (is_null($r->total) ? 0 : $r->total);
  }

  function registro_creacion_usuario($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $id_contacto = isset($conf["id_contacto"]) ? $conf["id_contacto"] : 0;
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : date("Y-m-d H:i:s");
    $id_origen = 20; // CREACION DE USUARIO
    $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_referencia,id_contacto,id_empresa_relacion) VALUES(";
    $sql.= "'$id_empresa','$fecha','Nuevo usuario','$id_origen','0','$id_contacto','$id_empresa')";
    $this->db->query($sql);
  }

  function registro_email_interesado_propiedad($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $id_contacto = isset($conf["id_contacto"]) ? $conf["id_contacto"] : 0;
    $id_propiedad = isset($conf["id_propiedad"]) ? $conf["id_propiedad"] : 0;
    $propiedad = isset($conf["propiedad"]) ? $conf["propiedad"] : "";
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : date("Y-m-d H:i:s");
    $id_origen = 28; // EMAIL INTERESADO EN PROPIEDAD
    $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_referencia,id_contacto,id_empresa_relacion,tipo) VALUES(";
    $sql.= "'$id_empresa','$fecha','Interesado en propiedad $propiedad','$id_origen','$id_propiedad','$id_contacto','$id_empresa',1)";
    $this->db->query($sql);
    $id = $this->db->insert_id();
    return $id;
  }
    
  function find($filter) {
    $id_empresa = parent::get_empresa();
    $this->db->where("id_empresa",$id_empresa);
    $this->db->like("texto",$filter);
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }

  function insert($data) {
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    if (isset($data->fecha)) {
      if (isset($data->hora) && empty($data->hora) && strlen($data->fecha)>10) {
        $data->hora = substr($data->fecha, strpos($data->fecha, " ")+1);
      }
      $data->fecha = fecha_mysql($data->fecha);
      $data->fecha = substr($data->fecha, 0, 10)." ".$data->hora;      
    }
    // Links adjuntos son por ejemplo facturas
    $links_adjuntos = isset($data->links_adjuntos) ? $data->links_adjuntos : array();
    // Adjuntos son archivos realmente subidos al servidor
    $adjuntos = isset($data->adjuntos) ? $data->adjuntos : array();

    $data->nombre = isset($data->nombre) ? $data->nombre : "";
    $data->email = isset($data->email) ? $data->email : "";

    // Primero tenemos que crear el contacto
    if (isset($data->id_contacto) && $data->id_contacto == 0 && (!empty($data->nombre) || !empty($data->email)) ) {

      // Si se paso un email, buscamos el contacto para saber si existe
      $this->load->model("Cliente_Model");
      $contacto = (!empty($data->email)) ? $this->Cliente_Model->get_by_email($data->email,$id_empresa) : FALSE;
      
      if ($contacto === FALSE) {
        // Debemos crearlo
        $contacto = new stdClass();
        $contacto->id_empresa = $id_empresa;
        $contacto->email = $data->email;
        $contacto->nombre = $data->nombre;
        $contacto->telefono = (isset($data->telefono) ? $data->telefono: "");
        $contacto->fecha_inicial = date("Y-m-d");
        $contacto->fecha_ult_operacion = date("Y-m-d H:i:s");
        $contacto->tipo = 1; // 1 = Contacto
        $contacto->activo = 1; // El cliente esta activo por defecto
        $id = $this->Cliente_Model->insert($contacto);
        $contacto->id = $id;

        // REGISTRAMOS COMO UN EVENTO LA CREACION DEL NUEVO USUARIO
        $this->load->model("Consulta_Model");
        $this->Consulta_Model->registro_creacion_usuario(array(
          "id_contacto"=>$id,
          "id_empresa"=>$id_empresa,
        ));
      }

      $data->id_contacto = $contacto->id;
    }

    //$id = parent::insert($data);
    $sql = "INSERT INTO crm_consultas (";
    $sql.= "id_contacto,id_empresa_relacion,id_empresa,id_entrada,id_paciente,fecha,asunto,";
    $sql.= "texto,id_email_respuesta,id_origen,id_usuario,subtitulo,tipo,id_referencia,id_relacion,id_asunto,estado";
    $sql.= ") VALUES (";
    $sql.= (isset($data->id_contacto)) ? "'$data->id_contacto'," : "0,";
    $sql.= (isset($data->id_empresa_relacion)) ? "'$data->id_empresa_relacion'," : ((isset($data->id_empresa)) ? "'$data->id_empresa'," : "0,");
    $sql.= (isset($data->id_empresa)) ? "'$data->id_empresa'," : "0,";
    $sql.= (isset($data->id_entrada)) ? "'$data->id_entrada'," : "0,";
    $sql.= (isset($data->id_paciente)) ? "'$data->id_paciente'," : "0,";
    $sql.= (isset($data->fecha)) ? "'$data->fecha'," : "NOW(),";
    $sql.= (isset($data->asunto)) ? "'$data->asunto'," : "'',";
    $sql.= (isset($data->texto)) ? "'$data->texto'," : "'',";
    $sql.= (isset($data->id_email_respuesta)) ? "'$data->id_email_respuesta'," : "0,";
    $sql.= (isset($data->id_origen)) ? "'$data->id_origen'," : "0,";
    $sql.= (isset($data->id_usuario)) ? "'$data->id_usuario'," : "0,";
    $sql.= (isset($data->subtitulo)) ? "'$data->subtitulo'," : "'',";
    $sql.= (isset($data->tipo)) ? "'$data->tipo'," : "0,";
    $sql.= (isset($data->id_referencia)) ? "'$data->id_referencia'," : "0,";
    $sql.= (isset($data->id_relacion)) ? "'$data->id_relacion'," : "0,";
    $sql.= (isset($data->id_asunto)) ? "'$data->id_asunto'," : "0,";
    $sql.= (isset($data->estado)) ? "'$data->estado'" : "0";
    $sql.= ")";
    file_put_contents("consulta_insertar.txt", date("Y-m-d H:i:s")." - ".$sql."\n", FILE_APPEND);
    $this->db->query($sql);
    $id = $this->db->insert_id();

    // ESTAMOS MANDANDO
    if (isset($data->tipo) && $data->tipo == 1) {

      // Estamos enviando un EMAIL
      if ($data->id_origen == 5) {

        $this->load->model("Empresa_Model");
        $empresa = $this->Empresa_Model->get_min($id_empresa);

        $this->load->model("Usuario_Model");
        $usuario = $this->Usuario_Model->get($data->id_usuario);
        if ($usuario !== FALSE) {
          $email = "";
          if ($data->id_paciente != 0) {
            $this->load->model("Paciente_Model");  
            $paciente = $this->Paciente_Model->get($data->id_paciente);
            $email = $paciente->email;
          } else if ($data->id_contacto != 0) {
            $this->load->model("Cliente_Model");  
            $cliente = $this->Cliente_Model->get($data->id_contacto);
            $email = $cliente->email;
          }
          if (!empty($email)) {

            $base = $this->config->item("base_url");

            // Links de fichas adjuntas
            if (!empty($links_adjuntos)) {
              $links = array();
              foreach($links_adjuntos as $l) {
                
                // SI ES UN COMPROBANTE
                if ($l->tipo == 2) {
                  $q = $this->db->query("SELECT * FROM facturas WHERE id = $l->id_objeto AND id_empresa = $id_empresa ");
                  if ($q->num_rows()>0) {
                    $r = $q->row();
                    $links[] = "<a href='".$base."facturas/function/ver/$r->hash'>$r->comprobante</a>";
                  }
                }
                // SI ES UNA PROPIEDAD
                else if ($l->tipo == 3) {
                  $q = $this->db->query("SELECT * FROM inm_propiedades WHERE id = $l->id_objeto AND id_empresa = $id_empresa ");
                  if ($q->num_rows()>0) {
                    $r = $q->row();
                    $links[] = "<a href='".$base."propiedades/function/ficha/$r->hash'>$r->nombre</a>";
                  }

                // Si tenemos que mandar directamente el LINK sin procesarlo
                } else if ($l->tipo == -1) {
                  $links[] = $l->id_objeto;
                }
              }
              // NO SE ADJUNTA, SE MANDAN LINKS
              if (sizeof($links)>0) {
                $data->texto.= implode("<br/>",$links);
              }
            }

            if (sizeof($adjuntos)>0) {
              $adjuntos2 = array();
              foreach($adjuntos as $adj) {
                $adj = str_replace($base, "", $adj);
                $adjuntos2[] = $adj;

                // Insertamos el adjunto
                $adj2 = "/sistema/".$adj;
                $sql = "INSERT INTO crm_emails_adjuntos (id_empresa,id_email,tipo,path) VALUES(";
                $sql.= "$data->id_empresa, $id, 0, '$adj2')";
                $this->db->query($sql);
              }
              $adjuntos = $adjuntos2;
            }

            // Obtenemos el contacto
            $this->load->model("Cliente_Model");
            $contacto = $this->Cliente_Model->get($data->id_contacto,$data->id_empresa);
            if ($contacto->tipo == 1) {
              // Si el tipo de contacto esta "A CONTACTAR"
              // y le estamos respondiendo un email, entonces tenemos que pasarlo a "EN PROGRESO"
              $sql = "UPDATE clientes SET tipo = 2 WHERE id = $contacto->id AND id_empresa = $contacto->id_empresa ";
              $this->db->query($sql);
            }

            require APPPATH.'libraries/Mandrill/Mandrill.php';
            mandrill_send(array(
              "to"=>$email,
              "subject"=>$data->asunto,
              "body"=>$data->texto,
              "from_name"=>(($usuario->nombre != $empresa->nombre) ? ($usuario->nombre." - ".$empresa->nombre) : $usuario->nombre),
              "reply_to"=>$usuario->email,
              "bcc"=>"basile.matias99@gmail.com",
              "attachments"=>$adjuntos,
              "metadata"=>array(
                "id_contacto"=>$data->id_contacto,
                "id_consulta"=>$id,
                "id_empresa"=>$data->id_empresa,
              ),
            ));
          }
        }

      // Estamos mandando un SMS
      } else if ($data->id_origen == 15) {

        $celular = "";
        if ($data->id_paciente != 0) {
          $this->load->model("Paciente_Model");  
          $paciente = $this->Paciente_Model->get($data->id_paciente);
          $celular = $paciente->celular;
        } else if (!empty($data->asunto)) {
          $celular = $data->asunto;
        }

        if (!empty($celular)) {
          $this->load->helper("sms_helper");
          $salida = send_sms(array(
            "numero"=>$celular,
            "texto"=>$data->texto,
          ));
        }

      }

    }
    return $id;
  }

  function update($id,$data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    $data->fecha = substr($data->fecha, 0, 10)." ".$data->hora;
    $data->fecha_visto = fecha_mysql($data->fecha_visto);
    return parent::update($id,$data);
  }
  
  function get($id) {
    $id_empresa = parent::get_empresa();

    $q = $this->db->query("SELECT id_proyecto FROM empresas WHERE id = $id_empresa");
    $proy = $q->row();
    $id_proyecto = $proy->id_proyecto;

    $sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
    $sql.= " DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " DATE_FORMAT(C.fecha,'%H:%i') AS hora, ";
    $sql.= " IF(C.fecha_visto = '0000-00-00','',DATE_FORMAT(C.fecha_visto,'%d/%m/%Y %H:%i')) AS fecha_visto, ";
    if ($id_empresa == 228) {
      $sql.= " IF(CO.nombre IS NULL,'',CONCAT(CO.nombre,' ',CO.apellido)) AS nombre, ";  
    } else {
      $sql.= " IF(CO.nombre IS NULL,'',CO.nombre) AS nombre, ";  
    }
    $sql.= " IF(CO.email IS NULL,'',CO.email) AS email, ";
    $sql.= " IF(CO.telefono IS NULL,'',CO.telefono) AS telefono, ";
    if ($id_empresa != 228) $sql.= " IF(CO.celular IS NULL,'',CO.celular) AS celular, ";
    if ($id_proyecto == 11) {
      $sql.= " IF(V.nombre IS NULL,'',V.nombre) AS viaje_nombre, ";
      $sql.= " IF(V.path IS NULL,'',V.path) AS viaje_path, ";
    } else if ($id_proyecto == 3) {
      $sql.= " IF(P.nombre IS NULL,'',P.nombre) AS propiedad_nombre, ";
      $sql.= " IF(P.path IS NULL,'',P.path) AS propiedad_path, ";
      $sql.= " IF(P.calle IS NULL,'',CONCAT(P.calle,' ',P.altura)) AS propiedad_direccion, ";
      $sql.= " IF(P.id_tipo_operacion IS NULL,0,P.id_tipo_operacion) AS propiedad_id_tipo_operacion, ";
      $sql.= " IF(P.id_tipo_inmueble IS NULL,0,P.id_tipo_inmueble) AS propiedad_id_tipo_inmueble, ";    
      $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS propiedad_ciudad, ";      
    } else if ($id_proyecto == 2) {
      $sql.= " IF(A.nombre IS NULL,'',A.nombre) AS articulo_nombre, ";
      $sql.= " IF(A.path IS NULL,'',A.path) AS articulo_path, ";
    } else if ($id_proyecto == 7) {
      $sql.= " IF(TM.estado IS NULL,0,TM.estado) AS estado_turno, ";
      $sql.= " IF(CO.nombre IS NULL,'',CO.nombre) AS paciente, "; 
    }
    $sql.= " IF(ASUN.color IS NULL,'',ASUN.color) AS color_asunto, ";
    $sql.= " IF(E.titulo IS NULL,'',E.titulo) AS entrada_nombre, ";
    $sql.= " IF(E.path IS NULL,'',E.path) AS entrada_path, ";
    $sql.= " IF(O.nombre IS NULL,'',O.nombre) AS origen, ";
    $sql.= " IF(O.path IS NULL,'',O.path) AS origen_path, ";
    $sql.= " IF(O.color IS NULL,'',O.color) AS color_origen, ";
    $sql.= " IF(EM.fecha IS NULL,'',DATE_FORMAT(EM.fecha,'%d/%m/%Y %H:%i')) AS email_fecha, ";
    $sql.= " IF(EM_U.nombre IS NULL,'',EM_U.nombre) AS email_usuario, ";    
    $sql.= " IF(EM.texto IS NULL,'',EM.texto) AS texto_respuesta, ";
    $sql.= " IF(EM.archivo IS NULL,'',EM.archivo) AS email_archivo_adjunto, ";
    $sql.= " IF(U.nombre IS NULL,'',U.nombre) AS usuario ";    
    $sql.= "FROM crm_consultas C ";
    if ($id_empresa == 228) {
      $sql.= "LEFT JOIN pres_clientes CO ON (C.id_contacto = CO.id AND C.id_empresa = CO.id_empresa) ";
    } else {
      $sql.= "LEFT JOIN clientes CO ON (C.id_contacto = CO.id AND C.id_empresa = CO.id_empresa) ";  
    }
    $sql.= "LEFT JOIN crm_origenes O ON (C.id_origen = O.id) ";
    $sql.= "LEFT JOIN crm_asuntos ASUN ON (C.id_asunto = ASUN.id) ";
    if ($id_proyecto == 3)  {
      $sql.= "LEFT JOIN inm_propiedades P ON (C.id_referencia = P.id AND C.id_empresa_relacion = P.id_empresa) ";
      $sql.= "LEFT JOIN com_localidades L ON (P.id_localidad = L.id) ";
    } else if ($id_proyecto == 11) {
      $sql.= "LEFT JOIN via_viajes V ON (C.id_referencia = V.id AND C.id_empresa = V.id_empresa) ";
    } else if ($id_proyecto == 2) {
      $sql.= "LEFT JOIN articulos A ON (C.id_referencia = A.id AND C.id_empresa = A.id_empresa) ";
    } else if ($id_proyecto == 7) {
      $sql.= "LEFT JOIN med_turnos_medicos TM ON (C.id_referencia = TM.id AND C.id_empresa = TM.id_empresa) ";
      $sql.= "LEFT JOIN med_pacientes PA ON (C.id_contacto = PA.id_cliente AND C.id_empresa = PA.id_empresa) ";
    }
    $sql.= "LEFT JOIN com_usuarios U ON (C.id_usuario = U.id AND C.id_empresa = U.id_empresa) ";
    $sql.= "LEFT JOIN crm_emails EM ON (C.id_email_respuesta = EM.id AND C.id_empresa = EM.id_empresa) ";
    $sql.= "LEFT JOIN not_entradas E ON (C.id_entrada = E.id AND C.id_empresa = E.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios EM_U ON (EM_U.id = EM.id_usuario AND C.id_empresa = EM_U.id_empresa) ";    
    $sql.= "WHERE C.id = $id ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $row = $q->row();
    if ($row !== FALSE) {
      $row->texto = nl2br($row->texto);
      $row->texto_respuesta = nl2br($row->texto_respuesta);
    }
    return $q->row();
  }

  function count_all() {
    $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM crm_consultas ";
    $sql.= "WHERE id_empresa = ".parent::get_empresa()." ";
    $sql.= "AND tipo = 0 AND id_origen NOT IN (12,13,14,15,16,18,20) ";
    $q = $this->db->query($sql);
    $row = $q->row();
    return $row->cantidad;
  }

  function contar($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $in_origenes = isset($config["in_origenes"]) ? $config["in_origenes"] : "";
    $not_in_origenes = isset($config["not_ids_origen"]) ? $config["not_ids_origen"] : "";
    $clientes_unicos = isset($config["clientes_unicos"]) ? $config["clientes_unicos"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $referencia_unica = isset($config["referencia_unica"]) ? $config["referencia_unica"] : 0;
    $tipo_estado = isset($config["tipo_estado"]) ? $config["tipo_estado"] : -1;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $custom_1 = isset($config["custom_1"]) ? $config["custom_1"] : "";
    $id_referencia = isset($config["id_referencia"]) ? $config["id_referencia"] : 0;

    $sql = "SELECT ";
    // Contamos los clientes unicos
    if ($clientes_unicos == 1) $sql.= "IF(COUNT(DISTINCT id_contacto) IS NULL,0,COUNT(DISTINCT id_contacto)) AS cantidad ";
    // Contamos las propiedades/articulos consultados
    else if ($referencia_unica == 1) $sql.= "IF(COUNT(DISTINCT id_referencia) IS NULL,0,COUNT(DISTINCT id_referencia)) AS cantidad ";
    // Contamos la cantidad total de consultas
    else $sql.= "IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM crm_consultas C ";
    $sql.= "LEFT JOIN clientes CLI ON (C.id_empresa = CLI.id_empresa AND C.id_contacto = CLI.id) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    if (!empty($in_origenes)) $sql.= "AND id_origen IN ($in_origenes) ";
    if (!empty($not_in_origenes)) $sql.= "AND id_origen NOT IN ($not_in_origenes) ";
    if ($tipo != -1) $sql.= "AND C.tipo = '$tipo' ";
    if ($tipo_estado != -1) $sql.= "AND CLI.tipo = '$tipo_estado' ";
    if (!empty($id_usuario)) $sql.= "AND C.id_usuario = '$id_usuario' ";
    if (!empty($fecha)) $sql.= "AND DATE_FORMAT(C.fecha,'%Y-%m-%d') = '$fecha' ";
    if (!empty($desde)) $sql.= "AND DATE_FORMAT(C.fecha,'%Y-%m-%d') >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND DATE_FORMAT(C.fecha,'%Y-%m-%d') <= '$hasta' ";
    if (!empty($id_referencia)) $sql.= "AND C.id_referencia = '$id_referencia' ";
    if (!empty($custom_1)) $sql.= "AND C.custom_1 = '$custom_1' ";
    $this->sql = $sql;

    $q = $this->db->query($sql);
    $row = $q->row();
    return $row->cantidad;    
  }
  
  function buscar($conf = array()) {
    
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    if (empty($id_empresa)) return array();
    $buscar_respuestas = isset($conf["buscar_respuestas"]) ? $conf["buscar_respuestas"] : 1;
    $buscar_adjuntos = isset($conf["buscar_adjuntos"]) ? $conf["buscar_adjuntos"] : 1;
    $tiene_id_referencia = isset($conf["tiene_id_referencia"]) ? $conf["tiene_id_referencia"] : -1;
    $id_origen = isset($conf["id_origen"]) ? $conf["id_origen"] : 0;
    $custom_1 = isset($conf["custom_1"]) ? $conf["custom_1"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $tipo = isset($conf["tipo"]) ? $conf["tipo"] : -1;
    $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
    $id_contacto = isset($conf["id_contacto"]) ? $conf["id_contacto"] : 0;
    $estado = isset($conf["estado"]) ? $conf["estado"] : -1;
    $id_asunto = isset($conf["id_asunto"]) ? $conf["id_asunto"] : 0;
    $id_profesional = isset($conf["id_profesional"]) ? $conf["id_profesional"] : 0;
    $estado_turno = isset($conf["estado_turno"]) ? $conf["estado_turno"] : -1;
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : "";
    $desde = isset($conf["desde"]) ? $conf["desde"] : "";
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
    $not_ids_origen = isset($conf["not_ids_origen"]) ? $conf["not_ids_origen"] : "";
    $offset = isset($conf["offset"]) ? $conf["offset"] : 20;
    $id_origenes = isset($conf["id_origenes"]) ? str_replace("-",",",$conf["id_origenes"]) : "";
    $id_consulta_padre = isset($conf["id_consulta_padre"]) ? $conf["id_consulta_padre"] : 0;
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "C.fecha DESC, C.id DESC ";

    $sql = "SELECT id_proyecto FROM empresas WHERE id = $id_empresa";
    $q = $this->db->query($sql);
    $proy = $q->row();
    $id_proyecto = $proy->id_proyecto;
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
    $sql.= " DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " DATE_FORMAT(C.fecha,'%H:%i') AS hora, ";
    $sql.= " IF(C.fecha_visto = '0000-00-00','',DATE_FORMAT(C.fecha_visto,'%d/%m/%Y %H:%i')) AS fecha_visto, ";
    if ($id_empresa == 228) {
      $sql.= " IF((CO.nombre IS NULL OR CO.nombre = '0'),'',CONCAT(CO.nombre,' ',CO.apellido)) AS nombre, ";  
    } else {
      $sql.= " IF((CO.nombre IS NULL OR CO.nombre = '0'),'',CO.nombre) AS nombre, ";  
    }
    $sql.= " IF(CO.email IS NULL,'',CO.email) AS email, ";
    $sql.= " IF(CO.telefono IS NULL,'',CO.telefono) AS telefono, ";
    if ($id_empresa != 228) $sql.= " IF(CO.celular IS NULL,'',CO.celular) AS celular, ";
    $sql.= " IF(E.titulo IS NULL,'',E.titulo) AS entrada_nombre, ";
    $sql.= " IF(E.path IS NULL,'',E.path) AS entrada_path, ";    
    if ($id_proyecto == 11) {
      $sql.= " IF(V.nombre IS NULL,'',V.nombre) AS viaje_nombre, ";
      $sql.= " IF(V.path IS NULL,'',V.path) AS viaje_path, ";
    } else if ($id_proyecto == 3) {
      $sql.= " IF(P.nombre IS NULL,'',P.nombre) AS propiedad_nombre, ";
      $sql.= " IF(P.path IS NULL,'',P.path) AS propiedad_path, ";
      $sql.= " IF(P.calle IS NULL,'',CONCAT(P.calle,' ',P.altura)) AS propiedad_direccion, ";
      $sql.= " IF(P.id_tipo_operacion IS NULL,0,P.id_tipo_operacion) AS propiedad_id_tipo_operacion, ";
      $sql.= " IF(P.id_tipo_inmueble IS NULL,0,P.id_tipo_inmueble) AS propiedad_id_tipo_inmueble, ";    
      $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS propiedad_ciudad, ";      
    } else if ($id_proyecto == 2) {
      $sql.= " IF(A.nombre IS NULL,'',A.nombre) AS articulo_nombre, ";
      $sql.= " IF(A.path IS NULL,'',A.path) AS articulo_path, ";
    } else if ($id_proyecto == 7) {
      $sql.= " IF(TM.estado IS NULL,0,TM.estado) AS estado_turno, ";
      $sql.= " IF(CO.nombre IS NULL,'',CO.nombre) AS paciente, "; 
    }
    $sql.= " IF(ASUN.color IS NULL,'',ASUN.color) AS color_asunto, ";
    $sql.= " IF(O.nombre IS NULL,'',O.nombre) AS origen, ";
    $sql.= " IF(O.path IS NULL,'',O.path) AS origen_path, ";
    $sql.= " IF(O.color IS NULL,'',O.color) AS color_origen, ";
    $sql.= " IF(EM.fecha IS NULL,'',DATE_FORMAT(EM.fecha,'%d/%m/%Y %H:%i')) AS email_fecha, ";
    $sql.= " IF(EM.archivo IS NULL,'',EM.archivo) AS email_archivo_adjunto, ";
    $sql.= " IF(EM_U.nombre IS NULL,'',EM_U.nombre) AS email_usuario, ";
    $sql.= " IF(U.nombre IS NULL,'',U.nombre) AS usuario ";
    $sql.= "FROM crm_consultas C ";
    if ($id_empresa == 228) {
      $sql.= "LEFT JOIN pres_clientes CO ON (C.id_contacto = CO.id AND C.id_empresa = CO.id_empresa) ";
    } else {
      $sql.= "LEFT JOIN clientes CO ON (C.id_contacto = CO.id AND C.id_empresa = CO.id_empresa) ";  
    }
    $sql.= "LEFT JOIN crm_origenes O ON (C.id_origen = O.id) ";
    $sql.= "LEFT JOIN crm_asuntos ASUN ON (C.id_asunto = ASUN.id) ";
    if ($id_proyecto == 3)  {
      $sql.= "LEFT JOIN inm_propiedades P ON (C.id_referencia = P.id AND C.id_empresa_relacion = P.id_empresa) ";
      $sql.= "LEFT JOIN com_localidades L ON (P.id_localidad = L.id) ";
    } else if ($id_proyecto == 11) {
      $sql.= "LEFT JOIN via_viajes V ON (C.id_referencia = V.id AND C.id_empresa = V.id_empresa) ";
    } else if ($id_proyecto == 2) {
      $sql.= "LEFT JOIN articulos A ON (C.id_referencia = A.id AND C.id_empresa = A.id_empresa) ";
    } else if ($id_proyecto == 7) {
      $sql.= "LEFT JOIN med_turnos_medicos TM ON (C.id_referencia = TM.id AND C.id_empresa = TM.id_empresa) ";
      $sql.= "LEFT JOIN med_pacientes PA ON (C.id_contacto = PA.id_cliente AND C.id_empresa = PA.id_empresa) ";
    }
    $sql.= "LEFT JOIN com_usuarios U ON (C.id_usuario = U.id AND C.id_empresa = U.id_empresa) ";
    $sql.= "LEFT JOIN crm_emails EM ON (C.id_email_respuesta = EM.id AND C.id_empresa = EM.id_empresa) ";
    $sql.= "LEFT JOIN not_entradas E ON (C.id_entrada = E.id AND C.id_empresa = E.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios EM_U ON (EM_U.id = EM.id_usuario AND C.id_empresa = EM_U.id_empresa) ";    
    $sql.= "WHERE 1=1 ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    $sql.= "AND C.id_email_respuesta = $id_consulta_padre ";
    if ($id_origen != 0) $sql.= "AND C.id_origen = $id_origen ";
    else if (!empty($id_origenes)) $sql.= "AND C.id_origen IN ($id_origenes) ";
    if (!empty($custom_1)) $sql.= "AND C.custom_1 = $custom_1 ";
    if (!empty($id_asunto)) $sql.= "AND C.id_asunto = $id_asunto ";
    if (!empty($id_usuario)) $sql.= "AND C.id_usuario = $id_usuario ";
    if (!empty($id_contacto)) $sql.= "AND C.id_contacto = $id_contacto ";
    if (!empty($id_profesional)) $sql.= "AND TM.id_profesional = $id_profesional ";
    if (!empty($not_ids_origen)) $sql.= "AND C.id_origen NOT IN ($not_ids_origen) ";
    if ($estado_turno != -1) $sql.= "AND TM.estado = $estado_turno ";
    if ($tiene_id_referencia == 1) $sql.= "AND C.id_referencia != 0 ";
    else if ($tiene_id_referencia == 0) $sql.= "AND C.id_referencia = 0 ";
    if (!empty($fecha)) $sql.= "AND DATE_FORMAT(C.fecha,'%Y-%m-%d') = '$fecha' ";
    if (!empty($desde)) $sql.= "AND DATE_FORMAT(C.fecha,'%Y-%m-%d') >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND DATE_FORMAT(C.fecha,'%Y-%m-%d') <= '$hasta' ";
    if ($tipo != -1) $sql.= "AND C.tipo = '$tipo' ";
    if ($estado != -1) $sql.= "AND C.estado = '$estado' ";
    //if (isset($conf["filter"]) && !empty($conf["filter"])) $sql.= "AND A.codigo LIKE '%".$conf["filter"]."%' ";
    $sql.= "ORDER BY $order_by ";
    //if (empty($order)) $sql.= "ORDER BY A.nombre ASC ";
    //else $sql.= "ORDER BY A.$order ";
    $sql.= "LIMIT $limit, $offset";
    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $resultado = array();
    foreach($q->result() as $res) {
      $res->children = array();
      if ($buscar_respuestas == 1) {
        $config["id_consulta_padre"] = $res->id;
        $children = $this->buscar($config);
        $res->children = $children["results"];
      }

      // Obtenemos si tiene adjuntos
      $res->adjuntos = array();
      if ($buscar_adjuntos == 1) {
        $sql_adj = "SELECT * FROM crm_emails_adjuntos WHERE id_empresa = $id_empresa AND id_email = $res->id AND tipo = 0 ";
        $q_adj = $this->db->query($sql_adj);
        $res->adjuntos = $q_adj->result();        
      }

      $resultado[] = $res;
    }
    
    return array(
      "sql"=>$sql,
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

}