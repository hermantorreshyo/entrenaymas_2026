<script type="text/template" id="cuentas_corrientes_clientes_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
    <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-user icono_principal"></i>Clientes</h1>
  </div>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <input type="hidden" id="cuentas_corrientes_clientes_datos_id_sucursal" value="0" />

    <?php 
    if ($empresa->id_proyecto == 3) { 
      $active = "cuentas_corrientes_clientes"; include("inm/alquileres_menu.php"); 
    } else {
      $active = "cuentas_corrientes_clientes"; include("cli/clientes_menu.php"); 
    } ?>
  
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-8 sm-m-b">
          <div style="display: inline-block">
          <div class="input-group" style="width: 250px;">
            <input type="text" class="form-control" id="cuentas_corrientes_clientes_codigo" autocomplete="off" placeholder="Nombre o codigo de cliente" value="<%= id_cliente %>"/>
            <span class="input-group-btn">
              <button title="Atajo: F2 = Buscar" id="cuentas_corrientes_clientes_buscar_cliente" class="btn btn-default ml0" type="button"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <div style="display: inline-block" <%= (ID_EMPRESA == 70)?'hidden-xs':'' %>>
          <div class="input-group" style="width: 150px;">
            <input type="text" class="form-control" id="cuentas_corrientes_clientes_desde" autocomplete="off" placeholder="Desde">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
        <div style="display: inline-block" <%= (ID_EMPRESA == 70)?'hidden-xs':'' %>>
          <div class="input-group" style="width: 150px;">
            <input type="text" class="form-control" id="cuentas_corrientes_clientes_hasta" autocomplete="off" placeholder="Hasta">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>

        <% if (ID_EMPRESA == 224 || ID_EMPRESA == 1325) { %>
          <div style="display: inline-block">
            <div class="input-group" style="width: 100px;">
              <select class="form-control no-model" id="cuentas_corrientes_clientes_moneda">
                <option value="ARS">Pesos</option>
                <option value="USD">Dolares</option>
              </select>
            </div>
          </div>
        <% } %>

        <div style="display: inline-block">
          <div class="input-group">
            <button id="cuentas_corrientes_clientes_buscar" class="buscar btn btn-default"><i class="fa fa-search"></i></button>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 text-right">        
        <div class="btn-group dropdown">
          <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
            <i class="fa fa-cog"></i><span>Opciones</span>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
            <li><a href="javascript:void(0)" class="imprimir_resumen">Imprimir Resumen</a></li>
            <li><a href="javascript:void(0)" class="imprimir_resumen_detalle">Imprimir Resumen Detalle</a></li>
            <li><a href="javascript:void(0)" class="reasignar_pagos">Asignar Recibos</a></li>
            <li class="divider"></li>
            <li><a onclick="workspace.cambiar_estado()" href="javascript:void(0)">Modo supervisor</a></li>
          </ul>
        </div>
        <% if (control.check("cuentas_corrientes_clientes")>1) { %>
          <a class="btn btn-info btn-addon ml5 agregar_recibo" href="javascript:void(0)">
            <i class="fa fa-plus"></i>
            <span class="hidden-xs">Agregar Pago</span>
          </a>
        <% } %>
      </div>
      </div>
      </div>
      <div class="panel-body">
    <p class="h3" id="cuentas_corrientes_clientes_datos_nombre">Cuenta de Cliente</p>
    <div style="color:#58666e" class="m-t m-b">
    <i class="fa fa-home text-muted m-r-xs"></i><b>Direccion:</b> <span id="cuentas_corrientes_clientes_datos_direccion" class="m-r-lg"></span>
    <i class="fa fa-phone text-muted m-r-xs"></i><b>Telefono:</b> <span id="cuentas_corrientes_clientes_datos_telefono" class="m-r-lg"></span>
    <i class="fa fa-envelope text-muted m-r-xs"></i><b>Email:</b> <a id="cuentas_corrientes_clientes_datos_email" class="m-r-lg text-primary dker"></a>
    <b>IVA:</b> <span id="cuentas_corrientes_clientes_datos_iva" class="m-r-lg"></span>
    <b>CUIT:</b> <span id="cuentas_corrientes_clientes_datos_cuit" class="m-r-lg"></span>
    <div style="font-style: italic; <%= (ID_EMPRESA == 70 ? "display:none":"") %>" id="cuentas_corrientes_clientes_datos_observaciones"></div>
    </div>
    
          <div class="b-a table-responsive">
              <table class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
          <th style="width:20px;">
            <label class="i-checks m-b-none">
              <input class="esc sel_todos" type="checkbox"><i></i>
            </label>
          </th>                      
                      <th>Fecha</th>
                      <th>Comprobante</th>
                      <th>Numero</th>
                      <th class="tar">Debe</th>
                      <th class="tar">Haber</th>
                      <th class="tar">Saldo</th>
                      <th>Pago</th>
                      <th class="w25"></th>
                      <th class="w25"></th>
                      <th class="w25"></th>
                  </thead>
                  <tbody id="cuentas_corrientes_clientes_tbody" class="tbody">
                      <tr><td colspan="20">Seleccione un cliente</td></tr>
                  </tbody>
              </table>
          </div>
      </div>
  </div>
