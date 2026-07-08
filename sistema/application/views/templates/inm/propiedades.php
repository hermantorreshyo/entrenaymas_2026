<script type="text/template" id="propiedades_resultados_template">
<% if (vista_busqueda) { %>
  <div class="panel panel-default">

    <?php include("buscar_propiedades_contacto.php") ?>

    <div class="panel-body">
      <div class="tab-container mb0">
        <ul class="nav nav-tabs nav-tabs-2" role="tablist">
          <li id="buscar_propias_tab" class="buscar_tab <%= (window.propiedades_buscar_red == 0)?"active":"" %>">
            <a href="javascript:void(0)"><i class="fa fa-home text-info mr5"></i> Mis Propiedades</a>
          </li>
          <li id="buscar_red_tab" class="buscar_tab <%= (window.propiedades_buscar_red == 1)?"active":"" %>">
            <a href="javascript:void(0)"><img src="/sistema/resources/images/red_inmovar.png" style="width:14px;margin-right:5px"/> Red Inmovar</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="table-responsive-xs b-a pr oh table-fixed">
            <table id="propiedades_tabla" class="table table-striped sortable m-b-none default footable">
              <tbody class="tbody"></tbody>
              <tfoot class="pagination_container hide-if-no-paging"></tfoot>
            </table>
          </div>
        </div>
        <div class="bulk_action tar m-t">
          <div class="dib m-r">
            <p><b class="cantidad_seleccionados"></b> elementos seleccionados</p>  
          </div>
          <button class="btn btn-default marcar_interes btn-addon"><i class="icon fa fa-star"></i>Marcar Inter&eacute;s</button>
          <button class="btn btn-info enviar btn-addon"><i class="icon fa fa-send"></i>Enviar fichas por email</button>
          <button class="btn btn-success enviar_whatsapp btn-addon"><i class="icon fa fa-whatsapp"></i>Enviar Whatsapp</button>
        </div>
      </div>

    </div>
  </div>
<% } else { %>
  <?php /*
  <div class="seccion_vacia" style="display:none">
    <h1 class="h1">Todav&iacute;a no ten&eacute;s ninguna propiedad</h1>
    <h3 class="h3">Para a&ntilde;adir tu primera propiedad, hace click en el siguiente bot&oacute;n</h3>
    <div class="list-icon">
      <a href="app/#propiedad"><i class="icon-note"></i></a>
    </div>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#propiedad">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
    <p>
      Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
    </p>
  </div>
  */ ?>
  <div>
    <% if (!seleccionar) { %>
      <div class="bg-light lter b-b wrapper-md ng-scope">
        <h1 class="m-n font-thin h3">
          <i class="glyphicon glyphicon-home icono_principal mr10"></i><%= (!isEmpty(nombre))?"Buscando propiedades para "+nombre : "Propiedades" %>
        </h1>
      </div>
    <% } %>
    <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
      <div class="panel panel-default">
        <?php include("buscar_propiedades.php") ?>
        <% if (!seleccionar) { %>
          <div class="bulk_action wrapper pb0">
            <p><b class="cantidad_seleccionados"></b> elementos seleccionados</p>
            <button class="btn btn-default enviar btn-addon"><i class="icon fa fa-send"></i>Enviar fichas por email</button>
            <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO)) { %>
              <div class="btn-group dropdown">
                <button class="btn btn-default btn-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="icon fa fa-share-alt"></i>MercadoLibre
                </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" class="compartir_meli">Compartir</a></li>
                  <li><a href="javascript:void(0)" class="meli_pausar_multiple">Pausar</a></li>
                </ul>
              </div> 
            <% } %>
            <div class="btn-group dropdown">
              <button class="btn btn-default btn-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon fa fa-share-alt"></i>Red Inmovar
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void(0)" class="compartir_red_multiple">Compartir</a></li>
                <li><a href="javascript:void(0)" class="no_compartir_red_multiple">No Compartir</a></li>
              </ul>
            </div> 
          </div>
        <% } %>
        <div class="tab-container mb0">
          <ul class="nav nav-tabs nav-tabs-2" role="tablist">
            <li id="buscar_propias_tab" class="buscar_tab <%= (window.propiedades_buscar_red == 0)?"active":"" %>">
              <a href="javascript:void(0)"><i class="fa fa-home text-info mr5"></i> Mis Propiedades</a>
            </li>
            <li id="buscar_red_tab" class="buscar_tab <%= (window.propiedades_buscar_red == 1)?"active":"" %>">
              <a href="javascript:void(0)"><img src="/sistema/resources/images/red_inmovar.png" style="width:14px;margin-right:5px"/> Red Inmovar</a>
            </li>
          </ul>
        </div>
        <div class="panel-body">

          <div style="height:500px;display:<%= (window.propiedades_mapa == 1)?"block":"none" %>" id="propiedades_mapa"></div>

          <div id="propiedades_tabla_cont" class="b-a table-responsive">
            <table id="propiedades_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <% if (!seleccionar) { %>
                    <th style="width:20px;">
                        <label class="i-checks m-b-none">
                            <input class="esc sel_todos" type="checkbox"><i></i>
                        </label>
                    </th>
                    <th class="w50 tac"></th>
                  <% } else { %>
                    <th style="width:20px;"></th>
                  <% } %>
                  <th>Titulo</th>
                  <th>Direccion</th>
                  <th class="sorting">Operaci&oacute;n</th>
                  <% if (!seleccionar) { %>
                    <th class="w150 sorting" data-sort-by="precio_final">Precio</th>
                    <th class="w30 sorting ocultar_en_red" data-sort-by="usuario"></th>
                    <th class="ocultar_en_red "><%= (ID_EMPRESA == 70) ? "Consultas":"Estado" %></th>
                    <th class="w140 ocultar_en_red"></th>
                    <th class="th_acciones ocultar_en_red w180">Acciones</th>
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

<script type="text/template" id="propiedades_item_resultados_template">
<% if (vista_busqueda) { %>

  <% var clase = (activo==1)?"data":"text-muted"; %>
  <input type="hidden" class="link_completo" value="<%= link_completo %>"/>
  
  <td class="w20">
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="<%= clase %> w120">
    <% if (!isEmpty(path)) { %>
      <% var prefix = (path.indexOf("http") == 0) ? "" : "/sistema/" %>
      <img src="<%= prefix + path %>?t=<%= Math.ceil(Math.random()*10000) %>" class="propiedad-image"/>
    <% } %>
  </td>
  <td class="<%= clase %>">
    <span class="text-info"><%= nombre %></span><br/>
    Cod.: <%= codigo %>
    <% if (!isEmpty(custom_3)) { %>
      <span class="label bg-light dk">Red</span>
    <% } %>
  </td>
  <td class="<%= clase %>">
    <%= calle %> <%= altura %> <%= piso %> <%= numero %><br/>
    <%= localidad %>
  </td>
  <td class="<%= clase %>">
    <%= tipo_operacion %>
  </td>
  <td class="<%= clase %>">
    <%= tipo_inmueble %>
    <input type="hidden" id="<%= id %>_localidad" value="<%= id_localidad %>"/>
    <input type="hidden" id="<%= id %>_tipo_operacion" value="<%= id_tipo_operacion %>"/>
    <input type="hidden" id="<%= id %>_tipo_inmueble" value="<%= id_tipo_inmueble %>"/>
  </td>
  <td class="<%= clase %> tar w200"><span class="tag_precio tac dib"><%= moneda %> <%= Number(precio_final).format(0) %></span></td>


<% } else { %>
  <% var clase = (activo==1)?"":"text-muted"; %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>
    <td class="<%= clase %> p0 data">
      <% if (!isEmpty(path)) { %>
        <% var prefix = (path.indexOf("http") == 0) ? "" : "/sistema/" %>
        <img src="<%= prefix + path %>?t=<%= Math.ceil(Math.random()*10000) %>" class="customcomplete-image"/>
      <% } %>
    </td>
  <% } %>
  <td class="<%= clase %> data">
    <span class="text-info"><%= nombre %></span><br/>
    Cod.: <%= codigo %>
    <% if (!isEmpty(custom_3)) { %>
      <span class="label bg-light dk">Red</span>
    <% } %>
  </td>
  <td class="<%= clase %> data">
    <%= calle %> <%= altura %> <%= piso %> <%= numero %><br/>
    <%= localidad %>
  </td>
  <td class="<%= clase %> data">
    <%= tipo_inmueble %><br/><%= tipo_operacion %>
    <input type="hidden" id="<%= id %>_localidad" value="<%= id_localidad %>"/>
    <input type="hidden" id="<%= id %>_tipo_operacion" value="<%= id_tipo_operacion %>"/>
    <input type="hidden" id="<%= id %>_tipo_inmueble" value="<%= id_tipo_inmueble %>"/>
  </td>
  <% if (!seleccionar) { %>
    <td class="<%= clase %> data tar"><span class="tag_precio tac dib"><%= moneda %> <%= Number(precio_final).format(0) %></span></td>
    <% if (id_empresa == ID_EMPRESA) { %>
      <td class="<%= clase %> data">
        <i class="fa fa-user" data-toggle="tooltip" title="<%= usuario %>"></i>
      </td>
      <td class="<%= clase %> data">
        <% if (ID_EMPRESA == 70) { %>
          <span class="label bg-success"><%= cantidad_consultas %></span>
        <% } else { %>
          <% if (id_tipo_estado == 1) { %>
            <span class="label bg-success"><%= tipo_estado %></span>
          <% } else if (id_tipo_estado == 2) { %>
            <span class="label bg-warning"><%= tipo_estado %></span>
          <% } else if (id_tipo_estado == 3) { %>
            <span class="label bg-danger"><%= tipo_estado %></span>
          <% } else if (id_tipo_estado == 4) { %>
            <span class="label bg-primary"><%= tipo_estado %></span>
          <% } else if (id_tipo_estado == 5) { %>
            <span class="label bg-info"><%= tipo_estado %></span>
          <% } else if (id_tipo_estado == 6) { %>
            <span class="label bg-danger"><%= tipo_estado %></span>
          <% } %>
        <% } %>
      </td>
      <td class="vam">
        <div class="btn-group dropdown">
          <% var esta_compartida = (compartida == 1 || !isEmpty(permalink) || !isEmpty(olx_id) || inmobusquedas_habilitado == 1 || argenprop_habilitado == 1 || eldia_habilitado == 1) ? 1 : 0 %>
          <button class="btn <%= (esta_compartida==1)?"btn-success":"btn-info" %> btn-sm btn-addon btn-addon2 btn-menu-compartir">
            <i class="fa fa-bullhorn"></i><%= (esta_compartida==1)?"Compartida":"Compartir" %>
          </button>

          <div class="menu-compartir">
            <div class="dt">

              <div class="dtr">
                <div class="dtc menu-compartir-logo tac">
                  <img src="<%= (compartida==1)?"/sistema/resources/images/red_inmovar.png":"/sistema/resources/images/red_inmovar_d.png" %>" data-toggle="tooltip" class="compartida" title="<%= (compartida==1)?"Compartido en Red INMOVAR":"Compartir en Red INMOVAR" %>"/>
                </div>
                <div class="dtc menu-compartir-nombre">
                  <span class="compartida">Red Inmovar</span>
                </div>
                <div class="dtc menu-compartir-submenu">
                </div>
              </div>

              <div class="dtr">
                <div class="dtc menu-compartir-logo facebook tac">
                  <% if (compartida_facebook == 1) { %>
                    <img src="/sistema/resources/images/facebook.png" data-toggle="tooltip" title="Compartir en Facebook"/>
                  <% } else { %>
                    <img src="/sistema/resources/images/facebook_d.png" data-toggle="tooltip" title="Compartir en Facebook"/>
                  <% } %>
                </div>
                <div class="dtc menu-compartir-nombre">
                  <span class="facebook">Facebook</span>
                </div>
                <div class="dtc menu-compartir-submenu">
                </div>
              </div>

              <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && (typeof ML_ACCESS_TOKEN != "undefined")) { %>
                <div class="dtr">
                  <div class="dtc menu-compartir-logo tac">
                    <% if (isEmpty(ML_ACCESS_TOKEN)) { %>
                      <img src="/sistema/resources/images/ML-Off.png" data-toggle="tooltip" class="compartir_meli" title="Compartir en MercadoLibre"/>
                    <% } else { %>
                      <% if (typeof permalink != "undefined" && !isEmpty(permalink)) { %>
                        <div style="position: relative;">
                          <img src="/sistema/resources/images/ML-On.png" data-toggle="tooltip" title="Compartido en MercadoLibre"/>
                          <% if (status == 'active') { %>
                            <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-success pull-right"><i class="fa fa-play"></i></b>
                          <% } else if (status == 'paused') { %>
                            <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-danger pull-right"><i class="fa fa-pause"></i></b>
                          <% } else if (status == 'closed') { %>
                            <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-danger pull-right"><i class="fa fa-times"></i></b>
                          <% } %>
                        </div>
                      <% } else { %>
                        <img src="/sistema/resources/images/ML-Off.png" data-toggle="tooltip" class="compartir_meli" title="Compartir en MercadoLibre"/>
                      <% } %>
                    <% } %>
                  </div>
                  <div class="dtc menu-compartir-nombre">
                    <span class="compartir_meli">MercadoLibre</span>
                  </div>
                  <div class="dtc menu-compartir-submenu">
                    <div class="btn-group dropdown ml10">
                      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle menu-compartir-submenu-dropdown" data-toggle="dropdown"></i>
                      <ul class="dropdown-menu pull-right">
                        <li><a target="_blank" href="<%= permalink %>">Ver publicacion</a></li>
                        <% if (status == 'paused') { %>
                          <li><a class="compartir_meli" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Modificar</a></li>
                          <li><a class="meli_reactivar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Reactivar</a></li>
                          <li><a class="meli_finalizar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Finalizar</a></li>
                        <% } else if (status == 'active') { %>
                          <li><a class="compartir_meli" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Modificar</a></li>
                          <li><a class="meli_pausar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Pausar</a></li>
                        <% } else if (status == 'closed') { %>
                          <li><a class="meli_eliminar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Eliminar</a></li>
                        <% } %>
                      </ul>
                    </div>
                  </div>
                </div>
              <% } %>

              <div class="dtr">
                <div class="dtc menu-compartir-logo tac">
                  <% if (olx_habilitado == 0) { %>
                    <img src="/sistema/resources/images/OLX-Off.png" data-toggle="tooltip" class="compartir_olx" title="Compartir en OLX"/>
                  <% } else { %>
                    <div style="position: relative;">
                      <% if (isEmpty(olx_id)) { %>
                        <img src="/sistema/resources/images/OLX-On.png" data-toggle="tooltip" title="Esperando aprobacion de OLX"/>
                        <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-warning pull-right"><i class="fa fa-clock-o"></i></b>
                      <% } else { %>
                        <img src="/sistema/resources/images/OLX-On.png" data-toggle="tooltip" title="Compartido en OLX"/>
                        <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-success pull-right"><i class="fa fa-play"></i></b>
                      <% } %>
                    </div>
                  <% } %>
                </div>
                <div class="dtc menu-compartir-nombre">
                  <span class="compartir_olx">OLX</span>
                </div>
                <div class="dtc menu-compartir-submenu">
                  <div class="btn-group dropdown ml10">
                    <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle menu-compartir-submenu-dropdown" data-toggle="dropdown"></i>
                    <ul class="dropdown-menu pull-right">
                      <% if (!isEmpty(olx_id)) { %>
                        <li><a target="_blank" href="https://www.olx.com.ar/iid-<%= olx_id %>">Ver publicacion</a></li>
                      <% } %>
                      <li><a class="compartir_olx" href="javascript:void(0)">Dejar de compartir</a></li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="dtr">
                <div class="dtc menu-compartir-logo tac">
                  <div style="position: relative;">
                    <img src="<%= (inmobusquedas_habilitado==1)?"/sistema/resources/images/inmobusquedas.png":"/sistema/resources/images/inmobusquedas_d.png" %>" data-toggle="tooltip" class="<%= (inmobusquedas_habilitado==0) ? "inmobusquedas_habilitado":"" %>" title="<%= (inmobusquedas_habilitado==0)? "Compartir en Inmobusqueda": ((isEmpty(inmobusquedas_url))?"Revisar informacion":"Compartido correctamente") %>"/>
                    <% if (inmobusquedas_habilitado==1 && !isEmpty(inmobusquedas_url)) { %>
                      <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-success pull-right"><i class="fa fa-play"></i></b>
                    <% } else if (inmobusquedas_habilitado==1 && isEmpty(inmobusquedas_url)) { %>
                      <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-danger pull-right"><i class="fa fa-times"></i></b>
                    <% } %>
                  </div>
                </div>
                <div class="dtc menu-compartir-nombre">
                  <span class="inmobusquedas_habilitado">Inmobusqueda</span>
                </div>
                <div class="dtc menu-compartir-submenu">
                  <div class="btn-group dropdown ml10">
                    <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle menu-compartir-submenu-dropdown" data-toggle="dropdown"></i>
                    <ul class="dropdown-menu pull-right">
                      <% if (!isEmpty(inmobusquedas_url)) { %>
                        <li><a target="_blank" href="https://www.inmobusqueda.com.ar/ficha-<%= inmobusquedas_url %>">Ver publicacion</a></li>
                      <% } %>
                      <li><a class="inmobusquedas_habilitado" href="javascript:void(0)">Dejar de compartir</a></li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="dtr">
                <div class="dtc menu-compartir-logo tac">
                  <div style="position: relative;">
                    <img src="<%= (argenprop_habilitado >= 1)?"/sistema/resources/images/argenprop.png":"/sistema/resources/images/argenprop_d.png" %>" data-toggle="tooltip" class="argenprop_habilitado" title="<%= (argenprop_habilitado==1)? "Compartido en Argenprop":"Compartir en Argenprop" %>"/>
                    <% if (argenprop_habilitado==1) { %>
                      <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-success pull-right"><i class="fa fa-play"></i></b>
                    <% } else if (argenprop_habilitado > 1) { %>
                      <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-danger pull-right"><i class="fa fa-times"></i></b>
                    <% } %>
                  </div>
                </div>
                <div class="dtc menu-compartir-nombre">
                  <span class="argenprop_habilitado">Argenprop</span>
                </div>
                <div class="dtc menu-compartir-submenu">
                  <% if (!isEmpty(argenprop_url)) { %>
                    <div class="btn-group dropdown ml10">
                      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle menu-compartir-submenu-dropdown" data-toggle="dropdown"></i>
                      <ul class="dropdown-menu pull-right">
                        <li><a target="_blank" href="<%= argenprop_url %>">Ver publicacion</a></li>
                        <% if (argenprop_habilitado == 1) { %>
                          <li><a class="argenprop_pausar" href="javascript:void(0)">Pausar</a></li>
                        <% } else if (argenprop_habilitado > 1) { %>
                          <li><a class="argenprop_activar" href="javascript:void(0)">Activar</a></li>
                        <% } %>
                        <?php /*
                        <li><a class="argenprop_eliminar" href="javascript:void(0)">Eliminar</a></li>
                        */ ?>
                      </ul>
                    </div>
                  <% } %>
                </div>
              </div>

              <div class="dtr">
                <div class="dtc menu-compartir-logo tac">
                  <img src="<%= (eldia_habilitado==1)?"/sistema/resources/images/eldia.png":"/sistema/resources/images/eldia_d.png" %>" data-toggle="tooltip" class="eldia_habilitado" title="<%= (eldia_habilitado==1)? "Compartido en Diario El Dia":"Compartir en Diario El Dia" %>"/>
                </div>
                <div class="dtc menu-compartir-nombre">
                  <span class="eldia_habilitado">Diario El Dia</span>
                </div>
                <div class="dtc menu-compartir-submenu">
                </div>
              </div>

            </div>
          </div>
        </div>
      </ul>

      <td class="tar td_acciones">
        <i data-toggle="tooltip" title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="Destacado" class="fa fa-star iconito warning destacado <%= (destacado == 1)?"active":"" %>"></i>
        <i data-toggle="tooltip" title="Respuesta Automatica" class="fa fa-comments iconito danger respuesta_habilitado <%= (respuesta_habilitado == 1)?"active":"" %>"></i>
        <% if (ID_EMPRESA != 685) { %>
          <i data-toggle="tooltip" title="Apto cr&eacute;dito bancario" class="text-muted-2 success fa fa-home iconito apto_banco <%= (apto_banco == 1)?"active":"" %>"></i>
        <% } %>
        <div class="fr btn-group dropdown ml10">
          <i title="Opciones" class="iconito text-muted-2 fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="editar"><i class="text-muted-2 fa fa-pencil w25"></i> Editar</a></li>
            <li class="divider"></li>
            <li><a href="javascript:void(0)" class="ver_interesados"><i class="text-muted-2 fa fa-users w25"></i> Ver interesados</a></li>
            <li><a href="javascript:void(0)" class="buscar_interesados"><i class="text-muted-2 fa fa-search w25"></i> Buscar interesados</a></li>
            <li class="divider"></li>
            <li><a href="<%= link_completo %>?preview=1" target="_blank"><i class="text-muted-2 fa fa-globe w25"></i> Ver web</a></li>
            <li><a href="javascript:void(0)" class="ver_ficha" data-id="<%= id %>"><i class="text-muted-2 fa fa-file w25"></i> Ver ficha</a></li>
            <li class="divider"></li>
            <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>"><i class="text-muted-2 fa fa-files-o w25"></i> Duplicar</a></li>
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>"><i class="text-muted-2 fa fa-times w25"></i> Eliminar</a></li>
          </ul>
        </div>
      </td>
    <% } %>
  <% } %>
