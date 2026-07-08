<script type="text/template" id="articulos_resultados_template">
  <div class="seccion_vacia" style="display:none">
    <h1 class="h1">Todav&iacute;a no ten&eacute;s ning&uacute;n producto</h1>
    <h3 class="h3">Para crear tu primer producto, hace click en el siguiente bot&oacute;n</h3>
    <div class="list-icon">
      <a href="app/#articulo"><i class="icon-note"></i></a>
    </div>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#articulo">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
    <h3 class="h3 mt30 mb30 tac"><span style="max-width: 450px; line-height: 30px; display: inline-block;">O pod&eacute;s utilizar nuestra herramienta para importar art&iacute;culos desde un archivo de Excel</span></h3>
    <div>
      <a class="btn btn-lg importar_excel btn-default btn-addon" href="javascript:void(0)">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Importar desde Excel&nbsp;&nbsp;</span>
      </a>
    </div>
    <p>
      Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
    </p>
  </div>
  <div class="seccion_llena" style="display:none">
    <% if (!seleccionar) { %>
      <?php include("art/articulos_header.php") ?>
    <% } %>
    <div class="<%= (!seleccionar)?'wrapper-md':''%> ng-scope">
      <div class="panel panel-default">
        <% if (IDIOMA != "en") { %>
          <?php $active = "articulos"; include("art/articulos_menu.php"); ?>
        <% } %>
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="<% if (!seleccionar) { %>col-md-8 <% } else { %> col-xs-12 <% } %> sm-m-b">
              <div class="input-group">
                <input type="text" id="articulos_buscar" value="<%= window.articulos_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default"><i class="fa fa-search"></i></button>
                </span>
                <span class="input-group-btn" style="display: none;">
                  <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
                </span>
                <% if (MILLING == 0 && TOQUE == 0 && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                  <span class="input-group-btn">
                    <div class="btn-group dropdown ml5">
                      <button style="display: none;" class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-print"></i><span><?php echo lang(array("es"=>"Imprimir","en"=>"Print")); ?></span>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <% if (MEGASHOP == 1 || ID_EMPRESA == 421) { %>
                          <% if (control.check("articulos")>2) { %>
                            <li><a href="javascript:void(0)" class="notificar">Notificar cambios</a></li>
                          <% } %>
                          <li><a href="javascript:void(0)" class="imprimir_etiquetas">Etiquetas</a></li>
                        <% } %>
                        <li><a href="javascript:void(0)" class="imprimir" data-tipo="4">Lista de precios</a></li>
                        <li><a href="javascript:void(0)" class="imprimir" data-tipo="10">Lista por proveedor</a></li>
                        <% if (control.check("articulos")>2) { %>
                          <li><a href="javascript:void(0)" class="imprimir" data-tipo="8">Lista de costos</a></li>
                        <% } %>
                        <li><a href="javascript:void(0)" class="imprimir" data-tipo="1">Carteles chicos</a></li>
                        <li><a href="javascript:void(0)" class="imprimir" data-tipo="9">Carteles medianos</a></li>
                        <li><a href="javascript:void(0)" class="imprimir" data-tipo="2">Carteles grandes</a></li>
                        <li><a href="javascript:void(0)" class="imprimir" data-tipo="7">Oferta Mediana</a></li>
                        <li><a href="javascript:void(0)" class="imprimir" data-tipo="3">Oferta Grande</a></li>
                        <% if (typeof FACTURACION_USA_NPLU != "undefined" && FACTURACION_USA_NPLU == 1) { %>
                          <li><a href="javascript:void(0)" class="imprimir" data-tipo="5">Hoja de Pedido</a></li>
                          <li><a href="javascript:void(0)" class="imprimir" data-tipo="6">Listado de PLU</a></li>
                        <% } %>
                        <% if (ID_EMPRESA == 121) { %>
                          <li><a href="javascript:void(0)" class="precios_maximos">Precios Maximos</a></li>
                        <% } %>
                      </ul>
                    </div>
                  </span>
                <% } %>
                <% if (MILLING == 0 && TOQUE == 0 && ID_EMPRESA != 1284) { %>
                  <span class="input-group-btn">
                    <div class="btn-group dropdown ml5">
                      <button style="display: none;" class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
                        <% if (IDIOMA != "en") { %>
                          <li><a href="javascript:void" class="exportar_csv">Exportar CSV</a></li>
                        <% } %>
                      </ul>
                    </div>
                  </span>
                <% } %>
                <% if (MILLING == 0) { %>
                  <span class="input-group-btn">
                    <div class="btn-group dropdown ml5">
                      <button style="display: none;" class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-download"></i><span><?php echo lang(array("es"=>"Importar","en"=>"Import")); ?></span>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <% if (TOQUE == 0) { %>
                          <% if (ID_EMPRESA == 186 || ID_EMPRESA == 120) { %>
                            <li><a href="javascript:void" class="importar_vulca">Importar sistema</a></li>
                          <% } else if (ID_EMPRESA == 121) { %>
                            <li><a href="/sistema/articulos/function/importar_csv/" target="_blank">Importar CSV</a></li>
                          <% } else if (ID_EMPRESA == 252) { %>
                            <li><a href="javascript:void" class="importar_center">Importar sistema</a></li>
                          <% } else if (ID_EMPRESA == 1284) { %>
                            <li><a href="javascript:void" class="importar_excel">Importar Excel</a></li>
                            <li><a href="javascript:void" class="importar_imagenes">Im&aacute;genes</a></li>
                          <% } else { %>
                            <li><a href="javascript:void" class="importar_excel">Importar Excel</a></li>
                            <% if (IDIOMA != "en") { %>
                              <li><a href="javascript:void" class="importar_csv">Importar CSV</a></li>
                              <li><a href="javascript:void" class="importar_imagenes">Im&aacute;genes</a></li>
                            <% } %>
                          <% } %>
                        <% } else { %>
                          <li><a href="javascript:void" class="importar_excel">Importar Excel</a></li>
                        <% } %>
                      </ul>
                    </div>
                  </span>
                <% } %>
              </div>
            </div>
            <% if (!seleccionar) { %>
              <div class="col-md-4 text-right">
                
                <% if (control.check("articulos")>1) { %>
                  <a class="btn btn-info btn-addon btn-block-xs" href="app/#articulo">
                    <i class="fa fa-plus"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo Producto","en"=>"New Product")); ?>&nbsp;&nbsp;</span>
                  </a>
                <% } %>
                
              </div>
            <% } %>
          </div>
        </div>
        <div class="advanced-search-div bg-light dk" style="<%= (window.articulos_id_marca != 0 || window.articulos_id_rubro != 0 || window.articulos_fecha != '' || window.articulos_id_departamento != 0 || window.articulos_id_proveedor != 0 || window.articulos_imagen != -1 || window.articulos_destacado != -1 || window.articulos_con_descuento != -1 || !isEmpty(window.articulos_codigo_proveedor) ) ? "display:block" : "display:none" %>">
          <div class="wrapper oh">
            <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
            <div class="row pl10 pr10">
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select id="articulos_buscar_categorias" class="w100p form-control no-model">
                    <option selected value="0"><?php echo lang(array("es"=>"Rubro","en"=>"Category")); ?></option>
                    <option value="-1"><?php echo lang(array("es"=>"Sin definir","en"=>"Not defined")); ?></option>
                    <%= workspace.crear_select(rubros,"",window.articulos_id_rubro) %>
                  </select>
                </div>
              </div>
              <% if (control.check("marcas")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="articulos_buscar_marcas" class="w100p no-model">
                      <option selected value="0"><?php echo lang(array("es"=>"Marca","en"=>"Brand")); ?></option>
                    </select>
                  </div>
                </div>
              <% } %>
              <% if (control.check("stock")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="articulos_filtro_stock" class="form-control no-model">
                      <option selected value="">Stock</option>
                      <option value="sin_stock"><?php echo lang(array("es"=>"Productos sin stock","en"=>"Out of stock")); ?></option>
                      <option value="con_stock"><?php echo lang(array("es"=>"Productos con stock","en"=>"With stock")); ?></option>
                    </select>
                  </div>
                </div>
              <% } %>
              <% if (control.check("proveedores")>0 && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="articulos_buscar_proveedores"></select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <input type="text" placeholder="Codigo Prov." class="input form-control no-model" id="articulos_buscar_codigo_prov" value="<%= window.articulos_codigo_proveedor %>" />
                  </div>
                </div>
              <% } %>
              <% if (control.check("departamentos_comerciales")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="articulos_buscar_departamentos_comerciales" class="w100p no-model">
                      <option selected value="0"></option>
                    </select>
                  </div>
                </div>
              <% } %>
              
              <% if (IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <div class="input-group">
                      <select class="form-control no-model pl5 pr0" id="articulos_fecha_tipo">
                        <option <%= (window.articulos_fecha_tipo == "") ? "selected":"" %> value="igual">=</option>
                        <option <%= (window.articulos_fecha_tipo == "mayor") ? "selected":"" %> value="mayor">></option>
                        <option <%= (window.articulos_fecha_tipo == "menor") ? "selected":"" %> value="menor"><</option>
                      </select>
                      <span class="input-group-btn w70p">
                        <input type="text" placeholder="Fecha Modif." class="input form-control no-model" id="articulos_fecha"/>
                      </span>
                    </div>
                  </div>
                </div>
              <% } %>

              <% if (IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="articulos_buscar_etiquetas" class="w100p no-model form-control">
                      <option <%= (window.articulos_id_etiqueta == 0) ? "selected":"" %> value="0"><?php echo lang(array("es"=>"Etiqueta","en"=>"Label")); ?></option>
                      <% for(var jj=0;jj<window.articulos_etiquetas.length;jj++) { %>
                        <% var et = window.articulos_etiquetas[jj] %>
                        <option <%= (window.articulos_id_etiqueta == et.id) ? "selected":"" %> value="<%= et.id %>"><%= et.nombre %></option>
                      <% } %>                    
                    </select>
                  </div>
                </div>
              <% } %>

              <% if (MILLING == 0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="articulos_buscar_activo" class="w100p form-control no-model">
                      <option <%= (window.articulos_activo == -1)?"selected":"" %> value="-1"><?php echo lang(array("es"=>"Activo/Inactivo","en"=>"Active/Inactive")); ?></option>
                      <option <%= (window.articulos_activo == 1)?"selected":"" %> value="1"><?php echo lang(array("es"=>"S&oacute;lo activos","en"=>"Only actives")); ?></option>
                      <option <%= (window.articulos_activo == 0)?"selected":"" %> value="0"><?php echo lang(array("es"=>"S&oacute;lo inactivos","en"=>"Only inactives")); ?></option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="articulos_buscar_destacado" class="w100p form-control no-model">
                      <option <%= (window.articulos_destacado == -1)?"selected":"" %> value="-1"><?php echo lang(array("es"=>"Destacados","en"=>"Feature products")); ?></option>
                      <option <%= (window.articulos_destacado == 1)?"selected":"" %> value="1"><?php echo lang(array("es"=>"S&oacute;lo destacados","en"=>"Only feature products")); ?></option>
                      <option <%= (window.articulos_destacado == 0)?"selected":"" %> value="0"><?php echo lang(array("es"=>"S&oacute;lo no destacados","en"=>"Only NO feature products")); ?></option>
                    </select>
                  </div>
                </div>
                <% if (IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                  <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                    <div class="form-group">
                      <select id="articulos_con_descuento" class="w100p form-control no-model">
                        <option <%= (window.articulos_con_descuento == -1)?"selected":"" %> value="-1">Descuento</option>
                        <option <%= (window.articulos_con_descuento == 1)?"selected":"" %> value="1">S&oacute;lo con descuento</option>
                        <option <%= (window.articulos_con_descuento == 0)?"selected":"" %> value="0">Sin descuento</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                    <div class="form-group">
                      <select id="articulos_buscar_imagen" class="w100p form-control no-model">
                        <option <%= (window.articulos_imagen == -1)?"selected":"" %> value="-1">Imagen</option>
                        <option <%= (window.articulos_imagen == 1)?"selected":"" %> value="1">S&oacute;lo con imagen</option>
                        <option <%= (window.articulos_imagen == 0)?"selected":"" %> value="0">Sin imagen</option>
                      </select>
                    </div>
                  </div>
                  <% if (typeof ML_ACCESS_TOKEN != "undefined" && !isEmpty(ML_ACCESS_TOKEN)) { %>
                    <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                      <div class="form-group">
                        <select id="articulos_buscar_mercadolibre" class="w100p form-control no-model">
                          <option <%= (window.articulos_buscar_mercadolibre == "")?"selected":"" %> value="">MercadoLibre</option>
                          <option <%= (window.articulos_buscar_mercadolibre == "A")?"selected":"" %> value="A">Activos</option>
                          <option <%= (window.articulos_buscar_mercadolibre == "P")?"selected":"" %> value="P">En Pausa</option>
                        </select>
                      </div>
                    </div>
                  <% } %>
                <% } %>
                <?php /*
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="articulos_buscar_custom_5" class="w100p form-control no-model">
                      <option <%= (window.articulos_custom_5 == "")?"selected":"" %> value="">Sin Filtro</option>
                      <option <%= (window.articulos_custom_5 == "1")?"selected":"" %> value="1">S&oacute;lo Canasta Basica</option>
                      <option <%= (window.articulos_custom_5 == "0")?"selected":"" %> value="0">Sin Canasta Basica</option>
                      <option <%= (window.articulos_custom_5 == "0_iva")?"selected":"" %> value="0_iva">0% IVA</option>
                    </select>
                  </div>
                </div>  
                */ ?>              
              <% } %>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <button id="articulos_buscar_avanzada_btn" class="btn btn-default btn-dark btn-block"><i class="fa fa-search m-r-xs"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <% if (!seleccionar) { %>
          <div class="bulk_action wrapper pb0">
            <% if (IDIOMA != "en" && ID_EMPRESA != 1284) { %>
              <button class="btn btn-default enviar btn-addon"><i class="icon fa fa-send"></i>Enviar por email</button>
              
              <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO)) { %>
                <div class="btn-group dropdown">
                  <button class="btn btn-default btn-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="icon fa fa-share-alt"></i>MercadoLibre
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="javascript:void(0)" class="compartir_meli">Compartir</a></li>
                    <li><a href="javascript:void(0)" class="meli_activar_multiple">Reactivar</a></li>
                    <li><a href="javascript:void(0)" class="meli_pausar_multiple">Pausar</a></li>
                  </ul>
                </div> 
              <% } %>
              <div class="btn-group dropdown">
                <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                  <i class="fa fa-pencil"></i><span><?php echo lang(array("es"=>"Editar","en"=>"Edit")); ?></span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" class="cambiar_imagen">Imagenes</a></li>
                  <li><a href="javascript:void(0)" class="cambiar_rubro">Categoria</a></li>
                  <li><a href="javascript:void(0)" class="cambiar_marca">Marca</a></li>
                  <li><a href="javascript:void(0)" class="cambiar_etiqueta">Etiqueta</a></li>
                  <li><a href="javascript:void(0)" class="cambiar_moneda">Moneda</a></li>
                  <li><a href="javascript:void(0)" class="cambiar_oferta">Oferta</a></li>
                  <li><a href="javascript:void(0)" class="editar_masivo_proveedor">Proveedor</a></li>
                  <li><a href="javascript:void(0)" class="ajuste_masivo_stock">Ajuste Stock</a></li>
                  <% if (typeof CANASTA_BASICA != "undefined" && CANASTA_BASICA == 1) { %>
                    <li><a href="javascript:void(0)" class="ajuste_masivo_canasta_basica">Canasta Básica</a></li>
                  <% } %>
                </ul>
              </div>
            <% } %>
            <% if (permiso > 2) { %>
              <button class="btn btn-default eliminar_lote btn-addon"><i class="icon fa fa-times"></i><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></button>
            <% } %>
          </div>
        <% } %>
        
        <div class="panel-body">
          <div class="b-a table-responsive">
          <table id="articulos_tabla" class="table <%= (seleccionar || ID_EMPRESA == 121 || MEGASHOP == 1 || ID_EMPRESA == 421)?'table-small':'' %> table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th style="width:20px;">
                    <% if (!seleccionar) { %>
                      <label class="i-checks m-b-none">
                        <input class="esc sel_todos" type="checkbox"><i></i>
                      </label>
                    <% } %>
                  </th>
                  <% for(var i=0; i< tabla_articulos.campos.length; i++) { %>
                    <% var c = tabla_articulos.campos[i] %>
                    <% if (c.visible == 1) { %>
                      
                      <% if (c.campo == "stock_almacenes") { %>
                        <% for(var k=0;k< window.almacenes.length;k++) { %>
                          <% var alm = window.almacenes[k] %>
                          <% if (ID_SUCURSAL == 0 || (ID_SUCURSAL != 0 && ID_SUCURSAL == alm.id)) { %>
                            <th class="w80"><%= alm.nombre %></th>
                          <% } %>
                        <% } %>
                        <th style="width:15px" class="pl0 pr0"></th>
                      
                      <% } else if (c.campo == "precio_final_dto") { %>
                        <th class="<%= c.clases %> mmw100 <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                          <%= LISTA_1_NOMBRE %>
                        </th>
                      <% } else if (c.campo == "precio_final_dto_2") { %>
                        <th class="<%= c.clases %> mmw100 <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                          <%= LISTA_2_NOMBRE %>
                        </th>
                      <% } else if (c.campo == "precio_final_dto_3") { %>
                        <th class="<%= c.clases %> mmw100 <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                          <%= LISTA_3_NOMBRE %>
                        </th>
                      <% } else if (c.campo == "precio_final_dto_4") { %>
                        <th class="<%= c.clases %> mmw100 <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                          <%= LISTA_4_NOMBRE %>
                        </th>
                      <% } else if (c.campo == "precio_final_dto_5") { %>
                        <th class="<%= c.clases %> mmw100 <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                          <%= LISTA_5_NOMBRE %>
                        </th>
                      <% } else if (c.campo == "precio_final_dto_6") { %>
                        <th class="<%= c.clases %> mmw100 <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                          <%= LISTA_6_NOMBRE %>
                        </th>

                      <% } else { %>
                        <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  ><%= (c.campo == "path")?"":c.titulo %></th>
                      <% } %>

                    <% } %>
                  <% } %>

                  <% if (MILLING == 0 && TOQUE == 0) { %>
                    <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && (typeof ML_ACCESS_TOKEN != "undefined") && permiso > 1 && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
                      <th class="<%= (TIPO_EMPRESA == 4)?"w80":"w50" %> th_acciones pr0"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?></th>
                    <% } %>
                  <% } %>
                  <% if (!seleccionar && permiso > 1) { %>
                    <th class="th_acciones w100">
                      <% if (permiso > 2 && ID_EMPRESA != 1284) { %>
                        <i class="fa configurar_tabla cp fa-cog pull-right pr t-3"></i>
                      <% } %>
                    </th>
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
<?php /* <% } %> */ ?>
</script>

<script type="text/template" id="articulos_buscar_resultados_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <div class="row">
      <div class="col-xs-12 sm-m-b">
        <input type="text" id="articulos_buscar" style="font-size: 26px; padding-top: 10px; padding-bottom: 10px; height: auto " placeholder="Buscar..." autocomplete="off" class="form-control"/>
      </div>
    </div>
  </div>
  <div class="panel-body">
    <div class="b-a table-responsive">
      <table id="articulos_tabla" class="table table-small table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th style="width:20px;"></th>
            <% for(var i=0; i< tabla_articulos.campos.length; i++) { %>
              <% var c = tabla_articulos.campos[i] %>
              <% if (c.visible == 1) { %>
                
                <% if (c.campo == "stock_almacenes") { %>
                  <% for(var k=0;k< window.almacenes.length;k++) { %>
                    <% var alm = window.almacenes[k] %>
                    <% if (ID_SUCURSAL == 0 || (ID_SUCURSAL != 0 && ID_SUCURSAL == alm.id)) { %>
                      <th class="w80"><%= alm.nombre %></th>
                    <% } %>
                  <% } %>
                
                <% } else if (c.campo == "precio_final_dto") { %>
                  <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                    <%= LISTA_1_NOMBRE %>
                  </th>
                <% } else if (c.campo == "precio_final_dto_2") { %>
                  <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                    <%= LISTA_2_NOMBRE %>
                  </th>
                <% } else if (c.campo == "precio_final_dto_3") { %>
                  <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                    <%= LISTA_3_NOMBRE %>
                  </th>
                <% } else if (c.campo == "precio_final_dto_4") { %>
                  <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                    <%= LISTA_4_NOMBRE %>
                  </th>
                <% } else if (c.campo == "precio_final_dto_5") { %>
                  <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                    <%= LISTA_5_NOMBRE %>
                  </th>
                <% } else if (c.campo == "precio_final_dto_6") { %>
                  <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
                    <%= LISTA_6_NOMBRE %>
                  </th>

                <% } else { %>
                  <th class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  ><%= (c.campo == "path")?"":c.titulo %></th>
                <% } %>

              <% } %>
            <% } %>

          </tr>
        </thead>
        <tbody class="tbody"></tbody>
        <tfoot class="pagination_container hide-if-no-paging"></tfoot>
      </table>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="articulos_mostrar_precio_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <div class="row">
      <div class="col-xs-12 sm-m-b">
        <input type="text" id="articulos_mostrar_precio_buscar" style="font-size: 26px; padding-top: 10px; padding-bottom: 10px; height: auto " placeholder="Buscar..." autocomplete="off" class="form-control"/>
      </div>
    </div>
  </div>
  <div class="panel-body">
    <div style="font-size: 28px; padding-left: 20px; padding-right: 20px; margin-top: 30px; text-align: center;" id="articulos_mostrar_precio_texto"></div>
    <div style="font-size: 64px; font-weight: bold; text-align: center; margin-top: 20px; margin-bottom: 20px" id="articulos_mostrar_precio_precio"></div>
  </div>
</div>
</script>

<script type="text/template" id="articulos_buscar_por_rubros_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Buscar productos</b>
    <i class="fa fa-times cerrar cp fr"></i>
  </div>
  <div class="panel-body pt0 pb0">
    <div class="row">
      <div id="articulos_rubros" class="col-sm-3 p0" style="overflow: auto; height: 280px">
        <% for(var i=0;i< rubros.length; i++) { %>
          <% var o = rubros[i] %>
          <% if (id_usuario == 0 || o.id_usuario == id_usuario) { %>
            <div class="list-group mb0">
              <a href="javascript:void(0)" data-id="<%= o.id %>" class="list-group-item rubro">
                <%= o.title %>
              </a>
            </div>
            <% for(var ii=0;ii< o.children.length; ii++) { %>
              <% var oo = o.children[ii] %>
              <div class="list-group mb0">
                <a href="javascript:void(0)" data-id="<%= oo.id %>" class="list-group-item rubro">
                  - <%= oo.title %>
                </a>
              </div>
            <% } %>
          <% } %>
        <% } %>
      </div>
      <div class="col-sm-9 p0" style="overflow: auto; height: 280px">
        <ul id ="articulos_listado" class="list-group alt"></ul>
      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn fr btn-success aceptar">Aceptar</button>
  </div>
</div>
</script>

<script type="text/template" id="articulos_buscar_carta_completa_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Buscar productos</b>
    <i class="fa fa-times cerrar cp fr"></i>
  </div>
  <div class="panel-body pt0 pb0">
    <div id="articulos_buscar_carta_completa_container" class="hbox"></div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn fl btn-lg btn-default cerrar">&nbsp;&nbsp;CERRAR&nbsp;&nbsp;</button>
    <button class="btn fr btn-lg btn-success aceptar">&nbsp;&nbsp;ACEPTAR&nbsp;&nbsp;</button>
  </div>
</div>
</script>

<script type="text/template" id="articulos_buscar_por_rubros_item_template">
<div class="media">
  <div class="media-body clearfix">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="rubro-item-nombre p0 mt0 mb10">
          <a class="<%= (ingredientes.length > 0 || ID_EMPRESA == 171 || ID_EMPRESA == 599) ? 'expand-link-ing' : '' %>" href="javascript:void(0)">
            <%= nombre %>
            <% if (ingredientes.length > 0 || ID_EMPRESA == 171 || ID_EMPRESA == 599) { %>
              <i class="fa fa-angle-double-down"></i>
            <% } %>
          </a>
        </h3>
      </div>
      <div class="col-sm-6">
        <div class="pull-right clearfix rubro-item-cantidad" style="width: 160px">
          <div class="input-group no-br no-br-both">
            <span class="input-group-addon cp addon_minus">&nbsp;&nbsp;<i class="fa fa-minus"></i>&nbsp;&nbsp;</span>
            <input min="0" disabled type="text" data-step="<%= (unidad=='M')? 0.5 : 1 %>" value="<%= cantidad %>" class="form-control tar cantidad"/>
            <span class="input-group-addon cp addon_plus">&nbsp;&nbsp;<i class="fa fa-plus"></i>&nbsp;&nbsp;</span>
          </div>
        </div>
      </div>
    </div>
    <% if (ingredientes.length > 0 || ID_EMPRESA == 171 || ID_EMPRESA == 599) { %>
      <div class="expandable" style="display: none;">
        <% for (var i=0;i< ingredientes.length;i++) { %>
          <% var ing = ingredientes[i] %>
          <div class="clearfix dt">
            <h4 class="dtc">
              <span class="text-info m-r"><%= ing.nombre %></span>
            </h4>
            <div class="dtc">
              <% var valores = ing.valores.split(",") %>
              <% for (var j=0;j < valores.length; j++) { %>
                <% var v = valores[j] %>
                <div class="radio fl m-r mt10 mb10">
                  <label class="i-checks">
                    <input data-nombre="<%= ing.nombre %>" data-adicional="<%= ing.adicional %>" type="radio" name="campo_<%= id %>_<%= i %>" value="<%= v %>" class="radio_ingrediente <%= (j>0)?'valor_sel':'' %>" <%= (j==0)?'checked=""':'' %>>
                    <i></i>
                    <%= v %>
                  </label>
                </div>
              <% } %>
            </div>
          </div>
        <% } %>
        <?php /*
        <div class="cb <%= (typeof FACTURACION_EDITAR_DESCUENTO != 'undefined' && FACTURACION_EDITAR_DESCUENTO==1)?"":"dn" %>">
          <div class="row">
            <div class="col-xs-6">
              <span class="mr5 mt10">Dto. (%)</span>
            </div>
            <div class="col-xs-6">
              <input type="number" min="0" max="100" value="<%= porc_bonif %>" name="porc_bonif" class="form-control w-xs pull-right action text-right"/>
            </div>
          </div>
        </div>
        */ ?>
      </div>
    <% } %>
  </div>
</div>
</script>


<script type="text/template" id="articulos_cambiar_fecha_template">
    <div class="titulo">
	Cambiar Fecha
    </div>    
    <div class="panel">
    	<div class="mb10 bold">Cambiar articulos con fecha:</div>
    	<div>
    	    <input type="text" class="input w65" id="articulos_cambiar_fecha_actual"/>
    	    <span class="fwn ml10 mr10">por:</span>
    	    <input type="text" class="input w65" id="articulos_cambiar_fecha_posterior"/>
    	</div>
    	<div class="row tar cb">
    	    <button class="btn btn-primary verde guardar">Cambiar</button>
    	</div>
    </div>
</script>



<script type="text/template" id="articulos_item_resultados_template">
  <% var clase = (lista_precios==0)?"text-muted":""; %>
  <% var cant_dec = ((typeof FACTURACION_CANTIDAD_DECIMALES != undefined) ? FACTURACION_CANTIDAD_DECIMALES : 0) %>
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
  <% } %>
  <% for(var i=0; i< tabla_articulos.campos.length; i++) { %>
    <% var c = tabla_articulos.campos[i] %>
    
    <% if (c.campo == "path" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (!isEmpty(path)) { %>
          <% if (path.indexOf("http") == -1) { %>
            <img src="/sistema/<%= path %>?t=<%= Math.ceil(Math.random()*10000) %>" class="customcomplete-image"/>
          <% } else { %>
            <img src="<%= path %>" class="customcomplete-image"/>
          <% } %>  
        <% } %>
      </td>

    <% } else if (c.campo == "codigo" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= codigo %>
        <% if (destacado >= 1) { %>
          <br/><span class="fs12"><i class="fa fa-star text-warning"></i> (<%= destacado %>)</span>
        <% } %>
      </td>

    <% } else if (c.campo == "nombre" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="<%= (lista_precios >= 1)?'text-info':'text-muted' %> nombre"><%= nombre %></span>

        <% if (etiquetas.length > 0) { %>
          <div class="clearfix">
            <% for(var j=0;j< etiquetas.length; j++) { %>
              <% var etiq = etiquetas[j] %>
              <span class="label bg-light pull-left m-t-xs m-r-xs"><%= etiq %></span>
            <% } %>
          </div>
        <% } %>

        <% if (ID_EMPRESA == 120) { %>
          <br/>
          <%= alto.replace(".00","") %> / <%= ancho.replace(".00","") %> / <%= profundidad.replace(".00","") %>
          <%= custom_1 %> <%= custom_2 %>
        <% } else if (ID_EMPRESA == 252) { %>
          <br/>
          <%= custom_1 %>
        <% } %>
      </td>

    <% } else if (c.campo == "marca" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= marca %>
      </td>

    <% } else if (c.campo == "custom_1" && c.visible == 1 && TOQUE == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= custom_1 %>
      </td>

    <% } else if (c.campo == "usuario" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= usuario %>
      </td>

    <% } else if (c.campo == "rubro" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= (rubro=="-")?"—":rubro %>
      </td>

    <% } else if (c.campo == "fecha_mov" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= fecha_mov %>
      </td>

    <% } else if (c.campo == "tipo" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (tipo == 10) { %>
          <span class="label bg-success">Clase A</span>
        <% } else if (tipo == 5) { %>
          <span class="label bg-warning">Clase B</span>
        <% } else if (tipo == 3) { %>
          <span class="label bg-light dk">Clase C</span>
        <% } %>
      </td>

    <% } else if (c.campo == "costo_neto" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= Number(costo_neto).format(cant_dec) %>
      </td>

    <% } else if (c.campo == "costo_final" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= Number(costo_final).format(cant_dec) %>
      </td>

    <% } else if (c.campo == "porc_ganancia" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= Number(porc_ganancia).format(cant_dec) %>
      </td>

    <% } else if (c.campo == "fecha_ingreso" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= fecha_ingreso %>
      </td>

    <% } else if (c.campo == "fecha_mov" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= fecha_mov %>
      </td>

    <% } else if (c.campo == "proveedor" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= custom_6 %>
      </td>

    <% } else if (c.campo == "etiquetas" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% for(var jj=0;jj<etiquetas.length;jj++) { %>
          <% var tag = etiquetas[jj] %>
          <span class="label bg-light dk"><%= tag %></span>
        <% } %>
      </td>

    <% } else if (c.campo == "codigo_barra" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <%= codigo_barra.replace(/\#\#\#/g,"<br/>") %>
      </td>

    <% } else if (c.campo == "precio_final_dto" && c.visible == 1) { %>
      <td class="tar <%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="">
          <%= Number(precio_final_dto).format(cant_dec) %>
          <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && porc_bonif > 0) { %>
            <i data-toggle="tooltip" title="Descuento <%= porc_bonif %>%" class="m-l-xs fa fa-tag text-warning"></i>
          <% } %>
        </span>
      </td>

    <% } else if (c.campo == "precio_final_dto_2" && c.visible == 1) { %>
      <td class="tar <%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="">
          <%= Number(precio_final_dto_2).format(cant_dec) %>
          <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && porc_bonif_2 > 0) { %>
            <i data-toggle="tooltip" title="Descuento <%= porc_bonif_2 %>%" class="m-l-xs fa fa-tag text-warning"></i>
          <% } %>
        </span>
      </td>

    <% } else if (c.campo == "precio_final_dto_3" && c.visible == 1) { %>
      <td class="tar <%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="">
          <%= Number(precio_final_dto_3).format(cant_dec) %>
          <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && porc_bonif_3 > 0) { %>
            <i data-toggle="tooltip" title="Descuento <%= porc_bonif_3 %>%" class="m-l-xs fa fa-tag text-warning"></i>
          <% } %>
        </span>
      </td>

    <% } else if (c.campo == "precio_final_dto_4" && c.visible == 1) { %>
      <td class="tar <%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="">
          <%= Number(precio_final_dto_4).format(cant_dec) %>
          <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && porc_bonif_4 > 0) { %>
            <i data-toggle="tooltip" title="Descuento <%= porc_bonif_4 %>%" class="m-l-xs fa fa-tag text-warning"></i>
          <% } %>
        </span>
      </td>

    <% } else if (c.campo == "precio_final_dto_5" && c.visible == 1) { %>
      <td class="tar <%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="">
          <%= Number(precio_final_dto_5).format(cant_dec) %>
          <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && porc_bonif_5 > 0) { %>
            <i data-toggle="tooltip" title="Descuento <%= porc_bonif_5 %>%" class="m-l-xs fa fa-tag text-warning"></i>
          <% } %>
        </span>
      </td>

    <% } else if (c.campo == "precio_final_dto_6" && c.visible == 1) { %>
      <td class="tar <%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <span class="">
          <%= Number(precio_final_dto_6).format(cant_dec) %>
          <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && porc_bonif_6 > 0) { %>
            <i data-toggle="tooltip" title="Descuento <%= porc_bonif_6 %>%" class="m-l-xs fa fa-tag text-warning"></i>
          <% } %>
        </span>
      </td>

    <% } else if (c.campo == "codigo_prov" && c.visible == 1) { %>
      <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% for(var cp = 0;cp < proveedores.length;cp++) { %>
          <% var codigo_prov = proveedores[cp] %>
          <%= codigo_prov.codigo %><br/>
        <% } %>
      </td>

    <% } else if (c.campo == "stock_almacenes" && c.visible == 1) { %>
      <% for(var k=0;k< stock_almacenes.length; k++) { %>
        <% var salm = stock_almacenes[k] %>
        <% if (ID_SUCURSAL == 0 || (ID_SUCURSAL != 0 && ID_SUCURSAL == salm.id_sucursal)) { %>
          <td class="<%= clase %> <%= (c.ocultable == 1)?"hidden-xs":"" %>">
            <% var modificar_stock = (permiso > 2 && MEGASHOP == 0 && variantes.length == 0) ? 1 : 0 %>
            <span data-id_sucursal="<%= salm.id_sucursal %>" data-id_variante="0" class="tag_precio <%= (modificar_stock == 1)?"modificar_stock":"" %> mmw50 pl8 pr8 tac dib <%= (salm.stock_actual <= 0)?"bg-danger lter":"" %>">
              <span class="inline-text-cont <%= (modificar_stock == 1)?"bbd":"" %>">
                <%= Number(salm.stock_actual).format(cant_dec) %>
                <% if (salm.reservado > 0) { %>
                  (<%= Number(salm.reservado).format(cant_dec) %>)
                <% } %>
              </span>
              <input type="text" value="<%= salm.stock_actual %>" class="inline-text" />
            </span>
          </td>
        <% } %>
      <% } %>
      <td class="pl0 pr0 tac <%= clase %> <%= (c.ocultable == 1)?"hidden-xs":"" %>">
        <% if (variantes.length > 0) { %>
          <i class="fa fa-plus-circle ver_variantes"></i>
        <% } %>
      </td>

    <% } else if (c.campo == "pedi_en_chacabuco" && c.visible == 1) { %>
      <% if (typeof SINCRONIZADO_PEDI_EN_CHACABUCO != "undefined") { %>
        <td class="<%= clase %> data <%= (c.ocultable == 1)?"hidden-xs":"" %>">
          <% if (isEmpty(custom_5)) { %>
            <img width="38" src="/sistema/resources/images/pedi-off.png" data-toggle="tooltip" class="canasta_basica" title="Compartir en Pedi En Chacabuco"/>
          <% } else { %>  
            <img width="38" src="/sistema/resources/images/pedi-on.png" data-toggle="tooltip" class="canasta_basica" title="Compartir en Pedi En Chacabuco"/>
          <% } %>
        </td>
      <% } %>      

    <% } %>

  <% } %>

  <% if (MILLING == 0 && IDIOMA != "en" && ID_EMPRESA != 1284 && TOQUE == 0 && typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && (typeof ML_ACCESS_TOKEN != "undefined") && !seleccionar && permiso > 1) { %>
    <td class="pt5 pb5 pl5 pr0">
      <% if (isEmpty(ML_ACCESS_TOKEN)) { %>
        <img style="display: none;" src="/sistema/resources/images/ML-Off.png" data-toggle="tooltip" class="compartir_meli" title="Compartir en MercadoLibre"/>
      <% } else { %>
        <% if (typeof permalink != "undefined" && !isEmpty(permalink)) { %>
          <div class="btn-group dropdown">
            <div style="position: relative;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img src="/sistema/resources/images/ML-On.png" data-toggle="tooltip" title="Compartido en MercadoLibre"/>
              <% if (status == 'active') { %>
                <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-success pull-right"><i class="fa fa-play"></i></b>
              <% } else if (status == 'paused') { %>
                <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-danger pull-right"><i class="fa fa-pause"></i></b>
              <% } else if (status == 'closed') { %>
                <b style="position: absolute; bottom: -5px; right: -5px; font-size: 7px" class="badge bg-danger pull-right"><i class="fa fa-times"></i></b>
              <% } %>
            </div>
            <ul class="dropdown-menu pull-right">
              <li><a target="_blank" href="<%= permalink %>">Ver publicación</a></li>
              <% if (status == 'paused') { %>
                <li><a class="compartir_meli" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Modificar</a></li>
                <li><a class="meli_reactivar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Reactivar</a></li>
                <li><a class="meli_finalizar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Finalizar</a></li>
              <% } else if (status == 'active') { %>
                <li><a class="compartir_meli" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Modificar</a></li>
                <li><a class="meli_pausar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Pausar</a></li>
              <% } else if (status == 'closed') { %>
                <li><a class="meli_republicar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Republicar</a></li>
                <li><a class="meli_eliminar" data-id_meli="<%= id_meli %>" href="javascript:void(0)">Eliminar publicación</a></li>
              <% } %>
            </ul>
          </div>
        <% } else { %>
          <img src="/sistema/resources/images/ML-Off.png" data-toggle="tooltip" class="compartir_meli" title="Compartir en MercadoLibre"/>
        <% } %>
      <% } %>

    </td>
  <% } %>

  <% if (!seleccionar && permiso > 1) { %>
    <td class="p5 tar <%= clase %> td_acciones">
      <div class="btn-group dropdown">

        <% if (FORMA_ENVIO == "MERCADOENVIOS" && (peso == 0 || ancho == 0 || alto == 0 || profundidad == 0)) { %>
          <i data-toggle="tooltip" title="ATENCION: falta configurar las medidas del producto." class="warning fa iconito active fa-exclamation-triangle"></i>
        <% } %>

        <% var iconito = "fa-check" %>
        <% var titulo_icono = "Deshabilitado" %>
        <% if (lista_precios == 1) { %>
          <% iconito = "fa-check active success" %>
          <% var titulo_icono = "Activo en Sistema" %>
        <% } else if (lista_precios == 2) { %>
          <% iconito = "fa-globe active" %>
          <% var titulo_icono = "Publicado en Web" %>
        <% } else if (lista_precios == 3) { %>
          <% iconito = "fa-star active warning" %>
          <% var titulo_icono = "Destacado en Web" %>
        <% } %>
        <i data-toggle="tooltip" title="<%= titulo_icono %>" class="estado_articulo fa iconito <%= iconito %>"></i>
      </div>

      <?php /*
      <div class="btn-group dropdown">
        <% var iconito = "fa-check" %>
        <% var titulo_icono = "Deshabilitado" %>
        <% if (lista_precios == 1) { %>
          <% iconito = "fa-check active success" %>
          <% var titulo_icono = "Activo" %>
        <% } else if (lista_precios == 2) { %>
          <% iconito = "fa-globe active" %>
          <% var titulo_icono = "Publicado en Web" %>
        <% } else if (lista_precios == 3) { %>
          <% iconito = "fa-star active warning" %>
          <% var titulo_icono = "Destacado en Web" %>
        <% } %>
        <i class="fa iconito <%= iconito %> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
        <ul class="dropdown-menu pull-right">
          <% if (lista_precios != 0) { %>
            <li><a href="javascript:void(0)" data-toggle="tooltip" title="No se podr&aacute; vender por sistema ni aparecer&aacute; en la web" class="inactivo" data-id="<%= id %>"><?php echo lang(array("es"=>"Deshabilitar","en"=>"Disable")); ?></a></li>
          <% } %>
          <% if (lista_precios != 1) { %>
            <li><a href="javascript:void(0)" data-toggle="tooltip" title="Se podr&aacute; vender por sistema pero no aparecer&aacute; en la web" class="activo" data-id="<%= id %>"><?php echo lang(array("es"=>"Habilitar solo Sistema","en"=>"Enable to system")); ?></a></li>
          <% } %>
          <% if (lista_precios != 2) { %>
            <li><a href="javascript:void(0)" data-toggle="tooltip" title="El producto aparecer&aacute; publicado en la web" class="mostrar_web" data-id="<%= id %>"><?php echo lang(array("es"=>"Habilitar Web","en"=>"Enable to web")); ?></a></li>
          <% } %>
          <% if (lista_precios == 2) { %>
            <li><a href="javascript:void(0)" data-toggle="tooltip" title="Coloca este producto en la portada de tu web" class="destacado" data-id="<%= id %>"><?php echo lang(array("es"=>"Destacar Web","en"=>"Featured")); ?></a></li>
          <% } %>
        </ul>
      </div>
      */ ?>
      <% if (ID_PROYECTO == 10 && TOQUE == 0) { %>
        <i data-toggle="tooltip" title="Preparar en cocina" class="fa fa-fire iconito cocina <%= (no_totalizar_reparto == 1)?"active":"" %>"></i>
      <% } %>

      <div class="btn-group dropdown ml5 mr5">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO)) { %>
            <li><a target="_blank" href="http://<%= String(DOMINIO+'/'+link+'?preview=1').replace('//','/') %>"><?php echo lang(array("es"=>"Ver web","en"=>"View on website")); ?></a></li>
          <% } %>
          <% if (MILLING == 0 && TOQUE == 0 && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
            <li><a href="javascript:void(0)" class="enviar_ficha" data-id="<%= id %>">Enviar ficha</a></li>
          <% } %>
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>"><?php echo lang(array("es"=>"Duplicar","en"=>"Duplicate")); ?></a></li>
          <% if (MEGASHOP != 1 || (typeof VOLVER_SUPERADMIN != undefined)) { %>
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
          <% } %>
          <% if (ID_EMPRESA != 1284) { %>
            <li><a href="javascript:void(0)" class="orden_destacado" data-id="<%= id %>"><?php echo lang(array("es"=>"Orden Destacado","en"=>"Order")); ?></a></li>
          <% } %>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="articulos_variantes_item_resultados_template">
  <% var cant_dec = 0 %>
  <td style="width:20px;"></td>
  <% for(var i=0; i< tabla_articulos.campos.length; i++) { %>
    <% var c = tabla_articulos.campos[i] %>
    <% if (c.visible == 1) { %>
      
      <% if (c.campo == "stock_almacenes") { %>
        <% for(var k=0;k< window.almacenes.length;k++) { %>
          <% var alm = window.almacenes[k] %>
          <% for (var j=0;j< stock_almacenes.length;j++) { %>
            <% var stk = stock_almacenes[j] %>
            <% if (stk.id_sucursal == alm.id) { %>
              <td class="<%= (c.ocultable == 1)?"hidden-xs":"" %>">
                <span data-id_sucursal="<%= stk.id_sucursal %>" data-id_variante="<%= id %>" class="tag_precio <%= (permiso > 2 && MEGASHOP == 0)?"modificar_stock_variante":"" %> mmw50 pl8 pr8 tac dib <%= (stk.stock_actual <= 0)?"bg-danger lter":"" %>">
                  <span class="inline-text-cont bbd">
                    <%= Number(stk.stock_actual).format(cant_dec) %>
                    <% if (stk.reservado > 0) { %>
                      (<%= Number(stk.reservado).format(cant_dec) %>)
                    <% } %>
                  </span>
                  <input type="text" value="<%= stk.stock_actual %>" class="inline-text" />
                </span>
              </td>
            <% } %>
          <% } %>
        <% } %>
        <td class="<%= (c.ocultable == 1)?"hidden-xs":"" %> pl0 pr0"></td>
      
      <% } else if (c.campo == "nombre") { %>
        <td class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  >
          <span class="text-info"><%= nombre %></span>
        </td>

      <% } else if (c.campo == "path") { %>
        <td class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %>">
          <% if (!isEmpty(path)) { %>
            <% if (path.indexOf("http") == -1) { %>
              <img src="/sistema/<%= path %>?t=<%= Math.ceil(Math.random()*10000) %>" class="customcomplete-image"/>
            <% } else { %>
              <img src="<%= path %>" class="customcomplete-image"/>
            <% } %>  
          <% } %>
        </td>      

      <% } else { %>
        <td class="<%= c.clases %> <%= (c.ocultable == 1)?"hidden-xs":"" %> <%= (c.ordenable == 1)?"sorting":"" %>" <%= (c.ordenable==1)?"data-sort-by='"+c.campo+"'":"" %>  ></td>
      <% } %>

    <% } %>
  <% } %>

  <% if (MILLING == 0 && TOQUE == 0) { %>
    <% if (typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && (typeof ML_ACCESS_TOKEN != "undefined") && permiso > 1 && IDIOMA != "en" && ID_EMPRESA != 1284) { %>
      <td></td>
    <% } %>
  <% } %>
  <% if (!seleccionar && permiso > 1) { %>
    <td></td>
  <% } %>
