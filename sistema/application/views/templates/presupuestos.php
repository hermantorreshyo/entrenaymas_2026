<script type="text/template" id="presupuestos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <div class="row clearfix">
    <div class="col-xs-12 col-sm-6">
      <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>Presupuestos</h1>
    </div>
    <div class="col-xs-12 col-sm-6">
      <?php /*
      <div class="form-inline pull-right">
        <div class="btn-group dropdown">
          <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
          <i class="fa fa-cog"></i><span>Opciones</span>
          <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void" class="exportar">Importar de remito</a></li>
          <li><a href="javascript:void" class="importar_presupuesto">Importar de presupuesto</a></li>
          </ul>
        </div>
      </div>
      */ ?>
    </div>
  </div>
</div>
<div class="wrapper-md pb0">
  <div class="centrado">
    <div class="panel panel-default pull-in">
      <div class="panel-heading font-bold">
      Datos del Presupuesto     
      </div>
      <div class="panel-body pl0 pr0">
        <div class="clearfix">
          <div class="col-md-3 col-sm-6">
            <div class="form-group">
              <label>Cliente <i title="Click para ayuda" class="buscar_clientes_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></label>
              <div class="input-group">
                <input type="text" class="dn" id="presupuestos_id_cliente" value="<%= id_cliente %>"/>
                <input title="Ingrese el codigo de Cliente o comience a escribir parte del nombre. (0 = Consumidor Final)" type="text" class="form-control action no-model" id="presupuestos_codigo_cliente" placeholder="Nombre o codigo de cliente" value="<%= cliente.nombre %>"/>
                <span class="input-group-btn">
                  <button title="Atajo: F2 = Buscar" id="presupuestos_buscar_cliente" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="form-group">
              <label>Fecha</label>
              <div class="input-group">
                <input type="text" title="Fecha de emision de comprobante" id="presupuestos_fecha" name="fecha" class="form-control no-model action">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>        
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="form-group">
              <label>V&aacute;lido hasta</label>
              <div class="input-group">
                <input type="text" title="Fecha de Vencimiento del presupuesto" id="presupuestos_fecha_hasta" name="fecha_hasta" class="form-control no-model action">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>        
              </div>
            </div>
          </div>
          <% if (typeof window.almacenes != "undefined" && window.almacenes.length > 0) { %>
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label>Sucursal</label>
                <select class="form-control" id="presupuestos_sucursales">
                  <% if (ID_SUCURSAL == 0) { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <option <%= (o.id == id_sucursal)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  <% } else { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <% if (o.id == ID_SUCURSAL) { %>
                        <option selected value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    <% } %>
                  <% } %>
                </select>
              </div>
            </div>
          <% } %>
        </div>

        <div class="clearfix">
          <div class="col-md-3 col-sm-6">
            <div class="form-group">
              <label>Forma de Pago</label>
              <select name="forma_pago" id="presupuesto_forma_pago" class="form-control">
                <option <%= (forma_pago == "E")?"selected":"" %> value="E">Efectivo</option>
                <option <%= (forma_pago == "C")?"selected":"" %> value="C">Cuenta Corriente</option>
                <option <%= (forma_pago == "Q")?"selected":"" %> value="Q">Cheque</option>
                <option <%= (forma_pago == "T")?"selected":"" %> value="T">Tarjeta</option>
              </select>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="form-group">
              <label>Tarjeta</label>
              <select id="presupuesto_tarjetas" class="form-control habilitar_tarjeta">
                <option value="0" <%= (id_tarjeta == 0)?"selected":"" %>>Tarjeta</option>
                <% for(var i=0;i< tarjetas.length;i++) { %>
                  <% var t = tarjetas[i] %>
                  <option <%= (id_tarjeta == t.id)?"selected":"" %> value="<%= t.id %>"><%= t.nombre %></option>
                <% } %>
              </select>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Cuotas</label>
                  <select id="presupuesto_cuotas" name="cuotas" class="form-control habilitar_tarjeta">
                    <option <%= (cuotas == 0) ? "selected":"" %> value="0">-</option>
                    <option <%= (cuotas == 1) ? "selected":"" %> value="1">1</option>
                    <option <%= (cuotas == 2) ? "selected":"" %> value="2">2</option>
                    <option <%= (cuotas == 3) ? "selected":"" %> value="3">3</option>
                    <option <%= (cuotas == 4) ? "selected":"" %> value="4">4</option>
                    <option <%= (cuotas == 5) ? "selected":"" %> value="5">5</option>
                    <option <%= (cuotas == 6) ? "selected":"" %> value="6">6</option>
                    <option <%= (cuotas == 7) ? "selected":"" %> value="7">7</option>
                    <option <%= (cuotas == 8) ? "selected":"" %> value="8">8</option>
                    <option <%= (cuotas == 9) ? "selected":"" %> value="9">9</option>
                    <option <%= (cuotas == 10) ? "selected":"" %> value="10">10</option>
                    <option <%= (cuotas == 11) ? "selected":"" %> value="11">11</option>
                    <option <%= (cuotas == 12) ? "selected":"" %> value="12">12</option>
                    <option <%= (cuotas == 13) ? "selected":"" %> value="13">13</option>
                    <option <%= (cuotas == 14) ? "selected":"" %> value="14">14</option>
                    <option <%= (cuotas == 15) ? "selected":"" %> value="15">15</option>
                    <option <%= (cuotas == 16) ? "selected":"" %> value="16">16</option>
                    <option <%= (cuotas == 17) ? "selected":"" %> value="17">17</option>
                    <option <%= (cuotas == 18) ? "selected":"" %> value="18">18</option>
                    <option <%= (cuotas == 19) ? "selected":"" %> value="19">19</option>
                    <option <%= (cuotas == 20) ? "selected":"" %> value="20">20</option>
                    <option <%= (cuotas == 21) ? "selected":"" %> value="21">21</option>
                    <option <%= (cuotas == 22) ? "selected":"" %> value="22">22</option>
                    <option <%= (cuotas == 23) ? "selected":"" %> value="23">23</option>
                    <option <%= (cuotas == 24) ? "selected":"" %> value="24">24</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Recargo</label>
                  <input id="presupuesto_recargo" value="<%= recargo %>" type="text" class="form-control habilitar_tarjeta"/>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="form-group">
              <label class="control-label">Moneda</label>
              <select id="presupuesto_monedas" class="form-control" name="moneda">
                <% for(var i=0;i< window.monedas.length;i++) { %>
                  <% var o = monedas[i]; %>
                  <option <%= (moneda == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.signo %> (<%= o.nombre %>)</option>
                <% } %>
              </select>
            </div>
          </div>
        </div>

      </div>
    </div>
  
  <div class="panel panel-info pull-in">
    <div class="panel-heading font-bold">Previsualizaci&oacute;n</div>
    <div class="panel-body preview-container">
    <div class="preview">
      
      <div class="invoice-block">
        <div class="invoice-type">Presupuesto</div>
      </div>

      <div class="invoice-block m-t">
        <div class="col-xs-6 pull-in">
          <div>
            <span class="bold">Cliente: </span>
            <span id="presupuestos_cliente_presupuesto"></span>
          </div>
          <div>
            <span class="bold">Direcci&oacute;n: </span>
            <span id="presupuestos_cliente_direccion"></span>
          </div>
          <div>
            <span class="bold">Localidad: </span>
            <span id="presupuestos_cliente_localidad"></span>
          </div>
        </div>
        <div class="col-xs-6 pull-in">
          <div>
            <span class="bold">Tipo Contribuyente: </span>
            <span id="presupuestos_cliente_iva"></span>
          </div>
          <div>
            <span class="bold">CUIT / DNI: </span>
            <span id="presupuestos_cliente_cuit"></span>
          </div>
        </div>
      </div>
      
      <div class="line line-dashed b-b line-lg"></div>
      
      <input type="hidden" id="presupuestos_id_articulo"/>
        <div class="clearfix">
          <div class="col-md-3 col-sm-6 p0">
            <label class="text-muted">Producto / Servicio</label>
            <div class="input-group">
              <input type="text" class="form-control action" id="presupuestos_codigo_articulo"/>
              <span class="input-group-btn">
                <button tabindex="-1" title="Atajo: F9 = Buscar" id="presupuestos_buscar_articulo" class="btn btn-default ml0" type="button"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row">
              <div class="col-sm-3 p0">
                <label class="text-muted">Cantidad</label>
                <input type="text" class="form-control action" value="1" id="presupuestos_item_cantidad"/>
              </div>
              <div class="col-sm-3 p0">
                <label class="text-muted">Lista</label>
                <select class="form-control no-model" id="presupuestos_lista">
                  <option value="0"><%= (typeof LISTA_1_NOMBRE != undefined) ? LISTA_1_NOMBRE : "Lista 1" %></option>
                  <option value="1"><%= (typeof LISTA_2_NOMBRE != undefined) ? LISTA_2_NOMBRE : "Lista 2" %></option>
                  <option value="2"><%= (typeof LISTA_3_NOMBRE != undefined) ? LISTA_3_NOMBRE : "Lista 3" %></option>
                  <option value="3"><%= (typeof LISTA_4_NOMBRE != undefined) ? LISTA_4_NOMBRE : "Lista 4" %></option>
                  <option value="4"><%= (typeof LISTA_5_NOMBRE != undefined) ? LISTA_5_NOMBRE : "Lista 5" %></option>
                  <option value="5"><%= (typeof LISTA_6_NOMBRE != undefined) ? LISTA_6_NOMBRE : "Lista 6" %></option>
                </select>                  
              </div>          
              <div class="col-sm-3 p0">
                <label class="text-muted">Precio Unit.</label>
                <input type="text" class="form-control action dn" value="0.00" id="presupuestos_item_neto"/>
                <input type="text" class="form-control action" value="0.00" id="presupuestos_item_precio"/>
              </div>
              <div class="col-sm-3 p0">
                <label class="text-muted">% IVA</label>
                <select id="presupuestos_item_alicuotas_iva" class="no-model form-control">
                  <option value="0">-</option>
                  <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                    <% var o = alicuotas_iva[i]; %>
                    <option value="<%= o.porcentaje %>"><%= o.nombre %></option>
                  <% } %>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-1 col-sm-6 p0">
            <label class="text-muted">% Bonif.</label>
            <input type="number" min="0" max="100" class="form-control action" placeholder="0 %" id="presupuestos_item_bonificado"/>
          </div>
          
          <div class="col-md-2 col-sm-6 p0">
            <label class="text-muted">Importe</label>
            <div class="input-group">
              <input type="text" disabled class="form-control" id="presupuestos_item_subtotal" placeholder="Subtotal"/>
              <span class="input-group-btn">
                <button title="Ingresar linea" id="presupuestos_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
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
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
          <div class="form-horizontal pull-in totales">
            <div class="form-group">
              <label class="control-label col-xs-8">Subtotal:</label>
              <div class="col-xs-4">
                <input type="text" disabled class="no-input" id="presupuestos_subtotal"/>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-8">
                Descuento (%):
                <input type="number" min="0" max="100" value="<%= porc_descuento %>" name="porc_descuento" class="form-control w-xs pull-right action text-right" id="presupuestos_porc_descuento"/>
              </label>
              <div class="col-xs-4">
                <input type="text" disabled class="no-input" id="presupuestos_descuento"/>
              </div>
            </div>

            <div class="form-group <%= (ID_EMPRESA==224)?"":"dn" %>">
              <label class="control-label col-xs-8">
                IVA (%):
                <input type="number" min="0" max="100" value="<%= porc_iva %>" name="porc_iva" class="form-control w-xs pull-right action text-right" id="presupuestos_porc_iva"/>
              </label>
              <div class="col-xs-4">
                <input type="text" disabled class="no-input" id="presupuestos_iva"/>
              </div>
            </div>

            <div class="line line-dashed b-b"></div>
            <div class="form-group">
              <label class="control-label col-xs-6 fs26">Total:</label>
              <div class="col-xs-6">
                <input type="text" disabled class="no-input fs26 bold" id="presupuestos_total"/>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="line line-dashed b-b line-lg"></div>
      
      <div class="oh m-t">
        <h4>Notas y Observaciones</h4>
        <div>
          <textarea style="height: 100px" id="presupuestos_observaciones" name="observaciones" placeholder="Puede escribir una nota u observacion que aparecer&aacute; al pie de p&aacute;gina del comprobante..." class="form-control"><%= observaciones.replaceAll("<br />","\n") %></textarea>
        </div>
      </div>
      
      <div class="line line-dashed b-b line-lg"></div>
      
    </div>
    </div>
  </div>
  
  <div class="oh m-t m-b tar pull-in">
    <% if (id != undefined) { %>
      <button class="btn btn-primary imprimir btn-addon pull-left m-r"><i class="icon glyphicon glyphicon-print"></i>Imprimir</button>
    <% } %>
    <% if (stock == 0) { %>
      <button class="btn btn-default anular btn-addon m-r"><i class="icon glyphicon glyphicon-remove"></i>Cancelar</button>
      <button class="btn btn-success aceptar btn-addon"><i class="icon fa fa-plus"></i>Guardar</button>
    <% } %>
  </div>
  
  </div>
