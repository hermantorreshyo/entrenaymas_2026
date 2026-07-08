<script type="text/template" id="propiedades_reservas_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-bed icono_principal"></i>Alquileres Temporarios</h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="">
        <a href="app/#propiedades_reservas_listado"><i class="fa fa-list text-info"></i> Listado</a>
      </li>
      <li class="active">
        <a href="javascript:void(0)"><i class="fa fa-calendar text-warning"></i> Calendario</a>
      </li>
    </ul>
    <div class="panel-body">
      <div id="calendar"></div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="propiedades_reservas_listado_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-bed icono_principal"></i>Alquileres Temporarios</h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a href="javascript:void(0)"><i class="fa fa-list text-info"></i> Listado</a>
      </li>
      <li class="">
        <a href="app/#propiedades_reservas"><i class="fa fa-calendar text-warning"></i> Calendario</a>
      </li>
    </ul>
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-4 sm-m-b">
          <div class="input-group">
            <input type="text" id="propiedades_reservas_listado_buscar" value="<%= window.propiedades_reservas_listado_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default"><i class="fa fa-search"></i></button>
            </span>
            <span class="input-group-btn">
              <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
            </span>
          </div>
        </div>          
        <div class="col-md-8 text-right">
          <% if (control.check("propiedades_reservas")>=3) { %>
            <a class="btn btn-info btn-addon nueva_reserva ml5" href="javascript:void(0)">
              <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nueva Reserva&nbsp;&nbsp;</span>
            </a>
          <% } %>
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
                <input type="text" placeholder="Desde" id="propiedades_reservas_desde" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
            </div>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <div class="input-group">
                <input type="text" placeholder="Hasta" id="propiedades_reservas_hasta" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
            </div>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <select class="form-control no-model" id="propiedades_reservas_tipo_estado">
                <option value="-1">Estado</option>
                <option value="0">Reservada</option>
                <option value="2">Pagada</option>
                <option value="3">A coordinar</option>
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
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="propiedades_reservas_tabla" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;">
                <label class="i-checks m-b-none">
                  <input class="esc sel_todos" type="checkbox"><i></i>
                </label>
              </th>
              <th>Numero</th>
              <th>Fecha Reserva</th>
              <th>Cliente</th>
              <th>Desde</th>
              <th>Hasta</th>
              <th>Noches</th>
              <th>Habitacion</th>
              <th>Estado</th>
              <th>Total</th>
              <th>Pago</th>
              <th>Debe</th>
              <th class="th_acciones"></th>
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

<script type="text/template" id="propiedades_reservas_item_resultados_template">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>    
  <td class="data"><%= numero %></td>
  <td class="data"><%= fecha_reserva %></td>
  <td class="data"><%= cliente.nombre %></td>
  <td class="data"><%= fecha_desde %></td>
  <td class="data"><%= fecha_hasta %></td>
  <td class="data"><%= cantidad_noches %></td>
  <td class="data"><%= propiedad %></td>
  <td class="data">
    <% if (eliminada == 1) { %>
      <span class="label bg-danger">Eliminada</span>
    <% } else if (id_estado == 0) { %>
      <span class="label bg-danger">Reservado</span>
    <% } else if (id_estado == 3) { %>
      <span class="label bg-warning">A Coordinar</span>
    <% } else if (id_estado == 2) { %>
      <span class="label bg-success">Pagado</span>
    <% } %>
  </td>
  <td class="data">
    <span class="tag_precio"><%= Number(precio).format() %></span>
  </td>
  <td class="data">
    <span class="tag_precio"><%= Number(total_pagado).format() %></span>
  </td>
  <td class="data">
    <span class="tag_precio"><%= Number(precio - total_pagado).format() %></span>
  </td>
  <td class="p5 td_acciones">
    <i data-toggle="tooltip" title="Imprimir" class="fa iconito active fa-print imprimir" />
    <% if (!seleccionar && control.check("propiedades_reservas") == 3) { %>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar Reserva</a></li>
          <li><a href="javascript:void(0)" class="eliminar_definitivo" data-id="<%= id %>">Eliminar Definitiva</a></li>
        </ul>
      </div>    
    <% } %>
  </td>
</script>

