<?php
print_r(get_loaded_extensions());
if (!extension_loaded("php_tm20v52ts.dll")) {
    echo "Libreria PHP no esta cargada";
    exit();
}
$port = IF_OPEN("COM1",9600);


// Este modulo contiene el código a disposicion por parte de IFDRIVERS
// en una base TAL CUAL. Todo receptor del Modulo se considera
// bajo licencia de los derechos de autor de IFDRIVERS para utilizar el
// codigo fuente siempre en modo que el o ella considere conveniente,
// incluida la copia, la compilacion, su modificacion o la redistribucion,
// con o sin modificaciones. Ninguna licencia o patentes de IFDRivers
// este implicita en la presente licencia.
//
// El usuario del codigo fuente debera entender que IFDRIVERS no puede
// Proporcionar apoyo tecnico para el modulo y no sera Responsable
// de las consecuencias del uso del programa.
//
// Todas las comunicaciones, incluida esta, no deben ser removidos
// del modulo sin el consentimiento previo por escrito de IFDRIVERS
// www: http://www.impresoras-fiscales.com.ar/
// email: soporte@impresoras-fiscales.com.ar
//
// Instrucciones para usar el driver y las funciones de alto nivel en PHP:
//
// 1) Instale la extension fiscal en el directorio ext de PHP.
// 2) Agregue la extension fiscal en el archivo php.ini, por ejemplo
//
// Para versiones de PHP Thread Safe ( la mas usada)
// extension = php_TM20V5xts.dll 
//
// Para versiones de PHP Non-Thread Safe (la menos usada)
// extension = php_TM20V5xnts.dll 
//
// 3) Copie el archivo con estas funciones al directorio de su su proyecto.
// 4) Agregue las funciones con la funcion required al principio del codigo php
// 5) Todas las funciones de la extension fiscal mas las funciones de alto nivel
//    seran accesibles desde PHP.
// 
// Por ejemplo:
//
// require 'TM20PhpApi.php'
//
// $nError = IF_OPEN("COM1",9600);
//
// $nError = Sincro();
//
// ....etc
//

