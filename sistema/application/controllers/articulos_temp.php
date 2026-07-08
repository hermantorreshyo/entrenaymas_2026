<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Articulos_Temp extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function confirmar() {
    $id_empresa = parent::get_empresa();
    $id_proveedor = parent::get_post("id_proveedor",0);
    $id_importacion = parent::get_post("id_importacion",0);
    $this->load->model("Articulo_Model");
    $sql = "SELECT * FROM articulos_temp ";    
    $sql.= "WHERE seleccionado = 1 AND id_importacion = $id_importacion AND id_proveedor = $id_proveedor AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "DELETE FROM articulos_proveedores WHERE id_empresa = $id_empresa AND id_articulo = $row->id AND id_proveedor = $id_proveedor ";
      if ($row->tipo_modif == "N") {
        $id = $this->Articulo_Model->insert($row);
      } else if ($row->tipo_modif == "M") {
        $this->Articulo_Model->update($row->id,$row);
        $id = $row->id;
      }
      $sql = "INSERT INTO articulos_proveedores (id_empresa,id_articulo,codigo,id_proveedor,orden) VALUES ($id_empresa,$id,'$row->custom_10',$id_proveedor,0) ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  }

  function guardar() {
    $id_empresa = parent::get_empresa();
    $id_proveedor = parent::get_post("id_proveedor",0);
    $id_importacion = parent::get_post("id_importacion",0);
    $nuevos = json_decode(parent::get_post("nuevos",array()));
    $modificaciones = json_decode(parent::get_post("modificaciones",array()));    

    // Limpiamos todos los seleccionados
    $sql = "UPDATE articulos_temp SET seleccionado = 0 WHERE id_importacion = $id_importacion AND id_proveedor = $id_proveedor AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    foreach($nuevos as $r) {
      $sql = "UPDATE articulos_temp SET ";
      $sql.= " tipo_modif = 'N', ";
      $sql.= " seleccionado = 1, ";
      $sql.= " costo_neto_inicial = $r->costo_neto_inicial, ";
      $sql.= " modif_1 = $r->modif_1, ";
      $sql.= " modif_2 = $r->modif_2, ";
      $sql.= " modif_3 = $r->modif_3, ";
      $sql.= " modif_4 = $r->modif_4, ";
      $sql.= " modif_5 = $r->modif_5, ";
      $sql.= " id_tipo_alicuota_iva = $r->id_tipo_alicuota_iva, ";
      $sql.= " costo_neto = $r->costo_neto, ";
      $sql.= " porc_ganancia = $r->porc_ganancia, ";
      $sql.= " costo_final = $r->costo_final, ";
      $sql.= " precio_final = $r->precio_final ";
      $sql.= "WHERE id = $r->id ";
      $sql.= "AND id_importacion = $id_importacion ";
      $sql.= "AND id_proveedor = $id_proveedor ";
      $this->db->query($sql);
    }
    foreach($modificaciones as $r) {
      $sql = "UPDATE articulos_temp SET ";
      $sql.= " tipo_modif = 'M', ";
      $sql.= " seleccionado = 1, ";
      $sql.= " costo_neto_inicial = $r->costo_neto_inicial, ";
      $sql.= " modif_1 = $r->modif_1, ";
      $sql.= " modif_2 = $r->modif_2, ";
      $sql.= " modif_3 = $r->modif_3, ";
      $sql.= " modif_4 = $r->modif_4, ";
      $sql.= " modif_5 = $r->modif_5, ";
      $sql.= " id_tipo_alicuota_iva = $r->id_tipo_alicuota_iva, ";
      $sql.= " costo_neto = $r->costo_neto, ";
      $sql.= " porc_ganancia = $r->porc_ganancia, ";
      $sql.= " costo_final = $r->costo_final, ";
      $sql.= " precio_final = $r->precio_final ";
      $sql.= "WHERE id = $r->id ";
      $sql.= "AND id_importacion = $id_importacion ";
      $sql.= "AND id_proveedor = $id_proveedor ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  }

  function ver() {
    $id_empresa = parent::get_empresa();
    $id_proveedor = parent::get_get("id_proveedor",0);
    $id_importacion = parent::get_get("id_importacion",0);

    $nuevos = array();
    $no_modificados = array();
    $modificados = array();
    $eliminados = array();

    $sql = "SELECT * FROM articulos_temp ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_proveedor = $id_proveedor ";
    $sql.= "AND id_importacion = $id_importacion ";
    $q = $this->db->query($sql);
    foreach($q->result() as $temp) {
      $sql = "SELECT A.* FROM articulos A INNER JOIN articulos_proveedores AP ON (A.id = AP.id_articulo AND A.id_empresa = AP.id_empresa) ";
      $sql.= "WHERE A.id_empresa = '$id_empresa' ";
      $sql.= "AND AP.codigo = '$temp->custom_10' ";
      $sql.= "AND AP.id_proveedor = '$id_proveedor' ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() == 0) {
        $temp->modifico = 0;
        $nuevos[] = $temp;
      } else {
        $art = $qq->row();
        $temp->modifico = ($temp->costo_neto_inicial > $art->costo_neto_inicial)?1:(($temp->costo_neto_inicial < $art->costo_neto_inicial)?-1:0);
        if ($temp->modifico == 0) {
          $no_modificados[] = $temp;
        } else {
          $modificados[] = $temp;  
        }
      }
    }
    echo json_encode(array(
      "id_proveedor"=>$id_proveedor,
      "id_importacion"=>$id_importacion,
      "nuevos"=>$nuevos,
      "modificados"=>$modificados,
      "no_modificados"=>$no_modificados,
      "eliminados"=>$eliminados,
    ));
  }
	
}