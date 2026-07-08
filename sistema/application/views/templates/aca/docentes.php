<script type="text/template" id="docentes_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("docentes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a>
          <i class="fa text-warning fa-list m-r-xs"></i>
          Listado
        </a>
      </li>
      <li>
        <a href="app/#asistencias_docentes">
          <i class="fa text-info fa-address-book m-r-xs"></i>
          Asistencias
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane panel-body active">
        <div class="form-inline">
          <div class="row m-b oh">
            <div class="col-md-6 col-lg-3 sm-m-b">
              <div class="input-group">
                <input type="text" id="docentes_buscar" value="<%= window.docentes_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default"><i class="fa fa-search"></i></button>
                </span>
              </div>              
            </div>
            <% if (!seleccionar) { %>
              <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                <a class="btn btn-info btn-addon" href="app/#docente">
                  <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
                </a>
              </div>
            <% } %>
          </div>
          <div class="b-a table-responsive">
            <table id="docentes_table" class="table table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th class="w50 tac hidden-xs"></th>
                  <th class="sorting" data-sort-by="nombre">Nombre</th>
                  <th>Email</th>
                  <th>Teléfono</th>
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
  </div>
</div>    
</script>


<script type="text/template" id="docentes_item">
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %> pull-left">
        <%= isEmpty(nombre) ? email.substr(0,1) : nombre.substr(0,1) %>
      </span>
    <% } %>
  </td>
	<td class='ver'><span class='<%= (activo==1)?"text-info":"" %>'><%= nombre %></span></td>
	<td class='ver'><span><%= email %></span></td>
  <td class='ver'><span><%= telefono %></span></td>
	<% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <a href="app/#docente/<%= id %>"><i class="fa fa-pencil iconito active"></i></a>
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>        
      <div class="btn-group dropdown ml10">
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

<script type="text/template" id="docentes_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("docentes") %>
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
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" id="docente_nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" id="docente_apellido" value="<%= apellido %>"/>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Email</label>
                <input type="text" name="email" class="form-control" id="docente_email" value="<%= email %>"/>
              </div>  

              <div class="form-group">
                <label class="control-label">Departamento</label>
                <select id="docente_departamentos" name="id_departamento" class="form-control"></select>
              </div>        

              <div class="form-group mb0 tar">
                <a class="expand-link">
                  <?php echo lang(array(
                    "es"=>"+ M&aacute;s opciones",
                    "en"=>"+ More options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">DNI</label>
                    <input type="text" name="cuit" class="form-control" id="docente_cuit" value="<%= cuit %>"/>
                  </div>                  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha de nacimiento</label>
                    <div class="input-group">
                      <input type="text" name="fecha_nac" class="form-control" id="docente_fecha_nac" value="<%= fecha_nac %>"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>                  
                </div>
              </div>

              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>"Foto",
                "url"=>"docentes/function/save_image/",
                "width"=>(isset($empresa->config["docente_image_width"]) ? $empresa->config["docente_image_width"] : 400),
                "height"=>(isset($empresa->config["docente_image_height"]) ? $empresa->config["docente_image_height"] : 400),
                "quality"=>(isset($empresa->config["docente_image_quality"]) ? $empresa->config["docente_image_quality"] : 0),
                "thumbnail_width"=>(isset($empresa->config["docente_thumbnail_width"]) ? $empresa->config["docente_thumbnail_width"] : 0),
                "thumbnail_height"=>(isset($empresa->config["docente_thumbnail_height"]) ? $empresa->config["docente_thumbnail_height"] : 0),
              )); ?>
                
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Informaci&oacute;n de contacto",
                    "en"=>"Contact information",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Tel&eacute;fonos, direcciones, y dem&aacute;s informaci&oacute;n de contacto.",
                    "en"=>"Tel&eacute;fonos, direcciones, y dem&aacute;s informaci&oacute;n de contacto.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Localidad</label>
                    <input type="text" value="<%= localidad %>" id="docente_localidad" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control"/>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Direccion </label>
                    <input type="text" name="direccion" class="form-control" id="docente_direccion" value="<%= direccion %>"/>
                  </div>  
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tel&eacute;fono </label>
                    <input type="text" name="telefono" class="form-control" id="docente_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Celular </label>
                    <input type="text" name="celular" class="form-control" id="docente_celular" value="<%= celular %>"/>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Informaci&oacute;n profesional y laboral",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Datos acad&eacute;micos y laborales del profesional.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <div class="form-group">
                <label class="control-label">T&iacute;tulos y especializaciones</label>
                <textarea id="docente_titulo" name="titulo" class="form-control"><%= titulo %></textarea>
              </div>  

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha de ingreso</label>
                    <div class="input-group">
                      <input type="text" name="fecha_ingreso" class="form-control" id="docente_fecha_ingreso" value="<%= fecha_ingreso %>"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>                  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha de egreso</label>
                    <div class="input-group">
                      <input type="text" name="fecha_egreso" class="form-control" id="docente_fecha_egreso" value="<%= fecha_egreso %>"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>                  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Banco</label>
                    <input type="text" value="<%= banco %>" id="docente_banco" name="banco" class="form-control"/>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">N&uacute;mero de cuenta </label>
                    <input type="text" name="cuenta_bancaria" class="form-control" id="docente_cuenta_bancaria" value="<%= cuenta_bancaria %>"/>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Obra social / Prepaga</label>
                    <input type="text" value="<%= obra_social %>" id="docente_obra_social" name="obra_social" class="form-control"/>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">N&uacute;mero de obra social </label>
                    <input type="text" name="numero_obra_social" class="form-control" id="docente_numero_obra_social" value="<%= numero_obra_social %>"/>
                  </div>  
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Contrase&ntilde;a",
                    "en"=>"Password",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"Cambiar contrase&ntilde;a",
                    "en"=>"Change password",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Clave utilizada para ingresar al sistema.",
                    "en"=>"Agregar variantes a productos como talle, color, etc.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Contrase&ntilde;a</label>
                    <input type="password" class="form-control" id="docente_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
                  </div>                  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Repetir contrase&ntilde;a</label>
                    <input type="password" class="form-control" id="docente_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
                  </div>                  
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