<% } %>
</script>


<script type="text/template" id="propiedad_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="glyphicon glyphicon-home icono_principal mr10"></i>Propiedades
    / <b><%= (id == undefined) ? "Nueva" : nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <% if (ID_EMPRESA != 70) { %>
                <div class="form-group">
                  <label class="control-label">T&iacute;tulo</label>
                  <input type="text" required name="nombre" id="propiedad_nombre" value="<%= nombre %>" class="form-control"/>
                </div>
              <% } %>
              <% if (ID_EMPRESA == 45) { %>
                <div class="form-group">
                  <label class="control-label">Subt&iacute;tulo</label>
                  <input type="text" name="subtitulo" id="propiedad_subtitulo" value="<%= subtitulo %>" class="form-control"/>
                </div>
              <% } %>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tipo Operacion</label>
                    <select id="propiedad_tipos_operacion" class="w100p">
                      <% for(var i=0;i< window.tipos_operacion.length;i++) { %>
                        <% var o = tipos_operacion[i]; %>
                        <option value="<%= o.id %>" <%= (o.id == id_tipo_operacion)?"selected":"" %>><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tipo Inmueble</label>
                    <select id="propiedad_tipos_inmueble" class="w100p">
                      <% for(var i=0;i< window.tipos_inmueble.length;i++) { %>
                        <% var o = tipos_inmueble[i]; %>
                        <option value="<%= o.id %>" <%= (o.id == id_tipo_inmueble)?"selected":"" %>><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <textarea name="texto" name="propiedad_texto" id="propiedad_texto"><%= texto %></textarea>
              </div>

              <% if (ID_EMPRESA == 70) { %>
                <div class="form-group">
                  <label class="control-label">
                    Vendedor
                  </label>
                  <textarea name="texto_privado" style="height:120px" class="form-control" placeholder="Datos del vendedor..." id="propiedad_texto_privado"><%= texto_privado %></textarea>
                </div>
              <% } %>

              <div class="form-group">
                <label class="control-label">Propietario</label>
                <div class="input-group">
                  <select id="propiedad_propietarios" style="width: 100%" class="form-control"></select>
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-info nuevo_propietario">+ Agregar</button>
                  </div>
                </div>
              </div>

              <div style="<%= (ID_EMPRESA == 70)?'display:none':'' %>">
                <?php
                single_upload(array(
                  "name"=>"path",
                  "label"=>"Imagen Principal",
                  "url"=>"propiedades/function/save_image/",
                  "url_file"=>"propiedades/function/save_file/",
                  "width"=>(isset($empresa->config["propiedad_image_width"]) ? $empresa->config["propiedad_image_width"] : 400),
                  "height"=>(isset($empresa->config["propiedad_image_height"]) ? $empresa->config["propiedad_image_height"] : 400),
                  "quality"=>(isset($empresa->config["propiedad_image_quality"]) ? $empresa->config["propiedad_image_quality"] : 0),
                  "thumbnail_width"=>(isset($empresa->config["propiedad_thumbnail_width"]) ? $empresa->config["propiedad_thumbnail_width"] : 0),
                  "thumbnail_height"=>(isset($empresa->config["propiedad_thumbnail_height"]) ? $empresa->config["propiedad_thumbnail_height"] : 0),
                )); ?>
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
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">C&oacute;digo Interno</label>
                    <input type="text" name="codigo" id="propiedad_codigo" value="<%= codigo %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Nivel de destaque</label>
                    <input type="text" name="destacado" id="propiedad_destacado" value="<%= destacado %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Estado</label>
                    <select id="propiedad_tipos_estado" class="form-control">
                      <% if (ID_EMPRESA == 538) { %>
                        <option value="1" <%= (id_tipo_estado == 1)?"selected":"" %>>-</option>
                        <option value="2" <%= (id_tipo_estado == 2)?"selected":"" %>>En construccion</option>
                        <option value="3" <%= (id_tipo_estado == 3)?"selected":"" %>>Lanzamiento</option>
                      <% } else { %>
                        <% for(var i=0;i< window.tipos_estado.length;i++) { %>
                          <% var o = tipos_estado[i]; %>
                          <option value="<%= o.id %>" <%= (o.id == id_tipo_estado)?"selected":"" %>><%= o.nombre %></option>
                        <% } %>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Antigüedad</label>
                    <select name="nuevo" id="propiedad_antiguedad" class="form-control">
                      <option value="0" <%= (nuevo == 0)?"selected":"" %>>No definida</option>
                      <option value="1" <%= (nuevo == 1)?"selected":"" %>>A estrenar</option>
                      <option value="2" <%= (nuevo == 2)?"selected":"" %>>Aprox. 2 a&ntilde;os</option>
                      <option value="5" <%= (nuevo == 5)?"selected":"" %>>Aprox. 5 a&ntilde;os</option>
                      <option value="10" <%= (nuevo == 10)?"selected":"" %>>Aprox. 10 a&ntilde;os</option>
                      <option value="20" <%= (nuevo == 20)?"selected":"" %>>Aprox. 20 a&ntilde;os</option>
                      <option value="30" <%= (nuevo == 30)?"selected":"" %>>Aprox. 30 a&ntilde;os</option>
                      <option value="40" <%= (nuevo == 40)?"selected":"" %>>Aprox. 40 a&ntilde;os</option>
                      <option value="50" <%= (nuevo == 50)?"selected":"" %>>Aprox. 50 a&ntilde;os</option>
                      <option value="60" <%= (nuevo == 60)?"selected":"" %>>Aprox. 60 a&ntilde;os</option>
                      <option value="70" <%= (nuevo == 70)?"selected":"" %>>Aprox. 70 a&ntilde;os</option>
                      <option value="80" <%= (nuevo == 80)?"selected":"" %>>Aprox. 80 a&ntilde;os</option>
                      <option value="90" <%= (nuevo == 90)?"selected":"" %>>Aprox. 90 a&ntilde;os</option>
                      <option value="100" <%= (nuevo == 100)?"selected":"" %>>Aprox. 100 a&ntilde;os</option>
                      <option value="200" <%= (nuevo == 200)?"selected":"" %>>M&aacute;s de 100 a&ntilde;os</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Asignado a usuario</label>
                    <select id="propiedad_usuarios" class="w100p">
                      <% for(var i=0;i< window.usuarios.models.length;i++) { %>
                        <% var o = usuarios.models[i]; %>
                        <option value="<%= o.id %>" <%= (o.id == id_usuario)?"selected":"" %>><%= o.get("nombre") %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Publicacion</label>
                    <div class="input-group">
                      <input type="text" name="fecha_publicacion" id="propiedad_fecha_publicacion" value="<%= fecha_publicacion %>" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label"><%= (ID_EMPRESA == 685) ? "Comentarios" : "Descripci&oacute;n breve utilizada en el listado de propiedades" %></label>
                <textarea id="propiedad_descripcion" class="form-control" name="descripcion"><%= descripcion %></textarea>
              </div>

              <div class="form-group cb">
                <div class="pt0 checkbox">
                  <label class="i-checks">
                    <input name="activo" id="propiedad_activo" value="1" type="checkbox" <%= (activo == 1) ? "checked" : "" %>><i></i> 
                    Propiedad activa.
                  </label>
                </div>
              </div>
              <?php /*
              <div class="form-group cb">
                <div class="pt0 checkbox">
                  <label class="i-checks">
                    <input name="destacado" id="propiedad_destacado" value="1" type="checkbox" <%= (destacado == 1) ? "checked" : "" %>><i></i> 
                    Marcar como 'Propiedad destacada'.
                  </label>
                </div>
              </div>
              */ ?>
              <div class="form-group cb">
                <div class="pt0 checkbox">
                  <label class="i-checks">
                    <input name="mostrar_home" id="propiedad_mostrar_home" value="1" type="checkbox" <%= (mostrar_home == 1) ? "checked" : "" %>><i></i> 
                    Mostrar en Home de la web.
                  </label>
                </div>
              </div>

              <% if (ID_EMPRESA == 263) { %>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Vencimiento</label>
                      <div class="input-group">
                        <input type="text" name="valido_hasta" id="propiedad_valido_hasta" value="<%= valido_hasta %>" class="form-control"/>
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              <% } %>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Etiquetas",
                    "en"=>"Tags",
                  )); ?>
                </label>
                <select multiple id="propiedad_etiquetas" style="width: 100%">
                  <% for (var i=0; i< etiquetas.length; i++) { %>
                    <% var o = etiquetas[i] %>
                    <option selected><%= o %></option>
                  <% } %>
                </select>
              </div>

              <div class="row">
                <?php for($i=1;$i<=10;$i++) { ?>

                  <?php if (isset($empresa->config["propiedad_custom_".$i."_file"])) { ?>
                    
                    <div class="col-xs-12">
                      <?php single_file_upload(array(
                        "name"=>"custom_$i",
                        "label"=>$empresa->config["propiedad_custom_".$i."_file"],
                        "url"=>"/sistema/propiedades/function/save_file/",
                      )); ?>
                    </div>

                  <?php } else if (isset($empresa->config["propiedad_custom_".$i."_label"])) { ?>
                    <div class="<?php echo (isset($empresa->config['propiedad_custom_'.$i.'_class'])) ? $empresa->config['propiedad_custom_'.$i.'_class'] :'col-xs-12'?>">
                      <div class="form-group">
                        <label class="control-label"><?php echo $empresa->config["propiedad_custom_".$i."_label"] ?></label>
                        <?php if(isset($empresa->config['propiedad_custom_'.$i.'_values'])) { 
                          $values = explode("|",$empresa->config['propiedad_custom_'.$i.'_values']); ?>
                          <select class="form-control" name="custom_<?php echo $i ?>">
                            <?php foreach($values as $value) { ?>
                              <option <%= (<?php echo "custom_".$i ?> == "<?php echo $value ?>")?"selected":""  %> value="<?php echo $value ?>"><?php echo $value ?></option>
                            <?php } ?>
                          </select>
                        <?php } else { ?>
                          <input type="text" name="custom_<?php echo $i ?>" id="propiedad_custom_<?php echo $i ?>" value="<%= custom_<?php echo $i ?> %>" class="form-control"/>
                        <?php } ?>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>
              </div>

            </div>
          </div>
        </div>

        <?php include_once("propiedad_precios.php") ?>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Ubicaci&oacute;n",
                    "en"=>"Location",
                  )); ?>
                </label>
                <a id="expand_mapa" class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Indique la direcci&oacute;n de la propiedad.",
                    "en"=>"Agregar variantes a productos como talle, color, etc.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" id="mapa_expandable">
            <div class="padder">

              <div class="row">
                <div class="col-md-4">
                  <label class="control-label">Pais</label>
                  <div class="form-group">
                    <select id="propiedad_paises" name="id_pais" class="form-control">
                      <% for(var i=0;i< paises.length;i++) { %>
                        <% var p = paises[i] %>
                        <option <%= (id_pais == p.id)?"selected":"" %> value="<%= p.id %>"><%= p.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="control-label">Provincia</label>
                  <div class="form-group">
                    <select id="propiedad_provincias" name="id_provincia" class="form-control">
                      <% for(var i=0;i< provincias.length;i++) { %>
                        <% var p = provincias[i] %>
                        <option data-id_pais="<%= p.id_pais %>" <%= (id_provincia == p.id)?"selected":"" %> value="<%= p.id %>"><%= p.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="control-label">Departamento / Partido</label>
                  <div class="form-group">
                    <select id="propiedad_departamentos" name="id_departamento" class="form-control"></select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Localidad</label>
                    <select id="propiedad_localidades" name="id_localidad" class="form-control"></select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Barrio</label>
                    <div class="input-group">
                      <select class="form-control" name="id_barrio" id="propiedad_barrio"></select>
                      <div class="input-group-btn">
                        <button id="cargar_mapa" class="btn btn-default">Actualizar Mapa</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Calle</label>
                    <input type="text" name="calle" id="propiedad_calle" value="<%= calle %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <div class="">
                      <label data-toggle="tooltip" title="Si esta activo, publica la altura de la calle." class="i-checks">
                        <input name="publica_altura" id="propiedad_publica_altura" value="1" type="checkbox" <%= (publica_altura == 1) ? "checked" : "" %>><i></i> 
                        Altura
                      </label>
                    </div>
                    <input type="text" name="altura" id="propiedad_altura" value="<%= altura %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Piso</label>
                    <input type="text" name="piso" id="propiedad_piso" value="<%= piso %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Dpto.</label>
                    <input type="text" name="numero" id="propiedad_numero" value="<%= numero %>" class="form-control"/>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Entre las calles</label>
                    <input type="text" name="entre_calles" id="propiedad_entre_calles" value="<%= entre_calles %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tipo de Acceso</label>
                    <select class="form-control" name="tipo_calle" id="propiedad_tipo_calle">
                      <option <%= (tipo_calle == 0)?"selected":"" %> value="0">Sin especificar</option>
                      <option <%= (tipo_calle == 1)?"selected":"" %> value="1">Asfalto</option>
                      <option <%= (tipo_calle == 2)?"selected":"" %> value="2">Tierra</option>
                      <option <%= (tipo_calle == 3)?"selected":"" %> value="3">Arena</option>
                      <option <%= (tipo_calle == 4)?"selected":"" %> value="4">Ripio</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div style="height:400px;" id="mapa"></div>
                <div class="help-block">
                  Puede arrastrar el marcador del mapa para ponerlo en la direccion exacta.<br/>
                  Tambi&eacute;n puede utilizar la vista de Street View, para mostrar el frente de la propiedad.
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Descripci&oacute;n u observaciones sobre la ubicaci&oacute;n</label>
                <textarea id="propiedad_descripcion_ubicacion" class="form-control" name="descripcion_ubicacion"><%= descripcion_ubicacion %></textarea>
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
                    "es"=>"Caracter&iacute;sticas ",
                    "en"=>"Location",
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
                    "es"=>"Agregue m&aacute;s datos espec&iacute;ficos de la propiedad, como superficie, cantidad de ambientes, etc.",
                    "en"=>"Agregar variantes a productos como talle, color, etc.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (!isEmpty(ambientes)) ? 'display:block':'' %>">
            <div class="padder">

              <div class="row">
                <div class="col-md-10">
                  <div class="row <%= (ID_EMPRESA == 685)?"dn":"" %>">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="control-label">Sup. Cubierta</label>
                        <input type="text" id="propiedad_superficie_cubierta" name="superficie_cubierta" value="<%= superficie_cubierta %>" class="form-control superficie"/>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="control-label">Descubierta</label>
                        <input type="text" id="propiedad_superficie_descubierta" name="superficie_descubierta" value="<%= superficie_descubierta %>" class="form-control superficie"/>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="control-label">Semicubierta</label>
                        <input type="text" id="propiedad_superficie_semicubierta" name="superficie_semicubierta" value="<%= superficie_semicubierta %>" class="form-control superficie"/>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="control-label">Total</label>
                        <input type="text" id="propiedad_superficie_total" name="superficie_total" value="<%= superficie_total %>" class="form-control"/>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="control-label">Mts. Frente</label>
                        <input type="text" id="propiedad_mts_frente" name="mts_frente" value="<%= mts_frente %>" class="form-control"/>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="control-label">Mts. Fondo</label>
                        <input type="text" id="propiedad_mts_fondo" name="mts_fondo" value="<%= mts_fondo %>" class="form-control"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 <%= (ID_EMPRESA == 685)?"dn":"" %>">
                  <div class="form-group">
                    <label class="control-label">Orientaci&oacute;n Depto.</label>
                    <select class="form-control" id="propiedad_ubicacion_departamento" name="ubicacion_departamento">
                      <option value="" <%= (ubicacion_departamento=="")?"selected":"" %>>Sin definir</option>
                      <option value="F" <%= (ubicacion_departamento=="F")?"selected":"" %>>Frente</option>
                      <option value="C" <%= (ubicacion_departamento=="C")?"selected":"" %>>Contrafrente</option>
                      <option value="I" <%= (ubicacion_departamento=="I")?"selected":"" %>>Interno</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label"><%= (ID_EMPRESA == 685)?"Camas":"Ambientes" %></label>
                    <input type="number" min="0" id="propiedad_ambientes" value="<%= ambientes %>" name="ambientes" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Dormitorios</label>
                    <input type="number" min="0" id="propiedad_dormitorios" value="<%= dormitorios %>" name="dormitorios" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Cocheras</label>
                    <input type="number" min="0" id="propiedad_cocheras" value="<%= cocheras %>" name="cocheras" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Ba&ntilde;os</label>
                    <input type="number" min="0" id="propiedad_banios" value="<%= banios %>" name="banios" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="oh">
                      <label class="i-checks m-t-xs">
                        <input type="checkbox" id="propiedad_patio" name="patio" class="checkbox" value="1" <%= (patio == 1)?"checked":"" %> >
                        <i></i>
                        Tiene patio
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="oh">
                      <label class="i-checks m-t-xs">
                        <input type="checkbox" id="propiedad_balcon" name="balcon" class="checkbox" value="1" <%= (balcon == 1)?"checked":"" %> >
                        <i></i>
                        Tiene balcon
                      </label>
                    </div>
                  </div>
                </div>

              </div>


              <div class="form-group">
                <label class="h5" style="font-weight:bold">Servicios</label>
              </div>
              <div class="form-group">
                <label class="i-checks m-r m-b">
                  <input type="checkbox" id="propiedad_servicios_cloacas" name="servicios_cloacas" class="checkbox" value="1" <%= (servicios_cloacas == 1)?"checked":"" %> >
                  <i></i> Cloacas
                </label>
                <label class="i-checks m-r m-b">
                  <input type="checkbox" id="propiedad_servicios_agua_corriente" name="servicios_agua_corriente" class="checkbox" value="1" <%= (servicios_agua_corriente == 1)?"checked":"" %> >
                  <i></i> Agua Corriente
                </label>
                <label class="i-checks m-r m-b">
                  <input type="checkbox" id="propiedad_servicios_electricidad" name="servicios_electricidad" class="checkbox" value="1" <%= (servicios_electricidad == 1)?"checked":"" %> >
                  <i></i> Electricidad
                </label>
                <label class="i-checks m-r m-b">
                  <input type="checkbox" id="propiedad_servicios_asfalto" name="servicios_asfalto" class="checkbox" value="1" <%= (servicios_asfalto == 1)?"checked":"" %> >
                  <i></i> Asfalto
                </label>
                <label class="i-checks m-r m-b">
                  <input type="checkbox" id="propiedad_servicios_gas" name="servicios_gas" class="checkbox" value="1" <%= (servicios_gas == 1)?"checked":"" %> >
                  <i></i> Gas
                </label>
                <label class="i-checks m-r m-b">
                  <input type="checkbox" id="propiedad_servicios_telefono" name="servicios_telefono" class="checkbox" value="1" <%= (servicios_telefono == 1)?"checked":"" %> >
                  <i></i> Tel&eacute;fono
                </label>
                <label class="i-checks m-r m-b">
                  <input type="checkbox" id="propiedad_servicios_cable" name="servicios_cable" class="checkbox" value="1" <%= (servicios_cable == 1)?"checked":"" %> >
                  <i></i> Cable
                </label>
              </div>

              <div class="form-group">
                <label class="h5 mb10" style="font-weight:bold">Otras caracter&iacute;sticas</label>
                <select data-placeholder="Ej: Aire acondicionado..." multiple id="propiedad_caracteristicas" style="width: 100%">
                  <% if (!isEmpty(caracteristicas)) { %>
                    <% var carac = caracteristicas.split(";;;") %>
                    <% for (var i=0; i< carac.length; i++) { %>
                      <% var o = carac[i] %>
                      <option selected><%= o %></option>
                    <% } %>
                  <% } %>
                </select>
                <span class="help-block m-b-none">Escriba una por una cada caracteristica y presione Enter.</span>
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
                    "es"=>"Multimedia",
                    "en"=>"Multimedia",
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
                    "es"=>"Agregue galeria de imagenes, videos, etc.",
                    "en"=>"Agregue galeria de imagenes, videos, etc.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (images.length>0 || ID_EMPRESA == 70) ? 'display:block':'' %>">
            <div class="padder">

              <?php
              multiple_upload(array(
                "name"=>"images",
                "label"=>"Galer&iacute;a de Fotos",
                "url"=>"propiedades/function/save_image/",
                "crop_type"=>(isset($empresa->config["propiedad_galeria_crop_type"]) ? $empresa->config["propiedad_galeria_crop_type"] : 0),
                "width"=>(isset($empresa->config["propiedad_galeria_image_width"]) ? $empresa->config["propiedad_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["propiedad_galeria_image_height"]) ? $empresa->config["propiedad_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["propiedad_galeria_image_quality"]) ? $empresa->config["propiedad_galeria_image_quality"] : 0),
                "upload_multiple"=>true,
              )); ?>

              <div style="<%= (ID_EMPRESA == 70)?'display:none':'' %>">
                <?php
                multiple_upload(array(
                  "name"=>"planos",
                  "label"=>"Planos",
                  "url"=>"propiedades/function/save_image/",
                  "width"=>(isset($empresa->config["propiedad_plano_image_width"]) ? $empresa->config["propiedad_plano_image_width"] : 1200),
                  "height"=>(isset($empresa->config["propiedad_plano_image_height"]) ? $empresa->config["propiedad_plano_image_height"] : 600),
                  "quality"=>(isset($empresa->config["propiedad_plano_image_quality"]) ? $empresa->config["propiedad_plano_image_quality"] : 0),
                )); ?>

                <div class="form-group">
                  <label class="control-label">Video</label>
                  <textarea id="propiedad_video" style="height:80px;" placeholder="Pegue aqui el codigo del video que desea insertar" class="form-control" name="video"><%= video %></textarea>
                </div>

                <?php
                single_file_upload(array(
                  "name"=>"archivo",
                  "label"=>"Archivo adjunto",
                  "url"=>"/sistema/propiedades/function/save_file/",
                )); ?>

                <?php
                single_file_upload(array(
                  "name"=>"audio",
                  "label"=>"Archivo de audio",
                  "url"=>"/sistema/propiedades/function/save_file/",
                )); ?>

                <div class="form-group">
                  <label class="control-label">Recorrido 3D</label>
                  <textarea id="propiedad_pint" style="height:80px;" placeholder="Pegue aqui el codigo que desea insertar" class="form-control" name="pint"><%= pint %></textarea>
                </div>

              </div>

            </div>
          </div>
        </div>

        <% if (ID_EMPRESA == 202 || ID_EMPRESA == 208 || ID_EMPRESA == 476 || ID_EMPRESA == 538) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Departamentos",
                      "en"=>"Departaments",
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
                      "es"=>"Agregue datos espec&iacute;ficos de los distintos departamentos o unidades que forman la obra.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (departamentos.length>0) ? 'display:block':'' %>">
              <div class="padder">
                <div class="clearfix tar">
                  <button class="btn btn-info nuevo_departamento">+ Agregar</button>
                </div>
                <div id="propiedad_departamentos" class="mt10"></div>
              </div>
            </div>
          </div>
        <% } %>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Respuesta Automática",
                    "en"=>"Automatic Response",
                  )); ?>
                </label>
                <a id="articulo_expand_respuesta_automatica" class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Puede configurar una respuesta automática personalizada cuando algun cliente consulta por esta propiedad.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" id="propiedad_respuesta_habilitado" name="respuesta_habilitado" class="checkbox" value="1" <%= (respuesta_habilitado == 1)?"checked":"" %> >
                  <i></i>
                  Habilitar respuesta automática para esta propiedad
                </label>
              </div>

              <div class="form-group">
                <textarea class="form-control" id="propiedad_respuesta_texto" name="respuesta_texto"><%= respuesta_texto %></textarea>
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
                    "es"=>"Observaciones",
                    "en"=>"Notes",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Agregue notas sobre la propiedad, tanto de uso privado como para la red.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (!isEmpty(nota_privada) || !isEmpty(nota_publica)) ? 'display:block':'' %>">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">
                  Nota Privada (no se muestra a la red):
                </label>
                <textarea class="form-control" id="propiedad_nota_privada" name="nota_privada"><%= nota_privada %></textarea>
              </div>
              <div class="form-group">
                <label class="control-label">
                  Nota Publica (se muestra a la red):
                </label>
                <textarea class="form-control" id="propiedad_nota_publica" name="nota_publica"><%= nota_publica %></textarea>
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
                    "es"=>"Mejore el posicionamiento de su web utilizando las siguientes opciones.",
                    "en"=>"Agregar variantes a productos como talle, color, etc.",
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
                  <span id="propiedad_seo_title_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>70</span>
                </label>
                <input type="text" data-max="70" data-id="propiedad_seo_title_cantidad" name="seo_title" id="propiedad_seo_title" value="<%= seo_title %>" class="form-control text-remain"/>
              </div>
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <label class="control-label fr">
                  <span id="propiedad_seo_description_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>160</span>
                </label>
                <textarea data-max="160" data-id="propiedad_seo_description_cantidad" name="seo_description" id="propiedad_seo_description" class="form-control text-remain"><%= seo_description %></textarea>
              </div>
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"C&oacute;digo de seguimiento",
                    "en"=>"",
                  )); ?>
                </label>
                <textarea name="codigo_seguimiento" id="propiedad_codigo_seguimiento" class="form-control"><%= codigo_seguimiento %></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="line b-b m-b-lg"></div>

      </div>

    </div>

    <div class="row">
      <div class="col-md-10 col-md-offset-1 tar">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>

  </div>
