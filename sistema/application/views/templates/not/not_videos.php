<script type="text/template" id="not_videos_resultados_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">
    <?php echo lang(array("es"=>"Todav&iacute;a no ten&eacute;s ning&uacute;n video","en"=>"You haven't upload any video")); ?>
  </h1>
  <h3 class="h3">
    <?php echo lang(array("es"=>"Para crear tu primera video, hace click en el siguiente bot&oacute;n","en"=>'Click in "New Video" button to create the first')); ?>
  </h3>
  <div class="list-icon">
    <a href="app/#not_video"><i class="icon-note"></i></a>
  </div>
  <div>
    <a class="btn btn-lg btn-info btn-addon" href="app/#not_video">
      <i class="fa fa-plus"></i><span>
      <?php echo lang(array("es"=>"  Nuevo  ","en"=>"New Video")); ?>
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
      <% var modulo1 = control.get("not_videos") %>
      / <b><%= modulo1.title %></b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="not_videos_buscar" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." value="<%= window.not_videos_filter %>" autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </span>
            </div>
          </div>
          <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#not_video">
                <i class="fa fa-plus"></i>
                <span class="hidden-xs">
                  <?php echo lang(array("es"=>"&nbsp;&nbsp;Nueva&nbsp;&nbsp;","en"=>"&nbsp;&nbsp;New&nbsp;&nbsp;")); ?>
                </span>
              </a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="display:none">
        <div class="wrapper oh">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"B&uacute;squeda Avanzada:","en"=>"Advanced Search:")); ?></h4>
          <div class="form-inline">
            <div style="width: 250px; display: inline-block">
              <select id="not_videos_buscar_categorias" class="w100p"></select>
            </div>
            <div class="form-group">
              <button id="not_videos_buscar_avanzada_btn" class="btn btn-default"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
            </div>
          </div>
        </div>
      </div>
      <% if (!seleccionar) { %>
        <div class="bulk_action wrapper pb0">
          <button class="btn btn-default eliminar_lote btn-addon"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></button>
        </div>
      <% } %>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="not_videos_tabla" class="table table-striped sortable m-b-none default footable">
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
                <th class="sorting" data-sort-by="categoria"><?php echo lang(array("es"=>"Categor&iacute;a","en"=>"Category")); ?></th>
                <th class="sorting" data-sort-by="A.fecha"><?php echo lang(array("es"=>"Fecha","en"=>"Date")); ?></th>
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

<script type="text/template" id="not_videos_item_resultados_template">
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
  <td class="<%= clase %> data"><%= categoria %></td>
  <td class="<%= clase %> data"><%= fecha %></td>
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


<script type="text/template" id="not_video_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("menu_not_eventos") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    <% var modulo1 = control.get("not_videos") %>
    / <%= modulo1.title %>
    / <b><?php echo lang(array("es"=>"<%= (id == undefined)?'Nueva':titulo %>","en"=>"<%= (id == undefined)?'New':titulo %>")); ?></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-10 col-md-offset-1">
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
                  <input type="text" id="not_video_titulo" class="form-control active" value="<%= titulo %>" name="titulo"/>
                  <input type="text" id="not_video_titulo_en" name="titulo_en" class="form-control" id="not_video_titulo_en" value="<%= titulo_en %>"/>
                  <input type="text" id="not_video_titulo_pt" name="titulo_pt" class="form-control" id="not_video_titulo_pt" value="<%= titulo_pt %>"/>
                  <div class="input-group-btn" style="<?php echo ($entrada_single_lang == 1)?"display: none":""; ?>">
                    <label class="btn btn-default btn-lang active" data-id="not_video_titulo" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="not_video_titulo_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="not_video_titulo_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
              </div>

              <div class="form-group lang-control">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Subt&iacute;tulo",
                    "en"=>"Subtitle",
                  )); ?>
                </label>
                <div class="input-group" style="<?php echo ($entrada_single_lang == 1)?"width:100%":""; ?>">
                  <input type="text" id="not_video_subtitulo" class="form-control active" value="<%= subtitulo %>" name="subtitulo"/>
                  <input type="text" id="not_video_subtitulo_en" name="subtitulo_en" class="form-control" id="not_video_subtitulo_en" value="<%= subtitulo_en %>"/>
                  <input type="text" id="not_video_subtitulo_pt" name="subtitulo_pt" class="form-control" id="not_video_subtitulo_pt" value="<%= subtitulo_pt %>"/>
                  <div class="input-group-btn" style="<?php echo ($entrada_single_lang == 1)?"display: none":""; ?>">
                    <label class="btn btn-default btn-lang active" data-id="not_video_subtitulo" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="not_video_subtitulo_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="not_video_subtitulo_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Categor&iacute;a",
                    "en"=>"Category",
                  )); ?>
                </label>
                <div class="input-group">
                  <select id="not_video_categorias" class="form-control"></select>
                  <span class="input-group-btn">
                    <button tabindex="-1" class="btn btn-info w100 agregar_categoria">
                      <?php echo lang(array(
                        "es"=>"+ Categor&iacute;a",
                        "en"=>"+ Add New",
                      )); ?>
                    </button>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Cliente",
                    "en"=>"Company",
                  )); ?>
                </label>
                <select id="not_video_clientes" class="form-control no-model w100p"></select>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Evento",
                    "en"=>"Event",
                  )); ?>
                </label>
                <select id="not_video_eventos" class="form-control"></select>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Link de Youtube",
                    "en"=>"Youtube link",
                  )); ?>
                </label>
                <input type="text" name="link_youtube" id="not_video_link_youtube" value="<%= link_youtube %>" class="form-control"/>
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
                    <label class="btn btn-default btn-lang active" data-id="not_video_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="not_video_link_2" class="btn btn-default btn-lang" data-id="not_video_texto_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label id="not_video_link_3" class="btn btn-default btn-lang" data-id="not_video_texto_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="not_video_texto_cont">
                    <textarea name="texto" name="texto" id="not_video_texto"><%= texto %></textarea>
                  </div>
                  <div class="form-control-cont" id="not_video_texto_en_cont">
                    <textarea name="texto_en" name="texto_en" id="not_video_texto_en"><%= texto_en %></textarea>
                  </div>
                  <div class="form-control-cont" id="not_video_texto_pt_cont">
                    <textarea name="texto_pt" name="texto_pt" id="not_video_texto_pt"><%= texto_pt %></textarea>
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

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <label class="i-checks m-b-none">
                        <input id="not_video_mostrar_fecha" value="1" <%= (mostrar_fecha==1)?"checked":"" %> type="checkbox"><i></i>
                        <?php echo lang(array(
                          "es"=>"Mostrar fecha de publicaci&oacute;n",
                          "en"=>"Show publication date",
                        )); ?>
                      </label>
                    </label>
                    <div class="input-group">
                      <input type="text" name="fecha" id="not_video_fecha" value="<%= fecha %>" class="form-control"/>
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
                  <input type="text" class="form-control" name="link" value="<%= link %>" id="not_video_link">
                </div>
              <% } %>

            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="row">
      <div class="col-md-10 col-md-offset-1 tar">
        <div class="line b-b m-b-lg"></div>
        <button title="<?php echo lang(array("es"=>"Abre la not_video desde la web","en"=>"View the not_video preview on the web")) ?>" class="btn m-r-xs previsualizar btn-default">
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
