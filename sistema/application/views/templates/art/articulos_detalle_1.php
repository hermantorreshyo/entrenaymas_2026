<% var perfil_web = (MEGASHOP == 1 && PERFIL == 595 || ID_EMPRESA == 224 && PERFIL == 720) %>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Codigo Interno</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> required name="codigo" id="articulo_codigo" value="<%= codigo %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> required name="nombre" id="articulo_nombre" value="<%= nombre %>" class="form-control"/>
                  </div>
                </div>
              </div>

              <% if (ID_EMPRESA == 342 || perfil_web ) { %>
                <div class="form-group">
                  <label class="control-label">Titulo para web</label>
                  <input type="text" <%= (!edicion && !perfil_web)?"disabled":"" %> required name="custom_1" id="articulo_custom_1" value="<%= custom_1 %>" class="form-control"/>
                </div>
              <% } %>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Categoria</label>
                    <% if (edicion || perfil_web) { %>
                      <div class="input-group">
                        <select id="articulo_rubros" class="w100p"></select>
                        <span class="input-group-btn">
                          <button tabindex="-1" class="btn btn-info agregar_rubro">+</button>  
                        </span>
                      </div>
                    <% } else { %>
                      <select id="articulo_rubros" disabled class="w100p"></select>
                    <% } %>
                  </div>  
                </div>
                <div class="col-md-4">
                  <% if (control.check("marcas") > 0 || perfil_web) { %>
                    <div class="form-group">
                      <label class="control-label">Marca</label>
                      <% if (edicion || perfil_web) { %>
                        <div class="input-group">
                          <select id="articulo_marcas" class="w100p"></select>
                          <span class="input-group-btn">
                            <button tabindex="-1" class="btn btn-info agregar_marca">+</button>  
                          </span>
                        </div>
                      <% } else { %>
                        <select id="articulo_marcas" disabled class="w100p"></select>
                      <% } %>
                    </div>
                  <% } %>
                </div>
                <div class="col-md-4">
                  <% if (ID_EMPRESA == 229 || ID_EMPRESA == 230 || ID_EMPRESA == 1355) { %>
                    <div class="form-group">
                      <label class="control-label">Orden</label>
                      <input type="text" name="custom_1" id="articulo_custom_1" value="<%= custom_1 %>" class="form-control"/>
                    </div>
                  <% } else if (control.check("departamentos_comerciales") > 0) { %>
                    <div class="form-group">
                      <label class="control-label">Departamento</label>
                      <% if (edicion) { %>
                        <div class="input-group">
                          <select id="articulo_departamentos_comerciales" class="w100p"></select>
                          <span class="input-group-btn">
                            <button tabindex="-1" class="btn btn-info agregar_departamento">+</button>  
                          </span>
                        </div>
                      <% } else { %>
                        <select id="articulo_departamentos_comerciales" class="w100p"></select>
                      <% } %>
                    </div>
                  <% } %>
                </div>
              </div>

              <% if (ID_EMPRESA == 342 || ID_EMPRESA == 224 || ID_EMPRESA == 1325 || ID_EMPRESA == 293 || perfil_web) { %>
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Descripci&oacute;n",
                      "en"=>"Description",
                    )); ?>
                  </label>
                  <textarea name="texto" id="articulo_texto"><%= texto %></textarea>
                </div>
              <% } else { %>
                <div class="form-group">
                  <label class="control-label">Descripci&oacute;n</label>
                  <textarea <%= (!edicion)?"disabled":"" %> name="descripcion" id="articulo_descripcion" class="form-control"><%= descripcion %></textarea>
                </div>
              <% } %>

              <?php // SI ES UNA EMPRESA DE NEUMATICOS */ ?>
              <% if (typeof TIPO_EMPRESA != "undefined" && TIPO_EMPRESA == 1) { %>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Ancho</label>
                      <input type="text" name="custom_7" id="articulo_custom_7" value="<%= custom_7 %>" class="form-control"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Perfil</label>
                      <input type="text" name="custom_8" id="articulo_custom_8" value="<%= custom_8 %>" class="form-control"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Rodado</label>
                      <input type="text" name="custom_9" id="articulo_custom_9" value="<%= custom_9 %>" class="form-control"/>
                    </div>
                  </div>
                </div>
              <% } %>

              <a id="expand_principal" class="expand-link fr">
                <?php echo lang(array(
                  "es"=>"+ Ver opciones",
                  "en"=>"+ View options",
                )); ?>
              </a>
            </div>
          </div>
          <div class="panel-body expand" style="display:block">
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
                "upload_multiple"=>true,
              )); ?>
            
              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Etiquetas",
                    "en"=>"Tags",
                  )); ?>
                </label>
                <select multiple id="articulo_etiquetas" style="width: 100%">
                  <% for (var i=0; i< etiquetas.length; i++) { %>
                    <% var o = etiquetas[i] %>
                    <option selected><%= o %></option>
                  <% } %>
                </select>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="<%= (ID_EMPRESA == 342)?"display:none":"" %>">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
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
                <% if (MEGASHOP == 1) { %>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Clase</label>
                      <select class="form-control" id="articulo_tipo" name="tipo" <%= (!edicion)?"disabled":"" %>>
                        <option <%= (tipo=="")?"selected":"" %> value="">-</option>
                        <option <%= (tipo=="10")?"selected":"" %> value="10">Clase A</option>
                        <option <%= (tipo=="5")?"selected":"" %> value="5">Clase B</option>
                        <option <%= (tipo=="3")?"selected":"" %> value="3">Clase C</option>
                      </select>
                    </div> 
                  </div>
                <% } %>                
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Codigo Bulto</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="codigo_barra_bulto" id="articulo_codigo_barra_bulto" value="<%= codigo_barra_bulto %>" class="form-control"/>
                  </div> 
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Forma de Venta</label>
                    <select class="form-control" id="articulo_unidades" name="unidad" <%= (!edicion)?"disabled":"" %>>
                        <option <%= (unidad=="U")?"selected":"" %> value="U">Unidades</option>
                        <option <%= (unidad=="K")?"selected":"" %> value="K">Kilogramos</option>
                        <option <%= (unidad=="L")?"selected":"" %> value="L">Litros</option>
                        <option <%= (unidad=="C3")?"selected":"" %> value="C3">Cm 3</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Unid. x Bulto</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="uxb" id="articulo_uxbm" value="<%= uxb %>" class="form-control"/>
                  </div>
                </div>
                <% if (id != undefined) { %>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Fecha Actualizado</label>
                      <div class="input-group">
                        <input type="text" name="fecha_mov" <%= (ID_EMPRESA != 121) ? "disabled" : "" %> id="articulo_fecha_mov" value="<%= fecha_mov %>" class="form-control"/>
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                <% } %>
              </div>
              <% if (control.check("stock") > 0) { %>
                <div class="row">
                  <?php /*
                  <% if (id == undefined) { %>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Stock inicial</label>
                        <input type="text" <%= (!edicion)?"disabled":"" %> name="stock" id="articulo_stock" value="<%= stock %>" class="form-control"/>
                      </div>
                    </div>
                  <% } %>
                  */ ?>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="checkbox">
                        <label class="i-checks">
                          <input type="checkbox" <%= (!edicion)?"disabled":"" %> id="articulo_usa_stock" name="usa_stock" value="1" <%= (usa_stock == 1)?"checked":"" %> ><i></i>
                          Gestionar stock del producto
                        </label>
                      </div>          
                    </div>
                  </div>
                </div>
              <% } %>
              <% if (ID_EMPRESA != 226) { %>
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" <%= (!edicion)?"disabled":"" %> id="articulo_no_totalizar_reparto" name="no_totalizar_reparto" class="checkbox" value="1" <%= (no_totalizar_reparto == 1)?"checked":"" %> ><i></i>
                      Articulo pesable
                    </label>
                  </div>
                </div>
              <% } %>
              <% if (typeof FACTURACION_USA_NPLU != "undefined" && FACTURACION_USA_NPLU == 1) { %>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">PLU Balanza</label>
                      <input type="text" name="nplu" id="articulo_nplu" value="<%= nplu %>" class="form-control"/>
                    </div>
                  </div>
                </div>
              <% } %>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php /*<% var mostrar_costos = (!edicion && ID_EMPRESA == 287) ? false : true %>*/?>
    <% var mostrar_costos = (!edicion) ? false : true %>
    <div class="row <%= (ID_EMPRESA == 224 && perfil_web)?'dn':'' %>">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <% if (MEGASHOP == 1 || ID_EMPRESA == 224 || ID_EMPRESA == 421 || (typeof MOSTRAR_PRECIOS_SUCURSALES != "undefined")) { %>
                <% if (edicion) { %>
                  <label class="i-checks mb0">
                    <input type="checkbox" id="articulo_enlazar_costo" value="1"><i></i>
                    Editar todos a la vez
                  </label>
                <% } %>
                <div id="articulo_costos_sucursales"></div>
              <% } else { %>
                <?php //<% if ( (ID_EMPRESA == 134 || ID_EMPRESA == 342) && mostrar_costos) { %> ?>
                <% if ( (ID_EMPRESA == 134 || ID_EMPRESA == 342 || ID_EMPRESA == 287 || ID_EMPRESA == 444) && mostrar_costos) { %>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Costo Neto s/dto.</label>
                        <input type="text" <%= (!edicion)?"disabled":"" %> class="form-control number calc_total" id="articulo_costo_neto_inicial" name="costo_neto_inicial" value="<%= costo_neto_inicial %>"/>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">% Dto. Proveedor</label>
                        <input type="text" <%= (!edicion)?"disabled":"" %> class="form-control number calc_total" id="articulo_dto_prov" name="dto_prov" value="<%= dto_prov %>"/>
                      </div>
                    </div>
                  </div>
                <% } %>
                <div class="row" style="<%= (!mostrar_costos)?'display:none':'' %>">
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
                      <input type="text" <%= (!edicion)?"disabled":"" %> class="form-control number calc_total" id="articulo_costo_neto" name="costo_neto" value="<%= costo_neto %>"/>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="row">
                      <div class="col-xs-6 p0">
                        <div class="form-group">
                          <label class="control-label">% IVA</label>
                          <select id="articulo_tipos_alicuotas_iva" class="form-control" <%= (!edicion)?"disabled":"" %>>
                            <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                              <% var o = alicuotas_iva[i]; %>
                              <option <%= (id_tipo_alicuota_iva == o.id)?"selected":"" %> value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                            <% } %>
                          </select>
                        </div>
                      </div>
                      <div class="col-xs-6 p0">
                        <div class="form-group">
                          <label class="control-label">IVA</label>
                          <input id="articulo_iva" disabled value="<%= Number(costo_neto * porc_iva / 100).toFixed(2) %>" type="text" class="form-control" name="costo_iva"/>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Adicionales</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="articulo_costo_agregado" disabled value="<%= Number(Number(costo_1) + Number(costo_2) + Number(costo_3) + Number(costo_4)).toFixed(2) %>">
                            <span class="input-group-btn">
                              <button tabindex="-1" type="button" id="articulo_costo_agregado_btn" class="btn btn-default"><i class="fa fa-plus"></i></button>
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Costo Final</label>
                          <input type="text" class="form-control number calc_total" id="articulo_costo_final" name="costo_final" value="<%= Number(costo_final).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="articulo_costos_agregados" style="<%= (costo_1 == 0 && costo_2 == 0 && costo_3 == 0 && costo_4 == 0) ? 'display:none':'' %>">
                  <div class="row">
                    <div class="col-sm-3 costo_adicional_item">
                      <div class="form-group">
                        <label class="control-label">Costo 1</label>
                        <div class="clearfix">
                          <div class="col-xs-6 pl0">
                            <div class="input-group m-b">
                              <input type="text" class="form-control calc_porc_costo_adicional" id="articulo_porc_costo_1" name="porc_costo_1" value="<%= Number(porc_costo_1).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                              <span class="input-group-addon">%</span>
                            </div>
                          </div>
                          <div class="col-xs-6 p0">
                            <input type="text" class="form-control calc_costo_adicional" id="articulo_costo_1" name="costo_1" value="<%= Number(costo_1).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3 costo_adicional_item">
                      <div class="form-group">
                        <label class="control-label">Costo 2</label>
                        <div class="clearfix">
                          <div class="col-xs-6 pl0">
                            <div class="input-group m-b">
                              <input type="text" class="form-control calc_porc_costo_adicional" id="articulo_porc_costo_2" name="porc_costo_2" value="<%= Number(porc_costo_2).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                              <span class="input-group-addon">%</span>
                            </div>
                          </div>
                          <div class="col-xs-6 p0">
                            <input type="text" class="form-control calc_costo_adicional" id="articulo_costo_2" name="costo_2" value="<%= Number(costo_2).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3 costo_adicional_item">
                      <div class="form-group">
                        <label class="control-label">Costo 3</label>
                        <div class="clearfix">
                          <div class="col-xs-6 pl0">
                            <div class="input-group m-b">
                              <input type="text" class="form-control calc_porc_costo_adicional" id="articulo_porc_costo_3" name="porc_costo_3" value="<%= Number(porc_costo_3).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                              <span class="input-group-addon">%</span>
                            </div>
                          </div>
                          <div class="col-xs-6 p0">
                            <input type="text" class="form-control calc_costo_adicional" id="articulo_costo_3" name="costo_3" value="<%= Number(costo_3).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3 costo_adicional_item">
                      <div class="form-group">
                        <label class="control-label">Costo 4</label>
                        <div class="clearfix">
                          <div class="col-xs-6 pl0">
                            <div class="input-group m-b">
                              <input type="text" class="form-control calc_porc_costo_adicional" id="articulo_porc_costo_4" name="porc_costo_4" value="<%= Number(porc_costo_4).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                              <span class="input-group-addon">%</span>
                            </div>
                          </div>
                          <div class="col-xs-6 p0">
                            <input type="text" class="form-control calc_costo_adicional" id="articulo_costo_4" name="costo_4" value="<%= Number(costo_4).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="tab-container">
                    <ul class="nav nav-tabs nav-tabs-4" role="tablist">
                      <li class="active">
                        <a href="#tab_lista1" role="tab" data-toggle="tab"><%= LISTA_1_NOMBRE %></a>
                      </li>
                      <li style="<%= (!mostrar_costos)?'display:none':'' %>">
                        <a href="#tab_lista2" role="tab" data-toggle="tab"><%= LISTA_2_NOMBRE %></a>
                      </li>
                      <li style="<%= (!mostrar_costos)?'display:none':'' %>">
                        <a href="#tab_lista3" role="tab" data-toggle="tab"><%= LISTA_3_NOMBRE %></a>
                      </li>
                      <li style="<%= (!mostrar_costos)?'display:none':'' %>">
                        <a href="#tab_lista4" role="tab" data-toggle="tab"><%= LISTA_4_NOMBRE %></a>
                      </li>
                      <li style="<%= (!mostrar_costos)?'display:none':'' %>">
                        <a href="#tab_lista5" role="tab" data-toggle="tab"><%= LISTA_5_NOMBRE %></a>
                      </li>
                      <li style="<%= (!mostrar_costos)?'display:none':'' %>">
                        <a href="#tab_lista6" role="tab" data-toggle="tab"><%= LISTA_6_NOMBRE %></a>
                      </li>
                      <button class="btn btn-default pull-right hidden-xs configurar_listas"><i class="fa fa-cog"></i></button>
                    </ul>
                    <div class="tab-content">
                      <div id="tab_lista1" class="tab-pane active">
                        <div class="row">
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia" value="<%= porc_ganancia %>" type="text" class="form-control number calc_total" name="porc_ganancia" <%= (!edicion)?"disabled":"" %> />
                            </div>
                          </div>
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
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
                              <label class="control-label">Final</label>
                              <input id="articulo_precio_final" value="<%= precio_final %>" type="text" class="form-control number" name="precio_final" <%= (!edicion)?"disabled":"" %>/>
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
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_2" value="<%= porc_ganancia_2 %>" type="text" class="form-control number calc_total" name="porc_ganancia_2" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
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
                              <label class="control-label">Final</label>
                              <input id="articulo_precio_final_2" value="<%= precio_final_2 %>" type="text" class="form-control number" name="precio_final_2" <%= (!edicion)?"disabled":"" %>/>
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
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_3" value="<%= porc_ganancia_3 %>" type="text" class="form-control number calc_total" name="porc_ganancia_3" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
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
                              <label class="control-label">Final</label>
                              <input id="articulo_precio_final_3" value="<%= precio_final_3 %>" type="text" class="form-control number" name="precio_final_3" <%= (!edicion)?"disabled":"" %>/>
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
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_4" value="<%= porc_ganancia_4 %>" type="text" class="form-control number calc_total" name="porc_ganancia_4" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
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
                              <label class="control-label">Final</label>
                              <input id="articulo_precio_final_4" value="<%= precio_final_4 %>" type="text" class="form-control number" name="precio_final_4" <%= (!edicion)?"disabled":"" %>/>
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
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_5" value="<%= porc_ganancia_5 %>" type="text" class="form-control number calc_total" name="porc_ganancia_5" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
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
                              <label class="control-label">Final</label>
                              <input id="articulo_precio_final_5" value="<%= precio_final_5 %>" type="text" class="form-control number" name="precio_final_5" <%= (!edicion)?"disabled":"" %>/>
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
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
                            <div class="form-group">
                              <label class="control-label">% Marcado</label>
                              <input id="articulo_porc_ganancia_6" value="<%= porc_ganancia_6 %>" type="text" class="form-control number calc_total" name="porc_ganancia_6" <%= (!edicion)?"disabled":"" %>/>
                            </div>
                          </div>
                          <div class="col-md-2" style="<%= (!mostrar_costos)?'display:none':'' %>">
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
                              <label class="control-label">Final</label>
                              <input id="articulo_precio_final_6" value="<%= precio_final_6 %>" type="text" class="form-control number" name="precio_final_6" <%= (!edicion)?"disabled":"" %>/>
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
      </div>
    </div>

    <% if (control.check("proveedores")>0) { %>

      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <% if (edicion) { %>
                  <div class="form-inline row m-b clearfix">
                    <div class="form-group col-sm-8">
                      <label class="control-label">Proveedor</label>
                      <div class="input-group" style="width: 100%">
                        <select id="articulo_proveedores" class="w100p"></select>
                        <span class="input-group-btn w1p">
                          <button tabindex="-1" class="btn btn-info agregar_proveedor">+</button>  
                        </span>
                      </div>
                    </div>
                    <div class="form-group col-sm-4">
                      <label class="control-label">C&oacute;digo Art.</label>
                      <div class="input-group">
                        <input id="proveedor_codigo" value="" type="text" class="form-control"/>
                        <span class="input-group-btn">
                          <a id="proveedor_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                        </span>
                      </div>
                    </div>
                  </div>
                <% } %>
                <div class="">
                  <table id="proveedores_tabla" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th>Proveedor</th>
                        <th>C&oacute;digo Art.</th>
                        <% if (edicion) { %>
                          <th class="w25"></th>
                          <th class="w25"></th>
                        <% } %>
                      </tr>
                    </thead>
                    <tbody>
                      <% for(var i=0;i< proveedores.length;i++) { %>
                        <% var p = proveedores[i] %>
                        <tr data-id="<%= p.id_proveedor %>">
                          <td><%= p.nombre %></td>
                          <td><%= p.codigo %></td>
                          <% if (edicion) { %>
                            <td><i class='fa fa-pencil cp editar_proveedor'></i></td>
                            <td><i class='fa fa-times eliminar_proveedor text-danger cp'></i></td>
                          <% } %>
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
    <% } %>

    <% if (!isEmpty(DOMINIO) || !isEmpty(ML_ACCESS_TOKEN)) { %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <?php include_once("articulos_envio.php"); ?>
          <?php include_once("articulos_variantes.php"); ?>
          <?php include_once("articulos_componentes.php"); ?>
          <?php include_once("articulos_relaciones.php"); ?>
          <?php include_once("articulos_seo.php"); ?>  
        </div>
      </div>
    <% } %>

    <% if (edicion || perfil_web) { %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    <% } %>
  </div>
</div>