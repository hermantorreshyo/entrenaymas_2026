<?php // TODO: HACER ESTO DINAMICO ?>
<% var FACTURACION_OCULTAR_HEADER_FACTURA = (ID_EMPRESA == 574 || ID_EMPRESA == 1326)?1:0 %>

<div class="bg-light lter b-b wrapper-md">
  <div class="row clearfix">
    <div class="col-xs-12 col-sm-6">
      <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i><%= ((ID_EMPRESA == 574 || ID_EMPRESA == 1326) && numero_paso == 1) ? "Pedidos" : "Facturaci&oacute;n" %></h1>
    </div>
    <div class="col-xs-12 col-sm-6">
      <div class="form-inline pull-right">
        <div class="btn-group dropdown">
          <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
            <i class="fa fa-cog"></i><span>Opciones</span>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void" class="anular">Nuevo</a></li>
            <li class="divider"></li>
            <li><a href="javascript:void" class="importar_factura">Importar de factura</a></li>
            <li><a href="javascript:void" class="importar_presupuesto">Importar presupuesto</a></li>
            <li class="divider"></li>
            <li><a href="javascript:void" class="config">Configuraci&oacute;n</a></li>
            <li class="divider"></li>
            <li><a onclick="workspace.cambiar_estado()" href="javascript:void(0)">Modo supervisor</a></li>
          </ul>
        </div>  
      </div>
    </div>
  </div>
