<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-4"></div>

      <div class="col-md-8">

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Nombre",
                    "en"=>"Name",
                  )); ?>
                </label>
                <% if (edicion) { %>
                  <input type="text" 
                    placeholder="<?php echo lang(array(
                      "es"=>"Ej: Camisa a cuadros manga larga",
                      "en"=>"Ej: Camisa a cuadros manga larga",
                    )); ?>" 
                    required name="nombre" id="articulo_nombre" value="<%= nombre %>" class="form-control"/>
                <% } else { %>
                  <span><%= nombre %></span>
                <% } %>
              </div>
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <textarea name="texto" id="articulo_texto"><%= texto %></textarea>
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
                  <select id="articulo_rubros" class="w100p"></select>
                  <span class="input-group-btn">
                    <button tabindex="-1" class="btn btn-info w100 agregar_rubro">
                      <?php echo lang(array(
                        "es"=>"+ Categor&iacute;a",
                        "en"=>"+ Category",
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
                <select id="articulo_clientes" class="form-control no-model w100p"></select>
              </div>
              <% if (control.check("marcas") > 0) { %>
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Marca",
                      "en"=>"Trade",
                    )); ?>
                  </label>
                  <div class="input-group">
                    <select id="articulo_marcas" class="w100p"></select>
                    <span class="input-group-btn">
                      <button tabindex="-1" class="btn btn-info w100 agregar_marca">
                        <?php echo lang(array(
                          "es"=>"+ Marca",
                          "en"=>"+ Trade",
                        )); ?>
                      </button>  
                    </span>
                  </div>
                </div>
              <% } %>
            </div>
          </div>
        </div>


        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <?php
              $label = lang(array(
                "es"=>"Im&aacute;genes",
                "en"=>"Photos",
              )); ?>
              <?php 
              multiple_upload(array(
                "name"=>"images",
                "label"=>$label,
                "url"=>"articulos/function/save_image/",
                "width"=>(isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 600),
                "resizable"=>(isset($empresa->config["producto_galeria_image_resizable"]) ? $empresa->config["producto_galeria_image_resizable"] : 0),
                "upload_multiple"=>true,
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
                    "es"=>"Mejore el posicionamiento de su web utilizando las siguientes opciones.",
                    "en"=>"Improve the position of your site with this tools.",
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
                  <span id="articulo_seo_title_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>70</span>
                </label>
                <input type="text" data-max="70" data-id="articulo_seo_title_cantidad" name="seo_title" id="articulo_seo_title" value="<%= seo_title %>" class="form-control text-remain"/>
              </div>
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <label class="control-label fr">
                  <span id="articulo_seo_description_cantidad">0</span>
                  <?php echo lang(array(
                    "es"=>"de",
                    "en"=>"of",
                  )); ?>
                  <span>160</span>
                </label>
                <textarea data-max="160" data-id="articulo_seo_description_cantidad" name="seo_description" id="articulo_seo_description" class="form-control text-remain"><%= seo_description %></textarea>
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
        <button class="btn guardar btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
      </div>
    </div>
  </div>
</div>