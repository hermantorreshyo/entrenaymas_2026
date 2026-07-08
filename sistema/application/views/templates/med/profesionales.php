<script type="text/template" id="profesionales_panel_template">
  <div class="seccion_vacia" style="display:none">
    <h1 class="h1">Todav&iacute;a no ten&eacute;s ning&uacute;n profesional</h1>
    <h3 class="h3">Para crear tu primer profesional, hace click en el siguiente bot&oacute;n</h3>
    <div class="list-icon">
      <a href="app/#profesional"><i class="icon-note"></i></a>
    </div>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#profesional">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
    <p>
      Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
    </p>
  </div>
  <div class="seccion_llena" style="display:none">
    <div class="bg-light lter b-b wrapper-md">
      <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Profesionales</h1>
    </div>
    <div class="wrapper-md">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-3 sm-m-b">
              <div class="input-group">
                <input type="text" id="profesionales_buscar" value="<%= window.profesionales_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>
            <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#profesional">
                <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            </div>
            <% } %>
          </div>
        </div>
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="profesionales_table" class="table table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th class="sorting" data-sort-by="nombre">Apellido y Nombre</th>
                  <th class="sorting" data-sort-by="especialidad">Especialidad</th>
                  <th class="sorting" data-sort-by="email">Email</th>
                  <th class="sorting" data-sort-by="celular">Celular</th>
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

<script type="text/template" id="profesionales_item">
	<td class='data'><span class="text-info"><%= apellido %> <%= nombre %></span></td>
  <td class='data'><span><%= especialidad %></span></td>
	<td class='data'><span><%= email %></span></td>
	<td class='data'><span><%= celular %></span></td>
  <td class="p5 td_acciones">
    <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
    <i title="Destacado" class="fa-star iconito fa destacado <%= (destacado == 1)?"active":"" %>"></i>
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

