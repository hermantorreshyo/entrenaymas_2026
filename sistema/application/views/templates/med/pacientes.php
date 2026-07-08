<script type="text/template" id="pacientes_panel_template">
  <div class="seccion_vacia" style="display:none">
    <h1 class="h1">Todav&iacute;a no ten&eacute;s ning&uacute;n paciente</h1>
    <h3 class="h3">Para crear tu primer paciente, hace click en el siguiente bot&oacute;n</h3>
    <div class="list-icon">
      <a href="app/#paciente"><i class="icon-note"></i></a>
    </div>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#paciente">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
    <p>
      Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
    </p>
  </div>
  <div class="seccion_llena" style="display:none">
    <div class="bg-light lter b-b wrapper-md">
      <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Pacientes</h1>
    </div>
    <div class="wrapper-md">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-3 sm-m-b">
              <div class="input-group">
                <input type="text" id="pacientes_buscar" value="<%= window.pacientes_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>
            <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#paciente">
                <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            </div>
            <% } %>
          </div>
        </div>
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="pacientes_table" class="table table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th class="w50 tac hidden-xs"></th>
                  <th class="sorting" data-sort-by="nombre">Nombre</th>
                  <th class="sorting hidden-xs" data-sort-by="obra_social">Obra social</th>
                  <th class="sorting hidden-xs" data-sort-by="email">Email</th>
                  <th class="sorting hidden-xs" data-sort-by="celular">Celular</th>
                  <% if (permiso > 1) { %>
                    <th class="w120 th_acciones"></th>
                  <% } %>
                </tr>
              </thead>
              <tbody class="tbody"></tbody>
              <tfoot class="pagination_container hide-if-no-paging"></tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>    
  </div>
</script>

<script type="text/template" id="pacientes_item">
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %> pull-left">
        <%= isEmpty(nombre) ? email.substr(0,1) : nombre.substr(0,1) %>
      </span>
    <% } %>
  </td>
	<td class='data'><span class="text-info"><%= nombre %></span></td>
  <td class='data hidden-xs'><span><%= obra_social %></span></td>
	<td class='data hidden-xs'><span><%= email %></span></td>
	<td class='data hidden-xs'><span><%= celular %></span></td>
  <td class="p5 td_acciones">
    <a href="app/#paciente/<%= id %>"><i class="fa fa-pencil iconito active"></i></a>
    <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>        
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>    
  </td>
</script>

<script type="text/template" id="pacientes_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Pacientes / 
      <b><%= (id == undefined)?"Nuevo":nombre %></b>
    </h1>
  </div>
  <div class="wrapper-md">

    <div class="centrado rform">
      <div class="row">

        <div class="col-md-4">
          <div class="detalle_texto">
            <?php 
            $clave = "Pacientes / Detalle / Texto 1";
            echo lang(array(
              "es"=>(isset($videos[$clave]["nombre_es"]) ? $videos[$clave]["nombre_es"] : "" ),
              "en"=>(isset($videos[$clave]["nombre_en"]) ? $videos[$clave]["nombre_en"] : "" ),
            )); ?>
          </div>
          <div class="detalle_texto_info text-muted">
            <?php echo lang(array(
              "es"=>(isset($videos[$clave]["texto_es"]) ? $videos[$clave]["texto_es"] : "" ),
              "en"=>(isset($videos[$clave]["texto_en"]) ? $videos[$clave]["texto_en"] : "" ),
            )); ?>
          </div>
          <?php if (isset($videos[$clave]["video_es"])) { ?>
            <a onclick="workspace.open_video(this)" data-iframe='<?php echo $videos[$clave]["video_es"] ?>'>
              Ver video
            </a>
          <?php } ?>
        </div>

        <div class="col-md-8">

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Nombre</label>
                      <input type="text" name="nombre_solo" class="form-control" id="pacientes_nombre_solo" value="<%= nombre_solo %>"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Apellido</label>
                      <input type="text" name="apellido" class="form-control" id="pacientes_apellido" value="<%= apellido %>"/>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label">Email</label>
                  <input type="text" name="email" class="form-control" id="pacientes_email" value="<%= email %>"/>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Cobertura</label>
                      <div class="input-group">
                        <select id="paciente_obras_sociales" class="w100p"></select>
                        <span class="input-group-btn">
                          <button tabindex="-1" class="btn btn-info agregar_obra_social">+ Agregar</button>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="control-label">N&uacute;mero</label>
                    <input type="text" name="numero_obra_social" class="form-control" id="pacientes_numero_obra_social" value="<%= numero_obra_social %>"/>
                  </div>
                </div>
                <div class="form-group mb0 tar">
                  <a class="expand-link">
                    <?php echo lang(array(
                      "es"=>"+ M&aacute;s opciones",
                      "en"=>"+ More options",
                    )); ?>
                  </a>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="row">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">DNI</label>
                        <input type="text" name="cuit" class="form-control" id="pacientes_cuit" value="<%= cuit %>"/>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Fecha de nacimiento</label>
                        <div class="input-group">
                          <input type="text" class="form-control" id="pacientes_fecha_nac" name="fecha_nac" value="<%= fecha_nac %>"/>
                          <span class="input-group-btn">
                            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Sexo</label>
                        <select id="pacientes_sexo" name="sexo" class="form-control">
                          <option <%= (sexo=="M")?"selected":"" %> value="M">Masculino</option>
                          <option <%= (sexo=="F")?"selected":"" %> value="F">Femenino</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Telefono</label>
                      <input type="text" name="telefono" class="form-control" id="pacientes_telefono" value="<%= telefono %>"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Celular</label>
                      <input type="text" name="celular" class="form-control" id="pacientes_celular" value="<%= celular %>"/>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Direccion</label>
                      <input type="text" name="direccion" class="form-control" id="pacientes_direccion" value="<%= direccion %>"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Localidad</label>
                      <input type="text" name="localidad" class="form-control" id="pacientes_localidad" value="<%= localidad %>"/>
                    </div>
                  </div>
                </div>
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
                  <input type="password" class="form-control" id="pacientes_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Repetir contrase&ntilde;a</label>
                  <input type="password" class="form-control" id="pacientes_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="line b-b m-b-lg"></div>

      <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-8 tar">
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    </div>

  </div>
</script>

<script type="text/template" id="pacientes_edit_mini_panel_template">
<div class="panel pb0 mb0">
  <div class="panel-body">
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Nombre" name="nombre" class="tab form-control" id="pacientes_mini_nombre"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Apellido" name="nombre" class="tab form-control" id="pacientes_mini_apellido"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Celular" name="celular" class="tab form-control" id="pacientes_mini_celular"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="DNI" name="cuit" class="tab form-control" id="pacientes_mini_cuit"/>
    </div>    
    <div class="form-group">
      <button class="btn guardar btn-success tab btn-block">Guardar</button>
    </div>
  </div>
</div>
</script>