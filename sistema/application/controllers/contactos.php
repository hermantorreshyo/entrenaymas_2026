<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Contactos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Contacto_Model', 'modelo');
  }

  // Registra cuando el cliente hizo click en un email de interesado
  function registrar_interes_email() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_propiedad = parent::get_get("p",0);
    $id_cliente = parent::get_get("c",0);
    $id_empresa = parent::get_get("e",0);
    $id_consulta = parent::get_get("x",0);
    $this->load->model("Empresa_Model");
    $this->load->model("Propiedad_Model");
    $propiedad = $this->Propiedad_Model->get_by_id($id_propiedad,array(
      "id_empresa"=>$id_empresa,
    ));

    // Registramos que se abrio el link
    $fecha = date("Y-m-d H:i:s");
    $sql = "UPDATE crm_consultas SET fecha_visto = '$fecha' WHERE id = $id_consulta AND id_referencia = $id_propiedad AND id_contacto = $id_cliente AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    // Redirecionamos
    $empresa = $this->Empresa_Model->get($id_empresa);
    $dominio = (isset($empresa->dominios) && sizeof($empresa->dominios)>0) ? $empresa->dominios[0] : "www.varcreative.com/sandbox";
    $link = "https://".$dominio."/sistema/propiedades/function/ficha/".$propiedad->hash."/";
    header("Location: $link");
  }

  // Envia emails a los interesados de una propiedad
  function enviar_email_interesados() {
    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $id_empresa = parent::get_empresa();
    $id_propiedad = parent::get_post("id_propiedad",0);
    $id_clientes = parent::get_post("id_clientes","");
    $clientes = explode("-", $id_clientes);
    $this->load->model("Empresa_Model");
    $this->load->model("Propiedad_Model");
    $this->load->model("Cliente_Model");
    $this->load->model("Consulta_Model");
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("email-interesado",$id_empresa);
    if ($template === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No hay un template configurado para enviar los emails de interesados.",
      ));
      exit();
    }
    $bcc_array = array("basile.matias99@gmail.com");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $dominio = (isset($empresa->dominios) && sizeof($empresa->dominios)>0) ? $empresa->dominios[0] : "www.varcreative.com/sandbox";
    $propiedad = $this->Propiedad_Model->get_by_id($id_propiedad);
    foreach($clientes as $id_cliente) {

      $cliente = $this->Cliente_Model->get($id_cliente,$id_empresa,array(
        "buscar_consultas"=>0,
        "buscar_etiquetas"=>0,
      ));

      // Guardamos que le enviamos un email de interesado en propiedad a determinado usuario
      $id_consulta = $this->Consulta_Model->registro_email_interesado_propiedad(array(
        "id_propiedad"=>$id_propiedad,
        "id_empresa"=>$id_empresa,
        "id_contacto"=>$id_cliente,
        "propiedad"=>$propiedad->nombre,
      ));

      $asunto = $template->nombre;
      $asunto = str_replace("{{nombre}}",$cliente->nombre,$asunto);

      $body = $template->texto;
      $body = str_replace("{{nombre}}",$cliente->nombre,$body);
      $body = str_replace("{{propiedad_nombre}}",$propiedad->nombre,$body);
      $body = str_replace("{{propiedad_direccion}}",$propiedad->calle,$body);
      if (empty($propiedad->path)) {
        $body = str_replace("{{propiedad_foto}}","\" style=\"display:none ",$body);
      } else if (strpos($propiedad->path, "http") == 0) {
        $body = str_replace("{{propiedad_foto}}",$propiedad->path,$body);
      } else {
        $body = str_replace("{{propiedad_foto}}","https://www.varcreative.com/sistema/uploads/".$propiedad->path,$body);
      }
      $body = str_replace("{{propiedad_direccion}}",$propiedad->calle,$body);
      if ($propiedad->publica_precio == 0) {
        $body = str_replace("{{propiedad_precio}}","",$body);  
      } else {
        $body = str_replace("{{propiedad_precio}}",$propiedad->moneda." ".number_format($propiedad->precio_final,0,".",","),$body);
      }

      $body = str_replace("{{empresa_nombre}}",$empresa->razon_social,$body);
      if (isset($empresa->config["logo_1"]) && !empty($empresa->config["logo_1"])) {
        $body = str_replace("{{empresa_logo}}","https://www.varcreative.com/sistema/".$empresa->config["logo_1"],$body);
      }
      $body = str_replace("{{empresa_telefono}}",$empresa->telefono,$body);
      $telefono = $empresa->telefono;
      $telefono = preg_replace("/[^0-9]/", "", $telefono);
      $body = str_replace("{{empresa_telefono_link}}",$telefono,$body);
      $body = str_replace("{{empresa_direccion}}",utf8_encode($empresa->direccion),$body);
      $body = str_replace("{{empresa_email}}",$empresa->email,$body);

      $propiedad_link = "https://www.varcreative.com/sistema/contactos/function/registrar_interes_email/?e=".$propiedad->id_empresa."&p=".$propiedad->id."&c=".$id_cliente."&x=".$id_consulta;
      $body = str_replace("{{propiedad_link}}",$propiedad_link,$body);
      $body = str_replace("'", "\"", $body);

      mandrill_send(array(
        "to"=>$cliente->email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>$empresa->nombre,
        "subject"=>$asunto,
        "body"=>$body,
        "reply_to"=>$empresa->email,
        //"bcc"=>$bcc_array,
      ));
    }
    echo json_encode(array("error"=>0));
  }

  function buscar_interesados_por_propiedad() {

    $id_empresa = parent::get_empresa();
    $id_propiedad = parent::get_get("id_propiedad",0);
    $this->load->model("Propiedad_Model");
    $this->load->model("Cliente_Model");
    $propiedad = $this->Propiedad_Model->get_by_id($id_propiedad);

    // Solamente tomamos las busquedas interesadas de hace 60 dias
    $desde = date("Y-m-d",strtotime("-60 days"));
    $sql = "SELECT *, ";
    $sql.= " DATE_FORMAT(fecha,'%d/%m/%Y %H:%i') AS fecha ";
    $sql.= "FROM inm_busquedas_contactos WHERE id_empresa = $id_empresa AND fecha > '$desde' ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $row) {

      // Buscamos por localidad
      if (!empty($row->id_localidad)) {
        $localidades = explode("-", $row->id_localidad);  
        if (!in_array($propiedad->id_localidad, $localidades)) continue;
      }

      // Buscamos por tipo_operacion
      if (!empty($row->id_tipo_operacion)) {
        $tipo_operaciones = explode("-", $row->id_tipo_operacion);  
        if (!in_array($propiedad->id_tipo_operacion, $tipo_operaciones)) continue;
      }

      // Buscamos por tipo_inmueble
      if (!empty($row->id_tipo_inmueble)) {
        $tipo_inmuebles = explode("-", $row->id_tipo_inmueble);  
        if (!in_array($propiedad->id_tipo_inmueble, $tipo_inmuebles)) continue;
      }

      // Si llego hasta aca, es porque el contacto aplica a la busqueda
      $cliente = $this->Cliente_Model->get($row->id_cliente,$id_empresa,array(
        "buscar_consultas"=>0,
        "buscar_etiquetas"=>0,
      ));

      $obj = new stdClass();
      $obj->nombre = $cliente->nombre;
      $obj->telefono = $cliente->telefono;
      $obj->email = $cliente->email;
      $obj->fecha = $row->fecha;
      $obj->id_contacto = $cliente->id;
      $obj->link = $propiedad->link;

      $salida[] = $obj;
    }
    echo json_encode($salida);
  }

  function ver_interesadas() {
    $id_empresa = parent::get_empresa();
    $id_cliente = parent::get_get("id_cliente",0);
    $sql = "SELECT PC.*, A.nombre, A.path, PC.id_empresa_propiedad AS id_empresa, ";
    $sql.= " IF(PC.fecha='0000-00-00 00:00:00','',DATE_FORMAT(PC.fecha,'%d/%m/%Y %H:%i')) AS fecha, ";
    $sql.= " IF(TE.nombre IS NULL,'',TE.nombre) AS tipo_estado, ";
    $sql.= " IF(TI.nombre IS NULL,'',TI.nombre) AS tipo_inmueble, ";
    $sql.= " IF(X.nombre IS NULL,'',X.nombre) AS tipo_operacion, ";
    $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= "FROM inm_propiedades_contactos PC ";
    $sql.= "INNER JOIN inm_propiedades A ON (PC.id_empresa_propiedad = A.id_empresa AND PC.id_propiedad = A.id) ";
    $sql.= "LEFT JOIN inm_tipos_estado TE ON (A.id_tipo_estado = TE.id) ";
    $sql.= "LEFT JOIN inm_tipos_inmueble TI ON (A.id_tipo_inmueble = TI.id) ";
    $sql.= "LEFT JOIN inm_tipos_operacion X ON (A.id_tipo_operacion = X.id) ";
    $sql.= "LEFT JOIN com_localidades L ON (A.id_localidad = L.id) ";
    $sql.= "WHERE PC.id_empresa = $id_empresa AND PC.id_contacto = $id_cliente ";
    $sql.= "ORDER BY PC.fecha DESC ";
    $q = $this->db->query($sql);
    echo json_encode(array(
      "results"=>$q->result(),
    ));
  }

  function eliminar_interes() {
    $id_empresa = parent::get_empresa();
    $id = parent::get_post("id",0);
    $sql = "DELETE FROM inm_propiedades_contactos WHERE id_empresa = $id_empresa AND id = $id ";
    $this->db->query($sql);
    echo json_encode(array());
  }

  function guardar_propiedades_interesadas() {
    $id_contacto = parent::get_post("id_cliente",0);
    $id_empresa = parent::get_empresa();
    $id_empresa_propiedad = parent::get_post("id_empresa_propiedad",parent::get_empresa());
    if (!is_numeric($id_contacto) || empty($id_contacto)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: el contacto no es valido"
      ));
      exit();
    }
    $ids = parent::get_post("ids",array());
    foreach($ids as $id_propiedad) {
      $sql = "SELECT * FROM inm_propiedades_contactos WHERE id_empresa_propiedad = $id_empresa_propiedad AND id_contacto = $id_contacto AND id_propiedad = $id_propiedad ";
      $q = $this->db->query($sql);
      if ($q->num_rows()<=0) {
        $sql = "INSERT INTO inm_propiedades_contactos (id_empresa,id_contacto,id_propiedad,fecha,id_empresa_propiedad) VALUES ($id_empresa,$id_contacto,$id_propiedad,NOW(),$id_empresa_propiedad)";
        $this->db->query($sql);
      }
    }
    echo json_encode(array("error"=>0));
  }


  function ver_busquedas() {
    $id_empresa = parent::get_empresa();
    $id_cliente = parent::get_get("id_cliente",0);
    $sql = "SELECT *, ";
    $sql.= " DATE_FORMAT(IBC.fecha,'%d/%m/%Y %H:%i') AS fecha ";
    $sql.= "FROM inm_busquedas_contactos IBC ";
    $sql.= "WHERE IBC.id_empresa = $id_empresa AND IBC.id_cliente = $id_cliente ";
    //$sql.= "ORDER BY IBC.fecha DESC ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $row) {

      $row->localidades = "";
      if (!empty($row->id_localidad)) {
        // Buscamos las localidades
        $localidades = array();
        $row->id_localidad = str_replace("-", ",", $row->id_localidad);
        $sql = "SELECT * FROM com_localidades WHERE id IN ($row->id_localidad) ";
        $qq = $this->db->query($sql);
        foreach($qq->result() as $rr) {
          $localidades[] = $rr->nombre;
        }
        $row->localidades = implode(",", $localidades);
      }

      $row->tipos_operacion = "";
      if (!empty($row->id_tipo_operacion)) {
        // Buscamos las tipos_operacion
        $tipos_operacion = array();
        $row->id_tipo_operacion = str_replace("-", ",", $row->id_tipo_operacion);
        $sql = "SELECT * FROM inm_tipos_operacion WHERE id IN ($row->id_tipo_operacion) ";
        $qq = $this->db->query($sql);
        foreach($qq->result() as $rr) {
          $tipos_operacion[] = $rr->nombre;
        }
        $row->tipos_operacion = implode(",", $tipos_operacion);
      }

      $row->tipos_inmueble = "";
      if (!empty($row->id_tipo_inmueble)) {
        // Buscamos las tipos_inmueble
        $tipos_inmueble = array();
        $row->id_tipo_inmueble = str_replace("-", ",", $row->id_tipo_inmueble);
        $sql = "SELECT * FROM inm_tipos_inmueble WHERE id IN ($row->id_tipo_inmueble) ";
        $qq = $this->db->query($sql);
        foreach($qq->result() as $rr) {
          $tipos_inmueble[] = $rr->nombre;
        }
        $row->tipos_inmueble = implode(",", $tipos_inmueble);
      }

      $salida[] = $row;
    }
    echo json_encode($salida);
  }

  function guardar_busqueda() {
    $id_empresa = parent::get_empresa();
    $id_cliente = parent::get_post("id_cliente",0);
    $id_localidad = parent::get_post("id_localidad","");
    $id_tipo_operacion = parent::get_post("id_tipo_operacion","");
    $id_tipo_inmueble = parent::get_post("id_tipo_inmueble","");
    $sql = "INSERT INTO inm_busquedas_contactos (id_empresa, id_cliente, id_localidad, id_tipo_operacion, id_tipo_inmueble, fecha ";
    $sql.= ") VALUES (";
    $sql.= "'$id_empresa', '$id_cliente', '$id_localidad', '$id_tipo_operacion', '$id_tipo_inmueble', NOW() )";
    $this->db->query($sql);
    echo json_encode(array());
  }

  function eliminar_busqueda() {
    $id_empresa = parent::get_empresa();
    $id = parent::get_post("id",0);
    $sql = "DELETE FROM inm_busquedas_contactos WHERE id_empresa = $id_empresa AND id = $id ";
    $this->db->query($sql);
    echo json_encode(array());
  }
  
  function get_by_nombre() {
    $nombre = $this->input->get("term");
    $sql = "SELECT * FROM crm_contactos L ";
    $sql.= "WHERE L.nombre LIKE '%$nombre%' ";
    $sql.= "ORDER BY L.nombre ASC ";
    $sql.= "LIMIT 0,20 ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->nombre;
      $rr->label = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }  
  
  
}