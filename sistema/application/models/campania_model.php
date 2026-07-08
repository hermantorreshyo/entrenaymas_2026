<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Campania_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("pub_campanias","id","valida_desde DESC");
	}

	function existe_remito($campania,$periodo,$pv) {

		// Controlamos que ya no exista un remito para el mismo periodo de la campaña
		// Estas variables:
		//   id_referencia = id_campania
		//   numero_referencia = periodo
    $sql = "SELECT * ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_referencia = $campania->id ";
    $sql.= "AND numero_referencia = '$periodo' ";
    $sql.= "AND id_punto_venta = $pv->id ";
    $sql.= "AND id_empresa = $campania->id_empresa ";
    $q_fact = $this->db->query($sql);
    if ($q_fact->num_rows()>0) {

      // Ya hay una factura para esa cuota
      $factura = $q_fact->row();

      // Si la factura no esta paga
      if ($factura->pagada == 0) {
      	$sql = "UPDATE facturas SET ";
      	$sql.= " total = $campania->costo, ";
      	$sql.= " subtotal = $campania->costo ";
      	$sql.= "WHERE id = $factura->id AND id_empresa = $factura->id_empresa ";
      	$this->db->query($sql);
      }
      return TRUE;
    }
    return FALSE;
	}

  function remitos($campania) {

		$desde = new DateTime($campania->valida_desde);
		$hasta = new DateTime($campania->valida_hasta);
    $interval = new DateInterval('P1M');
    $range = new DatePeriod($desde,$interval,$hasta);

    // Punto de venta por defecto
    $sql = "SELECT * FROM puntos_venta ";
    $sql.= "WHERE id_empresa = $campania->id_empresa ";
    $sql.= "ORDER BY por_default DESC ";
    $sql.= "LIMIT 0,1 ";
    $q_pv = $this->db->query($sql);
    if ($q_pv->num_rows()<=0) return;
    $pv = $q_pv->row();

    // Ultimo numero de remito
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_tipo_comprobante = 999 ";
    $sql.= "AND id_punto_venta = $pv->id ";
    $sql.= "AND id_empresa = $campania->id_empresa ";
    $q_remito = $this->db->query($sql);
    $row = $q_remito->row();
    $numero_remito = $row->numero + 1;

    // Si la campaña pertenece a un vendedor
    if ($campania->id_vendedor != 0) {
    	$this->load->model("Vendedor_Model");
    	$vendedor = $this->Vendedor_Model->get($campania->id_vendedor);
    	if ($campania->primer_pago == 1) {
				$comision_vendedor = 100;
    	} else {
    		$comision_vendedor = $vendedor->comision;	
    	}
    } else {
    	$comision_vendedor = 0;
    }

    // Se genera un solo remito, no por MES
    if ($campania->pago_unico == 1) {

    	// Generamos el periodo a partir de la fecha
    	$periodo = date('Ym',strtotime($campania->valida_desde));

    	// Si no existe el remito, lo generamos
    	if ($this->existe_remito($campania,$periodo,$pv) === FALSE) {

		    // Insertamos un nuevo comprobante que corresponde al periodo
		    $hoy = date("Y-m-d");
		    $hora = date("H:i:s");
		    $comprobante = "R ".str_pad($pv->numero, 4 , "0", STR_PAD_LEFT)."-".str_pad($numero_remito, 8, "0", STR_PAD_LEFT);
		    $hash = md5($campania->id_empresa.$comprobante);

		    // Sirven al momento del pago, poder identificar que cuota se esta pagando
		    $sql = "INSERT INTO facturas (";
		    $sql.= " id_empresa, fecha, hora, numero, comprobante, ";
		    $sql.= " id_cliente, id_tipo_comprobante, total, subtotal, ";
		    $sql.= " tipo_pago, estado, hash, id_referencia, numero_referencia, ";
		    $sql.= " id_punto_venta, punto_venta, comision_vendedor, id_vendedor ";
		    $sql.= ") VALUES (";
		    $sql.= " '$campania->id_empresa', '$hoy', '$hora', $numero_remito, '$comprobante', ";
		    $sql.= " '$campania->id_cliente', '999', $campania->costo, $campania->costo,  ";
		    $sql.= " 'C',1,'$hash', $campania->id, '$periodo', ";
		    $sql.= " '$pv->id', '$pv->numero', $comision_vendedor, $campania->id_vendedor ";
		    $sql.= ")";
		    $this->db->query($sql);
		    $id_remito = $this->db->insert_id();

		    // Insertamos las filas del remito
		    $j=0;
		    foreach($campania->piezas as $pieza) {

		    	$costo = ($j==0) ? $campania->costo : 0;
			    $sql = "INSERT INTO facturas_items (";
			    $sql.= " id_punto_venta, id_empresa, id_factura, cantidad, porc_iva, id_tipo_alicuota_iva, ";
			    $sql.= " neto, precio, nombre, iva, total_sin_iva, total_con_iva, orden ";
			    $sql.= ") VALUES (";
			    $sql.= " $pv->id, $campania->id_empresa, $id_remito, 1, 0, 0, ";
			    $sql.= " $costo, $costo, '$pieza->nombre', 0, $costo, $costo, $j ";
			    $sql.= ")";
			    $this->db->query($sql);
			    $j++;
		    }

		    $numero_remito++;

    	}

    } else {

    	// RECORREMOS EL RANGO DE FECHAS
	    foreach($range as $fecha) {

	    	$periodo = $fecha->format("Ym");
	    	if ($this->existe_remito($campania,$periodo,$pv) === TRUE) continue;

		    // Insertamos un nuevo comprobante que corresponde al periodo
		    $hoy = $fecha->format("Y-m-d");
		    $hora = date("H:i:s");
		    $comprobante = "R ".str_pad($pv->numero, 4 , "0", STR_PAD_LEFT)."-".str_pad($numero_remito, 8, "0", STR_PAD_LEFT);
		    $hash = md5($campania->id_empresa.$comprobante);

		    // Sirven al momento del pago, poder identificar que cuota se esta pagando
		    $sql = "INSERT INTO facturas (";
		    $sql.= " id_empresa, fecha, hora, numero, comprobante, ";
		    $sql.= " id_cliente, id_tipo_comprobante, total, subtotal, ";
		    $sql.= " tipo_pago, estado, hash, id_referencia, numero_referencia, ";
		    $sql.= " id_punto_venta, punto_venta, comision_vendedor, id_vendedor ";
		    $sql.= ") VALUES (";
		    $sql.= " '$campania->id_empresa', '$hoy', '$hora', $numero_remito, '$comprobante', ";
		    $sql.= " '$campania->id_cliente', '999', $campania->costo, $campania->costo,  ";
		    $sql.= " 'C',1,'$hash', $campania->id, '$periodo', ";
		    $sql.= " '$pv->id', '$pv->numero', $comision_vendedor, $campania->id_vendedor ";
		    $sql.= ")";
		    $this->db->query($sql);
		    $id_remito = $this->db->insert_id();

		    // Insertamos las filas del remito
		    $j=0;
		    foreach($campania->piezas as $pieza) {

		    	$costo = ($j==0) ? $campania->costo : 0;
			    $sql = "INSERT INTO facturas_items (";
			    $sql.= " id_punto_venta, id_empresa, id_factura, cantidad, porc_iva, id_tipo_alicuota_iva, ";
			    $sql.= " neto, precio, nombre, iva, total_sin_iva, total_con_iva, orden ";
			    $sql.= ") VALUES (";
			    $sql.= " $pv->id, $campania->id_empresa, $id_remito, 1, 0, 0, ";
			    $sql.= " $costo, $costo, '$pieza->nombre', 0, $costo, $costo, $j ";
			    $sql.= ")";
			    $this->db->query($sql);
			    $j++;
		    }

		    $numero_remito++;
	    }
	  }

    $this->db->query("UPDATE numeros_comprobantes SET ultimo = $numero_remito - 1 WHERE id_empresa = $campania->id_empresa AND id_tipo_comprobante = 999");
  }
	
	/**
	 * Obtiene los publicidades a partir de diferentes parametros
	 */
	function buscar($conf = array()) {
		
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$estado = isset($conf["estado"]) ? $conf["estado"] : "";
		$id_vendedor = isset($conf["id_vendedor"]) ? $conf["id_vendedor"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$order = isset($conf["order"]) ? $conf["order"] : "A.valida_desde DESC";
    $lunes = isset($conf["lunes"]) ? $conf["lunes"] : -1;
    $martes = isset($conf["martes"]) ? $conf["martes"] : -1;
    $miercoles = isset($conf["miercoles"]) ? $conf["miercoles"] : -1;
    $jueves = isset($conf["jueves"]) ? $conf["jueves"] : -1;
    $viernes = isset($conf["viernes"]) ? $conf["viernes"] : -1;
    $sabado = isset($conf["sabado"]) ? $conf["sabado"] : -1;
    $domingo = isset($conf["domingo"]) ? $conf["domingo"] : -1;
    $id_categoria = isset($conf["id_categoria"]) ? $conf["id_categoria"] : 0;
    $hora_desde = isset($conf["hora_desde"]) ? $conf["hora_desde"] : "";
    $hora_hasta = isset($conf["hora_hasta"]) ? $conf["hora_hasta"] : "";
		
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT P.*, ";
    $sql.= "  IF(P.estado != 'A',0,DATEDIFF(NOW(),P.valida_hasta)) AS dias_vencimiento, ";
    $sql.= "  IF(V.nombre IS NULL,'',V.nombre) AS vendedor, ";
    $sql.= "  IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
		$sql.= "  DATE_FORMAT(P.valida_desde,'%d/%m/%Y') AS valida_desde, ";
		$sql.= "  DATE_FORMAT(P.valida_hasta,'%d/%m/%Y') AS valida_hasta ";
		$sql.= "FROM pub_campanias P ";
		$sql.= " LEFT JOIN vendedores V ON (P.id_vendedor = V.id AND P.id_empresa = V.id_empresa) ";
		$sql.= " LEFT JOIN clientes C ON (P.id_cliente = C.id AND P.id_empresa = C.id_empresa) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND P.id_empresa = $id_empresa ";
		if (!empty($id_vendedor)) $sql.= "AND P.id_vendedor = $id_vendedor ";
		if (!empty($filter)) $sql.= "AND (P.nombre LIKE '%$filter%' OR C.nombre LIKE '%$filter%') ";
		if (!empty($estado) && $estado != "T") $sql.= "AND P.estado = '$estado' ";
		$sql.= "ORDER BY $order ";
		$sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    if ($id_empresa == 70) {
      $tipos_publicidades = array(
        "fullscreen"=>7,
        "mediana_destacada"=>13,
        "fija_medio"=>14,
        "fija_abajo"=>12,
      );
    }

    $salida = array();
    foreach($q->result() as $row) {

      if ($id_empresa == 70) {
        foreach($tipos_publicidades as $key => $value) { 
          // Contamos la cantidad de tipos de publicidades que tiene cada campaña
          $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
          $sql.= "FROM pub_piezas PP ";
          $sql.= "WHERE PP.id_empresa = $row->id_empresa ";
          $sql.= "AND PP.id_campania = $row->id ";
          $sql.= "AND PP.activo = 1 ";
          $sql.= "AND PP.id_categoria = $value ";
          $qq = $this->db->query($sql);
          $rr = $qq->row();
          $row->{$key} = $rr->cantidad;
        }
      }
      
      if ($id_categoria != 0 || $lunes != -1 || $martes != -1 || $miercoles != -1 || $jueves != -1 || $viernes != -1 || $sabado != -1 || $domingo != -1 || !empty($hora_desde) || !empty($hora_hasta)) {
        $sql = "SELECT * ";
        $sql.= "FROM pub_piezas PP ";
        $sql.= "WHERE PP.id_empresa = $row->id_empresa ";
        $sql.= "AND PP.id_campania = $row->id ";
        $sql.= "AND PP.activo = 1 ";
        if ($id_categoria != 0) $sql.= "AND PP.id_categoria = $id_categoria ";
        if ($lunes == 1) $sql.= "AND PP.lunes = 1 ";
        if ($martes == 1) $sql.= "AND PP.martes = 1 ";
        if ($miercoles == 1) $sql.= "AND PP.miercoles = 1 ";
        if ($jueves == 1) $sql.= "AND PP.jueves = 1 ";
        if ($viernes == 1) $sql.= "AND PP.viernes = 1 ";
        if ($sabado == 1) $sql.= "AND PP.sabado = 1 ";
        if ($domingo == 1) $sql.= "AND PP.domingo = 1 ";
        if (!empty($hora_desde) && !empty($hora_hasta)) {
          $sql.= "AND (";
          $sql.= " (PP.hora_desde_1 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_1) OR ";
          $sql.= " (PP.hora_desde_2 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_2) OR ";
          $sql.= " (PP.hora_desde_3 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_3) OR ";
          $sql.= " (PP.hora_desde_4 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_4) OR ";
          $sql.= " (PP.hora_desde_5 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_5) OR ";
          $sql.= " (PP.hora_desde_6 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_6) OR ";
          $sql.= " (PP.hora_desde_7 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_7) OR ";
          $sql.= " (PP.hora_desde_8 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_8) OR ";
          $sql.= " (PP.hora_desde_9 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_9) OR ";
          $sql.= " (PP.hora_desde_10 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_10) OR ";
          $sql.= " (PP.hora_desde_11 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_11) OR ";
          $sql.= " (PP.hora_desde_12 <= '$hora_desde' AND '$hora_hasta' <= PP.hora_hasta_12) OR ";
          $sql.= " (PP.hora_desde_1 = '00:00:00' AND PP.hora_desde_2 = '00:00:00' AND PP.hora_desde_3 = '00:00:00' AND PP.hora_desde_4 = '00:00:00' AND PP.hora_desde_5 = '00:00:00' AND PP.hora_desde_6 = '00:00:00' AND PP.hora_desde_7 = '00:00:00' AND PP.hora_desde_8 = '00:00:00' AND PP.hora_desde_9 = '00:00:00' AND PP.hora_desde_10 = '00:00:00' AND PP.hora_desde_11 = '00:00:00' AND PP.hora_desde_12 = '00:00:00') ";
          $sql.= ")";
        }
        $qq = $this->db->query($sql);
        if ($qq->num_rows()>0) $salida[] = $row;
      } else {
        $salida[] = $row;
      }
    }
		return array(
      "results"=>$salida,
      "total"=>$total->total,
		);
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del publicidad
		$id = (int)$id;
		$sql = "SELECT P.*, ";
		$sql.= "  DATE_FORMAT(P.valida_desde,'%d/%m/%Y') AS valida_desde, ";
		$sql.= "  DATE_FORMAT(P.valida_hasta,'%d/%m/%Y') AS valida_hasta ";
		$sql.= "FROM pub_campanias P ";
		$sql.= "WHERE P.id = $id ";
		$sql.= "AND P.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$r = $q->row();
    if ($r !== FALSE) {
      $this->load->model("Pieza_Model");
      $r->piezas = $this->Pieza_Model->get_list(array(
        "id_campania"=>$r->id,
        "id_empresa"=>$id_empresa,
        "estado"=>"",
      ));  
      if ($r->piezas === FALSE) $r->piezas = array();
    }
		return $r;
	}

}