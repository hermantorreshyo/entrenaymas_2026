<script type="text/template" id="clientes_panel_template">
<% if (seleccionar) { %>
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-xs-12 sm-m-b">
          <div class="input-group">
            <input type="text" id="clientes_buscar" value="<%= window.clientes_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="clientes_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;"></th>
              <th class="sorting" data-sort-by="nombre"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
<% } else { %>
  <div class="seccion_vacia" style="display:none">
    <h1 class="h1"><?php echo lang(array("es"=>"Todav&iacute;a no ten&eacute;s ning&uacute;n","en"=>"You still do not have any")); ?> <%= (ID_PROYECTO==3)?"inquilino":"<?php echo lang(array("es"=>"cliente","en"=>"customers")); ?>" %></h1>
    <h3 class="h3"><?php echo lang(array("es"=>"Para crear tu primer cliente, hace click en el siguiente bot&oacute;n","en"=>"To create your first client, click on the following button")); ?></h3>
    <div class="list-icon">
      <a href="app/#cliente"><i class="icon-note"></i></a>
    </div>
    <% if (control.check("clientes")>1) { %>
      <div>
        <a class="btn btn-lg btn-info btn-addon" href="app/#cliente">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</span>
        </a>
      </div>
    <% } %>
    <p>
      <?php echo lang(array("es"=>"Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click ","en"=>"If you need some help, please communicate with us ")); ?>
      <a class="text-info">
        <?php echo lang(array("es"=>"acá!","en"=>"here!")); ?>
      </a>
    </p>
  </div>
  <div class="seccion_llena" style="display:none">
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i><%= modulo.title %></h1>
    </div>

    <div class="wrapper-md ng-scope">
      <div class="panel panel-default">

        <?php 
        // CONTACTOS
        // =======================
        ?>
        <% if (vista_contactos) { %>
          <ul class="nav nav-tabs nav-tabs-2" role="tablist">
            <% if (ID_EMPRESA == 259) { %>
              <li class="<%= (window.clientes_tipo == 1) ? "active":"" %>">
                <a href="javascript:void(0)" class="cambiar_tab" data-tipo="1" role="tab" data-toggle="tab">Contactos</a>
              </li>
              <li class="<%= (window.clientes_tipo == 3) ? "active":"" %>">
                <a href="javascript:void(0)" class="cambiar_tab" data-tipo="3" role="tab" data-toggle="tab">Salon Comercial</a>
              </li>
              <li class="<%= (window.clientes_tipo == 4) ? "active":"" %>">
                <a href="javascript:void(0)" class="cambiar_tab" data-tipo="4" role="tab" data-toggle="tab">Conferencias</a>
              </li>
              <li class="<%= (window.clientes_tipo == 5) ? "active":"" %>">
                <a href="javascript:void(0)" class="cambiar_tab" data-tipo="5" role="tab" data-toggle="tab">Staff</a>
              </li>
              <li class="<%= (window.clientes_tipo == 6) ? "active":"" %>">
                <a href="javascript:void(0)" class="cambiar_tab" data-tipo="6" role="tab" data-toggle="tab">Expositores</a>
              </li>
              <li class="<%= (window.clientes_tipo == 7) ? "active":"" %>">
                <a href="javascript:void(0)" class="cambiar_tab" data-tipo="7" role="tab" data-toggle="tab">Conferencistas</a>
              </li>
            <% } else { %>
              <% if (MILLING == 1) { %>
                <li class="<%= (window.clientes_tipo == 3) ? "active":"" %>">
                  <a href="javascript:void(0)" class="cambiar_tab" data-tipo="3" role="tab" data-toggle="tab">Commercial</a>
                </li>
                <li class="<%= (window.clientes_tipo == 5) ? "active":"" %>">
                  <a href="javascript:void(0)" class="cambiar_tab" data-tipo="5" role="tab" data-toggle="tab">Interested in product</a>
                </li>              
                <li class="<%= (window.clientes_tipo == 4) ? "active":"" %>">
                  <a href="javascript:void(0)" class="cambiar_tab" data-tipo="4" role="tab" data-toggle="tab">Contact with Team</a>
                </li>
                <li class="<%= (window.clientes_tipo == 2) ? "active":"" %>">
                  <a href="javascript:void(0)" class="cambiar_tab" data-tipo="2" role="tab" data-toggle="tab">Digital Subscribers</a>
                </li>
                <li class="<%= (window.clientes_tipo == 6) ? "active":"" %>">
                  <a href="javascript:void(0)" class="cambiar_tab" data-tipo="6" role="tab" data-toggle="tab">Newsletter</a>
                </li>
                <% if (ID_EMPRESA == 448 || ID_EMPRESA == 520 || ID_EMPRESA == 493) { %>
                  <li class="<%= (window.clientes_tipo == 8) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="8" role="tab" data-toggle="tab">Webinar</a>
                  </li>
                  <li class="<%= (window.clientes_tipo == 9) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="9" role="tab" data-toggle="tab">Test</a>
                  </li>
                <% } %>
              <% } else { %>
                <% if (ID_PROYECTO == 3) { %>
                  <li class="<%= (window.clientes_custom_3 == 1) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="-1" data-custom_3="1" role="tab" data-toggle="tab"><i class="fa fa-envelope text-info"></i> Contactos</a>
                  </li>
                  <li class="<%= (window.clientes_custom_4 == 1) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="-1" data-custom_4="1" role="tab" data-toggle="tab"><i class="fa fa-key text-warning"></i> Inquilinos</a>
                  </li>
                  <li class="<%= (window.clientes_custom_5 == 1) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="-1" data-custom_5="1" role="tab" data-toggle="tab"><i class="fa fa-home text-success"></i> Propietarios</a>
                  </li>
                <% } else { %>
                  <li class="<%= (window.clientes_tipo == -1) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="-1" role="tab" data-toggle="tab"><?php echo lang(array("es"=>"Todos","en"=>"All")); ?></a>
                  </li>
                  <li class="<%= (window.clientes_tipo == 1) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="1" role="tab" data-toggle="tab"><i class="fa fa-clock-o text-warning"></i> <?php echo lang(array("es"=>"A contactar","en"=>"To contact")); ?></a>
                  </li>
                  <li class="<%= (window.clientes_tipo == 2) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="2" role="tab" data-toggle="tab"><i class="fa fa-check text-info"></i> <?php echo lang(array("es"=>"En progreso","en"=>"In progress")); ?></a>
                  </li>
                  <li class="<%= (window.clientes_tipo == 0) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="0" role="tab" data-toggle="tab"><i class="fa fa-thumbs-up text-success"></i> <?php echo lang(array("es"=>"Con venta","en"=>"With sales")); ?></a>
                  </li>
                  <li class="<%= (window.clientes_tipo == 3) ? "active":"" %>">
                    <a href="javascript:void(0)" class="cambiar_tab" data-tipo="3" role="tab" data-toggle="tab"><i class="fa fa-thumbs-down text-danger"></i> <?php echo lang(array("es"=>"Sin venta","en"=>"Without sales")); ?></a>
                  </li>
                <% } %>
              <% } %>
            <% } %>
          </ul>
          <div class="tab-content">
            <div id="tab1" class="tab-pane active">

              <div class="panel-heading clearfix">
                <div class="row">
                  <div class="col-md-8 sm-m-b">
                    <div class="input-group">
                      <input type="text" id="clientes_buscar" value="<%= window.clientes_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
                      <span class="input-group-btn">
                        <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                      </span>

                      <% if (ID_EMPRESA != 571) { %>
                        <span class="input-group-btn">
                          <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
                        </span>
                      <% } %>

                      <% if (ID_EMPRESA == 114) { %>

                        <span class="input-group-btn">
                          <div class="btn-group dropdown ml5">
                            <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                              <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Operaciones","en"=>"Operaciones")); ?></span>
                            </button>
                            <ul class="dropdown-menu pull-right">
                              <li><a href="javascript:void(0)" class="modificar_cuentas">Modificar Cuentas</a></li>
                              <li class="divider"></li>
                              <li><a href="javascript:void(0)" class="importar_cuentas">Importar Cuentas</a></li>
                              <li><a href="javascript:void(0)" class="importar_mora">Importar Mora</a></li>
                            </ul>
                          </div>
                        </span>

                      <% } else { %>

                        <% if (permiso == 3 && MILLING == 0) { %>

                          <span class="input-group-btn">
                            <div class="btn-group dropdown ml5">
                              <button class="btn btn-default btn-addon btn-addon-2 exportar_excel">
                                <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                              </button>
                            </div>
                          </span>

                          <?php /*
                          <span class="input-group-btn">
                            <div class="btn-group dropdown ml5">
                              <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-download"></i><span><?php echo lang(array("es"=>"Importar","en"=>"Import")); ?></span>
                              </button>
                              <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0)" class="importar_excel">Excel</a></li>
                                <li><a href="javascript:void(0)" class="importar_csv">Archivo Texto</a></li>
                                <% if (typeof PERCIBE_IB != "undefined" && PERCIBE_IB == 1) { %>
                                  <li class="divider"></li>
                                  <li><a href="clientes/function/actualizar_padron/" target="_blank">Actualizar padron</a></li>
                                <% } %>
                              </ul>
                            </div>
                          </span>
                          */ ?>

                        <% } else if (MILLING == 1) { %>
                          <span class="input-group-btn">
                            <div class="btn-group dropdown ml5">
                              <button class="btn btn-default btn-addon btn-addon-2 exportar_excel">
                                <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                              </button>
                            </div>
                          </span>
                        <% } %>

                      <% } %>

                    </div>
                  </div>
                  <div class="col-md-4 text-right">
                    <% if (permiso > 1 && MILLING == 0) { %>
                      <a style="<%= (ID_PROYECTO == 3 && window.clientes_custom_3 == 1) ? "display:inline-block":"display:none" %>" class="btn btn-info nuevo_cliente btn-addon ml5" href="javascript:void(0)">
                        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nueva Consulta","en"=>"New Contact")); ?>&nbsp;&nbsp;</span>
                      </a>
                      <a style="<%= (ID_PROYECTO == 3 && window.clientes_custom_4 == 1) ? "display:inline-block":"display:none" %>" class="btn btn-info nuevo_inquilino btn-addon ml5" href="javascript:void(0)">
                        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo Inquilino","en"=>"New Contact")); ?>&nbsp;&nbsp;</span>
                      </a>
                      <a style="<%= (ID_PROYECTO == 3 && window.clientes_custom_5 == 1)?"display:inline-block":"display:none" %>" class="btn btn-info nuevo_propietario btn-addon ml5" href="javascript:void(0)">
                        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo Propietario&nbsp;&nbsp;</span>
                      </a>
                    <% } %>
                  </div>
                </div>
              </div>
              <% if (ID_PROYECTO == 3) { %>
                <div class="advanced-search-div bg-light dk" style="<%= (window.clientes_codigo_propiedad != 0) ? "display:block" : "display:none" %>">
                  <div class="wrapper oh">
                    <h4 class="m-t-xs"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
                    <div class="cb">
                      <div class="form-group fl" style="width: 200px; display: inline-block">
                        <input type="text" placeholder="Código Propiedad" value="<%= window.clientes_codigo_propiedad %>" class="input form-control no-model" id="clientes_codigo_propiedad"/>
                      </div>
                      <div class="form-group dib fl">
                        <button class="btn buscar btn-default ml10"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
                      </div>
                    </div>
                  </div>
                </div>
              <% } %>
              <div class="panel-body">
                <div class="b-a table-responsive">
                  <table id="clientes_table" class="table table-striped sortable m-b-none default footable">
                    <thead>
                      <tr>
                        <th style="width:20px;"></th>
                        <th class="w50 tac hidden-xs"></th>
                        <th class="sorting" data-sort-by="nombre"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></th>
                        <% if (vista_contactos) { %>
                          <th class="w25"></th>
                        <% } %>
                        <th class="col-xxs-0 sorting" data-sort-by="C.fecha_ult_operacion">
                          <% if (ID_EMPRESA == 220) { %>
                            Vencimiento
                          <% } else { %>
                            <?php echo lang(array("es"=>"Ult. Consulta","en"=>"Date")); ?>
                          <% } %>
                        </th>
                        <th class="col-xxs-0 sorting" data-sort-by="email">Email</th>
                        <% if (vista_contactos && MILLING == 0) { %>
                          <th class="w120"><?php echo lang(array("es"=>"Estado","en"=>"Status")); ?></th>
                        <% } %>
                        <% if (permiso > 1) { %>
                          <th class="th_acciones w120"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?></th>
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
        <?php
        // CLIENTES
        // ================= ?>
        <% } else { %>
          
          <?php $active = "clientes"; include("cli/clientes_menu.php"); ?>

          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-8 sm-m-b">
                <div class="input-group">
                  <input type="text" id="clientes_buscar" value="<%= window.clientes_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
                  <span class="input-group-btn">
                    <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                  </span>
                  <span class="input-group-btn">
                    <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
                  </span>

                  <% if (ID_EMPRESA == 114) { %>

                    <span class="input-group-btn">
                      <div class="btn-group dropdown ml5">
                        <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Operaciones","en"=>"Operaciones")); ?></span>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          <li><a href="javascript:void(0)" class="modificar_cuentas">Modificar Cuentas</a></li>
                          <li class="divider"></li>
                          <li><a href="javascript:void(0)" class="importar_cuentas">Importar Cuentas</a></li>
                          <li><a href="javascript:void(0)" class="importar_mora">Importar Mora</a></li>
                        </ul>
                      </div>
                    </span>

                  <% } else { %>

                    <% if (MILLING == 0) { %>
                      <span class="input-group-btn">
                        <div class="btn-group dropdown ml5">
                          <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                          </button>
                          <ul class="dropdown-menu pull-right">
                            <li><a href="javascript:void(0)" class="exportar_excel">Excel</a></li>
                          </ul>
                        </div>
                      </span>
                    <% } %>

                  <% } %>
                </div>
              </div>
              <div class="col-md-4 text-right">
                <% if (control.check("clientes")>1) { %>
                  <a class="btn btn-info nuevo_cliente btn-addon ml5" href="javascript:void(0)">
                    <i class="fa fa-plus"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo Cliente","en"=>"New Client")); ?>&nbsp;&nbsp;</span>
                  </a>
                <% } %>
              </div>
            </div>
          </div>
          <div class="advanced-search-div bg-light dk" style="<%= (window.clientes_id_vendedor != 0 && ID_VENDEDOR != 0) ? "display:block" : "display:none" %>">
            <div class="wrapper oh">
              <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
              <div class="row pl10 pr10">
                <% if (ID_EMPRESA != 571) { %>
                  <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                    <div class="form-group">
                      <select id="clientes_vendedores" class="w100p form-control no-model">
                        <% if (ID_VENDEDOR == 0 || ID_EMPRESA == 287) { %>
                          <option value="0">Vendedor</option>
                        <% } %>
                        <% for(var i=0;i< vendedores.length;i++) { %>
                          <% var o = vendedores[i]; %>
                          <% if (control.check("vendedores") < 3 && ID_EMPRESA != 287) { %>
                            <% if (ID_VENDEDOR == o.id) { %>
                              <option value="<%= o.id %>"><%= o.nombre %></option>
                            <% } %>
                          <% } else { %>
                            <option <%= (o.id == window.clientes_id_vendedor)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                          <% } %>
                        <% } %>                   
                      </select>
                    </div>
                  </div>
                <% } %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="clientes_etiquetas" class="w100p form-control no-model"></select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <button class="btn buscar btn-default btn-dark btn-block"><i class="fa fa-search m-r-xs"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body">
            <div class="b-a table-responsive">
              <table id="clientes_table" class="table <%= (ID_EMPRESA == 70)?'table-small':'' %> table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th style="width:20px;"></th>
                    <th class="w50 tac hidden-xs"></th>
                    <th class="sorting" data-sort-by="nombre"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></th>
                    <% if (ID_PROYECTO == 1) { %>
                      <th class="col-xxs-0 sorting" data-sort-by="codigo"><?php echo lang(array("es"=>"C&oacute;digo","en"=>"Code")); ?></th>
                    <% } %>
                    <% if (MILLING == 1) { %>
                      <th class="col-xxs-0 sorting" data-sort-by="contacto_telefono"><?php echo lang(array("es"=>"Tel&eacute;fono","en"=>"Telephone")); ?></th>
                      <th class="col-xxs-0 sorting" data-sort-by="contacto_email">Email</th>
                    <% } else { %>
                      <% if (control.check("vendedores") > 0) { %>
                        <th class="col-xxs-0 sorting" data-sort-by="vendedor">Vendedor</th>
                      <% } %>
                      <th class="col-xxs-0"><?php echo lang(array("es"=>"Contacto","en"=>"Contact")); ?></th>
                      <th class="col-xxs-0 sorting" data-sort-by="email">Email</th>
                    <% } %>
                    <% if (!vista_contactos && control.check("cuentas_corrientes_clientes") > 0) { %>
                      <th class="col-xxs-0 w70"></th>
                    <% } %>
                    <% if (TOQUE == 1) { %>
                      <th class="col-xxs-0 sorting" data-sort-by="saldo_inicial">Saldo</th>
                      <% if (PERFIL == 660) { %>
                        <th class="col-xxs-0 w70"></th>
                      <% } %>
                    <% } %>
                    <% if (permiso > 1) { %>
                      <th class="th_acciones w120"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?></th>
                    <% } %>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot class="pagination_container hide-if-no-paging"></tfoot>
              </table>
            </div>
          </div>
        <% } %>
      </div>
    </div>
  </div>
<% } %>
</script>


<script type="text/template" id="clientes_item">
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
      <% if (path.indexOf("http") == 0) { %>
        <img src="<%= path %>" class="customcomplete-image"/>
      <% } else { %>
        <img src="/sistema/<%= path %>" class="customcomplete-image"/>
      <% } %>
    <% } else { %>
      <span class="avatar xs avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %> pull-left">
        <%= isEmpty(nombre) ? email.substr(0,1).toUpperCase() : nombre.substr(0,1).toUpperCase() %>
      </span>
    <% } %>
    </td>
  <% } %> 
  <td class='data'>
    <% if (isEmpty(nombre)) { %>
      <span class="capitalize <%= (activo==1)?'text-info':'text-muted' %>"><%= email %></span>
    <% } else { %>
      <span class="capitalize <%= (activo==1)?'text-info':'text-muted' %>"><%= nombre.ucwords() %>
        <%= (ID_EMPRESA == 259) ? direccion : "" %>
      </span>
    <% } %>
    <% if (TOQUE == 1) { %>
      <br/>ID: <%= id %>
    <% } %>
    <% if (ID_PROYECTO != 1) { %>
      <% if (!isEmpty(codigo)) { %><br/><span>Cod: <%= codigo %></span><% } %>
    <% } %>    
    <% if (etiquetas.length > 0 || vista_contactos) { %>
      <div class="clearfix">
        <% if (etiquetas.length > 0) { %>
          <% for(var j=0;j< etiquetas.length; j++) { %>
            <% var etiq = etiquetas[j] %>
            <span class="label bg-info pull-left m-t-xs m-r-xs"><%= etiq.nombre %></span>
          <% } %>
        <% } %>
        <% if (vista_contactos) { %>
          <% if (!isEmpty(observaciones)) { %>
            <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-comments pull-left m-l-xs text-default"></i>
          <% } %>
          <div class="calification-container pull-left"></div>
        <% } %>
      </div>
    <% } %>
  </td>
  <% if (vista_contactos) { %>
    <td class="pl0 pr0">
      <% if ((typeof tarea_asignada != undefined) && tarea_asignada == 1) { %>
        <a href="app/#tareas">
          <i data-toggle="tooltip" title='<%= (typeof tarea_titulo != undefined) ? tarea_titulo : "" %>' class='fa fs18 text-danger fa-calendar pull-right'></i>
        </a>
      <% } %>    
    </td>
  <% } %>
  <% if (!seleccionar) { %>
    <% if (vista_contactos) { %>
      <td class="data col-xxs-0 <%= clase %>">
        <span>
          <% if (ID_EMPRESA == 220) { %>
            <%= (fecha_vencimiento == "00/00/0000 00:00:00") ? "" : mostrar_fecha_completa(fecha_vencimiento) %>
          <% } else { %>
            <%= (fecha_ult_operacion == "00/00/0000 00:00:00") ? "" : mostrar_fecha_completa(fecha_ult_operacion) %>
          <% } %>
        </span>
        <%= ((typeof respondido != undefined) && respondido == 1 && (typeof respondido_por != undefined) && !isEmpty(respondido_por) ) ? "<i data-toggle='tooltip' title='Respondido por: "+respondido_por+"' class='fa fa-share m-l'></i>" : "" %>

        <% if (id_origen == 30 || id_origen == 31) { %>
          <br/><span class="label bg-success">Whatsapp</span>
        <% } else if (ID_PROYECTO == 3) { %>
          <br/>
          <% if (propiedad_id_tipo_operacion == 1) { %>
            <span class="label bg-danger"><%= propiedad_tipo_operacion %></span>
          <% } else if (propiedad_id_tipo_operacion == 2) { %>
            <span class="label bg-warning"><%= propiedad_tipo_operacion %></span>
          <% } else { %>
            <span class="label bg-light dk">General</span>
          <% } %>
        <% } %>

      </td>
    <% } else { %>
      <% if (ID_PROYECTO == 1) { %>
        <td class="data col-xxs-0 <%= clase %>"><span><%= codigo %></span></td>
      <% } %>
      <% if (control.check("vendedores") > 0) { %>
        <td class="data col-xxs-0 <%= clase %>"><span><%= vendedor %></span></td>
      <% } %>
      <% if (MILLING == 1) { %>
        <td class="data col-xxs-0 <%= clase %>"><span><%= (isEmpty(contacto_telefono))?"—":contacto_telefono %></span></td>
      <% } else if (ID_EMPRESA == 571) { %>
        <td class="data col-xxs-0 <%= clase %>"><span><%= (isEmpty(telefono))?"—":telefono+celular %></span></td>
      <% } else { %>
        <td class="data col-xxs-0 <%= clase %>">
          <% if (!isEmpty(telefono)) { %>
            <a data-toggle="tooltip" title="+<%= fax %> <%= telefono %>" class="enviar_whatsapp" href="javascript:void(0)"><i class="fa fa-whatsapp iconito active success"></i></a>
            <a class="enviar_whatsapp pr t-5 ml5 text-info" href="javascript:void(0)"><%= (isEmpty(telefono))?"—":("+"+fax+" "+telefono) %></a>
          <% } %>
          <% if (!isEmpty(direccion)) { %>
            <br/><%= direccion %>
          <% } %>
        </td>
      <% } %>
    <% } %>
    <% if (MILLING == 1) { %>
      <td class="data col-xxs-0 <%= clase %>"><span class="text-info"><%= (isEmpty(contacto_email))?"—":contacto_email.toLowerCase() %></span></td>
    <% } else { %>
      <% if (ID_EMPRESA != 70) { %>
        <td class="data col-xxs-0 <%= clase %>">
          <% if (ID_PROYECTO != 1) { %>
            <% if (!isEmpty(email)) { %>
              <span class="text-info"><%= email.toLowerCase() %></span>
            <% } %>
          <% } else { %>
            <span class="text-info"><%= (isEmpty(email))?"—":email.toLowerCase() %></span>
          <% } %>
        </td>
      <% } %>
    <% } %>

    <% if (!vista_contactos && control.check("cuentas_corrientes_clientes") > 0) { %>
      <td class="p5"><a class="btn btn-success btn-xs" href="app/#cuentas_corrientes_clientes/<%= id %>">Cuenta Cte.</a></td>
    <% } else if (TOQUE == 1) { %>
      <td>$ <%= saldo_inicial %></td>
      <% if (PERFIL == 660) { %>
        <td class="p5"><a class="btn btn-success btn-xs" href="app/#cuenta_cliente/<%= id %>">Cuenta Cte.</a></td>
      <% } %>
    <% } %>

  <% } %>
  <% if (vista_contactos) { %>
    <% if (MILLING == 0) { %>
      <td class="pl0 pr0">
        <% if (tipo == 0) { %>
          <button class="btn btn-sm btn-success btn-addon btn-addon2 <%= (activo==0)?'inactive':'' %>"><i class="fa fa-thumbs-up"></i><?php echo lang(array("es"=>"Con venta","en"=>"With sales")); ?></button>
        <% } else if (tipo == 1) { %>
          <button class="btn btn-sm btn-warning btn-addon btn-addon2 <%= (activo==0)?'inactive':'' %>"><i class="fa fa-clock-o"></i><?php echo lang(array("es"=>"A contactar","en"=>"To contact")); ?></button>
        <% } else if (tipo == 2) { %>
          <button class="btn btn-sm btn-info btn-addon btn-addon2 <%= (activo==0)?'inactive':'' %>"><i class="fa fa-check"></i><?php echo lang(array("es"=>"En progreso","en"=>"In progress")); ?></button>
        <% } else if (tipo == 3) { %>
          <button class="btn btn-sm btn-danger btn-addon btn-addon2 <%= (activo==0)?'inactive':'' %>"><i class="fa fa-thumbs-down"></i><?php echo lang(array("es"=>"Sin venta","en"=>"Without sales")); ?></button>
        <% } %>
      </td>
    <% } %>
  <% } %>
  <% if (permiso > 1) { %>
    <td class="<%= clase %> td_acciones">
      <% if (ID_EMPRESA != 341) { %> 
        <i title="Activo" data-toggle="tooltip" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <% } %>

      <% if (typeof CANASTA_BASICA != "undefined" && CANASTA_BASICA == 1) { %>
        <i data-toggle="tooltip" title="Canasta Basica" class="fa fa-shopping-basket iconito canasta_basica <%= (custom_5 == 1)?"active":"" %>"></i>
      <% } %>
      
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <% if (ID_EMPRESA == 341) { %>
            <% if (activo==1) { %>
              <li><a class="activar_laboral_gym" data-activo="0" href="javascript:void(0)">Desactivar</a></li>
            <% } else { %>
              <li><a class="activar_laboral_gym" data-activo="1" href="javascript:void(0)">Activar</a></li>
            <% } %>
          <% } %>
          <% if (permiso == 3) { %>
            <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
          <% } %>
        </ul>
      </div>  
    </td>
  <% } %>