</div>
</script>


<script type="text/template" id="propiedades_departamentos_resultados_template">
<table id="departamentos_tabla" class="table table-small table-striped sortable m-b-none default footable">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Piso</th>
      <th class="th_acciones w50"></th>
    </tr>
  </thead>
  <tbody class="tbody"></tbody>
</table>
</script>

<script type="text/template" id="propiedades_departamentos_item_resultados_template">
<td class="text-info data"><%= nombre %></td>
<td class="data"><%= piso %></td>
<td class="tar td_acciones">
  <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
</td>
</script>

<script type="text/template" id="propiedad_departamento_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Editar departamento</b>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Nombre</label>
          <input type="text" required name="nombre" id="departamento_nombre" value="<%= nombre %>" class="form-control"/>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <% if (ID_EMPRESA == 208) { %>
            <label class="control-label">Galeria</label>
            <select class="form-control" name="piso" id="departamento_piso">
              <option <%= (piso=="Planos y vistas")?"selected":"" %>>Planos y vistas</option>
              <option <%= (piso=="Avance de obra")?"selected":"" %>>Avance de obra</option>
            </select>
          <% } else { %>
            <label class="control-label">Piso</label>
            <input type="text" name="piso" id="departamento_piso" value="<%= piso %>" class="form-control"/>
          <% } %>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Orden</label>
          <input type="text" name="orden" id="departamento_orden" value="<%= orden %>" class="form-control"/>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="i-checks">
        <input type="checkbox" id="departamento_disponible" name="disponible" class="checkbox" value="1" <%= (disponible == 1)?"checked":"" %> >
        <i></i>
        El departamento se encuentra disponible
      </label>
    </div>
    <div class="form-group">
      <label class="control-label">
        <?php echo lang(array(
          "es"=>"Descripci&oacute;n",
          "en"=>"Description",
        )); ?>
      </label>
      <textarea name="texto" name="departamento_texto" id="departamento_texto"><%= texto %></textarea>
    </div>
    <?php
    multiple_upload(array(
      "name"=>"images_dptos",
      "label"=>"Galer&iacute;a de Fotos",
      "url"=>"propiedades/function/save_image/",
      "width"=>(isset($empresa->config["departamento_galeria_image_width"]) ? $empresa->config["departamento_galeria_image_width"] : 800),
      "height"=>(isset($empresa->config["departamento_galeria_image_height"]) ? $empresa->config["departamento_galeria_image_height"] : 600),
      "quality"=>(isset($empresa->config["departamento_galeria_image_quality"]) ? $empresa->config["departamento_galeria_image_quality"] : 0),
    )); ?>
  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>

