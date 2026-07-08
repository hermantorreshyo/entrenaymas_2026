<style type="text/css">
tr.no-padding .form-control { height: 28px; padding: 0px 5px; }
.panel-body-xs { padding: 5px !important; }
</style>
<script type="text/template" id="importacion_articulos_template">
<div class="bg-light lter b-b wrapper-sm ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Importacion de datos</h1>
</div>
<div class="wrapper-xs">
  <div class="panel panel-default">

    <div class="panel-body pb0 panel-body-xs">
      <div class="form-group mb0">
        <textarea id="importacion_articulo_observaciones" class="form-control mb0 no-model h80" placeholder="Observaciones del proveedor..."><%= observaciones %></textarea>
      </div>
    </div>

    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a href="#tab_lista4" role="tab" data-toggle="tab"><i class="fa fa-pencil text-warning"></i> Modificados <% if (modificados.length > 0) { %><span class="badge ml5 bg-warning"><%= modificados.length %></span><% } %></a>
      </li>
      <li>
        <a href="#tab_lista2" role="tab" data-toggle="tab"><i class="fa fa-plus text-success"></i> Nuevos <% if (nuevos.length > 0) { %><span class="badge ml5 bg-success"><%= nuevos.length %></span><% } %></a>
      </li>
      <li>
        <a href="#tab_lista3" role="tab" data-toggle="tab"><i class="fa fa-times text-danger"></i> Eliminados <% if (eliminados.length > 0) { %><span class="badge ml5 bg-danger"><%= eliminados.length %></span><% } %></a>
      </li>
      <li>
        <a href="#tab_lista1" role="tab" data-toggle="tab"><i class="fa fa-pencil text-warning"></i> No Modificados <% if (no_modificados.length > 0) { %><span class="badge ml5 bg-warning"><%= no_modificados.length %></span><% } %></a>
      </li>
      <% var permiso = control.check("importaciones_articulos") %>
      <% if (!(estado==3 && permiso<3)) { %>
        <button class="btn btn-success guardar mr15 pull-right">Guardar</button>
      <% } %>
      <a href="javascript:window.history.back()" class="btn btn-default mr15 pull-right">Cancelar</a>
      <a href="app/#web_seo" target="_blank" class="btn btn-default mr15 pull-right">Dolar: <b>$<%= Number(COTIZACION_DOLAR).toFixed(2) %></b></a>
    </ul>
    <div class="tab-content">
      <div id="tab_lista1" class="tab-pane panel-body panel-body-xs">
        <div class="b-a table-responsive">
          <table id="importacion_articulos_no_modificados" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="pr2 pl2" style="width: 20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">Interno</th>
                <th class="pr0 pl0">Prov.</th>
                <th class="">Nombre</th>
                <th class="pr0 pl0">Bulto</th>
                <th class="pr0 pl0">Lista USD</th>
                <th class="pr0 pl0">Lista $</th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_no_modificados_modif_costo_1_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_no_modificados_modif_costo_2_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_no_modificados_modif_costo_3_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_no_modificados_modif_costo_4_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_no_modificados_modif_costo_5_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">Costo ($)</th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_no_modificados_coeficiente_check" type="checkbox"><i></i>
                  </label>
                  Coef.
                </th>
                <th class="pr0 pl0">IVA</th>
                <th class="pr0 pl0">s/IVA ($)</th>
                <th class="pr0 pl0">Venta ($)</th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
      </div>
      <div id="tab_lista4" class="tab-pane panel-body panel-body-xs active">
        <div class="b-a table-responsive">
          <table id="importacion_articulos_modificaciones" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="pr2 pl2" style="width: 20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">Interno</th>
                <th class="pr0 pl0">Prov.</th>
                <th class="">Nombre</th>
                <th class="pr0 pl0">Bulto</th>
                <th class="pr0 pl0">Anterior</th>
                <th class="pr0 pl0">Lista USD</th>
                <th class="pr0 pl0">Lista $</th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_modificaciones_modif_costo_1_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_modificaciones_modif_costo_2_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_modificaciones_modif_costo_3_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_modificaciones_modif_costo_4_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_modificaciones_modif_costo_5_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">Costo ($)</th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_modificaciones_coeficiente_check" type="checkbox"><i></i>
                  </label>
                  Coef.
                </th>
                <th class="pr0 pl0">IVA</th>
                <th class="pr0 pl0">s/IVA ($)</th>
                <th class="pr0 pl0">Venta ($)</th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
      </div>
      <div id="tab_lista2" class="tab-pane panel-body panel-body-xs">
        <% if (estado <= 1) { %>
          <div class="oh mb10 mt10">
            <button class="btn btn-info agregar_fila">Nuevo Producto</button>
          </div>
        <% } %>
        <div class="b-a table-responsive">
          <table id="importacion_articulos_nuevos" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="pr2 pl2" style="width: 20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">Interno</th>
                <th class="pr0 pl0">Prov.</th>
                <th class="">Nombre</th>
                <th class="pl0 pr0">Bulto</th>
                <th class="pl0 pr0">Lista USD</th>
                <th class="pr0 pl0">Lista $</th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_nuevos_modif_costo_1_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_nuevos_modif_costo_2_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_nuevos_modif_costo_3_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_nuevos_modif_costo_4_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_nuevos_modif_costo_5_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pl0 pr0">Costo ($)</th>
                <th class="pl0 pr0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_nuevos_coeficiente_check" type="checkbox"><i></i>
                  </label>
                  Coef.
                </th>
                <th class="pr0 pl0">IVA</th>
                <th class="pr0 pl0">s/IVA ($)</th>
                <th class="pl0 pr0">Venta ($)</th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
      </div>
      <div id="tab_lista3" class="tab-pane panel-body panel-body-xs">
        <div class="b-a table-responsive">
          <table id="importacion_articulos_eliminados" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="pr2 pl2" style="width: 20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">Interno</th>
                <th class="pr0 pl0">Prov.</th>
                <th class="">Nombre</th>
                <th class="pr0 pl0">Bulto</th>
                <th class="pr0 pl0">Lista USD</th>
                <th class="pr0 pl0">Lista $</th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_eliminados_modif_costo_1_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_eliminados_modif_costo_2_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_eliminados_modif_costo_3_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_eliminados_modif_costo_4_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_eliminados_modif_costo_5_check" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="pr0 pl0">Costo ($)</th>
                <th class="pr0 pl0">
                  <label class="i-checks m-b-none">
                    <input id="importacion_articulos_eliminados_coeficiente_check" type="checkbox"><i></i>
                  </label>
                  Coef.
                </th>
                <th class="pr0 pl0">IVA</th>
                <th class="pr0 pl0">s/IVA ($)</th>
                <th class="pr0 pl0">Venta ($)</th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>  
