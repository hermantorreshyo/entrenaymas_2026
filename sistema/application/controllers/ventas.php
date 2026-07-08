<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Ventas extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  // Reemplaza a la funcion resumen_compras_arbol
  function imprimir_resumen_arbol_ventas() {
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_razon_social = parent::get_get("id_razon_social",0);
    $id_padre = parent::get_get("id_padre",0);
    $desde = parent::get_get("desde","");
    $hasta = parent::get_get("hasta","");
    $this->load->helper("fecha_helper");
    $desde = (empty($desde)) ? date("Y-m-d") : fecha_mysql($desde);
    $hasta = (empty($hasta)) ? date("Y-m-d") : fecha_mysql($hasta);

    // Obtenemos todo el arbol
    $this->load->model("Venta_Model");
    $arr = $this->Venta_Model->get_arbol(array(
      "id_padre"=>$id_padre,
      "id_sucursal"=>$id_sucursal,
      "id_razon_social"=>$id_razon_social,
      "desde"=>$desde,
      "hasta"=>$hasta,
    ));
    $header = $this->load->view("reports/factura/header",null,true);
    $this->load->view("reports/ventas_resumen",array(
      "header"=>$header,
      "results"=>$arr,
      "desde"=>$desde,
      "hasta"=>$hasta,
    ));
  } 

  function enviar_email_descuento() {

    $id_empresa = parent::get_empresa();
    $id_cliente = parent::get_post("id_cliente",0);
    $id_factura = parent::get_post("id_factura",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);
    $porc_descuento = parent::get_post("porc_descuento",0);
    $this->load->model("Cliente_Model");
    $this->load->model("Factura_Model");
    $this->load->model("Email_Template_Model");

    $factura = $this->Factura_Model->get($id_factura,$id_punto_venta);
    if ($factura === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe la factura con ID: $id_factura",
      ));
      exit();
    }
    $cliente = $this->Cliente_Model->get($id_cliente,$id_empresa);
    if ($cliente === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe cliente con ID: $id_cliente",
      ));
      exit();
    }

    $template = $this->Email_Template_Model->get_by_key("compra-descuento",$id_empresa);
    if ($template === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No tiene creado una plantilla de email para enviar descuentos automaticos.",
      ));
      exit();
    }

    // Actualizamos el descuento en la factura
    $total = $factura->total * ((100-$porc_descuento)/100);
    $sql = "UPDATE facturas SET porc_descuento = $porc_descuento, total = $total, id_tipo_estado = 0 ";
    $sql.= "WHERE id = $factura->id AND id_punto_venta = $factura->id_punto_venta AND id_empresa = $factura->id_empresa ";
    $this->db->query($sql);

    echo json_encode(array(
      "email"=>$cliente->email,
      "nombre"=>$cliente->nombre,
      "id_cliente"=>$cliente->id,
      "sql"=>$sql,
    ));
    /*
    // Reemplazamos los datos del template
    $template->texto = str_replace("{{porc_descuento}}", round($porc_descuento,2), $template->texto);
    $template->texto = str_replace("{{cliente}}", $cliente->nombre, $template->texto);
    $template->texto = str_replace("{{link_web}}", $dominio, $template->texto);
    */
  }

  function editar_vendedor() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $ventas = parent::get_post("ventas",array());
    $id_vendedor = parent::get_post("id_vendedor",0);
    if (empty($id_vendedor) || empty($ventas)) {
      echo json_encode(array("error"=>1));
      exit();
    }
    foreach($ventas as $v) {
      $sql = "UPDATE facturas SET id_vendedor = $id_vendedor ";
      $sql.= "WHERE id = '".$v["id"]."' AND id_punto_venta = '".$v["id_punto_venta"]."' AND id_empresa = '$id_empresa' ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  } 

  function editar_fecha_reparto() {
    set_time_limit(0);
    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $ventas = parent::get_post("ventas",array());
    $fecha_reparto = parent::get_post("fecha_reparto","");
    $reparto = parent::get_post("reparto","");
    if (empty($fecha_reparto) || empty($reparto)) {
      echo json_encode(array("error"=>1));
      exit();
    }
    $fecha_reparto = fecha_mysql($fecha_reparto);
    foreach($ventas as $v) {
      $sql = "UPDATE facturas SET reparto = '$reparto', fecha_reparto = '$fecha_reparto' ";
      $sql.= "WHERE id = '".$v["id"]."' AND id_punto_venta = '".$v["id_punto_venta"]."' AND id_empresa = '$id_empresa' ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  } 

  function importar_caja() {

    $id_empresa = $this->get_empresa();
    $file = "uploads/".$_FILES["file"]["name"];
    if (move_uploaded_file($_FILES["file"]["tmp_name"],$file)) {

      // Ahora llamamos al metodo para que procese los archivos que subimos
      $ip_server = $_SERVER["SERVER_NAME"];

      $url = "http://$ip_server/sistema/uploader/function/procesar_put/";
      $c = curl_init($url);
      curl_setopt($c, CURLOPT_PUT, 1 );
      curl_setopt($c, CURLOPT_INFILESIZE, filesize($file) );
      curl_setopt($c, CURLOPT_INFILE, ($in=fopen($file, 'r')) );
      curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'POST' );
      curl_setopt($c, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
      curl_setopt($c, CURLOPT_URL, $url );
      curl_setopt($c, CURLOPT_RETURNTRANSFER, 1 );
      curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($c, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
      $html = curl_exec($c);     
    }
    header("Location: /sistema/app/#ventas_listado");
  }


  function percepciones_iibb($fecha_desde,$fecha_hasta,$tipo_descarga = 1) {
    $id_empresa = parent::get_empresa();
    $data = array(
      "datos" => array(),
    );
    $this->load->helper("fecha_helper");
    $this->load->helper("cuit_helper");
    $this->load->helper('download');
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
        
    $sql = "SELECT T.*, C.nombre, C.cuit, TC.letra ";
    $sql.= "FROM facturas T ";
    $sql.= "LEFT JOIN clientes C ON (T.id_cliente = C.id AND T.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN tipos_comprobante TC ON (T.id_tipo_comprobante = TC.id) ";
    $sql.= "WHERE T.id_empresa = $id_empresa ";
    $sql.= "AND '$fecha_desde' <= T.fecha ";
    $sql.= "AND T.fecha <= '$fecha_hasta' ";
    $sql.= "AND T.percepcion_ib != 0 ";
    $sql.= "AND T.anulada = 0 AND T.pendiente = 0 ";
    $query = $this->db->query($sql);
    $data = "";
    foreach($query->result() as $r) { 
            
      // CUIT Contribuyente
      $r->cuit = validarCUIT($r->cuit) ? $r->cuit : "20111111112";
      $data.= substr($r->cuit,0,2)."-".substr($r->cuit,2,8)."-".substr($r->cuit,10,1);
      if($tipo_descarga == 2) $data.= ";";
      
      // Fecha Percepcion
      $data.= fecha_es($r->fecha);
      if($tipo_descarga == 2) $data.= ";";
      
      // Tipo de Comprobante
      if ($r->id_tipo_comprobante == 2 || $r->id_tipo_comprobante == 7) $data.= "D";
      else if ($r->id_tipo_comprobante == 3 || $r->id_tipo_comprobante == 8) $data.="C";
      else $data.= "F";
      if($tipo_descarga == 2) $data.= ";";
            
      // Letra de Comprobante
      $data.= $r->letra;
      if($tipo_descarga == 2) $data.= ";";
            
      // Nro Caja
      $data.= str_pad($r->punto_venta,4,"0",STR_PAD_LEFT);
      if($tipo_descarga == 2) $data.= ";";
      
      // Nro factura
      $data.= str_pad($r->numero,8,"0",STR_PAD_LEFT);
      if($tipo_descarga == 2) $data.= ";";
      
      // Importe Total
      if ($r->id_tipo_comprobante == 3 || $r->id_tipo_comprobante == 8) $r->total = -1 * round($r->total,2);
      if ($r->total < 0) {
        $data.= "-".str_pad(abs((int)($r->total)),8,"0",STR_PAD_LEFT);
      } else {
        $data.= str_pad((int)($r->total),9,"0",STR_PAD_LEFT);
      }
      $data.= ",";
      $aux = (string)$r->total;
      $data.= str_pad(substr($aux,strpos($aux,".")+1),"0",2,STR_PAD_RIGHT);
      if($tipo_descarga == 2) $data.= ";";
      
      // Percepcion Ingresos Brutos
      if ($r->id_tipo_comprobante == 3 || $r->id_tipo_comprobante == 8) $r->percepcion_ib = -1 * round($r->percepcion_ib,2);
      if ($r->percepcion_ib < 0) {
        $data.= "-".str_pad(abs((int)($r->percepcion_ib)),7,"0",STR_PAD_LEFT);
      } else {
        $data.= str_pad((int)($r->percepcion_ib),8,"0",STR_PAD_LEFT);  
      }
      $data.= ",";
      $aux = (string)$r->percepcion_ib;
      $data.= str_pad(substr($aux,strpos($aux,".")+1),"0",2,STR_PAD_RIGHT);
      if($tipo_descarga == 2) $data.= ";";

      // Fecha de Emision (porque la actividad es 7)
      if ($id_empresa != 46) $data.= fecha_es($r->fecha);
      if($tipo_descarga == 2) $data.= ";";
      
      $data.= "A\n";
    }

    $anio = substr($fecha_desde, 0, 4);
    $mes = substr($fecha_desde, 5, 2);
    $dia = substr($fecha_desde, 7, 2);
    $quincena = ($dia == 15) ? 2 : 1;

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $cuit = str_replace("-", "", $empresa->cuit);
    $nombre = "AR-".$cuit."-".$anio.$mes.$quincena."-7-LOTE1";
    $extension = ($tipo_descarga == 1) ? "txt" : "csv";

    header("Content-disposition: attachment; filename=$nombre.".$extension);
    header("Content-type: application/octet-stream");
    echo $data;
  }

  function por_concepto() {
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $fecha_desde = $this->input->get("desde");
    $fecha_hasta = $this->input->get("hasta");
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));

    $sql = "SELECT DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " F.punto_venta, A.nombre, ";
    $sql.= " SUM(IF(TC.negativo = 1,-total_sin_iva,total_sin_iva)) AS neto, ";
    $sql.= " SUM(IF(TC.negativo = 1,-FI.iva,FI.iva)) AS iva, ";
    $sql.= " SUM(IF(TC.negativo = 1, -total_con_iva, total_con_iva)) AS total ";
    $sql.= "FROM facturas_items FI ";
    $sql.= "INNER JOIN facturas F ON (FI.id_factura = F.id AND FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta) ";
    $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND F.fecha >= '$fecha_desde' AND fecha <= '$fecha_hasta' ";
    $sql.= "AND F.anulada = 0 AND F.pendiente = 0 ";
    $sql.= "GROUP BY FI.id_punto_venta, FI.id_articulo ";
    $sql.= "ORDER BY F.punto_venta ASC ";
    $q = $this->db->query($sql);
    $result = $q->result();
    $salida = array();
    foreach($result as $r) {
      if (!isset($salida[$r->punto_venta])) $salida[$r->punto_venta] = array();
      $salida[$r->punto_venta][] = $r;
    }

    if ($id_empresa == 135) {
      $sql = "SELECT 14 AS punto_venta, 'Servicios de Confiteria' AS nombre,  ";
      $sql.= " SUM(F.neto) AS neto, SUM(F.iva) AS iva, SUM(F.total) AS total ";
      $sql.= "FROM zetas F ";
      $sql.= "WHERE F.id_empresa = $id_empresa ";
      $sql.= "AND F.fecha >= '$fecha_desde' ";
      $sql.= "AND F.fecha <= '$fecha_hasta' ";
      $q = $this->db->query($sql);
      $result = $q->result();
      foreach($result as $r) {
        if (!isset($salida[$r->punto_venta])) $salida[$r->punto_venta] = array();
        $salida[$r->punto_venta][] = $r;
      }
    }

    $header = $this->load->view("reports/iva/header",null,true);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    $data = array(
      "datos"=>$salida,
      "fecha_desde"=>fecha_es($fecha_desde),
      "fecha_hasta"=>fecha_es($fecha_hasta),
      "empresa"=>$empresa,
      "header"=>$header,
    );
    $this->load->view("reports/iva/ventas_por_concepto",$data);
  }
        
    function totales() {
    
      $fecha_desde = $this->input->get("desde");
      $fecha_hasta = $this->input->get("hasta");
      $agrupado_por = $this->input->get("agrupado_por");
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");
      
      // Salida
      $data = array(
          "datos" => array(),
      );
      $this->load->helper("fecha_helper");
      $resultado = array();

      // Acomodamos los datos de entrada
      $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
      $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));

      $conf = array(
        "fecha_desde"=>$fecha_desde,
        "fecha_hasta"=>$fecha_hasta,
        "limit"=>$limit,
        "agrupado_por"=>$agrupado_por,
        "offset"=>$offset,
      );
        
        $this->load->model("Venta_Model");
        $resultado = $this->Venta_Model->totales($conf);
        
        // Imprimimos la salida
        echo json_encode($resultado);
    }
  
  private function regimen_informacion_facturas($conf) {

    $id_empresa = $conf["id_empresa"];
    $fecha_desde = $conf["fecha_desde"];
    $fecha_hasta = $conf["fecha_hasta"];
    $id_razon_social = (isset($conf["id_razon_social"])) ? $conf["id_razon_social"] : 0;

    // LAS FACTURAS CON CLIENTES ESPECIFICOS NO SE AGRUPAN
    $sql = "SELECT F.punto_venta, F.id_cliente, F.id_tipo_comprobante, F.id_punto_venta, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= " F.numero AS numero_desde, F.numero AS numero_hasta, ";
    $sql.= " F.id AS id_desde, F.id AS id_hasta, ";
    $sql.= " F.total AS total, F.neto AS neto, F.iva AS iva, ";
    $sql.= " F.percepcion_ib AS percepcion_ib, F.percep_viajes, ";
    $sql.= " DATE_FORMAT(F.fecha,'%Y%m%d') AS fecha, ";
    $sql.= " IF(C.id_tipo_documento IS NULL,'99',C.id_tipo_documento) AS id_tipo_documento, ";
    $sql.= " IF(C.cuit IS NULL,'',C.cuit) AS cuit ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "WHERE F.fecha >=  '$fecha_desde' ";
    $sql.= "AND F.fecha <=  '$fecha_hasta' ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    if ($id_empresa == 121) $sql.= "AND F.id_cliente != 60993 "; // Empleados cooperativa
    //$sql.= "AND F.estado = 0 ";
    $sql.= "AND F.id_tipo_comprobante != 999 ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.pendiente = 0 ";
    $sql.= "AND F.tipo != 'P' ";
    $sql.= "ORDER BY fecha ASC, punto_venta ASC, numero_desde ASC ";
    $filas = $this->db->query($sql);

    $numeros = array();

    $str = "";
    $str_alicuotas = "";
    foreach($filas->result() as $r) {
      
      // Campo 1: FECHA
      $str.= $r->fecha;
      
      // Campo 2: TIPO DE COMPROBANTE
      $str.= str_pad($r->id_tipo_comprobante,3,"0",STR_PAD_LEFT);
      
      // Campo 3: PUNTO DE VENTA
      $str.= str_pad($r->punto_venta,5,"0",STR_PAD_LEFT);
      
      // Campo 4: NUMERO DE COMPROBANTE DESDE
      $numeros[$r->punto_venta] = $r->numero_hasta+1;
      $str.= str_pad($r->numero_desde,20,"0",STR_PAD_LEFT);
      
      // Campo 5: NUMERO DE COMPROBANTE HASTA
      $str.= str_pad($r->numero_hasta,20,"0",STR_PAD_LEFT);
      
      // Campo 6: CODIGO DE DOCUMENTO DE COMPRADOR
      if ($r->id_tipo_documento == 0) $r->id_tipo_documento = 80;

      if ($r->total > 1000 && $r->id_tipo_documento == 99) {
        // Si el ticket supera los 1000, el cliente debe estar identificado si o si
        $r->id_tipo_documento = 80;
        if (empty($r->cuit)) $r->cuit = "20111111112";
      }
      $str.= str_pad($r->id_tipo_documento,2,"0",STR_PAD_LEFT);
      
      // Campo 7: NUMERO DE IDENTIFICACION DEL COMPRADOR
      $cuit = trim($r->cuit);
      $cuit = str_replace("-","",$cuit);
      if ($r->id_tipo_documento == 80) {
        // Ponemos un CUIT generico
        if (!validarCUIT($r->cuit)) $cuit = "20111111112";
      }
      $cuit = str_pad($cuit,20,"0",STR_PAD_LEFT); // Nro CUIT
      if ($r->id_tipo_documento == 80 && $cuit == "00000000000000000000") $cuit = "00000000020111111112";
      $str.= $cuit;

      // Campo 8: IDENTIFICACION DEL COMPRADOR
      $cliente = trim($r->cliente);
      if (empty($cliente)) $cliente = "Consumidor Final";
      $str.= substr(str_pad($cliente,30," ",STR_PAD_RIGHT),0,30);
      
      // Campo 9: IMPORTE TOTAL DE LA OPERACION
      // TODO: IMPORTANTISIMO!!! INCLUIR LAS PERCEPCIONES DENTRO DEL TOTAL GENERAL
      $r->total = $r->total + $r->percep_viajes; // + $r->percepcion_ib;
      $str.= str_pad(str_replace(".00","",abs($r->total*100)),15,"0",STR_PAD_LEFT);
      
      // Campo 10: Total de conceptos que no integran el precio neto gravado
      $str.= "000000000000000";
      
      // Campo 11: Percepcion a No Categorizados
      $str.= "000000000000000";
      
      // Campo 12: Importe operaciones exentas
      $str.= "000000000000000";
      
      // Campo 13: Importe de percepciones a cuenta de impuestos nacionales
      $str.= str_pad(str_replace(".00","",abs($r->percep_viajes*100)),15,"0",STR_PAD_LEFT);
      
      // Campo 14: Importe de percepciones de ing brutos
      $str.= str_pad(str_replace(".00","",abs($r->percepcion_ib*100)),15,"0",STR_PAD_LEFT);
      
      // Campo 15: Percepciones municipales
      $str.= "000000000000000";
      
      // Campo 16: Impuestos Internos
      $str.= "000000000000000";
      
      // Campo 17: Codigo de Moneda
      $str.= "PES";
      
      // Campo 18: Tipo de cambio
      $str.= "0001000000";
      
      $usa_alicuota_exento = 0;
      // Campo 19: Cantidad de Alicuotas de IVA
      $sql = "SELECT id_alicuota_iva, SUM(neto) AS neto, SUM(iva) AS iva ";
      $sql.= "FROM facturas_iva ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_factura >= $r->id_desde ";
      $sql.= "AND id_factura <= $r->id_hasta ";
      $sql.= "AND id_punto_venta = $r->id_punto_venta ";
      $sql.= "GROUP BY id_alicuota_iva ";
      $qq = $this->db->query($sql);
      $str.= $qq->num_rows();        
      foreach($qq->result() as $rr) {
        if ($rr->id_alicuota_iva == 3) { $usa_alicuota_exento = 1; break; }
      }
      
      // Campo 20: Codigo de Operacion
      if ($r->iva == 0 || $usa_alicuota_exento = 1) {
        // Si el IVA del comprobante es cero, hay que especificar el codigo de operacion
        // Z - Exportaciones a zona franca
        // X - Exportaciones al exterior
        // E - Operaciones exentas
        // N - No gravado
        $str.= "N";
      } else {
        // Si tiene IVA, se deja en blanco
        $str.= " ";  
      }
      
      // Campo 21: Otros tributos
      $str.= "000000000000000";
      
      // Campo 22: Fechas de vencimiento de pago
      $str.= "00000000";
      
      $str.= "\r\n";
      
      // ALICUOTAS

      foreach($qq->result() as $rr) {
        
        // Campo 1: TIPO DE COMPROBANTE
        $str_alicuotas.= str_pad($r->id_tipo_comprobante,3,"0",STR_PAD_LEFT);
        
        // Campo 2: PUNTO DE VENTA
        $str_alicuotas.= str_pad($r->punto_venta,5,"0",STR_PAD_LEFT);
        
        // Campo 3: NUMERO DE COMPROBANTE
        $str_alicuotas.= str_pad($r->numero_desde,20,"0",STR_PAD_LEFT);
        
        // Campo 4: Importe Neto
        $str_alicuotas.= str_pad(str_replace(".00","",abs($rr->neto*100)),15,"0",STR_PAD_LEFT);
        
        // Campo 5 Alicuota de IVA
        $str_alicuotas.= str_pad($rr->id_alicuota_iva,4,"0",STR_PAD_LEFT);
        
        // Campo 6: Impuesto Liquidado
        $str_alicuotas.= str_pad(str_replace(".00","",abs($rr->iva*100)),15,"0",STR_PAD_LEFT);
        
        $str_alicuotas.= "\r\n";  
      }

    }

    return array(
      "comprobantes"=>$str,
      "alicuotas"=>$str_alicuotas,
    );

  }


  private function regimen_informacion_zetas($conf) {

    $id_empresa = $conf["id_empresa"];
    $fecha_desde = $conf["fecha_desde"];
    $fecha_hasta = $conf["fecha_hasta"];
    $id_razon_social = (isset($conf["id_razon_social"])) ? $conf["id_razon_social"] : 0;
    $id_punto_venta = parent::get_get("id_punto_venta",0);

    // id_tipo_comprobante de ZETAS es TICKET FB - NO PERMITE '80' TICKET Z
    $sql = "SELECT F.*, ";
    $sql.= " F.comp_desde AS numero_desde, ";
    $sql.= " F.comp_hasta AS numero_hasta, ";
    $sql.= " 0 AS percep_viajes, ";
    $sql.= " 0 AS percepcion_ib, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= " IF(C.id_tipo_documento IS NULL,'99',C.id_tipo_documento) AS id_tipo_documento, ";
    $sql.= " IF(C.cuit IS NULL,'',C.cuit) AS cuit, ";
    $sql.= " DATE_FORMAT(F.fecha,'%Y%m%d') AS fecha ";
    $sql.= "FROM zetas F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN puntos_venta PV ON (F.id_punto_venta = PV.id AND F.id_empresa = PV.id_empresa) ";
    $sql.= "LEFT JOIN almacenes S ON (PV.id_sucursal = S.id AND PV.id_empresa = S.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.fecha >= '$fecha_desde' ";
    $sql.= "AND F.fecha <= '$fecha_hasta' ";
    $sql.= "AND F.total != 0 "; // Los zetas en cero no los mostramos
    if ($id_punto_venta > 0) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    if ($id_razon_social != 0) $sql.= "AND S.id_razon_social = $id_razon_social ";
    $sql.= "ORDER BY F.fecha ASC, F.punto_venta ASC ";

    $filas = $this->db->query($sql);

    $numeros = array();

    $str = "";
    $str_alicuotas = "";
    foreach($filas->result() as $r) {
      
      // Campo 1: FECHA
      $str.= $r->fecha;
      
      // Campo 2: TIPO DE COMPROBANTE
      $str.= str_pad($r->id_tipo_comprobante,3,"0",STR_PAD_LEFT);
      
      // Campo 3: PUNTO DE VENTA
      $str.= str_pad($r->punto_venta,5,"0",STR_PAD_LEFT);

      if ($r->numero_desde == 0 && $r->numero != 0) $r->numero_desde = $r->numero;
      if ($r->numero_hasta == 0 && $r->numero != 0) $r->numero_hasta = $r->numero;
      
      // Campo 4: NUMERO DE COMPROBANTE DESDE
      if ($r->numero_desde == 0) {
        $r->numero_desde = (isset($numeros[$r->punto_venta]) ? $numeros[$r->punto_venta] : 1);
      }
      $numeros[$r->punto_venta] = $r->numero_hasta+1;
      $str.= str_pad($r->numero_desde,20,"0",STR_PAD_LEFT);
      
      // Campo 5: NUMERO DE COMPROBANTE HASTA
      $str.= str_pad($r->numero_hasta,20,"0",STR_PAD_LEFT);
      
      // Campo 6: CODIGO DE DOCUMENTO DE COMPRADOR
      if ($r->id_tipo_documento == 0) $r->id_tipo_documento = 80;

      $str.= str_pad($r->id_tipo_documento,2,"0",STR_PAD_LEFT);
      
      // Campo 7: NUMERO DE IDENTIFICACION DEL COMPRADOR
      $cuit = trim($r->cuit);
      $cuit = str_replace("-","",$cuit);
      if ($r->id_tipo_documento == 80) {
        // Ponemos un CUIT generico
        if (!validarCUIT($r->cuit)) $cuit = "20111111112";
      }
      $cuit = str_pad($cuit,20,"0",STR_PAD_LEFT); // Nro CUIT
      if ($r->id_tipo_documento == 80 && $cuit == "00000000000000000000") $cuit = "00000000020111111112";
      $str.= $cuit;

      // Campo 8: IDENTIFICACION DEL COMPRADOR
      $cliente = trim($r->cliente);
      if (empty($cliente)) $cliente = "Consumidor Final";
      $str.= substr(str_pad($cliente,30," ",STR_PAD_RIGHT),0,30);
      
      // Campo 9: IMPORTE TOTAL DE LA OPERACION
      // TODO: IMPORTANTISIMO!!! INCLUIR LAS PERCEPCIONES DENTRO DEL TOTAL GENERAL
      $r->total = $r->total + $r->percep_viajes; // + $r->percepcion_ib;
      $str.= str_pad(str_replace(".00","",abs($r->total*100)),15,"0",STR_PAD_LEFT);
      
      // Campo 10: Total de conceptos que no integran el precio neto gravado
      $str.= "000000000000000";
      
      // Campo 11: Percepcion a No Categorizados
      $str.= "000000000000000";
      
      // Campo 12: Importe operaciones exentas
      $str.= "000000000000000";
      
      // Campo 13: Importe de percepciones a cuenta de impuestos nacionales
      $str.= str_pad(str_replace(".00","",abs($r->percep_viajes*100)),15,"0",STR_PAD_LEFT);
      
      // Campo 14: Importe de percepciones de ing brutos
      $str.= str_pad(str_replace(".00","",abs($r->percepcion_ib*100)),15,"0",STR_PAD_LEFT);
      
      // Campo 15: Percepciones municipales
      $str.= "000000000000000";
      
      // Campo 16: Impuestos Internos
      $str.= "000000000000000";
      
      // Campo 17: Codigo de Moneda
      $str.= "PES";
      
      // Campo 18: Tipo de cambio
      $str.= "0001000000";
      
      $usa_alicuota_exento = 0;
      // Campo 19: Cantidad de Alicuotas de IVA
      $cantidad_alicuotas = 0;
      if ($r->iva > 0) $cantidad_alicuotas++;
      if ($r->iva_105 > 0) $cantidad_alicuotas++;
      if ($r->neto_0 > 0) {
        $cantidad_alicuotas++;
        $usa_alicuota_exento = 1;
      }
      $str.= $cantidad_alicuotas;
      
      // Campo 20: Codigo de Operacion
      if ($r->iva == 0 || $usa_alicuota_exento = 1) {
        // Si el IVA del comprobante es cero, hay que especificar el codigo de operacion
        // Z - Exportaciones a zona franca
        // X - Exportaciones al exterior
        // E - Operaciones exentas
        // N - No gravado
        $str.= "N";
      } else {
        // Si tiene IVA, se deja en blanco
        $str.= " ";  
      }
      
      // Campo 21: Otros tributos
      $str.= "000000000000000";
      
      // Campo 22: Fechas de vencimiento de pago
      $str.= "00000000";
      
      $str.= "\r\n";
      

      // TODO: SOLO ESTAMOS USANDO UNA ALICUOTA DEL 21%

      if ($r->iva > 0) {

        // Campo 1: TIPO DE COMPROBANTE
        $str_alicuotas.= str_pad($r->id_tipo_comprobante,3,"0",STR_PAD_LEFT);
        // Campo 2: PUNTO DE VENTA
        $str_alicuotas.= str_pad($r->punto_venta,5,"0",STR_PAD_LEFT);
        // Campo 3: NUMERO DE COMPROBANTE
        $str_alicuotas.= str_pad($r->numero_desde,20,"0",STR_PAD_LEFT);
        // Campo 4: Importe Neto
        $str_alicuotas.= str_pad(str_replace(".00","",abs($r->neto*100)),15,"0",STR_PAD_LEFT);
        // Campo 5 Alicuota de IVA
        $str_alicuotas.= "0005"; // TODO: Esta prefijado a 21%
        // Campo 6: Impuesto Liquidado
        $str_alicuotas.= str_pad(str_replace(".00","",abs($r->iva*100)),15,"0",STR_PAD_LEFT);
        $str_alicuotas.= "\r\n";  
      }

      if ($r->neto_105 > 0) {

        // Campo 1: TIPO DE COMPROBANTE
        $str_alicuotas.= str_pad($r->id_tipo_comprobante,3,"0",STR_PAD_LEFT);
        // Campo 2: PUNTO DE VENTA
        $str_alicuotas.= str_pad($r->punto_venta,5,"0",STR_PAD_LEFT);
        // Campo 3: NUMERO DE COMPROBANTE
        $str_alicuotas.= str_pad($r->numero_desde,20,"0",STR_PAD_LEFT);
        // Campo 4: Importe Neto
        $str_alicuotas.= str_pad(str_replace(".00","",abs($r->neto_105*100)),15,"0",STR_PAD_LEFT);
        // Campo 5 Alicuota de IVA
        $str_alicuotas.= "0004"; // TODO: Esta prefijado a 10.5%
        // Campo 6: Impuesto Liquidado
        $str_alicuotas.= str_pad(str_replace(".00","",abs($r->iva_105*100)),15,"0",STR_PAD_LEFT);
        $str_alicuotas.= "\r\n";  
      }

      if ($r->neto_0 > 0) {

        // Campo 1: TIPO DE COMPROBANTE
        $str_alicuotas.= str_pad($r->id_tipo_comprobante,3,"0",STR_PAD_LEFT);
        // Campo 2: PUNTO DE VENTA
        $str_alicuotas.= str_pad($r->punto_venta,5,"0",STR_PAD_LEFT);
        // Campo 3: NUMERO DE COMPROBANTE
        $str_alicuotas.= str_pad($r->numero_desde,20,"0",STR_PAD_LEFT);
        // Campo 4: Importe Neto
        $str_alicuotas.= str_pad(str_replace(".00","",abs($r->neto_0*100)),15,"0",STR_PAD_LEFT);
        // Campo 5 Alicuota de IVA
        $str_alicuotas.= "0003"; // EXENTO
        // Campo 6: Impuesto Liquidado
        $str_alicuotas.= str_pad("",15,"0",STR_PAD_LEFT);
        $str_alicuotas.= "\r\n";  
      }
    }

    return array(
      "comprobantes"=>$str,
      "alicuotas"=>$str_alicuotas,
    );

  }

  function regimen_informacion($fecha_desde,$fecha_hasta,$archivo="cbte",$id_razon_social=0) {
    
    $id_empresa = parent::get_empresa();
    // Acomodamos los datos de entrada
    $this->load->helper("fecha_helper");
    $this->load->helper("cuit_helper");
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    $anio = substr($fecha_desde,0,4);
    $mes = substr($fecha_desde,5,2);

    if ($id_empresa == 135) {

      // FLAMINGO TIENE LOS DOS
      $salida = $this->regimen_informacion_zetas(array(
        "id_empresa"=>$id_empresa,
        "fecha_desde"=>$fecha_desde,
        "fecha_hasta"=>$fecha_hasta,
        "id_razon_social"=>$id_razon_social,
      ));
      $str = $salida["comprobantes"];
      $str_alicuotas = $salida["alicuotas"];
      $salida = $this->regimen_informacion_facturas(array(
        "id_empresa"=>$id_empresa,
        "fecha_desde"=>$fecha_desde,
        "fecha_hasta"=>$fecha_hasta,
        "id_razon_social"=>$id_razon_social,
      ));
      $str .= $salida["comprobantes"];
      $str_alicuotas .= $salida["alicuotas"];

    } else {

      $sql = "SELECT * FROM zetas WHERE id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $salida = $this->regimen_informacion_zetas(array(
          "id_empresa"=>$id_empresa,
          "fecha_desde"=>$fecha_desde,
          "fecha_hasta"=>$fecha_hasta,
          "id_razon_social"=>$id_razon_social,
        ));
      } else {
        $salida = $this->regimen_informacion_facturas(array(
          "id_empresa"=>$id_empresa,
          "fecha_desde"=>$fecha_desde,
          "fecha_hasta"=>$fecha_hasta,
          "id_razon_social"=>$id_razon_social,
        ));
      }
      $str = $salida["comprobantes"];
      $str_alicuotas = $salida["alicuotas"];
    }
    if ($archivo == "cbte") {
      header("Content-disposition: attachment; filename=LIBRO_IVA_DIGITAL_VENTAS_CBTE.txt");
      header("Content-type: application/octet-stream");
      echo $str;    
    } else {
      header("Content-disposition: attachment; filename=LIBRO_IVA_DIGITAL_VENTAS_ALICUOTAS.txt");
      header("Content-type: application/octet-stream");
      echo $str_alicuotas;      
    }
  }

  function importar_regimenes_informacion() {
    set_time_limit(0);
    $id_empresa = 121;
    $f_comprobantes = file_get_contents("comprobantes.txt");
    $f_alicuotas = file_get_contents("alicuotas.txt");
    $comprobantes = explode("\n", $f_comprobantes);
    $alicuotas = explode("\n", $f_alicuotas);
    $resultado = array();
    foreach($comprobantes as $c) {
      $anio = substr($c, 0, 4);
      $mes = substr($c, 4, 2);
      $dia = substr($c, 6, 2);
      $id_tipo_comprobante = substr($c, 8, 3);
      $punto_venta = substr($c, 11, 5);
      $numero = substr($c, 16, 20);
      $total = 0;
      $iva = 0;
      $neto = 0;
      foreach($alicuotas as $a) {
        $id_tipo_comprobante_alicuota = substr($a, 0, 3);
        $punto_venta_alicuota = substr($a, 3, 5);
        $numero_alicuota = substr($a, 8, 20);
        if ($id_tipo_comprobante_alicuota == $id_tipo_comprobante && $punto_venta == $punto_venta_alicuota && $numero == $numero_alicuota) {
          // Es el mismo componente, tenemos que sumarlo
          $neto_alicuota = intval(substr($a, 28, 15))/100;
          $iva_alicuota = intval(substr($a, 47, 15))/100;
          $neto += $neto_alicuota;
          $iva += $iva_alicuota;
          $total += $neto_alicuota + $iva_alicuota;
        }
      }
      $resultado[] = array(
        $anio.$mes.$dia,
        $id_tipo_comprobante,
        $punto_venta,
        intval($numero),
        $neto,
        $iva,
        $total,
      );
    }
    $header = array("Fecha","Tipo Comprobante","Punto Venta","Numero","Neto","IVA","Total");
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>date("d/m/Y"),
      "filename"=>"listado_ventas",
      "header"=>$header,
      "footer"=>array(),
      "datos"=>$resultado,
      "title"=>"Exportacion",
    ));
  }


  function exportar_percepcion_ganancias($fecha_desde, $fecha_hasta) {
  
    $str = "";
    $this->load->helper("cuit_helper");
    $this->load->helper("fecha_helper");
    $this->load->helper("encode_helper");
    $id_empresa = parent::get_empresa();
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    
    // Seleccionamos las facturas que se les aplico percepciones
    $sql = "SELECT F.*, IF(C.cuit IS NULL,'20111111112',C.cuit) AS cuit, IF(C.nombre IS NULL,'Consumidor Final',C.nombre) AS nombre ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.percep_viajes != 0 ";
    $sql.= "AND '$fecha_desde' <= F.fecha ";
    $sql.= "AND F.fecha <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      
      $str.= "01"; // Codigo del Comprobante: 1 = Factura
      $str.= fecha_es($r->fecha); // Fecha del comprobante
      $str.= str_pad($r->punto_venta,8,"0",STR_PAD_LEFT); // Nro Comprobante
      $str.= str_pad($r->numero,8,"0",STR_PAD_LEFT);
      $str.= str_pad(number_format($r->neto,2,",",""),16,"0",STR_PAD_LEFT); // Neto Factura
      $str.= "219"; // Cod Impuesto: 217 Ganancias
      $str.= "760"; // Cod Regimen
      $str.= "2";   // Cod Operacion: 1 = Retencion; 2 = Percepcion
      $str.= str_pad(number_format($r->neto,2,",",""),14,"0",STR_PAD_LEFT); // Base de CALCULO
      $str.= fecha_es($r->fecha); // Fecha Emision Retencion = Fecha comprobante
      $str.= "2 "; // Cod Condicion: 1 = Inscripto; 2 = No Inscripto
      $str.= "0";  // Ret aplicada a sujetos suspendidos segun
      $str.= str_pad(number_format(abs($r->percep_viajes),2,",",""),14,"0",STR_PAD_LEFT);
      $str.= "000,00"; // Porc de exclusion
      $str.= "0000000000"; // Fecha de emision del boletin
      $str.= "80"; // Tipo del doc retenido
      $r->cuit = validarCUIT($r->cuit) ? $r->cuit : "20-11111111-2";
      $str.= str_pad(str_replace("-","",$r->cuit),20," "); // Nro de doc retenido
      $str.= "0             "; // Nro Certificado Original
      $r->nombre = str_replace("\t", " ", $r->nombre);
      $r->nombre = toAscii($r->nombre);
      $str.= str_pad(substr($r->nombre,0,30),30," ",STR_PAD_RIGHT); // Denominacion del ordenante
      $str.= "0"; // Acrecentamiento
      $str.= "           "; // Cuit del pais retenido
      $str.= "           \r\n"; // Cuit del ordenante
    }

    // FLAMINGO ADEMAS TIENE PERCEPCIONES DEL IMPUESTO PAIS
    if ($id_empresa == 135) {
      $sql = "SELECT F.*, IF(C.cuit IS NULL,'20111111112',C.cuit) AS cuit, IF(C.nombre IS NULL,'Consumidor Final',C.nombre) AS nombre ";
      $sql.= "FROM facturas F ";
      $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
      $sql.= "WHERE F.id_empresa = $id_empresa ";
      $sql.= "AND F.custom_1 != '' ";
      $sql.= "AND '$fecha_desde' <= F.fecha ";
      $sql.= "AND F.fecha <= '$fecha_hasta' ";
      $q = $this->db->query($sql);
      foreach($q->result() as $r) {
        
        $str.= "01"; // Codigo del Comprobante: 1 = Factura
        $str.= fecha_es($r->fecha); // Fecha del comprobante
        $str.= str_pad($r->punto_venta,8,"0",STR_PAD_LEFT); // Nro Comprobante
        $str.= str_pad($r->numero,8,"0",STR_PAD_LEFT);
        $str.= str_pad(number_format($r->neto,2,",",""),16,"0",STR_PAD_LEFT); // Neto Factura
        $str.= "939"; // Cod Impuesto: Impuesto PAIS
        $str.= "991"; // Cod Regimen
        $str.= "2";   // Cod Operacion: 1 = Retencion; 2 = Percepcion
        $str.= str_pad(number_format($r->neto,2,",",""),14,"0",STR_PAD_LEFT); // Base de CALCULO
        $str.= fecha_es($r->fecha); // Fecha Emision Retencion = Fecha comprobante
        $str.= "2 "; // Cod Condicion: 1 = Inscripto; 2 = No Inscripto
        $str.= "0";  // Ret aplicada a sujetos suspendidos segun
        $str.= str_pad(number_format(abs($r->custom_1),2,",",""),14,"0",STR_PAD_LEFT);
        $str.= "000,00"; // Porc de exclusion
        $str.= "0000000000"; // Fecha de emision del boletin
        $str.= "80"; // Tipo del doc retenido
        $r->cuit = validarCUIT($r->cuit) ? $r->cuit : "20-11111111-2";
        $str.= str_pad(str_replace("-","",$r->cuit),20," "); // Nro de doc retenido
        $str.= "0             "; // Nro Certificado Original
        $r->nombre = str_replace("\t", " ", $r->nombre);
        $r->nombre = toAscii($r->nombre);
        $str.= str_pad(substr($r->nombre,0,30),30," ",STR_PAD_RIGHT); // Denominacion del ordenante
        $str.= "0"; // Acrecentamiento
        $str.= "           "; // Cuit del pais retenido
        $str.= "           \r\n"; // Cuit del ordenante
      }
    }
    
    // Descargamos el archivo
    $nombre = "Percepcion Ganancias.txt";
    header("Content-disposition: attachment; filename=$nombre");
    header("Content-type: application/octet-stream");  
    echo $str;
    }

    function modificar_codigo_activacion(){
      $codigo = parent::get_post("codigo",0);
      $id_factura = parent::get_post("id_factura",0);
      $sql = "UPDATE facturas SET codigo_activacion = '$codigo' WHERE id = $id_factura ";
      $this->db->query($sql);
      echo json_encode(array(
        "error"=>0,
      ));
    }
}