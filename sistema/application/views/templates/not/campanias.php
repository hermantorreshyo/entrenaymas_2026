<script type="text/template" id="campanias_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Campa&ntilde;as Publicitarias</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="pull-left pl15">
                <div style="width: 250px; display: inline-block">
                  <select id="campanias_buscar_publicidades" class="100p"></select>
                </div>
                <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
              </div>
              <% if (!seleccionar) { %>
                <div class="pull-right pr15">
                  
                  <a class="btn btn-success nuevo btn-addon ml5" href="javascript:void(0)">
                    <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
                  </a>
                  
                  <div class="btn-group dropdown ml5">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <span>Acciones</span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li><a href="javascript:void" class="eliminar_lote">Exportar Excel</a></li>
                      <li><a href="javascript:void" class="eliminar_lote">Imprimir</a></li>
                    </ul>
                  </div>                  
                  
                </div>
              <% } %>
            </div>
          </div>
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="campanias_tabla" class="table table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="sorting" data-sort-by="publicidad">Publicidad</th>
                      <th class="sorting" data-sort-by="fecha_desde">Desde</th>
                      <th class="sorting" data-sort-by="fecha_hasta">Hasta</th>
                      <th class="sorting" data-sort-by="total_impresiones">Total</th>
                      <th class="sorting" data-sort-by="impresiones_disponibles">Disponible</th>
                      <% if (!seleccionar) { %>
                        <th style="width:150px;text-align:right">Acciones</th>
                      <% } %>
                    </tr>
                  </thead>
                  <tbody class="tbody"><tbody>
                  <tfoot class="pagination_container hide-if-no-paging"></tfoot>
                </table>
              </div>
          </div>
          <!--
          <div class="panel-footer clearfix bg-light lter">
            <button class="btn btn-info enviar btn-addon pull-left"><i class="icon fa fa-send"></i>Enviar</button>
          </div>
          -->
      </div>
  </div>
</script>

<script type="text/template" id="campanias_item_resultados_template">
    <% var clase = (activo == 0)?"text-muted":"" %>
    <td class="<%= clase %> data"><%= publicidad %></td>
    <td class="<%= clase %> data"><%= fecha_desde %></td>
    <td class="<%= clase %> data"><%= fecha_hasta %></td>
    <td class="<%= clase %> data"><%= total_impresiones %></td>
    <td class="<%= clase %> data"><%= impresiones_disponibles %></td>
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <div class="btn-group dropdown">
          <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
          <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
          </ul>
        </div>        
      </td>
    <% } %>
</script>


<script type="text/template" id="campania_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    <% if (id == undefined) { %>
      Nueva Campa&ntilde;a
    <% } else { %>
      Editar Campa&ntilde;a
    <% } %>	       
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-horizontal">                  
      <div class="form-group">
        <div class="col-md-6 col-xs-12">
          <label>Publicidad</label>
          <select id="campania_publicidades" style="width: 100%" class="form-control"></select>
        </div>
        <div class="col-md-6 col-xs-12">
          <label>Total Impresiones</label>
          <input type="text" name="total_impresiones" value="<%= total_impresiones %>" id="campania_total_impresiones" class="form-control"/>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-6 col-xs-12">
          <label>Fecha inicio</label>
          <input type="text" placeholder="Desde" id="campania_fecha_desde" class="form-control"/>
        </div>
        <div class="col-md-6 col-xs-12">
          <label>Fecha vencimiento</label>
          <input type="text" placeholder="Hasta" id="campania_fecha_hasta" class="form-control"/>
        </div>
      </div>
      
      <div class="form-group">
        <div class="col-xs-12">
          <label>Dias de la semana</label>
          <div>
            <div class="checkbox pull-left mr10"><label class="i-checks"><input type="checkbox" value="1" name="lunes" <%= (lunes==1)?"checked":"" %>><i></i>Lunes</label></div>
            <div class="checkbox pull-left mr10"><label class="i-checks"><input type="checkbox" value="1" name="martes" <%= (martes==1)?"checked":"" %>><i></i>Martes</label></div>
            <div class="checkbox pull-left mr10"><label class="i-checks"><input type="checkbox" value="1" name="miercoles" <%= (miercoles==1)?"checked":"" %>><i></i>Miercoles</label></div>
            <div class="checkbox pull-left mr10"><label class="i-checks"><input type="checkbox" value="1" name="jueves" <%= (jueves==1)?"checked":"" %>><i></i>Jueves</label></div>
            <div class="checkbox pull-left mr10"><label class="i-checks"><input type="checkbox" value="1" name="viernes" <%= (viernes==1)?"checked":"" %>><i></i>Viernes</label></div>
            <div class="checkbox pull-left mr10"><label class="i-checks"><input type="checkbox" value="1" name="sabado" <%= (sabado==1)?"checked":"" %>><i></i>Sabado</label></div>
            <div class="checkbox pull-left mr10"><label class="i-checks"><input type="checkbox" value="1" name="domingo" <%= (sabado==1)?"checked":"" %>><i></i>Domingo</label></div>
          </div>
        </div>
      </div>      
      
      <div class="form-group">
        <div class="col-md-3 col-xs-6">
          <label>Horario 1</label>
          <input type="text" placeholder="Desde" id="campania_hora_desde_1" name="hora_desde_1" value="<%= hora_desde_1 %>" class="form-control"/>
        </div>
        <div class="col-md-3 col-xs-6">
          <label>&nbsp;</label>
          <input type="text" placeholder="Hasta" id="campania_hora_hasta_1" name="hora_hasta_1" value="<%= hora_hasta_1 %>" class="form-control"/>
        </div>
        <div class="col-md-3 col-xs-6">
          <label>Horario 2</label>
          <input type="text" placeholder="Desde" id="campania_hora_desde_2" name="hora_desde_2" value="<%= hora_desde_2 %>" class="form-control"/>
        </div>
        <div class="col-md-3 col-xs-6">
          <label>&nbsp;</label>
          <input type="text" placeholder="Hasta" id="campania_hora_hasta_2" name="hora_hasta_2" value="<%= hora_hasta_2 %>" class="form-control"/>
        </div>
      </div>      
      <div class="form-group">
        <div class="col-md-3 col-xs-6">
          <label>Horario 3</label>
          <input type="text" placeholder="Desde" id="campania_hora_desde_3" name="hora_desde_3" value="<%= hora_desde_3 %>" class="form-control"/>
        </div>
        <div class="col-md-3 col-xs-6">
          <label>&nbsp;</label>
          <input type="text" placeholder="Hasta" id="campania_hora_hasta_3" name="hora_hasta_3" value="<%= hora_hasta_3 %>" class="form-control"/>
        </div>
        <div class="col-md-3 col-xs-6">
          <label>Horario 4</label>
          <input type="text" placeholder="Desde" id="campania_hora_desde_4" name="hora_desde_4" value="<%= hora_desde_4 %>" class="form-control"/>
        </div>
        <div class="col-md-3 col-xs-6">
          <label>&nbsp;</label>
          <input type="text" placeholder="Hasta" id="campania_hora_hasta_4" name="hora_hasta_4" value="<%= hora_hasta_4 %>" class="form-control"/>
        </div>
      </div>      
      
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success">Guardar</button>
  </div>  
</div>
     
</script>