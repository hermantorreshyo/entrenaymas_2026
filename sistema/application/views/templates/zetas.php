<script type="text/template" id="zetas_panel_template">
  <div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal mr10"></i>Ventas
      /  <b>Zetas</b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="panel panel-default">
    
      <div class="panel-heading">
        <div class="clearfix">
          <div class="input-group fl ml5" style="width: 140px;">
            <input type="text" value="<%= window.zetas_fecha_desde %>" placeholder="Desde" id="zetas_fecha_desde" class="form-control">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
          <div class="input-group fl ml5" style="width: 140px;">
            <input type="text" value="<%= window.zetas_fecha_hasta %>" placeholder="Hasta" class="form-control" id="zetas_fecha_hasta" />
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
          <select class="form-control pull-left m-l-xs" style="display: inline-block; width: 140px;" id="zetas_razones_sociales">
            <% if (ID_SUCURSAL != 0) { %>
              <% var id_razon_social = 0 %>
              <% for(var i=0; i< almacenes.length; i++) { %>
                <% var almacen = almacenes[i] %>
                <% if (almacen.id == ID_SUCURSAL) { %>
                  <% id_razon_social = almacen.id_razon_social %>
                <% } %>
              <% } %>
              <% for(var i=0; i< razones_sociales.length; i++) { %>
                <% var raz = razones_sociales[i] %>
                <% if (id_razon_social == raz.id) { %>
                  <option value="<%= raz.id %>"><%= raz.nombre %></option>
                <% } %>
              <% } %>
            <% } else { %>
              <option value="0">Razon Social</option>
              <% for(var i=0; i< razones_sociales.length; i++) { %>
                <% var alm = razones_sociales[i] %>
                <option <%= (window.zetas_id_razon_social == alm.id)?"selected":"" %> value="<%= alm.id %>"><%= alm.nombre %></option>
              <% } %>
            <% } %>
          </select>
          <% if ((MEGASHOP == 1 || ID_EMPRESA == 421) && typeof almacenes != "undefined") { %>
            <select class="form-control pull-left m-l-xs" style="display: inline-block; width: 140px;" id="zetas_sucursales">
              <% if (ID_SUCURSAL != 0) { %>
                <% for(var i=0; i< almacenes.length; i++) { %>
                  <% var alm = almacenes[i] %>
                  <% if (ID_SUCURSAL == alm.id) { %>
                    <option value="<%= alm.id %>"><%= alm.nombre %></option>
                  <% } %>
                <% } %>
              <% } else { %>
                <option value="0">Sucursal</option>
                <% for(var i=0; i< almacenes.length; i++) { %>
                  <% var alm = almacenes[i] %>
                  <option <%= (window.zetas_id_sucursal == alm.id)?"selected":"" %> value="<%= alm.id %>"><%= alm.nombre %></option>
                <% } %>
              <% } %>
            </select>
          <% } %>
          <% if (control.check("puntos_venta")>0 || MEGASHOP == 1 || ID_EMPRESA == 421) { %>
            <select class="form-control pull-left m-l-xs" style="display: inline-block; width: 140px;" id="zetas_puntos_venta">
              <option <%= (window.zetas_id_razon_social==-1)?"selected":"" %> value="-1">Punto de Venta</option>
              <% for(var i=0;i< puntos_venta.length;i++) { %>
                <% var pv = puntos_venta[i] %>
                <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == pv.id_sucursal) { %>
                  <option <%= (window.zetas_id_razon_social==pv.id)?"selected":"" %> value="<%= pv.id %>"><%= pv.sucursal+" PV: "+pv.numero_fiscal %></option>
                <% } %>  
              <% } %>
            </select>
          <% } %>

          <div class="dib ml5" style="width: 90px">
            <input type="number" placeholder="Nro. Pagina" class="form-control" id="zetas_desde" />
          </div>
          <div class="dib ml5">
            <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
          </div>
          <div class="pull-right">
            <div class="btn-group dropdown">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="fa fa-cog"></i><span>Opciones</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void" class="imprimir">Imprimir Libro IVA</a></li>
                <li><a href="javascript:void" class="citi">Exportar CITI</a></li>
                <li><a href="javascript:void" class="exportar_excel">Exportar Excel</a></li>
              </ul>
            </div>
            <% if (permiso > 1) { %>
              <a class="btn btn-info btn-addon ml5" href="app/#zeta">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            <% } %>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="zetas_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th>PV</th>
                <th>Fecha</th>
                <th>N&uacute;mero</th>
                <% if (MEGASHOP != 1) { %>
                  <th>Desde</th>
                  <th>Hasta</th>
                <% } %>
                <th class="tar">Neto</th>
                <th class="tar">IVA</th>
                <th class="tar">Total</th>
                <th class="w100"></th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
            <tfoot>
              <tr class="bg-important">
                <td colspan="<%= (MEGASHOP == 1 || ID_EMPRESA == 421)?4:6 %>"></td>
                <td id="zetas_panel_neto" class="tar negro bold"></td>
                <td id="zetas_panel_iva" class="tar negro bold"></td>
                <td id="zetas_panel_total" class="tar negro bold"></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>  
</script>


