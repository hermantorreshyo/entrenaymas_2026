<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Factura_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("facturas","id");
  }

  // Devuelve el saldo consumido hasta el momento
  function get_saldo_actual($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;    

    $this->load->helper("fecha_helper");
    $this->load->model("Usuario_Model");
    $usuario = $this->Usuario_Model->get($id_usuario,array(
      "id_empresa"=>$id_empresa
    ));

    $date = new DateTime($fecha);
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_usuario = $id_usuario ";
    $sql.= "AND fecha = '".$date->format("Y-m-01")."' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return 0;
    $factura = $q->row();
    return floatval($factura->total);
  }

  // Funcion utilizada para registrar cada consulta en la cuenta del usuario
  // Por cada whatsapp, consulta de formulario o pedido de turno, se llama a esta funcion
  // para sumarlo en la factura abierta en el mes
  // El tope máximo por mes se usa como descuento
  function registrar_consulta($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_localidad = isset($config["id_localidad"]) ? $config["id_localidad"] : 0;
    $origen = isset($config["origen"]) ? $config["origen"] : "";
    $id_punto_venta = 2457;
    $punto_venta = 1;

    $this->load->helper("fecha_helper");
    $this->load->model("Usuario_Model");
    $usuario = $this->Usuario_Model->get($id_usuario,array(
      "id_empresa"=>$id_empresa
    ));

    // Buscamos el costo que debemos cobrarle por la consulta que estamos registrando
    $valor = 0;
    if ($id_localidad != 0) {
      $sql = "SELECT * FROM com_usuarios_localidades ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_usuario = $id_usuario ";
      $sql.= "AND id_localidad = $id_localidad ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $r = $q->row();
        $valor = $r->valor;
      }
    } else {
      // En caso de que no estemos filtrando por localidad, cobramos el maximo
      $sql = "SELECT * FROM com_usuarios_localidades ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_usuario = $id_usuario ";
      $sql.= "ORDER BY valor DESC ";
      $sql.= "LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $r = $q->row();
        $valor = $r->valor;
      }
    }

    $date = new DateTime($fecha);
    // Buscamos si existe una factura abierta para el mes en curso
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_usuario = $id_usuario ";
    $sql.= "AND fecha = '".$date->format("Y-m-01")."' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      // Debemos insertar la nueva factura
      $factura = new stdClass();
      $factura->id_empresa = $id_empresa;
      $factura->fecha = $date->format("Y-m-01");
      $factura->id_punto_venta = $id_punto_venta;
      $factura->punto_venta = $punto_venta;
      $factura->id_tipo_comprobante = 999;
      $factura->estado = 0;
      $factura->cliente = $usuario->nombre;
      $factura->id_usuario = $id_usuario;
      $proximos = $this->next(array(
        "id_empresa"=>$id_empresa,
        "id_tipo_comprobante"=>$factura->id_tipo_comprobante
      ));
      $factura->numero = "1";//$proximos[$factura->id_tipo_comprobante];
      $factura->comprobante = "R ".str_pad("1",4,"0",STR_PAD_LEFT)."-".str_pad($factura->numero,8,"0",STR_PAD_LEFT);
      $this->db->insert("facturas",$factura);
      $factura->id = $this->db->insert_id();

      $this->db->query("UPDATE numeros_comprobantes SET ultimo = $factura->numero WHERE id_empresa = $factura->id_empresa AND id_tipo_comprobante = $factura->id_tipo_comprobante AND id_punto_venta = $factura->id_punto_venta");

    } else {
      $factura = $q->row();
    }

    $this->db->insert("facturas_items",array(
      "id_empresa"=>$id_empresa,
      "id_punto_venta"=>$id_punto_venta,
      "id_factura"=>$factura->id,
      "id_articulo"=>0,
      "tipo"=>0,
      "tipo_cantidad"=>"",
      "cantidad"=>"1",
      "precio"=>$valor,
      "neto"=>$valor,
      "porc_iva"=>0,
      "id_tipo_alicuota_iva"=>0,
      "nombre"=>"Consulta ".(!empty($origen) ? "desde $origen" : "")." del día ".fecha_es($fecha),
      "total_con_iva"=>$valor,
      "total_sin_iva"=>$valor,
      "stamp"=>time(),
    ));

    $this->recalcular_totales(array(
      "id_factura"=>$factura->id,
      "id_punto_venta"=>$id_punto_venta,
      "id_empresa"=>$id_empresa,
      "monto_maximo"=>$usuario->maximo,
      "id_usuario"=>$id_usuario,
    ));
  }

  // Wrapper de la funcion recalcular_totales, nada mas que ya le manda cual es la ultima factura
  function recalcular_ultima_factura($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $date = new DateTime();
    // Buscamos si existe una factura abierta para el mes en curso
    $sql = "SELECT * FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_usuario = $id_usuario ";
    $sql.= "AND fecha = '".$date->format("Y-m-01")."' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return;
    $factura = $q->row();
    $config["id_factura"] = $factura->id;
    $config["id_punto_venta"] = $factura->id_punto_venta;
    $this->recalcular_totales($config);
  }

  // Recalcula los totales de la factura dada
  function recalcular_totales($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $monto_maximo = isset($config["monto_maximo"]) ? $config["monto_maximo"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $sql = "SELECT IF(SUM(total_con_iva) IS NULL,0,SUM(total_con_iva)) AS total FROM facturas_items ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $sql.= "AND id_factura = $id_factura ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $total = $r->total;
    $gasto_real = $total;

    $descuento = 0;
    if ($monto_maximo > 0) {
      // Si esta definido un monto maximo, controlamos si el total supero ese monto
      // en caso de haberlo superado, tenemos que ponerlo como descuento a la factura
      if ($total > $monto_maximo) {
        $descuento = $total - $monto_maximo;
        $total = $monto_maximo;
      }
    }

    $sql = "UPDATE facturas SET total = $total, subtotal = $total, descuento = $descuento ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $sql.= "AND id = $id_factura ";
    $this->db->query($sql);

    if ($id_usuario != 0) {
      $sql = "UPDATE com_usuarios_extension SET saldo = $total, gasto_real = $gasto_real ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_usuario = $id_usuario ";
      $this->db->query($sql);      
    }
  }












  function modificar_numero_comprobante($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id = isset($config["id"]) ? $config["id"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $letra = isset($config["letra"]) ? $config["letra"] : "";
    $punto_venta = isset($config["punto_venta"]) ? $config["punto_venta"] : 0;
    $numero = isset($config["numero"]) ? $config["numero"] : 0;
    $comprobante = $letra." ".str_pad($punto_venta, 4, "0", STR_PAD_LEFT)."-".str_pad($numero, 8, "0", STR_PAD_LEFT);
    $sql = "UPDATE facturas SET ";
    $sql.= " comprobante = '$comprobante', numero = '$numero', punto_venta = '$punto_venta' ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id = $id ";
    if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);
  }

  function generar_electronica($config = array()) {

    /*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    */

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $cambiar_fecha = isset($config["cambiar_fecha"]) ? $config["cambiar_fecha"] : 0;

    $this->load->model("Log_Model");
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    // Excepciones de CUITS
    if ($id_punto_venta == 1705 && $id_empresa == 228) {
      // BERISSO ARGENCASH USA CUIT DE FERNANDA
      $empresa->cuit = "27249992386";
    } else if ($id_punto_venta == 2003 && $id_empresa == 228) {
      // ENSENADA 2 USA OTRO PUNTO DE VENTA
      $empresa->cuit = "30716565641";
    }

    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);    

    $factura = $this->get($id_factura,$id_punto_venta,array(
      "id_empresa"=>$id_empresa,
    ));
    if (empty($factura)) {
      return (array("error"=>1,"mensaje"=>"No existe el remito indicado."));
    }
    if (!empty($factura->cae)) {
      return (array("error"=>1,"mensaje"=>"El comprobante ya fue facturado.")); 
    }

    if ($cambiar_fecha == 1) $factura->fecha = date("d/m/Y");

    // Controlamos si el punto de venta es FACTURA ELECTRONICA
    if ($id_empresa != 228) {
      $this->load->model("Punto_Venta_Model");
      $punto_venta = $this->Punto_Venta_Model->get($id_punto_venta,array(
        "id_empresa"=>$id_empresa
      ));
      if (empty($punto_venta)) {
        return array(
          "error"=>1,
          "mensaje"=>"Error al obtener el punto de venta $id_punto_venta.",
        );                
      }
      if ($punto_venta->tipo_impresion != "E") {
        // Tomamos el punto de venta por defecto
        // (tiene que ser FACTURA ELECTRONICA)
        $pv = $this->Punto_Venta_Model->get_por_defecto(array(
          "id_empresa"=>$id_empresa
        ));
        if ($pv->tipo_impresion == "E") {
          $factura->punto_venta = $pv->numero;
        } else {
          return array(
            "error"=>1,
            "mensaje"=>"El PV por defecto no es electronico.",
          );                  
        }
      } else {
        $factura->punto_venta = $punto_venta->numero;
      }
    }

    // IMPORTANTE:
    // Si la empresa tiene canasta_basica = 1
    // Si el cliente es CF
    // Y alguno de los articulos tiene custom_5 = 1
    // Tenemos que recalcular facturas_iva, facturas.neto, facturas.iva, facturas.total
    // Antes de pasarlo a FE.


    // RECALCULAMOS LA TABLA "facturas_iva"
    $actualizar_neto = 0;
    $neto_factura = 0;
    $iva_factura = 0;
    if ($id_empresa != 228) {
      $ivas = array();
      $this->load->model("Articulo_Model");
      foreach($factura->items as $l) {

        // En caso de que el item sea un articulo
        if (isset($l->id_articulo) && !empty($l->id_articulo)) {
          $articulo = $this->Articulo_Model->get_nombre($l->id_articulo,array(
            "id_empresa"=>$factura->id_empresa
          ));
          if (!isset($l->id_tipo_alicuota_iva) || $l->id_tipo_alicuota_iva == 0 || $l->total_sin_iva == 0) {

            // En caso de que la alicuota este en cero
            // Este caso se puede dar si factura una compra de la web

            // Ponemos el valor que tiene cargado en la tabla articulos
            $l->id_tipo_alicuota_iva = $articulo->id_tipo_alicuota_iva;
            $l->porc_iva = $articulo->porc_iva;

            // Recalculamos
            $l->neto = $l->precio / ((100+($l->porc_iva))/100);
            $l->total_sin_iva = $l->total_con_iva / ((100+($l->porc_iva))/100);
            $l->iva = $l->total_sin_iva * ($l->porc_iva / 100);

            // Actualizamos la base de datos
            $sql = "UPDATE facturas_items SET ";
            $sql.= " id_tipo_alicuota_iva = '$l->id_tipo_alicuota_iva', ";
            $sql.= " porc_iva = '$l->porc_iva', ";
            $sql.= " neto = '$l->neto', ";
            $sql.= " iva = '$l->iva', ";
            $sql.= " total_sin_iva = '$l->total_sin_iva' ";
            $sql.= "WHERE id = $l->id ";
            $sql.= "AND id_empresa = $l->id_empresa ";
            $sql.= "AND id_factura = $l->id_factura ";
            $sql.= "AND id_punto_venta = $l->id_punto_venta ";
            $this->db->query($sql);

            // Al final de todo actualizamos la tabla facturas
            $actualizar_neto = 1;
          }
        }
        // Sumamos las alicuotas de IVA
        if (!isset($ivas[$l->id_tipo_alicuota_iva])) {
          $ivas[$l->id_tipo_alicuota_iva] = array("neto"=>0,"iva"=>0);
        }
        $ivas[$l->id_tipo_alicuota_iva]["neto"] += ($l->total_sin_iva * ((100-$factura->porc_descuento) / 100));
        $ivas[$l->id_tipo_alicuota_iva]["iva"] += ($l->iva * ((100-$factura->porc_descuento) / 100));
        $neto_factura += $ivas[$l->id_tipo_alicuota_iva]["neto"];
        $iva_factura += $ivas[$l->id_tipo_alicuota_iva]["iva"];
      }

      $this->db->query("DELETE FROM facturas_iva WHERE id_empresa = $factura->id_empresa AND id_factura = $factura->id AND id_punto_venta = $factura->id_punto_venta ");
      foreach($ivas as $id_alicuota_iva => $iva) {

        // ARREGLO POR EL TEMA DE LOS AJUSTES
        $s = $this->calcular_iva_segun_alicuota($id_alicuota_iva,$iva["neto"]);

        $this->db->insert("facturas_iva",array(
          "id_empresa"=>$factura->id_empresa,
          "id_factura"=>$factura->id,
          "id_alicuota_iva"=>$id_alicuota_iva,
          "id_punto_venta"=>$factura->id_punto_venta,
          "neto"=>round($iva["neto"],2),
          "iva"=>$s,
        ));
      }
    }

    // Volvemos a tomar los ivas
    $sql = "SELECT * ";
    $sql.= "FROM facturas_iva FI ";
    $sql.= "WHERE FI.id_factura = $factura->id ";
    $sql.= "AND FI.id_empresa = $factura->id_empresa ";
    $sql.= "AND FI.id_punto_venta = $factura->id_punto_venta ";
    $q_ivas = $this->db->query($sql);
    $factura->ivas = $q_ivas->result();

    // La empresa es RI
    $letra = "R";
    if ($empresa->id_tipo_contribuyente == 1) {
      // El cliente es RI
      if ($factura->cliente->id_tipo_iva == 1) {
        $factura->id_tipo_comprobante = 1; // FA
        $letra = "A";
      } else {
        $factura->id_tipo_comprobante = 6; // FB
        $letra = "B";
      }
    // La empresa es MONOTRIBUTO
    } else if ($empresa->id_tipo_contribuyente == 2) {
      $factura->id_tipo_comprobante = 11; // FC
      $letra = "C";
    }

    // Controlamos si es un cliente CF y no tiene DNI
    if ($factura->cliente->id_tipo_iva == 4 && empty($factura->cliente->cuit) && $factura->total >= 10000) {
      $mensaje = "ERROR: Para importes mayores a $10000 el cliente debe tener DNI/CUIT.";
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "file"=>date("Ymd")."_fe.txt",
        "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$mensaje,
      ));
      return (array(
        "error"=>1,
        "mensaje"=>$mensaje,
      ));
    }

    // Vamos a la AFIP a obtener el ultimo numero de comprobante autorizado
    $respuesta = $fe->get_ultimo_autorizado($factura->punto_venta,$factura->id_tipo_comprobante);
    if (!isset($respuesta->error)) {
      $ultimo_numero = $respuesta->CbteNro;
    } else {
      $mensaje = "Error al obtener el ultimo numero de comprobante";
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "file"=>date("Ymd")."_fe.txt",
        "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$mensaje,
      ));      
      return (array(
        "error"=>1,
        "mensaje"=>$mensaje,
      ));
    }

    // Argencash mandamos todo como Consumidor Final
    if ($empresa->id == 228) {
      $factura->id_cliente = 0;
    } else if ($empresa->id == 718) {
      $factura->punto_venta = 4;
    }

    $factura->numero = $ultimo_numero + 1;
    $factura->comprobante = $letra." ".str_pad($factura->punto_venta, 4, "0", STR_PAD_LEFT)."-".str_pad($factura->numero, 8, "0", STR_PAD_LEFT);

    $this->Log_Model->imprimir(array(
      "id_empresa"=>$id_empresa,
      "file"=>date("Ymd")."_fe.txt",
      "texto"=>"GENERAR FACTURA ELECTRONICA DE: \n".print_r($factura,TRUE)."\n",
    ));

    $res = $fe->solicitar($factura);

    // SI ES UN ERROR NUESTRO
    if (is_array($res) && isset($res["error"]) && $res["error"] == 1) {
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "file"=>date("Ymd")."_fe.txt",
        "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$res["mensaje"],
      ));      
      return $res;
    }

    if ($res !== FALSE) {
      // Contiene errores
      if (isset($res->Errors)) {
        $error = mb_convert_encoding($res->Errors->Err->Msg, 'ISO-8859-1', 'UTF-8');
        $this->Log_Model->imprimir(array(
          "id_empresa"=>$id_empresa,
          "file"=>date("Ymd")."_fe.txt",
          "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$error,
        ));
        return (array(
          "error"=>1,
          "mensaje"=>$error,
        ));
      } else {
        // La operacion ha sido rechazada, debemos informar
        if ($res->FeCabResp->Resultado == "R") {
          
          // Concatenamos todos los mensajes de error en uno solo
          $mensaje = "";
          if (isset($res->FeDetResp->FECAEDetResponse->Observaciones)) {
            foreach($res->FeDetResp->FECAEDetResponse->Observaciones as $obs) {
              $mensaje.= $obs->Msg."\n";
            }
          }
          if (empty($mensaje)) $mensaje = "Comprobante rechazado.";
          $mensaje = mb_convert_encoding($mensaje, 'ISO-8859-1', 'UTF-8');
          $this->Log_Model->imprimir(array(
            "id_empresa"=>$id_empresa,
            "file"=>date("Ymd")."_fe.txt",
            "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$mensaje,
          ));
          return (array(
            "error"=>1,
            "mensaje"=>$mensaje
          ));
          
        // La operacin ha sido exitosa
        } else {
          // Guardamos el CAE y la fecha de vencimiento
          if ($res->FeDetResp->FECAEDetResponse->Resultado == "A") {

            if (!isset($res->FeDetResp->FECAEDetResponse->CAE) || !isset($res->FeDetResp->FECAEDetResponse->CAEFchVto)) {
              $mensaje = "AFIP NO DEVOLVIO CAE.";
              $this->Log_Model->imprimir(array(
                "id_empresa"=>$id_empresa,
                "file"=>date("Ymd")."_fe.txt",
                "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$mensaje,
              ));              
              return (array("error"=>1,"mensaje"=>$mensaje));
            }

            $factura->cae = trim($res->FeDetResp->FECAEDetResponse->CAE);
            $fecha_vto = $res->FeDetResp->FECAEDetResponse->CAEFchVto;

            if (empty($factura->cae)) {
              $mensaje = "ERROR EN LOS SERVIDORES DE AFIP. CAE VACIO.";
              $this->Log_Model->imprimir(array(
                "id_empresa"=>$id_empresa,
                "file"=>date("Ymd")."_fe.txt",
                "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$mensaje,
              ));              
              return (array("error"=>1,"mensaje"=>$mensaje));
            }
            $anio = substr($fecha_vto,0,4);
            $mes = substr($fecha_vto,4,2);
            $dia = substr($fecha_vto,6,2);
            $factura->fecha_vto = "$anio-$mes-$dia";
            $sql = "UPDATE facturas SET ";
            if ($actualizar_neto == 1) {
              $sql.= " neto = '$neto_factura', ";
              $sql.= " iva = '$iva_factura', ";
            }
            if ($cambiar_fecha == 1) $sql.= " fecha = '".date("Y-m-d")."', ";
            $sql.= " numero = '$factura->numero', ";
            $sql.= " comprobante = '$factura->comprobante', ";
            $sql.= " cae = '$factura->cae', ";
            $sql.= " fecha_vto = '$factura->fecha_vto', ";
            $sql.= " id_tipo_comprobante = '$factura->id_tipo_comprobante', ";
            $sql.= " pendiente = 0 "; // La sacamos del pendiente
            $sql.= "WHERE id = $factura->id ";
            $sql.= "AND id_empresa = $id_empresa ";
            $sql.= "AND id_punto_venta = $id_punto_venta ";
            $this->db->query($sql);
            
            // Actualizamos el ultimo numero del comprobante
            $this->db->query("UPDATE numeros_comprobantes SET ultimo = $factura->numero WHERE id_empresa = $factura->id_empresa AND id_tipo_comprobante = $factura->id_tipo_comprobante AND id_punto_venta = $factura->id_punto_venta");

            $this->Log_Model->imprimir(array(
              "id_empresa"=>$id_empresa,
              "file"=>date("Ymd")."_fe.txt",
              "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: COMPROBANTE APROBADO $factura->comprobante",
            ));
            return (array(
              "error"=>0,
              "comprobante"=>$factura->comprobante,
              "id_tipo_comprobante"=>$factura->id_tipo_comprobante,
              "numero"=>$factura->numero,
              "cae"=>$factura->cae,
              "fecha_vto"=>$factura->fecha_vto,
              "mensaje"=>"OK",
            ));
          } else {
            $mensaje = "ERROR: No se aprobo el comprobante.";
            $this->Log_Model->imprimir(array(
              "id_empresa"=>$id_empresa,
              "file"=>date("Ymd")."_fe.txt",
              "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$mensaje,
            ));
            return (array("error"=>1,"mensaje"=>$mensaje));
          }
        }
      }
    } else {
      $mensaje = "Error al conectarse con los servidores de la AFIP";
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "file"=>date("Ymd")."_fe.txt",
        "texto"=>"ID: [".$factura->id."] ID_PUNTO_VENTA: [".$factura->id_punto_venta."] MENSAJE: ".$mensaje,
      ));
      return (array(
        "error"=>1,
        "mensaje"=>$mensaje,
      ));
    }
  }

  function next($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $sql = "SELECT id_tipo_comprobante, ultimo ";
    $sql.= "FROM numeros_comprobantes ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $r) {
      $salida[$r->id_tipo_comprobante] = $r->ultimo + 1;
    }
    return $salida;
  }

  function next_by_tipo_comprobante($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_tipo_comprobante = isset($config["id_tipo_comprobante"]) ? $config["id_tipo_comprobante"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;    
    $salida = $this->next(array(
      "id_empresa"=>$id_empresa,
      "id_punto_venta"=>$id_punto_venta,
    ));
    $s = 1;
    foreach ($salida as $key => $value) {
      if ($key == $id_tipo_comprobante) return $value;
    }
    return $s;
  }

  // PERMITE CREAR UNA FACTURA A PARTIR DE LOS DATOS PASADOS
  function crear($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $es_periodica = isset($config["es_periodica"]) ? $config["es_periodica"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $hora = isset($config["hora"]) ? $config["hora"] : date("H:i:s");
    $anulada = isset($config["anulada"]) ? $config["anulada"] : 0;
    $id_tipo_comprobante = isset($config["id_tipo_comprobante"]) ? $config["id_tipo_comprobante"] : 999;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_vendedor = isset($config["id_vendedor"]) ? $config["id_vendedor"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_tipo_estado = isset($config["id_tipo_estado"]) ? $config["id_tipo_estado"] : 0;
    $id_referencia = isset($config["id_referencia"]) ? $config["id_referencia"] : 0;
    $numero_referencia = isset($config["numero_referencia"]) ? $config["numero_referencia"] : 0;
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : "";
    $tipo_pago = isset($config["tipo_pago"]) ? $config["tipo_pago"] : "C";
    $usuario = isset($config["usuario"]) ? $config["usuario"] : "";
    $estado = isset($config["estado"]) ? $config["estado"] : 0;

    // Array con formato [{id_articulo=>0,cantidad=>1}]
    $items = isset($config["items"]) ? $config["items"] : array();

    // Si no enviamos el parametro id_punto_venta, buscamos el que tiene la empresa por defecto
    $this->load->model("Punto_Venta_Model");
    if ($id_punto_venta == 0) {
      $punto_venta = $this->Punto_Venta_Model->get_por_defecto(array(
        "id_empresa"=>$id_empresa
      ));
      if ($punto_venta === FALSE) {
        return array(
          "error"=>1,
          "mensaje"=>"No se encuentra el punto de venta con ID: [$id_punto_venta]",
        );
      }
      $id_punto_venta = $punto_venta->id;
    }
    $punto_venta = $this->Punto_Venta_Model->get($id_punto_venta,array(
      "id_empresa"=>$id_empresa,
    ));

    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($id_tipo_comprobante);

    // Calculamos el proximo
    $proximo = $this->next_by_tipo_comprobante(array(
      "id_empresa"=>$id_empresa,
      "id_tipo_comprobante"=>$id_tipo_comprobante,
      "id_punto_venta"=>$id_punto_venta,
    ));   

    $comprobante = $tipo_comprobante->letra." ".str_pad($punto_venta->numero, 4, "0", STR_PAD_LEFT)."-".str_pad($proximo, 8, "0", STR_PAD_LEFT); 

    $factura = new stdClass();
    $factura->id = 0;
    $factura->id_empresa = $id_empresa;
    $factura->id_punto_venta = $id_punto_venta;
    $factura->id_tipo_comprobante = $id_tipo_comprobante;
    $factura->tipo_comprobante = $tipo_comprobante->nombre;
    $factura->id_tipo_estado = $id_tipo_estado;
    $factura->punto_venta = $punto_venta->numero;
    $factura->numero = $proximo;
    $factura->anulada = $anulada;
    $factura->total = 0;
    $factura->subtotal = 0;
    $factura->neto = 0;
    $factura->iva = 0;
    $factura->costo_final = 0;
    $factura->fecha = $fecha;
    $factura->hora = $hora;
    $factura->hash = md5($id_empresa.$comprobante);
    $factura->comprobante = $comprobante;
    $factura->visto = 0;
    $factura->enviada = 0;
    $factura->impresa = 0;
    $factura->nueva = 1;
    $factura->id_referencia = $id_referencia;
    $factura->numero_referencia = $numero_referencia;
    $factura->costo_envio = 0;
    $factura->numero_remito = 0;
    $factura->pendiente = 0;
    $factura->fecha_vto = "0000-00-00";
    $factura->estado = $estado;
    $factura->cae = "";
    $factura->efectivo = 0;
    $factura->cta_cte = 0;
    $factura->tarjeta = 0;
    $factura->cheque = 0;
    $factura->vuelto = 0;
    $factura->tipo_pago = $tipo_pago;
    $factura->pagada = 0;
    $factura->pago = 0;    
    $factura->id_sucursal = $punto_venta->id_sucursal;
    $factura->sucursal = $punto_venta->sucursal;
    $factura->tipo_punto_venta = $punto_venta->tipo_impresion;
    $factura->id_usuario = $id_usuario;
    if (!empty($usuario)) $factura->usuario = $usuario;
    $factura->observaciones = $observaciones;
    $factura->id_vendedor = $id_vendedor;

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_empresa_min($id_empresa);
    $factura->empresa = $empresa->nombre;

    // Buscamos los datos del cliente
    $factura->id_cliente = $id_cliente;
    if ($id_cliente != 0) {
      $this->load->model("Cliente_Model");
      $cliente = $this->Cliente_Model->get_by_id($id_cliente,array(
        "id_empresa"=>$id_empresa
      ));
      $factura->cliente = (!empty($cliente) && isset($cliente->nombre) ? $cliente->nombre : "");
    } else {
      $factura->cliente = "Consumidor Final";
    }

    // Buscamos los datos del vendedor
    if ($id_vendedor != 0) {
      if ($this->Empresa_Model->es_toque($id_empresa)) {
        $this->load->model("Repartidor_Model");
        $vendedor = $this->Repartidor_Model->get($id_vendedor,array(
          "id_empresa"=>$id_empresa
        ));
      } else {
        $this->load->model("Vendedor_Model");
        $vendedor = $this->Vendedor_Model->get($id_vendedor,array(
          "id_empresa"=>$id_empresa
        ));        
      }
      $factura->vendedor = (!empty($vendedor) && isset($vendedor->nombre) ? $vendedor->nombre : "");
    } else {
      $factura->vendedor = "";
    }

    // Buscamos los datos del articulo
    $facturas_items = array();
    if (sizeof($items)>0) {
      $this->load->model("Articulo_Model");
      foreach($items as $it) {
        $id_articulo = isset($it["id_articulo"]) ? $it["id_articulo"] : 0;
        // Insertamos el item a la factura
        $articulo = $this->Articulo_Model->get_by_id($id_articulo,array(
          "id_empresa"=>$id_empresa
        ));
        if (!empty($articulo)) {
          $item = new stdClass();
          $item->id_articulo = $id_articulo;
          $item->id_empresa = $id_empresa;
          $item->id_factura = 0;
          $item->id_punto_venta = $id_punto_venta;
          $item->cantidad = isset($it["cantidad"]) ? $it["cantidad"] : 1;
          $item->id_articulo = $id_articulo;
          $item->nombre = $articulo->nombre;
          $item->id_tipo_comprobante = $id_tipo_comprobante;
          $item->id_tipo_alicuota_iva = $articulo->id_tipo_alicuota_iva;
          $item->id_cliente = $id_cliente;
          $item->id_vendedor = $id_vendedor;
          $item->porc_iva = $articulo->porc_iva;
          $item->neto = $articulo->precio_final_dto / ((100+$articulo->porc_iva)/100);
          $item->precio = $articulo->precio_final_dto;
          $item->costo_final = $articulo->costo_final * $item->cantidad;
          $item->iva = $item->neto * ($articulo->porc_iva / 100) * $item->cantidad;
          $item->total_sin_iva = $item->neto * $item->cantidad;
          $item->total_con_iva = $item->precio * $item->cantidad;
          $facturas_items[] = $item;

          // Sumamos los valores a la factura
          $factura->costo_final += $item->costo_final;
          $factura->neto += $item->total_sin_iva;
          $factura->subtotal += $item->total_con_iva;
          $factura->total += $item->total_con_iva;
          $factura->iva += $item->iva;
        }
      }
    }

    if ($tipo_pago == "E") $factura->efectivo = $factura->total;
    else if ($tipo_pago == "C") $factura->cta_cte = $factura->total;

    // Guardamos la factura
    $id_factura = $this->insert($factura);
    // Recorremos los items y los guardamos
    foreach($facturas_items as $item) {
      $item->id_factura = $id_factura;
      $this->db->insert("facturas_items",$item);
    }

    // Actualizamos los numeros de comprobante
    $sql = "UPDATE numeros_comprobantes SET ultimo = $proximo ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta AND id_tipo_comprobante = $id_tipo_comprobante ";
    $this->db->query($sql);

    // Si se envio el parametro "es_periodica"
    if ($es_periodica == 1) {
      // Creamos una factura periodica para ese comprobante
      $this->guardar_factura_periodica(array(
        "id_empresa"=>$id_empresa,
        "id_punto_venta"=>$id_punto_venta,
        "id_factura"=>$id_factura,
        "es_periodica"=>1,
        "periodo_dia"=>0, // El mismo dia del mes que viene
        "fecha"=>$fecha,
        "dias_vencimiento"=>10,
      ));
    }
    return array(
      "error"=>0,
      "id_factura"=>$id_factura,
      "id_punto_venta"=>$id_punto_venta,
      "id_empresa"=>$id_empresa,
      "total"=>$factura->total,
      "comprobante"=>$comprobante,
    );
  }

  function guardar_factura_periodica($config = array()) {

    if (!$this->db->table_exists('facturas_periodicas')) return;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $es_periodica = isset($config["es_periodica"]) ? $config["es_periodica"] : 0;
    $periodo_cantidad = isset($config["periodo_cantidad"]) ? $config["periodo_cantidad"] : 1;
    $periodo_tipo = isset($config["periodo_tipo"]) ? $config["periodo_tipo"] : "M";
    $periodo_dia = isset($config["periodo_dia"]) ? $config["periodo_dia"] : 1;
    $dias_vencimiento = isset($config["dias_vencimiento"]) ? $config["dias_vencimiento"] : 1;

    if ($id_empresa == 0 || $id_punto_venta == 0 || $id_factura == 0) return;

    // Primero eliminamos si existe
    $sql = "DELETE FROM facturas_periodicas WHERE id_factura = $id_factura AND id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta ";
    $this->db->query($sql);

    // Y la volvemos a crear de ser necesario
    if ($es_periodica == 1) {
      $proxima_emision = new DateTime($fecha);
      $proxima_emision->add(new DateInterval("P".$periodo_cantidad.$periodo_tipo));
      if ($periodo_dia == 0) {
        // Es el mismo dia del mes
        $proxima_emision_f = $proxima_emision->format("Y-m-d");
      } else {
        // Es un dia especifico del mes
        $dia_mes = str_pad($periodo_dia, 2, "0", STR_PAD_LEFT);
        $proxima_emision_f = $proxima_emision->format("Y-m-".$dia_mes);
      }
      $proximo_vencimiento = new DateTime($proxima_emision_f);
      $proximo_vencimiento->add(new DateInterval("P".$dias_vencimiento."D"));
      $proximo_vencimiento_f = $proximo_vencimiento->format("Y-m-d");
      $sql = "INSERT INTO facturas_periodicas (id_factura, id_punto_venta, id_empresa, proxima_emision, proximo_vencimiento, periodo_cantidad, periodo_tipo, periodo_dia, dias_vencimiento) VALUES (";
      $sql.= "'$id_factura','$id_punto_venta','$id_empresa','$proxima_emision_f','$proximo_vencimiento_f','$periodo_cantidad','$periodo_tipo','$periodo_dia','$dias_vencimiento') ";
      $this->db->query($sql);
    }    
  }

  function cambiar_estado($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id = isset($config["id"]) ? $config["id"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : -1;
    $estado_anterior = isset($config["estado_anterior"]) ? $config["estado_anterior"] : -1;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $set_id_vendedor = isset($config["set_id_vendedor"]) ? $config["set_id_vendedor"] : 0;
    $set_vendedor = isset($config["set_vendedor"]) ? $config["set_vendedor"] : "";
    $set_custom_6 = isset($config["set_custom_6"]) ? $config["set_custom_6"] : "";
    $set_codigo_postal = isset($config["set_codigo_postal"]) ? $config["set_codigo_postal"] : "";

    if ($estado == -1 || $id == 0) return;
    $sql = "UPDATE facturas F SET ";
    if (!empty($set_vendedor)) $sql.= " F.vendedor = '$set_vendedor', ";
    if (!empty($set_id_vendedor)) $sql.= " F.id_vendedor = '$set_id_vendedor', ";
    if (!empty($set_custom_6)) $sql.= " F.custom_6 = '$set_custom_6', ";
    if (!empty($set_codigo_postal)) $sql.= " F.codigo_postal = '$set_codigo_postal', ";
    $sql.= " id_tipo_estado = '$estado' ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id = $id ";
    if ($estado_anterior != -1) $sql.= "AND F.id_tipo_estado = '$estado_anterior' ";
    if (!empty($id_punto_venta)) $sql.= "AND F.id_punto_venta = '$id_punto_venta' ";
    $this->db->query($sql);
  }

  function pasar_a_abandonados($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "UPDATE facturas F SET id_tipo_estado = 7 "; // Estado Abandonado
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_tipo_estado = 0 "; // Estado Pendiente
    $sql.= "AND CONCAT(F.fecha,' ',F.hora) < '".date("Y-m-d H:i:s",strtotime("-1 day"))."' ";
    $this->db->query($sql);
  }

  function calcular_iva_segun_alicuota($id_tipo_alicuota_iva,$neto) {
    if ($id_tipo_alicuota_iva == 5) $s = $neto * 0.21;
    else if ($id_tipo_alicuota_iva == 4) $s = $neto * 0.105;
    else if ($id_tipo_alicuota_iva == 6) $s = $neto * 0.27;
    else if ($id_tipo_alicuota_iva == 8) $s = $neto * 0.05;
    else if ($id_tipo_alicuota_iva == 9) $s = $neto * 0.025;
    else $s = 0;
    return $s;
  }

  function update($id,$data) {
    $data = $this->limpiar_campos($data,$this->tabla);
    $this->db->where("id",$id);
    if (isset($data->id_empresa)) $this->db->where("id_empresa",$data->id_empresa);
    if (isset($data->id_punto_venta)) $this->db->where("id_punto_venta",$data->id_punto_venta);
    $this->db->update("facturas",$data);
    $aff = $this->db->affected_rows();
    $this->db->close();
    return $aff;
  }

  // Convierte un remito a una factura
  function convertir($id,$id_punto_venta=0) {

    $f = $this->get($id,$id_punto_venta);
    if ($f === FALSE || empty($f) || $f->id_tipo_comprobante != 999) {
      return array(
        "error"=>1,
        "mensaje"=>"El comprobante no es un remito.",
      );
    }

    $this->load->model("Punto_Venta_Model");
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($f->id_empresa);

    // Guardamos el numero de remito que correspondia
    $numero_remito = $f->numero;

    // Si el cliente no esta definido, asumimos consumidor final
    $f->cliente->id_tipo_iva = ($f->cliente->id_tipo_iva == 0) ? 4 : $f->cliente->id_tipo_iva;
    
    // La empresa es RI
    if ($empresa->id_tipo_contribuyente == 1) {

      // El cliente es RI, hace Factura A
      if ($f->cliente->id_tipo_iva == 1) {
        $f->id_tipo_comprobante = 1;
        $letra = "A";
      
      } else {
        // El cliente es MO,CF,EX, hace Factura B
        $f->id_tipo_comprobante = 6;
        $letra = "B";
      }

    // La empresa es Monotributo
    } else if ($empresa->id_tipo_contribuyente == 2) {
      // Hace factura C
      $f->id_tipo_comprobante = 11;
      $letra = "C";
    }

    // Si no tiene punto de venta, tomamos el que esta por defecto para ticket
    $pv_numero = 0;
    if ($f->id_punto_venta == 0) {
      $sql = "SELECT * FROM puntos_venta WHERE tipo_impresion = 'F' AND id_empresa = $f->id_empresa LIMIT 0,1";
      $q_pv = $this->db->query($sql);
      $pv = $q_pv->row();
      $pv_numero = $pv->numero;
      $f->id_punto_venta = $pv->id;
      $f->punto_venta = $pv->numero;
    }

    // Debemos calcular los NETOS
    $total_neto = 0;
    $total_iva = 0;
    $iva = array(
      "3"=>array("neto"=>0,"iva"=>0,"alicuota"=>0), // Exento / NG
      "4"=>array("neto"=>0,"iva"=>0,"alicuota"=>10.5),// 10.5
      "5"=>array("neto"=>0,"iva"=>0,"alicuota"=>21),  // 21
      "6"=>array("neto"=>0,"iva"=>0,"alicuota"=>27),  // 27
      "8"=>array("neto"=>0,"iva"=>0,"alicuota"=>5),   // 5
      "9"=>array("neto"=>0,"iva"=>0,"alicuota"=>2.5), // 2.5
    );
    // Recorremos los items
    $array_items = array();
    foreach($f->items as $item) {

      $alicuota_iva = $iva[$item->id_tipo_alicuota_iva]["alicuota"];

      $item->neto = $item->precio / ((100+$alicuota_iva)/100);
      $item->total_sin_iva = $item->total_con_iva / ((100+$alicuota_iva)/100);
      $item->iva = $item->total_con_iva - $item->total_sin_iva;
      $item->porc_iva = $alicuota_iva;

      $iva[$item->id_tipo_alicuota_iva]["neto"] += $item->total_sin_iva;
      $iva[$item->id_tipo_alicuota_iva]["iva"] += $item->iva;

      // Actualizamos el item
      $sql = "UPDATE facturas_items ";
      $sql.= "SET neto = '$item->neto', total_sin_iva = '$item->total_sin_iva', ";
      $sql.= " porc_iva = '$item->porc_iva', id_tipo_alicuota_iva = '$item->id_tipo_alicuota_iva', ";
      $sql.= " id_punto_venta = '$f->id_punto_venta', iva = '$item->iva' ";
      $sql.= "WHERE id_empresa = $f->id_empresa AND id_factura = $f->id ";
      //$this->db->query($sql);
      $array_items[] = $item;
    }
    $f->items = $array_items;

    // Actualizamos los IVA
    foreach($iva as $id_tipo_iva => $iv) {
      if ($iv["neto"] == 0) continue;

      // Eliminamos por si ya existe
      $sql = "DELETE FROM facturas_iva ";
      $sql.= "WHERE id_empresa = $f->id_empresa ";
      $sql.= "AND id_factura = $f->id ";
      $sql.= "AND id_alicuota_iva = $id_tipo_iva ";
      $sql.= "AND id_punto_venta = $f->id_punto_venta ";
      $this->db->query($sql);

      $sql = "INSERT INTO facturas_iva (";
      $sql.= " id_empresa, id_factura, id_alicuota_iva, neto, iva, id_punto_venta ";
      $sql.= ") VALUES(";
      $sql.= " '$f->id_empresa', '$f->id', $id_tipo_iva, '".$iv["neto"]."', '".$iv["iva"]."', '$f->id_punto_venta' ";
      $sql.= ")";
      $total_neto += $iv["neto"];
      $total_iva += $iv["iva"];
      $this->db->query($sql);
    }

    $f->neto = $total_neto;
    $f->iva = $total_iva;

    // Controlamos si hay que sumarle alguna percepcion de IIBB
    if ($f->id_tipo_comprobante == 1 
      && isset($empresa->config["percibe_ib"])
      && $empresa->config["percibe_ib"] == 1
      && isset($f->cliente->percibe_ib)
      && $f->cliente->percibe_ib == 1
      && isset($f->cliente->percepcion_ib)
      && $f->cliente->percepcion_ib > 0) {

      $f->porc_ib = ($f->cliente->percepcion_ib / 100);
      $f->percepcion_ib = $f->neto * ($f->porc_descuento / 100) * $f->porc_ib;
      $f->total = $f->total + $f->percepcion_ib;
    }

    // Tomamos el proximo numero
    $sql = "SELECT * FROM numeros_comprobantes WHERE id_empresa = $f->id_empresa AND id_punto_venta = $f->id_punto_venta AND id_tipo_comprobante = $f->id_tipo_comprobante LIMIT 0,1";
    $q_numero = $this->db->query($sql);
    $r = $q_numero->row();
    $f->numero = $r->ultimo + 1;

    $f->estado = 0;
    $f->comprobante = $letra." ".str_pad($pv_numero,4,"0",STR_PAD_LEFT)."-".str_pad($f->numero,8,"0",STR_PAD_LEFT);

    // Acomodamos los datos para volverlos a guardar
    $this->load->helper("fecha_helper");
    if (!empty($f->fecha)) $f->fecha = fecha_mysql($f->fecha);
    if (!empty($f->fecha_reparto)) $f->fecha_reparto = fecha_mysql($f->fecha_reparto);

    $sql = "UPDATE facturas SET ";
    $sql.= " numero_remito = '$numero_remito', ";
    $sql.= " comprobante = '$f->comprobante', ";
    $sql.= " estado = '$f->estado', ";
    $sql.= " fecha = '$f->fecha', ";
    $sql.= " numero = '$f->numero', ";
    $sql.= " id_punto_venta = '$f->id_punto_venta', ";
    $sql.= " porc_ib = '$f->porc_ib', ";
    $sql.= " percepcion_ib = '$f->percepcion_ib', ";
    $sql.= " total = '$f->total', ";
    $sql.= " neto = '$f->neto', ";
    $sql.= " iva = '$f->iva', ";
    $sql.= " punto_venta = '$f->punto_venta', ";
    $sql.= " id_tipo_comprobante = '$f->id_tipo_comprobante' ";
    $sql.= "WHERE id_empresa = $f->id_empresa ";
    $sql.= "AND id = $id ";
    $this->db->query($sql);

    // Actualizamos el numero
    $this->db->query("UPDATE numeros_comprobantes SET ultimo = $f->numero WHERE id_empresa = $f->id_empresa AND id_punto_venta = $f->id_punto_venta AND id_tipo_comprobante = $f->id_tipo_comprobante");
    return array(
      "error"=>0,
    );
  }

  function limpiar($array) {
    
    // Eliminamos los atributos que no se persisten
    unset($array->undefined);
    unset($array->error);
    unset($array->ivas);
    unset($array->mensaje);
    unset($array->items);
    unset($array->tarjetas);
    unset($array->cheques);
    unset($array->codigo_cliente);
    unset($array->nombre_cliente);
    unset($array->letra);
    unset($array->imprimir);
    unset($array->tipo_estado);
    unset($array->gestiona_stock);
    
    // Redondeamos
    if (isset($array->total)) $array->total = round($array->total,2);
    if (isset($array->subtotal)) $array->subtotal = round($array->subtotal,2);
    if (isset($array->neto)) $array->neto = round($array->neto,2);
    if (isset($array->iva)) $array->iva = round($array->iva,2);
    if (isset($array->porc_descuento)) $array->porc_descuento = round($array->porc_descuento,2);
    if (isset($array->descuento)) $array->descuento = round($array->descuento,2);
    if (isset($array->percepcion_ib)) $array->percepcion_ib = round($array->percepcion_ib,2);
    if (isset($array->efectivo)) $array->efectivo = round($array->efectivo,2);
    if (isset($array->cta_cte)) $array->cta_cte = round($array->cta_cte,2);
    if (isset($array->tarjeta)) $array->tarjeta = round($array->tarjeta,2);
    if (isset($array->cheque)) $array->cheque = round($array->cheque,2);
    if (isset($array->vuelto)) $array->vuelto = round($array->vuelto,2);
    
    return $array;
  }

  
  function obtener_cae($id,$id_punto_venta=0) {
    
    $factura = $this->get($id,$id_punto_venta);
    
    // Tomamos los datos de la empresa
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($factura->id_empresa);
    
    // Creamos el objeto de Factura Electronica
    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);
    
    $res = $fe->solicitar($factura);
    file_put_contents("salida.txt",print_r($res,true),FILE_APPEND);
    if ($res !== FALSE) {
      // Contiene errores
      if (isset($res->Errors)) {
        return array(
          "error"=>1,
          "id"=>$id,
          "tipo_impresion"=>"E",
          "mensaje"=>mb_convert_encoding($res->Errors->Err->Msg, 'ISO-8859-1', 'UTF-8')
        );
      } else {
        
        // La operacion ha sido rechazada, debemos informar
        if ($res->FeCabResp->Resultado == "R") {
          
          // Concatenamos todos los mensajes de error en uno solo
          $mensaje = "";
          if (isset($res->FeDetResp->FECAEDetResponse->Observaciones)) {
            foreach($res->FeDetResp->FECAEDetResponse->Observaciones as $obs) {
              $mensaje.= $obs->Msg."\n";
            }
          }
          if (empty($mensaje)) $mensaje = "Comprobante rechazado.";
          return array(
            "error"=>1,
            "id"=>$id,
            "tipo_impresion"=>"E",
            "mensaje"=>mb_convert_encoding($mensaje, 'ISO-8859-1', 'UTF-8')
          );
          
        // La operacin ha sido exitosa
        } else {
          // Guardamos el CAE y la fecha de vencimiento
          if ($res->FeDetResp->FECAEDetResponse->Resultado == "A") {
            $factura->cae = $res->FeDetResp->FECAEDetResponse->CAE;
            $fecha_vto = $res->FeDetResp->FECAEDetResponse->CAEFchVto;
            $anio = substr($fecha_vto,0,4);
            $mes = substr($fecha_vto,4,2);
            $dia = substr($fecha_vto,6,2);
            $factura->fecha_vto = "$anio-$mes-$dia";
            $sql = "UPDATE facturas SET ";
            $sql.= " cae = '$factura->cae', ";
            $sql.= " fecha_vto = '$factura->fecha_vto', ";
            $sql.= " pendiente = 0 "; // La sacamos del pendiente
            $sql.= "WHERE id = $id ";
            $sql.= "AND id_empresa = $factura->id_empresa ";
            $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
            $this->db->query($sql);
            
            // Actualizamos el ultimo numero del comprobante
            $this->db->query("UPDATE numeros_comprobantes SET ultimo = $factura->numero WHERE id_empresa = $factura->id_empresa AND id_tipo_comprobante = $factura->id_tipo_comprobante AND id_punto_venta = $factura->id_punto_venta");
          }
        }
        
      }
      
    } else {
      return array(
        "error"=>1,
        "id"=>$id,
        "tipo_impresion"=>"E",
        "mensaje"=>"Error al conectarse con los servidores de la AFIP"
      );
    }
    
    return array(
      "error"=>0,
      "id"=>$id,
      "tipo_impresion"=>"E",
      "mensaje"=>"",
    );    
  }
  
  function get_barcode($factura) {
    $barcode = (isset($factura->cliente->cuit)) ? str_replace("-","",str_pad($factura->cliente->cuit,11,"0")) : "";
    $codigo_comprobante = str_pad($factura->id_tipo_comprobante,2,"0",STR_PAD_LEFT);
    $barcode.= $codigo_comprobante.str_pad($factura->punto_venta,4,"0",STR_PAD_LEFT);
    $barcode.= $factura->cae;
    $fecha_vto = str_replace("/","",$factura->fecha_vto);
    $barcode.= substr($fecha_vto,4).substr($fecha_vto,2,2).substr($fecha_vto,0,2);
    
    // Calculamos el digito verificador
    $digito = 0;
    $pares = 0; $impares = 0;
    for($i=0;$i<strlen($barcode);$i++) {
      $car = (int)substr($barcode,$i,1);
      if ($i%2==0) $impares += $car;
      else $pares += $car;
    }
    $impares = 3*$impares;
    $total = $pares + $impares;
    $digito = 10 - ($total%10);
    if ($digito == 10) $digito = 0;
    $barcode.= $digito;
    return $barcode;
  }
  

  /*
  function obtener_cae($id) {
    
    $factura = $this->get($id);
    
    // Tomamos los datos de la empresa
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($factura->id_empresa);
    
    // Creamos el objeto de Factura Electronica
    $this->load->library("Factura_Electronica");
    $fe = new Factura_Electronica();
    $fe->set_empresa($empresa);
    
    // Si es alguna nota de credito
    if ($factura->id_tipo_comprobante == 3 || $factura->id_tipo_comprobante == 8 || $factura->id_tipo_comprobante == 13) {
      $factura->total = abs($factura->total);
      $factura->neto = abs($factura->neto);
      $factura->iva = abs($factura->iva);
      $factura->iva_105 = abs($factura->iva_105);
      foreach($factura->items as $item) {
        $item->subtotal = abs($item->subtotal);
        $item->neto = abs($item->neto);
        $item->precio = abs($item->precio);
        $item->total_con_iva = abs($item->total_con_iva);
      }
    }
    
    $res = $fe->solicitar($factura);
    if ($res !== FALSE) {
      file_put_contents("salida.txt",print_r($res,true));
      
      // Contiene errores
      if (isset($res->Errors) || isset($res["error"])) {
        $mensaje = isset($res->Errors) ? $res->Errors->Err->Msg : $res["error"];
        return array(
          "error"=>1,
          "id"=>$id,
          "tipo_impresion"=>"E",
          "mensaje"=>mb_convert_encoding($mensaje, 'ISO-8859-1', 'UTF-8')
        );
      } else {
        
        // La operacion ha sido rechazada, debemos informar
        if ($res->FeCabResp->Resultado == "R") {
          
          // Concatenamos todos los mensajes de error en uno solo
          $mensaje = "";
          if (isset($res->FeDetResp->FECAEDetResponse->Observaciones)) {
            foreach($res->FeDetResp->FECAEDetResponse->Observaciones as $obs) {
              $mensaje.= $obs->Msg."\n";
            }
          }
          if (empty($mensaje)) $mensaje = "Comprobante rechazado.";
          return array(
            "error"=>1,
            "id"=>$id,
            "tipo_impresion"=>"E",
            "mensaje"=>mb_convert_encoding($mensaje, 'ISO-8859-1', 'UTF-8')
          );
          
        // La operacin ha sido exitosa
        } else {
          // Guardamos el CAE y la fecha de vencimiento
          if ($res->FeDetResp->FECAEDetResponse->Resultado == "A") {
            $factura->cae = $res->FeDetResp->FECAEDetResponse->CAE;
            $fecha_vto = $res->FeDetResp->FECAEDetResponse->CAEFchVto;
            $anio = substr($fecha_vto,0,4);
            $mes = substr($fecha_vto,4,2);
            $dia = substr($fecha_vto,6,2);
            $factura->fecha_vto = "$anio-$mes-$dia";
            $sql = "UPDATE facturas SET ";
            $sql.= " cae = '$factura->cae', ";
            $sql.= " fecha_vto = '$factura->fecha_vto', ";
            $sql.= " pendiente = 0 "; // La sacamos del pendiente
            $sql.= "WHERE id = $id AND id_empresa = $factura->id_empresa";
            $this->db->query($sql);
            
            // Actualizamos el ultimo numero del comprobante
            $this->db->query("UPDATE numeros_comprobantes SET ultimo = $factura->numero WHERE id_empresa = $factura->id_empresa AND id_tipo_comprobante = $factura->id_tipo_comprobante AND id_punto_venta = $factura->id_punto_venta");
          }
        }
        
      }
      
    } else {
      return array(
        "error"=>1,
        "id"=>$id,
        "tipo_impresion"=>"E",
        "mensaje"=>"Error al conectarse con los servidores de la AFIP"
      );
    }
    
    // Generamos el PDF
    //$fe->generar_pdf($array);
    
    // Si cambio el token
    $token = (string)$fe->get_token();
    $sign = (string)$fe->get_sign();
    if ($empresa->token != $token || $empresa->sign != $sign) {
      // Lo cacheamos en la empresa
      $empresa->token = $token;
      $empresa->sign = $sign;
      $empresa->expiration_time = $fe->get_expiration_time();
      $this->Empresa_Model->save($empresa);
    }
    /*
    // Si es necesario enviar un email automatico
    if ($punto_venta->enviar_email == 1 && !empty($cliente->email) && !empty($empresa->email)) {
      $this->load->library("email");
      $this->email->from($empresa->email);
      $this->email->to($cliente->email);
      $this->email->subject("Factura Electronica");
      $this->email->message($punto_venta->texto_email);
      if (file_exists("uploads/$cuit/comprobantes/$pdf")) $this->email->attach("uploads/$cuit/comprobantes/$pdf");
      $this->email->send();
    }
    */
    /*
    return array(
      "error"=>0,
      "id"=>$id,
      "tipo_impresion"=>"E",
      "mensaje"=>"",
    );    
  }
  */
  
  // Controla si la empresa puede seguir facturando o no
  function controlar_plan($id_empresa) {
    
    /*
    // Tomamos el plan de la empresa
    $q = $this->db->query("SELECT P.* FROM empresas E INNER JOIN planes P ON (E.id_plan = P.id) WHERE E.id = $id_empresa");
    if ($q->num_rows()<=0) return FALSE;
    $plan = $q->row();
    
    // Contamos la cantidad de facturas que hizo la empresa en el mes
    $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE()) ";
    $q = $this->db->query($sql);
    $total = $q->row();
    
    if ($plan->limite_facturacion == 0 || $total->cantidad < $plan->limite_facturacion) {
      return TRUE;
    } else {
      return FALSE;
    }
    */
    return TRUE;
    
  }
  
  
    
  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    $id_empresa = parent::get_empresa();
    $estado = ($_SESSION["estado"] == 1 ? 1 : 0);
    $sql = "SELECT A.id, A.comprobante, ";
    $sql.= "IF(A.id_cliente = 0,'Consumidor Final',IF(C.nombre IS NULL,'',C.nombre)) AS cliente, ";
    $sql.= "IF(TC.letra IS NULL,'X',TC.letra) AS letra, ";
    $sql.= "IF(A.fecha IS NULL,'',DATE_FORMAT(A.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM facturas A ";
    $sql.= "LEFT JOIN tipos_comprobante TC ON (A.id_tipo_comprobante = TC.id) ";
    $sql.= "LEFT JOIN clientes C ON (A.id_cliente = C.id) ";
    $sql.= "LEFT JOIN empresas E ON (A.id_empresa = E.id) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if ($estado == 0) $sql.= "AND A.id_tipo_comprobante <= 20 AND A.id_tipo_comprobante > 0 ";
    $sql.= "ORDER BY A.fecha DESC ";
    if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
      $sql.= "LIMIT $limit, $offset ";
    }
    $query = $this->db->query($sql);
    $result = $query->result();
    $this->db->close();
    return $result;
  }  
  
  function get($id,$id_punto_venta = 0,$config = array()) {
    
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $stamp = isset($config["stamp"]) ? $config["stamp"] : -1;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $buscar_consultas = isset($config["buscar_consultas"]) ? $config["buscar_consultas"] : 0;
    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 0;
    
    $sql = "SELECT F.*, ";
    $sql.= " IF(F.fecha_reparto IS NULL,'',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
    $sql.= " IF(F.fecha_vto IS NULL,'',IF(F.fecha_vto = '0000-00-00','',DATE_FORMAT(F.fecha_vto,'%d/%m/%Y'))) AS fecha_vto, ";
    $sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, ";
    $sql.= " IF(TC.letra IS NULL,'',TC.letra) AS letra, ";
    $sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM facturas F ";
    if ($id_empresa == 228) {
      $sql.= "LEFT JOIN pres_clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
    } else {
      if ($id_sucursal == 0) $sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
      else $sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa AND C.id_sucursal = F.id_sucursal) ";      
    }
    $sql.= "LEFT JOIN tipos_comprobante TC ON (TC.id = F.id_tipo_comprobante) ";
    $sql.= "WHERE F.id = $id ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    if ($id_punto_venta != 0) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    if ($stamp != -1) $sql.= "AND F.last_update = $stamp ";
    $query = $this->db->query($sql);
    $row = $query->row();
    
    if (!empty($row)) {
      // Tomamos los items
      $sql = "SELECT FI.*, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
      $sql.= " IF(A.ancho IS NULL,0,A.ancho) AS ancho, ";
      $sql.= " IF(A.alto IS NULL,0,A.alto) AS alto, ";
      $sql.= " IF(A.profundidad IS NULL,0,A.profundidad) AS profundidad, ";
      $sql.= " IF(A.codigo_barra IS NULL,'',A.codigo_barra) AS codigo_barra, ";
      $sql.= " IF(A.id_tipo_alicuota_iva IS NULL,5,A.id_tipo_alicuota_iva) AS id_tipo_alicuota_iva, ";
      $sql.= " IF(AV.nombre IS NULL,'',AV.nombre) AS variante ";
      $sql.= "FROM facturas_items FI ";
      $sql.= " LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
      $sql.= " LEFT JOIN articulos_variantes AV ON (AV.id_empresa = FI.id_empresa AND AV.id_articulo = FI.id_articulo AND AV.id = FI.id_variante) ";
      $sql.= "WHERE FI.id_factura = $id ";
      $sql.= "AND FI.id_punto_venta = $row->id_punto_venta ";
      $sql.= "AND FI.id_empresa = $id_empresa ";
      if ($stamp != -1) $sql.= "AND FI.stamp = $stamp ";

      // VICTOR ordena los items de acuerdo al articulo.custom_1
      if ($id_empresa == 229 || $id_empresa == 230 || $id_empresa == 1355) {
        $sql.= "ORDER BY CAST(A.custom_1 AS SIGNED) ASC, FI.orden ASC ";
      } else {
        $sql.= "ORDER BY FI.orden ASC ";
      }
      $q = $this->db->query($sql);
      $row->items = $q->result();

      // En caso de tener datos de facturacion periodica
      $encontro_periodica = 0;
      if ($this->db->table_exists('facturas_periodicas')) {
        $sql = "SELECT * FROM facturas_periodicas ";
        $sql.= "WHERE id_factura = $id ";
        $sql.= "AND id_punto_venta = $row->id_punto_venta ";
        $sql.= "AND id_empresa = $id_empresa ";
        $q_per = $this->db->query($sql);
        if ($q_per->num_rows()>0) {
          $r_per = $q_per->row();
          $row->es_periodica = 1;
          $row->proxima_emision = $r_per->proxima_emision;
          $row->proximo_vencimiento = $r_per->proximo_vencimiento;
          $row->periodo_cantidad = $r_per->periodo_cantidad;
          $row->periodo_tipo = $r_per->periodo_tipo;
          $row->periodo_dia = $r_per->periodo_dia;
          $row->dias_vencimiento = $r_per->dias_vencimiento;
          $row->factura_electronica = $r_per->factura_electronica;
          $encontro_periodica = 1;
        }
      }
      if ($encontro_periodica == 0) {
        $row->es_periodica = 0;
        $row->proxima_emision = "";
        $row->proximo_vencimiento = "";
        $row->periodo_cantidad = 1;
        $row->periodo_tipo = "M";
        $row->periodo_dia = 1;
        $row->dias_vencimiento = 10;
        $row->factura_electronica = 0;
      }
      
      // Tomamos los ivas
      $sql = "SELECT * ";
      $sql.= "FROM facturas_iva FI ";
      $sql.= "WHERE FI.id_factura = $id ";
      $sql.= "AND FI.id_empresa = $id_empresa ";
      $sql.= "AND FI.id_punto_venta = $row->id_punto_venta ";
      $q = $this->db->query($sql);
      $row->ivas = $q->result();
      
      // Tomamos los datos del cliente
      if ($id_empresa == 228) {
        $this->load->model("Pres_Cliente_Model");
        $cliente = $this->Pres_Cliente_Model->get($row->id_cliente,$id_empresa);
        $cliente->nombre = $cliente->nombre." ".$cliente->apellido;
        $cliente->provincia = "";
        $cliente->tipo_iva = "Consumidor Final";
        $cliente->percibe_ib = 0;
        $cliente->percepcion_ib = 0;
        $cliente->id_sucursal = $id_sucursal;
      } else {
        $this->load->model("Cliente_Model");
        $cliente = $this->Cliente_Model->get($row->id_cliente,$id_empresa,array(
          "id_sucursal"=>$id_sucursal,
          "buscar_consultas"=>$buscar_consultas,
          "buscar_etiquetas"=>$buscar_etiquetas,
        ));
      }
      if ($cliente === FALSE) {
        // Si no existe, es un CF
        $cliente = new stdClass();
        $cliente->cuit = 0;
        $cliente->nombre = "Consumidor Final";
        $cliente->email = "";
        $cliente->direccion = "";
        $cliente->localidad = "";
        $cliente->provincia = "";
        $cliente->custom_1 = "";
        $cliente->custom_2 = "";
        $cliente->custom_3 = "";
        $cliente->custom_4 = "";
        $cliente->custom_5 = "";
        $cliente->id_tipo_iva = 4;
        $cliente->id_tipo_documento = 80;
        $cliente->tipo_iva = "Consumidor Final";
        $cliente->percibe_ib = 0;
        $cliente->percepcion_ib = 0;
        $cliente->id_sucursal = $id_sucursal;
        $cliente->latitud = 0;
        $cliente->longitud = 0;
        $cliente->consultas = array();
        $cliente->etiquetas = array();
      }
      $row->cliente = $cliente;    
    }
    
    $this->db->close();
    return $row;
  }
  
  
  
  function get_by_hash($hash) {
    
    $sql = "SELECT F.*, ";
    $sql.= " IF(F.fecha_reparto IS NULL,'',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
    $sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, ";
    $sql.= " IF(TC.letra IS NULL,'',TC.letra) AS letra, ";
    $sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
    $sql.= "LEFT JOIN tipos_comprobante TC ON (TC.id = F.id_tipo_comprobante) ";
    $sql.= "WHERE F.hash = '$hash' ";
    $query = $this->db->query($sql);
    $row = $query->row();
    
    if (!empty($row)) {
      // Tomamos los items
      $sql = "SELECT * ";
      $sql.= "FROM facturas_items FI ";
      $sql.= "WHERE FI.id_factura = $row->id ";
      $sql.= "AND FI.id_empresa = $row->id_empresa ";
      $sql.= "AND FI.id_punto_venta = $row->id_punto_venta ";
      $sql.= "ORDER BY orden ASC";
      $q = $this->db->query($sql);
      $row->items = $q->result();
      
      // Tomamos los ivas
      $sql = "SELECT * ";
      $sql.= "FROM facturas_iva FI ";
      $sql.= "WHERE FI.id_factura = $row->id ";
      $sql.= "AND FI.id_empresa = $row->id_empresa ";
      $sql.= "AND FI.id_punto_venta = $row->id_punto_venta ";
      $q = $this->db->query($sql);
      $row->ivas = $q->result();
      
      // Tomamos los datos del cliente
      $this->load->model("Cliente_Model");
      $cliente = $this->Cliente_Model->get($row->id_cliente,$row->id_empresa);
      if ($cliente === FALSE) {
        // Si no existe, es un CF
        $cliente = new stdClass();
        $cliente->cuit = 0;
        $cliente->nombre = "Consumidor Final";
        $cliente->direccion = "";
        $cliente->localidad = "";
        $cliente->provincia = "";
        $cliente->tipo_iva = "Consumidor Final";
      }
      $row->cliente = $cliente;    
    }
    
    $this->db->close();
    return $row;
  }
  

  function get_by_hash_empresa($hash) {
    
    $sql = "SELECT F.*, ";
    $sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, ";
    $sql.= " IF(TC.letra IS NULL,'',TC.letra) AS letra, ";
    $sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN tipos_comprobante TC ON (TC.id = F.id_tipo_comprobante) ";
    $sql.= "WHERE F.hash = '$hash' ";
    $query = $this->db->query($sql);
    $row = $query->row();
    
    if (!empty($row)) {
      // Tomamos los items
      $sql = "SELECT * ";
      $sql.= "FROM facturas_items FI ";
      $sql.= "WHERE FI.id_factura = $row->id ";
      $sql.= "AND FI.id_empresa = $row->id_empresa ";
      $sql.= "AND FI.id_punto_venta = $row->id_punto_venta ";
      $sql.= "ORDER BY orden ASC";
      $q = $this->db->query($sql);
      $row->items = $q->result();
      
      // Tomamos los ivas
      $sql = "SELECT * ";
      $sql.= "FROM facturas_iva FI ";
      $sql.= "WHERE FI.id_factura = $row->id ";
      $sql.= "AND FI.id_empresa = $row->id_empresa ";
      $sql.= "AND FI.id_punto_venta = $row->id_punto_venta ";
      $q = $this->db->query($sql);
      $row->ivas = $q->result();
    }
    
    $this->db->close();
    return $row;
  }
  
  
  function get_between_days($desde = "", $hasta = "", $estado = 0) {
    if (empty($desde)) $desde = date("Y-m-d");
    if (empty($hasta)) $hasta = date("Y-m-d");
    $d = new DateTime($desde);
    $h = new DateTime($hasta);
    $interval = new DateInterval('P1D');
    $range = new DatePeriod($d,$interval,$h);
    $id_empresa = parent::get_empresa();
    $salida = array();
    foreach($range as $fecha) {
      $sql = "SELECT ";
      $sql.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad, ";
      $sql.= " IF(SUM(total) IS NULL,0,SUM(total)) AS total ";
      $sql.= "FROM facturas F ";
      $sql.= "WHERE F.id_empresa = $id_empresa AND F.tipo != 'P' AND F.anulada = 0 AND F.pendiente = 0 ";
      $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
      if ($estado == 0) $sql.= "AND F.estado = 0 ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $r->fecha = $fecha->format("Y-m-d");
      $salida[] = $r;
    }
    return $salida;
  }

  // Obtiene la ultima factura generada a un cliente
  function get_ultima($config = array()) {
    $factura = FALSE;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $buscar_consultas = isset($config["buscar_consultas"]) ? $config["buscar_consultas"] : 1;
    $buscar_etiquetas = isset($config["buscar_etiquetas"]) ? $config["buscar_etiquetas"] : 1;
    $sql = "SELECT id, id_punto_venta, id_empresa FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id_cliente)) $sql.= "AND id_cliente = $id_cliente ";
    $sql.= "ORDER BY fecha DESC, hora DESC ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $factura = $this->get($r->id,$r->id_punto_venta,array(
        "id_empresa"=>$r->id_empresa,
        "buscar_consultas"=>$buscar_consultas,
        "buscar_etiquetas"=>$buscar_etiquetas,
      ));
    }
    return $factura;
  }
  
    
  function get_last() {
    $id_empresa = parent::get_empresa();
    $q = $this->db->query("SELECT id FROM facturas WHERE id_empresa = $id_empresa ORDER BY id DESC LIMIT 0,1");
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $f = $this->get($r->id);
      if ($f->numero == 0) return $f;
      else return FALSE;
    } else {
      return FALSE;
    }
  }
  
}