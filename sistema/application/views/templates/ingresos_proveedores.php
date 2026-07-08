<script type="text/template" id="ingreso_proveedor_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Ingreso de mercaderia</h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="centrado">
      <div class="panel panel-default pull-in">
        <div class="panel-heading font-bold">
          Datos de Comprobante       
        </div>
        <div class="panel-body pl0 pr0">
          <div class="clearfix">
            <div class="col-md-3 col-sm-6">
              <label>Proveedor </label>
              <div class="input-group">
                <input type="text" class="dn" id="ingreso_proveedor_id_proveedor" value=""/>
                <input title="Ingrese el codigo de Proveedor o comience a escribir parte del nombre" type="text" class="form-control action" id="ingreso_proveedor_codigo_proveedor" placeholder="Nombre o codigo de proveedor" value=""/>
                <span class="input-group-btn">
                  <button title="Atajo: F2 = Buscar" id="ingreso_proveedor_buscar_proveedor" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                </span>
              </div>      
            </div>
            <div class="col-md-2 col-sm-6">
              <label>Fecha de ingreso</label>
              <div class="input-group">
                <input type="text" title="Fecha de emision de comprobante" id="ingreso_proveedor_fecha" name="fecha" class="form-control action">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <% if (control.check("almacenes")>0) { %>
              <div class="col-md-3 col-sm-6">
                <label>Sucursal</label>
                <select class="form-control action no-model" id="ingreso_proveedor_almacenes">
                  <% if (window.almacenes.length > 1) { %><option value="0">Seleccione</option><% } %>
                  <% for(var i=0;i< almacenes.length;i++) { %>
                    <% var almacen = almacenes[i] %>
                    <option <%= (id_almacen == almacen.id)?"selected":"" %> value="<%= almacen.id %>"><%= almacen.nombre %></option>
                  <% } %>
                </select>
              </div>
            <% } %>
            <div class="col-md-2 col-sm-6">
              <label>N&uacute;mero de remito</label>
              <input type="text" name="numero_remito" value="<%= numero_remito %>" class="form-control action" id="ingreso_proveedor_numero"/>
            </div>
          </div>
        </div>
      </div>

      <div class="panel panel-info pull-in">
        <div class="panel-heading font-bold">Ingreso de productos</div>
        <div class="panel-body">
          <div class="">

            <input type="hidden" id="ingreso_proveedor_id_articulo"/>

            <% if (MEGASHOP == 1) { %>
              <div class="clearfix <%= (estado == 1)?'dn':'' %>">
                <div class="col-sm-5 col-xs-12 p0 form-group">
                  <div class="col-sm-4 p0">
                    <label class="text-muted clearfix mb0">
                      <div class="radio fl mb0 mt0">
                        <label class="i-checks">
                          <input type="radio" name="tipo_codigo" value="INTERNO" checked="">
                          <i></i>
                          Int.
                        </label>
                      </div>
                      <div class="radio fl mb0 mt0 ml5">
                        <label class="i-checks">
                          <input type="radio" name="tipo_codigo" value="PROVEEDOR">
                          <i></i>
                          Prov.
                        </label>
                      </div>
                    </label>
                    <input type="text" placeholder="C&oacute;digo..." class="form-control action no-model" id="ingreso_proveedor_codigo_articulo"/>
                  </div>
                  <div class="col-sm-8 p0">
                    <label class="text-muted">Descripci&oacute;n</label>
                    <div class="input-group">
                      <input disabled type="text" class="form-control action no-model" id="ingreso_proveedor_item_nombre"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" id="ingreso_proveedor_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-xs-12 p0 form-group">
                  <div class="col-sm-3 p0">
                    <label class="text-muted">Cant.</label>
                    <input type="text" class="form-control action no-model" value="1" id="ingreso_proveedor_item_cantidad"/>
                  </div>                  
                  <div class="col-sm-2 p0">
                    <label class="text-muted">Bonif.</label>
                    <select id="ingreso_proveedor_item_bonificado" class="form-control action no-model">
                      <option value="0">No</option>
                      <option value="1">Si</option>
                    </select>
                  </div>
                  <div class="col-sm-2 p0">
                    <label class="text-muted">Edit. Precios</label>
                    <select id="ingreso_proveedor_item_no_editar_precios" class="form-control action no-model">
                      <option value="0">Si</option>
                      <option value="1">No</option>
                    </select>
                  </div>
                  <div class="col-sm-2 p0">
                    <label class="text-muted">Edit. Stock</label>
                    <select id="ingreso_proveedor_item_no_editar_stock" class="form-control action no-model">
                      <option value="0">Si</option>
                      <option value="1">No</option>
                    </select>
                  </div>                  
                </div>
              </div>

              <div class="clearfix <%= (estado == 1)?'dn':'' %>">
                <div class="col-sm-6 col-xs-12 p0 form-group">
                  <div class="col-sm-2 p0">
                    <label class="text-muted">C. Neto</label>
                    <input type="text" class="form-control action no-model" value="0.00" id="ingreso_proveedor_item_costo_neto_inicial"/>
                  </div>                  
                  <div class="col-sm-2 p0">
                    <label class="text-muted">% Dtos.</label>
                    <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_dto_prov"/>
                  </div>
                  <div class="col-sm-2 p0">
                    <label class="text-muted">&nbsp;</label>
                    <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_dto_prov_2"/>
                  </div>
                  <div class="col-sm-2 p0">
                    <label class="text-muted">&nbsp;</label>
                    <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_dto_prov_3"/>
                  </div>
                  <div class="col-sm-2 p0">
                    <label class="text-muted">&nbsp;</label>
                    <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_dto_prov_4"/>
                  </div>
                  <div class="col-sm-2 p0">
                    <label class="text-muted">&nbsp;</label>
                    <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_dto_prov_5"/>
                  </div>
                </div>
                <div class="col-sm-3 col-xs-12 p0 form-group">
                  <div class="col-sm-4 p0">
                    <label class="text-muted">Neto c/Dto</label>
                    <input type="text" class="form-control action no-model" disabled id="ingreso_proveedor_item_costo_neto"/>
                  </div>
                  <div class="col-sm-4 p0">
                    <label class="text-muted">% IVA</label>
                    <select id="ingreso_proveedor_alicuotas_iva" class="form-control action no-model">
                      <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                      <% var o = alicuotas_iva[i]; %>
                        <option value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                  <div class="col-sm-4 p0">
                    <label class="text-muted">Costo Final</label>
                    <input type="text" class="form-control action no-model" <%= ((MEGASHOP == 1 || ID_EMPRESA == 421) ? "disabled":"") %> value="0.00" id="ingreso_proveedor_item_costo_final"/>
                  </div>
                </div>
                <% if (ID_EMPRESA != 868) { %>
                  <div class="col-sm-3 col-xs-12 p0 form-group">
                    <div class="col-sm-6 p0">
                      <label class="text-muted">% Gan.</label>
                      <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_porc_ganancia"/>
                    </div>
                    <div class="col-sm-6 p0">
                      <label class="text-muted">P. Venta</label>                  
                      <div class="input-group">
                        <input type="text" class="form-control no-model" id="ingreso_proveedor_precio_final"/>
                        <input type="text" disabled class="dn form-control no-model" id="ingreso_proveedor_precio_neto"/>
                        <input type="text" disabled class="dn form-control no-model" id="ingreso_proveedor_item_subtotal" placeholder="Subtotal"/>
                        <span class="input-group-btn">
                          <button title="Ingresar linea" id="ingreso_proveedor_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                <% } %>
              </div>

              <% if (ID_EMPRESA == 868) { %>
                <div class="clearfix <%= (estado == 1)?'dn':'' %>">
                  <div class="col-sm-3 col-xs-12 p0 form-group">
                    <div class="col-sm-6 p0">
                      <label class="text-muted">% Gan. Central</label>
                      <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_porc_ganancia"/>
                    </div>
                    <div class="col-sm-6 p0">
                      <label class="text-muted">Pr. Central</label>
                      <input type="text" class="form-control no-model" id="ingreso_proveedor_precio_final_central"/>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xs-12 p0 form-group">
                    <div class="col-sm-5 p0">
                      <label class="text-muted">% Gan. Suc.</label>
                      <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_porc_ganancia_sucursal"/>
                    </div>
                    <div class="col-sm-7 p0">
                      <label class="text-muted">P. Venta</label>                  
                      <div class="input-group">
                        <input type="text" class="form-control no-model" id="ingreso_proveedor_precio_final"/>
                        <input type="text" disabled class="dn form-control no-model" id="ingreso_proveedor_precio_neto"/>
                        <input type="text" disabled class="dn form-control no-model" id="ingreso_proveedor_item_subtotal" placeholder="Subtotal"/>
                        <span class="input-group-btn">
                          <button title="Ingresar linea" id="ingreso_proveedor_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>                  
                </div>
              <% } %>

            <% } else { %>

            <div class="clearfix <%= (estado == 1)?'dn':'' %>">
              <div class="col-sm-4 col-xs-12 p0 form-group">
                <div class="col-sm-5 p0">
                  <label class="text-muted clearfix mb0">
                    <div class="radio fl mb0 mt0">
                      <label class="i-checks">
                        <input type="radio" name="tipo_codigo" value="INTERNO" checked="">
                        <i></i>
                        Int.
                      </label>
                    </div>
                    <div class="radio fl mb0 mt0 ml5">
                      <label class="i-checks">
                        <input type="radio" name="tipo_codigo" value="PROVEEDOR">
                        <i></i>
                        Prov.
                      </label>
                    </div>
                  </label>
                  <input type="text" placeholder="C&oacute;digo..." class="form-control action no-model" id="ingreso_proveedor_codigo_articulo"/>
                </div>
                <div class="col-sm-7 p0">
                  <label class="text-muted">Descripci&oacute;n</label>
                  <div class="input-group">
                    <input disabled type="text" class="form-control action no-model" id="ingreso_proveedor_item_nombre"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" id="ingreso_proveedor_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-8 col-xs-12 p0 form-group">
                <div class="col-sm-2 p0">
                  <label class="text-muted">Cant.</label>
                  <input type="text" class="form-control action no-model" value="1" id="ingreso_proveedor_item_cantidad"/>
                </div>
                <input type="hidden" class="form-control action no-model" disabled id="ingreso_proveedor_item_costo_neto_inicial"/>
                <input type="hidden" class="form-control action no-model" disabled id="ingreso_proveedor_item_dto_prov"/>
                <input type="hidden" class="form-control action no-model" disabled id="ingreso_proveedor_item_dto_prov_2"/>
                <input type="hidden" class="form-control action no-model" disabled id="ingreso_proveedor_item_dto_prov_3"/>
                <input type="hidden" class="form-control action no-model" disabled id="ingreso_proveedor_item_dto_prov_4"/>
                <input type="hidden" class="form-control action no-model" disabled id="ingreso_proveedor_item_dto_prov_5"/>
                <input type="hidden" class="form-control action no-model" disabled id="ingreso_proveedor_item_costo_neto"/>

                <select id="ingreso_proveedor_alicuotas_iva" class="dn no-model">
                  <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                  <% var o = alicuotas_iva[i]; %>
                    <option value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                  <% } %>
                </select>                

                <input type="hidden" class="form-control action no-model" disabled id="ingreso_proveedor_item_bonificado"/>
                <div class="col-sm-2 p0">
                  <label class="text-muted">Costo Final</label>
                  <input type="text" class="form-control action no-model" <%= ((MEGASHOP == 1 || ID_EMPRESA == 421) ? "disabled":"") %> value="0.00" id="ingreso_proveedor_item_costo_final"/>
                </div>
                <div class="col-sm-2 p0">
                  <label class="text-muted">% Gan.</label>
                  <input type="text" class="form-control action no-model" placeholder="0 %" id="ingreso_proveedor_item_porc_ganancia"/>
                </div>
                <div class="col-sm-3 p0">
                  <div class="col-sm-6 col-xs-12 p0">
                    <label class="text-muted">Prec</label>
                    <select id="ingreso_proveedor_item_no_editar_precios" class="form-control action no-model">
                      <option value="0">Si</option>
                      <option value="1">No</option>
                    </select>
                  </div>
                  <div class="col-sm-6 col-xs-12 p0">
                    <label class="text-muted">Stk</label>
                    <select id="ingreso_proveedor_item_no_editar_stock" class="form-control action no-model">
                      <option value="0">Si</option>
                      <option value="1">No</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3 p0">
                  <label class="text-muted">P. Venta</label>                  
                  <div class="input-group">
                    <input type="text" class="form-control no-model" id="ingreso_proveedor_precio_final"/>
                    <input type="text" disabled class="dn form-control no-model" id="ingreso_proveedor_precio_neto"/>
                    <input type="text" disabled class="dn form-control no-model" id="ingreso_proveedor_item_subtotal" placeholder="Subtotal"/>
                    <span class="input-group-btn">
                      <button title="Ingresar linea" id="ingreso_proveedor_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
                </div>
              </div>

            <% } %>

            <div class="b-a" style="overflow: auto; height: 250px; margin-top: 15px;">
              <table id="tabla_items" class="table table-small sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th class="w75">Etiq.</th>
                    <th>Cod.</th>
                    <th class="w75">Cant.</th>
                    <th>Descripci&oacute;n</th>
                    <th class="w100">C. Neto</th>
                    <th class="w100">C. Final</th>
                    <th class="w100">P. Venta</th>
                    <th class="w100">Subtotal</th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t">
              <div class="col-md-6">
              </div>
              <div class="col-md-6">
                <div class="form-horizontal pull-in totales">
                  <div class="form-group">
                    <label class="control-label col-xs-8">Neto:</label>
                    <div class="col-xs-4">
                      <input type="text" disabled class="no-input" id="ingreso_proveedor_subtotal_neto"/>
                    </div>
                  </div>
                  <div class="line line-dashed b-b"></div>
                  <div class="form-group">
                    <label class="control-label col-xs-6 fs26">Total:</label>
                    <div class="col-xs-6">
                      <input type="text" disabled class="no-input fs26 bold" id="ingreso_proveedor_total"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

            <div class="oh m-t">
              <h4>Notas y Observaciones <i title="Click para ayuda" class="observaciones_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></h4>
              <div>
                <% if (estado == 0) { %>
                  <textarea style="height: 100px" id="ingreso_proveedor_observaciones" name="observaciones" placeholder="Puede escribir una nota u observacion que aparecer&aacute; al pie de p&aacute;gina del comprobante..." class="form-control"><%= ((id != undefined)?observaciones:OBSERVACIONES).replaceAll("<br />","\n") %></textarea>
                <% } else { %>
                  <p><%= observaciones %></p>
                <% } %>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>

          </div>
        </div>
      </div>

      <div class="oh m-t m-b tar pull-in">
        <% if (id != undefined && id != 0) { %>
          <button class="btn btn-primary imprimir btn-addon pull-left m-r"><i class="icon glyphicon glyphicon-print"></i>Imprimir</button>
        <% } %>
        <% if (estado == 0) { %>
          <button class="btn btn-default aceptar btn-addon"><i class="icon fa fa-plus"></i>Guardar borrador</button>
          <button class="btn btn-success confirmar btn-addon"><i class="icon fa fa-plus"></i>Confirmar</button>
        <% } %>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="ingreso_proveedor_item_tabla_template">
  <td class="pr0">
    <select class="form-control pl0 pr0" name="tipo_etiqueta">
      <option <%= (tipo_etiqueta == 0) ? "selected":"" %> value="0">No</option>
      <option <%= (tipo_etiqueta == 1) ? "selected":"" %> value="1">S/Prec</option>
      <option <%= (tipo_etiqueta == 2) ? "selected":"" %> value="2">C/Prec</option>
    </select>
  </td>
  <td class="editar"><%= codigo %></td>
  <td class="editar"><%= cantidad %></td>
  <td class="editar"><span class="text-info"><%= nombre %></span></td>
  <td class="editar"><%= Number(costo_neto).toFixed(2) %></td>
  <td class="editar"><%= Number(costo_final).toFixed(2) %></td>
  <td class="editar"><%= Number(precio_final).toFixed(2) %></td>
  <td class="editar"><%= Number(total_final).toFixed(2) %></td>
  <td class="w25 p5">
    <% if (control.check("ingresos_proveedores") > 2) { %>
      <i title="Eliminar" class="glyphicon glyphicon-remove eliminar_flechita text-danger" />
    <% } %>
  </td>
