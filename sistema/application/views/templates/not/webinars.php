<script type="text/template" id="webinars_panel_template">
	<% var modulo = control.get("webinars") %>
	<div class="bg-light lter b-b wrapper-md ng-scope">
		<h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> <b><%= modulo.title %></b>
		</h1>
	</div>
	<div class="wrapper-md ng-scope">
		<div class="panel panel-default">
		
			<div class="panel-heading oh">
				<div class="row">
					<div class="col-md-6 col-lg-3 sm-m-b">
						<div class="search_container"></div>
					</div>
          <% if (control.check("webinars") > 1) { %>
  					<div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
  						<a class="btn btn-info btn-addon" href="app/#webinar"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")) ?>&nbsp;&nbsp;</a>
  					</div>
          <% } %>
				</div>
			</div>
			<div class="panel-body">
				<div class="b-a table-responsive">
					<table id="webinars_table" class="table table-striped sortable m-b-none default footable">
						<thead>
							<tr>
								<th style="width:20px;">
									<label class="i-checks m-b-none">
										<input class="esc sel_todos" type="checkbox"><i></i>
									</label>
								</th>
								<th class="sorting" data-sort-by="nombre"><?php echo lang(array("es"=>"Nombre","en"=>"Name")) ?></th>
								<% if (permiso > 1) { %>
									<th class="w100"></th>
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


<script type="text/template" id="webinars_item">
	<td>
		<label class="i-checks m-b-none">
			<input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
		</label>
	</td>
	<td class="ver"><span class='text-info'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
		<td class="p5 td_acciones">
			<div class="btn-group dropdown ml10">
				<button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-plus"></i>
				</button>		
				<ul class="dropdown-menu pull-right">
					<li><a target="_blank" href="https://<%= DOMINIO %><%= link %>">Ver Web</a></li>
					<li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>"><?php echo lang(array("es"=>"Duplicar","en"=>"Duplicate")) ?></a></li>
					<li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Delete","en"=>"Delete")) ?></a></li>
				</ul>
			</div>
		</td>
	<% } %>
</script>

<script type="text/template" id="webinars_edit_panel_template">
<% var modulo = control.get("webinars") %>
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i><%= modulo.title %>
		/ <b><%= (id == undefined) ? '<?php echo lang(array("es"=>"Nuevo","en"=>"New")) ?>' : nombre %></b>
	</h1>
</div>
<form onsubmit="return false" class="wrapper-md ng-scope">
	<div class="centrado rform">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">

							<div class="form-group">
								<label class="control-label"><?php echo lang(array("es"=>"Nombre","en"=>"Name")) ?></label>
								<input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" autocomplete="off" class="form-control" id="webinars_nombre" value="<%= nombre %>"/>
							</div>					
						
							<div class="form-group">
								<label class="control-label"><?php echo lang(array("es"=>"Descripcion","en"=>"Description")) ?></label>
								<textarea <%= (!edicion)?"disabled":"" %> type="text" name="texto" autocomplete="off" class="form-control" id="webinars_texto"><%= texto %></textarea>
							</div>

							<div class="form-group">
								<label class="control-label"><?php echo lang(array("es"=>"Fecha","en"=>"Date")) ?></label>
								<input type="date" id="webinars_fecha_realizacion" name="fecha_realizacion" value="<%= fecha_realizacion %>" class="form-control" />
							</div>

							<div class="form-group">
	              <?php
	              single_upload(array(
	                "name"=>"path",
	                "label"=>lang(array("es"=>"Imagen de encabezado","en"=>"Header Image")),
	                "url"=>"/sistema/webinars/function/save_image/",
	                "url_file"=>"/sistema/webinars/function/save_file/",
	              )); ?>
	            </div>

							<div class="form-group">
	              <?php
	              single_upload(array(
	                "name"=>"path_2",
	                "label"=>lang(array("es"=>"Imagen lateral","en"=>"Side Image")),
	                "url"=>"/sistema/webinars/function/save_image/",
	                "url_file"=>"/sistema/webinars/function/save_file/",
	              )); ?>
	            </div>

							<div class="form-group">
								<label class="control-label"><?php echo lang(array("es"=>"Link de Video","en"=>"Video Link")) ?></label>
								<input <%= (!edicion)?"disabled":"" %> type="text" name="video" autocomplete="off" class="form-control" id="webinars_video" value="<%= video %>"/>
							</div>					

						</div>
					</div>
				</div>
				<% if (edicion) { %>
					<button class="btn guardar btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")) ?></button>
				<% } %>
			</div>
		</div>
	</div>
</form>

</script>