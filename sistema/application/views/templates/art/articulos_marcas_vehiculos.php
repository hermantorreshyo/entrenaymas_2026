<% if (control.check("marcas_vehiculos")>0) { %>
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="padder">
        <div class="form-group mb0 clearfix">
          <label class="control-label">
            <?php echo lang(array(
              "es"=>"Marcas de vehiculos",
            )); ?>
          </label>
          <a class="expand-link fr">
            <?php echo lang(array(
              "es"=>"+ Ver opciones",
              "en"=>"+ View options",
            )); ?>
          </a>
          <div class="panel-description">
            <?php echo lang(array(
              "es"=>"Agregue las diferentes marcas para los cuales sirve el producto.",
            )); ?>                  
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body expand" style="<%= (marcas_vehiculos.length > 0)?'display:block':'' %>">
      <div class="padder">
        <div class="form-inline row m-b clearfix">
          <div class="form-group col-sm-8">
            <label class="control-label">Marca</label>
            <select id="articulo_marcas_vehiculos" class="w100p"></select>
          </div>
          <div class="form-group col-sm-4">
            <label class="control-label">Modelo</label>
            <div class="input-group">
              <input id="marca_vehiculo_codigo" value="" type="text" class="form-control"/>
              <span class="input-group-btn">
                <a id="marca_vehiculo_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
              </span>
            </div>
          </div>
        </div>
        <div class="">
          <table id="marcas_vehiculos_tabla" class="table m-b-none default footable">
            <thead>
              <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th class="w25"></th>
                <th class="w25"></th>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i< marcas_vehiculos.length;i++) { %>
                <% var p = marcas_vehiculos[i] %>
                <tr data-id="<%= p.id_marca_vehiculo %>">
                  <td><%= p.nombre %></td>
                  <td><%= p.modelo %></td>
                  <td><i class='fa fa-pencil cp editar_marca_vehiculo'></i></td>
                  <td><i class='fa fa-times eliminar_marca_vehiculo text-danger cp'></i></td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
<% } %>
