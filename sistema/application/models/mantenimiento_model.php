<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Mantenimiento_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("mant_mantenimientos","id","fecha DESC",1);
  }

  function next() {
    $id_empresa = parent::get_empresa();
    $q = $this->db->query("SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero FROM mant_mantenimientos WHERE id_empresa = $id_empresa");
    $r = $q->row();
    $qq = $this->db->query("SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero FROM mant_ordenes_trabajo WHERE id_empresa = $id_empresa");
    $rr = $qq->row();
    $salida = new stdClass();
    $salida->numero = ((int)$r->numero + 1);
    $salida->numero_orden_trabajo = ((int)$rr->numero + 1);
    return $salida;
  }

  function next_orden_trabajo() {
    $id_empresa = parent::get_empresa();
    return ((int)$r->numero + 1);
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);

    $es_nueva = ($data->id == 0);
    $ordenes_trabajo = $data->ordenes_trabajo;
    unset($data->ordenes_trabajo);

    // Ponemos los stamps desde y hasta
    $data->desde = $data->fecha." ".$data->hora;
    $dt = new DateTime($data->desde);
    $dt->add(new DateInterval("PT".round($data->duracion_aprox_cantidad,0).$data->duracion_aprox_tipo));
    $data->hasta = $dt->format("Y-m-d H:i");

    // Controlamos si el turno esta libre
    $libre = $this->is_free(array(
      "fecha"=>$data->fecha,
      "hora"=>$data->hora,
      "not_id"=>((isset($data->id) && !empty($data->id)) ? $data->id : 0),
    ));
    if (!$libre) $this->send_error("ERROR: Existe otro mantenimiento en ese horario.");

    $id_mantenimiento = parent::save($data);

    // Recorremos las ordenes de trabajo
    $i_orden = 0;
    foreach($ordenes_trabajo as $orden) {

      $tareas = $orden->tareas;
      $orden->id_mantenimiento = $id_mantenimiento;
      $orden->id_empresa = $data->id_empresa;
      $orden->orden = $i_orden;
      $orden_eliminado = (isset($orden->eliminado)) ? $orden->eliminado : 0;
      parent::limpiar_campos($orden,"mant_ordenes_trabajo");

      if ($es_nueva) {
        if ($orden_eliminado == 0) { 
          $this->db->insert("mant_ordenes_trabajo",$orden);
          $id_orden = $this->db->insert_id();
        }
      } else {
        if ($orden_eliminado == 1) {
          $this->db->query("DELETE FROM mant_ordenes_trabajo WHERE id_empresa = $data->id_empresa AND id = $orden->id ");
          $this->db->query("DELETE FROM mant_tareas WHERE id_empresa = $data->id_empresa AND id_orden_trabajo = $orden->id ");
          continue;
        } else {
          $q = $this->db->query("SELECT * FROM mant_ordenes_trabajo WHERE id_empresa = $data->id_empresa AND id = $orden->id ");
          if ($q->num_rows()>0) {
            $this->db->where("id_empresa",$data->id_empresa);
            $this->db->where("id",$orden->id);
            $this->db->update("mant_ordenes_trabajo",$orden);
            $id_orden = $orden->id;
          } else {
            $this->db->insert("mant_ordenes_trabajo",$orden);
            $id_orden = $this->db->insert_id();
          }
        }
      }

      $i_tarea = 0;
      foreach($tareas as $tarea) {
        
        $tarea->id_empresa = $data->id_empresa;
        $tarea->id_orden_trabajo = $id_orden;
        $tarea->orden = $i_tarea;
        $articulos = (isset($tarea->articulos) ? $tarea->articulos : array());
        $tarea_eliminado = (isset($tarea->eliminado)) ? $tarea->eliminado : 0;
        $id_tarea = 0;
        parent::limpiar_campos($tarea,"mant_tareas");

        if ($es_nueva) {
          if ($tarea_eliminado == 0) { 
            $this->db->insert("mant_tareas",$tarea);
            $id_tarea = $this->db->insert_id();
          }
        } else {
          if ($tarea_eliminado == 1) {
            $this->db->query("DELETE FROM mant_tareas WHERE id_empresa = $data->id_empresa AND id = $tarea->id AND id_orden_trabajo = $id_orden");
            continue;
          } else {
            $q = $this->db->query("SELECT * FROM mant_tareas WHERE id_empresa = $data->id_empresa AND id_orden_trabajo = $id_orden AND id = $tarea->id ");
            if ($q->num_rows()>0) {
              $this->db->where("id_empresa",$data->id_empresa);
              $this->db->where("id",$tarea->id);
              $this->db->update("mant_tareas",$tarea);
              $id_tarea = $tarea->id;
            } else {
              $this->db->insert("mant_tareas",$tarea);
              $id_tarea = $this->db->insert_id();
            }
          }
        }
        $i_tarea++;
      }

      if ($id_tarea != 0) {
        $this->db->query("DELETE FROM mant_tareas_articulos WHERE id_empresa = $data->id_empresa AND id_tarea = $id_tarea ");
        $i_art = 0;
        foreach($articulos as $art) {
          $sql = "INSERT INTO mant_tareas_articulos (id_tarea,id_empresa,id_articulo,cantidad,orden,descripcion) VALUES(";
          $sql.= "'$id_tarea','$data->id_empresa','$art->id_articulo','$art->cantidad','$i_art','$art->descripcion')";
          $this->db->query($sql);
          $i_art++;
        }
      }

      $i_orden++;
    }

    return $id_mantenimiento;
  }

  function is_free($config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $hora = isset($config["hora"]) ? $config["hora"] : "";
    $sql = "SELECT * FROM mant_mantenimientos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($not_id)) $sql.= "AND id != $not_id ";
    if (!empty($fecha) && !empty($hora)) {
      $fechahora = $fecha." ".$hora;
      $sql.= "AND desde <= '$fechahora' AND '$fechahora' < hasta ";
    }
    $q = $this->db->query($sql);
    return ($q->num_rows()>0)?FALSE:TRUE;
  }

  function get($id, $id_empresa = 0) {
    $id_empresa = ($id_empresa == 0) ? parent::get_empresa() : $id_empresa;
    $sql = "SELECT T.*, ";
    $sql.= " IF(TM.nombre IS NULL,'',TM.nombre) AS tipo_mantenimiento ";
    $sql.= "FROM mant_mantenimientos T ";
    $sql.= " INNER JOIN mant_tipos_mantenimiento TM ON (T.id_tipo_mantenimiento = TM.id AND T.id_empresa = TM.id_empresa) ";
    $sql.= "WHERE T.id = $id AND T.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $row = $q->row();
    if ($row === FALSE) return $row;

    $row->ordenes_trabajo = array();
    $sql = "SELECT OT.*, ";
    $sql.= " IF(TOT.nombre IS NULL,'',TOT.nombre) AS tipo_orden_trabajo ";
    $sql.= "FROM mant_ordenes_trabajo OT ";
    $sql.= " INNER JOIN mant_tipos_ordenes_trabajo TOT ON (OT.id_tipo_orden_trabajo = TOT.id AND OT.id_empresa = TOT.id_empresa) ";
    $sql.= "WHERE OT.id_mantenimiento = $row->id AND OT.id_empresa = $id_empresa ";
    $sql.= "ORDER BY OT.orden ASC ";
    $qq = $this->db->query($sql);
    foreach($qq->result() as $ot) {
      
      $ot->tareas = array();
      $sql = "SELECT T.*, ";
      $sql.= " IF(TT.nombre IS NULL,'',TT.nombre) AS tipo_tarea, ";
      $sql.= " IF(M.nombre IS NULL,'',M.nombre) AS maquina, ";
      $sql.= " IF(P.nombre IS NULL,'Completa',P.nombre) AS parte ";
      $sql.= "FROM mant_tareas T ";
      $sql.= " INNER JOIN mant_tipos_tareas TT ON (T.id_tipo_tarea = TT.id AND T.id_empresa = TT.id_empresa) ";
      $sql.= " LEFT JOIN mant_maquinas M ON (T.id_maquina = M.id AND T.id_empresa = M.id_empresa) ";
      $sql.= " LEFT JOIN mant_partes P ON (T.id_parte = P.id AND T.id_empresa = P.id_empresa) ";
      $sql.= "WHERE T.id_empresa = $id_empresa AND T.id_orden_trabajo = $ot->id ";
      $sql.= "ORDER BY T.orden ASC";
      $qqq = $this->db->query($sql);
      foreach($qqq->result() as $tarea) {

        $tarea->articulos = array();
        $sql = "SELECT TA.*, ";
        $sql.= " IF(A.nombre IS NULL,'',A.nombre) AS articulo ";
        $sql.= "FROM mant_tareas_articulos TA ";
        $sql.= " INNER JOIN articulos A ON (TA.id_articulo = A.id AND TA.id_empresa = A.id_empresa) ";
        $sql.= "WHERE TA.id_empresa = $id_empresa AND TA.id_tarea = $tarea->id ";
        $q4 = $this->db->query($sql);
        foreach($q4->result() as $art) {
          $tarea->articulos[] = $art;
        }

        $ot->tareas[] = $tarea;
      }

      $row->ordenes_trabajo[] = $ot;
    }

    return $row;
  }
  
  function calendario($conf = array()) {
    $id_empresa = isset($conf["id_empresa"])?$conf["id_empresa"]:parent::get_empresa();
    $fecha_desde = isset($conf["desde"])?$conf["desde"]:"";
    $fecha_hasta = isset($conf["hasta"])?$conf["hasta"]:"";
    $sql = "SELECT C.*, ";
    $sql.= " IF(C.realizado=0,'#28b492','#a94442') AS backgroundColor, ";
    $sql.= " IF(C.realizado=0,'#28b492','#a94442') AS borderColor, ";
    $sql.= " C.numero AS title, ";
    $sql.= " CONCAT(C.fecha,' ',C.hora) AS start, ";
    $sql.= " (IF(C.duracion_aprox_tipo = 'H',DATE_ADD((CONCAT(C.fecha,' ',C.hora)),INTERVAL C.duracion_aprox_cantidad*60 MINUTE), DATE_ADD((CONCAT(C.fecha,' ',C.hora)),INTERVAL C.duracion_aprox_cantidad MINUTE))) AS end ";
    $sql.= "FROM mant_mantenimientos C ";
    $sql.= "WHERE 1=1 ";
    if (!empty($id_empresa)) $sql.= "AND C.id_empresa = $id_empresa ";
    if (!empty($fecha_desde)) $sql.= "AND '$fecha_desde' <= C.fecha ";
    if (!empty($fecha_hasta)) $sql.= "AND C.fecha <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    return $q->result();
  }

  function delete($id) {
    // Controlamos que se este borrando un propiedad que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $q = $this->db->query("SELECT * FROM mant_mantenimientos WHERE id = $id AND id_empresa = $id_empresa ");
    if ($q->num_rows()>0) {
      $this->db->query("DELETE TA.* FROM mant_tareas_articulos TA INNER JOIN mant_tareas T ON (TA.id_tarea = T.id AND TA.id_empresa = T.id_empresa) INNER JOIN mant_ordenes_trabajo OT ON (T.id_orden_trabajo = OT.id AND T.id_empresa = OT.id_empresa) WHERE OT.id_mantenimiento = $id AND OT.id_empresa = $id_empresa");
      $this->db->query("DELETE T.* FROM mant_tareas T INNER JOIN mant_ordenes_trabajo OT ON (T.id_orden_trabajo = OT.id AND T.id_empresa = OT.id_empresa) WHERE OT.id_mantenimiento = $id AND OT.id_empresa = $id_empresa");
      $this->db->query("DELETE FROM mant_ordenes_trabajo WHERE id_mantenimiento = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM mant_mantenimientos WHERE id = $id AND id_empresa = $id_empresa");
    }
  }

}