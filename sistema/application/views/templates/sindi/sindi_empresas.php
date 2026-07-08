<script type="text/template" id="sindi_empresa_detalle_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("sindi_empresas") %>
  <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>Empresa
    / <%= subzona %>-<%= codigo %>-<%= identificador %> / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="rform">
    <div class="row">

      <div class="col-md-3">
        <div class="panel panel-default">
          <div class="panel-body pl10 pr10 pt10 pb10">
            <div class="clearfix mb5">
              <div class="row">
                <div class="col-sm-12 col-xs-12 <%= (estado==0)?"alta_empresa":"baja_empresa" %>">
                  <span class="label cp db fs14 pt5 pb5 pr0 pl0 <%= (estado==0)?"label-danger":"label-success" %>"><%= (estado==0)?"BAJA":"ACTIVA" %> desde <%= moment(fecha_alta_baja).format("DD/MM/YYYY") %></span>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-heading">
            <span class="bold negro">Informaci&oacute;n b&aacute;sica</span>
          </div>
          <div class="panel-body acerca_de">
            <% if (estado==0) { %>
              <div class="form-group">
                <label class="control-label oh h22">Fecha ultimo empleado</label>
                <span class="control-info">testdefecha</span>
              </div>
            <% } %>
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
            <% if (titular1!="") { %>
            <div class="form-group">
              <label class="control-label oh h22">Titular</label>
              <span class="control-info"><%= titular1 %></span>
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
            <% if (estudiocontable != "") { %>
              <div class="form-group">
                <label class="control-label oh h22">Estudio Contable</label>
                <a href="app/#sindi_estudio_contable/<%= id_estudio_contable %>"><span class="control-info"><%= estudiocontable %></span></a>
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
            <li id="empleados_activos_link" class="active">
              <a href="#tab1_empleados" role="tab" data-toggle="tab">
                <i class="fa text-success fa-users m-r-xs"></i>
                Empleados Activos
              </a>
            </li>
            <li id="empleados_baja_link">
              <a href="#tab2_empleados" role="tab" data-toggle="tab">
                <i class="fa text-danger fa-user-times m-r-xs"></i>
                Empleados Baja
              </a>
            </li>
            <li id="historial_link">
              <a href="#tab3_historial" role="tab" data-toggle="tab">
                <i class="fa text-info fa-calendar m-r-xs"></i>
                Historial
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div id="tab1_empleados" class="tab-pane panel-body active">
              <div class="b-a table-responsive">
                <table class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="w20">#</th>
                      <th>Afiliado Nº</th>
                      <th>Nombre</th>
                      <th>Fecha Ingreso</th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< afiliados_activos.length; i++) { %>
                      <% var afi = afiliados_activos[i] %>
                      <tr>
                        <td><%= (i+1) %></td>
                        <td><a href="app/#sindi_afiliado/<%= afi.id %>"><%= afi.codigo+"-"+afi.identificador %></a></td>
                        <td><a href="app/#sindi_afiliado/<%= afi.id %>" class="text-info"><%= afi.nombre %></a></td>
                        <td><%= moment(afi.fecha_ingreso_empresa).format("DD/MM/YYYY") %></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>
            </div>
            <div id="tab2_empleados" class="tab-pane panel-body">
              <div class="b-a table-responsive">
                <table class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="w20">#</th>
                      <th>Afiliado Nº</th>
                      <th>Nombre</th>
                      <th>Fecha Baja</th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< afiliados_inactivos.length; i++) { %>
                      <% var afi = afiliados_inactivos[i] %>
                      <tr>
                        <td><%= (i+1) %></td>
                        <td><a href="app/#sindi_afiliado/<%= afi.id %>"><%= afi.codigo+"-"+afi.identificador %></a></td>
                        <td><a href="app/#sindi_afiliado/<%= afi.id %>" class="text-info"><%= afi.nombre %></a></td>
                        <td><%= moment(afi.fecha_ingreso_empresa).format("DD/MM/YYYY") %></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>
            </div>
            <div id="tab3_historial" class="tab-pane panel-body">
              <div class="row">
                <ul class="timelinemauro">
                  <% for(var i=0;i< historial.length; i++) { %>
                    <% var hist = historial[i] %>

                    <% if (hist.evento == "Alta en Sistema") { %>
                      <div class="timelinemauro-badge success"><i class="fa fa-sign-in" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Empresa dada de baja") { %>
                      <div class="timelinemauro-badge danger"><i class="fa fa-sign-out" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Empresa dada de alta") { %>
                      <div class="timelinemauro-badge success"><i class="fa fa-industry" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Baja en Empresa") { %>
                      <div class="timelinemauro-badge danger"><i class="fa fa-industry" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Alta en Sindicato") { %>
                      <div class="timelinemauro-badge success"><i class="fa fa-id-card" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Baja en Sindicato") { %>
                      <div class="timelinemauro-badge danger"><i class="fa fa-id-card" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Alta en Obra Social") { %>
                      <div class="timelinemauro-badge success"><i class="fa fa-id-card-o" aria-hidden="true"></i></div>
                    <% } else if (hist.evento == "Baja en Obra Social") { %>
                      <div class="timelinemauro-badge danger"><i class="fa fa-id-card-o" aria-hidden="true"></i></div>
                    <% } %>
                    <div class="timelinemauro-panel">
                      <div class="timelinemauro-heading tac">
                        <h5 class="timelinemauro-title"><%= hist.evento %> de <%= hist.nombreempresa %></h5>
                      </div>
                      <div class="timelinemauro-body tac">
                        <p><small class="text-muted"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> <%= moment(hist.fecha).format("LL") %> </small></p>
                      </div>
                    </div>
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

<script type="text/template" id="sindi_empresas_panel_template">
	<div class="bg-light lter b-b wrapper-md ng-scope">
		<h1 class="m-n font-thin h3"><i class="fa fa-industry icono_principal"></i><b>Empresas</b>
		</h1>
	</div>
	<div class="wrapper-md ng-scope">
		<div class="panel panel-default">

			<div class="panel-heading oh">
				<div class="row">
					<div class="col-md-6 col-lg-3 sm-m-b">
						<div class="search_container"></div>
					</div>
          <% if (control.check("sindi_empresas") > 1) { %>
  					<div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
  						<a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
  					</div>
          <% } %>
				</div>
			</div>
			<div class="panel-body">
				<div class="b-a table-responsive">
					<table id="sindi_empresas_table" class="table table-striped sortable m-b-none default footable">
						<thead>
							<tr>
                <th style="width:140px;" class="sorting tac" data-sort-by="codigo">Empresa Nº</th>
								<th class="sorting" data-sort-by="nombre">Nombre</th>
                <th style="width:200px"class="sorting" data-sort-by="id_localidad">Localidad</th>
                <th style="width:90px; padding-left:0px; padding-right:0px" class="tac">Afiliados</th>
                <th style="width:90px; padding-left:0px; padding-right:0px" class="tac">Estado</th>
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


<script type="text/template" id="sindi_empresas_item">
  <td class="ver tac"><span class='text-info'><%= subzona %>-<%= codigo %>-<%= identificador %></span></td>
	<td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><span class='text-info'><%= localidad %></span></td>
  <td class="ver tac"><span class='text-info'><%= afiliados_activos.length %></span></td>
  <td style="padding-left:0px; padding-right:0px" class="tac">
    <% if (estado == 0) { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-danger">Baja</span>
    <% } else { %>
      <span class="label db fs14 pt5 pb5 ml5 mr5 label-success">Alta</span>
    <% } %>
  </td>
</script>

<script type="text/template" id="sindi_empresas_edit_panel_template">
<form onsubmit="return false" class='modal-content'>
  <div class='modal-header'>
    <div class="row">
      <div class="col-md-6">
        <b><%= (id == undefined) ? 'Nueva Empresa' : nombre %></b>
      </div>
      <div class="col-md-6 tar">
        <% if (id == undefined) { %>
         <b>(/)</b>
        <% } else { %>
          <b>(<%= subzona %>/<%= codigo %>/<%= identificador %>)</b>
        <% } %>
      </div>
    </div>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-xs-1">
        <div class="form-group">
          <label class="control-label">Sub</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="subzona" class="form-control tac labelcontrol" maxlength="2" id="sindi_empresas_subzona" value="<%= subzona %>"/>
        </div>
      </div>
      <div class="col-xs-2">
        <div class="form-group">
          <label class="control-label">Codigo</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="codigo" class="form-control tac labelcontrol" maxlength="8" id="sindi_empresas_codigo" value="<%= codigo %>"/>
        </div>
      </div>
      <div class="col-xs-1">
        <div class="form-group">
          <label class="control-label">ID</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="identificador" class="form-control tac labelcontrol" maxlength="2" id="sindi_empresas_identificador" value="<%= identificador %>"/>
        </div>
      </div>
      <div class="col-xs-1">
        <i id="label-codigo" style="display: none" class='fa text-danger fa-exclamation-triangle' aria-hidden='true'></i></label>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Nombre (Razón Social)</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" class="form-control" id="sindi_empresas_nombre" value="<%= nombre%>"/>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Tipo de Sociedad</label>
          <select <%= (!edicion)?"disabled":"" %> type="text" name="id_tipo_sociedad" class="form-control tac" id="sindi_empresas_id_tipo_sociedad">
            <option <%= (id_tipo_sociedad == 0)?"selected":"" %> value="0">Sin Definir</option>
            <option <%= (id_tipo_sociedad == 1)?"selected":"" %> value="1">S.A.</option>
            <option <%= (id_tipo_sociedad == 2)?"selected":"" %> value="2">S.R.L.</option>
            <option <%= (id_tipo_sociedad == 3)?"selected":"" %> value="3">S.C.P.A.</option>
            <option <%= (id_tipo_sociedad == 4)?"selected":"" %> value="4">S.H.</option>
            <option <%= (id_tipo_sociedad == 4)?"selected":"" %> value="5">Ninguna</option>
          </select>
        </div>
      </div>
      <% if (id == undefined) { %>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Fecha de Alta</label>
            <input type="date" name="fecha_alta" required class="form-control" id="sindi_empresas_fecha_alta" value="<%= fecha_alta %>" />
          </div>
        </div>
      <% } %>
    </div>

    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">C.U.I.T.</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="cuit" class="form-control" id="sindi_empresas_cuit" value="<%= cuit%>"/>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Condición IVA</label>
          <select <%= (!edicion)?"disabled":"" %> type="text" name="id_iva" class="form-control tac" id="sindi_empresas_id_iva">
            <option <%= (id_iva == 0)?"selected":"" %> value="0">Sin Definir</option>
            <option <%= (id_iva == 1)?"selected":"" %> value="1">Responsable Inscripto</option>
            <option <%= (id_iva == 2)?"selected":"" %> value="2">Responsable No Inscripto</option>
            <option <%= (id_iva == 3)?"selected":"" %> value="3">Consumidor Final</option>
            <option <%= (id_iva == 4)?"selected":"" %> value="4">Exento</option>
            <option <%= (id_iva == 5)?"selected":"" %> value="5">Otros</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Estudio Contable</label>
          <select <%= (!edicion)?"disabled":"" %>  name="id_estudio_contable" class="form-control tac" id="sindi_empresas_id_estudio_contable">
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Dirección</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="domicilio" class="form-control" id="sindi_empresas_domicilio" value="<%= domicilio%>"/>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Localidad</label>
          <select <%= (!edicion)?"disabled":"" %> type="text" name="id_localidad" class="form-control tac" id="sindi_empresas_id_localidad"/>

          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Email</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="email" class="form-control" id="sindi_empresas_email" value="<%= email%>"/>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Telefono</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" class="form-control" id="sindi_empresas_telefono" value="<%= telefono%>"/>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Titular</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="titular1" class="form-control" id="sindi_empresas_titular1" value="<%= titular1 %>"/>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Titular (N° 2)</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="titular2" class="form-control" id="sindi_empresas_titular1" value="<%= titular2 %>"/>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Titular (N° 3)</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="titular3" class="form-control" id="sindi_empresas_titular3" value="<%= titular3 %>"/>
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

<script type="text/template" id="sindi_empresas_alta_baja_panel_template">
  <div class="panel-heading">
    <h4 class="bold"><%= (tipo=="alta") ? "Alta de Empresa" : "Baja de Empresa" %></h4>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">Motivo</label>
      <input type="text" id="sindi_empresas_alta_baja_motivo" autocomplete="off" class="form-control no-model"/>
    </div>
    <div class="form-group">
      <input type="hidden" id="sindi_empresas_alta_baja_id" value="<%= id_sindi_empresa %>" name="id_sindi_empresa" autocomplete="off" class="form-control"/>
    </div>
    <div class="form-group">
      <label class="control-label">Fecha</label>
      <input type="date" value="<%= fecha %>" class="form-control no-model" id="sindi_empresas_alta_baja_fecha"/>
    </div>
  </div>
  <div class="panel-footer tar oh">
    <button class="btn btn-default cerrar fl">Cerrar</button>
    <button class="btn btn-info guardar"><%= (tipo=="alta") ? "Dar de Alta" : "Dar de Baja" %></button>
  </div>
</script>