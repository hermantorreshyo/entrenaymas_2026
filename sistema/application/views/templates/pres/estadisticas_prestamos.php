<script type="text/template" id="estadisticas_prestamos_template">
  <div id="estadisticas_prestamos_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md">
      <div class="row">
        <div class="col-lg-6 col-sm-4 col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Pagos y Otorgaciones</b>
          </h1>
        </div>
        <div class="col-lg-6 col-sm-8 col-xs-12">
          <div class="pull-right">
            <input type="text" id="estadisticas_prestamos_fecha_desde" autocomplete="off" value="<%= fecha_desde %>" class="form-control w120 pull-left">
            <button id="fecha_desde_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
            <input type="text" id="estadisticas_prestamos_fecha_hasta" autocomplete="off" value="<%= fecha_hasta %>" class="form-control w120 m-l-xs pull-left">
            <button id="fecha_hasta_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>

            <select class="form-control pull-left m-l-xs" style="display: inline-block; width: 160px;" id="estadisticas_prestamos_sucursales">
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
                  <option <%= (id_sucursal == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                <% } %>
              <% } %>
            </select>

            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i> Buscar</button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="wrapper-md">
      
      <div class="row pagina">
        <div class="col-md-5">
          <div class="row row-sm text-center">
            <div class="col-xs-6">
              <div class="panel padder-v item bg-success" style="height: 140px">
                <div class="h2 font-thin m-t-md">$ <%= Number(total_pagos).toFixed(2) %></div>
                <span class="text-muted text-md pt10 db">Total Pagos</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item bg-info" style="height: 140px">
                <div class="h2 font-thin text-white m-t-md">$ <%= Number(total_otorgaciones).toFixed(2) %></div>
                <span class="text-muted text-md pt10 db">Total Otorgaciones</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item" style="height: 140px">
                <span class="font-thin h2 block m-t-md"><%= cantidad_pagos %></span>
                <span class="text-muted text-md pt10 db">Cant. Pagos</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel padder-v item ver_cantidad_otorgaciones cp" style="height: 140px">
                <div class="font-thin h2 m-t-md"><%= Number(cantidad_otorgaciones).toFixed(0) %></div>
                <span class="text-info text-md pt10 db">Cant. Otorgaciones</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item" style="height: 140px">
                <span class="font-thin h2 block m-t-md">$ <%= (cantidad_pagos > 0) ? Number(total_pagos / cantidad_pagos).toFixed(2) : Number(0).toFixed(2) %></span>
                <span class="text-muted text-md pt10 db">Promedio Pagos</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel padder-v item" style="height: 140px">
                <span class="font-thin h2 block m-t-md">$ <%= (cantidad_otorgaciones > 0) ? Number(total_otorgaciones / cantidad_otorgaciones).toFixed(2) : Number(0).toFixed(2) %></span>
                <span class="text-muted text-md pt10 db">Promedio Otorgaciones</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="panel wrapper">
            <h4 class="font-thin m-t-none m-b text-muted">Visi&oacute;n general</h4>
            <div id="estadisticas_prestamos_graficos" style="height: 235px;"></div>
          </div>
        </div>
      </div>
    
      <?php /*
      <div class="pagina row">
        <div class="col-xs-12 col-md-4">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Clientes con mayor mora</div>
            <table class="estadisticas_prestamos_table table-small table table-striped m-b-none">
              <tbody>
                <% for(var i=0;i< productos_mas_vendidos.length;i++) { %>
                <% var o = productos_mas_vendidos[i]; %>
                <tr>
                  <td><%= o.nombre %></td>
                  <td class="tar"><%= o.cantidad %></td>
                </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-xs-12 col-md-4">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Top de clientes</div>
            <table class="estadisticas_prestamos_table table-small table table-striped m-b-none">
              <tbody>
                <% for(var i=0;i< productos_mayor_ganancia.length;i++) { %>
                <% var o = productos_mayor_ganancia[i]; %>
                <tr>
                  <td><%= o.nombre %></td>
                  <td class="tar"><%= o.diferencia %></td>
                </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-xs-12 col-md-4">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Tipos de préstamos</div>
            <div class="panel-body" style="padding-top: 0px">
              <div id="dispositivos_bar" style="height: 200px"></div>
            </div>
            <div class="panel-footer">
              <span class="label bg-success m-r-xs">1</span>
              <small>A 30 días</small>
              <small class="pull-right">$ <%= Number(efectivo).toFixed(2) %></small>
            </div>
            <div class="panel-footer">
              <span class="label bg-info m-r-xs">2</span>
              <small>A 45 días</small>
              <small class="pull-right">$ <%= Number(tarjetas).toFixed(2) %></small>
            </div>
            <div class="panel-footer">
              <span class="label bg-warning m-r-xs">3</span>
              <small>A 60 días</small>
              <small class="pull-right">$ <%= Number(cuenta_corriente).toFixed(2) %></small>
            </div>
          </div>
        </div>
      </div>
      */ ?>

    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_prestamos_graficos_template">
  <div style="min-height: 250px" class="grafico"></div>
