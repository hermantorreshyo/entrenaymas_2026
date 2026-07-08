<script type="text/template" id="megashop_cambios_precios_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Cambio de Precios</h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="centrado">
      <div class="panel panel-default pull-in">
        <div class="panel-body">
          <div class="clearfix">

            <div class="col-sm-4 p0 form-group">
              <label>Proveedor </label>
              <div class="input-group">
                <input type="text" class="dn" id="megashop_cambios_precios_id_proveedor" value=""/>
                <input title="Ingrese el codigo de Proveedor o comience a escribir parte del nombre" type="text" class="form-control action" id="megashop_cambios_precios_codigo_proveedor" placeholder="Nombre o codigo de proveedor" value=""/>
                <span class="input-group-btn">
                  <button title="Atajo: F2 = Buscar" id="megashop_cambios_precios_buscar_proveedor" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                </span>
              </div>      
            </div>

            <div class="col-sm-4 p0 form-group">
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
                <input type="hidden" id="megashop_cambios_precios_id_articulo"/>
                <input type="text" placeholder="C&oacute;digo..." class="form-control action no-model" id="megashop_cambios_precios_codigo_articulo"/>
              </div>
              <div class="col-sm-7 p0">
                <label class="text-muted">Descripci&oacute;n</label>
                <div class="input-group">
                  <input disabled type="text" class="form-control action no-model" id="megashop_cambios_precios_item_nombre"/>
                  <span class="input-group-btn">
                    <button tabindex="-1" id="megashop_cambios_precios_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="clearfix">
            <div class="col-sm-2 p0 form-group">
              <label class="text-muted">Costo Neto</label>
              <input type="text" class="form-control action no-model" value="0.00" id="megashop_cambios_precios_item_costo_neto_inicial"/>
            </div>            
            <div class="col-sm-6 p0 form-group">
              <div class="col-sm-3 p0">
                <label class="text-muted">% Dtos.</label>
                <input type="text" class="form-control action no-model" placeholder="0 %" id="megashop_cambios_precios_item_dto_prov"/>
              </div>
              <div class="col-sm-9 p0">
                <div class="col-sm-3 p0">
                  <label class="text-muted">&nbsp;</label>
                  <input type="text" class="form-control action no-model" placeholder="0 %" id="megashop_cambios_precios_item_dto_prov_2"/>
                </div>
                <div class="col-sm-3 p0">
                  <label class="text-muted">&nbsp;</label>
                  <input type="text" class="form-control action no-model" placeholder="0 %" id="megashop_cambios_precios_item_dto_prov_3"/>
                </div>
                <div class="col-sm-3 p0">
                  <label class="text-muted">&nbsp;</label>
                  <input type="text" class="form-control action no-model" placeholder="0 %" id="megashop_cambios_precios_item_dto_prov_4"/>
                </div>
                <div class="col-sm-3 p0">
                  <label class="text-muted">&nbsp;</label>
                  <input type="text" class="form-control action no-model" placeholder="0 %" id="megashop_cambios_precios_item_dto_prov_5"/>
                </div>
              </div>
            </div>            
            <div class="col-sm-4 p0 form-group">
              <div class="col-sm-4 p0">
                <label class="text-muted">Neto c/Dto</label>
                <input type="text" class="form-control action no-model" disabled id="megashop_cambios_precios_item_costo_neto"/>
              </div>
              <div class="col-sm-4 p0">
                <label class="text-muted">% IVA</label>
                <select id="megashop_cambios_precios_alicuotas_iva" class="form-control action no-model">
                  <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                  <% var o = alicuotas_iva[i]; %>
                    <option value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                  <% } %>
                </select>
              </div>
              <div class="col-sm-4 p0">
                <label class="text-muted">Costo Final</label>
                <input type="text" class="form-control action no-model" disabled value="0.00" id="megashop_cambios_precios_item_costo_final"/>
              </div>              
            </div>
          </div>

          <div class="clearfix">
            <% for(var i=0; i< megashop_sucursales.length; i++) { %>
              <% var suc = megashop_sucursales[i] %>
              <div class="col-sm-2 p0 form-group sucursal sucursal_<%= suc.id %>">
                <div class="col-sm-5 p0">
                  <label class="text-muted">% Gan.</label>
                  <input type="text" class="form-control action no-model porc_ganancia" placeholder="0 %" id="megashop_cambios_precios_item_porc_ganancia_<%= suc.id %>"/>
                </div>
                <div class="col-sm-7 p0">
                  <label class="bold"><%= suc.nombre %></label>                  
                  <input type="text" class="form-control no-model precio_final" id="megashop_cambios_precios_precio_final_<%= suc.id %>"/>
                  <input type="hidden" disabled class="dn form-control no-model" id="megashop_cambios_precios_precio_neto_<%= suc.id %>"/>
                </div>
              </div>
            <% } %>
          </div>

        </div>
      </div>

      <div class="panel panel-default pull-in">
        <div class="panel-body">
          <div class="clearfix">
            <div class="b-a" style="overflow: auto; height: 250px;">
              <table id="tabla_items" class="table table-small sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th>Cod.</th>
                    <th>Descripci&oacute;n</th>
                    <th class="w100">C. Neto</th>
                    <th class="w100">C. Final</th>
                    <% for(var i=0; i< megashop_sucursales.length; i++) { %>
                      <% var suc = megashop_sucursales[i] %>                    
                      <th class="w100"><%= suc.nombre %></th>
                    <% } %>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>            

          </div>
        </div>
      </div>

      <div class="oh m-t m-b tar pull-in">
        <button class="btn btn-success confirmar btn-addon"><i class="icon fa fa-plus"></i>Confirmar</button>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="megashop_cambios_precios_item_tabla_template">
  <td class="editar"><%= codigo %></td>
  <td class="editar"><span class="text-info"><%= nombre %></span></td>
  <td class="editar"><%= Number(costo_neto).format(2) %></td>
  <td class="editar"><%= Number(costo_final).format(2) %></td>
  <% for(var i=0; i< precios.length; i++) { %>
    <% var suc = precios[i] %>                    
    <th class="w100"><%= Number(suc.valor).format(2) %></th>
  <% } %>
  <td class="w25 p5">
    <% if (control.check("megashop_cambios_precios") > 2) { %>
      <i title="Eliminar" class="glyphicon glyphicon-remove eliminar_flechita text-danger" />
    <% } %>
  </td>
</script>