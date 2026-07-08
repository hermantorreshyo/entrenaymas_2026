<script type="text/template" id="pres_clientes_panel_template">
<% if (seleccionar) { %>
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-xs-12 sm-m-b">
          <div class="input-group">
            <input type="text" id="pres_clientes_buscar" value="<%= window.pres_clientes_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="pres_clientes_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;"></th>
              <th class="sorting" data-sort-by="nombre">Nombre</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
<% } else { %>
  <div class="seccion_llena">
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <% var modulo = control.get("pres_clientes") %>
      <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= (window.pres_clientes_garante == 1) ? "Garantes" : modulo.title %></h1>
    </div>
    <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="clearfix">
            <div class="input-group fl w400 mr10">
              <input type="text" id="pres_clientes_buscar" value="<%= window.pres_clientes_filter %>" placeholder="Cliente" autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
              </span>
            </div>
            <div class="pull-right">
              <div class="btn-group dropdown">
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-cog"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" class="exportar_excel">Exportar Excel</a></li>
                  <li><a href="javascript:void(0)" class="importar_excel">Importar Excel</a></li>
                  <li class="divider"></li>
                  <li><a href="javascript:void(0)" class="exportar_csv">Exportar TXT</a></li>
                  <li><a href="javascript:void(0)" class="importar_csv">Importar TXT</a></li>
                </ul>
              </div>
              <a class="btn btn-default ml5 btn-addon simular" href="javascript:void(0)">
                <i class="fa fa-gamepad"></i><span>Simular</span>
              </a>
              <% if (control.check("pres_clientes")>1) { %>
                <a class="btn btn-info btn-addon ml5 nuevo_cliente" href="javascript:void(0)">
                  <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
                </a>
              <% } %>
            </div>
          </div>
        </div>
        <div class="advanced-search-div bg-light dk" style="<%= ( !isEmpty(window.pres_clientes_numero_prestamo) || (window.pres_clientes_estado > 0) || (window.pres_clientes_filtro_especial > 0) || (window.pres_clientes_id_plan != 0) )?"display:block":"display:none" %>">
          <div class="wrapper oh">
            <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
            <div class="row pl10 pr10">
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <input type="text" id="pres_clientes_numero_prestamo" value="<%= window.pres_clientes_numero_prestamo %>" placeholder="Nro. Prestamo" autocomplete="off" class="form-control">
                </div>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select class="form-control action no-model" id="pres_clientes_sucursales">
                    <option value="0">Sucursal</option>
                    <% for(var i=0;i< almacenes.length;i++) { %>
                      <% var almacen = almacenes[i] %>
                      <option value="<%= almacen.id %>" <%= (window.pres_clientes_id_sucursal == almacen.id)?"selected":"" %>><%= almacen.nombre %></option>
                    <% } %>
                  </select>
                </div>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select class="form-control action no-model" id="pres_clientes_planes"></select>
                </div>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select class="form-control action no-model" id="pres_clientes_estado">
                    <option <%= (window.pres_clientes_estado == 0)?"selected":"" %> value="0">Estado</option>
                    <option <%= (window.pres_clientes_estado == 1)?"selected":"" %> value="1">Vigentes</option>
                    <option <%= (window.pres_clientes_estado == 2)?"selected":"" %> value="2">Cancelados</option>
                    <option <%= (window.pres_clientes_estado == 3)?"selected":"" %> value="3">Sin Prestamos</option>
                  </select>
                </div>
              </div>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select class="form-control action no-model" id="pres_clientes_filtro_especial">
                    <option value="0">Ver todos</option>
                    <option <%= (window.pres_clientes_filtro_especial==1)?"selected":"" %> value="1">Habilitados para paralelo</option>
                    <option <%= (window.pres_clientes_filtro_especial==2)?"selected":"" %> value="2">Habilitados para renovacion</option>
                    <option <%= (window.pres_clientes_filtro_especial==3)?"selected":"" %> value="3">Enviados a Estudio</option>
                  </select>
                </div>
              </div>

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="input-group">
                    <input placeholder="Vencimiento" type="text" class="form-control no-model" value="<%= window.pres_clientes_filtro_fecha_vencimiento %>" id="pres_clientes_filtro_fecha_vencimiento"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>        
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="pres_clientes_table" class="table table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th style="width:20px;"></th>
                  <th class="w50 tac hidden-xs"></th>
                  <th class="sorting" data-sort-by="apellido">Nombre</th>
                  <th class="w20"></th>
                  <th class="col-xxs-0">Documento</th>
                  <th class="col-xxs-0 sorting" data-sort-by="telefono">Telefono</th>
                  <th class="col-xxs-0 sorting" data-sort-by="localidad">Localidad</th>
                  <% if (permiso > 1) { %>
                    <th class="th_acciones w120">Acciones</th>
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
  </div>
<% } %>
</script>

<script type="text/template" id="pres_clientes_analisis_view">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>An&aacute;lisis del cliente</b>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <div class="row form-group">
        <div class="col-md-4 col-xs-4">
          <a id="pres_clientes_analisis_label_1" href="http://www.asolafirma.com.ar/ingreso.htm" target="_blank" class="btn btn-default btn-block">
            Cr&eacute;ditos Berisso
          </a>
        </div>
        <div class="col-md-8 col-xs-8">
          <input type="text" id="pres_clientes_analisis_1" class="form-control no-model" />
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-xs-4">
          <a id="pres_clientes_analisis_label_2" href="https://prot.com.ar/" target="_blank" class="btn btn-default btn-block">
            Protcom
          </a>
        </div>
        <div class="col-md-8 col-xs-8">
          <input type="text" id="pres_clientes_analisis_2" class="form-control no-model" />
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-xs-4">
          <a id="pres_clientes_analisis_label_3" href="http://www.bcra.gob.ar/BCRAyVos/Situacion_Crediticia_CUIT_CUIL.asp" target="_blank" class="btn btn-default btn-block">
            BCRA
          </a>
        </div>
        <div class="col-md-8 col-xs-8">
          <input type="text" id="pres_clientes_analisis_3" class="form-control no-model" />
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-xs-4">
          <a id="pres_clientes_analisis_label_4" href="https://servicios1.afip.gob.ar/tramites_con_clave_fiscal/misaportes/app/basica/ingresoDatos.aspx" target="_blank" class="btn btn-default btn-block">
            AFIP
          </a>
        </div>
        <div class="col-md-8 col-xs-8">
          <input type="text" id="pres_clientes_analisis_4" class="form-control no-model" />
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-xs-4">
          <a id="pres_clientes_analisis_label_5" href="https://www.nosis.com/es" target="_blank" class="btn btn-default btn-block">
            NOSIS
          </a>
        </div>
        <div class="col-md-8 col-xs-8">
          <input type="text" id="pres_clientes_analisis_5" class="form-control no-model" />
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-xs-4">
          <a id="pres_clientes_analisis_label_6" href="https://online.org.veraz.com.ar/pls/consulta817/WORA2_ONLINE_WEB.HOMEPAGE?TIPO_INFORME=0" target="_blank" class="btn btn-default btn-block">
            VERAZ
          </a>
        </div>
        <div class="col-md-8 col-xs-8">
          <input type="text" id="pres_clientes_analisis_6" class="form-control no-model" />
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-xs-4">
          <a id="pres_clientes_analisis_label_7" href="http://www.riesgonet.com/servicios.php" target="_blank" class="btn btn-default btn-block">
            RIESGONET
          </a>
        </div>
        <div class="col-md-8 col-xs-8">
          <input type="text" id="pres_clientes_analisis_7" class="form-control no-model" />
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-xs-4">
          <a id="pres_clientes_analisis_label_8" href="https://www.pypdatos.com.ar/peype/" target="_blank" class="btn btn-default btn-block">
            P&P DATOS
          </a>
        </div>
        <div class="col-md-8 col-xs-8">
          <input type="text" id="pres_clientes_analisis_8" class="form-control no-model" />
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn btn-default fl cerrar">Cerrar</button>
    <button class="btn btn-success fr continuar">Continuar</button>
  </div>
</div>
</script>

<script type="text/template" id="pres_clientes_item">
  <% var clase = (activo==1)?"":"text-muted"; %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>
    <td class="<%= clase %> data hidden-xs">
    <% if (!isEmpty(path)) { %>
      <a href="/sistema/<%= path %>" target="_blank">
        <img src="/sistema/<%= path %>" class="customcomplete-image"/>
      </a>
    <% } else { %>
      <span class="avatar xs avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %> pull-left">
        <%= isEmpty(nombre) ? email.substr(0,1).toUpperCase() : nombre.substr(0,1).toUpperCase() %>
      </span>
    <% } %>
    </td>
  <% } %> 
  <td class='data'><span class="capitalize <%= (activo==1)?'text-info':'text-muted' %>"><%= apellido.ucwords() %> <%= nombre.ucwords() %></span></td>
  <td>
    <% if (!isEmpty(nota)) { %>
      <i data-toggle="tooltip" title="<%= nota %>" class="fa fa-commenting text-warning"></i>
    <% } %>
  </td>
  <% if (!seleccionar) { %>
    <td class="data col-xxs-0 <%= clase %>">
      <span>
        <%= (id_tipo_documento == 96) ? "DNI":"" %>
        <%= (id_tipo_documento == 89) ? "LE":"" %>
        <%= (id_tipo_documento == 90) ? "LC":"" %>
        <%= (id_tipo_documento == 94) ? "Pas.":"" %>
        <%= documento %>
      </span>
    </td>
    <td class="data col-xxs-0 <%= clase %>"><span><%= (isEmpty(telefono))?"—":telefono %></span></td>
    <td class="data col-xxs-0 <%= clase %>"><span class="text-info"><%= (isEmpty(localidad))?"—":localidad.toLowerCase() %></span></td>
  <% } %> 
  <% if (permiso > 1) { %>
    <td class="p5 <%= clase %> td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>   
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>  
    </td>
  <% } %>
