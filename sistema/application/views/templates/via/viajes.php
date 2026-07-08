<script type="text/template" id="viajes_resultados_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">Todav&iacute;a no ten&eacute;s ningun viaje</h1>
  <h3 class="h3">Para a&ntilde;adir tu primer viaje, hace click en el siguiente bot&oacute;n</h3>
  <div class="list-icon">
    <a href="app/#viaje"><i class="icon-note"></i></a>
  </div>
  <% if (control.check("viajes") == 3) { %>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#viaje">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
  <% } %>
  <p>
    Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
  </p>
</div>
<div class="seccion_llena" style="display:none">
  <% if (!seleccionar) { %>
    <div class="bg-light lter b-b wrapper-md ng-scope">
        <h1 class="m-n font-thin h3"><i class="fa fa-suitcase icono_principal"></i>Viajes
    </div>
  <% } %>
  <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="<% if (!seleccionar) { %>col-md-6 col-lg-3 <% } else { %> col-xs-12 <% } %> sm-m-b">
            <div class="input-group">
              <input type="text" id="viajes_buscar" value="<%= window.viajes_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </span>
            </div>
          </div>
          <% if (!seleccionar && (control.check("viajes") == 3)) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#viaje">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk">
        <div class="wrapper oh">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="cb">
            <div class="form-group fl" style="width: 250px; display: inline-block">
              <select id="viajes_buscar_activos" class="w100p form-control no-model">
                <option <%= (window.viajes_buscar_activo == 1 ? "selected":"") %> value="1">Mostrar solo activos</option>
                <option <%= (window.viajes_buscar_activo == -1 ? "selected":"") %> value="-1">Mostrar todos</option>
              </select>
            </div>
            <div class="form-group dib fl">
              <button id="articulos_buscar_avanzada_btn" class="btn buscar btn-default ml10"><i class="fa fa-search"></i> Buscar</button>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="viajes_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <% if (!seleccionar) { %>
                  <th style="width:20px;">
                    <label class="i-checks m-b-none">
                      <input class="esc sel_todos" type="checkbox"><i></i>
                    </label>
                  </th>
                <% } else { %>
                  <th style="width:20px;"></th>
                <% } %>
                <th>Nombre</th>
                <% if (ID_EMPRESA == 501) { %>
                  <th></th>
                <% } %>
                <th>Salida</th>
                <th>Regreso</th>
                <% if (ID_EMPRESA == 135) { %>
                  <th>Asientos</th>
                  <th>Ocupado</th>
                <% } else if (ID_EMPRESA != 501) { %>
                  <th class="sorting" data-sort-by="orden">Orden</th>
                <% } %>
                <% if (control.check("vehiculos")>0 && ID_EMPRESA != 501) { %>
                  <th class="th_acciones w80"></th>
                <% } %>
                <% if (!seleccionar) { %>
                  <th class="th_acciones w120"> Acciones</th>
                <% } %>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="viajes_item_resultados_template">
  <% var clase = "" %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>
  <% } %>
  <td class="<%= clase %> data">
    <span class="<%= (activo==1)?'text-info':'text-muted' %>"><%= nombre.ucwords() %></span>
  </td>
  <% if (ID_EMPRESA == 501) { %>
    <td><button class="btn <%= (estado==0)?"btn-default":"btn-success" %> realizar">
      <%= (estado==0)?"Marcar":"Hecho" %>
    </button></td>
  <% } %>
  <td class="<%= clase %> data"><%= fecha %></td>
  <td class="<%= clase %> data"><%= fecha_llegada %></td>
  <% if (ID_EMPRESA == 135) { %>
    <td class="<%= clase %> data"><%= total_asientos %></td>
    <td class="<%= clase %> data"><%= total_ocupados %></td>
  <% } else if (ID_EMPRESA != 501) { %>
    <td class="<%= clase %> data"><%= orden %></td>
  <% } %>
  <% if (control.check("vehiculos")>0 && ID_EMPRESA != 501) { %>
    <td>
      <a class="btn btn-default btn-sm" href="app/#viajes_asientos/<%= id %>">Asientos</a>
    </td>
  <% } %>
  <% if (!seleccionar) { %>
    <td class="<%= clase %>">
      <% if (control.check("viajes") == 3) { %>
        <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
        <% if (control.check("web_configuracion")>0) { %>
          <i title="Destacado" class="fa fa-star iconito destacado <%= (destacado == 1)?"active":"" %>"></i>
        <% } %>
      <% } %>
      <div class="btn-group dropdown ml10">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <% if (control.check("web_configuracion")>0) { %>
            <li><a target="_blank" href="http://<%= String(DOMINIO+'/'+link+'?preview=1').replace('//','/') %>">Ver web</a></li>
          <% } %>
          <% if (control.check("viajes") == 3) { %>
            <% if (ID_EMPRESA == 501) { %>
              <li><a href="javascript:void(0)" class="imprimir_contrato" data-id="<%= id %>">Contrato</a></li>
            <% } %>
            <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
          <% } %>
        </ul>
      </div>
    </td>
  <% } %>
