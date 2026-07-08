<script type="text/template" id="ventas_resultados_template">
<% if (!seleccionar) { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var titulo = "<?php echo lang(array("es"=>"Ventas","en"=>"Sales")); ?>" %>
    <% titulo = (ID_EMPRESA == 228) ? "Facturas" : titulo %>
    <% titulo = (ID_EMPRESA == 1284) ? "Pedidos" : titulo %>
    <h1 class="m-n font-thin h3"><i class="fa fa-dollar icono_principal"></i><%= titulo %></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <?php /*
      <% if (typeof FACTURACION_PERIODICA != "undefined" &&  FACTURACION_PERIODICA == 1) { %>
        <ul class="nav nav-tabs nav-tabs-2" role="tablist">
          <li class="<%= (window.ventas_listado_in_tipos_estados == "") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="" role="tab" data-toggle="tab"><?php echo lang(array("es"=>"Todos","en"=>"All")); ?></a>
          </li>
          <li class="<%= (window.ventas_listado_in_tipos_estados == "200") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="200" role="tab" data-toggle="tab"><i class="fa fa-check text-info"></i> En proceso</a>
          </li>
          <li class="<%= (window.ventas_listado_in_tipos_estados == "201") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="201" role="tab" data-toggle="tab"><i class="fa fa-thumbs-up text-success"></i> Pagadas</a>
          </li>
          <li class="<%= (window.ventas_listado_in_tipos_estados == "202") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="202" role="tab" data-toggle="tab"><i class="fa fa-thumbs-down text-danger"></i> Vencidas</a>
          </li>
          <li class="<%= (window.ventas_listado_in_tipos_estados == "203") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="203" role="tab" data-toggle="tab"><i class="fa fa-times text-danger"></i> Adeudadas</a>
          </li>
        </ul>
      <% } else { %>
        <% if (ID_EMPRESA != 224 && MEGASHOP != 1 && ID_EMPRESA != 228 && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
          <ul class="nav nav-tabs nav-tabs-2" role="tablist">
            <li class="<%= (window.ventas_listado_id_origen == "") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab_origen" data-origen="-1" role="tab" data-toggle="tab"><?php echo lang(array("es"=>"Todos","en"=>"All")); ?></a>
            </li>
            <li class="<%= (window.ventas_listado_id_origen == "0") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab_origen" data-origen="0" role="tab" data-toggle="tab"><i class="fa fa-home text-success"></i> Local</a>
            </li>
            <li class="<%= (window.ventas_listado_id_origen == "1") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab_origen" data-origen="1" role="tab" data-toggle="tab"><i class="fa fa-globe text-info"></i> Web</a>
            </li>
            <li class="<%= (window.ventas_listado_id_origen == "2") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab_origen" data-origen="2" role="tab" data-toggle="tab"><img src="/sistema/resources/images/ML-On.png" width="17" style="position:relative;top:-2px;margin-right:5px;" /> MercadoLibre</a>
            </li>
            <li class="<%= (window.ventas_listado_id_origen == "3") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab_origen" data-origen="3" role="tab" data-toggle="tab"><i class="fa fa-mobile text-danger"></i> <?php echo lang(array("es"=>"Aplicacion","en"=>"Application")); ?></a>
            </li>
            <% if (control.check("presupuestos")>0) { %>
              <li>
                <a href="app/#presupuestos"><i class="fa fa-file text-info"></i> <?php echo lang(array("es"=>"Presupuestos","en"=>"Presupuestos")); ?></a>
              </li>
            <% } %>
          </ul>
        <% } %>
      <% } %>
      */ ?>
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-8 sm-m-b">
            <div class="input-group">
              <input type="text" id="ventas_listado_buscar" value="<%= window.ventas_listado_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
              </span>
              <% if (TOQUE == 0) { %>
                <span class="input-group-btn">
                  <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
                </span>
              <% } %>
              <% if (control.check("ventas_listado")>=3) { %>

                <% if ((ID_PROYECTO == 1 || ID_PROYECTO == 2) && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                  <span class="input-group-btn">
                    <div class="btn-group dropdown ml5">
                      <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bank"></i><span><?php echo lang(array("es"=>"Impuestos","en"=>"Taxes")); ?></span>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void" class="iva_ventas">IVA Ventas</a></li>
                        <li><a href="javascript:void" class="percep_ganancias">Percep. Ganancias</a></li>
                        <li><a href="javascript:void" class="percep_iibb">Percep. IIBB</a></li>
                      </ul>
                    </div>
                  </span>
                <% } %>

                <% if (ID_EMPRESA == 980) { %>
                  <span class="input-group-btn">
                    <div class="btn-group dropdown ml5">
                      <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void" class="exportar_yeyo">Exportar Excel</a></li>
                      </ul>
                    </div>
                  </span>
                <% } else { %>
                  <span class="input-group-btn">
                    <div class="btn-group dropdown ml5">
                      <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void" class="exportar">Excel</a></li>
                        <% if (IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                          <li><a href="javascript:void" class="exportar_csv">Exportar CSV</a></li>
                        <% } %>
                      </ul>
                    </div>
                  </span>
                <% } %>

                <% if (IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                  <span class="input-group-btn">
                    <div class="btn-group dropdown ml5">
                      <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-download"></i><span><?php echo lang(array("es"=>"Importar","en"=>"Import")); ?></span>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <% if (ID_EMPRESA != 121) { %>
                          <li><a href="javascript:void" class="importar_csv">Importar CSV</a></li>
                        <% } %>
                        <% if (ID_EMPRESA == 121 || VOLVER_SUPERADMIN == 1) { %>
                          <li><a href="javascript:void" class="importar_caja">Importar Caja</a></li>
                        <% } %>
                      </ul>
                    </div>
                  </span>
                <% } %>

              <% } %>

              <% if ((ID_EMPRESA == 574 || ID_EMPRESA == 1326) && LOCAL == 1) { %>
                <span class="input-group-btn">
                  <div class="btn-group dropdown ml5">
                    <button class="imprimir_caja_tato btn btn-default btn-addon btn-addon-2">
                      <i class="fa fa-print"></i><span>Imprimir</span>
                    </button>
                  </div>
                </span>
              <% } %>


            </div>
          </div>          
          <div class="col-md-4 text-right">
            <% if (control.check("ventas_listado")>=2 && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
              <% var link = (ID_EMPRESA == 86) ? "app/#remitos" : 'app/#facturacion' %>
              <a class="btn btn-info btn-addon ml5" href="<%= link %>">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            <% } %>
          </div>
        </div>
      </div>
      <% var display_search = ( (ID_EMPRESA != 342 && ID_EMPRESA != 1354 && (window.ventas_listado_fecha_desde != "" || window.ventas_listado_fecha_hasta !=  "")) || (window.ventas_listado_hora_desde != "" || window.ventas_listado_hora_hasta !=  "") || window.ventas_listado_con_anulados == 1 || window.ventas_listado_punto_venta != -1 || window.ventas_listado_monto != "" || window.ventas_listado_tarjeta || !isEmpty(window.ventas_listado_lote) || !isEmpty(window.ventas_listado_cupon) || !isEmpty(window.ventas_listado_fecha_reparto) || !isEmpty(window.ventas_listado_numero_reparto) || !isEmpty(window.ventas_listado_codigo_articulo) || (window.ventas_listado_forma_pago != "0") || (window.ventas_listado_pago != "-1") || (window.ventas_listado_tipo_cliente != "") || (window.ventas_listado_tipo_estado != "-1")) ? "display:block":"display:none" %>
      <div class="advanced-search-div bg-light dk" style="<%= display_search %>">

        <% if (ID_EMPRESA == 228) { %>

          <div class="wrapper clearfix">
            <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
            <div class="row pl10 pr10">
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="input-group">
                    <input autocomplete="off" type="text" placeholder="<?php echo lang(array("es"=>"Desde","en"=>"From")); ?>" id="ventas_desde" class="form-control">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>              
                  </div>
                </div>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="input-group">
                    <input autocomplete="off" type="text" placeholder="<?php echo lang(array("es"=>"Hasta","en"=>"To")); ?>" id="ventas_hasta" class="form-control">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>              
                  </div>
                </div>
              </div>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="btn-group dropdown w100p">
                    <button class="btn btn-default tal btn-block dropdown-toggle" data-toggle="dropdown">
                      <span>Tipos de comprobante</span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a href="javascript:void(0)">
                          <label class="i-checks">
                            <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="1-6-11-19-51-201-206" ) ? 'checked=""':'' %>  value="1-6-11-19-51-201-206">
                            <i></i>Factura
                          </label>
                        </a>
                      </li>
                      <li>
                        <a href="javascript:void(0)">
                          <label class="i-checks">
                            <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="3-8-13-21-53-203-208" ) ? 'checked=""':'' %> value="3-8-13-21-53-203-208">
                            <i></i>Nota de Cr&eacute;dito
                          </label>
                        </a>
                      </li>
                      <li>
                        <a href="javascript:void(0)">
                          <label class="i-checks">
                            <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="2-7-12-20-52-202-207" ) ? 'checked=""':'' %> value="2-7-12-20-52-202-207">
                            <i></i>Nota de D&eacute;bito
                          </label>
                        </a>
                      </li>
                      <li>
                        <a href="javascript:void(0)">
                          <label class="i-checks">
                            <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="4-9-15" ) ? 'checked=""':'' %> value="4-9-15">
                            <i></i>Recibo
                          </label>
                        </a>
                      </li>
                      <li>
                        <a href="javascript:void(0)">
                          <label class="i-checks">
                            <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="999" ) ? 'checked=""':'' %> value="999">
                            <i></i>Remito
                          </label>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select class="form-control no-model" id="ventas_almacenes">
                    <option value="0">Sucursal</option>
                    <% for(var i=0;i< almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == o.id) { %>
                        <option <%= (window.ventas_listado_sucursal == o.id) ? "selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    <% } %>
                  </select>
                </div>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <button class="buscar btn btn-block btn-dark btn-default"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
            </div>
          </div>

        <% } else { %>

          <div class="wrapper clearfix">
            <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
            <div class="row pl10 pr10">
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="input-group">
                    <input autocomplete="off" type="text" placeholder="<?php echo lang(array("es"=>"Desde","en"=>"From")); ?>" id="ventas_desde" class="form-control">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>              
                  </div>
                </div>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="input-group">
                    <input autocomplete="off" type="text" placeholder="<?php echo lang(array("es"=>"Hasta","en"=>"To")); ?>" id="ventas_hasta" class="form-control">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>              
                  </div>
                </div>
              </div>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                <div class="row">
                  <div class="col-xs-6 pr0">
                    <div class="form-group">
                      <input autocomplete="off" type="text" value="<%= window.ventas_listado_hora_desde %>" placeholder="Hora desde" id="ventas_hora_desde" class="form-control">
                    </div>
                  </div>
                  <div class="col-xs-6 pl0">
                    <div class="form-group">
                      <input autocomplete="off" type="text" value="<%= window.ventas_listado_hora_hasta %>" placeholder="Hasta" id="ventas_hora_hasta" class="form-control">
                    </div>
                  </div>
                </div>
              </div>

              <% if (ID_PROYECTO == 10) { %>

                <% if (TOQUE == 0) { %>
                  <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                    <div class="form-group">
                      <div class="btn-group dropdown">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                          <span>Tipo</span>
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                          <li>
                            <a href="javascript:void(0)">
                              <label class="i-checks">
                                <input type="checkbox" class="ventas_tipo_check" <%= (isEmpty(tipo) || tipo =="M" ) ? 'checked=""':'' %>  value="M">
                                <i></i>Mesa
                              </label>
                            </a>
                          </li>
                          <li>
                            <a href="javascript:void(0)">
                              <label class="i-checks">
                                <input type="checkbox" class="ventas_tipo_check" <%= (isEmpty(tipo) || tipo =="T" ) ? 'checked=""':'' %> value="T">
                                <i></i>Mostrador
                              </label>
                            </a>
                          </li>
                          <li>
                            <a href="javascript:void(0)">
                              <label class="i-checks">
                                <input type="checkbox" class="ventas_tipo_check" <%= (isEmpty(tipo) || tipo =="D" ) ? 'checked=""':'' %> value="D">
                                <i></i>Delivery
                              </label>
                            </a>
                          </li>
                          <li>
                            <a href="javascript:void(0)">
                              <label class="i-checks">
                                <input type="checkbox" class="ventas_tipo_check" <%= (isEmpty(tipo) || tipo =="B" ) ? 'checked=""':'' %> value="B">
                                <i></i>Barra
                              </label>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                <% } %>

                <% if (control.check("usuarios")>0) { %>
                  <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                    <div class="form-group">
                      <select class="form-control no-model" id="ventas_usuarios">
                        <option value="0">Usuario</option>
                        <% for(var i=0;i< usuarios.models.length;i++) { %>
                          <% var o = usuarios.models[i]; %>
                          <option value="<%= o.id %>"><%= o.nombre %></option>
                        <% } %>
                      </select>
                    </div>
                  </div>
                <% } %>

              <% } else { %>

                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                  <div class="form-group">
                    <select class="form-control no-model" id="ventas_puntos_venta">
                      <option value="-1">Punto de venta</option>
                      <% for(var i=0;i< puntos_venta.length;i++) { %>
                        <% var o = puntos_venta[i]; %>
                        <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == o.id_sucursal) { %>
                          <option <%= (window.ventas_listado_punto_venta == o.id) ? "selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                        <% } %>
                      <% } %>
                    </select>
                  </div>
                </div>

                <% if (!isEmpty(DOMINIO)) { %>
                  <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                    <div class="form-group">
                      <select class="form-control no-model" id="ventas_tipo_estado">
                        <option value="-1"><?php echo lang(array("es"=>"Estado","en"=>"Status")); ?></option>
                        <% for(var i=0;i< tipos_estado_pedidos.length;i++) { %>
                          <% var c = tipos_estado_pedidos[i]; %>
                          <% if (c.id < 100) { %>
                            <option <%= (window.ventas_listado_tipo_estado == c.id)?"selected":"" %> value="<%= c.id %>"><%= c.nombre %></option>
                          <% } %>    
                        <% } %>
                      </select>
                    </div>
                  </div>
                <% } %>

                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                  <div class="form-group">
                    <div class="btn-group dropdown w100p">
                      <button class="btn btn-default tal btn-block dropdown-toggle" data-toggle="dropdown">
                        <span>Tipos de comprobante</span>
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a href="javascript:void(0)">
                            <label class="i-checks">
                              <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="1-6-11" ) ? 'checked=""':'' %>  value="1-6-11">
                              <i></i>Factura
                            </label>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)">
                            <label class="i-checks">
                              <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="3-8-13" ) ? 'checked=""':'' %> value="3-8-13">
                              <i></i>Nota de Cr&eacute;dito
                            </label>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)">
                            <label class="i-checks">
                              <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="2-7-12" ) ? 'checked=""':'' %> value="2-7-12">
                              <i></i>Nota de D&eacute;bito
                            </label>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)">
                            <label class="i-checks">
                              <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="4-9-15" ) ? 'checked=""':'' %> value="4-9-15">
                              <i></i>Recibo
                            </label>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)">
                            <label class="i-checks">
                              <input type="checkbox" class="ventas_tipo_comprobante_check" <%= (isEmpty(tipos_comprobante) || tipos_comprobante =="999" ) ? 'checked=""':'' %> value="999">
                              <i></i>Remito
                            </label>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              <% } %>

              <% if (control.check("vendedores")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select class="form-control no-model" id="ventas_vendedores">
                      <option value="0">Vendedor</option>
                      <% for(var i=0;i< vendedores.length;i++) { %>
                          <% var o = vendedores[i]; %>
                          <option <%= (window.ventas_listado_vendedor == o.id) ? "selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
              <% } %>

              <% if (control.check("conceptos")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <% if (ID_EMPRESA == 399) { %>
                      <select class="form-control no-model" id="ventas_conceptos">
                        <option value='0'>Unidad Negocio</option>
                        <option value='299'>CORRETAJE</option>
                        <option value='457'>AGROINSUMOS</option>
                        <option value='301'>BALANCEADO</option>
                        <option value='300'>TRANSPORTE K</option>
                        <option value='1308'>TRANSPORTE F</option>
                      </select>
                    <% } else { %>
                      <select class="form-control no-model" id="ventas_conceptos">
                        <option value='0'>Concepto</option>
                        <%= workspace.crear_select(tipos_gastos,"",0) %>
                      </select>
                    <% } %>
                  </div>
                </div>
              <% } %>

              <% if (DISTRIBUIDORA == 1) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-btn">
                        <input type="text" class="form-control w50 no-model" placeholder="Nro." id="ventas_numero_reparto" value="<%= window.ventas_listado_numero_reparto %>"/>
                      </span>
                      <input type="text" id="ventas_fecha_reparto" placeholder="Reparto" value="<%= window.ventas_listado_fecha_reparto %>" class="form-control no-model">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              <% } %>  

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                <div class="form-group">            
                  <input type="text" id="ventas_codigo_articulo" value="<%= window.ventas_listado_codigo_articulo %>" placeholder="C&oacute;digo Art." class="form-control no-model">
                </div>
              </div>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                <div class="form-group">
                  <select class="form-control no-model" id="ventas_tipo_cliente">
                    <option <%= (isEmpty(window.ventas_listado_tipo_cliente)) ? "selected":"" %> value="">Tipo de cliente</option>
                    <option <%= (window.ventas_listado_tipo_cliente == "CF") ? "selected":"" %> value="CF">Consumidor Final</option>
                    <option <%= (window.ventas_listado_tipo_cliente == "NCF") ? "selected":"" %> value="NCF">Cliente</option>
                  </select>
                </div>
              </div>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                <div class="form-group">
                  <div class="input-group">
                    <select class="form-control no-model" id="ventas_monto_tipo">
                      <option <%= (window.ventas_listado_monto_tipo == "igual" || isEmpty(window.ventas_listado_monto_tipo)) ? "selected":"" %> value="igual">Total =</option>
                      <option <%= (window.ventas_listado_monto_tipo == "mayor") ? "selected":"" %> value="mayor">Total ></option>
                      <option <%= (window.ventas_listado_monto_tipo == "menor") ? "selected":"" %> value="menor">Total <</option>
                    </select>
                    <span class="input-group-btn w50p">
                      <input type="text" value="<%= window.ventas_listado_monto %>" placeholder="Valor" id="ventas_monto" class="form-control">
                    </span>
                  </div>
                </div>
              </div>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                <div class="form-group">
                  <select class="form-control no-model" id="ventas_con_anulados">
                    <option <%= (window.ventas_listado_con_anulados == 3) ? "selected":"" %> value="3">Ocultar anuladas</option>
                    <option value="1" <%= (window.ventas_listado_con_anulados == 1) ? "selected":"" %>>Con items anulados</option>
                    <option value="2" <%= (window.ventas_listado_con_anulados == 2) ? "selected":"" %>>Solo ventas anuladas</option>
                    <option value="0" <%= (window.ventas_listado_con_anulados == 0) ? "selected":"" %>>Todas las ventas</option>
                  </select>
                </div>
              </div>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                <div class="form-group">
                  <select class="form-control no-model" id="ventas_pago">
                    <option value="-1" <%= (window.ventas_listado_pago == "-1") ? "selected":"" %>>Pago</option>
                    <option value="0" <%= (window.ventas_listado_pago == "0") ? "selected":"" %>>Pendiente</option>
                    <option value="1" <%= (window.ventas_listado_pago == "1") ? "selected":"" %>>Pagada</option>
                  </select>
                </div>
              </div>              

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5 <%= (IDIOMA == "en" || ID_EMPRESA == 1284)?"dn":"" %>">
                <div class="form-group">
                  <select class="form-control no-model" id="ventas_forma_pago">
                    <option value="0" <%= (window.ventas_listado_forma_pago == "0") ? "selected":"" %>>Forma de Pago</option>
                    <option value="E" <%= (window.ventas_listado_forma_pago == "E") ? "selected":"" %>>Efectivo</option>
                    <option value="T" <%= (window.ventas_listado_forma_pago == "T") ? "selected":"" %>>Tarjeta</option>
                    <option value="H" <%= (window.ventas_listado_forma_pago == "H") ? "selected":"" %>>Cheque</option>
                    <option value="C" <%= (window.ventas_listado_forma_pago == "C") ? "selected":"" %>>Cuenta Corriente</option>
                  </select>
                </div>
              </div>

              <% if (typeof window.tarjetas != "undefined" && window.tarjetas.length > 0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select class="form-control no-model" id="ventas_tarjeta">
                      <option value="0">Filtrar por tarjeta</option>
                      <% for(var i=0;i< tarjetas.length;i++) { %>
                        <% var o = tarjetas[i]; %>
                        <option <%= (window.ventas_listado_tarjeta == o.id) ? "selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <input type="text" value="<%= window.ventas_listado_lote %>" placeholder="Lote" id="ventas_lote" class="form-control">
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <input type="text" value="<%= window.ventas_listado_cupon %>" placeholder="Cupon" id="ventas_cupon" class="form-control">
                  </div>
                </div>
              <% } %>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <button class="buscar btn btn-block btn-dark btn-default"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
                </div>
              </div>

            </div>
          </div>

        <% } %>
      </div>
      <% if (IDIOMA != "en" && ID_EMPRESA != 1284) { %>
        <div class="bulk_action wrapper pb0">
          <button class="btn btn-default enviar btn-addon"><i class="icon fa fa-send"></i>Enviar por email</button>

          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-print"></i><span><?php echo lang(array("es"=>"Imprimir","en"=>"Print")); ?></span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void(0)" class="imprimir_lote">Lote</a></li>
              <li><a href="javascript:void(0)" class="imprimir_agrupado">Agrupado</a></li>
            </ul>
          </div>

          <% if (ID_EMPRESA == 121) { %>
            <button class="btn btn-default calcular_iva_lote btn-addon"><i class="icon fa fa-calculator"></i>Calcular IVA</button>
          <% } else { %>
            <button class="btn btn-default sumar_lote btn-addon"><i class="icon fa fa-calculator"></i>Sumar</button>
          <% } %>
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-pencil"></i><span><?php echo lang(array("es"=>"Editar","en"=>"Edit")); ?></span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void(0)" class="editar_vendedor">Vendedor</a></li>
              <li><a href="javascript:void(0)" class="editar_reparto">Reparto</a></li>
            </ul>
          </div>
        </div>
      <% } %>

      <div class="panel-body resumen pb0" style="display:none">
        <div class="row">
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
              <div id="ventas_resumen_total" class="h3 font-thin text-white block">0</div>
              <span class="text-muted text-md pt5 db">Total</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
              <span id="ventas_resumen_cantidad" class="font-thin h3 block">0</span>
              <span class="text-muted text-md pt5 db"><?php echo lang(array("es"=>"Cantidad","en"=>"Count")); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">

          <ul id="filtro_web" style="<%= (window.ventas_listado_id_origen == "1") ? "":"display:none" %>" class="nav nav-tabs nav-tabs-5" role="tablist">
            <li id="cambiar_tab_todos" class="<%= (window.ventas_listado_in_tipos_estados == "") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab" data-tipo="" role="tab" data-toggle="tab"><?php echo lang(array("es"=>"Todos","en"=>"All")); ?></a>
            </li>
            <li id="cambiar_tab_finalizadas" class="<%= (window.ventas_listado_in_tipos_estados == "4-5-6-8-9-10") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab" data-tipo="4-5-6-8-9-10" role="tab" data-toggle="tab"><i class="fa fa-thumbs-up text-success"></i> <?php echo lang(array("es"=>"Finalizadas","en"=>"Completed")); ?></a>
            </li>
            <li id="cambiar_tab_en_proceso" class="<%= (window.ventas_listado_in_tipos_estados == "0-1-2-3") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab" data-tipo="0-1-2-3" role="tab" data-toggle="tab"><i class="fa fa-check text-info"></i> <?php echo lang(array("es"=>"En Proceso","en"=>"In process")); ?></a>
            </li>
            <li id="cambiar_tab_abandonados" class="<%= (window.ventas_listado_in_tipos_estados == "7") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab" data-tipo="7" role="tab" data-toggle="tab"><i class="fa fa-thumbs-down text-danger"></i> <?php echo lang(array("es"=>"Abandonadas","en"=>"Discarded")); ?></a>
            </li>
          </ul>

          <table id="ventas_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <% for(var i=0; i< tabla_ventas.campos.length; i++) { %>
                  <% var c = tabla_ventas.campos[i] %>
                  <% if (c.visible == 1) { %>
                    <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  ><%= (c.campo == "path")?"":c.titulo %></th>
                  <% } %>
                <% } %>
                <th>Profesional</th>
                <th>Codigo de activacion</th>
                <th></th>
                <th class="th_acciones w120">
                  <?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?>
                  <% if (permiso > 2 || (typeof VOLVER_SUPERADMIN != undefined)) { %>
                    <i class="fa configurar_tabla cp fa-cog pull-right mt3"></i>
                  <% } %>
                </th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>              
        </div>
      </div>
    </div>
  </div>
