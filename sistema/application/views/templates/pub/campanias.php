<script type="text/template" id="campanias_resultados_template">
<div class="seccion_llena">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("campanias") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
  </div>
  <div class="wrapper-md ng-scope pr30">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-9 sm-m-b">
                <input style="width: 200px; display: inline-block" type="text" id="campanias_buscar" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")) ?>..." autocomplete="off" class="form-control">
                <% if (ID_EMPRESA == 70 && control.check("vendedores")>0) { %>
                  <select style="width: 200px; display: inline-block" class="form-control" id="campanias_vendedores">
                    <option value="0">Vendedor</option>
                    <% for(var i=0;i< vendedores.length;i++) { %>
                      <% var o = vendedores[i]; %>
                      <% if (control.check("vendedores") < 3) { %>
                        <% if (ID_VENDEDOR == o.id) { %>
                          <option value="<%= o.id %>"><%= o.nombre %></option>
                        <% } %>
                      <% } else { %>
                        <option value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    <% } %>
                  </select>
                <% } %>
                <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </div>
              <% if (!seleccionar) { %>
                <div class="col-md-6 col-lg-3 text-right">
                  <a class="btn btn-info btn-addon ml5 nuevo" href="javascript:void(0)">
                    <i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nueva","en"=>"New")) ?>&nbsp;&nbsp;
                  </a>
                </div>
              <% } %>
            </div>
          </div>
          <div class="advanced-search-div bg-light dk" style="display:none">
            <div class="wrapper clearfix">
              <h4 class="m-t-xs"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"B&uacute;squeda Avanzada:","en"=>"Advanced Search:")) ?></h4>
              <div class="form-inline">
                <?php /*
                <div style="display: inline-block; width: 200px;">
                  <select class="w100p form-control" id="campanias_clientes"></select>
                </div>  
                */ ?>              
                <div style="display: inline-block; width: 200px;">
                  <select class="w100p form-control no-model" id="campanias_estado">
                    <option value=""><?php echo lang(array("es"=>"Estado","en"=>"State")) ?></option>
                    <option value="A"><?php echo lang(array("es"=>"Activas","en"=>"Active")) ?></option>
                    <option value="P"><?php echo lang(array("es"=>"Pendientes","en"=>"Pending")) ?></option>
                    <option value="I"><?php echo lang(array("es"=>"Suspendidas","en"=>"Suspending")) ?></option>
                    <option value="F"><?php echo lang(array("es"=>"Finalizadas","en"=>"Finished")) ?></option>
                    <option value="T"><?php echo lang(array("es"=>"Todas","en"=>"All")) ?></option>
                  </select>
                </div>

                <div style="display: inline-block; width: 200px;">
                  <select class="w100p form-control no-model" id="campanias_prioridad">
                    <option value=""><?php echo lang(array("es"=>"Prioridad","en"=>"Priority")) ?></option>
                    <option value="1"><?php echo lang(array("es"=>"Normal","en"=>"Normal")) ?></option>
                    <option value="0"><?php echo lang(array("es"=>"Baja","en"=>"Low")) ?></option>
                    <option value="2"><?php echo lang(array("es"=>"Alta","en"=>"Hight")) ?></option>
                  </select>
                </div>

                <div style="display: inline-block; width: 180px;">
                  <select id="campanias_categorias" class="form-control w100p no-model">
                    <option value="0">Tipo Publicidad</option>
                    <% for(var i=0;i< window.categorias_publicidades.length;i++) { %>
                      <% var o = categorias_publicidades[i]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>                            
                  </select>
                </div>

                <div class="input-group" style="width: 80px;">
                  <input type="text" placeholder="Hora Inicio" id="campanias_hora_desde" class="form-control">
                </div>
                <div class="input-group" style="width: 80px;">
                  <input type="text" placeholder="Hora Fin" id="campanias_hora_hasta" class="form-control">
                </div>
                <div class="btn-group dropdown">
                  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span>Dias de la semana</span>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="campanias_dias_check" value="lunes"><i></i>Lunes
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="campanias_dias_check" value="martes"><i></i>Martes
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="campanias_dias_check" value="miercoles"><i></i>Miercoles
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="campanias_dias_check" value="jueves"><i></i>Jueves
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="campanias_dias_check" value="viernes"><i></i>Viernes
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="campanias_dias_check" value="sabado"><i></i>S&aacute;bado
                        </label>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <label class="i-checks">
                          <input type="checkbox" class="campanias_dias_check" value="domingo"><i></i>Domingo
                        </label>
                      </a>
                    </li>
                  </ul>
                </div>

                <button class="btn buscar btn-default"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")) ?></button>
              </div>
            </div>
          </div>
        
          <div class="panel-body">
              <div class="b-a table-responsive" style="max-height: 500px; overflow: auto">
              <table id="campanias_tabla" class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <% if (!seleccionar) { %>
                        <th style="width:20px;">
                            <label class="i-checks m-b-none">
                                <input class="esc sel_todos" type="checkbox"><i></i>
                            </label>
                        </th>
                      <% } else { %>
                        <th style="width:20px;"></th>
                      <% } %>
                      <th class="sorting" data-sort-by="nombre"><?php echo lang(array("es"=>"Nombre","en"=>"Name")) ?></th>
                      <th class="sorting" data-sort-by="cliente"><?php echo lang(array("es"=>"Cliente","en"=>"Client")) ?></th>
                      <% if (ID_EMPRESA == 70) { %>
                        <th>F</th>
                        <th>R</th>
                        <th>Z</th>
                        <th>M</th>
                        <th class="sorting" data-sort-by="vendedor">Vendedor</th>
                      <% } %>
                      <th class="sorting" data-sort-by="P.valida_desde"><?php echo lang(array("es"=>"Fecha Alta","en"=>"Start date")) ?></th>
                      <th class="sorting" data-sort-by="P.valida_hasta"><?php echo lang(array("es"=>"Fecha Venc.","en"=>"Due date")) ?></th>
                      <th class="sorting" data-sort-by="dias_vencimiento"><?php echo lang(array("es"=>"Dias p/venc.","en"=>"Days remaining")) ?></th>
                      <th class="sorting" data-sort-by="costo"><?php echo lang(array("es"=>"Costo","en"=>"Cost")) ?></th>
                      <% if (!seleccionar) { %>
                        <th class="th_acciones" style="width: 70px;"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")) ?></th>
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

<script type="text/template" id="campanias_item_resultados_template">
<% var clase = (estado=="A") ? ( (dias_vencimiento > -10 && dias_vencimiento <= 0) ? "fila_alerta" : ((dias_vencimiento > 0) ? "fila_roja":"" )) :"fila_inactiva"; %>
<% if (seleccionar) { %>
  <td class="<%= clase %>">
    <label class="i-checks m-b-none">
      <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
    </label>
  </td>
<% } else { %>
  <td class="<%= clase %>">
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>    
<% } %>
<!--
<td class="<%= clase %> data">
  <% if (!isEmpty(path)) { %>
    <div class="customcomplete-image-sm" style="background-position: top center; background-image: url('/sistema/<%= path %>'); background-size: cover"></div>
  <% } %>
</td>
-->
<td class="<%= clase %> data"><span class="text-info"><%= nombre %></span></td>
<td class="<%= clase %> data"><%= cliente %></td>
<% if (ID_EMPRESA == 70) { %>
  <td><%= fullscreen %></td>
  <td><%= mediana_destacada %></td>
  <td><%= fija_abajo %></td>
  <td><%= fija_medio %></td>
  <td class="<%= clase %> data"><%= vendedor %></td>
<% } %>
<td class="<%= clase %> data"><%= valida_desde.substr(0,10) %></td>  
<td class="<%= clase %> data"><%= valida_hasta.substr(0,10) %></td>  
<td class="<%= clase %> data"><%= (estado=="A") ? ((dias_vencimiento > 0) ? dias_vencimiento+" <?php echo lang(array("es"=>"dias vencidos","en"=>"days expired")); ?>" : ((dias_vencimiento == 0) ? "<?php echo lang(array("es"=>"Vence hoy","en"=>"Expire today")); ?>" : Math.abs(dias_vencimiento)+" <?php echo lang(array("es"=>"dias para vencer","en"=>"days to expire")); ?>") ): ((estado=="P")?"<?php echo lang(array("es"=>"Pendiente","en"=>"Pending")); ?>":"<?php echo lang(array("es"=>"Suspendida","en"=>"Suspending")); ?>") %></td>
<td class="<%= clase %> data">$ <%= costo %></td>  
<% if (!seleccionar) { %>
  <td class="tar <%= clase %>">
    <div class="btn-group dropdown">
      <i title="Opciones" class="iconito bg-light fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")) ?></a></li>
      </ul>
    </div>        
  </td>      
<% } %>
</script>

