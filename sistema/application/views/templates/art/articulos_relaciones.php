<div class="panel panel-default <%= (ID_EMPRESA == 1284)?"dn":"" %>">
  <div class="panel-body">
    <div class="padder">
      <div class="form-group mb0 clearfix">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Relaciones de productos",
            "en"=>"Related products",
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
            "es"=>"Agregue relaciones con otros productos puntuales o con categorias determinadas.",
            "en"=>"You can set a exact post or select a related category.",
          )); ?>                  
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body expand">
    <div class="padder">
      <div class="form-group">
        <label class="control-label">Relaciones con otros productos espec&iacute;ficos</label>
        <input class="form-control" type="text" id="articulos_buscar_productos" placeholder="Busque los productos especificos con los que desea relacionar..." />
        <ul id="articulos_tabla_relacionados" style="overflow-y: auto;" class="list-group gutter list-group-lg list-group-sp">
          <% for(var i=0;i< relacionados.length;i++) { %>
            <% var a = relacionados[i]; %>
            <li class='list-group-item'>
              <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>
              <img style='margin-left: 10px; margin-right:10px; max-height:50px' src='/sistema/<%= a.path %>'/>
              <span class='id dn'><%= a.id %></span>
              <span class='nombre'><%= a.nombre %></span>
              <span class='pull-right btn btn-white eliminar_relacionado'><i class='fa fa-times'></i> </span>
            </li>
          <% } %>
        </ul>
      </div>
      <div class="form-group">
        <label class="control-label">Relaciones con categorias</label>
        <div id="articulos_rubros_tree" style="overflow: auto;"></div>
      </div>
      <?php /*
      <div class="form-group">
        <label>Mostrar</label>
      </div>
      <div class="form-inline">
        <div class="form-group">
          <select class="form-control" name="relacionados_tipo">
            <option <%= (relacionados_tipo=="U")?"selected":"" %> value="U">ultimos</option>
            <option <%= (relacionados_tipo=="A")?"selected":"" %> value="A">aleatorios</option>
          </select>
        </div>
        <div class="form-group">
          <select class="form-control" name="relacionados_cantidad">
            <% for(j=1;j<=20;j++) { %>
              <option <%= (j==relacionados_cantidad)?"selected":"" %> value="<%= j %>"><%= j %></option>
            <% } %>
            <option <%= (relacionados_cantidad==0)?"selected":"" %> value="0">Todos</option>
          </select>
        </div>
        <div class="form-group">
          <label>elementos de cada categoria.</label>
        </div>
      </div>
      */ ?>
      
    </div>
  </div>
</div>