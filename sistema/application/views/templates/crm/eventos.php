<script type="text/template" id="eventos_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Calendario</h1>
</div>
<div class="wrapper-md ng-scope">
    <div id="calendar"></div>
</div>
</script>

<script type="text/template" id="eventos_item_resultados_template">
    <td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" data-id="<%= id %>" /></td>
</script>


<script type="text/template" id="evento_template">
<div class="panel panel-default mb0">
    <div class="panel-heading font-bold">
        Evento
        <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
    </div>
    <div class="panel-body">
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-xs-12">
                    <input type="text" name="nombre" id="evento_nombre" placeholder="Nombre: " value="<%= nombre %>" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <texevento name="observaciones" placeholder="Observaciones o comentarios sobre esta evento" id="evento_observaciones"><%= observaciones %></texevento>
                </div>
            </div>            
        </div>
    </div>
    <div class="panel-footer clearfix">
        <button class="borrar_evento btn btn-danger">Eliminar</button>
        <button class="btn guardar pull-right btn-success">Guardar</button>
    </div>
</div>
</script>
