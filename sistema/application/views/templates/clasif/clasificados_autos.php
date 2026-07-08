<script type="text/template" id="clasificados_autos_resultados_template">
  <div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-tags icono_principal mr10"></i>Clasificados
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="clasificados_autos_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="buscar btn btn-default"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
          <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#clasificado_auto">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
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
      <div class="panel-body">
        <div class="b-a table-responsive">
        <table id="clasificados_autos_tabla" class="table table-striped sortable m-b-none default footable">
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
                <th>Cliente</th>
                <th class="w150 sorting" data-sort-by="precio_final">Precio</th>
                <% if (ID_EMPRESA == 70) { %>
                  <th style="width: 110px">Consultas</th>
                <% } else if (ID_EMPRESA == 263) { %>
                  <th style="width: 110px">Vencimiento</th>
                <% } %>
                <% if (!seleccionar) { %>
                  <th style="width:100px;"></th>
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

<script type="text/template" id="clasificados_autos_item_resultados_template">
  <% var clase = (activo==1)?"":"text-muted"; %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= id %>" name="radio" type="radio"><i></i>
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
    <% if (ID_EMPRESA == 70) { %>
      <%= marca %> <%= modelo %> <%= anio %>
    <% } else { %>
      <span class="text-info"><%= titulo %></span>
    <% } %>
  </td>
  <td class="<%= clase %> data"><%= cliente %></td>
  <td class="<%= clase %> data tar"><%= moneda %> <%= Number(precio_final).toFixed(2) %></td>
  <% if (ID_EMPRESA == 70) { %>
    <td class="<%= clase %> data">
      <span class="label bg-success"><%= cantidad_consultas %></span>
    </td>
  <% } else if (ID_EMPRESA == 263) { %>
    <td class="<%= clase %> data"><%= valido_hasta %></td>
  <% } %>
  <% if (!seleccionar) { %>
    <td class="p5 tar <%= clase %>">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <i title="Destacado" class="fa fa-star iconito destacado <%= (destacado == 1)?"active":"" %>"></i>
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


<script type="text/template" id="clasificado_auto_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-tags icono_principal mr10"></i>Clasificados
    / <b><%= (id == undefined) ? "Nuevo" : titulo %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <div class="form-group">
                <label class="control-label">T&iacute;tulo</label>
                <input type="text" required name="titulo" id="clasificado_auto_titulo" value="<%= titulo %>" class="form-control"/>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Marca</label>
                    <% if (control.check("marcas_vehiculos")>0) { %>
                      <div class="input-group">
                        <select id="clasificado_auto_marcas_vehiculos" class="w100p"></select>
                        <span class="input-group-btn">
                          <button tabindex="-1" class="btn btn-info agregar_marca_vehiculo">+</button>  
                        </span>
                      </div>
                    <% } else { %>
                      <input type="text" id="clasificado_auto_marca" value="<%= marca %>" name="marca" class="form-control"/>
                    <% } %>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Modelo</label>
                    <input type="text" id="clasificado_auto_modelo" value="<%= modelo %>" name="modelo" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">A&ntilde;o</label>
                    <input type="text" id="clasificado_auto_anio" value="<%= anio %>" name="anio" class="form-control"/>                    
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <textarea name="texto" name="clasificado_auto_texto" id="clasificado_auto_texto"><%= texto %></textarea>
              </div>

              <% if (ID_EMPRESA != 70) { %>
                <div class="form-group">
                  <label class="control-label">Vendedor</label>
                  <div class="input-group">
                    <select id="clasificado_auto_clientes" style="width: 100%" class="form-control"></select>
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-info nuevo_cliente">Agregar</button>
                    </div>
                  </div>
                </div>
              <% } else { %>
                <div class="form-group">
                  <label class="control-label">Vendedor</label>
                  <textarea name="texto_privado" style="height:120px" class="form-control" placeholder="Datos del vendedor..." id="clasificado_auto_texto_privado"><%= texto_privado %></textarea>
                </div>
              <% } %>

              <% if (ID_EMPRESA == 263) { %>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Vencimiento</label>
                      <div class="input-group">
                        <input type="text" name="valido_hasta" id="clasificado_auto_valido_hasta" value="<%= valido_hasta %>" class="form-control"/>
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              <% } %>

          </div>
        </div>
      </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <b>Precio</b>
          </div>
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label no-bold">Moneda</label>
                    <select id="clasificado_auto_monedas" class="form-control" name="moneda">
                      <% for(var i=0;i< window.monedas.length;i++) { %>
                        <% var o = monedas[i]; %>
                        <option <%= (o.signo == moneda)?"selected":"" %> value="<%= o.signo %>"><%= o.signo %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label no-bold">Precio final</label>
                    <input id="clasificado_auto_precio_final" value="<%= precio_final %>" type="number" class="form-control number" name="precio_final"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="m-l pt0 mt5 checkbox">
                      <label class="i-checks">
                        <input name="publica_precio" id="clasificado_auto_publica_precio" value="1" type="checkbox" <%= (publica_precio == 1) ? "checked" : "" %>><i></i> 
                        Publicar precio en web
                      </label>
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
                    "es"=>"Multimedia",
                    "en"=>"Multimedia",
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
                    "en"=>"Agregue galeria de imagenes, videos, etc.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (images.length>0 || ID_EMPRESA == 70) ? 'display:block':'' %>">
            <div class="padder">

              <?php
              multiple_upload(array(
                "name"=>"images",
                "label"=>"Galer&iacute;a de Fotos",
                "url"=>"autos/function/save_image/",
                "crop_type"=>(isset($empresa->config["clasificado_auto_galeria_crop_type"]) ? $empresa->config["clasificado_auto_galeria_crop_type"] : 0),
                "width"=>(isset($empresa->config["clasificado_auto_galeria_image_width"]) ? $empresa->config["clasificado_auto_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["clasificado_auto_galeria_image_height"]) ? $empresa->config["clasificado_auto_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["clasificado_auto_galeria_image_quality"]) ? $empresa->config["clasificado_auto_galeria_image_quality"] : 0),
                "upload_multiple"=>true,
              )); ?>

              <div class="form-group">
                <label class="control-label">Video</label>
                <textarea id="clasificado_auto_video" style="height:80px;" placeholder="Pegue aqui el codigo del video que desea insertar" class="form-control" name="video"><%= video %></textarea>
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
                    "es"=>"Ubicaci&oacute;n",
                    "en"=>"Location",
                  )); ?>
                </label>
                <a id="expand_mapa" class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Indique la ubicaci&oacute;n del clasificado.",
                    "en"=>"Agregar variantes a productos como talle, color, etc.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (id_localidad != 0)?"display:block":"" %>" id="mapa_expandable">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <label class="control-label">Pais</label>
                  <div class="form-group">
                    <select id="clasificado_auto_paises" name="id_pais" class="form-control">
                      <% for(var i=0;i< paises.length;i++) { %>
                        <% var p = paises[i] %>
                        <option <%= (id_pais == p.id)?"selected":"" %> value="<%= p.id %>"><%= p.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="control-label">Provincia</label>
                  <div class="form-group">
                    <select id="clasificado_auto_provincias" name="id_provincia" class="form-control">
                      <% for(var i=0;i< provincias.length;i++) { %>
                        <% var p = provincias[i] %>
                        <option data-id_pais="<%= p.id_pais %>" <%= (id_provincia == p.id)?"selected":"" %> value="<%= p.id %>"><%= p.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <label class="control-label">Departamento / Partido</label>
                  <div class="form-group">
                    <select id="clasificado_auto_departamentos" name="id_departamento" class="form-control"></select>
                  </div>
                </div>                
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Localidad</label>
                    <select id="clasificado_auto_localidades" name="id_localidad" class="form-control"></select>
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
                    "es"=>"Caracter&iacute;sticas",
                    "en"=>"Multimedia",
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
                    "es"=>"Marque las diferentes caracter&iacute;sticas del veh&iacute;culo.",
                    "en"=>"Agregue galeria de imagenes, videos, etc.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (images.length>0 || ID_EMPRESA == 70) ? 'display:block':'' %>">
            <div class="padder">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Tipo</label>
                    <select id="clasificado_auto_tipos_vehiculo" style="width: 100%" class="form-control">
                      <% for(var i=0;i< window.tipos_vehiculo.length;i++) { %>
                        <% var o = tipos_vehiculo[i]; %>
                        <option value="<%= o.id %>" <%= (o.id == id_tipo)?"selected":"" %>><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Estado</label>
                    <select class="form-control" id="clasificado_auto_nuevo" name="nuevo">
                      <option <%= (nuevo==0)?"selected":"" %> value="0">Usado</option>
                      <option <%= (nuevo==1)?"selected":"" %> value="1">Nuevo</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Color</label>
                    <input type="text" id="clasificado_auto_color" value="<%= color %>" name="color" class="form-control"/>
                  </div>
                </div>                
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Kms.</label>
                    <input type="text" id="clasificado_auto_kms" value="<%= kms %>" name="kms" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Motor</label>
                    <input type="text" id="clasificado_auto_motor" value="<%= motor %>" name="motor" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tipo de combustible</label>
                    <input type="text" id="clasificado_auto_combustible" value="<%= combustible %>" name="combustible" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Traccion</label>
                    <input type="text" id="clasificado_auto_traccion" value="<%= traccion %>" name="traccion" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Cant. de puertas</label>
                    <input type="text" id="clasificado_auto_puertas" value="<%= puertas %>" name="puertas" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Versi&oacute;n</label>
                    <input type="text" id="clasificado_auto_version" value="<%= version %>" name="version" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tipo de direcci&oacute;n</label>
                    <input type="text" id="clasificado_auto_direccion" value="<%= direccion %>" name="direccion" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_aire_acondicionado" name="aire_acondicionado" class="checkbox" value="1" <%= (aire_acondicionado == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Aire acondicionado </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_alarma" name="alarma" class="checkbox" value="1" <%= (alarma == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Alarma </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_cierre_centralizado" name="cierre_centralizado" class="checkbox" value="1" <%= (cierre_centralizado == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Cierre Centr. </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_levanta_cristales" name="levanta_cristales" class="checkbox" value="1" <%= (levanta_cristales == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Levanta Cristales </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_gps" name="gps" class="checkbox" value="1" <%= (gps == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">GPS </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_espejos_electricos" name="espejos_electricos" class="checkbox" value="1" <%= (espejos_electricos == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Espejos Elect. </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_tapizado_cuero" name="tapizado_cuero" class="checkbox" value="1" <%= (tapizado_cuero == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Tapizado Cuero </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_computadora" name="computadora" class="checkbox" value="1" <%= (computadora == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Computadora </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_airbag" name="airbag" class="checkbox" value="1" <%= (airbag == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Airbag </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_frenos_abs" name="frenos_abs" class="checkbox" value="1" <%= (frenos_abs == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Frenos ABS </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_control_traccion" name="control_traccion" class="checkbox" value="1" <%= (control_traccion == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Control Traccion </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_control_estabilidad" name="control_estabilidad" class="checkbox" value="1" <%= (control_estabilidad == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Control Estabilidad </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_antiniebla" name="antiniebla" class="checkbox" value="1" <%= (antiniebla == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Antiniebla </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="clasificado_auto_tercer_stop" name="tercer_stop" class="checkbox" value="1" <%= (tercer_stop == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Tercer Stop </label>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>