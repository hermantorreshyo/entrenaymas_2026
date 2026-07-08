<script type="text/template" id="fot_trabajos_resultados_template">
<div>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("menu_fot_trabajos") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
      <% var modulo1 = control.get("fot_trabajos") %>
      / <b><%= modulo1.title %></b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="fot_trabajos_buscar" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." value="<%= window.fot_trabajos_filter %>" autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
          <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#fot_trabajo">
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
          <table id="fot_trabajos_tabla" class="table table-striped sortable m-b-none default footable">
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

<script type="text/template" id="fot_trabajos_item_resultados_template">
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


<script type="text/template" id="fot_trabajo_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("menu_fot_trabajos") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    <% var modulo1 = control.get("fot_trabajos") %>
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
                <?php $fot_trabajo_single_lang = (isset($empresa->config["fot_trabajo_single_language"]) ? $empresa->config["fot_trabajo_single_language"] : 0); ?>
                <div class="input-group" style="<?php echo ($fot_trabajo_single_lang == 1)?"width:100%":""; ?>">
                  <input type="text" id="fot_trabajo_titulo" class="form-control active" value="<%= titulo %>" name="titulo"/>
                  <input type="text" id="fot_trabajo_titulo_en" name="titulo_en" class="form-control" id="fot_trabajo_titulo_en" value="<%= titulo_en %>"/>
                  <input type="text" id="fot_trabajo_titulo_pt" name="titulo_pt" class="form-control" id="fot_trabajo_titulo_pt" value="<%= titulo_pt %>"/>
                  <div class="input-group-btn" style="<?php echo ($fot_trabajo_single_lang == 1)?"display: none":""; ?>">
                    <label class="btn btn-default btn-lang active" data-id="fot_trabajo_titulo" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="fot_trabajo_titulo_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="fot_trabajo_titulo_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
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
                <% if (control.check("categorias_entradas")>1) { %>
                  <div class="input-group">
                    <select id="fot_trabajo_categorias" class="form-control"></select>
                    <span class="input-group-btn">
                      <button tabindex="-1" class="btn btn-info w100 agregar_categoria">
                        <?php echo lang(array(
                          "es"=>"+ Categor&iacute;a",
                          "en"=>"+ Add New",
                        )); ?>
                      </button>
                    </span>
                  </div>
                <% } else { %>
                  <select id="fot_trabajo_categorias" class="form-control"></select>
                <% } %>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Cliente",
                    "en"=>"Customer",
                  )); ?>
                </label>
                <select class="select" id="fot_trabajo_clientes" name="id_cliente"></select>
              </div>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">
                    <?php echo lang(array(
                      "es"=>"Texto",
                      "en"=>"Text",
                    )); ?>
                  </label>
                  <div class="lang-control-btn" style="<?php echo ($fot_trabajo_single_lang == 1)?"display: none":""; ?>">
                    <label class="btn btn-default btn-lang active" data-id="fot_trabajo_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="fot_trabajo_link_2" class="btn btn-default btn-lang" data-id="fot_trabajo_texto_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label id="fot_trabajo_link_3" class="btn btn-default btn-lang" data-id="fot_trabajo_texto_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="fot_trabajo_texto_cont">
                    <textarea name="texto" name="texto" id="fot_trabajo_texto"><%= texto %></textarea>
                  </div>
                  <div class="form-control-cont" id="fot_trabajo_texto_en_cont">
                    <textarea name="texto_en" name="texto_en" id="fot_trabajo_texto_en"><%= texto_en %></textarea>
                  </div>
                  <div class="form-control-cont" id="fot_trabajo_texto_pt_cont">
                    <textarea name="texto_pt" name="texto_pt" id="fot_trabajo_texto_pt"><%= texto_pt %></textarea>
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
                      <?php echo lang(array(
                        "es"=>"Fecha",
                        "en"=>"Date",
                      )); ?>
                    </label>
                    <div class="input-group">
                      <input type="text" name="fecha" id="fot_trabajo_fecha" value="<%= fecha %>" class="form-control"/>
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
                  <input type="text" class="form-control" name="link" value="<%= link %>" id="fot_trabajo_link">
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
                "url"=>"/sistema/fot_trabajos/function/save_image/",
                "width"=>(isset($empresa->config["fot_trabajo_image_width"]) ? $empresa->config["fot_trabajo_image_width"] : 256),
                "height"=>(isset($empresa->config["fot_trabajo_image_height"]) ? $empresa->config["fot_trabajo_image_height"] : 256),
                "quality"=>(isset($empresa->config["fot_trabajo_image_quality"]) ? $empresa->config["fot_trabajo_image_quality"] : 0.92),
                "crop_type"=>(isset($empresa->config["fot_trabajo_image_crop_type"]) ? $empresa->config["fot_trabajo_image_crop_type"] : 1),
                "resizable"=>(isset($empresa->config["fot_trabajo_image_resizable"]) ? $empresa->config["fot_trabajo_image_resizable"] : 0),
                "thumbnail_width"=>(isset($empresa->config["fot_trabajo_thumbnail_width"]) ? $empresa->config["fot_trabajo_thumbnail_width"] : 0),
                "thumbnail_height"=>(isset($empresa->config["fot_trabajo_thumbnail_height"]) ? $empresa->config["fot_trabajo_thumbnail_height"] : 0),
              )); ?>

              <?php
              multiple_upload(array(
                "name"=>"images",
                "label"=>lang(array("en"=>"Image Gallery","es"=>"Galería de fotos")),
                "url"=>"fot_trabajos/function/save_image/",
                "width"=>(isset($empresa->config["fot_trabajo_galeria_image_width"]) ? $empresa->config["fot_trabajo_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["fot_trabajo_galeria_image_height"]) ? $empresa->config["fot_trabajo_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["fot_trabajo_galeria_image_quality"]) ? $empresa->config["fot_trabajo_galeria_image_quality"] : 0.8),
              )); ?>

            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="line b-b m-b-lg"></div>

    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8 tar">
        <button class="btn m-r-xs guardar btn-success">Guardar</button>
      </div>
    </div>

  </div>
</div>
</script>
