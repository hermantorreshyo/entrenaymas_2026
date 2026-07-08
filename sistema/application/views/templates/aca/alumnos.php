<script type="text/template" id="alumnos_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("alumnos") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
</div>
<div class="wrapper-md ng-scope">
	<div class="panel panel-default">
	  
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="input-group">
            <input type="text" id="alumnos_buscar" value="<%= window.alumnos_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default"><i class="fa fa-search"></i></button>
            </span>
            <span class="input-group-btn">
              <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
            </span>
          </div>
        </div>
        <% if (!seleccionar) { %>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#alumno">
              <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
            </a>
          </div>
        <% } %>
      </div>
    </div>
    <div class="advanced-search-div bg-light dk" style="display:none">
      <div class="wrapper oh">
        <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
        <div class="form-inline">
          <div style="width: 250px; display: inline-block">
            <select id="alumnos_buscar_comisiones" style="width: 100%"></select>
          </div>
          <div class="form-group">
            <button id="alumnos_buscar_avanzada_btn" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button>
          </div>
        </div>
      </div>
    </div>
	  <div class="panel-body">
		  <div class="b-a table-responsive">
			  <table id="alumnos_table" class="table table-striped sortable m-b-none default footable">
				  <thead>
					  <tr>
              <th class="w50 tac hidden-xs"></th>
						  <th class="sorting" data-sort-by="nombre">Nombre</th>
						  <th class="sorting" data-sort-by="email">Email</th>
						  <th class="sorting" data-sort-by="numero_legajo">Legajo</th>
              <th class="sorting" data-sort-by="comision">Comision / Curso</th>
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

<script type="text/template" id="alumnos_item">
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %> pull-left">
        <%= isEmpty(nombre) ? email.substr(0,1) : nombre.substr(0,1) %>
      </span>
    <% } %>
  </td>
	<td class="ver"><span class='<%= (activo==1)?"text-info":"" %>'><%= nombre %></span></td>
	<td class='ver'><span><%= email %></span></td>
	<td class='ver'><span><%= numero_legajo %></span></td>
  <td><a href="app/#comision_calendario/<%= id_comision %>"><span class="label bg-success"><%= comision %></span></a></td>
	<% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <a href="app/#alumno/<%= id %>"><i class="fa fa-pencil iconito active"></i></a>
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

<script type="text/template" id="alumnos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("alumnos") %>
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
                    <input type="text" name="nombre" class="form-control" id="alumno_nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" id="alumno_apellido" value="<%= apellido %>"/>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Email</label>
                <input type="text" name="email" class="form-control" id="alumno_email" value="<%= email %>"/>
              </div>  

              <div class="form-group">
                <label class="control-label">Comision / Curso</label>
                <select id="alumno_comisiones" name="id_comision" class="form-control"></select>
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
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Sexo</label>
                    <select name="sexo" class="form-control" id="alumno_sexo">
                      <option <%= (sexo=='M')?"selected":"" %> value="M">Masculino</option>
                      <option <%= (sexo=='F')?"selected":"" %> value="F">Femenino</option>
                    </select>
                  </div>                  
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">DNI</label>
                    <input type="text" name="cuit" class="form-control" id="alumno_cuit" value="<%= cuit %>"/>
                  </div>                  
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Fecha de nacimiento</label>
                    <div class="input-group">
                      <input type="text" name="fecha_nac" class="form-control" id="alumno_fecha_nac" value="<%= fecha_nac %>"/>
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
                "url"=>"alumnos/function/save_image/",
                "width"=>(isset($empresa->config["alumno_image_width"]) ? $empresa->config["alumno_image_width"] : 400),
                "height"=>(isset($empresa->config["alumno_image_height"]) ? $empresa->config["alumno_image_height"] : 400),
                "quality"=>(isset($empresa->config["alumno_image_quality"]) ? $empresa->config["alumno_image_quality"] : 0),
                "thumbnail_width"=>(isset($empresa->config["alumno_thumbnail_width"]) ? $empresa->config["alumno_thumbnail_width"] : 0),
                "thumbnail_height"=>(isset($empresa->config["alumno_thumbnail_height"]) ? $empresa->config["alumno_thumbnail_height"] : 0),
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

              <div class="form-group">
                <label class="control-label">Tutor</label>
                <div class="input-group">
                  <select id="alumno_tutores" style="width: 100%" class="form-control"></select>
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-success nuevo_tutor">Nuevo</button>
                  </div>
                </div>                    
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Localidad</label>
                    <input type="text" value="<%= localidad %>" id="alumno_localidad" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control"/>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Direccion </label>
                    <input type="text" name="direccion" class="form-control" id="alumno_direccion" value="<%= direccion %>"/>
                  </div>  
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tel&eacute;fono </label>
                    <input type="text" name="telefono" class="form-control" id="alumno_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Celular </label>
                    <input type="text" name="celular" class="form-control" id="alumno_celular" value="<%= celular %>"/>
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
                    "es"=>"Informaci&oacute;n acad&eacute;mica",
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
                    "es"=>"Datos referidos a la parte acad&eacute;mica e institucional.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">N&uacute;mero de legajo </label>
                    <input type="text" name="numero_legajo" class="form-control" id="alumno_numero_legajo" value="<%= numero_legajo %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Fecha de ingreso</label>
                    <div class="input-group">
                      <input type="text" name="fecha_ingreso" class="form-control" id="alumno_fecha_ingreso" value="<%= fecha_ingreso %>"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>                  
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Fecha de egreso</label>
                    <div class="input-group">
                      <input type="text" name="fecha_egreso" class="form-control" id="alumno_fecha_egreso" value="<%= fecha_egreso %>"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
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
                    "es"=>"Informaci&oacute;n sobre salud",
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
                    "es"=>"Datos sobre patologias, enfermedades, etc. del alumno.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Patologias</label>
                <input type="text" name="patologia" id="alumno_patologia" value="<%= patologia %>" class="form-control"/>
              </div>                
              <div class="form-group">
                <label class="control-label">Alergias</label>
                <input type="text" name="alergia" id="alumno_alergia" value="<%= alergia %>" class="form-control"/>
              </div>                
              <div class="form-group">
                <label class="control-label">Medicacion</label>
                <input type="text" name="medicacion" id="alumno_medicacion" value="<%= medicacion %>" class="form-control"/>
              </div> 
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Obra social / Prepaga</label>
                    <input type="text" value="<%= obra_social %>" id="alumno_obra_social" name="obra_social" class="form-control"/>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">N&uacute;mero de obra social </label>
                    <input type="text" name="numero_obra_social" class="form-control" id="alumno_numero_obra_social" value="<%= numero_obra_social %>"/>
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
                    <input type="password" class="form-control" id="alumno_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
                  </div>                  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Repetir contrase&ntilde;a</label>
                    <input type="password" class="form-control" id="alumno_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
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