<script type="text/template" id="mant_configuracion_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n / 
  	<b>Avanzada</b>
	</h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div id="mant_configuracion_comprobantes" class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Configuraci&oacute;n general</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Toneladas mensuales</label>
                    <input type="text" name="mant_toneladas_mensuales" class="form-control" value="<%= mant_toneladas_mensuales %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Horas mensuales</label>
                    <input type="text" name="mant_horas_mensuales" class="form-control" value="<%= mant_horas_mensuales %>"/>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Horario de inicio</label>
                    <input type="text" id="mant_horario_desde" name="mant_horario_desde" class="form-control" value="<%= mant_horario_desde %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Horario de fin</label>
                    <input type="text" id="mant_horario_hasta" name="mant_horario_hasta" class="form-control" value="<%= mant_horario_hasta %>"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- Fin row -->
    <div class="line b-b m-b-lg"></div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
	</div>
</div>
</script>