</div>
</script>

<script type="text/template" id="presupuesto_item_template">
<td><%= Number(cantidad).toFixed(2) %></td>
<td><%= nombre %></td>
<td><%= Number(precio).toFixed(2) %></td>
<td><%= Number(bonificacion).toFixed(2) %>%</td>
<td><%= Number(total).toFixed(2) %></td>
<td class="w25 p5"><i title="Editar" class="fa fa-file-text-o editar text-dark" /></td>
<td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" /></td>
</script>



<script type="text/template" id="presupuestos_resultados_template">
<% if (!seleccionar) { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <div class="row clearfix padder">
    <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-file-text icono_principal"></i>Presupuestos</h1>
    </div>
  </div>
<% } %>
<div class="<%= (!seleccionar)?"wrapper-md":"" %>">
  <div class="panel panel-default">
    
    <% if (!seleccionar) { %>
      <ul class="nav nav-tabs nav-tabs-2" role="tablist">
        <li>
          <a href="app/#ventas_listado"><?php echo lang(array("es"=>"Todos","en"=>"All")); ?></a>
        </li>
        <li>
          <a href="app/#ventas_listado"><i class="fa fa-thumbs-up text-success"></i> <?php echo lang(array("es"=>"Finalizadas","en"=>"Completed")); ?></a>
        </li>
        <li>
          <a href="app/#ventas_listado"><i class="fa fa-check text-info"></i> <?php echo lang(array("es"=>"En Proceso","en"=>"In process")); ?></a>
        </li>
        <li>
          <a href="app/#ventas_listado"><i class="fa fa-thumbs-down text-danger"></i> <?php echo lang(array("es"=>"Abandonadas","en"=>"Discarded")); ?></a>
        </li>
        <% if (control.check("presupuestos")>0) { %>
          <li class="active">
            <a href="javascript:void(0)" role="tab" data-toggle="tab"><i class="fa fa-file text-info"></i> <?php echo lang(array("es"=>"Presupuestos","en"=>"Presupuestos")); ?></a>
          </li>
        <% } %>
      </ul>
    <% } %>

    <div class="panel-heading clearfix">
      <div class="row">
      <div class="col-md-6 <%= (!seleccionar) ? "col-lg-3" : "" %> sm-m-b">
        <div class="input-group">
          <input type="text" placeholder="Buscar..." autocomplete="off" class="buscar form-control">
          <span class="input-group-btn">
          <button class="btn buscar-btn btn-default"><i class="fa fa-search"></i></button>
          </span>
          <span class="input-group-btn">
          <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
          </span>
        </div>
      </div>
      <% if (!seleccionar) { %>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
        
        <a class="btn btn-info btn-addon ml5" href="app/#presupuesto">
          <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
        </a>
        <!--
        <div class="btn-group dropdown">
          <button class="btn btn-sm btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
          <i class="glyphicon glyphicon-print"></i><span class="hidden-xs">Imprimir</span>
          <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
          <li><a href="javascript:void(0)" class="imprimir" data-tipo="1">Cartelitos</a></li>
          <li><a href="javascript:void(0)" class="imprimir" data-tipo="7">Carteles Medianos</a></li>
          <li><a href="javascript:void(0)" class="imprimir" data-tipo="2">Carteles Grandes</a></li>
          <li><a href="javascript:void(0)" class="imprimir" data-tipo="3">Ofertas</a></li>
          <li><a href="javascript:void(0)" class="imprimir" data-tipo="4">Listado de Precios</a></li>
          <li><a href="javascript:void(0)" class="imprimir" data-tipo="8">Listado de Costos</a></li>
          <li><a href="javascript:void(0)" class="imprimir" data-tipo="5">Listado de Presupuestos</a></li>
          <li><a href="javascript:void(0)" class="imprimir" data-tipo="6">Listado de PLUs</a></li>
          </ul>
        </div>
        
        <div class="btn-group dropdown">
          <button class="btn btn-sm btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
          <i class="fa fa-database"></i><span class="hidden-xs">Datos</span>
          <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
          <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
          <li><a href="javascript:void" class="exportar_csv">Exportar CSV</a></li>
          <li class="divider"></li>
          <li><a href="javascript:void" class="importar_csv">Importar CSV</a></li>
          </ul>
        </div>
        -->
        </div>
      <% } %>
      </div>
    </div>
    <div class="advanced-search-div bg-light dk" style="display:none">
      <div class="wrapper clearfix">
      <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
      <div class="form-inline">
        <div style="width: 100px; display: inline-block">
        <input type="text" class="w100p form-control" id="presupuestos_desde" placeholder="Desde" />  
        </div>
        <div style="width: 100px; display: inline-block">
        <input type="text" class="w100p form-control" id="presupuestos_hasta" placeholder="Hasta" />  
        </div>
        <div class="form-group">
        <button class="buscar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
        </div>      
      </div>
      </div>
    </div>
    <% if (!seleccionar) { %>
      <div class="bulk_action wrapper pb0">
      <button class="btn btn-default enviar btn-addon"><i class="icon fa fa-send"></i>Enviar por email</button>
      </div>
    <% } %>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="presupuestos_tabla" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <% if (!seleccionar) { %>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>            
              <% } else { %>
                <th style="width:20px;"></th>
              <% } %>
              <th class="w25">#</th>
              <th style="width:20px;"></th>
              <th>Cliente</th>
              <th class="w25">Fecha</th>
              <% if (control.check("vendedores")>0) { %>
                <th>Vendedor</th>
              <% } %>
              <th class="tar">Total</th>
              <% if (!seleccionar) { %>
                <th class="w100 th_acciones">Acciones</th>
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

<script type="text/template" id="presupuestos_item_resultados_template">
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
  <td><%= numero %></td>
  <td>
    <% if (visto > 0) { %>
    <i class="fa fa-eye text-muted" title="<%= (visto==1)?"Visto: 1 vez":"Visto: "+visto+" veces" %>"></i>
    <?php /* <% } else if (enviada == 1) { %>
    <i class="fa fa-send-o text-muted" title="Enviado por email"></i>
    */?>
    <% } %>
    <% if (MEGASHOP == 1) { %>
      <% if (stock == 0) { %>
        <span class="label bg-danger">Pendiente</span>
      <% } else { %>
        <span class="label bg-success">Finalizado</span>
      <% } %>
    <% } %>
  </td>
  <td class="data text-info <%= clase %>"><span class="text-info"><%= (isEmpty(cliente)) ? "Consumidor Final" : cliente %></span></td>
  <td class="data <%= clase %>"><%= fecha %></td>
  <% if (control.check("vendedores")>0) { %>
    <td class="data <%= clase %>"><%= vendedor %></td>
  <% } %>
  <td class="data <%= clase %> tar">
    <span class="tag_precio">$ <%= Number(total).format() %></span>
  </td>
  <% if (!seleccionar) { %>
    <td class="p5 <%= clase %> td_acciones">
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <% if (MEGASHOP == 1 && PERFIL == 302 && stock == 0) { %>
            <li><a href="javascript:void(0)" class="procesar_stock" data-id="<%= id %>">Procesar Stock</a></li>
          <% } %>
          <li><a href="javascript:void(0)" class="imprimir" data-id="<%= id %>">Imprimir</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>  
    </td>
  <% } %>
</script>