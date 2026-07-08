<script type="text/template" id="carreras_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("carreras") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="input-group">
            <input type="text" id="carreras_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <% if (!seleccionar) { %>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#carrera">
              <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
            </a>
          </div>
        <% } %>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="carreras_tabla" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th>Nombre</th>
              <% if (!seleccionar) { %>
                <th class="w120">Acciones</th>
              <% } %>
            </tr>
          </thead>
          <tbody class="tbody"></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="carreras_item_resultados_template">
<% var clase = ""; %>
<td class="<%= clase %> data"><span class="text-info"><%= nombre %></span></td>
<% if (!seleccionar) { %>
  <td class="tar <%= clase %>">
    <div class="btn-group dropdown">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
        <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>        
  </td>
<% } %>
</script>

<script type="text/template" id="carrera_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var materia = control.get("carreras") %>
  <h1 class="m-n font-thin h3"><i class="<%= materia.clase %> icono_principal"></i><%= materia.title %>
    / <b><%= (id == undefined)?"Nuevo":nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">

    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Informaci&oacute;n general</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input type="text" required name="nombre" id="carrera_nombre" value="<%= nombre %>" class="form-control"/>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <b>Materias</b>
          </div>
          <div class="panel-body">
            <div class="padder">
              <div class="m-b row clearfix">
                <div class="form-group col-sm-6">
                  <label class="control-label">Nombre</label>
                  <input type="text" id="carrera_materia_nombre" class="form-control">
                </div>
                <div class="form-group col-sm-2">
                  <label class="control-label">A&ntilde;o</label>
                  <input type="number" min="0" id="carrera_materia_anio" class="form-control">
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">Tipo</label>
                  <div class="input-group">
                    <select id="carrera_materia_cuatrimestre" class="form-control no-model">
                      <option value="0">Anual</option>
                      <option value="1">1º Cuatrimestre</option>
                      <option value="2">2º Cuatrimestre</option>
                    </select>
                    <span class="input-group-btn">
                      <a id="carrera_materia_agregar" class="btn btn-info"><i class="fa fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
              <div class="b-a" style="overflow: auto; max-height: 400px">
                <div class="table-responsive">
                  <table id="carrera_materias_tabla" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th>Nombre</th>
                        <th>A&ntilde;o</th>
                        <th>Tipo</th>
                        <th style="width: 20px">Color</th>
                        <th style="width: 20px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <% for(var i=0;i< materias.length;i++) { %>
                        <% var p = materias[i] %>
                        <tr>
                          <input type="hidden" class="dn id" value="<%= p.id %>"/>
                          <td><input type="text" class="form-control no-model nombre" value="<%= p.nombre %>"/></td>
                          <td><input type="text" class="form-control no-model anio" value="<%= p.anio %>"/></td>
                          <td>
                            <select class="form-control cuatrimestre no-model">
                              <option <%= (p.cuatrimestre == 0)?"selected":"" %> value="0">Anual</option>
                              <option <%= (p.cuatrimestre == 1)?"selected":"" %> value="1">1º Cuatrimestre</option>
                              <option <%= (p.cuatrimestre == 2)?"selected":"" %> value="2">2º Cuatrimestre</option>
                            </select>
                          </td>
                          <td>
                            <input type="color" class="color" value="<%= p.color %>" id="carrera_materia_color" />
                          </td>
                          <td class="tar">
                            <button class="btn btn-sm btn-white eliminar_materia"><i class="fa fa-trash"></i></button>
                          </td>
                        </tr>
                      <% } %>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
        
      </div>
    </div>
    
    <div class="line b-b m-b-lg"></div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8 tar">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>



<script type="text/template" id="clase_edit_panel_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <%= (id == undefined) ? "Nueva Clase":"Editar Clase" %>
  </div>
  <div class="panel-body clearfix">
    <div class="">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Materia</label>
            <select class="form-control" name="id_materia" id="clase_materias"></select>
          </div>
        </div> 
        <div class="col-md-6">
          <% if (mostrar_comisiones) { %>
            <div class="form-group">
              <label class="control-label">Comision</label>
              <select class="form-control" name="id_comision" id="clase_comisiones"></select>
              <input type="hidden" name="id_docente" id="clase_docentes" value="<%= id_docente %>"/>
            </div>
          <% } else if (mostrar_docentes) { %>
            <div class="form-group">
              <label class="control-label">Docente</label>
              <select class="form-control no-model" id="clase_docentes"></select>
              <input type="hidden" name="id_comision" id="clase_comisiones" value="<%= id_comision %>"/>
            </div>
          <% } %>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="col-md-8 p0">
            <div class="form-group">
              <label class="control-label">Fecha</label>
              <div class="input-group">
                <input type="text" name="fecha" class="form-control" id="clase_fecha" value="<%= fecha %>"/>
                <span class="input-group-btn">
                  <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>      
            </div>
          </div>
          <div class="col-md-4 p0">
            <div class="form-group">
              <label class="control-label">&nbsp;</label>
              <input type="text" name="hora" class="form-control" id="clase_hora" value="<%= hora %>"/>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="col-md-6 p0">
            <div class="form-group">
              <label class="control-label">Duracion</label>
              <input type="number" min="0" value="<%= duracion_cantidad %>" class="form-control no-model" id="clase_duracion_cantidad"/>
            </div>
          </div>
          <div class="col-md-6 p0">
            <div class="form-group">
              <label class="control-label">&nbsp;</label>
              <select id="clase_duracion_tipo" class="form-control no-model">
                <option <%= (duracion_tipo=="M")?"selected":"" %> value="M">Minutos</option>
                <option <%= (duracion_tipo=="H")?"selected":"" %> value="H">Horas</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <% if (id == undefined) { %>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Repetir</label>
              <select class="form-control no-model" id="clase_repeticion">
                <option value="0">Clase &uacute;nica</option>
                <option value="1">Cada semana</option>
                <option value="2">Cada 2 semanas</option>
                <option value="4">Cada 1 mes</option>
              </select>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group">
              <label class="control-label">Hasta:</label>
              <div class="input-group">
                <input type="text" class="form-control no-model" placeholder="Hasta" id="clase_fecha_hasta"/>
                <span class="input-group-btn">
                  <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
          </div>
        </div>
      <% } %>
    </div>
  </div>
  <div class="panel-footer clearfix tar">
    <% if (id != undefined) { %><button class="btn btn-danger eliminar fl">Eliminar</button><% } %>
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>