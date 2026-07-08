<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Compras extends REST_Controller {

  private $total = 0;

  function __construct() {
    parent::__construct();
    $this->load->model('Compra_Model', 'modelo');
  }

  function unir_comprobantes() {
    $id_empresa = 815;
    $sql = "SELECT C.*, P.nombre AS proveedor ";
    $sql.= "FROM compras C INNER JOIN proveedores P ON (C.id_empresa = P.id_empresa AND C.id_proveedor = P.id) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "ORDER BY C.total DESC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $sql = "SELECT * FROM compras WHERE id_empresa = $id_empresa ";
      $sql.= "AND id != $r->id ";
      $sql.= "AND numero_1 = '$r->numero_1' AND numero_2 = '$r->numero_2' ";
      $sql.= "AND id_proveedor = '$r->id_proveedor' ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        $rr = $qq->row();
        $sql = "UPDATE compras_netos SET id_compra = $r->id ";
        $sql.= "WHERE id_empresa = $id_empresa AND id_compra = $rr->id ";
        $this->db->query($sql);

        $sql = "UPDATE compras SET ";
        $sql.= "total_general = total_general + $rr->total_general, ";
        $sql.= "total_neto = total_neto + $rr->total_neto, ";
        $sql.= "impuesto_interno = impuesto_interno + $rr->impuesto_interno, ";
        $sql.= "no_gravado = no_gravado + $rr->no_gravado, ";
        $sql.= "exento = exento + $rr->exento, ";
        $sql.= "subtotal = subtotal + $rr->subtotal, ";
        $sql.= "total_regimenes_especiales = total_regimenes_especiales + $rr->total_regimenes_especiales, ";
        $sql.= "ret_ing_brutos = ret_ing_brutos + $rr->ret_ing_brutos, ";
        $sql.= "ret_ganancias = ret_ganancias + $rr->ret_ganancias, ";
        $sql.= "perc_ing_brutos = perc_ing_brutos + $rr->perc_ing_brutos, ";
        $sql.= "perc_iva = perc_iva + $rr->perc_iva, ";
        $sql.= "total_depositos = total_depositos + $rr->total_depositos ";
        $sql.= "WHERE id = $r->id AND id_empresa = $id_empresa ";
        $this->db->query($sql);

        //$sql = "DELETE FROM compras WHERE id_empresa = $id_empresa AND id = $rr->id ";
        //$this->db->query($sql);
        echo $r->numero_1." ".$r->numero_2." - ".$r->proveedor."<br/>";
      }
    }
  }

  function imprimir_remito($id) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $id_empresa = parent::get_empresa();
    $compra = $this->modelo->get($id);
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    $this->load->model("Proveedor_Model");
    $proveedor = $this->Proveedor_Model->get($compra->id_proveedor);

    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($compra->id_tipo_comprobante);
        
    $header = $this->load->view("reports/factura/header",null,true);
    
    $datos = array(
      "proveedor"=>$proveedor,
      "tipo_comprobante"=>$tipo_comprobante,
      "compra"=>$compra,
      "empresa"=>$empresa,
      "header"=>$header,
    );
    $this->load->view("reports/factura/basico/remito_compra.php",$datos);
  }


  function regimen_informacion_alicuotas($anio,$mes,$id_razon_social=0) {

    $this->load->model("Razon_Social_Model");
    $id_empresa = parent::get_empresa();
    $ids_sucursales = "";
    if ($id_empresa == 249 || $id_empresa == 868) {
      $ids_sucursales = $this->Razon_Social_Model->get_sucursales_by_razon_social($id_razon_social);
    }
    
    $str = "";
    $sql = "SELECT C.*, ";
    $sql.= " P.nombre AS proveedor, ";
    $sql.= " P.id_tipo_iva AS id_tipo_iva, ";
    $sql.= " P.cuit AS cuit, P.id AS id_proveedor ";
    $sql.= "FROM compras C ";
    $sql.= " LEFT JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "WHERE C.movimiento = '$mes$anio' ";
    if (!empty($ids_sucursales)) $sql.= "AND C.id_sucursal IN ($ids_sucursales) ";
    $sql.= "AND C.id_tipo_comprobante < 900 ";
    $sql.= "AND C.incluido_libro_iva = 1 ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    $sql.= "AND P.id_tipo_iva != 3 "; // No se muestran las alicuotas si es Exento
    $q = $this->db->query($sql);
    $result = $q->result();
    foreach($result as $r) {

      $sql = "SELECT porc_iva, ";
      $sql.= " IF(SUM(neto_dto) IS NULL,0,SUM(neto_dto)) AS neto, ";
      $sql.= " IF(SUM(iva) IS NULL,0,SUM(iva)) AS iva ";
      $sql.= "FROM compras_netos ";
      $sql.= "WHERE id_compra = $r->id ";
      $sql.= "AND id_empresa = $id_empresa ";
            //$sql.= "AND porc_iva != 0 ";
      $sql.= "GROUP BY porc_iva ";
      $qq = $this->db->query($sql);
      $result2 = $qq->result();
      foreach($result2 as $rr) {

        // Campo 1: TIPO DE COMPROBANTE
        $str.= str_pad($r->id_tipo_comprobante, 3, "0", STR_PAD_LEFT);

        // Campo 2: PUNTO DE VENTA
        $str.= str_pad($r->numero_1, 5, "0", STR_PAD_LEFT);

        // Campo 3: NUMERO DE COMPROBANTE
        $str.= str_pad($r->numero_2, 20, "0", STR_PAD_LEFT);

              // Campo 4: CODIGO DE DOCUMENTO DEL VENDEDOR
        $str.= "80";

        // Campo 5: NUMERO IDENTIFICACION VENDEDOR
        $str.= str_pad(str_replace("-","",$r->cuit),20,"0",STR_PAD_LEFT); // Nro CUIT
        
        // Campo 6: IMPORTE NETO GRAVADO
        if (abs($rr->porc_iva) == 0) {
          $str.= str_pad("0",15,"0",STR_PAD_LEFT);
        } else {
          $str.= str_pad(str_replace(".00","",abs($rr->neto*100)),15,"0",STR_PAD_LEFT);
        }
        
        // Campo 7: ALICUOTA DE IVA
        if (abs($rr->porc_iva) == 21) {
          $str.= "0005";
        } else if (abs($rr->porc_iva) == 10.5) {
          $str.= "0004";
        } else if (abs($rr->porc_iva) == 27) {
          $str.= "0006";
        } else if (abs($rr->porc_iva) == 5) {
          $str.= "0008";
        } else if (abs($rr->porc_iva) == 2.5) {
          $str.= "0009";
        } else if (abs($rr->porc_iva) == 0) {
          $str.= "0003";
        }
        
        // Campo 8: IMPUESTO LIQUIDADO
        $str.= str_pad(str_replace(".00","",abs($rr->iva*100)),15,"0",STR_PAD_LEFT);
        $str.= "\r\n";
      }
    }
    header("Content-disposition: attachment; filename=LIBRO_IVA_DIGITAL_COMPRAS_ALICUOTAS.txt");
    header("Content-type: application/octet-stream");   
    echo $str;      
  }

  function regimen_informacion($anio,$mes,$id_razon_social = 0) {

    $id_empresa = parent::get_empresa();
    $ids_sucursales = "";
    if ($id_empresa == 249 || $id_empresa == 868) {
      $this->load->model("Razon_Social_Model");
      $ids_sucursales = $this->Razon_Social_Model->get_sucursales_by_razon_social($id_razon_social);
    }

    $str = "";
    $sql = "SELECT C.*, ";
    $sql.= " IF(C.fecha < '2015-01-01','20150101',DATE_FORMAT(C.fecha,'%Y%m%d')) AS fecha, ";
    $sql.= " IF(P.razon_social != '',P.razon_social,P.nombre) AS proveedor, ";
    $sql.= " P.id_tipo_iva AS id_tipo_iva, ";
    $sql.= " P.cuit AS cuit ";
    $sql.= "FROM compras C ";
    $sql.= " LEFT JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "WHERE C.movimiento = '$mes$anio' ";
    if (!empty($ids_sucursales)) $sql.= "AND C.id_sucursal IN ($ids_sucursales) ";
    $sql.= "AND C.id_tipo_comprobante < 900 ";
    $sql.= "AND C.incluido_libro_iva = 1 ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $result = $q->result();
    foreach($result as $r) {

      $sql = "SELECT * FROM compras_netos WHERE id_compra = $r->id AND id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      $r->compras_netos = $qq->result();

      // Si es A
      if ($r->id_tipo_comprobante <= 4 || ($r->id_tipo_comprobante >= 51 && $r->id_tipo_comprobante <= 53) || ($r->id_tipo_comprobante >= 201 && $r->id_tipo_comprobante <= 203)) {
        //if ($r->id_tipo_comprobante == 1) {
        // TOTAL EXENTO
        $sql = "SELECT IF(SUM(neto_dto) IS NULL,0,SUM(neto_dto)) as neto FROM compras_netos WHERE id_compra = $r->id AND porc_iva = 0 AND id_empresa = $id_empresa ";
        $q_exento = $this->db->query($sql);
        $ex = $q_exento->row();
        $exento = $ex->neto;
      } else {
        $exento = 0;
      }

      // Campo 1: FECHA
      $str.= $r->fecha;

      // Campo 2: TIPO DE COMPROBANTE
      $str.= str_pad($r->id_tipo_comprobante, 3, "0", STR_PAD_LEFT);

      // Campo 3: PUNTO DE VENTA
      $str.= str_pad($r->numero_1, 5, "0", STR_PAD_LEFT);

      // Campo 4: NUMERO DE COMPROBANTE
      $str.= str_pad($r->numero_2, 20, "0", STR_PAD_LEFT);

      // Campo 5: Nro despacho de importacion
        $str.= "                ";

      // Campo 6: CODIGO DE DOCUMENTO DEL VENDEDOR
      $str.= "80"; // CUIT
      
      // Campo 7: NUMERO DE IDENTIFICACION DEL VENDEDOR
      $str.= str_pad(str_replace("-","",$r->cuit),20,"0",STR_PAD_LEFT); // Nro CUIT
      
      // Campo 8: APELLIDO Y NOMBRE
      $r->proveedor = strtoupper($r->proveedor);
      $r->proveedor = str_replace("Ñ","N",$r->proveedor);
      $r->proveedor = str_replace("Á","A",$r->proveedor);
      $r->proveedor = str_replace("É","E",$r->proveedor);
      $r->proveedor = str_replace("Í","I",$r->proveedor);
      $r->proveedor = str_replace("Ó","O",$r->proveedor);
      $r->proveedor = str_replace("Ú","U",$r->proveedor);
      $str.= substr(str_pad($r->proveedor,30," ",STR_PAD_RIGHT),0,30);
      
      // Campo 9: IMPORTE TOTAL
      if ($r->id_tipo_comprobante <= 4 || ($r->id_tipo_comprobante >= 51 && $r->id_tipo_comprobante <= 53) || ($r->id_tipo_comprobante >= 201 && $r->id_tipo_comprobante <= 203)) {
        // Para los comprobantes A
        $str.= str_pad(str_replace(".00","",abs($r->total_general*100)),15,"0",STR_PAD_LEFT); // Total
      } else {
        // No se aplica para los comprobantes B o C
        $str.= str_pad(str_replace(".00","",abs($r->total_general*100)),15,"0",STR_PAD_LEFT); // Total
        //$str.= "000000000000000";
      }

      // Campo 10: Total de conceptos que no integran el precio neto gravado
      //$str.= str_pad(str_replace(".00","",abs($r->total_regimenes_especiales*100)),15,"0",STR_PAD_LEFT);
      $str.= "000000000000000";

      // Campo 11: Exento o No gravado
      $r->exento = (float)$r->exento;
      $r->no_gravado = (float)$r->no_gravado;
      $exento = $r->exento + $r->no_gravado + $exento;
      $str.= str_pad(str_replace(".00","",(abs($exento*100))),15,"0",STR_PAD_LEFT);

      // Campo 12: Perc IVA
      $r->perc_iva = (float)$r->perc_iva;
      $str.= str_pad(str_replace(".00","",abs($r->perc_iva*100)),15,"0",STR_PAD_LEFT);

      // Campo 13: Perc de otros impuestos nacionales
      $str.= "000000000000000";

      // Campo 14: Perc Ing Brutos
      $r->perc_ing_brutos = (float)$r->perc_ing_brutos;
      $str.= str_pad(str_replace(".00","",abs($r->perc_ing_brutos*100)),15,"0",STR_PAD_LEFT);

      // Campo 15: Perc de otros impuestos municipales
      $r->perc_agip = (float)$r->perc_agip;
      $r->perc_san_luis = (float)$r->perc_san_luis;
      $str.= str_pad(str_replace(".00","",abs(($r->perc_agip + $r->perc_san_luis)*100)),15,"0",STR_PAD_LEFT);
      //$str.= "000000000000000";

      // Campo 16: Impuesto Interno
      $r->impuesto_interno = (float)$r->impuesto_interno;
      $str.= str_pad(str_replace(".00","",abs($r->impuesto_interno*100)),15,"0",STR_PAD_LEFT);

      // Campo 17: Codigo de Moneda
      $str.= "PES";

      // Campo 18: Tipo de cambio
      $str.= "0001000000";

      // Campo 19: Cantidad de Alicuotas de IVA (solo aplica a RI)
      if ($r->id_tipo_comprobante <= 4 || ($r->id_tipo_comprobante >= 51 && $r->id_tipo_comprobante <= 53) || ($r->id_tipo_comprobante >= 201 && $r->id_tipo_comprobante <= 203)) {
        $compras_netos = array();
        foreach($r->compras_netos as $cn) {
          if (!in_array($cn->porc_iva,$compras_netos)) {
            $compras_netos[] = $cn->porc_iva;
          }
        }
        $str.= sizeof($compras_netos);  
      } else {
        $str.= "0";
      }
      // Campo 20: Codigo de operacion
      if ($exento > 0) $str.= "E";
      else $str.= " ";

      // Campo 21: Credito Fiscal Computable
      // IGUAL A TOTAL -> Solo aplica para A
      if ($r->id_tipo_comprobante <= 4 || ($r->id_tipo_comprobante >= 51 && $r->id_tipo_comprobante <= 53) || ($r->id_tipo_comprobante >= 201 && $r->id_tipo_comprobante <= 203)) {
        $str.= str_pad(str_replace(".00","",abs($r->total_iva*100)),15,"0",STR_PAD_LEFT); // Total
      } else {
        $str.= "000000000000000";
      }

      // Campo 22: Otros tributos
      $str.= "000000000000000";

      // Campo 23: Cuit corredor
      $str.= "00000000000";

      // Campo 24: Denominacion del Emisor
      $str.= "                              ";

      // Campo 25: IVA Comision
      $str.= "000000000000000";           

      $str.= "\r\n";
    }

    header("Content-disposition: attachment; filename=LIBRO_IVA_DIGITAL_COMPRAS_CBTE.txt");
    header("Content-type: application/octet-stream");
    echo $str;
  }


  function consulta_ordenes_pago() {
    $conf = $this->get_params();
    $this->load->model("Orden_Pago_Model");
    $salida = $this->Orden_Pago_Model->buscar($conf);
    echo json_encode($salida);
  }

  private function get_params() {     

    $conf = array();
    $id_empresa = $this->input->get("id_empresa");
    $conf["id_empresa"] = ($id_empresa !== FALSE) ? $id_empresa : parent::get_empresa();
    $conf["compra_real"] = parent::get_get("ver_todas",1);
    $conf["order_by"] = parent::get_get("order_by","");
    $conf["order"] = parent::get_get("order","");

    $this->load->helper("fecha_helper");
    $desde = $this->input->get("desde");
    if ($desde !== FALSE) $conf["desde"] = fecha_mysql($desde);
    $hasta = $this->input->get("hasta");
    if ($hasta !== FALSE) $conf["hasta"] = fecha_mysql($hasta);

    $id_proveedor = $this->input->get("id_proveedor");
    if ($id_proveedor !== FALSE) $conf["id_proveedor"] = $id_proveedor;

    $id_sucursal = $this->input->get("id_sucursal");
    if ($id_sucursal !== FALSE) $conf["id_sucursal"] = $id_sucursal;

    $ids_conceptos = $this->input->get("ids_conceptos");
    if ($ids_conceptos !== FALSE) $conf["ids_conceptos"] = str_replace("-",",",$ids_conceptos);

    $movimiento = $this->input->get("movimiento");
    if ($movimiento !== FALSE) $conf["movimiento"] = $movimiento;

    $filter = $this->input->get("filter");
    if ($filter !== FALSE) $conf["filter"] = $filter;

    $conf["estado"] = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);

    $tipos_comprobantes = $this->input->get("tc");
    if ($tipos_comprobantes !== FALSE) $conf["tc"] = str_replace("-",",",$tipos_comprobantes);

    $conf["id_usuario"] = parent::get_get("id_usuario",0);

    $limit = $this->input->get("limit");
    if ($limit !== FALSE) $conf["limit"] = $limit;
    $offset = $this->input->get("offset");
    if ($offset !== FALSE) $conf["offset"] = $offset;
    return $conf;
  }

  function consulta() {
    $conf = $this->get_params();
    $salida = $this->modelo->listado($conf);
    echo json_encode($salida);
  }    

  public function resumen_compras_impuestos($fecha_desde,$fecha_hasta) {

    $id_empresa = parent::get_empresa();

    $this->load->helper("fecha_helper");
    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $fecha_desde = fecha_mysql(str_replace(",","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace(",","/",$fecha_hasta));
    $array = array();
    // Obtenemos los totales de impuestos
    $sql = "SELECT ";
    $sql.= "  SUM(perc_ing_brutos) AS perc_ing_brutos, ";
    $sql.= "  SUM(perc_iva) AS perc_iva, ";
    $sql.= "  SUM(perc_agip) AS perc_agip, ";
    $sql.= "  SUM(perc_san_luis) AS perc_san_luis, ";
    $sql.= "  SUM(impuesto_interno) AS impuesto_interno, ";
    $sql.= "  SUM(no_gravado) AS no_gravado, ";
    $sql.= "  SUM(exento) AS exento ";
    $sql.= "FROM compras C WHERE C.id_empresa = $id_empresa ";
    //$sql.= "AND C.estado = $estado ";
    $sql.= "AND '$fecha_desde' <= DATE_FORMAT(C.fecha,'%Y-%m-%d') ";
    $sql.= "AND DATE_FORMAT(C.fecha,'%Y-%m-%d') <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    $row = $q->row();
    $array["perc_ing_brutos"] = $row->perc_ing_brutos;
    $array["perc_iva"] = $row->perc_iva;
    $array["perc_agip"] = $row->perc_agip;
    $array["perc_san_luis"] = $row->perc_san_luis;
    $array["impuesto_interno"] = $row->impuesto_interno;
    $array["no_gravado"] = $row->no_gravado;
    $array["exento"] = $row->exento;
    $array["total_reg_esp"] = $row->perc_ing_brutos + $row->perc_iva + $row->impuesto_interno + $row->no_gravado + $row->exento + $row->perc_agip + $row->perc_san_luis;

    $sql = "SELECT ";
    $sql.= "  SUM(CN.neto_dto) AS neto, ";
    $sql.= "  SUM(CN.iva) AS iva, ";
    $sql.= "  SUM(CN.neto_dto + CN.iva) AS total ";
    $sql.= "FROM compras C ";
    $sql.= "INNER JOIN compras_netos CN ON (C.id = CN.id_compra AND C.id_empresa = CN.id_empresa) ";
    $sql.= "INNER JOIN tipos_gastos CO ON (CN.id_concepto = CO.id AND CN.id_empresa = CO.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    //$sql.= "AND C.estado = $estado ";
    $sql.= "AND '$fecha_desde' <= DATE_FORMAT(C.fecha,'%Y-%m-%d') ";
    $sql.= "AND DATE_FORMAT(C.fecha,'%Y-%m-%d') <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    $row = $q->row();
    $array["neto"] = $row->neto;
    $array["iva"] = $row->iva;
    $array["total"] = $row->total;

    echo json_encode($array);
  }

  // Reemplaza a la funcion resumen_compras_arbol
  function imprimir_resumen_arbol_compras() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $movimiento = parent::get_get("movimiento","");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_razon_social = parent::get_get("id_razon_social",0);
    $id_padre = parent::get_get("id_padre",0);
    $desde = parent::get_get("desde","");
    $hasta = parent::get_get("hasta","");
    $incluir = parent::get_get("incluir",1);
    $this->load->helper("fecha_helper");
    $desde = (empty($desde) && empty($movimiento)) ? date("Y-m-d") : fecha_mysql($desde);
    $hasta = (empty($hasta) && empty($movimiento)) ? date("Y-m-d") : fecha_mysql($hasta);

    // Obtenemos todo el arbol
    $arr = $this->modelo->get_arbol(array(
      "id_padre"=>$id_padre,
      "movimiento"=>$movimiento,
      "id_sucursal"=>$id_sucursal,
      "id_razon_social"=>$id_razon_social,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "compra_real"=>$incluir,
    ));
    $header = $this->load->view("reports/factura/header",null,true);
    $this->load->view("reports/compras_resumen",array(
      "header"=>$header,
      "results"=>$arr,
      "movimiento"=>$movimiento,
      "desde"=>$desde,
      "hasta"=>$hasta,
    ));
  }

  // Reemplaza a la funcion resumen_compras_arbol
  function resumen_arbol_compras() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $movimiento = parent::get_post("movimiento","");
    $id_sucursal = parent::get_post("id_sucursal",0);
    $id_razon_social = parent::get_post("id_razon_social",0);
    $id_padre = parent::get_post("id_padre",0);
    $desde = parent::get_post("desde","");
    $hasta = parent::get_post("hasta","");
    $incluir = parent::get_post("incluir",1);
    $this->load->helper("fecha_helper");
    $desde = (empty($desde) && empty($movimiento)) ? date("Y-m-d") : fecha_mysql($desde);
    $hasta = (empty($hasta) && empty($movimiento)) ? date("Y-m-d") : fecha_mysql($hasta);

    // Obtenemos todo el arbol
    $arr = $this->modelo->get_arbol(array(
      "id_padre"=>$id_padre,
      "movimiento"=>$movimiento,
      "id_sucursal"=>$id_sucursal,
      "id_razon_social"=>$id_razon_social,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "compra_real"=>$incluir,
    ));
    $salida = array(
      "results"=>$arr,
    );        
    echo json_encode($salida);
  }


  function exportar_retencion_ganancias($fecha_desde, $fecha_hasta, $quincena = 1) {

    $str = "";
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    $fecha_quincena = date('Y-m-15',strtotime($fecha_desde));

    // Seleccionamos los proveedores a los que se aplica RET GANANCIAS
    $sql = "SELECT * ";
    $sql.= "FROM proveedores P ";
    $sql.= "WHERE P.aplica_ret_ganancias = 1 "; // Si Aplica RET GCIAS
    $sql.= "AND P.id_empresa = $id_empresa ";
    $sql.= "AND P.id_tipo_iva IN (1,5,6) "; // Si es RI
    $q = $this->db->query($sql);

    // Recorremos los PROVEEDORES
    foreach($q->result() as $proveedor) {

      // Calculamos los pagos efectuados en el mes
      $sql = "SELECT * ";
      $sql.= "FROM compras CO ";
      $sql.= "WHERE CO.id_tipo_comprobante = -1 "; // Si es OP
      $sql.= "AND CO.id_empresa = $id_empresa ";
      $sql.= "AND '$fecha_desde' <= CO.fecha ";
      $sql.= "AND CO.fecha <= '$fecha_hasta' ";
      $sql.= "AND CO.id_proveedor = $proveedor->id ";
      $sql.= "ORDER BY CO.fecha ASC, CO.numero_2 ASC ";
      $qq = $this->db->query($sql);
      $suma_ret_ganancias = 0;
      $base = 0;
        
      foreach($qq->result() as $orden_pago) {
        // Calculamos las compras realizadas en esa ORDEN DE PAGO
        $sql = "SELECT * FROM compras CO ";
        $sql.= "WHERE id_orden_pago = $orden_pago->id ";
        $sql.= "AND id_tipo_comprobante IN (1,2,3,4) "; // Si es FA, NC o ND
        $sql.= "AND id_empresa = $id_empresa ";
        $sql.= "AND CO.compra_real = 1 ";
        $q_compras = $this->db->query($sql);
        $neto_orden_pago = 0;
        foreach($q_compras->result() as $compra) {
          $neto_orden_pago = $neto_orden_pago + $compra->total_neto;
        }
        $suma_ret_ganancias = $suma_ret_ganancias + abs($orden_pago->ret_ganancias);
        $base = $base + abs($neto_orden_pago);
        
        if ($orden_pago->ret_ganancias != 0) {

          if ($quincena == 2 && ($orden_pago->fecha <= $fecha_quincena)) $mostrar = false;
          else $mostrar = true;

          if ($mostrar) {
            $str.= "6 "; // Codigo del Comprobante
            $str.= fecha_es($orden_pago->fecha); // Fecha de la OP
            $str.= "0000".$orden_pago->numero_1; // Nro Comprobante
            $str.= $orden_pago->numero_2;
            $str.= str_pad(number_format($neto_orden_pago,2,",",""),16,"0",STR_PAD_LEFT); // Neto OP
            $str.= "217"; // Cod Impuesto: 217 Ganancias
            $str.= " 78"; // Cod Regimen
            $str.= "1";   // Cod Operacion
            $str.= str_pad(number_format($base-100000,2,",",""),14,"0",STR_PAD_LEFT); // Base de CALCULO    
            $str.= fecha_es($orden_pago->fecha); // Fecha Emision Retencion = Fecha OP
            $str.= "1 "; // Cod Condicion
            $str.= "0";  // Ret aplicada a sujetos suspendidos segun
            $str.= str_pad(number_format(abs($orden_pago->ret_ganancias),2,",",""),14,"0",STR_PAD_LEFT);
            $str.= "000,00"; // Porc de exclusion
            $str.= "0000000000"; // Fecha de emision del boletin
            $str.= "80"; // Tipo del doc retenido
            $str.= str_pad(str_replace("-","",$proveedor->cuit),20," "); // Nro de doc retenido
            $str.= "0             "; // Nro Certificado Original
            if (strlen($proveedor->nombre)>30) $str.= str_pad(substr($proveedor->nombre,0,30),30," ",STR_PAD_RIGHT); // Denominacion del ordenante
            else $str.= str_pad($proveedor->nombre,30," ",STR_PAD_RIGHT); // Denominacion del ordenante
            $str.= "0"; // Acrecentamiento
            $str.= "           "; // Cuit del pais retenido
            $str.= "           \n"; // Cuit del ordenante
          }
        }
      }
    }
    header("Content-disposition: attachment; filename=ganancias.txt");
    header("Content-type: application/octet-stream");  
    echo $str;
  }


