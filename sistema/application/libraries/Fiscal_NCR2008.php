<?php
/**
 * Implementacion de IMPRESORA FISCAL marca HASAR
 */
require APPPATH.'libraries/Fiscal.php';

class Fiscal_NCR2008 extends Fiscal {

  // Constantes utilizadas para determinar la funcion
  const CMD_SET_CUSTOMER_DATA = 0x62;
  const CMD_OPEN_FISCAL_RECEIPT = 0x40;
  const CMD_PRINT_LINE_ITEM = 0x42;
  const CMD_SUBTOTAL = 0x43;
  const CMD_TOTAL = 0x44;
  const CMD_CLOSE_FISCAL_RECEIPT = 0x45;
  const CMD_CANCEL = 0x98;

  private $secuencia = 0x80; // Numero de secuencia del paquete

  // Comprobantes Homologados
  const FACTURA_A = "A";
  const FACTURA_B = "B";
  const NOTA_DEBITO_A = "D";
  const NOTA_DEBITO_B = "E";
  const RECIBO_A = "a";
  const RECIBO_B = "b";
  const TICKET_C = "B";
  const TICKET_A = "A";
  const TICKET_B = "B";
  const TICKET_NOTA_DEBITO_A = "D";
  const TICKET_NOTA_DEBITO_B = "E";

  // Comprobantes No Homologados
  const NOTA_CREDITO_A = 82;
  const NOTA_CREDITO_B = 83;
  const TICKET_NOTA_CREDITO_A = 52;
  const TICKET_NOTA_CREDITO_B = 53;
  const REMITO = 114;

  private $fiscal;
  private $serial;

  private function empaquetar($comando,$parametros = array()) {
    
    if ($this->secuencia > 127) $this->secuencia = 32;
    //$array[] = chr(0x06); // ACK
    $array[] = chr(0x02); // Start of Frame
    $array[] = chr($this->secuencia); // Nro Seq
    $array[] = chr(0x1B); // ESC
    $array[] = chr($comando);
    if (sizeof($parametros)>0) {
      foreach($parametros as $param) {
        $array[] = chr(0x1C); // Separador
        $array[] = $param;
      }
    }
    $array[] = chr(0x03); // End of Frame
    $suma = 0;
    foreach($array as $r) {
      for($i=0;$i<strlen($r);$i++) {
        $suma+=ord(substr($r,$i,1));
      }
      fwrite($this->serial,$r);
    }

    // Calculamos el checksum
    $salida = "";
    $checksum = array();
    $sumahex = dechex($suma);
    for($i=0;$i<strlen($sumahex);$i++) $checksum[] = substr($sumahex,$i,1);
    for($i=sizeof($checksum);$i<4;$i++) array_unshift($checksum,"0");
    foreach($checksum as $c) {
      $salida.= $c;
    }
    fwrite($this->serial, $salida);
    fflush($this->serial);
    sleep(1);
    $buffer = fgets($this->serial);
    echo $buffer;
    
    $this->secuencia = $this->secuencia + 2;
  }

  function connect() {
    try {
      $this->serial = fopen("COM1:", "a+");
    } catch(Exception $e) {
      echo "No se puede conectar al puerto COM1";
    }
  }

  public function abrir_cajon() {
    echo "Funcion no soportada";
  }

  public function cancelar() {
    try {
      $this->connect();
      $this->empaquetar(self::CMD_CANCEL);
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }
  
  public function comenzar($comprobante,$cliente = array()) {
    try {
      $this->connect();
      if (!empty($cliente)) {
        $razon_social = $cliente["razon_social"];
        $numero = str_replace("-","",$cliente["numero"]);
        $iva = $this->get_codigo_tipo_iva($cliente["tipo_iva"]);
        $tipo_documento = $this->get_tipo_documento($cliente["tipo_doc"]);
        $domicilio = (isset($cliente["domicilio"]) ? $cliente["domicilio"] : "");

        $this->empaquetar(self::CMD_SET_CUSTOMER_DATA,array(
          $razon_social,$numero,$iva,$tipo_documento,$domicilio
        ));
      } else {
        $this->empaquetar(self::CMD_SET_CUSTOMER_DATA,array(
          "Consumidor Final","","C"," "," "
        ));        
      }

      if ($comprobante == self::NOTA_CREDITO_A ||
        $comprobante == self::NOTA_CREDITO_B ||
        $comprobante == self::TICKET_NOTA_CREDITO_A ||
        $comprobante == self::TICKET_NOTA_CREDITO_B ||
        $comprobante == self::REMITO) {
        $this->fiscal->DocumentoDeReferencia("1");
        $this->fiscal->AbrirDNFH($comprobante);
      } else {
        $this->empaquetar(self::CMD_OPEN_FISCAL_RECEIPT,array($comprobante,"T"));
      }
      return 1;
    } catch(Exception $e) {
      echo $e->getMessage();
      return 0;
    }
  }


  function get_codigo_tipo_iva($iva) {
    $iva = strtolower($iva);
    if ($iva == "responsable inscripto") return "I";
    else if ($iva == "monotributo") return "M";
    else if ($iva == "exento") return "E";
    else return "C"; // consumidor final
  }

  function get_tipo_documento($tipo_doc) {
    if ($tipo_doc == 80) return "C"; // CUIT
    else if ($tipo_doc == 96) return "2"; // DNI
    else if ($tipo_doc == 86) return " "; // CUIL
    else if ($tipo_doc == 89) return "0"; // Libreta Enrolamiento
    else if ($tipo_doc == 90) return "1"; // Libreta Civica
    else if ($tipo_doc == 94) return "3"; // Pasaporte
    else return " "; // No es ninguno
  }

  public function imprimir_item($descripcion,$cantidad,$precio,$iva = 21,$imp_interno = 0) {
    try {
      $cantidad = (float)$cantidad;
      $precio = (float)$precio;
      $this->empaquetar(self::CMD_PRINT_LINE_ITEM,array(
        $descripcion,$cantidad,$precio,21.00,"M",0.0,0,"B",
      ));
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }

  public function imprimir_pago($pago,$metodo_pago) {
    try {
      $this->empaquetar(self::CMD_TOTAL,array(
        $metodo_pago,$pago,"T"
      ));
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }

  public function percepcion_global($nombre,$monto) {
    echo "Funcion no soportada";
  }

  public function cerrar($comprobante) {
    try {
      if ($comprobante == self::NOTA_CREDITO_A ||
        $comprobante == self::NOTA_CREDITO_B ||
        $comprobante == self::TICKET_NOTA_CREDITO_A ||
        $comprobante == self::TICKET_NOTA_CREDITO_B ||
        $comprobante == self::REMITO) {

        $this->fiscal->CerrarDNFH();
      } else {
        $this->empaquetar(self::CMD_CLOSE_FISCAL_RECEIPT);
      }
    } catch(Exception $e) {
      echo $e->getMessage();
    }        
  }
  
  public function imprimir_z() {
    try {
      $this->connect();
      $this->fiscal->ReporteZ();
    } catch(Exception $e) {
      echo $e->getMessage();
    }        
  }
  
  public function imprimir_x() {
    try {
      $this->connect();
      $this->fiscal->ReporteX();
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }

}
?>