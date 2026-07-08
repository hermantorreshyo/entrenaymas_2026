<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cheques_Propios extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Cheque_Model', 'modelo','id');
  }
    
    // Calcula los cheques que no se cobraron hasta determinada fecha
    function no_cobrados() {
  /*
select P.nombre, B.nombre, CH.numero, CH.monto,
DATE_FORMAT(fecha_emision,'%d/%m/%Y') AS fecha_emision,
DATE_FORMAT(fecha_cobro,'%d/%m/%Y') AS fecha_cobro
from cheques CH
INNER JOIN compras C ON (CH.id_orden_pago = C.id)
INNER JOIN proveedores P ON (C.id_proveedor = P.id)
INNER JOIN bancos B ON (CH.id_banco = B.id)
where tipo = 'P' and fecha_cobro < '2014-07-01' AND entregado = 1 AND anulado = 0 AND devuelto = 0 AND fecha_debitado = '0000-00-00'
ORDER BY CH.fecha_emision    
  */
    }
    
    
    function actualizar_fecha_debitado($id,$fecha='') {
    $this->load->helper("fecha_helper");
    if (empty($fecha)) $fecha = "0000-00-00";
    else $fecha = fecha_mysql(str_replace("-","/",$fecha));
    $this->db->where("id",$id);
    $this->db->update("cheques",array(
      "fecha_debitado"=>$fecha
    ));
    echo json_encode(array("error"=>0));
    }
    
    
    function detalle_deuda() {
  
    $total = 0;
    $sucursales = array(
      array("sucursal"=>0,"monto"=>0,"vencido"=>0,"no_vencido"=>0),
      array("sucursal"=>"Sucursal 1","monto"=>0,"vencido"=>0,"no_vencido"=>0),
      array("sucursal"=>"Sucursal 2","monto"=>0,"vencido"=>0,"no_vencido"=>0),
      array("sucursal"=>"Sucursal 3","monto"=>0,"vencido"=>0,"no_vencido"=>0),
      array("sucursal"=>"Sucursal 4","monto"=>0,"vencido"=>0,"no_vencido"=>0),
      array("sucursal"=>"Deposito","monto"=>0,"vencido"=>0,"no_vencido"=>0),
    );
    
    // Tomamos las ordenes de pago
    // que todavia tienen al menos un cheque
    // que no fue cancelado
    $sql = "SELECT * FROM compras C ";
    $sql.= "WHERE C.id_tipo_comprobante = -1 "; // OP
    $sql.= "AND EXISTS ( ";
    $sql.= "  SELECT * FROM cheques CH ";
    $sql.= "  WHERE CH.fecha_debitado = '0000-00-00' ";
    $sql.= "  AND CH.entregado = 1 ";
    $sql.= "  AND CH.anulado = 0 ";
    $sql.= "  AND CH.devuelto = 0 ";
    $sql.= "  AND CH.tipo = 'P' ";
    $sql.= "  AND CH.id_orden_pago = C.id ";  
    $sql.= ") ";
    $q = $this->db->query($sql);
    
    foreach($q->result() as $o) {
      
      // Tomamos todos los cheques de esa orden de pago
      $sql = "SELECT * ";
      $sql.= "FROM cheques CH ";
      $sql.= "WHERE CH.id_orden_pago = $o->id ";
      $sql.= "AND CH.entregado = 1 ";
      $sql.= "AND CH.anulado = 0 ";
      $sql.= "AND CH.devuelto = 0 ";
      $sql.= "AND CH.tipo = 'P' ";
      $qq = $this->db->query($sql);
      //$total_cancelado = 0;
      //$total_cancelado += abs($o->efectivo);
      //$total_cancelado += abs($o->ret_ing_brutos);
      //$total_cancelado += abs($o->ret_ganancias);
      //$total_cancelado += abs($o->descuento);
      //$total_cancelado += abs($o->rotura);
      $deuda = 0;
      $deuda_vencida = 0;
      $deuda_no_vencida = 0;
      foreach($qq->result() as $ch) {
      // Ya fue cancelado el cheque
      if ($ch->fecha_debitado != '0000-00-00') {
        //$total_cancelado = $total_cancelado + $ch->monto;
      } else {
        $deuda += $ch->monto;
        if ($ch->fecha_cobro <= date("Y-m-d")) {
        // Ya esta vencido
        $deuda_vencida += $ch->monto;
        } else {
        // Aun no esta vencido
        $deuda_no_vencida += $ch->monto;
        }
      }
      }
      
      // La deuda es el total de la orden de pago
      // menos lo que ya fue cancelado
      //$deuda = (abs($o->total_general) - $total_cancelado);
      
      // Calculamos la proporcion de deuda
      $porc_deuda = $deuda / abs($o->total_general);
      $porc_deuda_vencida = $deuda_vencida / abs($o->total_general);
      $porc_deuda_no_vencida = $deuda_no_vencida / abs($o->total_general);
      //echo $porc_deuda."<br/>";
      
      // Ahora tomamos las facturas de esa orden de pago
      $sql = "SELECT * ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_orden_pago = $o->id ";
      $sql.= "AND compra_real = 1 ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $f) {
      $sucursales[$f->id_sucursal]["monto"] += (float) $f->total_general * $porc_deuda;
      $sucursales[$f->id_sucursal]["vencido"] += (float) $f->total_general * $porc_deuda_vencida;
      $sucursales[$f->id_sucursal]["no_vencido"] += (float) $f->total_general * $porc_deuda_no_vencida;
      }
      $total = $total + $deuda;
    }
    // echo $total;
    echo json_encode(array("datos"=>$sucursales));
    }
    
    
    function emitidos_no_cobrados($fecha=0,$id_empresa=1) {
    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql(str_replace("-","/",$fecha));
    $sql = "SELECT B.nombre, SUM(C.monto) AS total FROM cheques C ";
    $sql.= "INNER JOIN bancos B ON (B.id = C.id_banco) ";
    $sql.= "where C.fecha_emision <= '$fecha' ";
    $sql.= "and C.fecha_cobro > '$fecha' ";
    $sql.= "and (C.fecha_debitado > '$fecha' OR C.fecha_debitado = '0000-00-00') ";
    $sql.= "and C.anulado = 0 ";
    $sql.= "and C.devuelto = 0 ";
    $sql.= "and C.entregado = 1 ";
    $sql.= "and C.id_empresa = $id_empresa ";
    $sql.= "and tipo = 'P' ";
    $sql.= "GROUP BY B.id ";
    $q = $this->db->query($sql);
    echo json_encode(array(
      "datos"=>$q->result()
    ));
  }
    
    
    function emitidos($fecha_desde=0,$fecha_hasta=0) {
  
    $this->load->helper("fecha_helper");
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    
    // Obtenemos los bancos con los que se trabaja
    $sql = "SELECT DISTINCT B.id AS id_banco, ";
    $sql.= "B.nombre ";
    $sql.= "FROM cheques C INNER JOIN bancos B ON (C.id_banco = B.id) ";
    $sql.= "WHERE tipo = 'P' ";
    $q_bancos = $this->db->query($sql);
    $data = array(
      "datos"=>array()
    );
    
    foreach($q_bancos->result() as $banco) {
      $emitido = new stdClass();
      $emitido->total = $this->modelo->emitidos($fecha_desde,$fecha_hasta,$banco->id_banco);
      $emitido->nombre = $banco->nombre;
      $data["datos"][] = $emitido;
    }
    echo json_encode($data);
    }
    
    
    function saldos($fecha=0,$fecha_hasta=0,$empresa = 0) {
  
    $this->load->helper("fecha_helper");
    
    if (empty($fecha)) {
      $fecha = date('Y-m-d');
    } else {
      $fecha = fecha_mysql(str_replace("-","/",$fecha));
    }
    
    if (!empty($fecha_hasta)) {
      $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    }
    
    // Obtenemos los bancos con los que se trabaja
    $sql = "SELECT DISTINCT B.id AS id_banco, ";
    $sql.= "B.nombre ";
    $sql.= "FROM cheques C INNER JOIN bancos B ON (C.id_banco = B.id) ";
    $sql.= "WHERE tipo = 'P' ";
    $q_bancos = $this->db->query($sql);
    $data = array(
      "datos"=>array()
    );
    
    
    // SI CONSULTAMOS POR UNA UNICA FECHA
    if (empty($fecha_hasta)) {
      
      // El tipo de consulta es adeudado a una fecha
      $data["tipo_consulta"] = "A";
      
      foreach($q_bancos->result() as $banco) {
        
        $desde = date("Y-m-d", strtotime("-1 months"));
      
        // Obtenemos lo adeudado hasta la fecha
        $sql = "SELECT ";
        $sql.= "  IF(SUM(monto) IS NULL,0,SUM(monto)) AS total ";
        $sql.= "FROM cheques ";
        $sql.= "WHERE tipo = 'P' ";
        $sql.= "AND anulado = 0 ";
        $sql.= "AND devuelto = 0 ";
        $sql.= "AND entregado = 1 ";
        $sql.= "AND id_banco = $banco->id_banco ";
        $sql.= "AND fecha_emision <= '$fecha' ";
        $sql.= "AND fecha_cobro <= '$fecha' "; // Ya estaria para cobrar
        $sql.= "AND (fecha_debitado = '0000-00-00' OR fecha_debitado >= '$fecha') ";
        $sql.= "AND fecha_cobro > '$desde' ";
        if (!empty($empresa)) $sql.= "AND id_empresa = $empresa ";
        $q_adeudado_hasta = $this->db->query($sql);
        $adeudado = $q_adeudado_hasta->row();
        $banco->sql = $sql;
        $banco->adeudado_hasta_fecha = $adeudado->total;
        
        // Obtenemos lo adeudado desde la fecha y en total
        $sql = "SELECT ";
        $sql.= "  IF(SUM(monto) IS NULL,0,SUM(monto)) AS total ";
        $sql.= "FROM cheques ";
        $sql.= "WHERE tipo = 'P' ";
        $sql.= "AND anulado = 0 ";
        $sql.= "AND devuelto = 0 ";
        $sql.= "AND entregado = 1 ";
        $sql.= "AND id_banco = $banco->id_banco ";
        $sql.= "AND fecha_cobro > '$fecha' ";
        $sql.= "AND (fecha_debitado = '0000-00-00' OR fecha_debitado > '$fecha') ";
        $sql.= "AND fecha_emision <= '$fecha' ";
        if (!empty($empresa)) $sql.= "AND id_empresa = $empresa ";
        $q_adeudado_hasta2 = $this->db->query($sql);
        $adeudado2 = $q_adeudado_hasta2->row();
        $banco->adeudado_desde_fecha = $adeudado2->total;
        
        // Sumamos toda la deuda
        $banco->total_adeudado = $adeudado->total + $adeudado2->total;
            
        $data["datos"][] = $banco;
      
      }
      
    } else {
      
      // SI CONSULTAMOS ENTRE DOS FECHAS
    
      // El tipo de consulta es debitado entre las dos fechas
      $data["tipo_consulta"] = "D";
    
      foreach($q_bancos->result() as $banco) {
      
        // Obtenemos lo debitado entre las dos fechas
        $sql = "SELECT ";
        $sql.= "  IF(SUM(monto) IS NULL,0,SUM(monto)) AS total ";
        $sql.= "FROM cheques ";
        $sql.= "WHERE tipo = 'P' ";
        $sql.= "AND anulado = 0 ";
        $sql.= "AND devuelto = 0 ";
        $sql.= "AND entregado = 1 ";    
        $sql.= "AND id_banco = $banco->id_banco ";
        $sql.= "AND '$fecha' <= fecha_debitado ";
        $sql.= "AND fecha_debitado <= '$fecha_hasta' ";
        $q_debitado = $this->db->query($sql);
        $debitado = $q_debitado->row();
        $banco->debitado = $debitado->total;
        
        $data["datos"][] = $banco;
      }
      
    }
    
    echo json_encode($data);
    }
    
    
    /**
     * @param ver
     *   0: Muestra todo
     *   1: Muestra lo que todavia no fue cobrado
     *   2: Muestra lo que ya se cobro
     */
    function listado_cheques($fecha_desde,$fecha_hasta,$id_proveedor=0,$id_banco=0,$ver=0,$id_empresa=0) {
  
  $this->load->helper("fecha_helper");
  
        // Acomodamos los datos de entrada
        $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
        $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    
        $data = array();
    $sql = "SELECT ";
    $sql.= "  CH.id AS id, ";
    $sql.= "  CH.monto AS monto, ";
    $sql.= "  CH.tipo AS tipo, ";
    $sql.= "  CH.numero AS numero, ";
    $sql.= "  CH.entregado AS entregado, ";
    $sql.= "  CH.anulado AS anulado, ";
    $sql.= "  CH.devuelto AS devuelto, ";
    $sql.= "  CH.id_banco AS id_banco, ";  
    $sql.= "  B.color AS color, ";
    $sql.= "  B.nombre AS banco, ";
    $sql.= "  P.nombre AS proveedor, ";
    $sql.= "  IF(fecha_emision='0000-00-00','',DATE_FORMAT(fecha_emision,'%d/%m/%Y')) AS fecha_emision, ";
    $sql.= "  IF(fecha_cobro='0000-00-00','',DATE_FORMAT(fecha_cobro,'%d/%m/%Y')) AS fecha_cobro, ";
    $sql.= "  IF(fecha_debitado='0000-00-00','',DATE_FORMAT(fecha_debitado,'%d/%m/%Y')) AS fecha_debitado ";
    $sql.= "FROM cheques CH ";
    $sql.= "INNER JOIN bancos B ON (CH.id_banco = B.id) ";
    $sql.= "INNER JOIN compras C ON (CH.id_orden_pago = C.id) ";
    $sql.= "INNER JOIN proveedores P ON (C.id_proveedor = P.id) ";
    $sql.= "WHERE tipo = 'P' ";
    if ($id_proveedor != 0) $sql.= "AND P.id = $id_proveedor ";
    if ($id_banco != 0) $sql.= "AND B.id = $id_banco ";
    
    if (!empty($id_empresa)) {
      $sql.= "AND C.id_empresa = $id_empresa ";
    }
    
    // Muestra lo que todavia no fue cobrado
    if ($ver == 1) {
      $sql.= "AND fecha_debitado = '0000-00-00' ";
    }
    // Muestra todo lo que ya se cobro
    if ($ver == 2) {
      $sql.= "AND fecha_debitado != '0000-00-00' ";
    }  
    
    $sql.= "AND '$fecha_desde' <= CH.fecha_cobro AND CH.fecha_cobro <= '$fecha_hasta' ";
    $sql.= "ORDER BY CH.fecha_cobro ASC, CH.id_banco ASC ";
      
      $query = $this->db->query($sql);
    $data["datos"] = $query->result();
    echo json_encode($data);
    }
  

  function get_by_date($agrupado = "C") {
      
    // fecha_desde = principio de mes
    // fecha_hasta = fin de mes
    $fecha_desde = $this->input->get("start");
    $fecha_hasta = $this->input->get("end");
    $agrupado = parent::get_get("agrupado","C");
    $mostrar_tipo = parent::get_get("mostrar_tipo",0);
    $id_sucursal = parent::get_get("id_sucursal",0);
    $tipo = parent::get_get("tipo","P");
    $id_empresa = parent::get_empresa();
    $cheques = array();

    // Cheques por dia todos separados
    if ($agrupado == "C") {
      $sql = "SELECT CH.id, CH.anulado, ";
      $sql.= " CH.numero, CH.id_orden_pago, ";
      $sql.= " IF(CH.fecha_debitado = '0000-00-00','#28b492','#a94442') AS backgroundColor, ";
      $sql.= " IF(CH.fecha_debitado = '0000-00-00','#28b492','#a94442') AS borderColor, ";
      $sql.= " CH.monto, ";
      $sql.= " CH.fecha_cobro ";
      $sql.= "FROM cheques CH ";
      $sql.= "WHERE CH.tipo = '$tipo' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "AND CH.fecha_cobro >= '$fecha_desde' AND '$fecha_hasta' >= CH.fecha_cobro ";
      $sql.= "AND CH.fecha_cobro != '0000-00-00' ";      
      $sql.= "AND CH.devuelto = 0 "; // Que no fue devuelto
      $sql.= "AND CH.entregado = 1 "; // Que fue entregado
      if ($mostrar_tipo == "D") {
        // Solo debitados
        $sql.= "AND CH.fecha_debitado != '0000-00-00' ";
      } else if ($mostrar_tipo == "N") {
        // No debitados
        $sql.= "AND CH.fecha_debitado = '0000-00-00' ";
      }
      if ($mostrar_tipo == "A") $sql.= "AND CH.anulado = 1 ";
      else $sql.= "AND CH.anulado = 0 ";
      $query = $this->db->query($sql);
      foreach($query->result() as $m) {
        $m->title = $m->numero." - $ ".$m->monto;
        $m->allDay = true;
        $m->start = $m->fecha_cobro;

        // Si el cheque fue anulado, lo ponemos en negro
        if ($m->anulado == 1) {
          $m->title .= " (ANULADO)";
          $m->backgroundColor = "#000000";
          $m->borderColor = "#000000";
        }
        $cheques[] = $m;
      }

    // Cheques agrupados por dia
    } else if ($agrupado == "D") {

      $sql = "SELECT 0 AS id, 0 AS anulado, ";
      $sql.= " '' AS numero, 0 AS id_orden_pago, ";
      $sql.= " '#28b492' AS backgroundColor, ";
      $sql.= " '#28b492' AS borderColor, ";
      $sql.= " SUM(CH.monto) AS monto, ";
      $sql.= " CH.fecha_cobro ";
      $sql.= "FROM cheques CH ";
      $sql.= "WHERE CH.tipo = '$tipo' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "AND CH.fecha_cobro >= '$fecha_desde' AND '$fecha_hasta' >= CH.fecha_cobro ";
      $sql.= "AND CH.fecha_cobro != '0000-00-00' ";
      $sql.= "AND CH.devuelto = 0 "; // Que no fue devuelto
      $sql.= "AND CH.entregado = 1 "; // Que fue entregado
      if ($mostrar_tipo == "D") {
        // Solo debitados
        $sql.= "AND CH.fecha_debitado != '0000-00-00' ";
      } else if ($mostrar_tipo == "N") {
        // No debitados
        $sql.= "AND CH.fecha_debitado = '0000-00-00' ";
      }
      if ($mostrar_tipo == "A") $sql.= "AND CH.anulado = 1 ";
      else $sql.= "AND CH.anulado = 0 ";
      $sql.= "GROUP BY CH.fecha_cobro ";
      $query = $this->db->query($sql);
      foreach($query->result() as $m) {
        $m->title = "$ ".$m->monto;
        $m->allDay = true;
        $m->start = $m->fecha_cobro;
        $cheques[] = $m;
      }

    // Cheques agrupados por semana
    } else if ($agrupado == "S") {

      $sql = "SELECT 0 AS id, 0 AS anulado, ";
      $sql.= " '' AS numero, 0 AS id_orden_pago, ";
      $sql.= " '#28b492' AS backgroundColor, ";
      $sql.= " '#28b492' AS borderColor, ";
      $sql.= " SUM(CH.monto) AS monto, ";
      $sql.= " CH.fecha_cobro ";
      $sql.= "FROM cheques CH ";
      $sql.= "WHERE CH.tipo = '$tipo' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "AND CH.fecha_cobro >= '$fecha_desde' AND '$fecha_hasta' >= CH.fecha_cobro ";
      $sql.= "AND CH.fecha_cobro != '0000-00-00' ";
      $sql.= "AND CH.anulado = 0 "; // Que no este anulado
      $sql.= "AND CH.devuelto = 0 "; // Que no fue devuelto
      $sql.= "AND CH.entregado = 1 "; // Que fue entregado
      $sql.= "GROUP BY YEARWEEK(CH.fecha_cobro) ";
          
      $query = $this->db->query($sql);
      foreach($query->result() as $m) {
        $m->fecha_cobro = $m->fecha_cobro;
        $m->title = "$ ".$m->monto;
        $m->allDay = true;
        $desde = new DateTime($m->fecha_cobro);
        $desde->modify("monday this week");
        $m->start = $desde->format("Y-m-d");
        $hasta = new DateTime($m->fecha_cobro);
        $hasta->modify("monday next week");
        $m->end = $hasta->format("Y-m-d");
        $cheques[] = $m;
      }

    }

    echo json_encode($cheques);
  }  
  
    
    function get_by_banco($id_banco = 0,$id_empresa = 0) {
      
  $lista = $this->modelo->get_all(array(
      "id_banco"=>$id_banco,
      "id_empresa"=>$id_empresa,
      "tipo"=>'P',
      "entregado"=>0,
  ));
  if (!$lista) $lista = array();
  
  $total = $this->modelo->count_all(array(
      "id_banco"=>$id_banco,
      "id_empresa"=>$id_empresa,
      "tipo"=>'P',
      "entregado"=>0,
  ));
  
  $salida = array(
      "total"=> $total,
      "results"=>$lista
  );
  echo json_encode($salida);
    }
  
    
    
    function get($id) {

        // Obtenemos todos los registros
        if ($id == "index") {
        
            $limit = $this->input->get("limit");
            $offset = $this->input->get("offset");
            $filter = $this->input->get("filter");
            $id_banco = $this->input->get("id_banco");
            $id_cliente = $this->input->get("id_cliente");
            $entregado = $this->input->get("entregado");
      $numero = $this->input->get("numero");
            $lista = $this->modelo->get_all(array(
    "limit"=>$limit,
    "offset"=>$offset,
    "id_banco"=>$id_banco,
    "tipo"=>'P',
    "entregado"=>$entregado,
    "id_cliente"=>$id_cliente,
    "numero"=>$numero,
      ));
            if (!$lista) $lista = array();
    
            // Total de lista
            $total = $this->modelo->count_all(array(
    "tipo"=>'P',
    "entregado"=>$entregado,
    "id_banco"=>$id_banco,
    "id_cliente"=>$id_cliente,
    "numero"=>$numero
      ));
            
            $suma = $this->modelo->sum_all(array(
    "tipo"=>'P',
    "entregado"=>$entregado,
    "id_banco"=>$id_banco,
    "id_cliente"=>$id_cliente,
    "numero"=>$numero    
      ));
            
            // Armamos la salida
            $salida = array(
                "_meta"=> array(
                    "suma" => $suma
                ),
                "total"=> $total,
                "results"=>$lista
            );
            echo json_encode($salida);
        } else {
            // Estamos obteniendo un elemento en particular
            echo json_encode($this->modelo->get($id));
        }
    }
    
    function insert() {
        $this->load->helper("fecha_helper");
        $array = $this->parse_put();
    unset($array->banco);
    unset($array->cliente);
        $array->fecha_emision = fecha_mysql($array->fecha_emision);
        $array->fecha_cobro = fecha_mysql($array->fecha_cobro);
        $array->tipo = 'P';
    $insert_id = $this->modelo->save($array);
    $salida = array("id"=>$insert_id);
    echo json_encode($salida);
    }

    function update($id) {
  // Si es 0, entonces lo insertamos
  if ($id == 0) { $this->insert($id); return; }
        
        $this->load->helper("fecha_helper");
  $array = $this->parse_put();
  unset($array->banco);
  unset($array->cliente);
        $array->fecha_emision = fecha_mysql($array->fecha_emision);
        $array->fecha_cobro = fecha_mysql($array->fecha_cobro);
        $array->tipo = 'P';
  $this->modelo->save($array);
        
        // TODO:
        // Si el cheque fue rechazado, debemos controlar si
        // esta en una orden de pago. Si lo está,
        // se debe generar una nota de débito del proveedor
        // automáticamente, por el monto del cheque rechazado.
        // De esa manera, la cuenta corriente queda consistente
        // Luego se generaría otro pago aparte, no se modifica
        // el pago realizado.
        
        $salida = array("id"=>$id);
  echo json_encode($salida);
    }
    
    
}