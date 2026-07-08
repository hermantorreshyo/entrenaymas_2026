<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Ordenes_Pago extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Orden_Pago_Model', 'modelo');
  }

  // TODO: Genera los registros en la tabla compras_pagos a partir de la tabla compras
  function generar_compras_pagos() {

    $this->db->query("DELETE FROM compras_pagos");

    // Seleccionamos las compras que tengan una OP
    $sql = "SELECT * FROM compras WHERE id_orden_pago != 0";
    $q = $this->db->query($sql);
    foreach($q->result() as $c) {
      $sql = "INSERT INTO compras_pagos (id_empresa, id_pago, id_factura, id_sucursal, monto) VALUES (";
      $sql.= "'$c->id_empresa', '$c->id_orden_pago', '$c->id', '$c->id_sucursal', '$c->total_general' ) ";
      $this->db->query($sql);
    }

    // Hay que actualizar el cancelado en compras
    $sql = "UPDATE compras SET cancelado = total_general WHERE pagada = 1 AND id_orden_pago != 0";
    $this->db->query($sql);
    echo "TERMINO";
  }

  function pendientes($id_proveedor = 0) {
    $pendientes = $this->modelo->pendientes($id_proveedor);
    $salida = array(
      "datos" => $pendientes
      );
    echo json_encode($salida);
  }

  function preparar($id = 0,$id_caja = 1) {

    $data = new stdClass();
    $data->fecha = date("Y-m-d H:i:s");
    $this->load->model("Caja_Movimiento_Model");

    // Obtenemos la orden de pago
    $op = $this->modelo->get($id);

    // Si la orden de pago tiene EFECTIVO
    /*
    if ($op->forma_pago == "E" && $op->efectivo != 0) {
        // Se debe descontar de la caja
        $data->id_concepto_caja = 1;
        if ($op->efectivo > 0) $data->salida = $op->efectivo;
        else $data->entrada = -$op->efectivo;
        $data->observaciones = "Efectivo $op->proveedor (OP Nro: $op->numero_2)";
        $this->Caja_Movimiento_Model->insert($data);
    }
    
    // Si la orden de pago tiene INTERDEPOSITO
    if ($op->forma_pago == "I" && $op->efectivo != 0) {
        // Se debe descontar de la caja
        $data->id_concepto_caja = 1;
        if ($op->efectivo > 0) $data->salida = $op->efectivo;
        else $data->entrada = -$op->efectivo;
        $data->observaciones = "Interdeposito $op->proveedor (OP Nro: $op->numero_2)";
        $this->Caja_Movimiento_Model->insert($data);
    }
    */
  }


  function entregar($id = 0,$fecha = "") {

    $this->load->helper("fecha_helper");
    if (empty($fecha)) $fecha = date("Y-m-d");
    else $fecha = fecha_mysql($fecha);
    
    // Obtenemos la orden de pago
    $op = $this->modelo->get($id);
    
    // Guardamos la fecha de entregado
    $sql = "UPDATE compras SET fecha_entregado = '$fecha' ";
    $sql.= "WHERE id = $id ";
    $q = $this->db->query($sql);

  }

  function imprimir($id) {
    $this->load->helper("numero_letra_helper");
    $row = $this->modelo->get($id);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($row->id_empresa);
    $template = ($empresa->id == 46) ? 'reports/orden_pago_2' : 'reports/orden_pago';
    $this->load->view($template,array(
      "orden_pago"=>$row,
      "empresa"=>$empresa
    ));
  }

  function imprimir_recibo($id) {
    $this->load->helper("numero_letra_helper");
    $row = $this->modelo->get($id);
    $this->load->view('reports/recibo',array(
      "recibo"=>$row
    ));
  }

  function get($id) {
    $row = $this->modelo->get($id);
    echo json_encode($row);
  }

  function imprimir_ret_ib($id,$simple = 0) {
    $row = $this->modelo->get($id);
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($row->id_empresa);        
    
    // La primera vez que mostramos el certificado de ganancias
    if ($row->numero_certificado_ret_ib == 0) {

        // Tenemos que calcular el numero de certificado
      $sql = "SELECT MAX(numero_certificado_ret_ib) AS numero ";
      $sql.= "FROM compras ";
      $q = $this->db->query($sql);
      $r = $q->row();
      if (is_null($r->numero)) $numero = 1;
      else $numero = $r->numero + 1;

        // Lo guardamos
      $sql = "UPDATE compras SET numero_certificado_ret_ib = $numero WHERE id = $id ";
      $this->db->query($sql);
      $row->numero_certificado_ret_ib = $numero;
    }
    if ($simple == 0) {
      $this->load->view('reports/ret_ing_brutos',array("orden_pago"=>$row,"empresa"=>$empresa));    
    } else {
      $this->load->view('reports/ret_ing_brutos_simple',array("orden_pago"=>$row,"empresa"=>$empresa));    
    }
  }


  function imprimir_ret_ganancias($id,$simple = 0,$empresa = 0) {

    $this->load->helper("fecha_helper");
    $row = $this->modelo->get($id);
    $id_empresa = parent::get_empresa();
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    // Obtenemos los datos del proveedor

    // La primera vez que mostramos el certificado de ganancias
    if ($row->numero_certificado_ret_ganancias == 0) {

      // Tenemos que calcular el numero de certificado
      $sql = "SELECT MAX(numero_certificado_ret_ganancias) AS numero ";
      $sql.= "FROM compras ";
      $sql.= "WHERE DATE_FORMAT(fecha,'%Y') = '".date("Y")."' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      $r = $q->row();
      if (is_null($r->numero)) $numero = 1;
      else $numero = $r->numero + 1;

      // Lo guardamos
      $sql = "UPDATE compras SET numero_certificado_ret_ganancias = $numero ";
      $sql.= "WHERE id = $id AND id_empresa = $id_empresa ";
      $this->db->query($sql);
      $row->numero_certificado_ret_ganancias = $numero;
    }

    $fecha_desde = date("Y-m-01", strtotime(fecha_mysql($row->fecha)));
    $fecha_hasta = date("Y-m-t", strtotime(fecha_mysql($row->fecha)));
    
    // Volvemos a calcular la retencion de ganancias
    $retencion = $this->ret_ganancias($row->id_proveedor,$fecha_desde,$fecha_hasta,$empresa);
    
    if ($row->tipo_proveedor == 1) {
      $this->load->view('reports/ret_ganancias',array(
        "empresa"=>$empresa,
        "orden_pago"=>$row,
        "total_neto"=>$retencion["total_neto"],
        "importe_retenido"=>$retencion["total_ret_ganancias"],
        "simple"=>$simple,
      ));		
    } else {
      $total_neto = 0;
      foreach($row->comprobantes as $c) {
        $total_neto = $total_neto + $c->total_neto;
      }
      $this->load->view('reports/ret_ganancias',array(
        "empresa"=>$empresa,
        "orden_pago"=>$row,
        "total_neto"=>$total_neto,
        "importe_retenido"=>abs($row->ret_ganancias),
        "simple"=>$simple,
      ));		
    }
  }

  function ret_ganancias($id_proveedor,$fecha_desde,$fecha_hasta) {

    $id_empresa = parent::get_empresa();
    $this->load->model("Proveedor_model");
    $proveedor = $this->Proveedor_model->get($id_proveedor);

    // Si al proveedor hay que aplicarle ganancias
    if ($proveedor->aplica_ret_ganancias == 1) {

        // Para que se aplique retencion de ganancias
        // el proveedor debe ser RESPONSABLE INSCRIPTO
        // De otra forma, no se debe aplicar
      if ($proveedor->id_tipo_iva == 1) {

            // SI ES UN PROVEEDOR DE MERCADERIA O ALQUILERES
        if ($proveedor->tipo_proveedor == 1 || $proveedor->tipo_proveedor == 2) {

                // Calculamos los pagos efectuados en el mes
          $sql = "SELECT * ";
          $sql.= "FROM compras CO ";
          $sql.= "WHERE CO.id_tipo_comprobante = -1 ";
          $sql.= "AND '$fecha_desde' <= CO.fecha ";
          $sql.= "AND CO.fecha <= '$fecha_hasta' ";
          $sql.= "AND CO.id_proveedor = $id_proveedor ";
          $sql.= "AND CO.id_empresa = $id_empresa ";
          $q = $this->db->query($sql);
          $suma_ret_ganancias = 0;
          $suma_neto = 0;
          foreach($q->result() as $orden_pago) {
            $sql = "SELECT * FROM compras CO ";
            $sql.= "WHERE id_orden_pago = $orden_pago->id ";
            $sql.= "AND id_tipo_comprobante IN (1,3,4) ";
            $sql.= "AND CO.id_empresa = $id_empresa ";
            $q_compras = $this->db->query($sql);
            foreach($q_compras->result() as $compra) {
              $suma_neto = $suma_neto + $compra->total_neto;
            }
            $suma_ret_ganancias = $suma_ret_ganancias + abs($orden_pago->ret_ganancias);
          }

          return array(
            "calcula"=>1,
            "tipo_proveedor"=>$proveedor->tipo_proveedor,
            "total_neto"=>$suma_neto,
            "total_ret_ganancias"=>$suma_ret_ganancias
            );

            // SI ES UN PROFESIONAL
        } elseif ($proveedor->tipo_proveedor == 3) {

                // Calculamos los efectuados en el mes
          $sql = "SELECT ";
          $sql.= " IF(SUM(total_iva) IS NULL,0,SUM(total_iva)) AS total_iva, ";
          $sql.= " IF(SUM(total_general) IS NULL,0,SUM(total_general)) AS total_general, ";
          $sql.= " IF(SUM(total_neto) IS NULL,0,SUM(total_neto)) AS total_neto ";
          $sql.= "FROM compras CO ";
          $sql.= "WHERE CO.id_tipo_comprobante != -1 ";
          $sql.= "AND '$fecha_desde' <= CO.fecha ";
          $sql.= "AND CO.fecha <= '$fecha_hasta' ";
          $sql.= "AND CO.id_proveedor = $id_proveedor ";
          if (!empty($empresa)) $sql.= "AND CO.id_empresa = $empresa ";
          $q = $this->db->query($sql);
          $row = $q->row();
          $base = $row->total_neto - 1200;
          if ($base <= 0) {
           $fijo = 0;
           $porc_excedente = 0;
           $sobre_excedente = 0;
         } else if ($base > 0 && $base <= 2000) {
           $fijo = 0;
           $porc_excedente = 10;
           $sobre_excedente = 0;
         } else if ($base > 2000 && $base < 4000) {
           $fijo = 200;
           $porc_excedente = 14;
           $sobre_excedente = 2000;			
         } else if ($base > 4000 && $base < 8000) {
           $fijo = 480;
           $porc_excedente = 18;
           $sobre_excedente = 4000;			
         } else if ($base > 8000 && $base < 14000) {
           $fijo = 1200;
           $porc_excedente = 22;
           $sobre_excedente = 8000;			
         } else if ($base > 14000 && $base < 24000) {
           $fijo = 2520;
           $porc_excedente = 26;
           $sobre_excedente = 14000;			
         } else if ($base > 24000 && $base < 40000) {
           $fijo = 5120;
           $porc_excedente = 28;
           $sobre_excedente = 24000;			
         } else {
           $fijo = 9600;
           $porc_excedente = 30;
           $sobre_excedente = 40000;						
         }

         $excedente = $base - $sobre_excedente;
         $retencion = round($fijo + ($excedente * $porc_excedente / 100),2);

         return array(
          "calcula"=>0,
          "total_neto"=>$row->total_neto,
          "total_ret_ganancias"=>$retencion
          );
       }
            // TODO: SI ES OTRO TIPO DE PROVEEDOR, NO CALCULAMOS GANANCIAS

      }

    }

    return array(
      "calcula"=>0,
      "total_neto"=>0,
      "total_ret_ganancias"=>0
    );

  }

  function recalcular_estados() {
    $id_empresa = 249;
    $sql = "SELECT * FROM compras WHERE id_tipo_comprobante = -1 AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql= "SELECT * FROM compras WHERE id_empresa = $id_empresa AND id_orden_pago = $row->id ";
      $qq = $this->db->query($sql);
      $estado = 0;
      foreach($qq->result() as $rr) {
        if ($rr->id_tipo_comprobante == 999) {
          $estado = 1;
        }
      }
      $sql = "UPDATE compras SET estado = $estado WHERE id_empresa = $id_empresa AND id = $row->id ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }



  /**
   * Calculamos la retencion de ganancias correspondiente
   * al pago que se va a realizar
   * @param $id_proveedor
   * @param $fecha
   */
  function calcular_ret_ganancias($id_proveedor,$fecha,$id_empresa=0) {

    $this->load->helper("fecha_helper");
    $a = explode("-",$fecha);
    $dia = $a[0];
    $mes = $a[1];
    $anio = $a[2];

      // Construimos la primera y ultima fecha del mes actual
    $fecha_desde = date("Y-m-01", strtotime("$anio-$mes-$dia"));
    $fecha_hasta = date("Y-m-t", strtotime("$anio-$mes-$dia"));

    $salida = $this->ret_ganancias($id_proveedor,$fecha_desde,$fecha_hasta,$id_empresa);
    echo json_encode($salida);
  }
    
    
  function insert() {

    $id_empresa = parent::get_empresa();
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;

    $id_usuario = $_SESSION["id"];
    $array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;    

    // Acomodamos los datos de entrada
    $this->load->helper("fecha_helper");
    $array->fecha = fecha_mysql($array->fecha);

    $array->id_sucursal = (isset($array->id_sucursal)) ? $array->id_sucursal : 0;

    $sql = "SELECT * FROM proveedores WHERE id = '$array->id_proveedor' AND id_empresa = $id_empresa LIMIT 0,1";
    $q = $this->db->query($sql);
    $proveedor = $q->row();

    // el ID_TIPO_COMPROBANTE es -1 para indicar
    // que es una ORDEN DE PAGO

    // Calculamos el proximo numero de la ORDEN DE PAGO
    $sql = "SELECT MAX(numero_2) AS numero ";
    $sql.= "FROM compras WHERE id_tipo_comprobante = -1 ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $array->numero = (is_null($r->numero) ? 1 : ($r->numero + 1));

    // Insertamos la orden de pago
    $sql = "INSERT INTO compras (";
    $sql.= "id_proveedor, fecha, id_tipo_comprobante, ";
    $sql.= "incluido_libro_iva, numero_1, numero_2, ";
    $sql.= "total_general, ";
    $sql.= "ret_ing_brutos, ";
    $sql.= "ret_ganancias, ";
    $sql.= "efectivo, ";
    $sql.= "descuento, ";
    $sql.= "rotura, ";
    $sql.= "observaciones, ";
    $sql.= "compra_real, ";
    $sql.= "id_sucursal, ";
    $sql.= "total_depositos, ";
    $sql.= "id_empresa, id_usuario ";
    $sql.= ") VALUES (";
    $sql.= "$proveedor->id, ";
    $sql.= "'$array->fecha', ";
    $sql.= "-1, "; // id_tipo_comprobante
    $sql.= "0, ";  // incluido_libro_iva
    $sql.= "0, ";  // numero_1
    $sql.= "$array->numero, ";  // numero_2
    $sql.= "-$array->total_valores_entregados, ";  // total_general
    $sql.= "-$array->ret_ing_brutos, ";
    $sql.= "-$array->ret_ganancias, ";
    $sql.= "-$array->efectivo, ";
    $sql.= "-$array->descuento, ";
    $sql.= "-$array->rotura, ";
    $sql.= "'$array->observaciones', ";
    $sql.= "$array->compra_real, "; // Compra Real
    $sql.= "$array->id_sucursal, ";
    $sql.= "$array->total_depositos, ";
    $sql.= "$array->id_empresa, '$array->id_usuario' ";
    $sql.= ")";
    $query = $this->db->query($sql);
    $id_orden_pago = $this->db->insert_id();

    // Insertamos los comprobantes a la orden de pago
    $estado = 0;
    foreach($array->comprobantes as $comprobante) {

      $sql = "UPDATE compras SET ";
      $sql.= " id_orden_pago = $id_orden_pago, ";
      $sql.= " cancelado = cancelado + $comprobante->por_cancelar ";
      //$sql.= " pagada = 1 ";
      $sql.= "WHERE id = $comprobante->id ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);

      // Guardamos cuando se pago de esa compra
      $sql = "INSERT INTO compras_pagos (id_empresa,id_pago,id_factura,monto,id_sucursal) VALUES (";
      $sql.= "'$array->id_empresa', '$id_orden_pago', '$comprobante->id', '$comprobante->por_cancelar', '$comprobante->id_sucursal') ";
      file_put_contents("log_ordenes_pago.txt", $sql.";\n", FILE_APPEND);
      $this->db->query($sql);

      // Si hay al menos un remito, la orden de pago pasa a estado 1
      if ($comprobante->id_tipo_comprobante == 999) {
        $estado = 1;
      }
    }

    // Actualizamos el estado de la orden de pago
    $sql = "UPDATE compras SET estado = $estado WHERE id = $id_orden_pago AND id_empresa = $array->id_empresa ";
    $this->db->query($sql);
      
    // Actualizamos los cheques
    $this->load->model("Cheque_Model");
    foreach($array->cheques as $cheque) {
      $cheque->entregado = 1;
      $cheque->id_orden_pago = $id_orden_pago;
      $cheque->id_proveedor = $proveedor->id;
      unset($cheque->fecha_debitado);
      $this->Cheque_Model->save($cheque);

      // Si es un cheque propio

    }

    $this->load->model("Caja_Movimiento_Model");
    $observaciones_caja = "OP $array->numero ".$proveedor->nombre;
    if (!empty($array->observaciones)) $observaciones_caja.= ". Obs: ".$array->observaciones;
    $id_concepto = (($id_empresa == 249 || $id_empresa == 868) ? 1231 : 0);

    // DEPOSITOS EN CUENTAS BANCARIAS
    foreach($array->depositos as $dep) {
      $this->Caja_Movimiento_Model->egreso(array(
        "id_empresa"=>$id_empresa,
        "id_orden_pago"=>$id_orden_pago,
        "id_caja"=>$dep->id_caja,
        "monto"=>$dep->monto,
        "fecha"=>$array->fecha." ".date("H:i:s"),
        "observaciones"=>$observaciones_caja,
        "id_sucursal"=>$array->id_sucursal,
        "id_usuario"=>$array->id_usuario,
        "estado"=>(($id_empresa == 249 || $id_empresa == 868) ? 1 : 0), // PENDIENTE
        "id_concepto"=>$id_concepto,
      ));
    }
    // EFECTIVO
    foreach($array->movimientos_efectivo as $dep) {
      if ($dep->monto < 0) {
        // Si el monto es negativo, lo ponemos como un INGRESO
        $monto = abs($dep->monto);
        $this->Caja_Movimiento_Model->ingreso(array(
          "id_empresa"=>$id_empresa,
          "id_orden_pago"=>$id_orden_pago,
          "id_caja"=>$dep->id_caja,
          "monto"=>$monto,
          "fecha"=>$array->fecha." ".date("H:i:s"),
          "observaciones"=>$observaciones_caja,
          "id_sucursal"=>$array->id_sucursal,
          "id_usuario"=>$array->id_usuario,
          "estado"=>(($id_empresa == 249 || $id_empresa == 868) ? 1 : 0), // PENDIENTE
          "id_concepto"=>$id_concepto,
        ));
      } else {
        // Sino el pago es un EGRESO
        $this->Caja_Movimiento_Model->egreso(array(
          "id_empresa"=>$id_empresa,
          "id_orden_pago"=>$id_orden_pago,
          "id_caja"=>$dep->id_caja,
          "monto"=>$dep->monto,
          "fecha"=>$array->fecha." ".date("H:i:s"),
          "observaciones"=>$observaciones_caja,
          "id_sucursal"=>$array->id_sucursal,
          "id_usuario"=>$array->id_usuario,
          "estado"=>(($id_empresa == 249 || $id_empresa == 868) ? 1 : 0), // PENDIENTE
          "id_concepto"=>$id_concepto,
        ));
      }
    }
    echo json_encode($array);
  }

  /**
   * Cuando se borra una orden de pago:
   * Los cheques que se hayan emitido a esa orden de pago
   * se deben anular o se vuelve a entregar
   */
  function delete($id = null) {
    $id_empresa = parent::get_empresa();
    $entregar = $this->input->get("entregar");
    $this->load->Model("Compra_Model");
    if ($entregar == "A") {
      // Los cheques se anulan
      $sql = "UPDATE cheques SET anulado = 1, entregado = 0, id_orden_pago = 0, id_proveedor = 0 WHERE id_orden_pago = $id AND id_empresa = $id_empresa ";
    } else {
      // Los cheques se vuelven a entregar
      $sql = "UPDATE cheques SET entregado = 0, id_orden_pago = 0, id_proveedor = 0 WHERE id_orden_pago = $id AND id_empresa = $id_empresa ";
    }
    $this->db->query($sql);

    // Recorremos los comprobantes que se pagaron en la OP
    // y restamos el saldo cancelado de cada factura
    $sql = "SELECT * FROM compras_pagos WHERE id_empresa = $id_empresa AND id_pago = $id ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $this->db->query("UPDATE compras SET cancelado = cancelado - $r->monto WHERE id_empresa = $id_empresa AND id = $r->id_factura ");
    }

    // Las facturas asociadas a esa orden de pago pasan a no estar pagadas
    $sql = "UPDATE compras SET pagada = 0, id_orden_pago = 0 WHERE id_orden_pago = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    $this->Compra_Model->delete($id);
    echo json_encode(array());
  }

}