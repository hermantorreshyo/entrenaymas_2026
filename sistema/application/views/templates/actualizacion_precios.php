<script type="text/template" id="actualizacion_precios_resultados_template">
  <div>
    <?php include("art/articulos_header.php") ?>
    <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
        <?php $active = "actualizacion_precios"; include("art/articulos_menu.php"); ?>
        <div class="panel-heading clearfix">
          <div class="row pl10 pr10 pt10">
            <div class="col-md-3 col-xs-12 pr5 pl5">
              <label class="control-label bold">
                <span class="numero-paso">1</span>
                ¿Que desea actualizar?
              </label>
              <div class="form-group">
                <select id="actualizacion_precios_campo" class="form-control">
                  <option value="">Seleccione</option>
                  <% if (ID_EMPRESA == 342) { %>
                    <option value="CI">Costo Neto sin dto.</option>
                  <% } else { %>
                    <option value="C">Costo Neto</option>
                  <% } %>
                  <option value="DP">Desc. Proveedor</option>
                  <option value="P">Lista 1</option>
                  <option value="P2">Lista 2</option>
                  <option value="P3">Lista 3</option>
                  <option value="P4">Lista 4</option>
                  <option value="D">Descuento 1</option>
                  <option value="D2">Descuento 2</option>
                  <option value="D3">Descuento 3</option>
                  <option value="D4">Descuento 4</option>
                  <option value="M1">Marcacion 1</option>
                  <option value="M2">Marcacion 2</option>
                  <option value="M3">Marcacion 3</option>
                  <option value="M4">Marcacion 4</option>
                </select>
              </div>         
            </div>
            <div class="col-md-3 col-xs-12 pr5 pl5">
              <label class="control-label bold">
                <span class="numero-paso">2</span>
                ¿Cuánto?
              </label>
              <div class="form-group">
                <div class="row">
                  <div class="col-xs-6 pr0">
                    <select id="actualizacion_precios_tipo" class="form-control">
                      <option value="P">Porcentaje</option>
                      <option value="F">Suma Fija</option>
                      <option value="I">Reemplazar</option>
                    </select>
                  </div>
                  <div class="col-xs-6 pl0">
                    <input type="text" class="form-control" id="actualizacion_precios_monto" placeholder="Cantidad" />  
                  </div>
                </div>
              </div>         
              <div id="actualizacion_precios_base_cont" style="display: none" class="form-group">
                <select id="actualizacion_precios_base" class="form-control"></select>          
              </div>
            </div>

            <div class="col-md-3 col-xs-12 pr5 pl5">
              <label class="control-label bold">
                <span class="numero-paso">3</span>
                ¿Redondear resultado?
              </label>
              <div class="form-group">
                <select id="actualizacion_precios_redondeo" class="form-control">
                  <option value="1">Precio Redondo (sin centavos)</option>
                  <option value="2">50 Centavos</option>
                  <?php /*<option value="-20">Redondeo a $5</option>*/ ?>
                  <option value="0">Sin redondeo</option>
                </select>
              </div>
            </div>

            <div class="col-md-3 col-xs-12 pr5 pl5">
              <label class="control-label bold">
                <span class="numero-paso">4</span>
                Actualizar!
              </label>
              <div class="form-group">
                <div class="input-group">
                  <button class="btn btn-default generar mr5">Previsualizar</button>
                  <button class="btn btn-success confirmar">Confirmar</button>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="advanced-search-div bg-light dk" style="display:block">
          <div class="wrapper oh">
            <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
            <div class="row pl10 pr10">

              <% if (control.check("proveedores")>0) { %>
                <div class="col-md-6 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-btn">
                        <button id="actualizacion_precios_not_proveedores" data-toggle="tooltip" title="Si esta marcado, filtra aquellos proveedores que NO sean los seleccionados." class="btn btn-default"><i class="fa fa-ban"></i></button>
                      </span>
                      <select id="actualizacion_precios_buscar_proveedores" class="w100p"></select>
                    </div>
                  </div>
                </div>
              <% } %>

              <div class="col-md-6 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select id="actualizacion_precios_buscar_categorias" class="w100p"></select>
                </div>
              </div>

              <% if ((MEGASHOP == 1 || ID_EMPRESA == 224 || ID_EMPRESA == 421) && ID_SUCURSAL == 0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <select id="actualizacion_precios_sucursal" class="form-control">
                    <option value="0">Sucursal</option>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>                      
                  </select>          
                </div>
              <% } %>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <input type="text" id="actualizacion_precios_buscar" placeholder="Nombre o código" autocomplete="off" class="form-control">
              </div>

              <% if (control.check("marcas")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="actualizacion_precios_buscar_marcas" class="w100p"></select>
                  </div>
                </div>
              <% } %>
              <% if (control.check("departamentos_comerciales")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="actualizacion_precios_buscar_departamentos" class="w100p no-model">
                      <option selected value="0"></option>
                    </select>
                  </div>
                </div>
              <% } %>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="input-group">
                    <select class="form-control no-model pl5 pr0" id="actualizacion_precios_fecha_tipo">
                      <option value="igual">=</option>
                      <option value="mayor">></option>
                      <option value="menor"><</option>
                    </select>
                    <span class="input-group-btn w70p">
                      <input type="text" placeholder="Fecha Modif." class="input form-control no-model" id="actualizacion_precios_fecha"/>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <button id="actualizacion_precios_buscar_avanzada_btn" class="btn btn-default btn-dark btn-block"><i class="fa fa-search m-r-xs"></i> Buscar</button>
                </div>                
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="actualizacion_precios_tabla" class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <td rowspan="2" style="vertical-align: middle;" class="text-center">Codigo</td>
                  <td rowspan="2" style="vertical-align: middle;" class="text-center">Descripcion</td>
                  <td colspan="5" style="border-left: solid 1px #dddddd" class="bold text-center">PRECIOS ACTUALES</td>
                  <td colspan="5" style="border-left: solid 1px #dddddd" class="bold text-center">PRECIOS NUEVOS</td>
                </tr>
                <tr>
                  <td class="tac" style="border-left: solid 1px #dddddd">Neto</td>
                  <td class="tac">Lista 1</td>
                  <td class="tac">Lista 2</td>
                  <td class="tac">Lista 3</td>
                  <td class="tac" style="border-right: solid 1px #dddddd">Lista 4</td>
                  <td class="tac">Neto</td>
                  <td class="tac">Lista 1</td>
                  <td class="tac">Lista 2</td>
                  <td class="tac">Lista 3</td>
                  <td class="tac">Lista 4</td>
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

<script type="text/template" id="actualizacion_precios_item_resultados_template">
  <td><%= codigo %></td>
  <td><%= nombre %></td>
  <td class="tar" style="border-left: solid 1px #dddddd"><%= Number(costo_neto).format() %></td>
  <td class="tar"><%= Number(precio_final_dto).format() %></td>
  <td class="tar"><%= Number(precio_final_dto_2).format() %></td>
  <td class="tar"><%= Number(precio_final_dto_3).format() %></td>
  <td class="tar" style="border-right: solid 1px #dddddd"><%= Number(precio_final_dto_4).format() %></td>
  <td class="tar bold bg-light lt"><%= Number(costo_nuevo).format() %></td>
  <td class="tar bold bg-light lt"><%= Number(precio_nuevo).format() %></td>
  <td class="tar bold bg-light lt"><%= Number(precio_nuevo_2).format() %></td>
  <td class="tar bold bg-light lt"><%= Number(precio_nuevo_3).format() %></td>
  <td class="tar bold bg-light lt"><%= Number(precio_nuevo_4).format() %></td>
</script>