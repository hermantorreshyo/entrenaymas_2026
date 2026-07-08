<div class="row align-items-center">
  <div class="col-xl-3">
    <h1 class="titulo-listado wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s"><?php echo (isset($h1) && !empty($h1)) ? $h1 : "Resultados" ?></h1>
    <?php if (isset($texto_comercial) && !empty($texto_comercial)) { ?>
      <p class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s"><?php echo $texto_comercial ?></p>
    <?php } ?>
    <p class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s"><?php echo $total_resultados ?> profesionales encontrados</p>
  </div>
  <div class="col-xl-9 textright wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s">

    <div class="dropdown checkboxes <?php echo (sizeof($vc_tipos_perfil)>0)?"actived":"" ?>" style="width: 130px;">
      <div class="select">
        <span>Tipo de Perfil</span>
      </div>
      <ul class="dropdown-menu checkboxes">                               
        <li>
          <label>
            <div class="custom-control custom-checkbox buscador">
              <input <?php echo (in_array("1", $vc_tipos_perfil))?"checked":"" ?> value="1" type="checkbox" class="tipo_perfil custom-control-input" id="tipo_perfil_1">
              <label class="custom-control-label" for="tipo_perfil_1">Verificado</label>
            </div>
          </label>
        </li>
        <li>
          <label>
            <div class="custom-control custom-checkbox buscador">
              <input <?php echo (in_array("2", $vc_tipos_perfil))?"checked":"" ?> value="2" type="checkbox" class="tipo_perfil custom-control-input" id="tipo_perfil_2">
              <label class="custom-control-label" for="tipo_perfil_2">Todos los Perfiles</label>
            </div>
          </label>
        </li>
      </ul>
    </div>


    <div class="dropdown checkboxes <?php echo (sizeof($vc_tipos_terapias)>0)?"actived":"" ?>">
      <div class="select">
        <span>Tipo de Entrenamiento</span>
      </div>
      <ul class="dropdown-menu checkboxes">
        <?php $tipos_terapias = $profesional_model->get_tipos_terapias();
        foreach($tipos_terapias as $l) { ?>                                    
          <li>
            <label>
              <div class="custom-control custom-checkbox buscador">
                <input <?php echo (in_array($l->id, $vc_tipos_terapias))?"checked":"" ?> value="<?php echo $l->id ?>" type="checkbox" class="tipo_terapia custom-control-input" id="tipo_terapia_<?php echo $l->id ?>">
                <label class="custom-control-label" for="tipo_terapia_<?php echo $l->id ?>"><?php echo $l->nombre ?></label>
              </div>
            </label>
          </li>
        <?php } ?>
      </ul>
    </div>
    <div class="dropdown checkboxes <?php echo (sizeof($vc_tipos_atenciones)>0)?"actived":"" ?>" style="width: 155px;">
      <div class="select">
        <span>Tipo de Servicio</span>
      </div>
      <ul class="dropdown-menu checkboxes">
        <?php $tipos_atenciones = $profesional_model->get_tipos_atenciones();
        foreach($tipos_atenciones as $l) { ?>                                    
          <li>
            <label>
              <div class="custom-control custom-checkbox buscador">
                <input <?php echo (in_array($l->id, $vc_tipos_atenciones))?"checked":"" ?> value="<?php echo $l->id ?>" type="checkbox" class="tipo_atencion custom-control-input" id="tipo_atencion_<?php echo $l->id ?>">
                <label class="custom-control-label" for="tipo_atencion_<?php echo $l->id ?>"><?php echo $l->nombre ?></label>
              </div>
            </label>
          </li>
        <?php } ?>
      </ul>
    </div>
    <div class="dropdown checkboxes <?php echo (sizeof($vc_categorias)>0)?"actived":"" ?>">
      <div class="select">
        <span>Especialidades</span>
      </div>
      <ul class="dropdown-menu checkboxes">
        <?php $categorias = $profesional_model->get_categorias();
        foreach($categorias as $l) { ?>                                    
          <li>
            <label>
              <div class="custom-control custom-checkbox buscador">
                <input <?php echo (in_array($l->id, $vc_categorias))?"checked":"" ?> value="<?php echo $l->id ?>" type="checkbox" class="categoria custom-control-input" id="categoria_<?php echo $l->id ?>">
                <label class="custom-control-label" for="categoria_<?php echo $l->id ?>"><?php echo $l->nombre ?></label>
              </div>
            </label>
          </li>
        <?php } ?>
      </ul>
    </div>
    <div class="dropdown checkboxes <?php echo (sizeof($vc_formas_pago)>0)?"actived":"" ?>">
      <div class="select">
        <span>Objetivos</span>
      </div>
      <ul class="dropdown-menu checkboxes">
        <?php $formas_pago = $profesional_model->get_formas_pago();
        foreach($formas_pago as $l) { ?>                                    
          <li>
            <label>
              <div class="custom-control custom-checkbox buscador">
                <input <?php echo (in_array($l->id, $vc_formas_pago))?"checked":"" ?> value="<?php echo $l->id ?>" type="checkbox" class="forma_pago custom-control-input" id="forma_pago_<?php echo $l->id ?>">
                <label class="custom-control-label" for="forma_pago_<?php echo $l->id ?>"><?php echo $l->nombre ?></label>
              </div>
            </label>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <?php if (sizeof($vc_tipos_terapias)>0 || sizeof($vc_tipos_atenciones)>0 || sizeof($vc_categorias)>0 || sizeof($vc_formas_pago)>0) { ?>
    <div class="col">
      <div class="mt15 mb15">
        <label class="dib mr5">Filtros aplicados:</label>
        <?php foreach($tipos_terapias as $tipo_terapia) { 
          if (in_array($tipo_terapia->id, $vc_tipos_terapias)) { ?>
            <a href="javascript:void(0)" class="etiqueta-filtro"><?php echo $tipo_terapia->nombre ?></a>
          <?php } ?>
        <?php } ?>
        <?php foreach($tipos_atenciones as $tipo_atencion) {
          if (in_array($tipo_atencion->id, $vc_tipos_atenciones)) { ?>
            <a href="javascript:void(0)" class="etiqueta-filtro"><?php echo $tipo_atencion->nombre ?></a>
          <?php } ?>
        <?php } ?>
        <?php foreach($categorias as $categoria) { 
          if (in_array($categoria->id, $vc_categorias)) { ?>
            <a href="javascript:void(0)" class="etiqueta-filtro"><?php echo $categoria->nombre ?></a>
          <?php } ?>
        <?php } ?>
        <?php foreach($formas_pago as $forma_pago) { 
          if (in_array($forma_pago->id, $vc_formas_pago)) { ?>
            <a href="javascript:void(0)" class="etiqueta-filtro"><?php echo $forma_pago->nombre ?></a>
          <?php } ?>
        <?php } ?>
      </div>
    </div>
  <?php } ?>
</div>