</script>

<script type="text/template" id="pres_clientes_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("pres_clientes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= (window.pres_clientes_garante == 1) ? "Garantes" : modulo.title %>
    / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <?php include("pres_clientes_detalle.php"); ?>
    <% if (PERFIL != 1181) { %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1 tar">
          <div class="line b-b m-b-lg"></div>
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    <% } %>
  </div>
</div>
</script>

<script type="text/template" id="pres_clientes_timeline_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("pres_clientes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-4">

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row tac-xs">
                <div class="col-md-3 col-xs-12">
                  <% if (!isEmpty(path)) { %>
                    <a href="/sistema/<%= path %>" target="_blank">
                      <img src="/sistema/<%= path %>" class="customcomplete-image xl"/>
                    </a>
                  <% } else { %>
                    <span class="avatar xl avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %>">
                      <%= isEmpty(nombre) ? email.substr(0,1).toUpperCase() : nombre.substr(0,1).toUpperCase() %>
                    </span>
                  <% } %>
                </div>
                <div class="col-md-9 col-xs-12">
                  <h3 class="m-t-sm m-b-xs"><%= nombre.ucwords()+" "+apellido.ucwords() %></h3>
                  <a class="text-azul fs14"><%= email.toLowerCase() %></a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <span class="bold negro">Informaci&oacute;n b&aacute;sica</span>
          </div>
          <div class="panel-body acerca_de">
            <div class="form-group">
              <label class="control-label oh h22">Nombre completo</label>
              <span class="control-info"><%= apellido.ucwords() %> <%= nombre.ucwords() %></span>
            </div>
            <div class="form-group">
              <label class="control-label oh h22">Documento</label>
              <span class="control-info">
                <%= (id_tipo_documento == 96) ? "DNI":"" %>
                <%= (id_tipo_documento == 89) ? "LE":"" %>
                <%= (id_tipo_documento == 90) ? "LC":"" %>
                <%= (id_tipo_documento == 94) ? "Pas.":"" %>
                <%= documento %>
              </span>
            </div>
            <div class="form-group">
              <label class="control-label oh h22">Dirección</label>
              <span class="control-info"><%= (isEmpty(direccion)) ? "Sin datos" : (direccion + " " + localidad) %></span>
            </div>
            <div class="form-group">
              <label class="control-label oh h22">Teléfonos</label>
              <span class="control-info">
                <%= telefono %> <%= (!isEmpty(telefono_obs)) ? "<span class='text-muted'>("+telefono_obs+")</span>" : "" %>
                <% if (!isEmpty(telefono_2)) { %><br/><%= telefono_2 %> <%= (!isEmpty(telefono_2_obs)) ? "<span class='text-muted'>("+telefono_2_obs+")</span>" : "" %><% } %>
                <% if (!isEmpty(telefono_3)) { %><br/><%= telefono_3 %> <%= (!isEmpty(telefono_3_obs)) ? "<span class='text-muted'>("+telefono_3_obs+")</span>" : "" %><% } %>
                <% if (!isEmpty(telefono_4)) { %><br/><%= telefono_4 %> <%= (!isEmpty(telefono_4_obs)) ? "<span class='text-muted'>("+telefono_4_obs+")</span>" : "" %><% } %>
                <% if (!isEmpty(telefono_5)) { %><br/><%= telefono_5 %> <%= (!isEmpty(telefono_5_obs)) ? "<span class='text-muted'>("+telefono_5_obs+")</span>" : "" %><% } %>
                <% if (!isEmpty(telefono_6)) { %><br/><%= telefono_6 %> <%= (!isEmpty(telefono_6_obs)) ? "<span class='text-muted'>("+telefono_6_obs+")</span>" : "" %><% } %>
              </span>
            </div>
            <div class="form-group mb0 oh">
              <a class="btn estudio <%= (estudio==1)?"btn-success":"btn-default" %> fl" href="javascript:void(0)">
                Estudio
              </a>     
              <a class="btn btn-white fr" href="app/#pres_cliente/<%= id %>">
                <i class="fa fa-pencil m-r-xs"></i>Editar
              </a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <span class="bold negro">Informaci&oacute;n laboral</span>
          </div>
          <% var estado_laboral = (estados_laborales.length > 0) ? estados_laborales[0] : {"ingreso":"Sin datos", id_estado_laboral: 0} %>
          <div class="panel-body acerca_de">
            <div class="form-group">
              <label class="control-label oh h22">Tipo</label>
              <span class="control-info">
                <%= (estado_laboral.id_estado_laboral==1)?"Relacion de dependencia":"" %>
                <%= (estado_laboral.id_estado_laboral==2)?"Monotributo":"" %>
                <%= (estado_laboral.id_estado_laboral==3)?"Monotributista comerciante":"" %>
                <%= (estado_laboral.id_estado_laboral==4)?"Monotributista ambulante":"" %>
                <%= (estado_laboral.id_estado_laboral==5)?"Jubilado":"" %>
                <%= (estado_laboral.id_estado_laboral==6)?"Pensionado":"" %>
                <%= (estado_laboral.id_estado_laboral==7)?"Otro":"" %>
              </span>
            </div>
            <div class="form-group">
              <label class="control-label oh h22">Sueldo actual</label>
              <span class="control-info"><%= estado_laboral.ingreso %></span>
            </div>
            <% if (!isEmpty(fecha_ult_operacion)) { %>
              <div class="form-group">
                <label class="control-label oh h22">&Uacute;ltima actualizaci&oacute;n</label>
                <span class="control-info"><%= fecha_ult_operacion %></span>
              </div>
            <% } %>
            <div class="form-group mb0 tar">
              <a class="btn btn-white" href="app/#pres_cliente/<%= id %>">
                <i class="fa fa-pencil m-r-xs"></i>Editar
              </a>
            </div>
          </div>
        </div>

      </div>

      <div class="col-md-8">
        <div class="panel panel-default mb0">
          <ul class="nav nav-tabs nav-tabs-2" role="tablist">
            <li id="prestamos_link" class="active">
              <a href="#tab1_pres_cliente" role="tab" data-toggle="tab">
                <i class="fa text-warning fa-calendar m-r-xs"></i>
                Pr&eacute;stamos
              </a>
            </li>
            <li id="premios_canjeados_link">
              <a href="#tab3_pres_cliente" role="tab" data-toggle="tab">
                <i class="fa text-success fa-file-text m-r-xs"></i>
                Premios canjeados
              </a>
            </li>
            <li id="seguimiento_link">
              <a href="#tab4_pres_cliente" role="tab" data-toggle="tab">
                <i class="fa text-danger fa-share m-r-xs"></i>
                Seguimiento
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div id="tab1_pres_cliente" class="tab-pane panel-body active">
              <ul class="nav nav-tabs" role="tablist">
                <li class="active">
                  <a href="#tab1_prestamos" role="tab" data-toggle="tab">
                    <i class="fa text-warning fa-calendar m-r-xs"></i>
                    Vigentes
                  </a>
                </li>
                <li>
                  <a href="#tab2_prestamos" role="tab" data-toggle="tab">
                    <i class="fa text-info fa-address-book m-r-xs"></i>
                    Cancelados
                  </a>
                </li>
                <% if (control.check("pres_clientes")>1) { %>
                  <button class="btn fr btn-info nuevo_prestamo">Nuevo préstamo</button>  
                <% } %>
              </ul>
              <div class="tab-content">
                <div id="tab1_prestamos" class="tab-pane panel-body active p0">
                  <div id="pres_cliente_prestamos" class="b-a table-responsive"></div>

                  <div id="pres_cliente_prestamos_habilitar_paralelo" style="display:none">
                    <i class="fa fa-exclamation-triangle text-warning"></i>
                    <span>El cliente puede pedir un prestamo paralelo.</span>
                  </div>

                  <div id="pres_cliente_prestamos_tiene_garante" style="display:none">
                    <i class="fa fa-exclamation-triangle text-warning"></i>
                    <span>El cliente tiene un garante asignado.</span>
                  </div>

                </div>
                <div id="tab2_prestamos" class="tab-pane panel-body p0">
                  <div id="pres_cliente_prestamos_cancelados" class="b-a table-responsive"></div>
                </div>
              </div>
            </div>
            <div id="tab3_pres_cliente" class="tab-pane panel-body">
            </div>
            <div id="tab4_pres_cliente" class="tab-pane panel-body">

              <div id="pres_cliente_crear_consultas"></div>
              <!--
              <div class="panel panel-default mb0">
                <ul class="nav nav-tabs nav-tabs-2" role="tablist">
                  <li class="active">
                    <a id="tab1_link" href="#seguimiento_tab1" role="tab" data-toggle="tab"><i class="fa fa-phone text-muted"></i> Llamada</a>
                  </li>
                  <li>
                    <a id="tab4_link" href="#seguimiento_tab4" role="tab" data-toggle="tab"><i class="fa fa-mobile text-muted"></i> SMS</a>
                  </li>
                  <li>
                    <a id="tab2_link" href="#seguimiento_tab2" role="tab" data-toggle="tab"><i class="fa fa-clock-o text-muted"></i> Alarma</a>
                  </li>
                  <li>
                    <a id="tab3_link" href="#seguimiento_tab3" role="tab" data-toggle="tab"><i class="fa fa-file-text text-muted"></i> Nota</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div id="seguimiento_tab1" class="tab-pane panel-body active">
                    <div class="form-group">
                      <textarea id="consulta_tarea_texto" placeholder="Escribe aqui alguna nota u observacion..." class="form-control no-model h100"></textarea>
                    </div>
                    <div class="form-group clearfix">
                      <button class="btn btn-pd btn-info guardar_sms fr">Guardar</button>
                    </div>
                  </div>
                  <div id="seguimiento_tab2" class="tab-pane panel-body">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="input-group">
                            <input placeholder="Fecha" type="text" class="form-control" id="clientes_fecha_vencimiento" name="fecha_vencimiento"/>
                            <span class="input-group-btn">
                              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                            </span>        
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <textarea id="consulta_tarea_texto" placeholder="Escribe aqui alguna nota u observacion..." class="form-control no-model h100"></textarea>
                    </div>
                    <div class="form-group clearfix">
                      <button class="btn btn-pd btn-info guardar_sms fr">Guardar</button>
                    </div>
                  </div>
                  <div id="seguimiento_tab3" class="tab-pane panel-body">
                    <div class="form-group">
                      <textarea id="consulta_nota" placeholder="Escribe aqui alguna nota u observacion..." class="form-control no-model h100"></textarea>
                    </div>
                    <div class="form-group tar">
                      <button class="btn btn-pd btn-info guardar_nota fr">Guardar</button>
                    </div>
                  </div>
                </div>
              </div>
              -->

            </div>
          </div>
        </div>

        <div id="pres_cliente_consultas">
          
        </div>
        <div style="display: none;" class="streamline b-l b-info m-l-lg m-b padder-v fs14">

          <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
            <i class="fa fa-phone"></i>
          </a>
          <div class="m-l-lg panel b-a">
            <div class="panel-heading clearfix pos-rlt b-b b-light">
              <span class="arrow left"></span>
              <div>
                <div class="pb5">
                  Se llamó por telefono
                  <span class="text-muted fs13 pull-right">
                    <i class="fa fa-clock-o"></i>
                    18/07/2017 a las 10:50
                  </span>
                </div>
              </div>
            </div>
            <div class="panel-body">
              <div class="dt pb5 w100p">
                <div class="dtc vam">
                  <span class="h4 cp ver_compra">Dijo que iba a pasar por el local.</span>
                </div>
              </div>
            </div>
          </div>
          
          <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
            <i class="fa fa-phone"></i>
          </a>
          <div class="m-l-lg panel b-a">
            <div class="panel-heading clearfix pos-rlt b-b b-light">
              <span class="arrow left"></span>
              <div>
                <div class="pb5">
                  Se llamó por telefono
                  <span class="text-muted fs13 pull-right">
                    <i class="fa fa-clock-o"></i>
                    15/07/2017 a las 15:39
                  </span>
                </div>
              </div>
            </div>
            <div class="panel-body">
              <div class="dt pb5 w100p">
                <div class="dtc vam">
                  <span class="h4 cp ver_compra">No atendió nadie. Llamar después.</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      
    </div>

  </div>