</script>


<script type="text/template" id="viaje_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-suitcase icono_principal"></i>Viajes /
    <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <% if (ID_EMPRESA == 501) { %>
    <?php include("viajes_detalle_501.php") ?>
  <% } else { %>
    <?php include("viajes_detalle.php") ?>
  <% } %>
</div>
</script>

<script type="text/template" id="viaje_asientos_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-suitcase icono_principal"></i>Viajes /
    <b>Asignaci&oacute;n de asientos</b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row piso_1">
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="form-group">
              <b><%= nombre.toUpperCase() %></b>
            </div>
            <% if (control.check("viajes") == 3) { %>
              <div class="form-group">
                <label>Observaciones:</label>
                <textarea id="viaje_asientos_observaciones" name="texto" class="form-control h100"><%= texto %></textarea>
              </div>
              <button class="btn btn-success guardar">Guardar</button>
            <% } else { %>
              <div class="form-group">
                <label>Observaciones:</label>
                <%= nl2br(texto) %>
              </div>
            <% } %>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row clearfix">
              <div class="form-group clearfix">
                <div class="col-md-4">
                  <select id="viaje_asientos_vehiculos" class="form-control no-model">
                    <% for (var i=0;i< vehiculos.length;i++) { %>
                      <% var vehiculo = vehiculos[i]; %>
                      <option value="<%= vehiculo.id %>"><%= vehiculo.nombre %></option>
                    <% } %>
                  </select>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <input type="text" id="viaje_asientos_buscar_cliente" class="form-control no-model" autocomplete="off" placeholder="Buscar cliente..."/>
                    <span class="input-group-btn">
                      <button class="btn buscar_cliente btn-default"><i class="fa fa-search"></i></button>
                    </span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="clearfix">
                    <button class="btn btn-success asignar">Asignar</button>
                    <% if (control.check("viajes") == 3) { %>
                      <div class="btn-group dropdown pull-right">
                        <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                          <span>Opciones</span>
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a href="javascript:void(0)" class="imprimir_manifiesto">Imprimir manifiesto</a></li>
                          <li><a href="javascript:void(0)" class="imprimir_pasajeros">Imprimir pasajeros</a></li>
                          <li><a href="javascript:void(0)" class="imprimir_taquilla">Imprimir taquilla</a></li>
                          <li><a href="javascript:void(0)" class="ver_habitaciones">Editar habitaciones</a></li>
                        </ul>
                      </div>
                    <% } %>
                  </div>
                </div>
              </div>
            </div>
            <div id="viaje_asientos_dibujo">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="reserva_asiento_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="font-bold">Detalle de reserva</span>
    <i class="fr cp cerrar fa fa-times text-muted"></i>
  </div>
  <div class="panel-body">
    <div class="clearfix m-b padder">
      <div class="row">
        <div class="col-sm-3 p3 <%= (ID_EMPRESA == 135)?"dn":"" %>">
          <label class="control-label">Cliente</label>
          <input placeholder="Escriba un nombre..." type="text" value="<%= cliente %>" id="reserva_asiento_cliente" class="form-control no-model">
        </div>
        <div class="col-sm-2 p3">
          <label class="control-label">Fecha reserva</label>
          <div class="input-group">
            <input type="text" id="reserva_asiento_fecha" class="form-control"/>
            <span class="input-group-btn">
              <button class="btn btn-default btn-cal"><i class="fa fa-calendar"></i></button>
            </span>
          </div>
        </div>
        <div class="col-sm-2 p3">
          <label class="control-label">Estado</label>
          <select class="form-control action" id="reserva_asiento_tipo_estado" name="id_tipo_estado">
            <% for(var i=0;i< tipos_estado_pedidos.length;i++) { %>
            <% var c = tipos_estado_pedidos[i]; %>
              <option <%= (id_tipo_estado == c.id)?"selected":"" %> value="<%= c.id %>"><%= c.nombre %></option>
            <% } %>                
          </select>            
        </div>
        <div class="col-sm-2 p3">
          <label class="control-label">
            <% if (ID_EMPRESA == 135) { %>
              <% if (control.check("vehiculos")>0) { %>
                Salida desde
              <% } else { %>
                Horario
              <% } %>
            <% } else { %>
              Salida
            <% } %>
          </label>
          <input type="text" name="salida_desde" value="<%= salida_desde %>" id="reserva_asiento_salida_desde" class="form-control">
        </div>
        <div class="col-sm-2 p3">
          <label class="control-label">Vendedor</label>
          <select id="reserva_viaje_vendedores" class="form-control">
            <option value="0">-</option>
            <% for(var i=0; i< vendedores.length; i++) { %>
              <% var ven = vendedores[i] %>
              <% if (ID_SUCURSAL != 0) { %>
                <% if (ven.id_sucursal == ID_SUCURSAL) { %>
                  <option <%= (ven.id == id_vendedor)?"selected":"" %> value="<%= ven.id %>"><%= ven.nombre %></option>
                <% } %>
              <% } else { %>
                <option <%= (ven.id == id_vendedor)?"selected":"" %> value="<%= ven.id %>"><%= ven.nombre %></option>
              <% } %>
            <% } %>
          </select>
        </div>
      </div>
    </div>
    <div class="p3">
      <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="tab_link active">
            <a href="#tab_reserva1" class="fs14 bold" role="tab" data-toggle="tab">Pasajeros</a>
          </li>
          <% if (control.check("opcionales")>0) { %>
            <li class="tab_link">
              <a href="#tab_reserva3" class="fs14 bold" role="tab" data-toggle="tab">Opcionales</a>
            </li>
          <% } %>
          <li class="tab_link">
            <a href="#tab_reserva2" class="fs14 bold" role="tab" data-toggle="tab">Entregas</a>
          </li>
          <% if (control.check("hoteles")>0) { %>
            <li class="tab_link">
              <a href="#tab_reserva6" class="fs14 bold" role="tab" data-toggle="tab">Hotel</a>
            </li>
          <% } %>
          <li class="tab_link">
            <a href="#tab_reserva4" class="fs14 bold" role="tab" data-toggle="tab">Observaciones</a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="tab_reserva1" class="tab-pane active pt0">
            <div class="b-a" style="overflow: auto; height: 180px">
              <table id="tabla_asientos" class="table table-small sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <% if (control.check("vehiculos")>0) { %>
                      <th style="width: 60px">Nro</th>
                    <% } %>
                    <th style="min-width: 120px">Nombre</th>
                    <th style="min-width: 120px">Apellido</th>
                    <?php /*
                    <th style="min-width: 120px">Fecha Nac.</th>
                    <th style="min-width: 100px">DNI</th>
                    <th style="min-width: 50px">Menor</th>
                    <th style="min-width: 120px;">Nacionalidad</th>
                    */ ?>
                    <th style="min-width: 50px"></th>
                    <th style="min-width: 100px">Precio</th>
                    <th style="min-width: 100px"><%= (ID_EMPRESA == 135)?"But. Arriba":"Adicionales" %></th>
                    <th style="min-width: 100px"><%= (ID_EMPRESA == 135)?"But. Abajo":"" %></th>
                    <th style="min-width: 100px"><%= (ID_EMPRESA == 135)?"Map":"" %></th>
                    <th style="min-width: 100px"><%= (ID_EMPRESA == 135)?"Single":"" %></th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div class="b-a" style="overflow: auto;">
              <table class="table table-small sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <% if (control.check("opcionales")>0) { %>
                      <td>
                        <button class="btn btn-default agregar_pasajero">Agregar pasajero</button>
                      </td>
                    <% } %>
                    <td class="tar" style="vertical-align: middle;" id="reserva_asientos_subtotal_asientos"></td>
                    <td class="w25"></td>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <div id="tab_reserva2" class="tab-pane pt0">
            <div class="clearfix">
              <div class="col-sm-4 p3">
                <div class="input-group">
                  <input type="text" id="reserva_asiento_fecha_pago" class="form-control"/>
                  <span class="input-group-btn">
                    <button class="btn btn-default btn-cal"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <div class="col-sm-4 p3">
                <select id="reserva_asiento_metodo_pago" class="form-control">
                  <option>Efectivo</option>
                  <option>Tarjeta</option>
                  <option>Cheque</option>
                  <option>Transferencia</option>
                  <option>Mercadopago</option>
                  <option>Paypal</option>
                  <option>TodoPago</option>
                </select>
              </div>
              <div class="col-sm-4 p3">
                <div class="input-group">
                  <input type="text" placeholder="Monto" id="reserva_asiento_total_pago" class="form-control no-model">
                  <span class="input-group-btn">
                    <button id="reserva_asiento_agregar_pago" class="btn btn-info"><i class="fa fa-plus"></i></button>
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
                    <th class="w100">Total</th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div class="b-a" style="overflow: auto;">
              <table class="table table-small sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <td class="tar" id="reserva_asientos_subtotal_pagos"></td>
                    <td class="w25"></td>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <% if (control.check("opcionales")>0) { %>
            <div id="tab_reserva3" class="tab-pane pt0">
              <div class="clearfix">
                <div class="col-sm-4 p3">
                  <select id="reserva_asiento_opcionales" class="form-control"></select>
                </div>
                <div class="col-sm-4 p3">
                  <div class="input-group">
                    <input type="text" placeholder="Monto" id="reserva_asiento_opcional_total" class="form-control no-model">
                    <span class="input-group-btn">
                      <button id="reserva_asiento_agregar_opcional" class="btn btn-info"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="b-a" style="overflow: auto; height: 180px">
                <table id="tabla_opcionales" class="table table-small sortable m-b-none default footable">
                  <thead class="bg-light">
                    <tr>
                      <th>Nombre</th>
                      <th class="w100">Total</th>
                      <th class="w25"></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
              <div class="b-a" style="overflow: auto;">
                <table class="table table-small sortable m-b-none default footable">
                  <thead class="bg-light">
                    <tr>
                      <td class="tar" id="reserva_asientos_subtotal_opcionales"></td>
                      <td class="w25"></td>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          <% } %>
          <div id="tab_reserva4" class="tab-pane pt0">
            <div class="clearfix">
              <div class="form-group">
                <label class="control-label">Prestador del servicio</label>
                <input type="text" class="form-control" value="<%= prestador_servicio %>" name="prestador_servicio">
              </div>
              <div class="form-group">
                <textarea placeholder="Escriba aqui observaciones o comentarios..." id="reserva_viaje_observaciones" name="observaciones" class="form-control h100"><%= observaciones %></textarea>
              </div>
            </div>
          </div>
          <% if (control.check("hoteles")>0) { %>
            <div id="tab_reserva6" class="tab-pane pt0">
              <% if (control.check("vehiculos") == 0) { %>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Hotel</label>
                      <input type="text" class="form-control" value="<%= hotel %>" name="hotel">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Fecha de llegada</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="reserva_fecha_llegada_hotel" value="<%= fecha_llegada_hotel %>" name="fecha_llegada_hotel">
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>              
                      </div>
                    </div>
                  </div>
                </div>
              <% } else { %>
                <div class="b-a" style="overflow: auto; height: 200px">
                  <table id="tabla_habitaciones" class="table table-small sortable m-b-none default footable">
                    <thead class="bg-light">
                      <tr>
                        <th>Pasajero</th>
                        <th>Habitacion</th>
                        <th>Hotel</th>
                        <th style="width: 100px">Numero</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              <% } %>
              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <textarea name="hotel_observaciones" class="form-control"><%= hotel_observaciones %></textarea>
              </div>
            </div>
          <% } %>
        </div>
      </div>
      <div class="m-t row clearfix">
        <div class="col-md-12">
          <div class="tar">
            <label class="control-label tar fs24 font-bold">Resto: </label>
            <span id="reserva_viaje_diferencia" style="margin-left: 20px; background-color: transparent; border: none; color: black; font-size: 24px; font-weight: bold; text-align: left; "></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <% if (typeof id != "undefined" && control.check("viajes") == 3) { %>
      <button class="btn eliminar btn-danger">Eliminar</button>
    <% } %>
    <button class="btn pull-right guardar btn-success">Guardar</button>
    <% if (typeof id != "undefined") { %>
      <div class="btn-group dropdown pull-right m-r">
        <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
          <span>Imprimir</span>
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <% if (control.check("vehiculos")>0) { %>
            <li><a href="javascript:void(0)" class="imprimir">Boletos</a></li>
            <li><a href="javascript:void(0)" class="imprimir_recibo">Recibo</a></li>
          <% } %>
          <% if (control.check("web_configuracion")>0) { %>
            <li><a href="javascript:void(0)" class="voucher">Voucher</a></li>
          <% } %>
        </ul>
      </div>
    <% } %>
  </div>
