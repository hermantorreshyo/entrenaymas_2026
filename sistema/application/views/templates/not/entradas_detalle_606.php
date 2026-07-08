  <div class="panel panel-default">
    <div class="panel-body">
      <div class="padder">
        <div class="form-group mb0 clearfix">
          <label class="control-label">Cursos</label>
          <a id="expand_capacitaciones" class="expand-link fr">
            <?php echo lang(array(
              "es"=>"+ Ver opciones",
              "en"=>"+ View options",
            )); ?>
          </a>
          <div class="panel-description">
            Informacion relacionada a la capacitaciones y cursos.
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body expand">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a id="link_custom_8" href="#tab_custom8" role="tab" data-toggle="tab">Cuerpo Docente</a></li>
        <li><a id="link_custom_9" href="#tab_custom9" role="tab" data-toggle="tab">Modalidad</a></li>
        <li><a id="link_custom_10" href="#tab_custom10" role="tab" data-toggle="tab">Datos Generales</a></li>
        <li><a id="link_custom_11" href="#tab_custom11" role="tab" data-toggle="tab">Objetivos Generales</a></li>
        <li><a id="link_custom_12" href="#tab_custom12" role="tab" data-toggle="tab">Desarrollo</a></li>
        <li><a id="link_custom_13" href="#tab_custom13" role="tab" data-toggle="tab">Tematica</a></li>
      </ul>
      <div class="tab-content">
        <div id="tab_custom8" class="tab-pane panel-body active">
          <textarea name="custom_8" id="entrada_custom_8"><%= custom_8 %></textarea>
        </div>
        <div id="tab_custom9" class="tab-pane panel-body">
          <textarea name="custom_9" id="entrada_custom_9"><%= custom_9 %></textarea>
        </div>
        <div id="tab_custom10" class="tab-pane panel-body">
          <textarea name="custom_10" id="entrada_custom_10"><%= custom_10 %></textarea>
        </div>
        <div id="tab_custom11" class="tab-pane panel-body">
          <textarea name="custom_11" id="entrada_custom_11"><%= custom_11 %></textarea>
        </div>
        <div id="tab_custom12" class="tab-pane panel-body">
          <textarea name="custom_12" id="entrada_custom_12"><%= custom_12 %></textarea>
        </div>
        <div id="tab_custom13" class="tab-pane panel-body">
          <textarea name="custom_13" id="entrada_custom_13"><%= custom_13 %></textarea>
        </div>
      </div>
    </div>
  </div>