</script>

<script type="text/template" id="ingresos_proveedores_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Ingreso de mercaderia</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="ingresos_proveedores_buscar" value="<%= window.ingresos_proveedores_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </span>
            </div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <% if (permiso > 1) { %>
              <a class="btn btn-info btn-addon" href="app/#ingreso_proveedor"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            <% } %>
          </div>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="<%= (window.ingresos_proveedores_filter != '' || window.ingresos_proveedores_id_proveedor != 0 || window.ingresos_proveedores_id_sucursal != 0) ? "display:block" : "display:none" %>">
        <div class="wrapper oh">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"B&uacute;squeda Avanzada:","en"=>"Advanced Search:")); ?></h4>
          <div class="cb">
            <div class="pull-left">
              <input type="text" style="display: inline-block; width: 120px;" id="ingresos_proveedores_codigo_articulo" value="<%= window.ingresos_proveedores_codigo_articulo %>" placeholder="C&oacute;digo Art." class="form-control no-model">
            </div>
            <div class="input-group fl" style="width: 140px;">
              <input type="text" placeholder="Desde" id="ingresos_proveedores_desde" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group fl" style="width: 140px;">
              <input type="text" placeholder="Hasta" id="ingresos_proveedores_hasta" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="form-group fl" style="width: 200px; display: inline-block">
              <select id="ingresos_proveedores_buscar_proveedores"></select>
            </div>
            <% if (control.check("ingresos_proveedores")==3) { %>
              <div class="form-group fl" style="width: 150px; display: inline-block">
                <select id="ingresos_proveedores_buscar_estados" class="form-control no-model">
                  <option value="-1" <%= (window.ingresos_proveedores_estado == -1)?"selected":"" %>>Estado</option>
                  <option value="1" <%= (window.ingresos_proveedores_estado == 1)?"selected":"" %>>Confirmado</option>
                  <option value="0" <%= (window.ingresos_proveedores_estado == 0)?"selected":"" %>>No Confirmado</option>
                </select>
              </div>
            <% } %>
            <div class="form-group fl" style="width: 200px; display: inline-block">
              <select class="form-control" id="ingresos_proveedores_buscar_sucursales">
                <% if (ID_SUCURSAL != 0) { %>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i]; %>
                    <% if (ID_SUCURSAL == o.id) { %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>  
                  <% } %>
                <% } else { %>
                  <option value="0">Sucursal</option>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i]; %>
                    <option <%= (window.ingresos_proveedores_id_sucursal == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                <% } %>
              </select>   
            </div>
            <div class="form-group dib fl">
              <button class="btn btn-default buscar ml10"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
            </div>
          </div>
        </div>
      </div>
      <div class="bulk_action wrapper pb0">
        <button class="btn btn-default calcular_ventas btn-addon"><i class="icon fa fa-send"></i>Comparar ventas</button>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="ingresos_proveedores_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="w25">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="proveedor">Proveedor</th>
                <th class="w25"></th>
                <th class="sorting" data-sort-by="fecha">Fecha</th>
                <th class="sorting" data-sort-by="numero">Numero</th>
                <th class="sorting" data-sort-by="almacen">Destino</th>
                <th class="sorting" data-sort-by="total">Costo</th>
                <% if (ID_EMPRESA == 868) { %>
                  <th class="sorting" data-sort-by="total">Valor</th>
                  <th>% Central</th>
                <% } %>
                <th>Estado</th>
                <th class="th_acciones w120">Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
        <div>
          Suma Total: <b id="ingresos_proveedores_total"></b>
        </div>
      </div>
    </div>
  </div>  
