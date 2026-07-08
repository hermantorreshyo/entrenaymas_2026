<div class="panel panel-default <%= (ID_EMPRESA == 1284)?"dn":"" %>">
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
            "en"=>"Agregar variantes a productos como talle, color, etc.",
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