<script type="text/template" id="campania_template">
<div class="panel panel-default">
  <div class="panel-heading bold">
    <%= (id == undefined) ? "<?php echo lang(array("es"=>"Nueva campa&ntilde;a","en"=>"New Campaign")) ?>" : nombre %>
    <span class="fr"><%= (typeof id != "undefined") ? "ID: "+id : "" %>
  </div>
  <div class="panel-body">
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#tab_camp1" role="tab" data-toggle="tab"><i class="fa fa-info"></i><?php echo lang(array("es"=>"Informaci&oacute;n","en"=>"Information")) ?></a>
        </li>
        <li>
          <a href="#tab_camp5" role="tab" data-toggle="tab"><i class="fa fa-puzzle-piece"></i><?php echo lang(array("es"=>"Piezas","en"=>"Advertising Pieces")) ?></a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="tab_camp1" class="tab-pane active panel-body pt0">
          <div class="row">
            <% if (MILLING == 1) { %>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Nombre de campa&ntilde;a","en"=>"Campaign name")) ?></label>
                    <input type="text" required name="nombre" id="campania_nombre" value="<%= nombre %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Cliente","en"=>"Client")) ?></label>
                    <select id="campania_clientes" class="form-control"></select>
                  </div>
                </div>
              </div>
            <% } else { %>
              <div class="form-group">
                <label class="control-label"><?php echo lang(array("es"=>"Nombre de campa&ntilde;a","en"=>"Campaign name")) ?></label>
                <input type="text" required name="nombre" id="campania_nombre" value="<%= nombre %>" class="form-control"/>
              </div>
              <div class="row">
                <% if (control.check("clientes")>0 && ID_EMPRESA != 1129) { %>
                  <div class="col-md-6 pr0">
                    <div class="form-group">
                      <label class="control-label"><?php echo lang(array("es"=>"Cliente","en"=>"Client")) ?></label>
                      <div class="input-group">
                        <select id="campania_clientes" style="width: 100%" class="form-control"></select>
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-success nuevo_cliente"><?php echo lang(array("es"=>"Nuevo","en"=>"New")) ?></button>
                        </div>
                      </div>
                    </div>
                  </div>
                <% } %>
                <% if (ID_EMPRESA == 70) { %>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Vendedor </label>
                      <select class="w100p form-control" id="campania_vendedores">
                        <option value="0">-</option>
                        <% for(var i=0;i< vendedores.length;i++) { %>
                          <% var o = vendedores[i]; %>
                          <% if (control.check("vendedores") < 3) { %>
                            <% if (ID_VENDEDOR == o.id) { %>
                              <option value="<%= o.id %>"><%= o.nombre %></option>
                            <% } %>
                          <% } else { %>
                            <option <%= (o.id==id_vendedor)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                          <% } %>
                        <% } %>										
                      </select>
                    </div>
                  </div>
                <% } %>
              </div>   
            <% } %>               
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Desde","en"=>"Start date")) ?></label>
                  <input type="text" name="valida_desde" id="campania_valida_desde" value="<%= valida_desde %>" class="form-control"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Hasta","en"=>"End date")) ?></label>
                  <input type="text" name="valida_hasta" id="campania_valida_hasta" value="<%= valida_hasta %>" class="form-control w150"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Costo","en"=>"Cost")) ?></label>
                  <input type="text" name="costo" id="campania_costo" value="<%= costo %>" class="form-control"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Estado","en"=>"State")) ?></label>
                  <select name="estado" id="campania_estado" class="form-control">
                    <option <%= (estado=="A")?"selected":"" %> value="A"><?php echo lang(array("es"=>"Activa","en"=>"Active")) ?></option>
                    <option <%= (estado=="P")?"selected":"" %> value="P"><?php echo lang(array("es"=>"Pendiente","en"=>"Pending")) ?></option>
                    <option <%= (estado=="I")?"selected":"" %> value="I"><?php echo lang(array("es"=>"Suspendida","en"=>"Suspending")) ?></option>
                    <option <%= (estado=="F")?"selected":"" %> value="F"><?php echo lang(array("es"=>"Finalizada","en"=>"Finished")) ?></option>
                  </select>
                </div>
              </div>
            </div>
            <% if (id == undefined && ID_EMPRESA == 70) { %>
              <div class="form-group">
                <label class="i-checks m-b-none">
                  <input id="campania_primer_pago" name="primer_pago" class="" type="checkbox"><i></i>
                  Primer mes pago
                </label>
                <label class="i-checks m-b-none m-l-lg">
                  <input id="campania_pago_unico" name="pago_unico" class="" type="checkbox"><i></i>
                  Pago &uacute;nico (no mensual)
                </label>
              </div>
            <% } %>
          </div>
        </div>
        <div id="tab_camp5" class="tab-pane panel-body pt0">
          <div class="row">
            <div class="form-group">
              <button class="btn btn-info nueva_pieza"><?php echo lang(array("es"=>"Agregar","en"=>"Add")) ?></button>
            </div>
            <div class="b-a table-responsive">
              <table id="campania_piezas" class="table table-small table-striped m-b-none default footable">
                <thead>
                  <tr>
                    <th><?php echo lang(array("es"=>"Nombre","en"=>"Name")) ?></th>
                    <th><?php echo lang(array("es"=>"Tipo","en"=>"Type")) ?></th>
                    <th><?php echo lang(array("es"=>"Desde","en"=>"Start date")) ?></th>
                    <th><?php echo lang(array("es"=>"Hasta","en"=>"End date")) ?></th>
                    <% if (ID_EMPRESA == 70) { %>
                      <th class="w80"><?php echo lang(array("es"=>"Horas","en"=>"Hours")) ?></th>
                      <th class="w50"><?php echo lang(array("es"=>"Dias","en"=>"Days")) ?></th>
                    <% } %>
                    <th style="width: 20px;"></th>
                  </tr>
                </thead>
                <tbody class="tbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")) ?></button>
    <img src="/sistema/resources/images/ajax-loader.gif" class="img_loading"/>
  </div>