</script>

<script type="text/template" id="clientes_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>
    <% if (MILLING == 1 || ID_EMPRESA == 571) { %>
      <%= modulo.title %>
      / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
    <% } else { %>
      Contactos / <%= modulo.title %>
      / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
    <% } %>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <?php 
    if ($empresa->id == 70) {
      include("cli/clientes_detalle_70.php"); 
    } else if ($empresa->id == 341) {
      include("cli/clientes_detalle_341.php"); 
    } else if ($empresa->id == 571) {
      include("cli/clientes_detalle_571.php"); 
    } else if (isset($milling) && $milling == 1) { 
      include("cli/clientes_detalle_256.php"); 
    } else if ($empresa->id_proyecto == 3){
      include("cli/clientes_detalle_3.php");
    } else {
      include("cli/clientes_detalle.php"); 
    } 
    ?>
    <% if (edicion) { %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1 tar">
          <div class="line b-b m-b-lg"></div>
          <button class="btn guardar btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
        </div>
      </div>
    <% } %>
  </div>
</div>
</script>

<script type="text/template" id="clientes_edit_mini_panel_template">
<div class="panel pb0 mb0">
  <div class="panel-body">
    
    <div class="form-group">
      <input type="text" autocomplete="off" placeholder="Nombre" name="nombre" class="tab form-control" id="clientes_mini_nombre"/>
    </div>
    <div class="form-group">
      <input type="text" autocomplete="off" name="email" placeholder="Email" class="form-control" id="clientes_mini_email"/>
    </div>

    <% if (ID_EMPRESA == 1354) { %>

      <div class="form-group">
        <input type="text" autocomplete="off" placeholder="Direccion" name="direccion" class="tab form-control" id="clientes_mini_direccion"/>
      </div>        
      <div class="form-group">
        <input type="text" autocomplete="off" name="telefono" placeholder="Telefono" class="form-control" id="clientes_mini_telefono"/>
      </div>    
      <div class="form-group">
        <input type="text" autocomplete="off" name="observaciones" name="observaciones" placeholder="Zona" class="form-control" id="clientes_mini_observaciones"/>
      </div>

    <% } else { %>

      <% if (tipo_formulario == "contacto") { %>

        <div class="form-group">
          <button class="btn btn-default ver_avanzadas btn-block">Ver m&aacute;s</button>
        </div>
        <div id="clientes_edit_mini_avanzadas" style="display: none;">
          <div class="form-group">
            <input type="text" autocomplete="off" name="telefono" placeholder="Telefono" class="form-control" id="clientes_mini_telefono"/>
          </div>    
          <div class="form-group">
            <input type="text" autocomplete="off" placeholder="Direccion" name="direccion" class="tab form-control" id="clientes_mini_direccion"/>
          </div>
        </div>

      <% } else { %>

        <div class="form-group">
          <select class="tab form-control tab" name="tipo_iva" id="clientes_mini_tipo_iva">
            <option <%= (id_tipo_iva == 1) ? "selected":"" %> value="1">Responsable Inscripto</option>
            <option <%= (id_tipo_iva == 2) ? "selected":"" %> value="2">Monotributo</option>
            <option <%= (id_tipo_iva == 3) ? "selected":"" %> value="3">Exento</option>
            <option <%= (id_tipo_iva == 4) ? "selected":"" %> value="4">Consumidor Final</option>
          </select>    
        </div>
        <div class="row pl10 pr10">
          <div class="col-sm-6 pr5 pl5">
            <div class="form-group">
              <select class="form-control tab" name="id_tipo_documento" id="clientes_mini_tipo_documento">
                <option <%= (id_tipo_documento == 99) ? "selected":"" %> value="99">Sin identificacion</option>
                <option <%= (id_tipo_documento == 96) ? "selected":"" %> value="96">DNI</option>
                <option <%= (id_tipo_documento == 80) ? "selected":"" %> value="80">CUIT</option>
                <option <%= (id_tipo_documento == 86) ? "selected":"" %> value="86">CUIL</option>
                <option <%= (id_tipo_documento == 89) ? "selected":"" %> value="89">Libreta Enrolamiento</option>
                <option <%= (id_tipo_documento == 90) ? "selected":"" %> value="90">Libreta Civica</option>
                <option <%= (id_tipo_documento == 94) ? "selected":"" %> value="94">Pasaporte</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6 pr5 pl5">
            <div class="form-group">
              <input type="text" autocomplete="off" placeholder="Nro. Doc / CUIT" name="cuit" class="tab form-control" id="clientes_mini_cuit"/>
            </div>
          </div>
        </div>
        <div class="form-group">
          <button class="btn btn-default ver_avanzadas btn-block">Ver m&aacute;s</button>
        </div>
        <div id="clientes_edit_mini_avanzadas" style="display: none;">
          <div class="form-group">
            <input type="text" autocomplete="off" placeholder="Direccion" name="direccion" class="tab form-control" id="clientes_mini_direccion"/>
          </div>        
          <div class="form-group">
            <input type="text" autocomplete="off" name="telefono" placeholder="Telefono" class="form-control" id="clientes_mini_telefono"/>
          </div>    
        </div>

      <% } %>

    <% } %>
    
    <div class="form-group">
      <button class="btn guardar btn-success tab btn-block">Guardar</button>
    </div>
  </div>
