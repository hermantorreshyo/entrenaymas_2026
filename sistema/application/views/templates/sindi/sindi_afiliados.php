<script type="text/template" id="sindi_afiliado_detalle_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("sindi_afiliados") %>
  <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>Afiliado
    / <%= codigo %>-<%= identificador %> / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="rform">
    <div class="row">
      <div class="col-md-3">
        <div class="panel panel-default">
          <div class="panel-body pl10 pr10 pt10 pb10">
             <div class="row">
                <div class="col-sm-6 col-xs-12">
                  <span class="label db fs14 pt5 pb5 cp <%= (estado_sindicato==0)?"label-danger":"label-success" %> <%= (estado_sindicato==0)?"alta_sindicato":"baja_sindicato" %>">Sindicato</span>
                </div>
                <div class="col-sm-6 col-xs-12">
                  <span class="label db fs14 pt5 pb5 cp <%= (estado_obra_social==0)?"label-danger":"label-success" %> <%= (estado_obra_social==0)?"alta_os":"baja_os" %>">Obra Social</span>
                </div>
              </div>
          </div>
          <div class="panel-heading">
            <span class="bold negro">Informaci&oacute;n b&aacute;sica</span>
          </div>
          <div class="panel-body acerca_de">
            <div class="form-group">
              <label class="control-label oh h22">Tipo de Afiliado</label>
              <span class="control-info">
                <%= (id_tipo_afiliado == 0)?"Sin Definir":"" %>
                <%= (id_tipo_afiliado == 1)?"Dueño de Empresa":"" %>
                <%= (id_tipo_afiliado == 2)?"Monotributo":"" %>
                <%= (id_tipo_afiliado == 3)?"Directo":"" %>
                <%= (id_tipo_afiliado == 4)?"Cambio O.S.":"" %>
                <%= (id_tipo_afiliado == 5)?"Jubilado":"" %>
                <%= (id_tipo_afiliado == 6)?"Ama de Casa":"" %>
                <%= (id_tipo_afiliado == 7)?"Pensionado":"" %>
              </span>
            </div>
            <% if (fecha_alta!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">Fecha de Alta</label>
              <span class="control-info"><%= moment(fecha_alta).format("DD/MM/YYYY") %></span>
            </div>
            <% } %>
            <% if (nombreempresa!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">Empresa</label>
              <% if (id_empresa_transporte == 1) { %>
                <a class="alta_empresa">Sin Empresa</a>
              <% } else { %>
                <a href="app/#sindi_empresa/<%= id_empresa_transporte %>"><span class="control-info"><%= (nombreempresa=="")?"Sin Empresa":nombreempresa %><%= (fecha_ingreso_empresa == "0000-00-00")?"":"<p style='font-size:14px'>Ingreso: "+moment(fecha_ingreso_empresa).format("DD/MM/YYYY")+"</p>" %></span></a>
              <% } %>
            </div>
            <% } %>
            <% if (localidad!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">Localidad</label>
              <span class="control-info"><%= (isEmpty(id_localidad)) ? "Sin datos" : localidad %></span>
            </div>
            <% } %>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="panel panel-default mb0">
          <ul class="nav nav-tabs nav-tabs-2" role="tablist">
            <li id="grupo_familiar_link" class="active">
              <a href="#tab1_afiliados" role="tab" data-toggle="tab">
                <i class="fa text-warning fa-users m-r-xs"></i>
                Grupo Familiar
              </a>
            </li>
            <li id="consumos_link">
              <a href="#tab2_afiliados" role="tab" data-toggle="tab">
                <i class="fa text-success fa-file-text m-r-xs"></i>
                Consumos
              </a>
            </li>
            <li id="empresas_link">
              <a href="#tab3_afiliados" role="tab" data-toggle="tab">
                <i class="fa text-info fa-industry m-r-xs"></i>
                Empresas
              </a>
            </li>
            <li id="historial_link">
              <a href="#tab4_afiliados" role="tab" data-toggle="tab">
                <i class="fa text-primary fa-calendar m-r-xs"></i>
                Historial
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div id="tab1_afiliados" class="tab-pane panel-body active">
            </div>
            <div id="tab2_afiliados" class="tab-pane panel-body">
            </div>
            <div id="tab3_afiliados" class="tab-pane panel-body">
              <div class="form-group">
                <% if (id_empresa_transporte < 2) { %>
                  <button class="btn btn-info alta_empresa">Dar de alta</button>
                <% } else { %>
                  <button class="btn btn-info baja_empresa">Dar de baja</button>
                <% } %>
              </div>
              <div class="row">
                <ul class="timelinemauro">
                  <% for(var i=0;i< historial.length; i++) { %>
                    <% var hist = historial[i] %>
                    <% var hidden = 0 %>
                    <% if (hist.evento == "Baja de la Empresa") { %>
                      <li class="timelinemauro-inverted">
                      <div class="timelinemauro-badge danger"><i class="fa fa-industry" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Alta en la Empresa") { %>
                      <li>
                      <div class="timelinemauro-badge success"><i class="fa fa-industry" aria-hidden="true"></i></div>
                    <% } else { var hidden = 1; } %>
                    <div class="timelinemauro-panel <%= (hidden == 1)? 'dn' :'' %> ">
                      <div class="timelinemauro-heading tac">
                        <h5 class="timelinemauro-title"><%= hist.evento %> <%= hist.nombreempresa %> <br> <%= hist.nombreafiliado%></h5>
                      </div>
                      <div class="timelinemauro-body tac">
                        <p><small class="text-muted"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> <%= moment(hist.fecha).format("LL") %> </small></p>
                      </div>
                    </div>
                  </li class="<%= (hidden == 1)? 'dn' :'' %>">
                  <% } %>
                </ul>
              </div>
            </div>

            <div id="tab4_afiliados" class="tab-pane panel-body">
              <div class="row">
                <ul class="timelinemauro">
                  <% for(var i=0;i< historial.length; i++) { %>
                    <% var hist = historial[i] %>
                    <% var hidden = 0 %>
                    <% if (hist.evento == "Cambio de Codigo") { %>
                      <li>
                      <div class="timelinemauro-badge warning"><i class="fa fa-id-card-o" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Alta en Obra Social") { %>
                      <li>
                      <div class="timelinemauro-badge success"><i class="fa fa-id-card" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Baja en Obra Social") { %>
                      <li class="timelinemauro-inverted">
                      <div class="timelinemauro-badge danger"><i class="fa fa-id-card" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Baja en Sindicato") { %>
                      <li class="timelinemauro-inverted">
                      <div class="timelinemauro-badge danger"><i class="fa fa-id-card" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Alta en Sindicato") { %>
                      <li>
                      <div class="timelinemauro-badge success"><i class="fa fa-id-card" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Ingreso al Sistema") { %>
                      <li>
                      <div class="timelinemauro-badge success"><i class="fa fa-sign-in" aria-hidden="true"></i></div>
                    <% } else { var hidden = 1; } %>
                    <div class="timelinemauro-panel <%= (hidden == 1)? 'dn' :'' %> ">
                      <div class="timelinemauro-heading tac">
                        <h5 class="timelinemauro-title"><%= hist.evento %> de <%= hist.nombreafiliado%></h5>
                      </div>
                      <div class="timelinemauro-body tac">
                        <p><small class="text-muted"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> <%= moment(hist.fecha).format("LL") %> </small></p>
                      </div>
                    </div>
                  </li class="<%= (hidden == 1)? 'dn' :'' %>">
                  <% } %>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="sindi_afiliados_panel_template">
	<div class="bg-light lter b-b wrapper-md ng-scope">
		<h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i><b>Afiliados</b>
		</h1>
	</div>
	<div class="wrapper-md ng-scope">
		<div class="panel panel-default">

			<div class="panel-heading oh">
				<div class="row">
					<div class="col-md-6 col-lg-3 sm-m-b">
						<div class="search_container"></div>
					</div>
          <% if (control.check("sindi_afiliados") > 1) { %>
  					<div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
  						<a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
  					</div>
          <% } %>
				</div>
			</div>
			<div class="panel-body">
				<div class="b-a table-responsive">
					<table id="sindi_afiliados_table" class="table table-striped sortable m-b-none default footable">
						<thead>
							<tr>
                <th style="width:140px;" class="sorting tac" data-sort-by="codigo">Afiliado Nº</th>
								<th class="sorting" data-sort-by="nombre">Nombre</th>
                <th style="width:200px"class="sorting tac" data-sort-by="id_localidad">Localidad</th>
                <th style="width:175px" class="sorting tac" data-sort-by="id_tipo_afiliado">Tipo de Afiliado</th>
                <th style="width:90px; padding-left:0px; padding-right:0px" class="tac">Sindicato</th>
                <th style="width:90px; padding-left:0px; padding-right:0px" class="tac">Obra Social</th>
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