</div>
</script>

<script type="text/template" id="reserva_viaje_asiento_item_template">
<% if (control.check("vehiculos")>0) { %>
  <td class="p0">
    <select class="form-control pl5 pr5">
      <option><%= numero_asiento %></option>
    </select>
  </td>
<% } %>
<td class="p0"><input type="text" name="nombre" value="<%= nombre %>" data-next-select=".apellido" class="form-control nombre text-u-c"/></td>
<td class="p0"><input type="text" name="apellido" value="<%= apellido %>" data-next-select=".fecha" class="form-control apellido text-u-c"/></td>


<td class="p0 dn"><input type="text" name="fecha_nac" value="<%= fecha_nac %>" data-next-select=".dni" class="fecha form-control text-u-c"/></td>
<td class="p0 dn"><input type="text" name="dni" value="<%= dni %>" data-next-select=".nacionalidad" class="form-control dni text-u-c"/></td>
<td class="p0 dn">
  <select name="menor" class="w100p pl5 pr0 form-control">
    <option <%= (menor == "0")?"selected":"" %> value="0">No</option>
    <option <%= (menor == "1")?"selected":"" %> value="1">Si</option>
  </select>
</td>
<td class="p0 dn"><input type="text" name="nacionalidad" value="<%= nacionalidad %>" data-next-select=".precio" class="form-control nacionalidad text-u-c"/></td>

