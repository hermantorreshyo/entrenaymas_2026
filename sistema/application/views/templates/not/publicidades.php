<script type="text/template" id="publicidades_resultados_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">Todav&iacute;a no ten&eacute;s ninguna publicidad</h1>
  <h3 class="h3">Para a&ntilde;adir tu primera publicidad, hace click en el siguiente bot&oacute;n</h3>
  <div class="list-icon">
    <a href="app/#publicidad"><i class="icon-note"></i></a>
  </div>
  <div>
    <a class="btn btn-lg btn-info btn-addon" href="app/#publicidad">
      <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
    </a>
  </div>
  <p>
    Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
  </p>
</div>
<div class="seccion_llena" style="display:none">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Listado de Publicidades</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-9 sm-m-b">
                <input style="width: 200px; display: inline-block" type="text" id="publicidades_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                <div style="width: 250px; display: inline-block">
                  <select class="w100p" id="publicidades_buscar_categorias">
                    <option value="0">Categoria</option>
                    <% for(var i=0;i< window.categorias_publicidades.length;i++) { %>
                      <% var o = categorias_publicidades[i]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  </select>
                </div>
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </div>
              <% if (!seleccionar) { %>
                <div class="col-md-6 col-lg-3 text-right">
                  <a class="btn btn-info btn-addon ml5 nuevo" href="javascript:void(0)">
                    <i class="fa fa-plus"></i>&nbsp;&nbsp;Nueva&nbsp;&nbsp;
                  </a>
                </div>
              <% } %>
            </div>
          </div>
          <div class="advanced-search-div bg-light dk" style="display:none">
            <div class="wrapper oh">
              <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
              <div class="form-inline">
                <div style="display: inline-block; width: 200px;">
                  <select class="w100p form-control" id="publicidades_clientes"></select>
                </div>                
                <% if (control.check("vendedores") > 0) { %>
                  <div style="display: inline-block; width: 200px;">
                    <select class="w100p form-control" id="publicidades_vendedores">
                      <option value="0">Vendedor</option>
                      <% for(var i=0;i<vendedores.length;i++) { %>
                          <% var o = vendedores[i]; %>
                          <option value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>										
                    </select>
                  </div>
                <% } %>
                <div style="display: inline-block; width: 200px;">
                  <select class="w100p form-control" id="publicidades_activo">
                    <option value="-1">Mostrar todas</option>
                    <option value="1">S&oacute;lo activas</option>
                    <option value="0">Inactivas</option>
                  </select>
                </div>
                <button id="publicidades_buscar_avanzada_btn" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button>
              </div>
            </div>
          </div>
        
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="publicidades_tabla" class="table table-small table-striped sortable m-b-none default footable">
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
                      <th class="sorting" data-sort-by="nombre">Nombre</th>
                      <th class="sorting" data-sort-by="categoria">Categoria</th>
                      <th class="sorting" data-sort-by="cliente">Cliente</th>
                      <th class="sorting" data-sort-by="vendedor">Vendedor</th>
                      <th class="sorting" data-sort-by="desde">Fecha Alta</th>
                      <th class="sorting" data-sort-by="hasta">Fecha Venc.</th>
                      <th class="sorting" data-sort-by="dias_vencimiento">Dias p/venc.</th>
                      <th class="sorting" data-sort-by="costo">Costo</th>
                      <% if (!seleccionar) { %>
                        <th style="width: 70px;">Acciones</th>
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

<script type="text/template" id="publicidades_item_resultados_template">
    <% var clase = (activo==1) ? ( (dias_vencimiento > -10 && dias_vencimiento < 0) ? "fila_alerta" : ((dias_vencimiento > 0) ? "fila_roja":"" )) :"text-muted"; %>
    <% if (seleccionar) { %>
      <td class="<%= clase %>">
          <label class="i-checks m-b-none">
              <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
          </label>
      </td>
    <% } else { %>
      <td class="<%= clase %>">
          <label class="i-checks m-b-none">
              <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
          </label>
      </td>    
    <% } %>
    <!--
    <td class="<%= clase %> data">
      <% if (!isEmpty(path)) { %>
        <div class="customcomplete-image-sm" style="background-position: top center; background-image: url('/sistema/<%= path %>'); background-size: cover"></div>
      <% } %>
    </td>
    -->
    <td class="<%= clase %> data"><%= nombre %> <%= (isEmpty(path))?"(FALTA)":"" %></td>
    <td class="<%= clase %> data"><%= categoria %></td>
    <td class="<%= clase %> data"><%= cliente %></td>
    <td class="<%= clase %> data"><%= vendedor %></td>
    <td class="<%= clase %> data"><%= valida_desde.substr(0,10) %></td>  
    <td class="<%= clase %> data"><%= valida_hasta.substr(0,10) %></td>  
    <td class="<%= clase %> data"><%= (activo==1) ? ((dias_vencimiento > 0) ? dias_vencimiento+" dias vencidos" : Math.abs(dias_vencimiento)+" dias para vencer" ): "" %></td>  
    <td class="<%= clase %> data"><%= costo %></td>  
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
        <div class="btn-group dropdown">
          <i title="Opciones" class="iconito bg-light fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
          </ul>
        </div>        
      </td>      
    <% } %>
</script>


<script type="text/template" id="publicidad_template">
<div class="panel panel-default">
  <div class="panel-heading bold"><%= (id == undefined) ? "Nueva Publicidad" : nombre %></div>
  <div class="panel-body">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-md-2 control-label">Cliente</label>
        <div class="col-md-10">
          <div class="input-group">
            <select id="publicidad_clientes" style="width: 100%" class="form-control"></select>
            <div class="input-group-btn">
              <button type="button" class="btn btn-success nuevo_cliente">Nuevo</button>
            </div>
          </div>                    
        </div>
      </div>
      <% if (control.check("vendedores") > 0) { %>
        <div class="form-group">
            <label class="col-lg-2 control-label">Vendedor </label>
            <div class="col-lg-10">
              <% if (edicion) { %>
                <select class="w100p form-control" id="publicidad_vendedores">
                  <option value="0">-</option>
                  <% for(var i=0;i< vendedores.length;i++) { %>
                      <% var o = vendedores[i]; %>
                      <option value="<%= o.id %>" <%= (o.id==id_vendedor)?"selected":"" %>><%= o.nombre %></option>
                  <% } %>										
                </select>
              <% } %>
            </div>
        </div>
      <% } %>                     
      <div class="form-group">
          <label class="col-md-2 control-label">Campa&ntilde;a</label>
          <div class="col-md-10">
              <% if (edicion) { %>
                  <input type="text" required name="nombre" id="publicidad_nombre" value="<%= nombre %>" class="form-control"/>
              <% } else { %>
                  <span><%= nombre %></span>
              <% } %>
          </div>
      </div>                    
      <div class="form-group">
          <label class="col-md-2 control-label">Categoria</label>
          <div class="col-md-10">
            <select id="publicidad_categorias" name="id_categoria" class="form-control">
              <% for(var i=0;i< window.categorias_publicidades.length;i++) { %>
                <% var o = categorias_publicidades[i]; %>
                <option value="<%= o.id %>" data-alto="<%= o.alto %>" data-ancho="<%= o.ancho %>" <%= (o.id == id_categoria)?"selected":"" %>><%= o.nombre %></option>
              <% } %>                            
            </select>
          </div>
      </div>
      <div class="form-group">
          <label class="col-md-2 control-label">Costo</label>
          <div class="col-md-10">
              <% if (edicion) { %>
                  <input type="text" name="costo" id="publicidad_costo" value="<%= costo %>" class="form-control"/>
              <% } else { %>
                  <span><%= costo %></span>
              <% } %>
          </div>
      </div>                    
      <div class="form-group">
          <label class="col-md-2 control-label">Valido</label>
          <div class="col-md-10">
            <div class="form-inline">
              <input type="text" name="valida_desde" id="publicidad_valida_desde" value="<%= valida_desde %>" class="form-control w150"/>
              <input type="text" name="valida_hasta" id="publicidad_valida_hasta" value="<%= valida_hasta %>" class="form-control w150"/>
            </div>
          </div>
      </div>
      <div class="form-group">
          <label class="col-md-2 control-label">Link</label>
          <div class="col-md-10">
              <% if (edicion) { %>
                  <input type="text" name="link" id="publicidad_link" value="<%= link %>" class="form-control"/>
              <% } else { %>
                  <span><%= link %></span>
              <% } %>
          </div>
      </div>
      <div class="form-group cb">
        <label class="col-md-2 control-label">Puede cerrarse? </label>
        <div class="col-md-10">
          <% if (edicion) { %>
            <label class="i-switch i-switch-md bg-info m-t-xs m-r">
              <input type="checkbox" id="publicidad_cerrar" name="cerrar" class="checkbox" value="1" <%= (cerrar == 1)?"checked":"" %> >
              <i></i>
            </label>
          <% } else { %>
            <span><%= ((cerrar==0) ? "No" : "Si") %></span>
          <% } %>
        </div>
      </div>
      <div class="publicidad_tipo_container" id="publicidad_tipo_3">
        <div class="form-group">
          <div class="col-xs-12">
            <label>C&oacute;digo:</label>
            <textarea name="codigo_html" class="form-control" id="publicidad_codigo_html"><%= codigo_html %></textarea>
          </div>
        </div>
      </div>
      <div class="publicidad_tipo_container" id="publicidad_tipo_1"></div>
      <div class="padder">
        <?php
        /*
        single_upload(array(
          "name"=>"path",
          "label"=>"Imagen Principal",
          "url"=>"publicidades/function/save_image/",
          "width"=>(isset($empresa->config["publicidad_image_width"]) ? $empresa->config["publicidad_image_width"] : 400),
          "height"=>(isset($empresa->config["publicidad_image_height"]) ? $empresa->config["publicidad_image_height"] : 400),
        ));
        */
        single_file_upload(array(
          "name"=>"path",
          "label"=>"Imagen Principal",
          "url"=>"publicidades/function/save_file/",
        ));
        
        single_file_upload(array(
          "name"=>"path_2",
          "label"=>"Imagen Responsive",
          "url"=>"publicidades/function/save_file/",
        ));?>
      </div>
                  
      <div class="publicidad_tipo_container" id="publicidad_tipo_2">
        <?php
        multiple_upload(array(
          "name"=>"publicidades",
          "label"=>"Listado de Fotos",
          "url"=>"publicidades/function/save_image/",
          "width"=>(isset($empresa->config["publicidad_galeria_image_width"]) ? $empresa->config["publicidad_galeria_image_width"] : 800),
          "height"=>(isset($empresa->config["publicidad_galeria_image_height"]) ? $empresa->config["publicidad_galeria_image_height"] : 600),
        )); ?>
      </div>
    </div>
  </div>
  <div class="panel-footer">
    <button class="btn guardar btn-success">Guardar</button>
    <img src="/sistema/resources/images/ajax-loader.gif" class="img_loading"/>
  </div>
</div>
</script>



<script type="text/template" id="publicidades_impresiones_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <div class="row">
      <div class="col-lg-6 col-sm-4 col-xs-12">
        <h1 class="m-n font-thin h3 text-black">Impresiones de Publicidades</h1>
      </div>
    </div>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-xs-12">
				<div style="width: 120px; display: inline-block">
				  <input type="text" class="w100p form-control" id="publicidades_impresiones_fecha_desde" value="<%= fecha_desde %>" placeholder="Desde">
				</div>
				<div style="width: 120px; display: inline-block">
				  <input type="text" class="w100p form-control" id="publicidades_impresiones_fecha_hasta" value="<%= fecha_hasta %>" placeholder="Hasta">
				</div>                
                <select style="width: 250px; display: inline-block" class="form-control" id="publicidades_impresiones_buscar_tipos_estado">
                  <option value="0">Categoria</option>
                  <% for(var i=0;i<window.categorias_publicidades.length;i++) { %>
                    <% var o = categorias_publicidades[i]; %>
                    <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                </select>
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
              </div>
            </div>
          </div>
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="publicidades_impresiones_tabla" class="table table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="sorting">Nombre</th>
                      <th class="sorting">Categoria</th>
                      <th class="sorting">Costo</th>
                      <th class="sorting">Impresa</th>
                      <th class="sorting">Prom./dia</th>
                      <th class="sorting">Costo/Imp.</th>
                      <th class="sorting">Clicks</th>
                      <th class="sorting">Costo/Click.</th>
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

<script type="text/template" id="publicidades_impresiones_item_resultados_template">
    <% var clase = (activo==1)?"":"text-muted"; %>
    <td class="<%= clase %> data"><a href="app/#publicidad/<%= id %>" class="text-info"><%= nombre %></a></td>
    <td class="<%= clase %> data"><%= categoria %></td>
    <td class="<%= clase %> data"><%= costo %></td>
    <td class="<%= clase %> data"><%= impresiones %></td>
    <td class="<%= clase %> data"><%= Number(promedio_impresiones_dia).toFixed(2) %></td>
    <td class="<%= clase %> data">$ <%= Number(costo_impresion).toFixed(4) %></td>
    <td class="<%= clase %> data"><%= clicks %></td>
    <td class="<%= clase %> data">$ <%= Number(costo_click).toFixed(4) %></td>
</script>