<script type="text/template" id="cuentas_corrientes_proveedores_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
    <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-user icono_principal"></i>Proveedores</h1>
  </div>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">

    <?php $active = "cuentas_corrientes_proveedores"; include("cli/proveedores_menu.php"); ?>

    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-9 sm-m-b">
          <div style="display: inline-block">
            <div class="input-group" style="width: 250px;">
              <input type="text" class="form-control" id="cuentas_corrientes_codigo_proveedor" autocomplete="off" placeholder="Nombre o codigo de proveedor" value="<%= id_proveedor %>"/>
              <?php /*
              <span class="input-group-btn">
                <button title="Atajo: F2 = Buscar" id="cuentas_corrientes_proveedores_buscar_proveedor" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
              </span>
              */ ?>
            </div>
          </div>
          <% if (control.check("almacenes")>0 || MEGASHOP == 1 || ID_EMPRESA == 224) { %>
            <div style="display: inline-block">
              <div class="input-group" style="width: 150px;">
                <select class="form-control" id="cuentas_corrientes_sucursales">
                  <% if (ID_SUCURSAL != 0) { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <% if (ID_SUCURSAL == o.id) { %>
                        <option selected value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    <% } %>                    
                  <% } else { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <option <%= (o.id == id_sucursal)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  <% } %>
                </select>
              </div>
            </div>
          <% } %>
          <div style="display: inline-block">
            <div class="input-group" style="width: 150px;">
              <input type="text" class="form-control" id="cuentas_corrientes_proveedores_fecha_desde" autocomplete="off" placeholder="Desde">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
          </div>
          <div style="display: inline-block">
            <div class="input-group" style="width: 150px;">
              <input type="text" class="form-control" id="cuentas_corrientes_proveedores_fecha_hasta" autocomplete="off" placeholder="Hasta">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
          </div>
          <div style="display: inline-block">
            <div class="input-group">
              <button id="cuentas_corrientes_proveedores_buscar" class="buscar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
            </div>
          </div>
        </div>
        <div class="col-md-3 text-right">               
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void(0)" class="imprimir">Imprimir</a></li>
              <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
              <li class="divider"></li>
              <li><a onclick="workspace.cambiar_estado()" href="javascript:void(0)">Modo supervisor</a></li>
            </ul>
          </div>
          <% if (control.check("cuentas_corrientes_proveedores")>1) { %>
            <a class="btn btn-info btn-addon ml5 agregar_orden_pago" href="javascript:void(0)">
              <i class="fa fa-plus"></i>
              <span class="hidden-xs">Agregar Pago</span>
            </a>
          <% } %>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <p class="h3" id="cuentas_corrientes_proveedores_datos_nombre">Cuenta de Proveedor</p>
      <div style="color:#58666e" class="m-t m-b">
        <i class="fa fa-home text-muted m-r-xs"></i><b>Direccion:</b> <span id="cuentas_corrientes_proveedores_datos_direccion" class="m-r-lg"></span>
        <i class="fa fa-phone text-muted m-r-xs"></i><b>Telefono:</b> <span id="cuentas_corrientes_proveedores_datos_telefono" class="m-r-lg"></span>
        <i class="fa fa-envelope text-muted m-r-xs"></i><b>Email:</b> <a id="cuentas_corrientes_proveedores_datos_email" class="m-r-lg text-primary dker"></a>
        <b>IVA:</b> <span id="cuentas_corrientes_proveedores_datos_iva" class="m-r-lg"></span>
        <b>CUIT:</b> <span id="cuentas_corrientes_proveedores_datos_cuit" class="m-r-lg"></span>
        <div style="font-style: italic;" id="cuentas_corrientes_proveedores_datos_observaciones"></div>
      </div>
      <div class="b-a table-responsive">
        <table id="cuentas_corrientes_proveedores_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <th style="width:20px;">
              <label class="i-checks m-b-none">
                <input class="esc sel_todos" type="checkbox"><i></i>
              </label>
            </th>                      
            <th>Proveedor</th>
            <th class="w25"></th>
            <th class="w120">Fecha</th>
            <th>Comprobante</th>
            <th>Numero</th>
            <th class="tar">Debe</th>
            <th class="tar">Haber</th>
            <th class="tar">Saldo</th>
            <th class="tar"></th>
            <th class="w25"></th>
            <% if (control.check("cuentas_corrientes_proveedores")>1) { %>
              <th class="w25"></th>
            <% } %>
          </thead>
          <tbody id="cuentas_corrientes_proveedores_tbody" class="tbody">
            <tr><td colspan="20">Seleccione un proveedor</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="cuentas_corrientes_proveedores_item_resultados_template">
	<% var clase = (pagada==0 && progreso < 100 && tipo_comprobante != "Orden de Pago") ? "text-danger" : "";
	clase = (compra_real==0) ? "fila_azul" : clase;
	%>
	<td class="<%= clase %>">
		<% if (mostrar_checkbox) { %>
      <label class="i-checks m-b-none">
        <input type="checkbox" class="checkbox" id="check<%= id %>"/><i></i>
      </label>
    <% } %>
	</td>
  <td class="<%= clase %>"><%= proveedor %></td>
  <td>
    <input type="hidden" class="observaciones" value="<%= observaciones %>"/>
    <% if (!isEmpty(observaciones)) { %>
      <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-commenting text-warning"></i>
    <% } %>
    <% if (pendiente == 1) { %><span class="label bg-warning">Pendiente</span><% } %>
  </td>
	<td class="<%= clase %>"><%= fecha %></td>
	<td class="<%= clase %>"><%= tipo_comprobante %></td>
	<td class="<%= clase %>">
    <span class="numero_comprobante"><%= numero %></span>
    <% if (efectivo > 0) { %><i data-toggle="tooltip" title="Pago efectivo" class="fa fa-money m-l-xs text-success"></i><% } %>
  </td>
	<td class="tar <%= clase %>"><%= Number(pago).toFixed(2) %></td>
	<td class="tar <%= clase %>"><%= Number(monto).toFixed(2) %></td>
	<td class="tar <%= clase %>"><%= Number(saldo).toFixed(2) %></td>
  <td>
    <% if (id_proveedor != 0 && id_tipo_comprobante > 0) { %>
    <div class="progress mb0">
      <div class="progress-bar" role="progressbar" style="width:<%= Number(progreso).toFixed(2) %>%"><%= Number(progreso).toFixed(0) %>%</div>
    </div>
    <% } %>
  </td>  
	<td class="<%= clase %>">
		<% if (!isEmpty(tipo_comprobante)) { %>
      <i class="fa fa-file-text-o edit text-dark" />
		<% } %>
	</td>
  <% if (control.check("cuentas_corrientes_proveedores")>1) { %>
  	<td class="<%= clase %>">
  		<% if (!isEmpty(tipo_comprobante)) { %>
        <i class="glyphicon glyphicon-remove delete text-danger" />
  		<% } %>
  	</td>
  <% } %>
