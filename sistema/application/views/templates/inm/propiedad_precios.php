<div id="propiedad_precios" class="panel panel-default" style="<%= (id_tipo_operacion!=3)?"display:block":"display:none" %>">
  <div class="panel-body">
    <div class="padder">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Precio final</label>
            <div class="input-group">
              <div class="input-group-btn">
                <select id="propiedad_monedas" class="form-control w100" name="moneda">
                  <% for(var i=0;i< window.monedas.length;i++) { %>
                    <% var o = monedas[i]; %>
                    <option <%= (o.signo == moneda)?"selected":"" %> value="<%= o.signo %>"><%= o.signo %></option>
                  <% } %>
                </select>                
              </div>
              <input id="propiedad_precio_final" value="<%= precio_final %>" type="number" class="form-control number" name="precio_final"/>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <div class="m-l pt0 mt5 checkbox">
              <label class="i-checks">
                <input name="publica_precio" id="propiedad_publica_precio" value="1" type="checkbox" <%= (publica_precio == 1) ? "checked" : "" %>><i></i> 
                Mostrar precio en web
              </label>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Valor Expensas</label>
            <input id="propiedad_valor_expensas" value="<%= valor_expensas %>" type="number" class="form-control number" name="valor_expensas"/>
          </div>
        </div>

      </div>
      <div class="form-group <%= (ID_EMPRESA == 685)?"dn":"" %>">
        <div class="pt0 mt5 checkbox">
          <label class="i-checks">
            <input name="apto_banco" id="propiedad_apto_banco" value="1" type="checkbox" <%= (apto_banco == 1) ? "checked" : "" %>><i></i> 
            La propiedad es apta para cr&eacute;dito bancario.
          </label>
        </div>
      </div>
      <div class="form-group <%= (ID_EMPRESA == 685)?"dn":"" %>">
        <div class="pt0 mt5 checkbox">
          <label class="i-checks">
            <input name="acepta_permuta" id="propiedad_acepta_permuta" value="1" type="checkbox" <%= (acepta_permuta == 1) ? "checked" : "" %>><i></i> 
            El propietario acepta permuta por la propiedad.
          </label>
        </div>
      </div>

    </div>
  </div>
</div>


<div id="propiedad_capacidad" class="panel panel-default" style="<%= (id_tipo_operacion==3)?"display:block":"display:none" %>">
  <div class="panel-body">
    <div class="padder">
      <div class="form-group mb0 clearfix">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Capacidad",
            "en"=>"Capacity",
          )); ?>
        </label>
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-md-4">
            <span class="db mb5">Mayores</span>
            <input type="number" min="0" id="propiedad_capacidad_maxima" class="form-control" value="<%= capacidad_maxima %>" name="capacidad_maxima"/>
          </div>
          <div class="col-md-4">
            <span class="db mb5">Menores</span>
            <input type="number" min="0" id="propiedad_capacidad_maxima_menores" class="form-control" value="<%= capacidad_maxima_menores %>" name="capacidad_maxima_menores"/>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-md-4">
            <span class="db mb5">Minimo dias de reserva</span>
            <input type="number" min="0" id="propiedad_alq_minimo_dias_reserva" class="form-control" value="<%= alq_minimo_dias_reserva %>" name="alq_minimo_dias_reserva"/>
          </div>
          <div class="col-md-4">
            <span class="db mb5">Dias previos para reserva</span>
            <input type="number" min="0" id="propiedad_alq_reservar_dias_antes" class="form-control" value="<%= alq_reservar_dias_antes %>" name="alq_reservar_dias_antes"/>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="checkbox">
          <label class="i-checks">
            <input name="habitacion_compartida" id="propiedad_habitacion_compartida" value="1" type="checkbox" <%= (habitacion_compartida == 1) ? "checked" : "" %>><i></i> 
            La habitacion es compartida con otras personas.
          </label>
        </div>
      </div>
      <div class="form-group">
        <div class="oh w100p">
          <label class="control-label">Links para sincronizacion de calendarios</label>
          <a href="https://www.varcreative.com/sistema/propiedades/function/exportar_calendario/<%= ID_EMPRESA %>/" target="_blank" class="text-info link fr cp ml15">URL Calendario</a>
          <span id="propiedad_sincronizar_calendario" class="text-info link fr cp ml15">Sincronizar</span>
        </div>
        <textarea id="propiedad_links_ical" class="form-control" name="links_ical"><%= links_ical %></textarea>
        <span class="text-muted">Nota: Un link por cada linea.</span>
      </div>      
    </div>
  </div>
</div>

