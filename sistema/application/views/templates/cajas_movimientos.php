<script type="text/template" id="listado_cajas_movimientos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <div class="row clearfix padder">
      <% if (ver_saldos == 0) { %>
        <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-shopping-cart icono_principal"></i><%= (tipo==0)?"Ingresos":"Compras / <b>Egresos</b>" %></h1>
      <% } else { %>
        <% var modulo = control.get("cajas") %>
        <h1 class="m-n font-thin h3 pull-left"><a style="color:inherit" href="app/#cajas"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.nombre_es %></a>
          / <b><%= titulo %></b>
        </h1>
      <% } %>
    </div>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <% if (tipo == 1) { %>
        <?php $active = "gastos"; include("compras/compras_menu.php"); ?>
      <% } else { %>
        <?php $active = "cajas"; include("caja/caja_menu.php"); ?>
      <% } %>

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-5 sm-m-b">
            <div class="form-inline">    
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Desde" id="cajas_movimientos_desde" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Hasta" id="cajas_movimientos_hasta" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <?php /*
              <div class="form-group">
                <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
              </div>
              */ ?>
              <div class="form-group">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-filter mr5"></i> Filtros</button>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-7 sm-m-b">

            <div class="btn-group pull-right dropdown ml5">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="fa fa-cog"></i><span>Opciones</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void(0)" class="transferencia">Transferencia</a></li>
                <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
                <% if (VOLVER_SUPERADMIN == 1) { %>
                  <li><a href="javascript:void(0)" class="nuevo_ajuste">Ajuste de Caja</a></li>
                <% } %>
              </ul>
            </div>

            <% if (ver_saldos == 0) { %>
              <a class="btn pull-right btn-info btn-addon ml5 nuevo_caja_movimiento" href="javascript:void(0)">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</span>
              </a>
            <% } else { %>
              <div class="btn-group pull-right dropdown ml5">
                <button class="btn btn-danger dropdown-toggle btn-addon" data-toggle="dropdown">
                  <i class="fa fa-arrow-down"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Egreso","en"=>"Exit")); ?>&nbsp;&nbsp;</span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" class="nuevo_gasto">Efectivo</a></li>
                  <% if (ID_EMPRESA != 249) { %>
                    <li><a href="javascript:void(0)" class="nuevo_egreso_cheque">Cheque</a></li>
                  <% } %>
                </ul>
              </div>
              <div class="btn-group pull-right dropdown ml5">
                <button class="btn btn-success dropdown-toggle btn-addon" data-toggle="dropdown">
                  <i class="fa fa-arrow-up"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Ingreso","en"=>"Income")); ?>&nbsp;&nbsp;</span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" class="nuevo_ingreso">Efectivo</a></li>
                  <% if (ID_EMPRESA != 249) { %>
                    <li><a href="javascript:void(0)" class="nuevo_ingreso_cheque">Cheque</a></li>
                  <% } %>
                </ul>
              </div>
            <% } %>

          </div>
        </div>
      </div>
      <% var display_search = (id_concepto != 0) ? "display:block":"display:none" %>
      <div class="advanced-search-div bg-light dk" style="<%= display_search %>">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">    
            <div class="form-group">
              <select class="form-control no-model" id="cajas_movimientos_conceptos">
                <option value="0">Concepto</option>
                <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
              </select>
            </div>            
            <div class="form-group">
              <select class="form-control no-model" id="cajas_movimientos_estado">
                <option <%= (estado==-1)?"selected":"" %> value="-1">Estado</option>
                <option <%= (estado==0)?"selected":"" %> value="0">Realizado</option>
                <option <%= (estado==1)?"selected":"" %> value="1">Pendiente</option>
              </select>
            </div>            
            <div class="form-group">
              <select class="form-control no-model" id="cajas_movimientos_orden_pago">
                <option <%= (orden_pago==-1)?"selected":"" %> value="-1">Orden de Pago</option>
                <option <%= (orden_pago==1)?"selected":"" %> value="1">Pertenece a OP</option>
                <option <%= (orden_pago==0)?"selected":"" %> value="0">No pertenece a OP</option>
              </select>
            </div>                        
          </div>
        </div>
      </div>

      <div class="bulk_action panel-body resumen pb0">
        <div class="row">
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
              <div id="cajas_movimientos_monto" class="h3 font-thin text-white block">0</div>
              <span class="text-muted text-md pt5 db">Total</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
              <span id="cajas_movimientos_cantidad" class="font-thin h3 block">0</span>
              <span class="text-muted text-md pt5 db">Operaciones</span>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cajas_movimientos_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="w150">Estado</th>
                <th class="w180 exportable">Fecha</th>
                <th class="exportable">Concepto</th>
                <th class="exportable">Descripci&oacute;n</th>
                <th class="exportable tar w150">Monto</th>
                <th class="exportable tar w150">Saldo</th>
                <th style="width:20px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="editar_caja_movimiento_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <%= (id == undefined)?"Cargar":"Editar" %> 
    <%= (tipo==0)?"Ingreso":"" %>
    <%= (tipo==1)?"Egreso":"" %>
    <%= (tipo==2)?"Ajuste":"" %>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha</label>
          <div class="input-group">
            <input <%= (editar==0)?"disabled":"" %> type="text" value="<%= fecha %>" name="fecha" class="form-control esc" id="cajas_movimientos_fecha"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Monto</label>
          <input <%= (editar==0)?"disabled":"" %> type="text" value="<%= monto %>" name="monto" class="form-control esc" id="cajas_movimientos_monto"/>
        </div>
      </div>      
    </div>
    <div class="form-group">
      <label class="control-label">Concepto</label>
      <% if (ID_EMPRESA == 249) { %>
        <select <%= (editar==0)?"disabled":"" %> class="form-control no-model esc w100p" id="cajas_movimientos_tipo">
          <option value="0">Seleccione</option>
          <% if (tipo == 1) { %>
            <% for(var i=0;i< tipos_gastos.length;i++) { %>
              <% var t = tipos_gastos[i] %>
              <% if (t.id == 168) { %>
                <%= workspace.crear_select(t.children,"",id_concepto) %>
              <% } %>  
            <% } %>
          <% } else if (tipo == 0) { %>
            <% for(var i=0;i< tipos_gastos.length;i++) { %>
              <% var t = tipos_gastos[i] %>
              <% if (t.id == 1228) { %>
                <%= workspace.crear_select(t.children,"",id_concepto) %>
              <% } %>  
            <% } %>
          <% } %>          
        </select>
      <% } else { %>
        <div class="input-group">
          <select class="form-control no-model esc" id="cajas_movimientos_tipo">
            <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
          </select>
          <span class="input-group-btn">
            <button tabindex="-1" class="btn btn-info w100 agregar_concepto">
              <?php echo lang(array(
                "es"=>"+ Agregar",
                "en"=>"+ Add",
              )); ?>
            </button>  
          </span>
        </div>
      <% } %>
    </div>
    <div class="form-group">
      <label class="control-label">Descripci&oacute;n</label>
      <textarea <%= (editar==0)?"disabled":"" %> name="observaciones" class="h80 form-control"><%= observaciones %></textarea>
    </div>

    <?php
    single_file_upload(array(
      "name"=>"path",
      "label"=>lang(array("es"=>"Archivo adjunto","en"=>"Atacchment file")),
      "url"=>"/sistema/cajas_movimientos/function/save_file/",
    )); ?>

    <% if (id == undefined) { %>
      <div class="form-group">
        <div class="checkbox">
          <label class="i-checks">
            <input id="cajas_movimientos_estado" type="checkbox" name="estado" value="1"><i></i> Marcar como pendiente de pago.
          </label>
        </div>
      </div>
    <% } %>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn btn-default fl cerrar">Cerrar</button>
    <button class="btn btn-success fr guardar">Guardar</button>
  </div>
