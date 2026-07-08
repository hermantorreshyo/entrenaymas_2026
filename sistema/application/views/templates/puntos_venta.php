<script type="text/template" id="puntos_venta_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
    / <b>Puntos de Venta</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="search_container"></div>
        </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn btn-info btn-addon" href="app/#punto_venta"><i class="fa fa-plus"></i>Nuevo</a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="puntos_venta_table" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <% if (VOLVER_SUPERADMIN == 1) { %>
                <th class="w25">ID</th>
              <% } %>
              <th class="w100">Default</th>
              <th class="w25">Activo</th>
              <th class="sorting" data-sort-by="numero">Numero Caja</th>
              <th class="sorting" data-sort-by="numero_fiscal">Numero Fiscal</th>
              <th class="sorting" data-sort-by="nombre">Nombre</th>
              <% if (permiso > 1) { %>
                <th class="w25"></th>
              <% } %>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="puntos_venta_item">
  <% if (VOLVER_SUPERADMIN == 1) { %>
    <td><span class='ver'><%= id %></span></td>
  <% } %>
  <td><span class='ver'><%= (por_default==1)?"Si":"No" %></span></td>
  <td><span class='ver'><%= (activo==1)?"Si":"No" %></span></td>
  <td><span class='ver'><%= numero %></span></td>
  <td><span class='ver'><%= numero_fiscal %></span></td>
  <td><span class='ver'><%= nombre %></span></td>
  <% if (permiso > 1) { %>
    <td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
  <% } %>
</script>

