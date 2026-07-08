<script type="text/template" id="servicios_envio_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3">Servicios de Envio</h1>
  </div>
  <div class="wrapper-md ng-scope">
	  <div class="panel panel-default">
	  
		  <div class="panel-heading oh">
			  <div class="search_container col-lg-3 col-md-4 col-sm-6"></div>
			  <a class="btn pull-right btn-success btn-addon" href="app/#servicio_envio"><i class="fa fa-plus"></i>Agregar</a>
		  </div>
		  <div class="panel-body">
			  <div class="b-a table-responsive">
				  <table id="servicios_envio_table" data-ordenable-table="servicios_envio" class="table table-striped ordenable m-b-none default footable">
					  <thead>
						  <tr>
							  <th>Nombre</th>
							  <th>Empresa</th>
							  <% if (permiso > 1) { %>
								  <th class="w25"></th>
								  <th class="w25"></th>
							  <% } %>
						  </tr>
					  </thead>
					  <tbody></tbody>
				  </table>
			  </div>
		  </div>
	  </div>
  </div>
</script>


<script type="text/template" id="servicios_envio_item">
	<td><%= nombre %></td>
	<td><%= empresa %></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="servicios_envio_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nuevo Servicio de Envio
    <% } else { %>
        <%= nombre %>
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
				  <label class="col-md-2 control-label">Nombre</label>
				  <div class="col-md-10">
					  <input type="text" name="nombre" class="form-control" id="servicios_envio_nombre" value="<%= nombre %>"/>
				  </div>
			  </div>
			  <div class="form-group">
				  <label class="col-md-2 control-label">Empresa</label>
				  <div class="col-md-10">
					<select class="form-control" name="empresa" id="servicios_empresa">
					  <option value="OCA" <%= (empresa=="OCA")?"selected":"" %>>OCA</option>
					  <option value="Andreani" <%= (empresa=="Andreani")?"selected":"" %>>Andreani</option>
					  <option value="Personalizada" <%= (empresa=="Personalizada")?"selected":"" %>>Personalizada</option>
					</select>
				  </div>
			  </div>
			  
			  <div class="form-group cb">
				  <label class="col-md-2 control-label">Activo </label>
				  <div class="col-md-10">
					  <% if (edicion) { %>
						  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
							<input type="checkbox" id="servicios_envio_activo" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> >
							<i></i>
						  </label>
					  <% } else { %>
						  <span><%= ((activo==0) ? "No" : "Si") %></span>
					  <% } %>
				  </div>
			  </div>
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Coef. Peso Aforado</label>
				  <div class="col-md-10">
					  <input type="text" name="coef_aforado" class="form-control" value="<%= coef_aforado %>"/>
				  </div>
			  </div>			  
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Comision seguro (% s/Valor declarado)</label>
				  <div class="col-md-10">
					  <input type="text" name="seguro_porcentaje" class="form-control" value="<%= seguro_porcentaje %>"/>
				  </div>
			  </div>
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Importe m&iacute;nimo</label>
				  <div class="col-md-10">
					  <input type="text" name="seguro_minimo" class="form-control" value="<%= seguro_minimo %>"/>
				  </div>
			  </div>			  
			  
			  <div class="row">
				<div class="col-sm-12">
				  <div class="panel panel-default">
					<div class="panel-heading font-bold">Tabla de Costos</div>
					<div class="panel-body">
					  <p class="text-muted">Aqui puede formar la tabla con los costos del servicio, segun el peso (en kilogramos) de los productos y la distancia (en kilometros) con el cliente.</p>
					  <div>
						<button class="btn btn-default nuevo_peso">Agregar Peso</button>
						<button class="btn btn-default nueva_distancia">Agregar Distancia</button>
					  </div>
					  <div class="m-t">
						<table id="costos_envio_tabla" class="table table-striped m-b-none default"></table>
					  </div>
					</div>
					<div class="panel-footer bg-light lter">
					  <p class="text-muted">M&aacute;s alla de los siguientes limites, el cliente deber&aacute; acordar con la empresa el m&eacute;todo de envio.</p>
					  <div class="padder">
						<div class="form-inline">
						  <div class="form-group">
							<label>L&iacute;mite de Distancia (Kms): </label>
						  </div>
						  <div class="form-group padder-lg">
							<input type="number" name="limite_distancia" class="form-control" value="<%= limite_distancia %>"/>
						  </div>					  
						  <div class="form-group padder-lg">
							<label>L&iacute;mite de Peso (Kgs): </label>
						  </div>
						  <div class="form-group">
							<input type="number" name="limite_peso" class="form-control" value="<%= limite_peso %>"/>
						  </div>
						</div>
					  </div>
					</div>
					
				  </div>
				</div>
			  </div>
			  
			  <div class="form-group cb">
				  <label class="col-md-2 control-label">Modo de Prueba </label>
				  <div class="col-md-10">
					  <% if (edicion) { %>
						  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
							<input type="checkbox" id="servicios_envio_test" name="test" class="checkbox" value="1" <%= (test == 1)?"checked":"" %> >
							<i></i>
						  </label>
					  <% } else { %>
						  <span><%= ((test==0) ? "No" : "Si") %></span>
					  <% } %>
				  </div>
			  </div>
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Cliente de Prueba</label>
				  <div class="col-md-10">
					  <input type="text" name="test_cliente" class="form-control" value="<%= test_cliente %>"/>
				  </div>
			  </div>
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Usuario de Prueba</label>
				  <div class="col-md-10">
					  <input type="text" name="test_usuario" class="form-control" value="<%= test_usuario %>"/>
				  </div>
			  </div>			  
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Contrase&ntilde;a de Prueba</label>
				  <div class="col-md-10">
					  <input type="text" name="test_password" class="form-control" value="<%= test_password %>"/>
				  </div>
			  </div>
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Nro. Contrato de Prueba</label>
				  <div class="col-md-10">
					  <input type="text" name="test_contrato" class="form-control" value="<%= test_contrato %>"/>
				  </div>
			  </div>

			  <div class="form-group">
				  <label class="col-md-2 control-label">Cliente Producci&oacute;n</label>
				  <div class="col-md-10">
					  <input type="text" name="prod_cliente" class="form-control" value="<%= prod_cliente %>"/>
				  </div>
			  </div>			  
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Usuario Producci&oacute;n</label>
				  <div class="col-md-10">
					  <input type="text" name="prod_usuario" class="form-control" value="<%= prod_usuario %>"/>
				  </div>
			  </div>			  
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Contrase&ntilde;a Producci&oacute;n</label>
				  <div class="col-md-10">
					  <input type="text" name="prod_password" class="form-control" value="<%= prod_password %>"/>
				  </div>
			  </div>
			  
			  <div class="form-group">
				  <label class="col-md-2 control-label">Contrato Producci&oacute;n</label>
				  <div class="col-md-10">
					  <input type="text" name="prod_contrato" class="form-control" value="<%= prod_contrato %>"/>
				  </div>
			  </div>
			  
			  <div class="line line-dashed b-b line-lg pull-in"></div>
			  <% if (edicion) { %>
				  <div class="form-group">
					  <div class="col-xs-12">    
						  <button class="btn btn-success guardar">Guardar</button>
					  </div>
				  </div>
			  <% } %>
		</div>
	  </div>
  </div>
</div>

</script>