<td class="p0">
  <select name="moneda" class="w100p pl5 pr0 form-control">
    <% for(var i=0; i < window.monedas.length; i++) { %>
      <% var o = monedas[i]; %>
      <option <%= (moneda == o.codigo)?"selected":"" %> value="<%= o.codigo %>"><%= o.codigo %></option>
    <% } %>
  </select>
</td>
<td class="p0">
  <input type="text" <%= (control.check("viajes")<3)?"disabled":"" %> name="precio" data-next-select=".recargo" value="<%= Number(precio).toFixed(2) %>" class="form-control precio text-u-c"/>
</td>
<td class="p0">
  <% if (control.check("viajes")==3) { %>
    <input type="text" name="recargo" value="<%= Number(recargo).toFixed(2) %>" class="form-control recargo text-u-c"/>
  <% } else if (control.check("viajes")>1) { %>
    <select name="recargo" class="form-control recargo">
      <% if (recargo_default != 0) { %>
        <option <%= (recargo == recargo_default)?"selected":"" %> value="<%= Number(recargo_default).toFixed(2) %>"><%= Number(recargo_default).toFixed(2) %></option>
      <% } %>
      <option <%= (recargo==0)?"selected":"" %> value="0">0.00</option>
    </select>
  <% } %>
</td>
<td class="p0">
  <% if (control.check("viajes")==3) { %>
    <input type="text" name="recargo_2" value="<%= Number(recargo_2).toFixed(2) %>" class="form-control recargo_2 text-u-c"/>
  <% } else if (control.check("viajes")>1) { %>
    <select name="recargo_2" class="form-control recargo_2">
      <% if (recargo_2_default != 0) { %>
        <option <%= (recargo_2 == recargo_2_default)?"selected":"" %> value="<%= Number(recargo_2_default).toFixed(2) %>"><%= Number(recargo_2_default).toFixed(2) %></option>
      <% } %>
      <option <%= (recargo_2==0)?"selected":"" %> value="0">0.00</option>
    </select>
  <% } %>