function exportar_retencion_ing_brutos($fecha_desde, $fecha_hasta) {

  $this->load->helper("fecha_helper");
  $id_empresa = parent::get_empresa();
  $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
  $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));

  $sql = "SELECT ";
  $sql.= "C.numero_1, C.numero_2, ";
  $sql.= "P.cuit AS cuit, ";
  $sql.= "DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
  $sql.= "C.fecha AS fecha_ingles, ";
  $sql.= "ABS(C.ret_ing_brutos) AS ret_ib ";
  $sql.= "FROM compras C ";
  $sql.= "INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa AND C.id_empresa = P.id_empresa) ";
  $sql.= "WHERE C.id_tipo_comprobante = -1 AND C.ret_ing_brutos != 0 ";
  $sql.= "AND C.id_empresa = $id_empresa ";
  $sql.= "AND '$fecha_desde' <= C.fecha AND C.fecha <= '$fecha_hasta' ";
  $q = $this->db->query($sql);

  $str = "";
  foreach($q->result() as $r) {
    $str.= $r->cuit;
    $str.= $r->fecha;
    $str.= "0001";//$r->numero_1;
    $str.= $r->numero_2;
    $str.= str_pad($r->ret_ib,11,'0',STR_PAD_LEFT);
    $str.= "A\n";
  }
  header("Content-disposition: attachment; filename=ret_ib.txt");
  header("Content-type: application/octet-stream");  
  echo $str;
}

