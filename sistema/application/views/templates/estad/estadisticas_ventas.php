<script type="text/template" id="estadisticas_ventas_template">
  <div id="estadisticas_ventas_container" class="col">

    <?php include("print_header.php"); ?>

    <div class="bg-light titulo-pagina lter b-b wrapper-md no-print">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i><?php echo lang(array("es"=>"Estad&iacute;sticas","en"=>"Statistics")); ?>
            / <b><?php echo lang(array("es"=>"Ventas","en"=>"Sales")); ?></b>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="panel panel-default">
        <div class="panel-body no-print">
          <div class="">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_ventas_fecha_desde" value="<%= fecha_desde %>" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_ventas_fecha_hasta" value="<%= fecha_hasta %>" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>

            <% if (ID_SUCURSAL == 0 && typeof almacenes != "undefined") { %>
              <select class="form-control pull-left m-l-xs <%= (almacenes.length <= 1)?"dn":"" %>" style="display: inline-block; width: 160px;" id="estadisticas_ventas_sucursales">
                <option <%= (id_sucursal == 0)?"selected":"" %> value="0">Sucursal</option>
                <% for(var i=0; i< almacenes.length; i++) { %>
                  <% var alm = almacenes[i] %>
                  <option <%= (id_sucursal == alm.id)?"selected":"" %> value="<%= alm.id %>"><%= alm.nombre %></option>
                <% } %>
              </select>
            <% } %>

            <% if (control.check("puntos_venta")>0 || MEGASHOP == 1 || ID_EMPRESA == 421) { %>
              <select class="form-control pull-left m-l-xs" style="display: inline-block; width: 160px;" id="estadisticas_ventas_puntos_venta">
                <option <%= (id_punto_venta==-1)?"selected":"" %> value="-1">Punto de Venta</option>
                <% for(var i=0;i< puntos_venta.length;i++) { %>
                  <% var pv = puntos_venta[i] %>
                  <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == pv.id_sucursal) { %>
                    <option <%= (id_punto_venta==pv.id)?"selected":"" %> value="<%= pv.id %>"><%= pv.nombre %></option>
                  <% } %>  
                <% } %>
              </select>
            <% } %>

            <% if (control.check("repartos")>1) { %>
              <div class="pull-left" style="width: 100px;">
                <input placeholder="Reparto" type="text" id="estadisticas_ventas_reparto" value="<%= reparto %>" class="form-control">
              </div>
            <% } %>

            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>
      
      <div class="row pagina">
        <div class="col-md-5">
          <div class="row row-sm text-center">
            <div class="col-xs-6">
              <div class="panel padder-v item bg-info" style="height: 140px">
                <div class="h2 font-thin text-white m-t-md">$ <%= Number(total_ventas).format(2) %></div>
                <span class="text-muted text-md pt10 db"><?php echo lang(array("es"=>"Total de ventas","en"=>"Total Sales")); ?></span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item bg-success" style="height: 140px">
                <div class="h2 font-thin text-white m-t-md"><%= cantidad_operaciones %></div>
                <span class="text-muted text-md pt10 db"><?php echo lang(array("es"=>"Cantidad de operaciones","en"=>"Quantity")); ?></span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item" style="height: 140px">
                <span class="font-thin h2 block m-t-md">$ <%= Number(venta_promedio).format(2) %></span>
                <span class="text-muted text-md pt10 db">Ticket promedio</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel padder-v item" style="height: 140px">
                <div class="font-thin h2 m-t-md">$ <%= Number(venta_promedio_por_dia).format(2) %></div>
                <span class="text-muted text-md pt10 db">Venta promedio por dia</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7 page-break">
          <div class="panel wrapper">
            <h4 class="font-thin m-t-none m-b text-muted">Visi&oacute;n general</h4>
            <div id="estadisticas_ventas_graficos" style="height: 235px;"></div>
          </div>
        </div>

        <% if (ID_PROYECTO == 1) { %>
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-4 col-xs-12">
                <div class="panel padder-v item tac" style="height: 117px">
                  <div class="font-thin h2 m-t-sm">$ <%= Number(costo_mercaderia_vendida).format(2) %></div>
                  <span class="text-muted text-md pt10 db">Costo de la mercaderia vendida</span>
                </div>
              </div>
              <div class="col-md-4 col-xs-6">
                <div class="block panel padder-v item tac" style="height: 117px">
                  <div class="font-thin h2 m-t-sm">$ <%= Number(ganancia_bruta).format(2) %></div>
                  <span class="text-muted text-md pt10 db">Ganancia bruta</span>
                </div>
              </div>
              <div class="col-md-4 col-xs-6">
                <div class="block panel padder-v item tac" style="height: 117px">
                  <span class="font-thin h2 block m-t-sm"><%= Number(marcacion_promedio).format(2) %></span>
                  <span class="text-muted text-md pt10 db">% de marcaci&oacute;n promedio</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-3 col-xs-6">
                <div class="block panel padder-v item tac" style="height: 117px">
                  <span class="font-thin h2 block m-t-sm"><%= Number(total_ofertas).format(2) %></span>
                  <span class="text-muted text-md pt10 db">En Oferta</span>
                </div>
              </div>
              <div class="col-md-3 col-xs-6">
                <div class="block panel padder-v item tac" style="height: 117px">
                  <span class="font-thin h2 block m-t-sm"><%= Number(total_descuentos).format(2) %></span>
                  <span class="text-muted text-md pt10 db">Descuentos realizados</span>
                </div>
              </div>
              <div class="col-md-3 col-xs-6">
                <div class="block panel padder-v item tac" style="height: 117px">
                  <span class="font-thin h2 block m-t-sm"><%= Number(total_clientes).format(2) %></span>
                  <span class="text-muted text-md pt10 db">Total a Clientes</span>
                </div>
              </div>
              <div class="col-md-3 col-xs-6">
                <div class="block panel padder-v item tac" style="height: 117px">
                  <span class="font-thin h2 block m-t-sm"><%= Number(total_ventas - total_clientes).format(2) %></span>
                  <span class="text-muted text-md pt10 db">Total a CF</span>
                </div>
              </div>
            </div>
          </div>
        <% } %>

      </div>
    
      <div class="pagina row">
        <div class="col-xs-12 col-md-4">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Productos m&aacute;s vendidos</div>
            <table class="estadisticas_ventas_table table-small table table-striped m-b-none">
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
            <div class="panel-heading font-bold">Productos con mayor ganancia</div>
            <table class="estadisticas_ventas_table table-small table table-striped m-b-none">
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
            <div class="panel-heading font-bold">Formas de pago</div>
            <div class="panel-body" style="padding-top: 0px">
              <div id="dispositivos_bar" style="height: 200px"></div>
            </div>
            <div class="panel-footer">
              <span class="label bg-success m-r-xs">1</span>
              <small>Efectivo</small>
              <small class="pull-right">$ <%= Number(efectivo).format(2) %></small>
            </div>
            <div class="panel-footer">
              <span class="label bg-info m-r-xs">2</span>
              <small>Tarjeta</small>
              <small class="pull-right">$ <%= Number(tarjetas).format(2) %></small>
            </div>
            <div class="panel-footer">
              <span class="label bg-warning m-r-xs">3</span>
              <small>Cuenta corriente</small>
              <small class="pull-right">$ <%= Number(cuenta_corriente).format(2) %></small>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_ventas_graficos_template">
  <div style="min-height: 250px" class="grafico"></div>
