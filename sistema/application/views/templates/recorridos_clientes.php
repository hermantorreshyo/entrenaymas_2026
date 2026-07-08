<script type="text/template" id="recorridos_clientes_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("recorridos_clientes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    
    <div class="panel-heading oh">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="input-group">
            <input type="text" id="recorridos_clientes_buscar" value="<%= window.recorridos_clientes_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn pull-right btn-info btn-addon" href="app/#recorrido_cliente">
            <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
          </a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="recorridos_clientes_table" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th class="sorting" data-sort-by="nombre">Nombre</th>
              <th class="sorting tac" data-sort-by="cantidad_clientes">Cant. de Clientes</th>
              <% if (permiso > 1) { %>
                <th class="th_acciones w120">Acciones</th>
              <% } %>
            </tr>
          </thead>
          <tbody class="tbody"></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
  </div>    
</script>


<script type="text/template" id="recorridos_clientes_item">
  <td class='ver'><span class="text-info"><%= nombre %></span></td>
  <td class='ver tac'><%= cantidad_clientes %></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones tar">
      <div class="btn-group dropdown ml10 mr10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="imprimir">Imprimir</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>    
    </td>
  <% } %>
</script>

<script type="text/template" id="recorridos_clientes_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("recorridos_clientes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <b><%= (id == undefined)?"Nuevo":"Editar" %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">

    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Informaci&oacute;n general</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input id="recorrido_cliente_nombre" type="text" class="form-control" name="nombre" value="<%= nombre %>"/>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <b>Clientes</b>
          </div>
          <div class="panel-body">
            <div class="padder">
              <div class="clearfix">
                <input type="hidden" id="recorrido_cliente_cliente_id" value="0"/>
                <input type="hidden" id="recorrido_cliente_cliente_codigo" class="dn">
                <input type="hidden" id="recorrido_cliente_cliente_info" class="dn">
                <div class="form-group">
                  <label class="control-label">Buscar</label>
                  <div class="input-group">
                    <input type="text" id="recorrido_cliente_cliente_nombre" class="form-control">
                    <span class="input-group-btn">
                      <a id="recorrido_cliente_cliente_agregar" class="btn btn-info"><i class="fa fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
              <div class="b-a" style="overflow: auto; max-height: 400px">
                <div class="table-responsive mb0">
                  <table style="width: 100%" class="table m-b-none default footable">
                    <thead>
                      <tr>
                        <th style="width: 50%">Cliente</th>
                        <th style="width: 10%">Codigo</th>
                        <th style="width: 30%">Direccion</th>
                        <th style="width: 10%"></th>
                      </tr>
                    </thead>
                  </table>
                  <ol class="reorder" id="recorridos_clientes_clientes_tabla">
                    <% for(var i=0;i< clientes.length;i++) { %>
                      <% var p = clientes[i] %>
                      <li>
                        <table class="table m-b-none default" style="width: 100%">
                          <tr>
                            <input type="hidden" class="dn id" value="<%= p.id %>"/>
                            <td style="width: 50%">
                              <span class="btn fs14 btn-default m-r-xs">
                                <i class="fa fa-arrows"></i>
                              </span>
                              <span class="text-info"><%= p.nombre %></span>
                            </td>
                            <td style="width: 10%"><%= p.codigo %></td>
                            <td style="width: 30%"><%= p.direccion %></td>
                            <td style="width: 10%" class="tar">
                              <button class="btn btn-sm btn-white eliminar_cliente"><i class="fa fa-trash"></i></button>
                            </td>
                          </tr>
                        </table>
                      </li>
                    <% } %>
                  </ol>
                </div>
              </div>

            </div>
          </div>
        </div>
        
      </div>
    </div>
    <div class="line b-b m-b-lg"></div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8 tar">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>