<script type="text/template" id="profesionales_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Profesionales / 
      <b><%= (id == undefined)?"Nuevo":nombre %></b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="centrado rform">
      <div class="row">

        <div class="col-md-4">
          <div class="detalle_texto">
            <?php 
            $clave = "Profesionales / Detalle / Texto 1";
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
                      <input type="text" name="nombre" class="form-control" id="profesionales_nombre" value="<%= nombre %>"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Apellido</label>
                      <input type="text" name="apellido" class="form-control" id="profesionales_apellido" value="<%= apellido %>"/>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label">Email</label>
                  <input type="text" name="email" class="form-control" id="profesionales_email" value="<%= email %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Especialidad</label>
                  <div class="input-group">
                    <select id="profesional_especialidades" class="w100p"></select>
                    <span class="input-group-btn">
                      <button tabindex="-1" class="btn btn-info w100 agregar_especialidad">
                        + Agregar
                      </button>
                    </span>
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

                  <?php
                  single_upload(array(
                    "name"=>"path",
                    "label"=>"Foto",
                    "url"=>"profesionales/function/save_image/",
                    "width"=>(isset($empresa->config["profesional_image_width"]) ? $empresa->config["profesional_image_width"] : 400),
                    "height"=>(isset($empresa->config["profesional_image_height"]) ? $empresa->config["profesional_image_height"] : 400),
                    "quality"=>(isset($empresa->config["profesional_image_quality"]) ? $empresa->config["profesional_image_quality"] : 0),
                    "thumbnail_width"=>(isset($empresa->config["profesional_thumbnail_width"]) ? $empresa->config["profesional_thumbnail_width"] : 0),
                    "thumbnail_height"=>(isset($empresa->config["profesional_thumbnail_height"]) ? $empresa->config["profesional_thumbnail_height"] : 0),
                  )); ?>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">DNI</label>
                        <input type="text" name="dni" class="form-control" id="profesionales_dni" value="<%= dni %>"/>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Matricula</label>
                        <input type="text" name="matricula" class="form-control" id="profesionales_matricula" value="<%= matricula %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Telefono</label>
                      <input type="text" name="telefono" class="form-control" id="profesionales_telefono" value="<%= telefono %>"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Celular</label>
                      <input type="text" name="celular" class="form-control" id="profesionales_celular" value="<%= celular %>"/>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Direccion</label>
                      <input type="text" name="direccion" class="form-control" id="profesionales_direccion" value="<%= direccion %>"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Localidad</label>
                      <input type="text" name="localidad" class="form-control" id="profesionales_localidad" value="<%= localidad %>"/>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label">Texto</label>
                  <textarea name="texto" class="form-control h100" id="profesionales_texto"><%= texto %></textarea>
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
                  <input type="password" class="form-control" id="profesionales_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Repetir contrase&ntilde;a</label>
                  <input type="password" class="form-control" id="profesionales_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">Horarios de atenci&oacute;n</label>
                  <a id="expand_mapa" class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    Dias y horarios habilitados para reservar turnos.
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Horario de agenda</label>
                      <input type="text" name="hora_desde" class="form-control" id="profesional_hora_desde" value="<%= hora_desde %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Hasta</label>
                      <input type="text" name="hora_hasta" class="form-control" id="profesional_hora_hasta" value="<%= hora_hasta %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Duracion de turno (minutos)</label>
                      <input type="text" name="duracion_turno" class="form-control" id="profesionales_duracion_turno" value="<%= duracion_turno %>"/>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-inline">
                    <label class="control-label tal col-lg-2 col-md-3 col-sm-4 mt5">Lunes</label>
                    <input type="text" id="profesionales_horario_lunes_1" name="horario_lunes_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_lunes_1 %>"/>
                    <input type="text" id="profesionales_horario_lunes_2" name="horario_lunes_2" placeholder="Ej: 12:00" class="form-control w100" value="<%= horario_lunes_2 %>"/>
                    <span class="m-l m-r">-</span>
                    <input type="text" id="profesionales_horario_lunes_3" name="horario_lunes_3" placeholder="Ej: 16:00" class="form-control w100" value="<%= horario_lunes_3 %>"/>
                    <input type="text" id="profesionales_horario_lunes_4" name="horario_lunes_4" placeholder="Ej: 19:00" class="form-control w100" value="<%= horario_lunes_4 %>"/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-inline">
                    <label class="control-label tal col-lg-2 col-md-3 col-sm-4 mt5">Martes</label>
                    <input type="text" id="profesionales_horario_martes_1" name="horario_martes_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_martes_1 %>"/>
                    <input type="text" id="profesionales_horario_martes_2" name="horario_martes_2" placeholder="Ej: 12:00" class="form-control w100" value="<%= horario_martes_2 %>"/>
                    <span class="m-l m-r">-</span>
                    <input type="text" id="profesionales_horario_martes_3" name="horario_martes_3" placeholder="Ej: 16:00" class="form-control w100" value="<%= horario_martes_3 %>"/>
                    <input type="text" id="profesionales_horario_martes_4" name="horario_martes_4" placeholder="Ej: 19:00" class="form-control w100" value="<%= horario_martes_4 %>"/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-inline">
                    <label class="control-label tal col-lg-2 col-md-3 col-sm-4 mt5">Miercoles</label>
                    <input type="text" id="profesionales_horario_miercoles_1" name="horario_miercoles_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_miercoles_1 %>"/>
                    <input type="text" id="profesionales_horario_miercoles_2" name="horario_miercoles_2" placeholder="Ej: 12:00" class="form-control w100" value="<%= horario_miercoles_2 %>"/>
                    <span class="m-l m-r">-</span>
                    <input type="text" id="profesionales_horario_miercoles_3" name="horario_miercoles_3" placeholder="Ej: 16:00" class="form-control w100" value="<%= horario_miercoles_3 %>"/>
                    <input type="text" id="profesionales_horario_miercoles_4" name="horario_miercoles_4" placeholder="Ej: 19:00" class="form-control w100" value="<%= horario_miercoles_4 %>"/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-inline">
                    <label class="control-label tal col-lg-2 col-md-3 col-sm-4 mt5">Jueves</label>
                    <input type="text" id="profesionales_horario_jueves_1" name="horario_jueves_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_jueves_1 %>"/>
                    <input type="text" id="profesionales_horario_jueves_2" name="horario_jueves_2" placeholder="Ej: 12:00" class="form-control w100" value="<%= horario_jueves_2 %>"/>
                    <span class="m-l m-r">-</span>
                    <input type="text" id="profesionales_horario_jueves_3" name="horario_jueves_3" placeholder="Ej: 16:00" class="form-control w100" value="<%= horario_jueves_3 %>"/>
                    <input type="text" id="profesionales_horario_jueves_4" name="horario_jueves_4" placeholder="Ej: 19:00" class="form-control w100" value="<%= horario_jueves_4 %>"/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-inline">
                    <label class="control-label tal col-lg-2 col-md-3 col-sm-4 mt5">Viernes</label>
                    <input type="text" id="profesionales_horario_viernes_1" name="horario_viernes_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_viernes_1 %>"/>
                    <input type="text" id="profesionales_horario_viernes_2" name="horario_viernes_2" placeholder="Ej: 12:00" class="form-control w100" value="<%= horario_viernes_2 %>"/>
                    <span class="m-l m-r">-</span>
                    <input type="text" id="profesionales_horario_viernes_3" name="horario_viernes_3" placeholder="Ej: 16:00" class="form-control w100" value="<%= horario_viernes_3 %>"/>
                    <input type="text" id="profesionales_horario_viernes_4" name="horario_viernes_4" placeholder="Ej: 19:00" class="form-control w100" value="<%= horario_viernes_4 %>"/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-inline">
                    <label class="control-label tal col-lg-2 col-md-3 col-sm-4 mt5">Sabado</label>
                    <input type="text" id="profesionales_horario_sabado_1" name="horario_sabado_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_sabado_1 %>"/>
                    <input type="text" id="profesionales_horario_sabado_2" name="horario_sabado_2" placeholder="Ej: 12:00" class="form-control w100" value="<%= horario_sabado_2 %>"/>
                    <span class="m-l m-r">-</span>
                    <input type="text" id="profesionales_horario_sabado_3" name="horario_sabado_3" placeholder="Ej: 16:00" class="form-control w100" value="<%= horario_sabado_3 %>"/>
                    <input type="text" id="profesionales_horario_sabado_4" name="horario_sabado_4" placeholder="Ej: 19:00" class="form-control w100" value="<%= horario_sabado_4 %>"/>             
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-inline">
                    <label class="control-label tal col-lg-2 col-md-3 col-sm-4 mt5">Domingo</label>
                    <input type="text" id="profesionales_horario_domingo_1" name="horario_domingo_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_domingo_1 %>"/>
                    <input type="text" id="profesionales_horario_domingo_2" name="horario_domingo_2" placeholder="Ej: 12:00" class="form-control w100" value="<%= horario_domingo_2 %>"/>
                    <span class="m-l m-r">-</span>
                    <input type="text" id="profesionales_horario_domingo_3" name="horario_domingo_3" placeholder="Ej: 16:00" class="form-control w100" value="<%= horario_domingo_3 %>"/>
                    <input type="text" id="profesionales_horario_domingo_4" name="horario_domingo_4" placeholder="Ej: 19:00" class="form-control w100" value="<%= horario_domingo_4 %>"/>              
                  </div>
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

<script type="text/template" id="profesionales_edit_mini_panel_template">
<div class="panel pb0 mb0">
  <div class="panel-body">
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Nombre" name="nombre" class="tab form-control" id="profesionales_mini_nombre"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Apellido" name="nombre" class="tab form-control" id="profesionales_mini_apellido"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Direccion" name="direccion" class="tab form-control" id="profesionales_mini_direccion"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="DNI" name="dni" class="tab form-control" id="profesionales_mini_dni"/>
    </div>    
    <div class="form-group">
      <button class="btn guardar btn-success tab btn-block">Guardar</button>
    </div>
  </div>
</div>
</script>



<script type="text/template" id="profesional_turnos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-tags icono_principal"></i>Turnos del dia
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="centrado rform">
      <div class="row">

        <div class="col-md-12">
          <div class="wrapper bg-white b-b">
            <ul class="nav nav-pills nav-sm">
              <li><button class="btn btn-info pendientes">Pendientes</button></li>
              <li><button class="btn btn-none realizados">Realizados</button></li>
            </ul>
          </div>
          <div style="padding-top: 30px" class="streamline b-l b-info m-l-lg m-b padder-v fs14"></div>
        </div>
        
      </div>

    </div>
  </div>
</script>