<script type="text/template" id="calm_clientes_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i>Usuarios</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("calm_clientes") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#calm_cliente"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="calm_clientes_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="email">Email</th>
                <th class="sorting" data-sort-by="lista">Plan</th>
                <% if (permiso > 1) { %>
                  <th class="w100"></th>
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


<script type="text/template" id="calm_clientes_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><%= email %></td>
  <td class="ver"><%= (lista==0)?"Basico":"Premium" %></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="calm_clientes_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i>Usuarios
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" id="calm_clientes_nombre" value="<%= nombre %>"/>
              </div>

              <div class="form-group">
                <label class="control-label">Email</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="email" class="form-control" id="calm_clientes_email" value="<%= email %>"/>
              </div>

              <div class="form-group">
                <label class="control-label">Plan</label>
                <select <%= (!edicion)?"disabled":"" %> id="calm_cliente_planes" name="lista" class="form-control">
                  <option <%= (lista==0)?"selected":"" %> value="0">Basico</option>
                  <option <%= (lista==1)?"selected":"" %> value="1">Premium</option>
                </select>
              </div>

              <?php
              single_file_upload(array(
                "name"=>"path",
                "label"=>"Imagen",
                "url"=>"/sistema/calm_clientes/function/save_file/",
              )); ?>
              
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Contrase&ntilde;a",
                    "en"=>"Password",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"Cambiar contrase&ntilde;a",
                    "en"=>"Change password",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Clave utilizada para ingresar al sistema.",
                    "en"=>"Agregar variantes a productos como talle, color, etc.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Contrase&ntilde;a</label>
                <input type="password" autocomplete="new-password" class="form-control" id="calm_cliente_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
              </div>
              <div class="form-group">
                <label class="control-label">Repetir contrase&ntilde;a</label>
                <input type="password" autocomplete="new-password" class="form-control" id="calm_cliente_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
              </div>
             </div>
          </div>
        </div>  
              
        <% if (edicion) { %>
          <button class="btn guardar btn-success">Guardar</button>
        <% } %>
      </div>
    </div>
  </div>
</div>

</script>