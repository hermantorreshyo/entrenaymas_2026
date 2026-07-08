<script type="text/template" id="compras_listado_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-shopping-cart icono_principal"></i>Compras</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <?php $active = "compras_listado"; include("compras/compras_menu.php"); ?>

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-8 sm-m-b">
            <div class="input-group">
              <input type="text" id="compras_listado_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
              </span>

              <span class="input-group-btn">
                <div class="btn-group dropdown ml5">
                  <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bank"></i><span><?php echo lang(array("es"=>"Impuestos","en"=>"Taxes")); ?></span>
                  </button>
                  <ul class="dropdown-menu pull-right">              
                    <li><a href="javascript:void" class="iva_compras">IVA Compras</a></li>
                    <% if (RETIENE_IB == 1) { %>
                      <li><a href="javascript:void" class="retencion_iibb">Retencion IIBB</a></li>
                    <% } %>
                    <% if (RETIENE_GANANCIAS == 1) { %>
                      <li><a href="javascript:void" class="retencion_ganancias">Retencion Ganancias</a></li>
                    <% } %>
                  </ul>
                </div>
              </span>

              <span class="input-group-btn">
                <div class="btn-group dropdown ml5">
                  <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
                    <li><a href="javascript:void" class="exportar_csv">Exportar CSV</a></li>
                  </ul>
                </div>
              </span>

              <span class="input-group-btn">
                <div class="btn-group dropdown ml5">
                  <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-download"></i><span><?php echo lang(array("es"=>"Importar","en"=>"Import")); ?></span>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void" class="importar">Importar Excel</a></li>
                    <li><a href="javascript:void" class="importar_csv">Importar CSV</a></li>
                  </ul>
                </div>
              </span>

            </div>
          </div>      
          <% if (!seleccionar) { %>
          <div class="col-md-4 text-right">
            <a class="btn btn-info btn-addon btn-block-xs" href="app/#compras">
              <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nueva Compra&nbsp;&nbsp;</span>
            </a>
          </div>
          <% } %>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="<%= (id_concepto!=0) ? 'display:block':'display:none' %>">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
          <div class="row pl10 pr10">

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" placeholder="Desde" autocomplete="off" id="compras_desde" class="form-control">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" placeholder="Hasta" autocomplete="off" id="compras_hasta" class="form-control">
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <select class="form-control" id="compras_conceptos">
                  <option value='0'>Concepto</option>
                  <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
                </select>
              </div>
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <select class="form-control" id="compras_sucursales">
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
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-btn">
                    <select class="form-control w130" id="compras_movimiento_mes">
                      <option value="00">Movimiento</option>
                      <option <%= (mes==1)?"selected":"" %> value="01">Enero</option>
                      <option <%= (mes==2)?"selected":"" %> value="02">Febrero</option>
                      <option <%= (mes==3)?"selected":"" %> value="03">Marzo</option>
                      <option <%= (mes==4)?"selected":"" %> value="04">Abril</option>
                      <option <%= (mes==5)?"selected":"" %> value="05">Mayo</option>
                      <option <%= (mes==6)?"selected":"" %> value="06">Junio</option>
                      <option <%= (mes==7)?"selected":"" %> value="07">Julio</option>
                      <option <%= (mes==8)?"selected":"" %> value="08">Agosto</option>
                      <option <%= (mes==9)?"selected":"" %> value="09">Sep</option>
                      <option <%= (mes==10)?"selected":"" %> value="10">Octubre</option>
                      <option <%= (mes==11)?"selected":"" %> value="11">Noviembre</option>
                      <option <%= (mes==12)?"selected":"" %> value="12">Diciembre</option>            
                    </select>
                  </span>
                  <input type="text" value="<%= anio %>" placeholder="A&ntilde;o" class="form-control w80 mr10" id="compras_movimiento_anio"/>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <div class="btn-group dropdown w100p">
                  <button class="btn btn-default tal btn-block dropdown-toggle" data-toggle="dropdown">
                    <span>Tipos de comprobante</span>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="compras_tipo_comprobante_check" checked="" value="1-6-11-51-201-206">
                          <i></i>Factura
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="compras_tipo_comprobante_check" checked="" value="3-8-13-53-203-208">
                          <i></i>Nota de Cr&eacute;dito
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="compras_tipo_comprobante_check" checked="" value="2-7-12-52-202-207">
                          <i></i>Nota de D&eacute;bito
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="compras_tipo_comprobante_check" checked="" value="4-9-15">
                          <i></i>Recibo
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="compras_tipo_comprobante_check" checked="" value="999">
                          <i></i>Remito
                        </label>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <select class="form-control" id="compras_incluir_todas">
                  <option value='1'>Ver solo compras del resumen</option>
                  <option value='0'>Ver solo no incluidas</option>
                  <option value='-1'>Ver todas</option>
                </select>
              </div>
            </div>

            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <button class="buscar btn btn-block btn-dark btn-default"><i class="fa fa-search"></i> Buscar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php /*
    <% if (!seleccionar) { %>
      <div class="bulk_action wrapper pb0">
      <button class="btn btn-default enviar btn-addon"><i class="icon fa fa-send"></i>Enviar por email</button>
      </div>
    <% } %>
    */ ?>
    <div class="panel-body resumen pb0" style="display:none">
      <div class="row">
        <div class="col-sm-9">
          <div class="row">
            <div class="col-sm-3">
              <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
                <span id="compras_resumen_cantidad" class="font-thin h3 block">0</span>
                <span class="text-muted text-md pt5 db">Cantidad</span>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="block tac panel padder-v item mb0" style="height: 80px">
                <span id="compras_resumen_neto" class="font-thin h3 block">0</span>
                <span class="text-muted text-md pt5 db">Neto</span>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="block tac panel padder-v item mb0" style="height: 80px">
                <span id="compras_resumen_iva" class="font-thin h3 block">0</span>
                <span class="text-muted text-md pt5 db">IVA</span>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="block tac panel padder-v item mb0" style="height: 80px">
                <span id="compras_resumen_reg_especiales" class="font-thin h3 block">0</span>
                <span class="text-muted text-md pt5 db">Reg. Especiales</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
            <div id="compras_resumen_total" class="h3 font-thin text-white block">0</div>
            <span class="text-muted text-md pt5 db">Total</span>
          </div>
        </div>
      </div>
    </div>


    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="compras_tabla" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <% if (!seleccionar) { %>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>            
              <% } else { %>
                <th style="width:20px;"></th>
              <% } %>
              <% for(var i=0; i< tabla_compras.campos.length; i++) { %>
                <% var c = tabla_compras.campos[i] %>
                <% if (c.visible == 1) { %>
                  <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  ><%= (c.campo == "path")?"":c.titulo %></th>
                <% } %>
              <% } %>              
              <% if (!seleccionar) { %>
                <th class="th_acciones w30">
                  <% if (control.check("compras_listado") > 2) { %>
                    <i class="fa configurar_tabla cp fa-cog pull-right mt3"></i>
                  <% } %>
                </th>
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

<script type="text/template" id="compras_item_resultados_template">
  <% var clase = "edit"; %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= id %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>  
  <% } %>
  <% for(var i=0; i< tabla_compras.campos.length; i++) { %>
    <% var c = tabla_compras.campos[i] %>

    <% if (c.campo == "fecha" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= fecha %></td>

    <% } else if (c.campo == "proveedor" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="text-info"><%= proveedor %></span>
      </td>

    <% } else if (c.campo == "tipo_comprobante" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= tipo_comprobante %></td>

    <% } else if (c.campo == "comprobante" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= comprobante %></td>

    <% } else if (c.campo == "total_neto" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= Number(total_neto).format() %></td>

    <% } else if (c.campo == "observaciones" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= observaciones %></td>

    <% } else if (c.campo == "total_iva" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= Number(total_iva).format() %></td>

    <% } else if (c.campo == "total_regimenes_especiales" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= Number(total_regimenes_especiales).format() %></td>

    <% } else if (c.campo == "total_general" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %>"><span class="tag_precio data">$ <%= Number(total_general).format() %></span></td>

    <% } else if (c.campo == "sucursal" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>"><%= sucursal %></td>

    <% } else if (c.campo == "observaciones" && c.visible == 1) { %>
      <td class="<%= clase %> <%= c.clases %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (!isEmpty(observaciones)) { %>
          <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-commenting text-warning"></i>
        <% } %>
      </td>
    <% } %>
  <% } %>

  <% if (!seleccionar) { %>
    <td class="p5 td_acciones">
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <% if (control.check("compras_listado")>2) { %>
            <li><a href="javascript:void(0)" class="imprimir" data-id="<%= id %>">Imprimir</a></li>
            <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
          <% } %>
        </ul>
      </div>  
    </td>
  <% } %>