<script type="text/template" id="asistencias_docentes_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("docentes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / Asistencias / <b>Cargar</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li>
        <a>
          <i class="fa text-warning fa-list m-r-xs"></i>
          Listado
        </a>
      </li>
      <li class="active">
        <a href="javascript:void(0)" role="tab" data-toggle="tab">
          <i class="fa text-info fa-address-book m-r-xs"></i>
          Asistencias
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane panel-body active">
        <div class="form-inline m-b">
          <a class="btn btn-default m-r-xs" href="app/#asistencias_docentes"><i class="fa fa-reply m-r-xs"></i>Volver</a>
          <% if (ASISTENCIA_DOCENTE_POR_MATERIA == 1) { %>
            <div class="form-group dib w200">
              <select id="asistencias_docentes_buscar_materias" class="w100p form-control">
                <option value="0">Seleccionar materia</option>
              </select>
            </div>
          <% } %>
          <div class="form-group dib w150">
            <div class="input-group">
              <input type="text" id="asistencias_docentes_buscar_fecha" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>

          <div class="fr">
            <button class="btn btn-default imprimir"><i class="fa fa-print m-r-xs"></i> Imprimir</button>
            <button class="btn btn-success guardar">&nbsp;&nbsp;&nbsp;Guardar&nbsp;&nbsp;&nbsp;</button>
          </div>

        </div>
        <div class="b-a table-responsive">
          <table id="asistencias_docentes_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="hidden-xs" style="width: 20px;">#</th>
                <th class="hidden-xs" style="width: 20px;"></th>
                <th>Docente</th>
                <th style="min-width: 400px; max-width: 400px; width: 400px">Asistencia</th>
                <th style="width: 300px;">Observaciones</th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
        <div class="panel-footer clearfix tar">
          <div class="fl">
            <span>Asistencias:</span>
            <b id="asistencias_docentes_asistencia"></b>
            <span class="m-l">Inasistencias:</span>
            <b id="asistencias_docentes_inasistencia"></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>    
</script>

<script type="text/template" id="asistencias_docentes_item_template">
  <input type="hidden" class="id_clase" value="<%= id_clase %>">
  <input type="hidden" class="fecha" value="<%= fecha %>">
  <input type="hidden" class="id_docente" value="<%= id_docente %>">
  <td class="ver hidden-xs"><%= numero %></td>
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto bg-info %> pull-left">
        <%= isEmpty(nombre) ? "" : nombre.toUpperCase().substr(0,1) %>
      </span>
    <% } %>
  </td>
  <td class="ver"><span class='text-info'><%= nombre.ucwords() %></span></td>
  <td>
    <div class="btn-group">
      <label data-valor="P" class="btn <%= (condicion=='P')?'active btn-success':'btn-default' %> condicion">Presente</label>
      <label data-valor="T" class="btn <%= (condicion=='T')?'active btn-warning':'btn-default' %> condicion">Tarde</label>
      <label data-valor="A" class="btn <%= (condicion=='A')?'active btn-danger':'btn-default' %> condicion">Ausente</label>
      <label data-valor="J" class="btn <%= (condicion=='J')?'active btn-primary':'btn-default' %> condicion">Aus. c/ justificacion</label>
    </div>
  </td>
  <td>
    <input type="text" value="<%= observaciones %>" class="form-control observaciones no-model">
  </td>
</script>

