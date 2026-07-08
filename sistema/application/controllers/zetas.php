<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Zetas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Zeta_Model', 'modelo');
  }

  // PASA LAS FACTURAS DE LA MUNICIPALIDAD DE JAVIER
  function pasar_facturas() {
    $id_empresa = 121;
    $desde = new DateTime();
    $desde->sub(new DateInterval("P1M"));
    $d = $desde->format("Y-m-01");
    $h = $desde->format("Y-m-t");
    $sql = "SELECT * FROM facturas WHERE id_cliente != 0 AND id_empresa = $id_empresa AND id_tipo_comprobante < 900 ";
    $sql.= "AND fecha >= '$d' AND fecha <= '$h' ";
    $sql.= "AND id_punto_venta = 1301 ";    
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      echo "Factura $r->punto_venta $r->numero <br/>";

      // Comprobamos si existe el registro en la tabla de zetas
      $sql = "SELECT * FROM zetas WHERE id_punto_venta = $r->id_punto_venta AND id_empresa = $id_empresa ";
      $sql.= "AND numero = $r->numero AND fecha = '$r->fecha' ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) continue;

      $neto_0 = 0;
      $neto_21 = 0;
      $iva_21 = 0;
      $neto_105 = 0;
      $iva_105 = 0;
      $sql = "SELECT * FROM facturas_iva WHERE id_factura = $r->id AND id_punto_venta = $r->id_punto_venta AND id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $rr) {
        if ($rr->id_alicuota_iva == 5) {
          $neto_21 = $rr->neto;
          $iva_21 = $rr->iva;
        } else if ($rr->id_alicuota_iva == 4) {
          $neto_105 = $rr->neto;
          $iva_105 = $rr->iva;  
        } else if ($rr->id_alicuota_iva == 3) {        
          $neto_0 = $rr->neto;
        }
      }

      // Insertamos el zeta del cliente
      $sql = "INSERT INTO zetas (id_empresa, id_punto_venta, punto_venta, numero, fecha, comp_desde, comp_hasta, ";
      $sql.= " neto, iva, total, id_tipo_comprobante, id_cliente, anulada, neto_105, iva_105, neto_0 ";
      $sql.= ") VALUES ( ";
      $sql.= " '$id_empresa', '$r->id_punto_venta', '$r->punto_venta', '$r->numero', '$r->fecha', '$r->numero', '$r->numero', ";
      $sql.= " '$neto_21', '$iva_21', '$r->total', '$r->id_tipo_comprobante', '$r->id_cliente', 0, '$neto_105', '$iva_105', '$neto_0' ";
      $sql.= ")";
      $this->db->query($sql);
      echo "Insertamos ZETA CLIENTE<br/>";
    }    
  }

  // PASA LOS CLIENTES A ZETAS
  function pasar_clientes() {
    $id_empresa = 121;
    $desde = new DateTime();
    $desde->sub(new DateInterval("P1M"));
    $d = $desde->format("Y-m-01");
    $h = $desde->format("Y-m-t");    
    $sql = "SELECT * FROM facturas WHERE id_cliente != 0 AND id_empresa = $id_empresa AND id_tipo_comprobante < 900 ";
    $sql.= "AND fecha >= '$d' AND fecha <= '$h' ";
    $sql.= "AND id_punto_venta != 1301 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      echo "Factura $r->punto_venta $r->numero <br/>";

      // Buscamos los IVA
      $neto_21 = 0; $iva_21 = 0; $neto_105 = 0; $iva_105 = 0; $neto_0 = 0;
      $sql = "SELECT * FROM facturas_iva WHERE id_empresa = $id_empresa AND id_punto_venta = $r->id_punto_venta AND id_factura = $r->id ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $rr) {
        if ($rr->id_alicuota_iva == 5) {
          $neto_21 = $rr->neto;
          $iva_21 = $rr->iva;
        } else if ($rr->id_alicuota_iva == 4) {
          $neto_105 = $rr->neto;
          $iva_105 = $rr->iva;
        } else if ($rr->id_alicuota_iva == 3) {
          $neto_0 = $rr->neto;
        }
      }
      $sql = "SELECT * FROM zetas WHERE id_empresa = $id_empresa AND punto_venta = $r->punto_venta AND fecha = '$r->fecha' ";
      $q_zeta = $this->db->query($sql);
      if ($q_zeta->num_rows() > 0) {
        echo "Encontro ZETA<br/>";
        $zeta = $q_zeta->row();

        $id_tipo_comprobante = 1;
        $sql = "SELECT * FROM clientes WHERE id = $r->id_cliente AND id_empresa = $id_empresa ";
        $q_cliente = $this->db->query($sql);
        if ($q_cliente->num_rows() > 0) {
          $cliente = $q_cliente->row();
          if ($cliente->id_tipo_iva != 1) $id_tipo_comprobante = 6;
        }

        // Insertamos el zeta del cliente
        $sql = "INSERT INTO zetas (id_empresa, id_punto_venta, punto_venta, numero, fecha, comp_desde, comp_hasta, ";
        $sql.= " neto, iva, total, id_tipo_comprobante, id_cliente, anulada, neto_105, iva_105, neto_0 ";
        $sql.= ") VALUES ( ";
        $sql.= " '$id_empresa', '$r->id_punto_venta', '$r->punto_venta', '$r->numero', '$r->fecha', '$r->numero', '$r->numero', ";
        $sql.= " '$neto_21', '$iva_21', '$r->total', '$id_tipo_comprobante', '$r->id_cliente', 0, '$neto_105', '$iva_105', '$neto_0' ";
        $sql.= ")";
        $this->db->query($sql);
        echo "Insertamos ZETA CLIENTE<br/>";

        // Restamos los valores del zeta original
        $sql = "UPDATE zetas SET ";
        $sql.= " neto = neto - $neto_21, ";
        $sql.= " iva = iva - $iva_21, ";
        $sql.= " neto_105 = neto_105 - $neto_105, ";
        $sql.= " iva_105 = iva_105 - $iva_105, ";
        $sql.= " neto_0 = neto_0 - $neto_0, ";
        $sql.= " total = total - $neto_21 - $iva_21 - $neto_105 - $iva_105 - $neto_0 ";
        $sql.= "WHERE id = $zeta->id AND id_punto_venta = $zeta->id_punto_venta ";
        $this->db->query($sql);
        echo "Modificamos ZETA CF <br/>";
      } else {
        echo "NO Encontro ZETA - $sql<br/>";
      }
    }
  }

  function buscar() {
    $this->load->helper("fecha_helper");
    $fecha_desde = ($this->input->get("fecha_desde") !== FALSE) ? fecha_mysql($this->input->get("fecha_desde")) : "";
    $fecha_hasta = ($this->input->get("fecha_hasta") !== FALSE) ? fecha_mysql($this->input->get("fecha_hasta")) : "";
    $id_sucursal = ($this->input->get("id_sucursal") !== FALSE) ? $this->input->get("id_sucursal") : 0;
    $id_punto_venta = parent::get_get("id_punto_venta",0);
    $id_razon_social = ($this->input->get("id_razon_social") !== FALSE) ? $this->input->get("id_razon_social") : 0;
    $limit = ($this->input->get("limit") !== FALSE) ? $this->input->get("limit") : 0;
    $offset = ($this->input->get("offset") !== FALSE) ? $this->input->get("offset") : 100;
    $array = $this->modelo->buscar(array(
      "fecha_desde"=>$fecha_desde,
      "fecha_hasta"=>$fecha_hasta,
      "id_sucursal"=>$id_sucursal,
      "id_razon_social"=>$id_razon_social,
      "id_punto_venta"=>$id_punto_venta,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    echo json_encode($array);
  }

  function exportar() {
    $this->load->helper("fecha_helper");
    $fecha_desde = ($this->input->get("fecha_desde") !== FALSE) ? fecha_mysql($this->input->get("fecha_desde")) : "";
    $fecha_hasta = ($this->input->get("fecha_hasta") !== FALSE) ? fecha_mysql($this->input->get("fecha_hasta")) : "";
    $id_sucursal = ($this->input->get("id_sucursal") !== FALSE) ? $this->input->get("id_sucursal") : 0;
    $id_punto_venta = parent::get_get("id_punto_venta",0);
    $id_razon_social = ($this->input->get("id_razon_social") !== FALSE) ? $this->input->get("id_razon_social") : 0;
    $limit = ($this->input->get("limit") !== FALSE) ? $this->input->get("limit") : 0;
    $offset = ($this->input->get("offset") !== FALSE) ? $this->input->get("offset") : 100;
    $array = $this->modelo->buscar(array(
      "fecha_desde"=>$fecha_desde,
      "fecha_hasta"=>$fecha_hasta,
      "id_sucursal"=>$id_sucursal,
      "id_razon_social"=>$id_razon_social,
      "id_punto_venta"=>$id_punto_venta,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    $resultado = array();
    foreach($array["results"] as $r) {
      $resultado[] = array(
        substr($r->fecha, 6).substr($r->fecha, 3,2).substr($r->fecha, 0,2),
        $r->id_tipo_comprobante,
        $r->punto_venta,
        $r->numero,
        $r->neto + $r->neto_0 + $r->neto_105,
        $r->iva + $r->iva_105,
        $r->total,
      );
    }
    $header = array("Fecha","Tipo Comprobante","Punto Venta","Numero","Neto","IVA","Total");
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>date("d/m/Y"),
      "filename"=>"listado_ventas",
      "header"=>$header,
      "footer"=>array(),
      "datos"=>$resultado,
      "title"=>"Exportacion",
    ));
  }

  function next($id_punto_venta = 1,$id_tipo_comprobante = 82) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
    $sql.= "FROM zetas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $sql.= "AND id_tipo_comprobante = $id_tipo_comprobante ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $salida = $q->row();  
      echo json_encode(array(
        "proximo"=>($salida->numero)+1
      ));
    } else {
      echo json_encode(array(
        "proximo"=>0
      ));      
    }
  }

	
}