<script type="text/template" id="propiedad_mercado_libre_template">
  <div class="panel panel-default">
    <div class="panel-heading fs16 bold">
      Compartir a MercadoLibre
      <i class="fa fa-times cerrar cp fr"></i>
    </div>
    <div class="panel-body">
      <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
            <a id="propiedad_mercado_libre_paso_1_link" href="#propiedad_mercado_libre_tab1" class="buscar_todos" role="tab" data-toggle="tab">
              <i class="fa text-warning fa-calendar m-r-xs"></i>
              Datos
            </a>
          </li>
          <li>
            <a id="propiedad_mercado_libre_paso_2_link" href="#propiedad_mercado_libre_tab2" role="tab" data-toggle="tab">
              <i class="fa text-info fa-address-book m-r-xs"></i>
              Publicacion
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="propiedad_mercado_libre_tab1" class="tab-pane active">
            <div class="row">
              <% if (!multiple) { %>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Titulo</label>
                    <input id="propiedad_mercado_libre_titulo_meli" value="<%= titulo_meli %>" type="text" class="form-control" name="titulo_meli"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Precio</label>
                    <input id="propiedad_mercado_libre_precio_meli" value="<%= precio_meli %>" type="text" class="form-control" name="precio_meli"/>
                  </div>
                </div>
              <% } %>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Tipo de publicacion</label>
                  <select id="propiedad_mercado_libre_tipo_publicacion" class="form-control">
                    <option value="0">Seleccione</option>
                  </select>
                </div>
              </div>
            </div>
            <% if (!multiple) { %>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Descripcion</label>
                    <textarea style="height: 250px;" class="form-control" name="texto_meli" id="propiedad_mercado_libre_texto_meli"><%= texto_meli %></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <?php 
                  multiple_upload(array(
                    "name"=>"images_meli",
                    "label"=>"Im&aacute;genes adicionales",
                    "url"=>"propiedades/function/save_image/",
                    "width"=>(isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 800),
                    "height"=>(isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 600),
                    "resizable"=>(isset($empresa->config["producto_galeria_image_resizable"]) ? $empresa->config["producto_galeria_image_resizable"] : 0),
                    "upload_multiple"=>true,
                  )); ?>
                </div>
              </div>
            <% } else { %>
                <?php 
                multiple_upload(array(
                  "name"=>"images_meli",
                  "label"=>"Im&aacute;genes adicionales",
                  "url"=>"propiedades/function/save_image/",
                  "width"=>(isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 800),
                  "height"=>(isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 600),
                  "resizable"=>(isset($empresa->config["producto_galeria_image_resizable"]) ? $empresa->config["producto_galeria_image_resizable"] : 0),
                  "upload_multiple"=>true,
                )); ?>
            <% } %>
            <div class="clearfix tar">
              <button class="ir_paso_2 btn btn-success">Siguiente</button>
            </div>
          </div>
          <div id="propiedad_mercado_libre_tab2" class="tab-pane">
            <div style="overflow-y: auto;">
              <div style="height: 260px; text-align: center;" class="loading_grande">
                <img src="/sistema/resources/images/spinner.gif" style="line-height: 260px;"/>
              </div>
              <div id="propiedad_mercado_libre_categorias"></div>
            </div>
            <div class="clearfix m-t">
              <button class="ir_paso_1 btn btn-default">Anterior</button>
            </div>
          </div>
        </div> 
      </div>   
    </div>
  </div>
