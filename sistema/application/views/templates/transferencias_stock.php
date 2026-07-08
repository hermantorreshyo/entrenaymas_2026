<script type="text/template" id="transferencia_stock_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md">
    <% var modulo = control.get("transferencias_stock") %>
    <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i><%= modulo.title %></h1>
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
                <input type="text" title="Fecha de emision de comprobante" id="transferencia_stock_fecha" name="fecha" class="form-control action" <%= (!edicion)?"disabled":"" %>>
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <label>Origen</label>
              <select class="form-control action no-model" id="transferencia_stock_almacen_origen" <%= (!edicion)?"disabled":"" %>>
                <% for(var i=0;i< almacenes.length;i++) { %>
                  <% var almacen = almacenes[i] %>
                  <option <%= (almacen.id == id_origen)?"selected":"" %> value="<%= almacen.id %>"><%= almacen.nombre %></option>
                <% } %>
              </select>
            </div>
            <div class="col-md-3 col-sm-6">
              <label>Destino</label>
              <select class="form-control action no-model" id="transferencia_stock_almacen_destino" <%= (!edicion)?"disabled":"" %>>
                <% for(var i=0;i< almacenes.length;i++) { %>
                  <% var almacen = almacenes[i] %>
                  <option <%= (almacen.id == id_destino)?"selected":"" %> value="<%= almacen.id %>"><%= almacen.nombre %></option>
                <% } %>
              </select>
            </div>
            <div class="col-md-2 col-sm-6">
              <label>N&uacute;mero de remito</label>
              <input type="text" name="numero_remito" value="<%= numero_remito %>" class="form-control action" id="transferencia_stock_numero" <%= (!edicion)?"disabled":"" %>/>
            </div>
          </div>
        </div>
      </div>

      <div class="panel panel-info pull-in">
        <div class="panel-heading font-bold">Previsualizaci&oacute;n</div>
        <div class="panel-body preview-container">
          <div class="preview">

            <input type="hidden" id="transferencia_stock_id_articulo"/>
            <% var ocultar_costos = (control.check("transferencias_stock")<3) %>

            <div class="clearfix" style="<%= (!edicion)?"display:none":"" %>">
              <div class="<%= (ocultar_costos)?"col-md-6":"col-md-4" %> col-xs-12 p0">
                <div class="col-sm-6 p0">
                  <label class="text-muted">C&oacute;digo</label>
                  <div class="input-group">
                    <input type="text" class="form-control action no-model" id="transferencia_stock_codigo_articulo"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" id="transferencia_stock_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                    </span>
                  </div>
                </div>
                <div class="col-sm-6 p0">
                  <label class="text-muted">Descripci&oacute;n</label>
                  <input disabled type="text" class="form-control action no-model" id="transferencia_stock_item_nombre"/>
                </div>
              </div>
              <div class="col-md-5 col-xs-12 p0">
                <div class="col-sm-3 p0">
                  <label class="text-muted">Cant.</label>
                  <input type="text" class="form-control action no-model" value="1" id="transferencia_stock_item_cantidad"/>
                </div>
                <div class="col-sm-3 p0 <%= (ocultar_costos)?"dn":"" %>">
                  <label class="text-muted">Costo Neto</label>
                  <input type="text" disabled class="form-control action no-model" value="0.00" id="transferencia_stock_item_costo_neto"/>
                </div>
                <div class="col-sm-3 p0 <%= (ocultar_costos)?"dn":"" %>">
                  <label class="text-muted">% IVA</label>
                  <select disabled id="transferencia_stock_alicuotas_iva" class="form-control action no-model">
                    <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                    <% var o = alicuotas_iva[i]; %>
                      <option value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                    <% } %>
                  </select>
                </div>
                <div class="col-sm-3 p0 <%= (ocultar_costos)?"dn":"" %>">
                  <label class="text-muted">Costo Final</label>
                  <input type="text" disabled class="form-control action no-model" value="0.00" id="transferencia_stock_item_costo_final"/>
                </div>
              </div>
              <div class="col-md-3 col-xs-12 p0 <%= (ocultar_costos)?"dn":"" %>">
                <div class="col-sm-4 p0">
                  <label class="text-muted">% Gan.</label>
                  <input type="number" disabled min="0" max="100" class="form-control action no-model" placeholder="0 %" id="transferencia_stock_item_porc_ganancia"/>
                </div>
                <div class="col-sm-8 p0">
                  <label class="text-muted">P. Venta</label>                  
                  <div class="input-group">
                    <input type="text" disabled class="form-control no-model" id="transferencia_stock_precio_final"/>
                    <input type="text" disabled class="dn form-control no-model" id="transferencia_stock_item_subtotal" placeholder="Subtotal"/>
                    <span class="input-group-btn">
                      <button title="Ingresar linea" id="transferencia_stock_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="b-a" style="overflow: auto; margin-top: 15px;">
              <table id="tabla_items" class="table sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th>Cod.</th>
                    <th class="w75">Cant.</th>
                    <th>Descripci&oacute;n</th>
                    <th class="<%= (ocultar_costos)?"dn":"" %> w100">C. Neto</th>
                    <th class="<%= (ocultar_costos)?"dn":"" %> w100">C. Final</th>
                    <th class="<%= (ocultar_costos)?"dn":"" %> w100">P. Venta</th>
                    <th class="<%= (ocultar_costos)?"dn":"" %> w100">Subtotal</th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t <%= (ocultar_costos)?"dn":"" %>">
              <div class="col-md-6">
              </div>
              <div class="col-md-6">
                <div class="form-horizontal pull-in totales">
                  <div class="form-group">
                    <label class="control-label col-xs-8">Neto:</label>
                    <div class="col-xs-4">
                      <input type="text" disabled class="no-input" id="transferencia_stock_subtotal_neto"/>
                    </div>
                  </div>
                  <div class="line line-dashed b-b"></div>
                  <div class="form-group">
                    <label class="control-label col-xs-6 fs26">Total:</label>
                    <div class="col-xs-6">
                      <input type="text" disabled class="no-input fs26 bold" id="transferencia_stock_total"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t">
              <h4>Notas y Observaciones <i title="Click para ayuda" class="observaciones_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></h4>
              <div>
                <textarea style="height: 100px" id="transferencia_stock_observaciones" name="observaciones" placeholder="Puede escribir una nota u observacion que aparecer&aacute; al pie de p&aacute;gina del comprobante..." class="form-control" <%= (!edicion)?"disabled":"" %>><%= ((id != undefined)?observaciones:OBSERVACIONES).replaceAll("<br />","\n") %></textarea>
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
          <button class="btn btn-success confirmar btn-addon"><i class="icon fa fa-plus"></i>Guardar y confirmar stock</button>
        <% } %>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="transferencia_stock_item_tabla_template">
  <% var ocultar_costos = (control.check("transferencias_stock")<3) %>
  <td class="editar"><%= codigo %></td>
  <td class="editar"><%= cantidad %></td>
  <td class="editar"><span class="text-info"><%= nombre %></span></td>
  <td class="editar <%= (ocultar_costos)?"dn":"" %>"><%= Number(costo_neto).toFixed(2) %></td>
  <td class="editar <%= (ocultar_costos)?"dn":"" %>"><%= Number(costo_final).toFixed(2) %></td>
  <td class="editar <%= (ocultar_costos)?"dn":"" %>"><%= Number(precio_final).toFixed(2) %></td>
  <td class="editar <%= (ocultar_costos)?"dn":"" %>"><%= Number(total_final).toFixed(2) %></td>
  <td class="w25 p5">
    <% if (control.check("transferencias_stock")>1) { %>
      <i title="Eliminar" class="glyphicon glyphicon-remove eliminar_flechita text-danger" />
    <% } %>
  </td>
