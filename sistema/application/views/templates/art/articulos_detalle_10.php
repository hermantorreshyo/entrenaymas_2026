<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-3" style="<%= (TOQUE == 1)?'display:none':'' %>">
                  <div class="form-group">
                    <label class="control-label">Codigo Interno</label>
                    <% if (edicion) { %>
                      <input type="text" required name="codigo" id="articulo_codigo" value="<%= codigo %>" class="form-control"/>
                    <% } else { %>
                      <span><%= codigo %></span>
                    <% } %>
                  </div>
                </div>
                <div class="<%= (TOQUE == 1)?'col-xs-12':'col-md-9' %>">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <% if (edicion) { %>
                      <input type="text" required name="nombre" id="articulo_nombre" value="<%= nombre %>" class="form-control"/>
                    <% } else { %>
                      <span><%= nombre %></span>
                    <% } %>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Descripci&oacute;n</label>
                <input type="text" name="descripcion" id="articulo_descripcion" value="<%= descripcion %>" class="form-control"/>
              </div>
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label class="control-label">Categoria</label>
                    <div class="input-group">
                      <select id="articulo_rubros" class="w100p"></select>
                      <span class="input-group-btn">
                        <button tabindex="-1" class="btn btn-info agregar_rubro">+</button>  
                      </span>
                    </div>
                  </div>  
                </div>
                <% if (control.check("marcas") > 0) { %>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Marca</label>
                      <div class="input-group">
                        <select id="articulo_marcas" class="w100p"></select>
                        <span class="input-group-btn">
                          <button tabindex="-1" class="btn btn-info agregar_marca">+</button>  
                        </span>
                      </div>
                    </div>
                  </div>
                <% } %>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Medida</label>
                    <select class="form-control" id="articulo_unidades" name="unidad">
                        <option <%= (unidad=="U")?"selected":"" %> value="U">Unidades</option>
                        <option <%= (unidad=="M")?"selected":"" %> value="M">Mitades</option>
                        <option <%= (unidad=="K")?"selected":"" %> value="K">Kilogramos</option>
                        <option <%= (unidad=="L")?"selected":"" %> value="L">Litros</option>
                        <option <%= (unidad=="C3")?"selected":"" %> value="C3">Cm 3</option>
                        <% if (TOQUE == 1) { %>
                          <option <%= (unidad=="H4")?"selected":"" %> value="H4">Helado 1/4 Kg.</option>
                          <option <%= (unidad=="H2")?"selected":"" %> value="H2">Helado 1/2 Kg.</option>
                          <option <%= (unidad=="H1")?"selected":"" %> value="H1">Helado 1 Kg.</option>
                        <% } %>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3" style="<%= (TOQUE == 1)?"display:none":"" %>">
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
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Precio Final</label>
                    <input id="articulo_precio_final" value="<%= precio_final %>" type="text" class="form-control number" name="precio_final"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">% Descuento</label>
                    <input id="articulo_porc_bonif" value="<%= porc_bonif %>" type="text" class="form-control number" name="porc_bonif"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Precio c/dto</label>
                    <input disabled id="articulo_precio_final_dto" value="<%= precio_final_dto %>" type="text" class="form-control number" name="precio_final_dto"/>
                  </div>
                </div>
                <% if (TOQUE == 1) { %>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Tiempo Preparacion (min)</label>
                      <input id="articulo_custom_1" value="<%= custom_1 %>" type="text" class="form-control number" name="custom_1"/>
                    </div>
                  </div>
                <% } %>
              </div>

              <?php
              single_upload(array(
                  "name"=>"path",
                  "label"=>"Imagen Principal",
                  "url"=>"/sistema/articulos/function/save_image/",
                  "width"=>(isset($empresa->config["producto_image_width"]) ? $empresa->config["producto_image_width"] : 256),
                  "height"=>(isset($empresa->config["producto_image_height"]) ? $empresa->config["producto_image_height"] : 256),
              )); ?>  

              <% if (TOQUE == 0) { %>
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" id="articulo_no_totalizar_reparto" name="no_totalizar_reparto" class="checkbox" value="1" <%= (no_totalizar_reparto == 1)?"checked":"" %> ><i></i>
                        Mostrar art&iacute;culo en 'Cocina' para ser preparado.
                    </label>
                  </div>
                </div>
              <% } %>

              <div class="form-group">
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
      </div>
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Variantes",
                    "en"=>"Variantes",
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
                    "es"=>"Puede agregar diferentes ingredientes, condimentos, sabores, etc. y al momento de cargar el pedido, pueda seleccionarlos f&aacute;cilmente.",
                    "en"=>"Puede agregar diferentes ingredientes, condimentos, sabores, etc. para que al momento de cargar el pedido pueda seleccionarlos facilmente.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (ingredientes.length > 0) ? "display:block":"" %>">
            <div class="padder">
              <div class="clearfix">
                <div class="col-xs-12 col-sm-3">
                  <input type="hidden" id="articulo_ingrediente_activo" value="1" class="form-control no-model">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" id="articulo_ingrediente_nombre" class="form-control no-model">
                  </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Valores</label>
                    <select multiple id="articulo_ingrediente_valores" style="width: 100%"></select>
                  </div>
                </div>
                <div class="col-xs-9 col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Costo</label>
                    <input type="text" id="articulo_ingrediente_adicional" value="0" class="form-control no-model">
                  </div>
                </div>
                <div class="col-xs-3 col-sm-2">
                  <div class="form-group">
                    <label class="control-label db">&nbsp;</label>
                    <a id="articulo_ingrediente_agregar" class="btn btn-block btn-info">Agregar</a>
                  </div>
                </div>
              </div>
              <div class="">
                <table id="ingredientes_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="w25"></th>
                      <th>Nombre</th>
                      <th>Valores</th>
                      <th>Costo</th>
                      <th class="w25"></th>
                      <th class="w25"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< ingredientes.length;i++) { %>
                      <% var p = ingredientes[i] %>
                      <tr>
                        <td><label class='i-checks'><input type='checkbox' <%= (p.activo == 1)?"checked":"" %> class='checkbox' value='1'><i></i></label></td>
                        <td><%= p.nombre %></td>
                        <td><%= p.valores %></td>
                        <td><%= p.adicional %></td>
                        <td><i class='fa fa-pencil cp editar_ingrediente'></i></td>
                        <td><i class='fa fa-times eliminar_ingrediente text-danger cp'></i></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="panel-body expand">
            <div class="padder">
              <div class="w100p dt">
                <div class="dtr">
                  <div class="dtc">
                    <div class="form-group">
                      <label class="control-label bold mb0">Propiedad</label>  
                    </div>
                  </div>
                  <div class="dtc">
                    <div class="form-group">
                      <label class="control-label bold mb0">Variantes</label>  
                    </div>
                  </div>
                  <div class="dtc"></div>
                </div>
              </div>
              <div class="w100p dt" id="articulo_propiedades"></div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <button class="btn btn-white btn-block nueva_propiedad">Agregar Propiedad</button>
                  </div>
                </div>
              </div>
              <div class="form-group" id="articulo_variantes_tabla_cont" style="display: none;">
                <div class="b-a table-responsive">
                  <table id="articulo_variantes_tabla" class="table table-small table-striped sortable m-b-none default footable">
                    <thead>
                      <tr>
                        <th>Variante</th>
                        <th>Stock</th>
                        <th>Precio</th>
                      </tr>
                    </thead>
                    <tbody class="tbody"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <% if (control.check("stock")>0) { %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <div class="panel panel-default">
            <div class="panel-heading bold">
              Stock
            </div>
            <div class="panel-body">
              <div class="padder">
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" id="articulo_usa_stock" name="usa_stock" value="1" <%= (usa_stock == 1)?"checked":"" %> ><i></i>
                      Gestionar el stock de este producto
                    </label>
                  </div>          
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        <?php echo lang(array(
                          "es"=>"Stock actual",
                          "en"=>"Stock",
                        )); ?>
                      </label>
                      <input type="text" name="stock" id="articulo_stock" value="<%= stock %>" class="form-control"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <% } %>

    <div class="row" style="display:none">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Moneda</label>
                    <select id="articulo_monedas" class="form-control" name="moneda">
                      <% for(var i=0;i< window.monedas.length;i++) { %>
                        <% var o = monedas[i]; %>
                        <option <%= (o.signo == moneda)?"selected":"" %> value="<%= o.signo %>"><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Costo Neto</label>
                    <input type="text" class="form-control number calc_total" id="articulo_costo_neto" name="costo_neto" value="<%= costo_neto %>"/>
                  </div>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3" style="display:none">
                  <div class="form-group">
                    <label class="control-label">IVA</label>
                    <input id="articulo_iva" disabled value="<%= Number(costo_neto * porc_iva / 100).toFixed(2) %>" type="text" class="form-control" name="costo_iva"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Costo Final</label>
                    <input type="text" class="form-control number calc_total" id="articulo_costo_final" name="costo_final" value="<%= Number(costo_final).toFixed(2) %>"/>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="tab-container">
                  <ul class="nav nav-tabs nav-tabs-4" role="tablist">
                    <li class="active">
                      <a href="#tab_lista1" role="tab" data-toggle="tab">Lista 1</a>
                    </li>
                    <li>
                      <a href="#tab_lista2" role="tab" data-toggle="tab">Lista 2</a>
                    </li>
                    <li>
                      <a href="#tab_lista3" role="tab" data-toggle="tab">Lista 3</a>
                    </li>
                  </ul>
                  <div class="tab-content">
                    <div id="tab_lista1" class="tab-pane active">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">% Marcado</label>
                            <input id="articulo_porc_ganancia" value="<%= porc_ganancia %>" type="text" class="form-control number calc_total" name="porc_ganancia"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Ganancia</label>
                            <input id="articulo_ganancia" disabled value="<%= ganancia %>" type="text" class="form-control" name="ganancia"/>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Neto</label>
                            <input id="articulo_precio_neto" disabled value="<%= precio_neto %>" type="text" class="form-control" name="precio_neto"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Final</label>
                            <input id="articulo_precio_final" value="<%= precio_final %>" type="text" class="form-control number" name="precio_final"/>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">% Descuento</label>
                            <input id="articulo_porc_bonif" value="<%= porc_bonif %>" type="text" class="form-control number" name="porc_bonif"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Final c/dto.</label>
                            <input disabled id="articulo_precio_final_dto" value="<%= precio_final_dto %>" type="text" class="form-control number" name="precio_final_dto"/>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="tab_lista2" class="tab-pane">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">% Marcado</label>
                            <input id="articulo_porc_ganancia_2" value="<%= porc_ganancia_2 %>" type="text" class="form-control number calc_total" name="porc_ganancia_2"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Ganancia</label>
                            <input id="articulo_ganancia_2" disabled value="<%= ganancia_2 %>" type="text" class="form-control" name="ganancia_2"/>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Neto</label>
                            <input id="articulo_precio_neto_2" disabled value="<%= precio_neto_2 %>" type="text" class="form-control" name="precio_neto_2"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Final</label>
                            <input id="articulo_precio_final_2" value="<%= precio_final_2 %>" type="text" class="form-control number" name="precio_final_2"/>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">% Descuento</label>
                            <input id="articulo_porc_bonif_2" value="<%= porc_bonif_2 %>" type="text" class="form-control number" name="porc_bonif_2"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Final c/dto.</label>
                            <input disabled id="articulo_precio_final_dto_2" value="<%= precio_final_dto_2 %>" type="text" class="form-control number" name="precio_final_dto_2"/>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="tab_lista3" class="tab-pane">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">% Marcado</label>
                            <input id="articulo_porc_ganancia_3" value="<%= porc_ganancia_3 %>" type="text" class="form-control number calc_total" name="porc_ganancia_3"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Ganancia</label>
                            <input id="articulo_ganancia_3" disabled value="<%= ganancia_3 %>" type="text" class="form-control" name="ganancia_3"/>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Neto</label>
                            <input id="articulo_precio_neto_3" disabled value="<%= precio_neto_3 %>" type="text" class="form-control" name="precio_neto_3"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Final</label>
                            <input id="articulo_precio_final_3" value="<%= precio_final_3 %>" type="text" class="form-control number" name="precio_final_3"/>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">% Descuento</label>
                            <input id="articulo_porc_bonif_3" value="<%= porc_bonif_3 %>" type="text" class="form-control number" name="porc_bonif_3"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Precio Final c/dto.</label>
                            <input disabled id="articulo_precio_final_dto_3" value="<%= precio_final_dto_3 %>" type="text" class="form-control number" name="precio_final_dto_3"/>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
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