<script type="text/template" id="pedidos_proveedores_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md">
    <div class="row clearfix">
      <div class="col-xs-12 col-sm-6">
        <h1 class="m-n font-thin h3">Pedidos</h1>
      </div>
    </div>
  </div>
  <div class="wrapper-md pb0">
    <div class="centrado">
      <div class="panel panel-default pull-in">
        <div class="panel-heading font-bold">Datos del Pedido</div>
        <div class="panel-body pl0 pr0">
          <div class="clearfix">
            <div class="col-md-3 col-sm-6">
              <label>Proveedor <i title="Click para ayuda" class="buscar_proveedores_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></label>
              <div class="input-group">
                <input type="text" class="dn" id="pedidos_proveedores_id_proveedor" value="<%= id_proveedor %>"/>
                <input title="Ingrese el codigo de Proveedor o comience a escribir parte del nombre. (0 = Consumidor Final)" type="text" class="form-control action" id="pedidos_proveedores_codigo_proveedor" placeholder="Nombre o codigo de proveedor" value="<%= proveedor.nombre %>"/>
                <span class="input-group-btn">
                  <button title="Atajo: F2 = Buscar" id="pedidos_proveedores_buscar_proveedor" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                </span>
              </div>            
            </div>
            <div class="col-md-3 col-sm-6">
              <label>Fecha</label>
              <div class="input-group">
                <input type="text" title="Fecha de emision de comprobante" id="pedidos_proveedores_fecha" name="fecha" class="form-control action">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <label>Estado</label>
              <select class="form-control action" id="pedidos_proveedores_tipo_estado" name="id_tipo_estado">
                <option value="0" <%= (id_tipo_estado==0)?"selected":"" %>>Pendiente</option>
                <option value="1" <%= (id_tipo_estado==1)?"selected":"" %>>Entregado</option>
              </select>            
            </div>
            <% if (control.check("sucursales")>0) { %>
              <div class="col-md-3 col-sm-6">
                <label>Sucursal</label>
                <select class="form-control no-model" id="pedidos_proveedores_sucursales"></select>
              </div>
            <% } %>
          </div>
        </div>
      </div>

      <div class="panel panel-info pull-in">
        <div class="panel-heading font-bold">Previsualizaci&oacute;n</div>
        <div class="panel-body preview-container">
          <div class="preview">

            <div class="invoice-block">
              <div class="invoice-type">Pedido</div>
            </div>

            <div class="invoice-block m-t">
              <div class="col-xs-6 pull-in">
                <div>
                  <span class="bold">Proveedor: </span>
                  <span id="pedidos_proveedores_proveedor_pedido"></span>
                </div>
                <div>
                  <span class="bold">Direcci&oacute;n: </span>
                  <span id="pedidos_proveedores_proveedor_direccion"><%= direccion %></span>
                </div>
              </div>
              <div class="col-xs-6 pull-in">
                <div>
                  <span class="bold">CUIT / DNI: </span>
                  <span id="pedidos_proveedores_proveedor_cuit"></span>
                </div>
                <div>
                  <span class="bold">Localidad: </span>
                  <span id="pedidos_proveedores_proveedor_localidad"><%= localidad %></span>
                </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>
            <input type="hidden" id="pedidos_proveedores_id_articulo"/>

            <div class="clearfix">
              <div class="col-md-4 col-xs-12 p0">
                <div class="col-sm-6 p0">
            
                  <label class="text-muted clearfix mb0">
                    <div class="radio fl mb0 mt0">
                      <label class="i-checks">
                        <input type="radio" name="tipo_codigo" value="INTERNO" checked="">
                        <i></i>
                        Int.
                      </label>
                    </div>
                    <div class="radio fl mb0 mt0 ml5">
                      <label class="i-checks">
                        <input type="radio" name="tipo_codigo" value="PROVEEDOR">
                        <i></i>
                        Prov.
                      </label>
                    </div>
                  </label>
                  <input type="text" placeholder="C&oacute;digo..." class="form-control action no-model" id="pedidos_proveedores_codigo_articulo"/>
                </div>
                <div class="col-sm-6 p0">
                  <label class="text-muted">Descripci&oacute;n</label>
                  <input disabled type="text" class="form-control action no-model" id="pedidos_proveedores_item_nombre"/>
                </div>
              </div>
              <div class="col-md-6 col-sm-6 p0">
                <div class="col-sm-3 p0">
                  <label class="text-muted">Cantidad</label>
                  <input type="text" class="form-control action no-model" value="1" id="pedidos_proveedores_item_cantidad"/>
                </div>
                <div class="col-sm-3 p0">
                  <label class="text-muted">&nbsp;</label>
                  <select class="form-control action no-model" id="pedidos_proveedores_item_tipo_cantidad">
                    <option value="U">Unidades</option>
                    <option value="B">Bultos</option>
                  </select>
                </div>
                <div class="col-sm-3 p0">
                  <label class="text-muted">UxB</label>
                  <input type="text" disabled class="form-control no-model action" value="1" id="pedidos_proveedores_item_uxb"/>
                </div>
                <div class="col-sm-3 p0">
                  <label class="text-muted">Precio Unit.</label>
                  <input disabled type="text" class="form-control no-model action dn" value="0.00" id="pedidos_proveedores_item_neto"/>
                  <input disabled type="text" class="form-control no-model action" value="0.00" id="pedidos_proveedores_item_precio"/>
                </div>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Importe</label>
                <div class="input-group">
                  <input type="text" disabled class="form-control no-model" id="pedidos_proveedores_item_subtotal" placeholder="Subtotal"/>
                  <span class="input-group-btn">
                    <button title="Ingresar linea" id="pedidos_proveedores_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>
            <div class="b-a" style="overflow: auto; margin-top: 15px;">
              <table id="tabla_items" class="table sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th class="w75">Cant.</th>
                    <th>Detalle</th>
                    <th class="w75">Unit.</th>
                    <th class="w75">Subtotal</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t">
              <div class="col-md-6"></div>
              <div class="col-md-6">
                <div class="form-horizontal pull-in totales">
                  <div class="form-group">
                    <label class="control-label col-xs-8">Subtotal:</label>
                    <div class="col-xs-4">
                      <input type="text" disabled class="no-input" id="pedidos_proveedores_subtotal"/>
                    </div>
                  </div>
                  <div class="line line-dashed b-b"></div>
                  <div class="form-group">
                    <label class="control-label col-xs-6 fs26">Total:</label>
                    <div class="col-xs-6">
                      <input type="text" disabled class="no-input fs26 bold" id="pedidos_proveedores_total"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t">
              <h4>Notas y Observaciones</h4>
              <div>
                <textarea style="height: 100px" id="pedidos_proveedores_observaciones" name="observaciones" placeholder="Puede escribir una nota u observacion que aparecer&aacute; al pie de p&aacute;gina del comprobante..." class="form-control"><%= observaciones.replaceAll("<br />","\n") %></textarea>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

          </div>
        </div>
      </div>
      <div class="oh m-t m-b tar pull-in mb50">
        <% if (id != undefined) { %>
          <button class="btn btn-primary imprimir btn-addon pull-left m-r"><i class="icon glyphicon glyphicon-print"></i>Imprimir</button>
        <% } %>
        <button class="btn btn-default anular btn-addon m-r"><i class="icon glyphicon glyphicon-remove"></i>Cancelar</button>
        <button class="btn btn-success aceptar btn-addon"><i class="icon fa fa-plus"></i>Guardar</button>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="pedido_proveedor_item_template">
  <td><%= (tipo_cantidad == "B") ? Number(cantidad * uxb).toFixed(2) : Number(cantidad).toFixed(2) %></td>
  <td><%= nombre %></td>
  <td><%= Number(precio).toFixed(2) %></td>
  <td><%= Number(total).toFixed(2) %></td>
  <td class="w25 p5"><i title="Editar" class="fa fa-file-text-o editar text-dark" /></td>
  <td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" /></td>
