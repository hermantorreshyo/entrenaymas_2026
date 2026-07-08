<script type="text/template" id="image_upload_template">
<div class='modal-content'>
  <div class='modal-header'>
    Subir im&aacute;genes en lote
  </div>
  <div class='modal-body'>
    <span class="btn btn-default fileinput-button">
        <i class="glyphicon glyphicon-folder-open m-r-xs"></i>
        <span>Seleccionar archivos</span>
        <!-- The file input field used as target for the file upload widget -->
        <input id="fileupload" type="file" name="files[]" multiple>
    </span>
    <br>
    <br>
    <!-- The global progress bar -->
    <div id="progress" class="progress">
        <div class="progress-bar progress-bar-success"></div>
    </div>
    <!-- The container for the uploaded files -->
    <div id="files" class="files"></div>
  </div>
  <div class='modal-footer clearfix'>
    <button class='cerrar btn btn-default pull-left'>Cerrar</button>
    <button class='aceptar btn btn-success pull-right'>Aceptar</button>
  </div>
</div>
</script>