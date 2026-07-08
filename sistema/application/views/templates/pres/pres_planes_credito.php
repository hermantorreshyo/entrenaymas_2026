<script type="text/template" id="pres_planes_credito_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <b>Planes de cr&eacute;dito</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#pres_plan_credito"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="pres_planes_credito_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="codigo">C&oacute;digo</th>
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


<script type="text/template" id="pres_planes_credito_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><span><%= codigo %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="pres_planes_credito_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n 
    / Planes de cr&eacute;dito
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto"></div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Nombre</label>
                <% if (edicion) { %>
                  <input type="text" name="nombre" class="form-control" id="pres_planes_credito_nombre" value="<%= nombre %>"/>
                <% } else { %>
                  <span><%= nombre %></span>
                <% } %>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">C&oacute;digo</label>
                    <% if (edicion) { %>
                      <input type="text" name="codigo" class="form-control" id="pres_planes_credito_codigo" value="<%= codigo %>"/>
                    <% } else { %>
                      <span><%= codigo %></span>
                    <% } %>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Coeficiente punitorio</label>
                    <input type="text" name="coeficiente_punitorio" class="form-control" id="pres_planes_credito_coeficiente_punitorio" value="<%= coeficiente_punitorio %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">D&iacute;as para primera cuota</label>
                    <input type="text" name="dias_primera_cuota" class="form-control" id="pres_planes_credito_dias_primera_cuota" value="<%= dias_primera_cuota %>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="pres_planes_credito_hab_monotributista" name="hab_monotributista" class="checkbox" value="1" <%= (hab_monotributista == 1)?"checked":"" %> >
                    <i></i>
                    Habilitado para monotributista
                  </label>
                </div>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="pres_planes_credito_hab_dependencia" name="hab_dependencia" class="checkbox" value="1" <%= (hab_dependencia == 1)?"checked":"" %> >
                    <i></i>
                    Habilitado para trabajador en relacion de dependencia
                  </label>
                </div>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="pres_planes_credito_hab_jubilado" name="hab_jubilado" class="checkbox" value="1" <%= (hab_jubilado == 1)?"checked":"" %> >
                    <i></i>
                    Habilitado para jubilado
                  </label>
                </div>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="pres_planes_credito_hab_otro_estado_laboral" name="hab_otro_estado_laboral" class="checkbox" value="1" <%= (hab_otro_estado_laboral == 1)?"checked":"" %> >
                    <i></i>
                    Habilitado para otro estado laboral
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Documentacion requerida</label>
                <select multiple id="pres_planes_credito_documentacion" style="width: 100%">
                  <% for (var i=0; i< documentacion.length; i++) { %>
                    <% var o = documentacion[i] %>
                    <option selected><%= o %></option>
                  <% } %>
                </select>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  Cuotas y tasas de inter&eacute;s
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Administre las diferentes tasas de intereses segun la cantidad de cuotas del pr&eacute;stamo.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (cuotas.length > 0)?'display:block':'' %>">

            <div class="padder">
              <div class="m-b row clearfix">
                <div class="form-group col-sm-6">
                  <label class="control-label">Cuota n&uacute;mero</label>
                  <input type="text" class="form-control no-model" id="pres_planes_credito_cuota" />
                </div>
                <div class="form-group col-sm-6">
                  <label class="control-label">Tasa de inter&eacute;s</label>
                  <div class="input-group">
                    <input id="pres_planes_credito_tasa_interes" value="" type="text" class="form-control no-model"/>
                    <span class="input-group-btn">
                      <a id="cuota_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table id="pres_planes_credito_cuotas_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th style="display: none"></th>
                      <th>Cuota</th>
                      <th>Tasa de interes</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< cuotas.length;i++) { %>
                      <% var p = cuotas[i] %>
                      <tr>
                        <td class="editar_cuota"><span class="text-info editar_cuota cuota"><%= p.cuota %></span></td>
                        <td class="tasa_interes editar_cuota"><%= p.tasa_interes %></td>
                        <td class="tar">
                          <button class="btn btn-sm btn-white eliminar_cuota"><i class="fa fa-trash"></i></button>
                        </td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <% if (edicion) { %>
          <button class="btn guardar btn-success">Guardar</button>
        <% } %>
      </div>
    </div>
  </div>
</div>

</script>