<script type="text/template" id="puntos_venta_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
    / Puntos de Venta
    / <b><%= (id == undefined) ? "Nuevo" : nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" id="puntos_venta_nombre" value="<%= nombre %>" <%= (!edicion) ? "disabled":"" %>/>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Numero Caja</label>
                  <input type="text" name="numero" class="form-control" id="puntos_venta_numero" value="<%= numero %>" <%= (!edicion) ? "disabled":"" %>/>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Numero Fiscal</label>
                  <input type="text" name="numero_fiscal" class="form-control" id="puntos_venta_numero_fiscal" value="<%= numero_fiscal %>" <%= (!edicion) ? "disabled":"" %>/>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Domicilio Fiscal</label>
                  <input type="text" name="direccion" class="form-control" id="puntos_venta_direccion" value="<%= direccion %>" <%= (!edicion) ? "disabled":"" %>/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Localidad</label>
                  <input type="text" name="localidad" class="form-control" id="puntos_venta_localidad" value="<%= localidad %>" <%= (!edicion) ? "disabled":"" %>/>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group cb">
                  <label class="control-label">Activo </label>
                    <% if (edicion) { %>
                      <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                      <input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> >
                      <i></i>
                      </label>
                    <% } else { %>
                      <span><%= ((activo==0) ? "No" : "Si") %></span>
                    <% } %>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group cb">
                  <label class="control-label">Por defecto </label>
                  <% if (por_default) { %>
                    <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                    <input type="checkbox" name="por_default" class="checkbox" value="1" <%= (por_default == 1)?"checked":"" %> >
                    <i></i>
                    </label>
                  <% } else { %>
                    <span><%= ((por_default==0) ? "No" : "Si") %></span>
                  <% } %>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Tipo de Impresion</label>
                  <select class="form-control" id="puntos_venta_tipo_impresion" name="tipo_impresion" <%= (!edicion) ? "disabled":"" %>>
                    <option value="E" <%= (tipo_impresion == "E") ? "selected": "" %>>Factura Electronica</option>
                    <option value="F" <%= (tipo_impresion == "F") ? "selected": "" %>>Impresor Fiscal</option>
                    <option value="P" <%= (tipo_impresion == "P") ? "selected": "" %>>Factura Preimpresa (No Electronica)</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Utilizado en</label>
                  <select class="form-control" id="puntos_venta_tipo_uso" name="tipo_uso" <%= (!edicion) ? "disabled":"" %>>
                    <option value="" <%= (tipo_uso == "") ? "selected": "" %>>Punto de venta</option>
                    <option value="W" <%= (tipo_uso == "W") ? "selected": "" %>>Web</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <% if (control.check("almacenes")>0) { %>
                  <div class="form-group">
                    <label class="control-label">Sucursal</label>
                    <select id="punto_venta_sucursales" name="id_sucursal" class="form-control no-model" <%= (!edicion) ? "disabled":"" %>>
                      <option value="0">Todas</option>
                      <% for(var i=0; i< almacenes.length; i++) { %>
                        <% var alm = almacenes[i] %>
                        <option <%= (id_sucursal == alm.id)?"selected":"" %> value="<%= alm.id %>"><%= alm.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                <% } %>
              </div>
            </div>
            
            <div class="row" id="puntos_venta_imp_fiscal_container" style="display:<%= (tipo_impresion != "F") ? "none":"block" %>">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Impresor Fiscal</label>
                  <select class="form-control" id="puntos_venta_imp_fiscal" name="imp_fiscal" <%= (!edicion) ? "disabled":"" %>>
                    <option value="" <%= (imp_fiscal == "") ? "selected": "" %>>Seleccione</option>
                    <option value="Hasar" <%= (imp_fiscal == "Hasar") ? "selected": "" %>>Hasar SMH/P-715F</option>
                    <option value="Epson" <%= (imp_fiscal == "Epson") ? "selected": "" %>>Epson</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Puerto</label>
                  <input type="text" class="form-control" id="puntos_venta_numero_puerto" name="numero_puerto" value="<%= numero_puerto %>"/>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Velocidad</label>
                  <input type="text" class="form-control" id="puntos_venta_velocidad" name="velocidad" value="<%= velocidad %>"/>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-4">
                <label class="control-label">Caja</label>
                <select id="puntos_venta_caja" class="form-control no-model">
                  <option <%= (id_caja == 0)?"selected":"" %> value="0">Seleccione</option>
                  <% for (var i=0;i< window.cajas.length; i++) { %>
                    <% var o = window.cajas[i] %>
                    <option <%= (id_caja == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                </select>
              </div>
              <div class="col-md-4">
                <div class="form-group" id="puntos_venta_disenio_factura_container" style="display: <%= (tipo_impresion == "F") ? "none":"block" %>">
                  <label class="control-label">Dise&ntilde;o de Comprobante</label>
                  <div class="">
                    <% if (edicion) { %>
                      <select class="form-control" id="puntos_venta_disenio_factura" name="disenio_factura">
                        <option value="" <%= (disenio_factura == "") ? "selected": "" %>>Seleccione</option>
                        <option value="pedido" <%= (disenio_factura == "pedido") ? "selected": "" %>>Nota Pedido</option>
                        <option value="basico" <%= (disenio_factura == "basico") ? "selected": "" %>>Basico</option>
                        <option value="bolsas" <%= (disenio_factura == "bolsas") ? "selected": "" %>>Basico 2</option>
                        <option value="termica" <%= (disenio_factura == "termica") ? "selected": "" %>>Termica</option>
                        <option value="modelo1" <%= (disenio_factura == "modelo1") ? "selected": "" %>>Moderno</option>
                        <option value="distribuidora" <%= (disenio_factura == "distribuidora") ? "selected": "" %>>Distribuidora</option>
                      </select>
                    <% } else { %>
                      <span><%= disenio_factura %></span>
                    <% } %>
                  </div>
                </div>  
              </div>
              <div class="col-md-4">
                <div class="form-group" id="puntos_venta_disenio_factura_color_container" style="display: <%= (tipo_impresion == "F") ? "none":"block" %>">
                  <label class="control-label">Color</label>
                  <div class="">
                    <% if (edicion) { %>
                      <select class="form-control" id="puntos_venta_disenio_factura_color" name="disenio_factura_color">
                        <option value="red" <%= (disenio_factura_color == "red") ? "selected": "" %>>Rojo</option>
                        <option value="yellow" <%= (disenio_factura_color == "yellow") ? "selected": "" %>>Amarillo</option>
                        <option value="pink" <%= (disenio_factura_color == "pink") ? "selected": "" %>>Rosa</option>
                        <option value="green" <%= (disenio_factura_color == "green") ? "selected": "" %>>Verde</option>
                        <option value="blue" <%= (disenio_factura_color == "blue") ? "selected": "" %>>Azul</option>
                        <option value="dark_blue" <%= (disenio_factura_color == "dark_blue") ? "selected": "" %>>Azul Oscuro</option>
                      </select>
                    <% } else { %>
                      <span><%= disenio_factura_color %></span>
                    <% } %>
                  </div>
                </div> 
              </div>
            </div>
          </div>      
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Numeraci&oacute;n</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  N&uacute;meros correspondientes a cada tipo de comprobante.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="form-horizontal">
              <?php foreach($comprobantes as $c) { ?>
                <div class="form-inline m-b">
                  <label class="control-label m-r bold w-sm tal"><?php echo $c->nombre ?></label>
                  <input type="text" id="numero_comp_<?php echo $c->id ?>" name="numero_comp_<?php echo $c->id ?>" class="form-control m-r" value="<%= numero_comp_<?php echo $c->id ?> %>"/>
                  <label class="control-label m-r">Copias</label>
                  <input type="text" id="copias_comp_<?php echo $c->id ?>" name="copias_comp_<?php echo $c->id ?>" class="form-control m-r" value="<%= copias_comp_<?php echo $c->id ?> %>"/>
                  <?php if ($c->id < 900) { ?>
                  <button style="display:<%= (tipo_impresion == 'E') ? 'inline-block':'none' %>" id="sincronizar_<?php echo $c->id ?>" class="sincronizar btn btn-default">Sincronizar</button>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>

      </div>
    </div>
  
    <% if (edicion) { %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1 tar">
          <div class="line b-b m-b-lg"></div>
          <button class="btn btn-success guardar">Guardar</button>
        </div>
      </div>
    <% } %>

  </div>
</div>
</script>