</div>
</script>



<script type="text/template" id="clientes_timeline_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="<%= clase_modulo %> icono_principal"></i><%= titulo_modulo %>
    / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
  </h1>
</div>
<div class="wrapper-md">
<div class="centrado rform">

    <% if (blur == 2) { %>
      <div class="tac mb30">
        <div class="w100p util-container selected">
          ¡Atencion! La consulta de <%= nombre.ucwords() %> esta oculta para el profesional.<br>
          <button class="mostrar_consultas mt10">Mostrar consultas</button>
        </div>
      </div>
    <% } %>

    <div class="row">

      <div class="col-md-4">

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row tac-xs">
                <div class="col-md-3 col-xs-12">
                  <% if (!isEmpty(path)) { %>
                    <img src="/sistema/<%= path %>" class="customcomplete-image xl"/>
                  <% } else { %>
                    <span class="avatar xl avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %>">
                      <%= isEmpty(nombre) ? email.substr(0,1).toUpperCase() : nombre.substr(0,1).toUpperCase() %>
                    </span>
                  <% } %>
                </div>
                <div class="col-md-9 col-xs-12">
                  <h3 class="m-t-sm m-b-xs"><%= nombre.ucwords() %> </h3>
                  <a class="text-azul fs14">

                    <% if (blur != 1) { %>
                      <%= email.toLowerCase() %>
                    <% } else { %>
                      <span style="color: transparent;text-shadow: 0 0 5px rgba(0,0,0,0.5);">***********</span>
                    <% } %>

                  </a>
                  <div class="calification-container"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <span class="bold negro"><?php echo lang(array("es"=>"Acerca de","en"=>"About ")); ?> <%= nombre.ucwords() %> </span>
          </div>
          <div class="panel-body acerca_de">
            <div class="form-group">
              <label class="control-label"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></label>
              <span class="control-info"><%= nombre.ucwords() %></span>
            </div>
            <div class="form-group">
              <label class="control-label"><?php echo lang(array("es"=>"Teléfono","en"=>"Phone")); ?></label>
              <span class="control-info">+<%= fax %> 
                <% if (blur != 1) { %>
                  <%= telefono %>
                <% } else { %>
                  <span style="color: transparent;text-shadow: 0 0 5px rgba(0,0,0,0.5);">***********</span>
                <% } %>
              </span>
            </div>
            <% if (MILLING == 0) { %>
              <?php /*
              <div class="form-group">
                <div class="btn-group dropdown">
                  <% if (tipo == 0) { %>
                    <button class="btn btn-sm btn-success btn-addon btn-addon2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-thumbs-up"></i><?php echo lang(array("es"=>"Con venta","en"=>"With sales")); ?></button>
                  <% } else if (tipo == 1) { %>
                    <button class="btn btn-sm btn-warning btn-addon btn-addon2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-clock-o"></i><?php echo lang(array("es"=>"A contactar","en"=>"To contact")); ?></button>
                  <% } else if (tipo == 2) { %>
                    <button class="btn btn-sm btn-info btn-addon btn-addon2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-check"></i><?php echo lang(array("es"=>"En progreso","en"=>"In progress")); ?></button>
                  <% } else if (tipo == 3) { %>
                    <button class="btn btn-sm btn-danger btn-addon btn-addon2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-thumbs-down"></i><?php echo lang(array("es"=>"Sin venta","en"=>"Without sales")); ?></button>
                  <% } %>
                  <span class="fs12 m-l-xs"><i class="fa fa-caret-down"></i></span>
                  <ul class="dropdown-menu">
                    <li><a href="javascript:void(0)" class="editar_tipo" data-tipo="1"><?php echo lang(array("es"=>"A contactar","en"=>"To contact")); ?></a></li>
                    <li><a href="javascript:void(0)" class="editar_tipo" data-tipo="2"><?php echo lang(array("es"=>"En progreso","en"=>"In progress")); ?></a></li>
                    <li><a href="javascript:void(0)" class="editar_tipo" data-tipo="0"><?php echo lang(array("es"=>"Con venta","en"=>"With sales")); ?></a></li>
                    <li><a href="javascript:void(0)" class="editar_tipo" data-tipo="3"><?php echo lang(array("es"=>"Sin venta","en"=>"Without sales")); ?></a></li>
                  </ul>
                </div>  
              </div>
              */ ?>
              <% if (PERFIL != 1357 && PERFIL != 1358) { %>
                <div class="form-group mb0 tar">
                  <a class="btn btn-white" href="app/#<%= tipo_cliente %>/<%= id %>">
                  <i class="fa fa-pencil m-r-xs"></i>
                  Editar
                  </a>
                </div>
              <% } %>
            <% } %>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div id="cliente_crear_consultas"></div>
        <div class="streamline b-l b-info m-l-lg m-b padder-v fs14"></div>
      </div>
      
    </div>

  </div>
</div>
</script>