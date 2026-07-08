<script type="text/template" id="empresas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <b>Empresas</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading oh">
        <div class="row">
          <div class="search_container col-md-3 pr0"></div>
          <div class="col-md-3">
            <select class="form-control no-model" id="empresas_estado">
              <option value="-1">Estado</option>
              <option value="0">Demo</option>
              <option value="5">Preparando Cuenta</option>
              <option value="10">Cliente</option>
              <option value="20">Baja</option>              
            </select>
          </div>
          <div class="col-md-6">
            <a class="btn pull-right btn-info btn-addon" href="app/#nuevo_proyecto/<%= id_proyecto %>"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="empresas_table" class="m-b-none table table-small table-striped sortable default footable">
            <thead>
              <tr>
                <th class="w25"></th>
                <th class="w25"></th>
                <th class="w25">ID</th>
                <th>Razon Social</th>
                <th></th>
                <th>Plan</th>
                <th>Login</th>
                <th>Costo</th>
                <th>Estado</th>
                <th class="w150">Vendedor</th>
                <th>Observaciones</th>
                <% if (permiso > 1) { %>
                  <th class="th_acciones w80"></th>
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


<script type="text/template" id="empresas_item">
  <td><span class='ver'>
    <% if (estado == 0) { %><i data-toggle="tooltip" title="Activa. Prox Venc: <%= fecha_prox_venc %>" class='fa fa-check text-success'></i><% } %>
    <% if (estado == 1) { %><i data-toggle="tooltip" title="Vencida el <%= fecha_prox_venc %>" class='fa fa-clock-o text-warning'></i><% } %>
    <% if (estado == 2) { %><i data-toggle="tooltip" title="Suspendida el <%= fecha_suspension %>" class='fa fa-times text-danger'></i><% } %>
  </span></td>
  <td><span class='ver'>
    <% if (administrar_pagos == 1) { %><i data-toggle="tooltip" title="Administrar Pagos" class='fa fa-dollar'></i><% } %>
  </span></td>
  <td><span class='ver'><%= id %></span></td>
  <td><span class='ver <%= (activo==1)?"text-info":"" %>'><%= razon_social %></span>
    <% if (dominios.length > 0) { %>
      <br/>
      <% for(i=0;i< dominios.length;i++) { %>
        <% var dom = dominios[i] %>
        <a target="_blank" href="https://<%= dom.dominio %>" class="label bg-light dk"><%= dom.dominio %></a>
      <% } %>
    <% } %>
  </td>
  <td>
    <% if (!isEmpty(telefono_empresa)) { %>
      <a data-toggle="tooltip" title="<%= telefono_empresa %>" class="enviar_whatsapp" href="javascript:void(0)"><i class="fa fa-whatsapp iconito active success"></i></a>
    <% } %>
  </td>
  <td>
    <span class="label bg-light dk"><%= plan %></span><br/>
    <%= cantidad %>/<%= limite %></td>
  <td><span data-toggle="tooltip" title="<%= usuario_ultimo_ingreso %>" class="label bg-light dk"><%= fecha_ultimo_ingreso %></span></td>
  <td>
    <% if (costo > 0) { %>
      <span class="label bg-light dk"><%= costo %></span>
    <% } %>
  </td>
  <td>
    <% if (estado_empresa == 1) { %><span class="label bg-danger">A contactar</span><% } %>
    <% if (estado_empresa == 2) { %><span class="label bg-primary">En progreso</span><% } %>
    <% if (estado_empresa == 3) { %><span class="label bg-info">Interesado</span><% } %>
    <% if (estado_empresa == 0) { %><span class="label bg-light dk">Demo</span><% } %>
    <% if (estado_empresa == 5) { %><span class="label bg-warning">Preparando cuenta</span><% } %>
    <% if (estado_empresa == 10) { %><span class="label bg-success">Cliente</span><% } %>
    <% if (estado_empresa == 20) { %><span class="label bg-danger">Baja</span><% } %>
  </td>
  <td>
    <% for (var i=0;i< vendedores.length;i++) { %>
      <% var v = vendedores[i] %>
      <span class="label bg-light dk"><%= v.nombre %></span>
    <% } %>
  </td>
  <td><span class='ver'><% if (tipo_empresa == 4) { %>
    <span class="label bg-success">La Plata Construye</span><br/>
  <% } %><%= nl2br(comentarios) %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i class="icon-user user text-dark" title="Login" data-id="<%= id %>" />
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>
        <ul class="dropdown-menu pull-right">
          <% if (ID_USUARIO == 0) { %>
            <li><a target="_blank" href="empresas/function/exportar/<%= id %>/0/">Exportar Base</a></li>
            <li><a target="_blank" href="empresas/function/exportar/<%= id %>/1/">Exportar Datos</a></li>
            <li><a target="_blank" href="empresas/function/exportar_configuracion/<%= id %>/">Exportar Configuracion</a></li>
            <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
          <% } %>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="empresas_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("mis_datos") %>
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuracion","en"=>"Configuration")); ?>
      / <b><%= modulo.title %></b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="centrado rform">
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></label>
                  <input type="text" name="nombre" class="form-control" id="empresas_nombre" value="<%= nombre %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Email</label>
                  <input type="text" name="email" class="form-control" id="empresas_email" value="<%= email %>"/>
                </div>
                <% if (PERFIL == -1) { %>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Estado</label>
                        <select id="empresas_estado_empresa" class="form-control" name="estado_empresa">
                          <option <%= (estado_empresa == 1)?"selected":"" %> value="1">A contactar</option>
                          <option <%= (estado_empresa == 2)?"selected":"" %> value="2">En progreso</option>
                          <option <%= (estado_empresa == 3)?"selected":"" %> value="3">Interesado</option>
                          <option <%= (estado_empresa == 0)?"selected":"" %> value="0">Demo</option>
                          <option <%= (estado_empresa == 5)?"selected":"" %> value="5">Preparando Cuenta</option>
                          <option <%= (estado_empresa == 10)?"selected":"" %> value="10">Cliente</option>
                          <option <%= (estado_empresa == 20)?"selected":"" %> value="20">Baja</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Plan</label>
                        <select name="id_plan" class="form-control" id="empresas_plan">
                          <option value="0" <%= (id_plan == 0)?"selected":"" %>>-</option>
                          <% for (var i=0;i< planes.length;i++) { %>
                            <% var plan = planes[i] %>
                            <% if (id_proyecto == plan.id_proyecto) { %>
                              <option data-costo="<%= plan.precio_anual %>" data-limite="<%= plan.limite_articulos %>" value="<%= plan.id %>" <%= (plan.id == id_plan)?"selected":"" %>><%= plan.nombre %></option>
                            <% } %>
                          <% } %>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Costo Servicio</label>
                        <input type="text" name="costo" class="form-control" id="empresas_costo" value="<%= costo %>"/>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Limite (0 = Sin Limite)</label>
                        <input type="text" name="limite" class="form-control" id="empresas_limite" value="<%= limite %>"/>
                      </div>
                    </div>
                  </div>
                <% } %>                
                <% if (PERFIL == -1) { %>
                  <div class="form-group">
                    <label class="control-label">Observaciones</label>
                    <textarea style="height:250px" name="comentarios" class="form-control" id="empresas_comentarios"><%= comentarios %></textarea>
                  </div>
                <% } %>
                <% if (MILLING == 0) { %>
                  <div class="form-group mb0 tar">
                    <a class="expand-link">
                      <?php echo lang(array(
                        "es"=>"+ M&aacute;s opciones",
                        "en"=>"+ More options",
                      )); ?>
                    </a>
                  </div>
                <% } %>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Raz&oacute;n Social</label>
                      <input type="text" name="razon_social" class="form-control" id="empresas_razon_social" value="<%= razon_social %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Tipo de contribuyente</label>
                      <select class="form-control" name="tipo_contribuyente" id="empresas_tipo_contribuyente">
                        <option value="2" <%= (id_tipo_contribuyente == 2) ? "selected": "" %>>Monotributo</option>
                        <option value="1" <%= (id_tipo_contribuyente == 1) ? "selected": "" %>>Responsable Inscripto</option>
                        <option value="3" <%= (id_tipo_contribuyente == 3) ? "selected": "" %>>Exento</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">CUIT</label>
                      <input type="text" name="cuit" class="form-control" id="empresas_cuit" value="<%= cuit %>"/>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Tel&eacute;fono</label>
                      <input type="text" name="telefono_empresa" class="form-control" id="empresas_telefono" value="<%= telefono_empresa %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Direcci&oacute;n</label>
                      <input type="text" name="direccion_empresa" class="form-control" id="empresas_direccion" value="<%= direccion_empresa %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Localidad</label>
                      <input type="text" value="<%= localidad %>" id="empresas_localidad" autocomplete="off" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control no-model"/>
                    </div>
                  </div>
                </div>
                <% if (id == undefined) { %>
                  <div class="form-group">
                    <label class="control-label">Duplicar desde</label>
                    <select name="id_empresa_modelo" class="form-control" id="empresas_modelos"></select>
                  </div>
                <% } %>
                <% if (id != undefined) { %>
                  <div class="form-group">
                    <?php
                    single_upload(array(
                     "name"=>"logo",
                     "label"=>"Encabezado de informes",
                     "url"=>"empresas/function/save_image/",
                     "resizable"=>1,
                     "description"=>"Utilizado en los comprobantes, remitos, presupuestos, etc. Tama&ntilde;o recomendado: 450 x 280 p&iacute;xeles"
                     )); ?>
                  </div>
                   <div class="form-group">
                    <?php
                    single_upload(array(
                     "name"=>"path",
                     "label"=>"Foto de perfil del sistema",
                     "url"=>"empresas/function/save_image/",
                     "width"=>400,
                     "height"=>400,
                     "description"=>"Utilizado como imagen de perfil del sistema. Tama&ntilde;o recomendado: 200 x 200 p&iacute;xeles"
                     )); ?>
                   </div>
                <% } %>
                <% if (PERFIL == -1) { %>
                  <div class="form-group">
                    <label class="control-label">
                      <label class="i-checks m-b-none">
                        <input id="empresa_activo" value="1" <%= (activo==1)?"checked":"" %> type="checkbox"><i></i>
                        La empresa se encuentra activa.
                      </label>
                    </label>
                  </div>
                <% } %>
              </div>
            </div>
          </div>
        </div>
      </div>
      <% if (PERFIL == -1) { %>
        <div class="row">
          <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="padder">
                  <div class="form-group mb0 clearfix">
                    <label class="control-label">
                      P&aacute;gina Web
                    </label>
                    <a class="expand-link fr">
                      <?php echo lang(array(
                        "es"=>"+ Ver opciones",
                        "en"=>"+ View options",
                      )); ?>
                    </a>
                  </div>
                </div>
              </div>
              <div class="panel-body expand">
                <div class="padder">
                  <div class="form-group">
                    <label class="control-label">Dominios</label>
                    <select multiple id="empresas_dominios" style="width: 100%">
                      <% for (var i=0; i< dominios.length; i++) { %>
                        <% var o = dominios[i] %>
                        <option selected><%= o %></option>
                      <% } %>
                    </select>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Template</label>
                        <select name="id_web_template" class="form-control" id="empresas_templates"></select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Dominio principal</label>
                        <input type="text" name="dominio_ppal" class="form-control" id="empresas_dominio_ppal" value="<%= dominio_ppal %>"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="padder">
                  <div class="form-group mb0 clearfix">
                    <label class="control-label">
                      Administraci&oacute;n de pagos
                    </label>
                    <a class="expand-link fr">
                      <?php echo lang(array(
                        "es"=>"+ Ver opciones",
                        "en"=>"+ View options",
                      )); ?>
                    </a>
                  </div>
                </div>
              </div>
              <div class="panel-body expand">
                <div class="padder">
                  <div class="form-group">
                    <label class="control-label">
                      <label class="i-checks m-b-none">
                        <input id="empresa_administrar_pagos" value="1" <%= (administrar_pagos==1)?"checked":"" %> type="checkbox"><i></i>
                        Habilitar la gesti&oacute;n de pagos para esta empresa
                      </label>
                    </label>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Periodicidad</label>
                        <select class="form-control" name="periodo_fact">
                          <option <%= (periodo_fact == "+1 month")?"selected":"" %> value="+1 month">Mensual</option>
                          <option <%= (periodo_fact == "+3 months")?"selected":"" %> value="+3 months">Trimestral</option>
                          <option <%= (periodo_fact == "+6 months")?"selected":"" %> value="+6 months">Semestral</option>
                          <option <%= (periodo_fact == "+1 year")?"selected":"" %> value="+1 year">Anual</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Fecha Prox. Emision</label>
                        <div class="input-group">
                          <input type="text" name="fecha_prox_venc" class="form-control" id="empresas_fecha_prox_venc" value="<%= fecha_prox_venc %>"/>
                          <span class="input-group-btn">
                            <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Fecha Suspension Panel</label>
                        <div class="input-group">
                          <input type="text" name="fecha_suspension" class="form-control" id="empresas_fecha_suspension" value="<%= fecha_suspension %>"/>
                          <span class="input-group-btn">
                            <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                          </span>
                        </div>
                     </div>
                    </div>
                  </div>
                  <div class="b-a m-b" style="overflow: auto; max-height: 400px">
                    <div class="table-responsive fondo-blanco">
                      <table id="empresa_facturas_tabla" class="table m-b-none default footable">
                        <thead>
                          <tr>
                            <th class="w50">Pago</th>
                            <th class="w50">Nro.</th>
                            <th class="w50">Monto</th>
                            <th class="w100">Vencimiento</th>
                            <th class="w120">Fecha Pago</th>
                            <th>Observaciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <% for(var i=0; i< facturas.length; i++) { %>
                            <% var factura = facturas[i] %>
                            <tr data-id="<%= factura.id %>">
                              <td>
                                <label class="i-checks m-b-none">
                                  <input class="pagada" value="1" <%= (factura.pagada==1)?"checked":"" %> type="checkbox"><i></i>
                                </label>
                              </td>
                              <td class="tar"><%= factura.numero %></td>
                              <td class="tar"><%= Number(factura.monto).toFixed(2) %></td>
                              <td class="tar"><%= factura.vencimiento %></td>
                              <td class="p0">
                                <input type="text" class="form-control fecha_pago" data-value="<%= factura.fecha_pago %>"/>
                              </td>
                              <td class="p0"><input type="text" class="form-control observaciones" value="<%= factura.observaciones %>"/></td>
                            </tr>
                          <% } %>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <% if (ID_USUARIO == 0) { %>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Vendedor</label>
                          <select id="empresa_vendedores" class="form-control no-model">
                            <option value="0">-</option>
                            <% for(var i=0;i< window.usuarios.size(); i++) { %>
                              <% var usuario = window.usuarios.models[i] %>
                              <option value="<%= usuario.id %>"><%= usuario.get("nombre") %></option>
                            <% } %>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Monto</label>
                          <div class="input-group w100p">
                            <input type="text" class="form-control no-model" id="empresa_monto">
                            <span class="input-group-btn w1p">
                              <a id="vendedor_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="fondo-blanco">
                      <table id="vendedores_tabla" class="table m-b-none default footable">
                        <thead>
                          <tr>
                            <th>Nombre</th>
                            <th>Monto</th>
                            <th class="w25"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <% for(var i=0;i< vendedores.length;i++) { %>
                            <% var p = vendedores[i] %>
                            <tr id="vendedor_<%= p.id %>" data-id="<%= p.id_usuario %>">
                              <td class="editar_vendedor"><%= p.nombre %></td>
                              <td class="editar_vendedor monto"><%= p.monto %></td>
                              <td><i class='fa fa-times eliminar_vendedor text-danger cp'></i></td>
                            </tr>
                          <% } %>
                        </tbody>
                      </table>
                    </div>
                  <% } %>

                </div>
              </div>
            </div>


            <% if (id != undefined) { %>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="padder">
                    <div class="form-group mb0 clearfix">
                      <label class="control-label">
                        M&oacute;dulos habilitados
                      </label>
                      <a class="expand-link fr">
                        <?php echo lang(array(
                          "es"=>"+ Ver opciones",
                          "en"=>"+ View options",
                        )); ?>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="panel-body expand">
                  <div class="padder">
                    <div class="b-a" style="overflow: auto; max-height: 400px">
                      <div class="table-responsive fondo-blanco">
                        <table id="empresa_modulos_tabla" class="table m-b-none default footable">
                          <thead>
                            <tr>
                              <th>Etiqueta</th>
                              <th>ID</th>
                              <th>Estado</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <% } %>

            <div class="panel panel-default">
              <div class="panel-body">
                <div class="padder">
                  <div class="form-group mb0 clearfix">
                    <label class="control-label">
                      Configuraci&oacute;n avanzada
                    </label>
                    <a class="expand-link fr">
                      <?php echo lang(array(
                        "es"=>"+ Ver opciones",
                        "en"=>"+ View options",
                      )); ?>
                    </a>
                  </div>
                </div>
              </div>
              <div class="panel-body expand">
                <div class="padder">

                  <% if (PERFIL == -1) { %>
                    <div class="form-group">
                      <label class="control-label">Configuraciones Especiales: Lineas con formato: <b>clave = valor;</b> </label>
                      <textarea style="height:250px" name="configuraciones_especiales" class="form-control" id="empresas_configuraciones_especiales"><%= configuraciones_especiales %></textarea>
                    </div>

                    <div>
                      <p>
                        <b>FACTURACION_INGRESAR_SOLO_PRODUCTOS (0|1)</b>: <br/>
                        Si es 1, obliga a que solamente se puedan ingresar productos a la factura. (No se puede ingresar cualquier cosa)
                      </p>
                      <p>
                        <b>FACTURACION_PERIODICA (0|1)</b>: <br/>
                        Indica si habilitamos a la empresa a hacer facturaciones periodicas. (El filtro de estados cambia).
                      </p>
                      <p>
                        <b>FACTURACION_VENDER_AL_COSTO (0|1)</b>: <br/>
                        Habilita para vender la mercaderia al costo.
                      </p>
                    </div>

                  <% } %>

                  <div class="form-group">
                    <label class="control-label">Servidor local</label>
                    <input type="text" name="servidor_local" class="form-control" id="empresas_servidor_local" value="<%= servidor_local %>"/>
                  </div>
                  <div class="form-inline m-b">
                    <label class="control-label m-r">Menu Lateral</label>
                    <div class="form-group ml20">
                      <label class="i-switch bg-info m-r">
                        <input type="checkbox" id="configuracion_menu" name="configuracion_menu" class="checkbox" value="1" <%= (configuracion_menu == 1)?"checked":"" %> >
                        <i></i>
                      </label>
                    </div>
                  </div>
                  <div class="form-inline m-b">
                    <label class="control-label m-r">Mostrar iconos de menu</label>
                    <div class="form-group ml20">
                      <label class="i-switch bg-info m-r">
                        <input type="checkbox" id="configuracion_menu_iconos" name="configuracion_menu_iconos" class="checkbox" value="1" <%= (configuracion_menu_iconos == 1)?"checked":"" %> >
                        <i></i>
                      </label>
                    </div>
                  </div>            
                  <div class="form-inline m-b">
                    <label class="control-label m-r">Emitir alerta de sonido al guardar</label>
                    <div class="form-group ml20">
                      <label class="i-switch bg-info m-r">
                        <input type="checkbox" name="configuracion_sonido" class="checkbox" value="1" <%= (configuracion_sonido == 1)?"checked":"" %> >
                        <i></i>
                      </label>
                    </div>
                  </div>
                  <div class="form-inline m-b">
                    <label class="control-label m-r">Generar autom&aacute;ticamente codigos de articulos, clientes, proveedores, etc</label>
                    <div class="form-group ml20">
                      <label class="i-switch bg-info m-r">
                        <input type="checkbox" name="configuracion_autogenerar_codigos" class="checkbox" value="1" <%= (configuracion_autogenerar_codigos == 1)?"checked":"" %> >
                        <i></i>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <% } %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1 tar">
          <% if (ID_EMPRESA == 0 && id != undefined) { %>
            <button class="btn btn-default login">Login</button>
          <% } %>
          <button class="btn btn-success guardar m-l-xs">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</script>

<?php include("config/empresa_detalle_10.php"); ?>
<?php include("config/empresa_detalle_5.php"); ?>

<script type="text/template" id="configuracion_tabla_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Columnas de la tabla: <%= titulo %></div>
    <div class="panel-body">
      <div style="height:340px;overflow:auto">
        <div ui-jq="nestable" class="dd">
          <ol class="dd-list">
            <% for(var i=0;i< tabla.campos.length;i++) { %>
              <% var c = tabla.campos[i] %>
              <li class="dd-item dd3-item" data-id="'+o.id+'">
                <div class="dd-handle dd3-handle">Drag</div>
                <div class="dd3-content" style="padding:3px 10px 3px 50px">
                  <div class="clearfix columna_editable">
                    <label class="i-checks m-b-none pull-left mt7">
                      <input <%= (c.visible==1)?"checked":"" %> type="checkbox"><i></i>
                    </label>
                    <input data-campo="<%= c.campo %>" data-ordenable="<%= c.ordenable %>" data-ocultable="<%= c.ocultable %>" data-clases="<%= c.clases %>" value="<%= c.titulo %>" type="text" class="form-control no-model pull-left w200"/>
                  </div>
                </div>
              </li>
            <% } %>
          </ol>
        </div>
      </div>
      <div class="form-group">
        <label class="label-control">Items por pagina</label>
        <select id="configuracion_tabla_cant_items" class="form-control no-model">
          <option <%= (tabla.cant_items == 10)?"selected":"" %> value="10">10</option>
          <option <%= (tabla.cant_items == 15)?"selected":"" %> value="15">15</option>
          <option <%= (tabla.cant_items == 20)?"selected":"" %> value="20">20</option>
          <option <%= (tabla.cant_items == 30)?"selected":"" %> value="30">30</option>
          <option <%= (tabla.cant_items == 50)?"selected":"" %> value="50">50</option>
          <option <%= (tabla.cant_items == 100)?"selected":"" %> value="100">100</option>
          <option <%= (tabla.cant_items == 200)?"selected":"" %> value="200">200</option>
          <option <%= (tabla.cant_items == 99999999999)?"selected":"" %> value="99999999999">Todo</option>
        </select>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="guardar btn btn-success">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="empresas_gestion_pagos_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Gestion de Pagos</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="input-group pull-left" style="width: 140px;">
            <input type="text" id="empresas_gestion_pagos_fecha_desde" class="form-control">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
          <div class="input-group pull-left" style="width: 140px;">
            <input type="text" id="empresas_gestion_pagos_fecha_hasta" class="form-control">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
          <select id="empresas_gestion_pagos_proyectos" class="form-control no-model w150 fl">
            <option value="0">Proyecto</option>
            <% for(var i=0;i< window.proyectos.length; i++) { %>
              <% var proyecto = window.proyectos[i] %>
              <% if (proyecto.id > 0) { %>
                <option value="<%= proyecto.id %>"><%= proyecto.nombre %></option>
              <% } %>
            <% } %>
          </select>
          <select id="empresas_gestion_pagos_estados" class="form-control no-model w150 fl">
            <option value="-1">Estado</option>
            <option value="1">A contactar</option>
            <option value="2">En progreso</option>
            <option value="3">Interesado</option>
            <option value="0">Demo</option>
            <option value="5">Preparando Cuenta</option>
            <option value="10">Cliente</option>
            <option value="20">Baja</option>
          </select>
          <% if (ID_USUARIO == 0) { %>
            <select id="empresas_gestion_pagos_vendedores" class="form-control no-model w150 fl">
              <option value="0">Vendedor</option>
              <% for(var i=0;i< window.usuarios.size(); i++) { %>
                <% var usuario = window.usuarios.models[i] %>
                <option value="<%= usuario.id %>"><%= usuario.get("nombre") %></option>
              <% } %>
            </select>
          <% } %>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-body">
          <div id="empresas_gestion_pagos_tabla" class="b-a table-responsive">
          </div>
        </div>
      </div>
    </div>
  </div>  
</script>

<script type="text/template" id="empresas_gestion_pagos_tabla_template">
<table class="table table-small table-striped sortable m-b-none default footable">
  <thead>
    <tr>
      <th>#</th>
      <th>Estado</th>
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
        <td>
          <% if (c.estado_empresa == 1) { %><span class="label bg-danger dk">A contactar</span><% } %>
          <% if (c.estado_empresa == 0) { %><span class="label bg-light dk">Demo</span><% } %>
          <% if (c.estado_empresa == 5) { %><span class="label bg-warning">Preparando Cuenta</span><% } %>
          <% if (c.estado_empresa == 10) { %><span class="label bg-success">Cliente</span><% } %>
          <% if (c.estado_empresa == 20) { %><span class="label bg-danger">Baja</span><% } %>
        </td>        
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
      <td></td>
      <td></td>
      <% for(var i=0; i< totales.length; i++) { %>
        <% var c = totales[i] %>
        <td><%= c %></td>
      <% } %>
    </tr>
  </tfoot>
</table>
</script>