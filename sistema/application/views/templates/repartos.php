<script type="text/template" id="repartos_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
  <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-truck icono_principal"></i>Reparto de Mercader&iacute;a</h1>
  </div>
</div>
<div class="wrapper-md ng-scope">

  <div class="tab-container">
    <ul class="nav nav-tabs" role="tablist">
      <li class="active">
        <a href="#tab1" role="tab" data-toggle="tab">Por Articulos</a>
      </li>
      <li>
        <a href="#tab2" role="tab" data-toggle="tab">Por Facturas</a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane active panel-body p0">
      
        <div class="form-inline">
          <label class="control-label mr15">Reparto</label>
          <div class="input-group">
            <span class="input-group-btn">
              <input type="text" class="form-control no-model w40" id="repartos_numero" value="1"/>
            </span>
            <input type="text" id="repartos_fecha" value="<?php echo date("d/m/Y"); ?>" class="form-control action no-model w120">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>
          </div>
          <div class="form-group ml10">
            <button class="btn btn-default buscar_por_articulos"><i class="fa fa-search"></i> Buscar</button>
          </div>
          <div class="pull-right">
            <div class="btn-group dropdown">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void" class="imprimir">Imprimir</a></li>
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
              </ul>
            </div>       
          </div>
        </div>
      
        <div class="b-a mt15">
          <table id="repartos_tabla" class="table table-small sortable m-b-none default footable">
            <thead class="thead">
              <tr>
                <th class="w25">Reparto</th>
                <th class="w25">Cod.</th>
                <th>Articulo</th>
                <th class="w25">Facturado</th>
                <th class="w25">Devolucion</th>
                <th class="w25">Bonificado</th>
                <th class="w25">Total</th>
                <th class="w150">Bultos</th>
              </tr>
            </thead>
            <tbody class="tbody">
              <tr>
                <td colspan="20">Seleccione los parametros deseados y haga click en Buscar.</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td id="reparto_total_facturado" class="tar">0.00</td>
                <td id="reparto_total_devolucion" class="tar">0.00</td>
                <td id="reparto_total_bonificacion" class="tar">0.00</td>
                <td id="reparto_total" class="tar">0.00</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="mt15">
          <div class="form-inline">
            <span>Recambio: </span>
            <input type="text" id="repartos_porc_recambio" value="0.00000" disabled class="form-control w100 ml5 tar"/> %
          </div>
        </div>
      </div>
      <div id="tab2" class="tab-pane panel-body p0">
      
        <div class="form-inline">
          <label class="control-label mr15">Reparto</label>
          <div class="input-group">
            <span class="input-group-btn">
              <input type="text" class="form-control no-model w40" id="repartos_por_factura_numero" value="1"/>
            </span>
            <input type="text" id="repartos_por_factura_fecha" value="<?php echo date("d/m/Y"); ?>" class="form-control action no-model w120">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>
          </div>
          <div class="form-group ml10">
            <button class="btn btn-default buscar_por_facturas"><i class="fa fa-search"></i> Buscar</button>
          </div>
          <div class="pull-right">
            <div class="btn-group dropdown">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void" class="imprimir_facturas">Imprimir</a></li>
              <li><a href="javascript:void" class="exportar_por_facturas">Exportar Excel</a></li>
              </ul>
            </div>       
          </div>
        </div>
      
        <div class="b-a mt15">
          <table id="repartos_por_factura_tabla" class="table table-small sortable m-b-none default footable">
            <thead class="thead">
              <tr>
                <th class="w20">#</th>
                <th class="w25">Fecha</th>
                <th>Comprobante</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th class="tar">Total</th>
              </tr>
            </thead>
            <tbody class="tbody">
              <tr>
                <td colspan="20">Seleccione los parametros deseados y haga click en Buscar.</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td id="reparto_por_factura_total" class="tar">0.00</td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="mt15">
          <div class="form-inline">
            <span class="">Clientes: </span>
            <input type="text" id="repartos_cant_clientes" value="0" disabled class="form-control w100 ml5 tar"/>
            <span class="ml15">Facturas: </span>
            <input type="text" id="repartos_cant_facturas" value="0" disabled class="form-control w100 ml5 tar"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="repartos_item_resultados_template">
  <% var clase = ""; %>
  <td class="<%= clase %>"><%= reparto %></td>
  <td class="<%= clase %>"><%= codigo %></td>
  <td class="<%= clase %>"><%= descripcion %></td>
  <td class="<%= clase %> tar"><%= Number(facturado).toFixed(2) %></td>
  <td class="<%= clase %> tar"><%= Number(devolucion).toFixed(2) %></td>
  <td class="<%= clase %> tar"><%= Number(bonificacion).toFixed(2) %></td>
  <% var t = parseFloat(facturado) + parseFloat(devolucion) + parseFloat(bonificacion) %>
  <% uxb = parseFloat(uxb) %>
  <td class="<%= clase %> tar"><%= Number(t).toFixed(2) %></td>
  <td class="<%= clase %>"><%= (uxb > 1) ? "("+Number(Math.floor(t / uxb),0).toFixed(0)+" Bul."+(((t % uxb)!=0) ? ("+"+(t % uxb)+" unid.") : "")+")" : "" %></td>
</script>

<script type="text/template" id="repartos_por_factura_item_template">
  <% var clase = ""; %>
  <td><%= contador %></td>
  <td class="<%= clase %>"><%= fecha %></td>
  <td class="<%= clase %>"><%= comprobante %></td>
  <td class="<%= clase %>"><span class="text-info"><%= cliente %></span></td>
  <td class="<%= clase %>"><%= vendedor %></td>
  <td class="<%= clase %> tar"><%= (negativo==1)?"-":"" %><%= Number(total).toFixed(2) %></td>
</script>