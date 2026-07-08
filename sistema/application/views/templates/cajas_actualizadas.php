<script type="text/template" id="cajas_actualizadas_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-dollar icono_principal"></i>Cajas
    / <b>Actualizadas</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="cajas_actualizadas_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th>Sucursal</th>
              <th>PV Numero</th>
              <th>PV Nombre</th>
              <th>Fecha</th>
              <th>Version GIT</th>
              <th>Version DB</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="cajas_actualizadas_item">
  <td><%= sucursal %></td>
  <td><%= numero %></td>
  <td><%= nombre %></td>
  <td><%= fecha %></td>
  <td><%= version_git %></td>
  <td><%= version_db %></td>
</script>