</script>


<script type="text/template" id="orden_pago_proveedores_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    &Oacute;rden de Pago
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body pb0">
    <% if (id != undefined) { %>
      <h3 class="mt0"><%= proveedor %></h3>
    <% } %>
    <div class="form-inline m-b">
      <% if (mostrar_fecha == 1) { %>
        <label class="control-label">Fecha de Pago</label>
        <div class="ml5" style="width: 150px; display: inline-block">
          <div class="input-group">
            <input <%= (id!=undefined)?"disabled":"" %> type="text" class="w100p form-control" id="orden_pago_proveedores_fecha"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      <% } %>
      <% if (mostrar_numero == 1) { %>
        <label class="control-label m-l">Nro. de Orden</label>
        <div class="ml5" style="width: 100px; display: inline-block">
          <input type="text" <%= (id!=undefined)?"disabled":""%> class="w100p form-control" id="orden_pago_proveedores_numero" value="<%= numero_2 %>"/>
        </div>
      <% } %>
    </div>
      
    <% if (mostrar_comprobantes == 1) { %>
      <h4>Comprobantes incluidos:</h4>
      <div class="b-a m-b">
        <table class="table table-small sortable m-b-none default footable" style="overflow:auto; height:100px;">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Comprobante</th>
              <th style="width: 150px">Numero</th>
              <th class="tar">Neto</th>
              <th class="tar">IVA</th>
              <th class="tar">Total</th>
              <th class="tar">Saldo</th>
            </tr>
          </thead>
          <tbody id="orden_pago_comprobantes" class="tbody"></tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="bold fs14">TOTALES</td>
              <% if (id == undefined) { %>
                <td class="tar bold fs14" id="orden_pago_total_debe"></td>
                <td class="tar bold fs14" id="orden_pago_total_haber"></td>
                <td class="tar bold fs14" id=""></td>
                <td class="tar bold fs14" id="orden_pago_total_gral"></td>
              <% } else { %>
                <td class="tar bold fs14" id=""></td>
                <td class="tar bold fs14" id=""></td>
                <td class="tar bold fs14" id=""></td>
                <td class="tar bold fs14" id="orden_pago_total_gral"></td>
              <% } %>
            </tr>
          </tfoot>
        </table>
      </div>
    <% } %>

    <h4>Formas de Pago:</h4>
      
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <% if (mostrar_efectivo == 1) { %>
          <li class="active">
            <a href="#tab_orden_pago_1" role="tab" data-toggle="tab"><i class="fa fa-dollar"></i>Efectivo</a>
          </li>          
        <% } %>
        <% if (mostrar_depositos == 1) { %>
          <li>
            <a href="#tab_orden_pago_2" role="tab" data-toggle="tab"><i class="fa fa-exchange"></i>Depositos / Transf.</a>
          </li>
        <% } %>
        <% if (mostrar_cheques == 1) { %>
          <li>
            <a href="#tab_orden_pago_5" role="tab" data-toggle="tab"><i class="fa fa-bank"></i>Cheques</a>
          </li>
        <% } %>
        <li>
          <a href="#tab_orden_pago_7" role="tab" data-toggle="tab"><i class="fa fa-book"></i>Otros</a>
        </li>
        <li>
          <a href="#tab_orden_pago_10" role="tab" data-toggle="tab"><i class="fa fa-file-text-o"></i>Observaciones</a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="tab_orden_pago_1" class="tab-pane active">

          <% if (id == undefined) { %>
            <div class="clearfix m-b">
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Caja</label>
                <select class="form-control" id="orden_pago_movimientos_efectivo_cajas">
                  <% for(var i=0;i< window.cajas.length;i++) { %>
                    <% var c = window.cajas[i] %>
                    <% if (c.tipo == 0 && c.activo == 1) { %>
                      <option value="<%= c.id %>"><%= c.nombre %></option>
                    <% } %>
                  <% } %>
                </select>
              </div>
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Importe</label>
                  <div class="input-group">
                    <input type="text" class="w100p form-control" id="orden_pago_movimientos_efectivo_monto" value="0"/>
                    <span class="input-group-btn">
                      <button id="orden_pago_movimientos_efectivo_agregar" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
              </div>
            </div>
          <% } %>

          <div class="b-a table-responsive">
            <table class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th>Caja</th>
                  <th>Adjunto</th>
                  <th class="tar">Monto</th>
                  <th class="w25"></th>
                </tr>
              </thead>
              <tbody class="tbody" id="orden_pago_movimientos_efectivo_table"></tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="bold">Total Efectivo</td>
                  <td class="tar bold" id="orden_pago_movimientos_efectivo_total">$ 0.00</td>
                  <td>&nbsp;</td>
                </tr>
              </tfoot>
            </table>            
          </div>
        </div>
        <div id="tab_orden_pago_2" class="tab-pane">
          <% if (id == undefined) { %>
            <div class="clearfix m-b">
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Cuenta</label>
                <select class="form-control" id="orden_pago_depositos_cajas">
                  <% for(var i=0;i< window.cajas.length;i++) { %>
                    <% var c = window.cajas[i] %>
                    <% if (c.tipo == 1 && c.activo == 1) { %>
                      <option value="<%= c.id %>"><%= c.nombre %></option>
                    <% } %>
                  <% } %>
                </select>
              </div>
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Importe</label>
                <div class="input-group">
                  <input type="text" class="w100p form-control" id="orden_pago_depositos_monto" value="0"/>
                  <span class="input-group-btn">
                    <button id="orden_pago_depositos_agregar" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>
          <% } %>
          <div class="b-a table-responsive">
            <table class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th>Caja</th>
                  <th>Adjunto</th>
                  <th class="tar">Monto</th>
                  <th class="w25"></th>
                </tr>
              </thead>
              <tbody class="tbody" id="orden_pago_depositos_table"></tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="bold">Total Depositos</td>
                  <td class="tar bold" id="orden_pago_depositos_total">$ 0.00</td>
                  <td>&nbsp;</td>
                </tr>
              </tfoot>
            </table>            
          </div>
        </div>
        <div id="tab_orden_pago_5" class="tab-pane">
          <% if (id == undefined) { %>
            <div class="clearfix m-b">
              <button class="btn btn-sm btn-addon btn-default" id="orden_pago_cheques_terceros"><i class="fa fa-search"></i> Buscar cheque de tercero</button>
            </div>
            <div class="clearfix m-b">
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Tipo</label>
                <select id="orden_pago_cheques_tipo" class="form-control action no-model">
                  <option value="P">Propio</option>
                  <option value="T">Tercero</option>
                </select>
              </div>  
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Banco</label>
                <select id="orden_pago_cheques_bancos" class="form-control no-model">
                  <option value="0">Banco</option>
                  <% for(var i=0;i<bancos.length;i++) { %>
                    <% var banco = bancos[i] %>
                    <option value="<%= banco.id %>"><%= banco.nombre %></option>
                  <% } %>
                </select>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">F. Emision</label>
                <input type="text" class="form-control action no-model" id="orden_pago_cheques_fecha_emision"/>
              </div>                  
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">F. Cobro</label>
                <input type="text" class="form-control action no-model" id="orden_pago_cheques_fecha_cobro"/>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Numero</label>
                <input type="text" class="form-control action no-model" id="orden_pago_cheques_numero"/>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Importe</label>
                <div class="input-group">
                  <input type="text" class="form-control no-model" id="orden_pago_cheques_monto"/>
                  <span class="input-group-btn">
                    <button title="Ingresar linea" id="orden_pago_cheques_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>
          <% } %>
          <div class="b-a table-responsive">
            <table class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th>Emisor</th>
                  <th>Banco</th>
                  <th>N&uacute;mero</th>
                  <th>Fecha Pago</th>
                  <th class="tar">Monto</th>
                  <th></th>
                </tr>
              </thead>
              <tbody class="tbody" id="orden_pago_cheques_table"></tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="bold">Total Cheques</td>
                  <td class="tar bold" id="orden_pago_cheque_total">$ 0.00</td>
                  <td>&nbsp;</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div id="tab_orden_pago_7" class="tab-pane">
          <div class="clearfix m-b">
            <div class="row">
              <?php /*
              <div class="col-md-3 col-sm-6">
                <label class="text-muted">Efectivo</label>
                <input type="text" <%= (id!=undefined)?"disabled":"" %> class="tar input bold form-control" id="orden_pago_efectivo" value="<%= efectivo %>"/>
              </div>
              */ ?>
              <% if (RETIENE_IB == 1) { %>
                <div class="col-md-3 col-sm-6">
                  <label class="text-muted">Retencion IB</label>
                  <div class="input-group">
                    <input type="text"  <%= (id!=undefined)?"disabled":"" %> class="form-control w50p tar" id="orden_pago_porc_ret_ib" value="<%= Number(porc_ret_ib).toFixed(2) %>"/>
                    <input disabled type="text" class="form-control w50p tar" id="orden_pago_retencion_ib" value="<%= Number(ret_ing_brutos).toFixed(2) %>"/>
                  </div>
                </div>
              <% } %>
              <% if (RETIENE_GANANCIAS == 1) { %>
                <div class="col-md-3 col-sm-6">
                  <label class="text-muted">Ret. Ganancias</label>
                  <input id="orden_pago_ret_ganancias" <%= (id!=undefined)?"disabled":"" %> type="text" class="form-control tar" value="<%= Number(ret_ganancias).toFixed(2) %>"/>
                </div>
              <% } %>                     
              <div class="col-md-3 col-sm-6">
                <label class="text-muted">Dto. / Devoluci&oacute;n</label>
                <input type="text" <%= (id!=undefined)?"disabled":"" %> class="form-control tar" id="orden_pago_descuento" value="<%= descuento %>"/>
              </div>
            </div>
          </div>
        </div>

        <div id="tab_orden_pago_10" class="tab-pane">
          <div class="form-group">
            <textarea placeholder="Escribe aqui alguna nota o comentario sobre el pago..." <%= (id != undefined)?'disabled':'' %> name="observaciones" id="orden_pago_observaciones" class="form-control h100"><%= observaciones %></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <div class="row">
      <div class="col-md-3 col-xs-6">
        <label class="control-label bold">TOTAL A PAGAR</label>
        <input type="text" class="form-control bold fs16 tar" disabled id="orden_pago_total_pagar"/>
      </div>
      <div class="col-md-3 col-xs-6">
        <label class="control-label">Valores Entregados</label>
        <input type="text" class="form-control bold fs16 tar" disabled id="orden_pago_total_valores_entregados"/>
      </div>
      <div class="col-md-3 col-xs-6">
        <label class="control-label">Diferencia</label>
        <input type="text" class="form-control bold fs16 tar" disabled id="orden_pago_total_diferencia"/>
      </div>
      <div class="col-md-3 col-xs-6">
        <label class="control-label"></label>
        <div class="m-t-xs tar">
          <% if (id != undefined) { %>
            <div class="btn-group dropdown">
              <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Imprimir <span class="caret"></span></button>
              <ul class="dropdown-menu" role="menu">
                <li><a class="imprimir_orden_pago">Orden de Pago</a></li>
                <li><a class="imprimir_ret_ib_simple">Ing. Brutos Simple</a></li>
                <li><a class="imprimir_ret_ib">Ing. Brutos Doble</a></li>
                <li><a class="imprimir_ret_ganancias_simple">Ganancias Simple</a></li>
                <li><a class="imprimir_ret_ganancias">Ganancias Doble</a></li>
              </ul>
            </div>                        
          <% } else { %>
            <button class="btn btn-success guardar bold">Guardar</button>
          <% } %>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="cuentas_corrientes_notas_template">
	<div class="panel">
		<div class="subtitulo mb5">Notas: </div>
		<div>
			<textarea style="float: left; width:677px; height: 50px" class="input" id="orden_pago_notas"><%= notas %></textarea>
			<button class="button verde guardar fr">Guardar</button>
		</div>
	</div>