</td>
<td class="p0">
  <% if (control.check("viajes")==3) { %>
    <input type="text" name="recargo_3" value="<%= Number(recargo_3).toFixed(2) %>" class="form-control recargo_3 text-u-c"/>
  <% } else if (control.check("viajes")>1) { %>
    <select name="recargo_3" class="form-control recargo_3">
      <% if (recargo_3_default != 0) { %>
        <option <%= (recargo_3 == recargo_3_default)?"selected":"" %> value="<%= Number(recargo_3_default).toFixed(2) %>"><%= Number(recargo_3_default).toFixed(2) %></option>
      <% } %>
      <option <%= (recargo_3==0)?"selected":"" %> value="0">0.00</option>
    </select>
  <% } %>
</td>
<td class="p0">
  <% if (control.check("viajes")==3) { %>
    <input type="text" name="recargo_4" value="<%= Number(recargo_4).toFixed(2) %>" class="form-control recargo_4 text-u-c"/>
  <% } else if (control.check("viajes")>1) { %>
    <select name="recargo_4" class="form-control recargo_4">
      <% if (recargo_4_default != 0) { %>
        <option <%= (recargo_4 == recargo_4_default)?"selected":"" %> value="<%= Number(recargo_4).toFixed(2) %>"><%= Number(recargo_4).toFixed(2) %></option>
      <% } %>
      <option <%= (recargo_4==0)?"selected":"" %> value="0">0.00</option>
    </select>
  <% } %>
