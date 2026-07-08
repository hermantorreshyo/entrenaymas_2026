<script type="text/template" id="remito_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
	<div class="row clearfix">
		<div class="col-xs-12 col-sm-6">
			<h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal mr10"></i>Ventas
				/	<b>Remitos</b>
			</h1>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-inline pull-right">
				<div class="btn-group dropdown">
					<button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
					<i class="fa fa-cog"></i><span>Opciones</span>
					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu pull-right">
					<li><a href="javascript:void" class="anular">Nuevo</a></li>
					<li class="divider"></li>
					<li><a href="javascript:void" class="exportar">Importar de remito</a></li>
					<li><a href="javascript:void" class="exportar_csv">Importar resupuesto</a></li>
					<li><a href="javascript:void" class="importar_remito">Importar de factura</a></li>
					<li class="divider"></li>
					<li><a href="javascript:void" class="config">Configuraci&oacute;n</a></li>
					<li class="divider"></li>
					<li><a onclick="workspace.cambiar_estado()" href="javascript:void(0)">Modo supervisor</a></li>
					</ul>
				</div>			 
			</div>
		</div>
	</div>
</div>
<div class="wrapper-md pb0">
	<div class="centrado">
	<div class="panel panel-default pull-in">
		<div class="panel-heading font-bold">
		Datos de Comprobante		 
		</div>
		<div class="panel-body pb0 pl0 pr0">
		<div class="clearfix">
			<div class="form-group col-md-3 col-sm-6">
				<label>Cliente <i title="Click para ayuda" class="buscar_clientes_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></label>
				<div class="input-group">
					<input type="text" class="dn" id="remito_id_cliente" value="<%= id_cliente %>"/>
					<input title="Ingrese el codigo de Cliente o comience a escribir parte del nombre. (0 = Consumidor Final)" type="text" class="form-control action no-model" id="remito_codigo_cliente" placeholder="Nombre o codigo de cliente" value="<%= cliente.nombre %>"/>
					<span class="input-group-btn">
					<button title="Atajo: F2 = Buscar" id="remito_buscar_cliente" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group col-md-3 col-sm-6">
				<label>Forma de Pago</label>
				<select title="Forma de Pago" class="form-control action no-model" name="tipo_pago" id="remito_tipo_pago">
					<option value="C">Cuenta Corriente</option>
					<option value="E">Efectivo</option>
					<option value="T">Tarjeta</option>
				</select>		
			</div>
			<div class="form-group col-md-2 col-sm-6">
				<label>N&uacute;mero</label>
				<input type="number" min="1" name="numero" value="<%= numero %>" class="tar form-control no-model action" id="remito_numero"/>
			</div>			
			<div class="form-group col-md-2 col-sm-6">
				<label>Fecha de Emisi&oacute;n</label>
				<div class="input-group">
				<input type="text" title="Fecha de emision de comprobante" id="remito_fecha" name="fecha" class="form-control no-model action">
				<span class="input-group-btn">
					<button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
				</span>				
				</div>
			</div>
      <% if (control.check("vendedores")>0) { %>
        <div class="form-group col-md-2 col-sm-6">
          <label>Vendedor</label>
          <select class="form-control" id="remito_vendedores">
          <option value="0">-</option>
          <% for(var i=0;i< vendedores.length;i++) { %>
            <% var o = vendedores[i]; %>
            <option value="<%= o.id %>" <%= (o.id==id_vendedor)?"selected":"" %>><%= o.nombre %></option>
          <% } %>
          </select>
        </div>
      <% } %>
			<div class="form-group col-md-3 col-sm-6">
				<label>Direcci&oacute;n</label>
				<input type="text" name="direccion" value="<%= direccion %>" class="form-control no-model" id="remito_direccion"/>
			</div>			 
			<div class="form-group col-md-3 col-sm-6">
				<label>Localidad</label>
				<input type="text" name="localidad" value="<%= localidad %>" class="form-control no-model" id="remito_localidad"/>
			</div>
			<div class="form-group col-md-2 col-sm-6">
				<label>C&oacute;digo Postal</label>
				<input type="text" name="codigo_postal" value="<%= codigo_postal %>" class="form-control no-model" id="remito_codigo_postal"/>
			</div>
			<% if (ID_PROYECTO == 2) { %>
				<div class="col-md-2 col-sm-6">
					<label>Estado</label>
					<select class="form-control action no-model" id="remito_tipo_estado" name="id_tipo_estado">
						<% for(var i=0;i< tipos_estado_pedidos.length;i++) { %>
						<% var c = tipos_estado_pedidos[i]; %>
							<option <%= (id_tipo_estado == c.id)?"selected":"" %> value="<%= c.id %>"><%= c.nombre %></option>
						<% } %>
					</select>			
				</div>
			<% } %>
				<% if (control.check("repartos")>0) { %>
					<div class="form-group col-md-3 col-sm-6">
						<label>Reparto</label>
						<div class="input-group">
						<span class="input-group-btn">
							<input type="text" class="form-control w40" id="remito_reparto" value="<%= reparto %>" name="reparto"/>
						</span>
						<input type="text" id="remito_fecha_reparto" name="fecha_reparto" class="form-control action">
						<span class="input-group-btn">
							<button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
						</span>
						</div>
					</div>
				<% } %>	
		</div>
		</div>
	</div>
	
	<div class="panel panel-info pull-in">
		<div class="panel-heading font-bold">Previsualizaci&oacute;n</div>
		<div class="panel-body preview-container">
		<div class="preview">
			
			<div class="invoice-block">
				<div class="invoice-type">Remito</div>
				<div class="letter">R</div>
			</div>
			<div class="invoice-block">
				<div class="col-md-6 pull-in">
					<div>
						<span class="bold">Fecha de Emisi&oacute;n: </span>
						<span id="remito_fecha_remito"></span>
					</div>
					<div>
						<span class="bold">Condici&oacute;n de Venta: </span>
						<span id="remito_forma_pago_remito"></span>
					</div>
				</div>
				<div class="col-md-6 pull-in">
					<div>
						<div><%= RAZON_SOCIAL %></div>
						<div><%= DIRECCION %> <%= LOCALIDAD %></div>
						<div><%= TIPO_CONTRIBUYENTE %></div>
						<div>CUIT: <%= CUIT %></div>
					</div>
				</div>
			</div>
			<div class="line line-dashed b-b line-lg"></div>

        <select class="dn" disabled id="remito_puntos_venta">
          <% if (id_punto_venta == 0 && id != undefined) { %>
            <option value="0" selected>Punto Venta</option>
          <% } %>
          <?php foreach($puntos_venta as $pv) { ?>
            <% var por_defecto = <?php echo $pv->por_default ?>; %>
            <% if (id == undefined || id == 0) { %>
              <% selected = (por_defecto==1)?"selected":"" %>
            <% } else { %>
              <% selected = (id_punto_venta == <?php echo $pv->id ?>)?"selected":"" %>
            <% } %>
            <option data-tipo_impresion="<?php echo $pv->tipo_impresion ?>" <%= selected %> value="<?php echo $pv->id ?>"><?php echo $pv->numero ?></option>
          <?php } ?>
        </select>

				<div class="invoice-block">
					<div class="col-xs-6 pull-in">
						<div>
							<span class="bold">Cliente: </span>
							<span id="remito_cliente_remito"></span>
						</div>
						<div>
							<span class="bold">Direcci&oacute;n: </span>
							<span id="remito_cliente_direccion"></span>
						</div>
						<div>
							<span class="bold">Localidad: </span>
							<span id="remito_cliente_localidad"></span>
						</div>
					</div>
					<div class="col-xs-6 pull-in">
						<div>
							<span class="bold">Tipo Contribuyente: </span>
							<span id="remito_cliente_iva"></span>
						</div>
						<div>
							<span class="bold">CUIT / DNI: </span>
							<span id="remito_cliente_cuit"></span>
						</div>
					</div>
				</div>
			
				<div class="line line-dashed b-b line-lg"></div>
			
				<input type="hidden" id="remito_id_articulo"/>
        <input type="hidden" class="dn no-model" id="remito_alicuotas_iva"/>
        <input type="hidden" class="dn no-model" id="remito_porc_iva"/>
        <input type="hidden" class="dn no-model" value="0" id="remito_costo_final"/>
				<div class="clearfix">
					<div class="col-md-3 col-sm-6 p0">
						<label class="text-muted">Producto / Servicio</label>
						<input type="text" class="form-control no-model action" id="remito_codigo_articulo"/>
					</div>
					<div class="col-md-2 col-sm-6 p0">
						<label class="text-muted">Cantidad</label>
						<input type="text" class="form-control no-model action" value="1" id="remito_item_cantidad"/>
					</div>
					<div class="col-md-2 col-sm-6 p0">
						<label class="text-muted">Tomar precio de</label>
						<select class="form-control no-model" id="remito_lista">
							<option value="0">Lista 1</option>
							<option value="1">Lista 2</option>
							<option value="2">Lista 3</option>
						</select>									
					</div>					
					<div class="col-md-2 col-sm-6 p0">
						<label class="text-muted">Precio Unit.</label>
						<input type="text" class="form-control no-model action <%= (typeof REMITOS_TOMAR_PRECIO_NETO != 'undefined' && REMITOS_TOMAR_PRECIO_NETO == 1)?"":"dn" %>" value="0.00" id="remito_item_neto"/>
						<input type="text" class="form-control no-model action <%= (typeof REMITOS_TOMAR_PRECIO_NETO != 'undefined' && REMITOS_TOMAR_PRECIO_NETO == 1)?"dn":"" %>" value="0.00" id="remito_item_precio"/>
					</div>
					<div class="col-md-1 col-sm-6 p0">
						<label class="text-muted">% Bonif.</label>
						<input type="number" min="0" max="100" class="form-control action no-model" placeholder="0 %" id="remito_item_bonificado"/>
					</div>
					
					<div class="col-md-2 col-sm-6 p0">
						<label class="text-muted">Importe</label>
						<div class="input-group">
							<input type="text" disabled class="form-control no-model" id="remito_item_subtotal" placeholder="Subtotal"/>
							<span class="input-group-btn">
								<button title="Ingresar linea" id="remito_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
							</span>
						</div>
					</div>
				</div>
			
			<div class="b-a" style="overflow: auto; margin-top: 15px;">
				<table id="tabla_items" class="table sortable m-b-none default footable">
					<thead class="bg-light">
						<tr>
							<th class="w75">Cant.</th>
							<th>Detalle</th>
							<th class="w75">Unit.</th>
							<th class="w75">Bonif.</th>
							<th class="w75">Subtotal</th>
							<th class="w25"></th>
							<th class="w25"></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			
			<div class="line line-dashed b-b line-lg"></div>
			
			<div class="oh m-t">
				<div class="col-md-6">
					<div class="form-horizontal pull-in">
						
						<div class="b-a iva_container" style="overflow: auto; margin-right: 30px;">
							<table id="tabla_impuestos" class="table sortable m-b-none default footable">
								<thead class="bg-light lter">
									<tr>
										<th>Tributo</th>
										<th class="w100">Base Imp.</th>
										<th class="w100">Monto</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>						
						<div id="detalle_ivas" class="iva_container"></div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-horizontal pull-in totales">
						<div class="form-group">
							<label class="control-label col-xs-8">Subtotal:</label>
							<div class="col-xs-4">
								<input type="text" disabled class="no-input" id="remito_subtotal"/>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-8">
								Descuento (%):
								<input type="number" min="0" max="100" value="<%= porc_descuento %>" <%= (typeof FACTURACION_EDITAR_DESCUENTO != 'undefined' && FACTURACION_EDITAR_DESCUENTO==1)?"":"disabled" %> class="form-control w-xs pull-right action text-right" id="remito_porc_descuento"/>
							</label>
							<div class="col-xs-4">
								<input type="text" disabled class="no-input" id="remito_descuento"/>
							</div>
						</div>

						<% if (ID_PROYECTO == 2) { %>
							<div class="form-group">
								<label class="control-label col-xs-8">
									Costo Envio:
								</label>
								<div class="col-xs-4">
									<input type="text" class="form-control tar" value="<%= costo_envio %>" name="costo_envio" id="remito_costo_envio"/>
								</div>
							</div>
							<div class="form-group">
								<div class="col-xs-12">
									<div class="checkbox">
										<label class="i-checks">
											<input type="checkbox" name="retirar_envio" id="remito_retirar_envio" <%= (retirar_envio==1)?"checked":"" %>><i></i> Retira en local
										</label>
									</div>							
								</div>
							</div>
						<% } else { %>
						
							<div class="form-group iva_container">
								<label class="control-label col-xs-6">IVA: </label>
								<div class="col-xs-6">
									<input type="text" disabled class="no-input" id="remito_iva"/>
								</div>
							</div>

							<div class="form-group iva_container">
								<label class="control-label col-xs-6">Otros Tributos: </label>
								<div class="col-xs-6">
									<input type="text" disabled class="no-input" id="remito_otros_impuestos"/>
								</div>
							</div>

						<% } %>
						
						<div class="line line-dashed b-b"></div>
						<div class="form-group">
							<label class="control-label col-xs-6 fs26">Total:</label>
							<div class="col-xs-6">
								<input type="text" disabled class="no-input fs26 bold" id="remito_total"/>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="line line-dashed b-b line-lg"></div>
			
			<div class="oh m-t">
				<h4>Notas y Observaciones <i title="Click para ayuda" class="observaciones_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></h4>
				<div>
					<textarea style="height: 100px" id="remito_observaciones" name="observaciones" placeholder="Puede escribir una nota u observacion que aparecer&aacute; al pie de p&aacute;gina del comprobante..." class="form-control"><%= ((id != undefined)?observaciones:(typeof OBSERVACIONES != 'undefined' ? OBSERVACIONES : "")).replaceAll("<br />","\n") %></textarea>
				</div>
			</div>
			
			<div class="line line-dashed b-b line-lg"></div>
			
		</div>
		</div>
	</div>
	
	<div class="oh m-t m-b tar pull-in">
		<% if (id != undefined && id != 0 && pendiente != 1) { %>
			<button class="btn btn-primary imprimir btn-addon pull-left m-r"><i class="icon glyphicon glyphicon-print"></i>Imprimir</button>
			<button class="btn btn-info enviar btn-addon pull-left"><i class="icon fa fa-send"></i>Enviar</button>
		<% } %>
		<button class="btn btn-success btn-lg aceptar btn-addon"><i class="icon fa fa-plus"></i>Guardar</button>
	</div>
	
	</div>
</div>
					<!--<select title="Vendedores" class="form-control action m-b" id="remito_vendedores"></select>-->
</script>

<script type="text/template" id="remito_item_template">
<td><%= Number(cantidad).toFixed(2) %></td>
<td><%= nombre %></td>
<td><%= Number(precio).toFixed(2) %></td>
<td><%= Number(bonificacion).toFixed(2) %>%</td>
<td><%= Number(total_con_iva).toFixed(2) %></td>
<td class="w25 p5"><i title="Editar" class="fa fa-file-text-o editar text-dark" /></td>
<td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" /></td>
</script>