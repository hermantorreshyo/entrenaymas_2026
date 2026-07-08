<script type="text/template" id="seo_urls_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="fa fa-suitcase icono_principal"></i>SEO
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="seo_urls_buscar" value="<%= window.seo_urls_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </span>
            </div>
          </div>
          <% if (control.check("seo_urls") == 3) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#seo_url">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="seo_urls_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>URL</th>
                <th class="th_acciones w120"> Acciones</th>
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

<script type="text/template" id="seo_urls_item_resultados_template">
  <% var clase = "" %>
  <td class="<%= clase %> data">
    <span class="text-info"><%= url %></span>
  </td>
  <td class="<%= clase %>">
    <div class="btn-group dropdown ml10">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
        <% if (control.check("seo_urls") == 3) { %>
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
        <% } %>
      </ul>
    </div>
  </td>
</script>


<script type="text/template" id="seo_url_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-suitcase icono_principal"></i>SEO /
      <b><%= (id == undefined) ? 'Nuevo' : 'Editar' %></b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">

    <div class="centrado rform">
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">

                <div class="form-group">
                  <label class="control-label">URL</label>
                  <input type="text" id="seo_url_url" class="form-control" value="<%= url %>" name="url"/>
                </div>

                <div class="form-group">
                  <label class="control-label">Título</label>
                  <input type="text" id="seo_url_title" class="form-control" value="<%= title %>" name="title"/>
                </div>

                <div class="form-group">
                  <label class="control-label">Descripción</label>
                  <textarea id="seo_url_description" name="description" class="form-control"><%= description %></textarea>
                </div>

                <div class="form-group">
                  <label class="control-label">H1</label>
                  <input type="text" id="seo_url_h1" class="form-control" value="<%= h1 %>" name="h1"/>
                </div>

                <div class="form-group">
                  <label class="control-label">H2</label>
                  <input type="text" id="seo_url_h2" class="form-control" value="<%= h2 %>" name="h2"/>
                </div>

                <div class="form-group">
                  <label class="control-label">Texto Comercial</label>
                  <textarea row="3" id="seo_url_texto_comercial" class="form-control"><%= texto_comercial %></textarea>
                </div>

                <div class="form-group">
                  <label class="control-label">Texto</label>
                  <textarea name="texto" name="texto" id="seo_url_texto"><%= texto %></textarea>
                </div>
              </div>
            </div>

          </div>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">Parámetros de la URL</label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">Ingrese los valores de cada parámetro que componen la URL</div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (parametros.length > 0)?'display:block':'' %>">

              <div class="padder">

                <div class="m-b row clearfix">
                  <div class="form-group col-sm-4">
                    <label class="control-label">Orden</label>
                    <input type="number" min="1" id="seo_url_parametro_orden" class="form-control no-model">
                  </div>
                  <div class="form-group col-sm-4">
                    <label class="control-label">Campo</label>
                    <select id="seo_url_parametro_campo" class="form-control no-model" style="width: 100%">
                      <option>Localidad</option>
                      <option>Especialidad</option>
                      <option>Titulacion</option>
                      <option>Objetivo</option>
                      <option>No tener en cuenta</option>
                    </select>
                  </div>
                  <div class="form-group col-sm-4">
                    <label class="control-label">Valor</label>
                    <div class="input-group">
                      <input type="text" id="seo_url_parametro_valor" class="form-control no-model">
                      <span class="input-group-btn">
                        <a id="parametro_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="seo_url_parametros_tabla" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th style="display: none"></th>
                        <th>Orden</th>
                        <th>Campo</th>
                        <th>Valor</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <% for(var i=0;i< parametros.length;i++) { %>
                        <% var p = parametros[i] %>
                        <tr>
                          <td class="orden editar_parametro"><%= p.orden %></td>
                          <td class="campo editar_parametro"><%= p.campo %></td>
                          <td class="valor editar_parametro"><%= p.valor %></td>
                          <td class="tar">
                            <button class="btn btn-sm btn-white eliminar_parametro"><i class="fa fa-trash"></i></button>
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

      <div class="line b-b m-b-lg"></div>

      <% if (edicion) { %>
        <div class="row">
          <div class="col-md-10 col-md-offset-1 tar">
            <button class="btn guardar btn-success">Guardar</button>
          </div>
        </div>
      <% } %>

    </div>

  </div>
</script>