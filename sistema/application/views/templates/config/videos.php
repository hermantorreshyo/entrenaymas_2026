<script type="text/template" id="videos_panel_template">
    <div class="bg-light lter b-b wrapper-md ng-scope">
        <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n / <b>Videos</b></h1>
    </div>
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
                <div class="row">
                    <div class="col-md-6 col-lg-3 sm-m-b">
                        <div class="search_container"></div>
                    </div>
                    <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                        <a class="btn btn-info btn-addon" href="app/#video"><i class="fa fa-plus"></i>Nuevo</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="videos_table" class="table table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input class="esc sel_todos" type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th class="sorting" data-sort-by="clave">Clave</th>
                                <% if (permiso > 1) { %>
                                    <th class="w100"></th>
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


<script type="text/template" id="videos_item">
    <td>
        <label class="i-checks m-b-none">
            <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
        </label>
    </td>
	<td><span class='ver'><%= clave %></span></td>
	<% if (permiso > 1) { %>
        <td class="p5 td_acciones">
            <div class="btn-group dropdown ml10">
                <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-plus"></i>
                </button>        
                <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
                    <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
                </ul>
            </div>
        </td>
	<% } %>
</script>

<script type="text/template" id="videos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i> 
        Configuraci&oacute;n /
        <b><%= (id == undefined) ? 'Nuevo video' : 'Editar video' %></b>
    </h1>
</div>
<div class="wrapper-md ng-scope">
    <div class="centrado rform">
        <div class="row">
            <div class="col-md-4">
                <div class="detalle_texto">Datos del video</div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                    
                        <div class="padder">

                            <div class="form-group">
                                <label class="control-label">Clave</label>
                                <select id="videos_clave" name="clave" class="form-control">
                                    <option>Productos / Detalle / Texto 1</option>
                                    <option>Productos / Detalle / Texto 2</option>
                                    <option>Productos / Listado</option>
                                    <option>Marcas / Detalle / Texto 1</option>
                                    <option>Marcas / Detalle / Texto 2</option>
                                    <option>Marcas / Listado</option>
                                    <option>Propiedades / Detalle / Texto 1</option>
                                    <option>Propiedades / Detalle / Texto 2</option>
                                    <option>Propiedades / Listado</option>
                                    <option>Clientes / Detalle / Texto 1</option>
                                    <option>Clientes / Detalle / Texto 2</option>
                                    <option>Clientes / Listado</option>
                                    <option>Proveedores / Detalle / Texto 1</option>
                                    <option>Proveedores / Detalle / Texto 2</option>
                                    <option>Proveedores / Listado</option>
                                    <option>Paginas / Detalle / Texto 1</option>
                                    <option>Paginas / Detalle / Texto 2</option>
                                    <option>Paginas / Listado</option>
                                    <option>Habitaciones / Detalle / Texto 1</option>
                                    <option>Habitaciones / Detalle / Texto 2</option>
                                    <option>Habitaciones / Listado</option>
                                    <option>Viajes / Detalle / Texto 1</option>
                                    <option>Viajes / Detalle / Texto 2</option>
                                    <option>Viajes / Listado</option>
                                </select>
                            </div>

                            <div class="form-group lang-control">
                              <label class="control-label">T&iacute;tulo</label>
                              <div class="input-group">
                                <input type="text" id="videos_nombre_es" name="nombre_es" class="form-control active" value="<%= nombre_es %>"/>
                                <input type="text" id="videos_nombre_en" name="nombre_en" class="form-control" value="<%= nombre_en %>"/>
                                <div class="input-group-btn">
                                  <label class="btn btn-default btn-lang active" data-id="videos_nombre_es" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                                  <label class="btn btn-default btn-lang" data-id="videos_nombre_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                                </div>
                              </div>
                            </div>

                            <div class="form-group lang-control">
                              <label class="control-label">Texto</label>
                              <div class="input-group">
                                <textarea name="texto_es" class="form-control active" id="videos_texto_es"><%= texto_es %></textarea>
                                <textarea name="texto_en" class="form-control" id="videos_texto_en"><%= texto_en %></textarea>
                                <div class="input-group-btn">
                                  <label class="btn btn-default btn-lang active" data-id="videos_texto_es" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                                  <label class="btn btn-default btn-lang" data-id="videos_texto_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                                </div>
                              </div>
                            </div>

                            <div class="form-group lang-control">
                              <label class="control-label">Video</label>
                              <div class="input-group">
                                <textarea name="video_es" class="form-control active" id="videos_video_es"><%= video_es %></textarea>
                                <textarea name="video_en" class="form-control" id="videos_video_en"><%= video_en %></textarea>
                                <div class="input-group-btn">
                                  <label class="btn btn-default btn-lang active" data-id="videos_video_es" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                                  <label class="btn btn-default btn-lang" data-id="videos_video_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                                </div>
                              </div>
                            </div>

                        </div>
                    </div>
                </div>
                <button class="btn guardar btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>

</script>