<script type="text/template" id="carp_postulantes_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i><b>Búsqueda Laboral</b></h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="<%= (window.carp_postulantes_estado == "1") ? "active":"" %>">
        <a href="javascript:void(0)" class="cambiar_tab" data-tipo="1" role="tab" data-toggle="tab"><i class="fa text-info fa-list"></i> Listado</a>
      </li>
      <li class="<%= (window.carp_postulantes_estado == "2") ? "active":"" %>">
        <a href="javascript:void(0)" class="cambiar_tab" data-tipo="2" role="tab" data-toggle="tab"><i class="fa text-danger fa-map"></i> Mapa</a>
      </li>
    </ul>          
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 sm-m-b">
          <div class="input-group">
            <input type="text" id="carp_postulantes_buscar" value="<%= window.carp_postulantes_filter %>" placeholder="Buscar por nombre, DNI o direccion..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-6 text-right">
          <a class="btn btn-info btn-addon" href="app/#carp_postulante"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div style="<%= (window.carp_postulantes_estado == "1") ? "display:none":"" %>;height:400px;" id="carp_postulantes_mapa"></div>
      <div style="<%= (window.carp_postulantes_estado == "2") ? "display:none":"" %>" id="carp_postulantes_listado" class="b-a table-responsive">
        <table id="carp_postulantes_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th class="w25 tac"></th>
              <th>Nombre</th>
              <th class="w150">Telefono</th>
              <th>Dirección</th>
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

<script type="text/template" id="carp_postulantes_item">
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
  <td>
    <% if (!isEmpty(telefono)) { %>
      <a data-toggle="tooltip" title="<%= telefono %>" class="enviar_whatsapp fl" href="javascript:void(0)"><i class="fa fa-whatsapp iconito active success"></i></a>
    <% } %>      
    <span class='ver ml5'><%= telefono %></span>
  </td>
  <td><span class='ver'><%= direccion %> <%= numero_calle %> <%= ciudad %></span></td>
  <td class="ver"><%= observaciones %></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="contratar" data-id="<%= id %>">Contratar</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
        </ul>
      </div>    
    </td>
  <% } %>
</script>

<script type="text/template" id="carp_postulantes_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i><b>Búsqueda Laboral</b></h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> id="carp_postulante_nombre" name="nombre" class="form-control" id="nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Apellido</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> id="carp_postulante_apellido" name="apellido" class="form-control" id="apellido" value="<%= apellido %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">DNI</label>
                    <input type="number" <%= (!edicion)?"disabled":"" %> id="carp_postulante_dni" name="dni" class="form-control" id="dni" value="<%= dni %>"/>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Tel&eacute;fono","en"=>"Telephone")); ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="number" name="telefono" class="form-control" id="carp_postulante_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Calle</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="direccion" class="form-control" id="carp_postulante_direccion" value="<%= direccion %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Altura</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="numero_calle" class="form-control" id="carp_postulante_numero_calle" value="<%= numero_calle %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Ciudad</label>
                    <div class="input-group">
                      <input type="text" <%= (!edicion)?"disabled":"" %> name="ciudad" class="form-control" id="carp_postulante_ciudad" value="<%= ciudad %>"/>
                      <span class="input-group-btn">
                        <button id="cargar_mapa" class="btn btn-default">Actualizar</button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>lang(array("es"=>"Foto","en"=>"Photo")),
                "url"=>"/sistema/carp_postulantes/function/save_image/",
                "width"=>(isset($empresa->config["carp_postulante_image_width"]) ? $empresa->config["carp_postulante_image_width"] : 400),
                "height"=>(isset($empresa->config["carp_postulante_image_height"]) ? $empresa->config["carp_postulante_image_height"] : 400),
              )); ?>              

              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <textarea class="form-control" <%= (!edicion)?"disabled":"" %> name="observaciones" id="carp_postulante_observaciones"><%= observaciones %></textarea>
              </div>              

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div id="carp_postulante_mapa" style="height:400px"></div>
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