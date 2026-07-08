<script type="text/template" id="alquileres_resultados_template">
<div class="seccion_llena">
  <% if (!seleccionar) { %>
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="glyphicon glyphicon-home icono_principal"></i>Alquileres
      / <b>Contratos</b>
      </h1>
    </div>
  <% } %>

  <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
    <div class="panel panel-default">

      <?php include("alquileres_menu.php") ?>

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="<% if (!seleccionar) { %>col-md-6 <% } else { %> col-xs-12 <% } %> sm-m-b">
            <div class="input-group">
              <input type="text" id="alquileres_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn-advanced-search m-l advanced-search-btn"><i class="fa fa-plus-circle"></i><span><?php echo lang(array("es"=>"M&aacute;s Filtros","en"=>"More Filters")); ?></span></button>
              </span>
            </div>
          </div>
          <% if (!seleccionar) { %>
            <div class="col-md-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#alquiler">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="display:none">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs"><i class="fa fa-filter"></i> Filtros:</h4>
          <div class="row pl10 pr10">
            <div class="col-md-2 col-sm-3 col-xs-12 mh50 pr5 pl5">
              <select class="form-control no-model" id="alquileres_buscar_estados">
                <option value="0">Estado</option>
                <option value="A">Activo</option>
                <option value="R">Reservado</option>
                <option value="C">Cancelado</option>
                <option value="F">Finalizado</option>
              </select>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 mh50 pr5 pl5">
              <input type="hidden" id="alquileres_buscar_id_propiedad" value="0" />
              <input type="text" class="form-control no-model" placeholder="Propiedad" id="alquileres_buscar_propiedades" />
            </div>
            <div class="col-md-2 col-sm-3 col-xs-12 mh50 pr5 pl5">
              <button id="alquileres_buscar_avanzada_btn" class="btn btn-dark btn-block"><i class="fa fa-search m-r-xs"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="alquileres_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
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
                <th class="sorting" data-sort-by="cliente">Inquilino</th>
                <th>Propiedad</th>
                <th>Direcci&oacute;n</th>
                <th class="sorting" data-sort-by="fecha_inicio">Desde</th>
                <th class="sorting" data-sort-by="fecha_fin">Hasta</th>
                <% if (!seleccionar) { %>
                  <th class="w50" style="width:10px;"></th>
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

<script type="text/template" id="alquileres_item_resultados_template">
    <% var clase = (estado=="A")?"":"text-danger"; %>
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
    <td class="<%= clase %> data"><span class="text-info"><%= cliente %></span></td>
    <td class="<%= clase %> data">
      <%= propiedad %> (Cod: <%= propiedad_codigo %>) <br/>
    </td>
    <td class="<%= clase %> data"><%= propiedad_direccion %></td>
    <td class="<%= clase %> data"><%= fecha_inicio %></td>
    <td class="<%= clase %> data"><%= fecha_fin %></td>
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <div class="btn-group dropdown">
          <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="rescindir" data-id="<%= id %>">Rescindir contrato</a></li>
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
          </ul>
        </div>
      </td>
    <% } %>
</script>


<script type="text/template" id="alquiler_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="glyphicon glyphicon-home icono_principal"></i>Alquileres
    / <b>Contratos</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Inquilino</label>
                <input type="text" value="<%= cliente %>" class="form-control no-model" id="alquiler_clientes">
              </div>
              <div class="form-group">
                <label class="control-label">Propiedad</label>
                <input type="text" value="<%= propiedad %>" class="form-control no-model" id="alquiler_propiedades">
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Contrato v&aacute;lido desde</label>
                    <div class="input-group">
                      <input type="text" placeholder="Desde" <%= (id == undefined)?"":"disabled" %> name="fecha_inicio" id="alquiler_fecha_inicio" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Hasta</label>
                    <div class="input-group">
                      <input type="text" placeholder="Hasta" <%= (id == undefined)?"":"disabled" %> name="fecha_fin" id="alquiler_fecha_fin" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Dia de Vencimiento</label>
                    <input type="number" min="1" max="31" id="alquiler_dia_vencimiento" value="<%= dia_vencimiento %>" class="no-model form-control"/>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <?php
                single_file_upload(array(
                  "name"=>"contrato",
                  "label"=>"Archivo adjunto del contrato",
                  "url"=>"/sistema/alquileres/function/save_file/",
                )); ?>
              </div>

              <div class="form-group">
                <div class="checkbox pt0">
                  <label class="i-checks">
                    <input type="checkbox" id="alquiler_enviar_recordatorios" <%= (enviar_recordatorios == 1)?"checked":"" %>><i></i> 
                    Enviar recordatorios al inquilino antes del vencimiento
                  </label>
                </div>                  
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="b-a table-responsive">
                <table id="cuotas_tabla" class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Nro. Cuota</th>
                      <th>Corresponde</th>
                      <th>Vence</th>
                      <th style="width:150px">Valor</th>
                      <th class="tac w50">Pago</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Expensas y servicios</label>
                <a id="expand_mapa" class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Agregue diferentes servicios que desea incluir en el cobro de la mensualidad.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" id="mapa_expandable">
            <div class="padder">
              <div class="row clearfix">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" class="form-control" id="expensa_nombre" />
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Monto ($)</label>
                    <div class="input-group">
                      <input id="expensa_monto" value="0" type="number" class="form-control"/>
                      <span class="input-group-btn">
                        <a id="expensa_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="">
                <table id="expensas_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Nombre</th>
                      <th>Monto</th>
                      <th class="w25"></th>
                      <th class="w25"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< expensas.length;i++) { %>
                      <% var p = expensas[i] %>
                      <tr data-id="<%= p.id %>">
                        <td><%= p.nombre %></td>
                        <td><%= p.monto %></td>
                        <td><i class='fa fa-pencil cp editar_expensa'></i></td>
                        <td><i class='fa fa-times eliminar_expensa text-danger cp'></i></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

        <% if (edicion) { %>
          <button class="btn btn-success guardar">Guardar</button>
        <% } %>             

      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="rescindir_alquiler_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    Rescindir contrato de alquiler
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-horizontal">
      <div class="form-group">
        <div class="col-md-6 col-xs-12">
          <label>Fecha de cancelacion:</label>
          <input type="text" name="fecha_cancelacion_contrato" placeholder="Fecha" id="rescindir_alquier_fecha" class="form-control"/>
        </div>
      </div>      
      <div class="form-group">
        <div class="col-xs-12">
          <label>Motivo: </label>
          <textarea name="motivo_cancelacion_contrato" id="rescindir_alquiler_motivo_cancelacion_contrato" placeholder="Escribe aqui alguna observacion..." class="h100 form-control"><%= motivo_cancelacion_contrato %></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success">Guardar</button>
  </div>  
</div>
     
</script>


<script type="text/template" id="recibos_alquileres_resultados_template">
<div>
  <% if (!seleccionar) { %>
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="glyphicon glyphicon-home icono_principal"></i>Alquileres
      / <b>Cobranzas</b>
      </h1>
    </div>
  <% } %>
  <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
    <div class="panel panel-default">

      <?php include("alquileres_menu.php") ?>

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-xs-12 sm-m-b">
            <div class="form-inline">
              <div class="input-group">
                <input type="text" id="recibos_alquileres_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              </div>
              <select class="form-control" style="width: 120px; display: inline-block" id="recibos_alquileres_meses">
                <option <?php echo(date("m")==1)?"selected":"" ?>>Enero</option>
                <option <?php echo(date("m")==2)?"selected":"" ?>>Febrero</option>
                <option <?php echo(date("m")==3)?"selected":"" ?>>Marzo</option>
                <option <?php echo(date("m")==4)?"selected":"" ?>>Abril</option>
                <option <?php echo(date("m")==5)?"selected":"" ?>>Mayo</option>
                <option <?php echo(date("m")==6)?"selected":"" ?>>Junio</option>
                <option <?php echo(date("m")==7)?"selected":"" ?>>Julio</option>
                <option <?php echo(date("m")==8)?"selected":"" ?>>Agosto</option>
                <option <?php echo(date("m")==9)?"selected":"" ?>>Septiembre</option>
                <option <?php echo(date("m")==10)?"selected":"" ?>>Octubre</option>
                <option <?php echo(date("m")==11)?"selected":"" ?>>Noviembre</option>
                <option <?php echo(date("m")==12)?"selected":"" ?>>Diciembre</option>
              </select>
              <input type="text" id="recibos_alquileres_anio" class="form-control" style="display: inline-block; width: 65px; " value="<?php echo date("Y")?>" />
              <?php /*
              <select class="form-control" style="width: 120px; display: inline-block" id="recibos_alquileres_estado">
                <option value="0">Adeudados</option>
                <option value="1">Pagados</option>
                <option value="-1">Todos</option>
              </select>
              */ ?>
              <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="recibos_alquileres_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
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
                <th class="sorting" data-sort-by="cliente">Inquilino</th>
                <th class="sorting" data-sort-by="propiedad">Propiedad</th>
                <th class="sorting" data-sort-by="monto">Alquiler</th>
                <?php /*
                <th class="sorting" data-sort-by="expensa">Tasas/Serv.</th>
                <th class="sorting" data-sort-by="total">Total</th>
                <th class="sorting" data-sort-by="pago">Pago</th>
                */ ?>
                <th class="sorting" data-sort-by="vencimiento">Venc. Cuota</th>
                <% if (!seleccionar) { %>
                  <th></th>
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

<script type="text/template" id="recibos_alquileres_item_resultados_template">
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
    <td class="<%= clase %> data"><span class="text-info"><%= cliente %></span></td>
    <td class="<%= clase %> data"><%= propiedad %><br/><%= direccion %></td>
    <td class="<%= clase %> data">$ <%= monto %></td>
    <td class="<%= clase %> data"><%= vencimiento %></td>
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <% if (pagada == 0) { %>
          <button class="btn btn-danger btn-addon btn-addon2 agregar_pago"><i class="fa fa-dollar"></i>Cobrar</button>
          <button class="btn btn-success enviar_whatsapp"><i class="fa fa-whatsapp"></i></button>
          <button class="btn btn-default imprimir_cupon_pago"><i class="fa fa-print"></i></button>
        <% } else { %>
          <button class="btn btn-default imprimir"><i class="fa fa-print"></i></button>
        <% } %>
        <div class="btn-group dropdown">
          <i title="Opciones" class="iconito fa ml15 mt5 fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="ver_contrato" data-id="<%= id %>">Ver contrato</a></li>
            <% if (pagada == 1) { %>
              <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
            <% } %>
          </ul>
        </div>
      </td>
    <% } %>
</script>

<?php /*
<script type="text/template" id="alquiler_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="glyphicon glyphicon-home icono_principal"></i>Alquileres
    / <b><%= (id == undefined)?"Nuevo":"Editar" %></b>
  </h1>
</div>
<div class="wrapper-md">
    <div class="centrado rform">
        <div class="row">
            <div class="col-md-4">
                <div class="detalle_texto">Datos del contrato de alquiler</div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="padder">
                          <div class="form-group">
                              <label class="control-label">Cliente</label>
                              <input type="text" value="<%= cliente %>" class="form-control no-model" id="alquiler_clientes">
                          </div>
                          <div class="form-group">
                              <label class="control-label">Propiedad</label>
                              <input type="text" value="<%= propiedad %>" class="form-control no-model" id="alquiler_propiedades">
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Contrato v&aacute;lido desde</label>
                                <div class="input-group">
                                  <input type="text" placeholder="Desde" name="fecha_inicio" id="alquiler_fecha_inicio" class="form-control"/>
                                  <span class="input-group-btn">
                                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                                  </span>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Hasta</label>
                                <div class="input-group">
                                  <input type="text" placeholder="Hasta" name="fecha_fin" id="alquiler_fecha_fin" class="form-control"/>
                                  <span class="input-group-btn">
                                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <?php
                            single_file_upload(array(
                              "name"=>"contrato",
                              "label"=>"Archivo adjunto del contrato",
                              "url"=>"/sistema/recibos_alquileres/function/save_file/",
                            )); ?>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="detalle_texto">Gesti&oacute;n de Cuotas</div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="padder">
                          <div class="b-a table-responsive">
                            <table id="cuotas_tabla" class="table table-small table-striped sortable m-b-none default footable">
                              <thead>
                                <tr>
                                  <th>Nro. Cuota</th>
                                  <th>Corresponde</th>
                                  <th>Vence</th>
                                  <th style="width:150px">Valor</th>
                                  <th class="tac w50">Pago</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                            </table>
                          </div>

                          <div class="h4 m-t-lg">Totales</div>
                          <div class="line b-b m-b"></div>

                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="control-label">Total contrato</label>
                                <input type="text" disabled="" class="form-control no-model" id="alquiler_total_adeudado">
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="control-label">Pago</label>
                                <input type="text" disabled="" class="form-control no-model" id="alquiler_total_adeudado">
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="control-label">Adeudada</label>
                                <input type="text" disabled="" class="form-control no-model" id="alquiler_total_adeudado">
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <a class="btn btn-default" href="app/#cuentas_corrientes_clientes/<%= id_cliente %>">Ver cuenta corriente del cliente</a>
                          </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="detalle_texto">Configuraci&oacute;n</div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="padder">

                          <div class="form-group">
                              <div class="checkbox pt0">
                                <label class="i-checks">
                                  <input type="checkbox" id="alquiler_enviar_recordatorios" <%= (enviar_recordatorios == 1)?"checked":"" %>><i></i> 
                                  Enviar recordatorios al inquilino antes del vencimiento
                                </label>
                              </div>                  
                          </div>
                          <div class="form-group">
                              <div class="form-inline">
                                <label>Fecha de vencimiento del alquiler</label>
                                <input type="number" min="1" max="31" id="alquiler_dia_vencimiento" value="<%= dia_vencimiento %>" class="m-l no-model form-control w75"/>
                                <select id="alquiler_mes_vencimiento" class="m-l no-model form-control w150">
                                  <option <%= (mes_vencimiento == "A")?"selected":"" %> value="A">Mes corriente</option>
                                  <option <%= (mes_vencimiento == "P")?"selected":"" %> value="P">Mes pr&oacute;ximo</option>
                                </select>                      
                              </div>                  
                          </div>
                          <div class="form-group">
                              <div class="form-inline">
                                <label>Tipo de recordatorio</label>
                                <select id="alquiler_tipo_facturacion" class="m-l no-model form-control w150">
                                  <option <%= (tipo_facturacion == "R")?"selected":"" %> value="R">Recibo</option>
                                  <option <%= (tipo_facturacion == "F")?"selected":"" %> value="F">Factura electr&oacute;nica</option>
                                </select>
                              </div>                  
                          </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-8">
              <% if (edicion) { %>
                <button class="btn btn-success guardar">Guardar</button>
                <img src="/sistema/resources/images/ajax-loader.gif" class="img_loading"/>
              <% } %>             
            </div>
        </div>

    </div>
</div>
</script>


<script type="text/template" id="rescindir_alquiler_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    Rescindir contrato de alquiler
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-horizontal">
      <div class="form-group">
        <div class="col-md-6 col-xs-12">
          <label>Fecha de cancelacion:</label>
          <input type="text" name="fecha_cancelacion_contrato" placeholder="Fecha" id="rescindir_alquier_fecha" class="form-control"/>
        </div>
      </div>      
      <div class="form-group">
        <div class="col-xs-12">
          <label>Motivo: </label>
          <textarea name="motivo_cancelacion_contrato" id="rescindir_alquiler_motivo_cancelacion_contrato" placeholder="Escribe aqui alguna observacion..." class="h100 form-control"><%= motivo_cancelacion_contrato %></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success">Guardar</button>
  </div>  
</div>
     
</script>

*/ ?>