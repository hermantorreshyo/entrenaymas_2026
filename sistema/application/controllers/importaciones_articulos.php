<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Importaciones_Articulos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Importacion_Articulo_Model', 'modelo',"fecha_alta DESC");
  }

  function ver($id_importacion) {

    $this->load->helper("import_helper");
    $id_empresa = parent::get_empresa();
    $nuevos = array();
    $no_modificados = array();
    $modificados = array();
    $eliminados = array();

    // Seleccionamos la importacion actual
    $sql = "SELECT * FROM importaciones_articulos WHERE id = $id_importacion AND id_empresa = $id_empresa AND eliminado = 0 ";
    $q = $this->db->query($sql);
    $importacion = $q->row();    

    // Si la importacion todavia no se proceso, comparamos con los articulos guardados
    if ($importacion->estado == 0) {

      // Seleccionamos la importacion anterior
      $sql = "SELECT * FROM importaciones_articulos ";
      $sql.= "WHERE eliminado = 0 ";
      $sql.= "AND estado = 3 "; // Estado finalizado
      $sql.= "AND id_proveedor = $importacion->id_proveedor "; // Que sea el mismo proveedor
      $sql.= "AND id != $id_importacion "; // Que no sea la propia importacion
      $sql.= "AND id_empresa = $id_empresa ORDER BY fecha_alta DESC LIMIT 0,1";
      $q_ant = $this->db->query($sql);

      $ids = array();
      $sql = "SELECT * FROM importaciones_articulos_items ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_importacion = $id_importacion ";
      $q = $this->db->query($sql);
      foreach($q->result() as $temp) {

        $ids[] = "'".$temp->codigo_prov."'";

        // Si no hay importacion anterior, es todo NUEVO
        if ($q_ant->num_rows() == 0) {
          // Seleccionamos los items, y lo pasamos al array de nuevos
          $temp->modifico_costo = 0;
          $temp->agregado = 0;
          $nuevos[] = $temp;

        } else {

          $importacion_anterior = $q_ant->row();

          // Buscamos el item de la importacion anterior, y comparamos a ver que es lo que cambio
          $sql = "SELECT * FROM importaciones_articulos_items ";
          $sql.= "WHERE id_empresa = $id_empresa ";
          $sql.= "AND id_importacion = $importacion_anterior->id ";
          $sql.= "AND codigo_prov = '$temp->codigo_prov' ";
          $qq = $this->db->query($sql);
          // Si la importacion anterior no se encuentra el item, es NUEVO
          if ($qq->num_rows() == 0) {
            $temp->modifico_costo = 0;
            $temp->agregado = 0;
            $nuevos[] = $temp;
          } else {
            $art = $qq->row();
            $temp->codigo = $art->codigo;
            $temp->agregado = 0;
            $temp->modif_costo_1 = $art->modif_costo_1;
            $temp->modif_costo_2 = $art->modif_costo_2;
            $temp->modif_costo_3 = $art->modif_costo_3;
            $temp->modif_costo_4 = $art->modif_costo_4;
            $temp->modif_costo_5 = $art->modif_costo_5;
            $temp->modif_precio_1 = $art->modif_precio_1;
            $temp->modif_precio_2 = $art->modif_precio_2;
            $temp->modif_precio_3 = $art->modif_precio_3;
            $temp->modif_precio_4 = $art->modif_precio_4;
            $temp->modif_precio_5 = $art->modif_precio_5;
            $temp->porc_iva = $art->porc_iva;
            $temp->coeficiente = $art->coeficiente;
            $temp->cantidad = $art->cantidad;
            $temp->estado = $art->estado;
            $temp->modifico_costo = ($temp->costo_neto_inicial > $art->costo_neto_inicial)?1:(($temp->costo_neto_inicial < $art->costo_neto_inicial)?-1:0);
            if ($temp->modifico_costo == 0) {
              $temp->tipo_modif = "I";
              $no_modificados[] = $temp;
            } else {
              $temp->tipo_modif = "M";
              $temp->costo_anterior = $art->costo_neto_inicial;
              $modificados[] = $temp;  
            }
          }
        }        
      }

      // Al final buscamos en la importacion anterior aquellos que no esten en la importacion nueva
      if ($importacion_anterior != FALSE) {
        $ids_s = implode(",", $ids);
        $sql = "SELECT * FROM importaciones_articulos_items ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_importacion = $importacion_anterior->id ";
        $sql.= "AND codigo_prov NOT IN ($ids_s) ";
        $q_elim = $this->db->query($sql);
        foreach($q_elim->result() as $rr) {
          $sql = create_insert_sql(array(
            "table"=>"importaciones_articulos_items",
            "fields"=>array(
              "id_empresa",
              "id_articulo",
              "id_importacion",
              "codigo",
              "codigo_prov",
              "nombre",
              "costo_neto_inicial",
              "costo_neto",
              "porc_iva",
              "modif_costo_1",
              "modif_costo_2",
              "modif_costo_3",
              "modif_costo_4",
              "modif_costo_5",
              "costo_final",
              "modif_precio_1",
              "modif_precio_2",
              "modif_precio_3",
              "modif_precio_4",
              "modif_precio_5",
              "precio_final",
              "estado",
              "tipo_modif",
              "coeficiente",
              "modifico_costo",
              "cantidad"
            ),
            "data"=>array(
              $rr->id_empresa,
              $rr->id_articulo,
              $id_importacion,
              $rr->codigo,
              $rr->codigo_prov,
              $rr->nombre,
              $rr->costo_neto_inicial,
              $rr->costo_neto,
              $rr->porc_iva,
              $rr->modif_costo_1,
              $rr->modif_costo_2,
              $rr->modif_costo_3,
              $rr->modif_costo_4,
              $rr->modif_costo_5,
              $rr->costo_final,
              $rr->modif_precio_1,
              $rr->modif_precio_2,
              $rr->modif_precio_3,
              $rr->modif_precio_4,
              $rr->modif_precio_5,
              $rr->precio_final,
              $rr->estado,
              "X",
              $rr->coeficiente,
              0,
              1
            )
          ));
          $this->db->query($sql);
          $rr->id = $this->db->insert_id();
          $rr->agregado = 0;
          $eliminados[] = $rr;
        }
      }

    // Si la importacion ya se proceso, levantamos los estados directamente de la base de datos
    } else if ($importacion->estado >= 1) {

      // Articulos nuevos
      $sql = "SELECT *, 0 AS agregado FROM importaciones_articulos_items WHERE id_empresa = $id_empresa AND id_importacion = $id_importacion AND tipo_modif = 'N' ";
      $q = $this->db->query($sql);
      $nuevos = $q->result();

      // Articulos modificados
      $sql = "SELECT *, 0 AS agregado FROM importaciones_articulos_items WHERE id_empresa = $id_empresa AND id_importacion = $id_importacion AND tipo_modif = 'M' ";
      $q = $this->db->query($sql);
      $modificados = $q->result();

      // Articulos no modificados
      $sql = "SELECT *, 0 AS agregado FROM importaciones_articulos_items WHERE id_empresa = $id_empresa AND id_importacion = $id_importacion AND tipo_modif = 'I' ";
      $q = $this->db->query($sql);
      $no_modificados = $q->result();

      // Articulos eliminados
      $sql = "SELECT *, 0 AS agregado FROM importaciones_articulos_items WHERE id_empresa = $id_empresa AND id_importacion = $id_importacion AND tipo_modif = 'X' ";
      $q = $this->db->query($sql);
      $eliminados = $q->result();
    }

    echo json_encode(array(
      "id_proveedor"=>$importacion->id_proveedor,
      "id_importacion"=>$id_importacion,
      "observaciones"=>$importacion->observaciones,
      "estado"=>$importacion->estado,
      "nuevos"=>$nuevos,
      "modificados"=>$modificados,
      "no_modificados"=>$no_modificados,
      "eliminados"=>$eliminados,
    ));
  }

  function marcar_cargado($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("UPDATE importaciones_articulos SET estado = 3 WHERE id = $id AND id_empresa = $id_empresa ");

    $importacion = $this->modelo->get($id);

    // Registramos el LOG
    $this->load->model("Log_Model");
    $this->Log_Model->log("Finalizo ".$importacion->proveedor->nombre." (ID: $importacion->id)",$importacion->id);

    echo json_encode(array());
  }

  function exportar_excel($id) {

    $id_empresa = parent::get_empresa();
    $importacion = $this->modelo->get($id);
    $this->load->model("Configuracion_Model");
    $cotizacion = $this->Configuracion_Model->get_cotizacion();
    $datos = array();
    foreach($importacion->items as $item) {
      if ($item->tipo_modif == 'X') continue;
      if (empty($item->codigo_item)) continue;
      $item->costo_final_dolares = ($cotizacion > 0) ? round($item->costo_final / $cotizacion,2) : 0;
      $datos[] = array(
        "codigo_item"=>$item->codigo_item,
        "nombre"=>$item->nombre,
        "costo_final"=>$item->costo_final,
        "costo_final_dolares"=>$item->costo_final_dolares,
        "coeficiente"=>$item->coeficiente,
        "precio_final"=>(($item->estado == 1) ? $item->precio_final : "NO"),
      );
    }

    // Cambiamos el estado
    $this->db->query("UPDATE importaciones_articulos SET estado = 2 WHERE id = $id AND id_empresa = $id_empresa ");

    // Registramos el LOG
    $this->load->model("Log_Model");
    $this->Log_Model->log("Exporto ".$importacion->proveedor->nombre." (ID: $importacion->id)",$importacion->id);

    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>"",
      "filename"=>$importacion->proveedor->nombre." | ".date("Y-m-d"),
      "footer"=>array(),
      "header"=>array("Interno","Nombre","Lista","Lista Dolar","Coeficiente","Venta"),
      "data"=>$datos,
      "title"=>"",
    ));
  }

  function guardar() {
    $id_empresa = parent::get_empresa();
    $id_proveedor = parent::get_post("id_proveedor",0);
    $id_importacion = parent::get_post("id_importacion",0);
    $observaciones = parent::get_post("observaciones","");
    $nuevos = json_decode(parent::get_post("nuevos",array()));
    $modificaciones = json_decode(parent::get_post("modificaciones",array()));
    $no_modificados = json_decode(parent::get_post("no_modificados",array()));
    $eliminados = json_decode(parent::get_post("eliminados",array()));
    $insertados = json_decode(parent::get_post("insertados",array()));

    // Le cambiamos el estado a la importacion, para indicar que ya se proceso
    $sql = "UPDATE importaciones_articulos SET ";
    $sql.= "estado = 1, "; // ESTADO 1 = MODIFICADO
    $sql.= "observaciones = '$observaciones' ";
    $sql.= "WHERE id = $id_importacion AND id_empresa = $id_empresa";
    $this->db->query($sql);

    $importacion = $this->modelo->get($id_importacion);

    // Registramos el LOG
    $this->load->model("Log_Model");
    $this->Log_Model->log("Modifico ".$importacion->proveedor->nombre." (ID: $importacion->id)",$importacion->id);

    foreach($insertados as $r) {
      // Son los que agregan nuevos
      $sql = "INSERT INTO importaciones_articulos_items (";
      $sql.= " id_importacion, id_empresa, tipo_modif, fecha_modif, estado, codigo, codigo_prov, costo_neto_inicial, costo_neto_inicial_dolar, ";
      $sql.= " modif_costo_1, modif_costo_2, modif_costo_3, modif_costo_4, modif_costo_5, nombre, bulto, ";
      $sql.= " porc_iva, precio_neto, fue_modificado, costo_neto, coeficiente, cantidad, costo_final, precio_final ";
      $sql.= ") VALUES (";
      $sql.= " $id_importacion, $id_empresa, 'N', NOW(), '$r->estado', '$r->codigo', '$r->codigo_prov', $r->costo_neto_inicial, $r->costo_neto_inicial_dolar, ";
      $sql.= " $r->modif_costo_1, $r->modif_costo_2, $r->modif_costo_3, $r->modif_costo_4, $r->modif_costo_5, '$r->nombre', '$r->bulto', ";
      $sql.= " $r->porc_iva, $r->precio_neto, $r->fue_modificado, $r->costo_neto, $r->coeficiente, $r->cantidad, $r->costo_final, $r->precio_final ";
      $sql.= ") ";
      $this->db->query($sql);
    }
    foreach($nuevos as $r) {
      $sql = "UPDATE importaciones_articulos_items SET ";
      $sql.= " tipo_modif = 'N', ";
      $sql.= " fecha_modif = NOW(), ";
      $sql.= " estado = '$r->estado', ";
      $sql.= " codigo = '$r->codigo', ";
      $sql.= " costo_neto_inicial = $r->costo_neto_inicial, ";
      $sql.= " costo_neto_inicial_dolar = $r->costo_neto_inicial_dolar, ";
      $sql.= " modif_costo_1 = $r->modif_costo_1, ";
      $sql.= " modif_costo_2 = $r->modif_costo_2, ";
      $sql.= " modif_costo_3 = $r->modif_costo_3, ";
      $sql.= " modif_costo_4 = $r->modif_costo_4, ";
      $sql.= " modif_costo_5 = $r->modif_costo_5, ";
      $sql.= " porc_iva = $r->porc_iva, ";
      $sql.= " precio_neto = $r->precio_neto, ";
      $sql.= " fue_modificado = $r->fue_modificado, ";
      $sql.= " costo_neto = $r->costo_neto, ";
      if (isset($r->costo_anterior)) $sql.= " costo_anterior = $r->costo_anterior, ";
      $sql.= " coeficiente = $r->coeficiente, ";
      $sql.= " cantidad = $r->cantidad, ";
      $sql.= " costo_final = $r->costo_final, ";
      $sql.= " precio_final = $r->precio_final ";
      $sql.= "WHERE id = $r->id ";
      $sql.= "AND id_importacion = $id_importacion ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);
    }
    foreach($modificaciones as $r) {
      $sql = "UPDATE importaciones_articulos_items SET ";
      $sql.= " tipo_modif = 'M', ";
      $sql.= " fecha_modif = NOW(), ";
      $sql.= " estado = '$r->estado', ";
      $sql.= " codigo = '$r->codigo', ";
      $sql.= " costo_neto_inicial = $r->costo_neto_inicial, ";
      $sql.= " costo_neto_inicial_dolar = $r->costo_neto_inicial_dolar, ";
      $sql.= " modif_costo_1 = $r->modif_costo_1, ";
      $sql.= " modif_costo_2 = $r->modif_costo_2, ";
      $sql.= " modif_costo_3 = $r->modif_costo_3, ";
      $sql.= " modif_costo_4 = $r->modif_costo_4, ";
      $sql.= " modif_costo_5 = $r->modif_costo_5, ";
      $sql.= " porc_iva = $r->porc_iva, ";
      $sql.= " precio_neto = $r->precio_neto, ";
      $sql.= " fue_modificado = $r->fue_modificado, ";
      $sql.= " costo_neto = $r->costo_neto, ";
      $sql.= " coeficiente = $r->coeficiente, ";
      $sql.= " cantidad = $r->cantidad, ";
      $sql.= " costo_final = $r->costo_final, ";
      if (isset($r->costo_anterior)) $sql.= " costo_anterior = $r->costo_anterior, ";
      $sql.= " precio_final = $r->precio_final ";
      $sql.= "WHERE id = $r->id ";
      $sql.= "AND id_importacion = $id_importacion ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);
    }
    foreach($no_modificados as $r) {
      $sql = "UPDATE importaciones_articulos_items SET ";
      $sql.= " tipo_modif = 'I', ";
      $sql.= " fecha_modif = NOW(), ";
      $sql.= " estado = '$r->estado', ";
      $sql.= " codigo = '$r->codigo', ";
      $sql.= " costo_neto_inicial = $r->costo_neto_inicial, ";
      $sql.= " costo_neto_inicial_dolar = $r->costo_neto_inicial_dolar, ";
      $sql.= " modif_costo_1 = $r->modif_costo_1, ";
      $sql.= " modif_costo_2 = $r->modif_costo_2, ";
      $sql.= " modif_costo_3 = $r->modif_costo_3, ";
      $sql.= " modif_costo_4 = $r->modif_costo_4, ";
      $sql.= " modif_costo_5 = $r->modif_costo_5, ";
      $sql.= " porc_iva = $r->porc_iva, ";
      $sql.= " precio_neto = $r->precio_neto, ";
      $sql.= " fue_modificado = $r->fue_modificado, ";
      $sql.= " costo_neto = $r->costo_neto, ";
      $sql.= " coeficiente = $r->coeficiente, ";
      $sql.= " cantidad = $r->cantidad, ";
      $sql.= " costo_final = $r->costo_final, ";
      if (isset($r->costo_anterior)) $sql.= " costo_anterior = $r->costo_anterior, ";
      $sql.= " precio_final = $r->precio_final ";
      $sql.= "WHERE id = $r->id ";
      $sql.= "AND id_importacion = $id_importacion ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);
    }
    foreach($eliminados as $r) {
      $sql = "UPDATE importaciones_articulos_items SET ";
      $sql.= " tipo_modif = 'X', ";
      $sql.= " fecha_modif = NOW(), ";
      $sql.= " estado = '$r->estado', ";
      $sql.= " codigo = '$r->codigo', ";
      $sql.= " costo_neto_inicial = $r->costo_neto_inicial, ";
      $sql.= " costo_neto_inicial_dolar = $r->costo_neto_inicial_dolar, ";
      $sql.= " modif_costo_1 = $r->modif_costo_1, ";
      $sql.= " modif_costo_2 = $r->modif_costo_2, ";
      $sql.= " modif_costo_3 = $r->modif_costo_3, ";
      $sql.= " modif_costo_4 = $r->modif_costo_4, ";
      $sql.= " modif_costo_5 = $r->modif_costo_5, ";
      $sql.= " porc_iva = $r->porc_iva, ";
      $sql.= " precio_neto = $r->precio_neto, ";
      $sql.= " fue_modificado = $r->fue_modificado, ";
      $sql.= " costo_neto = $r->costo_neto, ";
      $sql.= " coeficiente = $r->coeficiente, ";
      $sql.= " cantidad = $r->cantidad, ";
      $sql.= " costo_final = $r->costo_final, ";
      if (isset($r->costo_anterior)) $sql.= " costo_anterior = $r->costo_anterior, ";
      $sql.= " precio_final = $r->precio_final ";
      $sql.= "WHERE id = $r->id ";
      $sql.= "AND id_importacion = $id_importacion ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  }

  function consulta() {
    
    $id_empresa = ($this->input->get("e") !== FALSE) ? $this->input->get("e") : parent::get_empresa();
    $desde = $this->input->get("desde");
    $hasta = $this->input->get("hasta");
    $id_cliente = $this->input->get("id_cliente");
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
        
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");        
    $this->load->helper("fecha_helper");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    if (!empty($hasta)) $hasta = fecha_mysql($hasta);
    
    $lista = $this->modelo->get_all(array(
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "id_cliente"=>$id_cliente,
      "id_usuario"=>$id_usuario,
      "id_empresa"=>$id_empresa,
    ));
                
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $salida = array(
      "total"=> $total->total,
      "results"=>$lista,
    );
    echo json_encode($salida);
  }

  function insert() {
    
    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();

    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");

    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $fecha_alta = $array->fecha_alta;
    if (isset($array->fecha_alta)) $array->fecha_alta = fecha_mysql($array->fecha_alta);
    else $array->fecha_alta = date("Y-m-d H:i:s");

    if (isset($array->fecha_modif)) $array->fecha_modif = fecha_mysql($array->fecha_modif);
    else $array->fecha_modif = date("Y-m-d H:i:s");

    $items = $array->items;
    $id_importacion = $this->modelo->insert($array);

    $i=0;
    foreach($items as $l) {
      $this->db->insert("importaciones_articulos_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_importacion"=>$id_importacion,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total_con_iva"=>$l->total_con_iva,
        "bonificacion"=>$l->bonificacion,
        "orden"=>$i,
      ));
      $i++;
    }

    echo json_encode(array(
    "id"=>$id_importacion,
    "error"=>0,
    ));
  }
  
  
  function update($id_importacion) {
    
    // Si es 0, entonces lo insertamos
    if ($id_importacion == 0) { $this->insert($id_importacion); return; }    
    
    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();
    
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");

    $anterior = $this->modelo->get($id_importacion);
    
    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    
    if (isset($array->fecha_modif)) $array->fecha_modif = fecha_mysql($array->fecha_modif);
    else $array->fecha_modif = date("Y-m-d H:i:s");
    
    $items = $array->items;
    $this->modelo->update($id_importacion,$array);

    $i=0;
    $this->db->query("DELETE FROM importaciones_articulos_items WHERE id_importacion = $id_importacion AND id_empresa = $id_empresa");
    foreach($items as $l) {
      $this->db->insert("importaciones_articulos_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_importacion"=>$id_importacion,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total_con_iva"=>$l->total_con_iva,
        "orden"=>$i,
      ));
      $i++;
    }

    echo json_encode(array(
      "id"=>$id_importacion,
      "error"=>0,
    ));
  }
  
  function show_error($mensaje = "Ocurrio un error al guardar el comprobante") {
    echo json_encode(array(
      "error"=>1,
      "mensaje"=>$mensaje,
      "imprimir"=>0,
    ));
    exit();    
  }  

  function delete($id = null) {
    $id_empresa = parent::get_empresa();
    $importacion = $this->modelo->get($id);
    $this->db->query("UPDATE importaciones_articulos_items SET eliminado = 1 WHERE id_importacion = $id AND id_empresa = $id_empresa");
    $this->db->query("UPDATE importaciones_articulos SET eliminado = 1 WHERE id = $id AND id_empresa = $id_empresa");
    // Registramos el LOG
    $this->load->model("Log_Model");
    $this->Log_Model->log("Elimino ".$importacion->proveedor->nombre." (ID: $importacion->id)",$importacion->id);
    echo json_encode(array());
  }

  function verlog($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT L.*, U.nombre AS usuario, ";
    $sql.= " DATE_FORMAT(L.fecha,'%d/%m/%Y %H:%i:%s') AS fecha ";
    $sql.= "FROM com_log L INNER JOIN com_usuarios U ON (L.id_empresa = U.id_empresa AND L.id_usuario = U.id) ";
    $sql.= "WHERE L.link = '$id' AND L.id_empresa = $id_empresa ORDER BY L.fecha DESC";
    $res = $this->db->query($sql);
    echo json_encode(array(
      "resultado"=>$res->result()
    ));
  }

}