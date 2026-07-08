<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Campanias extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Campania_Model', 'modelo');
  }

  function ver_pagos() {

    $id_empresa = $this->get_empresa();
    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql($this->input->post("fecha"));
    $primer_dia = date('Y-m-01', strtotime($fecha));
    $id_vendedor = $this->input->post("id_vendedor");

    $sql = "SELECT F.*, ";
    $sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " (F.total * F.comision_vendedor / 100) AS comision, ";
    $sql.= " total - (F.total * F.comision_vendedor / 100) AS diferencia, ";
    $sql.= " IF (PC.nombre IS NULL,'',PC.nombre) AS campania, ";
    $sql.= " IF (C.nombre IS NULL,'Consumidor Final',C.nombre) AS cliente, ";
    $sql.= " IF (V.nombre IS NULL,'',V.nombre) AS vendedor ";
    $sql.= "FROM facturas F ";
    $sql.= " LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= " LEFT JOIN vendedores V ON (F.id_vendedor = V.id AND F.id_empresa = V.id_empresa) ";
    $sql.= " LEFT JOIN pub_campanias PC ON (F.id_referencia = PC.id AND F.id_empresa = PC.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    // Tomamos las que no esten pagadas a la fecha, o las que corresponden a este mes (esten pagadas o no)
    $sql.= "AND ((F.fecha <= '$fecha' AND F.pagada = 0) OR (F.fecha > '$primer_dia' AND F.fecha <= '$fecha')) ";
    $sql.= "AND F.id_tipo_comprobante != 0 ";
    if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
    $q = $this->db->query($sql);
    $resultado = $q->result();

    echo json_encode(array(
      "results"=>$resultado,
    ));
  }
  
  private function remove_properties($array) {
    unset($array->publicidad);
    unset($array->cliente);
    unset($array->vendedor);
    unset($array->primer_pago);
    unset($array->piezas);
    unset($array->dias_vencimiento);
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

  function eliminar_pieza() {
    $id = $this->input->post("id");
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM pub_piezas WHERE id = $id AND id_empresa = $id_empresa ");
    echo json_encode(array(
      "error"=>0,
    ));
  }
  
  function update($id) {
    
    if ($id == 0) { $this->insert(); return; }
    $this->load->model("Pieza_Model");
    $this->load->helper("file_helper");
		$this->load->helper("fecha_helper");
    $array = $this->parse_put();
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
		$array->valida_desde = fecha_mysql($array->valida_desde);
		$array->valida_hasta = fecha_mysql($array->valida_hasta);
    $piezas = $array->piezas;
    $this->remove_properties($array);
    
    // Actualizamos los datos del publicidad
    $this->modelo->save($array);

    // Recorremos las piezas
    foreach($piezas as $pieza) {
      $categorias_relacionados = $pieza->categorias_relacionados;
      unset($pieza->categorias_relacionados);
      unset($pieza->campania);
      unset($pieza->categoria);
      $pieza->fecha_desde = fecha_mysql($pieza->fecha_desde);
      $pieza->fecha_hasta = fecha_mysql($pieza->fecha_hasta);
      $pieza->id_campania = $id;
      $id_pieza = $this->Pieza_Model->save($pieza);

      // Actualizamos las categorias relacionadas
      $i=1;
      $this->db->query("DELETE FROM pub_piezas_categorias WHERE id_pieza = $id_pieza AND id_empresa = $id_empresa ");
      foreach($categorias_relacionados as $p) {
        $this->db->insert("pub_piezas_categorias",array(
          "id_pieza"=>$id_pieza,
          "id_empresa"=>$id_empresa,
          "id_relacion"=>$p->id,
          "orden"=>$i,
        ));
        $i++;
      }
    }
    $array->piezas = $piezas;
    $array->primer_pago = 0;

    // Sincroniza con los remitos para la cobranza
    if ($array->id_empresa == 70) $this->modelo->remitos($array);

    // Llamamos al cacheador
    if ($array->id_empresa == 70) {
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
    
    $this->load->model("Pieza_Model");
    $this->load->helper("file_helper");
		$this->load->helper("fecha_helper");
  	$array = $this->parse_put();
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
		$array->valida_desde = fecha_mysql($array->valida_desde);
		$array->valida_hasta = fecha_mysql($array->valida_hasta);
    $primer_pago = $array->primer_pago;
    $piezas = $array->piezas;
    $this->remove_properties($array);

    // Insertamos el publicidad
    $insert_id = $this->modelo->save($array);

    // Recorremos las piezas
    foreach($piezas as $pieza) {
      $categorias_relacionados = $pieza->categorias_relacionados;
      unset($pieza->categorias_relacionados);
      unset($pieza->campania);
      unset($pieza->categoria);
      $pieza->fecha_desde = fecha_mysql($pieza->fecha_desde);
      $pieza->fecha_hasta = fecha_mysql($pieza->fecha_hasta);
      $pieza->id_campania = $insert_id;
      $id_pieza = $this->Pieza_Model->save($pieza);

      // Actualizamos las categorias relacionadas
      $i=1;
      $this->db->query("DELETE FROM pub_piezas_categorias WHERE id_pieza = $id_pieza AND id_empresa = $id_empresa ");
      foreach($categorias_relacionados as $p) {
        $this->db->insert("pub_piezas_categorias",array(
          "id_pieza"=>$id_pieza,
          "id_empresa"=>$id_empresa,
          "id_relacion"=>$p->id,
          "orden"=>$i,
        ));
        $i++;
      }
    }
    $array->id = $insert_id;
    $array->piezas = $piezas;
    $array->primer_pago = $primer_pago;

    // Sincroniza con los remitos para la cobranza
    if ($array->id_empresa == 70) $this->modelo->remitos($array);

    // Llamamos al cacheador
    if ($array->id_empresa == 70) {
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
      $this->ver();
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
    
    $limit = $this->input->get("limit");
    if ($limit === FALSE) $limit = 0;
		$filter = $this->input->get("filter");
    if ($filter === FALSE) $filter = "";
    $id_vendedor = $this->input->get("id_vendedor");
    if ($id_vendedor === FALSE) $id_vendedor = 0;
    $offset = $this->input->get("offset");
    if ($offset === FALSE) $offset = 999999;
    $order_by = $this->input->get("order_by");
    if ($order_by === FALSE) $order_by = "nombre";
    $order = $this->input->get("order");
    if ($order === FALSE) $order = "asc";
		$id_publicidad = $this->input->get("id_publicidad");
    if ($id_publicidad === FALSE) $id_publicidad = 0;
    $estado = $this->input->get("estado");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";

    $hora_desde = ($this->input->get("hora_desde") !== FALSE) ? $this->input->get("hora_desde") : "";
    $hora_hasta = ($this->input->get("hora_hasta") !== FALSE) ? $this->input->get("hora_hasta") : "";
    $lunes = ($this->input->get("lunes") !== FALSE) ? $this->input->get("lunes") : -1;
    $martes = ($this->input->get("martes") !== FALSE) ? $this->input->get("martes") : -1;
    $miercoles = ($this->input->get("miercoles") !== FALSE) ? $this->input->get("miercoles") : -1;
    $jueves = ($this->input->get("jueves") !== FALSE) ? $this->input->get("jueves") : -1;
    $viernes = ($this->input->get("viernes") !== FALSE) ? $this->input->get("viernes") : -1;
    $sabado = ($this->input->get("sabado") !== FALSE) ? $this->input->get("sabado") : -1;
    $domingo = ($this->input->get("domingo") !== FALSE) ? $this->input->get("domingo") : -1;
    $id_categoria = ($this->input->get("id_categoria") !== FALSE) ? $this->input->get("id_categoria") : 0;
    
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "limit"=>$limit,
      "offset"=>$offset,
      "id_categoria"=>$id_categoria,
			"id_publicidad"=>$id_publicidad,
      "estado"=>$estado,
      "id_vendedor"=>$id_vendedor,
      "hora_hasta"=>$hora_hasta,
      "hora_desde"=>$hora_desde,
      "lunes"=>$lunes,
      "martes"=>$martes,
      "miercoles"=>$miercoles,
      "jueves"=>$jueves,
      "viernes"=>$viernes,
      "sabado"=>$sabado,
      "domingo"=>$domingo,
    );
    
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }
	


  function ver_piezas() {
    
    $limit = $this->input->get("limit");
    if ($limit === FALSE) $limit = 0;
    $filter = $this->input->get("filter");
    if ($filter === FALSE) $filter = "";
    $offset = $this->input->get("offset");
    if ($offset === FALSE) $offset = 999999;
    $order_by = $this->input->get("order_by");
    if ($order_by === FALSE) $order_by = "nombre";
    $order = $this->input->get("order");
    if ($order === FALSE) $order = "asc";
    $id_campania = $this->input->get("id_campania");
    if ($id_campania === FALSE) $id_campania = 0;
    $estado = $this->input->get("estado");
    if ($estado === FALSE) $estado = "";
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "limit"=>$limit,
      "offset"=>$offset,
      "id_campania"=>$id_campania,
      "estado"=>$estado,
    );
    
    $this->load->model("Pieza_Model");
    $r = $this->Pieza_Model->get_list($conf);
    if ($r === FALSE) $r = array();
    echo json_encode(array(
      "results"=>$r,
      "total"=>sizeof($r),
    ));
  }

}