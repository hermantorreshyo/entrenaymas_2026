<script type="text/template" id="comisiones_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("comisiones") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    
    <div class="panel-heading oh">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="input-group">
            <input type="text" id="comisiones_buscar" value="<%= window.comisiones_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn pull-right btn-info btn-addon" href="app/#comision">
            <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nueva Comisión&nbsp;&nbsp;</span>
          </a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="comisiones_table" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th class="sorting" data-sort-by="nombre">Nombre</th>
              <th class="sorting" data-sort-by="turno">Turno</th>
              <th class="sorting tac" data-sort-by="cantidad_alumnos">Cant. de Alumnos</th>
              <th></th>
              <th></th>
              <th></th>
              <% if (permiso > 1) { %>
                <th class="th_acciones w120">Acciones</th>
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


<script type="text/template" id="comisiones_item">
  <td class='ver'><span class="text-info"><%= nombre %></span></td>
  <td class='ver'><%= turno %></td>
  <td class='ver tac'><%= cantidad_alumnos %></td>
  <td class="tac">
    <a href="app/#comision_calendario/<%= id %>" style="color: inherit;">
      <i class="fa text-warning fa-calendar m-r-xs"></i>
      Cronograma
    </a>
  </td>
  <td class="tac">
    <a href="app/#asistencias/<%= id %>" style="color: inherit;">
      <i class="fa text-info fa-address-book m-r-xs"></i>
      Asistencias
    </a>
  </td>
  <td class="tac">
    <a href="app/#examenes/<%= id %>" style="color: inherit;">
      <i class="fa text-success fa-file-text m-r-xs"></i>
      Exámenes
    </a>
  </td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones tar">
      <i class="fa fa-pencil iconito active"></i>
      <i class="fa fa-paper-plane iconito success enviar active"></i>
      <div class="btn-group dropdown ml10 mr10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>    
    </td>
  <% } %>
</script>

<script type="text/template" id="comisiones_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("comisiones") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
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
                <input type="text" required name="nombre" id="comision_nombre" value="<%= nombre %>" class="form-control"/>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Plan de estudio</label>
                    <select class="form-control" style="width: 100%" name="id_carrera" id="comision_carreras"></select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">A&ntilde;o</label>
                    <select class="form-control" name="anio" id="comision_anio">
                      <option <%= (anio=="1")?"selected":"" %>>1</option>
                      <option <%= (anio=="2")?"selected":"" %>>2</option>
                      <option <%= (anio=="3")?"selected":"" %>>3</option>
                      <option <%= (anio=="4")?"selected":"" %>>4</option>
                      <option <%= (anio=="5")?"selected":"" %>>5</option>
                      <option <%= (anio=="6")?"selected":"" %>>6</option>
                      <option <%= (anio=="7")?"selected":"" %>>7</option>
                      <option <%= (anio=="8")?"selected":"" %>>8</option>
                      <option <%= (anio=="9")?"selected":"" %>>9</option>
                      <option <%= (anio=="10")?"selected":"" %>>10</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Turno</label>
                    <select class="form-control" name="turno" id="comision_turno">
                      <option <%= (turno=="Mañana")?"selected":"" %>>Mañana</option>
                      <option <%= (turno=="Tarde")?"selected":"" %>>Tarde</option>
                      <option <%= (turno=="Noche")?"selected":"" %>>Noche</option>
                      <option <%= (turno=="Doble Turno")?"selected":"" %>>Doble Turno</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <b>Alumnos de la comision</b>
          </div>
          <div class="panel-body">
            <div class="padder">
              <div class="clearfix">
                <input type="hidden" id="comision_alumno_id" value="0"/>
                <input type="hidden" id="comision_alumno_numero_legajo" class="dn">
                <input type="hidden" id="comision_alumno_cuit" class="dn">
                <div class="form-group">
                  <label class="control-label">Buscar</label>
                  <div class="input-group">
                    <input type="text" id="comision_alumno_nombre" class="form-control">
                    <span class="input-group-btn">
                      <a id="comision_alumno_agregar" class="btn btn-info"><i class="fa fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
              <div class="b-a" style="overflow: auto; max-height: 400px">
                <div class="table-responsive">
                  <table id="comision_alumnos_tabla" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th>Alumno</th>
                        <th>Legajo</th>
                        <th>DNI</th>
                        <th style="width: 20px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <% for(var i=0;i< alumnos.length;i++) { %>
                        <% var p = alumnos[i] %>
                        <tr>
                          <input type="hidden" class="dn id" value="<%= p.id %>"/>
                          <td><span class="text-info"><%= p.nombre %></span></td>
                          <td><%= p.numero_legajo %></td>
                          <td><%= p.cuit %></td>
                          <td class="tar">
                            <button class="btn btn-sm btn-white eliminar_alumno"><i class="fa fa-trash"></i></button>
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
        <% if (id !== undefined) { %>
          <a href="app/#comision_calendario/<%= id %>" class="btn btn-default">Ver clases</a>
        <% } %>
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="comisiones_calendario_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("comisiones") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <%= nombre %> / <b>Clases</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a href="javascript:void(0)" class="buscar_todos" role="tab" data-toggle="tab">
          <i class="fa text-warning fa-calendar m-r-xs"></i>
          Cronograma
        </a>
      </li>
      <li>
        <a href="app/#asistencias/<%= id %>">
          <i class="fa text-info fa-address-book m-r-xs"></i>
          Asistencias
        </a>
      </li>
      <li>
        <a href="app/#examenes/<%= id %>">
          <i class="fa text-success fa-file-text m-r-xs"></i>
          Exámenes
        </a>
      </li>
      <li>
        <a href="app/#comisiones">
          <i class="fa text-danger fa-share m-r-xs"></i>
          Volver a comisiones
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane panel-body active">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>
</script>