<?php
/**
 * Implementacion de IMPRESORA FISCAL marca EPSON
 */
require_once APPPATH.'libraries/Fiscal.php';

class Fiscal_Epson extends Fiscal {

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
    
    private $fiscal;
    
    /**
     * Establece la conexion para enviar un mensaje al impresor
     */
    private function connect() {
        // En caso de que haya abierto un ticket, lo cancela
        $this->fiscal = new COM("EpsonFiscalInterface");
        $this->fiscal->ConfigurarPuerto("0");
        $this->fiscal->Conectar();
        $ultimo = "";
        $this->fiscal->ConsultarNumeroComprobanteUltimo("83",$ultimo,255);
        echo "TERMINO [$ultimo]";
    }

    public function test() {
        $this->connect();
    }
    
    // IMPORTANTE: SE USA ESTE METODO PARA IMPRIMIR TODO JUNTO
    // EN VEZ DE IR IMPRIMIENDO LINEA POR LINEA
    public function imprimir($c) {
        
        $space = "        ";
        
        // En caso de que haya abierto un ticket, lo cancela
        $this->fiscal = new COM("Activex.Fiscal.1");
        $this->fiscal->IF_OPEN("COM1",9600);
        
        if ($c->id_cliente == 0) {
            // Abrimos ticket comun
            $this->fiscal->EpsonTicket->TIQUEABRE("C");
        } else {
            // Abrimos un ticket factura
            $iva = $this->get_codigo_tipo_iva($c->id_tipo_iva);
            $cuit = str_replace("-","",$c->cuit);
            $direccion = empty($c->direccion) ? chr(127) : $space.$c->direccion;
            $localidad = empty($c->localidad) ? chr(127) : $space.$c->localidad;
            $provincia = empty($c->provincia) ? chr(127) : $space.$c->provincia;
            $this->fiscal->EpsonTicket->FACTABRE("T","C",$c->tipo,1,"P","10","I",$iva,$space.$c->cliente,chr(127),"CUIT",$cuit,"N",$direccion,$localidad,$provincia,chr(127),chr(127),"C");
        }
        
        foreach($c->items as $item) {
            $descripcion = $space.substr($item->descripcion,0,30);
            $cantidad = (float)$item->cantidad;
            $porc_iva = (float)$item->porc_iva;
            $precio = (float)$item->precio;
            if ($c->id_cliente == 0) {
                $this->fiscal->EpsonTicket->TIQUEITEM($descripcion,$cantidad,$precio,$porc_iva,"M",1,0.0,0.0);
            } else {
                if ($c->id_tipo_iva == 1) {
                    $precio = ($item->precio / (1+($item->porc_iva/100))); // Sacamos el IVA
                }
                $this->fiscal->EpsonTicket->FACTITEM($descripcion,$cantidad,$precio,$porc_iva,"M",1,0.0,chr(127),chr(127),chr(127),0.0,0.0);
            }
        }

        if ($c->efectivo != 0) {
            $metodo_pago = "EFECTIVO";
            $vuelto = round($c->efectivo,2);
        } else if ($c->cta_cte != 0) {
            $metodo_pago = "CUENTA CORRIENTE";
            $vuelto = round($c->cta_cte,2);
        } else if ($c->tarjeta != 0) {
            $metodo_pago = "TARJETA";
            $vuelto = round($c->tarjeta,2);
        } else if ($c->cheque != 0) {
            $metodo_pago = "CHEQUE";
            $vuelto = round($c->cheque,2);
        }
        
        if ($c->id_cliente == 0) {
            $this->fiscal->EpsonTicket->TIQUEPAGO($metodo_pago,$vuelto,"T");
        } else {
            $this->fiscal->EpsonTicket->FACTPAGO($metodo_pago,$vuelto,"T");
        }

        if ($c->id_cliente == 0) {
            $this->fiscal->EpsonTicket->TIQUECIERRA("T");
        } else {
            $this->fiscal->EpsonTicket->FACTCIERRA("T",$c->tipo," ");
        }
    }
    
   
    public function abrir_cajon() {
        $this->connect();
        $this->fiscal->EpsonTicket->ABRECAJON1();
    }
    
    public function cancelar() {
        $this->connect();
        $this->fiscal->EpsonTicket->TIQUECANCEL();
        $this->fiscal->EpsonTicket->FACTCANCEL();
    }
    
    public function comenzar($comprobante,$cliente = array()) {
        /*
        $this->connect();
        if (empty($cliente)) {
            // Abrimos ticket comun
            $err = $this->fiscal->EpsonTicket->TIQUEABRE("C"); // Indica que no se va a imprimir ningun DFNH despues del ticket
            echo $err;
        } else {
            // Abrimos ticket factura
            $razon_social = $cliente["razon_social"];
            $numero = str_replace("-","",$cliente["numero"]);
            $iva = $this->get_codigo_tipo_iva($cliente["tipo_iva"]);
            // TODO: Arreglar el tema de la letra del comprobante
            $err = $this->fiscal->EpsonTicket->FACTABRE("T","C","A","1","P","10","I",$iva,$razon_social," ","CUIT",$numero,"N"," "," "," "," "," ","C");
            echo $err;
        }
        //return ($err == 0) ? 1 : 0;
        */
    }
    
    
    private function get_codigo_tipo_iva($iva) {
        if ($iva == 1) return "I"; // Responsable inscripto
        else if ($iva == 2) return "M"; // Monotributo
        else if ($iva == 3) return "E"; // Exento
        else return "F"; // consumidor final
    }
    
   
    public function imprimir_item($descripcion,$cantidad,$precio,$iva = 21,$imp_interno = 0) {
        /*
        $this->connect();
        $this->fiscal->EpsonTicket->TIQUEITEM(substr($descripcion,0,20),$cantidad,$precio,$iva,"M",1,0,0);
        */
    }
    
    public function imprimir_pago($pago,$metodo_pago) {
        /*
        try {
            $this->connect();
            $this->fiscal->ImprimirPago($metodo_pago,$pago);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        */
    }
    
    public function percepcion_global($nombre,$monto) {
        /*
        try {
            $this->connect();
            $this->fiscal->EspecificarPercepcionGlobal($nombre,$monto);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        */
    }
    
    public function cerrar($comprobante) {
        /*
        try {
            $this->connect();
            $this->fiscal->CerrarComprobanteFiscal();
            $this->fiscal->Finalizar();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        */
    }
    
    public function imprimir_z() {
        $this->connect();
        $this->fiscal->EpsonTicket->CIERREZ();
    }
    
    public function imprimir_x() {
        $this->connect();
        $this->fiscal->EpsonTicket->CIERREX();
    }
   
}
?>