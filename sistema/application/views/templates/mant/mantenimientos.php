<script type="text/template" id="mantenimientos_template">
<div class="hbox hbox-auto-xs hbox-auto-sm">
  <div class="col">
    <div class="bg-light lter b-b wrapper-md">
      <% var modulo = control.get("mantenimientos") %>
      <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
    </div>
    <div class="wrapper-md pb0">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-4 sm-m-b">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar..." />
                <span class="input-group-btn">
                  <button class="btn btn-default"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="mantenimiento_edit_panel_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7"><%= (id == undefined)?"Cargar mantenimiento":"Editar mantenimiento" %></span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body oh">
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a id="mantenimiento_paso_1_link" href="#mantenimiento_tab1" class="buscar_todos" role="tab" data-toggle="tab">
            <i class="fa text-warning fa-calendar m-r-xs"></i>
            Informaci&oacute;n
          </a>
        </li>
        <li>
          <a id="mantenimiento_paso_2_link" href="#mantenimiento_tab2" role="tab" data-toggle="tab">
            <i class="fa text-info fa-address-book m-r-xs"></i>
            &Oacute;rdenes de trabajo
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="mantenimiento_tab1" class="tab-pane active">
          <div class="">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Tipo</label>
                  <select class="form-control" id="mantenimiento_tipos_mantenimiento">
                    <% for(var i=0;i< window.tipos_mantenimiento.length;i++) { %>
                      <% var o = tipos_mantenimiento[i]; %>
                      <option <%= (id_tipo_mantenimiento == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">N&uacute;mero</label>
                  <input type="text" name="numero" class="form-control" id="mantenimiento_numero" value="<%= numero %>"/>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Fecha y hora</label>
                  <div class="input-group">
                    <div class="">
                      <div class="col-md-7 p0">
                        <input type="text" name="fecha" class="form-control" id="mantenimiento_fecha" value="<%= fecha %>"/>
                      </div>
                      <div class="col-md-5 p0">
                        <input type="text" name="hora" class="form-control" id="mantenimiento_hora" value="<%= hora %>"/>
                      </div>
                    </div>
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Tiempo estimado</label>
                  <div class="clearfix">
                    <div class="col-md-5 p0">
                      <input type="number" min="0" value="<%= duracion_aprox_cantidad %>" class="form-control no-model" id="mantenimiento_duracion_aprox_cantidad"/>
                    </div>
                    <div class="col-md-7 p0">
                      <select id="mantenimiento_duracion_aprox_tipo" name="duracion_aprox_tipo" class="form-control">
                        <option <%= (duracion_aprox_tipo=="M")?"selected":"" %> value="M">Minutos</option>
                        <option <%= (duracion_aprox_tipo=="H")?"selected":"" %> value="H">Horas</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label">Observaciones</label>
              <textarea placeholder="Escriba aqui alguna nota o recordatorio..." id="mantenimiento_observaciones" class="form-control h60 no-model"><%= observaciones %></textarea>
            </div>
            <?php /*

            <% if (id == undefined) { %>
              <div class="form-group">
                  <label class="control-label">Periodicidad</label>
                  <div class="col-md-9">
                    <div class="row">
                      <div class="col-xs-6">
                        <select class="form-control no-model" id="mantenimiento_repeticion">
                          <option value="0">No repetir</option>
                          <option value="1">Cada una semana</option>
                          <option value="2">Cada 2 semanas</option>
                          <option value="3">Cada 3 semanas</option>
                          <option value="4">Cada 1 mes</option>
                        </select>
                      </div>
                      <div class="col-xs-6">
                        <input type="text" class="form-control no-model" placeholder="Hasta" id="mantenimiento_fecha_hasta"/>
                      </div>
                    </div>
                  </div>
              </div>    
            <% } %>
            */ ?>
          </div>
        </div>
        <div id="mantenimiento_tab2" class="tab-pane">
          <div class="">
            <div class="clearfix">
              <button class="btn btn-info nueva_orden_trabajo">+ Agregar</button>
            </div>
            <div id="mantenimiento_ordenes_trabajo" class="mt10"></div>
          </div>
        </div>
      </div> 
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar btn-success fr">Guardar</button>
    <% if (id != undefined) { %>
      <button class="btn btn-danger eliminar fl">Eliminar</button>
      <button class="btn mr5 imprimir btn-default fr">Imprimir</button>
    <% } %>
  </div>
</div>
</script>

<script type="text/template" id="ordenes_trabajo_resultados_template">
  <table id="ordenes_trabajo_tabla" class="table table-small table-striped sortable m-b-none default footable">
    <thead>
      <tr>
        <th>Tipo</th>
        <th>C&oacute;digo</th>
        <th class="th_acciones w50"></th>
      </tr>
    </thead>
    <tbody class="tbody"></tbody>
  </table>
</script>

<script type="text/template" id="ordenes_trabajo_item_resultados_template">
  <td class="text-info data"><%= tipo_orden_trabajo %></td>
  <td class="data"><%= numero %></td>
  <td class="tar td_acciones">
    <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
  </td>
</script>

<script type="text/template" id="orden_trabajo_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7">Editar orden de trabajo</span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a id="orden_trabajo_paso_1_link" href="#orden_trabajo_tab1" class="buscar_todos" role="tab" data-toggle="tab">
            <i class="fa text-warning fa-calendar m-r-xs"></i>
            Informaci&oacute;n
          </a>
        </li>
        <li>
          <a id="orden_trabajo_paso_2_link" href="#orden_trabajo_tab2" role="tab" data-toggle="tab">
            <i class="fa text-info fa-address-book m-r-xs"></i>
            Tareas
          </a>
        </li>
        <li>
          <a id="orden_trabajo_paso_3_link" href="#orden_trabajo_tab3" role="tab" data-toggle="tab">
            <i class="fa text-success fa-align-left m-r-xs"></i>
            Observaciones
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="orden_trabajo_tab1" class="tab-pane active">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Tipo</label>
                <select class="form-control" id="orden_trabajo_tipos_ordenes_trabajo">
                  <% for(var i=0;i< window.tipos_ordenes_trabajo.length;i++) { %>
                    <% var o = tipos_ordenes_trabajo[i]; %>
                    <option <%= (id_tipo_orden_trabajo == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">N&uacute;mero</label>
                <input type="text" name="numero" id="orden_trabajo_numero" value="<%= numero %>" class="form-control"/>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Tiempo estimado</label>
                <div class="clearfix">
                  <div class="col-md-5 p0">
                    <input type="number" min="0" value="<%= duracion_cantidad %>" class="form-control no-model" id="orden_trabajo_duracion_cantidad"/>
                  </div>
                  <div class="col-md-7 p0">
                    <select id="orden_trabajo_duracion_tipo" name="duracion_tipo" class="form-control">
                      <option <%= (duracion_tipo=="M")?"selected":"" %> value="M">Minutos</option>
                      <option <%= (duracion_tipo=="H")?"selected":"" %> value="H">Horas</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Cant. de personas</label>
                <input type="text" name="cantidad_personas" id="orden_trabajo_cantidad_personas" value="<%= cantidad_personas %>" class="form-control"/>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Responsable</label>
                <input type="text" name="responsable" id="orden_trabajo_responsable" value="<%= responsable %>" class="form-control"/>
              </div>
            </div>
          </div>
          <% if (control.check("empresas_tercerizadas") > 0) { %>
            <div class="form-group">
              <label class="control-label">Empresa tercerizada</label>
              <div class="input-group">
                <select id="orden_trabajo_empresas_tercerizadas" class="form-control"></select>
                <span class="input-group-btn">
                  <button tabindex="-1" class="btn btn-info w100 agregar_empresa_tercerizada">+ Agregar</button>
                </span>
              </div>
            </div>
          <% } %>
        </div>
        <div id="orden_trabajo_tab2" class="tab-pane">
          <div class="">
            <div class="clearfix">
              <button class="btn btn-info nueva_tarea">+ Agregar</button>
            </div>
            <div id="orden_trabajo_tareas" class="mt10"></div>
          </div>
        </div>
        <div id="orden_trabajo_tab3" class="tab-pane">
          <div class="">
            <div class="form-group">
              <label class="control-label">Precauciones de seguridad</label>
              <textarea name="precauciones" class="form-control" name="orden_trabajo_precauciones" id="orden_trabajo_precauciones"><%= precauciones %></textarea>
            </div>
            <div class="form-group">
              <label class="control-label">Observaciones</label>
              <textarea name="observaciones" class="form-control" name="orden_trabajo_observaciones" id="orden_trabajo_observaciones"><%= observaciones %></textarea>
            </div>
          </div>
        </div>
      </div> 
    </div>
  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>

<script type="text/template" id="tareas_resultados_template">
  <table id="tareas_tabla" class="table table-small table-striped sortable m-b-none default footable">
    <thead>
      <tr>
        <th>Tipo</th>
        <th>M&aacute;quina</th>
        <th>Parte</th>
        <th class="th_acciones w50"></th>
      </tr>
    </thead>
    <tbody class="tbody"></tbody>
  </table>
</script>

<script type="text/template" id="tareas_item_resultados_template">
  <td class="text-info data"><%= tipo_tarea %></td>
  <td class="data"><%= maquina %></td>
  <td class="data"><%= parte %></td>
  <td class="tar td_acciones">
    <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
  </td>
</script>

<script type="text/template" id="tarea_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7">Editar tarea</span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a id="tarea_paso_1_link" href="#tarea_tab1" class="buscar_todos" role="tab" data-toggle="tab">
            <i class="fa text-warning fa-calendar m-r-xs"></i>
            Informaci&oacute;n
          </a>
        </li>
        <li>
          <a id="tarea_paso_2_link" href="#tarea_tab2" role="tab" data-toggle="tab">
            <i class="fa text-info fa-database m-r-xs"></i>
            Recursos
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="tarea_tab1" class="tab-pane active">
          <div class="">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Tipo de Tarea</label>
                  <div class="input-group">
                    <select id="tarea_tipos_tareas" class="form-control"></select>
                    <span class="input-group-btn">
                      <button tabindex="-1" class="btn btn-info agregar_tipo_tarea">+</button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Desgaste</label>
                  <div class="">
                    <div class="col-md-6 p0">
                      <input type="text" value="<%= cantidad_desgaste %>" name="cantidad_desgaste" class="form-control" id="tarea_cantidad_desgaste"/>
                    </div>
                    <div class="col-md-6 p0">
                      <select id="tarea_tipo_desgaste" class="form-control" name="tipo_desgaste">
                        <option <%= (tipo_desgaste=="H")?"selected":"" %> value="H">Horas</option>
                        <option <%= (tipo_desgaste=="T")?"selected":"" %> value="T">Toneladas</option>
                      </select>                
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">M&aacute;quina</label>
                  <select id="tarea_maquinas" class="form-control"></select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Parte</label>
                  <select id="tarea_partes" class="form-control"></select>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label">Observaciones</label>
              <textarea name="observaciones" class="form-control" name="tarea_observaciones" id="tarea_observaciones"><%= observaciones %></textarea>
            </div>
          </div>
        </div>
        <div id="tarea_tab2" class="tab-pane">
          <div class="">
            <div class="row clearfix">
              <div class="form-group col-sm-6">
                <label class="control-label">Recurso</label>
                <select id="tarea_articulos" class="form-control no-model" style="width: 100%"></select>
              </div>
              <div class="form-group col-sm-6">
                <label class="control-label">Cantidad</label>
                <div class="input-group">
                  <input id="tarea_articulo_cantidad" type="text" class="form-control no-model"/>
                  <span class="input-group-btn">
                    <a id="tarea_articulo_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                  </span>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <textarea id="tarea_articulo_descripcion" class="form-control no-model" placeholder="Anote alguna observacion para el recurso solicitado..."></textarea>
              </div>
            </div>
            <div class="table-responsive" style="overflow: auto; max-height: 400px">
              <table id="tarea_articulos_tabla" class="table m-b-none default footable">
                <thead>
                  <tr>
                    <th style="display: none"></th>
                    <th>Recurso</th>
                    <th>Cantidad</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var i=0;i< articulos.length;i++) { %>
                    <% var p = articulos[i] %>
                    <tr>
                      <td class="id_articulo dn"><%= p.id_articulo %></td>
                      <td class="articulo editar_articulo"><span class="text-info editar_articulo"><%= p.articulo %></span></td>
                      <td class="descripcion dn editar_articulo"><%= p.descripcion %></td>
                      <td class="cantidad editar_articulo tar"><%= Number(p.cantidad).toFixed(2) %></td>
                      <td class="tar">
                        <button class="btn btn-sm btn-white eliminar_articulo"><i class="fa fa-trash"></i></button>
                      </td>
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> 
    </div>
    
  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>