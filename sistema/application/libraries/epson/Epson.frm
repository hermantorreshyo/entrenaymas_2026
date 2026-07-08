VERSION 5.00
Begin VB.Form Form1 
   Caption         =   "Form1"
   ClientHeight    =   3030
   ClientLeft      =   120
   ClientTop       =   450
   ClientWidth     =   4560
   LinkTopic       =   "Form1"
   ScaleHeight     =   3030
   ScaleWidth      =   4560
   StartUpPosition =   3  'Windows Default
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Public puerto As String
Public velocidad As Integer

Const ERROR_NINGUNO As Long = 0

Private Declare Function EnviarComando Lib "EpsonFiscalInterface.dll" (ByVal comando As String) As Long
Private Declare Function ObtenerRespuestaExtendida Lib "EpsonFiscalInterface.dll" (ByVal numero_campo As Long, ByVal buffer_salida As Long, ByVal largo_buffer_salida As Long, ByVal largo_final_buffer_salida As Long) As Long

Private Declare Function Cancelar Lib "EpsonFiscalInterface.dll" () As Long
Private Declare Function ConsultarUltimoError Lib "EpsonFiscalInterface.dll" () As Long

Private Declare Function ComenzarLog Lib "EpsonFiscalInterface.dll" (ByVal incluir_tramas As Boolean) As Long
Private Declare Function DetenerLog Lib "EpsonFiscalInterface.dll" () As Long

Private Declare Function ConsultarVersionDll Lib "EpsonFiscalInterface.dll" (ByVal descripcion As String, ByVal descripcion_largo_maximo As Long, ByVal mayor As Long, ByVal menor As Long) As Long
Private Declare Function ConsultarVersionEquipo Lib "EpsonFiscalInterface.dll" (ByVal descripcion As String, ByVal descripcion_largo_maximo As Long, ByVal mayor As Long, ByVal menor As Long) As Long
Private Declare Function ConsultarFechaHora Lib "EpsonFiscalInterface.dll" (ByVal respuesta As String, ByVal descripcion_largo_maximo As Long) As Long
Private Declare Function ConsultarDescripcionDeError Lib "EpsonFiscalInterface.dll" (ByVal numero_de_errr As Long, ByVal respuesta_descripcion As String, ByVal respuesta_descripcion_largo_maximo As Long) As Long

Private Declare Function ConsultarEstado Lib "EpsonFiscalInterface.dll" (ByVal id_consulta As Long, ByVal respuesta As Long) As Long

Private Declare Function ConsultarNumeroPuntoDeVenta Lib "EpsonFiscalInterface.dll" (ByVal respuesta As String, ByVal respuesta_largo_maximo As Long) As Long
Private Declare Function ConsultarNumeroComprobanteUltimo Lib "EpsonFiscalInterface.dll" (ByVal tipo_de_comprobante As String, ByVal respuesta As String, ByVal respuesta_largo_maximo As Long) As Long
Private Declare Function ConsultarNumeroComprobanteActual Lib "EpsonFiscalInterface.dll" (ByVal respuesta As String, ByVal respuesta_largo_maximo As Long) As Long
Private Declare Function ConsultarTipoComprobanteActual Lib "EpsonFiscalInterface.dll" (ByVal respuesta As String, ByVal respuesta_largo_maximo As Long) As Long