<% } else { %>
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-xs-12 sm-m-b">
          <div class="input-group">
            <input type="text" id="ventas_listado_buscar" placeholder="Buscar..." autocomplete="off" class="form-control buscar">
            <span class="input-group-btn">
              <button class="btn btn-default"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>          
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="ventas_tabla" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;"></th>
              <% if (LOCAL == 1) { %>
                <th style="width:20px;"></th>
              <% } %>
              <% if (ID_PROYECTO == 10) { %>
                <th>Tipo</th>
              <% } %>
              <th>Cliente</th>
              <th>Fecha</th>
              <% if (ID_PROYECTO != 10) { %>
                <th>Numero</th>
              <% } %>
              <% if (control.check("vendedores")>0) { %>
                <th>Vendedor</th>
              <% } %>
              <% if (ID_PROYECTO == 10) { %>
                <th><%= (TOQUE == 1)?"Comercio":"Usuario" %></th>
              <% } %>
              <th class="tar w150">Total</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>              
      </div>
    </div>
  </div>
<% } %>
</script>

<script type="text/template" id="ventas_item_resultados_template">
  <% var clase = (anulada == 1) ? "text-danger" : "" %>
  <% var editar = (!seleccionar)?"edit":"" %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc check-row" value="<%= id %>" data-total="<%= total %>" data-id_punto_venta="<%= id_punto_venta %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" data-total="<%= total %>" data-id_punto_venta="<%= id_punto_venta %>" type="checkbox"><i></i>
      </label>
    </td>    
  <% } %>

  <% if (LOCAL == 1) { %>
    <% if (uploaded == 1) { %>
      <td style="width: 20px">
        <i data-toggle="tooltip" title="Subido correctamente" class="fa fa-check text-success"></i>
      </td>
    <% } %>
  <% } %>

  <% for(var i=0; i< tabla_ventas.campos.length; i++) { %>
    <% var c = tabla_ventas.campos[i] %>
    
    <% if (c.campo == "fecha" && c.visible == 1) { %>
      
      <% if (ID_EMPRESA == 228 && id_tipo_comprobante == 999) { %>
        <td class="<%= clase %> <%= editar %> <%= (c.ocultable == 1)?"hidden-xs":"" %>">
          <span class="w90 pl8 pr8 tac dib editar_fecha">
            <span class="inline-text-cont bbd">
              <%= fecha %>
            </span>
            <input type="text" value="<%= fecha %>" class="inline-text" />
          </span>
          <%= hora %>
        </td>
      <% } else { %>
        <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
          <%= fecha %> <%= hora %>
          <% if (visto > 0) { %>
            <i data-toggle="tooltip" class="fa fa-eye text-muted" title="<%= (visto==1)?"Visto: 1 vez":("Visto: "+visto+" veces") %>"></i>
          <% } else if (enviada == 1) { %>
            <i data-toggle="tooltip" title="Enviada por email" class="fa fa-send-o text-muted"></i>
          <% } %>
        </td>
      <% } %>

    <% } else if (c.campo == "reparto" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= fecha_reparto %> | <%= reparto %>
      </td>

    <% } else if (c.campo == "fecha_reparto" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= fecha_reparto %>
      </td>

    <% } else if (c.campo == "fecha_vencimiento" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= fecha_vto %>
      </td>

    <% } else if (c.campo == "observaciones" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= observaciones %>
      </td>

    <% } else if (c.campo == "direccion" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= direccion %>
      </td>

    <% } else if (c.campo == "items" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% for(var jj=0; jj<items.length;jj++) { %>
          <% var item = items[jj] %>
          <%= Number(item.cantidad).toFixed(2) %> x <br/>
          <b class="negro"><%= item.nombre %></b>
          <%= (!isEmpty(item.descripcion)) ? ("<br/>"+item.descripcion) : "" %>
          <br/><br/>
        <% } %>
      </td>      

    <% } else if (c.campo == "iva" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> tar data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="tag_precio"><%= Number(iva).format() %> €</span>
      </td>

    <% } else if (c.campo == "tipo" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= (tipo=="M")?reference_id:((tipo=="D")?"Delivery":((tipo=="T")?"Mostrador":"")) %></td>

    <% } else if (c.campo == "concepto" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><span class="label bg-light dk"><%= concepto %></span></td>

    <% } else if (c.campo == "cliente" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="text-info"><%= (isEmpty(cliente)) ? "Consumidor Final" : cliente %></span>
        <% if (ID_EMPRESA == 972) { %>
          (Cod: <%= codigo_cliente %>)
        <% } %>
        <%= (anulada == 1) ? "(ANULADA)":"" %>
        <% if (!isEmpty(custom_5)) { %>
          <i data-toggle="tooltip" title="<%= custom_5 %>" class="fa fa-commenting ml10 text-warning"></i>
        <% } %>
        <% if (ID_EMPRESA == 120 && !isEmpty(custom_1)) { %>
          <br/><span class="label bg-success"><%= custom_1.replaceAll("_"," ") %></span>
        <% } %>
        <% if (tipo_impresion == 'E' && isEmpty(cae) && FACTURACION_TESTING == 0 && id_tipo_comprobante < 900) { %>
          <br/><span class="label bg-danger convertir_factura">Error en CAE: Click para Refacturar</span>
        <% } %>        
      </td>

    <% } else if (c.campo == "usuario" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= usuario %></td>

    <% } else if (c.campo == "codigo_cliente" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= codigo_cliente %></td>

    <% } else if (c.campo == "empresa" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= empresa %></td>

    <% } else if (c.campo == "sucursal" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= sucursal %></td>

    <% } else if (c.campo == "impresa" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (impresa==1) { %>
          <i class="fa fa-check text-success"></i>
        <% } %>
      </td>

    <% } else if (c.campo == "pagada" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (pagada==1) { %>
          <span class="label bg-success">Pagada</span>
        <% } else if (pagada == 0) { %>
          <span class="label bg-warning">Pendiente</span>
        <% } %>
      </td>

    <% } else if (c.campo == "numero" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><span class="numero"><%= numero %></span></td>

    <% } else if (c.campo == "punto_venta" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= punto_venta %></td>

    <% } else if (c.campo == "vendedor" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= vendedor %></td>

    <% } else if (c.campo == "comprobante" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% var tipo_comprobante_abreviado = (id_tipo_comprobante == 3 || id_tipo_comprobante == 8 || id_tipo_comprobante == 13 || id_tipo_comprobante == 21 || id_tipo_comprobante == 53 || id_tipo_comprobante == 203 || id_tipo_comprobante == 208) ? "NC " : ((id_tipo_comprobante == 2 || id_tipo_comprobante == 7 || id_tipo_comprobante == 12 || id_tipo_comprobante == 20 || id_tipo_comprobante == 52 || id_tipo_comprobante == 202 || id_tipo_comprobante == 207) ? "ND " : "") %>
        <%= tipo_comprobante_abreviado %><span class="comprobante"><%= comprobante %></span>
        <% if (tarjeta > 0) { %>
          <% if (tipo_pago == "B") { %>
            <i data-toggle="tooltip" title="Por banco" class="fa fa-bank m-l-xs text-warning"></i>
          <% } else { %>
            <i data-toggle="tooltip" title="Tarjeta: <%= tarjeta %> €" class="fa fa-credit-card m-l-xs text-warning cp ver_tarjeta"></i>
          <% } %>
        <% } %>
        <% if (efectivo > 0) { %>
          <% if (tipo_pago == "O") { %>
            <i data-toggle="tooltip" title="Otra forma de pago" class="fa fa-credit-card-alt m-l-xs text-primary"></i>
          <% } else { %>        
            <i data-toggle="tooltip" title="Efectivo: <%= efectivo %>" class="fa fa-money m-l-xs text-success"></i>
          <% } %>
        <% } %>
        <% if (cheque > 0) { %>
          <i data-toggle="tooltip" title="Cheque: <%= cheque %>" class="fa fa-list-alt m-l-xs text-danger"></i>
        <% } %>
        <% if (cta_cte > 0) { %>
          <i data-toggle="tooltip" title="Cuenta: <%= cta_cte %>" class="fa fa-table m-l-xs text-info"></i>
        <% } %>
        <% if (id_origen == 1) { %>
          <i data-toggle="tooltip" title="Venta Web" class="fa fa-globe m-l-xs text-info"></i>
        <% } %>
        <% if (pendiente == 1) { %>
          <span class="label bg-danger">Pendiente</span>
        <% } %>
      </td>

    <% } else if (c.campo == "tipo_comprobante" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= tipo_comprobante %></td>

    <% } else if (c.campo == "telefono" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> <%= (c.ocultable == 1)?"hidden-xs":"" %>"><a href="javascript:void(0)" class="enviar_whatsapp text-info"><%= telefono %></a></td>

    <% } else if (c.campo == "total" && c.visible == 1) { %>
      <td class="<%= clase %> <%= editar %> tar data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="tag_precio"><%= Number(total).format() %> €</span>
      </td>

    <% } else if (c.campo == "cambiar_pago" && c.visible == 1) { %>
      <td class="<%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <div class="btn-group dropdown">
          <% if (tipo_pago == "C" == 1) { %>
            <% var tipo_pago_label = "Cuenta Corriente" %>
          <% } else if (tipo_pago == "E") { %>
            <% var tipo_pago_label = "Efectivo" %>
          <% } else if (tipo_pago == "H") { %>
            <% var tipo_pago_label = "Cheque" %>
          <% } else if (tipo_pago == "T") { %>
            <% var tipo_pago_label = "Tarjeta" %>
          <% } else if (tipo_pago == "B") { %>
            <% var tipo_pago_label = "Banco" %>
          <% } else if (tipo_pago == "O") { %>
            <% var tipo_pago_label = "Otro" %>
          <% } else { %>
            <% var tipo_pago_label = "-" %>
          <% } %>
          <span class="label bg-light dk dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <%= tipo_pago_label %> <span class="fs12 m-l-xs"><i class="fa fa-caret-down"></i></span>
          </span>
          <ul class="dropdown-menu">
            <li><a href="javascript:void(0)" class="editar_tipo_pago" data-valor="C">Cuenta Corriente</a></li>
            <li><a href="javascript:void(0)" class="editar_tipo_pago" data-valor="E">Efectivo</a></li>
            <li><a href="javascript:void(0)" class="editar_tipo_pago" data-valor="H">Cheque</a></li>
            <li><a href="javascript:void(0)" class="editar_tipo_pago" data-valor="T">Tarjeta</a></li>
            <li><a href="javascript:void(0)" class="editar_tipo_pago" data-valor="B">Banco</a></li>
            <li><a href="javascript:void(0)" class="editar_tipo_pago" data-valor="O">Otro</a></li>
          </ul>
        </div>
      </td>

    <% } else if (c.campo == "estado" && c.visible == 1) { %>
      <td class="<%= clase %> editar_pago <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (control.check("ventas_listado") == 3 && id_tipo_estado == -1) { %>
          <span class="label bg-danger m-l">Revisar</span>
        <% } else if (id_tipo_estado == 0) { %>
          <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO)) { %>
            <span class="label bg-light dk"><?php echo lang(array("es"=>"En proceso","en"=>"In Process")); ?></span>
          <% } %>
        <% } else if (id_tipo_estado == 1) { %>
          <span class="label bg-warning"><?php echo lang(array("es"=>"Pendiente","en"=>"In Process")); ?></span>
        <% } else if (id_tipo_estado == 2) { %>
          <span class="label bg-warning"><?php echo lang(array("es"=>"Autorizado","en"=>"Authorized")); ?></span>
        <% } else if (id_tipo_estado == 3) { %>
          <span class="label bg-warning"><?php echo lang(array("es"=>"Pendiente de Pago","en"=>"Pending")); ?></span>
        <% } else if (id_tipo_estado == 4) { %>
          <span class="label bg-success"><?php echo lang(array("es"=>"MercadoPago","en"=>"MercadoPago")); ?></span>
        <% } else if (id_tipo_estado == 5) { %>
          <span class="label bg-success"><?php echo lang(array("es"=>"Facturado","en"=>"Billing")); ?></span>
        <% } else if (id_tipo_estado == 6) { %>
          <span class="label bg-success"><?php echo lang(array("es"=>"Finalizado","en"=>"Complete")); ?></span>
        <% } else if (id_tipo_estado == 7) { %>
          <span class="label bg-danger"><%= (TIPO_EMPRESA == 3) ? "Sin Pedido":"<?php echo lang(array("es"=>"Abandonado","en"=>"Abandoned")); ?>" %></span>
        <% } else if (id_tipo_estado == 8) { %>
          <span class="label bg-success"><?php echo lang(array("es"=>"Pago en Sucursal","en"=>"Payment in Store")); ?></span>
        <% } else if (id_tipo_estado == 9) { %>
          <span class="label bg-success"><?php echo lang(array("es"=>"Pago a convenir","en"=>"Agreed with Buyer")); ?></span>
        <% } else if (id_tipo_estado == 10) { %>
          <span class="label bg-success"><?php echo lang(array("es"=>"Contrarrembolso","en"=>"Payment in Delivery")); ?></span>
        <% } %>
      </td>

      
    <% } else if (c.campo == "custom_6" && c.visible == 1) { %>
      <td class="<%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <div class="btn-group dropdown">
          <% if (custom_6 == 1) { %>
            <% var custom_6_label = "En proceso" %>
            <% var custom_6_class = "bg-warning" %>
          <% } else if (custom_6 == 2) { %>
            <% var custom_6_label = "Listo para enviar" %>
            <% var custom_6_class = "bg-warning" %>
          <% } else if (custom_6 == 3) { %>
            <% var custom_6_label = "En transito" %>
            <% var custom_6_class = "bg-info" %>
          <% } else if (custom_6 == 4) { %>
            <% var custom_6_label = "Entregado" %>
            <% var custom_6_class = "bg-success" %>
          <% } else { %>
            <% var custom_6_label = "Pendiente" %>
            <% var custom_6_class = "bg-light dk" %>
          <% } %>
          <span class="label <%= custom_6_class %> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <%= custom_6_label %> <span class="fs12 m-l-xs"><i class="fa fa-caret-down"></i></span>
          </span>
          <ul class="dropdown-menu">
            <li><a href="javascript:void(0)" class="editar_custom_6" data-valor="">Pendiente</a></li>
            <li><a href="javascript:void(0)" class="editar_custom_6" data-valor="1">En proceso</a></li>
            <li><a href="javascript:void(0)" class="editar_custom_6" data-valor="2">Listo para enviar</a></li>
            <li><a href="javascript:void(0)" class="editar_custom_6" data-valor="3">En transito</a></li>
            <li><a href="javascript:void(0)" class="editar_custom_6" data-valor="4">Entregado</a></li>
          </ul>
        </div>
      </td>
    
    <% } %>

  <% } %>

  <% if (!seleccionar) { %>

    <% if (ID_PROYECTO == 10) { %>

      <td class="p5 td_acciones">
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

    <% } else { %>
      <td><%=nombre_profesional%></td>
      <td><%=codigo_activacion%></td>
      <td></td>
      <td class="p5 td_acciones">
        <% if (pendiente == 0) { %>
          <i data-toggle="tooltip" title="Imprimir" class="fa iconito active fa-print imprimir" />
        <% } %>
        <% if (codigo_reclamado == 0) { %>
          <i style="border: none;" data-toggle="tooltip" title="No reclamado" class="bg-light fa iconito active fa-check imprimir" />
        <% } else { %>
          <i  data-toggle="tooltip" title="<%= moment(fecha_activacion.substring(0, 10), "YYYY-MM-DD").format("DD/MM/YYYY") %>" class="fa iconito active fa-check imprimir" />
        <% } %>
        <% if (id_tipo_estado == 7 && ID_PROYECTO != 1 && ID_EMPRESA != 1284) { %>
          <i data-toggle="tooltip" title="Enviar Email Descuento" class="fa iconito active success fa-tags enviar_email_descuento" />
        <% } %>
        <% if (TIPO_EMPRESA == 3) { %>
          <% if (!isEmpty(custom_7) && !isEmpty(custom_8) && custom_7 != 0 && custom_8 != 0) { %>
            <a href="https://www.google.com/maps/search/?api=1&query=<%= custom_7 %>,<%= custom_8 %>" target="_blank"><i data-toggle="tooltip" title="Posicion de pedido" class="fa iconito active warning fa-map-marker" /></a>
          <% } %>  
        <% } %>

        <% if (!seleccionar) { %>
          <div class="btn-group dropdown ml10 fr">
            <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-plus"></i>
            </button>        
            <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void(0)" class="modificar_codigo" data-id="<%= id %>">Modificar codigo de activacion</a></li>
              <?php /*
              <% if (id_tipo_comprobante == 999 && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                <li><a href="javascript:void(0)" class="convertir_factura" data-id="<%= id %>">Emitir Factura</a></li>
              <% } %>
              */?>

              <% if (ID_EMPRESA == 574) { %>
                <li><a href="javascript:void(0)" class="imprimir_factura" data-id="<%= id %>">Imprimir Factura</a></li>
              <% } %>

              <% if (control.check("ventas_listado") == 3 || ID_EMPRESA == 1021) { %>

                <?php /* GENERALIZAR ESTO DESPUES 
                <% if (ID_EMPRESA == 1021 || VOLVER_SUPERADMIN == 1) { %>
                  <li><a href="javascript:void(0)" class="cambiar_metodo_pago" data-id="<%= id %>">Cambiar Pago</a></li>
                <% } %>
                */?>

                <% if (VOLVER_SUPERADMIN == 1) { %>
                  <% if (!isEmpty(cae)) { %>
                    <li><a target="_blank" href="/sistema/facturas/function/consultar_comprobante/<%= id_tipo_comprobante %>/<%= numero %>/<%= punto_venta %>/">Ver CAE</a></li>
                  <% } else { %>
                    <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
                  <% } %>
                <% } else { %>
                  <% if (ID_EMPRESA == 121) { %>
                    <li><a href="/sistema/facturas/function/comparar_precios_maximos/<%= id %>/<%= id_punto_venta %>/" target="_blank">Comparar precios maximos</a></li>
                  <% } %>
                  <% if (ID_EMPRESA != 135) { %>
                    <% if (((id_tipo_comprobante == 999 && MEGASHOP != 1)) && isEmpty(cae)) { %>
                      <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
                    <% } else if ((tipo_punto_venta == "E" && pendiente == 1) || (tipo_punto_venta == "E" && cae == "")) { %>
                      <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
                    <% } else if (tipo_punto_venta != "E" && anulada == 0 && MEGASHOP != 1) { %>
                      <li><a href="javascript:void(0)" class="anular" data-id="<%= id %>">Anular</a></li>
                    <% } else if (tipo_punto_venta != "E" && anulada == 1) { %>
                      <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
                    <% } %>
                  <% } %>
                <% } %>
                <% if (ID_EMPRESA == 356) { %>
                  <li><a href="javascript:void(0)" class="imprimir_plano">Imprimir Plano</a></li>
                <% } %>
                <% if (ID_EMPRESA == 121) { %>
                  <li><a href="javascript:void(0)" class="imprimir_remito" data-id="<%= id %>">Imprimir remito</a></li>
                <% } %>
                <% if (typeof REPARACION_WEB != "undefined") { %>
                  <li><a href="https://<%= DOMINIO+'web/estado/?id='+id+'&id_punto_venta='+id_punto_venta %>" target="_blank">Link de estado</a></li>
                <% } %>
              <% } %>
            </ul>
          </div>    
        <% } %>
      </td>

    <% } %>
  <% } %>