</td>
<td class="w25 p5">
  <% if (control.check("viajes")==3) { %>
    <i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" />
  <% } %>
</td>
</script>

<script type="text/template" id="reserva_viaje_asiento_habitacion_item_template">
<input type="hidden" class="id" value="<%= (typeof id != undefined)?id:0 %>" />
<input type="hidden" class="id_reserva" value="<%= id_reserva %>" />
<td>
  <label class="i-checks m-b-none">
    <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
  </label>
</td>
<td><%= apellido+" "+nombre %></td>
<td>
  <%= (tipo_habitacion == 0)?"Sin definir":"" %>
  <%= (tipo_habitacion == 1)?"SINGLE":"" %>
  <%= (tipo_habitacion == 2)?"MAT":"" %>
  <%= (tipo_habitacion == 3)?"DOBLE":"" %>
  <%= (tipo_habitacion == 4)?"MAT+1":"" %>
  <%= (tipo_habitacion == 5)?"TRIPLE":"" %>
  <%= (tipo_habitacion == 6)?"X4":"" %>
  <%= (tipo_habitacion == 7)?"SOLO A COMPARTIR":"" %>
  <%= (tipo_habitacion == 8)?"SOLA A COMPARTIR":"" %>
  <%= (tipo_habitacion == 9)?"MAT+2":"" %>
  <%= (tipo_habitacion == 10)?"X5":"" %>
  <%= (tipo_habitacion == 11)?"MAT+3":"" %>
  <%= (tipo_habitacion == 12)?"X6":"" %>
