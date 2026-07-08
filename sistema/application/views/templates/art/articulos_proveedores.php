<% if (control.check("proveedores")>0) { %>
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="padder">
        <div class="form-group mb0 clearfix">
          <label class="control-label">
            <?php echo lang(array(
              "es"=>"Proveedores",
              "en"=>"Suppliers",
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
              "es"=>"Puede relacionar distintos proveedores al mismo producto.",
              "en"=>"You can relate different suppliers to the same product.",
            )); ?>                  
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body expand">
      <div class="padder">
        <% if (edicion) { %>
          <div class="form-inline row m-b clearfix">
            <div class="form-group col-sm-8">
              <label class="control-label">Proveedor</label>
              <div class="input-group" style="width: 100%">
                <select id="articulo_proveedores" class="w100p"></select>
                <span class="input-group-btn w1p">
                  <button tabindex="-1" class="btn btn-info agregar_proveedor">+</button>  
                </span>
              </div>
            </div>
            <div class="form-group col-sm-4">
              <label class="control-label">C&oacute;digo Art.</label>
              <div class="input-group">
                <input id="proveedor_codigo" value="" type="text" class="form-control"/>
                <span class="input-group-btn">
                  <a id="proveedor_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                </span>
              </div>
            </div>
          </div>
        <% } %>
        <div class="">
          <table id="proveedores_tabla" class="table m-b-none default footable">
            <thead>
              <tr>
                <th>Proveedor</th>
                <th>C&oacute;digo Art.</th>
                <% if (edicion) { %>
                  <th class="w25"></th>
                  <th class="w25"></th>
                <% } %>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i< proveedores.length;i++) { %>
                <% var p = proveedores[i] %>
                <tr data-id="<%= p.id_proveedor %>">
                  <td><%= p.nombre %></td>
                  <td><%= p.codigo %></td>
                  <% if (edicion) { %>
                    <td><i class='fa fa-pencil cp editar_proveedor'></i></td>
                    <td><i class='fa fa-times eliminar_proveedor text-danger cp'></i></td>
                  <% } %>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
<% } %>   