<script type="text/template" id="zetas_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td><span class='ver'><%= punto_venta %></span></td>
  <td><span class='ver'><%= fecha %></span></td>
  <td><span class='ver'><%= numero %> <%= (anulada==1)?"(ANULADO)":"" %></span></td>
  <% if (MEGASHOP != 1) { %>
    <td><span class='ver'><%= comp_desde %></span></td>
    <td><span class='ver'><%= comp_hasta %></span></td>
  <% } %>
  <td class="tar"><span class='ver'>$ <%= Number(neto).toFixed(2) %></span></td>
  <td class="tar"><span class='ver'>$ <%= Number(iva).toFixed(2) %></span></td>
  <td class="tar"><span class='ver'>$ <%= Number(total).toFixed(2) %></span></td>
  <td class="p5 td_acciones">
    <% if (permiso > 1) { %>
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

<script type="text/template" id="zetas_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal mr10"></i>Ventas
    / Zetas
    /  <b><%= (id == undefined) ? 'Nuevo' : numero %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Punto de Venta</label>
                    <select id="zetas_punto_venta" class="form-control no-model">
                      <% for(var i=0;i< puntos_venta.length;i++) { %>
                        <% var pv = puntos_venta[i] %>
                        <% if (ID_SUCURSAL != 0) { %>
                          <% if (pv.id_sucursal == ID_SUCURSAL) { %>
                            <option <%= (pv.id == id_punto_venta)?"selected":"" %> data-numero_fiscal="<%= pv.numero_fiscal %>" value="<%= pv.id %>"><%= ((!isEmpty(pv.sucursal)) ? pv.sucursal+" - ":"")+pv.numero_fiscal %></option>
                          <% } %>
                        <% } else { %>
                          <option <%= (pv.id == id_punto_venta)?"selected":"" %> data-numero_fiscal="<%= pv.numero_fiscal %>" value="<%= pv.id %>"><%= ((!isEmpty(pv.sucursal)) ? pv.sucursal+" - ":"")+pv.numero_fiscal %></option>
                        <% } %>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Fecha</label>
                    <input type="text" name="fecha" class="form-control" id="zetas_fecha" value="<%= fecha %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Comprobante</label>
                    <select class="form-control" name="id_tipo_comprobante" id="zetas_comprobantes">
                      <option <%= (id_tipo_comprobante == 82)?"selected":"" %> value="82">Zeta</option>
                      <option <%= (id_tipo_comprobante == 1)?"selected":"" %> value="1">Factura A</option>
                      <option <%= (id_tipo_comprobante == 6)?"selected":"" %> value="6">Factura B</option>
                      <option <%= (id_tipo_comprobante == 3)?"selected":"" %> value="3">Nota Credito A</option>
                      <option <%= (id_tipo_comprobante == 8)?"selected":"" %> value="8">Nota Credito B</option>
                      <option <%= (id_tipo_comprobante == 2)?"selected":"" %> value="2">Nota Debito A</option>
                      <option <%= (id_tipo_comprobante == 7)?"selected":"" %> value="7">Nota Debito B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Numero</label>
                    <input type="text" name="numero" class="form-control" id="zetas_numero" value="<%= numero %>"/>
                  </div>
                </div>
              </div>
              <% if (ID_EMPRESA != 135) { %>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Comprobante desde</label>
                      <input type="text" name="comp_desde" class="form-control" id="zetas_comp_desde" value="<%= comp_desde %>"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Comprobante hasta</label>
                      <input type="text" name="comp_hasta" class="form-control" id="zetas_comp_hasta" value="<%= comp_hasta %>"/>
                    </div>
                  </div>
                </div>
              <% } %>
              <div class="form-group">
                <label class="control-label">Cliente</label>
                <div class="input-group">
                  <input type="text" class="dn" id="zetas_id_cliente" value="<%= id_cliente %>"/>
                  <input title="Ingrese el c&oacute;digo de Cliente o comience a escribir parte del nombre. (0 = Consumidor Final)" type="text" class="form-control action no-model" id="zetas_codigo_cliente" placeholder="Nombre o codigo de cliente" value="<%= cliente %>"/>
                  <span class="input-group-btn">
                    <button title="Atajo: F2 = Buscar" tabindex="-1" id="zetas_buscar_cliente" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                    <button title="Crear nuevo cliente" tabindex="-1" id="zetas_nuevo_cliente" class="btn btn-default" type="button"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Neto 21%</label>
                    <input type="text" <%= (ID_EMPRESA==135 || ID_EMPRESA==121)?"":"disabled" %> name="neto" class="form-control" id="zetas_neto" value="<%= neto %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">IVA 21%</label>
                    <input type="text" <%= (ID_EMPRESA==135 || ID_EMPRESA==121)?"":"disabled" %> name="iva" class="form-control" id="zetas_iva" value="<%= iva %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Neto 10.5%</label>
                    <input type="text" <%= (ID_EMPRESA==135 || ID_EMPRESA==121)?"":"disabled" %> name="neto_105" class="form-control" id="zetas_neto_105" value="<%= neto_105 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">IVA 10.5%</label>
                    <input type="text" <%= (ID_EMPRESA==135 || ID_EMPRESA==121)?"":"disabled" %> name="iva_105" class="form-control" id="zetas_iva_105" value="<%= iva_105 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Neto 0%</label>
                    <input type="text" <%= (ID_EMPRESA==135 || ID_EMPRESA==121)?"":"disabled" %> name="neto_0" class="form-control" id="zetas_neto_0" value="<%= neto_0 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Total</label>
                    <input type="text" name="total" class="form-control" id="zetas_total" value="<%= total %>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="zetas_anulada" name="anulada" value="1" <%= (anulada == 1)?"checked":"" %> ><i></i>
                    El comprobante esta anulado.
                  </label>
                </div>          
              </div>

            </div>
          </div>
        </div>
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>