</script>

<script type="text/template" id="articulo_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i><?php echo lang(array("es"=>"Productos","en"=>"Products")); ?> / 
    <b><%= (id == undefined)?"<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>":nombre %></b>
  </h1>
</div>
<% if (ID_PROYECTO == 1) { %>
  <?php include("art/articulos_detalle_1.php"); ?>
<% } else if (ID_PROYECTO == 10) { %>
  <?php include("art/articulos_detalle_10.php"); ?>
<% } else if (ID_PROYECTO == 13) { %>
  <?php include("art/articulos_detalle_13.php"); ?>
<% } else if (MILLING == 1) { %>
  <?php include("art/articulos_detalle_256.php"); ?>
<% } else { %>
  <?php include("art/articulos_detalle_2.php"); ?>
<% } %>
</script>

<script type="text/template" id="articulos_propiedades_item_template">
  <div class="dtc w20p">
    <div class="form-group" style="font-size:14px !important; font-weight: bold !important;">
      <%= nombre %>
      <% if (id_empresa != 0) { %>
        <i data-id="<%= id %>" class="fa fa-times text-danger m-l cp eliminar_propiedad"></i>
      <% } %>
    </div>
  </div>
  <div class="dtc">
    <div class="form-group chosen-<%= color %>">
      <select data-placeholder="Ej: rojo, negro, L, XL, etc." data-nombre_propiedad="<%= nombre %>" data-id_propiedad="<%= id %>" id="propiedad_<%= id %>" multiple class="opciones" style="width: 100%">
        <% for (var i=0; i< opciones.length; i++) { %>
          <% var o = opciones[i] %>
          <% var checked = false %>
          <% for (var j=0; j< seleccionadas.length;j++){ %>
            <% var s = seleccionadas[j] %>
            <% if (s == o.nombre) { checked = true } %>
          <% } %>
          <option value="<%= o.nombre %>" <%= (checked)?"selected":"" %>><%= o.etiqueta %></option>
        <% } %>
      </select>
    </div>
  </div>
