<script type="text/template" id="mant_estadisticas_template">
<div class="col">
  <div class="bg-light lter b-b wrapper-md">
    <div class="row">
      <div class="col-lg-6 col-sm-4 col-xs-12">
        <h1 class="m-n font-thin h3 text-black">
          <i class="fa fa-bar-chart icono_principal"></i><b>Estad&iacute;sticas</b>
        </h1>
      </div>
      <div class="col-lg-6 col-sm-8 col-xs-12">
        <div class="pull-right">
          <input type="text" id="mant_estadisticas_fecha_desde" value="<%= fecha_desde %>" class="form-control w120 pull-left">
          <button id="fecha_desde_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
          <input type="text" id="mant_estadisticas_fecha_hasta" value="<%= fecha_hasta %>" class="form-control w120 m-l-xs pull-left">
          <button id="fecha_hasta_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="wrapper-md">
    <div class="row">
      <div class="col-md-5">
        <div class="row row-sm text-center">
        <div class="col-xs-6">
          <div class="panel padder-v item bg-info" style="height: 140px">
          <div class="h1 font-thin h1 m-t-md"><%= total_sesiones %></div>
          <span class="text-muted text-md">Mant. Preventivos</span>
          </div>
        </div>
        <div class="col-xs-6">
          <div class="block panel padder-v item bg-success" style="height: 140px">
          <div class="h1 font-thin text-white h1 m-t-md"><%= total_usuarios_nuevos + total_usuarios_recurrentes %></div>
          <span class="text-muted text-md">Mant. Correctivos</span>
          </div>
        </div>
        <div class="col-xs-6">
          <div class="block panel padder-v item" style="height: 140px">
          <span class="font-thin h1 block m-t-md"><%= paginas_vistas %></span>
          <span class="text-muted text-md">% Realizaci&oacute;n</span>
          </div>
        </div>
        <div class="col-xs-6">
          <div class="panel padder-v item" style="height: 140px">
          <div class="font-thin h1 m-t-md"><%= porcentaje_rebote %></div>
          <span class="text-muted text-md">% Retraso</span>
          </div>
        </div>
        </div>
      </div>
      <div class="col-md-7">
        <div class="panel wrapper">
        <h4 class="font-thin m-t-none m-b text-muted">Visi&oacute;n general</h4>
        <div id="vision_general_bar" style="height: 235px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>