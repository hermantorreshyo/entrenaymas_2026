<script type="text/template" id="usuarios_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <% if (TOQUE == 1) { %>
    <h1 class="m-n font-thin h3"><i class="fa fa-home icono_principal"></i><b>Comercios</b></h1>
  <% } else if (ID_EMPRESA == 1245 || ID_EMPRESA == 1319) { %>
    <h1 class="m-n font-thin h3"><i class="fa fa-user-md icono_principal"></i><b>Profesionales</b></h1>
  <% } else { %>
    <% var modulo = control.get("usuarios") %>
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuracion","en"=>"Configuration")); ?>
      / <b><%= modulo.title %></b>
    </h1>
  <% } %>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <div class="panel-heading oh">
      <div class="row">
        <div class="col-md-6 col-lg-4 sm-m-b">
          <div class="input-group">
            <input type="text" id="usuarios_buscar" value="<%= window.usuarios_filter %>" placeholder="Buscar por nombre o por email..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
            </span>
            <% if (TOQUE == 0) { %>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
              </span>
            <% } %>
          </div>
        </div>
        <div class="col-md-6 col-lg-offset-2 col-lg-6 text-right">
          <% if (permiso>1) { %>
            <a class="btn btn-info btn-addon" href="app/#<%= (admin==1)?"administrador":link_nuevo %>"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</a>
          <% } %>
        </div>
      </div>
    </div>
    <div class="advanced-search-div bg-light dk">
      <div class="wrapper clearfix pb0">
        <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
        <div class="row pl10 pr10">
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <select id="usuarios_perfiles" class="w100p form-control no-model"></select>
            </div>
          </div>
          <% if (control.check("almacenes")>0) { %>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <select id="usuarios_sucursales" class="w100p form-control no-model">
                  <option value="0">Sucursal</option>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var alm = window.almacenes[i] %>
                    <option <%= (window.usuarios_id_sucursal == alm.id ? "selected":"") %> value="<%= alm.id %>"><%= alm.nombre %></option>
                  <% } %>
                </select>
              </div>
            </div>
          <% } %>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <button class="btn buscar btn-block btn-dark btn-default ml10"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="usuarios_table" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></th>
              <% if (control.check("almacenes")>0) { %>
                <th class="col-xxs-0 col-xs-0">Sucursal</th>
              <% } %>
              <th class="col-xxs-0 col-xs-0">Email</th>
              <% if (TOQUE == 0) { %>
                <th><?php echo lang(array("es"=>"Perfil","en"=>"Profile")); ?></th>
              <% } %>
              <% if (ID_EMPRESA == 1284) { %>
                <th class="col-xxs-0 col-xs-0">Celular</th>
              <% } %>
              <th class="col-xxs-0 col-xs-0 w130">Puntaje</th>
              <% if (permiso > 1) { %>
                <th class="th_acciones <%= (ID_EMPRESA == 1284)?"w180":"w180" %>"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?></th>
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

<script type="text/template" id="usuarios_item">
  <% var clase = (activo==1)?"text-info":"text-muted" %>
  <td class="ver"><span class='ver <%= clase %>'><%= nombre.ucwords() %></span></td>
  <% if (control.check("almacenes")>0) { %>
    <td><span class='ver col-xxs-0 col-xs-0'><%= sucursal %></span></td>
  <% } %>
  <td><span class='ver col-xxs-0 col-xs-0'><%= email.toLowerCase() %></span></td>
  <% if (TOQUE == 0) { %>
    <td><span class='ver'><%= perfil.ucwords() %></span></td>
  <% } %>
  <% if (ID_EMPRESA == 1284) { %>
    <td><%= celular %></td>
  <% } %>
  <td><input class="puntaje form-control" value="<%= puntaje %>"></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i data-toggle="tooltip" title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <% if (TOQUE == 1) { %>
        <i data-toggle="tooltip" title="Habilitar reparto propio" class="fa-motorcycle iconito fa reparto_propio <%= (aparece_web == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="Sumar tiempo de servicio" class="fa-clock-o iconito fa sumar_tiempo_servicio <%= (id_referencia == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="Pickup" class="fa-home iconito fa pickup <%= (ocultar_notificaciones == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="Habilitar Depositos" class="fa-dollar iconito fa habilitar_deposito <%= (custom_1 == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="Entrega Programada" class="fa-calendar iconito fa habilitar_entrega_programada <%= (custom_2 == 1)?"active":"" %>"></i>
      <% } %>

      <% if (ID_EMPRESA == 1245 || ID_EMPRESA == 1319) { %>
        <i data-toggle="tooltip" title="Destacado" class="fa-star warning iconito fa destacado <%= (destacado == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="No calcular puntaje" class="fa-minus danger iconito fa no_calcular_puntaje <%= (no_calcular_puntaje == 1)?"active":"" %>"></i>
      <% } %>

      <% if (ID_EMPRESA == 1284) { %>
        <i data-toggle="tooltip" title="Envio a Coordinar" class="fa-truck iconito fa coordinar_envio <%= (custom_3 == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="Mostrar Horarios" class="fa-clock-o iconito fa sumar_tiempo_servicio <%= (id_referencia == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="Habilitar Reservas" class="fa-calendar iconito fa pickup <%= (ocultar_notificaciones == 1)?"active":"" %>"></i>
      <% } %>

      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <% if (VOLVER_SUPERADMIN == 1 || ID_USUARIO == 0 || (TOQUE == 1 && (PERFIL == 660 || PERFIL == 862) ) || (ID_EMPRESA == 1284 && PERFIL == 1399)) { %>
            <li><a href="javascript:void(0)" class="login" data-id="<%= id %>">Login</a></li>
          <% } %>
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>"><?php echo lang(array("es"=>"Duplicar","en"=>"Duplicate")); ?></a></li>
          <% if (principal == 0) { %>
            <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
          <% } %>
        </ul>
      </div>    
    </td>
  <% } %>
</script>

<script type="text/template" id="usuarios_edit_panel_template">
<% if (ID_EMPRESA == 1245 || ID_EMPRESA == 1319) { %>
  <?php include("custom/profesionales.php"); ?>
<% } else { %>
<div class="bg-light lter b-b wrapper-md">
  <% var modulo = control.get("usuarios") %>
  <% if (TOQUE == 1) { %>
    <h1 class="m-n font-thin h3"><i class="fa fa-home icono_principal"></i><b>Comercios</b>
    </h1>
  <% } else { %>
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuracion","en"=>"Configuration")); ?>
      / <%= modulo.title %>
      / <b><%= (id == undefined)?"<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>":nombre %></b>
    </h1>
  <% } %>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" id="nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="email" class="form-control" id="usuarios_email" value="<%= email %>"/>
                  </div>
                </div>
               </div>

              <% if (TOQUE == 1) { %>
                <div class="form-group">
                  <label class="control-label">Categorias</label>
                  <select id="usuario_toque_categorias" class="w100p"></select>
                </div>
                <div class="form-group">
                  <label class="control-label">Subtitulo</label>
                  <input type="text" <%= (!edicion)?"disabled":"" %> name="cargo" class="form-control" id="usuarios_cargo" value="<%= cargo %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Latitud ; Longitud</label>
                  <input type="text" <%= (!edicion)?"disabled":"" %> name="titulo" class="form-control" id="titulo" value="<%= titulo %>"/>
                </div>
                <% if (PERFIL == 660) { %>
                  <div class="form-group">
                    <label class="control-label">Nivel de destacado</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="destacado" class="form-control" id="usuarios_destacado" value="<%= destacado %>"/>
                  </div>
                <% } %>
              <% } else if (ID_EMPRESA == 1284) { %>
                <div class="form-group">
                  <label class="control-label">Categorias</label>
                  <select id="usuario_toque_categorias" class="w100p"></select>
                </div>
              <% } %>

              <% if (edicion) { %>
                <% if (admin == 0 && ID_PROYECTO != 14 && TOQUE == 0) { %>
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Perfil","en"=>"Profile")); ?></label>
                    <select class="form-control" id="usuarios_perfiles"></select>
                  </div>
                <% } %>
                <% if (control.check("almacenes")>0) { %>
                  <div class="form-group">
                    <label class="control-label">Sucursal</label>
                    <select multiple class="form-control" id="usuarios_sucursales" style="width:100%">
                      <% for (var i=0; i< almacenes.length; i++) { %>
                        <% var almacen = almacenes[i] %>
                        <% var encontro = false %>
                        <% for (var j=0; j< sucursales.length; j++) { %>
                          <% var sc = sucursales[j] %>
                          <% if (sc.id_sucursal == almacen.id) { %>
                            <% encontro = true %>
                            <% break %>
                          <% } %>
                        <% } %>
                        <option <%= (encontro)?"selected":"" %> value="<%= almacen.id %>"><%= almacen.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                <% } %>
              <% } %>
            </div>
          </div>
        </div>

        <% if (edicion || cambiar_password) { %>
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
                  <input type="password" autocomplete="new-password" class="form-control" id="usuarios_password" placeholder="<?php echo lang(array("es"=>"Escriba aqui para cambiar la contrase&ntilde;a","en"=>"Enter here your new password")); ?>"/>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Repetir contrase&ntilde;a","en"=>"Repeat password")); ?></label>
                  <input type="password" autocomplete="new-password" class="form-control" id="usuarios_password_2" placeholder="<?php echo lang(array("es"=>"Escriba nuevamente la contrase&ntilde;a anterior","en"=>"Repeat your new password")); ?> "/>
                </div>
               </div>
            </div>
          </div> 
        <% } %>       

        <% if (ID_PROYECTO == 14) { %>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array("es"=>"Datos personales","en"=>"Personal information")); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Informaci&oacute;n de contacto, foto, etc.",
                      "en"=>"Contact information such as telephone, photo, etc.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="display: block;">
              <div class="padder">

                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Celular","en"=>"Mobile")); ?></label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="celular" class="form-control" id="celular" value="<%= celular %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Cargo","en"=>"Position")); ?></label>
                      <div class="">
                        <input <%= (!edicion)?"disabled":"" %> type="text" name="cargo" class="form-control" id="cargo" value="<%= cargo %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Idioma","en"=>"Language")); ?></label>
                      <div class="">
                        <select <%= (!edicion)?"disabled":"" %> class="form-control" name="language" id="usuario_language">
                          <option <%= (language=="es")?"selected":"" %> value="es"><?php echo lang(array("es"=>"Espa&ntilde;ol","en"=>"Spanish")); ?></option>
                          <option <%= (language=="en")?"selected":"" %> value="en"><?php echo lang(array("es"=>"Ingl&eacute;s","en"=>"English")); ?></option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <?php
                single_upload(array(
                  "name"=>"path",
                  "label"=>lang(array("es"=>"Foto","en"=>"Photo")),
                  "url"=>"/sistema/usuarios/function/save_image/",
                  "width"=>(isset($empresa->config["usuario_image_width"]) ? $empresa->config["usuario_image_width"] : 256),
                  "height"=>(isset($empresa->config["usuario_image_height"]) ? $empresa->config["usuario_image_height"] : 256),
                )); ?>

              </div>
            </div>
          </div>

        <% } else if (ID_EMPRESA == 1284) { %>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">Datos del comercio</label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">Informaci&oacute;n de contacto y redes sociales.</div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Ciudad</label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="custom_1" class="form-control" id="custom_1" value="<%= custom_1 %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Direcci&oacute;n","en"=>"Address")); ?></label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="direccion" class="form-control" id="direccion" value="<%= direccion %>"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Tel&eacute;fono","en"=>"Telephone")); ?></label>
                      <div class="">
                        <input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" class="form-control" id="telefono" value="<%= telefono %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Whatsapp</label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> placeholder="Sin 0 ni 15. Ej: 1112345678" name="celular" class="form-control" id="celular" value="<%= celular %>"/>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Facebook</label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="facebook" placeholder="Copiar URL (https://www.facebook.com/tunegocio) " class="form-control" id="facebook" value="<%= facebook %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Instagram</label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="instagram" class="form-control" placeholder="@TuComercio" id="instagram" value="<%= instagram %>"/>
                      </div>
                    </div>
                  </div>
                </div>

                <?php
                single_upload(array(
                  "name"=>"path",
                  "label"=>lang(array("es"=>"Foto","en"=>"Photo")),
                  "url"=>"/sistema/usuarios/function/save_image/",
                  "width"=>(isset($empresa->config["usuario_image_width"]) ? $empresa->config["usuario_image_width"] : 256),
                  "height"=>(isset($empresa->config["usuario_image_height"]) ? $empresa->config["usuario_image_height"] : 256),
                )); ?>

              </div>
            </div>
          </div>        

        <% } else { %>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array("es"=>"Datos personales","en"=>"Personal information")); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Informaci&oacute;n de contacto, foto, etc.",
                      "en"=>"Contact information such as telephone, photo, etc.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">

                <% if (control.check("vendedores")>0) { %>
                  <div class="form-group">
                    <label class="control-label">Vendedor</label>
                    <select <%= (!edicion)?"disabled":"" %> class="form-control" id="usuario_vendedores"></select>
                  </div>
                <% } %>

                <% if (TOQUE == 1) { %>
                  <div class="form-group">
                    <label class="control-label">Nombre y Apellido</label>
                    <div class="">
                      <input type="text" <%= (!edicion)?"disabled":"" %> name="cargo" class="form-control" id="cargo" value="<%= cargo %>"/>
                    </div>
                  </div>
                <% } %>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <% if (TOQUE == 1) { %>
                        <label class="control-label">CUIT</label>
                      <% } else { %>
                        <label class="control-label"><?php echo lang(array("es"=>"DNI","en"=>"Identification Number")); ?></label>
                      <% } %>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="dni" class="form-control" id="dni" value="<%= dni %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Direcci&oacute;n","en"=>"Address")); ?></label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="direccion" class="form-control" id="direccion" value="<%= direccion %>"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="<%= (TOQUE == 1)?"col-md-6":"col-md-4" %>">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Tel&eacute;fono","en"=>"Telephone")); ?></label>
                      <div class="">
                        <input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" class="form-control" id="telefono" value="<%= telefono %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="<%= (TOQUE == 1)?"col-md-6":"col-md-4" %>">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Celular","en"=>"Mobile")); ?></label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="celular" class="form-control" id="celular" value="<%= celular %>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4" style="<%= (TOQUE == 1)?"display:none":"" %>">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Idioma","en"=>"Language")); ?></label>
                      <div class="">
                        <select <%= (!edicion)?"disabled":"" %> class="form-control" name="language" id="usuario_language">
                          <option <%= (language=="es")?"selected":"" %> value="es"><?php echo lang(array("es"=>"Espa&ntilde;ol","en"=>"Spanish")); ?></option>
                          <option <%= (language=="en")?"selected":"" %> value="en"><?php echo lang(array("es"=>"Ingl&eacute;s","en"=>"English")); ?></option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <% if (TOQUE == 0) { %>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label"><?php echo lang(array("es"=>"Cargo","en"=>"Position")); ?></label>
                        <div class="">
                          <input type="text" <%= (!edicion)?"disabled":"" %> name="cargo" class="form-control" id="cargo" value="<%= cargo %>"/>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label"><?php echo lang(array("es"=>"Titulo","en"=>"Title")); ?></label>
                        <div class="">
                          <input type="text" <%= (!edicion)?"disabled":"" %> name="titulo" class="form-control" id="titulo" value="<%= titulo %>"/>
                        </div>
                      </div>
                    </div>
                  </div>
                <% } else { %>
                  <% if (PERFIL == 660) { %>
                    <div class="form-group">
                      <label class="control-label">Clave para Dashboard</label>
                      <div class="">
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="clave_especial" class="form-control" id="clave_especial" value="<%= clave_especial %>"/>
                      </div>
                    </div>
                  <% } %>
                <% } %>

                <?php
                single_upload(array(
                  "name"=>"path",
                  "label"=>lang(array("es"=>"Foto","en"=>"Photo")),
                  "url"=>"/sistema/usuarios/function/save_image/",
                  "width"=>(isset($empresa->config["usuario_image_width"]) ? $empresa->config["usuario_image_width"] : 256),
                  "height"=>(isset($empresa->config["usuario_image_height"]) ? $empresa->config["usuario_image_height"] : 256),
                )); ?>

                <?php
                single_upload(array(
                  "name"=>"path_2",
                  "label"=>lang(array("es"=>"Portada","en"=>"Portada")),
                  "url"=>"/sistema/usuarios/function/save_image/",
                  "width"=>(isset($empresa->config["usuario_image_2_width"]) ? $empresa->config["usuario_image_2_width"] : 256),
                  "height"=>(isset($empresa->config["usuario_image_2_height"]) ? $empresa->config["usuario_image_2_height"] : 256),
                )); ?>

                <div class="form-group" style="<%= (TOQUE == 1)?"display:none":"" %>">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" <%= (!edicion)?"disabled":"" %> name="estado_inicial" class="checkbox" value="1" <%= (estado_inicial == 1)?"checked":"" %>><i></i>
                      Entrar en el sistema como Supervisor
                    </label>
                  </div>          
                </div>
                <% if (ID_PROYECTO == 10) { %>
                  <div class="form-group" style="<%= (TOQUE == 1)?"display:none":"" %>">
                    <div class="checkbox">
                      <label class="i-checks">
                        <input type="checkbox" <%= (!edicion)?"disabled":"" %> name="ocultar_notificaciones" class="checkbox" value="1" <%= (ocultar_notificaciones == 1)?"checked":"" %>><i></i>
                        Ocultar notificaciones para este usuario
                      </label>
                    </div>          
                  </div>
                <% } %>

                <div class="form-group" style="<%= (TOQUE == 1)?"display:none":"" %>">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" <%= (!edicion)?"disabled":"" %> name="aparece_web" class="checkbox" value="1" <%= (aparece_web == 1)?"checked":"" %>><i></i>
                      <?php echo lang(array("es"=>"Habilitar el usuario para que aparezca en la web","en"=>"Enable the user to appear on the web")); ?>
                    </label>
                  </div>
                </div>

                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="solo_usuario" class="checkbox" value="1" <%= (solo_usuario == 1)?"checked":"" %>><i></i>
                      <?php echo lang(array("es"=>"Mostrar solamente la informacion correspondiente al usuario","en"=>"Show only the information created by the user")); ?>
                    </label>
                  </div>        
                </div>

              </div>
            </div>
          </div>
        <% } %>

        <% if (control.check("estadisticas_whatsapp")>0 || TOQUE == 1 || ID_EMPRESA == 1284) { %>
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
                    <% if (TOQUE == 1 || ID_EMPRESA == 1284) { %>
                      Aqui puede configurar los dias y horarios que atendera el comercio.
                    <% } else { %>
                      Aqui puede configurar los dias y horarios que aparecer&aacute; disponible para recibir consultas.
                    <% } %>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (horarios.length > 0)?'display:block':'' %>">
              <div class="padder">
                <div class="row clearfix">
                  <div class="form-group col-sm-4">
                    <label class="control-label">Dia de la semana</label>
                    <select id="usuario_horario_dia" class="form-control no-model" style="width: 100%">
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
                    <label class="control-label">Hora desde</label>
                    <input type="text" id="usuario_horario_desde" class="form-control">
                  </div>
                  <div class="form-group col-sm-2">
                    <label class="control-label">Hora hasta</label>
                    <input type="text" id="usuario_horario_hasta" class="form-control">
                  </div>
                  <div class="form-group col-sm-2">
                    <label class="control-label">&nbsp;</label>
                    <a id="horario_agregar" class="btn btn-info btn-block">+ Agregar</a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="usuario_horarios_tabla" class="table m-b-none default footable">
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
                <% if (TOQUE == 0 && ID_EMPRESA != 1284) { %>
                  <div class="form-group">
                    <label class="control-label">En caso de estar fuera de horario:</label>
                    <div class="">
                      <select class="form-control" name="ocultar_notificaciones" id="usuario_ocultar_notificaciones">
                        <option <%= (ocultar_notificaciones=="0")?"selected":"" %> value="0"><?php echo lang(array("es"=>"Marcar como no disponible y mostrar formulario de contacto","en"=>"Set the user as not available and show the form.")); ?></option>
                        <option <%= (ocultar_notificaciones=="1")?"selected":"" %> value="1"><?php echo lang(array("es"=>"No mostrar el usuario","en"=>"Hide the user")); ?></option>
                      </select>
                    </div>
                  </div>
                <% } %>
              </div>
            </div>
          </div>
        <% } %>

        <% if (ID_EMPRESA == 1234) { %>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">Horarios de entrega</label>
                  <a id="expand_mapa" class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    Aqui puede configurar los dias y horarios que entregará los pedidos.
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (horarios_entrega.length > 0)?'display:block':'' %>">
              <div class="padder">
                <div class="row clearfix">
                  <div class="form-group col-sm-4">
                    <label class="control-label">Dia de la semana</label>
                    <select id="usuario_horario_entrega_dia" class="form-control no-model" style="width: 100%">
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
                    <label class="control-label">Hora desde</label>
                    <input type="text" id="usuario_horario_entrega_desde" class="form-control">
                  </div>
                  <div class="form-group col-sm-2">
                    <label class="control-label">Hora hasta</label>
                    <input type="text" id="usuario_horario_entrega_hasta" class="form-control">
                  </div>
                  <div class="form-group col-sm-2">
                    <label class="control-label">&nbsp;</label>
                    <a id="horario_entrega_agregar" class="btn btn-info btn-block">+ Agregar</a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="usuario_horario_entregas_tabla" class="table m-b-none default footable">
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
                      <% for(var i=0;i< horarios_entrega.length;i++) { %>
                        <% var p = horarios_entrega[i] %>
                        <tr>
                          <td class='dn dia'><%= p.dia %></td>
                          <td class="editar_horario_entrega">
                            <span class="text-info editar_horario_entrega">
                              <%= (p.dia==1)?"Lunes":"" %>
                              <%= (p.dia==2)?"Martes":"" %>
                              <%= (p.dia==3)?"Miercoles":"" %>
                              <%= (p.dia==4)?"Jueves":"" %>
                              <%= (p.dia==5)?"Viernes":"" %>
                              <%= (p.dia==6)?"Sabado":"" %>
                              <%= (p.dia==7)?"Domingo":"" %>
                            </span>
                          </td>
                          <td class="desde editar_horario_entrega"><%= p.desde.substr(0,5) %></td>
                          <td class="hasta editar_horario_entrega"><%= p.hasta.substr(0,5) %></td>
                          <td class="tar">
                            <button class="btn btn-sm btn-white eliminar_horario_entrega"><i class="fa fa-trash"></i></button>
                          </td>
                        </tr>
                      <% } %>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    Datos de MercadoPago
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    Si desea habilitar MercadoPago, ingrese los siguientes datos
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">   

                <div class="form-group">
                  <label class="control-label tal">Client ID</label>
                  <input type="text" name="custom_1" class="form-control" value="<%= custom_1 %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label tal">Client Secret</label>
                  <input type="text" name="custom_2" class="form-control" value="<%= custom_2 %>"/>
                </div>
                <div class="form-group">
                  <a class="btn btn-default" href="https://www.mercadopago.com/mla/account/credentials?type=basic" target="_blank">Obtener credenciales</a>
                </div>
              </div>
            </div>
          </div>
        <% } %>
        
        <?php /* GRUPO URBANO UTILIZA ESTAS OPCIONES */ ?>
        <% if (ID_EMPRESA == 45 || MILLING == 1) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <% if (ID_EMPRESA == 45) { %>
                  <?php
                  single_file_upload(array(
                    "label"=>"Archivo para descargar",
                    "name"=>"archivo",
                    "url"=>"/sistema/usuarios/function/save_file/",
                  )); ?>
                <% } %>
              </div>
            </div>
          </div>
        <% } %>
      </div>
    </div>  

    <% if (edicion || cambiar_password) { %>
      <div class="line b-b m-b-lg"></div>
      <div class="row">
        <div class="col-md-10 col-md-offset-1 tar">
          <button class="btn btn-success guardar"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
        </div>
      </div>
    <% } %>

  </div>
</div>
<% } %>
</script>