</script>

<script type="text/template" id="orden_pago_observaciones_template">
	<div class="titulo">Observaciones</div>
	<div class="row">
		<textarea style="width:350px; height: 100px" class="input" id="orden_pago_observaciones"><%= observaciones %></textarea>
	</div>
	<div class="row">
		<button class="button verde guardar fr">Guardar</button>
	</div>
</script>

<script type="text/template" id="cuentas_corrientes_proveedores_item_orden_pago_template">
	<td style="width:77px"><%= fecha %></td>
	<td style="width:108px"><%= tipo_comprobante %></td>
	<td style="width:108px"><%= numero %></td>
	<td class="tar" style="width:90px"><%= Number(neto).toFixed(2) %></td>
	<td class="tar" style="width:90px"><%= Number(iva).toFixed(2) %></td>
	<td class="tar" style="width:100px"><%= Number(total).toFixed(2) %></td>
  <td class="tar" style="width:130px"><input type="text" class="form-control no-model tar input_saldo" <%= (id_orden_pago != 0 || por_cancelar < 0)?"disabled":"" %> value="<%= Number(por_cancelar).toFixed(2) %>" data-min="0" data-max="<%= Number(resto).toFixed(2) %>" /></td>
</script>

<script type="text/template" id="cuentas_corrientes_proveedores_item_cheques_orden_pago_template">
  <td><%= titular %></td>
	<td><%= banco %></td>
	<td><%= numero %></td>
	<td class="tar"><%= fecha_cobro %></td>
	<td class="tar"><%= Number(monto).toFixed(2) %></td>
  <td class="col_chica">
    <% if (edicion) { %>
      <i class="glyphicon glyphicon-remove eliminar text-danger"/>
    <% } %>
  </td>