</script>

<script type="text/template" id="articulos_variante_item_template">
  <td class='variante_nombre'><%= nombre %></td>
  <td>
    <div class="upload_container">
      <% var display_file = (!isEmpty(path)) %>
      <input id="<%= name %>_url" type="hidden" value="articulos/function/save_image/" />
      <img id="preview_<%= name %>" class="img_preview" style="max-width: 60px; display:<%= (display_file)?'inline-block':'none' %>" src="<%= path %>"/>
      <input id="hidden_<%= name %>" type="hidden" value="<%= path %>" class="path_campo_variante" data-campo="path" name="<%= name %>" data-nombre_opcion_1='<%= nombre_opcion_1 %>' data-nombre_opcion_2='<%= nombre_opcion_2 %>' data-nombre_opcion_3='<%= nombre_opcion_3 %>'/>
      <i style="display:<%= (display_file)?'inline-block':'none' %>" class="fa fa-pencil editar_imagen" data-id="<%= name %>"></i>
      <i style="display:<%= (display_file)?'inline-block':'none' %>" class="glyphicon glyphicon-remove text-danger eliminar_imagen" data-id="<%= name %>"></i>
      <!-- Datos que se envian junto con la imagen cuando se sube -->
      <input id="<%= name %>_data" class="hidden_data" type="hidden"/>
      <input id="<%= name %>_color" class="hidden_color" type="hidden"/>
      <input id="<%= name %>_src" class="hidden_src" type="hidden"/>
      <input id="<%= name %>_width" class="width" value="<?php echo (isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 256) ?>" type="hidden"/>
      <input id="<%= name %>_height" class="height" value="<?php echo (isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 256) ?>" type="hidden"/>
      <input id="<%= name %>_resizable" class="resizable" value="0" type="hidden"/>
      <input id="<%= name %>_quality" class="hidden_quality" value="0.92" type="hidden"/>
      <input id="<%= name %>_thumbnail_width" class="hidden_thumbnail_width" value="0" type="hidden"/>
      <input id="<%= name %>_thumbnail_height" class="hidden_thumbnail_height" value="0" type="hidden"/>
      <input id="<%= name %>_crop_type" class="hidden_crop_type" value="0" type="hidden"/>

      <div class="bootstrap-filestyle-container" style="display:<%= (!display_file)?'inline-block':'none' %>">
        <input id="<%= name %>" value="<%= path %>" class="single_upload" type="file" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
        <div class="bootstrap-filestyle input-group">
          <input id="<%= name %>_text" type="text" class="form-control" disabled="">
          <span class="group-span-filestyle input-group-btn" tabindex="0">
            <label for="<%= name %>" class="btn btn-default">
              <span class="glyphicon glyphicon-folder-open m-r-xs"></span>
              Subir archivo
            </label>
          </span>
        </div>
      </div>
    </div>
  </td>