</script>

<script type="text/template" id="percep_ganancias_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Exportar percepciones de ganancias</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Desde</label>
            <div class="input-group">
              <input type="text" id="percep_ganancias_desde" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Hasta</label>
            <div class="input-group">
              <input type="text" id="percep_ganancias_hasta" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="exportar btn btn-default">Exportar</button>
    </div>
  </div>
</script>

<script type="text/template" id="percep_iibb_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Exportar Percepciones de Ingresos Brutos</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Desde</label>
            <div class="input-group">
              <input type="text" id="percep_iibb_desde" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Hasta</label>
            <div class="input-group">
              <input type="text" id="percep_iibb_hasta" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label>Descargar como</label>
        <select id="percep_iibb_descarga" class="form-control no-model">
          <option selected value="1">Archivo para presentacion</option>
          <option value="2">CSV (Separado por comas)</option>
        </select>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="exportar btn btn-default">Exportar</button>
    </div>
  </div>
</script>

<script type="text/template" id="cupon_tarjeta_panel_template">
  <div class="panel panel-default oh">
    <div class="panel-body">
      <ul class="nav nav-tabs" role="tablist">
        <li id="ver_tarjeta_1" class="<%= (tarjetas.length >= 1)?"active":"" %>">
          <a href="#tarjeta1" role="tab" data-toggle="tab">Tarjeta 1</a>
        </li>
        <% if (tarjetas.length == 2) { %>
          <li id="ver_tarjeta_2">
            <a href="#tarjeta2" role="tab" data-toggle="tab">Tarjeta 2</a>
          </li>
        <% } %>
        <li id="ver_efectivo" class="<%= (tarjetas.length == 0)?"active":"" %>">
          <a href="#efectivo" role="tab" data-toggle="tab">Efectivo</a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="efectivo" class="tab-pane panel-body <%= (tarjetas.length == 0)?"active":"" %>">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-md-4 control-label">Efectivo: </label>
              <div class="col-md-8">
                <input type="text" value="<%= efectivo %>" id="cupon_tarjeta_efectivo" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label">Vuelto: </label>
              <div class="col-md-8">
                <input type="text" value="<%= vuelto %>" id="cupor_tarjeta_vuelto" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
              </div>
            </div>
          </div>
        </div>
        <% if (tarjetas.length >= 1) { %>
          <div id="tarjeta1" class="tab-pane panel-body <%= (tarjetas.length >= 1)?"active":"" %>">
            <% var t1 = tarjetas[0] %>
            <div class="form-horizontal">
              <input type="hidden" class="dn" value="<%= t1.id %>" id="tarjeta1_id">
              <div class="form-group">
                <label class="col-md-4 control-label">Tarjeta: </label>
                <div class="col-md-8">
                  <select class="form-control no-model" id="tarjeta1_tarjeta" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                    <option value="0">Seleccione</option>
                    <% for(var i=0;i< window.tarjetas.length;i++) { %>
                      <% var t = window.tarjetas[i] %>
                      <option <%= (t1.id_tarjeta == t.id)?"selected":"" %> value="<%= t.id %>"><%= t.nombre %></option>
                    <% } %>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Nro. Cuotas: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta1_cuotas" value="<%= t1.cuotas %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Cupon: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta1_cupon" value="<%= t1.cupon %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Lote: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta1_lote" value="<%= t1.lote %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Importe: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta1_importe" value="<%= t1.importe %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Intereses: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta1_interes" value="<%= t1.interes %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Total: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta1_total" value="<%= t1.total %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <% if (modificar_forma_pago == 1) { %>
                <div class="form-group">
                  <button class="btn btn-danger mover_tarjeta1_efectivo">Mover a efectivo</button>
                </div>
              <% } %>
            </div>
          </div>
        <% } %>
        <% if (tarjetas.length == 2) { %>
          <div id="tarjeta2" class="tab-pane panel-body">
            <% var t1 = tarjetas[1] %>
            <div class="form-horizontal">
              <input type="hidden" class="dn" value="<%= t1.id %>" id="tarjeta2_id">
              <div class="form-group">
                <label class="col-md-4 control-label">Tarjeta: </label>
                <div class="col-md-8">
                  <select class="form-control no-model" id="tarjeta2_tarjeta" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                    <option value="0">Seleccione</option>
                    <% for(var i=0;i< window.tarjetas.length;i++) { %>
                      <% var t = window.tarjetas[i] %>
                      <option <%= (t1.id_tarjeta == t.id)?"selected":"" %> value="<%= t.id %>"><%= t.nombre %></option>
                    <% } %>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Nro. Cuotas: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta2_cuotas" value="<%= t1.cuotas %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Cupon: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta2_cupon" value="<%= t1.cupon %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Lote: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta2_lote" value="<%= t1.lote %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Importe: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta2_importe" value="<%= t1.importe %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Intereses: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta2_interes" value="<%= t1.interes %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Total: </label>
                <div class="col-md-8">
                  <input type="text" id="tarjeta2_total" value="<%= t1.total %>" class="form-control no-model" <%= (modificar_forma_pago == 0)?"disabled":"" %>>
                </div>
              </div>
              <% if (modificar_forma_pago == 1) { %>
                <div class="form-group">
                  <button class="btn btn-danger mover_tarjeta1_efectivo">Mover a efectivo</button>
                </div>
              <% } %>
            </div>
          </div>
        <% } %>
      </div>
    </div>
    <% if (modificar_forma_pago == 1) { %>
      <div class="panel-footer tar">
        <button class="btn btn-success guardar">Guardar</button>
      </div>
    <% } %>
  </div>
