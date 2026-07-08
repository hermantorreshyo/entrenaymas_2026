<script type="text/template" id="procesar_pagos_campanias_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-dollar icono_principal"></i>Cobranza</h1>
    </div>
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading clearfix">
                <div class="row">
                    <div class="col-xs-12 sm-m-b">
                        <div class="form-group fl w180">
                            <div class="input-group">
                                <input type="text" title="Fecha" id="procesar_pagos_campanias_fecha" class="form-control no-model">
                                <span class="input-group-btn">
                                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                        <% if (control.check("vendedores")>0) { %>
                          <select style="float:left; width: 200px; display: inline-block" class="form-control" id="procesar_pagos_campanias_vendedores">
                            <option value="0">Vendedor</option>
                            <% for(var i=0;i< vendedores.length;i++) { %>
                                <% var o = vendedores[i]; %>
                                <option value="<%= o.id %>"><%= o.nombre %></option>
                            <% } %>
                          </select>
                        <% } %>
                        <button class="btn btn-default fl buscar"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="procesar_pagos_campanias_tabla" class="table table-small sortable m-b-none default">
                        <thead class="thead">
                            <th class="w25">Pago</th>
                            <th class="">Vendedor</th>
                            <th class="">Cliente</th>
                            <th class="">Campa&ntilde;a</th>
                            <th class="">Fecha</th>
                            <th class="">Comprobante</th>
                            <th class="tar th_acciones">Total</th>
                            <th style="width: 100px" class="tar">% Com.</th>
                            <th class="tar th_acciones">Comisi&oacute;n</th>
                            <th class="tar th_acciones">Diferencia</th>
                        </thead>
                        <tbody class="tbody"></tbody>
                        <tfoot class="bg-important">
                            <tr>
                                <td colspan="5"></td>
                                <td class="tar">Cobrado</td>
                                <td id="procesar_pagos_campanias_total_cobrado" class="tar bold">$ 0.00</td>
                                <td></td>
                                <td id="procesar_pagos_campanias_total_comisiones_cobrado" class="tar bold">$ 0.00</td>
                                <td id="procesar_pagos_campanias_total_resto_cobrado" class="tar bold">$ 0.00</td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td class="tar">Por cobrar</td>
                                <td id="procesar_pagos_campanias_total_por_cobrar" class="tar bold">$ 0.00</td>
                                <td></td>
                                <td id="procesar_pagos_campanias_total_comisiones_por_cobrar" class="tar bold">$ 0.00</td>
                                <td id="procesar_pagos_campanias_total_resto_por_cobrar" class="tar bold">$ 0.00</td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td class="tar">Total</td>
                                <td id="procesar_pagos_campanias_total" class="tar bold fs16 tar">$ 0.00</td>
                                <td></td>
                                <td id="procesar_pagos_campanias_total_comisiones" class="tar bold fs16">$ 0.00</td>
                                <td id="procesar_pagos_campanias_total_resto" class="tar bold fs16">$ 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="clearfix">
                    <button class="btn btn-success m-t guardar">Guardar</button>
                </div>
            </div>

        </div>
    </div>
</div>
</script>


<script type="text/template" id="procesar_pagos_campanias_item_resultados_template">
    <td><label class="i-checks m-b-none"><input tabindex="-1" <%= (pagada == "1") ? "checked" : "" %> type="checkbox"><i></i></label></td>
    <td class=""><%= vendedor %></td>
    <td class=""><%= cliente %></td>
    <td class=""><%= campania %></td>
    <td class=""><%= fecha %></td>
    <td class=""><%= comprobante %></td>
    <td class="tar">$ <%= Number(total).toFixed(2) %></td>
    <td class="tar"><input type="text" class="form-control comision no-model w100p" value="<%= Number(comision_vendedor).toFixed(2) %>" /></td>
    <td class="tar">$ <%= Number(comision).toFixed(2) %></td>
    <td class="tar">$ <%= Number(diferencia).toFixed(2) %></td>
</script>