</div>
</script>





<script type="text/template" id="piezas_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><?php echo lang(array("es"=>"Piezas publicitarias","en"=>"Advertising Pieces")) ?></h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="pull-left pl15">
                <div style="width: 250px; display: inline-block">
                  <select id="piezas_buscar_publicidades" class="w100p"></select>
                </div>
                <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
              </div>
              <% if (!seleccionar) { %>
                <div class="pull-right pr15">
                  
                  <a class="btn btn-success nuevo btn-addon ml5" href="javascript:void(0)">
                    <i class="fa fa-plus"></i><span class="hidden-xs"><?php echo lang(array("es"=>"Nuevo","en"=>"New")) ?></span>
                  </a>
                  
                  <div class="btn-group dropdown ml5">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <span><?php echo lang(array("es"=>"Acciones","en"=>"Actions")) ?></span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li><a href="javascript:void" class="eliminar_lote"><?php echo lang(array("es"=>"Exportar Excel","en"=>"Export to Excel")) ?></a></li>
                      <li><a href="javascript:void" class="eliminar_lote"><?php echo lang(array("es"=>"Imprimir","en"=>"Print")) ?></a></li>
                    </ul>
                  </div>                  
                  
                </div>
              <% } %>
            </div>
          </div>
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="piezas_tabla" class="table table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="sorting" data-sort-by="valida_desde"><?php echo lang(array("es"=>"Desde","en"=>"From date")) ?></th>
                      <th class="sorting" data-sort-by="valida_hasta"><?php echo lang(array("es"=>"Hasta","en"=>"To date")) ?></th>
                      <% if (!seleccionar) { %>
                        <th style="width:150px;text-align:right"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")) ?></th>
                      <% } %>
                    </tr>
                  </thead>
                  <tbody class="tbody"><tbody>
                  <tfoot class="pagination_container hide-if-no-paging"></tfoot>
                </table>
              </div>
          </div>
          <!--
          <div class="panel-footer clearfix bg-light lter">
            <button class="btn btn-info enviar btn-addon pull-left"><i class="icon fa fa-send"></i>Enviar</button>
          </div>
          -->
      </div>
  </div>