Private Declare Function CargarDatosCliente Lib "EpsonFiscalInterface.dll" (ByVal nombre_o_razon_social1 As String, ByVal nombre_o_razon_social2 As String, ByVal domicilio1 As String, ByVal domicilio2 As String, ByVal domicilio3 As String, ByVal id_tipo_documento As Long, ByVal numero_documento As String, ByVal id_responsabilidad_iva As Long) As Long
Private Declare Function CargarComprobanteAsociado Lib "EpsonFiscalInterface.dll" (ByVal descripcion As String) As Long
Private Declare Function AbrirComprobante Lib "EpsonFiscalInterface.dll" (ByVal id_tipo_documento As Long) As Long
Private Declare Function CargarTextoExtra Lib "EpsonFiscalInterface.dll" (ByVal descripcion As String) As Long
Private Declare Function ImprimirItem Lib "EpsonFiscalInterface.dll" (ByVal id_modificador As Long, ByVal descripcion As String, ByVal cantidad As String, ByVal precio As String, ByVal id_tasa_iva As Long, ByVal ii_id As Long, ByVal ii_valor As String, ByVal id_codigo As Long, ByVal codigo As String, ByVal codigo_unidad_matrix As String, ByVal codigo_unidad_medida As Long) As Long
Private Declare Function ImprimirTextoLibre Lib "EpsonFiscalInterface.dll" (ByVal descripcion As String) As Long
Private Declare Function CerrarComprobante Lib "EpsonFiscalInterface.dll" () As Long

Private Declare Function CargarAjuste Lib "EpsonFiscalInterface.dll" (ByVal id_modificador As Integer, ByVal descripcion As String, ByVal monto As String, ByVal id_tasa_iva As Integer, ByVal codigo_interno As String) As Long

Private Declare Function CargarLogo Lib "EpsonFiscalInterface.dll" (ByVal nombre_de_archivo As String) As Long
Private Declare Function EliminarLogo Lib "EpsonFiscalInterface.dll" () As Long

Private Declare Function CargarPago Lib "EpsonFiscalInterface.dll" (ByVal id_modificador As Long, ByVal codigo_forma_pago As Long, ByVal cantidad_cuotas As Integer, ByVal monto As String, ByVal descripcion_cupones As String, ByVal descripcion As String, ByVal descripcion_extra1 As String, ByVal descripcion_extra2 As String) As Long

Private Declare Function ConfigurarVelocidad Lib "EpsonFiscalInterface.dll" (ByVal velocidad As Long) As Long
Private Declare Function ConfigurarPuerto Lib "EpsonFiscalInterface.dll" (ByVal puerto As String) As Long
Private Declare Function Conectar Lib "EpsonFiscalInterface.dll" () As Long
Private Declare Function ImprimirCierreX Lib "EpsonFiscalInterface.dll" () As Long
Private Declare Function ImprimirCierreZ Lib "EpsonFiscalInterface.dll" () As Long
Private Declare Function Desconectar Lib "EpsonFiscalInterface.dll" () As Long

Private Sub log(linea As String)
    Dim iFileNo As Integer
    iFileNo = FreeFile
    Open App.Path & "\log.txt" For Append As #iFileNo
    Print #iFileNo, Now() & " " & linea
    Close #iFileNo
End Sub


Private Function conectar_impresora() As Long
    Dim error As Long
    ConfigurarVelocidad (velocidad)
    ConfigurarPuerto (puerto)
    error = Conectar()
    conectar_impresora = error
End Function

Private Function desconectar_impresora() As Long
    Dim error As Long
    error = Desconectar()
    desconectar_impresora = error
End Function

Private Function imprimir_x() As Long
    Dim error As Long
    error = ImprimirCierreX()
    imprimir_x = error
End Function

Private Function imprimir_z() As Long
    Dim error As Long
    error = ImprimirCierreZ()
    imprimir_z = error
End Function

Private Function ultimo_comprobante(tipo As String) As String
    Dim error As Long
    Dim str_comprobante_numero As String
    str_comprobante_numero = String(60, vbNullChar)
    error = ConsultarNumeroComprobanteUltimo(tipo, str_comprobante_numero, Len(str_comprobante_numero))
    If Not error Then
        ultimo_comprobante = ""
    Else
        ultimo_comprobante = str_comprobante_numero
    End If
End Function