</script>

<script type="text/template" id="propiedad_mercado_libre_categoria_template">
  <select size="15" class="form-control categoria_mercado_libre" data-nivel="<%= nivel %>">
    <% for(var i=0; i< categories.length; i++) { %>
      <% var cat = categories[i] %>
      <option <%= (cat.id == selected)?"selected":"" %> value="<%= cat.id %>"><%= cat.name %></option>
    <% } %>
  </select>
</script>


<script type="text/template" id="propiedad_buscar_interesados_template">
<div class="panel panel-default">
  <div class="panel-heading fs16 bold">
    Interesados en la propiedad
    <i class="fa fa-times cerrar cp fr"></i>
  </div>
  <div class="panel-body">
    <div class="b-a table-responsive" style="height:250px; overflow:auto">
      <table id="propiedad_buscar_interesados_tabla" class="table table-striped sortable m-b-none default footable">
        <thead>
          <th>Nombre</th>
          <th>Fecha Interes</th>
          <th>Email</th>
          <th>Tel&eacute;fono</th>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div class="tar">
      <button class="btn btn-info enviar_emails btn-addon"><i class="icon fa fa-send"></i>Enviar email</button>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="propiedad_buscar_interesados_item_template">
<% var link_completo = 'https://' + DOMINIO + ((DOMINIO.substr(DOMINIO.length - 1) == "/") ? "" : "/") + link %>
<td><a href="app/#contacto_acciones/<%= id_contacto %>" class="text-info"><%= nombre %></a></td>
<td><%= fecha %></td>
<td>
  <label class="i-checks">
    <input data-id="<%= id_contacto %>" class="propiedad_buscar_interesados_checkbox" type="checkbox" checked value="1">
    <i></i>
  </label>
  <span class="text-info m-l-xs"><%= email %></span>
