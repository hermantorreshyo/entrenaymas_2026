<script type="text/template" id="repartidores_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-motorcycle icono_principal"></i><b>Repartidores</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("repartidores") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#repartidor"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="repartidores_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="telefono">Telefono</th>
                <th class="sorting" data-sort-by="limite_efectivo">Efectivo / Limite</th>
                <th></th>
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


<script type="text/template" id="repartidores_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><%= telefono %></td>
  <td class="ver"><%= efectivo %> / <%= limite_efectivo %></td>
  <td><a href="app/#cuenta_repartidor/<%= id %>" class="label ver_cta_cte bg-success">Ver cuenta</a></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" data-toggle="tooltip" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
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

<script type="text/template" id="repartidores_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-motorcycle icono_principal"></i>Repartidores
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
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
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" autocomplete="off" class="form-control" id="repartidores_nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="email" autocomplete="off" class="form-control" id="repartidores_email" value="<%= email %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Contraseña</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="password" autocomplete="off" class="form-control" id="repartidores_password" value="<%= password %>"/>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">CUIT</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="cuit" autocomplete="off" class="form-control" id="repartidores_cuit" value="<%= cuit %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Telefono</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" autocomplete="off" class="form-control" id="repartidores_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Limite Efectivo</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="limite_efectivo" autocomplete="off" class="form-control" id="repartidores_limite_efectivo" value="<%= limite_efectivo %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Metros por minuto</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="metros_por_minuto" autocomplete="off" class="form-control" id="repartidores_metros_por_minuto" value="<%= metros_por_minuto %>"/>
                  </div>
                </div>
              </div>

              <% if (edicion) { %>
                <?php
                single_upload(array(
                  "name"=>"path",
                  "label"=>"Foto",
                  "url"=>"/sistema/repartidores/function/save_image/",
                  "width"=>(isset($empresa->config["repartidor_image_width"]) ? $empresa->config["repartidor_image_width"] : 400),
                  "height"=>(isset($empresa->config["repartidor_image_height"]) ? $empresa->config["repartidor_image_height"] : 400),
                  "quality"=>(isset($empresa->config["repartidor_image_quality"]) ? $empresa->config["repartidor_image_quality"] : 0.92),
                )); ?>
              <% } %>

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


<?php /*
// ===========================================================
// CAJAS DE REPARTIDORES

*/?>

