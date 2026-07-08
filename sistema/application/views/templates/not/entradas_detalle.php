<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <% if (ID_EMPRESA == 105) { %>
                <div class="form-group">
                  <label class="control-label">Antetitulo</label>
                  <input type="text" id="entrada_antetitulo" class="form-control" value="<%= antetitulo %>" name="antetitulo"/>
                </div>
              <% } %>

              <% if (MILLING == 1) { %>
                <div class="form-group">
                  <label class="control-label">Title</label>
                  <input type="text" id="entrada_titulo" class="form-control" value="<%= titulo %>" name="titulo"/>
                </div>
              <% } else { %>
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
              <% } %>

              <% if (ID_EMPRESA == 70 || ID_EMPRESA == 105) { %>
                <div class="form-group lang-control">
                  <label class="control-label">
                    <%= (ID_EMPRESA == 70) ? "Titulo para Facebook" : "Subtitulo" %>
                  </label>
                  <div class="input-group">
                    <input type="text" id="entrada_subtitulo" class="form-control active" value="<%= subtitulo %>" name="subtitulo"/>
                    <input type="text" id="entrada_subtitulo_en" name="subtitulo_en" class="form-control" id="entrada_subtitulo_en" value="<%= subtitulo_en %>"/>
                    <input type="text" id="entrada_subtitulo_pt" name="subtitulo_pt" class="form-control" id="entrada_subtitulo_pt" value="<%= subtitulo_pt %>"/>
                    <div class="input-group-btn">
                      <label class="btn btn-default btn-lang active" data-id="entrada_subtitulo" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                      <label class="btn btn-default btn-lang" data-id="entrada_subtitulo_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                      <label class="btn btn-default btn-lang" data-id="entrada_subtitulo_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                    </div>
                  </div>
                </div>
              <% } %>

              <div class="form-group lang-control">
                <% if (MILLING == 0) { %>
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
                <% } %>
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
                  "url_file"=>"/sistema/entradas/function/save_file/",
                  "hide_crop"=>(($milling == 1) ? 1 : 0),
                  "width"=>(isset($empresa->config["entrada_image_width"]) ? $empresa->config["entrada_image_width"] : 256),
                  "height"=>(isset($empresa->config["entrada_image_height"]) ? $empresa->config["entrada_image_height"] : 256),
                  "quality"=>(isset($empresa->config["entrada_image_quality"]) ? $empresa->config["entrada_image_quality"] : 0.92),
                  "thumbnail_width"=>(isset($empresa->config["entrada_thumbnail_width"]) ? $empresa->config["entrada_thumbnail_width"] : 0),
                  "thumbnail_height"=>(isset($empresa->config["entrada_thumbnail_height"]) ? $empresa->config["entrada_thumbnail_height"] : 0),
              )); ?>

              <?php
              if (isset($empresa->config["entrada_image_2_label"])) {
                single_upload(array(
                  "name"=>"path_2",
                  "label"=>$empresa->config["entrada_image_2_label"],
                  "url"=>"/sistema/entradas/function/save_image/",
                  "url_file"=>"/sistema/entradas/function/save_file/",
                  "width"=>(isset($empresa->config["entrada_image_2_width"]) ? $empresa->config["entrada_image_2_width"] : 256),
                  "height"=>(isset($empresa->config["entrada_image_2_height"]) ? $empresa->config["entrada_image_2_height"] : 256),
                  "quality"=>(isset($empresa->config["entrada_image_2_quality"]) ? $empresa->config["entrada_image_2_quality"] : 0.92),
                )); 
              } ?>

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

              <% if (ID_EMPRESA != 70 && ID_EMPRESA != 105) { %>
                <div class="form-group lang-control">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>(($empresa->id == 490)?"Categoria":"Subt&iacute;tulo"),
                      "en"=>"Subtitle",
                    )); ?>
                  </label>
                  <div class="input-group">
                    <input type="text" id="entrada_subtitulo" class="form-control active" value="<%= subtitulo %>" name="subtitulo"/>
                    <input type="text" id="entrada_subtitulo_en" name="subtitulo_en" class="form-control" id="entrada_subtitulo_en" value="<%= subtitulo_en %>"/>
                    <input type="text" id="entrada_subtitulo_pt" name="subtitulo_pt" class="form-control" id="entrada_subtitulo_pt" value="<%= subtitulo_pt %>"/>
                    <div class="input-group-btn">
                      <label class="btn btn-default btn-lang active" data-id="entrada_subtitulo" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                      <label class="btn btn-default btn-lang" data-id="entrada_subtitulo_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                      <label class="btn btn-default btn-lang" data-id="entrada_subtitulo_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                    </div>
                  </div>
                </div>
              <% } %>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>(($empresa->id == 490)?"Destinations":"Etiquetas"),
                    "en"=>"Tags",
                  )); ?>
                </label>
                <select multiple id="entrada_etiquetas" style="width: 100%">
                  <% for (var i=0; i< etiquetas.length; i++) { %>
                    <% var o = etiquetas[i] %>
                    <option selected><%= o %></option>
                  <% } %>
                </select>
              </div>

              <div class="row">
                <div class="col-md-4 <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
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
                <div class="col-md-4 <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array("es"=>"Fuente","en"=>"Source")) ?>
                    </label>
                    <input type="text" name="fuente" id="entrada_fuente" value="<%= fuente %>" class="form-control"/>
                  </div>
                </div>

                <% if (control.check("usuarios")>=3) { %>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">
                        <?php echo lang(array("es"=>"Usuario","en"=>"User")) ?>
                      </label>                
                      <select id="entrada_usuarios" class="form-control">
                        <% for(var i=0;i< window.usuarios.models.length;i++) { %>
                          <% var o = window.usuarios.models[i]; %>
                          <option value="<%= o.id %>" <%= (o.id == id_usuario)?"selected":"" %>><%= o.get("nombre") %></option>
                        <% } %>
                      </select>
                    </div>
                  </div>
                <% } %>

              </div>

              <% if (ID_EMPRESA == 225) { %>
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array("es"=>"Destacar en Portada",""=>"Highlight on Home")) ?>
                  </label>
                  <select name="nivel_importancia" id="entrada_nivel_importancia" class="form-control">
                    <option <%= (nivel_importancia == 0)?"selected":"" %> value="0">
                    <?php echo lang(array("es"=>"No aparece en portada",""=>"Not visible on Home")) ?>
                    </option>
                    <option <%= (nivel_importancia == 1)?"selected":"" %> value="1">
                    <?php echo lang(array("es"=>"Aparece en portada",""=>"Visible on Home")) ?>
                    </option>
                    <option <%= (nivel_importancia == 2)?"selected":"" %> value="2">
                    <?php echo lang(array("es"=>"Unica noticia en portada",""=>"Only post on Home")) ?>
                    </option>
                  </select>
                </div>
              <% } %>

              <% if (id != undefined && ID_EMPRESA != 366) { %>
                <div class="form-group <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
                  <label class="control-label">Link</label>
                  <input type="text" class="form-control" name="link" value="<%= link %>" id="entrada_link">
                </div>
              <% } %>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">
                    <?php echo lang(array(
                      "es"=>(($empresa->id == 283) ? "Telefono" : "Texto para listado"),
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

              <div class="form-group <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
                <label class="i-checks m-b-none">
                  <input id="entrada_privada" value="1" <%= (privada==1)?"checked":"" %> type="checkbox"><i></i>
                  <?php echo lang(array(
                    "es"=>"La publicaci&oacute;n es privada (solo la pueden ver los usuarios registrados).",
                    "en"=>"Private page (only visible for registered users).",
                  )); ?>
                </label>
              </div>

              <% if (ID_PROYECTO == 5) { %>
                <div class="form-group">
                  <label class="control-label">Comisión de alumnos</label>
                  <select id="entradas_comisiones" name="id_comision" class="form-control w100p"></select>
                </div>
              <% } %>

              <% if (ID_EMPRESA == 70) { %>
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Cliente",
                      "en"=>"Company",
                    )); ?>
                  </label>
                  <select id="entradas_clientes" name="id_cliente" class="form-control w100p"></select>
                </div>
              <% } %>

              <div class="row <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
                <?php for($i=1;$i<=20;$i++) { ?>

                  <?php if (isset($empresa->config["entrada_custom_".$i."_file"])) { ?>
                    
                    <div class="col-xs-12">
                      <?php single_file_upload(array(
                        "name"=>"custom_$i",
                        "label"=>$empresa->config["entrada_custom_".$i."_file"],
                        "url"=>"/sistema/entradas/function/save_file/",
                      )); ?>
                    </div>

                  <?php } else if (isset($empresa->config["entrada_custom_".$i."_label"])) { ?>
                    <div class="<?php echo (isset($empresa->config['entrada_custom_'.$i.'_class'])) ? $empresa->config['entrada_custom_'.$i.'_class'] :'col-xs-12'?>">
                      <div class="form-group">
                        <label class="control-label"><?php echo $empresa->config["entrada_custom_".$i."_label"] ?></label>
                        <?php if(isset($empresa->config['entrada_custom_'.$i.'_values'])) { 
                          $values = explode("|",$empresa->config['entrada_custom_'.$i.'_values']); ?>
                          <select class="form-control" name="custom_<?php echo $i ?>">
                            <?php foreach($values as $value) { ?>
                              <option <%= (<?php echo "custom_".$i ?> == "<?php echo $value ?>")?"selected":""  %> value="<?php echo $value ?>"><?php echo $value ?></option>
                            <?php } ?>
                          </select>
                        <?php } else { ?>
                          <input type="text" name="custom_<?php echo $i ?>" id="entrada_custom_<?php echo $i ?>" value="<%= custom_<?php echo $i ?> %>" class="form-control"/>
                        <?php } ?>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Categor&iacute;a",
                    "en"=>"Category",
                  )); ?>
                </label>
                <% if (control.check("categorias_entradas")>1) { %>
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
                <% } else { %>
                  <select id="entrada_categorias" class="form-control"></select>
                <% } %>
              </div>

              <% if (control.check("not_editores")>0) { %>
                <div class="form-group">
                  <label class="control-label">
                    <%= (ID_EMPRESA == 1129)?"Anunciante":"<?php echo lang(array("es"=>"Editor","en"=>"Editor")); ?>" %>
                  </label>
                  <?php /*
                  <% if (control.check("not_editores")>1) { %>
                    <div class="input-group">
                      <select id="entrada_editores" class="form-control"></select>
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
                    <select id="entrada_editores" class="form-control"></select>
                  <% } %>
                  */ ?>
                  <select id="entrada_editores" class="form-control"></select>
                </div>
              <% } %>

              <% if (MILLING == 1) { %>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Event</label>
                      <select id="entradas_eventos" name="custom_1" class="form-control w100p"></select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Conference</label>
                      <select id="entradas_training" name="custom_2" class="form-control w100p"></select>
                    </div>
                  </div>
                </div>                
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Cliente",
                      "en"=>"Company",
                    )); ?>
                  </label>
                  <select id="entradas_clientes" name="id_cliente" class="form-control w100p"></select>
                </div>
              <% } %>

            </div>
          </div>
        </div>
      </div>

      <div class="col-md-10 col-md-offset-1">
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
                "url_file"=>"entradas/function/save_file/",
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

        <?php 
        // Colegio de Odontologos
        if ($empresa->id == 606) include_once("entradas_detalle_606.php"); ?>

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

        <div class="panel panel-default <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Comentarios",
                    "en"=>"Comments",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" id="entrada_comentarios_activo" name="comentarios_activo" class="checkbox" value="1" <%= (comentarios_activo == 1)?"checked":"" %> >
                      <i></i>
                      <?php echo lang(array(
                        "es"=>"Habilitar comentarios para la entrada.",
                        "en"=>"Enable comments",
                      )); ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="b-a table-responsive">
                <table id="entradas_tabla" class="table table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th><?php echo lang(array(
                    "es"=>"Fecha",
                    "en"=>"Date",
                  )); ?></th>
                      <th><?php echo lang(array(
                    "es"=>"Nombre",
                    "en"=>"Name",
                  )); ?></th>
                      <th><?php echo lang(array(
                    "es"=>"Comentarios",
                    "en"=>"Comments",
                  )); ?></th>
                      <th><?php echo lang(array(
                    "es"=>"Acciones",
                    "en"=>"Actions",
                  )); ?></th>
                    </tr>
                  </thead>
                  <tbody class="tbody">
                    <% if (comentarios.length == 0) { %>
                      <tr><td colspan="5"><?php echo lang(array(
                    "es"=>"La nota no tiene comentarios",
                    "en"=>"No comments in this post",
                  )); ?></td></tr>
                    <% } else { %>
                      <% for (var i=0;i< comentarios.length;i++) { %>
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
            </div>
          </div>
        </div>

        <% if (ID_EMPRESA == 225) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Emociones",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <div class="checkbox">
                      <label class="i-checks">
                        <input type="checkbox" id="entrada_habilitar_emociones" name="habilitar_emociones" class="checkbox" value="1" <%= (habilitar_emociones == 1)?"checked":"" %> >
                        <i></i>
                        <?php echo lang(array(
                          "es"=>"Habilitar emociones para la entrada.",
                        )); ?>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <?php for($i=1;$i<=4;$i++) { ?>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Emocion <?php echo $i ?></label>
                        <select id="entrada_emocion_<?php echo $i ?>_label" class="form-control" name="emocion_<?php echo $i ?>_label">
                          <option <%= (emocion_<?php echo $i ?>_label == "")?"selected":"" %> value="">Seleccionar</option>
                          <option <%= (emocion_<?php echo $i ?>_label == "Me encanta")?"selected":"" %> value="Me encanta">Me encanta</option>
                          <option <%= (emocion_<?php echo $i ?>_label == "Me alegra")?"selected":"" %> value="Me alegra">Me alegra</option>
                          <option <%= (emocion_<?php echo $i ?>_label == "Me enoja")?"selected":"" %> value="Me enoja">Me enoja</option>
                          <option <%= (emocion_<?php echo $i ?>_label == "Me entristece")?"selected":"" %> value="Me entristece">Me entristece</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Votos</label>
                        <input type="text" name="emocion_<?php echo $i ?>_cant" id="entrada_emocion_<?php echo $i ?>_cant" value="<%= emocion_<?php echo $i ?>_cant %>" class="form-control"/>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Preguntas y respuestas",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                </div>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Puede agregar distintas preguntas y respuestas a la entrada, como una entrevista, FAQs, etc.",
                  )); ?>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (preguntas.length > 0)?'display:block':'' %>">
              <div class="padder">
                <div class="m-b clearfix">
                  <div class="form-group">
                    <textarea placeholder="Escribe aqui la pregunta..." id="entrada_entrevista_pregunta" class="form-control no-model"></textarea>
                  </div>
                  <div class="form-group">
                    <textarea placeholder="Escribe aqui la respuesta..." id="entrada_entrevista_respuesta" class="form-control no-model"></textarea>
                  </div>
                  <div class="form-group">
                    <div class="form-inline">
                      <label class="control-label m-r">Segundos en el video:</label>
                      <div class="input-group w300">
                        <input type="text" id="entrada_entrevista_segundos" class="form-control no-model">
                        <span class="input-group-btn w1p">
                          <a id="preguntas_agregar" class="btn btn-info">
                            <i class="fa ico fa-plus"></i> Agregar pregunta
                          </a>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="">
                  <table id="preguntas_tabla" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th>Pregunta</th>
                        <th>Respuesta</th>
                        <th>Tiempo</th>
                        <th class="w25"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <% for(var i=0;i< preguntas.length;i++) { %>
                        <% var p = preguntas[i] %>
                        <tr>
                          <td class="editar_pregunta pregunta"><%= p.pregunta %></td>
                          <td class="editar_pregunta respuesta"><%= p.respuesta %></td>
                          <td class="editar_pregunta segundos"><%= p.segundos %></td>
                          <td><i class='fa fa-times eliminar_pregunta text-danger cp'></i></td>
                        </tr>
                      <% } %>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        <% } %>


        <% if (ID_EMPRESA == 366) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">Fechas y horarios</label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                </div>
                <div class="panel-description">Seleccione las distintas fechas y horarios del evento.</div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (horarios.length > 0)?'display:block':'' %>">
              <div class="padder">
                <div class="m-b clearfix">
                  <div class="row">
                    <div class="col-sm-6">
                      <label class="control-label">Fecha</label>
                      <div class="input-group">
                        <input type="text" id="entrada_horarios_fecha" class="form-control no-model"/>
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>              
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <label class="control-label">Hora</label>
                      <div class="input-group">
                        <input type="text" id="entrada_horarios_hora" class="form-control no-model">
                        <span class="input-group-btn w1p">
                          <a id="horarios_agregar" class="btn btn-info">
                            <i class="fa ico fa-plus"></i> Agregar horario
                          </a>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="">
                  <table id="horarios_tabla" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th class="w25"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <% for(var i=0;i< horarios.length;i++) { %>
                        <% var p = horarios[i] %>
                        <tr>
                          <td class="editar_horario fecha"><%= p.fecha %></td>
                          <td class="editar_horario hora"><%= p.hora %></td>
                          <td><i class='fa fa-times eliminar_horario text-danger cp'></i></td>
                        </tr>
                      <% } %>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        <% } %>


        <% if (MILLING == 0) { %>
          <div class="panel panel-default <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Consultas",
                      "en"=>"Contact Form",
                    )); ?>                  
                  </label>
                  <div class="panel-description">
                    <div class="checkbox">
                      <label class="i-checks">
                        <input type="checkbox" id="entrada_habilitar_contacto" name="habilitar_contacto" class="checkbox" value="1" <%= (habilitar_contacto == 1)?"checked":"" %> >
                        <i></i>
                        <?php echo lang(array(
                          "es"=>"Habilitar el formulario de consulta.",
                          "en"=>"Enable a contact form.",
                        )); ?>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <% } %>

        <% if (MILLING == 0) { %>
          <div class="panel panel-default <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Entradas relacionadas",
                      "en"=>"Related posts",
                    )); ?>
                  </label>
                  <a id="expand_relacionados" class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Agregue relaciones con otras entradas puntuales o con categorias determinadas.",
                      "en"=>"You can set a exact post or select a related category.",
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
                      "es"=>"Relaciones con otras publicaciones específicas",
                      "en"=>"Set specific post",
                    )); ?>
                  </label>

                  
                  <input class="form-control" type="text" id="entradas_buscar_productos" placeholder="<?php echo lang(array(
                      "es"=>"Busque los productos especificos con los que desea relacionar...",
                      "en"=>"Type the related post...",
                    )); ?>" />
                  <ul id="entradas_tabla_relacionados" style="overflow-y: auto;" class="list-group gutter list-group-lg list-group-sp">
                    <% for(var i=0;i< relacionados.length;i++) { %>
                      <% var a = relacionados[i]; %>
                      <li class='list-group-item'>
                        <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>
                        <img style='margin-left: 10px; margin-right:10px; max-height:50px' src='/sistema/<%= a.path %>'/>
                        <span class='id dn'><%= a.id %></span>
                        <span class='titulo'><%= a.titulo %></span>
                        <span class='pull-right m-t eliminar_relacionado'><i class='fa fa-fw fa-times'></i> </span>
                      </li>
                    <% } %>
                  </ul>
                </div>
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Relaciones por categorias",
                      "en"=>"Select a related category",
                    )); ?>
                  </label>
                  <div id="entradas_categorias_tree" style="overflow: auto;"></div>
                </div>
              </div>
            </div>
          </div>
        <% } %>

        <div class="panel panel-default <%= (ID_EMPRESA == 283 && PERFIL != 347)?"dn":"" %>">
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
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="entrada_seo_ocultar_sitemap" name="seo_ocultar_sitemap" class="checkbox" value="1" <%= (seo_ocultar_sitemap == 1)?"checked":"" %> >
                    <i></i>
                    <?php echo lang(array(
                      "es"=>"No agregar la entrada al sitemap.xml.",
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
                    <input type="text" name="seo_sitemap_priority" id="entrada_seo_sitemap_priority" value="<%= seo_sitemap_priority %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array("es"=>"Frecuencia","en"=>"Change Frecuency")) ?>
                    </label>
                    <select name="seo_sitemap_change_freq" id="entrada_seo_sitemap_change_freq" class="form-control">
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

        <div class="line b-b m-b-lg"></div>

        <div class="tar">
          <button title="<?php echo lang(array("es"=>"Abre la entrada desde la web","en"=>"View the post preview on the web")) ?>" class="btn m-r-xs previsualizar btn-default">
            <?php echo lang(array("es"=>"Previsualizar","en"=>"&nbsp;&nbsp;Preview&nbsp;&nbsp;",)); ?>
          </button>
          <button title="<?php echo lang(array("es"=>"Guarda los cambios pero no se aplican a la web","en"=>"Save the changes without apply on the web")) ?>" class="btn m-r-xs guardar_borrador btn-default">
          <?php echo lang(array("es"=>"Guardar en borrador","en"=>"Save as draft",)); ?>
          </button>
          <?php /*
          <% if (typeof eliminada != undefined && eliminada == 1) { %>
            <button title="Restaura una entrada eliminada" class="btn m-r-xs restaurar btn-success">&nbsp;&nbsp;&nbsp;Restaurar&nbsp;&nbsp;&nbsp;</button>
          <% } else { %>
            */ ?>
            <button title="<?php echo lang(array("es"=>"Guarda los cambios y los publica en la web","en"=>"The changes will be saved and applied on the web")) ?>" class="btn m-r-xs guardar btn-success">
              <?php echo lang(array("es"=>"&nbsp;&nbsp;Publicar&nbsp;&nbsp;","en"=>"&nbsp;&nbsp;Publish&nbsp;&nbsp;",)); ?>
            </button>
          <?php /*<% } %>*/ ?>
        </div>
      </div>
      
    </div>
  </div>
</div>