</div>
</script>


<script type="text/template" id="listado_cajas_movimientos_item">
  <td>
    <% if (tipo != 2) { %>
      <label class="i-checks m-b-none">
        <input class="esc check-row2" value="<%= id %>" data-total="<%= (tipo==1)?"-":"" %><%= monto %>" type="checkbox"><i></i>
      </label>
    <% } %>
  </td>
  <td>
    <% if (tipo != 2) { %>
      <div class="btn-group dropdown">
        <% if (estado == 0) { %>
          <button class="btn btn-sm btn-success btn-addon btn-addon2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang(array("es"=>"Realizado","en"=>"Done")); ?></button>
        <% } else if (estado == 1) { %>
          <button class="btn btn-sm btn-warning btn-addon btn-addon2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang(array("es"=>"Pendiente","en"=>"Pending")); ?></button>
        <% } %>
        <span class="fs12 m-l-xs"><i class="fa fa-caret-down"></i></span>
        <ul class="dropdown-menu">
          <li><a href="javascript:void(0)" class="editar_estado" data-estado="0"><?php echo lang(array("es"=>"Realizado","en"=>"Done")); ?></a></li>
          <li><a href="javascript:void(0)" class="editar_estado" data-estado="1"><?php echo lang(array("es"=>"Pendiente","en"=>"Pending")); ?></a></li>
        </ul>
      </div>  
    <% } %>
  </td>
  <td class='ver exportable'><%= fecha %></td>
  <td class='ver exportable'><span class="text-info"><%= concepto %></span></td>
  <td class='exportable'><span class="ver"><%= (tipo == 2) ? "AJUSTE CAJA" : observaciones %></span>
    <% if (!isEmpty(path)) { %>
      <a class="fr text-info fs16" href="/sistema/<%= path %>" target="_blank"><i class="fa fa-file-o"></i></a>
    <% } %>
  </td>
  <td class="ver exportable tar number">$ <%= (ver_saldos==1 && tipo==1)?"-":"" %><%= Number(monto).format(2) %></td>
  <td class="ver exportable tar number">$ <%= Number(subtotal).format(2) %></td>
  <td class="p5 td_acciones">
    <% if (tipo != 2) { %>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <% if (id_orden_pago != 0) { %>
            <li><a href="javascript:void(0)" class="orden_pago">Ver Orden de Pago</a></li>
          <% } %>
          <% if (!(id_concepto == 1489 && ID_EMPRESA == 249)) { %>
            <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
          <% } %>
        </ul>
      </div> 
    <% } %>   
  </td>
