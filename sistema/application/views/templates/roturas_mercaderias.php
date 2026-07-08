<script type="text/template" id="rotura_mercaderia_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Productos
      / <b>Baja de mercaderia</b>
    </h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="centrado">
      <div class="panel panel-default pull-in">
        <div class="panel-heading font-bold">
          Datos de Comprobante       
        </div>
        <div class="panel-body pl0 pr0">
          <div class="clearfix">
            <div class="col-md-2 col-sm-6">
              <label>Fecha</label>
              <div class="input-group">
                <input type="text" title="Fecha de emision de comprobante" id="rotura_mercaderia_fecha" name="fecha" class="form-control action">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <% if (control.check("almacenes")>0) { %>
              <div class="col-md-3 col-sm-6">
                <label>Sucursal</label>
                <select class="form-control action no-model" id="rotura_mercaderia_almacenes">
                  <option value="0">Seleccione</option>
                  <% for(var i=0;i< almacenes.length;i++) { %>
                    <% var almacen = almacenes[i] %>
                    <option <%= (id_almacen == almacen.id)?"selected":"" %> value="<%= almacen.id %>"><%= almacen.nombre %></option>
                  <% } %>
                </select>
              </div>
            <% } %>
            <div class="col-md-2 col-sm-6">
              <label>N&uacute;mero de remito</label>
              <input type="text" name="numero_remito" value="<%= numero_remito %>" class="form-control action" id="rotura_mercaderia_numero"/>
            </div>
          </div>
        </div>
      </div>

      <div class="panel panel-info pull-in">
        <div class="panel-heading font-bold">Ingreso de productos</div>
        <div class="panel-body">
          <div class="">

            <input type="hidden" id="rotura_mercaderia_id_articulo"/>

            <div class="clearfix">
              <div class="col-sm-7 col-xs-12 p0">
                <div class="col-sm-4 p0 form-group">
                  <label class="text-muted clearfix">C&oacute;digo</label>
                  <input type="text" placeholder="C&oacute;digo..." class="form-control action no-model" id="rotura_mercaderia_codigo_articulo"/>
                </div>
                <div class="col-sm-5 p0 form-group">
                  <label class="text-muted">Descripci&oacute;n</label>
                  <div class="input-group">
                    <input disabled type="text" class="form-control action no-model" id="rotura_mercaderia_item_nombre"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" id="rotura_mercaderia_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                    </span>
                  </div>
                </div>
                <div class="col-sm-3 col-xs-12 p0 form-group">
                  <label class="text-muted">Cant.</label>
                  <input type="text" class="form-control action no-model" value="1" id="rotura_mercaderia_item_cantidad"/>
                </div>
              </div>
              <div class="col-sm-1 p0">
                <label class="text-muted">Edit. Stock</label>
                <select id="rotura_mercaderia_item_no_editar_stock" class="form-control action no-model">
                  <option value="0">Si</option>
                  <option value="1">No</option>
                </select>
              </div>
              <div class="col-sm-2 col-xs-12 p0 form-group">
                <label class="text-muted">Costo Final</label>
                <input type="text" class="form-control action no-model" <%= ((MEGASHOP == 1 || ID_EMPRESA == 421) ? "disabled":"") %> value="0.00" id="rotura_mercaderia_item_costo_final"/>
              </div>
              <div class="col-sm-2 col-xs-12 p0 form-group">
                <label class="text-muted">P. Venta</label>                  
                <div class="input-group">
                  <input type="text" disabled class="form-control no-model" id="rotura_mercaderia_precio_final"/>
                  <input type="text" disabled class="dn form-control no-model" id="rotura_mercaderia_item_subtotal" placeholder="Subtotal"/>
                  <span class="input-group-btn">
                    <button title="Ingresar linea" id="rotura_mercaderia_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>

            <div class="b-a" style="overflow: auto; height: 250px;">
              <table id="tabla_items" class="table table-small sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th>Cod.</th>
                    <th class="w75">Cant.</th>
                    <th>Descripci&oacute;n</th>
                    <th class="w100">Costo</th>
                    <th class="w100">Venta</th>
                    <th class="w100">Subtotal</th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t">
              <div class="col-md-6">
              </div>
              <div class="col-md-6">
                <div class="form-horizontal pull-in totales">
                  <div class="form-group">
                    <label class="control-label col-xs-6 fs26">Total:</label>
                    <div class="col-xs-6">
                      <input type="text" disabled class="no-input fs26 bold" id="rotura_mercaderia_total"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t">
              <h4>Notas y Observaciones <i title="Click para ayuda" class="observaciones_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></h4>
              <div>
                <% if (estado == 0) { %>
                  <textarea style="height: 100px" id="rotura_mercaderia_observaciones" name="observaciones" placeholder="Puede escribir una nota u observacion que aparecer&aacute; al pie de p&aacute;gina del comprobante..." class="form-control"><%= ((id != undefined)?observaciones:OBSERVACIONES).replaceAll("<br />","\n") %></textarea>
                <% } else { %>
                  <p><%= observaciones %></p>
                <% } %>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

          </div>
        </div>
      </div>

      <div class="oh m-t m-b tar pull-in">
        <% if (id != undefined && id != 0) { %>
          <button class="btn btn-primary imprimir btn-addon pull-left m-r"><i class="icon glyphicon glyphicon-print"></i>Imprimir</button>
        <% } %>
        <% if (estado == 0) { %>
          <button class="btn btn-default aceptar btn-addon"><i class="icon fa fa-plus"></i>Guardar borrador</button>
          <button class="btn btn-success confirmar btn-addon"><i class="icon fa fa-plus"></i>Confirmar</button>
        <% } %>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="rotura_mercaderia_item_tabla_template">
  <td class="editar"><%= codigo %></td>
  <td class="editar"><%= cantidad %></td>
  <td class="editar"><span class="text-info"><%= nombre %></span></td>
  <td class="editar"><%= Number(costo_final).toFixed(2) %></td>
  <td class="editar"><%= Number(precio_final).toFixed(2) %></td>
  <td class="editar"><%= Number(total_final).toFixed(2) %></td>
  <td class="w25 p5">
    <% if (control.check("roturas_mercaderias") > 2) { %>
      <i title="Eliminar" class="glyphicon glyphicon-remove eliminar_flechita text-danger" />
    <% } %>
  </td>