</script>


<script type="text/template" id="importacion_articulos_item_template">
  <td class="pl2 pr2">
    <label class="i-checks m-b-none">
      <input <%= (estado==1)?"checked":"" %> class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <input type="hidden" value="<%= id %>" class="id"/>
  <input type="hidden" value="<%= costo_neto %>" class="costo_neto"/>
  <input type="hidden" value="<%= moneda %>" class="moneda"/>

  <td class="pl0 pr0"><input type="text" style="max-width:90px" class="form-control flechas br0 codigo" name="codigo" data-campo="codigo" value="<%= codigo %>" /></td>
  <td class="pl0 pr0"><input type="text" style="max-width:90px" class="form-control flechas br0 codigo_prov" name="codigo_prov" data-campo="codigo_prov" value="<%= codigo_prov %>" /></td>

  <% if (typeof agregado != undefined && agregado == 1) { %>
    <td class="pl5 pr5"><input name="nombre" type="text" class="form-control br0 nombre"/></td>
  <% } else { %>
    <td class="pl5 pr5"><span class="<%= (modifico_costo == 0)?"":((modifico_costo == 1)?"text-danger":"text-success") %>"><%= nombre %></span></td>
  <% } %>

  <td class="pl0 pr0"><input type="text" data-campo="bulto" name="bulto" class="form-control flechas br0 w50 bulto" value="<%= bulto %>" /></td>

  <% if (tipo_modif == 'M') { %>
    <td class="pl0 pr0"><input type="text" class="form-control no-model br0 costo_anterior" disabled value="<%= costo_anterior %>" /></td>
  <% } %>

  <td class="pl0 pr0"><input type="text" data-campo="costo_neto_inicial_dolar" class="w80 form-control no-model flechas br0 costo_neto_inicial_dolar" value="<%= costo_neto_inicial_dolar %>" /></td>
  <td class="pl0 pr0"><input type="text" data-campo="costo_neto_inicial" class="w80 form-control no-model flechas br0 costo_neto_inicial" value="<%= costo_neto_inicial %>" /></td>
  <td class="pl0 pr0"><input type="text" data-campo="modif_costo_1" class="form-control w60 no-model flechas br0 modif_costo_1" value="<%= modif_costo_1 %>" /></td>
  <td class="pl0 pr0"><input type="text" data-campo="modif_costo_2" class="form-control w60 no-model flechas br0 modif_costo_2" value="<%= modif_costo_2 %>" /></td>
  <td class="pl0 pr0"><input type="text" data-campo="modif_costo_3" class="form-control w60 no-model flechas br0 modif_costo_3" value="<%= modif_costo_3 %>" /></td>
  <td class="pl0 pr0"><input type="text" data-campo="modif_costo_4" class="form-control w60 no-model flechas br0 modif_costo_4" value="<%= modif_costo_4 %>" /></td>
  <td class="pl0 pr0"><input type="text" data-campo="modif_costo_5" class="form-control w60 no-model flechas br0 modif_costo_5" value="<%= modif_costo_5 %>" /></td>
  <td class="pl0 pr0"><input type="text" disabled class="form-control no-model br0 w80 costo_final" value="<%= costo_final %>" /></td>
  <td class="pl0 pr0"><input type="text" data-campo="coeficiente" class="form-control w100 no-model flechas br0 coeficiente" value="<%= coeficiente %>" /></td>
  <td class="pl0 pr0">
    <select data-campo="porc_iva" class="w70 form-control flechas br0 porc_iva" name="porc_iva">
      <option value="21" <%= (porc_iva == 21)?"selected":"" %>>21</option>
      <option value="10.5" <%= (porc_iva == 10.5)?"selected":"" %>>10.5</option>
    </select>
  </td>
  <td class="pl0 pr0"><input type="text" disabled class="form-control no-model w80 br0 precio_neto" value="<%= precio_neto %>" /></td>
  <td class="pl0 pr0"><input type="text" data-campo="precio_final" class="form-control w80 no-model flechas br0 precio_final" value="<%= precio_final %>" /></td>