</div>
</script>


<script type="text/template" id="cuentas_corrientes_clientes_item_resultados_template">
  <% var clase = ((anulada == 1 || progreso < 100) && fecha != "Saldo Inicial" && tipo != "P" && pagada != 1) ? "text-danger" : ""; %>
  <% if (fecha == "Saldo Inicial") { %>
    <td></td>
    <td><%= fecha.replace("Saldo ","") %></td>
    <td></td>
    <td></td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>  
    <td class="<%= clase %> fecha"><%= fecha %></td>
    <td class="<%= clase %> comprobante"><%= (tipo=="P") ? "Recibo": tipo_comprobante %></td>
    <td class="<%= clase %> numero">
      <span><%= comprobante %> <%= (anulada == 1) ? "(ANULADA)":"" %></span>
      <% if (!isEmpty(observaciones)) { %>
        <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-commenting text-warning"></i>
      <% } %>
    </td>
  <% } %>
  <td class="tar <%= clase %>"><%= Math.abs(Number(debe)).toFixed(2) %></td>
  <td class="tar <%= clase %>"><%= Math.abs(Number(haber)).toFixed(2) %></td>
  <td class="tar <%= clase %>"><%= Number(saldo).toFixed(2) %></td>
  <td>
    <% if (fecha != "Saldo Inicial" && tipo != "P") { %>
    <div class="progress mb0">
      <div class="progress-bar" role="progressbar" style="width:<%= Number(progreso).toFixed(2) %>%"><%= Number(progreso).toFixed(0) %>%</div>
    </div>
    <% } %>
  </td>
  <td>
    <% if (fecha != "Saldo Inicial") { %>
      <i class="fa fa-print imprimir" />
    <% } %>
  </td>      
    <td>
      <% if (fecha != "Saldo Inicial" && anulada == 0) { %>
        <% if (tipo != "P") { %>
          <i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" />
        <% } else { %>
          <i class="fa fa-file-text-o ver_recibo text-dark" data-id="<%= id %>" />
        <% } %>
      <% } %>
  </td>
  <td>
    <% if (fecha != "Saldo Inicial" && control.check("cuentas_corrientes_clientes")>2) { %>
      <% if (tipo == "P") { %>
        <i title="Eliminar comprobante" class="glyphicon glyphicon-trash delete" />
      <% } else { %>
        <% if (tipo_punto_venta != "E") { %>
          <% if (anulada == 0) { %>
            <i title="Anular comprobante" class="glyphicon glyphicon-remove anular text-danger" />
          <% } else if (anulada == 1) { %>
            <i title="Eliminar comprobante" class="glyphicon glyphicon-trash delete" />
          <% } %>    
        <% } %>
      <% } %>
    <% } %>
  </td>
</script>