</script>


<script type="text/template" id="ingresos_proveedores_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" data-fecha="<%= fecha %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= proveedor %></span></td>
  <td>
    <% if (!isEmpty(observaciones)) { %>
      <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-commenting text-warning"></i>
    <% } %>
  </td>
  <td class="ver"><%= fecha %></td>
  <td class="ver"><%= numero_remito %></td>
  <td class="ver"><%= almacen %></td>
  <td class="ver">$ <%= Number(total).format() %></td>
  <% if (ID_EMPRESA == 868) { %>
    <td class="ver">$ <%= Number(valor).format() %></td>
    <td class="ver"><%= (total != 0) ? Number(((valor - total)/total)*100).format() : "0.00" %>%</td>
  <% } %>
  <td class="ver">
    <% if (estado == 0) { %><span class="label bg-danger">No confirmado</span><% } %>
    <% if (estado == 1) { %><span class="label bg-success">Confirmado</span><% } %>
  </td>
  <td class="p5 td_acciones">
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>    
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="imprimir" data-id="<%= id %>">Listado c/precio</a></li>
        <li><a href="javascript:void(0)" class="imprimir_sin_costo" data-id="<%= id %>">Listado s/precio</a></li>
        <li><a href="javascript:void(0)" class="imprimir_remito" data-id="<%= id %>">Imprimir</a></li>
        <% if (MEGASHOP == 1 || ID_EMPRESA == 421) { %>
          <li><a href="javascript:void(0)" class="etiquetas" data-id="<%= id %>">Etiquetas</a></li>
        <% } %>
        <% if (permiso > 1) { %>
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        <% } %>
      </ul>
    </div>
  </td>
