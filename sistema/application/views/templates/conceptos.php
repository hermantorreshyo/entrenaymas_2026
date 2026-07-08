<script type="text/template" id="conceptos_tree_panel_template">
  <% if (!lightbox) { %>
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="fa fa-shopping-cart icono_principal"></i>Compras</h1>
    </div>
    <div class="wrapper-md pb0">
      <div class="panel panel-default">

        <?php $active = "conceptos"; include("compras/compras_menu.php"); ?>

        <ul class="nav nav-tabs nav-tabs-2" role="tablist">
          <li class='<%= (totaliza_en == "G")?"active":"" %>'>
            <a href="app/#conceptos/G/">
              <i class="fa text-danger fa-upload m-r-xs"></i>
              Gastos
            </a>
          </li>
          <li class='<%= (totaliza_en == "C")?"active":"" %>'>
            <a href="app/#conceptos/C/">
              <i class="fa text-warning fa-upload m-r-xs"></i>
              Compras
            </a>
          </li>
          <li class='<%= (totaliza_en == "V")?"active":"" %>'>
            <a href="app/#conceptos/V/">
              <i class="fa text-success fa-download m-r-xs"></i>
              Ingresos
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="tab_concepto_1" class="tab-pane panel-body active">
            <div class="clearfix m-b">
              <a class="btn btn-info btn-sm btn-addon nuevo" href="javascript:void(0)">
                <i class="fa fa-plus"></i>
                Agregar concepto
              </a>
            </div>
            <div class="oh">
              <div ui-jq="nestable" class="dd">
                <% if (totaliza_en == "G") { %>
                  <%= workspace.crear_nestable(conceptos_gastos) %>
                <% } else if (totaliza_en == "C") { %>
                  <%= workspace.crear_nestable(conceptos_compras) %>
                <% } else if (totaliza_en == "V") { %>
                  <%= workspace.crear_nestable(conceptos_ventas) %>
                <% } %>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <% } else { %>
    <div class="panel panel-default">
      <div class="panel-heading oh font-bold">
        <span>Seleccionar conceptos</span>
        <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
      </div>
      <div class="panel-body oh">
        <div style="overflow: auto; height: 400px">
          <div ui-jq="nestable" class="dd">
            <%= workspace.crear_nestable(tipos_gastos,{
              "seleccionar":true
            }) %>
          </div>
        </div>
      </div>
    </div>
  <% } %>
</script>


<script type="text/template" id="conceptos_edit_panel_template">
  <div class="panel panel-default">
    <div class="panel-heading oh font-bold">
      <span><%= (id == undefined)?"Nuevo":nombre %></span>
      <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
    </div>
    <div class="panel-body oh">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">C&oacute;digo</label>
            <input type="text" name="codigo" class="form-control" id="conceptos_codigo" value="<%= codigo %>"/>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <label class="control-label">Nombre </label>
            <input type="text" name="nombre" class="form-control" id="conceptos_nombre" value="<%= nombre %>"/>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label class="control-label">Pertenece a </label>
            <select id="conceptos_padre" class="form-control">
              <option value="0">-</option>
              <% if (totaliza_en == "G") { %>
                <%= workspace.crear_select(conceptos_gastos,"",id_padre) %>
              <% } else if (totaliza_en == "C") { %>
                <%= workspace.crear_select(conceptos_compras,"",id_padre) %>
              <% } else if (totaliza_en == "V") { %>
                <%= workspace.crear_select(conceptos_ventas,"",id_padre) %>
              <% } %>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Al&iacute;cuota IVA </label>
            <select id="conceptos_iva" name="id_tipo_alicuota_iva" class="form-control">
              <option value="3">-</option>
              <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                <% var o = alicuotas_iva[i]; %>
                <option <%= (id_tipo_alicuota_iva == o.id)?"selected":"" %> value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
              <% } %>
            </select>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label">Descripci&oacute;n</label>
        <textarea id="conceptos_descripcion" name="descripcion" class="form-control"><%= descripcion %></textarea>
      </div>
    </div>
    <div class="panel-footer clearfix text-right">
      <button class="btn btn-success guardar">Guardar</button>
      <% if (id != undefined) { %>
        <% if (!(ID_EMPRESA == 249 && VOLVER_SUPERADMIN == 0)) { %>
          <button class="btn btn-danger eliminar fl">Eliminar</button>
        <% } %>
      <% } %>
    </div>
  </div>
</script>

<script type="text/template" id="concepto_edit_mini_panel_template">
  <div class="panel pb0 mb0">
    <div class="panel-body">
      <div class="form-group">
        <input type="text" name="nombre" placeholder="Nombre" class="form-control tab" id="concepto_mini_nombre" value="<%= nombre %>"/>
      </div>
      <div class="form-group">
        <select id="concepto_mini_padre" class="form-control tab">
          <option value="0">Pertenece a</option>
          <% if (totaliza_en == "G") { %>
            <%= workspace.crear_select(conceptos_gastos,"",id_padre) %>
          <% } else if (totaliza_en == "C") { %>
            <%= workspace.crear_select(conceptos_compras,"",id_padre) %>
          <% } else if (totaliza_en == "V") { %>
            <%= workspace.crear_select(conceptos_ventas,"",id_padre) %>
          <% } %>
        </select>
      </div>
      <div class="form-group">
        <button class="btn guardar btn-success tab btn-block">Guardar</button>
      </div>
    </div>
  </div>
</script>