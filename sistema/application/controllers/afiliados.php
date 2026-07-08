<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Afiliados extends REST_Controller
{
  function __construct() {
    parent::__construct();
    $this->load->model('Afiliado_Model', 'modelo');
  }

  function check() {

    $email = $this->input->post("nombre");
    $password = $this->input->post("password");
    
    $sql = "SELECT A.* ";
    $sql.= "FROM custom_afiliados A ";
    $sql.= "WHERE A.email = '$email' ";
    $sql.= "AND A.password = '$password' ";
    $sql.= "AND A.activo = 1 ";
    $sql.= "LIMIT 0,1 ";
    $query = $this->db->query($sql);

    // Datos invalidos
    $resultado = $query->result();
    if (empty($resultado)) {
      // Usuario incorrecto
      echo json_encode(array("error"=>true,"mensaje"=>"Nombre de usuario y/o claves incorrectos."));
      return;
    }
    $usuario = $query->row();
    // Guardamos el usuario en la session
    $_SESSION["id"] = $usuario->id;
    $_SESSION["email"] = $usuario->email;
    $_SESSION["nombre_usuario"] = $usuario->nombre;
    $_SESSION["id_empresa"] = $usuario->id_empresa;
    echo json_encode(array("error"=>false,"id_empresa"=>$usuario->id_empresa));
  }


  /*
  function reset_password() {
    $id_empresa = $this->input->post("id_empresa");
    $email = $this->input->post("email");
    $afiliado = $this->modelo->get_by_email($email,$id_empresa);
    if ($afiliado != FALSE) {
      // Generamos un nuevo password aleatorio

      // Codificamos el password en MD5

      // Guardamos el pass en la base de datos

      // Enviamos un email al usuario
    }
  }
  */

  function registro() {

    $this->load->helper("fecha_helper");
    $id_empresa = $this->input->post("id_empresa");
    $nombre = $this->input->post("nombre");
    $apellido = $this->input->post("apellido");
    $dni = $this->input->post("dni");
    $fecha_nac = fecha_mysql($this->input->post("fecha_nac"));
    $email = $this->input->post("email");
    $telefono = $this->input->post("telefono");
    $celular = $this->input->post("celular");
    $calle = $this->input->post("calle");
    $numero = $this->input->post("numero");
    $piso = $this->input->post("piso");
    $depto = $this->input->post("depto");
    $es_afiliado = $this->input->post("afiliado");
    $localidad = $this->input->post("localidad");
    $partido = $this->input->post("partido");
    $profesion = $this->input->post("profesion");
    $sexo = $this->input->post("sexo");
    $password = $this->input->post("password");
    $facebook = $this->input->post("facebook");
    $twitter = $this->input->post("twitter");
    $instagram = $this->input->post("instagram");
    $otras_redes = $this->input->post("otras_redes");

    $id_afiliado = 0;
    $afiliado = $this->modelo->get_by_email($email,$id_empresa);
    if ($afiliado === FALSE) {
      // Debemos guardar el afiliado
      $afiliado = new stdClass();
      $afiliado->activo = 0;
      $afiliado->apellido = $apellido;
      $afiliado->nombre = $nombre;
      $afiliado->dni = $dni;
      $afiliado->fecha_nac = $fecha_nac;
      $afiliado->email = $email;
      $afiliado->telefono = $telefono;
      $afiliado->celular = $celular;
      $afiliado->password = $password;
      $afiliado->calle = $calle;
      $afiliado->numero = $numero;
      $afiliado->piso = $piso;
      $afiliado->depto = $depto;
      $afiliado->facebook = $facebook;
      $afiliado->twitter = $twitter;
      $afiliado->instagram = $instagram;
      $afiliado->otras_redes = $otras_redes;
      $afiliado->sexo = $sexo;
      $afiliado->profesion = $profesion;
      $afiliado->localidad = $localidad;
      $afiliado->partido = $partido;
      $afiliado->afiliado = $es_afiliado;
      $afiliado->id_empresa = $id_empresa;
      $afiliado->fecha_inicial = date("Y-m-d");
      $id_afiliado = $this->modelo->insert($afiliado);
      $salida = array(
        "id"=>$id_afiliado,
        "error"=>0,
        "mensaje"=>"Muchas gracias por su registro! Los datos se han guardado correctamente.",
      );
    } else {
      // El afiliado ya existe
      $salida = array(
        "id"=>$afiliado->id,
        "mensaje"=>"El afiliado ya esta registrado en el sistema. Utilice sus datos de acceso para entrar.",
        "error"=>1,
      );
    }

    $sql = "SELECT * FROM crm_emails_templates WHERE id_empresa = $id_empresa AND clave = 'registro' ";
    $q_temp = $this->db->query($sql);
    if ($q_temp->num_rows()>0) {
      $temp = $q_temp->row();    
      $body = $temp->texto;
      $body = str_replace("{{nombre}}", $nombre, $body);
      $body = str_replace("{{link}}", "http://www.ucrchacabuco.com.ar/?u=".$id_afiliado, $body);
      $headers = "From: info@ucrchacabuco.com.ar\r\n";
      $headers.= "MIME-Version: 1.0\r\n";
      $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
      $headers.= "Bcc: basile.matias99@gmail.com\r\n";
      @mail($email,$temp->nombre,$body,$headers);
    }

    echo json_encode($salida);
  }

  function editar() {

    $this->load->helper("fecha_helper");
    $id_empresa = $this->input->post("id_empresa");
    $nombre = $this->input->post("nombre");
    $apellido = $this->input->post("apellido");
    $dni = $this->input->post("dni");
    $fecha_nac = fecha_mysql($this->input->post("fecha_nac"));
    $email = $this->input->post("email");
    $telefono = $this->input->post("telefono");
    $celular = $this->input->post("celular");
    $calle = $this->input->post("calle");
    $numero = $this->input->post("numero");
    $piso = $this->input->post("piso");
    $depto = $this->input->post("depto");
    $es_afiliado = $this->input->post("afiliado");
    $localidad = $this->input->post("localidad");
    $partido = $this->input->post("partido");
    $profesion = $this->input->post("profesion");
    $sexo = $this->input->post("sexo");
    $password = $this->input->post("password");
    $facebook = $this->input->post("facebook");
    $twitter = $this->input->post("twitter");
    $instagram = $this->input->post("instagram");
    $otras_redes = $this->input->post("otras_redes");

    $afiliado = $this->modelo->get_by_email($email,$id_empresa);
    if ($afiliado === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El afiliado no esta registrado",
      ));
    } else {

      $afiliado->apellido = $apellido;
      $afiliado->nombre = $nombre;
      $afiliado->dni = $dni;
      $afiliado->fecha_nac = $fecha_nac;
      $afiliado->email = $email;
      $afiliado->telefono = $telefono;
      $afiliado->celular = $celular;
      if (!empty($password)) $afiliado->password = $password;
      $afiliado->calle = $calle;
      $afiliado->numero = $numero;
      $afiliado->piso = $piso;
      $afiliado->depto = $depto;
      $afiliado->facebook = $facebook;
      $afiliado->twitter = $twitter;
      $afiliado->instagram = $instagram;
      $afiliado->otras_redes = $otras_redes;
      $afiliado->sexo = $sexo;
      $afiliado->profesion = $profesion;
      $afiliado->localidad = $localidad;
      $afiliado->partido = $partido;
      $afiliado->afiliado = $es_afiliado;

      $this->modelo->update($afiliado->id,$afiliado);
      echo json_encode(array(
        "error"=>0,
      ));
    }
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {

      $order_by = ($this->input->get("order_by") !== FALSE) ? $this->input->get("order_by")." " : "";
      $order = ($this->input->get("order") !== FALSE) ? $this->input->get("order") : "";
      $filter = ($this->input->get("term") !== FALSE) ? urldecode($this->input->get("term")) : "";
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");

      $r = $this->modelo->buscar(array(
        "filter"=>$filter,
        "order"=>$order_by.$order,
        "limit"=>$limit,
        "offset"=>$offset,
      ));
      echo json_encode($r);

    } else {
      $afiliado = $this->modelo->get($id);
      echo json_encode($afiliado);
    }
  }  
  
  function insert() {
    $array = $this->parse_put();
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    if (isset($array->fecha_nac)) $array->fecha_nac = fecha_mysql($array->fecha_nac);
    else $array->fecha_nac = date("Y-m-d");
    $array->fecha_inicial = date("Y-m-d");
    $array->id_empresa = $id_empresa;
    unset($array->usuario);
    
    // Controlamos si el email ya existe
    $email = trim($array->email);
    if (!empty($email)) {
      $q = $this->db->query("SELECT * FROM custom_afiliados WHERE email = '$array->email' AND id_empresa = $id_empresa");
      if ($q->num_rows()>0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: Ya existe un afiliado con el email $array->email."
        ));
        return;
      }
    }
    
    $id = $this->modelo->insert($array);
    echo json_encode(array(
      "id"=>$id,
      "error"=>0
    ));
  }
  
  function update($id) {
    
    if ($id == 0) { $this->insert($id); return; }
    $id_empresa = parent::get_empresa();
    $array = $this->parse_put();
    $this->load->helper("fecha_helper");
    $array->fecha_inicial = fecha_mysql($array->fecha_inicial);
    $array->fecha_nac = fecha_mysql($array->fecha_nac);
    $array->id_empresa = $id_empresa;
    unset($array->usuario);
    
    // Controlamos si el email ya existe
    $email = trim($array->email);
    if (!empty($email)) {
      $q = $this->db->query("SELECT * FROM custom_afiliados WHERE email = '$array->email' AND id != $id AND id_empresa = $id_empresa");
      if ($q->num_rows()>0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: El email es repetido con otro afiliado."
        ));
        return;
      }
    }
    $this->modelo->save($array);

    echo json_encode(array(
      "id"=>$id,
      "error"=>0
    ));
  }

  function exportar_csv() {
    $id_empresa = parent::get_empresa();
    $this->load->dbutil();
    $this->load->helper('download');
    $query = $this->db->query("SELECT * FROM custom_afiliados WHERE id_empresa = $id_empresa");
    $salida = $this->dbutil->csv_from_result($query, ";", "\r\n");
    force_download('afiliados.csv', $salida);
  }
  
  function importar() {
    $tabla = "afiliados";
    parent::import($tabla,1);
    header("Location: app/#$tabla");
  }
  
}