<script type="text/template" id="sindi_afiliados_item">
  <td class="ver tac"><span class='text-info'><%= codigo %>-<%= identificador %></span></td>
	<td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver tac"><span class='text-info'><%= localidad %></span></td>
  <td class="ver tac"><span class='text-info'>
    <%= (id_tipo_afiliado == 0)?"Sin Definir":"" %>
    <%= (id_tipo_afiliado == 1)?"Dueño de Empresa":"" %>
    <%= (id_tipo_afiliado == 2)?"Monotributo":"" %>
    <%= (id_tipo_afiliado == 3)?"Directo":"" %>
    <%= (id_tipo_afiliado == 4)?"Cambio O.S.":"" %>
    <%= (id_tipo_afiliado == 5)?"Jubilado":"" %>
    <%= (id_tipo_afiliado == 6)?"Ama de Casa":"" %>
    <%= (id_tipo_afiliado == 7)?"Pensionado":"" %>
  </span></td>
  <td style="padding-left:0px; padding-right:0px" class="tac">
    <% if (estado_sindicato == 0) { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-danger">Baja</span>
    <% } else { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-success">Alta</span>
    <% } %>
  </td>
  <td style="padding-left:0px; padding-right:0px" class="tac">
    <% if (estado_obra_social == 0) { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-danger">Baja</span>
    <% } else { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-success">Alta</span>
    <% } %>
  </td>
</script>

