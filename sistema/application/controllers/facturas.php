<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';
require APPPATH.'/libraries/escpos/autoload.php';
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class Facturas extends REST_Controller {
  
  const DEBUG = 0;

  function editar_fecha() {
    $id = parent::get_post("id");
    $id_empresa = parent::get_post("id_empresa");
    $id_punto_venta = parent::get_post("id_punto_venta");
    $fecha = parent::get_post("fecha");
    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql($fecha);
    $sql = "UPDATE facturas SET fecha = '$fecha' WHERE id_empresa = $id_empresa AND id = $id AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);
    echo json_encode(array("error"=>0));
  }

  function comparar_precios_maximos($id_factura,$id_punto_venta) {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = parent::get_empresa();
    $this->load->model("Articulo_Model");
    $factura = $this->modelo->get($id_factura,$id_punto_venta);

    // Primero traemos todos los precios maximos
    $result = file_get_contents("http://www.varcreative.com/products.json");
    $r = json_decode($result,true);
    $articulos = array();
    for($i=0;$i<sizeof($r["result"]);$i++) {
      $p = $r["result"][$i];
      $codigo_barra = str_replace("-", "", $p["id_producto"]);
      $precio_maximo = (float)$p["Precio sugerido"];
      $c = new stdClass();
      $c->codigo_barra = $codigo_barra;
      $c->precio_maximo = $precio_maximo;
      $articulos[] = $c;
    }

    $salida = array();
    // Luego buscamos los items de la factura
    foreach($factura->items as $item) {
      $articulo = $this->Articulo_Model->get($item->id_articulo);

      // Buscamos en el array si existe
      foreach($articulos as $c) {
        if ($c->codigo_barra == $articulo->codigo_barra) {
          // Ahora comparamos el precio
          $obj = new stdClass();
          $obj->codigo = $articulo->codigo;
          $obj->codigo_barra = $articulo->codigo_barra;
          $obj->nombre = $articulo->nombre;
          $obj->precio_final_dto = $item->precio;
          $obj->precio_maximo = $c->precio_maximo;
          $obj->diferencia = $item->precio - $c->precio_maximo;
          $salida[] = $obj;
        }
      }
    }
    $this->load->view("reports/precios_maximos",array(
      "resultados"=>$salida,
    ));
  }

  function test() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = parent::get_empresa();
    $id_factura = parent::get_post("id_factura",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);

    $this->load->model("Configuracion_Model");
    if ($this->Configuracion_Model->es_local()) {
      // Si no dio error, es local y estamos convirtiendo, tenemos que controlar si fue subida
      // Si ya fue subida, tambien tendriamos que cambiar el CAE y fecha_vto en el servidor
      $this->load->model("Factura_Model");
      $factura = $this->Factura_Model->get($id_factura,$id_punto_venta,array(
        "id_empresa"=>$id_empresa
      ));
      if ($factura->uploaded == 1) {
        $fields = array(
          "id_empresa"=>$id_empresa,
          "id_factura"=>$id_factura,
          "id_punto_venta"=>$id_punto_venta,
          "id_tipo_comprobante"=>$factura->id_tipo_comprobante,
          "cae"=>$factura->cae,
          "fecha_vto"=>$factura->fecha_vto,
          "numero"=>$factura->numero,
          "comprobante"=>$factura->comprobante,
        );
        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, "https://www.varcreative.com/sistema/facturas/function/actualizar_cae_servidor/");
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
      }
    }

    echo json_encode($result);
  }  

  function consultar_afip() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = 229;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;
    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE fecha >= '2019-10-01' AND fecha <= '2019-10-31' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "AND id_tipo_comprobante < 900 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $s = $fe->consultar($r->id_tipo_comprobante,$r->numero,$r->punto_venta);
      if (isset($s->ResultGet->ImpTotal)) {
        $total = $s->ResultGet->ImpTotal;
        $neto = $s->ResultGet->ImpNeto;
        $iva = $s->ResultGet->ImpIVA;
        $sql = "UPDATE facturas SET ";
        $sql.= "custom_1 = '$total', ";
        $sql.= "custom_2 = '$neto', ";
        $sql.= "custom_3 = '$iva' ";
        $sql.= "WHERE id = $r->id AND id_punto_venta = $r->id_punto_venta AND id_empresa = $r->id_empresa ";
        $this->db->query($sql);
        echo "[$r->id - $r->id_punto_venta] TOTAL: $r->total ($total) | NETO: $r->neto ($neto) | IVA: $r->iva ($iva) <br/>";
      } else {
        echo "No encontro registros para $r->id <br/>";
      }
    }
  }

  // IPN sobre el pago de una factura de MercadoPago
  function ipn_mercadopago($id_empresa = 0,$id_factura = 0,$id_punto_venta = 0) {
    set_time_limit(0);
    // Si no esta definido el ID, devolvemos ERROR
    if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
      http_response_code(400);
      exit();
    }
    include_once("/home/ubuntu/data/models/mercadopago.php");
    $this->load->model("Log_Model");
    $this->load->model("Email_Template_Model");
    $bcc_array = array("basile.matias99@gmail.com");
    require APPPATH.'libraries/Mandrill/Mandrill.php';    

    // Si la factura ya fue pagada, no volvemos a ejecutar el codigo
    $sql = "SELECT * FROM facturas WHERE id = $id_factura AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      // La factura no existe
      http_response_code(400);
      exit();
    }
    $factura = $q->row();
    if ($factura->id_tipo_estado == 4) {
      // La factura ya fue pagada con mercadopago, no volvemos a procesar el pago
      http_response_code(200);
      exit();
    }

    // Consultamos la configuracion de pago de la empresa
    $q = $this->db->query("SELECT * FROM medios_pago_configuracion WHERE id_empresa = $id_empresa ");
    $medio = $q->row();
    if (empty($medio->mp_client_id) || empty($medio->mp_client_secret)) {
      http_response_code(400);
      exit();
    }
    $mp = new MP($medio->mp_client_id, $medio->mp_client_secret);  

    http_response_code(200);

    // Get the payment and the corresponding merchant_order reported by the IPN.
    if(isset($_GET["topic"]) && $_GET["topic"] == 'payment') {
      $payment_info = $mp->get("/collections/notifications/" . $_GET["id"]);
      $merchant_order_info = $mp->get("/merchant_orders/" . $payment_info["response"]["collection"]["merchant_order_id"]);
    // Get the merchant_order reported by the IPN.
    } else if(isset($_GET["topic"]) && $_GET["topic"] == 'merchant_order'){
      $merchant_order_info = $mp->get("/merchant_orders/" . $_GET["id"]);
    }

    // La external reference tiene el ID del pedido
    $salida = print_r($merchant_order_info["response"],true);
    $salida.= print_r($_GET,true);
    $this->Log_Model->imprimir(array(
      "id_empresa"=>$id_empresa,
      "file"=>"ipn_mercadopago.txt",
      "texto"=>$salida,
    ));

    if (isset($merchant_order_info) & $merchant_order_info["status"] == 200) {
      // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items 
      $paid_amount = 0;
      $pago_pendiente = 0;

      // Sumamos cada pago realizado
      foreach ($merchant_order_info["response"]["payments"] as  $payment) {
        if ($payment['status'] == 'approved'){
          $paid_amount += $payment['transaction_amount'];
        } else if ($payment['status'] == 'pending') {
          $pago_pendiente += $payment['transaction_amount'];
        }
      }

      if ($paid_amount >= $merchant_order_info["response"]["total_amount"]){
        // Primero de todo, finalizamos el pedido y actualizamos los datos
        $sql = "UPDATE facturas SET ";
        $sql.= "id_tipo_estado = 4, "; // ESTADO: PAGO CON MERCADOPAGO
        $sql.= "pagada = 1, pago = -$paid_amount, ";
        $sql.= "custom_10 = '".$_GET["id"]."', "; // Guardamos el ID de MercadoPago para consultar por las dudas la transaccion
        $sql.= "codigo_autorizacion = '".$payment_info["response"]["collection"]["authorization_code"]."' ";
        $sql.= "WHERE id = $id_factura ";
        $sql.= "AND id_empresa = $id_empresa ";
        $sql.= "AND id_punto_venta = $id_punto_venta ";
        $this->db->query($sql);

        $this->load->model("Empresa_Model");
        $empresa = $this->Empresa_Model->get($id_empresa);

        $this->load->model("Factura_Model");
        $factura = $this->Factura_Model->get($id_factura,$id_punto_venta,array(
          "id_empresa"=>$id_empresa
        ));

        $bcc_array[] = $empresa->email; 

        if ($id_empresa == 936) {
          $this->Empresa_Model->actualizar_pago_empresa(array(
            "id_empresa"=>$factura->id_cliente
          ));
          $template = $this->Email_Template_Model->get_by_key("comprobante-pago",936);
          $asunto = $template->nombre;
          $texto = $template->texto;
        } else {
          $asunto = "Aviso Pago";
          $texto = "La factura $factura->comprobante de $factura->cliente ha sido pagada.";
        }
        mandrill_send(array(
          "to"=>$empresa->email,
          "from"=>MAIL_FROM_ADDRESS,
          "from_name"=>$empresa->nombre,
          "subject"=>$asunto,
          "body"=>$texto,
          "bcc"=>$bcc_array,
        ));                  

      }
      echo "OK";
    }
  }

  function arreglar_numeros() {
    /*
    $sql = "SELECT * FROM facturas WHERE numero < 6200 and numero >= 6056 and id_tipo_comprobante = 1 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $numero = $r->numero + 2;
      $this->modelo->modificar_numero_comprobante(array(
        "id"=>$r->id,
        "id_punto_venta"=>$r->id_punto_venta,
        "letra"=>"A",
        "punto_venta"=>4,
        "id_empresa"=>$r->id_empresa,
        "numero"=>$numero,
      ));
    }
    */
    $sql = "SELECT * FROM facturas WHERE numero < 1826 and numero >= 1781 and id_tipo_comprobante = 6 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $numero = $r->numero + 2;
      $this->modelo->modificar_numero_comprobante(array(
        "id"=>$r->id,
        "id_punto_venta"=>$r->id_punto_venta,
        "letra"=>"B",
        "punto_venta"=>4,
        "id_empresa"=>$r->id_empresa,
        "numero"=>$numero,
      ));
    }
    echo "TERMINO";
  }


  function arreglar_costo_final() {
    set_time_limit(0);
    $id_empresa = 1325;
    $id_punto_venta = 2463;
    $sql = "SELECT * FROM facturas WHERE id_empresa = $id_empresa AND fecha >= '2020-08-01' AND id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    foreach($q->result() as $factura) {
      if ($id_empresa == 224 || $id_empresa == 249) {
        $sql = "UPDATE facturas_items FI ";
        $sql.= " INNER JOIN articulos_precios_sucursales A ON (FI.id_empresa = A.id_empresa AND A.id_sucursal = $factura->id_sucursal AND FI.id_articulo = A.id_articulo) ";
        $sql.= " INNER JOIN facturas F ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND FI.id_factura = F.id) ";
        $sql.= "SET FI.costo_final = (A.costo_final * FI.cantidad * (IF(A.moneda = 1,1,F.cotizacion_dolar))) ";
        $sql.= "WHERE FI.id_factura = $factura->id AND FI.id_empresa = $id_empresa AND FI.id_punto_venta = $id_punto_venta ";
      } else {
        $sql = "UPDATE facturas_items FI ";
        $sql.= " INNER JOIN articulos A ON (FI.id_empresa = A.id_empresa AND FI.id_articulo = A.id) ";
        $sql.= " INNER JOIN facturas F ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND FI.id_factura = F.id) ";
        $sql.= "SET FI.costo_final = (A.costo_final * FI.cantidad * (IF(A.moneda = 1,1,F.cotizacion_dolar))) ";
        $sql.= "WHERE FI.id_factura = $factura->id AND FI.id_empresa = $id_empresa AND FI.id_punto_venta = $id_punto_venta ";
      }
      $this->db->query($sql);

      $sql = "UPDATE facturas F ";
      $sql.= "SET F.costo_final = (";
      $sql.= "  SELECT SUM(FI.costo_final) AS costo_final FROM facturas_items FI WHERE FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND FI.id_factura = F.id AND FI.anulado = 0 ";
      $sql.= ") WHERE F.id = $factura->id AND F.id_empresa = $id_empresa AND F.id_punto_venta = $id_punto_venta ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }


  function __construct() {
    parent::__construct();
    $this->load->model('Factura_Model', 'modelo');
  }

  // Sobrescribimos este metodo agregandole como parametro obligatorio el punto de venta
  function change_property() {
    $id_empresa = parent::get_post("id_empresa",parent::get_empresa());
    $id = parent::get_post("id",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);
    $attribute = parent::get_post("attribute","");
    $value = parent::get_post("value","");
    if (!is_numeric($id) || !is_numeric($id_punto_venta) || !is_numeric($id_empresa)) parent::send_error("Error en los parametros.");
    
    // TODO: Los unicos parametros permitidos por el momento son:
    // custom_6 = Estado de envio
    // id_tipo_estado
    if (!($attribute == "custom_6" || $attribute == "tipo_pago" || $attribute == "id_tipo_estado")) parent::send_error("Error en el parametro attribute.");

    $sql = "UPDATE facturas SET $attribute = '$value' ";
    if ($attribute == "tipo_pago")  {
      // Si estamos cambiando el tipo de pago
      $sql.= ", cta_cte = 0, tarjeta = 0, efectivo = 0, cheque = 0 ";
      if ($value == "C") $sql.= ", cta_cte = total ";
      else if ($value == "E") $sql.= ", efectivo = total ";
      else if ($value == "O") $sql.= ", efectivo = total ";
      else if ($value == "B") $sql.= ", tarjeta = total ";
      else if ($value == "T") $sql.= ", tarjeta = total ";
      else if ($value == "H") $sql.= ", cheque = total ";
    }
    $sql.= "WHERE id = '$id' AND id_punto_venta = '$id_punto_venta' AND id_empresa = '$id_empresa' ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function arreglar_precios_descuentos() {

    $sql = "UPDATE facturas_items FI ";
    $sql.= "INNER JOIN facturas F ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND FI.id_factura = F.id) ";
    $sql.= "INNER JOIN articulos_precios_sucursales APS ON (APS.id_empresa = FI.id_empresa AND APS.id_articulo = FI.id_articulo AND APS.id_sucursal = F.id_sucursal) ";
    $sql.= "SET FI.precio = 0 ";
    $sql.= "WHERE FI.tipo_cantidad = 'B' AND FI.id_empresa = 249 ";
    $this->db->query($sql);

    // Por otro, ponemos los precios que corresponden a la sucursal
    $sql = "UPDATE facturas_items FI ";
    $sql.= "INNER JOIN facturas F ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND FI.id_factura = F.id) ";
    $sql.= "INNER JOIN articulos_precios_sucursales APS ON (APS.id_empresa = FI.id_empresa AND APS.id_articulo = FI.id_articulo AND APS.id_sucursal = F.id_sucursal) ";
    $sql.= "SET FI.precio = APS.precio_final_dto ";
    $sql.= "WHERE FI.tipo_cantidad = 'B' AND FI.id_empresa = 249 AND FI.precio = 0 ";
    $this->db->query($sql);

    // Por un lado, ponemos los mismos precios que un item de la misma factura
    $cant_updates = 0;
    $sql = "SELECT FI.* FROM facturas_items FI ";
    $sql.= "INNER JOIN facturas F ON (FI.id_empresa = F.id_empresa AND FI.id_factura = F.id AND FI.id_punto_venta = F.id_punto_venta) ";
    $sql.= "WHERE FI.id_empresa = 249 AND F.fecha >= '2018-11-01' AND FI.tipo_cantidad = 'B' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "SELECT precio FROM facturas_items ";
      $sql.= "WHERE id_empresa = $row->id_empresa ";
      $sql.= "AND id_punto_venta = $row->id_punto_venta ";
      $sql.= "AND id_factura = $row->id_factura ";
      $sql.= "AND id_articulo = $row->id_articulo ";
      $sql.= "AND tipo_cantidad = '' ";
      $sql.= "AND id != $row->id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $sql = "UPDATE facturas_items SET precio = $rr->precio ";
        $sql.= "WHERE id = $row->id ";
        $sql.= "AND id_empresa = $row->id_empresa ";
        $sql.= "AND id_punto_venta = $row->id_punto_venta ";
        $sql.= "AND id_factura = $row->id_factura ";
        $sql.= "AND id_articulo = $row->id_articulo ";
        $sql.= "AND tipo_cantidad = 'B' ";
        echo $sql."<br/>";
        $this->db->query($sql);
        $cant_updates++;
      }
    }

    echo "CANT UPDATES: $cant_updates";
  }

  function renumerar() {
    set_time_limit(0);
    $id_empresa = 121;
    $id_punto_venta = 1004;
    $fecha_desde = '2018-09-01';
    $fecha_hasta = '2018-09-30';
    $id_tipo_comprobante = 1;
    $base = 126147;
    $sql = "SELECT id FROM facturas WHERE id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta ";
    $sql.= "AND id_tipo_comprobante = $id_tipo_comprobante AND fecha >= '$fecha_desde' AND fecha <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "UPDATE facturas SET numero = $base ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta AND id = $row->id ";
      $this->db->query($sql);
      $base++;
    }
    echo "TERMINO";
  }

  function recalcular_facturas_iva() {
    // Solo recalculamos aquellos que no existen
    $id_empresa = 121;
    $ii = 0;
    $sql = "SELECT F.id, F.id_punto_venta FROM facturas F ";
    $sql.= "WHERE F.id_empresa = $id_empresa AND F.id_tipo_comprobante != 999 AND F.fecha >= '2020-01-01' AND F.fecha <= '2020-01-31' ";
    $sql.= "AND NOT EXISTS (SELECT 1 FROM facturas_iva FI WHERE FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "SELECT id_tipo_alicuota_iva, SUM(total_sin_iva) AS neto, SUM(iva) AS iva, SUM(total_con_iva) AS total ";
      $sql.= "FROM facturas_items WHERE id_factura = $row->id and id_punto_venta = $row->id_punto_venta and id_empresa = $id_empresa ";
      $sql.= "AND anulado = 0 ";
      $sql.= "GROUP BY id_tipo_alicuota_iva ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        //$this->db->query("DELETE FROM facturas_iva WHERE id_empresa = $id_empresa AND id_punto_venta = $row->id_punto_venta AND id_factura = $row->id ");
        $total = 0;
        $neto = 0;
        $iva = 0;
        foreach($qq->result() as $fi) {
          $sql = "INSERT INTO facturas_iva (id_empresa, id_factura, id_alicuota_iva, id_punto_venta, neto, iva, uploaded) VALUES ( ";
          $sql.= " '$id_empresa', '$row->id', '$fi->id_tipo_alicuota_iva', '$row->id_punto_venta', '$fi->neto', '$fi->iva', 0 ) ";
          $this->db->query($sql);
          $ii++;
          $total += $fi->total;
          $neto += $fi->neto;
          $iva += $fi->iva;
        }
        // Actualizamos los totales del comprobante
        $sql = "UPDATE facturas SET ";
        $sql.= " total = $total, subtotal = '$neto', neto = '$neto', iva = '$iva' ";
        $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta ";
        $this->db->query($sql);
      }
    }
    echo "TERMINO $ii";
  }

  function eliminar_duplicados() {
    set_time_limit(0);
    $this->load->model("Stock_Model");
    $this->load->model("Almacen_Model");
    $this->load->model("Empresa_Model");
    $this->load->model("Articulo_Model");

    $ids_empresas = array(264);
    foreach($ids_empresas as $id_empresa) {
      $sql = "SELECT id, id_punto_venta, fecha, hora, total, id_cliente, id_vendedor ";
      $sql.= "FROM facturas ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $q_fact = $this->db->query($sql);
      foreach($q_fact->result() as $factura) {
        $sql = "SELECT id, id_sucursal FROM facturas ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
        $sql.= "AND id_cliente = $factura->id_cliente ";
        $sql.= "AND id_vendedor = $factura->id_vendedor ";
        $sql.= "AND fecha = '$factura->fecha' ";
        $sql.= "AND hora = '$factura->hora' ";
        $sql.= "AND id != $factura->id ";
        $q_fact_2 = $this->db->query($sql);
        if ($q_fact_2->num_rows()>0) {
          $fact2 = $q_fact_2->row();

          // Obtenemos los items de esa factura

          $sql = "SELECT * FROM facturas_items ";
          $sql.= "WHERE id_factura = $fact2->id ";
          $sql.= "AND id_empresa = $id_empresa ";
          $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
          $sql.= "AND id_articulo != 0 ";
          $q = $this->db->query($sql);
          foreach($q->result() as $row) {
            $id_variante = (isset($row->id_variante) ? $row->id_variante : 0);
            if ($row->custom_3 == 1) {
              // Si el stock estaba reservado, lo sacamos
              $this->Stock_Model->reservar(array(
                "cantidad"=>($row->cantidad * -1),
                "id_almacen"=>$fact2->id_sucursal,
                "id_articulo"=>$row->id_articulo,
                "id_variante"=>$id_variante,
                "fecha"=>$factura->fecha,
              ));
            } else {
              $obs = "Anulacion comprobante";
              $this->Stock_Model->agregar($row->id_articulo,$row->cantidad,$fact2->id_sucursal,$factura->fecha,$obs,0,$id_variante);
            }

            // Recalculamos el stock
            /*
            $this->Stock_Model->recalcular_stock(array(
              "id_articulo"=>$row->id_articulo,
              "id_sucursal"=>$fact2->id_sucursal,
              "id_empresa"=>$id_empresa,
            ));
            */
          }

          $sql = "DELETE FROM cupones_tarjetas WHERE id_factura = $fact2->id AND id_empresa = $id_empresa ";
          $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
          $this->db->query($sql);

          $sql = "DELETE FROM facturas_iva WHERE id_factura = $fact2->id AND id_empresa = $id_empresa ";
          $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
          $this->db->query($sql);

          $sql = "DELETE FROM facturas_items WHERE id_factura = $fact2->id AND id_empresa = $id_empresa ";
          $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
          $this->db->query($sql);

          $sql = "DELETE FROM facturas WHERE id = $fact2->id AND id_empresa = $id_empresa ";
          $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
          $this->db->query($sql);

          echo "ELIMINO FACTURA $id_empresa $factura->id_punto_venta $factura->fecha $factura->hora $factura->id_cliente $factura->id_vendedor <br/>";
        }
      }
    }
    echo "TERMINO";
  }

  // Calcula el IVA de los remitos o comprobantes seleccionados
  function mostrar_iva($ids) {

    $id_empresa = parent::get_empresa();
    $this->load->model("Tipo_Alicuota_Iva_Model");
    $alicuotas = $this->Tipo_Alicuota_Iva_Model->buscar(array(
      "offset"=>9999,
    ));
    if (sizeof($alicuotas) == 0) {
      $alicuotas = $this->Tipo_Alicuota_Iva_Model->buscar(array(
        "offset"=>9999,
        "id_empresa"=>0,
      ));      
    }

    $salida = array();
    foreach($alicuotas as $ali) {
      $r = new stdClass();
      $r->id_alicuota_iva = $ali->id;
      $r->nombre = $ali->nombre;
      $r->neto = 0;
      $r->iva = 0;
      $r->total = 0;
      $salida[] = $r;
    }
    
    // IDS tiene el formato (id).(id_punto_venta)-(id).(id_punto_venta)...
    $ides = explode("-", $ids);
    foreach($ides as $ide) {
      $campo = explode(".", $ide);
      $id = $campo[0];
      $id_punto_venta = $campo[1];
      $sql = "SELECT id_tipo_alicuota_iva, ";
      $sql.= " SUM(iva) AS iva, SUM(total_sin_iva) AS neto ";
      $sql.= "FROM facturas_items ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND anulado = 0 ";
      $sql.= "AND id_factura = $id ";
      $sql.= "AND id_punto_venta = $id_punto_venta ";
      $sql.= "GROUP BY id_tipo_alicuota_iva ";
      $q = $this->db->query($sql);
      foreach($q->result() as $row) {
        foreach($salida as $s) {
          if ($s->id_alicuota_iva == $row->id_tipo_alicuota_iva) {
            $s->neto = $s->neto + $row->neto;
            $s->iva = $s->iva + $row->iva;
            $s->total = $s->neto + $s->iva;
            break;
          }
        }
      }
    }

    // Limpiamos la salida sacando los id_alicuotas_iva que no corresponden
    $salida2 = array();
    foreach($salida as $row) {
      if ($row->neto != 0) {
        $salida2[] = $row;
      }
    }

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;

    $header = $this->load->view("reports/iva/header",null,true);
    $data = array(
      "datos"=>$salida2,
      "empresa"=>$empresa,
      "header"=>$header,
    );
    $this->load->view("reports/iva/agrupado",$data);
  }

  function acomodar_numeros() {
    $desdes = array(
      "1"=>0,
      "6"=>1026,
    );
    $id_empresa = 135;
    $id_punto_venta = 205;
    $fecha = "2018-01-01";
    $sql = "SELECT * FROM facturas WHERE id_empresa = $id_empresa and fecha >= '$fecha' AND id_punto_venta = $id_punto_venta ";

    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $desde = $desdes[$row->id_tipo_comprobante];
      $sql = "UPDATE facturas SET numero = $desde, comprobante = 'B 0011-0000$desde' ";
      $sql.= "WHERE id_empresa = $id_empresa and id_punto_venta = $id_punto_venta AND id = $row->id ";
      $this->db->query($sql);
      echo $desde."<br/>";
      $desdes[$row->id_tipo_comprobante] = $desdes[$row->id_tipo_comprobante] + 1;
    }
    echo "TERMINO";
  }

  function sincronizar() {
    header('Access-Control-Allow-Origin: *');

    $this->load->model("Dispositivo_Model");
    $version = ($this->input->post("version") !== FALSE) ? $this->input->post("version") : 1;
    $dispositivo_string = $this->input->post("dispositivo");
    $id_vendedor = $this->input->post("id_vendedor");
    $id_empresa_asignada = parent::get_post("id_empresa_asignada",-1);
    $this->load->model("Vendedor_Model");

    if (!empty($dispositivo_string)) {
      $dispositivo = $this->Dispositivo_Model->get_by_dispositivo($dispositivo_string);
      if ($dispositivo === FALSE) {
        echo "0"; exit();
      }
      $id_empresa = $dispositivo->id_empresa;

      // Tomamos el vendedor
      $vendedor = $this->Vendedor_Model->get($dispositivo->id_vendedor,array("id_empresa"=>$id_empresa));
      $vendedor_nombre = ($vendedor === FALSE) ? "" : $vendedor->nombre;
      $id_vendedor = $dispositivo->id_vendedor;
      $id_punto_venta_vendedor = ($vendedor === FALSE) ? 0 : $vendedor->id_punto_venta;

    } elseif (!empty($id_vendedor)) {
      $id_vendedor = (int) $id_vendedor;
      $vendedor = $this->Vendedor_Model->get($id_vendedor,array(
        // Si es -1, se busca el vendedor sin filtrar por empresa. Si tiene un valor busca ese vendedor de esa empresa en particular
        "id_empresa"=>$id_empresa_asignada 
      ));
      if ($vendedor === FALSE) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Error: Vendedor no encontrador",
        ));
        return;        
      }
      $id_empresa = $vendedor->id_empresa;
      $vendedor_nombre = $vendedor->nombre;
      $id_punto_venta_vendedor = ($vendedor === FALSE) ? 0 : $vendedor->id_punto_venta;
    }
    $facturas = $this->input->post("facturas");
    if ($facturas === FALSE) {
      echo "0"; exit();
    }

    $sql = "SELECT * FROM empresas WHERE id = $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      echo "0"; exit();
    }
    $empresa = $q->row();
    $nombre_empresa = $empresa->nombre;

    $this->load->model("Cliente_Model");
    $this->load->model("Log_Model");
    $facturas = json_decode($facturas);
    $this->Log_Model->imprimir(array(
      "id_empresa"=>$id_empresa,
      "id_usuario"=>0,
      "file"=>date("Ymd")."_pedidos.txt",
      "texto"=>"========================\n\nDISPOSITIVO: $dispositivo_string \n\n".print_r($facturas,TRUE)
    ));

    $clientes = $this->input->post("clientes");
    if (!empty($clientes)) {
      $clientes = json_decode($clientes);
      foreach($clientes as $cliente) {
        $id_cliente = $cliente->id;
        unset($cliente->id);

        $existe_cliente = $this->Cliente_Model->buscar_por_nombre($cliente->nombre,array(
          "id_empresa"=>$id_empresa
        ));

        if ($existe_cliente === FALSE) {
          $cliente->codigo = $this->Cliente_Model->next(array(
            "id_empresa"=>$id_empresa
          ));
          $cliente->id_empresa = $id_empresa;
          $cliente->tipo = 0;
          $cliente->id_tipo_documento = 80;
          $cliente->activo = 1;
          $cliente->lista = 0;
          $cliente->id_vendedor = $id_vendedor;
          $cliente->forma_pago = "C";
          $cliente->fecha_inicial = date("Y-m-d");
          $cliente->fecha_ult_operacion = date("Y-m-d");
          $cliente->uploaded = 1;
          $this->db->insert("clientes",$cliente);
          $id_cliente_nuevo = $this->db->insert_id();
        } else {
          $id_cliente_nuevo = $existe_cliente->id;
        }

        // Debemos buscar las facturas que tienen ese ID de cliente para reemplazarlo con el ID nuevo creado
        foreach($facturas as $f) {
          if ($f->id_cliente == $id_cliente) $f->id_cliente = $id_cliente_nuevo;
        }
      }
    }


    $this->load->model("Articulo_Model");
    $this->load->helper("fecha_helper");

    // Tomamos el punto de venta por defecto
    $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
    $sql.= "FROM puntos_venta PV LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
    $sql.= "WHERE PV.id_empresa = $id_empresa ";
    if ($id_punto_venta_vendedor != 0) {
      $sql.= "AND PV.id = $id_punto_venta_vendedor ";
    } else if ($id_vendedor == 112 || $id_vendedor == 96) {
      // Di Piero / Lobos.. usa PV 3
      $sql.= "AND PV.id = 1647 ";
    } else {
      $sql.= "AND PV.por_default = 1 ";
    }
    $sql.= "LIMIT 0,1 ";
    $q_pv = $this->db->query($sql);
    if ($q_pv->num_rows()>0) {
      $pv = $q_pv->row();
      $id_punto_venta = $pv->id;
      $pv_numero = $pv->numero;      
      $tipo_punto_venta = $pv->tipo_impresion;
      $sucursal = $pv->sucursal;
    } else {
      $id_punto_venta = 0;
      $pv_numero = 0;
      $tipo_punto_venta = "";
      $sucursal = "";
    }

    // TODO: Por ahora se envia todo como remito, y se convierte a factura a mano
    $id_tipo_comprobante = 999;
    $tipo_comprobante = "Remito";
    $letra = "R";

    // Tomamos el proximo numero
    $sql = "SELECT * FROM numeros_comprobantes WHERE id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta AND id_tipo_comprobante = $id_tipo_comprobante LIMIT 0,1";
    $q_numero = $this->db->query($sql);
    $r_numero = $q_numero->row();
    $numero = $r_numero->ultimo + 1;

    foreach($facturas as $f) {

      $pesable = FALSE;
      $tiene_descuento = FALSE;

      $fecha_fact = (isset($f->fecha)) ? fecha_mysql($f->fecha) : date("Y-m-d");
      if (!isset($f->hora)) $f->hora = date("H:i:s");
      if (!isset($f->reparto)) $f->reparto = 1;
      if (($id_vendedor == 112 || $id_vendedor == 96 || $id_vendedor == 213) && $f->reparto == 1) $f->reparto = 15;
      if ($id_vendedor == 111 && $f->reparto == 1) $f->reparto = 23;
      $observaciones = (isset($f->observaciones)) ? $f->observaciones : "";
      $comprobante = $letra." ".str_pad($pv_numero,4,"0",STR_PAD_LEFT)."-".str_pad($numero,8,"0",STR_PAD_LEFT);

      // Controlamos si la factura ya existe para evitar el error de duplicados
      $sql = "SELECT * FROM facturas ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_punto_venta = $id_punto_venta ";
      $sql.= "AND id_cliente = $f->id_cliente ";
      $sql.= "AND id_vendedor = $id_vendedor ";
      $sql.= "AND fecha = '$fecha_fact' ";
      $sql.= "AND hora = '$f->hora' ";
      $q_repetido = $this->db->query($sql);
      if ($q_repetido->num_rows()>0) continue;

      $ivas = array();

      $cliente = $this->Cliente_Model->get($f->id_cliente,$id_empresa);
      $cliente_canasta_basica = false;
      if (($id_empresa == 229 || $id_empresa == 230 || $id_empresa == 1355) && $cliente->custom_5 == "1") $cliente_canasta_basica = true;

      $stamp = time();
      $sql = "INSERT INTO facturas (";
      $sql.= " id_cliente,id_empresa,id_punto_venta,id_vendedor,numero, punto_venta, ";
      $sql.= " id_tipo_estado,uploaded,fecha_reparto,reparto,";
      $sql.= " fecha,hora,comprobante,id_tipo_comprobante,observaciones,cliente, nueva, impresa, id_origen, tipo_punto_venta, empresa, tipo_comprobante, vendedor, sucursal, last_update ";
      $sql.= ") VALUES (";
      $sql.= " '$f->id_cliente','$id_empresa','$id_punto_venta','$id_vendedor','$numero', '$pv_numero', ";
      $sql.= " '$f->id_tipo_estado',1,'$fecha_fact','$f->reparto',";
      $sql.= " '$fecha_fact','$f->hora','$comprobante',$id_tipo_comprobante,'$observaciones','$cliente->nombre', 1, 0, 3, '$tipo_punto_venta', '$nombre_empresa', '$tipo_comprobante', '$vendedor_nombre', '$sucursal', '$stamp' ";
      $sql.= ")";
      $this->db->query($sql);
      $id_factura = $this->db->insert_id();
      $t_neto = 0; $t_iva = 0; $t_total = 0; $t_costo_final = 0;
      for($i=0;$i<sizeof($f->facturaItemsArray);$i++) {
        $item = $f->facturaItemsArray[$i];
        $item->porc_bonif = (isset($item->porc_bonif)) ? $item->porc_bonif : 0;
        if ($item->porc_bonif > 100) $item->porc_bonif = 100;
        $item->tipoCantidad = (isset($item->tipoCantidad)) ? $item->tipoCantidad : "";

        // Buscamos el articulo por el codigo
        $articulo = $this->Articulo_Model->get_by_codigo($item->codigoArticulo,array(
          "id_empresa"=>$id_empresa,
        ));
        if ($articulo === FALSE) continue;

        // Si el articulo y el cliente estan marcados
        $producto_exento = false;
        if (($id_empresa == 229 || $id_empresa == 230 || $id_empresa == 1355) && $articulo->custom_5 == "1" && $cliente_canasta_basica) {
          $producto_exento = true;
          $articulo->porc_iva = 0;
          $articulo->id_tipo_alicuota_iva = 3;
        }

        // Tenemos algun articulo pesable, hay que marcar la fila
        if ($articulo->no_totalizar_reparto == 1) $pesable = TRUE;

        if (empty($item->cantidad)) $item->cantidad = 1;

        // Dependiendo de la lista de cada cliente
        $precio_final = 0;
        if ($version > 1) {
          if ($item->lista == 0) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto : $articulo->precio_final_dto;
          } else if ($item->lista == 1) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto_2 : $articulo->precio_final_dto_2;
          } else if ($item->lista == 2) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto_3 : $articulo->precio_final_dto_3;
          } else if ($item->lista == 3) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto_4 : $articulo->precio_final_dto_4;
          } else if ($item->lista == 4) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto_5 : $articulo->precio_final_dto_5;
          } else if ($item->lista == 5) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto_6 : $articulo->precio_final_dto_6;
          }
        } else {
          if ($cliente->lista == 0) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto : $articulo->precio_final_dto;
          } else if ($cliente->lista == 1) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto_2 : $articulo->precio_final_dto_2;
          } else if ($cliente->lista == 2) {
            $precio_final = ($producto_exento) ? $articulo->precio_neto_3 : $articulo->precio_final_dto_3;
          }
        }

        if ($item->tipoCantidad == "Bonificado") {
          // Si es bonificacion, no tiene costo
          $articulo->costo_final = 0;
          $precio_neto = 0;
          $precio_final = 0;
          $item->porc_bonif = 100; // Se bonifica el 100% del costo
        }
        else if ($item->tipoCantidad == "Devolucion") {
          // Si es una devolucion, la cantidad es en negativo
          $item->cantidad = -$item->cantidad;
          // y no tiene costo
          $articulo->costo_final = 0;
          $precio_neto = 0;
          $precio_final = 0;
        }

        $porc_bonif = ((100 - $item->porc_bonif)/100);
        if ($item->porc_bonif > 0) $tiene_descuento = TRUE;
        $precio_neto = round($precio_final / ((100+$articulo->porc_iva)/100),2);

        if ($item->porc_bonif == 100) {
          $item->tipo_cantidad = "B";
        } else if ($item->cantidad < 0) {
          $item->tipo_cantidad = "D";
        } else {
          $item->tipo_cantidad = "";
        }

        // Calculamos los totales de la fila y sumamos a los totales generales
        $costo_final = $item->cantidad * $articulo->costo_final;
        $total_sin_iva = $item->cantidad * $precio_neto * $porc_bonif;
        $total_con_iva = $item->cantidad * $precio_final * $porc_bonif;
        $iva = $total_con_iva - $total_sin_iva;
        $t_neto += $total_sin_iva;
        $t_iva += $iva;
        $t_total += $total_con_iva;
        $t_costo_final += $costo_final;

        // Insertamos la fila
        $sql = "INSERT INTO facturas_items (";
        $sql.= " id_empresa,id_punto_venta,id_factura,";
        $sql.= " id_articulo,cantidad,";
        $sql.= " porc_iva,id_tipo_alicuota_iva,neto,precio,";
        $sql.= " nombre,orden,id_rubro,iva,";
        $sql.= " total_sin_iva,total_con_iva,costo_final,uploaded,bonificacion, ";
        $sql.= " id_cliente, id_vendedor, id_proveedor, anulado, negativo, stamp, tipo_cantidad ";
        $sql.= ") VALUES (";
        $sql.= " '$id_empresa','$id_punto_venta','$id_factura',";
        $sql.= " '$articulo->id','$item->cantidad', ";
        $sql.= " '$articulo->porc_iva,','$articulo->id_tipo_alicuota_iva','$precio_neto','$precio_final', ";
        $sql.= " '$articulo->nombre','$i','$articulo->id_rubro','$iva', ";
        $sql.= " '$total_sin_iva','$total_con_iva','$costo_final',1,'$item->porc_bonif', ";
        $sql.= " '$f->id_cliente', '$id_vendedor', 0, 0, 0, '$stamp', '$item->tipo_cantidad' ";
        $sql.= ")";
        $this->db->query($sql);

        if (isset($ivas[$articulo->id_tipo_alicuota_iva])) {
          $ivas[$articulo->id_tipo_alicuota_iva]["neto"] += $total_sin_iva;
          $ivas[$articulo->id_tipo_alicuota_iva]["iva"] += $iva;
        } else {
          $ivas[$articulo->id_tipo_alicuota_iva] = array(
            "neto"=>$total_sin_iva,
            "iva"=>$iva,
          );
        }
      }

      // Si el pedido tiene algun producto pesable o algun descuento aplicado, se debe marcar para que el usuario lo vea
      if ($pesable || $tiene_descuento || !empty($observaciones)) {
        $sql = "UPDATE facturas SET ";
        $sql.= " id_tipo_estado = -1 ";
        $sql.= "WHERE id = $id_factura ";
        $sql.= "AND id_punto_venta = $id_punto_venta ";
        $sql.= "AND id_empresa = $id_empresa ";
        $this->db->query($sql);
      }

      // Actualizamos los totales de la factura
      $sql = "UPDATE facturas SET ";
      $sql.= " total = '$t_total', neto = '$t_neto', iva = '$t_iva', subtotal = '$t_neto', costo_final = '$t_costo_final' ";
      $sql.= "WHERE id = $id_factura ";
      $sql.= "AND id_punto_venta = $id_punto_venta ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);

      // Guardamos los IVAS
      file_put_contents("ivas.txt", print_r($ivas,TRUE), FILE_APPEND);
      foreach ($ivas as $id_tipo_iva => $ti) {

        // ARREGLO POR EL TEMA DE LOS AJUSTES
        $s = $this->modelo->calcular_iva_segun_alicuota($id_tipo_iva,$ti["neto"]);

        $sql = "INSERT INTO facturas_iva (id_factura, id_empresa, id_punto_venta, id_alicuota_iva, neto, iva, uploaded) VALUES (";
        $sql.= " $id_factura, $id_empresa, $id_punto_venta,$id_tipo_iva,".$ti["neto"].",".$s.",0)";
        file_put_contents("ivas.txt", "\n".$sql."\n", FILE_APPEND);
        $this->db->query($sql);        
      }

      $numero++;
    } // Fin FOR

    // Actualizamos los numeros de comprobantes
    $ultimo = $numero--;
    $sql = "UPDATE numeros_comprobantes SET ultimo = $ultimo WHERE id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta AND id_tipo_comprobante = $id_tipo_comprobante";
    $this->db->query($sql);

    echo "1";
  }

  function verificar($id_factura,$id_punto_venta = 0) {
    
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;

    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);

    $factura = $this->modelo->get($id_factura,$id_punto_venta);

    $respuesta = $fe->consultar($factura->id_tipo_comprobante,$factura->numero,$factura->punto_venta);
    if (!isset($respuesta->error)) {

      if (isset($respuesta->CodAutorizacion) && !empty($respuesta->CodAutorizacion)) {

        $fecha_vto = substr($respuesta->FchVto,0,4)."-".substr($respuesta->FchVto,4,2)."-".substr($respuesta->FchVto, 6, 2);
        // Actualizamos la factura
        $sql = "UPDATE facturas SET ";
        $sql.= " cae = '$respuesta->CodAutorizacion', ";
        $sql.= " fecha_vto = '$fecha_vto', ";
        $sql.= " pendiente = 0 ";
        $sql.= "WHERE id = $id_factura AND id_empresa = $id_empresa ";
        $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
        $this->db->query($sql);
        
        echo json_encode(array(
          "error"=>0,
          ));
        return;
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"El comprobante no tiene codigo de autorizacion",
          ));
        return;
      }
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$respuesta->mensaje
        ));
      return;
    }
  }
  
  function imprimir_2($id_factura,$id_punto_venta = 0) {
    
    $this->load->helper("fecha_helper");
    $factura = $this->modelo->get($id_factura,$id_punto_venta);
    
    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($factura->id_tipo_comprobante);
    
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;    
    
    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get($factura->id_punto_venta);
    
    $datos = array(
      "tipo_comprobante"=>$tipo_comprobante,
      "factura"=>$factura,
      "empresa"=>$empresa,
      "folder"=>"/application/views/reports/factura/$punto_venta->disenio_factura/$punto_venta->disenio_factura_color",
      );
    $this->load->view("reports/factura/$punto_venta->disenio_factura/remito.php",$datos);    
  }
  
  
  function imprimir($id_factura) {
    if ($this->modelo->imprimir($id_factura,Facturas::DEBUG) === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Ocurrio un error al intentar imprimir el comprobante."
        ));
    } else {
      echo json_encode(array("error"=>0));
    }
  }  
  
  function ultimo_reparto($fecha) {
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $fecha = fecha_mysql(str_replace("-","/",$fecha));
    $sql = "SELECT IF(MAX(reparto) IS NULL,1,MAX(reparto)) AS reparto FROM facturas ";
    $sql.= "WHERE fecha_reparto = '$fecha' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $r = $q->row();
    echo json_encode($r);
  }
  
  function next($id_punto_venta = 1) {
    $id_empresa = parent::get_empresa();
    $salida = $this->modelo->next(array(
      "id_empresa"=>$id_empresa,
      "id_punto_venta"=>$id_punto_venta,
    ));
    echo json_encode($salida);
  }

  function ver_comprobante($id_factura,$id_punto_venta = 0) {
    echo json_encode($this->modelo->get($id_factura,$id_punto_venta));
  }


  function imprimir_lote() {
    
    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");
    $this->load->model("Punto_Venta_Model");

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;

    $header = $this->load->view("reports/factura/header",null,true);

    $facturas = array();
    $lista_facturas = $this->input->get("facturas");
    $obj = json_decode($lista_facturas);
    foreach($obj as $f) {
      $factura = $this->modelo->get($f->id,$f->id_punto_venta);
      $factura->barcode = $this->modelo->get_barcode($factura);
      $factura->neto = abs($factura->neto);
      $factura->iva = abs($factura->iva);
      $factura->total = abs($factura->total);    
      $facturas[] = $factura;

      // Guardamos que la factura fue impresa
      $sql = "UPDATE facturas SET impresa = 1 ";
      $sql.= "WHERE id = $factura->id AND id_punto_venta = $factura->id_punto_venta AND id_empresa = $factura->id_empresa ";
      $this->db->query($sql);
    }
    $tpl = "distribuidora";
    $folder = "/sistema/application/views/reports/factura/$tpl";
    $datos = array(
      "facturas"=>$facturas,
      "empresa"=>$empresa,
      "header"=>$header,
      "letras"=> new EnLetras(),
      "folder"=>$folder,
    );
    $this->load->view("reports/factura/$tpl/factura_lote.php",$datos);
  }


  function imprimir_agrupado() {
    
    $this->load->helper("fecha_helper");
    $this->load->model("Punto_Venta_Model");

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;
    
    $header = $this->load->view("reports/factura/header",null,true);

    $lista_facturas = $this->input->get("facturas");
    $obj = json_decode($lista_facturas);
    $items = array();
    $total = 0;
    foreach($obj as $f) {
      $factura = $this->modelo->get($f->id,$f->id_punto_venta);
      $items = array_merge($items,$factura->items);
      $total += abs($factura->total);
    }
    $datos = array(
      "cliente"=>$factura->cliente,
      "total"=>$total,
      "items"=>$items,
      "empresa"=>$empresa,
      "header"=>$header,
    );
    $this->load->view("reports/factura/basico/remito_agrupado.php",$datos);
  }

  // TODO: FALTA TERMINAR LO GENERA MAL
  function generar_pdf() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_factura = parent::get_get("id_factura");
    $id_punto_venta = parent::get_get("id_punto_venta");
    $id_empresa = parent::get_get("id_empresa");
    // Generamos el PDF
    $cache_dir = '/home/ubuntu/data/cache';
    require_once('/home/ubuntu/data/vendor/autoload.php');
    $mpdf = new \Mpdf\Mpdf([
      'tempDir' => $cache_dir
    ]);
    $url = "https://www.varcreative.com/sistema/facturas/function/ver_pdf/".$id_factura."/".$id_punto_venta."/".$id_empresa."?header=0";
    $html = file_get_contents($url);
    $mpdf->CSSselectMedia='mpdf';
    $mpdf->setBasePath($url);
    $mpdf->WriteHTML($html);
    $mpdf->Output("Comprobante.pdf", \Mpdf\Output\Destination::DOWNLOAD);
  }

  
  function ver_pdf($id_factura,$id_punto_venta = 0, $id_empresa = 0) {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");
    $this->load->model("Punto_Venta_Model");
    $id_empresa = (empty($id_empresa)) ? parent::get_empresa() : $id_empresa;

    // SUPERUSUARIO DONYEYO
    if ($id_empresa == 980) {
      $sql = "SELECT id_empresa FROM facturas WHERE id = $id_factura AND id_punto_venta = $id_punto_venta";
      $q = $this->db->query($sql);
      if ($q->result() > 0) {
        $r = $q->row();
        $id_empresa = $r->id_empresa;
      }
    }

    $mostrar_header = parent::get_get("header",1);
    $visto = parent::get_get("v",0);

    if ($id_punto_venta != 0) {
      $punto_venta = $this->Punto_Venta_Model->get($id_punto_venta,array(
        "id_empresa"=>$id_empresa
      ));

      // ESPACIO VIRTUAL, PROBLEMA CON LOS CLIENTES DE DISTINTAS SUCURSALES
      if ($punto_venta->id_empresa == 287) {
        $factura = $this->modelo->get($id_factura,$id_punto_venta,array(
          "id_sucursal"=>$punto_venta->id_sucursal,
          "buscar_etiquetas"=>1,
          "id_empresa"=>$id_empresa
        ));        
      } else {
        $factura = $this->modelo->get($id_factura,$id_punto_venta,array(
          "buscar_etiquetas"=>1,
          "id_empresa"=>$id_empresa
        ));
      }
    } else {
      // NO ESTA DEFINIDO EL PUNTO DE VENTA
      $factura = $this->modelo->get($id_factura,$id_punto_venta,array(
        "id_empresa"=>$id_empresa
      ));
      if ($factura->id_punto_venta != 0) {
        $punto_venta = $this->Punto_Venta_Model->get($factura->id_punto_venta,array(
          "id_empresa"=>$id_empresa
        ));
      } else {
        $punto_venta = new stdClass();
        $punto_venta->id = 0;
        $punto_venta->direccion = "";
        $punto_venta->localidad = "";
        $punto_venta->disenio_factura = "";
        $punto_venta->disenio_factura_color = "";
      }
    }
    /*
    $factura = $this->modelo->get($id_factura,$id_punto_venta);
    $punto_venta = $this->Punto_Venta_Model->get($factura->id_punto_venta);
    */

    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($factura->id_tipo_comprobante);
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;
    if (isset($punto_venta->direccion) && isset($punto_venta->localidad) && !empty($punto_venta->direccion)) $empresa->direccion = $punto_venta->direccion." ".$punto_venta->localidad;
        
    $header = ($mostrar_header == 1) ? $this->load->view("reports/factura/header",null,true) : "";

    // Excepciones
    if ($id_punto_venta == 1705 && $id_empresa == 228) {
      $empresa->razon_social = "MASSI MARIA FERNANDA";
      $empresa->cuit = "27-24999238-6";
    } else if ($id_punto_venta == 2003 && $id_empresa == 228) {
      $empresa->razon_social = "CABAN MASSI SRL";
      $empresa->cuit = "30-71656564-1";
    }
    
    $tpl = $punto_venta->disenio_factura;
    if (empty($tpl)) $tpl = "basico";
    $folder = "/sistema/application/views/reports/factura/$tpl";
    if (!empty($punto_venta->disenio_factura_color)) $folder.= "/$punto_venta->disenio_factura_color";
    
    $barcode = $this->modelo->get_barcode($factura);
    $factura->neto = abs($factura->neto);
    $factura->iva = abs($factura->iva);
    $factura->total = abs($factura->total);    

    // Indicamos que el cliente vio la factura
    if ($visto == 1) {
      $this->db->query("UPDATE facturas SET visto = visto + 1 WHERE id = $factura->id AND id_empresa = $factura->id_empresa AND id_punto_venta = $factura->id_punto_venta");    
    }

    $this->load->model("Almacen_Model");
    $sucursal = $this->Almacen_Model->get($factura->id_sucursal,array(
      "id_empresa"=>$id_empresa
    ));

    // Si la factura no fue pagada, le damos la posibilidad de que la pague con MP
    $preference = FALSE;
    if ($factura->pagada == 0 && $factura->id_tipo_estado != 4 && $id_empresa != 120 && $id_empresa != 571 && $id_empresa != 1284) {
      $this->load->model("Medio_Pago_Configuracion_Model");
      $medio_pago = $this->Medio_Pago_Configuracion_Model->get($id_empresa);
      if (isset($medio_pago) && !empty($medio_pago) && $medio_pago->habilitar_mp == 1 && !empty($medio_pago->mp_client_id) && !empty($medio_pago->mp_client_secret)) {
        $preference = $this->Medio_Pago_Configuracion_Model->create_preference_mp(array(
          "id_empresa"=>$factura->id_empresa,
          "id_factura"=>$factura->id,
          "id_punto_venta"=>$factura->id_punto_venta,
          "titulo"=>$factura->comprobante,
          "monto"=>($factura->total + 0),
          "email"=>$factura->cliente->email,
        ));        
      }
    }
    
    $datos = array(
      "preference"=>$preference,
      "tipo_comprobante"=>$tipo_comprobante,
      "factura"=>$factura,
      "empresa"=>$empresa,
      "sucursal"=>$sucursal,
      "header"=>$header,
      "barcode"=>$barcode,
      "letras"=> new EnLetras(),
      "folder"=>$folder,
    );

    // Guardamos que la factura fue impresa
    $sql = "UPDATE facturas SET impresa = 1 ";
    $sql.= "WHERE id = $factura->id AND id_punto_venta = $factura->id_punto_venta AND id_empresa = $factura->id_empresa ";
    $this->db->query($sql);

    $this->load->view("reports/factura/$tpl/factura.php",$datos);
  }


  function imprimir_plano($id_factura,$id_punto_venta = 0) {
    
    $this->load->helper("fecha_helper");
    $this->load->model("Punto_Venta_Model");

    if ($id_punto_venta != 0) {
      $punto_venta = $this->Punto_Venta_Model->get($id_punto_venta);

      // ESPACIO VIRTUAL, PROBLEMA CON LOS CLIENTES DE DISTINTAS SUCURSALES
      if ($punto_venta->id_empresa == 287) {
        $factura = $this->modelo->get($id_factura,$id_punto_venta,array(
          "id_sucursal"=>$punto_venta->id_sucursal,
          "buscar_etiquetas"=>1,
        ));        
      } else {
        $factura = $this->modelo->get($id_factura,$id_punto_venta,array(
          "buscar_etiquetas"=>1,
        ));
      }
    } else {
      $factura = $this->modelo->get($id_factura,$id_punto_venta);
      $punto_venta = $this->Punto_Venta_Model->get($factura->id_punto_venta);
    }
    
    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($factura->id_tipo_comprobante);
    
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;
        
    $header = $this->load->view("reports/factura/header",null,true);
    
    $factura->neto = abs($factura->neto);
    $factura->iva = abs($factura->iva);
    $factura->total = abs($factura->total);    

    $this->load->model("Almacen_Model");
    $sucursal = $this->Almacen_Model->get($factura->id_sucursal);
    
    $datos = array(
      "tipo_comprobante"=>$tipo_comprobante,
      "factura"=>$factura,
      "empresa"=>$empresa,
      "sucursal"=>$sucursal,
      "header"=>$header,
    );

    $this->load->view("reports/factura/plano.php",$datos);
  }

  
  /**
   * ESTA FUNCION LA USAN LOS CLIENTES DE LA EMPRESA PARA VER SUS COMPROBANTES
   */
  function ver($hash) {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");
    $factura = $this->modelo->get_by_hash($hash);
    
    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($factura->id_tipo_comprobante);
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($factura->id_empresa);
    
    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get($factura->id_punto_venta,array(
      "id_empresa"=>$factura->id_empresa
    ));
    
    $header = $this->load->view("reports/factura/header",null,true);
    
    $tpl = $punto_venta->disenio_factura;
    if (empty($tpl)) $tpl = "basico";
    $folder = "/sistema/application/views/reports/factura/$tpl";
    if (!empty($punto_venta->disenio_factura_color)) $folder.= "/$punto_venta->disenio_factura_color";

    // Indicamos que el cliente vio la factura
    $this->db->query("UPDATE facturas SET visto = visto + 1 WHERE id = $factura->id");
    
    $barcode = $this->modelo->get_barcode($factura);

    /*
    $this->load->model("Log_Model");
    $this->Log_Model->notify(array(
      "texto"=>"$factura->cliente ha visto $factura->comprobante",
      "link"=>"facturacion/$factura->id",
      "id_empresa"=>$empresa->id,
    ));
    */

    // Obtenemos la configuracion de MP y el objeto (si fue configurado)
    $mp = FALSE;
    $preference_data = FALSE;
    require_once("../models/mercadopago.php");
    $q = $this->db->query("SELECT * FROM medios_pago_configuracion WHERE id_empresa = $empresa->id ");
    if ($q->num_rows()>0) {
      $medio = $q->row();
      if ($medio->habilitar_mp == 1) {
        // La configuracion de las dos cuentas esta separada por ;;;
        // Dependiendo del carrito, tomamos una u otra configuracion
        $clients_id = explode(";;;", $medio->mp_client_id);
        $clients_secret = explode(";;;", $medio->mp_client_secret);

        // Dependiendo de cual carrito estamos haciendo el checkout
        $mp_client_id = trim($clients_id[0]);
        $mp_client_secret = trim($clients_secret[0]);
        if (!empty($mp_client_id) && !empty($mp_client_secret)) {

          // Creamos el objeto de MercadoPago
          $mp = new MP($mp_client_id, $mp_client_secret); 

          // Creamos el objeto de preferencia
          $items = array();
          foreach($factura->items as $it) {
            $items[] = array(
              "id"=>$it->id_articulo,
              "title"=>$it->nombre,
              "currency_id"=>"ARS",
              "quantity"=>$it->cantidad + 0,
              "unit_price"=>$it->precio + 0,
            );            
          }
          $current_url = "https://www.varcreative.com/sistema/facturas/function/ver/".$hash;
          $preference_data = array(
            "items" => $items,
            "payer" => array(
              "name" => $factura->cliente->nombre,
              "email" => $factura->cliente->email,
            ),
            "back_urls" => array(
              "success" => $current_url,
              "failure" => $current_url,
              "pending" => $current_url,
            ),
            "auto_return" => "all",
            "notification_url" => "https://www.varcreative.com/ipn-factura-periodica.php",
            "external_reference" => $factura->id."_".$factura->id_empresa."_".$factura->id_punto_venta,
          );
        }
      }
    }
    
    $datos = array(
      "tipo_comprobante"=>$tipo_comprobante,
      "factura"=>$factura,
      "empresa"=>$empresa,
      "header"=>$header,
      "letras"=> new EnLetras(),
      "folder"=>$folder,
      "barcode"=>$barcode,
      "mp"=>$mp,
      "preference_data"=>$preference_data,
    );
    $this->load->view("reports/factura/$tpl/factura.php",$datos);
    
  }
  
  function exportar_csv($desde,$hasta) {
    
    $id_empresa = parent::get_empresa();
    
    $this->load->helper("fecha_helper");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    if (!empty($hasta)) $hasta = fecha_mysql($hasta);    
    
    $this->load->dbutil();
    $this->load->helper('download');

    $query = $this->db->query("SELECT F.* FROM facturas F WHERE F.id_empresa = $id_empresa AND '$desde' <= F.fecha AND F.fecha <= '$hasta'");
    $facturas = $this->dbutil->csv_from_result($query, ";", "\r\n");
    
    $query = $this->db->query("SELECT FI.* FROM facturas F INNER JOIN facturas_items FI ON (F.id = FI.id_factura) WHERE F.id_empresa = $id_empresa AND FI.id_empresa = $id_empresa AND '$desde' <= F.fecha AND F.fecha <= '$hasta' ");
    $facturas_items = $this->dbutil->csv_from_result($query, ";", "\r\n");    
    
    force_download('facturas.csv', $facturas.$facturas_items);
  }
  
  function importar() {
    $tabla = "facturas";
    $file = "uploads/".$_FILES["file"]["name"];
    $sqls = array();
    if (move_uploaded_file($_FILES["file"]["tmp_name"],$file)) {
      $f = fopen($file,"r+");
      $i=0;
      while(($linea = fgets($f))!==FALSE) {
        if ($i==0) { $i++; continue; } // La primera linea la obviamos
        $linea = trim($linea);
        $linea = str_replace(";",",",$linea);
        $linea = substr($linea,0,strlen($linea)-1);
        if (strpos($linea,'"id","sucursal","id_factura"') === 0) {
          $tabla = "facturas_items";
          $i++; continue; // Cambiamos de tabla
        }
        $s = 'INSERT INTO '.$tabla.' VALUES ('.$linea.')';
        $sqls[] = $s;
        $i++;
      }
      fclose($f);
      if (!empty($sqls)) {
        foreach($sqls as $s) {
          $this->db->query($s);
        }
      }
    }
    header("Location: app/#ventas_listado");
  }  
  
  function show_error($mensaje = "Ocurrio un error al guardar el comprobante") {
    echo json_encode(array(
      "error"=>1,
      "mensaje"=>$mensaje,
      "imprimir"=>0,
      ));
    exit();    
  }
  
  function insert() {

    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();
    
    $this->load->model("Articulo_Model");
    $this->load->model("Tipo_Comprobante_Model");
    $this->load->model("Punto_Venta_Model");
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");
    $this->load->model("Log_Model");
    
    // Comenzamos la transaccion
    if ($id_empresa != 249 && $id_empresa != 868) {
      $this->db->trans_start();  
    }
    
    // Controlamos el tipo de plan, para saber si el usuario no esta pasado
    if ($this->modelo->controlar_plan($id_empresa) === FALSE) {
      parent::send_error("Su plan ha alcanzado el limite para este mes.");
    }

    $this->load->model("Configuracion_Model");
    
    // Tomamos los datos
    $array = $this->parse_put();

    if ($this->Configuracion_Model->es_local()==0) {
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "file"=>date("Ymd")."_insertar_facturas.txt",
        "texto"=>"INSERT: ".print_r($array,TRUE)."\n\n",
      ));
    }

    // Obtenemos el tipo de comprobante
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($array->id_tipo_comprobante);
    
    // Obtenemos el punto de venta
    $punto_venta = $this->Punto_Venta_Model->get($array->id_punto_venta);
    $array->id_sucursal = $punto_venta->id_sucursal;
    
    // Si la factura es electronica
    if ($punto_venta->tipo_impresion == "E" && $array->id_tipo_comprobante < 900) {
      // La ponemos en pendiente hasta que devuelva el CAE
      $array->pendiente = 1;
    }
    
    $stamp = time();
    $array->id_empresa = $id_empresa;
    if (is_null($array->id_vendedor)) $array->id_vendedor = 0;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");
    $array->last_update = $stamp;
    if (isset($array->fecha_vto) && !empty($array->fecha_vto)) $array->fecha_vto = fecha_mysql($array->fecha_vto);

    // Si es una factura periodica
    $es_periodica = (isset($array->es_periodica)) ? $array->es_periodica : 0;
    $periodo_cantidad = (isset($array->periodo_cantidad)) ? $array->periodo_cantidad : 1;
    $periodo_tipo = (isset($array->periodo_tipo)) ? $array->periodo_tipo : 'M';
    $periodo_dia = (isset($array->periodo_dia)) ? $array->periodo_dia : 1;
    $dias_vencimiento = (isset($array->dias_vencimiento)) ? $array->dias_vencimiento : 10;

    // Si viene seteado el impuesto pais
    $impuesto_pais = (isset($array->impuesto_pais)) ? $array->impuesto_pais : 0;
    if ($impuesto_pais > 0) $array->custom_1 = $impuesto_pais;

    $this->load->model("Almacen_Model");
    $this->load->model("Vendedor_Model");
    $this->load->model("Cliente_Model");
    $array->tipo_punto_venta = $punto_venta->tipo_impresion;
    $almacen = $this->Almacen_Model->get($array->id_sucursal);
    $array->sucursal = (!empty($almacen)) ? $almacen->nombre : "";
    if (!empty($array->id_vendedor)) {
      $vendedor = $this->Vendedor_Model->get($array->id_vendedor);
      $array->vendedor = ($vendedor !== FALSE) ? $vendedor->nombre : "";      
    } else {
      $array->vendedor = "";
    }
    $array->tipo_comprobante = $tipo_comprobante->nombre;
    $array->id_cliente = (empty($array->id_cliente)) ? 0 : $array->id_cliente;
    $cliente = $this->Cliente_Model->get_by_id($array->id_cliente,array("id_empresa"=>$array->id_empresa));
    $array->cliente = ($cliente !== FALSE) ? $cliente->nombre : "";

    if (is_null($array->neto)) $array->neto = 0;
    if (is_null($array->iva)) $array->iva = 0;
    
    if (isset($array->fecha_reparto)) $array->fecha_reparto = fecha_mysql($array->fecha_reparto);
    else $array->fecha_reparto = date("Y-m-d");        
    
    $id_usuario = $_SESSION["id"];
    $array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;
    
    $array->comprobante = $tipo_comprobante->letra." ".str_pad($punto_venta->numero,4,"0",STR_PAD_LEFT)."-".str_pad($array->numero,8,"0",STR_PAD_LEFT);
    
    $array->hash = md5($array->id_empresa.$array->comprobante);

    //$array->subtotal = (float)$array->total + (float)$array->descuento;

    // Dependiendo de la configuracion del sistema, si es LOCAL o NO
    $array->uploaded = 0;

    $items = $array->items;
    $tarjetas = $array->tarjetas;
    $cheques = $array->cheques;
    $pagos = array();
    $creditos_personales = (isset($array->creditos_personales)) ? $array->creditos_personales : array();
    $ofertas = (isset($array->ofertas)) ? $array->ofertas : array();
    $bonificaciones = (isset($array->bonificaciones)) ? $array->bonificaciones : array();
    $this->modelo->limpiar($array);

    // Si es un comprobante C, NO se informa IVA y el neto es igual al total
    if ($tipo_comprobante->letra == "C") {
      $array->iva = 0;
      $array->neto = $array->total;
    }
    
    if (sizeof($creditos_personales)>0) {
      // Si tiene creditos personales, siempre el tipo de pago es CUENTA CORRIENTE
      $array->tipo_pago = "C";
      $array->pagada = 0;
      $array->cta_cte = $array->credito;
      $pagos = array(
        "monto"=>($array->efectivo + $array->tarjeta)
      );
      $array->pago = -$array->efectivo;

    } else {

      // Si el comprobante es en EFECTIVO
      if ($array->tipo_pago == "E") {
        $array->pago = -$array->total;
        $array->pagada = 1;
      // Si el comprobante es CUENTA CORRIENTE
      } else if ($array->tipo_pago == "C") {
        $array->pagada = 0;
        $array->pago = 0;
      }

      // Debemos controlar que IMPORT SHOW si paga con tarjeta sindicado no se tiene que marcar como pagada
      $encontro_tarjeta_sindicato = 0;
      if ($id_empresa == 356) {
        foreach($tarjetas as $tar) {
          if ($tar->id_tarjeta == 51) {
            $encontro_tarjeta_sindicato = 1;
            break;
          }
        }
      }

      // Si el cliente dio valores para pagar la factura
      $pago = $array->efectivo;
      if (isset($array->tarjeta) && $encontro_tarjeta_sindicato == 0) $pago = $pago + $array->tarjeta;
      if (isset($array->cheque)) $pago = $pago + $array->cheque;
      $array->pago = - ($pago - $array->vuelto);
      // Si el pago es igual al total, entonces la factura esta paga
      $array->pagada = (abs($array->pago) == abs($array->total)) ? 1 : 0;
    }

    // Si se aplico un codigo de descuento (ART: 2222 MEGASHOP)
    if (isset($array->aplico_codigo_descuento) && $array->aplico_codigo_descuento == 1) {
      $descuento_aplicado = $array->descuento;
      $array->subtotal = $array->subtotal - $array->descuento;
      $array->porc_descuento = 0;
      $array->descuento = 0;
      foreach($items as $item) {
        if (($item->id_articulo == 10213586 || $item->id_articulo == 10216830) && $item->anulado == 0) {
          $item->cantidad = 1;
          $item->neto = -$descuento_aplicado;
          $item->precio = -$descuento_aplicado;
          $item->total_sin_iva = -$descuento_aplicado;
          $item->total_con_iva = -$descuento_aplicado;
          $item->costo_final = 0; // La bonificacion no tiene costo final
          break; // Se aplica una sola vez por las dudas
        }
      }
    }

    //if ($array->descuento > 0) {
    //  $array->total = $array->total - $array->descuento;
    //}


    $id_factura = $this->modelo->save($array);

    // Si el punto de venta tiene una caja asignada, tenemos que hacer el movimiento en dicha caja
    if ($punto_venta->id_caja != 0) {
      $this->load->model("Caja_Movimiento_Model");
      $this->Caja_Movimiento_Model->ingreso(array(
        "id_empresa"=>$array->id_empresa,
        "id_factura"=>$id_factura,
        "id_punto_venta"=>$array->id_punto_venta,
        "id_sucursal"=>$array->id_sucursal,
        "fecha"=>$array->fecha." ".$array->hora,
        "id_caja"=>$punto_venta->id_caja,
        "observaciones"=>$array->comprobante,
        "id_usuario"=>$array->id_usuario,
        "monto"=>($array->efectivo - $array->vuelto),
      ));
    }
    
    // Guardamos los posibles errores
    /*
    if ($this->db->_error_number() == 1062) {
      $this->show_error("Numero de comprobante duplicado");
    }
    */
    
    // Guardamos la relacion con vendedores para que se vaya formando
    if ($array->id_cliente != 0 && isset($array->id_vendedor) && $array->id_vendedor != 0) {
      $this->db->query("UPDATE clientes SET id_vendedor = $array->id_vendedor WHERE id = $array->id_cliente");
    }
    $tiene_items_anulados = 0;
    $bonificaciones_2 = array();
    $ivas = array();
    $costo_final_factura = 0;
    $orden_items = 0;
    foreach($items as $l) {

      $anulado = $this->defecto($l->anulado);

      // Si tiene bonificaciones y no esta anulado
      if (sizeof($bonificaciones)>0 && $anulado == 0) {
        foreach($bonificaciones as $bonificacion) {
          if ($bonificacion->id_articulo == $l->id_articulo && $l->cantidad > 0) {
            $l->cantidad = $l->cantidad - 1; // Le restamos 1 a la cantidad
            $l->total_sin_iva = $l->total_sin_iva - $l->neto;
            $l->total_con_iva = $l->total_con_iva - $l->precio;
            array_shift($bonificaciones); // Una vez que lo restamos, sacamos el objeto del array de bonificaciones
            $bonificaciones_2[] = $bonificacion;
          }
        }
      }

      // Si el pedido no esta FINALIZADO, entonces debemos reservar el STOCK
      $custom_3 = $this->defecto($l->custom_3,"");
      if ($array->id_tipo_estado != 6) $custom_3 = "1";

      if ($l->cantidad < 0) $l->tipo_cantidad = "D"; // Si la cantidad es negativa, es una devolucion

      if (isset($l->id_articulo) && !empty($l->id_articulo)) {
        $articulo = $this->Articulo_Model->get_nombre($l->id_articulo,array(
          "id_empresa"=>$array->id_empresa
        ));
        if (!isset($l->id_rubro) || $l->id_rubro == 0) {
          $l->id_rubro = $articulo->id_rubro;
        }
      }

      // Arreglo victor
      if (isset($l->porc_iva) && $l->porc_iva == 0) $l->id_tipo_alicuota_iva = 3;

      // Si la bonificacion es 100%, el tipo cantidad = B
      if (isset($l->bonificacion) && $l->bonificacion == 100) $l->tipo_cantidad = "B";

      $this->db->insert("facturas_items",array(
        "id_empresa"=>$this->defecto($array->id_empresa),
        "id_punto_venta"=>$this->defecto($array->id_punto_venta),
        "id_factura"=>$this->defecto($id_factura),
        "id_articulo"=>$this->defecto($l->id_articulo),
        "tipo"=>(isset($l->tipo) ? $l->tipo : 0),
        "tipo_cantidad"=>$this->defecto($l->tipo_cantidad,""),
        "cantidad"=>$this->defecto($l->cantidad),
        "precio"=>$this->defecto($l->precio),
        "neto"=>$this->defecto($l->neto),
        "porc_iva"=>$this->defecto($l->porc_iva),
        "id_tipo_alicuota_iva"=>$this->defecto($l->id_tipo_alicuota_iva),
        "nombre"=>$this->defecto($l->nombre,""),
        "descripcion"=>$this->defecto($l->descripcion,""),
        "total_con_iva"=>$this->defecto($l->total_con_iva),
        "total_sin_iva"=>$this->defecto($l->total_sin_iva),
        "bonificacion"=>$this->defecto($l->bonificacion),
        "iva"=>$this->defecto($l->iva),
        "anulado"=>$anulado,
        "orden"=>$this->defecto($orden_items),
        "uploaded"=>$this->defecto($array->uploaded),
        "id_rubro"=>$this->defecto($l->id_rubro),
        "costo_final"=>$this->defecto($l->costo_final),
        "custom_1"=>$this->defecto($l->custom_1,""), // Guardar LEIDO
        "custom_2"=>$this->defecto($l->custom_2,""), // Articulo de OFERTA
        "custom_3"=>$custom_3, // Si reservo stock
        "custom_4"=>$this->defecto($l->custom_4,""), // 868 = Costo Final CENTRAL
        "id_proveedor"=>$this->defecto($l->id_proveedor),
        "id_variante"=>$this->defecto($l->id_variante),
        "stamp"=>$stamp,
      ));

      // Se marcan las facturas que tienen anulados, o que se cambio algun precio en el interior
      if ($l->anulado == 1) $tiene_items_anulados = 1;
      if ($l->tipo_cantidad == "X") $tiene_items_anulados = 1;

      // Si la configuracion NO ES LOCAL, tenemos que gestionar STOCK desde ACA
      // Sino, eso se hace desde el cronjob "uploader"
      // TODO: HACER DINAMICO ESTO
      if ( $this->Configuracion_Model->es_local()==0 || $id_empresa == 271 || $id_empresa == 756 || $id_empresa == 374 || $id_empresa == 481 || $id_empresa == 781 ) {
        $this->load->model("Stock_Model");
        $this->Stock_Model->procesar($array->id_empresa,$array->punto_venta);
      }
      
      // Sumamos las alicuotas de IVA
      if (!isset($ivas[$l->id_tipo_alicuota_iva])) {
        $ivas[$l->id_tipo_alicuota_iva] = array("neto"=>0,"iva"=>0);
      }
      $ivas[$l->id_tipo_alicuota_iva]["neto"] += ($l->total_sin_iva * ((100-$array->porc_descuento) / 100));
      $ivas[$l->id_tipo_alicuota_iva]["iva"] += ($l->iva * ((100-$array->porc_descuento) / 100));
      
      if ($anulado == 0) {
        $costo_final_factura = $costo_final_factura + ((float)$this->defecto($l->costo_final));
      }

      $orden_items++;
    }

    // Actualizamos algunos valores calculados de la factura
    $sql_update = "UPDATE facturas SET ";
    $sql_update.= " costo_final = $costo_final_factura ";
    // Si tiene items anulados
    if ($tiene_items_anulados == 1) $sql_update.= ", id_tipo_estado = -1 ";
    $sql_update.= "WHERE id_empresa = $array->id_empresa AND id_punto_venta = $array->id_punto_venta AND id = $id_factura ";
    $this->db->query($sql_update);
    
    foreach($ivas as $id_alicuota_iva => $iva) {
      $this->db->insert("facturas_iva",array(
        "id_empresa"=>$this->defecto($id_empresa),
        "id_factura"=>$this->defecto($id_factura),
        "id_alicuota_iva"=>$this->defecto($id_alicuota_iva),
        "id_punto_venta"=>$this->defecto($array->id_punto_venta),
        "neto"=>$this->defecto($iva["neto"]),
        "iva"=>$this->defecto($iva["iva"]),
        "uploaded"=>$this->defecto($array->uploaded),
      ));
    }

    foreach($pagos as $pago) {
      $this->db->insert("facturas_pagos",array(
        "id_empresa"=>$this->defecto($id_empresa),
        "id_factura"=>$this->defecto($id_factura),
        "id_punto_venta"=>$this->defecto($array->id_punto_venta),
        "id_pago"=>0,
        "monto"=>$this->defecto($pago["monto"]),
        "uploaded"=>$this->defecto($array->uploaded),
      ));
    }

    // A las ofertas los tratamos como items negativos
    $en_oferta = 0;
    foreach($ofertas as $of) {
      $en_oferta += ((float)$of->monto);
      $this->db->insert("facturas_items",array(
        "id_empresa"=>$this->defecto($id_empresa),
        "id_factura"=>$this->defecto($id_factura),
        "id_punto_venta"=>$this->defecto($array->id_punto_venta),
        "cantidad"=>$this->defecto($of->cantidad),
        "iva"=>0,
        "porc_iva"=>0,
        "id_tipo_alicuota_iva"=>3,
        "uploaded"=>$this->defecto($array->uploaded),
        "neto"=>$this->defecto($of->unitario * -1),
        "precio"=>$this->defecto($of->unitario * -1),
        "total_sin_iva"=>$this->defecto($of->monto * -1),
        "total_con_iva"=>$this->defecto($of->monto * -1),
        "costo_final"=>0,
        "nombre"=>$this->defecto($of->nombre,""),
        "tipo_cantidad"=>"O", // Indica que es una oferta
        "ganancia"=>0,
        "uploaded"=>$this->defecto($array->uploaded),
        "orden"=>$orden_items,
        "anulado"=>0,
        "negativo"=>0,
        "custom_5"=>$this->defecto($of->id_regla,""), // ID DE LA REGLA APLICADA
      ));
      $orden_items++;
    }
    // Actualizamos el valor de la oferta
    if ($en_oferta > 0) $this->db->query("UPDATE facturas SET en_oferta = $en_oferta WHERE id_empresa = $array->id_empresa AND id = $id_factura AND id_punto_venta = $array->id_punto_venta");

    // A las bonificaciones las tratamos como items positivos pero sin precio
    $descuento = 0;
    foreach($bonificaciones_2 as $bon) {
      $descuento += ((float)$bon->monto);
      $this->db->insert("facturas_items",array(
        "id_articulo"=>$bon->id_articulo,
        "id_empresa"=>$this->defecto($id_empresa),
        "id_factura"=>$this->defecto($id_factura),
        "id_punto_venta"=>$this->defecto($array->id_punto_venta),
        "cantidad"=>1, // Siempre son de a 1
        "iva"=>0,
        "porc_iva"=>0,
        "id_tipo_alicuota_iva"=>3,
        "uploaded"=>$this->defecto($array->uploaded),
        "neto"=>0,
        "precio"=>$bon->monto,
        "total_sin_iva"=>0,
        "total_con_iva"=>0,
        "costo_final"=>0,
        "nombre"=>$this->defecto($bon->nombre,""),
        "tipo_cantidad"=>"B", // Indica que es una bonificacion
        "ganancia"=>0,
        "uploaded"=>$this->defecto($array->uploaded),
        "orden"=>$orden_items,
        "anulado"=>0,
        "negativo"=>0,
        "id_rubro"=>$this->defecto($bon->id_rubro),
        "id_proveedor"=>$this->defecto($bon->id_proveedor),
        "stamp"=>$stamp,
      ));
      $orden_items++;
    }
    // Actualizamos el valor del descuento
    if ($descuento > 0) $this->db->query("UPDATE facturas SET descuento = $descuento WHERE id_empresa = $array->id_empresa AND id = $id_factura AND id_punto_venta = $array->id_punto_venta");

    // Si tenemos que guardar una factura periodica
    $this->modelo->guardar_factura_periodica(array(
      "id_factura"=>$id_factura,
      "id_punto_venta"=>$array->id_punto_venta,
      "id_empresa"=>$array->id_empresa,
      "fecha"=>$array->fecha,
      "es_periodica"=>$es_periodica,
      "periodo_dia"=>$periodo_dia,
      "periodo_tipo"=>$periodo_tipo,
      "periodo_cantidad"=>$periodo_cantidad,
      "dias_vencimiento"=>$dias_vencimiento
    ));

    if (!empty($tarjetas)) {
      $this->load->model("Cupon_Tarjeta_Model");
      // GUARDAMOS LAS TARJETAS
      foreach($tarjetas as $t) {
        $t->id_factura = $id_factura;
        $t->fecha = date("Y-m-d H:i:s");
        $t->id_empresa = $id_empresa;
        $t->uploaded = $array->uploaded;
        $t->id_punto_venta = $array->id_punto_venta;
        $this->Cupon_Tarjeta_Model->insert($t);
      }      
    }
    
    if (!empty($cheques)) {
      $this->load->model("Cheque_Model");
      // GUARDAMOS LOS CHEQUES
      foreach($cheques as $ch) {
        $ch->id_factura = $id_factura;
        $ch->id_empresa = $id_empresa;
        $ch->id_punto_venta = $array->id_punto_venta;
        $ch->fecha_recibido = date("Y-m-d");
        $ch->fecha_emision = fecha_mysql($ch->fecha_emision);
        $ch->fecha_cobro = fecha_mysql($ch->fecha_cobro);
        $ch->tipo = "T";
        $ch->monto = $ch->importe;
        $ch->id_cliente = $array->id_cliente;
        $this->Cheque_Model->insert($ch);
      }      
    }

    /*
    if (!empty($creditos_personales)) {
      // Si tiene creditos personales, tenemos que insertar cada una de las cuotas en la fecha que corresponde
      // Y la primer cuota se marca como paga
      foreach($creditos_personales as $cred) {
        
        foreach($cred->cuotas as $cuota) {
          $sql = "INSERT INTO facturas (";
          $sql.= " id_empresa, id_punto_venta, id_cliente, id_vendedor, tipo_pago, tipo, ";
          $sql.= " fecha, hora, id_usuario, numero, comprobante, id_tipo_comprobante, ";
          $sql.= " total, subtotal, numero_referencia, reference_id, cta_cte ";
          $sql.= ") VALUES (";
          $sql.= " '$id_empresa', '$array->id_punto_venta', '$array->id_cliente', '$array->id_vendedor', 'C', 'C', ";
          $sql.= " '$cuota->fecha_vencimiento', '$array->hora', '$array->id_usuario', '$array->numero', '$array->comprobante', '$array->id_tipo_comprobante', ";
          $sql.= " '$cuota->monto', '$cuota->monto', '$cuota->numero', $id_factura, '$cuota->monto' ";
          $sql.= ")";
          $this->db->insert($sql);
        }

      }
    }*/
    
    // Finalizamos la transaccion
    if ($id_empresa != 249 && $id_empresa != 868) {
      $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE) {
        $mensaje = $this->db->_error_message();
        $this->show_error($mensaje);
      }
    }
    
    // Utiliza factura electronica
    if ($punto_venta->tipo_impresion == "E" && $array->id_tipo_comprobante > 0 && $array->id_tipo_comprobante < 900) {
      $res = $this->modelo->obtener_cae($id_factura,$array->id_punto_venta);
      if ($res["error"] == 1) {
        echo json_encode($res); return;
      }
    } else {
      // Actualizamos el ultimo numero del comprobante
      if (isset($array->numero) && ($array->numero != 0)) {
        $this->db->query("UPDATE numeros_comprobantes SET ultimo = $array->numero WHERE id_empresa = $array->id_empresa AND id_tipo_comprobante = $array->id_tipo_comprobante AND id_punto_venta = $array->id_punto_venta");
      }
    }
    
    $tipo_impresion = $punto_venta->tipo_impresion;
    if ($array->estado == 1) $tipo_impresion = "P"; // FACTURA PREIMPRESA
    
    echo json_encode(array(
      "id"=>$id_factura,
      "error"=>0,
      "imprimir"=>1,
      "tipo_impresion"=>$tipo_impresion
    ));
  }

  private function defecto($valor,$por_defecto = 0) {
    if (!isset($valor)) return $por_defecto;
    if (is_null($valor)) return $por_defecto;
    return $valor;
  }
  
  
  function obtener_cae($id) {
    echo json_encode($this->modelo->obtener_cae($id));
  }
  
  // TODO: METODO PARA VER LOS TIPOS DE TRIBUTOS DISPONIBLES
  /*
  function test_tipos_tributos() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    // Tomamos los datos de la empresa
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get(46);
    
    // Creamos el objeto de Factura Electronica
    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);

    $salida = $fe->get_tipos_tributos();
    print_r($salida);
  }
  */
  
  function sincronizar_numero($punto_venta = 3,$id_tipo_comprobante = 1) {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;

    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);
    $respuesta = $fe->get_ultimo_autorizado($punto_venta,$id_tipo_comprobante);
    file_put_contents("sincronizar_numero.txt", print_r($respuesta,TRUE), FILE_APPEND);
    if (!isset($respuesta->error)) {
      echo json_encode(array(
        "error"=>0,
        "numero"=>$respuesta->CbteNro,
        ));      
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$respuesta->mensaje
        ));
    }
  }

  function ver_puntos_venta() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;

    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);
    $respuesta = $fe->get_puntos_venta();
    print_r($respuesta);
  }  

  function update($id) {

    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }
    
    $id_empresa = parent::get_empresa();
    
    $this->load->helper("fecha_helper");
    $this->load->model("Log_Model");
    $this->load->model("Punto_Venta_Model");
    $this->load->model("Tipo_Comprobante_Model");
    
    // Comenzamos la transaccion
    $this->db->trans_start();    
    
    $array = $this->parse_put();

    $this->Log_Model->imprimir(array(
      "id_empresa"=>$id_empresa,
      "file"=>date("Ymd")."_update_facturas.txt",
      "texto"=>"UPDATE: ".print_r($array,TRUE)."\n\n",
    ));

    // Obtenemos los valores que estan guardados actualmente en la BD
    $sql = "SELECT * FROM facturas WHERE id = $id AND id_empresa = $id_empresa AND id_punto_venta = $array->id_punto_venta ";
    $q = $this->db->query($sql);
    if ($q === FALSE || $q->num_rows()==0) {
      $this->show_error("No se encuentra una factura con ID: $id");
      exit();
    }
    $factura = $q->row();
    
    // Obtenemos el tipo de comprobante
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($array->id_tipo_comprobante);
    
    // Obtenemos el punto de venta
    $punto_venta = $this->Punto_Venta_Model->get($array->id_punto_venta);    
    $id_sucursal = $array->id_sucursal;
    if ($punto_venta === FALSE) {
      $punto_venta = new stdClass();
      $punto_venta->numero = "0";
      $punto_venta->tipo_impresion = "";
      $punto_venta->id_caja = 0;
      $id_sucursal = 0;
    } else {
      $id_sucursal = $punto_venta->id_sucursal;
    }

    $this->load->model("Articulo_Model");
    $this->load->model("Almacen_Model");
    $this->load->model("Vendedor_Model");
    $this->load->model("Cliente_Model");
    $this->load->model("Stock_Model");
    $array->tipo_punto_venta = $punto_venta->tipo_impresion;
    $almacen = $this->Almacen_Model->get($array->id_sucursal);
    $array->sucursal = (!empty($almacen)) ? $almacen->nombre : "";
    if (!empty($array->id_vendedor)) {
      $vendedor = $this->Vendedor_Model->get($array->id_vendedor);
      $array->vendedor = ($vendedor !== FALSE) ? $vendedor->nombre : "";
    } else {
      $array->vendedor = "";
    }    
    $array->tipo_comprobante = $tipo_comprobante->nombre;
    $array->id_cliente = (empty($array->id_cliente)) ? 0 : $array->id_cliente;
    if ($array->id_empresa == 228) {
      $this->load->model("Pres_Cliente_Model");
      $cliente = $this->Pres_Cliente_Model->get($array->id_cliente,$array->id_empresa);  
      if ($cliente !== FALSE) $cliente->nombre = $cliente->nombre." ".$cliente->apellido;
    } else {
      $cliente = $this->Cliente_Model->get_by_id($array->id_cliente,array("id_empresa"=>$array->id_empresa));
    }
    $array->cliente = ($cliente !== FALSE) ? $cliente->nombre : "";
    
    $array->id_empresa = $id_empresa;
    if (is_null($array->id_vendedor)) $array->id_vendedor = 0;
    $fecha = $array->fecha;
    $array->fecha = (isset($array->fecha)) ? fecha_mysql($array->fecha) : date("Y-m-d");
    $array->hora = (isset($array->hora)) ? $array->hora : date("H:i:s");
    if (isset($array->fecha_vto) && !empty($array->fecha_vto)) $array->fecha_vto = fecha_mysql($array->fecha_vto);
    
    if (isset($array->fecha_reparto)) $array->fecha_reparto = fecha_mysql($array->fecha_reparto);
    else $array->fecha_reparto = date("Y-m-d");
    
    $id_usuario = $_SESSION["id"];
    $array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;    

    //$array->subtotal = (float)$array->total + (float)$array->descuento;

    // Actualizamos el estado
    if ($id_empresa == 133) $array->id_tipo_estado = 0;
    
    $array->comprobante = $tipo_comprobante->letra." ".str_pad($punto_venta->numero,4,"0",STR_PAD_LEFT)."-".str_pad($array->numero,8,"0",STR_PAD_LEFT);
    $array->hash = md5($array->id_empresa.$array->comprobante);    
    
    $items = $array->items;
    $tarjetas = $array->tarjetas;
    $cheques = $array->cheques;
    $this->modelo->limpiar($array);
    
    // Si el comprobante es en EFECTIVO
    if ($array->tipo_pago == "E") {
      $array->pago = -$array->total;
      $array->pagada = 1;
    // Si el comprobante es CUENTA CORRIENTE
    } else if ($array->tipo_pago == "C") {
      $array->pagada = 0;
      $array->pago = 0;
    }
    
    // Si es un comprobante C, NO se informa IVA y el neto es igual al total
    if ($tipo_comprobante->letra == "C") {
      $array->iva = 0;
      $array->neto = $array->total;
    }    

    // Si es una factura periodica
    $es_periodica = (isset($array->es_periodica)) ? $array->es_periodica : 0;
    $periodo_cantidad = (isset($array->periodo_cantidad)) ? $array->periodo_cantidad : 1;
    $periodo_tipo = (isset($array->periodo_tipo)) ? $array->periodo_tipo : 'M';
    $periodo_dia = (isset($array->periodo_dia)) ? $array->periodo_dia : 1;
    $dias_vencimiento = (isset($array->dias_vencimiento)) ? $array->dias_vencimiento : 10;
    
    $array->id_punto_venta = (isset($punto_venta->id) ? $punto_venta->id : 0);
    $this->modelo->save($array);

    // Si el punto de venta tiene una caja asignada, tenemos que hacer el movimiento en dicha caja
    if ($punto_venta->id_caja != 0) {
      $this->load->model("Caja_Movimiento_Model");
      $this->Caja_Movimiento_Model->borrar(array(
        "id_empresa"=>$array->id_empresa,
        "id_factura"=>$id,
        "id_punto_venta"=>$array->id_punto_venta,
        "id_caja"=>$punto_venta->id_caja,
      ));
      $this->Caja_Movimiento_Model->ingreso(array(
        "id_empresa"=>$array->id_empresa,
        "id_factura"=>$id,
        "id_punto_venta"=>$array->id_punto_venta,
        "id_sucursal"=>$array->id_sucursal,
        "fecha"=>$array->fecha." ".$array->hora,
        "id_caja"=>$punto_venta->id_caja,
        "observaciones"=>$array->comprobante,
        "id_usuario"=>$array->id_usuario,
        "monto"=>($array->efectivo - $array->vuelto),
      ));
    }    
    
    // Guardamos la relacion con vendedores
    if (isset($array->id_vendedor) && ($array->id_cliente != 0 && $array->id_vendedor != 0)) {
      $this->db->query("UPDATE clientes SET id_vendedor = $array->id_vendedor WHERE id = $array->id_cliente");
    }    

    $sql_1 = "DELETE FROM facturas_items WHERE id_factura = $id AND id_empresa = $array->id_empresa AND id_punto_venta = $array->id_punto_venta";
    $this->db->query($sql_1);
    $ivas = array();
    $costo_final_factura = 0;
    $en_oferta = 0;
    $i=0;

    foreach($items as $l) {

      $id_variante = (isset($l->id_variante) ? $l->id_variante : 0);
      $custom_3 = (isset($l->custom_3) ? $l->custom_3 : ""); // Si reservo stock

      // Si pasamos de un estado no finalizado, a FINALIZADO
      if ($array->id_tipo_estado == 6 && $factura->id_tipo_estado != 6) {
        // Debitamos el reservado
        if ($custom_3 == "1") {
          $this->Stock_Model->reservar(array(
            "id_articulo"=>$l->id_articulo,
            "id_variante"=>$id_variante,
            "id_almacen"=>$array->id_sucursal,
            "cantidad"=>($l->cantidad * -1),
          ));
          $custom_3 = ""; // Limpiamos para que ya no queden marcados como reservados
        }
        // Debitamos el stock actual
        $this->Stock_Model->sacar($l->id_articulo,$l->cantidad,$array->id_sucursal,'B',"","Mov. Stock Reservado",0,$id_variante);
      }

      if (isset($l->id_articulo) && !empty($l->id_articulo)) {
        $articulo = $this->Articulo_Model->get_nombre($l->id_articulo,array(
          "id_empresa"=>$array->id_empresa
        ));
        if (!isset($l->id_rubro) || $l->id_rubro == 0) {
          $l->id_rubro = $articulo->id_rubro;
        }
      }

      // Arreglo victor
      if (isset($l->porc_iva) && $l->porc_iva == 0) $l->id_tipo_alicuota_iva = 3;

      if ($l->cantidad < 0) $l->tipo_cantidad = "D"; // Si la cantidad es negativa, es una devolucion
      
      // Si la bonificacion es 100%, el tipo cantidad = B
      if (isset($l->bonificacion) && $l->bonificacion == 100) $l->tipo_cantidad = "B";      

      $anulado = (isset($l->anulado) ? $l->anulado : 0);

      $item_array = array(
        "id_empresa"=>$array->id_empresa,
        "id_punto_venta"=>$array->id_punto_venta,
        "id_factura"=>$id,
        "id_articulo"=>(isset($l->id_articulo) ? $l->id_articulo : 0),
        "tipo"=>(isset($l->tipo) ? $l->tipo : 0),
        "tipo_cantidad"=>(isset($l->tipo_cantidad) ? $l->tipo_cantidad : ""),
        "cantidad"=>(isset($l->cantidad) ? $l->cantidad : 0),
        "precio"=>(isset($l->precio) ? $l->precio : 0),
        "neto"=>(isset($l->neto) ? $l->neto : 0),
        "porc_iva"=>(isset($l->porc_iva) ? $l->porc_iva : 0),
        "id_tipo_alicuota_iva"=>(isset($l->id_tipo_alicuota_iva) ? $l->id_tipo_alicuota_iva : 0),
        "nombre"=>(isset($l->nombre) ? $l->nombre : ""),
        "descripcion"=>(isset($l->descripcion) ? $l->descripcion : ""),
        "bonificacion"=>((isset($l->bonificacion)) ? $l->bonificacion : 0),
        "total_con_iva"=>(isset($l->total_con_iva) ? $l->total_con_iva : 0),
        "total_sin_iva"=>(isset($l->total_sin_iva) ? $l->total_sin_iva : 0),
        "iva"=>(isset($l->iva) ? $l->iva : 0),
        "anulado"=>$anulado,
        "orden"=>$i,
        "id_rubro"=>(isset($l->id_rubro) ? $l->id_rubro : 0),
        "costo_final"=>(isset($l->costo_final) ? $l->costo_final : 0),
        "custom_1"=>(isset($l->custom_1) ? $l->custom_1 : ""), // Codigo leido por el lector
        "custom_2"=>(isset($l->custom_2) ? $l->custom_2 : ""), // Articulo de OFERTA
        "custom_3"=>(isset($custom_3) ? $custom_3 : ""),
        "custom_4"=>(isset($l->custom_4) ? $l->custom_4 : ""), // 868 = COSTO FINAL CENTRAL
        "id_proveedor"=>(isset($l->id_proveedor) ? $l->id_proveedor : 0),
        "id_variante"=>(isset($id_variante) ? $id_variante : 0),
        "stamp"=>$factura->last_update,
      );
      $this->db->insert("facturas_items",$item_array);
      
      // Sumamos las alicuotas de IVA
      if (!isset($ivas[$l->id_tipo_alicuota_iva])) {
        $ivas[$l->id_tipo_alicuota_iva] = array("neto"=>0,"iva"=>0);
      }
      $ivas[$l->id_tipo_alicuota_iva]["neto"] += ($l->total_sin_iva * ((100-$array->porc_descuento) / 100));
      $ivas[$l->id_tipo_alicuota_iva]["iva"] += ($l->iva * ((100-$array->porc_descuento) / 100));

      if ($anulado == 0) {
        $costo_final_factura = $costo_final_factura + ((float)$this->defecto($l->costo_final));
      }
      
      $i++;
    }

    // Si tenemos que guardar una factura periodica
    $this->modelo->guardar_factura_periodica(array(
      "id_factura"=>$id,
      "id_punto_venta"=>$array->id_punto_venta,
      "id_empresa"=>$array->id_empresa,
      "fecha"=>$array->fecha,
      "es_periodica"=>$es_periodica,
      "periodo_dia"=>$periodo_dia,
      "periodo_tipo"=>$periodo_tipo,
      "periodo_cantidad"=>$periodo_cantidad,
      "dias_vencimiento"=>$dias_vencimiento
    ));

    // Actualizamos algunos valores calculados de la factura
    $sql_update = "UPDATE facturas SET ";
    $sql_update.= " costo_final = $costo_final_factura ";
    $sql_update.= "WHERE id_empresa = $array->id_empresa AND id_punto_venta = $array->id_punto_venta AND id = $id ";
    $this->db->query($sql_update);

    
    $this->db->query("DELETE FROM facturas_iva WHERE id_empresa = $id_empresa AND id_factura = $id");
    foreach($ivas as $id_alicuota_iva => $iva) {

      // ARREGLO POR EL TEMA DE LOS AJUSTES
      $s = $this->modelo->calcular_iva_segun_alicuota($id_alicuota_iva,$iva["neto"]);

      $this->db->insert("facturas_iva",array(
        "id_empresa"=>$id_empresa,
        "id_factura"=>$id,
        "id_alicuota_iva"=>$id_alicuota_iva,
        "id_punto_venta"=>$array->id_punto_venta,
        "neto"=>$iva["neto"],
        "iva"=>$s,
      ));
    }    
        /*
    if (!empty($tarjetas)) {
      $this->load->model("Cupon_Tarjeta_Model");
      // GUARDAMOS LAS TARJETAS
      $this->db->query("DELETE FROM cupones_tarjetas WHERE id_factura = $id AND id_empresa = $id_empresa");
      foreach($tarjetas as $t) {
        $t->id_factura = $id;
        $t->id_empresa = $id_empresa;
        $t->fecha = date("Y-m-d H:i:s");
        $this->Cupon_Tarjeta_Model->insert($t);
      }      
    }
        
    if (!empty($cheques)) {
      $this->load->model("Cheque_Model");
      // GUARDAMOS LOS CHEQUES
      $this->db->query("DELETE FROM cheques WHERE id_factura = $id AND id_empresa = $id_empresa");
      foreach($cheques as $ch) {
        $ch->id_factura = $id;
        $ch->fecha_recibido = date("Y-m-d");
        $ch->fecha_emision = fecha_mysql($ch->fecha_emision);
        $ch->fecha_cobro = fecha_mysql($ch->fecha_cobro);
        $ch->id_empresa = $id_empresa;
        $ch->tipo = "C";
        $ch->id_cliente = $array->id_cliente;
        $this->Cheque_Model->insert($ch);
      }      
    }
    */
    
    // Finalizamos la transaccion
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $mensaje = $this->db->_error_message();
      $this->show_error($mensaje);
    }


    // CITY NOTEBOOKS MANDA EL EMAIL CON EL CAMBIO DE ESTADO
    if ($array->id_empresa == 1003 && ($array->id_tipo_estado == 6 || $array->id_tipo_estado == 7) && ($array->id_tipo_estado != $factura->id_tipo_estado) && !empty($cliente->email)) {
      $this->load->model("Email_Template_Model");
      $temp = $this->Email_Template_Model->get_by_key("cambio-estado-factura",$array->id_empresa);
      if ($temp !== FALSE) {
        $body = $temp->texto;
        $body = str_replace("{{nombre}}", $cliente->nombre, $body);
        $body = str_replace("{{link_estado}}", "https://www.varcreative.com/sandbox/1003/web/estado/?id=".$id."&id_punto_venta=".$array->id_punto_venta, $body);
        $bcc_array = array("basile.matias99@gmail.com");
        require APPPATH.'libraries/Mandrill/Mandrill.php';
        mandrill_send(array(
          "to"=>$cliente->email,
          "from_name"=>"City Notebooks",
          "subject"=>$temp->nombre,
          "body"=>$body,
          "bcc"=>$bcc_array,
        ));
      }
    }
    
    $imprimir = 0;
    
    // Utiliza factura electronica
    if ($punto_venta->tipo_impresion == "E" && $array->pendiente == 1) {
      $res = $this->modelo->obtener_cae($id);
      if ($res["error"] == 1) {
        echo json_encode($res); return;
      } else {
        $this->db->query("UPDATE facturas SET pendiente = 0 WHERE id = $id AND id_empresa = $array->id_empresa");
        $imprimir = 1;
      }
    }
    
    $tipo_impresion = $punto_venta->tipo_impresion;
    if ($array->estado == 1) $tipo_impresion = "P"; // FACTURA PREIMPRESA    
    
    $salida = array(
      "id"=>$id,
      "imprimir"=>$imprimir,
      "error"=>0,
      "tipo_impresion"=>$tipo_impresion
      );
    echo json_encode($salida);
  }
  
  function delete($id = NULL) {
    echo "OPERACION NO PERMITIDA";
  }

  function borrar_factura($id,$id_punto_venta) {

    $id_empresa = parent::get_empresa();
    if ($id_empresa == 571) echo json_encode(array("error"=>1));
    if ($id_empresa == 980) {
      $sql = "SELECT id_empresa FROM puntos_venta WHERE id = $id_punto_venta ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $id_empresa = $r->id_empresa;
    }

    // TODO: Cuando se elimina una venta que es de la web y todavia no esta finalizada, no se debe volver a poner los productos al stock

    // Si borramos un comprobante, volvemos a ponerlo en el stock
    $this->load->model("Stock_Model");
    $this->load->model("Almacen_Model");
    $this->load->model("Empresa_Model");
    $this->load->model("Articulo_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($id_empresa);

    $q_fact = $this->db->query("SELECT * FROM facturas WHERE id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta AND id = $id");
    if ($q_fact->num_rows() == 0) {
      echo json_encode(array());
      exit();
    }
    $fact = $q_fact->row();

    $id_sucursal = $this->Almacen_Model->get_sucursal_punto_venta($id_punto_venta);
    $sql = "SELECT * FROM facturas_items ";
    $sql.= "WHERE id_factura = $id ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $sql.= "AND id_articulo != 0 ";
    $sql.= "AND (anulado IS NULL OR anulado = 0) ";
    $sql.= "AND tipo_cantidad != 'C' "; // Los cambios no tienen que mover el stock
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $id_variante = (isset($row->id_variante) ? $row->id_variante : 0);
      if ($row->custom_3 == 1) {
        // Si el stock estaba reservado, lo sacamos
        $this->Stock_Model->reservar(array(
          "cantidad"=>($row->cantidad * -1),
          "id_almacen"=>$id_sucursal,
          "id_articulo"=>$row->id_articulo,
          "id_variante"=>$id_variante,
          "fecha"=>$fact->fecha,
        ));
      } else {        
        if ($fact->id_tipo_comprobante == 3 || $fact->id_tipo_comprobante == 8 || $fact->id_tipo_comprobante == 13) {
          // Es una nota de credito
          $obs = "Anulacion NC $fact->comprobante ";
          $this->Stock_Model->sacar($row->id_articulo,$row->cantidad,$id_sucursal,'B',$fact->fecha,$obs,0,$id_variante);          
        } else {
          // Es un comprobante normal
          $obs = "Anulacion $fact->comprobante ";
          $this->Stock_Model->agregar($row->id_articulo,$row->cantidad,$id_sucursal,$fact->fecha,$obs,0,$id_variante);          
        }
      }

      if ($id_variante != 0) {
        $this->Stock_Model->ajustar_calcular_desde_variantes(array(
          "id_articulo"=>$row->id_articulo,
          "id_sucursal"=>$id_sucursal,
          "id_empresa"=>$id_empresa,
        ));
      } else {
        // Recalculamos el stock
        $this->Stock_Model->recalcular_stock(array(
          "id_articulo"=>$row->id_articulo,
          "id_sucursal"=>$id_sucursal,
          "id_empresa"=>$id_empresa,
        ));      
      }

      // Si el articulo esta compartido en mercadolibre
      if ($usa_meli) {
        $this->Articulo_Model->update_publicacion_mercadolibre($row->id_articulo,array(
          "id_empresa"=>$id_empresa,
        ));
      }
    }

    $sql = "DELETE FROM cajas_movimientos WHERE id_factura = $id AND id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);

    $sql = "DELETE FROM cupones_tarjetas WHERE id_factura = $id AND id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);

    $sql = "DELETE FROM facturas_iva WHERE id_factura = $id AND id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);

    $sql = "DELETE FROM facturas_items WHERE id_factura = $id AND id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);

    if ($this->db->table_exists('facturas_periodicas')) {
      $sql = "DELETE FROM facturas_periodicas WHERE id_factura = $id AND id_empresa = $id_empresa ";
      $sql.= "AND id_punto_venta = $id_punto_venta ";
      $this->db->query($sql);      
    }
    
    if ($this->db->table_exists('repartidores_pedidos')) {
      $sql = "DELETE FROM repartidores_pedidos WHERE id_factura = $id AND id_empresa = $id_empresa ";
      $sql.= "AND id_punto_venta = $id_punto_venta ";
      $this->db->query($sql);      
    }

    $sql = "DELETE FROM facturas WHERE id = $id AND id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);

    // Si es ARGENCASH, tenemos que volver a poner que la cuota no fue facturada
    // El ID de la cuota lo tenemos en el campo numero_referencia
    if ($id_empresa == 228) {
      $this->db->query("UPDATE pres_prestamos_cuotas SET id_factura = 0, id_punto_venta = 0 WHERE id = $fact->numero_referencia AND id_empresa = 228 ");
      $this->db->query("UPDATE pres_cajas_movimientos SET id_factura = 0, id_punto_venta = 0 WHERE id = $fact->id_referencia AND id_empresa = 228 ");
    }

    echo json_encode(array());
  }


  function marcar_visto($id,$id_punto_venta = 0) {
    $id_empresa = parent::get_empresa();
    $sql = "UPDATE facturas SET nueva = 0 WHERE id = $id AND id_empresa = $id_empresa ";
    if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>false
    ));
  }

  
  function anular($id,$id_punto_venta = 0) {
    $id_empresa = parent::get_empresa();
    $comp = $this->modelo->get($id);
    $sql = "UPDATE facturas SET anulada = 1, id_tipo_estado = 7 WHERE id = $id AND id_empresa = $id_empresa ";
    if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);

    // Si se anula una factura de toque y el punto de venta es el designado para entregar el efectivo al comercio
    $this->load->model("Empresa_Model");
    if ($this->Empresa_Model->es_toque($id_empresa) && $id_punto_venta == 2444) {
      $this->load->model("Repartidor_Caja_Movimiento_Model");
      $this->Repartidor_Caja_Movimiento_Model->borrar(array(
        "id_empresa"=>$id_empresa,
        "id_factura"=>$id,
        "id_punto_venta"=>$id_punto_venta,
      ));
    }

    // Si es toque
    if ($id_empresa == 571) {
      // Y tiene una regla de oferta, volvemos para atras la cantidad de veces que se uso
      if (!empty($comp->custom_5)) {
        $sql = "UPDATE reglas_ofertas SET codigo_cantidad_veces = codigo_cantidad_veces - 1 WHERE id_empresa = $id_empresa AND nombre = '$comp->custom_5' ";
        $this->db->query($sql);
      }      
    }

    echo json_encode(array(
      "error"=>false
    ));
  }

  function restaurar($id,$id_punto_venta = 0) {
    $id_empresa = parent::get_empresa();
    $comp = $this->modelo->get($id);
    $sql = "UPDATE facturas SET anulada = 0 WHERE id = $id AND id_empresa = $id_empresa ";
    if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>false
    ));
  }

  // Metodos utilizados en la cobranza de publicidades
  function marcar_pagada($id,$id_punto_venta,$pagada) {
    
    $id_empresa = parent::get_empresa();
    $factura = $this->modelo->get($id,$id_punto_venta);
    if ($factura === FALSE) {
      echo json_encode(array(
        "error"=>"1",
        "mensaje"=>"No existe la factura $id PV: $id_punto_venta",
        ));
      return;
    }

    $sql = "UPDATE facturas ";
    $sql.= "SET pagada = $pagada ";
    if ($pagada == 1) $sql.= ", pago = -total ";
    else $sql.= ", pago = 0 ";
    $sql.= "WHERE id = $id AND id_empresa = $id_empresa";
    $this->db->query($sql);

    if ($pagada == 1) {
      // Agregamos el pago
      $sql = "INSERT INTO facturas_pagos (id_empresa,id_pago,id_factura,monto) VALUES(";
      $sql.= "$id_empresa,0,$id,$factura->total)";
    } else {
      // Eliminamos el pago
      $sql = "DELETE FROM facturas_pagos WHERE id_empresa = $id_empresa AND id_factura = $id AND id_pago = 0 ";
    }
    $this->db->query($sql);

    echo json_encode(array(
      "error"=>"0"
      ));
  }
  function cambiar_comision($id,$comision = 0) {
    $id_empresa = parent::get_empresa();
    $this->db->query("UPDATE facturas SET comision_vendedor = $comision WHERE id = $id AND id_empresa = $id_empresa");
    echo json_encode(array(
      "error"=>"0"
      ));
  }
  
  private function get_params() {    
    $conf = array();
    $this->load->helper("fecha_helper");
    $desde = $this->input->get("desde");
    if ($desde !== FALSE) $conf["desde"] = fecha_mysql($desde);
    $hasta = $this->input->get("hasta");
    if ($hasta !== FALSE) $conf["hasta"] = fecha_mysql($hasta);
    $id_cliente = $this->input->get("id_cliente");
    if ($id_cliente !== FALSE) $conf["id_cliente"] = $id_cliente;
    $id_vendedor = $this->input->get("id_vendedor");
    if ($id_vendedor !== FALSE) $conf["id_vendedor"] = $id_vendedor;
    $id_tarjeta = $this->input->get("id_tarjeta");
    if ($id_tarjeta !== FALSE) $conf["id_tarjeta"] = $id_tarjeta;
    $lote = $this->input->get("lote");
    if ($lote !== FALSE) $conf["lote"] = $lote;
    $cupon = $this->input->get("cupon");
    if ($cupon !== FALSE) $conf["cupon"] = $cupon;
    $id_sucursal = $this->input->get("id_sucursal");
    if ($id_sucursal !== FALSE) $conf["id_sucursal"] = $id_sucursal;
    $id_punto_venta = $this->input->get("id_punto_venta");
    if ($id_punto_venta !== FALSE) $conf["id_punto_venta"] = $id_punto_venta;
    $con_anulados = $this->input->get("con_anulados");
    if ($con_anulados !== FALSE) $conf["con_anulados"] = $con_anulados;
    $id_usuario = $this->input->get("id_usuario");
    if ($id_usuario !== FALSE) $conf["id_usuario"] = $id_usuario;
    $numero = $this->input->get("numero");
    if ($numero !== FALSE) $conf["numero"] = $numero;
    $monto = $this->input->get("monto");
    if ($monto !== FALSE) $conf["monto"] = $monto;
    $monto_tipo = $this->input->get("monto_tipo");
    if ($monto_tipo !== FALSE) $conf["monto_tipo"] = $monto_tipo;
    $caja_abierta = $this->input->get("caja_abierta");
    if ($caja_abierta !== FALSE) $conf["caja_abierta"] = $caja_abierta;
    $numero_reparto = $this->input->get("numero_reparto");
    if ($numero_reparto !== FALSE) $conf["numero_reparto"] = $numero_reparto;
    $incluir_saldo = $this->input->get("incluir_saldo");
    if ($incluir_saldo !== FALSE) $conf["incluir_saldo"] = $incluir_saldo;
    $conf["estado"] = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $tipos_comprobantes = $this->input->get("tc");
    if ($tipos_comprobantes !== FALSE) $conf["tc"] = $tipos_comprobantes;
    $limit = $this->input->get("limit");
    if ($limit !== FALSE) $conf["limit"] = $limit;
    $offset = $this->input->get("offset");
    if ($offset !== FALSE) $conf["offset"] = $offset;
    $filter = $this->input->get("filter");
    if ($filter !== FALSE) $conf["filter"] = $filter;
    $conf["id_origen"] = parent::get_get("id_origen",-1);
    $conf["incluir_suma"] = parent::get_get("incluir_suma",0);
    $conf["pago"] = parent::get_get("pago",-1);
    $conf["fecha_reparto"] = parent::get_get("fecha_reparto","");
    $conf["numero_reparto"] = parent::get_get("numero_reparto","");
    $conf["tipo_cliente"] = parent::get_get("tipo_cliente","");
    $conf["hora_desde"] = parent::get_get("hora_desde","");
    $conf["hora_hasta"] = parent::get_get("hora_hasta","");
    $conf["forma_pago"] = parent::get_get("forma_pago","0");
    $conf["custom_10"] = parent::get_get("custom_10","0");
    $conf["id_concepto"] = parent::get_get("id_concepto","0");
    $conf["distribuidora"] = parent::get_get("distribuidora","0");
    $conf["tipo_estado"] = parent::get_get("tipo_estado","-1");
    $conf["in_tipos_estados"] = str_replace("-", ",", parent::get_get("in_tipos_estados",""));
    $conf["in_ids_punto_venta"] = str_replace("-", ",", parent::get_get("in_ids_punto_venta",""));
    $conf["not_in_ids_punto_venta"] = str_replace("-", ",", parent::get_get("not_in_ids_punto_venta",""));
    $conf["id_proyecto"] = ($this->input->get("id_proyecto") !== FALSE) ? $this->input->get("id_proyecto") : 0;
    $conf["codigo_articulo"] = ($this->input->get("codigo_articulo") !== FALSE) ? $this->input->get("codigo_articulo") : "";
    $conf["tipos"] = ($this->input->get("tipos") !== FALSE) ? $this->input->get("tipos") : "";
    $conf["custom_orden"] = parent::get_get("custom_orden","");
    return $conf;
  }
  
  
  function exportar_excel() {
    $this->load->model("Venta_Model");
    $conf = $this->get_params();
    $conf["limit"] = FALSE;
    $salida = $this->Venta_Model->listado($conf);
    $resultado = array();
    $header = array(
      "Fecha","Tipo","Comprobante","Cliente","Vendedor","Total"
    );
    if ($conf["distribuidora"] == 1) {
      $header[] = "Fecha Reparto";
      $header[] = "Nro. Reparto";
    }
    foreach($salida["results"] as $r) {
      $row = new stdClass();
      if ($r->negativo == 1) $r->total = -($r->total);
      $cc = array(
        "fecha"=>$r->fecha,
        "tipo_comprobante"=>$r->tipo_comprobante,
        "comprobante"=>$r->comprobante.(($r->pendiente==1)?" (Pendiente)":""),
        "cliente"=>$r->cliente.(($r->anulada==1)?" (ANULADA)":""),
        "vendedor"=>$r->vendedor,
        "total"=>(($r->anulada == 1) ? 0 : $r->total),
      );
      if ($conf["distribuidora"] == 1) {
        $cc["fecha_reparto"] = $r->fecha_reparto;
        $cc["reparto"] = $r->reparto;
      }
      $resultado[] = $cc;
    }
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>date("d/m/Y"),
      "filename"=>"listado_ventas",
      "header"=>$header,
      "footer"=>array(),
      "data"=>$resultado,
      "title"=>"Listado de Ventas",
    ));    
  }


  function consultar_comprobante($comprobante,$numero,$punto_venta) {
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;

    // Excepciones
    if ($punto_venta == 13 && $id_empresa == 228) {
      $empresa->razon_social = "MASSI MARIA FERNANDA";
      $empresa->cuit = "27-24999238-6";
    } else if ($punto_venta == 3 && $id_empresa == 228) {
      $empresa->razon_social = "CABAN MASSI SRL";
      $empresa->cuit = "30-71656564-1";
    }
    echo $empresa->cuit;
    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);
    $respuesta = $fe->consultar($comprobante,$numero,$punto_venta);
    print_r($respuesta); exit();
    /*
    if (!isset($respuesta->error)) {
      echo json_encode(array(
        "error"=>0,
        "numero"=>$respuesta->CbteNro,
      ));      
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$respuesta->mensaje
      ));
    } 
    */       
  }  
  
  function get_monedas() {
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;

    if ($punto_venta == 3 && $id_empresa == 228) {
      $empresa->razon_social = "CABAN MASSI SRL";
      $empresa->cuit = "30-71656564-1";
    }
    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);
    $respuesta = $fe->get_monedas();
    print_r($respuesta); exit();
  }    
  
  function consulta($id = 0) {
    $this->load->model("Venta_Model");
    $conf = $this->get_params();
    $conf["id"] = $id; // Si estamos buscando por un ID en particular
    $salida = $this->Venta_Model->listado($conf);
    if ($id != 0) {
      // Estamos obteniendo un solo elemento
      if (sizeof($salida["results"])>0) {
        echo json_encode($salida["results"][0]);
      } else echo json_encode(array());
    } else {
      // Estamos listando
      echo json_encode($salida);
    }
  }


  function recalcular_percepciones_iibb() {
    $id_empresa = 121;
    $fecha_desde = "2017-05-16";
    $fecha_hasta = "2017-05-31";
    $total = 0;
    $q = $this->db->query("SELECT * FROM clientes WHERE percibe_ib = 1 AND percepcion_ib > 0 AND id_empresa = $id_empresa ");
    foreach($q->result() as $cliente) {
      $sql = "UPDATE facturas SET percepcion_ib = neto * ($cliente->percepcion_ib / 100) ";
      $sql.= "WHERE id_cliente = $cliente->id AND id_empresa = $id_empresa ";
      $this->db->query($sql);
    }
  }
  

  function convertir_factura() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = parent::get_empresa();
    $id_factura = parent::get_post("id_factura",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);
    $cambiar_fecha = parent::get_post("cambiar_fecha",0);
    $salida = $this->modelo->generar_electronica(array(
      "id_empresa"=>$id_empresa,
      "id_factura"=>$id_factura,
      "id_punto_venta"=>$id_punto_venta,
      "cambiar_fecha"=>$cambiar_fecha,
    ));

    $this->load->model("Configuracion_Model");
    if ($this->Configuracion_Model->es_local() && $salida["error"] == 0) {
      // Si no dio error, es local y estamos convirtiendo, tenemos que controlar si fue subida
      // Si ya fue subida, tambien tendriamos que cambiar el CAE y fecha_vto en el servidor
      $this->load->model("Factura_Model");
      $factura = $this->Factura_Model->get($id_factura,$id_punto_venta,array(
        "id_empresa"=>$id_empresa
      ));
      if ($factura->uploaded == 1) {
        $fields = array(
          "id_empresa"=>$id_empresa,
          "id_factura"=>$id_factura,
          "id_punto_venta"=>$id_punto_venta,
          "id_tipo_comprobante"=>$salida["id_tipo_comprobante"],
          "cae"=>$salida["cae"],
          "fecha_vto"=>$salida["fecha_vto"],
          "numero"=>$salida["numero"],
          "comprobante"=>$salida["comprobante"],
        );
        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, "https://www.varcreative.com/sistema/facturas/function/actualizar_cae_servidor/");
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
      }
    }

    echo json_encode($salida);
  }

  // ESTA FUNCION ES USADA POR SI ACTUALIZAN EL COMPROBANTE EN LA CAJA Y YA HABIA SIDO SUBIDO AL SERVIDOR
  function actualizar_cae_servidor() {

    header("Access-Control-Allow-Origin: *");
    $id_empresa = parent::get_post("id_empresa",0);
    $id_factura = parent::get_post("id_factura",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);
    $id_tipo_comprobante = parent::get_post("id_tipo_comprobante",0);
    $cae = parent::get_post("cae","");
    $fecha_vto = parent::get_post("fecha_vto","");
    $numero = parent::get_post("numero","");
    $comprobante = parent::get_post("comprobante","");
    file_put_contents("log_actualizar_cae.txt", print_r($_POST,true)."\n", FILE_APPEND);
    
    $sql = "UPDATE facturas SET ";
    $sql.= " numero = '$numero', ";
    $sql.= " comprobante = '$comprobante', ";
    $sql.= " cae = '$cae', ";
    $sql.= " fecha_vto = '$fecha_vto', ";
    $sql.= " id_tipo_comprobante = '$id_tipo_comprobante', ";
    $sql.= " pendiente = 0 "; // La sacamos del pendiente por las dudas
    $sql.= "WHERE id = $id_factura ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    file_put_contents("log_actualizar_cae.txt", $sql."\n", FILE_APPEND);
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function imprimir_epson($id_factura,$id_punto_venta) {

    $this->load->helper("fecha_helper");
    $factura = $this->modelo->get($id_factura,$id_punto_venta);
    
    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($factura->id_tipo_comprobante);
    
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $empresa->direccion = $empresa->direccion_empresa;
    $empresa->telefono = $empresa->telefono_empresa;    
    
    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get($factura->id_punto_venta);

    $impresora = $punto_venta->texto_email;
    // A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
    $filename = "$impresora.txt";
    if (file_exists($filename) === FALSE) file_put_contents($filename, "");
    $file = fopen($filename, "r+");
    // Intenta adquirir un bloqueo exclusivo
    while((flock($file, LOCK_EX | LOCK_NB) === FALSE)) usleep(1000000);

    $lineas = 32;
    $sep = str_pad("",$lineas,"-",STR_PAD_LEFT)."\n";
    $cliente = $factura->cliente;

    $connector = new WindowsPrintConnector($impresora);
    $printer = new Printer($connector);
    $printer -> selectPrintMode(Printer::MODE_FONT_B);
    $printer -> setTextSize(2, 2);
    $printer -> setEmphasis(true);
    $printer -> text($empresa->razon_social."\n\n");
    $printer -> setEmphasis(false);
    $printer -> text("Fecha: ".date("d/m/Y")." Hora: ".date("H:i")."\n");
    $printer -> text("$cliente->nombre\n");
    if (!empty($cliente->direccion)) $printer -> text("Direccion: $cliente->direccion\n");
    if (!empty($cliente->telefono)) $printer -> text("Telefono: $cliente->telefono\n");
    if (!empty($factura->id_vendedor)) $printer -> text("Atendido por: ".$factura->vendedor."\n");
    $printer -> text($sep);
    foreach($factura->items as $item) {
      $precio_unit = ($item->precio * ((100-$item->bonificacion) / 100));
      $lin1 = number_format($item->cantidad,2)." x ".number_format($precio_unit,2);
      $printer -> text($lin1);
      $lin2 = "$".number_format($item->total_con_iva,2);
      $dif = $lineas - strlen($lin1) - strlen($lin2);
      $a = "";
      $a = str_pad($a,$dif," ",STR_PAD_RIGHT);
      $printer -> text($a);
      $printer -> text($lin2."\n");
      $printer -> text("$item->nombre\n");
    }
    $printer -> text($sep);
    if (isset($factura->descuento) && $factura->descuento > 0) {
      $printer -> text("Subtotal:  ".($factura->total + $factura->descuento)." \n");  
      $printer -> text("Descuento: $factura->descuento \n");
    }
    $printer -> text(" \n \n");
    $printer -> setTextSize(3, 3);
    $printer -> text("TOTAL: $factura->total \n");
    $printer -> setTextSize(2, 2);
    $printer -> text($sep);
    $printer -> text(" \n \n \n \n");
    $printer -> cut();
    $printer -> close();
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function imprimir_reporte_epson() {

    $this->load->helper("fecha_helper");
    $desde = parent::get_post("desde",date("d-m-Y"));
    $hasta = parent::get_post("hasta",date("d-m-Y"));
    $tipo_cliente = parent::get_post("tipo_cliente");
    if (empty($desde)) $desde = date("d-m-Y");
    if (empty($hasta)) $hasta = date("d-m-Y");
    $desde = fecha_mysql($desde);
    $hasta = fecha_mysql($hasta);
    $id_empresa = parent::get_empresa();
    $this->load->model("Venta_Model");
    $salida = $this->Venta_Model->resumen(array(
      "desde"=>$desde,
      "hasta"=>$hasta,
      "tipo_cliente"=>$tipo_cliente,
    ));
    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get_por_defecto();
    $impresora = $punto_venta->texto_email;
    // A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
    $filename = "$impresora.txt";
    if (file_exists($filename) === FALSE) file_put_contents($filename, "");
    $file = fopen($filename, "r+");
    // Intenta adquirir un bloqueo exclusivo
    while((flock($file, LOCK_EX | LOCK_NB) === FALSE)) usleep(1000000);
    $lineas = 32;
    $sep = str_pad("",$lineas,"-",STR_PAD_LEFT)."\n";
    $connector = new WindowsPrintConnector($impresora);
    $printer = new Printer($connector);
    $printer -> selectPrintMode(Printer::MODE_FONT_B);
    $printer -> setTextSize(2, 2);
    $printer -> setEmphasis(true);
    $printer -> text($empresa->razon_social."\n\n");
    $printer -> setEmphasis(false);
    $printer -> text("RESUMEN DE VENTAS\n");
    $printer -> text($sep);
    $printer -> text("Punto Venta: ".$punto_venta->nombre."\n");
    $printer -> text("Desde: ".fecha_es($desde)."\n");
    $printer -> text("Hasta: ".fecha_es($hasta)."\n");
    if ($tipo_cliente == "NCF") $printer -> text("SOLO CLIENTES\n");
    $printer -> text($sep);
    $printer -> text("Cantidad: ".$salida->cantidad." \n");
    $printer -> text("Total: $ ".$salida->total." \n");
    $printer -> text($sep);
    $printer -> text(" \n \n \n \n");
    $printer -> cut();
    $printer -> close();
    echo json_encode(array(
      "error"=>0,
    ));
  }
  
}