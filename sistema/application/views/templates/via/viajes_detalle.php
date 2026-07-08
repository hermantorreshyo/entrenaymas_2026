<div class="centrado rform">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="padder">

            <% if (control.check("web_configuracion")>0) { %>
              <div class="form-group lang-control">
                <label class="control-label">T&iacute;tulo</label>
                <div class="input-group">
                  <input type="text" id="viaje_nombre" class="form-control active" value="<%= nombre %>" name="nombre"/>
                  <input type="text" id="viaje_nombre_en" name="nombre_en" class="form-control" id="viaje_nombre_en" value="<%= nombre_en %>"/>
                  <input type="text" id="viaje_nombre_pt" name="nombre_pt" class="form-control" id="viaje_nombre_pt" value="<%= nombre_pt %>"/>
                  <div class="input-group-btn">
                    <label class="btn btn-default btn-lang active" data-id="viaje_nombre" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="viaje_nombre_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="viaje_nombre_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
              </div>
            <% } else { %>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" id="viaje_nombre" class="form-control" value="<%= nombre %>" name="nombre"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Recorrido</label>
                    <input type="text" id="viaje_subtitulo" class="form-control" value="<%= subtitulo %>" name="subtitulo"/>
                  </div>
                </div>
              </div>                  
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha de salida</label>
                    <div class="input-group">
                      <input type="text" id="viaje_fecha" name="fecha" class="form-control">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha de regreso</label>
                    <div class="input-group">
                      <input type="text" id="viaje_fecha_llegada" name="fecha_llegada" class="form-control">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Descripci&oacute;n</label>
                <textarea name="texto" class="form-control" id="viaje_texto"><%= texto %></textarea>
              </div>
            <% } %>

            <% if (control.check("web_configuracion")>0) { %>
              <div class="form-group">
                <label class="control-label">Categor&iacute;a</label>
                <div class="input-group">
                  <select id="viaje_categorias" class="form-control"></select>
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-info nueva_categoria">+ Categor&iacute;a</button>
                  </div>
                </div>
              </div>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">Descripci&oacute;n</label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="viaje_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="viaje_link_2" class="btn btn-default btn-lang" data-id="viaje_texto_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label id="viaje_link_3" class="btn btn-default btn-lang" data-id="viaje_texto_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="viaje_texto_cont">
                    <textarea name="texto" name="texto" id="viaje_texto"><%= texto %></textarea>
                  </div>
                  <div class="form-control-cont" id="viaje_texto_en_cont">
                    <textarea name="texto_en" name="texto_en" id="viaje_texto_en"><%= texto_en %></textarea>
                  </div>
                  <div class="form-control-cont" id="viaje_texto_pt_cont">
                    <textarea name="texto_pt" name="texto_pt" id="viaje_texto_pt"><%= texto_pt %></textarea>
                  </div>
                </div>
              </div>

              <div class="form-group mb0 tar">
                <a class="expand-link">
                  <?php echo lang(array(
                    "es"=>"+ M&aacute;s opciones",
                    "en"=>"+ More options",
                  )); ?>
                </a>
              </div>

            <% } %>

          </div>
        </div>

        <% if (control.check("web_configuracion")>0) { %>
          <div class="panel-body expand">
            <div class="padder">

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Fecha de salida</label>
                    <div class="input-group">
                      <input type="text" id="viaje_fecha" name="fecha" class="form-control">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Fecha de regreso</label>
                    <div class="input-group">
                      <input type="text" id="viaje_fecha_llegada" name="fecha_llegada" class="form-control">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Orden</label>
                    <input type="text" id="viaje_orden" name="orden" value="<%= orden %>" class="form-control">
                  </div>
                </div>                      
              </div>

              <div class="form-group lang-control">
                <label class="control-label">Subt&iacute;tulo</label>
                <div class="input-group">
                  <input type="text" id="viaje_subtitulo" class="form-control active" value="<%= subtitulo %>" name="subtitulo"/>
                  <input type="text" id="viaje_subtitulo_en" name="subtitulo_en" class="form-control" id="viaje_subtitulo_en" value="<%= subtitulo_en %>"/>
                  <input type="text" id="viaje_subtitulo_pt" name="subtitulo_pt" class="form-control" id="viaje_subtitulo_pt" value="<%= subtitulo_pt %>"/>
                  <div class="input-group-btn">
                    <label class="btn btn-default btn-lang active" data-id="viaje_subtitulo" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="viaje_subtitulo_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="viaje_subtitulo_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
              </div>

              <div class="form-group lang-control">
                <label class="control-label">Descripci&oacute;n para el listado</label>
                <div class="input-group">
                  <input type="text" id="viaje_observaciones" class="form-control active" value="<%= observaciones %>" name="observaciones"/>
                  <input type="text" id="viaje_observaciones_en" name="observaciones_en" class="form-control" id="viaje_observaciones_en" value="<%= observaciones_en %>"/>
                  <input type="text" id="viaje_observaciones_pt" name="observaciones_pt" class="form-control" id="viaje_observaciones_pt" value="<%= observaciones_pt %>"/>
                  <div class="input-group-btn">
                    <label class="btn btn-default btn-lang active" data-id="viaje_observaciones" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="viaje_observaciones_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                    <label class="btn btn-default btn-lang" data-id="viaje_observaciones_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Link de t&eacute;rminos y condiciones</label>
                <input type="text" id="viaje_link_terminos" class="form-control" value="<%= link_terminos %>" name="link_terminos"/>
              </div>

              <div class="row">
                <?php for($i=1;$i<=10;$i++) { ?>
                  
                  <?php if (isset($empresa->config["viaje_custom_".$i."_label"])) { ?>

                    <?php if (isset($empresa->config["viaje_custom_".$i."_file"])) { ?>
                      <div class="<?php echo (isset($empresa->config['viaje_custom_'.$i.'_class'])) ? $empresa->config['viaje_custom_'.$i.'_class'] :'col-xs-12'?>">
                        <?php single_file_upload(array(
                          "name"=>"custom_$i",
                          "label"=>$empresa->config["viaje_custom_".$i."_file"],
                          "url"=>"/sistema/viajes/function/save_file/",
                        )); ?>
                      </div>
                    <?php } else { ?>
                      <div class="<?php echo (isset($empresa->config['viaje_custom_'.$i.'_class'])) ? $empresa->config['viaje_custom_'.$i.'_class'] :'col-xs-12'?>">
                        <label class="control-label"><?php echo $empresa->config["viaje_custom_".$i."_label"] ?></label>
                        <?php if(isset($empresa->config['viaje_custom_'.$i.'_values'])) { 
                          $values = explode("|",$empresa->config['viaje_custom_'.$i.'_values']); ?>
                          <div class="form-group">
                            <select class="form-control" name="custom_<?php echo $i ?>">
                              <?php foreach($values as $value) { ?>
                                <option <%= (<?php echo "custom_".$i ?> == "<?php echo $value ?>")?"selected":""  %> value="<?php echo $value ?>"><?php echo $value ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        <?php } else { ?>
                          <div class="form-group lang-control">
                            <div class="input-group">
                              <input type="text" id="viaje_custom_<?php echo $i ?>" class="form-control active" value="<%= custom_<?php echo $i ?> %>" name="custom_<?php echo $i ?>"/>
                              <input type="text" id="viaje_custom_<?php echo $i ?>_en" name="custom_<?php echo $i ?>_en" class="form-control" id="viaje_custom_<?php echo $i ?>_en" value="<%= custom_<?php echo $i ?>_en %>"/>
                              <input type="text" id="viaje_custom_<?php echo $i ?>_pt" name="custom_<?php echo $i ?>_pt" class="form-control" id="viaje_custom_<?php echo $i ?>_pt" value="<%= custom_<?php echo $i ?>_pt %>"/>
                              <div class="input-group-btn">
                                <label class="btn btn-default btn-lang active" data-id="viaje_custom_<?php echo $i ?>" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                                <label class="btn btn-default btn-lang" data-id="viaje_custom_<?php echo $i ?>_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                                <label class="btn btn-default btn-lang" data-id="viaje_custom_<?php echo $i ?>_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                              </div>
                            </div>
                          </div>
                        <?php } ?>
                      </div>
                    <?php } ?>

                  <?php } ?>
                <?php } ?>
              </div>

            </div>
          </div>
        <% } %>
      </div>

      <% if (control.check("web_configuracion")>0) { %>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <?php
              /*
              single_upload(array(
                  "name"=>"path",
                  "label"=>"Foto de Portada",
                  "url"=>"/sistema/viajes/function/save_image/",
                  "width"=>(isset($empresa->config["viaje_image_width"]) ? $empresa->config["viaje_image_width"] : 256),
                  "height"=>(isset($empresa->config["viaje_image_height"]) ? $empresa->config["viaje_image_height"] : 256),
                  "quality"=>(isset($empresa->config["viaje_image_quality"]) ? $empresa->config["viaje_image_quality"] : 0.92),
                  "thumbnail_width"=>(isset($empresa->config["viaje_thumbnail_width"]) ? $empresa->config["viaje_thumbnail_width"] : 0),
                  "thumbnail_height"=>(isset($empresa->config["viaje_thumbnail_height"]) ? $empresa->config["viaje_thumbnail_height"] : 0),
              ));
              */ ?>

              <?php
              $label = lang(array(
                "es"=>"Im&aacute;genes",
                "en"=>"Photos",
              ));
              multiple_upload(array(
                "name"=>"images",
                "label"=>$label,
                "url"=>"viajes/function/save_image/",
                "crop_type"=>(isset($empresa->config["viaje_galeria_crop_type"]) ? $empresa->config["viaje_galeria_crop_type"] : 0),
                "width"=>(isset($empresa->config["viaje_galeria_image_width"]) ? $empresa->config["viaje_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["viaje_galeria_image_height"]) ? $empresa->config["viaje_galeria_image_height"] : 600),
                "quality"=>(isset($empresa->config["viaje_galeria_image_quality"]) ? $empresa->config["viaje_galeria_image_quality"] : 0.92),
              )); ?>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Video</label>
                <textarea id="viaje_video" style="height:80px;" placeholder="Pegue aqui el codigo del video que desea insertar" class="form-control" name="video"><%= video %></textarea>
              </div>
            </div>
          </div>
        </div>
      <% } %>

      <% if (control.check("vehiculos")>0) { %>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Veh&iacute;culos y tripulantes",
                    "en"=>"Veh&iacute;culos y tripulantes",
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
                    "es"=>"Configure con cu&aacute;les veh&iacute;culos realizar&aacute; el viaje y las personas encargadas del mismo.",
                    "en"=>"Configure con cuales vehiculos realizara el viaje y las personas encargadas del mismo.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
              <div class="padder">
                <div class="row clearfix">
                  <div class="form-group col-xs-12 col-sm-5">
                    <label class="control-label">Veh&iacute;culo</label>
                    <select id="viaje_vehiculos" style="width: 100%" class="form-control no-model"></select>
                  </div>
                  <div class="form-group col-xs-9 col-sm-5">
                    <label class="control-label">Tripulante</label>
                    <select id="viaje_tripulantes" style="width: 100%" class="form-control no-model"></select>
                  </div>
                  <div class="form-group col-xs-3 col-sm-2">
                    <label class="control-label db">&nbsp;</label>
                    <a id="vehiculo_agregar" class="btn btn-block btn-info">Agregar</a>
                  </div>
                </div>
                <div class="">
                  <table id="vehiculos_tabla" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th>Vehiculo</th>
                        <th>Tripulante</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <% for(var i=0;i< vehiculos_tripulantes.length;i++) { %>
                        <% var p = vehiculos_tripulantes[i] %>
                        <tr id="fila_<%= p.id_vehiculo %>_<%= p.id_tripulante %>" data-id_vehiculo="<%= p.id_vehiculo %>" data-comision="<%= p.comision %>" data-id_tripulante="<%= p.id_tripulante %>">
                          <td><%= p.vehiculo %></td>
                          <td><%= p.tripulante %></td>
                          <td><i class='glyphicon glyphicon-remove eliminar_vehiculo text-danger cp'></i></td>
                        </tr>
                      <% } %>
                    </tbody>
                  </table>
                </div>

              </div>
          </div>
        </div>
      <% } %>

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
                  "es"=>"Administre los valores de los paquetes turisticos.",
                  "en"=>"Administre los valores de los paquetes turisticos.",
                )); ?>                  
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body expand" style="<%= (precios.length > 0)?'display:block':'' %>">

          <% if (control.check("vehiculos")==0) { %>
            <div class="padder">

              <% if (control.check("web_configuracion")>0) { %>
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" class="checkbox" value="1" name="solo_consultar" <%= (solo_consultar==1)?"checked":"" %>>
                      <i></i>S&oacute;lo permitir consultar el viaje o paquete (ocultar el boton de compra o reserva).
                    </label>
                  </div>
                </div>
              <% } %>

              <div class="m-b row clearfix">
                <div class="form-group col-sm-4">
                  <label class="control-label">Tarifa</label>
                  <select id="viaje_precio_tarifas" class="form-control no-model" style="width: 100%">
                    <% for(var t=0; t < window.tipos_tarifas.length; t++) { %>
                      <% var o = window.tipos_tarifas[t]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  </select>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">Fecha desde</label>
                  <div class="input-group">
                    <input type="text" id="viaje_precio_fecha_desde" class="form-control">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">Fecha hasta</label>
                  <div class="input-group">
                    <input type="text" id="viaje_precio_fecha_hasta" class="form-control w-md">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
                <div class="form-group col-sm-3">
                  <label class="control-label">Edad desde</label>
                  <input type="number" id="viaje_precio_edad_desde" min="0" class="no-model form-control" style="width: 100%" placeholder="Desde" />
                </div>
                <div class="form-group col-sm-3">
                  <label class="control-label">Edad hasta</label>
                  <input type="number" id="viaje_precio_edad_hasta" min="0" class="no-model form-control" style="width: 100%" placeholder="Hasta" />
                </div>
                <div class="form-group col-sm-2">
                  <label class="control-label">Precio</label>
                  <select id="viaje_precio_moneda" class="form-control no-model" style="width: 100%">
                    <% for(var i=0; i < window.monedas.length; i++) { %>
                      <% var o = monedas[i]; %>
                      <option value="<%= o.codigo %>"><%= o.codigo %></option>
                    <% } %>
                  </select>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">&nbsp;</label>
                  <div class="input-group">
                    <input id="viaje_precio_monto" value="0" type="number" class="form-control"/>
                    <span class="input-group-btn">
                      <a id="precio_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                    </span>
                  </div>
                </div>
                <div class="col-xs-12">
                  <div class="form-group">
                    <label>Dias de la semana</label>
                    <div class="clearfix">
                      <div class="checkbox mt0 pull-left mr10"><label class="i-checks"><input type="checkbox" class="checkbox" value="1" id="viaje_precio_lunes" checked><i></i>Lunes</label></div>
                      <div class="checkbox mt0 pull-left mr10"><label class="i-checks"><input type="checkbox" class="checkbox" value="1" id="viaje_precio_martes" checked><i></i>Martes</label></div>
                      <div class="checkbox mt0 pull-left mr10"><label class="i-checks"><input type="checkbox" class="checkbox" value="1" id="viaje_precio_miercoles" checked><i></i>Miercoles</label></div>
                      <div class="checkbox mt0 pull-left mr10"><label class="i-checks"><input type="checkbox" class="checkbox" value="1" id="viaje_precio_jueves" checked><i></i>Jueves</label></div>
                      <div class="checkbox mt0 pull-left mr10"><label class="i-checks"><input type="checkbox" class="checkbox" value="1" id="viaje_precio_viernes" checked><i></i>Viernes</label></div>
                      <div class="checkbox mt0 pull-left mr10"><label class="i-checks"><input type="checkbox" class="checkbox" value="1" id="viaje_precio_sabado" checked><i></i>Sabado</label></div>
                      <div class="checkbox mt0 pull-left mr10"><label class="i-checks"><input type="checkbox" class="checkbox" value="1" id="viaje_precio_domingo" checked><i></i>Domingo</label></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table id="viaje_precios_tabla" class="table m-b-none default footable">
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
                          <input type='hidden' class='lunes' value='<%= p.lunes %>'/>
                          <input type='hidden' class='martes' value='<%= p.martes %>'/>
                          <input type='hidden' class='miercoles' value='<%= p.miercoles %>'/>
                          <input type='hidden' class='jueves' value='<%= p.jueves %>'/>
                          <input type='hidden' class='viernes' value='<%= p.viernes %>'/>
                          <input type='hidden' class='sabado' value='<%= p.sabado %>'/>
                          <input type='hidden' class='domingo' value='<%= p.domingo %>'/>
                          <button class="btn btn-sm btn-white eliminar_precio"><i class="fa fa-trash"></i></button>
                        </td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

              <% if (control.check("opcionales")>0) { %>
                <div class="form-group">
                  <label class="control-label">Horarios disponibles</label>
                  <select multiple id="viaje_caracteristicas" style="width: 100%">
                    <% if (!isEmpty(caracteristicas)) { %>
                      <% var carac = caracteristicas.split(";;;") %>
                      <% for (var i=0; i< carac.length; i++) { %>
                        <% var o = carac[i] %>
                        <option selected><%= o %></option>
                      <% } %>
                    <% } %>
                  </select>
                  <div class="text-muted fs14">
                    <?php echo lang(array(
                      "es"=>"Nota: Escriba una opcion y presione Enter para ingresarla.",
                    )); ?>
                  </div>
                </div>
              <% } %>

            </div>

          <% } else { %>

            <div class="padder">
              <div class="table-responsive">
                <table id="viaje_tarifas_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Tarifa</th>
                      <th>Precio Base + Adicionales</th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var t=0; t < window.tipos_tarifas.length; t++) { %>
                      <% var o = window.tipos_tarifas[t]; %>
                      <% var prec = {"moneda":"$","precio":0,"edad_desde":"","edad_hasta":"",recargo:0,recargo_2:0,recargo_3:0,recargo_4:0} %>
                      <% for (var h=0;h< precios.length;h++) { %>
                        <% if (precios[h].id_tipo_tarifa == o.id) { %>
                          <% prec = precios[h]; %>
                        <% } %>  
                      <% } %>
                      <tr>
                        <td><%= o.nombre %></td>
                        <td>
                          <div class="row">
                            <div class="col-xs-2 p0">
                              <input type="hidden" value="<%= o.id %>" class="no-model tarifa">
                              <select class="form-control no-model moneda">
                                <% for(var i=0; i < window.monedas.length; i++) { %>
                                  <% var o = monedas[i]; %>
                                  <option <%= (o.codigo == prec.moneda)?"selected":"" %> value="<%= o.codigo %>"><%= o.codigo %></option>
                                <% } %>
                              </select>
                            </div>
                            <div class="col-xs-2 p0">
                              <input id="viaje_precio" value="<%= prec.precio %>" type="text" class="form-control no-model precio"/>
                            </div>
                            <div class="col-xs-2 p0">
                              <input id="viaje_recargo" value="<%= prec.recargo %>" type="text" class="form-control no-model recargo"/>
                            </div>
                            <div class="col-xs-2 p0">
                              <input id="viaje_recargo_2" value="<%= prec.recargo_2 %>" type="text" class="form-control no-model recargo_2"/>
                            </div>
                            <div class="col-xs-2 p0">
                              <input id="viaje_recargo_3" value="<%= prec.recargo_3 %>" type="text" class="form-control no-model recargo_3"/>
                            </div>
                            <div class="col-xs-2 p0">
                              <input id="viaje_recargo_4" value="<%= prec.recargo_4 %>" type="text" class="form-control no-model recargo_4"/>
                            </div>
                          </div>
                        </td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

              <% if (control.check("promociones") > 0) { %>
                <div id="viaje_promociones_cont">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Promoci&oacute;n</label>
                        <select class="w100p no-model" id="viaje_promociones"></select>
                      </div>
                    </div>
                  </div>
                </div>
              <% } %>

            </div>
          <% } %>
        </div>
      </div>

      <% if (control.check("opcionales")>0) { %>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Opcionales",
                    "en"=>"Opcionales",
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
                    "es"=>"Agregue servicios opcionales para el viaje.",
                    "en"=>"Agregue servicios opcionales para el viaje.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (opcionales.length > 0)?'display:block':'' %>">
            <div class="padder">
              <div class="form-inline row m-b clearfix">
                <div class="form-group col-xs-12">
                  <label class="control-label">Opcionales</label>
                  <div class="input-group w100p">
                    <select id="viaje_opcionales" class="w100p"></select>
                    <span class="input-group-btn w1p">
                      <a id="opcional_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
              <div class="">
                <table id="opcionales_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Nombre</th>
                      <th class="w25"></th>
                      <th class="w25"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< opcionales.length;i++) { %>
                      <% var p = opcionales[i] %>
                      <tr id="opcional_<%= p.id %>" data-id="<%= p.id %>">
                        <td><%= p.nombre %></td>
                        <td><i class='fa fa-times eliminar_opcional text-danger cp'></i></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      <% } %>


      <div class="panel panel-default">
        <div class="panel-body">
          <div class="padder">
            <div class="form-group mb0 clearfix">
              <label class="control-label">
                <?php echo lang(array(
                  "es"=>"Viajes Relacionados",
                  "en"=>"Related Travels",
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
                  "es"=>"Indique otros viajes relacionados que desea mostrar.",
                  "en"=>"Show other related travels.",
                )); ?>                  
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body expand" style="<%= (relacionados.length > 0)?'display:block':'' %>">
          <div class="padder">
            <div class="form-inline row m-b clearfix">
              <div class="form-group col-xs-12">
                <div class="input-group w100p">
                  <select id="viaje_relacionados" class="w100p"></select>
                  <span class="input-group-btn w1p">
                    <a id="relacionado_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                  </span>
                </div>
              </div>
            </div>
            <div class="">
              <table id="relacionados_tabla" class="table m-b-none default footable">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var i=0;i< relacionados.length;i++) { %>
                    <% var p = relacionados[i] %>
                    <tr id="relacionado_<%= p.id %>" data-id="<%= p.id %>">
                      <td><%= p.nombre %></td>
                      <td><i class='fa fa-times eliminar_relacionado text-danger cp'></i></td>
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>


      <?php if (!isset($empresa->config["viaje_detalle_ocultar_mapa"])) { ?>
        <% if (control.check("web_configuracion")>0) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Mapa",
                      "en"=>"Map",
                    )); ?>
                  </label>
                  <a id="viaje_mapa_expand_link" class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Permite agregar varias ubicaciones del destino.",
                      "en"=>"Agregar variantes a productos como talle, color, etc.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="form-group">
                  <div class="input-group">
                    <input type="text" id="viaje_direccion" placeholder="Escriba un lugar para buscarlo en el mapa" class="form-control no-model"/>
                    <div class="input-group-btn">
                      <button id="cargar_mapa" class="btn btn-default">Actualizar Mapa</button>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div style="height:400px;" id="viaje_mapa"></div>
                  <div class="help-block"><button class="btn btn-default add_marker m-r">Agregar Marcador</button>Doble click al marcador para eliminarlo. </div>
                </div>
              </div>
            </div>
          </div>
        <% } %>
      <?php } ?>

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
