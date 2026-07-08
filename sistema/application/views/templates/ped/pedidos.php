<script type="text/template" id="pedidos_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md">
    <div class="row clearfix">
      <div class="col-xs-12 col-sm-6">
        <h1 class="m-n font-thin h3">Pedidos</h1>
      </div>
      <div class="col-xs-12 col-sm-6">
        <% if (id == undefined || id == 0) { %>
          <div class="form-inline pull-right">
            <div class="btn-group dropdown">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="glyphicon glyphicon-import"></i><span class="hidden-xs">Importar</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void" class="exportar">Remito</a></li>
                <li><a href="javascript:void" class="exportar_csv">Presupuesto</a></li>
                <li><a href="javascript:void" class="importar_pedido">Otro Pedido</a></li>
              </ul>
            </div>
          </div>
        <% } %>
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
              <label>Cliente <i title="Click para ayuda" class="buscar_clientes_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></label>
              <div class="input-group">
                <input type="text" class="dn" id="pedidos_id_cliente" value="<%= id_cliente %>"/>
                <input title="Ingrese el codigo de Cliente o comience a escribir parte del nombre. (0 = Consumidor Final)" type="text" class="form-control action" id="pedidos_codigo_cliente" placeholder="Nombre o codigo de cliente" value="<%= cliente.nombre %>"/>
                <span class="input-group-btn">
                  <button title="Atajo: F2 = Buscar" id="pedidos_buscar_cliente" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                </span>
              </div>            
            </div>
            <div class="col-md-3 col-sm-6">
              <label>Fecha</label>
              <div class="input-group">
                <input type="text" title="Fecha de emision de comprobante" id="pedidos_fecha" name="fecha" class="form-control action">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <label>Estado</label>
              <% if (id_tipo_estado == 6) { %>
                <input type="text" disabled class="form-control" value="Finalizado"/>
              <% } else { %>
                <select class="form-control action" id="pedidos_tipo_estado" name="id_tipo_estado">
                  <% for(var i=0;i<tipos_estado_pedidos.length;i++) { %>
                  <% var c = tipos_estado_pedidos[i]; %>
                  <option <%= (id_tipo_estado == c.id)?"selected":"" %> value="<%= c.id %>"><%= c.nombre %></option>
                  <% } %>
                </select>            
              <% } %>
            </div>
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
                  <span class="bold">Cliente: </span>
                  <span id="pedidos_cliente_pedido"></span>
                </div>
                <div>
                  <span class="bold">Direcci&oacute;n: </span>
                  <span id="pedidos_cliente_direccion"><%= direccion %></span>
                </div>
              </div>
              <div class="col-xs-6 pull-in">
                <div>
                  <span class="bold">CUIT / DNI: </span>
                  <span id="pedidos_cliente_cuit"></span>
                </div>
                <div>
                  <span class="bold">Localidad: </span>
                  <span id="pedidos_cliente_localidad"><%= localidad %></span>
                </div>
                <% if (!isEmpty(numero_envio)) { %>
                <div>
                  <span class="bold">N&uacute;mero Env&iacute;o: </span>
                  <span id="pedidos_numero_envio">
                    <%= numero_envio %>
                    <% if (!isEmpty(link_envio)) { %>
                    <a href="<%= link_envio %>" target="_blank" class="text-info m-l">Ver Etiqueta</a>
                    <% } else { %>
                    <a href="javascript:void(0)" class="obtener_link_envio text-info m-l">Ver Etiqueta</a>
                    <% } %>
                  </span>
                </div>
                <% } %>                    
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>
            <input type="hidden" id="pedidos_id_articulo"/>

            <div class="clearfix">
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Producto / Servicio</label>
                <input type="text" class="form-control action no-model" id="pedidos_codigo_articulo"/>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Cantidad</label>
                <input type="text" class="form-control action no-model" value="1" id="pedidos_item_cantidad"/>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Tomar precio de</label>
                <select class="form-control no-model" id="pedidos_lista">
                  <option value="0">Lista 1</option>
                  <option value="1">Lista 2</option>
                  <option value="2">Lista 3</option>
                </select>                                    
              </div>                    
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Precio Unit.</label>
                <input type="text" class="form-control no-model action dn" value="0.00" id="pedidos_item_neto"/>
                <input type="text" class="form-control no-model action" value="0.00" id="pedidos_item_precio"/>
              </div>
              <div class="col-md-1 col-sm-6 p0">
                <label class="text-muted">% Bonif.</label>
                <input type="number" min="0" max="100" class="form-control no-model action" placeholder="0 %" id="pedidos_item_bonificado"/>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Importe</label>
                <div class="input-group">
                  <input type="text" disabled class="form-control no-model" id="pedidos_item_subtotal" placeholder="Subtotal"/>
                  <span class="input-group-btn">
                    <button title="Ingresar linea" id="pedidos_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>

            <div class="b-a" style="overflow: auto; margin-top: 15px;">
              <table id="tabla_items" class="table sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th class="w75">Cant.</th>
                    <th class="w75">Cod.</th>
                    <th>Detalle</th>
                    <th class="w75">Unit.</th>
                    <th class="w75">Bonif.</th>
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
                      <input type="text" disabled class="no-input" id="pedidos_subtotal"/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-xs-8">
                      Descuento (%):
                      <input type="number" min="0" max="100" value="<%= porc_descuento %>" name="porc_descuento" class="form-control w-xs pull-right action text-right" id="pedidos_porc_descuento"/>
                    </label>
                    <div class="col-xs-4">
                      <input type="text" disabled class="no-input" id="pedidos_descuento"/>
                    </div>
                  </div>
                  <% if (costo_envio >= 0) { %>
                  <div class="form-group">
                    <label class="control-label col-xs-8">
                      Costo Envio:
                    </label>
                    <div class="col-xs-4">
                      <input type="text" class="form-control tar" value="<%= costo_envio %>" name="costo_envio" id="pedidos_costo_envio"/>
                    </div>
                  </div>
                  <% } %>
                  <% if (iva > 0) { %>
                    <div class="form-group">
                      <label class="control-label col-xs-8">
                        IVA:
                      </label>
                      <div class="col-xs-4">
                        <input type="text" disabled class="no-input" value="<%= iva %>" id="pedidos_iva"/>
                      </div>
                    </div>
                  <% } %>
                  <div class="form-group">
                    <div class="col-xs-12">
                      <div class="checkbox">
                        <label class="i-checks">
                          <input type="checkbox" name="retirar_envio" id="pedidos_retirar_envio" <%= (retirar_envio==1)?"checked":"" %>><i></i> Retira en local
                        </label>
                      </div>                            
                    </div>
                  </div>

                  <div class="line line-dashed b-b"></div>
                  <div class="form-group">
                    <label class="control-label col-xs-6 fs26">Total:</label>
                    <div class="col-xs-6">
                      <input type="text" disabled class="no-input fs26 bold" id="pedidos_total"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t">
              <h4>Notas y Observaciones</h4>
              <div>
                <textarea style="height: 100px" id="pedidos_observaciones" name="observaciones" placeholder="Puede escribir una nota u observacion que aparecer&aacute; al pie de p&aacute;gina del comprobante..." class="form-control"><%= observaciones.replaceAll("<br />","\n") %></textarea>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

          </div>
        </div>
      </div>
      <div class="oh m-t m-b tar pull-in mb50">
        <% if (id != undefined) { %>
        <button class="btn btn-primary imprimir btn-addon pull-left m-r"><i class="icon glyphicon glyphicon-print"></i>Imprimir</button>
        <% if (isEmpty(numero_envio) && (id_tipo_estado == 6 || id_tipo_estado == 5 || id_tipo_estado == 4)) { %>
        <button class="btn btn-primary enviar_andreani btn-addon pull-left m-r">Enviar con Andreani</button>
        <% } %>
        <% } %>
        <button class="btn btn-default anular btn-addon m-r"><i class="icon glyphicon glyphicon-remove"></i>Cancelar</button>
        <button class="btn btn-success aceptar btn-addon"><i class="icon fa fa-plus"></i>Guardar</button>
      </div>

    </div>
  </div>
