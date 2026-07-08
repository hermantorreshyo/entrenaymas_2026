<script type="text/template" id="not_eventos_resultados_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">
    <?php echo lang(array("es"=>"Todav&iacute;a no ten&eacute;s ning&uacute;n evento","en"=>"You haven't upload any evento")); ?>
  </h1>
  <h3 class="h3">
    <?php echo lang(array("es"=>"Para crear tu primer evento, hace click en el siguiente bot&oacute;n","en"=>'Click in "New Event" button to create the first')); ?>
  </h3>
  <div class="list-icon">
    <a href="app/#not_evento"><i class="icon-note"></i></a>
  </div>
  <div>
    <a class="btn btn-lg btn-info btn-addon" href="app/#not_evento">
      <i class="fa fa-plus"></i><span>
      <?php echo lang(array("es"=>"  Nuevo  ","en"=>"New Event")); ?>
      </span>
    </a>
  </div>
  <p>
    <?php echo lang(array("es"=>"Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click ","en"=>"If you need some help, please communicate with us ")); ?>
    <a class="text-info">
      <?php echo lang(array("es"=>"acá!","en"=>"here!")); ?>
    </a>
  </p>
</div>
<div class="seccion_llena" style="display:none">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("menu_not_eventos") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
      <% var modulo1 = control.get("not_eventos") %>
      / <b><%= modulo1.title %></b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="not_eventos_buscar" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." value="<%= window.not_eventos_filter %>" autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
          <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#not_evento">
                <i class="fa fa-plus"></i>
                <span class="hidden-xs">
                  <?php echo lang(array("es"=>"&nbsp;&nbsp;Nueva&nbsp;&nbsp;","en"=>"&nbsp;&nbsp;New&nbsp;&nbsp;")); ?>
                </span>
              </a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="not_eventos_tabla" class="table table-striped sortable m-b-none default footable">
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
                <th class="sorting" data-sort-by="titulo"><?php echo lang(array("es"=>"T&iacute;tulo","en"=>"Title")); ?></th>
                <th><?php echo lang(array("es"=>"Categoria","en"=>"Category")); ?></th>
                <th><?php echo lang(array("es"=>"Lugar","en"=>"Location")); ?></th>
                <th class="sorting" data-sort-by="A.fecha_desde"><?php echo lang(array("es"=>"Fecha","en"=>"Date")); ?></th>
                <% if (!seleccionar) { %>
                  <th class="th_acciones w150"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?></th>
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
</script>

<script type="text/template" id="not_eventos_item_resultados_template">
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
  <% } %>
  <td class="<%= clase %> data">
    <span class="text-info"><%= titulo %></span>
  </td>
  <td class="<%= clase %> data"><%= (categoria==0)?"Event":(categoria==1?"Our Conference":"Training") %></td>
  <td class="<%= clase %> data"><%= lugar %></td>
  <td class="<%= clase %> data"><%= fecha_desde %></td>
  <% if (!seleccionar) { %>
    <td class="tar <%= clase %>">
      <a target="_blank" href="http://<%= String(DOMINIO+'/'+link+'?preview=1').replace('//','/') %>"><i title="Ir a pagina" class="fa-external-link iconito fa"></i></a>
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <i title="Destacado" class="fa fa-star iconito destacado <%= (destacado == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>"><?php echo lang(array("es"=>"Duplicar","en"=>"Duplicate")); ?></a></li>
          <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>


<script type="text/template" id="not_evento_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("menu_not_eventos") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    <% var modulo1 = control.get("not_eventos") %>
    / <%= modulo1.title %>
    / <b><?php echo lang(array("es"=>"<%= (id == undefined)?'Nueva':titulo %>","en"=>"<%= (id == undefined)?'New':titulo %>")); ?></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-4">
        <div class="detalle_texto">
          <?php 
          $clave = "Events / Detalle / Texto 1";
          echo lang(array(
            "es"=>(isset($not_eventos[$clave]["nombre_es"]) ? $not_eventos[$clave]["nombre_es"] : "" ),
            "en"=>(isset($not_eventos[$clave]["nombre_en"]) ? $not_eventos[$clave]["nombre_en"] : "" ),
          )); ?>
        </div>
        <div class="detalle_texto_info text-muted">
          <?php echo lang(array(
            "es"=>(isset($not_eventos[$clave]["texto_es"]) ? $not_eventos[$clave]["texto_es"] : "" ),
            "en"=>(isset($not_eventos[$clave]["texto_en"]) ? $not_eventos[$clave]["texto_en"] : "" ),
          )); ?>
        </div>
        <?php if (isset($not_eventos[$clave]["not_evento_es"]) && !empty($not_eventos[$clave]["not_evento_es"])) { ?>
          <a onclick="workspace.open_not_evento(this)" data-iframe='<?php echo $not_eventos[$clave]["not_evento_es"] ?>'>
            Ver not_evento
          </a>
        <?php } ?>
      </div>

      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group lang-control">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"T&iacute;tulo",
                    "en"=>"Title",
                  )); ?>
                </label>
                <?php $entrada_single_lang = (isset($empresa->config["entrada_single_language"]) ? $empresa->config["entrada_single_language"] : 0); ?>
                <div class="input-group" style="<?php echo ($entrada_single_lang == 1)?"width:100%":""; ?>">
                  <input type="text" id="not_evento_titulo" class="form-control active" value="<%= titulo %>" name="titulo"/>
                  <input type="text" id="not_evento_titulo_en" name="titulo_en" class="form-control" id="not_evento_titulo_en" value="<%= titulo_en %>"/>
                  <input type="text" id="not_evento_titulo_pt" name="titulo_pt" class="form-control" id="not_evento_titulo_pt" value="<%= titulo_pt %>"/>
                  <div class="input-group-btn" style="<?php echo ($entrada_single_lang == 1)?"display: none":""; ?>">
                    <label class="btn btn-default btn-lang active" data-id="not_evento_titulo" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="not_evento_titulo_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="not_evento_titulo_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array(
                        "es"=>"Organizador",
                        "en"=>"Event organizer",
                      )); ?>
                    </label>
                    <select class="select" id="not_evento_organizadores" name="id_organizador"></select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array(
                        "es"=>"Categoria",
                        "en"=>"Category",
                      )); ?>
                    </label>
                    <select class="form-control" id="not_evento_categorias" name="categoria">
                      <option <%= (categoria==0)?"selected":"" %> value="0">Event</option>
                      <option <%= (categoria==1)?"selected":"" %> value="1">Conference</option>
                      <option <%= (categoria==2)?"selected":"" %> value="2">Training</option>
                    </select>
                  </div>                  
                </div>
              </div>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">
                    <?php echo lang(array(
                      "es"=>"Texto",
                      "en"=>"Text",
                    )); ?>
                  </label>
                  <div class="lang-control-btn" style="<?php echo ($entrada_single_lang == 1)?"display: none":""; ?>">
                    <label class="btn btn-default btn-lang active" data-id="not_evento_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="not_evento_link_2" class="btn btn-default btn-lang" data-id="not_evento_texto_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label id="not_evento_link_3" class="btn btn-default btn-lang" data-id="not_evento_texto_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="not_evento_texto_cont">
                    <textarea name="texto" name="texto" id="not_evento_texto"><%= texto %></textarea>
                  </div>
                  <div class="form-control-cont" id="not_evento_texto_en_cont">
                    <textarea name="texto_en" name="texto_en" id="not_evento_texto_en"><%= texto_en %></textarea>
                  </div>
                  <div class="form-control-cont" id="not_evento_texto_pt_cont">
                    <textarea name="texto_pt" name="texto_pt" id="not_evento_texto_pt"><%= texto_pt %></textarea>
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
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Lugar",
                    "en"=>"Location",
                  )); ?>
                </label>
                <input type="text" class="form-control" name="lugar" value="<%= lugar %>" id="not_evento_lugar">
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array(
                        "es"=>"Fecha desde",
                        "en"=>"Start date",
                      )); ?>
                    </label>
                    <div class="input-group">
                      <input type="text" name="fecha_desde" id="not_evento_fecha_desde" value="<%= fecha_desde %>" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array(
                        "es"=>"Fecha hasta",
                        "en"=>"End date",
                      )); ?>
                    </label>
                    <div class="input-group">
                      <input type="text" name="fecha_hasta" id="not_evento_fecha_hasta" value="<%= fecha_hasta %>" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
              </div>

              <% if (id != undefined) { %>
                <div class="form-group">
                  <label class="control-label">Link</label>
                  <input type="text" class="form-control" name="link" value="<%= link %>" id="not_evento_link">
                </div>
              <% } %>

              <% if (MILLING == 1) { %>
                <div class="form-group">
                  <label class="control-label">Type</label>
                  <select class="form-control" name="tipo" id="not_evento_tipo">
                    <option <%= (tipo=="Feed/Food")?"selected":"" %> value="">Feed/Food</option>
                    <option <%= (tipo=="Feed")?"selected":"" %> value="Feed">Feed</option>
                    <option <%= (tipo=="Food")?"selected":"" %> value="Food">Food</option>
                  </select>
                </div>
              <% } %>

              <div class="panel-description">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="entrada_con_acuerdo" name="con_acuerdo" class="checkbox" value="1" <%= (con_acuerdo == 1)?"checked":"" %> >
                    <i></i>
                    <?php echo lang(array(
                      "es"=>"Evento con acuerdo comercial",
                      "en"=>"Event with commercial agreement.",
                    )); ?>
                  </label>
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
                  <?php echo lang(array(
                    "es"=>"Informaci&oacute;n de contacto",
                    "en"=>"Contact information",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Name </label>
                <% if (edicion) { %>
                  <input type="text" name="contacto_nombre" class="form-control" id="not_evento_contacto_nombre" value="<%= contacto_nombre %>"/>
                <% } else { %>
                  <span><%= contacto_nombre %></span>
                <% } %>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Email </label>
                    <% if (edicion) { %>
                      <input type="text" name="contacto_email" class="form-control" id="not_evento_contacto_email" value="<%= contacto_email %>"/>
                    <% } else { %>
                      <span><%= contacto_email %></span>
                    <% } %>
                  </div>    
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Telephone </label>
                    <% if (edicion) { %>
                      <input type="text" name="contacto_telefono" class="form-control" id="not_evento_contacto_telefono" value="<%= contacto_telefono %>"/>
                    <% } else { %>
                      <span><%= contacto_telefono %></span>
                    <% } %>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"P&aacute;gina web",
                    "en"=>"Web page",
                  )); ?>
                </label>
                <input type="text" name="web" id="entrada_web" value="<%= web %>" class="form-control"/>
              </div>                

              <?php
              single_upload(array(
                "name"=>"path_2",
                "label"=>"Contact photo",
                "url"=>"/sistema/not_eventos/function/save_image/",
                "width"=>(isset($empresa->config["cliente_image_2_width"]) ? $empresa->config["cliente_image_2_width"] : 256),
                "height"=>(isset($empresa->config["cliente_image_2_height"]) ? $empresa->config["cliente_image_2_height"] : 256),
                "quality"=>(isset($empresa->config["cliente_image_2_quality"]) ? $empresa->config["cliente_image_2_quality"] : 0.92),
                "thumbnail_width"=>(isset($empresa->config["cliente_thumbnail_width"]) ? $empresa->config["cliente_thumbnail_width"] : 0),
                "thumbnail_height"=>(isset($empresa->config["cliente_thumbnail_height"]) ? $empresa->config["cliente_thumbnail_height"] : 0),
              )); ?>

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
              single_upload(array(
                "name"=>"path",
                "label"=>lang(array("es"=>"Imagen Principal","en"=>"Featured Image")),
                "url"=>"/sistema/not_eventos/function/save_image/",
                "width"=>(isset($empresa->config["not_evento_image_width"]) ? $empresa->config["not_evento_image_width"] : 256),
                "height"=>(isset($empresa->config["not_evento_image_height"]) ? $empresa->config["not_evento_image_height"] : 256),
                "quality"=>(isset($empresa->config["not_evento_image_quality"]) ? $empresa->config["not_evento_image_quality"] : 0.92),
                "crop_type"=>(isset($empresa->config["not_evento_image_crop_type"]) ? $empresa->config["not_evento_image_crop_type"] : 1),
                "resizable"=>(isset($empresa->config["not_evento_image_resizable"]) ? $empresa->config["not_evento_image_resizable"] : 0),
                "thumbnail_width"=>(isset($empresa->config["not_evento_thumbnail_width"]) ? $empresa->config["not_evento_thumbnail_width"] : 0),
                "thumbnail_height"=>(isset($empresa->config["not_evento_thumbnail_height"]) ? $empresa->config["not_evento_thumbnail_height"] : 0),
              )); ?>

              <?php
              multiple_upload(array(
                "name"=>"images",
                "label"=>lang(array("en"=>"Image Gallery","es"=>"Galería de fotos")),
                "url"=>"not_eventos/function/save_image/",
                "width"=>(isset($empresa->config["not_evento_galeria_image_width"]) ? $empresa->config["not_evento_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["not_evento_galeria_image_height"]) ? $empresa->config["not_evento_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["not_evento_galeria_image_quality"]) ? $empresa->config["not_evento_galeria_image_quality"] : 0.8),
              )); ?>

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
                  <span id="entrada_seo_title_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>70</span>
                </label>
                <input type="text" data-max="70" data-id="entrada_seo_title_cantidad" name="seo_title" id="entrada_seo_title" value="<%= seo_title %>" class="form-control text-remain"/>
              </div>
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <label class="control-label fr">
                  <span id="entrada_seo_description_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>160</span>
                </label>
                <textarea data-max="160" data-id="entrada_seo_description_cantidad" name="seo_description" id="entrada_seo_description" class="form-control text-remain"><%= seo_description %></textarea>
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
        <button title="<?php echo lang(array("es"=>"Abre la not_evento desde la web","en"=>"View the not_evento preview on the web")) ?>" class="btn m-r-xs previsualizar btn-default">
          <?php echo lang(array(
          "es"=>"Previsualizar",
          "en"=>"&nbsp;&nbsp;Preview&nbsp;&nbsp;",
        )); ?>
        </button>
        <button title="<?php echo lang(array("es"=>"Guarda los cambios y los publica en la web","en"=>"The changes will be saved and applied on the web")) ?>" class="btn m-r-xs guardar btn-success">
          <?php echo lang(array(
          "es"=>"&nbsp;&nbsp;Publicar&nbsp;&nbsp;",
          "en"=>"&nbsp;&nbsp;Publish&nbsp;&nbsp;",
        )); ?>
        </button>
      </div>
    </div>

  </div>
</div>
</script>