</script>


<script type="text/template" id="prestamos_detalle_otorgaciones_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <b class="pull-left mt5">Detalle de pr&eacute;stamos otorgados</b>
    <button class="pull-right btn btn-default btn-small cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">  
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a href="#tab_prestamos_otorgados_0" role="tab" data-toggle="tab">Todos (<%= todos.length %>)</a>
      </li>
      <li>
        <a href="#tab_prestamos_otorgados_1" role="tab" data-toggle="tab">Nuevos (<%= nuevos.length %>)</a>
      </li>
      <li>
        <a href="#tab_prestamos_otorgados_2" role="tab" data-toggle="tab">Reingresos (<%= reingresos.length %>)</a>
      </li>
      <li>
        <a href="#tab_prestamos_otorgados_3" role="tab" data-toggle="tab">Paralelos (<%= paralelos.length %>)</a>
      </li>
      <li>
        <a href="#tab_prestamos_otorgados_4" role="tab" data-toggle="tab">Renovaciones (<%= renovaciones.length %>)</a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab_prestamos_otorgados_0" class="tab-pane pr0 pl0 panel-body active">
        <div class="b-a" style="height:250px; overflow: auto;">
          <table class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>#</th>
                <th>Monto</th>
                <th>Cant. Cuotas</th>
                <th>Valor Cuota</th>
                <th>Pagas</th>
                <th>Ult. Pago</th>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i< todos.length;i++) { %>
                <% var obj = todos[i] %>
                <tr class="ver_link" data-id="<%= obj.id %>">
                  <td><a href="javascript:void(0)" class="text-info cp"><%= String(obj.apellido+" "+obj.nombre).ucwords() %></a></td>
                  <td><%= obj.numero %></td>
                  <td><%= obj.monto_prestado %></td>
                  <td><%= obj.cantidad_cuotas %></td>
                  <td><%= obj.valor_cuota %></td>
                  <td><%= obj.cantidad_cuotas_pagas %></td>
                  <td><%= obj.fecha_ultimo_pago %></td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>
      <div id="tab_prestamos_otorgados_1" class="tab-pane pr0 pl0 panel-body">
        <div class="b-a" style="height:250px; overflow: auto;">
          <table class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>#</th>
                <th>Monto</th>
                <th>Cant. Cuotas</th>
                <th>Valor Cuota</th>
                <th>Pagas</th>
                <th>Ult. Pago</th>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i< nuevos.length;i++) { %>
                <% var obj = nuevos[i] %>
                <tr class="ver_link" data-id="<%= obj.id %>">
                  <td><a href="javascript:void(0)" class="text-info cp"><%= String(obj.apellido+" "+obj.nombre).ucwords() %></a></td>
                  <td><%= obj.numero %></td>
                  <td><%= obj.monto_prestado %></td>
                  <td><%= obj.cantidad_cuotas %></td>
                  <td><%= obj.valor_cuota %></td>
                  <td><%= obj.cantidad_cuotas_pagas %></td>
                  <td><%= obj.fecha_ultimo_pago %></td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>
      <div id="tab_prestamos_otorgados_2" class="tab-pane pr0 pl0 panel-body">
        <div class="b-a" style="height:250px; overflow: auto;">
          <table class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>#</th>
                <th>Monto</th>
                <th>Cant. Cuotas</th>
                <th>Valor Cuota</th>
                <th>Pagas</th>
                <th>Ult. Pago</th>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i< reingresos.length;i++) { %>
                <% var obj = reingresos[i] %>
                <tr class="ver_link" data-id="<%= obj.id %>">
                  <td><a href="javascript:void(0)" class="text-info cp"><%= String(obj.apellido+" "+obj.nombre).ucwords() %></a></td>
                  <td><%= obj.numero %></td>
                  <td><%= obj.monto_prestado %></td>
                  <td><%= obj.cantidad_cuotas %></td>
                  <td><%= obj.valor_cuota %></td>
                  <td><%= obj.cantidad_cuotas_pagas %></td>
                  <td><%= obj.fecha_ultimo_pago %></td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>
      <div id="tab_prestamos_otorgados_3" class="tab-pane pr0 pl0 panel-body">
        <div class="b-a" style="height:250px; overflow: auto;">
          <table class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>#</th>
                <th>Monto</th>
                <th>Cant. Cuotas</th>
                <th>Valor Cuota</th>
                <th>Pagas</th>
                <th>Ult. Pago</th>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i< paralelos.length;i++) { %>
                <% var obj = paralelos[i] %>
                <tr class="ver_link" data-id="<%= obj.id %>">
                  <td><a href="javascript:void(0)" class="text-info cp"><%= String(obj.apellido+" "+obj.nombre).ucwords() %></a></td>
                  <td><%= obj.numero %></td>
                  <td><%= obj.monto_prestado %></td>
                  <td><%= obj.cantidad_cuotas %></td>
                  <td><%= obj.valor_cuota %></td>
                  <td><%= obj.cantidad_cuotas_pagas %></td>
                  <td><%= obj.fecha_ultimo_pago %></td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>
      <div id="tab_prestamos_otorgados_4" class="tab-pane pr0 pl0 panel-body">
        <div class="b-a" style="height:250px; overflow: auto;">
          <table class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>#</th>
                <th>Monto</th>
                <th>Cant. Cuotas</th>
                <th>Valor Cuota</th>
                <th>Pagas</th>
                <th>Ult. Pago</th>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i< renovaciones.length;i++) { %>
                <% var obj = renovaciones[i] %>
                <tr class="ver_link" data-id="<%= obj.id %>">
                  <td><a href="javascript:void(0)" class="text-info cp"><%= String(obj.apellido+" "+obj.nombre).ucwords() %></a></td>
                  <td><%= obj.numero %></td>
                  <td><%= obj.monto_prestado %></td>
                  <td><%= obj.cantidad_cuotas %></td>
                  <td><%= obj.valor_cuota %></td>
                  <td><%= obj.cantidad_cuotas_pagas %></td>
                  <td><%= obj.fecha_ultimo_pago %></td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</script>


<?php /*
<script type="text/template" id="estadisticas_prestamos_template">
<div class="col">
  <div class="bg-light lter b-b wrapper-md">
    <div class="row">
      <div class="col-lg-6 col-sm-4 col-xs-12">
        <h1 class="m-n font-thin h3 text-black">
          <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
          / <b>Ventas</b>
        </h1>
      </div>
    </div>
  </div>
  <div class="wrapper-md">
    <div class="row">
      <div class="col-sm-6 col-md-3">
        <div class="panel panel-default">
          <div class="panel-heading font-bold">
            Par&aacute;metros
          </div>
          <div class="panel-body">
            <h5 class="m-t-xs">Mostrar:</h5>
            <div class="form-group">
              <select id="estadisticas_prestamos_parametro" class="form-control no-model">
                <option value="T">Totales ($)</option>
                <option value="N">Netos ($)</option>
                <option value="C">Cantidades</option>
              </select>
            </div>
            <div style="display: none;">
              <div class="line b-b line-lg"></div>
              <h5 class="m-t-xs">Filtros:</h5>
              <div class="form-group">
                <select id="estadisticas_prestamos_rubros" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_prestamos_rubros_opciones"></div>
                <label id="estadisticas_prestamos_rubros_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="rubros" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <div class="form-group">
                <select id="estadisticas_prestamos_articulos" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_prestamos_articulos_opciones"></div>
                <label id="estadisticas_prestamos_articulos_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="articulos" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <% if (control.check("vendedores")>0) { %>
              <div class="form-group">
                <select id="estadisticas_prestamos_vendedores" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_prestamos_vendedores_opciones"></div>
                <label id="estadisticas_prestamos_vendedores_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="vendedores" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <% } %>
              <div class="form-group">
                <select id="estadisticas_prestamos_clientes" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_prestamos_clientes_opciones"></div>
                <label id="estadisticas_prestamos_clientes_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="clientes" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <% if (control.check("proveedores")>0) { %>
              <div class="form-group">
                <select id="estadisticas_prestamos_proveedores" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_prestamos_proveedores_opciones"></div>
                <label id="estadisticas_prestamos_proveedores_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="proveedores" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <% } %>
          </div>

          <div class="line b-b line-lg"></div>
          <h5 class="m-t-xs">
            Per&iacute;odos de fechas:
            <a class="cp agregar_fecha text-info fr">Agregar</a>
          </h5>
          <div class="form-group">
            <div id="estadisticas_prestamos_fecha_inicial"></div>
            <div id="estadisticas_prestamos_fechas_opciones"></div>
          </div>
          <div class="form-group dn">
            <select id="estadisticas_prestamos_intervalo" class="form-control">
              <option value="D">Por dia</option>
              <option selected value="W">Por semana</option>
              <option value="M">Por mes</option>
            </select>
          </div>

          <div class="line b-b line-lg"></div>
          <button class="buscar btn btn-info btn-block">Buscar</button>

        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-9">
      <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
            <a href="#tab1" role="tab" data-toggle="tab">
              Gr&aacute;fico
            </a>
          </li>
          </ul>
          <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body">
              <div id="estadisticas_prestamos_graficos" style="height: 235px;"></div>
              <div style="margin-top: 10px;">
                Total: <b id="estadisticas_prestamos_total"></b><br/>
                Costo Mercaderia Vendida: <b id="estadisticas_prestamos_total_costo"></b><br/>
                Ganancia bruta: <b id="estadisticas_prestamos_ganancia"></b><br/>
                Marcaci&oacute;n promedio: <b id="estadisticas_prestamos_porc_marc_promedio"></b><br/>
              </div>
            </div>
            <div id="tab2" class="tab-pane panel-body">
              <div id="estadisticas_prestamos_listados" style="height: 235px;"></div>
            </div>
            <div id="tab3" class="tab-pane panel-body">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="estadisticas_prestamos_fechas_template">
  <div class="fechas m-b-xs clearfix">
    <% if (numero > 1) { %>
    <h5 class="m-t-xs">
      Per&iacute;odo <%= numero %>:
      <a class="cp eliminar_fecha text-info fr">Eliminar</a>
    </h5>
    <% } %>
    <div class="col-md-6 p0">
      <div class="input-group">
        <input placeholder="Desde" type="text" class="pr0 fecha_desde form-control no-model">
        <span class="input-group-btn">
          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
        </span>
      </div>
    </div>
    <div class="col-md-6 p0">
      <div class="input-group">
        <input placeholder="Hasta" type="text" class="pr0 fecha_hasta form-control no-model">
        <span class="input-group-btn">
          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
        </span>
      </div>
    </div>
  </div>
</script>
*/ ?>