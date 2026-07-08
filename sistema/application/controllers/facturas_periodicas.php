<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Facturas_Periodicas extends REST_Controller {

  function __construct() {
    parent::__construct();
  } 

  function test() {
    $id_empresa = 622;
    $id_punto_venta = 1630;
    $fecha = "2020-03-24";
    $sql = "SELECT * FROM facturas WHERE fecha = '$fecha' AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $sql = "INSERT INTO facturas_iva (id_empresa,id_factura,id_alicuota_iva,id_punto_venta,neto,iva) VALUES ($id_empresa,$r->id,5,$r->id_punto_venta,$r->neto,$r->iva)";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }

  function crear_facturas_sergio() {
    
    $id_empresa = 622;
    $id_punto_venta = 1630;
    $id_tipo_comprobante = 999;
    $hora = date("H:i:s");
    $fecha = "2020-05-06";
    $desde = "2020-05-01";
    $hasta = "2020-05-31";
    $numero = 1;
    $this->load->model("Factura_Model");
    $this->load->model("Articulo_Model");

    //$this->db->query("DELETE FROM facturas WHERE id_empresa = $id_empresa AND id_tipo_comprobante = $id_tipo_comprobante");
    //$this->db->query("DELETE FROM facturas_items WHERE id_empresa = $id_empresa AND id_tipo_comprobante = $id_tipo_comprobante");

    $sql = "SELECT C.* FROM clientes C WHERE C.id_empresa = $id_empresa ";
    // Para no repetir
    $sql.= "AND NOT EXISTS (SELECT 1 FROM facturas F WHERE F.id_empresa = $id_empresa AND F.id_cliente = C.id AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ) ";

    $q = $this->db->query($sql);
    foreach($q->result() as $cliente) {
      $id_articulo = 0;
      if ($cliente->observaciones == "3") {
        $id_articulo = 10236384;
      } else if ($cliente->observaciones == "6") {
        $id_articulo = 10229945;
      } else if ($cliente->observaciones == "10") {
        $id_articulo = 10229946;
      } else if ($cliente->observaciones == "4") {
        $id_articulo = 10229944;
      } else if ($cliente->observaciones == "15") {
        $id_articulo = 10229947;
      }
      $articulo = $this->Articulo_Model->get($id_articulo,$id_empresa);

      if ($id_articulo == 0) continue;

      $comprobante = "R ".str_pad(3,4,"0",STR_PAD_LEFT)."-".str_pad($numero,8,"0",STR_PAD_LEFT);
      
      // Insertamos la factura
      $sql = "INSERT INTO facturas (";
      $sql.= " id_empresa, id_tipo_comprobante, id_punto_venta, fecha, punto_venta, numero, comprobante, ";
      $sql.= " tipo_pago, id_cliente, cliente, estado, pago, hora, ";
      $sql.= " total, subtotal, neto, iva ";
      $sql.= ") VALUES (";
      $sql.= " $id_empresa, 999, $id_punto_venta, '$fecha', 3, '$numero', '$comprobante', ";
      $sql.= " 'C', '$cliente->id', '$cliente->nombre', 0, 0, '$hora', ";
      $sql.= " $articulo->precio_final_dto, $articulo->precio_final_dto, $articulo->precio_final_dto, 0 ";
      $sql.= ") ";
      $q_fact = $this->db->query($sql);
      $id_factura = $this->db->insert_id();

      // Insertamos el item
      $sql = "INSERT INTO facturas_items (";
      $sql.= " id_tipo_comprobante, id_empresa, id_punto_venta, id_factura, id_articulo, cantidad, porc_iva, id_tipo_alicuota_iva, ";
      $sql.= " neto, precio, nombre, iva, total_sin_iva, total_con_iva, tipo ";
      $sql.= ") VALUES (";
      $sql.= " 999, $id_empresa, $id_punto_venta, $id_factura, $id_articulo, 1, 21, 5, ";
      $sql.= " $articulo->precio_neto, $articulo->precio_final_dto, '$articulo->nombre', 21, $articulo->precio_neto, $articulo->precio_final_dto, 0 ";
      $sql.= ") ";
      $this->db->query($sql);

      // Insertamos facturas_iva

      // Insertamos la periodicidad
      $this->Factura_Model->guardar_factura_periodica(array(
        "id_empresa"=>$id_empresa,
        "id_factura"=>$id_factura,
        "id_punto_venta"=>$id_punto_venta,
        "es_periodica"=>1,
        "dias_vencimiento"=>10,
        "periodo_dia"=>1,
        "fecha"=>$fecha, //"2020-04-01",
      ));

      $numero++;
    }
    echo "CANTIDAD FACTURAS $numero";
  }

  // FUNCION DE PRUEBA QUE SOLO ENVIA LOS EMAILS DE LAS FACTURAS SELECCIONADAS
  function enviar_emails() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = 622;

    $cantidad_facturas = 0;
    $log_file = "log_facturas_periodicas.txt";
    $this->load->model("Factura_Model");
    $this->load->model("Empresa_Model");
    $this->load->model("Email_Template_Model");
    $this->load->model("Log_Model");
    $this->load->model("Cliente_Model");
    $this->load->model("Articulo_Model");
    require APPPATH.'libraries/Mandrill/Mandrill.php';

    // Obtenemos la empresa
    $empresa = $this->Empresa_Model->get($id_empresa);

    // Obtenemos el template para enviar
    $template = $this->Email_Template_Model->get_by_key("emision-factura",$id_empresa);
    if (empty($template)) {
      $this->Log_Model->log_file($log_file,"ERROR: No existe el template de enviar factura periodica en la empresa $empresa->nombre.");
      exit();
    }

    $sql = "SELECT id, id_punto_venta FROM facturas WHERE id_empresa = $id_empresa AND fecha = '2020-03-24' AND enviada = 0 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {

      $factura = $this->Factura_Model->get($r->id,$r->id_punto_venta,array(
        "id_empresa"=>$id_empresa,
        "buscar_consultas"=>0,
        "buscar_etiquetas"=>0,
      ));
      $id_factura = $factura->id;

      // Si no existe el cliente, es porque fue eliminado
      $cliente = $this->Cliente_Model->get($factura->id_cliente,$factura->id_empresa);
      if (empty($cliente)) continue;

      $hacer_factura = FALSE;
      // SERGIO TIENE MARCADOS LO QUE QUIERE FACTURAR CON UN *
      if ( ($factura->id_empresa == 622 || $factura->id_empresa == 99) && isset($cliente->etiquetas) && !empty($cliente->etiquetas)) {
        foreach($cliente->etiquetas as $etiqueta) {
          if ($etiqueta == "*") {
            $hacer_factura = TRUE;
            break;
          }
        }
      }

      if ($hacer_factura) {
        
        $s = "Creamos FE ID_FACTURA: [$id_factura] ID_PUNTO_VENTA: [$factura->id_punto_venta] ";
        $this->Log_Model->log_file($log_file,$s);
        $cantidad_facturas++;

        // Creamos la factura electronica
        $s = $this->Factura_Model->generar_electronica(array(
          "id_empresa"=>$factura->id_empresa,
          "id_punto_venta"=>$factura->id_punto_venta,
          "id_factura"=>$id_factura,
        ));
        if ($s["error"] == 1) {
          echo $s["mensaje"]."<br/>";
          $this->Log_Model->log_file($log_file,$s["mensaje"]);
        } else {
          $factura = $this->Factura_Model->get($id_factura,$factura->id_punto_venta,array(
            "id_empresa"=>$id_empresa,
            "buscar_consultas"=>0,
            "buscar_etiquetas"=>0,
          ));                  
        }
      }

      // Controlamos si el cliente tiene cargado un email
      if (empty($cliente->email)) {
        $this->Log_Model->log_file($log_file,"ERROR: El cliente de $id_factura no tiene email cargado.");
        continue;
      }

      // Obtenemos el template para enviar
      $template = $this->Email_Template_Model->get_by_key("emision-factura",$factura->id_empresa);
      if (empty($template)) {
        $this->Log_Model->log_file($log_file,"ERROR: No existe el template de enviar factura periodica en la empresa $empresa->nombre.");
        continue;
      }
      $body = $template->texto;
      $body = str_replace("{{nombre}}", $cliente->nombre, $body);
      $body = str_replace("{{id_factura}}", $id_factura, $body);
      $body = str_replace("{{id_punto_venta}}", $factura->id_punto_venta, $body);
      $body = str_replace("{{id_empresa}}", $factura->id_empresa, $body);
      $body = str_replace("{{total}}", $factura->total, $body);
      // Nuevo link para ver la factura
      $body = str_replace("{{link_factura}}", "https://www.varcreative.com/sistema/facturas/function/ver_pdf/".$id_factura."/".$factura->id_punto_venta."/".$factura->id_empresa."/", $body);

      // Si el template tiene el placeholder de {{preference}}
      // Creamos el preference_data y se lo agregamos al mismo email tambien
      if (strpos($body, "{{preference}}")) {
        $this->load->model("Medio_Pago_Configuracion_Model");
        $preference = $this->Medio_Pago_Configuracion_Model->create_preference_mp(array(
          "id_empresa"=>$factura->id_empresa,
          "id_factura"=>$id_factura,
          "id_punto_venta"=>$factura->id_punto_venta,
          "titulo"=>$factura->comprobante,
          "monto"=>($factura->total + 0),
          "email"=>$cliente->email,
        ));
        if (!empty($preference)) $body = str_replace("{{preference}}", $preference["response"]["init_point"], $body);
      }

      $bcc_array = array();
      $bcc_array[] = "basile.matias99@gmail.com";

      mandrill_send(array(
        "to"=>$cliente->email,
        "from"=>"no-reply@varcreative.com",
        "from_name"=>$empresa->nombre,
        "subject"=>$template->nombre,
        "body"=>$body,
        "reply_to"=>$empresa->email,
        "bcc"=>$bcc_array,
      ));

      $sql = "UPDATE facturas SET enviada = 1 WHERE id = $id_factura AND id_punto_venta = $factura->id_punto_venta AND id_empresa = $factura->id_empresa ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }

  // Esta funcion la usa SERGIO para ver cuanto se va a facturar
  function calcular_facturacion() {

    $cantidad = 0;
    $total = 0;
    $id_empresa = 622;
    $sql = "SELECT * FROM clientes C WHERE C.id_empresa = 622 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $cli) {
      // Tiene un * como etiqueta
      $sql = "SELECT * FROM clientes_etiquetas_relacion CR INNER JOIN clientes_etiquetas CE ON (CR.id_empresa = CE.id_empresa AND CR.id_etiqueta = CE.id) ";
      $sql.= "WHERE CE.id_empresa = $id_empresa AND CE.nombre = '*' AND CR.id_cliente = $cli->id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        // Ahora controlamos el articulo facturado
        $codigo = trim($cli->observaciones);
        $sql = "SELECT precio_final_dto FROM articulos WHERE id_empresa = $id_empresa AND codigo = '$codigo' ";
        $qqq = $this->db->query($sql);
        if ($qqq->num_rows()>0) {
          $art = $qqq->row();
          $total += $art->precio_final_dto;
          $cantidad++;
        }
      }
    }
    echo "CANTIDAD: $cantidad. TOTAL: $total";
  }

  function emitir() {

    set_time_limit(0);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $test = 0;
    $cantidad_facturas = 0; $total_facturas = 0;
    $hoy = "2020-05-01"; //date("Y-m-d");
    $desde = "2020-05-01";
    $hasta = "2020-05-31";
    $log_file = "log_facturas_periodicas.txt";
    $this->load->model("Factura_Model");
    $this->load->model("Articulo_Model");
    $this->load->model("Log_Model");
    $this->load->model("Empresa_Model");
    $this->load->model("Cliente_Model");
    $this->load->model("Email_Template_Model");
    require APPPATH.'libraries/Mandrill/Mandrill.php';

    $sql = "SELECT F.id, F.id_empresa, F.id_punto_venta ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN facturas_periodicas FP ON (F.id_empresa = FP.id_empresa AND F.id_punto_venta = FP.id_punto_venta AND F.id = FP.id_factura) ";
    $sql.= "WHERE FP.proxima_emision = '$hoy' ";
    $sql.= "AND F.id_tipo_comprobante NOT IN (2,3,7,8,12,13,82,20,21,52,53,202,203,207,208) "; // Con esto evitamos que por error se hagan NC/ND
    // Y no existe otra factura del mismo cliente
    $sql.= "AND NOT EXISTS (SELECT 1 FROM facturas F2 WHERE F.id != F2.id AND F.id_punto_venta = F2.id_punto_venta AND F.id_empresa = F2.id_empresa AND F.id_cliente = F2.id_cliente AND F2.fecha >= '$desde' AND F2.fecha <= '$hasta' ) ";
    $q = $this->db->query($sql);
    foreach($q->result() as $f) {

      // Obtenemos la factura
      $factura = $this->Factura_Model->get($f->id,$f->id_punto_venta,array(
        "id_empresa"=>$f->id_empresa,
        "buscar_etiquetas"=>1,
      ));

      // Si no existe el cliente, es porque fue eliminado
      $cliente = $this->Cliente_Model->get($factura->id_cliente,$f->id_empresa);
      if (empty($cliente)) continue;

      // Calculamos el proximo
      $proximo = $this->Factura_Model->next_by_tipo_comprobante(array(
        "id_empresa"=>$factura->id_empresa,
        "id_tipo_comprobante"=>$factura->id_tipo_comprobante,
        "id_punto_venta"=>$factura->id_punto_venta,
      ));

      // Creamos el comprobante
      $comprobante = "R ".str_pad($factura->punto_venta, 4, "0", STR_PAD_LEFT)."-".str_pad($proximo, 8, "0", STR_PAD_LEFT);

      // Calculamos la proxima emision de la factura
      $proxima_emision = new DateTime($hoy);
      $proxima_emision->add(new DateInterval("P".$factura->periodo_cantidad.$factura->periodo_tipo));
      if ($factura->periodo_dia == 0) {
        // Es el mismo dia del mes
        $proxima_emision_f = $proxima_emision->format("Y-m-d");
      } else {
        // Es un dia especifico del mes
        $dia_mes = str_pad($factura->periodo_dia, 2, "0", STR_PAD_LEFT);
        $proxima_emision_f = $proxima_emision->format("Y-m-".$dia_mes);
      }
      $proximo_vencimiento = new DateTime($proxima_emision_f);
      $proximo_vencimiento->add(new DateInterval("P".$factura->dias_vencimiento."D"));
      $proximo_vencimiento_f = $proximo_vencimiento->format("Y-m-d");

      // Duplicamos la factura
      $factura->id_tipo_comprobante = 999;
      $factura->tipo_comprobante = "Remito";
      $factura->id = 0;
      $factura->total = 0;
      $factura->neto = 0;
      $factura->iva = 0;
      $factura->costo_final = 0;
      $factura->fecha = $hoy;
      $factura->hora = date("H:i:s");
      $factura->hash = "";
      $factura->comprobante = $comprobante;
      $factura->numero = $proximo;
      $factura->visto = 0;
      $factura->enviada = 0;
      $factura->impresa = 0;
      $factura->nueva = 0;
      $factura->id_tipo_estado = 0;
      $factura->id_referencia = 0;
      $factura->numero_referencia = 0;
      $factura->costo_envio = 0;
      $factura->numero_remito = 0;
      $factura->pendiente = 0;
      $factura->fecha_vto = "0000-00-00";
      $factura->estado = 0;
      $factura->cae = "";
      $items = array();
      foreach($factura->items as $item) {
        $item->id_factura = 0;
        $articulo = $this->Articulo_Model->get_by_id($item->id_articulo,array(
          "id_empresa"=>$factura->id_empresa
        ));
        if (!empty($articulo)) {
          $item->neto = $articulo->precio_final_dto / ((100+$articulo->porc_iva)/100);
          $item->precio = $articulo->precio_final_dto;
          $item->costo_final = $articulo->costo_final * $item->cantidad;
          $item->iva = $item->neto * ($articulo->porc_iva / 100) * $item->cantidad;
          $item->total_sin_iva = $item->neto * $item->cantidad;
          $item->total_con_iva = $item->precio * $item->cantidad;
        }
        $factura->costo_final += $item->costo_final;
        $factura->neto += $item->total_sin_iva;
        $factura->subtotal += $item->total_con_iva;
        $factura->total += $item->total_con_iva;
        $factura->iva += $item->iva;
        $items[] = $item;
      }

      // Guardamos los items
      $factura_anterior = clone $factura;
      $email = $factura_anterior->cliente->email;
      $factura->cliente = $factura_anterior->cliente->nombre;
      $factura->efectivo = 0;
      $factura->cta_cte = 0;
      $factura->tarjeta = 0;
      $factura->cheque = 0;
      $factura->vuelto = 0;
      $factura->tipo_pago = "C";
      $factura->pagada = 0;
      $factura->pago = 0;
      $id_factura = $this->Factura_Model->save($factura);
      foreach($items as $item) {
        $item->id_punto_venta = $factura->id_punto_venta;
        $item->id_factura = $id_factura;
        $item->id = 0;
        unset($item->codigo);
        unset($item->codigo_barra);
        unset($item->variante);
        $this->db->insert("facturas_items",$item);
      }

      if ($test == 0) {
        // Actualizamos los numeros de comprobante
        $sql = "UPDATE numeros_comprobantes SET ultimo = $proximo ";
        $sql.= "WHERE id_empresa = $factura_anterior->id_empresa AND id_punto_venta = $factura_anterior->id_punto_venta AND id_tipo_comprobante = $factura_anterior->id_tipo_comprobante ";
        $this->db->query($sql);

        // Guardamos los datos de la proxima factura
        $this->Factura_Model->guardar_factura_periodica(array(
          "id_empresa"=>$factura_anterior->id_empresa,
          "id_punto_venta"=>$factura_anterior->id_punto_venta,
          "id_factura"=>$id_factura,
          "fecha"=>$hoy,
          "es_periodica"=>1,
          "periodo_cantidad"=>$factura_anterior->periodo_cantidad,
          "periodo_tipo"=>$factura_anterior->periodo_tipo,
          "periodo_dia"=>$factura_anterior->periodo_dia,
          "dias_vencimiento"=>$factura_anterior->dias_vencimiento,
        ));
      }

      $hacer_factura = FALSE;
      // SERGIO TIENE MARCADOS LO QUE QUIERE FACTURAR CON UN *
      if ( ($factura_anterior->id_empresa == 622 || $factura_anterior->id_empresa == 99) && isset($factura_anterior->cliente->etiquetas) && !empty($factura_anterior->cliente->etiquetas)) {
        foreach($factura_anterior->cliente->etiquetas as $etiqueta) {
          if ($etiqueta == "*") {
            $hacer_factura = TRUE;
            break;
          }
        }
      }

      if ($hacer_factura) {
        
        $s = "Creamos FE ID_FACTURA: [$id_factura] ID_PUNTO_VENTA: [$factura_anterior->id_punto_venta] ";
        $this->Log_Model->log_file($log_file,$s);
        $cantidad_facturas++;
        $total_facturas += $factura->total;

        if ($test == 0) {
          // Creamos la factura electronica
          $s = $this->Factura_Model->generar_electronica(array(
            "id_empresa"=>$factura_anterior->id_empresa,
            "id_punto_venta"=>$factura_anterior->id_punto_venta,
            "id_factura"=>$id_factura,
          ));
          if ($s["error"] == 1) {
            echo $s["mensaje"]."<br/>";
            $this->Log_Model->log_file($log_file,$s["mensaje"]);
          }
        }
      }

      // Actualizamos el HASH
      $hash = md5($factura->id_empresa."-".$factura->id_punto_venta."-".$id_factura."-".$hoy);
      $this->db->query("UPDATE facturas SET hash = '$hash' WHERE id = $id_factura AND id_empresa = $factura->id_empresa AND id_punto_venta = $factura->id_punto_venta");

      // Controlamos si el cliente tiene cargado un email
      if (empty($email)) {
        $this->Log_Model->log_file($log_file,"ERROR: El cliente de $id_factura no tiene email cargado.");
        continue;
      }

      if ($test == 0) {
        // Obtenemos la empresa
        $empresa = $this->Empresa_Model->get($factura_anterior->id_empresa);

        // Obtenemos el template para enviar
        $template = $this->Email_Template_Model->get_by_key("emision-factura",$factura_anterior->id_empresa);
        if (empty($template)) {
          $this->Log_Model->log_file($log_file,"ERROR: No existe el template de enviar factura periodica en la empresa $empresa->nombre.");
          continue;
        }
        $body = $template->texto;
        $body = str_replace("{{nombre}}", $factura_anterior->cliente->nombre, $body);
        $body = str_replace("{{id_factura}}", $id_factura, $body);
        $body = str_replace("{{id_punto_venta}}", $factura_anterior->id_punto_venta, $body);
        $body = str_replace("{{id_empresa}}", $factura_anterior->id_empresa, $body);
        $body = str_replace("{{total}}", $factura_anterior->total, $body);

        // Nuevo link para ver la factura
        $body = str_replace("{{link_factura}}", "https://www.varcreative.com/sistema/facturas/function/ver_pdf/".$id_factura."/".$factura_anterior->id_punto_venta."/".$factura_anterior->id_empresa."/", $body);
        //$body = str_replace("{{link_factura}}", "https://www.varcreative.com/sistema/facturas/function/ver/".$hash, $body);

        // Si el template tiene el placeholder de {{preference}}
        // Creamos el preference_data y se lo agregamos al mismo email tambien
        if (strpos($body, "{{preference}}")) {
          $this->load->model("Medio_Pago_Configuracion_Model");
          $preference = $this->Medio_Pago_Configuracion_Model->create_preference_mp(array(
            "id_empresa"=>$factura_anterior->id_empresa,
            "id_factura"=>$id_factura,
            "id_punto_venta"=>$factura_anterior->id_punto_venta,
            "titulo"=>$comprobante,
            "monto"=>($factura->total + 0),
            "email"=>$email,
          ));
          if (!empty($preference)) $body = str_replace("{{preference}}", $preference["response"]["init_point"], $body);
        }

        $bcc_array = array();
        $bcc_array[] = "basile.matias99@gmail.com";

        mandrill_send(array(
          "to"=>$email,
          "from"=>"no-reply@varcreative.com",
          "from_name"=>$empresa->nombre,
          "subject"=>$template->nombre,
          "body"=>$body,
          "reply_to"=>$empresa->email,
          "bcc"=>$bcc_array,
        ));
      }
    }
    echo "CANTIDAD FACTURAS: $cantidad_facturas. TOTAL: $total_facturas";
  }
    
}