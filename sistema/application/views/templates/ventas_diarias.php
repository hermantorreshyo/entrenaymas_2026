<script type="text/template" id="ventas_diarias_parametros_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Consulta de Ventas</h1>
</div>
<div class="wrapper-md pb0">
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
              <a href="#tab1" role="tab" data-toggle="tab">Buscar</a>
          </li>
          <li>
              <a href="#tab2" role="tab" data-toggle="tab">Avanzada</a>
          </li>
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body p0">
                <div class="form-horizontal">
                    <div class="form-group col-lg-3 col-sm-6 col-xs-10 m-b-none">
                        <div class="input-group">
                            <input type="text" class="form-control" id="ventas_diarias_desde" value="<?php echo date("d/m/Y"); ?>" />
                            <span class="input-group-btn">
                              <button disabled class="btn btn-default">-</button>
                            </span>
                            <input type="text" class="form-control" id="ventas_diarias_hasta" value="<?php echo date("d/m/Y"); ?>" />
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-xs-2 m-b-none">
                        <button class="btn btn-success generar btn-addon">
                            <i class="fa fa-search"></i>
                            <span class="hidden-xs">Buscar</span>
                        </button>
                    </div>                    
                </div>
            </div>
            <div id="tab2" class="tab-pane panel-body p0">
                <div class="form-group col-lg-2 col-md-3 col-sm-4 col-xs-6">
                    <input type="text" placeholder="Codigos" class="form-control" id="ventas_diarias_codigo"/>
                </div>
                <div class="form-group col-lg-2 col-md-3 col-sm-4 col-xs-6">
                    <select class="form-control" id="ventas_diarias_rubros"></select>
                </div>
                <div class="form-group col-lg-2 col-sm-3 col-xs-6">
                    <select class="form-control" id="ventas_diarias_tipo_venta">
                        <option value="0">Todas</option>
                        <option value="A">A</option>
                        <option value="B">B</option>                
                    </select>
                </div>
                <div class="form-group col-lg-2 col-sm-4 col-xs-6">
                    <div class="input-group">
                      <span class="input-group-btn">
                        <button id="ventas_diarias_buscar_proveedor" class="btn btn-default">Prov</button>
                      </span>
                      <input type="text" class="form-control" id="ventas_diarias_codigo_proveedor" placeholder="codigo"/>
                    </div>
                </div>			
                <div class="form-group col-lg-4 col-sm-8 col-xs-6">
                    <select class="form-control" id="ventas_diarias_proveedores"></select>
                </div>
            </div>
        </div>
    </div>
</div>
</script>


<script type="text/template" id="ventas_diarias_resultados_template">
<div class="wrapper-md ng-scope pt0">
    <div class="panel panel-default">
        <div class="panel-heading oh">
            <span class="font-bold m-t-xs pull-left">Resultados de B&uacute;squeda</span>
        
            <button class="btn pull-right btn-sm btn-info exportar btn-addon mr5">
                <i class="fa fa-file-excel-o"></i><span class="hidden-xs">Exportar</span>
            </button>
                
        </div>
        <div class="panel-body">
            <div class="b-a table-responsive">
                <table id="ventas_diarias_tabla" class="table table-striped sortable m-b-none default footable">
                    <thead>
                        <th>Cod.</th>
                        <th>Rubro / Producto</th>
                        <th>Unid.</th>
                        <th>UxB</th>
                        <th>Bultos</th>
                        <th>Final</th>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="pagination_container hide-if-no-paging"></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</script>


<script type="text/template" id="ventas_diarias_item_resultados_template">
    <td><%= id %></td>
    <td><%= descripcion %></td>
    <td class="tar"><%= Number(cantidad).format() %></td>
    <td class="tar"><%= Number(uxb).format() %></td>
    <td class="tar"><%= (uxb != 0) ? Number(cantidad / uxb).format() : 0 %></td>
    <td class="tar"><%= Number(total).format() %></td>
</script>