<script type="text/template" id="sindi_afiliados_edit_panel_template">
<form onsubmit="return false" class='modal-content'>
  <div class='modal-header'>
    <div class="row">
      <div class="col-md-6">
        <b><%= (id == undefined) ? 'Nuevo Afiliado' : nombre %></b>
      </div>
      <div class="col-md-6 tar">
        <% if (id == undefined) { %>
          (/)
        <% } else { %>
          <b>(<%= codigo %>/<%= identificador %>)</b>
        <% } %>
      </div>
    </div>
  </div>

  <div class="modal-body pl0 pr0 pt0 pb0">

      <ul id="fichaafiliado" class="nav nav-tabs nav-tabs-2" role="tablist">
        <li id="edicion" class="<%= (tab_activo=="ficha")?"active":"" %>">
          <a href="#tab1_afiliados_edit" role="tab" data-toggle="tab">
            <i class="fa text-warning fa-user m-r-xs"></i>
            Ficha Afiliado
          </a>
        </li>
        <li id="condicionespecial" class="<%= (tab_activo=="condiciones_especiales")?"active":"" %>">
          <a href="#tab2_afiliados_edit" <%= (id == undefined)?"style='display:none'":"" %> role="tab" data-toggle="tab">
            <i class="fa text-danger fa-plus-square m-r-xs"></i>
            Condición Especial
          </a>
        </li>
        <% if (permiso == 3) { %>
          <li id="limites_link" class="<%= (tab_activo=="limites")?"active":"" %>">
            <a href="#tab3_afiliados_edit" <%= (id == undefined)?"style='display:none'":"" %> role="tab" data-toggle="tab">
              <i class="fa text-danger fa-ban m-r-xs"></i>
              Límites
            </a>
          </li>
        <% } %>
      </ul>

      <div class="tab-content">

        <div id="tab1_afiliados_edit" class="tab-pane panel-body <%= (tab_activo=="ficha")?"active":"" %>">
          <div class="">
            <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                  <label class="control-label">Afiliado Nº <i id="label-codigo" style="display: none" class='fa text-danger fa-exclamation-triangle' aria-hidden='true'></i></label>
                  <% var disabled = "" %>
                  <% if (id == undefined && codigo == 0) { %>
                    <?php // TITULAR NUEVO ?>
                    <% disabled = "" %>
                    <% familiardisabled = "" %>
                  <% } else if (id == undefined && codigo != 0) { %>
                    <?php // FAMILIAR NUEVO ?>
                    <% disabled = "disabled" %>
                    <% familiardisabled = "disabled" %>
                  <% } else if (id != undefined && identificador == 0) { %>
                    <?php // EDITAR TITULAR ?>
                    <% disabled = "" %>
                    <% familiardisabled = "" %>
                  <% } else if (id != undefined && identificador != 0) { %>
                    <?php // EDITAR FAMILIAR ?>
                    <% disabled = "disabled" %>
                    <% familiardisabled = "disabled" %>
                  <% } %>
                  <input required <%= disabled %> name="codigo" class="form-control tac no-spinner" autocomplete="off" type="number" step="1" maxlength="8" min="1" id="sindi_afiliados_codigo" value="<%= codigo %>"/>
                </div>
              </div>
              <div class="col-md-1">
                <div class="form-group">
                  <label class="control-label"> </label>
                  <% var disabled = "" %>
                  <% if (id == undefined && codigo == 0) { %>
                    <?php // TITULAR NUEVO ?>
                    <% disabled = "disabled" %>
                  <% } else if (id == undefined && codigo != 0) { %>
                    <?php // FAMILIAR NUEVO ?>
                    <% disabled = "" %>
                  <% } else if (id != undefined && identificador == 0) { %>
                    <?php // EDITAR TITULAR ?>
                    <% disabled = "disabled" %>
                  <% } else if (id != undefined && identificador != 0) { %>
                    <?php // EDITAR FAMILIAR ?>
                    <% disabled = "" %>
                  <% } %>
                  <input required <%= disabled %> name="identificador" class="form-control tac no-spinner" autocomplete="off" type="number" maxlength="2" id="sindi_afiliados_identificador" value="<%= identificador %>"/>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Apellido y Nombres</label>
                  <input required <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" type="text" autocomplete="off" minlength="5" pattern="[a-zA-Z, ]*" id="sindi_afiliados_nombre" value="<%= nombre %>"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Fecha de Alta</label>
                  <input required <%= (!edicion)?"disabled":"" %> name="fecha_alta" class="form-control" type="date" id="sindi_afiliados_fecha_alta" value="<%= fecha_alta %>"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Tipo de Afiliado</label>
                  <select required <%= familiardisabled %> <%= (!edicion)?"disabled":"" %> name="id_tipo_afiliado"  class="form-control" id="sindi_afiliados_id_tipo_afiliado">
                    <option <%= (id_tipo_afiliado == 0)?"selected":"" %> value="0">Sin Definir</option>
                    <option <%= (id_tipo_afiliado == 1)?"selected":"" %> value="1">Dueño de Empresa</option>
                    <option <%= (id_tipo_afiliado == 2)?"selected":"" %> value="2">Monotributo</option>
                    <option <%= (id_tipo_afiliado == 3)?"selected":"" %> value="3">Directo</option>
                    <option <%= (id_tipo_afiliado == 4)?"selected":"" %> value="4">Cambio O.S.</option>
                    <option <%= (id_tipo_afiliado == 5)?"selected":"" %> value="5">Jubilado</option>
                    <option <%= (id_tipo_afiliado == 6)?"selected":"" %> value="6">Ama de Casa</option>
                    <option <%= (id_tipo_afiliado == 7)?"selected":"" %> value="7">Pensionado</option>
                  </select>
                </div>
              </div>
            </div>

            <% if (familiardisabled != "disabled") { %>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label pull-left">Dirección</label>
                  <input <%= (!edicion)?"disabled":"" %> name="domicilio" class="form-control" type="text" minlenght="5" pattern="[a-zA-Z0-9 ]*" id="sindi_afiliados_domicilio" value="<%= domicilio %>"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label pull-left">Localidad</label>
                  <select <%= (!edicion)?"disabled":"" %> name="id_localidad" class="form-control ttc" id="sindi_afiliados_id_localidad">

                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Telefono</label>
                  <input <%= (!edicion)?"disabled":"" %> name="telefono" class="form-control" type="text" pattern="[0-9()-]*" maxlength="15" id="sindi_afiliados_telefono" value="<%= telefono %>"/>
                </div>
              </div>
            </div>
            <% } %>

            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">D.N.I.</label>
                  <input <%= (!edicion)?"disabled":"" %> name="dni" class="form-control" type="text" pattern="[0-9]*" title="Solo numeros!" maxlength="8" id="sindi_afiliados_dni" value="<%= dni %>"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">C.U.I.L.</label>
                  <input <%= (!edicion)?"disabled":"" %> name="cuil" class="form-control" type="text" pattern="[0-9]*" title="Solo numeros!" maxlength="11" id="sindi_afiliados_cuil" value="<%= cuil %>"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label pull-left">Fecha de Nacimiento</label>
                  <input required <%= (!edicion)?"disabled":"" %> name="fecha_nacimiento" class="form-control" type="date" id="sindi_afiliados_fecha_nacimiento" value="<%= fecha_nacimiento %>"/>
                </div>
              </div>
              <% if (familiardisabled != "disabled") { %>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Estado Civil</label>
                  <select <%= (!edicion)?"disabled":"" %> name="estado_civil" class="form-control" id="sindi_afiliados_estado_civil">
                    <option <%= (estado_civil == 0)?"selected":"" %> value="0">Sin Definir</option>
                    <option <%= (estado_civil == 1)?"selected":"" %> value="1">Soltero</option>
                    <option <%= (estado_civil == 2)?"selected":"" %> value="2">Casado</option>
                    <option <%= (estado_civil == 3)?"selected":"" %> value="3">Divorciado</option>
                    <option <%= (estado_civil == 4)?"selected":"" %> value="4">Viudo</option>
                  </select>
                </div>
              </div>
              <% } %>
            </div>
          </div>
        </div>

        <div id="tab2_afiliados_edit" class="tab-pane panel-body <%= (tab_activo=="condiciones_especiales")?"active":"" %>">
          <div class="panel-body pl0 pr0">
            <div class="row">
              <div class="col-md-6">
                <div class="input-group">
                  <span class="input-group-addon">Condición Especial</span>
                  <select class="form-control" id="condiciones_especiales_select"></select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon">Vencimiento</span>
                  <input type="date" id="condicion_especial_fecha_vencimiento" class="form-control">
                </div>
              </div>
              <div class="col-md-2">
                <% if (permiso == 3) { %>
                  <button class="btn btn-success btn-block pull-right solicitaryasignar">Asignar</button>
                <% } else { %>
                  <button class="btn btn-warning btn-block pull-right solicitar">Solicitar</button>
                <% } %>
              </div>
            </div>



            <div class="b-a table-responsive" style="margin-top: 10px">
              <table id="condiciones_especiales" class="table table-small table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th style="width:40%">Condicion</th>
                    <th style="width:20%; text-align:center">Estado</th>
                    <th style="width:20%; text-align:center">Vence</th>
                    <th style="width:20%; text-align:center">Accion</th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var i=0;i< condiciones_especiales.length;i++) { %>
                    <% var c = condiciones_especiales[i] %>
                    <tr data-vencimiento="<%= c.vence %>" data-estado="<%= c.estado %>" id="condicion_<%= c.id_condicion_especial %>">
                      <td style="width:40%"><%= c.nombre %></td>
                      <td style="width:20%; text-align:center">
                        <% if (c.estado == 0) { %>
                          <span class="label label-warning">Solicitado</span>
                        <% } else if (c.estado == 1) { %>
                          <span class="label label-success">Asignado</span>
                        <% } else if (c.estado == 2) { %>
                          <span class="label label-danger">Inactivo</span>
                        <% } %>
                      </td>
                      <td style="width:20%; text-align:center"><%= c.vence %></td>
                      <td style="width:20%; text-align:center">
                        <% if (c.estado == 0 || c.estado == 2 && permiso == 3) { %>
                          <button class="btn btn-success btn-xs asignar">Asignar</button>
                        <% } %>
                        <% if (permiso < 3 && c.estado == 0 || permiso == 3) { %>
                          <button class="btn btn-danger btn-xs eliminar_condicion">Remover</button>
                        <% } %>
                      </td>
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <div id="tab3_afiliados_edit" class="tab-pane <%= (tab_activo=="limites")?"active":"" %>">

          <ul class="nav nav-tabs nav-tabs-2" role="tablist">
            <li id="consultas_link" class="<%= (subtab_activo == "" || subtab_activo == 1)?"active":"" %>">
              <a href="#tab1_limites_edit" role="tab" data-toggle="tab">
                <i class="fa text-danger fa-file-text-o m-r-xs"></i>
                Límite Consultas
              </a>
            </li>
            <li id="recetarios_link" class="<%= (subtab_activo == 2 || subtab_activo == 3 || subtab_activo == 4)?"active":"" %>">
              <a href="#tab2_limites_edit" role="tab" data-toggle="tab">
                <i class="fa text-danger fa-file-text-o m-r-xs"></i>
                Límite Recetarios
              </a>
            </li>
            <li id="practicas_link" class="<%= (subtab_activo == 5)?"active":"" %>">
              <a href="#tab3_limites_edit" role="tab" data-toggle="tab">
                <i class="fa text-danger fa-file-text-o m-r-xs"></i>
                Límite Practicas
              </a>
            </li>
          </ul>
          <div class="tab-content">

            <div id="tab1_limites_edit" class="tab-pane panel-body <%= (subtab_activo == "" || subtab_activo == 1)?"active":"" %>">
              <% var consulta_id = 0 %>
              <% var consulta_cantidad = 2 %>
              <% var consulta_fecha = "" %>
              <% var consulta_motivo = "" %>
              <% var recetarios_id = 0 %>
              <% var recetarios_cantidad = 2 %>
              <% var recetarios_fecha = "" %>
              <% var recetarios_motivo = "" %>
              <% var recetarios_70_id = 0 %>
              <% var recetarios_70_cantidad = 0 %>
              <% var recetarios_70_fecha = "" %>
              <% var recetarios_70_motivo = "" %>
              <% var recetarios_100_id = 0 %>
              <% var recetarios_100_cantidad = 0 %>
              <% var recetarios_100_fecha = "" %>
              <% var recetarios_100_motivo = "" %>

              <% for(var i=0;i< limites.length; i++) { %>
                <% var c = limites[i] %>
                <% if (c.tipo == 1) { %>
                  <% consulta_id = c.id %>
                  <% consulta_cantidad = c.cantidad %>
                  <% consulta_fecha = c.vencimiento %>
                  <% consulta_motivo = c.motivo %>
                <% } else if (c.tipo == 2) { %>
                  <% recetarios_id = c.id %>
                  <% recetarios_cantidad = c.cantidad %>
                  <% recetarios_fecha = c.vencimiento %>
                  <% recetarios_motivo = c.motivo %>
                <% } else if (c.tipo == 3) { %>
                  <% recetarios_70_id = c.id %>
                  <% recetarios_70_cantidad = c.cantidad %>
                  <% recetarios_70_fecha = c.vencimiento %>
                  <% recetarios_70_motivo = c.motivo %>
                <% } else if (c.tipo == 4) { %>
                  <% recetarios_100_id = c.id %>
                  <% recetarios_100_cantidad = c.cantidad %>
                  <% recetarios_100_fecha = c.vencimiento %>
                  <% recetarios_100_motivo = c.motivo %>
                <% } %>
              <% } %>

              <input type="hidden" id="consulta_id" value="<%= consulta_id %>" />
              <input type="hidden" id="recetarios_id" value="<%= recetarios_id %>" />
              <input type="hidden" id="recetarios_70_id" value="<%= recetarios_70_id %>" />
              <input type="hidden" id="recetarios_100_id" value="<%= recetarios_100_id %>" />
              <input type="hidden" id="sindi_afiliados_limite_id" value="0" />

              <label class="control-label">Bonos de Consulta</label>
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <div class="input-group">
                      <input type="number" value="<%= consulta_cantidad %>" min="1" step="1" id="sindi_afiliados_limite_consulta_cantidad" autocomplete="off" class="form-control no-spinner tac"/>
                      <span class="input-group-addon">al mes</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Hasta el dia</span>
                      <input type="date" value="<%= consulta_fecha %>" id="sindi_afiliados_limite_consulta_fecha" autocomplete="off" class="form-control" style="width:160px"/>
                    </div>
                  </div>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Motivo</span>
                      <input type="text" value="<%= consulta_motivo %>" id="sindi_afiliados_limite_consulta_motivo" autocomplete="off" class="form-control"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="tab2_limites_edit" class="tab-pane panel-body <%= (subtab_activo == 2 || subtab_activo == 3 || subtab_activo == 4)?"active":"" %>">
              <label class="control-label">Recetarios <%= (id_tipo_afiliado == 2 || id_tipo_afiliado == 6)?"40%":"50%" %></label>
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <div class="input-group">
                      <input type="number" value="<%= recetarios_cantidad %>" min="2" step="1" id="sindi_afiliados_limite_recetarios_cantidad" autocomplete="off" class="form-control no-spinner tac"/>
                      <span class="input-group-addon">al mes</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Hasta el dia</span>
                      <input type="date" value="<%= recetarios_fecha %>" id="sindi_afiliados_limite_recetarios_fecha" autocomplete="off" class="form-control" style="width:160px"/>
                    </div>
                  </div>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Motivo</span>
                      <input type="text" value="<%= recetarios_motivo %>" id="sindi_afiliados_limite_recetarios_motivo" autocomplete="off" class="form-control"/>
                    </div>
                  </div>
                </div>
              </div>
            <hr class="style4 mt5 mb5">
              <label class="control-label">Recetarios 70% (Resolución 310)</label>
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <div class="input-group">
                      <input type="number" value="<%= recetarios_70_cantidad %>" min="0" step="1" id="sindi_afiliados_limite_recetarios_70_cantidad" autocomplete="off" class="form-control no-spinner tac"/>
                      <span class="input-group-addon">al mes</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Hasta el dia</span>
                      <input type="date" value="<%= recetarios_70_fecha %>" id="sindi_afiliados_limite_recetarios_70_fecha" autocomplete="off" class="form-control" style="width:160px"/>
                    </div>
                  </div>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Motivo</span>
                      <input type="text" value="<%= recetarios_70_motivo %>" id="sindi_afiliados_limite_recetarios_70_motivo" autocomplete="off" class="form-control"/>
                    </div>
                  </div>
                </div>
              </div>
            <hr class="style4 mt5 mb5">
              <label class="control-label">Recetarios 100%</label>
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <div class="input-group">
                      <input type="number" value="<%= recetarios_100_cantidad %>" min="0" step="1" id="sindi_afiliados_limite_recetarios_100_cantidad" autocomplete="off" class="form-control no-spinner tac"/>
                      <span class="input-group-addon">al mes</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Hasta el dia</span>
                      <input type="date" value="<%= recetarios_100_fecha %>" id="sindi_afiliados_limite_recetarios_100_fecha" autocomplete="off" class="form-control" style="width:160px"/>
                    </div>
                  </div>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Motivo</span>
                      <input type="text" value="<%= recetarios_100_motivo %>" id="sindi_afiliados_limite_recetarios_100_motivo" autocomplete="off" class="form-control"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div id="tab3_limites_edit" class="tab-pane panel-body <%= (subtab_activo == 5)?"active":"" %>">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Tipo Practica</label>
                    <select <%= (!edicion)?"disabled":"" %> name="limite" class="form-control ttc" id="sindi_afiliados_tipo_practica_select"></select>
                  </div>
                </div>
                <div class="col-md-1">
                  <div class="form-group">
                    <label class="control-label">Cantidad</label>
                    <input <%= (!edicion)?"disabled":"" %> type="number" min="1" step="1" id="sindi_afiliados_cantidad" name="cantidad" autocomplete="off" class="form-control no-spinner"/>
                  </div>
                </div>
                <div class="col-md-1">
                  <div class="form-group">
                    <label class="control-label">Meses</label>
                    <input <%= (!edicion)?"disabled":"" %> type="number" min="1" step="1" id="sindi_afiliados_meses" name="meses" autocomplete="off" class="form-control no-spinner" />
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Vencimiento</label>
                    <input <%= (!edicion)?"disabled":"" %> type="date" id="sindi_afiliados_vencimiento" name="vencimiento" autocomplete="off" class="form-control no-spinner"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Motivo</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" id="sindi_afiliados_motivo" name="motivo" autocomplete="off" class="form-control no-spinner"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="btn-block">
                      <button class="btn btn-success agregar_limite">Añadir</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="b-a table-responsive">
                <table id="sindi_limites_afiliados_table" class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="sorting" style="width: 26%" data-sort-by="id_tipo_practica">Tipo Practica</th>
                      <th class="sorting" style="width: 12%" data-sort-by="cantidad">Cantidad</th>
                      <th class="sorting" style="width: 12%" data-sort-by="meses">Meses</th>
                      <th class="sorting" style="width: 12%">Vencimiento</th>
                      <th class="" style="width: 50%" data-sort-by="motivo">Motivo</th>
                      <th class="w20"></th>
                      <th class="w20"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< limites.length;i++) { %>
                      <% var c = limites[i] %>
                      <% if (c.tipo != 5) continue %>
                      <tr data-id="<%= c.id %>" data-tipo_practica='<%= c.tipopractica %>' data-vencimiento='<%= c.vencimiento %>' data-id_tipo_practica='<%= c.id_tipo_practica %>' data-cantidad='<%= c.cantidad %>' data-meses='<%= c.meses %>' data-motivo='<%= c.motivo %>'>
                        <td><%= c.tipopractica %></td>
                        <td><%= c.cantidad %></td>
                        <td><%= c.meses %></td>
                        <td><%= c.vencimiento %></td>
                        <td><%= c.motivo %></td>
                        <td><i class="fa fa-pencil editar_limite"></i></td>
                        <td><i class="fa fa-times eliminar_limite text-danger"></i></td>
                      </tr>
                    <% } %>
                  </tbody>
                  <tfoot class="pagination_container hide-if-no-paging"></tfoot>
                </table>
              </div>
            </div>

          </div>

        </div>

      </div>


  </div>
  <div class="modal-footer">
    <a class="btn btn-default pull-left cerrar">Cerrar</a>
    <% if (edicion) { %>
      <button class="btn btn-success">Guardar</button>
    <% } %>
  </div>