<script type="text/template" id="propiedad_reserva_template">
<div class="panel panel-default mb0">
  <div class="tab-container">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="tab_link active">
        <a href="#tab_reserva1" class="fs14 bold" role="tab" data-toggle="tab"><i class="fa fa-list text-info"></i> Informacion</a>
      </li>
      <li class="tab_link">
        <a href="#tab_reserva2" class="fs14 bold" role="tab" data-toggle="tab"><i class="fa fa-dollar text-success"></i> Pagos</a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab_reserva1" class="tab-pane active pt0">
        <div class="form-group">
          <label class="control-label">Cliente</label>
          <input type="hidden" id="propiedad_reserva_id_cliente" value="<%= id_cliente %>"/>
          <input type="text" placeholder="Escriba parte del nombre y seleccionelo de la lista..." value="<%= cliente.nombre %>" class="form-control no-model" id="propiedad_reserva_clientes">
        </div>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label class="control-label">Email</label>
              <input type="text" value="<%= cliente.email %>" class="form-control no-model" id="propiedad_reserva_cliente_email">
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label class="control-label">Telefono</label>
              <input type="text" value="<%= cliente.telefono %>" class="form-control no-model" id="propiedad_reserva_cliente_telefono">
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label class="control-label">Celular</label>
              <input type="text" value="<%= cliente.celular %>" class="form-control no-model" id="propiedad_reserva_cliente_celular">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label class="control-label">Entrada</label>
              <div class="input-group">
                <input type="text" name="fecha_desde" id="propiedad_reserva_fecha_desde" class="form-control"/>
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
            </div>
          </div>
          <div class="col-md-4 col-xs-12">
            <div class="form-group">
              <label class="control-label">Salida</label>
              <div class="input-group">
                <input type="text" name="fecha_hasta" id="propiedad_reserva_fecha_hasta" class="form-control"/>
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
            </div>
          </div>
          <div class="col-md-4 col-xs-12">
            <div class="form-group">
              <label class="control-label">Cant. Personas</label>
              <input type="number" min="1" name="personas" value="<%= personas %>" id="propiedad_reserva_personas" class="form-control"/>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 col-xs-12">
            <div class="form-group">
              <label class="control-label">Propiedad</label>
              <select id="propiedad_reserva_propiedades" name="id_propiedad" class="form-control"></select>
            </div>
          </div>
          <div class="col-md-4 col-xs-12">
            <div class="form-group">
              <label class="control-label">Precio</label>
              <input type="text" name="precio" value="<%= precio %>" id="propiedad_reserva_precio" class="form-control"/>
            </div>
          </div>
          <div class="col-md-4 col-xs-12">
            <div class="form-group">
              <label class="control-label">Estado</label>
              <select id="propiedad_reserva_estados" class="form-control">
                <option <%= (id_estado==0)?"selected":"" %> value="0">Reservada</option>
                <option <%= (id_estado==2)?"selected":"" %> value="2">Pago completo</option>
                <option <%= (id_estado==3)?"selected":"" %> value="3">A coordinar</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label">Notas </label>
          <textarea name="comentario" id="propiedad_reserva_comentario" placeholder="Escriba aqui observaciones o comentarios..." class="h100 form-control"><%= comentario %></textarea>
        </div>
      </div>

      <div id="tab_reserva2" class="tab-pane pt0">
        <div class="clearfix">
          <div class="col-sm-3 p3">
            <label class="control-label">Fecha de Pago</label>
            <div class="input-group">
              <input type="text" id="propiedad_reserva_fecha_pago" class="form-control no-model"/>
              <span class="input-group-btn">
                <button class="btn btn-default btn-cal"><i class="fa fa-calendar"></i></button>
              </span>
            </div>
          </div>
          <div class="col-sm-3 p3">
            <label class="control-label">Forma de Pago</label>
            <select id="propiedad_reserva_metodo_pago" class="form-control no-model">
              <option>Efectivo</option>
              <option>Tarjeta</option>
              <option>Cheque</option>
              <option>Transferencia</option>
              <option>Mercadopago</option>
              <option>Paypal</option>
              <option>TodoPago</option>
            </select>
          </div>
          <div class="col-sm-3 p3">
            <label class="control-label">Observaciones</label>
            <input id="propiedad_reserva_pago_observaciones" type="text" class="form-control no-model"/>
          </div>
          <div class="col-sm-3 p3">
            <label class="control-label">Monto</label>
            <div class="input-group">
              <input type="text" placeholder="Monto" id="propiedad_reserva_total_pago" class="form-control no-model">
              <span class="input-group-btn">
                <button id="propiedad_reserva_agregar_pago" class="btn btn-info"><i class="fa fa-plus"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="b-a" style="overflow: auto; height: 180px">
          <table id="tabla_pagos" class="table table-small sortable m-b-none default footable">
            <thead class="bg-light">
              <tr>
                <th>Fecha</th>
                <th>Metodo</th>
                <th>Observaciones</th>
                <th class="w100">Total</th>
                <th class="w25"></th>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i< pagos.length;i++) { %>
                <% var p = pagos[i] %>
                <tr data-fecha_pago='<%= p.fecha_pago %>' data-metodo_pago='<%= p.metodo_pago %>' data-pago='<%= p.pago %>' data-observaciones='<%= p.observaciones %>'>";
                <td class='editar_pago'><%= p.fecha_pago %></td>
                <td class='editar_pago'><%= p.metodo_pago %></td>
                <td class='editar_pago'><%= p.observaciones %></td>
                <td class='editar_pago'><%= Number(p.pago).format() %></td>
                <td><i class="fa fa-times eliminar_pago text-danger"></i></td>
                </tr>
              <% } %>
            </tbody>
            <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td id="propiedad_reserva_subtotal_pagos"><%= Number(total_pagado).format() %></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn eliminar pull-left btn-danger">Eliminar</button>
    <button class="btn guardar pull-right btn-success">Guardar</button>
    <button class="btn imprimir mr5 pull-right btn-default">Imprimir</button>
  </div>  
</div>
</script>