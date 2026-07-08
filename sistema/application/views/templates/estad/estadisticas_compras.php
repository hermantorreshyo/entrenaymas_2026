<script type="text/template" id="estadisticas_compras_template">
  <% var tipo = (tipo_proveedor == "C") ? "Compras" : "Gastos" %>
  <div id="estadisticas_compras_container" class="col">
    <div class="bg-light lter b-b wrapper-md">
      <div class="row">
        <div class="col-lg-6 col-sm-4 col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b><%= tipo %></b>
          </h1>
        </div>
        <div class="col-lg-6 col-sm-8 col-xs-12">
          <div class="pull-right">
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
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
                <select id="estadisticas_compras_parametro" class="form-control no-model">
                  <option value="T">Totales ($)</option>
                  <option value="N">Netos ($)</option>
                </select>
              </div>
              <div style="display: none;">
                <div class="line b-b line-lg"></div>
                <h5 class="m-t-xs">Filtros:</h5>
                <div class="form-group">
                  <select id="estadisticas_compras_rubros" class="w100p no-model">
                  </select>
                  <div class="m-t-xs" id="estadisticas_compras_rubros_opciones"></div>
                  <label id="estadisticas_compras_rubros_comparar" style="display: none;" class="checkbox i-checks">
                    <input value="rubros" class="comparar" type="checkbox"><i></i>
                    Comparar
                  </label>
                </div>
                <div class="form-group">
                  <select id="estadisticas_compras_articulos" class="w100p no-model">
                  </select>
                  <div class="m-t-xs" id="estadisticas_compras_articulos_opciones"></div>
                  <label id="estadisticas_compras_articulos_comparar" style="display: none;" class="checkbox i-checks">
                    <input value="articulos" class="comparar" type="checkbox"><i></i>
                    Comparar
                  </label>
                </div>
                <% if (control.check("vendedores")>0) { %>
                <div class="form-group">
                  <select id="estadisticas_compras_vendedores" class="w100p no-model">
                  </select>
                  <div class="m-t-xs" id="estadisticas_compras_vendedores_opciones"></div>
                  <label id="estadisticas_compras_vendedores_comparar" style="display: none;" class="checkbox i-checks">
                    <input value="vendedores" class="comparar" type="checkbox"><i></i>
                    Comparar
                  </label>
                </div>
                <% } %>
                <div class="form-group">
                  <select id="estadisticas_compras_clientes" class="w100p no-model">
                  </select>
                  <div class="m-t-xs" id="estadisticas_compras_clientes_opciones"></div>
                  <label id="estadisticas_compras_clientes_comparar" style="display: none;" class="checkbox i-checks">
                    <input value="clientes" class="comparar" type="checkbox"><i></i>
                    Comparar
                  </label>
                </div>
                <% if (control.check("proveedores")>0) { %>
                <div class="form-group">
                  <select id="estadisticas_compras_proveedores" class="w100p no-model">
                  </select>
                  <div class="m-t-xs" id="estadisticas_compras_proveedores_opciones"></div>
                  <label id="estadisticas_compras_proveedores_comparar" style="display: none;" class="checkbox i-checks">
                    <input value="proveedores" class="comparar" type="checkbox"><i></i>
                    Comparar
                  </label>
                </div>
                <% } %>

                        <?php /*
                        SHOPVAR:
                        - Otro filtro (y comparacion) pueden ser los ORIGENES de la VENTA (Web, MercadoLibre, etc)
                        */ ?>
                      </div>

                      <div class="line b-b line-lg"></div>
                      <h5 class="m-t-xs">
                        Per&iacute;odos de fechas:
                        <a class="cp agregar_fecha text-info fr">Agregar</a>
                      </h5>
                      <div class="form-group">
                        <div id="estadisticas_compras_fecha_inicial"></div>
                        <div id="estadisticas_compras_fechas_opciones"></div>
                      </div>
                      <div class="form-group dn">
                        <select id="estadisticas_compras_intervalo" class="form-control">
                          <option value="D">Por dia</option>
                          <option value="W">Por semana</option>
                          <option selected value="M">Por mes</option>
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
                        <?php /*
                        <li>
                            <a href="#tab2" role="tab" data-toggle="tab">
                                Tablas
                            </a>
                        </li>
                        <li>
                            <a href="#tab3" role="tab" data-toggle="tab">
                                Resumen
                            </a>
                        </li>
                        */ ?>
                      </ul>
                      <div class="tab-content">
                        <div id="tab1" class="tab-pane active panel-body">
                          <div id="estadisticas_compras_graficos" style="height: 235px;"></div>
                          <div style="margin-top: 10px;">
                            Total: <b id="estadisticas_compras_total"></b>
                          </div>
                        </div>
                        <div id="tab2" class="tab-pane panel-body">
                          <div id="estadisticas_compras_listados" style="height: 235px;"></div>
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

          <script type="text/template" id="estadisticas_compras_fechas_template">
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


          <script type="text/template" id="estadisticas_compras_graficos_template">
            <div style="min-height: 250px" class="grafico"></div>
          </script>