</script>

<script type="text/template" id="retencion_iibb_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Exportar Retenciones de Ingresos Brutos</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Desde</label>
            <div class="input-group">
              <input type="text" id="retencion_iibb_desde" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Hasta</label>
            <div class="input-group">
              <input type="text" id="retencion_iibb_hasta" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="exportar btn btn-default">Exportar</button>
    </div>
  </div>
</script>

<script type="text/template" id="retencion_ganancias_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Exportar Retenciones de Ganancias</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Desde</label>
            <div class="input-group">
              <input type="text" id="retencion_ganancias_desde" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Hasta</label>
            <div class="input-group">
              <input type="text" id="retencion_ganancias_hasta" class="form-control no-model">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="i-checks">
          <input type="checkbox" class="compras_tipo_comprobante_check" name="2da_quincena" value="1">
          <i></i> Exportar los datos de la segunda quincena.
        </label>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="generar btn btn-default">Exportar</button>
    </div>
  </div>
</script>

<script type="text/template" id="iva_compras_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Reporte de IVA Compras</div>
    <div class="panel-body">
      <% if (control.check("razones_sociales")>0) { %>
        <div class="form-group">
          <label class="control-label mt7 mr15">Razon Social</label>
          <select class="form-control" id="iva_compras_razones_sociales"></select>
        </div>
      <% } %>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Movimiento</label>
            <div class="input-group">
              <select id="iva_compras_movimiento_mes" class="form-control">
                <option <%= (mes=="1")?"selected":"" %> value='01'>Enero</option>
                <option <%= (mes=="2")?"selected":"" %> value='02'>Febrero</option>
                <option <%= (mes=="3")?"selected":"" %> value='03'>Marzo</option>
                <option <%= (mes=="4")?"selected":"" %> value='04'>Abril</option>
                <option <%= (mes=="5")?"selected":"" %> value='05'>Mayo</option>
                <option <%= (mes=="6")?"selected":"" %> value='06'>Junio</option>
                <option <%= (mes=="7")?"selected":"" %> value='07'>Julio</option>
                <option <%= (mes=="8")?"selected":"" %> value='08'>Agosto</option>
                <option <%= (mes=="9")?"selected":"" %> value='09'>Septiembre</option>
                <option <%= (mes=="10")?"selected":"" %> value='10'>Octubre</option>
                <option <%= (mes=="11")?"selected":"" %> value='11'>Noviembre</option>
                <option <%= (mes=="0")?"selected":"" %> value='12'>Diciembre</option>
              </select>            
              <span class="input-group-btn">
                <input type="text" id="iva_compras_movimiento_anio" class="form-control w80" value="<%= anio %>"/>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Nro. Página</label>
            <input type="number" class="form-control" value="1" id="iva_compras_desde" />
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <div class="btn-group dropdown">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Exportar archivos
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void" class="citi_comprobantes">CITI Comprobantes</a></li>
          <li><a href="javascript:void" class="citi_alicuotas">CITI Alicuotas</a></li>
          <li><a href="javascript:void" class="citi">Ambos</a></li>
        </ul>
      </div>
      <button class="iva_excel btn btn-default">Exportar Excel</button>
      <button class="imprimir btn btn-default">Imprimir</button>
    </div>
  </div>