</td>
<td><%= numero_habitacion %></td>
<td><%= hotel %></td>
</script>

<script type="text/template" id="reserva_viaje_asiento_asignar_habitacion">
  <div class="panel panel-default">
    <div class="panel panel-default">
      <div class="panel-heading">
        Asignar habitaciones
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Tipo de Habitacion</label>
          <select name="tipo_habitacion" id="reserva_viaje_asiento_asignar_tipo_habitacion" class="form-control tipo_habitacion" style="width: 100%">
            <option value="0">Seleccione</option>
            <option value="1">SINGLE</option>
            <option value="2">MAT</option>
            <option value="3">DOBLE</option>
            <option value="4">MAT+1</option>
            <option value="5">TRIPLE</option>
            <option value="6">X4</option>
            <option value="9">MAT+2</option>
            <option value="10">X5</option>
            <option value="11">MAT+3</option>
            <option value="12">X6</option>
            <option value="7">SOLO A COMPARTIR</option>
            <option value="8">SOLA A COMPARTIR</option>
          </select>
        </div>
        <div class="form-group">
          <label class="control-label">Hotel</label>
          <select id="reserva_viaje_asiento_asignar_hotel" name="hotel" class="hotel form-control" style="width: 100%">
            <option value="">Sin asignar</option>
            <% for(var i=0; i < window.hoteles.length; i++) { %>
              <% var o = hoteles[i]; %>
              <option value="<%= o.nombre %>"><%= o.nombre %></option>
            <% } %>
          </select>
        </div>
        <div class="form-group">
          <label class="control-label">Habitacion</label>
          <input type="text" name="numero_habitacion" id="reserva_viaje_asiento_asignar_numero_habitacion" class="numero_habitacion form-control"/>
        </div>
      </div>
    </div>
    <div class="panel-footer tar">
      <button class="btn btn-success guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="reserva_viaje_asiento_habitaciones_template">
  <div class="panel panel-default">
    <div class="panel-heading">
      Listado de habitaciones
    </div>
    <div class="panel-body">
      <div class="b-a" style="overflow: auto; height: 300px">
        <table id="tabla_habitaciones" class="table table-small sortable m-b-none default footable">
          <thead class="bg-light">
            <tr>
              <th class="w25"></th>
              <th>Pasajero</th>
              <th>Habitacion</th>
              <th>Numero</th>
              <th>Hotel</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
    <div class="panel-footer tar">
      <button class="btn btn-default asignar_habitaciones">Asignar habitacion</button>
    </div>
  </div>
</script>

<script type="text/template" id="reserva_viaje_pago_item_template">
<td class="editar"><%= fecha %></td>
<td class="editar"><%= metodo %></td>
<td class="editar"><%= Number(total).toFixed(2) %></td>
<td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" /></td>
</script>

<script type="text/template" id="reserva_viaje_opcional_item_template">
<td class="editar"><span class="text-info"><%= opcional %></span></td>
<td class="editar"><%= Number(total).toFixed(2) %></td>
<td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" /></td>
</script>


