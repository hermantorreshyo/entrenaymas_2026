<script type="text/template" id="configuracion_facturacion_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n / 
  	<b>Facturaci&oacute;n</b>
	</h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div id="configuracion_facturacion_comprobantes" class="row">
      <div class="col-md-10 col-md-offset-1">


        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Datos Impositivos</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Numero de Ingresos Brutos, fecha de inicio de actividades, etc.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                   <div class="form-group">
                    <label class="control-label">Numero Ing. Brutos </label>
                    <input type="text" name="numero_ib" class="form-control" value="<%= numero_ib %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha de Inicio de Actividades </label>
                    <div class="input-group">
                      <input type="text" id="facturacion_fecha_inicio" name="fecha_inicio" class="form-control" value="<%= fecha_inicio %>"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="empresas_percibe_ib" name="percibe_ib" class="checkbox" value="1" <%= (percibe_ib == 1)?"checked":"" %> >
                  <i></i>
                  Percibe Ingresos Brutos?
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="empresas_retiene_ib" name="retiene_ib" class="checkbox" value="1" <%= (retiene_ib == 1)?"checked":"" %> >
                  <i></i>
                  Retiene Ingresos Brutos?
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="empresas_retiene_ganancias" name="retiene_ganancias" class="checkbox" value="1" <%= (retiene_ganancias == 1)?"checked":"" %> >
                  <i></i>
                  Retiene Imp. a las Ganancias?
                </label>
              </div>              
            </div>
          </div>
        </div>        

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Monedas</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Configure la cotizacion de las distintas monedas.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_mostrar_dolar" name="facturacion_mostrar_dolar" class="checkbox" value="1" <%= (facturacion_mostrar_dolar == 1)?"checked":"" %> >
                  <i></i>
                  Mostrar cotizaci&oacute;n del dolar
                </label>
              </div>
              <div class="form-group">
                <label class="control-label">Cotizacion Dolar (USD)</label>
                <input type="text" name="cotizacion_dolar" class="form-control" value="<%= cotizacion_dolar %>"/>
                <span class="text-muted">Dejar en 0 para tomar la cotización automática del Banco Nacion.</span>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Formulario de Facturacion</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Configure las distintas opciones del formulario de facturacion.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Tipo de facturaci&oacute;n</label>
                <select name="facturacion_tipo" class="form-control">
                  <option value="" <%= (facturacion_tipo=="")?"selected":"" %>>Tipo comprobante</option>
                  <option value="fm" <%= (facturacion_tipo=="fm")?"selected":"" %>>Formulario compacto</option>
                  <option value="pv" <%= (facturacion_tipo=="pv")?"selected":"" %>>Punto de Venta</option>
                </select>
              </div>

              <div class="form-group">
                <label class="control-label">Tipo de conexi&oacute;n con controlador fiscal</label>
                <select name="facturacion_imprimir_item_al_final" class="form-control">
                  <option value="0" <%= (facturacion_imprimir_item_al_final=="0")?"selected":"" %>>Enviar items a medida que se va vendiendo</option>
                  <option value="1" <%= (facturacion_imprimir_item_al_final=="1")?"selected":"" %>>Enviar ticket completo al finalizar</option>
                </select>
              </div>

              <div class="form-group">
                <label class="control-label tal">Forma de Pago</label>
                <select class="form-control" id="facturacion_forma_pago" name="facturacion_forma_pago">
                  <option value="C" <%= (facturacion_forma_pago == "C") ? "selected": "" %>>Elegir forma de pago en el mismo formulario.</option>
                  <option value="M" <%= (facturacion_forma_pago == "M") ? "selected": "" %>>Abrir menu de opciones de pago al aceptar comprobante.</option>
                </select>
              </div>

              <div class="form-group">
                <label class="control-label m-r">C&oacute;digo de articulo usado para finalizar el comprobante</label>
                <input type="text" class="form-control" name="facturacion_codigo_finalizar" value="<%= facturacion_codigo_finalizar %>" />
              </div>

              <div class="form-group">
                <label class="control-label">Luego de guardar un comprobante</label>
                <select class="form-control" name="facturacion_conservar_cliente_al_guardar">
                  <option value="1" <%= (facturacion_conservar_cliente_al_guardar == 1)?"selected":"" %>>Conserver el mismo cliente</option>
                  <option value="0" <%= (facturacion_conservar_cliente_al_guardar == 0)?"selected":"" %>>Limpiar el cliente</option>
                </select>
              </div>

              <div class="form-group">
                <label class="control-label">Contrase&ntilde;a Supervisor </label>
                <input type="password" name="supervisor" class="form-control" value="<%= supervisor %>"/>
              </div>

              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_remitos_tomar_precio_neto" name="remitos_tomar_precio_neto" class="checkbox" value="1" <%= (remitos_tomar_precio_neto == 1)?"checked":"" %> >
                  <i></i>
                  Utilizar precio neto en los remitos
                </label>
              </div>

              <div class="form-group">
                <label class="control-label">Dise&ntilde;o de comprobantes </label>
                <select name="disenio_factura" class="form-control" id="disenio_factura">
                  <option value="" <%= (disenio_factura == "") ? "selected": "" %>>Seleccione</option>
                  <option value="basico" <%= (disenio_factura == "basico") ? "selected": "" %>>Basico</option>
                  <option value="bolsas" <%= (disenio_factura == "bolsas") ? "selected": "" %>>Basico 2</option>
                  <option value="modelo1" <%= (disenio_factura == "modelo1") ? "selected": "" %>>Moderno</option>
                  <option value="distribuidora" <%= (disenio_factura == "distribuidora") ? "selected": "" %>>Distribuidora</option>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label">Cantidad de Copias </label>
                <select name="facturacion_cantidad_copias" class="form-control" id="facturacion_cantidad_copias">
                  <option value="1" <%= (facturacion_cantidad_copias == "1") ? "selected":"" %>>Original</option>
                  <option value="2" <%= (facturacion_cantidad_copias == "2") ? "selected":"" %>>Duplicado</option>
                  <option value="3" <%= (facturacion_cantidad_copias == "3") ? "selected":"" %>>Triplicado</option>
                  <option value="4" <%= (facturacion_cantidad_copias == "4") ? "selected":"" %>>Cuadruplicado</option>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label">Limite de items </label>
                <input type="text" name="facturacion_cantidad_items" value="<%= facturacion_cantidad_items %>" class="form-control"/>
              </div>

              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_mostrar_logo_en_comprobante" name="facturacion_mostrar_logo_en_comprobante" class="checkbox" value="1" <%= (facturacion_mostrar_logo_en_comprobante == 1)?"checked":"" %> >
                  <i></i>
                  Mostrar logo de la empresa en el comprobante
                </label>
              </div>
              <div class="form-group">
                <label class="control-label">Observaciones por defecto </label>
                <textarea style="height: 150px; " class="form-control" name="observaciones" placeholder="Escriba aqui las notas de pie de p&aacute;gina que desea que aparezcan por defecto en todos sus comprobantes"><%= observaciones %></textarea>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_usa_cache_articulos" name="facturacion_usa_cache_articulos" class="checkbox" value="1" <%= (facturacion_usa_cache_articulos == 1)?"checked":"" %> >
                  <i></i>
                  Habilitar cache de articulos.
                </label>
              </div>

              <div class="form-group">
                <div class="form-group">
                  M&eacute;todos de pago aceptados:
                </div>
                <div class="clearfix">
                  <label class="i-checks fl">
                    <input type="checkbox" id="facturacion_usa_creditos_personales" name="facturacion_usa_creditos_personales" class="checkbox" value="1" <%= (facturacion_usa_creditos_personales == 1)?"checked":"" %> >
                    <i></i>
                    Credito Personal
                  </label>
                  <?php // TODO: No es ocultar_cuenta_corriente sino USA_CUENTA_CORRIENTE, pero para no cambiar el nombre del campo lo deje asi ?>
                  <label class="i-checks fl m-l">
                    <input type="checkbox" id="facturacion_ocultar_cuenta_corriente" name="facturacion_ocultar_cuenta_corriente" class="checkbox" value="1" <%= (facturacion_ocultar_cuenta_corriente == 1)?"checked":"" %> >
                    <i></i>
                    Cuenta Corriente
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_permitir_total_negativo" name="facturacion_permitir_total_negativo" class="checkbox" value="1" <%= (facturacion_permitir_total_negativo == 1)?"checked":"" %> >
                  <i></i>
                  Permitir devoluciones de dinero (habilita para guardar el comprobante en negativo).
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_guardar_leido" name="facturacion_guardar_leido" class="checkbox" value="1" <%= (facturacion_guardar_leido == 1)?"checked":"" %> >
                  <i></i>
                  Guardar el c&oacute;digo leido por el lector.
                </label>
              </div>

              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_abrir_dialogo_imprimir" name="facturacion_abrir_dialogo_imprimir" class="checkbox" value="1" <%= (facturacion_abrir_dialogo_imprimir == 1)?"checked":"" %> >
                  <i></i>
                  Luego de guardar el comprobante, abrir el dialogo de impresion automaticamente.
                </label>
              </div>

              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_crear_cliente" name="facturacion_crear_cliente" class="checkbox" value="1" <%= (facturacion_crear_cliente == 1)?"checked":"" %> >
                  <i></i>
                  Sugerir la creaci&oacute;n de un nuevo cliente cuando no se encuentran resultados.
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_modificar_precio" name="facturacion_modificar_precio" class="checkbox" value="1" <%= (facturacion_modificar_precio == 1)?"checked":"" %> >
                  <i></i>
                  Editar el precio de un articulo
                </label>
              </div>

              <% if (typeof facturacion_saltar_precio != "undefined") { %>
                <div class="form-group">
                  <label class="i-checks">
                    <input type="checkbox" id="facturacion_saltar_precio" name="facturacion_saltar_precio" class="checkbox" value="1" <%= (facturacion_saltar_precio == 1)?"checked":"" %> >
                    <i></i>
                    Saltar el precio de un articulo (permite editarlo, pero por defecto lo pasa)
                  </label>
                </div>
              <% } %>


              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_modificar_descripcion" name="facturacion_modificar_descripcion" class="checkbox" value="1" <%= (facturacion_modificar_descripcion == 1)?"checked":"" %> >
                  <i></i>
                  Editar la descripcion de un articulo
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_modificar_item" name="facturacion_modificar_item" class="checkbox" value="1" <%= (facturacion_modificar_item == 1)?"checked":"" %> >
                  <i></i>
                  Modificar un item una vez ingresado
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_mostrar_fecha" name="facturacion_mostrar_fecha" class="checkbox" value="1" <%= (facturacion_mostrar_fecha == 1)?"checked":"" %> >
                  <i></i>
                  Editar la fecha del comprobante
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_mostrar_numero" name="facturacion_mostrar_numero" class="checkbox" value="1" <%= (facturacion_mostrar_numero == 1)?"checked":"" %> >
                  <i></i>
                  Editar el numero de comprobante
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_editar_descuento" name="facturacion_editar_descuento" class="checkbox" value="1" <%= (facturacion_editar_descuento == 1)?"checked":"" %> >
                  <i></i>
                  Editar porcentaje de descuento general.
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_consultar_eliminar_item" name="facturacion_consultar_eliminar_item" class="checkbox" value="1" <%= (facturacion_consultar_eliminar_item == 1)?"checked":"" %> >
                  <i></i>
                  Preguntar antes de eliminar un item del comprobante.
                </label>
              </div>
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_controlar_caja_abierta" name="facturacion_controlar_caja_abierta" class="checkbox" value="1" <%= (facturacion_controlar_caja_abierta == 1)?"checked":"" %> >
                  <i></i>
                  Controlar si la caja esta abierta antes de facturar.
                </label>
              </div>

            </div>
          </div>
        </div>        


        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Balanza</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Parametros relacionados a la impresion de etiquetas en la balanza.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <label class="i-checks">
                  <input type="checkbox" id="facturacion_usa_nplu" name="facturacion_usa_nplu" class="checkbox" value="1" <%= (facturacion_usa_nplu == 1)?"checked":"" %> >
                  <i></i>
                  Utiliza PLU de balanza en codigo de barra
                </label>                        
              </div>
              <div class="form-group">
                <label class="control-label">Cantidad de caracteres de PLU en codigo de barra </label>
                <input type="text" name="facturacion_largo_plu" value="<%= facturacion_largo_plu %>" class="form-control" <%= (facturacion_usa_nplu == 0) ? "disabled" : "" %> id="facturacion_largo_plu"/>
              </div>
              <div class="form-group">
                <label class="control-label">Caracteres de inicio de PLU </label>
                <input type="text" name="facturacion_identificador_plu" value="<%= facturacion_identificador_plu %>" <%= (facturacion_usa_nplu == 0) ? "disabled" : "" %> class="form-control" id="facturacion_identificador_plu"/>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="col-md-10 col-md-offset-1 clearfix">
        <div class="line b-b m-b-lg"></div>
        <button class="btn guardar btn-success pull-right">Guardar</button>
      </div>
    </div>

	</div>
</div>
</script>