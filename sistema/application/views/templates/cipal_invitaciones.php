<script type="text/template" id="cipal_invitaciones_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Invitaciones</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-sm-3 sm-m-b">
            <select id="cipal_invitaciones_empresas" class="form-control no-model"></select>
          </div>
          <div class="col-sm-3 sm-m-b">
            <input type="text" id="cipal_invitaciones_cantidad" class="form-control no-model" placeholder="Cantidad"/>
          </div>
          <div class="col-sm-3 sm-m-b">
            <button id="cipal_invitaciones_generar" class="btn btn-info">Generar</button>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cipal_invitaciones_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Empresa</th>
                <th>Codigo</th>
                <th class="w100"></th>
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


<script type="text/template" id="cipal_invitaciones_item">
  <td class="ver"><span class='text-info'><%= empresa %></span></td>
  <td class="ver"><%= codigo %></td>
  <td class="p5 td_acciones">
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>    
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="descargar" data-id="<%= id %>">Descargar</a></li>
      </ul>
    </div>
  </td>
</script>