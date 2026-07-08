<script type="text/template" id="planes_panel_template">
  
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Listado de Planes</h1>
  </div>  

  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
			  <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
        <a class="btn pull-right btn-success btn-addon" href="app/#plan"><i class="fa fa-plus"></i>Nuevo</a>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="planes_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <% if (ID_PROYECTO == 0) { %>
                  <th>ID</th>
  								<th>Proyecto</th>
                  <th>Nombre</th>
  								<th>Limite</th>
  								<th>Precio</th>
                <% } else { %>
                  <th>Nombre</th>
                  <th>Precio</th>
                <% } %>
                <% if (permiso > 1) { %>
                  <th class="w25"></th>
                  <th class="w25"></th>
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


<script type="text/template" id="planes_item">
  <% if (ID_PROYECTO == 0) { %>
    <td><span class='ver'><%= id %></span></td>
  	<td><span class='ver'><%= proyecto %></span></td>
  	<td><span class='ver'><%= nombre %></span></td>
  	<td><span class='ver'><%= limite_articulos %></span></td>
  	<td><span class='ver'><%= precio_anual %></span></td>
  <% } else { %>
    <td><span class='ver'><%= nombre %></span></td>
    <td><span class='ver'><%= precio_anual %></span></td>
  <% } %>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="planes_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
  <% if (id == undefined) { %>
    Nuevo Plan
  <% } else { %>
    <%= nombre %>
  <% } %>	    
  </h1>
</div>

<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
  
    <div class="panel-heading">
      <span class="font-bold">Ingrese los datos</span>
    </div>
    <div class="panel-body">
    
      <div class="form-horizontal">
        <% if (ID_PROYECTO == 0) { %>
          <div class="form-group">
            <label class="col-lg-2 control-label">Proyecto</label>
            <div class="col-lg-10">
  					  <select id="planes_proyecto" class="form-control" name="id_proyecto"></select>
            </div>
          </div>				
        <% } %>
        <div class="form-group">
          <label class="col-lg-2 control-label">Nombre</label>
          <div class="col-lg-10">
					  <input type="text" name="nombre" class="form-control" id="planes_nombre" value="<%= nombre %>"/>
          </div>
        </div>
        <% if (ID_PROYECTO == 0) { %>

          <div class="form-group">
            <label class="col-lg-2 control-label">ID Articulo</label>
            <div class="col-lg-10">
              <input type="text" name="id_articulo" class="form-control" value="<%= id_articulo %>"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-2 control-label">Limite</label>
            <div class="col-lg-10">
              <input type="text" name="limite_articulos" class="form-control" value="<%= limite_articulos %>"/>
            </div>
          </div>				
        <% } %>
        <div class="form-group">
          <label class="col-lg-2 control-label">Precio</label>
          <div class="col-lg-10">
            <input type="text" name="precio_anual" class="form-control" value="<%= precio_anual %>"/>
          </div>
        </div>				
        <div class="form-group">
          <label class="col-lg-2 control-label">Texto</label>
          <div class="col-lg-10">
            <textarea class="form-control h100" name="observaciones"><%= observaciones %></textarea>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label">Boton MercadoPago</label>
          <div class="col-lg-10">
            <textarea class="form-control h100" name="boton_pago_mp"><%= boton_pago_mp %></textarea>
          </div>
        </div>

        <div class="line line-dashed b-b line-lg pull-in"></div>
        <% if (edicion) { %>
          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <button class="btn guardar btn-success">Guardar</button>
            </div>
          </div>
        <% } %>
      </div>
    </div>
  </div>
</div>

</script>