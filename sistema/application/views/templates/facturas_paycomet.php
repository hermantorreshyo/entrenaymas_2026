<script type="text/template" id="facturas_paycomet_table_view">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> <b>Facturación</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row pl10 pr10">   
          <% if (PERFIL == 1355) { %>
            <div class="col-md-2 col-sm-3 col-xs-12 pr5 pl5">
              <select class="w100p no-model buscar form-control" id="facturas_paycomet_usuario"></select>
            </div>
          <% } %>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cursos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Cliente</th>
                <th>Valor</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Opciones</th>
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
<script type="text/template" id="facturas_paycomet_item">
  <td><span class=''><%= tipo %></span></td>
  <td><span class='text-info'><%= usuario %></span></td>
  <td><span class=''><%= Number(monto/100).format(2) %> €</span></td>
  <td><span class=''><%= moment(fecha, "YYYY-MM-DD HH:mm:ss").format("DD/MM/YYYY HH:mm") %></span></td>
  <td>
    <% if (estado == 0) { %>
      <span class='label bg-danger'>Pendiente</span>
    <% } else if (estado == 1) { %>
      <span class='label bg-success'>Pagado</span>
    <% } %>
  </td>
  <td>
    <a target="_blank" href="facturas_paycomet/function/ver_factura/<%= id %>" class="btn btn-info imprimir_factura">Descargar</a>
  </td>
</script>

<script type="text/template" id="tarjetas_usuarios_table_view">
  <div class="modal-header">
    <b>Administrar tarjetas <% if (usuario != "") { %> de <%= usuario %> <% } %></b>
    <i class="pull-right cerrar fs16 fa fa-times cp"></i>
  </div>

  <div class="modal-body">
    <div class="">
      <div class="row mb30">
        <div class="col-md-7">
          <label>Numero de Tarjeta</label>
          <input type="text" id="tarjetas_usuarios_numero" class="form-control">
        </div>
        <div class="col-md-3">
          <label>Vencimiento</label>
          <input type="text" id="tarjetas_usuarios_vencimiento" class="form-control">
        </div>
        <div class="col-md-2">
          <label>Agregar</label>
          <span class="db">
            <a id="tarjetas_usuarios_agregar" class="w100p btn btn-info"><i class="fa ico fa-plus"></i></a>
          </span>
        </div>
      </div>
      <div class="">
        <table id="usuarios_tarjetas_table" class="table table-striped ordenable m-b-none default footable">
          <thead>
            <tr>
              <th>Numero</th>
              <th>Fecha Ven.</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <% for (var i = 0; i < tarjetas.length; i++) { %>
              <% var t = tarjetas[i] %>
              <tr data-id="<%= t.id %>">
                <td class="numero"> <%= t.number %> </td>
                <td class="caducidad"> <%= t.caducidad %> </td>
                <td> 
                  <button title='Eliminar Tarjeta' data-toggle='tooltip' title='Tooltip on top' class='ml15 btn btn-sm btn-white eliminar_tarjeta'><i class='fa fa-trash'></i></button>
                </td>
              </tr>
            <% } %>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-info guardar"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
  </div>

</script>

<script type="text/template" id="seleccionar_tarjetas_table_view">
  <div class="modal-header">
    <b>Seleccionar tarjeta <% if (usuario != "") { %> de <%= usuario %> <% } %></b>
    <i class="pull-right cerrar fs16 fa fa-times cp"></i>
  </div>

  <div class="modal-body">
    <div class="">
      <div class="mb30">
        <% if (tarjetas.length > 0) { %>
          <table id="seleccionar_tarjeta_table" class="table table-striped ordenable m-b-none default footable">
            <thead>
              <tr>
                <th class="w20"></th>
                <th>Numero</th>
                <th>Fecha Ven.</th>
                <th>CVV</th>
              </tr>
            </thead>
            <tbody>
              <% for (var i = 0; i < tarjetas.length; i++) { %>
                <% var t = tarjetas[i] %>
                <tr data-id="<%= t.id %>">
                  <td class="w20">
                    <label class="i-checks m-b-none">
                      <input class="esc seleccionar_tarjeta" type="checkbox"><i></i>
                    </label>
                  </td>
                  <td class="numero"> <%= t.number %> </td>
                  <td class="caducidad"> <%= t.caducidad %> </td>
                  <td class="cvv"><input class="form-control" type="number"></td>
                </tr>
              <% } %>

              <tr data-id="0">
                <td class="w20">
                  <label class="i-checks m-b-none">
                    <input class="esc seleccionar_tarjeta" type="checkbox"><i></i>
                  </label>
                </td>
                <td class="numero"> <input class="form-control" placeholder="Ingrese el numero" type="text"></td>
                <td class="caducidad"> <input class="form-control" placeholder="Ingrese la caducidad" type="text"> </td>
                <td class="cvv"><input class="form-control" placeholder="CVV" type="number"></td>
              </tr>
            </tbody>
          </table>
        <% } %>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button disabled class="btn btn-info guardar"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
  </div>

</script>