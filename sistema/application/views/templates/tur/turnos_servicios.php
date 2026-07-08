<script type="text/template" id="turnos_servicios_panel_template">
  <% if (lightbox) { %>
    <div class="b-a table-responsive">
      <table id="turnos_servicios_table" class="table table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th class="sorting" data-sort-by="nombre">Nombre de la Tarifa</th>
            <th class="w25 th_acciones"></th>
            <th class="w25 th_acciones"></th>
          </tr>
        </thead>
        <tbody class="tbody"></tbody>
      </table>
    </div>
  <% } else { %>
    <div class="seccion_llena">
      <div class="bg-light lter b-b wrapper-md">
        <% var modulo = control.get("turnos_servicios") %>
        <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
      </div>
      <div class="wrapper-md">
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-3 sm-m-b">
                <div class="input-group">
                  <input type="text" id="turnos_servicios_buscar" value="<%= window.turnos_servicios_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
                  <span class="input-group-btn">
                    <button class="btn btn-default"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
              <% if (permiso > 2) { %>
              <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                <a class="btn btn-info btn-addon" href="app/#turno_servicio">
                  <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
                </a>
              </div>
              <% } %>
            </div>
          </div>
          <div class="panel-body">
            <div class="b-a table-responsive">
              <table id="turnos_servicios_table" class="table table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th class="sorting" data-sort-by="nombre">Nombre de la Tarifa</th>
                    <th class="sorting" data-sort-by="usuario">Usuario</th>
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
  <% } %>
</script>

<script type="text/template" id="turnos_servicios_item">
  <% if (lightbox) { %>
    <td class='data'><span class="text-info"><%= nombre %></span></td>
    <td class="tar td_acciones">
      <button class="btn btn-white data"><i class="fa fa-pencil"></i></button>
    </td>    
    <td class="tar td_acciones">
      <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
    </td>    
  <% } else { %>
  	<td class='data'><span class="text-info"><%= nombre %></span></td>
    <td class='data'><%= usuario %></td>
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
  <% } %>
</script>

