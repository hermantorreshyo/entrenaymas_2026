<?php include("turnos_css.php"); ?>
<section class="turnos_form_tabs">
  <div class="container">
    <div class="tabs">
      <ul class="tab-button">
        <li class="active"><a class="b-t-main-h active" href="#for-rent">Solicitar un turno</a></li>
      </ul>
      <form onsubmit="return enviar_turno();" class="tab-content" id="for-rent">
        <input type="hidden" id="turno_empresa" value="<?php echo $empresa->id ?>"/>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <input id="turno_nombre" type="text" placeholder="Nombre y Apellido" />
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <input id="turno_email" type="email" placeholder="Email" />
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <input id="turno_telefono" type="tel" placeholder="Tel&eacute;fono" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-3 turnos_form_select_cont">
            <div class="form-group">
              <select onchange="cambiar_calendario()" id="turno_servicio">
                <?php $servicios = $turno_model->get_servicios(); ?>
                <?php if (sizeof($servicios)>1) { ?>
                  <option data-dias="" value="0">Servicio Solicitado</option>
                <?php } ?>
                <?php foreach($servicios as $s) { 
                  $dias = implode("-",$s->dias); ?>
                  <option data-dias="<?php echo $dias ?>" value="<?php echo $s->id ?>"><?php echo $s->nombre ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <input id="turno_fecha" type="text" placeholder="Elija Fecha" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <select id="turno_horario" class="schedule">
                <option value="0">Elija Horario</option>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <input id="turno_submit" class="btn bg-main btn-block" type="submit" value="Solicitar Turno Ahora">
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>