</script>

<script type="text/template" id="pedidos_proveedores_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <div class="row clearfix padder">
      <h1 class="m-n font-thin h3 pull-left">Listado de Pedidos</h1>
    </div>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 <%= (!seleccionar) ? "col-lg-3" : "" %> sm-m-b">
            <div class="input-group">
              <input type="text" placeholder="Buscar..." id="pedidos_proveedores_buscar" autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </span>
            </div>
          </div>
          <% if (!seleccionar) { %>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#pedido_proveedor">
              <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
            </a>
          </div>
          <% } %>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="display:none">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">
            <div style="width: 100px; display: inline-block">
              <input type="text" class="w100p form-control" id="pedidos_proveedores_desde" placeholder="Desde" />  
            </div>
            <div style="width: 100px; display: inline-block">
              <input type="text" class="w100p form-control" id="pedidos_proveedores_hasta" placeholder="Hasta" />  
            </div>
            <div class="btn-group dropdown">
              <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span>Estado</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu"></ul>
            </div>
            <div class="form-group">
              <button class="buscar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
            </div>            
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="pedidos_proveedores_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;"></th>
                <th class="w25">Numero</th>
                <% if (control.check("sucursales")>0) { %>
                  <th>Sucursal</th>
                <% } %>
                <th>Proveedor</th>
                <th>Fecha</th>
                <th class="tar">Total</th>
                <th class="w50">Estado</th>
                <% if (!seleccionar) { %>
                  <th class="th_acciones w120"></th>
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
<script type="text/template" id="pedidos_proveedores_item_resultados_template">
  <% var clase = ""; %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= id %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>
  <% } %>
  <td class="<%= clase %> edit"><%= numero %></td>
  <% if (control.check("sucursales")>0) { %>
    <td class="<%= clase %> edit"><%= sucursal %></td>
  <% } %>
  <td class="<%= clase %> edit capitalize">
    <span class="text-info"><%= (isEmpty(proveedor)) ? "Consumidor Final" : proveedor %></span>
  </td>
  <td class="<%= clase %> edit"><%= fecha %> <%= hora.substr(0,5) %></td>
  <td class="<%= clase %> tar edit">$ <%= Number(total).format() %></td>
  <td class="<%= clase %> edit">
    <% if (id_tipo_estado == 1) { %>
      <span class="label bg-success">Entregado</span>
    <% } else if (id_tipo_estado == 0) { %>
      <span class="label bg-danger">Pendiente</span>
    <% } %>        
  </td>
  <% if (!seleccionar) { %>
    <td class="p5 <%= clase %> td_acciones">
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="imprimir" data-id="<%= id %>">Imprimir</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>    
    </td>
  <% } %>
</script>