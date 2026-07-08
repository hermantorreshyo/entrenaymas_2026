<script type="text/template" id="cursos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("cursos") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <?php 
      $active = "cursos";
      include("cursos_menu.php") ?>
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("cursos") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#curso"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cursos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Titulo</th>
                <th class="sorting" data-sort-by="categoria">Categoria</th>
                <th class="sorting" data-sort-by="autor">Autor</th>
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


<script type="text/template" id="cursos_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><%= categoria %></td>
  <td class="ver"><%= autor %></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <i title="Destacado" class="fa-star iconito fa destacado <%= (destacado == 1)?"active":"" %>"></i>
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

<script type="text/template" id="cursos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("cursos") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Titulo</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" id="cursos_nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Subtitulo</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="subtitulo" class="form-control" id="cursos_subtitulo" value="<%= subtitulo %>"/>
                  </div>
                </div>
              </div>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">Descripcion</label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="curso_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="curso_texto_cont">
                    <textarea name="texto" name="texto" id="curso_texto"><%= texto %></textarea>
                  </div>
                </div>
              </div>

              <div class="form-group mb0 tar">
                <a id="expand_principal" class="expand-link">
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

              <div class="form-group">
                <label class="control-label">Etiquetas</label>
                <select multiple id="curso_etiquetas" style="width: 100%">
                  <% for (var i=0; i< etiquetas.length; i++) { %>
                    <% var o = etiquetas[i] %>
                    <option selected><%= o %></option>
                  <% } %>
                </select>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      <label class="i-checks m-b-none">
                        <input id="curso_mostrar_fecha" value="1" <%= (mostrar_fecha==1)?"checked":"" %> type="checkbox"><i></i>
                        <?php echo lang(array(
                          "es"=>"Publicar la fecha",
                          "en"=>"Show publication date",
                        )); ?>
                      </label>
                    </label>
                    <div class="input-group">
                      <input type="text" name="fecha" id="curso_fecha" value="<%= fecha %>" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>              
              </div>

              <div class="row">
                <?php for($i=1;$i<=10;$i++) { ?>

                  <?php if (isset($empresa->config["curso_custom_".$i."_file"])) { ?>
                    
                    <div class="col-xs-12">
                      <?php single_file_upload(array(
                        "name"=>"custom_$i",
                        "label"=>$empresa->config["curso_custom_".$i."_file"],
                        "url"=>"/sistema/cursos/function/save_file/",
                      )); ?>
                    </div>

                  <?php } else if (isset($empresa->config["curso_custom_".$i."_label"])) { ?>
                    <div class="<?php echo (isset($empresa->config['curso_custom_'.$i.'_class'])) ? $empresa->config['curso_custom_'.$i.'_class'] :'col-xs-12'?>">
                      <div class="form-group">
                        <label class="control-label"><?php echo $empresa->config["curso_custom_".$i."_label"] ?></label>
                        <?php if(isset($empresa->config['curso_custom_'.$i.'_values'])) { 
                          $values = explode("|",$empresa->config['curso_custom_'.$i.'_values']); ?>
                          <select class="form-control" name="custom_<?php echo $i ?>">
                            <?php foreach($values as $value) { ?>
                              <option <%= (<?php echo "custom_".$i ?> == "<?php echo $value ?>")?"selected":""  %> value="<?php echo $value ?>"><?php echo $value ?></option>
                            <?php } ?>
                          </select>
                        <?php } else { ?>
                          <input type="text" name="custom_<?php echo $i ?>" id="curso_custom_<?php echo $i ?>" value="<%= custom_<?php echo $i ?> %>" class="form-control"/>
                        <?php } ?>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>
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
                    "en"=>"Media",
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
                    "en"=>"Create a image gallery, add a single video or atachmentt files...",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <?php
              single_file_upload(array(
                "name"=>"path",
                "label"=>"Imagen principal",
                "url"=>"/sistema/cursos/function/save_file/",
              )); ?>

              <?php
              single_file_upload(array(
                "label"=>"Archivo para descargar",
                "name"=>"archivo",
                "url"=>"/sistema/cursos/function/save_file/",
              )); ?>              

              <?php
              multiple_upload(array(
                "name"=>"images",
                "label"=>lang(array("en"=>"Image Gallery","es"=>"Galería de fotos")),
                "url"=>"cursos/function/save_image/",
                "url_file"=>"cursos/function/save_file/",
                "width"=>(isset($empresa->config["curso_galeria_image_width"]) ? $empresa->config["curso_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["curso_galeria_image_height"]) ? $empresa->config["curso_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["curso_galeria_image_quality"]) ? $empresa->config["curso_galeria_image_quality"] : 0.8),
              )); ?>

              <div class="form-group <%= (MILLING == 1)?"dn":"" %>">
                <label class="control-label">Video</label>
                <textarea id="curso_video" style="height:80px;" placeholder="<?php echo lang(array('es'=>'Inserte aquí el código de insercción de su video','en'=>'Paste here your insertion code'));?>" class="form-control" name="video"><%= video %></textarea>
              </div>

            </div>
          </div>
        </div>        

        <div class="panel panel-default <%= (MILLING == 1 || ID_EMPRESA == 1099 || ID_EMPRESA == 1372)?"dn":"" %>">
          <div class="panel-body">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Categoria</label>
                    <select <%= (!edicion)?"disabled":"" %> id="curso_categorias" name="id_categoria" class="form-control"></select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Autor</label>
                    <select <%= (!edicion)?"disabled":"" %> id="curso_autores" name="id_autor" class="form-control"></select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tipo de Curso</label>
                    <select <%= (!edicion)?"disabled":"" %> class="form-control" name="tipo" id="curso_tipo">
                      <option <%= (tipo==0)?"selected":"" %> value="0">Abierto (sin registro)</option>
                      <option <%= (tipo==1)?"selected":"" %> value="1">Gratuito (con registro pero gratis)</option>
                      <option <%= (tipo==2)?"selected":"" %> value="2">Pago (con registro y proceso de pago)</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Moneda</label>
                    <select id="curso_monedas" class="form-control" name="moneda">
                      <% for(var i=0;i< window.monedas.length;i++) { %>
                        <% var o = monedas[i]; %>
                        <option <%= (o.id == moneda)?"selected":"" %> value="<%= o.id %>"><%= o.signo %> (<%= o.nombre %>)</option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Precio Final</label>
                    <input id="curso_precio_final" value="<%= precio_final %>" type="text" class="form-control number b-success" name="precio_final" <%= (!edicion)?"disabled":"" %>/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">% Descuento</label>
                    <input id="curso_porc_bonif" value="<%= porc_bonif %>" type="text" class="form-control number" name="porc_bonif" <%= (!edicion)?"disabled":"" %>/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Final c/dto.</label>
                    <input disabled id="curso_precio_final_dto" value="<%= precio_final_dto %>" type="text" class="form-control number" name="precio_final_dto"/>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>          

        <div class="panel panel-default <%= (MILLING == 1)?"dn":"" %>">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Clases</label>
                <div class="panel-description">
                  Agrege los distintos recursos que identifican cada clase, como puede ser un videos,
                  archivos PDF, documentos, etc.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="display:block">
            <div class="padder">
              <div class="clearfix tar">
                <button class="btn btn-info nuevo_clase fr">+ Agregar</button>
              </div>
              <div id="cursos_clases" class="mt10"></div>

              <% if (ID_EMPRESA == 1372) { %>
                <div class="form-group mt20">
                  <label class="control-label">Texto de finalización exitosa</label>
                  <textarea id="curso_clase_texto_finalizacion" style="height:80px;" class="form-control" name="texto_finalizacion"><%= texto_finalizacion %></textarea>
                </div>
              <% } %>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Usuarios",
                    "en"=>"Users",
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
                    "es"=>"Usuarios que estan desarrollando el curso.",
                    "en"=>"Create a image gallery, add a single video or atachmentt files...",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder"> 
              <div class="form-group">
                <div class="input-group">
                  <input type="text" placeholder="Escriba un nombre..." id="cursos_clientes" autocomplete="off" class="form-control"/>
                  <span class="input-group-btn">
                    <button class="btn btn-info agregar_cliente">+ Agregar</button>
                  </span>
                </div>
              </div>
              <div id="usuarios_tabla" class="mt10"></div>
            </div>
          </div>
        </div>

        <div class="panel panel-default <%= (MILLING == 1 || ID_EMPRESA == 1372)?"dn":"" %>">
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
                  <span id="curso_seo_title_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>70</span>
                </label>
                <input type="text" data-max="70" data-id="curso_seo_title_cantidad" name="seo_title" id="curso_seo_title" value="<%= seo_title %>" class="form-control text-remain"/>
              </div>
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <label class="control-label fr">
                  <span id="curso_seo_description_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>160</span>
                </label>
                <textarea data-max="160" data-id="curso_seo_description_cantidad" name="seo_description" id="curso_seo_description" class="form-control text-remain"><%= seo_description %></textarea>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="curso_seo_ocultar_sitemap" name="seo_ocultar_sitemap" class="checkbox" value="1" <%= (seo_ocultar_sitemap == 1)?"checked":"" %> >
                    <i></i>
                    <?php echo lang(array(
                      "es"=>"No agregar el curso al sitemap.xml.",
                      "en"=>"No add this post to sitemap.xml.",
                    )); ?>
                  </label>
                </div>
              </div>              
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array("es"=>"Prioridad","en"=>"Priority")) ?>
                    </label>
                    <input type="text" name="seo_sitemap_priority" id="curso_seo_sitemap_priority" value="<%= seo_sitemap_priority %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array("es"=>"Frecuencia","en"=>"Change Frecuency")) ?>
                    </label>
                    <select name="seo_sitemap_change_freq" id="curso_seo_sitemap_change_freq" class="form-control">
                      <option value="" <%= (seo_sitemap_change_freq == "")?"selected":"" %>>-</option>
                      <option value="always" <%= (seo_sitemap_change_freq == "always")?"selected":"" %>>Always</option>
                      <option value="hourly" <%= (seo_sitemap_change_freq == "hourly")?"selected":"" %>>Hourly</option>
                      <option value="daily" <%= (seo_sitemap_change_freq == "daily")?"selected":"" %>>Daily</option>
                      <option value="weekly" <%= (seo_sitemap_change_freq == "weekly")?"selected":"" %>>Weekly</option>
                      <option value="monthly" <%= (seo_sitemap_change_freq == "monthly")?"selected":"" %>>Monthly</option>
                      <option value="yearly" <%= (seo_sitemap_change_freq == "yearly")?"selected":"" %>>Yearly</option>
                      <option value="never" <%= (seo_sitemap_change_freq == "never")?"selected":"" %>>Never</option>
                    </select>
                  </div>
                </div>
              </div>
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


<script type="text/template" id="cursos_clases_resultados_template">
<table id="clases_tabla" class="table table-small table-striped sortable m-b-none default footable">
  <thead>
    <tr>
      <th>Nombre</th>
      <th class="th_acciones w50"></th>
    </tr>
  </thead>
  <tbody class="tbody"></tbody>
</table>
</script>

<script type="text/template" id="cursos_clases_item_resultados_template">
<td class="text-info data"><%= nombre %></td>
<td class="tar td_acciones">
  <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
</td>
</script>

<script type="text/template" id="curso_clase_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Editar contenido</b>
  </div>
  <div class="tab-container mb0">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a id="clase_1_link" href="#clase_tab1" role="tab" data-toggle="tab">
          <i class="fa text-success fa-check m-r-xs"></i>
          Datos generales
        </a>
      </li>
      <li>
        <a id="clase_2_link" href="#clase_tab2" role="tab" data-toggle="tab">
          <i class="fa text-warning fa-image m-r-xs"></i>
          Contenido
        </a>
      </li>
      <% if (ID_EMPRESA == 1372) { %>
        <li>
          <a id="clase_3_link" href="#clase_tab3" role="tab" data-toggle="tab">
            <i class="fa text-danger fa-list m-r-xs"></i>
            Evaluación
          </a>
        </li>
      <% } %>
    </ul>
    <div class="tab-content">
      <div id="clase_tab1" class="tab-pane active">

        <div class="row">
          <div class="col-md-9">
            <div class="form-group">
              <label class="control-label">Nombre</label>
              <input type="text" required name="nombre" id="curso_clase_nombre" value="<%= nombre %>" class="form-control"/>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Orden</label>
              <input type="text" name="orden" id="curso_clase_orden" value="<%= orden %>" class="form-control"/>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label">Descripcion</label>
          <textarea id="curso_clase_texto" style="height:80px;" class="form-control" name="texto"><%= texto %></textarea>
        </div>

      </div>

      <div id="clase_tab3" class="tab-pane">

        <div class="form-group">
          <label class="control-label">Cantidad de respuestas correctas para aprobar el módulo:</label>
          <input type="text" name="respuestas_correctas" id="curso_clase_respuestas_correctas" value="<%= respuestas_correctas %>" class="form-control"/>
        </div>

        <div class="line b-b m-b"></div>

        <div id="cursos_preguntas"></div>

      </div>

      <div id="clase_tab2" class="tab-pane">

        <div class="form-group">
          <label class="control-label">Video</label>
          <textarea id="curso_clase_video" style="height:80px;" placeholder="Pegue aqui el codigo del video que desea insertar" class="form-control" name="video"><%= video %></textarea>
        </div>

        <div class="<%= (ID_EMPRESA == 1129)?"dn":"" %>">
          <?php
          single_file_upload(array(
            "name"=>"path_clase",
            "label"=>"Archivo",
            "url"=>"/sistema/cursos/function/save_file/",
          )); ?>        
        </div>

        <div class="<%= (ID_EMPRESA == 1129)?"dn":"" %>">
          <?php
          single_file_upload(array(
            "name"=>"audio",
            "label"=>"Audio",
            "url"=>"/sistema/cursos/function/save_file/",
          )); ?>        
        </div>

        <div class="row">
          <?php for($i=1;$i<=10;$i++) { ?>

            <?php if (isset($empresa->config["curso_clase_custom_".$i."_file"])) { ?>
              
              <div class="col-xs-12">
                <?php single_file_upload(array(
                  "name"=>"custom_$i",
                  "label"=>$empresa->config["curso_clase_custom_".$i."_file"],
                  "url"=>"/sistema/cursos/function/save_file/",
                )); ?>
              </div>

            <?php } else if (isset($empresa->config["curso_clase_custom_".$i."_label"])) { ?>
              <div class="<?php echo (isset($empresa->config['curso_clase_custom_'.$i.'_class'])) ? $empresa->config['curso_clase_custom_'.$i.'_class'] :'col-xs-12'?>">
                <div class="form-group">
                  <label class="control-label"><?php echo $empresa->config["curso_clase_custom_".$i."_label"] ?></label>
                  <?php if(isset($empresa->config['curso_clase_custom_'.$i.'_values'])) { 
                    $values = explode("|",$empresa->config['curso_clase_custom_'.$i.'_values']); ?>
                    <select class="form-control" name="custom_<?php echo $i ?>">
                      <?php foreach($values as $value) { ?>
                        <option <%= (<?php echo "custom_".$i ?> == "<?php echo $value ?>")?"selected":""  %> value="<?php echo $value ?>"><?php echo $value ?></option>
                      <?php } ?>
                    </select>
                  <?php } else { ?>
                    <input type="text" name="custom_<?php echo $i ?>" id="curso_clase_custom_<?php echo $i ?>" value="<%= custom_<?php echo $i ?> %>" class="form-control"/>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
          <?php } ?>
        </div>
      </div>

    </div>
  </div>  
  <div class="panel-footer clearfix tar">
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>

<script type="text/template" id="cursos_preguntas_template">
  <div class="form-group">
    <div class="input-group">
      <input placeholder="Escriba una nueva pregunta..." type="text" id="curso_preguntas_texto" class="form-control"/>
      <span class="input-group-btn">
        <button tabindex="-1" type="button" class="btn agregar_pregunta btn-info">+ Agregar</button>
      </span>              
    </div>
  </div>
  <div class="curso_preguntas_table"></div>
</script>

<script type="text/template" id="cursos_preguntas_item_resultados_template">
  <textarea name="pregunta" class="form-control"><%= pregunta %></textarea>
  <div class="form-group">
    <div class="input-group">
      <input type="text" placeholder="Escriba una respuesta y enter..." id="curso_pregunta_respuesta" class="form-control"/>
      <span class="input-group-btn">
        <button tabindex="-1" type="button" class="btn btn-white eliminar_pregunta"><i class="fa fa-trash"></i></button>
      </span>              
    </div>
  </div>
  <div class="curso_pregunta_respuestas mb20"></div>
</script>

<script type="text/template" id="cursos_autores_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("cursos") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %> / <b>Autores</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <?php 
      $active = "cursos_autores";
      include("cursos_menu.php") ?>

      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("cursos") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#curso_autor"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cursos_autores_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
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


<script type="text/template" id="cursos_autores_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>   
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>"><?php echo lang(array("es"=>"Duplicar","en"=>"Duplicate")) ?></a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Delete","en"=>"Delete")) ?></a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="cursos_autores_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("cursos") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %> / Autores
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<form onsubmit="return false" class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" autocomplete="off" class="form-control" id="cursos_autores_nombre" value="<%= nombre %>"/>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Telefono","en"=>"Telephone")) ?></label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" autocomplete="off" class="form-control" id="cursos_autores_telefono" value="<%= telefono %>"/>
                  </div>                  
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="email" autocomplete="off" class="form-control" id="cursos_autores_email" value="<%= email %>"/>
                  </div>                  
                </div>
              </div>
              <div class="form-group">
                <label class="control-label"><?php echo lang(array("es"=>"Descripcion","en"=>"Description")) ?></label>
                <textarea <%= (!edicion)?"disabled":"" %> type="text" name="texto" autocomplete="off" class="form-control" id="cursos_autores_texto"><%= texto %></textarea>
              </div>

              <div class="form-group">
                <?php
                single_upload(array(
                  "name"=>"path",
                  "label"=>lang(array("es"=>"Foto","en"=>"Photo")),
                  "url"=>"/sistema/cursos_autores/function/save_image/",
                  "width"=>(isset($empresa->config["curso_autor_image_width"]) ? $empresa->config["curso_autor_image_width"] : 256),
                  "height"=>(isset($empresa->config["curso_autor_image_height"]) ? $empresa->config["curso_autor_image_height"] : 256),
                  "quality"=>(isset($empresa->config["curso_autor_image_quality"]) ? $empresa->config["curso_autor_image_quality"] : 0.98),
                  "crop_type"=>(isset($empresa->config["curso_autor_image_crop_type"]) ? $empresa->config["curso_autor_image_crop_type"] : 1),
                  "resizable"=>(isset($empresa->config["curso_autor_image_resizable"]) ? $empresa->config["curso_autor_image_resizable"] : 0),
                  "thumbnail_width"=>(isset($empresa->config["curso_autor_thumbnail_width"]) ? $empresa->config["curso_autor_thumbnail_width"] : 0),
                  "thumbnail_height"=>(isset($empresa->config["curso_autor_thumbnail_height"]) ? $empresa->config["curso_autor_thumbnail_height"] : 0),
                )); ?>
              </div>

            </div>
          </div>
        </div>
        <% if (edicion) { %>
          <button class="btn guardar btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")) ?></button>
        <% } %>
      </div>
    </div>
  </div>
</form>

</script>


<script type="text/template" id="cursos_categorias_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("cursos") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %> / <b>Categorias</b></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <?php 
      $active = "cursos_categorias";
      include("cursos_menu.php") ?>

      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("cursos") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#curso_categoria"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cursos_categorias_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
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


<script type="text/template" id="cursos_categorias_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <i title="Destacado" class="fa-star iconito fa destacado <%= (destacado == 1)?"active":"" %>"></i>
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

<script type="text/template" id="cursos_categorias_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("cursos") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %> / Categorias
    / <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" id="cursos_categorias_nombre" value="<%= nombre %>"/>
              </div>
              
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