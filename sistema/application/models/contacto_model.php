<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Contacto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("clientes","id","nombre ASC");
	}

	function get($id,$id_empresa = 0,$config = array()) {
		if (empty($id)) return FALSE;
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_min($id_empresa);

    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
		$sql = "SELECT C.*, ";
		$sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_ult_operacion,'%d/%m/%Y %H:%i:%s') AS fecha_ult_operacion, ";
    $sql.= "  DATE_FORMAT(C.fecha_vencimiento,'%d/%m/%Y %H:%i:%s') AS fecha_vencimiento, ";
		$sql.= "  IF (TI.nombre IS NULL,'',TI.nombre) AS tipo_iva, ";
		$sql.= "  IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM clientes C ";
		$sql.= " LEFT JOIN tipos_iva TI ON (C.id_tipo_iva = TI.id) ";
		$sql.= " LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
		$sql.= "WHERE C.id = $id ";
		$sql.= "AND C.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND C.id_sucursal = $id_sucursal ";
		$query = $this->db->query($sql);
		$row = $query->row(); 
    if ($row !== FALSE) {
      $this->load->model("Consulta_Model");
      $res = $this->Consulta_Model->buscar(array(
        "id_empresa"=>$id_empresa,
        "id_contacto"=>$row->id,
        "id_sucursal"=>$id_sucursal,
        "offset"=>999999,
      ));
      $row->consultas = $res["results"];

      $sql = "SELECT CO.id, CO.subtitulo, CO.id_origen, CO.id_usuario,  ";
      $sql.= " IF(RES.id IS NULL,0,1) AS respondido, ";
      if ($empresa->id_proyecto == 3) {
        $sql.= " IF(PRO.nombre IS NULL,'',PRO.nombre) AS propiedad_nombre, ";
        $sql.= " IF(PRO.id_tipo_operacion IS NULL,0,PRO.id_tipo_operacion) AS propiedad_id_tipo_operacion, ";
        $sql.= " IF(OPE.id IS NULL,'',OPE.nombre) AS propiedad_tipo_operacion, ";
      }
      if ($empresa->id_proyecto == 3) 
      $sql.= " IF(USER.nombre IS NULL,'',USER.nombre) AS respondido_por, ";
      $sql.= " IF(RES.subtitulo IS NULL,'',RES.subtitulo) AS subtitulo ";
      $sql.= "FROM crm_consultas CO ";
      $sql.= "LEFT JOIN crm_consultas RES ON (RES.id_empresa = CO.id_empresa AND RES.id_contacto = CO.id_contacto AND RES.id_email_respuesta = CO.id AND RES.tipo = 1) ";
      $sql.= "LEFT JOIN com_usuarios USER ON (RES.id_usuario = USER.id AND RES.id_empresa = USER.id_empresa) ";
      if ($empresa->id_proyecto == 3) {
        $sql.= "LEFT JOIN inm_propiedades PRO ON (PRO.id_empresa = CO.id_empresa AND PRO.id = CO.id_referencia) ";
        $sql.= "LEFT JOIN inm_tipos_operacion OPE ON (PRO.id_tipo_operacion = OPE.id) ";
      }
      $sql.= "WHERE CO.id_contacto = $row->id AND CO.id_empresa = $row->id_empresa ";
      $sql.= "AND CO.tipo = 0 ";
      $sql.= "AND CO.id_origen NOT IN (20,32) "; // Que no tome las creaciones de usuario ni las notificaciones del mismo sistema
      $sql.= "ORDER BY CO.fecha DESC ";
      $sql.= "LIMIT 0,1 ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $respuesta = $qq->row();
        $row->id_consulta = $respuesta->id;
        $row->respondido = $respuesta->respondido;
        $row->respondido_por = $respuesta->respondido_por;
        $row->subtitulo = $respuesta->subtitulo;            
        $row->id_origen = $respuesta->id_origen;
        $row->id_usuario_asignado = $respuesta->id_usuario;
        if ($empresa->id_proyecto == 3) {
          $row->propiedad_nombre = $respuesta->propiedad_nombre;
          $row->propiedad_id_tipo_operacion = $respuesta->propiedad_id_tipo_operacion;
          $row->propiedad_tipo_operacion = $respuesta->propiedad_tipo_operacion;
        }
      } else {
        $row->respondido = 0;
        $row->id_consulta = 0;
        $row->respondido_por = "";
        $row->subtitulo = "";
        $row->id_origen = 0;
        $row->id_usuario_asignado = 0;
        if ($empresa->id_proyecto == 3) {
          $row->propiedad_nombre = "";
          $row->propiedad_id_tipo_operacion = 0;
          $row->propiedad_tipo_operacion = "";
        }
      }

      // Buscamos tambien si tiene una tarea no realizada
      $sql = "SELECT id, asunto FROM crm_consultas CO ";
      $sql.= "WHERE CO.id_contacto = $row->id AND CO.id_empresa = $row->id_empresa ";
      $sql.= "AND CO.tipo = 1 ";
      $sql.= "AND CO.id_origen = 17 ";
      $sql.= "AND CO.estado = 0 ";
      $sql.= "LIMIT 0,1 ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $r_tarea = $qq->row();
        $row->tarea_asignada = 1;
        $row->tarea_titulo = $r_tarea->asunto;
      } else {
        $row->tarea_asignada = 0;
        $row->tarea_titulo = "";
      }      

      // Obtenemos las etiquetas de esa entrada
      $sql = "SELECT E.nombre ";
      $sql.= " FROM clientes_etiquetas_relacion EE INNER JOIN clientes_etiquetas E ON (EE.id_etiqueta = E.id AND EE.id_empresa = E.id_empresa) ";
      $sql.= "WHERE EE.id_cliente = $id AND EE.id_empresa = $id_empresa ORDER BY EE.orden ASC";
      $q = $this->db->query($sql);
      $row->etiquetas = array();
      foreach($q->result() as $r) {
        $row->etiquetas[] = (html_entity_decode($r->nombre));
      }
    }
		return $row;
	}
    
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function find_by_email($email,$id_empresa = 0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$res = FALSE;
		$q = $this->db->query("SELECT * FROM crm_contactos WHERE id_empresa = $id_empresa AND email = '$email'");
		if ($q->num_rows()>0) {
			$res = $q->row();
		}
		return $res;
	}

}