<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Impresor_Fiscal extends CI_Controller {

  private $impresor;

  function __construct() {
    parent::__construct();
    // Cargamos la libreria dependiendo del tipo de controlador que tiene configurado
    $this->load->library("Fiscal_Hasar");
    $this->impresor = new Fiscal_Hasar();
  }

  function test() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    echo $this->impresor->ultima_factura_a();
  }

  function test_epson() {
    echo APPPATH;
  }

  function estado() {
    $this->impresor->estado();
  }

  function cancelar() {
    $this->impresor->cancelar();
    echo json_encode(array("result"=>1));
  }

  function abrir_cajon() {
    $this->impresor->abrir_cajon();
    echo json_encode(array("result"=>1));
  }

  function imprimir_x() {
    $this->impresor->imprimir_x();
    echo json_encode(array("result"=>1));
  }    

  function imprimir_z() {
    $this->impresor->imprimir_z();
    echo json_encode(array("result"=>1));
  }

  // Imprimir cierre X o Z
  function imprimir_cierre_epson($tipo) {
    $sql = "SELECT * FROM puntos_venta ";
    $q = $this->db->query($sql);
    $punto_venta = $q->row();
    $numero_puerto = $punto_venta->numero_puerto;
    $velocidad = $punto_venta->velocidad;
    $fl = "\r\n";
    $sep = ";;;";
    $epson = "";
    $epson.= "puerto".$sep.$numero_puerto.$fl;
    $epson.= "velocidad".$sep.$velocidad.$fl;
    $epson.= "conectar_impresora".$fl;
    if ($tipo == "X") {
      $epson.= "imprimir_x".$fl;
    } else if ($tipo == "Z") {
      $epson.= "imprimir_z".$fl;
    }
    $epson.= "desconectar_impresora";
    file_put_contents(APPPATH.'libraries/epson/epson.txt', $epson);
    shell_exec('C:/xampp/htdocs/sistema/application/libraries/epson/Epson.exe');
    echo json_encode(array("error"=>0));
  }


  function imprimir_remito($id) {

    // Obtenemos la ultima factura
    $this->load->model("Factura_Model");
    $f = $this->Factura_Model->get($id);
    if ($f === FALSE) {
      echo json_encode(array("result"=>0)); return;
    }

    // Si tiene cliente
    $id_tipo_iva = 0;
    $letra = "B";
    $cliente = array();
    if ($f->id_cliente != 0) {
      $id_tipo_iva = $f->cliente->id_tipo_iva;
      if ($id_tipo_iva == 1) $tipo_iva = "Responsable Inscripto";
      else if ($id_tipo_iva == 4) $tipo_iva = "Consumidor Final";
      else if ($id_tipo_iva == 2) $tipo_iva = "Monotributo";
      else if ($id_tipo_iva == 3) $tipo_iva = "Exento";
      $cliente = array(
        "razon_social" => $f->cliente->nombre,
        "numero" => $f->cliente->cuit,
        "tipo_iva" => $tipo_iva,
        "domicilio" => $f->cliente->direccion,
        "tipo_doc" => $f->cliente->id_tipo_documento,
      );
    }
    $comprobante = 114; // REMITO
    $this->impresor->comenzar($comprobante,$cliente);
    $this->impresor->imprimir_item_remito(" ");
    foreach($f->items as $item) {
      $this->impresor->imprimir_item_remito(number_format($item->cantidad,2)." x ".mb_convert_encoding($item->nombre, 'ISO-8859-1', 'UTF-8')." x ".number_format($item->precio,2));
      $this->impresor->imprimir_item_remito("Subtotal: ".number_format($item->total_con_iva,2));
      $this->impresor->imprimir_item_remito(" ");
      $this->impresor->imprimir_item_remito(" ");
    }
    $this->impresor->imprimir_item_remito("TOTAL: ".number_format($f->total,2));
    $this->impresor->imprimir_item_remito(" ");
    $this->impresor->imprimir_item_remito(" ");
    $this->impresor->cerrar($comprobante);

    echo json_encode(array("result"=>1));
  }


  function imprimir($id) {

    // Obtenemos la ultima factura
    $this->load->model("Factura_Model");
    $f = $this->Factura_Model->get($id);
    if ($f === FALSE) {
      echo json_encode(array("result"=>0)); return;
    }

    // Si es un remito, primero lo convertimos
    if ($f->id_tipo_comprobante == 999) {
      $res = $this->Factura_Model->convertir($id);
      if ($res["error"] == 1) {
        echo json_encode($res); return;
      }
      // Volvemos a cargar, pero ahora como factura
      $f = $this->Factura_Model->get($id);
    }
    file_put_contents("log_impresor_fiscal.txt", print_r($f,TRUE), FILE_APPEND);

    // Si tiene cliente
    $id_tipo_iva = 0;
    $letra = "B";
    $cliente = array();
    if ($f->id_cliente != 0) {
      $id_tipo_iva = $f->cliente->id_tipo_iva;
      if ($id_tipo_iva == 1) $tipo_iva = "Responsable Inscripto";
      else if ($id_tipo_iva == 4) $tipo_iva = "Consumidor Final";
      else if ($id_tipo_iva == 2) $tipo_iva = "Monotributo";
      else if ($id_tipo_iva == 3) $tipo_iva = "Exento";
      $cliente = array(
        "razon_social" => $f->cliente->nombre,
        "numero" => $f->cliente->cuit,
        "tipo_iva" => $tipo_iva,
        "domicilio" => $f->cliente->direccion,
        "tipo_doc" => $f->cliente->id_tipo_documento,
      );
    }

    $comprobante = $this->get_comprobante($f->id_tipo_comprobante);

    $perc_ib = ((float)$f->percepcion_ib);
    $percep_viajes = ((float)$f->percep_viajes);
    $impuesto_pais = ((!empty($f->custom_1)) ? ((float)$f->custom_1) : 0);
    $descuento = ((float)$f->descuento);
    $efectivo = ((float)$f->efectivo) + $percep_viajes;
    $cta_cte = ((float)$f->cta_cte);
    $tarjeta = ((float)$f->tarjeta);
    $cheque = ((float)$f->cheque);

    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get($f->id_punto_venta);
    if ($punto_venta->imp_fiscal == "Epson") {

      $numero_puerto = $punto_venta->numero_puerto;
      $velocidad = $punto_venta->velocidad;

      // RIO GRANDE ES EXENTO
      $id_alicuota_iva = ($punto_venta->id_empresa == 249 && $punto_venta->id_sucursal == 23) ? 1 : 5;
      $porc_iva = ($punto_venta->id_empresa == 249 && $punto_venta->id_sucursal == 23) ? 0 : 21;

      // Tenemos que armar un archivo txt
      $fl = "\r\n";
      $sep = ";;;";
      $epson = "";
      $epson.= "puerto".$sep.$numero_puerto.$fl;
      $epson.= "velocidad".$sep.$velocidad.$fl;
      $epson.= "conectar_impresora".$fl;

      if (empty($cliente)) {
        $cliente = array(
          "razon_social"=>"Consumidor Final",
          "numero"=>"",
          "tipo_iva"=>"Consumidor Final",
          "domicilio"=>"",
          "tipo_doc"=>0,
        );
      }

      // El comprobante depende si tenemos definido o no el cliente
      if ($cliente["razon_social"] != "Consumidor Final") {
        // Tique factura A/B/C/M

        if ($cliente["tipo_iva"] == "Consumidor Final") {
          $cliente["tipo_iva"] = "F"; // CF
        } else if ($cliente["tipo_iva"] == "Monotributo") {
          $cliente["tipo_iva"] = "M"; // Monotributo
        } else if ($cliente["tipo_iva"] == "Responsable Inscripto") {
          $cliente["tipo_iva"] = "I"; // RI
        } else if ($cliente["tipo_iva"] == "Exento") {
          $cliente["tipo_iva"] = "E"; // Exento
        }
        if (empty($cliente["numero"])) $cliente["tipo_doc"] = "D";
        if ($cliente["tipo_doc"] == 96) $cliente["tipo_doc"] = "D"; // DNI
        else if ($cliente["tipo_doc"] == 86) $cliente["tipo_doc"] = "L"; // CUIL
        else if ($cliente["tipo_doc"] == 80) $cliente["tipo_doc"] = "T"; // CUIT
        else if ($cliente["tipo_doc"] == 90) $cliente["tipo_doc"] = "V"; // Libreta Civica
        else if ($cliente["tipo_doc"] == 89) $cliente["tipo_doc"] = "E"; // Libreta de enrolamiento
        else if ($cliente["tipo_doc"] == 94) $cliente["tipo_doc"] = "P"; // Pasaporte
        else $cliente["tipo_doc"] = "D";

        if (empty($cliente["domicilio"])) $cliente["domicilio"] = "Sin especificar";
        $cliente["numero"] = str_replace("-", "", $cliente["numero"]);

        $comando = "0B01|0000|".$cliente["razon_social"]."||".$cliente["domicilio"]."|||".$cliente["tipo_doc"]."|".$cliente["numero"]."|".$cliente["tipo_iva"]."|001-00001-00000001||";
        $epson.= "enviar_comando".$sep.$comando.$fl;

      } else {
        // Tique normal
        $epson.= "abrir_comprobante".$sep."1".$fl;
      }

      $bonificados = array();

      // SI ES RIO GRANDE, ANALIZAMOS SI LOS ITEMS TIENEN ALGUN DESCUENTO
      // SI ES ASI, SOLAMENTE MOSTRAMOS UN SOLO ITEM CON EL TOTAL
      // Y EL DETALLE SE IMPRIMIRA COMO REMITO
      if ($f->id_punto_venta == 1499) {
        $reemplazar_items = false;
        $nuevo_item = new stdClass();
        $nuevo_item->precio = 0;
        $nuevo_item->anulado = 0;
        $nuevo_item->cantidad = 1;
        $nuevo_item->tipo_cantidad = "";
        $nuevo_item->id_articulo = 0;
        $nuevo_item->nombre = "Total de articulos";
        $nuevo_item->codigo = "";
        $nuevo_item->bonificacion = 0;
        $nuevo_item->porc_iva = 0;
        foreach($f->items as $item) {
          if ($item->anulado == 1) continue;
          if ($item->cantidad < 0 || $item->precio < 0) {
            $reemplazar_items = true;
          }
          $item->precio = (float)$item->precio;
          $item->cantidad = (float)$item->cantidad;
          $nuevo_item->precio += ($item->precio * $item->cantidad);
        }
        if ($reemplazar_items) {
          $f->items = array($nuevo_item);
        }
      }


      // Imprimimos los items
      foreach($f->items as $item) {

        if ($item->anulado == 1) continue;

        // Si el precio esta en negativo, es porque estamos pasando alguna OFERTA o COMBO
        // Lo que hacemos es sumarlo al descuento
        if ($item->precio < 0) {
          $f->descuento = $f->descuento + abs($item->precio);
          // No se imprime, ya que se pone un descuento general al final
          continue;

        } else if ($item->cantidad == 0) {
          // Si la cantidad es 0, entonces no lo imprimimos
          continue;
        
        // Si tiene articulos bonificados
        // se agrupan todos juntos al final del ticket
        } else if ($item->tipo_cantidad == "B") {
          $pos_bonif = -1;
          foreach($bonificados as $b) {
            $pos_bonif++;
            if ($b->id_articulo == $item->id_articulo) break;
          }
          if ($pos_bonif == -1) {
            // Agregamos al array
            $bonif = new stdClass();
            $bonif->nombre = $item->nombre;
            $bonif->cantidad = 1;
            if ($cliente["razon_social"] != "Consumidor Final") {
              $bonif->precio = $item->precio / ((100+$porc_iva) / 100);
            } else {
              $bonif->precio = $item->precio;
            }
            $bonificados[] = $bonif;            
          } else {
            // Sumamos al array
            $o = $bonificados[$pos_bonif];
            $o->cantidad++;
            $bonificados[$pos_bonif] = $o;
          }
        }

        $epson.="imprimir_item".$sep;
        $epson.="200".$sep;
        $epson.=mb_convert_encoding($item->nombre, 'ISO-8859-1', 'UTF-8').$sep;
        if ($cliente["razon_social"] != "Consumidor Final") {
          $precio = $item->precio / ((100+$porc_iva) / 100);
        } else {
          $precio = $item->precio;
        }
        
        $epson.=number_format($item->cantidad,4,".","").$sep;
        $epson.=number_format($precio,4,".","").$sep;
        $epson.=$id_alicuota_iva.$sep;
        $epson.="0".$sep;
        $epson.="0.0000".$sep;
        $epson.=$item->codigo.$sep.$fl;
      }

      // Como ultimo item ponemos el interes
      if ($f->interes > 0) {
        $epson.="imprimir_item".$sep;
        $epson.="200".$sep;
        $epson.="Recargo Pago Tarjeta".$sep;
        $epson.="1.000".$sep;
        $epson.=number_format($f->interes,4,".","").$sep;
        $epson.=$id_alicuota_iva.$sep;
        $epson.="0".$sep;
        $epson.="0.0000".$sep;
        $epson.="".$sep.$fl;
      }

      // Si tiene elementos bonificados
      if (sizeof($bonificados)>0) {
        foreach($bonificados as $bonif) {
          $epson.="imprimir_item".$sep;
          $epson.="200".$sep;
          $epson.="BONIF: ".mb_convert_encoding($bonif->nombre, 'ISO-8859-1', 'UTF-8').$sep;
          $epson.=number_format($bonif->cantidad,4,".","").$sep;
          $epson.=number_format(0,4,".","").$sep;
          $epson.=$id_alicuota_iva.$sep;
          $epson.="0".$sep;
          $epson.="0.0000".$sep;
          $epson.="".$sep.$fl;          
        }
      }

      if ($f->descuento > 0) {
        if ($cliente["tipo_iva"] == "I") {
          $descuento = ($f->descuento / 1.21);
        } else {
          $descuento = $f->descuento;
        }
        $epson.="cargar_descuento".$sep.number_format($descuento,2,".","").$sep.$id_alicuota_iva.$sep.$fl;
      }

      //if ($efectivo > 0) $epson.= "cargar_pago".$sep."8".$sep.number_format($efectivo,2,".","").$fl;
      //if ($tarjeta > 0) $epson.= "cargar_pago".$sep."20".$sep.number_format($tarjeta,2,".","").$fl;

      $epson.= "cerrar_comprobante".$fl;
      $epson.= "desconectar_impresora";
      file_put_contents(APPPATH.'libraries/epson/epson.txt', $epson);
      shell_exec('C:/xampp/htdocs/sistema/application/libraries/epson/Epson.exe');

    } else {

      // Comenzamos con el ticket
      $this->impresor->comenzar($comprobante,$cliente);

      // Imprimimos los items
      foreach($f->items as $item) {
      	$precio_unit = ($item->precio * ((100-$item->bonificacion) / 100));
        $this->impresor->imprimir_item(mb_convert_encoding($item->nombre, 'ISO-8859-1', 'UTF-8'),((float)$item->cantidad),((float)$precio_unit),((float)$item->porc_iva),0);
      }

      if ($f->interes > 0) {
        $this->impresor->imprimir_item("Recargo pago tarjeta",1,((float)$f->interes),21,0);
      }

      // Imprimimos las percepciones y las formas de pago
      if (!empty($perc_ib))   $this->impresor->percepcion_global("Percepcion IB",$perc_ib);
      if (!empty($percep_viajes))  $this->impresor->percepcion_global("Perc. Viaje Ext.",$percep_viajes);
      if (!empty($impuesto_pais))  $this->impresor->percepcion_global("Impuesto PAIS",$impuesto_pais);
      if (!empty($descuento))   $this->impresor->descuento_general($descuento);
      if (!empty($efectivo))  $this->impresor->imprimir_pago($efectivo,"EFECTIVO");
      if (!empty($cta_cte))   $this->impresor->imprimir_pago($cta_cte,"CUENTA CORRIENTE");
      if (!empty($tarjeta))   $this->impresor->imprimir_pago($tarjeta,"TARJETA");
      if (!empty($cheque))    $this->impresor->imprimir_pago($cheque,"CHEQUE");
      $this->impresor->cerrar($comprobante);


      // Obtener los ultimos numeros de comprobantes del controlador fiscal
      // y actualizar el numero de factura
      $numero = 0;
      $letra = "B";
      if ($f->id_tipo_comprobante == 1) {
        // Si es una factura A
        $numero = $this->impresor->ultima_factura_a();
        $letra = "A";
      } else {
        // Si es una factura B o C
        $numero = $this->impresor->ultima_factura_b();
      }
      $this->Factura_Model->modificar_numero_comprobante(array(
        "id"=>$f->id,
        "id_empresa"=>$f->id_empresa,
        "id_punto_venta"=>$f->id_punto_venta,
        "punto_venta"=>$f->punto_venta,
        "letra"=>$letra,
        "numero"=>$numero,
      ));
    }

    echo json_encode(array("result"=>1));
  }

  function cambiar_numero() {
    $id = $this->input->post("id");
    $id_punto_venta = $this->input->post("id_punto_venta");
    $id_empresa = $this->input->post("id_empresa");

    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get($id_punto_venta);
    if ($punto_venta->imp_fiscal != "Hasar") {
      echo json_encode(array("error"=>1)); return;
    }

    $this->load->model("Factura_Model");
    $f = $this->Factura_Model->get($id,$id_punto_venta,array(
      "id_empresa"=>$id_empresa
    ));
    if ($f === FALSE) {
      echo json_encode(array("error"=>1)); return;
    }

    // Obtener los ultimos numeros de comprobantes del controlador fiscal
    // y actualizar el numero de factura
    $numero = 0;
    $letra = "B";
    if ($f->id_tipo_comprobante == 1) {
      // Si es una factura A
      $numero = $this->impresor->ultima_factura_a();
      $letra = "A";
    } else {
      // Si es una factura B o C
      $numero = $this->impresor->ultima_factura_b();
    }
    $this->Factura_Model->modificar_numero_comprobante(array(
      "id"=>$f->id,
      "id_empresa"=>$f->id_empresa,
      "id_punto_venta"=>$f->id_punto_venta,
      "punto_venta"=>$f->punto_venta,
      "letra"=>$letra,
      "numero"=>$numero,
    ));
    echo json_encode(array("error"=>0)); return;
  }

  function get_comprobante($id_tipo_comprobante) {

    $i = $this->impresor;

    // FACTURA A
    if ($id_tipo_comprobante == 1) {
      return $i::TICKET_A;

    // FACTURA B
    } else if ($id_tipo_comprobante == 6) {
      return $i::TICKET_B;

    // NOTA DE CREDITO A
    } else if ($id_tipo_comprobante == 3) {
      return $i::NOTA_CREDITO_A;

    // NOTA DE CREDITO B
    } else if ($id_tipo_comprobante == 8) {
      return $i::NOTA_CREDITO_B;

    // NOTA DE DEBITO A
    } else if ($id_tipo_comprobante == 2) {
      return $i::NOTA_DEBITO_A;

    // NOTA DE DEBITO B
    } else if ($id_tipo_comprobante == 7) {
      return $i::NOTA_DEBITO_B;

    // FACTURA C
    } else if ($id_tipo_comprobante == 11) {
      return $i::TICKET_C;

    // REMITO
    } else if ($id_tipo_comprobante == 999) {
      return $i::REMITO;

    } else {
      // TODO: Deberia marcar algun error
      return 0;
    }
  }


  function cerrar() {

    $efectivo = (float) $this->input->post("efectivo");
    $descuento  = (float) $this->input->post("descuento");
    $tarjeta  = (float) $this->input->post("tarjeta");
    $cheque   = (float) $this->input->post("cheque");
    $cta_cte  = (float) $this->input->post("cta_cte");
    $perc_ib  = (float) $this->input->post("perc_ib");
    $perc_viajes  = (float) $this->input->post("percep_viajes");
    $impuesto_pais  = (float) $this->input->post("impuesto_pais");
    $id_tipo_comprobante  = $this->input->post("id_tipo_comprobante");
    $comprobante = $this->get_comprobante($id_tipo_comprobante);
    if (!empty($perc_ib))  $this->impresor->percepcion_global("Percepcion IB",$perc_ib);
    if (!empty($perc_viajes))  $this->impresor->percepcion_global("Perc. Viaje Ext.",$perc_viajes);
    if (!empty($impuesto_pais))  $this->impresor->percepcion_global("Impuesto PAIS",$impuesto_pais);
    if (!empty($descuento))   $this->impresor->descuento_general($descuento);
    if (!empty($efectivo)) $this->impresor->imprimir_pago($efectivo,"EFECTIVO");
    if (!empty($cta_cte))  $this->impresor->imprimir_pago($cta_cte,"CUENTA CORRIENTE");
    if (!empty($tarjeta))  $this->impresor->imprimir_pago($tarjeta,"TARJETA");
    if (!empty($cheque))   $this->impresor->imprimir_pago($cheque,"CHEQUE");

    $this->impresor->abrir_cajon();
    $this->impresor->cerrar($comprobante);
    echo json_encode(array("result"=>1));
  }

  function imprimir_item() {

    $descripcion = mb_convert_encoding($this->input->post("nombre"), 'ISO-8859-1', 'UTF-8');
    $cantidad = (float) $this->input->post("cantidad");
    $precio = (float) $this->input->post("precio");
    $porc_iva = (float) $this->input->post("porc_iva");
    $id_cliente = $this->input->post("id_cliente");
    $id_tipo_comprobante = $this->input->post("id_tipo_comprobante");

    // Comienzo del ticket
    $comienzo = $this->input->post("comienzo");

    if ($comienzo == 1) {

      // Si tiene cliente
      $cliente = array();
      if ($id_cliente != 0) {
        $this->load->model("Cliente_Model");
        $c = $this->Cliente_Model->get($id_cliente);
        if ($c->id_tipo_iva == 1) $tipo_iva = "Responsable Inscripto";
        else if ($c->id_tipo_iva == 4) $tipo_iva = "Consumidor Final";
        else if ($c->id_tipo_iva == 2) $tipo_iva = "Monotributo";
        else if ($c->id_tipo_iva == 3) $tipo_iva = "Exento";
        $cliente = array(
          "razon_social" => $c->nombre,
          "numero" => $c->cuit,
          "tipo_iva" => $tipo_iva,
          "domicilio" => $c->direccion,
          "tipo_doc" => $c->id_tipo_documento,
          );
      }

      // Abrimos el ticket segun corresponda el tipo de comprobante
      $comprobante = $this->get_comprobante($id_tipo_comprobante);

      $r = $this->impresor->comenzar($comprobante,$cliente);

      if ($r==0) {
        echo json_encode(array("result"=>0));
        return;
      }
    }

    // Imprimimos el item
    $this->impresor->imprimir_item($descripcion,$cantidad,$precio,$porc_iva,0);
    echo json_encode(array("result"=>1));
  }


  function descuento_item() {
    $descripcion = mb_convert_encoding($this->input->post("descripcion"), 'ISO-8859-1', 'UTF-8');
    $precio = abs((float) $this->input->post("precio"));
    $this->impresor->descuento_item($descripcion,$precio,1);
    echo json_encode(array("result"=>1));
  }


  function eliminar_item() {
    $descripcion = mb_convert_encoding($this->input->post("nombre"), 'ISO-8859-1', 'UTF-8');
    $cantidad = (float) $this->input->post("cantidad");
    $precio = ((float) ($this->input->post("precio")));
    $porc_iva = (float) $this->input->post("porc_iva");
    $this->impresor->imprimir_item($descripcion,$cantidad,$precio,$porc_iva,0);
    echo json_encode(array("result"=>1));
  }

  function imprimir_texto() {
    $this->impresor->imprimir_texto("Texto de Prueba");
  }

}