</form>
</script>



<script type="text/template" id="sindi_afiliados_grupo_familiar_template">

<div class="row">
  <div class="col-md-6 mb10">
    <a class="btn btn-sm btn-info btn-addon nuevogrp" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Agregar&nbsp;&nbsp;</a>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
  <div class="b-a table-responsive">
    <table id="sindi_afiliados_grupo_familiar_table" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th class="tac pl0 pr0">Codigo</th>
          <th class="pl0 pr0">Nombre</th>
          <th class="tac pl0 pr0">Fecha Nac.</th>
          <th class="pl0 pr0">Cond. Especiales</th>
          <th class="tac pl0 pr0">Sindicato</th>
          <th class="tac pl0 pr0">Obra Social</th>
          <th class="w20"></th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
</div>
</script>


<script type="text/template" id="sindi_afiliados_grupo_familiar_item">
  <td style="width:85px" class="ver tac pl0 pr0"><%= codigo %>-<%= identificador %></td>
  <td class="ver pl0 pr0"><span class='text-info'><%= nombre %></span></td>
  <td style="width:90px;" class="ver tac pl0 pr0"><%= moment(fecha_nacimiento).format("DD/MM/YYYY") %></td>
  <td style="width:200px;" class="pl0 pr0">
    <% for(i = 0; i < condiciones_especiales.length; i++) { %>
      <% var res = condiciones_especiales[i] %>
      <% if (res.estado==0) { %>
        <label class="label label-warning" title="Pendiente de asignación por el auditor"><%= res.nombre %></label>
      <% } else if (res.estado==1) { %>
        <label class="label label-success" title="Condición Especial en vigencia"><%= res.nombre %></label>
      <% } else { %>
        <label class="label label-danger" title="Inactiva, consultar con el auditor"><%= res.nombre %></label>
      <% } %>
    <% } %>
  </td>
  <td class="tac pl0 pr0 <%= (estado_sindicato==0)?"alta_sindicato_item":"baja_sindicato_item" %>" style="width:75px; padding-left:0px; padding-right:0px">
    <% if (estado_sindicato == 0) { %>
      <span class="label label-danger db">Baja</span>
    <% } else { %>
      <span class="label label-success db">Alta</span>
    <% } %>
  </td>
  <td class="tac pl5 pr5 <%= (estado_obra_social==0)?"alta_os_item":"baja_os_item" %>" style="width:75px; padding-left:0px; padding-right:0px">
    <% if (estado_obra_social == 0) { %>
      <span class="label label-danger db">Baja</span>
    <% } else { %>
      <span class="label label-success db">Alta</span>
    <% } %>
  </td>
  <td><button class="btn btn-sm btn-info sacar_bono">+</button></td>
