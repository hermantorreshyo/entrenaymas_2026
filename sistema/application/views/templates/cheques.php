<script type="text/template" id="cheques_panel_template">
  <% if (!lightbox) { %>
    <div class="bg-light lter b-b wrapper-md clearfix">
      <h1 class="m-n font-thin h3"><i class="fa fa-inbox icono_principal"></i>Cajas / <b>Cartera de Cheques</b></h1>
    </div>
  <% } %>
  <div class="<% if (!lightbox) { %>wrapper-md ng-scope<% } %>">
    <div class="panel panel-default">

      <% if (!lightbox) { %>
        <?php $active = "cheques"; include("caja/caja_menu.php"); ?>
      <% } %>

      <div class="panel-heading clearfix">
        <div class="">
          <div class="sm-m-b">
            <div class="fl w200">
              <div class="input-group">
                <input type="text" placeholder="Buscar..." autocomplete="off" id="cheques_texto" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                </span>
                <span class="input-group-btn">
                  <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
                </span>
              </div>
            </div>
            <div class="fl w150 m-l">
              <select class="form-control" id="cheques_tipos">
                <option value="P">Cheques Propios</option>
                <option value="T">Cheques de Terceros</option>
              </select>
            </div>
            <% if (control.check("almacenes")>0 || MEGASHOP == 1) { %>
              <div class="fl w150 m-l">
                <select class="form-control" id="cheques_sucursales">
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
            <% } %>
            <% if (!lightbox) { %>
              <% if (permiso > 1) { %>
                <div class="pull-right">
                  <div class="btn-group dropdown">
                    <button class="btn btn-info dropdown-toggle btn-addon" data-toggle="dropdown">
                      <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo Cheque&nbsp;&nbsp;</span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="javascript:void" class="nuevo_tercero">Tercero</a></li>
                      <li><a href="javascript:void" class="nuevo_propio">Propio</a></li>
                    </ul>
                  </div>
                </div>
              <% } %>
              <div class="pull-right m-r">
                <div class="btn-group dropdown">
                  <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                    <i class="fa fa-cog"></i><span>Opciones</span>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="javascript:void" class="exportar_excel">Exportar Excel</a></li>
                  </ul>
                </div>
              </div>
            <% } %>
          </div>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="display:none">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">
            <div class="form-group dib w150">
              <select id="cheques_bancos" class="w100p form-control">
                <option value="0">Banco</option>
                <% for(var i=0;i<bancos.length;i++) { %>
                <% var banco = bancos[i] %>
                  <option value="<%= banco.id %>"><%= banco.nombre %></option>
                <% } %>
              </select>
            </div>
            <div class="form-group dib w150">
              <input type="text" placeholder="Titular" class="form-control no-model" id="cheques_titular"/>
            </div>
            <div class="form-group dib w150">
              <select id="cheques_mostrar_tipo" class="w100p form-control">
                <option value="">Filtrar</option>
                <option value="D">Solo debitados</option>
                <option value="N">No debitados</option>
                <option value="A">Solo anulados</option>
                <option value="E">No Entregados</option>
              </select>
            </div>
            <div class="form-group dib w150">
              <select id="cheques_fecha_comparacion" class="w100p form-control">
                <option value="0">Buscar por fecha</option>
                <option value="E">Emisi&oacute;n</option>
                <option value="C">Cobro</option>
                <option value="D">Debitado</option>
              </select>
            </div>
            <div class="input-group" style="width: 140px;">
              <input type="text" placeholder="Desde" disabled id="cheques_desde" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group" style="width: 140px;">
              <input type="text" placeholder="Hasta" disabled id="cheques_hasta" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="form-group">
              <button class="buscar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
            </div>            
          </div>
        </div>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs nav-tabs-4" role="tablist">
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
          <div id="tab1" class="tab-pane active">
            <div class="b-a table-responsive">
              <table id="cheques_table" class="m-b-none table-small table sortable default footable">
                <thead>
                  <tr>
                    <th>Banco</th>
                    <th>Numero</th>
                    <th>Titular</th>
                    <th>Recibido</th>
                    <th>Entregado</th>
                    <th class="sorting" data-sort-by="C.fecha_emision">Emisi&oacute;n</th>
                    <th class="sorting" data-sort-by="C.fecha_cobro">Cobro</th>
                    <th class="sorting" data-sort-by="C.fecha_debitado">Debitado</th>
                    <th class="tar">Monto</th>
                    <% if (permiso > 1) { %>
                      <th class="w100 th_acciones"></th>
                    <% } %>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot class="pagination_container hide-if-no-paging"></tfoot>
              </table>
            </div>
            <div>
              Suma Total: <b id="cheques_total"></b>
            </div>
          </div>
          <div id="tab2" class="tab-pane">
            <div class="clearfix">
              <div class="form-group dib w250">
                <select id="cheques_calendario_agrupado" class="w100p form-control">
                  <option value="C">Cheques por separado</option>
                  <option value="D">Agrupado por dia</option>
                  <option value="S">Agrupado por semana</option>
                </select>
              </div>
            </div>
            <div id="cheques_calendario"></div>
          </div>
        </div>
      </div>
    </div>
  </div>  
