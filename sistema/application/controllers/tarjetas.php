<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tarjetas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Tarjeta_Model', 'modelo');
  }

  function editar_forma_pago() {
    $id_empresa = parent::get_empresa();
    $obj = json_decode(parent::get_post("datos"));
    $this->load->helper("fecha_helper");
    $obj->fecha = fecha_mysql($obj->fecha);

    // Lo primero que hacemos es comprobar que el total sea igual a lo que esta pagando
    $total_tarjetas = 0;
    $total_interes = 0;
    foreach($obj->tarjetas as $t) {
      $total_interes += (float)$t->interes;
      $total_tarjetas += (float)$t->total; // Incluimos el interes porque en el total esta incluido
    }
    $total = $obj->efectivo - $obj->vuelto + $total_tarjetas;

    $sql = "SELECT * FROM facturas WHERE id = $obj->id AND id_punto_venta = $obj->id_punto_venta AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      echo json_encode(array("error"=>1,"mensaje"=>"ERROR: No se encuentra la venta."));
      exit();
    }
    $factura = $q->row();
    if (($factura->total - $factura->interes) != $total) {
      echo json_encode(array("error"=>1,"mensaje"=>"ERROR: El total del comprobante no se corresponde con el total de los metodos de pago."));
      exit();      
    }

    // Actualizamos la factura
    $sql = "UPDATE facturas SET total = '$total', subtotal = '$total', efectivo = '$obj->efectivo', vuelto = '$obj->vuelto', tarjeta = '$total_tarjetas', interes = '$total_interes' ";
    $sql.= "WHERE id = $obj->id AND id_punto_venta = $obj->id_punto_venta AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    // Actualizamos los cupones
    foreach($obj->tarjetas as $t) {
      if ($t->id == 0) {
        // Debemos insertarla
        $sql = "INSERT INTO cupones_tarjetas ( ";
        $sql.= " id_empresa, lote, cupon, fecha, id_factura, importe, status, id_tarjeta, cuotas, interes, total, uploaded, id_punto_venta ";
        $sql.= ") VALUES ( ";
        $sql.= " '$id_empresa', '$t->lote', '$t->cupon', '$obj->fecha', '$t->id_factura', '$t->importe', 0, '$t->id_tarjeta', '$t->cuotas', '$t->interes', '$t->total', 1, '$t->id_punto_venta' ";
        $sql.= ") ";
      } else {
        if ($t->id_tarjeta == 0) {
          // Esta marcada como que debemos eliminarla
          $sql = "DELETE FROM cupones_tarjetas ";
          $sql.= "WHERE id = '$t->id' AND id_factura = '$t->id_factura' AND id_empresa = '$id_empresa' AND id_punto_venta = '$t->id_punto_venta' ";
        } else {
          $sql = "UPDATE cupones_tarjetas SET ";
          $sql.= " lote = '$t->lote', ";
          $sql.= " cupon = '$t->cupon', ";
          $sql.= " importe = '$t->importe', ";
          $sql.= " interes = '$t->interes', ";
          $sql.= " total = '$t->total', ";
          $sql.= " id_tarjeta = '$t->id_tarjeta', ";
          $sql.= " cuotas = '$t->cuotas' ";
          $sql.= "WHERE id = '$t->id' AND id_factura = '$t->id_factura' AND id_empresa = '$id_empresa' AND id_punto_venta = '$t->id_punto_venta' ";
        }
      }
      $this->db->query($sql);
    }

    $this->load->model("Caja_Diaria_Model");
    $this->Caja_Diaria_Model->recalcular_caja(array(
      "id_empresa"=>$id_empresa,
      "id_caja_diaria"=>$obj->id_caja_diaria,
      "id_punto_venta"=>$obj->id_punto_venta,
      "fecha"=>$obj->fecha,
    ));
    echo json_encode(array("error"=>0));
  }

  function ver_cupon($id_factura,$id_punto_venta) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT CT.*, ";
    $sql.= " IF(T.nombre IS NULL,'',T.nombre) AS tarjeta ";
    $sql.= "FROM cupones_tarjetas CT ";
    $sql.= "LEFT JOIN tarjetas T ON (CT.id_tarjeta = T.id AND CT.id_empresa = T.id_empresa) ";
    $sql.= "WHERE CT.id_empresa = $id_empresa ";
    $sql.= "AND CT.id_punto_venta = $id_punto_venta ";
    $sql.= "AND CT.id_factura = $id_factura ";
    $q = $this->db->query($sql);
    $tarjetas = array();
    foreach($q->result() as $t) $tarjetas[] = $t;

    $t = new stdClass();
    $t->id = 0;
    $t->id_empresa = $id_empresa;
    $t->lote = 0;
    $t->cupon = 0;
    $t->id_factura = $id_factura;
    $t->id_punto_venta = $id_punto_venta;
    $t->importe = 0;
    $t->status = 0;
    $t->id_tarjeta = 0;
    $t->cuotas = 1;
    $t->interes = 0;
    $t->total = 0;

    if (sizeof($tarjetas) == 1) {
      // Agregamos solo una
      $tarjetas[] = $t;
    } else if (sizeof($tarjetas) == 0) {
      // Agregamos las dos
      $tarjetas[] = $t;
      $tarjetas[] = $t;
    }
    echo json_encode($tarjetas);
  }

  function export($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM tarjetas A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);

    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function export_intereses($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM tarjetas_intereses A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function listado() {
    
    $id_caja_diaria = $this->input->post("id_caja_diaria");
    if ($id_caja_diaria === FALSE) $id_caja_diaria = 0;

    $id_empresa = $this->get_empresa();
    $id_punto_venta = $this->input->post("id_punto_venta");

    $sql = "SELECT * FROM caja_diaria WHERE id = $id_caja_diaria AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta ";
    $q_caja = $this->db->query($sql);
    $caja_diaria = $q_caja->row();
    if ($caja_diaria === FALSE) { 
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No hay caja seleccionada"
      ));
      return;
    }
    
    $sql = "SELECT CT.*, ";
    $sql.= " DATE_FORMAT(CT.fecha,'%d/%m/%Y %H:%i') AS fecha, ";
    $sql.= " IF(F.comprobante IS NULL,'',F.comprobante) AS comprobante, ";
    $sql.= " IF(T.nombre IS NULL,'',T.nombre) AS tarjeta ";
    $sql.= "FROM cupones_tarjetas CT ";
    $sql.= " LEFT JOIN facturas F ON (CT.id_factura = F.id AND CT.id_empresa = F.id_empresa AND CT.id_punto_venta = F.id_punto_venta) ";
    $sql.= " LEFT JOIN tarjetas T ON (CT.id_tarjeta = T.id AND CT.id_empresa = T.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    if (!empty($caja_diaria)) {
      if ($caja_diaria->estado == "A") $sql.= "AND F.id_caja_diaria = 0 ";
      else $sql.= "AND F.id_caja_diaria = $id_caja_diaria ";
      if ($id_empresa == 249 || $id_empresa == 868) {
        $sql.= "AND DATE_FORMAT(CT.fecha,'%Y-%m-%d') = '$caja_diaria->fecha' ";
      }
    }
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND CT.status = 0 ";
    $sql.= "AND CT.id_punto_venta = $id_punto_venta ";
    $sql.= "AND CT.id_empresa = $id_empresa ";
    $sql.= "ORDER BY CT.fecha DESC ";
    $q = $this->db->query($sql);
    echo json_encode(array(
      "results"=>$q->result(),
    ));
  }
  
  function consulta() {
    $this->load->helper("fecha_helper");
    $fecha_desde = fecha_mysql($this->input->post("fecha_desde"));
    $fecha_hasta = fecha_mysql($this->input->post("fecha_hasta"));
    $sql = "SELECT ";
    $sql.= "  CT.id, CT.lote, CT.cupon, CT.importe, T.nombre AS tarjeta, ";
    $sql.= "  DATE_FORMAT(CT.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= "  DATE_FORMAT(CT.fecha,'%H:%i:%s') AS hora ";
    $sql.= "FROM cupones_tarjetas CT INNER JOIN tarjetas T ON (CT.id_tarjeta = T.id AND CT.id_empresa = T.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND CT.status = 0 ";
    if (!empty($fecha_desde)) $sql.= "AND DATE_FORMAT(fecha,'%Y-%m-%d') >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND DATE_FORMAT(fecha,'%Y-%m-%d') <= '$fecha_hasta' ";
    $sql.= "ORDER BY lote ASC, cupon ASC ";
    $q = $this->db->query($sql);
    echo json_encode($q->result());        
  }

  function calcular_intereses($id_tarjeta,$cuotas) {
    $id_empresa = $this->get_empresa();
    $interes = 1;
    $sql = "SELECT * FROM tarjetas_intereses WHERE 1=1 ";
    $sql.= "AND id_tarjeta = $id_tarjeta ";
    $sql.= "AND cuota_desde = $cuotas ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $r = $q->row();
      $dia_semana = date("w");
      if ($dia_semana == 0) $dia_semana = "domingo";
      if ($dia_semana == 1) $dia_semana = "lunes";
      if ($dia_semana == 2) $dia_semana = "martes";
      if ($dia_semana == 3) $dia_semana = "miercoles";
      if ($dia_semana == 4) $dia_semana = "jueves";
      if ($dia_semana == 5) $dia_semana = "viernes";
      if ($dia_semana == 6) $dia_semana = "sabado";
      if ($r->{"interes_".$dia_semana} != 0) {
        $interes = $r->{"interes_".$dia_semana};
      } else {
        $interes = $r->interes;
      }

            // Si hoy es alguna fecha especial
      if (date("Y-m-d") >= $r->fecha_desde && date("Y-m-d") <= $r->fecha_hasta) {
        $interes = $r->interes_especial;
      }
    }
    echo json_encode(array("interes"=>$interes));
  }
  
  function consulta_agrupada() {
    $this->load->helper("fecha_helper");
    $fecha_desde = fecha_mysql($this->input->post("fecha_desde"));
    $fecha_hasta = fecha_mysql($this->input->post("fecha_hasta"));
    $sql = "SELECT ";
    $sql.= "  SUM(CT.importe) AS importe, T.nombre AS tarjeta ";
    $sql.= "FROM cupones_tarjetas CT INNER JOIN tarjetas T ON (CT.id_tarjeta = T.id AND CT.id_empresa = T.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND CT.status = 0 ";
    if (!empty($fecha_desde)) $sql.= "AND DATE_FORMAT(fecha,'%Y-%m-%d') >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND DATE_FORMAT(fecha,'%Y-%m-%d') <= '$fecha_hasta' ";
    $sql.= "GROUP BY CT.id_tarjeta ";
    $sql.= "ORDER BY T.nombre ASC ";
    $q = $this->db->query($sql);
    echo json_encode($q->result());        
  }   

  function duplicar($id) {

    $tarjeta = $this->modelo->get($id);
    if ($tarjeta === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra tarjeta con ID: $id",
      ));
      return;
    }
    $tarjeta->id = 0;
    $tarjeta->nombre = $tarjeta->nombre." (copia) ";
    $cuotas = $tarjeta->cuotas;
    $insert_id = $this->modelo->insert($tarjeta);
    
    foreach($cuotas as $cuota) {
      $cuota->id_tarjeta = $insert_id;
      $this->db->insert("tarjetas_intereses",$cuota);
    }
    echo json_encode(array(
      "id"=>$insert_id
    ));
  }


}