</div>
</script>

<script type="text/template" id="prestamos_resultados_template">
  <table id="prestamos_tabla" class="table table-small table-striped sortable m-b-none default footable">
    <thead>
      <tr>
        <th style="width: 20px">#</th>
        <th>Fecha</th>
        <th>Plan</th>
        <th>Monto</th>
        <th>Cuotas</th>
        <th>Estado</th>
        <th>Prox. Venc.</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody class="tbody"></tbody>
  </table>
</script>

<script type="text/template" id="prestamos_item_resultados_template">
  <% cantidad_cuotas_pagas = parseInt(cantidad_cuotas_pagas) %>
  <% cantidad_cuotas = parseInt(cantidad_cuotas) %>
  <td><%= numero %></td>
  <td><%= fecha %></td>
  <td><%= plan %></td>
  <td><%= valor_cuota %></td>
  <td>
    <%= cantidad_cuotas_pagas %>/<%= cantidad_cuotas %>
    <% if (habilitado_renovacion == 1 && deuda_vencida_total == 0) { %>
      <i class="fa fa-exclamation-triangle text-warning habilitado_renovacion" data-toggle="tooltip" title="Habilitado para renovacion" class=""></i>
    <% } %>
  </td>
  <td>
    <% var vencimiento = moment(proximo_vencimiento,"DD/MM/YYYY") %>
    <% if (vencimiento.isBefore(moment())) { %>
      Vencido
    <% } else { %>
      <%= (cantidad_cuotas_pagas < cantidad_cuotas) ? "Vigente":"Cancelado" %>
    <% } %>
  </td>
  <td><%= (cantidad_cuotas_pagas < cantidad_cuotas) ? proximo_vencimiento:"" %></td>
  <td class="tar td_acciones">
    <a href="javascript:void(0)" class="btn btn-white editar"><i class="fa fa-pencil"></i></a>
  </td>
