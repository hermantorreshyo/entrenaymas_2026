<script type="text/template" id="tripulantes_panel_template">
  <div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <b>Tripulantes</b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#tripulante"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="tripulantes_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th>Tel&eacute;fono</th>
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


<script type="text/template" id="tripulantes_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class='ver'><span class='<%= (activo==1)?"text-info":"" %>'><%= nombre %></span></td>
  <td class='ver'><span><%= telefono %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="tripulantes_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n 
    / Tripulantes
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" id="tripulantes_nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">DNI</label>
                    <input type="text" name="dni" class="form-control" id="tripulantes_dni" value="<%= dni %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <input type="text" name="email" class="form-control" id="tripulantes_email" value="<%= email %>"/>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Sueldo Base</label>
                    <input type="text" name="sueldo_base" class="form-control" id="tripulantes_sueldo_base" value="<%= sueldo_base %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tel&eacute;fono</label>
                    <input type="text" name="telefono" class="form-control" id="tripulantes_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Nacionalidad</label>
                    <input type="text" name="nacionalidad" class="form-control" id="tripulantes_nacionalidad" value="<%= nacionalidad %>"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <% if (edicion) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array("es"=>"Contrase&ntilde;a","en"=>"Password")); ?>
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
                      "en"=>"In this section you can change your personal password.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Contrase&ntilde;a","en"=>"Password")); ?></label>
                  <input type="password" class="form-control" id="tripulantes_password" placeholder="<?php echo lang(array("es"=>"Escriba aqui para cambiar la contrase&ntilde;a","en"=>"Enter here your new password")); ?>"/>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Repetir contrase&ntilde;a","en"=>"Repeat password")); ?></label>
                  <input type="password" class="form-control" id="tripulantes_password_2" placeholder="<?php echo lang(array("es"=>"Escriba nuevamente la contrase&ntilde;a anterior","en"=>"Repeat your new password")); ?> "/>
                </div>
               </div>
            </div>
          </div> 
        <% } %>

        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>