</div>
<div class="wrapper-md pb0">
  <div class="centrado">
    <div class="panel panel-default pull-in">
      <div class="panel-heading font-bold">
        Datos de Comprobante       
      </div>
      <div class="panel-body pl10 pr10">
        <div class="clearfix">
          <div class="col-md-3 col-sm-6 pl10 pr10">
            <label>Cliente <i title="Click para ayuda" class="buscar_clientes_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></label>
            <div class="input-group">
              <input type="text" class="dn" id="facturacion_id_cliente" autocomplete="off" value="<%= id_cliente %>"/>
              <% if (ID_EMPRESA == 228) { %>
                <input disabled type="text" class="form-control action no-model" id="facturacion_codigo_cliente" value="<%= cliente.nombre %>"/>
              <% } else { %>
                <input <%= (!edicion)?"disabled":"" %> title="Ingrese el codigo de Cliente o comience a escribir parte del nombre. (0 = Consumidor Final)" type="text" class="form-control action no-model" id="facturacion_codigo_cliente" placeholder="Nombre o codigo de cliente" autocomplete="off" value="<%= cliente.nombre %>"/>
              <% } %>
              <span class="input-group-btn">
                <button <%= (!edicion)?"disabled":"" %> title="Atajo: F2 = Buscar" id="facturacion_buscar_cliente" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button <%= (!edicion)?"disabled":"" %> id="facturacion_agregar_cliente" class="btn btn-default" type="button"><i class="fa fa-plus"></i></button>
              </span>
            </div>            
          </div>
          <div class="col-md-2 col-sm-6 pl10 pr10 <%= (FACTURACION_FORMA_PAGO != "M")?"":"dn" %> ">
            <label>Forma de Pago</label>
            <select <%= (!edicion)?"disabled":"" %> title="Forma de Pago" class="form-control action no-model" name="tipo_pago" id="facturacion_tipo_pago">
              <option <%= (tipo_pago == "C")?"selected":"" %> value="C">Cuenta Corriente</option>
              <option <%= (tipo_pago == "E")?"selected":"" %> value="E">Efectivo</option>
              <option <%= (tipo_pago == "H")?"selected":"" %> value="H">Cheque</option>
              <option <%= (tipo_pago == "T")?"selected":"" %> value="T">Tarjeta</option>
              <option <%= (tipo_pago == "B")?"selected":"" %> value="B">Banco</option>
              <option <%= (tipo_pago == "O")?"selected":"" %> value="O">Otro</option>
            </select>        
          </div>
          <div class="col-md-7">
            <div class="row">
              <div class="col-sm-3 pl10 pr10">
                <label>Comprobante</label>
                <select class="form-control action" <%= ((id == undefined || id == 0) && edicion)?"":"disabled" %> id="facturacion_tipo" name="id_tipo_comprobante"></select>
              </div>
              <div class="col-sm-4 pl10 pr10">
                <% if (FACTURACION_MOSTRAR_NUMERO == 1) { %>
                <label>
                  <span id="<%= (ID_EMPRESA == 1343 || ID_EMPRESA == 1350)?"nombre_punto_venta":"" %>"><%= (ID_EMPRESA == 1343 || ID_EMPRESA == 1350)?"AllExtruded":"N&uacute;mero" %></span>
                  <i id="facturacion_sincronizar_numero" title="Obtiene el numero del proximo comprobante electronico por emitir" class="fs14 ml5 cp text-muted fa fa-refresh"></i>
                </label>
                <div class="input-group">
                  <span class="input-group-btn">
                    <select <%= ((id == undefined || id == 0) && edicion)?"":"disabled" %> title="Punto de Venta" class="form-control w75" id="facturacion_puntos_venta">
                      <?php foreach($puntos_venta as $pv) { ?>
                      <% var por_defecto = <?php echo ($empresa->id == 224 && $local == 0 || $empresa->id == 445) ? (($pv->id_sucursal == $id_sucursal)?1:0) : $pv->por_default ?>; %>
                      <% if (id == undefined || id == 0) { %>
                      <% selected = (por_defecto==1)?"selected":"" %>
                      <% } else { %>
                      <% selected = (id_punto_venta == <?php echo $pv->id ?>)?"selected":"" %>
                      <% } %>
                      <option data-nombre="<?php echo $pv->nombre ?>" data-tipo_impresion="<?php echo $pv->tipo_impresion ?>" <%= selected %> value="<?php echo $pv->id ?>"><?php echo $pv->numero ?></option>
                      <?php } ?>
                    </select>
                  </span>
                  <input <%= ((id == undefined || id == 0) && edicion)?"":"disabled" %> type="text" name="numero" value="<%= numero %>" class="tar form-control action" id="facturacion_numero"/>
                </div>                
                <% } %>             
              </div>
              <div class="col-sm-5 pl10 pr10">
                <div class="row">
                  <div class="col-xs-6 pr0 pl0">
                    <label>Emisi&oacute;n</label>
                    <div class="input-group">
                      <input <%= (!edicion)?"disabled":"" %> type="text" title="Fecha de emision de comprobante" id="facturacion_fecha" name="fecha" autocomplete="off" class="form-control action pr0">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                  <div class="col-xs-6 pr0 pl0">
                    <label>Vencimiento</label>
                    <div class="input-group">
                      <input <%= (!edicion)?"disabled":"" %> type="text" title="Fecha de vencimiento de pago" id="facturacion_fecha_vto" name="fecha_vto" autocomplete="off" class="form-control action pr0">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="clearfix m-b"></div>
          <div class="clearfix">
            <% if (FACTURACION_MOSTRAR_DOLAR == 1) { %>
              <div class="form-group col-md-2 form-group-h col-sm-6 pl10 pr10">
                <label>Cotizaci&oacute;n Dolar</label>
                <input <%= (!edicion)?"disabled":"" %> type="text" class="form-control action" name="cotizacion_dolar" id="facturacion_cotizacion_dolar" value="<%= cotizacion_dolar %>"/>
              </div>
            <% } %>
            <div class="form-group col-md-1 form-group-h col-sm-6 pl10 pr10">
              <label>Remito</label>
              <input type="text" <%= (!edicion)?"disabled":"" %> class="form-control action" name="numero_remito" id="facturacion_numero_remito" value="<%= numero_remito %>"/>
            </div>
            <% if (control.check("repartos")>0) { %>
              <div class="form-group col-md-3 form-group-h col-sm-6 pl10 pr10">
                <label>Reparto</label>
                <div class="input-group">
                  <span class="input-group-btn">
                    <input type="text" class="form-control w40" id="facturacion_reparto" value="<%= reparto %>" name="reparto"/>
                  </span>
                  <input <%= (!edicion)?"disabled":"" %> type="text" id="facturacion_fecha_reparto" name="fecha_reparto" class="form-control action">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>
                </div>
              </div>
            <% } %>
            <% if (control.check("vendedores")>0) { %>
              <div class="form-group col-md-2 form-group-h col-sm-6 pl10 pr10">
                <label>Vendedor</label>
                <select <%= (!edicion)?"disabled":"" %> id="facturacion_vendedores" class="form-control no-model"></select>
              </div>
            <% } %>
            <% if (control.check("conceptos")>0) { %>
              <div class="form-group col-md-2 form-group-h col-sm-6 pl10 pr10">
                <% if (ID_EMPRESA == 399) { %>
                  <label>Unidad Negocio</label>
                  <select class="form-control no-model" id="facturacion_conceptos">
                    <option value='0'>-</option>
                    <option <%= (id_concepto == 299)?"selected":"" %> value='299'>CORRETAJE</option>
                    <option <%= (id_concepto == 457)?"selected":"" %> value='457'>AGROINSUMOS</option>
                    <option <%= (id_concepto == 301)?"selected":"" %> value='301'>BALANCEADO</option>
                    <option <%= (id_concepto == 300)?"selected":"" %> value='300'>TRANSPORTE K</option>
                    <option <%= (id_concepto == 1308)?"selected":"" %> value='1308'>TRANSPORTE F</option>
                  </select>
                <% } else { %>
                  <label>Concepto</label>
                  <select class="form-control no-model" id="facturacion_conceptos">
                    <option value='0'>-</option>
                    <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
                    <%= workspace.crear_select(conceptos_ventas,"",id_concepto) %>
                  </select>
                <% } %>
              </div>
            <% } %>

            <div class="form-group col-sm-4 form-group-h pl10 pr10">
              <div class="row">
                <div class="col-sm-4 pl0 pr5">
                  <label>Direcci&oacute;n</label>
                  <input type="text" name="direccion" value="<%= direccion %>" class="form-control no-model" id="facturacion_direccion"/>
                </div>       
                <div class="col-sm-4 pl0 pr5">
                  <label>Localidad</label>
                  <input type="text" name="localidad" value="<%= localidad %>" class="form-control no-model" id="facturacion_localidad"/>
                </div>
                <div class="col-sm-4 pl0 pr5">
                  <label>C&oacute;digo Postal</label>
                  <input type="text" name="codigo_postal" value="<%= codigo_postal %>" class="form-control no-model" id="facturacion_codigo_postal"/>
                </div>
              </div>
            </div>
            <div class="form-group col-md-2 form-group-h col-sm-6 pl10 pr10">
              <label>Estado</label>
              <select class="form-control action" id="facturacion_tipo_estado" name="id_tipo_estado">
                <% for(var i=0;i< tipos_estado_pedidos.length;i++) { %>
                  <% var c = tipos_estado_pedidos[i]; %>
                  <% if (typeof FACTURACION_PERIODICA != "undefined" &&  FACTURACION_PERIODICA == 1) { %>
                    <?php // Si la empresa tiene FACTURACION_PERIODICA, los estados son 200, 201, 202, etc.. ?>
                    <% if (c.id >= 200 && c.id < 300) { %>
                      <option <%= (id_tipo_estado == c.id)?"selected":"" %> value="<%= c.id %>"><%= c.nombre %></option>
                    <% } %>
                  <% } else { %>
                    <% if (c.id < 100) { %>
                      <option <%= (id_tipo_estado == c.id)?"selected":"" %> value="<%= c.id %>"><%= c.nombre %></option>
                    <% } %>
                  <% } %>
                <% } %>
              </select>
            </div>
          </div>

          <% if (typeof FACTURACION_PERIODICA != "undefined" &&  FACTURACION_PERIODICA == 1) { %>
            <div class="clearfix">
              <div class="col-sm-6 pl10 pr10">
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input <%= (!edicion)?"disabled":"" %> type="checkbox" id="facturacion_es_periodica" class="checkbox no-model" <%= (es_periodica == 1)?"checked":"" %> value="1">
                      <i></i> Es una factura recurrente?
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div id="facturacion_periodica_opciones" style="display:<%= (es_periodica == 1)?"block":"none" %>" class="clearfix">
              <div class="col-sm-2 pl10 pr10">
                <label>Enviar cada</label>
                <div class="input-group">
                  <span class="input-group-btn">
                    <select <%= (!edicion)?"disabled":"" %> class="form-control w75 no-model" id="facturacion_periodo_cantidad">
                      <option <%= (periodo_cantidad==1)?"selected":"" %> value="1">1</option>
                      <option <%= (periodo_cantidad==2)?"selected":"" %> value="2">2</option>
                      <option <%= (periodo_cantidad==3)?"selected":"" %> value="3">3</option>
                      <option <%= (periodo_cantidad==4)?"selected":"" %> value="4">4</option>
                      <option <%= (periodo_cantidad==5)?"selected":"" %> value="5">5</option>
                      <option <%= (periodo_cantidad==6)?"selected":"" %> value="6">6</option>
                      <option <%= (periodo_cantidad==7)?"selected":"" %> value="7">7</option>
                      <option <%= (periodo_cantidad==8)?"selected":"" %> value="8">8</option>
                      <option <%= (periodo_cantidad==9)?"selected":"" %> value="9">9</option>
                      <option <%= (periodo_cantidad==10)?"selected":"" %> value="10">10</option>
                      <option <%= (periodo_cantidad==11)?"selected":"" %> value="11">11</option>
                      <option <%= (periodo_cantidad==12)?"selected":"" %> value="12">12</option>
                      <option <%= (periodo_cantidad==13)?"selected":"" %> value="13">13</option>
                      <option <%= (periodo_cantidad==14)?"selected":"" %> value="14">14</option>
                      <option <%= (periodo_cantidad==15)?"selected":"" %> value="15">15</option>
                    </select>
                  </span>
                  <select <%= (!edicion)?"disabled":"" %> class="form-control no-model" id="facturacion_periodo_tipo">
                    <option <%= (periodo_tipo=="M")?"selected":"" %> value="M">Meses</option>
                    <option <%= (periodo_tipo=="Y")?"selected":"" %> value="Y">Años</option>
                  </select>
                </div>
              </div>

              <div class="col-sm-2 pl10 pr10">
                <label>D&iacute;a del mes</label>
                <select <%= (!edicion)?"disabled":"" %> id="facturacion_periodo_dia" name="periodo_dia" class="form-control">
                  <option <%= (periodo_dia == 0)?"selected":"" %> value="0">Igual fecha emision</option>
                  <% for (j=1;j<=31;j++) { %>
                    <option <%= (periodo_dia == j)?"selected":"" %> value="<%= j %>"><%= j %></option>
                  <% } %>
                </select>
              </div>

              <div class="col-sm-2 pl10 pr10">
                <label>V&aacute;lida por</label>
                <select <%= (!edicion)?"disabled":"" %> id="facturacion_dias_vencimiento" name="dias_vencimiento" class="form-control">
                  <option <%= (dias_vencimiento == 5)?"selected":"" %> value="5">5 d&iacute;as</option>
                  <option <%= (dias_vencimiento == 10)?"selected":"" %> value="10">10 d&iacute;as</option>
                  <option <%= (dias_vencimiento == 15)?"selected":"" %> value="15">15 d&iacute;as</option>
                  <option <%= (dias_vencimiento == 20)?"selected":"" %> value="20">20 d&iacute;as</option>
                  <option <%= (dias_vencimiento == 30)?"selected":"" %> value="30">30 d&iacute;as</option>
                </select>
              </div>
            </div>
          <% } %>

        </div>
      </div>
    </div>

    <div class="panel panel-info pull-in">
      <div style="<%= (FACTURACION_OCULTAR_HEADER_FACTURA == 1)?"display:none":"" %>" class="panel-heading font-bold">Previsualizaci&oacute;n</div>
      <div class="panel-body <%= (FACTURACION_OCULTAR_HEADER_FACTURA == 1)?"p0":"preview-container" %>">
        <div class="preview">

          <div style="<%= (FACTURACION_OCULTAR_HEADER_FACTURA == 1)?"display:none":"" %>">
            <div class="invoice-block">
              <div class="invoice-type">Factura</div>
              <div class="letter">B</div>
            </div>
            <div class="invoice-block">
              <div class="col-md-6 pull-in">
                <div>
                  <span class="bold">Fecha de Emisi&oacute;n: </span>
                  <span id="facturacion_fecha_factura"></span>
                </div>
                <div>
                  <span class="bold">Condici&oacute;n de Venta: </span>
                  <span id="facturacion_forma_pago_factura"></span>
                </div>
                <div>
                  <span class="bold">Nro. Remito: </span>
                  <span id="facturacion_numero_remito_factura"></span>
                </div>
              </div>
              <div class="col-md-6 pull-in">
                <div class="<%= (ID_EMPRESA == 228)?"dn":"" %>">
                  <div><%= RAZON_SOCIAL %></div>
                  <div><%= TIPO_CONTRIBUYENTE %></div>
                  <div>CUIT: <%= CUIT %></div>
                </div>
              </div>
            </div>
            <div class="line line-dashed b-b line-lg"></div>

            <div class="invoice-block">
              <div class="col-xs-6 pull-in">
                <div>
                  <span class="bold">Cliente: </span>
                  <span id="facturacion_cliente_factura"></span>
                </div>
                <div>
                  <span class="bold">Direcci&oacute;n: </span>
                  <span id="facturacion_cliente_direccion"></span>
                </div>
                <div>
                  <span class="bold">Localidad: </span>
                  <span id="facturacion_cliente_localidad"></span>
                </div>
              </div>
              <div class="col-xs-6 pull-in">
                <div>
                  <span class="bold">Tipo Contribuyente: </span>
                  <span id="facturacion_cliente_iva"></span>
                </div>
                <div>
                  <span class="bold">CUIT / DNI: </span>
                  <span id="facturacion_cliente_cuit"></span>
                </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg"></div>
          </div>

          <input type="hidden" id="facturacion_id_articulo"/>
          <input type="hidden" class="dn no-model" value="0.00" id="facturacion_item_percep_viajes"/>
          <input type="hidden" class="dn no-model" value="" id="facturacion_item_custom_2"/>
          <input type="hidden" class="dn no-model" value="" id="facturacion_item_custom_4"/>
          <input type="hidden" class="dn no-model" value="" id="facturacion_item_id_proveedor"/>
          <input type="hidden" class="dn no-model" value="0" id="facturacion_item_stock"/>
          <input type="hidden" class="dn no-model" value="" id="facturacion_moneda"/>

          <% if (FACTURACION_METODO_INGRESO == "J") { %>
            <div class="col-xs-12 col-lg-6">
              <div class="input-group">
                <input type="text" class="form-control action" id="facturacion_codigo_articulo" placeholder="codigo (/cambio) (+bonif) (-desc) (*unid)"/>
                <span class="input-group-btn">
                  <button title="Atajo: F9 = BUSCAR" id="facturacion_buscar_articulo" class="btn btn-info">Buscar</button>
                </span>
              </div>
            </div>
          <% } else { %>
            <div class="clearfix">
              <div class="col-md-5 col-sm-12 p0">
                <div class="col-md-8 col-sm-6 p0">
                  <label class="text-muted">Producto / Servicio</label>
                  <div class="input-group">
                    <input type="text" class="form-control action no-model" autocomplete="off" id="facturacion_codigo_articulo"/>
                    <span class="input-group-btn">
                      <button title="Atajo: F9 = Buscar" id="facturacion_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                    </span>
                    <span class="input-group-btn">
                      <button title="M&aacute;s Opciones" tabindex="-1" class="btn btn-default advanced-search-btn ml0"><i class="fa fa-angle-double-down"></i></button>
                    </span>
                  </div>
                </div>
                <div class="col-md-4 col-sm-6 pl0">
                  <label class="text-muted">Cantidad</label>
                  <input type="text" class="form-control action no-model" autocomplete="off" value="1" id="facturacion_item_cantidad"/>
                </div>
              </div>
              <div class="col-md-7 col-sm-12 p0">
                <div class="row">
                  <div class="col-md-4 col-sm-6 p0">
                    <% if (ID_EMPRESA == 869) { %>
                      <select class="form-control no-model dn" id="facturacion_variantes" autocomplete="off" disabled></select>
                      <label class="text-muted">Costo</label>
                      <input type="text" class="no-model form-control" value="0" id="facturacion_costo_final"/>
                    <% } else { %>                    
                      <input type="hidden" class="dn no-model" value="0" id="facturacion_costo_final"/>
                      <label class="text-muted">Variantes</label>
                      <select class="form-control no-model" id="facturacion_variantes" autocomplete="off" disabled></select>
                    <% } %>
                  </div>
                  <div class="col-md-8">
                    <div class="row">
                      <div class="col-md-4 col-sm-6 p0">
                        <label class="text-muted">Precio Unit.</label>
                        <input <%= ((typeof FACTURACION_MODIFICAR_PRECIO != "undefined" && FACTURACION_MODIFICAR_PRECIO == 0)?"disabled":"") %> type="text" class="form-control no-model action <%= (typeof REMITOS_TOMAR_PRECIO_NETO != 'undefined' && REMITOS_TOMAR_PRECIO_NETO == 1)?"":"dn" %>" value="0.00" id="facturacion_item_neto"/>
                        <input <%= ((typeof FACTURACION_MODIFICAR_PRECIO != "undefined" && FACTURACION_MODIFICAR_PRECIO == 0)?"disabled":"") %> type="text" class="form-control no-model action <%= (typeof REMITOS_TOMAR_PRECIO_NETO != 'undefined' && REMITOS_TOMAR_PRECIO_NETO == 1)?"dn":"" %>" value="0.00" id="facturacion_item_precio"/>
                      </div>
                      <div class="col-md-4 col-sm-6 p0">
                        <label class="text-muted">% IVA</label>
                        <select <%= ((PERFIL != 235 && (ID_EMPRESA == 135 || (typeof FACTURACION_MODIFICAR_IVA != "undefined" && FACTURACION_MODIFICAR_IVA == 0)))?"disabled":"") %> id="facturacion_alicuotas_iva" class="form-control action no-model">
                          <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                          <% var o = alicuotas_iva[i]; %>
                          <option value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                          <% } %>
                        </select>
                      </div>
                      <div class="col-md-4 col-sm-6 p0">
                        <label class="text-muted">% Bonif.</label>
                        <div class="input-group">
                          <input type="number" min="0" max="100" class="form-control action no-model" autocomplete="off" placeholder="0 %" id="facturacion_item_bonificado"/>
                          <span class="input-group-btn pr15">
                            <button title="Ingresar linea" id="facturacion_agregar_item" class="btn btn-info ml0 form-control"><i class="fa fa-plus"></i></button>
                          </span>
                        </div>                            
                        <input type="text" disabled class="form-control no-model dn" id="facturacion_item_subtotal" placeholder="Subtotal"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="advanced-search-div bg-light dk" style="<%= (ID_EMPRESA == 342) ? 'display:block' : 'display:none' %>">
              <div class="wrapper oh">
                <div class="col-md-2 col-sm-6 p0">
                  <label class="text-muted">Tipo</label>
                  <select class="form-control no-model" id="facturacion_tipo_item">
                    <option value="0">Producto</option>
                    <option value="1">Servicio</option>
                    <option value="2">Producto y Servicio</option>
                  </select>                                    
                </div>
                <div class="col-md-2 col-sm-6 p0">
                  <label class="text-muted">Tomar precio de</label>
                  <select class="form-control no-model" id="facturacion_lista">
                    <option value="0"><%= (typeof LISTA_1_NOMBRE != undefined) ? LISTA_1_NOMBRE : "Lista 1" %></option>
                    <option value="1"><%= (typeof LISTA_2_NOMBRE != undefined) ? LISTA_2_NOMBRE : "Lista 2" %></option>
                    <option value="2"><%= (typeof LISTA_3_NOMBRE != undefined) ? LISTA_3_NOMBRE : "Lista 3" %></option>
                    <option value="3"><%= (typeof LISTA_4_NOMBRE != undefined) ? LISTA_4_NOMBRE : "Lista 4" %></option>
                    <option value="4"><%= (typeof LISTA_5_NOMBRE != undefined) ? LISTA_5_NOMBRE : "Lista 5" %></option>
                    <option value="5"><%= (typeof LISTA_6_NOMBRE != undefined) ? LISTA_6_NOMBRE : "Lista 6" %></option>
                  </select>
                </div>
                <div class="col-md-2 col-sm-6 p0">
                  <label class="text-muted">Stock</label>
                  <select class="form-control no-model" id="facturacion_item_custom_3">
                    <option value="">Descontar</option>
                    <option value="1">Reservar</option>
                  </select>                                    
                </div>
                <div class="col-md-6 col-sm-6 p0">
                  <label class="text-muted">Descripcion detallada</label>
                  <input type="text" class="form-control no-model" id="facturacion_item_descripcion" placeholder="Escriba aqui un detalle del producto o servicio"/>
                </div>                    
              </div>
            </div>
          <% } %>

          <div class="b-a" style="overflow: auto; margin-top: 15px;">
            <table id="tabla_items" class="table sortable m-b-none default footable">
              <thead class="bg-light">
                <tr>
                  <th class="w75">Cant.</th>
                  <th>Detalle</th>
                  <th class="w75">Unit.</th>
                  <th class="w75">Bonif.</th>
                  <th class="w75">Subtotal</th>
                  <% if (FACTURACION_MODIFICAR_ITEM == 1) { %><th class="w25"></th><% } %>
                  <th class="w25"></th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <div class="line line-dashed b-b line-lg"></div>

          <div class="oh m-t">
            <div class="col-md-6">
              <div class="form-horizontal pull-in totales">

                <div class="b-a iva_container" style="overflow: auto; margin-right: 30px;">
                  <table id="tabla_impuestos" class="table sortable m-b-none default footable">
                    <thead class="bg-light lter">
                      <tr>
                        <th>Tributo</th>
                        <th class="w100">Base Imp.</th>
                        <th class="w100">Monto</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>                        
                <div id="detalle_ivas" class="iva_container"></div>                            
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-horizontal pull-in totales">
                <div id="facturacion_subtotal_sin_dto_div" class="form-group">
                  <label class="control-label col-xs-8">Subtotal:</label>
                  <div class="col-xs-4">
                    <input type="text" disabled class="no-input" id="facturacion_subtotal_sin_dto"/>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-xs-8">
                    Descuento (%):
                    <input type="number" min="0" max="100" value="<%= porc_descuento %>" <%= (!edicion || FACTURACION_EDITAR_DESCUENTO==0)?"disabled":"" %> class="form-control w-xs pull-right action text-right" id="facturacion_porc_descuento"/>
                  </label>
                  <div class="col-xs-4">
                    <input type="text" disabled class="no-input" id="facturacion_descuento"/>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-xs-8">Subtotal:</label>
                  <div class="col-xs-4">
                    <input type="text" disabled class="no-input" id="facturacion_subtotal"/>
                  </div>
                </div>
                
                <div class="form-group iva_container">
                  <label class="control-label col-xs-6">IVA: </label>
                  <div class="col-xs-6">
                    <input type="text" disabled class="no-input" id="facturacion_iva"/>
                  </div>
                </div>

                <?php /*
                <div class="form-group iva_container">
                  <label class="control-label col-xs-6">Otros Tributos: </label>
                  <div class="col-xs-6">
                    <input type="text" disabled class="no-input" id="facturacion_otros_impuestos"/>
                  </div>
                </div>
                */ ?>

                <div class="form-group <%= (PERCIBE_IB == 0) ? 'dn' : '' %>" id="facturacion_percepcion_ib_row">
                  <label class="control-label col-xs-8">
                    Percep. Ingr. Brutos
                    <input type="number" disabled class="form-control w-xs pull-right action text-right no-model" id="facturacion_porc_percepcion_ib"/>
                  </label>
                  <div class="col-xs-4">
                    <input type="text" disabled class="no-input" id="facturacion_percepcion_ib"/>
                  </div>
                </div>

                <% if ((id != undefined || id != 0) && interes > 0) { %>
                  <div class="form-group">
                    <label class="control-label col-xs-8">
                      Recargo Tarjeta:
                    </label>
                    <div class="col-xs-4">
                      <input type="text" disabled class="no-input" value="<%= interes %>"/>
                    </div>
                  </div>
                <% } %>

                <div class="line line-dashed b-b"></div>
                <div class="form-group">
                  <label class="control-label col-xs-6 fs26">Total:</label>
                  <div class="col-xs-6">
                    <input type="text" disabled class="dn" id="facturacion_total"/>
                    <input type="text" disabled class="no-input fs26 bold" id="facturacion_total_visible"/>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="line line-dashed b-b line-lg"></div>

          <div class="oh m-t">
            <ul class="nav nav-tabs nav-tabs-4" role="tablist">
              <li class="active">
                <a href="#tab_observaciones_1" role="tab" data-toggle="tab">Observaciones <i title="Click para ayuda" class="observaciones_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></a>
              </li>
              <li class="">
                <a href="#tab_observaciones_2" role="tab" data-toggle="tab">Comentarios Internos</a>
              </li>
            </ul>
            <div class="tab-content">
              <div id="tab_observaciones_1" class="tab-pane active">
                <textarea style="height: 100px" id="facturacion_observaciones" name="observaciones" placeholder="Puede escribir una nota u observacion que aparecer&aacute; al pie de p&aacute;gina del comprobante..." class="form-control"><%= ((id != undefined)?observaciones:OBSERVACIONES).replaceAll("<br />","\n").replaceAll("<br/>","\n").replaceAll("<br>","\n") %></textarea>
              </div>                  
              <div id="tab_observaciones_2" class="tab-pane">
                <textarea style="height: 100px" id="facturacion_custom_5" name="custom_5" placeholder="Puede escribir aqui un comentario privado que no se mostrar&aacute; al cliente..." class="form-control"><%= (typeof custom_5 != "undefined") ? (custom_5.replaceAll("<br />","\n").replaceAll("<br/>","\n").replaceAll("<br>","\n")) : "" %></textarea>
              </div>
            </div>
          </div>

          <div class="line line-dashed b-b line-lg"></div>

        </div>
      </div>
    </div>

    <% if (ID_EMPRESA == 228) { %>
      <% if (id_tipo_comprobante == 999) { %>
        <div class="oh m-t m-b tar pull-in">
          <button class="btn btn-success btn-lg aceptar btn-addon"><i class="icon fa fa-plus"></i>Guardar</button>
        </div>
      <% } %>
    <% } else { %>

      <div class="oh m-t m-b tar pull-in">
        <% if (id != undefined && id != 0 && pendiente != 1) { %>
          <button class="btn btn-primary imprimir btn-addon pull-left m-r"><i class="icon glyphicon glyphicon-print"></i>Imprimir</button>
          <button class="btn btn-info enviar btn-addon pull-left"><i class="icon fa fa-send"></i>Enviar</button>
        <% } %>
        <button class="btn btn-success btn-lg aceptar btn-addon"><i class="icon fa fa-plus"></i>Guardar</button>
      </div>
    <% } %>

  </div>
</div>