</script>

<script type="text/template" id="prestamo_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7"><%= (id == undefined || id == 0) ? "Nuevo Pr&eacute;stamo" : "Pr&eacute;stamo #"+ numero %></span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <% if (id == undefined) { %>
    <div class="panel-body">
      <div class="row">
        <% if (ID_SUCURSAL == 0) { %>
          <div class="col-md-3">
            <label class="control-label">Caja</label>
            <select class="form-control action no-model" id="prestamo_sucursales">
              <% for(var i=0;i< almacenes.length;i++) { %>
                <% var almacen = almacenes[i] %>
                <option value="<%= almacen.id %>"><%= almacen.nombre %></option>
              <% } %>
            </select>
          </div>
        <% } %>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Plan</label>
            <select class="form-control" name="id_plan" id="prestamo_planes_credito"></select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Monto a prestar</label>
            <input type="text" name="monto_prestado" id="prestamo_monto_prestado" value="<%= monto_prestado %>" class="form-control"/>
          </div>
        </div>
        <% if (saldo_renovacion != 0) { %>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Saldo Renovacion</label>
              <input type="text" disabled id="prestamo_saldo_renovacion" value="<%= saldo_renovacion %>" class="form-control"/>
            </div>
          </div>
        <% } %>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Cuotas</label>
            <select id="prestamo_cantidad_cuotas" name="cantidad_cuotas" class="form-control"></select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Valor de cuota</label>
            <input type="text" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %> name="valor_cuota" id="prestamo_valor_cuota" value="<%= valor_cuota %>" class="form-control no-model"/>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Fecha Otorgacion</label>
            <div class="input-group">
              <input type="text" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %> name="fecha" id="prestamo_fecha" value="<%= fecha %>" class="form-control no-model"/>
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Primera cuota</label>
            <div class="input-group">
              <input type="text" id="prestamo_primera_cuota" class="form-control no-model" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %>/>
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label">Garante</label>
        <div class="input-group">
          <input type="hidden" id="prestamo_garante_id"/>
          <input type="text" id="prestamo_garante" name="garante" class="form-control"/>
          <span class="input-group-btn">
            <a tabindex="-1" type="button" href="app/#pres_garante" target="_blank" class="btn btn-info"><i class="fa fa-plus"></i></a>
          </span>        
        </div>
      </div>
      <div class="form-group">
        <label class="control-label">Observaciones</label>
        <textarea class="form-control" id="prestamo_observaciones" name="observaciones"></textarea>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn guardar btn-success">Guardar</button>
    </div>
  <% } else { %>
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a href="#tab1_prestamo" role="tab" data-toggle="tab">
          <i class="fa text-warning fa-calendar m-r-xs"></i>
          Cuotas
        </a>
      </li>
      <li>
        <a href="#tab2_prestamo" role="tab" data-toggle="tab">
          <i class="fa text-success fa-file-text m-r-xs"></i>
          Informaci&oacute;n
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1_prestamo" class="tab-pane panel-body active">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Plan</label>
              <input type="text" class="form-control no-model" value="<%= plan %>" disabled />
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Monto prestado</label>
              <input type="text" name="monto_prestado" disabled id="prestamo_monto_prestado" value="<%= monto_prestado %>" class="form-control"/>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Intereses</label>
              <input type="text" disabled id="prestamo_intereses_calculados" value="<%= Number((valor_cuota * cantidad_cuotas)-monto_prestado).toFixed(2) %>" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Total a devolver</label>
              <input type="text" disabled id="prestamo_total_devolver" value="<%= Number((valor_cuota * cantidad_cuotas)).toFixed(2) %>" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Saldo actual</label>
              <input type="text" disabled id="prestamo_saldo_actual" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Capital Pagado</label>
              <input type="text" disabled id="prestamo_monto_pagado_total" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Punitorios Pagados</label>
              <input type="text" disabled id="prestamo_interes_pagado_total" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Total Pagado</label>
              <input type="text" disabled id="prestamo_total_pagado" class="form-control no-model"/>
            </div>
          </div>
        </div>
        <div id="prestamo_cuotas" class="b-a table-responsive"></div>

        <div class="clearfix">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Cobrado</label>
                <input type="text" disabled id="prestamo_total_cobrado" value="<%= sesion_total_cobrado %>" class="form-control no-model"/>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Paga con</label>
                <input type="text" id="prestamo_paga_con" class="form-control no-model"/>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Vuelto</label>
                <input type="text" disabled id="prestamo_vuelto" class="form-control no-model"/>
              </div>
            </div>
          </div>
        </div>
        <% if (PERFIL != 1181) { %>
          <div class="clearfix">
            <button class="btn liquidar_cuotas btn-default">Liquidar cuotas</button>
            <% if (habilitado_renovacion == 1) { %>
              <button class="btn renovar btn-default">Renovar Prestamo</button>
            <% } %>
            <button class="btn imprimir_prestamo btn-default">Imprimir Otorgacion</button>
            <button class="btn imprimir_seleccionadas btn-default">Imprimir Liquidacion</button>
            <% if (control.check("pres_planes_credito") == 3) { %>
              <button class="btn eliminar_prestamo fr btn-danger">Eliminar</button>
            <% } %>
          </div>
        <% } %>
      </div>
      <div id="tab2_prestamo" class="tab-pane panel-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Numero</label>
              <input type="text" id="prestamo_numero" <%= (control.check("pres_planes_credito") == 3)?"":"disabled" %> value="<%= numero %>" name="numero" class="form-control"/>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label">Garante</label>
          <div class="input-group">
            <input type="text" id="prestamo_garante" disabled value="<%= garante %>" name="garante" class="form-control"/>
            <span class="input-group-btn">
              <a tabindex="-1" type="button" href="app/#pres_garante/<%= id_garante %>" target="_blank" class="btn btn-default"><i class="fa fa-search"></i></a>
            </span>        
          </div>
        </div>
        <div class="form-group">
          <label class="control-label">Observaciones</label>
          <textarea class="form-control" id="prestamo_observaciones" name="observaciones"><%= observaciones %></textarea>
        </div>
        <% if (PERFIL != 1181) { %>
          <div class="clearfix tar">
            <button class="btn guardar btn-success">Guardar</button>
          </div>
        <% } %>
      </div>
    </div>
  <% } %>