<script type="text/template" id="turnos_servicios_edit_panel_template">
  <?php // SI SE USA COMO LIGHTBOX EN LOS PSICOLOGOS POR EJEMPLO ?>
  <% if (lightbox) { %>
    <div class="modal-header">
      <b><%= (ID_EMPRESA == 1319) ? "Tarifa" : "Dirección" %></b>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="control-label"><%= (ID_EMPRESA == 1245)?"Lugar de Atención":"Nombre" %></label>
        <input type="text" name="nombre" class="form-control" placeholder="<%= (ID_EMPRESA == 1245)?"Ej: Consultorio particular, Clínica del Sur, etc.":"" %>" id="turnos_servicios_nombre" value="<%= nombre %>"/>
      </div>      
      <div class="row <%= (ID_EMPRESA == 1319) ? "dn" : "" %>">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">País</label>
            <select class="form-control" name="pais" id="turno_servicio_pais">
              <option <%= (pais == "Argentina")?"selected":"" %> value="Argentina">Argentina</option>
            </select>
          </div>
        </div>        
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Provincia</label>
            <select class="form-control" name="provincia" id="turno_servicio_provincia">
              <option <%= (id_provincia == "24")?"selected":"" %> value="24">Capital Federal</option>
              <option <%= (id_provincia == "1")?"selected":"" %> value="1">Buenos Aires</option>
              <option <%= (id_provincia == "2")?"selected":"" %> value="2">Catamarca</option>
              <option <%= (id_provincia == "3")?"selected":"" %> value="3">Chaco</option>
              <option <%= (id_provincia == "4")?"selected":"" %> value="4">Chubut</option>
              <option <%= (id_provincia == "5")?"selected":"" %> value="5">Cordoba</option>
              <option <%= (id_provincia == "6")?"selected":"" %> value="6">Corrientes</option>
              <option <%= (id_provincia == "7")?"selected":"" %> value="7">Entre Rios</option>
              <option <%= (id_provincia == "8")?"selected":"" %> value="8">Formosa</option>
              <option <%= (id_provincia == "9")?"selected":"" %> value="9">Jujuy</option>
              <option <%= (id_provincia == "10")?"selected":"" %> value="10">La Pampa</option>
              <option <%= (id_provincia == "11")?"selected":"" %> value="11">La Rioja</option>
              <option <%= (id_provincia == "12")?"selected":"" %> value="12">Mendoza</option>
              <option <%= (id_provincia == "13")?"selected":"" %> value="13">Misiones</option>
              <option <%= (id_provincia == "14")?"selected":"" %> value="14">Neuquen</option>
              <option <%= (id_provincia == "15")?"selected":"" %> value="15">Rio Negro</option>
              <option <%= (id_provincia == "16")?"selected":"" %> value="16">Salta</option>
              <option <%= (id_provincia == "17")?"selected":"" %> value="17">San Juan</option>
              <option <%= (id_provincia == "18")?"selected":"" %> value="18">San Luis</option>
              <option <%= (id_provincia == "19")?"selected":"" %> value="19">Santa Cruz</option>
              <option <%= (id_provincia == "20")?"selected":"" %> value="20">Santa Fe</option>
              <option <%= (id_provincia == "21")?"selected":"" %> value="21">Santiago Del Estero</option>
              <option <%= (id_provincia == "22")?"selected":"" %> value="22">Tierra Del Fuego</option>
              <option <%= (id_provincia == "23")?"selected":"" %> value="23">Tucuman</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row <%= (ID_EMPRESA == 1319) ? "dn" : "" %>">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Ciudad</label>
            <input type="text" name="localidad" class="form-control" id="turno_servicio_localidad" value="<%= localidad %>"/>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Dirección</label>
            <input type="text" name="direccion" placeholder="Ej: Juncal 1900" class="form-control" id="turnos_servicios_direccion" value="<%= direccion %>"/>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Costo</label>
            <input type="text" name="costo" class="form-control" id="turnos_servicios_costo" value="<%= costo %>"/>
          </div>
        </div>                  
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Duración (minutos)</label>
            <input type="text" name="duracion_turno" class="form-control" id="turnos_servicios_duracion_turno" value="<%= duracion_turno %>"/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Color de agenda</label>
            <div class="input-group color colorpicker-component">
              <input type="text" class="form-control" value="<%= color %>" />
              <span class="input-group-addon"><i></i></span>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-description bold mb10">
        Días y horarios habilitados para reservar turnos.
      </div>
      <div class="row clearfix">
        <div class="form-group col-sm-4">
          <select id="turnos_servicios_horario_dia" class="form-control no-model" style="width: 100%">
            <option value="1">Lunes</option>
            <option value="2">Martes</option>
            <option value="3">Miercoles</option>
            <option value="4">Jueves</option>
            <option value="5">Viernes</option>
            <option value="6">Sabado</option>
            <option value="7">Domingo</option>
          </select>
        </div>
        <div class="form-group col-sm-4">
          <input type="text" placeholder="Desde" id="turnos_servicios_horario_desde" class="form-control">
        </div>
        <div class="form-group col-sm-4">
          <div class="input-group">
            <input type="text" placeholder="Hasta" id="turnos_servicios_horario_hasta" class="form-control w-md">
            <span class="input-group-btn">
              <a id="horario_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
            </span>
          </div>
        </div>                  
      </div>
      <div class="table-responsive">
        <table id="turnos_servicios_horarios_tabla" class="table m-b-none default footable">
          <thead>
            <tr>
              <th style="display: none"></th>
              <th>Dia de la semana</th>
              <th>Desde</th>
              <th>Hasta</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <% for(var i=0;i< horarios.length;i++) { %>
              <% var p = horarios[i] %>
              <tr>
                <td class='dn dia'><%= p.dia %></td>
                <td class="editar_horario">
                  <span class="text-info editar_horario">
                    <%= (p.dia==1)?"Lunes":"" %>
                    <%= (p.dia==2)?"Martes":"" %>
                    <%= (p.dia==3)?"Miercoles":"" %>
                    <%= (p.dia==4)?"Jueves":"" %>
                    <%= (p.dia==5)?"Viernes":"" %>
                    <%= (p.dia==6)?"Sabado":"" %>
                    <%= (p.dia==7)?"Domingo":"" %>
                  </span>
                </td>
                <td class="desde editar_horario"><%= p.desde.substr(0,5) %></td>
                <td class="hasta editar_horario"><%= p.hasta.substr(0,5) %></td>
                <td class="tar">
                  <button class="btn btn-sm btn-white eliminar_horario"><i class="fa fa-trash"></i></button>
                </td>
              </tr>
            <% } %>
          </tbody>
        </table>
      </div>

      <p class="bold">Desactivar el servicio entre las fechas:</p>
      <div class="row">
        <div class="col-md-4">
          <label class="control-label">Desde</label>
          <div class="input-group">
            <input type="text" name="deshabilitado_desde" id="turnos_servicios_deshabilitado_desde" value="<%= deshabilitado_desde %>" class="form-control"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
        <div class="col-md-4">
          <label class="control-label">Hasta</label>
          <div class="input-group">
            <input type="text" name="deshabilitado_hasta" id="turnos_servicios_deshabilitado_hasta" value="<%= deshabilitado_hasta %>" class="form-control"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer tar">
      <button class="btn guardar btn-success">Guardar</button>
    </div>

  <?php // SI ES UN TURNO COMUN ?>
  <% } else { %>

    <div class="bg-light lter b-b wrapper-md ng-scope">
      <% var modulo = control.get("turnos_servicios") %>
      <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %> / 
        <b><%= (id == undefined)?"Nuevo":nombre %></b>
      </h1>
    </div>
    <div class="wrapper-md">
      <div class="centrado rform">
        <div class="row">

          <div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
              <div class="panel-body">
                <div class="padder">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" id="turnos_servicios_nombre" value="<%= nombre %>"/>
                  </div>

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Costo</label>
                        <input type="text" name="costo" class="form-control" id="turnos_servicios_costo" value="<%= costo %>"/>
                      </div>
                    </div>                  
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Duración (minutos)</label>
                        <input type="text" name="duracion_turno" class="form-control" id="turnos_servicios_duracion_turno" value="<%= duracion_turno %>"/>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Color de agenda</label>
                        <div class="input-group color colorpicker-component">
                          <input type="text" class="form-control" value="<%= color %>" />
                          <span class="input-group-addon"><i></i></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Usuario</label>
                        <select id="turnos_servicios_usuarios" class="form-control" name="id_usuario">
                          <% for(var i=0;i< window.usuarios.size(); i++) { %>
                            <% var usuario = window.usuarios.models[i] %>
                            <option <%= (id_usuario == usuario.id)?"selected":"" %> value="<%= usuario.id %>"><%= usuario.get("nombre") %></option>
                          <% } %>
                        </select>
                      </div>
                    </div>

                    <?php /*
                    <div class="col-md-4">
                      <?php
                      single_upload(array(
                        "name"=>"path",
                        "label"=>"Foto",
                        "url"=>"turnos_servicios/function/save_image/",
                        "width"=>(isset($empresa->config["turno_servicio_image_width"]) ? $empresa->config["turno_servicio_image_width"] : 400),
                        "height"=>(isset($empresa->config["turno_servicio_image_height"]) ? $empresa->config["turno_servicio_image_height"] : 400),
                        "quality"=>(isset($empresa->config["turno_servicio_image_quality"]) ? $empresa->config["turno_servicio_image_quality"] : 0),
                        "thumbnail_width"=>(isset($empresa->config["turno_servicio_thumbnail_width"]) ? $empresa->config["turno_servicio_thumbnail_width"] : 0),
                        "thumbnail_height"=>(isset($empresa->config["turno_servicio_thumbnail_height"]) ? $empresa->config["turno_servicio_thumbnail_height"] : 0),
                      )); ?>
                    </div>
                    */ ?>
                  </div>

                  <div class="form-group">
                    <label class="control-label">Descripción</label>
                    <textarea name="texto" class="form-control h100" id="turnos_servicios_texto"><%= texto %></textarea>
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
                      Días y horarios habilitados para reservar turnos.
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-body expand" style="display: block;">
                <div class="padder">                
                  <div class="m-b row clearfix">
                    <div class="form-group col-sm-4">
                      <label class="control-label">Dia de la semana</label>
                      <select id="turnos_servicios_horario_dia" class="form-control no-model" style="width: 100%">
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miercoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sabado</option>
                        <option value="7">Domingo</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-2">
                      <label class="control-label">Desde</label>
                      <input type="text" id="turnos_servicios_horario_desde" class="form-control">
                    </div>
                    <div class="form-group col-sm-3">
                      <label class="control-label">Hasta</label>
                      <div class="input-group">
                        <input type="text" id="turnos_servicios_horario_hasta" class="form-control w-md">
                        <span class="input-group-btn">
                          <a id="horario_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                        </span>
                      </div>
                    </div>                  
                  </div>
                  <div class="table-responsive">
                    <table id="turnos_servicios_horarios_tabla" class="table m-b-none default footable">
                      <thead>
                        <tr>
                          <th style="display: none"></th>
                          <th>Dia de la semana</th>
                          <th>Desde</th>
                          <th>Hasta</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <% for(var i=0;i< horarios.length;i++) { %>
                          <% var p = horarios[i] %>
                          <tr>
                            <td class='dn dia'><%= p.dia %></td>
                            <td class="editar_horario">
                              <span class="text-info editar_horario">
                                <%= (p.dia==1)?"Lunes":"" %>
                                <%= (p.dia==2)?"Martes":"" %>
                                <%= (p.dia==3)?"Miercoles":"" %>
                                <%= (p.dia==4)?"Jueves":"" %>
                                <%= (p.dia==5)?"Viernes":"" %>
                                <%= (p.dia==6)?"Sabado":"" %>
                                <%= (p.dia==7)?"Domingo":"" %>
                              </span>
                            </td>
                            <td class="desde editar_horario"><%= p.desde.substr(0,5) %></td>
                            <td class="hasta editar_horario"><%= p.hasta.substr(0,5) %></td>
                            <td class="tar">
                              <button class="btn btn-sm btn-white eliminar_horario"><i class="fa fa-trash"></i></button>
                            </td>
                          </tr>
                        <% } %>
                      </tbody>
                    </table>
                  </div>

                  <p>Desactivar el servicio entre las fechas:</p>
                  <div class="row">
                    <div class="col-md-4">
                      <label class="control-label">Desde</label>
                      <div class="input-group">
                        <input type="text" name="deshabilitado_desde" id="turnos_servicios_deshabilitado_desde" value="<%= deshabilitado_desde %>" class="form-control"/>
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>              
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label class="control-label">Hasta</label>
                      <div class="input-group">
                        <input type="text" name="deshabilitado_hasta" id="turnos_servicios_deshabilitado_hasta" value="<%= deshabilitado_hasta %>" class="form-control"/>
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>              
                      </div>
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
  <% } %>
</script>