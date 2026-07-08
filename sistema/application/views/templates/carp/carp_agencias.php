<script type="text/template" id="carp_agencias_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-home icono_principal"></i><b>Agencias</b></h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <div class="panel-heading oh">
      <div class="row">
        <div class="col-md-6 col-lg-4 sm-m-b">
          <div class="input-group">
            <input type="text" id="carp_agencias_buscar" value="<%= window.carp_agencias_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-offset-2 col-lg-6 text-right">
          <% if (permiso>1) { %>
            <a class="btn btn-info btn-addon" href="app/#carp_agencia"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</a>
          <% } %>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="carp_agencias_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th class="w25 tac"></th>
              <th>Nombre</th>
              <th>Código</th>
              <th>Direccion</th>
              <th>Telefono</th>
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

<script type="text/template" id="carp_agencias_item">
  <% var clase = (activo==1)?"text-info":"text-muted" %>
  <td class="<%= clase %>">
    <% if (!isEmpty(path)) { %>
      <% var prefix = (path.indexOf("http") == 0) ? "" : "/sistema/" %>
      <img src="<%= prefix + path %>?t=<%= Math.ceil(Math.random()*10000) %>" class="customcomplete-image xl mr0"/>
    <% } %>
  </td>  
  <td class="ver"><span class='ver <%= clase %>'><%= nombre.ucwords() %></span></td>
  <td><span class='ver'><%= cargo %></span></td>
  <td><span class='ver'><%= direccion %></span></td>
  <td><span class='ver'><%= telefono %></span></td>
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

<script type="text/template" id="carp_agencias_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-home icono_principal"></i><b>Agencias</b></h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" id="nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Código</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="cargo" class="form-control" id="cargo" value="<%= cargo %>"/>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Direcci&oacute;n","en"=>"Address")); ?></label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="direccion" class="form-control" id="carp_agencia_direccion" value="<%= direccion %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Tel&eacute;fono","en"=>"Telephone")); ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" class="form-control" id="carp_agencia_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Email de acceso</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="email" class="form-control" id="carp_agencias_email" value="<%= email %>"/>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Contrase&ntilde;a","en"=>"Password")); ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="password" autocomplete="new-password" class="form-control" id="carp_agencia_password" placeholder="<?php echo lang(array("es"=>"Escriba aqui para cambiar la contrase&ntilde;a","en"=>"Enter here your new password")); ?>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Repetir contrase&ntilde;a","en"=>"Repeat password")); ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="password" autocomplete="new-password" class="form-control" id="carp_agencia_password_2" placeholder="<?php echo lang(array("es"=>"Escriba nuevamente la contrase&ntilde;a anterior","en"=>"Repeat your new password")); ?> "/>
                  </div>
                </div>
              </div>

              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>"Logo",
                "url"=>"/sistema/carp_agencias/function/save_image/",
                "width"=>(isset($empresa->config["carp_agencia_image_width"]) ? $empresa->config["carp_agencia_image_width"] : 400),
                "height"=>(isset($empresa->config["carp_agencia_image_height"]) ? $empresa->config["carp_agencia_image_height"] : 400),
              )); ?>              

              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <textarea class="form-control" <%= (!edicion)?"disabled":"" %> name="titulo" id="carp_agencia_titulo"><%= titulo %></textarea>
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