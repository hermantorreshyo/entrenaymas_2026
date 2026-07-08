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
                <div class="input-group">
                  <input type="text" id="entrada_titulo" class="form-control active" value="<%= titulo %>" name="titulo"/>
                  <input type="text" id="entrada_titulo_en" name="titulo_en" class="form-control" id="entrada_titulo_en" value="<%= titulo_en %>"/>
                  <input type="text" id="entrada_titulo_pt" name="titulo_pt" class="form-control" id="entrada_titulo_pt" value="<%= titulo_pt %>"/>
                  <div class="input-group-btn">
                    <label class="btn btn-default btn-lang active" data-id="entrada_titulo" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="entrada_titulo_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="entrada_titulo_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
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
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="entrada_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="entrada_link_2" class="btn btn-default btn-lang" data-id="entrada_texto_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label id="entrada_link_3" class="btn btn-default btn-lang" data-id="entrada_texto_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="entrada_texto_cont">
                    <textarea name="texto" name="texto" id="entrada_texto"><%= texto %></textarea>
                  </div>
                  <div class="form-control-cont" id="entrada_texto_en_cont">
                    <textarea name="texto_en" name="texto_en" id="entrada_texto_en"><%= texto_en %></textarea>
                  </div>
                  <div class="form-control-cont" id="entrada_texto_pt_cont">
                    <textarea name="texto_pt" name="texto_pt" id="entrada_texto_pt"><%= texto_pt %></textarea>
                  </div>
                </div>
              </div>

              <?php
              single_upload(array(
                  "name"=>"path",
                  "label"=>lang(array("es"=>"Imagen Principal","en"=>"Featured Image")),
                  "url"=>"/sistema/entradas/function/save_image/",
                  "width"=>(isset($empresa->config["entrada_image_width"]) ? $empresa->config["entrada_image_width"] : 256),
                  "height"=>(isset($empresa->config["entrada_image_height"]) ? $empresa->config["entrada_image_height"] : 256),
                  "quality"=>(isset($empresa->config["entrada_image_quality"]) ? $empresa->config["entrada_image_quality"] : 0.92),
                  "thumbnail_width"=>(isset($empresa->config["entrada_thumbnail_width"]) ? $empresa->config["entrada_thumbnail_width"] : 0),
                  "thumbnail_height"=>(isset($empresa->config["entrada_thumbnail_height"]) ? $empresa->config["entrada_thumbnail_height"] : 0),
              )); ?>

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
          <div class="panel-body expand" style="<%= (ID_EMPRESA == 225) ? 'display:block':'' %>">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <label class="i-checks m-b-none">
                        <input id="entrada_mostrar_fecha" value="1" <%= (mostrar_fecha==1)?"checked":"" %> type="checkbox"><i></i>
                        <?php echo lang(array(
                          "es"=>"Mostrar fecha de publicaci&oacute;n",
                          "en"=>"Show publication date",
                        )); ?>
                      </label>
                    </label>
                    <div class="input-group">
                      <input type="text" name="fecha" id="entrada_fecha" value="<%= fecha %>" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array("es"=>"Fuente","en"=>"Source")) ?>
                    </label>
                    <input type="text" name="fuente" id="entrada_fuente" value="<%= fuente %>" class="form-control"/>
                  </div>
                </div>
              </div>

              <% if (id != undefined) { %>
                <div class="form-group">
                  <label class="control-label">Link</label>
                  <input type="text" class="form-control" name="link" value="<%= link %>" id="entrada_link">
                </div>
              <% } %>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">
                    <?php echo lang(array(
                      "es"=>"Texto para listado",
                      "en"=>"Text for list",
                    )); ?>
                  </label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="entrada_descripcion_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="entrada_descripcion_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="entrada_descripcion_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="entrada_descripcion_cont">
                    <textarea id="entrada_descripcion" class="form-control db h100" name="descripcion"><%= descripcion %></textarea>
                  </div>
                  <div class="form-control-cont" id="entrada_descripcion_en_cont">
                    <textarea id="entrada_descripcion_en" class="form-control db h100" name="descripcion_en"><%= descripcion_en %></textarea>
                  </div>
                  <div class="form-control-cont" id="entrada_descripcion_pt_cont">
                    <textarea id="entrada_descripcion_pt" class="form-control db h100" name="descripcion_pt"><%= descripcion_pt %></textarea>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Categor&iacute;a",
                    "en"=>"Category",
                  )); ?>
                </label>
                <div class="input-group">
                  <select id="entrada_categorias" class="form-control"></select>
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
              multiple_upload(array(
                "name"=>"images",
                "label"=>lang(array("en"=>"Image Gallery","es"=>"Galería de fotos")),
                "url"=>"entradas/function/save_image/",
                "width"=>(isset($empresa->config["entrada_galeria_image_width"]) ? $empresa->config["entrada_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["entrada_galeria_image_height"]) ? $empresa->config["entrada_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["entrada_galeria_image_quality"]) ? $empresa->config["entrada_galeria_image_quality"] : 0.8),
              )); ?>
              <div class="form-group">
                <label class="control-label">Video</label>
                <textarea id="entrada_video" style="height:80px;" placeholder="<?php echo lang(array('es'=>'Inserte aquí el código de insercción de su video','en'=>'Paste here your insertion code'));?>" class="form-control" name="video"><%= video %></textarea>
              </div>

              <?php
              single_file_upload(array(
                "name"=>"archivo",
                "label"=>lang(array("es"=>"Archivo adjunto","en"=>"Atacchment file")),
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
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Link externo",
                    "en"=>"External link",
                  )); ?>
                </label>
                <input type="text" name="link_externo" id="entrada_link_externo" value="<%= link_externo %>" class="form-control"/>
              </div>                

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Capacitaci&oacute;n</label>
                <a id="expand_capacitaciones" class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Informacion relacionada a la capacitacion.",
                    "en"=>"Create a image gallery, add a single video or atachmentt files...",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Tipo de Certificado</label>
                    <input type="text" class="form-control" name="custom_1" value="<%= custom_1 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Horas Catedra</label>
                    <input type="text" class="form-control" name="custom_2" value="<%= custom_2 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Duracion</label>
                    <input type="text" class="form-control" name="custom_3" value="<%= custom_3 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Modalidad Cursada</label>
                    <input type="text" class="form-control" name="custom_4" value="<%= custom_4 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Horario Inicio</label>
                    <input type="text" class="form-control" name="custom_5" value="<%= custom_5 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Horario Fin</label>
                    <input type="text" class="form-control" name="custom_17" value="<%= custom_17 %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Fecha fin</label>
                    <input type="text" class="form-control" name="custom_6" value="<%= custom_6 %>"/>
                  </div>
                </div>
              </div>
              <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a id="link_custom_8" href="#tab_custom8" role="tab" data-toggle="tab">Programa</a></li>
                <li><a id="link_custom_9" href="#tab_custom9" role="tab" data-toggle="tab">Organiza</a></li>
                <li><a id="link_custom_10" href="#tab_custom10" role="tab" data-toggle="tab">Costos</a></li>
                <li><a id="link_custom_11" href="#tab_custom11" role="tab" data-toggle="tab">Certificado</a></li>
                <li><a id="link_custom_12" href="#tab_custom12" role="tab" data-toggle="tab">Dirigido</a></li>
                <li><a id="link_custom_13" href="#tab_custom13" role="tab" data-toggle="tab">Docente</a></li>
                <li><a id="link_custom_14" href="#tab_custom14" role="tab" data-toggle="tab">Inscripcion</a></li>
                <li><a id="link_custom_16" href="#tab_custom16" role="tab" data-toggle="tab">Email</a></li>
              </ul>
              <div class="tab-content">
                <div id="tab_custom8" class="tab-pane panel-body active">
                  <textarea name="custom_8" id="entrada_custom_8"><%= custom_8 %></textarea>
                </div>
                <div id="tab_custom9" class="tab-pane panel-body">
                  <textarea name="custom_9" id="entrada_custom_9"><%= custom_9 %></textarea>
                </div>
                <div id="tab_custom10" class="tab-pane panel-body">
                  <textarea name="custom_10" id="entrada_custom_10"><%= custom_10 %></textarea>
                </div>
                <div id="tab_custom11" class="tab-pane panel-body">
                  <textarea name="custom_11" id="entrada_custom_11"><%= custom_11 %></textarea>
                </div>
                <div id="tab_custom12" class="tab-pane panel-body">
                  <textarea name="custom_12" id="entrada_custom_12"><%= custom_12 %></textarea>
                </div>
                <div id="tab_custom13" class="tab-pane panel-body">
                  <textarea name="custom_13" id="entrada_custom_13"><%= custom_13 %></textarea>
                </div>
                <div id="tab_custom14" class="tab-pane panel-body">
                  <textarea name="custom_14" id="entrada_custom_14"><%= custom_14 %></textarea>
                </div>
                <div id="tab_custom16" class="tab-pane panel-body">
                  <textarea name="custom_16" id="entrada_custom_16"><%= custom_16 %></textarea>
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
                    "es"=>"Agregar mapa con ubicaciones a la entrada.",
                    "en"=>" Add marker in Google Maps, city  and country.",
                  )); ?>                  
                </div>
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
                        "es"=>"Localidad",
                        "en"=>"City",
                      )); ?>
                    </label>
                    <input type="text" name="localidad" id="entrada_localidad" value="<%= localidad %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array(
                        "es"=>"Pa&iacute;s",
                        "en"=>"Country",
                      )); ?>
                    </label>
                    <div class="input-group">
                      <select name="id_pais" id="entrada_pais" class="w100p"></select>
                      <span class="input-group-btn">
                        <button class="btn btn-info add_marker">
                          <?php echo lang(array(
                            "es"=>"+ Marcador",
                            "en"=>"+ Add Marker",
                          )); ?>
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Direcci&oacute;n</label>
                    <input type="text" name="direccion" id="entrada_direccion" value="<%= direccion %>" class="form-control"/>
                  </div>
                </div>
              </div>
              <div style="height:400px;" id="mapa"></div>
              <div class="help-block">
              <?php echo lang(array(
                "es"=>"Puede arrastrar el marcador del mapa para ponerlo en la direccion exacta. Doble click para eliminarlo.",
                "en"=>"First add a marker! Then simply dragging the marker to the right position. Double click for delete the marker.",
              )); ?>
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
        <button title="<?php echo lang(array("es"=>"Abre la entrada desde la web","en"=>"View the post preview on the web")) ?>" class="btn m-r-xs previsualizar btn-default">
          <?php echo lang(array("es"=>"Previsualizar","en"=>"&nbsp;&nbsp;Preview&nbsp;&nbsp;")); ?>
        </button>
        <button title="<?php echo lang(array("es"=>"Guarda los cambios pero no se aplican a la web","en"=>"Save the changes without apply on the web")) ?>" class="btn m-r-xs guardar_borrador btn-default">
        <?php echo lang(array("es"=>"Guardar en borrador","en"=>"Save as draft")); ?>
        </button>
        <?php /*
        <% if (typeof eliminada != undefined && eliminada == 1) { %>
          <button title="Restaura una entrada eliminada" class="btn m-r-xs restaurar btn-success">&nbsp;&nbsp;&nbsp;Restaurar&nbsp;&nbsp;&nbsp;</button>
        <% } else { %>
          */ ?>
          <button title="<?php echo lang(array("es"=>"Guarda los cambios y los publica en la web","en"=>"The changes will be saved and applied on the web")) ?>" class="btn m-r-xs guardar btn-success">
            <?php echo lang(array("es"=>"&nbsp;&nbsp;Publicar&nbsp;&nbsp;","en"=>"&nbsp;&nbsp;Publish&nbsp;&nbsp;")); ?>
          </button>
        <?php /*<% } %>*/ ?>
      </div>
    </div>

  </div>
</div>
