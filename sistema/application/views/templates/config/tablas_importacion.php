<script type="text/template" id="tablas_importacion_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n / <b>Importaci&oacute;n</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="tablas_importacion_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon ml5" href="app/#tabla_importacion">
            <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nueva&nbsp;&nbsp;</span>
            </a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="tablas_importacion_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Tabla</th>
                <th class="th_acciones w100">Acciones</th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
</script>

<script type="text/template" id="tablas_importacion_item_resultados_template">
  <% var clase = "" %>
  <td class="<%= clase %> data"><%= nombre %><br/></td>
  <td class="<%= clase %> data"><%= tabla %><br/></td>
  <td class="tar <%= clase %>">
    <div class="btn-group dropdown">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
      <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
      <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>
  </td>
</script>


<script type="text/template" id="tabla_importacion_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
    <i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n / Importaci&oacute;n /
    <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="centrado rform">
      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="tabla_importacion_nombre" />
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Tabla</label>
                    <select class="form-control" name="tabla" id="tabla_importacin_tabla">
                      <option value="articulos" <%= (tabla=="articulos")?"selected":"" %>>Productos</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-body expand">
              <div class="form-inline m-b clearfix">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Campo</label>
                      <select class="form-control no-model" id="tabla_importacion_campo">
                        <option value="nombre">Nombre</option>
                        <option value="codigo">Codigo</option>
                        <option value="precio_final">Precio Final</option>
                        <option value="costo_final">Costo Final</option>
                        <option value="rubro">Categoria</option>
                        <option value="subrubro">Subcategoria</option>
                        <option value="marca">Marca</option>
                        <option value="etiquetas">Etiquetas</option>
                        <option value="texto">Descripcion</option>
                      </select>
                    </div>
                  </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Nro. Columna</label>
                    <div class="input-group">
                    <input id="tabla_importacion_columna" value="1" type="number" min="1" class="form-control no-model"/>
                    <span class="input-group-btn">
                      <a id="tabla_importacion_campo_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                    </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="">
                <table id="tabla_importacion_campos_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Campo</th>
                      <th>Columna</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< campos.length;i++) { %>
                      <% var p = campos[i] %>
                      <tr>
                      <td><%= p.campo %></td>
                      <td><%= p.columna %></td>
                      <td><i class='glyphicon glyphicon-edit cp editar_campo'></i></td>
                      <td><i class='glyphicon glyphicon-remove eliminar_campo text-danger cp'></i></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</script>