</script>




<script type="text/template" id="sindi_afiliados_consumos_template">
<div class="row">
  <div class="col-md-6">
    <div class="input-group" style="width: 100%">
      <label style="width: 135px" class="input-group-addon">Tipo de Consumo</label>
      <select class="form-control no-model" id="sindi_afiliados_consumos_tipos">
      <option value="">Todos</option>
      <option value="C">Consultas</option>
      <option value="P">Practicas</option>
      <option value="R">Reintegros</option>
      <option value="T">Recetarios</option>
      </select>
    </div>
  </div>
  <div class="col-md-6">
    <div class="input-group" style="width: 100%">
      <label style="width: 135px" class="input-group-addon">Afiliado</label>
      <select class="form-control no-model" id="sindi_afiliados_consumos_grupo_familiar"></select>
    </div>
  </div>
</div>
<div class="row" style="margin-top: 5px; margin-bottom: 5px">
  <div class="col-md-6">
    <div class="input-group" style="width: 100%">
      <label style="width: 135px" class="input-group-addon">Desde</label>
      <input class="form-control" type="date" id="sindi_afiliados_consumos_desde">
    </div>
  </div>
  <div class="col-md-6">
    <div class="input-group" style="width: 100%">
      <label style="width: 135px" class="input-group-addon">Hasta</label>
      <input class="form-control" type="date" id="sindi_afiliados_consumos_hasta">
    </div>
  </div>
