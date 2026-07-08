<script type="text/template" id="gestion_pagos_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Gestion de Pagos</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="input-group pull-left" style="width: 140px;">
            <input type="text" id="gestion_pagos_fecha_desde" class="form-control">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
          <div class="input-group pull-left" style="width: 140px;">
            <input type="text" id="gestion_pagos_fecha_hasta" class="form-control">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>

          <?php 
          // Si somos VARCREATIVE
          if ($empresa->id == 936) { ?>

            <select id="gestion_pagos_proyectos" class="form-control no-model w150 fl">
              <option value="0">Proyecto</option>
              <?php $query_proyectos = $db->query("SELECT * FROM com_proyectos WHERE inactivo = 0 AND id != 0 ORDER BY id ASC "); 
              foreach($query_proyectos->result() as $proyecto_row) { ?>
                <option value="<?php echo $proyecto_row->id ?>"><?php echo $proyecto_row->nombre ?></option>
              <?php } ?>
            </select>

            <select id="gestion_pagos_estados" class="form-control no-model w150 fl">
              <option value="-1">Estado</option>
              <option value="1">A contactar</option>
              <option value="2">En progreso</option>
              <option value="3">Interesado</option>
              <option value="0">Demo</option>
              <option value="5">Preparando Cuenta</option>
              <option selected value="10">Cliente</option>
              <option value="20">Baja</option>
            </select>
            
            <select id="gestion_pagos_usuarios" class="form-control no-model w150 fl">
              <option value="0">Vendedor</option>
              <?php $query_usuarios_admin = $db->query("SELECT * FROM com_usuarios WHERE admin = 1 "); 
              foreach($query_usuarios_admin->result() as $usuario_admin_row) { ?>
                <option value="<?php echo $usuario_admin_row->id ?>"><?php echo $usuario_admin_row->nombre ?></option>
              <?php } ?>
            </select>

          <?php } ?>

          <div class="w100 fl">
            <button class="btn btn-default buscar"><i class="fa fa-search"></i> Buscar</button>
          </div>            

        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-body">
          <div id="gestion_pagos_tabla" class="b-a table-responsive">
          </div>
        </div>
      </div>
    </div>
  </div>  
</script>

<script type="text/template" id="gestion_pagos_tabla_template">
<table class="table table-small table-striped sortable m-b-none default footable">
  <thead>
    <tr>
      <th>#</th>
      <% if (ID_EMPRESA == 936) { %>
        <th>Estado</th>
        <th>Proyecto</th>
        <th>Vendedores</th>
      <% } %>
      <th>Cliente</th>
      <% for(var i=0;i< meses.length;i++) { %>
        <% var m = meses[i] %>
        <th><%= m %></th>  
      <% } %>
    </tr>
  </thead>
  <tbody>
    <% for(var i=0; i< resultado.length; i++) { %>
      <% var c = resultado[i] %>
      <tr>
        <td><%= c.id %></td>
        <% if (ID_EMPRESA == 936) { %>
          <td>
            <% if (c.estado_empresa == 1) { %><span class="label bg-danger">A contactar</span><% } %>
            <% if (c.estado_empresa == 0) { %><span class="label bg-light dk">Demo</span><% } %>
            <% if (c.estado_empresa == 5) { %><span class="label bg-warning">Preparando Cuenta</span><% } %>
            <% if (c.estado_empresa == 10) { %><span class="label bg-success">Cliente</span><% } %>
            <% if (c.estado_empresa == 20) { %><span class="label bg-danger">Baja</span><% } %>
          </td>
          <td><span class="label bg-light dk"><%= c.proyecto %></span></td>
          <td>
            <% for(var j=0; j< c.vendedores.length; j++) { %>
              <% var p = c.vendedores[j] %>
              <span class="label bg-light dk"><%= p.nombre %></span>
            <% } %>
          </td>
        <% } %>
        <td><a class="text-info" href="app/empresa/<%= c.id %>"><%= c.cliente %></a></td>
        <% for(var j=0; j< c.pagos.length; j++) { %>
          <% var p = c.pagos[j] %>
          <td><%= p.monto %></td>
        <% } %>
      </tr>
    <% } %>
  </tbody>
  <tfoot class="bg-important">
    <tr>
      <td><%= i %></td>
      <% if (ID_EMPRESA == 936) { %>
        <td></td><td></td><td></td>
      <% } %>      
      <td></td>
      <% for(var i=0; i< totales.length; i++) { %>
        <% var c = totales[i] %>
        <td><%= c %></td>
      <% } %>
    </tr>
  </tfoot>
</table>
</script>