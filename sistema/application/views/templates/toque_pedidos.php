<script type="text/template" id="toque_pedidos_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-shopping-cart icono_principal"></i>Pedidos</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <% if (ID_EMPRESA != 224 && ID_EMPRESA != 249 && ID_EMPRESA != 421 && ID_EMPRESA != 228) { %>
        <ul class="nav nav-tabs nav-tabs-2" role="tablist">
          <li class="<%= (window.toque_pedidos_listado_in_tipos_estados == "0-1-2-3-4-5-6-8" && window.toque_pedidos_listado_con_anulados == 3) ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="0-1-2-3-4-5-6-8" data-anulados="3" role="tab" data-toggle="tab"><?php echo lang(array("es"=>"Todos","en"=>"All")); ?></a>
          </li>
          <li class="<%= (window.toque_pedidos_listado_in_tipos_estados == "0-1-2-3-4-5") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="0-1-2-3-4-5" data-anulados="3" role="tab" data-toggle="tab"><i class="fa fa-check text-info"></i> <?php echo lang(array("es"=>"En Proceso","en"=>"In process")); ?></a>
          </li>
          <li class="<%= (window.toque_pedidos_listado_in_tipos_estados == "8") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="8" data-anulados="3" role="tab" data-toggle="tab"><i class="fa fa-clock-o text-primary"></i> Programado</a>
          </li>
          <li class="<%= (window.toque_pedidos_listado_in_tipos_estados == "6") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="6" data-anulados="3" role="tab" data-toggle="tab"><i class="fa fa-thumbs-up text-success"></i> <?php echo lang(array("es"=>"Finalizadas","en"=>"Completed")); ?></a>
          </li>
          <li class="<%= (window.toque_pedidos_listado_in_tipos_estados == "7") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="7" data-anulados="3" role="tab" data-toggle="tab"><i class="fa fa-thumbs-down text-danger"></i> <?php echo lang(array("es"=>"Rechazadas","en"=>"Discarded")); ?></a>
          </li>
          <% if (PERFIL == 660 || PERFIL == 862) { %>
            <li class="<%= (window.toque_pedidos_listado_in_tipos_estados == "M1") ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab" data-tipo="M1" data-anulados="3" role="tab" data-toggle="tab"><i class="fa fa-clock-o text-warning"></i> Pendientes</a>
            </li>
          <% } %>
          <% if (PERFIL == 660) { %>
            <li class="<%= (window.toque_pedidos_listado_in_tipos_estados == "" && window.toque_pedidos_listado_con_anulados == 2) ? "active":"" %>">
              <a href="javascript:void(0)" class="cambiar_tab" data-tipo="" data-anulados="2" role="tab" data-toggle="tab"><i class="fa fa-trash text-danger"></i> Eliminados</a>
            </li>
          <% } %>
        </ul>
      <% } %>
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-10 sm-m-b">

            <div class="fl">
              <div class="input-group w200">
                <input type="text" id="toque_pedidos_listado_buscar" value="<%= window.toque_pedidos_listado_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>

            <% if (PERFIL != 661) { %>
              <div class="fl">
                <div class="input-group w150">
                  <span class="input-group-btn">
                    <select id="toque_pedidos_usuarios" class="form-control no-model">
                      <option <%= (window.toque_pedidos_listado_id_usuario == 0)?"selected":"" %> value="0">Comercios</option>
                      <% for(var i=0;i< window.usuarios_array.length;i++) { %>
                        <% var o = window.usuarios_array[i]; %>
                        <% if (o.id_perfiles == 661) { %>
                          <option value="<%= o.id %>" <%= (o.id == window.toque_pedidos_listado_id_usuario)?"selected":"" %>><%= o.nombre %></option>
                        <% } %>
                      <% } %>
                    </select>
                  </span>
                </div>
              </div>

              <div class="fl">
                <div class="input-group w150">
                  <span class="input-group-btn">
                    <select class="form-control no-model w150" id="toque_pedidos_vendedores">
                      <option value="0">Repartidores</option>
                    </select>
                  </span>
                </div>
              </div>
            <% } %>

            <div class="fl">
              <div class="btn-group dropdown ml5">
                <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-cogs"></i><span>Operaciones</span>
                </button>
                <ul class="dropdown-menu pull-right">
                  <li><a href="javascript:void" class="ordenar_retrasos">Ordenar por retraso</a></li>
                  <li><a href="javascript:void" class="ordenar_por_listo">Ordenar por listo</a></li>
                  <% if (PERFIL == 660) { %>
                    <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
                  <% } else if (PERFIL == 661) { %>
                    <li><a href="javascript:void" class="exportar_comercios">Exportar Excel</a></li>
                  <% } %>
                </ul>
              </div>
            </div>

            <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>

          </div> 

          <div class="col-md-2 text-right">
            <% if (control.check("toque_pedidos")>=2) { %>
              <button class="btn btn-info btn-addon ml5 nuevo">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </button>            
            <% } %>
          </div>
        </div>
      </div>
      <% var display_search = "display:none" %>
      <div class="advanced-search-div bg-light dk" style="<%= display_search %>">

        <div class="wrapper clearfix">
          <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
          <div class="row pl10 pr10">
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <input autocomplete="off" type="text" placeholder="Desde" id="toque_pedidos_desde" class="form-control">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <input autocomplete="off" type="text" placeholder="Hasta" id="toque_pedidos_hasta" class="form-control">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <select class="form-control no-model" id="toque_pedidos_puntos_venta">
                  <option value="-1">Origen</option>
                  <% for(var i=0;i< puntos_venta.length;i++) { %>
                    <% var o = puntos_venta[i]; %>
                    <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == o.id_sucursal) { %>
                      <option <%= (window.ventas_listado_punto_venta == o.id) ? "selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  <% } %>
                </select>
              </div>
            </div>            

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">            
                <input type="text" id="toque_pedidos_custom_10" value="<%= window.toque_pedidos_listado_custom_10 %>" placeholder="Nro. MercadoPago" class="form-control no-model">
              </div>
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <select class="form-control no-model" id="toque_pedidos_monto_tipo">
                    <option <%= (window.toque_pedidos_listado_monto_tipo == "igual" || isEmpty(window.toque_pedidos_listado_monto_tipo)) ? "selected":"" %> value="igual">Total =</option>
                    <option <%= (window.toque_pedidos_listado_monto_tipo == "mayor") ? "selected":"" %> value="mayor">Total ></option>
                    <option <%= (window.toque_pedidos_listado_monto_tipo == "menor") ? "selected":"" %> value="menor">Total <</option>
                  </select>
                  <span class="input-group-btn w50p">
                    <input type="text" value="<%= window.toque_pedidos_listado_monto %>" placeholder="Valor" id="toque_pedidos_monto" class="form-control">
                  </span>
                </div>
              </div>
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <select class="form-control no-model" id="toque_pedidos_forma_pago">
                  <option value="0" <%= (window.toque_pedidos_listado_forma_pago == "0") ? "selected":"" %>>Forma de Pago</option>
                  <option value="E" <%= (window.toque_pedidos_listado_forma_pago == "E") ? "selected":"" %>>Efectivo</option>
                  <option value="T" <%= (window.toque_pedidos_listado_forma_pago == "T") ? "selected":"" %>>Tarjeta</option>
                  <option value="H" <%= (window.toque_pedidos_listado_forma_pago == "H") ? "selected":"" %>>Cheque</option>
                  <option value="C" <%= (window.toque_pedidos_listado_forma_pago == "C") ? "selected":"" %>>Cuenta Corriente</option>
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

      </div>
      <div class="bulk_action wrapper pb0">
        <button class="btn btn-default sumar_lote btn-addon"><i class="icon fa fa-calculator"></i>Sumar</button>
        <% if (PERFIL != 661) { %>
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-pencil"></i><span><?php echo lang(array("es"=>"Editar","en"=>"Edit")); ?></span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void(0)" class="editar_repartidor">Repartidor</a></li>
              <li><a href="javascript:void(0)" class="editar_estado">Estado</a></li>
            </ul>
          </div>
        <% } %>
      </div>

      <div class="panel-body resumen pb0" style="display:none">
        <div class="row">
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
              <div id="toque_pedidos_resumen_total" class="h3 font-thin text-white block">0</div>
              <span class="text-muted text-md pt5 db">Total</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
              <span id="toque_pedidos_resumen_cantidad" class="font-thin h3 block">0</span>
              <span class="text-muted text-md pt5 db">Cantidad</span>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="toque_pedidos_tabla" class="table table-small table-striped sortable m-b-none default footable">
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
                    <% if (PERFIL == 661 && (c.campo == "cliente" || c.campo == "usuario")) { %>
                    <% } else { %>
                      <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  ><%= (c.campo == "path")?"":c.titulo %></th>
                    <% } %>
                  <% } %>
                <% } %>
                <th class="th_acciones w250">
                  Acciones
                  <% if (VOLVER_SUPERADMIN == 1) { %>
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
</script>

<script type="text/template" id="toque_pedidos_item_resultados_template">
  <% var clase = "vat "+((anulada == 1) ? "text-danger" : ((!seleccionar)?"edit":"")) %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc check-row" value="<%= id %>" data-numero_envio="<%= numero_envio %>" data-total="<%= total %>" data-efectivo="<%= (efectivo-vuelto) %>" data-id_punto_venta="<%= id_punto_venta %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" data-numero_envio="<%= numero_envio %>" data-total="<%= total %>" data-efectivo="<%= (efectivo-vuelto) %>" data-id_punto_venta="<%= id_punto_venta %>" type="checkbox"><i></i>
      </label>
    </td>    
  <% } %>

  <% if (LOCAL == 1) { %>
    <td style="width: 20px">
      <% if (uploaded == 1) { %>
        <i data-toggle="tooltip" title="Subido correctamente" class="fa fa-check text-success"></i>
      <% } %>
    </td>
  <% } %>

  <% for(var i=0; i< tabla_ventas.campos.length; i++) { %>
    <% var c = tabla_ventas.campos[i] %>
    
    <% if (c.campo == "fecha" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="numero bold negro">#<%= numero %></span><br/>
        <%= fecha %> <%= hora %>
        <br/>
        <b><%= (punto_venta == 1)?"Web":"Panel" %></b>
      </td>

    <% } else if (c.campo == "reparto" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% for(var jj=0; jj<items.length;jj++) { %>
          <% var item = items[jj] %>
          <%= Number(item.cantidad).toFixed(2) %> x <br/>
          <b class="negro"><%= item.nombre %></b>
          <%= (!isEmpty(item.descripcion)) ? ("<br/>"+item.descripcion) : "" %>
          <br/><br/>
        <% } %>
      </td>

    <% } else if (c.campo == "tipo" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= (tipo=="M")?reference_id:((tipo=="D")?"Delivery":((tipo=="T")?"Mostrador":"")) %></td>

    <% } else if (c.campo == "cliente" && c.visible == 1 && PERFIL != 661) { %>
      <td class="<%= clase %> <%= (c.ocultable == 1)?"hidden-xs":"" %>">

        <% if (id_punto_venta == 2444) { %>
          <span class="text-info data">ANTICIPO EFECTIVO <%= vendedor %></span>
        <% } else { %>
          <span class="text-info data"><%= (isEmpty(cliente)) ? "Consumidor Final" : cliente %></span>
        <% } %>

        <% if (!isEmpty(custom_5)) { %>
          <i data-toggle="tooltip" title="<%= custom_5 %>" class="fa fa-commenting ml10 text-warning"></i>
        <% } %>
        <% if (!isEmpty(telefono)) { %>
          <br/>
          <span><%= telefono %></span>
          <a class="enviar_whatsapp" href="javascript:void(0)"><i class="fa fa-whatsapp iconito active success"></i></a>
        <% } %>
        <% if (!isEmpty(direccion) && PERFIL != 661) { %>
          <br/><br/>
          <b><%= direccion %></b>
        <% } %>
      </td>

    <% } else if (c.campo == "usuario" && c.visible == 1 && PERFIL != 661) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= usuario %></td>

    <% } else if (c.campo == "sucursal" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= sucursal %></td>

    <% } else if (c.campo == "custom_4" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (!isEmpty(observaciones)) { %>
          <b>Obs. Repartidor: </b><br/>
          <%= observaciones %><br/>
        <% } %>
        <% if (!isEmpty(custom_4)) { %>
          <b>Obs. Comercio: </b><br/>
          <%= custom_4 %><br/>
        <% } %>
        <% if (!isEmpty(custom_6)) { %>
          <b>Rechazo Comercio: </b><br/>
          <%= custom_6 %><br/>
        <% } %>
      </td>

    <% } else if (c.campo == "impresa" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (impresa==1) { %>
          <i class="fa fa-check text-success"></i>
        <% } %>
      </td>

    <% } else if (c.campo == "numero" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><span class="numero"><%= numero %></span></td>

    <% } else if (c.campo == "punto_venta" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= punto_venta %></td>

    <% } else if (c.campo == "vendedor" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= vendedor %>
        <br/><%= vendedor_telefono %>
      </td>

    <% } else if (c.campo == "comprobante" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% var tipo_comprobante_abreviado = (id_tipo_comprobante == 3 || id_tipo_comprobante == 8 || id_tipo_comprobante == 13) ? "NC " : ((id_tipo_comprobante == 2 || id_tipo_comprobante == 7 || id_tipo_comprobante == 12) ? "ND " : "") %>
        <%= tipo_comprobante_abreviado %><span class="comprobante"><%= comprobante %></span>
      </td>

    <% } else if (c.campo == "tipo_comprobante" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= tipo_comprobante %></td>

    <% } else if (c.campo == "total" && c.visible == 1) { %>
      <td class="<%= clase %> tar data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (tarjeta > 0) { %>
          <i data-toggle="tooltip" title="MercadoPago: <%= custom_10 %>" class="fa fa-credit-card m-l-xs text-warning cp ver_tarjeta"></i>
        <% } %>
        <% if (efectivo > 0) { %>
          <i data-toggle="tooltip" title="Paga con $<%= efectivo %>" class="fa fa-money m-l-xs text-success"></i>
        <% } %>
        <% if (cheque > 0) { %>
          <i data-toggle="tooltip" title="Pago con cheque" class="fa fa-list-alt m-l-xs text-danger"></i>
        <% } %>
        <% if (cta_cte > 0) { %>
          <i data-toggle="tooltip" title="Billetera Toque: $<%= cta_cte %>" class="fa fa-table m-l-xs text-info"></i>
        <% } %>

        <% if (tarjeta == 0 && efectivo == 0 && cheque == 0 && cta_cte == 0) { %>
          <i data-toggle="tooltip" title="Pago Efectivo" class="fa fa-money m-l-xs text-success"></i>
        <% } %>

        <span class="tag_precio">$ <%= Number(total).format() %></span>
      </td>

    <% } else if (c.campo == "estado" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (id_tipo_estado == 0) { %>
          <span class="label bg-light dk">En proceso</span>
        <% } else if (id_tipo_estado == 1) { %>
          <span class="label bg-warning">Aceptado Comercio</span>
        <% } else if (id_tipo_estado == 2) { %>
          <span class="label bg-warning">Aceptado por Toquer</span>
        <% } else if (id_tipo_estado == 3) { %>
          <span class="label bg-primary">Repartidor en Comercio</span>
        <% } else if (id_tipo_estado == 4) { %>
          <span class="label bg-success">En camino</span>
        <% } else if (id_tipo_estado == 5) { %>
          <span class="label bg-danger">Esperando Confirmacion</span>
        <% } else if (id_tipo_estado == 6) { %>
          <span class="label bg-success">Finalizado</span>
        <% } else if (id_tipo_estado == 7) { %>
          <span class="label bg-danger">Rechazado</span>
        <% } %>
        <% if (numero_envio == "pickup") { %>
          <br/><span class="label bg-rosa">Pickup</span>
        <% } else if (numero_envio == "programado") { %>
          <br/><span class="label bg-naranja">Programado</span>
        <% } %>
        <% if (!isEmpty(codigo_postal)) { %>
          <br/><span class="label bg-primary">Pedido Listo</span>
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

      <td class="p5 td_acciones vat">

        <% if (id_tipo_estado == 0) { %>

          <% if (id_punto_venta == 2444) { %>
            <a class="aceptar_pedido_deposito btn btn-small btn-success">Aceptar</a>
            <a class="rechazar_pedido_deposito btn btn-small btn-danger">Rechazar</a>
          <% } else { %>
            <a class="aceptar_pedido_comercio btn btn-small btn-success">Aceptar</a>
            <a class="rechazar_pedido_comercio btn btn-small btn-danger">Rechazar</a>
          <% } %>
        
        <?php // PEDIDO CON PICKUP ?>
        <% } else if (id_tipo_estado >= 1 && id_tipo_estado <= 5 && numero_envio == "pickup") { %>
          <% if (isEmpty(codigo_postal)) { %>
            <div><a class="db pedido_listo btn btn-small btn-block btn-primary m-b-xs">Pedido Listo!</a></div>
          <% } %>
          <div><a class="db pickup_listo btn btn-small btn-block btn-success">Entregar Pedido</a></div>

        <% } else if (id_tipo_estado >= 1 && id_tipo_estado <= 2) { %>
          <% if (ID_EMPRESA != 1234 && isEmpty(codigo_postal)) { %>
            <div><a class="db pedido_listo btn btn-small btn-block btn-primary">Pedido Listo!</a></div>
          <% } %>

          <% if (ID_EMPRESA == 1234) { %>
            <span>Entrega:</span><br/>
            <span class="fs18 bold"><%= moment(custom_2).format("DD/MM HH:mm") %></span>

            <div><a class="db pedido_listo btn btn-small btn-block btn-primary">Entregado!</a></div>
            
          <% } else { %>          
            <span data-toggle="tooltip" title="Estimado Comercio: <%= moment(custom_2).format("DD/MM/YYYY HH:mm") %>" class="texttiempo fs18 bold"></span>
          <% } %>

        <% } else if (id_tipo_estado > 2 && id_tipo_estado <= 6) { %>
          <% if (ID_EMPRESA == 1234) { %>
            <span class="fs18 bold"><%= moment(custom_2).format("DD/MM/YYYY HH:mm") %></span>

          <% } else { %>
            <span data-toggle="tooltip" title="Estimado Comercio: <%= moment(custom_2).format("DD/MM/YYYY HH:mm") %> <% if (!isEmpty(codigo_postal)) { %> | Preparado Comercio: <%= moment(codigo_postal).format("DD/MM/YYYY HH:mm") %><% } %> <% if (!isEmpty(retirado)) { %> | Entregado Comercio: <%= moment(retirado).format("DD/MM/YYYY HH:mm") %><% } %>" class="texttiempo fs18 bold"></span>
          <% } %>
        <% } %>

        <% if (id_tipo_estado >= 1 && id_tipo_estado < 6 && ID_EMPRESA != 1234) { %>
          <br/><span data-toggle="tooltip" title="Entrega Estimada: <%= moment(vencimiento).format("DD/MM/YYYY HH:mm") %>" class="texttiempo_vencimiento azul fs18 bold"></span>
        <% } else if (id_tipo_estado == 6) { %>
          <br/><span data-toggle="tooltip" title="Entrega Estimada: <%= moment(vencimiento).format("DD/MM/YYYY HH:mm") %> | Entrega: <%= moment(entregado).format("DD/MM/YYYY HH:mm") %>" class="texttiempo_vencimiento azul fs18 bold"></span>
        <% } %>

        <% if (PERFIL == 660 || PERFIL == 862) { %>
          <div class="btn-group dropdown ml10">
            <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-plus"></i>
            </button>        
            <ul class="dropdown-menu pull-right">
              <li><a target="_blank" href="https://www.varcreative.com/sistema/toque/function/simular_asignar_pedido/<%= id %>">Simular Asignacion</a></li>
              <li><a href="javascript:void(0)" class="editar_observaciones">Editar Observaciones</a></li>
              <li><a href="https://www.varcreative.com/sistema/logs/<%= ID_EMPRESA %>/<%= id %>.txt" target="_blank">Ver log</a></li>
              <li><a href="https://www.toque.com.ar/web/finalizar/?id=<%= id %>" target="_blank">Ver Web</a></li>
              <% if (PERFIL == 660) { %>
                <% if (anulada == 0) { %>
                  <li><a href="javascript:void(0)" class="anular" data-id="<%= id %>">Eliminar</a></li>
                <% } else { %>
                  <li><a href="javascript:void(0)" class="restaurar" data-id="<%= id %>">Restaurar</a></li>
                <% } %>
              <% } %>
            </ul>
          </div>
        <% } %>

      </td>

  <% } %>

</script>


<script type="text/template" id="rechazo_pedido_toque_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Rechazo de pedido</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <div class="form-group">
          <label class="control-label">Motivo</label>
          <select class="form-control no-model" id="rechazo_pedido_toque_motivos">
            <option>Sin Stock</option>
            <option>Fuera de Horario de Atencion</option>
            <option>Otro</option>
          </select>
        </div>          
        <div class="form-group">
          <label class="control-label">Observaciones</label>
          <textarea class="form-control no-model" id="rechazo_pedido_toque_observaciones"></textarea>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>


<script type="text/template" id="editar_repartidor_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Editar Repartidor</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">Repartidor</label>
        <select id="editar_repartidores" class="form-control no-model"></select>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="editar_estado_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Editar Estado</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">Estado</label>
        <select id="editar_estados" class="form-control no-model">
          <option value="0">Pendiente</option>
          <option value="1">Aceptado por el comercio</option>
          <option value="2">Aceptado por el repartidor</option>
          <option value="3">Repartidor en comercio</option>
          <option value="4">En camino</option>
          <option value="6">Finalizado</option>
          <option value="7">Rechazado</option>
        </select>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="editar_observaciones_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Editar Observaciones</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <div class="form-group">
          <label class="control-label">Para el Comercio:</label>
          <textarea class="form-control" id="editar_observaciones_comercio"><%= custom_4 %></textarea>
        </div>
        <div class="form-group">
          <label class="control-label">Para el Repartidor:</label>
          <textarea class="form-control" id="editar_observaciones_repartidor"><%= observaciones %></textarea>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>


<script type="text/template" id="toque_pedido_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span id="toque_pedido_titulo" class="font-bold fl fs20 m-t-xs">Pedido</span>
    <button class="fr cp cerrar btn btn-default">
      <i class="fa fa-times text-muted"></i>
    </button>
  </div>
  <div class="panel-body">
    <div class="">
      <% if (PERFIL == 660 || PERFIL == 862) { %>
        <div class="form-group">
          <select class="form-control no-model" id="toque_pedido_comercios">
            <% for(var i=0;i< usuarios.models.length;i++) { %>
              <% var m = usuarios.models[i] %>
              <% if (m.get("id_perfiles") == 661) { %>
                <option value="<%= m.id %>"><%= m.get("nombre") %></option>
              <% } %>
            <% } %>
          </select>
        </div>
      <% } %>
      <div class="col-md-4 p3">
        <div class="form-group">
          <input type="text" placeholder="Cliente" value="<%= cliente %>" id="toque_pedido_cliente" class="form-control" <%= (id_tipo_estado == 6)?"disabled":"" %>>
        </div>
      </div>
      <div class="col-md-4 p3">
        <div class="form-group text-muted">
          <input type="text" placeholder="Tel&eacute;fono" name="telefono" value="<%= telefono %>" name="telefono" id="toque_pedido_telefono" class="form-control" <%= (id_tipo_estado == 6)?"disabled":"" %>>
        </div>
      </div>      
      <div class="col-md-4 p3">
        <div class="form-group">
          <input type="text" placeholder="DNI" value="<%= documento %>" id="toque_pedido_documento" class="form-control" <%= (id_tipo_estado == 6)?"disabled":"" %>>
        </div>
      </div>
    </div>
    <div class="form-group text-muted">
      <input type="text" placeholder="Direcci&oacute;n" name="direccion" value="<%= direccion %>" name="direccion" id="toque_pedido_direccion" class="form-control" <%= (id_tipo_estado == 6)?"disabled":"" %>>
      <input type="hidden" id="toque_pedido_latitud" value="0" />
      <input type="hidden" id="toque_pedido_longitud" value="0" />
    </div>
    <div class="">
      <div class="col-md-6 p3">
        <div class="form-group text-muted">
          <input type="text" placeholder="Email" name="email" value="<%= email %>" name="email" id="toque_pedido_email" class="form-control" <%= (id_tipo_estado == 6)?"disabled":"" %>>
        </div>
      </div>
      <div class="col-md-6 p3">
        <div class="form-group text-muted">
          <input type="text" placeholder="Obs. Repartidor" value="<%= observaciones %>" class="form-control action no-model" id="toque_pedido_observaciones" <%= (id_tipo_estado == 6)?"disabled":"" %>/>
        </div>
      </div>
    </div>
    <div>
      <input type="hidden" id="toque_pedido_item_id_articulo">
      <input type="hidden" id="toque_pedido_item_tipo">
      <div class="clearfix">
        <div class="col-sm-8 p3">
          <div class="form-group">
            <div class="input-group no-br">
              <input type="text" placeholder="Producto" class="form-control action no-model" id="toque_pedido_item_articulo"/>
              <span class="input-group-btn">
                <button id="toque_pedido_buscar_articulo" tabindex="-1" class="btn btn-default"><i class="fa fa-search"></i>&nbsp;&nbsp;Buscar&nbsp;</button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-sm-4 p3">
          <div class="form-group">
            <div class="input-group no-br no-br-both">
              <span class="input-group-addon addon_minus"><i class="fa fa-minus"></i></span>
              <input placeholder="Cantidad" min="0" type="text" id="toque_pedido_item_cantidad" class="form-control tar"/>
              <span class="input-group-addon addon_plus"><i class="fa fa-plus"></i></span>
            </div>
          </div>
        </div>
        <input type="hidden" disabled class="form-control no-model" id="toque_pedido_item_no_totalizar_reparto"/>
        <input type="hidden" disabled class="form-control no-model" id="toque_pedido_item_subtotal" placeholder="Subtotal"/>
      </div>
      <div class="clearfix">
        <div class="col-sm-8 p3">
          <input type="text" placeholder="Obs. Producto" class="form-control no-model" id="toque_pedido_item_descripcion"/>
        </div>
        <div class="col-sm-4 p3">
          <div class="input-group no-br">
            <input placeholder="Precio" type="text" class="form-control action no-model" <%= (control.check("toque_pedidos") == 2) ? "disabled" : "" %>  value="0.00" id="toque_pedido_item_precio"/>    
            <span class="input-group-btn">
              <button title="Ingresar linea" id="toque_pedido_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="b-a" style="overflow: auto; margin-top: 15px; height: 180px">
      <table id="tabla_items" class="table sortable m-b-none default footable">
        <thead class="bg-light">
          <tr>
            <th class="w75">Cant.</th>
            <th>Producto</th>
            <th class="w75">Unit.</th>
            <th class="w75">Subtotal</th>
            <th class="w25"></th>
            <th class="w25"></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div class="m-t tar clearfix">
      <div class="row">
        <div class="col-xs-6">
          <div class="mb10 cb tal">
            <label class="control-label fs18 font-bold">Costo Envio: </label>
            <span id="toque_pedido_costo_envio" style="margin-left: 20px; background-color: transparent; border: none; color: black; font-size: 24px; font-weight: bold; text-align: left; "></span>
          </div>
          <div class="cb tal">
            <label class="control-label fs18 font-bold">Demora: </label>
            <span id="toque_pedido_demora" style="margin-left: 20px; background-color: transparent; border: none; color: black; font-size: 24px; font-weight: bold; text-align: left; "></span>
          </div>
        </div>
        <div class="col-xs-6">
          <label class="control-label tar fs24 font-bold">Total: </label>
          <span id="toque_pedido_total" style="margin-left: 20px; background-color: transparent; border: none; color: black; font-size: 24px; font-weight: bold; text-align: left; ">
            $ <%= Number(total).toFixed(2) %>
          </span>
          <div class="row">
            <div class="col-xs-6 form-group">
              <label class="control-label">Paga con:</label>
            </div>
            <div class="col-xs-6 form-group">
              <input type="number" class="form-control" id="toque_pedido_paga_con" />
            </div>
          </div>
          <div class="row">
            <div class="col-xs-6">
              <label class="control-label">Vuelto:</label>
            </div>
            <div class="col-xs-6">
              <input type="text" disabled class="form-control" id="toque_pedido_vuelto" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer tar clearfix">
    <% if (control.check("toque_pedidos") >= 2) { %>
      <button class="btn m-l-xs cerrar_mesa_efectivo btn-success">Guardar</button>
    <% } %>
  </div>
</div>
</script>

<script type="text/template" id="toque_pedido_item_template">
  <td class="editar"><%= Number(cantidad).toFixed(2) %></td>
  <td class="editar">
    <span class="text-info"><%= nombre %></span>
    <% if (!isEmpty(descripcion)) { %>
      <br/><%= descripcion %>
    <% } %>
  </td>
  <td class="editar"><%= Number(precio).toFixed(2) %></td>
  <td class="editar"><%= Number(total_con_iva).toFixed(2) %></td>
  <% if (control.check("toque_pedidos") > 2) { %>
    <% if (id_tipo_estado <= 3) { %>
      <td class="w25 p3">
        <button class="btn btn-default eliminar">
          <i title="Eliminar" class="glyphicon glyphicon-remove text-danger"></i>
        </button>
      </td>
      <td class="w25 p3">
        <button class="btn btn-default editar">
          <i title="Editar" class="fa fa-pencil"></i>
        </button>
      </td>
    <% } %>
  <% } %>
</script>


<?php /*
// ===========================================================
// CUENTA CORRIENTE DE LA BILLETERA
*/?>

<script type="text/template" id="toque_billetera_movimientos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <div class="row clearfix padder">
      <% var modulo = control.get("clientes") %>
      <h1 class="m-n font-thin h3 pull-left"><a style="color:inherit" href="app/#clientes"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.nombre_es %></a>
        <% if (!isEmpty(titulo)) { %> / <%= titulo %> <% } %>
        / <b>Cuenta Corriente</b>
      </h1>
    </div>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-7 sm-m-b">
            <div class="form-inline">    
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Desde" id="toque_billetera_movimientos_desde" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Hasta" id="toque_billetera_movimientos_hasta" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="form-group">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-filter mr5"></i> Filtros</button>
              </div>
            </div>
          </div>
          <div class="col-md-5 sm-m-b">

            <div class="btn-group pull-right dropdown ml5">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="fa fa-cog"></i><span>Opciones</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
              </ul>
            </div>

            <div class="btn-group pull-right ml5">
              <button class="btn btn-danger nuevo_gasto">Egreso</span>
              </button>
            </div>
            <div class="btn-group pull-right ml5">
              <button class="btn btn-success nuevo_ingreso">Ingreso</span>
              </button>
            </div>

          </div>
        </div>
      </div>
      <% var display_search = (id_concepto != 0) ? "display:block":"display:none" %>
      <div class="advanced-search-div bg-light dk" style="<%= display_search %>">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">    
            <div class="form-group">
              <select class="form-control no-model" id="toque_billetera_movimientos_conceptos">
                <option value="0">Concepto</option>
                <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="bulk_action panel-body resumen pb0">
        <div class="row">
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
              <div id="toque_billetera_movimientos_monto" class="h3 font-thin text-white block">0</div>
              <span class="text-muted text-md pt5 db">Total</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
              <span id="toque_billetera_movimientos_cantidad" class="font-thin h3 block">0</span>
              <span class="text-muted text-md pt5 db">Operaciones</span>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="toque_billetera_movimientos_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="w180 exportable">Fecha</th>
                <th class="exportable">Concepto</th>
                <th class="exportable">Descripci&oacute;n</th>
                <th class="exportable tar w150">Monto</th>
                <th class="exportable tar w150">Saldo</th>
                <th style="width:20px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="toque_billetera_movimiento_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <%= (id == undefined)?"Cargar":"Editar" %> <%= (tipo==0)?"Ingreso":"Egreso" %>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha</label>
          <div class="input-group">
            <input type="text" value="<%= fecha %>" name="fecha" class="form-control esc" id="toque_billetera_movimientos_fecha"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Monto</label>
          <input type="text" value="<%= monto %>" name="monto" class="form-control esc" id="toque_billetera_movimientos_monto"/>
        </div>
      </div>      
    </div>
    <div class="form-group">
      <label class="control-label">Concepto</label>
      <div class="input-group">
        <select class="form-control no-model esc" id="toque_billetera_movimientos_tipo">
          <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
        </select>
        <span class="input-group-btn">
          <button tabindex="-1" class="btn btn-info w100 agregar_concepto">
            <?php echo lang(array(
              "es"=>"+ Agregar",
              "en"=>"+ Add",
            )); ?>
          </button>  
        </span>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label">Descripci&oacute;n</label>
      <textarea name="observaciones" class="h80 form-control"><%= observaciones %></textarea>
    </div>

    <?php
    single_file_upload(array(
      "name"=>"path",
      "label"=>lang(array("es"=>"Archivo adjunto","en"=>"Atacchment file")),
      "url"=>"/sistema/cajas_movimientos/function/save_file/",
    )); ?>

  </div>
  <div class="panel-footer clearfix">
    <button class="btn btn-default fl cerrar">Cerrar</button>
    <button class="btn btn-success fr guardar">Guardar</button>
  </div>
</div>
</script>


<script type="text/template" id="toque_billetera_movimientos_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row2" value="<%= id %>" data-total="<%= (tipo==1)?"-":"" %><%= monto %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class='ver exportable'><%= fecha %></td>
  <td class='ver exportable'><span class="text-info"><%= concepto %></span></td>
  <td class='exportable'><span class="ver"><%= observaciones %></span>
    <% if (!isEmpty(path)) { %>
      <a class="fr text-info fs16" href="/sistema/<%= path %>" target="_blank"><i class="fa fa-file-o"></i></a>
    <% } %>
  </td>
  <td class="ver exportable tar number">$ <%= (tipo==1)?"-":"" %><%= Number(monto).format(2) %></td>
  <td class="ver exportable tar number">$ <%= Number(subtotal).format(2) %></td>
  <td class="p5 td_acciones">
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>        
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>    
  </td>
</script>