</script>


<script type="text/template" id="caja_transferencia_template">
<div class="panel panel-default">
  <div class="panel-heading">Transferencia entre cajas</div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha</label>
          <div class="input-group">
            <input type="text" class="form-control no-model esc" id="caja_transferencia_fecha"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Monto</label>
          <input type="text" class="form-control esc no-model" id="caja_transferencia_monto"/>
        </div>
      </div>      
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Desde</label>
          <select id="caja_transferencia_caja_desde" class="form-control no-model">
            <% for (var i=0;i< window.cajas.length; i++) { %>
              <% var o = window.cajas[i] %>
              <% if (o.activo == 1) { %>
                <option value="<%= o.id %>"><%= o.nombre %></option>
              <% } %>
            <% } %>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Hacia</label>
          <select id="caja_transferencia_caja_hasta" class="form-control no-model">
            <% for (var i=0;i< window.cajas.length; i++) { %>
              <% var o = window.cajas[i] %>
              <% if (o.activo == 1) { %>
                <option value="<%= o.id %>"><%= o.nombre %></option>
              <% } %>
            <% } %>
          </select>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label">Observaciones</label>
      <textarea id="caja_transferencia_observaciones" name="observaciones" class="form-control esc no-model"></textarea>
    </div>    
  </div>
  <div class="panel-footer clearfix">
    <button class="btn btn-default fl cerrar">Cerrar</button>
    <button class="btn btn-success fr guardar">Guardar</button>
  </div>
</div>
</script>