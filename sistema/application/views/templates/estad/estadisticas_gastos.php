<script type="text/template" id="estadisticas_gastos_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">    
    <h1 class="m-n font-thin h3"><i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
      / <b>Gastos</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-5 sm-m-b">
            <div class="form-inline">    
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Desde" id="estadisticas_gastos_fecha_desde" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Hasta" id="estadisticas_gastos_fecha_hasta" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="form-group">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-filter mr5"></i> Filtros</button>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-7 sm-m-b">
            <div class="btn-group dropdown fr">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="fa fa-cog"></i><span>Exportar</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right">
                <li><a href="javascript:void" class="exportar">Todo</a></li>
                <li><a href="javascript:void" class="exportar_seleccionados">Seleccionados</a></li>
                <li><a href="javascript:void" class="exportar_con_valores">Con totales</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="display:none">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
          <div class="row pl10 pr10">

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <select id="estadisticas_gastos_mes" class="form-control w130">
                    <option <%= (window.estadisticas_gastos_mes_fiscal=="")?"selected":"" %> value=''>Mes Fiscal</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==1)?"selected":"" %> value='01'>Enero</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==2)?"selected":"" %> value='02'>Febrero</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==3)?"selected":"" %> value='03'>Marzo</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==4)?"selected":"" %> value='04'>Abril</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==5)?"selected":"" %> value='05'>Mayo</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==6)?"selected":"" %> value='06'>Junio</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==7)?"selected":"" %> value='07'>Julio</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==8)?"selected":"" %> value='08'>Agosto</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==9)?"selected":"" %> value='09'>Septiembre</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==10)?"selected":"" %> value='10'>Octubre</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==11)?"selected":"" %> value='11'>Noviembre</option>
                    <option <%= (window.estadisticas_gastos_mes_fiscal==12)?"selected":"" %> value='12'>Diciembre</option>
                  </select>                       
                  <span class="input-group-btn">
                    <input type="text" id="estadisticas_gastos_anio" class="form-control w80 mr10" value="<%= window.estadisticas_gastos_anio_fiscal %>"/>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <select class="form-control" id="estadisticas_gastos_sucursales">
                <% if (ID_SUCURSAL != 0) { %>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i] %>
                    <% if (ID_SUCURSAL == o.id) { %>
                      <option selected value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  <% } %>
                <% } else { %>
                  <option value="0">Sucursal</option>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i] %>
                    <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                <% } %>
              </select>   
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <select class="form-control" id="estadisticas_gastos_incluir_todas">
                  <option value='1'>Ver solo compras del resumen</option>
                  <option value='0'>Ver solo no incluidas</option>
                  <option value='-1'>Ver todas</option>
                </select>
              </div>
            </div>            

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <button class="generar btn btn-block btn-dark btn-default"><i class="fa fa-search"></i> Buscar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="estadisticas_gastos_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="w25">
                  <i class="expand cp fa fa-toggle-down icon"></i>
                </th>
                <th>Concepto</th>
                <th class="tar">Total</th>
                <th class="tac w40"></th>
              </tr>
            </thead>
            <tbody></tbody>
            <% if (ID_EMPRESA != 134) { %>
              <tfoot>
                <tr>
                  <td></td>
                  <td></td>
                  <td id="estadisticas_gastos_total" class="tar bold fs14">0.00</td>
                </tr>
              </tfoot>
            <% } %>
          </table>
        </div>
      </div>
    </div>
  </div>
</script>


<script type="text/template" id="estadisticas_gastos_item_resultados_template">
  <% var clase = ""; %>
  <td class="<%= clase %>">
    <% if(children.length > 0) { %>
      <i class="fa icon fa-plus"></i>
    <% } %>
  </td>
  <td class="<%= clase %>">
    <div class="checkbox mt0 mb0">
      <label class="i-checks">
        <input type="checkbox" value="<%= id %>">
        <i></i>
        <b data-nivel='<%= nivel %>' class='nombre fwn ml<%= nivel %>0'><%= nombre %></b>
      </label>
    </div>
  </td>
  <td class="<%= clase %> tar number">$ <%= Number(parseFloat(total)).format(2) %></td>
  <td class="<%= clase %> tac"><i title="Ver comprobantes" class="fa ver_listado fa-search"></i></td>
</script>