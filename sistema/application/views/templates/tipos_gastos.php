<script type="text/template" id="gastos_tree_panel_template">
  <style type="text/css">.seleccionar_conceptos .dd3-content { cursor: pointer; } </style>
  <% if (!lightbox) { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Conceptos de Compras</h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="centrado">
      <div class="panel panel-default">
        <div class="panel-heading oh">
          <span>Organizar conceptos</span>
          <a class="btn btn-info btn-sm pull-right btn-addon nuevo" href="javascript:void(0)">
            <i class="fa fa-plus"></i>
            Nuevo
          </a>
        </div>
        <div class="panel-body oh">
          <div ui-jq="nestable" class="dd">
            <%= workspace.crear_nestable(tipos_gastos) %>
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
    <div class="panel-body oh seleccionar_conceptos">
      <div class="form-group">
        <a target="_blank" href="app/#conceptos" class="btn btn-info">Gestionar conceptos</a>
      </div>
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


<script type="text/template" id="tipos_gastos_edit_panel_template">
  <div class="panel panel-default">
    <div class="panel-heading oh font-bold">
      <span><%= (id == undefined)?"Nuevo":nombre %> <%= (VOLVER_SUPERADMIN == 1 && id != undefined)?id:"" %></span>
      <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
    </div>
    <div class="panel-body oh">
      <div class="form-horizontal">
        <div class="form-group">
          <div class="col-md-6">
            <label class="control-label">Nombre </label>
            <input type="text" name="nombre" class="form-control" id="tipos_gastos_nombre" value="<%= nombre %>"/>
          </div>
          <div class="col-md-6">
            <label class="control-label">Pertenece a </label>
            <select id="tipos_gastos_padre" class="form-control">
              <option value="0">-</option>
              <%= workspace.crear_select(tipos_gastos,"",id_padre) %>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-4">
            <label class="control-label">C&oacute;digo</label>
            <input type="text" name="codigo" class="form-control" id="tipos_gastos_codigo" value="<%= codigo %>"/>
          </div>
          <div class="col-md-4">
            <label class="control-label">Al&iacute;cuota IVA </label>
            <select id="tipos_gastos_iva" name="id_tipo_alicuota_iva" class="form-control">
              <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
              <% var o = alicuotas_iva[i]; %>
              <option <%= (id_tipo_alicuota_iva == o.id)?"selected":"" %> value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
              <% } %>
            </select>
          </div>
          <div class="col-md-4">
            <label class="control-label">Totaliza en</label>
            <select id="tipos_gastos_totaliza_en" class="form-control" name="totaliza_en">
              <% if (ID_EMPRESA == 228) { %>
                <option <%= (totaliza_en=="E")?"selected":"" %> value="E">Entradas</option>
                <option <%= (totaliza_en=="S")?"selected":"" %> value="S">Salidas</option>
              <% } else { %>
                <option value="">-</option>
                <option <%= (totaliza_en=="C")?"selected":"" %> value="C">Compras</option>
                <option <%= (totaliza_en=="G")?"selected":"" %> value="G">Gastos</option>
              <% } %>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <label class="control-label">Descripci&oacute;n</label>
            <textarea id="tipos_gastos_descripcion" name="descripcion" class="form-control"><%= descripcion %></textarea>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix text-right">
      <button class="btn btn-success guardar">Guardar</button>
      <% if (id != undefined) { %>
      <button class="btn btn-danger eliminar fl">Eliminar</button>
      <% } %>
    </div>
  </div>
</script>

<script type="text/template" id="tipo_gasto_edit_mini_panel_template">
  <div class="panel pb0 mb0">
    <div class="panel-body">
      <div class="form-group">
        <input type="text" name="nombre" placeholder="Nombre" class="form-control tab" id="tipo_gasto_mini_nombre" value="<%= nombre %>"/>
      </div>
      <div class="form-group">
        <select id="tipo_gasto_mini_padre" class="form-control tab">
          <option value="0">Pertenece a</option>
          <%= workspace.crear_select(tipos_gastos,"",id_padre) %>
        </select>
      </div>
      <div class="form-group">
        <input type="text" name="codigo" placeholder="Codigo" class="form-control tab" id="tipo_gasto_mini_codigo" value="<%= codigo %>"/>
      </div>
      <div class="form-group">
        <select id="tipos_gastos_mini_totaliza_en" class="form-control" name="totaliza_en">
          <option value="">Totaliza en</option>
          <option value="C">Compras</option>
          <option value="G">Gastos</option>
        </select>
      </div>
      <div class="form-group">
        <select id="tipo_gasto_mini_iva" name="id_tipo_alicuota_iva" class="form-control tab">
          <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
            <% var o = alicuotas_iva[i]; %>
            <option <%= (id_tipo_alicuota_iva == o.id)?"selected":"" %> value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
          <% } %>
        </select>
      </div>
      <div class="row pl15 pr15">
        <div class="col-xs-8 pl0 pr0">
          <div class="form-group">
            <button class="btn guardar btn-success tab btn-block">Guardar</button>
          </div>
        </div>
        <div class="col-xs-4 pl0 pr0">
          <div class="form-group">
            <button class="btn cerrar btn-default tab btn-block">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>