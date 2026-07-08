<script type="text/template" id="farmacias_turnos_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Farmacias de Guardia</h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <div class="panel-body">
      <div id="calendar"></div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="farmacia_turno_template">
<div class="panel panel-default mb0">
    <div class="panel-heading font-bold">
        Turno de Farmacia
        <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
    </div>
    <div class="panel-body">
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-xs-12">
                  <select class="form-control" name="id_farmacia" id="farmacia_turno_farmacias"></select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                  <input type="text" class="form-control" name="fecha" value="<%= fecha %>" id="farmacia_turno_fecha" placeholder="Fecha"/>
                </div>
            </div>            
        </div>
    </div>
    <div class="panel-footer clearfix">
      <% if (id != undefined) { %>
        <button class="eliminar btn btn-danger">Eliminar</button>
      <% } %>
      <button class="btn guardar pull-right btn-success">Guardar</button>
    </div>
</div>
</script>