</script>


<script type="text/template" id="editar_vendedor_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Editar vendedor</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Vendedor",
            "en"=>"Seller",
          )); ?>
        </label>
        <select id="editar_vendedor_vendedores" class="form-control no-model"></select>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="editar_fecha_reparto_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Editar</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label>Reparto</label>
        <div class="input-group">
          <span class="input-group-btn">
            <input type="text" value="1" class="form-control w40 no-model" id="editar_fecha_reparto_numero_reparto"/>
          </span>
          <input type="text" id="editar_fecha_reparto_fecha_reparto" class="form-control action no-model">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="iva_ventas_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Reporte de IVA Ventas</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Desde</label>
            <div class="input-group">
              <input type="text" id="iva_ventas_fecha_desde" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Hasta</label>
            <div class="input-group">
              <input type="text" id="iva_ventas_fecha_hasta" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Nro. Página</label>
            <input type="number" class="form-control" value="1" id="iva_ventas_desde" />
          </div>
        </div>
      </div>
      <% if (window.razones_sociales.length > 0) { %>
        <div class="form-group">
          <label class="control-label">Razon Social</label>
          <select class="form-control" id="iva_ventas_razones_sociales">
            <% for (var i=0;i<window.razones_sociales.length;i++) { %>
              <% var r = window.razones_sociales[i] %>
              <option value="<%= r.id %>"><%= r.nombre %></option>
            <% } %>
          </select>
        </div>
      <% } %>
    </div>
    <div class="panel-footer clearfix tar">
      <% if (ID_EMPRESA == 135) { %>
        <button id="iva_ventas_por_concepto" class="btn btn-default">Ventas por concepto</button>
      <% } else { %>     
        <button id="ventas_por_concepto" class="btn btn-default">Ventas por concepto</button>
      <% } %>
      <div class="btn-group dropdown">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Exportar archivos
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void" class="citi_comprobantes">CITI Comprobantes</a></li>
          <li><a href="javascript:void" class="citi_alicuotas">CITI Alicuotas</a></li>
          <li><a href="javascript:void" class="citi">Ambos</a></li>
        </ul>
      </div>
      <button class="iva_excel btn btn-default">Exportar Excel</button>
      <button class="imprimir btn btn-default">Imprimir</button>
    </div>
  </div>
</script>


<script type="text/template" id="modificar_codigo_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Editar codigo de activacion</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Codigo",
            "en"=>"Seller",
          )); ?>
        </label>
        <input id="modificar_codigo_activacion" class="form-control" type="text">
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>