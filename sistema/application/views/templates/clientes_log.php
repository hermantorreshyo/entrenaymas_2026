<script type="text/template" id="clientes_log_table_view">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> <b>Log de clientes</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row pl10 pr10">   
          <div class="col-md-2 col-sm-3 col-xs-12 pr5 pl5">
            <select class="w100p no-model" id="clientes_log_id"></select>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cursos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Accion</th>
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
<script type="text/template" id="clientes_log_item">
  <td class="ver"><span class=''><%= fecha %></span></td>
  <td class="ver"><span class=''><%= nombre %></span></td>
  <td class="ver"><span class=''><%= accion %></span></td>
</script>