<script type="text/template" id="deliveries_resultados_template">
<% if (!seleccionar) { %>
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
    <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-cart icono_principal mr10"></i>Pedidos</h1>
  </div>
</div>
<div class="wrapper-md ng-scope">
<% } %>
    <div class="panel panel-default">
      
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-3 sm-m-b">
              <div class="input-group">
                  <input type="text" id="deliveries_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                  <span class="input-group-btn">
                    <button class="btn btn-default"><i class="fa fa-search"></i></button>
                  </span>
                  <?php /*
                  <span class="input-group-btn">
                    <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
                  </span>
                  */ ?>
              </div>
            </div>          
            <% if (!seleccionar) { %>
              <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                <button class="btn btn-info btn-addon ml5 nuevo">
                  <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
                </button>
              </div>
            <% } %>
          </div>
        </div>
        <?php /*
        <div class="advanced-search-div bg-light dk" style="display:none">
          <div class="wrapper clearfix">
            <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
            <div class="form-inline">    

              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Desde" id="deliveries_desde" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Hasta" id="deliveries_hasta" class="form-control">
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
        */ ?>
        <% if (!seleccionar) { %>
          <div class="bulk_action wrapper pb0">
            <button class="btn btn-default enviar btn-addon"><i class="icon fa fa-send"></i>Enviar por email</button>
            <button class="btn btn-default imprimir btn-addon"><i class="icon fa fa-print"></i>Imprimir</button>
          </div>
        <% } %>

        <div class="panel-body">
            <div class="b-a table-responsive">
                <table id="deliveries_tabla" class="table table-striped sortable m-b-none default footable">
                    <thead>
                        <tr>
                            <% if (!seleccionar) { %>
                              <th style="width:20px;">
                                  <label class="i-checks m-b-none">
                                      <input class="esc sel_todos" type="checkbox"><i></i>
                                  </label>
                              </th>                      
                              <th class="w150">Fecha</th>
                            <% } else { %>
                              <th style="width:20px;"></th>
                            <% } %>
                            <th>Hora</th>
                            <th>Mesa</th>
                            <th>Cliente</th>
                            <% if (!seleccionar) { %>
                              <th>Comprobante</th>
                            <% } %>
                            <th>Direccion</th>
                            <th>Telefono</th>
                            <th class="tar w150">Total</th>
                            <% if (!seleccionar) { %>
                              <th class="th_acciones w120">Acciones</th>
                            <% } %>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="pagination_container hide-if-no-paging"></tfoot>
                </table>              
            </div>
        </div>
    </div>
<% if (!seleccionar) { %></div><% } %>
</script>

<script type="text/template" id="deliveries_item_resultados_template">
    <% var clase = (anulada == 1) ? "text-danger" : ((!seleccionar)?"edit":""); %>
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
    <td class="<%= clase %>"><%= fecha %></td>
    <td class="<%= clase %>"><%= hora %></td>
    <td class="<%= clase %>"><%= reference_id %></td>
    <td class="<%= clase %> data">
      <span class="text-info"><%= (isEmpty(cliente)) ? "Consumidor Final" : cliente %></span>
    </td>
    <td class="<%= clase %> comprobante data">
      <%= comprobante %>
      <% if (tipo=="T" && id_tipo_estado == 0) { %>
        <span class="label bg-danger m-l">En proceso</span>
      <% } %>
    </td>
    <td class="<%= clase %>">
      <%= (tipo=="T")?"Mostrador":"" %>
      <%= direccion %>
    </td>
    <td class="<%= clase %>"><%= telefono %></td>
    <td class="<%= clase %> tar data">
      <span class="tag_precio">$ <%= Number(total).format() %></span>
    </td>
    <% if (!seleccionar) { %>
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
    <% } %>
</script>