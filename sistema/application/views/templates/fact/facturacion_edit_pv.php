<div class="wrapper-md pb0">
  <div class="centrado">
    <div class="row">
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-heading font-bold">
            Ingreso de datos
          </div>
          <div class="panel-body">
            <div class="clearfix m-b-md">
              <div class="row">
                <div class="col-md-5">
                  <div class="input-group">
                    <input type="text" class="dn" id="facturacion_id_cliente" value="<%= id_cliente %>"/>
                    <input <%= (!edicion)?"disabled":"" %> title="Ingrese el c&oacute;digo de Cliente o comience a escribir parte del nombre. (0 = Consumidor Final)" type="text" class="form-control action no-model" id="facturacion_codigo_cliente" placeholder="Nombre o codigo de cliente" autocomplete="off" value="<%= cliente.nombre %>"/>
                    <span class="input-group-btn">
                      <button title="Atajo: F2 = Buscar" tabindex="-1" id="facturacion_buscar_cliente" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                      <% if (FACTURACION_CREAR_CLIENTE == 1) { %>
                        <button title="Crear nuevo cliente" tabindex="-1" id="facturacion_nuevo_cliente" class="btn btn-default" type="button"><i class="fa fa-plus"></i></button>
                      <% } %>
                    </span>
                  </div>
                </div>
                <% if (control.check("vendedores")>0) { %>
                  <div class="col-md-4">
                    <select id="facturacion_vendedores" class="form-control no-model"></select>
                  </div>
                <% } %>
                <% if (ID_EMPRESA == 224 || ID_EMPRESA == 1325) { %>
                  <div class="col-md-3">
                    <select class="no-model form-control" id="facturacion_lista">
                      <option value="0">Lista 1</option>
                      <option value="1">Lista 2</option>
                      <option value="2">Lista 3</option>
                      <option value="3">Lista 4</option>
                      <option value="4">Lista 5</option>
                      <option value="5">Lista 6</option>
                    </select> 
                  </div>
                <% } %>
              </div>
            </div>
            <div class="clearfix m-b-md">
              <div class="<%= (typeof FACTURACION_OCULTAR_BUSCADOR != 'undefined' && FACTURACION_OCULTAR_BUSCADOR == 1)?'w100p':'input-group' %>">
                <input type="text" style="line-height: 64px; height: 64px;" placeholder="C&oacute;digo del producto" autocomplete="off" class="form-control action fs24 no-model" id="facturacion_codigo_articulo"/>
                <span class="input-group-btn" style="<%= (typeof FACTURACION_OCULTAR_BUSCADOR != 'undefined' && FACTURACION_OCULTAR_BUSCADOR == 1)?'display:none':'' %>">
                  <button style="height: 64px;" tabindex="-1" title="Atajo: F9 = Buscar" id="facturacion_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                </span>
              </div>

              <?php // CAMPOS OCULTOS ?>
              <input type="hidden" class="dn no-model" value="0" id="facturacion_rubro"/>
              <input type="hidden" class="dn no-model" value="0" id="facturacion_departamento"/>
              <input type="hidden" class="dn no-model" value="0" id="facturacion_costo_final"/>
              <input type="hidden" class="dn no-model" value="1" id="facturacion_item_nombre"/>
              <input type="hidden" class="dn no-model" value="1" id="facturacion_item_cantidad"/>
              <input type="hidden" class="dn no-model" value="0.00" id="facturacion_item_neto"/>
              <input type="hidden" class="dn no-model" value="0" id="facturacion_item_stock"/>
              <input type="hidden" class="dn no-model" value="0.00" id="facturacion_item_precio"/>
              <input type="hidden" class="dn no-model" value="0.00" id="facturacion_item_percep_viajes"/>
              <input type="hidden" class="dn no-model" value="" id="facturacion_moneda"/>
              <input type="hidden" class="dn no-model" value="" id="facturacion_item_custom_2"/>
              <input type="hidden" class="dn no-model" value="" id="facturacion_item_custom_4"/>
              <input type="hidden" class="dn no-model" value="" id="facturacion_item_id_proveedor"/>
              <input type="hidden" class="dn no-model" value="<%= ((id != undefined)?observaciones:OBSERVACIONES).replaceAll("<br />","\n").replaceAll("<br/>","\n").replaceAll("<br>","\n") %>" id="facturacion_observaciones"/>

              <select class="no-model dn" name="tipo_pago" id="facturacion_tipo_pago">
                <option value="C">Cuenta Corriente</option>
                <option value="E">Efectivo</option>
              </select>
              <select id="facturacion_alicuotas_iva" class="dn no-model">
                <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                <% var o = alicuotas_iva[i]; %>
                <option value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                <% } %>
              </select>
              <input type="hidden" min="0" max="100" class="dn no-model" id="facturacion_item_bonificado"/>
              <input type="hidden" disabled class="dn no-model" id="facturacion_item_subtotal"/>
              <input type="text" name="numero" value="<%= numero %>" class="dn" id="facturacion_numero"/>
              <input type="hidden" id="facturacion_id_articulo"/>
              <input type="hidden" class="dn no-model" value="0.00" id="facturacion_item_percep_viajes"/>
            </div>
            <div class="b-a" style="overflow: auto; margin-top: 15px; height: 320px">
              <table id="tabla_items" class="table sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th class="w75">Cant.</th>
                    <th>Detalle</th>
                    <th class="w75">Unit.</th>
                    <th class="w75">Subtotal</th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <% if (MEGASHOP == 1) { %>
              <div class="oh mt10">
                <span class="fl mr10 fs12 db">Referencia:</span>
                <table class="fl mr10 fs12">
                  <tr class="fila_roja_2" style="border-color:transparent!important">
                    <td class="pl10 pr10">Bonificado</td>
                  </tr>
                </table>
                <table class="fl mr10 fs12">
                  <tr class="fila_roja" style="border-color:transparent!important">
                    <td class="pl10 pr10">En Oferta</td>
                  </tr>
                </table>
              </div>
            <% } %>

          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-info">
          <div class="panel-heading font-bold">
            Totales
          </div>
          <div class="panel-body">

            <div style="display:none" class="form-group">
              <label>Fecha de Emisi&oacute;n</label>
              <div class="input-group">
                <input disabled="disabled" type="text" title="Fecha de emision de comprobante" id="facturacion_fecha" name="fecha" class="form-control action">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <div style="<%= ((ID_EMPRESA == 224 && LOCAL == 0) || (ID_EMPRESA == 1021))?"":"display:none" %>" class="form-group">
              <label>Comprobante</label>
              <select class="form-control action" <%= (id == undefined || id == 0)?"":"disabled" %> id="facturacion_tipo" name="id_tipo_comprobante"></select>            
            </div>

            <div class="row">
              <div class="col-xs-6">
                <label>Punto de Venta
                  <p class="<%= (ID_EMPRESA == 287 && LOCAL == 0) ? "" : "dn" %>" id="nombre_punto_venta"></p>
                </label>
              </div>
              <div class="col-xs-6">
                <select class="<%= (ID_EMPRESA == 287 && LOCAL == 0) ? "form-control no-model" : "no-select" %> fs16 bold fr" <%= (ID_EMPRESA == 287 && LOCAL == 0) ? "" : "disabled" %> id="facturacion_puntos_venta">
                  <?php foreach($puntos_venta as $pv) { ?>
                    <% var por_defecto = <?php echo ($empresa->id == 224 && $local == 0 || $empresa->id == 445) ? (($pv->id_sucursal == $id_sucursal)?1:0) : $pv->por_default ?>; %>
                    <% if (id == undefined || id == 0) { %>
                      <% selected = (por_defecto==1)?"selected":"" %>
                    <% } else { %>
                      <% selected = (id_punto_venta == <?php echo $pv->id ?>)?"selected":"" %>
                    <% } %>
                    <option data-tipo_impresion="<?php echo $pv->tipo_impresion ?>" <%= selected %> data-nombre="<?php echo $pv->nombre ?>" value="<?php echo $pv->id ?>"><?php echo $pv->numero ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <% if (ID_EMPRESA == 224 || ID_EMPRESA == 1325) { %>
              <div class="row clearfix mt5">
                <div class="col-xs-6">
                  <label>Cotizacion Dolar</label>
                </div>
                <div class="col-xs-6">
                  <input type="text" class="no-input fs16 bold fr" disabled value="<%= (typeof COTIZACION_DOLAR != "undefined")?COTIZACION_DOLAR : "" %>" />
                </div>
              </div>
            <% } %>                

            <div class="line line-dashed b-b"></div>
            <div class="row">
              <div class="form-group">
                <label class="control-label col-xs-4">Subtotal:</label>
                <div class="col-xs-8">
                  <input type="text" disabled class="no-input" id="facturacion_subtotal"/>
                </div>
              </div>
            </div>

            <div id="facturacion_ofertas_cont" class="row" style="display: <%= (ofertas.length > 0)?'block':'none' %>"></div>
            <div id="facturacion_bonificaciones_cont" style="max-height: 180px; overflow-x: hidden; overflow-y: auto; display: <%= (bonificaciones.length > 0)?'block':'none' %>">
              <div class="row">
                <div class="col-xs-12">Bonificaciones:</div>
                <div class="col-xs-12">
                  <div class="row" id="facturacion_bonificaciones_container">
                  </div>
                </div>
              </div>  
            </div>

            <div id="facturacion_descuento_cont" class="row">
              <div class="form-group">
                <div class="col-xs-6">
                  <div class="col-xs-6 p0">
                    <label class="control-label mt5 mr5">Dto. (%):</label>
                  </div>
                  <div class="col-xs-6 p0">
                    <input type="text" value="<%= porc_descuento %>" class="form-control w-xs action text-right" id="facturacion_porc_descuento" <%= (ID_USUARIO == 752)?"": ((FACTURACION_EDITAR_DESCUENTO==0)?"disabled":"") %>/>
                  </div>
                </div>
                <div class="col-xs-6">
                  <input type="text" disabled class="no-input" id="facturacion_descuento"/>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="form-group iva_container">
                <label class="control-label col-xs-6">IVA: </label>
                <div class="col-xs-6">
                  <input type="text" disabled class="no-input" id="facturacion_iva"/>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="form-group iva_container <%= (PERCIBE_IB == 0) ? 'dn' : '' %>" id="facturacion_percepcion_ib_row">
                <label class="control-label col-xs-6">
                  Per. Ing Brut: <input type="text" disabled class="no-input dn no-model" id="facturacion_porc_percepcion_ib"/>
                </label>
                <div class="col-xs-6">
                  <input type="text" disabled class="no-input bold" id="facturacion_percepcion_ib"/>
                </div>
              </div>
            </div>

            <% if (typeof id != "undefined" && interes > 0) { %>
              <div class="row">
                <div class="form-group">
                  <label class="control-label col-xs-6">
                    Recargo Tarjeta:
                  </label>
                  <div class="col-xs-6">
                    <input type="text" disabled class="no-input bold" value="<%= interes %>"/>
                  </div>
                </div>
              </div>
            <% } %>

            <div class="line line-dashed b-b"></div>
            <div class="row">
              <div class="form-group">
                <label class="control-label col-xs-4 fs26">Total:</label>
                <div class="col-xs-8">
                  <input type="text" disabled class="no-input fs30 bold" id="facturacion_total"/>
                </div>
              </div>
            </div>
          </div>
        </div>
        <% if (typeof id == "undefined" || ID_EMPRESA == 224 || ID_EMPRESA == 1325) { %>
          <div class="m-t">
            <div class="row pl10 pr10">
              <div class="col-md-4 pl5 pr5">
                <div class="form-group">
                  <button class="btn btn-block btn-default" id="facturacion_consultar_articulo">Consultar</button>
                </div>
              </div>
              <div class="col-md-4 pl5 pr5">
                <div class="form-group">
                  <button class="btn btn-block btn-default anular">Anular</button>
                </div>
              </div>
              <div class="col-md-4 pl5 pr5">
                <div class="btn-group dropdown w100p">
                  <button class="btn btn-block btn-default dropdown-toggle" data-toggle="dropdown">
                    Opciones</span>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <% if (LOCAL == 0) { %>
                      <li><a href="javascript:void" class="importar_presupuesto">Importar presupuesto</a></li>
                    <% } %>
                  </ul>
                </div>  
              </div>                  
            </div>
            <?php /*
            <% if (MEGASHOP == 1) { %>
              <div class="form-group">
                <button class="btn btn-block btn-default descuento_efectivo_mega btn-lg bold">Dto. 20% Efectivo</button>
              </div>
            <% } %>
            <% if (MEGASHOP == 1) { %>
              <div class="form-group">
                <button class="btn btn-block btn-default oferta_cubiertos_mega btn-lg bold">Cubiertos 48pzs Vidrio</button>
              </div>
              <div class="form-group">
                <button class="btn btn-block btn-default oferta_platos_mega btn-lg bold">Combo Platos Ancers</button>
              </div>
              <div class="form-group">
                <button class="btn btn-block btn-default oferta_cubiertero_mega btn-lg bold">Combo Cubiertero</button>
              </div>
            <% } %>
            */ ?>

            <div class="form-group">
              <button class="btn btn-block btn-success aceptar btn-lg bold">FINALIZAR</button>
            </div>
          </div>
        <% } %>
      </div>
    </div>
  </div>
</div>
