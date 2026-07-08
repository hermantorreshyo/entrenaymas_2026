<div class="form">
  <div class="form-group form-spacer-two">
    <select name="tit" class="form-control" id="searchform_titulos">
      <option value="0">Elegir Tipo de Profesión</option>
      <?php $titulos = $profesional_model->get_titulos(); 
      foreach($titulos as $r) { ?>
        <option <?php echo (isset($vc_titulos) && in_array($r->id, $vc_titulos))?"selected":"" ?> value="<?php echo $r->id ?>"><?php echo $r->nombre ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="form-group no-border">
    <select name="locs" class="form-control" id="searchform_localidades">
      <option value="0">Localidades</option>
      <?php $localidades = $profesional_model->get_localidades(); 
      foreach($localidades as $r) { ?>      
        <option <?php echo (isset($vc_localidades) && in_array($r->id, $vc_localidades))?"selected":"" ?> value="<?php echo $r->id ?>"><?php echo $r->nombre ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="form-btn">
    <button onclick="<?php echo $funcion_buscar ?>()" class="psweb-btn">Buscar Ahora</button>
  </div>
</div>