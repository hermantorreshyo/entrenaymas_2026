<script type="text/template" id="pres_tareas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-calendar icono_principal"></i><b>Tareas</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="">
          <div class="sm-m-b">
            <div class="dib w300">
              <div class="input-group">
                <input type="text" placeholder="Buscar..." autocomplete="off" id="pres_tareas_texto" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>
            <% if (control.check("almacenes")>0) { %>
              <div class="dib w150">
                <div class="input-group">
                  <select class="form-control" id="listado_saldos_proveedores_sucursales">
                    <option value="0">Sucursal</option>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  </select>
                </div>
              </div>
            <% } %>
            <?php /*
            <% if (!lightbox) { %>
              <div class="padder pull-right">
                <a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
              </div>
            <% } %>
            */ ?>
          </div>
        </div>
      </div>
      <ul class="nav nav-tabs nav-tabs-2" role="tablist">
        <li class="active">
          <a id="tab1_link" href="#tab1" role="tab" data-toggle="tab">
            <i class="fa text-warning fa-file-text m-r-xs"></i>
            Listado
          </a>
        </li>
        <li>
          <a id="tab2_link" href="#tab2" role="tab" data-toggle="tab">
            <i class="fa text-success fa-calendar m-r-xs"></i>
            Cronograma
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="tab1" class="tab-pane panel-body active">
          <div class="b-a table-responsive">
            <table id="pres_tareas_table" class="m-b-none table-small table sortable default footable">
              <thead>
                <tr>
                  <th>Usuario</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Cliente</th>
                  <th>Tarea</th>
                  <th>Realizada</th>
                  <% if (permiso > 1) { %>
                    <th class="w100 th_acciones"></th>
                  <% } %>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot class="pagination_container hide-if-no-paging"></tfoot>
            </table>
          </div>
        </div>
        <div id="tab2" class="tab-pane panel-body">
          <div id="pres_tareas_calendario"></div>
        </div>
      </div>
    </div>
  </div>  
</script>

<script type="text/template" id="pres_tareas_item">
  <% var clase = "";
  if (anulado == 1) clase = "text-danger";
  %>
  <td class="ver"><span class='<%= clase %>'><%= banco %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= numero %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= orden_pago %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= proveedor %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= fecha_emision %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= fecha_cobro %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= fecha_debitado %></span></td>
  <td class="ver tar"><span class='<%= clase %>'>$ <%= Number(monto).toFixed(2) %></span></td>
  <% if (permiso > 1) { %>
    <td class="tar <%= clase %>">
      <?php /*
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      */ ?>
      <div class="btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="pres_tareas_edit_panel_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b><%= (id == undefined) ? "Nuevo cheque":"Editar cheque" %></b>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Numero</label>
          <input type="text" name="numero" class="form-control" id="pres_tareas_numero" value="<%= numero %>"/>
        </div>  
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Banco</label>
          <select class="form-control" id="pres_tareas_bancos">
            <option value="0">Seleccione</option>
            <% for(var i=0;i< bancos.length;i++) { %>
            <% var banco = bancos[i] %>
            <option <%= (banco.id == id_banco)?"selected":"" %> value="<%= banco.id %>"><%= banco.nombre %></option>
            <% } %>              
          </select>
        </div>
      </div>  
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Monto</label>
          <input type="text" name="monto" class="form-control" id="pres_tareas_monto" value="<%= monto %>"/>
        </div>  
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha de emisi&oacute;n</label>
          <div class="input-group">
            <input type="text" name="fecha_emision" class="form-control" id="pres_tareas_fecha_emision" value="<%= fecha_emision %>"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>  
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha de cobro</label>
          <div class="input-group">
            <input type="text" name="fecha_cobro" class="form-control" id="pres_tareas_fecha_cobro" value="<%= fecha_cobro %>"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha de debitado</label>
          <div class="input-group">
            <input type="text" name="fecha_debitado" class="form-control" id="pres_tareas_fecha_debitado" value="<%= fecha_debitado %>"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>
    </div>
    <% if (!lightbox) { %>
      <div class="form-group">
        <label class="i-checks m-t-xs m-r">
          <input type="checkbox" name="anulado" id="pres_tareas_anulado" class="checkbox" value="1" <%= (anulado == 1)?"checked":"" %> >
          <i></i> El cheque fue anulado
        </label>
      </div>
      <input type="text" placeholder="Observaciones" name="motivo" class="form-control" id="pres_tareas_motivo" value="<%= motivo %>"/>
    <% } %>
  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn btn-success guardar">Guardar</button>
  </div>
</div>
</script>