</div>
</script>

<script type="text/template" id="prestamos_cuotas_resultados_template">
  <table id="prestamos_cuotas_tabla" class="table table-small table-striped sortable m-b-none default footable">
    <thead>
      <tr>
        <th class="w25"></th>
        <th class="w25">#</th>
        <th>Vencimiento</th>
        <th>Estado</th>
        <th>Total Cuota</th>
        <th>Capital</th>
        <th>Interes</th>
        <th>Canc. Cuota</th>
        <th>Int. Punit.</th>
        <th>Ult. Pago</th>
        <th>Saldo</th>
        <th class="th_acciones"></th>
      </tr>
    </thead>
    <tbody class="tbody"></tbody>
  </table>
</script>

<script type="text/template" id="prestamos_cuotas_item_resultados_template">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row check_cuota" data-saldo="<%= saldo %>" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="data"><%= numero %></td>
  <td class="data"><%= fecha_vencimiento %></td>
  <td class="data">
    <%= (estado == 0) ? "Vigente" : "" %>
    <%= (estado == 1) ? "Pagado" : "" %>
    <%= (estado == 2) ? "Parcial" : "" %>
  </td>
  <% var monto_f = parseFloat(monto) %>
  <% monto_f = (isNaN(monto_f)) ? 0 : monto_f %>
  <% var interes_f = parseFloat(interes) %>
  <% interes_f = (isNaN(interes_f)) ? 0 : interes_f %>
  <td class="data"><b><%= Number(monto_f + interes_f).toFixed(2) %></b></td>
  <td class="data"><%= capital_cuota %></td>
  <td class="data"><%= interes_cuota %></td>
  <td class="data"><%= monto_pagado %></td>
  <td class="data"><%= interes %></td>
  <td class="data"><%= fecha_pago %></td>
  <td class="data"><%= saldo %></td>
  <td class="tar td_acciones">
    <% if (estado > 0 && PERFIL != 1181) { %>
      <button title="Imprimir" class="btn btn-white btn-sm imprimir_cuota"><i class="fa fa-print"></i></button>
      <button title="Facturar cuota" class="btn btn-white btn-sm <%= (id_factura != 0)?"active":"" %> facturar_cuota"><i class="fa fa-file-text"></i></button>
    <% } %>
  </td>
</script>

