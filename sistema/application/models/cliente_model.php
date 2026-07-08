<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Cliente_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("clientes","id","nombre ASC");
	}

  // CAMBIA EL ESTADO DE UN CLIENTE
  // Si el estado nuevo tiene configurado una plantilla
  // se envia tambien ese email
  function editar_tipo($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id = isset($config["id"]) ? $config["id"] : 0;
    $id_asunto = isset($config["id_asunto"]) ? $config["id_asunto"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : -1;
    $custom_1 = isset($config["custom_1"]) ? $config["custom_1"] : "";

    if ($id == 0) return array("error"=>1,"mensaje"=>"Falta el parametro id.");
    if ($tipo == -1) return array("error"=>1,"mensaje"=>"Falta el parametro tipo.");

    $this->load->model("Usuario_Model");
    $this->load->model("Consulta_Tipo_Model");
    $this->load->model("Email_Template_Model");
    $this->load->model("Empresa_Model");
    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $empresa = $this->Empresa_Model->get($id_empresa);

    // Primero obtenemos el estado actual 
    $sql = "SELECT * FROM clientes WHERE id = $id AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      return array("error"=>1,"mensaje"=>"No existe el cliente solicitado");
    }
    $cliente = $q->row();

    $estado_actual = $this->Consulta_Tipo_Model->get($cliente->tipo,array(
      "id_empresa"=>$id_empresa,
    ));
    $estado_nuevo = $this->Consulta_Tipo_Model->get($tipo,array(
      "id_empresa"=>$id_empresa,
    ));

    // Actualizamos el tipo en la tabla de clientes
    $sql = "UPDATE clientes SET tipo = '$tipo' WHERE id_empresa = $id_empresa AND id = $id ";
    $q = $this->db->query($sql);

    $usuario = $this->Usuario_Model->get($id_usuario,array(
      "id_empresa"=>$id_empresa,
    ));
    if ($usuario !== FALSE) {
      // Creamos un nuevo movimiento en el historial de ese cliente
      $texto = $usuario->nombre." ha cambiado el estado: ".$estado_actual->nombre." > ".$estado_nuevo->nombre;
    } else {
      $texto = $estado_actual->nombre." > ".$estado_nuevo->nombre;
    }
    $sql = "INSERT INTO crm_consultas (id_contacto,id_empresa,fecha,asunto,texto,id_usuario,tipo,id_origen,id_asunto,custom_1) VALUES (";
    $sql.= " $id,$id_empresa,NOW(),'Cambio de estado','$texto',$id_usuario,0,32,'$id_asunto','$custom_1') ";
    $this->db->query($sql);

    // Si el estado tiene que disparar un email
    if ($estado_nuevo->id_email_template != 0) {
      $temp = $this->Email_Template_Model->get($estado_nuevo->id_email_template,$id_empresa);
      if (!empty($temp)) {
        $body = $temp->texto;
        $body = str_replace("{{nombre}}", $cliente->nombre, $body);
        $body = str_replace("{{email}}", $cliente->email, $body);
        mandrill_send(array(
          "to"=>$cliente->email,
          "from"=>"no-reply@varcreative.com",
          "from_name"=>$empresa->nombre,
          "subject"=>$temp->nombre,
          "body"=>$body,
          "bcc"=>$empresa->config["bcc_email"],
        ));
      }
    }
    return array("error"=>0);
  }

  function enviar_constant_contact($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();    
    $nombre = isset($config["nombre"]) ? $config["nombre"] : "";
    $email = isset($config["email"]) ? $config["email"] : "";
    $listas = isset($config["listas"]) ? $config["listas"] : array();
    $db_name = isset($config["db_name"]) ? $config["db_name"] : "servidor";
    $db_password = isset($config["db_password"]) ? $config["db_password"] : "qu4r2200";
    $db_user = isset($config["db_user"]) ? $config["db_user"] : "root";
    $db_server = isset($config["db_server"]) ? $config["db_server"] : "localhost";
    $this->load->library("ConstantContact.php");
    $cc = new ConstantContact(array(
      "id_empresa"=>$id_empresa,
      "db_name"=>$db_name,
      "db_password"=>$db_password,
      "db_user"=>$db_user,
      "db_server"=>$db_server
    ));
    $cc->enviar_contacto(array(
      "nombre"=>$nombre,
      "email"=>$email,
      "listas"=>$listas,
    ));
  }

  function get_cuenta_corriente($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : 0;
    $moneda = isset($config["moneda"]) ? $config["moneda"] : "ARS";
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : date("Y-m-d");
    $fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : date("Y-m-d");

    // Obtenemos los registros que estan dentro del intervalo de fechas
    $sql = "SELECT CL.nombre, C.tipo, C.numero, C.id, C.comprobante, C.anulada, C.pagada, C.tipo_pago, C.observaciones, C.cotizacion_dolar, ";
    $sql.= " DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, C.id_punto_venta, ";
    $sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, C.id_tipo_comprobante, ";
    $sql.= " IF(PV.tipo_impresion IS NULL,'',PV.tipo_impresion) AS tipo_punto_venta, ";
    $sql.= " IF(TC.negativo IS NULL,0,TC.negativo) AS negativo, ";
    $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    if ($moneda == "USD") { 
      $sql.= " IF(C.cotizacion_dolar > 0,C.total / C.cotizacion_dolar,0) AS total, ";
      $sql.= " IF(C.cotizacion_dolar > 0,C.pago / C.cotizacion_dolar,0) AS pago, ";
      $sql.= " IF(C.cotizacion_dolar > 0,(IF(s.total_pagado IS NULL,0,s.total_pagado)),0) AS total_pagado, ";
    } else {
      $sql.= " C.total, C.pago, ";
      $sql.= " (IF(s.total_pagado IS NULL,0,s.total_pagado)) AS total_pagado, ";        
    }
    //$sql.= " (IF(s.total_pagado IS NULL,0,s.total_pagado) + C.efectivo + C.tarjeta) AS total_pagado, ";
    $sql.= " CL.nombre, CL.cuit AS cuit ";
    $sql.= "FROM facturas C ";
    $sql.= "LEFT JOIN (SELECT SUM(FP.monto) AS total_pagado, FP.id_factura FROM facturas_pagos FP WHERE FP.id_empresa = $id_empresa GROUP BY FP.id_factura) s ON (s.id_factura = C.id) ";
    $sql.= "LEFT JOIN puntos_venta PV ON (C.id_punto_venta = PV.id AND C.id_empresa = PV.id_empresa) ";
    if ($id_sucursal != 0 && $id_empresa != 224) {
      $sql.= "LEFT JOIN clientes CL ON (C.id_cliente = CL.id AND CL.id_empresa = C.id_empresa AND PV.id_sucursal = CL.id_sucursal) ";
    } else {
      $sql.= "LEFT JOIN clientes CL ON (C.id_cliente = CL.id AND CL.id_empresa = C.id_empresa) ";
    }
    $sql.= "LEFT JOIN tipos_comprobante TC ON (C.id_tipo_comprobante = TC.id) ";
    $sql.= "LEFT JOIN com_localidades L ON (CL.id_localidad = L.id) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "AND C.pendiente = 0 "; // Las pendientes no figuran en la cuenta corriente
    $sql.= "AND C.id_cliente = '$id_cliente' ";
    if (!empty($id_sucursal) && $id_empresa != 224) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($estado == 0) $sql.= "AND C.estado = $estado ";
    $sql.= "AND '$fecha_desde' <= C.fecha ";
    $sql.= "AND C.fecha <= '$fecha_hasta' ";
    $sql.= "ORDER BY C.fecha ASC, C.id ASC ";
    $query = $this->db->query($sql);
    return $query->result();
  }

  function buscar_por_nombre($nombre,$config=array()) {
    $direccion = isset($config["direccion"]) ? $config["direccion"] : "";
    $nombre = trim($nombre);
    $id_empresa = $config["id_empresa"];
    $sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa AND nombre = '$nombre' ";
    if (!empty($direccion)) $sql.= "AND direccion = '$direccion' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      return $row;
    } else return FALSE;
  }

  function sincronizar_app($config = array()) {
    $id_empresa = $config["id_empresa"];
    $id_vendedor = isset($config["id_vendedor"]) ? $config["id_vendedor"] : 0;
    $version = isset($config["version"]) ? $config["version"] : 1;
    $sep = ";;;";
    $sql = "SELECT * FROM clientes WHERE id_empresa = $id_empresa AND nombre != '' ";
    if ($id_vendedor != 0) $sql.= "AND id_vendedor = $id_vendedor ";
    $q = $this->db->query($sql);
    $salida = "";
    foreach($q->result() as $row) {
      $row->direccion = str_replace("\"", "", str_replace("'", "", $row->direccion));
      $row->nombre = str_replace("\"", "", str_replace("'", "", $row->nombre));
      $row->observaciones = str_replace("\"", "", str_replace("'", "", $row->observaciones));
      if ($version >= 9) {
        $inhabilitado_pedir = 0;

        // En BASILE, si tiene la etiqueta Deuda entonces tiene que quedar automaticamente inhabilitado para pedir
        if ($id_empresa == 972) {
          $sql = "SELECT 1 FROM clientes_etiquetas_relacion ER ";
          $sql.= " INNER JOIN clientes_etiquetas E ON (E.id_empresa = ER.id_empresa AND E.id = ER.id_etiqueta) ";
          $sql.= "WHERE ER.id_empresa = $id_empresa ";
          $sql.= "AND ER.id_cliente = $row->id ";
          $sql.= "AND E.link = 'deuda' ";
          $qq = $this->db->query($sql);
          $inhabilitado_pedir = (($qq->num_rows() > 0) ? 1 : 0);
        }
        $custom_1 = "";
        $custom_2 = "";
        $custom_3 = "";
        $custom_4 = "";
        $custom_5 = "";
        $custom_6 = "";
        $custom_7 = "";
        $custom_8 = "";
        $custom_9 = "";
        $custom_10 = "";
        
        // Tabla de clientes
        $salida.= "clientes".$sep."$row->id".$sep."$row->nombre".$sep."$row->direccion".$sep;
        $salida.= "$row->lista".$sep."$row->telefono".$sep."$row->cuit".$sep."$row->id_tipo_iva".$sep;
        $salida.= "1".$sep."0".$sep.$row->email.$sep.$row->observaciones.$sep.$row->latitud.$sep.$row->longitud;
        $salida.= "\n";

        // Tabla de clientes_2
        $salida.= "clientes_2".$sep."$row->id".$sep;
        $salida.= $inhabilitado_pedir.$sep;
        // Todos estos campos pueden tener uso en el futuro para cualquier cosa
        $salida.= $custom_1.$sep.$custom_2.$sep.$custom_3.$sep.$custom_4.$sep.$custom_5.$sep.$custom_6.$sep.$custom_7.$sep.$custom_8.$sep.$custom_9.$sep.$custom_10;
        $salida.= "\n";        

      } else if ($version >= 3) {
        // Estructura: id,nombre,direccion,lista,telefono,cuit,id_tipo_iva,uploaded,limite_bonif
        $salida.= "clientes".$sep."$row->id".$sep."$row->nombre".$sep."$row->direccion".$sep."$row->lista".$sep."$row->telefono".$sep."$row->cuit".$sep."$row->id_tipo_iva".$sep."1".$sep."0".$sep.$row->email.$sep.$row->observaciones.$sep.$row->latitud.$sep.$row->longitud."\n";
      } else {
        $salida.= "INSERT INTO clientes (id,nombre,direccion,lista,telefono,cuit,id_tipo_iva,uploaded) VALUES ('$row->id','$row->nombre','$row->direccion','$row->lista','$row->telefono','$row->cuit','$row->id_tipo_iva','1') \n";
      }      
    }

    $sql = "SELECT * FROM recorridos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      if ($version >= 3) {
        $salida.= "recorridos".$sep."$row->id".$sep."$row->nombre\n";
      } else {
        $salida.= "INSERT INTO recorridos (_id,nombre) VALUES ('$row->id','$row->nombre') \n";
      }
    }

    $sql = "SELECT * FROM recorridos_clientes WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      if ($version >= 3) {
        // Estructura: id_recorrido,id_cliente,orden,visitado
        $salida.= "recorridos_clientes".$sep."$row->id".$sep."$row->id_recorrido".$sep."$row->id_cliente".$sep."$row->orden".$sep."0\n";
      } else {
        $salida.= "INSERT INTO recorridos_clientes (id_recorrido,id_cliente,orden,visitado) VALUES ('$row->id_recorrido','$row->id_cliente','$row->orden',0) \n";  
      }      
    }

    if ($version > 1) {
      $sql = "SELECT *, IF(fecha_ultima_venta = '0000-00-00','',DATE_FORMAT(fecha_ultima_venta,'%d/%m/%Y')) AS fecha_ultima_venta ";
      $sql.= "FROM articulos_clientes WHERE id_empresa = $id_empresa AND promedio_venta > 0 ORDER BY promedio_venta DESC ";
      $q = $this->db->query($sql);
      foreach($q->result() as $row) {
        if ($version >= 7) {
          // Estructura: id_empresa,id_articulo,id_cliente,promedio_venta,ultima_venta,fecha_ultima_venta
          $salida.= "articulos_clientes".$sep."$row->id_empresa".$sep."$row->id_articulo".$sep."$row->id_cliente".$sep."$row->promedio_venta".$sep.$row->ultima_venta.$sep.$row->fecha_ultima_venta."\n";
        } else if ($version >= 6) {
          // Estructura: id_empresa,id_articulo,id_cliente,promedio_venta
          $salida.= "articulos_clientes".$sep."$row->id_empresa".$sep."$row->id_articulo".$sep."$row->id_cliente".$sep."$row->promedio_venta\n";          
        } else if ($version >= 3) {
          // Estructura: id_empresa,id_articulo,id_cliente,promedio_venta
          $salida.= "articulos_clientes".$sep."$row->id_empresa".$sep."$row->id_articulo".$sep."$row->id_cliente".$sep."$row->promedio_venta\n";
        } else {
          $salida.= "INSERT INTO articulos_clientes (id_empresa,id_articulo,id_cliente,promedio_venta) VALUES ('$row->id_empresa','$row->id_articulo','$row->id_cliente','$row->promedio_venta') \n";  
        }
      }
    }

    return $salida;
  }

  function update($id,$data) {
    $this->remove_attributes($data);
    return parent::update($id,$data);
  }

  function remove_attributes($data) {
    unset($data->tipo_iva);
    unset($data->error);
    unset($data->undefined);
    unset($data->etiquetas);
  }
	
	function count_actives() {
		$id_empresa = parent::get_empresa();
		$q = $this->db->query("SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM clientes WHERE id_empresa = $id_empresa AND activo = 1 ");
		$r = $q->row();
		return $r->cantidad;
	}	

	function save_tag($tag) {
		$this->load->helper("file_helper");
		// Primero controlamos si existe la etiqueta
		$q = $this->db->query("SELECT * FROM clientes_etiquetas WHERE nombre = '$tag->nombre' AND id_empresa = $tag->id_empresa LIMIT 0,1");
		if ($q->num_rows()<=0) {
			// Si no existe, la guardamos
			$link = filename($tag->nombre,"-",0);
			$this->db->query("INSERT INTO clientes_etiquetas (nombre,link,id_empresa) VALUES ('$tag->nombre','$link',$tag->id_empresa)");
			$id_etiqueta = $this->db->insert_id();
		} else {
			$row = $q->row();
			$id_etiqueta = $row->id;
		}
    // Controlamos que ya no este cargada, para no ponerla 2 veces
    $sql = "SELECT * FROM clientes_etiquetas_relacion WHERE id_empresa = $tag->id_empresa AND id_cliente = $tag->id_cliente AND id_etiqueta = $id_etiqueta ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
		  $this->db->query("INSERT INTO clientes_etiquetas_relacion (id_empresa,id_cliente,id_etiqueta,orden) VALUES ($tag->id_empresa,$tag->id_cliente,$id_etiqueta,$tag->orden) ");
    }
	}

	
	/*
    function delete($id) {
        // En realidad no se borra el cliente, sino que se desactiva
        $id_empresa = parent::get_empresa();
        $this->db->query("UPDATE clientes SET activo = 0 WHERE id = $id AND id_empresa = $id_empresa");
    }
    */

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM turnos WHERE id_cliente = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM via_reservas WHERE id_cliente = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM inm_propiedades_contactos WHERE id_contacto = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM inm_busquedas_contactos WHERE id_cliente = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM crm_consultas WHERE id_contacto = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM hot_reservas WHERE id_cliente = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM clientes WHERE id = $id AND id_empresa = $id_empresa");
  }
	
	function get($id,$id_empresa = 0,$config = array()) {
		if (empty($id)) return FALSE;
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $buscar_consultas = isset($config["buscar_consultas"]) ? $config["buscar_consultas"] : 1;
    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 1;

		$sql = "SELECT C.*, C.localidad AS cliente_localidad, ";
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
    if ($row !== FALSE && !empty($row) && isset($row->id)) {
      if ($buscar_consultas == 1) {
        $this->load->model("Consulta_Model");
        $res = $this->Consulta_Model->buscar(array(
          "id_empresa"=>$id_empresa,
          "id_contacto"=>$row->id,
          "id_sucursal"=>$id_sucursal,
          "offset"=>999999,
        ));
        $row->consultas = $res["results"];
      } else $row->consultas = array();

      if ($buscar_etiquetas == 1) {
        $sql = "SELECT E.nombre ";
        $sql.= " FROM clientes_etiquetas_relacion EE INNER JOIN clientes_etiquetas E ON (EE.id_etiqueta = E.id AND EE.id_empresa = E.id_empresa) ";
        $sql.= "WHERE EE.id_cliente = $id AND EE.id_empresa = $id_empresa ORDER BY EE.orden ASC";
        $q = $this->db->query($sql);
        $row->etiquetas = array();
        foreach($q->result() as $r) {
          $row->etiquetas[] = (html_entity_decode($r->nombre));
        }
      } else $row->etiquetas = array();
    }
		return $row;
	}

  function next($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT IF(MAX(CAST(codigo AS SIGNED)) IS NULL,0,MAX(CAST(codigo AS SIGNED))) AS codigo FROM clientes WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    $r = $q->row();
    if (!empty($r->codigo) && is_numeric($r->codigo)) {
      $codigo = ((int)$r->codigo + 1);
    } else {
      $codigo = "";
    }
    return $codigo;
  }
	
  function get_by_codigo($codigo,$config = array()) {
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT C.*, ";
    $sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_ult_operacion,'%d/%m/%Y %H:%i:%s') AS fecha_ult_operacion, ";
    $sql.= "  DATE_FORMAT(C.fecha_vencimiento,'%d/%m/%Y %H:%i:%s') AS fecha_vencimiento, ";
    $sql.= "  IF (TI.nombre IS NULL,'',TI.nombre) AS tipo_iva, ";
    $sql.= "  IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= "FROM clientes C ";
    $sql.= " LEFT JOIN tipos_iva TI ON (C.id_tipo_iva = TI.id) ";
    $sql.= " LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
    $sql.= "WHERE C.codigo = '$codigo' AND C.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND C.id_sucursal = $id_sucursal ";
    $query = $this->db->query($sql);
    $row = $query->row(); 
    if (isset($row->id)) {
      // Obtenemos las etiquetas de esa entrada
      $row->etiquetas = array();
      $sql = "SELECT E.nombre ";
      $sql.= " FROM clientes_etiquetas_relacion EE INNER JOIN clientes_etiquetas E ON (EE.id_etiqueta = E.id AND EE.id_empresa = E.id_empresa) ";
      $sql.= "WHERE EE.id_cliente = $row->id AND EE.id_empresa = $id_empresa ORDER BY EE.orden ASC";
      $q = $this->db->query($sql);
      $row->etiquetas = array();
      foreach($q->result() as $r) {
        $row->etiquetas[] = (html_entity_decode($r->nombre));
      }
    }

    $this->db->close();
    return $row;
  }
	
	function get_by_email($email,$id_empresa = 0,$config=array()) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$tipo = isset($config["tipo"]) ? $config["tipo"] : -1;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
		$sql = "SELECT C.*, ";
		$sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_ult_operacion,'%d/%m/%Y %H:%i:%s') AS fecha_ult_operacion, ";
    $sql.= "  DATE_FORMAT(C.fecha_vencimiento,'%d/%m/%Y %H:%i:%s') AS fecha_vencimiento, ";
		$sql.= "  IF (TI.nombre IS NULL,'',TI.nombre) AS tipo_iva, ";
		$sql.= "  IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM clientes C ";
		$sql.= " LEFT JOIN tipos_iva TI ON (C.id_tipo_iva = TI.id) ";
		$sql.= " LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
		$sql.= "WHERE C.email = '$email' AND C.id_empresa = $id_empresa ";
    if (!empty($id_usuario)) $sql.= "AND C.id_usuario = '$id_usuario' ";
		if ($tipo != -1) $sql.= "AND C.tipo = '$tipo' ";
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) return FALSE;
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}	

  function get_by_telefono($telefono,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $tipo = isset($config["tipo"]) ? $config["tipo"] : -1;
    $sql = "SELECT C.*, ";
    $sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_ult_operacion,'%d/%m/%Y %H:%i:%s') AS fecha_ult_operacion, ";
    $sql.= "  DATE_FORMAT(C.fecha_vencimiento,'%d/%m/%Y %H:%i:%s') AS fecha_vencimiento, ";
    $sql.= "  IF (TI.nombre IS NULL,'',TI.nombre) AS tipo_iva, ";
    $sql.= "  IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= "FROM clientes C ";
    $sql.= " LEFT JOIN tipos_iva TI ON (C.id_tipo_iva = TI.id) ";
    $sql.= " LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
    $sql.= "WHERE C.telefono = '$telefono' AND C.id_empresa = $id_empresa ";
    if ($tipo != -1) $sql.= "AND C.tipo = '$tipo' ";
    $query = $this->db->query($sql);
    if ($query->num_rows() == 0) return FALSE;
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }   
	
  function get_by_id($id,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT C.* ";
    $sql.= "FROM clientes C ";
    $sql.= "WHERE C.id = $id AND C.id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    if ($query->num_rows() == 0) return FALSE;
    $row = $query->row(); 
    return $row;
  } 
    
  function saldo($id_cliente,$id_empresa = 0,$fecha = '',$config = array()) {
    $id_empresa = ($id_empresa != 0) ? $id_empresa : parent::get_empresa();
    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $moneda = (isset($config["moneda"])) ? $config["moneda"] : "ARS";

    $saldo_inicial = 0;
    $id_sucursal = 0;
    $sql = "SELECT saldo_inicial, saldo_inicial_2, id_sucursal FROM clientes WHERE id = $id_cliente AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $cliente = $q->row();
      $id_sucursal = $cliente->id_sucursal;
      if ($estado == 1) $saldo_inicial = $cliente->saldo_inicial_2;
      else $saldo_inicial = $cliente->saldo_inicial;
    }

    $sql = "SELECT ";
    if ($moneda == "USD") {
      // Si no tiene cotizacion del dolar, no la tomamos
      $sql.= "IF (C.cotizacion_dolar > 0, (SUM(CASE T.negativo WHEN 1 THEN -(total + pago) ELSE (total + pago) END) / C.cotizacion_dolar), 0)  AS saldo ";
    } else { 
      $sql.= "SUM(CASE T.negativo WHEN 1 THEN -(total + pago) ELSE (total + pago) END) AS saldo ";
    }
    $sql.= "FROM facturas C ";
    $sql.= " LEFT JOIN puntos_venta PV ON (C.id_punto_venta = PV.id AND C.id_empresa = PV.id_empresa) ";
    $sql.= " LEFT JOIN tipos_comprobante T ON (C.id_tipo_comprobante = T.id) ";
    $sql.= "WHERE C.id_cliente = $id_cliente AND C.anulada = 0 "; // Que no este anulada
    //$sql.= "AND PV.id_sucursal = $id_sucursal ";
    if (!empty($fecha)) $sql.= "AND C.fecha < '$fecha' "; // Que sea menor a la fecha que estamos buscando
    $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
    $sql.= "AND C.pendiente = 0 "; // Que no este pendiente de aprobacion
    if ($estado == 0) $sql.= "AND C.estado = $estado ";
    $query = $this->db->query($sql);
    $row = $query->row();
    return (is_null($row->saldo) ? $saldo_inicial : ($saldo_inicial + $row->saldo));
  }
	
	function listado_saldos($id_empresa = 0,$fecha = '', $order = "", $filtrar_en_cero = 0, $config = array()) {
		
    $id_etiqueta = isset($config["id_etiqueta"]) ? $config["id_etiqueta"] : 0;
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";

		$salida = array();
		$id_empresa = parent::get_empresa();
		$estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
		
		$sql = "SELECT C.id,C.saldo_inicial,C.saldo_inicial_2, ";
		$sql.= " C.codigo, C.nombre, C.id_vendedor, C.observaciones ";
		$sql.= "FROM clientes C WHERE C.id_empresa = $id_empresa AND C.activo = 1 ";
    if (!empty($id_etiqueta)) {
      $sql.= "AND EXISTS (SELECT 1 FROM clientes_etiquetas_relacion CER WHERE CER.id_empresa = C.id_empresa AND CER.id_cliente = C.id AND CER.id_etiqueta = $id_etiqueta) ";
    }

		if (!empty($order)) $sql.= "ORDER BY $order";
		$q_clientes = $this->db->query($sql);
		foreach($q_clientes->result() as $cliente) {
			
			// Saldo inicial de ese cliente
			$saldo_inicial = 0;
			if ($estado == 1) $saldo_inicial = $cliente->saldo_inicial_2;
			else $saldo_inicial = $cliente->saldo_inicial;
			
			// Calculamos el saldo a partir de los movimientos
			$sql = "SELECT SUM(CASE T.negativo WHEN 1 THEN -(total + pago) ELSE (total + pago) END) AS saldo, MAX(F.fecha) AS fecha ";
			$sql.= "FROM facturas F ";
			$sql.= " LEFT JOIN tipos_comprobante T ON (F.id_tipo_comprobante = T.id) ";
			$sql.= "WHERE F.anulada = 0 AND F.id_empresa = $id_empresa ";
			$sql.= "AND F.pendiente = 0 "; // Que no este pendiente de aprobacion
			if ($estado == 0) $sql.= "AND F.estado = $estado ";
			if (!empty($fecha)) $sql.= "AND F.fecha <= '$fecha' ";
			$sql.= "AND F.id_cliente = $cliente->id ";
			$q_saldo = $this->db->query($sql);
			$saldo = $q_saldo->row();

      // Solamente tomamos los saldos en caso de que la ultima factura sea superior a la fecha desde
      if (!empty($fecha_desde) && $saldo->fecha <= $fecha_desde) continue;

			if (empty($saldo->saldo) || is_null($saldo->saldo)) $saldo->saldo = 0;
			
			// Creamos el objeto que representa a una fila
			$row = new stdClass();
			$row->id = $cliente->id;
			$row->id_vendedor = $cliente->id_vendedor;
			$row->codigo = $cliente->codigo;
			$row->nombre = $cliente->nombre;
			$row->saldo = $saldo_inicial + $saldo->saldo;
      $row->observaciones = $cliente->observaciones;
			
			if ($filtrar_en_cero == 1) {
				// Tenemos una tolerancia de un peso
				if (abs($row->saldo) > 1) $salida[] = $row;
			} else {
				$salida[] = $row;
			}
		}
		return $salida;
	}
	
	function buscar($params = array()) {
		$id_empresa = (isset($params["id_empresa"])) ? $params["id_empresa"] : $this->get_empresa();
		$filter = isset($params["filter"]) ? $params["filter"] : "";
    $cuit = isset($params["cuit"]) ? $params["cuit"] : "";
    $telefono = isset($params["telefono"]) ? $params["telefono"] : "";
    $codigo_propiedad = isset($params["codigo_propiedad"]) ? $params["codigo_propiedad"] : "";
    $id_usuario = isset($params["id_usuario"]) ? $params["id_usuario"] : 0;
		$id_vendedor = isset($params["id_vendedor"]) ? $params["id_vendedor"] : 0;
    $id_etiqueta = isset($params["id_etiqueta"]) ? $params["id_etiqueta"] : 0;
    $id_proyecto = isset($params["id_proyecto"]) ? $params["id_proyecto"] : 0;
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
		$offset = isset($params["offset"]) ? $params["offset"] : 0;
    $tipo = isset($params["tipo"]) ? $params["tipo"] : -1;
    $activo = isset($params["activo"]) ? $params["activo"] : -1;
    $buscar_respuesta = isset($params["buscar_respuesta"]) ? $params["buscar_respuesta"] : 0;
		$order = (isset($params["order"]) && !empty($params["order"])) ? $params["order"] : "C.nombre ASC ";
    $custom_3 = isset($params["custom_3"]) ? $params["custom_3"] : "";
    $custom_4 = isset($params["custom_4"]) ? $params["custom_4"] : "";
    $custom_5 = isset($params["custom_5"]) ? $params["custom_5"] : "";
    $desde = isset($params["desde"]) ? $params["desde"] : "";
    $hasta = isset($params["hasta"]) ? $params["hasta"] : "";

		$sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
    $sql.= "  IF(T.nombre IS NULL,'',T.nombre) AS tipo_estado, ";
    $sql.= "  IF(T.color IS NULL,'',T.color) AS color_estado, ";
		$sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_ult_operacion,'%d/%m/%Y %H:%i:%s') AS fecha_ult_operacion, ";
    $sql.= "  DATE_FORMAT(C.fecha_vencimiento,'%d/%m/%Y %H:%i:%s') AS fecha_vencimiento, ";
    $sql.= "  IF(V.nombre IS NULL,'',V.nombre) AS vendedor, ";
		$sql.= " IF(L.nombre IS NULL,'',CONCAT(L.nombre,' (',P.abreviacion,')')) AS localidad ";
		$sql.= "FROM clientes C ";
    $sql.= "LEFT JOIN crm_consultas_tipos T ON (C.id_empresa = T.id_empresa AND C.tipo = T.id) ";
		$sql.= "LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
		$sql.= "LEFT JOIN com_departamentos D ON (L.id_departamento = D.id) ";
		$sql.= "LEFT JOIN com_provincias P ON (D.id_provincia = P.id) ";
    $sql.= "LEFT JOIN vendedores V ON (V.id = C.id_vendedor AND V.id_empresa = C.id_empresa) ";
		$sql.= "WHERE C.id_empresa = $id_empresa ";
    if (!empty($id_vendedor)) $sql.= "AND C.id_vendedor = $id_vendedor ";
		if (!empty($filter)) $sql.= "AND (C.nombre LIKE '%$filter%' OR C.codigo = '$filter' ) ";
    if (!empty($cuit)) $sql.= "AND C.cuit = '$cuit' ";
    if (!empty($telefono)) {
      if ($id_empresa == 571) $sql.= "AND CONCAT(C.telefono,C.celular) = '$telefono' ";
      else $sql.= "AND C.telefono = '$telefono' ";
    }
    if ($tipo != -1) $sql.= "AND C.tipo = '$tipo' ";
    if ($activo != -1) $sql.= "AND C.activo = '$activo' ";

    // INMOVAR: Filtro de consulta
    if (!empty($custom_3)) $sql.= "AND C.custom_3 = '$custom_3' ";

    // INMOVAR: Filtro de inquilino
    if (!empty($custom_4)) $sql.= "AND C.custom_4 = '$custom_4' ";

    // INMOVAR: Filtro de propietario | TOQUE lo usa como ID_USUARIO
    if (!empty($custom_5)) $sql.= "AND C.custom_5 = '$custom_5' ";

    if ($id_usuario != 0) {
      $sql.= "AND C.id_usuario = $id_usuario ";
      //$sql.= "AND EXISTS (SELECT 1 FROM crm_consultas CON WHERE CON.id_empresa = C.id_empresa AND CON.id_contacto = C.id AND CON.id_usuario = $id_usuario) ";
    }
    if (!empty($codigo_propiedad)) {
      $sql.= "AND EXISTS (SELECT 1 FROM crm_consultas CON INNER JOIN inm_propiedades PROP ON (CON.id_empresa = PROP.id_empresa AND CON.id_referencia = PROP.id) WHERE CON.id_empresa = C.id_empresa AND CON.id_contacto = C.id AND PROP.codigo = '$codigo_propiedad') ";
    }
    if (!empty($desde) || !empty($hasta)) {
      $this->load->helper("fecha_helper");
      $sql.= "AND EXISTS (SELECT 1 FROM crm_consultas CON WHERE CON.id_empresa = C.id_empresa AND CON.id_contacto = C.id ";
      $sql.= " AND CON.id_origen NOT IN (20,32) ";
      if (!empty($desde)) {
        $desde = fecha_mysql($desde);
        $sql.= " AND CON.fecha >= '$desde 00:00:00' ";
      }
      if (!empty($hasta)) {
        $hasta = fecha_mysql($hasta);
        $sql.= " AND CON.fecha <= '$hasta 23:59:59' ";
      }
      $sql.= ") ";
    }

    if (!empty($id_etiqueta)) {
      $sql.= "AND EXISTS (SELECT 1 FROM clientes_etiquetas_relacion CER WHERE CER.id_empresa = C.id_empresa AND CER.id_cliente = C.id AND CER.id_etiqueta = $id_etiqueta) ";
    }

		$sql.= "ORDER BY $order ";
		if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $mi_sql = $sql;
    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    //Si estamos filtrando por id usuario, tenemos que verificar si
    //El cliente debe aparecer blureado o no

    $this->load->model("Usuario_Model");
    $this->load->model("Consulta_Model");
    if ($id_usuario != 0) {
      $usu = $this->Usuario_Model->get($id_usuario);
    }


    $salida = array();
    foreach($q->result() as $row) {

      // Buscamos si la ultima consulta que tiene fue respondida
      if ($buscar_respuesta == 1) {
        $sql = "SELECT CO.id, CO.subtitulo, CO.id_origen, CO.id_usuario,  ";
        $sql.= " IF(RES.id IS NULL,0,1) AS respondido, ";
        if ($id_proyecto == 3) {
          $sql.= " IF(PRO.nombre IS NULL,'',PRO.nombre) AS propiedad_nombre, ";
          $sql.= " IF(PRO.id_tipo_operacion IS NULL,0,PRO.id_tipo_operacion) AS propiedad_id_tipo_operacion, ";
          $sql.= " IF(OPE.id IS NULL,'',OPE.nombre) AS propiedad_tipo_operacion, ";
        }
        if ($id_proyecto == 3) 
        $sql.= " IF(USER.nombre IS NULL,'',USER.nombre) AS respondido_por, ";
        $sql.= " IF(RES.subtitulo IS NULL,'',RES.subtitulo) AS subtitulo ";
        $sql.= "FROM crm_consultas CO ";
        $sql.= "LEFT JOIN crm_consultas RES ON (RES.id_empresa = CO.id_empresa AND RES.id_contacto = CO.id_contacto AND RES.id_email_respuesta = CO.id AND RES.tipo = 1) ";
        $sql.= "LEFT JOIN com_usuarios USER ON (RES.id_usuario = USER.id AND RES.id_empresa = USER.id_empresa) ";
        if ($id_proyecto == 3) {
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
          if ($id_proyecto == 3) {
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
          if ($id_proyecto == 3) {
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


        $cliente_no_blureado = FALSE;
        //Si un usuario esta buscando sus consultas
        if ($id_usuario != 0) {
          if ($usu->id_perfiles == 1357) {
            $cliente_no_blureado = $this->Consulta_Model->get_primera_consulta_mensual(array(
              "id_usuario"=>$id_usuario,
              "asunto"=>"Contacto",
              "id_origen"=>9,
              "id_contacto"=>$row->id,
            ));
          }  
        //Si un administrador esta buscando todo
        } else {
          $cliente_no_blureado = $this->Consulta_Model->get_primera_consulta_mensual(array(
            "id_usuario"=>$row->id_usuario,
            "asunto"=>"Contacto",
            "id_origen"=>9,
            "id_contacto"=>$row->id,
          ));
        }

        // Blur
        $row->blur = 0;
        if ($cliente_no_blureado !== FALSE && $cliente_no_blureado->dias_ultima_consulta < 5) {
          //Si esta blureado y busca un usuario, le pasamos 1, si esta blureado pero le pasamos un admin, 2
          $row->blur = ($id_usuario == 0) ? 2 : 1;
        }

      }

      // Buscamos si tiene etiquetas
      $row->etiquetas = array();
      $sql = "SELECT ER.*, CE.nombre, CE.link ";
      $sql.= "FROM clientes_etiquetas_relacion ER ";
      $sql.= "INNER JOIN clientes_etiquetas CE ON (ER.id_etiqueta = CE.id AND ER.id_empresa = CE.id_empresa) ";
      $sql.= "WHERE ER.id_empresa = $row->id_empresa ";
      $sql.= "AND ER.id_cliente = $row->id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        foreach($qq->result() as $etiq) {
          $row->etiquetas[] = $etiq;
        }
      }

      $salida[] = $row;
    }

    $ss = array(
      "results"=>$salida,
      "total"=>$total->total,
      "sql"=>$mi_sql,
    );      

    // El parametro buscar_respuesta indica que estamos buscando las consultas
    // y en la parte de consultas necesitamos mandar los totales de consultas por estado
    if ($buscar_respuesta == 1) {
      $sql = "SELECT C.tipo, IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM clientes C ";
      $sql.= "WHERE C.id_empresa = $id_empresa ";
      if ($activo != -1) $sql.= "AND C.activo = '$activo' ";      
      if ($id_usuario != 0 && $id_empresa != 571) {
        $sql.= "AND EXISTS (SELECT 1 FROM crm_consultas CON WHERE CON.id_empresa = C.id_empresa AND CON.id_contacto = C.id AND CON.id_usuario = $id_usuario) ";
      }
      $sql.= "GROUP BY C.tipo ";
      $q = $this->db->query($sql);
      $ss["meta"] = array("estados"=>$q->result());
    }

		return $ss;
	}    
}