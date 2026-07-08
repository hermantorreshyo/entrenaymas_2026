<script type="text/template" id="pedido_mesa_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span id="pedido_mesa_titulo" class="font-bold fl fs20 m-t-xs"><%= titulo %></span>
    <button class="fr cp cerrar btn btn-default">
      <i class="fa fa-times text-muted"></i>
    </button>
  </div>
  <div class="panel-body">
    <% if (tipo == "M") { %>
      <div class="clearfix">
        <div class="col-md-4 p3">
          <div class="form-group">
            <label class="control-label text-muted">Cant. de personas</label>
            <div class="input-group">
              <span class="input-group-btn addon_minus"><i class="fa fa-minus"></i></span>
              <input min="0" type="text" id="pedido_mesa_personas" class="form-control tar" value="<%= numero_referencia %>" name="numero_referencia"/>
              <span class="input-group-btn addon_plus"><i class="fa fa-plus"></i></span>
            </div>
          </div>
        </div>
        <div class="col-md-4 p3">
          <div class="form-group">
            <label class="control-label text-muted">Cliente</label>
            <input type="text" value="<%= cliente.nombre %>" id="pedido_mesa_cliente" class="form-control no-model">
          </div>
        </div>
        <div class="col-md-4 p3">
          <div class="form-group text-muted">
            <label class="control-label">Atendido por</label>
            <select name="id_usuario" id="pedido_mesa_usuarios" class="form-control">
              <% for (var i=0; i< usuarios.length; i++) { %>
                <% var u = usuarios.models[i] %>
                <option <%= (u.id == id_usuario)?"selected":"" %> value="<%= u.id %>"><%= u.get("nombre") %></option>
              <% } %>
            </select>
          </div>
        </div>
      </div>
    <% } else if (tipo == "D" || tipo == "T") { %>
      <div class="col-md-8 p3">
        <div class="form-group">
          <input type="text" placeholder="Cliente" value="<%= nombre %>" id="pedido_mesa_cliente" class="form-control" <%= (id_tipo_estado == 6 && ID_EMPRESA != 171)?"disabled":"" %>>
        </div>
      </div>
      <div class="col-md-4 p3">
        <div class="form-group">
          <select id="pedido_mesa_tipo_entrega" class="form-control no-model" <%= (id_tipo_estado == 6 && ID_EMPRESA != 171)?"disabled":"" %>>
            <option <%= (tipo=="T")?"selected":"" %> value="T">En mostrador</option>
            <option <%= (tipo=="D")?"selected":"" %> value="D">Delivery</option>
          </select>
        </div>
      </div>
      <div class="col-md-6 p3 <%= (tipo=='T')?'dn':'' %>">
        <div class="form-group text-muted">
          <input type="text" placeholder="Direcci&oacute;n" name="direccion" value="<%= direccion %>" name="direccion" id="pedido_mesa_direccion" class="form-control" <%= (id_tipo_estado == 6 && ID_EMPRESA != 171)?"disabled":"" %>>
        </div>
      </div>
      <div class="col-md-6 p3 <%= (tipo=='T')?'dn':'' %>">
        <div class="form-group text-muted">
          <input type="text" placeholder="Tel&eacute;fono" name="telefono" value="<%= telefono %>" name="telefono" id="pedido_mesa_telefono" class="form-control" <%= (id_tipo_estado == 6 && ID_EMPRESA != 171)?"disabled":"" %>>
        </div>
      </div>
      <% if (ID_EMPRESA == 171) { %>
        <div class="form-group">
          <div class="input-group no-br">
            <input type="text" placeholder="Observaciones" value="<%= observaciones %>" class="form-control action no-model" id="pedido_mesa_observaciones" <%= (id_tipo_estado == 6 && ID_EMPRESA != 171)?"disabled":"" %>/>
            <span class="input-group-btn">
              <button id="pedido_mesa_buscar_articulo" tabindex="-1" class="btn btn-default" <%= (id_tipo_estado == 6 && ID_EMPRESA != 171)?"disabled":"" %>>
                <i class="fa fa-search"></i>&nbsp;&nbsp;Buscar&nbsp;
              </button>
            </span>
          </div>
        </div>
      <% } %>
    <% } else if (tipo == "B") { %>
      <div class="row">
        <div class="col-xs-12">
          <div class="form-group">
            <input placeholder="Cliente" type="text" value="<%= cliente.nombre %>" id="pedido_mesa_cliente" class="form-control no-model" <%= (id_tipo_estado == 6 && ID_EMPRESA != 171)?"disabled":"" %>>
          </div>
        </div>
      </div>
    <% } %>
    <div style="<%= (ID_EMPRESA == 171)?'display:none':'' %>">
      <input type="hidden" id="pedido_mesa_item_id_articulo">
      <input type="hidden" id="pedido_mesa_item_tipo">
      <div class="clearfix">
        <div class="col-sm-8 p3">
          <div class="form-group">
            <div class="input-group no-br">
              <input type="text" placeholder="Producto" class="form-control action no-model" id="pedido_mesa_item_articulo"/>
              <span class="input-group-btn">
                <button id="pedido_mesa_buscar_articulo" tabindex="-1" class="btn btn-default"><i class="fa fa-search"></i>&nbsp;&nbsp;Buscar&nbsp;</button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-sm-4 p3">
          <div class="form-group">
            <div class="input-group no-br no-br-both">
              <span class="input-group-addon addon_minus"><i class="fa fa-minus"></i></span>
              <input placeholder="Cantidad" min="0" type="text" id="pedido_mesa_item_cantidad" class="form-control tar"/>
              <span class="input-group-addon addon_plus"><i class="fa fa-plus"></i></span>
            </div>
          </div>
        </div>
        <input type="hidden" disabled class="form-control no-model" id="pedido_mesa_item_no_totalizar_reparto"/>
        <input type="hidden" disabled class="form-control no-model" id="pedido_mesa_item_subtotal" placeholder="Subtotal"/>
      </div>
      <div class="clearfix">
        <div class="col-sm-8 p3">
          <input type="text" placeholder="Descripci&oacute;n" class="form-control no-model" id="pedido_mesa_item_descripcion"/>
        </div>
        <div class="col-sm-4 p3">
          <div class="input-group no-br">
            <input placeholder="Precio" type="text" class="form-control action no-model" <%= (control.check("pedidos_mesas") == 2) ? "disabled" : "" %>  value="0.00" id="pedido_mesa_item_precio"/>    
            <span class="input-group-btn">
              <button title="Ingresar linea" id="pedido_mesa_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="b-a" style="overflow: auto; margin-top: 15px; height: 180px">
      <table id="tabla_items" class="table sortable m-b-none default footable">
        <thead class="bg-light">
          <tr>
            <% if (tipo != "D") { %>
              <th class="w10 tac" style="padding-left: 1px; padding-right: 1px;">Ord.</th>
            <% } %>
            <th class="w75">Cant.</th>
            <th>Producto</th>
            <th class="w75">Unit.</th>
            <th class="w75">Subtotal</th>
            <th class="w25"></th>
            <th class="w25"></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div class="m-t tar clearfix">
      <% if (tipo == "D") { %>
        <div class="fl">
          <div class="cb">
            <div class="checkbox">
              <label class="i-checks">
                <input type="checkbox" id="pedido_mesa_pagada" name="pagada" class="checkbox" value="1" <%= (pagada == 1)?"checked":"" %> ><i></i>
                El pedido ya fue pagado.
              </label>
            </div>
          </div>
          <div class="cb <%= (typeof FACTURACION_EDITAR_DESCUENTO != 'undefined' && FACTURACION_EDITAR_DESCUENTO==1)?"":"dn" %>">
            <label class="control-label">
              <span class="mr5 mt5">Descuento (%):</span>
              <input type="number" min="0" max="100" value="<%= porc_descuento %>" class="form-control w-xs pull-right action text-right" id="pedido_mesa_porc_descuento"/>
              <input type="text" disabled class="dn" id="pedido_mesa_descuento"/>
            </label>
          </div>
        </div>
      <% } %>
      <label class="control-label tar fs24 font-bold">Total: </label>
      <span id="pedido_mesa_total" style="margin-left: 20px; background-color: transparent; border: none; color: black; font-size: 24px; font-weight: bold; text-align: left; ">
        $ <%= Number(total).toFixed(2) %>
      </span>
    </div>
  </div>
  <div class="panel-footer tar clearfix">
    <% if (tipo == "M") { %>

      <?php // Tenemos perfil de MOZO ?>
      <% if (control.check("pedidos_mesas") == 2) { %>
        <% if (id_tipo_estado == 0) { %>
          <button class="btn guardar btn-success">Guardar</button>
          <% if (ID_EMPRESA == 162) { %>
            <button class="btn m-l-xs cerrar_mesa_efectivo btn-danger">Efectivo</button>
            <button class="btn m-l-xs cerrar_mesa_tarjeta btn-danger">Tarjeta</button>
          <% } else { %>
            <button class="btn m-l-xs cerrar_mesa btn-danger">Finalizar Pedido</button>
          <% } %>
        <% } %>

      <?php // Tenemos perfil de ADMINISTRADOR ?>
      <% } else if (control.check("pedidos_mesas") >= 3) { %>
        <% if (id_tipo_estado <= 3) { %>
          <div class="fl btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <% if (id_tipo_estado == 0 && items.length == 0) { %>
                <li><a href="javascript:void(0)" class="reservar">Reservar</a></li>
              <% } else if (id_tipo_estado == 1) { %>
                <li><a href="javascript:void(0)" class="eliminar_reservar">Eliminar Reservar</a></li>
              <% } %>
              <li><a href="javascript:void(0)" class="reasignar">Mover a otra mesa</a></li>
              <li><a href="javascript:void(0)" class="unir">Juntar con otra mesa</a></li>
            </ul>
          </div>
          <button class="btn guardar btn-success">Guardar</button>
          <% if (typeof id != "undefined") { %>
            <button class="btn m-l-xs imprimir btn-info">Imprimir</button>
          <% } %>

          <% if (ID_EMPRESA == 162) { %>
            <button class="btn m-l-xs cerrar_mesa_efectivo btn-danger">Efectivo</button>
            <button class="btn m-l-xs cerrar_mesa_tarjeta btn-danger">Tarjeta</button>
          <% } else { %>
            <button class="btn m-l-xs cerrar_mesa btn-danger">Finalizar Pedido</button>
          <% } %>
        <% } %>
      <% } %>

    <% } else if (tipo == "T" && id_tipo_estado == 0) { %>
        <button class="btn guardar btn-success">Guardar</button>

        <% if (ID_EMPRESA == 162) { %>
          <button class="btn m-l-xs cerrar_mesa_efectivo btn-danger">Efectivo</button>
          <button class="btn m-l-xs cerrar_mesa_tarjeta btn-danger">Tarjeta</button>
        <% } else { %>
          <button class="btn m-l-xs cerrar_mesa btn-danger">Finalizar Pedido</button>
        <% } %>

    <% } else if (tipo == "D") { %>
      <% if (ID_EMPRESA == 171) { %>
    	<button class="btn guardar btn-success">Guardar</button>  
      <% } else if (id_tipo_estado == 0) { %>
        <button class="btn guardar btn-success">Guardar</button>  
      <% } else if (id_tipo_estado == 2) { %>
        <!-- ESTADO PENDIENTE DE APROBACION POR EL COMERCIO -->
        <button class="btn pull-left rechazar_pedido btn-danger">Rechazar pedido</button>
        <button class="btn pull-right aceptar_pedido btn-success">Aceptar pedido</button>
      <% } %>
    <% } else if (tipo == "B") { %>
      <button class="btn guardar btn-success">Guardar</button>
    <% } %>
  </div>