</script>

<script type="text/template" id="roturas_mercaderias_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Productos
      / <b>Baja de mercaderia</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="roturas_mercaderias_buscar" value="<%= window.roturas_mercaderias_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </span>
            </div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <% if (permiso > 1) { %>
              <a class="btn btn-info btn-addon" href="app/#rotura_mercaderia"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            <% } %>
          </div>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="<%= (window.roturas_mercaderias_filter != '') ? "display:block" : "display:none" %>">
        <div class="wrapper oh">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"B&uacute;squeda Avanzada:","en"=>"Advanced Search:")); ?></h4>
          <div class="cb">
            <div class="input-group fl" style="width: 140px;">
              <input type="text" placeholder="Desde" id="roturas_mercaderias_desde" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group fl" style="width: 140px;">
              <input type="text" placeholder="Hasta" id="roturas_mercaderias_hasta" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="form-group fl" style="width: 200px; display: inline-block">
              <select class="form-control" id="roturas_mercaderias_buscar_sucursales">
                <% if (ID_SUCURSAL != 0) { %>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i]; %>
                    <% if (ID_SUCURSAL == o.id) { %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
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
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="roturas_mercaderias_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="sorting" data-sort-by="almacen">sucursal</th>
                <th class="w25"></th>
                <th class="sorting" data-sort-by="fecha">Fecha</th>
                <th class="sorting" data-sort-by="numero">Numero</th>
                <th class="sorting" data-sort-by="total">Valor</th>
                <th>Estado</th>
                <th class="th_acciones w120">Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
        <div>
          Suma Total: <b id="roturas_mercaderias_total"></b>
        </div>
      </div>
    </div>
  </div>  
</script>


<script type="text/template" id="roturas_mercaderias_item">
  <td class="ver"><span class='text-info'><%= almacen %></span></td>
  <td>
    <% if (!isEmpty(observaciones)) { %>
      <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-commenting text-warning"></i>
    <% } %>
  </td>
  <td class="ver"><%= fecha %></td>
  <td class="ver"><%= numero_remito %></td>  
  <td class="ver">$ <%= total %></td>
  <td class="ver">
    <% if (estado == 0) { %><span class="label bg-danger">No confirmado</span><% } %>
    <% if (estado == 1) { %><span class="label bg-success">Confirmado</span><% } %>
  </td>
  <td class="p5 td_acciones">
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>    
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="imprimir" data-id="<%= id %>">Listado c/precio</a></li>
        <li><a href="javascript:void(0)" class="imprimir_sin_costo" data-id="<%= id %>">Listado s/precio</a></li>
        <% if (permiso > 1) { %>
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        <% } %>
      </ul>
    </div>
  </td>
</script>
