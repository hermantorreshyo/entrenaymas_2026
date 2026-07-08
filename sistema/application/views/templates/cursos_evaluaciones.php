<script type="text/template" id="cursos_evaluaciones_table_view">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> <b>Evaluaciones</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row pl10 pr10">
          <div class="col-md-2 col-sm-3 col-xs-12 pr5 pl5">
            <select class="w100p no-model" id="cursos_evaluaciones_clientes_etiquetas"></select>
          </div>          
          <div class="col-md-2 col-sm-3 col-xs-12 pr5 pl5">
            <select class="w100p no-model" id="cursos_evaluaciones_clientes"></select>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 pr5 pl5">
            <select class="w100p no-model" id="cursos_evaluaciones_cursos"></select>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 pr5 pl5">
            <select class="form-control no-model" id="cursos_evaluaciones_estados">
              <option value="-1">Estado</option>
              <option value="1">Aprobado</option>
              <option value="0">Desaprobado</option>
            </select>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cursos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Grupo</th>
                <th>Curso</th>
                <th>Clase</th>
                <th>Fecha</th>
                <th>Estado</th>
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
<script type="text/template" id="cursos_evaluaciones_item">
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><span class=''><%= etiqueta %></span></td>
  <td class="ver"><span class=''><%= curso %></span></td>
  <td class="ver"><span class=''><%= clase %></span></td>
  <td class="ver"><span class=''><%= fecha %></span></td>
  <% if (estado==0) { %>
    <td class="ver"><span class='label bg-danger'>Desaprobado</span></td>
  <% } else { %>
    <td class="ver"><span class='label bg-success'>Aprobado</span></td>
  <% } %>
</script>