</div>
</script>

<script type="text/template" id="pedido_mesa_item_template">
  <% if (origen != "D") { %>
    <td class="p3"><button class="btn btn-default orden"><%= orden %></button></td>
  <% } %>
  <td class="editar"><%= Number(cantidad).toFixed(2) %></td>
  <td class="editar">
    <span class="text-info"><%= nombre %></span>
    <% if (!isEmpty(descripcion)) { %>
      <br/><%= descripcion %>
    <% } %>
  </td>
  <td class="editar"><%= Number(precio).toFixed(2) %></td>
  <td class="editar"><%= Number(total_con_iva).toFixed(2) %></td>

  <?php // John puede editar siempre, incluso con el pedido cerrado. TODO: Hacer configurable esto y listo ?>
  <% if (ID_EMPRESA == 171) { %>
    <td class="w25 p3">
      <button class="btn btn-default eliminar">
        <i title="Eliminar" class="glyphicon glyphicon-remove text-danger"></i>
      </button>
    </td>
    <td class="w25 p3">
      <button class="btn btn-default duplicar">
        <i title="Agregar item" class="fa fa-plus"></i>
      </button>
    </td>
  <% } else { %>
    <?php // Tenemos perfil de MOZO ?>
    <% if (control.check("pedidos_mesas") == 2) { %>
      <% if (id_tipo_estado == 0) { %>
        <td class="w25 p3">
          <% if (typeof id == "undefined") { %>  
            <button class="btn btn-default eliminar">
              <i title="Eliminar" class="glyphicon glyphicon-remove text-danger"></i>
            </button>
          <% } %>
        </td>
        <td class="w25 p3">
          <button class="btn btn-default duplicar">
            <i title="Agregar item" class="fa fa-plus"></i>
          </button>
        </td>
      <% } %>
    <?php // Tenemos perfil de ADMINISTRADOR ?>
    <% } else if (control.check("pedidos_mesas") > 2) { %>
      <% if (id_tipo_estado <= 3 || ID_EMPRESA == 171) { %>
        <td class="w25 p3">
          <button class="btn btn-default eliminar">
            <i title="Eliminar" class="glyphicon glyphicon-remove text-danger"></i>
          </button>
        </td>
        <td class="w25 p3">
          <button class="btn btn-default duplicar">
            <i title="Agregar item" class="fa fa-plus"></i>
          </button>
        </td>
      <% } %>
    <% } %>
  <% } %>
</script>

<script type="text/template" id="pedido_mesa_reasignar_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    Mover mesa a:
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body pb0">
    <select id="pedido_mesa_reasignar_mesas" class="form-control"></select>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success"><span>Aceptar</span></button>
  </div>
</div>
</script>

<script type="text/template" id="pedido_mesa_unir_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    Unir mesa con:
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body pb0">
    <select id="pedido_mesa_unir_mesas" class="form-control"></select>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success"><span>Aceptar</span></button>
  </div>
</div>
</script>