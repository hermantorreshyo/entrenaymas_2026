<script type="text/template" id="reparacion_edit_panel_template">

  <div class="panel panel-default">
    <div class="panel-heading fs16 bold">
      Solicitud de Reparaci&oacute;n #1
      <i class="fa fa-times cerrar cp fr"></i>
    </div>
    <div class="panel-body">
      <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
            <a href="#reparacion_tab1" role="tab" data-toggle="tab">
              <i class="fa text-warning fa-calendar m-r-xs"></i>
              Datos
            </a>
          </li>
          <li>
            <a href="#reparacion_tab2" role="tab" data-toggle="tab">
              <i class="fa text-info fa-address-book m-r-xs"></i>
              Fallas
            </a>
          </li>
          <li>
            <a href="#reparacion_tab3" role="tab" data-toggle="tab">
              <i class="fa text-success fa-file-text m-r-xs"></i>
              Reparaci&oacute;n
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="reparacion_tab1" class="tab-pane active">

            <div class="clearfix">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Tipo de servicio:</label>
                    <select class="form-control" name="domicilio">
                      <option value="0" <%= (domicilio==0)?"selected":"" %>>En local</option>
                      <option value="1" <%= (domicilio==1)?"selected":"" %>>A domicilio</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Fecha:</label>
                    <div class="input-group">
                      <input type="text" title="Fecha de emision de comprobante" id="reparacion_fecha" name="fecha" class="form-control action">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Estado:</label>
                    <select class="form-control" id="reparacion_estados" name="id_estado">
                      <option <%= (id_estado == 0 ? "selected":"") %> value="0">Pendiente</option>
                      <option <%= (id_estado == 1 ? "selected":"") %> value="1">Realizada</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  <div class="form-group">
                    <label>Cliente:</label>
                    <div class="input-group">
                      <input type="text" class="dn" id="reparacion_id_cliente" value="<%= id_cliente %>"/>
                      <input title="Ingrese el c&oacute;digo de Cliente o comience a escribir parte del nombre. (0 = Consumidor Final)" type="text" class="form-control action no-model" id="reparacion_codigo_cliente" placeholder="Nombre o codigo de cliente" value="<%= cliente.nombre %>"/>
                      <span class="input-group-btn">
                        <button title="Atajo: F2 = Buscar" tabindex="-1" id="reparacion_buscar_cliente" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                        <button title="Crear nuevo cliente" tabindex="-1" id="reparacion_nuevo_cliente" class="btn btn-default" type="button"><i class="fa fa-plus"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Direcci&oacute;n:</label>
                    <input type="text" class="form-control no-model" name="direccion" id="reparacion_direccion"/>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Tel&eacute;fono:</label>
                    <input type="text" class="form-control no-model" name="localidad" id="reparacion_localidad"/>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Solicitado por:</label>
                    <input type="text" class="form-control no-model" name="solicitado_por" id="reparacion_solicitado_por"/>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Observaciones de ingreso:</label>
                <textarea class="form-control h100" id="reparacion_observaciones_equipo" name="observaciones_equipo"><%= observaciones_equipo %></textarea>
              </div>
            </div>
          </div>
          <div id="reparacion_tab2" class="tab-pane">

            <div class="form-group">
              <label>Falla declarada:</label>
              <textarea class="form-control h100" id="reparacion_falla_declarada" name="falla_declarada"><%= falla_declarada %></textarea>
            </div>
            <div class="form-group">
              <label>Requerimientos del cliente:</label>
              <textarea class="form-control h100" id="reparacion_requerimientos_cliente" name="requerimientos_cliente"><%= requerimientos_cliente %></textarea>
            </div>
            <div class="form-group">
              <label>Diagn&oacute;stico:</label>
              <textarea class="form-control h100" id="reparacion_diagnostico" name="diagnostico"><%= diagnostico %></textarea>
            </div>
            
          </div>
          <div id="reparacion_tab3" class="tab-pane">

            <div class="clearfix">
              <input type="hidden" id="reparacion_id_articulo"/>
              <div class="col-sm-3 p0">
                <label class="text-muted">C&oacute;digo</label>
                <div class="input-group">
                  <input type="text" class="form-control action no-model" id="reparacion_codigo_articulo"/>
                  <span class="input-group-btn">
                    <button tabindex="-1" id="reparacion_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
              <div class="col-sm-4 p0">
                <label class="text-muted">Descripci&oacute;n</label>
                <input disabled type="text" class="form-control action no-model" id="reparacion_item_nombre"/>
              </div>
              <div class="col-sm-2 p0">
                <label class="text-muted">Cant.</label>
                <input type="text" class="form-control action no-model" value="1" id="reparacion_item_cantidad"/>
              </div>
              <div class="col-sm-3 p0">
                <label class="text-muted">P. Venta</label>                  
                <div class="input-group">
                  <input type="text" class="form-control no-model" id="reparacion_precio_final"/>
                  <span class="input-group-btn">
                    <button title="Ingresar linea" id="reparacion_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>

            <div class="b-a" style="overflow: auto; margin-top: 15px;">
              <table id="tabla_items" class="table table-small sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th>Cod.</th>
                    <th class="w75">Cant.</th>
                    <th>Descripci&oacute;n</th>
                    <th class="w100">Unit.</th>
                    <th class="w100">Subtotal</th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot class="bg-important">
                  <tr>
                    <td colspan="4"></td>
                    <td class="bold" id="reparacion_total"></td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div class="row mt15">
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Fecha de Entrega:</label>
                  <div class="input-group">
                    <input type="text" id="reparacion_fecha_entrega" name="fecha_entrega" class="form-control action">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-8">
                <div class="form-group">
                  <label>Observaciones:</label>
                  <input type="text" class="form-control" id="reparacion_observaciones" name="observaciones" value="<%= observaciones %>"/>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer tar">
      <button class="btn btn-success guardar btn-addon"><i class="icon fa fa-plus"></i>Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="reparacion_item_tabla_template">
  <td class="editar"><%= codigo %></td>
  <td class="editar"><%= cantidad %></td>
  <td class="editar"><span class="text-info"><%= nombre %></span></td>
  <td class="editar"><%= Number(precio_final).toFixed(2) %></td>
  <td class="editar"><%= Number(total).toFixed(2) %></td>
  <td class="w25 p5">
    <i title="Eliminar" class="glyphicon glyphicon-remove eliminar_flechita text-danger" />
  </td>
</script>

<script type="text/template" id="reparaciones_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Reparaciones</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="reparaciones_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="w100 sorting" data-sort-by="numero">Numero</th>
                <th class="sorting" data-sort-by="fecha">Fecha</th>
                <th class="sorting">Cliente</th>
                <th class="w120 sorting" data-sort-by="total">Total</th>
                <th class="w120">Estado</th>
                <% if (permiso > 1) { %>
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
  </div>  
</script>


<script type="text/template" id="reparaciones_item">
  <td class="ver"><%= numero %></td>
  <td class="ver"><%= fecha %></td>
  <td class="ver"><span class="text-info"><%= cliente %></span></td>
  <td class="ver">$ <%= Number(total).toFixed(2) %></td>
  <td class="ver"><%= estado %></td>
  <% if (permiso > 1) { %>
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
