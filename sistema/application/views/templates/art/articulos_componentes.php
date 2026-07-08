<div class="panel panel-default <%= (ID_EMPRESA == 1284)?"dn":"" %>">
  <div class="panel-body">
    <div class="padder">
      <div class="form-group mb0 clearfix">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Componentes",
            "en"=>"Components",
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
            "es"=>"Puede formar un artículo compuesto desde esta sección.",
            "en"=>"Puede formar un artículo compuesto desde esta sección.",
          )); ?>                  
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body expand">
    <div class="padder">
      <div class="text-muted mb15">
        <?php echo lang(array(
          "es"=>"Un artículo compuesto es aquel formado por otros productos que se encuentran cargados en el sistema de manera individual. Cuando se vende un producto compuesto, se descuenta el stock de todos sus componentes automáticamente.",
          "en"=>"Un artículo compuesto es aquel formado por otros productos que se encuentran cargados en el sistema de manera individual. Cuando se vende un producto compuesto, se descuenta el stock de todos sus componentes automáticamente.",
        )); ?>
      </div>
      <div class="row">
        <div class="col-md-8">
          <input type="hidden" id="articulo_componentes_id" value="0"/>
          <div class="form-group">
            <label class="control-label">Producto</label>
            <input class="form-control" type="text" id="articulo_componentes_buscar" placeholder="Busque los productos que desea relacionar..." />
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Cantidad</label>
            <div class="input-group">
              <input id="articulo_componentes_cantidad" type="text" class="form-control"/>
              <span class="input-group-btn">
                <a class="btn btn-info agregar_componente">Agregar</a>
              </span>
            </div>
          </div>
        </div>        
      </div>
      <div class="">
        <table id="articulos_tabla_componentes" class="table m-b-none default footable">
          <thead>
            <tr>
              <th>Componente</th>
              <th>Cantidad</th>
              <th class="w25"></th>
              <th class="w25"></th>
            </tr>
          </thead>
          <tbody>
            <% for(var i=0;i< componentes.length;i++) { %>
              <% var p = componentes[i] %>
              <tr data-id='<%= p.id_articulo_componente %>'>
                <td><%= p.nombre %></td>
                <td><%= p.cantidad %></td>
                <td><i class='fa fa-pencil cp editar_componente'></i></td>
                <td><i class='fa fa-times eliminar_componente text-danger cp'></i></td>
              </tr>
            <% } %>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>