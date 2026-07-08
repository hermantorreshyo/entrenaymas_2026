<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Recibos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Recibo_Model', 'modelo');
  }

  // TOMA LOS PAGOS DE CLIENTES EN CUENTA CORRIENTE Y MARCA LAS FACTURAS COMO PAGADA
  function acomodar() {
    $id_empresa = 641;
    $sql = "SELECT * FROM facturas_pagos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $this->modelo->marcar_pagada(array(
        "id_empresa"=>$id_empresa,
        "id_factura"=>$r->id_factura,
        "id_punto_venta"=>$r->id_punto_venta,
      ));
    }
    echo "TERMINO 1";
  }

  private function get_params() {     
    $conf = array();
    $id_empresa = $this->input->get("id_empresa");
    $conf["id_empresa"] = ($id_empresa !== FALSE) ? $id_empresa : parent::get_empresa();
    $conf["order_by"] = parent::get_get("order_by","");
    $conf["order"] = parent::get_get("order","");
    $conf["limit"] = parent::get_get("limit",0);
    $conf["offset"] = parent::get_get("offset",10);
    $conf["id_cliente"] = parent::get_get("id_cliente",0);
    $conf["id_sucursal"] = parent::get_get("id_sucursal",0);
    $conf["filter"] = parent::get_get("filter","");
    $conf["id_usuario"] = parent::get_get("id_usuario",0);
    $conf["estado"] = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $this->load->helper("fecha_helper");
    $desde = parent::get_get("desde","");
    if (!empty($desde)) $conf["desde"] = fecha_mysql($desde);
    $hasta = parent::get_get("hasta","");
    if (!empty($hasta)) $conf["hasta"] = fecha_mysql($hasta);
    return $conf;
  }

  function consulta() {
    $conf = $this->get_params();
    $salida = $this->modelo->buscar($conf);
    echo json_encode($salida);
  }
	
  function next() {
    $id_empresa = parent::get_empresa();
    $q = $this->db->query("SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero FROM facturas WHERE tipo = 'P' AND id_empresa = $id_empresa");
    $r = $q->row();
    echo json_encode(array(
      "numero"=>((int)$r->numero + 1)
    ));
  }

  function imprimir_recibo($id,$id_punto_venta=0) {

    $this->load->helper("numero_letra_helper");
    $id_empresa = parent::get_empresa();
    $row = $this->modelo->get($id,array(
      "id_empresa"=>$id_empresa,
      "id_punto_venta"=>$id_punto_venta,
    ));

    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($row->id_cliente);

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($row->id_empresa);

    $header = $this->load->view("reports/factura/header.php",null,true);

    $this->load->view('reports/recibo',array(
      "header"=>$header,
      "recibo"=>$row,
      "cliente"=>$cliente,
      "empresa"=>$empresa,
    ));
  }
            
	function insert() {
        
		$id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $array = $this->parse_put();
    $array->fecha = fecha_mysql($array->fecha);
    $array->total = -$array->total;
		$estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $array->id_punto_venta = (isset($array->id_punto_venta)) ? $array->id_punto_venta : 0;
    $array->id_sucursal = (isset($array->id_sucursal)) ? $array->id_sucursal : 0;
    $total_tarjetas = isset($array->total_tarjetas) ? $array->total_tarjetas : 0;
    $total_cheques = isset($array->total_cheques) ? $array->total_cheques : 0;
    $comprobante = "P ".str_pad($array->numero,8,"0",STR_PAD_LEFT);

    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($array->id_cliente,$id_empresa,array(
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
		
		// Guardamos el recibo como una factura
    $sql = "INSERT INTO facturas (";
		$sql.= "  fecha,hora,numero,pago,cta_cte,";
		$sql.= "  tipo,id_cliente,id_empresa,tipo_pago,pagada,descuento,tarjeta,cheque,";
    $sql.= "  comprobante,id_usuario,efectivo,estado,vuelto, id_punto_venta, cotizacion_dolar, id_sucursal, observaciones, ";
    $sql.= "  retencion_suss, retencion_iva, retencion_otras, punto_venta ";
    if (isset($array->retencion_iibb)) $sql.= ", custom_7, custom_8 ";
		$sql.= ") VALUES(";
		$sql.= "  '$array->fecha','".date("H:i:s")."',$array->numero,";
		$sql.= "  $array->total,$array->total,'P',$array->id_cliente,$id_empresa,'C',1,'$array->descuento','$total_tarjetas','$total_cheques',";
    $sql.= "  '$comprobante',$array->id_usuario,$array->efectivo,$estado,$array->vuelto, $array->id_punto_venta, '$array->cotizacion_dolar', '$array->id_sucursal', '$array->observaciones', ";
    $sql.= "  '$array->retencion_suss', '$array->retencion_iva', '$array->retencion_otras', '$array->punto_venta' ";
    if (isset($array->retencion_iibb)) $sql.= ", '$array->retencion_iibb', '$array->retencion_ganancias' ";
    $sql.= ") ";
    $this->db->query($sql);
		$id_recibo = $this->db->insert_id();
		
		// Si el pago incluye CHEQUES DE TERCEROS
		if (sizeof($array->cheques)>0) {
			foreach($array->cheques as $cheque) {
				if ($cheque->id == 0) {
					// Cargamos el cheque
					$sql = "INSERT INTO cheques (";
					$sql.= " id_banco, numero, id_cliente, cliente, fecha_recibido, fecha_emision, fecha_cobro, ";
					$sql.= " importe, tipo, id_recibo, monto, titular, id_empresa, id_punto_venta ) VALUES (";
					$sql.= " $cheque->id_banco, $cheque->numero, $array->id_cliente, '$array->cliente', '$array->fecha', '$cheque->fecha_emision', '$cheque->fecha_cobro', ";
					$sql.= " $cheque->monto, 'T', $id_recibo, $cheque->monto, '$cheque->titular', $id_empresa, '$array->id_punto_venta' ";
					$sql.= ") ";
				} else {
					// Actualizamos el cheque
					$sql = "UPDATE cheques SET fecha_recibido = '$array->fecha', id_recibo = $id_recibo WHERE id_empresa = $id_empresa AND id = $cheque->id";	
				}
				$this->db->query($sql);
			}
		}

    $f_tar = date("Y-m-d H:i:s");

		// Si el pago incluye TARJETAS
		if (sizeof($array->tarjetas)>0) {
			foreach($array->tarjetas as $t) {
				$sql = "INSERT INTO cupones_tarjetas (id_empresa,lote,cupon,fecha,id_factura,importe,id_tarjeta,cuotas,id_punto_venta) VALUES(";
				$sql.= "$id_empresa,'$t->lote','$t->cupon','$f_tar',$id_recibo,'$t->importe','$t->id_tarjeta','$t->cuotas','$array->id_punto_venta')";
				$this->db->query($sql);
			}
		}
		
		// Si el pago incluye DEPOSITOS
    $this->load->model("Caja_Movimiento_Model");
    $observaciones_caja = $cliente->nombre." ".$comprobante;
    if (!empty($array->observaciones)) $observaciones_caja.= ". Obs: ".$array->observaciones;

		if (sizeof($array->depositos)>0) {
			foreach($array->depositos as $deposito) {
				// Guardamos los depositos
        $this->Caja_Movimiento_Model->ingreso(array(
          "id_empresa"=>$id_empresa,
          "id_factura"=>$id_recibo,
          "id_caja"=>$deposito->id_caja,
          "fecha"=>$array->fecha,
          "monto"=>$deposito->monto,
          "id_sucursal"=>$array->id_sucursal,
          "id_punto_venta"=>$array->id_punto_venta,
          "observaciones"=>$observaciones_caja,
        ));
        /*
				// TODO: La fecha del deposito puede ser distinta a la OP y a la actual
				$sql = "INSERT INTO depositos (id_empresa,id_recibo,id_cliente,id_cuenta_bancaria,fecha,monto,id_punto_venta) VALUES (";
				$sql.= "$id_empresa, $id_recibo , $array->id_cliente, $deposito->id_cuenta, '$f_tar', $deposito->monto, '$array->id_punto_venta' ) ";
				$this->db->query($sql);
        */
			}
		}
    if (sizeof($array->movimientos_efectivo)>0) {
      foreach($array->movimientos_efectivo as $ef) {
        // Guardamos los movimientos_efectivo
        if ($ef->monto < 0) {
          $monto = abs($ef->monto);
          $this->Caja_Movimiento_Model->egreso(array(
            "id_empresa"=>$id_empresa,
            "id_factura"=>$id_recibo,
            "id_caja"=>$ef->id_caja,
            "fecha"=>$array->fecha,
            "monto"=>$monto,
            "id_sucursal"=>$array->id_sucursal,
            "id_punto_venta"=>$array->id_punto_venta,
            "observaciones"=>$observaciones_caja,
          ));
        } else {
          $this->Caja_Movimiento_Model->ingreso(array(
            "id_empresa"=>$id_empresa,
            "id_factura"=>$id_recibo,
            "id_caja"=>$ef->id_caja,
            "fecha"=>$array->fecha,
            "monto"=>$ef->monto,
            "id_sucursal"=>$array->id_sucursal,
            "id_punto_venta"=>$array->id_punto_venta,
            "observaciones"=>$observaciones_caja,
          ));          
        }
      }
    }

		foreach($array->comprobantes as $comprobante) {

      // Insertamos el registro en la tabla de pagos
			$sql = "INSERT INTO facturas_pagos (id_empresa,id_pago,id_factura,monto,id_punto_venta) VALUES (";
			$sql.= "$id_empresa, $id_recibo, $comprobante->id, $comprobante->total, '$array->id_punto_venta') ";
			$this->db->query($sql);

      // Marcamos la factura como paga
      $this->modelo->marcar_pagada(array(
        "id_empresa"=>$id_empresa,
        "id_factura"=>$comprobante->id,
        "id_punto_venta"=>$array->id_punto_venta,
      ));
		}

		// Si es INMOVAR, entonces los pagos son sobre alquileres
		if ($array->id_proyecto == 3) {

			// TODO: SOLO SI EL PAGO DEL ALQUILER FUE COMPLETO
			foreach($array->comprobantes as $comprobante) {
				$sql = "SELECT * FROM facturas WHERE id = $comprobante->id ";
				$q_facturas = $this->db->query($sql);
				if ($q_facturas->num_rows() <= 0) break;
				$factura = $q_facturas->row();
				$sql = "UPDATE inm_alquileres_cuotas SET pagada = 1 ";
				$sql.= "WHERE numero = $factura->numero_referencia ";
				$sql.= "AND id_alquiler = $factura->id_referencia ";
				$this->db->query($sql);
			}

		}

    // Si es un recibo nuestro
    // Actualizamos el estado de la empresa
    if ($id_empresa == 936) {
      $this->load->model("Empresa_Model");
      $this->Empresa_Model->actualizar_pago_empresa(array(
        "id_empresa"=>$array->id_cliente,
      ));
    }
		
		/*
		// La variable $a_favor tiene el total del pago, que seria la plata a favor
		$a_favor = -$array->total; // El pago esta en positivo, lo cambiamos

		// Los comprobantes negativos los tomamos como plata a favor
		foreach($array->comprobantes as $comprobante) {
			$comprobante_total = ($comprobante->total + $comprobante->pago - $comprobante->total_pagado);
			if ($comprobante->negativo == 1) $a_favor += $comprobante_total;
		}

		// Procesamos todos los comprobantes que tenia el pago, para ponerle pagada
		foreach($array->comprobantes as $comprobante) {
			$comprobante_total = ($comprobante->total + $comprobante->pago - $comprobante->total_pagado);
			if ($comprobante->negativo == 1) $comprobante_total = -$comprobante_total;
			$resto = $a_favor - $comprobante_total;
			if ($resto >= 0) {
			} else if ($a_favor > 0) {
				// Guardamos el restito en la siguiente factura
				$sql = "INSERT INTO facturas_pagos (id_empresa,id_pago,id_factura,monto) VALUES (";
				$sql.= "$id_empresa, $id_recibo, $comprobante->id, $a_favor) ";
				$this->db->query($sql);
			} else {
				// El resto no llega a cubrir la factura, por lo tanto no tiene que pertenecer a ningun pago
			}
			$a_favor = $resto;
		}
		*/
    echo json_encode(array("error"=>0));
	}
    
	function borrar_recibo($id = null,$id_punto_venta = 0) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
		$id_empresa = parent::get_empresa();
    $this->db->query("UPDATE facturas F INNER JOIN facturas_pagos FP ON (F.id = FP.id_factura AND F.id_empresa = FP.id_empresa AND F.id_punto_venta = FP.id_punto_venta) SET F.pagada = 0 WHERE FP.id_empresa = $id_empresa AND FP.id_pago = $id AND FP.id_punto_venta = $id_punto_venta ");
    if ($id_empresa == 869) {
      // EN MOBEL eliminamos el cheque asi no se confunde
      $this->db->query("DELETE FROM cheques WHERE id_empresa = $id_empresa AND id_recibo = $id AND id_punto_venta = $id_punto_venta");
    } else {
      $this->db->query("UPDATE cheques SET fecha_recibido = '0000-00-00', id_recibo = 0 WHERE id_empresa = $id_empresa AND id_recibo = $id AND id_punto_venta = $id_punto_venta");  
    }		
    $this->db->query("DELETE FROM cajas_movimientos WHERE id_factura = $id AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta");
		$this->db->query("DELETE FROM depositos WHERE id_recibo = $id AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta");
    $this->db->query("DELETE FROM facturas_pagos WHERE id_pago = $id AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta");
    $this->db->query("DELETE FROM cupones_tarjetas WHERE id_factura = $id AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta");
		$this->db->query("DELETE FROM facturas WHERE id = $id AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta");
		echo json_encode(array());
	}	
    
}