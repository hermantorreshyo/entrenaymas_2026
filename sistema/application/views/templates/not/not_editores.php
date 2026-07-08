<script type="text/template" id="not_editores_panel_template">
	<% var modulo = control.get("not_editores") %>
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
          <% if (control.check("not_editores") > 1) { %>
  					<div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
  						<a class="btn btn-info btn-addon" href="app/#not_editor"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")) ?>&nbsp;&nbsp;</a>
  					</div>
          <% } %>
				</div>
			</div>
			<div class="panel-body">
				<div class="b-a table-responsive">
					<table id="not_editores_table" class="table table-striped sortable m-b-none default footable">
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


<script type="text/template" id="not_editores_item">
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
					<li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>"><?php echo lang(array("es"=>"Duplicar","en"=>"Duplicate")) ?></a></li>
					<li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Delete","en"=>"Delete")) ?></a></li>
				</ul>
			</div>
		</td>
	<% } %>
</script>

<script type="text/template" id="not_editores_edit_panel_template">
<% var modulo = control.get("not_editores") %>
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
							<div class="row">
								<div class="<%= (ID_EMPRESA == 1129)?"col-xs-8":"col-sm-4" %>">
									<div class="form-group">
										<label class="control-label"><?php echo lang(array("es"=>"Nombre","en"=>"Name")) ?></label>
										<input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" autocomplete="off" class="form-control" id="not_editores_nombre" value="<%= nombre %>"/>
									</div>
							  </div>
                <div class="<%= (ID_EMPRESA == 1129)?"dn":"col-sm-4" %>">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Cargo","en"=>"Subtitle")) ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="subtitulo" autocomplete="off" class="form-control" id="not_editores_subtitulo" value="<%= subtitulo %>"/>
                  </div>
                </div>
							  <div class="col-sm-4">
									<div class="form-group">
										<label class="control-label"><?php echo lang(array("es"=>"Tipo","en"=>"Type")) ?></label>
										<select <%= (!edicion)?"disabled":"" %> name="tipo" class="form-control" id="not_editores_tipo">
											<% if (ID_EMPRESA == 1129) { %>
												<option value="L" <%= (tipo=="L")?"selected":"" %>>Laboratorio</option>
												<option value="C" <%= (tipo=="C")?"selected":"" %>>Centro de Reproduccion</option>
											<% } else { %>
												<option value="P" <%= (tipo=="P")?"selected":"" %>><?php echo lang(array("es"=>"Propio","en"=>"Staff")) ?></option>
												<option value="I" <%= (tipo=="P")?"selected":"" %>><?php echo lang(array("es"=>"Invitado","en"=>"Guess")) ?></option>
											<% } %>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label"><?php echo lang(array("es"=>"Telefono","en"=>"Telephone")) ?></label>
										<input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" autocomplete="off" class="form-control" id="not_editores_telefono" value="<%= telefono %>"/>
									</div>									
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Email</label>
										<input <%= (!edicion)?"disabled":"" %> type="text" name="email" autocomplete="off" class="form-control" id="not_editores_email" value="<%= email %>"/>
									</div>									
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Web</label>
										<input <%= (!edicion)?"disabled":"" %> type="text" name="web" autocomplete="off" class="form-control" id="not_editores_web" value="<%= web %>"/>
									</div>									
								</div>
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo lang(array("es"=>"Descripcion","en"=>"Description")) ?></label>
								<textarea <%= (!edicion)?"disabled":"" %> type="text" name="texto" autocomplete="off" class="form-control" id="not_editores_texto"><%= texto %></textarea>
							</div>

							<div class="form-group">
	              <?php
	              single_upload(array(
	                "name"=>"path",
	                "label"=>lang(array("es"=>"Foto","en"=>"Photo")),
	                "url"=>"/sistema/not_eventos/function/save_image/",
	                "url_file"=>"/sistema/not_eventos/function/save_file/",
	                "width"=>(isset($empresa->config["not_editor_image_width"]) ? $empresa->config["not_editor_image_width"] : 256),
	                "height"=>(isset($empresa->config["not_editor_image_height"]) ? $empresa->config["not_editor_image_height"] : 256),
	                "quality"=>(isset($empresa->config["not_editor_image_quality"]) ? $empresa->config["not_editor_image_quality"] : 0.98),
	                "crop_type"=>(isset($empresa->config["not_editor_image_crop_type"]) ? $empresa->config["not_editor_image_crop_type"] : 1),
	                "resizable"=>(isset($empresa->config["not_editor_image_resizable"]) ? $empresa->config["not_editor_image_resizable"] : 0),
	                "thumbnail_width"=>(isset($empresa->config["not_editor_thumbnail_width"]) ? $empresa->config["not_editor_thumbnail_width"] : 0),
	                "thumbnail_height"=>(isset($empresa->config["not_editor_thumbnail_height"]) ? $empresa->config["not_editor_thumbnail_height"] : 0),
	              )); ?>
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