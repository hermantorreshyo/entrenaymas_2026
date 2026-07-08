<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-10 col-md-offset-1">

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <% if (ID_EMPRESA == 1284) { %>
                  <div class="col-xs-12">
                    <div class="form-group">
                      <label class="control-label">
                        <?php echo lang(array(
                          "es"=>"Nombre",
                          "en"=>"Name",
                        )); ?>
                      </label>
                      <input type="text" 
                        placeholder="<?php echo lang(array(
                          "es"=>"Ej: Camisa a cuadros manga larga",
                          "en"=>"Ej: Camisa a cuadros manga larga",
                        )); ?>" 
                        required name="nombre" id="articulo_nombre" value="<%= nombre %>" class="form-control"
                        <%= (!edicion)?"disabled":"" %>/>
                    </div>
                    <input type="hidden" name="codigo" id="articulo_codigo" value="<%= codigo %>" class="form-control" <%= (!edicion)?"disabled":"" %>/>
                  </div>
                <% } else { %>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">
                        <?php echo lang(array(
                          "es"=>"C&oacute;digo interno",
                          "en"=>"SKU",
                        )); ?>
                      </label>
                      <input type="text" name="codigo" id="articulo_codigo" value="<%= codigo %>" class="form-control" <%= (!edicion)?"disabled":"" %>/>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="form-group">
                      <label class="control-label">
                        <?php echo lang(array(
                          "es"=>"Nombre",
                          "en"=>"Name",
                        )); ?>
                      </label>
                      <input type="text" 
                        placeholder="<?php echo lang(array(
                          "es"=>"Ej: Camisa a cuadros manga larga",
                          "en"=>"Ej: Camisa a cuadros manga larga",
                        )); ?>" 
                        required name="nombre" id="articulo_nombre" value="<%= nombre %>" class="form-control"
                        <%= (!edicion)?"disabled":"" %>/>
                    </div>
                  </div>
                <% } %>
              </div>
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <textarea name="texto" id="articulo_texto"><%= texto %></textarea>
              </div>
              <div class="form-group mb0 tar" style="display: none;">
                <a id="expand_principal" class="expand-link">
                  <?php echo lang(array(
                    "es"=>"+ M&aacute;s opciones",
                    "en"=>"+ More options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">

                <div class="form-group">
                  <label class="control-label">C&oacute;digos de barra</label>
                  <% if (edicion) { %>
                    <select multiple id="articulo_codigos_barra" style="width: 100%">
                      <% if (!isEmpty(codigo_barra)) { %>
                        <% var carac = codigo_barra.split("###") %>
                        <% for (var i=0; i< carac.length; i++) { %>
                          <% var o = carac[i] %>
                          <option selected><%= o %></option>
                        <% } %>
                      <% } %>
                    </select>
                    <div class="text-muted">Nota: Escribe un c&oacute;digo de barra y presiona Enter para ingresarlo.</div>
                  <% } else { %>
                    <textarea class="form-control no-model" disabled><%= codigo_barra.replace(/\#\#\#/g,"\n") %></textarea>
                  <% } %>
                </div>                

                <div class="row">
                <?php for($i=1;$i<=10;$i++) { ?>

                  <?php if (isset($empresa->config["producto_custom_".$i."_file"])) { ?>
                    
                    <div class="col-xs-12">
                      <?php single_file_upload(array(
                        "name"=>"custom_$i",
                        "label"=>$empresa->config["producto_custom_".$i."_file"],
                        "url"=>"/sistema/articulos/function/save_file/",
                      )); ?>
                    </div>

                  <?php } else if (isset($empresa->config["producto_custom_".$i."_area"])) { ?>
                    <div class="form-group">
                      <label class="control-label"><?php echo $empresa->config["producto_custom_".$i."_label"] ?></label>
                      <textarea name="custom_<?php echo $i ?>" id="articulo_custom_<?php echo $i ?>" class="form-control"><%= custom_<?php echo $i ?> %></textarea>
                    </div>

                  <?php } else if (isset($empresa->config["producto_custom_".$i."_label"])) { ?>
                    <div class="<?php echo (isset($empresa->config['producto_custom_'.$i.'_class'])) ? $empresa->config['producto_custom_'.$i.'_class'] :'col-xs-12'?>">
                      <div class="form-group">
                        <label class="control-label"><?php echo $empresa->config["producto_custom_".$i."_label"] ?></label>
                        <?php if(isset($empresa->config['producto_custom_'.$i.'_values'])) { 
                          $values = explode("|",$empresa->config['producto_custom_'.$i.'_values']); ?>
                          <select class="form-control" name="custom_<?php echo $i ?>">
                            <?php foreach($values as $value) { ?>
                              <option <%= (<?php echo "custom_".$i ?> == "<?php echo $value ?>")?"selected":""  %> value="<?php echo $value ?>"><?php echo $value ?></option>
                            <?php } ?>
                          </select>
                        <?php } else { ?>
                          <input type="text" name="custom_<?php echo $i ?>" id="articulo_custom_<?php echo $i ?>" value="<%= custom_<?php echo $i ?> %>" class="form-control"/>
                        <?php } ?>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>
                </div>

                <% if (ID_EMPRESA == 257) { %>
                  <div class="form-group">
                    <label class="control-label">Ficha tecnica</label>
                    <textarea name="breve" id="articulo_breve"><%= breve %></textarea>
                  </div>
                <% } %>

                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Caracter&iacute;sticas",
                      "en"=>"Features",
                    )); ?>
                  </label>
                  <select multiple id="articulo_caracteristicas" style="width: 100%">
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
                      "es"=>"Nota: Escribe una caracteristica y presiona Enter para ingresarla.",
                      "en"=>"Note: Write a new feature and press Enter.",
                    )); ?>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label">Descripci&oacute;n</label>
                  <textarea name="descripcion" id="articulo_descripcion" class="form-control"><%= descripcion %></textarea>
                </div>  

                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="articulo_nuevo" name="nuevo" class="checkbox" value="1" <%= (nuevo == 1)?"checked":"" %> >
                    <i></i>
                    <?php echo lang(array(
                      "es"=>"Marcar producto como 'Nuevo'.",
                      "en"=>"Mark as new product.",
                    )); ?>
                  </label>
                </div>

                <% if (ID_EMPRESA == 263) { %>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Vencimiento</label>
                        <div class="input-group">
                          <input type="text" name="fecha_ingreso" id="articulo_fecha_ingreso" value="<%= fecha_ingreso %>" class="form-control"/>
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
        </div>

        <div class="panel panel-default" style="display: none;">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">
                  <% if (TIPO_EMPRESA == 4) { %>
                    Categor&iacute;a Web
                  <% } else { %>
                    <?php echo lang(array(
                      "es"=>"Categor&iacute;a",
                      "en"=>"Category",
                    )); ?>
                  <% } %>
                </label>
                <div class="input-group">
                  <select id="articulo_rubros" class="w100p"></select>
                  <span class="input-group-btn">
                    <button tabindex="-1" class="btn btn-info w100 agregar_rubro">
                      <?php echo lang(array(
                        "es"=>"+ Categor&iacute;a",
                        "en"=>"+ Category",
                      )); ?>
                    </button>
                  </span>
                  <span class="input-group-btn <%= (ID_EMPRESA == 1284 && PERFIL != 1399)?"dn":"" %>">
                    <a target="_blank" href="app/#rubros" class="btn btn-default"><i class="fa fa-cog"></i></a>
                  </span>
                </div>
              </div>
              <% if (TIPO_EMPRESA == 4) { %>  
                <div class="form-group">
                  <label class="control-label">
                    Categor&iacute;a Portal La Plata Construye
                  </label>
                  <select id="articulo_custom_1" class="form-control" name="custom_1">
                    <option <%= (isEmpty(custom_1))?"selected":"" %> value="">-</option>
                    <%= workspace.crear_select(window.rubros_lpc,"  ",custom_1) %>
                  </select>
                </div>
              <% } %>
              <% if (control.check("marcas") > 0) { %>
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Marca",
                      "en"=>"Trade",
                    )); ?>
                  </label>
                  <div class="input-group">
                    <select id="articulo_marcas" class="w100p"></select>
                    <span class="input-group-btn">
                      <button tabindex="-1" class="btn btn-info w100 agregar_marca">
                        <?php echo lang(array(
                          "es"=>"+ Marca",
                          "en"=>"+ Trade",
                        )); ?>
                      </button>  
                    </span>
                    <span class="input-group-btn <%= (ID_EMPRESA == 1284 && PERFIL != 1399)?"dn":"" %>">
                      <a target="_blank" href="app/#marcas" class="btn btn-default"><i class="fa fa-cog"></i></a>
                    </span>
                  </div>
                </div>
              <% } %>

              <div class="form-group <%= (ID_EMPRESA == 1284)?"dn":"" %>">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Etiquetas",
                    "en"=>"Tags",
                  )); ?>
                </label>
                <div class="input-group">
                  <select multiple id="articulo_etiquetas" style="width: 100%">
                    <% if (typeof ARTICULOS_ETIQUETAS_NO_CREAR_NUEVAS != "undefined") { %>
                      <% for (var i=0; i< articulos_etiquetas.length; i++) { %>
                        <% var o = articulos_etiquetas[i] %>
                        <% var selected = "" %>
                        <% for (var j=0; j< etiquetas.length; j++) { %>
                          <% var oo = etiquetas[j] %>
                          <% if (oo == o.nombre) { %>
                            <% selected = "selected" %>
                          <% } %>
                        <% } %>
                        <option <%= selected %>><%= o.nombre %></option>
                      <% } %>
                    <% } else { %>
                      <% for (var i=0; i< etiquetas.length; i++) { %>
                        <% var o = etiquetas[i] %>
                        <option selected><%= o %></option>
                      <% } %>
                    <% } %>
                  </select>
                  <span class="input-group-btn">
                    <a target="_blank" href="app/#articulos_etiquetas" class="btn btn-default"><i class="fa fa-cog"></i></a>
                  </span>
                </div>                  
              </div> 
                             
            </div>
          </div>
        </div>

        <?php if (isset($empresa->config["tipo_empresa"]) && $empresa->config["tipo_empresa"] == 1) { // SI ES UNA EMPRESA DE NEUMATICOS ?>
          <?php include_once("articulos_atributos_neumaticos.php") ?>
        <?php } ?>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <?php
              $label = lang(array(
                "es"=>"Im&aacute;genes",
                "en"=>"Photos",
              )); ?>
              <?php 
              multiple_upload(array(
                "name"=>"images",
                "label"=>$label,
                "url"=>"articulos/function/save_image/",
                "width"=>(isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 600),
                "resizable"=>(isset($empresa->config["producto_galeria_image_resizable"]) ? $empresa->config["producto_galeria_image_resizable"] : 0),
                "crop_type"=>(isset($empresa->config["producto_galeria_image_crop_type"]) ? $empresa->config["producto_galeria_image_crop_type"] : 1),
                "upload_multiple"=>true,
              )); ?>
            </div>
          </div>
        </div>

        <?php /*if (!isset($empresa->config["producto_mostrar_listas"])) { ?>
          <div class="panel panel-default">
            <div class="panel-heading">
              <div class="padder">
                <label class="control-label fs16 bold">Precio</label>
              </div>
            </div>
            <div class="panel-body">
              <div class="padder">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label no-bold">Moneda</label>
                      <select id="articulo_monedas" class="form-control" name="moneda">
                        <% for(var i=0;i< window.monedas.length;i++) { %>
                          <% var o = monedas[i]; %>
                          <option <%= (o.signo == moneda)?"selected":"" %> value="<%= o.signo %>"><%= o.signo %></option>
                        <% } %>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label no-bold">Precio Final</label>
                      <input id="articulo_precio_final" value="<%= precio_final %>" type="text" class="form-control number" name="precio_final"/>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label no-bold">% Descuento</label>
                      <input id="articulo_porc_bonif" value="<%= porc_bonif %>" type="text" class="form-control number" name="porc_bonif"/>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label no-bold oh w100p">
                        Precio c/dto
                        <span class="link fs12 fr cp redondear_precio_final_dto">Redondear</span>
                      </label>
                      <input disabled id="articulo_precio_final_dto" value="<%= precio_final_dto %>" type="text" class="form-control number" name="precio_final_dto"/>
                    </div>
                  </div>
                </div>
                <% if (control.check("promociones") > 0) { %>
                  <div id="articulo_promociones_cont" style="display:<%= (porc_bonif == 0)?'none':'block' %>" class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label no-bold">Incluir en promoci&oacute;n</label>
                        <select class="no-model w100p" id="articulo_promociones"></select>
                      </div>
                    </div>
                  </div>
                <% } %>
              </div>
            </div>
          </div>
        <?php }*/ ?>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <% if (MEGASHOP == 1 || ID_EMPRESA == 224 || ID_EMPRESA == 421 || ID_EMPRESA == 445 || (typeof MOSTRAR_PRECIOS_SUCURSALES != "undefined")) { %>
                <% if (edicion) { %>
                  <label class="i-checks mb0">
                    <input type="checkbox" id="articulo_enlazar_costo" value="1"><i></i>
                    Editar todos a la vez
                  </label>
                <% } %>
                <div id="articulo_costos_sucursales"></div>
              <% } else { %>

                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Moneda</label>
                      <select id="articulo_monedas" class="form-control" name="moneda">
                        <% for(var i=0;i< window.monedas.length;i++) { %>
                          <% var o = monedas[i]; %>
                          <option <%= (o.id == moneda)?"selected":"" %> value="<%= o.id %>"><%= o.signo %> (<%= o.nombre %>)</option>
                        <% } %>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Costo Neto</label>
                      <input type="text" class="form-control number calc_total" id="articulo_costo_neto" name="costo_neto" value="<%= costo_neto %>"/>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">% IVA</label>
                      <select id="articulo_tipos_alicuotas_iva" class="form-control">
                        <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                          <% var o = alicuotas_iva[i]; %>
                          <option <%= (id_tipo_alicuota_iva == o.id)?"selected":"" %> value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                        <% } %>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2" style="display:none">
                    <div class="form-group">
                      <label class="control-label">IVA</label>
                      <input id="articulo_iva" disabled value="<%= Number(costo_neto * porc_iva / 100).toFixed(2) %>" type="text" class="form-control" name="costo_iva"/>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Costo Final</label>
                      <input type="text" class="form-control number calc_total" id="articulo_costo_final" name="costo_final" value="<%= Number(costo_final).toFixed(2) %>"/>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="tab-container <%= (ID_EMPRESA == 1284)?"b-t-important":"" %>">
                    <ul class="nav nav-tabs nav-tabs-4 <%= (ID_EMPRESA == 1284)?"dn":"" %>" role="tablist">
                      <li class="active">
                        <a href="#tab_lista1" role="tab" data-toggle="tab"><%= LISTA_1_NOMBRE %></a>
                      </li>
                      <li>
                        <a href="#tab_lista2" role="tab" data-toggle="tab"><%= LISTA_2_NOMBRE %></a>
                      </li>
                      <li>
                        <a href="#tab_lista3" role="tab" data-toggle="tab"><%= LISTA_3_NOMBRE %></a>
                      </li>
                      <li>
                        <a href="#tab_lista4" role="tab" data-toggle="tab"><%= LISTA_4_NOMBRE %></a>
                      </li>
                      <li>
                        <a href="#tab_lista5" role="tab" data-toggle="tab"><%= LISTA_5_NOMBRE %></a>
                      </li>
                      <li>
                        <a href="#tab_lista6" role="tab" data-toggle="tab"><%= LISTA_6_NOMBRE %></a>
                      </li>
                      <button class="btn btn-default pull-right hidden-xs configurar_listas"><i class="fa fa-cog"></i></button>
                    </ul>
                    <div class="tab-content">
                      <div id="tab_lista1" class="tab-pane active">
                        <div class="row">
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia" value="<%= porc_ganancia %>" type="text" class="form-control number calc_total" name="porc_ganancia" <%= (!edicion)?"disabled":"" %> />
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Ganancia</label>
                              <input id="articulo_ganancia" disabled value="<%= ganancia %>" type="text" class="form-control" name="ganancia"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Neto</label>
                              <input id="articulo_precio_neto" disabled value="<%= precio_neto %>" type="text" class="form-control" name="precio_neto"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Precio Final</label>
                              <input id="articulo_precio_final" value="<%= precio_final %>" type="text" class="form-control number b-success" name="precio_final" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Descuento</label>
                              <input id="articulo_porc_bonif" value="<%= porc_bonif %>" type="text" class="form-control number" name="porc_bonif" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Final c/dto.</label>
                              <input disabled id="articulo_precio_final_dto" value="<%= precio_final_dto %>" type="text" class="form-control number" name="precio_final_dto"/>
                            </div>
                          </div>
                        </div>
                        <% if (control.check("promociones") > 0) { %>
                          <div id="articulo_promociones_cont" style="display:<%= (porc_bonif == 0)?'none':'block' %>" class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Incluir en promoci&oacute;n</label>
                                <select class="form-control no-model" id="articulo_promociones" <%= (!edicion)?"disabled":"" %>></select>
                              </div>
                            </div>
                          </div>
                        <% } %>
                      </div>
                      <div id="tab_lista2" class="tab-pane">
                        <div class="row">
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_2" value="<%= porc_ganancia_2 %>" type="text" class="form-control number calc_total" name="porc_ganancia_2" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Ganancia</label>
                              <input id="articulo_ganancia_2" disabled value="<%= ganancia_2 %>" type="text" class="form-control" name="ganancia_2"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Neto</label>
                              <input id="articulo_precio_neto_2" disabled value="<%= precio_neto_2 %>" type="text" class="form-control" name="precio_neto_2"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Precio Final</label>
                              <input id="articulo_precio_final_2" value="<%= precio_final_2 %>" type="text" class="form-control number b-success" name="precio_final_2" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Descuento</label>
                              <input id="articulo_porc_bonif_2" value="<%= porc_bonif_2 %>" type="text" class="form-control number" name="porc_bonif_2" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Final c/dto.</label>
                              <input disabled id="articulo_precio_final_dto_2" value="<%= precio_final_dto_2 %>" type="text" class="form-control number" name="precio_final_dto_2"/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id="tab_lista3" class="tab-pane">
                        <div class="row">
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_3" value="<%= porc_ganancia_3 %>" type="text" class="form-control number calc_total" name="porc_ganancia_3" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Ganancia</label>
                              <input id="articulo_ganancia_3" disabled value="<%= ganancia_3 %>" type="text" class="form-control" name="ganancia_3"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Neto</label>
                              <input id="articulo_precio_neto_3" disabled value="<%= precio_neto_3 %>" type="text" class="form-control" name="precio_neto_3"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Precio Final</label>
                              <input id="articulo_precio_final_3" value="<%= precio_final_3 %>" type="text" class="form-control number b-success" name="precio_final_3" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Descuento</label>
                              <input id="articulo_porc_bonif_3" value="<%= porc_bonif_3 %>" type="text" class="form-control number" name="porc_bonif_3" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Final c/dto.</label>
                              <input disabled id="articulo_precio_final_dto_3" value="<%= precio_final_dto_3 %>" type="text" class="form-control number" name="precio_final_dto_3"/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id="tab_lista4" class="tab-pane">
                        <div class="row">
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_4" value="<%= porc_ganancia_4 %>" type="text" class="form-control number calc_total" name="porc_ganancia_4" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Ganancia</label>
                              <input id="articulo_ganancia_4" disabled value="<%= ganancia_4 %>" type="text" class="form-control" name="ganancia_4"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Neto</label>
                              <input id="articulo_precio_neto_4" disabled value="<%= precio_neto_4 %>" type="text" class="form-control" name="precio_neto_4"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Precio Final</label>
                              <input id="articulo_precio_final_4" value="<%= precio_final_4 %>" type="text" class="form-control number b-success" name="precio_final_4" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Descuento</label>
                              <input id="articulo_porc_bonif_4" value="<%= porc_bonif_4 %>" type="text" class="form-control number" name="porc_bonif_4" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Final c/dto.</label>
                              <input disabled id="articulo_precio_final_dto_4" value="<%= precio_final_dto_4 %>" type="text" class="form-control number" name="precio_final_dto_4"/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id="tab_lista5" class="tab-pane">
                        <div class="row">
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_5" value="<%= porc_ganancia_5 %>" type="text" class="form-control number calc_total" name="porc_ganancia_5" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Ganancia</label>
                              <input id="articulo_ganancia_5" disabled value="<%= ganancia_5 %>" type="text" class="form-control" name="ganancia_5"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Neto</label>
                              <input id="articulo_precio_neto_5" disabled value="<%= precio_neto_5 %>" type="text" class="form-control" name="precio_neto_5"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Precio Final</label>
                              <input id="articulo_precio_final_5" value="<%= precio_final_5 %>" type="text" class="form-control number b-success" name="precio_final_5" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Descuento</label>
                              <input id="articulo_porc_bonif_5" value="<%= porc_bonif_5 %>" type="text" class="form-control number" name="porc_bonif_5" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Final c/dto.</label>
                              <input disabled id="articulo_precio_final_dto_5" value="<%= precio_final_dto_5 %>" type="text" class="form-control number" name="precio_final_dto_5"/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id="tab_lista6" class="tab-pane">
                        <div class="row">
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_6" value="<%= porc_ganancia_6 %>" type="text" class="form-control number calc_total" name="porc_ganancia_6" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Ganancia</label>
                              <input id="articulo_ganancia_6" disabled value="<%= ganancia_6 %>" type="text" class="form-control" name="ganancia_6"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Neto</label>
                              <input id="articulo_precio_neto_6" disabled value="<%= precio_neto_6 %>" type="text" class="form-control" name="precio_neto_6"/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Precio Final</label>
                              <input id="articulo_precio_final_6" value="<%= precio_final_6 %>" type="text" class="form-control number b-success" name="precio_final_6" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">% Descuento</label>
                              <input id="articulo_porc_bonif_6" value="<%= porc_bonif_6 %>" type="text" class="form-control number" name="porc_bonif_6" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label">Final c/dto.</label>
                              <input disabled id="articulo_precio_final_dto_6" value="<%= precio_final_dto_6 %>" type="text" class="form-control number" name="precio_final_dto_6"/>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              <% } %>
            </div>
          </div>
        </div>

        <div class="panel panel-default mt30">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Profesionales",
                    "en"=>"Profesionales",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                  <label for="buscar_usuarios">Profesional</label>
                  <select id="buscar_usuarios" class="form-control">
                    <option value="0">Usuario asignado</option>
                    <% for(var i=0;i< window.usuarios.models.length;i++) { %>
                      <% var o = window.usuarios.models[i]; %>
                      <option value="<%= o.id %>" <%= (o.id == window.consultas_usuario)?"selected":"" %>><%= o.get("nombre") %></option>
                    <% } %>
                  </select>
                </div>
                <div class="col-md-5">
                  <label for="buscar_categoria">Categorias</label>
                  <select id="buscar_categoria" class="form-control">
                    <option value="0">Categoria</option>
                    <% for(var i=0;i< categorias_entrena.length;i++) { %>
                      <% var o = categorias_entrena[i]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  </select>
                </div>
                <div class="col-md-1">
                  <a class="agregar_usuario btn pull-right btn-success btn-addon mt25"><i class="fa fa-plus"></i></a>
                </div>
              </div>
              <table id="usuario_entrena_tabla" class="mt20 table table-small table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th style="width:20px;"></th>
                    <th style="width: 60%;">Nombre</th>
                    <th style="width: 30%:">Categorias</th>
                    <th>Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var c = 0 ;c < profesionales.length; c++) { %>
                    <tr data-id_categoria="<%= profesionales[c]['id_categoria'] %>" data-id="<%= profesionales[c]['id_usuario'] %>">
                      <td></td>
                      <td><%= profesionales[c]['nombre'] %></td>
                      <td><%= profesionales[c]['nombre_categoria'] %></td>
                      <td><i class='fa fa-times eliminar_usuario text-danger cp'></i></td>
                    </tr>
                  <% } %>
                </tbody>
                <tfoot class="pagination_container hide-if-no-paging"></tfoot>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div> 

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <?php //include("articulos_marcas_vehiculos.php"); ?>

        <?php //include("articulos_envio.php"); ?>

        <?php //include("articulos_proveedores.php"); ?>

        <?php include("articulos_variantes.php"); ?>

        <?php //include("articulos_componentes.php"); ?>
        
        <?php //include("articulos_relaciones.php"); ?>

        <?php //include("articulos_seo.php"); ?>

      </div>
    </div>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="line b-b m-b-lg"></div>
        <button class="btn fr guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>