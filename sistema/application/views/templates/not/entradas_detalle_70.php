<div class="wrapper-md pb0">
  <div class="tab-container">
    <ul class="nav nav-tabs" role="tablist">
      <li class="active">
        <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Carga R&aacute;pida</a>
      </li>
      <li>
        <a href="#tab4" role="tab" data-toggle="tab"><i class="fa fa-plus"></i>M&aacute;s info</a>
      </li>
      <% if (ID_EMPRESA != 70) { %>
      <li>
        <a href="#tab6" role="tab" data-toggle="tab"><i class="fa fa-picture-o"></i>Multimedia</a>
      </li>
      <li>
        <a href="#tab2" id="link_tab2" role="tab" data-toggle="tab"><i class="fa fa-map-marker"></i>Ubicaci&oacute;n</a>
      </li>
      <% } %>
      <li>
        <a href="#tab3" role="tab" data-toggle="tab"><i class="fa fa-comments"></i>Comentarios</a>
      </li>
      <li>
        <a href="#tab8" role="tab" data-toggle="tab"><i class="fa fa-exchange"></i>Relacionados</a>
      </li>
      <li>
        <a href="#tab11" role="tab" data-toggle="tab"><i class="fa fa-globe"></i>SEO</a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane active panel-body">

        <div class="form-horizontal">

          <div class="form-group">
            <label class="col-md-2 control-label">Categoria</label>
            <div class="col-md-10">
              <select id="entrada_categorias" class="form-control"></select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-md-2 control-label">Titulo</label>
            <div class="col-md-10">
              <% if (edicion) { %>
                <input type="text" required name="titulo" id="entrada_titulo" value="<%= titulo %>" class="form-control"/>
              <% } else { %>
                <span><%= titulo %></span>
              <% } %>
            </div>
          </div>

          <div class="form-group">
            <label class="col-md-2 control-label">
            <%= (ID_EMPRESA != 70) ? "Subtitulo" : "Titulo para Facebook" %>
            </label>
            <div class="col-md-10">
            <% if (edicion) { %>
              <input type="text" name="subtitulo" id="entrada_subtitulo" value="<%= subtitulo %>" class="form-control"/>
            <% } else { %>
              <span><%= subtitulo %></span>
            <% } %>
            </div>
          </div>

          <div class="padder">
          <?php
          single_upload(array(
            "name"=>"path",
            "label"=>"Foto de Portada",
            "url"=>"/sistema/entradas/function/save_image/",
            "width"=>(isset($empresa->config["entrada_image_width"]) ? $empresa->config["entrada_image_width"] : 256),
            "height"=>(isset($empresa->config["entrada_image_height"]) ? $empresa->config["entrada_image_height"] : 256),
            "quality"=>(isset($empresa->config["entrada_image_quality"]) ? $empresa->config["entrada_image_quality"] : 0.92),
            "thumbnail_width"=>(isset($empresa->config["entrada_thumbnail_width"]) ? $empresa->config["entrada_thumbnail_width"] : 0),
            "thumbnail_height"=>(isset($empresa->config["entrada_thumbnail_height"]) ? $empresa->config["entrada_thumbnail_height"] : 0),
          )); ?>
          </div>

          <div class="form-group">
            <div class="col-xs-12">
            <textarea name="texto" id="entrada_texto"><%= texto %></textarea>
            </div>
          </div>

          <% if (ID_EMPRESA == 70) { %>
            <div class="col-md-6">
              <div class="h4">Galer&iacute;a de Im&aacute;genes</div>
              <div class="line b-b m-b"></div>

              <?php
              multiple_upload(array(
                "name"=>"images",
                "label"=>"",
                "url"=>"entradas/function/save_image/",
                "width"=>(isset($empresa->config["entrada_galeria_image_width"]) ? $empresa->config["entrada_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["entrada_galeria_image_height"]) ? $empresa->config["entrada_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["entrada_galeria_image_quality"]) ? $empresa->config["entrada_galeria_image_quality"] : 0.92),
              )); ?>

              <div class="h4 m-t-lg">Video</div>
              <div class="line b-b m-b"></div>

              <div class="form-group">
                <div class="col-md-12">
                  <% if (edicion) { %>
                    <textarea id="entrada_video" style="height:80px;" placeholder="Pegue aqui el codigo del video que desea insertar" class="form-control" name="video"><%= video %></textarea>
                  <% } else { %>
                    <span><%= video %></span>
                  <% } %>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="h4">Mapa</div>
              <div class="line b-b m-b"></div>
              <div class="">
                <input class="form-control" type="text" id="entrada_buscar_calle" placeholder="Buscar direccion..." />
              </div>
              <div style="height:400px;" id="mapa"></div>
              <div class="help-block"><button class="btn btn-default add_marker m-r">Agregar Marcador</button>Doble click al marcador para eliminarlo. </div>
            </div>
          <% } %>
          <div class="form-group">
            <label class="col-md-2 control-label">Etiquetas</label>
            <div class="col-md-10">
              <input class="select2_textarea w100p" type="text" id="entrada_etiquetas"/>
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

      <% if (ID_EMPRESA != 70) { %>
        <div id="tab2" class="tab-pane panel-body">
          <div class="form-horizontal">

          <div class="h4">Mapa</div>
          <div class="line b-b m-b"></div>

      <div class="form-group">
      <label class="col-md-2 control-label">Localidad</label>
      <div class="col-md-10">
        <% if (edicion) { %>
        <input type="text" name="localidad" id="entrada_localidad" value="<%= localidad %>" class="form-control"/>
        <% } else { %>
        <span><%= localidad %></span>
        <% } %>
      </div>
      </div>

      <div class="form-group">
      <label class="col-md-2 control-label">Pais</label>
      <div class="col-md-10">
        <select name="id_pais" id="entrada_pais" class="w100p"></select>
      </div>      
      </div>

          <div style="height:400px;" id="mapa"></div>
          <div class="help-block"><button class="btn btn-default add_marker m-r">Agregar Marcador</button>Puede arrastrar el marcador del mapa para ponerlo en la direccion exacta. Doble click para eliminarlo. </div>

          <div class="line line-dashed b-b line-lg pull-in"></div>
          <% if (edicion) { %>
            <div class="form-group">
              <div class="col-xs-12">
                <button class="btn btn-success guardar">Guardar</button>
                <img src="/sistema/resources/images/ajax-loader.gif" class="img_loading"/>
              </div>
            </div>
          <% } %>
          </div>
        </div>
      <% } %>

      <div id="tab3" class="tab-pane panel-body">
        <div class="form-horizontal">

        <div class="h4">Comentarios</div>
        <div class="line b-b m-b"></div>

        <div class="b-a table-responsive">
          <table id="entradas_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Comentario</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody class="tbody">
              <% if (comentarios.length == 0) { %>
                <tr><td colspan="5">La nota no tiene comentarios.</td></tr>
              <% } else { %>
                <% for (var i=0;i<comentarios.length;i++) { %>
                  <% var c = comentarios[i] %>
                  <tr>
                    <td><%= c.fecha %> a las <%= c.hora %></td>
                    <td><a class="text-info" href="app/#web_user/<%= c.id_usuario %>"><%= c.nombre %></a></td>
                    <td><%= c.texto %></td>
                    <td>
                      <i title="Activo" data-id="<%= c.id %>" title="Activo" class="fa-check fa activar_comentario iconito <%= (c.estado == 1)?"active":"" %>"></i>
                      <i title="Eliminar" data-id="<%= c.id %>" title="Eliminar" class="fa-remove eliminar_comentario fa iconito"></i>
                    </td>
                  </tr>
                <% } %>
              <% } %>
            </tbody>
          </table>
        </div>

        <div class="line line-dashed b-b line-lg pull-in"></div>
        <% if (edicion) { %>
          <div class="form-group">
            <div class="col-xs-12">
              <button class="btn btn-success guardar">Guardar</button>
              <img src="/sistema/resources/images/ajax-loader.gif" class="img_loading"/>
            </div>
          </div>
        <% } %>
      </div>
      </div>

      <div id="tab4" class="tab-pane panel-body">
        <div class="form-horizontal">
        <?php /*
        <div class="form-group">
          <label class="col-md-2 control-label">Antetitulo</label>
          <div class="col-md-10">
            <% if (edicion) { %>
              <input type="text" name="antetitulo" id="entrada_antetitulo" value="<%= antetitulo %>" class="form-control"/>
            <% } else { %>
              <span><%= antetitulo %></span>
            <% } %>
          </div>
        </div>
        */?>

        <div class="form-group">
          <label class="col-md-2 control-label">Fecha</label>
          <div class="col-md-10">
            <% if (edicion) { %>
              <input type="text" name="fecha" id="entrada_fecha" value="<%= fecha %>" class="form-control"/>
            <% } else { %>
              <span><%= fecha %></span>
            <% } %>
          </div>
        </div>

        <div class="form-group cb">
          <label class="col-md-2 control-label">Habilitar comentarios </label>
          <div class="col-md-10">
            <% if (edicion) { %>
              <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                <input type="checkbox" id="entrada_comentarios_activo" name="comentarios_activo" class="checkbox" value="1" <%= (comentarios_activo == 1)?"checked":"" %> >
                <i></i>
              </label>
            <% } else { %>
              <span><%= ((comentarios_activo==0) ? "No" : "Si") %></span>
            <% } %>
          </div>
        </div>

        <div class="form-group cb">
          <label class="col-md-2 control-label">Habilitar consulta </label>
          <div class="col-md-10">
            <label class="i-switch i-switch-md bg-info m-t-xs m-r">
            <input type="checkbox" id="entrada_habilitar_contacto" name="habilitar_contacto" class="checkbox" value="1" <%= (habilitar_contacto == 1)?"checked":"" %> >
            <i></i>
            </label>
          </div>
        </div>

        <?php
        single_file_upload(array(
          "name"=>"archivo",
          "label"=>"Archivo adjunto",
          "url"=>"/sistema/entradas/function/save_file/",
        )); ?>

    <?php
    if (isset($empresa->config["entrada_logo_mostrar"])) { 
      single_upload(array(
        "name"=>"logo",
        "label"=>(isset($empresa->config["entrada_logo_label"]) ? $empresa->config["entrada_logo_label"] : "Logo"),
        "url"=>"/sistema/entradas/function/save_image/",
        "width"=>(isset($empresa->config["entrada_logo_image_width"]) ? $empresa->config["entrada_logo_image_width"] : 256),
        "height"=>(isset($empresa->config["entrada_logo_image_height"]) ? $empresa->config["entrada_logo_image_height"] : 256),
        "quality"=>(isset($empresa->config["entrada_logo_image_quality"]) ? $empresa->config["entrada_logo_image_quality"] : 0.92),
      )); 
    } ?>

        <div class="form-group">
          <label class="col-md-2 control-label">Fuente</label>
          <div class="col-md-10">
            <% if (edicion) { %>
              <input type="text" name="fuente" id="entrada_fuente" value="<%= fuente %>" class="form-control"/>
            <% } else { %>
              <span><%= fuente %></span>
            <% } %>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">Descripcion breve</label>
          <div class="col-md-10">
            <% if (edicion) { %>
              <textarea id="entrada_descripcion" class="form-control h100" name="descripcion"><%= descripcion %></textarea>
            <% } else { %>
              <span><%= descripcion %></span>
            <% } %>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">Texto destacado</label>
          <div class="col-md-10">
            <% if (edicion) { %>
              <textarea id="entrada_texto_destacado" class="form-control h100" name="texto_destacado"><%= texto_destacado %></textarea>
            <% } else { %>
              <span><%= texto_destacado %></span>
            <% } %>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">Link Externo</label>
          <div class="col-md-10">
          <% if (edicion) { %>
            <input type="text" name="link_externo" id="entrada_link_externo" value="<%= link_externo %>" class="form-control"/>
          <% } else { %>
            <span><%= link_externo %></span>
          <% } %>
          </div>
        </div>        

        <?php /*
        <div class="form-group cb">
          <label class="col-md-2 control-label">Nuevo </label>
          <div class="col-md-10">
            <% if (edicion) { %>
              <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                <input type="checkbox" id="entrada_nuevo" name="nuevo" class="checkbox" value="1" <%= (nuevo == 1)?"checked":"" %> >
                <i></i>
              </label>
            <% } else { %>
              <span><%= ((nuevo==0) ? "No" : "Si") %></span>
            <% } %>
          </div>
        </div>
        */?>
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

      <% if (ID_EMPRESA != 70) { %>
        <div id="tab6" class="tab-pane panel-body">
          <div class="form-horizontal">

            <div class="h4">Galer&iacute;a de Im&aacute;genes</div>
            <div class="line b-b m-b"></div>

            <?php
            multiple_upload(array(
              "name"=>"images",
              "label"=>"Listado de Fotos",
              "url"=>"entradas/function/save_image/",
              "width"=>(isset($empresa->config["entrada_galeria_image_width"]) ? $empresa->config["entrada_galeria_image_width"] : 800),
              "height"=>(isset($empresa->config["entrada_galeria_image_height"]) ? $empresa->config["entrada_galeria_image_height"] : 600),
        "quality"=>(isset($empresa->config["entrada_galeria_image_quality"]) ? $empresa->config["entrada_galeria_image_quality"] : 0.8),
            )); ?>

            <div class="h4 m-t-lg">Video</div>
            <div class="line b-b m-b"></div>

            <div class="form-group">
              <div class="col-md-12">
                <% if (edicion) { %>
                  <textarea id="entrada_video" style="height:80px;" placeholder="Pegue aqui el codigo del video que desea insertar" class="form-control" name="video"><%= video %></textarea>
                <% } else { %>
                  <span><%= video %></span>
                <% } %>
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
      <% } %>

      <div id="tab8" class="tab-pane panel-body">
        <div class="">

          <div class="row">
          <div class="col-md-6 col-sm-12">
            <div class="panel panel-default">
            <div class="panel-heading font-bold">Relaciones con categorias</div>
            <div class="panel-body">
              <div id="entradas_categorias_tree" style="height: 400px; overflow: auto;"></div>
            </div>
            <div class="panel-footer bg-light lter">
              <div class="form-inline">
              <div class="form-group">
                <label>Mostrar</label>
              </div>
              <div class="form-group">
                <select class="form-control" name="relacionados_tipo">
                <option <%= (relacionados_tipo=="U")?"selected":"" %> value="U">ultimos</option>
                <option <%= (relacionados_tipo=="A")?"selected":"" %> value="A">aleatorios</option>
                </select>
              </div>
              <div class="form-group">
                <select class="form-control" name="relacionados_cantidad">
                <% for(j=1;j<=20;j++) { %>
                  <option <%= (j==relacionados_cantidad)?"selected":"" %> value="<%= j %>"><%= j %></option>
                <% } %>
                <option <%= (relacionados_cantidad==0)?"selected":"" %> value="0">Todos</option>
                </select>
              </div>
              <div class="form-group">
                <label>elementos de cada categoria.</label>
              </div>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="panel panel-default">
            <div class="panel-heading font-bold">Relaciones con otros entradas</div>
            <div class="panel-body">
              <div class="m-b">
              <input class="form-control" type="text" id="entradas_buscar_productos" placeholder="Busque los productos especificos con los que desea relacionar..." />
              </div>
              <div class="b-a table-responsive">
              <ul id="entradas_tabla_relacionados" style="height: 384px; overflow-y: auto;" class="list-group gutter list-group-lg list-group-sp">
                <% for(var i=0;i<relacionados.length;i++) { %>
                <% var a = relacionados[i]; %>
                <li class='list-group-item'>
                  <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>
                  <img style='margin-left: 10px; margin-right:10px; max-height:50px' src='/sistema/<%= a.path %>'/>
                  <span class='id dn'><%= a.id %></span>
                  <span class='titulo'><%= a.titulo %></span>
                  <span class='pull-right m-t eliminar_foto'><i class='fa fa-fw fa-times'></i> </span>
                </li>
                <% } %>
              </ul>
              </div>
            </div>
            </div>
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

      <div id="tab11" class="tab-pane panel-body">
        <div class="form-horizontal">
          <div class="form-group">
            <label class="col-lg-1 control-label">Titulo</label>
            <div class="col-lg-11">
            <textarea id="entrada_seo_title" class="form-control no-model"><%= seo_title %></textarea>
            <span class="help-block m-b-none">Titulo del navegador cuando se visualice la pagina.</span>
            </div>
          </div>
          <div class="line line-dashed b-b line-lg pull-in"></div>
          <div class="form-group">
            <label class="col-lg-1 control-label">Descripcion</label>
            <div class="col-lg-11">
            <textarea id="entrada_seo_description" class="form-control no-model"><%= seo_description %></textarea>
            <span class="help-block m-b-none">Escribe una breve descripcion del producto.</span>
            </div>
          </div>
          <div class="line line-dashed b-b line-lg pull-in"></div>
          <div class="form-group">
            <label class="col-lg-1 control-label">Palabras Clave</label>
            <div class="col-lg-11">
            <textarea id="entrada_seo_keywords" class="form-control no-model"><%= seo_keywords %></textarea>
            <span class="help-block m-b-none">Escribe las palabras clave que describan el entrada.</span>
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
