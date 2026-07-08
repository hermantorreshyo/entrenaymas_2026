<script type="text/template" id="via_comisiones_vendedores_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>Ventas / 
    <b>Comisiones de vendedores</b>
  </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-xs-12 sm-m-b">
            <select style="float:left; width: 200px; display: inline-block" class="form-control" id="via_comisiones_vendedores_vendedores">
              <option value="0">Vendedor</option>
              <% for(var i=0;i< vendedores.length;i++) { %>
                <% var o = vendedores[i]; %>
                <option value="<%= o.id %>"><%= o.nombre %></option>
              <% } %>
            </select>
            <div class="fl w180">
              <div class="input-group">
                <input type="text" title="Desde" id="via_comisiones_vendedores_fecha_desde" class="form-control no-model">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <div class="fl w180">
              <div class="input-group">
                <input type="text" title="Hasta" id="via_comisiones_vendedores_fecha_hasta" class="form-control no-model">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <button class="btn btn-default fl buscar"><i class="fa fa-search"></i></button>
            <button class="btn btn-info fr agregar_pago">Agregar pago</button>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="clearfix m-b">
          <div class="fl">
            <span class="m-r">Saldo Inicial: </span>
            <input type="text" disabled class="form-control w100 dib" id="via_comisiones_vendedores_saldo_inicial" value="0.00"/>
          </div>
        </div>
        <div class="b-a table-responsive">
          <table id="via_comisiones_vendedores_tabla" class="table table-small sortable m-b-none default">
            <thead class="thead">
              <th class="">Viaje</th>
              <th class="">Cliente</th>
              <th class="">Habitacion</th>
              <th class="">Fecha</th>
              <th class="tar th_acciones">Precio</th>
              <th class="tar">Adicionales</th>
              <th class="tar">Comisi&oacute;n</th>
              <th class="w30"></th>
              <th class="tar">Resto</th>
              <th class="tar th_acciones">Saldo</th>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="via_comisiones_vendedores_item_resultados_template">
  <!-- ES UN PAGO -->
  <% if (id_reserva == 0) { %>
    <td colspan="3">Pago</td>
    <td class=""><%= fecha_mov %></td>
    <td class="tar">$ <%= Number(total).toFixed(2) %></td>
    <td class="tar"></td>
    <td class="tar"></td>
    <td class="tar"></td>
    <td class="tar"></td>
    <td class="tar">$ <%= Number(saldo).toFixed(2) %></td>
  <!-- COMISION DE VENTA -->
  <% } else { %>
    <td class=""><%= viaje %></td>
    <td class=""><%= nombre %></td>
    <td class="">
      <%= (tipo_habitacion==1)?"SINGLE":"" %>
      <%= (tipo_habitacion==2)?"MAT":"" %>
      <%= (tipo_habitacion==3)?"DOBLE":"" %>
      <%= (tipo_habitacion==4)?"MAT+1":"" %>
      <%= (tipo_habitacion==5)?"TRIPLE":"" %>
      <%= (tipo_habitacion==6)?"X4":"" %>
      <%= (tipo_habitacion==9)?"MAT+2":"" %>
      <%= (tipo_habitacion==10)?"X5":"" %>
      <%= (tipo_habitacion==11)?"MAT+3":"" %>
      <%= (tipo_habitacion==12)?"X6":"" %>
      <%= (tipo_habitacion==7)?"SOLO A COMPARTIR":"" %>
      <%= (tipo_habitacion==8)?"SOLA A COMPARTIR":"" %>
    </td>
    <td class=""><%= fecha_mov %></td>
    <td class="tar">$ <%= Number(total).toFixed(2) %></td>
    <td class="tar">$ <%= Number(adicionales).toFixed(2) %></td>
    <td class="tar">$ <%= Number(comision_vendedor).toFixed(2) %></td>
    <td><i data-id="<%= id %>" class="fa fa-pencil editar_comision"></i></td>
    <td class="tar">$ <%= Number(total - comision_vendedor).toFixed(2) %></td>
    <td class="tar">$ <%= Number(saldo).toFixed(2) %></td>
  <% } %>
</script>