</script>

<?php /*
<script type="text/template" id="estadisticas_ventas_template">
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
              <select id="estadisticas_ventas_parametro" class="form-control no-model">
                <option value="T">Totales ($)</option>
                <option value="N">Netos ($)</option>
                <option value="C">Cantidades</option>
              </select>
            </div>
            <div style="display: none;">
              <div class="line b-b line-lg"></div>
              <h5 class="m-t-xs">Filtros:</h5>
              <div class="form-group">
                <select id="estadisticas_ventas_rubros" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_ventas_rubros_opciones"></div>
                <label id="estadisticas_ventas_rubros_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="rubros" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <div class="form-group">
                <select id="estadisticas_ventas_articulos" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_ventas_articulos_opciones"></div>
                <label id="estadisticas_ventas_articulos_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="articulos" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <% if (control.check("vendedores")>0) { %>
              <div class="form-group">
                <select id="estadisticas_ventas_vendedores" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_ventas_vendedores_opciones"></div>
                <label id="estadisticas_ventas_vendedores_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="vendedores" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <% } %>
              <div class="form-group">
                <select id="estadisticas_ventas_clientes" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_ventas_clientes_opciones"></div>
                <label id="estadisticas_ventas_clientes_comparar" style="display: none;" class="checkbox i-checks">
                  <input value="clientes" class="comparar" type="checkbox"><i></i>
                  Comparar
                </label>
              </div>
              <% if (control.check("proveedores")>0) { %>
              <div class="form-group">
                <select id="estadisticas_ventas_proveedores" class="w100p no-model">
                </select>
                <div class="m-t-xs" id="estadisticas_ventas_proveedores_opciones"></div>
                <label id="estadisticas_ventas_proveedores_comparar" style="display: none;" class="checkbox i-checks">
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
            <div id="estadisticas_ventas_fecha_inicial"></div>
            <div id="estadisticas_ventas_fechas_opciones"></div>
          </div>
          <div class="form-group dn">
            <select id="estadisticas_ventas_intervalo" class="form-control">
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
              <div id="estadisticas_ventas_graficos" style="height: 235px;"></div>
              <div style="margin-top: 10px;">
                Total: <b id="estadisticas_ventas_total"></b><br/>
                Costo Mercaderia Vendida: <b id="estadisticas_ventas_total_costo"></b><br/>
                Ganancia bruta: <b id="estadisticas_ventas_ganancia"></b><br/>
                Marcaci&oacute;n promedio: <b id="estadisticas_ventas_porc_marc_promedio"></b><br/>
              </div>
            </div>
            <div id="tab2" class="tab-pane panel-body">
              <div id="estadisticas_ventas_listados" style="height: 235px;"></div>
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

<script type="text/template" id="estadisticas_ventas_fechas_template">
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