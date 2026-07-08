<script type="text/template" id="cocinas_template">
<div class="bg-light lter b-b wrapper-md">
  <div class="row clearfix padder">
    <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-cutlery icono_principal mr10"></i>Cocina</h1>
  </div>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <li class="tab_link active">
          <a href="#tab1" role="tab" data-toggle="tab">
            En proceso
          </a>
        </li>
        <li class="tab_link">
          <a href="#tab2" role="tab" data-toggle="tab">
            Finalizados
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="tab1" class="tab-pane active">
          <h3 class="mt0">Pedidos en proceso</h3>
          <div class="line line-lg b-b"></div>
          <div class="mb100" id="cocinas_en_proceso"></div>
        </div>
        <div id="tab2" class="tab-pane">
          <h3 class="mt0">Pedidos finalizados</h3>
          <div class="line line-lg b-b"></div>
          <div class="mb100" id="cocinas_finalizados"></div>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="cocinas_item_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <div class="pull-left m-t-xs">
      <b class="fs18"><%= nombre %></b>
    </div>
    <div class="pull-right">
      <select id="cocina_item_usuario" class="form-control entregado_por no-model dib w200">
        <option value="0">Entregado por</option>
        <% for (var i=0; i< usuarios.length; i++) { %>
          <% var u = usuarios.models[i] %>
          <option <%= (u.id == id_usuario)?"selected":"" %> value="<%= u.id %>"><%= u.get("nombre") %></option>
        <% } %>
      </select>
    </div>
  </div>
  <div class="panel-body">
    <ul class="list-group mb20">
      <% for (var i=0; i< items.length; i++) { %>
        <% var item = items[i] %>
        <li class="list-group-item clearfix">
          <div class="w100p dt">
            <div class="dtc w100 tar">
              <span class="text-info bold fs20 m-r"><%= Number(item.cantidad).toFixed(2) %></span>
            </div>
            <div class="dtc">
              <span class="fs20"><%= item.nombre %></span> <span class="m-l text-muted"><i class="fa fa-clock-o"></i> <%= item.tipo_cantidad.substr(0,2)+":"+item.tipo_cantidad.substr(2,2) %></span>
              <% if (!isEmpty(item.descripcion)) { %>
                <br/><span class="fs14"><%= item.descripcion %></span>
              <% } %>
            </div>
            <div class="dtc tar">
              <button data-id="<%= item.id %>" class="terminar btn <%= (item.tipo == 1)?'btn-success':'btn-info' %>">
                <%= (item.tipo == 1)?"Hecho":"Terminar" %>
              </button>
            </div>
          </div>          
        </li>
        <% if (i+1 < items.length) { %>
          <% var o2 = items[i+1] %>
          <%= (o2.orden != item.orden) ? "<div class='mt20 mb20'>Orden Nro. "+(o2.orden)+":</div>" : "" %>
        <% } %>
      <% } %>
    </ul>
  </div>
</div>
</script>