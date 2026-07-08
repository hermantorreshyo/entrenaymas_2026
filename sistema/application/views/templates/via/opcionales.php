<script type="text/template" id="opcionales_resultados_template">
<div class="seccion_llena">
  <% if (!seleccionar) { %>
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <b>Opcionales</b>
      </h1>
    </div>
  <% } %>
  <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="<% if (!seleccionar) { %>col-md-6 col-lg-3 <% } else { %> col-xs-12 <% } %> sm-m-b">
              <div class="input-group">
                  <input type="text" id="opcionales_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                  <span class="input-group-btn">
                    <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
                  </span>
              </div>
            </div>
            <% if (!seleccionar) { %>
              <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                <a class="btn btn-info btn-addon ml5" href="app/#opcional">
                  <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
                </a>
              </div>
            <% } %>
          </div>
        </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="opcionales_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <% if (!seleccionar) { %>
                  <th style="width:20px;">
                    <label class="i-checks m-b-none">
                      <input class="esc sel_todos" type="checkbox"><i></i>
                    </label>
                  </th>
                <% } else { %>
                  <th style="width:20px;"></th>
                <% } %>
                <th>Nombre</th>
                <% if (!seleccionar) { %>
                  <th class="th_acciones w150">Acciones</th>
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
</script>

<script type="text/template" id="opcionales_item_resultados_template">
  <% var clase = "" %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>
  <% } %>
  <td class="<%= clase %> data">
    <span class="text-info"><%= nombre.ucwords() %></span>
  </td>
  <% if (!seleccionar) { %>
    <td class="<%= clase %>">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="fr m-t-xs btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>


<script type="text/template" id="opcional_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n 
    / Opcionales
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group lang-control">
                <label class="control-label">T&iacute;tulo</label>
                <div class="input-group">
                  <input type="text" id="opcional_nombre" class="form-control active" value="<%= nombre %>" name="nombre"/>
                  <input type="text" id="opcional_nombre_en" name="nombre_en" class="form-control" id="opcional_nombre_en" value="<%= nombre_en %>"/>
                  <input type="text" id="opcional_nombre_pt" name="nombre_pt" class="form-control" id="opcional_nombre_pt" value="<%= nombre_pt %>"/>
                  <div class="input-group-btn">
                    <label class="btn btn-default btn-lang active" data-id="opcional_nombre" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="opcional_nombre_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="opcional_nombre_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Categor&iacute;a</label>
                <div class="input-group">
                  <select id="opcional_categorias" class="w100p"></select>
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-info nueva_categoria">+ Categor&iacute;a</button>
                  </div>
                </div>
              </div>
              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">Descripci&oacute;n</label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="opcional_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="opcional_link_2" class="btn btn-default btn-lang" data-id="opcional_texto_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label id="opcional_link_3" class="btn btn-default btn-lang" data-id="opcional_texto_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="opcional_texto_cont">
                    <textarea name="texto" name="texto" id="opcional_texto"><%= texto %></textarea>
                  </div>
                  <div class="form-control-cont" id="opcional_texto_en_cont">
                    <textarea name="texto_en" name="texto_en" id="opcional_texto_en"><%= texto_en %></textarea>
                  </div>
                  <div class="form-control-cont" id="opcional_texto_pt_cont">
                    <textarea name="texto_pt" name="texto_pt" id="opcional_texto_pt"><%= texto_pt %></textarea>
                  </div>
                </div>
              </div>

              <?php 
              single_upload(array(
                "name"=>"path",
                "label"=>"Imagen",
                "url"=>"/sistema/opcionales/function/save_image/",
                "width"=>(isset($empresa->config["opcional_image_width"]) ? $empresa->config["opcional_image_width"] : 256),
                "height"=>(isset($empresa->config["opcional_image_height"]) ? $empresa->config["opcional_image_height"] : 256),
                "quality"=>(isset($empresa->config["opcional_image_quality"]) ? $empresa->config["opcional_image_quality"] : 0.92),
                "thumbnail_width"=>(isset($empresa->config["opcional_thumbnail_width"]) ? $empresa->config["opcional_thumbnail_width"] : 0),
                "thumbnail_height"=>(isset($empresa->config["opcional_thumbnail_height"]) ? $empresa->config["opcional_thumbnail_height"] : 0),
              ));
              ?>

            </div>
          </div>

        </div>


        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Precios y tarifas",
                    "en"=>"Precios y tarifas",
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
                    "es"=>"Administre los valores por temporada y edades.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (precios.length > 0)?'display:block':'' %>">

            <div class="padder">
              <div class="m-b row clearfix">
                <div class="form-group col-sm-4">
                  <label class="control-label">Tarifa</label>
                  <select id="opcional_precio_tarifas" class="form-control no-model" style="width: 100%">
                    <% for(var t=0; t < window.tipos_tarifas.length; t++) { %>
                      <% var o = window.tipos_tarifas[t]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  </select>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">Fecha desde</label>
                  <div class="input-group">
                    <input type="text" id="opcional_precio_fecha_desde" class="form-control">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">Fecha hasta</label>
                  <div class="input-group">
                    <input type="text" id="opcional_precio_fecha_hasta" class="form-control w-md">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
                <div class="form-group col-sm-3">
                  <label class="control-label">Edad desde</label>
                  <input type="number" id="opcional_precio_edad_desde" min="0" class="no-model form-control" style="width: 100%" placeholder="Desde" />
                </div>
                <div class="form-group col-sm-3">
                  <label class="control-label">Edad hasta</label>
                  <input type="number" id="opcional_precio_edad_hasta" min="0" class="no-model form-control" style="width: 100%" placeholder="Hasta" />
                </div>
                <div class="form-group col-sm-2">
                  <label class="control-label">Precio</label>
                  <select id="opcional_precio_moneda" class="form-control no-model" style="width: 100%">
                    <% for(var i=0; i < window.monedas.length; i++) { %>
                      <% var o = monedas[i]; %>
                      <option value="<%= o.codigo %>"><%= o.codigo %></option>
                    <% } %>
                  </select>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">&nbsp;</label>
                  <div class="input-group">
                    <input id="opcional_precio_monto" value="0" type="number" class="form-control"/>
                    <span class="input-group-btn">
                      <a id="precio_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table id="opcional_precios_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th style="display: none"></th>
                      <th>Tarifa</th>
                      <th>Fecha</th>
                      <th>Hasta</th>
                      <th>Edad</th>
                      <th>Hasta</th>
                      <th style="width: 20px"></th>
                      <th>Monto</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< precios.length;i++) { %>
                      <% var p = precios[i] %>
                      <tr>
                        <td class="id_tipo_tarifa dn"><%= p.id_tipo_tarifa %></td>
                        <td class="tarifa editar_precio"><span class="text-info editar_precio"><%= p.nombre %></span></td>
                        <td class="fecha_desde editar_precio"><%= p.fecha_desde %></td>
                        <td class="fecha_hasta editar_precio"><%= p.fecha_hasta %></td>
                        <td class="edad_desde editar_precio"><%= p.edad_desde %></td>
                        <td class="edad_hasta editar_precio"><%= p.edad_hasta %></td>
                        <td class="moneda tar pr0 editar_precio"><%= p.moneda %></td>
                        <td class="precio editar_precio"><%= p.precio %></td>
                        <td class="tar">
                          <button class="btn btn-sm btn-white eliminar_precio"><i class="fa fa-trash"></i></button>
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
        <div class="col-md-offset-1 tar">
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    <% } %>

  </div>
</div>

</script>