<script type="text/template" id="asistencias_docentes_reporte_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("docentes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <b>Asistencias</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li>
        <a href="app/#docentes">
          <i class="fa text-warning fa-list m-r-xs"></i>
          Listado
        </a>
      </li>
      <li class="active">
        <a href="javascript:void(0)" class="buscar_todos" role="tab" data-toggle="tab">
          <i class="fa text-info fa-address-book m-r-xs"></i>
          Asistencias
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane panel-body active">
        <div class="form-inline m-b">
          <% if (ASISTENCIA_DOCENTE_POR_MATERIA == 1) { %>
            <div class="form-group dib w200">
              <select id="asistencias_docentes_reporte_materias" class="w100p form-control">
                <option value="0">Seleccionar materia</option>
              </select>
            </div>
          <% } %>
          <div class="form-group dib w180">
            <select class="trimestre_select form-control w100p no-model">
              <% for(var i=0;i< trimestres.length; i++) { %>
                <% var t = trimestres[i] %>
                <% var selected = moment().isBetween(moment(t.fecha_desde,"DD/MM/YYYY"), moment(t.fecha_hasta,"DD/MM/YYYY"));%>
                <option <%= (selected)?"selected":"" %> value="<%= t-id %>" data-desde="<%= t.fecha_desde %>" data-hasta="<%= t.fecha_hasta %>"><%= t.nombre %></option>
              <% } %>
              <option data-desde="<%= moment().subtract(3,'months').format('DD/MM/YYYY') %>" data-hasta="<%= moment().format('DD/MM/YYYY') %>" value="0">Rango de fechas</option>
            </select>
          </div>
          <div class="form-group dib w150">
            <div class="input-group">
              <input placeholder="Desde" value="<%= fecha_desde %>" type="text" id="asistencias_docentes_reporte_fecha_desde" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
          <div class="form-group dib w150">
            <div class="input-group">
              <input placeholder="Hasta" value="<%= fecha_hasta %>" type="text" id="asistencias_docentes_reporte_fecha_hasta" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>

          <div class="fr">
            <button class="btn m-r-xs btn-default imprimir"><i class="fa fa-print m-r-xs"></i> Imprimir</button>
            <a class="btn btn-info btn-addon" href="app/#asistencia_docente">
              <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Cargar Asistencia&nbsp;&nbsp;</span>
            </a>
          </div>
        </div>
        <div id="asistencias_docentes_reporte_tabla"></div>
        <?php /*
        <div class="panel-footer clearfix tar">
          <div class="fl">
            <span>Asistencias:</span>
            <b id="asistencias_docentes_asistencia"></b>
            <span class="m-l">Inasistencias_docentes:</span>
            <b id="asistencias_docentes_inasistencia"></b>
          </div>
        </div>
        */ ?>
      </div>
    </div>
  </div>
</div>    
</script>

<script type="text/template" id="asistencias_docentes_reporte_tabla_template">
<div class="">
  <div class="col-xs-4 col-sm-3 p0">
    <div class="b-a oh">
      <table id="reporte_asistencias_docentes_table_nombres" class="table table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th class="hidden-xs" style="width: 20px;">#</th>
            <th class="hidden-xs" style="width: 20px;"></th>
            <th style="min-width: 180px;">Alumno</th>
          </tr>
        </thead>
        <tbody class="tbody"></tbody>
      </table>
    </div>
  </div>
  <div class="col-xs-8 col-sm-9 p0">
    <div class="b-a table-responsive">
      <table id="reporte_asistencias_docentes_table" class="table table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <% for(i=0;i< clases.length;i++) { %>
              <% var c = clases[i] %>
              <th style="min-width: 75px; padding-left: 0px; padding-right: 0px; text-align: center;">
                <input type="checkbox" id="check_<%= i %>" class="check_fecha m0" value="<%= moment(c.fecha).format('DD/MM') %>" /> 
                <label for="check_<%= i %>" class="fs14 bold mb0 cp"><%= moment(c.fecha).format("DD/MM") %></label>
              </th>
            <% } %>
          </tr>
        </thead>
        <tbody class="tbody"></tbody>
      </table>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="asistencias_docentes_reporte_item_template">
  <% for(i=0;i< clases.length;i++) { %>
    <% var as = clases[i] %>
    <% var color = "" %>
    <% if (as.condicion=='P') { color = "bg-success"; } %>
    <% if (as.condicion=='T') { color = "bg-warning"; } %>
    <% if (as.condicion=='A') { color = "bg-danger"; } %>
    <% if (as.condicion=='J') { color = "bg-primary"; } %>
    <td class="<%= color %> tac" style="padding: 0px">
      <%= as.condicion %>
      <%= (isEmpty(as.observaciones)) ? "" : "<i data-toggle='tooltip' title='"+as.observaciones+"' class='fa blanco fa-commenting'></i>" %>
    </td>
  <% } %>
</script>
