<script type="text/template" id="necrologicas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3">Necrologicas</h1>
  </div>
  <div class="wrapper-md ng-scope">
	<div class="panel panel-default">
	  <div class="panel-heading oh">
		<div class="search_container col-lg-3 col-md-4 col-sm-6"></div>
		<a class="btn pull-right btn-success btn-addon" href="app/#necrologica"><i class="fa fa-plus"></i>Nueva</a>
	  </div>
	  <div class="panel-body">
		<div class="b-a table-responsive">
		  <table id="necrologicas_table" data-ordenable-table="necrologica" data-ordenable-where="" class="table table-striped ordenable m-b-none default footable">
			<thead>
			  <tr>
				<th>Nombre</th>
				<th>Participante</th>
				<th>Edad</th>
				<th>Fecha</th>
				<% if (permiso > 1) { %>
					<th class="w25"></th>
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
	</div>
  </div>
</script>


<script type="text/template" id="necrologicas_item">
	<td class="edit"><%= nombre %></td>
	<td class="edit"><%= participante %><%= (isEmpty(participante_email)) ? "" : "<br/>"+participante_email %></td>
	<td class="edit"><%= edad %></td>
	<td class="edit"><%= fecha_fallecimiento %></td>
	<% if (permiso > 1) { %>
		<td><i title="Activo" data-toggle="tooltip" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i></td>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="necrologicas_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nueva Necrologica
    <% } else { %>
        <%= nombre %>
    <% } %>	      
  </h1>
</div>

<div class="wrapper-md">
  <div class="tab-container">
	  <ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Datos</a>
		</li>
		<li class="">
			<a href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Texto</a>
		</li>
	  </ul>
	  <div class="tab-content">
		<div id="tab1" class="tab-pane active panel-body">
			<div class="form-horizontal">    

				<div class="form-group">
					<label class="col-md-2 control-label tal">Nombre</label>
					<div class="col-md-10">
						<input type="text" name="nombre" class="form-control" id="necrologicas_nombre" value="<%= nombre %>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label tal">Edad</label>
					<div class="col-md-10">
						<input type="text" name="edad" class="form-control" id="necrologicas_edad" value="<%= edad %>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label tal">Fecha Fallecimiento</label>
					<div class="col-md-10">
						<input type="text" class="form-control" id="necrologicas_fecha_fallecimiento" value="<%= fecha_fallecimiento %>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label tal">Fecha Traslado</label>
					<div class="col-md-10">
						<input type="text" class="form-control" id="necrologicas_fecha_traslado" value="<%= fecha_traslado %>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label tal">Hora Traslado</label>
					<div class="col-md-10">
						<input type="text" name="hora_traslado" class="form-control" id="necrologicas_hora_traslado" value="<%= hora_traslado %>"/>
					</div>
				</div>							
				<div class="form-group">
					<label class="col-md-2 control-label tal">Casa Duelo</label>
					<div class="col-md-10">
						<input type="text" name="casa_duelo" class="form-control" id="necrologicas_casa_duelo" value="<%= casa_duelo %>"/>
					</div>
				</div>							
				<div class="form-group">
					<label class="col-md-2 control-label tal">Cementerio</label>
					<div class="col-md-10">
						<input type="text" name="cementerio" class="form-control" id="necrologicas_cementerio" value="<%= (typeof id != "undefined") ? cementerio : "<?php echo (isset($empresa->config["necrologica_cementerio"]) ? $empresa->config["necrologica_cementerio"] : "") ?>" %>"/>
					</div>
				</div>							
				<div class="form-group">
					<label class="col-md-2 control-label tal">Servicio Velatorio</label>
					<div class="col-md-10">
						<input type="text" name="servicio_velatorio" class="form-control" id="necrologicas_servicio_velatorio" value="<%= (typeof id != "undefined") ? servicio_velatorio : "<?php echo (isset($empresa->config["necrologica_servicio_velatorio"]) ? $empresa->config["necrologica_servicio_velatorio"] : "") ?>" %>"/>
					</div>
				</div>							

				<div class="form-group">
					<label class="col-md-2 control-label tal">Participante</label>
					<div class="col-md-10">
						<input type="text" name="participante" class="form-control" id="necrologicas_participante" value="<%= (typeof id != "undefined") ? participante : "<?php echo (isset($empresa->config["necrologica_participante"]) ? $empresa->config["necrologica_participante"] : "") ?>" %>"/>
					</div>
				</div>							
				<div class="form-group">
					<label class="col-md-2 control-label tal">Email</label>
					<div class="col-md-10">
						<input type="text" name="participante_email" class="form-control" id="necrologicas_participante_email" value="<%= (typeof id != "undefined") ? participante_email : "<?php echo (isset($empresa->config["necrologica_participante_email"]) ? $empresa->config["necrologica_participante_email"] : "") ?>" %>"/>
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
		<div id="tab2" class="tab-pane panel-body">
			<div class="form-horizontal">
			  
				<div class="form-group">
					<div class="col-xs-12">
						<textarea name="texto" id="necrologicas_texto"><%= (typeof id != "undefined") ? texto : "<?php echo (isset($empresa->config["necrologica_texto"]) ? $empresa->config["necrologica_texto"] : "") ?>" %></textarea>
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
</div>

</script>