function existe_comprobante($id_proveedor,$numero_1,$numero_2,$id_tipo_comprobante=0) {
  $id_empresa = parent::get_empresa();
    // Debemos controlar que el numero de comprobante
    // no exista para el proveedor dado
  $sql = "SELECT * FROM compras C ";
  $sql.= "WHERE C.id_proveedor = $id_proveedor ";
  $sql.= "AND numero_1 = $numero_1 ";
  $sql.= "AND numero_2 = $numero_2 ";
  $sql.= "AND id_empresa = $id_empresa ";
  if (!empty($id_tipo_comprobante) && $id_tipo_comprobante != 999) $sql.= "AND id_tipo_comprobante = $id_tipo_comprobante ";
  $query = $this->db->query($sql);
  return ($query->num_rows() != 0);
}

function comprobar_existe_comprobante() {

  $id_proveedor = $this->input->post("id_proveedor");
  $id_tipo_comprobante = $this->input->post("id_tipo_comprobante");
  $numero_1 = $this->input->post("numero_1");
  $numero_2 = $this->input->post("numero_2");

  if ($this->existe_comprobante($id_proveedor,$numero_1,$numero_2,$id_tipo_comprobante)) {
    $salida = array(
      "error"=>1,
      "mensaje"=>"ERROR: El comprobante ya existe en el sistema."
      );
  } else {
    $salida = array(
      "error"=>0,
      "mensaje"=>""
      );
  }
  echo json_encode($salida);
}



  /**
   * Insertamos una nueva factura de compras
   */
  function insert() {

    $array = $this->parse_put();
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
    $proveedor = isset($array->proveedor) ? $array->proveedor : "";
    unset($array->proveedor);

    // Si no existe el comprobante
    if ($this->existe_comprobante($array->id_proveedor,$array->numero_1,$array->numero_2,$array->id_tipo_comprobante)) {
      $salida = array(
        "error"=>1,
        "mensaje"=>"ERROR: El comprobante ya existe en el sistema."
        );
      echo json_encode($salida);
      exit();      
    }

    // Controlamos si el libro de IVA ya fue cerrado
    // para el movimiento que se intenta ingresar
    if ($array->incluido_libro_iva == "1") {

      $sql = "SELECT * FROM fact_configuracion WHERE id_empresa = $array->id_empresa ";
      $q = $this->db->query($sql);
      $libro = $q->row();
      $ultimo_movimiento_cerrado = $libro->ultimo_movimiento_cerrado_compras;
      if (!empty($ultimo_movimiento_cerrado)) {
        $mes_cerrado = substr($ultimo_movimiento_cerrado,0,2);
        $anio_cerrado = substr($ultimo_movimiento_cerrado,2,2);
        $cerrado = ((int)"20".$anio_cerrado.$mes_cerrado); // Ej: 201304
        
        $mes_mov = substr($array->movimiento,0,2);
        $anio_mov = substr($array->movimiento,2,2);
        $actual = ((int)"20".$anio_mov.$mes_mov); // Ej: 201305
        
        if ($actual <= $cerrado) {
          $salida = array(
            "error"=>1,
            "mensaje"=>"ERROR: El libro de IVA para '$array->movimiento' ya fue cerrado."
            );
          echo json_encode($salida);
          exit();
        }
      }
    }

    // Si es un remito, cambiamos el estado
    if ($array->id_tipo_comprobante > 100) $array->estado = 1;

    // TODO: SI NO ESTA HABILITADO LOS REPARTOS !!
    // Si el pago es en EFECTIVO
    if ($array->forma_pago == "E") {
      $array->pago = $array->total_general;
      $array->pagada = 1;
    // Si el pago es en CUENTA CORRIENTE
    } else if ($array->forma_pago == "C") {
      $array->pago = 0;
    }

    // Entonces podemos guardar el comprobante
    $insert_id = $this->modelo->save($array);    

    // Si hay que guardar el movimiento en la caja
    // solamente los movimientos en efectivo tienen id_caja
    if (isset($array->id_caja) && $array->id_caja != 0 && $array->forma_pago == "E") {
      $this->load->model("Caja_Movimiento_Model");
      if ($array->total_general < 0) {
        // Si el monto es negativo, lo ponemos como un INGRESO
        $monto = abs($array->total_general);
        $observaciones_caja = $proveedor." ".$array->numero_1."-".$array->numero_2;
        $this->Caja_Movimiento_Model->ingreso(array(
          "id_empresa"=>$id_empresa,
          "id_orden_pago"=>$insert_id,
          "id_caja"=>$array->id_caja,
          "monto"=>$monto,
          "fecha"=>$array->fecha." ".date("H:i:s"),
          "observaciones"=>$observaciones_caja,
          "id_sucursal"=>$array->id_sucursal,
          "id_usuario"=>$array->id_usuario,
        ));
      } else {
        // Sino el pago es un EGRESO
        $this->Caja_Movimiento_Model->egreso(array(
          "id_empresa"=>$id_empresa,
          "id_orden_pago"=>$insert_id,
          "id_caja"=>$array->id_caja,
          "monto"=>$array->total_general,
          "fecha"=>$array->fecha." ".date("H:i:s"),
          "observaciones"=>$observaciones_caja,
          "id_sucursal"=>$array->id_sucursal,
          "id_usuario"=>$array->id_usuario,
        ));
      }
    }

    $salida = array(
      "id"=>$insert_id,
      "error"=>0
    );
    echo json_encode($salida);

  }

  function update($id) {
    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }
    $array = $this->parse_put();

    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;    
    $proveedor = isset($array->proveedor) ? $array->proveedor : "";
    unset($array->proveedor);

    // Si es un remito, cambiamos el estado
    if ($array->id_tipo_comprobante > 100) $array->estado = 1;

    // Si el pago es en EFECTIVO
    if ($array->forma_pago == "E") {
      $array->pago = $array->total_general;
      if ($array->pagada == 0) $array->pagada = 1;
    // Si el pago es en CUENTA CORRIENTE
    } else if ($array->forma_pago == "C") {
      $array->pago = 0;
      if ($array->id_orden_pago == 0 && $array->pagada == 1) $array->pagada = 0;
    }

    $this->modelo->update($id,$array);

    // Si hay que guardar el movimiento en la caja
    // solamente los movimientos en efectivo tienen id_caja
    if (isset($array->id_caja) && $array->id_caja != 0 && $array->forma_pago == "E") {
      $this->load->model("Caja_Movimiento_Model");
      // Primero borramos en caso de que exista por si cambio el monto
      $this->Caja_Movimiento_Model->borrar(array(
        "id_empresa"=>$id_empresa,
        "id_caja"=>$array->id_caja,
        "id_orden_pago"=>$id,        
        "id_sucursal"=>$array->id_sucursal,
      ));

      if ($array->total_general < 0) {
        // Si el monto es negativo, lo ponemos como un INGRESO
        $monto = abs($array->total_general);
        $observaciones_caja = $proveedor." ".$array->numero_1."-".$array->numero_2;
        $this->Caja_Movimiento_Model->ingreso(array(
          "id_empresa"=>$id_empresa,
          "id_orden_pago"=>$id,
          "id_caja"=>$array->id_caja,
          "monto"=>$monto,
          "fecha"=>$array->fecha." ".date("H:i:s"),
          "observaciones"=>$observaciones_caja,
          "id_sucursal"=>$array->id_sucursal,
          "id_usuario"=>$array->id_usuario,
        ));
      } else {
        // Sino el pago es un EGRESO
        $this->Caja_Movimiento_Model->egreso(array(
          "id_empresa"=>$id_empresa,
          "id_orden_pago"=>$id,
          "id_caja"=>$array->id_caja,
          "monto"=>$array->total_general,
          "fecha"=>$array->fecha." ".date("H:i:s"),
          "observaciones"=>$observaciones_caja,
          "id_sucursal"=>$array->id_sucursal,
          "id_usuario"=>$array->id_usuario,
        ));
      }
    }

    echo json_encode(array(
      "id"=>$id,
      "error"=>0
    ));
  }

  function delete($id = null) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM compras_netos WHERE id_compra = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM cajas_movimientos WHERE id_orden_pago = $id AND id_empresa = $id_empresa");
    $this->modelo->delete($id);
    echo json_encode(array());
  }    