//***************************************************************
//* 1. Comandos de diagnóstico
//*******************************************************************************
// ESTADO()
// 
// Syntax: 
//		ESTADO($byVar1);
// Propósito:
//		Consulta de estado
// Argumentos: 
//		byVar1	Código de operación {NPCADS}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION ESTADO($byVar1)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 $strBuff= "@ESTADO" . "|" . $byVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//***************************************************************
//* 2. Comandos de control fiscal
//*******************************************************************************
// CIERRE()
// 
// Syntax: 
//		CIERRE($byVar1, $byVar2);
// Propósito:
//		Cierre de jornada fiscal
// Argumentos: 
//		byVar1	Tipo de reporte {ZX}
//		byVar2	Imprimir {PN}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION CIERRE($byVar1, $byVar2)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 $strBuff= "@CIERRE" . "|" . $byVar1 . "|" . $byVar2;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// CIERREZ()
// 
// Syntax: 
//		CIERREZ();
// Propósito:
//		Efectúa un CierreZ
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION CIERREZ()
{

 $strBuff= "@CIERREZ";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// CIERREX()
// 
// Syntax: 
//		CIERREX();
// Propósito:
//		Efectua un CierreX
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION CIERREX()
{

 $strBuff= "@CIERREX";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// AUDITORIAF()
// 
// Syntax: 
//		AUDITORIAF($strVar1, $strVar2, $byVar3);
// Propósito:
//		Reporte de auditoría por fechas
// Argumentos: 
//		strVar1	Fecha inicial (AAMMDD) (max 6 bytes)
//		strVar2	Fecha final (AAMMDD) (max 6 bytes)
//		byVar3	tipo {TDtd}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION AUDITORIAF($strVar1, $strVar2, $byVar3)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 $strBuff= "@AUDITORIAF" . "|" . $strVar1 . "|" . $strVar2 . "|" . $byVar3;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// AUDITORIAZ()
// 
// Syntax: 
//		AUDITORIAZ($nVar1, $nVar2, $byVar3);
// Propósito:
//		Reporte de auditoría por fechas
// Argumentos: 
//		nVar1	Nro Z inicial (nnnn)
//		nVar2	Nro Z final (nnnn)
//		byVar3	tipo {TDtd}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION AUDITORIAZ($nVar1, $nVar2, $byVar3)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 if(!is_int($nVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return -1;
 }

 if(!is_string($byVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 $strBuff= "@AUDITORIAZ" . "|" . $nVar1 . "|" . $nVar2 . "|" . $byVar3;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//***************************************************************
//* 3. Comandos para generar comprobantes fiscales
//*******************************************************************************
// TIQUEABRE()
// 
// Syntax: 
//		TIQUEABRE($byVar1);
// Propósito:
//		Abrir comprobante fiscal
// Argumentos: 
//		byVar1	Formato para almacenar los datos {CG}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION TIQUEABRE($byVar1)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 $strBuff= "@TIQUEABRE" . "|" . $byVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// TIQUETEXTO()
// 
// Syntax: 
//		TIQUETEXTO($strVar1);
// Propósito:
//		Imprimir texto fiscal
// Argumentos: 
//		strVar1	Texto fiscal a imprimir (max 26 bytes)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION TIQUETEXTO($strVar1)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 $strBuff= "@TIQUETEXTO" . "|" . $strVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// TIQUEITEM()
// 
// Syntax: 
//		TIQUEITEM($strVar1, $dblVar2, $dblVar3, $dblVar4, $byVar5, $nVar6, $dblVar7, $dblVar8);
// Propósito:
//		Imprimir Item
// Argumentos: 
//		strVar1	Descripción del Item (max 20 bytes)
//		dblVar2	Cantidad (nnnnn.nnn)
//		dblVar3	Monto del ítem (nnnnnnn.nn)
//		dblVar4	Tasa del IVA (10.5, 21.0) (nn.nn)
//		byVar5	Calificador de la operación {MmRr}
//		nVar6	Nro de bultos (nnnnn)
//		dblVar7	Tasa de ajuste variable (.nnnnnnnn)
//		dblVar8	Impuestos internos fijos (nnnnnnnnn.nnnnnnnn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION TIQUEITEM($strVar1, $dblVar2, $dblVar3, $dblVar4, $byVar5, $nVar6, $dblVar7, 
                    $dblVar8)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return -1;
 }

 if(!is_string($byVar5))
 {
  echo "Error: el tipo del parametro 5 no coincide\n";
  return (-1);
 }

 if(!is_int($nVar6))
 {
  echo "Error: el tipo del parametro 6 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar7))
 {
  echo "Error: el tipo del parametro 7 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar8))
 {
  echo "Error: el tipo del parametro 8 no coincide\n";
  return -1;
 }

 $strBuff= "@TIQUEITEM" . "|" . $strVar1 . "|" . $dblVar2 . "|" . $dblVar3 . "|" . 
             $dblVar4 . "|" . $byVar5 . "|" . $nVar6 . "|" . $dblVar7 . "|" . 
              $dblVar8;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// TIQUEITEM2()
// 
// Syntax: 
//		TIQUEITEM2($strVar1, $dblVar2, $dblVar3, $dblVar4, $byVar5, $nVar6, $dblVar7, $dblVar8);
// Propósito:
//		Imprimir Item con 4 decimales en el precio
// Argumentos: 
//		strVar1	Descripción del Item (max 20 bytes)
//		dblVar2	Cantidad (nnnnn.nnn)
//		dblVar3	Monto del ítem (nnnnnnn.nnnn)
//		dblVar4	Tasa del IVA (10.5, 21.0) (nn.nn)
//		byVar5	Calificador de la operación {MmRr}
//		nVar6	Nro de bultos (nnnnn)
//		dblVar7	Tasa de ajuste variable (.nnnnnnnn)
//		dblVar8	Impuestos internos fijos (nnnnnnnnn.nnnnnnnn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION TIQUEITEM2($strVar1, $dblVar2, $dblVar3, $dblVar4, $byVar5, $nVar6, $dblVar7, 
                     $dblVar8)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return -1;
 }

 if(!is_string($byVar5))
 {
  echo "Error: el tipo del parametro 5 no coincide\n";
  return (-1);
 }

 if(!is_int($nVar6))
 {
  echo "Error: el tipo del parametro 6 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar7))
 {
  echo "Error: el tipo del parametro 7 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar8))
 {
  echo "Error: el tipo del parametro 8 no coincide\n";
  return -1;
 }

 $strBuff= "@TIQUEITEM2" . "|" . $strVar1 . "|" . $dblVar2 . "|" . $dblVar3 . "|" . 
             $dblVar4 . "|" . $byVar5 . "|" . $nVar6 . "|" . $dblVar7 . "|" . 
              $dblVar8;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// TIQUESUBTOTAL()
// 
// Syntax: 
//		TIQUESUBTOTAL($byVar1, $strVar2);
// Propósito:
//		Subtotal
// Argumentos: 
//		byVar1	Imprimir {PN}
//		strVar2	Texto a imprimir en el subtotal (max 25 bytes)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION TIQUESUBTOTAL($byVar1, $strVar2)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 $strBuff= "@TIQUESUBTOTAL" . "|" . $byVar1 . "|" . $strVar2;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// TIQUEPAGO()
// 
// Syntax: 
//		TIQUEPAGO($strVar1, $dblVar2, $byVar3);
// Propósito:
//		Devolución de envases, Bonificaciones y Recargos
// Argumentos: 
//		strVar1	Texto con descripción del pago (max 25 bytes)
//		dblVar2	monto (nnnnnnn.nn)
//		byVar3	Calificador del ítem de línea {CTtDR}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION TIQUEPAGO($strVar1, $dblVar2, $byVar3)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return -1;
 }

 if(!is_string($byVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 $strBuff= "@TIQUEPAGO" . "|" . $strVar1 . "|" . $dblVar2 . "|" . $byVar3;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// TIQUECIERRA()
// 
// Syntax: 
//		TIQUECIERRA($byVar1);
// Propósito:
//		Cerrar comprobante fiscal
// Argumentos: 
//		byVar1	Tipo de corte {TP}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION TIQUECIERRA($byVar1)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 $strBuff= "@TIQUECIERRA" . "|" . $byVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// TIQUECANCEL()
// 
// Syntax: 
//		TIQUECANCEL();
// Propósito:
//		Cancela una ticket
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION TIQUECANCEL()
{

 $strBuff= "@TIQUECANCEL";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//***************************************************************
//* 4. Comandos para generar comprobantes no fiscales
//*******************************************************************************
// NOFISABRE()
// 
// Syntax: 
//		NOFISABRE($byVar1);
// Propósito:
//		Abrir comprobante no-fiscal por rollo de papel
// Argumentos: 
//		byVar1	Se debe enviar S para imprimir en hoja suelta {S}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION NOFISABRE($byVar1)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 $strBuff= "@NOFISABRE" . "|" . $byVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// NOFISITEM()
// 
// Syntax: 
//		NOFISITEM($strVar1);
// Propósito:
//		Imprimir texto no-fiscal en un Documento No Fiscal
// Argumentos: 
//		strVar1	Texto no fiscal (max 40 bytes)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION NOFISITEM($strVar1)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 $strBuff= "@NOFISITEM" . "|" . $strVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// NOFISCIERRA()
// 
// Syntax: 
//		NOFISCIERRA($byVar1);
// Propósito:
//		Cerrar comprobante no-fiscal
// Argumentos: 
//		byVar1	Tipo de corte {TP}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION NOFISCIERRA($byVar1)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 $strBuff= "@NOFISCIERRA" . "|" . $byVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//***************************************************************
//* 5. Comandos para generar DNFH
//*******************************************************************************
// DNFHTARJETADECREDITO()
// 
// Syntax: 
//		DNFHTARJETADECREDITO($strVar1, $strVar2, $strVar3, $strVar4, $strVar5, $strVar6, 
//		                      $strVar7, $strVar8, $strVar9, $dblVar10, $strVar11, 
//		                       $strVar12, $strVar13, $strVar14, $strVar15, $strVar16, 
//		                        $strVar17, $strVar18, $byVar19, $byVar20, $byVar21);
// Propósito:
//		Comando Voucher Tarjeta de Crédito generado con un comprobante no fiscal homologado.
// Argumentos: 
//		strVar1	Nombre de tarjeta de crédito (max 20 bytes)
//		strVar2	nro de la tarjeta de crédito (max 20 bytes)
//		strVar3	Nombre del usuario de la tarjeta de crédito (max 23 bytes)
//		strVar4	Fecha de vencimiento de la tarjeta de crédito (AAMMDD) (max 6 bytes)
//		strVar5	número de establecimiento (max 20 bytes)
//		strVar6	número de cupon (max 20 bytes)
//		strVar7	número interno del comprobante que se esta emitiendo (max 20 bytes)
//		strVar8	codigo de autorización de la transaccion (max 20 bytes)
//		strVar9	tipo de operación (max 20 bytes)
//		dblVar10	Importe que a pagar. (nnnnnnnnn.nn)
//		strVar11	cantidad de cuotas (max 20 bytes)
//		strVar12	en que moneda se ha realizado la transaccion (max 20 bytes)
//		strVar13	número de terminal (max 20 bytes)
//		strVar14	número de lote (max 20 bytes)
//		strVar15	número de terminal electronica (max 20 bytes)
//		strVar16	número de sucursal (max 20 bytes)
//		strVar17	número o nombre del operador. (max 20 bytes)
//		strVar18	número de documento fiscal al que se hace referencia (max 20 bytes)
//		byVar19	Si se envia P se deja una línea para la firma del cliente {NP}
//		byVar20	Si se envia P se deja una línea para la aclaración de la firma {NP}
//		byVar21	Si se envia P se deja una línea para el nro de telefono {NP}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION DNFHTARJETADECREDITO($strVar1, $strVar2, $strVar3, $strVar4, $strVar5, $strVar6, 
                               $strVar7, $strVar8, $strVar9, $dblVar10, 
                                $strVar11, $strVar12, $strVar13, $strVar14, 
                                 $strVar15, $strVar16, $strVar17, $strVar18, 
                                  $byVar19, $byVar20, $byVar21)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar5))
 {
  echo "Error: el tipo del parametro 5 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar6))
 {
  echo "Error: el tipo del parametro 6 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar7))
 {
  echo "Error: el tipo del parametro 7 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar8))
 {
  echo "Error: el tipo del parametro 8 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar9))
 {
  echo "Error: el tipo del parametro 9 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar10))
 {
  echo "Error: el tipo del parametro 10 no coincide\n";
  return -1;
 }

 if(!is_string($strVar11))
 {
  echo "Error: el tipo del parametro 11 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar12))
 {
  echo "Error: el tipo del parametro 12 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar13))
 {
  echo "Error: el tipo del parametro 13 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar14))
 {
  echo "Error: el tipo del parametro 14 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar15))
 {
  echo "Error: el tipo del parametro 15 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar16))
 {
  echo "Error: el tipo del parametro 16 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar17))
 {
  echo "Error: el tipo del parametro 17 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar18))
 {
  echo "Error: el tipo del parametro 18 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar19))
 {
  echo "Error: el tipo del parametro 19 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar20))
 {
  echo "Error: el tipo del parametro 20 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar21))
 {
  echo "Error: el tipo del parametro 21 no coincide\n";
  return (-1);
 }

 $strBuff= "@DNFHTARJETADECREDITO" . "|" . $strVar1 . "|" . $strVar2 . "|" . $strVar3 . "|" . 
             $strVar4 . "|" . $strVar5 . "|" . $strVar6 . "|" . 
              $strVar7 . "|" . $strVar8 . "|" . $strVar9 . "|" . $dblVar10 . "|" . 
               $strVar11 . "|" . $strVar12 . "|" . $strVar13 . "|" . $strVar14 . "|" . 
                $strVar15 . "|" . $strVar16 . "|" . $strVar17 . "|" . 
                 $strVar18 . "|" . $byVar19 . "|" . $byVar20 . "|" . $byVar21;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// DNFHOBRASOCIAL()
// 
// Syntax: 
//		DNFHOBRASOCIAL($strVar1, $strVar2, $strVar3, $strVar4, $strVar5, $strVar6, $strVar7, 
//		                $strVar8, $strVar9, $strVar10, $strVar11, $strVar12, $strVar13, 
//		                 $byVar14, $byVar15, $byVar16, $byVar17, $byVar18);
// Propósito:
//		Comando Documento Exclusivo para Farmacias como Documento No Fiscal Homologado.
// Argumentos: 
//		strVar1	Nombre de la Obra Social y/o el nro de Obra Social. (max 20 bytes)
//		strVar2	Linea para identificar el coseguro (max 20 bytes)
//		strVar3	Linea para identificar el coseguro (max 20 bytes)
//		strVar4	Linea para identificar el coseguro (max 20 bytes)
//		strVar5	Nro de afiliado (max 20 bytes)
//		strVar6	Nombre del afiliado (max 20 bytes)
//		strVar7	Fecha de vencimiento del carnet de la obra social. El formato es AAMMDD (max 6 bytes)
//		strVar8	Domicilio Fiscal del Vendedor línea 1 (max 20 bytes)
//		strVar9	Domicilio Fiscal del Vendedor línea 2 (max 20 bytes)
//		strVar10	número o nombre del establecimiento (max 20 bytes)
//		strVar11	número interno del comprobante que se esta emitiendo. (max 20 bytes)
//		strVar12	línea 1 para especificar algun dato a la obra social (max 20 bytes)
//		strVar13	línea 2 para especificar algun dato a la obra social (max 20 bytes)
//		byVar14	Si se envia 'P' se deja un espacio para que el cliente ponga su domicilio {NP}
//		byVar15	Si se envia 'P' se deja un espacio para que el cliente ponga su nro de documento {NP}
//		byVar16	Si se envia 'P' se deja un espacio para que el cliente ponga su firma {NP}
//		byVar17	Si se envia 'P' se deja un espacio para que el cliente aclare su firma {NP}
//		byVar18	Si se envia 'P' se deja un espacio para que el cliente ponga su telefono {NP}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION DNFHOBRASOCIAL($strVar1, $strVar2, $strVar3, $strVar4, $strVar5, $strVar6, 
                         $strVar7, $strVar8, $strVar9, $strVar10, $strVar11, 
                          $strVar12, $strVar13, $byVar14, $byVar15, $byVar16, 
                           $byVar17, $byVar18)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar5))
 {
  echo "Error: el tipo del parametro 5 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar6))
 {
  echo "Error: el tipo del parametro 6 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar7))
 {
  echo "Error: el tipo del parametro 7 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar8))
 {
  echo "Error: el tipo del parametro 8 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar9))
 {
  echo "Error: el tipo del parametro 9 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar10))
 {
  echo "Error: el tipo del parametro 10 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar11))
 {
  echo "Error: el tipo del parametro 11 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar12))
 {
  echo "Error: el tipo del parametro 12 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar13))
 {
  echo "Error: el tipo del parametro 13 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar14))
 {
  echo "Error: el tipo del parametro 14 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar15))
 {
  echo "Error: el tipo del parametro 15 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar16))
 {
  echo "Error: el tipo del parametro 16 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar17))
 {
  echo "Error: el tipo del parametro 17 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar18))
 {
  echo "Error: el tipo del parametro 18 no coincide\n";
  return (-1);
 }

 $strBuff= "@DNFHOBRASOCIAL" . "|" . $strVar1 . "|" . $strVar2 . "|" . $strVar3 . "|" . 
             $strVar4 . "|" . $strVar5 . "|" . $strVar6 . "|" . $strVar7 . "|" . 
              $strVar8 . "|" . $strVar9 . "|" . $strVar10 . "|" . $strVar11 . "|" . 
               $strVar12 . "|" . $strVar13 . "|" . $byVar14 . "|" . 
                $byVar15 . "|" . $byVar16 . "|" . $byVar17 . "|" . $byVar18;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// PONEPREFERENCIA()
// 
// Syntax: 
//		PONEPREFERENCIA($byVar1, $byVar2, $byVar3, $byVar4, $nVar5, $nVar6);
// Propósito:
//		Seleccionar las preferencias del usuario
// Argumentos: 
//		byVar1	Se debe enviar 'P' {P}
//		byVar2	Opción 1 {DPT}
//		byVar3	Opción 2 {SRPQ}
//		byVar4	Opción 3 {OUNS}
//		nVar5	Si el campo 4 es 'U': establece la cantidad de columnas (nn)
//		nVar6	Si el campo 4 es 'U': establece la cantidad de líneas que mide la hoja de papel a usar (nn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION PONEPREFERENCIA($byVar1, $byVar2, $byVar3, $byVar4, $nVar5, $nVar6)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return (-1);
 }

 if(!is_int($nVar5))
 {
  echo "Error: el tipo del parametro 5 no coincide\n";
  return -1;
 }

 if(!is_int($nVar6))
 {
  echo "Error: el tipo del parametro 6 no coincide\n";
  return -1;
 }

 $strBuff= "@PONEPREFERENCIA" . "|" . $byVar1 . "|" . $byVar2 . "|" . $byVar3 . "|" . 
             $byVar4 . "|" . $nVar5 . "|" . $nVar6;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// LEEPREFERENCIA()
// 
// Syntax: 
//		LEEPREFERENCIA($byVar1, $byVar2, $byVar3);
// Propósito:
//		Leer las preferencias del usuario
// Argumentos: 
//		byVar1	Se debe enviar P {P}
//		byVar2	Opción 1 {DPT}
//		byVar3	Opción 2 {SPQ}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION LEEPREFERENCIA($byVar1, $byVar2, $byVar3)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 $strBuff= "@LEEPREFERENCIA" . "|" . $byVar1 . "|" . $byVar2 . "|" . $byVar3;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// ESTACIONPPAL()
// 
// Syntax: 
//		ESTACIONPPAL();
// Propósito:
//		Preparar estación principal
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION ESTACIONPPAL()
{

 $strBuff= "@ESTACIONPPAL";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//***************************************************************
//* 6. Comandos de control de la impresora
//*******************************************************************************
// CORTAPAPEL()
// 
// Syntax: 
//		CORTAPAPEL();
// Propósito:
//		Cortar papel
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION CORTAPAPEL()
{

 $strBuff= "@CORTAPAPEL";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// AVANZATIQUE()
// 
// Syntax: 
//		AVANZATIQUE($nVar1);
// Propósito:
//		Avanzar papel de tickets
// Argumentos: 
//		nVar1	Nro de líneas a avanzar (nn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION AVANZATIQUE($nVar1)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 $strBuff= "@AVANZATIQUE" . "|" . $nVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// AVANZAAUDIT()
// 
// Syntax: 
//		AVANZAAUDIT($nVar1);
// Propósito:
//		Avanzar papel cinta de auditoría
// Argumentos: 
//		nVar1	Nro de líneas a avanzar (nn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION AVANZAAUDIT($nVar1)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 $strBuff= "@AVANZAAUDIT" . "|" . $nVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// AVANZAAMBOS()
// 
// Syntax: 
//		AVANZAAMBOS($nVar1);
// Propósito:
//		Avanzar papeles de tickets y cinta de auditoría
// Argumentos: 
//		nVar1	Nro de líneas a avanzar (nn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION AVANZAAMBOS($nVar1)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 $strBuff= "@AVANZAAMBOS" . "|" . $nVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// AVANZAHOJA()
// 
// Syntax: 
//		AVANZAHOJA($nVar1);
// Propósito:
//		Avanzar hoja suelta o factura
// Argumentos: 
//		nVar1	Nro de líneas a avanzar (nn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION AVANZAHOJA($nVar1)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 $strBuff= "@AVANZAHOJA" . "|" . $nVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//***************************************************************
//* 7. Comandos generales
//*******************************************************************************
// PONEFECHORA()
// 
// Syntax: 
//		PONEFECHORA($strVar1, $strVar2);
// Propósito:
//		Ingresar fecha y hora
// Argumentos: 
//		strVar1	Fecha (AAMMDD) (max 6 bytes)
//		strVar2	Hora (HHMMSS) (max 6 bytes)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION PONEFECHORA($strVar1, $strVar2)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 $strBuff= "@PONEFECHORA" . "|" . $strVar1 . "|" . $strVar2;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// PIDEFECHORA()
// 
// Syntax: 
//		PIDEFECHORA();
// Propósito:
//		Consultar fecha y hora
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION PIDEFECHORA()
{

 $strBuff= "@PIDEFECHORA";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// PONEENCABEZADO()
// 
// Syntax: 
//		PONEENCABEZADO($nVar1, $strVar2);
// Propósito:
//		Programar texto de encabezamiento y cola
// Argumentos: 
//		nVar1	Nro de línea de datos fijos (nnnnn)
//		strVar2	Texto fiscal de datos fijos (max 40 bytes)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION PONEENCABEZADO($nVar1, $strVar2)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 if(!is_string($strVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 $strBuff= "@PONEENCABEZADO" . "|" . $nVar1 . "|" . $strVar2;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// PIDEENCABEZADO()
// 
// Syntax: 
//		PIDEENCABEZADO($nVar1);
// Propósito:
//		Consultar texto de encabezamiento y cola
// Argumentos: 
//		nVar1	Nro de línea de datos fijos (nnnnn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION PIDEENCABEZADO($nVar1)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 $strBuff= "@PIDEENCABEZADO" . "|" . $nVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// BARCODE()
// 
// Syntax: 
//		BARCODE($nVar1, $strVar2, $nVar3);
// Propósito:
//		Imprimir Código de Barras en los encabezados o pié del Ticket.
// Argumentos: 
//		nVar1	Tipo de código de Barra a imprimir (nn)
//		strVar2	Datos a Imprimir (max 40 bytes)
//		nVar3	Nro de línea de datos fijos (nnnnn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION BARCODE($nVar1, $strVar2, $nVar3)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 if(!is_string($strVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_int($nVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return -1;
 }

 $strBuff= "@BARCODE" . "|" . $nVar1 . "|" . $strVar2 . "|" . $nVar3;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// ABRECAJON1()
// 
// Syntax: 
//		ABRECAJON1();
// Propósito:
//		Abrir gaveta de dinero 1
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION ABRECAJON1()
{

 $strBuff= "@ABRECAJON1";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// ABRECAJON2()
// 
// Syntax: 
//		ABRECAJON2();
// Propósito:
//		Abrir gaveta de dinero 2
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION ABRECAJON2()
{

 $strBuff= "@ABRECAJON2";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//***************************************************************
//* 8. Emisión de Tickets Factura y NC
//*******************************************************************************
// FACTABRE()
// 
// Syntax: 
//		FACTABRE($byVar1, $byVar2, $byVar3, $nVar4, $byVar5, $strVar6, $byVar7, $byVar8, 
//		          $strVar9, $strVar10, $strVar11, $strVar12, $byVar13, $strVar14, 
//		           $strVar15, $strVar16, $strVar17, $strVar18, $byVar19);
// Propósito:
//		Abrir comprobante fiscal Ticket-Factura
// Argumentos: 
//		byVar1	Tipo de documento fiscal que se va a realizar {TM}
//		byVar2	Tipo de salida impresa para factura fiscal o recibo - factura {CS}
//		byVar3	Letra del documento fiscal (A,B o C) {ABC}
//		nVar4	Cantidad de copias que se deben IMPRIMIR (1 a 5) (n)
//		byVar5	Tipo de formulario que se utiliza para la factura emitidas en hoja suelta o formulario continuo {PFA}
//		strVar6	Tamaño de los caracteres que se van a utilizar en toda la factura emitida en hoja suelta o formulario continuo) (max 2 bytes)
//		byVar7	Responsabilidad frente al IVA del EMISOR en el modo entrenamiento {IRNEMT}
//		byVar8	Responsabilidad frente al IVA del COMPRADOR {IRNEMFSTCV}
//		strVar9	Nombre comercial del comprador línea 1 (max 40 bytes)
//		strVar10	Nombre comercial del comprador línea 2 (max 40 bytes)
//		strVar11	Tipo de documento del comprador (CUIT, CUIL,DNI) (max 6 bytes)
//		strVar12	CUIT o documento del comprador (max 11 bytes)
//		byVar13	Linea OPCIONAL bien de USO {NB}
//		strVar14	Domicilio del comprador, línea 1 (max 40 bytes)
//		strVar15	Domicilio del comprador, línea 2 (max 40 bytes)
//		strVar16	Domicilio del comprador, línea 3 (max 40 bytes)
//		strVar17	Línea 1 de texto variable (Nro. de Remito en Ticket-Factura o nro. de factura original en Notas de Crédito) (max 40 bytes)
//		strVar18	Línea 2 de texto variable (max 40 bytes)
//		byVar19	Formato para almacenar los datos (C o G) {CG}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION FACTABRE($byVar1, $byVar2, $byVar3, $nVar4, $byVar5, $strVar6, $byVar7, 
                   $byVar8, $strVar9, $strVar10, $strVar11, $strVar12, $byVar13, 
                    $strVar14, $strVar15, $strVar16, $strVar17, $strVar18, 
                     $byVar19)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 if(!is_int($nVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return -1;
 }

 if(!is_string($byVar5))
 {
  echo "Error: el tipo del parametro 5 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar6))
 {
  echo "Error: el tipo del parametro 6 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar7))
 {
  echo "Error: el tipo del parametro 7 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar8))
 {
  echo "Error: el tipo del parametro 8 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar9))
 {
  echo "Error: el tipo del parametro 9 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar10))
 {
  echo "Error: el tipo del parametro 10 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar11))
 {
  echo "Error: el tipo del parametro 11 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar12))
 {
  echo "Error: el tipo del parametro 12 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar13))
 {
  echo "Error: el tipo del parametro 13 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar14))
 {
  echo "Error: el tipo del parametro 14 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar15))
 {
  echo "Error: el tipo del parametro 15 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar16))
 {
  echo "Error: el tipo del parametro 16 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar17))
 {
  echo "Error: el tipo del parametro 17 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar18))
 {
  echo "Error: el tipo del parametro 18 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar19))
 {
  echo "Error: el tipo del parametro 19 no coincide\n";
  return (-1);
 }

 $strBuff= "@FACTABRE" . "|" . $byVar1 . "|" . $byVar2 . "|" . $byVar3 . "|" . $nVar4 . "|" . 
             $byVar5 . "|" . $strVar6 . "|" . $byVar7 . "|" . $byVar8 . "|" . 
              $strVar9 . "|" . $strVar10 . "|" . $strVar11 . "|" . 
               $strVar12 . "|" . $byVar13 . "|" . $strVar14 . "|" . $strVar15 . "|" . 
                $strVar16 . "|" . $strVar17 . "|" . $strVar18 . "|" . 
                 $byVar19;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// FACTITEM()
// 
// Syntax: 
//		FACTITEM($strVar1, $dblVar2, $dblVar3, $dblVar4, $byVar5, $nVar6, $dblVar7, $strVar8, 
//		          $strVar9, $strVar10, $dblVar11, $dblVar12);
// Propósito:
//		Imprimir Item
// Argumentos: 
//		strVar1	Descripción del producto (max 20 bytes)
//		dblVar2	Cantidad de unidades (nnnnn.nnn)
//		dblVar3	Monto del ítem (nnnnnnn.nn)
//		dblVar4	Tasa del IVA (nn.nn)
//		byVar5	Calificador de la operación {MmRr}
//		nVar6	Nro de bultos (nnnnn)
//		dblVar7	Tasa de ajuste variable (.nnnnnnnn)
//		strVar8	Linea 1 descripción complementaria del ítem (max 30 bytes)
//		strVar9	Linea 2 descripción complementaria del ítem (max 30 bytes)
//		strVar10	Linea 3 descripción complementaria del ítem (max 30 bytes)
//		dblVar11	Acrecentamiento (.nnnn)
//		dblVar12	Impuestos internos fijos (nnnnnnnnn.nnnnnnnn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION FACTITEM($strVar1, $dblVar2, $dblVar3, $dblVar4, $byVar5, $nVar6, $dblVar7, 
                   $strVar8, $strVar9, $strVar10, $dblVar11, $dblVar12)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return -1;
 }

 if(!is_string($byVar5))
 {
  echo "Error: el tipo del parametro 5 no coincide\n";
  return (-1);
 }

 if(!is_int($nVar6))
 {
  echo "Error: el tipo del parametro 6 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar7))
 {
  echo "Error: el tipo del parametro 7 no coincide\n";
  return -1;
 }

 if(!is_string($strVar8))
 {
  echo "Error: el tipo del parametro 8 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar9))
 {
  echo "Error: el tipo del parametro 9 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar10))
 {
  echo "Error: el tipo del parametro 10 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar11))
 {
  echo "Error: el tipo del parametro 11 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar12))
 {
  echo "Error: el tipo del parametro 12 no coincide\n";
  return -1;
 }

 $strBuff= "@FACTITEM" . "|" . $strVar1 . "|" . $dblVar2 . "|" . $dblVar3 . "|" . 
             $dblVar4 . "|" . $byVar5 . "|" . $nVar6 . "|" . $dblVar7 . "|" . 
              $strVar8 . "|" . $strVar9 . "|" . $strVar10 . "|" . $dblVar11 . "|" . 
               $dblVar12;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// FACTITEM2()
// 
// Syntax: 
//		FACTITEM2($strVar1, $dblVar2, $dblVar3, $dblVar4, $byVar5, $nVar6, $dblVar7, $strVar8, 
//		           $strVar9, $strVar10, $dblVar11, $dblVar12);
// Propósito:
//		Imprimir Item con 4 decimales
// Argumentos: 
//		strVar1	Descripción del producto (max 20 bytes)
//		dblVar2	Cantidad de unidades (nnnnn.nnn)
//		dblVar3	Monto del ítem (nnnnnnn.nnnn)
//		dblVar4	Tasa del IVA (nn.nn)
//		byVar5	Calificador de la operación {MmRr}
//		nVar6	Nro de bultos (nnnnn)
//		dblVar7	Tasa de ajuste variable (.nnnnnnnn)
//		strVar8	Linea 1 descripción complementaria del ítem (max 30 bytes)
//		strVar9	Linea 2 descripción complementaria del ítem (max 30 bytes)
//		strVar10	Linea 3 descripción complementaria del ítem (max 30 bytes)
//		dblVar11	Acrecentamiento (.nnnn)
//		dblVar12	Impuestos internos fijos (nnnnnnnnn.nnnnnnnn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION FACTITEM2($strVar1, $dblVar2, $dblVar3, $dblVar4, $byVar5, $nVar6, $dblVar7, 
                    $strVar8, $strVar9, $strVar10, $dblVar11, $dblVar12)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return -1;
 }

 if(!is_string($byVar5))
 {
  echo "Error: el tipo del parametro 5 no coincide\n";
  return (-1);
 }

 if(!is_int($nVar6))
 {
  echo "Error: el tipo del parametro 6 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar7))
 {
  echo "Error: el tipo del parametro 7 no coincide\n";
  return -1;
 }

 if(!is_string($strVar8))
 {
  echo "Error: el tipo del parametro 8 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar9))
 {
  echo "Error: el tipo del parametro 9 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar10))
 {
  echo "Error: el tipo del parametro 10 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar11))
 {
  echo "Error: el tipo del parametro 11 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar12))
 {
  echo "Error: el tipo del parametro 12 no coincide\n";
  return -1;
 }

 $strBuff= "@FACTITEM2" . "|" . $strVar1 . "|" . $dblVar2 . "|" . $dblVar3 . "|" . 
             $dblVar4 . "|" . $byVar5 . "|" . $nVar6 . "|" . $dblVar7 . "|" . 
              $strVar8 . "|" . $strVar9 . "|" . $strVar10 . "|" . $dblVar11 . "|" . 
               $dblVar12;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// FACTSUBTOTAL()
// 
// Syntax: 
//		FACTSUBTOTAL($byVar1, $strVar2);
// Propósito:
//		Subtotal
// Argumentos: 
//		byVar1	Imprimir {PN}
//		strVar2	Texto a imprimir en el subtotal (max 29 bytes)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION FACTSUBTOTAL($byVar1, $strVar2)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 $strBuff= "@FACTSUBTOTAL" . "|" . $byVar1 . "|" . $strVar2;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// FACTPAGO()
// 
// Syntax: 
//		FACTPAGO($strVar1, $dblVar2, $byVar3);
// Propósito:
//		Pagos / Descuentos y Recargos/ Cancelar en Factura / Nota de Crédito / Tique-Factura / Tique-Nota de Crédito.
// Argumentos: 
//		strVar1	Texto con descripción del pago (max 25 bytes)
//		dblVar2	monto (nnnnnnnnnn.nn)
//		byVar3	Calificador del ítem de línea {CTtDR}
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION FACTPAGO($strVar1, $dblVar2, $byVar3)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return -1;
 }

 if(!is_string($byVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 $strBuff= "@FACTPAGO" . "|" . $strVar1 . "|" . $dblVar2 . "|" . $byVar3;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// FACTPERCEP()
// 
// Syntax: 
//		FACTPERCEP($strVar1, $byVar2, $dblVar3, $dblVar4);
// Propósito:
//		Percepciones en Factura / Nota de Crédito / TF / TNC.
// Argumentos: 
//		strVar1	Descripción de la percepción (max 25 bytes)
//		byVar2	Indica si es una percepción sobre el IVA u otra percepción {OIT}
//		dblVar3	Monto de la percepción (nnnnnnnn.nn)
//		dblVar4	Tasa del IVA (percepciones sobre el IVA) (nn.nn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION FACTPERCEP($strVar1, $byVar2, $dblVar3, $dblVar4)
{

 if(!is_string($strVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_numeric($dblVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return -1;
 }

 if(!is_numeric($dblVar4))
 {
  echo "Error: el tipo del parametro 4 no coincide\n";
  return -1;
 }

 $strBuff= "@FACTPERCEP" . "|" . $strVar1 . "|" . $byVar2 . "|" . $dblVar3 . "|" . 
             $dblVar4;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// FACTCIERRA()
// 
// Syntax: 
//		FACTCIERRA($byVar1, $byVar2, $strVar3);
// Propósito:
//		Cerrar comprobante fiscal
// Argumentos: 
//		byVar1	Tipo de documento {TM}
//		byVar2	Letra del documento fiscal {ABC}
//		strVar3	Descripción que se imprime en la línea TOTAL (max 20 bytes)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION FACTCIERRA($byVar1, $byVar2, $strVar3)
{

 if(!is_string($byVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return (-1);
 }

 if(!is_string($byVar2))
 {
  echo "Error: el tipo del parametro 2 no coincide\n";
  return (-1);
 }

 if(!is_string($strVar3))
 {
  echo "Error: el tipo del parametro 3 no coincide\n";
  return (-1);
 }

 $strBuff= "@FACTCIERRA" . "|" . $byVar1 . "|" . $byVar2 . "|" . $strVar3;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// FACTCANCEL()
// 
// Syntax: 
//		FACTCANCEL();
// Propósito:
//		Cancela una factura
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION FACTCANCEL()
{

 $strBuff= "@FACTCANCEL";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//***************************************************************
//* 9. Otros comandos
//*******************************************************************************
// PAUSA()
// 
// Syntax: 
//		PAUSA($nVar1);
// Propósito:
//		Efectúa una pausa
// Argumentos: 
//		nVar1	timeout (en milisegundos) (nnnn)
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION PAUSA($nVar1)
{

 if(!is_int($nVar1))
 {
  echo "Error: el tipo del parametro 1 no coincide\n";
  return -1;
 }

 $strBuff= "@PAUSA" . "|" . $nVar1;	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
//*******************************************************************************
// SINCRO()
// 
// Syntax: 
//		SINCRO();
// Propósito:
//		Cancela cualquier comprobante fiscal abierto
// Argumentos: 
//		Ninguno
// Devuelve:
//		0 si no hay error y != 0 si hay un error
//******************************************************************************
FUNCTION SINCRO()
{

 $strBuff= "@SINCRO";	

 $nError = IF_WRITE($strBuff);
 
 return ($nError);
}
 
?>