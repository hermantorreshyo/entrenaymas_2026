<script type="text/template" id="descuentos_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Productos
  / <b>Descuentos</b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
  
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="input-group">
            <input type="text" id="descuentos_texto" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
            <span class="input-group-btn">
              <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span><?php echo lang(array("es"=>"Opciones","en"=>"Options")); ?></span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void" class="imprimir">Imprimir</a></li>
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            </ul>
          </div>
          <% if (control.check("descuentos")>1) { %>
            <button class="btn pull-right ml5 btn-info btn-addon nuevo">
              <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
            </button>
          <% } %>
        </div>
      </div>
    </div>
    <div class="advanced-search-div bg-light dk">
      <div class="wrapper oh">
        <h4 class="m-t-xs"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"B&uacute;squeda Avanzada:","en"=>"Advanced Search:")); ?></h4>
        <div class="cb">
          <div class="input-group fl" style="width: 140px;">
            <input type="text" placeholder="Fecha" id="descuentos_fecha" class="form-control">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
          <div class="form-group fl" style="width: 200px; display: inline-block">
            <select id="descuentos_almacenes" class="form-control w200">
              <% if (ID_SUCURSAL != 0) { %>
                <% for(var i=0;i< window.almacenes.length;i++) { %>
                  <% var o = almacenes[i]; %>
                  <% if (ID_SUCURSAL == o.id) { %>
                    <option selected value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                <% } %>                    
              <% } else { %>
                <option value="0">Sucursal</option>
                <% for(var i=0;i< window.almacenes.length;i++) { %>
                  <% var o = almacenes[i]; %>
                  <option value="<%= o.id %>"><%= o.nombre %></option>
                <% } %>
              <% } %>
            </select>          
          </div>
          <div class="form-group dib fl">
            <button class="btn btn-default buscar ml10"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
          </div>
        </div>
      </div>
    </div>

    <div class="bulk_action wrapper pb0">
      <button class="btn btn-default eliminar_multiple btn-addon"><i class="icon fa fa-times"></i>Eliminar</button>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="descuentos_tabla" class="table table-small sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;">
                <label class="i-checks m-b-none">
                  <input class="esc sel_todos" type="checkbox"><i></i>
                </label>
              </th>
              <th class="w200">Sucursal</th>
              <th class="w120">Codigo</th>
              <th class="w120">EAN</th>
              <th class="w120">Prov.</th>
              <th>Descripcion</th>
              <th class="w120">Desde</th>
              <th class="w120">Hasta</th>
              <th class="tar w120">Precio Final</th>
              <th class="w20 th_acciones"></th>
            </tr>
          </thead>
          <tbody class="tbody"></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="descuentos_item_resultados_template">
  <% var clase = "" %>
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="<%= clase %>"><%= almacen %></td>
  <td class="<%= clase %>"><%= codigo %></td>
  <td class="<%= clase %>"><%= codigo_barra.replace(/\#\#\#/g,"<br/>") %></td>
  <td class="<%= clase %>"><%= custom_10 %></td>
  <td class="<%= clase %>"><span class="text-info"><%= nombre %></span></td>
  <td class="<%= clase %>"><%= desde %></td>
  <td class="<%= clase %>"><%= hasta %></td>
  <td class="<%= clase %> tar"><%= Number(precio_final).toFixed(2) %></td>
  <td>
    <% if (control.check("descuentos")>1) { %>
      <i data-toggle="tooltip" class="fa fa-times text-danger delete" title="Eliminar" />
    <% } %>
  </td>
</script>

<script type="text/template" id="descuentos_multiple_template">
  <div class="tab-container">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a href="#tab_lista_dto1" role="tab" data-toggle="tab">Articulos</a>
      </li>
      <li>
        <a href="#tab_lista_dto2" role="tab" data-toggle="tab">Sucursales</a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab_lista_dto1" class="tab-pane active">
        <div class="">
          <div class="clearfix">
            <div class="col-sm-4 p0">
              <div class="col-sm-6 p0">
                <label class="text-muted">C&oacute;digo</label>
                <div class="input-group">
                  <input type="hidden" class="form-control action no-model" id="descuentos_id_articulo"/>
                  <input type="text" class="form-control action no-model" id="descuentos_codigo_articulo"/>
                  <span class="input-group-btn">
                    <button tabindex="-1" id="descuentos_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
              <div class="col-sm-6 p0">
                <label class="text-muted">Descripci&oacute;n</label>
                <input disabled type="text" class="form-control action no-model" id="descuentos_item_nombre"/>
              </div>
            </div>
            <div class="col-sm-8 p0">
              <div class="col-sm-4 col-xs-12 p0">
                <div class="form-group">
                  <label class="control-label">Desde</label>
                  <div class="input-group">
                    <input type="text" class="form-control no-model" id="descuentos_desde">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 col-xs-12 p0">
                <div class="form-group">
                  <label class="control-label">Hasta</label>
                  <div class="input-group">
                    <input type="text" class="form-control no-model" id="descuentos_hasta">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 col-xs-12 p0">
                <label class="text-muted">Final</label>
                <div class="input-group">
                  <input type="text" class="form-control no-model" id="descuentos_item_precio_final"/>
                  <span class="input-group-btn">
                    <button title="Ingresar linea" id="descuentos_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="b-a" style="overflow: auto; height: 200px; max-height: 200px;">
            <table id="descuentos_tabla_items" class="table table-small sortable m-b-none default footable">
              <thead class="bg-light">
                <tr>
                  <th>C&oacute;digo</th>
                  <th>Descripci&oacute;n</th>
                  <th class="w130">Desde</th>
                  <th class="w130">Hasta</th>
                  <th class="w100">Precio</th>
                  <th class="w25"></th>
                  <th class="w25"></th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
      <div id="tab_lista_dto2" class="tab-pane">
        <div id="descuentos_sucursales" class="row">
          <% for(var i=0;i< window.almacenes.length; i++) { %>
            <% var alm = window.almacenes[i] %>
            <div class="col-md-4">
              <div class="m-r pt0 checkbox">
                <label class="i-checks">
                  <input class="check_sucursal" value="<%= alm.id %>" type="checkbox" checked><i></i> 
                  <%= alm.nombre %>
                </label>
              </div>
            </div>
          <% } %>
        </div>
      </div>
    </div>
  </div>
  <div class="padder clearfix pb20">
    <button class="btn btn-default cerrar fl">Cerrar</button>
    <button class="btn btn-success guardar fr">Guardar</button>
  </div>
</script>