</div>
  <div class="col-md-12">
    <div class="b-a table-responsive">
      <table id="sindi_afiliados_consumos_table" class="table table-small table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Numero</th>
            <th>Afiliado</th>
            <th>Importe</th>
            <th>Observaciones</th>
            <th class="w25"></th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="pagination_container hide-if-no-paging"></tfoot>
      </table>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="sindi_afiliados_consumo_item">
  <td><%= fecha %></td>
  <td>
    <%= (tipo=="C")?"Consulta":"" %>
    <%= (tipo=="P")?"Practica":"" %>
    <%= (tipo=="R")?"Reintegro":"" %>
    <%= (tipo=="T")?"Recetario":"" %>
  </td>
  <td><%= numero %></td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td><%= importe %></td>
  <td><%= observaciones %></td>
  <td><i class="fa ver fa-search"></i></td>
</script>


<script type="text/template" id="sindi_afiliados_empresa_alta_baja_template">
  <div class="panel-heading">
    <h4 class="bold"><%= (tipo=="alta") ? "Alta en Empresa" : "Baja de Empresa" %></h4>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">Empresa</label>
      <% if (tipo == "alta") { %>
        <select class="form-control" value="<%= id_empresa_transporte %>" name="id_sindi_empresa" id="sindi_afiliados_empresas"></select>
      <% } else { %>
        <input type="text" class="form-control no-model" disabled value="<%= empresa_transporte %>"/>
        <input type="hidden" name="id_sindi_empresa" id="sindi_afiliados_empresas" value="<%= id_empresa_transporte %>"/>
      <% } %>
    </div>
    <div class="form-group">
      <label class="control-label">Motivo</label>
      <input type="text" id="sindi_afiliados_afiliados_empresas_motivo" name="motivo" autocomplete="off" class="form-control"/>
    </div>
    <div class="form-group">
      <label class="control-label">Fecha</label>
      <input type="date" value="<%= fecha %>" class="form-control no-model" id="sindi_afiliados_empresa_fecha"/>
    </div>
  </div>
  <div class="panel-footer tar oh">
    <button class="btn btn-default cerrar fl">Cerrar</button>
    <button id="boton_guardar_alta_baja_empresa" class="btn btn-success guardar">Guardar</button>
  </div>