// ===========================================

  // Funcion deprecada
  public function resumen_compras_arbol($movimiento, $id_sucursal = 0) {

    $this->load->helper("fecha_helper");
    // Obtenemos todo el arbol
    $arr = $this->get_arbol(0,$movimiento,$id_sucursal);
    $salida = array(
      "total"=>sizeof($arr),
      "results"=>$arr,
    );        
    echo json_encode($salida);
  }

  // Funcion deprecada
  public function get_arbol( $id_padre = 0, $movimiento = "", $id_sucursal = 0) {

    $id_empresa = parent::get_empresa();
    $this->db->where("id_empresa = $id_empresa");
    $this->db->where("id_padre = $id_padre");
    $this->db->order_by("nombre","asc");
    $query = $this->db->get("tipos_gastos");
    $result = $query->result();
    $elementos = array();
    foreach($result as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $row->id_padre;
      $e->orden = $row->orden;
      $e->nombre = $row->nombre;
      $e->codigo = $row->codigo;
      $e->descripcion = $row->descripcion;
      $a = $this->resumen_compras_por_concepto($row->id,$movimiento,$id_sucursal);
      $e->total = $a["total"];
      $e->neto = $a["neto"];
      $e->iva = $a["iva"];
      $this->total = $this->total + $e->total;
      $e->children = $this->get_arbol($row->id,$movimiento,$id_sucursal);
      $elementos[] = $e;
    }
    return $elementos;    
  }

  // Funcion deprecada
  function resumen_compras_por_concepto($id_concepto, $movimiento, $id_sucursal = 0)
  {
    @session_start();
    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $id_empresa = parent::get_empresa();

    // Tomamos los hijos
    $sql = "SELECT * FROM tipos_gastos WHERE id_padre = $id_concepto AND id_empresa = $id_empresa";
    $q_hijos = $this->db->query($sql);
    $hijos = $q_hijos->result();

    // Calculamos el total de ese concepto
    $sql = "SELECT ";
    $sql.= "  SUM(CN.neto_dto) AS neto, ";
    $sql.= "  SUM(CN.iva) AS iva, ";
    $sql.= "  SUM(CN.neto_dto + CN.iva) AS total ";
    $sql.= "FROM compras C ";
    $sql.= "INNER JOIN compras_netos CN ON (C.id = CN.id_compra AND C.id_empresa = CN.id_empresa) ";
    $sql.= "INNER JOIN tipos_gastos CO ON (CN.id_concepto = CO.id AND CN.id_empresa = CO.id_empresa) ";
    $sql.= "WHERE CN.id_concepto = $id_concepto ";
    $sql.= "AND C.compra_real = 1 "; // Si es una compra real
    $sql.= "AND C.id_empresa = $id_empresa ";
    $sql.= "AND C.movimiento = '$movimiento' ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($estado == 0) $sql.= "AND C.id_tipo_comprobante > 0 AND C.id_tipo_comprobante < 900 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      $total = $row->total;
      $iva = $row->iva;
      $neto = $row->neto;
    } else {
      $total = 0;
      $neto = 0;
      $iva = 0;
    }

    // Calculamos el total de todos los hijos
    foreach($hijos as $hijo) {
      $a = $this->resumen_compras_por_concepto($hijo->id,$movimiento,$id_sucursal);
      $total = $total + (float) $a["total"];
      $neto  = $neto  + (float) $a["neto"];
      $iva   = $iva   + (float) $a["iva"];
    }
    return array(
      "total"=>$total,
      "neto"=>$neto,
      "iva"=>$iva
      );
  }


}