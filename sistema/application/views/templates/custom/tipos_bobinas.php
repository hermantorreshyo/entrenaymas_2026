<script type="text/template" id="tipos_bobinas_panel_template">
  <div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-database icono_principal mr10"></i>Tipos de bobinas
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="autos_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#tipo_bobina">
              <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
            </a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
        <table id="tipos_bobinas_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <th style="width:100px;"></th>
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

<script type="text/template" id="tipos_bobinas_item">
  <td class="ver">
    <span class="text-info"><%= nombre %></span>
  </td>
  <td class="p5 tar">
    <div class="btn-group dropdown">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
        <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>
  </td>
</script>


<script type="text/template" id="tipos_bobinas_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-database icono_principal mr10"></i>Tipos de bobinas
    / <b><%= (id == undefined) ? "Nuevo" : nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-4">
        <div class="detalle_texto">
        </div>
      </div>

      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" id="tipo_bobina_nombre" value="<%= nombre %>" name="nombre" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Orden</label>
                    <input type="text" id="tipo_bobina_orden" value="<%= orden %>" name="orden" class="form-control"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>