</script>



<script type="text/template" id="estadisticas_ventas_por_ingreso_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="fl pt5">Ventas de Ingresos de Mercaderia</span> <button class="cerrar pull-right btn-sm btn btn-default"><i class="fa fa-times"></i></button>
    </div>
    <div class="panel-body">
      <div class="pb15 clearfix">
        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="estadisticas_ventas_por_ingreso_fecha_desde" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>              
        </div>
        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="estadisticas_ventas_por_ingreso_fecha_hasta" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>
        <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i></button>

        <div class="btn-group dropdown pull-right">
          <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
            <i class="fa fa-cog"></i><span><?php echo lang(array("es"=>"Opciones","en"=>"Options")); ?></span>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <?php /* <li><a href="javascript:void(0)" class="imprimir">Imprimir</a></li> */ ?>
            <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
          </ul>
        </div>
      </div>
      <div class="b-a table-responsive" style="height: 400px; overflow: auto">
        <table id="estadisticas_ventas_por_ingreso_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th>Sucursal</th>
              <th>Interno</th>
              <th>EAN</th>
              <th>Prov.</th>
              <th class="sorting" style="min-width:150px" data-sort-by="nombre">Producto</th>
              <th>Costo</th>
              <th>Precio</th>
              <th>Margen</th>
              <th class="sorting" style="min-width:100px" data-sort-by="cantidad_compra">Compra</th>
              <th>$</th>
              <th>Ult. Compra</th>
              <th class="sorting" style="min-width:100px" data-sort-by="cantidad_venta">Venta</th>
              <th>$</th>
              <th>Ult. Venta</th>
              <th>Dif.</th>
              <th>%</th>
              <th>Stock</th>
              <th class="w25"></th>
            </tr>
          </thead>
          <tbody></tbody>
          <?php /*
          <tfoot class="bg-important">
            <tr>
              <td colspan="4"></td>
              <td id="estadisticas_ventas_por_ingreso_cantidad" class="bold">0</td>
              <td id="estadisticas_ventas_por_ingreso_costo_final" class="bold">$ 0.00</td>
              <td id="estadisticas_ventas_por_ingreso_total_final" class="bold">$ 0.00</td>
            </tr>
          </tfoot>
          */ ?>
        </table>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_ventas_por_ingreso_item_template">
  <% var decimal = (MEGASHOP == 1 || ID_EMPRESA == 421)?0:2 %>
  <td><%= sucursal %></td>
  <td><%= codigo %></td>
  <td><%= codigo_barra.replaceAll("###","<br/>") %></td>
  <td><%= codigo_prov %></td>
  <td><span class="text-info"><%= nombre %></span></td>
  <td><%= Number(costo_final).toFixed(2) %></td>
  <td><%= Number(precio_final_dto).toFixed(2) %></td>
  <td><%= (costo_final > 0) ? Number(((precio_final_dto - costo_final)/costo_final)*100).toFixed(2) : Number(0).toFixed(2) %></td>
  <td><%= Number(cantidad_compra).toFixed(decimal) %></td>
  <td><%= Number(total_compra).toFixed(2) %></td>
  <td><%= fecha_compra %></td>
  <td><%= Number(cantidad_venta).toFixed(decimal) %></td>
  <td><%= Number(total_venta).toFixed(2) %></td>
  <td><%= fecha_venta %></td>
  <td><%= Number(cantidad_compra - cantidad_venta).toFixed(decimal) %></td>
  <td><%= Number(porcentaje).toFixed(2) %>%</td>
  <td><%= Number(stock).toFixed(decimal) %></td>
  <td><i class="fa fa-search cp ver_detalle"></i></td>
</script>