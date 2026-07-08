<?php
function single_upload($conf) {
  $name = isset($conf["name"]) ? $conf["name"] : "path";
  $url = isset($conf["url"]) ? $conf["url"] : "";
  $url_file = isset($conf["url_file"]) ? $conf["url_file"] : "";
  $dir = isset($conf["dir"]) ? $conf["dir"] : "uploads";
  $width = isset($conf["width"]) ? $conf["width"] : 0;
  $height = isset($conf["height"]) ? $conf["height"] : 0;
  $quality = isset($conf["quality"]) ? $conf["quality"] : 0.92;
  $label = isset($conf["label"]) ? $conf["label"] : "Imagen";
  $label_button = isset($conf["label_button"]) ? $conf["label_button"] : lang(array("es"=>"Recortar","en"=>"Crop"));
  $class_button = isset($conf["class_button"]) ? $conf["class_button"] : "btn-default";
  $description = isset($conf["description"]) ? $conf["description"] : "";
  $resizable = isset($conf["resizable"]) ? $conf["resizable"] : 0;
  $hide_crop = isset($conf["hide_crop"]) ? $conf["hide_crop"] : 0;
  $thumbnail_width = isset($conf["thumbnail_width"]) ? $conf["thumbnail_width"] : 0;
  $thumbnail_height = isset($conf["thumbnail_height"]) ? $conf["thumbnail_height"] : 0;
  $crop_type = isset($conf["crop_type"]) ? $conf["crop_type"] : 0;
?>
<div class="form-group upload_container">

    <?php if (!empty($label)) { ?>
        <label class="control-label tal"><?php echo $label ?></label>
        <div class="">
    <?php } else { ?>
        <div>
    <?php } ?>

        <% var display_file = (!isEmpty(<?php echo $name; ?>)) %>

        <!-- URL DONDE SE GUARDA LA IMAGEN -->
        <input id="<?php echo $name; ?>_url" type="hidden" value="<?php echo $url; ?>" />

        <input id="<?php echo $name; ?>_url_file" type="hidden" value="<?php echo $url_file; ?>" />

        <!-- PREVIEW DE LA IMAGEN GUARDADA -->
        <img id="preview_<?php echo $name; ?>" class="img_preview" style="max-width: 150px; display:<%= (display_file)?'inline-block':'none' %>" src="<%= <?php echo $name; ?> %>"/>

        <!-- PATH DE LA IMAGEN (Se mantiene para guardarla cuando se submitea el form) -->
        <input id="hidden_<?php echo $name; ?>" type="hidden" value="<%= <?php echo $name; ?> %>" name="<?php echo $name; ?>"/>

        <!-- Para editar la imagen -->
        <i style="display:<%= (display_file)?'inline-block':'none' %>" class="fa fa-pencil editar_imagen" data-id="<?php echo $name ?>"></i>
        <!-- Para borrar la imagen -->
        <i style="display:<%= (display_file)?'inline-block':'none' %>" class="glyphicon glyphicon-remove text-danger eliminar_imagen" data-id="<?php echo $name ?>"></i>

        <!-- Datos que se envian junto con la imagen cuando se sube -->
        <input id="<?php echo $name; ?>_data" class="hidden_data" type="hidden"/>
        <input id="<?php echo $name; ?>_color" class="hidden_color" type="hidden"/>
        <input id="<?php echo $name; ?>_src" class="hidden_src" type="hidden"/>
        <input id="<?php echo $name; ?>_width" class="width" value="<?php echo $width ?>" type="hidden"/>
        <input id="<?php echo $name; ?>_height" class="height" value="<?php echo $height ?>" type="hidden"/>
        <input id="<?php echo $name; ?>_resizable" class="resizable" value="<?php echo $resizable ?>" type="hidden"/>
        <input id="<?php echo $name; ?>_quality" class="hidden_quality" value="<?php echo $quality ?>" type="hidden"/>
        <input id="<?php echo $name; ?>_thumbnail_width" class="hidden_thumbnail_width" value="<?php echo $thumbnail_width ?>" type="hidden"/>
        <input id="<?php echo $name; ?>_thumbnail_height" class="hidden_thumbnail_height" value="<?php echo $thumbnail_height ?>" type="hidden"/>
        <input id="<?php echo $name; ?>_crop_type" class="hidden_crop_type" value="<?php echo $crop_type ?>" type="hidden"/>

        <div class="bootstrap-filestyle-container" style="display:<%= (!display_file)?'inline-block':'none' %>">
            <input id="<?php echo $name; ?>" value="<%= <?php echo $name; ?> %>" class="single_upload" type="file" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
            <input id="<?php echo $name; ?>_upload" value="<%= <?php echo $name; ?> %>" data-name="<?php echo $name; ?>" class="single_file_upload" type="file" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
            <div class="bootstrap-filestyle input-group">
                <input style="<?php echo (empty($label)?"display:none":"") ?>" id="<?php echo $name; ?>_text" type="text" class="form-control" disabled="">
                <span class="group-span-filestyle input-group-btn" tabindex="0">
                    <?php if (!empty($url)) { ?>
                      <label style="<?php echo ($hide_crop==1)?"display:none":"" ?>" for="<?php echo $name; ?>" class="btn <?php echo $class_button; ?>">
                        <span class="fa fa-crop m-r-xs"></span>
                        <?php echo $label_button ?>
                      </label>
                    <?php } ?>
                    <?php if (!empty($url_file)) { ?>
                      <label for="<?php echo $name; ?>_upload" class="btn btn-default subir">
                        <span class="fa fa-upload m-r-xs"></span>
                        <?php echo lang(array("es"=>"Subir","en"=>"Upload")); ?>
                      </label>
                    <?php } ?>
                </span>
            </div>
            <?php if (!empty($description)) { ?>
                <span class="help-block m-b-none"><?php echo $description ?></span>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<?php
function single_file_upload($conf) {
  $name = isset($conf["name"]) ? $conf["name"] : "path";
  $url = isset($conf["url"]) ? $conf["url"] : "";
  $label = isset($conf["label"]) ? $conf["label"] : "Archivo";
  $description = isset($conf["description"]) ? $conf["description"] : "";
?>
<div class="form-group upload_container">
  <?php if (!empty($label)) { ?>
    <label class="control-label tal"><?php echo $label ?></label>
  <?php } ?>
  <div class="">
      <% var display_file = (!isEmpty(<?php echo $name; ?>)) %>

      <!-- URL DONDE SE GUARDA LA IMAGEN -->
      <input id="<?php echo $name; ?>_url_file" type="hidden" value="<?php echo $url; ?>" />

      <!-- PREVIEW DE LA IMAGEN GUARDADA -->
      <a style="display:<%= (display_file)?'inline-block':'none' %>" target="_blank" href="/sistema/<%= <?php echo $name; ?> %>" class="preview_file" id="preview_<?php echo $name; ?>">
        <% var ext = String(<?php echo $name; ?>).split('.').pop().toLowerCase() %>
        <% if (ext=="gif" || ext=="jpg" || ext=="png" || ext=="jpeg") { %>
          <img style="max-width: 60px;" src="<%= <?php echo $name; ?> %>"/>
        <% } else { %>
          <%= <?php echo $name; ?> %>
        <% } %>
      </a>

      <!-- PATH DEL ARCHIVO -->
      <input id="hidden_<?php echo $name; ?>" type="hidden" value="<%= <?php echo $name; ?> %>" name="<?php echo $name; ?>"/>

      <!-- Para borrar el archivo -->
      <i style="display:<%= (display_file)?'inline-block':'none' %>" class="glyphicon glyphicon-remove text-danger eliminar_archivo" data-id="<?php echo $name ?>"></i>

      <!-- Datos que se envian junto con la imagen cuando se sube -->
      <input id="<?php echo $name; ?>_src" class="hidden_src" type="hidden"/>

      <div class="bootstrap-filestyle-container" style="display:<%= (!display_file)?'inline-block':'none' %>">
        <input id="<?php echo $name; ?>_upload" data-name="<?php echo $name; ?>" value="<%= <?php echo $name; ?> %>" class="single_file_upload" type="file" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
        <div class="bootstrap-filestyle input-group">
          <input id="<?php echo $name; ?>_text" type="text" class="form-control" disabled="">
          <span class="group-span-filestyle input-group-btn" tabindex="0">
            <label for="<?php echo $name; ?>_upload" class="btn btn-default subir">
              <span class="fa fa-upload m-r-xs"></span>
              <?php echo lang(array(
                "es"=>"Elegir archivo",
                "en"=>"Add File"
              )); ?>
            </label>
          </span>
        </div>
        <?php if (!empty($description)) { ?>
          <span class="help-block m-b-none"><?php echo $description ?></span>
        <?php } ?>
      </div>
  </div>
</div>
<?php } ?>




<?php
function multiple_upload($conf) {
  $name = isset($conf["name"]) ? $conf["name"] : "path_multiple";
  $url = isset($conf["url"]) ? $conf["url"] : "";
  $width = isset($conf["width"]) ? $conf["width"] : 0;
  $height = isset($conf["height"]) ? $conf["height"] : 0;
  $quality = isset($conf["quality"]) ? $conf["quality"] : 0.92;
  $label = isset($conf["label"]) ? $conf["label"] : "Listado de Imagenes";
  $description = isset($conf["description"]) ? $conf["description"] : "";
  $resizable = isset($conf["resizable"]) ? $conf["resizable"] : 0;
  $thumbnail_width = isset($conf["thumbnail_width"]) ? $conf["thumbnail_width"] : 0;
  $thumbnail_height = isset($conf["thumbnail_height"]) ? $conf["thumbnail_height"] : 0;
  $crop_type = isset($conf["crop_type"]) ? $conf["crop_type"] : 1;
  $upload_multiple = isset($conf["upload_multiple"]) ? $conf["upload_multiple"] : true;
  $ver_galeria = isset($conf["ver_galeria"]) ? $conf["ver_galeria"] : true;
?>
<div class="form-group upload_container">

  <?php if (!empty($label)) { ?>
    <label class="control-label tal"><?php echo $label ?></label>
    <form id="<?php echo $name ?>_form">
  <?php } else { ?>
    <form id="<?php echo $name ?>_form">
  <?php } ?>

      <!-- URL DONDE SE GUARDA LA IMAGEN -->
      <input id="<?php echo $name; ?>_url" type="hidden" value="<?php echo $url; ?>" />

      <!-- PATH DE LA IMAGEN (Se mantiene para guardarla cuando se submitea el form) -->
      <input id="hidden_<?php echo $name; ?>" type="hidden" name="<?php echo $name; ?>"/>

      <!-- Datos que se envian junto con la imagen cuando se sube -->
      <input id="<?php echo $name; ?>_data" class="hidden_data" type="hidden"/>
      <input id="<?php echo $name; ?>_color" class="hidden_color" type="hidden"/>
      <input id="<?php echo $name; ?>_src" class="hidden_src" type="hidden"/>
      <input id="<?php echo $name; ?>_quality" class="hidden_quality" value="<?php echo $quality ?>" type="hidden"/>
      <input id="<?php echo $name; ?>_width" class="width" value="<?php echo $width ?>" type="hidden"/>
      <input id="<?php echo $name; ?>_height" class="height" value="<?php echo $height ?>" type="hidden"/>
      <input id="<?php echo $name; ?>_resizable" class="resizable" value="<?php echo $resizable ?>" type="hidden"/>
      <input id="<?php echo $name; ?>_thumbnail_width" class="hidden_thumbnail_width" value="<?php echo $thumbnail_width ?>" type="hidden"/>
      <input id="<?php echo $name; ?>_thumbnail_height" class="hidden_thumbnail_height" value="<?php echo $thumbnail_height ?>" type="hidden"/>
      <input id="<?php echo $name; ?>_crop_type" class="hidden_crop_type" value="<?php echo $crop_type ?>" type="hidden"/>

      <div class="bootstrap-filestyle-container">
          <input id="<?php echo $name; ?>" class="multiple_upload" type="file" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
          <div class="bootstrap-filestyle input-group">
              <input type="text" class="form-control" id="<?php echo $name; ?>_text" disabled="">
              <span class="group-span-filestyle input-group-btn" tabindex="0">
                <label for="<?php echo $name; ?>" class="btn btn-default ">
                    <span class="glyphicon glyphicon-folder-open m-r-xs"></span>
                    <?php echo lang(array("es"=>"Elegir y editar","en"=>"Choose and edit")); ?>
                </label>
              </span>
              <span class="group-span-filestyle input-group-btn" tabindex="0">
                <a href="javascript:void(0)" id="<?php echo $name; ?>_upload_multiple" class="btn btn-default fs14 fr w100p upload_multiple">
                  <i class="fa fa-upload m-r-xs"></i><?php echo lang(array("es"=>"Multiple","en"=>"Multiple")); ?>
                </a>
              </span>
              <?php /*if ($ver_galeria) { ?>
                <div style="display: table-cell; width: 70px">
                  <a href="javascript:void(0)" id="<?php echo $name; ?>_galeria" class="btn btn-default fs14 fr w100p abrir_galeria">
                    <i class="fa fa-picture-o m-r-xs"></i>Galeria
                  </a>
                </div>
              <?php }*/ ?>
          </div>
          <?php if (!empty($description)) { ?>
              <span class="help-block m-b-none"><?php echo $description ?></span>
          <?php } ?>
      </div>
  </form>
</div>
<div id="<?php echo $name; ?>_container" class="m-b sin-imagen">
  <ul id="<?php echo $name; ?>_tabla" class="list-group gutter list-group-lg list-group-sp"></ul>
</div>
<?php } ?>