<script type="text/template" id="recibos_clientes_listado_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <div class="row clearfix padder">
      <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-user icono_principal"></i>Clientes</h1>
    </div>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <?php 
      if ($empresa->id_proyecto == 3) { 
        $active = "recibos_clientes"; include("inm/alquileres_menu.php"); 
      } else {
        $active = "recibos_clientes"; include("cli/clientes_menu.php"); 
      } ?>

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-8 sm-m-b">
            <div class="input-group">
              <input type="text" id="recibos_clientes_listado_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
              </span>

              <span class="input-group-btn">
                <div class="btn-group dropdown ml5">
                  <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void(0)" class="exportar_excel">Excel</a></li>
                  </ul>
                </div>
              </span>              
            </div>
          </div>      

        </div>
      </div>
      <div class="advanced-search-div bg-light dk">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
          <div class="row pl10 pr10">

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" placeholder="Desde" autocomplete="off" id="recibos_clientes_desde" class="form-control">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" placeholder="Hasta" autocomplete="off" id="recibos_clientes_hasta" class="form-control">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>

            <% if (control.check("almacenes")>0) { %>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select class="form-control" id="recibos_clientes_sucursales">
                    <option value="0">Sucursal</option>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  </select>   
                </div>
              </div>
            <% } %>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <button class="buscar btn btn-block btn-dark btn-default"><i class="fa fa-search"></i> Buscar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <div class="panel-body resumen pb0" style="display:none">
      <div class="row">
        <div class="col-md-3">
          <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
            <div id="recibos_clientes_resumen_total" class="h3 font-thin text-white block">0</div>
            <span class="text-muted text-md pt5 db">Total</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="block tac panel padder-v item mb0" style="height: 80px">
            <div id="recibos_clientes_resumen_efectivo" class="h3 font-thin block">0</div>
            <span class="text-muted text-md pt5 db">Efectivo</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="block tac panel padder-v item mb0" style="height: 80px">
            <div id="recibos_clientes_resumen_tarjeta" class="h3 font-thin block">0</div>
            <span class="text-muted text-md pt5 db">Tarjeta</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
            <span id="recibos_clientes_resumen_cantidad" class="font-thin h3 block">0</span>
            <span class="text-muted text-md pt5 db">Cantidad</span>
          </div>
        </div>
      </div>
    </div>


    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="recibos_clientes_tabla" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;">
                <label class="i-checks m-b-none">
                  <input class="esc sel_todos" type="checkbox"><i></i>
                </label>
              </th>
              <th>Sucursal</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Comprobante</th>
              <th class="tar">Efectivo</th>
              <th class="tar">Tarjetas</th>
              <th class="tar">Cheques</th>
              <th class="tar">Depositos</th>
              <th class="tar">Descuento</th>
              <th class="tar">Retenciones</th>
              <th class="tar">Total</th>
              <% if (!seleccionar) { %>
                <th class="th_acciones w25"></th>
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

<script type="text/template" id="recibos_clientes_item_resultados_template">
  <% var clase = "edit"; %>
  <td>
    <label class="i-checks m-b-none">
      <input type="checkbox" class="checkbox" id="check<%= id %>"/><i></i>
    </label>
  </td>
  <td data-exp="<%= sucursal %>" class="<%= clase %>"><%= sucursal %></td>
  <td data-exp="<%= fecha %>" class="<%= clase %>"><%= fecha %></td>
  <td data-exp="<%= cliente %>" class="<%= clase %>"><span class="text-info"><%= cliente %></span>
    <% if (!isEmpty(observaciones)) { %>
      <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-commenting text-warning"></i>
    <% } %>
  </td>
  <td data-exp="P <%= zeroFill(punto_venta,3) %>-<%= zeroFill(numero,8) %>" class="<%= clase %>">P <%= zeroFill(punto_venta,3) %>-<%= zeroFill(numero,8) %></td>
  <td data-exp="<%= efectivo %>" class="<%= clase %> tar"><span class="tag_precio_2">$ <%= Number(efectivo).format() %></span></td>
  <td data-exp="<%= total_tarjetas %>" class="<%= clase %> tar"><span class="tag_precio_2">$ <%= Number(total_tarjetas).format() %></span></td>
  <td data-exp="<%= total_cheques %>" class="<%= clase %> tar"><span class="tag_precio_2">$ <%= Number(total_cheques).format() %></span></td>
  <td data-exp="<%= total_depositos %>" class="<%= clase %> tar"><span class="tag_precio_2">$ <%= Number(total_depositos).format() %></span></td>
  <td data-exp="<%= descuento %>" class="<%= clase %> tar"><span class="tag_precio_2">$ <%= Number(descuento).format() %></span></td>
  <% var ret = Number(retencion_ganancias) + Number(retencion_iibb) + Number(retencion_suss) + Number(retencion_iva) + Number(retencion_otras) %>
  <td data-exp="<%= ret %>" class="<%= clase %> tar"><span class="tag_precio_2">$ <%= Number(ret).format() %></span></td>
  <td data-exp="<%= -pago %>" class="<%= clase %> tar"><span class="tag_precio">$ <%= Number(-pago).format() %></span></td>
  <td class="p5 td_acciones">
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>    
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="ver_cta_cte">Ver Cta. Cte.</a></li>
      </ul>
    </div>  
  </td>
</script>