<div id="propiedad_precios_temporal" class="panel panel-default" style="<%= (id_tipo_operacion==3)?"display:block":"display:none" %>">
  <div class="panel-body">
    <div class="padder">
      <div class="form-group mb0 clearfix">
        <label class="control-label">Tarifa base</label>
      </div>      
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            <label class="control-label no-bold">Estadia Min.</label>
            <input id="propiedad_alq_minimo_dias_reserva" value="<%= alq_minimo_dias_reserva %>" type="text" class="form-control number" name="alq_minimo_dias_reserva"/>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label class="control-label no-bold">Moneda</label>
            <select id="propiedad_precios_temporal_monedas" class="form-control" name="moneda">
              <% for(var i=0;i< window.monedas.length;i++) { %>
                <% var o = monedas[i]; %>
                <option <%= (o.signo == moneda)?"selected":"" %> value="<%= o.signo %>"><%= o.signo %></option>
              <% } %>
            </select>              
          </div>
        </div>
        <div class="col-md-8">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label no-bold">Por Noche</label>
                <input id="propiedad_precios_temporal_precio_final" value="<%= precio_final %>" type="text" class="form-control number" name="precio_final"/>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label no-bold">Fin de Semana</label>
                <input id="propiedad_precios_temporal_alq_tarifa_base_finde" value="<%= alq_tarifa_base_finde %>" type="text" class="form-control number" name="alq_tarifa_base_finde"/>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label no-bold">Por Semana</label>
                <input id="propiedad_precios_temporal_alq_tarifa_base_semana" value="<%= alq_tarifa_base_semana %>" type="text" class="form-control number" name="alq_tarifa_base_semana"/>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label no-bold">Por Mes</label>
                <input id="propiedad_precios_temporal_alq_tarifa_base_mes" value="<%= alq_tarifa_base_mes %>" type="text" class="form-control number" name="alq_tarifa_base_mes"/>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body expand" style="display:block">

    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#tab_propiedad_precio1" role="tab" data-toggle="tab">Precios por temporada</a>
        </li>
        <li>
          <a href="#tab_propiedad_precio3" role="tab" data-toggle="tab">Descuentos</a>
        </li>
        <li>
          <a href="#tab_propiedad_precio2" role="tab" data-toggle="tab">Impuestos y tasas</a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="tab_propiedad_precio1" class="tab-pane active">
          <div class="padder">
            <p class="text-muted">
              Los precios por temporada sobreescriben a la tarifa base.
            </p>            
            <div class="m-b clearfix">
              <button id="propiedad_temporada_nuevo" class="btn btn-info">Agregar tarifa por temporada</button>
            </div>
            <div class="">
              <table id="propiedad_temporada_tabla" class="table m-b-none default footable">
                <thead>
                  <tr>
                    <th>Temporada</th>
                    <th>Estadia Min.</th>
                    <th>Noche</th>
                    <th>Fin de Semana</th>
                    <th>Semana</th>
                    <th>Mes</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var i=0;i< temporada.length;i++) { %>
                    <% var p = temporada[i] %>
                    <tr>
                      <td>
                        <span class="nombre text-info"><%= p.nombre %></span><br/>
                        <span class="desde"><%= p.fecha_desde %></span> -
                        <span class="hasta"><%= p.fecha_hasta %></span>
                      </td>
                      <td><span class="minimo_dias_reserva"><%= p.minimo_dias_reserva %></span> noches</td>
                      <td><span class="precio"><%= p.precio %></span></td>
                      <td><span class="precio_finde"><%= p.precio_finde %></span></td>
                      <td><span class="precio_semana"><%= p.precio_semana %></span></td>
                      <td><span class="precio_mes"><%= p.precio_mes %></span></td>
                      <td><button class="btn btn-white editar_precio mr5"><i class="fa fa-pencil"></i></button><button class="btn btn-white eliminar_precio"><i class="fa fa-trash"></i></button></td>
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div id="tab_propiedad_precio2" class="tab-pane">
          <div class="padder">
            <p class="text-muted">
              Aplique diferentes impuestos y tasas que se sumaran al precio calculado.
            </p>
            <div class="form-inline m-b clearfix">
              <button id="propiedad_impuesto_nuevo" class="btn btn-info">Agregar impuesto o tasa</button>
            </div>
            <div class="">
              <table id="propiedad_impuestos_tabla" class="table m-b-none default footable">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var i=0;i< impuestos.length;i++) { %>
                    <% var p = impuestos[i] %>
                    <tr>
                      <td>
                        <span class="nombre text-info"><%= p.nombre %></span>
                      </td>
                      <td>
                        <%= (p.tipo==1)?"Porcentaje por reserva":"" %>
                        <%= (p.tipo==2)?"Tarifa por viajero":"" %>
                        <%= (p.tipo==3)?"Tarifa por persona y noche":"" %>
                        <%= (p.tipo==4)?"Tarifa por noche":"" %>
                        <%= (p.tipo==5)?"Precio fijo por estadia":"" %>
                        <span class="tipo dn"><%= p.tipo %></span>
                      </td>
                      <td><span class="monto"><%= p.monto %></span></td>
                      <td><button class="btn btn-white editar_impuesto mr5"><i class="fa fa-pencil"></i></button><button class="btn btn-white eliminar_impuesto"><i class="fa fa-trash"></i></button></td>
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div id="tab_propiedad_precio3" class="tab-pane">
          <div class="padder">
            <p class="text-muted">
              Los descuentos se aplican sobre el precio final (sea por tarifa base o temporada).
            </p>            
            <div class="m-b clearfix">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Descuento Ultima Hora (%)</label>
                    <input type="text" id="propiedad_alq_descuento_ultima_hora" name="alq_descuento_ultima_hora" value="<%= alq_descuento_ultima_hora %>" class="form-control">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Antes de: (Hs.)</label>
                    <input type="text" id="propiedad_alq_ultima_hora_cantidad" name="alq_ultima_hora_cantidad" value="<%= alq_ultima_hora_cantidad %>" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Descuento Venta Anticipada (%)</label>
                    <input type="text" id="propiedad_alq_descuento_por_anticipado" name="alq_descuento_por_anticipado" value="<%= alq_descuento_por_anticipado %>" class="form-control">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Antes de: (dias)</label>
                    <input type="text" id="propiedad_alq_descuento_por_anticipado_cantidad" name="alq_descuento_por_anticipado_cantidad" value="<%= alq_descuento_por_anticipado_cantidad %>" class="form-control">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>