</script>

<script type="text/template" id="sindi_afiliados_empresas_template">
  <div class="form-group">
    <% if (id_empresa_transporte < 2) { %>
      <button class="btn btn-info alta_empresa">Dar de alta</button>
    <% } else { %>
      <button class="btn btn-info baja_empresa">Dar de baja</button>
    <% } %>
  </div>
  <div class="b-a table-responsive">
    <table id="sindi_afiliados_consumos_table" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th>Nombre</th>
          <th class="tac">Fecha de Alta</th>
          <th class="tac">Fecha de Baja</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot class="pagination_container hide-if-no-paging"></tfoot>
    </table>
  </div>
</div>
</script>


<script type="text/template" id="sindi_afiliados_empresa_item">
  <td><%= nombre %></td>
  <td class="tac"><%= (fecha_ingreso=="0000-00-00")?"-":fecha_ingreso %></td>
  <td class="tac"><%= (fecha_baja=="0000-00-00")?"-":fecha_baja %></td>
</script>

<script type="text/template" id="sindi_afiliados_sindicato_edit_panel_template">
  <div class="panel-heading">
    <h4 class="bold"><%= (tipo=="alta") ? "Alta en Sindicato" : "Baja de Sindicato" %></h4>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">Motivo</label>
      <input type="text" id="sindi_afiliados_sindicato_motivo" name="motivo" autocomplete="off" class="form-control"/>
    </div>
    <div class="form-group">
      <label class="control-label">Fecha</label>
      <input type="date" value="<%= fecha %>" class="form-control no-model" id="sindi_afiliados_sindicato_fecha"/>
    </div>
  </div>
  <div class="panel-footer tar oh">
    <button class="btn btn-default cerrar fl">Cerrar</button>
    <button class="btn btn-info guardar"><%= (tipo=="alta") ? "Dar de Alta" : "Dar de Baja" %></button>
  </div>
