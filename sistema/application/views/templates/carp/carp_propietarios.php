<script type="text/template" id="carp_propietarios_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i><b>Propietarios</b></h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <div class="panel-heading oh">
      <div class="row">
        <div class="col-md-6 col-lg-4 sm-m-b">
          <div class="input-group">
            <input type="text" id="carp_propietarios_buscar" value="<%= window.carp_propietarios_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-offset-2 col-lg-6 text-right">
          <a class="btn btn-info btn-addon" href="app/#carp_propietario"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="carp_propietarios_table" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th class="w25 tac"></th>
              <th>Nombre</th>
              <th>Agencia</th>
              <th>Telefono</th>
              <th>Observaciones</th>
              <% if (permiso > 1) { %>
                <th class="th_acciones w120"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?></th>
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

<script type="text/template" id="carp_propietarios_item">
  <% var clase = (activo==1)?"text-info":"text-muted" %>
  <td class="<%= clase %>">
    <% if (!isEmpty(path)) { %>
      <% var prefix = (path.indexOf("http") == 0) ? "" : "/sistema/" %>
      <img src="<%= prefix + path %>?t=<%= Math.ceil(Math.random()*10000) %>" class="customcomplete-image xl mr0"/>
    <% } else { %>
      <img src="resources/images/a0.jpg" class="customcomplete-image xl mr0"/>
    <% } %>
  </td>
  <td class="ver"><span class='ver <%= clase %>'><%= nombre.ucwords() %> <%= apellido.ucwords() %></span></td>
  <td><span class='ver'><%= agencia %></span></td>
  <td><span class='ver'><%= telefono %></span></td>
  <td><span class='ver'><%= titulo %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
        </ul>
      </div>    
    </td>
  <% } %>
</script>

<script type="text/template" id="carp_propietarios_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i><b>Propietarios</b></h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> id="carp_propietario_nombre" name="nombre" class="form-control" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Apellido</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> id="carp_propietario_apellido" name="apellido" class="form-control" value="<%= apellido %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">DNI</label>
                    <input type="number" <%= (!edicion)?"disabled":"" %> id="carp_propietario_documento" name="documento" class="form-control" value="<%= documento %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Tel&eacute;fono","en"=>"Telephone")); ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="number" name="telefono" class="form-control" id="carp_propietario_telefono" value="<%= telefono %>"/>
                  </div>
                </div>                
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Agencia</label>
                    <select class="form-control" <%= (!edicion || (permiso <= 2))?"disabled":"" %> id="carp_propietario_agencias"></select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Nro. Interno</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="numero_interno" class="form-control" id="carp_propietario_numero_interno" value="<%= numero_interno %>"/>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Calle</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="direccion" class="form-control" id="carp_propietario_direccion" value="<%= direccion %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Altura</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="numero_calle" class="form-control" id="carp_propietario_numero_calle" value="<%= numero_calle %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Ciudad</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="ciudad" class="form-control" id="carp_propietario_ciudad" value="<%= ciudad %>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Email de acceso</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="email" class="form-control" id="carp_propietarios_email" value="<%= email %>"/>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Contrase&ntilde;a","en"=>"Password")); ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="password" autocomplete="new-password" class="form-control" id="carp_propietario_password" placeholder="<?php echo lang(array("es"=>"Escriba aqui para cambiar la contrase&ntilde;a","en"=>"Enter here your new password")); ?>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Repetir contrase&ntilde;a","en"=>"Repeat password")); ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="password" autocomplete="new-password" class="form-control" id="carp_propietario_password_2" placeholder="<?php echo lang(array("es"=>"Escriba nuevamente la contrase&ntilde;a anterior","en"=>"Repeat your new password")); ?> "/>
                  </div>
                </div>
              </div>

              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>"Foto",
                "url"=>"/sistema/carp_propietarios/function/save_image/",
                "width"=>(isset($empresa->config["carp_propietario_image_width"]) ? $empresa->config["carp_propietario_image_width"] : 400),
                "height"=>(isset($empresa->config["carp_propietario_image_height"]) ? $empresa->config["carp_propietario_image_height"] : 400),
              )); ?>              

              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <textarea class="form-control" <%= (!edicion)?"disabled":"" %> name="titulo" id="carp_propietario_titulo"><%= titulo %></textarea>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>  

    <% if (edicion) { %>
      <div class="line b-b m-b-lg"></div>
      <div class="row">
        <div class="col-md-10 col-md-offset-1 tar">
          <button class="btn btn-success guardar"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
        </div>
      </div>
    <% } %>

  </div>
</div>
</script>