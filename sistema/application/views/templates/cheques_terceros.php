<script type="text/template" id="cheques_terceros_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Cheques de Terceros</h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <div class="row">
		<div class="col-md-6 <%= (!lightbox) ? "col-lg-3" : "" %> sm-m-b">
		  <div class="input-group">
			  <input type="text" placeholder="Buscar..." autocomplete="off" id="cheques_terceros_texto" class="buscar form-control">
			  <span class="input-group-btn">
				<button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
			  </span>
			  <span class="input-group-btn">
				<button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
			  </span>
		  </div>
		</div>
		<% if (!lightbox) { %>
		  <div class="padder pull-right">
			<a class="btn btn-success btn-addon" href="app/#cheque_tercero"><i class="fa fa-plus"></i>Nuevo</a>
		  </div>
		<% } %>
	  </div>
	</div>
	<div class="advanced-search-div bg-light dk" style="display:none">
	  <div class="wrapper clearfix">
		<h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
		<div class="form-inline">
		  <div style="width: 150px; display: inline-block">
			<select id="cheques_terceros_entregados" class="w100p form-control">
				<option value="0">No Entregados</option>
				<option value="-1">Todos</option>
			</select>
		  </div>
		  <div style="width: 200px; display: inline-block">
			<select id="cheques_terceros_clientes" class="w100p form-control"></select>
		  </div>
		  <div style="width: 150px; display: inline-block">
			<select id="cheques_terceros_bancos" class="form-control">
			  <option value="0">Banco</option>
			  <% for(var i=0;i<bancos.length;i++) { %>
				<% var banco = bancos[i] %>
				<option value="<%= banco.id %>"><%= banco.nombre %></option>
			  <% } %>
			</select>
		  </div>
		  <div class="form-group">
			<button class="buscar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
		  </div>            
		</div>
	  </div>
	</div>		
	<div class="panel-body">
		<div class="b-a">
			<table id="cheques_terceros_table" class="m-b-none table sortable default footable">
				<thead>
					<tr>
						<th>Cliente</th>
						<th>Banco</th>
						<th>Numero</th>
						<th>Fecha Cobro</th>
						<th class="tar">Monto</th>
						<% if (permiso > 1) { %>
							<th class="w25"></th>
							<th class="w25"></th>
						<% } %>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot class="pagination_container hide-if-no-paging"></tfoot>
			</table>
		</div>
	</div>
	<div class="panel-footer">
		<% if (!lightbox) { %>
			<span class="bold">Leyenda: </span>
			<span class="ml10">Disponibles</span>
			<span class="ml10 text-primary">Entregados</span>
			<span class="ml10 text-danger">Anulados</span>
		<% } %>			
	</div>
  </div>
</div>	
</script>


<script type="text/template" id="cheques_terceros_item">
	<% var clase = "";
	if (entregado == 1) clase = "text-primary";
	if (devuelto == 1) clase = "text-danger";
	%>
	<td><span class='ver <%= clase %>'><%= cliente %></span></td>
	<td><span class='ver <%= clase %>'><%= banco %></span></td>
	<td><span class='ver <%= clase %>'><%= numero %></span></td>
	<td><span class='ver <%= clase %>'><%= fecha_cobro %></span></td>
	<td class="tar"><span class='ver <%= clase %>'><%= Number(monto).toFixed(2) %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="cheques_terceros_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == 0) { %>
        Nuevo Cheque de Tercero
    <% } else { %>
        Modificar Cheque de Tercero
    <% } %>	      
  </h1>
</div>

<div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
        <div class="panel-heading">
            <span class="font-bold">Ingrese los datos</span>
        </div>
        <div class="panel-body">
            <div class="form-horizontal">
			  
				<div class="form-group">
					<label class="col-md-2 control-label">Titular</label>
					<div class="col-md-10">
						<input type="text" name="titular" class="form-control" id="cheques_terceros_titular" value="<%= titular %>"/>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-md-2 control-label">CUIT</label>
					<div class="col-md-10">
						<input type="text" name="cuit_titular" class="form-control" id="cheques_terceros_cuit_titular" value="<%= cuit_titular %>"/>
					</div>
				</div>	
			  
				<% if (!lightbox) { %>
				  <div class="form-group">
					  <label class="col-md-2 control-label">Cliente</label>
					  <div class="col-md-10">
						  <select class="form-control" id="cheques_terceros_clientes"></select>
					  </div>
				  </div>
				<% } %>
				<div class="form-group">
					<label class="col-md-2 control-label">Numero</label>
					<div class="col-md-10">
						<input type="text" name="numero" class="form-control" id="cheques_terceros_numero" value="<%= numero %>"/>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-md-2 control-label">Banco</label>
					<div class="col-md-10">
						<select class="form-control" id="cheques_terceros_bancos">
						  <option value="0">Seleccione</option>
						  <% for(var i=0;i<bancos.length;i++) { %>
							<% var banco = bancos[i] %>
							<option <%= (banco.id == id_banco)?"selected":"" %> value="<%= banco.id %>"><%= banco.nombre %></option>
						  <% } %>						  
						</select>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-md-2 control-label">Sucursal</label>
					<div class="col-md-10">
						<input type="text" name="sucursal" class="form-control" id="cheques_terceros_sucursal" value="<%= sucursal %>"/>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-md-2 control-label">Monto</label>
					<div class="col-md-10">
						<input type="text" name="monto" class="form-control" id="cheques_terceros_monto" value="<%= monto %>"/>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-md-2 control-label">Fecha Emision</label>
					<div class="col-md-10">
						<input type="text" name="fecha_emision" class="form-control" id="cheques_terceros_fecha_emision" value="<%= fecha_emision %>"/>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-md-2 control-label">Fecha Pago</label>
					<div class="col-md-10">
						<input type="text" name="fecha_cobro" class="form-control" id="cheques_terceros_fecha_cobro" value="<%= fecha_cobro %>"/>
					</div>
				</div>
				<% if (!lightbox) { %>
				  <div class="form-group">
					  <label class="col-md-2 control-label">Fecha Cancelado</label>
					  <div class="col-md-10">
						  <input type="text" name="fecha_debitado" class="form-control" id="cheques_terceros_fecha_debitado" value="<%= fecha_debitado %>"/>
					  </div>
				  </div>
				<% } %>
    
				<% if (!lightbox) { %>
					<div class="form-group">
						<label class="col-lg-2 control-label">Devuelto</label>
						<div class="col-lg-10">
							<label class="i-switch i-switch-md bg-info m-t-xs m-r">
							  <input type="checkbox" name="devuelto" id="cheques_terceros_devuelto" class="checkbox" value="1" <%= (devuelto == 1)?"checked":"" %> >
							  <i></i>
							</label>
						</div>
					</div>				
					<div class="form-group">
						<label class="col-md-2 control-label">Motivo</label>
						<div class="col-md-10">
							<input type="text" name="motivo" class="form-control" id="cheques_terceros_motivo" value="<%= motivo %>"/>
						</div>
					</div>
				<% } %>
				
                <div class="line line-dashed b-b line-lg pull-in"></div>
                
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">    
                            <button class="btn btn-success guardar">Guardar</button>
							<% if (id_orden_pago != 0) { %>
								<button class="btn btn-info orden_pago">Ver Orden de Pago</button>
							<% } %>			
                        </div>
                    </div>
                <% } %>
			</div>
		</div>
	</div>
</div>
</script>