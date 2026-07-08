<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="form-group">
            <label class="control-label">Name</label>
            <% if (edicion) { %>
              <input type="text" required name="nombre" id="clientes_nombre" value="<%= nombre %>" class="form-control"/>
            <% } else { %>
              <span><%= nombre %></span>
            <% } %>
          </div>

          <div class="form-group">
            <label class="control-label">Email list </label>
            <% if (edicion) { %>
              <input type="text" name="email" class="form-control" id="clientes_email" value="<%= email %>"/>
            <% } else { %>
              <span><%= email %></span>
            <% } %>
          </div>

          <div class="form-group">
            <label class="control-label">Web </label>
            <% if (edicion) { %>
              <input type="text" name="custom_1" class="form-control" id="clientes_custom_1" value="<%= custom_1 %>"/>
            <% } else { %>
              <span><%= custom_1 %></span>
            <% } %>
          </div>

          <div class="form-group">
            <label class="control-label">QR Code </label>
            <% if (edicion) { %>
              <div class="input-group">
                <input type="text" name="codigo_postal" class="form-control" id="clientes_codigo_postal" value="<%= codigo_postal %>"/>
                <span class="input-group-btn">
                  <button class="btn btn-default generar_qr">Generate QR</button>
                </span>
                <span class="input-group-btn">
                  <button class="btn btn-default generar_short_link">Generate Short Link</button>
                </span>
              </div>
            <% } else { %>
              <span><%= codigo_postal %></span>
            <% } %>
          </div>

          <?php
          single_upload(array(
            "name"=>"path",
            "label"=>"Company Logo",
            "url"=>"/sistema/clientes/function/save_image/",
            "width"=>(isset($empresa->config["cliente_image_width"]) ? $empresa->config["cliente_image_width"] : 256),
            "height"=>(isset($empresa->config["cliente_image_height"]) ? $empresa->config["cliente_image_height"] : 256),
            "crop_type"=>(isset($empresa->config["cliente_image_crop_type"]) ? $empresa->config["cliente_image_crop_type"] : 1),
            "resizable"=>(isset($empresa->config["cliente_image_resizable"]) ? $empresa->config["cliente_image_resizable"] : 0),
            "quality"=>(isset($empresa->config["cliente_image_quality"]) ? $empresa->config["cliente_image_quality"] : 0.92),
            "thumbnail_width"=>(isset($empresa->config["cliente_thumbnail_width"]) ? $empresa->config["cliente_thumbnail_width"] : 0),
            "thumbnail_height"=>(isset($empresa->config["cliente_thumbnail_height"]) ? $empresa->config["cliente_thumbnail_height"] : 0),
          )); ?>

          <div class="form-group">
            <label class="control-label">Information </label>
            <% if (edicion) { %>
              <textarea style="height:100px" class="form-control" name="observaciones" id="cliente_observaciones"><%= observaciones %></textarea>
            <% } else { %>
              <span><%= observaciones %></span>
            <% } %>
          </div>

          <div class="form-group">
            <label class="control-label">Company type</label>
            <% if (edicion) { %>
              <select class="form-control" name="custom_2">
                <% if (ID_EMPRESA == 448) { %>
                  <option <%= (custom_2=="")?"selected":"" %> value="">Todas</option>
                  <option <%= (custom_2=="maquinarias")?"selected":"" %> value="maquinarias">Maquinarias</option>
                  <option <%= (custom_2=="nutricion")?"selected":"" %> value="nutricion">Nutricion</option>
                  <option <%= (custom_2=="packaging")?"selected":"" %> value="packaging">Packaging</option>
                <% } else if (ID_EMPRESA == 493) { %>
                  <option <%= (custom_2=="")?"selected":"" %> value="">All</option>
                  <option <%= (custom_2=="maquinarias")?"selected":"" %> value="maquinarias">Machinery</option>
                  <option <%= (custom_2=="nutricion")?"selected":"" %> value="nutricion">Nutrition</option>
                  <option <%= (custom_2=="packaging")?"selected":"" %> value="packaging">Packaging</option>
                <% } else { %>
                  <option <%= (custom_2=="")?"selected":"" %> value="">Feed/Food company</option>
                  <option <%= (custom_2=="feed")?"selected":"" %> value="feed">Feed company</option>
                  <option <%= (custom_2=="food")?"selected":"" %> value="food">Food company</option>
                <% } %>
              </select>
            <% } else { %>
              <span><%= custom_1 %></span>
            <% } %>
          </div>

          <div class="form-group">
            <% if (edicion) { %>
              <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" id="cliente_lista" name="lista" class="checkbox" value="1" <%= (lista == 1)?"checked":"" %> ><i></i>
                  Is a customer?
                </label>
              </div>
            <% } else { %>
              <span><%= ((lista==0) ? "No es cliente" : "Es cliente") %></span>
            <% } %>
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

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Name </label>
                <% if (edicion) { %>
                  <input type="text" name="contacto_nombre" class="form-control" id="clientes_contacto_nombre" value="<%= contacto_nombre %>"/>
                <% } else { %>
                  <span><%= contacto_nombre %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Address </label>
                <% if (edicion) { %>
                  <input type="text" name="direccion" class="form-control" id="clientes_direccion" value="<%= direccion %>"/>
                <% } else { %>
                  <span><%= direccion %></span>
                <% } %>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Email </label>
                <% if (edicion) { %>
                  <input type="text" name="contacto_email" class="form-control" id="clientes_contacto_email" value="<%= contacto_email %>"/>
                <% } else { %>
                  <span><%= contacto_email %></span>
                <% } %>
              </div>    
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Telephone </label>
                <% if (edicion) { %>
                  <input type="text" name="contacto_telefono" class="form-control" id="clientes_contacto_telefono" value="<%= contacto_telefono %>"/>
                <% } else { %>
                  <span><%= contacto_telefono %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Position </label>
                <% if (edicion) { %>
                  <input type="text" name="celular" class="form-control" id="clientes_celular" value="<%= celular %>"/>
                <% } else { %>
                  <span><%= celular %></span>
                <% } %>
              </div>
            </div>
          </div>

          <?php
          single_upload(array(
            "name"=>"path_2",
            "label"=>"Contact photo",
            "url"=>"/sistema/clientes/function/save_image/",
            "width"=>(isset($empresa->config["cliente_image_2_width"]) ? $empresa->config["cliente_image_2_width"] : 256),
            "height"=>(isset($empresa->config["cliente_image_2_height"]) ? $empresa->config["cliente_image_2_height"] : 256),
            "quality"=>(isset($empresa->config["cliente_image_2_quality"]) ? $empresa->config["cliente_image_2_quality"] : 0.92),
            "thumbnail_width"=>(isset($empresa->config["cliente_thumbnail_width"]) ? $empresa->config["cliente_thumbnail_width"] : 0),
            "thumbnail_height"=>(isset($empresa->config["cliente_thumbnail_height"]) ? $empresa->config["cliente_thumbnail_height"] : 0),
          )); ?>

          <div class="form-group">
            <label class="control-label">Link Ingles</label>
            <input type="text" name="custom_3" class="form-control" id="clientes_custom_3" value="<%= custom_3 %>" <%= (edicion)?"":"disabled" %>/>
          </div>
          <div class="form-group">
            <label class="control-label">Link Español</label>
            <input type="text" name="fax" class="form-control" id="clientes_fax" value="<%= fax %>" <%= (edicion)?"":"disabled" %>/>
          </div>
          <div class="form-group">
            <label class="control-label">Link Turco</label>
            <input type="text" name="horario" class="form-control" id="clientes_horario" value="<%= horario %>" <%= (edicion)?"":"disabled" %>/>
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
            <input type="text" data-max="70" data-id="articulo_seo_title_cantidad" name="custom_4" id="articulo_custom_4" value="<%= custom_4 %>" class="form-control text-remain"/>
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
            <textarea data-max="160" data-id="articulo_seo_description_cantidad" name="custom_5" id="articulo_custom_5" class="form-control text-remain"><%= custom_5 %></textarea>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>