Private Function imprimir_item(modificador As Integer, descripcion As String, cantidad As String, precio As String, tipo_iva As Integer, tipo_imp_interno As Integer, valor_imp_interno As String, codigo As String) As Long
    Call log("Comienzo imprimir item")
    Dim error As Long
    ' 1 Int modificador (200 agrega, 201 elimina)
    ' 2 String descripcion
    ' 3 String cantidad (99999.9999)
    ' 4 String precio (9999999.9999)
    ' 5 Int tipo_iva (0 = Ninguno, 1 = Exento, 4 = 10.50, 5 = 21)
    ' 6 Int impuesto interno (0 = Ninguno, 1 = Interno Fijo, 2 = Porcentaje
    ' 7 String valor impuesto interno (9999999.9999 Fijo o 0.99999999 %)
    ' 8 Int tipo codigo (1 = interno, 2 = matrix)
    ' 9 String codigo
    ' 10 Codigo unidad matrix
    ' 11 Int codigo_medida (7 = Unidades)
    error = ImprimirItem(modificador, descripcion, cantidad, precio, tipo_iva, tipo_imp_interno, valor_imp_interno, 1, codigo, "", 7)
    imprimir_item = error
End Function

' 1 = Tique, 2 = Factura A/B/C/M, 3 = NC, 4 = ND, 21 = DNFH Generico, 22 = DNFH Uso interno
Private Function abrir_comprobante(tipo As Integer) As Long
    Dim error As Long
    error = AbrirComprobante(tipo)
    abrir_comprobante = error
End Function

Private Function cerrar_comprobante() As Long
    Dim error As Long
    error = CerrarComprobante()
    cerrar_comprobante = error
End Function

Private Function cargar_pago(codigo As Integer, monto As String) As Long
    Dim error As Long
    error = CargarPago(200, codigo, 0, monto, "", "", "", "")
    cargar_pago = error
End Function

' tipo_documento: 0=Ninguno, 1=DNI, 2=CUIL, 3=CUIT
' tipo_iva: 0=Ninguno, 1=RI, 3=NR, 4=Monotributo, 5=CF, 6=Exento
Private Function cargar_datos_cliente(nombre As String, direccion As String, id_tipo_documento As Integer, numero_documento As String, tipo_iva As Integer) As Long
    Dim error As Long
    error = CargarDatosCliente(nombre, "0", direccion, "0", "0", id_tipo_documento, numero_documento, tipo_iva)
    cargar_datos_cliente = error
End Function

Private Function cargar_descuento(monto As String, id_alicuota_iva As Integer) As Long
    Dim error As Long
    ' 400 es el codigo de descuento
    error = CargarAjuste(400, "Descuento", monto, id_alicuota_iva, "")
    cargar_descuento = error
End Function

Private Function enviar_comando(comando As String) As Long
    Dim error As Long
    error = EnviarComando(comando)
    enviar_comando = error
End Function

