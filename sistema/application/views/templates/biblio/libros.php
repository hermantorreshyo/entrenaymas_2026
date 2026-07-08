<script type="text/template" id="libros_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("menu_biblioteca") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
      / <b>Libros</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="col-lg-4 col-md-6 col-sm-9 col-xs-12">
        <div class="row">
          <div class="input-group">
            <input type="text" id="libros_buscar" placeholder="Buscar..." value="<%= filter %>" autocomplete="off" class="form-control">
            <span class="input-group-btn">
            <button class="btn btn-default"><i class="fa fa-search"></i></button>
            </span>
            <span class="input-group-btn">
            <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
            </span>
          </div>
        </div>
        </div>
        <% if (!seleccionar) { %>
          <div class="pull-right">          
            <div class="btn-group dropdown ml5">
              <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span>Acciones</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right">
                <li><a href="javascript:void" class="eliminar_lote">Eliminar</a></li>
              </ul>
            </div>          
            <a class="btn btn-info btn-addon ml5" href="<%= (id_autor == 0) ? "app/#libro" : "app/#nuevo_libro/"+id_autor %>">
              <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
            </a>
          </div>
        <% } %>
      </div>
      <div class="advanced-search-div bg-light dk" style="display:none">
      <div class="wrapper oh">
        <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
        <div class="form-inline">
        <div style="width: 250px; display: inline-block">
          <select id="libros_buscar_autores" class="w100p"></select>
        </div>
        <div style="width: 250px; display: inline-block">
          <select id="libros_buscar_etiquetas" class="w100p"></select>
        </div>
        <div class="form-group">
          <button id="libros_buscar_avanzada_btn" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button>
        </div>
        </div>
      </div>
      </div>
    
      <div class="panel-body">
        <div class="b-a table-responsive">
        <table id="libros_tabla" class="table table-striped sortable m-b-none default footable">
          <thead>
          <tr>
            <% if (!seleccionar) { %>
            <th style="width:20px;">
              <label class="i-checks m-b-none">
                <input class="esc sel_todos" type="checkbox"><i></i>
              </label>
            </th>            
            <% } else { %>
            <th style="width:20px;"></th>
            <% } %>
            <th class="sorting" data-sort-by="nombre">Titulo</th>
            <th class="sorting" data-sort-by="autor">Autor</th>
            <th class="sorting" data-sort-by="stock">Cant. Ejemplares</th>
            <th class="sorting" data-sort-by="disponibles">Disponibles</th>
            <th></th>
            <% if (!seleccionar) { %>
            <th style="width:150px;text-align:right">Acciones</th>
            <% } %>
          </tr>
          </thead>
          <tbody class="tbody"><tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
        </div>
      </div>
      <!--
      <div class="panel-footer clearfix bg-light lter">
      <button class="btn btn-info enviar btn-addon pull-left"><i class="icon fa fa-send"></i>Enviar</button>
      </div>
      -->
    </div>
  </div>
</script>

<script type="text/template" id="libros_item_resultados_template">
  <% var clase = (activo==1) ? ((disponibles<=0)?"text-danger":"") : "text-muted"; %>
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
  <% } %>
  <td class="<%= clase %> data"><%= nombre %></td>
  <td class="<%= clase %> data"><%= autor %></td>
  <td class="<%= clase %> data"><%= stock %></td>
  <td class="<%= clase %> data"><%= disponibles %></td>
  <td class="tac">
    <% if (disponibles > 0) { %>
      <a href="javascript:void" class="prestar" style="color: inherit;">
        <i class="fa text-info fa-address-book m-r-xs"></i>
        Prestar
      </a>
    <% } %>
  </td>
  <% if (!seleccionar) { %>
    <td class="tar <%= clase %>">
    <div class="fr">
      <i title="Activo" class="fl mr5 fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown fl">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
        <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
      </div>    
    </div>
    </td>
  <% } %>
</script>