<script type="text/template" id="prestamo_cuota_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7">Cuota #<%= numero %></span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <ul class="nav nav-tabs nav-tabs-2" role="tablist">
    <li id="prestamos_link" class="active">
      <a href="#tab1_cuota" role="tab" data-toggle="tab">
        <i class="fa text-success fa-dollar m-r-xs"></i>
        Pagos
      </a>
    </li>
    <li id="seguimiento_link">
      <a href="#tab2_cuota" role="tab" data-toggle="tab">
        <i class="fa text-warning fa-search m-r-xs"></i>
        Observaciones
      </a>
    </li>
  </ul>
  <div class="tab-content">
    <div id="tab1_cuota" class="tab-pane panel-body active">
      <div class="row">
        <div class="col-md-8">
          <div class="row">
            <div class="col-md-4 pr0">
              <div class="form-group">
                <label class="control-label">Cuota Capital</label>
                <input type="text" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %> id="prestamo_cuota_monto" name="monto" value="<%= Number(monto).toFixed(2) %>" class="form-control"/>
              </div>
            </div>
            <div class="col-md-4 pr0">
              <div class="form-group">
                <label class="control-label">Cuota Intereses</label>
                <input type="text" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %> id="prestamo_cuota_interes" name="interes" value="<%= Number(interes).toFixed(2) %>" class="form-control"/>
              </div>
            </div>
            <div class="col-md-4 pr0">
              <div class="form-group dn">
                <label class="control-label">Cuota Total</label>
                <input type="text" disabled id="prestamo_cuota_total" value="<%= Number(total).toFixed(2) %>" class="form-control"/>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Vencimiento Cuota</label>
            <div class="input-group">
              <input type="text" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %> id="prestamo_cuota_fecha_vencimiento" value="<%= fecha_vencimiento %>" class="form-control"/>
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="row">
            <div class="col-md-4 pr0">
              <div class="form-group">
                <label class="control-label">Saldo Capital</label>
                <input type="text" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %> id="prestamo_cuota_saldo_capital" name="saldo_capital" value="<%= Number(saldo_capital).toFixed(2) %>" class="form-control"/>
              </div>
            </div>
            <div class="col-md-4 pr0">
              <div class="form-group">
                <label class="control-label">Saldo Intereses</label>
                <input type="text" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %> id="prestamo_cuota_saldo_interes" name="saldo_interes" value="<%= Number(saldo_interes).toFixed(2) %>" class="form-control"/>
              </div>
            </div>
            <div class="col-md-4 pr0">
              <div class="form-group">
                <label class="control-label">Saldo Total</label>
                <input type="text" disabled name="saldo" id="prestamo_cuota_saldo" value="<%= saldo %>" class="form-control bold"/>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Estado</label>
            <select class="form-control" id="prestamo_cuota_estado" name="estado" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %>>
              <option value="0" <%= (estado==0)?"selected":"" %>>Vigente</option>
              <option value="2" <%= (estado==2)?"selected":"" %>>Pago Parcial</option>
              <option value="1" <%= (estado==1)?"selected":"" %>>Pagada</option>
            </select>
          </div>
        </div>
        <div class="col-xs-12">
          <div class="line b-b m-b"></div>
          <div id="prestamo_cuota_pagos"></div>
        </div>
      </div>    
    </div>
    <div id="tab2_cuota" class="tab-pane panel-body">
      <textarea class="form-control h100" id="prestamo_cuota_observaciones" placeholder="Escriba aqui alguna observacion o nota..." name="observaciones"><%= observaciones %></textarea>
    </div>
  </div>
  <div class="panel-footer clearfix tar">
    <span class="fl">
      <span>Anterior:</span>
      <span id="sesion_total_cobrado">$ <%= Number(sesion_total_cobrado).toFixed(2) %></span>
      <span class="ml15 bold">Total:</span>
      <span class="bold" id="sesion_total">$ <%= Number(sesion_total_cobrado).toFixed(2) %></span>
    </span>
    <% if (PERFIL != 1181) { %>
      <button class="btn guardar btn-success">Guardar</button>
    <% } %>
  </div>
</div>
</script>


<script type="text/template" id="prestamos_cuotas_pagos_resultados_template">
  <div class="row">
    <div class="col-xs-12">
      <h3 class="h4 m-b">Detalle de Pagos:</h3>
    </div>
    <% if (PERFIL != 1181) { %>
      <div class="col-md-2 pr0">
        <div class="form-group">
          <label class="control-label">Fecha de pago</label>
          <div class="input-group">
            <input placeholder="Fecha" type="text" class="form-control no-model" id="prestamos_cuotas_pagos_fecha"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>        
          </div>
        </div>
      </div>
      <div class="col-md-10">
        <div class="row">
          <div class="col-md-2 pl0 pr0">
            <label class="control-label">Pago</label>
            <input type="text" id="prestamos_cuotas_pagos_monto" class="form-control no-model"/>
          </div>
          <div class="col-md-3 pl0 pr0">
            <div class="row">
              <div class="col-xs-6 pl0 pr0">
                <label class="control-label"><input type="checkbox" class="mr5" id="prestamos_cuotas_pagos_aplica_descuento"/>Dto <input type="text" value="5" class="form-control fr w50 pl5 ml5 pr5" id="prestamos_cuotas_pagos_descuento_porcentaje" disabled style="padding-top: 0px; padding-bottom: 0px; height: 23px;" /></label>
                <input type="text" id="prestamos_cuotas_pagos_descuento" disabled class="form-control no-model"/>
              </div>
              <div class="col-xs-6 pl0 pr0">
                <label class="control-label">Efectivo</label>
                <input type="text" id="prestamos_cuotas_pagos_pago_efectivo" disabled class="form-control no-model"/>
              </div>
            </div>
          </div>
          <div class="col-md-2 pl0 pr0">
            <label class="control-label">Capital</label>
            <input type="text" value="" id="prestamos_cuotas_pagos_capital" class="form-control no-model" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %>/>
          </div>
          <div class="col-md-2 pl0 pr0">
            <label class="control-label">Intereses</label>
            <input type="text" value="" id="prestamos_cuotas_pagos_interes" class="form-control no-model" <%= (control.check("pres_planes_credito")>2)?"":"disabled" %>/>
          </div>
          <div class="col-md-2 pl0 pr0">
            <label class="control-label">Caja</label>
            <select class="form-control action no-model" id="prestamos_cuotas_pagos_sucursales">
              <% for(var i=0;i< almacenes.length;i++) { %>
                <% var almacen = almacenes[i] %>
                <option value="<%= almacen.id %>" <%= (ID_SUCURSAL == almacen.id)?"selected":"" %>><%= almacen.nombre %></option>
              <% } %>
            </select>
          </div>
          <div class="col-md-1 pl0 pr0">
            <label class="control-label">&nbsp;</label>
            <button tabindex="-1" type="button" class="btn btn-success btn-default btn-block agregar_pago">Agregar</button>
          </div>
        </div>
      </div>
    <% } %>
  </div>
  <div class="b-a table-responsive">
    <table id="prestamos_cuotas_pagos_tabla" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Efectivo</th>
          <th>Descuento</th>
          <th>Pago Total</th>
          <th>Capital</th>
          <th>Interes</th>
          <th class="w25"></th>
          <th class="w25"></th>
          <th class="w25"></th>
        </tr>
      </thead>
      <tbody class="tbody"></tbody>
      <tfoot>
        <tr>
          <td></td>
          <td id="prestamos_cuotas_pagos_total_pagado"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</script>