Private Sub Form_Load()
    Dim error As Long
    Dim MyLine As String
    Dim i As Integer
    Dim cmd As String
    ReDim comandos(1000) As String
    ComenzarLog (True)
    
    Call log("comenzo proceso")
    Open App.Path & "\epson.txt" For Input As #1
    Do Until EOF(1)
        ' Recorremos cada lineaa
        Line Input #1, MyLine
        error = 0
        ' Y separamos cada campo
        comandos = Split(MyLine, ";;;")
        cmd = LCase(CStr(comandos(0)))
        
        Call log("Comando leido: " & comandos(0))
        
        ' Configuramos el puerto
        If cmd = "puerto" Then
            puerto = comandos(1)
            
            Call log("Puerto = " & puerto)
            
        ' Configuramos la velocidad
        ElseIf cmd = "velocidad" Then
            velocidad = CInt(comandos(1))
            Call log("Velocidad = " & comandos(1))
        
        ' Enviamos el comando directamente al controlador
        ElseIf cmd = "enviar_comando" Then

            error = enviar_comando(CStr(comandos(1)))
            If error Then
                Call log("Error enviar_comando: " & CStr(error))
            Else
                Call log("Enviar comando OK")
            End If
        
        ' Conectamos con la impresora
        ElseIf cmd = "conectar_impresora" Then
            error = conectar_impresora()
            If error Then
                Call log("Error conectar_impresora: " & CStr(error))
            Else
                Call log("Conexion establecida")
            End If
            
        ' Desconectamos la impresora
        ElseIf cmd = "desconectar_impresora" Then
            error = desconectar_impresora()
            If error Then
                Call log("Error desconectar_impresora: " & CStr(error))
            Else
                Call log("Desconexion realizada")
            End If
            
        ' Imprimimos el reporte X
        ElseIf cmd = "imprimir_x" Then
            error = imprimir_x()
            
        ' Imprimimos el reporte Z
        ElseIf cmd = "imprimir_z" Then
            error = imprimir_z()
            
        ' Cargar datos del cliente
        ElseIf cmd = "cargar_datos_cliente" Then
            ' nombre, direccion, tipo_documento, numero_documento, tipo_iva
            error = cargar_datos_cliente(CStr(comandos(1)), CStr(comandos(2)), CInt(comandos(3)), CStr(comandos(4)), CInt(comandos(5)))
            If error Then
                Call log("Error cargar_datos_cliente: " & CStr(error))
            Else
                Call log("Cargar datos cliente OK")
            End If
            
        ' Abrimos el comprobante
        ElseIf cmd = "abrir_comprobante" Then
            error = abrir_comprobante(CInt(comandos(1)))
            If error Then
                Call log("Error abrir_comprobante: " & CStr(error))
            Else
                Call log("Abrir Comprobantee OK")
            End If
            
        ' Imprimir Item
        ElseIf cmd = "imprimir_item" Then
            ' 1 Int modificador (200 agrega, 201 elimina)
            ' 2 String descripcion
            ' 3 String cantidad (99999.9999)
            ' 4 String precio (9999999.9999)
            ' 5 Int tipo_iva (0 = Ninguno, 1 = Exento, 4 = 10.50, 5 = 21)
            ' 6 Int impuesto interno (0 = Ninguno, 1 = Interno Fijo, 2 = Porcentaje
            ' 7 String valor impuesto interno (9999999.9999 Fijo o 0.99999999 %)
            ' 8 String codigo
            error = imprimir_item(CInt(comandos(1)), CStr(comandos(2)), CStr(comandos(3)), CStr(comandos(4)), CInt(comandos(5)), CInt(comandos(6)), CStr(comandos(7)), CStr(comandos(8)))
            If error Then
                Call log("Error imprimir_item: " & CStr(error))
            Else
                Call log("Imprimir Item OK")
            End If
            
        ' Cargar el pago
        ElseIf cmd = "cargar_pago" Then
            ' 1 Int Codigo de forma de pago (3=Cheque, 6=Cuenta Corriente, 8=Efectivo, 20=Tarj Credito, 21=Tarj Debito
            ' 2 String monto (999999999.99)
            error = cargar_pago(CInt(comandos(1)), CStr(comandos(2)))
            If error Then
                Call log("Error cargar_pago: " & CStr(error))
            Else
                Call log("Cargar Pago OK")
            End If
            
        ' Cargar el descuento
        ElseIf cmd = "cargar_descuento" Then
            ' 1 String monto (999999999.99)
            ' 2 id_tipo_iva
            error = cargar_descuento(CStr(comandos(1)), CInt(comandos(2)))
            If error Then
                Call log("Error cargar_descuento: " & CStr(error))
            Else
                Call log("Cargar Descuento OK")
            End If
            
        ' Cerramos el comprobante
        ElseIf cmd = "cerrar_comprobante" Then
            error = cerrar_comprobante()
            If error Then
                Call log("Error cerrar_comprobante: " & CStr(error))
            Else
                Call log("Cerrar Comprobante OK")
            End If
            
        ' Consultamos el ultimo numero de un determinado tipo de comprobante
        ElseIf cmd = "ultimo_comprobante" Then
            Call ultimo_comprobante(CStr(comandos(1)))
            
        End If
    Loop
    Close #1
    Call DetenerLog
    Call log("Termina EXE")
    Unload Me
    
End Sub

Private Sub Form_Unload(Cancel As Integer)
    Dim Form As VB.Form
    For Each Form In VB.Forms
        Unload Form
    Next
End Sub
