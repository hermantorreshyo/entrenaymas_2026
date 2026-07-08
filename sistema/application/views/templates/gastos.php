<script type="text/template" id="listado_gastos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-shopping-cart icono_principal"></i>Compras / <b>Gastos</b></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <?php $active = "gastos"; include("compras/compras_menu.php"); ?>

      <div class="panel-heading clearfix">
        <button class="btn btn-info nuevo_gasto">Agregar gasto</button>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="gastos_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Descripci&oacute;n</th>
                <th class="tar">Total</th>
                <th style="width:20px;"></th>
                <th style="width:20px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-important">
              <tr>
                <td colspan="4"></td>
                <td class="tar bold" id="caja_diaria_gastos_total">$ 0.00</td>
                <td></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="editar_gasto_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <%= (id == undefined)?"Cargar gasto":"Editar gasto" %>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha</label>
          <div class="input-group">
            <input type="text" value="<%= fecha %>" name="fecha" class="form-control esc" id="gastos_fecha"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Monto</label>
          <input type="text" value="<%= total %>" name="total" class="form-control esc" id="gastos_total"/>
        </div>
      </div>      
    </div>
    <div class="form-group">
      <label class="control-label">Concepto</label>
      <select class="form-control esc" name="id_tipo_gasto" id="gastos_tipo"></select>
    </div>
    <div class="form-group">
      <label class="control-label">Descripci&oacute;n</label>
      <textarea name="observaciones" class="h80 form-control"><%= observaciones %></textarea>
    </div>
  </div>
  <div class="panel-footer tar">
    <button class="btn btn-success guardar">Guardar</button>
  </div>
</div>
</script>


<script type="text/template" id="listado_gastos_item">
  <td class='ver'><%= fecha %></td>
  <td class='ver'><span class="text-info"><%= tipo_gasto %></span></td>
  <td class='ver'><%= proveedor %></td>
  <td class='ver'><%= observaciones %></td>
  <td class="ver tar">$ <%= Number(total).toFixed(2) %></td>
  <td><span class="btn btn-white edit cp" data-id='<%= id %>'><i class='fa fa-pencil'></i></td>
  <td><span class="btn btn-white delete cp" data-id='<%= id %>'><i class='fa fa-trash'></i></span></td>
</script>