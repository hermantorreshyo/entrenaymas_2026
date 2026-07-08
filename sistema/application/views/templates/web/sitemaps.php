<script type="text/template" id="sitemaps_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Sitemap</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
              <div class="col-lg-4 col-md-6 col-sm-9 col-xs-12">
                <div class="row">
                  <div class="input-group">
                      <input type="text" id="sitemaps_buscar" placeholder="Buscar..." value="<%= filter %>" autocomplete="off" class="form-control">
                      <span class="input-group-btn">
                        <button class="btn btn-default"><i class="fa fa-search"></i></button>
                      </span>
                  </div>
                </div>
              </div>
              <% if (!seleccionar) { %>
                <div class="pull-right">
                  
                  <a class="btn btn-success btn-addon ml5" href="app/#sitemap">
                    <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
                  </a>
                  
                  <div class="btn-group dropdown ml5">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <span>Acciones</span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li><a href="javascript:void" class="eliminar_lote">Eliminar</a></li>
                    </ul>
                  </div>                  
                  
                </div>
              <% } %>
          </div>
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="sitemaps_tabla" class="table table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <% if (!seleccionar) { %>
                        <th style="width:20px;">
                            <label class="i-checks m-b-none">
                                <input class="esc sel_todos" type="checkbox"><i></i>
                            </label>
                        </th>                      
                      <% } else { %>
                        <th style="width:20px;"></th>
                      <% } %>
                      <th class="sorting" data-sort-by="url">URL</th>
                      <th class="sorting" data-sort-by="priority">Prioridad</th>
                      <th class="sorting" data-sort-by="changefreq">Frecuencia</th>
                      <th class="sorting" data-sort-by="lastmod">&Uacute;lt. Mod.</th>
                      <% if (!seleccionar) { %>
                        <th style="width:150px;text-align:right">Acciones</th>
                      <% } %>
                    </tr>
                  </thead>
                  <tbody class="tbody"><tbody>
                  <tfoot class="pagination_container hide-if-no-paging"></tfoot>
                </table>
              </div>
          </div>
      </div>
  </div>
</script>

<script type="text/template" id="sitemaps_item_resultados_template">
    <% var clase = (activo==1) ? "" : "text-muted"; %>
    <% if (seleccionar) { %>
      <td>
          <label class="i-checks m-b-none">
              <input class="radio esc" value="<%= id %>" name="radio" type="radio"><i></i>
          </label>
      </td>
    <% } else { %>
      <td>
          <label class="i-checks m-b-none">
              <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
          </label>
      </td>    
    <% } %>
    <td class="<%= clase %> data"><%= url %></td>
    <td class="<%= clase %> data"><%= priority %></td>
    <td class="<%= clase %> data"><%= changefreq %></td>
    <td class="<%= clase %> data"><%= lastmod %></td>
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <div class="fr">
          <i title="Activo" class="fl mr5 fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
          <div class="btn-group dropdown fl">
            <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
            <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
              <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
            </ul>
          </div>        
        </div>
      </td>
    <% } %>
</script>


<script type="text/template" id="sitemap_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Sitemap</h1>
</div>

<div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
        <div class="panel-heading">
            <span class="font-bold">Ingrese los datos</span>
        </div>
        <div class="panel-body">
        
            <div class="form-horizontal">
                        
                <div class="form-horizontal">   

                  <div class="form-group">
                    <label class="col-md-2 control-label">URL</label>
                    <div class="col-md-10">
                      <input type="text" id="sitemap_url" name="url" value="<%= url %>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-2 control-label">Fecha Modificacion</label>
                    <div class="col-md-10">
                        <div class="input-group w-md">
                            <input type="text" name="lastmod" id="lastmod" value="<%= lastmod %>" class="form-control">
                            <span class="input-group-btn">
                                <button id="fecha_button" type="button" class="btn btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-2 control-label">Frecuencia de cambio</label>
                    <div class="col-md-10">
                      <select name="changefreq" id="sitemap_changefreq" class="form-control">
                        <option value="">Sin definir</option>
                        <option <%= (changefreq == "always")?"selected":"" %> value="always">Siempre</option>
                        <option <%= (changefreq == "hourly")?"selected":"" %> value="hourly">Por hora</option>
                        <option <%= (changefreq == "daily")?"selected":"" %> value="daily">Por dia</option>
                        <option <%= (changefreq == "weekly")?"selected":"" %> value="weekly">Por semana</option>
                        <option <%= (changefreq == "monthly")?"selected":"" %> value="monthly">Por mes</option>
                        <option <%= (changefreq == "yearly")?"selected":"" %> value="yearly">Por a&ntilde;o</option>
                        <option <%= (changefreq == "never")?"selected":"" %> value="never">Nunca</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-2 control-label">Prioridad</label>
                    <div class="col-md-10">
                      <input type="number" min="0" max="1" name="priority" value="<%= priority %>" class="form-control">
                    </div>
                  </div>                  
					
                  <div class="line line-dashed b-b line-lg pull-in"></div>
                  <% if (edicion) { %>
                      <div class="form-group">
                          <div class="col-xs-12">
                              <button class="btn guardar btn-success">Guardar</button>
                          </div>
                      </div>
                  <% } %>                    
                    
                </div>
            </div>
            
        </div>
    </div>
</div>
     
</script>
