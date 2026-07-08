<script type="text/template" id="maquinas_resultados_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">Todav&iacute;a no ten&eacute;s ninguna m&aacute;quina</h1>
  <h3 class="h3">Para a&ntilde;adir tu primera m&aacute;quina, hace click en el siguiente bot&oacute;n</h3>
  <div class="list-icon">
    <a href="app/#maquina"><i class="icon-note"></i></a>
  </div>
  <div>
    <a class="btn btn-lg btn-info btn-addon" href="app/#maquina">
      <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
    </a>
  </div>
  <p>
    Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
  </p>
</div>
<div class="seccion_llena" style="display:none">
  <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="<% if (!seleccionar) { %>col-md-6 col-lg-3 <% } else { %> col-xs-12 <% } %> sm-m-b">
            <div class="input-group">
                <input value="<%= window.maquinas_filter %>" type="text" id="maquinas_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
                </span>
                <span class="input-group-btn">
                  <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
                </span>
            </div>
          </div>
          <% if (!seleccionar) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#maquina">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nueva&nbsp;&nbsp;</span>
              </a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="display:none">
        <div class="wrapper oh">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">
            <div style="width: 200px; display: inline-block">
              <% if (typeof window.sectores != "undefined") { %>
                <select style="width: 100%" id="maquinas_buscar_sectores">
                  <option value="0">Sector</option>
                  <% for(var i=0;i< window.sectores.length;i++) { %>
                    <% var o = sectores[i]; %>
                    <option <%= (window.maquinas_sector == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                </select>
              <% } %>
            </div>
            <div class="form-group">
              <button id="maquinas_buscar_avanzada_btn" class="btn buscar btn-default"><i class="fa fa-search"></i> Buscar</button>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="maquinas_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th>Nombre</th>
                <th>Codigo</th>
                <th>Sector</th>
                <% if (!seleccionar) { %>
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
</div>
</script>

<script type="text/template" id="maquinas_item_resultados_template">
<% var clase = (activo==1)?"":"text-muted"; %>
<% if (!seleccionar) { %>
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
<% } %>
<td class="<%= clase %> data"><span class="text-info"><%= nombre %></span></td>
<td class="<%= clase %> data"><%= codigo %></td>
<td class="<%= clase %> data"><%= sector %></td>
<% if (!seleccionar) { %>
  <td class="tar td_acciones">
    <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
    <div class="btn-group dropdown ml10">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
        <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>
  </td>
<% } %>
</script>


<script type="text/template" id="maquina_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("maquinas") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i> 
    <%= modulo.title %> 
    / <b><%= (id == undefined) ? "Nueva" : nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-4">
        <div class="detalle_texto">
          <?php 
          $clave = "Maquinas / Detalle / Texto 1";
          echo lang(array(
            "es"=>(isset($videos[$clave]["nombre_es"]) ? $videos[$clave]["nombre_es"] : "" ),
            "en"=>(isset($videos[$clave]["nombre_en"]) ? $videos[$clave]["nombre_en"] : "" ),
          )); ?>
        </div>
        <div class="detalle_texto_info text-muted">
          <?php echo lang(array(
            "es"=>(isset($videos[$clave]["texto_es"]) ? $videos[$clave]["texto_es"] : "" ),
            "en"=>(isset($videos[$clave]["texto_en"]) ? $videos[$clave]["texto_en"] : "" ),
          )); ?>
        </div>
        <?php if (isset($videos[$clave]["video_es"]) && !empty($videos[$clave]["video_es"])) { ?>
          <a onclick="workspace.open_video(this)" data-iframe='<?php echo $videos[$clave]["video_es"] ?>'>
            Ver video
          </a>
        <?php } ?>
      </div>

      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" required name="nombre" id="maquina_nombre" value="<%= nombre %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">C&oacute;digo</label>
                    <input type="text" name="codigo" id="maquina_codigo" value="<%= codigo %>" class="form-control"/>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Sector</label>
                    <select id="maquina_sectores" class="w100p">
                      <% for(var i=0;i< window.sectores.length;i++) { %>
                        <% var o = window.sectores[i]; %>
                        <option value="<%= o.id %>" <%= (o.id == id_sector)?"selected":"" %>><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Modelo</label>
                    <input type="text" name="modelo" id="maquina_modelo" value="<%= modelo %>" class="form-control"/>
                  </div>
                </div>
              </div>
              <div class="form-group mb0 tar">
                <a class="expand-link">
                  <?php echo lang(array(
                    "es"=>"+ M&aacute;s opciones",
                    "en"=>"+ More options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha adquisici&oacute;n</label>
                    <div class="input-group">
                      <input type="text" name="fecha_adquisicion" id="entrada_fecha_adquisicion" value="<%= fecha_adquisicion %>" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Garantia</label>
                    <input type="text" name="garantia" id="maquina_garantia" value="<%= garantia %>" class="form-control"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" name="maquina_observaciones" id="maquina_observaciones"><%= observaciones %></textarea>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Partes de la m&aacute;quina</label>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Identifique los componentes que forman la maquinaria, para administrarlos de manera independiente en las ordenes de trabajo.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="display:block">
            <div class="padder">
              <div class="clearfix tar">
                <button class="btn btn-info nuevo_parte">+ Agregar</button>
              </div>
              <div id="maquina_partes" class="mt10"></div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="line b-b m-b-lg"></div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="maquinas_partes_resultados_template">
  <table id="partes_tabla" class="table table-small table-striped sortable m-b-none default footable">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>C&oacute;digo</th>
        <th class="th_acciones w50"></th>
      </tr>
    </thead>
    <tbody class="tbody"></tbody>
  </table>
</script>

<script type="text/template" id="maquinas_partes_item_resultados_template">
  <td class="text-info data"><%= nombre %></td>
  <td class="data"><%= codigo %></td>
  <td class="tar td_acciones">
    <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
  </td>
</script>

<script type="text/template" id="maquina_parte_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Editar parte</b>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Nombre</label>
          <input type="text" required name="nombre" id="parte_nombre" value="<%= nombre %>" class="form-control"/>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">C&oacute;digo</label>
          <input type="text" name="codigo" id="parte_codigo" value="<%= codigo %>" class="form-control"/>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" name="parte_observaciones" id="parte_observaciones"><%= observaciones %></textarea>
    </div>
    <div class="form-group">
      <label class="i-checks">
        <input type="checkbox" id="parte_activo" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> >
        <i></i>
        La parte se encuentra activa.
      </label>
    </div>
  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>