<script type="text/template" id="repartidores_cajas_movimientos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <div class="row clearfix padder">
      <% var modulo = control.get("repartidores") %>
      <h1 class="m-n font-thin h3 pull-left"><a style="color:inherit" href="app/#repartidores"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.nombre_es %></a>
        / <b>Cuenta Corriente</b>
      </h1>
    </div>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-7 sm-m-b">
            <div class="form-inline">    
              <div class="input-group" style="width: 200px;">
                <select id="repartidores_cajas_movimientos_repartidores" class="form-control no-model"></select>
              </div>
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Desde" id="repartidores_cajas_movimientos_desde" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Hasta" id="repartidores_cajas_movimientos_hasta" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="form-group">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-filter mr5"></i> Filtros</button>
              </div>
            </div>
          </div>
          <div class="col-md-5 sm-m-b">

            <% if (permiso > 2) { %>
              <div class="btn-group pull-right dropdown ml5">
                <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                  <i class="fa fa-cog"></i><span>Opciones</span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
                </ul>
              </div>            
              <div class="btn-group pull-right ml5">
                <button class="btn btn-danger nuevo_gasto">Egreso</span>
                </button>
              </div>
              <div class="btn-group pull-right ml5">
                <button class="btn btn-success nuevo_ingreso">Ingreso</span>
                </button>
              </div>
            <% } %>

          </div>
        </div>
      </div>
      <% var display_search = (id_concepto != 0) ? "display:block":"display:none" %>
      <div class="advanced-search-div bg-light dk" style="<%= display_search %>">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">    
            <div class="form-group">
              <select class="form-control no-model" id="repartidores_cajas_movimientos_conceptos">
                <option value="0">Concepto</option>
                <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="bulk_action panel-body resumen pb0">
        <div class="row">
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
              <div id="repartidores_cajas_movimientos_monto" class="h3 font-thin text-white block">0</div>
              <span class="text-muted text-md pt5 db">Total</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
              <span id="repartidores_cajas_movimientos_cantidad" class="font-thin h3 block">0</span>
              <span class="text-muted text-md pt5 db">Operaciones</span>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="repartidores_cajas_movimientos_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="w180 exportable">Fecha</th>
                <th class="exportable">Concepto</th>
                <th class="exportable">Descripci&oacute;n</th>
                <th class="exportable tar w150">Monto</th>
                <th class="exportable tar w150">Saldo</th>
                <th style="width:20px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="repartidor_caja_movimiento_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <%= (id == undefined)?"Cargar":"Editar" %> <%= (tipo==0)?"Ingreso":"Egreso" %>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha</label>
          <div class="input-group">
            <input <%= (permiso<=2)?"disabled":"" %> type="text" value="<%= fecha %>" name="fecha" class="form-control esc" id="repartidores_cajas_movimientos_fecha"/>
            <span class="input-group-btn">
              <button <%= (permiso<=2)?"disabled":"" %> tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Monto</label>
          <input <%= (permiso<=2)?"disabled":"" %> type="text" value="<%= monto %>" name="monto" class="form-control esc" id="repartidores_cajas_movimientos_monto"/>
        </div>
      </div>      
    </div>
    <div class="form-group">
      <label class="control-label">Concepto</label>
      <div class="input-group">
        <select <%= (permiso<=2)?"disabled":"" %> class="form-control no-model esc" id="repartidores_cajas_movimientos_tipo">
          <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
        </select>
        <span class="input-group-btn">
          <button <%= (permiso<=2)?"disabled":"" %> tabindex="-1" class="btn btn-info w100 agregar_concepto">
            <?php echo lang(array(
              "es"=>"+ Agregar",
              "en"=>"+ Add",
            )); ?>
          </button>  
        </span>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label">Descripci&oacute;n</label>
      <textarea <%= (permiso<=2)?"disabled":"" %> name="observaciones" class="h80 form-control"><%= observaciones %></textarea>
    </div>

    <% if (permiso > 2) { %>
      <?php
      single_file_upload(array(
        "name"=>"path",
        "label"=>lang(array("es"=>"Archivo adjunto","en"=>"Atacchment file")),
        "url"=>"/sistema/cajas_movimientos/function/save_file/",
      )); ?>
    <% } %>

  </div>
  <div class="panel-footer clearfix">
    <button class="btn btn-default fl cerrar">Cerrar</button>
    <% if (permiso > 2) { %>
      <button class="btn btn-success fr guardar">Guardar</button>
    <% } %>
  </div>
</div>
</script>


<script type="text/template" id="repartidores_cajas_movimientos_item">
  <% var clase = ((estado == 0) ? "text-info" : "text-danger") %>
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row2" value="<%= id %>" data-total="<%= (tipo==1)?"-":"" %><%= monto %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class='ver exportable <%= clase %>'><%= fecha %></td>
  <td class='ver exportable'><span class="<%= clase %>"><%= concepto %></span></td>
  <td class='exportable'><span class="ver <%= clase %>"><%= observaciones %></span>
    <% if (!isEmpty(path)) { %>
      <a class="fr text-info fs16" href="/sistema/<%= path %>" target="_blank"><i class="fa fa-file-o"></i></a>
    <% } %>
  </td>
  <td class="ver exportable tar number <%= clase %>">$ <%= (tipo==1)?"-":"" %><%= Number(monto).format(2) %></td>
  <td class="ver exportable tar number <%= clase %>">$ <%= Number(subtotal).format(2) %></td>
  <td class="p5 td_acciones">
    <% if (permiso > 2) { %>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    <% } %>
  </td>
</script>