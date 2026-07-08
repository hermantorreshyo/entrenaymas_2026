<div class="centrado rform">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="padder">

            <div class="form-group">
              <label class="control-label">Cliente</label>
              <div class="input-group">
                <select id="viaje_clientes" style="width: 100%" class="form-control"></select>
                <div class="input-group-btn">
                  <button type="button" class="btn btn-info nuevo_cliente">Agregar</button>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Desde</label>
                  <input type="text" id="viaje_custom_1" <%= (!edicion)?"disabled":"" %> class="form-control" value="<%= custom_1 %>" name="custom_1"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Hasta</label>
                  <input type="text" id="viaje_custom_2" <%= (!edicion)?"disabled":"" %> class="form-control" value="<%= custom_2 %>" name="custom_2"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Precio Base</label>
                  <input type="text" id="viaje_precio" <%= (!edicion)?"disabled":"" %> class="form-control" value="<%= precio %>" name="precio"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Estado</label>
                  <select class="form-control" name="estado">
                    <option value="0" <%= (estado==0)?"selected":"" %>>Pendiente</option>
                    <option value="1" <%= (estado==1)?"selected":"" %>>Realizado</option>
                  </select>
                </div>
              </div>
            </div>        

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Direccion de Salida</label>
                  <input type="text" id="viaje_custom_3" <%= (!edicion)?"disabled":"" %> class="form-control" value="<%= custom_3 %>" name="custom_3"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Direccion de Llegada</label>
                  <input type="text" id="viaje_custom_4" <%= (!edicion)?"disabled":"" %> class="form-control" value="<%= custom_4 %>" name="custom_4"/>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Fecha de salida</label>
                  <div class="input-group">
                    <input type="text" id="viaje_fecha" <%= (!edicion)?"disabled":"" %> name="fecha" class="form-control">
                    <span class="input-group-btn">
                      <button tabindex="-1" <%= (!edicion)?"disabled":"" %> type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>              
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Hora</label>
                  <input type="text" id="viaje_custom_6" <%= (!edicion)?"disabled":"" %> class="form-control" value="<%= custom_6 %>" name="custom_6"/>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Fecha de regreso</label>
                  <div class="input-group">
                    <input type="text" id="viaje_fecha_llegada" <%= (!edicion)?"disabled":"" %> name="fecha_llegada" class="form-control">
                    <span class="input-group-btn">
                      <button tabindex="-1" <%= (!edicion)?"disabled":"" %> type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>              
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Hora</label>
                  <input type="text" id="viaje_custom_7" <%= (!edicion)?"disabled":"" %> class="form-control" value="<%= custom_7 %>" name="custom_7"/>
                </div>
              </div>

            </div>

            <div class="form-group">
              <label class="control-label">Programacion del viaje</label>
              <textarea name="texto" <%= (!edicion)?"disabled":"" %> class="form-control" id="viaje_texto"><%= texto %></textarea>
            </div>

          </div>
        </div>
      </div>

      <% if (edicion) { %>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Veh&iacute;culos y tripulantes",
                    "en"=>"Veh&iacute;culos y tripulantes",
                  )); ?>
                </label>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Configure con cu&aacute;les veh&iacute;culos realizar&aacute; el viaje y las personas encargadas del mismo.",
                    "en"=>"Configure con cuales vehiculos realizara el viaje y las personas encargadas del mismo.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="display: block">
            <div class="padder">
              <div class="row clearfix">
                <div class="form-group col-xs-12 col-sm-3">
                  <label class="control-label">Veh&iacute;culo</label>
                  <select id="viaje_vehiculos" style="width: 100%" <%= (!edicion)?"disabled":"" %> class="form-control no-model"></select>
                </div>
                <div class="form-group col-xs-9 col-sm-4">
                  <label class="control-label">Tripulante</label>
                  <select id="viaje_tripulantes" style="width: 100%" <%= (!edicion)?"disabled":"" %> class="form-control no-model"></select>
                </div>
                <div class="form-group col-xs-9 col-sm-2">
                  <label class="control-label">% Comision</label>
                  <input type="text" class="form-control no-model" <%= (!edicion)?"disabled":"" %> id="viaje_vehiculos_comision" />
                </div>
                <div class="form-group col-xs-3 col-sm-2">
                  <label class="control-label db">&nbsp;</label>
                  <a id="vehiculo_agregar" <%= (!edicion)?"disabled":"" %> class="btn btn-block btn-info">Agregar</a>
                </div>
              </div>
              <div class="">
                <table id="vehiculos_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Vehiculo</th>
                      <th>Tripulante</th>
                      <th>%</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< vehiculos_tripulantes.length;i++) { %>
                      <% var p = vehiculos_tripulantes[i] %>
                      <tr id="fila_<%= p.id_vehiculo %>_<%= p.id_tripulante %>" data-id_vehiculo="<%= p.id_vehiculo %>" data-comision="<%= p.comision %>" data-id_tripulante="<%= p.id_tripulante %>">
                        <td><%= p.vehiculo %></td>
                        <td><%= p.tripulante %></td>
                        <td><%= Number(p.comision).toFixed(2) %></td>
                        <td>
                          <% if (edicion) { %>
                            <i class='glyphicon glyphicon-remove eliminar_vehiculo text-danger cp'></i>
                          <% } %>
                        </td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      <% } %>

    </div>
  </div>

  <div class="line b-b m-b-lg"></div>

  <div class="row">
    <div class="col-md-10 col-md-offset-1 tar oh">
      <button class="btn atras btn-default fl">Atras</button>
      <button class="btn guardar btn-success">Guardar</button>
    </div>
  </div>

</div>
