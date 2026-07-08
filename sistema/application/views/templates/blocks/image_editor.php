<script type="text/template" id="image_editor_template">
<div class='modal-content'>
  <div class='modal-header'>
    <div class="form-inline">
      <select class="form-control w200 dn" id="image_editor_filters">
        <option value="reset">Elegir un filtro</option>
        <option value="clarity">clarity</option>
        <option value="pinhole">pinhole</option>
        <option value="love">love</option>
        <option value="jarques">jarques</option>
        <option value="orangePeel">orangePeel</option>
        <option value="sinCity">sinCity</option>
        <option value="grungy">grungy</option>      
        <option value="oldBoot">oldBoot</option>
        <option value="lomo">lomo</option>
        <option value="vintage">vintage</option>
        <option value="crossProcess">crossProcess</option>
        <option value="concentrate">concentrate</option>
        <option value="glowingSun">glowingSun</option>
        <option value="sunrise">sunrise</option>
        <option value="nostalgia">nostalgia</option>
        <option value="hemingway">hemingway</option>
        <option value="herMajesty">herMajesty</option>
        <option value="hazyDays">hazyDays</option>
      </select>
      <button class="btn btn-default move-button dn"><i class="fa fa-arrows"></i></button>
      <button class="btn btn-default rotate-left"><i class="fa fa-rotate-left"></i></button>
      <button class="btn btn-default rotate-right"><i class="fa fa-rotate-right"></i></button>
    </div>
  </div>
  <div class='modal-body'>
    <canvas id='canvas' style='max-width:100%;'></canvas>
  </div>
  <div class='modal-footer'>
    <label class="pull-left" style="margin-top:7px; margin-right: 5px;">Fondo: </label>
    <div style="width:220px" class="input-group colorpicker-component pull-left">
      <input type="text" class="form-control" value="<%= (typeof COLOR_FONDO_IMAGENES_DEFECTO != 'undefined' && COLOR_FONDO_IMAGENES_DEFECTO != '') ? COLOR_FONDO_IMAGENES_DEFECTO : '#FFFFFF'  %>" />
      <span class="input-group-addon"><i></i></span>
    </div>
    <button class='crop-ok btn btn-success pull-right'>Aceptar</button>
  </div>
</div>
</script>