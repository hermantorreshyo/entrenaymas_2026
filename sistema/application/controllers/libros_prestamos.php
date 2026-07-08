<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Libros_Prestamos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Libro_Prestamo_Model', 'modelo');
  }
  
  private function remove_properties($array) {
    unset($array->libro);
		unset($array->alumno);
    unset($array->alumno_email);
		unset($array->autor);
		unset($array->dias_atraso);
    unset($array->modificar_disponibilidad);
  }  
  
  function update($id) {
    
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
		$this->load->helper("fecha_helper");
    $array = $this->parse_put();
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
		$array->fecha_desde = fecha_mysql($array->fecha_desde);
		$array->fecha_hasta = fecha_mysql($array->fecha_hasta);
		$array->fecha_devuelto = fecha_mysql($array->fecha_devuelto);		
    $modificar_disponibilidad = $array->modificar_disponibilidad;
    
    if (isset($array->ids_libros) && !empty($array->ids_libros)) {
      $ids_libros = $array->ids_libros;
      $this->remove_properties($array); // Eliminamos todo lo que no se persiste
      // Recorremos todos los libros y hacemos un prestamo por cada uno
      foreach($ids_libros as $id_libro) {
        $array->id_libro = $id_libro;
        $this->modelo->save($array);
      }
    } else {
      $this->remove_properties($array); // Eliminamos todo lo que no se persiste
      $this->modelo->save($array);      

      // Debemos descontar la cantidad disponible de cada libro
      if ($modificar_disponibilidad == 1) {
        $operacion = ($array->devuelto == 0) ? "-" : "+";
        $this->db->query("UPDATE biblio_libros SET disponibles = disponibles $operacion 1 WHERE id = $array->id_libro");
      }
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
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
		$array->fecha_desde = fecha_mysql($array->fecha_desde);
		$array->fecha_hasta = fecha_mysql($array->fecha_hasta);
		$array->fecha_devuelto = fecha_mysql($array->fecha_devuelto);		

    $ids_libros = $array->ids_libros;
    $this->remove_properties($array); // Eliminamos todo lo que no se persiste

    // Recorremos todos los libros y hacemos un prestamo por cada uno
    foreach($ids_libros as $id_libro) {
      $array->id_libro = $id_libro;
      $insert_id = $this->modelo->save($array);  

      // Debemos descontar la cantidad disponible de cada libro
      $this->db->query("UPDATE biblio_libros SET disponibles = disponibles - 1 WHERE id = $id_libro");
    }

    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
    );
    echo json_encode($salida);    
  }
  
  /**
   *  Obtenemos los datos de un libro en particular
   */
  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {
      $sql = "SELECT P.* ";
      $sql.= "FROM biblio_libros_prestamos P ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "ORDER BY P.fecha_desde DESC ";
      $q = $this->db->query($sql);
      $result = $q->result();
      echo json_encode(array(
        "results"=>$result,
        "total"=>sizeof($result)
      ));
    } else {
      $libro = $this->modelo->get($id);
      echo json_encode($libro);
    }
    
  }
  
  
  /**
   *  Muestra todos los libros filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver() {
    
    $limit = $this->input->get("limit");
		$filter = ($this->input->get("filter"));
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
		$id_libro = $this->input->get("id_libro");
		$id_alumno = $this->input->get("id_alumno");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "limit"=>$limit,
      "offset"=>$offset,
			"id_libro"=>$id_libro,
			"id_alumno"=>$id_alumno,
    );
    
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }

  function devolver() {
    $this->load->helper("fecha_helper");
    $fecha_devuelto = $this->input->post("fecha_devuelto");
    $ids = $this->input->post("ids");
    $observaciones = $this->input->post("observaciones");
    $devuelto = $this->input->post("devuelto");
    $fecha_devuelto = fecha_mysql($fecha_devuelto);       
    foreach($ids as $id) {
      $sql = "UPDATE biblio_libros_prestamos SET ";
      $sql.= " fecha_devuelto = '$fecha_devuelto', ";
      $sql.= " devuelto = $devuelto, ";
      $sql.= " observaciones = '$observaciones' ";
      $sql.= "WHERE id = $id ";
      $this->db->query($sql);

      // Actualizamos la disponibilidad del libro
      $qq = $this->db->query("SELECT * FROM biblio_libros_prestamos WHERE id = $id");
      $row = $qq->row();
      $this->db->query("UPDATE biblio_libros SET disponibles = disponibles + 1 WHERE id = $row->id_libro");
    }
    echo json_encode(array(
      "error"=>0
    ));
  }

  function delete($id = null) {

    // Si el prestamo no fue devuelto, tenemos que actualizar la disponibilidad del libro
    $qq = $this->db->query("SELECT * FROM biblio_libros_prestamos WHERE id = $id");
    $row = $qq->row();
    if ($row->devuelto == 0) {
      $this->db->query("UPDATE biblio_libros SET disponibles = disponibles + 1 WHERE id = $row->id_libro");
    }
    $this->modelo->delete($id);
    echo json_encode(array());
  }
	
}