</script>

<script type="text/template" id="importaciones_articulos_resultados_template">
  <div class="bg-light lter b-b wrapper-sm ng-scope">
    <div class="row clearfix padder">
      <h1 class="m-n font-thin h3 pull-left">Importaciones de Articulos</h1>
    </div>
  </div>
  <div class="wrapper-xs ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
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
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a href="app/#web_seo" target="_blank" class="btn btn-default mr15">Dolar: <b>$<%= Number(COTIZACION_DOLAR).toFixed(2) %></b></a>
            <a class="nuevo btn btn-info btn-addon" href="javascript:void(0)">
              <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
            </a>
          </div>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="display:none">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">
            <div style="width: 100px; display: inline-block">
              <input type="text" class="w100p form-control" id="importaciones_articulos_desde" placeholder="Desde" />  
            </div>
            <div style="width: 100px; display: inline-block">
              <input type="text" class="w100p form-control" id="importaciones_articulos_hasta" placeholder="Hasta" />  
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
      <div class="panel-body panel-body-xs">
        <div class="b-a table-responsive">
          <table id="importaciones_articulos_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="w20">ID</th>
                <th>Proveedor</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th class="w20"></th>
                <th class="w20"></th>
                <th class="w20"></th>
                <th class="w20"></th>
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
<script type="text/template" id="importaciones_articulos_item_resultados_template">
  <% var clase = ""; %>
  <td class="<%= clase %> w20"><%= id %></td>
  <td class="<%= clase %> capitalize"><span class='text-info'><%= proveedor %></span></td>
  <td class="<%= clase %>"><%= fecha_alta %></td>
  <td class="<%= clase %>"><%= usuario %></td>
  <td class="<%= clase %>">
    <% if (estado == 0) { %>
      <span class="label bg-info">Pendiente</span>
    <% } else if (estado == 1) { %>
      <span class="label bg-danger">Modificado</span>
    <% } else if (estado == 2) { %>
      <span class="label bg-warning">Exportado</span>
    <% } else if (estado == 3) { %>
      <span class="label bg-success">Finalizado</span>
    <% } %>
  </td>
  <td><i title="Exportar" class="fa fa-file-excel-o exportar text-success" /></td>
  <td>
    <% if (estado == 2) { %>
      <i title="Marcar cargado" class="fa fa-check marcar_cargado text-success" />
    <% } %>
  </td>
  <td><i title="Historial de cambios" class="fa fa-file-o verlog text-info" /></td>
  <td><i title="Eliminar" class="glyphicon glyphicon-remove delete text-danger" /></td>
</script>

<script type="text/template" id="importaciones_articulos_log_template">
<div class='modal-content'>
  <div class='modal-header'>
    <b>Registro de eventos</b>
  </div>
  <div class="modal-body">
    <div class="b-a table-responsive" style="min-height: 300px;">
      <table class="table table-small table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Evento</th>
          </tr>
        </thead>
        <tbody>
          <% for (var i = 0; i < resultado.length; i++) { %>
            <% var obj = resultado[i] %>
            <tr>
              <td><%= obj.fecha %></td>
              <td><%= obj.usuario %></td>
              <td><%= obj.texto %></td>
            </tr>
          <% } %>
        </tbody>
      </table>              
    </div>
  </div>
</div>
</script>