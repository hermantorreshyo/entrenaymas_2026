<script type="text/template" id="tipos_habitaciones_resultados_template">
  <div class="seccion_vacia" style="display:none">
    <h1 class="h1">Todav&iacute;a no ten&eacute;s ninguna habitaci&oacute;n</h1>
    <h3 class="h3">Para a&ntilde;adir tu primera habitaci&oacute;n, hace click en el siguiente bot&oacute;n</h3>
    <div class="list-icon">
      <a href="app/#tipo_habitacion"><i class="icon-note"></i></a>
    </div>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#tipo_habitacion">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
    <p>Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.</p>
  </div>
  <div class="seccion_llena" style="display:none">
    <% if (!seleccionar) { %>
      <div class="bg-light lter b-b wrapper-md ng-scope">
        <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Tipos de Habitaciones</h1>
      </div>
    <% } %>
    <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="<% if (!seleccionar) { %>col-md-6 col-lg-3 <% } else { %> col-xs-12 <% } %> sm-m-b">
            <div class="input-group">
              <input type="text" id="tipos_habitaciones_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
            </div>
            </div>
            <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#tipo_habitacion">
              <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nueva&nbsp;&nbsp;</span>
              </a>
            </div>
            <% } %>
          </div>
        </div>
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="tipos_habitaciones_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
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
                  <th class="w150 sorting" data-sort-by="precio">Precio base</th>
                  <% if (!seleccionar) { %>
                    <th class="th_acciones w100">Acciones</th>
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

<script type="text/template" id="tipos_habitaciones_item_resultados_template">
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
  <td class="<%= clase %> data"><%= nombre %><br/></td>
  <td class="<%= clase %> data tar"><%= moneda %> <%= Number(precio).toFixed(0) %></td>
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


<script type="text/template" id="tipo_habitacion_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
    <i class="fa fa-tags icono_principal"></i>Tipos de habitaciones /
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

              <div class="form-group lang-control">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"T&iacute;tulo",
                    "en"=>"Title",
                  )); ?>
                </label>
                <div class="input-group">
                  <input type="text" id="tipo_habitacion_nombre" class="form-control active" value="<%= nombre %>" name="nombre"/>
                  <input type="text" id="tipo_habitacion_nombre_en" name="nombre_en" class="form-control" id="tipo_habitacion_nombre_en" value="<%= nombre_en %>"/>
                  <input type="text" id="tipo_habitacion_nombre_pt" name="nombre_pt" class="form-control" id="tipo_habitacion_nombre_pt" value="<%= nombre_pt %>"/>
                  <div class="input-group-btn">
                    <label class="btn btn-default btn-lang active" data-id="tipo_habitacion_nombre" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="tipo_habitacion_nombre_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="tipo_habitacion_nombre_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
              </div>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">
                    <?php echo lang(array(
                      "es"=>"Texto",
                      "en"=>"Text",
                    )); ?>
                  </label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="tipo_habitacion_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="entrada_link_2" class="btn btn-default btn-lang" data-id="tipo_habitacion_texto_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label id="entrada_link_3" class="btn btn-default btn-lang" data-id="tipo_habitacion_texto_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="tipo_habitacion_texto_cont">
                    <textarea name="texto" name="texto" id="tipo_habitacion_texto"><%= texto %></textarea>
                  </div>
                  <div class="form-control-cont" id="tipo_habitacion_texto_en_cont">
                    <textarea name="texto_en" name="texto_en" id="tipo_habitacion_texto_en"><%= texto_en %></textarea>
                  </div>
                  <div class="form-control-cont" id="tipo_habitacion_texto_pt_cont">
                    <textarea name="texto_pt" name="texto_pt" id="tipo_habitacion_texto_pt"><%= texto_pt %></textarea>
                  </div>
                </div>
              </div>

              <div class="form-group lang-control">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Caracteristicas",
                    "en"=>"Amenities",
                  )); ?>
                </label>
                <div class="input-group">
                  <div id="tipo_habitacion_caracteristicas" style="border:none;padding:0px" class="form-control active">
                    <select multiple style="width: 100%">
                      <% if (!isEmpty(caracteristicas)) { %>
                        <% var carac = caracteristicas.split(";;;") %>
                        <% for (var i=0; i< carac.length; i++) { %>
                          <% var o = carac[i] %>
                          <option selected><%= o %></option>
                        <% } %>
                      <% } %>
                    </select>
                  </div>
                  <div id="tipo_habitacion_caracteristicas_en" style="border:none;padding:0px" class="form-control">
                    <select multiple style="width: 100%">
                      <% if (!isEmpty(caracteristicas_en)) { %>
                        <% var carac = caracteristicas_en.split(";;;") %>
                        <% for (var i=0; i< carac.length; i++) { %>
                          <% var o = carac[i] %>
                          <option selected><%= o %></option>
                        <% } %>
                      <% } %>
                    </select>
                  </div>
                  <div id="tipo_habitacion_caracteristicas_pt" style="border:none;padding:0px" class="form-control">
                    <select multiple style="width: 100%">
                      <% if (!isEmpty(caracteristicas_pt)) { %>
                        <% var carac = caracteristicas_pt.split(";;;") %>
                        <% for (var i=0; i< carac.length; i++) { %>
                          <% var o = carac[i] %>
                          <option selected><%= o %></option>
                        <% } %>
                      <% } %>
                    </select>
                  </div>
                  <div class="input-group-btn">
                    <label class="btn btn-default btn-lang active" data-id="tipo_habitacion_caracteristicas" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="tipo_habitacion_caracteristicas_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="tipo_habitacion_caracteristicas_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
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
                      "es"=>"Capacidad m&aacute;xima",
                      "en"=>"Capacity",
                    )); ?>
                  </label>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4">
                      <span class="db mb5">Mayores</span>
                      <input type="number" min="0" id="tipo_habitacion_capacidad_maxima" class="form-control" value="<%= capacidad_maxima %>" name="capacidad_maxima"/>
                    </div>
                    <div class="col-md-4">
                      <span class="db mb5">Menores</span>
                      <input type="number" min="0" id="tipo_habitacion_capacidad_maxima_menores" class="form-control" value="<%= capacidad_maxima_menores %>" name="capacidad_maxima_menores"/>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input name="compartida" id="tipo_habitacion_compartida" value="1" type="checkbox" <%= (compartida == 1) ? "checked" : "" %>><i></i> 
                      La habitacion es compartida con otras personas.
                    </label>
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
                      "es"=>"Multimedia",
                      "en"=>"Media",
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
                      "es"=>"Agregue galeria de imagenes, videos, etc.",
                      "en"=>"Create a image gallery, add a single video or atachmentt files...",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">

                <div class="form-group">
                  <?php
                  single_upload(array(
                    "name"=>"path",
                    "label"=>"Imagen Principal",
                    "url"=>"tipos_habitaciones/function/save_image/",
                    "width"=>(isset($empresa->config["tipo_habitacion_image_width"]) ? $empresa->config["tipo_habitacion_image_width"] : 400),
                    "height"=>(isset($empresa->config["tipo_habitacion_image_height"]) ? $empresa->config["tipo_habitacion_image_height"] : 400),
                    "quality"=>(isset($empresa->config["tipo_habitacion_image_quality"]) ? $empresa->config["tipo_habitacion_image_quality"] : 0),
                  )); ?>
                </div>

                <div class="form-group">
                  <?php
                  multiple_upload(array(
                    "name"=>"images",
                    "label"=>"Galer&iacute;a de Fotos",
                    "url"=>"tipos_habitaciones/function/save_image/",
                    "width"=>(isset($empresa->config["tipo_habitacion_galeria_image_width"]) ? $empresa->config["tipo_habitacion_galeria_image_width"] : 800),
                    "height"=>(isset($empresa->config["tipo_habitacion_galeria_image_height"]) ? $empresa->config["tipo_habitacion_galeria_image_height"] : 600),
                    "quality"=>(isset($empresa->config["tipo_habitacion_galeria_image_quality"]) ? $empresa->config["tipo_habitacion_galeria_image_quality"] : 0),
                  )); ?>
                </div>

                <div class="form-group">
                  <label class="control-label">Video</label>
                  <textarea id="tipo_habitacion_video" style="height:80px;" placeholder="Pegue aqui el codigo del video que desea insertar" class="form-control" name="video"><%= video %></textarea>
                </div>

              </div>
            </div>
          </div>


          <div class="panel panel-default">
            <div class="panel-body">

              <div class="form-group">
                <label class="control-label">Precio base por noche</label>
                <div class="form-inline">
                  <select id="tipo_habitacion_monedas" class="form-control" name="moneda">
                  <% for(var i=0; i < window.monedas.length; i++) { %>
                    <% var o = monedas[i]; %>
                    <option <%= (o.signo == moneda)?"selected":"" %> value="<%= o.signo %>"><%= o.signo %></option>
                  <% } %>
                  </select>
                  <input id="tipo_habitacion_precio" value="<%= precio %>" type="number" class="form-control number" name="precio"/>
                  <div class="m-l pt0 checkbox">
                    <label class="i-checks">
                      <input name="publica_precio" id="tipo_habitacion_publica_precio" value="1" type="checkbox" <%= (publica_precio == 1) ? "checked" : "" %>><i></i> 
                      Mostrar precio en la web
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="display:block">

              <div class="tab-container">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="active">
                    <a href="#tab_lista1" role="tab" data-toggle="tab">Precios por temporada</a>
                  </li>
                  <li>
                    <a href="#tab_lista2" role="tab" data-toggle="tab">Ofertas especiales</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div id="tab_lista1" class="tab-pane active">
                    <div class="padder">
                      <div class="form-inline m-b clearfix">
                      <div class="form-group col-xs-6 col-sm-3 p0">
                        <label class="control-label">Desde</label>
                        <div class="input-group">
                        <input type="text" id="precio_fecha_desde" class="form-control">
                        <span class="input-group-btn">
                          <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                        </div>
                      </div>
                      <div class="form-group col-xs-6 col-sm-3 p0">
                        <label class="control-label">Hasta</label>
                        <div class="input-group">
                        <input type="text" id="precio_fecha_hasta" class="form-control w-md">
                        <span class="input-group-btn">
                          <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                        </div>
                      </div>
                      <div class="form-group col-xs-6 col-sm-3 p0">
                        <label class="control-label">Personas</label>
                        <div>
                        <select id="precio_cantidad" class="w100p form-control">
                          <% for(var i=1;i<=capacidad_maxima;i++) { %>
                          <option><%= i %></option>
                          <% } %>
                        </select>
                        </div>
                      </div>
                      <div class="form-group col-xs-6 col-sm-3 p0">
                        <label class="control-label">Precio</label>
                        <div class="input-group">
                        <input id="precio_monto" value="0" type="number" class="form-control"/>
                        <span class="input-group-btn">
                          <a id="precio_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                        </span>
                        </div>
                      </div>
                      </div>
                      <div class="">
                      <table id="precios_tabla" class="table m-b-none default footable">
                        <thead>
                        <tr>
                          <th>Desde</th>
                          <th>Hasta</th>
                          <th>Cantidad</th>
                          <th>Precio</th>
                          <th></th>
                          <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <% for(var i=0;i< precios.length;i++) { %>
                          <% var p = precios[i] %>
                          <tr>
                          <td><%= p.fecha_desde %></td>
                          <td><%= p.fecha_hasta %></td>
                          <td><%= p.personas %></td>
                          <td><%= p.precio %></td>
                          <td><i class='glyphicon glyphicon-edit cp editar_precio'></i></td>
                          <td><i class='glyphicon glyphicon-remove eliminar_precio text-danger cp'></i></td>
                          </tr>
                        <% } %>
                        </tbody>
                      </table>
                      </div>
                    </div>
                  </div>
                  <div id="tab_lista2" class="tab-pane">
                    <div class="padder">
                      <div class="form-inline m-b clearfix">
                      <div class="form-group col-xs-6 col-sm-3 p0">
                        <label class="control-label">Desde</label>
                        <div class="input-group">
                        <input type="text" id="promocion_fecha_desde" class="form-control">
                        <span class="input-group-btn">
                          <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                        </div>
                      </div>
                      <div class="form-group col-xs-6 col-sm-3 p0">
                        <label class="control-label">Hasta</label>
                        <div class="input-group">
                        <input type="text" id="promocion_fecha_hasta" class="form-control w-md">
                        <span class="input-group-btn">
                          <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                        </div>
                      </div>
                      <div class="form-group col-xs-6 col-sm-3 p0">
                        <label class="control-label">Personas</label>
                        <div>
                        <select id="promocion_cantidad" class="w100p form-control">
                          <% for(var i=1;i<=capacidad_maxima;i++) { %>
                          <option><%= i %></option>
                          <% } %>
                        </select>
                        </div>
                      </div>
                      <div class="form-group col-xs-6 col-sm-3 p0">
                        <label class="control-label">Precio/Noche</label>
                        <div class="input-group">
                        <input id="promocion_monto" value="0" type="number" class="form-control"/>
                        <span class="input-group-btn">
                          <a id="promocion_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                        </span>
                        </div>
                      </div>
                      </div>
                      <div class="">
                      <table id="promociones_tabla" class="table m-b-none default footable">
                        <thead>
                        <tr>
                          <th>Desde</th>
                          <th>Hasta</th>
                          <th>Cantidad</th>
                          <th>Precio</th>
                          <th></th>
                          <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <% for(var i=0;i< promociones.length;i++) { %>
                          <% var p = promociones[i] %>
                          <tr>
                          <td><%= p.fecha_desde %></td>
                          <td><%= p.fecha_hasta %></td>
                          <td><%= p.personas %></td>
                          <td><%= p.precio %></td>
                          <td><i class='glyphicon glyphicon-edit cp editar_precio'></i></td>
                          <td><i class='glyphicon glyphicon-remove eliminar_precio text-danger cp'></i></td>
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
        </div>
      </div>

      <% if (edicion) { %>
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-10">
            <button class="btn guardar btn-success">Guardar</button>
          </div>
        </div>
      <% } %>

    </div>
  </div>

</script>
