<script type="text/template" id="sindi_estudio_contable_detalle_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("sindi_empresas") %>
  <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>Estudio Contable / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="rform">
    <div class="row">

      <div class="col-md-3">
        <div class="panel panel-default">
          
          <div class="panel-heading">
            <span class="bold negro">Informaci&oacute;n b&aacute;sica</span>
          </div>
          <div class="panel-body acerca_de">
            <% if (cuit!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">C.U.I.T.</label>
              <span class="control-info"><%= cuit %></span>
            </div>
            <% } %>
            <% if (domicilio!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">Domicilio</label>
              <span class="control-info"><%= domicilio %></span>
            </div>
            <% } %>
            <% if (localidad!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">Localidad</label>
              <span class="control-info"><%= localidad %></span>
            </div>
            <% } %>
            <% if (email!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">Email</label>
              <span class="control-info"><%= email %></span>
            </div>
            <% } %>
            <% if (telefono!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">Telefono</label>
              <span class="control-info"><%= telefono %></span>
            </div>
            <% } %>
            <div class="form-group mb0 tar">
              <a class="btn btn-white editar" data-id="<%= id %>" href="javascript:void(0)">
                <i class="fa fa-pencil m-r-xs"></i>Editar
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-9">
        <div class="panel panel-default mb0">
          <ul class="nav nav-tabs nav-tabs-2" role="tablist">
            <li id="empresas_activas_link" class="active">
              <a href="#tab1_empresas" role="tab" data-toggle="tab">
                <i class="fa text-success fa-users m-r-xs"></i>
                Empleados Activos
              </a>
            </li>
            <li id="historial_link">
              <a href="#tab2_historial" role="tab" data-toggle="tab">
                <i class="fa text-info fa-calendar m-r-xs"></i>
                Historial
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div id="tab1_empresas" class="tab-pane panel-body active">
              <div class="b-a table-responsive">
                <table class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="w20 tac">#</th>
                      <th>Empresa Nº</th>
                      <th>Nombre</th>
                      <th class=tac">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< empresas_activas.length; i++) { %>
                      <% var empr = empresas_activas[i] %>
                      <tr>
                        <td class=tac"><%= (i+1) %></td>
                        <td><a href="app/#sindi_empresa/<%= empr.id %>"><%= empr.codigo+"-"+empr.identificador %></a></td>
                        <td><a href="app/#sindi_empresa/<%= empr.id %>" class="text-info"><%= empr.nombre %></a></td>
                        <td class=tac">
                          <% if (empr.estado == 0) { %>
                            <span class="label db fs12 label-danger">Baja</span>
                          <% } else { %>
                            <span class="label db fs12 label-success">Alta</span>
                          <% } %>
                        </td>
                      </tr>
                    <% } %>                  
                  </tbody>
                </table>
              </div>
            </div>
            <div id="tab2_historial" class="tab-pane panel-body">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>
<script type="text/template" id="sindi_estudios_contables_panel_template">
	<div class="bg-light lter b-b wrapper-md ng-scope">
		<h1 class="m-n font-thin h3"><i class="fa fa-university icono_principal"></i>Empresas
			/ <b>Estudios Contables</b>
		</h1>
	</div>
	<div class="wrapper-md ng-scope">
		<div class="panel panel-default">

			<div class="panel-heading oh">
				<div class="row">
					<div class="col-md-6 col-lg-3 sm-m-b">
						<div class="search_container"></div>
					</div>
          <% if (control.check("sindi_estudios_contables") > 1) { %>
  					<div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
  						<a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
  					</div>
          <% } %>
				</div>
			</div>
			<div class="panel-body">
				<div class="b-a table-responsive">
					<table id="sindi_estudios_contables_table" class="table table-striped sortable m-b-none default footable">
						<thead>
							<tr>
								<th class="sorting" data-sort-by="nombre">Nombre</th>
								<th style="width:200px"class="sorting tac" data-sort-by="id_localidad">Localidad</th>
								<th style="width:200px; padding-left:0px; padding-right:0px" class="tac">Telefono</th>
								<th style="width:90px; padding-left:0px; padding-right:0px" class="tac">Empresas</th>
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


<script type="text/template" id="sindi_estudios_contables_item">
	<td class="ver"><span class='text-info'><%= nombre %></span></td>
	<td class="ver tac"><span class='text-info'><%= localidad %></span></td>
	<td class="ver tac"><span class='text-info'><%= telefono %></span></td>
	<td class="ver tac"><span class='text-info'><%= empresas_activas.length %></span></td>
</script>

<script type="text/template" id="sindi_estudios_contables_edit_panel_template">
<form onsubmit="return false" class='modal-content'>
	<div class='modal-header'>
    <div class="row">
      <div class="col-md-12">
        <b><%= (id == undefined) ? 'Nuevo Estudio Contable' : nombre %></b>
      </div>
		</div>
	</div>
  <div class="modal-body">
  	<div class="row">
  		<div class="col-sm-12">
  			<div class="form-group">
  				<label class="control-label">Nombre</label>
  				<input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" class="form-control" id="sindi_estudios_contables_nombre" value="<%= nombre %>"/>
  			</div>
  		</div>
  	</div>
  	<div class="row">
  		<div class="col-md-8">
  			<div class="form-group">
  				<label class="control-label">Dirección</label>
  				<input <%= (!edicion)?"disabled":"" %> type="text" name="domicilio" class="form-control" id="sindi_estudios_contables_domicilio" value="<%= domicilio%>"/>
  			</div>
  		</div>
  		<div class="col-md-4">
  			<div class="form-group">
  				<label class="control-label">Localidad</label>
  				<select <%= (!edicion)?"disabled":"" %> type="text" name="id_localidad" class="form-control tac" id="sindi_estudios_contables_id_localidad"/>

  				</select>
  			</div>
  		</div>
  	</div>
  	<div class="row">
  		<div class="col-md-4">
  			<div class="form-group">
  				<label class="control-label">C.U.I.T.</label>
  				<input <%= (!edicion)?"disabled":"" %> type="text" name="cuit" class="form-control" id="sindi_estudios_contables_cuit" value="<%= cuit%>"/>
  			</div>
  		</div>
  		<div class="col-md-4">
  			<div class="form-group">
  				<label class="control-label">Email</label>
  				<input <%= (!edicion)?"disabled":"" %> type="text" name="email" class="form-control" id="sindi_estudios_contables_email" value="<%= email%>"/>
  			</div>
  		</div>
  		<div class="col-md-4">
  			<div class="form-group">
  				<label class="control-label">Telefono</label>
  				<input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" class="form-control" id="sindi_estudios_contables_telefono" value="<%= telefono%>"/>
  			</div>
  		</div>
  	</div>
  </div>
  <div class="modal-footer">
    <a class="btn btn-default pull-left cerrar">Cerrar</a>
			<button class="btn guardar btn-success">Guardar</button>
	</div>

</form>

</script>