</td>
<td>
  <button data-link_completo="<%= link_completo %>" class="btn btn-success enviar_whatsapp_interesado"><i class="fa fa-whatsapp"></i></button>
  <span class="text-info m-l-xs"><%= telefono %></span>
</td>
</script>

<script type="text/template" id="propiedad_estadistica_detalle_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <b class="pull-left mt5"><%= nombre %> <%= (!isEmpty(codigo)) ? "("+codigo+")" : "" %></b>
    <button class="pull-right btn btn-default btn-small cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">  
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="render_tabla <%= (tab_default == "tabla")?"active":"" %>">
        <a href="#tab_propiedad_estadistica1" role="tab" data-toggle="tab"><i class="fa fa-list-ul text-info mr5"></i> Lista</a>
      </li>
      <li class="render_grafico <%= (tab_default == "grafico")?"active":"" %>">
        <a href="#tab_propiedad_estadistica2" role="tab" data-toggle="tab"><i class="fa fa-signal text-warning mr5"></i> Grafico</a>
      </li>
      <div class="pull-right mr5">
        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="propiedad_estadistica_fecha_desde" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>              
        </div>
        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="propiedad_estadistica_fecha_hasta" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>
        <button class="btn buscar btn-default pull-left"><i class="fa fa-search"></i></button>
      </div>
    </ul>
    <div class="tab-content">
      <div id="tab_propiedad_estadistica1" class="tab-pane pr0 pl0 panel-body <%= (tab_default == "tabla")?"active":"" %>">
        <div class="b-a" style="height:250px; overflow: auto;">
          <table id="propiedad_estadistica_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:150px">Fecha</th>
                <th>Contacto</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <div id="tab_propiedad_estadistica2" class="tab-pane pr0 pl0 panel-body <%= (tab_default == "grafico")?"active":"" %>">
        <div id="propiedad_estadistica_grafico" style="height:250px;"></div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="propiedad_preview_template">
  <?php include_once("propiedad_preview.php") ?>
