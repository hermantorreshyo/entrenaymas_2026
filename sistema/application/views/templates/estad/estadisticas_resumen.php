<script type="text/template" id="estadisticas_resumen_template">
  <div class="col">
    <div class="bg-light lter b-b wrapper-md">
      <div class="row">
        <div class="col-lg-6 col-sm-4 col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Resumen</b>
          </h1>
        </div>
        <div class="col-lg-6 col-sm-8 col-xs-12">
          <div class="pull-right">
            <input type="text" id="estadisticas_resumen_fecha_desde" class="form-control w120 pull-left">
            <button id="fecha_desde_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
            <input type="text" id="estadisticas_resumen_fecha_hasta" class="form-control w120 m-l-xs pull-left">
            <button id="fecha_hasta_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i> Buscar</button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>
    </div>
    <div class="wrapper-md">
      <div class="panel">
        <div class="panel-body">
          <div id="estadisticas_resumen_grafico" class="grafico" style="height: 235px;"></div>
        </div>
      </div>
      <div class="panel">
        <div class="panel-body">
          <div class="b-a" style="overflow: auto">
            <table class="table table-small table-striped m-b-none default footable" id="estadisticas_resumen_tabla">
              <thead></thead>  
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>