<script type="text/template" id="cupones_descuentos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-globe icono_principal"></i> Web / <b>Cupones de Descuento</b></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("cupones_descuentos") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#cupon_descuento"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cupones_descuentos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="codigo">Codigo</th>
                <th class="sorting" data-sort-by="descuento">Descuento</th>
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


<script type="text/template" id="cupones_descuentos_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><span class='text-info'><%= codigo %></span></td>
  <td class="ver"><span class='text-info'><%= descuento %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
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

<script type="text/template" id="cupones_descuentos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-globe icono_principal"></i> 
    Web / Cupones de Descuento
    / <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
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
              	<div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" autocomplete="off" class="form-control" id="cupones_descuentos_nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Codigo</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="codigo" autocomplete="off" class="form-control" id="cupones_descuentos_codigo" value="<%= codigo %>"/>
                  </div>
                </div>
              </div>

              <div class="row">
              	<div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Descuento (%)</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="descuento" autocomplete="off" class="form-control" id="cupones_descuentos_descuento" value="<%= descuento %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Desde</label>
			              <div class="input-group">
			              	<input <%= (!edicion)?"disabled":"" %> type="text" name="fecha_desde" autocomplete="off" class="form-control" id="cupones_descuentos_fecha_desde" value="<%= fecha_desde %>"/>
			                <span class="input-group-btn">
			                  <button tabindex="-1" type="button" class="btn btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
			                </span>              
			              </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Hasta</label>
			              <div class="input-group">
			              	<input <%= (!edicion)?"disabled":"" %> type="text" name="fecha_hasta" autocomplete="off" class="form-control" id="cupones_descuentos_fecha_hasta" value="<%= fecha_hasta %>"/>
			                <span class="input-group-btn">
			                  <button tabindex="-1" type="button" class="btn btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
			                </span>              
			              </div>
                  </div>
                </div>              
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
</form>

</script>