<script type="text/template" id="prestamos_cuotas_pagos_item_resultados_template">
  <td class="data"><%= fecha %></td>
  <td class="data"><%= Number(monto-descuento).toFixed(2) %></td>
  <td class="data"><%= Number(descuento).toFixed(2) %></td>
  <td class="data"><%= Number(monto).toFixed(2) %></td>
  <td class="data"><%= Number(cancelacion_capital).toFixed(2) %></td>
  <td class="data"><%= Number(cancelacion_interes).toFixed(2) %></td>
  <% if (PERFIL == 1181) { %>
    <td colspan=3></td>
  <% } else { %>
    <td>
      <% if (id != 0) { %>
        <i title="<%= (id_factura != 0)?"Ver factura":"Generar factura" %>" class="fa cp fa-file-o facturar_pago <%= (id_factura != 0)?"text-info active":"" %>"></i>
      <% } %>
    </td>  
    <td>
      <% if (id != 0) { %>
        <i class="fa cp fa-print imprimir_pago"></i>
      <% } %>
    </td>
    <td>
      <% if (id == 0) { %>
        <i class="fa fa-times text-danger eliminar_pago"></i>
      <% } else { %>
        <i class="fa fa-times text-danger borrar_pago"></i>
      <% } %>
    </td>
  <% } %>
</script>


<script type="text/template" id="estados_laborales_resultados_template">
  <table id="estados_laborales_tabla" class="table table-small table-striped sortable m-b-none default footable">
    <thead>
      <tr>
        <th>Tipo</th>
        <th>Fecha inicio</th>
        <th>Fecha fin</th>
        <th>Ingreso</th>
        <th class="th_acciones w50"></th>
      </tr>
    </thead>
    <tbody class="tbody"></tbody>
  </table>
</script>

<script type="text/template" id="estados_laborales_item_resultados_template">
  <td class="text-info data">
    <%= (id_estado_laboral==1)?"Relacion de dependencia":"" %>
    <%= (id_estado_laboral==2)?"Monotributo":"" %>
    <%= (id_estado_laboral==3)?"Monotributista comerciante":"" %>
    <%= (id_estado_laboral==4)?"Monotributista ambulante":"" %>
    <%= (id_estado_laboral==5)?"Jubilado":"" %>
    <%= (id_estado_laboral==6)?"Pensionado":"" %>
    <%= (id_estado_laboral==7)?"Otro":"" %>
  </td>
  <td class="data"><%= fecha_inicio %></td>
  <td class="data"><%= fecha_fin %></td>
  <td class="data"><%= ingreso %></td>
  <td class="tar td_acciones">
    <% if (PERFIL != 1181) { %>
      <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
    <% } else { %>
      <button class="btn btn-white data"><i class="fa fa-search"></i></button>
    <% } %>
  </td>
</script>

<script type="text/template" id="estado_laboral_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7">Editar estado laboral</span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Estado</label>
          <select class="form-control" name="id_estado_laboral" id="estado_laboral_tipos_estados_laborales" <%= (!edicion)?"disabled":"" %>>
            <option <%= (id_estado_laboral == 1)?"selected":"" %> value="1">Relacion de dependencia</option>
            <option <%= (id_estado_laboral == 2)?"selected":"" %> value="2">Monotributista domestico</option>
            <option <%= (id_estado_laboral == 3)?"selected":"" %> value="3">Monotributista comerciante</option>
            <option <%= (id_estado_laboral == 4)?"selected":"" %> value="4">Monotributista ambulante</option>
            <option <%= (id_estado_laboral == 5)?"selected":"" %> value="5">Jubilado</option>
            <option <%= (id_estado_laboral == 6)?"selected":"" %> value="6">Pensionado</option>
            <option <%= (id_estado_laboral == 7)?"selected":"" %> value="7">Otro</option>
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Fecha de inicio</label>
          <div class="input-group">
            <input placeholder="Fecha" type="text" class="form-control" id="estado_laboral_fecha_inicio" name="fecha_inicio" <%= (!edicion)?"disabled":"" %>/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>        
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Fecha de fin</label>
          <div class="input-group">
            <input placeholder="Fecha" type="text" class="form-control" id="estado_laboral_fecha_fin" name="fecha_fin" <%= (!edicion)?"disabled":"" %>/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>        
          </div>
        </div>
      </div>
    </div>
    <div class="campos campos_relacion_dependencia">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label class="control-label">Empresa</label>
            <input type="text" class="form-control" value="<%= empresa %>" name="empresa" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Empresa CUIT</label>
            <input type="text" class="form-control" value="<%= empresa_cuit %>" name="empresa_cuit" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Telefono 1</label>
            <input type="text" class="form-control" value="<%= telefono_1 %>" name="telefono_1" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Telefono 2</label>
            <input type="text" class="form-control" value="<%= telefono_2 %>" name="telefono_2" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Horario</label>
            <input type="text" class="form-control" value="<%= empresa_horario %>" name="empresa_horario" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label class="control-label">Empresa Direccion</label>
            <input type="text" class="form-control" value="<%= empresa_direccion %>" name="empresa_direccion" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Cargo</label>
            <input type="text" class="form-control" value="<%= empresa_cargo %>" name="empresa_cargo" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Seccion</label>
            <input type="text" class="form-control" value="<%= empresa_seccion %>" name="empresa_seccion" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Nro. Legajo</label>
            <input type="text" class="form-control" value="<%= empresa_legajo %>" name="empresa_legajo" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Ingreso mensual</label>
            <input type="text" name="ingreso" value="<%= ingreso %>" class="form-control" <%= (!edicion)?"disabled":"" %>/>
          </div>
        </div>
      </div>
    </div>
    <div class="campos campos_jubilados">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Institucion</label>
            <input type="text" class="form-control" value="<%= institucion %>" name="institucion" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Nro. Beneficio</label>
            <input type="text" class="form-control" value="<%= numero_beneficio %>" name="numero_beneficio" <%= (!edicion)?"disabled":"" %>>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Ingreso mensual</label>
            <input type="text" name="ingreso" value="<%= ingreso %>" class="form-control" <%= (!edicion)?"disabled":"" %>/>
          </div>
        </div>
      </div>
    </div>
    <div class="campos campos_monotributo">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Categoria</label>
            <input type="text" name="categoria_monotributo" value="<%= categoria_monotributo %>" class="form-control" <%= (!edicion)?"disabled":"" %>/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Ingreso mensual</label>
            <input type="text" name="ingreso" value="<%= ingreso %>" class="form-control" <%= (!edicion)?"disabled":"" %>/>
          </div>
        </div>
      </div>
    </div>
    <div class="campos campos_otros">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Ingreso mensual</label>
            <input type="text" name="ingreso" value="<%= ingreso %>" class="form-control" <%= (!edicion)?"disabled":"" %>/>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label">Observaciones</label>
      <textarea class="form-control" id="estado_laboral_observaciones" name="observaciones" <%= (!edicion)?"disabled":"" %>><%= observaciones %></textarea>
    </div>
  </div>
  <% if (PERFIL != 1181) { %>
    <div class="panel-footer clearfix tar">
      <button class="btn guardar btn-success">Guardar</button>
    </div>
  <% } %>
