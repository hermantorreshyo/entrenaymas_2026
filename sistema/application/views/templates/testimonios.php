<script type="text/template" id="testimonios_panel_template">
    <div class="bg-light lter b-b wrapper-md ng-scope">
        <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i> Testimonios</h1>
    </div>
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
                <div class="row">
                    <div class="col-md-6 col-lg-3 sm-m-b">
                        <div class="search_container"></div>
                    </div>
                    <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                        <a class="btn btn-info btn-addon" href="app/#testimonio"><i class="fa fa-plus"></i>Nuevo</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="testimonios_table" class="table table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input class="esc sel_todos" type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th class="w50 tac hidden-xs"></th>
                                <th class="sorting" data-sort-by="nombre">Nombre</th>
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


<script type="text/template" id="testimonios_item">
    <td>
        <label class="i-checks m-b-none">
            <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
        </label>
    </td>
    <td class="ver hidden-xs">
        <% if (!isEmpty(path)) { %><img src="/sistema/<%= path %>" class="customcomplete-image"/><% } %>
    </td>
	<td><span class='ver'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
        <td class="p5 td_acciones">
            <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
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

<script type="text/template" id="testimonios_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal"></i> 
        Testimonios /
        <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
    </h1>
</div>
<div class="wrapper-md ng-scope">
    <div class="centrado rform">
        <div class="row">
            <div class="col-md-4">
                <div class="detalle_texto">Datos del testimonio</div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                    
                        <div class="padder">

                            <div class="form-group">
                                <label class="control-label">Nombre</label>
                                <% if (edicion) { %>
                                    <input type="text" name="nombre" class="form-control" id="testimonios_nombre" value="<%= nombre %>"/>
                                <% } else { %>
                                    <span><%= nombre %></span>
                                <% } %>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Texto</label>
                                <% if (edicion) { %>
                                    <textarea id="testimonios_texto" name="texto" class="form-control"><%= texto %></textarea>
                                <% } else { %>
                                    <span><%= texto %></span>
                                <% } %>
                            </div>
            				
                            <div class="form-group">
                                <label class="control-label">Link</label>
                                <% if (edicion) { %>
                                    <input type="text" name="link" class="form-control" id="testimonios_link" value="<%= link %>"/>
                                <% } else { %>
                                    <span><%= link %></span>
                                <% } %>
                            </div>				
            				
            				<?php
            				single_upload(array(
            				  "name"=>"path",
            				  "label"=>"Imagen",
            				  "url"=>"/sistema/testimonios/function/save_image/",
            				  "width"=>(isset($empresa->config["testimonio_image_width"]) ? $empresa->config["testimonio_image_width"] : 400),
            				  "height"=>(isset($empresa->config["testimonio_image_height"]) ? $empresa->config["testimonio_image_height"] : 400),
            				)); ?>                                         
                            
                        </div>
                    </div>
                </div>
                <% if (edicion) { %>
                    <button class="btn guardar btn-success">Guardar</button>
                <% } %>
            </div>
        </div>
    </div>
</div>
</script>