<script type="text/template" id="libro_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("menu_biblioteca") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
      / Libros
      / <b><%= (id == undefined) ? "Nuevo" : nombre %></b>
    </h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
  			<li class="active">
  				<a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Carga R&aacute;pida</a>
  			</li>
  			<li>
  				<a href="#tab4" role="tab" data-toggle="tab"><i class="fa fa-plus"></i>Detalles</a>
  			</li>
  			<li>
  				<a href="#tab3" id="historial_tab" role="tab" data-toggle="tab"><i class="fa fa-list-ul"></i>Historial Pr&eacute;stamos</a>
  			</li>		  
      </ul>
      <div class="tab-content">
        <div id="tab1" class="tab-pane active panel-body">
              
          <div class="form-horizontal">          
            
            <div class="form-group">
              <label class="col-md-2 control-label">Titulo</label>
              <div class="col-md-10">
                <% if (edicion) { %>
                  <input type="text" required name="nombre" id="libro_nombre" value="<%= nombre %>" class="form-control"/>
                <% } else { %>
                  <span><%= nombre %></span>
                <% } %>
              </div>
            </div>
            
            <div class="form-group">
              <label class="col-md-2 control-label">Autor</label>
              <div class="col-md-10">
                <div class="input-group">
                <select id="libro_autores" style="width: 100%" class="form-control"></select>
                <div class="input-group-btn">
                  <button type="button" class="btn btn-success nuevo_autor">Nuevo</button>
                </div>
                </div>          
              </div>
            </div>
            
            <div class="form-group">
              <label class="col-md-2 control-label">Cant. Ejemplares</label>
              <div class="col-md-10">
                <% if (edicion) { %>
                  <input type="number" name="stock" id="libro_stock" value="<%= stock %>" class="form-control"/>
                <% } else { %>
                  <span><%= stock %></span>
                <% } %>
              </div>
            </div>
            
            <div class="form-group">
              <label class="col-md-2 control-label">Ejemplares disponibles</label>
              <div class="col-md-10">
                <% if (edicion) { %>
                  <input type="number" name="disponibles" id="libro_disponibles" value="<%= disponibles %>" class="form-control"/>
                <% } else { %>
                  <span><%= disponibles %></span>
                <% } %>
              </div>
            </div>          
            
            <div class="form-group">
              <label class="col-md-2 control-label">Etiquetas</label>
              <div class="col-md-10">
                <select data-placeholder="Ej: genero, palabras clave, etc..." multiple id="libro_etiquetas" style="width: 100%">
                <% for (var i=0; i< etiquetas.length; i++) { %>
                  <% var o = etiquetas[i] %>
                  <option selected><%= o %></option>
                <% } %>
                </select>
              </div>
            </div>          
  					
            <div class="line line-dashed b-b line-lg pull-in"></div>
            <% if (edicion) { %>
              <div class="form-group">
                <div class="col-xs-12">
                  <button class="btn guardar btn-success">Guardar</button>
                </div>
              </div>
            <% } %>          
            
          </div>
        </div>
        
        <div id="tab3" class="tab-pane panel-body">
          <div class="form-horizontal">
          
          <div class="h4">Historial de Pr&eacute;stamos</div>
          <div class="line b-b m-b"></div>
          <div id="libros_historial"></div>
        </div>
        </div>  			
        
        <div id="tab4" class="tab-pane panel-body">
        <div class="form-horizontal">
          
          <div class="form-group">
            <label class="col-md-2 control-label">ISBN / C&oacute;digo</label>
            <div class="col-md-10">
              <% if (edicion) { %>
                <input type="text" name="isbn" id="libro_isbn" value="<%= isbn %>" class="form-control"/>
              <% } else { %>
                <span><%= isbn %></span>
              <% } %>
            </div>
          </div>        
          
          
          <div class="form-group">
            <label class="col-md-2 control-label">Editorial</label>
            <div class="col-md-10">
              <% if (edicion) { %>
                <input type="text" name="editorial" id="libro_editorial" value="<%= editorial %>" class="form-control"/>
              <% } else { %>
                <span><%= editorial %></span>
              <% } %>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-md-2 control-label">Edicion</label>
            <div class="col-md-10">
              <% if (edicion) { %>
                <input type="text" name="numero_edicion" id="libro_numero_edicion" value="<%= numero_edicion %>" class="form-control"/>
              <% } else { %>
                <span><%= numero_edicion %></span>
              <% } %>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-md-2 control-label">A&ntilde;o</label>
            <div class="col-md-10">
              <% if (edicion) { %>
                <input type="text" name="numero_anio" id="libro_anio" value="<%= anio %>" class="form-control"/>
              <% } else { %>
                <span><%= anio %></span>
              <% } %>
            </div>
          </div>
          
          <?php
          single_upload(array(
            "name"=>"path",
            "label"=>"Foto de Portada",
            "url"=>"/sistema/libros/function/save_image/",
            "width"=>(isset($empresa->config["libro_image_width"]) ? $empresa->config["libro_image_width"] : 256),
            "height"=>(isset($empresa->config["libro_image_height"]) ? $empresa->config["libro_image_height"] : 256),
          )); ?>
          
          <?php
          single_file_upload(array(
            "name"=>"archivo",
            "label"=>"Archivo adjunto",
            "url"=>"/sistema/libros/function/save_file/",
          )); ?>
          
          <div class="form-group">
          <div class="col-xs-12">
            <textarea name="texto" id="libro_sinopsis"><%= sinopsis %></textarea>
          </div>
          </div>
          
          <div class="line line-dashed b-b line-lg pull-in"></div>
          <% if (edicion) { %>
            <div class="form-group">
              <div class="col-xs-12">  
                <button class="btn btn-success guardar">Guardar</button>
              </div>
            </div>
          <% } %>
        </div>
        </div>
        
      </div>
    </div>
  </div>
</script>
