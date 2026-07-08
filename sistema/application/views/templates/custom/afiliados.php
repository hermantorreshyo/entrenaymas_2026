<script type="text/template" id="afiliados_panel_template">
<div class="seccion_vacia" style="display:none">
    <h1 class="h1">Todav&iacute;a no ten&eacute;s ning&uacute;n afiliado</h1>
    <h3 class="h3">Para crear tu primer afiliado, hace click en el siguiente bot&oacute;n</h3>
    <div class="list-icon">
      <a href="app/#afiliado"><i class="icon-note"></i></a>
    </div>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#afiliado">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
    <p>
      Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
    </p>
</div>
<div class="seccion_llena" style="display:none">
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i>Afiliados</h1>
    </div>
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading clearfix">
                <div class="row">
                    <div class="<% if (!seleccionar) { %>col-md-6 col-lg-3 <% } else { %> col-xs-12 <% } %> sm-m-b">
    				  <div class="input-group">
    					  <input type="text" id="afiliados_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
    					  <span class="input-group-btn">
    						<button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
    					  </span>
    				  </div>
    				</div>
                    <% if (!seleccionar) { %>
                        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">

                            <div class="btn-group dropdown">
                              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                                <i class="fa fa-cog"></i><span>Opciones</span>
                                <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu">
                                <li><a href="javascript:void(0)" class="exportar_excel">Exportar Excel</a></li>
                                <li><a href="javascript:void(0)" class="importar_excel">Importar Excel</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)" class="exportar_csv">Exportar TXT</a></li>
                                <li><a href="javascript:void(0)" class="importar_csv">Importar TXT</a></li>
                              </ul>
                            </div>    
                            <a class="btn btn-info btn-addon ml5" href="app/#afiliado">
                                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
                            </a>
                        </div>
                    <% } %>
                </div>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="afiliados_table" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
                                <th style="width:20px;"></th>
                                <% if (!seleccionar) { %>
                                  <th class="w50 tac hidden-xs"></th>
                                <% } %>
                                <th class="sorting" data-sort-by="apellido">Apellido</th>
								<th class="sorting" data-sort-by="nombre">Nombre</th>
                                <% if (!seleccionar) { %>
                                    <th class="col-xxs-0 sorting" data-sort-by="telefono">Telefono</th>
                                    <th class="col-xxs-0 sorting" data-sort-by="email">Email</th>
                                    <th class="col-xxs-0 sorting" data-sort-by="fecha_inicial">Fecha de alta</th>
                                    <th class="col-xxs-0 sorting" data-sort-by="id_usuario">Usuario</th>
                                <% } %>
                                <% if (permiso > 1) { %>
                                    <th class="th_acciones w120">Acciones</th>
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
</div>
</script>


<script type="text/template" id="afiliados_item">
	<% var clase = (activo==1)?"":"text-muted"; %>
    <td>
          <label class="i-checks m-b-none">
              <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
          </label>
    </td>
    <td class="<%= clase %> data hidden-xs">
        <span class="avatar avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %> pull-left">
            <%= isEmpty(nombre) ? email.substr(0,1) : nombre.substr(0,1) %>
        </span>
    </td>
    <td class='data'><span class="<%= (activo==1)?'text-info':'text-muted' %>"><%= apellido %></span></td>
	<td class='data'><span class="<%= (activo==1)?'text-info':'text-muted' %>"><%= nombre %></span></td>
    <% if (!seleccionar) { %>
        <td class="data col-xxs-0 <%= clase %>"><span><%= (isEmpty(telefono))?"—":telefono %></span></td>
        <td class="data col-xxs-0 <%= clase %>"><span><%= (isEmpty(email))?"—":email %></span></td>
        <td class="data col-xxs-0 <%= clase %>"><span><%= (isEmpty(fecha_inicial))?"—":fecha_inicial %></span></td>
        <td class="data col-xxs-0 <%= clase %>"><span><%= (isEmpty(usuario))?"—":usuario %></span></td>
    <% } %>    
	<% if (permiso > 1) { %>
        <td class="p5 <%= clase %> td_acciones">
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

<script type="text/template" id="afiliados_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i>Afiliados
        / <b><%= (id == undefined)?"Nuevo":nombre %></b>
    </h1>
</div>
<div class="wrapper-md">
    <div class="centrado rform">
        <div class="row">
            <div class="col-md-4">
                <div class="detalle_texto">Informaci&oacute;n general</div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="padder">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Apellido</label>
                                        <input type="text" required name="apellido" id="afiliados_apellido" value="<%= apellido %>" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Nombre</label>
                                        <input type="text" required name="nombre" id="afiliados_nombre" value="<%= nombre %>" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">DNI </label>
                                        <input type="text" name="dni" class="form-control" id="afiliados_dni" value="<%= dni %>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Fecha de Nacimiento </label>
                                        <input type="text" name="fecha_nac" class="form-control" id="afiliados_fecha_nac" value="<%= fecha_nac %>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Calle</label>
                                        <input type="text" name="calle" value="<%= calle %>" id="afiliados_calle" class="form-control"/>
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Numero</label>
                                        <input type="text" name="numero" value="<%= numero %>" id="afiliados_numero" class="form-control"/>
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Piso</label>
                                        <input type="text" name="piso" value="<%= piso %>" id="afiliados_piso" class="form-control"/>
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Dpto.</label>
                                        <input type="text" name="depto" value="<%= depto %>" id="afiliados_depto" class="form-control"/>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Localidad</label>
                                        <input type="text" name="localidad" value="<%= localidad %>" id="afiliados_localidad" class="form-control"/>
                                    </div>  
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Partido</label>
                                        <input type="text" name="partido" value="<%= partido %>" id="afiliados_partido" class="form-control"/>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Profesion</label>
                                        <input type="text" name="profesion" value="<%= profesion %>" id="afiliados_profesion" class="form-control"/>
                                    </div>  
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Sexo</label>
                                        <select class="form-control" id="afiliados_sexo" name="sexo">
                                            <option <%= (sexo=="M")?"selected":"" %> value="M">Masculino</option>
                                            <option <%= (sexo=="F")?"selected":"" %> value="F">Femenino</option>
                                        </select>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Tel&eacute;fono </label>
                                        <input type="text" name="telefono" class="form-control" id="afiliados_telefono" value="<%= telefono %>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Celular </label>
                                        <input type="text" name="celular" class="form-control" id="afiliados_celular" value="<%= celular %>"/>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Email </label>
                                <input type="text" name="email" class="form-control" id="afiliados_email" value="<%= email %>"/>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Contrase&ntilde;a</label>
                                        <input type="password" class="form-control" id="afiliados_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Repetir contrase&ntilde;a</label>
                                        <input type="password" class="form-control" id="afiliados_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox">
                                    <label class="i-checks">
                                        <input type="checkbox" name="afiliado" class="checkbox" value="1" <%= (afiliado == 1)?"checked":"" %> ><i></i>
                                        Es afiliado?
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Facebook </label>
                                        <input type="text" name="facebook" class="form-control" id="afiliados_facebook" value="<%= facebook %>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Twitter </label>
                                        <input type="text" name="twitter" class="form-control" id="afiliados_twitter" value="<%= twitter %>"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Instagram </label>
                                        <input type="text" name="instagram" class="form-control" id="afiliados_instagram" value="<%= instagram %>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Otras redes sociales </label>
                                        <input type="text" name="otras_redes" class="form-control" id="afiliados_otras_redes" value="<%= otras_redes %>"/>
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
            <div class="col-md-8">
                <button class="btn guardar btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>
</script>