<script type="text/template" id="reserva_asiento_dashboard_template">
  <div class="panel consulta row-sm-same-height">
    <div class="col-sm-4 col-xs-12 col-sm-height">
      <div class="media">
        <% var clase = "bg-success" %>
        <% if (id_tipo_estado == 1 || id_tipo_estado == 2) clase = "bg-danger" %>
        <% if (id_tipo_estado == 3 || id_tipo_estado == 0) clase = "bg-warning" %>
        <span class="avatar <%= clase %> pull-left">
          <%= isEmpty(cliente) ? cliente_email.substr(0,1) : cliente.substr(0,1) %>
        </span>
        <div class="media-body">
          <div class="bold fs18"><%= cliente %></div>
          <% if (!isEmpty(cliente_email)) { %><div><%= cliente_email %></div><% } %>
          <% if (!isEmpty(cliente_telefono)) { %><div>Tel: <%= cliente_telefono %></div><% } %>
          <div><span class="bold"><%= fecha_realizacion %></span></div>
        </div>
      </div>                        
    </div>
    <div class="col-sm-8 bl col-xs-12 col-sm-height">
      <div class="media">
        <% if (id_tipo_estado == 1 || id_tipo_estado == 2) { %>
          <img class="thumb-sm pull-left" src="/sistema/resources/images/pedido.png"/>
          <div class="media-body">
            <div class="bold">NUEVA RESERVA</div>
            <div class="">
              Monto:
              <span class="consulta_precio text-danger">$ <%= total %></span>
            </div>
          </div>            
        <% } else { %>
          <% if (id_tipo_estado == 3 || id_tipo_estado == 0) { %>
            <img class="thumb-sm pull-left" src="/sistema/resources/images/pendiente-1.png"/>
          <% } else { %>
            <img class="thumb-sm pull-left" src="/sistema/resources/images/like-venta.png"/>
          <% } %>
          <% if (id_tipo_estado == 0) { %>
            <div class="media-body">
              <div class="bold">Tiene una compra en proceso!</div>
              <div class="">
                Monto:
                <span class="consulta_precio text-warning">$ <%= total %></span>
              </div>
            </div>
          <% } else { %>
            <div class="media-body">
              <div class="bold">FELICITACIONES! Realizaste una nueva venta!</div>
              <div class="">
                Monto:
                <span class="consulta_precio <%= (id_tipo_estado == 3) ? 'text-warning' : 'text-success' %>">$ <%= total %></span>
              </div>
            </div>
          <% } %>
        <% } %>
      </div>
      <div class="mt10">
        <% if (id_tipo_estado == 1) { %>
          <span class="bold">Estado: </span>Pendiente<br/>
          Comunicate con el cliente para finalizar la operaci&oacute;n
        <% } else if (id_tipo_estado == 0) { %>
          <span class="bold">Estado: </span>En Proceso<br/>
          Puedes comunicarte con el cliente para asesorarlo a finalizar la operaci&oacute;n
        <% } else if (id_tipo_estado == 2) { %>
          <span class="bold">Estado: </span>Autorizado<br/>
          El cliente podra finalizar el pedido.
        <% } else if (id_tipo_estado == 3) { %>
          <span class="bold">Estado: </span>Pendiente de Pago<br/>
          Solo resta que el cliente concrete el pago.
        <% } else { %>
        <?php /*
          <span class="bold">Medio de Pago: </span>MercadoPago<br/>
          <span class="bold">C&oacute;digo de Autorizaci&oacute;n: </span><%= codigo_autorizacion %><br/>
          */ ?>
        <% } %>
      </div>
      <div class="tar">
        <% if (id_tipo_estado == 1 || id_tipo_estado == 2) { %>
          <span class="fs14 mr10">En proceso</span>
          <i class="fa pr t5 fa-exclamation-circle text-danger fs26"></i>
        <% } else if (id_tipo_estado == 3) { %>
          <span class="fs14 mr10">Pendiente de pago</span>
          <i class="fa pr t5 fa-exclamation-circle text-warning fs26"></i>
        <% } else if (id_tipo_estado == 6 || id_tipo_estado == 5) { %>
          <span class="fs14 mr10">Finalizado</span>
          <i class="fa pr t5 fa-check-circle text-success fs26"></i>
        <% } %>
      </div>                        
    </div>
  </div>
</script>