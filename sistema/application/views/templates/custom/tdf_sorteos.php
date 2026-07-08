<script type="text/template" id="tdf_sorteos_resultados_template">
  <div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-tags icono_principal mr10"></i>Sorteos
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
          <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#tdf_sorteo">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
        <table id="tdf_sorteos_tabla" class="table table-striped sortable m-b-none default footable">
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
                <th class="sorting" data-sort-by="fecha_hasta">Fecha</th>
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

<script type="text/template" id="tdf_sorteos_item_resultados_template">
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
    <span class="text-info"><%= titulo %></span>
  </td>
  <td class="<%= clase %> data"><%= fecha_hasta %></td>
  <% if (!seleccionar) { %>
    <td class="p5 tar <%= clase %>">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
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


<script type="text/template" id="tdf_sorteo_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-tags icono_principal mr10"></i>Sorteos
    / <b><%= (id == undefined) ? "Nuevo" : titulo %></b>
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

              <div class="form-group">
                <label class="control-label">T&iacute;tulo</label>
                <input type="text" required name="titulo" id="tdf_sorteo_titulo" value="<%= titulo %>" class="form-control"/>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Desde</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="tdf_sorteo_fecha_desde" value="<%= fecha_desde %>" name="fecha_desde">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Hasta</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="tdf_sorteo_fecha_hasta" value="<%= fecha_hasta %>" name="fecha_hasta">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Cant. de n&uacute;meros</label>
                    <input type="text" name="maximo" id="tdf_sorteo_maximo" value="<%= maximo %>" class="form-control"/>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Marca</label>
                    <% if (control.check("marcas_vehiculos")>0) { %>
                      <select id="tdf_sorteo_marcas_vehiculos" class="w100p"></select>
                    <% } else { %>
                      <input type="text" id="tdf_sorteo_marca" value="<%= marca %>" name="marca" class="form-control"/>
                    <% } %>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Modelo</label>
                    <input type="text" id="tdf_sorteo_modelo" value="<%= modelo %>" name="modelo" class="form-control"/>
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
                <textarea name="texto" name="tdf_sorteo_texto" id="tdf_sorteo_texto"><%= texto %></textarea>
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
          <div class="panel-body expand" style="<%= (images.length>0) ? 'display:block':'' %>">
            <div class="padder">

              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>"Imagen Principal",
                "url"=>"tdf_sorteos/function/save_image/",
                "width"=>(isset($empresa->config["tdf_sorteo_image_width"]) ? $empresa->config["tdf_sorteo_image_width"] : 400),
                "height"=>(isset($empresa->config["tdf_sorteo_image_height"]) ? $empresa->config["tdf_sorteo_image_height"] : 400),
              )); ?>  

              <?php
              single_upload(array(
                "name"=>"path_fondo",
                "label"=>"Imagen de fondo",
                "url"=>"tdf_sorteos/function/save_image/",
                "width"=>(isset($empresa->config["tdf_sorteo_2_image_width"]) ? $empresa->config["tdf_sorteo_2_image_width"] : 400),
                "height"=>(isset($empresa->config["tdf_sorteo_2_image_height"]) ? $empresa->config["tdf_sorteo_2_image_height"] : 400),
              )); ?>  

              <?php
              multiple_upload(array(
                "name"=>"images",
                "label"=>"Galer&iacute;a de Fotos",
                "url"=>"tdf_sorteos/function/save_image/",
                "crop_type"=>(isset($empresa->config["tdf_sorteo_galeria_crop_type"]) ? $empresa->config["tdf_sorteo_galeria_crop_type"] : 0),
                "width"=>(isset($empresa->config["tdf_sorteo_galeria_image_width"]) ? $empresa->config["tdf_sorteo_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["tdf_sorteo_galeria_image_height"]) ? $empresa->config["tdf_sorteo_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["tdf_sorteo_galeria_image_quality"]) ? $empresa->config["tdf_sorteo_galeria_image_quality"] : 0),
                "upload_multiple"=>true,
              )); ?>

              <div class="form-group">
                <label class="control-label">Video</label>
                <textarea id="tdf_sorteo_video" style="height:80px;" placeholder="Pegue aqui el codigo del video que desea insertar" class="form-control" name="video"><%= video %></textarea>
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
          <div class="panel-body expand" style="<%= (images.length>0) ? 'display:block':'' %>">
            <div class="padder">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tipo</label>
                    <select id="tdf_sorteo_tipos_vehiculo" style="width: 100%" class="form-control">
                      <% for(var i=0;i< window.tipos_vehiculo.length;i++) { %>
                        <% var o = tipos_vehiculo[i]; %>
                        <option value="<%= o.id %>" <%= (o.id == id_tipo)?"selected":"" %>><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">A&ntilde;o</label>
                    <input type="text" id="tdf_sorteo_anio" value="<%= anio %>" name="anio" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Kms.</label>
                    <input type="text" id="tdf_sorteo_kms" value="<%= kms %>" name="kms" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Motor</label>
                    <input type="text" id="tdf_sorteo_motor" value="<%= motor %>" name="motor" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tipo de combustible</label>
                    <input type="text" id="tdf_sorteo_combustible" value="<%= combustible %>" name="combustible" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Traccion</label>
                    <input type="text" id="tdf_sorteo_traccion" value="<%= traccion %>" name="traccion" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Cant. de puertas</label>
                    <input type="text" id="tdf_sorteo_puertas" value="<%= puertas %>" name="puertas" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Versi&oacute;n</label>
                    <input type="text" id="tdf_sorteo_version" value="<%= version %>" name="version" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tipo de direcci&oacute;n</label>
                    <input type="text" id="tdf_sorteo_direccion" value="<%= direccion %>" name="direccion" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_aire_acondicionado" name="aire_acondicionado" class="checkbox" value="1" <%= (aire_acondicionado == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Aire acondicionado </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_alarma" name="alarma" class="checkbox" value="1" <%= (alarma == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Alarma </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_cierre_centralizado" name="cierre_centralizado" class="checkbox" value="1" <%= (cierre_centralizado == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Cierre Centr. </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_levanta_cristales" name="levanta_cristales" class="checkbox" value="1" <%= (levanta_cristales == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Levanta Cristales </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_gps" name="gps" class="checkbox" value="1" <%= (gps == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">GPS </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_espejos_electricos" name="espejos_electricos" class="checkbox" value="1" <%= (espejos_electricos == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Espejos Elect. </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_tapizado_cuero" name="tapizado_cuero" class="checkbox" value="1" <%= (tapizado_cuero == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Tapizado Cuero </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_computadora" name="computadora" class="checkbox" value="1" <%= (computadora == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Computadora </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_airbag" name="airbag" class="checkbox" value="1" <%= (airbag == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Airbag </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_frenos_abs" name="frenos_abs" class="checkbox" value="1" <%= (frenos_abs == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Frenos ABS </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_control_traccion" name="control_traccion" class="checkbox" value="1" <%= (control_traccion == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Control Traccion </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_control_estabilidad" name="control_estabilidad" class="checkbox" value="1" <%= (control_estabilidad == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Control Estabilidad </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_antiniebla" name="antiniebla" class="checkbox" value="1" <%= (antiniebla == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Antiniebla </label>
                </div>
                <div class="col-md-4">
                  <label class="i-checks m-r-xs">
                    <input type="checkbox" id="tdf_sorteo_tercer_stop" name="tercer_stop" class="checkbox" value="1" <%= (tercer_stop == 1)?"checked":"" %> >
                    <i></i>
                  </label>
                  <label class="control-label">Tercer Stop </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <% if (id != undefined) { %>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    Participantes
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    Vea los participantes del sorteo
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (clientes.length>0) ? 'display:block':'' %>">
              <div class="padder">
                <table style="max-height: 200px; overflow: auto" class="table table-small xs">
                  <thead>
                    <tr>
                      <th>Nombre</th>
                      <th>Numero</th>
                      <th>Fecha</th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0; i< clientes.length; i++) { %>
                      <% var par = clientes[i] %>
                      <tr>
                        <td><input type="radio" name="id_ganador" value="<%= par.id_cliente %>" <%= (id_ganador == par.id_cliente)?"checked":"" %> class="no-model"/></td>
                        <td><%= par.nombre %></td>
                        <td><%= par.numero %></td>
                        <td><%= par.fecha %></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>

                <div class="form-group">
                  <label class="control-label">Texto para la p&aacute;gina de ganador</label>
                  <textarea class="form-control" id="tdf_sorteo_texto_ganador" name="texto_ganador"><%= texto_ganador %></textarea>
                </div>

                <?php
                single_upload(array(
                  "name"=>"path_ganador",
                  "label"=>"Foto del ganador",
                  "url"=>"tdf_sorteos/function/save_image/",
                  "width"=>(isset($empresa->config["tdf_sorteo_ganador_image_width"]) ? $empresa->config["tdf_sorteo_ganador_image_width"] : 400),
                  "height"=>(isset($empresa->config["tdf_sorteo_ganador_image_height"]) ? $empresa->config["tdf_sorteo_ganador_image_height"] : 400),
                )); ?>

              </div>
            </div>
          </div>

        <% } %>
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