</script>


<script type="text/template" id="sindi_afiliados_os_edit_panel_template">
  <div class="panel-heading">
    <h4 class="bold"><%= (tipo=="alta") ? "Alta en Obra Social" : "Baja de Obra Social" %></h4>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">Motivo</label>
      <input type="text" id="sindi_afiliados_obra_social_motivo" name="motivo" autocomplete="off" class="form-control"/>
    </div>
    <div class="form-group">
      <label class="control-label">Fecha</label>
      <input type="date" value="<%= fecha %>" class="form-control no-model" id="sindi_afiliados_obra_social_fecha"/>
    </div>
  </div>
  <div class="panel-footer tar oh">
    <button class="btn btn-default cerrar fl">Cerrar</button>
    <button class="btn btn-info guardar"><%= (tipo=="alta") ? "Dar de Alta" : "Dar de Baja" %></button>
  </div>
</script>


<script type="text/template" id="sindi_afiliados_buscar_template">
<form onsubmit="return false" class="modal-content">


  <div class="modal-body">

      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-12 col-lg-12 sm-m-b">
            <div class="search_container"></div>
          </div>
        </div>
      </div>


    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="sindi_afiliados_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:110px;" class="tac" data-sort-by="codigo">Afiliado Nº</th>
              <th class="" data-sort-by="nombre">Nombre</th>
              <th style="width:190px"class="tac" data-sort-by="id_localidad">Localidad</th>
              <th style="width:150px" class="tac" data-sort-by="id_tipo_afiliado">Tipo de Afiliado</th>
             <th style="width:90px; padding-left:0px; padding-right:0px" class="tac">Sindicato</th>
       <!--        <th style="width:90px; padding-left:0px; padding-right:0px" class="tac">Obra Social</th> -->
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>

</form>

</script>

<script type="text/template" id="sindi_afiliados_buscar_item">
  <td class="ver tac"><span class='text-info'><%= codigo %>-<%= identificador %></span></td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver tac"><span class='text-info'><%= localidad %></span></td>
  <td class="ver tac"><span class='text-info'>
    <%= (id_tipo_afiliado == 0)?"Sin Definir":"" %>
    <%= (id_tipo_afiliado == 1)?"Dueño de Empresa":"" %>
    <%= (id_tipo_afiliado == 2)?"Monotributo":"" %>
    <%= (id_tipo_afiliado == 3)?"Directo":"" %>
    <%= (id_tipo_afiliado == 4)?"Cambio O.S.":"" %>
    <%= (id_tipo_afiliado == 5)?"Jubilado":"" %>
    <%= (id_tipo_afiliado == 6)?"Ama de Casa":"" %>
    <%= (id_tipo_afiliado == 7)?"Pensionado":"" %>
  </span></td>
 <td style="padding-left:0px; padding-right:0px" class="tac">
    <% if (estado_sindicato == 0) { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-danger">Baja</span>
    <% } else { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-success">Alta</span>
    <% } %>
  </td>
<!--   <td style="padding-left:0px; padding-right:0px" class="tac">
    <% if (estado_obra_social == 0) { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-danger">Baja</span>
    <% } else { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-success">Alta</span>
    <% } %>
  </td> -->
</script>