</script>


<script type="text/template" id="articulos_edicion_rapida_template">
  <div class="panel panel-default">
    <div class="panel-heading fs16 bold">
      Edici&oacute;n r&aacute;pida
      <i class="fa fa-times cerrar cp fr"></i>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Precio Final</label>
            <input id="articulo_edicion_rapida_precio_final" value="<%= precio_final %>" type="text" class="form-control number" name="precio_final"/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">% Descuento</label>
            <input id="articulo_edicion_rapida_porc_bonif" value="<%= porc_bonif %>" type="text" class="form-control number" name="porc_bonif"/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Precio c/dto</label>
            <input disabled id="articulo_edicion_rapida_precio_final_dto" value="<%= precio_final_dto %>" type="text" class="form-control number" name="precio_final_dto"/>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Ancho</label>
            <input value="<%= ancho %>" type="text" class="form-control number" name="ancho"/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Perfil</label>
            <input value="<%= alto %>" type="text" class="form-control number" name="alto"/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Rodado</label>
            <input value="<%= profundidad %>" type="text" class="form-control number" name="profundidad"/>
          </div>
        </div>
      </div>

      <div class="row">
        <?php for($i=1;$i<=2;$i++) { ?>
          <?php if (isset($empresa->config["producto_custom_".$i."_label"])) { ?>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label"><?php echo $empresa->config["producto_custom_".$i."_label"] ?></label>
                <?php if(isset($empresa->config['producto_custom_'.$i.'_values'])) { 
                  $values = explode("|",$empresa->config['producto_custom_'.$i.'_values']); ?>
                  <select class="form-control" name="custom_<?php echo $i ?>">
                    <?php foreach($values as $value) { ?>
                      <option <%= (<?php echo "custom_".$i ?> == "<?php echo $value ?>")?"selected":""  %> value="<?php echo $value ?>"><?php echo $value ?></option>
                    <?php } ?>
                  </select>
                <?php } else { ?>
                  <input type="text" name="custom_<?php echo $i ?>" id="articulo_custom_<?php echo $i ?>" value="<%= custom_<?php echo $i ?> %>" class="form-control"/>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        <?php } ?>
      </div>
    </div>
    <div class="panel-footer tar">
      <button class="guardar btn btn-success">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="articulo_mercado_libre_template">
  <div class="panel panel-default">
    <div class="panel-heading fs16 bold">
      Compartir a MercadoLibre
      <i class="fa fa-times cerrar cp fr"></i>
    </div>
    <div class="panel-body">
      <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
            <a id="articulo_mercado_libre_paso_1_link" href="#articulo_mercado_libre_tab1" class="buscar_todos" role="tab" data-toggle="tab">
              <i class="fa text-warning fa-calendar m-r-xs"></i>
              Datos
            </a>
          </li>
          <li>
            <a id="articulo_mercado_libre_paso_2_link" href="#articulo_mercado_libre_tab2" role="tab" data-toggle="tab">
              <i class="fa text-info fa-address-book m-r-xs"></i>
              Categoria
            </a>
          </li>
          <li>
            <a id="articulo_mercado_libre_paso_4_link" href="#articulo_mercado_libre_tab4" style="display:none" role="tab" data-toggle="tab">
              <i class="fa text-primary fa-cogs m-r-xs"></i>
              Ficha Técnica
            </a>
          </li>
          <li>
            <a id="articulo_mercado_libre_paso_3_link" href="#articulo_mercado_libre_tab3" role="tab" data-toggle="tab">
              <i class="fa text-success fa-file-text m-r-xs"></i>
              Publicacion
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="articulo_mercado_libre_tab1" class="tab-pane active">
            <% if (!multiple) { %>
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label class="control-label">Titulo</label>
                    <input id="articulo_mercado_libre_titulo_meli" value="<%= titulo_meli %>" type="text" class="form-control" name="titulo_meli"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Precio</label>
                    <input id="articulo_mercado_libre_precio_meli" value="<%= precio_meli %>" type="text" class="form-control" name="precio_meli"/>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Descripcion</label>
                    <textarea style="height: 250px;" class="form-control" name="texto_meli" id="articulo_mercado_libre_texto_meli"><%= texto_meli %></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <?php 
                  multiple_upload(array(
                    "name"=>"images_meli",
                    "label"=>"Im&aacute;genes adicionales",
                    "url"=>"articulos/function/save_image/",
                    "width"=>(isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 800),
                    "height"=>(isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 600),
                    "resizable"=>(isset($empresa->config["producto_galeria_image_resizable"]) ? $empresa->config["producto_galeria_image_resizable"] : 0),
                    "upload_multiple"=>true,
                  )); ?>
                </div>
              </div>
            <% } else { %>
                <?php 
                multiple_upload(array(
                  "name"=>"images_meli",
                  "label"=>"Im&aacute;genes adicionales",
                  "url"=>"articulos/function/save_image/",
                  "width"=>(isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 800),
                  "height"=>(isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 600),
                  "resizable"=>(isset($empresa->config["producto_galeria_image_resizable"]) ? $empresa->config["producto_galeria_image_resizable"] : 0),
                  "upload_multiple"=>true,
                )); ?>
            <% } %>
            <div class="clearfix tar">
              <button class="ir_paso_2 btn btn-success">Siguiente</button>
            </div>
          </div>
          <div id="articulo_mercado_libre_tab2" class="tab-pane">
            <div style="overflow-y: auto;">
              <div style="height: 260px; text-align: center;" class="loading_grande">
                <img src="/sistema/resources/images/spinner.gif" style="line-height: 260px;"/>
              </div>
              <div id="articulo_mercado_libre_categorias"></div>
            </div>
            <div class="clearfix m-t">
              <button class="ir_paso_1 btn btn-default">Anterior</button>
            </div>
          </div>

          <div id="articulo_mercado_libre_tab4" class="tab-pane">
            <div style="overflow-x:hidden;overflow-y: auto; height:400px">
              <div id="articulo_mercado_libre_paso4_container"></div>
            </div>
            <div class="clearfix m-t">
              <button class="ir_paso_2 btn btn-default">Anterior</button>
              <button class="guardar_paso_4 btn btn-success">Siguiente</button>
            </div>
          </div>

          <div id="articulo_mercado_libre_tab3" class="tab-pane">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Tipo de publicacion</label>
                  <select id="articulo_mercado_libre_tipo_publicacion" class="form-control">
                    <option value="0">Seleccione</option>
                    <option value="free">Gratuita</option>
                    <option value="bronze">Bronce</option>
                    <option value="silver">Plata</option>
                    <option value="gold">Oro</option>
                    <option selected value="gold_special">Cl&aacute;sica</option>
                    <option value="gold_premium">Oro Premium</option>
                    <option value="gold_pro">Premium</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Metodos de envio</label>
                  <select id="articulo_mercado_libre_forma_envio" class="form-control">
                    <option value="not_specified">Acordar con el comprador</option>
                    <option selected value="me2">MercadoEnvio</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">&nbsp;</label>
                  <div class="checkbox mt0 mb0">
                    <label class="i-checks">
                      <input type="checkbox" id="articulo_mercado_libre_retiro_sucursal" class="checkbox no-model" value="1">
                      <i></i> Retirar en sucursal
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Formas de pago aceptadas</label>
                </div>
              </div>
              <% var formas_pago = new Array() %>
              <% formas_pago.push({"id":"MLAMP","checked":true,"name":"MercadoPago"}) %>
              <% formas_pago.push({"id":"MLAWC","checked":false,"name":"Acordar con el comprador"}) %>
              <% formas_pago.push({"id":"MLAAM","checked":false,"name":"American Express"}) %>
              <% formas_pago.push({"id":"MLABC","checked":false,"name":"Cheque certificado"}) %>
              <% formas_pago.push({"id":"MLACD","checked":false,"name":"Contra reembolso"}) %>
              <% formas_pago.push({"id":"MLAOT","checked":false,"name":"Tarjeta de crédito"}) %>
              <% formas_pago.push({"id":"MLADC","checked":false,"name":"Diners"}) %>
              <% formas_pago.push({"id":"MLAMO","checked":false,"name":"Efectivo"}) %>
              <% formas_pago.push({"id":"MLAWT","checked":false,"name":"Giro postal"}) %>
              <% formas_pago.push({"id":"MLAMC","checked":false,"name":"MasterCard"}) %>
              <% formas_pago.push({"id":"MLAMS","checked":false,"name":"Mastercard Maestro"}) %>
              <% formas_pago.push({"id":"MLAVS","checked":false,"name":"Visa"}) %>
              <% formas_pago.push({"id":"MLATB","checked":false,"name":"Transferencia bancaria"}) %>
              <% formas_pago.push({"id":"MLAVE","checked":false,"name":"Visa Electron"}) %>
              <% for(var i=0; i< formas_pago.length; i++) { %>
                <% var fp = formas_pago[i] %>
                <div class="col-md-4">
                  <div class="form-group">
                    <a href="javascript:void(0)">
                      <label class="i-checks">
                        <input type="checkbox" class="articulo_mercado_libre_forma_pago" <%= (fp.checked)?"checked":"" %> value="<%= fp.id %>">
                        <i></i><%= fp.name %>
                      </label>
                    </a>
                  </div>
                </div>
              <% } %>
            </div>
            <div class="clearfix tar">
              <button class="ir_paso_2 fl btn btn-default">Anterior</button>
              <button class="btn btn-success publicar">Publicar</button>
            </div>
          </div>
        </div> 
      </div>   
    </div>
  </div>
</script>

<script type="text/template" id="articulo_mercado_libre_categoria_template">
  <select size="15" class="form-control categoria_mercado_libre" data-nivel="<%= nivel %>">
    <% for(var i=0; i< categories.length; i++) { %>
      <% var cat = categories[i] %>
      <option <%= (cat.id == selected)?"selected":"" %> value="<%= cat.id %>"><%= cat.name %></option>
    <% } %>
  </select>
</script>

<script type="text/template" id="articulo_precio_sucursal_template">
  <div class="fl cb" style="<%= (!visible)?'display:none':'' %>"> 
    <div class="row">
      <h4 class="cp h4 text-info mb10 mt20">
        <div class="checkbox mt0 mb0">
          <label class="i-checks">
            <input type="checkbox" class="sucursal_activo checkbox no-model" value="1" <%= (activo == 1)?"checked":"" %> <%= (!edicion)?"disabled":"" %>>
            <i></i>
            <span class="articulo_precio_nombre_sucursal ml0"><%= sucursal %></span>
          </label>
        </div>
      </h4>
    </div>
    <div class='articulo_precio_info' style="/*<%= (!collapsed)?'display:none':'' %>*/">
      <input id="articulo_precio_sucursal_ganancia" disabled value="<%= ganancia %>" type="text" class="form-control dn" name="ganancia"/>
      <input id="articulo_precio_sucursal_precio_neto" disabled value="<%= precio_neto %>" type="text" class="form-control dn" name="precio_neto"/>
      <input id="articulo_precio_sucursal_iva" disabled value="<%= Number(costo_neto * porc_iva / 100).toFixed(2) %>" type="text" class="form-control dn" name="costo_iva"/>
      <div class="precio_sucursal_container precio_sucursal_container_<%= id_sucursal %>">
        <div class="row">
          <div class="col-md-4 col-xs-12 p0">
            <div class="col-md-2 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">&nbsp;</label>
                <select id="articulo_precio_sucursal_moneda" class="form-control pl0 pr0" name="moneda" <%= (!edicion)?"disabled":"" %>>
                  <% for(var i=0;i< window.monedas.length;i++) { %>
                    <% var o = monedas[i]; %>
                    <option <%= (o.id == moneda)?"selected":"" %> value="<%= o.id %>"><%= o.signo %> (<%= o.nombre %>)</option>
                  <% } %>
                </select>
              </div>
            </div>
            <div class="col-md-4 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">Neto</label>
                <input type="text" class="form-control number calc_total" id="articulo_precio_sucursal_costo_neto_inicial" name="costo_neto_inicial" value="<%= costo_neto_inicial %>" <%= (!edicion)?"disabled":"" %> />
              </div>
            </div>
            <div class="col-md-3 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">% Dto.
                  <% if ( (MEGASHOP == 1 || ID_EMPRESA == 421) && !isEmpty(custom_1)) { %>
                    <i data-toggle="tooltip" title="<%= custom_1 %>" class="fa fa-commenting text-warning"></i>
                  <% } %>
                </label>
                <input type="text" class="form-control number calc_total" id="articulo_precio_sucursal_dto_prov" name="dto_prov" value="<%= dto_prov %>" <%= (!edicion)?"disabled":"" %>/>
              </div>
            </div>
            <div class="col-md-3 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">&nbsp;</label>
                <input type="text" disabled class="form-control number calc_total" id="articulo_precio_sucursal_costo_neto" name="costo_neto" value="<%= costo_neto %>"/>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-xs-12 p0">
            <div class="col-md-4 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">% IVA</label>
                <select id="articulo_precio_sucursal_tipos_alicuotas_iva" class="form-control" <%= (!edicion)?"disabled":"" %>>
                  <% for(var i=0;i< window.alicuotas_iva.length;i++) { %>
                    <% var o = alicuotas_iva[i]; %>
                    <option <%= (id_tipo_alicuota_iva == o.id)?"selected":"" %> value="<%= o.id %>" data-porcentaje="<%= o.porcentaje %>"><%= o.nombre %></option>
                  <% } %>
                </select>
              </div>
            </div>
            <div class="col-md-4 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">Final</label>
                <input type="text" class="form-control number calc_total articulo_precio_sucursal_costo_final_<%= id_sucursal %>" id="articulo_precio_sucursal_costo_final" name="costo_final" value="<%= Number(costo_final).toFixed(2) %>" <%= (!edicion)?"disabled":"" %>/>
              </div>
            </div>
            <div class="col-md-4 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">% Marc.</label>
                <input id="articulo_precio_sucursal_porc_ganancia" value="<%= porc_ganancia %>" type="text" class="form-control number calc_total" name="porc_ganancia" <%= (!edicion)?"disabled":"" %>/>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-xs-12 p0">
            <div class="col-md-4 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">Pr. Final</label>
                <input id="articulo_precio_sucursal_precio_final" value="<%= precio_final %>" type="text" class="form-control number" name="precio_final" <%= (!edicion)?"disabled":"" %>/>
              </div>
            </div>
            <div class="col-md-4 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label">% Dto.</label>
                <input id="articulo_precio_sucursal_porc_bonif" value="<%= porc_bonif %>" type="text" class="form-control number" name="porc_bonif" <%= (!edicion)?"disabled":"" %>/>
              </div>
            </div>
            <div class="col-md-4 col-xs-12 p0">
              <div class="form-group">
                <label class="control-label w100p">Publico <i class="fa cp fa-plus fr abrir_listas text-info"></i></label>
                <input id="articulo_precio_sucursal_precio_final_dto" value="<%= precio_final_dto %>" type="text" class="form-control number articulo_precio_sucursal_precio_final_dto_<%= id_sucursal %>" name="precio_final_dto" <%= (!edicion)?"disabled":"" %>/>
              </div>
            </div>
          </div>
        </div>
        <div class="articulo_precio_sucursal_lista_precios_cont" style="<%= (collapsed_lista_precios == 0)?'display:none':'' %>">
          <div class="row">
            <div class="col-md-4 col-md-offset-4 p0">
              <div class="col-md-8 p0 tar">
                <span class="text-info db mr15 mt5"><%= (!isEmpty(LISTA_2_NOMBRE) ? LISTA_2_NOMBRE : "LISTA 2") %></span>
              </div>
              <div class="col-md-4 col-xs-12 p0">
                <div class="form-group">
                  <input id="articulo_precio_sucursal_porc_ganancia_2" value="<%= porc_ganancia_2 %>" type="text" class="form-control number calc_total" name="porc_ganancia_2" <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-xs-12 p0">
              <div class="col-md-4 col-xs-12 p0">
                <div class="form-group">
                  <input id="articulo_precio_sucursal_precio_final_2" value="<%= precio_final_2 %>" type="text" class="form-control number" name="precio_final_2" <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
              <div class="col-md-4 col-xs-12 p0">
                <div class="form-group">
                  <input id="articulo_precio_sucursal_porc_bonif_2" value="<%= porc_bonif_2 %>" type="text" class="form-control number" name="porc_bonif_2" <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
              <div class="col-md-4 col-xs-12 p0">
                <div class="form-group">
                  <input id="articulo_precio_sucursal_precio_final_dto_2" value="<%= precio_final_dto_2 %>" type="text" class="form-control number" name="precio_final_dto_2" <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-4 p0">
              <div class="col-md-8 p0 tar">
                <span class="text-info db mr15 mt5"><%= (!isEmpty(LISTA_3_NOMBRE) ? LISTA_3_NOMBRE : "LISTA 3") %></span>
              </div>
              <div class="col-md-4 col-xs-12 p0">
                <div class="form-group">
                  <input id="articulo_precio_sucursal_porc_ganancia_3" value="<%= porc_ganancia_3 %>" type="text" class="form-control number calc_total" name="porc_ganancia_3" <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-xs-12 p0">
              <div class="col-md-4 col-xs-12 p0">
                <div class="form-group">
                  <input id="articulo_precio_sucursal_precio_final_3" value="<%= precio_final_3 %>" type="text" class="form-control number" name="precio_final_3" <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
              <div class="col-md-4 col-xs-12 p0">
                <div class="form-group">
                  <input id="articulo_precio_sucursal_porc_bonif_3" value="<%= porc_bonif_3 %>" type="text" class="form-control number" name="porc_bonif_3" <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
              <div class="col-md-4 col-xs-12 p0">
                <div class="form-group">
                  <input id="articulo_precio_sucursal_precio_final_dto_3" value="<%= precio_final_dto_3 %>" type="text" class="form-control number" name="precio_final_dto_3" <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="articulo_imprimir_etiquetas_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Imprimir etiquetas</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <div class="col-md-3 p0">
          <div class="input-group">
            <input type="text" class="form-control action" id="articulo_imprimir_etiquetas_codigo" placeholder="Codigo"/>
            <span class="input-group-btn">
              <button id="articulo_imprimir_etiquetas_buscar" class="btn btn-default"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-3 p0">
          <input type="text" class="form-control" disabled id="articulo_imprimir_etiquetas_nombre"/>
        </div>
        <div class="col-md-3 p0">
          <input type="text" class="form-control" disabled id="articulo_imprimir_etiquetas_precio"/>
        </div>
        <div class="col-md-3 p0">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Unidades" id="articulo_imprimir_etiquetas_cantidad" value=""/>
            <span class="input-group-btn">
              <button class="btn btn-info" id="articulo_imprimir_etiquetas_agregar"><i class="fa fa-plus"></i></button>
            </span>
          </div>
        </div>
      </div>
      <div class="b-a table-responsive" style="min-height: 180px; overflow: auto;">
        <table id="articulo_imprimir_etiquetas_tabla" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
            <th style="width:50px">Codigo</th>
            <th>Articulo</th>
            <th style="width:40px">Cantidad</th>
            <th style="width:40px">Precio</th>
            <th class="w25"></th>
            <th class="w25"></th>
            </tr>
          </thead>
          <tbody>
            <% for(var i=0;i< items.length;i++) { %>
              <% var item = items[i] %>
              <tr id='articulo_<%= item.id %>'>
                <td><%= item.get("codigo") %></td>
                <td><%= item.get("nombre") %></td>
                <td><%= Number(item.get("cantidad")).toFixed(0) %></td>
                <td><%= Number(item.get("precio_final")).toFixed(2) %></td>
                <td><i title='Editar' class='fa fa-file-text-o edit text-dark'></i></td>
                <td><i title='Eliminar' class='glyphicon glyphicon-remove delete text-danger'></i></td>
              </tr>
            <% } %>
          </tbody>
        </table>
      </div>
    </div>
    <div class="panel-footer clearfix">

      <div class="pull-left">
        <span class="input-group-btn w150">
          <div class="btn-group dropdown ml5">
            <button class="btn btn-default btn-addon dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-print"></i><span><?php echo lang(array("es"=>"Imprimir","en"=>"Print")); ?></span>
            </button>
            <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void(0)" class="imprimir_carteles" data-url="imprimir">Lista de precios</a></li>
              <% if (control.check("articulos")>2) { %>
                <li><a href="javascript:void(0)" class="imprimir_carteles" data-url="imprimir_costos">Lista de costos</a></li>
              <% } %>
              <li><a href="javascript:void(0)" class="imprimir_carteles" data-url="imprimir_precios">Carteles chicos</a></li>
              <li><a href="javascript:void(0)" class="imprimir_carteles" data-url="imprimir_precios_medianos">Carteles medianos</a></li>
              <li><a href="javascript:void(0)" class="imprimir_carteles" data-url="imprimir_precios_grandes">Carteles grandes</a></li>
              <li><a href="javascript:void(0)" class="imprimir_carteles" data-url="imprimir_ofertas">Ofertas</a></li>
              <% if (typeof FACTURACION_USA_NPLU != "undefined" && FACTURACION_USA_NPLU == 1) { %>
                <li><a href="javascript:void(0)" class="imprimir_carteles" data-url="5">Hoja de Pedido</a></li>
                <li><a href="javascript:void(0)" class="imprimir_carteles" data-url="imprimir_plu">Listado de PLU</a></li>
              <% } %>
            </ul>
          </div>
        </span>
      </div>
      <button class="btn btn-default fr imprimir">Generar archivo</button>
      <button class="btn btn-default fr imprimir_directo">Imprimir</button>
    </div>
  </div>
</script>

<script type="text/template" id="articulo_recategorizacion_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Cambiar categoria de productos</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Categor&iacute;a",
            "en"=>"Category",
          )); ?>
        </label>
        <div class="input-group">
          <select id="articulo_recategorizacion_rubros" class="form-control no-model"></select>
          <span class="input-group-btn">
            <button tabindex="-1" class="btn btn-info w100 agregar_rubro">
              <?php echo lang(array(
                "es"=>"+ Agregar",
                "en"=>"+ Add",
              )); ?>
            </button>
          </span>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>


