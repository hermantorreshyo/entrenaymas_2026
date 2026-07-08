<?php
/**
 * Esta clase sirve como interface para usar un controlador fiscal
 * definido en la configuracion del usuario del sistema
 * Tiene todos los metodos comunes que deben realizar los controladores
 */
abstract class Fiscal {
    
    abstract public function comenzar($comprobante,$cliente);
    
    abstract public function cancelar();
    
    abstract public function abrir_cajon();
    
    abstract public function imprimir_item($descripcion,$cantidad,$precio,$iva,$imp_interno = 0);
    
    abstract public function imprimir_pago($pago,$metodo_pago);
    
    abstract public function percepcion_global($nombre,$monto);
    
    abstract public function cerrar($comprobante);
    
    abstract public function imprimir_z();
    
    abstract public function imprimir_x();
    
}
?>