</script>

<script type="text/template" id="transferencias_stock_panel_template">
  <% var ocultar_costos = (control.check("transferencias_stock")<3) %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("transferencias_stock") %>
    <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i><%= modulo.title %></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (permiso > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#transferencia_stock"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="transferencias_stock_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="w100 sorting" data-sort-by="fecha">Fecha</th>
                <th class="sorting" data-sort-by="almacen">Origen</th>
                <th class="sorting" data-sort-by="almacen">Destino</th>
                <th class="w120 sorting" data-sort-by="numero_remito">Remito</th>
                <th class="w120 <%= (ocultar_costos)?"dn":"" %> sorting" data-sort-by="total">Total</th>
                <th class="w120">Estado</th>
                <th class="th_acciones w120">Acciones</th>
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


<script type="text/template" id="transferencias_stock_item">
  <% var ocultar_costos = (control.check("transferencias_stock")<3) %>
  <td class="ver"><%= fecha %></td>
  <td class="ver"><%= origen %></td>
  <td class="ver"><%= destino %></td>
  <td class="ver"><%= numero_remito %></td>
  <td class="ver <%= (ocultar_costos)?"dn":"" %>">$ <%= Number(total).toFixed(2) %></td>
  <td class="ver">
    <% if (estado == 0) { %>
      <span class="label bg-danger">Pendiente</span>
    <% } else if (estado == 1) { %>
      <span class="label bg-success">Confirmada</span>
    <% } %>
  </td>
  <td class="p5 td_acciones">
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>    
      <ul class="dropdown-menu pull-right">
        <li class="<%= (ocultar_costos)?"dn":"" %>"><a href="javascript:void(0)" class="imprimir" data-id="<%= id %>">Listado c/precio</a></li>
        <li class="<%= (ocultar_costos)?"dn":"" %>"><a href="javascript:void(0)" class="imprimir_sin_costo" data-id="<%= id %>">Listado s/precio</a></li>
        <% if (permiso > 2) { %>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        <% } %>
      </ul>
    </div>
  </td>
</script>