<script type="text/template" id="articulo_etiquetar_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Agregar etiquetas a productos</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Etiquetas",
            "en"=>"Tags",
          )); ?>
        </label>
        <div class="form-group">
          <select multiple id="articulo_etiquetar_etiquetas" style="width: 100%"></select>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="articulo_cambiar_moneda_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Cambiar moneda de productos</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Moneda",
            "en"=>"Currency",
          )); ?>
        </label>
        <div class="form-group">
          <select id="articulo_cambiar_moneda_moneda" class="form-control no-model">
            <% for(var i=0;i< window.monedas.length;i++) { %>
              <% var o = monedas[i]; %>
              <option value="<%= o.id %>"><%= o.signo %> (<%= o.nombre %>)</option>
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

<script type="text/template" id="articulo_cambiar_marca_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Cambiar marcas</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">Marcas</label>
        <select class="form-control no-model" id="articulo_cambiar_marca_marcas"></select>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="articulo_cambiar_oferta_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Cambiar ofertas</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">Promoci&oacute;n</label>
        <select class="form-control no-model" id="articulo_cambiar_oferta_promociones"></select>
      </div>
      <div class="clearfix m-b">
        <label class="control-label">Descuento (%)</label>
        <input type="text" class="form-control no-model" id="articulo_cambiar_oferta_descuento"/>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>


<script type="text/template" id="articulo_agregar_proveedor_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Agregar proveedor</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">Proveedor</label>
        <div class="form-group">
          <select class="no-model" id="articulo_agregar_proveedor_proveedores"></select>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="articulo_ajuste_masivo_stock_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Ajuste masivo de stock</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <div class="form-group">
          <label class="control-label">Sucursal</label>
          <select class="form-control no-model" id="ajuste_masivo_stock_sucursales">
            <% for(var i=0; i< almacenes.length; i++) { %>
              <% var alm = almacenes[i] %>
              <option value="<%= alm.id %>"><%= alm.nombre %></option>
            <% } %>
          </select>
        </div>          
        <div class="form-group">
          <label class="control-label">Cantidad</label>
          <input type="text" class="form-control no-model" id="ajuste_masivo_stock_cantidad" value="1" />
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>

<script type="text/template" id="lista_precios_configuracion_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Listas de precios</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Lista 1</label>
            <input type="text" id="lista_precios_configuracion_1" value="<%= LISTA_1_NOMBRE %>" class="form-control no-model">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Lista 2</label>
            <input type="text" id="lista_precios_configuracion_2" value="<%= LISTA_2_NOMBRE %>" class="form-control no-model">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Lista 3</label>
            <input type="text" id="lista_precios_configuracion_3" value="<%= LISTA_3_NOMBRE %>" class="form-control no-model">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Lista 4</label>
            <input type="text" id="lista_precios_configuracion_4" value="<%= LISTA_4_NOMBRE %>" class="form-control no-model">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Lista 5</label>
            <input type="text" id="lista_precios_configuracion_5" value="<%= LISTA_5_NOMBRE %>" class="form-control no-model">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Lista 6</label>
            <input type="text" id="lista_precios_configuracion_6" value="<%= LISTA_6_NOMBRE %>" class="form-control no-model">
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="guardar btn btn-success">Guardar</button>
    </div>
  </div>
</script>


<script type="text/template" id="imprimir_listado_articulos_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">&iquest;Que lista de precios desea imprimir?</div>
    <div class="panel-body">
      <% if (almacenes.length > 1) { %>
        <div class="form-group">
          <select id="imprimir_lista_precios_sucursales" class="form-control no-model">
            <% if (ID_SUCURSAL != 0) { %>
              <% for(var i=0;i< window.almacenes.length;i++) { %>
                <% var o = almacenes[i] %>
                <% if (ID_SUCURSAL == o.id) { %>
                  <option selected value="<%= o.id %>"><%= o.nombre %></option>
                <% } %>
              <% } %>
            <% } else { %>
              <option value="0">Sucursal</option>
              <% for(var i=0;i< window.almacenes.length;i++) { %>
                <% var o = almacenes[i] %>
                <option value="<%= o.id %>"><%= o.nombre %></option>
              <% } %>
            <% } %>
          </select>
        </div>
      <% } %>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="i-checks m-b-none">
              <input type="checkbox" checked id="imprimir_lista_precios_1"/><i></i> <%= LISTA_1_NOMBRE %>
            </label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="i-checks m-b-none">
              <input type="checkbox" checked id="imprimir_lista_precios_2"/><i></i> <%= LISTA_2_NOMBRE %>
            </label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="i-checks m-b-none">
              <input type="checkbox" checked id="imprimir_lista_precios_3"/><i></i> <%= LISTA_3_NOMBRE %>
            </label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="i-checks m-b-none">
              <input type="checkbox" checked id="imprimir_lista_precios_4"/><i></i> <%= LISTA_4_NOMBRE %>
            </label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="i-checks m-b-none">
              <input type="checkbox" checked id="imprimir_lista_precios_5"/><i></i> <%= LISTA_5_NOMBRE %>
            </label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="i-checks m-b-none">
              <input type="checkbox" checked id="imprimir_lista_precios_6"/><i></i> <%= LISTA_6_NOMBRE %>
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="imprimir btn btn-default">Imprimir</button>
    </div>
  </div>
</script>


<script type="text/template" id="imprimir_listado_articulos_por_proveedor_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Impresion de listas por proveedor</div>
    <div class="panel-body">
      <% if (almacenes.length > 1) { %>
        <div class="form-group">
          <select id="imprimir_lista_precios_por_proveedor_sucursales" class="form-control no-model">
            <% if (ID_SUCURSAL != 0) { %>
              <% for(var i=0;i< window.almacenes.length;i++) { %>
                <% var o = almacenes[i] %>
                <% if (ID_SUCURSAL == o.id) { %>
                  <option selected value="<%= o.id %>"><%= o.nombre %></option>
                <% } %>
              <% } %>
            <% } else { %>
              <option value="0">Sucursal</option>
              <% for(var i=0;i< window.almacenes.length;i++) { %>
                <% var o = almacenes[i] %>
                <option value="<%= o.id %>"><%= o.nombre %></option>
              <% } %>
            <% } %>
          </select>
        </div>
      <% } %>

      <div class="form-group">
        <label class="control-label">Con ventas desde la fecha:</label>
        <div class="input-group">
          <input type="text" id="imprimir_lista_precios_por_proveedor_con_ventas_desde" class="form-control no-model"/>
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>
      </div>

    </div>
    <div class="panel-footer clearfix tar">
      <button class="imprimir btn btn-default">Imprimir</button>
    </div>
  </div>
</script>

<script type="text/template" id="articulos_etiquetas_panel_template">
    
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Listado de Etiquetas</h1>
    </div>
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
                <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
                <a class="btn pull-right btn-success btn-addon" href="app/#articulo_etiqueta"><i class="fa fa-plus"></i>Nueva</a>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="articulos_etiquetas_table" class="table table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
                                <th class="sorting" data-sort-by="nombre">Nombre</th>
                                <% if (permiso > 1) { %>
                                    <th class="w25"></th>
                                    <th class="w25"></th>
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

<script type="text/template" id="articulo_mercado_libre_atributo_template">
  <% if (value_type == "number" || value_type == "string" || value_type == "list" || value_type == "boolean") { %>
    <div class="form-group h80 mb0">
      <% if (typeof values != "undefined") { %>
        <label class="control-label"><%= name %></label>
        <select id="atributo-meli-<%= id %>" data-required="<%= (typeof tags.required != "undefined")?"1":"0" %>" data-value_type="<%= value_type %>" class="atributo-meli no-model form-control" name="<%= id %>">
          <option value="0"></option>
          <% if (value_type != "list" && value_type != "boolean") { %><option value="-1">N/A</option><% } %>
          <% for(var i=0;i< values.length;i++) { %>
            <% var value = values[i] %>
            <option <%= (selected_id == value.id || selected_value == value.name)?"selected":"" %> value="<%= value.id %>"><%= value.name %></option>
          <% } %>
        </select>
      <% } else { %>
        <label class="control-label"><%= name %></label>
        <input value="<%= selected_value %>" data-required="<%= (typeof tags.required != "undefined")?"1":"0" %>" data-value_type="<%= value_type %>" id="atributo-meli-<%= id %>" type="<%= (value_type=="number")?"number":"text" %>" class="atributo-meli no-model form-control" name="<%= id %>"/>
      <% } %>
    </div>
  <% } else if (value_type == "number_unit") { %>    
    <div class="form-group h80 mb0">
      <label class="control-label"><%= name %></label>
      <div class="input-group">
        <input data-required="<%= (typeof tags.required != "undefined")?"1":"0" %>" id="atributo-meli-<%= id %>" type="number" data-value_type="<%= value_type %>" class="atributo-meli no-model form-control" name="<%= id %>"/>
        <span class="input-group-btn">
          <select class="form-control no-model w75">
            <% for(var j=0;j< allowed_units.length;j++) { %>
              <% var unit = allowed_units[j] %>
              <option <%= (unit.id == default_unit)?"selected":"" %> value="<%= unit.id %>"><%= unit.name %></option>
            <% } %>
          </select>
        </span>
      </div>
    </div>
  <% } %>
</script>

<script type="text/template" id="articulos_etiquetas_item">
  <td><span class='ver'><%= nombre %></span></td>
  <% if (permiso > 1) { %>
    <td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
    <td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
  <% } %>
</script>

<script type="text/template" id="articulos_etiquetas_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nueva Etiqueta
    <% } else { %>
        <%= nombre %>
    <% } %>       
  </h1>
</div>

<div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
        <div class="panel-heading">
            <span class="font-bold">Ingrese los datos</span>
        </div>
        <div class="panel-body">
        
            <div class="form-horizontal">

                <div class="form-group">
                    <label class="col-lg-2 control-label">Nombre</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="nombre" class="form-control" id="articulos_etiquetas_nombre" value="<%= nombre %>"/>
                        <% } else { %>
                            <span><%= nombre %></span>
                        <% } %>
                    </div>
                </div>        

                <div class="form-group">
                    <label class="col-lg-2 control-label">Texto</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="texto" class="form-control" id="articulos_etiquetas_texto" value="<%= texto %>"/>
                        <% } else { %>
                            <span><%= texto %></span>
                        <% } %>
                    </div>
                </div>        
        
                <div class="line line-dashed b-b line-lg pull-in"></div>
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn guardar btn-success">Guardar</button>
                        </div>
                    </div>
                <% } %>
            </div>
        </div>
    </div>
</div>

</script>