</script>
<script type="text/template" id="pedido_item_template">
  <td><%= Number(cantidad).toFixed(2) %></td>
  <td><%= codigo %></td>
  <td>
    <%= nombre %>
    <%= (isEmpty(descripcion)) ? "<br/>"+descripcion : "" %>
  </td>
  <td><%= Number(precio).toFixed(2) %></td>
  <td><%= Number(bonificacion).toFixed(2) %>%</td>
  <td><%= Number(total_con_iva).toFixed(2) %></td>
  <td class="w25 p5"><i title="Editar" class="fa fa-file-text-o editar text-dark" /></td>
  <td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" /></td>
</script>
<script type="text/template" id="pedidos_resultados_template">
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
              <input type="text" placeholder="Buscar..." autocomplete="off" class="buscar form-control">
              <span class="input-group-btn">
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </span>
            </div>
          </div>
          <% if (!seleccionar) { %>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-success btn-addon" href="app/#pedido">
              <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
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
              <input type="text" class="w100p form-control" id="pedidos_desde" placeholder="Desde" />  
            </div>
            <div style="width: 100px; display: inline-block">
              <input type="text" class="w100p form-control" id="pedidos_hasta" placeholder="Hasta" />  
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
          <table id="pedidos_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <% if (seleccionar) { %>
                <th style="width:20px;"></th>
                <% } %>
                <th class="w25">Numero</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Estado</th>
                <% if (control.check("vendedores")>0) { %>
                <th>Vendedor</th>
                <% } %>
                <th class="tar">Total</th>
                <% if (!seleccionar) { %>
                <th class="w20"></th>
                <th class="w20"></th>
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
<script type="text/template" id="pedidos_item_resultados_template">
  <% var clase = ""; %>
  <% if (seleccionar) { %>
  <td>
    <label class="i-checks m-b-none">
      <input class="radio esc" value="<%= id %>" name="radio" type="radio"><i></i>
    </label>
  </td>
  <% } %>
  <td class="<%= clase %>"><%= numero %></td>
  <td class="<%= clase %>"><%= fecha %> <%= hora.substr(0,5) %></td>
  <td class="<%= clase %> capitalize"><%= (isEmpty(cliente)) ? "Consumidor Final" : cliente %></td>
  <td class="<%= clase %>">
    <% if (id_tipo_estado == 6 || id_tipo_estado == 5 || id_tipo_estado == 4) { %>
    <span class="label bg-success"><%= estado %></span>
    <% } else if (id_tipo_estado == 7) { %>
    <span class="label bg-danger"><%= estado %></span>
    <% } else { %>
    <span class="label bg-light dk"><%= estado %></span>
    <% } %>        
  </td>
  <% if (control.check("vendedores")>0) { %>
  <td class="<%= clase %>"><%= vendedor %></td>
  <% } %>
  <td class="<%= clase %> tar">$ <%= Number(total).format() %></td>
  <% if (!seleccionar) { %>
  <td><i class="fa fa-file-text-o edit text-dark" /></td>
  <td><i class="glyphicon glyphicon-remove delete text-danger" /></td>
  <% } %>
</script>