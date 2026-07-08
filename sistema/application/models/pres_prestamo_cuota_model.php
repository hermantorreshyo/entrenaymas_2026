<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pres_Prestamo_Cuota_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("pres_prestamos_cuotas","id","numero ASC");
	}

  function delete($id) {
    $id_empresa = parent::get_empresa();
    //$this->db->query("DELETE FROM pres_prestamos WHERE id = $id AND id_empresa = $id_empresa");
  }

  function facturar($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_prestamo = isset($config["id_prestamo"]) ? $config["id_prestamo"] : 0;
    $id_cuota = isset($config["id_cuota"]) ? $config["id_cuota"] : 0;
    $id_pago = isset($config["id_pago"]) ? $config["id_pago"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $usuario = isset($config["usuario"]) ? $config["usuario"] : "";

    $this->load->model("Pres_Prestamo_Model");
    $prestamo = $this->Pres_Prestamo_Model->get($id_prestamo);
    if ($prestamo === FALSE) {
      return (array(
        "error"=>1,
        "mensaje"=>"El prestamo no es valido.",
      ));
    }
    $cuota = $this->get($id_cuota);
    if ($cuota === FALSE) {
      return (array(
        "error"=>1,
        "mensaje"=>"La cuota no es valida.",
      ));
    }
    $this->load->model("Pres_Cliente_Model");
    $pres_cliente = $this->Pres_Cliente_Model->get($prestamo->id_cliente);

    // Obtenemos la sucursal
    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa AND id = $id_sucursal";
    $q = $this->db->query($sql);
    if ($q->num_rows() <= 0) {
      return (array(
        "error"=>1,
        "mensaje"=>"Error al obtener la sucursal de la cuota.",
      ));      
    }
    $sucursal = $q->row();

    // Si se hizo a traves de un pago, lo buscamos en el array de la cuota
    $pago = FALSE;
    if ($id_pago != 0) {
      foreach($cuota->pagos as $p) {
        if ($id_pago == $p->id) {
          $pago = $p; break;
        }
      }
    }

    $this->load->model("Punto_Venta_Model");
    if ($sucursal->id == 208) {
      // Si es BERISSO, tomamos el ID = 1705
      $punto_venta = $this->Punto_Venta_Model->get(1705,array(
        "id_empresa"=>$id_empresa,
      ));
    } else if ($sucursal->id == 44) {
      // Si es MAR DEL PLATA, tomamos el ID = 1235
      $punto_venta = $this->Punto_Venta_Model->get(1235,array(
        "id_empresa"=>$id_empresa,
      ));      
    } else if ($sucursal->id == 575) {
      // Si es ENSENADA 2, tomamos el ID = 2003
      $punto_venta = $this->Punto_Venta_Model->get(2003,array(
        "id_empresa"=>$id_empresa,
      ));
    } else {
      // Sino, tomamos el PV por defecto
      $punto_venta = $this->Punto_Venta_Model->get_por_defecto(array(
        "id_empresa"=>$id_empresa,
      ));
    }
    if ($punto_venta === FALSE) {
      return (array(
        "error"=>1,
        "mensaje"=>"ERROR: no hay configurado un punto de venta por defecto.",
      ));
    }

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    // Creamos una nueva factura
    $factura = new stdClass();
    $factura->id_empresa = $id_empresa;
    $factura->id = 0;
    $factura->id_cliente = $pres_cliente->id;
    $factura->cliente = $pres_cliente->nombre." ".$pres_cliente->apellido;
    $direccion = $pres_cliente->direccion;

    $factura->fecha = date("Y-m-d");
    $factura->hora = date("H:i:s");
    $factura->id_punto_venta = $punto_venta->id;
    $factura->punto_venta = $punto_venta->numero_fiscal;
    // Primero hacemos remito, para luego facturar a partir de ahi
    $factura->id_tipo_comprobante = 999;
    $letra = "R";

    $total_interes = ($pago === FALSE) ? ($cuota->interes_cuota + $cuota->interes) : $pago->cancelacion_interes;
    $neto_interes = $total_interes / 1.21;
    $iva_interes = $total_interes - $neto_interes;

    $total_capital = ($pago === FALSE) ? $cuota->capital_cuota : $pago->cancelacion_capital;
    $neto_capital = ($pago === FALSE) ? $cuota->capital_cuota : $pago->cancelacion_capital;

    $factura->total = $total_interes + $total_capital;
    $factura->subtotal = $factura->total;
    $factura->neto = $neto_interes + $neto_capital;
    $factura->iva = $iva_interes;
    $factura->percepcion_ib = 0;
    $factura->porc_descuento = 0;
    $factura->descuento = 0;

    $sql = "SELECT ultimo ";
    $sql.= "FROM numeros_comprobantes ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $punto_venta->id ";
    $sql.= "AND id_tipo_comprobante = $factura->id_tipo_comprobante ";
    $q = $this->db->query($sql);
    $r_ultimo = $q->row();
    $ultimo_numero = $r_ultimo->ultimo;

    $factura->numero = $ultimo_numero + 1;
    $factura->comprobante = $letra." ".str_pad($factura->punto_venta, 4, "0", STR_PAD_LEFT)."-".str_pad($factura->numero, 8, "0", STR_PAD_LEFT);

    // Insertamos la factura
    // El campo numero_referencia tiene el ID de la cuota, por lo tanto
    // si se elimina el remito por algun motivo, la cuota vuelve a estar NO FACTURADA
    $sql = "INSERT INTO facturas (";
    $sql.= " id_punto_venta, id_empresa, fecha, hora, ";
    $sql.= " id_usuario, punto_venta, numero, comprobante, ";
    $sql.= " id_cliente, id_tipo_comprobante, total, subtotal, ";
    $sql.= " neto, iva, efectivo, tipo_pago, estado, pagada, anulada, pago, ";
    $sql.= " direccion, id_tipo_estado, last_update, id_sucursal, sucursal, ";
    $sql.= " cliente, empresa, usuario, numero_referencia, id_referencia ";
    $sql.= ") VALUES (";
    $sql.= " '$factura->id_punto_venta', $id_empresa, '$factura->fecha', '$factura->hora', ";
    $sql.= " '$id_usuario', '$factura->punto_venta', '$factura->numero', '$factura->comprobante', ";
    $sql.= " '$factura->id_cliente', '$factura->id_tipo_comprobante', '$factura->total', '$factura->subtotal', ";
    $sql.= " '$factura->neto', '$factura->iva', '$factura->total', 'E', 0, 1, 0, '-$factura->total', ";
    $sql.= " '$direccion', 0, NOW(), '$sucursal->id', '$sucursal->nombre', ";
    $sql.= " '$factura->cliente', '$empresa->nombre', '$usuario', '$cuota->id', '$id_pago' ";
    $sql.= ")";
    $this->db->query($sql);
    $factura->id = $this->db->insert_id();

    $descripcion = "Cta. Servicios administrativos y financieros.";

    // Insertamos un item
    $sql = "INSERT INTO facturas_items (";
    $sql.= " id_tipo_comprobante, id_empresa, id_punto_venta, id_factura, id_articulo, ";
    $sql.= " cantidad, porc_iva, id_tipo_alicuota_iva, neto, precio, nombre, ";
    $sql.= " iva, total_sin_iva, total_con_iva, id_cliente, anulado, negativo, stamp ";
    $sql.= ") VALUES (";
    $sql.= " '$factura->id_tipo_comprobante', '$id_empresa', '$factura->id_punto_venta', '$factura->id', 0, ";
    $sql.= " 1, 21, 5, '$neto_interes', '$total_interes', '$descripcion', ";
    $sql.= " '$iva_interes', '$neto_interes', '$total_interes', '$factura->id_cliente', 0, 0, NOW() ";
    $sql.= ")";
    $this->db->query($sql);

    // Insertamos el IVA
    $sql = "INSERT INTO facturas_iva (";
    $sql.= " id_empresa, id_factura, id_alicuota_iva, id_punto_venta, neto, iva ";
    $sql.= ") VALUES (";
    $sql.= " '$id_empresa', '$factura->id', 5, '$factura->id_punto_venta', '$neto_interes', '$iva_interes' ";
    $sql.= ")";
    $this->db->query($sql);

    $descripcion = "Cta. Capital.";

    // Insertamos una segunda linea con el capital exento
    $sql = "INSERT INTO facturas_items (";
    $sql.= " id_tipo_comprobante, id_empresa, id_punto_venta, id_factura, id_articulo, ";
    $sql.= " cantidad, porc_iva, id_tipo_alicuota_iva, neto, precio, nombre, ";
    $sql.= " iva, total_sin_iva, total_con_iva, id_cliente, anulado, negativo, stamp ";
    $sql.= ") VALUES (";
    $sql.= " '$factura->id_tipo_comprobante', '$id_empresa', '$factura->id_punto_venta', '$factura->id', 0, ";
    $sql.= " 1, 0, 3, '$neto_capital', '$total_capital', '$descripcion', ";
    $sql.= " '0', '$neto_capital', '$total_capital', '$factura->id_cliente', 0, 0, NOW() ";
    $sql.= ")";
    $this->db->query($sql);

    // Insertamos el IVA
    $sql = "INSERT INTO facturas_iva (";
    $sql.= " id_empresa, id_factura, id_alicuota_iva, id_punto_venta, neto, iva ";
    $sql.= ") VALUES (";
    $sql.= " '$id_empresa', '$factura->id', 3, '$factura->id_punto_venta', '$neto_capital', '0' ";
    $sql.= ")";
    $this->db->query($sql);

    
    // Actualizamos el ultimo numero del comprobante
    $this->db->query("UPDATE numeros_comprobantes SET ultimo = $factura->numero WHERE id_empresa = $factura->id_empresa AND id_tipo_comprobante = $factura->id_tipo_comprobante AND id_punto_venta = $factura->id_punto_venta");

    // Actualizamos la cuota para indicar que fue facturada
    $sql = "UPDATE pres_prestamos_cuotas SET ";
    $sql.= " id_factura = $factura->id, ";
    $sql.= " id_punto_venta = $factura->id_punto_venta ";
    $sql.= "WHERE id_empresa = $cuota->id_empresa ";
    $sql.= "AND id = $cuota->id ";
    $sql.= "AND id_prestamo = $cuota->id_prestamo ";
    $this->db->query($sql);

    // En caso de que se haya hecho desde un pago
    if ($id_pago != 0) {
      // Actualizamos el pago para marcar que fue facturado
      $sql = "UPDATE pres_cajas_movimientos SET ";
      $sql.= " id_factura = $factura->id, ";
      $sql.= " id_punto_venta = $factura->id_punto_venta ";
      $sql.= "WHERE id_empresa = $cuota->id_empresa ";
      $sql.= "AND id_cuota = $cuota->id ";
      $sql.= "AND id_prestamo = $cuota->id_prestamo ";
      $sql.= "AND id = $id_pago ";
      $this->db->query($sql);      
    }

    return array(
      "error"=>0,
      "id_factura"=>$factura->id,
      "id_punto_venta"=>$factura->id_punto_venta,
    );
  }

  function marcar_tarea($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    // Solamente actualizamos la ultima tarea
    $sql = "SELECT id FROM crm_consultas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_contacto = $id_cliente "; // Del cliente
    $sql.= "AND tipo = 1 "; // Es generada
    $sql.= "AND id_origen = 17 "; // Es una tarea
    $sql.= "ORDER BY fecha DESC ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $sql = "UPDATE crm_consultas SET custom_1 = '1' WHERE id_empresa = $id_empresa AND id = $r->id ";
      $this->db->query($sql);
    }
  }

  function update($id,$data) {
    @session_start();
    $this->load->helper("fecha_helper");
    $pagos = $data->pagos;
    file_put_contents("log_prestamos_cuotas.txt", print_r($data,TRUE)."\n\n", FILE_APPEND);
    unset($data->pagos);

    $fecha_pago = "";
    $total_pagos = 0;
    $cancelacion_capital = 0;
    $cancelacion_interes = 0;

    $data->fecha_pago = "0000-00-00"; // Ponemos asi para que sea la primera menor
    foreach($pagos as $p) {
      $p->fecha = fecha_mysql($p->fecha);
      if (strpos($p->fecha, ":") === FALSE) $p->fecha = $p->fecha." ".date("H:i:s");
      if ($p->fecha > $fecha_pago) $data->fecha_pago = $p->fecha;
      $total_pagos += (float) $p->monto; // MONTO EN EL PAGO = CAPITAL + INTERES
      $cancelacion_capital += (float) $p->cancelacion_capital;
      $cancelacion_interes += (float) $p->cancelacion_interes;
    }
    $data->monto_pagado = $cancelacion_capital;
    $data->interes_pagado = $cancelacion_interes;

    $data->saldo_capital = $data->monto - $data->monto_pagado;
    $data->saldo_interes = $data->interes - $data->interes_pagado;

    if ($cancelacion_capital == 0 && sizeof($pagos) == 0) {
      $data->estado = 0; // La cuota esta vigente, no hubo ningun movimiento
    } else if ($cancelacion_capital >= $data->monto) {
      // LA CUOTA ESTA PAGA, CUANDO EL CAPITAL ESTA CANCELADO
      $data->estado = 1;
    } else {
      // LA CUOTA TIENE UN PAGO PARCIAL
      $data->estado = 2;
    }
    $data->saldo = $data->saldo_capital + $data->saldo_interes;
    $data->fecha_vencimiento = fecha_mysql($data->fecha_vencimiento);

    // Ponemos la sucursal del usuario que la cobro
    $id_usuario = $_SESSION["id"];
    $this->load->model("Usuario_Model");
    $usuario = $this->Usuario_Model->get($id_usuario);

    $this->load->model("Tipo_Gasto_Model");
    $concepto = $this->Tipo_Gasto_Model->get_by_codigo("PAGO");
    if ($concepto !== FALSE) {

      $observaciones = "";
      $sql = "SELECT P.*, C.nombre, C.apellido FROM pres_prestamos P ";
      $sql.= "INNER JOIN pres_clientes C ON (P.id_empresa = C.id_empresa AND C.id = P.id_cliente) ";
      $sql.= "WHERE P.id_empresa = $data->id_empresa ";
      $sql.= "AND P.id = $data->id_prestamo ";
      $q_pres = $this->db->query($sql);
      if ($q_pres->num_rows()>0) {
        $pres = $q_pres->row();
        $observaciones = "Pago Cuota #".$data->numero." Pres #".$pres->numero.": ".ucwords($pres->apellido." ".$pres->nombre);

        // Marcamos la ultima tarea como realizada
        $this->marcar_tarea(array(
          "id_cliente"=>$pres->id_cliente
        ));
      }
      foreach($pagos as $p) {

        if (!isset($p->id_sucursal) || empty($p->id_sucursal)) $p->id_sucursal = $usuario->id_sucursal;

        if (isset($p->id) && $p->id > 0) {
          // Actualizamos
          $sql = "UPDATE pres_cajas_movimientos ";
          $sql.= "SET monto = '$p->monto', fecha = '$p->fecha', observaciones = '$observaciones', id_usuario = '$id_usuario', ";
          $sql.= " descuento = '$p->descuento', ";
          $sql.= " cancelacion_capital = '$p->cancelacion_capital', cancelacion_interes = '$p->cancelacion_interes' ";
          $sql.= "WHERE id_prestamo = $data->id_prestamo ";
          $sql.= "AND id = $p->id ";
          $sql.= "AND id_cuota = $id ";
          $sql.= "AND id_sucursal = $p->id_sucursal ";
          $sql.= "AND id_empresa = $data->id_empresa ";
          $sql.= "AND id_concepto = $concepto->id ";
          $this->db->query($sql);
        } else {

          if ($p->id_sucursal == 0) {
            // La sucursal no puede ser 0, buscamos la sucursal del prestamo
            $sql = "SELECT * FROM pres_prestamos WHERE id_empresa = $data->id_empresa AND id = $data->id_prestamo ";
            $q_prestamo = $this->db->query($sql);
            if ($q_prestamo->num_rows()>0) {
              $r_prestamo = $q_prestamo->row();
              $p->id_sucursal = $r_prestamo->id_sucursal;
            }
          }

          // Insertamos
          $sql = "INSERT INTO pres_cajas_movimientos (id_empresa,id_concepto,monto,fecha,observaciones,id_prestamo,id_cuota,id_sucursal,id_usuario,tipo,cancelacion_capital,cancelacion_interes,descuento) VALUES (";
          $sql.= "$data->id_empresa,$concepto->id,'$p->monto','$p->fecha','$observaciones','$data->id_prestamo',$id,'$p->id_sucursal',$id_usuario,'E','$p->cancelacion_capital','$p->cancelacion_interes','$p->descuento') ";
          $this->db->query($sql);

          // Agregamos en la sesion
          if (isset($pres)) {
            if (isset($_SESSION["id_cliente_cobrado"]) && $_SESSION["id_cliente_cobrado"] == $pres->id_cliente) {
              $_SESSION["total_cobrado"] += ((float) $p->monto);
            } else {
              $_SESSION["id_cliente_cobrado"] = $pres->id_cliente;
              $_SESSION["cliente_cobrado"] = ucwords($pres->apellido." ".$pres->nombre);              
              $_SESSION["total_cobrado"] = ((float) $p->monto);
            }
          }

        }
      }
    }
    $s = parent::update($id,$data);

    // Si esta cuota no esta facturada
    $sql = "SELECT * FROM pres_prestamos_cuotas PC ";
    $sql.= "WHERE PC.id_empresa = $data->id_empresa ";
    $sql.= "AND PC.id = $id ";
    $sql.= "AND PC.id_factura = 0 ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      // Si hay cuotas anteriores facturadas de ese mismo prestamo
      $sql = "SELECT * FROM pres_prestamos_cuotas PC ";
      $sql.= "WHERE PC.id_empresa = $data->id_empresa ";
      $sql.= "AND PC.id_prestamo = $data->id_prestamo ";
      $sql.= "AND PC.id_factura != 0 ";
      $sql.= "AND PC.id != $id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        $ant = $qq->row();
        // Tenemos que generar automaticamente el remito de esa cuota
        $this->facturar(array(
          "id_empresa"=>$data->id_empresa,
          "id_prestamo"=>$data->id_prestamo,
          "id_cuota"=>$id,
          "id_sucursal"=>$ant->id_sucursal,
          "id_usuario"=>$_SESSION["id"],
          "usuario"=>$_SESSION["nombre"],
        ));
      }
    }
    return $s;
  }
	
	function get($id,$id_empresa = 0) {
		if (empty($id)) return FALSE;
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
    $sql = "SELECT P.*, ";
    $sql.= " IF(P.fecha_vencimiento = '0000-00-00','',DATE_FORMAT(P.fecha_vencimiento,'%d/%m/%Y')) AS fecha_vencimiento, ";
    $sql.= " IF(P.fecha_pago = '0000-00-00','',DATE_FORMAT(P.fecha_pago,'%d/%m/%Y')) AS fecha_pago ";
    $sql.= "FROM pres_prestamos_cuotas P ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    $sql.= "AND P.id = $id ";
		$query = $this->db->query($sql);
		$row = $query->row(); 

    if ($row !== FALSE) {
      $sql = "SELECT C.*, ";
      $sql.= " IF(C.fecha = '0000-00-00','',DATE_FORMAT(C.fecha,'%d/%m/%Y')) AS fecha ";
      $sql.= "FROM pres_cajas_movimientos C ";
      $sql.= "WHERE C.id_empresa = $row->id_empresa ";
      $sql.= "AND C.id_prestamo = $row->id_prestamo ";
      $sql.= "AND C.id_cuota = $row->id ";
      $q = $this->db->query($sql);
      $row->pagos = $q->result();
    }
		return $row;
	}

  function buscar($conf = array()) {
    $id_empresa = (isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa());
    $id_prestamo = (isset($conf["id_prestamo"]) ? $conf["id_prestamo"] : 0);
    $sql = "SELECT P.id ";
    $sql.= "FROM pres_prestamos_cuotas P ";
    $sql.= "WHERE id_prestamo = $id_prestamo AND id_empresa = $id_empresa ";
    $sql.= "ORDER BY numero ASC ";
    $q = $this->db->query($sql);
    $cuotas = array();
    foreach($q->result() as $r) {
      $cuota = $this->get($r->id);
      $cuotas[] = $cuota;
    }
    return $cuotas;
  }

  /*
  function insert($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    $cuotas = $data->cuotas;
    unset($data->cuotas);
    $id_prestamo = parent::insert($data);

    foreach($cuotas as $cuota) {
      $cuota->fecha_vencimiento = fecha_mysql($cuota->fecha_vencimiento);
      $this->db->insert("pres_prestamos_cuotas",array(
        "id_empresa"=>$data->id_empresa,
        "id_prestamo"=>$id_prestamo,
        "numero"=>$cuota->numero,
        "fecha_vencimiento"=>$cuota->fecha_vencimiento,
        "estado"=>0,
        "monto"=>$cuota->monto,
      ));
    }

    return $id_prestamo;
  }


  function buscar($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $sql = "SELECT SQL_CALC_FOUND_ROWS P.*, PC.nombre AS plan, ";
    $sql.= " IF(P.fecha = '0000-00-00','',DATE_FORMAT(P.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM pres_prestamos P ";
    $sql.= "INNER JOIN pres_planes_credito PC ON (P.id_plan = PC.id AND P.id_empresa = PC.id_empresa) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    if (!empty($id_cliente)) $sql.= "AND P.id_cliente = $id_cliente ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }
  */
}