</div>
</script>

<script type="text/template" id="documentaciones_resultados_template">
  <div class="b-a table-responsive">
    <table id="documentaciones_tabla" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th>Tipo de documento</th>
          <th>Fecha</th>
          <th>Archivo</th>
          <th class="w20"></th>
          <th class="th_acciones w50"></th>
        </tr>
      </thead>
      <tbody class="tbody"></tbody>
    </table>
  </div>
</script>

<script type="text/template" id="documentaciones_item_resultados_template">
  <td class="text-info data"><%= documentacion %></td>
  <td class="data"><%= fecha %></td>
  <td>
    <% if (!isEmpty(path_documentacion)) { %>
      <a class="text-info" target="_blank" href="/sistema/<%= path_documentacion %>">Ver archivo</a>
    <% } %>
  </td>
  <td class="data">
    <% if (!isEmpty(observaciones)) { %>
      <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-commenting text-warning"></i>
    <% } %>
  </td>
  <td class="tar td_acciones">
    <% if (PERFIL != 1181) { %>
      <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
    <% } else { %>
      <button class="btn btn-white data"><i class="fa fa-search"></i></button>
    <% } %>    
  </td>
</script>

<script type="text/template" id="documentacion_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7">Editar documentacion</span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Tipo</label>
          <select class="form-control" name="id_documentacion" id="documentacion_tipos_documentaciones" <%= (!edicion)?"disabled":"" %>>
            <% for(var i=0;i< pres_documentaciones.length;i++) { %>
              <% var o = pres_documentaciones[i] %>
              <option <%= (o.id == id_documentacion)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
            <% } %>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Fecha</label>
          <div class="input-group">
            <input placeholder="Fecha" type="text" class="form-control" id="documentacion_fecha" name="fecha" <%= (!edicion)?"disabled":"" %>/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>        
          </div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <% if (PERFIL != 1181) { %>
        <?php
        single_file_upload(array(
          "name"=>"path_documentacion",
          "label"=>"",
          "url"=>"/sistema/pres_clientes/function/save_file/",
        )); ?>
      <% } else { %>
        <% if (!isEmpty(path_documentacion)) { %>
          <a href="/sistema/<%= path_documentacion %>" target="_blank">
            <img src="/sistema/<%= path_documentacion %>" style="max-width: 80px" />
          </a>
        <% } %>
      <% } %>
    </div>
    <div class="form-group">
      <label class="control-label">Observaciones</label>
      <textarea class="form-control" id="documentacion_observaciones" name="observaciones" <%= (!edicion)?"disabled":"" %>><%= observaciones %></textarea>
    </div>
  </div>
  <% if (PERFIL != 1181) { %>
    <div class="panel-footer clearfix tar">
      <button class="btn guardar btn-success">Guardar</button>
    </div>
  <% } %>
</div>
</script>


<script type="text/template" id="prestamo_simulador_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7">Simulador de Prestamos</span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Plan</label>
          <select class="form-control" name="id_plan" id="prestamo_simulador_planes_credito"></select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Monto a prestar</label>
          <input type="text" name="monto_prestado" id="prestamo_simulador_monto_prestado" value="<%= monto_prestado %>" class="form-control"/>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Cuotas</label>
          <select id="prestamo_simulador_cantidad_cuotas" name="cantidad_cuotas" class="form-control"></select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Valor de cuota</label>
          <input type="text" disabled name="valor_cuota" id="prestamo_simulador_valor_cuota" value="<%= valor_cuota %>" class="form-control no-model"/>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Fecha Otorgacion</label>
          <div class="input-group">
            <input type="text" disabled name="fecha" id="prestamo_simulador_fecha" value="<%= fecha %>" class="form-control no-model"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Primera cuota</label>
          <div class="input-group">
            <input type="text" id="prestamo_simulador_primera_cuota" class="form-control no-model" disabled />
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="prestamo_facturar_cuota_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Facturar Cuota</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <div class="form-group">
          <label class="control-label">Sucursal</label>
          <select class="form-control action no-model" id="prestamo_facturar_cuota_sucursales">
            <% for(var i=0;i< almacenes.length;i++) { %>
              <% var almacen = almacenes[i] %>
              <option value="<%= almacen.id %>" <%= (id_sucursal == almacen.id)?"selected":"" %>><%= almacen.nombre %></option>
            <% } %>
          </select>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="liquidar_cuotas_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Liquidar Cuotas</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <div class="form-group">
          <label class="control-label">Saldo</label>
          <input type="text" id="liquidar_cuotas_saldo" value="<%= saldo %>" class="form-control" name="saldo"/>
        </div>
        <div class="form-group">
          <label class="control-label">Caja</label>
          <select class="form-control action no-model" id="liquidar_cuotas_sucursales">
            <% for(var i=0;i< almacenes.length;i++) { %>
              <% var almacen = almacenes[i] %>
              <option value="<%= almacen.id %>" <%= (ID_SUCURSAL == almacen.id)?"selected":"" %>><%= almacen.nombre %></option>
            <% } %>
          </select>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="calculadora_prestamos_template">
  <div style="position: absolute; z-index: 999999; top: 0px; left: 0px; width: 180px" class="calculadora_prestamos">
    <div class="panel panel-default">
      <div class="panel-heading"><b>Calculadora</b></div>
      <div class="panel-body">
        <div class="form-group mb5">
          <label class="control-label">Total a pagar</label>
          <input type="text" value="<%= (typeof window.total_a_pagar == "undefined") ? Number(0).toFixed(2) : Number(window.total_a_pagar).toFixed(2) %>" class="form-control" id="calculadora_prestamos_total_pagar"/>
        </div>
        <div class="form-group mb5">
          <label class="control-label">Paga con</label>
          <input type="text" class="form-control" id="calculadora_prestamos_paga_con"/>
        </div>
        <div class="form-group mb5">
          <label class="control-label">Vuelto</label>
          <input type="text" class="form-control" id="calculadora_prestamos_vuelto" disabled />
        </div>
        <div class="form-group mb5">
          <button class="btn btn-default btn-block" id="calculadora_prestamos_limpiar">Limpiar</button>
        </div>
      </div>
    </div>
  </div>
</script>