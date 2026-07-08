<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Caja_Diaria extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Caja_Diaria_Model', 'modelo');
  }

  function controlar_ventas_subidas() {
    /*
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_post("id_empresa");
    $id_punto_venta = parent::get_post("id_punto_venta");
    $desde = parent::get_post("desde");
    $cantidad = parent::get_post("cantidad");

    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $body = "";
    mandrill_send(array(
      "to"=>"basile.matias99@gmail.com",
      "from"=>"no-reply@varcreative.com",
      "subject"=>"ERROR EN SUBIDA DE VENTAS",
      "body"=>$body,
    ));    
    */
  }

  function confirmar() {
    $id_empresa = parent::get_empresa();
    $id_caja_diaria = parent::get_get("id_caja_diaria");
    $id_punto_venta = parent::get_get("id_punto_venta");    
    $salida = $this->modelo->confirmar(array(
      "id_empresa"=>$id_empresa,
      "id_caja_diaria"=>$id_caja_diaria,
      "id_punto_venta"=>$id_punto_venta,
    ));
    echo json_encode($salida);
  }

  function recalcular() {
    $id_empresa = parent::get_empresa();
    $id_caja_diaria = parent::get_get("id_caja_diaria");
    $id_punto_venta = parent::get_get("id_punto_venta");
    $this->modelo->recalcular_caja(array(
      "id_empresa"=>$id_empresa,
      "id_caja_diaria"=>$id_caja_diaria,
      "id_punto_venta"=>$id_punto_venta,
    ));
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function imprimir($id,$id_punto_venta = 0) {
    $this->load->helper("fecha_helper");
    $caja = $this->modelo->obtener($id,array(
      "id_punto_venta"=>$id_punto_venta,
    ));
    if ($caja === FALSE || empty($caja)) {
      echo "La caja ya no existe.";
      exit();
    }
    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get($id_punto_venta);
    $id_empresa = $caja->id_empresa;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $header = $this->load->view("reports/caja/header",null,true);
    $datos = array(
      "caja"=>$caja,
      "empresa"=>$empresa,
      "punto_venta"=>$punto_venta,
      "header"=>$header,
    );
    $this->load->view("reports/caja/modelo1/caja.php",$datos);
  }

  function imprimir_agrupado($json="") {

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    $this->load->helper("fecha_helper");
    $json = json_decode(urldecode($json));

    $this->load->model("Punto_Venta_Model");

    $caja = new stdClass();
    $caja->fecha = "";
    $caja->efectivo = 0;
    $caja->efectivo_real = 0;
    $caja->tarjetas = 0;
    $caja->salida_efectivo = 0;
    $caja->efectivo_inicial = 0;
    $caja->total = 0;
    $caja->usuario = "";
    $caja->sucursal = "";
    $caja->retiro = 0;
    $caja->intereses = 0;
    $caja->pago_efectivo = 0;
    $caja->pago_cheques = 0;
    $caja->pago_tarjetas = 0;
    $caja->agrupado_tarjetas = array();
    $caja->departamentos = array();
    $caja->cheques = array();

    foreach($json as $obj) {
      $c = $this->modelo->obtener($obj->id,array(
        "id_punto_venta"=>$obj->id_punto_venta,
      ));
      if ($c === FALSE || empty($c)) {
        continue;
      }

      $punto_venta = $this->Punto_Venta_Model->get($c->id_punto_venta);

      $caja->fecha = $c->fecha;
      $caja->efectivo = $caja->efectivo + $c->efectivo;
      $caja->salida_efectivo = $caja->salida_efectivo + $c->salida_efectivo;
      $caja->retiro = $caja->retiro + $c->retiro;
      $caja->efectivo_real = $caja->efectivo_real + $c->efectivo_real;
      $caja->efectivo_inicial = $caja->efectivo_inicial + $c->efectivo_inicial;
      $caja->total = $caja->total + $c->total;
      $caja->pago_efectivo = $caja->pago_efectivo + $c->pago_efectivo;
      $caja->pago_cheques = $caja->pago_cheques + $c->pago_cheques;
      $caja->pago_tarjetas = $caja->pago_tarjetas + $c->pago_tarjetas;
      $caja->intereses = $caja->intereses + $c->intereses;
      $caja->tarjetas = array_merge($caja->tarjetas,$c->tarjetas);
      $caja->cheques = array_merge($caja->cheques,$c->cheques);

      // Agrupamos las tarjetas
      foreach($c->agrupado_tarjetas as $tar) {
        $encontro = FALSE;
        foreach($caja->agrupado_tarjetas as $tar_g) {
          if ($tar->tarjeta == $tar_g->tarjeta) {
            $tar_g->importe += $tar->importe;
            $tar_g->interes += $tar->interes;
            $tar_g->total += $tar->total;
            $tar_g->cantidad += $tar->cantidad;
            $encontro = TRUE;
            break;
          }
        }
        if (!$encontro) {
          $caja->agrupado_tarjetas[] = $tar;
        }
      }

      // Agrupamos los departamentos
      foreach($c->departamentos as $tar) {
        $encontro = FALSE;
        foreach($caja->departamentos as $tar_g) {
          if ($tar->id_departamento == $tar_g->id_departamento) {
            $tar_g->total += $tar->total;
            $tar_g->cantidad += $tar->cantidad;
            $encontro = TRUE;
            break;
          }
        }
        if (!$encontro) {
          $caja->departamentos[] = $tar;
        }
      }
    }
    $header = $this->load->view("reports/caja/header",null,true);
    $datos = array(
      "caja"=>$caja,
      "empresa"=>$empresa,
      "header"=>$header,
      "punto_venta"=>$punto_venta,
    );
    $this->load->view("reports/caja/modelo1/caja.php",$datos);
  }

  function get_by_pv($id,$id_punto_venta) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT CD.*, DATE_FORMAT(CD.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " IF(PV.nombre IS NULL,0,PV.nombre) AS punto_venta ";
    $sql.= "FROM caja_diaria CD ";
    $sql.= "LEFT JOIN puntos_venta PV ON (CD.id_punto_venta = PV.id AND CD.id_empresa = PV.id_empresa) ";
    $sql.= "WHERE CD.id_empresa = $id_empresa ";
    $sql.= "AND CD.id = $id AND CD.id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    $r = $q->row();

    if ($r->id_punto_venta != 0) {
      $this->load->model("Recibo_Model");
      $this->load->helper("fecha_helper");
      $sql = "SELECT * FROM com_usuarios_puntos_venta ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_punto_venta = $r->id_punto_venta ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $user) {
        $recibos = $this->Recibo_Model->buscar(array(
          "id_usuario"=>$user->id_usuario,
          "desde"=>fecha_mysql($r->fecha),
          "hasta"=>fecha_mysql($r->fecha),
          "id_empresa"=>$id_empresa,
          "offset"=>999999,
          "estado"=>1,
        ));
        foreach($recibos["results"] as $recibo) {
          $r->pago_efectivo += ((float)$recibo->efectivo);
          $r->pago_tarjetas += ((float)$recibo->total_tarjetas);
          $r->pago_cheques += ((float)$recibo->total_cheques);
        }
      }

      $this->load->model("Configuracion_Model");
      $r->confirmada = 0;
      if ($this->Configuracion_Model->es_local()==0) {
        $sql = "SELECT 1 FROM cajas_movimientos WHERE id_caja_diaria = $r->id AND id_punto_venta = $r->id_punto_venta AND id_empresa = $r->id_empresa ";
        $qq = $this->db->query($sql);
        if ($qq->num_rows() > 0) $r->confirmada = 1;
      }      
    }
    echo json_encode($r);
  }

  function buscar() {
    $this->load->helper("fecha_helper");
    $filter = ($this->input->get("texto") === FALSE) ? "" : $this->input->get("texto");
    $fecha = ($this->input->get("fecha") === FALSE) ? "" : $this->input->get("fecha");
    $desde = ($this->input->get("desde") === FALSE) ? "" : fecha_mysql($this->input->get("desde"));
    $hasta = ($this->input->get("hasta") === FALSE) ? "" : fecha_mysql($this->input->get("hasta"));
    $id_usuario = ($this->input->get("id_usuario") === FALSE) ? 0 : $this->input->get("id_usuario");
    $id_punto_venta = ($this->input->get("id_punto_venta") === FALSE) ? 0 : $this->input->get("id_punto_venta");
    $id_sucursal = ($this->input->get("id_sucursal") === FALSE) ? 0 : $this->input->get("id_sucursal");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "id_usuario"=>$id_usuario,
      "id_punto_venta"=>$id_punto_venta,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$fecha,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    ));
    echo json_encode($r);
  }

  function ver() {

    $id_empresa = parent::get_empresa();
    $id_sucursal = parent::get_get("id_sucursal",0);

    // Si hay una caja abierta
    $sql = "SELECT CD.*, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM caja_diaria CD ";
    $sql.= "INNER JOIN puntos_venta PV ON (CD.id_empresa = PV.id_empresa AND CD.id_punto_venta = PV.id) ";
    $sql.= "WHERE CD.id_empresa = $id_empresa AND CD.estado = 'A' ";
    if ($id_sucursal != 0) $sql.= "AND PV.id_sucursal = $id_sucursal ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

      $resultado = $q->row();

      // Sumamos los gastos
      $sql = "SELECT IF(SUM(total) IS NULL,0,SUM(total)) AS total ";
      $sql.= "FROM gastos ";
      $sql.= "WHERE id_caja_diaria = '$resultado->id' ";
      $sql.= "AND id_punto_venta = '$resultado->id_punto_venta' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      $r = $qq->row();
      $resultado->salida_efectivo = $r->total;

    } else {

      // Tomamos el ID del punto de venta por defecto
      $sql = "SELECT * FROM puntos_venta ";
      $sql.= "WHERE id_empresa = $id_empresa AND activo = 1 ";
      if ($id_sucursal != 0) $sql.= "AND id_sucursal = $id_sucursal ";
      $sql.= "ORDER BY por_default DESC ";
      $sql.= "LIMIT 0,1 ";
      $q_pv = $this->db->query($sql);
      $pv = $q_pv->row();
      $id_punto_venta = $pv->id;

      // Creamos el nuevo objeto
      $resultado = new stdClass();
      $resultado->id = 0;
      $resultado->punto_venta = $pv->nombre;
      $resultado->efectivo = 0;

      $resultado->efectivo_inicial = 0;
      if ($id_empresa != 249 && $id_empresa != 868) {
        // Tomamos como efectivo inicial el efectivo del cierre anterior
        $sql = "SELECT * FROM caja_diaria ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_punto_venta = $id_punto_venta ";
        $sql.= "ORDER BY fecha DESC, hora DESC ";
        $sql.= "LIMIT 0,1 ";
        $q_ant = $this->db->query($sql);
        if ($q_ant->row() > 0) {
          $ant = $q_ant->row();
          $resultado->efectivo_inicial = $ant->efectivo_real - $ant->retiro;
        }
      }
      $resultado->tarjetas = 0;
      $resultado->cheques = 0;
      $resultado->efectivo_real = 0;
      $resultado->efectivo = 0;
      $resultado->intereses = 0;
      $resultado->salida_efectivo = 0;
      $resultado->salida_cheques = 0;
      $resultado->estado = "X"; // No hay caja todavia            
      $resultado->fecha = date("d/m/Y");
      $resultado->hora = date("H:i");
      $resultado->id_punto_venta = $id_punto_venta;
      $resultado->uploaded = 0;
      $resultado->id_empresa = $id_empresa;
    }
    
    // Sumamos las entradas
    $sql = "SELECT ";
    $sql.= " SUM(IF(cta_cte < 0,efectivo-vuelto-cta_cte,efectivo-vuelto) * (IF(T.negativo = 1,-1,1)) ) AS efectivo, ";
    $sql.= " SUM(cheque * (IF(T.negativo = 1,-1,1))) AS cheques ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN tipos_comprobante T ON (F.id_tipo_comprobante = T.id) ";
    $sql.= "WHERE 1=1 ";
    if ($id_empresa == 1021) {
      $hoy = date("Y-m-d");
      $sql.= "AND F.fecha = '$hoy' ";
    }
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.id_caja_diaria = 0 ";
    $sql.= "AND F.tipo != 'P' ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_punto_venta = $resultado->id_punto_venta ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $resultado->efectivo = (is_null($r->efectivo)) ? 0 : $r->efectivo;
    $resultado->cheques = (is_null($r->cheques)) ? 0 : $r->cheques;

    // Sumamos los cupones de tarjetas
    $sql = "SELECT IF(SUM(CT.total) IS NULL,0,SUM(CT.total * (IF(T.negativo = 1,-1,1)))) AS tarjeta, ";
    $sql.= " IF(SUM(CT.interes) IS NULL,0,SUM(CT.interes * (IF(T.negativo = 1,-1,1)))) AS interes ";
    $sql.= "FROM cupones_tarjetas CT ";
    $sql.= "INNER JOIN facturas F ON (CT.id_factura = F.id AND CT.id_punto_venta = F.id_punto_venta AND CT.id_empresa = F.id_empresa) ";
    $sql.= "INNER JOIN tipos_comprobante T ON (F.id_tipo_comprobante = T.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND F.id_caja_diaria = 0 ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.tipo != 'P' ";
    $sql.= "AND CT.status = 0 "; // Que la tarjeta no este anulada
    $sql.= "AND F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_punto_venta = $resultado->id_punto_venta ";
    $sql.= "AND DATE_FORMAT(CT.fecha,'%d/%m/%Y') = '$resultado->fecha' ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $resultado->tarjetas = $r->tarjeta;
    $resultado->intereses = $r->interes;

    // Sumamos las entradas
    $sql = "SELECT ";
    $sql.= " SUM((efectivo-vuelto) * (IF(T.negativo = 1,-1,1))) AS efectivo, ";
    $sql.= " SUM(tarjeta * (IF(T.negativo = 1,-1,1))) AS tarjeta, ";
    $sql.= " SUM(cheque * (IF(T.negativo = 1,-1,1))) AS cheques ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN tipos_comprobante T ON (F.id_tipo_comprobante = T.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.id_caja_diaria = 0 ";
    $sql.= "AND F.tipo = 'P' ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_punto_venta = $resultado->id_punto_venta ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $resultado->pago_efectivo = (is_null($r->efectivo)) ? 0 : $r->efectivo;
    $resultado->pago_cheques = (is_null($r->cheques)) ? 0 : $r->cheques;
    $resultado->pago_tarjetas = (is_null($r->tarjeta)) ? 0 : $r->tarjeta;

    $this->load->model("Configuracion_Model");
    $resultado->confirmada = 0;
    if ($this->Configuracion_Model->es_local()==0) {
      $sql = "SELECT 1 FROM cajas_movimientos WHERE id_caja_diaria = $resultado->id AND id_punto_venta = $resultado->id_punto_venta AND id_empresa = $resultado->id_empresa ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) $resultado->confirmada = 1;
    }    

    echo json_encode($resultado);
  }
  
}