</script>

<script type="text/template" id="cheques_item">
  <% var clase = "";
  if (anulado == 1) clase = "text-danger";
  %>
  <td class="ver"><span class='<%= clase %>'><%= banco %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= numero %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= titular %></span></td>
  <td class="ver"><span class='<%= clase %>'>
    <%= comprobante %> <%= (!isEmpty(cliente)) ? " - "+cliente : "" %></span>
    <% if (!isEmpty(caja_origen)) { %>
      <br/><%= caja_origen %>
    <% } %>    
  </td>
  <td class="ver"><span class='<%= clase %>'>
    <% if (tipo == 'T' && id_caja_depositado != 0) { %>
      <%= caja_depositado %>
    <% } else { %>
      <%= orden_pago %> <%= (!isEmpty(proveedor)) ? " - "+proveedor : "" %></span>
    <% } %>
  </td>
  <td class="ver"><span class='<%= clase %>'><%= fecha_emision %></span></td>
  <td class="ver"><span class='<%= clase %>'><%= fecha_cobro %></span></td>
  <td class="ver"><span class='<%= clase %>'>
    <%= fecha_debitado %></span>
    <% if (tipo == 'P' && !isEmpty(caja_depositado)) { %><br/><%= caja_depositado %><% } %>
  </td>
  <td class="ver tar"><span class='<%= clase %>'><%= Number(monto).toFixed(2) %></span></td>
  <% if (permiso > 2) { %>
    <td class="tar <%= clase %>">
      <?php /*
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      */ ?>
      <div class="btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <% if (tipo == 'T' && isEmpty(proveedor)) { %>
            <% if (id_caja_depositado == 0) { %>
              <li><a href="javascript:void(0)" class="depositar" data-id="<%= id %>">Depositar</a></li>
            <% } else { %>
              <li><a href="javascript:void(0)" class="eliminar_deposito" data-id="<%= id %>">Eliminar Deposito</a></li>
            <% } %>
          <% } %>
          <% if (tipo == 'P') { %>
            <% if (id_caja_depositado == 0) { %>
              <li><a href="javascript:void(0)" class="debitar" data-id="<%= id %>">Debitar de cuenta</a></li>
            <% } else { %>
              <li><a href="javascript:void(0)" class="eliminar_deposito" data-id="<%= id %>">Eliminar Deposito</a></li>
            <% } %>
          <% } %>
          <% if (id_orden_pago != 0) { %>
            <li><a href="javascript:void(0)" class="orden_pago">Ver Orden de Pago</a></li>
          <% } %>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="cheques_edit_panel_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b><%= (id == undefined) ? "Nuevo cheque":"Editar cheque" %></b>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Numero</label>
          <input type="text" name="numero" class="form-control" id="cheques_numero" value="<%= numero %>"/>
        </div>  
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Banco</label>
          <select class="form-control" id="cheques_bancos">
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
          <input type="text" name="monto" class="form-control" id="cheques_monto" value="<%= monto %>"/>
        </div>  
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha de emisi&oacute;n</label>
          <div class="input-group">
            <input type="text" name="fecha_emision" class="form-control" id="cheques_fecha_emision" value="<%= fecha_emision %>"/>
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
            <input type="text" name="fecha_cobro" class="form-control" id="cheques_fecha_cobro" value="<%= fecha_cobro %>"/>
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
            <input type="text" name="fecha_debitado" class="form-control" id="cheques_fecha_debitado" value="<%= fecha_debitado %>"/>
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
          <input type="checkbox" name="anulado" id="cheques_anulado" class="checkbox" value="1" <%= (anulado == 1)?"checked":"" %> >
          <i></i> El cheque fue anulado
        </label>
      </div>
      <input type="text" placeholder="Observaciones" name="motivo" class="form-control" id="cheques_motivo" value="<%= motivo %>"/>
    <% } %>
  </div>
  <% if (control.check("cheques")>1) { %>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success guardar">Guardar</button>
    </div>
  <% } %>
</div>
</script>


<script type="text/template" id="depositar_cheque_panel_template">
  <div class="panel panel-default">
    <div class="panel-heading"><b><%= titulo %></b></div>
    <div class="panel-body">
      <div class="form-group">
        <label class="control-label">Cuenta</label>
        <select class="form-control no-model" id="depositar_cheque_cajas">
          <% for(var i=0;i< window.cajas.length;i++) { %>
            <% var c = window.cajas[i] %>
            <% if (c.activo == 1) { %>
              <% if (ID_EMPRESA == 641) { %>
                <option value="<%= c.id %>"><%= c.nombre %></option>
              <% } else if (c.tipo == 1) { %>
                <option value="<%= c.id %>"><%= c.nombre %></option>
              <% } %>
            <% } %>
          <% } %>
        </select>
      </div>  
      <div class="form-group">
        <label class="control-label">Fecha Debitado</label>
        <div class="input-group">
          <input type="text" name="fecha_debitado" class="form-control" id="depositar_cheque_fecha_debitado" value="<%= fecha_debitado %>"/>
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>              
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success guardar">Guardar</button>
    </div>
  </div>
</script>