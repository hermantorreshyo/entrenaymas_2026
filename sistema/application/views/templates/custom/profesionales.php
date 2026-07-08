<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-user-md icono_principal"></i><b>Profesionales</b></h1>
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
                  <label class="control-label"><?php echo lang(array("es"=>"Nombre Completo","en"=>"Full Name")); ?></label>
                  <input type="text" <%= (!edicion || (PERFIL == 1357 || PERFIL == 1358))?"disabled":"" %> name="nombre" class="form-control" id="nombre" value="<%= nombre %>"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Email</label>
                  <input type="text" <%= (!edicion || id != undefined)?"disabled":"" %> name="email" class="form-control" id="usuarios_email" value="<%= email %>"/>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Perfil</label>
                  <select class="form-control" <%= (!edicion || (PERFIL == 1357 || PERFIL == 1358))?"disabled":"" %> id="profesional_id_perfiles" name="id_perfiles">
                    <option <%= (id_perfiles == 1357) ? 'selected' : '' %> value="1357">Profesional Básico</option>
                    <option <%= (id_perfiles == 1358) ? 'selected' : '' %> value="1358">Profesional Premium</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label">Títulos</label>
              <select id="usuario_titulos" class="w100p"></select>
            </div>            

            <div class="form-group <%= (ID_EMPRESA == 1319)?"dn":"" %>">
              <label class="control-label">Nro. de Matrícula</label>
              <input type="text" <%= (!edicion || (PERFIL == 1357 || PERFIL == 1358))?"disabled":"" %> name="cargo" class="form-control" id="usuarios_cargo" value="<%= cargo %>"/>
            </div>

            <div class="form-group">
              <% if (ID_EMPRESA == 1245) { %>
                <label class="control-label mb0">Temáticas más buscadas por los pacientes</label>
                <div class="text-muted fs14 mb5">A continuación podrá cargar todas las temáticas o áreas de trabajo:</div>
              <% } else if (ID_EMPRESA == 1319) { %>
                <label class="control-label mb0">Especialidades</label>
              <% } else { %>
                <label class="control-label mb0">Áreas de trabajo</label>
              <% } %>
              <select id="usuario_toque_categorias" class="w100p"></select>
            </div>

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
                  "es"=>"Informaci&oacute;n de contacto, redes sociales y foto de perfil.",
                  "en"=>"Contact information such as telephone, photo, etc.",
                )); ?>                  
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body expand" style="display: block;">
          <div class="padder">

            <% if (ID_EMPRESA == 1319) { %>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Provincia</label>
                    <select class="form-control" name="id_provincia" id="usuario_eym_provincias">
                      <option <%= (id_provincia == 1518) ? "selected" : "" %> value="1518">A Coruña</option>
                      <option <%= (id_provincia == 1501) ? "selected" : "" %> value="1501">Álava</option>
                      <option <%= (id_provincia == 1505) ? "selected" : "" %> value="1505">Albacete</option>
                      <option <%= (id_provincia == 1509) ? "selected" : "" %> value="1509">Alicante</option>
                      <option <%= (id_provincia == 1513) ? "selected" : "" %> value="1513">Almería</option>
                      <option <%= (id_provincia == 1517) ? "selected" : "" %> value="1517">Asturias</option>
                      <option <%= (id_provincia == 1521) ? "selected" : "" %> value="1521">Ávila</option>
                      <option <%= (id_provincia == 1525) ? "selected" : "" %> value="1525">Badajoz</option>
                      <option <%= (id_provincia == 1529) ? "selected" : "" %> value="1529">Baleares</option>
                      <option <%= (id_provincia == 1533) ? "selected" : "" %> value="1533">Barcelona</option>
                      <option <%= (id_provincia == 1537) ? "selected" : "" %> value="1537">Burgos</option>
                      <option <%= (id_provincia == 1541) ? "selected" : "" %> value="1541">Cáceres</option>
                      <option <%= (id_provincia == 1545) ? "selected" : "" %> value="1545">Cádiz</option>
                      <option <%= (id_provincia == 1549) ? "selected" : "" %> value="1549">Cantabria</option>
                      <option <%= (id_provincia == 1502) ? "selected" : "" %> value="1502">Castellón</option>
                      <option <%= (id_provincia == 1510) ? "selected" : "" %> value="1510">Ciudad Real</option>
                      <option <%= (id_provincia == 1514) ? "selected" : "" %> value="1514">Córdoba</option>
                      <option <%= (id_provincia == 1522) ? "selected" : "" %> value="1522">Cuenca</option>
                      <option <%= (id_provincia == 1526) ? "selected" : "" %> value="1526">Girona</option>
                      <option <%= (id_provincia == 1530) ? "selected" : "" %> value="1530">Granada</option>
                      <option <%= (id_provincia == 1534) ? "selected" : "" %> value="1534">Guadalajara</option>
                      <option <%= (id_provincia == 1538) ? "selected" : "" %> value="1538">Guipúzcoa</option>
                      <option <%= (id_provincia == 1542) ? "selected" : "" %> value="1542">Huelva</option>
                      <option <%= (id_provincia == 1546) ? "selected" : "" %> value="1546">Huesca</option>
                      <option <%= (id_provincia == 1550) ? "selected" : "" %> value="1550">Jaén</option>
                      <option <%= (id_provincia == 1551) ? "selected" : "" %> value="1551">La Rioja</option>
                      <option <%= (id_provincia == 1543) ? "selected" : "" %> value="1543">Las Palmas</option>
                      <option <%= (id_provincia == 1503) ? "selected" : "" %> value="1503">León</option>
                      <option <%= (id_provincia == 1507) ? "selected" : "" %> value="1507">Lleida</option>
                      <option <%= (id_provincia == 1511) ? "selected" : "" %> value="1511">Lugo</option>
                      <option <%= (id_provincia == 1515) ? "selected" : "" %> value="1515">Madrid</option>
                      <option <%= (id_provincia == 1519) ? "selected" : "" %> value="1519">Málaga</option>
                      <option <%= (id_provincia == 1527) ? "selected" : "" %> value="1527">Murcia</option>
                      <option <%= (id_provincia == 1531) ? "selected" : "" %> value="1531">Navarra</option>
                      <option <%= (id_provincia == 1535) ? "selected" : "" %> value="1535">Ourense</option>
                      <option <%= (id_provincia == 1539) ? "selected" : "" %> value="1539">Palencia</option>
                      <option <%= (id_provincia == 1547) ? "selected" : "" %> value="1547">Pontevedra</option>
                      <option <%= (id_provincia == 1524) ? "selected" : "" %> value="1524">S.C. Tenerife</option>
                      <option <%= (id_provincia == 1504) ? "selected" : "" %> value="1504">Salamanca</option>
                      <option <%= (id_provincia == 1508) ? "selected" : "" %> value="1508">Segovia</option>
                      <option <%= (id_provincia == 1512) ? "selected" : "" %> value="1512">Sevilla</option>
                      <option <%= (id_provincia == 1516) ? "selected" : "" %> value="1516">Soria</option>
                      <option <%= (id_provincia == 1520) ? "selected" : "" %> value="1520">Tarragona</option>
                      <option <%= (id_provincia == 1528) ? "selected" : "" %> value="1528">Teruel</option>
                      <option <%= (id_provincia == 1532) ? "selected" : "" %> value="1532">Toledo</option>
                      <option <%= (id_provincia == 1536) ? "selected" : "" %> value="1536">Valencia</option>
                      <option <%= (id_provincia == 1540) ? "selected" : "" %> value="1540">Valladolid</option>
                      <option <%= (id_provincia == 1544) ? "selected" : "" %> value="1544">Vizcaya</option>
                      <option <%= (id_provincia == 1548) ? "selected" : "" %> value="1548">Zamora</option>
                      <option <%= (id_provincia == 1552) ? "selected" : "" %> value="1552">Zaragoza</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Localidad</label>
                    <select id="usuario_eym_localidades" name="id_localidad" class="form-control"></select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Dirección</label>
                    <input type="text" name="direccion" placeholder="Ej: Juncal 1900" class="form-control" id="usuario_eym_direccion" value="<%= direccion %>"/>
                  </div>
                </div>
              </div>
            <% } %>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Teléfono Fijo</label>
                  <div class="">
                    <input placeholder="Ej: 221 1234567" type="text" <%= (!edicion)?"disabled":"" %> name="telefono" class="form-control" id="telefono" value="<%= telefono %>"/>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <% if (ID_EMPRESA == 1319) { %>
                  <div class="form-group">
                    <label class="control-label">Teléfono Móvil</label>
                    <div class="">
                      <input type="text" placeholder="" <%= (!edicion)?"disabled":"" %> name="celular" class="form-control" id="celular" value="<%= celular %>"/>
                    </div>
                  </div>
                <% } else { %>
                  <div class="form-group">
                    <label class="control-label">Celular</label>
                    <div class="">
                      <input type="text" placeholder="Ej: 549 221 1234567" <%= (!edicion)?"disabled":"" %> name="celular" class="form-control" id="celular" value="<%= celular %>"/>
                    </div>
                  </div>
                <% } %>
              </div>
              <div class="col-md-4 <%= (PERFIL == 1357)?"dn":"" %>">
                <div class="form-group">
                  <label class="control-label">Web</label>
                  <div class="">
                    <input placeholder="Ej: www.google.com" <%= (!edicion)?"disabled":"" %> type="text" name="custom_5" class="form-control" id="custom_5" value="<%= custom_5 %>"/>
                  </div>
                </div>
              </div>
            </div>

            <div class="row <%= (PERFIL == 1357 ? "dn" : "") %>">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Facebook</label>
                  <div class="">
                    <input type="text" placeholder="Ej: www.facebook.com/miperfil" <%= (!edicion)?"disabled":"" %> name="facebook" class="form-control" id="facebook" value="<%= facebook %>"/>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Instagram</label>
                  <div class="">
                    <input type="text" placeholder="Ej: www.instagram.com/miperfil" <%= (!edicion)?"disabled":"" %> name="instagram" class="form-control" id="instagram" value="<%= instagram %>"/>
                  </div>
                </div>
              </div>
              <!-- A TWITTER LO TRATAMOS COMO LINKEDIN -->
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Twitter</label>
                  <div class="">
                    <input placeholder="Ej: www.twitter.com/miperfil" <%= (!edicion)?"disabled":"" %> type="text" name="linkedin" class="form-control" id="linkedin" value="<%= linkedin %>"/>
                  </div>
                </div>
              </div>
            </div>

            <?php
            single_upload(array(
              "name"=>"path",
              "label"=>lang(array("es"=>"Foto de Perfil","en"=>"Photo")),
              "url"=>"/sistema/usuarios/function/save_image/",
              "width"=>(isset($empresa->config["usuario_image_width"]) ? $empresa->config["usuario_image_width"] : 256),
              "height"=>(isset($empresa->config["usuario_image_height"]) ? $empresa->config["usuario_image_height"] : 256),
            )); ?>
            <div class="text-muted mb15">
              <% if (ID_EMPRESA == 1319) { %>
                Recomendamos el uso de una foto de la cara o una foto hasta la cintura. No uses fotos que no te identifiquen. Recuerda que es la primera impresión que se llevará el cliente/paciente. <a class="text-info" href="">Ver instructivo de foto de perfil.</a>
              <% } else { %>
                Se sugiere el uso de una foto personal de cara o una toma hasta el torso. No uses fotos que no te identifiquen. Recuerda que es la primera impresión que llevará el paciente. <a class="text-info" href="">Ver instructivo de foto de perfil.</a>
              <% } %>
            </div>

            <div class="form-group">
              <label class="control-label">Defínete en 150 caracteres</label>
              <div class="">
                <textarea maxlength="150" <%= (!edicion)?"disabled":"" %> name="sobre_mi" class="form-control" id="sobre_mi"><%= sobre_mi %></textarea>
              </div>
            </div>    

            <div class="form-group">
              <label class="control-label">Sobre Mi</label>
              <div class="">
                <textarea <%= (!edicion)?"disabled":"" %> name="custom_3" class="form-control" id="custom_3"><%= custom_3 %></textarea>
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
                Datos Profesionales
              </label>
              <a class="expand-link fr">
                <?php echo lang(array(
                  "es"=>"+ Ver opciones",
                  "en"=>"+ View options",
                )); ?>
              </a>
              <div class="panel-description">
                <% if (ID_EMPRESA == 1319) { %>
                  Información relativa a tus servicios
                <% } else { %>
                  Información relativa a consultas, especialidades, etc.
                <% } %>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body expand" style="display: block;">
          <div class="padder">

            <% if (ID_EMPRESA != 1319) { %>
              <div class="form-group">
                <label class="control-label">Obras Sociales</label>
                <select id="usuario_obras_sociales" class="w100p"></select>
              </div>
            <% } %>

            <div class="form-group">
              <label class="control-label"><%= (ID_EMPRESA == 1319) ? "Objetivos" : "Formas de Pago" %></label>
              <select id="usuario_formas_pago" class="w100p"></select>
            </div>

            <% if (ID_EMPRESA != 1319) { %>
              <div class="form-group">
                <label class="control-label">Tipos de Pacientes</label>
                <select id="usuario_tipos_pacientes" class="w100p"></select>
              </div>
            <% } %>

            <div class="form-group">
              <label class="control-label"><%= (ID_EMPRESA == 1319) ? "Tipo de Servicio" : "Tipos de Atencion" %></label>
              <select id="usuario_tipos_atenciones" class="w100p"></select>
            </div>

            <div class="form-group">
              <label class="control-label"><% var c = control.get("tipos_terapias")  %><%= c.title %></label>
              <select id="usuario_tipos_terapias" class="w100p"></select>
            </div>

            <div class="form-group">
              <label class="control-label">Formación Académica</label>
              <div class="">
                <textarea <%= (!edicion)?"disabled":"" %> name="custom_4" class="form-control" id="custom_4"><%= custom_4 %></textarea>
              </div>
            </div>

            <% if (id_perfiles == 1358) { %>
              <div class="input-group" style="width: 95%;">
                <label class="control-label">Obtén opiniones de tus clientes a través de este link</label>
                <input type="text" disabled class="form-control" value="https://entrenaymas.com/web/calificar/?id=<%=id%>">
                <div class="input-group-append" style="display: flex;">
                  <button class="btn btn-outline-secondary copiar" data-toggle="tooltip" data-placement="top" title="Copiar" type="button"><i class="fa fa-clone" aria-hidden="true"></i></button>
                </div>
              </div>            
            <% } %>

          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="padder">
            <div class="form-group mb0 clearfix">
              <label class="control-label"><%= (ID_EMPRESA == 1319) ? "Tarifas" : "Direcciones" %></label>
              <a class="expand-link fr">
                <?php echo lang(array(
                  "es"=>"+ Ver opciones",
                  "en"=>"+ View options",
                )); ?>
              </a>
              <div class="panel-description">
                <%= (ID_EMPRESA == 1319) ? "Gestiona tus tarifas." : "Agregue los lugares donde atiende." %>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body expand" style="<%= (direcciones.length>0) ? 'display:block':'' %>">
          <div class="padder">
            <div class="clearfix">
              <button class="btn btn-info nueva_direccion">+ Agregar</button>
            </div>
            <div id="usuario_direcciones" class="mt10"></div>

            <div class="checkbox">
              <label class="i-checks">
                <input type="checkbox" id="usuario_sesion_gratis" name="sesion_gratis" class="checkbox" value="1" <%= (sesion_gratis == 1)?"checked":"" %> >
                <i></i>
                Primera sesión gratuita
              </label>
            </div>

          </div>
        </div>
      </div>      

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="padder">
            <div class="form-group mb0 clearfix">
              <label class="control-label">Cajas de regalo</label>
              <a class="expand-link fr">
                <?php echo lang(array(
                  "es"=>"+ Ver opciones",
                  "en"=>"+ View options",
                )); ?>
              </a>
              <div class="panel-description">
                Mire las cajas de regalo que han canjeado contigo
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body expand">
          <div class="padder">
            <div class="clearfix">
              <table id="usuario_cajas_tabla" class="table m-b-none default footable mb15">
                <thead>
                  <tr>
                    <th>Cliente</th>
                    <th>Fecha de activacion</th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var i=0;i< cajas_regalo.length;i++) { %>
                    <% var p = cajas_regalo[i] %>
                    <tr>
                      <td><%= p.nombre %></td>
                      <td><%= moment(p.fecha_activacion,"YYYY-MM-DD HH:mm:ss").format("DD/MM/YYYY HH:mm:ss") %></td>
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>   

      <% if (ID_EMPRESA == 1319) { %>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Localizaciones</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">Autogestiona tus localizaciones</div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="display:block">
            <div class="padder">

              <div class="panel-description bold mb10">
                Agrega las distintas zonas en donde deseas aparecer en los resultados de búsqueda
              </div>

              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Provincia</label>
                    <select id="usuario_provincias" class="form-control no-model">
                      <option value="1518">A Coruña</option>
                      <option value="1501">Álava</option>
                      <option value="1505">Albacete</option>
                      <option value="1509">Alicante</option>
                      <option value="1513">Almería</option>
                      <option value="1517">Asturias</option>
                      <option value="1521">Ávila</option>
                      <option value="1525">Badajoz</option>
                      <option value="1529">Baleares</option>
                      <option value="1533">Barcelona</option>
                      <option value="1537">Burgos</option>
                      <option value="1541">Cáceres</option>
                      <option value="1545">Cádiz</option>
                      <option value="1549">Cantabria</option>
                      <option value="1502">Castellón</option>
                      <option value="1510">Ciudad Real</option>
                      <option value="1514">Córdoba</option>
                      <option value="1522">Cuenca</option>
                      <option value="1526">Girona</option>
                      <option value="1530">Granada</option>
                      <option value="1534">Guadalajara</option>
                      <option value="1538">Guipúzcoa</option>
                      <option value="1542">Huelva</option>
                      <option value="1546">Huesca</option>
                      <option value="1550">Jaén</option>
                      <option value="1551">La Rioja</option>
                      <option value="1543">Las Palmas</option>
                      <option value="1503">León</option>
                      <option value="1507">Lleida</option>
                      <option value="1511">Lugo</option>
                      <option value="1515">Madrid</option>
                      <option value="1519">Málaga</option>
                      <option value="1527">Murcia</option>
                      <option value="1531">Navarra</option>
                      <option value="1535">Ourense</option>
                      <option value="1539">Palencia</option>
                      <option value="1547">Pontevedra</option>
                      <option value="1524">S.C. Tenerife</option>
                      <option value="1504">Salamanca</option>
                      <option value="1508">Segovia</option>
                      <option value="1512">Sevilla</option>
                      <option value="1516">Soria</option>
                      <option value="1520">Tarragona</option>
                      <option value="1528">Teruel</option>
                      <option value="1532">Toledo</option>
                      <option value="1536">Valencia</option>
                      <option value="1540">Valladolid</option>
                      <option value="1544">Vizcaya</option>
                      <option value="1548">Zamora</option>
                      <option value="1552">Zaragoza</option>                    
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Ciudad</label>
                    <select id="usuario_localidades" class="w100p"></select>
                  </div>
                </div>
                <div class="col-sm-3 dn">
                  <div class="form-group">
                    <label class="control-label">Puja por la ciudad</label>
                    <input type="text" disabled id="usuario_localidades_puja" class="form-control no-model" />
                  </div>
                </div>
                <div class="col-sm-4 tar mt25">
                  <div class="form-group">
                    <label class="control-label dn">Precio por contacto</label>
                    <div class="input-group">
                      <input id="usuario_localidad_valor" value="" type="number" class="dn form-control"/>
                      <span class="input-group-btn">
                        <a id="usuario_localidad_agregar" class="btn btn-info db">Agregar</a>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <table id="usuario_localidades_tabla" class="table m-b-none default footable mb15">
                <thead>
                  <tr>
                    <th>Provincia</th>
                    <th>Ciudad</th>
                    <th class="dn">Precio por contacto</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var i=0;i< localidades.length;i++) { %>
                    <% var p = localidades[i] %>
                    <tr data-valor="<%= p.valor %>" data-id_provincia="<%= p.id_provincia %>" data-id="<%= p.id_localidad %>">
                      <td><%= p.provincia %></td>
                      <td><%= p.localidad %></td>
                      <td class="dn"><%= p.valor %></td>
                      <td><button class='btn btn-white editar_localidad'><i class='fa fa-pencil'></i></button></td>
                      <td><button class='btn btn-white eliminar_localidad'><i class='fa fa-trash'></i></button></td>
                    </tr>
                  <% } %>
                </tbody>
              </table>

              <div class="form-group dn">
                <label class="control-label">Tope máximo mensual</label>
                <input id="usuario_maximo" value="<%= maximo %>" name="maximo" type="number" class="form-control"/>
              </div>
              
            </div>
          </div>
        </div>
      <% } %>

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="padder">
            <div class="form-group mb0 clearfix">
              <label class="control-label">Galería de Imágenes</label>
              <a class="expand-link fr">
                <?php echo lang(array(
                  "es"=>"+ Ver opciones",
                  "en"=>"+ View options",
                )); ?>
              </a>
            </div>
          </div>
        </div>
        <div class="panel-body expand" style="<%= (images.length > 0)?"display:block":"" %>">
          <div class="padder">
            <?php
            multiple_upload(array(
              "name"=>"images",
              "label"=>lang(array("en"=>"Image Gallery","es"=>"Galería de fotos")),
              "url"=>"usuarios/function/save_image/",
              "url_file"=>"usuarios/function/save_file/",
              "width"=>(isset($empresa->config["usuario_galeria_image_width"]) ? $empresa->config["usuario_galeria_image_width"] : 800),
              "height"=>(isset($empresa->config["usuario_galeria_image_height"]) ? $empresa->config["usuario_galeria_image_height"] : 600),
              "quality"=>(isset($empresa->config["usuario_galeria_image_quality"]) ? $empresa->config["usuario_galeria_image_quality"] : 0.8),
            )); ?>

          </div>
        </div>
      </div>

      <% if (PERFIL == 1355) { %>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"SEO",
                    "en"=>"SEO",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Datos para optimización en buscadores",
                    "en"=>"Add data for Search Engine Optimization",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"T&iacute;tulo",
                    "en"=>"Title",
                  )); ?>
                </label>
                <label class="control-label fr">
                  <span id="usuario_seo_title_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>70</span>
                </label>
                <input type="text" data-max="70" data-id="usuario_seo_title_cantidad" name="seo_title" id="usuario_seo_title" value="<%= seo_title %>" class="form-control text-remain"/>
              </div>
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <label class="control-label fr">
                  <span id="usuario_seo_description_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>160</span>
                </label>
                <textarea data-max="160" data-id="usuario_seo_description_cantidad" name="seo_description" id="usuario_seo_description" class="form-control text-remain"><%= seo_description %></textarea>
              </div>           
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