</script>





<script type="text/template" id="propietarios_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
   <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i>Propietarios</h1>
  </div>
  <div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="search_container"></div>
        </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn btn-info btn-addon" href="app/#propietario"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="propietarios_table" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th class="sorting" data-sort-by="nombre">Nombre</th>
              <th class="sorting" data-sort-by="email">Email</th>
              <th class="sorting" data-sort-by="telefono">Telefono</th>
              <th class="sorting" data-sort-by="celular">Celular</th>
              <% if (permiso > 1) { %>
                <th class="w100 th_acciones">Acciones</th>
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


<script type="text/template" id="propietarios_item">
  <td class='ver'><span class="text-info"><%= nombre %></span></td>
  <td class='ver'><span class=''><%= email %></span></td>
  <td class='ver'><span class=''><%= telefono %></span></td>
  <td class='ver'><span class=''><%= celular %></span></td>
  <% if (permiso > 1) { %>
    <div class="btn-group dropdown">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
      <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>
  <% } %>
</script>

<script type="text/template" id="propietarios_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i>Propietarios / 
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
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <% if (edicion) { %>
                      <input type="text" name="nombre" class="form-control" id="propietarios_nombre" value="<%= nombre %>"/>
                    <% } else { %>
                      <span><%= nombre %></span>
                    <% } %>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <% if (edicion) { %>
                      <input type="text" name="email" class="form-control" id="propietarios_email" value="<%= email %>"/>
                    <% } else { %>
                      <span><%= email %></span>
                    <% } %>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Telefono</label>
                    <% if (edicion) { %>
                      <input type="text" name="telefono" class="form-control" id="propietarios_telefono" value="<%= telefono %>"/>
                    <% } else { %>
                      <span><%= telefono %></span>
                    <% } %>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Celular</label>
                    <% if (edicion) { %>
                      <input type="text" name="celular" class="form-control" id="propietarios_celular" value="<%= celular %>"/>
                    <% } else { %>
                      <span><%= celular %></span>
                    <% } %>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Direccion</label>
                <% if (edicion) { %>
                  <input type="text" name="direccion" class="form-control" id="propietarios_direccion" value="<%= direccion %>"/>
                <% } else { %>
                  <span><%= direccion %></span>
                <% } %>
              </div>
              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <% if (edicion) { %>
                  <textarea class="form-control" name="observaciones"><%= observaciones %></textarea>
                <% } else { %>
                  <span><%= observaciones %></span>
                <% } %>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-10 col-md-offset-1 clearfix">
        <button class="btn guardar fr btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="propietarios_edit_mini_panel_template">
  <div class="panel pb0 mb0">
  <div class="panel-body">
    <div class="oh m-b">
    <h4 class="h4 pull-left">Nuevo Propietario</h4>
    <i class="pull-right glyphicon glyphicon-remove text-muted cerrar"></i>
    </div>
    <div class="form-group">
    <input type="text" autocomplete="off" placeholder="Nombre" name="nombre" class="tab form-control" id="propietarios_mini_nombre"/>
    </div>
    <div class="form-group">
    <input type="text" autocomplete="off" placeholder="Email" name="email" class="tab form-control" id="propietarios_mini_email"/>
    </div>
    <div class="form-group">
    <input type="text" autocomplete="off" placeholder="Telefono" name="telefono" class="tab form-control" id="propietarios_mini_telefono"/>
    </div>
    <div class="form-group">
    <input type="text" autocomplete="off" placeholder="Celular" name="celular" class="tab form-control" id="propietarios_mini_celular"/>
    </div>
    <div class="form-group">
    <input type="text" autocomplete="off" placeholder="Direccion" name="direccion" class="tab form-control" id="propietarios_mini_direccion"/>
    </div>
    <div class="form-group">
    <textarea autocomplete="off" placeholder="Observaciones o notas..." name="observaciones" class="tab form-control h80" id="propietarios_mini_observaciones"></textarea>
    </div>
    <div class="text-right">
    <button class="btn guardar btn-success tab">Guardar</button>
    </div>
  </div>
  </div>
</script>

<script type="text/template" id="propiedad_temporada_panel_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Editar tarifa de temporada</b>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">Nombre</label>
      <input type="text" name="nombre" id="propiedad_temporada_nombre" value="<%= nombre %>" class="form-control"/>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Desde</label>
          <div class="input-group">
            <input type="text" id="propiedad_temporada_fecha_desde" value="<%= desde %>" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Hasta</label>
          <div class="input-group">
            <input type="text" id="propiedad_temporada_fecha_hasta" <%= hasta %> class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Estadia Min.</label>
          <input type="text" name="minimo_dias_reserva" id="propiedad_temporada_minimo_dias_reserva" value="<%= minimo_dias_reserva %>" class="form-control"/>
        </div>
      </div>      
    </div>
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Por Noche</label>
          <input type="text" id="propiedad_temporada_precio" value="<%= precio %>" name="precio" class="form-control"/>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Fin de Semana</label>
          <input type="text" id="propiedad_temporada_precio_finde" value="<%= precio_finde %>" name="precio_finde" class="form-control"/>
        </div>        
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Semana</label>
          <input type="text" id="propiedad_temporada_precio_semana" value="<%= precio_semana %>" name="precio_semana" class="form-control"/>
        </div>        
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Mes</label>
          <input type="text" id="propiedad_temporada_precio_mes" value="<%= precio_mes %>" name="precio_mes" class="form-control"/>
        </div>        
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn cancelar fl btn-default">Cancelar</button>
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>

<script type="text/template" id="propiedad_impuesto_panel_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Editar impuesto o tasa</b>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">Nombre</label>
      <input type="text" name="nombre" id="propiedad_impuesto_nombre" value="<%= nombre %>" class="form-control"/>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Tipo</label>
          <select class="form-control" name="tipo" id="propiedad_impuesto_tipo">
            <option value="1" <%= (tipo==1)?"selected":"" %>>Porcentaje por reserva</option>
            <option value="2" <%= (tipo==2)?"selected":"" %>>Tarifa por viajero</option>
            <option value="3" <%= (tipo==3)?"selected":"" %>>Tarifa por persona y noche</option>
            <option value="4" <%= (tipo==4)?"selected":"" %>>Tarifa por noche</option>
            <option value="5" <%= (tipo==5)?"selected":"" %>>Precio fijo por estadia</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Monto</label>
          <input type="text" id="propiedad_impuesto_monto" value="<%= monto %>" class="form-control">
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn cancelar fl btn-default">Cancelar</button>
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>