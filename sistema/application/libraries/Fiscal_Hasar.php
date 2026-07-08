<?php
/**
 * Implementacion de IMPRESORA FISCAL marca HASAR
 */
require APPPATH.'libraries/Fiscal.php';

class Fiscal_Hasar extends Fiscal {

    // Comprobantes Homologados
  const FACTURA_A = 48;
  const FACTURA_B = 49;
  const NOTA_DEBITO_A = 68;
  const NOTA_DEBITO_B = 69;
  const RECIBO_A = 97;
  const RECIBO_B = 98;
  const TICKET_C = 84;
  const TICKET_A = 65;
  const TICKET_B = 66;
  const TICKET_NOTA_DEBITO_A = 50;
  const TICKET_NOTA_DEBITO_B = 51;

    // Comprobantes No Homologados
  const NOTA_CREDITO_A = 82;
  const NOTA_CREDITO_B = 83;
  const TICKET_NOTA_CREDITO_A = 52;
  const TICKET_NOTA_CREDITO_B = 53;
  const REMITO = 114; 

  private $fiscal;

  /**
   * Establece la conexion para enviar un mensaje al impresor
   */
  function connect() {
    try {
      $this->fiscal = new COM("HASAR.Fiscal");
      $this->fiscal->Transporte = 0;
      $this->fiscal->Puerto = 1;
      $this->fiscal->Baudios = 9600;
      $this->fiscal->Comenzar();                    
          //$this->fiscal->AutodetectarModelo();
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function ultima_factura_a() {
    try {
      $this->connect();
      $numero = $this->fiscal->UltimoDocumentoFiscalA();
      return $numero;
    } catch(Exception $e) {
      return 0;
    }
  }

  public function ultima_factura_b() {
    try {
      $this->connect();
      $numero = $this->fiscal->UltimoDocumentoFiscalBC();
      return $numero;
    } catch(Exception $e) {
      return 0;
    }
  }

  public function abrir_cajon() {
    try {
      $this->connect();
      $this->fiscal->AbrirCajonDeDinero();
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }

  public function cancelar() {
    try {
      $this->connect();
      $this->fiscal->CancelarComprobanteFiscal();
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
        $direccion = isset($cliente["domicilio"]) ? $cliente["domicilio"] : "";
        $this->fiscal->DatosCliente($razon_social,$numero,$tipo_documento,$iva,$direccion);
      }

      if ($comprobante == self::NOTA_CREDITO_A ||
        $comprobante == self::NOTA_CREDITO_B ||
        $comprobante == self::TICKET_NOTA_CREDITO_A ||
        $comprobante == self::TICKET_NOTA_CREDITO_B ||
        $comprobante == self::REMITO) {
        //$this->fiscal->DocumentoDeReferencia("X 0001-00000001");
        $this->fiscal->AbrirComprobanteNoFiscal(84);
      } else {
        $this->fiscal->AbrirComprobanteFiscal($comprobante);
      }

      //$salida = $this->fiscal->PrimerNumeroDeDocumentoActual();
      //file_put_contents("impresor_fiscal.txt", $salida);

      return 1;
    } catch(Exception $e) {
      echo $e->getMessage();
      return 0;
    }
  }


  function get_codigo_tipo_iva($iva) {
    $iva = strtolower($iva);
    if ($iva == "responsable inscripto") return 73;
    else if ($iva == "monotributo") return 77;
    else if ($iva == "exento") return 69;
    else return 67; // consumidor final
  }

  function get_tipo_documento($tipo_doc) {
    if ($tipo_doc == 80) return 67; // CUIT
    else if ($tipo_doc == 96) return 50; // DNI
    else if ($tipo_doc == 86) return 76; // CUIL
    else if ($tipo_doc == 89) return 48; // Libreta Enrolamiento
    else if ($tipo_doc == 90) return 49; // Libreta Civica
    else if ($tipo_doc == 94) return 51; // Pasaporte
    else return 50; // No es ninguno, devolvemos DNI
  }

  public function imprimir_item($descripcion,$cantidad,$precio,$iva = 21,$imp_interno = 0) {
    try {
      $this->connect();
      $this->fiscal->ImprimirItem($descripcion,$cantidad,$precio,$iva,0);
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }

  public function imprimir_item_remito($descripcion) {
    try {
      $this->connect();
      $this->fiscal->ImprimirTextoNoFiscal($descripcion);
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }

  public function imprimir_pago($pago,$metodo_pago) {
    try {
      $this->connect();
      $this->fiscal->ImprimirPago($metodo_pago,$pago);
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }

  public function percepcion_global($nombre,$monto) {
    try {
      $this->connect();
      $this->fiscal->EspecificarPercepcionGlobal($nombre,$monto);
    } catch(Exception $e) {
      echo $e->getMessage();
    }        
  }

  public function cerrar($comprobante) {
    try {
      $this->connect();
      if ($comprobante == self::NOTA_CREDITO_A ||
        $comprobante == self::NOTA_CREDITO_B ||
        $comprobante == self::TICKET_NOTA_CREDITO_A ||
        $comprobante == self::TICKET_NOTA_CREDITO_B ||
        $comprobante == self::REMITO) {

        $this->fiscal->CerrarComprobanteNoFiscal();
      } else {
        $this->fiscal->CerrarComprobanteFiscal();
      }
      $this->fiscal->Finalizar();
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

  public function enviar($cmd) {
    try {
      $this->connect();
      $this->fiscal->Enviar($cmd);
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }

}
?>