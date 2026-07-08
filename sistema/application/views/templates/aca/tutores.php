<script type="text/template" id="tutores_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("tutores") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="input-group">
            <input type="text" id="tutores_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default"><i class="fa fa-search"></i></button>
            </span>
            <span class="input-group-btn">
              <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn pull-right btn-info btn-addon" href="app/#tutor">
            <i class="fa fa-plus"></i>
            <span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
          </a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="tutores_table" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th class="sorting" data-sort-by="nombre">Nombre</th>
              <th>Email</th>
              <th>Celular</th>
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


<script type="text/template" id="tutores_item">
  <td class='ver'><span class='<%= (activo==1)?"text-info":"" %>'><%= nombre %></span></td>
  <td class='ver'><span><%= email %></span></td>
  <td class='ver'><span><%= celular %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
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

<script type="text/template" id="tutores_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("tutores") %>
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
                    <input type="text" name="nombre" class="form-control" id="tutor_nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" id="tutor_apellido" value="<%= apellido %>"/>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Email</label>
                <input type="text" name="email" class="form-control" id="tutor_email" value="<%= email %>"/>
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

              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <textarea class="form-control" name="observaciones"><%= observaciones %></textarea>
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
                    <input type="text" value="<%= localidad %>" id="tutor_localidad" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control"/>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Direccion </label>
                    <input type="text" name="direccion" class="form-control" id="tutor_direccion" value="<%= direccion %>"/>
                  </div>  
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tel&eacute;fono 1</label>
                    <input type="text" name="telefono" class="form-control" id="tutor_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Celular 1</label>
                    <input type="text" name="celular" class="form-control" id="tutor_celular" value="<%= celular %>"/>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tel&eacute;fono 2</label>
                    <input type="text" name="telefono_2" class="form-control" id="tutor_telefono_2" value="<%= telefono_2 %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Celular 2</label>
                    <input type="text" name="celular_2" class="form-control" id="tutor_celular_2" value="<%= celular_2 %>"/>
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
                    <input type="password" class="form-control" id="tutor_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
                  </div>                  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Repetir contrase&ntilde;a</label>
                    <input type="password" class="form-control" id="tutor_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
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


<script type="text/template" id="tutores_edit_mini_panel_template">
  <div class="panel pb0 mb0">
  <div class="panel-body">
    <div class="oh m-b">
      <h4 class="h4 pull-left">Nuevo Tutor</h4>
      <i class="pull-right glyphicon glyphicon-remove text-muted cerrar"></i>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Nombre" name="nombre" class="tab form-control" id="tutores_mini_nombre"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Apellido" name="apellido" class="tab form-control" id="tutores_mini_apellido"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Email" name="email" class="tab form-control" id="tutores_mini_email"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Telefono" name="telefono" class="tab form-control" id="tutores_mini_telefono"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Celular" name="celular" class="tab form-control" id="tutores_mini_celular"/>
    </div>
    <div class="text-right">
      <button class="btn guardar btn-success tab">Guardar</button>
    </div>
  </div>
  </div>
</script>