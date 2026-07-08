<script type="text/template" id="libros_prestamos_resultados_template">
<% if (!seleccionar) { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("menu_biblioteca") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
      / <b>Pr&eacute;stamos</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
<% } %>
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="input-group">
            <input type="text" id="libros_prestamos_buscar" placeholder="Buscar..." value="" autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <% if (!seleccionar) { %>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <div class="btn-group dropdown ml5">
              <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span>Acciones</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right">
                <li><a href="javascript:void" class="devolver_lote">Devolver</a></li>
                <li><a href="javascript:void" class="eliminar_lote">Eliminar</a></li>
              </ul>
            </div>
            <a class="btn btn-info nuevo btn-addon ml5" href="javascript:void(0)">
              <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
            </a>
          </div>
        <% } %>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="libros_prestamos_tabla" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;">
                <label class="i-checks m-b-none">
                  <input class="esc sel_todos" type="checkbox"><i></i>
                </label>
              </th>
              <th class="sorting" data-sort-by="alumno">Alumno</th>
              <th class="sorting" data-sort-by="libro">Libro</th>
              <th class="sorting" data-sort-by="fecha_desde">Fecha Prestamo</th>
              <th class="sorting" data-sort-by="fecha_hasta">Fecha Venc.</th>
              <th class="sorting" data-sort-by="fecha_hasta">Dias atraso</th>
              <th></th>
              <th style="width:100px;text-align:right">Acciones</th>
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
<% if (!seleccionar) { %></div><% } %>
</script>

<script type="text/template" id="libros_prestamos_item_resultados_template">
    <% var clase = (devuelto==0)? ((dias_atraso>0)?"text-danger":"") :"text-muted" %>
    <td>
      <% if (devuelto==0) { %>
        <label class="i-checks m-b-none">
            <input class="esc check-row" value="<%= id %>" data-libro="<%= libro %>" type="checkbox"><i></i>
        </label>
      <% } %>
    </td>    
    <td class="<%= clase %> data"><%= alumno %></td>
    <td class="<%= clase %> data"><%= libro %> <%= (!isEmpty(autor))?"("+autor+")":"" %></td>
    <td class="<%= clase %> data"><%= fecha_desde %></td>
    <td class="<%= clase %> data"><%= fecha_hasta %></td>
    <td class="<%= clase %> data"><%= (devuelto==1) ? "Devuelto el "+fecha_devuelto : ((dias_atraso > 0) ? (dias_atraso+" dias de atraso") : "") %></td>
    <td class="tac">
      <a href="javascript:void" class="devolucion" style="color: inherit;">
        <i class="fa text-info fa-address-book m-r-xs"></i>
        Devolucion
      </a>
    </td>
    <td class="tar <%= clase %>">
      <a href="app/#alumno_acciones/<%= id_alumno %>"><i class="fa fa-paper-plane iconito success ver active"></i></a>
      <div class="btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="enviar_email" data-id="<%= id %>">Enviar Email</a></li>
          <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>        
    </td>
</script>


<script type="text/template" id="libro_prestamo_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    <% if (id == undefined) { %>
      Nuevo Pr&eacute;stamo
    <% } else { %>
      Editar Pr&eacute;stamo
    <% } %>	       
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-horizontal">
      <% if (id == undefined) { %>                  
        <div class="form-group">
          <div class="col-xs-12">
            <input type="text" id="libro_prestamo_alumnos" placeholder="Nombre o identificacion del alumno" style="width: 100%" class="form-control" />
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input type="text" id="libro_prestamo_libros" placeholder="Buscar por titulo del libro o ISBN" style="width: 100%" class="form-control" />
          </div>
        </div>
      <% } else { %>
        <div class="form-group">
          <div class="col-xs-12">
            <span class="dib w100">Alumno: </span><%= alumno %>
          </div>
        </div>
      <% } %>
      <div id="libro_prestamo_libros_cont" class="mb30"></div>
      <div class="form-group">
        <div class="col-md-6 col-xs-12">
          <label>Fecha pr&eacute;stamo</label>
          <input type="text" placeholder="Desde" id="libro_prestamo_fecha_desde" class="form-control"/>
        </div>
        <div class="col-md-6 col-xs-12">
          <label>Vencimiento</label>
          <input type="text" placeholder="Hasta" id="libro_prestamo_fecha_hasta" class="form-control"/>
        </div>
      </div>      
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success">Guardar</button>
  </div>  
</div>
     
</script>




<script type="text/template" id="libro_prestamo_devolucion_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    Devoluci&oacute;n de Libros
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-horizontal">
      <div class="form-group">
        <div class="col-xs-12">
          <span class="dib w100">Alumno: </span><%= alumno %>
        </div>
      </div>
      <div class="form-group">
        <div class="col-xs-12">
          <span class="dib w100">Libro: </span><%= libro %>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-6 col-xs-12">
          <label>Fecha de devolucion:</label>
          <input type="text" placeholder="Desde" id="libro_prestamo_devolucion_fecha_devolucion" class="form-control"/>
        </div>
      </div>      
      <div class="form-group">
        <div class="col-xs-12">
          <label>Observaciones: </label>
          <textarea name="observaciones" placeholder="Escribe aqui alguna observacion..." id="libro_prestamo_devolucion_observaciones" class="h100 form-control"><%= observaciones %></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <% if (devuelto == 1) { %>
      <button class="btn cancelar_devolucion pull-left btn-danger">Cancelar devoluci&oacute;n</button>
    <% } %>
    <button class="btn guardar pull-right btn-success">Guardar</button>
  </div>  
</div>
     
</script>


<script type="text/template" id="libro_prestamo_devolucion_masiva_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    Devoluci&oacute;n de Libros
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-horizontal">
      <div id="libro_prestamo_libros_cont" class="mb30"></div>
      <div class="form-group">
        <div class="col-md-6 col-xs-12">
          <label>Fecha de devolucion:</label>
          <input type="text" placeholder="Desde" id="libro_prestamo_devolucion_fecha_devolucion" class="form-control"/>
        </div>
      </div>      
      <div class="form-group">
        <div class="col-xs-12">
          <label>Observaciones: </label>
          <textarea name="observaciones" placeholder="Escribe aqui alguna observacion..." id="libro_prestamo_devolucion_observaciones" class="h100 form-control"></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success">Guardar</button>
  </div>  
</div>
</script>

