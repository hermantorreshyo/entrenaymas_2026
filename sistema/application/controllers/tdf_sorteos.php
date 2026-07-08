<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tdf_Sorteos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Tdf_Sorteo_Model', 'modelo');
  }

  function upload_images($param = array()) {
    $id_empresa = $this->get_empresa();
    return parent::upload_images(array(
      "clave_width"=>"tdf_sorteo_galeria_image_width",
      "clave_height"=>"tdf_sorteo_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/clasificados/",
    ));
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/clasificados/";
    $filename = $this->input->post("file");
    echo parent::save_image($dir,$filename);
  }

  function duplicar($id) {

    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");

    $auto = $this->modelo->get($id);
    if ($auto === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra la propiedad con ID: $id",
      ));
      return;
    }

    $images = $auto->images;
    $this->remove_properties($auto);

    $auto->id = 0;
    $auto->fecha_desde = fecha_mysql($auto->fecha_desde);
    $auto->fecha_hasta = fecha_mysql($auto->fecha_hasta);
    $insert_id = $this->modelo->insert($auto);

    // Actualizamos el link
    $auto->link = "web/sorteo/?id=$insert_id";
    $this->db->query("UPDATE custom_tdf_sorteos SET link = '$auto->link' WHERE id = $insert_id AND id_empresa = $auto->id_empresa");

    echo json_encode(array(
      "id"=>$insert_id
    ));
  }

  private function remove_properties($array) {
    unset($array->tipo);
    unset($array->localidad);
    unset($array->provincia);
    unset($array->clientes);
    unset($array->images);        
  }

  function update($id) {

    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();

    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;        

    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha_desde = fecha_mysql($array->fecha_desde);
    $array->fecha_hasta = fecha_mysql($array->fecha_hasta);

    // Eliminamos todo lo que no se persiste
    $images = $array->images;
    $this->remove_properties($array);

    // Actualizamos el link
    $array->link = "web/sorteo/?id=$id";

    // Actualizamos los datos del propiedad
    $this->modelo->save($array);

    // Guardamos las imagenes
    $this->db->query("DELETE FROM custom_tdf_sorteos_images WHERE id_sorteo = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO custom_tdf_sorteos_images (id_empresa,id_sorteo,path,orden) VALUES($id_empresa,$id,'$im',$k)");
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
    $array = $this->parse_put();

    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;

    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha_desde = fecha_mysql($array->fecha_desde);
    $array->fecha_hasta = fecha_mysql($array->fecha_hasta);

    // Eliminamos todo lo que no se persiste
    $images = $array->images;
    $this->remove_properties($array);

    $array->fecha_creacion = date("Y-m-d H:i:s");

    // Insertamos el propiedad
    $insert_id = $this->modelo->save($array);

    // Actualizamos el link
    $array->link = "web/sorteo/?id=$insert_id";
    $this->db->query("UPDATE custom_tdf_sorteos SET link = '$array->link' WHERE id = $insert_id AND id_empresa = $id_empresa");

    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO custom_tdf_sorteos_images (id_empresa,id_sorteo,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
      $k++;
    }


    // Tenemos que controlar cuales clientes tienen credito, y asignarlos al sorteo
    $sql = "SELECT * FROM clientes ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND saldo_inicial > 0 ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {

      $this->load->model("Email_Template_Model");
      $texto = $this->Email_Template_Model->get_by_key("sorteo-ok");
      if (empty($texto->nombre)) $texto->nombre = "Sorteo";
      if (empty($texto->texto)) $texto->texto = "Ya estas participando por el nuevo sorteo! Tu numero es {{numero}}.";
      $f_tar = date("Y-m-d H:i:s");

      $this->load->model("Empresa_Model");
      $empresa = $this->Empresa_Model->get($id_empresa);

      foreach($q->result() as $cliente) {

        // Obtenemos un nuevo numero para ese sorteo
        $numero = $this->modelo->get_numero($insert_id,$array->maximo);

        // Insertamos la participacion del cliente al sorteo
        $sql = "INSERT INTO custom_tdf_sorteos_clientes (id_empresa, id_sorteo, id_cliente, numero, fecha) VALUES(";
        $sql.= "$empresa->id, $insert_id, $cliente->id, $numero, '$f_tar')";
        $this->db->query($sql);

        // Enviamos un email al cliente que esta participando del nuevo sorteo
        $body = $texto->texto;
        $body = str_replace("{{cliente}}",$cliente->nombre,$body);
        $body = str_replace("{{numero}}",$numero,$body);
        
        $headers = "From: $empresa->email\r\n";
        $headers.= "MIME-Version: 1.0\r\n";
        $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $headers.= "BCC: basile.matias99@gmail.com\r\n";
        
        @mail($cliente->email,$texto->nombre,$body,$headers);

        // Como ya estan participando del sorteo, le bajamos el credito
        $sql = "UPDATE clientes SET ";
        $sql.= "saldo_inicial = saldo_inicial - 1 ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id = $cliente->id ";
        $this->db->query($sql);

      }
    }

    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }


  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {
      $resultado = $this->modelo->buscar();
      echo json_encode($resultado);
    } else {
      $auto = $this->modelo->get($id);
      echo json_encode($auto);
    }
  }


  /**
   *  Muestra todos los propiedades filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver($filter = "",$id_proveedor = 0,$id_marca = 0,
    $id_rubro = 0,$id_subrubro = 0, $fecha = "", $mostrar = 0, $negado = 0, $tipo_busqueda = 0) {

    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";

    $conf = array(
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }
  
}