</script>

<script type="text/template" id="piezas_item_resultados_template">
    <% var clase = (activo == 0)?"text-muted":"" %>
    <td class="<%= clase %> data"><%= fecha_desde %></td>
    <td class="<%= clase %> data"><%= fecha_hasta %></td>
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <div class="btn-group dropdown">
          <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
          <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")) ?></a></li>
          </ul>
        </div>        
      </td>
    <% } %>
</script>


<script type="text/template" id="pieza_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    <% if (id == undefined) { %>
      <?php echo lang(array("es"=>"Nueva Pieza","en"=>"New piece")) ?>
    <% } else { %>
      <?php echo lang(array("es"=>"Editar Pieza","en"=>"Edit piece")) ?>
    <% } %>
    <span class="fr"><%= (typeof id != "undefined") ? "ID: "+id : "" %>
    <!--<i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>-->
  </div>
  <div class="panel-body">
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i><?php echo lang(array("es"=>"Informaci&oacute;n","en"=>"Information")) ?></a>
        </li>
        <li>
          <a href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-globe"></i><?php echo lang(array("es"=>"Ubicacion","en"=>"Location")) ?></a>
        </li>
        <li>
          <a href="#tab5" role="tab" data-toggle="tab"><i class="fa fa-clock-o"></i><?php echo lang(array("es"=>"Planificaci&oacute;n","en"=>"Schedule")) ?></a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="tab1" class="tab-pane active panel-body pt0">
          <div class="row">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Nombre",
                      "en"=>"Name",
                    )); ?>
                  </label>
                  <input type="text" value="<%= nombre %>" id="pieza_nombre" class="form-control" name="nombre"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Tipo",
                      "en"=>"Type",
                    )); ?>
                  </label>
                  <select id="pieza_categorias" name="id_categoria" class="form-control">
                    <% for(var i=0;i< window.categorias_publicidades.length;i++) { %>
                      <% var o = categorias_publicidades[i]; %>
                      <option value="<%= o.id %>" data-alto="<%= o.alto %>" data-ancho="<%= o.ancho %>" <%= (o.id == id_categoria)?"selected":"" %>><%= o.nombre %></option>
                    <% } %>                            
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label><?php echo lang(array("es"=>"Desde","en"=>"From date")); ?></label>
                  <div class="input-group">
                    <input type="text" placeholder="Desde" id="pieza_fecha_desde" class="form-control"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>                
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><?php echo lang(array("es"=>"Hasta","en"=>"To date")); ?></label>
                  <div class="input-group">
                    <input type="text" placeholder="Hasta" id="pieza_fecha_hasta" class="form-control"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><?php echo lang(array("es"=>"Prioridad","en"=>"Priority")); ?></label>
                  <div class="form-group">
                    <select class="form-control" name="prioridad">
                      <option value="1" <%= (prioridad==1)?"selected":"" %>><?php echo lang(array("es"=>"Normal","en"=>"Normal")); ?></option>
                      <option value="0" <%= (prioridad==0)?"selected":"" %>><?php echo lang(array("es"=>"Baja","en"=>"Low")); ?></option>
                      <option value="2" <%= (prioridad==2)?"selected":"" %>><?php echo lang(array("es"=>"Alta","en"=>"Hight")); ?></option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <?php /*
            <input type="text" value="<%= path %>" id="hidden_path" placeholder="PATH" class="form-control" name="path">
            <input type="text" value="<%= path_2 %>" id="hidden_path_2" placeholder="RESPONSIVE" class="form-control" name="path_2">
            */ ?>
            <div class="row">
              <div class="col-md-9">
                <div class="form-group">
                  <label class="control-label">Link</label>
                  <input type="text" name="link" id="campania_link" value="<%= link %>" class="form-control"/>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Ventana","en"=>"Target")); ?></label>
                  <select class="form-control" id="campania_link_target" name="link_target">
                    <option <%= (link_target=="_blank")?"selected":"" %> value="_blank"><?php echo lang(array("es"=>"Nueva","en"=>"New window")); ?></option>
                    <option <%= (link_target=="")?"selected":"" %> value=""><?php echo lang(array("es"=>"Misma","en"=>"Same window")); ?></option>
                  </select>
                </div>
              </div>
            </div>
            <% if (ID_EMPRESA == 70) { %>
              <div class="form-inline mb10">
                <div class="form-group">
                  <label class="i-checks m-t-xs m-r">
                    <input type="checkbox" id="campania_cerrar" name="cerrar" class="checkbox" value="1" <%= (cerrar == 1)?"checked":"" %> >
                    <i></i>
                    Habilitar cierre
                  </label>
                  <input type="text" class="form-control" id="campania_cerrar_despues" value="<%= cerrar_despues %>"/>
                </div>
              </div>
            <% } %>
            <div class="">
              <?php
              single_file_upload(array(
                "name"=>"path",
                "label"=>lang(array("es"=>"Imagen Principal","en"=>"Main image")),
                "url"=>"campanias/function/save_file/",
              )); 
              if ($empresa->id == 70 || $empresa->id == 283) { 
                single_file_upload(array(
                  "name"=>"path_2",
                  "label"=>lang(array("es"=>"Imagen Responsive","en"=>"Responsive image")),
                  "url"=>"campanias/function/save_file/",
                ));
                single_file_upload(array(
                  "name"=>"path_3",
                  "label"=>lang(array("es"=>"Imagen Fija","en"=>"Fixed Imagen")),
                  "url"=>"campanias/function/save_file/",
                ));
              }
              ?>
            </div>
            <% if (ID_EMPRESA == 70) { %>
              <div class="form-group">
                <label class="control-label">Video</label>
                <input type="text" class="form-control" name="video" id="campania_video" value='<%= video %>'>
              </div>
              <?php if ($empresa->id == 70) {
                single_file_upload(array(
                  "name"=>"path_video",
                  "label"=>lang(array("es"=>"Video Propio","en"=>"Video")),
                  "url"=>"campanias/function/save_file/",
                ));
              } ?>
            <% } %>

            <div class="form-group">
              <label class="control-label">Código</label>
              <textarea id="campania_codigo" name="codigo" class="form-control"><%= codigo %></textarea>
            </div>

          </div>
        </div>
        <div id="tab2" class="tab-pane pt0">
          <div id="pieza_categorias_tree"></div>
        </div>
        <div id="tab5" class="tab-pane panel-body pt0">
          <div class="row">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label><?php echo lang(array("es"=>"Desde","en"=>"From date")); ?></label>
                  <div class="input-group">
                    <input type="text" placeholder="Desde" id="pieza_fecha_desde" class="form-control"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>                
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label><?php echo lang(array("es"=>"Hasta","en"=>"To date")); ?></label>
                  <div class="input-group">
                    <input type="text" placeholder="Hasta" id="pieza_fecha_hasta" class="form-control"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <% if (ID_EMPRESA == 70) { %>
                <div class="col-md-3">
                  <div class="form-group">
                    <label><?php echo lang(array("es"=>"Hora Inicio","en"=>"Start time")); ?></label>
                    <input type="text" id="pieza_fecha_hora_inicio" value="<%= hora_desde_1 %>" class="form-control no-model"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label><?php echo lang(array("es"=>"Repetir","en"=>"Repeat")); ?></label>
                    <select id="pieza_repetir" class="form-control" name="repetir">
                      <option <%= (repetir==0)?'selected':'' %> value="0">24 Hs.</option>
                      <option <%= (repetir==2)?'selected':'' %> value="2">12/24 Hs.</option>
                      <option <%= (repetir==3)?'selected':'' %> value="3">8/24 Hs.</option>
                      <option <%= (repetir==4)?'selected':'' %> value="4">6/24 Hs.</option>
                      <option <%= (repetir==6)?'selected':'' %> value="6">4/24 Hs.</option>
                      <option <%= (repetir==8)?'selected':'' %> value="8">3/24 Hs.</option>
                      <option <%= (repetir==12)?'selected':'' %> value="12">2/24 Hs.</option>
                    </select>
                  </div>
                </div>
              <% } %>
            </div>
            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label><?php echo lang(array("es"=>"Dias de la semana","en"=>"Days of week")); ?></label>
                  <div class="clearfix">
                    <div class="checkbox dib mr10"><label class="i-checks"><input type="checkbox" id="pieza_lunes" value="1" name="lunes" <%= (lunes==1)?"checked":"" %>><i></i><?php echo lang(array("es"=>"Lunes","en"=>"Monday")); ?></label></div>
                    <div class="checkbox dib mr10"><label class="i-checks"><input type="checkbox" id="pieza_martes" value="1" name="martes" <%= (martes==1)?"checked":"" %>><i></i><?php echo lang(array("es"=>"Martes","en"=>"Tuesday")); ?></label></div>
                    <div class="checkbox dib mr10"><label class="i-checks"><input type="checkbox" id="pieza_miercoles" value="1" name="miercoles" <%= (miercoles==1)?"checked":"" %>><i></i><?php echo lang(array("es"=>"Miercoles","en"=>"Wednesday")); ?></label></div>
                    <div class="checkbox dib mr10"><label class="i-checks"><input type="checkbox" id="pieza_jueves" value="1" name="jueves" <%= (jueves==1)?"checked":"" %>><i></i><?php echo lang(array("es"=>"Jueves","en"=>"Thursday")); ?></label></div>
                    <div class="checkbox dib mr10"><label class="i-checks"><input type="checkbox" id="pieza_viernes" value="1" name="viernes" <%= (viernes==1)?"checked":"" %>><i></i><?php echo lang(array("es"=>"Viernes","en"=>"Friday")); ?></label></div>
                    <div class="checkbox dib mr10"><label class="i-checks"><input type="checkbox" id="pieza_sabado" value="1" name="sabado" <%= (sabado==1)?"checked":"" %>><i></i><?php echo lang(array("es"=>"Sabado","en"=>"Saturday")); ?></label></div>
                    <div class="checkbox dib mr10"><label class="i-checks"><input type="checkbox" id="pieza_domingo" value="1" name="domingo" <%= (domingo==1)?"checked":"" %>><i></i><?php echo lang(array("es"=>"Domingo","en"=>"Sunday")); ?></label></div>
                  </div>
                </div>
              </div>
            </div>
            <% if (ID_EMPRESA == 70) { %>
              <div>
                <a class="expand-link-horarios btn btn-default btn-sm">
                  <?php echo lang(array(
                    "es"=>"Ver detalle de horarios",
                    "en"=>"Show schedule details",
                  )); ?>
                </a>
              </div>
            <% } %>
            <div style="display: none; padding-top: 15px" id="horarios_container">
              <div class="row">
                <?php for($i=1;$i<=12;$i++) { ?>
                  <div class="col-md-3 col-xs-6">
                    <div class="form-group">
                      <label><?php echo lang(array("es"=>"Horario","en"=>"Schedule")); ?> <?php echo $i ?></label>
                      <input type="text" placeholder="<?php echo lang(array("es"=>"Desde","en"=>"Start")); ?>" id="pieza_hora_desde_<?php echo $i ?>" name="hora_desde_<?php echo $i ?>" value="<%= hora_desde_<?php echo $i ?> %>" class="form-control"/>
                    </div>
                  </div>
                  <div class="col-md-3 col-xs-6">
                    <div class="form-group">
                      <label>&nbsp;</label>
                      <input type="text" placeholder="<?php echo lang(array("es"=>"Hasta","en"=>"End")); ?>" id="pieza_hora_hasta_<?php echo $i ?>" name="hora_hasta_<?php echo $i ?>" value="<%= hora_hasta_<?php echo $i ?> %>" class="form-control"/>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
  </div>  
</div>
     
</script>