<script type="text/template" id="clasificados_propiedades_resultados_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">Todav&iacute;a no ten&eacute;s ninguna propiedad</h1>
  <h3 class="h3">Para a&ntilde;adir tu primera propiedad, hace click en el siguiente bot&oacute;n</h3>
  <div class="list-icon">
    <a href="app/#clasificado_propiedad"><i class="icon-note"></i></a>
  </div>
  <div>
    <a class="btn btn-lg btn-info btn-addon" href="app/#clasificado_propiedad">
      <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
    </a>
  </div>
  <p>
    Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
  </p>
</div>
<div class="seccion_llena" style="display:none">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Listado de Propiedades</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-3 sm-m-b">
                <div class="input-group">
                    <input type="text" id="propiedades_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                    </span>
                    <span class="input-group-btn">
                      <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
                    </span>
                </div>
              </div>
              <% if (!seleccionar) { %>
                <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                  
                  <a class="btn btn-success btn-addon ml5" href="app/#clasificado_propiedad">
                    <i class="fa fa-plus"></i><span class="hidden-xs">Nueva</span>
                  </a>
                  <!--
                  <div class="btn-group dropdown">
                    <button class="btn btn-sm btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                      <i class="glyphicon glyphicon-print"></i><span class="hidden-xs">Imprimir</span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="javascript:void(0)" class="imprimir" data-tipo="1">Ficha</a></li>
                    </ul>
                  </div>
                  
                  <div class="btn-group dropdown">
                    <button class="btn btn-sm btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                      <i class="fa fa-database"></i><span class="hidden-xs">Datos</span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
                      <li><a href="javascript:void" class="exportar_csv">Exportar CSV</a></li>
                    </ul>
                  </div>
                  -->
                </div>
              <% } %>
            </div>
          </div>
          <div class="advanced-search-div bg-light dk" style="display:none">
            <div class="wrapper oh">
              <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
              <div class="form-inline">
                <select style="width: 250px; display: inline-block" id="clasificados_propiedades_buscar_tipos_estado">
                  <option value="0">Estado</option>
                  <% for(var i=0;i<window.tipos_estado.length;i++) { %>
                    <% var o = tipos_estado[i]; %>
                    <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                </select>
                <select style="width: 250px; display: inline-block" id="clasificados_propiedades_buscar_tipos_inmueble">
                  <option value="0">Tipo Inmueble</option>
                  <% for(var i=0;i<window.tipos_inmueble.length;i++) { %>
                    <% var o = tipos_inmueble[i]; %>
                    <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                </select>
                <select style="width: 250px; display: inline-block" id="clasificados_propiedades_buscar_tipos_operacion">
                  <option value="0">Operacion</option>
                  <% for(var i=0;i<window.tipos_operacion.length;i++) { %>
                    <% var o = tipos_operacion[i]; %>
                    <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                </select>
                <div class="form-group">
                  <button id="clasificados_propiedades_buscar_avanzada_btn" class="btn btn-default buscar"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
            </div>
          </div>
        
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="clasificados_propiedades_tabla" class="table table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <% if (!seleccionar) { %>
                        <th style="width:20px;">
                            <label class="i-checks m-b-none">
                                <input class="esc sel_todos" type="checkbox"><i></i>
                            </label>
                        </th>
                        <th style="width: 10px"></th>
                        <th style="width: 10px"></th>
                        <th style="width: 10px"></th>
                      <% } else { %>
                        <th style="width:20px;"></th>
                      <% } %>
                      <th>Titulo</th>
                      <th>Direccion</th>
                      <th class="w150 sorting" data-sort-by="precio_final">Precio</th>
                      <th class="w150">Estado</th>
                      <th class="w150">Consultas</th>
                      <th>Usuario</th>
                      <% if (!seleccionar) { %>
                        <th style="width:10px;"></th>
                        <th style="width:10px;"></th>
                        <th style="width:10px;"></th>
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

<script type="text/template" id="clasificados_propiedades_item_resultados_template">
    <% var clase = (activo==1)?"":"text-muted"; %>
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
      <td class="p5 <%= clase %> data tac"><i title="Activo" class="glyphicon glyphicon-ok activo <%= (activo == 1)?"text-success":"text-muted" %>"></i></td>
      <td class="p5 <%= clase %> data tac"><i title="Destacado" class="fa fa-star destacado <%= (destacado == 1)?"text-warning":"text-muted" %>"></i></td>
      <td class="p5 <%= clase %> data tac"><i title="Nuevo" class="fa fa-exclamation nuevo <%= (nuevo == 1)?"text-danger":"text-muted" %>"></i></td>
    <% } %>
    <td class="<%= clase %> data">
      <%= tipo_operacion %> <%= tipo_inmueble %>
    </td>
    <td class="<%= clase %> data">
      <%= calle %> <%= altura %> <%= piso %> <%= numero %><br/>
      <%= localidad %>
    </td>
    <td class="<%= clase %> data tar"><%= moneda %> <%= Number(precio_final).toFixed(0) %></td>
    <td class="<%= clase %> data"><%= tipo_estado %></td>
    <td class="<%= clase %> data"><%= cantidad_consultas %></td>
    <td class="<%= clase %> data"><%= usuario %></td>
    <% if (!seleccionar) { %>
      <?php /* <td class="w25 p5"><i title="Compartir Facebook" class="fa fa-facebook facebook text-dark" data-id="<%= id %>" /></td> */ ?>
      <td class="w25 p5"><i title="Duplicar" class="fa fa-copy duplicar text-dark" data-id="<%= id %>" /></td>
      <td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" data-id="<%= id %>" /></td>
    <% } %>
</script>


<script type="text/template" id="clasificado_propiedad_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
	<% if (id == undefined) { %>
	    Nueva Propiedad
	<% } else { %>
	    <%= nombre %>
	<% } %>
  </h1>
</div>

<div class="wrapper-md pb0">
    <div class="panel panel-default">
      <div class="panel-body">
                  
          <div class="form-horizontal">
          
              <div class="form-group">
                  <label class="col-md-2 control-label">Tipo Operacion</label>
                  <div class="col-md-10">
                      <select id="clasificado_propiedad_tipos_operacion" class="w100p">
                        <% for(var i=0;i<window.tipos_operacion.length;i++) { %>
                          <% var o = tipos_operacion[i]; %>
                          <option value="<%= o.id %>" <%= (o.id == id_tipo_operacion)?"selected":"" %>><%= o.nombre %></option>
                        <% } %>
                      </select>
                  </div>
              </div>

              <div class="form-group">
                  <label class="col-md-2 control-label">Tipo Inmueble</label>
                  <div class="col-md-10">
                      <select id="clasificado_propiedad_tipos_inmueble" class="w100p">
                        <% for(var i=0;i<window.tipos_inmueble.length;i++) { %>
                          <% var o = tipos_inmueble[i]; %>
                          <option value="<%= o.id %>" <%= (o.id == id_tipo_inmueble)?"selected":"" %>><%= o.nombre %></option>
                        <% } %>
                      </select>
                  </div>
              </div>
              
              <div class="form-group">
                  <label class="col-md-2 control-label">Estado</label>
                  <div class="col-md-10">
                      <select id="clasificado_propiedad_tipos_estado" class="w100p">
                        <% for(var i=0;i<window.tipos_estado.length;i++) { %>
                          <% var o = tipos_estado[i]; %>
                          <option value="<%= o.id %>" <%= (o.id == id_tipo_estado)?"selected":"" %>><%= o.nombre %></option>
                        <% } %>
                      </select>
                  </div>
              </div>                                  
              
              <div class="form-group">
                  <label class="col-md-2 control-label">Precio Final</label>
                  <div class="col-md-10">
                    <div class="form-inline">
                      <select id="clasificado_propiedad_monedas" class="form-control" name="moneda">
                        <% for(var i=0;i<window.monedas.length;i++) { %>
                          <% var o = monedas[i]; %>
                          <option <%= (o.signo == moneda)?"selected":"" %> value="<%= o.signo %>"><%= o.signo %></option>
                        <% } %>
                      </select>
                      <input id="clasificado_propiedad_precio_final" value="<%= precio_final %>" type="number" class="form-control number" name="precio_final"/>
                    </div>
                  </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg pull-in"></div>
            
            <div class="form-horizontal row">
              <div class="col-md-6">
                <div class="h4">Texto</div>
                <div class="line b-b m-b"></div>
                
                <div class="form-group">
                  <div class="col-xs-12">
                    <textarea name="texto" style="height:120px" class="form-control" id="clasificado_propiedad_texto"><%= texto %></textarea>
                  </div>
                </div>
                
              </div>
              <div class="col-md-6">
                <div class="h4">Fotos</div>
                <div class="line b-b m-b"></div>
                
                <div class="padder">
                  <?php
                  multiple_upload(array(
                    "name"=>"images",
                    "label"=>"",
                    "url"=>"propiedades/function/save_image/",
                    "width"=>(isset($empresa->config["propiedad_galeria_image_width"]) ? $empresa->config["propiedad_galeria_image_width"] : 800),
                    "height"=>(isset($empresa->config["propiedad_galeria_image_height"]) ? $empresa->config["propiedad_galeria_image_height"] : 600),
                    "thumbnail_width"=>(isset($empresa->config["propiedad_galeria_thumbnail_width"]) ? $empresa->config["propiedad_galeria_thumbnail_width"] : 267),
                    "thumbnail_height"=>(isset($empresa->config["propiedad_galeria_thumbnail_height"]) ? $empresa->config["propiedad_galeria_thumbnail_height"] : 150),
                  )); ?>
                </div>
                
              </div>                    
            </div>
            
            <div class="line line-dashed b-b line-lg pull-in"></div>
              
            <div class="form-horizontal row">
              
              <div class="col-md-6">
                <div class="h4">Lugar y Direcci&oacute;n</div>
                <div class="line b-b m-b"></div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Localidad</label>
                    <div class="col-md-8">
                      <input type="text" value="<%= localidad %>" id="clasificado_propiedad_localidad" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control"/>
                    </div>
                </div>                                      
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Calle</label>
                    <div class="col-md-8">
                        <% if (edicion) { %>
                            <input type="text" name="calle" id="clasificado_propiedad_calle" value="<%= calle %>" class="form-control"/>
                        <% } else { %>
                            <span><%= calle %></span>
                        <% } %>
                    </div>
                </div>                  
                <div class="form-group">
                    <label class="col-md-4 control-label">Altura</label>
                    <div class="col-md-8">
                        <% if (edicion) { %>
                            <input type="text" name="altura" id="clasificado_propiedad_altura" value="<%= altura %>" class="form-control"/>
                        <% } else { %>
                            <span><%= altura %></span>
                        <% } %>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Piso</label>
                    <div class="col-md-8">
                        <% if (edicion) { %>
                            <input type="text" name="piso" id="clasificado_propiedad_piso" value="<%= piso %>" class="form-control"/>
                        <% } else { %>
                            <span><%= piso %></span>
                        <% } %>
                    </div>
                </div>                  
                <div class="form-group">
                    <label class="col-md-4 control-label">Letra / N&uacute;mero</label>
                    <div class="col-md-8">
                        <% if (edicion) { %>
                            <input type="text" name="piso" id="clasificado_propiedad_numero" value="<%= numero %>" class="form-control"/>
                        <% } else { %>
                            <span><%= numero %></span>
                        <% } %>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label"></label>
                    <div class="col-md-8">
                      <button id="cargar_mapa" class="btn btn-default">Actualizar Mapa</button>
                    </div>
                </div>                   
              </div>
              <div class="col-md-6">
                <div class="h4">Mapa</div>
                <div class="line b-b m-b"></div>
                <div style="height:400px;" id="mapa"></div>
                <div class="help-block">Puede arrastrar el marcador del mapa para ponerlo en la direccion exacta.</div>
              </div>         
              
            </div>
            
          <div class="line line-dashed b-b line-lg pull-in"></div>
            
        <div class="form-horizontal row">
          
          <div class="col-md-6">
            <div class="h4">Generales</div>
            <div class="line b-b m-b"></div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Ambientes</label>
                <div class="col-md-8">
                  <input type="number" min="0" id="clasificado_propiedad_ambientes" value="<%= ambientes %>" name="ambientes" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Dormitorios</label>
                <div class="col-md-8">
                  <input type="number" min="0" id="clasificado_propiedad_dormitorios" value="<%= dormitorios %>" name="dormitorios" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Cocheras</label>
                <div class="col-md-8">
                  <input type="number" min="0" id="clasificado_propiedad_cocheras" value="<%= cocheras %>" name="cocheras" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Ba&ntilde;os</label>
                <div class="col-md-8">
                  <input type="number" min="0" id="clasificado_propiedad_banios" value="<%= banios %>" name="banios" class="form-control"/>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Caracteristicas</label>
                <div class="col-md-8">
                  <select multiple id="clasificado_propiedad_caracteristicas" style="width: 100%">
                    <% if (!isEmpty(caracteristicas)) { %>
                      <% var carac = caracteristicas.split(";;;") %>
                      <% for (var i=0; i< carac.length; i++) { %>
                        <% var o = carac[i] %>
                        <option selected><%= o %></option>
                      <% } %>
                    <% } %>
                  </select>
                  <span class="help-block m-b-none">Escriba las caracteristicas especificas de la propiedad.</span>
                </div>
            </div>
            
          </div>
          
          <div class="col-md-6">
            <div class="h4">Superficie</div>
            <div class="line b-b m-b"></div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Cubierta</label>
                <div class="col-md-8">
                  <input type="text" id="clasificado_propiedad_superficie_cubierta" name="superficie_cubierta" value="<%= superficie_cubierta %>" class="form-control superficie"/>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Descubierta</label>
                <div class="col-md-8">
                  <input type="text" id="clasificado_propiedad_superficie_descubierta" name="superficie_descubierta" value="<%= superficie_descubierta %>" class="form-control superficie"/>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Semicubierta</label>
                <div class="col-md-8">
                  <input type="text" id="clasificado_propiedad_superficie_semicubierta" name="superficie_semicubierta" value="<%= superficie_semicubierta %>" class="form-control superficie"/>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Total</label>
                <div class="col-md-8">
                  <input type="text" disabled="disabled" id="clasificado_propiedad_superficie_total" name="superficie_total" value="<%= superficie_total %>" class="form-control"/>
                </div>
            </div>

          </div>
          
      </div>
      
      <div class="line line-dashed b-b line-lg pull-in"></div>
      
            
      <div class="col-xs-12">
        <div class="h4">Vendedor</div>
        <div class="line b-b m-b"></div>
        <textarea name="texto_privado" style="height:120px" class="form-control" placeholder="Datos del vendedor..." id="clasificado_propiedad_texto_privado"><%= texto_privado %></textarea>

        <% if (SOLO_USUARIO == 0) { %>
          <div class="h4 m-t">Usuario</div>
          <div class="line b-b m-b"></div>
          <select id="clasificado_propiedad_usuarios" class="form-control m-b">
            <% for(var i=0;i< window.usuarios.models.length;i++) { %>
              <% var o = window.usuarios.models[i]; %>
              <option value="<%= o.id %>" <%= (o.id == id_usuario)?"selected":"" %>><%= o.get("nombre") %></option>
            <% } %>
          </select>
        <% } %>

      </div>
      <!--
      <div class="form-horizontal">
        <div class="form-group">
            <label class="col-md-2 control-label">Vendedor</label>
            <div class="col-md-10">
              <div class="input-group">
                <select id="clasificado_propiedad_clientes" style="width: 100%" class="form-control"></select>
                <div class="input-group-btn">
                  <button type="button" class="btn btn-success nuevo_cliente">Nuevo</button>
                </div>
              </div>                    
            </div>
        </div>
      </div>
      -->
      
      <div class="line line-dashed b-b line-lg pull-in"></div>
      <% if (edicion) { %>
          <div class="form-group">
              <div class="col-xs-12">
                  <button class="btn guardar btn-success">Guardar</button>
                  <img src="/sistema/resources/images/ajax-loader.gif" class="img_loading"/>
              </div>
          </div>
      <% } %>                  
            
        </div>
    </div>
</div>
     
</script>