</script>



<script type="text/template" id="ordenes_pago_listado_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-shopping-cart icono_principal"></i>Compras</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <?php $active = "ordenes_pago"; include("compras/compras_menu.php"); ?>

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-4 sm-m-b">
            <div class="input-group">
              <input type="text" id="ordenes_pago_listado_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
              </span>

              <?php /*
              <span class="input-group-btn">
                <div class="btn-group dropdown ml5">
                  <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
                    <li><a href="javascript:void" class="exportar_csv">Exportar CSV</a></li>
                  </ul>
                </div>
              </span>

              <span class="input-group-btn">
                <div class="btn-group dropdown ml5">
                  <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-download"></i><span><?php echo lang(array("es"=>"Importar","en"=>"Import")); ?></span>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void" class="importar">Importar Excel</a></li>
                    <li><a href="javascript:void" class="importar_csv">Importar CSV</a></li>
                  </ul>
                </div>
              </span>
              */ ?>

            </div>
          </div>      

        </div>
      </div>
      <div class="advanced-search-div bg-light dk">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
          <div class="row pl10 pr10">

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" placeholder="Desde" autocomplete="off" id="ordenes_pago_desde" class="form-control">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" placeholder="Hasta" autocomplete="off" id="ordenes_pago_hasta" class="form-control">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>

            <% if (window.almacenes.length > 0) { %>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select class="form-control" id="ordenes_pago_sucursales">
                    <% if (ID_SUCURSAL != 0) { %>
                      <% for(var i=0;i< window.almacenes.length;i++) { %>
                        <% var o = almacenes[i]; %>
                        <% if (ID_SUCURSAL == o.id) { %>
                          <option selected value="<%= o.id %>"><%= o.nombre %></option>
                        <% } %>
                      <% } %>                    
                    <% } else { %>
                      <option value="0">Sucursal</option>
                      <% for(var i=0;i< window.almacenes.length;i++) { %>
                        <% var o = almacenes[i]; %>
                        <option value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    <% } %>
                  </select>   
                </div>
              </div>
            <% } %>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <button class="buscar btn btn-block btn-dark btn-default"><i class="fa fa-search"></i> Buscar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php /*
    <% if (!seleccionar) { %>
      <div class="bulk_action wrapper pb0">
      <button class="btn btn-default enviar btn-addon"><i class="icon fa fa-send"></i>Enviar por email</button>
      </div>
    <% } %>
    */ ?>
    <div class="panel-body resumen pb0" style="display:none">
      <div class="row">
        <div class="col-md-2">
          <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
            <div id="ordenes_pago_resumen_total" class="h3 font-thin text-white block">0</div>
            <span class="text-muted text-md pt5 db">Total</span>
          </div>
        </div>
        <div class="col-md-2">
          <div class="block tac panel padder-v item mb0" style="height: 80px">
            <div id="ordenes_pago_resumen_total_efectivo" class="h3 font-thin block">0</div>
            <span class="text-muted text-md pt5 db">Efectivo</span>
          </div>
        </div>
        <div class="col-md-2">
          <div class="block tac panel padder-v item mb0" style="height: 80px">
            <div id="ordenes_pago_resumen_total_cheques" class="h3 font-thin block">0</div>
            <span class="text-muted text-md pt5 db">Cheques</span>
          </div>
        </div>
        <div class="col-md-2">
          <div class="block tac panel padder-v item mb0" style="height: 80px">
            <div id="ordenes_pago_resumen_total_depositos" class="h3 font-thin block">0</div>
            <span class="text-muted text-md pt5 db">Depositos</span>
          </div>
        </div>
        <div class="col-md-2">
          <div class="block tac panel padder-v item mb0" style="height: 80px">
            <div id="ordenes_pago_resumen_total_otros" class="h3 font-thin block">0</div>
            <span class="text-muted text-md pt5 db">Otros</span>
          </div>
        </div>
        <div class="col-md-2">
          <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
            <span id="ordenes_pago_resumen_cantidad" class="font-thin h3 block">0</span>
            <span class="text-muted text-md pt5 db">Cantidad</span>
          </div>
        </div>
      </div>
    </div>


    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="ordenes_pago_tabla" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;">
                <label class="i-checks m-b-none">
                  <input class="esc sel_todos" type="checkbox"><i></i>
                </label>
              </th>
              <% if (control.check("almacenes")>0) { %>
                <th>Sucursal</th>
              <% } %>
              <th class="w150">Fecha</th>
              <th>Proveedor</th>
              <th class="sorting w150" data-sort-by="numero_2">Comprobante</th>
              <th class="tar">Efectivo</th>
              <th class="tar">Cheques</th>
              <th class="tar">Transf.</th>
              <th class="tar">Otros</th>
              <th class="tar">Total</th>
              <% if (!seleccionar) { %>
                <th class="th_acciones w25"></th>
              <% } %>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>        
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="ordenes_pago_item_resultados_template">
  <% var clase = "edit"; %>
  <td>
    <label class="i-checks m-b-none">
      <input type="checkbox" class="checkbox" id="check<%= id %>"/><i></i>
    </label>
  </td>
  <% if (control.check("almacenes")>0) { %>
    <td class="<%= clase %>"><%= sucursal %></td>
  <% } %>
  <td class="<%= clase %>"><%= fecha %></td>
  <td class="<%= clase %>"><span class="text-info"><%= proveedor %></span>
    <% if (!isEmpty(observaciones)) { %>
      <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-commenting text-warning"></i>
    <% } %>
  </td>
  <td class="<%= clase %>">OP <%= numero_2 %></td>
  <% efectivo = Number(efectivo) %>
  <% total_cheques = Number(total_cheques) %>
  <% total_depositos = Number(total_depositos) %>
  <% descuento = Number(descuento) %>
  <% total = efectivo + total_cheques + total_depositos + descuento %>
  <td class="<%= clase %> tar"><span class="tag_precio">$ <%= Number(efectivo).format() %></span></td>
  <td class="<%= clase %> tar"><span class="tag_precio">$ <%= Number(total_cheques).format() %></span></td>
  <td class="<%= clase %> tar"><span class="tag_precio">$ <%= Number(total_depositos).format() %></span></td>
  <td class="<%= clase %> tar"><span class="tag_precio">$ <%= Number(descuento).format() %></span></td>
  <td class="<%= clase %> tar"><span class="tag_precio">$ <%= Number(total).format() %></span></td>
  <td class="p5 td_acciones">
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>    
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="ver_cta_cte">Ver Cta. Cte.</a></li>
      </ul>
    </div>  
  </td>
</script>