</script>


<script type="text/template" id="cargar_compras_template">
<div class="bg-light lter b-b wrapper-md">
  <div class="row clearfix">
    <div class="col-xs-12 col-sm-6">
      <h1 class="m-n font-thin h3"><i class="fa fa fa-shopping-cart icono_principal"></i>Compras 
        / <b><%= (id == undefined)?"Nueva":"Edicion" %></b>
      </h1>
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
            <li><a href="javascript:void" class="exportar">Importar de remito</a></li>
            <li><a href="javascript:void" class="exportar_csv">Importar presupuesto</a></li>
            <li><a href="javascript:void" class="importar_factura">Importar de factura</a></li>
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
    <div class="panel-heading font-bold">Datos de Comprobante</div>
    <div class="panel-body pl0 pr0">
      <div class="clearfix m-b">
        <div class="col-md-3 col-sm-6">
          <label>Proveedor <i title="Click para ayuda" class="buscar_proveedores_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></label>
          <div class="input-group">
            <input type="text" class="dn" id="cargar_compras_id_proveedor" value=""/>
            <input title="Ingrese el codigo de Proveedor o comience a escribir parte del nombre" type="text" class="form-control action" id="cargar_compras_codigo_proveedor" placeholder="Nombre o codigo de proveedor" value=""/>
            <span class="input-group-btn">
            <button title="Atajo: F2 = Buscar" id="cargar_compras_buscar_proveedor" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
            </span>
          </div>      
        </div>
        <div class="col-md-3 col-sm-6">
          <label>Fecha Comprobante</label>
          <div class="input-group">
            <input type="text" id="cargar_compras_fecha" name="fecha" value="<%= isEmpty(fecha) ? '<?php echo date("d/m/Y");?>' : fecha %>" class="form-control key">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>        
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <label>Tipo de Comprobante</label>
          <select class="form-control" id="cargar_compras_tipo"></select>
        </div>
        <div class="col-md-3 col-sm-6">
          <label>N&uacute;mero</label>
          <div class="input-group">
            <span class="input-group-btn">
              <input value="<%= numero_1 %>" type="number" class="form-control w75" id="cargar_compras_numero_1" maxlength="4" name="numero_1"/>
            </span>
            <input value="<%= numero_2 %>" type="number" class="form-control" id="cargar_compras_numero_2" maxlength="8" name="numero_2"/>
          </div>
        </div>
      </div>
      <div class="clearfix">
        <div class="col-md-2 col-sm-6">
          <label>Forma de Pago</label>
          <select class="form-control key" id="cargar_compras_forma_pago" name="forma_pago">
            <option <%=(forma_pago=="C")?"selected":"" %> value="C">Cuenta Corriente</option>
            <% if (ID_EMPRESA == 249 && ID_SUCURSAL != 0) { %>
            <% } else { %>
              <option <%=(forma_pago=="E")?"selected":"" %> value="E">Efectivo</option>
            <% } %>
          </select>
        </div>
        <div class="col-md-2 col-sm-6">
          <label>Caja</label>
          <select class="form-control key" <%= (forma_pago=="C")?"disabled":"" %> id="cargar_compras_cajas" name="id_caja">
            <option value="0">-</option>
            <% for(var q=0;q<cajas.length;q++) { %>
              <% var c = cajas[q] %>
              <% if (c.tipo == 0) { %>
                <option <%=(id_caja==c.id)?"selected":"" %> value="<%= c.id %>"><%= c.nombre %></option>
              <% } %>
            <% } %>
          </select>
        </div>
        <div class="col-md-2 col-sm-6">
          <label>Sucursal</label>
          <select class="form-control" id="cargar_compras_sucursales">
            <% if (ID_SUCURSAL != 0) { %>
              <% for(var i=0;i< window.almacenes.length;i++) { %>
                <% var o = almacenes[i] %>
                <% if (ID_SUCURSAL == o.id) { %>
                  <option selected value="<%= o.id %>"><%= o.nombre %></option>
                <% } %>
              <% } %>
            <% } else { %>
              <% for(var i=0;i< window.almacenes.length;i++) { %>
                <% var o = almacenes[i]; %>
                <option <%= (o.id == id_sucursal)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
              <% } %>
            <% } %>
          </select>
        </div>
        <div class="col-md-3 col-sm-6">
          <label>Movimiento <i title="Click para ayuda" class="movimiento_ayuda fs14 ml5 cp text-muted fa fa-question-circle"></i></label>
          <div class="input-group">
            <span class="input-group-btn">
              <select class="form-control key w120" id="cargar_compras_movimiento_mes">
                <option value="01">Enero</option>
                <option value="02">Febrero</option>
                <option value="03">Marzo</option>
                <option value="04">Abril</option>
                <option value="05">Mayo</option>
                <option value="06">Junio</option>
                <option value="07">Julio</option>
                <option value="08">Agosto</option>
                <option value="09">Sep</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>            
              </select>
            </span>
            <input type="number" min="0" class="enterToNext form-control key" id="cargar_compras_movimiento_anio"/>
          </div>
        </div>        
      </div>
    </div>
  </div>
  
  <div class="panel panel-info pull-in">
    <div class="panel-heading font-bold">Previsualizaci&oacute;n</div>
    <div class="panel-body preview-container">
      <div class="preview">
        
        <div class="invoice-block">
          <div class="invoice-type">Factura</div>
          <div class="letter">B</div>
        </div>
        <div class="invoice-block">
          <div class="col-md-6 pull-in">
            <div>
              <span class="bold">Fecha de Emisi&oacute;n: </span>
              <span id="cargar_compras_fecha_factura"></span>
            </div>
            <div>
              <span class="bold">Condici&oacute;n de Venta: </span>
              <span id="cargar_compras_forma_pago_factura">Cuenta Corriente</span>
            </div>
          </div>
          <div class="col-md-6 pull-in">
            <div>
              <div>
                <span id="cargar_compras_proveedor_nombre_factura" class="bold"></span>
              </div>
              <div>
                <span id="cargar_compras_proveedor_direccion_factura"></span>
              </div>
              <div>
                <span id="cargar_compras_proveedor_tipo_contribuyente_factura"></span>
              </div>
              <div>
                <span id="cargar_compras_proveedor_cuit_factura"></span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="line line-dashed b-b line-lg"></div>
        
        <input type="hidden" id="cargar_compras_id_articulo"/>
          <div class="clearfix">
            
            <input type="hidden" id="cargar_compras_concepto_id"/>
            
            <!-- ID OCULTO PARA SABER SI SE ESTA EDITANDO O CREANDO UNO NUEVO -->
            <input type="hidden" id="cargar_compras_netos_id" value="-1"/>
            
            <div class="col-md-3 col-sm-6 p0">
              <label class="text-muted">Concepto</label>
              <div class="input-group">
                <input type="text" class="form-control key" id="cargar_compras_concepto_codigo"/>
                <span class="input-group-btn">
                  <button class="btn btn-default key" id="cargar_compras_buscar_conceptos" type="button"><i class="fa fa-search"></i></button>
                </span>
                <input type="hidden" class="form-control" disabled id="cargar_compras_concepto_nombre" />
              </div>
            </div>
            
            <div class="col-md-2 col-sm-6 p0">
              <label class="text-muted">Neto</label>
              <input type="number" placeholder="Neto" class="form-control numerico key enterToNext" id="cargar_compras_neto"/>
            </div>
              
            <div class="col-md-3 col-sm-6 p0">
              <label class="text-muted">% Descuento</label>
              <div class="input-group">
                <span class="input-group-btn">
                  <input type="number" value="0" class="form-control w75 numerico key enterToNext" id="cargar_compras_porcentaje_descuento"/>
                </span>
                <input type="text" disabled class="form-control numerico key enterToNext" id="cargar_compras_descuento"/>
              </div>
            </div>
            
            <div class="col-md-2 col-sm-6 p0">
              <label class="text-muted">% IVA</label>
              <select class="form-control key enterToNext" id="cargar_compras_porc_iva">
                <option value="0" data-porcentaje="0">0.00</option>
                <?php foreach($alicuotas_iva as $ali) { ?>
                  <option value="<?php echo $ali->id; ?>" data-porcentaje="<?php echo $ali->porcentaje ?>"><?php echo $ali->nombre; ?></option>
                <?php } ?>
              </select>
              <input type="text" disabled class="dn form-control numerico key" id="cargar_compras_iva"/>
            </div>
            
            <div class="col-md-2 col-sm-6 p0">
              <label class="text-muted">Importe</label>
              <div class="input-group">
                <input type="text" disabled class="form-control" id="cargar_compras_item_subtotal" placeholder="Subtotal"/>
                <span class="input-group-btn">
                  <button title="Ingresar linea" id="cargar_compras_agregar_iva" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                </span>
              </div>
            </div>
          </div>
          
          <div class="oh">
            <div class="b-a" style="overflow: auto; height: 200px; margin-top: 15px;">
              <table id="cargar_compras_netos_table" class="table sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th>Concepto</th>
                    <th class="tar">Neto</th>
                    <th class="tar w75">% Iva</th>
                    <th class="tar">Iva</th>
                    <th class="tar">Subtotal</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody>
                  <%
                  var total_neto = 0;
                  var total_iva = 0;
                  for (var i=0; i < netos.length; i++) {
                    var neto = netos[i];%>
                    <tr id='fila<%=i%>'>
                    <td><%=neto.nombre_concepto%></td>
                    <td class='tar'><%=neto.neto_dto%></td>
                    <td class='tar'><%=neto.porc_iva%></td>
                    <td class='tar'><%=neto.iva%></td>
                    <td class='tar'><%= Number(parseFloat(neto.neto_dto) + parseFloat(neto.iva)).toFixed(2) %></td>
                    <td><i class='fa fa-file-text-o editar_fila_neto text-dark' /></td>
                    <td><i class='glyphicon glyphicon-remove eliminar_fila_neto text-danger' /></td>
                    <% total_neto = parseFloat(total_neto) + parseFloat(neto.neto_dto); %>
                    <% total_iva = parseFloat(total_iva) + parseFloat(neto.iva); %>                  
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>
          </div>
        
        <div class="line line-dashed b-b line-lg"></div>
        
        <div class="oh m-t">
          <div class="col-md-6">
            <div class="form-horizontal pull-in">
              
              <div class="b-a iva_container" style="overflow: auto; margin-right: 30px;">
                <table id="tabla_impuestos" class="table table-small sortable m-b-none default footable">
                  <thead class="bg-light lter">
                    <tr>
                      <th>Tributo</th>
                      <th class="w120">Monto</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Percepcion IIBB Arba</td>
                      <td><input value="<%= perc_ing_brutos %>" type="number" class="enterToNext form-control numerico key" id="cargar_compras_perc_ing_brutos" name="perc_ing_brutos"/></td>
                    </tr>
                    <tr>
                      <td>Percepcion IVA</td>
                      <td><input value="<%= perc_iva %>" type="number" class="enterToNext form-control numerico key" id="cargar_compras_perc_iva" name="perc_iva"/></td>
                    </tr>
                    <tr>
                      <td>Percepcion IIBB AGIP</td>
                      <td><input value="<%= perc_agip %>" type="number" class="enterToNext form-control numerico key" id="cargar_compras_perc_agip" name="perc_agip"/></td>
                    </tr>
                    <tr style="<%= (ID_EMPRESA == 399)?"":"dn" %>">
                      <td>Percepcion IIBB San Luis</td>
                      <td><input value="<%= perc_san_luis %>" type="number" class="enterToNext form-control numerico key" id="cargar_compras_perc_san_luis" name="perc_san_luis"/></td>
                    </tr>
                    <tr>
                      <td>Impuesto Interno</td>
                      <td><input value="<%= impuesto_interno %>" type="number" class="enterToNext form-control numerico key" id="cargar_compras_impuesto_interno" name="impuesto_interno"/></td>
                    </tr>
                    <tr>
                      <td>No Gravado</td>
                      <td><input value="<%= no_gravado %>" type="number" class="enterToNext form-control numerico key" id="cargar_compras_no_gravado" name="no_gravado"/></td>
                    </tr>
                    <tr>
                      <td>Exento</td>
                      <td><input value="<%= exento %>" type="number" class="enterToNext form-control numerico key" id="cargar_compras_exento" name="exento"/></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td>Subtotal</td>
                      <td><input value="<%= total_regimenes_especiales %>" type="text" disabled class="form-control key" id="cargar_compras_subtotal_regimenes" name="subtotal_regimenes"/></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-horizontal pull-in totales">
              
              <div class="form-group">
                <label class="control-label col-xs-8">Neto:</label>
                <div class="col-xs-4">
                  <input type="text" class="no-input" value="<%= total_neto %>" disabled id="cargar_compras_neto_total"/>
                </div>
              </div>
              
              <div class="form-group iva_container">
                <label class="control-label col-xs-6">IVA: </label>
                <div class="col-xs-6">
                  <input type="text" class="no-input" value="<%= total_iva %>" disabled id="cargar_compras_iva_total"/>
                </div>
              </div>

              <div class="form-group iva_container">
                <label class="control-label col-xs-6">Subtotal: </label>
                <div class="col-xs-6">
                  <input type="text" class="no-input" value="<%= subtotal %>" disabled id="cargar_compras_subtotal"/>
                </div>
              </div>
              
              <div class="line line-dashed b-b"></div>
              <div class="form-group">
                <label class="control-label col-xs-6 fs26">Total:</label>
                <div class="col-xs-6">
                  <input type="text" disabled value="<%= total_general %>" class="no-input fs26 bold" id="cargar_compras_total_general"/>
                </div>
              </div>
              
            </div>
          </div>
        </div>
        
        <div class="line line-dashed b-b line-lg"></div>
        
        <div class="oh m-t">
          <h4>Notas y Observaciones</h4>
          <div>
            <textarea style="height: 100px" id="cargar_compras_observaciones" value="<%= observaciones %>" name="observaciones" placeholder="Puede escribir una nota u observacion del comprobante..." class="form-control"></textarea>
          </div>

          <div class="form-group">
            <label class="i-checks m-b-none m-t">
              <input value="<%= compra_real %>" name="compra_real" <%= (compra_real == 1)?"checked":"" %> type="checkbox"><i></i>
              Incluir el comprobante en el resumen de compras.
            </label>
          </div>
          <div class="form-group">
            <label class="i-checks">
              <input value="<%= ver_en_cuenta %>" name="ver_en_cuenta" <%= (ver_en_cuenta == 1)?"checked":"" %> type="checkbox"><i></i>
              Incluir el comprobante en la cuenta corriente.
            </label>
          </div>

        </div>
        
        <div class="line line-dashed b-b line-lg"></div>
        
      </div>
    </div>
  </div>
  
  <div class="oh m-t m-b tar pull-in">
    <button class="btn btn-default nuevo btn-addon m-r"><i class="icon glyphicon glyphicon-remove"></i>Cancelar</button>
    <button class="btn btn-success guardar btn-addon"><i class="icon fa fa-plus"></i>Guardar</button>
  </div>
  
  </div>
</div>

</script>