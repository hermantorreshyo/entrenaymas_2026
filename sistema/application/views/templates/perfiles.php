<script type="text/template" id="perfiles_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <% var modulo = control.get("perfiles") %>
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuracion","en"=>"Configuration")); ?>
    / <b><%= modulo.title %></b>
  </h1>
</div>	
<div class="wrapper-md ng-scope">
	<div class="panel panel-default">
		<div class="panel-heading oh">
			<a class="btn pull-right btn-info btn-addon" href="app/#perfil"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</a>
		</div>
		<div class="panel-body">
			<div class="b-a table-responsive">
				<table id="perfiles_table" class="table table-striped m-b-none default footable">
					<thead>
						<tr>
							<th><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></th>
							<% if (permiso > 1) { %>
								<th class="th_acciones w120"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?></th>
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


<script type="text/template" id="perfiles_item">
	<td>
		<span class="ver"><%= nombre %></span>
	</td>
	<% if (permiso > 1) { %>
		<td>
		  <% if (principal == 0) { %>
			<div class="btn-group dropdown ml10">
			  <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fa fa-plus"></i>
			  </button>		
			  <ul class="dropdown-menu pull-right">
				<li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
			  </ul>
			</div>  
		  <% } %>
		</td>
	<% } %>
</script>

<script type="text/template" id="perfiles_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <% var modulo = control.get("perfiles") %>
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuracion","en"=>"Configuration")); ?>
    / <%= modulo.title %>
    / <b><%= (id == undefined)?"<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>":nombre %></b>
  </h1>
</div>	
<div class="wrapper-md">
	<div class="centrado rform">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">
							<div class="form-group">
								<label class="control-label"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></label>
								<% if (edicion) { %>
									<input type="text" name="nombre" class="form-control" id="nombre" value="<%= nombre %>"/>
								<% } else { %>
									<span><%= nombre %></span>
								<% } %>
							</div>
							<% if (USUARIO_PPAL == 1) { %>
								<div class="form-group">
									<div class="checkbox">
										<label class="i-checks">
											<input type="checkbox" name="solo_usuario" class="checkbox" value="1" <%= (solo_usuario == 1)?"checked":"" %>><i></i>
                      <?php echo lang(array("es"=>"Mostrar solamente la informacion creada por el usuario","en"=>"Show only the information created by the user")); ?>
										</label>
									</div>				
								</div>
							<% } %>
              <div class="form-group b-a" style="overflow: auto; max-height: 400px">
                <div class="table-responsive">
                  <table id="perfiles_tabla" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th class="vam"><?php echo lang(array("es"=>"Modulo","en"=>"Module")); ?></th>
                        <th class="vam">
                          <?php echo lang(array("es"=>"Permiso","en"=>"Permission")); ?>
                          <div class="fr">
                            <button class="btn btn-default btn-sm font-bold todos_administrables"><i class="fa fa-arrow-up"></i></button>
                            <button class="btn btn-default btn-sm font-bold todos_ocultos"><i class="fa fa-arrow-down"></i></button>
                          </div>
                        </th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="line b-b m-b-lg"></div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1 tar">
				<button class="btn btn-success guardar"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
			</div>
		</div>
	</div>
</script>